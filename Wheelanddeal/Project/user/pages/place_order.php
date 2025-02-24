<?php
session_start();
include('../assets/includes/db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];
$total_amount = isset($_POST['total_amount']) ? (float)$_POST['total_amount'] : 0;

if (empty($cart)) {
    header("Location: cart.php?error=Your cart is empty.");
    exit();
}

// Fetch user's shipping details
$user_sql = "SELECT shippingAddress, shippingCity, shippingState, shippingPincode FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_stmt->close();

// Ensure shipping details are available
if (!$user_data || empty($user_data['shippingAddress'])) {
    header("Location: checkout.php?error=Please update your shipping address.");
    exit();
}

// Get first product in cart (since only 1 item can be ordered at a time)
$item = reset($cart);
$product_id = $item['product_id'];
$quantity = $item['quantity'];
$price = $item['product_price'];

// Insert order into `orders` table
$order_sql = "INSERT INTO orders (user_id, product_id, quantity, total_price, order_status, order_date, shipping_address, shipping_city, shipping_state, shipping_pincode) 
              VALUES (?, ?, ?, ?, 'Pending', NOW(), ?, ?, ?, ?)";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("iiidssss", $user_id, $product_id, $quantity, $total_amount, 
                        $user_data['shippingAddress'], $user_data['shippingCity'], 
                        $user_data['shippingState'], $user_data['shippingPincode']);
$order_stmt->execute();
$order_id = $order_stmt->insert_id;
$order_stmt->close();

// Clear cart after placing order
unset($_SESSION['cart']);

// Redirect to order success page
header("Location: order_success.php?order_id=" . $order_id);
exit();
?>