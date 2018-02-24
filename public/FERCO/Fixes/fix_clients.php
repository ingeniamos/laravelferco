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

/*
Tablas Afectadas	-	Tablas a Sacar Informacion
	NUEVO						FERCO
- clientes			-		clientes_copy
- clientes_garant	-		clientes_garant_copy
- cliente_grupo		-		clientes_copy
*/

// Time Check!
$now = time();
echo "Tiempo de Inicio-> ".$now." <br/><br/>";

//-------------------------------
// 1º Borrar Filas Vacias y Datos Irrelevantes
//-------------------------------
echo "1º Borrar Filas Vacias y Datos Irrelevantes";
echo "<br/>";
$query = "DELETE FROM clientes_copy WHERE Cedula IS NULL OR Cedula = ''";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Borrado 1 Completado! Afectadas: ".mysql_affected_rows()."<br/>";
else
	echo ". Borrado 1 Completado!<br/>";

$query = "DELETE FROM clientes_garant_copy WHERE Garantía IS NULL OR Garantía = 'prendido'";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Borrado 2 Completado! Afectadas: ".mysql_affected_rows()."<br/>";
else
	echo ". Borrado 2 Completado!<br/>";

	echo "- Finalizado<br/><br/>";

//-------------------------------
// 2º Agregar
//-------------------------------

echo "2º Copiar Datos";
echo "<br/>";

// FALTANTES:
// ultima actualizacion

$query = "INSERT INTO clientes (nombre, tipo_sociedad, tipo_doc, cliente_id) VALUES ('SISTEMA', 'Persona Natural', 'RUT', '000000000-0')";
$result = mysql_query($query) or die("SQL Error 2-1: " . mysql_error());

