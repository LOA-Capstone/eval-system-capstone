<?php include 'db_connect.php'; ?>
<form action="ajax.php?action=upload_batch" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="file">Select File</label>
        <input type="file" name="file" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
</form>
