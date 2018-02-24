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

if (!isset($_GET['ClienteID']))
{
	echo "?ClienteID=ID para Iniciar el Proceso";
	die();
}

echo "<b>Informacion Detallada del Cliente \"<u>".$_GET['ClienteID']."</u>\"</b>";
echo "<br/>";
echo "<br/>";

$query = "SELECT * FROM cxp_movs WHERE cliente_id = '".$_GET['ClienteID']."' AND 
estado = 'Aprobado' AND tipo_movimiento = 'Compra' AND saldo > '0'";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	echo "* El Cliente Posee deuda.";
	echo "<br/>";
}

echo "<br/>";
echo "- <u>Listado de Compras y Abonos en estado \"Aprobado\":</u>";
echo "<br/>";
echo "<br/>";

$query = "SELECT * FROM cxp_movs WHERE cliente_id = '".$_GET['ClienteID']."' AND estado = 'Aprobado' ORDER BY compra_interno, tipo_movimiento DESC";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());
if (mysql_num_rows($result) > 0)	
{
	while ($row = mysql_fetch_array($result))
	{
		if ($row["tipo_movimiento"] == "Compra")
		{
			echo "Fecha -> ".$row["fecha_digitado"]."";
			echo " - ";
			echo "Tipo Movimiento -> ".$row["tipo_movimiento"]."";
			echo " - ";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Interno -> ".$row["compra_interno"]."";
			echo " - ";
			echo "Valor -> ".number_format($row["valor"],2)."";
			echo " - ";
			echo "Saldo -> ".number_format($row["saldo"],2)."";
			echo " - ";
			echo "Caja_Interno -> ".$row["caja_interno"]."";
			echo "<br/>";
		}
		else
		{
			echo "Fecha -> ".$row["fecha_digitado"]."";
			echo " - ";
			echo "Tipo Movimiento -> ".$row["tipo_movimiento"]."";
			echo " - ";
			echo "Interno -> ".$row["compra_interno"]."";
			echo " - ";
			echo "Valor -> ".number_format($row["valor"],2)."";
			echo " - ";
			echo "Saldo -> 0.00";
			echo " - ";
			echo "Caja_Interno -> ".$row["caja_interno"]."";
			echo "<br/>";
		}
		
		
		/*$CurrentID = $row["cliente_id"];
		if ($row["tipo_movimiento"] == "Compra")
		{
			
			$Compra[$row["cliente_id"]] = $row["valor"];
		}
		else
		{
			$Abono[$row["cliente_id"]] = $row["valor"];
		}
		
		if ($row["tipo_movimiento"] == "Compra")
		{
			if (isset($Compra[$row["cliente_id"]]))
				$Compra[$row["cliente_id"]] += $row["valor"];
			else
				$Compra[$row["cliente_id"]] = $row["valor"];
		}
		else
		{
			if (isset($Abono[$row["cliente_id"]]))
				$Abono[$row["cliente_id"]] += $row["valor"];
			else
				$Abono[$row["cliente_id"]] = $row["valor"];
		}*/
	}
}

echo "<br/>";
echo "Fin.";
echo "<br/>";
