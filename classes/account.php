<?php
require_once 'user.php';

class Account extends User {

    public $email;
    public $password;

    function login() {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1 LIMIT 1;";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':email', $this->email);
        $query->execute();

        $userData = $query->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        echo "<pre>No user found with email: {$this->email}</pre>";
        return false;
    }
    
        // Check if a record was found
        if ($userData) {
            if (password_verify($this->password, $userData['password'])) {
                // Save user data for session
                $_SESSION['user'] = [
                    'id' => $userData['id'],
                    'full_name' => $userData['full_name'],
                    'email' => $userData['email'],
                    'role' => $userData['role'],
                ];
                return true;
            }
        }

        return false;
    }

    public function addUser() {
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password, address, created_at, is_active)
                VALUES (:full_name, :email, :password, :address, NOW(), :is_active)";
        
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':full_name', $this->full_name);
        $query->bindParam(':email', $this->email);
        $query->bindParam(':password', $hashedPassword);
        $query->bindParam(':address', $this->address);
        $query->bindParam(':is_active', $this->is_active);

        return $query->execute();
    }
}
?>
