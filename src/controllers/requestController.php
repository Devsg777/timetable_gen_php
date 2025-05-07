<?php
include_once "../config/database.php";
include_once "../models/Request.php";

// Database Connection
$database = new Database();
$db = $database->getConnection();

// Request Model Instance
$request = new Request($db);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Handle Teacher Send Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request']) && isset($_SESSION['teacher_id'])) {
    $requester_id = $_SESSION['teacher_id'];
    $requester_type = 'teacher';
    $existing_timetable_id = $_POST['existing_timetable_id'];
    $proposed_subject_id = $_POST['proposed_subject_id'] ?? null;
    $proposed_teacher_id = $_POST['proposed_teacher_id'] ?? null;
    $proposed_classroom_id = $_POST['proposed_classroom_id'] ?? null;
    $proposed_day = $_POST['proposed_day'] ?? null;
    $proposed_start_time = $_POST['proposed_start_time'] ?? null;
    $proposed_end_time = $_POST['proposed_end_time'] ?? null;
    $reason = $_POST['reason'];

    if ($request->addRequest(
        $requester_id,
        $requester_type,
        $existing_timetable_id,
        $proposed_subject_id,
        $proposed_teacher_id,
        $proposed_classroom_id,
        $proposed_day,
        $proposed_start_time,
        $proposed_end_time,
        $reason
    )) {
        header("Location: ../views/teacher/sendRequest.php?success=Request submitted successfully");
        exit();
    } else {
        header("Location: ../views/teacher/sendRequest.php?error=Failed to submit request");
        exit();
    }
}

// Handle Student Send Request (Adapt as needed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request']) && isset($_SESSION['student_id'])) {
    $requester_id = $_SESSION['student_id'];
    $requester_type = 'student';
    // Assuming students also select an existing timetable entry to request a change for
    $existing_timetable_id = $_POST['existing_timetable_id'];
    $proposed_subject_id = $_POST['proposed_subject_id'] ?? null;
    // Students might not propose a teacher or classroom change, adjust as per your requirements
    $proposed_teacher_id = null;
    $proposed_classroom_id = null;
    $proposed_day = $_POST['proposed_day'] ?? null;
    $proposed_start_time = $_POST['proposed_start_time'] ?? null;
    $proposed_end_time = $_POST['proposed_end_time'] ?? null;
    $reason = $_POST['reason'];

    if ($request->addRequest(
        $requester_id,
        $requester_type,
        $existing_timetable_id,
        $proposed_subject_id,
        $proposed_teacher_id,
        $proposed_classroom_id,
        $proposed_day,
        $proposed_start_time,
        $proposed_end_time,
        $reason
    )) {
        header("Location: ../views/student/sendRequest.php?success=Request submitted successfully");
        exit();
    } else {
        header("Location: ../views/student/sendRequest.php?error=Failed to submit request");
        exit();
    }
}

// Handle Admin Update Request Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status']) && isset($_SESSION['admin_id'])) {
    $request_id = $_POST['request_id'];
    $status_id = $_POST['status_id'];

    if ($request->updateRequestStatus($request_id, $status_id)) {
        header("Location: ../views/admin/request.php?success=Request status updated successfully");
        exit();
    } else {
        header("Location: ../views/admin/request.php?error=Failed to update request status");
        exit();
    }
}

// No specific handling for deleting requests is shown in your Combination controller,
// but if you need one, you can add it here similarly.

// You might also have actions to fetch and display requests in the admin area here.
// For example:

function getAllRequests($db) {
    $requestModel = new Request($db);
    return $requestModel->getRequests();
}

function getRequestDetails($db, $requestId) {
    $requestModel = new Request($db);
    return $requestModel->getRequestById($requestId);
}

// You would then call these functions in your admin views to display the data.
?>