<?php
include_once(__DIR__ . '/../../config/database.php');
include_once(__DIR__ . '/../../models/Combination.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Combination Model Instance
$combination = new Combination($db);

// Fetch Data
$combinations = $combination->getCombinations();
?>


<?php include_once(__DIR__ . '/header.php'); ?>
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">Manage Combinations</h2>
        
        <a href="add_combination.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add Combination
        </a>

        <table class="w-full mt-4 border border-gray-300 shadow-lg">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Department</th>
                    <th class="p-3 text-left">Semester</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php if (!empty($combinations)): ?>
                    <?php foreach ($combinations as $combination): ?>
                        <tr class="border-b">
                            <td class="p-3"><?= htmlspecialchars($combination['id']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($combination['name']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($combination['department']) ?></td>
                            <td class="p-3"><?= htmlspecialchars($combination['semester']) ?></td>
                            <td class="p-3">
                                <a href="edit_combination.php?id=<?= $combination['id'] ?>" 
                                   class="text-blue-500 hover:underline">Edit</a> |
                                   <a href="../../controllers/combinationController.php?delete=<?= $combination['id'] ?>" 
                                 onclick="return confirm('Are you sure you want to delete this combination?');" class="text-red-500 hover:underline"> Delete</a>
                    </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="p-3 text-center">No combinations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once(__DIR__ . '/footer.php'); ?>
