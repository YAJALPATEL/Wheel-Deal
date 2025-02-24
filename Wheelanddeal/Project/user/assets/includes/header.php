<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . "/../includes/db_connection.php"); // Adjust as needed

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch the profile image from the database
    $stmt = $conn->prepare("SELECT profileimg FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($profileimg);
    $stmt->fetch();
    $stmt->close();

    // Check if an image exists in the database
    $profileImgSrc = !empty($profileimg) ? "data:image/jpeg;base64," . base64_encode($profileimg) : "./assets/img/default-profile.jpg";
} else {
    $profileImgSrc = "./assets/img/default-profile.jpg"; // Default profile image if user is not logged in
}

// Get the cart count
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// Fetch categories from the database
$categories = [];
$category_sql = "SELECT id, categoryName FROM categories";
$category_stmt = $conn->prepare($category_sql);
$category_stmt->execute();
$result = $category_stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$category_stmt->close();
?>

<link rel="stylesheet" href="http://localhost/wheelanddeal/Project/user/assets/css/styles.css">
<header class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Logo and Text in .navbar-brand -->
        <a class="navbar-brand d-flex align-items-center" href="http://localhost/wheelanddeal/Project/user/index.php">
            <img src="http://localhost/wheelanddeal/Project/user/assets/img/site_logo.png" alt="Logo"
                class="navbar-logo me-2">
            <span class="navbar-text">Wheel And Deal</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/wheelanddeal/Project/user/index.php">Home</a>
                </li>

                <!-- Categories Dropdown -->
                <li class="nav-item dropdown">
                    <!-- Changed href to # for proper dropdown toggle -->
                    <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                        <?php foreach ($categories as $category) : ?>
                        <li>
                            <a class="dropdown-item"
                                href="http://localhost/wheelanddeal/Project/user/pages/category.php?id=<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['categoryName']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/wheelanddeal/Project/user/pages/about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost/wheelanddeal/Project/user/pages/contact.php">Contact</a>
                </li>

                <!-- Wishlist -->
                <li class="nav-item">
                    <a class="nav-link position-relative"
                        href="http://localhost/wheelanddeal/Project/user/pages/my_orders.php">
                        Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link position-relative"
                        href="http://localhost/wheelanddeal/Project/user/pages/wishlist_page.php">
                        Wishlist
                    </a>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $profileImgSrc; ?>" alt="Profile" class="profile-img">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item"
                                href="http://localhost/wheelanddeal/Project/user/pages/profile.php">My Profile</a></li>
                        <li><a class="dropdown-item" href="http://localhost/wheelanddeal/Project/logout.php">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Bootstrap JavaScript (Include at the end of the body) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.dropdown-toggle').forEach(function(dropdown) {
        dropdown.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior

            // Toggle the visibility of the dropdown menu
            let dropdownMenu = this.nextElementSibling;

            if (dropdownMenu.classList.contains("show")) {
                dropdownMenu.classList.remove("show");
                dropdownMenu.style.display = "none";
            } else {
                // Hide all other open dropdowns first
                document.querySelectorAll(".dropdown-menu").forEach(menu => {
                    menu.classList.remove("show");
                    menu.style.display = "none";
                });

                dropdownMenu.classList.add("show");
                dropdownMenu.style.display = "block";
            }
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener("click", function(event) {
        if (!event.target.closest(".dropdown")) {
            document.querySelectorAll(".dropdown-menu").forEach(menu => {
                menu.classList.remove("show");
                menu.style.display = "none";
            });
        }
    });
});
</script>