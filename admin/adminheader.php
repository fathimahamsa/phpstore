<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EcoStore</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: url("../image/admin.jpg") no-repeat center center fixed;
    background-size: cover;
}
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 50px;
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(8px);
    position: sticky;
    top: 0;
    z-index: 1000;
}
.navbar .logo { font-size: 24px; font-weight: bold; color: #c47474ff; }
.navbar ul { list-style: none; display: flex; margin: 0; padding: 0; }
.navbar ul li { margin-left: 25px; }
.navbar ul li a { text-decoration: none; color: #c47474ff; font-size: 18px; transition: color 0.3s; }
.navbar ul li a:hover { color: #5e3c25ff; }
.page-content {
    max-width: 1200px;
    margin: 30px auto;
    background: rgba(255,255,255,0.9);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
}
</style>
</head>
<body>
<nav class="navbar">
  <div class="logo">EcoStore</div>
  <ul>
     <li><a href="admin_users.php">Manage Users</a></li>
     <li><a href="admin_orders.php">Orders</a></li>
    <li><a href="admin_category.php">Manage Catalogs</a></li>
     <li><a href="view_contact.php">View Contacts</a></li>
   
    <!-- <li><a href="addproduct.php">Add Product</a></li>
    <li><a href="viewproduct.php">View Product</a></li> -->
    <li><a href="adminlogout.php" >Logout</a></li>
  </ul>
</nav>
