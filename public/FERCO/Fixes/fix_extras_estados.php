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

// Time Check!
$now = time();
echo "Tiempo de Inicio-> ".$now." <br/><br/>";

echo "Extras_Movs";
echo "<br /><br />";

//Actualizar los recibos de caja con el interno del prestamo
$query = "SELECT interno, estado FROM nom_extras";
$result = mysql_query($query) or die("SQL Error 1-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$query = "UPDATE nom_extras_movs SET estado = '".$row["estado"]."' WHERE interno = '".$row["interno"]."' ";
		$result1 = mysql_query($query) or die("SQL Error 1-2: " . mysql_error());
		echo "nom_extras_movs -> ".$row["interno"]." -> ".mysql_affected_rows()." filas afectadas...<br />";
	}
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>