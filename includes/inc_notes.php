<?php

session_start();

$page = "notes";

include('inc_connect.php');

$stmt = "SELECT * FROM records WHERE id = :id";

$result = $db->prepare($stmt);
$result->bindParam(':id',$_GET['id']);
$result->execute();
$row = $result->fetch();

?>

<!doctype html>
<html>
<head>

  <?php include("inc_links.php"); ?>

  <style type="text/css">

    #records-div {
      margin-top: 20px;
    }

    thead tr th {
      text-align: center;
    }

  </style>
</head>
<body>
        
  <?php include("inc_nav.php"); ?>

  <div class="container"><!-- Main container -->

    <h3>Displaying notes for <?php echo $row['recordDate']; ?></h3>
    <div class="well">
      <p><?php echo $row['notes']; ?></p>
    </div>

    <a href="http://lifetracker.case-break.com/records.php">Back to Records</a>

  </div><!-- close container -->

</body>

</html>