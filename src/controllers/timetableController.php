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

if(isset($_POST['add'])){
    if($timetable->addTimetableEntry($_POST['day'],$_POST['time'],$_POST['isLab'],$_POST['combination_id'],$_POST['subject'], $_POST['teacher'], $_POST['classroom'])){
        header("Location: ../views/admin/timetable.php?success=Timetable Entry Added Successfully");
    }else{
        header("Location: ../views/admin/timetable.php?error=Failed to Add Timetable Entry");
    }
    exit();
}

if (isset($_GET['swap'])) {
    $day1 = $_GET['day1'];
    $time1 = $_GET['time1'];
    $day2 = $_GET['day2'];
    $time2 = $_GET['time2'];
    $combination_id = $_GET['combination_id'];

    // Get both entries
    $entry1 = $timetable->getEntryByTimeSlot($combination_id, $day1, $time1);
    $entry2 = $timetable->getEntryByTimeSlot($combination_id, $day2, $time2);

    // Swap the data
    if ($entry1 && $entry2) {
        $timetable->updateEntryTimeSlot($entry1['id'], $day2, $time2);
        $timetable->updateEntryTimeSlot($entry2['id'], $day1, $time1);
    } elseif ($entry1) {
        $timetable->updateEntryTimeSlot($entry1['id'], $day2, $time2);
    } elseif ($entry2) {
        $timetable->updateEntryTimeSlot($entry2['id'], $day1, $time1);
    }

    header("Location: ../views/admin/timetable.php?success=Timetable Entry Swapped Successfully");
    exit();
}
?>


