<?php 
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

// Prepare the SQL statement with placeholders
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

// Bind parameters to the placeholders
$academic_id = $_SESSION['academic']['id'];
$student_id = $_SESSION['login_id'];
$class_id = $_SESSION['login_class_id'];

$stmt->bind_param("iiii", $academic_id, $student_id, $academic_id, $class_id);

// Execute the statement
$stmt->execute();

// Get the result
$restriction = $stmt->get_result();

// If restrictions are found, optionally set default selection
if($restriction->num_rows > 0){
    if(empty($rid)){
        $row = $restriction->fetch_assoc();
        $rid = $row['id'];
        $faculty_id = $row['fid'];
        $subject_id = $row['sid'];
    }
}

$sentimentResult = null;
$error = null;

// Handle Sentiment Analysis AJAX Request
if (isset($_GET['action']) && $_GET['action'] == 'test_sentiment') {
    $comment = $_POST['comment'];
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
    <title>Evaluation Page</title>
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
                        // Set default selected restriction if not set
                        if($initialLoad && empty($rid)){
                            $rid = $row['id'];
                            $faculty_id = $row['fid'];
                            $subject_id = $row['sid'];
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
                           href="<?= $isEvaluated ? '#' : "index.php?page=evaluate&rid={$row['id']}&sid={$row['sid']}&fid={$row['fid']}&teacher_name=" . urlencode(ucwords($row['faculty'])) . "&subject=" . urlencode($row['subject']) ?>"
                           <?= $isEvaluated ? 'aria-disabled="true" tabindex="-1" data-bs-toggle="tooltip" title="You have already evaluated this faculty."' : '' ?>
                        >
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= ucwords($row['faculty']) ?></strong>
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
                            Evaluation Questionnaire for Academic: <?= $_SESSION['academic']['year'] . ' ' . ordinal_suffix($_SESSION['academic']['semester']) ?>
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
                            <input type="hidden" name="class_id" value="<?= $_SESSION['login_class_id'] ?>">
                            <input type="hidden" name="faculty_id" value="<?= $faculty_id ?>">
                            <input type="hidden" name="restriction_id" value="<?= $rid ?>">
                            <input type="hidden" name="subject_id" value="<?= $subject_id ?>">
                            <input type="hidden" name="academic_id" value="<?= $_SESSION['academic']['id'] ?>">
                            
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
                                                <input type="hidden" name="qid[]" value="<?= $qrow['id'] ?>">
                                            </td>
                                            <?php for($c=1;$c<=5;$c++): ?>
                                                <td class="text-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="rate[<?= $qrow['id'] ?>]" id="q<?= $qrow['id'] ?>_<?= $c ?>" value="<?= $c ?>">
                                                        <label class="form-check-label" for="q<?= $qrow['id'] ?>_<?= $c ?>"></label>
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

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Your existing CSS styles are already included above -->
    
    <!-- jQuery and Bootstrap JS for interactivity -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap JS for tooltips -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function(){
            const status = '<?= $_SESSION['academic']['status'] ?>';
            const folder = '<?= $_SESSION['login_view_folder'] ?>';

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

            if (<?= empty($rid) ? 1 : 0 ?> == 1) {
                uni_modal("", folder + "done.php");
                setTimeout(function() {
                    window.location.href = "./index.php";
                }, 5000);
            }

            // Initialize Bootstrap tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

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
                                        <h5 class="card-title">Sentiment Analysis Result:</h5>
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
        });
    </script>

    <!-- Include SweetAlert2 for alerts (if not already included) -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