$query = "INSERT INTO clientes (nombre, tipo_sociedad, tipo_doc, cliente_id, direccion, barrio, ciudad, departamento, contacto_p, telefono_cp, 
contacto_s, telefono_cs, telefono, fax, email, email2, notas, terminos, credito, vigencia_credito, adicional, vigencia_adicional, 
cupo_asignado_por, fecha_asignado, garantia, estado_cuenta, lista_precio, vendedor_codigo, cobrador_codigo) 
SELECT Nombre, `Tipo de Sociedad`, Tipo_Doc, Cedula, `Dirección Oficina`, BARRIO, CIUDAD, DEPARTAMENTO, `Contacto Principal`, `Teléfono (cp)`, 
`Contacto Secundario`, `Teléfono (cs)`, Teléfono, FAX, `E-mail`, email2, Notas, Términos, `Monto Asignado`, `Vigencia Cupo`, Adicional, Vigencia, 
asignocupo, fasignocupo, Garantia, `Estado de Cuenta`, (CASE WHEN `Lista Precios` = 'Lista 2' THEN '2' WHEN `Lista Precios` = 'Lista 3' THEN '3' 
WHEN `Lista Precios` = 'Lista 4' THEN '4' ELSE '1' END), `Vendedor Asignado`, Cobrador FROM clientes_copy";
$result = mysql_query($query) or die("SQL Error 2-2: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado<br/><br/>";

$query = "UPDATE clientes SET nombre = REPLACE(nombre, '\t', '' )";
$result = mysql_query($query) or die("SQL Error 2-3: " . mysql_error());

$query = "UPDATE clientes SET 
telefono_cp = REPLACE(telefono_cp, ' ', '' ), 
telefono_cs = REPLACE(telefono_cs, ' ', '' ), 
telefono = REPLACE(telefono, ' ', '' ), 
fax = REPLACE(fax, ' ', '' )";
$result = mysql_query($query) or die("SQL Error 2-3: " . mysql_error());

$query = "UPDATE clientes SET tipo_sociedad = 'RUT', tipo_doc = 'RUT' 
WHERE tipo_sociedad = 'Persona Natural' AND cliente_id LIKE '%-%' ";
$result = mysql_query($query) or die("SQL Error 2-3: " . mysql_error());

$query = "UPDATE clientes SET tipo_sociedad = 'RUT', tipo_doc = 'RUT' 
WHERE tipo_sociedad = 'Persona Natural' AND cliente_id LIKE '% %' ";
$result = mysql_query($query) or die("SQL Error 2-3: " . mysql_error());

// Modificarlos Luego de que esten Relacionadas las Tablas
//$query = "UPDATE clientes SET cliente_id = REPLACE(cliente_id, ' ', '-' ) ";
//$result = mysql_query($query) or die("SQL Error 2-3: " . mysql_error());

//-------------------------------
// 3º Actualizar Datos Faltantes
//-------------------------------
echo "2º Actualizar Datos Faltantes";
echo "<br/>";

// * Tipo de Sociedad y Tipo de Documento
echo "* Tipo de Sociedad y Tipo de Documento<br/>";

$query = "UPDATE clientes SET tipo_sociedad = 'Persona Natural', tipo_doc = 'Cédula' 
WHERE tipo_sociedad = '' OR tipo_doc = ''";
$result = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado 1<br/><br/>";

$query = "UPDATE clientes SET garantia = 'Incompleto' WHERE garantia = ''";
$result = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado 2<br/><br/>";

$query = "UPDATE clientes SET estado_cuenta = 'Al Dia' WHERE estado_cuenta = ''";
$result = mysql_query($query) or die("SQL Error 3-3: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado 3<br/><br/>";

$query = "UPDATE clientes SET vendedor_codigo = 'JF' WHERE vendedor_codigo = ''";
$result = mysql_query($query) or die("SQL Error 3-4: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado 4<br/><br/>";

$query = "UPDATE clientes SET cobrador_codigo = 'JF' WHERE cobrador_codigo = ''";
$result = mysql_query($query) or die("SQL Error 3-5: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado 5<br/><br/>";

$query = "UPDATE clientes SET terminos = 'Efectivo' WHERE terminos = ''";
$result = mysql_query($query) or die("SQL Error 3-6: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado 6<br/><br/>";

$query = "UPDATE clientes SET barrio = 'Cúcuta' WHERE barrio = ''";
$result = mysql_query($query) or die("SQL Error 3-7: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado 7<br/><br/>";

$query = "UPDATE clientes SET ciudad = 'Cúcuta' WHERE ciudad = ''";
$result = mysql_query($query) or die("SQL Error 3-8: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo "- Finalizado 8<br/><br/>";

echo "* Cupo Activo<br/>";

$query = "SELECT Cedula, usocupo FROM clientes_copy";
$result = mysql_query($query) or die("SQL Error 4: " . mysql_error());

while ($row = mysql_fetch_array($result))
{
	if ($row["usocupo"] == 0)
	{
		$query = "UPDATE clientes SET credito_activo = 'false' WHERE cliente_id = '".$row["Cedula"]."' ";
		$result1 = mysql_query($query) or die("SQL Error 5: " . mysql_error());
	}
}
echo "- Finalizado<br/><br/>";

// * Departamento
echo "* Departamento<br/>";

$query = "UPDATE clientes SET departamento = 'Norte de Santander' WHERE departamento = '' OR 
departamento = 'N. de S.' OR departamento = 'NDS' OR departamento = 'NORTE DE SANTANDER' OR departamento = 'SDS' ";
$result = mysql_query($query) or die("SQL Error 6-1: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 1 Finalizada<br/>";

$query = "UPDATE clientes SET departamento = 'Arauca' WHERE departamento = 'ARAUCA' ";
$result = mysql_query($query) or die("SQL Error 6-2: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 2 Finalizada<br/>";

$query = "UPDATE clientes SET departamento = 'Cundinamarca' WHERE departamento = 'CuNdinamarca' OR departamento = 'Ccubdinamarca' ";
$result = mysql_query($query) or die("SQL Error 6-3: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";

$query = "UPDATE clientes SET departamento = 'Antioquia' WHERE departamento = 'ANTIOQUIA' ";
$result = mysql_query($query) or die("SQL Error 6-4: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 4 Finalizada<br/>";

$query = "UPDATE clientes SET departamento = 'Caldas' WHERE departamento = 'MANIZALES' ";
$result = mysql_query($query) or die("SQL Error 6-5: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 5 Finalizada<br/>";

$query = "UPDATE clientes SET departamento = 'Santander' WHERE departamento = 'SANTANDER' ";
$result = mysql_query($query) or die("SQL Error 6-6: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 6 Finalizada<br/>";

$query = "UPDATE clientes SET departamento = 'Atlantico' WHERE departamento = 'ATLANTICO' ";
$result = mysql_query($query) or die("SQL Error 6-7: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 7 Finalizada<br/>";

$query = "UPDATE clientes SET departamento = 'Bolivar' WHERE departamento = 'BOLIVAR' ";
$result = mysql_query($query) or die("SQL Error 6-8: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 8 Finalizada<br/>";

$query = "UPDATE clientes SET departamento = 'San Cristóbal', pais = 'Venezuela' WHERE departamento = 'venz.' ";
$result = mysql_query($query) or die("SQL Error 6-9: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 9 Finalizada<br/>";

echo "- Finalizado<br/><br/>";

// * Actualizar Garantias
echo "* Actualizar Garantias<br/>";

$query = "SELECT clientes_garant_copy.Garantía, clientes_garant_copy.Ok, clientes_garant_copy.Cedula FROM clientes_garant_copy 
LEFT JOIN clientes_copy ON clientes_garant_copy.Cedula = clientes_copy.Cedula";
$result = mysql_query($query) or die("SQL Error 7: " . mysql_error());

while ($row = mysql_fetch_array($result))
{
	$OK = ($row["Ok"] == 1) ? "true":"false";
	$query = "INSERT INTO clientes_garant (cliente_id, garantia, ok) VALUES ('".$row["Cedula"]."', '".$row["Garantía"]."', '".$OK."')";
	$result1 = mysql_query($query) or die("SQL Error 8: " . mysql_error());
}
echo "- Finalizado<br/><br/>";


// * Actualizar Tipo de Cliente
echo "* Actualizar Tipo de Cliente<br/>";

$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Ocasional' WHERE `Tipo de Cliente2` = '' ";
$result = mysql_query($query) or die("SQL Error 9-1: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 1 Finalizada<br/>";

/*$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'FERCO' WHERE `Tipo de Cliente2` = 'Nómina' OR `Tipo de Cliente2` = 'Nómina FERCO'";
$result = mysql_query($query) or die("SQL Error 9-2: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 2 Finalizada<br/>";*/

$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Constructor' WHERE `Tipo de Cliente2` = 'C0NSTRUCTOR' ";
$result = mysql_query($query) or die("SQL Error 9-3: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";

$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Constructor' WHERE `Tipo de Cliente2` = 'constructor' ";
$result = mysql_query($query) or die("SQL Error 9-4: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";

$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Contratista' WHERE `Tipo de Cliente2` = 'contratista' ";
$result = mysql_query($query) or die("SQL Error 9-5: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";

/*$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Ferreteria' WHERE `Tipo de Cliente2` = 'Ferretería' ";
$result = mysql_query($query) or die("SQL Error 9-6: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";*/

/*$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Aseo y Servicios' WHERE `Tipo de Cliente2` = 'Nom. Aseo y servicios' ";
$result = mysql_query($query) or die("SQL Error 9-7: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";*/

/*$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Servicios y Asesorias' WHERE `Tipo de Cliente2` = 'Nóm. Servicios y asesorias' ";
$result = mysql_query($query) or die("SQL Error 9-8: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";*/

/*$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'L. M. P.' WHERE `Tipo de Cliente2` = 'Nómina L. M . P.' ";
$result = mysql_query($query) or die("SQL Error 9-9: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";*/

/*$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Union Temporal' WHERE `Tipo de Cliente2` = 'Unión Temporal' ";
$result = mysql_query($query) or die("SQL Error 9-10: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";*/

$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Aprendiz Sena' WHERE `Tipo de Cliente2` = 'aprendiz Sena' ";
$result = mysql_query($query) or die("SQL Error 9-11: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";

$query = "UPDATE clientes_copy SET `Tipo de Cliente2` = 'Proveedor' WHERE `Tipo de Cliente2` = 'PROVEEDOR' ";
$result = mysql_query($query) or die("SQL Error 9-12: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 3 Finalizada<br/>";

$query = "UPDATE clientes_copy SET Tercero = 'Cliente' WHERE Tercero = 'Ambos' ";
$result = mysql_query($query) or die("SQL Error 9-13: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 4 Finalizada<br/>";

/*$query = "UPDATE clientes_copy SET Tercero = 'Nomina' WHERE Tercero = 'Nómina' ";
$result = mysql_query($query) or die("SQL Error 9-14: " . mysql_error());
if (mysql_affected_rows() > 0)
	echo ". Actualizacion 4 Finalizada<br/>";*/

// * Insertar Tipo de Cliente
echo "* Insertar Tipo de Cliente<br/>";

$query = "SELECT Cedula, Tercero, `Tipo de Cliente2` FROM clientes_copy";
$result = mysql_query($query) or die("SQL Error 10: " . mysql_error());

while ($row = mysql_fetch_array($result))
{
	$query = "INSERT INTO cliente_grupo (cliente_id, clasificacion, tipo) VALUES ('".$row["Cedula"]."', '".$row["Tercero"]."', '".$row["Tipo de Cliente2"]."')";
	$result1 = mysql_query($query) or die("SQL Error 10-1: " . mysql_error());
}
echo "- Finalizado<br/><br/>";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>