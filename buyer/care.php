<?php
include 'db_connect.php';

// Get category ids from URL
if (!isset($_GET['cat_ids'])) {
    die("Category not found!");
}
$cat_ids = $_GET['cat_ids'];
$cat_id_array = explode(',', $cat_ids);

// Pagination variables
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12; // 3 rows x 4 columns = 12 products per page
$start = ($page - 1) * $per_page;

// Sorting variables
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Create placeholders for SQL
$placeholders = str_repeat('?,', count($cat_id_array) - 1) . '?';

// Fetch category names
$cat_sql = "SELECT name FROM category WHERE cat_id IN ($placeholders)";
$cat_stmt = mysqli_prepare($con, $cat_sql);

// Bind parameters
$types = str_repeat('i', count($cat_id_array));
$params = array_merge([$cat_stmt, $types], $cat_id_array);
$refs = [];
foreach($params as $key => $value) {
    $refs[$key] = &$params[$key];
}
call_user_func_array('mysqli_stmt_bind_param', $refs);

mysqli_stmt_execute($cat_stmt);
$cat_result = mysqli_stmt_get_result($cat_stmt);

if (mysqli_num_rows($cat_result) == 0) {
    die("Category not found!");
}

$category_names = [];
while ($cat_row = mysqli_fetch_assoc($cat_result)) {
    $category_names[] = $cat_row['name'];
}
$category_name = implode(' & ', $category_names);

// Base product query with sales calculation
$product_sql = "SELECT p.*, COALESCE(SUM(oi.quantity), 0) as total_sold 
                FROM product p 
                LEFT JOIN order_items oi ON p.product_id = oi.product_id 
                WHERE p.category_id IN ($placeholders) 
                GROUP BY p.product_id";

// Add sorting
switch($sort) {
    case 'price_high':
        $product_sql .= " ORDER BY p.price DESC";
        break;
    case 'price_low':
        $product_sql .= " ORDER BY p.price ASC";
        break;
    case 'rating':
        $product_sql .= " ORDER BY p.rating DESC";
        break;
    case 'sales':
        $product_sql .= " ORDER BY total_sold DESC";
        break;
    default:
        $product_sql .= " ORDER BY p.product_id DESC";
}

// Add pagination
$product_sql .= " LIMIT $start, $per_page";

// Prepare and execute product query
$product_stmt = mysqli_prepare($con, $product_sql);

// Bind parameters for products
$types = str_repeat('i', count($cat_id_array));
$params = array_merge([$product_stmt, $types], $cat_id_array);
$refs = [];
foreach($params as $key => $value) {
    $refs[$key] = &$params[$key];
}
call_user_func_array('mysqli_stmt_bind_param', $refs);

mysqli_stmt_execute($product_stmt);
$product_result = mysqli_stmt_get_result($product_stmt);

// Get total products count for pagination
$count_sql = "SELECT COUNT(*) as total FROM product WHERE category_id IN ($placeholders)";
$count_stmt = mysqli_prepare($con, $count_sql);

// Bind parameters for count
$types = str_repeat('i', count($cat_id_array));
$params = array_merge([$count_stmt, $types], $cat_id_array);
$refs = [];
foreach($params as $key => $value) {
    $refs[$key] = &$params[$key];
}
call_user_func_array('mysqli_stmt_bind_param', $refs);

mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$total_row = mysqli_fetch_assoc($count_result);
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category_name); ?> - EcoStore</title>
    <link rel="stylesheet" href="mstyle.css">
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

    <div class="small-container">
        <div class="row row-2">
            <h2>Skin Care</h2>
            <select onchange="location = this.value;">
                <option value="?cat_ids=<?php echo $cat_ids; ?>&sort=default" <?php echo $sort == 'default' ? 'selected' : ''; ?>>Default Sorting</option>
                <option value="?cat_ids=<?php echo $cat_ids; ?>&sort=price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Sort by price: high to low</option>
                <option value="?cat_ids=<?php echo $cat_ids; ?>&sort=price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Sort by price: low to high</option>
                <option value="?cat_ids=<?php echo $cat_ids; ?>&sort=rating" <?php echo $sort == 'rating' ? 'selected' : ''; ?>>Sort by rating</option>
                <option value="?cat_ids=<?php echo $cat_ids; ?>&sort=sales" <?php echo $sort == 'sales' ? 'selected' : ''; ?>>Sort by sales</option>
            </select>
        </div>
        
        <div class="row">
            <?php if (mysqli_num_rows($product_result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($product_result)): ?>
                    <div class="col-4">
                        <a href="product_details.php?id=<?php echo $product['product_id']; ?>">
                            <img src="http://localhost/miniproject/shop/uploads/<?php echo htmlspecialchars($product['image']); ?>">
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        </a>
                        <div class="rating">
                            <?php 
                            $fullStars = floor($product['rating']);
                            $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                            for($i = 0; $i < $fullStars; $i++) {
                                echo '<i class="fa fa-star"></i>';
                            }
                            if($halfStar) {
                                echo '<i class="fas fa-star-half-alt"></i>';
                            }
                            for($i = 0; $i < $emptyStars; $i++) {
                                echo '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <p>&#8377;<?php echo number_format($product['price'], 2); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; width: 100%; padding: 50px;">
                    <p style="color: red; font-size: 18px;">No products found in this category.</p>
                </div>
            <?php endif; ?>
        </div> 
        
        <?php if (mysqli_num_rows($product_result) > 0 && $total_pages > 1): ?>
            <div class="page-btn">
                <?php if ($page > 1): ?>
                    <span><a href="?cat_ids=<?php echo $cat_ids; ?>&page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>">&#8592;</a></span>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <span class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <a href="?cat_ids=<?php echo $cat_ids; ?>&page=<?php echo $i; ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
                    </span>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <span><a href="?cat_ids=<?php echo $cat_ids; ?>&page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>">&#8594;</a></span>
                <?php endif; ?>
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