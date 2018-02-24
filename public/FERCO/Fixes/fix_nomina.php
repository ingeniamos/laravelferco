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

//----------------------------------------------------------------
// Agregar el respectivo autoincrement a las siguientes 
// tablas al finalizar los datos...
//
// - nom_prestamos
// - nom_extras
// - nom_novedades
//----------------------------------------------------------------

/*
Tablas Afectadas		-	Tablas a Sacar Informacion
	NUEVO							FERCO
- nom_prestamos			-		nom_prestamos_copy
- nom_prestamos_movs			-		nom_prestamos1
- nom_extras			-		nom_extrasff
- nom_extras_movs		-		nom_extrasff
- nom_novedades			-		nom_novedades_copy
- nom_novedades_movs	-		nom_novedades_copy
*/

echo "Prestamos";
echo "<br/>";

$query = "INSERT INTO nom_prestamos (id, interno, fecha, beneficiario_id, acreedor_id, tipo_mov, valor, cuotas, valor_cuotas, 
forma_pago, observacion, estado, digitado_por, aprobado_por) SELECT Id, Documento, Fecha, Cédula, Idacreedor, Tipomov, Valor, 
Cuotas, Valorcuota, Fpago, Observaciones, Estado, Digitó, Aprobó FROM nom_prestamos_copy";
$result = mysql_query($query) or die("SQL Error 1-1: " . mysql_error());

$query = "INSERT INTO nom_prestamos_movs (interno, nombre, cuota, fecha, valor, estado) 
SELECT Documento, CONCAT('Cuota_', Cuota, '_', Documento), Cuota, `Fecha de pago`, Valor, Estado 
FROM nom_prestamos1";
$result = mysql_query($query) or die("SQL Error 1-2: " . mysql_error());

$query = "UPDATE nom_prestamos_movs LEFT JOIN nom_prestamos ON 
nom_prestamos_movs.interno = nom_prestamos.interno 
SET nom_prestamos_movs.estado = nom_prestamos.estado";
$result = mysql_query($query) or die("SQL Error 1-3: " . mysql_error());

echo "Extras";
echo "<br/>";

$query = "INSERT INTO nom_extras (id, interno, empleado_id, autorizador_id, justificacion, comentario, observacion, estado, 
digitado_por, fecha_digitado, aprobado_por, modificado_por) SELECT Id, Documento, Cédula, Idautoriza, Justificacion, Justificacion1, 
Observaciones, Estado, Digitó, Fecha, Aprobó, Modificó FROM nom_extrasff";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());

echo "Extras Movs";
echo "<br/>";

$query = "SELECT *, SUBSTR(f1, 1, 10) AS turno1, SUBSTR(f2, 1, 10) AS turno2, SUBSTR(h1, 12, 19) AS hora_ini1, 
SUBSTR(h2, 12, 19) AS hora_ini2, SUBSTR(h1a, 12, 19) AS hora_fin1, SUBSTR(h2a, 12, 19) AS hora_fin2, IF(ff1 > 0, 'true', 'false') AS ff1, 
IF(ff2 > 0, 'true', 'false') AS ff2, IF(n1 > 0, 'true', 'false') AS n1, IF(n2 > 0, 'true', 'false') AS n2 FROM nom_extrasff";
$result = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "INSERT INTO nom_extras_movs (interno, cliente_id, turno, hora_ini, hora_fin, total, nocturno, festivo, estado) VALUES 
		('".$row["Documento"]."', '".$row["Cédula"]."', '".$row["turno1"]."', '".$row["hora_ini1"]."', '".$row["hora_fin1"]."', 
		'".$row["t1"]."', '".$row["n1"]."', '".$row["ff1"]."', '".$row["Estado"]."')";
		$result1 = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
		
		if ($row["hora_ini2"] != $row["hora_fin2"])
		{
			$query = "INSERT INTO nom_extras_movs (interno, cliente_id, turno, hora_ini, hora_fin, total, nocturno, festivo, estado) VALUES 
			('".$row["Documento"]."', '".$row["Cédula"]."', '".$row["turno2"]."', '".$row["hora_ini2"]."', '".$row["hora_fin2"]."', 
			'".$row["t2"]."', '".$row["n2"]."', '".$row["ff2"]."', '".$row["Estado"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3-3: " . mysql_error());
		}
	}
}

