<?php
?>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form action="" id="manage_student">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
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
                            <label for="" class="control-label">Class</label>
                            <select name="class_id" id="class_id" class="form-control form-control-sm select2">
                                <option value=""></option>
                                <?php 
                                $classes = $conn->query("SELECT id,concat(curriculum,' ',level,' - ',section) as class FROM class_list");
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
                            <img src="<?php echo isset($avatar) ? 'assets/uploads/'.$avatar :'' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail ">
                        </div>
                    </div>
                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
                            <small id="#msg"></small>
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
<style>
    img#cimg{
        height: 15vh;
        width: 15vh;
        object-fit: cover;
        border-radius: 100% 100%;
    }
    /* Positioning adjustments if needed */
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
    $(document).ready(function(){
        // Password Match Validation
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

        // Image Preview Function
        function displayImg(input,_this) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#cimg').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Toggle Password Visibility for Password Field
        $('.toggle-password').click(function(){
            var passwordField = $('[name="password"]');
            var type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Toggle Password Visibility for Confirm Password Field
        $('.toggle-password-confirm').click(function(){
            var passwordField = $('[name="cpass"]');
            var type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Autofill Password Functionality
        $('#autofill_password').click(function(){
            // Retrieve the values of lastname, firstname, and school_id
            var lastname = $('[name="lastname"]').val().trim();
            var firstname = $('[name="firstname"]').val().trim();
            var school_id = $('[name="school_id"]').val().trim();

            // Check if all fields are filled
            if(lastname === '' || firstname === '' || school_id === ''){
                alert("Please ensure Last Name, First Name, and School ID are filled before autofilling the password.");
                return;
            }

            // Concatenate the values to form the password
            var generatedPassword = lastname + firstname + school_id;

            // Optionally, you can enforce password policies here (e.g., minimum length, special characters)

            // Set the password and confirm password fields
            $('[name="password"]').val(generatedPassword);
            $('[name="cpass"]').val(generatedPassword);

            // Update the password match status
            $('#pass_match').attr('data-status','1').html('<i class="text-success">Password Matched.</i>');

            // Reset the eye icons to 'fa-eye-slash' if necessary
            $('.toggle-password, .toggle-password-confirm').removeClass('fa-eye-slash').addClass('fa-eye');
            $('[name="password"], [name="cpass"]').attr('type', 'password');
        });

        // Form Submission Handling
        $('#manage_student').submit(function(e){
            e.preventDefault()
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
			url: 'ajax.php?action=save_student',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				if (resp == 1) {
					alert_toast('Data successfully saved.', "success");
					setTimeout(function() {
						location.replace('index.php?page=student_list')
					}, 750)
				} else if (resp == 2) {
					$('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
					$('[name="email"]').addClass("border-danger")
                    
					end_load()
				} 
                else if (resp == 3) {
                    $('#msg').html("<div class='alert alert-danger'>School ID already exist.</div>");
					$('[name="school_id"]').addClass("border-danger")
					end_load()
				}
			}
		})
        })
    });
</script>
