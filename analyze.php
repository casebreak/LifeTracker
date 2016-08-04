<?php

session_start();

//Redirect to login screen if no user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: http://lifetracker.case-break.com');
}

$page = "analyze";

include('includes/inc_connect.php');

$stmt = "";

//Query by daily satisfaction levels
if (isset($_GET['day'])) {

  $stmt .= "SELECT * FROM records WHERE user = :user ";

  switch ($_GET['day']) {
    case 'great':
      $stmt .= " AND satisfaction LIKE '%great%' ORDER BY recordDate DESC";
      break;
    case 'good':
      $stmt .= " AND satisfaction LIKE '%good%' ORDER BY recordDate DESC";
      break;
    case 'fair':
      $stmt .= " AND satisfaction LIKE '%fair%' ORDER BY recordDate DESC";
      break;
    case 'poor':
      $stmt .= " AND satisfaction LIKE '%poor%' ORDER BY recordDate DESC";
      break;
    default:
      break;
  }//End switch ($_GET['day'])

//Query by timeframe
} elseif (isset($_GET['timeframe'])) {
  
  $stmt .= "SELECT * FROM records WHERE user = :user ORDER BY recordDate DESC LIMIT " . $_GET['timeframe'] . "";

} elseif (isset($_POST['searchbtn'])) {
  
  $stmt .= "SELECT * FROM records WHERE user = :user AND notes LIKE '%" . $_POST['search'] . "%' ORDER BY recordDate DESC";

}

$result = $db->prepare($stmt);
$result->bindParam(':user',$_SESSION['username'], PDO::PARAM_STR);
$result->execute();

