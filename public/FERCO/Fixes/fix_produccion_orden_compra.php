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

// Buscar OP con ordenes en blanco
$query = "SELECT interno FROM produccion_final WHERE orden_compra = '' AND interno != '' AND estado != 'Anulado'";
$result = mysql_query($query) or die("SQL ERROR #1: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Internos[] = $row["interno"];
	}
}
$Clause = implode("', '", $Internos);
// Tomar las ordenes y actualizar las OP
$query = "SELECT interno, orden_compra FROM fact_final WHERE interno IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL ERROR #2-1: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "UPDATE produccion_final SET 
		orden_compra = '".$row["orden_compra"]."' WHERE interno = '".$row["interno"]."'";
		$result1 = mysql_query($query) or die("SQL ERROR #2-2: ".mysql_error());
		
		$query = "UPDATE produccion_movs SET 
		orden_compra = '".$row["orden_compra"]."' WHERE interno = '".$row["interno"]."'";
		$result1 = mysql_query($query) or die("SQL ERROR #2-2: ".mysql_error());
	}
}
echo "Numero de Filas -> ".mysql_num_rows($result)."<br/>";

echo "- Finalizado<br/><br/>";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
echo "<br/>";
