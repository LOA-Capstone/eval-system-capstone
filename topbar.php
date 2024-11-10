  <style>
    .user-img {
        border-radius: 50%;
        height: 25px;
        width: 25px;
        object-fit: cover;
    }

    .main-header.navbar {
  height: 60px;
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

.custom-nav-link {
  position: relative;
  display: flex;
  align-items: center;
  padding: 5px 15px;
  font-size: 17px;
  font-weight: 600;
  text-decoration: none;
  color: #1C204B;
  background: transparent;
  border: 1px solid #1C204B; 
  border-radius: 25px;
  cursor: pointer;
  overflow: hidden;
  transition: color 0.3s 0.1s ease-out;
}

.custom-nav-link span {
  display: flex;
  align-items: center;
  gap: 10px;
}

.custom-nav-link .user-img {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.custom-nav-link b.text {
  margin-left: 10px;
  color: #1C204B;
  transition: color 0.3s ease-out;
}

.custom-nav-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: auto;
  border-radius: 50%;
  width: 20em;
  height: 20em;
  left: -5em;
  transition: box-shadow 0.5s ease-out;
  z-index: -1;
}

.custom-nav-link:hover {
  color: #fff;
  border-color: #1C204B; 
}

.custom-nav-link:hover .text {
  color: #fff;
}

.custom-nav-link:hover::before {
  box-shadow: inset 0 0 0 10em #1C204B; 
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
     <a class="nav-link custom-nav-link" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
  <span>
    <img src="assets/uploads/<?php echo $_SESSION['login_avatar'] ?>" alt="" class="user-img">
    <b class="text"><?php echo ucwords($_SESSION['login_firstname']) ?></b>
    <span class="fa fa-angle-down"></span>
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
