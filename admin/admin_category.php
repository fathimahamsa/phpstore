<?php
session_start();
include 'adminheader.php';
$con = mysqli_connect("localhost","root","","ecostore");
if (!$con) { die("DB Error"); }

// Add Category
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $stmt = $con->prepare("INSERT INTO admin_categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    header("Location:   http://localhost/miniproject/admin/admin_category.php");
    exit();
}

// Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $con->prepare("DELETE FROM admin_categories WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_category.php");
    exit();
}

// Edit Category
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $stmt = $con->prepare("UPDATE admin_categories SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    header("Location: admin_category.php");
    exit();
}

// Fetch Categories
$result = $con->query("SELECT * FROM admin_categories ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Category Management</title>
    <style>
        table { border-collapse: collapse; width: 50%; margin-top: 20px;}
        th, td { border: 1px solid #000; padding: 8px; text-align: left;}
        form { margin-bottom: 20px; }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 700px;
            margin: 50px auto;
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
        form {
            margin-bottom: 30px;
        }
        form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            box-sizing: border-box;
        }
        form button {
            padding: 10px 20px;
            background-color: #c47474ff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        form button:hover {
            background-color: #5e3c25ff;
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 10px; 
            text-align: left; 
        }
        th { 
            background-color: #c47474ff; 
            color: white; 
        }
        tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        a {
            color: #c47474ff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn {
    padding: 5px 12px;
    border-radius: 5px;
    text-decoration: none;
    color: #fff;
    margin-right: 5px;
    font-size: 14px;
    display: inline-block;
}

.edit-btn {
    background-color: #c47474ff;
}

.edit-btn:hover {
    background-color: #c47474ff;
}

.delete-btn {
    background-color: #c47474ff;
}

.delete-btn:hover {
    background-color: #c47474ff;
}
    </style>
</head>
<body>
   <div class="container">
    <h2>Admin Category Management</h2>

    <!-- Add/Edit Form -->
    <?php if (isset($_GET['edit'])):
        $id = $_GET['edit'];
        $edit_result = $con->query("SELECT * FROM admin_categories WHERE id=$id");
        $edit_row = $edit_result->fetch_assoc();
    ?>
        <form method="post">
            <input type="hidden" name="id" value="<?= $edit_row['id'] ?>">
            <input type="text" name="name" value="<?= $edit_row['name'] ?>" required>
            <button type="submit" name="update">Update </button>
        </form>
    <?php else: ?>
        <form method="post">
            <input type="text" name="name" placeholder="Category Name" required>
            <button type="submit" name="add">Add </button>
        </form>
    <?php endif; ?>

    <!-- Category Table -->
    <table>
        <tr>
            <th>SI No</th>
            <!-- <th>ID</th> -->
            <th>Name</th>
            <th>Actions</th>
        </tr>
        <?php 
        $count=1;
        while($row = $result->fetch_assoc()): ?>
            <tr>
                 <td><?php echo $count++;?></td> 
                <!-- <td><?= $row['id'] ?></td> -->
                <td><?= $row['name'] ?></td>
                <td>
                    <a href=" http://localhost/miniproject/admin/admin_category.php?edit=<?= $row['id'] ?>" class="btn edit-btn">Edit</a> 
                    <a href=" http://localhost/miniproject/admin/admin_category.php?delete=<?= $row['id'] ?>" class="btn delete-btn" onclick="return confirm('Are you sure?')" >Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </div>
    </table>
</body>
</html>
