<?php

session_start();

//Redirect to login screen if no user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: http://lifetracker.case-break.com');
}

$page = "analyze";

include('includes/inc_connect.php');

$stmt = "SELECT * FROM records WHERE user = :user ";

//Query by satisfaction
if (isset($_GET['day'])) {

  switch ($_GET['day']) {
    case 'excellent':
      $stmt .= " AND satisfaction LIKE '%excellent%' ORDER BY ID DESC";
      break;
    case 'great':
      $stmt .= " AND satisfaction LIKE '%great%' ORDER BY ID DESC";
      break;
    case 'good':
      $stmt .= " AND satisfaction LIKE '%good%' ORDER BY ID DESC";
      break;
    case 'fair':
      $stmt .= " AND satisfaction LIKE '%fair%' ORDER BY ID DESC";
      break;
    case 'poor':
      $stmt .= " AND satisfaction LIKE '%poor%' ORDER BY ID DESC";
      break;
    default:
      break;
  }//End switch ($_GET['day'])
}//End if (isset($_GET['day']))

$result = $db->prepare($stmt);
$result->bindParam(':user',$_SESSION['username']);
$result->execute();

?>

<!doctype html>
<html>
<head>

  <?php include("includes/inc_links.php"); ?>

  <style type="text/css">

  /* Skillbar styles */
  .skillbar {
    position:relative;
    display:block;
    margin-bottom:15px;
    background:#eee;
    height:35px;
    width:100%;
    border-radius:3px;
    -moz-border-radius:3px;
    -webkit-border-radius:3px;
    -webkit-transition:0.4s linear;
    -moz-transition:0.4s linear;
    -ms-transition:0.4s linear;
    -o-transition:0.4s linear;
    transition:0.4s linear;
    -webkit-transition-property:width, background-color;
    -moz-transition-property:width, background-color;
    -ms-transition-property:width, background-color;
    -o-transition-property:width, background-color;
    transition-property:width, background-color;
  }

  .skillbar-title {
    position:absolute;
    top:0;
    left:0;
    width:70px;
    font-weight:bold;
    font-size:13px;
    color:#ffffff;
    background:#6adcfa;
    -webkit-border-top-left-radius:3px;
    -webkit-border-bottom-left-radius:4px;
    -moz-border-radius-topleft:3px;
    -moz-border-radius-bottomleft:3px;
    border-top-left-radius:3px;
    border-bottom-left-radius:3px;
  }

  .skillbar-title-wider {
    position:absolute;
    top:0;
    left:0;
    width:85px;
    font-weight:bold;
    font-size:13px;
    color:#ffffff;
    background:#6adcfa;
    -webkit-border-top-left-radius:3px;
    -webkit-border-bottom-left-radius:4px;
    -moz-border-radius-topleft:3px;
    -moz-border-radius-bottomleft:3px;
    border-top-left-radius:3px;
    border-bottom-left-radius:3px;
  }  

  .skillbar-title span {
    display:block;
    background:rgba(0, 0, 0, 0.1);
    padding:0 20px;
    height:35px;
    line-height:35px;
    -webkit-border-top-left-radius:3px;
    -webkit-border-bottom-left-radius:3px;
    -moz-border-radius-topleft:3px;
    -moz-border-radius-bottomleft:3px;
    border-top-left-radius:3px;
    border-bottom-left-radius:3px;
  }

  .skillbar-title-wider span {
    display:block;
    background:rgba(0, 0, 0, 0.1);
    padding:0 20px;
    height:35px;
    line-height:35px;
    -webkit-border-top-left-radius:3px;
    -webkit-border-bottom-left-radius:3px;
    -moz-border-radius-topleft:3px;
    -moz-border-radius-bottomleft:3px;
    border-top-left-radius:3px;
    border-bottom-left-radius:3px;
  }  

  .skillbar-bar {
    height:35px;
    width:0px;
    background:#6adcfa;
    border-radius:3px;
    -moz-border-radius:3px;
    -webkit-border-radius:3px;
  }

  .skill-bar-percent {
    position:absolute;
    right:10px;
    top:0;
    font-size:11px;
    height:35px;
    line-height:35px;
    color:#000 !important;
    color:rgba(0, 0, 0, 0.4);
  }

  #records-div {
    margin-top: 20px;
  }

  /* Miscellaneous styles */
  thead tr th {
    text-align: center;
  }

  </style>

  <script type="text/javascript">
  //jQuery code for the animated skillbars
  jQuery(document).ready(function(){
    jQuery('.skillbar').each(function(){
      jQuery(this).find('.skillbar-bar').animate({
        width:jQuery(this).attr('data-percent')
      },4000);
    });
  });
  </script>

</head>
<body>
        
  <?php include("includes/inc_nav.php"); ?>

  <?php echo session_id(); ?>

  <div class="container"><!-- Main container -->


    <div class="btn-group">
      <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding-left: 50px; padding-right: 30px;">
        Show me my <?php echo (isset($_GET['day'])) ? $_GET['day'] . ' days' : ''; ?> 
        <span class="caret" style="margin-left: 20px;"></span>
      </button>
      <ul class="dropdown-menu">
        <li><a href="analyze.php?day=excellent">Excellent days</a></li>
        <li><a href="analyze.php?day=great">Great days</a></li>
        <li><a href="analyze.php?day=good">Good days</a></li>
        <li><a href="analyze.php?day=fair">Fair days</a></li>
        <li><a href="analyze.php?day=poor">Poor days</a></li>
      </ul>
    </div>  

