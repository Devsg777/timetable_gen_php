<?php
include_once "../config/database.php";
include_once "../models/Teacher.php";

$database = new Database();
$db = $database->getConnection();
$teacher = new Teacher($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $department = $_POST['department'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone_no = $_POST['phone_no'];
        $address = $_POST['address'];
        $min_class_hours = $_POST['min_class_hours_week'];
        $min_lab_hours = $_POST['min_lab_hours_week'];

        if ($teacher->addTeacher($name, $department, $email, $password, $phone_no, $address, $min_class_hours, $min_lab_hours)) {
            header("Location: ../views/admin/teachers.php?success=Teacher added successfully");
        } else {
            header("Location: ../views/admin/add_teacher.php?error=Failed to add teacher");
        }
    }

    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $department = $_POST['department'];
        $email = $_POST['email'];
        $phone_no = $_POST['phone_no'];
        $address = $_POST['address'];
        $min_class_hours = $_POST['min_class_hours_week'];
        $min_lab_hours = $_POST['min_lab_hours_week'];

        if ($teacher->updateTeacher($id, $name, $department, $email, $phone_no, $address, $min_class_hours, $min_lab_hours)) {
            header("Location: ../views/admin/teachers.php?success=Teacher updated successfully");
        } else {
            header("Location: ../views/admin/edit_teacher.php?id=$id&error=Failed to update teacher");
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($teacher->deleteTeacher($id)) {
        header("Location: ../views/admin/teachers.php?success=Teacher deleted successfully");
    } else {
        header("Location: ../views/admin/teachers.php?error=Failed to delete teacher");
    }
}
?>
