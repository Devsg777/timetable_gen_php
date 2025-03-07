<?php
include_once '../../config/database.php';
include_once '../../models/Teacher.php';
include_once '../../models/TeacherSubject.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$database = new Database();
$db = $database->getConnection();
$teacher = new Teacher($db);
$teachers = $teacher->getTeachers();
$assignedSubject = new TeacherSubject($db);

?>

<?php include_once(__DIR__ . '/header.php'); ?>

    <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Teacher Management</h2>
        
        <!-- Success & Error Messages -->
        <?php if (isset($_GET['success'])) { ?>
            <p class="bg-green-100 text-green-700 px-4 py-2 rounded"><?= $_GET['success']; ?></p>
        <?php } elseif (isset($_GET['error'])) { ?>
            <p class="bg-red-100 text-red-700 px-4 py-2 rounded"><?= $_GET['error']; ?></p>
        <?php } ?>

        <!-- Add Teacher Button -->
        <div class="mb-4">
            <a href="add_teacher.php" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">+ Add Teacher</a>
        </div>

        <!-- Teachers Table -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Department</th>
                        <th class="border p-2">Email</th>
                        <th class="border p-2">Phone</th>
                        <th class="border p-2">Address</th>
                        <th class="border p-2">Min Classes/Week</th>
                        <th class="border p-2">Min Labs/Week</th>
                        <th class="border p-2">Assigned Subjects</th>
                        <th class="border p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teachers)) { ?>
                        <?php foreach ($teachers as $t) { 
                            $assignedSubjects = $assignedSubject->getAllSubjectsByTeacherId($t['id']); ?>
                            <tr class="border">
                                <td class="border p-2"><?= htmlspecialchars($t['name']); ?></td>
                                <td class="border p-2"><?= htmlspecialchars($t['department']); ?></td>
                                <td class="border p-2"><?= htmlspecialchars($t['email']); ?></td>
                                <td class="border p-2"><?= htmlspecialchars($t['phone_no']); ?></td>
                                <td class="border p-2"><?= htmlspecialchars($t['address']); ?></td>
                                <td class="border p-2"><?= htmlspecialchars($t['min_class_hours_week']); ?></td>
                                <td class="border p-2"><?= htmlspecialchars($t['min_lab_hours_week']); ?></td>
                                <td class="border p-2 ">
                                    <?php if (!empty($assignedSubjects)) {  ?> 
                                        <ul class="list-disc ml-2">
                                        <?php foreach($assignedSubjects as $subject) {?>
                                           <li class=""><?= $subject['subject_name']; ?></li>
                                        <?php }?>
                                        </ul>
                                    <?php } else { ?>
                                           <span class ="text-gray-300 text-sm "> No Subjects Assigned.</span>
                                        <?php }  ?>
                                </td>
                                <td class="border p-2">
                                    <a href="edit_teacher.php?id=<?= $t['id']; ?>" class="text-blue-500 hover:underline">Edit</a> |
                                    <a href="../../controllers/teacherController.php?delete=<?= $t['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this teacher?');">Delete</a> |
                                    <a href="add_teacher_subject.php?id=<?= $t['id']; ?>"  class="text-purple-500 hover:underline" >Assign Subjects</a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 p-4">No teachers found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include_once(__DIR__ . '/footer.php'); ?>
