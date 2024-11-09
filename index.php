<!DOCTYPE html>
<html lang="en">
<?php session_start() ?>
<?php 
	if(!isset($_SESSION['login_id']))
	    header('location:login.php');
    include 'db_connect.php';
    ob_start();
  if(!isset($_SESSION['system'])){

    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach($system as $k => $v){
      $_SESSION['system'][$k] = $v;
    }
  }
  ob_end_flush();

	include 'header.php' 
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

  .modal-content{
    background-color: #1C204B; /* Dark background color */
  border: 1px solid #ffffff; /* White border */
  box-shadow: 0 4px 8px rgba(255, 255, 255, 0.1); /* Light shadow */
  border-radius: 8px;
  padding: 2rem;
  color: #ffffff;
  margin: 0;
  }
  .modal-header{
    border: none;
    margin: 0;
    font-family: 'poppins';
  }
  .modal-header h5{
    font-weight: 800 !important;
    font-size: 30px;
  }
  .modal-body{
    margin: 0;
  }
  .modal-footer{
    border: none;
    margin: 0;
    display: flex;
    justify-content: center;
    gap: 15px; 
  }

  .btn-primary {
  background-color: green !important;
  border: none;
  color: white;
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.btn-primary:hover {
  background-color: darkgreen !important;
}

.btn-secondary {
  background-color: gray !important;
  border: none;
  color: white;
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.btn-secondary:hover {
  background-color: darkgray !important;
}

.modal-footer {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 20px;
}

  
</style>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed style">
<div class="wrapper">
  <?php include 'topbar.php' ?>
  <?php include $_SESSION['login_view_folder'].'sidebar.php' ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  	 <div class="toast" id="alert_toast" role="alert" aria-live="assertive" aria-atomic="true">
	    <div class="toast-body text-white">
	    </div>
	  </div>
    <div id="toastsContainerTopRight" class="toasts-top-right fixed"></div>
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo $title ?></h1>
          </div><!-- /.col -->

        </div><!-- /.row -->
            <hr class="border-primary">
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
         <?php 
            $page = isset($_GET['page']) ? $_GET['page'] : 'home';
            if(!file_exists($_SESSION['login_view_folder'].$page.".php")){
                include '404.html';
            }else{
            include $_SESSION['login_view_folder'].$page.'.php';

            }
          ?>
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
    <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmation</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">  
          <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog modal-full-height  modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-arrow-right"></span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
  <div class="main-footer">
    <strong>Copyright &copy; 2024 <a href="https://lyceumalabang.edu.ph">lyceumalabang.edu.ph</a>.</strong>
    All rights reserved.
  </div>
  </div>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<!-- Bootstrap -->
<?php include 'footer.php' ?>
</body>

<style>
  .content-wrapper{
    background: radial-gradient(178.94% 106.41% at 26.42% 106.41%, #B1E4FF 0%, #FFFFFF 71.88%);
    margin-bottom: 0;
    height: auto;
  }

  .main-footer {
    background: transparent;
    color: black;
    font-size: 9px;
    height: auto;
    width: auto;
    text-align: center;
    position: fixed;
    bottom: 0;
    margin-top: 0;
    border: none;
}

</style>
</html>


