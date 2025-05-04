<?php
class Subject {
    private $conn;
    private $table_name = "subjects";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch all subjects
    public function getAllSubjects() {
        $query = "SELECT subjects.*, combinations.name AS combination_name , combinations.semester As combination_semester, combinations.sections AS comination_section
                  FROM " . $this->table_name . " 
                  JOIN combinations ON subjects.combination_id = combinations.id 
                  ORDER BY subjects.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch subject by ID
    public function getSubjectById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Add new subject
    public function addSubject($name, $min_classes_per_week, $type, $combination_id, $duration) {
        $query = "INSERT INTO " . $this->table_name . " (name, min_classes_per_week, type, combination_id, duration) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $min_classes_per_week, $type, $combination_id, $duration]);
    }

    // Update subject
    public function updateSubject($id, $name, $min_classes_per_week, $type, $combination_id, $duration ) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = ?, min_classes_per_week = ?, type = ?, combination_id = ? , duration = ?  
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $min_classes_per_week, $type, $combination_id, $duration, $id]);
    }

    // Delete subject
    public function deleteSubject($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
    public function getSubjectsByCombinationId($combination_id){
        $query = "SELECT * FROM " . $this->table_name . " WHERE combination_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$combination_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
