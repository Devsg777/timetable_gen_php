<?php
include_once "../config/database.php";
include_once "../models/Timetable.php";

$database = new Database();
$db = $database->getConnection();
$timetable = new Timetable($db);

// ✅ Generate New Timetable
if (isset($_GET['generate'])) {
    $timetable->deleteTimetable(); // Clear existing timetable
    if ($timetable->generateTimetable()) {
        header("Location: ../views/admin/timetable.php?success=Timetable Generated Successfully");
    } else {
        header("Location: ../views/admin/timetable.php?error=Failed to Generate Timetable");
    }
    exit();
}

// ✅ Delete Existing Timetable
if (isset($_GET['delete'])) {
    if ($timetable->deleteTimetable()) {
        header("Location: ../views/admin/timetable.php?success=Timetable Deleted Successfully");
    } else {
        header("Location: ../views/admin/timetable.php?error=Failed to Delete Timetable");
    }
    exit();
}
// Edit Timetable Entry
if (isset($_POST['edit'])) {
    if($timetable->editTimetableEntry($_POST['id'],$_POST['subject'], $_POST['teacher'], $_POST['classroom'])){
        header("Location: ../views/admin/timetable.php?success=Timetable Entry Updated Successfully");
    }else{
        header("Location: ../views/admin/timetable.php?error=Failed to Update Timetable Entry");
    }
    exit();
}
//Delete Timetable Entry
if(isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    if($timetable->deleteTimetableEntry($id)){
        header("Location: ../views/admin/timetable.php?success=Timetable Entry Deleted Successfully");
    }else{
        header("Location: ../views/admin/timetable.php?error=Failed to Delete Entry");
    }
    exit();
}


?>
