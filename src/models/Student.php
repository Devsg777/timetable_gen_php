<?php
class Student {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    // Add Student
    public function addStudent($name, $email, $password, $combination_id, $phone_no, $address) {
        try {
            $query = "INSERT INTO students (name, email, password, combination_id, phone_no, address) 
                      VALUES (:name, :email, :password, :combination_id, :phone_no, :address)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'combination_id' => $combination_id,
                'phone_no' => $phone_no,
                'address' => $address
            ]);
        } catch (PDOException $e) {
            die("Error adding student: " . $e->getMessage());
        }
    }

    // Get All Students
    public function getAllStudents() {
        try {
            $query = "SELECT s.id, s.name, s.email, s.phone_no, s.address, c.name AS combination_name, c.semester As combination_semester 
                      FROM students s 
                      JOIN combinations c ON s.combination_id = c.id 
                      ORDER BY s.id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching students: " . $e->getMessage());
        }
    }

    // Get Student by ID
    public function getStudentById($id) {
        try {
            $query = "SELECT * FROM students WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching student: " . $e->getMessage());
        }
    }

    // Update Student
    public function updateStudent($id, $name, $email, $combination_id, $phone_no, $address) {
        try {
            $query = "UPDATE students SET name = :name, email = :email, combination_id = :combination_id, 
                      phone_no = :phone_no, address = :address WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'combination_id' => $combination_id,
                'phone_no' => $phone_no,
                'address' => $address
            ]);
        } catch (PDOException $e) {
            die("Error updating student: " . $e->getMessage());
        }
    }

    // Delete Student
    public function deleteStudent($id) {
        try {
            $query = "DELETE FROM students WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            die("Error deleting student: " . $e->getMessage());
        }
    }
    public function login($email, $password) {
        $query = "SELECT * FROM students WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['email' => $email]);
        $student  = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($student && password_verify($password, $student['password'])) {
            session_start();
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['student_com'] = $student['combiantion_id'];
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
