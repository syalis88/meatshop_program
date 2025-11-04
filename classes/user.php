<?php
require_once 'meatshopDB.php';

class User extends Database {

    public $id;
    public $firstname;
    public $lastname;
    public $username;
    public $email;
    public $password;
    public $role;
    public $is_active;

    function addUser() {
        $sql = "INSERT INTO users (full_name, email, password, address, created_at, is_active)
        VALUES (:full_name, :email, :password, :address, NOW(), 1)";
        
        $query = $this->connect()->prepare($sql);
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $query = $this->connect()->prepare($sql);
        $query->bindParam(':full_name', $this->full_name);
        $query->bindParam(':email', $this->email);
        $query->bindParam(':password', $this->password);
        $query->bindParam(':address', $this->address);

        return $query->execute();
    }

    function getUserByEmail() {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1;";
        $query = $this->connect()->prepare($sql);
        $query->bindParam(':email', $this->email);
        $query->execute();
        return $query->fetch();
    }
}
?>
