<?php
require_once "includes/auth_admin.php";
require_once "../classes/order.php";

if (!isset($_GET['id'])) {
    header('Location: vieworders.php'); // FIXED
    exit();
}

$orderObj = new Order();
$order = $orderObj->getOrderById($_GET['id']);
$items = $orderObj->getOrderItems($_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="../assets/viewordersstyle.css">
</head>
<body>

<div class="container">

    <a href="vieworders.php" class="back-btn">← Back</a> <!-- FIXED -->
    <h1>Order #<?= $order['id'] ?></h1>

    <div class="order-info">
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['customer_address']) ?></p>
        
        <?php if (!empty($order['notes'])): ?>
            <p><strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?></p>
        <?php endif; ?>

        <p><strong>Delivery Date:</strong> <?= htmlspecialchars($order['delivery_date']) ?></p>

        <p><strong>Status:</strong> 
            <span class="status-badge <?= strtolower($order['status']) ?>">
                <?= htmlspecialchars($order['status']) ?>
            </span>
        </p>
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
            <td><?= $item['quantity'] ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
        </tr>
        <?php endforeach ?>
    </table>

    <h3 class="total">Total: ₱<?= number_format($order['total_amount'], 2) ?></h3>

</div>
</body>
</html>
