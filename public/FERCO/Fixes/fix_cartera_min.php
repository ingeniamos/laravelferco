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

// Time Check!
$now = time();
echo "Tiempo de Inicio-> ".$now." <br/><br/>";

// Borrar todos los movimientos debitos duplicados y creditos con caja interno = ''
//Debitos
$query = "SELECT id, interno, COUNT(*) AS c FROM mov_clientes WHERE tipo_movimiento = 'Debito' 
AND estado = 'Aprobado' AND interno LIKE 'FER%' GROUP BY interno HAVING c > 1;";
$result = mysql_query($query) or die("SQL Error 1-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		$query = "DELETE FROM mov_clientes WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 1-2: " . mysql_error());
		echo "Borrado Debito Duplicado-> ID: ".$row["id"]." || Interno: ".$row["interno"]."<br/>";
	}
}

//Credito
$query = "SELECT id, interno FROM mov_clientes WHERE tipo_movimiento = 'Credito' 
AND estado = 'Aprobado' AND caja_interno = '' AND interno LIKE 'FER%';";
$result = mysql_query($query) or die("SQL Error 2-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		$query = "DELETE FROM mov_clientes WHERE id = '".$row["id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 1-2: " . mysql_error());
		echo "Borrado Credito con Caja Interno Vacio -> ID: ".$row["id"]." || Interno: ".$row["interno"]."<br/>";
	}
}

$query = "UPDATE caja_final SET saldo = 0 WHERE saldo > total";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

$query = "SELECT id, interno, caja_interno, SUM(valor) AS valor FROM mov_clientes WHERE caja_interno != '' AND caja_recibo = '' 
AND tipo_movimiento = 'Credito' AND estado = 'Aprobado' AND interno LIKE 'FER%' GROUP BY caja_interno";
$result = mysql_query($query) or die("SQL Error 2-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		echo "Valor: ".$row["valor"]." || Interno: ".$row["caja_interno"]."<br/>";
		
		$query = "SELECT * FROM caja_final WHERE caja_interno = '".$row["caja_interno"]."'";
		$result1 = mysql_query($query) or die("SQL Error 2-2: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			$CajaRC[] = array(
				"Caja_Interno" => $row1["caja_interno"],
				"Caja_Recibo" => $row1["caja_recibo"],
			);
			if ($row["valor"] != $row1["saldo"])
			{
				if ($row["valor"] < $row1["saldo"])
				{
					// restar
					echo "Restado <br />";
					$query = "UPDATE caja_final SET saldo = saldo - '".$row["valor"]."' WHERE caja_interno = '".$row["caja_interno"]."'";
					$result1 = mysql_query($query) or die("SQL Error 1: " . mysql_error());
				}
				else if ($row1["saldo"] != 0)
				{
					echo "Error <br />";
					// colocar el saldo igual que el valor del recibo y restarle el valor sumado..
					$query = "UPDATE caja_final SET saldo = total WHERE caja_interno = '".$row["caja_interno"]."'";
					$result1 = mysql_query($query) or die("SQL Error 1: " . mysql_error());
					
					$query = "UPDATE caja_final SET saldo = saldo - '".$row["valor"]."' WHERE caja_interno = '".$row["caja_interno"]."'";
					$result1 = mysql_query($query) or die("SQL Error 1: " . mysql_error());
				}
					
			}
			else if ($row["valor"] == $row1["saldo"])
			{
				echo "Iguales <br />";
				$query = "UPDATE caja_final SET saldo = 0 WHERE caja_interno = '".$row["caja_interno"]."'";
				$result1 = mysql_query($query) or die("SQL Error 1: " . mysql_error());
			}
		}
	}
}

if (isset($CajaRC))
{
	foreach ($CajaRC AS $item)
	{
		$query = "UPDATE mov_clientes SET caja_recibo = '".$item["Caja_Recibo"]."' 
		WHERE caja_interno = '".$item["Caja_Interno"]."' AND caja_recibo = ''";
		$result = mysql_query($query) or die("SQL Error 3: " . mysql_error());
	}
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
