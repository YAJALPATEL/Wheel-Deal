<?php
// Initialize variables
$username = $email = $favoriteThings = "";
$password1 = $password2 = "";
$termsChecked = false;

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection to "shopping"
    $conn = new mysqli("localhost", "root", "", "shopping");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $favoriteThings = trim($_POST['favorite_things']);
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $termsChecked = isset($_POST['terms']);

    // Validate inputs
    if (empty($username) || empty($email) || empty($favoriteThings) || empty($password1) || empty($password2)) {
        $error = "All fields are required.";
    } elseif (!preg_match("/^[a-zA-Z0-9_-]{3,16}$/", $username)) {
        $error = "Username must be 3-16 characters long and can only contain letters, numbers, underscores, and dashes.";
    } elseif (strtolower($username) === 'admin') {
        $error = "The username 'admin' is not allowed. Please choose a different username.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password1 !== $password2) {
        $error = "Passwords do not match.";
    } elseif (!$termsChecked) {
        $error = "You must accept the terms and conditions.";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!$%@^&])[A-Za-z\d!$%@^&]{8,}$/", $password1)) {
        $error = "Password must be at least 8 characters long, include at least one letter, one number, and one special character.";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE LOWER(username) = LOWER(?)");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $error = "The username '$username' is already taken. Please choose another.";
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE LOWER(email) = LOWER(?)");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($emailCount);
            $stmt->fetch();
            $stmt->close();
            if ($emailCount > 0) {
                $error = "The email '$email' is already associated with another account.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password1, PASSWORD_BCRYPT);

               // Insert into the database
                $defaultImage = file_get_contents('./user/assets/img/profileimg/default-profile.jpg');
                if ($defaultImage === false) {
                    die('Error: Unable to load default profile image.');
                }

                $stmt = $conn->prepare("INSERT INTO users (username, email, password, favorite_things, profileimg) VALUES (?, ?, ?, ?, ?)");

                // Initialize $null for BLOB and bind as 'b' type
                $null = null;
                $stmt->bind_param("ssssb", $username, $email, $hashedPassword, $favoriteThings, $null);

                // Send the image data (parameter index 4 for the 5th placeholder)
                $stmt->send_long_data(4, $defaultImage);

                if ($stmt->execute()) {
                    header("Location: login.php?username=" . urlencode($username));
                    exit();
                } else {
                    $error = "Error: " . $stmt->error;
                }

$stmt->close();
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
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

    .terms {
        font-size: 14px;
        color: #555;
        display: flex;
        align-items: center;
    }

    .terms label {
        margin-left: 5px;
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
        font-size: 12px;
    }

    .success {
        color: green;
        text-align: center;
        margin-bottom: 10px;
    }

    .login-link {
        margin-top: 15px;
        font-size: 14px;
    }

    .login-link a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
    }

    .login-link a:hover {
        color: #555;
    }

    small {
        font-size: 12px;
        color: #777;
    }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Signup</h2>
        <img src="./user/assets/img/profileimg/default-profile.jpg" alt="NULL">
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>"
                required>
            <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>"
                required>
            <input type="text" name="favorite_things" placeholder="Favorite Things"
                value="<?php echo htmlspecialchars($favoriteThings); ?>" required>
            <input type="password" name="password1" placeholder="Create Password" required>
            <small>Password must be at least 8 characters long, include one letter, one number, and one special
                character.</small>
            <input type="password" name="password2" placeholder="Confirm Password" required>
            <div class="terms">
                <input type="checkbox" name="terms" id="terms" <?php if ($termsChecked) echo "checked"; ?>>
                <label for="terms">I accept the <a href="terms_and_conditions.html" target="_blank">terms and
                        conditions</a>.</label>
            </div>
            <button type="submit">Signup</button>
        </form>
        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>

</html>