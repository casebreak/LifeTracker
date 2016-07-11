<?php

session_start();

include('includes/inc_connect.php');

$user = $_POST['user'];
$pass = md5($_POST['pass']);

if (isset($_POST['login'])) {

  $result = $db->prepare("SELECT * FROM users WHERE username = :username");
  $result->bindParam(':username',$user);
  $result->execute();

  $check = $result->fetch(PDO::FETCH_ASSOC);

  if (($pass === $check['pw']) && ($user === $check['username'])) {

    $_SESSION['user'] = $check['username'];
    $_SESSION['username'] = $check['username'];
    $_SESSION['password'] = $check['password'];
    $_SESSION['fname'] = $check['fname'];
    $_SESSION['lname'] = $check['lname'];
    $_SESSION['email'] = $check['email'];

    if ($check['adminFlag']) { $_SESSION['admin'] = TRUE; } 
      else { $_SESSION['admin'] = FALSE; }

    $db = null;
    header("location: tracker.php");

  } else { echo "<div class='alert alert-danger' role='alert'><h3>Invalid Credentials</h3></div>"; }
}
?>

<!doctype html>
<html>
<head>

  <?php include("includes/inc_links.php"); ?>

  <style type="text/css">

    form div, select, textarea, button, .bottom {
      margin-bottom: 20px;
    }

  </style>
</head>
<body>

<div class="container">

  <div class="row">

    <div class="col-lg-6 col-lg-offset-3 col-xs-8 col-xs-offset-2 bottom" style="margin-top: 20px;">
      <h1 style="display: inline;">Life Tracker</h1>
    </div>

    <div class="col-lg-6 col-lg-offset-3 col-xs-8 col-xs-offset-2" style="padding:0;">

      <!-- Login form -->
      <form action="" method="POST">

        <!-- Username -->
        <div class="input-group input-group-lg">
          <span class="input-group-addon" id="sizing-addon1"><span class="glyphicon glyphicon-user"></span></span>
          <input type="text" name="user" class="form-control" placeholder="Username" required>
        </div>

        <!-- Password -->
        <div class="input-group input-group-lg">
          <span class="input-group-addon" id="sizing-addon1"><span class="glyphicon glyphicon-asterisk"></span></span>
          <input type="password" name="pass" class="form-control" placeholder="Password" required>
        </div>

        <button type="submit" name="login" class="btn btn-lg btn-success pull-right" style="margin-top:10px;">Login</button>

      </form>
      <br>

      <!-- Create account link -->
      <strong>Dont have an account? <a href="register.php">Create one here.</a></strong>

      <!-- Forgot password -->
      <strong>Oops! I <a href="includes/inc_forgot.php">forgot my password!</a></strong>

    </div>
  </div>
</div>

</body>
</html>