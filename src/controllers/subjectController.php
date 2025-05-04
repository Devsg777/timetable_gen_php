<?php
include_once '../config/database.php';
include_once '../models/Subject.php';

$database = new Database();
$db = $database->getConnection();
$subject = new Subject($db);

// Handle Add Subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $name = $_POST['name'];
    $min_classes_per_week = $_POST['min_classes_per_week'];
    $type = $_POST['type'];
    $combination_id = $_POST['combination_id'];
    if($type=="theory"){
        $duration = 1;
    }else{
        $duration = 3;
    }

    // Check if the subject  is not in the same combination and each combination has max 6 theory subjects 3 lab subjects
    $existingSubjects = $subject->getSubjectsByCombinationId($combination_id);
    $subjectCount = 0;
    $labCount = 0;
    foreach ($existingSubjects as $existingSubject) {
        if ($existingSubject['type'] == 'theory') {
            $subjectCount++;
        } elseif ($existingSubject['type'] == 'lab') {
            $labCount++;
        }
    }
    if ($type == 'theory' && $subjectCount >= 6) {
        header("Location: ../views/admin/add_subject.php?error=Cannot add more than 6 theory subjects to the same combination");
        exit();
    } elseif ($type == 'lab' && $labCount >= 3) {
        header("Location: ../views/admin/add_subject.php?error=Cannot add more than 3 lab subjects to the same combination");
        exit();
    }

    if ($subject->addSubject($name, $min_classes_per_week, $type, $combination_id, $duration)) {
        header("Location: ../views/admin/subject.php?success=Subject added successfully");
    } else {
        header("Location: ../views/admin/add_subject.php?error=Failed to add subject");
    }
}

// Handle Edit Subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_subject'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $min_classes_per_week = $_POST['min_classes_per_week'];
    $type = $_POST['type'];
    $combination_id = $_POST['combination_id'];
    if($type=="theory"){
        $duration = 1;
    }else{
        $duration = 3;
    }


    if ($subject->updateSubject($id, $name, $min_classes_per_week, $type, $combination_id, $duration)) {
        header("Location: ../views/admin/subject.php?success=Subject updated successfully");
    } else {
        header("Location: ../views/admin/edit_subject.php?id=$id&error=Failed to update subject");
    }
}

// Handle Delete Subject
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    if ($subject->deleteSubject($id)) {
        header("Location: ../views/admin/subject.php?success=Subject deleted successfully");
    } else {
        header("Location: ../views/admin/subject.php?error=Failed to delete subject");
    }
}
?>
