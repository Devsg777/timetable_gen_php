<?php
class Timetable
{
    private $conn;
    private $table_name = "timetable";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ✅ Get full timetable


    public function generateTimetable()
    {
        // Step 1: Fetch all data
        $query = "
            SELECT c.id AS combination_id, c.sections, s.id AS subject_id, s.name AS subject_name, s.type AS subject_type, 
                   s.min_classes_per_week, s.duration, t.id AS teacher_id, t.min_class_hours_week, t.min_lab_hours_week, 
                   cl.id AS classroom_id
            FROM combinations c
            JOIN subjects s ON s.combination_id = c.id
            JOIN teacher_subjects ts ON ts.subject_id = s.id
            JOIN teachers t ON t.id = ts.teacher_id
            JOIN classrooms cl ON cl.type = s.type
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Step 2: Initialize
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $theory_slots = ['09:00:00', '10:00:00', '11:00:00', '12:00:00', '02:00:00', '03:00:00', '04:00:00', '05:00:00'];
        $lab_blocks = [
            ['start' => '09:00:00', 'end' => '12:00:00'],
            ['start' => '02:00:00', 'end' => '05:00:00']
        ];
    
        $teacher_theory_hours = [];
        $teacher_lab_hours = [];
        $section_subject_schedule_count = [];
        $teacher_avail = [];
        $classroom_avail = [];
        $section_avail = [];
        $section_day_class_count = []; // New: Track max 5 theory classes per day per section
    
        $used_lab_slots = [];
    
        // Initialize counters
        foreach ($data as $row) {
            $sections = explode(',', str_replace(['[', ']', '"', '\''], '', $row['sections']));
            foreach ($sections as $section) {
                $section = trim($section);
                $section_subject_schedule_count[$row['subject_id']][$section] = 0;
            }
            $teacher_theory_hours[$row['teacher_id']] = 0;
            $teacher_lab_hours[$row['teacher_id']] = 0;
        }
    
        shuffle($data);
    
        foreach ($data as $row) {
            $combination_id = $row['combination_id'];
            $subject_id = $row['subject_id'];
            $subject_name = $row['subject_name'];
            $subject_type = $row['subject_type'];
            $teacher_id = $row['teacher_id'];
            $classroom_id = $row['classroom_id'];
            $duration = $row['duration'];
            $min_classes = $row['min_classes_per_week'];
    
            $sections = explode(',', str_replace(['[', ']', '"', '\''], '', $row['sections']));
    
            if ($subject_type === 'lab') {
                // LAB SCHEDULING FIXED BLOCK
                $sections = array_map('trim', explode(',', str_replace(['[', ']', '"', '\''], '', $row['sections'])));
                $lab_assigned = false;
            
                foreach ($days as $day) {
                    foreach ($lab_blocks as $block) {
                        $slot_key = "$combination_id-$day-{$block['start']}";
                        if (isset($used_lab_slots[$slot_key])) continue;
            
                        $all_sections_schedulable = true;
            
                        // Check if this time is free for all sections (regardless of subject)
                        foreach ($sections as $section) {
                            for ($h = 0; $h < 3; $h++) {
                                $t = date("H:i:s", strtotime($block['start']) + $h * 3600);
                                if (isset($section_avail["$combination_id-$section"][$day][$t])) {
                                    $all_sections_schedulable = false;
                                    break 2;
                                }
                            }
                        }
            
                        if (!$all_sections_schedulable) continue;
            
                        // Now for each section, try to assign one lab subject still needing slots
                        foreach ($sections as $section) {
                            $section = trim($section);
            
                            // Find a lab subject needing assignment for this section
                            $lab_subject_query = "
                                SELECT s.id AS subject_id, s.name AS subject_name, s.duration, s.min_classes_per_week,
                                       ts.teacher_id, cl.id AS classroom_id
                                FROM subjects s
                                JOIN teacher_subjects ts ON ts.subject_id = s.id
                                JOIN teachers t ON t.id = ts.teacher_id
                                JOIN classrooms cl ON cl.type = 'lab'
                                WHERE s.type = 'lab' AND s.combination_id = :comb_id
                            ";
                            $stmtLab = $this->conn->prepare($lab_subject_query);
                            $stmtLab->execute([':comb_id' => $combination_id]);
                            $lab_subjects = $stmtLab->fetchAll(PDO::FETCH_ASSOC);
            
                            shuffle($lab_subjects); // Randomize selection
            
                            foreach ($lab_subjects as $lab_sub) {
                                $sid = $lab_sub['subject_id'];
                                $tid = $lab_sub['teacher_id'];
                                $clid = $lab_sub['classroom_id'];
                                $min_lab = $lab_sub['min_classes_per_week'];
            
                                if (($section_subject_schedule_count[$sid][$section] ?? 0) >= $min_lab) continue;
            
                                // Check availability of teacher and room for 3 hours
                                $conflict = false;
                                for ($h = 0; $h < 3; $h++) {
                                    $t = date("H:i:s", strtotime($block['start']) + $h * 3600);
                                    if (
                                        isset($teacher_avail[$tid][$day][$t]) ||
                                        isset($classroom_avail[$clid][$day][$t])
                                    ) {
                                        $conflict = true;
                                        break;
                                    }
                                }
            
                                if ($conflict) continue;
            
                                // Assign lab
                                $stmtIns = $this->conn->prepare("
                                    INSERT INTO timetable (combination_id, section, subject_id, teacher_id, classroom_id, day, start_time, end_time)
                                    VALUES (:combination_id, :section, :subject_id, :teacher_id, :classroom_id, :day, :start_time, :end_time)
                                ");
                                $stmtIns->execute([
                                    ':combination_id' => $combination_id,
                                    ':section' => $section,
                                    ':subject_id' => $sid,
                                    ':teacher_id' => $tid,
                                    ':classroom_id' => $clid,
                                    ':day' => $day,
                                    ':start_time' => $block['start'],
                                    ':end_time' => $block['end']
                                ]);
            
                                for ($h = 0; $h < 3; $h++) {
                                    $t = date("H:i:s", strtotime($block['start']) + $h * 3600);
                                    $teacher_avail[$tid][$day][$t] = true;
                                    $classroom_avail[$clid][$day][$t] = true;
                                    $section_avail["$combination_id-$section"][$day][$t] = true;
                                }
            
                                $teacher_lab_hours[$tid] += 3;
                                $section_subject_schedule_count[$sid][$section] = ($section_subject_schedule_count[$sid][$section] ?? 0) + 1;
                                break;
                            }
                        }
            
                        // Mark this time block as used for the combination
                        $used_lab_slots[$slot_key] = true;
                        $lab_assigned = true;
                        break 2;
                    }
                }
            
                continue; // move to next subject row
            } else {
                // Theory
                foreach ($days as $day) {
                    shuffle($theory_slots);
                    foreach ($theory_slots as $start) {
                        $end = date("H:i:s", strtotime($start) + ($duration * 3600));
                        shuffle($sections);
                        foreach ($sections as $section) {
                            $section = trim($section);
                            if ($section_subject_schedule_count[$subject_id][$section] >= $min_classes) continue;
    
                            if (
                                isset($section_day_class_count["$combination_id-$section"][$day]) &&
                                $section_day_class_count["$combination_id-$section"][$day] >= 4
                            ) continue;
    
                            $conflict = false;
                            for ($d = 0; $d < $duration; $d++) {
                                $slot = date("H:i:s", strtotime($start) + ($d * 3600));
                                if (
                                    isset($teacher_avail[$teacher_id][$day][$slot]) ||
                                    isset($classroom_avail[$classroom_id][$day][$slot]) ||
                                    isset($section_avail["$combination_id-$section"][$day][$slot])
                                ) {
                                    $conflict = true;
                                    break;
                                }
                            }
    
                            if (!$conflict) {
                                $stmtIns = $this->conn->prepare("
                                    INSERT INTO timetable (combination_id, section, subject_id, teacher_id, classroom_id, day, start_time, end_time)
                                    VALUES (:combination_id, :section, :subject_id, :teacher_id, :classroom_id, :day, :start_time, :end_time)
                                ");
                                $stmtIns->execute([
                                    ':combination_id' => $combination_id,
                                    ':section' => $section,
                                    ':subject_id' => $subject_id,
                                    ':teacher_id' => $teacher_id,
                                    ':classroom_id' => $classroom_id,
                                    ':day' => $day,
                                    ':start_time' => $start,
                                    ':end_time' => $end
                                ]);
    
                                for ($d = 0; $d < $duration; $d++) {
                                    $slot = date("H:i:s", strtotime($start) + ($d * 3600));
                                    $teacher_avail[$teacher_id][$day][$slot] = true;
                                    $classroom_avail[$classroom_id][$day][$slot] = true;
                                    $section_avail["$combination_id-$section"][$day][$slot] = true;
                                }
    
                                $teacher_theory_hours[$teacher_id] += $duration;
                                $section_subject_schedule_count[$subject_id][$section]++;
                                $section_day_class_count["$combination_id-$section"][$day] = ($section_day_class_count["$combination_id-$section"][$day] ?? 0) + 1;
                            }
    
                            if ($section_subject_schedule_count[$subject_id][$section] >= $min_classes) continue;
                        }
    
                        $done = true;
                        foreach ($sections as $section) {
                            if ($section_subject_schedule_count[$subject_id][trim($section)] < $min_classes) {
                                $done = false;
                                break;
                            }
                        }
                        if ($done) break;
                    }
                }
            }
        }
    
        return true;
    }
    public function addTimetableEntry($day, $time, $islab, $combination_id, $subject_id, $teacher_id, $classroom_id, $section)
    {
        if (!$this->conn) {
            die("Database connection is missing.");
        }
    
        // Debug: Confirm received input
        echo "Received Input: \n";
        var_dump(compact('day', 'time', 'islab', 'combination_id', 'subject_id', 'teacher_id', 'classroom_id', 'section'));
    
        // Split time range into start and end times
        $time_range = explode(" - ", $time);
        $start_time = $time_range[0] . ":00"; // Format as HH:MM:SS
        $end_time = $time_range[1] . ":00";
    
        // Ensure lab sessions are handled correctly
        if ($islab === "on" || $islab === 1) {
            $lab_duration = strtotime($start_time) + (2 * 60 * 60); // Add 2 hours
            $end_time = date("H:i:s", $lab_duration);
        }
    
        // Convert to integers
        $combination_id = (int)$combination_id;
        $subject_id = (int)$subject_id;
        $teacher_id = (int)$teacher_id;
        $classroom_id = (int)$classroom_id;
    
        // Check for teacher or classroom conflict
        $conflictQuery = "
            SELECT * FROM timetable 
            WHERE day = :day
            AND (
                (teacher_id = :teacher_id OR classroom_id = :classroom_id)
                AND (
                    (start_time < :end_time AND end_time > :start_time)
                )
            )
        ";
    
        $stmtCheck = $this->conn->prepare($conflictQuery);
        $stmtCheck->execute([
            ':day' => $day,
            ':teacher_id' => $teacher_id,
            ':classroom_id' => $classroom_id,
            ':start_time' => $start_time,
            ':end_time' => $end_time
        ]);
    
        if ($stmtCheck->rowCount() > 0) {
            echo "Conflict detected: Teacher or Classroom already booked.\n";
            return false;
        }
    
        // No conflict, proceed with insertion
        $insertQuery = "
            INSERT INTO timetable (combination_id, section, subject_id, teacher_id, classroom_id, day, start_time, end_time)
            VALUES (:combination_id, :section, :subject_id, :teacher_id, :classroom_id, :day, :start_time, :end_time)
        ";
    
        $stmtInsert = $this->conn->prepare($insertQuery);
        $stmtInsert->execute([
            ':combination_id' => $combination_id,
            ':section' => $section,
            ':subject_id' => $subject_id,
            ':teacher_id' => $teacher_id,
            ':classroom_id' => $classroom_id,
            ':day' => $day,
            ':start_time' => $start_time,
            ':end_time' => $end_time
        ]);
    
        echo "Timetable entry added successfully.\n";
        return true;
    }
    
    // Edit Single Entry of Combination
    public function editTimetableEntry($id, $subject_id, $teacher_id, $classroom_id)
{
    if (!$this->conn) {
        die("Database connection is missing.");
    }

    // Fetch the current start_time and end_time of the entry being edited.
    $getTimeQuery = "SELECT day, start_time, end_time FROM timetable WHERE id = :id";
    $stmtGetTime = $this->conn->prepare($getTimeQuery);
    $stmtGetTime->bindParam(':id', $id);
    $stmtGetTime->execute();
    $timeData = $stmtGetTime->fetch(PDO::FETCH_ASSOC);

    if (!$timeData) {
        echo "Error: Timetable entry not found.\n";
        return false;
    }

    $day = $timeData['day'];
    $start_time = $timeData['start_time'];
    $end_time = $timeData['end_time'];

    // Check for conflicts (room and teacher availability)
    $conflictQuery = "SELECT id FROM timetable 
                    WHERE day = :day 
                    AND ((start_time <= :start_time AND end_time >= :start_time) OR (start_time <= :end_time AND end_time >= :end_time))
                    AND (classroom_id = :classroom_id OR teacher_id = :teacher_id)
                    AND id != :id"; // Exclude the entry being edited

    $stmtConflict = $this->conn->prepare($conflictQuery);
    $stmtConflict->execute([
        ':day' => $day,
        ':start_time' => $start_time,
        ':end_time' => $end_time,
        ':classroom_id' => $classroom_id,
        ':teacher_id' => $teacher_id,
        ':id' => $id,
    ]);

    if ($stmtConflict->fetch()) {
        echo "Error: Room or teacher is not available for the selected time.\n";
        return false; // Indicate failure
    }

    $query = "UPDATE timetable SET subject_id = :subject_id, teacher_id = :teacher_id, classroom_id = :classroom_id WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':subject_id', $subject_id);
    $stmt->bindParam(':teacher_id', $teacher_id);
    $stmt->bindParam(':classroom_id', $classroom_id);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}
    // ✅ Delete full timetable
    public function deleteTimetable()
    {
        $query = "DELETE FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    // ✅ Delete specific entry
    public function deleteTimetableEntry($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
    public function getTimetableGrid()
    {
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
    public function getAllCombinations()
    {
        $query = "SELECT DISTINCT combination_id, name, semester, department ,sections FROM timetable join combinations on timetable.combination_id = combinations.id "; // Adjust table name if needed
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTimetableByCombination($combination_id,$section)
    {
        $query = "
            SELECT tt.id AS entry_id, tt.day, tt.start_time, tt.end_time, 
                   s.name AS subject, t.name AS teacher, cr.room_no AS classroom
            FROM timetable tt
            JOIN subjects s ON tt.subject_id = s.id
            JOIN teachers t ON tt.teacher_id = t.id
            JOIN classrooms cr ON tt.classroom_id = cr.id
            WHERE tt.combination_id = :combination_id AND tt.section = :section
            ORDER BY FIELD(tt.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), tt.start_time
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":combination_id", $combination_id);
        $stmt->bindParam(":section", $section);
        $stmt->execute();
        $timetable = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Restructure data for 1-hour time slot rendering
        $formatted = [];
        foreach ($timetable as $row) {
            $start = strtotime($row['start_time']);
            $end = strtotime($row['end_time']);
    
            // Split into 1-hour blocks
            while ($start < $end) {
                $slot_start = date("H:i", $start);
                $slot_end = date("H:i", strtotime("+1 hour", $start));
                $time_slot = $slot_start . " - " . $slot_end;
    
                $formatted[$row['day']][$time_slot] = [
                    "entry_id" => $row['entry_id'],
                    "subject" => $row["subject"],
                    "teacher" => $row["teacher"],
                    "classroom" => $row["classroom"]
                ];
    
                $start = strtotime("+1 hour", $start);
            }
        }
    
        return $formatted;
    }

    public function getTimetableByCombination_Data($combination_id,$section)
    {
        $query = "
            SELECT tt.id AS entry_id, tt.day, tt.start_time, tt.end_time, 
                   s.name AS subject, t.name AS teacher, cr.room_no AS classroom
            FROM timetable tt
            JOIN subjects s ON tt.subject_id = s.id
            JOIN teachers t ON tt.teacher_id = t.id
            JOIN classrooms cr ON tt.classroom_id = cr.id
            WHERE tt.combination_id = :combination_id AND tt.section = :section
            ORDER BY FIELD(tt.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), tt.start_time
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":combination_id", $combination_id);
        $stmt->bindParam(":section", $section);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTimetableByTeacherId($teacher_id)
    {
        // $query = "SELECT * FROM timetable WHERE combination_id = :combination_id ORDER BY day, start_time";
        // $stmt = $this->conn->prepare($query);
        // $stmt->bindParam(":combination_id", $combination_id);
        // $stmt->execute();
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);

        $query = "
    SELECT  tt.id AS entry_id, tt.day, tt.start_time, tt.end_time, 
           s.name AS subject, c.name AS combination, c.semester AS semester, cr.room_no AS classroom, tt.section
    FROM timetable tt
    JOIN subjects s ON tt.subject_id = s.id
    JOIN combinations c ON tt.combination_id = c.id
    JOIN classrooms cr ON tt.classroom_id = cr.id
    WHERE tt.teacher_id = :teacher_id
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id);
        $stmt->execute();
        $timetable = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restructure data
        $formatted = [];
        foreach ($timetable as $row) {
            $time_slot = date("H:i", strtotime($row['start_time'])) . " - " . date("H:i", strtotime($row['end_time']));
            $formatted[$row['day']][$time_slot] = [
                "entry_id" => $row['entry_id'],
                "subject" => $row["subject"],
                "combination" => $row["combination"],
                "semester" => $row['semester'],
                "classroom" => $row["classroom"],
                "section" => $row['section'],
                    "day" => $row['day'],
                "start_time" => $row['start_time'],
                "end_time" => $row['end_time']


            ];
        }
        return $formatted;
    }
    public function getTimetableByTeacherId_Data($teacher_id)
    {


        $query = "
    SELECT  tt.id AS entry_id, tt.day as day, tt.start_time AS start_time, tt.end_time AS end_time, 
           s.name AS subject, c.name AS combination, c.semester AS semester, cr.room_no AS classroom, tt.section
    FROM timetable tt
    JOIN subjects s ON tt.subject_id = s.id
    JOIN combinations c ON tt.combination_id = c.id
    JOIN classrooms cr ON tt.classroom_id = cr.id
    WHERE tt.teacher_id = :teacher_id
    ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":teacher_id", $teacher_id);
        $stmt->execute();
        $timetable = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Restructure data

        return $timetable;
    }
    public function getEntryByTimeSlot($combination_id, $day, $time_slot) {
        // Assume $time_slot = "10:00 - 11:00"
        list($start, $end) = explode(' - ', $time_slot);
    
        $query = "SELECT * FROM timetable WHERE combination_id = :combination_id AND day = :day AND start_time = :start_time AND end_time = :end_time";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':combination_id', $combination_id);
        $stmt->bindParam(':day', $day);
        $stmt->bindParam(':start_time', $start);
        $stmt->bindParam(':end_time', $end);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    public function updateEntryTimeSlot($entry_id, $day, $time) {
        // Split time range into start and end times
        $time_range = explode(" - ", $time); // Splitting input
        $start_time = $time_range[0] . ":00"; // Format as HH:MM:SS
        $end_time = $time_range[1] . ":00"; // Format as HH:MM:SS
    
        $query = "UPDATE timetable SET day = :day, start_time = :start_time, end_time = :end_time WHERE id = :entry_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':day', $day);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':entry_id', $entry_id);
    
        return $stmt->execute();
    }
    
}