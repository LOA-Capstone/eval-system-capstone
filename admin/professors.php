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
                // Expecting 7 columns: school_id, firstname, lastname, email, password, department_id, department
                if (count($data) === 7 && !empty(trim($data[0])) && !empty(trim($data[1])) && !empty(trim($data[2])) && filter_var($data[3], FILTER_VALIDATE_EMAIL) && !empty(trim($data[4])) && !empty(trim($data[5])) && !empty(trim($data[6]))) {
                    $rows[] = $data;
                }
            }
            fclose($handle);
        }

        // Check for duplicates
        foreach ($rows as $row) {
            $stmtCheck = $conn->prepare("SELECT ID FROM faculty_batch_upload WHERE school_id = ? AND firstname = ? AND lastname = ? AND email = ? AND department_id = ? AND department = ?");
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
                $stmtInsert = $conn->prepare("INSERT INTO faculty_batch_upload (school_id, firstname, lastname, email, department_id, department) VALUES (?, ?, ?, ?, ?, ?)");
                $stmtInsert->bind_param("ssssss", $row[0], $row[1], $row[2], $row[3], $row[5], $row[6]);
                $stmtInsert->execute();
                $stmtInsert->close();
            }
            
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
        $year = $_POST['row'][$index]['department_id'];
        $course = $_POST['row'][$index]['department'];

        // Update the database record
        $stmtUpdate = $conn->prepare("UPDATE faculty_batch_upload SET school_id = ?, firstname = ?, lastname = ?, email = ?, department_id = ?, department = ? WHERE ID = ?");
        $stmtUpdate->bind_param("ssssssi", $school_id, $firstname, $lastname, $email, $year, $course, $index);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        
    } elseif ($action === 'delete' && isset($index)) {
        // Delete the record
        $stmtDelete = $conn->prepare("DELETE FROM faculty_batch_upload WHERE ID = ?");
        $stmtDelete->bind_param("i", $index);
        $stmtDelete->execute();
        $stmtDelete->close();

        
    }
}

// Fetch data from database only after upload
$uploadedData = [];
if (!empty($uniqueRows)) {
    $result = $conn->query("SELECT * FROM faculty_batch_upload ORDER BY ID DESC");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $uploadedData[] = $row;
        }
    }
}
?>


