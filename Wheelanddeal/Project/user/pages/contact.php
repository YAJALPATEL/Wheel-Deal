<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Sports Car Online</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <?php include('../assets/includes/header.php'); ?>
    <?php include('../assets/includes/db_connection.php'); ?>

    <!-- Contact Us Hero Section -->
    <section class="contact-hero">
        <div class="contact-hero-content text-center">
            <h1>Contact Us</h1>
            <p>We are here to assist you. Feel free to reach out!</p>
        </div>
    </section>

    <!-- Contact Info and Map -->
    <section class="contact-info-map py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Our Contact Information</h2>
                    <p class="mb-3">We’re happy to help. Reach out through any of the following methods:</p>
                    <ul class="list-unstyled">
                        <li><strong>Address:</strong> Green City, Near Chobarifatak, Junagadh, Gujarat, India</li>
                        <li><strong>Email:</strong> support@wheelanddeal.com</li>
                        <li><strong>Phone:</strong> +91 70460 85161</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="map-container">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.95373631550496!3d-37.81627974202152!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577b7676edce60!2sFederation+Square!5e0!3m2!1sen!2sau!4v1611375586526!5m2!1sen!2sau"
                            width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form -->
    <section class="contact-form py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Send Us a Message</h2>
            <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") { 

    // Include the database connection
    include('../assets/includes/db_connection.php');

    // Get POST data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    // Check if the user is logged in and fetch their user ID (if applicable)
    $user_id = null; // Default to null
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id']; // Get user ID from session
    }

    // Validate input
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Adjust the INSERT query based on whether user ID is available
        if ($user_id) {
            $sql = "INSERT INTO contact_us (id, name, email, message) 
                    VALUES ('$user_id', '$name', '$email', '$message')";
        } else {
            $sql = "INSERT INTO contact_us (name, email, message) 
                    VALUES ('$name', '$email', '$message')";
        }

        // Execute the query and check for success
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success text-center'>Message sent successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger text-center'>Please fill in all required fields.</div>";
    }
}
?>

            <form action="" method="POST" class="mx-auto" style="max-width: 600px;">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Your Message"
                        required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
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