<?php
$hostname = "localhost";
$username = "root";
$password = "ingeniamos";
$database = "ferco_local";

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
/*
$query = "DELETE FROM mov_clientes WHERE estado = ''";
$result = mysql_query($query) or die("SQL Error 0-1: " . mysql_error());

$query = "UPDATE caja_final SET saldo = 0 WHERE saldo > total";
$result = mysql_query($query) or die("SQL Error 0-2: " . mysql_error());

$query = "SELECT * caja_final WHERE estado = 'Aprobado'";
$result = mysql_query($query) or die("SQL Error 0-3: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$CajaRC[$row["caja_interno"]] = $row["caja_recibo"];
		$CajaValor[$row["caja_interno"]] = $row["total"];
		$CajaSaldo[$row["caja_interno"]] = $row["saldo"];
	}
}

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
*/

//Arreglar Cartera Total
$query = "SELECT * FROM mov_clientes WHERE interno LIKE 'FER%' AND estado = 'Aprobado' ORDER BY interno, tipo_movimiento DESC, fecha ASC";
$result = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	$Interno = "_Empty_";
	$Deuda = 0;
	$Saldo = 0;
	$Pagado = true;
	
	while ($row = mysql_fetch_array($result))
	{
		if ($row["interno"] != $Interno)
		{
			$Interno = $row["interno"];
			$Deuda = $row["valor"];
			$Saldo = $Deuda;
			$Pagado = false;
			
			echo "Interno -> ".$Interno." - Deuda -> ".$Deuda." || Saldo -> ".$Saldo."<br/>";
			
			if ($row["tipo_movimiento"] == "Debito")
			{
				if ($Deuda == 0)
				{
					echo "El Valor De la Deuda -> ID: ".$row["id"]." || Interno: ".$row["interno"]." es '0'<br/>";
				}
			}
			else
			{
				echo "ERROR! No Posee Deuda -> ID: ".$row["id"]." || Interno: ".$row["interno"]." - ".$row["tipo_movimiento"]." Digitado -> ".$row["fecha_digitado"]."<br/>";
				// Deberia anular y regresar el saldo?
			}
		}
		else
		{
			if ($Saldo != 0)
			{
				if ($row["valor"] == $Deuda)
				{
					$Pagado = true;
					$Saldo = 0;
					
					echo "OK || ID: ".$row["id"]." || Interno: ".$row["interno"]."<br/>";
					
					$query = "UPDATE mov_clientes SET saldo = '0', caja_recibo = '".$CajaRC[$row["caja_interno"]]."' 
					WHERE interno = '".$row["interno"]."' AND estado = 'Aprobado' AND tipo_movimiento = 'Debito'";
					$result1 = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
				}
				else if ($row["valor"] > $Deuda)
				{
					$Pagado = true;
					$Saldo = 0;
					$Sobrante = $row["valor"] - $Deuda;
					
					echo "El Credito con Valor: ".$row["valor"]." -> ID: ".$row["id"]." || Interno: ".$row["interno"]." Sobre Pasa la Deuda a Pagar. -> ".$Deuda."<br/>";
					
					// Arreglar el valor real
					$query = "UPDATE mov_clientes SET valor = '".$Deuda."' WHERE id = '".$row["id"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
					// Saldar Deuda
					$query = "UPDATE mov_clientes SET saldo = '0' WHERE interno = '".$row["interno"]."' 
					AND estado = 'Aprobado' AND tipo_movimiento = 'Debito'";
					$result1 = mysql_query($query) or die("SQL Error 3-3: " . mysql_error());
					/*// Sumar saldo a Caja
					$query = "UPDATE caja_final SET saldo = saldo + '".$Sobrante."' WHERE caja_interno = '".$row["caja_interno"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 3-4: " . mysql_error());*/
				}
				else
				{
					$TmpSaldo = round($Saldo - $row["valor"], 2);
					if ($TmpSaldo < 0)
					{
						$Pagado = true;
						$Saldo = 0;
						$Sobrante = round($row["valor"] - $Saldo, 2);
						
						echo "ERROR FATAL|| ID: ".$row["id"]." || Interno: ".$row["interno"]." El valor del credito exede a la deuda a pagar. || Sobrante: ".$Sobrante."<br/>";
						
						// Arreglar el valor real
						$query = "UPDATE mov_clientes SET valor = valor - '".$Sobrante."' WHERE id = '".$row["id"]."' ";
						$result1 = mysql_query($query) or die("SQL Error 3-5: " . mysql_error());
						// Saldar Deuda
						$query = "UPDATE mov_clientes SET saldo = '0' WHERE interno = '".$row["interno"]."' 
						AND estado = 'Aprobado' AND tipo_movimiento = 'Debito'";
						$result1 = mysql_query($query) or die("SQL Error 3-6: " . mysql_error());
						/*// Sumar saldo a Caja
						$query = "UPDATE caja_final SET saldo = saldo + '".$Sobrante."' WHERE caja_interno = '".$row["caja_interno"]."' ";
						$result1 = mysql_query($query) or die("SQL Error 3-7: " . mysql_error());*/
					}
					else
					{
						$Saldo = $TmpSaldo;
						
						echo "Restando Saldo -> ID: ".$row["id"]." || Interno: ".$row["interno"]." || saldo nuevo: ".$Saldo."<br/>";
						
						$query = "UPDATE mov_clientes SET saldo = '".$Saldo."' WHERE interno = '".$row["interno"]."' 
						AND estado = 'Aprobado' AND tipo_movimiento = 'Debito'";
						$result1 = mysql_query($query) or die("SQL Error 3-8: " . mysql_error());
						
						if ($Saldo == 0)
						{
							$Pagado == true;
							echo "OK || ID: ".$row["id"]." || Interno: ".$row["interno"]."<br/>";
						}
					}
				}
			}
			else
			{
				if ($Pagado == true)
				{
					echo "Abono de sobra -> ID: ".$row["id"]." | Interno: ".$row["interno"]." con Valor de -> ".$row["valor"]."<br/>";
					
					$query = "DELETE FROM mov_clientes WHERE id = '".$row["id"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 3-9: " . mysql_error());
				}
				else
				{
					echo "Deuda en 0 pero saldo con valor? -> ID: ".$row["id"]." | Interno: ".$row["interno"]." Valor -> ".$row["valor"]."<br/>";
					//Deuda en 0?
				}
			}
		}
	}
	echo "Numero de resultados -> ".mysql_num_rows($result);
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
