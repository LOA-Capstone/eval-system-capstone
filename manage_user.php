<?php 
include('db_connect.php');
session_start();

// Debug logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure login_type is set and is a valid index
if(!isset($_SESSION['login_type']) || $_SESSION['login_type'] < 1 ) {
    die("Invalid login type. Current login_type: " . $_SESSION['login_type']);
}

// Define the type array to match your database tables
$type = array(
    "", // 0 index (typically empty)
    "users",           // 1 - users (admin, dean)
    "users",           // 4 - users (dean)
    "student_list",    // 3 - students
    "faculty_list",    // 2 - faculty       
    "faculty_list"     // 5 - faculty
);

// Debugging: Print out the current login type and corresponding table
// echo "Login Type: " . $_SESSION['login_type'] . "<br>";
// echo "Corresponding Table: " . $type[$_SESSION['login_type']] . "<br>";

// Check if ID is set in the URL
if(isset($_GET['id'])){
    
    // Safely select the correct table based on login type
    $table = $type[$_SESSION['login_type']];
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if a row was found
    if($result->num_rows > 0) {
        // Fetch the row as an associative array
        $meta = $result->fetch_assoc();
    } else {
        // No user found
        die("No user found in table: $table for ID: " . $_GET['id']);
    }
}
?>
<div class="container-fluid">
    <div id="msg"></div>
    
    <form action="" id="manage-user">    
        <input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
            <small><i>Leave this blank if you dont want to change the password.</i></small>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Avatar</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
              <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
        </div>
        <div class="form-group d-flex justify-content-center">
            <img src="<?php echo isset($meta['avatar']) ? 'assets/uploads/'.$meta['avatar'] : 'assets/uploads/no-image-available.png' ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
        </div>
    </form>
</div>
<!-- Rest of your existing HTML and JavaScript remains the same -->
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
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
	$('#manage-user').submit(function(e){
    e.preventDefault();
    start_load()
    $.ajax({
        url:'ajax.php?action=update_user_password',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        type: 'POST',
        success:function(resp){
            if(resp == 1){
                alert_toast("Password successfully updated",'success')
                setTimeout(function(){
                    location.reload()
                },1500)
            }else{
                $('#msg').html('<div class="alert alert-danger">Password update failed</div>')
                end_load()
            }
        }
    })
})

</script>

