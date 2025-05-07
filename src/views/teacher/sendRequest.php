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

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php"); // Adjust path as needed
    exit();
}

$database = new Database();
$db = $database->getConnection();

$timetableModel = new Timetable($db);
$subjectModel = new Subject($db);
$teacherModel = new Teacher($db);
$classroomModel = new Classroom($db);

// Fetch the teacher's timetable entries
$teacherId = $_SESSION['teacher_id'];
$teacherTimetable = $timetableModel->getTimetableByTeacherId_Data($teacherId);

// Fetch all subjects, teachers, and classrooms for the proposed changes dropdowns
$subjects = $subjectModel->getAllSubjects();
$teachers = $teacherModel->getTeachers();
$classrooms = $classroomModel->getAllClassrooms();

// Show Success or error messages if any


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
                    <?php if (!empty($teacherTimetable)): ?>
                        <?php foreach ($teacherTimetable as $entry): ?>
                            <option value="<?= $entry['entry_id']; ?>">
                                <?= htmlspecialchars($entry['subject']) ?> (<?= htmlspecialchars($entry['day']) ?>
                                <?= htmlspecialchars(date('h:i A', strtotime($entry['start_time']))) ?> -
                                <?= htmlspecialchars(date('h:i A', strtotime($entry['end_time']))) ?>,
                                Room: <?= htmlspecialchars($entry['classroom']) ?>, Combination: <?= htmlspecialchars($entry['combination']) ?> - <?= htmlspecialchars($entry['section']) ?>)
                            </option>
                       <div class="alert alert-danger">Error sending request. Please try again.</div>';
                   <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No classes scheduled for you.</option>
                    <?php endif; ?>
                </select>
            </div>

            <h3 class="text-lg font-semibold mt-6 mb-2">Proposed Changes</h3>

            <div class="mb-4">
                <label for="proposed_subject_id" class="block text-gray-700">Proposed Subject:</label>
                <select name="proposed_subject_id" id="proposed_subject_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- No Change --</option>
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject['id']; ?>"><?= htmlspecialchars($subject['name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="proposed_teacher_id" class="block text-gray-700">Proposed Teacher:</label>
                <select name="proposed_teacher_id" id="proposed_teacher_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- No Change --</option>
                    <?php if (!empty($teachers)): ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id']; ?>"><?= htmlspecialchars($teacher['name']) ?> (<?= htmlspecialchars($teacher['department']) ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="proposed_classroom_id" class="block text-gray-700">Proposed Classroom:</label>
                <select name="proposed_classroom_id" id="proposed_classroom_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- No Change --</option>
                    <?php if (!empty($classrooms)): ?>
                        <?php foreach ($classrooms as $classroom): ?>
                            <option value="<?= $classroom['id']; ?>"><?= htmlspecialchars($classroom['room_no']) ?> (<?= ucfirst(htmlspecialchars($classroom['type'])) ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="proposed_day" class="block text-gray-700">Proposed Day:</label>
                <select name="proposed_day" id="proposed_day" class="w-full border rounded px-3 py-2" required>
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
                <input type="time" name="proposed_start_time" id="proposed_start_time" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label for="proposed_end_time" class="block text-gray-700">Proposed End Time:</label>
                <input type="time" name="proposed_end_time" id="proposed_end_time" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-6">
                <label for="reason" class="block text-gray-700">Reason for Request:</label>
                <textarea name="reason" id="reason" rows="5" class="w-full border rounded px-3 py-2" required></textarea>
            </div>

            <button type="submit" name="send_request" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Submit Request</button>
            <a href="dashboard.php" class="ml-2 text-gray-600">Cancel</a>
        </form>
    </div>
</div>
</html>