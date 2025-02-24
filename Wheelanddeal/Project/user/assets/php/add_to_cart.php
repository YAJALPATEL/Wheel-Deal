<?php
session_start();

// Ensure cart session is initialized
$_SESSION['cart'] = []; // Clear previous cart, ensuring only one item exists

// Get product details from the form
$product_id = $_POST['product_id'] ?? 0;
$product_name = $_POST['product_name'] ?? '';
$product_price = $_POST['product_price'] ?? 0.0;

// Validate input
if ($product_id <= 0 || empty($product_name) || $product_price <= 0) {
    die("Invalid product details.");
}

// Add only one item to the cart
$_SESSION['cart'][] = [
    'product_id' => $product_id,
    'product_name' => $product_name,
    'product_price' => $product_price,
    'quantity' => 1
];

// Redirect to cart page
header("Location: ../../pages/cart.php");
exit();
?>