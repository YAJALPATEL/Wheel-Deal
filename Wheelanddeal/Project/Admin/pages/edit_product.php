<?php  
// Include database connection  
include_once('../assets/includes/db_connection.php');  

// Get product ID from URL  
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;  

// Fetch product data from the database  
$sql = "SELECT * FROM products WHERE id = ?";  
$stmt = $conn->prepare($sql);  
$stmt->bind_param("i", $productId);  
$stmt->execute();  
$result = $stmt->get_result();  
$product = $result->fetch_assoc();  

// If the product doesn't exist, redirect to the product management page  
if (!$product) {  
    header("Location: ../../pages/product_management.php?error=Product not found.");  
    exit();  
}  

// Fetch categories for the dropdown (assuming categories table exists)  
$categoriesSql = "SELECT * FROM categories";  
$categoriesResult = mysqli_query($conn, $categoriesSql);  
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <div class="section">
        <h3>Edit Product</h3>
        <!-- Edit Product Form -->
        <form action="../assets/php/update_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
            <label for="productName">Product Name</label>
            <input type="text" name="productName" value="<?php echo htmlspecialchars($product['productName']); ?>"
                required>

            <label for="category">Category</label>
            <select name="category" required>
                <?php while ($category = mysqli_fetch_assoc($categoriesResult)) {  
                    $selected = $category['id'] == $product['category'] ? 'selected' : '';  
                    echo "<option value='{$category['id']}' {$selected}>{$category['categoryName']}</option>";  
                } ?>
            </select>

            <label for="productCompany">Product Company</label>
            <input type="text" name="productCompany" value="<?php echo htmlspecialchars($product['productCompany']); ?>"
                required>

            <label for="productPrice">Price</label>
            <input type="number" name="productPrice" value="<?php echo htmlspecialchars($product['productPrice']); ?>"
                required>

            <label for="productDescription">Description</label>
            <textarea name="productDescription"
                required><?php echo htmlspecialchars($product['productDescription']); ?></textarea>

            <!-- Image Handling for BLOB Storage -->
            <?php 
            for ($i = 1; $i <= 4; $i++) {
                $imageField = "productImage$i";
                if (!empty($product[$imageField])) {
                    echo "<label>Current Product Image $i</label>";
                    echo "<br><img src='data:image/jpeg;base64," . base64_encode($product[$imageField]) . "' width='100' alt='Image $i'><br>";
                }
                echo "<label for='$imageField'>Upload New Image $i</label>";
                echo "<input type='file' name='$imageField'>";
            }
            ?>

            <label for="shippingCharge">Shipping Charge</label>
            <input type="number" name="shippingCharge"
                value="<?php echo htmlspecialchars($product['shippingCharge']); ?>" required>

            <label for="productAvailability">Availability</label>
            <input type="text" name="productAvailability"
                value="<?php echo htmlspecialchars($product['productAvailability']); ?>" required>

            <button type="submit" class="btn-primary">Update Product</button>
        </form>
    </div>
</body>

</html>

<?php  
// Close the connection  
mysqli_close($conn);  
?>