<?php

include_once(__DIR__ . '/../config/database.php');
class Request {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRequest(
        $requester_id,
        $requester_type,
        $existing_timetable_id,
        $proposed_subject_id = null,
        $proposed_teacher_id = null,
        $proposed_classroom_id = null,
        $proposed_day = null,
        $proposed_start_time = null,
        $proposed_end_time = null,
        $reason
    ) {
        $query = "INSERT INTO requests (
                    requester_type,
                    existing_timetable_id,
                    proposed_subject_id,
                    proposed_teacher_id,
                    proposed_classroom_id,
                    proposed_day,
                    proposed_start_time,
                    proposed_end_time,
                    reason,
                    status_id,
                    teacher_id,
                    student_id
                  ) VALUES (
                    :requester_type,
                    :existing_timetable_id,
                    :proposed_subject_id,
                    :proposed_teacher_id,
                    :proposed_classroom_id,
                    :proposed_day,
                    :proposed_start_time,
                    :proposed_end_time,
                    :reason,
                    1, -- Default status is 'Pending'
                    :teacher_id,
                    :student_id
                  )";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(":requester_type", $requester_type);
        $stmt->bindParam(":existing_timetable_id", $existing_timetable_id);
        $stmt->bindParam(":proposed_subject_id", $proposed_subject_id);
        $stmt->bindParam(":proposed_teacher_id", $proposed_teacher_id);
        $stmt->bindParam(":proposed_classroom_id", $proposed_classroom_id);
        $stmt->bindParam(":proposed_day", $proposed_day);
        $stmt->bindParam(":proposed_start_time", $proposed_start_time);
        $stmt->bindParam(":proposed_end_time", $proposed_end_time);
        $stmt->bindParam(":reason", $reason);
    
        if ($requester_type === 'teacher') {
            $stmt->bindValue(":teacher_id", $requester_id, PDO::PARAM_INT);
            $stmt->bindValue(":student_id", null, PDO::PARAM_INT);
        } elseif ($requester_type === 'student') {
            $stmt->bindValue(":teacher_id", null, PDO::PARAM_INT);
            $stmt->bindValue(":student_id", $requester_id, PDO::PARAM_INT);
        } else {
            // Handle error: unknown requester type
            return false;
        }
    