<!-- Add this CSS either in your stylesheet or within a <style> tag -->
<style>
    .counter {
        color: black;
    }
    body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
        }
        .container {
            max-width: auto;
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
        .csv-upload-form {
  background-color: #fff;
  box-shadow: 0 10px 60px rgb(218, 229, 255);
  border: 1px solid blue;
  border-radius: 20px;
  padding: 2rem .7rem .7rem .7rem;
  text-align: center;
  font-size: 1.125rem;
  max-width: 420px;
  margin: 0 auto;
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

.drop-container:hover .drop-title {
  color: #222;
}

.drop-title {
  color: #444;
  font-size: 20px;
  font-weight: bold;
  text-align: center;
  transition: color .2s ease-in-out;
}

#csv-file {
  display: none;
}

.submit-btns {
  background: #1C204B !important;
  border: none;
  padding: 10px 20px;
  color: #fff;
  border-radius: 10px;
  cursor: pointer;
  margin-top: 1rem;
  width: 100%;
  font-size: 1rem;
  transition: background .2s ease-in-out;
}

.submit-btns:hover {
    background: #3C437A !important;
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
        .table1 {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table1 th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .table1 th {
            background-color: #007bff;
            color: white;
        }
        .table1 tr:hover {
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
 
<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_professor">
                    <i class="fa fa-plus"></i> Add New Professor
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered" id="list">
                <thead>
                    <tr>
                        <th class="text-center counter">#</th>
                        <th>School ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Status</th> <!-- New Column -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    // Modify the query to include 'status' in the select statement
                    $qry = $conn->query("SELECT f.*, CONCAT(f.firstname, ' ', f.lastname) AS name, d.name AS department_name 
                                         FROM faculty_list f 
                                         LEFT JOIN department_list d ON f.department_id = d.id 
                                         ORDER BY name ASC");
                    while($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center counter"><?php echo $i++ ?></td>
                        <td><b><?php echo htmlspecialchars($row['school_id']) ?></b></td>
                        <td><b><?php echo htmlspecialchars(ucwords($row['name'])) ?></b></td>
                        <td><b><?php echo htmlspecialchars($row['email']) ?></b></td>
                        <td><b><?php echo htmlspecialchars($row['department_name']) ?></b></td>
                        <td><b><?php echo htmlspecialchars($row['status']) ?></b></td> <!-- Display Status -->
                        <td class="text-center">
                            <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                              Action
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item view_faculty" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="./index.php?page=edit_faculty&id=<?php echo $row['id'] ?>">Edit</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item delete_faculty" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                            </div>
                        </td>
                    </tr>   
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div>
<!-- <h1>Batch Upload</h1>
<form class="csv-upload-form" method="POST" enctype="multipart/form-data">
  <span class="form-title">UPLOAD DATA SYSTEM</span>
  <p class="form-paragraph">
    Please select a CSV file to upload
  </p>
  <label for="csv-file" class="drop-container">
    <span class="drop-title">Drop CSV file here</span>
    or
    <input type="file" name="csv_file" accept=".csv" required id="csv-file">
  </label>
  <input type="submit" value="Upload CSV" class="submit-btns">
</form> -->


    <?php if ($message): ?>
        <?php echo $message; ?>
    <?php endif; ?>

    <?php if (!empty($duplicates)): ?>
        <h3>Duplicate Records</h3>
        <table class="table1">
            <tr><th>School ID</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Department ID</th><th>Department</th><th>Actions</th></tr>
            <?php foreach ($duplicates as $dup): ?>
                <form method="POST">
                    <input type="hidden" name="index" value="<?php echo $dup[0]; ?>">
                    <tr>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][school_id]" value="<?php echo htmlspecialchars($dup[1]); ?>"></td>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][firstname]" value="<?php echo htmlspecialchars($dup[2]); ?>"></td>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][lastname]" value="<?php echo htmlspecialchars($dup[3]); ?>"></td>
                        <td><input type="email" name="row[<?php echo $dup[0]; ?>][email]" value="<?php echo htmlspecialchars($dup[4]); ?>"></td>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][department_id]" value="<?php echo htmlspecialchars($dup[6]); ?>"></td>
                        <td><input type="text" name="row[<?php echo $dup[0]; ?>][department]" value="<?php echo htmlspecialchars($dup[7]); ?>"></td>
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
        <table class = "table1">
            <tr><th>School ID</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Department ID</th><th>Department</th></tr>
            <?php foreach ($uploadedData as $data): ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['school_id']); ?></td>
                    <td><?php echo htmlspecialchars($data['firstname']); ?></td>
                    <td><?php echo htmlspecialchars($data['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($data['email']); ?></td>
                    <td><?php echo htmlspecialchars($data['department_id']); ?></td>
                    <td><?php echo htmlspecialchars($data['department']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>
<!-- JavaScript Section -->
<script>
    $(document).ready(function(){
        $('.view_faculty').click(function(){
            uni_modal("<i class='fa fa-id-card'></i> Faculty Details","<?php echo $_SESSION['login_view_folder'] ?>view_faculty.php?id="+$(this).attr('data-id'))
        });
        $('.delete_faculty').click(function(){
            _conf("Are you sure to delete this faculty?","delete_faculty",[$(this).attr('data-id')]);
        });
        $('#list').DataTable(); // Ensure DataTables is properly loaded
    });

    function delete_faculty($id){
        start_load();
        $.ajax({
            url:'ajax.php?action=delete_faculty',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted",'success');
                    setTimeout(function(){
                        location.reload();
                    },1500);
                }
            }
        });
    }
</script>
