<?php
// Include the database connection
include('../assets/includes/db_connection.php');

// Initialize response message
$response = [
    'status' => '',
    'message' => ''
];

// Handle form submission for updating the category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && is_numeric($_POST['id']) &&
        !empty($_POST['category_name']) && !empty($_POST['category_description'])) {

        $categoryId = intval($_POST['id']);
        $categoryName = trim($_POST['category_name']);
        $categoryDescription = trim($_POST['category_description']);

        // Update the category in the database
        $sql = "UPDATE categories SET categoryName = ?, categoryDescription = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $categoryName, $categoryDescription, $categoryId);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Category updated successfully.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to update the category. Please try again.';
        }

        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'All fields are required.';
    }
}

// Validate category ID for GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $categoryId = intval($_GET['id']);

    // Fetch the category details
    $sql = "SELECT * FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc(); // Fetch category data
    } else {
        $response['status'] = 'error';
        $response['message'] = 'No category found for the given ID.';
    }

    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request. No category to edit. Please check the category ID.';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
    .success {
        color: green;
    }

    .error {
        color: red;
    }
    </style>
    <script>
    // JavaScript to handle auto-closing the page
    function checkStatus(status) {
        if (status === 'success') {
            alert('Category updated successfully. Closing page...');
            window.close(); // Automatically close the page
        }
    }
    </script>
</head>

<body onload="checkStatus('<?= $response['status'] ?>')">
    <div class="container">
        <h2>Edit Category</h2>

        <!-- Display response message -->
        <?php if (!empty($response['message'])): ?>
        <div id="status-message" class="<?= htmlspecialchars($response['status']) ?>">
            <?= htmlspecialchars($response['message']) ?>
        </div>
        <?php endif; ?>

        <!-- Show the form if the category data is available -->
        <?php if (isset($category)): ?>
        <form action="edit_categories.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($categoryId) ?>">

            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name"
                    value="<?= htmlspecialchars($category['categoryName']) ?>" required>
            </div>

            <div class="form-group">
                <label for="category_description">Category Description:</label>
                <textarea id="category_description" name="category_description"
                    required><?= htmlspecialchars($category['categoryDescription']) ?></textarea>
            </div>

            <button type="submit" class="btn">Update Category</button>
        </form>
        <?php else: ?>
        <p>No category to edit. Please check the category ID.</p>
        <?php endif; ?>
    </div>
</body>

</html>