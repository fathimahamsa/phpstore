<?php
session_start();
include 'db_connect.php';

// check login
if (!isset($_SESSION['user_id'])) {
    die("You must log in first.");
}

$userid = $_SESSION['user_id'];

// get product id from URL
if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$product_id = $_GET['id'];

// fetch product to get image
$sql = "SELECT * FROM product WHERE product_id='$product_id' AND user_id='$userid'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) != 1) {
    die("Product not found or you don't have permission.");
}

$row = mysqli_fetch_assoc($result);

// delete image file if exists
if (!empty($row['image']) && file_exists("uploads/".$row['image'])) {
    unlink("uploads/".$row['image']);
}

// delete product from database
$delete = "DELETE FROM product WHERE product_id='$product_id' AND user_id='$userid'";
if ($con->query($delete)) {
    header("Location: viewproduct.php");
    exit();
} else {
    echo "Error deleting product: " . $con->error;
}
?>
