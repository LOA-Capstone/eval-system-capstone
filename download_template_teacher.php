<?php
// download_template_teacher.php

// Set headers to download file rather than display
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=faculty_template.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('school_id', 'firstname', 'lastname', 'email', 'department_name'));

// Optional: Output sample data
fputcsv($output, array('F1001', 'John', 'Doe', 'john.doe@example.com', 'College of Computer Studies'));
fputcsv($output, array('F1002', 'Jane', 'Smith', 'jane.smith@example.com', 'College of Engineering (COE)'));

// Close the output stream
fclose($output);
exit();
?>
