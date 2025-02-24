<?php
session_start();
include('../assets/includes/db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];
$total_amount = 0;

// Fetch user shipping details
$user_sql = "SELECT shippingAddress, shippingCity, shippingState, shippingPincode FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Confirm Your Order</h2>
        <h5 class="text-center" style="color:red;">Note: Once Order Are Placed Than You Can Not Cancel The Order</h5>
        <?php if (empty($cart)) : ?>
        <p class="text-center text-danger">Your cart is empty.</p>
        <a href="cart.php" class="btn btn-primary">Back to Cart</a>
        <?php else : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $item) :
                        $subtotal = $item['product_price'] * $item['quantity'];
                        $total_amount += $subtotal;
                    ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td> ₹<?php echo number_format($item['product_price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td> ₹<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="text-end">Total: ₹<?php echo number_format($total_amount, 2); ?></h3>

        <h5>Shipping Address</h5>
        <p><?php echo nl2br(htmlspecialchars($user_data['shippingAddress'])); ?></p>
        <p><?php echo htmlspecialchars($user_data['shippingCity'] . ", " . $user_data['shippingState'] . " - " . $user_data['shippingPincode']); ?>
        </p>

        <a href="change_address.php" class="btn btn-warning">Change Address</a>

        <form action="place_order.php" method="POST">
            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
            <button type="submit" class="btn btn-success">Place Order</button>
        </form>

        <?php endif; ?>
    </div>
</body>

</html>

<?php
$conn->close();
?>