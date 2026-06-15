<?php
session_start();
include 'db_connect.php';

// check login
if (!isset($_SESSION['user_id'])) {
    die("You must log in first.");
}

$userid = $_SESSION['user_id'];

// get category id from URL
if (!isset($_GET['id'])) {
    die("Category ID missing.");
}

$cat_id = $_GET['id'];

// fetch category to get image filename
$sql = "SELECT * FROM category WHERE cat_id='$cat_id' AND user_id='$userid'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) != 1) {
    die("Category not found or you don't have permission.");
}

$row = mysqli_fetch_assoc($result);

// delete image file if exists
if (!empty($row['image']) && file_exists("uploads/".$row['image'])) {
    unlink("uploads/".$row['image']);
}

// delete category from database
$delete = "DELETE FROM category WHERE cat_id='$cat_id' AND user_id='$userid'";
if ($con->query($delete)) {
    // redirect back to view categories
    header("Location: viewcategory.php");
    exit();
} else {
    echo "Error deleting category: " . $con->error;
}
?>
