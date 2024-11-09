<?php include 'db_connect.php'; ?>
<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
            </div>
        </div>
        <div class="card-body">
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
                    $qry = $conn->query("SELECT u.*, CONCAT(u.firstname, ' ', u.lastname) AS name, d.name AS department_name
                                         FROM users u
                                         LEFT JOIN department_list d ON u.department_id = d.id
                                         ORDER BY CONCAT(u.firstname, ' ', u.lastname) ASC");
                    while ($row = $qry->fetch_assoc()):
                        $user_type = '';
                        if ($row['type'] == 1) {
                            $user_type = 'Admin';
                            $department = 'Admin'; // For Admins, department column shows 'Admin'
                        } elseif ($row['type'] == 2) {
                            $user_type = 'Dean';
                            $department = $row['department_name'];
                        } else {
                            $user_type = 'Unknown';
                            $department = 'N/A';
                        }
                    ?>
                    <tr>
                        <th class="text-center"><?php echo $i++; ?></th>
                        <td><b><?php echo ucwords($row['name']); ?></b></td>
                        <td><b><?php echo $row['email']; ?></b></td>
                        <td><b><?php echo $user_type; ?></b></td> <!-- Display User Type -->
                        <td><b><?php echo $department; ?></b></td> <!-- Display Department -->
                        <td class="text-center">
                            <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                              Action
                            </button>
                            <div class="dropdown-menu" style="">
                              <a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">View</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item" href="./index.php?page=edit_user&id=<?php echo $row['id']; ?>">Edit</a>
                              <div class="dropdown-divider"></div>
                              <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="<?php echo $row['id']; ?>">Delete</a>
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
        $('.view_user').click(function(){
            uni_modal("<i class='fa fa-id-card'></i> User Details","view_user.php?id="+$(this).attr('data-id'))
        })
        $('.delete_user').click(function(){
            _conf("Are you sure to delete this user?","delete_user",[$(this).attr('data-id')])
        })
        $('#list').dataTable()
    })
    function delete_user(id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_user',
            method:'POST',
            data:{id:id},
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
