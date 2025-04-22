<?php
include_once(__DIR__ . '/../config/database.php');

class Classroom {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addClassroom($room_no, $type) {
        // Check if the room number already exists
        $checkQuery = "SELECT * FROM classrooms WHERE room_no = :room_no";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":room_no", $room_no);
        $checkStmt->execute();
        if ($checkStmt->rowCount() > 0) {
            return false; // Room number already exists
        }else{
            // Insert new classroom
            $query = "INSERT INTO classrooms (room_no, type) VALUES (:room_no, :type)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":room_no", $room_no);
            $stmt->bindParam(":type", $type);
            return $stmt->execute();
        }
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
        // Check if the room number already exists for another classroom
        $checkQuery = "SELECT * FROM classrooms WHERE room_no = :room_no AND id != :id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":room_no", $room_no);
        $checkStmt->bindParam(":id", $id);
        $checkStmt->execute();
        if ($checkStmt->rowCount() > 0) {
            return false; // Room number already exists for another classroom
        }else{
        // Update classroom

        $query = "UPDATE classrooms SET room_no = :room_no, type = :type WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":room_no", $room_no);
        $stmt->bindParam(":type", $type);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
        }
    }

    public function deleteClassroom($id) {
        $query = "DELETE FROM classrooms WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
