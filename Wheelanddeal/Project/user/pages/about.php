<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Sports Car Online</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <?php include('../assets/includes/header.php'); ?>

    <!-- Hero Section -->
    <section class="about-hero bg-dark text-white text-center py-5">
        <div class="container">
            <h1 class="display-4">About Us</h1>
            <p class="lead">Discover our journey and passion for luxury sports cars</p>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="our-story py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="../assets/img/our_story.jpg" alt="Our Story" class="img-fluid rounded">
                </div>
                <div class="col-md-6">
                    <h2>Our Story</h2>
                    <p>Founded with a passion for speed and luxury, Sports Car Online brings the finest collection of
                        luxury sports cars to enthusiasts worldwide. With years of experience in the automotive
                        industry,
                        our team is dedicated to providing exceptional service and the ultimate driving experience.</p>
                    <p>We believe in the thrill of the drive and the beauty of engineering excellence, and we strive to
                        connect our customers with the cars of their dreams.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section class="our-mission bg-light py-5">
        <div class="container text-center">
            <h2>Our Mission</h2>
            <p class="lead">To revolutionize the way luxury sports cars are bought and sold online.</p>
            <p>We aim to provide a seamless and transparent online car-buying experience, offering a curated selection
                of the world's finest sports cars while maintaining a commitment to quality and customer satisfaction.
            </p>
        </div>
    </section>

    <!-- Meet the Team Section -->
    <section class="team py-5">
        <div class="container">
            <h2 class="text-center mb-4">Meet the Team</h2>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <img src="../assets/img/team/team1.jpg" alt="Team Member" class="img-fluid rounded-circle mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;">
                    <h5>Yajal Patel</h5>
                    <p>Founder & CEO</p>
                </div>
                <div class="col-md-4 text-center">
                    <img src="../assets/img/team/team2.jpg" alt="Team Member" class="img-fluid rounded-circle mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;">
                    <h5>Bhargav Vadher</h5>
                    <p>Chief Marketing Officer</p>
                </div>
                <div class="col-md-4 text-center">
                    <img src="../assets/img/team/team3.jpg" alt="Team Member" class="img-fluid rounded-circle mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;">
                    <h5>Meet Vadher</h5>
                    <p>Head of Sales</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta bg-dark text-white text-center py-5">
        <div class="container">
            <h2>Ready to Drive the Dream?</h2>
            <p class="lead">Explore our collection and find your perfect ride today.</p>
            <a href="./explor_models.php" class="btn btn-light btn-lg">Explore Models</a>
        </div>
    </section>

    <?php include('../assets/includes/footer.php'); ?>
    <a href="https://wa.me/7046085161" target="_blank" class="whatsapp-float">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="Chat on WhatsApp">
    </a>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>