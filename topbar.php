  <style>
    .user-img {
        border-radius: 50%;
        height: 25px;
        width: 25px;
        object-fit: cover;
    }

    .main-header.navbar {
  height: 79px;
  background: #B1E4FF;
  display: flex;
  align-items: center; 
  text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1), 
                 -1px -1px 1px rgba(0, 0, 0, 0.1),
                 1px -1px 1px rgba(0, 0, 0, 0.1),
                 -1px 1px 1px rgba(0, 0, 0, 0.1);
}

.nav-link.Custom-Loa {
  font-size: 26px; 
  line-height:-100px; 
  white-space: nowrap; 
  overflow: hidden;
  padding: 0 15px; 
  
}


@media (max-width: 743px) {
  .nav-link.Custom-Loa {
    display: none;
  }
}

@media (max-width: 861px) {
  .nav-link.Custom-Loa {
    font-size: 20px; 
  line-height: 43px; 
  white-space: nowrap; 
  overflow: hidden;
  padding: 0 15px; 
  }
}

@media (min-width: 995px) and (max-width: 1111px){
  .nav-link.Custom-Loa {
    font-size: 21px; 
  line-height: 43px; 
  white-space: nowrap; 
  overflow: hidden;
  padding: 0 15px; 
  }
}

  </style>
<!-- Navbar -->
  <nav class="main-header navbar navbar-expand ">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
    <?php if(isset($_SESSION['login_id'])): ?>
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars" style="color: #1C204B;"></i></a>
      </li>
    <?php endif; ?>
      <li>
      <a class="nav-link Custom-Loa" href="./" role="button" style="color: #1C204B; font-weight: 900;"> <large><b><?php echo $_SESSION['system']['name'] ?></b></large></a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
     
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt" style="color: #1C204B;"></i>
        </a>
      </li>
     <li class="nav-item dropdown">
            <a class="nav-link"  data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
              <span>
                <div class="d-felx badge-pill">
                  <span class=""><img src="assets/uploads/<?php echo $_SESSION['login_avatar'] ?>" alt="" class="user-img border "></span>
                  <span>&nbsp;&nbsp;<b style="color: #1C204B;"><?php echo ucwords($_SESSION['login_firstname']) ?></b></span>
                  <span class="fa fa-angle-down ml-2" style="color: #1C204B;"></span>
                </div>
              </span>
            </a>
            <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
              <a class="dropdown-item" href="javascript:void(0)" id="manage_account"><i class="fa fa-cog"></i>&nbsp; Manage Account</a>
              <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i>&nbsp; Logout</a>
            </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <script>
     $('#manage_account').click(function(){
        uni_modal('Manage Account','manage_user.php?id=<?php echo $_SESSION['login_id'] ?>')
      })

      
  </script>
