<div class="section">
    <h3>User Messages</h3>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Received Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch messages from the contact_us table
            include_once('../assets/includes/db_connection.php');
            $sql = "SELECT * FROM contact_us ORDER BY 	submitted_at DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['message']}</td>
                        <td>{$row['submitted_at']}</td>
                    </tr>";
                }
            } else {
                echo "<tr>
                    <td colspan='5'>No messages found.</td>
                </tr>";
            }

            mysqli_close($conn);
            ?>
        </tbody>
    </table>
</div>

<script>
// JavaScript to handle "Edit" button click
document.addEventListener("DOMContentLoaded", () => {
    const editButtons = document.querySelectorAll(".edit-button");

    editButtons.forEach(button => {
        button.addEventListener("click", (event) => {
            const categoryId = event.target.getAttribute("data-id");

            if (categoryId) {
                const editUrl = `edit_categories.php?id=${categoryId}`;
                window.open(editUrl, "_blank"); // Open in a new tab
            } else {
                alert("Invalid category ID.");
            }
        });
    });
});
</script>