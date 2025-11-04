<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

<div class="container">

    <div class="header-bar">
        <h1>Order Management</h1>

        <div class="top-buttons">
            <a href="dashboard.php" class="back-btn">Back</a>
            <a href="../account/logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- ✅ Table wrapper ensures table doesn’t overflow -->
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
                        <?php $orderstatus = strtolower($order['status']); ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($order['customer_address'] ?? '') ?></td>
                            <td><?= $order['delivery_date'] ?></td>
                            <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <span class="status-box <?= $orderstatus ?>">
                                    <?= $order['status'] ?>
                                </span>
                            </td>
                            <td>
                                <a class="view-btn" href="vieworders.php?id=<?= $order['id'] ?>">View</a>
                                <a class="update-btn" href="updateorder.php?id=<?= $order['id'] ?>">Update</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>


