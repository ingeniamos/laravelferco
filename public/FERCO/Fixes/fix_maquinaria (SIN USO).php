<?php
$hostname = "localhost";
$username = "root";
$password = "ingeniamos";
$database = "ferco_new";

$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);
$bool = mysql_select_db($database, $connect);
if ($bool === False){
	print "can't find ".$database."";
}

if (!isset($_GET['START']))
{
	echo "START para Iniciar el Proceso";
	die();
}

echo "Maquinaria Final";
echo "<br/>";

$query = "SELECT * FROM maqs_final1";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "UPDATE maquinaria_final SET estado = '".$row["Estado"]."' WHERE ord_reparacion = '".$row["orden"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";
?>