<?php
session_start();
require_once "../classes/user.php";
require_once "../classes/email.php";
require_once "../classes/notification.php";

$errors = [];
$firstName = $lastName = $middleName = $email = $address = $phone = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $middleName = isset($_POST['middleName']) ? trim($_POST['middleName']) : "";
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    
    if (empty($firstName)) {
        $errors[] = "First name is required";
    }
    if (empty($lastName)) {
        $errors[] = "Last name is required";
    }
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (strlen($phone) < 10) {
        $errors[] = "Phone number must be at least 10 digits";
    } elseif (!preg_match('/^[0-9+() -]+$/', $phone)) {
        $errors[] = "Phone number contains invalid characters";
    }
    
    if (empty($errors)) {
        $userObj = new User();
        
        // Check if email exists
        if ($userObj->emailExists($email)) {
            $errors[] = "Email already registered";
        } else {
            // Generate verification code
            $verificationCode = bin2hex(random_bytes(32));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Register user with phone number
            if ($userObj->register($firstName, $lastName, $middleName, $email, $hashedPassword, $address, $phone, $verificationCode)) {
                
                $userObj->email = $email;
                $userData = $userObj->getUserByEmail();
                $newUserId = $userData['user_id'];
                
                // Send verification email
                $emailObj = new Email();
                $emailSent = $emailObj->sendVerificationEmail($email, $firstName, $verificationCode);
                
                // Create notification for admin
                $notificationObj = new Notification();
                $notificationObj->createNotification(
                    $newUserId,
                    null,
                    'user',
                    'New User Registration',
                    'New user registered: ' . $firstName . ' ' . $lastName . ' (' . $email . ')',
                    ''
                );
                
                $_SESSION['registration_success'] = "Registration successful! Please check your email to verify your account.";
                header("Location: login.php");
                exit;
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Meat Shop Online</title>
    <link rel="stylesheet" href="../assets/registerstyle.css">
</head>
<body>
<div class="auth-container">
    <div class="left-panel">
        <div class="overlay">
            <h2>Fresh & Quality Meat</h2>
            <p>Register now and start ordering conveniently.</p>
        </div>
    </div>
    <div class="right-panel">
        <div class="auth-box">
            <h2>Create Your Account</h2>
            <p class="subtitle">Fill out the form to continue.</p>
            
            <?php if (!empty($errors)): ?>
            <div class="error-box">
                <?php foreach ($errors as $error): ?>
                    <p>• <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>First Name *</label>
                    <input type="text" name="firstName" value="<?= htmlspecialchars($firstName) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Last Name *</label>
                    <input type="text" name="lastName" value="<?= htmlspecialchars($lastName) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Middle Name (Optional)</label>
                    <input type="text" name="middleName" value="<?= htmlspecialchars($middleName) ?>">
                </div>
                
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>" 
                           placeholder="09123456789" required>
                    <small style="color: #666; font-size: 12px; display: block; margin-top: 4px;">
                        Format: 09XX XXX XXXX or +639XX XXX XXXX
                    </small>
                </div>
                
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <label>Delivery Address *</label>
                    <textarea name="address" rows="3" required><?= htmlspecialchars($address) ?></textarea>
                </div>
                
                <button type="submit" class="btn-submit">Register</button>
            </form>
            
            <p class="text-center">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
