<?php
session_start();

include('config.php');

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);

$bool = mysql_select_db($database, $connect);

if ($bool === False){
  print "can't find ".$database."";
}

if (isset($_SESSION["UserID"]))
{
	if (isset($_SESSION["UserAccess"]) && $_SESSION["UserAccess"][0]["DB"] != "")
	{
		$query = "INSERT INTO ".$_SESSION["UserAccess"][0]["DB"].".login_log (user_id, type, time) VALUES ('".$_SESSION["UserID"]."', 'logout', NOW())";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	}
	$query = "INSERT INTO login_log (user_id, type, time) VALUES ('".$_SESSION["UserID"]."', 'logout', NOW())";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
session_unset();
session_destroy();
//header('Location: ../index.php');
header('Location: ../');
exit;
?>