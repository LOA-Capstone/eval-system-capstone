<?php include('db_connect.php');
function ordinal_suffix1($num){
    $num = $num % 100; // protect against large numbers
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}
$astat = array("Not Yet Started","Started","Closed");
?>


<div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4>Welcome <?php echo $_SESSION['login_name']; ?>!</h4>
        <br>
        <div class="col-md-5">
          <?php if (isset($_SESSION['academic'])): ?>
            <div class="callout callout-info">
              <h5><b>Academic Year: <?php echo $_SESSION['academic']['year'].' '.ordinal_suffix1($_SESSION['academic']['semester']); ?> Semester</b></h5>
              <h6><b>Evaluation Status: <?php echo $astat[$_SESSION['academic']['status']]; ?></b></h6>
              <h6><b>Term: <?php echo $_SESSION['academic']['term']; ?></b></h6>
            </div>
          <?php else: ?>
            <h5>No academic data available.</h5>
          <?php endif; ?>
        </div>
      </div>
    </div>
</div>