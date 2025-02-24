<?php
// Include the database connection
include('../assets/includes/db_connection.php');

// Initialize response
$response = ['status' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $categoryName = trim($_POST['category_name']);
    $categoryDescription = trim($_POST['category_description']);

    // Validate input
    if (!empty($categoryName) && !empty($categoryDescription)) {
        // Prepare and execute the SQL statement
        $sql = "INSERT INTO categories (categoryName, categoryDescription) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ss", $categoryName, $categoryDescription);
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Category added successfully';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to add category';
            }
            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error preparing the statement';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Both fields are required';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
    .success {
        color: green;
    }

    .error {
        color: red;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Add New Category</h2>

        <!-- Display status message -->
        <?php if (!empty($response['message'])): ?>
        <div id="status-message" class="<?= htmlspecialchars($response['status']) ?>">
            <?= htmlspecialchars($response['message']) ?>
        </div>
        <?php endif; ?>

        <form action="add_categories.php" method="POST">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>

            <div class="form-group">
                <label for="category_description">Category Description:</label>
                <textarea id="category_description" name="category_description" required></textarea>
            </div>

            <button type="submit" class="btn">Add Category</button>
        </form>
    </div>
</body>

</html>