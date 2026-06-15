<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Handle add to wishlist
if (isset($_GET['add_to_wishlist'])) {
    if (!$is_logged_in) {
        header("Location: login.php");
        exit();
    }
    
    $product_id = $_GET['add_to_wishlist'];
    
    // Check if product already in wishlist
    $check_sql = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $con->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Add to wishlist
        $insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)";
        $stmt = $con->prepare($insert_sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    }
    header("Location: wishlist.php");
    exit();
}

// Handle remove from wishlist
if (isset($_GET['remove_wishlist'])) {
    if (!$is_logged_in) {
        header("Location: login.php");
        exit();
    }
    
    $product_id = $_GET['remove_wishlist'];
    $delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $stmt = $con->prepare($delete_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    header("Location: wishlist.php");
    exit();
}

// Fetch wishlist items if logged in
if ($is_logged_in) {
    $wishlist_sql = "SELECT w.*, p.name, p.price, p.image, p.quantity as stock_quantity 
                     FROM wishlist w 
                     JOIN product p ON w.product_id = p.product_id 
                     WHERE w.user_id = ?";
    $stmt = $con->prepare($wishlist_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $wishlist_result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="mstyle.css">
    <title>Wishlist - EcoStore</title>
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

    <!----wishlist item details----->
    <div class="small-container cart-page">
        <?php if (!$is_logged_in): ?>
            <!-- Show login message if not logged in -->
            <div style="text-align: center; padding: 50px; background: #f9f9f9; border-radius: 10px;">
                <h3>Please Login First</h3>
                <p>You need to be logged in to view your wishlist.</p>
                <a href="login.php" class="btn">Login Now</a>
                <a href="product.php" class="btn" style="background: #6c757d;">Continue Shopping</a>
            </div>
        <?php elseif ($wishlist_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
                <?php while ($wishlist_item = $wishlist_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="cart-info">
                                <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($wishlist_item['image']); ?>">
                                <div>
                                    <p><?php echo htmlspecialchars($wishlist_item['name']); ?></p>
                                    <small>Price: &#8377;<?php echo number_format($wishlist_item['price'], 2); ?></small>
                                    <br>
                                    <a href="product_details.php?id=<?php echo $wishlist_item['product_id']; ?>">View Product</a>
                                    <br>
                                    <a href="wishlist.php?remove_wishlist=<?php echo $wishlist_item['product_id']; ?>" 
                                       onclick="return confirm('Remove from wishlist?')">Remove</a>
                                </div>
                            </div>
                        </td>
                        <td>&#8377;<?php echo number_format($wishlist_item['price'], 2); ?></td>
                        <td>
                            <a href="cart.php?add_to_cart=<?php echo $wishlist_item['product_id']; ?>" 
                               class="btn" style="padding: 5px 10px; font-size: 12px;">Add to Cart</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <!-- Show empty wishlist message -->
            <div style="text-align: center; padding: 50px;">
                <h3>Your wishlist is empty</h3>
                <p>Add some products to your wishlist!</p>
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
                    <h3>Follow As</h3>
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