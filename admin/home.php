<?php include('db_connect.php'); ?>
<?php 
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
$astat = array("Not Yet Started","On-going","Closed");
 ?>

 <style>
.card {
  display: flex;
  flex-direction: column;
  isolation: isolate;
  position: relative;
  width: auto;
  height: auto; 
  background: radial-gradient(178.94% 106.41% at 26.42% 106.41%, #FFF7B1 0%, #FFFFFF 71.88%);
  border-radius: 1rem;
  overflow: hidden;
  font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
  font-size: 16px;
}

.card:before {
  position: absolute;
  content: "";
  inset: 0.0625rem;
  border-radius: 0.9375rem;
  background: #18181b;
  z-index: 2;
}

.card:after {
  position: absolute;
  content: "";
  width: 0.25rem;
  inset: 0.65rem auto 0.65rem 0.5rem;
  border-radius: 0.125rem;
  background: linear-gradient(to bottom, #2eadff, #3d83ff, #7e61ff); /* Gradient for the border */
  transition: transform 300ms ease;
  z-index: 4;
}

.card:hover:after {
  transform: translateX(0.15rem);
}

.card-body {
  display: flex;
  flex-direction: column; 
  padding: 1rem; 
}
.card-body h5, .card-body h6 {
  color: black;
  transition: transform 300ms ease;
  z-index: 5;
}

.card-body h5:hover, .card-body h6:hover {
  transform: translateX(0.15rem); 
}

.card-body h6 {
  color: #2ECC71;
  padding: 0 1.25rem; 
}

.card-body h4 {
  color: #1C204B;
  padding: 0;
  margin: 0;
  font-weight: 600;
}


.academic-year {
    color: #2ECC71; 
    font-weight: bold; 
    font-size: 1.2em; 
}

.evaluation-status {
    color: #2ECC71; 
    font-weight: bold; 
    font-size: 1.2em; 
}


.card-body h5 b, .card-body h6 b {
    color: #1C204B;
    font-weight: bold;
}


.card::before {
  position: absolute;
  width: 20rem; 
  height: 20rem;
  transform: translate(-50%, -50%);
  background: radial-gradient(circle closest-side at center, white, transparent);
  opacity: 0;
  transition: opacity 300ms ease;
  z-index: 3;
}

.card:hover::before {
  opacity: 0.1; 
}

.card:hover .notiborderglow {
  opacity: 0.1;
}


.note {
  color: #32a6ff; 
  position: fixed;
  top: 80%;
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
  font-size: 0.9rem;
  width: 75%;
}


/* ----- */

.custom-box {
  max-width: auto;
  height: 130px;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  border-radius: 0.5rem;
  background:#1C204B;
  background: -webkit-linear-gradient(to right, #3f4c6b, #1C204B; );
  background: linear-gradient(to right top, #3f4c6b,#1C204B; );
  padding: 2rem;
  margin: 0.5rem;
  color: rgb(107, 114, 128);
  box-shadow: 0px 30px 78px -39px rgba(0, 0, 0, 0.4);
  transition: transform 0.3s ease; 
}

.custom-box:hover {
  transform: scale(1.05); /* Scale up slightly on hover */
}

.inner {
  flex-grow: 1;
}

.inner h3 {
  margin-bottom: 0.25rem;
  font-size: 1.5rem; 
  line-height: 1.25rem;
  font-weight: 600;
  color: rgb(255, 255, 255);
}

.inner p {
  margin: 0;
  font-size: 1rem; 
  color: rgb(255, 255, 255);
}

.icon {
  font-size: 2rem; 
  color: rgb(255, 255, 255);
}


 </style>
 <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4>&nbsp;Welcome <?php echo $_SESSION['login_name']?>!</h4> 
        <br>
        <div class="col-md-5">
        <h5>
    <b>&nbsp;Academic Year:</b>
    <span class="academic-year"><?php echo $_SESSION['academic']['year'] . ' ' . (ordinal_suffix1($_SESSION['academic']['semester'])); ?></span>
</h5>
<h6>
    <b>&nbsp;Evaluation Status:</b>
    <span class="evaluation-status"><?php echo $astat[$_SESSION['academic']['status']]; ?></span>
</h6>

        </div>
      </div>
    </div>
</div>
<div class="row">
  <div class="col-12 col-sm-6 col-md-4">
    <div class="custom-box">
      <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM faculty_list")->num_rows; ?></h3>
        <p>Total Faculties</p>
      </div>
      <div class="icon">
        <i class="fa fa-user-friends"></i>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-md-4">
    <div class="custom-box">
      <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM student_list")->num_rows; ?></h3>
        <p>Total Students</p>
      </div>
      <div class="icon">
        <i class="fa ion-ios-people-outline"></i>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-md-4">
    <div class="custom-box">
      <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM users")->num_rows; ?></h3>
        <p>Total Users</p>
      </div>
      <div class="icon">
        <i class="fa fa-users"></i>
      </div>
    </div>
  </div>
  <div class="col-12 col-sm-6 col-md-4">
    <div class="custom-box">
      <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM class_list")->num_rows; ?></h3>
        <p>Total Classes</p>
      </div>
      <div class="icon">
        <i class="fa fa-list-alt"></i>
      </div>
    </div>
  </div>
</div>

