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

//Elminar los de valor 0
$query = "DELETE FROM bancos WHERE valor = '0'";
$result = mysql_query($query) or die("SQL Error 0: " . mysql_error());

$query = "SELECT * FROM bancos WHERE caja_interno = ''";
$result = mysql_query($query) or die("SQL Error 1-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		if ($row["tipo"] == "Cheque" || $row["tipo"] == "Canje de Cheque")
		{
			$query = "SELECT caja_interno, caja_recibo, valor FROM cheques WHERE cheque = '".$row["numero"]."'";
			$result1 = mysql_query($query) or die("SQL Error 1-2: " . mysql_error());
			if (mysql_num_rows($result1) > 0)
			{
				$row1 = mysql_fetch_array($result1);
				
				$query = "SELECT estado FROM caja_final WHERE caja_interno = '".$row1["caja_interno"]."'";
				$result2 = mysql_query($query) or die("SQL Error 1-3: " . mysql_error());
				if (mysql_num_rows($result2) > 0)
				{
					echo "Encontrado en Cheques!<br/>";
					$row2 = mysql_fetch_array($result2);
					
					$query = "UPDATE bancos SET caja_interno = '".$row1["caja_interno"]."', caja_recibo = '".$row1["caja_recibo"]."', 
					estado = '".$row2["estado"]."' WHERE id = '".$row["id"]."'";
					$result2 = mysql_query($query) or die("SQL Error 1-4: " . mysql_error());
				}
			}
		}
		else if ($row["tipo"] == "")
		{
			$query = "UPDATE bancos SET tipo = 'Transferencia' WHERE id = '".$row["id"]."'";
			$result1 = mysql_query($query) or die("SQL Error 1-5: " . mysql_error());
		}
		else
		{
			$query = "SELECT caja_interno, caja_recibo, total, aplicado_a, estado FROM caja_final WHERE aplicado_a LIKE '%".$row["numero"]."%'";
			$result1 = mysql_query($query) or die("SQL Error 1-6: " . mysql_error());
			if (mysql_num_rows($result1) > 0)
			{
				$row1 = mysql_fetch_array($result1);
				
				if ($row["valor"] == $row1["total"])
				{
					echo "Encontrado en Caja! ID-> ".$row["id"]." Factura -> ".$row["numero"]." Caja Interno -> ".$row1["caja_interno"]." - ".$row1["aplicado_a"]." - ".$row1["estado"]." <br />";
					$query = "UPDATE bancos SET caja_interno = '".$row1["caja_interno"]."', caja_recibo = '".$row1["caja_recibo"]."', 
					estado = '".$row1["estado"]."' WHERE id = '".$row["id"]."'";
					$result2 = mysql_query($query) or die("SQL Error 1-7: " . mysql_error());
				}
				else
				{
					echo "ERROR: ID-> ".$row["id"]." Factura -> ".$row["numero"]." Caja Interno -> ".$row1["caja_interno"]." - ".$row1["aplicado_a"]." - ".$row1["estado"]." <br />";
				}
			}
		}
	}
}

$query = "SELECT * FROM caja_final";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		$data[] = array(
			"Caja_Interno" => $row["caja_interno"],
			"Estado" => $row["estado"],
		);
	}
}

echo "ACTUALIZAR ESTADOS DE BANCOS!<br/><br/>";

$query = "SELECT * FROM bancos";
$result = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		foreach($data AS $item)
		{
			if ($row["caja_interno"] == $item["Caja_Interno"])
			{
				$query = "UPDATE bancos SET estado = '".$item["Estado"]."' WHERE id = '".$row["id"]."'";
				$result1 = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
				break;
			}
		}
	}
}

echo "ACTUALIZAR ESTADOS DE CHEQUES!<br/><br/>";

$query = "SELECT * FROM cheques";
$result = mysql_query($query) or die("SQL Error 4-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	$U = 0;
	$D = 0;
	while ($row = mysql_fetch_array($result))
	{
		$Updated = false;
		foreach($data AS $item)
		{
			if ($row["caja_interno"] == $item["Caja_Interno"])
			{
				$Updated = true;
				$query = "UPDATE cheques SET estado = '".$item["Estado"]."' WHERE id = '".$row["id"]."'";
				$result1 = mysql_query($query) or die("SQL Error 4-2: " . mysql_error());
				$U++;
				break;
			}
		}
		if ($Updated == false)
		{
			$query = "DELETE FROM cheques WHERE id = '".$row["id"]."'";
			$result1 = mysql_query($query) or die("SQL Error 4-3: " . mysql_error());
			$D++;
		}
	}
	echo "Cheques Actualizados ->".$U." || Cheques Eliminados -> ".$D."<br/>";
}

echo "BORRAR DUPLICADOS EN BANCOS<br/><br/>";

$query = "SELECT *, COUNT(*) AS num FROM bancos WHERE caja_interno != '' GROUP BY caja_interno HAVING num > 1";
$result = mysql_query($query) or die("SQL Error 5-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	$LastID = "_empty_";
	$Pagado = false;
	$Total = 0;
	
	while ($row = mysql_fetch_array($result))
	{
		$query = "SELECT * FROM bancos WHERE caja_interno = '".$row["caja_interno"]."' ORDER BY fecha ASC";
		$result1 = mysql_query($query) or die("SQL Error 5-2: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			while ($row1 = mysql_fetch_array($result1))
			{
				if ($LastID != $row1["caja_interno"])
				{
					$LastID = $row1["caja_interno"];
					$Pagado = false;
					$Total = $row1["valor"];
					
					foreach($data AS $item)
					{
						if ($row1["caja_interno"] == $item["Caja_Interno"])
						{
							if ($Total == $item["Valor"])
							{
								$Pagado = true;
								echo "Pagado1! -> ".$row1["id"]." -> ".$row1["caja_interno"]." -> ".$row1["valor"]." -> ".$row1["banco"]." -> ".$row1["estado"]."<br/>";
								break;
							}
							else if ($Total > $item["Valor"])
							{
								$Pagado = true;
								echo "Error1! -> ".$row1["id"]." -> ".$row1["caja_interno"]." -> ".$row1["valor"]." -> ".$row1["banco"]." -> ".$row1["estado"]."<br/>";
								break;
							}
						}
					}
				}
				else
				{
					if ($Pagado == false)
					{
						$Total += $row1["valor"];
						
						foreach($data AS $item)
						{
							if ($row1["caja_interno"] == $item["Caja_Interno"])
							{
								if ($Total == $item["Valor"])
								{
									$Pagado = true;
									echo "Pagado2! -> ".$row1["id"]." -> ".$row1["caja_interno"]." -> ".$row1["valor"]." -> ".$row1["banco"]." -> ".$row1["estado"]."<br/>";
									break;
								}
								else if ($Total > $item["Valor"])
								{
									$Pagado = true;
									echo "Error2! -> ".$row1["id"]." -> ".$row1["caja_interno"]." -> ".$row1["valor"]." -> ".$row1["banco"]." -> ".$row1["estado"]."<br/>";
									break;
								}
							}
						}
					}
					else
					{
						echo "Borrar! -> ".$row1["id"]." -> ".$row1["caja_interno"]." -> ".$row1["valor"]." -> ".$row1["banco"]." -> ".$row1["estado"]."<br/>";
						
						$query = "DELETE FROM bancos WHERE id = '".$row1["id"]."'";
						$result2 = mysql_query($query) or die("SQL Error 5-3: " . mysql_error());
					}
				}
			}
		}
	}
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
