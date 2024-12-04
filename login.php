<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include('./db_connect.php');
ob_start();
// if(!isset($_SESSION['system'])){

$system = $conn->query("SELECT * FROM system_settings")->fetch_array();
foreach ($system as $k => $v) {
  $_SESSION['system'][$k] = $v;
}
// }
ob_end_flush();
?>
<?php
if (isset($_SESSION['login_id']))
  header("location:index.php?page=home");

?>
<?php include 'header.php' ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style/loginpagestyle.css">
  <title>Login</title>

  <!-- Normalize.css for consistent styling across browsers -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
  <style>
    /* Place your custom styles here (See below for some fixes) */
  </style>
</head>

<body style="background: linear-gradient(to bottom left, rgba(3, 63, 192, 0.521), rgba(172, 147, 7, 0.514)), url('assets/uploads/Lyceum2.jpg'); background-size: cover;">

  <header class="page-header"></header>
  <div class="login-box">
    <div class="login-logo">
      <a href="#" class="text-white"></a>
    </div>
    <div class="cards">
      <div class="card-bodies">
        <form action="" id="login-form">
          <div class="input-group mb-3">
          <input type="text" name="identifier" required placeholder="">
<label for="identifier">Email or School ID</label>

            <div class="input-iconiess">
              <span class="fas fa-envelope input-icon"></span>
            </div>
          </div>
          <div class="input-group mb-3">
  <input type="password" id="password" name="password" required placeholder="">
  <label for="password">Password</label>
  <div class="input-iconies">
    <i class="fa-solid fa-eye" id="toggle-password"></i>
  </div>
</div>

          <!-- Select dropdown -->
          <div class="select">
            <div class="selected">Login As</div>
            <div class="options">
              <input type="radio" id="student" name="login" value="3" checked>
              <label for="student" class="option">STUDENT</label>
              <input type="radio" id="faculty" name="login" value="4">
              <label for="faculty" class="option">FACULTY</label>
              <input type="radio" id="admin" name="login" value="1">
              <label for="admin" class="option">ADMIN</label>
              <input type="radio" id="dean" name="login" value="2">
              <label for="dean" class="option">DEAN</label>
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="path/to/adminlte.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#login-form').submit(function(e) {
        e.preventDefault();
        start_load();
        if ($(this).find('.alert-danger').length > 0)
          $(this).find('.alert-danger').remove();
        $.ajax({
          url: 'ajax.php?action=login',
          method: 'POST',
          data: $(this).serialize(),
          error: function(err) {
            console.log(err);
            end_load();
          },
          success: function(resp) {
            if (resp == 1) {
              location.href = 'index.php?page=home';
            } else {
              $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
              end_load();
            }
          }
        });
      });

      document.querySelector('.selected').addEventListener('click', function() {
        const options = document.querySelector('.options');
        options.style.display = options.style.display === 'flex' ? 'none' : 'flex';
      });

      document.querySelectorAll('.option').forEach(option => {
        option.addEventListener('click', function() {
          const selectedText = this.textContent;
          document.querySelector('.selected').textContent = selectedText;
          document.querySelector('.options').style.display = 'none';
        });
      });

      document.querySelector('.selected').addEventListener('click', function() {
        const options = document.querySelector('.options');
        options.classList.toggle('show');
        if (options.classList.contains('show')) {
          document.querySelector('.selected').classList.add('options-visible');
        } else {
          document.querySelector('.selected').classList.remove('options-visible');
        }
      });

      document.querySelectorAll('.option').forEach(option => {
        option.addEventListener('click', function() {
          const selectedText = this.textContent;
          document.querySelector('.selected').textContent = selectedText;
          document.querySelector('.options').style.display = 'none';
        });
      });
    });
    
    $(document).ready(function() {
      $('#toggle-password').click(function() {
        const passwordField = $('#password');
        const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
      });
    });
  </script>

</body>

</html>