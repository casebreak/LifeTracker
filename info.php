<!doctype html>
<html>
<head>

  <?php include("includes/inc_links.php"); $page = "info"; ?>

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

    <center><h2 style="color: red;">ATTENTION!<h2></center>
    <h4 style="color: red;">'Search' functionality is now implemented and working properly! Search for a term within your notes on the 'Analyze' page and your search term will be bold, uppercase and highlighted in yellow, along with displaying the entire note with the corresponding date of the entry.</h4>
    <br>
    <h2>Thank you for using Life Tracker!</h2>
    <h3>Life Tracker is designed to help you track some basic metrics about your day in order to help you live a happier and more productive life.</h3>

    <div class="jumbotron">
      <ul>
        <li>At the end of each day, sign in to your Life Tracker profile and use the "Tracker" feature to answer some yes/no questions about your day. Hit submit when you are done and the record will be stored in your database.</li>
        <li>The Notes section of the Tracker is meant to be a mini diary of sorts. Enter anything you'd like in here. You can even use keywords that you'll be able to search for later on.</li>
        <li>Select the "Records" link to be taken to an interactive list of all your records.</li>
        <li>The "Analyze" link will allow you to search and sort your data and display the results using graphs and percentages to see what you are doing well and what you need to improve on.</li>
        <li><strong>The Tracker only works when you remember to complete your entries at the end of each day. For accurate results, please try to use the app every day.</strong></li>
      </ul>
    </div>

    <div class="jumbotron">
      <p>If you have any questions or comments, or to report bugs, please email:</p>
      <p>Scott Duncan<br>codebyscott@gmail.com</p>
      <p>Thank you!</p>
    </div>

  </div><!-- close container -->

</body>
</html>