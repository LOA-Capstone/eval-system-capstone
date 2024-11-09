<?php
include '../db_connect.php';
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM subject_list where id={$_GET['id']}")->fetch_array();
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
</style>

<div class="container-fluids">
  <form class="form" id="manage-subject">
    <label for="subject" class="label">
      <span class="title">Subject</span>
      <input
        class="input-field"
        type="text"
        name="subject"
        id="subject"
        value="<?php echo isset($subject) ? $subject : '' ?>"
        placeholder="Enter subject name"
        required
      />
    </label>
    <label for="code" class="label">
      <span class="title">Subject Code</span>
      <input
        class="input-field"
        type="text"
        name="code"
        id="code"
        value="<?php echo isset($code) ? $code : '' ?>"
        placeholder="Enter subject code"
        required
      />
    </label>
    <label for="description" class="label">
      <span class="title">Description</span>
      <textarea
        class="input-field textarea"
        name="description"
        id="description"
        placeholder="Enter description"
        required
      ><?php echo isset($description) ? $description : '' ?></textarea>
    </label>
    <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
    <div id="msg" class="form-group"></div>
  </form>
</div>

<script>
$(document).ready(function() {
    // Store initial values of the fields
    let initialValues = {};
    $('#manage-subject input[required], #manage-subject textarea[required]').each(function() {
        initialValues[$(this).attr('name')] = $(this).val().trim();
    });

    $('#manage-subject').submit(function(e) {
        e.preventDefault();
        start_load();
        $('#msg').html('');

        let isValid = true;
        let noChangesMade = true; // Flag to track if any changes were made

        // Check if any required fields are empty
        $('#manage-subject input[required], #manage-subject textarea[required]').each(function() {
            const fieldName = $(this).attr('name');
            const currentValue = $(this).val().trim();

            // If the current value is empty, it's invalid
            if (currentValue === '') {
                isValid = false;
                $(this).addClass('is-invalid');
                $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> All fields are required.</div>');
            } else {
                $(this).removeClass('is-invalid');
            }

            // Check if the value has changed
            if (currentValue !== initialValues[fieldName]) {
                noChangesMade = false; // If any field has changed, set this to false
            }
        });

        if (!isValid) {
            end_load();
            return; // Stop further execution if validation fails
        }

        // If no changes were made, show the notification
        if (noChangesMade) {
            $('#msg').html('<div class="alert alert-warning"><i class="fa fa-info-circle"></i> No changes were made.</div>');
            end_load();
            return; // Stop further execution if no changes
        }

        // Proceed with AJAX request if validation passes and changes were made
        $.ajax({
            url: 'ajax.php?action=save_subject',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully saved.", "success");
                    setTimeout(function() {
                        location.reload();
                    }, 1750);
                } else if (resp == 2) {
                    $('#msg').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Subject Code already exists.</div>');
                    end_load();
                }
            }
        });
    });
});


	document.querySelectorAll('.input-field').forEach(function(input) {
    input.addEventListener('input', function() {
      let label = this.closest('.label');
      if (this.value.trim() !== "") {
        label.classList.add('filled');
      } else {
        label.classList.remove('filled');
      }
    });
    
    // Trigger input event on page load to check for pre-filled values
    if (input.value.trim() !== "") {
      input.closest('.label').classList.add('filled');
    }
  });

</script>