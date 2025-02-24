<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="section">
        <h3>Add New Product</h3>

        <?php
        // Include database connection
        include_once '../assets/includes/db_connection.php';

        // Fetch categories from the database
        $categories = [];
        $sql = "SELECT id, categoryName FROM categories ORDER BY categoryName ASC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = $row;
            }
        }
        ?>

        <form action="../assets/php/add_product_action.php" method="POST" enctype="multipart/form-data">
            <label for="productName">Product Name:</label>
            <input type="text" id="productName" name="productName" required>

            <label for="productCompany">Product Company:</label>
            <input type="text" id="productCompany" name="productCompany" required>

            <label for="productPrice">Product Price:</label>
            <input type="number" id="productPrice" name="productPrice" required>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category['id']); ?>">
                    <?php echo htmlspecialchars($category['categoryName']); ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label for="productDescription">Product Description:</label></br>
            <textarea id="productDescription" name="productDescription" rows="5" required></textarea></br></br>

            <label for="productImage1">Product Image 1:</label>
            <input type="file" id="productImage1" name="productImage1" required>

            <label for="productImage2">Product Image 2:</label>
            <input type="file" id="productImage2" name="productImage2">

            <label for="productImage3">Product Image 3:</label>
            <input type="file" id="productImage3" name="productImage3">

            <label for="productImage4">Product Image 4:</label>
            <input type="file" id="productImage4" name="productImage4">

            <label for="shippingCharge">Shipping Charge:</label>
            <input type="number" id="shippingCharge" name="shippingCharge" required>

            <label for="productAvailability">Product Availability:</label>
            <select id="productAvailability" name="productAvailability" required>
                <option value="">Select Availability</option>
                <option value="In Stock">In Stock</option>
                <option value="Out of Stock">Out of Stock</option>
            </select>
            <button type="submit" class="btn-primary">Add Product</button>
        </form>
    </div>
</body>

</html>