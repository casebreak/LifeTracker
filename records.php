<?php

session_start();

//Redirect to login screen if no user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: http://lifetracker.case-break.com');
}

if (isset($_GET['d'])) {
  echo "<div class='alert alert-success' role='alert' style='line-height:34px;overflow:auto;'>";
  echo "<h4 style='margin:0;'>Record removed</h4>";
  echo "</div>";
}

//Let the starting item index be 0
$start = 0;

//Items to display per page (limit) be 10
$limit = 10;

?>

<!doctype html>
<html>
<head>

  <?php include("includes/inc_links.php"); ?>

  <style type="text/css">

    thead tr th, td {
      text-align: center;
    }

    .tooltip-inner {
      max-width: 250px;
      /* If max-width does not work, try using width instead */
      /*width: 350px; */
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
          <th><a href="#" data-toggle="tooltip" title="Date"><img src="images/glyphicons-55-clock-blue.svg"><!-- <span class="glyphicons glyphicons-clock" style="font-size: 1.5em;"><span> --></a></th>
          <th><a href="#" data-toggle="tooltip" title="Sleep"><img src="images/glyphicons-496-bed-alt-blue.svg"></a></th>
          <th><a href="#" data-toggle="tooltip" title="Exercise"><img src="images/glyphicons-592-person-running-blue.svg"></a></th>
          <th><a href="#" data-toggle="tooltip" title="Diet"><img src="images/glyphicons-276-fast-food-blue.svg"></a></th>
          <th><a href="#" data-toggle="tooltip" title="Goal Progress"><img src="images/glyphicons-507-star-half-blue.svg"></a></th>
          <th><a href="#" data-toggle="tooltip" title="Fun"><img src="images/glyphicons-323-playing-dices-blue.svg"></a></th>
          <th><a href="#" data-toggle="tooltip" title="Satisfaction"><img src="images/glyphicons-649-important-day-blue.svg"></a></th>
          <th>Notes</th>
          <th>Edit</th>
        </tr>
      </thead>
      <tbody>

<?php

$page = "records";

include('includes/inc_connect.php');


/////////////////START PAGINATION//////////////////////////////////


//If the page number ($current_page) is set..
if(isset($_GET['pageNum'])) {
  $current_page = $_GET['pageNum'];
  $start = ($current_page-1)*$limit;
}
else {
  $current_page = 1;
}

// Retrieve required number of rows from database
$getData = $db->prepare('SELECT * FROM records WHERE user = :user ORDER BY recordDate DESC LIMIT :start, :limit');
$getData->bindParam(':start', $start, PDO::PARAM_INT);
$getData->bindParam(':limit', $limit, PDO::PARAM_INT);
$getData->bindParam(':user',$_SESSION['username'], PDO::PARAM_STR);
$getData->execute(); 

//Fetch the data and Display the items
$numRecords = 0;

while($dispData = $getData->fetch(PDO::FETCH_ASSOC)) {
$numRecords += 1;
?>

      <tr>
        <td><?php echo $dispData['recordDate']; ?></td>
        <td><?php echo $dispData['sleep']; ?></td>
        <td style="background-color: <?php echo ($dispData['exercise'] == 'No') ? '#FB5F67;' : '#5FD852'; ?>"><?php echo $dispData['exercise']; ?></td>
        <td style="background-color: <?php echo ($dispData['food'] == 'No') ? '#FB5F67;' : '#5FD852'; ?>"><?php echo $dispData['food']; ?></td>
        <td style="background-color: <?php echo ($dispData['goal'] == 'No') ? '#FB5F67;' : '#5FD852'; ?>"><?php echo $dispData['goal']; ?></td>
        <td style="background-color: <?php echo ($dispData['fun'] == 'No') ? '#FB5F67;' : '#5FD852'; ?>"><?php echo $dispData['fun']; ?></td>
        <td><?php echo $dispData['satisfaction']; ?></td>
        <td>
          <!-- Only display "view notes" button if there are notes present -->
          <?php if ($dispData['notes'] != "") { ?>
          <a href="#" data-toggle="tooltip" data-placement="left" title="<?php echo $dispData['notes']; ?>"><img src="images/glyphicons-30-notes-2-blue.svg"></a>
          &nbsp;
          <?php } ?>
        </td>
        <td>
          <a href="includes/inc_edit.php?id=<?php echo $dispData['ID']; ?>" data-toggle="tooltip" title="Edit Entry"><img src="images/glyphicons-151-edit-blue.svg"></a>
        </td>
      </tr>

<?php
}//End while($dispData = $getData->fetch(PDO::FETCH_ASSOC))
?>

    </tbody>
  </table>
  </div><!-- Close Table responsive -->

  <script>
  $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
  });
  </script>

  <center><nav><!-- Pagination nav -->
    <ul class="pagination" style="margin-top: 0;">

<?php

/*Calculate total number of pages to display, 
 based on total number of records in database */
$data=$db->prepare('SELECT * FROM records WHERE user = :user');
$data->bindParam(':user',$_SESSION['username'], PDO::PARAM_STR);
$data->execute();
$totalRecd = $data->rowCount();
$num_of_pages = ceil($totalRecd/$limit);

//If current page is greater than 1, add 'previous' button
if ($num_of_pages > 1) {
if($current_page>1) { 
?> 

    <li>
      <a href="?pageNum=<?php echo ($current_page-1); ?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>

<?php 
}//End if($current_page>1) 

for($i=1; $i<=$num_of_pages; $i++) {
  //Page number of Currenly viewing page
  if($i==$current_page) { 
?>

    <li class="active"><a href="?pageNum=<?php echo $i; ?>"><?php echo $i; ?></a></li>
  
<?php
}//End if($i==$current_page)
//Page number of other pages (with hyperlink to navigate)   
else { 
?>
    
    <li><a href="?pageNum=<?php echo $i; ?>"><?php echo $i; ?></a></li>

<?php
}//End else
}//End for($i=1; $i<=$num_of_pages; $i++)

// If current page is lesser than number of pages, add 'Next' button
if($current_page < $num_of_pages) {
?>

    <li>
      <a href="?pageNum=<?php echo ($current_page+1); ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>

<?php
}//End if($current_page < $num_of_pages)
}//End if ($num_of_pages > 1)

?>

</ul>
</nav><!-- End Pagination div --></center>
</div><!-- close container -->

</body>

</html>