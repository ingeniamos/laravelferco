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

$DEBUG = true;

function AfectarInventario($CodFab, $Cantidad, $Costo, $Interno, $Fecha, $Motivo, $Entrada)
{
	global $DEBUG;
	
	switch ($Entrada)
	{
		case "Produccion_Producir":
			$query = "INSERT INTO inventario_movs 
			(cod_fab, cantidad,  interno, motivo, fecha, tipo) VALUES ('".$CodFab."', '".$Cantidad."', '".$Interno."', '".$Motivo."', ";
			$query .= $Fecha == "" ? "NOW(), ":"'".$Fecha."', ";
			$query .= "'Entrada')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-1: ".mysql_error():"");
		break;
		case "Produccion_Consumir":
			$query = "INSERT INTO inventario_movs 
			(cod_fab, cantidad, interno, motivo, fecha, tipo) VALUES ('".$CodFab."', '".$Cantidad."', '".$Interno."', '".$Motivo."', ";
			$query .= $Fecha == "" ? "NOW(), ":"'".$Fecha."', ";
			$query .= "'Salida')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-2: ".mysql_error():"");
		break;
	}
}

if (!isset($_GET['START']))
{
	echo "START para Iniciar el Proceso";
	die();
}

// Time Check!
$now = time();
echo "Tiempo de Inicio-> ".$now." <br/><br/>";


$query = "SELECT orden_produccion FROM produccion_final WHERE estado = 'Aprobado'";
$result = mysql_query($query) or die("SQL ERROR #1-1: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Internos[] = $row["orden_produccion"];
	}
}
echo "Numero de Filas1 -> ".mysql_num_rows($result)."<br/>";

$Clause = implode("', '", $Internos);

$query = "SELECT * FROM inventario_movs WHERE interno IN ('".$Clause."') GROUP BY interno";
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
echo "OPs sin Inventario: ".count($Internos)."<br />";
echo "Numero de Filas2 -> ".mysql_num_rows($result)."<br/>";


foreach ($Internos AS $OP)
{
	$Obtener = false;
	$Requerido = false;
	
	$query = "SELECT * FROM produccion_final WHERE orden_produccion = '".$OP."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
	
		$query = "SELECT * FROM produccion_movs WHERE orden_produccion = '".$OP."'";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			while($row1 = mysql_fetch_array($result1))
			{
				if ($row1["tipo"] == "Obtener")
					$Obtener = true;
				else if ($row1["tipo"] == "Requerido")
					$Requerido = true;
			}
			
			if ($Obtener == true && $Requerido == true)
			{
				while($row2 = mysql_fetch_array($result2))
				{
					if ($row2["tipo"] == "Obtener")
					{
						if ($row["fecha_aprobado"] == "0000-00-00 00:00:00")
							AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $OP, "", "Produccion", "Produccion_Producir");
						else
							AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $OP, $row["fecha_aprobado"], "Produccion", "Produccion_Producir");
					}
					else if ($row2["tipo"] == "Requerido")
					{
						if ($row["fecha_aprobado"] == "0000-00-00 00:00:00")
							AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $OP, "", "Produccion", "Produccion_Consumir");
						else
							AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $OP, $row["fecha_aprobado"], "Produccion", "Produccion_Consumir");
					}
				}
			}
			else
			{
				echo "INCOMPLETO";
			}
		}
		else
		{
			echo "ERROR";
		}

		$query = "SELECT * FROM produccion_proc WHERE orden_produccion = '".$OP."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result))
			{
				if ($row["estado"] == "Finalizado")
				{
					$query = "SELECT * FROM produccion_proc_movs WHERE tipo = 'Despunte' AND origen = '".$row["proceso"]."' AND orden_produccion = '".$OP."' 
					OR tipo = 'Despunte' AND destino = '".$row["proceso"]."' AND orden_produccion = '".$OP."' ";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
					if (mysql_num_rows($result1) > 0)
					{
						while($row1 = mysql_fetch_array($result1))
						{
							if ($row["fecha_fin"] == "0000-00-00 00:00:00")
								AfectarInventario($row1["codigo"], $row1["cantidad"], 0, $OP, "", "Despunte", "Produccion_Producir");
							else
								AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $OP, $row["fecha_fin"], "Despunte", "Produccion_Producir");
						}
					}
				}
				else
					echo "OP: ".$OP." - Proceso: ".$row["proceso"]." - No finalizado!<br />";
			}
		}
	}
}

echo "- Finalizado<br/><br/>";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
echo "<br/>";
