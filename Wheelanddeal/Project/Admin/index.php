<?php
session_start(); // Ensure session is started
if ($_SESSION['admin_logged_in'] == false) { // Fix the comparison
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
?>

<?php include './assets/includes/db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    const BASE_URL = "/wheelanddeal/Project/Admin/"; // Define base URL
    </script>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#" onclick="loadContent('pages/user_management.php')">Users</a></li>
            <li><a href="#" onclick="loadContent('pages/categories.php')">Category</a></li>
            <li><a href="#" onclick="loadContent('pages/product_management.php')">Product Management</a></li>
            <li><a href="#" onclick="loadContent('pages/order_manegment.php')">Order Management</a></li>
            <li><a href="#" onclick="loadContent('pages/user_messages.php')">Users Massages</a></li>
        </ul>
        <div class="logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="content">
        <h1>Welcome to the Admin Dashboard</h1>
        <div id="content-area">
            <p>Select an option from the sidebar to manage.</p>
        </div>
    </div>
    <script>
    function loadContent(page) {
        const contentArea = document.getElementById('content-area');

        fetch(page)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response was not ok: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                contentArea.innerHTML = data;

                // ✅ If "user_management.php" is loaded, fetch users
                if (page.includes('user_management.php')) {
                    console.log("📄 user_management.php loaded. Fetching users...");
                    fetchUsers(); // Call fetchUsers directly
                }

                // ✅ Reinitialize event listeners for product management
                initProductManagementEvents();
                initOrderManagementEvents();
            })
            .catch(error => {
                console.error("Error loading content:", error);
                contentArea.innerHTML = `<p>Error loading page: ${error.message}</p>`;
            });
    }

    function initOrderManagementEvents() {
        $(document).off("click", ".update-status").on("click", ".update-status", function() {
            let orderId = $(this).data("order-id");
            let newStatus = $(this).closest('tr').find('.order-status').val();

            console.log("Order ID:", orderId); // Debugging
            console.log("New Status:", newStatus); // Debugging

            $.ajax({
                type: "POST",
                url: "assets/php/update_order_status.php", // Updated endpoint
                data: {
                    order_id: orderId,
                    order_status: newStatus
                },
                dataType: "json",
                success: function(response) {
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
    }

    // ✅ Function to reinitialize event handlers
    function initProductManagementEvents() {
        $(document).off("submit", "#topModelsForm").on("submit", "#topModelsForm", function(e) {
            e.preventDefault();

            let selectedModels = [];
            $(".top-model-checkbox:checked").each(function() {
                selectedModels.push($(this).val());
            });

            if (selectedModels.length !== 3) {
                $("#message").text("Please select exactly 3 top models.").css({
                    "color": "red",
                    "font-weight": "bold"
                });

                setTimeout(() => {
                    $("#message").fadeOut("slow").text("").show();
                }, 5000);
                return;
            }

            $.ajax({
                type: "POST",
                url: "pages/product_management.php",
                data: {
                    top_models: selectedModels,
                    update: true
                },
                dataType: "json",
                success: function(response) {
                    console.log("AJAX Success:", response); // Debugging output
                    $("#message").text(response.message).css({
                        "color": response.status === "success" ? "green" : "red",
                        "font-weight": "bold"
                    });

                    setTimeout(() => {
                        $("#message").fadeOut("slow").text("").show();
                    }, 5000);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error); // Log error in console
                    console.log("Response Text:", xhr.responseText); // Log full response

                    alert("Error: " + xhr.responseText); // Show full error message
                }
            });
        });

        // ✅ Checkbox validation
        $(document).off("click", ".top-model-checkbox").on("click", ".top-model-checkbox", function() {
            let checkedBoxes = $(".top-model-checkbox:checked");
            if (checkedBoxes.length > 3) {
                alert("You can only select up to 3 top models.");
                $(this).prop("checked", false);
            }
            if (checkedBoxes.length === 3) {
                $("#message").text("");
            }
        });
    }
    </script>
    <script src="./assets/js/function.js"></script>
</body>

</html>