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
    // Initialize $displayed_sections as an empty array to track displayed sections
    $displayed_sections = [];

    // Fetch the current term from the session (default to 'Not Set' if not available)
    $current_term = $_SESSION['academic']['term'] ?? 'Not Set';

    // Iterate over sections and subjects
    foreach ($sections as $section => $subjects):
        // Skip the section if it's already displayed
        if (in_array($section, $displayed_sections)) {
            continue;
        }

        // Add the section to the displayed list to prevent duplicates
        $displayed_sections[] = $section;

        // Fetch the class_id for the current section
        $class_query = $conn->prepare("SELECT id FROM class_list WHERE section = ?");
        $class_query->bind_param('s', $section);
        if ($class_query->execute()) {
            $class_result = $class_query->get_result();
            $class_data = $class_result->fetch_assoc();
            $class_id = $class_data['id'] ?? null;
        } else {
            // Handle query error
            echo "Error fetching class_id for section: $section";
            continue; // Skip to next section if query fails
        }

        // Initialize the evaluations count
        $completed_evaluations = 0;
        $total_students = 0;

        // Check if class_id and current_term are valid
        if ($class_id && $current_term !== 'Not Set') {
            // Fetch the teacher details from faculty_list and faculty_classes to match the teacher with the class
            $teacher_query = $conn->prepare("
                SELECT f.firstname, f.lastname, fc.class_id 
                FROM faculty_list f
                JOIN faculty_classes fc ON fc.faculty_id = f.id
                WHERE fc.class_id = ?
            ");
            $teacher_query->bind_param('i', $class_id);
            if ($teacher_query->execute()) {
                $teacher_result = $teacher_query->get_result();
                while ($teacher_data = $teacher_result->fetch_assoc()) {
                    $teacher_firstname = $teacher_data['firstname'];
                    $teacher_lastname = $teacher_data['lastname'];

                    // Count students who have evaluated the teacher
                    $eval_query = $conn->prepare("
                        SELECT COUNT(DISTINCT e.student_id) AS evaluations_completed
                        FROM evaluation_list e
                        JOIN faculty_list f ON e.faculty_id = f.id
                        JOIN academic_list a ON e.academic_id = a.id
                        WHERE e.class_id = ? 
                        AND e.faculty_id = (SELECT id FROM faculty_list WHERE firstname = ? AND lastname = ?)
                        AND a.term = ?
                    ");
                    $eval_query->bind_param('isss', $class_id, $teacher_firstname, $teacher_lastname, $current_term);
                    if ($eval_query->execute()) {
                        $eval_result = $eval_query->get_result();
                        $eval_data = $eval_result->fetch_assoc();
                        $completed_evaluations = $eval_data['evaluations_completed'] ?? 0;
                    } else {
                        // Handle query error
                        echo "Error fetching evaluations count for teacher: $teacher_firstname $teacher_lastname";
                        continue; // Skip to next teacher if query fails
                    }
                }
            } else {
                // Handle query error
                echo "Error fetching teacher details for section: $section";
                continue; // Skip to next section if query fails
            }
        }
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
                                <div class="viewstudent"><?php echo $completed_evaluations; ?></div>
                                <div class="viewstudent"><?php echo htmlspecialchars($current_term); ?></div>
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