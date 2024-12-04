<?php 
include('db_connect.php');

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

// Array for evaluation status
$astat = array("Not Yet Started","Started","Closed");

// Fetch evaluation data similar to the Evaluate page
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags for Responsiveness and SEO -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Evaluation System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS for Modern Styling -->
    <style>
        /* Root Variables for Theme Colors */
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --success-color: #36b9cc;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
            --font-family: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--light-color);
        }

        /* Card Styling */
        .card-eval {
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background-color: #ffffff;
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-eval:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-eval .card-body {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .status-badge {
            font-size: 0.85rem;
            padding: 0.5em 0.75em;
            border-radius: 20px;
            text-transform: capitalize;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .badge-pending {
            background-color: var(--dark-color);
            color: #ffffff;
        }

        .badge-completed {
            background-color: var(--secondary-color);
        }

        .btn-evaluate {
            width: 100%;
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 25px;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-evaluate:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
        }

        /* Responsive Grid */
        @media (max-width: 576px) {
            .card-eval {
                border-radius: 10px;
            }
        }

        /* Academic Info Card */
        .callout-info {
            background-color: #ffffff;
            border-left: 5px solid var(--primary-color);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .callout-info h5, .callout-info h6 {
            margin-bottom: 0.5rem;
        }

        /* Welcome Message */
        .welcome-message {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .welcome-icon {
            font-size: 3rem;
            color: var(--primary-color);
        }

        /* Section Titles */
        .section-title {
            position: relative;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .section-title::after {
            content: '';
            position: absolute;
            width: 50px;
            height: 4px;
            background-color: var(--primary-color);
            bottom: 0;
            left: 0;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Welcome Section -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="welcome-message">
                            <i class="bi bi-person-circle welcome-icon"></i>
                            <div>
                                <h2 class="mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['login_name']); ?>!</h2>
                                <?php if (isset($_SESSION['academic'])): ?>
                                    <p class="text-muted mb-0">Let's make your academic experience better.</p>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No academic data available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['academic'])): ?>
                            <div class="mt-4 callout-info">
                                <h5><strong>Academic Year:</strong> <?php echo htmlspecialchars($_SESSION['academic']['year']).' '.ordinal_suffix($_SESSION['academic']['semester']); ?> Semester</h5>
                                <h6><strong>Evaluation Status:</strong> <?php echo htmlspecialchars($astat[$_SESSION['academic']['status']]); ?></h6>
                                <h6><strong>Term:</strong> <?php echo htmlspecialchars($_SESSION['academic']['term']); ?></h6>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Evaluation Cards Section -->
                <div class="mb-5">
                    <h3 class="section-title">Your Teacher Evaluations</h3>
                    <div class="row g-4">
                        <?php 
                        if($restriction->num_rows > 0):
                            while($row = $restriction->fetch_assoc()):
                                // Determine if this faculty has been evaluated
                                $isEvaluated = $row['is_evaluated'] == 1;

                                // Prepare URLs and classes based on evaluation status
                                if(!$isEvaluated){
                                    $cardLink = "index.php?page=evaluate&rid=".urlencode($row['id'])."&sid=".urlencode($row['sid'])."&fid=".urlencode($row['fid'])."&teacher_name=".urlencode(ucwords($row['faculty']))."&subject=".urlencode($row['subject']);
                                    $badgeClass = 'badge-pending';
                                    $badgeText = 'Pending';
                                    $btnClass = 'btn-primary';
                                    $btnIcon = 'bi-pencil-square';
                                    $btnText = 'Evaluate';
                                } else {
                                    $cardLink = '#';
                                    $badgeClass = 'badge-completed';
                                    $badgeText = 'Completed';
                                    $btnClass = 'btn-secondary';
                                    $btnIcon = 'bi-check-circle';
                                    $btnText = 'Completed';
                                }
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-eval h-100">
                                <div class="card-body d-flex flex-column">
                                    <div>
                                        <h5 class="card-title"><?= htmlspecialchars(ucwords($row['faculty'])) ?></h5>
                                        <p class="card-text text-muted"><?= htmlspecialchars($row['subject']) ?> (<?= htmlspecialchars($row['code']) ?>)</p>
                                    </div>
                                    <div class="mt-auto">
                                        <span class="status-badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                        <?php if(!$isEvaluated): ?>
                                            <a href="<?= htmlspecialchars($cardLink) ?>" class="btn btn-evaluate <?= $btnClass ?> text-white mt-3">
                                                <i class="<?= $btnIcon ?>"></i> <?= $btnText ?>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-evaluate <?= $btnClass ?> text-white mt-3" disabled>
                                                <i class="<?= $btnIcon ?>"></i> <?= $btnText ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <div class="col-12">
                                <div class="alert alert-info text-center" role="alert">
                                    You have no pending evaluations at this time.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- End of Evaluation Cards Section -->
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies (Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Optional: Include jQuery if needed for other functionalities -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS (if any) -->
    <script>
        // Example: Tooltip Initialization (if needed)
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip()
        })
    </script>
</body>
</html>
