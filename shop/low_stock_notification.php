<?php
session_start();
include 'db_connect.php';
include 'shopheader.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: You must log in first.");
}

$userid = $_SESSION['user_id'];

// Get low stock products
$sql = "SELECT * FROM product WHERE user_id='$userid' AND quantity <= 10 ORDER BY quantity ASC";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
    <style>
        .low-stock-item {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
        }
        .out-of-stock {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .notification-badge {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 3px 8px;
            font-size: 14px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="page-content">
        <h2>Low Stock Alerts 
            <?php if(mysqli_num_rows($result) > 0): ?>
                <span class="notification-badge"><?php echo mysqli_num_rows($result); ?></span>
            <?php endif; ?>
        </h2>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($product = mysqli_fetch_assoc($result)): ?>
                <div class="low-stock-item <?php echo $product['quantity'] == 0 ? 'out-of-stock' : ''; ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><strong>Current Stock:</strong> 
                        <span style="color: <?php echo $product['quantity'] == 0 ? 'red' : 'orange'; ?>; font-weight: bold;">
                            <?php echo $product['quantity']; ?> items
                        </span>
                    </p>
                    <p><strong>Price:</strong> ₹<?php echo number_format($product['price'], 2); ?></p>
                    <a href="editproduct.php?id=<?php echo $product['product_id']; ?>" class="action-btn edit-btn">Update Stock</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No low stock products. All items have sufficient quantity.</p>
        <?php endif; ?>
    </div>
</body>
</html>