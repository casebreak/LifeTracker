<?php

session_start();

//Redirect to login screen if no user is logged in
// if (!isset($_SESSION['username'])) {
//   header('Location: http://lifetracker.case-break.com');
// }

$errCount = 0;
$duplicate = FALSE;
$success = FALSE;

if (isset($_POST['register'])) {
  if (isset($_POST['username'])) {
    $username_value = $_POST['username'];
  }
  if (isset($_POST['password'])) {
    $password_value = $_POST['password'];
  }
  if (isset($_POST['confirm'])) {
    $confirm_value = $_POST['confirm'];
  }
  if (isset($_POST['email'])) {
    $email_value = $_POST['email'];
  }
  if (isset($_POST['fname'])) {
    $fname_value = $_POST['fname'];
  }
  if (isset($_POST['lname'])) {
    $lname_value = $_POST['lname'];
  }
  if (isset($_POST['question'])) {
    $question_value = $_POST['question'];
  }
  if (isset($_POST['answer'])) {
    $answer_value = $_POST['answer'];
  }

  /* START VALIDATION */
  function clean($data) {
    $data = trim($data);
    $trans = array(" " => "", "-" => "", "(" => "", ")" => "");
    $data = strtr($data, $trans);
    return $data;    
  }

  function validUserName($data) {
    include('includes/inc_connect.php');
    global $errCount;
    global $duplicate;
    $data = clean($data);
    $pattern = "/^\w{4,25}$/";
    $result = $db->prepare("SELECT * FROM users WHERE username = :username");
    $result->bindParam(':username',$data);
    $result->execute();
    $numRows = $result->fetchColumn();
    if ($numRows > 0) {
      $duplicate = TRUE;
      $errCount++;
    }
    if (!preg_match($pattern, $data)) {
      $errCount++;
    } else {
      return $data;
    }   
  }

  function validPassword($data) {
    global $errCount;
    $data = trim($data);
    $pattern = "/^.{6,21}$/";
    if (!preg_match($pattern, $data)) {
      $errCount++;
    } else {
      return md5($data);
    }   
  }

  function validConfirm($data) {
    global $errCount;
    $data = trim($data);  
    if ($_POST['confirm'] != $_POST['password']) {
      $errCount++;
    } else {
      return md5($data);
    }
  }

  function validEmail($data) {
    include('includes/inc_connect.php');
    global $errCount;
    global $duplicate;
    $data = trim($data);
    $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[_a-z0-9-]+(\.[_a-z0-9-]+)*(\.[a-z]{2,5})$/i";
    $result = $db->prepare("SELECT * FROM users WHERE email = :email");
    $result->bindParam(':email',$data);
    $result->execute();
    $numRows = $result->fetchColumn();
    if ($numRows > 0) {
      $duplicate = TRUE;
      $errCount++;
    }
    if (!preg_match($pattern, $data)) {
      $errCount++;
    } else {
      return $data;
    }   
  }

  function validFName($data) {
    global $errCount;
    $data = trim($data);
    if (empty($data)) {
      $errCount++;
    } else {
      return $data;
    }
  }

  function validLName($data) {
    global $errCount;
    $data = trim($data);
    if (empty($data)) {
      $errCount++;
    } else {
      return $data;
    }
  }

  $un = validUserName($_POST['username']);
  $pw = validPassword($_POST['password']);
  $em = validEmail($_POST['email']);
  $fn = validFName($_POST['fname']);
  $ln = validLName($_POST['lname']);
  $foobar = validConfirm($_POST['confirm']);

  if ($errCount > 0) {
  	echo "<div class='alert alert-danger' role='alert'>";
  	echo "<h4 style='margin:0;'>Please fix your ".$errCount." error(s) in the form below.</h4>";
  	echo "</div>";
  } else {
    try {

      include('includes/inc_connect.php');
      $sql = "INSERT INTO users (username,
                                 pw,
                                 fname,
                                 lname,
                                 email,
                                 question,
                                 answer)
                                 VALUES
                                (:username,
                                 :pw,
                                 :fname,
                                 :lname,
                                 :email,
                                 :question,
                                 :answer)";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':username',$un);
      $stmt->bindParam(':pw',$pw);
      $stmt->bindParam(':email',$em);
      $stmt->bindParam(':fname',$fn);
      $stmt->bindParam(':lname',$ln);
      $stmt->bindParam(':question',$_POST['question']);
      $stmt->bindParam(':answer',$_POST['answer']);
      // $stmt->bindParam(':metric1',$_POST['metric1']);
      // $stmt->bindParam(':metric2',$_POST['metric2']);
      // $stmt->bindParam(':metric3',$_POST['metric3']);
      // $stmt->bindParam(':metric4',$_POST['metric4']);
      // $stmt->bindParam(':metric5',$_POST['metric5']);

      if ($stmt->execute()) {
        $success = TRUE;
        echo "<div class='alert alert-success' role='alert' style='line-height:34px;overflow:auto;'>";
        echo "<h4 style='margin:0;'>Registration Successful!<a class='btn btn-primary pull-right' style='margin-top:-7px;' href='index.php'>Login</a></h4>";
        echo "</div>";
      }
    }//End try
    catch(PDOException $e) {
      echo "<div class='alert alert-danger' role='alert'>";
      echo "<h4 style='margin:0;'>Something went wrong. Please contact us or try again later.</h4>";
      echo "Error: ".$e->getMessage();
      echo "</div>";      
    }
  }//End if
}//End if
?>

