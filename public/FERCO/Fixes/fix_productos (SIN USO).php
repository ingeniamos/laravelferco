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

echo "Productos";
echo "<br/>";

$query = "SELECT CodFab, IF(`Facturas sin existencia` > 0, 'true', 'false') AS factura, IF(Produccion > 0, 'true', 'false') AS produccion FROM productos_copy";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "UPDATE productos SET factura_sin_existencia = '".$row["factura"]."', 
		produccion = '".$row["produccion"]."' WHERE cod_fab = '".$row["CodFab"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";
?>