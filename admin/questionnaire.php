<?php include 'db_connect.php'; ?>

<style>
  /* Define a class to set text color to black */
  .black-text {
    color: black;
  }

  /* (Optional) Retain or add other existing styles here */

  /* Correcting any incomplete CSS declarations */
  .col-lg-12 {
    /* Ensure all CSS properties are properly defined */
    /* Add desired background properties if needed */
  }
</style>

<div class="col-lg-12">
  <div class="card card-outline card-primary">
    <div class="card-header">
      <div class="card-tools">
        <!-- Changed class from 'new_academic' to 'new_academic' for consistency -->
        <a class="btn btn-block btn-sm btn-default btn-flat border-primary new_academic" href="javascript:void(0)">
          <i class="fa fa-plus"></i> Add New
        </a>
      </div>
    </div>
    <div class="card-body">
      <!-- Corrected class name from 'tabe-hover' to 'table-hover' -->
      <table class="table table-hover table-bordered" id="list">
        <colgroup>
          <col width="5%">
          <col width="35%">
          <col width="15%">
          <col width="15%">
          <col width="15%">
          <col width="15%">
        </colgroup>
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th>Academic Year</th>
            <th>Semester</th>
            <th>Questions</th>
            <th>Answered</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1;
          // Fetch academic entries ordered by year and semester descending
          $qry = $conn->query("SELECT * FROM academic_list ORDER BY ABS(year) DESC, ABS(semester) DESC");
          while($row = $qry->fetch_assoc()):
            // Count related questions and answers
            $questions = $conn->query("SELECT * FROM question_list WHERE academic_id = {$row['id']}")->num_rows;
            $answers = $conn->query("SELECT * FROM evaluation_list WHERE academic_id = {$row['id']}")->num_rows;
          ?>
          <tr>
            <!-- Changed from <th> to <td> and added 'black-text' class -->
            <td class="text-center black-text"><?php echo $i++; ?></td>
            <td><b><?php echo htmlspecialchars($row['year']); ?></b></td>
            <td><b><?php echo htmlspecialchars($row['semester']); ?></b></td>
            <!-- Added 'black-text' class to these columns -->
            <td class="text-center black-text"><b><?php echo number_format($questions); ?></b></td>
            <td class="text-center black-text"><b><?php echo number_format($answers); ?></b></td>
            <td class="text-center">
              <div class="btn-group">
                <!-- 'Action' column remains untouched as per your request -->
                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                  Action
                </button>
                <div class="dropdown-menu">
                  <a class="dropdown-item manage_questionnaire" href="index.php?page=manage_questionnaire&id=<?php echo $row['id']; ?>">Manage</a>
                  <!-- Add more dropdown items here if needed -->
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
    
    // New academic click handler
    $('.new_academic').click(function(){
      uni_modal("New Academic Year", "<?php echo $_SESSION['login_view_folder']; ?>manage_academic.php");
    });
    
    // Manage academic click handler
    $('.manage_academic').click(function(){
      var academicId = $(this).data('id');
      uni_modal("Manage Academic Year", "<?php echo $_SESSION['login_view_folder']; ?>manage_academic.php?id=" + academicId);
    });
    
    // Delete academic click handler
    $('.delete_academic').click(function(){
      var academicId = $(this).data('id');
      
      Swal.fire({
        title: 'DELETE ACADEMIC YEAR',
        text: "Are you sure you want to delete this academic year?",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626', // Red color for delete
        cancelButtonColor: '#6c757d', // Gray color for cancel
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
        }
      }).then((result) => {
        if (result.isConfirmed) {
          delete_academic(academicId); 
        }
      });
    });
    
    // Make default academic click handler
    $('.make_default').click(function(){
      var academicId = $(this).data('id');
      
      Swal.fire({
        title: 'MAKE DEFAULT ACADEMIC YEAR',
        text: "Are you sure you want to make this academic year the system default?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6', // Blue color for confirm
        cancelButtonColor: '#d33', // Red color for cancel
        confirmButtonText: 'Yes, make default',
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
        }
      }).then((result) => {
        if (result.isConfirmed) {
          make_default(academicId); 
        }
      });
    });
  });
  
  // Function to delete the academic year
  function delete_academic(id){
    start_load();
    $.ajax({
      url: 'ajax.php?action=delete_academic',
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
            text: 'An error occurred while deleting the academic year.',
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
  
  // Function to make the academic year default
  function make_default(id){
    start_load();
    $.ajax({
      url: 'ajax.php?action=make_default',
      method: 'POST',
      data: {id: id},
      success: function(resp){
        if(resp == 1){
          alert_toast("Default Academic Year Updated", 'success');
          setTimeout(function(){
            location.reload();
          }, 1500);
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: 'An error occurred while updating the default academic year.',
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
