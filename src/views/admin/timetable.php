<?php
include_once "../../config/database.php";
include_once "../../models/Timetable.php";
include_once "../../models/Subject.php";
include_once "../../models/Teacher.php";
include_once "../../models/Classroom.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$database = new Database();
$db = $database->getConnection();
$timetable = new Timetable($db);
$subject = new Subject($db);
$teacher = new Teacher($db);
$classroom = new Classroom($db);


// Fetch timetables for all combinations
$timetable_data = $timetable->getTimetableGrid();
$combinations = $timetable->getAllCombinations(); // Fetch distinct combinations
$subjects =  $subject->getAllSubjects(); 
$teachers = $teacher->getTeachers();
$classrooms = $classroom->getAllClassrooms();

$time_slots = ['10:00 - 11:00', '11:00 - 12:00','12:00 - 13:00', '01:00 - 02:00', '02:00 - 03:00', '03:00 - 04:00', '04:00 - 05:00','05:00 - 06:00'];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <button onclick="window.location.href = 'dashboard.php'" class=" absolute t-0 right-0 mr-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mb-4">Go To Dashboard</button>
        <h1 class="text-3xl font-bold mb-6 text-center">Timetable Management</h1>

        <div class="flex space-x-4 mb-4">
            <a href="../../controllers/timetableController.php?generate" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Generate Timetable</a>
            <a href="../../controllers/timetableController.php?delete" onclick="return confirm('Are you sure you want to delete the timetable?')" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete Timetable</a>
            <a href="../../controllers/exportTimetable.php?type=excel" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Export to Excel</a>
            <a href="../../controllers/exportTimetable.php?type=pdf" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Export to PDF</a>
        </div>

        <?php foreach ($combinations as $combination) : ?>
            <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-xl font-bold mb-4 text-center">
                    <?= htmlspecialchars($combination['name']) . " (" . htmlspecialchars($combination['department']) . " - Semester " . htmlspecialchars($combination['semester']) . ")"; ?>
                </h2><pre>
                <?php $data = $timetable->getTimetableByCombination($combination['combination_id']); ?>
                </pre>
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2">Day</th>
                            <?php foreach ($time_slots as $slot) : ?>
                                <th class="border border-gray-300 p-2"><?= htmlspecialchars($slot); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($days as $day) : ?>
                            <tr>
                                <td class="border border-gray-300 p-2 bg-gray-100 font-bold"><?= htmlspecialchars($day); ?></td>
                                <?php foreach ($time_slots as $slot) : ?>
                                    
                                    <?php if (isset($data[$day][$slot])) :
                                        $entry = $data[$day][$slot]; ?>
                                        <td class="border border-gray-300 p-2 text-sm text-center">
                                            <b><?= htmlspecialchars($entry['subject']); ?></b><br>
                                            <span class="text-gray-600">Teacher: <?= htmlspecialchars($entry['teacher']); ?></span><br>
                                            <span class="text-gray-500">Room: <?= htmlspecialchars($entry['classroom']); ?></span><br>
                                            <button onclick="openEditModal( '<?=htmlspecialchars($entry['entry_id']);?>','<?= htmlspecialchars($entry['subject']); ?>', '<?= htmlspecialchars($entry['teacher']); ?>', '<?= htmlspecialchars($entry['classroom']); ?>', '<?= htmlspecialchars($day); ?>', '<?= htmlspecialchars($slot); ?>')" class="text-blue-500">Edit</button>
                                            <a href="../../controllers/timetableController.php?delete_id=<?= htmlspecialchars($entry['entry_id'] ?? ''); ?>" class="text-red-500">Delete</a>
                                        </td>
                                    <?php else : ?>
                                        <td class="border border-gray-300 p-2 text-center text-gray-400">---</td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <!-- Master Timetable -->
        <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4 text-center">Master Timetable</h2>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 p-2">Day</th>
                        <?php foreach ($time_slots as $slot) : ?>
                            <th class="border border-gray-300 p-2"><?= htmlspecialchars($slot); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($days as $day) : ?>
                        <tr>
                            <td class="border border-gray-300 p-2 bg-gray-100 font-bold"><?= htmlspecialchars($day); ?></td>
                            <?php foreach ($time_slots as $slot) : ?>
                                <?php
                                $entries = [];
                                // $teachers = [];
                                // $room = [];
                                // $com = [];
                                // $sem = [];
                                foreach ($combinations as $combination) {
                                    if (isset($timetable_data[$day][$slot])){
                                        $entries[$slot] = [
                                            "subject" => htmlspecialchars($timetable_data[$day][$slot]['subject']),
                                            "teacher" => htmlspecialchars($timetable_data[$day][$slot]['teacher']),
                                            "classroom" => htmlspecialchars($timetable_data[$day][$slot]['classroom']),
                                            "combination" => htmlspecialchars($timetable_data[$day][$slot]['combination']),
                                            "semester" => htmlspecialchars($timetable_data[$day][$slot]['semester']),
                                        ];
                                        // $teachers[] = htmlspecialchars($timetable_data[$day][$slot]['teacher']);
                                        // $room[] = htmlspecialchars($timetable_data[$day][$slot]['classroom']);
                                        // $com[] = htmlspecialchars($timetable_data[$day][$slot]['combination']);
                                        // $sem[] = htmlspecialchars($timetable_data[$day][$slot]['semester']); 
                                        // empty($entries[$slot]) ? '---' : "[ ".$entries[$slot]['semester']." - ".$entries[$slot]['combination']." <br>".$entries[$slot]['subject']. "(". $entries[$slot]['classroom']. ")<BR>". $entries[$slot]['teacher']."]" ;  
                                        
                                    }
                                }
                                 
                                ?>
                                <td class="border border-gray-300 p-2 text-center">
                                  ---
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Modal -->
        <div id="editModal" class="fixed inset-0 hidden bg-gray-600 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded-lg w-1/3 shadow-lg">
                <h2 class="text-xl font-bold mb-4">Edit Timetable Entry</h2>
                <form id="editForm" method="POST" action="../../controllers/timetableController.php">
                    <input type="hidden" name="id" id="editId">
                    <div class="mb-4">
                        <label class="block font-semibold">Subject</label>
                        <select name="subject" id="editSubject" class="w-full p-2 border rounded">
                            <option value="">Select Subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>"><?= $subject['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold">Teacher</label>
                        <select name="teacher" id="editTeacher" class="w-full p-2 border rounded">
                            <option value="">Select Teacher</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold">Classroom</label>
                        <select name="classroom" id="editClassroom" class="w-full p-2 border rounded">
                            <option value="">Select Classroom</option>
                            <?php foreach ($classrooms as $classroom): ?>
                                <option value="<?= $classroom['id'] ?>"><?= $classroom['room_no'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-500 text-white rounded">Cancel</button>
                        <button type="submit" name="edit" class="px-4 py-2 bg-green-500 text-white rounded">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal( entry_id,id,subject, teacher, classroom) {
            document.getElementById('editSubject').value = subject;
            document.getElementById('editTeacher').value = teacher;
            document.getElementById('editClassroom').value = classroom;
            document.getElementById('editId').value = entry_id;
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>
