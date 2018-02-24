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

echo "* Actualizar Caja -> UserCodes<br/>";

$query = "UPDATE caja_final SET digitado_por = UPPER(digitado_por)";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Actualizar Clientes -> UserCodes<br/>";

$query = "UPDATE clientes SET vendedor_codigo = UPPER(vendedor_codigo), cobrador_codigo = UPPER(cobrador_codigo)";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Actualizar Fact_Final -> UserCodes<br/>";

$query = "UPDATE fact_final SET digitado_por = UPPER(digitado_por), vendedor_codigo = UPPER(vendedor_codigo), 
cobrador_codigo = UPPER(cobrador_codigo)";
$result = mysql_query($query) or die("SQL Error 3: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Actualizar Compras_Final -> UserCodes<br/>";

$query = "UPDATE compras_final SET digitado_por = UPPER(digitado_por)";
$result = mysql_query($query) or die("SQL Error 4: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Actualizar CxP_Movs -> UserCodes<br/>";

$query = "UPDATE cxp_movs SET digitado_por = UPPER(digitado_por)";
$result = mysql_query($query) or die("SQL Error 5: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Actualizar Mov_Clientes -> UserCodes<br/>";

$query = "UPDATE mov_clientes SET digitado_por = UPPER(digitado_por), vendedor_codigo = UPPER(vendedor_codigo), 
cobrador_codigo = UPPER(cobrador_codigo)";
$result = mysql_query($query) or die("SQL Error 6: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Actualizar Produccion_Final -> UserCodes<br/>";

$query = "UPDATE produccion_final SET digitado_por = UPPER(digitado_por)";
$result = mysql_query($query) or die("SQL Error 7: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "* Actualizar Produccion_Proc -> UserCodes<br/>";

$query = "UPDATE produccion_proc SET iniciado_por = UPPER(iniciado_por), finalizado_por = UPPER(finalizado_por)";
$result = mysql_query($query) or die("SQL Error 8: " . mysql_error());

echo "- Filas Afectadas -> ".mysql_affected_rows()."<br />";

echo "<br/>";
echo "Final";
echo "<br/>";
