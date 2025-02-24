<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../assets/includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$default_image = '../assets/img/profileimg/default-profile.jpg';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['avatar'])) {
        $avatar_path = '../assets/img/profileimg/' . basename($_POST['avatar']);
        $avatar_data = file_get_contents($avatar_path);

        $sql = "UPDATE users SET profileimg = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $avatar_data, $user_id);

        if ($stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            header("Location: profile.php");
            exit();
        }
    }
}

// Fetch user data
$sql = "SELECT username, email, profileimg, favorite_things FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$username = $user['username'];
$email = $user['email'];
$favorite_things = $user['favorite_things'];

if (!empty($user['profileimg'])) {
    $profile_image = 'data:image/jpeg;base64,' . base64_encode($user['profileimg']);
} else {
    $profile_image = $default_image;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['favorite_things'])) {
    // Trim inputs to prevent unnecessary spaces
    $username = trim($_POST['username']);
    $favoriteThings = trim($_POST['favorite_things']);

    // Check for empty fields
    if (empty($username) || empty($favoriteThings)) {
        $error = "Username and Favorite Things are required.";
    } 
    // Restrict username format
    elseif (!preg_match("/^[a-zA-Z0-9_-]{3,16}$/", $username)) {
        $error = "Username must be 3-16 characters long and can only contain letters, numbers, underscores, and dashes.";
    } 
    // Block certain usernames
    elseif (in_array(strtolower($username), ['admin', 'administrator', 'root', 'superuser'])) {
        $error = "The username '$username' is not allowed. Please choose a different username.";
    } 
    else {
        // Check if username already exists (excluding the current user)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE LOWER(username) = LOWER(?) AND id != ?");
        $stmt->bind_param("si", $username, $user_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $error = "The username '$username' is already taken. Please choose another.";
        }
    }

    if (empty($error)) {
        // Update username and favorite things
        $stmt = $conn->prepare("UPDATE users SET username = ?, favorite_things = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $favoriteThings, $user_id);

        if ($stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            $error = "Update failed. Please try again.";
        }
    }
    else{
        $username=$user['username'];
    }
}
$user_sql = "SELECT shippingAddress, shippingCity, shippingState, shippingPincode FROM users WHERE id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Handle address update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_address'])) {
    $newAddress = trim($_POST['shipping_address']);
    $newCity = trim($_POST['shipping_city']);
    $newState = trim($_POST['shipping_state']);
    $newPincode = trim($_POST['shipping_pincode']);

    // Validate input fields
    if (empty($newAddress) || empty($newCity) || empty($newState) || empty($newPincode)) {
        $error_message = "All fields are required.";
    } else {
        // Update address in the database
        $update_sql = "UPDATE users SET shippingAddress = ?, shippingCity = ?, shippingState = ?, shippingPincode = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssi", $newAddress, $newCity, $newState, $newPincode, $user_id);

        if ($update_stmt->execute()) {
            // Redirect to the order page with the product ID
            header("Location: profile.php");
            exit(); // Stop further execution
        } else {
            $error_message = "Error updating address. Please try again.";
        }

        $update_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/wheelanddeal/Project/user/assets/css/styles.css">
</head>

<body>
    <header class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Logo and Text in .navbar-brand -->
            <a class="navbar-brand d-flex align-items-center"
                href="http://localhost/wheelanddeal/Project/user/index.php">
                <img src="http://localhost/wheelanddeal/Project/user/assets/img/site_logo.png" alt="Logo"
                    class="navbar-logo me-2">
                <span class="navbar-text">Wheel And Deal</span>
            </a>
        </div>
    </header>

    <div class="container">
        <h3>My Profile</h3>

        <!-- Display Error Messages -->
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Profile Info -->
        <div class="row">
            <div class="col-md-3">
                <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="img-fluid rounded-circle"
                    style="width: 150px; height: 150px;">

                <!-- Change Profile Picture Link -->
                <p class="text-center mt-2">
                    <a href="#" id="changeProfilePicLink">Change Profile Picture</a>
                </p>
            </div>
            <div class="col-md-9">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <?php if (!empty($favorite_things)): ?>
                <p><strong>Favorite Thing:</strong> <?php echo htmlspecialchars($favorite_things); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Update Profile Form -->
        <h4 class="mt-9">Update Profile Information</h4>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="favorite_things">Favorite Thing:</label>
                <input type="text" class="form-control" id="favorite_things" name="favorite_things"
                    value="<?php echo htmlspecialchars($favorite_things); ?>"
                    placeholder="Comma-separated (e.g., Supercar, Hypercar, Classic)">
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
        </form>
        <div class="container mt-5">
            <h2 class="text-center">Update Your Shipping Address</h2>

            <?php if (isset($error_message)) : ?>
            <p class="text-danger text-center"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="profile.php">
                <label for="shipping_address">Address:</label>
                <textarea id="shipping_address" name="shipping_address" class="form-control mb-2"
                    required><?php echo htmlspecialchars($user_data['shippingAddress'] ?? ''); ?></textarea>

                <label for="shipping_city">City/Village:</label>
                <input id="shipping_city" type="text" name="shipping_city" class="form-control mb-2" required
                    value="<?php echo htmlspecialchars($user_data['shippingCity'] ?? ''); ?>">

                <label for="shipping_state">State:</label>
                <input id="shipping_state" type="text" name="shipping_state" class="form-control mb-2" required
                    value="<?php echo htmlspecialchars($user_data['shippingState'] ?? ''); ?>">

                <label for="shipping_pincode">Pincode:</label>
                <input id="shipping_pincode" type="text" name="shipping_pincode" class="form-control mb-2" required
                    value="<?php echo htmlspecialchars($user_data['shippingPincode'] ?? ''); ?>">

                <button type="submit" name="update_address" class="btn btn-primary">Update Address</button>
            </form>
        </div>
        <div class="logout btn">
            <a href="../../logout.php">Logout</a>
        </div>
        <!-- Blurred Background Overlay -->
        <div id="overlay"></div>

        <!-- Change Profile Picture Card -->
        <div class="card" id="changeProfilePicCard">
            <div class="card-body">
                <h5 class="card-title">Change Profile Picture</h5>

                <!-- Predefined Avatars -->
                <div class="avatar-grid">
                    <?php
            $avatars = ['a1.png', 'a2.jpeg', 'a3.jpg', 'a4.jpg', 'a5.jpg', 'a6.jpg'];
            foreach ($avatars as $avatar): ?>
                    <img src="../assets/img/profileimg/<?php echo $avatar; ?>" alt="Avatar"
                        class="avatar-img rounded-circle" data-avatar="<?php echo $avatar; ?>">
                    <?php endforeach; ?>
                </div>

                <!-- Upload Custom Image -->
                <form action="profile.php" method="POST" enctype="multipart/form-data" id="avatarForm">
                    <!-- Hidden input to store selected avatar -->
                    <input type="hidden" id="selectedAvatar" name="avatar" value="">
                    <button type="submit" id="saveAvatarBtn" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
    <?php include('../assets/includes/footer.php');?>
    <script>
    document.getElementById('changeProfilePicLink').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('overlay').style.display = 'flex';
        document.getElementById('changeProfilePicCard').style.display = 'block';
    });

    document.getElementById('overlay').addEventListener('click', function() {
        this.style.display = 'none';
        document.getElementById('changeProfilePicCard').style.display = 'none';
    });

    // Handle Avatar Selection
    document.querySelectorAll('.avatar-img').forEach(avatar => {
        avatar.addEventListener('click', function() {
            document.querySelectorAll('.avatar-img').forEach(img => img.classList.remove(
                'selected-avatar'));
            this.classList.add('selected-avatar');
            document.getElementById('selectedAvatar').value = this.getAttribute('data-avatar');
        });
    });

    // Handle Avatar Form Submission (Without Fetch)
    document.getElementById('avatarForm').addEventListener('submit', function(event) {
        if (document.getElementById('selectedAvatar').value === "") {
            alert("Please select an avatar.");
            event.preventDefault(); // Prevent form submission if no avatar is selected
        }
    });

    // Show success or error message after redirection
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            alert("Profile updated successfully!");
        } else if (urlParams.has('error')) {
            alert("Error: " + urlParams.get('error').replace("_", " "));
        }
    };
    </script>

    <style>
    /* Overlay for Blur Effect */
    #overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        z-index: 1999;
    }

    /* Change Profile Pic Card */
    #changeProfilePicCard {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 400px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        z-index: 2000;
    }

    /* Avatar Grid */
    .avatar-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        justify-items: center;
    }

    /* Avatar Image */
    .avatar-img {
        width: 80px;
        height: 80px;
        cursor: pointer;
        border-radius: 50%;
        border: 2px solid transparent;
        transition: border-color 0.3s ease-in-out;
    }

    .avatar-img:hover {
        border-color: #007bff;
    }

    .selected-avatar {
        border-color: green !important;
    }
    </style>
</body>

</html>