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
<div class="container-fluid">
    <div class="form-container">
        <div id="msg"></div>

        <form action="" id="manage-user">    
            <input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">

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
                <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
                <small><i>Leave this blank if you don't want to change the password.</i></small>
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
        </form>
    </div>
</div>

<style>
    img#cimg {
        height: 15vh;
        width: 15vh;
        object-fit: cover;
        border-radius: 100% 100%;
        margin-left: 35%;
    }

    .form-container {
        max-width: auto;
        max-height: 700px;
        background-color: #fff;
        font-size: 14px;
        font-family: inherit;
        color: #212121;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-container button:active {
        scale: 0.95;
    }

    .form-container .form-group {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .form-container .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #313131;
        transition: color 0.2s ease, transform 0.2s ease;
    }

    .form-container .form-group input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 6px;
        font-family: inherit;
        border: 1px solid #ccc;
        transition: border-color 0.2s ease;
    }

    .form-container .form-group input::placeholder {
        opacity: 0.5;
    }

    .form-container .form-group input:focus {
        outline: none;
        border-color: #1778f2;
    }

    .form-container .form-group input:focus + label {
        color: #1778f2;
        transform: scale(1.05);
    }

    .form-container .form-group:focus-within label {
        color: #1778f2;
        transform: scale(1.05);
    }

    .form-container .form-submit-btn {
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: inherit;
        color: #fff;
        background-color: #1778f2;
        width: 100%;
        padding: 12px 16px;
        font-size: inherit;
        gap: 8px;
        margin: 12px 0;
        cursor: pointer;
        border-radius: 6px;
        border: none;
    }

    .form-container .form-submit-btn:hover {
        background-color: #1778f2;
    }

    .form-container .form-submit-btn:focus {
        outline: none;
    }
</style>

<script>
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $('#manage-user').submit(function(e) {
        e.preventDefault();
        start_load()
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
                    alert_toast("Data successfully saved", 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1500)
                } else {
                    $('#msg').html('<div class="alert alert-danger">Username already exist</div>')
                    end_load()
                }
            }
        })
    })
</script>
