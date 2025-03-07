<?php
include_once "../config/database.php";
include_once "../models/Classroom.php";

$database = new Database();
$db = $database->getConnection();
$classroom = new Classroom($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $room_no = $_POST['room_no'];
        $type = $_POST['type'];

        if ($classroom->addClassroom($room_no, $type)) {
            header("Location: ../views/admin/classrooms.php?success=Classroom added successfully");
        } else {
            header("Location: ../views/admin/add_classroom.php?error=Failed to add classroom");
        }
    }

    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $room_no = $_POST['room_no'];
        $type = $_POST['type'];

        if ($classroom->updateClassroom($id, $room_no, $type)) {
            header("Location: ../views/admin/classrooms.php?success=Classroom updated successfully");
        } else {
            header("Location: ../views/admin/edit_classroom.php?id=$id&error=Failed to update classroom");
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($classroom->deleteClassroom($id)) {
        header("Location: ../views/admin/classrooms.php?success=Classroom deleted successfully");
    } else {
        header("Location: ../views/admin/classrooms.php?error=Failed to delete classroom");
    }
}
?>
