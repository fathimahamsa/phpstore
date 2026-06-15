<?php
session_start();
include 'db_connect.php';
include 'shopheader.php';

// Check if user is logged in as seller
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$seller_id = $_SESSION['user_id'];

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];
    
    $update_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $con->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    
    // Refresh page to show updated status
    header("Location: seller_orders.php");
    exit();
}

// Fetch orders containing seller's products
$orders_sql = "SELECT DISTINCT o.*, 
               u.f_name as customer_name, u.email as customer_email,
               COUNT(oi.order_item_id) as item_count,
               SUM(oi.quantity) as total_items
               FROM orders o 
               JOIN order_items oi ON o.order_id = oi.order_id 
               JOIN product p ON oi.product_id = p.product_id 
               JOIN users u ON o.user_id = u.id
               WHERE p.user_id = ? 
               GROUP BY o.order_id
               ORDER BY o.created_at DESC";
$stmt = $con->prepare($orders_sql);
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Orders - EcoStore</title>
    <style>
        .page-content {
            max-width: 1200px;
            margin: 30px auto;
            background: rgba(255,255,255,0.9);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        
        .order-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #c47474ff;
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
        
        .status-form {
            margin-top: 15px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 5px;
        }
        
        .status-form select, .status-form button {
            padding: 8px 15px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .status-form button {
            background: #c47474ff;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .status-form button:hover {
            background: #a85c5c;
        }
        
        .seller-product {
            background: #f0f8ff;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="page-content">
        <h2 style="text-align: center; color: #c47474ff; margin-bottom: 30px;">Customer Orders</h2>
        
        <?php if ($orders_result->num_rows > 0): ?>
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h3>Order #<?php echo $order['order_id']; ?></h3>
                            <p>Placed on: <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></p>
                            <p>Customer: <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo htmlspecialchars($order['customer_email']); ?>)</p>
                            <p>Total: ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                        <div class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </div>
                    </div>

                    <!-- Seller's Products in this Order -->
                    <div class="order-items">
                        <h4>Your Products in this Order:</h4>
                        <?php 
                        $seller_items_sql = "SELECT oi.*, p.name, p.image, p.product_id, p.user_id as seller_id
                                             FROM order_items oi 
                                             JOIN product p ON oi.product_id = p.product_id 
                                             WHERE oi.order_id = ? AND p.user_id = ?";
                        $items_stmt = $con->prepare($seller_items_sql);
                        $items_stmt->bind_param("ii", $order['order_id'], $seller_id);
                        $items_stmt->execute();
                        $seller_items_result = $items_stmt->get_result();
                        
                        while ($item = $seller_items_result->fetch_assoc()): ?>
                            <div class="order-item seller-product">
                                <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($item['image']); ?>">
                                <div style="flex-grow: 1;">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Quantity: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['price'], 2); ?></p>
                                    <p><strong>Subtotal: ₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?></strong></p>
                                </div>
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

                    <!-- Order Status Update Form -->
                    <div class="status-form">
                        <h4>Update Order Status</h4>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <select name="order_status" required>
                                <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status">Update Status</button>
                        </form>
                    </div>

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
                <p>You haven't received any orders for your products yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>