if (isset($_POST['clear'])) {
  header('Location: http://lifetracker.case-break.com/analyze.php');
}

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
    height:30px;
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
    height:30px;
    line-height:30px;
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
    height:30px;
    line-height:30px;
    -webkit-border-top-left-radius:3px;
    -webkit-border-bottom-left-radius:3px;
    -moz-border-radius-topleft:3px;
    -moz-border-radius-bottomleft:3px;
    border-top-left-radius:3px;
    border-bottom-left-radius:3px;
  }  

  .skillbar-bar {
    height:30px;
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
    height:30px;
    line-height:30px;
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

  .panel-body {
    padding-bottom: 0;
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

  <div class="container"><!-- Main container -->

    <form method="POST">
      <div class="btn-group" role="group" style="margin-bottom: 20px;">
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Show me my 
              <?php
              if (isset($_GET['day'])) {
                echo $_GET['day'] . " days";
              } elseif (isset($_GET['timeframe'])) {
                echo "Last " . $_GET['timeframe'] . " records";
              }
              ?>
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li class="dropdown-header">Search by satisfaction</li>
            <li><a href="analyze.php?day=great">Great days</a></li>
            <li><a href="analyze.php?day=good">Good days</a></li>
            <li><a href="analyze.php?day=fair">Fair days</a></li>
            <li><a href="analyze.php?day=poor">Poor days</a></li>
            <li class="dropdown-header">Search by timeframe</li>
            <li><a href="analyze.php?timeframe=7">Last 7 records</a></li>
            <li><a href="analyze.php?timeframe=30">Last 30 records</a></li>
            <li><a href="analyze.php?timeframe=90">Last 90 records</a></li>
            <li><a href="analyze.php?timeframe=180">Last 180 records</a></li>
            <li><a href="analyze.php?timeframe=365">Last 365 records</a></li>
          </ul>
        </div>
        <button type="submit" name="clear" class="btn btn-danger" style="margin-left: 20px;">Clear Search</button>
      </div>

<?php
if (!isset($_GET['day']) && !isset($_GET['timeframe'])) {
?>

      <h4>OR</h4>

      <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-6">
          <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Look for keywords within your notes" value="<?php echo $_POST['search']; ?>">
            <span class="input-group-btn">
              <button class="btn btn-default" type="submit" name="searchbtn">Search</button>
            </span>
          </div>
        </div>
      </div>
    </form>

<?php

}//End if (!isset($_GET['day']) || !isset($_GET['timeframe']))

#Satisfaction variables
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

#Goal variables
$goal_yes = 0;
$goal_no = 0;

#Fun variables
$fun_yes = 0;
$fun_no = 0;

$sleepAvg = 0;


//Initialize empty $notes array
$notes = array();

for ($i=0; $row = $result->fetch(PDO::FETCH_ASSOC); $i++) { 

  //Append the $notes array to include the recordDate as the key and the note as the value
  $notes[$row['recordDate']] = $row['notes'];
 
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

  #goal
  switch ($row['goal']) {
    case 'Yes':
      $goal_yes++;
      break;
    case 'No':
      $goal_no++;
      break;
    default:
      break;
  }//End switch ($row['goal'])

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

//Goal
$per_goal = $goal_yes / $numRecords;
$per_nogoal = $goal_no / $numRecords;

//Fun
$per_fun = $fun_yes / $numRecords;
$per_nofun = $fun_no / $numRecords;

//Sleep
$foo = $sleepAvg / $numRecords;

/*
Prints the results container only if the query != it's default value, which is blank.
In other words, if no query is selected, no results will be printed.
*/
if ($stmt != "" && !isset($_POST['search'])) {
?>  
    

<?php
if ($numRecords == 0) { //If no records exist...
?>
      <p>You have no records for your selected search.</p>

<?php
} else {
  if (isset($_GET['timeframe'])) {
?>

      <div class="panel panel-primary">
        <div class="panel-heading">Satisfaction Overview</div>
        <div class="panel-body">          

          <div class="skillbar clearfix" data-percent="<?php echo round((float)($great / $numRecords) * 100) . '%'; ?>">
            <div class="skillbar-title" style="background: #A95C03;"><span>Great</span></div>
            <div class="skillbar-bar" style="background: #D87B0F;"></div>
            <div class="skill-bar-percent"><?php echo $great . ' day(s)'; ?></div>
          </div> <!-- End Skill Bar --> 

          <div class="skillbar clearfix" data-percent="<?php echo round((float)($good / $numRecords) * 100) . '%'; ?>">
            <div class="skillbar-title" style="background: #A95C03;"><span>Good</span></div>
            <div class="skillbar-bar" style="background: #D87B0F;"></div>
            <div class="skill-bar-percent"><?php echo $good . ' day(s)'; ?></div>
          </div> <!-- End Skill Bar -->

          <div class="skillbar clearfix" data-percent="<?php echo round((float)($fair / $numRecords) * 100) . '%'; ?>">
            <div class="skillbar-title" style="background: #A95C03;"><span>Fair</span></div>
            <div class="skillbar-bar" style="background: #D87B0F;"></div>
            <div class="skill-bar-percent"><?php echo $fair . ' day(s)'; ?></div>
          </div> <!-- End Skill Bar -->

          <div class="skillbar clearfix" data-percent="<?php echo round((float)($poor / $numRecords) * 100) . '%'; ?>">
            <div class="skillbar-title" style="background: #A95C03;"><span>Poor</span></div>
            <div class="skillbar-bar" style="background: #D87B0F;"></div>
            <div class="skill-bar-percent"><?php echo $poor . ' day(s)'; ?></div>
          </div> <!-- End Skill Bar -->

        </div>     
      </div>  

<?php
}//End if (isset($_GET['timeframe']))
?>      
  
      <!-- Display cards of data -->
      <div class="row">

        <div class="col-lg-6 col-md-6 col-sm-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you exercise?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_exercise * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_exercise * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_noexercise * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #95000B;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #E02331;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_noexercise * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- Close <div class="col-lg-6"> -->

        <div class="col-lg-6 col-md-6 col-sm-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you follow a good diet?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_diet * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_diet * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_nodiet * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #95000B;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #E02331;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_nodiet * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- Close <div class="col-lg-6"> -->
      </div><!-- Close <div class="row"> -->

      <div class="row">

        <div class="col-lg-6 col-md-6 col-sm-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you make progress towards a goal or project?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_goal * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_goal * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_nogoal * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #95000B;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #E02331;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_nogoal * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- End <div class="col-lg-6> -->

        <div class="col-lg-6 col-md-6 col-sm-6">
          <div class="panel panel-primary">
            <div class="panel-heading">Did you set aside time to have fun?</div>
            <div class="panel-body">          

              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_fun * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #27ae60;"><span>YES</span></div>
                <div class="skillbar-bar" style="background: #2ecc71;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_fun * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar -->                        
              
              <div class="skillbar clearfix" data-percent="<?php echo round((float)$per_nofun * 100) . '%'; ?>">
                <div class="skillbar-title" style="background: #95000B;"><span>NO</span></div>
                <div class="skillbar-bar" style="background: #E02331;"></div>
                <div class="skill-bar-percent"><?php echo round((float)$per_nofun * 100) . '%'; ?></div>
              </div> <!-- End Skill Bar --> 

            </div>     
          </div>
        </div><!-- End <div class="col-lg-6"> -->
      </div><!-- End <div class="row"> -->

      <div class="panel panel-primary">
        <div class="panel-heading">Sleep Average</div>
        <div class="panel-body">          

          <div class="skillbar clearfix" data-percent="<?php echo ($foo <= 8) ? round((float)(($foo / 8) * 100), 2) . '%' : '100%'; ?>">
            <div class="skillbar-title-wider" style="background: #27ae60;"><span>SLEEP</span></div>
            <div class="skillbar-bar" style="background: #2ecc71;"></div>
            <div class="skill-bar-percent"><?php echo round((float)$foo, 2) . " hours" ?></div>
          </div> <!-- End Skill Bar -->

        </div>     
      </div>

      <p>Number of records: <?php echo $numRecords; ?></p>

<?php
}//End if ($numRecords == 0) else...
}//End if ($stmt != "")

if (isset($_POST['searchbtn'])) {
?>
    <p>Number of records matching your search: <?php echo $numRecords; ?></p>

<?php
//Iterate through $notes array to print key->value pairs of entries
foreach ($notes as $day => $note) {
?>

    <div class="well" style="padding: 5px 10px;">
      <p style="margin-bottom: 5px;">Date: <strong><?php echo $day; ?></strong></p> 
      <p style="margin-bottom: 5px;"><?php echo $note; ?></p>
    </div>       

<?php
}//End foreach ($notes as $day => $note)
}//End if (isset($_POST['searchbtn']))
?>

  </div><!-- Close <div class="container"> -->

</body>

</html>