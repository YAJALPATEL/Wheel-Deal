<?php
session_start();
include('../assets/includes/db_connection.php');
include('../assets/includes/header.php');

$user_id = $_SESSION['user_id'] ?? null;

// Validate product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    die("<div class='container mt-5 text-center'><h3 class='text-danger'>Invalid Product ID.</h3></div>");
}

// Fetch product details
$sql = "SELECT productName, productPrice, productDescription, shippingCharge, productAvailability, 
                productImage1, productImage2, productImage3, productImage4
        FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='container mt-5 text-center'><h3 class='text-danger'>Product not found.</h3></div>";
    exit();
}

$product = $result->fetch_assoc();

// Check if product is already in wishlist
$is_wishlisted = false;
if ($user_id) {
    $wishlist_check = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
    $wishlist_stmt = $conn->prepare($wishlist_check);
    $wishlist_stmt->bind_param("ii", $user_id, $product_id);
    $wishlist_stmt->execute();
    $wishlist_stmt->store_result();
    $is_wishlisted = $wishlist_stmt->num_rows > 0;
    $wishlist_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['productName']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="http://localhost/wheelanddeal/Project/user/assets/css/styles.css">

    <style>
    body {
        background-color: #f8f9fa;
    }

    .product-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 50px;
    }

    .product-image {
        position: relative;
        text-align: center;
    }

    .wishlist-btn {
        position: absolute;
        top: 1px;
        right: 3px;
        font-size: 28px;
        cursor: pointer;
        background: none;
        border: none;
        outline: none;
        z-index: 1000;
        color: <?php echo $is_wishlisted ? 'red': 'gray';
        ?>;
    }

    .wishlist-btn:hover {
        color: red;
    }

    .carousel-item img {
        border-radius: 8px;
        max-width: 100%;
        height: auto;
    }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="product-card">
                    <h1 class="text-center mb-4"><?php echo htmlspecialchars($product['productName']); ?></h1>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="product-image">
                                <!-- Wishlist Heart Icon -->
                                <button id="wishlist-btn" class="wishlist-btn"
                                    onclick="toggleWishlist(<?php echo $product_id; ?>)">
                                    <?php echo $is_wishlisted ? '❤️' : '🤍'; ?>
                                </button>

                                <!-- Bootstrap Carousel for Product Images -->
                                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php 
                                        $images = [
                                            $product['productImage1'],
                                            $product['productImage2'],
                                            $product['productImage3'],
                                            $product['productImage4']
                                        ];
                                        $first = true;
                                        foreach ($images as $image) {
                                            if (!empty($image)) {
                                                echo '<div class="carousel-item ' . ($first ? 'active' : '') . '">';
                                                echo '<img src="data:image/jpeg;base64,' . base64_encode($image) . '" class="d-block w-100" alt="Product Image">';
                                                echo '</div>';
                                                $first = false;
                                            }
                                        }
                                        ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#productCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#productCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h2 class="text-success">₹<?php echo number_format($product['productPrice'], 2); ?></h2>
                            <p><?php echo nl2br(htmlspecialchars($product['productDescription'])); ?></p>
                            <p><strong>Shipping:</strong> ₹<?php echo number_format($product['shippingCharge'], 2); ?>
                            </p>
                            <p><strong>Availability:</strong>
                                <?php echo htmlspecialchars($product['productAvailability']); ?></p>

                            <!-- Add to Cart -->
                            <form action="../assets/php/add_to_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="hidden" name="product_name"
                                    value="<?php echo htmlspecialchars($product['productName']); ?>">
                                <input type="hidden" name="product_price"
                                    value="<?php echo $product['productPrice']; ?>">
                                <button type="submit" class="btn btn-primary mt-3">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                    <div class="mt-4 p-3 border rounded bg-white">
                        <h4>Customer Reviews</h4>
                        <div id="average-rating">
                            <?php
        // Fetch average rating
        $rating_sql = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE product_id = ?";
        $stmt = $conn->prepare($rating_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $rating_result = $stmt->get_result();
        $rating_data = $rating_result->fetch_assoc();
        $avg_rating = round($rating_data['avg_rating'], 1);

        echo "<h5>⭐ $avg_rating / 5</h5>";
        ?>
                        </div>

                        <!-- Display reviews -->
                        <div id="reviews" class="mt-3" style="max-height: 200px; overflow-y: auto;">
                            <?php
        $review_sql = "SELECT u.username, r.rating, r.review_text, r.created_at 
                       FROM reviews r 
                       JOIN users u ON r.user_id = u.id 
                       WHERE r.product_id = ? 
                       ORDER BY r.created_at DESC";

        $stmt = $conn->prepare($review_sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $review_result = $stmt->get_result();

        if ($review_result->num_rows > 0) {
            while ($review = $review_result->fetch_assoc()) {
                echo "<div class='border-bottom py-2'>
                        <strong>" . htmlspecialchars($review['username']) . "</strong> 
                        <span class='text-warning'>⭐ " . $review['rating'] . "/5</span>
                        <p>" . nl2br(htmlspecialchars($review['review_text'])) . "</p>
                        <small class='text-muted'>" . $review['created_at'] . "</small>
                      </div>";
            }
        } else {
            echo "<p>No reviews yet.</p>";
        }
        ?>
                        </div>

                        <!-- Submit a Review -->
                        <?php if ($user_id): ?>
                        <h5 class="mt-3">Leave a Review</h5>
                        <form id="reviewForm">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                            <label for="rating">Rating:</label>
                            <select name="rating" id="rating" class="form-select w-25" required>
                                <option value="5">⭐ 5</option>
                                <option value="4">⭐ 4</option>
                                <option value="3">⭐ 3</option>
                                <option value="2">⭐ 2</option>
                                <option value="1">⭐ 1</option>
                            </select>
                            <label for="review" class="mt-2">Review:</label>
                            <textarea name="review" id="review" class="form-control" required></textarea>
                            <button type="submit" class="btn btn-primary mt-2">Submit Review</button>
                        </form>
                        <?php else: ?>
                        <p class="text-danger mt-3">You must <a href="../login.php">log in</a> to leave a review.</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleWishlist(productId) {
        var heartIcon = document.getElementById("wishlist-btn");

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../assets/php/wishlist_action.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function() {
            if (xhr.status == 200) {
                let response = xhr.responseText.trim(); // Trim to remove unwanted whitespace
                console.log("Response:", response); // Debugging

                if (response === "added") {
                    heartIcon.innerHTML = "❤️";
                    heartIcon.style.color = "red";
                } else if (response === "removed") {
                    heartIcon.innerHTML = "🤍";
                    heartIcon.style.color = "gray";
                }
            }
        };

        xhr.send("product_id=" + productId);
    }
    </script>
    <?php include('../assets/includes/footer.php'); ?>
    <script>
    document.getElementById("reviewForm").addEventListener("submit", function(event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch("../assets/php/submit_review.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    alert("Review submitted successfully!");
                    location.reload();
                } else {
                    alert("Error: " + data);
                }
            })
            .catch(error => console.error("Error:", error));
    });
    </script>
    <a href="https://wa.me/7046085161" target="_blank" class="whatsapp-float">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="Chat on WhatsApp">
    </a>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>