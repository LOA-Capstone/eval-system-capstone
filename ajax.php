<?php
ob_start();
date_default_timezone_set("Asia/Manila");


// Include database connection and any required files
include 'db_connect.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['action']) && $_GET['action'] == 'test_sentiment'){
    if(isset($_POST['comment']) && !empty(trim($_POST['comment']))){
        $comment = $_POST['comment'];
        // Escape the comment to prevent command injection
        $comment_escaped = escapeshellarg($comment);

        // Paths to Python executable and script
        $pythonExecutable = 'C:\Users\xyrel\AppData\Local\Programs\Python\Python39\python.exe'; //To be changed
		$scriptPath = 'c:\xampp\htdocs\eval-system-capstone\sentiment_analysis.py'; // To be changed

        // Build the command
        $command = "\"$pythonExecutable\" \"$scriptPath\" $comment_escaped";

        // Execute the command and capture the output
        $output = shell_exec($command);

        // Decode the JSON output
        $sentimentResult = json_decode($output, true);

        // Handle cases where the output is not valid JSON
        if ($sentimentResult === null) {
            // Log the error
            error_log("Error processing sentiment analysis. Command output: $output");
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error processing sentiment analysis.']);
        } else {
            header('Content-Type: application/json');
            echo json_encode($sentimentResult);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No comment provided']);
    }
    exit;
}


$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();
if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
// if($action == 'logout2'){
// 	$logout = $crud->logout2();
// 	if($logout)
// 		echo $logout;
// }

if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'update_user'){
	$save = $crud->update_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'save_subject'){
	$save = $crud->save_subject();
	if($save)
		echo $save;
}
if($action == 'delete_subject'){
	$save = $crud->delete_subject();
	if($save)
		echo $save;
}
if($action == 'save_class'){
	$save = $crud->save_class();
	if($save)
		echo $save;
}
if($action == 'delete_class'){
	$save = $crud->delete_class();
	if($save)
		echo $save;
}
if($action == 'save_academic'){
	$save = $crud->save_academic();
	if($save)
		echo $save;
}
if($action == 'delete_academic'){
	$save = $crud->delete_academic();
	if($save)
		echo $save;
}
if($action == 'make_default'){
	$save = $crud->make_default();
	if($save)
		echo $save;
}
if($action == 'save_criteria'){
	$save = $crud->save_criteria();
	if($save)
		echo $save;
}
if($action == 'delete_criteria'){
	$save = $crud->delete_criteria();
	if($save)
		echo $save;
}
if($action == 'save_question'){
	$save = $crud->save_question();
	if($save)
		echo $save;
}
if($action == 'delete_question'){
	$save = $crud->delete_question();
	if($save)
		echo $save;
}

// if($action == 'save_criteria_question'){
// 	$save = $crud->save_criteria_question();
// 	if($save)
// 		echo $save;
// }
if($action == 'save_criteria_order'){
	$save = $crud->save_criteria_order();
	if($save)
		echo $save;
}

if($action == 'save_question_order'){
	$save = $crud->save_question_order();
	if($save)
		echo $save;
}
if($action == 'save_faculty'){
	$save = $crud->save_faculty();
	if($save)
		echo $save;
}
if($action == 'delete_faculty'){
	$save = $crud->delete_faculty();
	if($save)
		echo $save;
}
if($action == 'save_student'){
	$save = $crud->save_student();
	if($save)
		echo $save;
}
if($action == 'delete_student'){
	$save = $crud->delete_student();
	if($save)
		echo $save;
}
if($action == 'save_restriction'){
	$save = $crud->save_restriction();
	if($save)
		echo $save;
}

