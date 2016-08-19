<?php

session_start();

//Redirect to login screen if no user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: http://lifetracker.case-break.com');
}

$page = "tracker";
$duplicate = FALSE;

if(isset($_POST['submit'])) {
  if (isset($_POST['sleep'])) {
    $sleepValue = $_POST['sleep'];
  }
  if (isset($_POST['exercise'])) {
    $exerciseValue = $_POST['exercise'];
  }
  if (isset($_POST['food'])) {
    $foodValue = $_POST['food'];
  }
  if (isset($_POST['goal'])) {
    $goalValue = $_POST['goal'];
  }
  if (isset($_POST['fun'])) {
    $funValue = $_POST['fun'];
  }
  if (isset($_POST['satisfaction'])) {
    $satisfactionValue = $_POST['satisfaction'];
  }
  if (isset($_POST['notes'])) {
    $notesValue = $_POST['notes'];
  }

  include('includes/inc_connect.php');

  function clean($data) {
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;    
  }

  //This function checks for a duplicate recordDate and ensures the date field is not left blank.
  function duplicate($data) {
    include('includes/inc_connect.php');
    global $duplicate;
    $dupCheck = $db->prepare("SELECT * FROM records WHERE user = :user AND recordDate = :recordDate ");
    $dupCheck->bindParam(':user',$_SESSION['username'], PDO::PARAM_STR);
    $dupCheck->bindParam(':recordDate',$data, PDO::PARAM_STR);
    $dupCheck->execute();
    $numRows = $dupCheck->fetchColumn();
    if ($numRows > 0) {
      $duplicate = TRUE;
      echo "<div class='alert alert-danger' role='alert' style='line-height:34px;overflow:auto;'>";
      echo "<h4 style='margin:0;'>There is already an entry on this date. Please select a unique date.</h4>";
      echo "</div>";  
    } elseif ($_POST['recordDate'] == "") {
      $duplicate = TRUE;
      echo "<div class='alert alert-danger' role='alert' style='line-height:34px;overflow:auto;'>";
      echo "<h4 style='margin:0;'>The date field must not be left blank.</h4>";
      echo "</div>";
    } else {
      return $data;
    }
  }

  $recordDate = duplicate($_POST['date']);

  if ($duplicate == FALSE) {

    try {

      $user = $_SESSION['username'];
      $sleep = $_POST['sleep'];
      $exercise = $_POST['exercise'];
      $food = $_POST['food'];
      $goal = $_POST['goal'];
      $fun = $_POST['fun'];
      $satisfaction = $_POST['satisfaction'];
      $notes = base64_encode(clean($_POST['notes']));

      $query = "INSERT INTO records (user,
                                    recordDate,
                                    sleep,
                                    exercise,
                                    food,
                                    goal,
                                    fun,
                                    satisfaction,
                                    notes) 
                                    VALUES 
                                   (:user,
                                    :recordDate,
                                    :sleep,
                                    :exercise,
                                    :food,
                                    :goal,
                                    :fun,
                                    :satisfaction,
                                    :notes)";
      $stmt = $db->prepare($query);
      $stmt->bindParam(':user',$user, PDO::PARAM_STR);
      $stmt->bindParam(':recordDate',$recordDate, PDO::PARAM_STR);
      $stmt->bindParam(':sleep',$sleep, PDO::PARAM_STR);
      $stmt->bindParam(':exercise',$exercise, PDO::PARAM_STR);
      $stmt->bindParam(':food',$food, PDO::PARAM_STR);    
      $stmt->bindParam(':goal',$goal, PDO::PARAM_STR);
      $stmt->bindParam(':fun',$fun, PDO::PARAM_STR);
      $stmt->bindParam(':satisfaction',$satisfaction, PDO::PARAM_STR);
      $stmt->bindParam(':notes',$notes, PDO::PARAM_STR);

      if ($stmt->execute())
      {
        $success = TRUE;
        echo "<div class='alert alert-success' role='alert' style='line-height:34px;overflow:auto;'>";
        echo "<h4 style='margin:0;'>Your record was submitted successfully.</h4>";
        echo "</div>";
      } 
      else 
      {
        echo "<div class='alert alert-danger' role='alert' style='line-height:34px;overflow:auto;'>";
        echo "<h4 style='margin:0;'>There was an error submitting your record.</h4>";
        echo "</div>";        
      }
    }//End try

    catch(PDOException $e) {
      echo "<div class='alert alert-danger' role='alert'>";
      echo "<h4 style='margin:0;'>Something went wrong.</h4>";
      echo "Error: ".$e->getMessage();
      echo "</div>";      
    }
  }//End if ($duplicate == FALSE)
}//End if(isset($_POST['submit']))
?>

