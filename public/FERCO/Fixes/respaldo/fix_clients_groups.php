<?php
include('modulos/config.php');

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);
$bool = mysql_select_db($database, $connect);
if ($bool === False){
  print "can't find ".$database."";
}

$query = "SELECT cliente_id, tipo_cliente, tipo_tercero FROM clientes";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
$Num = mysql_num_rows($result);
if ($Num > 0)
{
	printf("Seleccion completada!<br/>Existen ".$Num." Records<br/>");
	
	while($row = mysql_fetch_array($result))
	{
		$query1 = "INSERT INTO cliente_grupo (cliente_id, clasificacion, tipo) VALUES 
		('".$row["cliente_id"]."', '".$row["tipo_tercero"]."', '".$row["tipo_cliente"]."')";
		$result1 = mysql_query($query1) or die("SQL Error 2: " . mysql_error());
	}
	
	printf("Insert Finalizado!!<br/>");
}

?>