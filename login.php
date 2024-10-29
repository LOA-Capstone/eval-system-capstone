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
  <!-- Include your existing head content -->
  <style>
    body {
      background: linear-gradient(to bottom left, rgba(3, 63, 192, 0.5), rgba(172, 147, 7, 0.5)), url('assets/uploads/Lyceum2.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
    }
    .login-box {
      width: 360px;
      margin: 7% auto;
    }
    h2 {
      text-align: center;
      margin-top: 20px;
      font-weight: bold;
      color: #fff;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
    }
    .card {
      background: rgba(255, 255, 255, 0.85);
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .login-card-body {
      padding: 20px;
    }
    .btn-primary {
      background-color: #003366;
      border-color: #003366;
    }
    .btn-primary:hover {
      background-color: #002244;
      border-color: #002244;
    }
    .input-group-text {
      background-color: #003366;
      color: #fff;
      border: none;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #003366;
    }
    .custom-select {
      border-color: #ced4da;
    }
    .custom-select:focus {
      box-shadow: none;
      border-color: #003366;
    }
    .icheck-primary input[type="checkbox"]:checked + label::before {
      background-color: #003366;
      border-color: #003366;
    }
  </style>
</head>
<body>
  <h2>STUDENT-FACULTY EVALUATION SYSTEM</h2>
  <div class="login-box">
    <div class="login-logo">
      <!-- You can add a logo here if you have one -->
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <form action="" id="login-form">
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" required placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" required placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="form-group mb-3">
            <label for="login">Login As</label>
            <select name="login" id="login" class="custom-select custom-select-sm">
              <option value="3">Student</option>
              <option value="2">Faculty</option>
              <option value="1">Admin</option>
              <option value="4">Guidance</option>
            </select>
          </div>
          <div class="row">
            <div class="col-7">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-5">
              <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->
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
  </script>
</body>
</html>
