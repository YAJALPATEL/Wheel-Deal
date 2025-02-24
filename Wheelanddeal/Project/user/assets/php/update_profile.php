<?php
session_start();
require_once __DIR__ . '/../includes/db_connection.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $favorite_things = $_POST['favorite_things'];

    // Handle image upload
    $profile_image = null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['profile_image']['tmp_name'];
        $file_type = $_FILES['profile_image']['type'];

        // Validate file type
        if (in_array($file_type, ['image/jpeg', 'image/png'])) {
            $profile_image = file_get_contents($file_tmp_path); // Read file as binary data
        } else {
            die("Invalid file type. Only JPEG and PNG are allowed.");
        }
    }

    // Update user data in the database
    $sql = "UPDATE users 
            SET username = ?, email = ?, favorite_things = ?, profileimg = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssbi", $username, $email, $favorite_things, $profile_image, $user_id);
        $stmt->send_long_data(3, $profile_image); // Handle large BLOB data
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: profile_content.php");
            exit();
        } else {
            die("Error updating profile: No rows affected.");
        }

        $stmt->close();
    } else {
        die("Error preparing SQL statement: " . $conn->error);
    }
}
?>