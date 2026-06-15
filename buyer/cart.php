<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle add to cart
if (isset($_GET['add_to_cart'])) {
    $product_id = $_GET['add_to_cart'];
    $quantity = 1;
    
    // Check if product already in cart
    $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $con->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update quantity
        $update_sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    } else {
        // Add new item
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $con->prepare($insert_sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
    }
    header("Location: cart.php");
    exit();
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    $delete_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $con->prepare($delete_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Handle quantity update
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    if ($quantity <= 0) {
        // Remove if quantity is 0 or less
        $delete_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $con->prepare($delete_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    } else {
        // Update quantity
        $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $stmt->execute();
    }
    header("Location: cart.php");
    exit();
}

// Fetch cart items with product details
$cart_sql = "SELECT c.*, p.name, p.price, p.image, p.quantity as stock_quantity 
             FROM cart c 
             JOIN product p ON c.product_id = p.product_id 
             WHERE c.user_id = ?";
$stmt = $con->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$subtotal = 0;
$tax_rate = 0.18; // 18% tax
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="mstyle.css">
    <title>Cart - EcoStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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

    <!----cart item details----->
    <div class="small-container cart-page">
        <?php if ($cart_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
                <?php while ($cart_item = $cart_result->fetch_assoc()): 
                    $item_total = $cart_item['price'] * $cart_item['quantity'];
                    $subtotal += $item_total;
                ?>
                    <tr>
                        <td>
                            <div class="cart-info">
                                <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($cart_item['image']); ?>">
                                <div>
                                    <p><?php echo htmlspecialchars($cart_item['name']); ?></p>
                                    <small>Price: &#8377;<?php echo number_format($cart_item['price'], 2); ?></small>
                                    <br>
                                    <a href="cart.php?remove=<?php echo $cart_item['product_id']; ?>" onclick="return confirm('Remove this item from cart?')">Remove</a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $cart_item['product_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $cart_item['quantity']; ?>" min="1" max="<?php echo $cart_item['stock_quantity']; ?>" onchange="this.form.submit()">
                                <input type="hidden" name="update_quantity" value="1">
                            </form>
                        </td>
                        <td>&#8377;<?php echo number_format($item_total, 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
            
            <div class="total-price">
                <?php
                $tax = $subtotal * $tax_rate;
                $total = $subtotal + $tax;
                ?>
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td>&#8377;<?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Tax (18%)</td>
                        <td>&#8377;<?php echo number_format($tax, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Total</td>
                        <td>&#8377;<?php echo number_format($total, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 20px;">
                            <a href="pay.php" class="btn">Proceed to Checkout</a>
                        </td>
                    </tr>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 50px;">
                <h3>Your cart is empty</h3>
                <p>Add some products to your cart!</p>
                <a href="product.php" class="btn">Continue Shopping</a>
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

</body>
</html>