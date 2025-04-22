<?php
include_once "../../config/database.php";
include_once "../../models/Classroom.php";

$database = new Database();
$db = $database->getConnection();
$classroom = new Classroom($db);

$id = $_GET['id'];
$classData = $classroom->getClassroomById($id);
$error = $_GET['error'] ?? null;
if ($error) {
    echo "<div class='text-red-500 text-center mb-4'>$error</div>";
}
?>

<?php include "header.php"; ?>
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Edit Classroom</h2>
        <form action="../../controllers/classroomController.php" method="POST">
            <input type="hidden" name="id" value="<?= $classData['id'] ?>">

            <label class="block mb-2 font-semibold">Room No:</label>
            <input type="text" name="room_no" value="<?= $classData['room_no'] ?>" required class="w-full p-2 border rounded mb-3">

            <label class="block mb-2 font-semibold">Type:</label>
            <select name="type" class="w-full p-2 border rounded mb-3">
                <option value="theory" <?= $classData['type'] == 'theory' ? 'selected' : '' ?>>Theory</option>
                <option value="lab" <?= $classData['type'] == 'lab' ? 'selected' : '' ?>>Lab</option>
            </select>

            <button type="submit" name="edit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md">Update Classroom</button>
        </form>
    </div>
<?php include "footer.php"; ?>
