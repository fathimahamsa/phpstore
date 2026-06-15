<?php
include 'db_connect.php';

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the main product details from database
$product_sql = "SELECT * FROM product WHERE product_id = $product_id";
$product_result = mysqli_query($con, $product_sql);

if (mysqli_num_rows($product_result) > 0) {
    $product = mysqli_fetch_assoc($product_result);
    $product_name = $product['name'];
    $product_description = $product['description'];
    $product_price = $product['price'];
    $product_quantity = $product['quantity'];
    $main_image = $product['image'];
    $category_id = $product['category_id'];
    
    // Get category name
    $cat_sql = "SELECT name FROM category WHERE cat_id = $category_id";
    $cat_result = mysqli_query($con, $cat_sql);
    $category = mysqli_fetch_assoc($cat_result);
    $category_name = $category ? $category['name'] : "Uncategorized";
} else {
    // If product not found, redirect to products page
    header("Location: product.php");
    exit();
}

// Fetch related products from the same category
$related_sql = "SELECT * FROM product WHERE category_id = $category_id AND product_id != $product_id LIMIT 4";
$related_result = mysqli_query($con, $related_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="mstyle.css">
    <title><?php echo htmlspecialchars($product_name); ?> - EcoStore</title>
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
    
    <!--------single product details------>
    
    <div class="small-container single-product">
        <div class="row">
            <div class="col-2">
            
                <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($main_image); ?>" width="100%" id="ProductImg">  
                <div class="small-img-row">
                    <div class="small-img-col">
                        <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($main_image); ?>" width="100%" class="small-img">
                    </div>
                    
                </div> 
            </div>
            <div class="col-2">
                <p>Home / <?php echo htmlspecialchars($category_name); ?></p> 
                <h1><?php echo htmlspecialchars($product_name); ?></h1>
                <h4>&#8377;<?php echo number_format($product_price, 2); ?></h4>
                
                <!-- Stock Details -->
                
<?php if($product_quantity > 0): ?>
    <?php if($product_quantity <= 10): ?>
        <p style="color: orange; font-weight: bold;">⚠️ Only <?php echo $product_quantity; ?> items left - Limited Stock!</p>
    <?php else: ?>
        <p style="color: green; font-weight: bold;">In Stock</p>
    <?php endif; ?>
    <form method="GET" action="cart.php" style="display: inline;">
        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product_quantity; ?>">
        <input type="hidden" name="add_to_cart" value="<?php echo $product_id; ?>">
        <button type="submit" class="btn" style="border: none; outline: none;">Add to cart</button>
    </form>
    <a href="wishlist.php?add_to_wishlist=<?php echo $product_id; ?>" class="btn">
        <i class="far fa-heart"></i> Add to Wishlist
    </a>
<?php else: ?>
    <p style="color: red; font-weight: bold;">Out of Stock</p>
    <input type="number" value="0" disabled>
    <a href="" class="btn" style="background-color: #ccc; cursor: not-allowed; border: none;">Out of Stock</a>
    <a href="wishlist.php?add_to_wishlist=<?php echo $product_id; ?>" class="btn" style="border: none;">
        <i class="far fa-heart"></i> Add to Wishlist
    </a>
<?php endif; ?>
                
                <h3>Product Details</h3>
                <br>
                <p><?php echo nl2br(htmlspecialchars($product_description)); ?></p>
            </div>
        </div>
    </div>
    
<!------title------>
 <div class="small-container">
        <div class="row row-2">
            <h2>Related Products</h2>
            <a href="category.php?id=<?php echo $category_id; ?>">View More</a>
        </div>
    </div>
    
    <div class="small-container">
        <div class="row">
            <?php if (mysqli_num_rows($related_result) > 0): ?>
                <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                    <div class="col-4">
                        <a href="product_details.php?id=<?php echo $related['product_id']; ?>">
                            <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($related['image']); ?>">
                            <h4><?php echo htmlspecialchars($related['name']); ?></h4>
                        </a>
                        <div class="rating">
                        <?php 
                            $fullStars = floor($related['rating']);
                            $halfStar = ($related['rating'] - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                            for($i = 0; $i < $fullStars; $i++) echo '<i class="fa fa-star"></i>';
                            if($halfStar) echo '<i class="fas fa-star-half-alt"></i>';
                            for($i = 0; $i < $emptyStars; $i++) echo '<i class="far fa-star"></i>';
                            ?>
                        </div>
                        <p>&#8377;<?php echo number_format($related['price'], 2); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No related products found.</p>
            <?php endif; ?>
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
    
<!-------js for product gallery------>
<script>
    var ProductImg = document.getElementById("ProductImg");
    var SmallImg = document.getElementsByClassName("small-img");

    SmallImg[0].onclick = function() {
        ProductImg.src = SmallImg[0].src;
    }
    SmallImg[1].onclick = function() {
        ProductImg.src = SmallImg[1].src;
    }
</script>

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