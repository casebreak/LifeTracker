<!doctype html>
<html>
<head>

  <?php include("inc_links.php"); ?>

  <style type="text/css">

  </style>
</head>
<body>
        
<?php include("inc_nav.php"); ?>

  <div class="container"><!-- Main container -->

  	<center><form action="POST">

 			<div class="input-group">
       	<span class="input-group-addon" id="basic-addon3">Username</span>
       	<input type="text" name="username" class="form-control" value="" required>
     	</div>

     	<button class="btn btn-success" type="submit" value="submit">Submit</button>

 		</form></center>

<?php
if (isset($_GET['reset'])) {
?>

  	<center><form action="POST">

 			<div class="input-group">
       	<span class="input-group-addon" id="basic-addon3">Username</span>
       	<input type="text" name="username" class="form-control" value="" required>
     	</div>

     	<button class="btn btn-success" type="submit" name="submit" value="Submit">Submit</button>

 		</form></center>

<?php
}//End if (isset($_GET['reset'])) 

if (isset($_POST['submit']) && $correct) {
?>

		<h1><?php echo $row['question']; ?></h1>

  	<center><form action="POST">

 			<div class="input-group">
       	<span class="input-group-addon" id="basic-addon3">Username</span>
       	<input type="text" name="answer" class="form-control" required>
     	</div>

     	<button class="btn btn-success" type="submit" name="reset" value="Reset my password">Reset my password</button>

 		</form></center>		

<?php
}//End if (isset($_POST['submit']) && $correct)
?>

  </div><!-- Close main container -->

</body>
</html>  