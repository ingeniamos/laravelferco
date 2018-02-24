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

//---------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------

echo "* Insertar Nuevo Permiso para Maquinaria Repuestos.<br/>";

$query = "INSERT INTO module_access (module, sub_module) VALUES ('Maquinaria', 'Repuestos')";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "<br/>";
echo "Final";
echo "<br/>";
