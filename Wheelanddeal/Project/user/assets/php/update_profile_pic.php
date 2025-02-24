<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Include database connection file
require_once __DIR__ . '/../includes/db_connection.php';

$user_id = $_SESSION['user_id'];

// Handle avatar selection
if (isset($_POST['avatar'])) {
    $avatar = $_POST['avatar'];
    $allowedAvatars = ['a1.png', 'a2.jpeg', 'a3.jpg', 'a4.jpg', 'a5.jpg', 'a6.jpg'];

    if (in_array($avatar, $allowedAvatars)) {
        // Prepare the SQL query to update the profile image
        $sql = "UPDATE users SET profileimg = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("si", $avatar, $user_id);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update profile image."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid avatar selected."]);
    }
} 
// Handle custom image upload
elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $file = $_FILES['profile_image'];

    // Validate file type and size
    $allowedTypes = ['image/jpeg', 'image/png'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
        // Read the file content
        $imageData = file_get_contents($file['tmp_name']);

        // Prepare the SQL query to update the profile image
        $sql = "UPDATE users SET profileimg = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("si", $imageData, $user_id);
            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update profile image."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid file type or size."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No avatar or image selected."]);
}
?>