<?php

#Satisfaction variables
$excellent = 0;
$great = 0;
$good = 0;
$fair = 0;
$poor = 0;

#Exercise variables
$ex_yes = 0;
$ex_no = 0;

#Diet variables
$diet_yes = 0;
$diet_no = 0;

#Service variables
$ser_yes = 0;
$serv_no = 0;

#Challenge variables
$chal_yes = 0;
$chal_no = 0;

#Fun variables
$fun_yes = 0;
$fun_no = 0;

$sleepAvg = 0;

for ($i=0; $row = $result->fetch(); $i++) { 
 
  $numRecords = $i + 1; //Count the number of records

  //Sum the total of sleep hours to calculate average later
  $sleepAvg += $row['sleep'];

  //Tally all the metrics each time the table is printed.

  #Exercise
  switch ($row['exercise']) {
    case 'Yes':
      $ex_yes++;
      break;
    case 'No':
      $ex_no++;
      break;    
    default:
      break;
  }//End switch ($row['exercise'])

  #Diet
  switch ($row['food']) {
    case 'Yes':
      $diet_yes++;
      break;
    case 'No':
      $diet_no++;
      break;
    default:
      break;
  }//End switch ($row['diet'])

  #Service
  switch ($row['service']) {
    case 'Yes':
      $serv_yes++;
      break;
    case 'No':
      $serv_no++;
      break;
    default:
      break;
  }//End switch ($row['service'])

  #Challenge
  switch ($row['challenge']) {
    case 'Yes':
      $chal_yes++;
      break;
    case 'No':
      $chal_no++;
      break;
    default:
      break;
  }//End switch ($row['challenge'])

  #Fun
  switch ($row['fun']) {
    case 'Yes':
      $fun_yes++;
      break;
    case 'No':
      $fun_no++;
      break;
    default:
      break;
  }//End switch ($row['fun'])

  #Satisfaction
  switch ($row['satisfaction']) {
    case 'Excellent':
      $excellent++;
      break;
    case 'Great':
      $great++;
      break;
    case 'Good':
      $good++;
      break;
    case 'Fair':
      $fair++;
      break;
    case 'Poor':
      $poor++;
      break;
    
    default:
      break;
  }//End switch ($row['satisfaction'])

}//End for ($i=0; $row = $result->fetch(); $i++)

#Set variables for metric yes/no percentages and average sleep time

//Exercise
$per_exercise = $ex_yes / $numRecords;
$per_noexercise = $ex_no / $numRecords;

//Diet
$per_diet = $diet_yes / $numRecords;
$per_nodiet = $diet_no / $numRecords;

//Service
$per_serv = $serv_yes / $numRecords;
$per_noserv = $serv_no / $numRecords;

//Challenge
$per_chal = $chal_yes / $numRecords;
$per_nochal = $chal_no / $numRecords;

//Fun
$per_fun = $fun_yes / $numRecords;
$per_nofun = $fun_no / $numRecords;

//Sleep
$foo = $sleepAvg / $numRecords;

/*
Prints the results container only if the query != it's default value.
In other words, if no query is selected, no results will be printed.
*/
if ($stmt != "SELECT * FROM records WHERE user = :user ") {

?>  
    
    <div class="well" style="margin-top: 20px;"><!-- Container to display results -->

<?php
if ($numRecords == 0) { //If no records exist...
?>
      <p>You've had no '<?php echo $_GET['day']; ?>' days.</p>

<?php
} else {
?>
      
      <!-- Output the total number of records for the selected query -->
      <p>Number of days: <?php echo $numRecords; ?></p>

      <!-- Display cards of data -->
      <div class="row">

        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you exercise?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_exercise * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_exercise * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_noexercise * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #d35400;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #e67e22;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_noexercise * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- Close <div class="col-lg-6"> -->

        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you follow a good diet?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_diet * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_diet * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_nodiet * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #d35400;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #e67e22;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_nodiet * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- Close <div class="col-lg-6"> -->
      </div><!-- Close <div class="row"> -->

      <div class="row">

        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you do something to help others?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_serv * 100) . '%' ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_serv * 100) . '%' ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_noserv * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #d35400;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #e67e22;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_noserv * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- End <div class="col-lg-6"> -->

        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you challenge yourself?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_chal * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_chal * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_nochal * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #d35400;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #e67e22;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_nochal * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- End <div class="col-lg-6> -->
      </div><!-- End <div class="row"> -->

      <div class="row">

        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you have fun?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_fun * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_fun * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_nofun * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #d35400;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #e67e22;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_nofun * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- End <div class="col-lg-6"> -->

        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Sleep Average</div>
            <div class="panel-body" style="padding-top: 40px; padding-bottom: 40px;">          

              <div class="skillbar clearfix" data-percent="<?php echo ($foo <= 8) ? round((float)(($foo / 8) * 100), 2) . '%' : '100%'; ?>">
                <div class="skillbar-title-wider" style="background: #27ae60;"><span>SLEEP</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$foo, 2) . " hours" ?></div>
              </div> <!-- End Skill Bar -->

            </div>     
          </div>
        </div><!-- End <div class="col-lg-6> -->
      </div><!-- End <div class="row"> -->
    </div><!-- Close <div clss="well"> -->

<?php
}//End if ($numRecords == 0) else...
}//End if ($stmt != "SELECT * FROM records WHERE user = :user ")
?>    

  </div><!-- Close <div class="container"> -->

</body>

</html>