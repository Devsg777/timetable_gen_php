<?php
include_once "../config/database.php";
include_once "../models/TeacherSubject.php";

$database = new Database();
$db = $database->getConnection();
$teacherSubject = new TeacherSubject($db);

// ✅ Add teacher-subject mapping
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $teacher_id = $_POST["teacher_id"];
    $subject_id = $_POST["subject_id"];

    if ($teacherSubject->addMapping($teacher_id, $subject_id)) {
        header("Location: ../views/admin/teachers.php?success=Mapping Added");
        exit();
    } else {
        header("Location: ../views/admin/teachers.php?error=Failed to add mapping");
        exit();
    }
}

// ✅ Delete teacher-subject mapping
if (isset($_GET["delete"])) {
    $id = $_GET["delete"];

    if ($teacherSubject->deleteMapping($id)) {
        header("Location: ../views/admin/teacher_subjects.php?success=Mapping Deleted");
        exit();
    } else {
        header("Location: ../views/admin/teacher_subjects.php?error=Failed to delete mapping");
        exit();
    }
}
?>
