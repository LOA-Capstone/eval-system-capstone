<?php
include 'db_connect.php';
$department_id = $_SESSION['login_department_id'];
$faculty_id = $_GET['id'];

// Check if the faculty belongs to the Dean's department
$qry = $conn->query("SELECT * FROM faculty_list WHERE id = '$faculty_id' AND department_id = '$department_id'");
if ($qry->num_rows > 0) {
    $faculty = $qry->fetch_assoc();
    foreach ($faculty as $k => $v) {
        $$k = $v;
    }
    include 'new_faculty.php';
} else {
    echo "<h4>You do not have permission to edit this faculty.</h4>";
    exit;
}
?>
