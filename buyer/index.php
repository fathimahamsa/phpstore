<?php
include 'db_connect.php';

// Function to generate star ratings (same as stationary.php)
function generateRating($rating) {
    $stars = '';
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

    // Full stars
    for($i = 0; $i < $fullStars; $i++) {
        $stars .= '<i class="fa fa-star"></i>';
    }
    
    // Half star
    if($halfStar) {
        $stars .= '<i class="fas fa-star-half-alt"></i>';
    }
    
    // Empty stars
    for($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<i class="far fa-star"></i>';
    }
    
    return $stars;
}

// Handle search functionality
$search_results = array();
$search_query = "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $search_query = mysqli_real_escape_string($con, $search_term);
    
    // Search in both product name/description and category name
    $search_sql = "SELECT p.*, c.name as category_name 
                   FROM product p 
                   LEFT JOIN category c ON p.category_id = c.cat_id 
                   WHERE p.name LIKE '%$search_query%' 
                   OR p.description LIKE '%$search_query%' 
                   OR c.name LIKE '%$search_query%' 
                   ORDER BY p.product_id DESC";
    
    $search_result = $con->query($search_sql);
    if ($search_result && $search_result->num_rows > 0) {
        while($row = $search_result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}

// Fetch featured products (limit to 4)
$featured_products = array();
$featured_query = "SELECT product_id, name, image, price, rating, description FROM product ORDER BY product_id LIMIT 4";
$featured_result = $con->query($featured_query);
if ($featured_result && $featured_result->num_rows > 0) {
    while($row = $featured_result->fetch_assoc()) {
        $featured_products[] = $row;
    }
}

// Fetch latest products (based on created_at date, most recent first)
$latest_products = array();
$latest_query = "SELECT product_id, name, image, price, rating, description FROM product ORDER BY created_at DESC, product_id DESC LIMIT 8";
$latest_result = $con->query($latest_query);
if ($latest_result && $latest_result->num_rows > 0) {
    while($row = $latest_result->fetch_assoc()) {
        $latest_products[] = $row;
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="mstyle.css">
    <title>index</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>
<body>
    <div class="header">
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <img src="image/eco logo.png" width="125px">
            </div>
            <nav>
            <ul>
                <li><a href="">Home</a></li>
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
                        <a href="pay.php" class="dropdown-item">
                            <i class="fas fa-shopping-cart"></i>
                            pay
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
        
        <!-- Search Bar -->
        <div class="search-container">
            <form method="GET" action="" class="search-form">
                <input type="text" name="search" class="search-input" placeholder="Search products or categories..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
        
        <div class="row">
            <div class="col-2">
                <h1>Elevate Your Brand With <br>Sustainable Products</h1>
                <p>Shop sustainably,choose consciously.Good for you, good for planet. So live green,shop green</p>
                <a href="product.php" class="btn">Explore Now &#8594;</a>
            </div>
            <div class="col-2">
                <img src="image/flower.jpg" >
            </div>
        </div>
    </div>
    </div>

  
    <!-- Display Search Results if search was performed -->
    <?php if (!empty($search_query)): ?>
        <div class="small-container">
            <h2 class="search-results-title">
                Search Results for "<span class="search-term"><?php echo htmlspecialchars($search_query); ?></span>"
            </h2>
            
            <?php if (count($search_results) > 0): ?>
                <div class="row">
                    <?php foreach ($search_results as $product): ?>
                        <div class="col-4">
                            <a href="product_details.php?id=<?php echo $product['product_id']; ?>"> 
                                <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($product['image']); ?>">
                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                <?php if (isset($product['category_name'])): ?>
                                    <small style="color: #666;">Category: <?php echo htmlspecialchars($product['category_name']); ?></small>
                                <?php endif; ?>
                            </a>
                            <div class="rating">
                                <?php 
                                $displayRating = $product['rating'] > 0 ? $product['rating'] : 0;
                                echo generateRating($displayRating); 
                                ?>
                            </div>
                            <p>&#8377;<?php echo htmlspecialchars($product['price']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <p>No products found matching your search criteria.</p>
                    <p>Try searching with different keywords or browse our categories.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-----featured category------->
    <?php if (empty($search_query)): ?>
    <div class="categories"> 
        <div class="small-container">
            <div class="row">
                <div class="col-3">
                    <a href="home.php?cat_ids=18,19,20,27">
                    <img src="image/kitchen1.jpg" alt="Home & Living">
                    <p>Home & Living</p>
                    </a>
                </div>
                

                <div class="col-3">
                    <a href="care.php?cat_ids=25">
                    <img src="image/skin1.jpg" alt="Personal Care">
                    <p>Personal Care</p>
                    </a>
                 </div>

                <div class="col-3">
                    <a href="fashion.php?cat_ids=23,24">
                    <img src="image/fashion1.jpg" alt="Fashion & Accessories">
                    <p>Fashion & Accessories</p>
                    </a>
                </div>

                    <div class="col-3">
                        <a href="stationary.php?cat_ids=21,22">
                    <img src="image/office1.jpg" alt="Stationery & Office">
                    <p>Stationery & Office</p>
                    </a>
                    </div>

                <div class="col-3">
                    <a href="garden.php?cat_ids=26">
                    <img src="image/garden1.jpg" alt="Plants & Gardening">
                    <p>Plants & Gardening</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-----featured product------->
    <?php if (empty($search_query)): ?>        
    <div class="small-container">
        <h2 class="title">Featured Products</h2>
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-4">
                    <a href="product_details.php?id=<?php echo $product['product_id']; ?>"> 
                        <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($product['image']); ?>">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    </a>
                    <div class="rating">
                        <?php 
                        // Use actual rating from database, default to 0 if not set
                        $displayRating = $product['rating'] > 0 ? $product['rating'] : 0;
                        echo generateRating($displayRating); 
                        ?>
                    </div>
                    <p>&#8377;<?php echo htmlspecialchars($product['price']); ?></p>
                    
                </div>
            <?php endforeach; ?>
        </div>
        <!--------Latest product------->
        <h2 class="title">Latest Products</h2>
        <div class="row">
            <?php foreach ($latest_products as $product): ?>
                <div class="col-4">
                    <a href="product_details.php?id=<?php echo $product['product_id']; ?>"> 
                        <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($product['image']); ?>">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    </a>
                    <div class="rating">
                        <?php 
                        // Use actual rating from database, default to 0 if not set
                        $displayRating = $product['rating'] > 0 ? $product['rating'] : 0;
                        echo generateRating($displayRating); 
                        ?>
                    </div>
                    <p>&#8377;<?php echo htmlspecialchars($product['price']); ?></p>
                    
                </div>
            <?php endforeach; ?>
        </div>   
    </div>
    <?php endif; ?>

    <!--------offer--------->
    <?php if (empty($search_query)): ?>
    <div class="offer"> 
        <div class="small-container">
            <div class="row">
                <div class="col-2">
                    
                    <img src="image/port making.jpg" class="offer-img">
                    
                    
                </div>
                <div class="col-2">
                    <h2><B>CookWare With A Stories<B></h2>
                    <h1> Premium Collection</h1>
                    <small>Add a touch of Indian culture and royal style to your home with authentic dinnerware, cookware,and serveware<br></small>    
                    <a href="traditional.php?cat_ids=27" class="btn">Buy Now &#8594;</a>   
                </div>
            </div>
        </div>   
     </div>
     <?php endif; ?>

     <!------testimonial----->
     <?php if (empty($search_query)): ?>
     <div class="testimonial">
        <div class="small-container">
            <div class="row">
                <div class="col-3">
                    <i class="fa fa-quote-left"></i>
                    <p>This is the best site for people who buy eco-friendly products. There is also a separate page for traditional cookware, which I like the most about this website. My mother really likes it too. I will surely recommend this site.</p>
                    <div class="rating">
                        
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <i class="far fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <img src="image/test1.jpg">
                    <h3>Nandhana C N</h3>
                </div>
                <div class="col-3">
                    <i class="fa fa-quote-left"></i>
                    <p>Ecostore makes eco-friendly shopping so simple and enjoyableI highly recommend Ecostore to anyone who wants quality products
                    <div class="rating">
                         <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>

                     </div>
                    <img src="image/test2.jpg">
                    <h3>Shalbin Varghese</h3>
                </div>
                <div class="col-3">
                    <i class="fa fa-quote-left"></i>
                    <p>The website is very user-friendly, and it's great to have a platform dedicated only to eco-friendly items. The products are well-categorized, making it easy to find what you need.</p>
                    <div class="rating">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <i class="far fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                    <img src="image/test3.jpg">
                    <h3>Sourav</h3>
                </div>
            </div>
        </div>
     </div>
     <?php endif; ?>

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