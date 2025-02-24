<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "shopping");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve username from query parameter
$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $favouriteThing = trim($_POST['favouritthing']);
    $newPassword = trim($_POST['newpassword']);

    // Validate inputs
    if (empty($username) || empty($favouriteThing) || empty($newPassword)) {
        $error = "All fields are required.";
    } else {
        // Fetch user from the database
        $stmt = $conn->prepare("SELECT id, favorite_things FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userId = $user['id'];

            // Verify favorite thing
            if ($favouriteThing === $user['favorite_things']) {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->bind_param("si", $hashedPassword, $userId);

                if ($updateStmt->execute()) {
                    $_SESSION['success'] = true;
                    header("Location: login.php?username=" . urlencode($username));
                    exit();
                } else {
                    $error = "Failed to update password. Please try again later.";
                }

                $updateStmt->close();
            } else {
                $error = "Invalid favorite thing.";
            }
        } else {
            $error = "User not found. <a href='signup.php'>Create an account</a>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
        color: #333;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 300px;
        text-align: center;
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    input,
    button {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    button {
        background-color: #333;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #555;
    }

    .error {
        color: red;
        text-align: center;
        margin-bottom: 10px;
    }

    .signup-link {
        margin-top: 15px;
        font-size: 14px;
    }

    .signup-link a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
    }

    .signup-link a:hover {
        color: #555;
    }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Forgot Password</h2>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required>
            <input type="text" name="favouritthing" placeholder="Enter Your Favorite Thing" required>
            <input type="password" name="newpassword" placeholder="Enter New Password" required>
            <button type="submit">Change Password</button>
        </form>
        <div class="signup-link">
            <a href="login.php"><span style="font-size:25px;">&larr;</span> Back</a>
        </div>
    </div>

    <script>
    history.pushState(null, null, location.href);
    window.onpopstate = function() {
        history.go(1);
    };
    </script>
</body>

</html>