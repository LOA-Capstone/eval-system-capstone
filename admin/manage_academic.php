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

/* Style for the Status dropdown */
.status-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 100%;
    height: 50px;
    border-radius: 15px;
    padding: 10px 15px;
    outline: none;
    background-color: transparent;
    border: 2px solid yellow;
    color: white;
    font-size: 16px;
    transition: all 0.3s;
    cursor: pointer;
}

/* Hover and focus effects for the Status dropdown */
.status-select:hover,
.status-select:focus {
    border-color: yellow;
}

/* Style for the options inside the Status dropdown */
.status-select option {
    background-color: #1C204B; 
    color: white;
    padding: 10px;
    font-size: 16px;
    border: none;
}

/* Hover effect for options */
.status-select option:hover {
    background-color: yellow;
    color: white;
}


/* Remove bottom borders when the select dropdown is focused */
.status-select:focus {
    border-radius: 15px 15px 0 0; /* Remove bottom corners when focused */
}

/* Adjust the label's border when the select is focused */
.status-select:focus + .status-label {
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom: none;
}


.form .arrow {
    font-size: 14px; /* Adjust size */
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(30%);
    color: white;
    pointer-events: none; /* Prevent it from interfering with dropdown */
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
        placeholder="Enter semester"
        required
        min="1"
        max="2"
    />
</label>

    
	<?php if(isset($status)): ?>
    <label for="status" class="label">
        <span class="title">Status</span>
        <select name="status" id="status" class="input-field status-select">
            <option value="0" <?php echo $status == 0 ? "selected" : "" ?>>Pending</option>
            <option value="1" <?php echo $status == 1 ? "selected" : "" ?>>Started</option>
            <option value="2" <?php echo $status == 2 ? "selected" : "" ?>>Closed</option>
        </select>
		<span class="arrow">▼</span>
    </label>
<?php endif; ?>

</form>

</div>
<script>
$(document).ready(function() {
    // Store initial values of the fields
    let initialValues = {};
    $('#manage-academic input[required], #manage-academic textarea[required]').each(function() {
        initialValues[$(this).attr('name')] = $(this).val().trim();
    });

    $('#manage-academic').submit(function(e) {
        e.preventDefault();
        start_load();
        $('#msg').html(''); // Clear any previous error/success messages
        $('#top-msg').html('');  // Clear top messages as well

        let isValid = true;
        let noChangesMade = true; // Flag to track if any changes were made

        // Check if any required fields are empty
        $('#manage-academic input[required], #manage-academic textarea[required]').each(function() {
            const fieldName = $(this).attr('name');
            const currentValue = $(this).val().trim();

            // If the current value is empty, it's invalid
            if (currentValue === '') {
                isValid = false;
                $(this).addClass('is-invalid');
                const errorMessage = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> All fields are required.</div>';
                $('#msg').html(errorMessage);  // Bottom message only
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
            const noChangesMessage = '<div class="alert alert-warning"><i class="fa fa-info-circle"></i> No changes were made.</div>';
            $('#msg').html(noChangesMessage);  // Bottom message only
            end_load();
            return; // Stop further execution if no changes
        }

        // Proceed with AJAX request if validation passes and changes were made
        $.ajax({
            url: 'ajax.php?action=save_academic',
            method: 'POST',
            data: $(this).serialize(),
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully saved.", "success");
                    setTimeout(function() {
                        location.reload();
                    }, 1750);
                } else if (resp == 2) {
                    const duplicateMessage = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Academic Code already exists.</div>';
                    $('#msg').html(duplicateMessage);  // Bottom message only
                    end_load();
                }
            }
        });
    });
});

	document.addEventListener('DOMContentLoaded', function () {
        const semesterInput = document.getElementById('semester');
        
        // Event listener for input changes
        semesterInput.addEventListener('input', function (e) {
            let value = parseInt(e.target.value);
            
            // Check if the value is valid (1 or 2)
            if (value < 1) {
                e.target.value = 1;
            } else if (value > 2) {
                e.target.value = 2;
            }
        });

        // Optional: Validate the input when the user tries to submit or change focus
        semesterInput.addEventListener('change', function (e) {
            let value = parseInt(e.target.value);
            
            if (value < 1 || value > 2) {
                alert("Please enter a valid semester (1 or 2).");
                e.target.value = ''; // Reset the value if invalid
            }
        });
    });
</script>