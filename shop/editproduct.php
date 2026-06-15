<?php
session_start();
include 'db_connect.php';
include 'shopheader.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    die("You must log in first.");
}

$userid = $_SESSION['user_id'];

// Get product id
if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$product_id = $_GET['id'];

// Fetch product (only if it belongs to this user)
$sql = "SELECT * FROM product WHERE product_id='$product_id' AND user_id='$userid'";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) != 1) {
    die("Product not found or you don't have permission.");
}

$product = mysqli_fetch_assoc($result);

// Fetch categories of this user for dropdown
$cat_sql = "SELECT * FROM category WHERE user_id='$userid'";
$cat_result = mysqli_query($con, $cat_sql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category'];

    $image = $product['image']; // default: keep existing

    if (!empty($_FILES['image']['name'])) {
        // delete old image
        if (!empty($product['image']) && file_exists("uploads/".$product['image'])) {
            unlink("uploads/".$product['image']);
        }

        $image = $_FILES['image']['name'];
        $target = "uploads/" . basename($image);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            echo "<p style='color:red;'>Failed to upload new image.</p>";
        }
    }

    // Update product
    $update = "UPDATE product SET name='$name', description='$description', price='$price', quantity='$quantity', category_id='$category_id', image='$image' WHERE product_id='$product_id' AND user_id='$userid'";
    if ($con->query($update)) {
        header("Location: viewproduct.php");
        exit();
    } else {
        echo "Error updating product: " . $con->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
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
.page-content input[type="number"],
.page-content input[type="file"],
.page-content textarea,
.page-content select,
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
<h2>Edit Product</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Product Name:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea><br><br>

    <label>Price:</label><br>
    <input type="text" name="price" step="0.01" value="<?php echo $product['price']; ?>" required><br><br>

    <label>Quantity:</label><br>
    <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" required><br><br>

    <label>Category:</label><br>
    <select name="category" required>
        <option value="">Select Category</option>
        <?php while ($cat = mysqli_fetch_assoc($cat_result)): ?>
            <option value="<?php echo $cat['cat_id']; ?>" <?php if ($cat['cat_id'] == $product['category_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($cat['name']); ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Current Image:</label><br>
    <?php if (!empty($product['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" style="max-width:150px;"><br><br>
    <?php else: ?>
        No image<br><br>
    <?php endif; ?>

    <label>Change Image:</label><br>
    <input type="file" name="image" accept="image/*"><br>
    <small>Leave empty to keep existing image.</small><br><br>

    <button type="submit">Update Product</button>
</form>
    </div>
</body>
</html>
