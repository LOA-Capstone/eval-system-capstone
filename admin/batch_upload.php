<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "evaluation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$rows = [];
$uniqueRows = [];
$duplicates = [];
$message = '';

// Handle CSV Upload and Processing
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    $csvFile = $_FILES['csv_file']['tmp_name'];

    if (!is_readable($csvFile)) {
        $message = "<div class='error'>CSV file is not readable.</div>";
    } else {
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            fgetcsv($handle); // Skip headers

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Expecting 7 columns: school_id, firstname, lastname, email, password, year, course
                if (count($data) === 7 && !empty(trim($data[0])) && !empty(trim($data[1])) && !empty(trim($data[2])) && filter_var($data[3], FILTER_VALIDATE_EMAIL) && !empty(trim($data[4])) && !empty(trim($data[5])) && !empty(trim($data[6]))) {
                    $rows[] = $data;
                }
            }
            fclose($handle);
        }

        // Check for duplicates
        foreach ($rows as $row) {
            $stmtCheck = $conn->prepare("SELECT ID FROM batch_upload WHERE school_id = ? AND firstname = ? AND lastname = ? AND email = ? AND year = ? AND course = ?");
            $stmtCheck->bind_param("ssssss", $row[0], $row[1], $row[2], $row[3], $row[5], $row[6]);
            $stmtCheck->execute();
            $stmtCheck->bind_result($id);
            if ($stmtCheck->fetch()) {
                $duplicates[] = array_merge([$id], $row); // Add ID to the row for updating
            } else {
                $uniqueRows[] = $row;
            }
            $stmtCheck->close();
        }

        // Save unique rows to database (without password)
        if (!empty($uniqueRows)) {
            foreach ($uniqueRows as $row) {
                // Exclude password from insertion
                $stmtInsert = $conn->prepare("INSERT INTO batch_upload (school_id, firstname, lastname, email, year, course) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtInsert->bind_param("ssssss", $row[0], $row[1], $row[2], $row[3], $row[5], $row[6]);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
            $message = "<div class='success'>Data saved successfully.</div>";
        }
    }
}

// Handle update or delete actions for duplicates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $index = $_POST['index'] ?? null; // Ensure index is set, if not, default to null

    if ($action === 'update' && isset($_POST['row'][$index])) {
        $school_id = $_POST['row'][$index]['school_id'];
        $firstname = $_POST['row'][$index]['firstname'];
        $lastname = $_POST['row'][$index]['lastname'];
        $email = $_POST['row'][$index]['email'];
        $year = $_POST['row'][$index]['year'];
        $course = $_POST['row'][$index]['course'];

        // Update the database record
        $stmtUpdate = $conn->prepare("UPDATE batch_upload SET school_id = ?, firstname = ?, lastname = ?, email = ?, year = ?, course = ? WHERE ID = ?");
        $stmtUpdate->bind_param("ssssssi", $school_id, $firstname, $lastname, $email, $year, $course, $index);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        $message = "<div class='success'>Record updated successfully.</div>";
    } elseif ($action === 'delete' && isset($index)) {
        // Delete the record
        $stmtDelete = $conn->prepare("DELETE FROM batch_upload WHERE ID = ?");
        $stmtDelete->bind_param("i", $index);
        $stmtDelete->execute();
        $stmtDelete->close();

        $message = "<div class='success'>Record deleted successfully.</div>";
    }
}

// Fetch data from database only after upload
$uploadedData = [];
if (!empty($uniqueRows)) {
    $result = $conn->query("SELECT * FROM batch_upload ORDER BY ID DESC");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $uploadedData[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV File Upload</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            padding: 40px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 30px;
            text-align: center;
        }
        input[type="file"] {
            padding: 10px;
            margin: 10px 0;
        }
        input[type="submit"] {
            padding: 12px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .success, .error {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            font-size: 16px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 8px;
    font-size: 16px;
    margin: 10px 5px; /* Adds space between buttons */
    transition: background-color 0.3s ease; /* Smooth hover effect */
}

button:hover {
    background-color: #218838;
}

button:focus {
    outline: none;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.6);
}
    </style>
</head>
<body>
<div class="container">
    <h2>Data Upload System</h2>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <input type="submit" value="Upload CSV">
    </form>

    <?php if ($message): ?>
        <?php echo $message; ?>
    <?php endif; ?>

    <?php if (!empty($duplicates)): ?>
        <h3>Duplicate Records</h3>
        <table>
            <tr><th>School ID</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Year</th><th>Course</th><th>Actions</th></tr>
            <?php foreach ($duplicates as $dup): ?>
                <form method="POST">
                    <input type="hidden" name="index" value="<?php echo $dup[0]; ?>">
                    <tr>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][school_id]" value="<?php echo htmlspecialchars($dup[1]); ?>"></td>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][firstname]" value="<?php echo htmlspecialchars($dup[2]); ?>"></td>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][lastname]" value="<?php echo htmlspecialchars($dup[3]); ?>"></td>
                        <td><input type="email" name="row[<?php echo $dup[0]; ?>][email]" value="<?php echo htmlspecialchars($dup[4]); ?>"></td>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][year]" value="<?php echo htmlspecialchars($dup[6]); ?>"></td>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][course]" value="<?php echo htmlspecialchars($dup[7]); ?>"></td>
                        <td>
                            <button type="submit" name="action" value="update">Update</button>
                            <button type="submit" name="action" value="delete">Delete</button>
                        </td>
                    </tr>
                </form>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if (!empty($uniqueRows)): ?>
        <h3>Uploaded Data</h3>
        <table>
            <tr><th>School ID</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Year</th><th>Course</th></tr>
            <?php foreach ($uploadedData as $data): ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['school_id']); ?></td>
                    <td><?php echo htmlspecialchars($data['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($data['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($data['email']); ?></td>
                    <td><?php echo htmlspecialchars($data['year']); ?></td>
                    <td><?php echo htmlspecialchars($data['course']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
