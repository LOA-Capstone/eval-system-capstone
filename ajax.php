<?php
ob_start();
date_default_timezone_set("Asia/Manila");


// Include database connection and any required files
include 'db_connect.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['action']) && $_GET['action'] == 'test_sentiment') {
	if (isset($_POST['comment']) && !empty(trim($_POST['comment']))) {
		$comment = $_POST['comment'];
		// Escape the comment to prevent command injection
		$comment_escaped = escapeshellarg($comment);

		// Paths to Python executable and script
        $pythonExecutable = 'C:/Users/Ivhan/AppData/Local/Programs/Python/Python312/python.exe';
        $scriptPath = 'C:/xampp/htdocs/eval/sentiment_analysis.py'; 



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
if ($action == 'login') {
	$login = $crud->login();
	if ($login)
		echo $login;
}
if ($action == 'login2') {
	$login = $crud->login2();
	if ($login)
		echo $login;
}
if ($action == 'logout') {
	$logout = $crud->logout();
	if ($logout)
		echo $logout;
}
// if($action == 'logout2'){
// 	$logout = $crud->logout2();
// 	if($logout)
// 		echo $logout;
// }

if ($action == 'signup') {
	$save = $crud->signup();
	if ($save)
		echo $save;
}
if ($action == 'save_user') {
	$save = $crud->save_user();
	if ($save)
		echo $save;
}
if ($action == 'update_user') {
	$save = $crud->update_user();
	if ($save)
		echo $save;
}
if($action == 'update_user_password'){
    $save = $crud->update_user_password();
    if($save)
        echo $save;
}
if ($action == 'delete_user') {
	$save = $crud->delete_user();
	if ($save)
		echo $save;
}
if ($action == 'save_subject') {
	$save = $crud->save_subject();
	if ($save)
		echo $save;
}
if ($action == 'delete_subject') {
	$save = $crud->delete_subject();
	if ($save)
		echo $save;
}
if ($action == 'save_class') {
	$save = $crud->save_class();
	if ($save)
		echo $save;
}
if ($action == 'delete_class') {
	$save = $crud->delete_class();
	if ($save)
		echo $save;
}
if ($action == 'save_academic') {
	$save = $crud->save_academic();
	if ($save)
		echo $save;
}
if ($action == 'delete_academic') {
	$save = $crud->delete_academic();
	if ($save)
		echo $save;
}
if ($action == 'make_default') {
	$save = $crud->make_default();
	if ($save)
		echo $save;
}
if ($action == 'save_criteria') {
	$save = $crud->save_criteria();
	if ($save)
		echo $save;
}
if ($action == 'delete_criteria') {
	$save = $crud->delete_criteria();
	if ($save)
		echo $save;
}
if ($action == 'save_question') {
	$save = $crud->save_question();
	if ($save)
		echo $save;
}
if ($action == 'delete_question') {
	$save = $crud->delete_question();
	if ($save)
		echo $save;
}

// if($action == 'save_criteria_question'){
// 	$save = $crud->save_criteria_question();
// 	if($save)
// 		echo $save;
// }
if ($action == 'save_criteria_order') {
	$save = $crud->save_criteria_order();
	if ($save)
		echo $save;
}

if ($action == 'save_question_order') {
	$save = $crud->save_question_order();
	if ($save)
		echo $save;
}
if ($action == 'save_faculty') {

	$status = $_POST['status'];
    if(empty($id)){
        // Insert new faculty
        $qry = $conn->prepare("INSERT INTO faculty_list (school_id, firstname, lastname, email, password, department_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $qry->bind_param("sssssis", $school_id, $firstname, $lastname, $email, $password, $department_id, $status);
    } else {
        // Update existing faculty
        $qry = $conn->prepare("UPDATE faculty_list SET school_id = ?, firstname = ?, lastname = ?, email = ?, password = ?, department_id = ?, status = ? WHERE id = ?");
        $qry->bind_param("sssssis", $school_id, $firstname, $lastname, $email, $password, $department_id, $status, $id);
    }
	
	$save = $crud->save_faculty();
	if ($save)
		echo $save;
}
if ($action == 'delete_faculty') {
	$save = $crud->delete_faculty();
	if ($save)
		echo $save;
}
if ($action == 'save_student') {
	$save = $crud->save_student();
	if ($save)
		echo $save;
}
if ($action == 'save_irregular_student') {
    $save = $crud->save_irregular_student();
    if ($save)
        echo $save;
}

if ($action == 'get_class_subjects') {
    $class_id = (int)$_POST['class_id'];
    $academic_id = (int)$_SESSION['academic']['id'];

    $qry = $conn->query("
        SELECT r.id as restriction_id, r.academic_id, r.faculty_id, s.id AS subject_id, s.code, s.subject
        FROM restriction_list r
        INNER JOIN subject_list s ON s.id = r.subject_id
        WHERE r.class_id = $class_id AND r.academic_id = $academic_id
    ");

    $data = array();
    while ($row = $qry->fetch_assoc()) {
        $data[] = array(
            'academic_id' => $row['academic_id'],
            'faculty_id' => $row['faculty_id'],
            'subject_id' => $row['subject_id'],
            'restriction_id' => $row['restriction_id'],
            'subject_name' => $row['subject'] . " (" . $row['code'] . ")"
        );
    }

    echo json_encode(array('status' => 1, 'data' => $data));
    exit;
}



if ($action == 'delete_student') {
	$save = $crud->delete_student();
	if ($save)
		echo $save;
}
if ($action == 'save_restriction') {
	$save = $crud->save_restriction();
	if ($save)
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
			$pythonExecutable = 'C:/Users/Ivhan/AppData/Local/Programs/Python/Python312/python.exe'; //To be changed
			$scriptPath = 'C:/xampp/htdocs/eval/sentiment_analysis.py'; // To be changed
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


if ($action == 'get_class') {
	$get = $crud->get_class();
	if ($get)
		echo $get;
}

// ajax.php
if ($_GET['action'] == 'get_report') {
    $faculty_id = $_POST['faculty_id'];
    $subject_id = $_POST['subject_id'];
    $class_id = $_POST['class_id'];
    $academic_id = $_SESSION['academic']['id'];

    // Get total number of students evaluated
	$qry = $conn->query("SELECT COUNT(DISTINCT student_id) as tse 
	FROM evaluation_list 
	WHERE faculty_id = $faculty_id 
	  AND subject_id = $subject_id 
	  AND academic_id = $academic_id
	  AND (class_id = $class_id OR class_id = 0)");
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
		$answers = $conn->query("SELECT rate, COUNT(rate) as count 
		FROM evaluation_answers ea 
		INNER JOIN evaluation_list el ON ea.evaluation_id = el.evaluation_id 
		WHERE ea.question_id = $qid 
		  AND el.faculty_id = $faculty_id 
		  AND el.subject_id = $subject_id 
		  AND el.academic_id = $academic_id 
		  AND (el.class_id = $class_id OR el.class_id = 0)
		GROUP BY rate");

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

    // Define all sentiment categories used
    $sentiment_categories = [
        'Very Strong (Negative)',
        'Strong (Negative)',
        'Moderate (Negative)',
        'Neutral',
        'Moderate (Positive)',
        'Strong (Positive)',
        'Very Strong (Positive)'
    ];

    $sentiment_counts = array_fill_keys($sentiment_categories, 0);

    // Get comments and their sentiments
	$comment_query = $conn->query("SELECT ec.comment, ec.sentiment, ec.polarity, ec.subjectivity 
	FROM evaluation_comments ec 
	INNER JOIN evaluation_list el ON ec.evaluation_id = el.evaluation_id 
	WHERE el.faculty_id = $faculty_id 
	  AND el.subject_id = $subject_id 
	  AND el.academic_id = $academic_id
	  AND (el.class_id = $class_id OR el.class_id = 0)");


    $total_polarity = 0;
    $total_subjectivity = 0;
    $total_comments = 0;
    $comments = [];

    while ($row = $comment_query->fetch_assoc()) {
        $polarity = $row['polarity'];
        $subjectivity = $row['subjectivity'];
        $sentiment_label = $row['sentiment']; // Use the stored sentiment label directly

        $total_polarity += $polarity;
        $total_subjectivity += $subjectivity;
        $total_comments++;

        // Increment count for the sentiment label if it exists
        if (isset($sentiment_counts[$sentiment_label])) {
            $sentiment_counts[$sentiment_label]++;
        }

        // Collect comments
        $comments[] = [
            'comment' => $row['comment'],
            'sentiment' => $sentiment_label,
            'polarity' => $polarity,
            'subjectivity' => $subjectivity
        ];
    }

    $avg_polarity = $total_comments > 0 ? $total_polarity / $total_comments : 0;
    $avg_subjectivity = $total_comments > 0 ? $total_subjectivity / $total_comments : 0;

    // Prepare response data
    $response = array(
        'tse' => $tse,
        'data' => $data,
        'avg_polarity' => $avg_polarity,
        'avg_subjectivity' => $avg_subjectivity,
        'sentiment_counts' => $sentiment_counts,
        'comments' => $comments
    );

    echo json_encode($response);
    exit;
}





ob_end_flush();

if ($_GET['action'] == 'upload_batch') {
	if (isset($_FILES['file']['name'])) {
		$file_name = $_FILES['file']['name'];
		$uploaded_by = $_SESSION['login_name'];  // Assuming you store the user's name in session
		$target_dir = "uploads/";
		$target_file = $target_dir . basename($file_name);

		if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
			$conn->query("INSERT INTO batch_uploads (file_name, uploaded_by) VALUES ('$file_name', '$uploaded_by')");
			echo 1;
		} else {
			echo 0;
		}
	}
	exit;
}


if ($action == 'save_department') {
    $save = $crud->save_department();
    if ($save)
        echo $save;
}
if ($action == 'delete_department') {
    $delete = $crud->delete_department();
    if ($delete)
        echo $delete;
}
