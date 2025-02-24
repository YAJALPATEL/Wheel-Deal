<?php
session_start();
include('../assets/includes/db_connection.php'); // Database connection

// Get selected category from URL
$category_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$category_name = "All Products"; // Default

if ($category_id) {
    // Fetch category details
    $category_sql = "SELECT categoryName FROM categories WHERE id = ?";
    $category_stmt = $conn->prepare($category_sql);
    $category_stmt->bind_param("i", $category_id);
    $category_stmt->execute();
    $category_result = $category_stmt->get_result();
    $category_data = $category_result->fetch_assoc();
    $category_stmt->close();

    if ($category_data) {
        $category_name = $category_data['categoryName'];
    }
}

// Get filter parameters
$brand = isset($_GET['brand']) ? $_GET['brand'] : null;
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : null;

// Fetch brands for filter
$brand_sql = "SELECT DISTINCT productCompany FROM products";
$brand_result = $conn->query($brand_sql);

// Construct product query
$products_sql = "SELECT id, productName, productCompany, productPrice, productDescription, shippingCharge, productAvailability, productImage1 FROM products WHERE 1";

// Apply filters
if ($category_id) {
    $products_sql .= " AND category = $category_id";
}
if ($brand) {
    $products_sql .= " AND productCompany = '" . $conn->real_escape_string($brand) . "'";
}
if ($min_price) {
    $products_sql .= " AND productPrice >= $min_price";
}
if ($max_price) {
    $products_sql .= " AND productPrice <= $max_price";
}

$products_result = $conn->query($products_sql);

// Fetch products
$products = [];
while ($row = $products_result->fetch_assoc()) {
    $row['productImage1'] = base64_encode($row['productImage1']);
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo htmlspecialchars($category_name); ?> | Wheel And Deal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
        width: 100%;
        max-width: 300px;
        /* Adjusted for better size */
        height: 450px;
        /* Increased height */
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
    }

    .card img {
        width: 100%;
        height: 150px;
        /* Slightly larger images */
        object-fit: cover;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 10px;
        width: 100%;
        flex-grow: 1;
    }

    .card-title {
        font-size: 16px;
        font-weight: bold;
    }

    .card-text {
        font-size: 14px;
        margin-bottom: 5px;
    }

    /* ✅ Scrollable Product Description Only */
    .scrollable-description {
        height: 60px;
        /* Fixed height for long descriptions */
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #ccc #f5f5f5;
    }

    /* Custom scrollbar for Webkit browsers */
    .scrollable-description::-webkit-scrollbar {
        width: 5px;
    }

    .scrollable-description::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 5px;
    }

    /* Buttons remain at the bottom */
    .card-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: auto;
        /* Pushes buttons to the bottom */
    }

    /* Button Effects */
    .btn {
        transition: background 0.3s ease, transform 0.2s ease;
        font-size: 14px;
        padding: 8px 12px;
    }

    .btn:hover {
        transform: scale(1.05);
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    /* Filter Section */
    .filter-section {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .filter-section select,
    .filter-section input {
        border-radius: 5px;
    }
    </style>
</head>

<body>
    <?php include('../assets/includes/header.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center"><?php echo htmlspecialchars($category_name); ?> Products</h2>

        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-md-3">
                <h4>Filters</h4>
                <form method="GET">
                    <input type="hidden" name="id" value="<?= $category_id; ?>">

                    <div class="filter-section">
                        <label><strong>Brand</strong></label>
                        <select name="brand" class="form-control mb-2">
                            <option value="">All Brands</option>
                            <?php while ($br = $brand_result->fetch_assoc()) : ?>
                            <option value="<?= htmlspecialchars($br['productCompany']); ?>"
                                <?= ($brand == $br['productCompany']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($br['productCompany']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="filter-section">
                        <label><strong>Price Range</strong></label>
                        <input type="number" name="min_price" class="form-control mb-2" placeholder="Min Price"
                            value="<?= $min_price ?? ''; ?>">
                        <input type="number" name="max_price" class="form-control mb-2" placeholder="Max Price"
                            value="<?= $max_price ?? ''; ?>">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </form>
            </div>

            <!-- Product Listing -->
            <div class="col-md-9">
                <?php if (empty($products)) : ?>
                <p class="text-center text-danger">No products found.</p>
                <?php else : ?>
                <div class="row">
                    <?php foreach ($products as $product) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="data:image/jpeg;base64,<?= $product['productImage1']; ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($product['productName']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['productName']); ?></h5>
                                <p class="card-text scrollable-description">
                                    <?= nl2br(htmlspecialchars($product['productDescription'])); ?></p>
                                <p class="card-text">Brand: <?= htmlspecialchars($product['productCompany']); ?></p>
                                <p class="card-text">Price: ₹<?= number_format($product['productPrice'], 2); ?></p>
                                <p class="card-text">Shipping: ₹<?= number_format($product['shippingCharge'], 2); ?></p>
                                <p class="card-text">
                                    <strong><?= $product['productAvailability'] ? "In Stock" : "Out of Stock"; ?></strong>
                                </p>
                                <a href="product_details.php?id=<?= $product['id']; ?>" class="btn btn-primary">View
                                    Details</a>
                                <form action="http://localhost/wheelanddeal/Project/user/assets/php/add_to_cart.php"
                                    method="POST" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                    <input type="hidden" name="product_name"
                                        value="<?= htmlspecialchars($product['productName']); ?>">
                                    <input type="hidden" name="product_price" value="<?= $product['productPrice']; ?>">
                                    <button type="submit" class="btn btn-success">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('../assets/includes/footer.php'); ?>
</body>

</html>

<?php $conn->close(); ?>