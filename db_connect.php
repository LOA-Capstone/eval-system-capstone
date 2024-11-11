<?php 

$conn= new mysqli('localhost','root','','evaluation_db')or die("Could not connect to mysql".mysqli_error($con));

// Fetch the default academic year
$academic_result = $conn->query("SELECT * FROM academic_list WHERE is_default = 1");
if ($academic_result) {
    $academic = $academic_result->fetch_assoc();
    if ($academic) {
        $_SESSION['academic'] = $academic;
    } else {
        // Handle case where no default academic year is set
        $_SESSION['academic'] = null;
    }
} else {
    // Handle query error
    die("Error fetching academic data: " . $conn->error);
}