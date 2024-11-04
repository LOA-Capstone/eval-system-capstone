<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "evaluation_db";
$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$rows = [];
$uniqueRows = [];
$duplicates = [];
$message = '';


if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    $csvFile = $_FILES['csv_file']['tmp_name'];

    if (!is_readable($csvFile)) {
        $message = "<div class='error'>CSV file is not readable.</div>";
    } else {
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            fgetcsv($handle); // Skip headers

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) === 6 && !empty(trim($data[0])) && !empty(trim($data[1])) && !empty(trim($data[2])) && filter_var($data[3], FILTER_VALIDATE_EMAIL) && !empty(trim($data[4])) && !empty(trim($data[5]))) {
                    $rows[] = $data;
                }
            }
            fclose($handle);
        }

       
        foreach ($rows as $row) {
            $stmtCheck = $conn->prepare("SELECT ID FROM batch_upload WHERE school_id = ? AND firstname = ? AND lastname = ? AND email = ? AND password = ? AND class_id = ?");
            $stmtCheck->bind_param("ssssss", $row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
            $stmtCheck->execute();
            $stmtCheck->bind_result($id);
            if ($stmtCheck->fetch()) {
                $duplicates[] = array_merge([$id], $row); 
            } else {
                $uniqueRows[] = $row;
            }
            $stmtCheck->close();
        }

       
        if (!empty($uniqueRows)) {
            foreach ($uniqueRows as $row) {
                $stmtInsert = $conn->prepare("INSERT INTO batch_upload (school_id, firstname, lastname, email, password, class_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtInsert->bind_param("ssssss", $row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
            $message = "<div class='success'>Data saved successfully.</div>";
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update') {
       
        foreach ($_POST['row'] as $id => $data) {
            $stmtUpdate = $conn->prepare("UPDATE batch_upload SET school_id = ?, firstname = ?, lastname = ?, email = ?, class_id = ? WHERE ID = ?");
            $stmtUpdate->bind_param("sssssi", $data['school_id'], $data['firstname'], $data['lastname'], $data['email'], $data['class_id'], $id);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }
        $message = "<div class='success'>Data updated successfully.</div>";
    } elseif ($_POST['action'] === 'delete') {
        $id = $_POST['index']; 
        $stmtDelete = $conn->prepare("DELETE FROM batch_upload WHERE ID = ?");
        $stmtDelete->bind_param("i", $id);
        $stmtDelete->execute();
        $stmtDelete->close();
        $message = "<div class='success'>Record deleted successfully.</div>";
    }
}


$uploadedData = [];
if (!empty($uniqueRows) || !empty($duplicates)) {
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif; 
            background-color: #e9ecef; 
            display: flex; 
            justify-content: center; 
            padding: 20px; 
        }
        .container {
            background-color: #ffffff; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
            width: 100%; 
            max-width: 900px; 
            overflow: hidden; 
        }
        h2, h3 {
            color: #333333; 
            margin-bottom: 20px; 
            text-align: center; 
        }
        form {
            margin-bottom: 20px; 
        }
        input[type="file"] {
            display: block; 
            margin: 10px auto; 
        }
        input[type="submit"] {
            background-color: #007bff; 
            color: #ffffff; 
            border: none; 
            border-radius: 5px; 
            padding: 10px 20px; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background-color 0.3s; 
        }
        input[type="submit"]:hover {
            background-color: #0056b3; 
        }
        table {
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            table-layout: fixed; 
            border-radius: 8px; 
            overflow: hidden; 
            background-color: #f8f9fa; 
        }
        th, td {
            padding: 12px; 
            text-align: left; 
            overflow-wrap: break-word; 
            border-bottom: 1px solid #dee2e6; 
        }
        th {
            background-color: #007bff; 
            color: #ffffff; 
            font-weight: 500; 
        }
        tr:hover {
            background-color: #f1f1f1; 
        }
        .success { 
            color: green; 
            margin-top: 20px; 
            text-align: center; 
        }
        .error { 
            color: red; 
            margin-top: 20px; 
            text-align: center; 
        }
        input[type="text"], input[type="email"] {
            width: 100%; 
            box-sizing: border-box; 
            padding: 10px; 
            border: 1px solid #ced4da; 
            border-radius: 5px; 
            margin-bottom: 10px; 
        }
        button {
            padding: 10px 15px; 
            background-color: #28a745; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: background-color 0.3s; 
        }
        button:hover {
            background-color: #218838; 
        }
    </style>
</head>
<body>
<div class="container">
    <h2>CSV Upload System</h2>

    <form method="POST" action="" enctype="multipart/form-data">
        <label for="csvFile">Choose CSV file:</label>
        <input type="file" id="csvFile" name="csv_file" accept=".csv" required>
        <input type="submit" value="Upload CSV">
    </form>

    <?php if ($message): ?>
        <?php echo $message; ?>
    <?php endif; ?>

    <?php if (!empty($uploadedData) || !empty($duplicates)): ?>
        <h3>Uploaded Data</h3>
        <table>
            <thead>
            <tr>
                <th>ID</th><th>School ID</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Class ID</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($uploadedData as $data): ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['ID']); ?></td>
                    <td><?php echo htmlspecialchars($data['school_id']); ?></td>
                    <td><?php echo htmlspecialchars($data['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($data['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($data['email']); ?></td>
                    <td><?php echo htmlspecialchars($data['class_id']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!empty($duplicates)): ?>
            <h3>Duplicates Found - Edit or Delete</h3>
            <form method="post">
                <input type="hidden" name="action" value="update">
                <table>
                    <thead>
                    <tr>
                        <th>School ID</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Class ID</th><th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($duplicates as $row): ?>
                        <tr>
                            <td><input type="text" name="row[<?php echo $row[0]; ?>][school_id]" value="<?php echo htmlspecialchars($row[1]); ?>"></td>
                            <td><input type="text" name="row[<?php echo $row[0]; ?>][firstname]" value="<?php echo htmlspecialchars($row[2]); ?>"></td>
                            <td><input type="text" name="row[<?php echo $row[0]; ?>][lastname]" value="<?php echo htmlspecialchars($row[3]); ?>"></td>
                            <td><input type="email" name="row[<?php echo $row[0]; ?>][email]" value="<?php echo htmlspecialchars($row[4]); ?>"></td>
                            <td><input type="text" name="row[<?php echo $row[0]; ?>][class_id]" value="<?php echo htmlspecialchars($row[6]); ?>"></td>
                            <td>
                                <button type="submit" class="btn btn-update">Save</button>
                                <button type="submit" name="action" value="delete" class="btn btn-delete">Delete</button>
                                <input type="hidden" name="index" value="<?php echo $row[0]; ?>">
                                <input type="hidden" name="row[<?php echo $row[0]; ?>][password]" value="<?php echo htmlspecialchars($row[5]); ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
