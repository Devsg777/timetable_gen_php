<?php
include_once "../config/database.php";
include_once "../models/Student.php";

$database = new Database();
$conn = $database->getConnection();
$student = new Student($conn);

// Handle Add Student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Secure password hashing
    $combination_id = $_POST['combination_id'];
    $phone_no = $_POST['phone_no'];
    $address = $_POST['address'];
    $section = $_POST['section'];

    
    if ($student->addStudent($name, $email, $password, $combination_id, $phone_no, $address,$section)) {
        header("Location: ../views/admin/students.php?success=Student added!");
    } else {
        header("Location: ../views/admin/add_student.php?error=Failed to add student.");
    }
}

// Handle Edit Student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_student'])) {

    if ($student->updateStudent($_POST['id'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['combination_id'], $_POST['section'],$_POST['address'])) {
        header("Location: ../views/admin/students.php?success=Student updated!");
    } else {
        header("Location: ../views/admin/edit_student.php?id=".$_POST['id']."&error=Update failed.");
    }
}

// Handle Delete Student
if (isset($_GET['delete_id'])) {
    if ($student->deleteStudent($_GET['delete_id'])) {
        header("Location: ../views/admin/students.php?success=Student deleted!");
    } else {
        header("Location: ../views/admin/students.php?error=Failed to delete student.");
    }
}
?>
