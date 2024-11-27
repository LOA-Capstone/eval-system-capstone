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

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_student') {
        // Retrieve the student ID
        $id = intval($_POST['id']);
        
        // Retrieve updated data
        $school_id = trim($_POST['school_id']);
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);
        $curriculum = trim($_POST['curriculum']);
        $level = trim($_POST['level']);
        $section = trim($_POST['section']);

        // Initialize response array
        $response = ['success' => false, 'message' => ''];

        // Validate inputs (you can add more validation as needed)
        if (empty($school_id) || empty($firstname) || empty($lastname) || empty($email) || empty($curriculum) || empty($level) || empty($section)) {
            $response['message'] = 'All fields are required.';
            echo json_encode($response);
            exit;
        }

        // Map curriculum, level, section to class_id
        $stmtClass = $conn->prepare("SELECT id FROM class_list WHERE curriculum = ? AND level = ? AND section = ?");
        $stmtClass->bind_param("sss", $curriculum, $level, $section);
        $stmtClass->execute();
        $stmtClass->bind_result($class_id);
        if ($stmtClass->fetch()) {
            // Found matching class_id
            // $class_id is already set
        } else {
            // No matching class, set class_id to NULL
            $class_id = NULL;
        }
        $stmtClass->close();

        // Generate password
        $password_plain = $lastname . $firstname . $school_id;
        $password_md5 = md5($password_plain);

        // Check for duplicates in student_batch
        $stmtCheckBatch = $conn->prepare("SELECT id FROM student_batch WHERE (school_id = ? OR email = ?) AND id != ? AND status != 'removed'");
        $stmtCheckBatch->bind_param("ssi", $school_id, $email, $id);
        $stmtCheckBatch->execute();
        if ($stmtCheckBatch->fetch()) {
            // Duplicate found
            $stmtCheckBatch->close();
            $response['message'] = 'A student with the same School ID or Email already exists.';
            echo json_encode($response);
            exit;
        }
        $stmtCheckBatch->close();

        // Update the student_batch record
        $stmtUpdate = $conn->prepare("UPDATE student_batch SET school_id = ?, firstname = ?, lastname = ?, email = ?, curriculum = ?, level = ?, section = ?, password = ?, class_id = ? WHERE id = ?");
        $stmtUpdate->bind_param("ssssssssii", $school_id, $firstname, $lastname, $email, $curriculum, $level, $section, $password_md5, $class_id, $id);
        if ($stmtUpdate->execute()) {
            $response['success'] = true;
            $response['message'] = 'Student updated successfully.';
        } else {
            $response['message'] = 'Failed to update student.';
        }
        $stmtUpdate->close();

        // Return the response
        echo json_encode($response);
        exit;

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
    } elseif ($action === 'clear_data') {
        // Handle clear data action
        // Clear pending students from student_batch where status is 'pending'
        $stmtClearPending = $conn->prepare("DELETE FROM student_batch WHERE status = 'pending'");
        $stmtClearPending->execute();
        $stmtClearPending->close();

        // Reset the duplicates array
        $duplicates = [];

        // Set a success message
        $message = "<div class='success'>
        <span class='success-icon'>&#10004;</span> <!-- Checkmark icon -->
        <span class='success-text'>Pending students and duplicate records have been cleared successfully.</span>
    </div>";

    }
}

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
                        $stmtCheckBatch = $conn->prepare("SELECT id, status FROM student_batch WHERE school_id = ? AND email = ?");
                        $stmtCheckBatch->bind_param("ss", $row['school_id'], $row['email']);
                        $stmtCheckBatch->execute();
                        $stmtCheckBatch->bind_result($batch_id, $batch_status);
                        if ($stmtCheckBatch->fetch()) {
                            // Duplicate found in batch
                            $stmtCheckBatch->close();
                            if ($batch_status == 'removed') {
                                // Update status back to 'pending' and update fields
                                $stmtUpdate = $conn->prepare("UPDATE student_batch SET firstname = ?, lastname = ?, curriculum = ?, level = ?, section = ?, password = ?, class_id = ?, status = 'pending' WHERE id = ?");
                                $stmtUpdate->bind_param("sssssisi", $row['firstname'], $row['lastname'], $row['curriculum'], $row['level'], $row['section'], $row['password'], $row['class_id'], $batch_id);
                                $stmtUpdate->execute();
                                $stmtUpdate->close();
                            } else {
                                // Student is already pending or added, skip
                                continue;
                            }
                        } else {
                            $stmtCheckBatch->close();
                            // Insert into student_batch with status 'pending'
                            $stmtInsert = $conn->prepare("INSERT INTO student_batch (school_id, firstname, lastname, email, curriculum, level, section, password, class_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                            $stmtInsert->bind_param("ssssssssi", $row['school_id'], $row['firstname'], $row['lastname'], $row['email'], $row['curriculum'], $row['level'], $row['section'], $row['password'], $row['class_id']);
                            $stmtInsert->execute();
                            $stmtInsert->close();
                        }
                    }
                    $stmtCheck->close();
                }
            }
            fclose($handle);
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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Batch Upload</title>
    <style>
        /* Your CSS styles here */
        /* ... (Copy your CSS styles related to batch upload here) ... */
        /* For brevity, I'm omitting the CSS code since it's the same as in your original file */
    </style>
