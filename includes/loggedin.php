<?php
if (isset($_SESSION['username'])) {
?>
<div class="pull-right" style="margin-top: 5px; margin-bottom: 10px; clear: both;">
	<h4>
		Logged in as: <?php echo $_SESSION['username']; ?>
		<span style="margin-left: 20px;"><a href="includes/inc_logout.php">Logout</a></span>
	</h4>
</div>

<?php
}//End if (isset($_SESSION['username']))
?>