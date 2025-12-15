<?php

session_start();
require_once('./includes/auth_admin.php');
require_once "../classes/order.php";

$orderObj = new Order();

// Get order ID from URL
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    die("Invalid order ID");
}

$order = $orderObj->getOrderById($orderId);
$items = $orderObj->getOrderItems($orderId);

if (!$order) {
    die("Order not found");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order['order_id'] ?> - Receipt</title>
    <link rel="stylesheet" href="../assets/printorder.css">
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="print-button">Print Receipt</button>
    <button onclick="window.close()" class="print-button" style="background: #95a5a6;">Close</button>
</div>

<div class="receipt-header">
    <h1>MEAT SHOP ONLINE</h1>
    <p>Order Receipt</p>
</div>

<div class="receipt-info">
    <div class="info-row">
        <strong>Order Number:</strong>
        <span>#<?= $order['order_id'] ?></span>
    </div>
    
    <div class="info-row">
        <strong>Order Date:</strong>
        <span><?= date('F j, Y - g:i A', strtotime($order['order_date'])) ?></span>
    </div>
    
    <div class="info-row">
        <strong>Status:</strong>
        <span class="status-badge status-<?= ($order['status'] == 'Pending') ? 'pending' : (($order['status'] == 'Processing') ? 'processing' : (($order['status'] == 'Completed') ? 'completed' : 'cancelled')) ?>">
            <?= htmlspecialchars($order['status']) ?>
        </span>
    </div>
</div>

<div class="receipt-info">
    <h3>Customer Information</h3>
    <div class="info-row">
        <strong>Name:</strong>
        <span><?= htmlspecialchars($order['FirstName'] . ' ' . $order['LastName']) ?></span>
    </div>
    
    <div class="info-row">
        <strong>Email:</strong>
        <span><?= htmlspecialchars($order['email']) ?></span>
    </div>
    
    <div class="info-row">
        <strong>Phone:</strong>
        <span><?= htmlspecialchars($order['customer_phone']) ?></span>
    </div>
    
    <div class="info-row">
        <strong>Address:</strong>
        <span><?= htmlspecialchars($order['address']) ?></span>
    </div>
    
    <?php if (!empty($order['delivery_date'])): ?>
    <div class="info-row">
        <strong>Delivery Date:</strong>
        <span><?= date('F j, Y', strtotime($order['delivery_date'])) ?></span>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($order['delivery_time'])): ?>
    <div class="info-row">
        <strong>Delivery Time:</strong>
        <span><?= htmlspecialchars($order['delivery_time']) ?></span>
    </div>
    <?php endif; ?>
</div>

<h3>Order Items</h3>
<table class="items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Kilos</th>
            <th>Price/kg</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $calculated_subtotal = 0;
        foreach ($items as $item): 
            $item_total = $item['kilo'] * $item['price'];
            $calculated_subtotal += $item_total;
        ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= number_format($item['kilo'], 2) ?> kg</td>
                <td>₱<?= number_format($item['price'], 2) ?></td>
                <td>₱<?= number_format($item_total, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if (!empty($order['notes'])): ?>
<div class="receipt-info">
    <strong>Special Instructions:</strong>
    <p><?= htmlspecialchars($order['notes']) ?></p>
</div>
<?php endif; ?>

<div class="total-section">
    <p>Subtotal: ₱<?= number_format($order['subtotal'], 2) ?></p>
    <p class="total-amount">Total: ₱<?= number_format($order['total_amount'], 2) ?></p>
</div>

<div style="text-align: center; margin-top: 40px; color: #999; font-size: 12px;">
    <p>Thank you for your order!</p>
    <p>For questions, contact us at meatshoponline@gmail.com</p>
</div>

</body>
</html>