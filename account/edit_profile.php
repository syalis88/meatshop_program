<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once "../classes/user.php";
require_once "../classes/account.php";

$userId = $_SESSION['user']['user_id'];
$userObj = new User();

// Get current user data from database
$userData = $userObj->getUserById($userId);

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $middleName = isset($_POST['middleName']) ? trim($_POST['middleName']) : "";
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    
    // Validation
    if (empty($firstName)) {
        $errors[] = "First name is required";
    }
    if (empty($lastName)) {
        $errors[] = "Last name is required";
    }
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    // Phone validation
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    } elseif (strlen($phone) < 10) {
        $errors[] = "Phone number must be at least 10 digits";
    } elseif (!preg_match('/^[0-9+() -]+$/', $phone)) {
        $errors[] = "Phone number contains invalid characters";
    }
    
    if (empty($errors)) {
        // Update profile
        if ($userObj->updateProfile($userId, $firstName, $lastName, $middleName, $address, $phone)) {
            // Update session data
            $_SESSION['user']['FirstName'] = $firstName;
            $_SESSION['user']['LastName'] = $lastName;
            $_SESSION['user']['MiddleName'] = $middleName;
            $_SESSION['user']['address'] = $address;
            $_SESSION['user']['phone'] = $phone;
            
            $success = "Profile updated successfully!";
            
            // Refresh user data
            $userData = $userObj->getUserById($userId);
        } else {
            $errors[] = "Failed to update profile. Please try again.";
        }
    }
}

// Get cart count
$cart_count = 0;
if (isset($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $cart_count += $item["kilo"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../assets/profile.css">
    <style>
        .edit-form {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
        .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .btn-cancel {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
        }
        .error-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .success-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .required {
            color: red;
        }
    </style>
</head>
<body>
<header>
    <h1>Meat Shop Online</h1>
    <nav>
        <a href="../productitems_index/viewproducts.php">Home</a>
        <a href="../cart/cart.php">Cart(<?= $cart_count ?>)</a>
        <?php include '../includes/usernotif.php'; ?>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <h2>Edit Profile</h2>
    
    <div class="edit-form">
        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $error): ?>
                <p>• <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
        <div class="success-box">
            <p><?= htmlspecialchars($success) ?></p>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>First Name <span class="required">*</span></label>
                <input type="text" name="firstName" 
                       value="<?= htmlspecialchars($userData['FirstName'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Last Name <span class="required">*</span></label>
                <input type="text" name="lastName" 
                       value="<?= htmlspecialchars($userData['LastName'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label>Middle Name (Optional)</label>
                <input type="text" name="middleName" 
                       value="<?= htmlspecialchars($userData['MiddleName'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Phone Number <span class="required">*</span></label>
                <input type="tel" name="phone" 
                       value="<?= htmlspecialchars($userData['phone'] ?? '') ?>" 
                       placeholder="09123456789" required>
                <small>Format: 09XX XXX XXXX or +639XX XXX XXXX</small>
            </div>
            
            <div class="form-group">
                <label>Delivery Address <span class="required">*</span></label>
                <textarea name="address" rows="3" required><?= htmlspecialchars($userData['address'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn-submit">Save Changes</button>
            <a href="profile.php" class="btn-cancel">Cancel</a>
        </form>
    </div>
</main>
</body>
</html>