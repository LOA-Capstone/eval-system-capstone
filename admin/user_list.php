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
        <!-- Changed href to javascript:void(0) for consistency with previous modifications -->
        <a class="btn btn-block btn-sm btn-default btn-flat border-primary new_user" href="javascript:void(0)">
          <i class="fa fa-plus"></i> Add New User
        </a>
      </div>
    </div>
    <div class="card-body">
      <!-- Corrected class name from 'tabe-hover' to 'table-hover' -->
      <table class="table table-hover table-bordered" id="list">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th>Name</th>
            <th>Email</th>
            <th>User Type</th> <!-- New Column -->
            <th>Department</th> <!-- New Column -->
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1;
          // Modified query to fetch user type and department name
          $qry = $conn->query("
            SELECT u.*, 
                   CONCAT(u.firstname, ' ', u.lastname) AS name, 
                   d.name AS department_name
            FROM users u
            LEFT JOIN department_list d ON u.department_id = d.id
            ORDER BY CONCAT(u.firstname, ' ', u.lastname) ASC
          ");
          while ($row = $qry->fetch_assoc()):
              // Determine user type and corresponding department
              if ($row['type'] == 1) {
                  $user_type = 'Admin';
                  $department = 'Admin'; // For Admins, department column shows 'Admin'
              } elseif ($row['type'] == 2) {
                  $user_type = 'Dean';
                  $department = htmlspecialchars($row['department_name']);
              } else {
                  $user_type = 'Unknown';
                  $department = 'N/A';
              }
          ?>
          <tr>
            <!-- Changed from <th> to <td> and added 'black-text' class -->
            <td class="text-center black-text"><?php echo $i++; ?></td>
            <td><b><?php echo htmlspecialchars(ucwords($row['name'])); ?></b></td>
            <td><b><?php echo htmlspecialchars($row['email']); ?></b></td>
            <!-- Added 'black-text' class to these columns -->
            <td><b class="black-text"><?php echo $user_type; ?></b></td>
            <td><b class="black-text"><?php echo $department; ?></b></td>
            <td class="text-center">
              <div class="btn-group">
                <!-- 'Action' column remains untouched as per your request -->
                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                  Action
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">View</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="./index.php?page=edit_user&id=<?php echo $row['id']; ?>">Edit</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">Delete</a>
                </div>
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
    // Initialize DataTables with correct method name and proper capitalization
    $('#list').DataTable();

    // New user click handler
    $('.new_user').click(function(){
      uni_modal("Add New User", "<?php echo $_SESSION['login_view_folder']; ?>manage_user.php");
    });

    // View user click handler
    $('.view_user').click(function(){
      var userId = $(this).data('id');
      uni_modal("<i class='fa fa-id-card'></i> User Details", "view_user.php?id=" + userId);
    });

    // Delete user click handler
    $('.delete_user').click(function(){
      var userId = $(this).data('id');
      _conf("Are you sure you want to delete this user?", "delete_user", [userId]);
    });
  });

  // Function to delete the user
  function delete_user(id){
    start_load();
    $.ajax({
      url: 'ajax.php?action=delete_user',
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
            text: 'An error occurred while deleting the user.',
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
