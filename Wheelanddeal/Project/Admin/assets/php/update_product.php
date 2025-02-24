<?php
// Start session and include database connection
session_start();
include_once('../includes/db_connection.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the product ID from the form
    $productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    // Fetch the existing product data
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        $_SESSION['error'] = "Product not found.";
        header("Location: ../../pages/product_management.php");
        exit();
    }

    // Initialize an array to hold the update fields and values
    $updateFields = [];
    $updateValues = [];

    // Check each field and add it to the update array if it's provided
    if (isset($_POST['productName'])) {
        $updateFields[] = "productName = ?";
        $updateValues[] = $_POST['productName'];
    }

    if (isset($_POST['category'])) {
        $updateFields[] = "category = ?";
        $updateValues[] = (int)$_POST['category'];
    }

    if (isset($_POST['productCompany'])) {
        $updateFields[] = "productCompany = ?";
        $updateValues[] = $_POST['productCompany'];
    }

    if (isset($_POST['productPrice'])) {
        $updateFields[] = "productPrice = ?";
        $updateValues[] = (float)$_POST['productPrice'];
    }

    if (isset($_POST['productDescription'])) {
        $updateFields[] = "productDescription = ?";
        $updateValues[] = $_POST['productDescription'];
    }

    if (isset($_POST['shippingCharge'])) {
        $updateFields[] = "shippingCharge = ?";
        $updateValues[] = (float)$_POST['shippingCharge'];
    }

    if (isset($_POST['productAvailability'])) {
        $updateFields[] = "productAvailability = ?";
        $updateValues[] = $_POST['productAvailability'];
    }

    // Handle file uploads for product images (BLOB)
    $imageFields = ['productImage1', 'productImage2', 'productImage3', 'productImage4'];
    foreach ($imageFields as $imageField) {
        if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] == 0) {
            // Read the file content
            $imageContent = file_get_contents($_FILES[$imageField]['tmp_name']);

            // Check if the file content is valid
            if ($imageContent === false) {
                $_SESSION['error'] = "Failed to read $imageField.";
                header("Location: ../../pages/edit_product.php?id=$productId");
                exit();
            }

            // Add the BLOB data to the update query
            $updateFields[] = "$imageField = ?";
            $updateValues[] = $imageContent;
        }
    }

    // If there are fields to update, prepare and execute the SQL query
    if (!empty($updateFields)) {
        $updateFields[] = "updationDate = NOW()"; // Update the updation date
        $updateQuery = "UPDATE products SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $updateValues[] = $productId;

        $stmt = $conn->prepare($updateQuery);
        $types = str_repeat('s', count($updateValues)); // Assuming all values are strings
        $stmt->bind_param($types, ...$updateValues);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update product.";
        }
    } else {
        $_SESSION['error'] = "No fields to update.";
    }

    // Redirect back to the product management page
    header("Location: ../../index.php");
    exit();
} else {
    // If the form is not submitted, redirect to the product management page
    header("Location: ../../pages/product_management.php");
    exit();
}
?>