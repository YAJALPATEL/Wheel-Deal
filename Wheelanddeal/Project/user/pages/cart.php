<?php
session_start();
include('../assets/includes/header.php');
// Ensure cart session is set
$cart = $_SESSION['cart'] ?? [];
$total_amount = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">🛒 Your Shopping Cart</h2>

        <?php if (empty($cart)) : ?>
        <p class="text-center text-danger">Your cart is empty.</p>
        <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
        <?php else : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $index => $item) :
                    $subtotal = $item['product_price'] * $item['quantity'];
                    $total_amount += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td>₹<?php echo number_format($item['product_price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($subtotal, 2); ?></td>
                    <td>
                        <a href="../assets/php/remove_from_cart.php?index=<?php echo $index; ?>"
                            class="btn btn-danger btn-sm">❌</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 class="text-end">Total: ₹<?php echo number_format($total_amount, 2); ?></h3>

        <form action="checkout.php" method="POST">
            <button type="submit" class="btn btn-success">Proceed to Checkout</button>
        </form>
        <?php endif; ?>
    </div>
</body>

</html>