<?php
session_start();

// Get Order ID if passed from checkout
$order_id = $_GET["order_id"] ?? null;

// If there is no order_id, user probably accessed page directly
if (!$order_id) {
    header("Location: ../productitems_index/viewmeat.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Success</title>
  <link rel="stylesheet" href="../assets/styles.css">
</head>

<body>

<header>
  <h1>Meat Shop Online</h1>
  <nav>
    <a href="../productitems_index/viewmeat.php">Shop</a>
    <a href="../cart/cart.php">Cart</a>
    <a href="../account/logout.php">Logout</a>
  </nav>
</header>

<div class="success-box">
    <h2 class="success-title">Order Placed Successfully!</h2>
    <p>Thank you for your purchase. Your order is now being processed.</p>
    <br><br>
    <a href="../productitems_index/viewmeat.php" class="proceed-btn">Return to Shop</a>
</div>

</body>
</html>
