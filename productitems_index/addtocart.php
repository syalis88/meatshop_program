<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'], $_POST['name'], $_POST['price'], $_POST['quantity'])) {
    $item = [
        'id' => intval($_POST['id']),
        'name' => htmlspecialchars($_POST['name']),
        'price' => floatval($_POST['price']),
        'quantity' => intval($_POST['quantity'])
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if item already exists
    $found = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['id'] === $item['id']) {
            $cartItem['quantity'] += $item['quantity'];
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $item;
    }

    header("Location: ../productitems_index/viewmeat.php");
    exit();
} else {
    echo "Invalid product.";
}
?>
