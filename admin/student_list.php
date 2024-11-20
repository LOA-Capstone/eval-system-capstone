<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "evaluation_db";   // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4"); // Set character set

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$duplicates = [];
$pendingRows = [];

// Handle CSV Upload and Processing
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
    $csvFile = $_FILES['csv_file']['tmp_name'];

    if (!is_readable($csvFile)) {
        $message = "<div class='error'>CSV file is not readable.</div>";
    } else {
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            $headers = fgetcsv($handle); // Read headers

            // Expected headers in CSV
            $expectedHeaders = ['school_id', 'firstname', 'lastname', 'email', 'curriculum', 'level', 'section'];

            if ($headers !== $expectedHeaders) {
                $message = "<div class='error'>Invalid CSV format. Please use the provided template.</div>";
            } else {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Build associative array
                    $row = array_combine($headers, $data);

                    // Trim values
                    foreach ($row as $key => $value) {
                        $row[$key] = trim($value);
                    }

                    // Perform validation
                    if (empty($row['school_id']) || empty($row['firstname']) || empty($row['lastname']) || empty($row['email']) || empty($row['curriculum']) || empty($row['level']) || empty($row['section'])) {
                        // Missing required fields, skip this row
                        continue;
                    }

                    if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                        // Invalid email, skip this row
                        continue;
                    }

                    // Check for duplicates in student_list
                    $stmtCheck = $conn->prepare("SELECT id FROM student_list WHERE school_id = ? OR email = ?");
                    $stmtCheck->bind_param("ss", $row['school_id'], $row['email']);
                    $stmtCheck->execute();
                    $stmtCheck->bind_result($id);
                    if ($stmtCheck->fetch()) {
                        // Duplicate found
                        $duplicates[] = $row;
                    } else {
                        // No duplicate, proceed
                        // Map curriculum, level, section to class_id
                        $stmtClass = $conn->prepare("SELECT id FROM class_list WHERE curriculum = ? AND level = ? AND section = ?");
                        $stmtClass->bind_param("sss", $row['curriculum'], $row['level'], $row['section']);
                        $stmtClass->execute();
                        $stmtClass->bind_result($class_id);
                        if ($stmtClass->fetch()) {
                            // Found matching class_id
                            $row['class_id'] = $class_id;
                        } else {
                            // No matching class, set class_id to NULL
                            $row['class_id'] = NULL;
                        }
                        $stmtClass->close();

                        // Generate password
                        $password_plain = $row['lastname'] . $row['firstname'] . $row['school_id'];
                        $password_md5 = md5($password_plain);
                        $row['password'] = $password_md5;

                        // Check for duplicates in student_batch
                        $stmtCheckBatch = $conn->prepare("SELECT id FROM student_batch WHERE school_id = ? AND email = ?");
                        $stmtCheckBatch->bind_param("ss", $row['school_id'], $row['email']);
                        $stmtCheckBatch->execute();
                        if ($stmtCheckBatch->fetch()) {
                            // Duplicate in batch, skip
                            $stmtCheckBatch->close();
                            continue;
                        }
                        $stmtCheckBatch->close();

                        // Insert into student_batch with status 'pending'
                        $stmtInsert = $conn->prepare("INSERT INTO student_batch (school_id, firstname, lastname, email, curriculum, level, section, password, class_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                        $stmtInsert->bind_param("ssssssssi", $row['school_id'], $row['firstname'], $row['lastname'], $row['email'], $row['curriculum'], $row['level'], $row['section'], $row['password'], $row['class_id']);
                        $stmtInsert->execute();
                        $stmtInsert->close();
                    }
                    $stmtCheck->close();
                }
            }
            fclose($handle);
        }
    }
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'update') {
        // Handle update
        $id = $_POST['id'];
        $school_id = $_POST['school_id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $curriculum = $_POST['curriculum'];
        $level = $_POST['level'];
        $section = $_POST['section'];

        // Generate password
        $password_plain = $lastname . $firstname . $school_id;
        $password_md5 = md5($password_plain);

        // Map curriculum, level, section to class_id
        $stmtClass = $conn->prepare("SELECT id FROM class_list WHERE curriculum = ? AND level = ? AND section = ?");
        $stmtClass->bind_param("sss", $curriculum, $level, $section);
        $stmtClass->execute();
        $stmtClass->bind_result($class_id);
        if ($stmtClass->fetch()) {
            // Found matching class_id
        } else {
            // No matching class, set class_id to NULL
            $class_id = NULL;
        }
        $stmtClass->close();

        // Update the entry in student_batch
        $stmtUpdate = $conn->prepare("UPDATE student_batch SET school_id = ?, firstname = ?, lastname = ?, email = ?, curriculum = ?, level = ?, section = ?, password = ?, class_id = ? WHERE id = ?");
        $stmtUpdate->bind_param("ssssssssii", $school_id, $firstname, $lastname, $email, $curriculum, $level, $section, $password_md5, $class_id, $id);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    } elseif ($action === 'delete') {
        // Handle delete
        $id = $_POST['id'];
        // Update status to 'removed'
        $stmtDelete = $conn->prepare("UPDATE student_batch SET status = 'removed' WHERE id = ?");
        $stmtDelete->bind_param("i", $id);
        $stmtDelete->execute();
        $stmtDelete->close();
    } elseif ($action === 'add_students') {
        // Handle adding students
        if (isset($_POST['selected_students']) && !empty($_POST['selected_students'])) {
            $selected_ids = $_POST['selected_students']; // array of IDs
            foreach ($selected_ids as $id) {
                // Get the student data from student_batch
                $stmtSelect = $conn->prepare("SELECT * FROM student_batch WHERE id = ?");
                $stmtSelect->bind_param("i", $id);
                $stmtSelect->execute();
                $result = $stmtSelect->get_result();
                if ($row = $result->fetch_assoc()) {
                    // Check again for duplicates in student_list before inserting
                    $stmtCheck = $conn->prepare("SELECT id FROM student_list WHERE school_id = ? OR email = ?");
                    $stmtCheck->bind_param("ss", $row['school_id'], $row['email']);
                    $stmtCheck->execute();
                    if ($stmtCheck->fetch()) {
                        // Duplicate found, skip insertion
                        $stmtCheck->close();
                        continue;
                    }
                    $stmtCheck->close();

                    // Prepare the INSERT statement
                    $stmtInsert = $conn->prepare("INSERT INTO student_list (school_id, firstname, lastname, email, password, class_id, avatar, date_created) VALUES (?, ?, ?, ?, ?, ?, 'no-image-available.png', NOW())");

                    // Handle possible NULL class_id
                    if (is_null($row['class_id'])) {
                        $null = NULL;
                        $stmtInsert->bind_param("sssssi", $row['school_id'], $row['firstname'], $row['lastname'], $row['email'], $row['password'], $null);
                    } else {
                        $stmtInsert->bind_param("sssssi", $row['school_id'], $row['firstname'], $row['lastname'], $row['email'], $row['password'], $row['class_id']);
                    }

                    try {
                        $stmtInsert->execute();
                        // Update status in student_batch to 'added'
                        $stmtUpdate = $conn->prepare("UPDATE student_batch SET status = 'added' WHERE id = ?");
                        $stmtUpdate->bind_param("i", $id);
                        $stmtUpdate->execute();
                        $stmtUpdate->close();
                    } catch (Exception $e) {
                        // Insertion failed, get error info
                        $message .= "<div class='error'>Error adding student " . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . ": " . $e->getMessage() . "</div>";
                    }

                    $stmtInsert->close();
                }
                $stmtSelect->close();
            }
            if (empty($message)) {
                $message = "<div class='success'>Selected students have been added successfully.</div>";
            }
        } else {
            // No students selected
            $message = "<div class='error'>No students were selected to add.</div>";
        }
    }
}

