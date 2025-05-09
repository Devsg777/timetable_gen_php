<?php
include_once(__DIR__ . '/../config/database.php');

class Teacher {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addTeacher($name, $department, $email, $password, $phone_no, $address, $min_class_hours, $min_lab_hours) {
        $query = "INSERT INTO teachers (name, department, email, password, phone_no, address, min_class_hours_week, min_lab_hours_week) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$name, $department, $email, $hashedPassword, $phone_no, $address, $min_class_hours, $min_lab_hours]);
        return $stmt;
    }

    public function getTeachers() {
        $query = "SELECT * FROM teachers ORDER BY id DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeacherById($id) {
        $query = "SELECT * FROM teachers WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTeacher($id, $name, $department, $email, $phone_no, $address, $min_class_hours, $min_lab_hours) {
        $query = "UPDATE teachers SET name = ?, department = ?, email = ?, phone_no = ?, address = ?, min_class_hours_week = ?, min_lab_hours_week = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $department, $email, $phone_no, $address, $min_class_hours, $min_lab_hours, $id]);
    }

    public function deleteTeacher($id) {
        $query = "DELETE FROM teachers WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
    public function login($email, $password) {
        $query = "SELECT * FROM teachers WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['email' => $email]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($teacher && password_verify($password, $teacher['password'])) {
            session_start();
            $_SESSION['teacher_id'] = $teacher['id'];
            $_SESSION['teacher_name'] = $teacher['name'];
            $_SESSION['teacher_email'] = $teacher['email'];
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
