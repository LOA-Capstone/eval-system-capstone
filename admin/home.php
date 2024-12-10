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
  background: #B1E4FF;
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
  background: linear-gradient(to bottom, #2eadff, #3d83ff, #7e61ff);
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
    color: yellow; 
    font-weight: bold; 
    font-size: 1.2em; 
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6), 
                 -2px -2px 4px rgba(0, 0, 0, 0.6),
                 2px -2px 4px rgba(0, 0, 0, 0.6),
                 -2px 2px 4px rgba(0, 0, 0, 0.6);  
}

.evaluation-status {
    color: yellow;
    font-weight: bold;
    font-size: 1.2em;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6), 
                 -2px -2px 4px rgba(0, 0, 0, 0.6),
                 2px -2px 4px rgba(0, 0, 0, 0.6),
                 -2px 2px 4px rgba(0, 0, 0, 0.6); 
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
  max-width: 100%;
  height: 150px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(to top right, #3f4c6b, #1C204B);
  border-radius: 1rem;
  padding: 2rem;
  margin: 1rem 0;
  color: white;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.custom-box:hover {
  transform: translateY(-5px);
}

.inner {
  flex-grow: 1;
  text-align: left;
}

.inner h3 {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}

.inner p {
  font-size: 1.1rem;
  font-weight: 500;
}

.icon {
  font-size: 2.5rem;
  color: #ffffff;
  transition: transform 0.3s ease;
}

.icon:hover {
  transform: scale(1.1);
}

/* Modern Note Design */
.note {
  color: #32a6ff;
  position: fixed;
  top: 80%;
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
  font-size: 1rem;
  width: 80%;
  padding: 0.75rem;
  background-color: rgba(0, 0, 0, 0.7);
  border-radius: 0.5rem;
  color: #fff;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
  .custom-box {
    height: 120px;
    flex-direction: column;
    text-align: center;
  }
  
  .inner h3 {
    font-size: 1.6rem;
  }
  
  .icon {
    font-size: 2rem;
    margin-top: 1rem;
  }
}
 </style>

<div class="col-12">
    <div class="card">
        <div class="card-body">
            <h4>&nbsp;Welcome <?php echo $_SESSION['login_name']; ?>!</h4> 
            <br>
            <div class="col-md-5">
                <?php if (isset($_SESSION['academic'])): ?>
                    <h5>
                        <b>&nbsp;Academic Year:</b>
                        <span class="academic-year">
                            <?php echo $_SESSION['academic']['year'] . ' ' . ordinal_suffix1($_SESSION['academic']['semester']); ?>
                        </span>
                    </h5>
                    <h6>
                        <b>&nbsp;Evaluation Status:</b>
                        <span class="evaluation-status">
                            <?php echo $astat[$_SESSION['academic']['status']]; ?>
                        </span>
                        <b>&nbsp;Term:</b>
                        <span class="evaluation-status">
                            <?php echo $_SESSION['academic']['term'] = $_SESSION['academic']['term'] ?? 'Not Set'; ?>
                        </span>
                    </h6>
                <?php else: ?>
                    <h5>No academic data available.</h5>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Total Faculties -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="custom-box">
            <div class="inner">
                <h3><?php echo $conn->query("SELECT COUNT(*) as total FROM faculty_list")->fetch_assoc()['total']; ?></h3>
                <p>Total Faculties</p>
            </div>
            <div class="icon">
                <i class="fa fa-user-friends"></i>
            </div>
        </div>
    </div>
    <!-- Total Students -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="custom-box">
            <div class="inner">
                <h3><?php echo $conn->query("SELECT COUNT(*) as total FROM student_list")->fetch_assoc()['total']; ?></h3>
                <p>Total Students</p>
            </div>
            <div class="icon">
                <i class="fa ion-ios-people-outline"></i>
            </div>
        </div>
    </div>
    <!-- Total Users -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="custom-box">
            <div class="inner">
                <h3><?php echo $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total']; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
        </div>
    </div>
    <!-- Total Classes -->
    <div class="col-12 col-sm-6 col-md-4">
        <div class="custom-box">
            <div class="inner">
                <h3><?php echo $conn->query("SELECT COUNT(*) as total FROM class_list")->fetch_assoc()['total']; ?></h3>
                <p>Total Classes</p>
            </div>
            <div class="icon">
                <i class="fa fa-list-alt"></i>
            </div>
        </div>
    </div>
</div>