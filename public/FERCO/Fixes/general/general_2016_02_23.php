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

echo "* Actualizar Vigencias de Cupo Credito<br/>";

$query = "UPDATE clientes SET vigencia_credito = '2016-12-31'";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Borrar Movimientos de Remisiones con Forma de Pago en Blanco.<br/>";

$query = "SELECT * FROM fact_final WHERE forma_pago = ''";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "DELETE FROM fact_movs WHERE interno = '".$row["interno"]."'";
		$result1 = mysql_query($query) or die("SQL Error 3: " . mysql_error());
		echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";
	}
}

echo "* Borrar Remisiones con Forma de Pago en Blanco.<br/>";

$query = "DELETE FROM fact_final WHERE forma_pago = ''";
$result = mysql_query($query) or die("SQL Error 4: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "<br/>";
echo "Final";
echo "<br/>";
