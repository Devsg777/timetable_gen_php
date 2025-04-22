<?php
include_once "../../config/database.php";
include_once "../../models/Student.php";
include_once "../../models/Combination.php"; // Include Combination model

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
$combination = new Combination($conn); // Instantiate Combination model
$students = $student->getAllStudentsWithCombination(); // Fetch student data with combination details
?>

<?php include "header.php"; ?>
    <div class="container mx-auto p-6 bg-gray-100">
        <h2 class="text-3xl font-semibold text-blue-700 mb-6">Manage Students</h2>
        <div class="mb-4">
            <a href="add_student.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                <i class="fas fa-plus mr-2"></i> Add Student
            </a>
        </div>

        <div class="bg-white shadow-md rounded overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm uppercase font-semibold">Name</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm uppercase font-semibold">Email</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm uppercase font-semibold">Phone</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-sm uppercase font-semibold">Combination</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-center text-sm uppercase font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    <?php foreach ($students as $s): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center">
                                    <span><?= htmlspecialchars($s['name']) ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span><?= htmlspecialchars($s['email']) ?></span>
                            </td>
                            <td class="px-5 py-3">
                                <span><?= htmlspecialchars($s['phone_no']) ?></span>
                            </td>
                            <td class="px-5 py-3">
                                <span><?= htmlspecialchars($s['combination_semester']) ?> sem - <?= htmlspecialchars($s['combination_name']) ?></span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex justify-center items-center">
                                    <a href="edit_student.php?id=<?= htmlspecialchars($s['id']) ?>" class="text-blue-500 hover:text-blue-700 mr-3">
                                        <i class="fas fa-edit"></i> <span class="hidden md:inline">Edit</span>
                                    </a>
                                    <a href="StudentController.php?delete_id=<?= htmlspecialchars($s['id']) ?>" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash-alt"></i> <span class="hidden md:inline">Delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                        <tr><td class="px-5 py-3 text-center" colspan="5">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include "footer.php"; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>