<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location:../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="theme-color" content="#FF5733">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Car Online</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="http://localhost/wheelanddeal/Project/user/assets/css/styles.css">
</head>

<body>
    <?php include('./assets/includes/header.php'); ?>

    <!-- Hero Section -->
    <section class="hero">
        <video class="hero-video" autoplay muted loop>
            <source src="./assets/video/v1.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-content">
            <h1>Drive the Dream</h1>
            <p>Luxury Sports Cars Await</p>
            <a href="./pages/explor_models.php" class="btn">Explore Models</a>
        </div>
    </section>

    <!-- Featured Brands -->
    <section class="brands py-5">
        <div class="container">
            <h2 class="text-center mb-4">Our Top Brands</h2>
            <div class="row justify-content-center">
                <?php
            // Example company names for the logos
$companies = [
    "Aston Martin", "Audi", "BMW", "Bugatti",
    "Chevrolet Corvette", "Ferrari", "Ford", "Lamborghini",
     "Mclaren", "Mercedes", "Porsche", "Toyota",
];

// Limit to 12 companies
$companies = array_slice($companies, 0, 12);

// Display logos in a 2x6 grid format
foreach ($companies as $index => $company) {
    // Assuming the logo filename matches the company name
    $logo = "brand" . ($index + 1) . ".png"; 

    echo "<div class='col-6 col-md-2 text-center my-3'>
            <a href='pages/product_list.php?company=" . urlencode($company) . "'> <!-- Add company data as query parameter -->
                <img src='assets/img/logo/$logo' alt='$company' class='brand-logo'>
            </a>
          </div>";
}
            ?>
            </div>
        </div>
    </section>

    <!-- Top Models -->
    <section class="models py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Top Models</h2>
            <div class="row g-4">
                <?php
// Include database connection
include_once('./assets/includes/db_connection.php');

// Fetch the top 3 models
$sql = "SELECT id, productName, productPrice, productImage1 FROM products WHERE is_top_model = 1 LIMIT 3";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $imageData = base64_encode($row['productImage1']); // Encode binary data to Base64
        $imageSrc = "data:image/jpeg;base64," . $imageData; // Convert to usable format

        echo "<div class='col-md-4'>
                <div class='card'>
                    <img src='$imageSrc' class='card-img-top' alt='" . htmlspecialchars($row['productName']) . "'>
                    <div class='card-body text-center'>
                        <h5 class='card-title'>" . htmlspecialchars($row['productName']) . "</h5>
                        <p class='card-text'>Price: $" . number_format($row['productPrice'], 2) . "</p>
                        <a href='./pages/product_details.php?id={$row['id']}' class='btn btn-primary'>View Details</a>
                    </div>
                </div>
              </div>";
    }
} else {
    echo "<p class='text-center'>No top models available.</p>";
}

mysqli_close($conn);
?>
            </div>
        </div>
    </section>


    <?php include('./assets/includes/footer.php'); ?>
    <a href="https://wa.me/7046085161" target="_blank" class="whatsapp-float">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="Chat on WhatsApp">
    </a>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>