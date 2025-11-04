<?php
session_start();
require_once "../classes/meatshopDB.php"; 

if (empty($_SESSION["cart"])) {
    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;
foreach ($_SESSION["cart"] as $item) {
    $total += $item["price"] * $item["quantity"];
}

$db = new Database();

$name = $phone = $address = $delivery_date = $delivery_time = $notes = "";
$errors = [
    "name" => "",
    "phone" => "",
    "address" => "",
    "delivery_date" => "",
    "delivery_time" => ""
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $phone = htmlspecialchars(trim($_POST["phone"] ?? ""));
    $address = htmlspecialchars(trim($_POST["address"] ?? ""));
    $delivery_date = htmlspecialchars(trim($_POST["delivery_date"] ?? ""));
    $delivery_time = htmlspecialchars(trim($_POST["delivery_time"] ?? ""));
    $notes = htmlspecialchars(trim($_POST["notes"] ?? ""));

    $today = date("Y-m-d");

    // Validation
    if (empty($name)) $errors["name"] = "Full name is required.";
    if (empty($phone) || strlen($phone) < 7) $errors["phone"] = "Valid phone number is required.";
    if (empty($address)) $errors["address"] = "Delivery address is required.";
    if (empty($delivery_date)) $errors["delivery_date"] = "Delivery date is required.";
    elseif ($delivery_date < $today) $errors["delivery_date"] = "Date cannot be in the past.";
    if (empty($delivery_time)) $errors["delivery_time"] = "Delivery time is required.";

    // If no errors
    if (!array_filter($errors)) {

    $conn = $db->connect();

    $user_id = $_SESSION["user"]["id"]; 
    $subtotal = $total; 
    $status = "Pending"; 

    $sql_order = "INSERT INTO orders 
    (user_id, customer_name, customer_phone, customer_address, delivery_date, delivery_time, notes, subtotal, total_amount, status) 
    VALUES 
    (:user_id, :name, :phone, :address, :delivery_date, :delivery_time, :notes, :subtotal, :total_amount, :status)";

    $query_order = $conn->prepare($sql_order);

    $query_order->bindParam(":user_id", $user_id);
    $query_order->bindParam(":name", $name);
    $query_order->bindParam(":phone", $phone);
    $query_order->bindParam(":address", $address);
    $query_order->bindParam(":delivery_date", $delivery_date);
    $query_order->bindParam(":delivery_time", $delivery_time);
    $query_order->bindParam(":notes", $notes);
    $query_order->bindParam(":subtotal", $subtotal);
    $query_order->bindParam(":total_amount", $total);
    $query_order->bindParam(":status", $status);

    $query_order->execute();
    $order_id = $conn->lastInsertId();

    // Insert order items
    $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price)
                 VALUES (:order_id, :product_id, :quantity, :price)";
    $query_item = $conn->prepare($sql_item);

    foreach ($_SESSION["cart"] as $item) {
        $query_item->bindValue(":order_id", $order_id);
        $query_item->bindValue(":product_id", $item["id"]);
        $query_item->bindValue(":quantity", $item["quantity"]);
        $query_item->bindValue(":price", $item["price"]);
        $query_item->execute();
    }

    // Clear cart & redirect
    $_SESSION["cart"] = [];
    header("Location: ../cart/order_success.php?order_id=$order_id");
    exit();
    }
}


// Count cart
$cart_count = 0;
if (!empty($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $cart_count += $item["quantity"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<link rel="stylesheet" href="../assets/checkout.css">
<style>
    .error-text { color: red; font-size: 14px; margin-bottom: 8px; }
</style>
</head>

<body>

<header class="navbar">
    <h1 class="logo">Meat Shop Online</h1>
    <nav>
        <a href="../productitems_index/viewmeat.php">Shop</a>
        <a href="../cart/cart.php">Cart (<?= $cart_count ?>)</a>
        <a href="../account/logout.php">Logout</a>
    </nav>
</header>

<main class="checkout-container">
    <div class="checkout-header">
        <h2>Checkout</h2>
        <a href="cart.php" class="btn edit">Edit Cart</a>
    </div>

<?php if (isset($success)) { ?>
    <p class="msg success"><?= $success ?></p>

<?php } else { ?>

    <h3 class="section-title">Order Summary</h3>

    <table class="checkout-table">
        <thead>
        <tr>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($_SESSION["cart"] as $item) { ?>
        <tr>
            <td><?= htmlspecialchars($item["name"]) ?></td>
            <td><?= $item["quantity"] ?></td>
            <td>₱<?= number_format($item["price"], 2) ?></td>
            <td>₱<?= number_format($item["price"] * $item["quantity"], 2) ?></td>
        </tr>
        <?php } ?>
        <tr class="total-row">
            <td colspan="4" align="right"><strong>Total: ₱<?= number_format($total, 2) ?></strong></td>
        </tr>
        </tbody>
    </table>

    <form method="POST" class="checkout-form">

        <label>Full Name:</label>
        <input type="text" name="name" value="<?= $name ?>">
        <div class="error-text"><?= $errors["name"] ?></div>

        <label>Phone Number:</label>
        <input type="text" name="phone" value="<?= $phone ?>">
        <div class="error-text"><?= $errors["phone"] ?></div>

        <label>Delivery Address:</label>
        <textarea name="address"><?= $address ?></textarea>
        <div class="error-text"><?= $errors["address"] ?></div>

        <label>Delivery Date:</label>
        <input type="date" name="delivery_date" min="<?= date('Y-m-d'); ?>" value="<?= $delivery_date ?>">
        <div class="error-text"><?= $errors["delivery_date"] ?></div>

        <label>Delivery Time:</label>
        <input type="time" name="delivery_time" value="<?= $delivery_time ?>">
        <div class="error-text"><?= $errors["delivery_time"] ?></div>

        <label>Order Notes (optional):</label>
        <textarea name="notes" rows="3"><?= $notes ?></textarea>

        <button type="submit" class="btn checkout">Place Order</button>
    </form>

<?php } ?>

</main>
</body>
</html>
