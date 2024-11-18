<?php 
include('db_connect.php');

// Function to add ordinal suffix to a number (1st, 2nd, 3rd, etc.)
function ordinal_suffix1($num){
    $num = $num % 100; // protect against large numbers
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

// Get login name from session
$login_name = $_SESSION['login_name'];  // Assuming login_name is in 'Firstname Lastname' format

// Query to fetch teachers and their subjects for the logged-in student
$query = "SELECT Teacher_Firstname, Teacher_Lastname, Subject_name 
          FROM student_subject_teacher 
          WHERE CONCAT(Student_Firstname, ' ', Student_Lastname) = '$login_name'";

// Execute the query
$result = $conn->query($query);

// Prepare an array to store teacher and subject details
$teachers = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teacherName = $row['Teacher_Firstname'] . ' ' . $row['Teacher_Lastname'];
        $subjectName = $row['Subject_name'];
        // Add teacher name and subject as an associative array
        $teachers[] = ['name' => $teacherName, 'subject' => $subjectName];
    }
}

?>

<div class="col-12">
    <div class="card">
        <div class="card-body">
            <h4>&nbsp;Welcome <?php echo $_SESSION['login_name']; ?>!</h4> 
            <br>
            <div class="col-md-5">
                <?php if (isset($_SESSION['academic'])): ?>
                    <h5>
                        <b>&nbsp;Academic Year:</b>
                        <span class="academic-year">
                            <?php echo $_SESSION['academic']['year'] . ' ' . ordinal_suffix1($_SESSION['academic']['semester']); ?>
                        </span>
                    </h5>
                    <h6>
                        <b>&nbsp;Evaluation Status:</b>
                        <span class="evaluation-status">
                            <?php echo $astat[$_SESSION['academic']['status']]; ?>
                        </span>
                        <b>&nbsp;Term:</b>
                        <span class="evaluation-status">
                            <?php echo $_SESSION['academic']['term'] = $_SESSION['academic']['term'] ?? 'Not Set'; ?>
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

            
            echo "<a href='index.php?page=evaluate&teacher_name=" . urlencode($teacherName) . "&subject=" . urlencode($subjectName) . "' class='teacher-card-link'>";
            echo "  <div class='Teacher-card'>";
            echo "    <div class='icon-container'>";
            echo "      <i class='fa-solid fa-user-tie icon'></i>";
            echo "    </div>";
            echo "    <div class='message-text-container'>";
            echo "      <p class='message-text'>$teacherName</p>";
            echo "      <p class='sub-text'>$subjectName</p>";
            echo "    </div>";
            echo "  </div>";
            echo "</a>";
        }
    } else {
        echo "<p>No teachers found for this student.</p>";
    }
    ?>
    </div>
</div>


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
.Teacher-card {
  background: radial-gradient(178.94% 106.41% at 26.42% 106.41%, #B1E4FF 0%, #FFFFFF 71.88%);
    border: 1px solid #1C204B;
    border-radius: 8px;
    width: 250px;
    height: 200px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
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


.card {
  display: flex;
  flex-direction: column;
  isolation: isolate;
  position: relative;
  width: auto;
  height: auto; 
  background: #B1E4FF;
  border-radius: 1rem;
  overflow: hidden;
  font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
  font-size: 16px;
}

.card:before {
  position: absolute;
  content: "";
  inset: 0.0625rem;
  border-radius: 0.9375rem;
  background: #18181b;
  z-index: 2;
}

.card:after {
  position: absolute;
  content: "";
  width: 0.25rem;
  inset: 0.65rem auto 0.65rem 0.5rem;
  border-radius: 0.125rem;
  background: linear-gradient(to bottom, #2eadff, #3d83ff, #7e61ff);
  transition: transform 300ms ease;
  z-index: 4;
}

.card:hover:after {
  transform: translateX(0.15rem);
}

.card-body {
  display: flex;
  flex-direction: column; 
  padding: 1rem; 
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
  color: #2ECC71;
  padding: 0 1.25rem; 
}

.card-body h4 {
  color: #1C204B;
  padding: 0;
  margin: 0;
  font-weight: 600;
}


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



.card-body h5 b, .card-body h6 b {
    color: #1C204B;
    font-weight: bold;
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

</style>