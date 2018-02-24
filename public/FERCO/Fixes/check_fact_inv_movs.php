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

if (!isset($_GET['START']))
{
	echo "START para Iniciar el Proceso";
	die();
}

// Time Check!
$now = time();
echo "Tiempo de Inicio-> ".$now." <br/><br/>";

/*
echo "Checkar Movimientos de Ventas que No tengan movimientos de inventario.";
echo "<br/><br/>";

$query = "SELECT DISTINCT(interno) FROM fact_movs";
$result = mysql_query($query) or die("SQL ERROR #1-1: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Internos[] = $row["interno"];
	}
}
echo "Numero de Facturas sacadas de sus Movimientos -> ".count($Internos)."<br/>";
echo "<br/>* Comprarando...<br/>";
$No_Internos = $Internos;
$Clause = implode("', '", $Internos);

$query = "SELECT interno FROM fact_final WHERE interno IN ('".$Clause."') AND estado = 'Aprobado' 
 ORDER BY interno";
//AND fecha_digitado >= '2016-03-01' ORDER BY interno";
$result = mysql_query($query) or die("SQL ERROR #1-2: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$New_Internos[] = $row["interno"];
		
		$found = array_search($row["interno"], $Internos);
		if ($found)
			unset($No_Internos[$found]);
	}
}
echo "<br />";
echo "Numero de Facturas Comparadas -> ".count($New_Internos)."<br/>";
echo "Numero de Facturas Descartadas por el Criterio de Busqueda -> ". (count($Internos) - count($New_Internos)) ."<br />";

echo "<br/>* Comprarando en Inventario...<br/>";
$No_Internos2 = $New_Internos;
$Clause = implode("', '", $New_Internos);

$query = "SELECT DISTINCT interno FROM inventario_movs WHERE interno IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL ERROR #1-3: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$New_Internos2[] = $row["interno"];
		
		$found = array_search($row["interno"], $New_Internos);
		if ($found)
			unset($No_Internos2[$found]);
	}
}
echo "<br />";
echo "Numero de Facturas Comparadas -> ".count($New_Internos2)."<br/>";
echo "Numero de Facturas no Encontradas -> ". (count($New_Internos) - count($New_Internos2)) ."<br />";

if (isset($No_Internos2) && count($No_Internos2) > 0)
{
	echo "* Listado de Facturas no Encontradas:<br/><br/>";
	foreach($No_Internos2 AS $Item)
	{
		echo $Item."<br/>";
	}
	echo "<br/>";
}

echo "<br/>* Comprarando Inventario por Movimientos...<br/>";
//$No_Internos3 = $New_Internos2;
$Clause = implode("', '", $New_Internos2);

$query = "SELECT * FROM fact_movs WHERE interno IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL ERROR #1-4: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		//$New_Internos3[] = $row["interno"];
		
		//$found = array_search($row["interno"], $New_Internos2);
		//if ($found)
		//	unset($No_Internos3[$found]);
		
		$query = "SELECT * FROM inventario_movs WHERE cod_fab = '".$row["codigo"]."' AND interno = '".$row["interno"]."'";
		$result1 = mysql_query($query) or die("SQL ERROR #1-5: ".mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			if ($row["cantidad"] != $row1["cantidad"])
			{
				$Movimientos[] = array(
					"Interno" => $row["interno"],
					"Codigo" => $row["codigo"],
					"Cantidad" => $row["cantidad"],
				);
			}
		}
		else
		{
			$No_Internos3[] = $row["interno"];
			//$found = array_search($row["interno"], $New_Internos2);
			//if ($found)
			//	unset($No_Internos3[$found]);
		}
	}
}

echo "<br />";
echo "Numero de Movimientos con Errores en Cantidad -> ". (isset($Movimientos) ? count($Movimientos):0) ."<br/>";
echo "Numero de Facturas con Movimientos Faltantes -> ". (isset($No_Internos3) ? count($No_Internos3):0) ."<br />";
echo "<br /><br />";

if (isset($No_Internos3) && count($No_Internos3) > 0)
{
	echo "* Listado de Facturas con Movimientos Faltantes:<br/><br/>";
	foreach($No_Internos3 AS $Item)
	{
		echo $Item."<br/>";
	}
	echo "<br/>";
}

if (isset($Movimientos) && count($Movimientos) > 0)
{
	echo "* Listado de Movimientos con Errores en Cantidad:<br/><br/>";
	foreach($Movimientos AS $Item)
	{
		print_r($Item);
		echo "<br/>";
	}
	echo "<br/>";
}
*/

