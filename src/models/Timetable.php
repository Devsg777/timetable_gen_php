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
    public function getAllTimetables()
    {
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

    public function generateTimetable()
{
    // Fetch all necessary data, including subject duration
    $query = "
        SELECT c.id AS combination_id, s.id AS subject_id, t.id AS teacher_id, cl.id AS classroom_id,
               s.type AS subject_type, s.min_classes_per_week, t.min_class_hours_week, t.min_lab_hours_week, s.duration
        FROM combinations c
        JOIN subjects s ON s.combination_id = c.id
        JOIN teacher_subjects ts ON ts.subject_id = s.id
        JOIN teachers t ON ts.teacher_id = t.id
        JOIN classrooms cl ON cl.type = s.type
        ORDER BY c.id, s.type";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $all_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Define days and time slots (1-hour slots for simplicity and flexibility)
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $time_slots = ['09:00:00', '10:00:00', '11:00:00', '12:00:00', '14:00:00', '15:00:00', '16:00:00'];

    // Initialize data structures
    $schedule = []; // To store the final timetable
    $teacher_availability = []; // [teacher_id][day][time_slot] = true if busy
    $classroom_availability = []; // [classroom_id][day][time_slot] = true if occupied
    $combination_availability = []; // [combination_id][day][time_slot] = true if has a class
    $subject_assignment_count = []; // To track how many times a subject has been assigned

    // Initialize counters
    foreach ($all_data as $row) {
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
        $duration = $row['duration'];

        // Skip if the subject has already been assigned the required number of times
        if ($subject_assignment_count[$subject_id] >= $min_classes_per_week) {
            continue;
        }

        // Shuffle days and time slots
        shuffle($days);
        shuffle($time_slots);

        foreach ($days as $day) {
            foreach ($time_slots as $start_time_slot) {
                $is_available = true;
                $end_time_slot = date("H:i:s", strtotime($start_time_slot) + ($duration * 3600));
                $current_time = $start_time_slot;

                // Check availability for the entire duration
                for ($i = 0; $i < $duration; $i++) {
                    $check_slot = date("H:i:s", strtotime($start_time_slot) + ($i * 3600));

                    if (isset($teacher_availability[$teacher_id][$day][$check_slot]) ||
                        isset($classroom_availability[$classroom_id][$day][$check_slot]) ||
                        isset($combination_availability[$combination_id][$day][$check_slot])) {
                        $is_available = false;
                        break;
                    }
                }

                if ($is_available) {
                    // Assign the class
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
                        ':start_time' => $start_time_slot,
                        ':end_time' => $end_time_slot
                    ]);

                    // Mark the time slots as occupied for the duration
                    for ($i = 0; $i < $duration; $i++) {
                        $occupied_slot = date("H:i:s", strtotime($start_time_slot) + ($i * 3600));
                        $teacher_availability[$teacher_id][$day][$occupied_slot] = true;
                        $classroom_availability[$classroom_id][$day][$occupied_slot] = true;
                        $combination_availability[$combination_id][$day][$occupied_slot] = true;
                    }

                    // Update subject assignment count
                    $subject_assignment_count[$subject_id]++;
                    break 2; // Move to the next subject
                }
            }
        }
    }

    return true;
}

    // ✅ Edit Timetable Entry
    public function addTimetableEntry($day, $time, $islab, $combination_id, $subject_id, $teacher_id, $classroom_id)
    {
        if (!$this->conn) {
            die("Database connection is missing.");
        }

        // Debug: Confirm received input
        echo "Received Input: \n";
        var_dump(compact('day', 'time', 'islab', 'combination_id', 'subject_id', 'teacher_id', 'classroom_id'));

        // Split time range into start and end times
        $time_range = explode(" - ", $time); // Splitting input
        $start_time = $time_range[0] . ":00"; // Format as HH:MM:SS
        $end_time = $time_range[1] . ":00"; // Format as HH:MM:SS

        // Ensure lab sessions are handled correctly
        if (isset($data['islab']) && $data['islab'] === "on") {
            // Define LAB duration logic correctly
            $lab_duration = strtotime($start_time) + (2 * 60 * 60); // Adds 2 hours if it's a lab
            $end_time = date("H:i:s", $lab_duration);
        }

        // Debug: Confirm time conversion
        echo "Converted Start Time: $start_time, End Time: $end_time\n";
        //convert string to int
        $combination_id = (int)$combination_id;
        $subject_id = (int)$subject_id;
        $teacher_id = (int)$teacher_id;
        $classroom_id = (int)$classroom_id;

        // SQL Query
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
    }




    // Edit Single Entry of Combination
    public function editTimetableEntry($id, $subject_id, $teacher_id, $classroom_id)
    {
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
        $query = "SELECT DISTINCT combination_id, name, semester, department FROM timetable join combinations on timetable.combination_id = combinations.id "; // Adjust table name if needed
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTimetableByCombination($combination_id)
    {
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

    public function getTimetableByTeacherId($teacher_id)
    {
        // $query = "SELECT * FROM timetable WHERE combination_id = :combination_id ORDER BY day, start_time";
        // $stmt = $this->conn->prepare($query);
        // $stmt->bindParam(":combination_id", $combination_id);
        // $stmt->execute();
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);

        $query = "
    SELECT  tt.id AS entry_id, tt.day, tt.start_time, tt.end_time, 
           s.name AS subject, c.name AS combination, c.semester AS semester, cr.room_no AS classroom
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
                "classroom" => $row["classroom"]
            ];
        }
        return $formatted;
    }
    public function getTimetableByTeacherId_Data($teacher_id)
    {


        $query = "
    SELECT  tt.id AS entry_id, tt.day as day, tt.start_time AS start_time, tt.end_time AS end_time, 
           s.name AS subject, c.name AS combination, c.semester AS semester, cr.room_no AS classroom
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
