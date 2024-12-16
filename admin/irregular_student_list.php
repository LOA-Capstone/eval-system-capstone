<?php
$irregular_students = $conn->query("SELECT * FROM student_list WHERE classification='irregular' ORDER BY lastname, firstname ASC");
?>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>School ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date Created</th>
                        <th>Avatar</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i=1;
                    while($row = $irregular_students->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $i++ ?></td>
                        <td><?php echo $row['school_id'] ?></td>
                        <td><?php echo ucwords($row['lastname'].", ".$row['firstname']) ?></td>
                        <td><?php echo $row['email'] ?></td>
                        <td><?php echo $row['date_created'] ?></td>
                        <td><img src="assets/uploads/<?php echo $row['avatar'] ?>" alt="Avatar" width="50" height="50"></td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="index.php?page=new_irregular_student&id=<?php echo $row['id'] ?>">Edit</a>
                            <a class="btn btn-sm btn-danger delete_student" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$('.delete_student').click(function(){
    _conf("Are you sure to delete this student?","delete_student",[$(this).attr('data-id')])
})
function delete_student($id){
    start_load()
    $.ajax({
        url:'ajax.php?action=delete_student',
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
