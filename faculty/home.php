<?php include('db_connect.php');
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
$astat = array("Not Yet Started","On-going","Closed");


$teacher_firstname = $_SESSION['login_firstname']; // Teacher's first name from session
$teacher_lastname = $_SESSION['login_lastname'];   // Teacher's last name from session

$sql = "SELECT 
            Class_Section, 
            Subject_Name, 
            Student_Firstname, 
            Student_Lastname
        FROM 
            student_subject_teacher
        WHERE 
            Teacher_Firstname = ? AND Teacher_Lastname = ?
        ORDER BY 
            Class_Section, Subject_Name";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $teacher_firstname, $teacher_lastname);
$stmt->execute();
$result = $stmt->get_result();

$sections = [];
while ($row = $result->fetch_assoc()) {
    $sections[$row['Class_Section']][$row['Subject_Name']][] = $row; // Group by Class_Section and then by Subject_Name
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

<div class="section-container">
    <?php
    // Array to track sections that have already been displayed
    $displayed_sections = [];

    foreach ($sections as $section => $subjects): 
        // Skip the section if it's already displayed
        if (in_array($section, $displayed_sections)) {
            continue;
        }

        // Add the section to the displayed list to prevent duplicates
        $displayed_sections[] = $section;
    ?>
        <div class="section-card">
            <?php foreach ($subjects as $subject => $students): 
                $total_students = count($students); // Count total students for this section and subject
            ?>
                <div class="cardt">
                    <div class="content">
                        <span class="title"><?php echo htmlspecialchars($section); ?></span>
                        <div class="desc"><?php echo htmlspecialchars($subject); ?></div>
                        <div class="actions">
                            <div>
                                <div class="viewstudent">Total Students</div>
                                <div class="viewstudent"><?php echo $total_students; ?></div>
                            </div>
                            <div>
                                <div class="viewstudent">Evaluations Completed</div>
                                <div class="viewstudent">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>


<style>

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

/* General card styles */
.section-card .cardt {
  width: auto;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  border-radius: 0.5rem;
  background:radial-gradient(178.94% 106.41% at 26.42% 106.41%, #FFFFFF 0%, #B1E4FF 71.88%);
  padding: 1rem;
  color: rgb(107, 114, 128);
  box-shadow: 0px 87px 78px -39px rgba(0, 0, 0, 0.4);
  border: 1px solid #1C204B;
  margin: 1rem;
}


/* Content styling */
.cardt .content {
  margin-left: 0.75rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  font-weight: 400;
}

/* Title styling */
.cardt .content .title {
  margin-bottom: 0.25rem;
  font-size: 1.9rem;
  line-height: 1.25rem;
  font-weight: 600;
  color: #1C204B;
}

/* Description styling */
.cardt .content .desc {
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  font-weight: 600;
  color: #1C204B;
  border-bottom: 1px solid black;
}

/* Action buttons */
.cardt .content .actions {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  grid-gap: 0.5rem;
  gap: 0.5rem;
}

.viewstudent{
  color: #1C204B;
}

</style>