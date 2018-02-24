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

//Crear RC para los Movimientos con Interno = 'Bancos'?

// Borrar todos los que tengan interno en blanco = '' y los duplicados de cartera
$query = "DELETE FROM cxp_movs WHERE compra_interno = ''";
$result = mysql_query($query) or die("SQL Error 1-1: " . mysql_error());

$query = "DELETE FROM cxp_movs WHERE grupo = 'Cliente' AND subgrupo = 'Cartera' AND subgrupo2 = 'N/A'";
$result = mysql_query($query) or die("SQL Error 1-2: " . mysql_error());

$query = "SELECT *, SUBSTR(compra_interno, 1, 2) AS CXP FROM cxp_movs WHERE compra_interno != '' AND estado = 'Aprobado' 
GROUP BY compra_interno, tipo_movimiento DESC, fecha_digitado ASC";
$result = mysql_query($query) or die("SQL Error 2: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	/*while ($row = mysql_fetch_array($result))
	{
		echo $row["compra_interno"];
		echo "		";
		echo $row["tipo_movimiento"];
		echo "		";
		echo $row["fecha_digitado"];
		echo "		";
		echo $row["cliente_id"];
		echo "		";
		echo $row["valor"];
		echo "		";
		echo $row["saldo"];
		echo "<br/>";
	}*/
	
	$Interno = "_Empty_";
	$Deuda = 0;
	$Saldo = 0;
	$Pagado = true;
	
	while ($row = mysql_fetch_array($result))
	{
		if ($row["CXP"] == "OR")
			continue;// Ord de reparacion, ignorar hasta nuevo aviso...
		
		if ($row["compra_interno"] != $Interno)
		{
			echo "Compra_Interno -> ".$Interno." - Deuda -> ".$Deuda." || Saldo -> ".$Saldo."<br/>";
			
			$Interno = $row["compra_interno"];
			$Deuda = $row["valor"];
			$Saldo = $row["saldo"];
			$Pagado = false;	
			
			if ($row["tipo_movimiento"] == "Compra")
			{
				if ($Saldo > $Deuda)
				{
					$Saldo = $Deuda;
					
					echo "El Saldo De la Compra -> ".$row["compra_interno"]." Sobre Pasa la Deuda a Pagar. -> ".$Deuda."<br/>";
					
					$query = "UPDATE cxp_movs SET saldo = valor WHERE id = '".$row["id"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 2-2: " . mysql_error());
				}
				else if ($Saldo < 0)
				{
					$Saldo = 0;
					
					echo "El Saldo De la Compra -> ".$row["compra_interno"]." Es Negativo -> ".$Saldo."<br/>";
					
					$query = "UPDATE cxp_movs SET saldo = '0' WHERE id = '".$row["id"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 2-3: " . mysql_error());
				}
			}
			else
			{
				echo "ERROR! No Posee Compra -> ".$row["compra_interno"]." - ".$row["tipo_movimiento"]." - Digitado -> ".$row["fecha_digitado"]."<br/>";
				
				$query = "SELECT estado FROM cxp_movs WHERE compra_interno = '".$row["compra_interno"]."' AND tipo_movimiento = 'Compra'";
				$result1 = mysql_query($query) or die("SQL Error 2-4: " . mysql_error());
				if (mysql_num_rows($result1) > 0)
				{
					$row1 = mysql_fetch_array($result1);
					if ($row1["estado"] == "Pendiente")
					{
						echo "Movimiento Cambiado a Pendiente!<br/>";
						
						$query = "UPDATE cxp_movs SET estado = 'Pendiente' WHERE compra_interno = '".$row["compra_interno"]."'";
						$result2 = mysql_query($query) or die("SQL Error 2-5: " . mysql_error());
						
						$query = "UPDATE caja_final SET estado = 'Pendiente' WHERE caja_interno = '".$row["caja_interno"]."'";
						$result2 = mysql_query($query) or die("SQL Error 2-6: " . mysql_error());
					}
					else
					{
						echo "Movimiento Anulado!<br/>";
						
						$query = "UPDATE cxp_movs SET estado = 'Anulado' WHERE compra_interno = '".$row["compra_interno"]."'";
						$result2 = mysql_query($query) or die("SQL Error 2-7: " . mysql_error());
						
						$query = "UPDATE caja_final SET estado = 'Anulado' WHERE caja_interno = '".$row["caja_interno"]."'";
						$result2 = mysql_query($query) or die("SQL Error 2-8: " . mysql_error());
					}
				}
				//$Interno = "_Empty_";
			}
		}
		else
		{
			if ($Saldo != 0)
			{
				if ($row["valor"] == $Deuda)
				{
					$Saldo = 0;
					
					$query = "UPDATE cxp_movs SET saldo = '0' WHERE compra_interno = '".$row["compra_interno"]."' ";//Ambos compra/abono
					$result1 = mysql_query($query) or die("SQL Error 2-7: " . mysql_error());
				}
				else if ($row["valor"] > $Deuda)
				{
					$Saldo = 0;
					
					echo "El Abono -> ".$row["valor"]." De la Compra -> ".$row["compra_interno"]." Sobre Pasa la Deuda a Pagar. -> ".$Deuda."<br/>";
					
					$query = "UPDATE cxp_movs SET valor = '".$Deuda."', saldo = '0' WHERE id = '".$row["id"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 2-8: " . mysql_error());
					
					$query = "UPDATE cxp_movs SET saldo = '0' WHERE compra_interno = '".$row["compra_interno"]."' 
					AND estado = 'Aprobado' AND tipo_movimiento = 'Compra'";
					$result1 = mysql_query($query) or die("SQL Error 2-9: " . mysql_error());
					
					$query = "UPDATE caja_final SET total = '".$Deuda."', saldo = '0' WHERE caja_interno = '".$row["caja_interno"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 2-10: " . mysql_error());
				}
				else
				{
					$Saldo -= $row["valor"];
					if ($Saldo < 0)
						$Saldo = 0;
					
					$query = "UPDATE cxp_movs SET saldo = '0' WHERE id = '".$row["id"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 2-11: " . mysql_error());
					
					$query = "UPDATE cxp_movs SET saldo = '".$Saldo."' WHERE compra_interno = '".$row["compra_interno"]."' 
					AND estado = 'Aprobado' AND tipo_movimiento = 'Compra'";
					$result1 = mysql_query($query) or die("SQL Error 2-12: " . mysql_error());
					
					$query = "UPDATE caja_final SET saldo = '".$Saldo."' WHERE caja_interno = '".$row["caja_interno"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 2-13: " . mysql_error());
				}
				
				$Pagado = true;
			}
			else
			{
				if ($Pagado == true)
				{
					echo "Abono de sobra -> ".$row["compra_interno"]." con Valor de -> ".$row["valor"]."<br/>";
					
					$query = "DELETE FROM cxp_movs WHERE id = '".$row["id"]."' ";
					$result1 = mysql_query($query) or die("SQL Error 2-14: " . mysql_error());
				}
				else
				{
					if ($row["saldo"] > 0)
					{
						echo "Ajuste de Saldo a '0' en el Abono a Compra -> ".$row["compra_interno"]." con Valor de -> ".$row["valor"]."<br/>";
						
						$query = "UPDATE cxp_movs SET saldo = '0' WHERE compra_interno = '".$row["compra_interno"]."'";//Ambos compra/abono
						$result1 = mysql_query($query) or die("SQL Error 2-15: " . mysql_error());
						
						$query = "UPDATE caja_final SET saldo = '0' WHERE caja_interno = '".$row["caja_interno"]."' ";
						$result1 = mysql_query($query) or die("SQL Error 2-16: " . mysql_error());
					}
				}
			}
		}
	}
}

echo "<br/>";
echo "Fin.";
echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";

/*

if (!isset($_GET['CxP']))
{
	echo "?CxP=ID para Iniciar el Proceso";
	die();
}

echo "<b>Informacion Detallada del CxP \"<u>".$_GET['CxP']."</u>\"</b>";
echo "<br/>";
echo "<br/>";

$query = "SELECT * FROM cxp_movs WHERE compra_interno = '".$_GET['CxP']."' AND estado = 'Aprobado' 
ORDER BY tipo_movimiento DESC, fecha_digitado ASC";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	$Deuda = 0;
	$Saldo = 0;
	
	while ($row = mysql_fetch_array($result))
	{
		if ($row["tipo_movimiento"] == "Compra")
		{
			$Deuda = $row["valor"];
			$Saldo = $row["saldo"];
			
			if ($Saldo > $Deuda)
			{
				$Saldo = $Deuda;
			}
			else if ($Saldo < 0)
			{
				$Saldo = 0;
			}
		}
		else
		{
			if ($Saldo != 0)
			{
				if ($row["valor"] == $Deuda)
				{
					$Saldo = 0;
				}
				else if ($row["valor"] > $Deuda)
				{
					echo "El Abono -> ".$row["valor"]." Sobre Pasa la Deuda a Pagar. -> ".$Deuda."<br/>";
				}
				else
				{
					$Saldo -= $row["valor"];
					if ($Saldo < 0)
						$Saldo = 0;
				}
			}
			else
			{
				echo "Abono de sobra ->".$row["compra_interno"]." con Valor de -> ".$row["valor"]."<br/>";
			}
		}
	}
	echo "Deuda -> ".$Deuda." || Saldo -> ".$Saldo."<br/>";
}

echo "<br/>";
echo "Fin.";
echo "<br/>";

*/