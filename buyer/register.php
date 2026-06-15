<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
     
</head>
<body>
     <img src="image/eco logo.png" alt="EcoStore Logo" class="site-logo">
    <div class="left-content">
        <h1 class="main-title">EcoStore</h1>
        <h2 class="sub-title"><i>Recycled Product Marketplace</i></h2>
        
        <br></br>
</div>
    <form action="#"method="post">
    <div class="container">
        <h1 class="form-title">Registration Form</h1>
        <div class="role-selectio">
           <label><input type="radio" name="choose" value="shop">SHOP</label>
           <label><input type="radio" name="choose" value="cus" required>BUYER</label>
        </div>
        <div class="input-group">
            
            First Name:<input type="text" name="fname" placeholder="Enter your first Name" required>
        </div>
         <div class="input-group">
           
           Last Name:<input type="text" name="lname" placeholder="Enter your Last Name" required>
        </div>
        <div class="input-group">
           
            Email:<input type="email" name="email" id="email" placeholder="Email" required minlength="10" maxlength="50">
        </div>
            <div class="input-group">
            
            Password:<input type="password" name="pswd" id="password" placeholder="Password"required>
        </div>
        <div class="input-group">
            Contact:<input type="text" name="contact" placeholder="contact details"required>
        </div>
        <div class="input-group">
            Gender:<input type="text" name="gender" placeholder="gender details">
        </div>
    <input type="submit"class="btn" value="Submit" name="submit">
    </div>
    </form>
</body>
</html>
<?php

// Connect to database
$con = mysqli_connect("localhost", "root", "", "ecostore");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if (isset($_POST["submit"])) {
    $role = $_POST["choose"];
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $email = $_POST["email"];
    $pswd = $_POST["pswd"];
    $contact = $_POST["contact"];
    $gender = $_POST["gender"];

    // Insert data into users table
    $q = "INSERT INTO users (f_name, l_name, email,password,role, contact, gender) 
      VALUES ('$fname', '$lname', '$email', '$pswd','$role','$contact', '$gender')";

    $res = mysqli_query($con, $q);

   if ($res) {
    echo "<script>
            alert('Registration Successful!');
            window.location.href = 'buyer/login.php';
          </script>";
    }else {
        echo "Error: " . mysqli_error($con);
    }
}



?>