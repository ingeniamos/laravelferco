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

if (!isset($_GET['Interno']))
{
	echo "Interno + valor para Iniciar el Proceso";
	die();
}

//---------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------

echo "* Pruebas de Brrado de Inventario.<br/>";
$DEBUG = true;
$Fecha = "";
$query = "SELECT * FROM inventario_movs WHERE interno = '".$_GET['Interno']."'";
$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
if (mysql_num_rows($result) > 0)
{
	echo "Movimientos -> ".mysql_num_rows($result)." <br />";
	while ($row = mysql_fetch_array($result))
	{
		echo "Codigo -> ".$row["cod_fab"]." <br />";
		$Fecha = $row["fecha"];
		$query = "SELECT * FROM inventario_movs WHERE cod_fab = '".$row["cod_fab"]."' 
		ORDER BY fecha DESC LIMIT 1";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			if ($row["fecha"] == $row1["fecha"])
			{
				if ($row["tipo"] == "Entrada")
				{
					$query = "UPDATE productos SET existencia = existencia WHERE cod_fab = '".$row["cod_fab"]."'";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
					echo "Restando1..<br />";
				}
				else if ($row["tipo"] == "Salida")
				{
					$query = "UPDATE productos SET existencia = existencia WHERE cod_fab = '".$row["cod_fab"]."'";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
					echo "Sumando1..<br />";
				}
			}
			else
			{
				if ($row["tipo"] == "Entrada")
				{
					$query = "UPDATE productos SET existencia = existencia WHERE cod_fab = '".$row["cod_fab"]."'";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
					echo "Restando2..<br />";
				}
				else if ($row["tipo"] == "Salida")
				{
					$query = "UPDATE productos SET existencia = existencia WHERE cod_fab = '".$row["cod_fab"]."'";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
					echo "Sumando2..<br />";
				}
			}
		}
		else
		{
			if ($row["tipo"] == "Entrada")
			{
				$query = "UPDATE productos SET existencia = existencia WHERE cod_fab = '".$row["cod_fab"]."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
				echo "Restando3..<br />";
			}
			else if ($row["tipo"] == "Salida")
			{
				$query = "UPDATE productos SET existencia = existencia WHERE cod_fab = '".$row["cod_fab"]."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
				echo "Sumando3..<br />";
			}
		}
	}
}

echo "<br/>";
echo "Final";
echo "<br/>";
