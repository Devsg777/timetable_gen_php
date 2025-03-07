<?php
include_once '../../config/database.php';
include_once '../../models/Subject.php';
include_once '../../models/Combination.php';

$database = new Database();
$db = $database->getConnection();
$combination = new Combination($db);
$combinations = $combination->getCombinations();
?>

<?php include "header.php"; ?>
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Add Subject</h2>

        <form action="../../controllers/SubjectController.php" method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Subject Name</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Min Classes per Week</label>
                <input type="number" name="min_classes_per_week" class="w-full border rounded px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Type</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="theory">Theory</option>
                    <option value="lab">Lab</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Combination</label>
                <select name="combination_id" class="w-full border rounded px-3 py-2">
                    <?php foreach ($combinations as $comb) { ?>
                        <option value="<?= $comb['id']; ?>"><?= $comb['semester']; ?> sem - <?= $comb['name']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" name="add_subject" class="bg-blue-500 text-white px-4 py-2 rounded">Add Subject</button>
            <a href="subjects.php" class="ml-2 text-gray-600">Cancel</a>
        </form>
    </div>
<?php include "footer.php"; ?>
