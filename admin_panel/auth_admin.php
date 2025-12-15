<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: /Meat_Ordering_Shop/account/login.php');
    exit();
}

if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: /Meat_Ordering_Shop/productitems/viewproducts.php');
    exit();
}
?>
