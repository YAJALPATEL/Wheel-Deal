<?php
session_start();
include('../includes/db_connection.php');

$user_id = $_SESSION['user_id'] ?? null;
$product_id = $_POST['product_id'] ?? 0;

if (!$user_id || $product_id <= 0) {
    echo "error";
    exit();
}

// Check if product is in wishlist
$sql_check = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $product_id);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Remove from wishlist
    $sql_remove = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt_remove = $conn->prepare($sql_remove);
    $stmt_remove->bind_param("ii", $user_id, $product_id);
    if ($stmt_remove->execute()) {
        echo "removed";
    } else {
        echo "error";
    }
    $stmt_remove->close();
} else {
    // Add to wishlist
    $sql_add = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
    $stmt_add = $conn->prepare($sql_add);
    $stmt_add->bind_param("ii", $user_id, $product_id);
    if ($stmt_add->execute()) {
        echo "added";
    } else {
        echo "error";
    }
    $stmt_add->close();
}

$stmt_check->close();
$conn->close();
?>