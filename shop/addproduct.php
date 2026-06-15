<?php
session_start();
include 'db_connect.php';
include 'shopheader.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must log in first.");
}

$userid = $_SESSION['user_id'];
 
// Fetch categories added by this user
$cat_sql = "SELECT * FROM category WHERE user_id='$userid'";
$cat_result = mysqli_query($con, $cat_sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO product (name, description, price, quantity, image, category_id, user_id)
                VALUES ('$name', '$description', '$price', '$quantity', '$image', '$category_id', '$userid')";
        if ($con->query($sql)) {
            header("Location: viewproduct.php"); // redirect to view products
            exit();
        } else {
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
    <title>Add Product</title>
    <style>
/* Page content wrapper */
.page-content {
    max-width: 500px;
    margin: 50px auto;
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
    margin-bottom: 8px;
    font-weight: bold;
}

.page-content input[type="text"],
.page-content input[type="number"],
.page-content input[type="file"],
.page-content select,
.page-content textarea {
    width: 100%;
    padding: 8px;           /* slightly smaller padding */
    margin-bottom: 12px;    /* reduced spacing */
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    box-sizing: border-box;
}

.page-content textarea {
    resize: vertical;
    min-height: 60px;       /* set a smaller default height */
}

.page-content button {
    width: 100%;
    padding: 10px;          /* smaller button */
    background-color: #c47474ff;
    color: white;
    font-size: 15px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
}
</style>
</head>
<body>
<div class="page-content">
<h2>Add New Product</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Product Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5" required></textarea><br><br>

    <label>Price:</label><br>
    <input type="text" name="price" step="0.01" required><br><br>

    <label>Quantity:</label><br>
    <input type="number" name="quantity" required><br><br>

    <label>Category:</label><br>
    <select name="category" required>
        <option value="">Select Category</option>
        <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
            <option value="<?php echo $cat['cat_id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Product Image:</label><br>
    <input type="file" name="image" accept="image/*" required><br><br>

    <button type="submit">Add Product</button>
</form>
</div>
</body>
</html>
