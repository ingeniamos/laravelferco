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

echo "Produccion Process Movs";
echo "<br/>";

$query = "SELECT DISTINCT operario FROM produccion_proc_movs WHERE operario != ''";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "SELECT cliente_id FROM clientes WHERE nombre = '".$row[0]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			//echo $row1[0];
			//echo "<br />";
			$query = "UPDATE produccion_proc_movs SET operario = '".$row1[0]."' WHERE operario = '".$row[0]."'";
			$result2 = mysql_query($query) or die("SQL Error 3: " . mysql_error());
		}
	}
}

echo "- Finalizado<br/><br/>";
?>