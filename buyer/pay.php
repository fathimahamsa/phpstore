<?php
include 'db_connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle payment when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    
    // Collect shipping address from form
    $shipping_name = mysqli_real_escape_string($con, $_POST['shipping_name']);
    $shipping_phone = mysqli_real_escape_string($con, $_POST['shipping_phone']);
    $shipping_address = mysqli_real_escape_string($con, $_POST['shipping_address']);
    $shipping_city = mysqli_real_escape_string($con, $_POST['shipping_city']);
    $shipping_state = mysqli_real_escape_string($con, $_POST['shipping_state']);
    $shipping_pincode = mysqli_real_escape_string($con, $_POST['shipping_pincode']);
    
    // Get cart items
    $cart_sql = "SELECT c.*, p.name, p.price, p.image 
                 FROM cart c 
                 JOIN product p ON c.product_id = p.product_id 
                 WHERE c.user_id = '$user_id'";
    $cart_result = mysqli_query($con, $cart_sql);
    
    // Calculate total
    $subtotal = 0;
    $cart_items = [];
    
    while ($item = mysqli_fetch_assoc($cart_result)) {
        $item_total = $item['price'] * $item['quantity'];
        $subtotal += $item_total;
        $cart_items[] = $item;
    }
    
    $shipping = 50;
    $tax = $subtotal * 0.18;
    $total = $subtotal + $shipping + $tax;
    
    // Create order with shipping address
    $order_sql = "INSERT INTO orders (user_id, total_amount, payment_method, 
                  shipping_name, shipping_phone, shipping_address, shipping_city, shipping_state, shipping_pincode) 
                  VALUES ('$user_id', '$total', '$payment_method',
                  '$shipping_name', '$shipping_phone', '$shipping_address', '$shipping_city', '$shipping_state', '$shipping_pincode')";
    
    if (mysqli_query($con, $order_sql)) {
        $order_id = mysqli_insert_id($con);
        
        // Add order items and reduce stock
        foreach ($cart_items as $item) {
            $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                               VALUES ('$order_id', '{$item['product_id']}', '{$item['quantity']}', '{$item['price']}')";
            mysqli_query($con, $order_item_sql);
            
            // Reduce stock quantity
            $update_stock_sql = "UPDATE product SET quantity = quantity - {$item['quantity']} 
                                 WHERE product_id = '{$item['product_id']}'";
            mysqli_query($con, $update_stock_sql);
        }
        
        // Clear cart
        $clear_cart = "DELETE FROM cart WHERE user_id = '$user_id'";
        mysqli_query($con, $clear_cart);
        
        // Show success message
        echo "<script>alert('Order placed successfully! Order ID: #$order_id'); window.location.href='index.php';</script>";
        exit();
    }
}

// Your existing cart display code
$cart_sql = "SELECT c.*, p.name, p.price, p.image 
             FROM cart c 
             JOIN product p ON c.product_id = p.product_id 
             WHERE c.user_id = '$user_id'
             LIMIT 3";
$cart_result = mysqli_query($con, $cart_sql);

$cart_items = [];
$subtotal = 0;
$total_items = 0;

if (mysqli_num_rows($cart_result) > 0) {
    while ($item = mysqli_fetch_assoc($cart_result)) {
        $item_total = $item['price'] * $item['quantity'];
        $subtotal += $item_total;
        $total_items += $item['quantity'];
        $cart_items[] = $item;
    }
}

