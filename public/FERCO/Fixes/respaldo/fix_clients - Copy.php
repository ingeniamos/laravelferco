<?php
include('modulos/config.php');

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);
$bool = mysql_select_db($database, $connect);
if ($bool === False){
  print "can't find ".$database."";
}

$query = "SELECT id, barrio, ciudad, departamento FROM par_ter_ciudad_copy";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
$Num = mysql_num_rows($result);
if ($Num > 0)
{
	printf("Seleccion completada!<br/>Existen ".$Num." Records<br/>");
	
	while($row = mysql_fetch_array($result))
	{
		$query = "UPDATE clientes SET barrio = '".$row["barrio"]."', ciudad = '".$row["ciudad"]."', departamento = '".$row["departamento"]."' 
		WHERE ubicacion_id = '".$row["id"]."' ";
		$result1 = mysql_query($query) or die("SQL Error 1: " . mysql_error());
	}
	
	printf("Update Finalizado!!<br/>");
}

?>