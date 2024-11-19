<?php

$login_name = $_SESSION['login_name']; // Assuming login_name is in 'Firstname Lastname' format

// Create a connection to the database
$conn = new mysqli('localhost', 'root', '', 'evaluation_db');

// Check for a connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get the student evaluations along with the teacher's name and evaluation date and time
$query = "
    SELECT 
        CONCAT(sl.firstname, ' ', sl.lastname) AS student_name,
        CONCAT(fl.firstname, ' ', fl.lastname) AS teacher_name,
        el.date_taken 
    FROM evaluation_list el
    JOIN faculty_list fl ON el.faculty_id = fl.id
    JOIN student_list sl ON el.student_id = sl.id
    WHERE CONCAT(sl.firstname, ' ', sl.lastname) = '$login_name'
";

// Execute the query
$result = $conn->query($query);

// Check if there are results
if ($result->num_rows > 0) {
    echo '<div class="evaluation-history">';
    
    // Loop through the results and display them
    while ($row = $result->fetch_assoc()) {
        // Format the date and time into a readable format (e.g., "November 18, 2024 3:30 PM")
        $formatted_date_time = date("F j, Y - g:i A", strtotime($row['date_taken']));
        $student_name = htmlspecialchars($row['student_name']);
        $teacher_name = htmlspecialchars($row['teacher_name']);
        
        // Display each evaluation entry
        echo '<div class="evaluation-entry">
                <div class="evaluation-details">
                    <p class="evaluation-date">' . $formatted_date_time . '</p>
                    <p class="teacher-name">' . $teacher_name . '</p>
                </div>
              </div>';
    }
    
    echo '</div>';
} else {
    echo "No evaluations found for this student.";
}

// Close the database connection
$conn->close();
?>



<style>
.evaluation-history {
  background: transparent;
  padding: 1em;
  padding-bottom: 1.1em;
  border-radius: 15px;
  margin: 1em;
}

.history-header {
  display: flex;
  align-items: center;
  margin-bottom: 1em;
}

.history-icon {
  width: 30px;
  height: 30px;
  margin-right: 0.5em;
  stroke: #1C204B; /* Icon color */
}

.history-title {
  font-size: 1.4em;
  font-weight: bold;
  color: #1C204B; /* Title color */
}

.evaluation-entry {
  display: flex;
  flex-direction: row;
  align-items: center;
  padding: 1em;
  margin-bottom: 0.8em;
  border-radius: 10px;
  background: radial-gradient(178.94% 106.41% at 26.42% 106.41%, #FFFFFF 0%, #B1E4FF 71.88%);
  border: 1px solid #1C204B; /* Border color */
  transition: background-color 0.3s ease;
  width: auto;
  height: 100px;
}

.evaluation-entry:hover {
  background-color: #f0f0f0;
  cursor: pointer;
}

.evaluation-date {
  font-size: 1.5em;
  font-weight: 900 !important;
  color: #1C204B; 
}

.teacher-name {
  font-size: 0.9em;
  color: #666;
  font-weight: 550;
}
</style>
