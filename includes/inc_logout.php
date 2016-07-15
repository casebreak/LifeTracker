<?php
session_start();

session_destroy();

header("location: http://lifetracker.case-break.com"); 
?>