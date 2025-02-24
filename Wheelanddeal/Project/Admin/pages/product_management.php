<?php
// Start session and include database connection
session_start();
include_once('../assets/includes/db_connection.php');

// Handle AJAX request to update top models BEFORE any HTML is output
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    header('Content-Type: application/json'); // Ensure response is JSON
    $response = [];

    if (isset($_POST['top_models']) && count($_POST['top_models']) == 3) {
        $selectedModels = $_POST['top_models'];

        // Reset all products to is_top_model = 0
        $resetQuery = "UPDATE products SET is_top_model = 0";
        if (!mysqli_query($conn, $resetQuery)) {
            $response['status'] = "error";
            $response['message'] = "Failed to reset previous top models.";
            echo json_encode($response);
            exit();
        }

        // Set the selected products as top models
        $updateQuery = "UPDATE products SET is_top_model = 1 WHERE id IN (" . implode(",", array_map('intval', $selectedModels)) . ")";
        
        if (mysqli_query($conn, $updateQuery)) {
            $response['status'] = "success";
            $response['message'] = "Top models updated successfully!";
        } else {
            $response['status'] = "error";
            $response['message'] = "Database update failed.";
        }
    } else {
        $response['status'] = "error";
        $response['message'] = "Please select exactly 3 top models.";
    }

    echo json_encode($response);
    exit();
}

// Fetch product data after handling AJAX
$sql = "SELECT products.id, products.productName, categories.categoryName, products.productPrice, 
               products.productCompany, products.postingDate, products.updationDate, products.productAvailability, products.is_top_model
        FROM products 
        JOIN categories ON products.category = categories.id 
        ORDER BY products.id ASC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, init ial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="http://localhost/wheelanddeal/Project/Admin/assets/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Event Delegation for Checkboxes
        $(document).on("click", ".top-model-checkbox", function() {
            let checkedBoxes = $(".top-model-checkbox:checked");

            if (checkedBoxes.length > 3) {
                alert("You can only select up to 3 top models.");
                $(this).prop("checked", false);
            }

            if (checkedBoxes.length === 3) {
                $("#message").text(""); // Hide message
            }
        });

        // AJAX Form Submission
        $(document).on("submit", "#topModelsForm", function(e) {
            e.preventDefault(); // Prevent Default Form Submission (No Reload)

            let selectedModels = [];
            $(".top-model-checkbox:checked").each(function() {
                selectedModels.push($(this).val());
            });

            if (selectedModels.length !== 3) {
                $("#message").text("Please select exactly 3 top models.").css({
                    "color": "red",
                    "font-weight": "bold"
                });

                // **Clear message after 5 seconds**
                setTimeout(function() {
                    $("#message").fadeOut("slow", function() {
                        $(this).text("").show();
                    });
                }, 5000);

                return;
            }

            $.ajax({
                type: "POST",
                url: window.location.href, // Ensure AJAX hits the correct PHP file
                data: {
                    top_models: selectedModels,
                    update: true
                },
                dataType: "json",
                success: function(response) {
                    console.log("Response Received:", response);

                    $("#message").text(response.message).css({
                        "color": response.status === "success" ? "green" : "red",
                        "font-weight": "bold"
                    });

                    // **Expire message after 5 seconds**
                    setTimeout(function() {
                        $("#message").fadeOut("slow", function() {
                            $(this).text("").show();
                        });
                    }, 3000);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    $("#message").text("An error occurred while processing.").css({
                        "color": "red",
                        "font-weight": "bold"
                    });
                }
            });
        });
    });
    </script>
</head>

<body>
    <div class="section">
        <h3>Product Management</h3>

        <form id="topModelsForm">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Company</th>
                        <th>Posting Date</th>
                        <th>Last Updated</th>
                        <th>Status</th>
                        <th>Top Model</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['productName']; ?></td>
                        <td><?php echo $row['categoryName']; ?></td>
                        <td>$<?php echo number_format($row['productPrice'], 2); ?></td>
                        <td><?php echo $row['productCompany']; ?></td>
                        <td><?php echo $row['postingDate']; ?></td>
                        <td><?php echo !empty($row['updationDate']) ? $row['updationDate'] : 'N/A'; ?></td>
                        <td><?php echo $row['productAvailability']; ?></td>
                        <td>
                            <input type="checkbox" name="top_models[]" value="<?php echo $row['id']; ?>"
                                class="top-model-checkbox" <?php echo $row['is_top_model'] ? 'checked' : ''; ?>>
                        </td>
                        <td>
                            <a href='http://localhost/wheelanddeal/Project/Admin/pages/edit_product.php?id=<?php echo $row['id']; ?>'
                                class='button'>Edit</a>
                            <a href='./assets/php/delete_product.php?id=<?php echo $row['id']; ?>' class='button'
                                onclick='return confirm("Are you sure you want to delete this product?")'>Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Save Button for Top Models -->
            <button type="submit" class="btn-primary">Save</button>
            <p id="message"></p>
        </form>

        <!-- Add Product Button -->
        <div class="add-product">
            <a href="pages/add_product.php" target="_blank" class="btn-primary">Add New Product</a>
        </div>
    </div>
</body>

</html>

<?php mysqli_close($conn); ?>