<?php include 'db_connect.php'; ?>

<style>
/* Button base style */
.btn.new_department {
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
.btn.new_department i {
  margin-right: 8px;
  font-size: 16px;
  color: #fff;
  transition: transform 0.2s ease;
}

/* Style the text */
.btn.new_department .text {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

/* Hover effect */
.btn.new_department:hover {
  transform: scale(1.05); /* Slight enlargement */
}

/* When hovered, the icon moves to the center */
.btn.new_department:hover i {
  transform: translateX(30px); /* Move the icon to the center */
}

/* Hide the text on hover */
.btn.new_department:hover .text {
  opacity: 0; /* Hide the text */
}

/* Active effect (on click) */
.btn.new_department:active {
  transform: scale(1); /* Reset size on click */
}

/* General button styles */
.btn.manage_department, .btn.delete_department {
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
  color: #333; /* Changed to dark color for visibility */
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
.btn.manage_department:hover .edit-tooltip,
.btn.delete_department:hover .edit-tooltip {
  top: -45px;
  opacity: 1;
  visibility: visible;
  pointer-events: auto;
}

/* Icon size */
.edit-icon {
  font-size: 20px;
}

/* Button hover effects for manage_department */
.btn.manage_department:hover,
.btn.manage_department:hover .edit-tooltip,
.btn.manage_department:hover .edit-tooltip::before {
  background: linear-gradient(320deg, rgb(3, 77, 146), rgb(0, 60, 255));
  color: #ffffff;
}

/* Button hover effects for delete_department */
.btn.delete_department:hover,
.btn.delete_department:hover .edit-tooltip,
.btn.delete_department:hover .edit-tooltip::before {
  background: linear-gradient(320deg, rgb(246, 68, 68), rgb(255, 0, 0));
  color: #ffffff;
}


</style>

<div class="col-lg-12">
  <div class="card card-outline card-success">
    <div class="card-header">
      <div class="card-tools">
        <a class="btn btn-block btn-sm btn-default btn-flat border-primary new_department" href="javascript:void(0)">
          <i class="fa fa-plus"></i> Add New Department
        </a>
      </div>
    </div>
    <div class="card-body">
      <!-- Corrected class name from 'tabe-hover' to 'table-hover' -->
      <table class="table table-hover table-bordered" id="list">
        <thead>
          <tr>
            <th class="text-center" width="50px">#</th>
            <th>Department Name</th>
            <th>Description</th>
            <th width="150px">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1;
          $qry = $conn->query("SELECT * FROM department_list ORDER BY name ASC");
          while($row = $qry->fetch_assoc()):
          ?>
          <tr>
            <!-- Changed from <th> to <td> and added 'black-text' class -->
            <td class="text-center black-text"><?php echo $i++; ?></td>
            <td><b><?php echo htmlspecialchars(ucwords($row['name'])); ?></b></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td class="text-center">
            <div class="btn-group">
              <a href="index.php?page=new_department&id=<?php echo $row['id']; ?>" class="btn manage_department">
                <span class="edit-tooltip">Manage Department</span>
                <span class="edit-icon"><i class="fas fa-edit"></i></span>
              </a>
          <button type="button" class="btn delete_department" data-id="<?php echo $row['id']; ?>">
            <span class="edit-tooltip">Delete Department</span>
            <span class="edit-icon"><i class="fas fa-trash"></i></span>
          </button>
            </td>
          </div>
          </tr>   
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    // Initialize DataTables with correct method name and proper capitalization
    $('#list').DataTable();

    // New department click handler
    $('.new_department').click(function(){
      uni_modal("Add New Department", "<?php echo $_SESSION['login_view_folder']; ?>manage_department.php");
    });

    // Delete department click handler
    $('.delete_department').click(function(){
      var departmentId = $(this).data('id');
      _conf("Are you sure you want to delete this department?", "delete_department", [departmentId]);
    });
  });

  // Function to delete the department
  function delete_department(id){
    start_load();
    $.ajax({
      url: 'ajax.php?action=delete_department',
      method: 'POST',
      data: {id: id},
      success: function(resp){
        if(resp == 1){
          alert_toast("Data successfully deleted", 'success');
          setTimeout(function(){
            location.reload();
          }, 1500);
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Deletion Failed',
            text: 'An error occurred while deleting the department.',
          });
          end_load();
        }
      },
      error: function(){
        Swal.fire({
          icon: 'error',
          title: 'AJAX Error',
          text: 'Failed to communicate with the server.',
        });
        end_load();
      }
    });
  }
</script>
