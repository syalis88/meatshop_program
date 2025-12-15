<?php

session_start();
require_once('./includes/auth_admin.php');
require_once "../classes/notification.php";

$notificationObj = new Notification();

// Mark as read if requested
if (isset($_GET['read']) && $_GET['read'] == 'all') {
    $notificationObj->markAllAsRead();
    header("Location: notifications.php");
    exit;
}

if (isset($_GET['read_id'])) {
    $notificationObj->markAsRead($_GET['read_id']);
    header("Location: notifications.php");
    exit;
}

$notifications = $notificationObj->getAllNotifications(null, 50);
$unreadCount = $notificationObj->getUnreadCount();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Admin Panel</title>
    <link rel="stylesheet" href="../assets/admindashboard.css">
    <link rel="stylesheet" href="../assets/notifications.css">
</head>
<body>

<header>
  <h1>Meat Shop Admin Panel</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="orders.php">View Orders</a>
    <a href="sales_report.php">Sales Report</a>
    <a href="notifications.php">Notifications (<?= $unreadCount ?>)</a>
    <?php include './includes/notification_bell.php'; ?>
    <a href="../account/logout.php">Logout</a>
  </nav>
</header>

<main>
    <div class="notifications-container">
        
        <div class="notifications-header">
            <h2>System Notifications</h2>
            <?php if ($unreadCount > 0): ?>
                <a href="?read=all" class="btn-mark-all">✓ Mark All as Read</a>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($notifications)): ?>
            <div class="notifications-list">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-card <?= $notif['is_read'] == 0 ? 'unread' : 'read' ?>">
                        
                        <?php if ($notif['is_read'] == 0): ?>
                            <span class="unread-badge"></span>
                        <?php endif; ?>
                        
                        <div class="notification-header">
                            <div class="notification-left">
                                <span class="notification-type <?= htmlspecialchars($notif['type']) ?>">
                                    <?= htmlspecialchars($notif['type']) ?>
                                </span>
                                <div class="notification-title">
                                    <?= htmlspecialchars($notif['title']) ?>
                                </div>
                            </div>
                            <div class="notification-right">
                                <div class="notification-time">
                                    <?= date('M j, Y g:i A', strtotime($notif['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="notification-message">
                            <?= htmlspecialchars($notif['message']) ?>
                        </div>
                        
                        <div class="notification-actions">
                            <?php if (!empty($notif['link'])): ?>
                                <a href="<?= htmlspecialchars($notif['link']) ?>" class="notification-link">
                                    View Details
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($notif['is_read'] == 0): ?>
                                <a href="?read_id=<?= $notif['notification_id'] ?>" class="btn-mark-read">
                                    Mark as read
                                </a>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-notifications">
                <div class="no-notifications-icon">🔔</div>
                <p>No notifications yet. You're all caught up!</p>
            </div>
        <?php endif; ?>
        
    </div>
</main>

</body>
</html>