/*
echo "Checkar Movimientos de Compras que No tengan movimientos de inventario.";
echo "<br/><br/>";

$query = "SELECT DISTINCT(interno) FROM compras_movs";
$result = mysql_query($query) or die("SQL ERROR #2-1: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Compras[] = $row["interno"];
	}
}
echo "Numero de Compras sacadas de sus Movimientos -> ".count($Compras)."<br/>";
echo "<br/>* Comprarando...<br/>";
$No_Compras = $Compras;
$Clause = implode("', '", $Compras);

$query = "SELECT interno FROM compras_final WHERE interno IN ('".$Clause."') AND estado = 'Aprobado' 
 ORDER BY interno";
//AND fecha_digitado >= '2016-03-01' ORDER BY interno";
$result = mysql_query($query) or die("SQL ERROR #2-2: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$New_Compras[] = $row["interno"];
		
		$found = array_search($row["interno"], $Compras);
		if ($found)
			unset($No_Compras[$found]);
	}
}
echo "<br />";
echo "Numero de Compras Comparadas -> ".count($New_Compras)."<br/>";
echo "Numero de Compras Descartadas por el Criterio de Busqueda -> ". (count($Compras) - count($New_Compras)) ."<br />";

echo "<br/>* Comprarando en Inventario...<br/>";
$No_Compras2 = $New_Compras;
$Clause = implode("', '", $New_Compras);

$query = "SELECT DISTINCT interno FROM inventario_movs WHERE interno IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL ERROR #2-3: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$New_Compras2[] = $row["interno"];
		
		$found = array_search($row["interno"], $New_Compras);
		if ($found)
			unset($No_Compras2[$found]);
	}
}
echo "<br />";
echo "Numero de Compras Comparadas -> ".count($New_Compras2)."<br/>";
echo "Numero de Compras no Encontradas -> ". (count($New_Compras) - count($New_Compras2)) ."<br />";

if (isset($No_Compras2) && count($No_Compras2) > 0)
{
	echo "* Listado de Compras no Encontradas:<br/><br/>";
	foreach($No_Compras2 AS $Item)
	{
		echo $Item."<br/>";
	}
	echo "<br/>";
}

echo "<br/>* Comprarando Inventario por Movimientos de Compra...<br/>";
//$No_Compras3 = $New_Compras2;
$Clause = implode("', '", $New_Compras2);

$query = "SELECT * FROM compras_movs WHERE interno IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL ERROR #1-4: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		//$New_Internos3[] = $row["interno"];
		
		//$found = array_search($row["interno"], $New_Compras2);
		//if ($found)
		//	unset($No_Compras3[$found]);
		
		$query = "SELECT * FROM inventario_movs WHERE cod_fab = '".$row["codigo"]."' AND interno = '".$row["interno"]."'";
		$result1 = mysql_query($query) or die("SQL ERROR #1-5: ".mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			if ($row["cantidad"] != $row1["cantidad"])
			{
				$Movimientos2[] = array(
					"Interno" => $row["interno"],
					"Codigo" => $row["codigo"],
					"Cantidad" => $row["cantidad"],
				);
			}
		}
		else
		{
			$No_Compras3[] = $row["interno"];
			//$found = array_search($row["interno"], $New_Compras2);
			//if ($found)
			//	unset($No_Compras3[$found]);
		}
	}
}

echo "<br />";
echo "Numero de Movimientos con Errores en Cantidad -> ". (isset($Movimientos2) ? count($Movimientos2):0) ."<br/>";
echo "Numero de Facturas con Movimientos Faltantes -> ". (isset($No_Compras3) ? count($No_Compras3):0) ."<br />";
echo "<br /><br />";

if (isset($No_Compras3) && count($No_Compras3) > 0)
{
	echo "* Listado de Facturas con Movimientos Faltantes:<br/><br/>";
	foreach($No_Compras3 AS $Item)
	{
		echo $Item."<br/>";
	}
	echo "<br/>";
}

if (isset($Movimientos2) && count($Movimientos2) > 0)
{
	echo "* Listado de Movimientos con Errores en Cantidad:<br/><br/>";
	foreach($Movimientos2 AS $Item)
	{
		print_r($Item);
		echo "<br/>";
	}
	echo "<br/>";
}
*/

