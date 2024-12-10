<?php
include 'db_connect.php';
$department_id = $_SESSION['login_department_id']; // Get Dean's department ID
?>

<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                
            </div>
        </div>
        <div class="card-body">
            <!-- Corrected class name from 'tabe-hover' to 'table-hover' -->
            <table class="table table-hover table-bordered" id="list">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>School ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    // Select faculties only from the Dean's department
                    $qry = $conn->query("
                        SELECT 
                            *, 
                            CONCAT(firstname, ' ', lastname) AS name 
                        FROM 
                            faculty_list 
                        WHERE 
                            department_id = $department_id 
                        ORDER BY 
                            name ASC
                    ");
                    while ($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <!-- Changed from <th> to <td> and set text color to black -->
                        <td class="text-center" style="color: black;"><?php echo $i++; ?></td>
                        <td><b><?php echo htmlspecialchars($row['school_id']); ?></b></td>
                        <td><b><?php echo htmlspecialchars(ucwords($row['name'])); ?></b></td>
                        <td><b><?php echo htmlspecialchars($row['email']); ?></b></td>
                        <td class="text-center">
                            <!-- Actions -->
                            <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                              Action
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item view_faculty" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">View</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="./index.php?page=edit_faculty&id=<?php echo $row['id']; ?>">Edit</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item delete_faculty" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">Delete</a>
                            </div>
                        </td>
                    </tr>   
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Optional: Define a CSS class instead of using inline styles -->
<style>
    .counter {
        color: black; /* Ensures text color is black */
    }
</style>

<script>
    $(document).ready(function(){
        // Initialize DataTables with correct method name
        $('#list').DataTable();

        $('.view_faculty').click(function(){
            uni_modal("<i class='fa fa-id-card'></i> Faculty Details","<?php echo $_SESSION['login_view_folder'] ?>view_faculty.php?id="+$(this).attr('data-id'));
        });

        $('.delete_faculty').click(function(){
            _conf("Are you sure to delete this faculty?", "delete_faculty", [$(this).attr('data-id')]);
        });
    });

    function delete_faculty($id){
        start_load();
        $.ajax({
            url:'ajax.php?action=delete_faculty',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp == 1){
                    alert_toast("Data successfully deleted",'success');
                    setTimeout(function(){
                        location.reload();
                    },1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Deletion Failed',
                        text: 'An error occurred while deleting the faculty.',
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
