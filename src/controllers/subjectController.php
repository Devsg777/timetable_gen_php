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

    if ($subject->addSubject($name, $min_classes_per_week, $type, $combination_id)) {
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

    if ($subject->updateSubject($id, $name, $min_classes_per_week, $type, $combination_id)) {
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
