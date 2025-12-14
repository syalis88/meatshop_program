<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once "../classes/order.php";
$orderObj = new Order();

$userId = $_SESSION['user']['user_id'];

// Get cart count
$cart_count = 0;
if (isset($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $cart_count += $item["kilo"];
    }
}

// Get user orders
$orders = $orderObj->getUserOrders($userId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../assets/profile.css">
    <style>
        .edit-profile-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .edit-profile-btn:hover {
            background-color: #0056b3;
        }
        .warning-message {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 12px;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<header>
    <h1>Meat Shop Online</h1>
    <nav>
        <a href="../productitems_index/viewproducts.php">Home</a>
        <a href="../cart/cart.php">Cart(<?= $cart_count ?>)</a>
        <?php include '../includes/usernotif.php'; ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <h2>My Profile</h2>
    
    <div class="profile-info">
        <h3>Account Information</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user']['FirstName'] . ' ' . $_SESSION['user']['LastName']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']); ?></p>
        
        <!-- UPDATED: Display phone from session -->
        <?php if (!empty($_SESSION['user']['phone'])): ?>
            <p><strong>Phone:</strong> <?= htmlspecialchars($_SESSION['user']['phone']); ?></p>
        <?php else: ?>
            <p><strong>Phone:</strong> <span style="color: #999;">Not provided</span></p>
            <div class="warning-message">
                ⚠️ You haven't added a phone number yet. Please add one to place orders.
            </div>
        <?php endif; ?>
        
        <!-- UPDATED: Display address from session -->
        <?php if (!empty($_SESSION['user']['address'])): ?>
            <p><strong>Address:</strong> <?= htmlspecialchars($_SESSION['user']['address']); ?></p>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user']['is_verified']) && $_SESSION['user']['is_verified']): ?>
            <p><strong>Email Verified:</strong> <span style="color: green;">✓ Verified</span></p>
        <?php else: ?>
            <p><strong>Email Verified:</strong> <span style="color: orange;">⚠ Not Verified</span></p>
        <?php endif; ?>
        
        <!-- Optional: Add edit profile button -->
        <a href="edit_profile.php" class="edit-profile-btn">Edit Profile</a>
    </div>

    <div class="orders-section">
        <h3>My Orders</h3>
        
        <?php if (!empty($orders)): ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h4>Order #<?= $order['order_id'] ?></h4>
                            <span class="order-status status-<?= ($order['status'] == 'Pending') ? 'pending' : (($order['status'] == 'Processing') ? 'processing' : (($order['status'] == 'Completed') ? 'completed' : 'cancelled')) ?>">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </div>
                        
                        <div class="order-details">
                            <p><strong>Order Date:</strong> <?= date('F j, Y', strtotime($order['order_date'])) ?></p>
                            <p><strong>Subtotal:</strong> ₱<?= number_format($order['subtotal'], 2) ?></p>
                            <p><strong>Total Amount:</strong> ₱<?= number_format($order['total_amount'], 2) ?></p>
                            
                            <!-- REMOVED: customer_phone from orders table - phone is now in user session -->
                            
                            <?php if (!empty($order['delivery_date'])): ?>
                                <p><strong>Delivery Date:</strong> <?= date('F j, Y', strtotime($order['delivery_date'])) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($order['delivery_time'])): ?>
                                <p><strong>Delivery Time:</strong> <?= htmlspecialchars($order['delivery_time']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($order['notes'])): ?>
                                <p><strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-items">
                            <h5>Items Ordered:</h5>
                            <?php 
                            $items = $orderObj->getOrderItems($order['order_id']);
                            if (!empty($items)) {
                                foreach ($items as $item): 
                            ?>
                                <div class="order-item">
                                    <div class="item-info">
                                        <p class="item-name"><?= htmlspecialchars($item['product_name']) ?></p>
                                        <p class="item-details">
                                            <?= number_format($item['kilo'], 2) ?> kg × ₱<?= number_format($item['price'], 2) ?> 
                                            = <strong>₱<?= number_format($item['kilo'] * $item['price'], 2) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            } else {
                                echo "<p class='no-items'>No items found.</p>";
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-orders">You haven't placed any orders yet. <a href="../productitems_index/viewmeat.php">Start shopping!</a></p>
        <?php endif; ?>
    </div>
</main>
</body>
</html>