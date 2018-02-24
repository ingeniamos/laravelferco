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
echo "<br /><br />";

//Actualizar los recibos de caja con el interno del prestamo
$query = "SELECT interno, fecha FROM nom_prestamos WHERE fecha > '2016-02-17'";
$result = mysql_query($query) or die("SQL Error 1-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$query = "SELECT *, SUBSTR(fecha, 1, 4) AS year, SUBSTR(fecha, 6, 2) AS month, SUBSTR(fecha, 9, 2) AS day FROM 
		nom_prestamos_movs WHERE interno = '".$row["interno"]."' ORDER BY fecha ASC";
		$result1 = mysql_query($query) or die("SQL Error 1-2: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			while($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
			{
				echo "Fecha Anterior -> ".$row1["interno"]." - ".$row1["fecha"]."<br />";
				if ($row1["day"] == 14)
				{
					$Year = $row1["year"];
					$Month = $row1["month"] - 1;
					
					if ($Month < 1)
					{
						$Year = $Year - 1;
						$Month = 12;
					}
					else if ($Month < 10)
						$Month = "0".$Month;
					
					$Fecha = $Year."-".$Month."-27";
					echo "Fecha Nueva -> ".$row1["interno"]." - ".$Fecha."<br />";
				}
				if ($row1["day"] == 27)
				{
					$Fecha = $row1["year"]."-".$row1["month"]."-14";
					echo "Fecha Nueva -> ".$row1["interno"]." - ".$Fecha."<br />";
				}
				
				$query = "UPDATE nom_prestamos_movs SET fecha = '".$Fecha."' WHERE id = '".$row1["id"]."' ";
				$result2 = mysql_query($query) or die("SQL Error 1-3: " . mysql_error());
				echo "nom_prestamos_mov ".mysql_affected_rows()." filas afectadas...<br />";
				
				$query = "UPDATE mov_clientes SET fecha = '".$Fecha."' WHERE interno = '".$row1["nombre"]."' ";
				$result2 = mysql_query($query) or die("SQL Error 1-4: " . mysql_error());
				echo "mov_clientes ".mysql_affected_rows()." filas afectadas...<br />";
				
				echo "<br />";
			}
		}
	}
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>