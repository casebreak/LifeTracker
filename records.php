<?php

session_start();

//Redirect to login screen if no user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: http://lifetracker.case-break.com');
}

$page = "records";

include('includes/inc_connect.php');

$stmt = "SELECT * FROM records WHERE user = :user ORDER BY ID DESC";

$result = $db->prepare($stmt);
$result->bindParam(':user',$_SESSION['username']);
$result->execute();

if (isset($_GET['d'])) {
  echo "<div class='alert alert-success' role='alert' style='line-height:34px;overflow:auto;'>";
  echo "<h4 style='margin:0;'>Record removed</h4>";
  echo "</div>";
}

?>

<!doctype html>
<html>
<head>

  <?php include("includes/inc_links.php"); ?>

  <style type="text/css">

    thead tr th {
      text-align: center;
    }

  </style>
</head>
<body>
        
  <?php include("includes/inc_nav.php"); ?>

  <div class="container"><!-- Main container -->

  <div class="table-responsive" id="records-div">
  <table class="table table-bordered table-striped" id="records">
    <thead>
      <tr>
        <th>Date</th>
        <th>Hours of Sleep</th>
        <th>Exercise?</th>
        <th>Diet?</th>
        <th>Help Others?</th>
        <th>Challenge?</th>
        <th>Fun?</th>
        <th>Day was...</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>

<?php

for ($i=0; $row = $result->fetch(); $i++) { 

?>

      <tr>
        <td><?php echo $row['recordDate']; ?></td>
        <td><?php echo $row['sleep']; ?></td>
        <td><?php echo $row['exercise']; ?></td>
        <td><?php echo $row['food']; ?></td>
        <td><?php echo $row['service']; ?></td>
        <td><?php echo $row['challenge']; ?></td>
        <td><?php echo $row['fun']; ?></td>
        <td><?php echo $row['satisfaction']; ?></td>
        <td>
          <!-- Only display "view notes" button if there are notes present -->
          <?php if ($row['notes'] != "") { ?>
          <a class="btn btn-primary btn-xs" href="includes/inc_notes.php?id=<?php echo $row['ID'] ?>">View Notes</a>
          &nbsp;
          <?php } ?>
          <a class="btn btn-warning btn-xs" href="includes/inc_edit.php?id=<?php echo $row['ID']; ?>">Edit</a>
        </td>
      </tr>

<?php 
$numRecords = $i + 1;
}//End for ($i=0; $row = $result->fetch(); $i++)
?>
    </tbody>
  </table>
  </div><!-- Close Table responsive -->

  <div class="row">
    <div class="col-lg-2">
      <p>Number of records: <?php echo $numRecords; ?></p>
    </div>
  </div>

</div><!-- close container -->

</body>

</html>