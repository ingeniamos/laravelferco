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

echo "Inventario Movs";
echo "<br/>";

$query = "SELECT * FROM inventario_movs WHERE cod_fab != '' AND motivo = 'Inicial' AND estado = 'Aprobado' GROUP BY cod_fab";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Inicial[$row["cod_fab"]] = $row["cantidad"];
		/*echo $row["cod_fab"];
		echo " - ";
		echo $row["cantidad"];
		echo "<br />";*/
	}
}
// group by, se come algunos... es mejor por ORDER BY

$query = "SELECT * FROM inventario_movs WHERE estado = 'Aprobado' ORDER BY cod_fab, fecha ASC";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

if (mysql_num_rows($result) > 0)
{
	$LastCode = "_empty_";
	$Existencia = 0;
	while($row = mysql_fetch_array($result))
	{
		if ($LastCode != $row["cod_fab"])
		{
			$LastCode = $row["cod_fab"];
			$Existencia = isset($Inicial[$row["cod_fab"]]) ? $Inicial[$row["cod_fab"]]:0;
			
			if ($row["motivo"] != "Inicial")
			{
				$query = "UPDATE inventario_movs SET existencia = '".$Existencia."' WHERE id = '".$row["id"]."'";
				$result1 = mysql_query($query) or die("SQL Error 2-1: " . mysql_error());
			}
		}
		else
		{
			$query = "UPDATE inventario_movs SET existencia = '".$Existencia."' WHERE id = '".$row["id"]."'";
			$result1 = mysql_query($query) or die("SQL Error 2-2: " . mysql_error());
			
			if ($row["tipo"] == "Entrada" && $row["motivo"] != "Inicial")
			{
				$Existencia += $row["cantidad"];
				
				//$query = "UPDATE inventario_movs SET existencia = existencia + '".$row["cantidad"]."' WHERE id = '".$row["id"]."'";
				//$result1 = mysql_query($query) or die("SQL Error 2-2: " . mysql_error());
			}
			else if ($row["tipo"] == "Salida" && $row["motivo"] != "Inicial")
			{
				$Existencia -= $row["cantidad"];
				
				//$query = "UPDATE inventario_movs SET existencia = existencia - '".$row["cantidad"]."' WHERE id = '".$row["id"]."'";
				//$result1 = mysql_query($query) or die("SQL Error 2-3: " . mysql_error());
			}
		}
		
		/*$data[] = array(
			"Codigo" => $row["cod_fab"],
			"Cantidad" => $row["cantidad"],
			"Costo" => $row["costo"],
			"ViejoCosto" => $row["viejo_costo"],
			"Existencia" => $row["existencia"],
			"Motivo" => $row["motivo"],
			"Tipo" => $row["tipo"],
			"Estado" => $row["estado"],
			"Fecha" => $row["fecha"],
		);*/
	}
	//echo json_encode($data);
}

echo "- Finalizado<br/><br/>";
?>