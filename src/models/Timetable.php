<?php
class Timetable {
    private $conn;
    private $table_name = "timetable";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ Get full timetable
    public function getAllTimetables() {
        $query = "SELECT tt.id, c.name AS combination, s.name AS subject, t.name AS teacher, cl.room_no, tt.day, tt.start_time, tt.end_time 
                  FROM " . $this->table_name . " tt
                  INNER JOIN combinations c ON tt.combination_id = c.id
                  INNER JOIN subjects s ON tt.subject_id = s.id
                  INNER JOIN teachers t ON tt.teacher_id = t.id
                  INNER JOIN classrooms cl ON tt.classroom_id = cl.id
                  ORDER BY tt.day, tt.start_time";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Generate Timetable (without conflicts)
    public function generateTimetable() {
        $query = "
            SELECT c.id AS combination_id, s.id AS subject_id, t.id AS teacher_id, cl.id AS classroom_id, s.type AS subject_type 
            FROM combinations c
            JOIN subjects s ON s.combination_id = c.id
            JOIN teacher_subjects ts ON ts.subject_id = s.id
            JOIN teachers t ON ts.teacher_id = t.id
            JOIN classrooms cl
            ORDER BY RAND()";
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $time_slots = ['09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00'];
    
        $schedule = [];
        $theory_count_per_day = [];
        $lab_count_per_week = [];
    
        foreach ($all_data as $row) {
            foreach ($days as $day) {
                // Initialize counters if not set
                if (!isset($theory_count_per_day[$row['combination_id']][$day])) {
                    $theory_count_per_day[$row['combination_id']][$day] = 0;
                }
                if (!isset($lab_count_per_week[$row['combination_id']])) {
                    $lab_count_per_week[$row['combination_id']] = 0;
                }
    
                // **Enforce class limits**
                if ($row['subject_type'] === 'theory' && $theory_count_per_day[$row['combination_id']][$day] >= 4) {
                    continue; // Skip if 4 theory classes are already assigned for this day
                }
                if ($row['subject_type'] === 'lab' && $lab_count_per_week[$row['combination_id']] >= 2) {
                    continue; // Skip if 2 lab classes are already assigned for this week
                }
    
                foreach ($time_slots as $slot) {
                    if (!isset($schedule[$day][$slot]['teacher'][$row['teacher_id']]) &&
                        !isset($schedule[$day][$slot]['classroom'][$row['classroom_id']]) &&
                        !isset($schedule[$day][$slot]['combination'][$row['combination_id']])) {
    
                        $start_time = isset($slot) ? $slot : '09:00:00';
                        $end_time = date("H:i:s", strtotime($start_time) + 3600); // Add 1 hour
    
                        $insertQuery = "
                            INSERT INTO timetable (combination_id, subject_id, teacher_id, classroom_id, day, start_time, end_time) 
                            VALUES (:combination_id, :subject_id, :teacher_id, :classroom_id, :day, :start_time, :end_time)";
    
                        $stmtInsert = $this->conn->prepare($insertQuery);
                        $stmtInsert->execute([
                            ':combination_id' => $row['combination_id'],
                            ':subject_id' => $row['subject_id'],
                            ':teacher_id' => $row['teacher_id'],
                            ':classroom_id' => $row['classroom_id'],
                            ':day' => $day,
                            ':start_time' => $start_time,
                            ':end_time' => $end_time
                        ]);
    
                        // **Update counters**
                        if ($row['subject_type'] === 'theory') {
                            $theory_count_per_day[$row['combination_id']][$day]++;
                        } elseif ($row['subject_type'] === 'lab') {
                            $lab_count_per_week[$row['combination_id']]++;
                        }
    
                        $schedule[$day][$slot]['teacher'][$row['teacher_id']] = true;
                        $schedule[$day][$slot]['classroom'][$row['classroom_id']] = true;
                        $schedule[$day][$slot]['combination'][$row['combination_id']] = true;
    
                        break 2; // Move to next subject after assignment
                    }
                }
            }
        }
        return true;
    }
    

    // ✅ Edit Timetable Entry
    public function updateTimetableEntry($id, $combination_id, $subject_id, $teacher_id, $classroom_id, $day, $start_time, $end_time) {
        $query = "UPDATE " . $this->table_name . " 
                  SET combination_id = :combination_id, subject_id = :subject_id, teacher_id = :teacher_id, 
                      classroom_id = :classroom_id, day = :day, start_time = :start_time, end_time = :end_time 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':combination_id' => $combination_id,
            ':subject_id' => $subject_id,
            ':teacher_id' => $teacher_id,
            ':classroom_id' => $classroom_id,
            ':day' => $day,
            ':start_time' => $start_time,
            ':end_time' => $end_time
        ]);
    }

    // Edit Single Entry of Combination
    public function editTimetableEntry($id, $subject_id, $teacher_id, $classroom_id) {
        $query = "UPDATE timetable SET subject_id = :subject_id, teacher_id = :teacher_id, classroom_id = :classroom_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':subject_id', $subject_id);
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->bindParam(':classroom_id', $classroom_id);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();

        
    }

    // ✅ Delete full timetable
    public function deleteTimetable() {
        $query = "DELETE FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    // ✅ Delete specific entry
    public function deleteTimetableEntry($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
    public function getTimetableGrid() {
        $query = "
            SELECT tt.day, tt.start_time, tt.end_time, 
                   s.name AS subject, t.name AS teacher, cr.room_no AS classroom , c.name AS combination,c.semester AS semester
            FROM timetable tt
            JOIN subjects s ON tt.subject_id = s.id
            JOIN teachers t ON tt.teacher_id = t.id
            JOIN classrooms cr ON tt.classroom_id = cr.id
            JOIN combinations c ON tt.combination_id = c.id
            ORDER BY tt.day, tt.start_time;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $timetable = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restructure data
        $formatted = [];
        foreach ($timetable as $row) {
            $time_slot = date("H:i", strtotime($row['start_time'])) . " - " . date("H:i", strtotime($row['end_time']));
            $formatted[$row['day']][$time_slot] = [
                "subject" => $row["subject"],
                "teacher" => $row["teacher"],
                "classroom" => $row["classroom"],
                "combination" => $row["combination"],
                "semester" => $row["semester"]
            ];
        }
        return $formatted;
    }
    public function getAllCombinations() {
        $query = "SELECT DISTINCT combination_id, name, semester, department FROM timetable join combinations on timetable.combination_id = combinations.id "; // Adjust table name if needed
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTimetableByCombination($combination_id) {
        // $query = "SELECT * FROM timetable WHERE combination_id = :combination_id ORDER BY day, start_time";
        // $stmt = $this->conn->prepare($query);
        // $stmt->bindParam(":combination_id", $combination_id);
        // $stmt->execute();
        
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);
        $query = "
    SELECT  tt.id AS entry_id, tt.day, tt.start_time, tt.end_time, 
           s.name AS subject, t.name AS teacher, cr.room_no AS classroom
    FROM timetable tt
    JOIN subjects s ON tt.subject_id = s.id
    JOIN teachers t ON tt.teacher_id = t.id
    JOIN classrooms cr ON tt.classroom_id = cr.id
    WHERE tt.combination_id = :combination_id
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":combination_id", $combination_id);
        $stmt->execute();
        $timetable = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restructure data
        $formatted = [];
        foreach ($timetable as $row) {
            $time_slot = date("H:i", strtotime($row['start_time'])) . " - " . date("H:i", strtotime($row['end_time']));
            $formatted[$row['day']][$time_slot] = [
                "entry_id" => $row['entry_id'],
                "subject" => $row["subject"],
                "teacher" => $row["teacher"],
                "classroom" => $row["classroom"]

            ];
        }
        return $formatted;
    }
        

}

?>