$shipping = $subtotal > 0 ? 50 : 0;
$tax = $subtotal * 0.18;
$total = $subtotal + $shipping + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EcoStore</title>
    <link rel="stylesheet" href="mstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .address-form input, .address-form textarea {
            width: 100%;
            padding: 15px;
            margin: 12px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        .address-form textarea {
            height: 100px;
            resize: vertical;
        }
        
        .address-form input:focus, .address-form textarea:focus {
            border-color: #c47474ff;
            outline: none;
        }
        
        .shipping-section {
            margin-bottom: 30px;
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
                    <li><a href="wishlist.php">Wishlist</a></li>
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

    <div class="checkout-container">
        <h1 style="text-align: center; margin-bottom: 30px; color: #333;">Checkout</h1>
        
        <!-- Shipping Address at the Top -->
                <!-- Payment Form - Moved to wrap everything -->
        <form method="POST" action="">
        
        <!-- Shipping Address at the Top -->
        <div class="shipping-section">
            <div class="checkout-box" style="max-width: 800px; margin: 0 auto;">
                <h2 class="checkout-title" style="text-align: center; font-size: 24px; margin-bottom: 20px;">Shipping Address</h2>
                <div class="address-form">
                    <input type="text" name="shipping_name" placeholder="Full Name" required>
                    <input type="tel" name="shipping_phone" placeholder="Phone Number" required>
                    <textarea name="shipping_address" placeholder="Full Address (House No, Street, Area)" required></textarea>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <input type="text" name="shipping_city" placeholder="City" required>
                        <input type="text" name="shipping_state" placeholder="State" required>
                        <input type="text" name="shipping_pincode" placeholder="Pincode" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="checkout-row">
            <!-- Left Column - Payment Methods -->
            <div class="checkout-col">
                <div class="checkout-box">
                    <h2 class="checkout-title">Select Payment Method</h2>
                    
                    <div class="payment-methods">
                        <!-- Cash on Delivery -->
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <div class="payment-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="payment-name">Cash on Delivery</div>
                            <div class="payment-desc">Pay when you receive</div>
                        </label>
                        
                        <!-- UPI Payment -->
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="upi">
                            <div class="payment-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="payment-name">UPI Payment</div>
                            <div class="payment-desc">Google Pay, PhonePe, etc.</div>
                        </label>
                        
                        <!-- Credit/Debit Card -->
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="card">
                            <div class="payment-icon">
                                <i class="far fa-credit-card"></i>
                            </div>
                            <div class="payment-name">Credit/Debit Card</div>
                            <div class="payment-desc">Visa, MasterCard, RuPay</div>
                        </label>
                        
                        <!-- Net Banking -->
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="netbanking">
                            <div class="payment-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="payment-name">Net Banking</div>
                            <div class="payment-desc">All major banks</div>
                        </label>
                    </div>

                    <div class="delivery-info">
                        <h4>🛵 Delivery Information</h4>
                        <p><strong>Estimated Delivery:</strong> 3-5 business days</p>
                        <p><strong>Free Shipping:</strong> On orders above ₹500</p>
                        <p><strong>Return Policy:</strong> 7 days easy returns</p>
                    </div>
                </div>
            </div>

            <!-- Right Column - Order Summary -->
            <div class="checkout-col">
                <div class="checkout-box">
                    <h2 class="checkout-title">Order Summary</h2>
                    
                    <!-- Cart Items -->
                    <?php if (!empty($cart_items)): ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <div class="cart-item-details">
                                    <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="cart-item-price">
                                        ₹<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?>
                                    </div>
                                </div>
                                <div><strong>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 20px;">No items in cart</p>
                    <?php endif; ?>

                    <!-- Order Totals -->
                    <div class="order-summary-item">
                        <span>Subtotal (<?php echo $total_items; ?> items)</span>
                        <span>₹<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="order-summary-item">
                        <span>Shipping</span>
                        <span>₹<?php echo number_format($shipping, 2); ?></span>
                    </div>
                    <div class="order-summary-item">
                        <span>Tax (GST)</span>
                        <span>₹<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="order-summary-item order-total">
                        <span>Total Amount</span>
                        <span>₹<?php echo number_format($total, 2); ?></span>
                    </div>

                                        <!-- Payment Form Submit Button -->
                                        <!-- Payment Form Submit Button -->
                    <input type="hidden" name="payment_method" id="paymentMethodInput" value="cod">
                    
                    <button type="submit" class="pay-now-btn" onclick="setPaymentMethod()">
                        <i class="fas fa-lock"></i> Proceed to Pay ₹<?php echo number_format($total, 2); ?>
                    </button>

                    <p style="text-align: center; margin-top: 15px; font-size: 12px; color: #666;">
                        <i class="fas fa-shield-alt"></i> Your payment information is secure and encrypted
                    </p>
                </div>
            </div>
        </div>
        </form> <!-- Close the main form here -->
    </div>
                </div>
            </div>
        </div>
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
        // Set payment method before form submission
        function setPaymentMethod() {
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;
            document.getElementById('paymentMethodInput').value = selectedPayment;
        }

        // Payment method selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                // Add selected class to clicked option
                this.classList.add('selected');
                // Check the radio button
                this.querySelector('input').checked = true;
            });
        });

        // Initialize with first option selected
        document.querySelector('.payment-option').classList.add('selected');
    </script>
</body>
</html>