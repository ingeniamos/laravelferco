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

//---------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------

echo "* Actualizar Cotizacion sin Acento.<br/>";

$query = "UPDATE fact_final SET estado = 'Cotizacion' WHERE estado = 'Cotización'";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Actualizar Recibos de Caja en blanco<br/>";

$query = "UPDATE caja_final SET caja_recibo = caja_interno WHERE caja_recibo = ''";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "<br/>";
echo "Final";
echo "<br/>";
