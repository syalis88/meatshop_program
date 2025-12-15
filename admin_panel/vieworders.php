<?php
require_once "includes/auth_admin.php";
require_once "../classes/order.php";

if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

$orderObj = new Order();

// Fetch order + items
$order = $orderObj->getOrderById($_GET['id']);
$items = $orderObj->getOrderItems($_GET['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="../assets/adminview.css">
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

    <a href="orders.php" class="back-btn">Back</a>
    <h1>Order #<?= $order['order_id'] ?></h1>

    <div class="order-info">

        <p><strong>Customer:</strong> <?= htmlspecialchars($order['FirstName'] . ' ' . $order['LastName']) ?></p>

        <p><strong>Phone:</strong> 
            <?= htmlspecialchars($order['customer_phone']) ?>
        </p>

        <?php if (!empty($order['notes'])): ?>
            <p><strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?></p>
        <?php endif; ?>

        <p><strong>Delivery Date:</strong> <?= htmlspecialchars($order['delivery_date']) ?></p>
        <p><strong>Delivery Time:</strong> <?= htmlspecialchars($order['delivery_time']) ?></p>

        <p><strong>Status:</strong> 
            <span class="status-badge <?= strtolower($order['status']) ?>">
                <?= htmlspecialchars($order['status']) ?>
            </span>
        </p>

        <p><strong>Order Created:</strong> <?= htmlspecialchars($order['order_date']) ?></p>

    </div>

    <h3>Items Ordered</h3>

    <table class="order-table">
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>

        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= $item['kilo'] ?> kg</td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($item['kilo'] * $item['price'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3 class="total">Total: ₱<?= number_format($order['total_amount'], 2) ?></h3>

</div>
</main>
</body>
</html>
