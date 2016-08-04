<?php

session_start();

//Redirect to login screen if no user is logged in
if (!isset($_SESSION['username'])) {
  header('Location: http://lifetracker.case-break.com');
}

$page = "tracker";

if(isset($_POST['submit'])) {

  function clean($data) {
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;    
  }  

  try {

    $user = $_SESSION['username'];
    $recordDate = $_POST['date'];
    $sleep = $_POST['sleep'];
    $exercise = $_POST['exercise'];
    $food = $_POST['food'];
    $goal = $_POST['goal'];
    $fun = $_POST['fun'];
    $satisfaction = $_POST['satisfaction'];
    $notes = clean($_POST['notes']);

    include('includes/inc_connect.php');

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
}
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
        <input type="text" id="date" class="form-control" name="date" />
        <script>$( "#date" ).dateDropper();</script>
      </div>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon1">Hours of Sleep (last night)</span>
        <input type="number" class="form-control" name="sleep" step="any" required>
      </div>

      <select class="form-control" name="exercise" required>
        <option value="" disabled selected>Did you exercise?</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>

      <select class="form-control" name="food" required>
        <option value="" disabled selected>Did you follow a good diet?</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>

      <select class="form-control" name="goal" required>
        <option value="" disabled selected>Did you make progress on a goal or project?</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>

      <select class="form-control" name="fun" required>
        <option value="" disabled selected>Did you set aside time to have fun?</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>      

      <select class="form-control" name="satisfaction" required>
        <option value="" disabled selected>How would you rate your day?</option>
        <option value="Great">Great</option>
        <option value="Good">Good</option>
        <option value="Fair">Fair</option>
        <option value="Poor">Poor</option>
      </select>

      <textarea name="notes" class="form-control" rows="5" col="100" maxlength="350" placeholder="Enter any additional notes here (Optional, 350 max characters)"></textarea>

      <button class="btn btn-success" type="submit" name="submit" value="Submit" style="margin-bottom: 20px">Submit</button>

    </form><!-- close form -->
  </div><!-- close container -->

</body>
</html>