<?php
$hostname = "localhost";
$username = "root";
$password = "ingeniamos";
$database = "ferco";

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

echo "Arreglar Internos";
echo "<br/>";

$query = "SELECT * FROM produccion_final WHERE interno != '' ORDER BY interno";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		if (!isset($Op[$row["interno"]]))
			$Op[$row["interno"]] = $row["orden_produccion"];
	}
}

$query = "SELECT * FROM inventario_movs WHERE motivo = 'Produccion' AND interno LIKE 'FER%'";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		echo $row["interno"]." - ";
		echo $Op[$row["interno"]]."<br />";
		
		//$query = "UPDATE inventario_movs SET interno = '".$Op[$row["interno"]]."' WHERE id = '".$row["id"]."'";
		//$result1 = mysql_query($query) or die("SQL Error 3: " . mysql_error());
	}
	echo mysql_num_rows($result);
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>