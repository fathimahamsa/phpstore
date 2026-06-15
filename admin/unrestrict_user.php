<?php
session_start();
$con = mysqli_connect("localhost","root","","ecostore");
if (!$con) die("DB Error");

// Only admin can unrestrict
if (!isset($_SESSION['role']) || $_SESSION['role'] != "Admin") {
    header("Location:  http://localhost/miniproject/miniproject/index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $q = "UPDATE users SET is_restricted=0 WHERE id=$id";
    if (!mysqli_query($con, $q)) {
        die("Update error: ".mysqli_error($con));
    }
}

header("Location:  http://localhost/miniproject/miniproject/admin/admin_users.php");
exit();
?>
