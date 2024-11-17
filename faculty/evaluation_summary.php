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

    return $data;
}

// Get evaluation summary for Midterm
$midterm_data = get_evaluation_summary($midterm_academic_id, $faculty_id);
// Similarly for Finals
$finals_data = get_evaluation_summary($finals_academic_id, $faculty_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Summary</title>
    <!-- Include Bootstrap CSS and JS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h2>Evaluation Summary</h2>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="#midterm" data-toggle="tab">Midterm</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#finals" data-toggle="tab">Finals</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#summary" data-toggle="tab">Summary</a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="midterm" class="tab-pane fade show active">
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
        <div id="finals" class="tab-pane fade">
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
        <div id="summary" class="tab-pane fade">
            <h3>Summary</h3>
            <p>To be implemented.</p>
        </div>
    </div>
</div>
</body>
</html>
