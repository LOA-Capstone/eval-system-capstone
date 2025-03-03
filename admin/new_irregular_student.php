<?php
// Include database connection
include 'db_connect.php';

// Check if an ID is provided for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch student details for this id
    $qry = $conn->query("SELECT * FROM student_list WHERE id = $id AND classification = 'irregular'");
    if ($qry->num_rows > 0) {
        $student = $qry->fetch_assoc();
        // Assign student data to variables
        $school_id = $student['school_id'];
        $firstname = $student['firstname'];
        $lastname = $student['lastname'];
        $email = $student['email'];
        $avatar = $student['avatar'];
    } else {
        echo "No student found with ID $id";
        exit;
    }

    // Fetch assigned subjects for this student
    $assigned_subjects = [];
    $asqry = $conn->query("SELECT iss.*, s.code, s.subject, c.id as class_id, CONCAT(c.curriculum,' ',c.level,' - ',c.section) AS class_name 
                           FROM irregular_student_subjects iss
                           INNER JOIN subject_list s ON s.id = iss.subject_id
                           INNER JOIN restriction_list r ON r.academic_id = iss.academic_id AND r.faculty_id = iss.faculty_id AND r.subject_id = iss.subject_id
                           INNER JOIN class_list c ON c.id = r.class_id
                           WHERE iss.student_id = $id");
    while($row = $asqry->fetch_assoc()){
        $assigned_subjects[] = $row; 
    }
}
?>

<!-- HTML Form for Editing Irregular Student -->
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form action="" id="manage_irregular_student">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <input type="hidden" name="classification" value="irregular">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <div class="form-group">
                            <label for="" class="control-label">School ID</label>
                            <input type="text" name="school_id" class="form-control form-control-sm" required value="<?php echo isset($school_id) ? $school_id : '' ?>">
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
                            <label for="" class="control-label">Avatar</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this, $(this))">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center">
                            <img src="<?php echo isset($avatar) ? 'assets/uploads/'.$avatar : 'assets/uploads/no-image-available.png' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
                            <small id="msg"></small>
                        </div>
                        <div class="form-group position-relative">
                            <label class="control-label">Password</label>
                            <input type="password" class="form-control form-control-sm" name="password">
                            <i class="fa-solid fa-eye toggle-password" style="position: absolute; top: 38px; right: 10px; cursor: pointer;"></i>
                        </div>
                        <div class="form-group position-relative">
                            <label class="label control-label">Confirm Password</label>
                            <input type="password" class="form-control form-control-sm" name="cpass">
                            <i class="fa-solid fa-eye toggle-password-confirm" style="position: absolute; top: 38px; right: 10px; cursor: pointer;"></i>
                            <small id="pass_match" data-status=''></small>
                        </div>
                        <hr>

                        <!-- Class and Subject Selection -->
                        <div class="form-group">
                            <label>Select Class (Section)</label>
                            <select id="class_select" class="form-control form-control-sm">
                                <option value="">--Select Class--</option>
                                <?php 
                                    // Fetching all the classes
                                    $classes = $conn->query("SELECT id, CONCAT(curriculum,' ',level,' - ',section) AS class FROM class_list ORDER BY curriculum, level, section");
                                    while($row = $classes->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['class'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group" id="subject_container" style="display:none;">
                            <label>Select Subject</label>
                            <select id="subject_select" class="form-control form-control-sm">
                                <option value="">--Select Subject--</option>
                            </select>
                        </div>
                        <button type="button" id="add_subject_btn" class="btn btn-sm btn-primary" style="display:none;">Add Subject</button>
                        <hr>

                        <!-- Assigned Subjects -->
                        <h5>Assigned Subjects:</h5>
                        <ul id="assigned_subjects_list" class="list-group">
                            <?php if(isset($assigned_subjects) && count($assigned_subjects) > 0): ?>
                                <?php foreach($assigned_subjects as $asub): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?php echo $asub['class_name']." - ".$asub['subject']." (".$asub['code'].")" ?></span>
                                        <button type="button" class="btn btn-sm btn-danger remove_subject" data-rid="<?php echo $asub['academic_id'] ?>">Remove</button>
                                    </li>
                                    <input type="hidden" name="subjects[]" value="<?php echo $asub['academic_id'].'|'.$asub['faculty_id'].'|'.$asub['subject_id'] ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="col-lg-12 text-right justify-content-center d-flex">
                    <button class="btn btn-primary mr-2">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=irregular_student_list'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function displayImg(input,_this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // AJAX logic for fetching subjects based on class selection
    $('#class_select').change(function(){
        var class_id = $(this).val();
        if(class_id === '') {
            $('#subject_container').hide();
            $('#add_subject_btn').hide();
            $('#subject_select').html('<option value="">--Select Subject--</option>');
            return;
        }

        $.ajax({
            url: 'ajax.php?action=get_class_subjects',
            method: 'POST',
            data: {class_id: class_id},
            dataType: 'json',
            success: function(resp) {
                if (resp.status == 1) {
                    var opts = '<option value="">--Select Subject--</option>';
                    resp.data.forEach(function(sub) {
                        opts += `<option value="${sub.restriction_id}">${sub.subject_name}</option>`;
                    });
                    $('#subject_select').html(opts);
                    $('#subject_container').show();
                    $('#add_subject_btn').show();
                } else {
                    $('#subject_container').hide();
                    $('#add_subject_btn').hide();
                }
            }
        });
    });

    // Add subject button
    $('#add_subject_btn').click(function(){
        var val = $('#subject_select').val();
        if(val === '') {
            alert("Please select a subject.");
            return;
        }
        var text = $("#subject_select option:selected").text();

        // Append to the assigned subjects list
        var li = `<li class="list-group-item d-flex justify-content-between align-items-center">
            <span>${$('#class_select option:selected').text()} - ${text}</span>
            <button type="button" class="btn btn-sm btn-danger remove_subject" data-rid="${val}">Remove</button>
        </li>
        <input type="hidden" name="subjects[]" value="${val}">`;

        $('#assigned_subjects_list').append(li);
    });

    // Remove subject from list
    $('#assigned_subjects_list').on('click', '.remove_subject', function(){
        $(this).closest('li').next('input[type="hidden"]').remove();
        $(this).closest('li').remove();
    });

    // Submit the form
    $('#manage_irregular_student').submit(function(e){
        e.preventDefault();
        start_load();
        $('#msg').html('');
        $.ajax({
            url: 'ajax.php?action=save_irregular_student',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp) {
                end_load();
                if (resp == 1) {
                    alert_toast('Data successfully saved.', "success");
                    setTimeout(function() {
                        location.replace('index.php?page=irregular_student_list')
                    }, 750)
                } else if (resp == 2) {
                    $('#msg').html("<div class='alert alert-danger'>Email already exists.</div>");
                    $('[name="email"]').addClass("border-danger")
                } else if (resp == 3) {
                    $('#msg').html("<div class='alert alert-danger'>School ID already exists.</div>");
                    $('[name="school_id"]').addClass("border-danger")
                }
            }
        });
    });
</script>
