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
        $pythonExecutable = 'C:/Users/Ivhan/AppData/Local/Programs/Python/Python312/python.exe'; // Update this path
        $scriptPath = 'C:/xampp/htdocs/eval/sentiment_analysis.py'; // Update this path

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
if($action == 'save_evaluation'){
    // First, save the evaluation
    $save = $crud->save_evaluation();
    if($save){
        // After saving, get the evaluation_id
        // Assuming $crud->save_evaluation() returns the evaluation_id
        $evaluation_id = $save;

        // Alternatively, if save_evaluation() doesn't return the evaluation_id,
        // you can use $conn->insert_id to get the last inserted ID.
        // Uncomment the following line if needed:
        // $evaluation_id = $conn->insert_id;

        // Now process the comment if provided
        if(isset($_POST['comment']) && !empty(trim($_POST['comment']))){
            $comment = $_POST['comment'];
            // Escape the comment to prevent shell injection
            $comment_escaped = escapeshellarg($comment);

            // Execute the Python script
            $command = "python3 sentiment_analysis.py $comment_escaped";
            $output = shell_exec($command);

            // Decode the JSON result
            $result = json_decode($output, true);

            // Check if the result is valid
            if($result && isset($result['sentiment'])){
                // Retrieve sentiment analysis results
                $sentiment = $conn->real_escape_string($result['sentiment']);
                $polarity = $conn->real_escape_string($result['polarity']);
                $subjectivity = $conn->real_escape_string($result['subjectivity']);
                $positive_percentage = $conn->real_escape_string($result['positive_percentage']);
                $negative_percentage = $conn->real_escape_string($result['negative_percentage']);

                // Insert the comment and sentiment results into the database
                $sql = "INSERT INTO evaluation_comments (evaluation_id, comment, sentiment, polarity, subjectivity, positive_percentage, negative_percentage) VALUES ('$evaluation_id', '$comment', '$sentiment', '$polarity', '$subjectivity', '$positive_percentage', '$negative_percentage')";
                $conn->query($sql) or die($conn->error);
            } else {
                // Handle error if sentiment analysis failed
                // Optionally, save the comment without sentiment results
                $sql = "INSERT INTO evaluation_comments (evaluation_id, comment) VALUES ('$evaluation_id', '$comment')";
                $conn->query($sql) or die($conn->error);
            }
        }
        // Finally, echo the result to indicate success
        echo $save;
    } else {
        // Handle error if saving evaluation failed
        echo 0;
    }
}


if($action == 'get_class'){
	$get = $crud->get_class();
	if($get)
		echo $get;
}
if($action == 'get_report'){
	$get = $crud->get_report();
	if($get)
		echo $get;
}
ob_end_flush();
?>
