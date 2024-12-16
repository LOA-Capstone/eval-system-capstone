<?php
include 'db_connect.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($id <= 0){
    die("Invalid irregular student ID.");
}

$qry = $conn->query("SELECT * FROM student_list WHERE id = $id AND classification='irregular'");
if($qry->num_rows == 0){
    die("No irregular student found with this ID.");
}

$student = $qry->fetch_assoc();
foreach($student as $k => $v){
    $$k = $v;
}

// Fetch currently assigned subjects for this irregular student
$assigned_pairs = array();
$sqry = $conn->query("SELECT r.id as rid 
                      FROM irregular_student_subjects iss
                      INNER JOIN restriction_list r ON r.academic_id = iss.academic_id AND r.faculty_id=iss.faculty_id AND r.subject_id=iss.subject_id
                      WHERE iss.student_id = $id");
while($row = $sqry->fetch_assoc()){
    $assigned_pairs[] = $row['rid'];
}

// Now include the new_irregular_student.php and it will use $assigned_pairs to pre-select
include 'new_irregular_student.php';
?>
