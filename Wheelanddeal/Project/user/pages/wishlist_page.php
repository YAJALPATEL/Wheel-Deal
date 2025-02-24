<?php
session_start();
include('../assets/includes/db_connection.php');
include('../assets/includes/header.php');

if (!isset($_SESSION['user_id'])) {
    echo "<div class='container mt-5 text-center'><h3 class='text-danger'>Please log in to view your wishlist.</h3></div>";
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT p.id, p.productName, p.productPrice, p.productImage1
          FROM wishlist w
          JOIN products p ON w.product_id = p.id
          WHERE w.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Bundle (includes Popper.js for dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">My Wishlist</h1>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img src="data:image/jpeg;base64,<?= base64_encode($row['productImage1']); ?>" class="card-img-top"
                        alt="Product Image">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($row['productName']); ?></h5>
                        <p class="card-text">$<?= number_format($row['productPrice'], 2); ?></p>
                        <a href="product_details.php?id=<?= $row['id']; ?>" class="btn btn-primary">View Product</a>
                        <button class="btn btn-danger remove-wishlist" data-id="<?= $row['id']; ?>">Remove</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
    document.querySelectorAll('.remove-wishlist').forEach(button => {
        button.addEventListener('click', function() {
            var productId = this.getAttribute('data-id');
            var card = this.closest('.card');

            fetch('../assets/php/wishlist_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.text())
                .then(response => {
                    if (response === "removed") {
                        card.remove();
                    }
                });
        });
    });
    </script>
</body>

</html>
<?php include('../assets/includes/footer.php')?>
<?php
$stmt->close();
$conn->close();
?>