// Fetch pending students from student_batch
$pendingRows = [];
$sqlPending = "SELECT * FROM student_batch WHERE status = 'pending'";
$resultPending = $conn->query($sqlPending);
if ($resultPending->num_rows > 0) {
    while ($row = $resultPending->fetch_assoc()) {
        $pendingRows[] = $row;
    }
}

// Fetch duplicates (from processing)
$duplicates = $duplicates ?? [];

// Fetch existing students for display
$i = 1;
$class = array();
$classes = $conn->query("SELECT id,concat(curriculum,' ',level,' - ',section) as `class` FROM class_list");
while($row = $classes->fetch_assoc()){
    $class[$row['id']] = $row['class'];
}
$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM student_list ORDER BY concat(firstname,' ',lastname) ASC");
$studentList = [];
while($row = $qry->fetch_assoc()){
    $studentList[] = $row;
}

?>


<style>
  /* Button base style */
  .btn.new_academic {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: bold;
    color: white;
    background-color: #28a745; /* Green color */
    border: none;
    border-radius: 5px;
    text-decoration: none;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease, background-color 0.2s ease;
    position: relative;
    overflow: hidden;
  }

  /* Style the icon */
  .btn.new_academic i {
    margin-right: 8px;
    font-size: 16px;
    color: #fff;
    transition: transform 0.2s ease;
  }

  /* Style the text */
  .btn.new_academic .text {
    transition: opacity 0.2s ease, transform 0.2s ease;
  }

  /* Hover effect */
  .btn.new_academic:hover {
    transform: scale(1.05); /* Slight enlargement */
  }

  /* When hovered, the icon moves to the center */
  .btn.new_academic:hover i {
    transform: translateX(55px); /* Move the icon to the center */
  }

  /* Hide the text on hover */
  .btn.new_academic:hover .text {
    opacity: 0; /* Hide the text */
  }

  /* Active effect (on click) */
  .btn.new_academic:active {
    transform: scale(1); /* Reset size on click */
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
            background-color: white;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: white;
        }
        .success, .error {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            font-size: 16px;
        }
        .success {
            background-color: white;
            color: white;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .Table1{
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
       
        .Table1 th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        .Table1 th {
            background-color: #007bff;
            color: white;
        }
        .Table1 tr:hover {
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
                <a class="btn new_academic" href="./index.php?page=new_student">
                    <i class="fa fa-plus"></i>
                    <span class="text">Add New Student</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Wrap the table in a div for horizontal scrolling -->
            <div class="table-container">
                <table class="table tabe-hover table-bordered" id="list">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>School ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Class</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($studentList as $student): ?>
                        <tr>
                            <th class="text-center"><?php echo $i++; ?></th>
                            <td><b><?php echo htmlspecialchars($student['school_id'] ?? ''); ?></b></td>
                            <td><b><?php echo htmlspecialchars(ucwords($student['name'] ?? '')); ?></b></td>
                            <td><b><?php echo htmlspecialchars($student['email'] ?? ''); ?></b></td>
                            <td><b><?php echo htmlspecialchars(isset($class[$student['class_id']]) ? $class[$student['class_id']] : "N/A"); ?></b></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    Action
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item view_student" href="javascript:void(0)" data-id="<?php echo $student['id']; ?>">View</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="./index.php?page=edit_student&id=<?php echo $student['id']; ?>">Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_student" href="javascript:void(0)" data-id="<?php echo $student['id']; ?>">Delete</a>
                                </div>
                            </td>
                        </tr>   
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div>
    <h1>Batch Upload</h1>
    <!-- Link to download CSV template -->
    <a href="download_template.php">Download CSV Template</a>

    <!-- CSV upload form -->
    <form class="csv-upload-form" method="POST" enctype="multipart/form-data" id="csvUploadForm">
        <span class="form-title">UPLOAD DATA SYSTEM</span>
        <p class="form-paragraph">
            Please select a CSV file to upload
        </p>
        <label for="csv-file" class="drop-container">
            <span class="drop-title">Drop CSV file here or click to select</span>
            <input type="file" name="csv_file" accept=".csv" required id="csv-file">
        </label>
    </form>

    <?php if ($message): ?>
        <?php echo $message; ?>
    <?php endif; ?>

    <!-- Display duplicates -->
    <?php if (!empty($duplicates)): ?>
        <h3>Duplicate Records</h3>
        <div class="table-container">
            <table class="Table1">
                <tr><th>School ID</th><th>Firstname</th><th>Lastname</th><th>Email</th><th>Curriculum</th><th>Level</th><th>Section</th></tr>
                <?php foreach ($duplicates as $dup): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dup['school_id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($dup['firstname'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($dup['lastname'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($dup['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($dup['curriculum'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($dup['level'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($dup['section'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>

    <!-- Display pending students -->
    <?php if (!empty($pendingRows)): ?>
        <h3>Pending Students</h3>
        <form method="POST" id="addStudentsForm">
            <input type="hidden" name="action" value="add_students">
            <div class="table-container">
                <table class="Table1">
                    <tr>
                        <th>Select</th>
                        <th>School ID</th>
                        <th>Firstname</th>
                        <th>Lastname</th>
                        <th>Email</th>
                        <th>Curriculum</th>
                        <th>Level</th>
                        <th>Section</th>
                        <th>Class ID</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($pendingRows as $row): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_students[]" value="<?php echo $row['id']; ?>"></td>
                            <td><?php echo htmlspecialchars($row['school_id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['firstname'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['lastname'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['curriculum'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['level'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['section'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['class_id'] ?? ''); ?></td>
                            <td>
                                <!-- Provide edit and delete options -->
                                <a href="?edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <button type="submit">Add Selected Students</button>
        </form>
    <?php endif; ?>

    <!-- Edit Form -->
    <?php
    // Check if editing a student
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        // Fetch the student's data
        $stmt = $conn->prepare("SELECT * FROM student_batch WHERE id = ?");
        $stmt->bind_param("i", $edit_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($student = $result->fetch_assoc()) {
            ?>
            <h3>Edit Student</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                <label>School ID:</label>
                <input type="text" name="school_id" value="<?php echo htmlspecialchars($student['school_id'] ?? ''); ?>" required>
                <label>Firstname:</label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($student['firstname'] ?? ''); ?>" required>
                <label>Lastname:</label>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($student['lastname'] ?? ''); ?>" required>
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>" required>
                <label>Curriculum:</label>
                <input type="text" name="curriculum" value="<?php echo htmlspecialchars($student['curriculum'] ?? ''); ?>" required>
                <label>Level:</label>
                <input type="text" name="level" value="<?php echo htmlspecialchars($student['level'] ?? ''); ?>" required>
                <label>Section:</label>
                <input type="text" name="section" value="<?php echo htmlspecialchars($student['section'] ?? ''); ?>" required>
                <button type="submit" name="action" value="update">Update Student</button>
            </form>
            <?php
        } else {
            echo "<div class='error'>Student not found.</div>";
        }
        $stmt->close();
    }
    ?>
</div>

<script>
// Automatically submit the form when a file is selected
document.getElementById('csv-file').addEventListener('change', function() {
    document.getElementById('csvUploadForm').submit();
});

// Optionally, handle drag and drop
var dropContainer = document.querySelector('.drop-container');
dropContainer.addEventListener('dragover', function(e) {
    e.preventDefault();
    e.stopPropagation();
    dropContainer.classList.add('dragover');
});

dropContainer.addEventListener('dragleave', function(e) {
    e.preventDefault();
    e.stopPropagation();
    dropContainer.classList.remove('dragover');
});

dropContainer.addEventListener('drop', function(e) {
    e.preventDefault();
    e.stopPropagation();
    dropContainer.classList.remove('dragover');
    var files = e.dataTransfer.files;
    if (files.length > 0) {
        document.getElementById('csv-file').files = files;
        document.getElementById('csvUploadForm').submit();
    }
});

$(document).ready(function(){
    $('.view_student').click(function(){
        uni_modal("<i class='fa fa-id-card'></i> Student Details","<?php echo $_SESSION['login_view_folder']; ?>view_student.php?id="+$(this).attr('data-id'));
    });
    $('.delete_student').click(function(){
        _conf("Are you sure to delete this student?","delete_student",[$(this).attr('data-id')]);
    });
    $('#list').dataTable();
});
function delete_student(id){
    start_load();
    $.ajax({
        url:'ajax.php?action=delete_student',
        method:'POST',
        data:{id:id},
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

// Handle delete action for pending students
function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = ''; // current page
        var actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';
        form.appendChild(actionInput);

        var idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(idInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
