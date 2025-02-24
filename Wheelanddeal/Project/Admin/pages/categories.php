<div class="section">
    <h3>Categories Management</h3>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Category Description</th>
                <th>Creation Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch categories from the database
            include_once('../assets/includes/db_connection.php');
            $sql = "SELECT * FROM categories ORDER BY creationDate DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['categoryName']}</td>
                        <td>{$row['categoryDescription']}</td>
                        <td>{$row['creationDate']}</td>
                        <td>
                            <a href='pages/edit_categories.php?id={$row['id']}' class='button'>Edit</a>
                            <a href='./assets/php/delete_categories.php?id={$row['id']}' class='button' onclick='return confirm(\"Are you sure you want to delete this category?\")'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No categories found.</td></tr>";
            }

            mysqli_close($conn);
            ?>
        </tbody>
    </table>

    <!-- Add Category Button -->
    <div class="add-category">
        <a href="./pages/add_categories.php" target="_blank" class="btn-primary">Add New Category</a>
    </div>
</div>