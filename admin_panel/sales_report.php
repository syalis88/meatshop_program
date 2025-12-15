<?php
session_start();
require_once('./includes/auth_admin.php');
require_once "../classes/order.php";

$orderObj = new Order();

// Get filter dates
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : "";
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : "";

// Get data
$sales_report = $orderObj->getSalesReport($start_date, $end_date);
$best_sellers = $orderObj->getBestSellingProducts(10);
$kpis = $orderObj->getKPIData();

// Calculate totals for filtered period
$total_filtered_sales = 0;
$total_filtered_orders = 0;

foreach ($sales_report as $row) {
    $total_filtered_sales += floatval($row['daily_sales']);
    $total_filtered_orders += intval($row['total_orders']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <link rel="stylesheet" href="../assets/salesreport.css">
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
    <div class="report-header">
        <h2>Sales Report & Analytics</h2>
        
        <form class="filter-form" method="get" action="">
            <div class="filter-group">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?= $start_date ?>">
            </div>
            
            <div class="filter-group">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?= $end_date ?>">
            </div>
            
            <button type="submit" class="btn">Filter</button>
            <a href="sales_report.php" class="btn btn-secondary">Clear</a>
            <button type="button" onclick="window.print()" class="btn btn-print">Print Report</button>
        </form>
    </div>

    <div class="report-summary">
        <h3>Summary <?= (!empty($start_date) || !empty($end_date)) ? "(Filtered Period)" : "(All Time)" ?></h3>
        <div class="summary-grid">

            <div class="summary-item">
                <span class="summary-label">Total Orders:</span>
                <span class="summary-value">
                    <?= number_format(($total_filtered_orders > 0) ? $total_filtered_orders : $kpis['total_orders']) ?>
                </span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Total Sales:</span>
                <span class="summary-value">
                    ₱<?= number_format(($total_filtered_sales > 0) ? $total_filtered_sales : $kpis['total_sales'], 2) ?>
                </span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Products Sold:</span>
                <span class="summary-value">
                    <?= number_format($kpis['products_sold']) ?>
                </span>
            </div>

        </div>
    </div>

    <div class="report-section">
        <h3>Daily Sales Report</h3>
        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Orders</th>
                        <th>Completed Orders</th>
                        <th>Daily Sales</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (!empty($sales_report)): ?>
                        <?php foreach ($sales_report as $row): ?>
                            <tr>
                                <td><?= date('F j, Y', strtotime($row['order_date'])); ?></td>
                                <td><?= $row['total_orders']; ?></td>
                                <td><?= $row['completed_orders']; ?></td>
                                <td>₱<?= number_format($row['daily_sales'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">No sales data found for this period.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <div class="report-section">
        <h3>Best Selling Products</h3>
        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Product Name</th>
                        <th>Units Sold (KG)</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (!empty($best_sellers)): ?>
                        <?php $rank = 1; ?>
                        <?php foreach ($best_sellers as $product): ?>
                            <tr>
                                <td><?= $rank++; ?></td>
                                <td><?= htmlspecialchars($product['product_name']); ?></td>
                                <td><?= number_format($product['total_sold'], 2); ?></td>
                                <td>₱<?= number_format($product['total_revenue'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">No product sales data available.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

</main>

</body>
</html>
