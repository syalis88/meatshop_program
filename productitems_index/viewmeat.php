<?php

session_start();

require_once "../classes/meat.php";
$meatObj = new Meat();

$search = $category = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"]) ? trim(htmlspecialchars($_GET["search"])) : "";
    $category = isset($_GET["category"]) ? trim(htmlspecialchars($_GET["category"])) : "";
}

$cart_count = 0;
if (isset($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $cart_count += $item["quantity"];
    }
}

$categories = $meatObj->getCategories();  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Meats</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<header>
    <h1>Meat Shop Online</h1>
    <nav>
        <a href="../productitems_index/viewmeat.php">Home</a>
        <a href="../cart/cart.php">Cart(<?= $cart_count ?>)</a>
        <a href="../account/logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <h2>Available Meats</h2>

    <form class="search-bar" action="" method="get">
        <input type="search" name="search" placeholder="Search meat..." value="<?= $search ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option 
                    value="<?= $cat['category_id']; ?>" 
                    <?= (isset($_GET['category']) && $_GET['category'] == $cat['category_id']) ? 'selected' : ''; ?>>
                    <?= $cat['category_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="meat-grid">
    <?php
    $meats = $meatObj->viewMeat($search, $category);
    if (!empty($meats)) {
        foreach ($meats as $meat) {
            $imagePath = "../assets/images/" . htmlspecialchars($meat['image']);
            $displayImage = file_exists($imagePath) ? $imagePath : "../assets/images/no-image.png";
    ?>
            <div class="meat-card">
                <img src="<?= $displayImage ?>" alt="<?= htmlspecialchars($meat['name']) ?>" class="meat-image">
                <h3><?= htmlspecialchars($meat['name']) ?></h3>
                <p class="category"><?= htmlspecialchars($meat['category_name']) ?></p>
                <p class="price">₱<?= number_format($meat['price'], 2) ?></p>
                <form action="addtocart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="id" value="<?= $meat['id'] ?>">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($meat['name']) ?>">
                    <input type="hidden" name="price" value="<?= $meat['price'] ?>">

                    <label for="quantity_<?= $meat['id'] ?>">Qty:</label>
                    <input type="number" id="quantity_<?= $meat['id'] ?>" name="quantity" value="1" min="1" max="99" required>

                    <button type="submit">Add to Cart</button>
                </form>
            </div>
    <?php
        } 
    } else {
        echo "<p class='no-meat'>No meats found.</p>";
    } 
    ?>
</div>
</main>
</body>
</html>