echo "Novedades";
echo "<br/>";

$query = "INSERT INTO nom_novedades (id, interno, empleado_id, reemplazo_id, autorizador_id, novedad, fecha_ini, hora_ini, fecha_fin, hora_fin, 
justificacion, comentario, reposicion, observacion, horas_novedad, horas_reposicion, descontable, remunerado100, remunerado66, cesantia, estado, 
digitado_por, fecha_digitado, aprobado_por) SELECT Id, Documento, Cédula, Idreemplazo, Idautoriza, Tipomov, f1, h1, f2, h2, Justificacion, 
Justificacion1, Reposicion, Observaciones, tt1, tt2, Descontar, IF(Remunerado1 = '1', 'true', 'false'), IF(Remunerado2 = '1', 'true', 'false'), 
IF(Tipomov = 'Permiso', 'true', 'false'), Estado, Digitó, Fecha, Aprobó FROM nom_novedades_copy";
$result = mysql_query($query) or die("SQL Error 4-1: " . mysql_error());

$query = "UPDATE nom_novedades SET cesantia = 'true' WHERE novedad = 'Permiso'";
$result = mysql_query($query) or die("SQL Error 4-2: " . mysql_error());

echo "Novedades Movs";
echo "<br/>";

$query = "SELECT *, SUBSTR(f3, 1, 10) AS f3, SUBSTR(f4, 1, 10) AS f4, SUBSTR(f5, 1, 10) AS f5, SUBSTR(f6, 1, 10) AS f6, 
SUBSTR(f7, 1, 10) AS f7, SUBSTR(r1, 12, 19) AS r1, SUBSTR(r1a, 12, 19) AS r1a, SUBSTR(r2, 12, 19) AS r2, SUBSTR(r2a, 12, 19) AS r2a, 
SUBSTR(r3, 12, 19) AS r3, SUBSTR(r3a, 12, 19) AS r3a, SUBSTR(r4, 12, 19) AS r4, SUBSTR(r4a, 12, 19) AS r4a, SUBSTR(r5, 12, 19) AS r5, 
SUBSTR(r5a, 12, 19) AS r5a FROM nom_novedades_copy";
$result = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		if ($row["f3"] != "")
		{
			$query = "INSERT INTO nom_novedades_movs (interno, fecha, hora_ini, hora_fin, total) VALUES 
			('".$row["Documento"]."', '".$row["f3"]."', '".$row["r1"]."', '".$row["r1a"]."', '".$row["t1"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
		}
		
		if ($row["f4"] != "")
		{
			$query = "INSERT INTO nom_novedades_movs (interno, fecha, hora_ini, hora_fin, total) VALUES 
			('".$row["Documento"]."', '".$row["f4"]."', '".$row["r2"]."', '".$row["r2a"]."', '".$row["t2"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3-3: " . mysql_error());
		}
		
		if ($row["f5"] != "")
		{
			$query = "INSERT INTO nom_novedades_movs (interno, fecha, hora_ini, hora_fin, total) VALUES 
			('".$row["Documento"]."', '".$row["f5"]."', '".$row["r3"]."', '".$row["r3a"]."', '".$row["t3"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3-4: " . mysql_error());
		}
		
		if ($row["f6"] != "")
		{
			$query = "INSERT INTO nom_novedades_movs (interno, fecha, hora_ini, hora_fin, total) VALUES 
			('".$row["Documento"]."', '".$row["f6"]."', '".$row["r4"]."', '".$row["r4a"]."', '".$row["t4"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3-5: " . mysql_error());
		}
		
		if ($row["f7"] != "")
		{
			$query = "INSERT INTO nom_novedades_movs (interno, fecha, hora_ini, hora_fin, total) VALUES 
			('".$row["Documento"]."', '".$row["f7"]."', '".$row["r5"]."', '".$row["r5a"]."', '".$row["t5"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3-6: " . mysql_error());
		}
	}
}

echo "- Finalizado<br/><br/>";
?>