echo "Checkar Movimientos de Produccion que No tengan movimientos de inventario.";
echo "<br/><br/>";

$query = "SELECT orden_produccion FROM produccion_final WHERE estado = 'Aprobado' 
 ORDER BY orden_produccion";
//AND fecha_digitado >= '2016-03-01' ORDER BY orden_produccion";
$result = mysql_query($query) or die("SQL ERROR #3-1: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Produccion[] = $row["orden_produccion"];
	}
}
echo "<br />";
echo "Numero de Ordenes de Produccion Encontradas -> ".count($Produccion)."<br/>";

echo "<br/>* Comprarando en Inventario...<br/>";
$No_Produccion = $Produccion;
$Clause = implode("', '", $Produccion);

$query = "SELECT DISTINCT interno FROM inventario_movs WHERE interno IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL ERROR #2-3: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$New_Produccion[] = $row["interno"];
		
		$found = array_search($row["interno"], $Produccion);
		if ($found)
			unset($No_Produccion[$found]);
	}
}
echo "<br />";
echo "Numero de Ordenes de Produccion Comparadas -> ".count($New_Produccion)."<br/>";
echo "Numero de Ordenes de Produccion no Encontradas -> ". (count($Produccion) - count($New_Produccion)) ."<br />";

if (isset($No_Produccion) && count($No_Produccion) > 0)
{
	echo "* Listado de Ordenes de Produccion no Encontradas:<br/><br/>";
	foreach($No_Produccion AS $Item)
	{
		echo $Item."<br/>";
	}
	echo "<br/>";
}

echo "<br/>* Comprarando Inventario por Movimientos de Produccion...<br/>";
//$No_Produccion2 = $New_Produccion;
$Clause = implode("', '", $New_Produccion);

$query = "SELECT * FROM produccion_movs WHERE orden_produccion IN ('".$Clause."')";
$result = mysql_query($query) or die("SQL ERROR #1-4: ".mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		if ($row["tipo"] != "Obtener")
			continue;
		
		$query = "SELECT * FROM inventario_movs WHERE cod_fab = '".$row["codigo"]."' AND interno = '".$row["orden_produccion"]."'";
		$result1 = mysql_query($query) or die("SQL ERROR #1-5: ".mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			if ($row["cantidad"] != $row1["cantidad"])
			{
				$Movimientos3[] = array(
					"OP" => $row["orden_produccion"],
					"Codigo" => $row["codigo"],
					"Cantidad" => $row["cantidad"],
				);
			}
		}
		else
		{
			$No_Produccion2[] = $row["orden_produccion"];
		}
	}
}

echo "<br />";
echo "Numero de Movimientos con Errores en Cantidad -> ". (isset($Movimientos3) ? count($Movimientos3):0) ."<br/>";
echo "Numero de OP con Movimientos Faltantes -> ". (isset($No_Produccion2) ? count($No_Produccion2):0) ."<br />";
echo "<br /><br />";

if (isset($No_Produccion2) && count($No_Produccion2) > 0)
{
	echo "* Listado de OP con Movimientos Faltantes:<br/><br/>";
	foreach($No_Produccion2 AS $Item)
	{
		echo $Item."<br/>";
	}
	echo "<br/>";
}

if (isset($Movimientos3) && count($Movimientos3) > 0)
{
	echo "* Listado de Movimientos con Errores en Cantidad:<br/><br/>";
	foreach($Movimientos3 AS $Item)
	{
		print_r($Item);
		echo "<br/>";
	}
	echo "<br/>";
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>