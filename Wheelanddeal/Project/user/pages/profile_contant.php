<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection file
require_once __DIR__ . '/../assets/includes/db_connection.php';

$user_id = $_SESSION['user_id'];
$default_image = '../assets/img/profileimg/default-profile.jpg';

// Handle profile picture update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['avatar'])) {
        // User selected a predefined avatar
        $avatar_path = '../assets/img/profileimg/' . basename($_POST['avatar']);
        $avatar_data = file_get_contents($avatar_path);

        $sql = "UPDATE users SET profileimg = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $avatar_data, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
        }
        exit();
    }

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        $file_type = $_FILES['profile_image']['type'];
        $file_size = $_FILES['profile_image']['size'];
        $tmp_name = $_FILES['profile_image']['tmp_name'];

        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
            $image_data = file_get_contents($tmp_name); // Convert file to binary
            
            $sql = "UPDATE users SET profileimg = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $image_data, $user_id);

            if ($stmt->execute()) {
                header("Location: profile.php"); // Reload profile page
                exit();
            } else {
                echo "Database error: " . $stmt->error;
            }
        } else {
            echo "Invalid file type or size exceeds 2MB.";
        }
    }
}

// Fetch user data
$sql = "SELECT username, email, profileimg, favorite_things FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $username = $user['username'];
    $email = $user['email'];
    $favorite_things = $user['favorite_things'];
    
    // Convert binary image data to base64 for display
    if (!empty($user['profileimg'])) {
        $profile_image = 'data:image/jpeg;base64,' . base64_encode($user['profileimg']);
    } else {
        $profile_image = $default_image;
    }
    
    $stmt->close();
} else {
    die("Error preparing SQL statement: " . $conn->error);
}
?>

<!-- HTML and JavaScript remain the same as in your original code -->

<div class="container">
    <h3>My Profile</h3>

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
            <p><strong>Favorite Cars:</strong> <?php echo htmlspecialchars($favorite_things); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Update Profile Form -->
    <h4 class="mt-4">Update Profile Information</h4>
    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username"
                value="<?php echo htmlspecialchars($username); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
            <label for="favorite_things">Favorite Car Types:</label>
            <input type="text" class="form-control" id="favorite_things" name="favorite_things"
                value="<?php echo htmlspecialchars($favorite_things); ?>"
                placeholder="Comma-separated (e.g., Supercar, Hypercar, Classic)">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Profile</button>
    </form>

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
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_image">Or upload your own picture:</label>
                    <input type="file" class="form-control-file" id="profile_image" name="profile_image"
                        accept="image/jpeg, image/png">
                    <small class="form-text text-muted">Max size: 2MB (JPEG or PNG only)</small>
                </div>
                <button type="submit" id="saveAvatarBtn" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('changeProfilePicLink').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('overlay').style.display = 'flex';
    document.getElementById('changeProfilePicCard').style.display = 'block';
});

// Close modal when clicking outside
document.getElementById('overlay').addEventListener('click', function() {
    this.style.display = 'none';
    document.getElementById('changeProfilePicCard').style.display = 'none';
});

// Handle Avatar Selection
document.querySelectorAll('.avatar-img').forEach(avatar => {
    avatar.addEventListener('click', function() {
        // Remove the 'selected-avatar' class from all avatars
        document.querySelectorAll('.avatar-img').forEach(img => img.classList.remove(
            'selected-avatar'));

        // Add the 'selected-avatar' class to the clicked avatar
        this.classList.add('selected-avatar');

        const selectedAvatar = this.getAttribute('data-avatar');

        let formData = new FormData();
        formData.append("avatar", selectedAvatar);

        fetch("profile.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Profile picture updated successfully!");
                    location.reload(); // Reload to show new profile pic
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An unexpected error occurred.");
            });
    });
});
</script>

<!-- CSS Styling -->
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