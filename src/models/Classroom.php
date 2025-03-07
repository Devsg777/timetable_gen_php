<?php
include_once(__DIR__ . '/../config/database.php');

class Classroom {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addClassroom($room_no, $type) {
        $query = "INSERT INTO classrooms (room_no, type) VALUES (:room_no, :type)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":room_no", $room_no);
        $stmt->bindParam(":type", $type);
        return $stmt->execute();
    }

    public function getAllClassrooms() {
        $query = "SELECT * FROM classrooms ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClassroomById($id) {
        $query = "SELECT * FROM classrooms WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateClassroom($id, $room_no, $type) {
        $query = "UPDATE classrooms SET room_no = :room_no, type = :type WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":room_no", $room_no);
        $stmt->bindParam(":type", $type);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function deleteClassroom($id) {
        $query = "DELETE FROM classrooms WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
