<?php
// View Request Page for Teachers by Get method ID

include_once '../../config/database.php';
include_once '../../models/Request.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if teacher is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php"); // Adjust path as needed
    exit();
}

$database = new Database();
$db = $database->getConnection();

$requestModel = new Request($db);

// Fetch the teacher's timetable entries
$id = $_GET['id'];

$myRequest = $requestModel->getRequestById($id);

if (!$myRequest) {
    // Handle case where request is not found
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Request Not Found</title>";
    echo "<link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>";
    echo "</head>";
    echo "<body class='bg-gray-100 h-screen flex items-center justify-center'>";
    echo "<div class='max-w-4xl mx-auto bg-white p-6 rounded shadow-lg text-center'>";
    echo "<p class='text-red-500 font-bold text-lg mb-4'>Request not found.</p>";
    echo "<a href='index.php' class='inline-block mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'>Back to Requests</a>";
    echo "</div>";
    echo "</body>";
    echo "</html>";
    exit();
}

?>

<?php include "header.php"; ?>
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">View Request Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <strong class="block text-gray-700 text-sm font-bold mb-2">Request ID:</strong>
                    <p class="text-gray-900"><?php echo htmlspecialchars($myRequest['id']); ?></p>
                </div>
        
                <div>
                    <strong class="block text-gray-700 text-sm font-bold mb-2">Request Type:</strong>
                    <p class="text-gray-900"><?php echo htmlspecialchars(ucfirst($myRequest['request_type'])); ?></p>
                </div>
                <div>
                    <strong class="block text-gray-700 text-sm font-bold mb-2">Requester Type:</strong>
                    <p class="text-gray-900"><?php echo htmlspecialchars(ucfirst($myRequest['requester_type'])); ?></p>
                </div>
                <div>
                    <strong class="block text-gray-700 text-sm font-bold mb-2">Request Status:</strong>
                    <span class="<?php
                    switch ($myRequest['status_name']) {
                        case 'Pending':
                            echo 'bg-yellow-200 text-yellow-800';
                            break;
                        case 'Approved':
                            echo 'bg-green-200 text-green-800';
                            break;
                        case 'Rejected':
                            echo 'bg-red-200 text-red-800';
                            break;
                        default:
                            echo 'bg-gray-200 text-gray-800';
                            break;
                    }
                    ?> inline-block py-1 px-2 rounded-full text-xs font-semibold"><?php echo htmlspecialchars($myRequest['status_name']); ?></span>
                </div>
                <?php if (!empty($myRequest['existing_day'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Existing Day:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars(date('l', strtotime($myRequest['existing_day']))); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($myRequest['proposed_day'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Proposed Day:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars(date('l', strtotime($myRequest['proposed_day']))); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($myRequest['existing_teacher_name'])): ?>
                <?php if (!empty($myRequest['existing_start_time'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Existing Start Time:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars(date('h:i A', strtotime($myRequest['existing_start_time']))); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($myRequest['proposed_start_time'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Proposed Start Time:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars(date('h:i A', strtotime($myRequest['proposed_start_time']))); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($myRequest['existing_end_time'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Existing End Time:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars(date('h:i A', strtotime($myRequest['existing_end_time']))); ?></p>
                    </div>
                <?php endif; ?>
                 <?php if (!empty($myRequest['proposed_end_time'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Proposed End Time:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars(date('h:i A', strtotime($myRequest['proposed_end_time']))); ?></p>
                    </div>
                <?php endif; ?>

                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Existing Teacher:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars($myRequest['existing_teacher_name']); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($myRequest['proposed_teacher_name'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Proposed Teacher:</strong>
                        <p class="text-indigo-600 font-semibold"><?php echo htmlspecialchars($myRequest['proposed_teacher_name']); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($myRequest['existing_subject_name'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Existing Subject:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars($myRequest['existing_subject_name']); ?></p>
                    </div>
                <?php endif; ?>
                 <?php if (!empty($myRequest['proposed_subject_name'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Proposed Subject:</strong>
                        <p class="text-indigo-600 font-semibold"><?php echo htmlspecialchars($myRequest['proposed_subject_name']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($myRequest['existing_classroom_name'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Existing Classroom:</strong>
                        <p class="text-gray-900"><?php echo htmlspecialchars($myRequest['existing_classroom_name']); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($myRequest['proposed_classroom_name'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Proposed Classroom:</strong>
                        <p class="text-indigo-600 font-semibold"><?php echo htmlspecialchars($myRequest['proposed_classroom_name']); ?></p>
                    </div>
                <?php endif; ?>
         
                
                <?php if ($myRequest['requester_type'] === 'teacher' && !empty($myRequest['requester_name'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Requested By (Teacher):</strong>
                        <p class="text-blue-600 font-semibold"><?php echo htmlspecialchars($myRequest['requester_name']); ?></p>
                    </div>
                <?php endif; ?>
        
                <?php if ($myRequest['requester_type'] === 'student' && !empty($myRequest['requester_name'])): ?>
                    <div>
                        <strong class="block text-gray-700 text-sm font-bold mb-2">Requested By (Student):</strong>
                        <p class="text-blue-600 font-semibold"><?php echo htmlspecialchars($myRequest['requester_name']); ?></p>
                    </div>
                <?php endif; ?>
                <div>
                    <strong class="block text-gray-700 text-sm font-bold mb-2">Reason for Request:</strong>
                    <p class="text-gray-900"><?php echo htmlspecialchars($myRequest['reason']); ?></p>
                </div>
                <div>
                    <strong class="block text-gray-700 text-sm font-bold mb-2">Requested On:</strong>
                    <p class="text-gray-900"><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($myRequest['request_date']))); ?></p>
                </div>

            </div>

            <div class="mt-8">
                <a href="request.php" class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Back to Requests</a>
            </div>
        </div>
</body>
</html>