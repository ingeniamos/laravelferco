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

function GetCode($String)
{
	$Array = str_split($String);

	$UserCode = "";

	for ($i = 0; $i < count($Array); $i++)
	{
		if (!is_numeric($Array[$i]))
		{
			if ($Array[$i] == "_")
				continue;
			$UserCode .= $Array[$i];
		}
		else
			break;
	}
	
	return $UserCode;
}

function GetFullDate($String)
{
	$Array = str_split($String);
	
	$Day = "";
	$Month = "";
	$Year = "";
	$Hour = "";
	$Minute = "";
	$Second = "";
	$Type = "";
	$Date = "";

	for ($i = 0; $i < count($Array); $i++)
	{
		if (is_numeric($Array[$i]))
		{
			if (strlen($Day) < 2)
				$Day .= $Array[$i];
			else if (strlen($Month) < 2)
				$Month .= $Array[$i];
			else if (strlen($Year) < 4)
				$Year .= $Array[$i];
			else if (strlen($Hour) < 1)
			{
				$Hour .= $Array[$i];
				if ($Array[$i+1] == ":")
					$Hour = "0".$Hour."";
				else {
					$Hour .= $Array[$i+1];
					$i++;
				}	
			}
			else if (strlen($Minute) < 2)
				$Minute .= $Array[$i];
			else if (strlen($Second) < 2)
				$Second .= $Array[$i];
		}
		else
		{
			if ($Array[$i] == " " || $Array[$i] == "/" || $Array[$i] == ":" || $Array[$i] == ".")
			{
				if (strlen($Type) == 0 && $Array[$i] == ".")
					$Type .= $Array[$i-1];
				else if (strlen($Type) == 1 && $Array[$i] == ".")
					$Type .= $Array[$i-1];
				
				continue;
			}
			
			if ($Second != "")
			{
				$Date = "".$Year."-".$Month."-".$Day." ".$Hour.":".$Minute.":".$Second."";
				continue;
			}
		}
	}

	if (strcasecmp($Type, "AM") == 0)
		$Date = "".$Year."-".$Month."-".$Day." ".$Hour.":".$Minute.":".$Second."";
	else
		$Date = "".$Year."-".$Month."-".$Day." ".($Hour == 12 ? $Hour:$Hour + 12).":".$Minute.":".$Second."";
	
	return $Date;
}

echo "1- Fact_Final";
echo "<br/>";

$query = "SELECT id, digitado_por FROM fact_final WHERE digitado_por != '' AND CHAR_LENGTH(digitado_por) > 18";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Dig = GetCode($row["digitado_por"]);
		$F_Dig = GetFullDate($row["digitado_por"]);
		
		echo "Anterior -> ".$row["digitado_por"]." || Ahora -> ".$Dig." - ".$F_Dig."<br />";
		
		$query = "UPDATE fact_final SET 
		digitado_por = '".$Dig."', 
		fecha_digitado = '".$F_Dig."' 
		WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";

echo "2- Maquinaria_Final";
echo "<br/>";

$query = "SELECT id, digitado_por FROM maquinaria_final WHERE digitado_por != '' AND CHAR_LENGTH(digitado_por) > 18";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Dig = GetCode($row["digitado_por"]);
		$F_Dig = GetFullDate($row["digitado_por"]);
		
		echo "Anterior -> ".$row["digitado_por"]." || Ahora -> ".$Dig." - ".$F_Dig."<br />";
		
		$query = "UPDATE maquinaria_final SET 
		digitado_por = '".$Dig."', 
		fecha_digitado = '".$F_Dig."' 
		WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";

echo "3- Nom_Extras -> Digitado";
echo "<br/>";

$query = "SELECT id, digitado_por, modificado_por FROM nom_extras  WHERE digitado_por != '' AND CHAR_LENGTH(digitado_por) > 18";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Dig = GetCode($row["digitado_por"]);
		$F_Dig = GetFullDate($row["digitado_por"]);
		
		echo "Anterior -> ".$row["digitado_por"]." || Ahora -> ".$Dig." - ".$F_Dig."<br />";
		
		$query = "UPDATE nom_extras SET 
		digitado_por = '".$Dig."', 
		fecha_digitado = '".$F_Dig."' 
		WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "4- Nom_Extras -> Modificado";
echo "<br/>";

$query = "SELECT id, modificado_por FROM nom_extras  WHERE modificado_por != '' AND CHAR_LENGTH(modificado_por) > 18";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Mod = GetCode($row["modificado_por"]);
		$F_Mod = GetFullDate($row["modificado_por"]);
		
		echo "AnteriorMod -> ".$row["modificado_por"]." || Ahora -> ".$Mod." - ".$F_Mod."<br />";
		
		$query = "UPDATE nom_extras SET 
		modificado_por = '".$Mod."', 
		fecha_modificado = '".$F_Mod."' 
		WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";

echo "5- Nom_Novedades";
echo "<br/>";

$query = "SELECT id, digitado_por FROM nom_novedades WHERE digitado_por != '' AND CHAR_LENGTH(digitado_por) > 18";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Dig = GetCode($row["digitado_por"]);
		$F_Dig = GetFullDate($row["digitado_por"]);
		
		echo "Anterior -> ".$row["digitado_por"]." || Ahora -> ".$Dig." - ".$F_Dig."<br />";
		
		$query = "UPDATE nom_novedades SET 
		digitado_por = '".$Dig."', 
		fecha_digitado = '".$F_Dig."' 
		WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";

echo "6- Nom_Prestamos";
echo "<br/>";

$query = "SELECT id, digitado_por FROM nom_prestamos WHERE digitado_por != '' AND CHAR_LENGTH(digitado_por) > 18";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Dig = GetCode($row["digitado_por"]);
		$F_Dig = GetFullDate($row["digitado_por"]);
		
		echo "Anterior -> ".$row["digitado_por"]." || Ahora -> ".$Dig." - ".$F_Dig."<br />";
		
		$query = "UPDATE nom_prestamos SET 
		digitado_por = '".$Dig."', 
		fecha_digitado = '".$F_Dig."' 
		WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";

echo "7- Produccion_Final";
echo "<br/>";

$query = "SELECT id, digitado_por FROM produccion_final WHERE digitado_por != '' AND CHAR_LENGTH(digitado_por) > 18";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Dig = GetCode($row["digitado_por"]);
		$F_Dig = GetFullDate($row["digitado_por"]);
		
		echo "Anterior -> ".$row["digitado_por"]." || Ahora -> ".$Dig." - ".$F_Dig."<br />";
		
		$query = "UPDATE produccion_final SET 
		digitado_por = '".$Dig."', 
		fecha_digitado = '".$F_Dig."' 
		WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)."<br/>";
?>