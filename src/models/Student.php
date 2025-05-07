<?php
class Student {
    private $conn;
    private $table_name = "students";
    
    public function __construct($db) {
        $this->conn = $db;
    }

    // Add Student
    public function addStudent($name, $email, $password, $combination_id, $phone_no, $address, $section) {
        try {
            $query = "INSERT INTO students (name, email, password, combination_id, phone_no, address, section) 
                      VALUES (:name, :email, :password, :combination_id, :phone_no, :address, :section)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'combination_id' => $combination_id,
                'phone_no' => $phone_no,
                'address' => $address,
                'section' => $section
            ]);
        } catch (PDOException $e) {
            die("Error adding student: " . $e->getMessage());
        }
    }

    // Get All Students
    public function getAllStudentsWithCombination() {
        $query = "SELECT
                    s.id,
                    s.name,
                    s.email,
                    s.phone_no,
                    s.address,
                    s.combination_id,
                    s.section,
                    c.id AS combination_id,
                    c.name AS combination_name,
                    c.semester AS combination_semester
                FROM
                    " . $this->table_name . " s
                LEFT JOIN
                    combinations c ON s.combination_id = c.id
                ORDER BY
                    s.name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function updateStudent($id, $name, $email, $phone_no, $address, $combination_id,$section) {
        // Check if the combination_id exists in the combinations table
        $checkQuery = "SELECT id FROM combinations WHERE id = :combination_id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':combination_id', $combination_id);
        $checkStmt->execute();
        if ($checkStmt->rowCount() == 0) {
            die("Error updating student: Invalid combination_id. Please ensure the combination_id exists in the combinations table.");
        }

        $query = "UPDATE " . $this->table_name . " SET name = :name, email = :email, phone_no = :phone_no, address = :address, combination_id = :combination_id, section = :section WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_no', $phone_no);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':combination_id', $combination_id);
        $stmt->bindParam(':section', $section);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateStudentWithPass($id, $name, $email, $phone_no, $address, $combination_id,$section, $password) {
        // Note: This function does NOT have the combination_id existence check

        $query = "UPDATE " . $this->table_name . " SET name = :name, email = :email, phone_no = :phone_no, address = :address, combination_id = :combination_id,  section = :section,password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone_no', $phone_no);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':combination_id', $combination_id);
        $stmt->bindParam(':section', $section);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            return true;
        }
        return false;
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
            $_SESSION['student_combination'] = $student['combination_id'];
            $_SESSION['student_section'] = $student['section']; // Assuming you have a section field in the students table
            return true;
        }
        return false;
    }
    
    
    public function logout() {
        session_start();
        session_destroy();
    }
}

?>
