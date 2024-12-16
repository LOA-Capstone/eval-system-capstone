<?php 

// Database connection (ensure you have established the $conn variable)
require_once 'db_connect.php'; // Update this path as necessary

// Enable detailed error reporting (remove in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Function to add ordinal suffix to numbers
function ordinal_suffix($num){
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

// Retrieve GET parameters with default values
$rid = $_GET['rid'] ?? '';
$faculty_id = $_GET['fid'] ?? '';
$subject_id = $_GET['sid'] ?? '';
$teacherName = $_GET['teacher_name'] ?? '';
$subjectName = $_GET['subject'] ?? '';

// Retrieve session values
$academic_id = $_SESSION['academic']['id'];
$student_id = $_SESSION['login_id'];

// Determine student's classification
$classif_res = $conn->query("SELECT classification FROM student_list WHERE id = $student_id");
$classification = $classif_res->fetch_assoc()['classification'] ?? 'regular';

if ($classification == 'regular') {
    // Regular student: use class_id from session
    $class_id = $_SESSION['login_class_id'];
    $stmt = $conn->prepare("
        SELECT 
            r.id, 
            s.id AS sid, 
            f.id AS fid, 
            CONCAT(f.firstname, ' ', f.lastname) AS faculty, 
            s.code, 
            s.subject,
            CASE 
                WHEN el.evaluation_id IS NOT NULL THEN 1 
                ELSE 0 
            END AS is_evaluated
        FROM restriction_list r 
        INNER JOIN faculty_list f ON f.id = r.faculty_id 
        INNER JOIN subject_list s ON s.id = r.subject_id 
        LEFT JOIN evaluation_list el ON el.restriction_id = r.id 
            AND el.academic_id = ? 
            AND el.student_id = ?
        WHERE r.academic_id = ? 
          AND r.class_id = ?
        ORDER BY f.lastname ASC, f.firstname ASC
    ");
    $stmt->bind_param("iiii", $academic_id, $student_id, $academic_id, $class_id);

} else {
    // Irregular student: fetch subjects from irregular_student_subjects
    $stmt = $conn->prepare("
        SELECT 
            r.id, 
            s.id AS sid, 
            f.id AS fid, 
            CONCAT(f.firstname, ' ', f.lastname) AS faculty, 
            s.code, 
            s.subject,
            CASE 
                WHEN el.evaluation_id IS NOT NULL THEN 1 
                ELSE 0 
            END AS is_evaluated
        FROM irregular_student_subjects iss
        INNER JOIN restriction_list r ON r.academic_id = iss.academic_id 
            AND r.faculty_id = iss.faculty_id 
            AND r.subject_id = iss.subject_id
        INNER JOIN faculty_list f ON f.id = r.faculty_id 
        INNER JOIN subject_list s ON s.id = r.subject_id 
        LEFT JOIN evaluation_list el ON el.restriction_id = r.id 
            AND el.academic_id = ?
            AND el.student_id = ?
        WHERE iss.student_id = ?
        ORDER BY f.lastname ASC, f.firstname ASC
    ");
    $stmt->bind_param("iii", $academic_id, $student_id, $student_id);
}

// Execute the statement
$stmt->execute();

// Get the result
$restriction = $stmt->get_result();

// Initialize the $all_done variable
$all_done = 1; // Assume all done initially

// Check if there are restrictions
if($restriction->num_rows > 0){
    // Iterate through the restrictions to check evaluation status
    while($row = $restriction->fetch_assoc()){
        if($row['is_evaluated'] == 0){
            $all_done = 0; // Found at least one not evaluated
            break;
        }
    }

    // Reset the result pointer for later use in the sidebar
    $restriction->data_seek(0);

    // If no restrictions are pending, set $rid to empty
    if($all_done == 1){
        $rid = '';
        $faculty_id = '';
        $subject_id = '';
    } else {
        // If $rid was empty and there are pending evaluations, set to first pending
        if(empty($rid)){
            while($row = $restriction->fetch_assoc()){
                if($row['is_evaluated'] == 0){
                    $rid = $row['id'];
                    $faculty_id = $row['fid'];
                    $subject_id = $row['sid'];
                    $teacherName = ucwords($row['faculty']);
                    $subjectName = htmlspecialchars($row['subject']);
                    break;
                }
            }
            // Reset again for sidebar
            $restriction->data_seek(0);
        }
    }
}

$sentimentResult = null;
$error = null;

// Handle Sentiment Analysis AJAX Request
if (isset($_GET['action']) && $_GET['action'] == 'test_sentiment') {
    $comment = $_POST['comment'] ?? '';
    if (!empty(trim($comment))) {
        // Escape the comment to prevent command injection
        $escapedComment = escapeshellarg($comment);

        // Paths to Python executable and script
        $pythonExecutable = 'C:/Users/Ivhan/AppData/Local/Programs/Python/Python312/python.exe';
        $scriptPath = 'C:/xampp/htdocs/eval/sentiment_analysis.py'; // Update this path as necessary

        // Build the command
        $command = "\"$pythonExecutable\" \"$scriptPath\" $escapedComment";

        // Execute the command and capture the output
        $output = shell_exec($command);

        // Decode the JSON output
        $sentimentResult = json_decode($output, true);

        // Handle cases where the output is not valid JSON
        if ($sentimentResult === null) {
            echo json_encode(['error' => 'Error processing sentiment analysis.']);
        } else {
            echo json_encode($sentimentResult);
        }
    } else {
        echo json_encode(['error' => 'Please enter a comment to analyze.']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing head content -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Your existing CSS styles */
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --font-family: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-family);
        }

        .card-header {
            background-color: var(--primary-color);
            color: #fff;
        }

        .list-group-item {
            transition: background-color 0.3s, color 0.3s;
        }

        .list-group-item:hover {
            background-color: var(--primary-color);
            color: #fff;
        }

        .list-group-item.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .list-group-item.disabled {
            background-color: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
        }

        .list-group-item.disabled .badge {
            background-color: #6c757d;
        }

        .rating-label {
            color: #000; /* Ensuring labels 1-5 are black */
            font-weight: 600;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-primary-custom:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .badge-primary {
            background-color: var(--primary-color);
        }

        .alert-success, .alert-danger, .alert-info, .alert-warning {
            border-radius: 0.5rem;
        }

        .table thead th {
            background-color: var(--secondary-color);
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }

        /* Smooth transitions for interactive elements */
        .btn, .list-group-item, .form-check-input {
            transition: all 0.3s ease;
        }

        /* Custom scrollbar for better aesthetics */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-color);
        }

        ::-webkit-scrollbar-thumb {
            background-color: var(--secondary-color);
            border-radius: 4px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1.25rem;
            }

            .btn {
                width: 100%;
                margin-top: 10px;
            }
        }

        .rating-cell {
            cursor: pointer;
        }

        .rating-cell:hover {
            background-color: rgba(13, 110, 253, 0.1); /* Light blue background on hover */
        }

    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-lg-3 mb-4">
                <div class="list-group">
                    <?php 
                    // Reset the pointer to the first row
                    $restriction->data_seek(0);
                    $initialLoad = true;
                    while($row = $restriction->fetch_array()):
                        // Set default selected restriction if not set and not all done
                        if($initialLoad && empty($rid) && $all_done == 0){
                            $rid = $row['id'];
                            $faculty_id = $row['fid'];
                            $subject_id = $row['sid'];
                            $teacherName = ucwords($row['faculty']);
                            $subjectName = htmlspecialchars($row['subject']);
                            $initialLoad = false;
                        }

                        // Determine if this faculty has been evaluated
                        $isEvaluated = $row['is_evaluated'] == 1;

                        // Determine active state
                        $isActive = (!$isEvaluated && 
                                     (ucwords($row['faculty']) == $teacherName && $row['subject'] == $subjectName)) ? 'active' : '';

                        // Set classes based on evaluation status
                        $itemClasses = 'list-group-item list-group-item-action';
                        if($isEvaluated){
                            $itemClasses .= ' disabled text-muted';
                        } else {
                            $itemClasses .= ' ' . $isActive;
                        }
                    ?>
                        <a 
                           class="<?= $itemClasses ?>" 
                           href="<?= $isEvaluated ? '#' : "index.php?page=evaluate&rid=".urlencode($row['id'])."&sid=".urlencode($row['sid'])."&fid=".urlencode($row['fid'])."&teacher_name=" . urlencode(ucwords($row['faculty'])) . "&subject=" . urlencode($row['subject']) ?>"
                           <?= $isEvaluated ? 'aria-disabled="true" tabindex="-1" data-bs-toggle="tooltip" title="You have already evaluated this faculty."' : '' ?>
                        >
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars(ucwords($row['faculty'])) ?></strong>
                                    <br>
                                    <small class="text-muted">(<?= htmlspecialchars($row["code"]) ?>) <?= htmlspecialchars($row['subject']) ?></small>
                                </div>
                                <?php if(!$isEvaluated): ?>
                                    <span class="badge bg-primary rounded-pill">Evaluate</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill">Completed</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </aside>	

            <!-- Main Content -->
            <main class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            Evaluation Questionnaire for Academic: <?= htmlspecialchars($_SESSION['academic']['year']) . ' ' . ordinal_suffix(htmlspecialchars($_SESSION['academic']['semester'])) ?>
                        </h5>
                        <div class="card-tools">
                            <button class="btn btn-light btn-sm" form="manage-evaluation">
                                <i class="bi bi-check-circle-fill"></i> Submit Evaluation
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <fieldset class="border rounded p-3 mb-4">
                            <legend class="w-auto px-2">Rating Legend</legend>
                            <p class="mb-0">5 = Strongly Agree, 4 = Agree, 3 = Neutral, 2 = Disagree, 1 = Strongly Disagree</p>
                        </fieldset>
                        <form id="manage-evaluation" method="POST" action="">
                            <input type="hidden" name="class_id" value="<?= htmlspecialchars($_SESSION['login_class_id']) ?>">
                            <input type="hidden" name="faculty_id" value="<?= htmlspecialchars($faculty_id) ?>">
                            <input type="hidden" name="restriction_id" value="<?= htmlspecialchars($rid) ?>">
                            <input type="hidden" name="subject_id" value="<?= htmlspecialchars($subject_id) ?>">
                            <input type="hidden" name="academic_id" value="<?= htmlspecialchars($_SESSION['academic']['id']) ?>">
                            
                            <?php 
                                $criteriaStmt = $conn->prepare("SELECT * FROM criteria_list WHERE id IN (
                                    SELECT criteria_id FROM question_list WHERE academic_id = ?
                                ) ORDER BY ABS(order_by) ASC");
                                $criteriaStmt->bind_param("i", $academic_id);
                                $criteriaStmt->execute();
                                $criteria = $criteriaStmt->get_result();

                                while($crow = $criteria->fetch_assoc()):
                            ?>
                            <div class="mb-4">
                                <h6 class="text-secondary"><?= htmlspecialchars($crow['criteria']) ?></h6>
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60%;">Question</th>
                                            <?php for($c=1;$c<=5;$c++): ?>
                                                <th class="text-center"><?= $c ?></th>
                                            <?php endfor; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $questionsStmt = $conn->prepare("SELECT * FROM question_list WHERE criteria_id = ? AND academic_id = ? ORDER BY ABS(order_by) ASC");
                                            $questionsStmt->bind_param("ii", $crow['id'], $academic_id);
                                            $questionsStmt->execute();
                                            $questions = $questionsStmt->get_result();

                                            while($qrow = $questions->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($qrow['question']) ?>
                                                <input type="hidden" name="qid[]" value="<?= htmlspecialchars($qrow['id']) ?>">
                                            </td>
                                            <?php for($c=1;$c<=5;$c++): ?>
                                                <td class="text-center rating-cell">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="rate[<?= htmlspecialchars($qrow['id']) ?>]" id="q<?= htmlspecialchars($qrow['id']) ?>_<?= $c ?>" value="<?= $c ?>">
                                                        <label class="form-check-label" for="q<?= htmlspecialchars($qrow['id']) ?>_<?= $c ?>"></label>
                                                    </div>
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endwhile; ?>
                            
                            <!-- Comments Section -->
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comments:</label>
                                <textarea name="comment" id="comment" class="form-control" placeholder="Enter your comments here... (English Only)" maxlength="255" rows="4"></textarea>
                                <small id="charCount" class="form-text text-muted">255 characters remaining</small>
                            </div>
                        </form>
                        
                        <!-- Sentiment Analysis Section -->
                        <div class="mt-4">
                            <button type="button" id="test-sentiment" class="btn btn-outline-primary">
                                <i class="bi bi-chat-left-text-fill"></i> Test Sentiment
                            </button>
                            <div id="sentiment-result" class="mt-3"></div>
                            <?php if (isset($sentimentResult)): ?>
                                <div class="alert alert-success" role="alert">
                                    <h5>Sentiment Analysis Result:</h5>
                                    <p><strong>Sentiment:</strong> <?= htmlspecialchars($sentimentResult['sentiment']) ?></p>
                                    <p><strong>Polarity:</strong> <?= htmlspecialchars($sentimentResult['score']) ?></p>
                                    <p><strong>Subjectivity:</strong> <?= htmlspecialchars($sentimentResult['subjectivity']) ?></p>
                                    <p><strong>Subjectivity Label:</strong> <?= htmlspecialchars($sentimentResult['subjectivity_label']) ?></p>
                                </div>
                            <?php elseif (isset($error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies (Popper.js) -->

    <!-- jQuery for interactivity -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 for alerts -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function(){
        const status = '<?= htmlspecialchars($_SESSION['academic']['status']) ?>';
        const folder = '<?= htmlspecialchars($_SESSION['login_view_folder']) ?>';
        const allDone = <?= $all_done ?>; // 1 if all done, 0 otherwise

        if (status == 0) {
            uni_modal("", folder + "not_started.php");
            setTimeout(function() {
                window.location.href = "./index.php";
            }, 5000);
        } else if (status == 2) {
            uni_modal("", folder + "closed.php");
            setTimeout(function() {
                window.location.href = "./index.php";
            }, 5000);
        }

        if (allDone == 1) { // Check the allDone variable
            uni_modal("", folder + "done.php");
            setTimeout(function() {
                window.location.href = "./index.php";
            }, 5000);
        }

        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Make entire cell clickable to select the radio button
        $('.rating-cell').click(function(e){
            // Prevent the default behavior if clicking on the radio button or label
            if(!$(e.target).is('input, label')){
                var radioInput = $(this).find('input[type="radio"]');
                radioInput.prop('checked', true).trigger('change'); // Trigger change event if needed
            }
        });

        // Optional: Keyboard accessibility
        $('.rating-cell').keypress(function(e){
            if(e.which === 13 || e.which === 32){ // Enter or Space key
                var radioInput = $(this).find('input[type="radio"]');
                radioInput.prop('checked', true).trigger('change');
                e.preventDefault();
            }
        });

        // Form Submission with Validation
        $('#manage-evaluation').submit(function(e){
            e.preventDefault();

            // Validate all questions are answered
            var allAnswered = true;
            $('input[name^="rate["]').each(function(){
                var name = $(this).attr('name');
                if (!$('input[name="'+name+'"]:checked').length) {
                    allAnswered = false;
                    return false; // Break loop
                }
            });

            // Validate comment is filled
            var commentText = $('#comment').val().trim();
            if (commentText === '') {
                allAnswered = false;
            }

            if (!allAnswered) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Form',
                    text: 'Please answer all questions and provide your comments.',
                });
                return false;
            }

            // AJAX Submission
            start_load();
            $.ajax({
                url:'ajax.php?action=save_evaluation',
                method:'POST',
                data:$(this).serialize(),
                success:function(resp){
                    if(resp == 1){
                        Swal.fire({
                            icon: 'success',
                            title: 'Evaluation Submitted',
                            text: 'Your evaluation has been successfully submitted.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Failed',
                            text: 'There was an error submitting your evaluation. Please try again.',
                        });
                    }
                    end_load();
                }
            });
        });

        // Sentiment Analysis
        $('#test-sentiment').click(function(){
            var commentText = $('#comment').val().trim();
            if(commentText === ''){
                Swal.fire({
                    icon: 'info',
                    title: 'No Comment Entered',
                    text: 'Please enter a comment to analyze.',
                });
                return;
            }
            start_load(); // Show loading indicator
            $.ajax({
                url: 'ajax.php?action=test_sentiment',
                method: 'POST',
                data: {comment: commentText},
                dataType: 'json',
                success: function(resp){
                    if(resp && !resp.error){
                        var sentiment = resp.sentiment;
                        var score = parseFloat(resp.score).toFixed(2);
                        var subjectivity = parseFloat(resp.subjectivity).toFixed(2);
                        var subjectivity_label = resp.subjectivity_label;
                        var resultHtml = `
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Sentiment Analysis Result</h5>
                                    <p>:</p>
                                    <p><strong>Sentiment:</strong> ${sentiment}</p>
                                    <p><strong>Polarity:</strong> ${score}</p>
                                    <p><strong>Subjectivity:</strong> ${subjectivity}</p>
                                    <p><strong>Subjectivity Label:</strong> ${subjectivity_label}</p>
                                </div>
                            </div>
                        `;
                        $('#sentiment-result').html(resultHtml);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Analysis Failed',
                            text: resp.error || 'There was an error performing sentiment analysis.',
                        });
                    }
                },
                error: function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Analysis Failed',
                        text: 'Unable to perform sentiment analysis at this time.',
                    });
                },
                complete: function(){
                    end_load(); // Hide loading indicator
                }
            });
        });

        // Character Count for Comments
        const maxChars = 255;
        $('#charCount').text(`${maxChars} characters remaining`);

        $('#comment').on('input', function(){
            var charsUsed = $(this).val().length;
            var charsLeft = maxChars - charsUsed;
            $('#charCount').text(`${charsLeft} characters remaining`);
        });

        // Calculate Score (Optional Feature)
        function calculateScore() {
            var totalScore = 0;
            var maxScore = 0;

            // Count the total number of questions
            var numQuestions = $('input[name^="rate["][value="5"]').length;
            maxScore = numQuestions * 5;

            // Sum the selected ratings
            $('input[name^="rate["]:checked').each(function(){
                totalScore += parseInt($(this).val());
            });

            var percentage = (totalScore / maxScore) * 100;

            $('#total-score').text(totalScore);
            $('#max-score').text(maxScore);
            $('#percentage-score').text(percentage.toFixed(2) + '%');
        }

        // Initialize score calculation
        calculateScore();
        $('input[name^="rate["]').change(function(){
            calculateScore();
        });

        // Placeholder functions for start_load and end_load
        function start_load(){
            // Implement your loading indicator logic here
            // For example, show a spinner
            $('body').append('<div id="loading-overlay" style="position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:9999;"><div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div></div></div>');
        }

        function end_load(){
            // Remove the loading indicator
            $('#loading-overlay').remove();
        }

        // Placeholder function for uni_modal
        function uni_modal(title, url){
            // Implement your modal logic here
            // For example, using Bootstrap's modal
            // Create modal HTML if not exists
            if($('#uni_modal').length == 0){
                $('body').append(`
                    <div class="modal fade" id="uni_modal" tabindex="-1" aria-labelledby="uni_modal_label" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="uni_modal_label">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <!-- Content loaded via AJAX -->
                          </div>
                        </div>
                      </div>
                    </div>
                `);
            } else {
                $('#uni_modal .modal-title').html(title);
                $('#uni_modal .modal-body').html('');
            }

            // Load content via AJAX
            $('#uni_modal .modal-body').load(url, function(){
                $('#uni_modal').modal('show');
            });
        }
    });
    </script>
</body>
</html>
