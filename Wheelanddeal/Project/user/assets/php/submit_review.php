<?php
session_start();
include('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? null;
    $product_id = $_POST['product_id'] ?? 0;
    $rating = $_POST['rating'] ?? 0;
    $review = trim($_POST['review'] ?? '');

    if (!$user_id) {
        echo "error: You must be logged in.";
        exit();
    }

    if ($product_id <= 0 || $rating < 1 || $rating > 5 || empty($review)) {
        echo "error: Invalid input.";
        exit();
    }

    $sql = "INSERT INTO reviews (user_id, product_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: Something went wrong.";
    }

    $stmt->close();
    $conn->close();
}
?>