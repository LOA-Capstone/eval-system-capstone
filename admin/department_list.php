<?php include'db_connect.php' ?>
<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_department"><i class="fa fa-plus"></i> Add New Department</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table tabe-hover table-bordered" id="list">
                <thead>
                    <tr>
                        <th class="text-center counter" width="50px">#</th>
                        <th>Department Name</th>
                        <th>Description</th>
                        <th width="150px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $qry = $conn->query("SELECT * FROM department_list ORDER BY name ASC");
                    while($row= $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center counter"><?php echo $i++ ?></td>
                        <td><b><?php echo ucwords($row['name']) ?></b></td>
                        <td><?php echo $row['description'] ?></td>
                        <td class="text-center">
                            <a href="index.php?page=new_department&id=<?php echo $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger delete_department" data-id="<?php echo $row['id'] ?>">Delete</button>
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

        $('.delete_department').click(function(){
            _conf("Are you sure to delete this department?", "delete_department", [$(this).attr('data-id')]);
        });
    });

    function delete_department(id){
        start_load();
        $.ajax({
            url:'ajax.php?action=delete_department',
            method:'POST',
            data:{id:id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>
<style> 
            .counter {
        color: black;
    }
</style>