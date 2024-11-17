<?php
include 'db_connect.php';

// Get the current academic year and semester from session
$current_year = $_SESSION['academic']['year'];
$current_semester = $_SESSION['academic']['semester'];
$faculty_id = $_SESSION['login_id'];

// Function to get academic_id for given year, semester, and term
function get_academic_id($year, $semester, $term) {
    global $conn;
    $qry = $conn->query("SELECT id FROM academic_list WHERE year = '$year' AND semester = '$semester' AND term = '$term' LIMIT 1");
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        return $row['id'];
    }
    return null;
}

// Function to convert integer to Roman numerals
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

// Now, for each term, get the academic_id
$midterm_academic_id = get_academic_id($current_year, $current_semester, 'Midterm');
$finals_academic_id = get_academic_id($current_year, $current_semester, 'Finals');

// Function to get evaluation summary data
function get_evaluation_summary($academic_id, $faculty_id) {
    global $conn;
    $data = array();

    if (!$academic_id) {
        return $data;
    }

    // Get all evaluation_list entries for this academic_id and faculty_id
    $evaluation_ids = array();
    $qry = $conn->query("SELECT evaluation_id FROM evaluation_list WHERE academic_id = '$academic_id' AND faculty_id = '$faculty_id'");
    while ($row = $qry->fetch_assoc()) {
        $evaluation_ids[] = $row['evaluation_id'];
    }

    if (empty($evaluation_ids)) {
        return $data;
    }

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

// Get evaluation summary for Midterm
$midterm_data = get_evaluation_summary($midterm_academic_id, $faculty_id);
// Similarly for Finals
$finals_data = get_evaluation_summary($finals_academic_id, $faculty_id);

// Fetch faculty details
$faculty_qry = $conn->query("SELECT f.*, d.name as department FROM faculty_list f LEFT JOIN department_list d ON f.department_id = d.id WHERE f.id = '$faculty_id'");
if($faculty_qry->num_rows > 0){
    $faculty = $faculty_qry->fetch_assoc();
    $faculty_name = $faculty['firstname'] . ' ' . $faculty['lastname'];
    $department = $faculty['department'];
} else {
    // Handle error
    $faculty_name = '';
    $department = '';
}

// Get today's date
$date_today = date('Y-m-d');

// Get current academic year and semester
$academic_qry = $conn->query("SELECT * FROM academic_list WHERE year = '$current_year' AND semester = '$current_semester' AND is_default = 1 LIMIT 1");
if($academic_qry->num_rows > 0){
    $academic = $academic_qry->fetch_assoc();
    $academic_year = $academic['year'];
    $semester = $academic['semester'];
} else {
    // Handle error
    $academic_year = '';
    $semester = '';
}

// Function to generate remarks based on rating
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

// Compute combined average rating
$total_respondents = ($midterm_data['total_respondents'] ?? 0) + ($finals_data['total_respondents'] ?? 0);
if($total_respondents > 0){
    $combined_total_rating = 0;
    if(isset($midterm_data['total_respondents']) && $midterm_data['total_respondents'] > 0){
        $combined_total_rating += $midterm_data['total_average'] * $midterm_data['total_respondents'];
    }
    if(isset($finals_data['total_respondents']) && $finals_data['total_respondents'] > 0){
        $combined_total_rating += $finals_data['total_average'] * $finals_data['total_respondents'];
    }
    $combined_total_rating /= $total_respondents;
} else {
    $combined_total_rating = 0;
}

// Get remarks for midterm
$midterm_remarks = get_remarks($midterm_data['total_average'] ?? 0);

// Get remarks for finals
$finals_remarks = get_remarks($finals_data['total_average'] ?? 0);

// Get remarks for average
$average_remarks = get_remarks($combined_total_rating);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Summary</title>
    <!-- Include Bootstrap CSS and JS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include custom CSS for print -->
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
        table {
            width: 100%;
        }
        .table-borderless td, .table-borderless th {
            border: none;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Evaluation Summary</h2>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php echo (!isset($_GET['s']) || $_GET['s'] == 'midterm') ? 'active' : ''; ?>" href="index.php?page=evaluation_summary&s=midterm">Midterm</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['s']) && $_GET['s'] == 'finals') ? 'active' : ''; ?>" href="index.php?page=evaluation_summary&s=finals">Finals</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['s']) && $_GET['s'] == 'summary') ? 'active' : ''; ?>" href="index.php?page=evaluation_summary&s=summary">Summary</a>
        </li>
    </ul>
    <div class="tab-content">
        <!-- Midterm Tab -->
        <div id="midterm" class="tab-pane fade <?php echo (!isset($_GET['s']) || $_GET['s'] == 'midterm') ? 'show active' : ''; ?>">
            <h3>Midterm Evaluation Summary</h3>
            <?php if (empty($midterm_data)): ?>
                <p>No evaluation data available for Midterm.</p>
            <?php else: ?>
                <?php
                $criteria = $midterm_data['criteria'];
                $criteria_questions = $midterm_data['criteria_questions'];
                $question_avg = $midterm_data['question_avg'];
                $criteria_avg = $midterm_data['criteria_avg'];
                $total_average = $midterm_data['total_average'];
                $criteria_counter = 1;
                ?>
                <?php foreach ($criteria_questions as $cid => $questions): ?>
                    <h4><?php echo int_to_roman($criteria_counter) . '. ' . $criteria[$cid]; ?></h4>
                    <table class="table table-bordered">
                        <thead>
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
                                <td><?php echo $question; ?></td>
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
                <h3>Total Average: <?php echo number_format($total_average, 2); ?></h3>
            <?php endif; ?>
        </div>
        <!-- Finals Tab -->
        <div id="finals" class="tab-pane fade <?php echo (isset($_GET['s']) && $_GET['s'] == 'finals') ? 'show active' : ''; ?>">
            <h3>Finals Evaluation Summary</h3>
            <?php if (empty($finals_data)): ?>
                <p>No evaluation data available for Finals.</p>
            <?php else: ?>
                <?php
                $criteria = $finals_data['criteria'];
                $criteria_questions = $finals_data['criteria_questions'];
                $question_avg = $finals_data['question_avg'];
                $criteria_avg = $finals_data['criteria_avg'];
                $total_average = $finals_data['total_average'];
                $criteria_counter = 1;
                ?>
                <?php foreach ($criteria_questions as $cid => $questions): ?>
                    <h4><?php echo int_to_roman($criteria_counter) . '. ' . $criteria[$cid]; ?></h4>
                    <table class="table table-bordered">
                        <thead>
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
                                <td><?php echo $question; ?></td>
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
                <h3>Total Average: <?php echo number_format($total_average, 2); ?></h3>
            <?php endif; ?>
        </div>
        <!-- Summary Tab -->
        <div id="summary" class="tab-pane fade <?php echo (isset($_GET['s']) && $_GET['s'] == 'summary') ? 'show active' : ''; ?>">
            <h3>Summary</h3>
            <button class="btn btn-primary no-print mt-3" onclick="window.print()">Print PDF</button>
            <div class="mt-3">
                <!-- First Section -->
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo $faculty_name; ?></td>
                        <td><strong>Semester:</strong></td>
                        <td><?php echo $semester; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Department:</strong></td>
                        <td><?php echo $department; ?></td>
                        <td><strong>Academic Year:</strong></td>
                        <td><?php echo $academic_year; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td></td>
                        <td><strong>Date:</strong></td>
                        <td><?php echo $date_today; ?></td>
                    </tr>
                </table>
                <br>
                <!-- Second Section -->
                <h3 class="text-center">SUMMARY EVALUATION</h3>
                <table class="table table-bordered">
                    <thead>
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
                            <td><?php echo $midterm_data['total_respondents'] ?? 0; ?></td>
                            <td><?php echo isset($midterm_data['total_average']) && $midterm_data['total_respondents'] > 0 ? number_format($midterm_data['total_average'], 2) : 'N/A'; ?></td>
                            <td><?php echo $midterm_remarks; ?></td>
                        </tr>
                        <tr>
                            <td>Finals</td>
                            <td><?php echo $finals_data['total_respondents'] ?? 0; ?></td>
                            <td><?php echo isset($finals_data['total_average']) && $finals_data['total_respondents'] > 0 ? number_format($finals_data['total_average'], 2) : 'N/A'; ?></td>
                            <td><?php echo $finals_remarks; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Average</strong></td>
                            <td></td>
                            <td><strong><?php echo $total_respondents > 0 ? number_format($combined_total_rating, 2) : 'N/A'; ?></strong></td>
                            <td><strong><?php echo $average_remarks; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            
            </div>
        </div>
    </div>
</div>
</body>
</html>
