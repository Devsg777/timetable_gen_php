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

    <div class="container mx-auto p-6 bg-gray-100 font-sans antialiased">
        <h2 class="text-3xl font-semibold text-blue-700 mb-6">Teacher Management</h2>

        <?php if (isset($_GET['success'])) : ?>
            <div class="bg-green-200 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i> <?= $_GET['success']; ?>
            </div>
        <?php elseif (isset($_GET['error'])) : ?>
            <div class="bg-red-200 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i> <?= $_GET['error']; ?>
            </div>
        <?php endif; ?>

        <div class="mb-6">
            <a href="add_teacher.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow focus:outline-none focus:shadow-outline">
                <i class="fas fa-plus mr-2"></i> Add Teacher
            </a>
        </div>

        <div class="overflow-x-auto bg-white shadow-md rounded">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="border p-3 text-left font-semibold">Name</th>
                        <th class="border p-3 text-left font-semibold">Department</th>
                        <th class="border p-3 text-left font-semibold">Email</th>
                        <th class="border p-3 text-left font-semibold">Phone</th>
                        <th class="border p-3 text-left font-semibold">Address</th>
                        <th class="border p-3 text-left font-semibold">Min Classes/Week</th>
                        <th class="border p-3 text-left font-semibold">Min Labs/Week</th>
                        <th class="border p-3 text-left font-semibold">Assigned Subjects</th>
                        <th class="border p-3 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teachers)) : ?>
                        <?php foreach ($teachers as $t) :
                            $assignedSubjects = $assignedSubject->getAllSubjectsByTeacherId($t['id']); ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="border p-3"><?= htmlspecialchars($t['name']); ?></td>
                                <td class="border p-3"><?= htmlspecialchars($t['department']); ?></td>
                                <td class="border p-3"><?= htmlspecialchars($t['email']); ?></td>
                                <td class="border p-3"><?= htmlspecialchars($t['phone_no']); ?></td>
                                <td class="border p-3"><?= htmlspecialchars($t['address']); ?></td>
                                <td class="border p-3"><?= htmlspecialchars($t['min_class_hours_week']); ?></td>
                                <td class="border p-3"><?= htmlspecialchars($t['min_lab_hours_week']); ?></td>
                                <td class="border p-3">
                                    <?php if (!empty($assignedSubjects)) : ?>
                                        <ul class="list-disc ml-5 text-sm">
                                            <?php foreach ($assignedSubjects as $subject) : ?>
                                                <li><?= htmlspecialchars($subject['subject_name']); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <span class="text-gray-500 text-sm italic">No Subjects Assigned.</span>
                                    <?php endif; ?>
                                </td>
                                <td class="border p-3 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="edit_teacher.php?id=<?= htmlspecialchars($t['id']); ?>" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-edit"></i> <span class="hidden md:inline">Edit</span>
                                        </a>
                                        <a href="../../controllers/teacherController.php?delete=<?= htmlspecialchars($t['id']); ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this teacher?');">
                                            <i class="fas fa-trash-alt"></i> <span class="hidden md:inline">Delete</span>
                                        </a>
                                        <a href="add_teacher_subject.php?id=<?= htmlspecialchars($t['id']); ?>" class="text-purple-500 hover:text-purple-700">
                                            <i class="fas fa-book-open"></i> <span class="hidden md:inline">Assign</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9" class="text-center text-gray-500 p-4">No teachers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include_once(__DIR__ . '/footer.php'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>