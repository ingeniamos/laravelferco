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

echo "1- Equipos";
echo "<br/>";

$query = "SELECT DISTINCT unidad FROM apu_equ_originales";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		if ($row["unidad"] == "")
			continue;
		
		$query = "SELECT unidad FROM par_presupuesto_unidad WHERE unidad = '".$row["unidad"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
			continue;
		else
		{
			$query = "INSERT INTO par_presupuesto_unidad (unidad) VALUES ('".$row["unidad"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3: " . mysql_error());
		}
	}
}

echo "- Finalizado<br/><br/>";

echo "2- Mano de Obra";
echo "<br/>";

$query = "SELECT DISTINCT unidad FROM apu_mo_originales";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		if ($row["unidad"] == "")
			continue;
		
		$query = "SELECT unidad FROM par_presupuesto_unidad WHERE unidad = '".$row["unidad"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
			continue;
		else
		{
			$query = "INSERT INTO par_presupuesto_unidad (unidad) VALUES ('".$row["unidad"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3: " . mysql_error());
		}
	}
}

echo "- Finalizado<br/><br/>";

echo "3- Nom_Extras -> Digitado";
echo "<br/>";

$query = "SELECT DISTINCT unidad FROM apu_mat_originales";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		if ($row["unidad"] == "")
			continue;
		
		$query = "SELECT unidad FROM par_presupuesto_unidad WHERE unidad = '".$row["unidad"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
			continue;
		else
		{
			$query = "INSERT INTO par_presupuesto_unidad (unidad) VALUES ('".$row["unidad"]."')";
			$result1 = mysql_query($query) or die("SQL Error 3: " . mysql_error());
		}
	}
}

echo "- Finalizado<br/><br/>";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)."<br/>";
?>