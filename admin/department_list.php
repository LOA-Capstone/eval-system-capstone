<?php include 'db_connect.php'; ?>

<style>
  /* Define a class to set text color to black */
  .black-text {
    color: black;
  }

  /* (Optional) Retain or add other existing styles here */
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
              <!-- 'Action' column remains untouched as per your request -->
              <a href="index.php?page=new_department&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
              <button type="button" class="btn btn-sm btn-danger delete_department" data-id="<?php echo $row['id']; ?>">Delete</button>
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
