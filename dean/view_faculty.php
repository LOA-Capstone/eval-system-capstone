<?php
include '../db_connect.php';
$department_id = $_SESSION['login_department_id'];
$faculty_id = $_GET['id'];

// Check if the faculty belongs to the Dean's department
$qry = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM faculty_list WHERE id = '$faculty_id' AND department_id = '$department_id'");
if ($qry->num_rows > 0) {
    $faculty = $qry->fetch_assoc();
    foreach ($faculty as $k => $v) {
        $$k = $v;
    }
    // Display faculty details
} else {
    echo "<h4>You do not have permission to view this faculty.</h4>";
    exit;
}
?>

<div class="container-fluid">
	<div class="card card-widget widget-user shadow">
      <div class="widget-user-header bg-dark">
        <h3 class="widget-user-username"><?php echo ucwords($name) ?></h3>
        <h5 class="widget-user-desc"><?php echo $email ?></h5>
      </div>
      <div class="widget-user-image">
      	<?php if(empty($avatar) || (!empty($avatar) && !is_file('../assets/uploads/'.$avatar))): ?>
      	<span class="brand-image img-circle elevation-2 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 90px;height:90px"><h4><?php echo strtoupper(substr($firstname, 0,1).substr($lastname, 0,1)) ?></h4></span>
      	<?php else: ?>
        <img class="img-circle elevation-2" src="assets/uploads/<?php echo $avatar ?>" alt="User Avatar"  style="width: 90px;height:90px;object-fit: cover">
      	<?php endif ?>
      </div>
      <div class="card-footer">
        <div class="container-fluid">
        	<dl>
        		<dt>School ID</dt>
        		<dd><?php echo $school_id ?></dd>
        	</dl>
        </div>
    </div>
	</div>
</div>
<div class="modal-footer display p-0 m-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<style>
	#uni_modal .modal-footer{
		display: none
	}
	#uni_modal .modal-footer.display{
		display: flex
	}
</style>