if ($_GET['action'] == 'save_evaluation') {
	$conn->begin_transaction();
	try {
	  // Insert into evaluation_list
	  $academic_id = $_POST['academic_id'];
	  $class_id = $_POST['class_id'];
	  $student_id = $_SESSION['login_id'];
	  $subject_id = $_POST['subject_id'];
	  $faculty_id = $_POST['faculty_id'];
	  $restriction_id = $_POST['restriction_id'];
  
	  $stmt = $conn->prepare("INSERT INTO evaluation_list (academic_id, class_id, student_id, subject_id, faculty_id, restriction_id) VALUES (?, ?, ?, ?, ?, ?)");
	  $stmt->bind_param("iiiiii", $academic_id, $class_id, $student_id, $subject_id, $faculty_id, $restriction_id);
	  $stmt->execute();
  
	  $evaluation_id = $conn->insert_id;
  
	  // Insert into evaluation_answers
	  foreach ($_POST['qid'] as $qid) {
		$rate = $_POST['rate'][$qid];
		$stmt = $conn->prepare("INSERT INTO evaluation_answers (evaluation_id, question_id, rate) VALUES (?, ?, ?)");
		$stmt->bind_param("iii", $evaluation_id, $qid, $rate);
		$stmt->execute();
	  }
  
	  // Get the comment
	  $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
  
	  if (!empty(trim($comment))) {
		// Perform sentiment analysis
		$escapedComment = escapeshellarg($comment);
		$pythonExecutable = 'C:\Users\xyrel\AppData\Local\Programs\Python\Python39\python.exe'; //To be changed
		$scriptPath = 'c:\xampp\htdocs\eval-system-capstone\sentiment_analysis.py'; // To be changed
		$command = "\"$pythonExecutable\" \"$scriptPath\" $escapedComment";
		$output = shell_exec($command);
		$sentimentResult = json_decode($output, true);
  
		if ($sentimentResult === null) {
		  // Handle error
		  $sentiment = 'Unknown';
		  $polarity = 0;
		  $subjectivity = 0;
		} else {
		  $sentiment = $sentimentResult['sentiment'];
		  $polarity = $sentimentResult['score'];
		  $subjectivity = $sentimentResult['subjectivity'];
		}
  
		// Insert into evaluation_comments
		$stmt = $conn->prepare("INSERT INTO evaluation_comments (evaluation_id, comment, sentiment, polarity, subjectivity) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("issdd", $evaluation_id, $comment, $sentiment, $polarity, $subjectivity);
		$stmt->execute();
	  }
  
	  $conn->commit();
	  echo 1;
	} catch (Exception $e) {
	  $conn->rollback();
	  echo 0;
	}
	exit;
  }
  

if($action == 'get_class'){
	$get = $crud->get_class();
	if($get)
		echo $get;
}

// ajax.php

if ($_GET['action'] == 'get_report') {
    $faculty_id = $_POST['faculty_id'];
    $subject_id = $_POST['subject_id'];
    $class_id = $_POST['class_id'];
    $academic_id = $_SESSION['academic']['id'];

    // Get total number of students evaluated
    $qry = $conn->query("SELECT COUNT(DISTINCT student_id) as tse FROM evaluation_list WHERE faculty_id = $faculty_id AND subject_id = $subject_id AND class_id = $class_id AND academic_id = $academic_id");
    $tse = 0;
    if ($qry->num_rows > 0) {
        $tse = $qry->fetch_assoc()['tse'];
    }

    // Get evaluation answers
    $data = array();
    $questions = $conn->query("SELECT id FROM question_list WHERE academic_id = $academic_id");
    while ($row = $questions->fetch_assoc()) {
        $qid = $row['id'];
        $rates = array_fill(1, 5, 0);
        $total = 0;
        $answers = $conn->query("SELECT rate, COUNT(rate) as count FROM evaluation_answers ea INNER JOIN evaluation_list el ON ea.evaluation_id = el.evaluation_id WHERE ea.question_id = $qid AND el.faculty_id = $faculty_id AND el.subject_id = $subject_id AND el.class_id = $class_id AND el.academic_id = $academic_id GROUP BY rate");
        while ($arow = $answers->fetch_assoc()) {
            $rate = $arow['rate'];
            $count = $arow['count'];
            $rates[$rate] = $count;
            $total += $count;
        }
        // Calculate percentage
        foreach ($rates as $rate => $count) {
            $rates[$rate] = $total > 0 ? round(($count / $total) * 100, 2) : 0;
        }
        $data[$qid] = $rates;
    }

    // Get average polarity and subjectivity from evaluation_comments
    $comment_query = $conn->query("SELECT AVG(polarity) as avg_polarity, AVG(subjectivity) as avg_subjectivity FROM evaluation_comments ec INNER JOIN evaluation_list el ON ec.evaluation_id = el.evaluation_id WHERE el.faculty_id = $faculty_id AND el.subject_id = $subject_id AND el.class_id = $class_id AND el.academic_id = $academic_id");
    $avg_polarity = 0;
    $avg_subjectivity = 0;
    if ($comment_query->num_rows > 0) {
        $row = $comment_query->fetch_assoc();
        $avg_polarity = $row['avg_polarity'];
        $avg_subjectivity = $row['avg_subjectivity'];
    }

    // Prepare response data
    $response = array(
        'tse' => $tse,
        'data' => $data,
        'avg_polarity' => $avg_polarity,
        'avg_subjectivity' => $avg_subjectivity
    );

    echo json_encode($response);
    exit;
}


ob_end_flush();
?>
