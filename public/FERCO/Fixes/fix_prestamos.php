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

echo "Prestamos";
echo "<br/>";

// ingresar los movimientos faltantes a nom_prestamos_movs desde nom_prestamos1
$query = "INSERT INTO nom_prestamos_movs (interno, nombre, cuota, fecha, valor, estado) 
SELECT Documento, CONCAT('Cuota_', Cuota, '_', Documento), Cuota, `Fecha de pago`, Valor, Estado 
FROM nom_prestamos1";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
// Actualizar los estados que no coincidan (aparentemente 1)
$query = "UPDATE nom_prestamos_movs LEFT JOIN nom_prestamos ON 
nom_prestamos_movs.interno = nom_prestamos.interno 
SET nom_prestamos_movs.estado = nom_prestamos.estado";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());

//Actualizar los recibos de caja con el interno del prestamo
$query = "SELECT mov_clientes.interno, mov_clientes.caja_interno, mov_clientes.caja_recibo 
FROM mov_clientes LEFT JOIN nom_prestamos ON mov_clientes.interno = nom_prestamos.interno 
WHERE mov_clientes.interno = nom_prestamos.interno AND mov_clientes.tipo_movimiento = 'Debito' GROUP BY mov_clientes.interno";
$result = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		echo "-> ".$row["interno"]." - ".$row["caja_interno"]." - ".$row["caja_recibo"]." <br />";
		$query = "UPDATE caja_final SET caja_recibo = '".$row["interno"]."' WHERE caja_interno = '".$row["caja_interno"]."'";
		$result1 = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
		
		$query = "UPDATE mov_clientes SET caja_recibo = '".$row["interno"]."' WHERE interno = '".$row["interno"]."'";
		$result1 = mysql_query($query) or die("SQL Error 3-3: " . mysql_error());
	}
}
// Actualizar las deudas invirtiendo el interno a la orden de compra
$query = "SELECT a.interno, a.nombre, b.id, b.interno AS mov_interno FROM nom_prestamos_movs a LEFT JOIN mov_clientes b 
ON a.nombre = b.interno WHERE a.nombre = b.interno";
$result = mysql_query($query) or die("SQL Error 4-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		//echo "Prestamo -> ".$row["interno"]." - ".$row["nombre"]." ||| Deuda -> ".$row["mov_interno"]."<br />";
		$query = "UPDATE mov_clientes SET orden_compra = '".$row["interno"]."' WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 4-2: " . mysql_error());
	}
}
// Lo mismo de arriba pero solo con las mias (que lo hacian al revez...)
$query = "SELECT id, interno, orden_compra FROM mov_clientes WHERE orden_compra LIKE '%Cuota%'";
$result = mysql_query($query) or die("SQL Error 5-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{		
		$query = "UPDATE mov_clientes SET 
		interno = '".$row["orden_compra"]."', 
		orden_compra = '".$row["interno"]."' 
		WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 4-2: " . mysql_error());
	}
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>