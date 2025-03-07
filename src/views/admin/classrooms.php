<?php
include_once "../../config/database.php";
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
$classroom = new Classroom($db);

$classrooms = $classroom->getAllClassrooms();
?>

<?php include_once(__DIR__ . '/header.php'); ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Classroom Management</h2>
        <a href="add_classroom.php" class="bg-blue-600 text-white px-4 py-2 rounded-md">+ Add Classroom</a>
        
        <table class="w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Room No</th>
                    <th class="border p-2">Type</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classrooms as $class) : ?>
                    <tr class="bg-white border">
                        <td class="border p-2"><?= $class['id'] ?></td>
                        <td class="border p-2"><?= $class['room_no'] ?></td>
                        <td class="border p-2"><?= ucfirst($class['type']) ?></td>
                        <td class="border p-2">
                            <a href="edit_classroom.php?id=<?= $class['id'] ?>" class="text-blue-500">Edit</a> |
                            <a href="../../controllers/classroomController.php?delete=<?= $class['id'] ?>" class="text-red-500" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php include_once(__DIR__ . '/footer.php'); ?>
