<?php
include_once(__DIR__ . '/../config/database.php');

class Combination {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addCombination($name, $department, $semester, $section) {

        // Check if the combination and section already exists
        $checkQuery = "SELECT * FROM combinations WHERE name = :name AND department = :department AND semester = :semester ";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":name", $name);
        $checkStmt->bindParam(":department", $department);
        $checkStmt->bindParam(":semester", $semester);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            return false; // Combination already exists
        }
        // If not, proceed to insert
        $query = "INSERT INTO combinations (name, department, semester,sections) VALUES (:name, :department, :semester, :section)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":department", $department);
        $stmt->bindParam(":semester", $semester);
        $stmt->bindParam(":section", $section);
        return $stmt->execute();
    }

    public function getCombinations() {
        $query = "SELECT * FROM combinations ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Corrected fetch method
    }

   

    public function updateCombination($id, $name, $department, $semester, $section) {
        // Check if the combination and section already exists

        // If not, proceed to update
        $query = "UPDATE combinations SET name = :name, department = :department, semester = :semester, sections = :section WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":department", $department);
        $stmt->bindParam(":semester", $semester);
        $stmt->bindParam(":section", $section);
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
