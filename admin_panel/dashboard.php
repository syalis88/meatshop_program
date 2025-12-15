<?php
session_start();
require_once('./includes/auth_admin.php');
require_once "../classes/order.php";

$orderObj = new Order();
$counts = $orderObj->getOrderCounts();
$kpis = $orderObj->getKPIData();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/admindashboard.css">
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
<div class="top-section">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['FirstName'] . ' ' . $_SESSION['user']['LastName']) ?></h2>
    <p>You are logged in as <strong>Admin</strong>.</p>

    <h3>Key Performance Indicators</h3>
    <div class="kpi-container">
        <div class="kpi-box kpi-primary">
            <h4>Total Orders</h4>
            <p class="kpi-value"><?= number_format($kpis['total_orders']); ?></p>
            <span class="kpi-label">All time</span>
        </div>
        
        <div class="kpi-box kpi-success">
            <h4>Total Sales</h4>
            <p class="kpi-value">₱<?= number_format($kpis['total_sales'], 2); ?></p>
            <span class="kpi-label">Revenue earned</span>
        </div>
        
        <div class="kpi-box kpi-info">
            <h4>Total Customers</h4>
            <p class="kpi-value"><?= number_format($kpis['total_customers']); ?></p>
            <span class="kpi-label">Unique buyers</span>
        </div>
        
        <div class="kpi-box kpi-warning">
            <h4>Products Sold</h4>
            <p class="kpi-value"><?= number_format($kpis['products_sold']); ?></p>
            <span class="kpi-label">Total products</span>
        </div>

        <div class="kpi-box kpi-danger">
            <h4>Pending Deliveries</h4>
            <p class="kpi-value"><?= number_format($kpis['pending_deliveries']); ?></p>
            <span class="kpi-label">Needs attention</span>
        </div>
    </div>

    <h3>Today's Summary</h3>
    <div class="summary-container">
        <div class="summary-box">
            <h4>Orders Today</h4>
            <p class="value"><?= $counts['today'] ?? 0; ?></p>
        </div>
        <div class="summary-box">
            <h4>Pending Orders</h4>
            <p class="value"><?= $counts['pending'] ?? 0; ?></p>
        </div>
        <div class="summary-box">
            <h4>Processing</h4>
            <p class="value"><?= $counts['processing'] ?? 0; ?></p>
        </div>
        <div class="summary-box">
            <h4>Completed Orders</h4>
            <p class="value"><?= $counts['completed'] ?? 0; ?></p>
        </div>
    </div>
</div>

<div class="cards-container">
    <div class="card">
        <h3>View Orders</h3>
        <p>Manage all customer orders.</p>
        <a href="orders.php" class="btn">Go to Orders</a>
    </div>
    
    <div class="card">
        <h3>Sales Report</h3>
        <p>View detailed sales analytics.</p>
        <a href="sales_report.php" class="btn">View Report</a>
    </div>
</div>

</main>

</body>
</html>
