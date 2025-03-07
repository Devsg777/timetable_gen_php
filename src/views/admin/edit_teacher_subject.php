<?php
include_once "../../config/database.php";
include_once "../../models/TeacherSubject.php";

$database = new Database();
$db = $database->getConnection();
$teacherSubject = new TeacherSubject($db);

$id = $_GET['id'];
$mapping = $teacherSubject->getMappingById($id);
?>

<?php include_once "header.php"; ?>
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Edit Mapping</h2>
        <form action="../../controllers/teacherSubjectController.php" method="POST">
            <input type="hidden" name="id" value="<?= $mapping['id'] ?>">

            <label class="block mb-2 font-semibold">Teacher:</label>
            <input type="text" value="<?= $mapping['teacher_name'] ?>" readonly class="w-full p-2 border rounded mb-3">

            <label class="block mb-2 font-semibold">Subject:</label>
            <input type="text" value="<?= $mapping['subject_name'] ?>" readonly class="w-full p-2 border rounded mb-3">

            <button type="submit" name="delete" class="w-full bg-red-600 text-white px-4 py-2 rounded-md">Remove Mapping</button>
        </form>
    </div>
<?php include_once "footer.php"; ?>
