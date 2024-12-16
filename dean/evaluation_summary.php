<?php
include 'db_connect.php';

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if login_department_id is set
if (!isset($_SESSION['login_department_id'])) {
    echo "<h4>Error: Department ID not set in session.</h4>";
    exit;
}

$department_id = $_SESSION['login_department_id'];

// Get the selected faculty_id from the GET parameters
$faculty_id = isset($_GET['fid']) ? intval($_GET['fid']) : 0;

function get_academic_id($year, $semester, $term) {
    global $conn;
    $term = $conn->real_escape_string($term);
    $qry = $conn->query("SELECT id FROM academic_list WHERE year = '$year' AND semester = '$semester' AND term = '$term' LIMIT 1");
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        return $row['id'];
    }
    return null;
}
function int_to_roman($num) {
    $n = intval($num);
    $result = '';
    $lookup = array(
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' =>9,
        'V' =>5,
        'IV' =>4,
        'I' =>1
    );
    foreach($lookup as $roman => $value){
        $matches = intval($n/$value);
        $result .= str_repeat($roman,$matches);
        $n = $n % $value;
    }
    return $result;
}
// Fetch all faculty members under the Dean's department
$f_arr = array();
$fname = array();
$stmt = $conn->prepare("SELECT id, firstname, lastname FROM faculty_list WHERE department_id = ? ORDER BY CONCAT(firstname,' ',lastname) ASC");
$stmt->bind_param("i", $department_id);
$stmt->execute();
$faculty = $stmt->get_result();

while ($row = $faculty->fetch_assoc()) {
    $f_arr[$row['id']] = $row;
    $fname[$row['id']] = ucwords($row['firstname'] . ' ' . $row['lastname']);
}

$stmt->close();

// Validate the selected faculty, if any
if ($faculty_id > 0) {
    $stmt = $conn->prepare("SELECT id FROM faculty_list WHERE id = ? AND department_id = ?");
    $stmt->bind_param("ii", $faculty_id, $department_id);
    $stmt->execute();
    $faculty_check = $stmt->get_result();
    
    if ($faculty_check->num_rows == 0) {
        // Faculty not in Dean's department
        echo "<h4>You do not have permission to view reports for this faculty.</h4>";
        exit;
    }
    $stmt->close();
}

// Get current academic year and semester from session
$current_year = $_SESSION['academic']['year'];
$current_semester = $_SESSION['academic']['semester'];

