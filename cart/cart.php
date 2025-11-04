<?php
session_start(); 

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

if (isset($_GET["empty"])) {
    $_SESSION["cart"] = [];
}

if (isset($_GET["remove"])) {
    $remove_id = $_GET["remove"];
    foreach ($_SESSION["cart"] as $key => $item) {
        if ($item["id"] == $remove_id) {
            unset($_SESSION["cart"][$key]);
            break;
        }
    }
    // Reindex array if na remove
    $_SESSION["cart"] = array_values($_SESSION["cart"]);
}

// Calculate total price
$total = 0;
foreach ($_SESSION["cart"] as $item) {
    $total += $item["price"] * $item["quantity"];
}

$cart_count = 0;
if (isset($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $cart_count += $item["quantity"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="../assets/cart.css">
</head>
<body>
<header class="navbar">
  <div class="logo">Meat Shop Online</div>
  <nav>
    <a href="../productitems_index/viewmeat.php">Shop</a>
    <a href="cart.php">Cart (<?= $cart_count ?>)</a>
    <a href="../account/logout.php">Logout</a>
  </nav>
</header>

<main class="cart-page">
  <h2>Your Shopping Cart</h2>

  <table class="cart-table">
    <thead>
      <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($_SESSION["cart"])): ?>
        <?php 
          $total = 0;
          foreach ($_SESSION["cart"] as $item): 
            $item_total = $item["price"] * $item["quantity"];
            $total += $item_total;
        ?>
          <tr>
            <td class="product-name"><?= htmlspecialchars($item["name"]) ?></td>
            <td>₱<?= number_format($item["price"], 2) ?></td>
            <td><input type="number" value="<?= $item["quantity"] ?>" min="1" class="qty-input"></td>
            <td>₱<?= number_format($item_total, 2) ?></td>
            <td>
              <form method="GET" action="cart.php">
                <input type="hidden" name="remove" value="<?= $item['id'] ?>">
                <button type="submit" class="remove-btn">Remove</button>
            </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5" class="empty-msg">Your cart is empty.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if (!empty($_SESSION["cart"])): ?>
    <div class="cart-footer">
      <h3>Total: ₱<?= number_format($total, 2) ?></h3>
      <div class="cart-actions">
        <a href="../productitems_index/viewmeat.php" class="btn back">Back to Shop</a>
        <a href="checkout.php" class="btn checkout">Proceed to Checkout</a>
      </div>
    </div>
  <?php endif; ?>
</main>

</body>
</html>