<?php
include_once(__DIR__ . '/../../config/database.php');
include_once(__DIR__ . '/../../models/Request.php');
include_once(__DIR__ . '/../../models/Timetable.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$timetableModel = new Timetable($db);

// Request Model Instance
$requestModel = new Request($db);

// Fetch all requests
$requests = $requestModel->getRequests();



// Fetch request statuses for the dropdown
$statusQuery = "SELECT * FROM request_statuses";
$statusStmt = $db->query($statusQuery);
$statuses = $statusStmt->fetchAll(PDO::FETCH_ASSOC);


?>

<?php include_once(__DIR__ . '/header.php'); ?>

<div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-6">
    <h2 class="text-2xl font-bold mb-4">Manage Class Change Requests</h2>

    <?php if (!empty($requests)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($requests as $req): $existingEntry = $timetableModel->getTimetableById($_GET['existing_timetable_id'] ?? null); ?>
                <div class="border border-gray-300 rounded-lg p-4 shadow-sm">
                <div class="flex"><h3 class="font-semibold mb-2 mr-3">Request ID: <?= htmlspecialchars($req['id']) ?></h3><a href="view_request.php?id=<?php echo htmlspecialchars($req['id']); ?>" class="text-blue-500 hover:underline"><i class="fa fa-eye" aria-hidden="true"></i> </a></div>
                    <p>
                        <strong>Requester:</strong>
                        <?php if ($req['requester_type'] === 'teacher' && isset($req['teacher_id_requested']) && $req['teacher_name_requested']): ?>
                            Teacher (ID: <?= htmlspecialchars($req['teacher_id_requested']) ?>) - <?= htmlspecialchars($req['teacher_name_requested']) ?>
                        <?php elseif ($req['requester_type'] === 'student' && isset($req['student_id_requested']) && $req['student_name_requested']): ?>
                            Student (ID: <?= htmlspecialchars($req['student_id_requested']) ?>) - <?= htmlspecialchars($req['student_name_requested']) ?>
                        <?php else: ?>
                            Unknown Requester
                        <?php endif; ?>
                    </p>
                    <p><strong>Requested On:</strong> <?= htmlspecialchars($req['request_date']) ?></p>      
                    <p class="mb-2"><strong>Reason:</strong> <?= htmlspecialchars($req['reason']) ?></p>
                    <div class="mt-3">
                        <form method="post" action="../../controllers/requestController.php" class="flex items-center space-x-2">
                            <input type="hidden" name="request_id" value="<?= htmlspecialchars($req['id']) ?>">
                            <label for="status_<?= htmlspecialchars($req['id']) ?>" class="font-semibold">Status:</label>
                            <select name="status_id" id="status_<?= htmlspecialchars($req['id']) ?>" class="border rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 text-sm">
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= htmlspecialchars($status['id']) ?>" <?= ($req['status_name'] === $status['status_name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($status['status_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm focus:outline-none focus:ring focus:ring-green-200">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-600">No class change requests found.</p>
    <?php endif; ?>
</div>

<?php include_once(__DIR__ . '/footer.php'); ?>