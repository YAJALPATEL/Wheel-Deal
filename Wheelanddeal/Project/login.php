<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "shopping");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Success message handling
$success = isset($_SESSION['success']) ? "Password changed successfully." : '';
unset($_SESSION['success']); // Clear the success message after displaying it

// Retrieve username from query parameter
$username = isset($_GET['username']) ? htmlspecialchars($_GET['username']) : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        // Admin login check
        if ($username === "admin") {
            if ($password === "123") {
                $_SESSION['admin_logged_in'] = true; // Set session for admin
                $_SESSION['admin_username'] = $username; // Store admin username
                header("Location: ./Admin/index.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            // Fetch user from the database
            $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables for regular user
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_username'] = $username; // Store username
                    header("Location: ./user/index.php");
                    exit;
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "User not found. <a href='signup.php'>Create account</a>";
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

    .success {
        color: green;
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

    .forgot-password {
        margin-top: 10px;
    }

    .forgot-password a {
        color: #333;
        text-decoration: none;
        font-size: 14px;
    }

    .forgot-password a:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="signup-link">
            Don't have an account? <a href="signup.php">Signup here</a>
        </div>
        <div class="forgot-password">
            <a href="forgot_password.php">Forgot Password?</a>
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