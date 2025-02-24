<?php
// Include database connection
include_once('../includes/db_connection.php');

// Get the product ID from the URL
$productId = isset($_GET['id']) ? $_GET['id'] : 0;

// Ensure the ID is valid
if ($productId > 0) {
    // First, fetch the product details to delete images (if needed)
    $sql = "SELECT * FROM products WHERE id = $productId";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        // Delete product images from the server (if they exist)
        $imagePaths = [
            '../assets/images/' . $product['productImage1'],
            '../assets/images/' . $product['productImage2'],
            '../assets/images/' . $product['productImage3'],
            '../assets/images/' . $product['productImage4']
        ];

        foreach ($imagePaths as $imagePath) {
            if (file_exists($imagePath) && !empty($imagePath)) {
                unlink($imagePath); // Delete the image file from the server
            }
        }

        // Delete the product from the database
        $deleteSql = "DELETE FROM products WHERE id = $productId";
        if (mysqli_query($conn, $deleteSql)) {
            // Redirect to the product management page after successful deletion
            header("Location: ../../index.php?message=Product deleted successfully.");
            exit();
        } else {
            // If there's an error during deletion
            echo "Error deleting product: " . mysqli_error($conn);
        }
    } else {
        echo "Product not found.";
    }
} else {
    echo "Invalid product ID.";
}

// Close the connection
mysqli_close($conn);
?>