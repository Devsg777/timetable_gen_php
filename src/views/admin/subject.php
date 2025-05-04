<?php
include_once '../../config/database.php';
include_once '../../models/Subject.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$database = new Database();
$db = $database->getConnection();
$subject = new Subject($db);
$subjects = $subject->getAllSubjects();


?>
<?php include_once(__DIR__ . '/header.php'); ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Subjects</h2>

        <a href="add_subject.php" class="bg-blue-500 text-white px-4 py-2 rounded">Add Subject</a>

        <table class="w-full mt-4 border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">Name</th>
                    <th class="border px-4 py-2">Classes/Week</th>
                    <th class="border px-4 py-2">Type</th>
                    <th class="border px-4 py-2">Combination</th>
                    <th class="border px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($subjects)) { ?>
                <?php foreach ($subjects as $sub) { ?>
                    <tr>
                        <td class="border px-4 py-2"><?= $sub['name']; ?></td>
                        <td class="border px-4 py-2"><?= $sub['min_classes_per_week']; ?></td>
                        <td class="border px-4 py-2"><?= ucfirst($sub['type']); ?></td>
                        <td class="border px-4 py-2"><?= $sub['combination_semester']; ?> sem - <?= $sub['combination_name']; ?></td>
                        <td class="border px-4 py-2">
                            <a href="edit_subject.php?id=<?= $sub['id']; ?>" class="text-blue-500">Edit</a>
                            <a href="../../controllers/SubjectController.php?delete=<?= $sub['id']; ?>" class="text-red-500 ml-2" onclick="return confirm('Are you sure you want to delete this subject?')">Delete</a>
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
<?php include_once(__DIR__ . '/footer.php'); ?>