<?php
// =============================================================================
// FILE 1: admin_panel/includes/notification_bell.php
// Create this new file
// =============================================================================

require_once "../classes/notification.php";
$notifObj = new Notification();
$unreadCount = $notifObj->getUnreadCount();
?>

<style>
.notification-bell {
    bottom: 4px;
    position: relative;
    display: inline-block;
    margin-left: 1px;
}

.notification-bell a {
    color: white;
    font-size: 20px;
    text-decoration: none;
    position: relative;
    padding: 5px 10px;
}

.notification-badge {
    position: absolute;
    top: -6px;
    right: -8px;
    background: #f44336;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.notification-bell:hover a {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
}
</style>

<div class="notification-bell">
    <a href="notifications.php" title="Notifications">
        🔔
        <?php if ($unreadCount > 0): ?>
            <span class="notification-badge"><?= $unreadCount ?></span>
        <?php endif; ?>
    </a>
</div>