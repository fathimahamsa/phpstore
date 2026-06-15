<?php 
session_start();
include 'db_connect.php'; 
include 'shopheader.php';
// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must log in first.");
}
?>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $userid=$_SESSION['user_id']; //logged in users

    // Handle Image Upload
    $image = $_FILES['image']['name'];
    $target = "C:\\xampp\\htdocs\\miniproject\\shop\\uploads\\" . basename($image);


    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO category (name, image, description, user_id)
                VALUES ('$name', '$image', '$description','$userid')";
        if ($con->query($sql)) {
            // Redirect immediately to prevent double submission
        header("Location: viewcategory.php");
        exit(); 
        } 
        else 
        {
            echo "<p style='color:red;'>Error: " . $con->error . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Failed to upload image.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <style>
/* Form overlay styling */
.page-content {
    max-width: 500px;
    margin: 50px auto; /* centers horizontally */
    background: rgba(255,255,255,0.95);
    padding: 30px 25px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

.page-content h2 {
    text-align: center;
    color: #c47474ff;
    margin-bottom: 25px;
}

.page-content label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.page-content input[type="text"],
.page-content input[type="file"],
.page-content textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
}

.page-content button {
    width: 100%;
    padding: 12px;
    background-color: #c47474ff;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s;
}

.page-content button:hover {
    background-color: #5e3c25ff;
}
.page-content select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
    background-color: #fff;
    color: #333;
    appearance: none; /* removes default arrow in some browsers */
    -webkit-appearance: none;
    -moz-appearance: none;
}

</style>
</head>
<body>
     <div class="page-content">
    <h2>Customize Category</h2>
    <form method="POST" enctype="multipart/form-data">
       
        <label>Category Name:</label><br>
     <select name="name" required>
    <option value="">Select Category</option>
    <?php
    // Fetch admin categories from the database
    $cat_result = $con->query("SELECT * FROM admin_categories ORDER BY name ASC");
    if ($cat_result->num_rows > 0) {
        while ($cat = $cat_result->fetch_assoc()) {
            echo '<option value="' . htmlspecialchars($cat['name']) . '">' . htmlspecialchars($cat['name']) . '</option>';
        }
    }
    ?>
    </select><br><br>

        <label>Category Image:</label><br>
        <input type="file" name="image" accept="image/*" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="5"></textarea><br><br>

        <button type="submit">Add Category</button>
    </form>
    </div>
</body>
</html>