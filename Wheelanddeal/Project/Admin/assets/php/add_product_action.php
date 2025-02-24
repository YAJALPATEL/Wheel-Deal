<?php
// Include database connection
include_once('../includes/db_connection.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $productCompany = mysqli_real_escape_string($conn, $_POST['productCompany']);
    $productPrice = mysqli_real_escape_string($conn, $_POST['productPrice']);
    $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription']);
    $shippingCharge = mysqli_real_escape_string($conn, $_POST['shippingCharge']);
    $productAvailability = mysqli_real_escape_string($conn, $_POST['productAvailability']);
    $postingDate = date("Y-m-d H:i:s"); // Current date and time
    $updationDate = NULL;

    // Prepare to handle image uploads
    $images = [];
    $imageFields = ['productImage1', 'productImage2', 'productImage3', 'productImage4'];

    foreach ($imageFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
            // Read the image file and encode it to binary
            $imageData = file_get_contents($_FILES[$field]['tmp_name']);
            $images[$field] = mysqli_real_escape_string($conn, $imageData);
        } else {
            $images[$field] = null; // If no file uploaded, set to NULL
        }
    }

    // Insert the product data into the database
    $sql = "INSERT INTO products (category, productName, productCompany, productPrice, productDescription, productImage1, productImage2, productImage3, productImage4, shippingCharge, productAvailability, postingDate, updationDate)
            VALUES ('$category', '$productName', '$productCompany', '$productPrice', '$productDescription', 
                    '{$images['productImage1']}', '{$images['productImage2']}', '{$images['productImage3']}', '{$images['productImage4']}', 
                    '$shippingCharge', '$productAvailability', '$postingDate', '$updationDate')";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the product management page after successful insert
        header("Location: ../../pages/add_product.php?success=Product added successfully.");
        exit();
    } else {
        // If there's an error during insertion
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close the connection
    mysqli_close($conn);
}
?>