// Function definitions (get_academic_id, int_to_roman, get_evaluation_summary, get_remarks) remain unchanged
function get_evaluation_summary($academic_id, $faculty_id) {
    global $conn;
    $data = array();

    if (!$academic_id) {
        return $data;
    }

    // Get all evaluation_list entries for this academic_id and faculty_id
    $evaluation_ids = array();
    $faculty_id = intval($faculty_id);
    $qry = $conn->query("SELECT evaluation_id FROM evaluation_list WHERE academic_id = '$academic_id' AND faculty_id = '$faculty_id'");
    while ($row = $qry->fetch_assoc()) {
        $evaluation_ids[] = $row['evaluation_id'];
    }

    if (empty($evaluation_ids)) {
        return $data;
    }

    // Sanitize evaluation_ids for SQL IN clause
    $evaluation_ids = array_map('intval', $evaluation_ids);
    $evaluation_ids_str = implode(',', $evaluation_ids);

    // Get all evaluation_answers for these evaluation_ids
    $answers = array();
    $qry = $conn->query("SELECT question_id, rate FROM evaluation_answers WHERE evaluation_id IN ($evaluation_ids_str)");
    while ($row = $qry->fetch_assoc()) {
        $qid = $row['question_id'];
        $rate = $row['rate'];
        if (!isset($answers[$qid])) {
            $answers[$qid] = array();
        }
        $answers[$qid][] = $rate;
    }

    // Compute average ratings per question
    $question_avg = array();
    foreach ($answers as $qid => $rates) {
        $question_avg[$qid] = array_sum($rates) / count($rates);
    }

    // Get the list of criteria
    $criteria = array();
    $qry = $conn->query("SELECT * FROM criteria_list ORDER BY order_by ASC");
    while ($row = $qry->fetch_assoc()) {
        $criteria[$row['id']] = $row['criteria'];
    }

    // Get the list of questions
    $questions = array();
    $qry = $conn->query("SELECT * FROM question_list WHERE academic_id = '$academic_id' ORDER BY order_by ASC");
    while ($row = $qry->fetch_assoc()) {
        $qid = $row['id'];
        $cid = $row['criteria_id'];
        $questions[$qid] = array(
            'question' => $row['question'],
            'criteria_id' => $cid,
        );
    }

    // Group questions by criteria
    $criteria_questions = array();
    foreach ($questions as $qid => $qdata) {
        $cid = $qdata['criteria_id'];
        if (!isset($criteria_questions[$cid])) {
            $criteria_questions[$cid] = array();
        }
        $criteria_questions[$cid][$qid] = $qdata['question'];
    }

    // Compute average ratings per criteria
    $criteria_avg = array();
    foreach ($criteria_questions as $cid => $qids) {
        $sum = 0;
        $count = 0;
        foreach ($qids as $qid => $question) {
            if (isset($question_avg[$qid])) {
                $sum += $question_avg[$qid];
                $count++;
            }
        }
        if ($count > 0) {
            $criteria_avg[$cid] = $sum / $count;
        } else {
            $criteria_avg[$cid] = 0;
        }
    }

    // Compute total average
    $total_sum = 0;
    $total_count = 0;
    foreach ($question_avg as $avg) {
        $total_sum += $avg;
        $total_count++;
    }
    $total_average = $total_count > 0 ? $total_sum / $total_count : 0;

    $data['criteria'] = $criteria;
    $data['criteria_questions'] = $criteria_questions;
    $data['question_avg'] = $question_avg;
    $data['criteria_avg'] = $criteria_avg;
    $data['total_average'] = $total_average;

    // Add total respondents
    $data['total_respondents'] = count($evaluation_ids);

    return $data;
}
// [Ensure these functions are defined here as per your original code]

// Only load data if a faculty is selected
if ($faculty_id > 0) {
    $midterm_academic_id = get_academic_id($current_year, $current_semester, 'Midterm');
    $finals_academic_id = get_academic_id($current_year, $current_semester, 'Finals');

    $midterm_data = get_evaluation_summary($midterm_academic_id, $faculty_id);
    $finals_data = get_evaluation_summary($finals_academic_id, $faculty_id);
} else {
    $midterm_data = array();
    $finals_data = array();
}

// Fetch faculty details if faculty_id is selected
if ($faculty_id > 0) {
    $stmt = $conn->prepare("SELECT f.*, d.name as department FROM faculty_list f LEFT JOIN department_list d ON f.department_id = d.id WHERE f.id = ?");
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $faculty_qry = $stmt->get_result();
    
    if($faculty_qry->num_rows > 0){
        $faculty = $faculty_qry->fetch_assoc();
        $faculty_name = $faculty['firstname'] . ' ' . $faculty['lastname'];
        $faculty_school_id = $faculty['school_id'];
        $department = $faculty['department'];
        $status = $faculty['status'];
    } else {
        $faculty_name = '';
        $faculty_school_id = '';
        $department = '';
        $status = '';
    }
    $stmt->close();
} else {
    $faculty_name = '';
    $faculty_school_id = '';
    $department = '';
    $status = '';
}

// Get today's date
$date_today = date('Y-m-d');

// Get current academic year and semester
$stmt = $conn->prepare("SELECT * FROM academic_list WHERE year = ? AND semester = ? AND is_default = 1 LIMIT 1");
$stmt->bind_param("ss", $current_year, $current_semester);
$stmt->execute();
$academic_result = $stmt->get_result();

if($academic_result->num_rows > 0){
    $academic = $academic_result->fetch_assoc();
    $academic_year = $academic['year'];
    $semester = $academic['semester'];
} else {
    $academic_year = '';
    $semester = '';
}
$stmt->close();

