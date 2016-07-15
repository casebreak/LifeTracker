<?php session_start(); ?>

<div class="container-fluid" style="padding:0;">
  <nav class="navbar navbar-inverse navbar-static-top">
    <div class="container">
      <div class="navbar-header">

        <!-- Hamburger menu button -->
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#dropdown-menu" aria-expanded="false">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>

        <!-- Navbar 'logo' or 'brand' -->
        <div class="navbar-brand">Life Tracker</div>
      </div>

      <!-- Navbar links -->
      <!-- The list item class values are set depending on the page that gets called -->
      <div class="collapse navbar-collapse" id="dropdown-menu">
        <ul class="nav navbar-nav">
          <li class="<?php echo ($page == 'tracker' ? 'active' : ' ')?>">
            <a href="http://lifetracker.case-break.com/tracker.php">Tracker</a>
          </li>
          <li class="<?php echo ($page == 'records' ? 'active' : ' ')?>">
            <a href="http://lifetracker.case-break.com/records.php">Records</a>
          </li>
          <li class="<?php echo ($page == 'analyze' ? 'active' : ' ')?>">
            <a href="http://lifetracker.case-break.com/analyze.php">Analyze</a>
          </li>
        </ul>

<?php
//Only display "logged in as" and "logout" labels when a user is logged in
if(isset($_SESSION['username'])) {
?>

        <ul class="nav navbar-nav navbar-right">
        	<li style="padding-top: 15px; padding-left: 15px; color: #fff;">
	        	Logged in as: <?php echo $_SESSION['username']; ?>
						<span style="margin-left: 20px;"><a href="includes/inc_logout.php">Logout</a></span>
					</li>
        </ul>

<?php
}//End if(isset($_SESSION['username']))
?>

      </div>
    </div>
  </nav><!-- Close navigation -->
 </div><!-- Close nav container -->



<!-- Navigation -->
<!-- <ul class="nav nav-pills" style="margin-top: 20px;">
  <li role="presentation" <?php //echo ($page == "tracker" ? "class='active'" : ""); ?>><a href="http://lifetracker.case-break.com/tracker.php">Tracker</a></li>
  <li role="presentation" <?php //echo ($page == "records" ? "class='active'" : ""); ?>><a href="http://lifetracker.case-break.com/records.php">Records</a></li>
  <li role="presentation" <?php //echo ($page == "analyze" ? "class='active'" : ""); ?>><a href="http://lifetracker.case-break.com/analyze.php">Analyze</a></li>
</ul> -->