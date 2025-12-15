<?php

require_once "includes/auth_admin.php";
require_once "../classes/order.php";

$orderObj = new Order();
$orders = $orderObj->getAllOrders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../assets/ordersstyle.css">
</head>
<body>

<header>
  <h1>Meat Shop Admin Panel</h1>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="orders.php">View Orders</a>
    <a href="sales_report.php">Sales Report</a>
    <a href="notifications.php">Notifications</a>
    
    <?php include './includes/notification_bell.php'; ?>
    
    <a href="../account/logout.php">Logout</a>
  </nav>
</header>

<main>
<div class="container">

    <div class="header-bar">
        <h1>Order Management</h1>

        <div class="top-buttons">
            <a href="dashboard.php" class="back-btn">Back</a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Delivery Date</th>
                    <th>Total (₱)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php 
                        // Determine status class
                        if ($order['status'] == 'Pending') {
                            $orderstatus = 'pending';
                        } elseif ($order['status'] == 'Processing') {
                            $orderstatus = 'processing';
                        } elseif ($order['status'] == 'Completed') {
                            $orderstatus = 'completed';
                        } elseif ($order['status'] == 'Cancelled') {
                            $orderstatus = 'cancelled';
                        } else {
                            $orderstatus = '';
                        }
                        ?>
                        <tr>
                            <td><?= $order['order_id'] ?></td>
                            <td><?= htmlspecialchars($order['FirstName'] . ' ' . $order['LastName']); ?></td>
                            <td><?= htmlspecialchars($order['address'] ?? ''); ?></td>
                            <td><?= $order['delivery_date'] ?></td>
                            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="status-box <?= $orderstatus ?>">
                                    <?= $order['status'] ?>
                                </span>
                            </td>
                            <td>
                                <a class="view-btn" href="vieworders.php?id=<?= $order['order_id'] ?>">View</a>
                                <a class="update-btn" href="updateorder.php?id=<?= $order['order_id'] ?>">Update</a>
                                <a class="print-btn" href="print_order.php?id=<?= $order['order_id'] ?>" target="_blank">Print</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-msg">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</main>
</body>
</html>
