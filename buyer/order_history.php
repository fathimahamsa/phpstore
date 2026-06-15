<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders with shipping details
$orders_sql = "SELECT o.*, 
               COUNT(oi.order_item_id) as item_count,
               SUM(oi.quantity) as total_items
               FROM orders o 
               LEFT JOIN order_items oi ON o.order_id = oi.order_id 
               WHERE o.user_id = ? 
               GROUP BY o.order_id 
               ORDER BY o.created_at DESC";
$stmt = $con->prepare($orders_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - EcoStore</title>
    <link rel="stylesheet" href="mstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .order-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #ff523b;
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce7ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .order-items img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
        }
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .shipping-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <img src="image/eco logo.png" width="125px">
            </div>
            <nav>
                <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="product.php">Product</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="wishlist.php">Wishist</a></li>
                <!-- Dropdown menu -->
                <li class="dropdown">
                    <button class="dropdown-toggle">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="cart.php" class="dropdown-item">
                            <i class="fas fa-shopping-cart"></i>
                            Cart 
                        </a>
                        
                        <a href="order_history.php" class="dropdown-item">
                            <i class="fas fa-box"></i>
                            My Order
                        </a>
                        
                        <a href="login.php" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            Login
                        </a>
                        <a href="logout.php" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
            </nav>
        </div>    
    </div>

    <div class="small-container">
        <h2 style="text-align: center; margin: 30px 0;">My Order History</h2>
        
        <?php if ($orders_result->num_rows > 0): ?>
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['order_id']; ?></h3>
                            <p>Placed on: <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></p>
                            <p><?php echo $order['total_items']; ?> items • Total: ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                        <div class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="order-items">
                        <?php 
                        $items_sql = "SELECT oi.*, p.name, p.image 
                                     FROM order_items oi 
                                     JOIN product p ON oi.product_id = p.product_id 
                                     WHERE oi.order_id = ?";
                        $items_stmt = $con->prepare($items_sql);
                        $items_stmt->bind_param("i", $order['order_id']);
                        $items_stmt->execute();
                        $items_result = $items_stmt->get_result();
                        
                        while ($item = $items_result->fetch_assoc()): ?>
                            <div class="order-item">
                                <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($item['image']); ?>">
                                <div style="flex-grow: 1;">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Quantity: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div>₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?></div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Shipping Address -->
                    <?php if (!empty($order['shipping_name'])): ?>
                        <div class="shipping-info">
                            <h4>Shipping Address</h4>
                            <p><strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong></p>
                            <p><?php echo htmlspecialchars($order['shipping_address']); ?></p>
                            <p><?php echo htmlspecialchars($order['shipping_city']); ?>, 
                               <?php echo htmlspecialchars($order['shipping_state']); ?> - 
                               <?php echo htmlspecialchars($order['shipping_pincode']); ?></p>
                            <p>Phone: <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Order Tracking -->
                    <div style="margin-top: 15px; padding: 15px; background: #e9ecef; border-radius: 5px;">
                        <h4>Order Tracking</h4>
                        <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                            <div style="text-align: center;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background: <?php echo $order['order_status'] != 'pending' ? '#28a745' : '#6c757d'; ?>; margin: 0 auto;"></div>
                                <small>Order Placed</small>
                            </div>
                            <div style="text-align: center;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background: <?php echo in_array($order['order_status'], ['processing', 'shipped', 'delivered']) ? '#28a745' : '#6c757d'; ?>; margin: 0 auto;"></div>
                                <small>Processing</small>
                            </div>
                            <div style="text-align: center;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background: <?php echo in_array($order['order_status'], ['shipped', 'delivered']) ? '#28a745' : '#6c757d'; ?>; margin: 0 auto;"></div>
                                <small>Shipped</small>
                            </div>
                            <div style="text-align: center;">
                                <div style="width: 20px; height: 20px; border-radius: 50%; background: <?php echo $order['order_status'] == 'delivered' ? '#28a745' : '#6c757d'; ?>; margin: 0 auto;"></div>
                                <small>Delivered</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <h3>No orders found</h3>
                <p>You haven't placed any orders yet.</p>
                <a href="product.php" class="btn">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <!---------footer--------->
    <div class="footer"> 
        <div class="container">
            <div class="row">
                <div class="footer-col-1">
                    <h3>Download Our App</h3>
                    <p>Download App for Android and ios mobile phone.</p>
                    <div class="app-logo">
                        <img src="image/app-icons.png">
                    </div>
                </div>
                <div class="footer-col-2">
                    <img src="image/eco logo.png" width="125px">
                    <p>Our goal is to create a future that is eco-friendly and sustainable, where every choice we make supports the health of our planet and promotes a greener lifestyle for generations to come.</p>
                </div>
                <div class="footer-col-3">
                    <h3>Useful Link</h3>
                    <ul>
                        <li>Coupons</li>
                        <li>Blog Post</li>
                        <li>Return Policy</li>
                        <li>Joint Affiliate</li>
                    </ul>
                </div>
                <div class="footer-col-4">
                    <h3>Follow Us</h3>
                    <ul>
                        <li>Facebook</li>
                        <li>Twitter</li>
                        <li>Instagram</li>
                        <li>YouTube</li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="copyright">Copyright-2024 ACT2003</p>
        </div>
    </div>
    <script>
    // Dropdown menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        const dropdownMenu = document.querySelector('.dropdown-menu');
        
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            dropdownMenu.classList.remove('show');
        });
        
        // Prevent dropdown from closing when clicking inside it
        dropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
</body>
</html>