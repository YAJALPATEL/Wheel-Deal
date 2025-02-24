<?php
session_start();

// Get item index to remove
$index = $_GET['index'] ?? null;

// Validate and remove item
if ($index !== null && isset($_SESSION['cart'][$index])) {
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
}

header("Location: ../../pages/cart.php");
exit();
?>