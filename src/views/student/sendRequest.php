<?php
include_once '../../config/database.php';
include_once '../../models/Timetable.php'; // Assuming you have a Timetable model
include_once '../../models/Subject.php';
include_once '../../models/Teacher.php';
include_once '../../models/Classroom.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php"); // Adjust path as needed
    exit();
}

$database = new Database();
$db = $database->getConnection();

$timetableModel = new Timetable($db);
$subjectModel = new Subject($db);
$teacherModel = new Teacher($db);
$classroomModel = new Classroom($db);

// Fetch the student's combination and then their timetable entries
$studentId = $_SESSION['student_id'];
// Assuming you have a way to get the student's combination_id
// You might need to fetch this from a 'students' table or session data
// For example:
// $studentQuery = "SELECT combination_id FROM students WHERE id = :student_id";
// $studentStmt = $db->prepare($studentQuery);
// $studentStmt->bindParam(':student_id', $studentId);
// $studentStmt->execute();
// $studentData = $studentStmt->fetch(PDO::FETCH_ASSOC);
// $studentCombinationId = $studentData['combination_id'] ?? null;

// For now, let's assume you have the combination ID in the session or can easily retrieve it.
// Replace 'YOUR_STUDENT_COMBINATION_ID' with the actual way to get it.
$studentCombinationId = $_SESSION['student_combination'] ?? null;
$studentSection = $_SESSION['student_section'] ?? null; // Assuming you have the section in session

$studentTimetable = [];
if ($studentCombinationId) {
    $studentTimetable = $timetableModel->getTimetableByCombination_Data($studentCombinationId,$studentSection);
}

// Fetch all subjects for the proposed changes dropdown
$subjects = $subjectModel->getAllSubjects();

?>

<?php include "header.php"; ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-lg">
    <?php if($_GET['success'] ?? false) {

echo '<div class="alert alert-success bg-green-200 p-3 m-3 text-center text-green-600 border ">Request sent successfully!</div>';
} elseif($_GET['error'] ?? false) {
echo '<div class="alert alert-danger bg-red-200 p-3 m-3 text-center text-red-600 border">Error sending request. Please try again.</div>';
} ?>
        <h2 class="text-2xl font-semibold mb-4">Send Class Change Request</h2>
        <form action="../../controllers/RequestController.php" method="POST">
            <div class="mb-4">
                <label for="existing_timetable_id" class="block text-gray-700">Select Class to Request Change For:</label>
                <select name="existing_timetable_id" id="existing_timetable_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Select a Class --</option>
                    <?php if (!empty($studentTimetable)): ?>
                        <?php foreach ($studentTimetable as $entry): ?>
                            <option value="<?= $entry['entry_id']; ?>">
                                <?= htmlspecialchars($entry['subject']) ?> (<?= htmlspecialchars($entry['day']) ?>
                                <?= htmlspecialchars(date('h:i A', strtotime($entry['start_time']))) ?> -
                                <?= htmlspecialchars(date('h:i A', strtotime($entry['end_time']))) ?>,
                                Teacher: <?= htmlspecialchars($entry['teacher']) ?>,
                                Room: <?= htmlspecialchars($entry['classroom']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No classes scheduled for your combination.</option>
                    <?php endif; ?>
                </select>
            </div>

            <h3 class="text-lg font-semibold mt-6 mb-2">Proposed Changes (Optional)</h3>

            <div class="mb-4">
                <label for="proposed_subject_id" class="block text-gray-700">Proposed Subject:</label>
                <select name="proposed_subject_id" id="proposed_subject_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- No Change --</option>
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject['id']; ?>"><?= htmlspecialchars($subject['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="proposed_day" class="block text-gray-700">Proposed Day:</label>
                <select name="proposed_day" id="proposed_day" class="w-full border rounded px-3 py-2">
                    <option value="">-- No Change --</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="proposed_start_time" class="block text-gray-700">Proposed Start Time:</label>
                <input type="time" name="proposed_start_time" id="proposed_start_time" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label for="proposed_end_time" class="block text-gray-700">Proposed End Time:</label>
                <input type="time" name="proposed_end_time" id="proposed_end_time" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-6">
                <label for="reason" class="block text-gray-700">Reason for Request:</label>
                <textarea name="reason" id="reason" rows="5" class="w-full border rounded px-3 py-2" required></textarea>
            </div>

            <button type="submit" name="send_request" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit Request</button>
            <a href="dashboard.php" class="ml-2 text-gray-600">Cancel</a>
        </form>
    </div>
