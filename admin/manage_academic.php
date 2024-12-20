<?php
include '../db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM academic_list where id={$_GET['id']}")->fetch_array();
	foreach($qry as $k => $v){
		$$k = $v;
	}
}
?>
<style>
.form {
  background: #1C204B;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 20px;
  position: relative;
  border-radius: 25px;
}

.form .label {
  display: flex;
  flex-direction: column;
  gap: 5px;
  height: fit-content;
  position: relative;
}

.form .label .title {
  padding: 0 10px;
  transition: all 300ms;
  font-size: 20px;
  color: white;
  font-weight: 700;
  width: fit-content;
  top: 17px;
  position: relative;
  left: 15px;
  background: #1C204B;
}

.form .input-field {
  width: 100%;
  height: 50px;
  text-indent: 15px;
  border-radius: 15px;
  outline: none;
  background-color: transparent;
  border: 1px solid white;
  transition: all 0.3s;
  caret-color: #d17842;
  color: white;
}

.form .input-field:hover {
  border-color: yellow;
}

.form .input-field:focus {
  border-color: yellow;
}

.form .input-field.textarea {
  height: 150px;
  resize: none;
  padding: 15px 0;
}

/* When the input or textarea has content, move the label */
.form .label.filled .title,
.form .label:has(input:focus) .title,
.form .label:has(textarea:focus) .title {
  top: -10px;
  left: 0;
  color: yellow;
}


.form select {
  background-color: transparent;
  color: white;
  border: 1px solid white;
  border-radius: 15px;
  padding: 0 15px;
  height: 50px;
  -webkit-appearance: none; /* Remove default select styling in Webkit browsers */
  -moz-appearance: none; /* Remove default select styling in Firefox */
  transition: all 0.3s ease;
}

.form select:focus {
  border-color: yellow;
}

.form select option {
  background-color: #1C204B;
  color: white;
  border: 2px solid yellow;
}

.form select::-ms-expand {
  display: none; /* Removes the default dropdown arrow in IE */
}

</style>


<div class="container-fluid">
  <form class="form" id="manage-academic">
    <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
    <div id="msg" class="form-group"></div>

    <label for="year" class="label">
      <span class="title">Year</span>
      <input
        class="input-field"
        type="text"
        name="year"
        id="year"
        value="<?php echo isset($year) ? $year : '' ?>"
        placeholder="(2019-2020)"
        required
      />
    </label>
	<label for="semester" class="label">
  <span class="title">Semester</span>
  <input
    class="input-field"
    type="number"
    name="semester"
    id="semester"
    value="<?php echo isset($semester) ? $semester : '' ?>"
    min="1"
    max="2"
    step="1"
    required
    oninput="validateSemester(this)"
  />
</label>


    <?php if (isset($status)): ?>
      <label for="status" class="label">
        <span class="title">Status</span>
        <select name="status" id="status" class="input-field">
          <option value="0" <?php echo $status == 0 ? "selected" : "" ?>>Pending</option>
          <option value="1" <?php echo $status == 1 ? "selected" : "" ?>>Started</option>
          <option value="2" <?php echo $status == 2 ? "selected" : "" ?>>Closed</option>
        </select>
      </label>
    <?php endif; ?>

    <label for="term" class="label">
  <span class="title">Term</span>
  <select name="term" id="term" class="input-field">
    <option value="Midterm" <?php echo isset($term) && $term == "Midterm" ? "selected" : "" ?>>Midterm</option>
    <option value="Finals" <?php echo isset($term) && $term == "Finals" ? "selected" : "" ?>>Finals</option>
  </select>
</label>

  </form>
</div>

<script>
    $(document).ready(function(){
        let initialValues = {};

        // Store initial values of all relevant fields to check for changes
        $('#manage-academic input, #manage-academic select').each(function() {
            initialValues[$(this).attr('name')] = $(this).val().trim();
        });

        $('#manage-academic').submit(function(e){
            e.preventDefault();
            start_load();
            $('#msg').html('');
            let isValid = true;
            let noChangesMade = true;

            // Validate required fields
            $('#manage-academic input[required], #manage-academic select[required]').each(function() {
                const fieldName = $(this).attr('name');
                const currentValue = $(this).val().trim();

                if (currentValue === '') {
                    isValid = false;
                    $(this).addClass('is-invalid');
                    const errorMessage = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> All fields are required.</div>';
                    $('#msg').html(errorMessage);  // Display error message
                } else {
                    $(this).removeClass('is-invalid');
                }

                // Check if any required field has changed
                if (currentValue !== initialValues[fieldName]) {
                    noChangesMade = false;
                }
            });

            // Additionally, check non-required fields for changes
            $('#manage-academic input:not([required]), #manage-academic select:not([required])').each(function() {
                const fieldName = $(this).attr('name');
                const currentValue = $(this).val().trim();

                if (currentValue !== initialValues[fieldName]) {
                    noChangesMade = false;
                }
            });

            if (!isValid) {
                end_load();
                return; // Stop further execution if validation fails
            }

            if (noChangesMade) {
                const noChangesMessage = '<div class="alert alert-warning"><i class="fa fa-info-circle"></i> No changes were made.</div>';
                $('#msg').html(noChangesMessage);  // Display no changes message
                end_load();
                return; // Stop further execution if no changes
            }

            $.ajax({
                url: 'ajax.php?action=save_academic',
                method: 'POST',
                data: $(this).serialize(),
                success: function(resp) {
                    if (resp == 1) {
                        alert_toast("Data successfully saved.", "success");
                        setTimeout(function(){
                            location.reload();
                        }, 1750);
                    } else if (resp == 2) {
                        const duplicateMessage = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Academic year Already exists!</div>';
                        $('#msg').html(duplicateMessage);  // Display duplicate message
                        end_load();
                    }
                }
            });
        });
    });
</script>
