<?php
// Include database connection
include 'db_connect.php';

// If editing, fetch department details
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM department_list WHERE id = {$_GET['id']}");
    if ($qry->num_rows > 0) {
        $res = $qry->fetch_assoc();
        foreach ($res as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <form action="" id="manage_department">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <!-- Department Name -->
                <div class="form-group">
                    <label for="name" class="control-label">Department Name</label>
                    <input type="text" name="name" class="form-control form-control-sm" required value="<?php echo isset($name) ? $name : '' ?>">
                </div>
                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="control-label">Description</label>
                    <textarea name="description" rows="4" class="form-control form-control-sm"><?php echo isset($description) ? $description : '' ?></textarea>
                </div>
                <!-- Submit Buttons -->
                <div class="text-right">
                    <button class="btn btn-primary mr-2">Save</button>
                    <button class="btn btn-secondary" type="button" onclick="location.href = 'index.php?page=department_list'">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#manage_department').submit(function(e) {
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_department',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully saved.", 'success');
                    setTimeout(function() {
                        location.href = 'index.php?page=department_list';
                    }, 1000);
                } else if (resp == 2) {
                    alert_toast("Department already exists.", 'danger');
                }
            }
        });
    });
</script>
