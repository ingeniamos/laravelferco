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

echo "* Actualizar Juridico con Acento.<br/>";

$query = "UPDATE `par_ter_tipo_sociedad` SET `tipo_sociedad`='Persona Jurídica' WHERE (`id`='1')";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Insertar Nuevo Permiso para Facturacion Directa.<br/>";

$query = "INSERT INTO module_access (module, sub_module) VALUES ('Ventas', 'Fact_Directa')";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "<br/>";
echo "Final";
echo "<br/>";
