<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoStore</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: url("../image/bg.jpeg") no-repeat center center fixed;
    background-size: cover;
}
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 50px;
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(8px);
    position: sticky;
    top: 0;
    z-index: 1000;
}
.navbar .logo { font-size: 24px; font-weight: bold; color: #c47474ff; }
.navbar ul { list-style: none; display: flex; margin: 0; padding: 0; }
.navbar ul li { margin-left: 25px; }
.navbar ul li a { text-decoration: none; color: #c47474ff; font-size: 18px; transition: color 0.3s; }
.navbar ul li a:hover { color: #5e3c25ff; }
.welcome-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
}
.welcome-message {
    color: #5e3c25ff;
    font-size: 3em;
    font-weight: bold;
    text-align: center;
    background: rgba(255,255,255,0.95);
    padding: 30px 50px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    transform: scale(0);
    animation: popIn 1s ease-out forwards;
}
@keyframes popIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    70% {
        transform: scale(1.1);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
.page-content {
    max-width: 1200px;
    margin: 30px auto;
    background: rgba(255,255,255,0.9);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
</style>
</head>
<body>
<?php
// Check if session is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Show welcome message only if it's the first time entering shop (not from other pages)
$current_page = basename($_SERVER['PHP_SELF']);
$show_welcome = ($current_page == 'shopprofile.php' || $current_page == 'shopheader.php'); // Adjust with your main shop page name

// Check if user is logged in and should show welcome
if(isset($_SESSION['user_id']) && $show_welcome && !isset($_SESSION['welcome_shown'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecostore";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get user details from database
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT f_name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $first_name = $user['f_name'];
        echo "
        <div class='welcome-overlay'>
            <div class='welcome-message'>Welcome $first_name!</div>
        </div>
        ";
        // Set session to prevent showing again
        $_SESSION['welcome_shown'] = true;
    }
    
    $stmt->close();
    $conn->close();
}
?>

<nav class="navbar">
  <div class="logo">EcoStore</div>
  <ul>
   
    <li><a href="addcategories.php">Add Category</a></li>
    <li><a href="viewcategory.php">View Category</a></li>
    <li><a href="addproduct.php">Add Product</a></li>
    <li><a href="viewproduct.php">View Product</a></li>
    <li><a href="low_stock_notification.php">Stock Alerts</a></li>
    <li><a href="seller_orders.php">Orders Details</a></li>
    <li><a href="shoplogout.php">Logout</a></li>
  </ul>
</nav>

<script>
// Remove welcome overlay after 3 seconds
setTimeout(function() {
    const welcomeOverlay = document.querySelector('.welcome-overlay');
    if (welcomeOverlay) {
        welcomeOverlay.style.opacity = '0';
        welcomeOverlay.style.transition = 'opacity 0.5s ease';
        setTimeout(() => {
            welcomeOverlay.remove();
        }, 500);
    }
}, 3000);
</script>