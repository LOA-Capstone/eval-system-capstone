<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    .main-sidebar {
        background: #1C204B;
        box-shadow: 1px 0 15px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .sidebars p {
        color: white;
        transition: background-color 0.3s, color 0.3s;
        font-weight: 500;
    }

    .nav-link {
        padding: 10px 15px;
        transition: color 0.3s ease;
    }

    .nav-link.active p,
    .nav-link.active .nav-icon {
        color: yellow;
        transition: color 0.3s ease;
        font-weight: 600;
    }

    .nav-icon {
        color: white;
    }

    .nav-link.active .nav-icon {
        color: yellow;
        transition: color 0.3s ease;
    }

    .nav-link:hover p,
    .nav-link:hover .nav-icon {
        color: yellow;
        transition: color 0.3s ease;
    }

    .nav-item {
        margin-top: 0.5rem;
        transition: 0.3s ease;
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
        overflow-y: auto; /* Enable vertical scrolling */
        max-height: calc(100vh - 100px); /* Adjust the height as needed */
    }

    /* Optional: Hide scrollbar for Webkit browsers */
    .sidebars::-webkit-scrollbar {
        width: 5px;
    }

    .sidebars::-webkit-scrollbar-track {
        background: #1C204B;
    }

    .sidebars::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 10px;
    }

    .nav-link {
        padding: 10px;
    }

    .text-center {
        color: white;
    }
</style>

<aside class="main-sidebar">
    <div class="brand-container">
        <a href="./">
            <img src="assets/uploads/LoaLogo.png" alt="Admin Panel Logo" class="brand-logo">
            <h4 class="text-center p-0 m-0"><b>ADMIN PANEL</b></h4>
        </a>
    </div>
    <div class="sidebars">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item dropdown">
                    <a href="./" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- Subjects -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=subject_list" class="nav-link nav-subject_list">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Subjects</p>
                    </a>
                </li>
                <!-- Classes -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=class_list" class="nav-link nav-class_list">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Classes</p>
                    </a>
                </li>
                <!-- Academic Year -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=academic_year_list" class="nav-link nav-academic_year_list">
                        <i class="nav-icon fas fa-calendar"></i>
                        <p>Academic Year List</p>
                    </a>
                </li>
                <!-- Questionnaires -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=questionnaire" class="nav-link nav-questionnaire">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Questionnaires</p>
                    </a>
                </li>
                <!-- Evaluation Criteria -->
                <li class="nav-item dropdown">
                    <a href="./index.php?page=criteria_list" class="nav-link nav-criteria_list">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Evaluation Criteria</p>
                    </a>
                </li>
                <!-- Teachers -->
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_faculty">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>Teachers<i class="right fas fa-angle-left"></i></p>
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
                              <!-- Batch Upload -->
                              <li class="nav-item">
                            <a href="./index.php?page=faculty_batch_upload" class="nav-link nav-faculty_batch_upload tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Batch Upload</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Students -->
                <li class="nav-item">
                    <a href="#" class="nav-link nav-edit_student">
                        <i class="nav-icon fa-solid fa-user-graduate"></i>
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
                        <!-- Batch Upload -->
                        <li class="nav-item">
                            <a href="./index.php?page=student_batch_upload" class="nav-link nav-student_batch_upload tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Batch Upload</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Department Section -->
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link nav-edit_department">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Departments
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="./index.php?page=new_department" class="nav-link nav-new_department tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>Add New</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="./index.php?page=department_list" class="nav-link nav-department_list tree-item">
                                <i class="fas fa-angle-right nav-icon"></i>
                                <p>List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Users Section -->
                <li class="nav-item dropdown">
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
    $(document).ready(function() {
        var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
        var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
        if (s != '')
            page = page + '_' + s;
        if ($('.nav-link.nav-' + page).length > 0) {
            $('.nav-link.nav-' + page).addClass('active')
            if ($('.nav-link.nav-' + page).hasClass('tree-item') == true) {
                $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active')
                $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open')
            }
            if ($('.nav-link.nav-' + page).hasClass('nav-is-tree') == true) {
                $('.nav-link.nav-' + page).parent().addClass('menu-open')
            }
        }
    })
</script>
