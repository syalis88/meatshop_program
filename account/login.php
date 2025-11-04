<?php
session_start();

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: ../admin_panel/dashboard.php');
        exit();
    } elseif ($_SESSION['user']['role'] === 'customer') {
        header('Location: ../productitems_index/viewmeat.php');
        exit();
    }
}

require_once('../classes/account.php');
$account = new Account();
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $account->email = htmlentities($_POST['email']);
    $account->password = htmlentities($_POST['password']);

    if ($account->login()) {
        // Get full user info
        $_SESSION['user'] = $account->getUserByEmail();

        if ($_SESSION['user']['role'] === 'admin') {
            header('Location: ../admin_panel/dashboard.php');
            exit();
        } elseif ($_SESSION['user']['role'] === 'customer') {
            header('Location: ../productitems_index/viewmeat.php');
            exit();
        }
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/loginstyle.css">
</head>

<body>
<div class="overlay"></div>

<div class="login-container">
    <h2>Meat Shop Online</h2>
    <h3>Sign in</h3>

    <form method="POST" action="">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <p class="register-text">Don't have an account?
        <a href="register.php">Register here</a>
    </p>
</div>

</body>
</html>