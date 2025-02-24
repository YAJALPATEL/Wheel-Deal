<?php
session_start();
include '../assets/includes/db_connection.php';

// Fetch all orders for displaying in table
$result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="http://localhost/wheelanddeal/Project/user/assets/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    /* General Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        padding: 20px;
    }

    /* Page Title */
    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    /* Order Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    table th,
    table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table th {
        background-color: #34495e;
        color: white;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    /* Dropdown Styling */
    .order-status {
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: #fff;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .order-status:focus {
        outline: none;
        border-color: #3498db;
    }

    /* Buttons */
    .update-status {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .update-status:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        table {
            font-size: 14px;
        }

        .order-status {
            font-size: 12px;
            padding: 6px;
        }

        .update-status {
            font-size: 12px;
            padding: 6px 10px;
        }
    }
    </style>
</head>

<body>
    <h2>Order Management</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Product ID</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Shipping Address</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
                <td><?= htmlspecialchars($order['user_id']) ?></td>
                <td><?= htmlspecialchars($order['product_id']) ?></td>
                <td><?= htmlspecialchars($order['quantity']) ?></td>
                <td>$<?= htmlspecialchars($order['total_price']) ?></td>
                <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                <td>
                    <select class="order-status" data-order-id="<?= $order['order_id'] ?>">
                        <option value="Pending" <?= $order['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending
                        </option>
                        <option value="Shipped" <?= $order['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped
                        </option>
                        <option value="Delivered" <?= $order['order_status'] == 'Delivered' ? 'selected' : '' ?>>
                            Delivered</option>
                    </select>
                </td>
                <td>
                    <button class="update-status" data-order-id="<?= $order['order_id'] ?>">Update</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    $(document).ready(function() {
        $(document).on("click", ".update-status", function() {
            let orderId = $(this).data("order-id");
            let newStatus = $(this).closest('tr').find('.order-status').val();

            console.log("Order ID:", orderId); // Debugging
            console.log("New Status:", newStatus); // Debugging

            $.ajax({
                type: "POST",
                url: "../assets/php/update_order_status.php",
                data: {
                    order_id: orderId,
                    order_status: newStatus
                },
                dataType: "json",
                success: function(response) {
                    console.log("AJAX Success:", response); // Debugging
                    alert(response.message);
                    if (response.success) {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error); // Debugging
                    alert("AJAX Error: " + xhr.responseText);
                }
            });
        });
    });
    </script>
</body>

</html>