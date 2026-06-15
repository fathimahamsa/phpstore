<?php
include 'db_connect.php';

// Function to generate star ratings
function generateRating($rating) {
    $stars = '';
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    
    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<i class="fa fa-star"></i>';
    }
    
    // Half star
    if ($hasHalfStar) {
        $stars .= '<i class="fas fa-star-half-alt"></i>';
        $fullStars++; // Count half star as one for empty stars calculation
    }
    
    // Empty stars
    $emptyStars = 5 - $fullStars;
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<i class="far fa-star"></i>';
    }
    
    return $stars;
}

// Handle search functionality
$search_results = array();
$search_query = "";
$products = array();
$no_products = false;

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12; // 3 rows x 4 columns = 12 products per page
$start = ($page - 1) * $per_page;

// Sorting variables
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Check if search was performed
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $search_query = mysqli_real_escape_string($con, $search_term);
    
    // Search in both product name/description and category name
    $search_sql = "SELECT p.*, c.name as category_name 
                   FROM product p 
                   LEFT JOIN category c ON p.category_id = c.cat_id 
                   WHERE p.name LIKE '%$search_query%' 
                   OR p.description LIKE '%$search_query%' 
                   OR c.name LIKE '%$search_query%'";
    
    // Add sorting to search results
    switch($sort) {
        case 'price_high':
            $search_sql .= " ORDER BY p.price DESC";
            break;
        case 'price_low':
            $search_sql .= " ORDER BY p.price ASC";
            break;
        case 'rating':
            $search_sql .= " ORDER BY p.rating DESC";
            break;
        case 'sales':
            $search_sql .= " ORDER BY (SELECT SUM(quantity) FROM order_items oi WHERE oi.product_id = p.product_id) DESC";
            break;
        default:
            $search_sql .= " ORDER BY p.product_id DESC";
    }
    
    $search_result = $con->query($search_sql);
    if ($search_result && $search_result->num_rows > 0) {
        while($row = $search_result->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}

// Fetch all products if no search or if we need to show regular products
if (empty($search_query)) {
    if (isset($con) && $con instanceof mysqli) {
        // Base query
        $sql = "SELECT p.product_id as id, p.name, p.image, p.price, p.rating, 
                       COALESCE(SUM(oi.quantity), 0) as total_sold
                FROM product p 
                LEFT JOIN order_items oi ON p.product_id = oi.product_id";
        
        // Add sorting
        switch($sort) {
            case 'price_high':
                $sql .= " GROUP BY p.product_id ORDER BY p.price DESC";
                break;
            case 'price_low':
                $sql .= " GROUP BY p.product_id ORDER BY p.price ASC";
                break;
            case 'rating':
                $sql .= " GROUP BY p.product_id ORDER BY p.rating DESC";
                break;
            case 'sales':
                $sql .= " GROUP BY p.product_id ORDER BY total_sold DESC";
                break;
            default:
                $sql .= " GROUP BY p.product_id ORDER BY p.product_id DESC";
        }
        
        // Add pagination
        $sql .= " LIMIT $start, $per_page";
        
        $result = $con->query($sql);

        if ($result && $result->num_rows > 0) {
            // Fetch from database
            while($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        } else {
            // If no products found in database, show empty message
            $no_products = true;
        }
        
        // Get total products count for pagination
        $count_sql = "SELECT COUNT(*) as total FROM product";
        $count_result = $con->query($count_sql);
        $total_products = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_products / $per_page);
        
    } else {
        $no_products = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="mstyle.css">
    <title>product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
       

        .page-btn span {
            display: inline-block;
            border: 1px solid #ff523b;
            margin-left: 10px;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }

        .page-btn span:hover, .page-btn span.active {
            background: #ff523b;
            color: #fff;
        }

        .page-btn span a {
            text-decoration: none;
            color: inherit;
            display: block;
            width: 100%;
            height: 100%;
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
    
    <!-- Search Bar -->
    <div class="search-container">
        <form method="GET" action="" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Search products or categories..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
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
    <a href="product_details.php?id=<?php echo $product['id']; ?>">
        <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($product['image']); ?>">
        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
    </a>
    
    <!-- Stock Status -->
   <div class="col-4">
    <a href="product_details.php?id=<?php echo $product['id']; ?>">
        <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($product['image']); ?>">
        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
    </a>
    
    <!-- Stock Status -->
    <div class="stock-status">
        <?php 
        $stock_qty = $product['quantity'];
        if ($stock_qty <= 0): ?>
            <span style="color: red; font-weight: bold;">Out of Stock</span>
        <?php elseif ($stock_qty <= 10): ?>
            <span style="color: orange; font-weight: bold;">Only <?php echo $stock_qty; ?> left!</span>
        <?php else: ?>
            <span style="color: green;">In Stock</span>
        <?php endif; ?>
    </div>
    
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
                    <p>Try searching with different keywords.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Regular Products Display when no search -->
        <div class="small-container">
            <div class="row row-2">
                <h2>All Products</h2>
                <select onchange="location = this.value;">
                    <option value="?sort=default" <?php echo $sort == 'default' ? 'selected' : ''; ?>>Default Sorting</option>
                    <option value="?sort=price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Sort by price: high to low</option>
                    <option value="?sort=price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Sort by price: low to high</option>
                    <option value="?sort=rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Sort by rating</option>
                    <option value="?sort=sales" <?php echo $sort == 'sales' ? 'selected' : ''; ?>>Sort by sales</option>
                </select>
            </div>
            
            <div class="row">
                <?php if ($no_products): ?>
                    <div class="col-12">
                        <p>No products found in the database.</p>
                        <p>Please add some products to your database.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-4">
                            <a href="product_details.php?id=<?php echo $product['id']; ?>">
                                <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($product['image']); ?>">
                                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
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
                <?php endif; ?>
            </div>
            
            <?php if (!$no_products && $total_pages > 1): ?>
                <div class="page-btn">
                    <?php if ($page > 1): ?>
                        <span><a href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>">&#8592;</a></span>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <span class="<?php echo $i == $page ? 'active' : ''; ?>">
                            <a href="?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
                        </span>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <span><a href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>">&#8594;</a></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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
            
            if (dropdownToggle && dropdownMenu) {
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
            }
        });
    </script>
</body>
</html>