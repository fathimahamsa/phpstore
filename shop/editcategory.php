<?php
session_start();
include 'db_connect.php';
include 'shopheader.php';

// check login
if (!isset($_SESSION['user_id'])) {
    die("You must log in first.");
}

$userid = $_SESSION['user_id'];

// get category id
if (!isset($_GET['id'])) {
    die("Category ID missing.");
}

$cat_id = $_GET['id'];

// fetch category only if it belongs to user
$sql = "SELECT * FROM category WHERE cat_id='$cat_id' AND user_id='$userid'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) != 1) {
    die("Category not found or you don't have permission.");
}

$row = mysqli_fetch_assoc($result);

// handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);

    $image = $row['image']; // default: keep existing image
    if (!empty($_FILES['image']['name'])) {
        // delete old image file if exists
        if (!empty($row['image']) && file_exists("uploads/".$row['image'])) {
            unlink("uploads/".$row['image']);
        }

        // upload new image
        $image = $_FILES['image']['name'];
        $target = "uploads/" . basename($image);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            echo "<p style='color:red;'>Failed to upload new image.</p>";
        }
    }

    // update category in DB
    $update = "UPDATE category SET name='$name', description='$description', image='$image' WHERE cat_id='$cat_id' AND user_id='$userid'";
    if ($con->query($update)) {
        header("Location: viewcategory.php");
        exit();
    } else {
        echo "Error updating category: " . $con->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <style>
.page-content {
    max-width: 500px;
    margin: 50px auto;
    background: rgba(255,255,255,0.95);
    padding: 25px 20px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

.page-content h2 {
    text-align: center;
    color: #c47474ff;
    margin-bottom: 20px;
}

.page-content input[type="text"],
.page-content input[type="file"],
.page-content textarea,
.page-content button {
    width: 100%;
    padding: 8px;
    margin-bottom: 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
}

.page-content textarea {
    resize: vertical;
}

.page-content button {
    background-color: #c47474ff;
    color: white;
    font-size: 15px;
    border: none;
    cursor: pointer;
    transition: background 0.3s;
}

.page-content button:hover {
    background-color: #5e3c25ff;
}
</style>
</head>
<body>
    <div class="page-content">
<h2>Edit Category</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5"><?php echo htmlspecialchars($row['description']); ?></textarea><br><br>

    <label>Current Image:</label><br>
    <?php if(!empty($row['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" style="max-width:150px;"><br><br>
    <?php else: ?>
        No image uploaded<br><br>
    <?php endif; ?>

    <label>Change Image:</label><br>
    <input type="file" name="image" accept="image/*"><br>
    <small>Leave empty to keep existing image.</small><br><br>

    <button type="submit">Update Category</button>
</form>
    </div>
</body>
</html>
