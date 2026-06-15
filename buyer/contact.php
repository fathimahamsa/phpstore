<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Insert into database
    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Message sent successfully!'); window.location.href='contact.php';</script>";
    } else {
        echo "<script>alert('Error sending message. Please try again.'); window.location.href='contact.php';</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - EcoStore</title>
    <link rel="stylesheet" href="mstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
</head>
<body>
    
    <!-- Navbar -->
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <img src="image/eco logo.png" width="125px">
            </div>
            <nav>
                <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="product.php">Product</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="wishlist.php">Wishist</a></li>
                <!-- Dropdown menu -->
                <li class="dropdown">
                    <button class="dropdown-toggle">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="cart.php" class="dropdown-item">
                            <i class="fas fa-shopping-cart"></i>
                            Cart 
                        </a>
                        
                        <a href="order_history.php" class="dropdown-item">
                            <i class="fas fa-box"></i>
                            My Order
                        </a>
                        
                        <a href="login.php" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            Login
                        </a>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
            </nav>
        </div>    
    </div>

    <!-- Contact Section -->
    <div class="small-container contact-section">
        <h1>Contact <span style="color:#ff523b;">EcoStore</span></h1>
        <p>We'd love to hear from you! Fill out the form below or use our contact details.</p>

        <div class="row contact-row">
            <!-- Contact Form -->
            <div class="col-2 contact-form">
                <form method="POST" action="">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="text" name="subject" placeholder="Subject" required>
                    <textarea name="message" rows="6" placeholder="Your Message" required></textarea>
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="col-2 contact-info">
                <h3>Our Contact Info</h3>
                <p><i class="fa fa-map-marker"></i> 123 Green Street, Eco City</p>
                <p><i class="fa fa-envelope"></i> support@ecostore.com</p>
                <p><i class="fa fa-phone"></i> +91 98765 43210</p>

                <h3>Follow Us</h3>
                <div class="follow-icons">
                    <p><i class="fab fa-facebook"></i> Facebook</p>
                    <p><i class="fab fa-twitter"></i> Twitter</p>
                    <p><i class="fab fa-instagram"></i> Instagram</p>
                    <p><i class="fab fa-youtube"></i> YouTube</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer"> 
        <div class="container">
            <div class="row">
        
                <div class="footer-col-1">
                    <h3>Download Our App</h3>
                    <p>Download App for Android and iOS mobile phone.</p>
                    <div class="app-logo">
                        <img src="image/app-icons.png">
                    </div>
                </div>
                <div class="footer-col-2">
                    <img src="image/eco logo.png" width="125px">
                    <p>Our goal is to create a future that is eco-friendly and sustainable, where every choice we make supports the health of our planet and promotes a greener lifestyle for generations to come.</p>
                </div>
                <div class="footer-col-3">
                    <h3>Useful Links</h3>
                    <ul>
                        <li>Coupons</li>
                        <li>Blog Post</li>
                        <li>Return Policy</li>
                        <li>Join Affiliate</li>
                    </ul>
                </div>
                <div class="footer-col-4">
                    <h3>Follow Us</h3>
                    <ul>
                        <li>Facebook</li>
                        <li>Twitter</li>
                        <li>Instagram</li>
                        <li>YouTube</li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="copyright">Copyright-2024 ACT2003</p>
        </div>
    </div>

    <script>
        // Dropdown menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            
            if (dropdownToggle && dropdownMenu) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    dropdownMenu.classList.remove('show');
                });
                
                // Prevent dropdown from closing when clicking inside it
                dropdownMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    </script>
</body>
</html>