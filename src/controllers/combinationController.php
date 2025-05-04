<?php
include_once "../config/database.php";
include_once "../models/Combination.php";

// Database Connection
$database = new Database();
$db = $database->getConnection();

// Combination Model Instance
$combination = new Combination($db);




// Handle Add Combination
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $semester = $_POST['semester'];


    //convert No of sections into array of Aphalbets
  
    if ($_POST['sections'] <= 0 || $_POST['sections'] > 26) {
        $section = json_encode(["A"]);
    }else{

    $alphabet = range('A', 'Z');
    $section = json_encode(array_slice($alphabet, 0, $_POST['sections']));
    }
     
    
    if ($combination->addCombination($name, $department, $semester,$section)) {
        header("Location: ../views/admin/combinations.php?success=Combination added successfully");
        exit();
    } else {
        header("Location: ../views/admin/add_combination.php?error=Failed to add combination");
        exit();
    }
}

// Handle Edit Combination
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $semester = $_POST['semester'];
    if ($_POST['sections'] <= 0 || $_POST['sections'] > 26) {
        $section = json_encode(["A"]);
    }else{

    $alphabet = range('A', 'Z');
    $section = json_encode(array_slice($alphabet, 0, $_POST['sections']));
    }

    if ($combination->updateCombination($id, $name, $department, $semester, $section)) {
        header("Location: ../views/admin/combinations.php?success=Combination updated successfully");
        exit();
    } else {
        header("Location: ../views/admin/edit_combination.php?id=$id&error=Failed to update combination");
        exit();
    }
}



// Handle Delete Combination
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Ensure ID is an integer

    // Check if ID exists before deleting
    $existingCombination = $combination->getCombinationById($id);
    if (!$existingCombination) {
        header("Location: ../views/admin/combinations.php?error=Combination not found");
        exit();
    }

    if ($combination->deleteCombination($id)) {
        header("Location: ../views/admin/combinations.php?success=Combination deleted successfully");
    } else {
        header("Location: ../views/admin/combinations.php?error=Failed to delete combination");
    }
    exit();
}
?>
