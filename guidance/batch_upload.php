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
/* Add your previous CSS styles here */
.form {
  background-color: #fff;
  box-shadow: 0 10px 60px rgb(218, 229, 255);
  border: 1px solid rgb(159, 159, 160);
  border-radius: 20px;
  padding: 2rem 0.7rem 0.7rem 0.7rem;
  text-align: center;
  font-size: 1.125rem;
  max-width: 320px;
  margin: auto; /* Center the form */
}

.form-title {
  color: #000000;
  font-size: 1.8rem;
  font-weight: 500;
}

.form-paragraph {
  margin-top: 10px;
  font-size: 0.9375rem;
  color: rgb(105, 105, 105);
}

.drop-container {
  background-color: #fff;
  position: relative;
  display: flex;
  gap: 10px;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 10px;
  margin-top: 2.1875rem;
  border-radius: 10px;
  border: 2px dashed rgb(171, 202, 255);
  color: #444;
  cursor: pointer;
  transition: background .2s ease-in-out, border .2s ease-in-out;
}

.drop-container:hover {
  background: rgba(0, 140, 255, 0.164);
  border-color: rgba(17, 17, 17, 0.616);
}

.drop-title {
  color: #444;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  transition: color .2s ease-in-out;
}

#csvFiles {
  width: 350px;
  max-width: 100%;
  color: #444;
  padding: 2px;
  background: #fff;
  border-radius: 10px;
  border: 1px solid rgba(8, 8, 8, 0.288);
}

#csvFiles::file-selector-button {
  margin-right: 20px;
  border: none;
  background: #084cdf;
  padding: 10px 20px;
  border-radius: 10px;
  color: #fff;
  cursor: pointer;
  transition: background .2s ease-in-out;
}

#csvFiles::file-selector-button:hover {
  background: #0d45a5;
}

.submit-btn {
  background: #084cdf;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
  transition: background .2s ease-in-out;
  margin-top: 1rem;
}

.submit-btn:hover {
  background: #0d45a5;
}
</style>
</head>
<body>
<div class="container">
    <h2>CSV Upload System</h2>

    <form method="POST" action="" enctype="multipart/form-data" class="form">
        <div class="form-title">Upload CSV Files</div>
        <div class="form-paragraph">Please select one or more CSV files to upload.</div>
        <div class="drop-container">
            <label for="csvFiles" class="drop-title">Choose CSV files:</label>
            <input type="file" id="csvFiles" name="csv_file[]" accept=".csv" required multiple>
        </div>
        <input type="submit" value="Upload CSV" class="submit-btn">
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
