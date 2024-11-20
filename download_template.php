<?php
// Set headers to download file rather than display
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=student_template.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('school_id', 'firstname', 'lastname', 'email', 'curriculum', 'level', 'section'));

// Optional: Output sample data
fputcsv($output, array('123456', 'John', 'Doe', 'john.doe@example.com', 'BSIT', '4', 'A1'));
fputcsv($output, array('789012', 'Jane', 'Smith', 'jane.smith@example.com', 'BSCS', '3', 'B2'));

// Close the output stream
fclose($output);
exit();
?>
