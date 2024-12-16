<?php
// Fetch classes
$classes = $conn->query("SELECT id, CONCAT(curriculum,' ',level,' - ',section) AS class FROM class_list ORDER BY curriculum, level, section");

// If editing, prefetch assigned subjects for this irregular student
$assigned_subjects = array(); // This will hold (class_id, subject_id) pairs or just subject_id if we store that way
if(isset($id) && !empty($id)){
    // Fetch currently assigned subjects for editing scenario:
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
                              <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
                              <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center">
                            <img src="<?php echo isset($avatar) ? 'assets/uploads/'.$avatar :'assets/uploads/no-image-available.png' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail ">
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
                        <hr>
                        <!-- Class and Subject Selection -->
                        <div class="form-group">
                            <label>Select Class (Section)</label>
                            <select id="class_select" class="form-control form-control-sm">
                                <option value="">--Select Class--</option>
                                <?php while($row=$classes->fetch_assoc()): ?>
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
                        <h5>Assigned Subjects:</h5>
                        <ul id="assigned_subjects_list" class="list-group">
                            <?php if(isset($assigned_subjects) && count($assigned_subjects) > 0): ?>
                                <?php foreach($assigned_subjects as $asub): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?php echo $asub['class_name']." - ".$asub['subject']." (".$asub['code'].")" ?></span>
                                    <button type="button" class="btn btn-sm btn-danger remove_subject" data-aid="<?php echo $asub['academic_id'] ?>" data-fid="<?php echo $asub['faculty_id'] ?>" data-sid="<?php echo $asub['subject_id'] ?>">Remove</button>
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
<style>
    img#cimg{
        height: 15vh;
        width: 15vh;
        object-fit: cover;
        border-radius: 100% 100%;
    }
    .form-group.position-relative {
        position: relative;
    }
    .toggle-password, .toggle-password-confirm {
        color: #6c757d;
    }
    .toggle-password:hover, .toggle-password-confirm:hover {
        color: #495057;
    }
</style>
<!-- Font Awesome for Eye Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pIVZkDkMGWf4AezBM/wFqbZAgN0q0bYlSg2kd+xV1Vh/M2GVaYqXybmV5fnW0xCtmV1Ij7X0eAkyakm6Ah1Lw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
    })
    $('.toggle-password').click(function(){
        var passwordField = $('[name="password"]');
        var type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });
    $('.toggle-password-confirm').click(function(){
        var passwordField = $('[name="cpass"]');
        var type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });

    // AJAX logic for fetching subjects based on class selection
    $('#class_select').change(function(){
        var class_id = $(this).val();
        if(class_id === ''){
            $('#subject_container').hide();
            $('#add_subject_btn').hide();
            $('#subject_select').html('<option value="">--Select Subject--</option>');
            return;
        }
        // Fetch subjects via AJAX
        $.ajax({
            url: 'ajax.php?action=get_class_subjects',
            method: 'POST',
            data: {class_id: class_id},
            dataType: 'json',
            success: function(resp) {
    if (resp.status == 1) {
        var opts = '<option value="">--Select Subject--</option>';
        resp.data.forEach(function(sub) {
            // Now we use restriction_id as the value
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
    if(val === ''){
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


    $('#manage_irregular_student').submit(function(e){
        e.preventDefault();
        $('input').removeClass("border-danger")
        start_load()
        $('#msg').html('')
        if($('[name="password"]').val() != '' && $('[name="cpass"]').val() != ''){
            if($('#pass_match').attr('data-status') != 1){
                if($("[name='password']").val() !=''){
                    $('[name="password"],[name="cpass"]').addClass("border-danger")
                    end_load()
                    return false;
                }
            }
        }
        $.ajax({
            url: 'ajax.php?action=save_irregular_student',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                end_load();
                if (resp == 1) {
                    alert_toast('Data successfully saved.', "success");
                    setTimeout(function() {
                        location.replace('index.php?page=irregular_student_list')
                    }, 750)
                } else if (resp == 2) {
                    $('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
                    $('[name="email"]').addClass("border-danger")
                } else if (resp == 3) {
                    $('#msg').html("<div class='alert alert-danger'>School ID already exist.</div>");
                    $('[name="school_id"]').addClass("border-danger")
                }
            }
        })
    });
</script>
