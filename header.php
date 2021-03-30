<header class="main-header">
  <!-- Logo -->
  <a href="#" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
	<!--<span class="logo-mini"><b>A</b>LT</span>-->
	<!-- logo for regular state and mobile devices -->
	<!--<span class="logo-lg"><b>Admin</b>LTE</span>-->
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
	<!-- Sidebar toggle button-->
	<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
	  <span class="sr-only">Toggle navigation</span>
	  <span class="icon-bar"></span>
	  <span class="icon-bar"></span>
	  <span class="icon-bar"></span>
	</a>

	<div class="navbar-custom-menu">
	  <ul class="nav navbar-nav">
		<li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_SESSION["AUTHEN"]["FULLNAME"]; ?> <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="logout.php">ออกจากระบบ</a></li>
          </ul>
        </li>
	  </ul>
	</div>
  </nav>
</header>