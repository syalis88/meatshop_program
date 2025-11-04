<?php
session_start();
require_once('./includes/auth_admin.php');
require_once "../classes/order.php";

$orderObj = new Order();
$counts = $orderObj->getOrderCounts();

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
        <a href="orders.php" class="btn">View Orders</a>
        <a href="../account/logout.php">Logout</a>
    </nav>
</header>

<main>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['full_name']); ?>!</h2>
    <p>You are logged in as <strong>Admin</strong>.</p>

    <!-- Summary Boxes -->
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
        <h4>Completed Orders</h4>
        <p class="value"><?= $counts['completed'] ?? 0; ?></p>
    </div>
    </div>

    <!-- Action Buttons -->
    <div class="cards-container">

        <div class="card">
            <h3>View Orders</h3>
            <p>Check all customer orders.</p>
            <a href="orders.php" class="btn">Go</a>
        </div>

    </div>
</main>

</body>
</html>