        return $stmt->execute();
    }

    public function getRequests() {
        $query = "SELECT
                    r.id,
                    r.requester_type,
                    r.request_type,
                    r.existing_timetable_id,
                    r.proposed_subject_id,
                    r.proposed_teacher_id,
                    r.proposed_classroom_id,
                    r.proposed_day,
                    r.proposed_start_time,
                    r.proposed_end_time,
                    r.reason,
                    r.request_date,
                    rs.status_name,
                    tchr_req.name AS teacher_name_requested,
                    stud_req.name AS student_name_requested,
                    tchr_req.id AS teacher_id_requested,
                    stud_req.id AS student_id_requested,
                    sub.name AS proposed_subject_name,
                    teach_prop.name AS proposed_teacher_name,
                    cr.room_no AS proposed_classroom_name
                  FROM requests r
                  JOIN request_statuses rs ON r.status_id = rs.id
                  LEFT JOIN teachers tchr_req ON r.teacher_id = tchr_req.id
                  LEFT JOIN students stud_req ON r.student_id = stud_req.id
                  LEFT JOIN subjects sub ON r.proposed_subject_id = sub.id
                  LEFT JOIN teachers teach_prop ON r.proposed_teacher_id = teach_prop.id
                  LEFT JOIN classrooms cr ON r.proposed_classroom_id = cr.id
                  ORDER BY r.request_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRequestById($id) {
        $query = "SELECT
                    r.id,
                    r.requester_type,
                    r.request_type,
                    r.existing_timetable_id,
                    r.proposed_subject_id,
                    r.proposed_teacher_id,
                    r.proposed_classroom_id,
                    r.proposed_day,
                    r.proposed_start_time,
                    r.proposed_end_time,
                    r.reason,
                    r.request_date,
                    rs.status_name,
                    tchr_req.name AS teacher_name_requested,
                    stud_req.name AS student_name_requested,
                    tchr_req.id AS teacher_id_requested,
                    stud_req.id AS student_id_requested,
                    sub.name AS proposed_subject_name,
                    teach_prop.name AS proposed_teacher_name,
                    cr.room_no AS proposed_classroom_name
                  FROM requests r
                  JOIN request_statuses rs ON r.status_id = rs.id
                  LEFT JOIN teachers tchr_req ON r.teacher_id = tchr_req.id
                  LEFT JOIN students stud_req ON r.student_id = stud_req.id
                  LEFT JOIN subjects sub ON r.proposed_subject_id = sub.id
                  LEFT JOIN teachers teach_prop ON r.proposed_teacher_id = teach_prop.id
                  LEFT JOIN classrooms cr ON r.proposed_classroom_id = cr.id
                  WHERE r.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getRequestsByTeacherId($teacher_id) {
        $query = "SELECT
                    r.id,
                    r.requester_type,
                    r.request_type,
                    r.existing_timetable_id,
                    r.proposed_subject_id,
                    r.proposed_teacher_id,
                    r.proposed_classroom_id,
                    r.proposed_day,
                    r.proposed_start_time,
                    r.proposed_end_time,
                    r.reason,
                    r.request_date,
                    rs.status_name,
                    tchr_req.name AS teacher_name_requested,
                    stud_req.name AS student_name_requested,
                    tchr_req.id AS teacher_id_requested,
                    stud_req.id AS student_id_requested,
                    sub.name AS proposed_subject_name,
                    teach_prop.name AS proposed_teacher_name,
                    cr.room_no AS proposed_classroom_name
                  FROM requests r
                  JOIN request_statuses rs ON r.status_id = rs.id
                  LEFT JOIN teachers tchr_req ON r.teacher_id = tchr_req.id
                  LEFT JOIN students stud_req ON r.student_id = stud_req.id
                  LEFT JOIN subjects sub ON r.proposed_subject_id = sub.id
                  LEFT JOIN teachers teach_prop ON r.proposed_teacher_id = teach_prop.id
                  LEFT JOIN classrooms cr ON r.proposed_classroom_id = cr.id
                  WHERE tchr_req.id = :teacher_id OR stud_req.id = :teacher_id";
        // Note: Adjust the WHERE clause based on your actual logic for filtering by teacher_id
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteRequest($id) {
        $query = "DELETE FROM requests WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    Public function getRequestByStudentId($student_id) {
        $query = "SELECT
                    r.id,
                    r.requester_type,
                    r.request_type,
                    r.existing_timetable_id,
                    r.proposed_subject_id,
                    r.proposed_teacher_id,
                    r.proposed_classroom_id,
                    r.proposed_day,
                    r.proposed_start_time,
                    r.proposed_end_time,
                    r.reason,
                    r.request_date,
                    rs.status_name,
                    tchr_req.name AS teacher_name_requested,
                    stud_req.name AS student_name_requested,
                    tchr_req.id AS teacher_id_requested,
                    stud_req.id AS student_id_requested,
                    sub.name AS proposed_subject_name,
                    teach_prop.name AS proposed_teacher_name,
                    cr.room_no AS proposed_classroom_name
                  FROM requests r
                  JOIN request_statuses rs ON r.status_id = rs.id
                  LEFT JOIN teachers tchr_req ON r.teacher_id = tchr_req.id
                  LEFT JOIN students stud_req ON r.student_id = stud_req.id
                  LEFT JOIN subjects sub ON r.proposed_subject_id = sub.id
                  LEFT JOIN teachers teach_prop ON r.proposed_teacher_id = teach_prop.id
                  LEFT JOIN classrooms cr ON r.proposed_classroom_id = cr.id
                  WHERE stud_req.id = :student_id
                  ORDER BY r.request_date DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":student_id", $student_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateRequestStatus($id, $status_id) {
        $query = "UPDATE requests SET status_id = :status_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status_id", $status_id, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // You might add more methods here for specific filtering or actions on requests
}
?>