<?php 
include('db_connect.php');
session_start();
if(isset($_GET['id'])){
    $type = array("", "users", "faculty_list", "student_list");
    $user = $conn->query("SELECT * FROM {$type[$_SESSION['login_type']]} where id =" . $_GET['id']);
    foreach($user->fetch_array() as $k => $v){
        $meta[$k] = $v;
    }
}
?>
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="container-fluid">
    <div id="msg"></div>
    <form action="" id="manage-user">    
        <input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">

        <div class="row">
            <!-- Single Column -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname'] : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname'] : '' ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email'] : '' ?>" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <small><i>Leave this blank if you don't want to change the password.</i></small>
                </div>

                <div class="form-group">
                    <label for="confirmpassword">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" value="" autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <small><i>Enter the same password to confirm.</i></small>
                </div>

                <div class="form-group">
                    <label for="customFile" class="control-label">Avatar</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>

                <div class="form-group d-flex justify-content-center">
                    <img src="<?php echo isset($meta['avatar']) ? 'assets/uploads/' . $meta['avatar'] : '' ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
                </div>

                <div class="form-group d-flex justify-content-center">
                    <button type="submit" class="form-submit-btn">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* Image style (if needed) */
img#cimg {
    height: 15vh;
    width: 15vh;
    object-fit: cover;
    border-radius: 100%;
    margin-left: 30%;
}

.container-fuild{
    padding: 0px;
}
/* Form group container */
.form-group {
    background: #1C204B;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0px;
    position: relative;
    border-radius: 25px;
}

/* Label container styling */
.form-group .label {
    display: flex;
    flex-direction: column;
    gap: 5px;
    height: fit-content;
    position: relative;
}

/* Title inside the label */
.form-group .label .title {
    padding: 0px;
    transition: all 300ms;
    font-size: 20px;
    color: white;
    font-weight: 700;
    width: fit-content;
    top: 17px;
    position: relative;
    left: 15px;
    background: #1C204B;
}

/* Input field inside the form-group */
.form-group .input-field {
    width: 100%;
    height: 50px;
    border-radius: 15px;
    outline: none;
    background-color: transparent;
    border: 1px solid white;
    transition: all 0.3s;
    caret-color: #d17842;
    color: white;
}

/* Hover effect for input field */
.form-group .input-field:hover {
    border-color: yellow;
}

/* Focus effect for input field */
.form-group .input-field:focus {
    border-color: yellow;
}


/* Label moves when input or textarea is filled, focused or has content */
.form-group .label.filled .title,
.form-group .label:has(input:focus) .title {
    top: -10px;
    color: yellow;
}

/* Submit button styling */
.form-submit-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: inherit;
    color: #fff;
    background-color: #1778f2;
    width: 100%;
    padding: 12px 16px;
    font-size: 16px;
    gap: 8px;
    cursor: pointer;
    border-radius: 50px; /* Rounded corners */
    border: none;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
    transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth transition */
}

/* Hover effect for submit button */
.form-submit-btn:hover {
    background-color: #0056b3; /* Darker shade on hover */
    transform: translateY(-3px); /* Slightly raise on hover */
}

/* Active effect for submit button */
.form-submit-btn:active {
    background-color: #004085; /* Even darker on click */
    transform: translateY(1px); /* Slightly lower on click */
}

/* Icon in submit button */
.form-submit-btn i {
    transition: transform 0.3s ease; /* Smooth transition for icon */
}

/* Rotate icon on hover */
.form-submit-btn:hover i {
    transform: rotate(90deg);
}

.col-md-12 {
    flex: 1 1 100%;
    padding: 10px;
}


</style>

<script>
    // Toggle password visibility
    $('#togglePassword').click(function() {
        var passwordField = $('#password');
        var type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).children('i').toggleClass('fa-eye fa-eye-slash');
    });

    $('#toggleConfirmPassword').click(function() {
        var confirmPasswordField = $('#confirmpassword');
        var type = confirmPasswordField.attr('type') === 'password' ? 'text' : 'password';
        confirmPasswordField.attr('type', type);
        $(this).children('i').toggleClass('fa-eye fa-eye-slash');
    });

    // Form submission with password validation
    $('#manage-user').submit(function(e) {
        e.preventDefault();
        var password = $('#password').val();
        var confirmPassword = $('#confirmpassword').val();

        if (password !== confirmPassword) {
            alert("PASSWORD NOT THE SAME");
            return false;
        }

        start_load();
        $.ajax({
            url: 'ajax.php?action=update_user',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully saved", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $('#msg').html('<div class="alert alert-danger">Username already exists</div>');
                    end_load();
                }
            }
        });
    });
</script>
