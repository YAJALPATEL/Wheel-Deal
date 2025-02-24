<?php
// Include database connection
include_once('../includes/db_connection.php');

// Check if the ID parameter is provided
if (isset($_GET['id'])) {
    $categoryId = $_GET['id']; // Get the category ID from the URL

    // Validate the ID (ensure it's a valid number)
    if (is_numeric($categoryId) && $categoryId > 0) {
        // Prepare the delete query
        $sql = "DELETE FROM categories WHERE id = $categoryId";

        // Execute the delete query
        if (mysqli_query($conn, $sql)) {
            // Redirect to categories management page after successful deletion
            header("Location: ../../index.php?message=Category deleted successfully.");
            exit();
        } else {
            // If the deletion fails, show an error message
            echo "Error deleting category: " . mysqli_error($conn);
        }
    } else {
        // If the ID is invalid, show an error message
        echo "Invalid category ID.";
    }
} else {
    // If no ID is provided, show an error message
    echo "Category ID not provided.";
}

// Close the database connection
mysqli_close($conn);
?>