<!doctype html>
<html>
<head>

  <?php include("includes/inc_links.php"); ?>

  <style type="text/css">

    form div, select, textarea, button {
      margin-bottom: 20px;
    }

    .wrong {
      text-align: center;
    }

  </style>
</head>
<body>
        
  <?php include("includes/inc_nav.php"); ?>

  <div class="container"><!-- Main container -->

    <form method="POST">

    	<h3>Account Information</h3>

      <?php 
      if (isset($_POST['register'])) {
        if (!validUserName($_POST['username'])) {
          echo "<p class='wrong bg-danger text-danger'>Please enter a valid Username (Minimum 4 characters)</p>"; 
        }
        if (($duplicate) && (!$success)) {
          echo "<p class='wrong bg-danger text-danger'>Username already taken. Please choose another Username.</p>"; 
        }
      }
      ?>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Username</span>
        <input type="text" name="username" class="form-control" value="<?php echo $username_value ?>" required>
      </div>

      <?php 
      if (isset($_POST['register']) && (!validPassword($_POST['password']))) {
        echo "<p class='wrong bg-danger text-danger'>Please enter a valid Password (Minimum 6 characters)</p>"; 
      }
      ?>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Password</span>
        <input type="password" name="password" class="form-control" value="<?php echo $password_value ?>" required>
      </div>

      <?php 
      if (isset($_POST['register']) && (!validConfirm($_POST['confirm']))) {
          echo "<p class='wrong bg-danger text-danger'>Passwords must match.</p>"; 
      }
      ?>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Confirm Password</span>
        <input type="password" name="confirm" class="form-control" value="<?php echo $confirm_value ?>" required>
      </div>

      <?php 
      if (isset($_POST['register'])) {
        if (!validEmail($_POST['email'])) {
          echo "<p class='wrong bg-danger text-danger'>Please enter a valid Email (name@example.com)</p>"; 
        }
        if (($duplicate) && (!$success)) {
          echo "<p class='wrong bg-danger text-danger'>This email is already registered to a different account. Choose a different email or <a href='index.php'>Login</a></p>"; 
        }
      }
      ?>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Email</span>
        <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?php echo $email_value ?>" required>
      </div>

      <div class="input-group">
      	<span class="input-group-addon" id="basic-addon3">First Name</span>
        <input type="text" name="fname" class="form-control" value="<?php echo $fname_value; ?>" required>
        <span class="input-group-addon" id="basic-addon3">Last Name</span>
        <input type="text" name="lname" class="form-control" value="<?php echo $lname_value; ?>" required>
      </div>

      <p><strong>Please note: At this time, password recovery is not implemented. Please fill out this section anyway.</strong></p>

      <select class="form-control" name="question" required>
        <option value="" disabled selected>Select a secret question for password recovery</option>
        <option <?php if($question_value=="Favorite movie?") echo "selected='selected'"; ?> value="Favorite movie?">Favorite movie</option>
        <option <?php if($question_value=="Mother's Maiden name") echo "selected='selected'"; ?> value="Mother's Maiden name">Mother's Maiden name</option>
        <option <?php if($question_value=="First pet's name") echo "selected='selected'"; ?> value="First pet's name">First pet's name</option>
        <option <?php if($question_value=="City you were born in") echo "selected='selected'"; ?> value="City you were born in">City you were born in</option>
      </select>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Answer to above question</span>
        <input type="text" name="answer" class="form-control" value="<?php echo $answer_value; ?>" required>
      </div> 

<!--  Uncomment for users to enter their own metrics

      <h3>You now get to enter the metrics you wish to track. Please enter 5 Yes or No questions that pertain to things you wish to keep track of every day. Some common metrics are:
        <ul>
          <li>Did you eat a good diet today?</li>
          <li>Did you make progress towards a goal?</li>
          <li>Did you exercise today?</li>
          <li>Did you do something to help others today?</li>
        </ul>
      </h3>  

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Metric #1</span>
        <input type="text" name="metric1" class="form-control" required>
      </div>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Metric #2</span>
        <input type="text" name="metric2" class="form-control" required>
      </div>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Metric #3</span>
        <input type="text" name="metric3" class="form-control" required>
      </div>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Metric #4</span>
        <input type="text" name="metric4" class="form-control" required>
      </div>

      <div class="input-group">
        <span class="input-group-addon" id="basic-addon3">Metric #5</span>
        <input type="text" name="metric5" class="form-control" required>
      </div>   

-->

      <button type="submit" name="register" class="btn btn-success">Register</button>

    </form>
  </div>

</body>
</html>
