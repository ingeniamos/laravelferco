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

function GetProduct_Info ($CodFab)
{
	$query = "SELECT costo, ultimo_costo, costo_promedio, existencia, stock_minimo, venta_promedio 
	FROM productos WHERE cod_fab = '".$CodFab."' ";
	$result = mysql_query($query) or die("SQL ERROR #1: ".mysql_error());
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array(
			'Costo' => $row["costo"],
			'Ultimo_Costo' => $row["ultimo_costo"],
			'Costo_Promedio' => $row["costo_promedio"],
			'Existencia' => $row["existencia"],
			'Stock_Minimo' => $row["stock_minimo"],
			'Venta_Promedio' => $row["venta_promedio"],
		);
	}
	return $data;
}

function Del_InventoryMovs ($Interno)
{
	$query = "DELETE FROM inventario_movs WHERE interno = '".$Interno."' ";
	$result = mysql_query($query) or die("SQL ERROR #1: ".mysql_error());
}

function AfectarInventario ($CodFab, $Cantidad, $Costo, $Interno, $Motivo, $Entrada)
{
	//--- INSERT INTO Inventario_Movs
	$Tmp = GetProduct_Info($CodFab);
	$Existencia = $Tmp[0]['Existencia'];
	$Costo_Actual = $Tmp[0]['Costo'];
	$Ultimo_Costo = $Tmp[0]['Ultimo_Costo'];
	$Costo_Promedio = $Tmp[0]['Costo_Promedio'];
	
	//--- CostoPromedio
	$Total_Costo = ($Costo_Promedio * $Existencia) + ($Costo * $Cantidad);
	$Total_Cantidad = $Existencia + $Cantidad;
	$Total = $Total_Costo / $Total_Cantidad;
	
	$query = "INSERT INTO inventario_movs 
	(cod_fab, cantidad, costo, viejo_costo, costo_promedio, viejo_costo_promedio, ultimo_costo, existencia, interno, 
	motivo, fecha, tipo) VALUES ('".$CodFab."', '".$Cantidad."', '".$Costo."',  '".$Costo_Actual."', '".$Total."', 
	'".$Costo_Promedio."', '".$Ultimo_Costo."', '".$Existencia."', '".$Interno."', '".$Motivo."', NOW(), 'Entrada')";
	$result = mysql_query($query) or die("SQL ERROR #1: ".mysql_error());
	if (mysql_affected_rows() > 0)
	{
		$query = "UPDATE productos SET 
		costo = '".$Costo."', 
		ultimo_costo = '".$Costo_Actual."', 
		costo_promedio = '".$Total."', 
		existencia = existencia + '".$Cantidad."' 
		WHERE cod_fab = '".$CodFab."' ";
		$result = mysql_query($query) or die(mysql_error());
	}
}

//---------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------

$query = "SELECT interno, fecha_aprobado FROM compras_final WHERE estado = 'Aprobado' AND forma_pago != '' 
AND DATE(fecha_aprobado) BETWEEN '2016-02-18 23:59:59' AND '2016-02-23 23:59:59'";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		$Internos[] = $row["interno"];
		$Fechas[$row["interno"]] = $row["fecha_aprobado"];
		Del_InventoryMovs($row["interno"]);
	}
}

echo "Compras desde La Fecha hasta Ahora -> ".mysql_num_rows($result)."";
echo "<br/>";

for ($i = 0; $i < count($Internos); $i++)
{
	$query = "SELECT * FROM compras_movs WHERE interno = '".$Internos[$i]."'";
	$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
	if (mysql_num_rows($result) > 0)	
	{
		while ($row = mysql_fetch_array($result))
		{
			AfectarInventario($row["codigo"], $row["cantidad"], $row["nuevo_costo"], $row["interno"], "Compra", "Compra");
		}
	}
}

echo "Actualizacion de Fechas en Inventario.";
echo "<br/>";

for ($i = 0; $i < count($Internos); $i++)
{
	$query = "UPDATE inventario_movs SET fecha = '".$Fechas[$Internos[$i]]."' 
	WHERE interno = '".$Internos[$i]."'";
	$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
}

echo "<br/>";
echo "Final";
echo "<br/>";
