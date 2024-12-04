<?php
// Include database connection
include('db_connect.php');  // Adjust the path as needed

// Check if form is being submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null; // Capture the ID to check if editing
    $school_id = $_POST['school_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpass = $_POST['cpass'];
    $class_id = $_POST['class_id'];
    $avatar = $_FILES['img']['name'];

    // Initialize error message
    $error_msg = '';
    $schoolIdDuplicate = false;
    $emailDuplicate = false;
    $success_msg = '';

    // Validation only applies for adding a new record or updating passwords
    if (!$id || (!empty($password) && !empty($cpass))) {
        // Check if School ID already exists in the database
        $checkSchoolIdQuery = "SELECT * FROM student_list WHERE school_id = ?" . ($id ? " AND id != ?" : "");
        $stmt = $conn->prepare($checkSchoolIdQuery);
        if ($id) {
            $stmt->bind_param('si', $school_id, $id);
        } else {
            $stmt->bind_param('s', $school_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        // If there's a record with the same School ID
        if ($result->num_rows > 0) {
            $schoolIdDuplicate = true;
            $error_msg = "School ID is already taken.";
        }

        // Check if Email already exists in the database
        $checkEmailQuery = "SELECT * FROM student_list WHERE email = ?" . ($id ? " AND id != ?" : "");
        $emailStmt = $conn->prepare($checkEmailQuery);
        if ($id) {
            $emailStmt->bind_param('si', $email, $id);
        } else {
            $emailStmt->bind_param('s', $email);
        }
        $emailStmt->execute();
        $emailResult = $emailStmt->get_result();

        if ($emailResult->num_rows > 0) {
            $emailDuplicate = true;
            $error_msg = "Email is already registered.";
        }

        // Validate password match (for new records or if password fields are filled)
        if ($password !== $cpass) {
            $error_msg = "Password and Confirm Password do not match.";
        } elseif (!$id && empty($password)) {
            $error_msg = "Password is required for new records.";
        }
    }

    if ($error_msg === '') {
        if ($id) {
            // Update existing record
            $updateQuery = "UPDATE student_list SET school_id = ?, firstname = ?, lastname = ?, email = ?, class_id = ?";
            $params = [$school_id, $firstname, $lastname, $email, $class_id];
            $types = 'ssssi';

            if (!empty($password)) {
                $password = password_hash($password, PASSWORD_DEFAULT);
                $updateQuery .= ", password = ?";
                $params[] = $password;
                $types .= 's';
            }

            if (!empty($avatar)) {
                $updateQuery .= ", avatar = ?";
                $params[] = $avatar;
                $types .= 's';
                move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $avatar);
            }

            $updateQuery .= " WHERE id = ?";
            $params[] = $id;
            $types .= 'i';

            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $success_msg = "Data successfully updated.";
            } else {
                $error_msg = "Error updating data: " . $stmt->error;
            }
        } else {
            // Insert new record
            $password = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO student_list (school_id, firstname, lastname, email, password, class_id, avatar) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param('sssssis', $school_id, $firstname, $lastname, $email, $password, $class_id, $avatar);

            if ($stmt->execute()) {
                $success_msg = "Data successfully saved.";
                move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $avatar);
            } else {
                $error_msg = "Error saving data: " . $stmt->error;
            }
        }

        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
                Swal.fire({
                    title: 'Success!',
                    text: '{$success_msg}',
                    icon: 'success',
                    confirmButtonText: 'Go to Student List'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php?page=student_list';
                    }
                });
              </script>";
        exit;
    }
}
?>



