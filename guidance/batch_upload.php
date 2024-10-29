<?php include 'db_connect.php'; ?>
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title">Batch Upload</h5>
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary new_batch_upload" href="javascript:void(0)">
                    <i class="fa fa-plus"></i> Upload File
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered" id="batch-upload-list">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>File Name</th>
                        <th>Upload Date</th>
                        <th>Uploaded By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $qry = $conn->query("SELECT * FROM batch_uploads ORDER BY upload_date DESC");
                    while ($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?php echo $row['file_name']; ?></td>
                        <td><?php echo $row['upload_date']; ?></td>
                        <td><?php echo $row['uploaded_by']; ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-flat border-info text-info view_file" data-id="<?php echo $row['id']; ?>">
                                View
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.new_batch_upload').click(function() {
            uni_modal("Upload New File", "<?php echo $_SESSION['login_view_folder']; ?>manage_batch_upload.php");
        });

        $('#batch-upload-list').dataTable();
    });
</script>
