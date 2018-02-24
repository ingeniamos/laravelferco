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

echo "Movimientos de Produccion con Origen y Destino dañados";
echo "<br/>";
/*
// BORRAR DESPUNTES DE PRODUCCION_MOVS
$query = "DELETE FROM produccion_movs WHERE tipo = 'Despunte'";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

// INTERCAMBIAR ORIGEN Y DESTINO EN PRODUCCION_PROC_MOVS DONDE TIPO = 'DESPUNTE'
$query = "SELECT * FROM produccion_proc_movs WHERE tipo = 'Despunte'";
$result = mysql_query($query) or die("SQL Error 2-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "UPDATE produccion_proc_movs SET origen = '".$row["destino"]."', destino = '".$row["origen"]."' WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2-2: " . mysql_error());
	}
}
*/
// INTERCAMBIAR ORIGEN Y DESTINO EN PRODUCCION_MOVS DONDE TIPO = 'OBTENER'
$query = "SELECT produccion_proc_movs.*, produccion_movs.id AS p_movs_id, produccion_movs.origen AS p_movs_o, produccion_movs.destino AS p_movs_d 
FROM produccion_proc_movs LEFT JOIN produccion_movs 
ON produccion_proc_movs.orden_produccion = produccion_movs.orden_produccion 
AND produccion_proc_movs.codigo = produccion_movs.codigo 
WHERE produccion_proc_movs.tipo = 'Obtener' ";
$result = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		/*echo "".$row["orden_produccion"]." - ".$row["origen"]." - ".$row["destino"]."";
		echo "<br />";
		echo "".$row["p_movs_id"]." - ".$row["p_movs_o"]." - ".$row["p_movs_d"]."";
		echo "<br />";
		*/
		$query = "UPDATE produccion_movs SET origen = '".$row["origen"]."', destino = '".$row["destino"]."' WHERE id = '".$row["p_movs_id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
	}
}

echo mysql_num_rows($result);

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>