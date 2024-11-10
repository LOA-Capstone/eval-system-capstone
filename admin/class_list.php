<?php include'db_connect.php' ?>
<style>
  /* Button base style */
  .btn.new_class {
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
  .btn.new_class i {
    margin-right: 8px;
    font-size: 16px;
    color: #fff;
    transition: transform 0.2s ease;
  }

  /* Style the text */
  .btn.new_class .text {
    transition: opacity 0.2s ease, transform 0.2s ease;
  }

  /* Hover effect */
  .btn.new_class:hover {
    transform: scale(1.05); /* Slight enlargement */
  }

  /* When hovered, the icon moves to the center */
  .btn.new_class:hover i {
    transform: translateX(30px); /* Move the icon to the center */
  }

  /* Hide the text on hover */
  .btn.new_class:hover .text {
    opacity: 0; /* Hide the text */
  }

  /* Active effect (on click) */
  .btn.new_class:active {
    transform: scale(1); /* Reset size on click */
  }

/* General button styles */
.btn.manage_class, .btn.delete_class {
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

/* Tooltip styles */
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

/* Tooltip arrow */
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

/* Tooltip show effect */
.btn.manage_class:hover .edit-tooltip,
.btn.delete_class:hover .edit-tooltip {
  top: -45px;
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}

/* Icon size */
.edit-icon {
  font-size: 20px;
}

/* Button hover effects for manage_class */
.btn.manage_class:hover,
.btn.manage_class:hover .edit-tooltip,
.btn.manage_class:hover .edit-tooltip::before {
  background: linear-gradient(320deg, rgb(3, 77, 146), rgb(0, 60, 255));
  color: #ffffff;
}

/* Button hover effects for delete_class */
.btn.delete_class:hover,
.btn.delete_class:hover .edit-tooltip,
.btn.delete_class:hover .edit-tooltip::before {
  background: linear-gradient(320deg, rgb(246, 68, 68), rgb(255, 0, 0));
  color: #ffffff;
}

/* Custom popup design */
.custom-popup {
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Custom title style */
.custom-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #dc2626; /* Red color for delete */
}

/* Custom cancel button style */
.custom-cancel-btn {
  background-color: gray !important;
  color: #333;
  font-size: 1rem;
  padding: 0.75rem 1.5rem;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.custom-cancel-btn:hover {
  background-color: #c0c0c0;
  border-color: #aaa;
}

/* Custom confirm button style */
.custom-confirm-btn {
  background-color: #dc2626; /* Red for delete */
  color: #fff;
  font-size: 1rem;
  padding: 0.75rem 1.5rem;
  border-radius: 5px;
  border: none;
}

.custom-confirm-btn:hover {
  background-color: #991b1b;
}

/* Custom icon style */
.custom-icon {
  background-color: #fee2e2;
  color: #dc2626;
}

/* Custom content style */
.custom-content {
  color: black;
}


</style>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<div class="card-tools">
			<a class="btn new_class" href="javascript:void(0)">
  <i class="fa fa-plus"></i>
  <span class="text">Add New</span>
</a>
			</div>
		</div>
		<div class="card-body">
			<table class="table tabe-hover table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="60%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Class</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT *,concat(curriculum,' ',level,'-',section) as `class` FROM class_list order by class asc ");
					while($row= $qry->fetch_assoc()):
					?>
					<tr>
						<th class="text-center"><?php echo $i++ ?></th>
						<td><b><?php echo $row['class'] ?></b></td>
						<td class="text-center">
		                    <div class="btn-group">
							<a href="javascript:void(0)" data-id='<?php echo $row['id']; ?>' class="btn manage_class">
  <span class="edit-tooltip">Edit Class</span>
  <span class="edit-icon"><i class="fas fa-edit"></i></span>
</a>
<button type="button" class="btn delete_class" data-id="<?php echo $row['id']; ?>">
  <span class="edit-tooltip">Delete Class</span>
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
  $('#list').dataTable();

  // New class click handler
  $('.new_class').click(function(){
    uni_modal("New class", "<?php echo $_SESSION['login_view_folder'] ?>manage_class.php");
  });

  // Manage class click handler
  $('.manage_class').click(function(){
    uni_modal("Manage class", "<?php echo $_SESSION['login_view_folder'] ?>manage_class.php?id=" + $(this).attr('data-id'));
  });

  // Delete class click handler
  $('.delete_class').click(function() {
    const classId = $(this).attr('data-id');

    Swal.fire({
      title: 'DELETE CLASS',
      text: "Are you sure to delete this class?",
      icon: 'error',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Delete',
      cancelButtonText: 'Cancel',
      background: '#f2f2f2',
      color: '#333',
      padding: '1.5em',
      width: '400px',
      customClass: {
        popup: 'custom-popup',
        title: 'custom-title',
        cancelButton: 'custom-cancel-btn',
        confirmButton: 'custom-confirm-btn',
        icon: 'custom-icon',
        content: 'custom-content'
      },
      // Trigger the delete_class function if confirmed
      preConfirm: function() {
        delete_class(classId);
      }
    });
  });

  // Function to delete the class
  function delete_class(id) {
    start_load();
    $.ajax({
      url: 'ajax.php?action=delete_class',
      method: 'POST',
      data: {id: id},
      success: function(resp) {
        if (resp == 1) {
          alert_toast("Data successfully deleted", 'success');
          setTimeout(function() {
            location.reload();
          }, 1500);
        }
      }
    });
  }
});

</script>