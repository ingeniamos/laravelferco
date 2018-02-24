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

echo "Existencias de Productos";
echo "<br/>";

$query = "SELECT * FROM inventario_movs WHERE motivo = 'Inicial' GROUP BY cod_fab";
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

$query = "SELECT * FROM inventario_movs ORDER BY cod_fab, fecha ASC";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	$CurrentCode = "_empty_";
	$Existencia = 0;
	while($row = mysql_fetch_array($result))
	{
		if ($CurrentCode != $row["cod_fab"])
		{
			if ($CurrentCode != "_empty_")
			{
				echo "Codigo: ".$CurrentCode." Existencias: ".$Existencia." Actualizado -> ";
				$query = "UPDATE productos SET existencia = '".$Existencia."' WHERE cod_fab = '".$CurrentCode."'";
				$result1 = mysql_query($query) or die("SQL Error 3: " . mysql_error());
				echo mysql_affected_rows() ? "Si <br />":"No <br />";
			}
			
			$Existencia = 0;
			$CurrentCode = $row["cod_fab"];
			
			if (isset($Inicial[$row["cod_fab"]]))
				$Existencia += $Inicial[$row["cod_fab"]];
			else if ($row["tipo"] == "Entrada")
				$Existencia += $row["cantidad"];
			else if ($row["tipo"] == "Salida")
				$Existencia -= $row["cantidad"];
		}
		else
		{
			if ($row["tipo"] == "Entrada" && $row["motivo"] != "Inicial")
				$Existencia += $row["cantidad"];
			else if ($row["tipo"] == "Salida" && $row["motivo"] != "Inicial")
				$Existencia -= $row["cantidad"];
		}
	}
}
echo mysql_num_rows($result);

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>