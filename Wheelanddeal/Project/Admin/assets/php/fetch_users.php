<?php
// Use an absolute path for the database connection
include(__DIR__ . '/../includes/db_connection.php');

// Fetch users from the database securely
$sql = "SELECT id, username, email, created_at, favorite_things FROM users";
$stmt = $conn->prepare($sql); // Prepare the SQL query

if ($stmt) {
    $stmt->execute(); // Execute the query
    $result = $stmt->get_result(); // Get the result set

    if ($result->num_rows > 0) {
        // Fetch user data into an array
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        // Return the data as a JSON response
        echo json_encode([
            'status' => 'success',
            'data' => $users
        ]);
    } else {
        // No users found
        echo json_encode([
            'status' => 'error',
            'message' => 'No users found'
        ]);
    }

    $stmt->close(); // Close the statement
} else {
    // Query preparation failed
    echo json_encode([
        'status' => 'error',
        'message' => 'Error executing query: ' . $conn->error
    ]);
}

// Close the connection
$conn->close();
?>