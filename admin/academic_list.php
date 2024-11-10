<?php include'db_connect.php' ?>
<style>
 .btn.new_academic {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: bold;
  color: white;
  background-color: #28a745; /* Green color */
  border: none;
  border-radius: 5px;
  text-decoration: none;
  box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
  transition: transform 0.2s ease, background-color 0.2s ease;
  position: relative;
  overflow: hidden;
}

/* Style the icon */
.btn.new_academic i {
  margin-right: 8px;
  font-size: 16px;
  color: #fff;
  transition: transform 0.2s ease;
}

/* Style the text */
.btn.new_academic .text {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

/* Hover effect */
.btn.new_academic:hover {
  transform: scale(1.05); /* Slight enlargement */
}

/* When hovered, the icon moves to the center */
.btn.new_academic:hover i {
  transform: translateX(30px); /* Move the icon to the center */
}

/* Hide the text on hover */
.btn.new_academic:hover .text {
  opacity: 0; /* Hide the text */
}

/* Active effect (on click) */
.btn.new_academic:active {
  transform: scale(1); /* Reset size on click */
}

  .btn.manage_academic, .btn.delete_academic {
  position: relative;
  background: rgb(177, 228, 255);
  color: #000;
  padding: 15px;
  margin: 0;
  border-radius: 10px;
  width: 40px;
  height: 40px;
  font-size: 17px;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.edit-tooltip {
  position: absolute;
  top: 0px;
  font-size: 14px;
  background: #ffffff;
  color: #ffffff;
  padding: 5px 8px;
  border-radius: 5px;
  box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
  opacity: 0;
  pointer-events: none;
  transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  width: 150px;
}

.edit-tooltip::before {
  position: absolute;
  content: "";
  height: 8px;
  width: 8px;
  background: #ffffff;
  bottom: -3px;
  left: 50%;
  transform: translate(-50%) rotate(45deg);
  transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.btn.manage_academic:hover .edit-tooltip,
.btn.delete_academic:hover .edit-tooltip {
  top: -45px;
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}

.edit-icon {
  font-size: 20px;
}

.btn.manage_academic:hover,
.btn.manage_academic:hover .edit-tooltip,
.btn.manage_academic:hover .edit-tooltip::before {
  background: linear-gradient(320deg, rgb(3, 77, 146), rgb(0, 60, 255));
  color: #ffffff;
}

.btn.delete_academic:hover,
.btn.delete_academic:hover .edit-tooltip,
.btn.delete_academic:hover .edit-tooltip::before {
  background: linear-gradient(320deg, rgb(246, 68, 68), rgb(255, 0, 0));
  color: #ffffff;
}


.custom-popup {
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.custom-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: red;
}

.custom-cancel-btn {
  background-color: gray !important;
  color: #333;
  font-size: 1rem;
  padding: 0.75rem 1.5rem;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.custom-confirm-btn {
  background-color: #b91c1c; /* Red for delete */
  color: #fff;
  font-size: 1rem;
  padding: 0.75rem 1.5rem;
  border-radius: 5px;
  border: none;
}

.custom-confirm-btn.make_default {
  background-color: #10b981; 
}

.custom-content {
  color: black;
}

.swal2-icon.swal2-error {
  background-color: #FEE2E2;
  color: #DC2626;
}

.swal2-icon.swal2-info {
  background-color: #E0F2FE;
  color: #0ea5e9;
}

.custom-cancel-btn:hover {
  background-color: #c0c0c0;
  border-color: #aaa;
}

.custom-confirm-btn:hover {
  background-color: #991b1b;
}

.custom-confirm-btn.make_default:hover {
  background-color: #047857;
}

.swal2-popup {
  width: 400px !important;
  padding: 1.5em;
}
.card-body {
    overflow-x: auto; 
}


.table {
    width: 100%;
    table-layout: fixed; 
}

</style>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
      <a class="btn new_academic" href="javascript:void(0)">
  <i class="fa fa-plus"></i>
  <span class="text">Add New</span>
</a>

			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="25%">
					<col width="25%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Year</th>
						<th>Semester</th>
						<th>System Default</th>
						<th>Evaluation Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT * FROM academic_list order by abs(year) desc,abs(semester) desc ");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo $row['year'] ?></b></td>
						<td><b><?php echo $row['semester'] ?></b></td>
						<td class="text-center">
							<?php if($row['is_default'] == 0): ?>
								<button type="button" class="btn btn-secondary bg-gradient-secondary col-sm-4 btn-flat btn-sm px-1 py-0 make_default" data-id="<?php echo $row['id'] ?>">No</button>
							<?php else: ?>
								<button type="button" class="btn btn-primary bg-gradient-primary col-sm-4 btn-flat btn-sm px-1 py-0">Yes</button>
							<?php endif; ?>
						</td>
						<td class="text-center">
							<?php if($row['status'] == 0): ?>
								<span class="badge badge-secondary">Not yet Started</span>
							<?php elseif($row['status'] == 1): ?>
								<span class="badge badge-success">Starting</span>
							<?php elseif($row['status'] == 2): ?>
								<span class="badge badge-primary">Closed</span>
							<?php endif; ?>
						</td>

						<td class="text-center">
		                    <div class="btn-group">
							<a href="javascript:void(0)" data-id="<?php echo $row['id']; ?>" class="btn manage_academic">
  <span class="edit-tooltip">Edit Academic</span>
  <span class="edit-icon"><i class="fas fa-edit"></i></span>
</a>

<button type="button" class="btn delete_academic" data-id="<?php echo $row['id']; ?>">
  <span class="edit-tooltip">Delete Academic</span>
  <span class="edit-icon"><i class="fas fa-trash"></i></span>
</button>

	                      </div>
						</td>
					</tr>	
				<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.new_academic').click(function(){
			uni_modal("New academic","<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php")
		})
		$('.manage_academic').click(function(){
			uni_modal("Manage academic","<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php?id="+$(this).attr('data-id'))
		})
		$('.delete_academic').click(function() {
    _conf("Are you sure to delete this academic?", "delete_academic", [$(this).attr('data-id')], 'error', 'DELETE');
});

$('.make_default').click(function() {
    _conf("Are you sure to make this academic year the system default?", "make_default", [$(this).attr('data-id')], 'info', 'CONFIRM');
});

function _conf(message, type, data, iconType, confirmText) {
    Swal.fire({
        title: type === 'delete_academic' ? 'DELETE ACADEMIC' : 'MAKE DEFAULT',
        text: message,
        icon: iconType,  // Error for delete, info for make default
        iconColor: iconType === 'error' ? '#b91c1c' : '#0ea5e9',  // Red for delete, Blue for make default
        showCancelButton: true,
        confirmButtonColor: iconType === 'error' ? '#b91c1c' : '#10b981',  // Red for delete, Green for make default
        cancelButtonColor: '#d3d3d3',
        confirmButtonText: confirmText,  // DELETE or CONFIRM
        cancelButtonText: 'Cancel',
        background: '#f9fafb',
        color: '#333',
        padding: '1.5em',
        width: '400px',
        customClass: {
            popup: 'custom-popup',
            title: 'custom-title',
            cancelButton: 'custom-cancel-btn',
            confirmButton: 'custom-confirm-btn',
            content: 'custom-content'  
        }
    });
}
		$('#list').dataTable()
	})
	function delete_academic($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_academic',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
	function make_default($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=make_default',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Dafaut Academic Year Updated",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
</script>