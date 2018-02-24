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

$query = "SELECT DISTINCT cliente_id FROM cxp_movs WHERE estado = 'Aprobado' AND tipo_movimiento = 'Compra' AND saldo > '0'";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		$Clientes[] = $row["cliente_id"];
	}
}

echo "Clientes a los que se les debe: ".count($Clientes)."";
echo "<br/>";

$Clause = implode("', '", $Clientes);
$CurrentID = "_empty_";

$query = "SELECT * FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND estado = 'Aprobado' ORDER BY cliente_id";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)	
{
	while ($row = mysql_fetch_array($result))
	{
		if ($CurrentID != $row["cliente_id"])
		{
			$CurrentID = $row["cliente_id"];
			if ($row["tipo_movimiento"] == "Compra")
			{
				
				$Compra[$row["cliente_id"]] = $row["valor"];
			}
			else
			{
				$Abono[$row["cliente_id"]] = $row["valor"];
			}
		}
		else
		{
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
			}
		}
	}
}

for($i = 0; $i < count($Clientes); $i++)
{
	$CompraValor = isset($Compra[$Clientes[$i]]) ? $Compra[$Clientes[$i]]:0;
	$AbonoValor = isset($Abono[$Clientes[$i]]) ? $Abono[$Clientes[$i]]:0;
	
	$Total = ($CompraValor - $AbonoValor);
	//if ($Total < 1);
	//	$Total = 0;
	
	echo "".$Clientes[$i]." -> ".$Total."";
	echo "<br/>";
}

die();

echo "Fact Final vs Clientes";
echo "<br/>";

$query = "SELECT DISTINCT cliente_id FROM fact_final";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
echo "".mysql_num_rows($result)."<br/>";
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Clientes[] = $row["cliente_id"];
	}
}

$Clause = implode("', '", $Clientes);

$query = "SELECT nombre, cliente_id FROM clientes WHERE cliente_id IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
echo "".mysql_num_rows($result)."<br/>";
/*
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		echo "Cliente: ".$row["nombre"]."<br/>ClienteID: ".$row["cliente_id"]."<br/>";
	}
}
*/

echo "<br/>";
echo "Final";
echo "<br/>";

echo "Produccion Final vs Clientes";
echo "<br/>";

$query = "SELECT DISTINCT cliente_id FROM produccion_final";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
echo "".mysql_num_rows($result)."<br/>";
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Clientes2[] = $row["cliente_id"];
	}
}

$Clause = implode("', '", $Clientes2);

$query = "SELECT nombre, cliente_id FROM clientes WHERE cliente_id IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
echo "".mysql_num_rows($result)."<br/>";
/*if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		echo "Cliente: ".$row["nombre"]."<br/>ClienteID: ".$row["cliente_id"]."<br/>";
	}
}*/

echo "<br/>";
echo "Final";
echo "<br/>";

echo "Mov Clientes vs Clientes";
echo "<br/>";

$query = "SELECT DISTINCT cliente_id FROM bancos";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
echo "".mysql_num_rows($result)."<br/>";
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Clientes3[] = $row["cliente_id"];
	}
}

$Clause = implode("', '", $Clientes3);

$query = "SELECT nombre, cliente_id FROM clientes WHERE cliente_id IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
echo "".mysql_num_rows($result)."<br/>";
/*if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		echo "Cliente: ".$row["nombre"]."<br/>ClienteID: ".$row["cliente_id"]."<br/>";
	}
}*/

echo "<br/>";
echo "Final";
echo "<br/>";

echo "Caja Final vs Clientes";
echo "<br/>";

$query = "SELECT DISTINCT cliente_id FROM caja_final";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
echo "".mysql_num_rows($result)."<br/>";
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Clientes4[] = $row["cliente_id"];
	}
}

$Clause = implode("', '", $Clientes4);

$query = "SELECT nombre, cliente_id FROM clientes WHERE cliente_id IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
echo "".mysql_num_rows($result)."<br/>";
/*if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		echo "Cliente: ".$row["nombre"]."<br/>ClienteID: ".$row["cliente_id"]."<br/>";
	}
}*/

echo "<br/>";
echo "Final";
echo "<br/>";
