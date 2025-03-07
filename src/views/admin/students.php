<?php
include_once "../../config/database.php";
include_once "../../models/Student.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->getConnection();
$student = new Student($conn);
$students = $student->getAllStudents();
?>


<?php include "header.php"; ?>
    <h2 class="text-2xl font-bold mb-4">Manage Students</h2>
    <a href="add_student.php" class="bg-blue-500 text-white px-4 py-2 rounded">Add Student</a>

    <table class="mt-4 bg-white w-full shadow-md rounded">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Name</th>
                <th class="p-2">Email</th>
                <th class="p-2">Phone</th>
                <th class="p-2">Combination</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
            <tr class="border-b">
                <td class="p-2"><?= $s['name'] ?></td>
                <td class="p-2"><?= $s['email'] ?></td>
                <td class="p-2"><?= $s['phone_no'] ?></td>
                <td class="p-2"><?php $s['combination_semester'] ?> sem - <?= $s['combination_name'] ?></td>
                <td class="p-2">
                    <a href="edit_student.php?id=<?= $s['id'] ?>" class="text-blue-500">Edit</a> |
                    <a href="StudentController.php?delete_id=<?= $s['id'] ?>" class="text-red-500">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php include "footer.php"; ?>