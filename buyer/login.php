<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
</head>
<body>
    
    <img src="image/eco logo.png" alt="EcoStore Logo" class="site-logo">
    <!--left content-->
    <div class="left-content">
        <h1 class="main-title">EcoStore</h1>
        <h2 class="sub-title"><i>Recycled Product Marketplace</i></h2>
        
        <br></br>

        
        <p>
            Ecostore is designed for eco-conscious consumers.
            It's a place where you can discover, buy, and support products that are
            sustainable, eco-friendly, and ethically made. From reusable daily essentials
            to upcycled home décor, Ecostore brings together a wide variety of green
            alternatives under one roof.
        </p>

        
    </div>
    <!--login-->
    <div class="container" id="signup">
        <h1 class="form-title">Login</h1>
        <form action="" method="post">
            <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email" placeholder="Email"required>
            </div>
            <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="pswd" id="password" placeholder="Password"required>
           
            </div>
            <div class="remeber-forgor">
            <label><input type="checkbox"name=class1 value="R">Remember me</label>
            
            </div>
            <input type="submit"class="btn" value="login" name="login">
            
        </form>
        <div class="register-link">
            <p>Don't have an account?<a href="register.php">Register</a></p>
        </div>
        
    </div>   
</body>
</html>
<?php
session_start();
$con = mysqli_connect("localhost","root","","ecostore");
if (!$con) {
    die("Connect failed: ".mysqli_connect_error());
}

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $pswd  = $_POST["pswd"];

    // check users table
    $q = "SELECT * FROM users WHERE email='$email' AND password='$pswd'";
    $result = mysqli_query($con, $q);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $role = $row['role'];

        //  the restriction check here
        if ($row['is_restricted'] == 1) {
            echo "<script>alert('Your account has been restricted by admin');</script>";
            exit(); // stop login
        }


        // set session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $row['email'];

        // add to active_users
        $uid = $row['id'];
        $email = $row['email'];
        mysqli_query($con, "INSERT INTO active_users (user_id, email, role, is_active)
                            VALUES ($uid, '$email', '$role', 1)
                            ON DUPLICATE KEY UPDATE is_active=1, login_time=NOW()");

        // redirect
        if ($role == 'Buyer') {
            header("Location: about.php"); exit();
        } elseif ($role == 'Shop') {
            header("Location:../shop/shopheader.php"); exit();
        } elseif ($role == 'Admin') {
            header("Location: /miniproject/shop/shopheader.php");        }

    } else {
        // check admin table
        $q2 = "SELECT * FROM admin WHERE email='$email' AND password='$pswd'";
        $result2 = mysqli_query($con, $q2);
        if (mysqli_num_rows($result2) == 1) {
            $_SESSION['role'] = "Admin";
            header("Location:../admin/adminheader.php"); exit();
        } else {
            echo "<script>alert('Invalid email or password!');</script>";
        }
    }
}
?>
