<?php
// ... existing code ...
// Check if user is a dean and is not editing an existing faculty
if ($_SESSION['login_type'] == 2 && !isset($_GET['id'])) {
    // User is a dean trying to add a new faculty, redirect or show error message
    echo "<h4>You do not have permission to add new faculties.</h4>";
    exit;
}
$department_id = $_SESSION['login_department_id']; // Get Dean's department ID
?>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form action="" id="manage_faculty">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <!-- Include department_id as a hidden input -->
                <input type="hidden" name="department_id" value="<?php echo $department_id; ?>">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <!-- Existing form fields -->
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
                    </div>
                    <div class="col-md-6">
                        <!-- Existing form fields -->
                        <div class="form-group">
                            <label for="" class="control-label">Avatar</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                        </div>
                        <div class="form-group d-flex justify-content-center align-items-center">
                            <img src="<?php echo isset($avatar) ? 'assets/uploads/' . $avatar : '' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail ">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control form-control-sm" name="email" required value="<?php echo isset($email) ? $email : '' ?>">
                            <small id="#msg"></small>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Password</label>
                            <input type="password" class="form-control form-control-sm" name="password" <?php echo !isset($id) ? "required" : '' ?>>
                            <small><i><?php echo isset($id) ? "Leave this blank if you don't want to change the password" : '' ?></i></small>
                        </div>
                        <div class="form-group">
                            <label class="label control-label">Confirm Password</label>
                            <input type="password" class="form-control form-control-sm" name="cpass" <?php echo !isset($id) ? 'required' : '' ?>>
                            <small id="pass_match" data-status=''></small>
                        </div>
                        <!-- Remove the Department selection field -->
                        <!-- The department is set automatically -->
                    </div>
                </div>
                <hr>
                <div class="col-lg-12 text-right justify-content-center d-flex">
                    <button class="btn btn-primary mr-2">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=faculty_list'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
	img#cimg {
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	$('[name="password"],[name="cpass"]').keyup(function() {
		var pass = $('[name="password"]').val()
		var cpass = $('[name="cpass"]').val()
		if (cpass == '' || pass == '') {
			$('#pass_match').attr('data-status', '')
		} else {
			if (cpass == pass) {
				$('#pass_match').attr('data-status', '1').html('<i class="text-success">Password Matched.</i>')
			} else {
				$('#pass_match').attr('data-status', '2').html('<i class="text-danger">Password does not match.</i>')
			}
		}
	})

	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$('#manage_faculty').submit(function(e) {
		e.preventDefault()
		$('input').removeClass("border-danger")
		start_load()
		$('#msg').html('')
		if ($('[name="password"]').val() != '' && $('[name="cpass"]').val() != '') {
			if ($('#pass_match').attr('data-status') != 1) {
				if ($("[name='password']").val() != '') {
					$('[name="password"],[name="cpass"]').addClass("border-danger")
					end_load()
					return false;
				}
			}
		}
		$.ajax({
			url: 'ajax.php?action=save_faculty',
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
						location.replace('index.php?page=faculty_list')
					}, 750)
				} else if (resp == 2) {
					$('#msg').html("<div class='alert alert-danger'>Email already exist.</div>");
					$('[name="email"]').addClass("border-danger")
					end_load()
				} else if (resp == 3) {
					$('#msg').html("<div class='alert alert-danger'>School ID already exist.</div>");
					$('[name="school_id"]').addClass("border-danger")
					end_load()
				}
			}
		})
	})
</script>