</head>
<body>
    <div>
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

        <!-- Add the Clear Data button -->
        <button type="button" class="clear-data-btn" onclick="clearData()">Clear Data</button>

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
                        <tr id="row-<?php echo $row['id']; ?>">
                            <td><input type="checkbox" name="selected_students[]" value="<?php echo $row['id']; ?>"></td>
                            <td class="editable" data-field="school_id"><?php echo htmlspecialchars($row['school_id'] ?? ''); ?></td>
                            <td class="editable" data-field="firstname"><?php echo htmlspecialchars($row['firstname'] ?? ''); ?></td>
                            <td class="editable" data-field="lastname"><?php echo htmlspecialchars($row['lastname'] ?? ''); ?></td>
                            <td class="editable" data-field="email"><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                            <td class="editable" data-field="curriculum"><?php echo htmlspecialchars($row['curriculum'] ?? ''); ?></td>
                            <td class="editable" data-field="level"><?php echo htmlspecialchars($row['level'] ?? ''); ?></td>
                            <td class="editable" data-field="section"><?php echo htmlspecialchars($row['section'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['class_id'] ?? ''); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary edit-btn" onclick="editRow(<?php echo $row['id']; ?>)">Edit</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $row['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <button type="submit">Add Selected Students</button>
        </form>
    <?php endif; ?>
    </div>

    <script>
        // Function to toggle between view and edit modes for each table row
        function editRow(id) {
            const row = document.getElementById('row-' + id);
            const cells = row.querySelectorAll('.editable');
            const editButton = row.querySelector('.edit-btn');
            
            // Toggle between Edit and Save
            if (editButton.innerHTML === 'Edit') {
                editButton.innerHTML = 'Save';  // Change button to Save
                cells.forEach(cell => {
                    const field = cell.getAttribute('data-field');
                    const cellValue = cell.innerText;
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.value = cellValue;
                    input.setAttribute('data-field', field);
                    cell.innerHTML = '';  // Clear the cell content
                    cell.appendChild(input);  // Add input field
                });
            } else {
                // Collect updated data when Save is clicked
                editButton.innerHTML = 'Edit';  // Change button back to Edit
                const updatedData = {};
                cells.forEach(cell => {
                    const input = cell.querySelector('input');
                    if (input) {
                        const field = input.getAttribute('data-field');
                        updatedData[field] = input.value.trim();  // Collect updated value
                        cell.innerText = input.value;  // Update the cell content
                    }
                });

                // Send updated data to the server using AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', window.location.href, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                alert(response.message);
                            } else {
                                alert('Error: ' + response.message);
                                // Optionally, revert the changes in the table or keep the row in edit mode
                            }
                        } else {
                            alert('An error occurred while updating data.');
                        }
                    }
                };

                // Send data as URL encoded parameters
                const params = new URLSearchParams();
                params.append('action', 'update_student');
                params.append('id', id);
                // Flatten the updatedData object into URL parameters
                for (const key in updatedData) {
                    params.append(key, updatedData[key]);
                }

                xhr.send(params.toString());  // Send the request
                reloadPageAfterDelay(300);
            }
        }

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

        // Handle delete action for pending students
           // JavaScript functions as defined above
           function deleteStudent(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this student?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
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
            });
        }

        function clearData() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will clear all pending students and duplicate records.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = ''; // current page

                    var actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'clear_data';
                    form.appendChild(actionInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function reloadPageAfterDelay(delayInMilliseconds) {
            setTimeout(() => {
                Swal.fire({
                    title: 'Reloading...',
                    timer: delayInMilliseconds,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                }).then(() => {
                    window.location.reload();
                });
            }, delayInMilliseconds);
        }
    </script>
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

        /* Style for Clear Data button */
        button.clear-data-btn {
            padding: 10px 20px;
            background-color: #dc3545; /* Bootstrap danger color */
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 8px;
            font-size: 16px;
            margin: 10px 5px;
            transition: background-color 0.3s ease;
        }

        button.clear-data-btn:hover {
            background-color: #c82333;
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
/* Success message styling */
.success {
    display: flex;
    align-items: center;
    background-color: #d4edda; /* Light green background */
    color: #155724; /* Dark green text */
    border: 1px solid #c3e6cb; /* Border matching the background */
    padding: 15px 20px;
    border-radius: 5px;
    margin: 20px auto;
    max-width: 600px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    animation: fadeIn 0.5s ease-in-out;
}

/* Success icon styling */
.success-icon {
    font-size: 20px;
    margin-right: 15px;
    color: #28a745; /* Slightly brighter green for the icon */
}

/* Success text styling */
.success-text {
    font-size: 16px;
    line-height: 1.5;
}

/* Fade-in animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Optional: Make the success message responsive */
@media (max-width: 600px) {
    .success {
        padding: 10px 15px;
    }
    .success-text {
        font-size: 14px;
    }
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
</body>
</html>
