<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection settings
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = "";     // Replace with your database password
$dbname = "evaluation_db";   // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4"); // Set character set

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing students for display
$i = 1;
$class = array();
$classes = $conn->query("SELECT id,concat(curriculum,' ',level,' - ',section) as `class` FROM class_list");
while($row = $classes->fetch_assoc()){
    $class[$row['id']] = $row['class'];
}
$qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM student_list ORDER BY concat(firstname,' ',lastname) ASC");
$studentList = [];
while($row = $qry->fetch_assoc()){
    $studentList[] = $row;
}

?>

<style>
        .counter {
        color: black;
    }
  /* Button base style */
  .btn.new_academic {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: bold;
    color: white;
    background-color: #28a745; /* Green color */
    border: none;
    border-radius: 5px;
    text-decoration: none;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease, background-color 0.2s ease;
    position: relative;
    overflow: hidden;
  }

  /* Style the icon */
  .btn.new_academic i {
    margin-right: 8px;
    font-size: 16px;
    color: #fff;
    transition: transform 0.2s ease;
  }

  /* Style the text */
  .btn.new_academic .text {
    transition: opacity 0.2s ease, transform 0.2s ease;
  }

  /* Hover effect */
  .btn.new_academic:hover {
    transform: scale(1.05); /* Slight enlargement */
  }

  /* When hovered, the icon moves to the center */
  .btn.new_academic:hover i {
    transform: translateX(55px); /* Move the icon to the center */
  }

  /* Hide the text on hover */
  .btn.new_academic:hover .text {
    opacity: 0; /* Hide the text */
  }

  /* Active effect (on click) */
  .btn.new_academic:active {
    transform: scale(1); /* Reset size on click */
  }
</style>

<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn new_academic" href="./index.php?page=new_student">
                    <i class="fa fa-plus"></i>
                    <span class="text">Add New Student</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Wrap the table in a div for horizontal scrolling -->
            <div class="table-container">
                <table class="table tabe-hover table-bordered" id="list">
                    <thead>
                        <tr>
                            <th class="text-center counter">#</th>
                            <th>School ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Class</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($studentList as $student): ?>
                        <tr>
                            <th class="text-center counter"><?php echo $i++; ?></th>
                            <td><b><?php echo htmlspecialchars($student['school_id'] ?? ''); ?></b></td>
                            <td><b><?php echo htmlspecialchars(ucwords($student['name'] ?? '')); ?></b></td>
                            <td><b><?php echo htmlspecialchars($student['email'] ?? ''); ?></b></td>
                            <td><b><?php echo htmlspecialchars(isset($class[$student['class_id']]) ? $class[$student['class_id']] : "N/A"); ?></b></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    Action
                                </button>
                                <div class="dropdown-menu" style="">
                                    <a class="dropdown-item view_student" href="javascript:void(0)" data-id="<?php echo $student['id']; ?>">View</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="./index.php?page=edit_student&id=<?php echo $student['id']; ?>">Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_student" href="javascript:void(0)" data-id="<?php echo $student['id']; ?>">Delete</a>
                                </div>
                            </td>
                        </tr>   
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.view_student').click(function(){
        uni_modal("<i class='fa fa-id-card'></i> Student Details","<?php echo $_SESSION['login_view_folder']; ?>view_student.php?id="+$(this).attr('data-id'));
    });
    $('.delete_student').click(function(){
        _conf("Are you sure to delete this student?","delete_student",[$(this).attr('data-id')]);
    });
    $('#list').dataTable();
});
function delete_student(id){
    start_load();
    $.ajax({
        url:'ajax.php?action=delete_student',
        method:'POST',
        data:{id:id},
        success:function(resp){
            if(resp==1){
                alert_toast("Data successfully deleted",'success');
                setTimeout(function(){
                    location.reload();
                },1500);
            }
        }
    });
}
</script>
