<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: ../login.php");
    exit;
}

// Include database connection
include_once('../assets/includes/db_connection.php');

// Get the company name from the query parameter
$company = isset($_GET['company']) ? urldecode($_GET['company']) : '';

// Fetch products for the selected company
$sql = "SELECT id, productName, productPrice, productImage1 FROM products WHERE productCompany = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $company);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="theme-color" content="#FF5733">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List - <?php echo htmlspecialchars($company); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://localhost/wheelanddeal/Project/user/assets/css/styles.css">
</head>

<body>
    <?php include('../assets/includes/header.php'); ?>

    <!-- Product List Section -->
    <section class="product-list py-5">
        <div class="container">
            <h2 class="text-center mb-4"><?php echo htmlspecialchars($company); ?> Models</h2>
            <div class="row g-4">
                <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <?php
                        $imageData = base64_encode($product['productImage1']);
                        $imageSrc = "data:image/jpeg;base64," . $imageData;
                        ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo $imageSrc; ?>" class="card-img-top"
                            alt="<?php echo htmlspecialchars($product['productName']); ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['productName']); ?></h5>
                            <p class="card-text">Price: ₹<?php echo number_format($product['productPrice'], 2); ?></p>
                            <a href="./product_details.php?id=<?php echo $product['id']; ?>"
                                class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-center">No products found for <?php echo htmlspecialchars($company); ?>.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include('../assets/includes/footer.php'); ?>
    <a href="https://wa.me/7046085161" target="_blank" class="whatsapp-float">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="Chat on WhatsApp">
    </a>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>