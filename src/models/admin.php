<?php
include_once __DIR__ . '/../config/database.php';

class Admin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM admins WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            session_start();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}
?>