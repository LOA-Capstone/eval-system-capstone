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
    ORDER BY el.date_taken DESC
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
/* Reset and base styles for consistency */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9fafb;
    color: #333;
    line-height: 1.6;
    padding: 20px;
}

/* Container for evaluation history with a sleek background */
.evaluation-history {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(174, 217, 250, 0.8));
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
    max-width: 900px;
    margin: 0 auto;
    backdrop-filter: blur(8px);
    overflow: hidden;
}

/* Header with icon and title */
.history-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    border-bottom: 2px solid #ddd;
    padding-bottom: 10px;
}

.history-icon {
    width: 35px;
    height: 35px;
    margin-right: 12px;
    fill: #1C204B;
}

.history-title {
    font-size: 2em;
    font-weight: 700;
    color: #1C204B;
    letter-spacing: 0.5px;
    text-transform: capitalize;
}

/* Style each evaluation entry as a card */
.evaluation-entry {
    display: flex;
    flex-direction: column;
    padding: 20px 25px;
    margin-bottom: 20px;
    border-left: 5px solid #1C204B;
    background: linear-gradient(135deg, #ffffff 0%, #B1E4FF 100%);
    border-radius: 12px;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    cursor: pointer;
}

.evaluation-entry:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #D4EBFF 0%, #B1E4FF 100%);
}

/* Content inside the evaluation entry */
.evaluation-details {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.evaluation-date {
    font-size: 1.4em;
    font-weight: 600;
    color: #1C204B;
    margin-bottom: 10px;
    letter-spacing: 0.5px;
}

.teacher-name {
    font-size: 1.1em;
    color: #555;
    font-weight: 500;
    margin-bottom: 10px;
}

/* Add a subtle hover animation to the header icon */
.history-icon path {
    transition: all 0.3s ease;
}

.history-header:hover .history-icon path {
    fill: #ff6b81; /* Icon color change on hover */
}

/* No evaluations message */
.no-evaluations {
    text-align: center;
    padding: 20px;
    background: #fff3f3;
    border: 1px solid #ffcccc;
    border-radius: 12px;
    color: #cc0000;
    font-size: 1.3em;
    margin: 0 auto;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .evaluation-history {
        padding: 20px;
    }

    .history-title {
        font-size: 1.6em;
    }

    .evaluation-entry {
        padding: 15px 20px;
    }

    .evaluation-date {
        font-size: 1.2em;
    }

    .teacher-name {
        font-size: 1em;
    }
}

@media (max-width: 480px) {
    .history-title {
        font-size: 1.4em;
    }

    .evaluation-entry {
        padding: 12px 15px;
    }

    .evaluation-date {
        font-size: 1.1em;
    }

    .teacher-name {
        font-size: 0.9em;
    }
}
</style>
