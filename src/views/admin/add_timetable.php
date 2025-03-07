<?php
require_once 'config/database.php'; // Include your database connection
require_once 'classes/Timetable.php';

$db = new Database();
$conn = $db->getConnection();
$timetable = new Timetable($conn);

// Fetch required data for the dropdowns
$combinations = $conn->query("SELECT id, name FROM combinations")->fetchAll(PDO::FETCH_ASSOC);
$subjects = $conn->query("SELECT id, name FROM subjects")->fetchAll(PDO::FETCH_ASSOC);
$teachers = $conn->query("SELECT id, name FROM teachers")->fetchAll(PDO::FETCH_ASSOC);
$classrooms = $conn->query("SELECT id, room_no FROM classrooms")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $combination_id = $_POST['combination_id'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];
    $classroom_id = $_POST['classroom_id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = date("H:i:s", strtotime($start_time . " +1 hour"));

    // Check for conflicts (same teacher, classroom, or combination at the same time)
    $conflictCheck = $conn->prepare("
        SELECT * FROM timetable 
        WHERE day = ? AND start_time = ? 
        AND (teacher_id = ? OR classroom_id = ? OR combination_id = ?)
    ");
    $conflictCheck->execute([$day, $start_time, $teacher_id, $classroom_id, $combination_id]);

    if ($conflictCheck->rowCount() > 0) {
        $message = "<p style='color: red;'>❌ Conflict detected! Choose a different slot.</p>";
    } else {
        // Insert new timetable entry
        $stmt = $conn->prepare("
            INSERT INTO timetable (combination_id, subject_id, teacher_id, classroom_id, day, start_time, end_time) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        if ($stmt->execute([$combination_id, $subject_id, $teacher_id, $classroom_id, $day, $start_time, $end_time])) {
            $message = "<p style='color: green;'>✅ Timetable entry added successfully!</p>";
        } else {
            $message = "<p style='color: red;'>❌ Error adding timetable entry.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Timetable Entry</title>
    <style>
        body { font-family: Arial, sans-serif; }
        form { width: 400px; margin: auto; padding: 20px; border: 1px solid #ccc; }
        label { display: block; margin-top: 10px; }
        select, input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        button:hover { background: #218838; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Add Timetable Entry</h2>
    <?php if (isset($message)) echo $message; ?>
    <form method="POST">
        <label>Combination:</label>
        <select name="combination_id" required>
            <option value="">-- Select Combination --</option>
            <?php foreach ($combinations as $c) { echo "<option value='{$c['id']}'>{$c['name']}</option>"; } ?>
        </select>

        <label>Subject:</label>
        <select name="subject_id" required>
            <option value="">-- Select Subject --</option>
            <?php foreach ($subjects as $s) { echo "<option value='{$s['id']}'>{$s['name']}</option>"; } ?>
        </select>

        <label>Teacher:</label>
        <select name="teacher_id" required>
            <option value="">-- Select Teacher --</option>
            <?php foreach ($teachers as $t) { echo "<option value='{$t['id']}'>{$t['name']}</option>"; } ?>
        </select>

        <label>Classroom:</label>
        <select name="classroom_id" required>
            <option value="">-- Select Classroom --</option>
            <?php foreach ($classrooms as $cl) { echo "<option value='{$cl['id']}'>{$cl['room_no']}</option>"; } ?>
        </select>

        <label>Day:</label>
        <select name="day" required>
            <option value="">-- Select Day --</option>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
        </select>

        <label>Start Time:</label>
        <input type="time" name="start_time" required>

        <button type="submit">Add Timetable Entry</button>
    </form>
</body>
</html>
