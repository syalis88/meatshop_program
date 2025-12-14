<?php
session_start();
require_once "../classes/notification.php";

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$notificationObj = new Notification();
$user_id = $_SESSION['user']['user_id'];

// Mark as read if clicked
if (isset($_GET['read'])) {
    $notificationObj->markAsRead($_GET['read']);
    header('Location: user_notifications.php');
    exit();
}

// Mark all as read
if (isset($_GET['mark_all'])) {
    $notificationObj->markAllAsRead($user_id);
    header('Location: user_notifications.php');
    exit();
}

$notifications = $notificationObj->getAllNotifications($user_id, 50);
$unread_count = $notificationObj->getUnreadCount($user_id);

// Count cart
$cart_count = 0;
if (isset($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $cart_count += $item["kilo"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        
        /* Navbar - Matching your site's maroon theme */
        header {
            background: linear-gradient(135deg, #8B0000 0%, #B22222 100%);
            padding: 20px 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        header h1 {
            color: white;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        nav {
            display: flex;
            gap: 0;
            align-items: center;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 16px;
        }
        
        nav a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* Container */
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }
        
        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: white;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #8B0000;
        }
        
        .header-section h2 {
            color: #8B0000;
            font-size: 26px;
            font-weight: bold;
        }
        
        /* Mark All Button - Matching maroon theme */
        .mark-all-btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, #8B0000 0%, #B22222 100%);
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(139, 0, 0, 0.3);
        }
        
        .mark-all-btn:hover {
            background: linear-gradient(135deg, #B22222 0%, #8B0000 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(139, 0, 0, 0.4);
        }
        
        /* Notification Cards */
        .notification {
            padding: 20px;
            margin: 15px 0;
            border-left: 5px solid #ddd;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }
        
        .notification:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(5px);
        }
        
        .notification.unread {
            background: linear-gradient(to right, #fff5f5 0%, #ffffff 100%);
            border-left-color: #8B0000;
            box-shadow: 0 2px 8px rgba(139, 0, 0, 0.15);
        }
        
        .notification-title {
            font-size: 18px;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .notification.unread .notification-title {
            color: #8B0000;
        }
        
        .notification-message {
            color: #555;
            margin-bottom: 10px;
            line-height: 1.6;
            font-size: 15px;
        }
        
        .notification-time {
            font-size: 13px;
            color: #999;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .notification-time::before {
            content: "🕒";
        }
        
        .notification a {
            color: #8B0000;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }
        
        .notification a:hover {
            color: #B22222;
            gap: 8px;
        }
        
        .notification a::after {
            content: "→";
        }
        
        /* No Notifications State */
        .no-notifications {
            text-align: center;
            padding: 60px 40px;
            background: white;
            border-radius: 8px;
            color: #999;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }
        
        .no-notifications p {
            font-size: 18px;
            margin-top: 10px;
        }
        
        /* Unread Badge Indicator */
        .unread-badge {
            display: inline-block;
            background: #8B0000;
            color: white;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
            margin-right: 8px;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
            }
            
            header h1 {
                font-size: 22px;
            }
            
            nav {
                flex-wrap: wrap;
                gap: 5px;
            }
            
            nav a {
                padding: 8px 12px;
                font-size: 14px;
            }
            
            .header-section {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .mark-all-btn {
                width: 100%;
                text-align: center;
            }
            
            .notification {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<header class="navbar">
    <h1>Meat Shop Online</h1>
    <nav>
        <a href="../productitems_index/viewproducts.php">Shop</a>
        <a href="../cart/cart.php">Cart (<?= $cart_count ?>)</a>
        <a href="user_notifications.php">Notifications (<?= $unread_count ?>)</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <div class="header-section">
        <h2>My Notifications</h2>
        <?php if ($unread_count > 0): ?>
            <a href="?mark_all=1" class="mark-all-btn">Mark All as Read</a>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="no-notifications">
            <p>📭 No notifications yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $notif): ?>
            <div class="notification <?= $notif['is_read'] ? '' : 'unread' ?>">
                <div class="notification-title">
                    <?php if (!$notif['is_read']): ?>
                        🔵 
                    <?php endif; ?>
                    <?= htmlspecialchars($notif['title']) ?>
                </div>
                <div class="notification-message">
                    <?= htmlspecialchars($notif['message']) ?>
                </div>
                <div class="notification-time">
                    <?= date('M d, Y h:i A', strtotime($notif['created_at'])) ?>
                </div>
                <?php if (!empty($notif['link']) && $notif['order_id']): ?>
                    <a href="order_details.php?id=<?= $notif['order_id'] ?>&read=<?= $notif['notification_id'] ?>">
                        View Order Details →
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>