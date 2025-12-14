<?php

session_start();
require_once "../classes/user.php";

$message = "";
$success = false;

if (isset($_GET['code'])) {
    $verificationCode = trim($_GET['code']);
    
    $userObj = new User();
    
    if ($userObj->verifyEmail($verificationCode)) {
        $message = "Your email has been verified successfully! You can now login to your account.";
        $success = true;
    } else {
        $message = "Invalid or expired verification link. Please contact support if you need help.";
    }
} else {
    $message = "No verification code provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Meat Shop Online</title>
    <style>
        .verification-container {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .verification-box {
            max-width: 500px;
            width: 100%;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .verification-box.success {
            border-top: 5px solid #4CAF50;
        }
        
        .verification-box.error {
            border-top: 5px solid #800020;
        }
        
        .verification-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        
        .verification-icon.success {
            color: #4CAF50;
        }
        
        .verification-icon.error {
            color: #800020;
        }
        
        .verification-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .verification-message {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .btn-login {
            background: #d32f2f;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .btn-login:hover {
            background: #b71c1c;
        }
        
        .btn-secondary {
            background: #757575;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            transition: background 0.3s;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background: #616161;
        }
    </style>
</head>
<body>
<header>
    <h1>Meat Shop Online</h1>
    <nav>
        <a href="../productitems_index/viewproducts.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<main>
    <div class="verification-container">
        <div class="verification-box <?= $success ? 'success' : 'error' ?>">
            <div class="verification-icon <?= $success ? 'success' : 'error' ?>">
                <?= $success ? '✓' : '✗' ?>
            </div>
            
            <div class="verification-title">
                <?= $success ? 'Email Verified!' : 'Verification Failed' ?>
            </div>
            
            <div class="verification-message">
                <?= htmlspecialchars($message) ?>
            </div>
            
            <div>
                <?php if ($success): ?>
                    <a href="login.php" class="btn-login">Go to Login</a>
                <?php else: ?>
                    <a href="register.php" class="btn-secondary">Register Again</a>
                    <a href="login.php" class="btn-login">Try to Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
</body>
</html>