<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <!-- Display error message if there are duplicates -->
            <?php if (isset($error_msg) && $error_msg != '') { echo "<div class='alert alert-danger'>$error_msg</div>"; } ?>

            <!-- Display success message -->
            <?php if (isset($success_msg) && $success_msg != '') { echo "<div class='alert alert-success'>$success_msg</div>"; } ?>

            <!-- Form to manage student details -->
            <form action="" id="manage_student" enctype="multipart/form-data" method="POST">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <div class="form-group">
                            <label for="" class="control-label">School ID</label>
                            <input type="text" name="school_id" class="form-control form-control-sm <?php echo $schoolIdDuplicate ? 'is-invalid' : '' ?>" required value="<?php echo isset($school_id) ? $school_id : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">First Name</label>
                            <input type="text" name="firstname" class="form-control form-control-sm" required value="<?php echo isset($firstname) ? $firstname : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Last Name</label>
                            <input type="text" name="lastname" class="form-control form-control-sm" required value="<?php echo isset($lastname) ? $lastname : '' ?>">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Class</label>
                            <select name="class_id" id="class_id" class="form-control form-control-sm select2">
                                <option value=""></option>
                                <?php 
                                $classes = $conn->query("SELECT id, concat(curriculum,' ',level,' - ',section) as class FROM class_list");
                                while($row=$classes->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $row['id'] ?>" <?php echo isset($class_id) && $class_id == $row['id'] ? "selected" : "" ?>><?php echo $row['class'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Avatar</label>
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
                              <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center">
                            <img src="<?php echo isset($avatar) ? 'assets/uploads/'.$avatar :'' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control form-control-sm <?php echo $emailDuplicate ? 'is-invalid' : '' ?>" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
                        </div>
                        <div class="form-group position-relative">
                            <label class="control-label">Password</label>
                            <input type="password" class="form-control form-control-sm" name="password" <?php echo !isset($id) ? "required":'' ?>>
                            <i class="fa-solid fa-eye toggle-password" style="position: absolute; top: 38px; right: 10px; cursor: pointer;"></i>
                            <small><i><?php echo isset($id) ? "Leave this blank if you don't want to change your password" : '' ?></i></small>
                        </div>
                        <div class="form-group position-relative">
                            <label class="label control-label">Confirm Password</label>
                            <input type="password" class="form-control form-control-sm" name="cpass" <?php echo !isset($id) ? 'required' : '' ?>>
                            <i class="fa-solid fa-eye toggle-password-confirm" style="position: absolute; top: 38px; right: 10px; cursor: pointer;"></i>
                            <small id="pass_match" data-status=''></small>
                        </div>
                        
                        <!-- Autofill Password Button -->
                        <div class="form-group">
                            <button type="button" id="autofill_password" class="btn btn-secondary btn-sm">Use Default Password</button>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-lg-12 text-right justify-content-center d-flex">
                    <button class="btn btn-primary mr-2">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=student_list'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('[name="password"],[name="cpass"]').keyup(function(){
            var pass = $('[name="password"]').val()
            var cpass = $('[name="cpass"]').val()
            if(cpass == '' || pass == ''){
                $('#pass_match').attr('data-status','')
            } else {
                if(cpass == pass){
                    $('#pass_match').attr('data-status','1').html('<i class="text-success">Password Matched.</i>')
                } else {
                    $('#pass_match').attr('data-status','2').html('<i class="text-danger">Password does not match.</i>')
                }
            }
        });

        function displayImg(input,_this) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#cimg').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#autofill_password').click(function(){
            var lastname = $('[name="lastname"]').val().trim();
            var firstname = $('[name="firstname"]').val().trim();
            var school_id = $('[name="school_id"]').val().trim();

            if(lastname === '' || firstname === '' || school_id === ''){
                alert("Please ensure Last Name, First Name, and School ID are filled before autofilling the password.");
                return;
            }

            var generatedPassword = lastname + firstname + school_id;
            $('[name="password"]').val(generatedPassword);
            $('[name="cpass"]').val(generatedPassword);
            $('#pass_match').attr('data-status','1').html('<i class="text-success">Password Matched.</i>');
        });
    });
    $(document).ready(function() {
    // Toggle visibility for Password
    $('.toggle-password').click(function() {
        let passwordField = $('[name="password"]');
        let type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });

    // Toggle visibility for Confirm Password
    $('.toggle-password-confirm').click(function() {
        let confirmPasswordField = $('[name="cpass"]');
        let type = confirmPasswordField.attr('type') === 'password' ? 'text' : 'password';
        confirmPasswordField.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });
});
</script>
