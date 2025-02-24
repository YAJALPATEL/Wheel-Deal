<?php
ob_start();
session_start();
include('../assets/includes/db_connection.php'); // Database connection
include('../assets/includes/header.php'); // Header file

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];
// Get the product ID from the query string
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch existing user shipping details
$user_sql = "SELECT shippingAddress, shippingCity, shippingState, shippingPincode FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Handle address update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_address'])) {
    $newAddress = trim($_POST['shipping_address']);
    $newCity = trim($_POST['shipping_city']);
    $newState = trim($_POST['shipping_state']);
    $newPincode = trim($_POST['shipping_pincode']);

    // Validate input fields
    if (empty($newAddress) || empty($newCity) || empty($newState) || empty($newPincode)) {
        $error_message = "All fields are required.";
    } else {
        // Update address in the database
        $update_sql = "UPDATE users SET shippingAddress = ?, shippingCity = ?, shippingState = ?, shippingPincode = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssi", $newAddress, $newCity, $newState, $newPincode, $user_id);

        if ($update_stmt->execute()) {
            // Redirect to the order page with the product ID
            header("Location: checkout.php?id=" . $product_id);
            exit(); // Stop further execution
        } else {
            $error_message = "Error updating address. Please try again.";
        }

        $update_stmt->close();
    }
}

$user_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Update Shipping Address</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://localhost/wheelanddeal/Project/user/assets/css/styles.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Update Your Shipping Address</h2>
        <?php if (isset($error_message)) : ?>
        <p class="text-danger text-center"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="shipping_address">Address:</label>
            <textarea id="shipping_address" name="shipping_address" class="form-control mb-2"
                required><?php echo htmlspecialchars($user_data['shippingAddress'] ?? ''); ?></textarea>

            <label for="shipping_city">City/Village:</label>
            <input id="shipping_city" type="text" name="shipping_city" class="form-control mb-2" required
                value="<?php echo htmlspecialchars($user_data['shippingCity'] ?? ''); ?>">

            <label for="shipping_state">State:</label>
            <input id="shipping_state" type="text" name="shipping_state" class="form-control mb-2" required
                value="<?php echo htmlspecialchars($user_data['shippingState'] ?? ''); ?>">

            <label for="shipping_pincode">Pincode:</label>
            <input id="shipping_pincode" type="text" name="shipping_pincode" class="form-control mb-2" required
                value="<?php echo htmlspecialchars($user_data['shippingPincode'] ?? ''); ?>">

            <button type="submit" name="update_address" class="btn btn-primary">Update Address</button>
            <a href="checkout.php?id=<?php echo $product_id; ?>" class="btn btn-secondary">Back to Order</a>
        </form>
    </div>
</body>

</html>