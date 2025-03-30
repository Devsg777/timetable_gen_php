<?php
include_once "../../config/database.php";
include_once "../../models/Combination.php";

$database = new Database();
$db = $database->getConnection();
$combination = new Combination($db);

// Get the combination ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request. Combination ID is required.");
}

$id = intval($_GET['id']);
$combo = $combination->getCombinationById($id);

if (!$combo) {
    die("Combination not found.");
}

?>

<?php include_once "header.php"; ?>

    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">Edit Combination</h2>

        <?php if (isset($error)) : ?>
            <p class="text-red-600 bg-red-100 border border-red-400 px-4 py-2 rounded mb-4"><?= $error; ?></p>
        <?php endif; ?>

        <form action="./../../controllers/combinationController.php" method="POST" class="space-y-4">
            <div>
                <input type="hidden" name="id" value="<?= $combo['id']; ?>">
                <label class="block text-gray-700 font-semibold">Combination Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($combo['name']); ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">Department</label>
                <input type="text" name="department" value="<?= htmlspecialchars($combo['department']); ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">Semester</label>
                <input type="number" name="semester" value="<?= htmlspecialchars($combo['semester']); ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
            </div>

            <button type="submit" name='edit'
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                Update Combination
            </button>

            <a href="combinations.php" 
               class="block text-center text-blue-600 hover:underline mt-2">
                Cancel & Go Back
            </a>
        </form>
    </div>

<?php include_once "footer.php"; ?>