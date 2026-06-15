<?php
session_start();
$con = mysqli_connect("localhost","root","","ecostore");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "Admin") {
    header("Location: login.php"); exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($con, "UPDATE users SET is_restricted=1 WHERE id=$id");
}

header("Location: http://localhost/miniproject/miniproject/admin/admin_users.php");
exit();
?>
