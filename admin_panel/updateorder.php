<?php
require_once "includes/auth_admin.php";
require_once "../classes/order.php";

$orderObj = new Order();

// Redirect if ID is missing
if (!isset($_GET['id'])) {
    header("Location: vieworders.php");
    exit();
}

$order_id = $_GET['id'];

// Fetch order
$order = $orderObj->getOrderById($order_id);
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_status = $_POST["status"];

    if ($orderObj->updateOrderStatus($order_id, $new_status)) {
        $message = "Order status updated successfully!";
        $order = $orderObj->getOrderById($order_id); // Refresh order details
    } else {
        $message = "Failed to update order status.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Order Status</title>
    <link rel="stylesheet" href="../assets/updateorderstyle.css">
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
    <h1>Update Order #<?= $order['order_id'] ?></h1>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" class="update-form">
        <label>Status</label>
        <select name="status">
            <option <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option <?= $order['status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
            <option <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
            <option <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <button type="submit" class="update-btn">Update</button>
    </form>

</div>
</main>
</body>
</html>
