<?php
session_start();

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: ../admin_panel/dashboard.php');
        exit();
    } elseif ($_SESSION['user']['role'] === 'customer') {
        header('Location: ../product/viewmeat.php');
        exit();
    }
}

require_once('../classes/account.php');
$user = new Account();

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    
    $firstname = trim(htmlspecialchars($_POST['firstname']));
    $lastname  = trim(htmlspecialchars($_POST['lastname']));
    $email     = trim(htmlspecialchars($_POST['email']));
    $password  = trim($_POST['password']);
    $confirm   = trim($_POST['confirm_password']);
    $address   = trim(htmlspecialchars($_POST['address']));

    if ($firstname === "" || $lastname === "" || $email === "" || $password === "" || $confirm === "" || $address === "") {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $user->full_name = $firstname . " " . $lastname;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->address = $address;
        $user->is_active = 1;

        if ($user->addUser()) {
            $success = "Account created successfully! You may now login.";
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Meat Shop</title>
    <link rel="stylesheet" href="../assets/registerstyle.css">
</head>
<body>

<div class="register-wrapper">

    <!-- LEFT SIDE -->
    <div class="left-panel">
        <div class="overlay">
            <h1>Meat Shop Online</h1>
            <p>Fresh. Quality. Delivered to your door.</p>
        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="right-panel">
        <div class="register-box">
            <h2>Create Account</h2>
            <h3>Register to meat shop today</h3>

            <form method="POST" action="">
                <input type="text" name="firstname" placeholder="First Name" value="<?= isset($firstname) ? $firstname : '' ?>">
                <input type="text" name="lastname" placeholder="Last Name" value="<?= isset($lastname) ? $lastname : '' ?>">
                <input type="email" name="email" placeholder="Email" value="<?= isset($email) ? $email : '' ?>">
                <input type="password" name="password" placeholder="Password">
                <input type="password" name="confirm_password" placeholder="Confirm Password">
                <input type="text" name="address" placeholder="Address" value="<?= isset($address) ? $address : '' ?>">

                <button type="submit">Register</button>
            </form>

            <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="success"><?= $success ?></p>
            <?php endif; ?>

            <p class="login-text">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

</div>

</body>
</html>
