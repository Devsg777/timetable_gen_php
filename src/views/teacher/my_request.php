<?php
include_once '../../config/database.php';
include_once '../../models/Request.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php"); // Adjust path as needed
    exit();
}

$database = new Database();
$db = $database->getConnection();

$rewuestModel = new Request($db);

// Fetch the teacher's timetable entries
$teacherId = $_SESSION['teacher_id'];

$myRequests = $rewuestModel->getRequestsByTeacherId($teacherId);




// Show Success or error messages if any


?>

<?php include "header.php"; ?> <?php if ($_GET['success'] ?? false) {
                             echo '<div class="alert alert-success bg-green-200 p-3 m-3 text-center text-green-600 border ">Request sent successfully!</div>';
                                } elseif ($_GET['error'] ?? false) {
                                    echo '<div class="alert alert-danger bg-red-200 p-3 m-3 text-center text-red-600 border">Error sending request. Please try again.</div>';
                                } ?>
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow-lg">
    <h2 class="text-2xl font-bold mb-4">My Requests</h2>

    <a href="sendRequest.php" class="bg-blue-600 text-white px-4 py-2 rounded-md mb-3">Send a Request <i class="fa fa-rocket" aria-hidden="true"></i></a>
    <table class="min-w-full bg-white border border-gray-300 mt-3">
        <thead>
            <tr class="bg-gray-100">
                <th class="py-2 px-4 border-b">Request ID</th>
                <th class="py-2 px-4 border-b">Request Type</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Created At</th>
                <th class="py-2 px-4 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($myRequests) > 0): ?>
                <?php foreach ($myRequests as $request): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($request['id']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($request['request_type']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($request['status_name']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($request['request_date']); ?></td>
                        <td class="py-2 px-4 border-b">
                            <a href="view_request.php?id=<?php echo htmlspecialchars($request['id']); ?>" class="text-blue-500 hover:underline"><i class="fa fa-eye" aria-hidden="true"></i></a> |
                            <a href="../../controllers/requestController.php?delete_request_id=<?php echo htmlspecialchars($request['id']); ?>" class="text-red-500 hover:underline"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4">No requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>