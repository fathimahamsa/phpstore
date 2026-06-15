<?php
session_start();
$con = mysqli_connect("localhost","root","","ecostore");
if (!$con) { die("DB Error"); }

// Only allow admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Admin") {
    header("Location:  http://localhost/miniproject/buyer/login.php"); exit();
}

$result = mysqli_query($con, "SELECT id, email, role FROM users");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Active Users</title>
</head>
<body>
    <h2>Currently Logged In Users</h2>
   <table border="1" cellpadding="8">
    <tr>
       <th>SI No</th> <th>User ID</th><th>Email</th><th>Role</th>
    </tr>
     
    <?php
    $count=1;
     while($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
         <td><?php echo $count++;?></td> 
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['role']; ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
