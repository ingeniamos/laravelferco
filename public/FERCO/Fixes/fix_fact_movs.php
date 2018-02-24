<?php
$hostname = "localhost";
$username = "root";
$password = "ingeniamos";
$database = "ferco_local";

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

$query = "SELECT DISTINCT(interno) FROM fact_movs";
$result = mysql_query($query) or die("SQL ERROR #1-1: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Internos[] = $row["interno"];
	}
}
echo "Numero de Facturas1 -> ".mysql_num_rows($result)."<br/>";
$Clause = implode("', '", $Internos);

$query = "SELECT interno FROM fact_final WHERE interno IN ('".$Clause."') ORDER BY interno";
$result = mysql_query($query) or die("SQL ERROR #1-2: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$found = array_search($row["interno"], $Internos);
		unset($Internos[$found]);
	}
}
print_r($Internos);
echo "<br />";
echo "Facturas Sobrantes: ".count($Internos)."<br />";
echo "Numero de Facturas2 -> ".mysql_num_rows($result)."<br/>";

foreach ($Internos AS $Interno)
{
	$query = "DELETE FROM fact_movs WHERE interno = '".$Interno."' ";
	$result = mysql_query($query) or die("SQL ERROR #1-3: ".mysql_error());
	
	$query = "DELETE FROM inventario_movs WHERE interno = '".$Interno."' ";
	$result = mysql_query($query) or die("SQL ERROR #1-4: ".mysql_error());
}
echo "<br />";
echo "- Finalizado<br/>";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
echo "<br/>";
