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
    if($timetable->editTimetableEntry($_POST['entry_id'],$_POST['subject'], $_POST['teacher'], $_POST['classroom'])){
        header("Location: ../views/admin/timetable.php?success=Timetable Entry Updated Successfully");
    }else{
        header("Location: ../views/admin/timetable.php?error=Failed to Update Timetable Entry Because of Conflicting Entry");
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
    if($timetable->addTimetableEntry($_POST['day'],$_POST['time'],$_POST['isLab'],$_POST['combination_id'],$_POST['subject'], $_POST['teacher'], $_POST['classroom'],$_POST['section'])){
        header("Location: ../views/admin/timetable.php?success=Timetable Entry Added Successfully");
    }else{
        header("Location: ../views/admin/timetable.php?error=Failed to Add Timetable Entry Because of Conflicting Entry");
    }
    exit();
}

if (isset($_GET['swap'])) {
    $day1 = $_GET['day1'];
    $time1 = $_GET['time1'];
    $day2 = $_GET['day2'];
    $time2 = $_GET['time2'];
    $combination_id = $_GET['combination_id'];
    $entry1 = $_GET['entry1'];
    $entry2 = $_GET['entry2'];


    // get the timetable entries for the specified time slots

    if($entry1 != '' && $entry2 != ''){

    $slot1= $timetable->getEntryTimeSlot($entry1);
    $slot2= $timetable->getEntryTimeSlot($entry2);
     $startTime1 = $slot1['start_time'];
    $endTime1 = $slot1['end_time'];
    $startTime2 = $slot2['start_time'];
    $endTime2 = $slot2['end_time']  ;
    }else if($entry1 != '' && $entry2 == ''){
        $slot1= $timetable->getEntryTimeSlot($entry1);
          $startTime1 = $slot1['start_time'];
    $endTime1 = $slot1['end_time'];
         $time_slot = explode(" - ", $time2);
     //  convert like this Date formate "11:00:00"
    $startTime2 = $time_slot[0]."00";
    $endTime2 = $time_slot[1].":00";
    }
    else if($entry1 == '' && $entry2 != ''){
        $slot2= $timetable->getEntryTimeSlot($entry2);
        $startTime2 = $slot2['start_time'];
    $endTime2 = $slot2['end_time'];
    $time_slot = explode(" - ", $time1);
    $startTime1 = $time_slot[0].":00";
    $endTime1 = $time_slot[1].":00";
    }
    else{
        $time_slot1 = explode(" - ", $time1);
        $startTime1 = $time_slot1[0].":00";
        $endTime1 = $time_slot1[1].":00";

        $time_slot2 = explode(" - ", $time2);
        $startTime2 = $time_slot2[0].":00";
        $endTime2 = $time_slot2[1].":00";
    }

        

    // Swap the data
    if ($entry1 && $entry2) {
        $timetable->updateEntryTimeSlot($entry1, $day2, $startTime2,$endTime2 );
        $timetable->updateEntryTimeSlot($entry2, $day1, $startTime1,$endTime1);
    } elseif ($entry1) {
        $timetable->updateEntryTimeSlot($entry1, $day2, $startTime2,$endTime2);
    } elseif ($entry2) {
        $timetable->updateEntryTimeSlot($entry2, $day1, $startTime1,$endTime1);
    }

    header("Location: ../views/admin/timetable.php?success=Timetable Entry Swapped Successfully");
    exit();
}
?>