<!doctype html>
<html>
<head>

  <?php include("includes/inc_links.php"); ?>

  <!-- Date Picker -->
  <script src="js/datedropper.js"></script>
  <link rel="stylesheet" type="text/css" href="js/datedropper.css">

  <style type="text/css">

    form div, select, textarea, button, .bottom {
      margin-bottom: 20px;
    }

  </style>
</head>
<body>
        
  <?php include("includes/inc_nav.php"); ?>

  <div class="container"><!-- Main container -->

    <!-- Start "tracker" form -->
    <form id="tracker" method="POST">

      <!-- Date picker -->
      <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Date</span>
        <input type="text" id="date" class="form-control" name="date" required>
        <script>$( "#date" ).dateDropper();</script>
      </div>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Hours of Sleep (last night)</span>
        <input type="number" class="form-control" name="sleep" step="any" placeholder="Whole numbers or decimals allowed. '6.25' for example." value="<?php echo $sleepValue; ?>" required>
      </div>

      <select class="form-control" name="exercise" required>
        <option value="" disabled selected>Did you exercise?</option>
        <option <?php echo ($exerciseValue == "Yes") ? "selected='selected'" : ""; ?> value="Yes">Yes</option>
        <option <?php echo ($exerciseValue == "No") ? "selected='selected'" : ""; ?> value="No">No</option>
      </select>

      <select class="form-control" name="food" required>
        <option value="" disabled selected>Did you follow a good diet?</option>
        <option <?php echo ($foodValue == "Yes") ? "selected='selected'" : ""; ?> value="Yes">Yes</option>
        <option <?php echo ($foodValue == "No") ? "selected='selected'" : ""; ?> value="No">No</option>
      </select>

      <select class="form-control" name="goal" required>
        <option value="" disabled selected>Did you make progress on a goal or project?</option>
        <option <?php echo ($goalValue == "Yes") ? "selected='selected'" : ""; ?> value="Yes">Yes</option>
        <option <?php echo ($goalValue == "No") ? "selected='selected'" : ""; ?> value="No">No</option>
      </select>

      <select class="form-control" name="fun" required>
        <option value="" disabled selected>Did you set aside time to have fun?</option>
        <option <?php echo ($funValue == "Yes") ? "selected='selected'" : ""; ?> value="Yes">Yes</option>
        <option <?php echo ($funValue == "No") ? "selected='selected'" : ""; ?> value="No">No</option>
      </select>      

      <select class="form-control" name="satisfaction" required>
        <option value="" disabled selected>How would you rate your day?</option>
        <option <?php echo ($satisfactionValue == "Great") ? "selected='selected'" : ""; ?> value="Great">Great</option>
        <option <?php echo ($satisfactionValue == "Good") ? "selected='selected'" : ""; ?> value="Good">Good</option>
        <option <?php echo ($satisfactionValue == "Fair") ? "selected='selected'" : ""; ?> value="Fair">Fair</option>
        <option <?php echo ($satisfactionValue == "Poor") ? "selected='selected'" : ""; ?> value="Poor">Poor</option>
      </select>

      <textarea name="notes" class="form-control" rows="5" col="100" maxlength="350" placeholder="Enter any additional notes here (Optional, 350 max characters)"><?php echo $notesValue ?></textarea>

      <button class="btn btn-success" type="submit" name="submit" value="Submit" style="margin-bottom: 20px">Submit</button>

    </form><!-- close form -->
  </div><!-- close container -->

</body>
</html>