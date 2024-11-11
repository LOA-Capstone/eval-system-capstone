<!-- student/sidebar.php -->

<!-- Include AdminLTE CSS (Ensure the path is correct) -->
<link rel="stylesheet" href="path/to/adminlte.min.css">
<!-- Include Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link d-flex align-items-center">
        <img src="assets/uploads/LoaLogo.png" alt="Student Panel Logo" class="brand-image img-circle elevation-3" style="opacity: .8;">
        <span class="brand-text font-weight-bold ml-2">STUDENT PANEL</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard Link -->
                <li class="nav-item">
                    <a href="./" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- Evaluate Link -->
                <li class="nav-item">
                    <a href="./index.php?page=evaluate" class="nav-link nav-evaluate">
                        <i class="nav-icon fas fa-th-list"></i>
                        <p>Evaluate</p>
                    </a>
                </li>
                <!-- Add more navigation items here -->
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<!-- Include jQuery (Required for AdminLTE) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include AdminLTE JS -->
<script src="path/to/adminlte.min.js"></script>

<script>
    $(document).ready(function(){
        var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
        var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
        if(s != '')
            page = page + '_' + s;
        if($('.nav-link.nav-' + page).length > 0){
            $('.nav-link.nav-' + page).addClass('active');
            if($('.nav-link.nav-' + page).hasClass('tree-item') == true){
                $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active');
                $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open');
            }
            if($('.nav-link.nav-' + page).hasClass('nav-is-tree') == true){
                $('.nav-link.nav-' + page).parent().addClass('menu-open');
            }
        }
    });
</script>
