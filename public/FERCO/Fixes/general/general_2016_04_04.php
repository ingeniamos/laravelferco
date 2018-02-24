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

echo "* Actualizar Caja_Interno en Fact_Final.<br/>";

$query = "SELECT * FROM fact_final WHERE caja_recibo != ''";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "SELECT caja_interno FROM caja_final WHERE caja_recibo = '".$row["caja_recibo"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			$query = "UPDATE fact_final SET caja_interno = '".$row1["caja_interno"]."' WHERE id = '".$row["id"]."'";
			$result2 = mysql_query($query) or die("SQL Error 3: " . mysql_error());
			if (mysql_affected_rows() > 0)
				echo "- Fact Actualizada -> ".$row["interno"]."<br />";
		}
	}
}

echo "<br/>";
echo "Final";
echo "<br/>";
