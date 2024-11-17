<?php include'db_connect.php' ?>

<!-- Add this CSS either in your stylesheet or within a <style> tag -->
<style>
    .counter {
        color: black;
    }
</style>

<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_faculty">
                    <i class="fa fa-plus"></i> Add New Teacher
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered" id="list">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>School ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th> <!-- New Column -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    // Modify the query to join with department_list
                    $qry = $conn->query("SELECT f.*, CONCAT(f.firstname, ' ', f.lastname) AS name, d.name AS department_name 
                                         FROM faculty_list f 
                                         LEFT JOIN department_list d ON f.department_id = d.id 
                                         ORDER BY name ASC");
                    while($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <!-- Applied the 'counter' class instead of inline style -->
                        <td class="text-center counter"><?php echo $i++ ?></td>
                        <td><b><?php echo htmlspecialchars($row['school_id']) ?></b></td>
                        <td><b><?php echo htmlspecialchars(ucwords($row['name'])) ?></b></td>
                        <td><b><?php echo htmlspecialchars($row['email']) ?></b></td>
                        <td><b><?php echo htmlspecialchars($row['department_name']) ?></b></td> <!-- Display Department -->
                        <td class="text-center">
                            <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                              Action
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item view_faculty" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="./index.php?page=edit_faculty&id=<?php echo $row['id'] ?>">Edit</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item delete_faculty" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                            </div>
                        </td>
                    </tr>   
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JavaScript Section -->
<script>
    $(document).ready(function(){
        $('.view_faculty').click(function(){
            uni_modal("<i class='fa fa-id-card'></i> Faculty Details","<?php echo $_SESSION['login_view_folder'] ?>view_faculty.php?id="+$(this).attr('data-id'))
        });
        $('.delete_faculty').click(function(){
            _conf("Are you sure to delete this faculty?","delete_faculty",[$(this).attr('data-id')]);
        });
        $('#list').DataTable(); // Correct method name and ensure DataTables is properly loaded
    });

    function delete_faculty($id){
        start_load();
        $.ajax({
            url:'ajax.php?action=delete_faculty',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted",'success');
                    setTimeout(function(){
                        location.reload();
                    },1500);
                }
            }
        });
    }
</script>
