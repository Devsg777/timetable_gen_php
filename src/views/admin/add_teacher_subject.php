<?php
include_once "../../config/database.php";
include_once "../../models/Teacher.php";
include_once "../../models/Subject.php";

$database = new Database();
$db = $database->getConnection();
$teacher = new Teacher($db);
$subject = new Subject($db);

$teachers = $teacher->getTeachers();
$subjects = $subject->getAllSubjects();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

?>

<?php include "header.php"; ?> 
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4">Add Mapping</h2>
        <form action="../../controllers/teacherSubjectController.php" method="POST">
            <?php if (isset($_GET['id'])) { ?>
                <?php foreach($teachers as $t) : if($t['id'] == $id){ $name = $t['name']; }endforeach;  ?>
                <label class="block mb-2 font-semibold"> Teacher: <?= $name ?></label>
                <input type="hidden" name="teacher_id" value="<?= $id ?>">
            <?php } else { ?>
                 <label class="block mb-2 font-semibold">Teacher:</label>
                 <select name="teacher_id" class="w-full p-2 border rounded mb-3">
                     <?php foreach ($teachers as $t) : ?>
                         <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                     <?php endforeach; ?>
                 </select>
            <?php } ?>

            <label class="block mb-2 font-semibold">Subject:</label>
            <select name="subject_id" class="w-full p-2 border rounded mb-3">
                <?php foreach ($subjects as $s) : ?>
                    <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md">Add Mapping</button>
        </form>
    </div>
<?php include "footer.php"; ?>

