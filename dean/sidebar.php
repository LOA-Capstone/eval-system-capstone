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

    .nav-link:hover p {
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
        <a>
            <img src="assets/uploads/LoaLogo.png" alt="Admin Panel Logo" class="brand-logo">
            <h4 class="text-center p-0 m-0"><b>DEAN PANEL</b></h4>
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
               
             
            


                <li class="nav-item">
    <a href="./index.php?page=faculty_list" class="nav-link nav-faculty_list">
        <i class="nav-icon fas fa-user-friends"></i>
        <p>Faculties</p>
    </a>
</li>


                <li class="nav-item dropdown">
                    <a href="./index.php?page=report" class="nav-link nav-report">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Evaluation Report</p>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="./index.php?page=evaluation_summary" class="nav-link nav-evaluation_summary">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Evaluation Summary</p>
                    </a>
                </li>
<!-- Department Section -->




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