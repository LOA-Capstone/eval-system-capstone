<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
  ob_start();
  // if(!isset($_SESSION['system'])){

    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
  // }
  ob_end_flush();
?>
<?php 
if(isset($_SESSION['login_id']))
header("location:index.php?page=home");

?>
<?php include 'header.php' ?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/loginpagestyle.css">
  <title>Document</title>
</head>

<body style="background: linear-gradient(to bottom left, rgba(3, 63, 192, 0.521), rgba(172, 147, 7, 0.514)), url('assets/uploads/Lyceum2.jpg');">
<header class="page-header">
  </header>
<div class="login-box">
  <div class="login-logo">
    <a href="#" class="text-white"></a>
  </div>
  <!-- /.login-logo -->
  <div class="cards">
    <div class="card-bodies">
      <form action="" id="login-form">
        <div class="input-group mb-3">
          <input type="email"  name="email" required placeholder="">
          <label for="email">Email</label>
          
            <div class="input-iconies">
              <span class="fas fa-envelope input-icon"></span>
            </div>
          
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" required placeholder="">
          <label for="password">Password</label>
          
            <div class="input-iconies">
              <span class="fas fa-lock input-icon"></span>
            
          </div>
        </div>
        <div class="select">
  <div class="selected">Login As</div>
  <div class="options">
    <input type="radio" id="student" name="login" value="3" checked>
    <label for="student" class="option">Student</label>
    <input type="radio" id="faculty" name="login" value="2">
    <label for="faculty" class="option">Faculty</label>
    <input type="radio" id="admin" name="login" value="1">
    <label for="admin" class="option">Admin</label>
    <input type="radio" id="guidance" name="login" value="4">
    <label for="guidance" class="option">Guidance</label>
  </div>
</div>
<div class="rows">
<label class="container">
    <input type="checkbox" id="remember">
    <span class="checkmark"></span>
    Remember Me
  </label>
    <button type="submit" class="loginbutton">Sign In</button>
</div>

      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<?php include 'footer.php' ?>

<!-- Include your scripts here -->
<!-- jQuery -->
<script src="path/to/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="path/to/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="path/to/adminlte.min.js"></script>
<!-- Custom Script -->
<script>
  $(document).ready(function(){
    $('#login-form').submit(function(e){
      e.preventDefault()
      start_load()
      if($(this).find('.alert-danger').length > 0 )
        $(this).find('.alert-danger').remove();
      $.ajax({
        url:'ajax.php?action=login',
        method:'POST',
        data:$(this).serialize(),
        error:err=>{
          console.log(err)
          end_load();

        },
        success:function(resp){
          if(resp == 1){
            location.href ='index.php?page=home';
          }else{
            $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>')
            end_load();
          }
        }
      })
    })
  })

  document.querySelector('.selected').addEventListener('click', function() {
    const options = document.querySelector('.options');
    options.style.display = options.style.display === 'flex' ? 'none' : 'flex'; // Toggle visibility
  });

  document.querySelectorAll('.option').forEach(option => {
    option.addEventListener('click', function() {
      const selectedText = this.textContent; // Get the text of the selected option
      document.querySelector('.selected').textContent = selectedText; // Update the displayed text
      document.querySelector('.options').style.display = 'none'; // Hide options after selection
    });
  });

</script>

</body>
</html>