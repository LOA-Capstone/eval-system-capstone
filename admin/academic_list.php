<?php include 'db_connect.php'; ?>
<style>
.btn.new_academic {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: bold;
    color: white;
    background-color: #28a745;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
    cursor: pointer;
}

.btn.new_academic i {
    margin-right: 8px;
    font-size: 16px;
}

.btn.new_academic:hover {
    transform: scale(1.05);
}

.btn.manage_academic, .btn.delete_academic {
    position: relative;
    background: rgb(177, 228, 255);
    color: #000;
    padding: 15px;
    border-radius: 10px;
    width: 40px;
    height: 40px;
    font-size: 17px;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: all 0.2s ease;
}

.edit-tooltip {
    position: absolute;
    top: -45px;
    background: #ffffff;
    color: #000;
    padding: 5px 8px;
    border-radius: 5px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
    font-size: 14px;
}

.btn:hover .edit-tooltip {
    opacity: 1;
}

/* SweetAlert Custom Styles */
.custom-popup {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.custom-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: red;
}

.custom-cancel-btn {
    background-color: gray !important;
    color: #333;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.custom-confirm-btn {
    background-color: #b91c1c;
    color: #fff;
    border-radius: 5px;
}

.custom-confirm-btn.make_default {
    background-color: #10b981;
}
</style>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn new_academic" href="javascript:void(0)">
                    <i class="fa fa-plus"></i> Add New
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="20%">
                    <col width="20%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Term</th>
                        <th>System Default</th>
                        <th>Evaluation Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $qry = $conn->query("SELECT * FROM academic_list ORDER BY ABS(year) DESC, ABS(semester) DESC");
                    while ($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <th class="text-center"><?php echo $i++ ?></th>
                        <td><b><?php echo $row['year'] ?></b></td>
                        <td><b><?php echo $row['semester'] ?></b></td>
                        <td><b><?php echo $row['term'] ?></b></td> <!-- New Term Column -->
                        <td class="text-center">
                            <?php if ($row['is_default'] == 0): ?>
                                <button type="button" class="btn btn-secondary make_default" data-id="<?php echo $row['id'] ?>">Inactive</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary">Active</button>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($row['status'] == 0): ?>
                                <span class="badge badge-secondary">Not yet Started</span>
                            <?php elseif ($row['status'] == 1): ?>
                                <span class="badge badge-success">Starting</span>
                            <?php elseif ($row['status'] == 2): ?>
                                <span class="badge badge-primary">Closed</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="javascript:void(0)" data-id="<?php echo $row['id']; ?>" class="btn manage_academic">
                                    <span class="edit-tooltip">Edit Academic</span><i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn delete_academic" data-id="<?php echo $row['id']; ?>">
                                    <span class="edit-tooltip">Delete Academic</span><i class="fas fa-trash"></i>
                                </button>
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
$(document).ready(function() {
    $('.new_academic').click(function() {
        uni_modal("New Academic", "<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php");
    });

    $('.manage_academic').click(function() {
        uni_modal("Manage Academic", "<?php echo $_SESSION['login_view_folder'] ?>manage_academic.php?id=" + $(this).data('id'));
    });

    $('.delete_academic').click(function() {
        _conf("Are you sure you want to delete this academic year?", "delete_academic", [$(this).data('id')], 'error', 'DELETE');
    });

    $('.make_default').click(function() {
        _conf("Are you sure to set this as the default academic year?", "make_default", [$(this).data('id')], 'info', 'CONFIRM');
    });

    function _conf(message, type, data, iconType, confirmText) {
        Swal.fire({
            title: type === 'delete_academic' ? 'DELETE ACADEMIC' : 'MAKE DEFAULT',
            text: message,
            icon: iconType,
            showCancelButton: true,
            confirmButtonColor: iconType === 'error' ? '#b91c1c' : '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            customClass: {
                popup: 'custom-popup',
                title: 'custom-title',
                confirmButton: 'custom-confirm-btn',
                cancelButton: 'custom-cancel-btn'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (type === 'delete_academic') delete_academic(data[0]);
                else make_default(data[0]);
            }
        });
    }

    function delete_academic(id) {
        $.post('ajax.php?action=delete_academic', { id: id }, function(resp) {
            if (resp == 1) location.reload();
        });
    }

    function make_default(id) {
        $.post('ajax.php?action=make_default', { id: id }, function(resp) {
            if (resp == 1) location.reload();
        });
    }

    $('#list').dataTable();
});
</script>
