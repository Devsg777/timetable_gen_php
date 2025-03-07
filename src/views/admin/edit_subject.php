<?php
include_once '../../config/database.php';
include_once '../../models/Subject.php';
include_once '../../models/Combination.php';

$database = new Database();
$db = $database->getConnection();
$subject = new Subject($db);
$combination = new Combination($db);

if (!isset($_GET['id'])) {
    header("Location: subjects.php?error=No Subject ID provided");
    exit;
}

$subjectData = $subject->getSubjectById($_GET['id']);
$combinations = $combination->getCombinations();
?>

<?php include "header.php"; ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Edit Subject</h2>

        <form action="../../controllers/SubjectController.php" method="POST">
            <input type="hidden" name="id" value="<?= $subjectData['id']; ?>">

            <div class="mb-4">
                <label class="block text-gray-700">Subject Name</label>
                <input type="text" name="name" value="<?= $subjectData['name']; ?>" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Min Classes per Week</label>
                <input type="number" name="min_classes_per_week" value="<?= $subjectData['min_classes_per_week']; ?>" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Type</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="theory" <?= $subjectData['type'] == 'theory' ? 'selected' : ''; ?>>Theory</option>
                    <option value="lab" <?= $subjectData['type'] == 'lab' ? 'selected' : ''; ?>>Lab</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Combination</label>
                <select name="combination_id" class="w-full border rounded px-3 py-2">
                    <?php foreach ($combinations as $comb) { ?>
                        <option value="<?= $comb['id']; ?>" <?= $subjectData['combination_id'] == $comb['id'] ? 'selected' : ''; ?>>
                            <?= $comb['semester']; ?> sem - <?= $comb['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" name="edit_subject" class="bg-green-500 text-white px-4 py-2 rounded">Update Subject</button>
            <a href="subjects.php" class="ml-2 text-gray-600">Cancel</a>
        </form>
    </div>
<?php include "footer.php"; ?>
