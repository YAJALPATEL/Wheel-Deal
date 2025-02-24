<?php
session_start();
include('../assets/includes/db_connection.php');

$order_id = $_GET['order_id'] ?? 0;

if ($order_id <= 0) {
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Order Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5 text-center">
        <h2 class="text-success">✅ Order Placed Successfully!</h2>
        <p>Your order ID is: <strong>#<?php echo $order_id; ?></strong></p>
        <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</body>

</html>