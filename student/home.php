<?php 
include('db_connect.php');

// Function to add ordinal suffix to a number (1st, 2nd, 3rd, etc.)
function ordinal_suffix1($num){
    $num = $num % 100; // Protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}

$astat = array("Not Yet Started", "Started", "Closed");

// Get login details from session
$login_name = $_SESSION['login_name'];  // Assuming login_name is in 'Firstname Lastname' format
$academic_id = $_SESSION['academic']['id'];
$student_id = $_SESSION['login_id'];

// Prepare the SQL statement with placeholders
$stmt = $conn->prepare("
    SELECT 
        sst.Teacher_Firstname, 
        sst.Teacher_Lastname, 
        sst.Subject_name,
        CASE 
            WHEN el.evaluation_id IS NOT NULL THEN 1 
            ELSE 0 
        END AS is_evaluated
    FROM student_subject_teacher sst
    JOIN faculty_list fl 
        ON fl.firstname = sst.Teacher_Firstname 
        AND fl.lastname = sst.Teacher_Lastname
    JOIN subject_list sl 
        ON sl.subject = sst.Subject_name
    LEFT JOIN evaluation_list el 
        ON el.faculty_id = fl.id 
        AND el.subject_id = sl.id 
        AND el.student_id = ? 
        AND el.academic_id = ?
    WHERE CONCAT(sst.Student_Firstname, ' ', sst.Student_Lastname) = ?
");

// Bind parameters to the placeholders
$stmt->bind_param("iis", $student_id, $academic_id, $login_name);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Prepare an array to store teacher and subject details
$teachers = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teacherName = $row['Teacher_Firstname'] . ' ' . $row['Teacher_Lastname'];
        $subjectName = $row['Subject_name'];
        $isEvaluated = $row['is_evaluated'];
        // Add teacher name, subject, and evaluation status as an associative array
        $teachers[] = ['name' => $teacherName, 'subject' => $subjectName, 'is_evaluated' => $isEvaluated];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing head content -->
    <meta charset="UTF-8">
    <title>Evaluation Page</title>
    <!-- Bootstrap CSS (Ensure Bootstrap is included) -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Font Awesome for icons -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <style>
    .column {
      display: flex;
      flex-direction: column; 
      align-items: center;
      gap: 15px; 
      margin-top: 20px;
    }
    
    .header {
      font-size: 24px;
      font-weight: 700;
      color: #333;
      margin: 0;
      width: 100%; 
      text-align: left; 
    }
    
    .card-containers {
      display: flex;
      flex-wrap: wrap; 
      gap: 15px; 
    }
    
    
    /* Teacher card design */
    
    .teacher-card-link{
      background: transparent;
      text-align: center;
    }

    .teacher-card-link.disabled {
      pointer-events: none; /* Disable click */
      opacity: 0.6; /* Visual indication of disabled state */
    }

    .Teacher-card {
      position: relative; /* For badge positioning */
      background: radial-gradient(178.94% 106.41% at 26.42% 106.41%, #B1E4FF 0%, #FFFFFF 71.88%);
      border: 1px solid #1C204B;
      border-radius: 8px;
      width: 250px;
      height: 200px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    
    .Teacher-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
    }
    
    /* Icon container styling */
    .icon-container {
        background-color: #B1E4FF;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        margin: 0 auto 10px auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon {
        color: #1C204B;
        font-size: 24px;
    }
    
    /* Message text container styling */
    .message-text-container {
        margin-top: 10px;
    }
    
    .message-text {
        font-size: 20px;
        font-weight: bold;
        color: #1C204B;
        margin-bottom: 5px;
    }
    
    .sub-text {
        font-size: 16px;
        color: #1C204B;
    }
    
    .badge {
        position: absolute;
        top: 15px;
        right: 15px;
    }
    
    /* Academic Year and Evaluation Status Styling */
    .academic-year {
        color: yellow; 
        font-weight: bold; 
        font-size: 1.2em; 
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6), 
                     -2px -2px 4px rgba(0, 0, 0, 0.6),
                     2px -2px 4px rgba(0, 0, 0, 0.6),
                     -2px 2px 4px rgba(0, 0, 0, 0.6);  
    }
    
    .evaluation-status {
        color: yellow;
        font-weight: bold;
        font-size: 1.2em;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6), 
                     -2px -2px 4px rgba(0, 0, 0, 0.6),
                     2px -2px 4px rgba(0, 0, 0, 0.6),
                     -2px 2px 4px rgba(0, 0, 0, 0.6); 
    }
    
    .card-body {
  display: flex;
  flex-direction: column; 
  padding: 1rem; 
  background-color:#B1E4FF ;
}

    .card-body h5, .card-body h6 {
        color: black;
        transition: transform 300ms ease;
        z-index: 5;
    }
    
    .card-body h5:hover, .card-body h6:hover {
        transform: translateX(0.15rem); 
    }
    
    .card-body h6 {
        
        padding: 0 1.25rem; 
    }
    
    .card-body h4 {
        color: #1C204B;
        padding: 0;
        margin: 0;
        font-weight: 600;
    }
    
    
    .card::before {
      position: absolute;
      width: 20rem; 
      height: 20rem;
      transform: translate(-50%, -50%);
      background: radial-gradient(circle closest-side at center, white, transparent);
      opacity: 0;
      transition: opacity 300ms ease;
      z-index: 3;
    }
    
    .card:hover::before {
      opacity: 0.1; 
    }
    
    .card:hover .notiborderglow {
      opacity: 0.1;
    }
    
    
    .note {
      color: #32a6ff; 
      position: fixed;
      top: 80%;
      left: 50%;
      transform: translateX(-50%);
      text-align: center;
      font-size: 0.9rem;
      width: 75%;
    }
    
    /* Tooltip styling */
    [data-bs-toggle="tooltip"] {
        cursor: not-allowed;
    }
    </style>
</head>
<body>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4>&nbsp;Welcome <?php echo htmlspecialchars($_SESSION['login_name']); ?>!</h4> 
                <br>
                <div class="col-md-5">
                    <?php if (isset($_SESSION['academic'])): ?>
                        <h5>
                            <b>&nbsp;Academic Year:</b>
                            <span class="academic-year">
                                <?php echo htmlspecialchars($_SESSION['academic']['year']); ?>
                            </span>
                        </h5>
                        <h6>
                            <b>&nbsp;Evaluation Status:</b>
                            <span class="evaluation-status">
                                <?php echo htmlspecialchars($astat[$_SESSION['academic']['status']]); ?>
                            </span>
                            <b>&nbsp;Semester:</b>
                            <span class="evaluation-status">
                                <?php echo ordinal_suffix1($_SESSION['academic']['semester']); ?>
                            </span>
                            <b>&nbsp;Term:</b>
                            <span class="evaluation-status">
                                <?php echo htmlspecialchars($_SESSION['academic']['term'] ?? 'Not Set'); ?>
                            </span>
                        </h6>
                    <?php else: ?>
                        <h5>No academic data available.</h5>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="column">
        <!-- Header -->
        <h3 class="header">List of Teachers</h3>
        <div class="card-containers">
        <?php 
        // Check if teachers array is not empty
        if (!empty($teachers)) {
            foreach ($teachers as $teacher) {
                $teacherName = $teacher['name'];
                $subjectName = $teacher['subject'];
                $isEvaluated = $teacher['is_evaluated'];
                
                if (!$isEvaluated) {
                    // Not evaluated: clickable link
                    echo "<a href='index.php?page=evaluate&teacher_name=" . urlencode($teacherName) . "&subject=" . urlencode($subjectName) . "' class='teacher-card-link'>";
                    echo "  <div class='Teacher-card'>";
                    echo "    <div class='icon-container'>";
                    echo "      <i class='fa-solid fa-user-tie icon'></i>";
                    echo "    </div>";
                    echo "    <div class='message-text-container'>";
                    echo "      <p class='message-text'>$teacherName</p>";
                    echo "      <p class='sub-text'>$subjectName</p>";
                    echo "    </div>";
                    echo "    <span class='badge bg-primary rounded-pill'>Evaluate</span>";
                    echo "  </div>";
                    echo "</a>";
                } else {
                    // Evaluated: non-clickable or disabled link
                    // Option 1: Use a div instead of an 'a' tag
                    echo "<div class='teacher-card-link disabled' aria-disabled='true' data-bs-toggle='tooltip' title='You have already evaluated this faculty.'>";
                    echo "  <div class='Teacher-card'>";
                    echo "    <div class='icon-container'>";
                    echo "      <i class='fa-solid fa-user-tie icon'></i>";
                    echo "    </div>";
                    echo "    <div class='message-text-container'>";
                    echo "      <p class='message-text'>$teacherName</p>";
                    echo "      <p class='sub-text'>$subjectName</p>";
                    echo "    </div>";
                    echo "    <span class='badge bg-secondary rounded-pill'>Completed</span>";
                    echo "  </div>";
                    echo "</div>";
                    
                    // Option 2: Use 'a' tag with href="#" and disabled class
                    /*
                    echo "<a href='#' class='teacher-card-link disabled text-muted' aria-disabled='true' data-bs-toggle='tooltip' title='You have already evaluated this faculty.'>";
                    echo "  <div class='Teacher-card'>";
                    echo "    <div class='icon-container'>";
                    echo "      <i class='fa-solid fa-user-tie icon'></i>";
                    echo "    </div>";
                    echo "    <div class='message-text-container'>";
                    echo "      <p class='message-text'>$teacherName</p>";
                    echo "      <p class='sub-text'>$subjectName</p>";
                    echo "    </div>";
                    echo "    <span class='badge bg-secondary rounded-pill'>Completed</span>";
                    echo "  </div>";
                    echo "</a>";
                    */
                }
            }
        } else {
            echo "<p>No teachers found for this student.</p>";
        }
        ?>
        </div>
    </div>


    <!-- Include Bootstrap JS for tooltips and other functionalities -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function(){
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
    </script>
</body>
</html>
