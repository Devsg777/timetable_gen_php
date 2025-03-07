<?php
include_once(__DIR__ . '/../config/database.php');

class Combination {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addCombination($name, $department, $semester) {
        $query = "INSERT INTO combinations (name, department, semester) VALUES (:name, :department, :semester)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":department", $department);
        $stmt->bindParam(":semester", $semester);
        return $stmt->execute();
    }

    public function getCombinations() {
        $query = "SELECT * FROM combinations ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Corrected fetch method
    }

   

    public function updateCombination($id, $name, $department, $semester) {
        $query = "UPDATE combinations SET name = :name, department = :department, semester = :semester WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":department", $department);
        $stmt->bindParam(":semester", $semester);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function deleteCombination($id) {
        $query = "DELETE FROM combinations WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Delete Error: " . implode(", ", $stmt->errorInfo()));
            return false;
        }
    }

    public function getCombinationById($id) {
        $query = "SELECT * FROM combinations WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
