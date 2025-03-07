<?php
include_once '../../config/database.php';
include_once '../../models/Teacher.php';

$database = new Database();
$db = $database->getConnection();
$teacher = new Teacher($db);

if (!isset($_GET['id'])) {
    header("Location: teachers.php?error=Invalid Teacher ID");
    exit;
}

$id = $_GET['id'];
$currentTeacher = $teacher->getTeacherById($id);

if (!$currentTeacher) {
    header("Location: teachers.php?error=Teacher not found");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_teacher'])) {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];
    $address = $_POST['address'];
    $min_class_hours_week = $_POST['min_class_hours_week'];
    $min_lab_hours_week = $_POST['min_lab_hours_week'];

    if ($teacher->updateTeacher($id, $name, $department, $email, $phone_no, $address, $min_class_hours_week, $min_lab_hours_week)) {
        header("Location: teachers.php?success=Teacher updated successfully");
    } else {
        header("Location: edit_teacher.php?id=$id&error=Failed to update teacher");
    }
}
?>

<?php include "header.php"; ?>

    <div class="max-w-lg  bg-white p-6 rounded shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Edit Teacher</h2>

        <?php if (isset($_GET['error'])) { ?>
            <p class="bg-red-100 text-red-700 px-4 py-2 rounded"><?= $_GET['error']; ?></p>
        <?php } ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block font-medium">Name</label>
                <input type="text" name="name" value="<?= $currentTeacher['name']; ?>" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Department</label>
                <input type="text" name="department" value="<?= $currentTeacher['department']; ?>" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Email</label>
                <input type="email" name="email" value="<?= $currentTeacher['email']; ?>" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Phone</label>
                <input type="text" name="phone_no" value="<?= $currentTeacher['phone_no']; ?>" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Address</label>
                <textarea name="address" class="w-full border px-3 py-2 rounded"><?= $currentTeacher['address']; ?></textarea>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Min Class Hours/Week</label>
                <input type="number" name="min_class_hours_week" value="<?= $currentTeacher['min_class_hours_week']; ?>" required class="w-full border px-3 py-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block font-medium">Min Lab Hours/Week</label>
                <input type="number" name="min_lab_hours_week" value="<?= $currentTeacher['min_lab_hours_week']; ?>" required class="w-full border px-3 py-2 rounded">
            </div>

            <button type="submit" name="edit_teacher" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update Teacher</button>
        </form>
        <a href="add_teacher_subject.php?id=<?= $currentTeacher['id']; ?>" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Assign Subjects</a>
    </div>

<?php include "footer.php"; ?>
