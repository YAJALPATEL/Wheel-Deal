<?php
// Start the session
session_start();

// Destroy all session data to log the user/admin out
session_unset(); // Removes all session variables
session_destroy(); // Destroys the session itself

// Redirect the user to the login page or homepage
header("Location: login.php"); // Adjust the URL as needed (login or homepage)
exit();
?>