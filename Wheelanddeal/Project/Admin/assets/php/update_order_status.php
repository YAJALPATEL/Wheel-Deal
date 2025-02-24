<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
include '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = intval($_POST['order_id']);
    $order_status = trim($_POST['order_status']); // Trim whitespace

    // Validate order status
    $valid_statuses = ['Pending', 'Shipped', 'Delivered']; // Updated valid statuses
    if (!in_array($order_status, $valid_statuses)) {
        echo json_encode(["success" => false, "message" => "Invalid order status."]);
        exit;
    }

    // Update order status in database
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("si", $order_status, $order_id);
    
    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        if ($affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Order status updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "No rows updated. Check if the order ID exists."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update order status: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit; // Stop further execution after AJAX response
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}
?>