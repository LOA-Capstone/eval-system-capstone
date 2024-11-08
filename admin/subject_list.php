<?php include'db_connect.php' ?>
<style>
  /* Button base style */
  .btn.new_subject {
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
  .btn.new_subject i {
    margin-right: 8px;
    font-size: 16px;
    color: #fff;
    transition: transform 0.2s ease;
  }

  /* Style the text */
  .btn.new_subject .text {
    transition: opacity 0.2s ease, transform 0.2s ease;
  }

  /* Hover effect */
  .btn.new_subject:hover {
    transform: scale(1.05); /* Slight enlargement */
  }

  /* When hovered, the icon moves to the center */
  .btn.new_subject:hover i {
    transform: translateX(30px); /* Move the icon to the center */
  }

  /* Hide the text on hover */
  .btn.new_subject:hover .text {
    opacity: 0; /* Hide the text */
  }

  /* Active effect (on click) */
  .btn.new_subject:active {
    transform: scale(1); /* Reset size on click */
  }
.col-lg-12{
	background:
}


.btn.manage_subject, .btn.delete_subject {
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

.btn.manage_subject:hover .edit-tooltip,
.btn.delete_subject:hover .edit-tooltip {
  top: -45px;
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}

.edit-icon {
  font-size: 20px;
}

.btn.manage_subject:hover,
.btn.manage_subject:hover .edit-tooltip,
.btn.manage_subject:hover .edit-tooltip::before {
  background: linear-gradient(320deg, rgb(3, 77, 146), rgb(0, 60, 255));
  color: #ffffff;
}

.btn.delete_subject:hover,
.btn.delete_subject:hover .edit-tooltip,
.btn.delete_subject:hover .edit-tooltip::before {
  background: linear-gradient(320deg, rgb(246, 68, 68), rgb(255, 0, 0));
  color: #ffffff;
}

  
</style>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
			<a class="btn new_subject" href="javascript:void(0)">
  <i class="fa fa-plus"></i>
  <span class="text">Add New</span>
</a>
			</div>
		</div>
		<div class="card-body">
    <table class="table tabe-hover table-bordered" id="list">
      <colgroup>
        <col width="5%">
        <col width="15%">
        <col width="30%">
        <col width="40%">
        <col width="15%">
      </colgroup>
      <thead>
        <tr>
          <th class="text-center">#</th>
          <th>Code</th>
          <th>Subject</th>
          <th>Description</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
        $qry = $conn->query("SELECT * FROM subject_list order by subject asc ");
        while($row= $qry->fetch_assoc()):
        ?>
        <tr>
          <th class="text-center"><?php echo $i++ ?></th>
          <td><b><?php echo $row['code'] ?></b></td>
          <td><b><?php echo $row['subject'] ?></b></td>
          <td><b><?php echo $row['description'] ?></b></td>
          <td class="text-center">
            <div class="btn-group">
            <a href="javascript:void(0)" data-id='<?php echo $row['id'] ?>' class="btn manage_subject">
  <span class="edit-tooltip">Edit Subject</span>
  <span class="edit-icon"><i class="fas fa-edit"></i></span>
</a>

<button type="button" class="btn delete_subject" data-id="<?php echo $row['id'] ?>">
  <span class="edit-tooltip">Delete Subject</span>
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
		$('.new_subject').click(function(){
			uni_modal("New subject","<?php echo $_SESSION['login_view_folder'] ?>manage_subject.php")
		})
		$('.manage_subject').click(function(){
			uni_modal("Manage subject","<?php echo $_SESSION['login_view_folder'] ?>manage_subject.php?id="+$(this).attr('data-id'))
		})
	$('.delete_subject').click(function(){
	_conf("Are you sure to delete this subject?","delete_subject",[$(this).attr('data-id')])
	})
		$('#list').dataTable()
	})
	function delete_subject($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_subject',
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
</script>