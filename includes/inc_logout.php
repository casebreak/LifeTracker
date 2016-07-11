<?php
session_start();

session_destroy();

header("Location: http://lifetracker.case-break.com");
?>