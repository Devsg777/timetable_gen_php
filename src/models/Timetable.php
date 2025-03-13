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
    // public function generateTimetable() {
    //     $query = "SELECT c.id AS combination_id, s.id AS subject_id, t.id AS teacher_id, cl.id AS classroom_id, s.type AS subject_type 
    //         FROM combinations c
    //         JOIN subjects s ON s.combination_id = c.id
    //         JOIN teacher_subjects ts ON ts.subject_id = s.id
    //         JOIN teachers t ON ts.teacher_id = t.id
    //         JOIN classrooms cl
    //         ORDER BY RAND()";
    
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->execute();
    //     $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    //     $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    //     $time_slots = ['10:00:00', '11:00:00', '12:00:00','02:00:00', '03:00:00', '04:00:00','05:00:00'];
    
    //     $schedule = [];
    //     $theory_count_per_day = [];
    //     $lab_count_per_week = [];
    
    //     foreach ($all_data as $row) {
    //         foreach ($days as $day) {
    //             // Initialize counters if not set
    //             if (!isset($theory_count_per_day[$row['combination_id']][$day])) {
    //                 $theory_count_per_day[$row['combination_id']][$day] = 0;
    //             }
    //             if (!isset($lab_count_per_week[$row['combination_id']])) {
    //                 $lab_count_per_week[$row['combination_id']] = 0;
    //             }
    
    //             // **Enforce class limits**
    //             if ($row['subject_type'] === 'theory' && $theory_count_per_day[$row['combination_id']][$day] >= 4) {
    //                 continue; // Skip if 4 theory classes are already assigned for this day
    //             }
    //             if ($row['subject_type'] === 'lab' && $lab_count_per_week[$row['combination_id']] >= 2) {
    //                 continue; // Skip if 2 lab classes are already assigned for this week
    //             }
    
    //             foreach ($time_slots as $slot) {
    //                 if (!isset($schedule[$day][$slot]['teacher'][$row['teacher_id']]) &&
    //                     !isset($schedule[$day][$slot]['classroom'][$row['classroom_id']]) &&
    //                     !isset($schedule[$day][$slot]['combination'][$row['combination_id']])) {
    
    //                     $start_time = isset($slot) ? $slot : '10:00:00';
    //                     $end_time = date("H:i:s", strtotime($start_time) + 3600); // Add 1 hour
    
    //                     $insertQuery = "
    //                         INSERT INTO timetable (combination_id, subject_id, teacher_id, classroom_id, day, start_time, end_time) 
    //                         VALUES (:combination_id, :subject_id, :teacher_id, :classroom_id, :day, :start_time, :end_time)";
    
    //                     $stmtInsert = $this->conn->prepare($insertQuery);
    //                     $stmtInsert->execute([
    //                         ':combination_id' => $row['combination_id'],
    //                         ':subject_id' => $row['subject_id'],
    //                         ':teacher_id' => $row['teacher_id'],
    //                         ':classroom_id' => $row['classroom_id'],
    //                         ':day' => $day,
    //                         ':start_time' => $start_time,
    //                         ':end_time' => $end_time
    //                     ]);
    
    //                     // **Update counters**
    //                     if ($row['subject_type'] === 'theory') {
    //                         $theory_count_per_day[$row['combination_id']][$day]++;
    //                     } elseif ($row['subject_type'] === 'lab') {
    //                         $lab_count_per_week[$row['combination_id']]++;
    //                     }
    
    //                     $schedule[$day][$slot]['teacher'][$row['teacher_id']] = true;
    //                     $schedule[$day][$slot]['classroom'][$row['classroom_id']] = true;
    //                     $schedule[$day][$slot]['combination'][$row['combination_id']] = true;
    
    //                     break 2; // Move to next subject after assignment
    //                 }
    //             }
    //         }
    //     }
    //     return true;
    // }
    
    public function generateTimetable() {
        // Fetch all necessary data
        $query = "
            SELECT c.id AS combination_id, s.id AS subject_id, t.id AS teacher_id, cl.id AS classroom_id, 
                   s.type AS subject_type, s.min_classes_per_week, t.min_class_hours_week, t.min_lab_hours_week
            FROM combinations c
            JOIN subjects s ON s.combination_id = c.id
            JOIN teacher_subjects ts ON ts.subject_id = s.id
            JOIN teachers t ON ts.teacher_id = t.id
            JOIN classrooms cl ON cl.type = s.type
            ORDER BY c.id, s.type";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Define days and time slots
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $theory_time_slots = ['10:00:00', '11:00:00', '12:00:00', '02:00:00', '03:00:00', '04:00:00'];
        $lab_time_slots = ['10:00:00']; // Labs are 3-hour sessions, so only one slot is needed
    
        // Initialize data structures
        $schedule = []; // To store the final timetable
        $teacher_load = []; // To track teacher workload
        $classroom_availability = []; // To track classroom availability
        $subject_assignment_count = []; // To track how many times a subject has been assigned
    
        // Initialize counters
        foreach ($all_data as $row) {
            $teacher_load[$row['teacher_id']] = 0;
            $subject_assignment_count[$row['subject_id']] = 0;
        }
    
        // Shuffle the data to assign classes randomly
        shuffle($all_data);
    
        // Generate timetable
        foreach ($all_data as $row) {
            $combination_id = $row['combination_id'];
            $subject_id = $row['subject_id'];
            $teacher_id = $row['teacher_id'];
            $classroom_id = $row['classroom_id'];
            $subject_type = $row['subject_type'];
            $min_classes_per_week = $row['min_classes_per_week'];
    
            // Skip if the subject has already been assigned the required number of times
            if ($subject_assignment_count[$subject_id] >= $min_classes_per_week) {
                continue;
            }
    
            // Determine time slots based on subject type
            $time_slots = ($subject_type === 'theory') ? $theory_time_slots : $lab_time_slots;
    
            // Shuffle days and time slots to assign classes randomly
            shuffle($days);
            shuffle($time_slots);
    
            foreach ($days as $day) {
                foreach ($time_slots as $slot) {
                    // Check if the teacher is available
                    if (isset($schedule[$day][$slot]['teacher'][$teacher_id])) {
                        continue; // Teacher is already assigned to another class at this time
                    }
    
                    // Check if the classroom is available
                    if (isset($schedule[$day][$slot]['classroom'][$classroom_id])) {
                        continue; // Classroom is already occupied at this time
                    }
    
                    // Check if the combination is already assigned at this time
                    if (isset($schedule[$day][$slot]['combination'][$combination_id])) {
                        continue; // Combination already has a class at this time
                    }
    
                    // For labs, ensure no other classes are scheduled during the 3-hour session
                    if ($subject_type === 'lab') {
                        $lab_end_time = date("H:i:s", strtotime($slot) + 10800); // 3 hours later
                        $is_lab_slot_available = true;
    
                        for ($t = strtotime($slot); $t < strtotime($lab_end_time); $t += 3600) {
                            $current_slot = date("H:i:s", $t);
                            if (isset($schedule[$day][$current_slot]['classroom'][$classroom_id])) {
                                $is_lab_slot_available = false;
                                break;
                            }
                        }
    
                        if (!$is_lab_slot_available) {
                            continue; // Lab slot is not available
                        }
                    }
    
                    // Assign the class
                    $start_time = $slot;
                    $end_time = ($subject_type === 'theory') ? date("H:i:s", strtotime($slot) + 3600) : date("H:i:s", strtotime($slot) + 10800);
    
                    $insertQuery = "
                        INSERT INTO timetable (combination_id, subject_id, teacher_id, classroom_id, day, start_time, end_time) 
                        VALUES (:combination_id, :subject_id, :teacher_id, :classroom_id, :day, :start_time, :end_time)";
    
                    $stmtInsert = $this->conn->prepare($insertQuery);
                    $stmtInsert->execute([
                        ':combination_id' => $combination_id,
                        ':subject_id' => $subject_id,
                        ':teacher_id' => $teacher_id,
                        ':classroom_id' => $classroom_id,
                        ':day' => $day,
                        ':start_time' => $start_time,
                        ':end_time' => $end_time
                    ]);
    
                    // Update counters
                    $teacher_load[$teacher_id]++;
                    $subject_assignment_count[$subject_id]++;
                    $schedule[$day][$slot]['teacher'][$teacher_id] = true;
                    $schedule[$day][$slot]['classroom'][$classroom_id] = true;
                    $schedule[$day][$slot]['combination'][$combination_id] = true;
    
                    // For labs, mark the entire 3-hour slot as occupied
                    if ($subject_type === 'lab') {
                        for ($t = strtotime($slot); $t < strtotime($lab_end_time); $t += 3600) {
                            $current_slot = date("H:i:s", $t);
                            $schedule[$day][$current_slot]['classroom'][$classroom_id] = true;
                        }
                    }
    
                    break 2; // Move to the next subject after assignment
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
