<?php
/* config section */
$db_connect = 'mysql:dbname=the_lifetracker;host=localhost';
$db_user = 'casebreakdefault';
$db_pass = '4#2a%.P}Ei^0';

/* end of config */

try {
	$db = new PDO($db_connect,$db_user,$db_pass);
}
catch (PDOException $e)
{
	echo 'Connection failed: '.$e->getMessage();
	die();
}
?>