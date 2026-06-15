<?php
session_start();
include 'adminheader.php';
$con = mysqli_connect("localhost","root","","ecostore");
if (!$con) { die("DB Error"); }

$result = $con->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$message_count = $result->num_rows; // Count total messages
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Messages</title>
    <style>
        .page-content {
            max-width: 1200px;
            margin: 30px auto;
            background: rgba(255,255,255,0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        
        h2 {
            text-align: center;
            color: #c47474ff;
            margin-bottom: 20px;
        }
        
        .message-count {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            font-size: 14px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: #c47474ff;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .message-cell {
            max-width: 300px;
            word-wrap: break-word;
        }
        
        .subject-cell {
            max-width: 200px;
            word-wrap: break-word;
        }
        
        .no-messages {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 8px;
            }
            
            .message-cell {
                max-width: 150px;
            }
            
            .subject-cell {
                max-width: 100px;
            }
        }
    </style>
</head>
<body>
    <div class="page-content">
        <h2>Contact Messages</h2>
        
        <div class="message-count">
            Total Messages: <?php echo $message_count; ?>
        </div>
        
        <?php if ($message_count > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th class="subject-cell">Subject</th>
                        <th class="message-cell">Message</th>
                        <th>Sent At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td class="subject-cell"><?= htmlspecialchars($row['subject']) ?></td>
                        <td class="message-cell"><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-messages">
                <p>No contact messages found.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>