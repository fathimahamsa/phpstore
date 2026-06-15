<?php
session_start();
include 'adminheader.php';
$con = mysqli_connect("localhost","root","","ecostore");
if (!$con) die("DB Error");

if (!isset($_SESSION['role']) || $_SESSION['role'] != "Admin") {
    header("Location:  http://localhost/miniproject/buyer/login.php"); exit();
}

$result = mysqli_query($con, "SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Registered Users</title>
    <style>
        /* Page content wrapper */
        .page-content {
            max-width: 1000px;
            margin: 50px auto;
            background: rgba(255,255,255,0.95);
            padding: 25px 20px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            color: #c47474ff;
            margin-bottom: 20px;
        }

        /* Table styling */
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 0 auto;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        th {
            background: #f4f4f4;
            color: #333;
        }

        /* Status badges */
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            color: #fff;
        }
        .active { background-color: #c47474ff; }
        .restricted { background-color: #c47474ff;; }

        /* Buttons */
        .action-btn {
            display: inline-block;
            padding: 7px 14px;
            font-size: 14px;
            text-decoration: none;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
            margin: 2px;
        }

        .restrict-btn {
            background-color: #c47474ff;
        }
        .restrict-btn:hover {
            background-color: #c47474ff;
        }

        .unrestrict-btn {
            background-color: #c47474ff;
        }
        .unrestrict-btn:hover {
            background-color: #c47474ff;
        }

        /* Responsive table for small screens */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            tr {
                margin-bottom: 15px;
            }
            td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 45%;
                padding-left: 10px;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="page-content">
        <h2>All Registered Users</h2>
        <table>
            <tr>
                <th>SI No</th>
                <th>User ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            $count=1;
            while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $count++;?></td>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['role']; ?></td>
                <td>
                    <?php if ($row['is_restricted']==1) { ?>
                        <span class="status restricted">Restricted</span>
                    <?php } else { ?>
                        <span class="status active">Active</span>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($row['is_restricted']==0) { ?>
                        <a href="http://localhost/miniproject/miniproject/admin/restricted_user.php?id=<?php echo $row['id']; ?>" class="action-btn restrict-btn">Restrict</a>
                    <?php } else { ?>
                        <a href="unrestrict_user.php?id=<?php echo $row['id']; ?>" class="action-btn unrestrict-btn">Unrestrict</a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
