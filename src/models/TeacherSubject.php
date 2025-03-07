<?php
class TeacherSubject {
    private $conn;
    private $table_name = "teacher_subjects";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ Get all teacher-subject mappings
    public function getAllMappings() {
        $query = "SELECT ts.id, t.name AS teacher_name, s.name AS subject_name 
                  FROM " . $this->table_name . " ts
                  INNER JOIN teachers t ON ts.teacher_id = t.id
                  INNER JOIN subjects s ON ts.subject_id = s.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get All Subjects by Teacher Id
    public function getAllSubjectsByTeacherId($teacher_id) {
        $query = "SELECT ts.id, t.name AS teacher_name, s.name AS subject_name 
                  FROM " . $this->table_name . " ts
                  INNER JOIN teachers t ON ts.teacher_id = t.id
                  INNER JOIN subjects s ON ts.subject_id = s.id
                  WHERE ts.teacher_id = :teacher_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Add a teacher-subject mapping
    public function addMapping($teacher_id, $subject_id) {
        $query = "INSERT INTO " . $this->table_name . " (teacher_id, subject_id) VALUES (:teacher_id, :subject_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id);
        $stmt->bindParam(":subject_id", $subject_id);
        return $stmt->execute();
    }

    // ✅ Get a single teacher-subject mapping by ID
    public function getMappingById($id) {
        $query = "SELECT ts.id, t.name AS teacher_name, s.name AS subject_name
                  FROM " . $this->table_name . " ts
                  INNER JOIN teachers t ON ts.teacher_id = t.id
                  INNER JOIN subjects s ON ts.subject_id = s.id
                  WHERE ts.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Delete a teacher-subject mapping
    public function deleteMapping($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
