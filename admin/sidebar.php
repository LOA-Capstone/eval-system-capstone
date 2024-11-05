<style>
.main-sidebar {
    background: radial-gradient(178.94% 106.41% at 26.42% 106.41%, #FFF7B1 0%, #FFFFFF 71.88%);
    box-shadow: 1px 0 15px rgba(0, 0, 0, 0.5);
}

.sidebars p {
    color: black;
    transition: background-color 0.3s, color 0.3s;
    font-weight: 500;
}

.nav-link {
    padding: 10px 15px;
    transition: color 0.3s ease;
}

.nav-link.active p,
.nav-link.active .nav-icon {
    color: #fbca1f;
    transition: color 0.3s ease;
    font-weight: 600;
}

.nav-link:hover p {
    font-weight: 600;
}

.nav-icon {
    color: black;
}

.nav-link.active .nav-icon {
  color: #fbca1f;
    transition: color 0.3s ease;
}

.nav-link:hover p,
.nav-link:hover .nav-icon {
    color: blue;
    transition: color 0.3s ease;
}

.nav-item {
    margin-top: 0.5rem;
    transition: background-color 0.3s ease;
}

.main-sidebar .sidebar .nav-item.active {
    background-color: red !important; /* Medium gray with 60% opacity */
    z-index: 999;
}



.nav-item.active .nav-link p,
.nav-item.active .nav-link .nav-icon {
    color: yellow;
    transition: color 0.3s ease;
}

.nav-item.active .nav-link:hover p,
.nav-item.active .nav-link:hover .nav-icon {
    color: yellow; 
}

.main-sidebar {
    display: flex;
    flex-direction: column; 
}

.brand-container {
    text-align: center; 
    padding: 10px 0; 
}

.brand-logo {
    width: auto; 
    height: 70px; 
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.60);
    border-radius: 50%;
}

.sidebars {
    flex-grow: 1; 
}

.nav-link {
    padding: 10px; 
}


</style>

<aside class="main-sidebar">
    <div class="brand-container"> <!-- Added a wrapper for the logo and title -->
        <a>
            <img src="assets/uploads/LoaLogo.png" alt="Admin Panel Logo" class="brand-logo">
            <h4 class="text-center p-0 m-0"><b>ADMIN PANEL</b></h2>
        </a>
    </div>
    <div class="sidebars">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item dropdown">
                    <a href="./" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=subject_list" class="nav-link nav-subject_list">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Subjects</p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=class_list" class="nav-link nav-class_list">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Classes</p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=academic_list" class="nav-link nav-academic_list">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>Academic Year</p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=questionnaire" class="nav-link nav-questionnaire">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Questionnaires</p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=criteria_list" class="nav-link nav-criteria_list">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Evaluation Criteria</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_faculty">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>Faculties<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./index.php?page=new_faculty" class="nav-link nav-new_faculty tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./index.php?page=faculty_list" class="nav-link nav-faculty_list tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_student">
                        <i class="nav-icon fa ion-ios-people-outline"></i>
                        <p>Students<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./index.php?page=new_student" class="nav-link nav-new_student tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./index.php?page=student_list" class="nav-link nav-student_list tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=report" class="nav-link nav-report">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Evaluation Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_user">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./index.php?page=new_user" class="nav-link nav-new_user tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./index.php?page=user_list" class="nav-link nav-user_list tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>



  <script>
  	$(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
  		var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      if(s!='')
        page = page+'_'+s;
  		if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
  			if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
  				$('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
  			}
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

  		}
     
  	})

    $(document).ready(function() {
    // Disable pointer events for active navigation links
    $('.nav-link.active').css('pointer-events', 'none');

    // Disable hover effects for active links
    $('.nav-link.active').hover(
        function() {
            $(this).css('color', 'yellow'); // Ensure color remains yellow when hovered
        },
        function() {
            $(this).css('color', 'yellow'); // Ensure color remains yellow when unhovered
        }
    );
});

  </script>
