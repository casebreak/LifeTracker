<?php

if (isset($_POST['delete'])) {
	include('inc_connect.php');
	$sql = "DELETE FROM records WHERE ID = :ID";
	$stmt = $db->prepare($sql);
	$stmt->bindParam(':ID',$_GET['id']);
	$stmt->execute();
	header("Location: http://lifetracker.case-break.com/records.php?d");
}

if (isset($_POST['back'])) {
	header("Location: http://lifetracker.case-break.com/records.php");
}
	
?>

<!doctype html>
<html>
<head>

  <?php include("inc_links.php"); ?>

</head>
<body>

  <div class="container">
    <center>
			<form method="POST">
				<h2>This action will permanently delete this record. Are you sure you want to continue?</h2>
				<button class="btn btn-danger" type="submit" name="delete">Yes, Delete</button>
				&nbsp;
				<button class="btn btn-primary" type="submit" name="back">No, Go Back</button>
			</form>
		</center>
  </div><!-- close container -->

</body>
</html>