// Function to generate remarks based on rating remains unchanged

// Compute combined average rating if data available
$midterm_total = (isset($midterm_data['total_average']) && isset($midterm_data['total_respondents'])) ? $midterm_data['total_average'] * $midterm_data['total_respondents'] : 0;
$finals_total = (isset($finals_data['total_average']) && isset($finals_data['total_respondents'])) ? $finals_data['total_average'] * $finals_data['total_respondents'] : 0;
$total_respondents = ($midterm_data['total_respondents'] ?? 0) + ($finals_data['total_respondents'] ?? 0);
if($total_respondents > 0){
    $combined_total_rating = ($midterm_total + $finals_total) / $total_respondents;
} else {
    $combined_total_rating = 0;
}

// Get remarks
$midterm_remarks = get_remarks($midterm_data['total_average'] ?? 0);
$finals_remarks = get_remarks($finals_data['total_average'] ?? 0);
$average_remarks = get_remarks($combined_total_rating);
function get_remarks($rating) {
    if($rating >= 4.51 && $rating <= 5.0){
        return 'Outstanding';
    } elseif($rating >= 3.51 && $rating <= 4.5){
        return 'Very Satisfactory';
    } elseif($rating >= 2.51 && $rating <= 3.5){
        return 'Satisfactory';
    } elseif($rating >= 1.51 && $rating <= 2.5){
        return 'Fair';
    } elseif($rating >= 1.0 && $rating <= 1.5){
        return 'Needs Improvement';
    } else {
        return 'No Data';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Summary</title>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <!-- Include custom CSS for print and styling -->
    <style>
        /* [Your existing CSS remains unchanged] */
        @media print {
            /* Print styles */
            .signatory {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
            }
            .signatory h3 {
                padding-bottom: 100px;
                font-weight: bolder;
            }
            .signatory h1 {
                text-decoration: underline;
            }
            .prepared {
                width: 45%;
                text-align: center;
                padding: 10px;
            }
            .noted {
                width: 45%;
                text-align: center;
                padding: 10px;
            }
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
            body {
                background-color: white;
            }
            .content-container {
                box-shadow: none;
                border: none;
            }
            .nav-tabs {
                display: none;
            }
            .header-section {
                margin-bottom: 20px;
            }
            .evaluation-type {
                text-align: center;
                margin-bottom: 20px;
            }
            .summary-section {
                margin-top: 30px;
            }
            .summary-section table th, .summary-section table td {
                font-weight: bold;
            }
        }
        @media screen {
            .print-only {
                display: none;
            }
            .signatory {
                display: none;
            }
        }
        body {
            background-color: #f8f9fa;
        }
        .content-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-borderless td, .table-borderless th {
            border: none;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            border: 1px solid #dee2e6;
            padding: 8px;
        }
        .table-bordered {
            border: 2px solid #dee2e6;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
        }
        .tab-content {
            margin-top: 20px;
        }
        .remarks {
            background-color: #f1f1f1;
            border-left: 5px solid #007bff;
            padding: 10px 15px;
            margin-top: 15px;
            font-style: italic;
        }
        .summary-section {
            margin-top: 20px;
        }
        .summary-section h4 {
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="callout callout-info">
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="evaluation_summary">
        <div class="d-flex w-100 justify-content-center align-items-center no-print">
            <label for="faculty_id">Select Faculty</label>
            <div class="mx-2 col-md-4">
                <select name="fid" id="faculty_id" class="form-control form-control-sm select2" onchange="this.form.submit()" aria-label="Select Faculty">
                    <option value="">-- Select Faculty --</option>
                    <?php foreach ($f_arr as $row): ?>
                        <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo ($faculty_id == $row['id']) ? "selected" : "" ?>>
                            <?php echo htmlspecialchars($fname[$row['id']]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
</div>
<div class="container my-4">
    <div class="content-container">
        <div class="header-section">
            <center><h2 class="mb-4">Faculty Performance Evaluation Students Evaluation</h2></center>
            <?php if($faculty_id > 0): ?>
            <button class="btn btn-sm btn-success no-print mb-3" onclick="window.print()" aria-label="Print Evaluation Summary">
                <i class="fas fa-print"></i> Print
            </button>
            <?php endif; ?>
        </div>
        
        <?php if($faculty_id <= 0): ?>
            <p>Please select a faculty to view the summary report.</p>
        <?php else: ?>
        <ul class="nav nav-tabs no-print">
            <li class="nav-item">
                <a class="nav-link <?php echo (!isset($_GET['s']) || $_GET['s'] == 'midterm') ? 'active' : ''; ?>" href="index.php?page=evaluation_summary&fid=<?php echo $faculty_id; ?>&s=midterm" aria-label="View Midterm Evaluation">Midterm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($_GET['s']) && $_GET['s'] == 'finals') ? 'active' : ''; ?>" href="index.php?page=evaluation_summary&fid=<?php echo $faculty_id; ?>&s=finals" aria-label="View Finals Evaluation">Finals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($_GET['s']) && $_GET['s'] == 'summary') ? 'active' : ''; ?>" href="index.php?page=evaluation_summary&fid=<?php echo $faculty_id; ?>&s=summary" aria-label="View Summary Evaluation">Summary</a>
            </li>
        </ul>
        <div class="tab-content">
            <!-- Midterm Tab -->
            <div id="midterm" class="tab-pane fade <?php echo (!isset($_GET['s']) || $_GET['s'] == 'midterm') ? 'show active' : ''; ?>">
                <?php if (empty($midterm_data)): ?>
                    <p>No evaluation data available for Midterm.</p>
                <?php else: ?>
                    <?php
                    $criteria = $midterm_data['criteria'];
                    $criteria_questions = $midterm_data['criteria_questions'];
                    $question_avg = $midterm_data['question_avg'];
                    $criteria_avg = $midterm_data['criteria_avg'];
                    $criteria_counter = 1;
                    ?>
                    <div class="evaluation-type print-only">
                        <h3>Midterm Evaluation Summary</h3>
                    </div>
                    <div class="print-only">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($faculty_name); ?></p>
                        <p><strong>Academic Year:</strong> <?php echo htmlspecialchars($academic_year); ?></p>
                    </div>
                    <?php foreach ($criteria_questions as $cid => $questions): ?>
                        <h4><?php echo int_to_roman($criteria_counter) . '. ' . htmlspecialchars($criteria[$cid]); ?></h4>
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Average Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $question_counter = 1;
                                foreach ($questions as $qid => $question):
                                ?>
                                <tr>
                                    <td><?php echo $question_counter; ?></td>
                                    <td><?php echo htmlspecialchars($question); ?></td>
                                    <td><?php echo isset($question_avg[$qid]) ? number_format($question_avg[$qid], 2) : 'N/A'; ?></td>
                                </tr>
                                <?php
                                $question_counter++;
                                endforeach;
                                ?>
                                <tr>
                                    <td colspan="2"><strong>Average</strong></td>
                                    <td><strong><?php echo number_format($criteria_avg[$cid], 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php $criteria_counter++; ?>
                    <?php endforeach; ?>
                    
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2"><strong>Total Average</strong></td>
                            <td><strong><?php echo number_format($midterm_data['total_average'], 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Remarks</strong></td>
                            <td><?php echo htmlspecialchars($midterm_remarks); ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Finals Tab -->
            <div id="finals" class="tab-pane fade <?php echo (isset($_GET['s']) && $_GET['s'] == 'finals') ? 'show active' : ''; ?>">
                <?php if (empty($finals_data)): ?>
                    <p>No evaluation data available for Finals.</p>
                <?php else: ?>
                    <?php
                    $criteria = $finals_data['criteria'];
                    $criteria_questions = $finals_data['criteria_questions'];
                    $question_avg = $finals_data['question_avg'];
                    $criteria_avg = $finals_data['criteria_avg'];
                    $criteria_counter = 1;
                    ?>
                    <div class="evaluation-type print-only">
                        <h3>Finals Evaluation Summary</h3>
                    </div>
                    <div class="print-only">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($faculty_name); ?></p>
                        <p><strong>Academic Year:</strong> <?php echo htmlspecialchars($academic_year); ?></p>
                    </div>
                    <?php foreach ($criteria_questions as $cid => $questions): ?>
                        <h4><?php echo int_to_roman($criteria_counter) . '. ' . htmlspecialchars($criteria[$cid]); ?></h4>
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Average Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $question_counter = 1;
                                foreach ($questions as $qid => $question):
                                ?>
                                <tr>
                                    <td><?php echo $question_counter; ?></td>
                                    <td><?php echo htmlspecialchars($question); ?></td>
                                    <td><?php echo isset($question_avg[$qid]) ? number_format($question_avg[$qid], 2) : 'N/A'; ?></td>
                                </tr>
                                <?php
                                $question_counter++;
                                endforeach;
                                ?>
                                <tr>
                                    <td colspan="2"><strong>Average</strong></td>
                                    <td><strong><?php echo number_format($criteria_avg[$cid], 2); ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <?php $criteria_counter++; ?>
                    <?php endforeach; ?>
                    
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2"><strong>Total Average</strong></td>
                            <td><strong><?php echo number_format($finals_data['total_average'], 2); ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Remarks</strong></td>
                            <td><?php echo htmlspecialchars($finals_remarks); ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Summary Tab -->
            <div id="summary" class="tab-pane fade <?php echo (isset($_GET['s']) && $_GET['s'] == 'summary') ? 'show active' : ''; ?>">
                <div class="mt-3">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($faculty_name); ?></td>
                            <td><strong>Academic Year:</strong></td>
                            <td><?php echo htmlspecialchars($academic_year); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Department:</strong></td>
                            <td><?php echo htmlspecialchars($department); ?></td>
                            <td><strong>Semester:</strong></td>
                            <td><?php echo htmlspecialchars($semester); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td><?php echo htmlspecialchars($status); ?></td>
                            <td><strong>Date:</strong></td>
                            <td><?php echo htmlspecialchars($date_today); ?></td>
                        </tr>
                    </table>
                    <br>
                    <h3 class="text-center">SUMMARY EVALUATION</h3>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th></th>
                                <th>No. of Respondents</th>
                                <th>Rating</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Midterm</td>
                                <td><?php echo htmlspecialchars($midterm_data['total_respondents'] ?? 0); ?></td>
                                <td><?php echo (isset($midterm_data['total_average']) && ($midterm_data['total_respondents'] > 0)) ? number_format($midterm_data['total_average'], 2) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($midterm_remarks); ?></td>
                            </tr>
                            <tr>
                                <td>Finals</td>
                                <td><?php echo htmlspecialchars($finals_data['total_respondents'] ?? 0); ?></td>
                                <td><?php echo (isset($finals_data['total_average']) && ($finals_data['total_respondents'] > 0)) ? number_format($finals_data['total_average'], 2) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($finals_remarks); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Average</strong></td>
                                <td><strong><?php echo ($total_respondents > 0) ? number_format($combined_total_rating, 2) : 'N/A'; ?></strong></td>
                                <td><strong><?php echo htmlspecialchars($average_remarks); ?></strong></td>
                            </tr>
                        </tbody>
                    </table><br>
                    <div class="signatory">
                        <div class="prepared">
                            <br>
                            <h3>Prepared by:</h3>
                            <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h1>
                            <p><strong>Regie Ellana</strong></p>
                            <p><strong>CCS Dean</strong></p>
                        </div>
                        <div class="noted">
                            <br>
                            <h3>Noted by:</h3>
                            <h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h1>
                            <p><strong>Dr. Leah P. Digo</strong></p>
                            <p><strong>VP for Academic Affairs</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Include Bootstrap JS and dependencies (Popper.js and jQuery) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3HbpN6Nnt9z5yP5iJ87mLqX2YY7fs7BJwG6N7" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
