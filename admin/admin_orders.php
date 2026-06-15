<?php
session_start();
include 'adminheader.php';
$con = mysqli_connect("localhost","root","","ecostore");
if (!$con) { 
    die("DB Error: " . mysqli_connect_error()); 
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) && $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

// Handle order status update
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];
    
    $update_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $con->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    
    header("Location: admin_orders.php");
    exit();
}

// Handle payment status update
if (isset($_POST['update_payment_status'])) {
    $order_id = $_POST['order_id'];
    $new_payment_status = $_POST['payment_status'];
    
    $update_sql = "UPDATE orders SET payment_status = ? WHERE order_id = ?";
    $stmt = $con->prepare($update_sql);
    $stmt->bind_param("si", $new_payment_status, $order_id);
    $stmt->execute();
    
    header("Location: admin_orders.php");
    exit();
}

// Fetch all orders
$orders_sql = "SELECT o.*, u.f_name as customer_name, u.email as customer_email,
               COUNT(oi.order_item_id) as item_count,
               SUM(oi.quantity) as total_items
               FROM orders o 
               JOIN users u ON o.user_id = u.id
               LEFT JOIN order_items oi ON o.order_id = oi.order_id 
               GROUP BY o.order_id 
               ORDER BY o.created_at DESC";
$orders_result = mysqli_query($con, $orders_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Order Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #c47474ff;
            margin-bottom: 25px;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin-top: 20px;
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 12px; 
            text-align: left; 
        }
        th { 
            background-color: #c47474ff; 
            color: white; 
        }
        tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        .btn {
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            margin-right: 5px;
            font-size: 12px;
            display: inline-block;
            border: none;
            cursor: pointer;
            background-color: #c47474ff;
        }
        .btn:hover {
            background-color: #a85c5c;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce7ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .payment-pending { background: #fff3cd; color: #856404; }
        .payment-paid { background: #d4edda; color: #155724; }
        .payment-failed { background: #f8d7da; color: #721c24; }
        .order-details {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #c47474ff;
        }
        .control-form {
            margin: 10px 0;
        }
        .control-form select {
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 5px;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-left: 4px solid #c47474ff;
        }
        .stat-number {
            font-size: 1.5em;
            font-weight: bold;
            color: #c47474ff;
        }
        .stat-label {
            color: #666;
            margin-top: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Management</h2>

        <!-- Statistics -->
        <div class="stats-container">
            <?php
            // Count orders by status
            $total_orders = mysqli_num_rows($orders_result);
            $pending_sql = "SELECT COUNT(*) as count FROM orders WHERE order_status = 'pending'";
            $pending_result = mysqli_query($con, $pending_sql);
            $pending_count = mysqli_fetch_assoc($pending_result)['count'];
            
            $delivered_sql = "SELECT COUNT(*) as count FROM orders WHERE order_status = 'delivered'";
            $delivered_result = mysqli_query($con, $delivered_sql);
            $delivered_count = mysqli_fetch_assoc($delivered_result)['count'];
            
            $revenue_sql = "SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'";
            $revenue_result = mysqli_query($con, $revenue_sql);
            $revenue_total = mysqli_fetch_assoc($revenue_result)['total'] ?? 0;
            ?>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_orders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pending_count; ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $delivered_count; ?></div>
                <div class="stat-label">Delivered</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">₹<?php echo number_format($revenue_total, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <!-- Orders Table -->
        <table>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Items</th>
                <th>Order Status</th>
                <th>Payment Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php 
            if ($orders_result && $orders_result->num_rows > 0):
                while ($order = mysqli_fetch_assoc($orders_result)): 
            ?>
                <tr>
                    <td>#<?php echo $order['order_id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                    </td>
                    <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td><?php echo $order['total_items']; ?> items</td>
                    <td>
                        <span class="status-badge status-<?php echo strtolower($order['order_status']); ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="status-badge payment-<?php echo strtolower($order['payment_status']); ?>">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <button type="button" class="btn" onclick="toggleDetails(<?php echo $order['order_id']; ?>)">
                            View Details
                        </button>
                    </td>
                </tr>
                
                <!-- Order Details Row -->
                <tr id="details-<?php echo $order['order_id']; ?>" style="display: none;">
                    <td colspan="8">
                        <div class="order-details">
                            <h4>Order #<?php echo $order['order_id']; ?> Details</h4>
                            
                            <!-- Order Items -->
                            <div style="margin-bottom: 15px;">
                                <h5>Order Items:</h5>
                                <?php 
                                $items_sql = "SELECT oi.*, p.name, p.image, u.f_name as seller_name
                                             FROM order_items oi 
                                             JOIN product p ON oi.product_id = p.product_id 
                                             JOIN users u ON p.user_id = u.id
                                             WHERE oi.order_id = ?";
                                $items_stmt = $con->prepare($items_sql);
                                $items_stmt->bind_param("i", $order['order_id']);
                                $items_stmt->execute();
                                $items_result = $items_stmt->get_result();
                                
                                if ($items_result && $items_result->num_rows > 0):
                                    while ($item = $items_result->fetch_assoc()): ?>
                                        <div style="display: flex; align-items: center; padding: 8px; border-bottom: 1px solid #eee;">
                                            <img src="../shop/uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                            <div style="flex-grow: 1;">
                                                <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                                <small>Seller: <?php echo htmlspecialchars($item['seller_name']); ?></small><br>
                                                <small>Qty: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['price'], 2); ?></small>
                                            </div>
                                            <div style="font-weight: bold;">
                                                ₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?>
                                            </div>
                                        </div>
                                    <?php endwhile;
                                else: ?>
                                    <p>No items found for this order.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Shipping Address -->
                            <?php if (!empty($order['shipping_name'])): ?>
                                <div style="margin-bottom: 15px;">
                                    <h5>Shipping Address:</h5>
                                    <p><strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong><br>
                                    <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                    <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                                    <?php echo htmlspecialchars($order['shipping_state']); ?> - 
                                    <?php echo htmlspecialchars($order['shipping_pincode']); ?><br>
                                    <strong>Phone:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                                </div>
                            <?php endif; ?>

                            <!-- Admin Controls -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div class="control-form">
                                    <h5>Update Order Status:</h5>
                                    <form method="POST" style="display: flex; align-items: center;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <select name="order_status" required>
                                            <option value="pending" <?php echo $order['order_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $order['order_status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['order_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn">Update</button>
                                    </form>
                                </div>

                                <div class="control-form">
                                    <h5>Update Payment Status:</h5>
                                    <form method="POST" style="display: flex; align-items: center;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <select name="payment_status" required>
                                            <option value="pending" <?php echo $order['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="paid" <?php echo $order['payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="failed" <?php echo $order['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                        </select>
                                        <button type="submit" name="update_payment_status" class="btn">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php 
                endwhile;
            else: 
            ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        No orders found in the system.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <script>
        function toggleDetails(orderId) {
            const detailsRow = document.getElementById('details-' + orderId);
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row';
            } else {
                detailsRow.style.display = 'none';
            }
        }
    </script>
</body>
</html>