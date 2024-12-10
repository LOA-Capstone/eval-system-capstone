<?php 
include('db_connect.php');

// Function to add ordinal suffix to numbers
function ordinal_suffix($num) {
    $num = $num % 100; // Protect against large numbers
    if ($num < 11 || $num > 13) {
        switch ($num % 10) {
            case 1: return $num . 'st';
            case 2: return $num . 'nd';
            case 3: return $num . 'rd';
        }
    }
    return $num . 'th';
}

// Array for evaluation status
$astat = array("Not Yet Started", "Started", "Closed");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Evaluation System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
                                    <p class="text-muted mb-0">See your evaluation results!</p>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No academic data available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (isset($_SESSION['academic'])): ?>
                            <div class="mt-4 callout-info">
                                <h5><strong>Academic Year:</strong> <?php echo htmlspecialchars($_SESSION['academic']['year']) . ' ' . ordinal_suffix($_SESSION['academic']['semester']); ?> Semester</h5>
                                <h6><strong>Evaluation Status:</strong> <?php echo htmlspecialchars($astat[$_SESSION['academic']['status']]); ?></h6>
                                <h6><strong>Term:</strong> <?php echo htmlspecialchars($_SESSION['academic']['term']); ?></h6>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Evaluation Cards Section -->
                
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->

</body>
</html>
