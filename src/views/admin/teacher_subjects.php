<?php
include_once "../../config/database.php";
include_once "../../models/TeacherSubject.php";

$database = new Database();
$db = $database->getConnection();
$teacherSubject = new TeacherSubject($db);

$teacherSubjects = $teacherSubject->getAllMappings();
?>

<?php include_once "header.php"; ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Teacher-Subject Mapping</h2>
        <a href="add_teacher_subject.php" class="bg-blue-600 text-white px-4 py-2 rounded-md">+ Add Mapping</a>
        
        <table class="w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Teacher Name</th>
                    <th class="border p-2">Subject Name</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teacherSubjects as $mapping) : ?>
                    <tr class="bg-white border">
                        <td class="border p-2"><?= $mapping['teacher_name'] ?></td>
                        <td class="border p-2"><?= $mapping['subject_name'] ?></td>
                        <td class="border p-2">
                            <a href="edit_teacher_subject.php?id=<?= $mapping['id'] ?>" class="text-blue-500">Edit</a> |
                            <a href="../../controllers/teacherSubjectController.php?delete=<?= $mapping['id'] ?>" class="text-red-500" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php include_once "footer.php"; ?>
