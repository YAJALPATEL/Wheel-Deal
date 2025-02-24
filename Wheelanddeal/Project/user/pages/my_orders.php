<?php
session_start();
include('../assets/includes/db_connection.php');
include('../assets/includes/header.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT orders.*, products.productName, products.productPrice FROM orders
        JOIN products ON orders.product_id = products.id
        WHERE orders.user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">My Orders</h2>

        <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['order_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['productName']); ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo $order['order_status']; ?></td>
                    <td><?php echo $order['order_date']; ?></td>
                    <td><a href="product_details.php?id=<?php echo $order['product_id']; ?>"
                            class="btn btn-primary">View
                            Details</a></td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center">No orders placed yet.</p>
        <?php endif; ?>

        <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>