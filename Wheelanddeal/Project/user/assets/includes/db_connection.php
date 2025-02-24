<?php
// db_connection.php
$host = 'localhost'; // Change to your database host
$user = 'root'; // Change to your database username
$password = ''; // Change to your database password
$dbname = 'shopping'; // Change to your database name

// Create a connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>