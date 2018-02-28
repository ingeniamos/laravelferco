<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
include('config.php');
ini_set('memory_limit', '256M');
$connect = mysql_connect($hostname, $username, $password)
or die('Could not connect: ' . mysql_error());
mysql_set_charset('utf8',$connect);

if (isset($_SESSION["UserAccess"]) && $_SESSION["UserAccess"][0]["DB"] != "")
	$bool = mysql_select_db($_SESSION["UserAccess"][0]["DB"], $connect);
else
	$bool = mysql_select_db($database, $connect);

mysql_query("SET NAMES utf8");
if ($bool === False){
	print "can't find ".$database."";
}
$final = false;

//-------------------------------------------------------------------------------------------------------------------------//
//------------------------------------------------------- FUNCTIONS -------------------------------------------------------//

function Anular_FactMovs($ClienteID, $Ord_Compra, $Interno, $Motivo)
{
	global $DEBUG;
	
	$query = "UPDATE fact_final SET 
	estado = 'Anulado', 
	anulado_por = '".$_SESSION["UserCode"]."', 
	motivo_anulado = '".$Motivo."', 
	fecha_anulado = NOW() 
	WHERE orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."' AND interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() <= 0)
		return false;
	else
		return true;
}

function SumarSaldo_Caja($Saldo, $Caja_Interno)
{
	global $DEBUG;
	
	$query = "UPDATE caja_final SET saldo = saldo + '".$Saldo."' WHERE caja_interno = '".$Caja_Interno."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() <= 0)
		return false;
	else
		return true;
}

function RestarSaldo_Caja($Saldo, $Caja_Interno, $Caja_Recibo)
{
	global $DEBUG;
	
	$query = "UPDATE caja_final SET saldo = saldo - '".$Saldo."' 
	WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() <= 0)
		return false;
	else
		return true;
}

function SumarSaldo_Cartera($Saldo, $Interno)
{
	global $DEBUG;
	
	//--- Mov_Clientes - UPDATE
	$query = "UPDATE mov_clientes SET saldo = saldo + '".$Saldo."' 
	WHERE interno = '".$Interno."' AND tipo_movimiento = 'Debito'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() <= 0)
		return false;
	else
		return true;
}

function RestarSaldo_Cartera($Saldo, $Interno)
{
	global $DEBUG;
	
	//--- Mov_Clientes - UPDATE
	$query = "UPDATE mov_clientes SET saldo = saldo - '".$Saldo."' 
	WHERE interno = '".$Interno."' AND tipo_movimiento = 'Debito'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() <= 0)
		return false;
	else
		return true;
}

function SumarSaldo_CxP($Saldo, $Interno)
{
	global $DEBUG;
	
	//--- Mov_Clientes - UPDATE
	$query = "UPDATE cxp_movs SET saldo = saldo + '".$Saldo."' 
	WHERE compra_interno = '".$Interno."' AND tipo_movimiento = 'Compra'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() <= 0)
		return false;
	else
		return true;
}

function Anular_Caja_Recibo($Caja_Interno, $Caja_Recibo)
{
	global $DEBUG;
	
	$query = "UPDATE caja_final SET 
	estado = 'Anulado', 
	anulado_por = '".$_SESSION["UserCode"]."', 
	fecha_anulado = NOW() 
	WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$query = "UPDATE bancos SET estado = 'Anulado' WHERE caja_interno = '".$Caja_Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		$query = "UPDATE cheques SET estado = 'Anulado' WHERE caja_interno = '".$Caja_Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	}
	else
		return false;
}

function Anular_CarteraMovs($ClienteID, $Ord_Compra, $Interno, $Motivo)
{
	global $DEBUG;
	
	$query = "SELECT tipo_movimiento, valor, saldo, caja_interno, caja_recibo FROM mov_clientes 
	WHERE orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."' AND interno = '".$Interno."' AND estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if ($row["tipo_movimiento"] == "Credito")
			{
				SumarSaldo_Caja($row["valor"], $row["caja_interno"]);
			}
		}
	}
	
	$query = "UPDATE mov_clientes SET 
	estado = 'Anulado', 
	anulado_por = '".$_SESSION["UserCode"]."', 
	motivo_anulado = '".$Motivo."', 
	fecha_modificado = NOW() 
	WHERE orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."' AND interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() <= 0)
		return false;
	else
		return true;
}

function Anular_CxPMovs($Interno, $Motivo)
{
	global $DEBUG;
	
	$query = "SELECT tipo_movimiento, valor, saldo, caja_interno, caja_recibo FROM cxp_movs 
	WHERE compra_interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if ($row["tipo_movimiento"] == "Abono a Compra")
			{
				SumarSaldo_Caja($row["valor"], $row["caja_interno"]);
				// No se Anula?
				//Anular_Caja_Recibo($row["caja_interno"], $row["caja_recibo"]);
			}
		}
	}
	
	$query = "UPDATE cxp_movs SET 
	estado = 'Anulado', 
	anulado_por = '".$_SESSION["UserCode"]."', 
	motivo_anulado = '".$Motivo."', 
	fecha_anulado = NOW() 
	WHERE compra_interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
}

function Anular_CompraMovs($ClienteID, $Entrada, $Interno, $Motivo)
{
	global $DEBUG;
	
	$query = "UPDATE compras_final SET 
	estado = 'Anulado', 
	anulado_por = '".$_SESSION["UserCode"]."', 
	motivo_anulado = '".$Motivo."', 
	fecha_anulado = NOW() 
	WHERE cliente_id = '".$ClienteID."' AND compra_interno = '".$Interno."' AND compra_entrada = '".$Entrada."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}

function Anular_MaquinariaMovs($Interno, $Motivo)
{
	global $DEBUG;
	
	$query = "UPDATE maquinaria_final SET 
	estado = 'Anulado', 
	anulado_por = '".$_SESSION["UserCode"]."', 
	motivo_anulado = '".$Motivo."', 
	fecha_modificado = NOW() 
	WHERE ord_reparacion = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}

function Anular_ProduccionMovs($ClienteID, $Ord_Compra, $Interno, $Motivo)
{
	global $DEBUG;
	
	$query = "SELECT orden_produccion FROM produccion_final WHERE interno = '".$Interno."' 
	AND orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		Del_InventoryMovs($row["orden_produccion"]);
		
		$query = "UPDATE produccion_final SET 
		estado = 'Anulado', 
		anulado_por = '".$_SESSION["UserCode"]."', 
		motivo_anulado = '".$Motivo."', 
		fecha_anulado = NOW() 
		WHERE orden_produccion = '".$row["orden_produccion"]."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-2: ".mysql_error():"");
		return true;
	}
	else
		return false;	
}

function Del_InventoryMovs($Interno)
{
	global $DEBUG;
	
	if ($Interno == "")
		return "";
	
	$Fecha = "";
	$query = "SELECT * FROM inventario_movs WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Fecha = $row["fecha"];
			$query = "SELECT * FROM inventario_movs WHERE cod_fab = '".$row["cod_fab"]."' 
			ORDER BY fecha DESC LIMIT 1";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			if (mysql_num_rows($result1) > 0)
			{
				$row1 = mysql_fetch_array($result1);
				if ($row["fecha"] == $row1["fecha"])
				{
					if ($row["tipo"] == "Entrada")
					{
						$query = "UPDATE productos SET 
						costo = '".$row["viejo_costo"]."', 
						ultimo_costo = '".$row["ultimo_costo"]."', 
						costo_promedio = '".$row["viejo_costo_promedio"]."', 
						existencia = existencia - '".$row["cantidad"]."' 
						WHERE cod_fab = '".$row["cod_fab"]."' ";
						$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
					}
					else if ($row["tipo"] == "Salida")
					{
						$query = "UPDATE productos SET 
						costo = '".$row["viejo_costo"]."', 
						ultimo_costo = '".$row["ultimo_costo"]."', 
						costo_promedio = '".$row["viejo_costo_promedio"]."', 
						existencia = existencia + '".$row["cantidad"]."' 
						WHERE cod_fab = '".$row["cod_fab"]."' ";
						$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
					}
				}
				else
				{
					if ($row["tipo"] == "Entrada")
					{
						$query = "UPDATE productos SET existencia = existencia - '".$row["cantidad"]."' 
						WHERE cod_fab = '".$row["cod_fab"]."' ";
						$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
					}
					else if ($row["tipo"] == "Salida")
					{
						$query = "UPDATE productos SET existencia = existencia + '".$row["cantidad"]."' 
						WHERE cod_fab = '".$row["cod_fab"]."' ";
						$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
					}
				}
			}
			else
			{
				if ($row["tipo"] == "Entrada")
				{
					$query = "UPDATE productos SET existencia = existencia - '".$row["cantidad"]."' 
					WHERE cod_fab = '".$row["cod_fab"]."' ";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
				}
				else if ($row["tipo"] == "Salida")
				{
					$query = "UPDATE productos SET existencia = existencia + '".$row["cantidad"]."' 
					WHERE cod_fab = '".$row["cod_fab"]."' ";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
				}
			}
		}
		
		$query = "DELETE FROM inventario_movs WHERE interno = '".$Interno."' ";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	}
	return $Fecha;
}

function Del_InventoryMovs2($Interno)
{
	global $DEBUG;
	
	if ($Interno == "")
		return false;
	
	$query = "SELECT * FROM requerimientos_inventario WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if ($row["tipo"] == "Entrada")
			{
				$query = "UPDATE requerimientos_productos SET existencia = existencia - '".$row["cantidad"]."' 
				WHERE codigo = '".$row["codigo"]."' ";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
			}
			else if ($row["tipo"] == "Salida")
			{
				$query = "UPDATE requerimientos_productos SET existencia = existencia + '".$row["cantidad"]."' 
				WHERE codigo = '".$row["codigo"]."' ";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
			}
		}
		
		$query = "DELETE FROM requerimientos_inventario WHERE interno = '".$Interno."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		return true;
	}
}

function Anular_Final($ClienteID, $Ord_Compra, $Interno, $From, $Motivo)
{
	global $DEBUG;
	
	switch ($From)
	{
		case "Venta":
			Anular_CarteraMovs($ClienteID, $Ord_Compra, $Interno, $Motivo);
			//Eliminado hasta que sea necesario o se tenga una mejor idea de cuando eliminar movimientos de produccion
			//Anular_ProduccionMovs($ClienteID, $Ord_Compra, $Interno, $Motivo);
			Del_InventoryMovs($Interno);
		break;
		case "Compra":
			Anular_CxPMovs($Interno, $Motivo);
			Del_InventoryMovs($Interno);
		break;
		case "Cartera":
			Anular_FactMovs($ClienteID, $Ord_Compra, $Interno, $Motivo);
			Anular_ProduccionMovs($ClienteID, $Ord_Compra, $Interno, $Motivo);
			Del_InventoryMovs($Interno);
		break;
		case "CxP":
			Anular_CompraMovs($ClienteID, $Ord_Compra, $Interno, $Motivo);
			Anular_MaquinariaMovs($Interno, $Motivo);
			Del_InventoryMovs($Interno);
		break;
		case "Produccion":
			Anular_FactMovs($ClienteID, $Ord_Compra, $Interno, $Motivo);
			Anular_CarteraMovs($ClienteID, $Ord_Compra, $Interno, $Motivo);
			Del_InventoryMovs($Interno);
		break;
	}
	
	// Buscar Despachado "No Finalizado" y que no sea el mismo
	$query = "SELECT * FROM fact_movs WHERE cantidad_despachada > '0' AND interno != '".$Interno."' 
	AND orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result)>0)
	{
		$row = mysql_fetch_array($result);
		$TmpInterno = $row["interno"];
		$TmpCode = $row["codigo"];
		// Buscar Productos Despachados "Finalizados"
		$query = "SELECT codigo FROM fact_movs WHERE cantidad_despachada = '0' AND interno = '".$TmpInterno."' 
		AND orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result)>0)
		{
			while ($row = mysql_fetch_array($result))
			{
				$TmpCantidad = 0;
				// Buscar Productos "Finalizados"
				$query = "SELECT codigo, cantidad, cantidad_despachada FROM fact_movs WHERE codigo = ".$row["codigo"]." AND 
				orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				while ($row1 = mysql_fetch_array($result1))
				{
					$TmpCantidad = $TmpCantidad + $row1["cantidad"];
				}
				// Buscar La Cantidad Despachada
				$query = "SELECT cantidad FROM fact_movs WHERE codigo = '".$row["codigo"]."' AND 
				interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				$row1 = mysql_fetch_array($result1);
				
				// Colocar la Cantidad Real y Cantidad Despachada
				$query = "UPDATE fact_movs SET cantidad = '".$TmpCantidad."', 
				cantidad_despachada = cantidad_despachada + ".$row1["cantidad"]." WHERE codigo = '".$row["codigo"]."' AND 
				interno = '".$TmpInterno."' AND orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			}
		}
		// Buscar Productos Despachados
		$query = "SELECT codigo, cantidad FROM fact_movs WHERE interno = '".$Interno."' 
		AND orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		while ($row = mysql_fetch_array($result))
		{
			// Regresar Productos Despachados
			$query = "UPDATE fact_movs SET cantidad_despachada = cantidad_despachada - '".$row["cantidad"]."'
			WHERE codigo = '".$row["codigo"]."' AND interno = '".$TmpInterno."' AND orden_compra = '".$Ord_Compra."' 
			AND cliente_id = '".$ClienteID."'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		}
	}

	// Buscar Facturas (No necesario porq Anular_CarteraMovs ya lo hace...)
	/*$query = "SELECT DISTINCT * FROM mov_clientes WHERE tipo_movimiento = 'Credito' AND interno = '".$Interno."' 
	AND orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			// Buscar Pago
			$query1 = "SELECT * FROM mov_clientes WHERE tipo_movimiento = 'Credito' AND interno = '".$Interno."' 
			AND caja_interno = '".$row["caja_interno"]."' AND caja_recibo = '".$row["caja_recibo"]."'";
			$result1 = mysql_query($query1) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
			if (mysql_num_rows($result1)>0)
			{
				$TotalDeuda = 0;
				while ($row1 = mysql_fetch_array($result1))
				{
					// Totalizar Deuda
					$TotalDeuda = $TotalDeuda + $row["valor"];
				}
				// Sumar Deuda
				$query = "UPDATE caja_final SET saldo = saldo + ".$TotalDeuda." WHERE caja_interno = '".$row["caja_interno"]."' 
				AND caja_recibo = '".$row["caja_recibo"]."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
			}
		}
		
		// Borrar Movimientos
		//$query = "DELETE FROM mov_clientes WHERE interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."'";
		//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
	}*/
}

function GetProduct_Info($CodFab)
{
	global $DEBUG;
	
	$query = "SELECT costo, ultimo_costo, costo_promedio, existencia, stock_minimo, venta_promedio FROM productos 
	WHERE cod_fab = '".$CodFab."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array(
			"Costo" => $row["costo"],
			"Ultimo_Costo" => $row["ultimo_costo"],
			"Costo_Promedio" => $row["costo_promedio"],
			"Existencia" => $row["existencia"],
			"Stock_Minimo" => $row["stock_minimo"],
			"Venta_Promedio" => $row["venta_promedio"],
		);
	}
	return $data;
}

function GetProduct_Info2($Codigo)
{
	global $DEBUG;
	
	$query = "SELECT valor, existencia FROM requerimientos_productos WHERE codigo = '".$Codigo."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array(
			'Valor' => $row["valor"],
			'Existencia' => $row["existencia"],
		);
	}
	return $data;
}

function AfectarInventario($CodFab, $Cantidad, $Costo, $Interno, $Fecha, $Motivo, $Entrada) // Revisar en Ajuste de Compra (que hacer con los costos?)
{
	global $DEBUG;
	
	//--- INSERT INTO Inventario_Movs
	$Tmp = GetProduct_Info($CodFab);
	$Existencia = $Tmp[0]['Existencia'];
	$Costo_Actual = $Tmp[0]['Costo'];
	$Ultimo_Costo = $Tmp[0]['Ultimo_Costo'];
	$Costo_Promedio = $Tmp[0]['Costo_Promedio'];
	
	switch ($Entrada)
	{
		case "Compra":
			//--- CostoPromedio
			$Total_Costo = ($Costo_Promedio * $Existencia) + ($Costo * $Cantidad);
			$Total_Cantidad = $Existencia + $Cantidad;
			$Total = $Total_Costo / $Total_Cantidad;
			
			$query = "INSERT INTO inventario_movs 
			(cod_fab, cantidad, costo, viejo_costo, costo_promedio, viejo_costo_promedio, ultimo_costo, existencia, interno, 
			motivo, fecha, tipo) VALUES ('".$CodFab."', '".$Cantidad."', '".$Costo."',  '".$Costo_Actual."', '".$Total."', 
			'".$Costo_Promedio."', '".$Ultimo_Costo."', '".$Existencia."', '".$Interno."', '".$Motivo."', ";
			$query .= $Fecha == "" ? "NOW(), ":"'".$Fecha."', ";
			$query .= "'Entrada')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			if (mysql_affected_rows() > 0)
			{
				$query = "UPDATE productos SET 
				costo = '".$Costo."', 
				ultimo_costo = '".$Costo_Actual."', 
				costo_promedio = '".$Total."', 
				existencia = existencia + '".$Cantidad."' 
				WHERE cod_fab = '".$CodFab."' ";
				$result = mysql_query($query) or die(mysql_error());
			}
		break;
		case "Produccion_Producir":
			$query = "INSERT INTO inventario_movs 
			(cod_fab, cantidad, costo, viejo_costo, costo_promedio, viejo_costo_promedio, ultimo_costo, existencia, interno, 
			motivo, fecha, tipo) VALUES ('".$CodFab."', '".$Cantidad."', '".$Costo_Actual."', '".$Costo_Actual."', 
			'".$Costo_Promedio."', '".$Costo_Promedio."', '".$Ultimo_Costo."', '".$Existencia."', '".$Interno."', 
			'".$Motivo."', ";
			$query .= $Fecha == "" ? "NOW(), ":"'".$Fecha."', ";
			$query .= "'Entrada')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			if (mysql_affected_rows() > 0)
			{
				$query = "UPDATE productos SET 
				existencia = existencia + '".$Cantidad."' 
				WHERE cod_fab = '".$CodFab."' ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
		break;
		case "Produccion_Consumir":
			$query = "INSERT INTO inventario_movs 
			(cod_fab, cantidad, costo, viejo_costo, costo_promedio, viejo_costo_promedio, ultimo_costo, existencia, interno, 
			motivo, fecha, tipo) VALUES ('".$CodFab."', '".$Cantidad."', '".$Costo_Actual."', '".$Costo_Actual."', 
			'".$Costo_Promedio."', '".$Costo_Promedio."', '".$Ultimo_Costo."', '".$Existencia."', '".$Interno."', 
			'".$Motivo."', ";
			$query .= $Fecha == "" ? "NOW(), ":"'".$Fecha."', ";
			$query .= "'Salida')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			if (mysql_affected_rows() > 0)
			{
				$query = "UPDATE productos SET 
				existencia = existencia - '".$Cantidad."' 
				WHERE cod_fab = '".$CodFab."' ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
		break;
		case "Venta":
			$query = "INSERT INTO inventario_movs 
			(cod_fab, cantidad, costo, viejo_costo, costo_promedio, viejo_costo_promedio, ultimo_costo, existencia, interno, 
			motivo, fecha, tipo) VALUES ('".$CodFab."', '".$Cantidad."', '".$Costo."', '".$Costo_Actual."', '".$Costo_Promedio."', 
			'".$Costo_Promedio."', '".$Ultimo_Costo."', '".$Existencia."', '".$Interno."', '".$Motivo."', ";
			$query .= $Fecha == "" ? "NOW(), ":"'".$Fecha."', ";
			$query .= "'Salida')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			if (mysql_affected_rows() > 0)
			{
				$query = "UPDATE productos SET 
				existencia = existencia - '".$Cantidad."' 
				WHERE cod_fab = '".$CodFab."' ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
		break;
		case "Ajuste_Entrada":
			$query = "INSERT INTO inventario_movs (cod_fab, cantidad, costo, viejo_costo, costo_promedio, 
			viejo_costo_promedio, ultimo_costo, existencia, motivo, fecha, tipo, observacion) VALUES 
			('".$CodFab."', '".$Cantidad."', '".$Costo_Actual."', '".$Costo_Actual."', '".$Costo_Promedio."', 
			'".$Costo_Promedio."', '".$Ultimo_Costo."', '".$Existencia."', 'Ajuste', NOW(), 'Entrada', '".$Motivo."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			if (mysql_affected_rows() > 0)
			{
				$query = "UPDATE productos SET 
				existencia = existencia + '".$Cantidad."' 
				WHERE cod_fab = '".$CodFab."' ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
		break;
		case "Ajuste_Salida":
			$query = "INSERT INTO inventario_movs (cod_fab, cantidad, costo, viejo_costo, costo_promedio, 
			viejo_costo_promedio, ultimo_costo, existencia, motivo, fecha, tipo, observacion) VALUES 
			('".$CodFab."', '".$Cantidad."', '".$Costo_Actual."', '".$Costo_Actual."', '".$Costo_Promedio."', 
			'".$Costo_Promedio."', '".$Ultimo_Costo."', '".$Existencia."', 'Ajuste', NOW(), 'Salida', '".$Motivo."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			if (mysql_affected_rows() > 0)
			{
				$query = "UPDATE productos SET 
				existencia = existencia - '".$Cantidad."' 
				WHERE cod_fab = '".$CodFab."' ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
		break;
	}
}

function Presupuesto_CrearClasificacion($Clasificacion, $Notas)
{
	global $DEBUG;
	
	if ($Clasificacion == "")
		return -1;
	
	$Clasificacion = strtolower($Clasificacion);
	
	$query = "SELECT * FROM apu_clasificacion WHERE clasificacion = '".$Clasificacion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		echo "EXIST";
		return 0;
	}
	else
	{
		$query = "INSERT INTO apu_clasificacion (clasificacion, nota) VALUES ('".$Clasificacion."', '".$Notas."') ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		$APU = "apu_".$Clasificacion."";
		$APU_Movs = "apu_".$Clasificacion."_movs";
		$Mano_de_Obra = "apu_mo_".$Clasificacion."";
		$Mano_de_Obra_Movs = "apu_mo_".$Clasificacion."_movs";
		$Equipos = "apu_equ_".$Clasificacion."";
		$Equipos_Prov = "apu_equ_".$Clasificacion."_prov";
		$Materiales = "apu_mat_".$Clasificacion."";
		$Materiales_Prov = "apu_mat_".$Clasificacion."_prov";
		$Grupos = "par_presupuesto_grupo_".$Clasificacion."";
		$SubGrupos = "par_presupuesto_subgrupo_".$Clasificacion."";
		
		$query = "DROP TABLE IF EXISTS `".$APU."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$APU."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`codigo2` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`nombre` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`unidad` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`desperdicios` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`gastos` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`digitado_por` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$query .= "`modificado_por` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$query .= "`notas` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "PRIMARY KEY (`id`), ";
		$query .= "KEY `".$APU."_codigo` (`codigo`) ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
		
		$query = "DROP TRIGGER IF EXISTS `update_presupuesto_A-U_".$APU."` ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-3: ".mysql_error().$query:"");
		
		$query = "CREATE TRIGGER `update_presupuesto_A-U_".$APU."` ";
		$query .= "AFTER UPDATE ON `".$APU."` ";
		$query .= "FOR EACH ROW ";
		$query .= "BEGIN";
		$query .= "	IF NEW.codigo != OLD.codigo THEN ";
		$query .= "		UPDATE presupuesto_movs SET codigo = NEW.codigo WHERE codigo = OLD.codigo; ";
		$query .= "	END IF; ";
		$query .= "END";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-4: ".mysql_error().$query:"");
		
		$query = "DROP TABLE IF EXISTS `".$APU_Movs."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-5: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$APU_Movs."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`objeto_codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`cantidad` decimal(20,3) NOT NULL DEFAULT '0.000', ";
		$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`total` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`tipo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fijo` enum('true','false') NOT NULL DEFAULT 'false', ";
		$query .= "`clasificacion` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "PRIMARY KEY (`id`), ";
		$query .= "KEY `".$APU."_codigo` (`codigo`), ";
		$query .= "CONSTRAINT `".$APU."_codigo` FOREIGN KEY (`codigo`) REFERENCES `".$APU."` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-6: ".mysql_error():"");
		
		$query = "DROP TRIGGER IF EXISTS `update_apu_A-U_".$APU_Movs."` ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-7: ".mysql_error().$query:"");
		
		$query = "CREATE TRIGGER `update_apu_A-U_".$APU_Movs."` ";
		$query .= "AFTER UPDATE ON `".$APU_Movs."` ";
		$query .= "FOR EACH ROW ";
		$query .= "BEGIN";
		$query .= "	DECLARE p_desp decimal(20, 2); ";
		$query .= "	DECLARE total_desp decimal(20, 2); ";
		$query .= "	DECLARE p_gast decimal(20, 2); ";
		$query .= "	DECLARE total_gast decimal(20, 2); ";
		$query .= "	DECLARE total_apu decimal(20, 2); ";
		$query .= "	DECLARE total_mat decimal(20, 2); ";
		$query .= "	DECLARE total_mo decimal(20, 2); ";
		$query .= "	DECLARE total_equ decimal(20, 2); ";
		$query .= "	DECLARE total_final decimal(20, 2); ";
		$query .= "	SELECT desperdicios INTO p_desp FROM `".$APU."` WHERE codigo = NEW.codigo; ";
		$query .= "	SELECT gastos INTO p_gast FROM `".$APU."` WHERE codigo = NEW.codigo; ";
		$query .= "	SELECT SUM(total) INTO total_apu FROM `".$APU_Movs."` WHERE tipo = 'APU' AND codigo = NEW.codigo; ";
		$query .= "	SELECT SUM(total) INTO total_mat FROM `".$APU_Movs."` WHERE tipo = 'Materiales' AND codigo = NEW.codigo; ";
		$query .= "	SELECT SUM(total) INTO total_mo FROM `".$APU_Movs."` WHERE tipo = 'Mano de Obra' AND codigo = NEW.codigo; ";
		$query .= "	SELECT SUM(total) INTO total_equ FROM `".$APU_Movs."` WHERE tipo = 'Equipos' AND codigo = NEW.codigo; ";
		$query .= "	SET total_desp := ((total_mat / 100) * p_desp); ";
		$query .= "	SET total_gast := ((total_mo / 100) * p_gast); ";
		$query .= "	IF ISNULL(total_desp) THEN SET total_desp := 0; END IF; ";
		$query .= "	IF ISNULL(total_gast) THEN SET total_gast := 0; END IF; ";
		$query .= "	IF ISNULL(total_apu) THEN SET total_apu := 0; END IF; ";
		$query .= "	IF ISNULL(total_mat) THEN SET total_mat := 0; END IF; ";
		$query .= "	IF ISNULL(total_mo) THEN SET total_mo := 0; END IF; ";
		$query .= "	IF ISNULL(total_equ) THEN SET total_equ := 0; END IF; ";
		$query .= "	SET total_final := total_desp + total_gast + total_apu + total_mat + total_mo + total_equ; ";
		$query .= "	UPDATE `".$APU."` SET valor = total_final WHERE codigo = NEW.codigo; ";
		$query .= "END";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-8: ".mysql_error().$query:"");
		
		$query = "DROP TABLE IF EXISTS `".$Mano_de_Obra."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-1: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$Mano_de_Obra."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`codigo2` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`nombre` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`unidad` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`valor_sc` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`valor_pd` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`valor_scc` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`notas` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha` date NOT NULL DEFAULT '0000-00-00', ";
		$query .= "`digitado_por` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$query .= "`modificado_por` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$query .= "PRIMARY KEY (`id`), ";
		$query .= "KEY `".$Mano_de_Obra."_codigo` (`codigo`) ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-2: ".mysql_error():"");
		
		$query = "DROP TRIGGER IF EXISTS `update_apu_movs_A-U_".$Mano_de_Obra."` ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-3: ".mysql_error().$query:"");
		
		$query = "CREATE TRIGGER `update_apu_movs_A-U_".$Mano_de_Obra."` ";
		$query .= "AFTER UPDATE ON `".$Mano_de_Obra."` ";
		$query .= "FOR EACH ROW ";
		$query .= "BEGIN";
		$query .= "	UPDATE `".$APU_Movs."` SET objeto_codigo = NEW.codigo, valor = NEW.valor_scc, total = (((NEW.valor_scc * cantidad) /100) * uso) WHERE tipo = 'Mano de Obra' AND objeto_codigo = OLD.codigo; ";
		$query .= "END";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-4: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `".$Mano_de_Obra_Movs."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-5: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$Mano_de_Obra_Movs."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`concepto` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`total` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`tipo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fijo` enum('true','false') NOT NULL DEFAULT 'false', ";
		$query .= "PRIMARY KEY (`id`), ";
		$query .= "KEY `".$Mano_de_Obra."_codigo` (`codigo`), ";
		$query .= "CONSTRAINT `".$Mano_de_Obra."_codigo` FOREIGN KEY (`codigo`) REFERENCES `".$Mano_de_Obra."` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-6: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `".$Equipos."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-1: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$Equipos."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`codigo2` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`nombre` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`unidad` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`notas` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha` date NOT NULL DEFAULT '0000-00-00', ";
		$query .= "`imagen` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`digitado_por` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$query .= "`modificado_por` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$query .= "PRIMARY KEY (`id`), ";
		$query .= "KEY `".$Equipos."_codigo` (`codigo`) ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
		
		$query = "DROP TRIGGER IF EXISTS `update_apu_movs_A-U_".$Equipos."` ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-3: ".mysql_error().$query:"");
		
		$query = "CREATE TRIGGER `update_apu_movs_A-U_".$Equipos."` ";
		$query .= "AFTER UPDATE ON `".$Equipos."` ";
		$query .= "FOR EACH ROW ";
		$query .= "BEGIN";
		$query .= "	UPDATE `".$APU_Movs."` SET objeto_codigo = NEW.codigo, valor = NEW.valor, total = (((NEW.valor * cantidad) /100) * uso) WHERE tipo = 'Equipos' AND objeto_codigo = OLD.codigo; ";
		$query .= "END";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-4: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `".$Equipos_Prov."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-5: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$Equipos_Prov."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`proveedor_id` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`proveedor_codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "PRIMARY KEY (`id`), ";
		$query .= "KEY `".$Equipos."_codigo` (`codigo`), ";
		$query .= "CONSTRAINT `".$Equipos."_codigo` FOREIGN KEY (`codigo`) REFERENCES `".$Equipos."` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-6: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `".$Materiales."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-1: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$Materiales."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`codigo2` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`nombre` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`unidad` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`peso` decimal(20,3) NOT NULL DEFAULT '0.000', ";
		$query .= "`valor` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`valor_km` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`uso` decimal(20,2) NOT NULL DEFAULT '0.00', ";
		$query .= "`notas` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha` date NOT NULL DEFAULT '0000-00-00', ";
		$query .= "`imagen` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`digitado_por` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha_digitado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$query .= "`modificado_por` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`fecha_modificado` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', ";
		$query .= "PRIMARY KEY (`id`), ";
		$query .= "KEY `".$Materiales."_codigo` (`codigo`) ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-2: ".mysql_error():"");
		
		$query = "DROP TRIGGER IF EXISTS `update_apu_movs_A-U_".$Materiales."` ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-3: ".mysql_error().$query:"");
		
		$query = "CREATE TRIGGER `update_apu_movs_A-U_".$Materiales."` ";
		$query .= "AFTER UPDATE ON `".$Materiales."` ";
		$query .= "FOR EACH ROW ";
		$query .= "BEGIN";
		$query .= "	UPDATE `".$APU_Movs."` SET objeto_codigo = NEW.codigo, valor = NEW.valor, total = (((NEW.valor * cantidad) /100) * uso) WHERE tipo = 'Materiales' AND objeto_codigo = OLD.codigo; ";
		$query .= "END";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-4: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `".$Materiales_Prov."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-5: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$Materiales_Prov."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`proveedor_id` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`proveedor_codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "PRIMARY KEY (`id`), ";
		$query .= "KEY `".$Materiales."_codigo` (`codigo`), ";
		$query .= "CONSTRAINT `".$Materiales."_codigo` FOREIGN KEY (`codigo`) REFERENCES `".$Materiales."` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-6: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `".$Grupos."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-1: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$Grupos."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "PRIMARY KEY (`id`) ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-2: ".mysql_error():"");
		
		$query = "DROP TRIGGER IF EXISTS `update_groups_A-U_".$Clasificacion."` ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-3: ".mysql_error().$query:"");
		
		$query = "CREATE TRIGGER `update_groups_A-U_".$Clasificacion."` ";
		$query .= "AFTER UPDATE ON `".$Grupos."` ";
		$query .= "FOR EACH ROW ";
		$query .= "BEGIN";
		$query .= "	IF NEW.codigo != OLD.codigo THEN BEGIN ";
		$query .= "		IF NEW.categoria = 'APU' THEN ";
		$query .= "			UPDATE `".$APU."` SET codigo = CONCAT(NEW.codigo, '.', subgrupo, '.', codigo2), grupo = NEW.codigo WHERE grupo = OLD.codigo; ";
		$query .= "		END IF; ";
		$query .= "		IF NEW.categoria = 'Materiales' THEN ";
		$query .= "			UPDATE `".$Materiales."` SET codigo = CONCAT(NEW.codigo, '.', subgrupo, '.', codigo2), grupo = NEW.codigo WHERE grupo = OLD.codigo; ";
		$query .= "		END IF; ";
		$query .= "		IF NEW.categoria = 'Equipos' THEN ";
		$query .= "			UPDATE `".$Equipos."` SET codigo = CONCAT(NEW.codigo, '.', subgrupo, '.', codigo2), grupo = NEW.codigo WHERE grupo = OLD.codigo; ";
		$query .= "		END IF; ";
		$query .= "		IF NEW.categoria = 'Mano de Obra' THEN ";
		$query .= "			UPDATE `".$Mano_de_Obra."` SET codigo = CONCAT(NEW.codigo, '.', subgrupo, '.', codigo2), grupo = NEW.codigo WHERE grupo = OLD.codigo; ";
		$query .= "		END IF; ";
		$query .= "		UPDATE `".$SubGrupos."` SET grupo = NEW.codigo WHERE grupo = OLD.codigo AND categoria = OLD.categoria; ";
		$query .= "	END; END IF; ";
		$query .= "END";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-4: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `".$SubGrupos."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-1: ".mysql_error():"");
		
		$query = "CREATE TABLE `".$SubGrupos."` ( ";
		$query .= "`id` int(11) NOT NULL AUTO_INCREMENT, ";
		$query .= "`codigo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`subgrupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`grupo` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "`categoria` varchar(100) NOT NULL DEFAULT '', ";
		$query .= "PRIMARY KEY (`id`) ";
		$query .= ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1; ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-2: ".mysql_error():"");
		
		$query = "DROP TRIGGER IF EXISTS `update_subgroups_A-U_".$Clasificacion."` ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-3: ".mysql_error():"");
		
		$query = "CREATE TRIGGER `update_subgroups_A-U_".$Clasificacion."` ";
		$query .= "AFTER UPDATE ON `".$SubGrupos."` ";
		$query .= "FOR EACH ROW ";
		$query .= "BEGIN";
		$query .= "	IF NEW.codigo != OLD.codigo THEN BEGIN ";
		$query .= "		IF NEW.categoria = 'APU' THEN ";
		$query .= "			UPDATE `".$APU."` SET codigo = CONCAT(grupo, '.', NEW.codigo, '.', codigo2), subgrupo = NEW.codigo WHERE grupo = OLD.grupo AND subgrupo = OLD.codigo; ";
		$query .= "		END IF; ";
		$query .= "		IF NEW.categoria = 'Materiales' THEN ";
		$query .= "			UPDATE `".$Materiales."` SET codigo = CONCAT(grupo, '.', NEW.codigo, '.', codigo2), subgrupo = NEW.codigo WHERE grupo = OLD.grupo AND subgrupo = OLD.codigo; ";
		$query .= "		END IF; ";
		$query .= "		IF NEW.categoria = 'Equipos' THEN ";
		$query .= "			UPDATE `".$Equipos."` SET codigo = CONCAT(grupo, '.', NEW.codigo, '.', codigo2), subgrupo = NEW.codigo WHERE grupo = OLD.grupo AND subgrupo = OLD.codigo; ";
		$query .= "		END IF; ";
		$query .= "		IF NEW.categoria = 'Mano de Obra' THEN ";
		$query .= "			UPDATE `".$Mano_de_Obra."` SET codigo = CONCAT(grupo, '.', NEW.codigo, '.', codigo2), subgrupo = NEW.codigo WHERE grupo = OLD.grupo AND subgrupo = OLD.codigo; ";
		$query .= "		END IF; ";
		$query .= "	END; END IF; ";
		$query .= "END";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-4: ".mysql_error():"");
	}
	return 1;
}

function Presupuesto_CopiarClasificacion($From_Clasificacion, $New_Clasificacion, $Notas)
{
	global $DEBUG;
	
	if ($From_Clasificacion == "" || $New_Clasificacion == "")
		return -1;
	
	$From_Clasificacion = strtolower($From_Clasificacion);
	$New_Clasificacion = strtolower($New_Clasificacion);
	
	if (Presupuesto_CrearClasificacion($New_Clasificacion, $Notas) == 1)
	{
		// Par_Presupuesto_Grupo
		$query = "INSERT INTO `par_presupuesto_grupo_".$New_Clasificacion."` SELECT * FROM `par_presupuesto_grupo_".$From_Clasificacion."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
		// Par_Presupuesto_SubGrupo
		$query = "INSERT INTO `par_presupuesto_subgrupo_".$New_Clasificacion."` SELECT * FROM `par_presupuesto_subgrupo_".$From_Clasificacion."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		// APU_EQU_Originales
		$query = "INSERT INTO `apu_equ_".$New_Clasificacion."` SELECT * FROM `apu_equ_".$From_Clasificacion."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
		// APU_EQU_Originales_Prov
		$query = "INSERT INTO `apu_equ_".$New_Clasificacion."_prov` SELECT * FROM `apu_equ_".$From_Clasificacion."_prov`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
		// APU_MAT_Originales
		$query = "INSERT INTO `apu_mat_".$New_Clasificacion."` SELECT * FROM `apu_mat_".$From_Clasificacion."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-5: ".mysql_error():"");
		// APU_MAT_Originales_Prov
		$query = "INSERT INTO `apu_mat_".$New_Clasificacion."_prov` SELECT * FROM `apu_mat_".$From_Clasificacion."_prov`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-6: ".mysql_error():"");
		// APU_MO_Originales
		$query = "INSERT INTO `apu_mo_".$New_Clasificacion."` SELECT * FROM `apu_mo_".$From_Clasificacion."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-7: ".mysql_error():"");
		// APU_MO_Originales_Movs
		$query = "INSERT INTO `apu_mo_".$New_Clasificacion."_movs` SELECT * FROM `apu_mo_".$From_Clasificacion."_movs`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-8: ".mysql_error():"");
		// APU_Originales
		$query = "INSERT INTO `apu_".$New_Clasificacion."` SELECT * FROM `apu_".$From_Clasificacion."`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-9: ".mysql_error():"");
		// APU_Originales_Movs
		$query = "INSERT INTO `apu_".$New_Clasificacion."_movs` SELECT * FROM `apu_".$From_Clasificacion."_movs`";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-10: ".mysql_error():"");
	}
}

function AfectarInventario2($Codigo, $Cantidad, $Valor, $Interno, $Motivo, $Entrada)
{
	global $DEBUG;
	
	//--- INSERT INTO Inventario_Movs
	$Tmp = GetProduct_Info2($Codigo);
	$Existencia = $Tmp[0]['Existencia'];
	$Valor_Actual = $Tmp[0]['Valor'];
	
	switch ($Entrada)
	{
		case "Requerimientos_Solicitud":
			$query = "INSERT INTO requerimientos_inventario (codigo, cantidad, valor, viejo_valor, existencia, 
			interno, motivo, fecha, tipo) VALUES ('".$Codigo."', '".$Cantidad."', '".$Valor."', 
			'".$Valor_Actual."', '".$Existencia."', '".$Interno."', '".$Motivo."', NOW(), 'Salida')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-1: ".mysql_error():"");
			
			if (mysql_affected_rows() > 0)
			{
				$query = "UPDATE requerimientos_productos SET existencia = existencia - '".$Cantidad."' WHERE codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-2: ".mysql_error():"");
			}
		break;
		case "Requerimientos_Compra":
			$query = "INSERT INTO requerimientos_inventario (codigo, cantidad, valor, viejo_valor, existencia, 
			interno, motivo, fecha, tipo) VALUES ('".$Codigo."', '".$Cantidad."', '".$Valor."', 
			'".$Valor_Actual."', '".$Existencia."', '".$Interno."', '".$Motivo."', NOW(), 'Entrada')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-1: ".mysql_error():"");
			
			if (mysql_affected_rows() > 0)
			{
				$query = "UPDATE requerimientos_productos SET existencia = existencia + '".$Cantidad."' WHERE codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0-2: ".mysql_error():"");
			}
		break;
	}
}

// Requires php_rar.dll extension
function ExtractFile($File, $FileType, $TmpDir)
{
	global $DEBUG;
	$ExtractVal = false;
	
	$Path = pathinfo(realpath($File), PATHINFO_DIRNAME)."\\".$TmpDir;
	//echo "Archivo -> ".$File." Path -> ".$Path."<br />";
	switch($FileType)
	{
		case "zip":
			$zip = new ZipArchive;
			$res = $zip->open($File);
			if ($res === true)
			{
				$zip->extractTo($Path);
				$zip->close();
				$ExtractVal = true;
			}
			else
				$ExtractVal = false;
		break;
		
		case "rar":
			$rar_file = rar_open($File) or $ExtractVal = false;
			$entries = rar_list($rar_file);
			
			foreach ($entries as $entry)
				$entry->extract($Path);

			rar_close($rar_file);
			$ExtractVal = true;
		break;
		
		default:
			$ExtractVal = false;
		break;
	}
	
	return $ExtractVal;
}

// Requires php_dbase.dll extension
function Read_DBF($FileArray)
{
	global $DEBUG;
	for ($i = 0; $i < count($FileArray); $i++)
	{
		// Open dbase file
		$db = dbase_open($FileArray[$i]["FilePath"], 0) or die("Error! Could not open dbase database file '".$FileArray[$i]["FilePath"]."'.");
		// Get column info
		$column_info = dbase_get_header_info($db);
		// Get number of columns/fields
		$num_fields = dbase_numfields($db);
		// Get Records
		$num_records = dbase_numrecords($db);

		// Display information
		$Columns = array();

		foreach($column_info as $column)
		{
			//echo "Columna -> ".$column["name"]."<br />";
			$Columns[] = $column["name"];
		}

		for ($a = 1; $a <= $num_records; $a++)
		{
			$TmpArray = @dbase_get_record_with_names($db, $a);
			array_walk($TmpArray, "Trim_Array");// CLEAN WHITE SPACES
			$row[] = Array(
				"Data" => $TmpArray,
				//"Data" => @dbase_get_record_with_names($db, $a),
				"Type" => $FileArray[$i]["Type"],
			);
		}
	}
	if (isset($row))
		return $row;
	else
		return array();
}

// Requires PHPExcel lib
function Read_XLSX($FileArray)
{
	//---
}

function Trim_Array(&$Val) 
{ 
    $Val = trim($Val); 
}

if (isset($_GET['Terceros_Crear']))
{
	$Garantia = "";
	$Imagen = "";
	$Ok = 0;
	// static
	$Foto_Cliente = 0;
	$Clasificacion = 0;
	$Tipo = 0;
	$Grupo = 0;
	$Nombre = 0;
	$Direccion = 0;
	$Barrio = 0;
	$Ciudad = 0;
	$Departamento = 0;
	$Pais = 0;
	$Telefono = 0;
	$Fax = 0;
	$Email = 0;
	$Email2 = 0;
	$Tipo_Sociedad = 0;
	$Tipo_Doc = 0;
	$ClienteID = 0;
	$Telefono_CP = 0;
	$Contacto_P = 0;
	$Contacto_S = 0;
	$Telefono_CS = 0;
	$VendedorID = 0;
	$CobradorID = 0;
	$Terminos = 0;
	$Cupo_Credito = 0;
	$Cupo_Adicional = 0;
	$ListaP = 0;
	$Vijencia_Cupo_Credito = 0;
	$Vijencia_Cupo_Adicional = 0;
	$Fecha_Notas = 0;
	$Notas = 0;
	$Estado_Garantia = "Completo";
	
	$data = $_GET['Terceros_Crear'];
	$num = count($data);
	for ($i=0; $i<$num; $i++)
	{
		if (isset($data[$i]['Ok']))
		{
			if ($data[$i]['Ok'] == "false") {
				$Estado_Garantia = "Incompleto";
			}
		}
	}
	
	foreach($data as $item)
	{
		if (isset($item['Garantia'])) {
			$Garantia = $item['Garantia'];
			$Imagen = $item['Imagen'];
			$Ok = $item['Ok'];
		}
		
		if ($final == false)
		{
			$Foto_Cliente = $item['Foto_Cliente'];
			$Clasificacion = $item['Clasificacion'];
			$Tipo = $item['Tipo'];
			
			if (isset($item['Grupo']))
				$Grupo = $item['Grupo'];
			
			$Nombre = $item['Nombre'];
			$Direccion = $item['Direccion'];
			$Barrio = $item['Barrio'];
			$Ciudad = $item['Ciudad'];
			$Departamento = $item['Departamento'];
			$Pais = $item['Pais'];
			$Telefono = $item['Telefono'];
			$Fax = $item['Fax'];
			$Email = $item['Email'];
			$Email2 = $item['Email2'];
			$Tipo_Sociedad = $item['Tipo_Sociedad'];
			$Tipo_Doc = $item['Tipo_Doc'];
			$ClienteID = $item['ClienteID'];
			$Telefono_CP = $item['Telefono_CP'];
			$Contacto_P = $item['Contacto_P'];
			$Contacto_S = $item['Contacto_S'];
			$Telefono_CS = $item['Telefono_CS'];
			$VendedorID = $item['VendedorID'];
			$CobradorID = $item['CobradorID'];
			$Terminos = $item['Terminos'];
			$Cupo_Credito = $item['Cupo_Credito'];
			$Cupo_Adicional = $item['Cupo_Adicional'];
			$ListaP = $item['ListaP'];
			$Vigencia_Cupo_Credito = $item['Vigencia_Cupo_Credito'];
			$Vigencia_Cupo_Adicional = $item['Vigencia_Cupo_Adicional'];
			$Fecha_Notas = $item['Fecha_Notas'];
			$Notas = $item['Notas'];
		}
		
		if ($final == false)
		{
			if ($Foto_Cliente != "")
			{
				$FilePath = pathinfo($Foto_Cliente);
				$EXT = $FilePath['extension'];
				$NewImage = "terceros/".$ClienteID.".".$EXT."";
				if (!rename("../".$Foto_Cliente."","../images/".$NewImage.""))
				{
					$Data[0] = array (
						"MESSAGE" => "ERROR",
					);
					echo json_encode($Data);
					die();
				}
				$Foto_Cliente = $NewImage;
				/*
				//-- Garantias - Rename Images
				$FilePath = pathinfo($Foto_Cliente);
				$EXT = $FilePath['extension'];
				$NewImage = "terceros/".$ClienteID.".".$EXT."";
				rename("../images/".$Foto_Cliente."","../images/".$NewImage."");
				$Foto_Cliente = $NewImage;
				*/
			}
			
			//--------------------------------------------------------------------------------//
			//-------------------------------- INSERT COMMAND --------------------------------//
			//--------------------------------------------------------------------------------//
			//-- Cliente
			$query = "INSERT INTO clientes
			(nombre, tipo_sociedad, tipo_doc, cliente_id, foto, direccion, barrio, ciudad, departamento, pais, contacto_p, 
			telefono_cp, contacto_s, telefono_cs, telefono, fax, email, email2, notas, vigencia_notas, terminos, credito, 
			vigencia_credito, adicional, vigencia_adicional, credito_activo, garantia, estado_cuenta, lista_precio, 
			vendedor_codigo, cobrador_codigo, ultima_actualizacion, fecha_creacion, creado_por) VALUES ('".$Nombre."', 
			'".$Tipo_Sociedad."', '".$Tipo_Doc."', '".$ClienteID."', '".$Foto_Cliente."', '".$Direccion."', '".$Barrio."', 
			'".$Ciudad."', '".$Departamento."', '".$Pais."', '".$Contacto_P."', '".$Telefono_CP."', '".$Contacto_S."', '".$Telefono_CS."', 
			'".$Telefono."', '".$Fax."', '".$Email."', '".$Email2."', '".$Notas."', '".$Fecha_Notas."', '".$Terminos."', 
			'".$Cupo_Credito."', '".$Vigencia_Cupo_Credito."', '".$Cupo_Adicional."', '".$Vigencia_Cupo_Adicional."', 'true', 
			'".$Estado_Garantia."', 'Al Dia', '".$ListaP."', '".$VendedorID."', '".$CobradorID."', NOW(), NOW(), '".$_SESSION["UserCode"]."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			$final = true;
			//echo "Resultado de Clientes -> ".$result."\n";
			
			if ($Grupo == 0)
			{
				$query = "INSERT INTO cliente_grupo 
				(cliente_id, clasificacion, tipo) VALUES ('".$ClienteID."', '".$Clasificacion."', 
				'".$Tipo."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			}
			else
			{
				$num = count($Grupo);
				for ($i=0; $i<$num; $i++)
				{
					$query = "INSERT INTO cliente_grupo 
					(cliente_id, clasificacion, tipo, grupo) VALUES ('".$ClienteID."', '".$Clasificacion."', 
					'".$Tipo."', '".$Grupo[$i]['Grupo']."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				}
			}
		}
		
		if ($Imagen != "")
		{
			$FilePath = pathinfo($Imagen);
			$EXT = $FilePath['extension'];
			$NewImage = "terceros/".$ClienteID."_".$Garantia.".".$EXT."";
			if (!rename("../".$Imagen."","../images/".$NewImage.""))
			{
				$Data[0] = array (
					"MESSAGE" => "ERROR",
				);
				echo json_encode($Data);
				die();
			}
			$Imagen = $NewImage;
			
			/*	
			//-- Garantias - Rename Images
			$FilePath = pathinfo($Imagen);
			$EXT = $FilePath['extension'];
			$NewImage = "terceros/".$ClienteID."_".$Garantia.".".$EXT."";
			rename("../images/".$Imagen."","../images/".$NewImage."");
			$Imagen = $NewImage;
			*/
		}

		if ($Garantia != "")
		{
			//-- Garantias - Insert
			$query = "INSERT INTO clientes_garant
			(cliente_id, garantia, ok, image) VALUES ('".$ClienteID."', '".$Garantia."', '".$Ok."', '".$Imagen."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			//echo "Resultado de Garantias -> ".$result."\n";
		}
		
	}
	$Data[0] = array (
		"MESSAGE" => "OK",
	);
	echo json_encode($Data);
}
else if (isset($_GET['Terceros_Modificar'])) // Cambiar el metodo de revision de foto cliente para borrar en caso de que no se coloque nada.
{
	$Garantia = "";
	$Imagen = "";
	$Ok = 0;
	// static
	$Foto_Cliente = 0;
	$Clasificacion = 0;
	$Tipo = 0;
	$Grupo = 0;
	$Nombre = 0;
	$Direccion = 0;
	$Barrio = 0;
	$Ciudad = 0;
	$Departamento = 0;
	$Pais = 0;
	$Telefono = 0;
	$Fax = 0;
	$Email = 0;
	$Email2 = 0;
	$Tipo_Sociedad = 0;
	$Tipo_Doc = 0;
	$ClienteID = 0;
	$Old_ClienteID = 0;
	$Telefono_CP = 0;
	$Contacto_P = 0;
	$Contacto_S = 0;
	$Telefono_CS = 0;
	$VendedorID = 0;
	$CobradorID = 0;
	$Terminos = 0;
	$Cupo_Credito = 0;
	$Cupo_Adicional = 0;
	$ListaP = 0;
	$Vijencia_Cupo_Credito = 0;
	$Vijencia_Cupo_Adicional = 0;
	$Cupo_Activo = false;
	$Cupo_Asignado_Por = "";
	$Estado_Cuenta = 0;
	$Fecha_Notas = 0;
	$Notas = 0;
	$Estado_Garantia = "Completo";
	
	$data = $_GET['Terceros_Modificar'];
	$num = count($data);
	for ($i=0; $i<$num; $i++)
	{
		if (isset($data[$i]['Ok']))
		{
			if ($data[$i]['Ok'] == "false") {
				$Estado_Garantia = "Incompleto";
			}
		}
	}
	
	foreach($data as $item)
	{
		if (isset($item['Garantia']))
		{
			$Garantia = $item['Garantia'];
			$Imagen = $item['Imagen'];
			$Ok = $item['Ok'];
		}
		
		if ($final == false)
		{
			$Foto_Cliente = $item['Foto_Cliente'];
			$Clasificacion = $item['Clasificacion'];
			$Tipo = $item['Tipo'];
			
			if (isset($item['Grupo']))
				$Grupo = $item['Grupo'];
			
			$Nombre = $item['Nombre'];
			$Direccion = $item['Direccion'];
			$Barrio = $item['Barrio'];
			$Ciudad = $item['Ciudad'];
			$Departamento = $item['Departamento'];
			$Pais = $item['Pais'];
			$Telefono = $item['Telefono'];
			$Fax = $item['Fax'];
			$Email = $item['Email'];
			$Email2 = $item['Email2'];
			$Tipo_Sociedad = $item['Tipo_Sociedad'];
			$Tipo_Doc = $item['Tipo_Doc'];
			$ClienteID = $item['ClienteID'];
			$Old_ClienteID = $item['Old_ClienteID'];
			$Telefono_CP = $item['Telefono_CP'];
			$Contacto_P = $item['Contacto_P'];
			$Contacto_S = $item['Contacto_S'];
			$Telefono_CS = $item['Telefono_CS'];
			$VendedorID = $item['VendedorID'];
			$CobradorID = $item['CobradorID'];
			$Terminos = $item['Terminos'];
			$Cupo_Credito = $item['Cupo_Credito'];
			$Cupo_Adicional = $item['Cupo_Adicional'];
			$ListaP = $item['ListaP'];
			$Vigencia_Cupo_Credito = $item['Vigencia_Cupo_Credito'];
			$Vigencia_Cupo_Adicional = $item['Vigencia_Cupo_Adicional'];
			$Cupo_Activo = $item['Cupo_Activo'];
			$Cupo_Asignado_Por = $item['Cupo_Asignado_Por'];
			$Estado_Cuenta = $item['Estado_Cuenta'];
			$Fecha_Notas = $item['Fecha_Notas'];
			$Notas = $item['Notas'];
		}
		
		if ($final == false)
		{
			//-- Garantias - Del
			$query = "SELECT * FROM clientes_garant WHERE cliente_id = '".$Old_ClienteID."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				while($row = mysql_fetch_array($result))
				{
					if ($row["image"] != "")
					{
						$TargetFile = "../images/".$row["image"];
						if ($WINDOWS)
						{
							if (chmod($TargetFile,0777))
							{
								if (!unlink($TargetFile))
									die();
							}
						}
						else
						{
							//Linux
							if (!unlink($TargetFile))
								die();
						}
					}
				}
				
				$query = "DELETE FROM clientes_garant WHERE cliente_id = '".$Old_ClienteID."' ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			}
			
			if ($Foto_Cliente != "")
			{
				$query = "SELECT foto FROM clientes WHERE cliente_id = '".$Old_ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				$row = mysql_fetch_array($result);
				if ($Foto_Cliente != $row["foto"])
				{
					if ($row["foto"] != "")
					{
						$TargetFile = "../images/".$row["foto"];
						if ($WINDOWS)
						{
							if (chmod($TargetFile,0777))
							{
								if (!unlink($TargetFile))
									die();
							}
						}
						else
						{
							//Linux
							if (!unlink($TargetFile))
								die();
						}
					}
					
					//-- Rename Image
					$FilePath = pathinfo($Foto_Cliente);
					$EXT = $FilePath['extension'];
					$NewImage = "terceros/".$Old_ClienteID.".".$EXT."";
					if (!rename("../".$Foto_Cliente."","../images/".$NewImage.""))
					{
						$Data[0] = array (
							"MESSAGE" => "ERROR",
						);
						echo json_encode($Data);
						die();
					}
					$Foto_Cliente = $NewImage;
				}
				/*
				//-- Garantias - Rename Images
				$FilePath = pathinfo($Foto_Cliente);
				$EXT = $FilePath['extension'];
				$NewImage = "terceros/".$Old_ClienteID.".".$EXT."";
				rename("../images/".$Foto_Cliente."","../images/".$NewImage."");
				$Foto_Cliente = $NewImage;
				*/
			}
			
			//--------------------------------------------------------------------------------//
			//-------------------------------- INSERT COMMAND --------------------------------//
			//--------------------------------------------------------------------------------//
			//-- Cliente
			$query = "UPDATE clientes SET 
			nombre = '".$Nombre."', ";
			//$query .= "tipo_sociedad = '".$Tipo_Sociedad."', 
			//$query .= "tipo_doc = '".$Tipo_Doc."', ";
			//$query .= ($ClienteID != $Old_ClienteID) ? "cliente_id = '".$ClienteID."', ":"";
			$query .= "
			foto = '".$Foto_Cliente."', 
			direccion = '".$Direccion."', 
			barrio = '".$Barrio."', 
			ciudad = '".$Ciudad."', 
			departamento = '".$Departamento."', 
			pais = '".$Pais."', 
			contacto_p = '".$Contacto_P."', 
			telefono_cp = '".$Telefono_CP."', 
			contacto_s = '".$Contacto_S."', 
			telefono_cs = '".$Telefono_CS."', 
			telefono = '".$Telefono."', 
			fax = '".$Fax."', 
			email = '".$Email."', 
			email2 = '".$Email2."', 
			notas = '".$Notas."', 
			vigencia_notas = '".$Fecha_Notas."', 
			terminos = '".$Terminos."', 
			credito = '".$Cupo_Credito."', 
			vigencia_credito = '".$Vigencia_Cupo_Credito."', 
			adicional = '".$Cupo_Adicional."', 
			vigencia_adicional = '".$Vigencia_Cupo_Adicional."', 
			credito_activo = '".$Cupo_Activo."', ";
			$query .= ($Cupo_Activo == "true" && $Cupo_Asignado_Por == "") ? "cupo_asignado_por = '".$_SESSION["UserCode"]."', ":"";
			$query .= "
			garantia = '".$Estado_Garantia."', 
			estado_cuenta = '".$Estado_Cuenta."', 
			lista_precio = '".$ListaP."', 
			vendedor_codigo = '".$VendedorID."', 
			cobrador_codigo = '".$CobradorID."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			ultima_actualizacion = NOW() ";
			//$query .= ($ClienteID != $Old_ClienteID) ? "WHERE cliente_id = '".$Old_ClienteID."'":"WHERE cliente_id = '".$ClienteID."'";
			$query .= "WHERE cliente_id = '".$Old_ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			$final = true;
			//echo "Resultado de Clientes -> ".$result."\n";
			
			if ($Grupo == 0)
			{
				$query = "UPDATE cliente_grupo SET 
				clasificacion = '".$Clasificacion."', 
				tipo = '".$Tipo."' WHERE cliente_id = '".$Old_ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			}
			else
			{
				$num = count($Grupo);
				for ($i=0; $i<$num; $i++)
				{
					$query = "UPDATE cliente_grupo SET 
					clasificacion = '".$Clasificacion."', 
					tipo = '".$Tipo."', 
					grupo = '".$Grupo[$i]['Grupo']."' WHERE cliente_id = '".$Old_ClienteID."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				}
			}
		}
		
		if ($Imagen != "")
		{
			//-- Rename Image
			$FilePath = pathinfo($Imagen);
			$EXT = $FilePath['extension'];
			$NewImage = "terceros/".$Old_ClienteID."_".$Garantia.".".$EXT."";
			if (!rename("../".$Imagen."","../images/".$NewImage.""))
			{
				$Data[0] = array (
					"MESSAGE" => "ERROR",
				);
				echo json_encode($Data);
				die();
			}
			$Imagen = $NewImage;
			/*
			//-- Garantias - Rename Images
			$FilePath = pathinfo($Imagen);
			$EXT = $FilePath['extension'];
			$NewImage = "terceros/".$Old_ClienteID."_".$Garantia.".".$EXT."";
			rename("../images/".$Imagen."","../images/".$NewImage."");
			$Imagen = $NewImage;
			*/
		}
		
		if ($Garantia != "")
		{
			//-- Garantias - Insert
			$query = "INSERT INTO clientes_garant
			(cliente_id, garantia, ok, image) VALUES ('".$Old_ClienteID."', '".$Garantia."', '".$Ok."', '".$Imagen."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			
			
			/*$query = "UPDATE clientes_garant SET
			garantia = '".$Garantia."', 
			ok = '".$Ok."', 
			image = '".$Imagen."' WHERE cliente_id = '".$Old_ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");*/
		}
	}
	$Data[0] = array (
		"MESSAGE" => "OK",
	);
	echo json_encode($Data);
}
else if (isset($_GET['Terceros_Modificar_Borrar']))
{
	$query = "SELECT foto FROM clientes WHERE cliente_id = '".$_GET['Terceros_Modificar_Borrar']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		if ($row["foto"] != "")
		{
			$TargetFile = "../images/".$row["foto"];
			if ($WINDOWS)
			{
				if (chmod($TargetFile,0777))
				{
					if (!unlink($TargetFile))
						die();
				}
			}
			else
			{
				//Linux
				if (!unlink($TargetFile))
					die();
			}
		}
		
		$query = "SELECT image FROM clientes_garant WHERE cliente_id = '".$_GET['Terceros_Modificar_Borrar']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				if ($row["image"] != "")
				{
					unlink("../images/".$row["image"]."");
				}
			}
		}
		
		$query = "DELETE FROM clientes WHERE cliente_id = '".$_GET['Terceros_Modificar_Borrar']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if ($result)
			echo "OK";
	}
}
else if (isset($_GET['Terceros_Listado']))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$Nombre = isset($_GET["Nombre"]) ? $_GET["Nombre"]:"";
	$TipoDoc = isset($_GET["TipoDoc"]) ? $_GET["TipoDoc"]:"";
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$Direccion = isset($_GET["Direccion"]) ? $_GET["Direccion"]:"";
	$Barrio = isset($_GET["Barrio"]) ? $_GET["Barrio"]:"";
	$Ciudad = isset($_GET["Ciudad"]) ? $_GET["Ciudad"]:"";
	$Departamento = isset($_GET["Departamento"]) ? $_GET["Departamento"]:"";
	$ContactoP = isset($_GET["ContactoCP"]) ? $_GET["ContactoCP"]:"";
	$TelefonoCP = isset($_GET["TelefonoCP"]) ? $_GET["TelefonoCP"]:"";
	$Telefono = isset($_GET["Telefono"]) ? $_GET["Telefono"]:"";
	$Terminos = isset($_GET["Terminos"]) ? $_GET["Terminos"]:"";
	$CupoCR = isset($_GET["CupoCR"]) ? $_GET["CupoCR"]:0;
	$CupoActivo = isset($_GET["CupoActivo"]) ? $_GET["CupoActivo"]:false;
	$Vigencia = isset($_GET["Vigencia"]) ? $_GET["Vigencia"]:"";
	$ListaP = isset($_GET["ListaP"]) ? $_GET["ListaP"]:0;
	$VendedorID = isset($_GET["VendedorID"]) ? $_GET["VendedorID"]:"";
	$CobradorID = isset($_GET["CobradorID"]) ? $_GET["CobradorID"]:"";
	$Activo = isset($_GET["Activo"]) ? $_GET["Activo"]:false;
	$Motivo = isset($_GET["Motivo"]) ? $_GET["Motivo"]:"";
	
	if ($TipoDoc == "Persona Natural")
	{
		$Doc = "Cédula";
	}
	else if ($TipoDoc == "Persona Jurídica")
	{
		$Doc = "NIT";
	}
	else
	{
		$Doc = $TipoDoc;
	}
	
	// Get ID
	$query = "SELECT cliente_id FROM clientes WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		// Check if Changed
		if ($row["cliente_id"] != $ClienteID)
		{
			// Check if Exist
			$query = "SELECT cliente_id FROM clientes WHERE cliente_id = '".$ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$ReturnData[0] = array(
					"MESSAGE" => "EXIST",
				);
				echo json_encode($ReturnData);
				die();
			}
			
			// Change...
			// 1º Login System
			$query = "UPDATE login SET user_id = '".$ClienteID."' WHERE user_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
			
			$query = "UPDATE login_access SET user_id = '".$ClienteID."' WHERE user_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
			
			// 2º Bancos
			$query = "UPDATE bancos SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-5: ".mysql_error():"");
			
			// 3º Caja
			$query = "UPDATE caja_final SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-6: ".mysql_error():"");
			
			// 4º Cheques
			$query = "UPDATE cheques SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-7: ".mysql_error():"");
			
			// 5º Grupos
			$query = "UPDATE cliente_grupo SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-8: ".mysql_error():"");
			
			// 6º Garantias
			$query = "UPDATE clientes_garant SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-9: ".mysql_error():"");
			
			// 7º Compras
			$query = "UPDATE compras_final SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-10: ".mysql_error():"");
			
			// 8º Compras Mov
			$query = "UPDATE compras_movs SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-11: ".mysql_error():"");
			
			// 9º Contratos
			$query = "UPDATE contratos SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-12: ".mysql_error():"");
			
			// 10º CxP Movs
			$query = "UPDATE cxp_movs SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-13: ".mysql_error():"");
			
			// 11º Ventas
			$query = "UPDATE fact_final SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-14: ".mysql_error():"");
			
			$query = "UPDATE fact_final SET chofer = '".$ClienteID."' WHERE chofer = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-15: ".mysql_error():"");
			
			// 12º Ventas Mov
			$query = "UPDATE fact_movs SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-16: ".mysql_error():"");
			
			// 13º Maquinaria
			$query = "UPDATE maquinaria_final SET proveedor1 = '".$ClienteID."' WHERE proveedor1 = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-17: ".mysql_error():"");
			
			$query = "UPDATE maquinaria_final SET proveedor2 = '".$ClienteID."' WHERE proveedor2 = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-18: ".mysql_error():"");
			
			$query = "UPDATE maquinaria_final SET proveedor3 = '".$ClienteID."' WHERE proveedor3 = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-19: ".mysql_error():"");
			
			// 14º Maquinaria Movs
			$query = "UPDATE maquinaria_movs SET proveedor = '".$ClienteID."' WHERE proveedor = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-20: ".mysql_error():"");
			
			// 15º Clientes Movs
			$query = "UPDATE mov_clientes SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-21: ".mysql_error():"");
			
			// 16º Nomina -> Extras
			$query = "UPDATE nom_extras SET empleado_id = '".$ClienteID."' WHERE empleado_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-22: ".mysql_error():"");
			
			$query = "UPDATE nom_extras SET autorizador_id = '".$ClienteID."' WHERE autorizador_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-23: ".mysql_error():"");
			
			// 17º Nomina -> Extras Movs
			$query = "UPDATE nom_extras_movs SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-24: ".mysql_error():"");
			
			// 18º Nomina -> Novedades
			$query = "UPDATE nom_novedades SET empleado_id = '".$ClienteID."' WHERE empleado_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-25: ".mysql_error():"");
			
			$query = "UPDATE nom_novedades SET reemplazo_id = '".$ClienteID."' WHERE reemplazo_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-26: ".mysql_error():"");
			
			$query = "UPDATE nom_novedades SET autorizador_id = '".$ClienteID."' WHERE autorizador_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-27: ".mysql_error():"");
			
			// 19º Nomina -> Prestamos
			$query = "UPDATE nom_prestamos SET beneficiario_id = '".$ClienteID."' WHERE beneficiario_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-28: ".mysql_error():"");
			
			$query = "UPDATE nom_prestamos SET acreedor_id = '".$ClienteID."' WHERE acreedor_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-29: ".mysql_error():"");
			
			// 20º Nomina -> Nomina
			$query = "UPDATE nomina SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-30: ".mysql_error():"");
			
			// 21º Produccion
			$query = "UPDATE produccion_final SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-31: ".mysql_error():"");
			
			// 22º Produccion Movs
			$query = "UPDATE produccion_movs SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-32: ".mysql_error():"");
			
			// 23º Produccion Process Movs
			$query = "UPDATE produccion_proc_movs SET cliente_id = '".$ClienteID."' WHERE cliente_id = '".$row["cliente_id"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-33: ".mysql_error():"");
			
			// 24º Terceros
			$query = "UPDATE clientes SET cliente_id = '".$ClienteID."' WHERE id = '".$ID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-34: ".mysql_error():"");
		}
	}
	
	$query = "UPDATE clientes SET 
	nombre = '".$Nombre."', 
	tipo_sociedad = '".$TipoDoc."', 
	tipo_doc = '".$Doc."', 
	direccion = '".$Direccion."', 
	barrio = '".$Barrio."', 
	ciudad = '".$Ciudad."', 
	departamento = '".$Departamento."', 
	contacto_p = '".$ContactoP."', 
	telefono_cp = '".$TelefonoCP."', 
	telefono = '".$Telefono."', 
	terminos = '".$Terminos."', 
	credito = '".$CupoCR."', 
	vigencia_credito = '".$Vigencia."', 
	credito_activo = '".$CupoActivo."', 
	lista_precio = '".$ListaP."', 
	vendedor_codigo = '".$VendedorID."', 
	cobrador_codigo = '".$CobradorID."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	ultima_actualizacion = NOW(), 
	activo = '".$Activo."', 
	motivo = '".$Motivo."' 
	WHERE id = '".$ID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_POST['Ventas_Crear']))
{
	//Parte 1
	$Duplicar = false;
	$ClienteID = 0;
	$Fecha = 0;
	$Remision = 0;
	$Ord_Compra = 0;
	$Ord_Produccion = "";
	$Factura = 0;
	$Interno = 0;
	$BeneficiarioID = 0;
	//Parte 2
	$Codigo = 0;
	$Cantidad = 0;
	$Dcto = 0;
	$Unitario = 0;
	$Produccion = 0;
	// Parte 3
	$Observaciones = 0;
	$Subtotal2 = 0;
	$TipoServicio = "";
	$TipoServicioValor = 0;
	$Iva = 0;
	$TipoDcto = "";
	$TipoDctoValor = 0;
	$Total = 0;
	$Chofer = 0;
	$Placa = 0;
	$Peso = 0;
	$FormaPago = 0;
	$Ruta = 0;
	$Caja_Interno = "";
	$Caja_Recibo = 0;
	$Direccion = 0;
	$ValorRC = 0;
	$VendedorID = 0;
	$CobradorID = 0;
	$CajeroID = 0;
	$Saldo = 0;
	$Estado = 0;
	$Tipo_Pedido = 0;
	
	$data = $_POST['Ventas_Crear'];
	
	/*print_r($data);
	echo "<br/>";
	if( $data[0]["Produccion"] == "false")
	echo "asdas - ";
	$Duplicar = $data[0]["Produccion"];
	echo $Duplicar;
	die();*/
	
	foreach($data as $item)
	{
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		$Dcto = $item['Dcto'];
		$Unitario = $item['Unitario'];
		$Produccion = $item['Produccion'];
		
		if ($final == false)
		{
			$Duplicar = $item['Duplicar'];
			$ClienteID = $item['ClienteID'];
			$Fecha = $item['Fecha'];
			$Remision = $item['Remision'];
			$Ord_Compra = $item['Ord_Compra'];
			$Factura = $item['Factura'];
			$BeneficiarioID = $item['BeneficiarioID'];
			$Observaciones = $item['Observaciones'];
			$Subtotal2 = $item['Subtotal2'];
			$TipoServicio = $item['TipoServicio'];
			$TipoServicioValor = $item['TipoServicioValor'];
			$Iva = $item['Iva'];
			$TipoDcto = $item['TipoDcto'];
			$TipoDctoValor = $item['TipoDctoValor'];
			$Total = $item['Total'];
			$Chofer = $item['Chofer'];
			$Placa = $item['Placa'];
			$Peso = $item['Peso'];
			$FormaPago = $item['FormaPago'];
			$Ruta = $item['Ruta'];
			//$Peso2 = $item['Peso2'];
			$Caja_Interno = $item['Caja_Interno'];
			$Caja_Recibo = $item['Caja_Recibo'];
			$Direccion = $item['Direccion'];
			$ValorRC = $item['ValorRC'];
			$VendedorID = $item['VendedorID'];
			$CobradorID = $item['CobradorID'];
			$CajeroID = $item['CajeroID'];
			$Saldo = $item['Saldo'];
			$Estado = $item['Estado'];
			$Tipo_Pedido = $item['Tipo_Pedido'];
			
			$query = "SELECT id FROM fact_final ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 8){
				$num = 8 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Interno = "FER".$zero."".$id."";
			}
			else {
				$Interno = "FER".$id."";
			}
			
			if ($Remision == "" || $Duplicar == "true") {
				$Remision = $Interno;
			}
			
			if ($Factura == "" || $Duplicar == "true") {
				$Factura = $Interno;
			}
			
			if ($Ord_Compra == "" || $Duplicar == "true") {
				$Ord_Compra = $Interno;
			}
			
			if ($Tipo_Pedido == "Produccion" && $Estado != "Cotizacion")
			{
				$query = "SELECT id FROM produccion_final ORDER BY id DESC LIMIT 1";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				
				$row = mysql_fetch_array($result);
				$id = $row['id'] + 1;
				$len = strlen($id);
				$zero = "";
				if ($len < 6){
					$num = 6 - $len;
					for ($a = 0; $a < $num; $a++) {
						$zero .= "0";
					}
					$Ord_Produccion = "OP".$zero."".$id."";
				}
				else {
					$Ord_Produccion = "OP".$id."";
				}
				
				$query = "INSERT INTO produccion_final 
				(orden_produccion, orden_compra, interno, destino, cliente_id, fecha, estado, digitado_por, fecha_digitado) VALUES 
				('".$Ord_Produccion."', '".$Ord_Compra."', '".$Interno."', 'Cliente', '".$ClienteID."', '".$Fecha."', 'Pendiente', 
				'".$_SESSION["UserCode"]."', NOW())";
				$result = mysql_query($query) or die ("SQL Error 2: " . mysql_error());
			}
		}
		
		//--------------------------------------------------------------------------------//
		//-------------------------------- INSERT COMMAND --------------------------------//
		//--------------------------------------------------------------------------------//
		//-- Fact Movs
		$query = "INSERT INTO fact_movs
		(`codigo`, `cantidad`, `desc`, `precio`, `interno`, `orden_compra`, `factura`, `remision`, `cliente_id`) VALUES
		('".$Codigo."', '".$Cantidad."', '".$Dcto."', '".$Unitario."', '".$Interno."', '".$Ord_Compra."', '".$Factura."', 
		'".$Remision."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		//echo "Resultado de Fac_Movs -> ".$result."\n";
		
		if ($Tipo_Pedido == "Produccion" && $Estado != "Cotizacion")
		{
			if ($Produccion == "true")
			{
				$query = "INSERT INTO produccion_movs 
				(codigo, cantidad, tipo, orden_produccion, orden_compra, interno, cliente_id) VALUES 
				('".$Codigo."', '".$Cantidad."', 'Obtener', '".$Ord_Produccion."', '".$Ord_Compra."', '".$Interno."', '".$ClienteID."')";
				$result = mysql_query($query) or die ("SQL Error 4: " . mysql_error());
			}
		}
		
		//-- Fact Final
		if ($final == false)
		{
			$query = "INSERT INTO fact_final 
			(`interno`, `remision`, `fecha_remision`, `factura`, `orden_compra`, `orden_produccion`, `cliente_id`, `ruta`, 
			`direccion_entrega`, `forma_pago`, `caja_interno`, `caja_recibo`, `vendedor_codigo`, `cobrador_codigo`, `chofer`, `placa`, 
			`tipo_servicio`, `tipo_servicio_valor`, `tipo_descuento`, `tipo_descuento_valor`, `observaciones`, `sub_total`, 
			`total`, `iva`, `estado`, `peso`, `digitado_por`, `fecha_digitado`, `tipo_pedido`, `beneficiario_id`) VALUES 
			('".$Interno."', '".$Remision."', '".$Fecha."', '".$Factura."', '".$Ord_Compra."', '".$Ord_Produccion."', 
			'".$ClienteID."', '".$Ruta."', '".$Direccion."', '".$FormaPago."', '".$Caja_Interno."', '".$Caja_Recibo."', '".$VendedorID."', 
			'".$CobradorID."', '".$Chofer."', '".$Placa."', '".$TipoServicio."', '".$TipoServicioValor."', '".$TipoDcto."', 
			'".$TipoDctoValor."', '".$Observaciones."', '".$Subtotal2."', '".$Total."', '".$Iva."', '".$Estado."', 
			'".$Peso."', '".$_SESSION["UserCode"]."', NOW(), '".$Tipo_Pedido."', '".$BeneficiarioID."')";
			$final = true;
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			//echo "Resultado de Fac_Final -> ".$result."\n";
			
			if ($FormaPago == "Efectivo" && $ValorRC > 0)
			{
				//-- Mov_Clientes - INSERT 1
				$query = "INSERT INTO mov_clientes 
				(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
				digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, vendedor_codigo, cobrador_codigo) VALUES 
				('".$ClienteID."', 'Debito', '".$Total."', '0', '".$Interno."', '".$Ord_Compra."', '".$Remision."', 
				'".$Factura."', NOW(), 'Aprobado', '".$_SESSION["UserCode"]."', NOW(), '".$_SESSION["UserCode"]."', 
				NOW(), '".$VendedorID."', '".$CobradorID."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
				
				//-- Caja_Final - UPDATE
				$query = "UPDATE caja_final SET saldo = saldo - '".$Total."' WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
				
				//--- Mov_Clientes - INSERT 2
				$query = "INSERT INTO mov_clientes 
				(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
				digitado_por, fecha_digitado, aprobado_por, vendedor_codigo, cobrador_codigo, caja_interno, caja_recibo) VALUES 
				('".$ClienteID."', 'Credito', '".$Total."', '".$ValorRC."', '".$Interno."', '".$Ord_Compra."', '".$Remision."', 
				'".$Factura."', NOW(), 'Aprobado', '".$_SESSION["UserCode"]."', NOW(), '".$_SESSION["UserCode"]."', '".$VendedorID."', 
				'".$CobradorID."', '".$Caja_Interno."', '".$Caja_Recibo."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
			}
			
			if ($CajeroID != "")
			{
				$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				
				$row = mysql_fetch_array($result);
				$id = $row['id'] + 1;
				$len = strlen($id);
				$zero = "";
				if ($len < 7){
					$num = 7 - $len;
					for ($a = 0; $a < $num; $a++) {
						$zero .= "0";
					}
					$Caja_Interno = "MCJ".$zero."".$id."";
				}
				else {
					$Caja_Interno = "MCJ".$id."";
				}
				$Caja_Recibo = $Caja_Interno;
				
				$query = "INSERT INTO caja_final (`fecha`, `caja_interno`, `caja_recibo`, `categoria`, `grupo`, `subgrupo`, 
				`subgrupo2`, `cliente_id`, `observaciones`, `efectivo`, `total`, `saldo`, `digitado_por`, `fecha_digitado`, 
				`estado`) VALUES ('".$Fecha."', '".$Caja_Interno."', '".$Caja_Recibo."', 'Ingresos', 'Cliente', 'Pago de contado', 
				'N/A', '".$ClienteID."', 'Generado automáticamente por pedido # ".$Interno."', '".$Total."', '".$Total."', '".$Total."', 
				'".$CajeroID."', NOW(), 'Pendiente')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
				
				$query = "UPDATE fact_final SET notas = 'RC Generado: ".$Caja_Interno."' WHERE interno = '".$Interno."' ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9: ".mysql_error():"");
			}
		}
	}
	
	$Data[] = array (
		'Ord_Produccion' => $Ord_Produccion,
		'Interno' => $Interno,
		'Caja_Interno' => $Caja_Interno,
	);
	echo json_encode($Data);
	//echo $Interno;
}
else if (isset($_POST['Ventas_Modificar']))
{
	//Parte 1
	$ClienteID = 0;
	$Fecha = 0;
	$Remision = 0;
	$Ord_Compra = 0;
	$Ord_Produccion = 0;
	$Factura = 0;
	$Interno = 0;
	$BeneficiarioID = 0;
	//Parte 2
	$Codigo = 0;
	$Cantidad = 0;
	$Dcto = 0;
	$Unitario = 0;
	$Produccion = 0;
	// Parte 3
	$Observaciones = 0;
	$Subtotal2 = 0;
	$TipoServicio = 0;
	$TipoServicioValor = 0;
	$Iva = 0;
	$TipoDcto = "";
	$TipoDctoValor = 0;
	$Total = 0;
	$Chofer = 0;
	$Placa = 0;
	$Peso = 0;
	$FormaPago = 0;
	$Ruta = 0;
	$Caja_Interno = 0;
	$Caja_Recibo = 0;
	$Direccion = 0;
	$ValorRC = 0;
	$VendedorID = 0;
	$CobradorID = 0;
	$Saldo = 0;
	$Tipo_Pedido = 0;
	$DigitadorID = "";
	
	$data = $_POST['Ventas_Modificar'];
	
	//-------- CHECK CHANGES
	$query = "SELECT * FROM fact_final WHERE interno = '".$data[0]['Interno']."' AND estado = 'Creado' 
	OR interno = '".$data[0]['Interno']."' AND estado = 'Cotizacion'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	else
	{
		$row = mysql_fetch_array($result);
		$DigitadorID = $row["digitado_por"];
	}
	
	//-------- CHECK IF SOME ITEM HAS BEEN DELETED
	$delete = true;
	$query = "SELECT codigo FROM fact_movs WHERE interno = '".$data[0]['Interno']."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$numrow = mysql_num_rows($result);
	if ($numrow > 0)
	{
		while ($rows = mysql_fetch_array($result))
		{
			$num = count($data);
			for ($i=0; $i<$num; $i++)
			{
				if ($rows['codigo'] == $data[$i]['CodFab'])
				{
					$delete = false;
				}
			}
			if ($delete == true)
			{
				$query2 = "DELETE FROM fact_movs WHERE codigo = '".$rows['codigo']."' AND interno = '".$data[0]['Interno']."' ";
				$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	foreach($data as $item)
	{
		//------------------ UPDATE/INSERT COMMAND
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		$Dcto = $item['Dcto'];
		$Unitario = $item['Unitario'];
		$Produccion = $item['Produccion'];
		
		if ($final == false)
		{
			$ClienteID = $item['ClienteID'];
			$Fecha = $item['Fecha'];
			$Remision = $item['Remision'];
			$Ord_Compra = $item['Ord_Compra'];
			$Ord_Produccion = $item['Ord_Produccion'];
			$Factura = $item['Factura'];
			$Interno = $item['Interno'];
			$BeneficiarioID = $item['BeneficiarioID'];
			$Observaciones = $item['Observaciones'];
			$Subtotal2 = $item['Subtotal2'];
			$TipoServicio = $item['TipoServicio'];
			$TipoServicioValor = $item['TipoServicioValor'];
			$Iva = $item['Iva'];
			$TipoDcto = $item['TipoDcto'];
			$TipoDctoValor = $item['TipoDctoValor'];
			$Total = $item['Total'];
			$Chofer = $item['Chofer'];
			$Placa = $item['Placa'];
			$Peso = $item['Peso'];
			$FormaPago = $item['FormaPago'];
			$Ruta = $item['Ruta'];
			//$Peso2 = $item['Peso2'];
			$Caja_Interno = $item['Caja_Interno'];
			$Caja_Recibo = $item['Caja_Recibo'];
			$Direccion = $item['Direccion'];
			$ValorRC = $item['ValorRC'];
			$VendedorID = $item['VendedorID'];
			$CobradorID = $item['CobradorID'];
			$Saldo = $item['Saldo'];
			$Tipo_Pedido = $item['Tipo_Pedido'];
			
			if ($Remision == "") {
				$Remision = $Interno;
			}
			
			if ($Factura == "") {
				$Factura = $Interno;
			}
			
			if ($Tipo_Pedido == "Produccion")
			{
				$query = "SELECT orden_produccion, estado FROM produccion_final WHERE interno = '".$Interno."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				if (mysql_num_rows($result) <= 0)
				{
					// CREAR
					$query = "SELECT id FROM produccion_final ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
					
					$row = mysql_fetch_array($result);
					$id = $row['id'] + 1;
					$len = strlen($id);
					$zero = "";
					if ($len < 6)
					{
						$num = 6 - $len;
						for ($a = 0; $a < $num; $a++) {
							$zero .= "0";
						}
						$Ord_Produccion = "OP".$zero."".$id."";
					}
					else {
						$Ord_Produccion = "OP".$id."";
					}
					
					$query = "INSERT INTO produccion_final 
					(orden_produccion, orden_compra, interno, destino, cliente_id, fecha, estado, digitado_por, fecha_digitado) 
					VALUES ('".$Ord_Produccion."', '".$Ord_Compra."', '".$Interno."', 'Cliente', '".$ClienteID."', '".$Fecha."', 
					'Pendiente', '".$DigitadorID."', NOW())";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				}
				else
				{
					$row = mysql_fetch_array($result, MYSQL_ASSOC);
					$Ord_Produccion = $row["orden_produccion"];
					if ($row["estado"] == "Aprobado")
					{
						$ReturnData[0] = array(
							"MESSAGE" => "PRODUCCION",
						);
						echo json_encode($ReturnData);
						die();
					}
					// UPDATE 
					$query = "UPDATE produccion_final SET cliente_id = '".$ClienteID."', 
					modificado_por = '".$_SESSION["UserCode"]."', fecha_modificado = NOW() 
					WHERE interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
					
					// BORRAR MOVIMIENTOS
					$query = "DELETE FROM produccion_movs WHERE interno = '".$Interno."' AND tipo = 'Obtener'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
				}
			}
		}
		
		if ($Tipo_Pedido == "Produccion")
		{
			if ($Produccion == "true")
			{
				$query = "INSERT INTO produccion_movs 
				(codigo, cantidad, tipo, orden_produccion, orden_compra, interno, cliente_id) VALUES 
				('".$Codigo."', '".$Cantidad."', 'Obtener', '".$Ord_Produccion."', '".$Ord_Compra."', '".$Interno."', '".$ClienteID."')";
				$result = mysql_query($query) or die ("SQL Error 4: " . mysql_error());
			}
		}
		
		$query = "SELECT `codigo` FROM fact_movs WHERE codigo = '".$Codigo."' AND `interno` = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			//-- Fact Movs
			$query = "UPDATE fact_movs SET 
			`cantidad`= '".$Cantidad."', 
			`desc` = '".$Dcto."', 
			`precio` = '".$Unitario."', 
			`interno` = '".$Interno."', 
			`factura` = '".$Factura."', 
			`remision` = '".$Remision."', 
			`cliente_id` = '".$ClienteID."' 
			WHERE codigo = '".$Codigo."' AND `interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			//echo "Resultado de Fac_Movs update -> ".$result."\n";
		}
		else
		{
			//-- Fact Movs
			$query = "INSERT INTO fact_movs
			(`codigo`, `cantidad`, `desc`, `precio`, `interno`, `orden_compra`, `factura`, `remision`, `cliente_id`) VALUES
			('".$Codigo."', '".$Cantidad."', '".$Dcto."', '".$Unitario."', '".$Interno."', '".$Ord_Compra."', '".$Factura."', 
			'".$Remision."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
			//echo "Resultado de Fac_Movs insert -> ".$result."\n";
		}
	
		//-- Fact Final
		if ($final == false)
		{
			$query = "UPDATE fact_final SET 
			`remision` = '".$Remision."', 
			`fecha_remision` = '".$Fecha."', 
			`factura` = '".$Factura."', 
			`cliente_id` = '".$ClienteID."', 
			`ruta` = '".$Ruta."', 
			`direccion_entrega` = '".$Direccion."', 
			`forma_pago` = '".$FormaPago."', 
			`caja_interno` = '".$Caja_Interno."',
			`caja_recibo` = '".$Caja_Recibo."',
			`vendedor_codigo` = '".$VendedorID."', 
			`cobrador_codigo` = '".$CobradorID."', 
			`chofer` = '".$Chofer."', 
			`placa` = '".$Placa."', 
			`tipo_servicio` = '".$TipoServicio."', 
			`tipo_servicio_valor` = '".$TipoServicioValor."', 
			`tipo_descuento` = '".$TipoDcto."', 
			`tipo_descuento_valor` = '".$TipoDctoValor."', 
			`observaciones` = '".$Observaciones."', 
			`sub_total` = '".$Subtotal2."', 
			`total` = '".$Total."', 
			`iva` = '".$Iva."', 
			`peso` = '".$Peso."', 
			`modificado_por` = '".$_SESSION["UserCode"]."', 
			`fecha_modificado` = NOW(), 
			`tipo_pedido` = '".$Tipo_Pedido."', 
			`beneficiario_id` = '".$BeneficiarioID."' 
			WHERE `interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
			
			// Buscar Movimientos Previamente Generados por Ventas -> CREAR
			$query = "SELECT * FROM mov_clientes WHERE orden_compra = '".$Ord_Compra."' 
			AND interno = '".$Interno."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				//-- Caja_Final - UPDATE 1
				$query = "SELECT tipo_movimiento, valor, saldo, caja_interno, caja_recibo FROM mov_clientes 
				WHERE interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."' AND estado = 'Aprobado'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while ($row = mysql_fetch_array($result))
					{
						if ($row["tipo_movimiento"] == "Credito")
						{
							SumarSaldo_Caja($row["valor"], $row["caja_interno"]);
						}
					}
				}
				
				if ($FormaPago == "Credito")
				{
					// Borrar Movimientos
					$query = "DELETE FROM mov_clientes WHERE interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
				}
				
				if ($FormaPago == "Efectivo" && $Caja_Interno != "")
				{
					//-- Mov_Clientes - UPDATE 1
					$query = "UPDATE mov_clientes SET 
					cliente_id = '".$ClienteID."', 
					valor = '".$Total."', 
					remision = '".$Remision."', 
					factura = '".$Factura."', 
					modificado_por = '".$_SESSION["UserCode"]."',
					fecha_modificado = NOW(), 
					vendedor_codigo = '".$VendedorID."', 
					cobrador_codigo = '".$CobradorID."' 
					WHERE interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-2: ".mysql_error():"");
				
					//-- Caja_Final - UPDATE 2
					$query = "UPDATE caja_final SET saldo = saldo - '".$Total."' WHERE caja_interno = '".$Caja_Interno."' 
					AND caja_recibo = '".$Caja_Recibo."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9: ".mysql_error():"");
					
					//--- Mov_Clientes - UPDATE 2
					$query = "UPDATE mov_clientes SET 
					cliente_id = '".$ClienteID."', 
					valor = '".$Total."', 
					saldo = '".$ValorRC."', 
					remision = '".$Remision."', 
					factura = '".$Factura."', 
					modificado_por = '".$_SESSION["UserCode"]."',
					fecha_modificado = NOW(), 
					vendedor_codigo = '".$VendedorID."', 
					cobrador_codigo = '".$CobradorID."', 
					caja_interno = '".$Caja_Interno."', 
					caja_recibo = '".$Caja_Recibo."' 
					WHERE interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."' AND tipo_movimiento = 'Credito' AND estado = 'Aprobado'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9: ".mysql_error():"");
				}
				/*if ($FormaPago == "Credito")
				{
					// Buscar Facturas
					$query = "SELECT DISTINCT * FROM mov_clientes WHERE tipo_movimiento = 'Credito' AND interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9: ".mysql_error():"");
					
					while ($row = mysql_fetch_array($result))
					{
						// Buscar Pago
						$query = "SELECT * FROM mov_clientes WHERE tipo_movimiento = 'Credito' AND interno = '".$Interno."' 
						AND caja_interno = '".$row["caja_interno"]."' AND caja_recibo = '".$row["caja_recibo"]."'";
						$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #10: ".mysql_error():"");
						if (mysql_num_rows($result1)>0)
						{
							$TotalDeuda = 0;
							while ($row1 = mysql_fetch_array($result1))
							{
								// Totalizar Deuda
								$TotalDeuda = $TotalDeuda + $row1["saldo"];
							}
							// Sumar Deuda
							$query = "UPDATE caja_final SET saldo = saldo + ".$TotalDeuda." WHERE caja_interno = '".$row["caja_interno"]."' 
							AND caja_recibo = '".$row["caja_recibo"]."'";
							$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #11: ".mysql_error():"");
						}
					}
					
					// Borrar Movimientos
					$query = "DELETE FROM mov_clientes WHERE interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
				}
				else
				{// Actualizar los Movimientos
					// UPDATE Debito
					$query = "UPDATE mov_clientes SET cliente_id = '".$ClienteID."', valor = '".$Total."',  
					remision = '".$Remision."', factura = '".$Factura."', vendedor_codigo = '".$VendedorID."',
					cobrador_codigo = '".$CobradorID."' WHERE tipo_movimiento = 'Debito' AND interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
					// UPDATE Credito
					$query = "UPDATE mov_clientes SET cliente_id = '".$ClienteID."', valor = '".$Total."', saldo = '".$ValorRC."', 
					remision = '".$Remision."', factura = '".$Factura."', vendedor_codigo = '".$VendedorID."', cobrador_codigo = '".$CobradorID."' 
					WHERE tipo_movimiento = 'Credito' AND interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9: ".mysql_error():"");
				}*/
			}
			else
			{
				if ($FormaPago == "Efectivo")
				{// Crear los Movimientos
					//-- Mov_Clientes - INSERT 1
					$query = "INSERT INTO mov_clientes 
					(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, 
					estado, digitado_por, fecha_digitado, aprobado_por, vendedor_codigo, cobrador_codigo) VALUES 
					('".$ClienteID."', 'Debito', '".$Total."', '".$Total."', '".$Interno."', '".$Ord_Compra."', 
					'".$Remision."', '".$Factura."', NOW(), 'Aprobado', '".$DigitadorID."', NOW(), 
					'".$_SESSION["UserCode"]."', '".$VendedorID."', '".$CobradorID."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
					
					//-- Caja_Final - UPDATE
					$query = "UPDATE caja_final SET saldo = saldo - '".$Total."' WHERE caja_interno = '".$Caja_Interno."' 
					AND caja_recibo = '".$Caja_Recibo."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9: ".mysql_error():"");
					
					//--- Mov_Clientes - INSERT 2
					$query = "INSERT INTO mov_clientes 
					(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
					digitado_por, fecha_digitado, aprobado_por, vendedor_codigo, cobrador_codigo, caja_interno, caja_recibo) VALUES 
					('".$ClienteID."', 'Credito', '".$Total."', '".$ValorRC."', '".$Interno."', '".$Ord_Compra."', '".$Remision."', 
					'".$Factura."', NOW(), 'Aprobado', '".$DigitadorID."', NOW(), '".$_SESSION["UserCode"]."', '".$VendedorID."', 
					'".$CobradorID."', '".$Caja_Interno."', '".$Caja_Recibo."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10: ".mysql_error():"");
				}
			}
			$final = true;
		}
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
		"Ord_Produccion" => $Ord_Produccion,
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Ventas_Modificar_Anular']))
{
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	$Motivo = isset($_GET['Motivo']) ? $_GET['Motivo']:"";
	
	if ($Interno != "")
	{
		//-------- CHECK CHANGES
		$query = "SELECT id FROM fact_final WHERE interno = '".$Interno."' AND estado = 'Creado' 
		OR interno = '".$Interno."' AND estado = 'Cotizacion'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) <= 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "CHANGED",
			);
		}
		else
		{
			$query = "SELECT id FROM produccion_final WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die("SQL Error: " . mysql_error());
			if (mysql_num_rows($result) > 0)
			{
				$query = "SELECT id FROM produccion_final WHERE interno = '".$Interno."' AND estado = 'Pendiente'";
				$result = mysql_query($query) or die("SQL Error: " . mysql_error());
				if (mysql_num_rows($result) > 0)
				{
					$query = "UPDATE produccion_final SET estado = 'Anulado', anulado_por = '".$_SESSION["UserCode"]."', 
					motivo_anulado = '".$Motivo."', 
					fecha_anulado = NOW() WHERE interno = '".$Interno."'";
					$result = mysql_query($query) or die("SQL Error: " . mysql_error());
					
					$query = "UPDATE fact_final SET `estado` = 'Anulado', `anulado_por` = '".$_SESSION["UserCode"]."', 
					`motivo_anulado` = '".$Motivo."', `fecha_anulado` = NOW() WHERE `interno` = '".$Interno."'";
					$result = mysql_query($query) or die("SQL Error: " . mysql_error());
				
					$ReturnData[0] = array(
						"MESSAGE" => "OK",
					);
				}
				else
				{
					$ReturnData[0] = array(
						"MESSAGE" => "ERROR",
					);
				}
			}
			else
			{
				$query = "UPDATE fact_final SET `estado` = 'Anulado', `anulado_por` = '".$_SESSION["UserCode"]."', 
				`motivo_anulado` = '".$Motivo."', `fecha_anulado` = NOW() WHERE `interno` = '".$Interno."'";
				$result = mysql_query($query) or die("SQL Error: " . mysql_error());
				
				$ReturnData[0] = array(
					"MESSAGE" => "OK",
				);
			}
		}
		echo json_encode($ReturnData);
	}
}
else if (isset($_GET['Ventas_Modificar_Crear']))
{
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	if ($Interno != "")
	{
		//-------- CHECK CHANGES
		$query = "SELECT id FROM fact_final WHERE interno = '".$Interno."' AND estado = 'Cotizacion'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) <= 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "CHANGED",
			);
		}
		else
		{
			$query = "UPDATE fact_final SET `estado` = 'Creado', `modificado_por` = '".$_SESSION["UserCode"]."', 
			`fecha_modificado` = NOW() WHERE `interno` = '".$Interno."'";
			$result = mysql_query($query) or die("SQL Error: " . mysql_error());
			
			$ReturnData[0] = array(
				"MESSAGE" => "OK",
			);
		}
		echo json_encode($ReturnData);
	}
}
else if (isset($_GET['CrearPedidoMovil']))
{
	$Codigo = 0;
	$Cantidad = 0;
	$ClienteID = 0;
	$Ord_Compra = 0;
	$Observaciones = "";
	$Interno = 0;
	$VendedorID = 0;
	
	$data = $_GET['CrearPedidoMovil'];
	foreach($data as $item) {
		
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		if ($final == false) {
			$ClienteID = $item['ClienteID'];
			$Ord_Compra = $item['Ord_Compra'];
			$Observaciones = $item['Observaciones'];
			$VendedorID = $item['VendedorID'];
			
			$query = "SELECT id FROM fact_final ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die("SQL Error: " . mysql_error());
			
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 7){
				$num = 7 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Interno = "FEM".$zero."".$id."";
			}
			else {
				$Interno = "FEM".$id."";
			}
			
			if ($Ord_Compra == "") {
				$Ord_Compra = $Interno;
			}
		}
		
		$query = "SELECT lista1 FROM productos WHERE cod_fab = '".$Codigo."'";
		$result = mysql_query($query) or die("SQL Error: " . mysql_error());
		$row = mysql_fetch_array($result);
		$Precio = $row['lista1'];
		
		//------------------ INSERT COMMAND
		//-- Fact Movs
		$query = "INSERT INTO fact_movs
		(`codigo`, `cantidad`, `precio`, `interno`, `orden_compra`, `cliente_id`) VALUES 
		('".$Codigo."', '".$Cantidad."', '".$Precio."', '".$Interno."', '".$Ord_Compra."', '".$ClienteID."')";
		$result = mysql_query($query) or die("SQL Error 1:12 " . mysql_error());
		//-- Fact Final
		if ($final == false) {
			$query = "INSERT INTO fact_final 
			(`interno`, `orden_compra`, `cliente_id`, `vendedor_codigo`, `observaciones`, `estado`) VALUES 
			('".$Interno."', '".$Ord_Compra."', '".$ClienteID."', '".$VendedorID."', '".$Observaciones."', 'Creado')";
			$final = true;
			$result = mysql_query($query) or die("SQL Error 13: " . mysql_error());
		}
	}
	echo $Interno;
}
else if (isset($_GET['Ventas_Autorizar']))// DONE ?// Arreglar la visualizacion de VendedorID en el Listado para poder guardar aca.
{
	$Old_Estado = isset($_GET['Old_Estado']) ? $_GET['Old_Estado']:"Creado";
	$New_Estado = isset($_GET['New_Estado']) ? $_GET['New_Estado']:"Creado";
	$Remision = isset($_GET['Remision']) ? $_GET['Remision']:"";
	$Motivo_Anulado = isset($_GET['Motivo_Anulado']) ? $_GET['Motivo_Anulado']:"";
	$Fecha = isset($_GET['Fecha']) ? $_GET['Fecha']:"0000-00-00";
	$Factura = isset($_GET['Factura']) ? $_GET['Factura']:"";
	$Ord_Compra = isset($_GET['Ord_Compra']) ? $_GET['Ord_Compra']:"";
	$Ord_Produccion = isset($_GET['Ord_Produccion']) ? $_GET['Ord_Produccion']:"";
	$VendedorID = isset($_GET['VendedorID']) ? $_GET['VendedorID']:"";
	$CobradorID = isset($_GET['CobradorID']) ? $_GET['CobradorID']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	//--
	//$EsCredito = false;
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM fact_final WHERE interno = '".$Interno."' AND estado = '".$Old_Estado."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
	}
	else
	{
		$query = "UPDATE fact_final SET 
		estado = '".$New_Estado."', 
		remision = '".$Remision."', 
		motivo_anulado = '".$Motivo_Anulado."', 
		fecha_remision = '".$Fecha."', 
		factura = '".$Factura."',
		vendedor_codigo = '".$VendedorID."', 
		cobrador_codigo = '".$CobradorID."'";
		$query .= ($New_Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
		$query .= ($New_Estado == "Autorizado") ? ", autorizado_por = '".$_SESSION["UserCode"]."', fecha_autorizado = NOW()":"";
		$query .= " WHERE orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."' AND interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		if (mysql_affected_rows() > 0)
		{
			switch($New_Estado)
			{
				case "Anulado":
					// Buscar Movimientos
					$query = "SELECT * FROM mov_clientes WHERE interno = '".$Interno."' 
					AND orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						Anular_Final($ClienteID, $Ord_Compra, $Interno, "Venta", $Motivo_Anulado);
					}
				break;
				
				case "Creado":
				case "Autorizado":
					if ($Old_Estado == "Aprobado")
					{
						Anular_CarteraMovs($ClienteID, $Ord_Compra, $Interno, "Anulado Automaticamente por Ventas -> Autorizar");
						Del_InventoryMovs($Interno);
						
						if ($Ord_Produccion != "")
						{
							$query = "UPDATE produccion_final SET estado = 'Pendiente' 
							WHERE orden_produccion = '".$Ord_Produccion."' AND interno = '".$Interno."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
							
							$query = "UPDATE produccion_proc SET estado = 'Pendiente' 
							WHERE orden_produccion = '".$Ord_Produccion."' ";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-5: ".mysql_error():"");
							
							Del_InventoryMovs($Ord_Produccion);
						}
					}
				break;
			}
		}
		
		$query = "UPDATE mov_clientes SET 
		remision = '".$Remision."', 
		factura = '".$Factura."', 
		vendedor_codigo = '".$VendedorID."', 
		cobrador_codigo = '".$CobradorID."' 
		WHERE orden_compra = '".$Ord_Compra."' AND cliente_id = '".$ClienteID."' AND interno = '".$Interno."' AND tipo_movimiento = 'Debito'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_POST['Ventas_Despachar']))
{
	//Parte 1
	$ClienteID = 0;
	$Fecha = 0;
	$Remision = 0;
	$Ord_Compra = 0;
	$Factura = 0;
	$Interno = 0;
	$BeneficiarioID = 0;
	//Parte 2
	$Codigo = 0;
	$Cantidad = 0;
	$Cantidad2 = 0;
	$Dcto = 0;
	$Unitario = 0;
	// Parte 3
	$Observaciones = 0;
	$Subtotal2 = 0;
	$TipoServicio = 0;
	$TipoServicioValor = 0;
	$Iva = 0;
	$TipoDcto = "";
	$TipoDctoValor = 0;
	$Total = 0;
	$Chofer = 0;
	$Placa = 0;
	$Peso = 0;
	$FormaPago = 0;
	$Ruta = 0;
	$Direccion = 0;
	$VendedorID = 0;
	//
	$NewInterno = 0;
	$last_parcial = true;
	$DigitadorID = "";
	$MantenerFecha = false;
	
	$data = $_POST['Ventas_Despachar'];
	
	//-------- CHECK CHANGES
	$query = "SELECT * FROM fact_final WHERE interno = '".$data[0]['Interno']."' AND estado = 'Autorizado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	else
	{
		$row = mysql_fetch_array($result);
		$DigitadorID = $row["digitado_por"];
		$AutorizadorID = $row["autorizado_por"];
		$AprobadorID = $row["aprobado_por"];
		$FechaAprobado = $row["fecha_aprobado"];
		
		if ($FechaAprobado != "0000-00-00 00:00:00")
			$MantenerFecha = true;
		else
			$MantenerFecha = false;
	}
	
	//-------- CHECK IF SOME ITEM HAS BEEN DELETED
	$delete = true;
	$query = "SELECT codigo FROM fact_movs WHERE orden_compra = '".$data[0]['Ord_Compra']."' AND interno = '".$data[0]['Interno']."'";
	$result = mysql_query($query) or die ("SQL Error 5: " . mysql_error());
	$numrow = mysql_num_rows($result);
	if ($numrow > 0)
	{
		while ($rows = mysql_fetch_array($result))
		{
			$num = count($data);
			for ($i=0; $i<$num; $i++)
			{
				if ($rows['codigo'] == $data[$i]['CodFab'])
				{
					$delete = false;
				}
			}
			if ($delete == true)
			{
				$query2 = "DELETE FROM fact_movs WHERE orden_compra = '".$data[0]['Ord_Compra']."' 
				AND interno = '".$data[0]['Interno']."' AND codigo = '".$rows['codigo']."'";
				$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	//-------- CHECK IF LAST PARCIAL
	foreach($data as $item)
	{
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		$Cantidad2 = $item['Cantidad2'];
		if ($final == false)
		{
			$ClienteID = $item['ClienteID'];
			$Fecha = $item['Fecha'];
			$Ord_Compra = $item['Ord_Compra'];
			$Interno = $item['Interno'];
			$Remision = $item['Remision'];
			$Factura = $item['Factura'];
			$final = true;
		}
		
		$query = "SELECT * FROM fact_movs WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."' AND codigo = '".$Codigo."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$total_qty = $row["cantidad_despachada"] + $Cantidad2;
			//echo " ".$total_qty." <br/>";
			if ($total_qty != $row["cantidad"])
			{
				$last_parcial = false;
			}
		}
	}
	$final = false;
	
	if ($last_parcial == true)
	{
		foreach($data as $item)
		{
			$Codigo = $item['CodFab'];
			$Cantidad = $item['Cantidad'];
			$Cantidad2 = $item['Cantidad2'];
			$Dcto = $item['Dcto'];
			$Unitario = $item['Unitario'];
			if ($final == false)
			{
				$ClienteID = $item['ClienteID'];
				$Fecha = $item['Fecha'];
				$VendedorID = $item['VendedorID'];
				$Remision = $item['Remision'];
				$Ord_Compra = $item['Ord_Compra'];
				$Factura = $item['Factura'];
				$Interno = $item['Interno'];
				$Observaciones = $item['Observaciones'];
				$Subtotal2 = $item['Subtotal2'];
				$TipoServicio = $item['TipoServicio'];
				$TipoServicioValor = $item['TipoServicioValor'];
				$Iva = $item['Iva'];
				$TipoDcto = $item['TipoDcto'];
				$TipoDctoValor = $item['TipoDctoValor'];
				$Peso = $item['Peso'];
				$FormaPago = $item['FormaPago'];
				$Total = $item['Total'];
				/*if ($FormaPago == "Efectivo") {
					$Total = $item['Total'] + $TipoServicioValor;
					$Iva = $Total - ($Total / 1.16);
				}
				else {
					$Total = $item['Total'];
					$Iva = $item['Iva'];
				}*/
				$Chofer = $item['Chofer'];
				$Placa = $item['Placa'];
				$Ruta = $item['Ruta'];
				$Direccion = $item['Direccion'];
				$VendedorID = $item['VendedorID'];
				$CobradorID = $item['CobradorID'];
				$Caja_Interno = $item['Caja_Interno'];
				$Caja_Recibo = $item['Caja_Recibo'];
				$ValorRC = $item['ValorRC'];
				$Saldo = $item['Saldo'];
				
				$query = "SELECT beneficiario_id FROM fact_final WHERE interno = '".$Interno."' 
				AND orden_compra = '".$Ord_Compra."' AND estado = 'Autorizado'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_array($result);
					$BeneficiarioID = $row['beneficiario_id'];
				}
				else
					$BeneficiarioID = "";
			}
			//--------------------------------------------------------------------------------//
			//-------------------------------- INSERT COMMAND --------------------------------//
			//--------------------------------------------------------------------------------//
			
			if ($Cantidad2 > 0)
			{
				//-- Update Fact Movs
				$query = "UPDATE fact_movs SET remision = '".$Remision."', factura = '".$Factura."', 
				cantidad = '".$Cantidad2."', cantidad_despachada = '0.00' 
				WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."' AND codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
			}
			else {
				$query = "DELETE FROM fact_movs WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."' AND codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
			}
			
			//-- Fact Final
			if ($final == false)
			{
				if ($FormaPago == "Efectivo")
				{
					// Buscar Movimientos Previamente Generados por Ventas -> CREAR / MODIFICAR
					$query = "SELECT * FROM mov_clientes WHERE orden_compra = '".$Ord_Compra."' 
					AND interno = '".$Interno."' AND tipo_movimiento = 'Credito' AND estado = 'Aprobado'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
					if (mysql_num_rows($result) <= 0)
					{
						//-- Mov_Clientes - INSERT 1
						$query = "INSERT INTO mov_clientes 
						(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
						digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, vendedor_codigo, cobrador_codigo) VALUES 
						('".$ClienteID."', 'Debito', '".$Total."', '0', '".$Interno."', '".$Ord_Compra."', 
						'".$Remision."', '".$Factura."', ";
						$query .= $MantenerFecha == false ? "NOW(), ":"'".$FechaAprobado."', ";
						$query .= "'Aprobado', '".$DigitadorID."', ";
						$query .= $MantenerFecha == false ? "NOW(), ":"'".$FechaAprobado."', ";
						$query .= $MantenerFecha == false ? "'".$_SESSION["UserCode"]."', NOW(), ":"'".$AprobadorID."', '".$FechaAprobado."', ";
						$query .= "'".$VendedorID."', '".$CobradorID."')";
						//'".$Remision."', '".$Factura."', NOW(), 'Aprobado', '".$DigitadorID."', NOW(), '".$_SESSION["UserCode"]."', 
						//NOW(), '".$VendedorID."', '".$CobradorID."')";
						$result = mysql_query($query) or die("SQL Error 7-3: " . mysql_error());
						
						//-- Caja_Final - UPDATE
						$query = "UPDATE caja_final SET saldo = saldo - '".$Total."' WHERE caja_interno = '".$Caja_Interno."' 
						AND caja_recibo = '".$Caja_Recibo."'";
						$result = mysql_query($query) or die("SQL Error 7-4: " . mysql_error());
						
						//--- Mov_Clientes - INSERT 2
						$query = "INSERT INTO mov_clientes 
						(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
						digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, vendedor_codigo, cobrador_codigo, caja_interno, 
						caja_recibo) VALUES ('".$ClienteID."', 'Credito', '".$Total."', '".$ValorRC."', '".$Interno."', '".$Ord_Compra."', 
						'".$Remision."', '".$Factura."', ";
						$query .= $MantenerFecha == false ? "NOW(), ":"'".$FechaAprobado."', ";
						$query .= "'Aprobado', '".$DigitadorID."', ";
						$query .= $MantenerFecha == false ? "NOW(), ":"'".$FechaAprobado."', ";
						$query .= $MantenerFecha == false ? "'".$_SESSION["UserCode"]."', NOW(), ":"'".$AprobadorID."', '".$FechaAprobado."', ";
						$query .= "'".$VendedorID."', '".$CobradorID."', '".$Caja_Interno."', '".$Caja_Recibo."')";
						//'".$Remision."', '".$Factura."', NOW(), 'Aprobado', '".$DigitadorID."', NOW(), '".$_SESSION["UserCode"]."', NOW(), 
						//'".$VendedorID."', '".$CobradorID."', '".$Caja_Interno."', '".$Caja_Recibo."')";
						$result = mysql_query($query) or die("SQL Error 7-5: " . mysql_error());
					}
					
					$query = "UPDATE fact_final SET 
					`remision` = '".$Remision."', 
					`fecha_remision` = '".$Fecha."', 
					`factura` = '".$Factura."', 
					`cliente_id` = '".$ClienteID."', 
					`ruta` = '".$Ruta."',
					`direccion_entrega` = '".$Direccion."', 
					`forma_pago` = '".$FormaPago."', 
					`caja_interno` = '".$Caja_Interno."',
					`caja_recibo` = '".$Caja_Recibo."',
					`vendedor_codigo` = '".$VendedorID."', 
					`cobrador_codigo` = '".$CobradorID."', 
					`chofer` = '".$Chofer."', 
					`placa` = '".$Placa."', 
					`tipo_servicio` = '".$TipoServicio."', 
					`tipo_servicio_valor` = '".$TipoServicioValor."', 
					`tipo_descuento` = '".$TipoDcto."', 
					`tipo_descuento_valor` = '".$TipoDctoValor."', 
					`observaciones` = '".$Observaciones."', 
					`sub_total` = '".$Subtotal2."', 
					`total` = '".$Total."', 
					`iva` = '".$Iva."', 
					`estado` = 'Aprobado', 
					`peso` = '".$Peso."', ";
					$query .= $MantenerFecha == false ? "
					`aprobado_por` = '".$_SESSION["UserCode"]."', 
					`fecha_aprobado` = NOW(), ":"";
					$query .= "
					`beneficiario_id` = '".$BeneficiarioID."' 
					WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."'";
					$result = mysql_query($query) or die("SQL Error 7-2: " . mysql_error());
				}
				else
				{
					$query = "UPDATE fact_final SET 
					`remision` = '".$Remision."', 
					`fecha_remision` = '".$Fecha."', 
					`factura` = '".$Factura."', 
					`cliente_id` = '".$ClienteID."', 
					`ruta` = '".$Ruta."', 
					`direccion_entrega` = '".$Direccion."', 
					`forma_pago` = '".$FormaPago."', 
					`vendedor_codigo` = '".$VendedorID."', 
					`cobrador_codigo` = '".$CobradorID."', 
					`chofer` = '".$Chofer."', 
					`placa` = '".$Placa."', 
					`tipo_servicio` = '".$TipoServicio."', 
					`tipo_servicio_valor` = '".$TipoServicioValor."', 
					`tipo_descuento` = '".$TipoDcto."', 
					`tipo_descuento_valor` = '".$TipoDctoValor."', 
					`observaciones` = '".$Observaciones."', 
					`sub_total` = '".$Subtotal2."', 
					`total` = '".$Total."', 
					`iva` = '".$Iva."', 
					`estado` = 'Aprobado', 
					`peso` = '".$Peso."', ";
					$query .= $MantenerFecha == false ? "
					`aprobado_por` = '".$_SESSION["UserCode"]."', 
					`fecha_aprobado` = NOW(), ":"";
					$query .= "
					`beneficiario_id` = '".$BeneficiarioID."'
					WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
				}
				
				//-- Mov_Clientes
				if ($FormaPago == "Credito")
				{
					$query = "INSERT INTO mov_clientes 
					(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
					digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, vendedor_codigo, cobrador_codigo) VALUES 
					('".$ClienteID."', 'Debito', '".$Total."', '".$Total."', '".$Interno."', '".$Ord_Compra."', 
					'".$Remision."', '".$Factura."', ";
					$query .= $MantenerFecha == false ? "NOW(), ":"'".$FechaAprobado."', ";
					$query .= "'Aprobado', '".$DigitadorID."', ";
					$query .= $MantenerFecha == false ? "NOW(), ":"'".$FechaAprobado."', ";
					$query .= $MantenerFecha == false ? "'".$_SESSION["UserCode"]."', NOW(), ":"'".$AprobadorID."', '".$FechaAprobado."', ";
					$query .= "'".$VendedorID."', '".$CobradorID."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
				}
				$final = true;
			}
			
			//--- INSERT INTO Inventario_Movs
			if ($Cantidad2 > 0)
			{
				if (!$MantenerFecha)
					AfectarInventario($Codigo, $Cantidad2, $Unitario, $Interno, "", "Factura", "Venta");
				else
					AfectarInventario($Codigo, $Cantidad2, $Unitario, $Interno, $FechaAprobado, "Factura", "Venta");
			}
		}
		$ReturnData[0] = array(
			"MESSAGE" => $Interno,
		);
	}
	else
	{
		foreach($data as $item)
		{
			$Codigo = $item['CodFab'];
			$Cantidad = $item['Cantidad'];
			$Cantidad2 = $item['Cantidad2'];
			$Dcto = $item['Dcto'];
			$Unitario = $item['Unitario'];
			if ($final == false)
			{
				$ClienteID = $item['ClienteID'];
				$Fecha = $item['Fecha'];
				$VendedorID = $item['VendedorID'];
				$Remision = $item['Remision'];
				$Ord_Compra = $item['Ord_Compra'];
				$Factura = $item['Factura'];
				$Interno = $item['Interno'];
				$Observaciones = $item['Observaciones'];
				$Subtotal2 = $item['Subtotal2'];
				$TipoServicio = $item['TipoServicio'];
				$TipoServicioValor = $item['TipoServicioValor'];
				$Iva = $item['Iva'];
				$TipoDcto = $item['TipoDcto'];
				$TipoDctoValor = $item['TipoDctoValor'];
				$Total = $item['Total'];
				$Chofer = $item['Chofer'];
				$Placa = $item['Placa'];
				$Peso = $item['Peso'];
				$FormaPago = $item['FormaPago'];
				$Ruta = $item['Ruta'];
				$Direccion = $item['Direccion'];
				$VendedorID = $item['VendedorID'];
				$CobradorID = $item['CobradorID'];
				$Caja_Interno = $item['Caja_Interno'];
				$Caja_Recibo = $item['Caja_Recibo'];
				$ValorRC = $item['ValorRC'];
				$Saldo = $item['Saldo'];
				
				$query = "SELECT id FROM fact_final ORDER BY id DESC LIMIT 1";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
				
				$row = mysql_fetch_array($result);
				$id = $row['id'] + 1;
				$len = strlen($id);
				$zero = "";
				if ($len < 8) {
					$num = 8 - $len;
					for ($a = 0; $a < $num; $a++) {
						$zero .= "0";
					}
					$NewInterno = "FER".$zero."".$id."";
				}
				else {
					$NewInterno = "FER".$id."";
				}
				
				$query = "SELECT beneficiario_id FROM fact_final WHERE orden_compra = '".$Ord_Compra."' AND estado = 'Autorizado'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
				
				$row = mysql_fetch_array($result);
				$BeneficiarioID = $row['beneficiario_id'];
			}
			//--------------------------------------------------------------------------------//
			//-------------------------------- INSERT COMMAND --------------------------------//
			//--------------------------------------------------------------------------------//
			
			if ($Cantidad2 > 0)
			{
				//-- Update Fact Movs
				$query = "UPDATE fact_movs SET cantidad_despachada = cantidad_despachada + '".$Cantidad2."' WHERE orden_compra = '".$Ord_Compra."' 
				AND interno = '".$Interno."' AND codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
				//-- Fact Movs
				$query = "INSERT INTO fact_movs
				(`codigo`, `cantidad`, `desc`, `precio`, `interno`, `orden_compra`, `factura`, `remision`, `cliente_id`) VALUES
				('".$Codigo."', '".$Cantidad2."', '".$Dcto."', '".$Unitario."', '".$NewInterno."', '".$Ord_Compra."', '".$Factura."', 
				'".$Remision."', '".$ClienteID."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
			}
			
			//-- Fact Final
			if ($final == false)
			{
				if ($FormaPago == "Efectivo")
				{
					// Buscar Movimientos Previamente Generados por Ventas -> CREAR / MODIFICAR
					$query = "SELECT * FROM mov_clientes WHERE orden_compra = '".$Ord_Compra."' 
					AND interno = '".$Interno."' AND tipo_movimiento = 'Credito' AND estado = 'Aprobado'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
					if (mysql_num_rows($result) <= 0)
					{
						//-- Mov_Clientes - INSERT 1
						$query = "INSERT INTO mov_clientes 
						(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
						digitado_por, fecha_digitado, aprobado_por, vendedor_codigo, cobrador_codigo) VALUES 
						('".$ClienteID."', 'Debito', '".$Total."', '0', '".$NewInterno."', '".$Ord_Compra."', 
						'".$Remision."', '".$Factura."', NOW(), 'Aprobado', '".$DigitadorID."', NOW(), '".$_SESSION["UserCode"]."', 
						'".$VendedorID."', '".$CobradorID."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-2: ".mysql_error():"");
						
						//-- Caja_Final - UPDATE
						$query = "UPDATE caja_final SET saldo = saldo - '".$Total."' WHERE caja_interno = '".$Caja_Interno."' 
						AND caja_recibo = '".$Caja_Recibo."'";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-3: ".mysql_error():"");
						
						//--- Mov_Clientes - INSERT 2
						$query = "INSERT INTO mov_clientes 
						(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
						digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, vendedor_codigo, cobrador_codigo, caja_interno, 
						caja_recibo) VALUES ('".$ClienteID."', 'Credito', '".$Total."', '".$ValorRC."', '".$NewInterno."', 
						'".$Ord_Compra."', '".$Remision."', '".$Factura."', NOW(), 'Aprobado', '".$DigitadorID."', NOW(), 
						'".$_SESSION["UserCode"]."', NOW(), '".$VendedorID."', '".$CobradorID."', '".$Caja_Interno."', '".$Caja_Recibo."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-4: ".mysql_error():"");
					}
					
					$query = "UPDATE fact_final SET peso = (peso - '".$Peso."') 
					WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-1: ".mysql_error():"");
						
					$query = "INSERT INTO fact_final 
					(`interno`, `remision`, `fecha_remision`, `factura`, `orden_compra`, `cliente_id`, `ruta`, 
					`direccion_entrega`, `forma_pago`, `caja_interno`, `caja_recibo`, `vendedor_codigo`, `cobrador_codigo`, 
					`chofer`, `placa`, `tipo_servicio`, `tipo_servicio_valor`, `tipo_descuento`, `tipo_descuento_valor`, 
					`observaciones`, `sub_total`, `total`, `iva`, `estado`, `peso`, `digitado_por`, `fecha_digitado`, 
					`autorizado_por`, `fecha_autorizado`, `aprobado_por`, `fecha_aprobado`, `beneficiario_id`) VALUES 
					('".$NewInterno."', '".$Remision."', '".$Fecha."', '".$Factura."', '".$Ord_Compra."', '".$ClienteID."', 
					'".$Ruta."', '".$Direccion."', '".$FormaPago."', '".$Caja_Interno."', '".$Caja_Recibo."', '".$VendedorID."', '".$CobradorID."', 
					'".$Chofer."', '".$Placa."', '".$TipoServicio."', '".$TipoServicioValor."', '".$TipoDcto."', '".$TipoDctoValor."', 
					'".$Observaciones."', '".$Subtotal2."', '".$Total."', '".$Iva."', 'Aprobado', '".$Peso."', '".$DigitadorID."', 
					NOW(), '".$AutorizadorID."', NOW(), '".$_SESSION["UserCode"]."', NOW(), '".$BeneficiarioID."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-5: ".mysql_error():"");
				}
				else
				{
					$query = "UPDATE fact_final SET peso = (peso - '".$Peso."') 
					WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-6: ".mysql_error():"");
					
					$query = "INSERT INTO fact_final 
					(`interno`, `remision`, `fecha_remision`, `factura`, `orden_compra`, `cliente_id`, `ruta`, 
					`direccion_entrega`, `forma_pago`, `vendedor_codigo`, `cobrador_codigo`, `chofer`, `placa`, `tipo_servicio`, 
					`tipo_servicio_valor`, `tipo_descuento`, `tipo_descuento_valor`, `observaciones`, `sub_total`, `total`, `iva`, `estado`, 
					`peso`, `digitado_por`, `fecha_digitado`, `autorizado_por`, `fecha_autorizado`, `aprobado_por`, `fecha_aprobado`, 
					`beneficiario_id`) VALUES ('".$NewInterno."', '".$Remision."', '".$Fecha."', '".$Factura."', '".$Ord_Compra."', 
					'".$ClienteID."', '".$Ruta."', '".$Direccion."', '".$FormaPago."', '".$VendedorID."', '".$CobradorID."', '".$Chofer."', 
					'".$Placa."', '".$TipoServicio."', '".$TipoServicioValor."', '".$TipoDcto."', '".$TipoDctoValor."', '".$Observaciones."', 
					'".$Subtotal2."', '".$Total."', '".$Iva."', 'Aprobado', '".$Peso."', '".$DigitadorID."', NOW(), '".$AutorizadorID."', 
					NOW(), '".$_SESSION["UserCode"]."', NOW(), '".$BeneficiarioID."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-7: ".mysql_error():"");
				}
				
				//-- Mov_Clientes
				if ($FormaPago == "Credito")
				{
					$query = "INSERT INTO mov_clientes 
					(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
					digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, cobrador_codigo) VALUES 
					('".$ClienteID."', 'Debito', '".$Total."', '".$Total."', '".$NewInterno."', '".$Ord_Compra."', '".$Remision."', 
					'".$Factura."', NOW(), 'Aprobado', '".$DigitadorID."', NOW(), '".$_SESSION["UserCode"]."', NOW(), '".$CobradorID."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9: ".mysql_error():"");
				}
				
				$final = true;
			}
			
			//--- INSERT INTO Inventario_Movs
			if ($Cantidad2 > 0)
				AfectarInventario($Codigo, $Cantidad2, $Unitario, $NewInterno, "", "Factura", "Venta");
		}
		$ReturnData[0] = array(
			"MESSAGE" => $NewInterno,
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_POST['Ventas_Ajustar']))
{
	//Parte 1
	$ClienteID = 0;
	$Fecha = 0;
	$Remision = 0;
	$Ord_Compra = 0;
	$Factura = 0;
	$Interno = 0;
	$BeneficiarioID = 0;
	//Parte 2
	$Codigo = 0;
	$Cantidad = 0;
	$Dcto = 0;
	$Unitario = 0;
	// Parte 3
	$Observaciones = 0;
	$Subtotal2 = 0;
	$TipoServicio = 0;
	$TipoServicioValor = 0;
	$Iva = 0;
	$TipoDcto = "";
	$TipoDctoValor = 0;
	$Total = 0;
	$Chofer = 0;
	$Placa = 0;
	$Peso = 0;
	$FormaPago = 0;
	$Ruta = 0;
	$Direccion = 0;
	$VendedorID = 0;
	//
	$NewInterno = 0;
	$last_parcial = true;
	$DigitadorID = "";
	
	$data = $_POST['Ventas_Ajustar'];
	
	//-------- CHECK CHANGES
	$query = "SELECT * FROM fact_final WHERE interno = '".$data[0]['Interno']."' AND estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	else
	{
		$row = mysql_fetch_array($result);
		$DigitadorID = $row["digitado_por"];
	}
	
	//-------- CHECK IF SOME ITEM HAS BEEN DELETED
	$delete = true;
	$query = "SELECT codigo FROM fact_movs WHERE orden_compra = '".$data[0]['Ord_Compra']."' AND interno = '".$data[0]['Interno']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
	$numrow = mysql_num_rows($result);
	if ($numrow > 0)
	{
		while ($rows = mysql_fetch_array($result))
		{
			$num = count($data);
			for ($i=0; $i<$num; $i++)
			{
				if ($rows['codigo'] == $data[$i]['CodFab'])
				{
					$delete = false;
				}
			}
			if ($delete == true)
			{
				$query2 = "DELETE FROM fact_movs WHERE orden_compra = '".$data[0]['Ord_Compra']."' 
				AND interno = '".$data[0]['Interno']."' AND codigo = '".$rows['codigo']."'";
				$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	$final = false;
	
	// Buscar Despachado "No Finalizado" y que no sea el mismo
	$query = "SELECT interno, codigo FROM fact_movs WHERE cantidad_despachada > '0' AND interno != '".$data[0]['Interno']."' 
	AND orden_compra = '".$data[0]['Ord_Compra']."' AND cliente_id = '".$data[0]['ClienteID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Original[] = array(
				"Interno" => $row["interno"],
				"Codigo" =>  $row["codigo"],
			);
		}
	}
	
	foreach($data as $item)
	{
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		$Dcto = $item['Dcto'];
		$Unitario = $item['Unitario'];
		if ($final == false)
		{
			$ClienteID = $item['ClienteID'];
			$VendedorID = $item['VendedorID'];
			$Remision = $item['Remision'];
			$Ord_Compra = $item['Ord_Compra'];
			$Factura = $item['Factura'];
			$Interno = $item['Interno'];
			$Observaciones = $item['Observaciones'];
			$Subtotal2 = $item['Subtotal2'];
			$TipoServicio = $item['TipoServicio'];
			$TipoServicioValor = $item['TipoServicioValor'];
			$Iva = $item['Iva'];
			$TipoDcto = $item['TipoDcto'];
			$TipoDctoValor = $item['TipoDctoValor'];
			$Peso = $item['Peso'];
			$FormaPago = $item['FormaPago'];
			$Total = $item['Total'];
			/*if ($FormaPago == "Efectivo") {
				$Total = $item['Total'] + $TipoServicioValor;
				$Iva = $Total - ($Total / 1.16);
			}
			else {
				
				$Iva = $item['Iva'];
			}*/
			$Chofer = $item['Chofer'];
			$Placa = $item['Placa'];
			$Ruta = $item['Ruta'];
			$Direccion = $item['Direccion'];
			$VendedorID = $item['VendedorID'];
			
			$query = "SELECT beneficiario_id, cobrador_codigo FROM fact_final WHERE interno = '".$Interno."' 
			AND orden_compra = '".$Ord_Compra."' AND estado = 'Aprobado'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
			
			$row = mysql_fetch_array($result);
			$BeneficiarioID = $row['beneficiario_id'];
			$CobradorID = $row['beneficiario_id'];
			
			$Fecha = Del_InventoryMovs($Interno);
		}
		//--------------------------------------------------------------------------------//
		//-------------------------------- INSERT COMMAND --------------------------------//
		//--------------------------------------------------------------------------------//
		
		$query = "SELECT codigo, cantidad FROM fact_movs WHERE codigo = '".$Codigo."' 
		AND orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			//-- Fact Movs
			$query = "UPDATE fact_movs SET 
			cantidad= '".$Cantidad."', 
			`desc` = '".$Dcto."', 
			precio = '".$Unitario."', 
			factura = '".$Factura."', 
			remision = '".$Remision."' 
			WHERE codigo = '".$Codigo."' AND orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-2: ".mysql_error():"");
			
			if (isset($Original))
			{
				foreach($Original AS $Odata)
				{
					if ($Odata["Codigo"] == $Codigo)
					{
						$NewTotal = 0;
						if ($row["cantidad"] < $Cantidad)
						{
							$NewTotal = $Cantidad - $row["cantidad"];
							$query = "UPDATE fact_movs SET 
							cantidad_despachada = cantidad_despachada + '".$NewTotal."' 
							WHERE codigo = '".$Codigo."' AND orden_compra = '".$Ord_Compra."' AND interno = '".$Odata["Interno"]."'";
						}
						else if ($row["cantidad"] > $Cantidad)
						{
							$NewTotal = $row["cantidad"] - $Cantidad;
							$query = "UPDATE fact_movs SET 
							cantidad_despachada = cantidad_despachada - '".$NewTotal."' 
							WHERE codigo = '".$Codigo."' AND orden_compra = '".$Ord_Compra."' AND interno = '".$Odata["Interno"]."'";
						}
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-3: ".mysql_error():"");
						break;
					}
				}
			}
		}
		else
		{
			//-- Fact Movs
			$query = "INSERT INTO fact_movs
			(`codigo`, `cantidad`, `desc`, `precio`, `interno`, `orden_compra`, `factura`, `remision`, `cliente_id`) VALUES
			('".$Codigo."', '".$Cantidad."', '".$Dcto."', '".$Unitario."', '".$Interno."', '".$Ord_Compra."', '".$Factura."', 
			'".$Remision."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-4: ".mysql_error():"");
		}
		
		//-- Fact Final
		if ($final == false)
		{
			$query = "UPDATE fact_final SET 
			`remision` = '".$Remision."', 
			`factura` = '".$Factura."', 
			`orden_compra` = '".$Ord_Compra."', 
			`cliente_id` = '".$ClienteID."', 
			`ruta` = '".$Ruta."',
			`direccion_entrega` = '".$Direccion."', 
			`forma_pago` = '".$FormaPago."', 
			`vendedor_codigo` = '".$VendedorID."', 
			`chofer` = '".$Chofer."', 
			`placa` = '".$Placa."', 
			`tipo_servicio` = '".$TipoServicio."', 
			`tipo_servicio_valor` = '".$TipoServicioValor."', 
			`tipo_descuento` = '".$TipoDcto."', 
			`tipo_descuento_valor` = '".$TipoDctoValor."', 
			`observaciones` = '".$Observaciones."', 
			`sub_total` = '".$Subtotal2."', 
			`total` = '".$Total."', 
			`iva` = '".$Iva."', 
			`estado` = 'Aprobado', 
			`Peso` = '".$Peso."', 
			`modificado_por` = '".$_SESSION["UserCode"]."', 
			`fecha_modificado` = NOW() 
			WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
			
			//-- Mov_Clientes 
			$query = "SELECT * FROM mov_clientes WHERE orden_compra = '".$Ord_Compra."' 
			AND interno = '".$Interno."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);
				Anular_CarteraMovs($ClienteID, $Ord_Compra, $Interno, "Anulado Automaticamente por Ajuste de Despacho");
				
				//-- Mov_Clientes - INSERT 1
				$query = "INSERT INTO mov_clientes 
				(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
				digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, vendedor_codigo, cobrador_codigo) VALUES 
				('".$ClienteID."', 'Debito', '".$Total."', '".$Total."', '".$Interno."', '".$Ord_Compra."', '".$Remision."', 
				'".$Factura."', '".$row["fecha"]."', 'Aprobado', '".$DigitadorID."', '".$row["fecha_digitado"]."', 
				'".$row["aprobado_por"]."', '".$row["fecha_aprobado"]."', '".$row["vendedor_codigo"]."', '".$row["cobrador_codigo"]."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-2: ".mysql_error():"");
			
				if ($FormaPago == "Efectivo")
				{
					$query = "SELECT * FROM mov_clientes WHERE orden_compra = '".$Ord_Compra."' 
					AND interno = '".$Interno."' AND tipo_movimiento = 'Credito' AND caja_interno != ''";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-3: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row1 = mysql_fetch_array($result);
						//-- Caja_Final - UPDATE
						$query = "UPDATE caja_final SET saldo = saldo - '".$Total."' WHERE caja_interno = '".$row1["caja_interno"]."' 
						AND caja_recibo = '".$row1["caja_recibo"]."'";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-4: ".mysql_error():"");
						
						//--- Mov_Clientes - INSERT 2
						$query = "INSERT INTO mov_clientes 
						(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
						digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, vendedor_codigo, cobrador_codigo, caja_interno, 
						caja_recibo) VALUES ('".$ClienteID."', 'Credito', '".$Total."', '".$row1["saldo"]."', '".$Interno."', '".$Ord_Compra."', 
						'".$Remision."', '".$Factura."', '".$row1["fecha"]."', 'Aprobado', '".$DigitadorID."', '".$row1["fecha_digitado"]."', 
						'".$row1["aprobado_por"]."', '".$row1["fecha_aprobado"]."', '".$VendedorID."', '".$CobradorID."', 
						'".$row1["caja_interno"]."', '".$row1["caja_recibo"]."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-5: ".mysql_error():"");
						
						//-- Mov_Clientes - UPDATE
						$query = "UPDATE mov_clientes SET saldo = '0' WHERE orden_compra = '".$Ord_Compra."' 
						AND interno = '".$Interno."' AND tipo_movimiento = 'Debito'";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-6: ".mysql_error():"");
					}
				}
			}
			
			$final = true;
		}
		
		//--- INSERT INTO Inventario_Movs
		if ($Cantidad > 0)
		{
			if ($Fecha == "")
				AfectarInventario($Codigo, $Cantidad, $Unitario, $Interno, "", "Factura", "Venta");
			else
				AfectarInventario($Codigo, $Cantidad, $Unitario, $Interno, $Fecha, "Factura", "Venta");
		}
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Ventas_Fact_Directa']))
{
	$Data = $_GET['Ventas_Fact_Directa'];
	foreach($Data AS $Item)
	{
		$query = "SELECT id FROM fact_final ORDER BY id DESC LIMIT 1";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$row = mysql_fetch_array($result);
		$id = $row['id'] + 1;
		$len = strlen($id);
		$zero = "";
		if ($len < 8) {
			$num = 8 - $len;
			for ($a = 0; $a < $num; $a++) {
				$zero .= "0";
			}
			$Interno = "FER".$zero."".$id."";
		}
		else {
			$Interno = "FER".$id."";
		}
		
		$Remision = ($Item["Remision"] != "") ? $Item["Remision"]:$Interno;
		$Factura = ($Item["Factura"] != "") ? $Item["Factura"]:$Interno;
		
		//-- Fact Final - INSERT
		$query = "INSERT INTO fact_final 
		(interno, remision, fecha_remision, factura, orden_compra, cliente_id, forma_pago, 
		vendedor_codigo, cobrador_codigo, observaciones, total, estado, digitado_por, fecha_digitado, 
		aprobado_por, fecha_aprobado) VALUES ('".$Interno."', '".$Remision."', '".$Item["Fecha"]."', '".$Factura."', 
		'".$Interno."', '".$Item["ClienteID"]."', 'Credito', '".$Item["VendedorID"]."', '".$Item["CobradorID"]."', 
		'Generado por Facturación Directa.', '".$Item["Valor"]."', 'Aprobado', '".$_SESSION["UserCode"]."', NOW(), 
		'".$_SESSION["UserCode"]."', NOW())";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		//-- Mov_Clientes - INSERT
		$query = "INSERT INTO mov_clientes 
		(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
		digitado_por, fecha_digitado, aprobado_por, vendedor_codigo, cobrador_codigo) VALUES 
		('".$Item["ClienteID"]."', 'Debito', '".$Item["Valor"]."', '".$Item["Valor"]."', '".$Interno."', '".$Interno."', 
		'".$Remision."', '".$Factura."', NOW(), 'Aprobado', '".$_SESSION["UserCode"]."', 
		NOW(), '".$_SESSION["UserCode"]."', '".$Item["VendedorID"]."', '".$Item["CobradorID"]."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	}
	
	$data[] = array();
	echo json_encode($data);
}
else if (isset($_GET['Cartera_Aplicar_Codigos']))
{
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	$VendedorID = isset($_GET['VendedorID']) ? $_GET['VendedorID']:"";
	$CobradorID = isset($_GET['CobradorID']) ? $_GET['CobradorID']:"";
	
	$query = "UPDATE fact_final SET vendedor_codigo = '".$VendedorID."', cobrador_codigo = '".$CobradorID."' 
	WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "UPDATE mov_clientes SET vendedor_codigo = '".$VendedorID."', cobrador_codigo = '".$CobradorID."' 
	WHERE interno = '".$Interno."' AND tipo_movimiento = 'Debito'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
}
else if (isset($_GET['Cartera_AplicarPago']))
{
	$ClienteID = 0;
	$Caja_Interno = 0;
	$Caja_Recibo = 0;
	$Interno = 0;
	$Abono = 0;
	$Sobrante = 0;
	$Total = 0;
	$Apply = "";
	
	$data = $_GET['Cartera_AplicarPago'];
	
	$Estado = "Pendiente";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] == "Administrador")
			$Estado = "Aprobado";
	}
	
	foreach($data as $item)
	{
		$Interno = $item['Interno'];
		$Abono = $item['Abono'];
		if ($final == false)
		{
			$ClienteID = $item['ClienteID'];
			$Caja_Interno = $item['Caja_Interno'];
			$Caja_Recibo = $item['Caja_Recibo'];
			$Sobrante = $item['Sobrante'];
			$Total = $item['Total'];
			$final = true;
		}
		
		$query = "SELECT * FROM mov_clientes WHERE interno = '".$Interno."' AND estado = 'Aprobado' 
		AND cliente_id = '".$ClienteID."' AND tipo_movimiento = 'Debito'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			
			if ($row['valor'] < $Abono)
			{
				$ReturnData[0] = array(
					"MESSAGE" => "Error de Valores!<br />El Movimiento ".$Interno." con Valor: ".$row['valor']."<br />Es menor al Abono: ".$Abono."",
				);
				echo json_encode($ReturnData);
				die();
			}
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "Error al Validar el Valor del Movimiento.",
			);
			echo json_encode($ReturnData);
			die();
		}
		
		$Apply .= "".$row['factura'].", ";
		
		//-- Caja_Final - UPDATE
		$query = "UPDATE caja_final SET aplicado_a = '".$Apply."', saldo = '".$Sobrante."' 
		WHERE caja_interno = '".$Caja_Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		//--- Mov_Clientes - UPDATE
		$query = "UPDATE mov_clientes SET saldo = saldo - '".$Abono."' 
		WHERE interno = '".$Interno."' AND tipo_movimiento = 'Debito' 
		AND cliente_id = '".$ClienteID."' AND estado = 'Aprobado'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		
		//--- Mov_Clientes - INSERT
		$query = "INSERT INTO mov_clientes 
		(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, 
		estado, digitado_por, fecha_digitado, cobrador_codigo, caja_interno, caja_recibo) VALUES 
		('".$ClienteID."', 'Credito', '".$Abono."', '".$Total."', '".$Interno."', '".$row['orden_compra']."', 
		'".$row['remision']."', '".$row['factura']."', NOW(), '".$Estado."', '".$_SESSION["UserCode"]."', NOW(), 
		'".$row['cobrador_codigo']."', '".$Caja_Interno."', '".$Caja_Recibo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Cartera_Movimientos']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Pendiente";
	$Motivo_Anulado = isset($_GET['Motivo_Anulado']) ? $_GET['Motivo_Anulado']:0;
	$Ord_Compra = isset($_GET['Ord_Compra']) ? $_GET['Ord_Compra']:0;
	$CobradorID = isset($_GET['CobradorID']) ? $_GET['CobradorID']:0;
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:0;
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:0;
	$TipoMovimiento = isset($_GET['TipoMovimiento']) ? $_GET['TipoMovimiento']:"";
	$ID = isset($_GET['ID']) ? $_GET['ID']:"";
	
	$query = "UPDATE mov_clientes SET 
	estado = '".$Estado."', 
	motivo_anulado = '".$Motivo_Anulado."', 
	cobrador_codigo = '".$CobradorID."', 
	fecha_modificado = NOW()";
	$query .= ($Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."' ":"";
	$query .= ($Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."' ":"";
	$query .= " WHERE id = '".$ID."' AND interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."'";
	$query .= ($TipoMovimiento == "Credito") ? " AND tipo_movimiento = '".$TipoMovimiento."' ":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if ($Estado == "Anulado" && $TipoMovimiento == "Credito")
	{
		$query = "SELECT tipo_movimiento, valor, caja_interno, caja_recibo FROM mov_clientes WHERE 
		id = '".$ID."' AND orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."' 
		AND tipo_movimiento = 'Credito' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			SumarSaldo_Cartera($row["valor"], $Interno);
			SumarSaldo_Caja($row["valor"], $row["caja_interno"]);
		}
	}
	
	if ($Estado == "Anulado" && $TipoMovimiento == "Debito")
	{
		$query = "SELECT valor, caja_interno, caja_recibo FROM mov_clientes 
		WHERE orden_compra = '".$Ord_Compra."' AND interno = '".$Interno."' AND tipo_movimiento = 'Credito' 
		AND estado != 'Anulado'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				SumarSaldo_Caja($row["valor"], $row["caja_interno"]);
			}
		}
		
		$query = "UPDATE mov_clientes SET 
		estado = 'Anulado', 
		motivo_anulado = '".$Motivo_Anulado."', 
		fecha_modificado = NOW(), 
		anulado_por = '".$_SESSION["UserCode"]."' 
		WHERE interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
		Anular_Final($ClienteID, $Ord_Compra, $Interno, "Cartera", $Motivo_Anulado);
	}
}
else if (isset($_GET['CxP_Crear']))
{
	$data = $_GET['CxP_Crear'];
	$ClienteID = $data["ClienteID"];
	$Fecha = $data["Fecha"];
	$Factura = $data["Factura"];
	$Grupo = $data["Grupo"];
	$SubGrupo = $data["SubGrupo"];
	$SubGrupo2 = $data["SubGrupo2"];
	$Valor = $data["Valor"];
	$Concepto = $data["Concepto"];
	$Observaciones = $data["Observaciones"];
	
	$Interno = "";
	
	$query = "SELECT id FROM compras_final ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$row = mysql_fetch_array($result);
	$id = $row['id'] + 1;
	$len = strlen($id);
	$zero = "";
	if ($len < 6){
		$num = 6 - $len;
		for ($a = 0; $a < $num; $a++) {
			$zero .= "0";
		}
		$Interno = "COMP".$zero."".$id."";
	}
	else {
		$Interno = "COMP".$id."";
	}
	
	$query = "INSERT INTO compras_final (interno, fecha_compra, cliente_id, notas, observaciones, estado, digitado_por, fecha_digitado) VALUES 
	('".$Interno."', '".$Fecha."', '".$ClienteID."', '".$Concepto."', '".$Observaciones."', 'Aprobado', '".$_SESSION["UserCode"]."', NOW() )";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	$query = "INSERT INTO cxp_movs (compra_interno, factura, cliente_id, tipo_movimiento, valor, saldo, estado, digitado_por, 
	fecha_digitado, grupo, subgrupo, subgrupo2) VALUES 
	('".$Interno."', '".$Factura."', '".$ClienteID."', 'Compra', '".$Valor."', '".$Valor."', 'Aprobado', '".$_SESSION["UserCode"]."', 
	NOW(), '".$Grupo."', '".$SubGrupo."', '".$SubGrupo2."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	echo $Interno;
}
else if (isset($_GET['CxP_Modificar']))
{
	$data = $_GET['CxP_Modificar'];
	$Interno = isset($data["Interno"]) ? $data["Interno"]:die();
	$ClienteID = isset($data["ClienteID"]) ? $data["ClienteID"]:die();
	$Fecha = isset($data["Fecha"]) ? $data["Fecha"]:die();
	$Factura = isset($data["Factura"]) ? $data["Factura"]:die();
	$Grupo = isset($data["Grupo"]) ? $data["Grupo"]:die();
	$SubGrupo = isset($data["SubGrupo"]) ? $data["SubGrupo"]:die();
	$SubGrupo2 = isset($data["SubGrupo2"]) ? $data["SubGrupo2"]:die();
	$Valor = isset($data["Valor"]) ? $data["Valor"]:die();
	$Concepto = isset($data["Concepto"]) ? $data["Concepto"]:die();
	$Observaciones = isset($data["Observaciones"]) ? $data["Observaciones"]:die();
	
	$query = "UPDATE cxp_movs SET 
	factura = '".$Factura."', 
	cliente_id = '".$ClienteID."', 
	valor = '".$Valor."', 
	saldo = '".$Valor."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	fecha_modificado = NOW(), 
	grupo = '".$Grupo."', 
	subgrupo = '".$SubGrupo."', 
	subgrupo2 = '".$SubGrupo2."' 
	WHERE compra_interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "UPDATE compras_final SET 
	notas = '".$Concepto."', 
	observaciones = '".$Observaciones."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	fecha_modificado = NOW() 
	WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	echo "OK";
}
else if (isset($_GET['CxP_Movimientos']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Pendiente";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:0;
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:0;
	$Entrada = isset($_GET['Entrada']) ? $_GET['Entrada']:0;
	$TipoMovimiento = isset($_GET['TipoMovimiento']) ? $_GET['TipoMovimiento']:"";
	$ID = isset($_GET['ID']) ? $_GET['ID']:"";
	
	$query = "UPDATE cxp_movs SET estado = '".$Estado."'";
	$query .= ($Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."',  fecha_anulado = NOW()":"";
	$query .= ($Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."',  fecha_aprobado = NOW()":"";
	$query .= " WHERE id = '".$ID."' AND compra_interno = '".$Interno."'";
	$query .= ($TipoMovimiento == "Abono a Compra") ? " AND tipo_movimiento = '".$TipoMovimiento."' ":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if ($TipoMovimiento == "Compra")
	{
		$query = "UPDATE maquinaria_final SET estado = '".$Estado."'";
		$query .= ($Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
		$query .= ($Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
		$query .= " WHERE ord_reparacion = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		
		if ($Estado == "Anulado")
		{
			$query = "UPDATE compras_final SET estado = 'Anulado'";
			$query .= ($Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
			$query .= ($Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
			$query .= " WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
			Del_InventoryMovs($Interno);
		}
	}
	
	if ($Estado == "Anulado" && $TipoMovimiento == "Abono a Compra")
	{
		$query = "SELECT valor, caja_interno, caja_recibo FROM cxp_movs WHERE id = '".$ID."' 
		AND compra_interno = '".$Interno."' AND tipo_movimiento = 'Abono a Compra' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			SumarSaldo_CxP($row["valor"], $Interno);
			Anular_Caja_Recibo($row["caja_interno"], $row["caja_recibo"]);// Necesario para eliminar el recibo de caja de salida
			
			// Es una salida de la empresa... no se necesita sumar saldo.
			//SumarSaldo_Caja($row["valor"], $row["caja_interno"]);
		}
	}
	
	if ($Estado == "Anulado" && $TipoMovimiento == "Compra")
	{
		$query = "UPDATE cxp_movs SET 
		estado = 'Anulado', 
		anulado_por = '".$_SESSION["UserCode"]."', 
		fecha_anulado = NOW() 
		WHERE compra_interno = '".$Interno."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$query = "SELECT valor, caja_interno, caja_recibo FROM cxp_movs WHERE id = '".$ID."' 
		AND compra_interno = '".$Interno."' AND tipo_movimiento = 'Abono a Compra' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			Anular_Caja_Recibo($row["caja_interno"], $row["caja_recibo"]);
		}
		
		Anular_Final($ClienteID, $Entrada, $Interno, "CxP", "Anulado Automaticamente por CxP -> Movimientos");
	}
}
else if (isset($_GET['CxP_AplicarPago']))
{
	$Today = date('Y-m-d');
	$ClienteID = 0;
	$Caja_Interno = "";
	$Compra_Interno = "";
	$Compra_Entrada = "";
	$Abono = 0;
	$Total = 0;
	$Forma_Pago = "";
	$Fuente = "";
	$Tipo = "";
	$Consignacion = "";
	
	$data = $_GET['CxP_AplicarPago'];
	//print_r($data);
	//die();
	
	$Estado = "Pendiente";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] == "Administrador")
			$Estado = "Aprobado";
	}
	
	$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$row = mysql_fetch_array($result);
	$id = $row['id'] + 1;
	$len = strlen($id);
	$zero = "";
	if ($len < 7){
		$num = 7 - $len;
		for ($a = 0; $a < $num; $a++) {
			$zero .= "0";
		}
		$Caja_Interno = "MCJ".$zero."".$id."";
	}
	else {
		$Caja_Interno = "MCJ".$id."";
	}
	
	$final = false;
	foreach($data as $item)
	{
		$Compra_Interno = $item['Interno'];
		$Compra_Entrada = $item['Entrada'];
		$Abono = $item['Abono'];
		if ($final == false)
		{
			$ClienteID = $item['ClienteID'];
			$Total = $item['Total'];
			$Forma_Pago = $item['Forma_Pago'];
			$Fuente = $item['Fuente'];
			$Tipo = $item['Tipo'];
			$Consignacion = $item['Consignacion'];
			
			//-- Caja Final
			$query = "INSERT INTO caja_final 
			(fecha, caja_interno, caja_recibo, cxp, categoria, grupo, subgrupo, subgrupo2, cliente_id, ";
			$query .= $Forma_Pago == "Efectivo" ? "efectivo, ":"";
			$query .= ($Forma_Pago == "Bancos" && $Tipo == "Cheque") ? "cheque, ":"";
			$query .= ($Forma_Pago == "Bancos" && $Tipo == "Transferencia") ? "consignacion, ":"";
			$query .= "total, digitado_por, fecha_digitado, estado) VALUES 
			('".$Today."', '".$Caja_Interno."', '".$Caja_Interno."', '".$Compra_Interno."', 'Egresos', 
			'Proveedor', 'Pago factura', 'N/A.', '".$ClienteID."', ";
			$query .= $Forma_Pago == "Efectivo" ? "'".$Total."', ":"";
			$query .= ($Forma_Pago == "Bancos" && $Tipo == "Cheque") ? "'".$Total."', ":"";
			$query .= ($Forma_Pago == "Bancos" && $Tipo == "Transferencia") ? "'".$Total."', ":"";
			$query .= "'".$Total."', '".$_SESSION["UserCode"]."', NOW(), '".$Estado."')";
			//echo $query;
			//die();
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			
			//-- Bancos
			if ($Forma_Pago == "Bancos" && $Tipo != "Cheque")
			{
				$query = "INSERT INTO bancos 
				(cliente_id, caja_interno, caja_recibo, fecha, tipo, numero, valor, banco, estado, tipo_mov, digitado_por) VALUES 
				('".$ClienteID."', '".$Caja_Interno."', '".$Caja_Interno."', NOW(), '".$Tipo."', '".$Consignacion."', 
				'".$Total."', '".$Fuente."', '".$Estado."', 'Salida', '".$_SESSION["UserCode"]."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			}
			else if ($Forma_Pago == "Bancos" && $Tipo == "Cheque")
			{
				$query = "INSERT INTO cheques 
				(cliente_id, caja_interno, caja_recibo, fecha, cheque, valor, banco, estado, estado_cheque, digitado_por) VALUES 
				('".$ClienteID."', '".$Caja_Interno."', '".$Caja_Interno."', NOW(), '".$Consignacion."', 
				'".$Total."', '".$Fuente."', '".$Estado."', 'Generado', '".$_SESSION["UserCode"]."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			}
			
			$final = true;
		}
		//--- CxP_Movs - UPDATE 
		$query = "UPDATE cxp_movs SET saldo = saldo - '".$Abono."' 
		WHERE compra_interno = '".$Compra_Interno."' AND tipo_movimiento = 'Compra' 
		AND cliente_id = '".$ClienteID."' AND estado = 'Aprobado'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		
		//--- CxP_Movs - INSERT 
		$query = "INSERT INTO cxp_movs 
		(compra_interno, compra_entrada, cliente_id, tipo_movimiento, valor, saldo, estado, digitado_por, fecha_digitado, 
		origen1, origen2, origen3, origen_documento, caja_interno, caja_recibo, grupo, subgrupo, subgrupo2) VALUES ('".$Compra_Interno."', 
		'".$Compra_Entrada."', '".$ClienteID."', 'Abono a Compra', '".$Abono."', '".$Abono."', '".$Estado."', '".$_SESSION["UserCode"]."', 
		NOW(), '".$Forma_Pago."', '".$Fuente."', '".$Tipo."', '".$Consignacion."', '".$Caja_Interno."', '".$Caja_Interno."', 'Proveedor', 
		'Pago factura', 'N/A.')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
	}
	$newdata[] = array(
		"Caja_Interno" => $Caja_Interno,
	);
	echo json_encode($newdata);
}
else if (isset($_GET['Caja_Crear']))
{
	$data = $_GET['Caja_Crear'];
	$data1 = isset($_GET['Grid1']) ? $_GET['Grid1']:false;
	$data2 = isset($_GET['Grid2']) ? $_GET['Grid2']:false;

	$ClienteID = 0;
	$Fecha = 0;
	$ReciboCaja = 0;
	$CxP = "";
	$Interno = "";
	$Tipo = 0;
	$Grupo = 0;
	$SubGrupo = 0;
	$SubGrupo2 = 0;
	$Efectivo = 0;
	$Cheque = 0;
	$Consignacion = 0;
	$ReteIva = 0;
	$ReteIca = 0;
	$ReteFuente = 0;
	$Desctarjeta = 0;
	$Descuento = 0;
	$ConceptoDcto = 0;
	$Total = 0;
	$Observaciones = 0;
	$CajaInterno = 0;
	//-- Grid1
	$Tipo1 = 0;
	$Valor1 = 0;
	$Num1 = 0;
	$Banco1 = 0;
	//-- Grid2
	$ChequeNum = 0;
	$Valor2 = 0;
	$Banco2 = 0;
	$Num2 = 0;
	$EstadoFecha = 0;
	$FechaCheque = 0;
	
	foreach($data as $item)
	{
		$ClienteID = $item['ClienteID'];
		$Fecha = $item['Fecha'];
		$ReciboCaja = $item['ReciboCaja'];
		$CxP_Factura = $item['CxP_Factura'];
		$CxP_Interno = $item['CxP_Interno'];
		$Tipo = $item['Tipo'];
		$Grupo = $item['Grupo'];
		$SubGrupo = $item['SubGrupo'];
		$SubGrupo2 = $item['SubGrupo2'];
		$Efectivo = $item['Efectivo'];
		$Cheque = $item['Cheque'];
		$Consignacion = $item['Consignacion'];
		$ReteIva = $item['ReteIva'];
		$ReteIca = $item['ReteIca'];
		$ReteFuente = $item['ReteFuente'];
		$Desctarjeta = $item['Desctarjeta'];
		$Descuento = $item['Descuento'];
		$ConceptoDcto =  $item['ConceptoDcto'];
		$Total = $item['Total'];
		$Observaciones = $item['Observaciones'];
	}
	
	$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$row = mysql_fetch_array($result);
	$id = $row['id'] + 1;
	$len = strlen($id);
	$zero = "";
	if ($len < 7){
		$num = 7 - $len;
		for ($a = 0; $a < $num; $a++) {
			$zero .= "0";
		}
		$CajaInterno = "MCJ".$zero."".$id."";
	}
	else {
		$CajaInterno = "MCJ".$id."";
	}
	
	if ($ReciboCaja == "")
		$ReciboCaja = $CajaInterno;
	
	//--------------------------------------------------------------------------------//
	//-------------------------------- INSERT COMMAND --------------------------------//
	//--------------------------------------------------------------------------------//
	if ($CxP_Interno != "")
	{
		//-- Caja Final
		$query = "INSERT INTO caja_final 
		(`fecha`, `caja_interno`, `caja_recibo`, `cxp`, `categoria`, `grupo`, `subgrupo`, `subgrupo2`, 
		`cliente_id`, `observaciones`, `efectivo`, `cheque`, `consignacion`, `total`, `digitado_por`, 
		`fecha_digitado`, `rete_iva`, `rete_ica`, `rete_fuente`, `descuento`, `concepto_dcto`, `estado`) VALUES 
		('".$Fecha."', '".$CajaInterno."', '".$ReciboCaja."', '".$CxP_Interno."', '".$Tipo."', '".$Grupo."', '".$SubGrupo."', '".$SubGrupo2."',
		'".$ClienteID."', '".$Observaciones."', '".$Efectivo."', '".$Cheque."', '".$Consignacion."', '".$Total."', '".$_SESSION["UserCode"]."', 
		NOW(), '".$ReteIva."', '".$ReteIca."', '".$ReteFuente."', '".$Descuento."', '".$ConceptoDcto."', 'Pendiente')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		$query = "SELECT * FROM cxp_movs WHERE factura = '".$CxP_Factura."' AND compra_interno = '".$CxP_Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			//--- CxP_Movs - UPDATE
			$query = "UPDATE cxp_movs SET saldo = saldo - '".$Total."' 
			WHERE compra_interno = '".$CxP_Interno."' AND tipo_movimiento = 'Compra' AND cliente_id = '".$ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
			//--- CxP_Movs - INSERT
			$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, 
			tipo_movimiento, valor, saldo, estado, digitado_por, fecha_digitado, caja_interno, caja_recibo, grupo, 
			subgrupo, subgrupo2) VALUES ('".$CxP_Interno."', '".$row["compra_entrada"]."', '".$CxP_Factura."', '".$ClienteID."', 
			'Abono a Compra', '".$Total."', '".$Total."', 'Pendiente', '".$_SESSION["UserCode"]."', NOW(), 
			'".$CajaInterno."', '".$ReciboCaja."', '".$Grupo."', '".$SubGrupo."', '".$SubGrupo2."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
		}
	}
	else
	{
		if (($Tipo == "Egresos" && $Grupo == "Nómina") && ($SubGrupo == "Anticipo" || $SubGrupo == "Préstamo"))
		{
			// Crear Prestamo y sus movimientos...
			$query = "SELECT id FROM nom_prestamos ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 4)
			{
				$num = 4 - $len;
				for ($a = 0; $a < $num; $a++)
					$zero .= "0";
				$Interno = "PR".$zero."".$id."";
			}
			else
				$Interno = "PR".$id."";
			
			$ReciboCaja = $Interno;
			$Nombre = "Cuota_1_".$Interno;
			$Day = date("d", strtotime($Fecha));
			$Month = date("n", strtotime($Fecha));
			$Year = date("Y", strtotime($Fecha));
			
			if ($Day <= 15)
			{
				// to 14
				$FechaPago = $Year."-".$Month."-14";
			}
			else
			{
				// to 27
				$FechaPago = $Year."-".$Month."-27";
			}
			
			$query = "INSERT INTO nom_prestamos (interno, fecha, beneficiario_id, acreedor_id, tipo_mov, valor, 
			cuotas, valor_cuotas, caja, forma_pago, observacion, estado, digitado_por, fecha_digitado) VALUES 
			('".$Interno."', '".$Fecha."', '".$ClienteID."', '".$MAIN_ID."', '".$SubGrupo."', '".$Total."', '1', 
			'".$Total."', '".$_SESSION["UserCode"]."', 'Efectivo', '".$Observaciones."', 'Pendiente', '".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
			
			$query = "INSERT INTO nom_prestamos_movs (interno, nombre, cuota, fecha, valor, estado) VALUES 
			('".$Interno."', '".$Nombre."', '1', '".$FechaPago."', '".$Total."', 'Pendiente')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
			
			$query = "INSERT INTO mov_clientes ( cliente_id, tipo_movimiento, valor, saldo, interno, 
			orden_compra, remision, factura, fecha, estado, digitado_por, fecha_digitado, caja_interno, 
			caja_recibo ) VALUES ('".$ClienteID."', 'Debito', '".$Total."', '".$Total."', 
			'".$Nombre."', '".$Interno."', '".$Nombre."', '".$Nombre."', '".$FechaPago."', 
			'Pendiente', '".$_SESSION["UserCode"]."', NOW(), '".$CajaInterno."', '".$ReciboCaja."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
		}
		
		//-- Caja Final
		$query = "INSERT INTO caja_final 
		(`fecha`, `caja_interno`, `caja_recibo`, `cxp`, `categoria`, `grupo`, `subgrupo`, `subgrupo2`, 
		`cliente_id`, `observaciones`, `efectivo`, `cheque`, `consignacion`, `total`, `saldo`, `digitado_por`, 
		`fecha_digitado`, `rete_iva`, `rete_ica`, `rete_fuente`, `descuento`, `concepto_dcto`, `estado`, `tarjeta`) VALUES 
		('".$Fecha."', '".$CajaInterno."', '".$ReciboCaja."', '".$CxP."', '".$Tipo."', '".$Grupo."', '".$SubGrupo."', '".$SubGrupo2."',
		'".$ClienteID."', '".$Observaciones."', '".$Efectivo."', '".$Cheque."', '".$Consignacion."', '".$Total."', '".$Total."', 
		'".$_SESSION["UserCode"]."', NOW(), '".$ReteIva."', '".$ReteIca."', '".$ReteFuente."', '".$Descuento."', '".$ConceptoDcto."', 'Pendiente', '".$Desctarjeta."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	}
	
	//-- Bancos - Consignaciones, Transferencias y Tarjetas
	if ($data1) {
		foreach($data1 as $item)
		{
			$Tipo1 = $item['Tipo1'];
			$Valor1 = $item['Valor1'];
			$Num1 = $item['Num1'];
			$Banco1 = $item['Banco1'];
			
			$query = "INSERT INTO bancos 
			(`cliente_id`, `caja_interno`, `caja_recibo`, `fecha`, `tipo`, `numero`, `valor`, `banco`, `estado`, `digitado_por`) VALUES 
			('".$ClienteID."', '".$CajaInterno."', '".$ReciboCaja."', NOW(), '".$Tipo1."', '".$Num1."', '".$Valor1."', '".$Banco1."', 
			'Pendiente', '".$_SESSION["UserCode"]."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		}
	}
	//-- Cheques
	if ($data2) {
		foreach($data2 as $item)
		{
			$ChequeNum = $item['ChequeNum'];
			$Valor2 = $item['Valor2'];
			$Banco2 = $item['Banco2'];
			$Num2 = $item['Num2'];
			$EstadoFecha = $item['EstadoFecha'];
			$FechaCheque = $item['FechaCheque'];
			
			$query = "INSERT INTO cheques 
			(`cliente_id`, `caja_interno`, `caja_recibo`, `fecha`, `cheque`, `valor`, `banco`, `cuenta`, `estado`, `estado_cheque`, 
			`fecha_cheque`, `digitado_por`) VALUES ('".$ClienteID."', '".$CajaInterno."', '".$ReciboCaja."', NOW(), '".$ChequeNum."', 
			'".$Valor2."', '".$Banco2."', '".$Num2."', 'Pendiente', '".$EstadoFecha."', '".$FechaCheque."', '".$_SESSION["UserCode"]."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		}
	}
	
	echo $CajaInterno;
}
else if (isset($_GET['Caja_Crear2']))
{
	$ClienteID = 0;
	$CajaInterno = 0;
	$ReciboCaja = 0;
	$Interno = 0;
	$Abono = 0;
	$Sobrante = 0;
	$Total = 0;
	$Apply = "";
	
	$data = $_GET['Caja_Crear2'];
	foreach($data as $item)
	{
		$Interno = $item['Interno'];
		$Ord_Compra = $item['Ord_Compra'];
		$Remision = $item['Remision'];
		$Factura = $item['Factura'];
		$Valor = $item['Valor'];
		$Abono = $item['Abono'];
		$Saldo = $item['Saldo'];
		$Saldo_Nuevo = $item['Saldo_Nuevo'];
		if ($final == false) {
			$ClienteID = $item['ClienteID'];
			$CajaInterno = $item['CajaInterno'];
			$ReciboCaja = $item['ReciboCaja'] == "" ? $item['CajaInterno']:$item['ReciboCaja'];
			$Sobrante = $item['Sobrante'];
			$Total = $item['Total'];
			$final = true;
		}
		
		$query = "SELECT * FROM mov_clientes WHERE interno = '".$Interno."' AND cliente_id = '".$ClienteID."' AND tipo_movimiento = 'Debito'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			
			if ($row['valor'] < $Abono) {
				echo "Ocurrió un Error de Valores y los Pagos no se procesaron...";
				//echo "<br/>";
				//echo $row['valor'];
				//echo "<br/>";
				//echo $row['interno'];
				die();
			}
			
			$Apply .= "".$row['factura'].", ";
			
			//-- Aplicar Log
			$query = "INSERT INTO cartera_aplicar_log (cliente_id, interno, orden_compra, remision, factura, caja_interno, caja_recibo, 
			valor, saldo, abono, saldo_nuevo) VALUES ('".$ClienteID."', '".$Interno."', '".$row['orden_compra']."', '".$row['remision']."', 
			'".$row['factura']."', '".$CajaInterno."', '".$ReciboCaja."', '".$Valor."', '".$Saldo."', '".$Abono."', '".$Saldo_Nuevo."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			
			//-- Caja_Final - UPDATE
			$query = "UPDATE caja_final SET aplicado_a = '".$Apply."', saldo = '".$Sobrante."' 
			WHERE caja_interno = '".$CajaInterno."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			
			//--- Mov_Clientes - UPDATE
			$query = "UPDATE mov_clientes SET saldo = saldo - '".$Abono."' 
			WHERE interno = '".$Interno."' AND tipo_movimiento = 'Debito' AND cliente_id = '".$ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			
			//--- Mov_Clientes - INSERT
			$query = "INSERT INTO mov_clientes 
			(cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, 
			estado, digitado_por, fecha_digitado, cobrador_codigo, caja_interno, caja_recibo) VALUES 
			('".$ClienteID."', 'Credito', '".$Abono."', '".$Total."', '".$Interno."', '".$row['orden_compra']."', '".$row['remision']."', 
			'".$row['factura']."', NOW(), 'Pendiente', '".$_SESSION["UserCode"]."', NOW(), '".$row['cobrador_codigo']."', 
			'".$CajaInterno."', '".$ReciboCaja."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
		}
	}
	echo $Apply;
}
else if (isset($_GET['Caja_Modificar']))
{
	$data = $_GET['Caja_Modificar'];
	$data1 = isset($_GET['Grid1']) ? $_GET['Grid1']:false;
	$data2 = isset($_GET['Grid2']) ? $_GET['Grid2']:false;

	$ClienteID = 0;
	$Fecha = 0;
	$CajaInterno = 0;
	$ReciboCaja = 0;
	$CxP_Factura = "";
	$CxP_Interno = "";
	$Tipo = 0;
	$Grupo = 0;
	$SubGrupo = 0;
	$SubGrupo2 = 0;
	$Efectivo = 0;
	$Cheque = 0;
	$Consignacion = 0;
	$ReteIva = 0;
	$ReteIca = 0;
	$ReteFuente = 0;
	$Descuento = 0;
	$ConceptoDcto = 0;
	$Total = 0;
	$Observaciones = 0;
	//-- Grid1
	$Tipo1 = 0;
	$Valor1 = 0;
	$Num1 = 0;
	$Banco1 = 0;
	//-- Grid2
	$ChequeNum = 0;
	$Valor2 = 0;
	$Banco2 = 0;
	$Num2 = 0;
	$EstadoFecha = 0;
	$FechaCheque = 0;
	
	//-------- CHECK CHANGES
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		if ($data_session[0]["Lvl"] != "Administrador")
		{
			$query = "SELECT id FROM caja_final WHERE caja_interno = '".$data[0]['CajaInterno']."' AND estado = 'Pendiente'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) <= 0)
			{
				$ReturnData[0] = array(
					"MESSAGE" => "CHANGED",
				);
				echo json_encode($ReturnData);
				die();
			}
		}
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	foreach($data as $item)
	{
		$ClienteID = $item['ClienteID'];
		$Fecha = $item['Fecha'];
		$CajaInterno = $item['CajaInterno'];
		$ReciboCaja = $item['ReciboCaja'];
		$CxP_Factura = $item['CxP_Factura'];
		$CxP_Interno = $item['CxP_Interno'];
		$Tipo = $item['Tipo'];
		$Grupo = $item['Grupo'];
		$SubGrupo = $item['SubGrupo'];
		$SubGrupo2 = $item['SubGrupo2'];
		$Efectivo = $item['Efectivo'];
		$Cheque = $item['Cheque'];
		$Consignacion = $item['Consignacion'];
		$ReteIva = $item['ReteIva'];
		$ReteIca = $item['ReteIca'];
		$ReteFuente = $item['ReteFuente'];
		$Descuento = $item['Descuento'];
		$ConceptoDcto =  $item['ConceptoDcto'];
		$Total = $item['Total'];
		$Observaciones = $item['Observaciones'];
	}
	
	$query = "SELECT digitado_por FROM caja_final WHERE caja_interno = '".$data[0]['CajaInterno']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$DigitadorID = $row["digitado_por"];
	}
	else
		$DigitadorID = "";
	
	// Cartera
	$query = "SELECT DISTINCT interno FROM mov_clientes WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."' 
	AND tipo_movimiento = 'Credito' AND estado != 'Anulado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			//SELECT DISTINCT interno FROM mov_clientes WHERE caja_interno = 'MCJ0030837' AND caja_recibo = 'prueba2134' AND tipo_movimiento = 'Credito' AND estado != 'Anulado'
			//Actualizar
			$query = "UPDATE mov_clientes SET saldo = valor WHERE interno = '".$row["interno"]."' AND tipo_movimiento = 'Debito'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			//Borrar
			$query = "DELETE FROM mov_clientes WHERE caja_interno = '".$CajaInterno."' 
			AND caja_recibo = '".$ReciboCaja."' AND tipo_movimiento = 'Credito'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
			//Regresar Abonos
			$query = "SELECT * FROM mov_clientes WHERE interno = '".$row["interno"]."' 
			AND tipo_movimiento = 'Credito' AND estado != 'Anulado'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
			if (mysql_num_rows($result1) > 0)
			{
				while($row1 = mysql_fetch_array($result1))
				{
					RestarSaldo_Cartera($row1["valor"], $row1["interno"]);
				}
			}
		}
	}
	
	/*$query = "SELECT * FROM mov_clientes WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."' 
	AND tipo_movimiento = 'Credito' AND estado != 'Anulado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			//Regresar Abonos
			SumarSaldo_Cartera($row["valor"], $row["interno"]);
		}
		//Borrar
		$query = "DELETE FROM mov_clientes WHERE caja_interno = '".$CajaInterno."' 
		AND caja_recibo = '".$ReciboCaja."' AND tipo_movimiento = 'Credito'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	}*/
	
	$query = "SELECT * FROM cxp_movs WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."' 
	AND tipo_movimiento = 'Abono a Compra' AND estado != 'Anulado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			//Regresar Abonos
			SumarSaldo_CxP($row["valor"], $row["compra_interno"]);
		}
		//Borrar
		$query = "DELETE FROM cxp_movs WHERE caja_interno = '".$CajaInterno."' 
		AND caja_recibo = '".$ReciboCaja."' AND tipo_movimiento = 'Abono a Compra'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
	}
	
	$query = "SELECT * FROM bancos WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-6: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$Old_FechaBanco = $row["fecha"];
		
		// Borrar Consignaciones
		$query = "DELETE FROM bancos WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-7: ".mysql_error():"");
	}
	
	$query = "SELECT * FROM cheques WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-8: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$Old_FechaCheque = $row["fecha"];
		
		// Borrar Cheques
		$query = "DELETE FROM cheques WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-9: ".mysql_error():"");
	}
	
	//--------------------------------------------------------------------------------//
	//----------------------------- UPDATE/INSERT COMMAND ----------------------------//
	//--------------------------------------------------------------------------------//
	if ($CxP_Interno != "")
	{
		//-- Caja Final
		$query = "UPDATE caja_final SET 
		fecha = '".$Fecha."', 
		cxp = '".$CxP_Interno."', 
		categoria = '".$Tipo."', 
		grupo = '".$Grupo."', 
		subgrupo = '".$SubGrupo."', 
		subgrupo2 = '".$SubGrupo2."', 
		cliente_id = '".$ClienteID."', 
		observaciones = '".$Observaciones."', 
		efectivo = '".$Efectivo."', 
		cheque = '".$Cheque."', 
		consignacion = '".$Consignacion."', 
		total = '".$Total."', 
		modificado_por = '".$_SESSION["UserCode"]."', 
		fecha_modificado = NOW(), 
		rete_iva = '".$ReteIva."', 
		rete_ica = '".$ReteIca."', 
		rete_fuente = '".$ReteFuente."', 
		descuento = '".$Descuento."', 
		concepto_dcto = '".$ConceptoDcto."', 
		estado = 'Pendiente' WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		$query = "SELECT * FROM cxp_movs WHERE factura = '".$CxP_Factura."' AND compra_interno = '".$CxP_Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			//--- CxP_Movs - UPDATE
			$query = "UPDATE cxp_movs SET saldo = saldo - '".$Total."' 
			WHERE compra_interno = '".$CxP_Interno."' AND tipo_movimiento = 'Compra' AND cliente_id = '".$ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
			//--- CxP_Movs - INSERT
			$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, 
			tipo_movimiento, valor, saldo, estado, digitado_por, fecha_digitado, caja_interno, caja_recibo, grupo, 
			subgrupo, subgrupo2) VALUES ('".$CxP_Interno."', '".$row["compra_entrada"]."', '".$CxP_Factura."', '".$ClienteID."', 
			'Abono a Compra', '".$Total."', '".$Total."', 'Pendiente', '".$DigitadorID."', NOW(), 
			'".$CajaInterno."', '".$ReciboCaja."', '".$Grupo."', '".$SubGrupo."', '".$SubGrupo2."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
		}
	}
	else
	{
		//-- Caja Final
		$query = "UPDATE caja_final SET 
		fecha = '".$Fecha."', 
		cxp = '".$CxP_Interno."', 
		categoria = '".$Tipo."', 
		grupo = '".$Grupo."', 
		subgrupo = '".$SubGrupo."', 
		subgrupo2 = '".$SubGrupo2."', 
		cliente_id = '".$ClienteID."', 
		observaciones = '".$Observaciones."', 
		efectivo = '".$Efectivo."', 
		cheque = '".$Cheque."', 
		consignacion = '".$Consignacion."', 
		total = '".$Total."', 
		saldo = '".$Total."', 
		modificado_por = '".$_SESSION["UserCode"]."', 
		fecha_modificado = NOW(), 
		rete_iva = '".$ReteIva."', 
		rete_ica = '".$ReteIca."', 
		rete_fuente = '".$ReteFuente."', 
		descuento = '".$Descuento."', 
		concepto_dcto = '".$ConceptoDcto."', 
		estado = 'Pendiente' WHERE caja_interno = '".$CajaInterno."' AND caja_recibo = '".$ReciboCaja."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	}
	
	//-- Bancos - Consignaciones, Transferencias y Tarjetas
	if ($data1)
	{
		foreach($data1 as $item)
		{
			$Tipo1 = $item['Tipo1'];
			$Valor1 = $item['Valor1'];
			$Num1 = $item['Num1'];
			$Banco1 = $item['Banco1'];
			
			$query = "INSERT INTO bancos 
			(`cliente_id`, `caja_interno`, `caja_recibo`, `fecha`, `tipo`, `numero`, `valor`, `banco`, `estado`, `digitado_por`) VALUES 
			('".$ClienteID."', '".$CajaInterno."', '".$ReciboCaja."', '".$Old_FechaBanco."', '".$Tipo1."', '".$Num1."', '".$Valor1."', 
			'".$Banco1."', 'Pendiente', '".$DigitadorID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		}
	}
	//-- Cheques
	if ($data2)
	{
		foreach($data2 as $item)
		{
			$ChequeNum = $item['ChequeNum'];
			$Valor2 = $item['Valor2'];
			$Banco2 = $item['Banco2'];
			$Num2 = $item['Num2'];
			$EstadoFecha = $item['EstadoFecha'];
			$FechaCheque = $item['FechaCheque'];
			
			if ($EstadoFecha != "Al Dia" && $EstadoFecha != "PostFechado")
				$EstadoFecha = "Al Dia";
			
			$query = "INSERT INTO cheques 
			(`cliente_id`, `caja_interno`, `caja_recibo`, `fecha`, `cheque`, `valor`, `banco`, `cuenta`, `estado`, `estado_cheque`, 
			`fecha_cheque`, `digitado_por`) VALUES ('".$ClienteID."', '".$CajaInterno."', '".$ReciboCaja."', '".$Old_FechaCheque."', 
			'".$ChequeNum."', '".$Valor2."', '".$Banco2."', '".$Num2."', 'Pendiente', '".$EstadoFecha."', '".$FechaCheque."', 
			'".$DigitadorID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		}
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Caja_Aprobar']))
{
	$Old_Estado = isset($_GET['Old_Estado']) ? $_GET['Old_Estado']:"Pendiente";
	$New_Estado = isset($_GET['New_Estado']) ? $_GET['New_Estado']:"Pendiente";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:0;
	$Caja_Interno = isset($_GET['Caja_Interno']) ? $_GET['Caja_Interno']:0;
	$Caja_Recibo = isset($_GET['Caja_Recibo']) ? $_GET['Caja_Recibo']:0;
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM caja_final WHERE caja_interno = '".$Caja_Interno."' AND estado = '".$Old_Estado."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
	}
	else
	{
		$query = "UPDATE caja_final SET estado = '".$New_Estado."'";
		$query .= ($New_Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
		$query .= ($New_Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
		$query .= " WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."' AND cliente_id = '".$ClienteID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
		
		$query = "UPDATE bancos SET estado = '".$New_Estado."' 
		WHERE caja_interno = '".$Caja_Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		
		$query = "UPDATE cheques SET estado = '".$New_Estado."' 
		WHERE caja_interno = '".$Caja_Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		
		$query = "SELECT * FROM mov_clientes WHERE caja_interno = '".$Caja_Interno."' AND cliente_id = '".$ClienteID."'";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
		
		$query = "SELECT * FROM cxp_movs WHERE caja_interno = '".$Caja_Interno."' AND cliente_id = '".$ClienteID."'";
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-1: ".mysql_error():"");
		
		if (mysql_num_rows($result1) > 0)
		{
			/*$query = "UPDATE mov_clientes SET estado = '".$New_Estado."'";
			$query .= ($New_Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
			$query .= ($New_Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
			$query .= " WHERE caja_interno = '".$Caja_Interno."' AND cliente_id = '".$ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");*/
			
			if ($New_Estado == "Anulado")
			{
				$query = "SELECT * FROM mov_clientes WHERE caja_interno = '".$Caja_Interno."' 
				AND tipo_movimiento = 'Credito' AND estado != 'Anulado'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while($row = mysql_fetch_array($result))
					{
						//Regresar Abonos
						SumarSaldo_Cartera($row["valor"], $row["interno"]);
					}
					//Borrar
					$query = "DELETE FROM mov_clientes WHERE caja_interno = '".$Caja_Interno."' AND tipo_movimiento = 'Credito'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-4: ".mysql_error():"");
				}
			}
			else if ($New_Estado == "Aprobado")
			{
				$query = "UPDATE mov_clientes SET estado = 'Aprobado', aprobado_por = '".$_SESSION["UserCode"]."', 
				fecha_aprobado = NOW() WHERE caja_interno = '".$Caja_Interno."' AND cliente_id = '".$ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-5: ".mysql_error():"");
			}
		}
		
		if (mysql_num_rows($result2) > 0)
		{
			/*$query = "UPDATE cxp_movs SET estado = '".$New_Estado."'";
			$query .= ($New_Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
			$query .= ($New_Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
			$query .= " WHERE caja_interno = '".$Caja_Interno."' AND cliente_id = '".$ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-2: ".mysql_error():"");*/
			
			if ($New_Estado == "Anulado")
			{
				$query = "SELECT * FROM cxp_movs WHERE caja_interno = '".$Caja_Interno."' 
				AND tipo_movimiento = 'Abono a Compra' AND estado != 'Anulado'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-2: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while($row = mysql_fetch_array($result))
					{
						//Regresar Abonos
						SumarSaldo_CxP($row["valor"], $row["compra_interno"]);
					}
					//Borrar
					$query = "DELETE FROM cxp_movs WHERE caja_interno = '".$Caja_Interno."' 
					AND tipo_movimiento = 'Abono a Compra'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-3: ".mysql_error():"");
				}
			}
			else if ($New_Estado == "Aprobado")
			{
				$query = "UPDATE cxp_movs SET estado = 'Aprobado', aprobado_por = '".$_SESSION["UserCode"]."', 
				fecha_aprobado = NOW() WHERE caja_interno = '".$Caja_Interno."' AND cliente_id = '".$ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-5: ".mysql_error():"");
			}
		}
		
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_GET['Caja_Cheques']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Al Dia";
	$Caja_Interno = isset($_GET['Caja_Interno']) ? $_GET['Caja_Interno']:0;
	$Caja_Recibo = isset($_GET['Caja_Recibo']) ? $_GET['Caja_Recibo']:0;
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:0;
	$Banco_Num = isset($_GET['Banco_Num']) ? $_GET['Banco_Num']:00;
	$Banco = isset($_GET['Banco']) ? $_GET['Banco']:"";
	$Fecha = isset($_GET['Fecha']) ? $_GET['Fecha']:"0000-00-00";
	$Cheque = isset($_GET['Cheque']) ? $_GET['Cheque']:0;
	$Valor = isset($_GET['Valor']) ? $_GET['Valor']:0;
	$NewInterno = "";
	
	switch ($Estado)
	{
		case "Al Dia":
			// BUSCAR BANCO
			$query = "SELECT * FROM bancos WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."' 
			AND cliente_id = '".$ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				// DELETE
				$query = "DELETE FROM bancos WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."' 
				AND cliente_id = '".$ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
			/*
			// BUSCAR MOV_CLIENTE
			$query = "SELECT * FROM mov_clientes WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."' 
			AND cliente_id = '".$ClienteID."' AND tipo_movimiento = 'Debito'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);
				$Interno = $row["interno"];
				// Buscar Facturas
				$query = "SELECT DISTINCT * FROM mov_clientes WHERE tipo_movimiento = 'Credito' AND interno = '".$Interno."' 
				AND cliente_id = '".$ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
				if (mysql_num_rows($result)>0)
				{
					while ($row = mysql_fetch_array($result))
					{
						// Buscar Pago
						$query = "SELECT * FROM mov_clientes WHERE tipo_movimiento = 'Credito' AND interno = '".$Interno."' 
						caja_interno = '".$row["caja_interno"]."' AND caja_recibo = '".$row["caja_recibo"]."'";
						$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
						if (mysql_num_rows($result1)>0)
						{
							$TotalDeuda = 0;
							while ($row1 = mysql_fetch_array($result1))
							{
								// Totalizar Deuda
								$TotalDeuda = $TotalDeuda + $row["saldo"];
							}
							// Sumar Deuda
							$query = "UPDATE caja_final SET saldo = saldo + ".$TotalDeuda." WHERE caja_interno = '".$row["caja_interno"]."' 
							AND caja_recibo = '".$row["caja_recibo"]."'";
							$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
						}
					}
				}
				// DELETE
				$query = "DELETE FROM mov_clientes WHERE interno = '".$Interno."' AND cliente_id = '".$ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}*/
			
		break;
		case "Devuelto":
			// BUSCAR BANCO
			$query = "SELECT * FROM bancos WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."' 
			AND cliente_id = '".$ClienteID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result)>0)
			{
				// DELETE
				$query = "DELETE FROM bancos WHERE caja_interno = '".$Caja_Interno."' AND caja_recibo = '".$Caja_Recibo."' 
				AND cliente_id = '".$ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
		break;
		
		case "Pagado":
			// INSERT
			$query = "INSERT INTO bancos (cliente_id, caja_interno, caja_recibo, fecha, tipo, numero, valor, banco, estado, digitado_por) VALUES 
			('".$ClienteID."', '".$Caja_Interno."', '".$Caja_Recibo."', NOW(), 'Cheque', '".$Cheque."', '".$Valor."', '".$Banco."', 
			'Aprobado', '".$_SESSION["UserCode"]."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		break;
	}
	// UPDATE
	$query = "UPDATE cheques SET estado_cheque = '".$Estado."', fecha_cheque = '".$Fecha."' WHERE caja_interno = '".$Caja_Interno."' 
	AND caja_recibo = '".$Caja_Recibo."' AND cliente_id = '".$ClienteID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	if ($Estado == "Devuelto")
	{
		$NewInterno = "CHQ";
		$NewInterno .= "".$Banco_Num."";
		$NewInterno .= "-".$Cheque."";
		// INSERT
		$query = "INSERT INTO mov_clientes 
		(cliente_id, tipo_movimiento, valor, saldo, interno, remision, fecha, estado, 
		digitado_por, fecha_digitado, aprobado_por, caja_interno, caja_recibo) VALUES 
		('".$ClienteID."', 'Debito', '".$Valor."', '".$Valor."', '".$NewInterno."', 'Cheque Devuelto', 
		NOW(), 'Aprobado', '".$_SESSION["UserCode"]."', NOW(), '".$_SESSION["UserCode"]."',  
		'".$Caja_Interno."', '".$Caja_Recibo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	}
}
else if (isset($_GET['Caja_General_Ajuste']))
{
	$Tipo = isset($_GET["Tipo"]) ? $_GET["Tipo"]:"";
	
	//----- AJUSTES -----//
	switch($Tipo)
	{
		case "Caja":
			$Valor = isset($_GET["Valor"]) ? $_GET["Valor"]:0;
			
			if ($Valor < 0)
			{
				echo "ERROR";
				break;
			}
			
			$query = "SELECT * FROM caja_final WHERE estado = 'Aprobado'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$query = "SELECT valor, tipo_mov FROM bancos WHERE estado = 'Aprobado'";
			$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$query = "SELECT valor, estado_cheque FROM cheques WHERE estado = 'Aprobado'";
			$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$Saldo_Efectivo = 0;
			$Total_Ingresos = 0;
			$Total_Egresos = 0;
			$Saldo_Bancos = 0;
			$Cheques_AlDia = 0;
			$Cheques_PostFechados = 0;
			
			if (mysql_num_rows($result2) > 0)
			{
				while ($row = mysql_fetch_array($result2, MYSQL_ASSOC))
				{
					if ($row["tipo_mov"] == "Entrada")
						$Saldo_Bancos += $row["valor"];
					else
						$Saldo_Bancos -= $row["valor"];
				}
			}
			else
			{
				echo "ERROR";
				break;
			}
					
			if (mysql_num_rows($result3) > 0)
			{
				while ($row = mysql_fetch_array($result3, MYSQL_ASSOC))
				{
					if ($row["estado_cheque"] == "Al Dia")
						$Cheques_AlDia += $row["valor"];
					
					if ($row["estado_cheque"] == "PostFechado")
						$Cheques_PostFechados += $row["valor"];
				}
			}
			else
			{
				echo "ERROR";
				break;
			}
			
			if (mysql_num_rows($result1) > 0)
			{
				$Entrada = 0;
				$Salida = 0;
				
				while ($row = mysql_fetch_array($result1, MYSQL_ASSOC))
				{
					if ($row["categoria"] == "Ingresos")
					{
						$Total_Ingresos += $row["total"];
						$Entrada += $row["efectivo"];
					}
					
					if ($row["categoria"] == "Egresos")
					{
						$Total_Egresos += $row["total"];
						$Salida += $row["efectivo"];
					}
				}
				
				//$Saldo_Efectivo = ($Total_Ingresos - $Total_Egresos) - $Saldo_Bancos - $Cheques_AlDia - $Cheques_PostFechados;
				$Saldo_Efectivo = ($Entrada - $Salida);
				
				if ($Saldo_Efectivo < 0)
				{
					$Total = ABS($Saldo_Efectivo) + $Valor;
					
					$Caja_Interno = "";
					$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$id = $row['id'] + 1;
						$len = strlen($id);
						$zero = "";
						if ($len < 7){
							$num = 7 - $len;
							for ($a = 0; $a < $num; $a++) {
								$zero .= "0";
							}
							$Caja_Interno = "MCJ".$zero."".$id."";
						}
						else {
							$Caja_Interno = "MCJ".$id."";
						}
					}
					else
					{
						echo "ERROR";
						break;
					}
					
					$query = "INSERT INTO caja_final 
					(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, 
					efectivo, total, digitado_por, fecha_digitado, estado) VALUES 
					(NOW(), '".$Caja_Interno."', '".$Caja_Interno."', 'Ingresos', 'Ajuste', 'Ajuste', 'N/A', 
					'".$MAIN_ID."', '".$Total."', '".$Total."', 'SISTEMA', NOW(), 'Aprobado')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				}
				else
				{
					if ($Valor > 0)
						$Total = $Saldo_Efectivo - $Valor;
					else
						$Total = $Saldo_Efectivo;
					
					$Caja_Interno = "";
					$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$id = $row['id'] + 1;
						$len = strlen($id);
						$zero = "";
						if ($len < 7){
							$num = 7 - $len;
							for ($a = 0; $a < $num; $a++) {
								$zero .= "0";
							}
							$Caja_Interno = "MCJ".$zero."".$id."";
						}
						else {
							$Caja_Interno = "MCJ".$id."";
						}
					}
					else
					{
						echo "ERROR";
						break;
					}
					
					$query = "INSERT INTO caja_final 
					(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, 
					efectivo, total, digitado_por, fecha_digitado, estado) VALUES 
					(NOW(), '".$Caja_Interno."', '".$Caja_Interno."', 'Egresos', 'Ajuste', 'Ajuste', 'N/A', 
					'".$MAIN_ID."', '".$Total."', '".$Total."', 'SISTEMA', NOW(), 'Aprobado')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				}
			}
			else
			{
				echo "ERROR";
			}
		break;
		
		case "Banco":
			$Banco = isset($_GET["Banco"]) ? $_GET["Banco"]:"";
			$Valor = isset($_GET["Valor"]) ? $_GET["Valor"]:0;
			
			if ($Valor < 0)
			{
				echo "ERROR";
				break;
			}
			
			$query = "SELECT * FROM bancos WHERE banco = '".$Banco."' AND estado = 'Aprobado'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$Entrada = 0;
				$Salida = 0;
				
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					if ($row["tipo_mov"] == "Entrada")
						$Entrada += $row["valor"];
					else
						$Salida += $row["valor"];
				}
				
				$Total = $Entrada - $Salida;
				
				//echo $Total;
				//echo "<br />";
				
				if ($Total < 0)
				{
					$Total = ABS($Total) + $Valor;
					//echo $Total;
					$query = "INSERT INTO bancos (cliente_id, fecha, tipo, valor, banco, estado, tipo_mov, digitado_por) VALUES 
					('".$MAIN_ID."', NOW(), 'Ajuste', '".$Total."', '".$Banco."', 'Aprobado', 'Entrada', 'SISTEMA')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				}
				else
				{
					if ($Valor > 0)
						$Total = $Total - $Valor;
					
					//echo $Total;
					$query = "INSERT INTO bancos (cliente_id, fecha, tipo, valor, banco, estado, tipo_mov, digitado_por) VALUES 
					('".$MAIN_ID."', NOW(), 'Ajuste', '".$Total."', '".$Banco."', 'Aprobado', 'Salida', 'SISTEMA')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				}
			}
			else
			{
				echo "ERROR";
			}
		break;
		
		default:
			echo "ERROR";
		break;
	}
}
else if (isset($_GET['Caja_General_Transacciones']))
{
	$Tipo = isset($_GET["Tipo"]) ? $_GET["Tipo"]:"";
	
	//----- TRANSACCIONES -----//
	switch($Tipo)
	{
		case "Banco":
			$Banco_Origen = isset($_GET["Banco_Origen"]) ? $_GET["Banco_Origen"]:"";
			$Banco_Destino = isset($_GET["Banco_Destino"]) ? $_GET["Banco_Destino"]:"";
			$Numero = isset($_GET["Numero"]) ? $_GET["Numero"]:"";
			$Valor = isset($_GET["Valor"]) ? $_GET["Valor"]:0;
			
			if ($Valor < 1)
			{
				echo "ERROR";
				break;
			}
			
		break;
		
		case "Caja":
			$Banco = isset($_GET["Banco"]) ? $_GET["Banco"]:"";
			$Numero = isset($_GET["Numero"]) ? $_GET["Numero"]:"";
			$Valor = isset($_GET["Valor"]) ? $_GET["Valor"]:0;
			
			if ($Valor < 1)
			{
				echo "ERROR";
				break;
			}
			
			$Caja_Interno = "";
			$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);
				$id = $row['id'] + 1;
				$len = strlen($id);
				$zero = "";
				if ($len < 7){
					$num = 7 - $len;
					for ($a = 0; $a < $num; $a++) {
						$zero .= "0";
					}
					$Caja_Interno = "MCJ".$zero."".$id."";
				}
				else {
					$Caja_Interno = "MCJ".$id."";
				}
			}
			else
			{
				echo "ERROR";
				break;
			}
					
			$query = "INSERT INTO caja_final 
			(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, 
			efectivo, total, digitado_por, fecha_digitado, estado) VALUES 
			(NOW(), '".$Caja_Interno."', '".$Caja_Interno."', 'Egresos', 'bancos', 'bancos', 'Deposito', 
			'".$MAIN_ID."', '".$Valor."', '".$Valor."', '".$_SESSION["UserCode"]."', NOW(), 'Aprobado')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			
			$Caja_Interno = "";
			$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);
				$id = $row['id'] + 1;
				$len = strlen($id);
				$zero = "";
				if ($len < 7){
					$num = 7 - $len;
					for ($a = 0; $a < $num; $a++) {
						$zero .= "0";
					}
					$Caja_Interno = "MCJ".$zero."".$id."";
				}
				else {
					$Caja_Interno = "MCJ".$id."";
				}
			}
			else
			{
				echo "ERROR";
				break;
			}
			
			$query = "INSERT INTO caja_final 
			(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, 
			consignacion, total, digitado_por, fecha_digitado, estado) VALUES 
			(NOW(), '".$Caja_Interno."', '".$Caja_Interno."', 'Ingresos', 'bancos', 'bancos', 'Deposito', 
			'".$MAIN_ID."', '".$Valor."', '".$Valor."', '".$_SESSION["UserCode"]."', NOW(), 'Aprobado')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
			
			$query = "INSERT INTO bancos 
			(cliente_id, caja_interno, caja_recibo, fecha, tipo, numero, valor, banco, estado, digitado_por) VALUES 
			('".$MAIN_ID."', '".$Caja_Interno."', '".$Caja_Interno."', NOW(), 'Consignacion', '".$Numero."', '".$Valor."', 
			'".$Banco."', 'Aprobado', '".$_SESSION["UserCode"]."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
		break;
		
		default:
			echo "ERROR";
		break;
	}
}
else if (isset($_GET['Compras_Crear']))
{
	//Parte 1
	$Entrada = 0;
	$Doc_Transp = 0;
	$Factura = 0;
	$Pedido = 0;
	$Interno = 0;
	$Fecha = 0;
	$ClienteID = 0;
	//Parte 2
	$Codigo = 0;
	$Cantidad = 0;
	$Dcto = 0;
	$UltimoCosto = 0;
	$Unitario = 0;
	// Parte 3
	$Observaciones = 0;
	$Subtotal = 0;
	$TipoServicio = "";
	$TipoServicioValor = 0;
	$Iva = 0;
	$TipoDcto = "";
	$TipoDctoValor = 0;
	$Total = 0;
	$Conductor = 0;
	$Placa = 0;
	$FormaPago = 0;
	$Peso = 0;
	$Peso_Bascula = 0;
	$Peso_Remision = 0;
	$Estado = 0;
	
	$data = $_GET['Compras_Crear'];
	foreach($data as $item)
	{
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		$Dcto = $item['Dcto'];
		$UltimoCosto = $item['UltCosto'];
		$Unitario = $item['Unitario'];
		if ($final == false) {
			$Entrada = $item['Entrada'];
			$Doc_Transp = $item['Doc_Transp'];
			$Factura = $item['Factura'];
			$Pedido = $item['Pedido'];
			$Interno = $item['Interno'];
			$Fecha = $item['Fecha'];
			$ClienteID = $item['ClienteID'];
			$Observaciones = $item['Observaciones'];
			$Subtotal = $item['Subtotal'];
			$TipoServicio = $item['TipoServicio'];
			$TipoServicioValor = $item['TipoServicioValor'];
			$Iva = $item['Iva'];
			$TipoDcto = $item['TipoDcto'];
			$TipoDctoValor = $item['TipoDctoValor'];
			$Total = $item['Total'];
			$Conductor = $item['Conductor'];
			$Placa = $item['Placa'];
			$FormaPago = $item['FormaPago'];
			$Peso = $item['Peso'];
			$Peso_Bascula = $item['Peso_Bascula'];
			$Peso_Remision = $item['Peso_Remision'];
			$Estado = $item['Estado'];
			
			$query = "SELECT id FROM compras_final ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 6){
				$num = 6 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Interno = "COMP".$zero."".$id."";
			}
			else {
				$Interno = "COMP".$id."";
			}
			
			if ($Factura == "")
				$Factura = $Interno;
			
			if ($Doc_Transp == "")
				$Doc_Transp = $Interno;
		}
		//--------------------------------------------------------------------------------//
		//-------------------------------- INSERT COMMAND --------------------------------//
		//--------------------------------------------------------------------------------//
		//-- Compras Movs
		$query = "INSERT INTO compras_movs
		(`codigo`, `cantidad`, `ultimo_costo`, `nuevo_costo`, `desc`, `interno`, `entrada`, `doc_transp`, `factura`, `cliente_id`) 
		VALUES ('".$Codigo."', '".$Cantidad."', '".$UltimoCosto."', '".$Unitario."', '".$Dcto."', '".$Interno."', '".$Entrada."', 
		'".$Doc_Transp."', '".$Factura."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		//echo "Resultado de Fac_Movs -> ".$result."\n";
		//-- Compras Final
		if ($final == false) {
			$query = "INSERT INTO compras_final 
			(`entrada`, `doc_transp`, `factura`, `pedido`, `interno`, `fecha_compra`, `cliente_id`, 
			`forma_pago`, `peso`, `peso_bascula`, `peso_remision`, `conductor`, `placa`, `observaciones`, `tipo_servicio`, 
			`tipo_servicio_valor`, `tipo_descuento`, `tipo_descuento_valor`, `sub_total`, `total`, `iva`, `estado`, 
			`digitado_por`, `fecha_digitado`) VALUES ('".$Entrada."', '".$Doc_Transp."', '".$Factura."', '".$Pedido."', 
			'".$Interno."', '".$Fecha."', '".$ClienteID."', '".$FormaPago."', '".$Peso."', '".$Peso_Bascula."', 
			'".$Peso_Remision."', '".$Conductor."', '".$Placa."', '".$Observaciones."', '".$TipoServicio."', 
			'".$TipoServicioValor."', '".$TipoDcto."', '".$TipoDctoValor."', '".$Subtotal."', '".$Total."', '".$Iva."', 
			'".$Estado."', '".$_SESSION["UserCode"]."', NOW())";
			$final = true;
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			//echo "Resultado de Fac_Final -> ".$result."\n";
		}
	}
	echo $Interno;
}
else if (isset($_GET['Compras_Modificar']))
{
	//Parte 1
	$Entrada = 0;
	$Doc_Transp = 0;
	$Factura = 0;
	$Pedido = 0;
	$Interno = 0;
	$Fecha = 0;
	$ClienteID = 0;
	//Parte 2
	$Codigo = 0;
	$Cantidad = 0;
	$Dcto = 0;
	$UltimoCosto = 0;
	$Unitario = 0;
	// Parte 3
	$Observaciones = 0;
	$Subtotal = 0;
	$TipoServicio = "";
	$TipoServicioValor = 0;
	$Iva = 0;
	$TipoDcto = "";
	$TipoDctoValor = 0;
	$Total = 0;
	$Conductor = 0;
	$Placa = 0;
	$FormaPago = 0;
	$Peso = 0;
	$Peso_Bascula = 0;
	$Peso_Remision = 0;
	
	//-------- CHECK IF SOME ITEM HAS BEEN DELETED
	$data = $_GET['Compras_Modificar'];
	$delete = true;
	$query = "SELECT codigo FROM compras_movs WHERE interno = '".$data[0]['Interno']."' ";
	$result = mysql_query($query) or die ("SQL Error 1: " . mysql_error());
	$numrow = mysql_num_rows($result);
	if ($numrow > 0)
	{
		while ($rows = mysql_fetch_array($result)){
			
			$num = count($data);
			for ($i=0; $i<$num; $i++) {
				if ($rows['codigo'] == $data[$i]['CodFab'])
				{
					$delete = false;
				}
			}
			if ($delete == true){
				$query2 = "DELETE FROM compras_movs WHERE codigo = '".$rows['codigo']."' AND interno = '".$data[0]['Interno']."' ";
				$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	foreach($data as $item)
	{
		//------------------ UPDATE/INSERT COMMAND
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		$Dcto = $item['Dcto'];
		$UltimoCosto = $item['UltCosto'];
		$Unitario = $item['Unitario'];
		if ($final == false)
		{
			$Entrada = $item['Entrada'];
			$Doc_Transp = $item['Doc_Transp'];
			$Factura = $item['Factura'];
			$Pedido = $item['Pedido'];
			$Interno = $item['Interno'];
			$Fecha = $item['Fecha'];
			$ClienteID = $item['ClienteID'];
			$Observaciones = $item['Observaciones'];
			$Subtotal = $item['Subtotal'];
			$TipoServicio = $item['TipoServicio'];
			$TipoServicioValor = $item['TipoServicioValor'];
			$Iva = $item['Iva'];
			$TipoDcto = $item['TipoDcto'];
			$TipoDctoValor = $item['TipoDctoValor'];
			$Total = $item['Total'];
			$Conductor = $item['Conductor'];
			$Placa = $item['Placa'];
			$FormaPago = $item['FormaPago'];
			$Peso = $item['Peso'];
			$Peso_Bascula = $item['Peso_Bascula'];
			$Peso_Remision = $item['Peso_Remision'];
		}
		
		$query = "SELECT codigo FROM compras_movs WHERE codigo = '".$Codigo."' AND interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			//-- Compras Movs
			$query = "UPDATE compras_movs SET 
			`cantidad`= '".$Cantidad."', 
			`ultimo_costo` = '".$UltimoCosto."', 
			`nuevo_costo` = '".$Unitario."', 
			`desc` = '".$Dcto."', 
			`entrada` = '".$Entrada."', 
			`doc_transp` = '".$Doc_Transp."', 
			`factura` = '".$Factura."', 
			`cliente_id` = '".$ClienteID."' 
			WHERE `codigo` = '".$Codigo."' AND `interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		}
		else
		{
			//-- Compras Movs
			$query = "INSERT INTO compras_movs
			(`codigo`, `cantidad`, `ultimo_costo`, `nuevo_costo`, `desc`, `interno`, `entrada`, `doc_transp`, `factura`, `cliente_id`) 
			VALUES ('".$Codigo."', '".$Cantidad."', '".$UltimoCosto."', '".$Unitario."', '".$Dcto."', '".$Interno."', '".$Entrada."', 
			'".$Doc_Transp."', '".$Factura."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
		}
		
		if ($final == false)
		{
			//-- Compras Final
			$query = "UPDATE compras_final SET 
			`entrada` = '".$Entrada."', 
			`doc_transp` = '".$Doc_Transp."', 
			`factura` = '".$Factura."', 
			`pedido` = '".$Pedido."', 
			`fecha_compra` = '".$Fecha."', 
			`cliente_id` = '".$ClienteID."',
			`forma_pago` = '".$FormaPago."', 
			`peso` = '".$Peso."', 
			`peso_bascula` = '".$Peso_Bascula."', 
			`peso_remision` = '".$Peso_Remision."', 
			`conductor` = '".$Conductor."', 
			`placa` = '".$Placa."', 
			`observaciones` = '".$Observaciones."', 
			`tipo_servicio` = '".$TipoServicio."', 
			`tipo_servicio_valor` = '".$TipoServicioValor."', 
			`tipo_descuento` = '".$TipoDcto."', 
			`tipo_descuento_valor` = '".$TipoDctoValor."', 
			`sub_total` = '".$Subtotal."', 
			`total` = '".$Total."', 
			`iva` = '".$Iva."', 
			`modificado_por` = '".$_SESSION["UserCode"]."', 
			`fecha_modificado` = NOW() 
			WHERE `interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
			$final = true;
		}
	}
}
else if (isset($_GET['Compras_Modificar_Anular']))
{
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:0;
	$Motivo = isset($_GET['Motivo']) ? $_GET['Motivo']:0;
	$query = "UPDATE compras_final SET `estado` = 'Anulado', `anulado_por` = '".$_SESSION["UserCode"]."', 
	`motivo_anulado` = '".$Motivo."', `fecha_anulado` = NOW() WHERE `interno` = '".$Interno."'";
	$result = mysql_query($query) or die("SQL Error: 1" . mysql_error());
}
else if (isset($_GET['Compras_Modificar_Pedido']))
{
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:0;
	$query = "UPDATE compras_final SET `estado` = 'Creado', `fecha_modificado` = NOW() WHERE `interno` = '".$Interno."'";
	$result = mysql_query($query) or die("SQL Error: 1" . mysql_error());
}
else if (isset($_GET['Compras_Autorizar']))
{
	$Old_Estado = isset($_GET['Old_Estado']) ? $_GET['Old_Estado']:"Creado";
	$New_Estado = isset($_GET['New_Estado']) ? $_GET['New_Estado']:"Creado";
	$Motivo_Anulado = isset($_GET['Motivo_Anulado']) ? $_GET['Motivo_Anulado']:0;
	$Fecha = isset($_GET['Fecha']) ? $_GET['Fecha']:"0000-00-00";
	$Factura = isset($_GET['Factura']) ? $_GET['Factura']:0;
	$Entrada = isset($_GET['Entrada']) ? $_GET['Entrada']:0;
	$Doc_Transp = isset($_GET['Doc_Transp']) ? $_GET['Doc_Transp']:0;
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:0;
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:0;
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM compras_final WHERE interno = '".$Interno."' AND estado = '".$Old_Estado."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
	}
	else
	{
		$query = "UPDATE compras_final SET 
		factura = '".$Factura."', 
		doc_transp = '".$Doc_Transp."', 
		estado = '".$New_Estado."'";
		$query .= ($New_Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', motivo_anulado = '".$Motivo_Anulado."', fecha_anulado = NOW() ":"";
		$query .= ($New_Estado == "Autorizado") ? ", autorizado_por = '".$_SESSION["UserCode"]."', fecha_autorizado = NOW() ":"";
		$query .= "WHERE entrada = '".$Entrada."' AND cliente_id = '".$ClienteID."' AND interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		
		$query = "UPDATE cxp_movs SET 
		factura = '".$Factura."', 
		doc_transp = '".$Doc_Transp."' 
		WHERE compra_interno = '".$Interno."' AND compra_entrada = '".$Entrada."' AND cliente_id = '".$ClienteID."' AND tipo_movimiento = 'Compra'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
		
		if ($New_Estado == "Anulado")
		{
			Anular_Final($ClienteID, $Entrada, $Interno, "Compra", $Motivo_Anulado);
		}
		
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_GET['Compras_Ingresar']))
{
	//Parte 1
	$Entrada = 0;
	$Doc_Transp = 0;
	$Factura = 0;
	$Pedido = 0;
	$Interno = 0;
	$Fecha = 0;
	$ClienteID = 0;
	//Parte 2
	$Codigo = 0;
	$Cantidad = 0;
	$Dcto = 0;
	$UltimoCosto = 0;
	$Unitario = 0;
	// Parte 3
	$Observaciones = 0;
	$Subtotal = 0;
	$TipoServicio = "";
	$TipoServicioValor = 0;
	$Iva = 0;
	$TipoDcto = "";
	$TipoDctoValor = 0;
	$Total = 0;
	$Conductor = 0;
	$Placa = 0;
	$FormaPago = 0;
	$Peso = 0;
	$Peso_Bascula = 0;
	$Peso_Remision = 0;
	
	//-------- CHECK IF SOME ITEM HAS BEEN DELETED
	$data = $_GET['Compras_Ingresar'];
	$delete = true;
	$query = "SELECT codigo FROM compras_movs WHERE interno = '".$data[0]['Interno']."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$numrow = mysql_num_rows($result);
	if ($numrow > 0)
	{
		while ($rows = mysql_fetch_array($result)){
			
			$num = count($data);
			for ($i=0; $i<$num; $i++) {
				if ($rows['codigo'] == $data[$i]['CodFab'])
				{
					$delete = false;
				}
			}
			if ($delete == true){
				$query2 = "DELETE FROM compras_movs WHERE codigo = '".$rows['codigo']."' AND interno = '".$data[0]['Interno']."' ";
				$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	$query = "SELECT digitado_por, aprobado_por, fecha_aprobado FROM compras_final WHERE interno = '".$data[0]['Interno']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$DigitadorID = $row["digitado_por"];
		$AprobadorID = $row["aprobado_por"];
		$FechaAprobado = $row["fecha_aprobado"];
		
		if ($FechaAprobado != "0000-00-00 00:00:00")
			$MantenerFecha = true;
		else
			$MantenerFecha = false;
	}
	else {
		$DigitadorID = "";
		$MantenerFecha = false;
	}
	
	foreach($data as $item)
	{
		//------------------ UPDATE/INSERT COMMAND
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		$Dcto = $item['Dcto'];
		$UltimoCosto = $item['UltCosto'];
		$Unitario = $item['Unitario'];
		if ($final == false)
		{
			$Entrada = $item['Entrada'];
			$Doc_Transp = $item['Doc_Transp'];
			$Factura = $item['Factura'];
			$Pedido = $item['Pedido'];
			$Interno = $item['Interno'];
			$Fecha = $item['Fecha'];
			$ClienteID = $item['ClienteID'];
			$Observaciones = $item['Observaciones'];
			$Subtotal = $item['Subtotal'];
			$TipoServicio = $item['TipoServicio'];
			$TipoServicioValor = $item['TipoServicioValor'];
			$Iva = $item['Iva'];
			$TipoDcto = $item['TipoDcto'];
			$TipoDctoValor = $item['TipoDctoValor'];
			$Total = $item['Total'];
			$Conductor = $item['Conductor'];
			$Placa = $item['Placa'];
			$FormaPago = $item['FormaPago'];
			$Peso = $item['Peso'];
			$Peso_Bascula = $item['Peso_Bascula'];
			$Peso_Remision = $item['Peso_Remision'];
		}
		
		$query = "SELECT codigo FROM compras_movs WHERE codigo = '".$Codigo."' AND interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		if (mysql_num_rows($result)>0)
		{
			//-- Compras Movs
			$query = "UPDATE compras_movs SET 
			`cantidad`= '".$Cantidad."', 
			`ultimo_costo` = '".$UltimoCosto."', 
			`nuevo_costo` = '".$Unitario."', 
			`desc` = '".$Dcto."',  
			`cliente_id` = '".$ClienteID."' 
			WHERE `codigo` = '".$Codigo."' AND `interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
		}
		else
		{
			//-- Compras Movs
			$query = "INSERT INTO compras_movs
			(`codigo`, `cantidad`, `ultimo_costo`, `nuevo_costo`, `desc`, `interno`, `entrada`, `doc_transp`, `factura`, `cliente_id`) 
			VALUES ('".$Codigo."', '".$Cantidad."', '".$UltimoCosto."', '".$Unitario."', '".$Dcto."', '".$Interno."', '".$Entrada."', 
			'".$Doc_Transp."', '".$Factura."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
		}
		
		if ($final == false)
		{
			//-- Compras Final
			$query = "UPDATE compras_final SET 
			`pedido` = '".$Pedido."', 
			`fecha_compra` = '".$Fecha."', 
			`cliente_id` = '".$ClienteID."',
			`forma_pago` = '".$FormaPago."', 
			`peso` = '".$Peso."', 
			`peso_bascula` = '".$Peso_Bascula."', 
			`peso_remision` = '".$Peso_Remision."', 
			`conductor` = '".$Conductor."', 
			`placa` = '".$Placa."', 
			`observaciones` = '".$Observaciones."', 
			`tipo_servicio` = '".$TipoServicio."', 
			`tipo_servicio_valor` = '".$TipoServicioValor."', 
			`tipo_descuento` = '".$TipoDcto."', 
			`tipo_descuento_valor` = '".$TipoDctoValor."', 
			`sub_total` = '".$Subtotal."', 
			`total` = '".$Total."', 
			`iva` = '".$Iva."', 
			`estado` = 'Aprobado' ";
			$query .= $MantenerFecha == false ? ", 
			`aprobado_por` = '".$_SESSION["UserCode"]."', 
			`fecha_aprobado` = NOW() ":"";
			$query .= "WHERE `interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
			
			//-- CxP Movs
			$query = "INSERT INTO cxp_movs
			(`compra_interno`, `compra_entrada`, `factura`, `cliente_id`, `tipo_movimiento`, `valor`, `saldo`, `estado`, 
			`digitado_por`, `fecha_digitado`, `aprobado_por`, `fecha_aprobado`, `grupo`, `subgrupo`, `subgrupo2`) VALUES
			('".$Interno."', '".$Entrada."', '".$Factura."', '".$ClienteID."', 'Compra', '".$Total."', '".$Total."', 'Aprobado', 
			'".$DigitadorID."', ";
			$query .= $MantenerFecha == false ? "NOW(), '".$_SESSION["UserCode"]."', NOW(), ":"'".$FechaAprobado."', '".$AprobadorID."', '".$FechaAprobado."', ";
			$query .= "'Inventarios', 'Inventarios', 'Inventarios')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
			$final = true;
		}
		
		//--- INSERT INTO Inventario_Movs
		if ($Cantidad > 0)
		{
			if (!$MantenerFecha)
				AfectarInventario($Codigo, $Cantidad, $Unitario, $Interno, "", "Compra", "Compra");
			else
				AfectarInventario($Codigo, $Cantidad, $Unitario, $Interno, $FechaAprobado, "Compra", "Compra");
		}
	}
}
else if (isset($_GET['Compras_Ajustes']))
{
	//Parte 1
	$Entrada = 0;
	$Doc_Transp = 0;
	$Factura = 0;
	$Pedido = 0;
	$Interno = 0;
	$Fecha = 0;
	$ClienteID = 0;
	//Parte 2
	$Codigo = 0;
	$Cantidad = 0;
	$Dcto = 0;
	$UltimoCosto = 0;
	$Unitario = 0;
	// Parte 3
	$Observaciones = 0;
	$Subtotal = 0;
	$TipoServicio = "";
	$TipoServicioValor = 0;
	$Iva = 0;
	$TipoDcto = "";
	$TipoDctoValor = 0;
	$Total = 0;
	$Conductor = 0;
	$Placa = 0;
	$FormaPago = 0;
	$Peso = 0;
	$Peso_Bascula = 0;
	$Peso_Remision = 0;
	
	//-------- CHECK IF SOME ITEM HAS BEEN DELETED
	$data = $_GET['Compras_Ajustes'];
	$delete = true;
	$query = "SELECT codigo FROM compras_movs WHERE interno = '".$data[0]['Interno']."' ";
	$result = mysql_query($query) or die ("SQL Error 1: " . mysql_error());
	$numrow = mysql_num_rows($result);
	if ($numrow > 0)
	{
		while ($rows = mysql_fetch_array($result)){
			
			$num = count($data);
			for ($i=0; $i<$num; $i++) {
				if ($rows['codigo'] == $data[$i]['CodFab'])
				{
					$delete = false;
				}
			}
			if ($delete == true){
				$query2 = "DELETE FROM compras_movs WHERE codigo = '".$rows['codigo']."' AND interno = '".$data[0]['Interno']."' ";
				$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	$query = "SELECT digitado_por FROM compras_final WHERE interno = '".$data[0]['Interno']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$DigitadorID = $row["digitado_por"];
	}
	else
		$DigitadorID = "";
	
	foreach($data as $item)
	{
		//------------------ UPDATE/INSERT COMMAND
		$Codigo = $item['CodFab'];
		$Cantidad = $item['Cantidad'];
		$Dcto = $item['Dcto'];
		$UltimoCosto = $item['UltCosto'];
		$Unitario = $item['Unitario'];
		if ($final == false)
		{
			$Entrada = $item['Entrada'];
			$Doc_Transp = $item['Doc_Transp'];
			$Factura = $item['Factura'];
			$Pedido = $item['Pedido'];
			$Interno = $item['Interno'];
			$ClienteID = $item['ClienteID'];
			$Observaciones = $item['Observaciones'];
			$Subtotal = $item['Subtotal'];
			$TipoServicio = $item['TipoServicio'];
			$TipoServicioValor = $item['TipoServicioValor'];
			$Iva = $item['Iva'];
			$TipoDcto = $item['TipoDcto'];
			$TipoDctoValor = $item['TipoDctoValor'];
			$Total = $item['Total'];
			$Conductor = $item['Conductor'];
			$Placa = $item['Placa'];
			$FormaPago = $item['FormaPago'];
			$Peso = $item['Peso'];
			$Peso_Bascula = $item['Peso_Bascula'];
			$Peso_Remision = $item['Peso_Remision'];
			
			$Fecha = Del_InventoryMovs($Interno);
		}
		
		$query = "SELECT codigo FROM compras_movs WHERE codigo = '".$Codigo."' AND interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result)>0)
		{
			//-- Compras Movs
			$query = "UPDATE compras_movs SET 
			`cantidad`= '".$Cantidad."', 
			`ultimo_costo` = '".$UltimoCosto."', 
			`nuevo_costo` = '".$Unitario."', 
			`desc` = '".$Dcto."',  
			`cliente_id` = '".$ClienteID."' 
			WHERE `codigo` = '".$Codigo."' AND `interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		}
		else
		{
			//-- Compras Movs
			$query = "INSERT INTO compras_movs
			(`codigo`, `cantidad`, `ultimo_costo`, `nuevo_costo`, `desc`, `interno`, `entrada`, `doc_transp`, `factura`, `cliente_id`) 
			VALUES ('".$Codigo."', '".$Cantidad."', '".$UltimoCosto."', '".$Unitario."', '".$Dcto."', '".$Interno."', '".$Entrada."', 
			'".$Doc_Transp."', '".$Factura."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
		}
		
		if ($final == false)
		{
			//-- Compras Final
			$query = "UPDATE compras_final SET 
			`pedido` = '".$Pedido."', 
			`cliente_id` = '".$ClienteID."',
			`forma_pago` = '".$FormaPago."', 
			`peso` = '".$Peso."', 
			`peso_bascula` = '".$Peso_Bascula."', 
			`peso_remision` = '".$Peso_Remision."', 
			`conductor` = '".$Conductor."', 
			`placa` = '".$Placa."', 
			`observaciones` = '".$Observaciones."', 
			`tipo_servicio` = '".$TipoServicio."', 
			`tipo_servicio_valor` = '".$TipoServicioValor."', 
			`tipo_descuento` = '".$TipoDcto."', 
			`tipo_descuento_valor` = '".$TipoDctoValor."', 
			`sub_total` = '".$Subtotal."', 
			`total` = '".$Total."', 
			`iva` = '".$Iva."', 
			`modificado_por` = '".$_SESSION["UserCode"]."', 
			`fecha_modificado` = NOW() 
			WHERE `interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
			
			$query = "SELECT * FROM cxp_movs WHERE compra_interno = '".$Interno."'";
			$result = mysql_query($query) or die("SQL Error 7-1: " . mysql_error());
			if (mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_array($result);
				Anular_CxPMovs($Interno, "Anulado Automaticamente por Compras -> Ajuste de Ingreso");
				
				$query = "INSERT INTO cxp_movs 
				(compra_interno, compra_entrada, factura, doc_transp, cliente_id, tipo_movimiento, valor, saldo, estado, 
				digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, origen1, origen2, origen3, origen_documento, 
				caja_interno, caja_recibo, grupo, subgrupo, subgrupo2) VALUES ('".$Interno."', '".$Entrada."', '".$Factura."', 
				'".$Doc_Transp."', '".$ClienteID."', 'Compra', '".$Total."', '".$Total."', 'Aprobado', '".$DigitadorID."', 
				'".$row["fecha_digitado"]."', '".$row["aprobado_por"]."', '".$row["fecha_aprobado"]."', '".$row["origen1"]."', 
				'".$row["origen2"]."', '".$row["origen3"]."', '".$row["origen_documento"]."', '".$row["caja_interno"]."', 
				'".$row["caja_recibo"]."', '".$row["grupo"]."', '".$row["subgrupo"]."', '".$row["subgrupo2"]."')";
				$result = mysql_query($query) or die("SQL Error 7-2: " . mysql_error());
			}
			
			$final = true;
			
			/*$query = "UPDATE cxp_movs SET `valor` = '".$Total."' WHERE `compra_interno` = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");*/
			
		}
		//--- INSERT INTO Inventario_Movs
		if ($Cantidad > 0)
		{
			if ($Fecha == "")
				AfectarInventario($Codigo, $Cantidad, $Unitario, $Interno, "", "Compra", "Compra");
			else
				AfectarInventario($Codigo, $Cantidad, $Unitario, $Interno, $Fecha, "Compra", "Compra");
		}
	}
}
else if (isset($_GET['Inventario_Crear']))
{
	$Foto_Producto = "";
	$ProductoID = "";
	$Producto = "";
	$Categoria = "";
	$Grupo = "";
	$SubGrupo = "";
	$Unidad = "";
	$Costo = "";
	$Peso = "";
	$ExistenciaIni = "";
	$Stock = "";
	$Facturar_sin_Existencia = "";
	$Produccion = "";
	$ListaP1 = "";
	$ListaP2 = "";
	$ListaP3 = "";
	$ListaP4 = "";
	$Notas = "";
	$ProveedorID = "";
	
	$data = $_GET['Inventario_Crear'];
	
	foreach($data as $item)
	{
		if (isset($item['ProveedorID'])) {
			$ProveedorID = $item['ProveedorID'];
		}
		
		if ($final == false)
		{
			$Foto_Producto = $item['Foto_Producto'];
			$ProductoID = $item['ProductoID'];
			$Producto =  $item['Producto'];
			$Categoria =  $item['Categoria'];
			$Grupo =  $item['Grupo'];
			$SubGrupo =  $item['SubGrupo'];
			$Unidad = $item['Unidad'];
			$Costo =  $item['Costo'];
			$Peso =  $item['Peso'];
			$ExistenciaIni =  $item['ExistenciaIni'];
			$Stock =  $item['Stock'];
			$Facturar_sin_Existencia =  $item['Facturar_sin_Existencia'];
			$Produccion =  $item['Produccion'];
			$ListaP1 =  $item['ListaP1'];
			$ListaP2 =  $item['ListaP2'];
			$ListaP3 =  $item['ListaP3'];
			$ListaP4 =  $item['ListaP4'];
			$Notas =  $item['Notas'];
		}
		
		if ($final == false)
		{
			if ($Foto_Producto != "")
			{
				$FilePath = pathinfo($Foto_Producto);
				$EXT = $FilePath['extension'];
				$NewImage = "productos/".$ProductoID.".".$EXT."";
				if (!rename("../".$Foto_Producto."","../images/".$NewImage.""))
				{
					$Data[0] = array (
						"MESSAGE" => "ERROR",
					);
					echo json_encode($Data);
					die();
				}
				$Foto_Producto = $NewImage;
				/*
				//-- Garantias - Rename Images
				$FilePath = pathinfo($Foto_Producto);
				$EXT = $FilePath['extension'];
				$NewImage = "productos/".$ProductoID.".".$EXT."";
				rename("../images/".$Foto_Producto."","../images/".$NewImage."");
				$Foto_Producto = $NewImage;
				*/
			}
			
			//--------------------------------------------------------------------------------//
			//-------------------------------- INSERT COMMAND --------------------------------//
			//--------------------------------------------------------------------------------//
			//--- Producto
			$query = "INSERT INTO productos
			(cod_fab, categoria, grupo, subgrupo, nombre, und_med, peso, costo, ultimo_costo, costo_promedio, lista1, lista2, lista3, 
			lista4, existencia, stock_minimo, venta_promedio, notas, factura_sin_existencia, image, produccion, digitado_por, fecha_digitado) VALUES 
			('".$ProductoID."', '".$Categoria."', '".$Grupo."', '".$SubGrupo."', '".$Producto."', '".$Unidad."', '".$Peso."', 
			'".$Costo."', '".$Costo."', '".$Costo."', '".$ListaP1."', '".$ListaP2."', '".$ListaP3."', '".$ListaP4."', '".$ExistenciaIni."', 
			'".$Stock."', '".$Costo."', '".$Notas."', '".$Facturar_sin_Existencia."', '".$Foto_Producto."', '".$Produccion."', 
			'".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			//--- Inventario Movs
			$query = "INSERT INTO inventario_movs 
			(cod_fab, cantidad, costo, viejo_costo, costo_promedio, viejo_costo_promedio, ultimo_costo, 
			existencia, motivo, fecha, tipo) VALUES ('".$ProductoID."', '".$ExistenciaIni."', '".$Costo."', 
			'".$Costo."', '".$Costo."', '".$Costo."', '".$Costo."', '0', 'Inicial', NOW(), 'Entrada')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			
			$final = true;
		}
		
		if ($ProveedorID != "")
		{
			//-- Proveedor - Insert
			$query = "INSERT INTO productos_prov
			(cod_fab, cliente_id) VALUES ('".$ProductoID."', '".$ProveedorID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		}
	}
	$Data[0] = array (
		"MESSAGE" => "OK",
	);
	echo json_encode($Data);
}
else if (isset($_GET['Inventario_Modificar']))
{
	$Foto_Producto = "";
	$ProductoID = "";
	$Producto = "";
	$Categoria = "";
	$Grupo = "";
	$SubGrupo = "";
	$Unidad = "";
	$Costo = "";
	$Peso = "";
	$Stock = "";
	$Facturar_sin_Existencia = "";
	$Produccion = "";
	$ListaP1 = "";
	$ListaP2 = "";
	$ListaP3 = "";
	$ListaP4 = "";
	$Notas = "";
	$ProveedorID = "";
	
	$data = $_GET['Inventario_Modificar'];
	
	$delete = true;
	$query = "SELECT * FROM productos_prov WHERE cod_fab = '".$data[0]["ProductoID"]."' ";
	$result = mysql_query($query) or die ("SQL Error 1: " . mysql_error());
	$numrow = mysql_num_rows($result);
	if ($numrow > 0){
		while ($rows = mysql_fetch_array($result))
		{
			$num = count($data);
			for ($i=0; $i<$num; $i++)
			{
				if ($rows['cliente_id'] == $data[$i]['ProveedorID'])
				{
					$delete = false;
				}
			}
			if ($delete == true){
				$query = "DELETE FROM productos_prov WHERE cod_fab = '".$data[0]["ProductoID"]."' AND cliente_id = '".$rows['cliente_id']."' ";
				$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	foreach($data as $item)
	{
		if (isset($item['ProveedorID'])) {
			$ProveedorID = $item['ProveedorID'];
		}
		
		if ($final == false)
		{
			$Foto_Producto = $item['Foto_Producto'];
			$ProductoID = $item['ProductoID'];
			$Producto =  $item['Producto'];
			$Categoria =  $item['Categoria'];
			$Grupo =  $item['Grupo'];
			$SubGrupo =  $item['SubGrupo'];
			$Unidad = $item['Unidad'];
			$Costo =  $item['Costo'];
			$Peso =  $item['Peso'];
			$Stock =  $item['Stock'];
			$Facturar_sin_Existencia =  $item['Facturar_sin_Existencia'];
			$Produccion =  $item['Produccion'];
			$ListaP1 =  $item['ListaP1'];
			$ListaP2 =  $item['ListaP2'];
			$ListaP3 =  $item['ListaP3'];
			$ListaP4 =  $item['ListaP4'];
			$Notas =  $item['Notas'];
		}
		
		if ($final == false)
		{
			if ($Foto_Producto != "")
			{
				$query = "SELECT image FROM productos WHERE cod_fab = '".$ProductoID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				$row = mysql_fetch_array($result);
				if ($Foto_Producto != $row["image"])
				{
					if ($row["image"] != "")
					{
						$TargetFile = "../images/".$row["image"];
						if ($WINDOWS)
						{
							if (chmod($TargetFile,0777))
							{
								if (!unlink($TargetFile))
									die();
							}
						}
						else
						{
							//Linux
							if (!unlink($TargetFile))
								die();
						}
					}
					
					//-- Rename Image
					$FilePath = pathinfo($Foto_Producto);
					$EXT = $FilePath['extension'];
					$NewImage = "productos/".$ProductoID.".".$EXT."";
					if (!rename("../".$Foto_Producto."","../images/".$NewImage.""))
					{
						$Data[0] = array (
							"MESSAGE" => "ERROR",
						);
						echo json_encode($Data);
						die();
					}
					$Foto_Producto = $NewImage;
				}
			}
			
			//--------------------------------------------------------------------------------//
			//-------------------------------- UPDATE COMMAND --------------------------------//
			//--------------------------------------------------------------------------------//
			//--- Producto
			$query = "UPDATE productos SET 
			categoria = '".$Categoria."', 
			grupo = '".$Grupo."', 
			subgrupo = '".$SubGrupo."', 
			nombre = '".$Producto."', 
			und_med = '".$Unidad."', 
			peso = '".$Peso."', 
			costo = '".$Costo."', 
			lista1 = '".$ListaP1."', 
			lista2 = '".$ListaP2."', 
			lista3 = '".$ListaP3."', 
			lista4 = '".$ListaP4."', 
			stock_minimo = '".$Stock."', 
			notas = '".$Notas."', 
			factura_sin_existencia = '".$Facturar_sin_Existencia."', 
			image = '".$Foto_Producto."', 
			produccion = '".$Produccion."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			ultima_actualizacion = NOW() 
			WHERE cod_fab = '".$ProductoID."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$final = true;
		}
		
		if ($ProveedorID != "")
		{
			$query = "SELECT * FROM productos_prov WHERE cod_fab = '".$ProductoID."' AND cliente_id = '".$ProveedorID."'";
			$result = mysql_query($query) or die ("SQL Error 1: " . mysql_error());
			$numrow = mysql_num_rows($result);
			if ($numrow <= 0)
			{
				$query = "INSERT INTO productos_prov
				(cod_fab, cliente_id) VALUES ('".$ProductoID."', '".$ProveedorID."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			}
		}
	}
	$Data[0] = array (
		"MESSAGE" => "OK",
	);
	echo json_encode($Data);
}
else if (isset($_GET['Inventario_Listado']))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$Activo = isset($_GET["Activo"]) ? $_GET["Activo"]:"";
	$Motivo = isset($_GET["Motivo"]) ? $_GET["Motivo"]:"";
	$Codigo = isset($_GET["Codigo"]) ? $_GET["Codigo"]:"";
	$Categoria = isset($_GET["Categoria"]) ? $_GET["Categoria"]:"";
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:"";
	$SubGrupo = isset($_GET["SubGrupo"]) ? $_GET["SubGrupo"]:"";
	$Nombre = isset($_GET["Nombre"]) ? $_GET["Nombre"]:"";
	$Und = isset($_GET["Und"]) ? $_GET["Und"]:"";
	$Peso = isset($_GET["Peso"]) ? $_GET["Peso"]:"";
	$Stock = isset($_GET["Stock"]) ? $_GET["Stock"]:"";
	$Costo = isset($_GET["Costo"]) ? $_GET["Costo"]:"";
	$Ult_Costo = isset($_GET["Ult_Costo"]) ? $_GET["Ult_Costo"]:"";
	$Costo_Prom = isset($_GET["Costo_Prom"]) ? $_GET["Costo_Prom"]:"";
	$Lista1 = isset($_GET["Lista1"]) ? $_GET["Lista1"]:"";
	$Lista2 = isset($_GET["Lista2"]) ? $_GET["Lista2"]:"";
	$Lista3 = isset($_GET["Lista3"]) ? $_GET["Lista3"]:"";
	$Lista4 = isset($_GET["Lista4"]) ? $_GET["Lista4"]:"";
	$Produccion = isset($_GET["Produccion"]) ? $_GET["Produccion"]:"";
	$Facturar = isset($_GET["Facturar"]) ? $_GET["Facturar"]:"";
	
	$query = "UPDATE productos SET 
	categoria = '".$Categoria."', 
	grupo = '".$Grupo."', 
	subgrupo = '".$SubGrupo."', 
	nombre = '".$Nombre."', 
	und_med = '".$Und."', 
	peso = '".$Peso."', 
	stock_minimo = '".$Stock."', 
	costo = '".$Costo."', 
	ultimo_costo = '".$Ult_Costo."', 
	costo_promedio = '".$Costo_Prom."', 
	lista1 = '".$Lista1."', 
	lista2 = '".$Lista2."', 
	lista3 = '".$Lista3."', 
	lista4 = '".$Lista4."', 
	produccion = '".$Produccion."', 
	factura_sin_existencia = '".$Facturar."', 
	activo = '".$Activo."', 
	motivo = '".$Motivo."' 
	WHERE id = '".$ID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_affected_rows() > 0)
		echo "OK";
	else
		echo "ERROR";
}
else if (isset($_GET['Inventario_Ajustar_Movs']))
{
	$Codigo = isset($_GET["Codigo"]) ? $_GET["Codigo"]:"";
	$Tipo = isset($_GET["Tipo"]) ? $_GET["Tipo"]:"";
	$Cantidad = isset($_GET["Cantidad"]) ? $_GET["Cantidad"]:0;
	$Motivo = isset($_GET["Motivo"]) ? $_GET["Motivo"]:"";
	
	if ($Tipo == "Entrada")
		AfectarInventario($Codigo, $Cantidad, 0, "", "", $Motivo, "Ajuste_Entrada");
	else if ($Tipo == "Salida")
		AfectarInventario($Codigo, $Cantidad, 0, "", "", $Motivo, "Ajuste_Salida");
	//
}
else if (isset($_POST["Inventario_Importar"]))
{
	$data = $_POST["Inventario_Importar"];
	
	$query = "SELECT * FROM productos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Producto[$row["cod_fab"]] = $row["cod_fab"];
			$ProductoPeso[$row["cod_fab"]] = $row["peso"];
			$ProductoCosto[$row["cod_fab"]] = $row["costo"];
			$ProductoLista1[$row["cod_fab"]] = $row["lista1"];
			$ProductoLista2[$row["cod_fab"]] = $row["lista2"];
			$ProductoLista3[$row["cod_fab"]] = $row["lista3"];
			$ProductoLista4[$row["cod_fab"]] = $row["lista4"];
		}
	}
	
	foreach($data AS $item)
	{
		$Codigo = $item["Codigo"];
		$Nombre = $item["Nombre"];
		$Categoria = $item["Categoria"];
		$Grupo = $item["Grupo"];
		$SubGrupo = $item["SubGrupo"];
		$Costo = $item["Costo"];
		$Unidad = $item["Unidad"];
		$Peso = $item["Peso"];
		$Lista1 = $item["Lista1"];
		$Lista2 = $item["Lista2"];
		$Lista3 = $item["Lista3"];
		$Lista4 = $item["Lista4"];
		
		if ($Categoria == "" || $Grupo == "" || $SubGrupo == "" || $Unidad == "")
		{
			if (!isset($Producto[$Codigo]))
			{
				$NewData[] = Array(
					"Codigo" => $Codigo,
					"Nombre" => $Nombre,
					"Categoria" => $Categoria,
					"Grupo" => $Grupo,
					"SubGrupo" => $SubGrupo,
					"Costo" => $Costo,
					"Unidad" => $Unidad,
					"Peso" => $Peso,
					"Lista1" => $Lista1,
					"Lista2" => $Lista2,
					"Lista3" => $Lista3,
					"Lista4" => $Lista4,
				);
				continue;
			}
		}
		
		if (!isset($Producto[$Codigo]))
		{
			$query = "INSERT INTO productos (cod_fab, categoria, grupo, subgrupo, nombre, und_med, peso, costo, 
			ultimo_costo, costo_promedio, lista1, lista2, lista3, lista4, venta_promedio, digitado_por, 
			fecha_digitado) VALUES ('".$Codigo."', '".$Categoria."', '".$Grupo."', '".$SubGrupo."', 
			'".$Nombre."', '".$Unidad."', '".$Peso."', '".$Costo."', '".$Costo."', '".$Costo."', '".$Lista1."', 
			'".$Lista2."', '".$Lista3."', '".$Lista4."', '".$Costo."', '".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		}
		else
		{
			$query = "UPDATE productos SET ";
			$query .= "costo = '".$Costo."', ";
			//$query .= "peso = '".$Peso."', ";
			$query .= "lista1 = '".$Lista1."', ";
			$query .= "lista2 = '".$Lista2."', ";
			$query .= "lista3 = '".$Lista3."', ";
			$query .= "lista4 = '".$Lista4."' ";
			$query .= "WHERE cod_fab = '".$Codigo."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		}
		if (mysql_affected_rows() > 0)
		{
			// INSERT Log
			$query = "INSERT INTO import_log (fecha, digitado_por, codigo, peso_anterior, peso_nuevo, 
			costo_anterior, costo_nuevo, lista1_1, lista1_2, lista2_1, lista2_2, lista3_1, lista3_2, 
			lista4_1, lista4_2) VALUES (NOW(), '".$_SESSION["UserCode"]."', '".$Codigo."', ";
			$query .= isset($ProductoPeso[$Codigo]) ? "'".$ProductoPeso[$Codigo]."', ":"'0', ";
			$query .= "'".$Peso."', ";
			$query .= isset($ProductoCosto[$Codigo]) ? "'".$ProductoCosto[$Codigo]."', ":"'0', ";
			$query .= "'".$Costo."', ";
			$query .= isset($ProductoLista1[$Codigo]) ? "'".$ProductoLista1[$Codigo]."', ":"'0', ";
			$query .= "'".$Lista1."', ";
			$query .= isset($ProductoLista2[$Codigo]) ? "'".$ProductoLista2[$Codigo]."', ":"'0', ";
			$query .= "'".$Lista2."', ";
			$query .= isset($ProductoLista3[$Codigo]) ? "'".$ProductoLista3[$Codigo]."', ":"'0', ";
			$query .= "'".$Lista3."', ";
			$query .= isset($ProductoLista4[$Codigo]) ? "'".$ProductoLista4[$Codigo]."', ":"'0', ";
			$query .= "'".$Lista4."' ";
			$query .= ")";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		}
	}
	if (isset($NewData))
		echo json_encode($NewData);
	else
		echo json_encode(array());
}
else if (isset($_GET["Nomina_Extras_Crear"]))
{
	$data1 = $_GET["Nomina_Extras_Crear"];
	$data2 = $_GET["Nomina_Extras_Crear"];
	$final = false;
	$TotalDiurnas = 0;
	$TotalNocturnas = 0;
	
	foreach($data1 as $item)
	{
		if ($item["Nocturno"] == "false")
			$TotalDiurnas += $item["Total"];
		else
			$TotalNocturnas += $item["Total"];
	}
	
	//echo $TotalDiurnas;
	//echo "<br />";
	//echo $TotalNocturnas;
	//die();
	
	foreach($data2 as $item)
	{
		$Fecha = $item["Fecha"];
		$Hora_Ini = $item["Hora_Ini"];
		$Hora_Fin = $item["Hora_Fin"];
		$Total = $item["Total"];
		$Festivo = $item["Festivo"];
		$Nocturno = $item["Nocturno"];
		
		if ($final == false)
		{
			$Empleado = $item["Empleado"];
			$Autorizador = $item["Autorizador"];
			$Justificacion = $item["Justificacion"];
			$Comentario = $item["Comentario"];
			$Observacion = $item["Observacion"];
			
			$query = "SELECT id FROM nom_extras ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 5){
				$num = 5 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Interno = "HEX".$zero."".$id."";
			}
			else {
				$Interno = "HEX".$id."";
			}
		}
		
		$query = "INSERT INTO nom_extras_movs (interno, cliente_id, turno, hora_ini, hora_fin, total, nocturno, 
		festivo, estado) VALUES ('".$Interno."', '".$Empleado."', '".$Fecha."', '".$Hora_Ini."', '".$Hora_Fin."', 
		'".$Total."', '".$Nocturno."', '".$Festivo."', 'Pendiente')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		if ($final == false)
		{
			$query = "INSERT INTO nom_extras (interno, empleado_id, autorizador_id, justificacion, comentario, observacion, 
			total_diurnas, total_nocturnas, estado, digitado_por, fecha_digitado) VALUES ('".$Interno."', '".$Empleado."', 
			'".$Autorizador."', '".$Justificacion."', '".$Comentario."', '".$Observacion."', '".$TotalDiurnas."', 
			'".$TotalNocturnas."', 'Pendiente', '".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			$final = true;
		}
		
		$data[] = array(
			"Interno" => $Interno,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET["Nomina_Extras_Modificar"]))
{
	$data1 = $_GET["Nomina_Extras_Modificar"];
	$data2 = $_GET["Nomina_Extras_Modificar"];
	$final = false;
	$TotalDiurnas = 0;
	$TotalNocturnas = 0;
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM nom_extras WHERE interno = '".$data1[0]['Interno']."' AND estado = 'Pendiente'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	foreach($data1 as $item)
	{
		if ($item["Nocturno"] == "false")
			$TotalDiurnas += $item["Total"];
		else
			$TotalNocturnas += $item["Total"];
	}
	
	foreach($data2 as $item)
	{
		$Fecha = $item["Fecha"];
		$Hora_Ini = $item["Hora_Ini"];
		$Hora_Fin = $item["Hora_Fin"];
		$Total = $item["Total"];
		$Festivo = $item["Festivo"];
		$Nocturno = $item["Nocturno"];
		
		if ($final == false)
		{
			$Interno = $item["Interno"];
			$Empleado = $item["Empleado"];
			$Autorizador = $item["Autorizador"];
			$Justificacion = $item["Justificacion"];
			$Comentario = $item["Comentario"];
			$Observacion = $item["Observacion"];
			
			$query = "DELETE FROM nom_extras_movs WHERE interno = '".$Interno."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		}
		
		$query = "INSERT INTO nom_extras_movs (interno, cliente_id, turno, hora_ini, hora_fin, total, 
		nocturno, festivo, estado) VALUES ('".$Interno."', '".$Empleado."', '".$Fecha."', '".$Hora_Ini."', 
		'".$Hora_Fin."', '".$Total."', '".$Nocturno."', '".$Festivo."', 'Pendiente')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		if ($final == false)
		{
			$query = "UPDATE nom_extras SET 
			empleado_id = '".$Empleado."', 
			autorizador_id = '".$Autorizador."', 
			justificacion = '".$Justificacion."', 
			comentario = '".$Comentario."', 
			observacion = '".$Observacion."', 
			total_diurnas = '".$TotalDiurnas."', 
			total_nocturnas = '".$TotalNocturnas."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW() WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			$final = true;
		}
	}
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET["Nomina_Prestamos_Crear"]))
{
	$data = $_GET["Nomina_Prestamos_Crear"];
	//print_r($data);
	//die();
	
	foreach($data as $item)
	{
		$Fecha = $item["Fecha"];
		$Beneficiario = $item["Beneficiario"];
		$Acreedor = $item["Acreedor"];
		$TipoMov = $item["TipoMov"];
		$Valor = $item["Valor"];
		$Cuotas = $item["Cuotas"];
		$ValorCuotas = $item["ValorCuotas"];
		$Observacion = $item["Observacion"];
		$Caja = $item["Caja"];
		
		$query = "SELECT id FROM nom_prestamos ORDER BY id DESC LIMIT 1";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$id = $row["id"] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 4)
			{
				$num = 4 - $len;
				for ($a = 0; $a < $num; $a++)
					$zero .= "0";
				$Interno = "PR".$zero."".$id."";
			}
			else
				$Interno = "PR".$id."";
		}
		
		$Caja_Interno = "";
		$Caja_Recibo = $Interno;
		$FechaPago = "";
		$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 7)
			{
				$num = 7 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Caja_Interno = "MCJ".$zero."".$id."";
			}
			else {
				$Caja_Interno = "MCJ".$id."";
			}
		}
		
		//-- Caja Final
		$query = "INSERT INTO caja_final 
		(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, efectivo, 
		total, digitado_por, fecha_digitado, estado) VALUES 
		('".$Fecha."', '".$Caja_Interno."', '".$Caja_Recibo."', 'Egresos', 'Nómina', 'Préstamo', 'N/A', 
		'".$Beneficiario."', '".$Valor."', '".$Valor."', '".$Caja."', NOW(), 'Pendiente')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		
		$query = "INSERT INTO nom_prestamos (interno, fecha, beneficiario_id, acreedor_id, tipo_mov, valor, 
		cuotas, valor_cuotas, caja, forma_pago, observacion, estado, digitado_por, fecha_digitado) VALUES 
		('".$Interno."', '".$Fecha."', '".$Beneficiario."', '".$Acreedor."', '".$TipoMov."', '".$Valor."', '".$Cuotas."', 
		'".$ValorCuotas."', '".$Caja."', 'Efectivo', '".$Observacion."', 'Pendiente', '".$_SESSION["UserCode"]."', NOW())";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		
		for ($i = 0; $i < $Cuotas; $i++)
		{
			$Cuota = $i + 1;
			$Nombre = "Cuota_".$Cuota."_".$Interno;
			
			if ($i == 0)
			{
				$Day = date("d", strtotime($Fecha));
				$Month = date("n", strtotime($Fecha));
				$Year = date("Y", strtotime($Fecha));
				
				if ($Day <= 15)
				{
					// to 14
					$NextCuota = date("Y-n", strtotime("".$Fecha."+ ".$i." month"));
					$FechaPago = "".$NextCuota."-14";
				}
				else
				{
					// to 27
					$NextCuota = date("Y-n", strtotime("".$Fecha." + ".$i." month"));
					$FechaPago = "".$NextCuota."-27";
				}
			}
			else
			{
				for ($a = 0;;$a++)
				{
					$NextCuota = date("Y-n-d", strtotime("".$FechaPago." + ".($a+1)." days"));
					$Day = date("d", strtotime($NextCuota));
					
					if ($Day == 14 || $Day == 27)
					{
						$FechaPago = $NextCuota;
						break;
					}
					
					/*if ($Day == 27)
					{
						$FechaPago = $NextCuota;
						break;
					}*/
				}
			}
			
			$query = "INSERT INTO nom_prestamos_movs (interno, nombre, cuota, fecha, valor, estado) VALUES 
			('".$Interno."', '".$Nombre."', '".$Cuota."', '".$FechaPago."', '".$ValorCuotas."', 'Pendiente')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-1: ".mysql_error():"");
			
			$query = "INSERT INTO mov_clientes ( cliente_id, tipo_movimiento, valor, saldo, interno, 
			orden_compra, remision, factura, fecha, estado, digitado_por, fecha_digitado, caja_interno, 
			caja_recibo ) VALUES ('".$Beneficiario."', 'Debito', '".$ValorCuotas."', '".$ValorCuotas."', 
			'".$Nombre."', '".$Interno."', '".$Nombre."', '".$Nombre."', '".$FechaPago."', 
			'Pendiente', '".$_SESSION["UserCode"]."', NOW(), '".$Caja_Interno."', '".$Caja_Recibo."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
		}
		
		$newdata[] = array(
			"Interno" => $Interno,
		);
	}
	echo json_encode($newdata);
}
else if (isset($_GET["Nomina_Novedades_Crear"]))
{
	$data = $_GET["Nomina_Novedades_Crear"];
	$final = false;
	
	foreach($data as $item)
	{
		$Fecha = isset($item["Fecha"]) ? $item["Fecha"]:"";
		$Hora_Ini = isset($item["Hora_Ini"]) ? $item["Hora_Ini"]:"";
		$Hora_Fin = isset($item["Hora_Fin"]) ? $item["Hora_Fin"]:"";
		$Total = isset($item["Total"]) ? $item["Total"]:"";
		
		if ($final == false)
		{
			$Empleado = $item["Empleado"];
			$Reemplazo = $item["Reemplazo"];
			$Autorizador = $item["Autorizador"];
			$Justificacion = $item["Justificacion"];
			$Comentario = $item["Comentario"];
			$Novedad = $item["Novedad"];
			$Descontable = $item["Descontable"];
			$Remunerado100 = $item["Remunerado100"];
			$Remunerado66 = $item["Remunerado66"];
			$Cesantia = $item["Cesantia"];
			$Fecha_Ini = $item["Fecha_Ini"];
			$Fecha_Fin = $item["Fecha_Fin"];
			$FechaHora_Ini = $item["FechaHora_Ini"];
			$FechaHora_Fin = $item["FechaHora_Fin"];
			$Total_Novedad = $item["Total_Novedad"];
			$Total_Reposicion = $item["Total_Reposicion"];
			$Reposicion = $item["Reposicion"];
			$Observacion = $item["Observacion"];
			
			$query = "SELECT id FROM nom_novedades ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 5)
			{
				$num = 5 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Interno = "NOV".$zero."".$id."";
			}
			else {
				$Interno = "NOV".$id."";
			}
		}
		
		if ($Fecha != "")
		{
			$query = "INSERT INTO nom_novedades_movs (interno, fecha, hora_ini, hora_fin, total) VALUES 
			('".$Interno."', '".$Fecha."', '".$Hora_Ini."', '".$Hora_Fin."', '".$Total."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		}
		
		if ($final == false)
		{
			$query = "INSERT INTO nom_novedades (interno, empleado_id, reemplazo_id, autorizador_id, novedad, fecha_ini, hora_ini, 
			fecha_fin, hora_fin, justificacion, comentario, reposicion, observacion, horas_novedad, horas_reposicion, descontable, 
			remunerado100, remunerado66, cesantia, estado, digitado_por, fecha_digitado) VALUES ('".$Interno."', '".$Empleado."', 
			'".$Reemplazo."', '".$Autorizador."', '".$Novedad."', '".$Fecha_Ini."', '".$FechaHora_Ini."', '".$Fecha_Fin."', 
			'".$FechaHora_Fin."', '".$Justificacion."', '".$Comentario."', '".$Reposicion."', '".$Observacion."', '".$Total_Novedad."', 
			'".$Total_Reposicion."', '".$Descontable."', '".$Remunerado100."', '".$Remunerado66."', '".$Cesantia."', 'Pendiente', 
			'".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			$final = true;
			
			$newdata[] = array(
				"Interno" => $Interno,
			);
		}
	}
	echo json_encode($newdata);
}
else if (isset($_GET["Nomina_Novedades_Modificar"]))
{
	$data = $_GET["Nomina_Novedades_Modificar"];
	$final = false;
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM nom_novedades WHERE interno = '".$data[0]['Interno']."' AND estado = 'Pendiente'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	foreach($data as $item)
	{
		$Fecha = isset($item["Fecha"]) ? $item["Fecha"]:"";
		$Hora_Ini = isset($item["Hora_Ini"]) ? $item["Hora_Ini"]:"";
		$Hora_Fin = isset($item["Hora_Fin"]) ? $item["Hora_Fin"]:"";
		$Total = isset($item["Total"]) ? $item["Total"]:"";
		
		if ($final == false)
		{
			$Interno = $item["Interno"];
			$Empleado = $item["Empleado"];
			$Reemplazo = $item["Reemplazo"];
			$Autorizador = $item["Autorizador"];
			$Justificacion = $item["Justificacion"];
			$Comentario = $item["Comentario"];
			$Novedad = $item["Novedad"];
			$Descontable = $item["Descontable"];
			$Remunerado100 = $item["Remunerado100"];
			$Remunerado66 = $item["Remunerado66"];
			$Cesantia = $item["Cesantia"];
			$Fecha_Ini = $item["Fecha_Ini"];
			$Fecha_Fin = $item["Fecha_Fin"];
			$FechaHora_Ini = $item["FechaHora_Ini"];
			$FechaHora_Fin = $item["FechaHora_Fin"];
			$Total_Novedad = $item["Total_Novedad"];
			$Total_Reposicion = $item["Total_Reposicion"];
			$Reposicion = $item["Reposicion"];
			$Observacion = $item["Observacion"];
			
			$query = "DELETE FROM nom_novedades_movs WHERE interno = '".$Interno."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		}
		
		if ($Fecha != "")
		{
			$query = "INSERT INTO nom_novedades_movs (interno, fecha, hora_ini, hora_fin, total) VALUES 
			('".$Interno."', '".$Fecha."', '".$Hora_Ini."', '".$Hora_Fin."', '".$Total."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		}
		 
		if ($final == false)
		{
			$query = "UPDATE nom_novedades SET  
			empleado_id = '".$Empleado."', 
			reemplazo_id = '".$Reemplazo."', 
			autorizador_id = '".$Autorizador."', 
			novedad = '".$Novedad."', 
			fecha_ini = '".$Fecha_Ini."', 
			hora_ini = '".$FechaHora_Ini."', 
			fecha_fin = '".$Fecha_Fin."', 
			hora_fin = '".$FechaHora_Fin."', 
			justificacion = '".$Justificacion."', 
			comentario = '".$Comentario."', 
			reposicion = '".$Reposicion."', 
			observacion = '".$Observacion."', 
			horas_novedad = '".$Total_Novedad."', 
			horas_reposicion = '".$Total_Reposicion."', 
			descontable = '".$Descontable."', 
			remunerado100 = '".$Remunerado100."', 
			remunerado66 = '".$Remunerado66."', 
			cesantia = '".$Cesantia."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW() 
			WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			$final = true;
		}
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Nomina_Aprobar_Prestamos']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Pendiente";
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:die();
	$ClienteID = isset($_GET['BeneficiarioID']) ? $_GET['BeneficiarioID']:"";
	$Valor = isset($_GET['Valor']) ? $_GET['Valor']:"0";
	$Caja = isset($_GET['Caja']) ? $_GET['Caja']:"";
	$DigitadorID = isset($_GET['DigitadorID']) ? $_GET['DigitadorID']:"";
	
	$query = "UPDATE nom_prestamos SET 
	estado = '".$Estado."'";
	$query .= ($Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
	$query .= ($Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
	$query .= " WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if ($Estado == "Aprobado")
	{
		$query = "UPDATE nom_prestamos_movs SET estado = 'Aprobado' WHERE interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		
		/*$query = "UPDATE caja_final SET estado = 'Aprobado', aprobado_por = '".$_SESSION["UserCode"]."', 
		fecha_aprobado = NOW() WHERE caja_interno = '".$Caja_Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
		
		$query = "UPDATE mov_clientes SET estado = 'Aprobado', aprobado_por = '".$_SESSION["UserCode"]."', 
		fecha_aprobado = NOW() WHERE interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");*/
		
		/*$Caja_Interno = "";
		$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 7){
				$num = 7 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Caja_Interno = "MCJ".$zero."".$id."";
			}
			else {
				$Caja_Interno = "MCJ".$id."";
			}
		}
		
		//-- Caja Final
		$query = "INSERT INTO caja_final (fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, 
		subgrupo2, cliente_id, efectivo, total, saldo, digitado_por, fecha_digitado, estado) VALUES 
		('".$Fecha."', '".$Caja_Interno."', '".$Caja_Interno."', 'Egresos', 'Nómina', 'Préstamo', 'N/A.', 
		'".$ClienteID."', '".$Valor."', '".$Valor."', '".$Valor."', '".$Caja."', NOW(), 'Aprobado')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		//---
		$query = "SELECT * FROM nom_prestamos_movs WHERE interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result))
			{
				$query = "INSERT INTO mov_clientes ( cliente_id, tipo_movimiento, valor, saldo, interno, 
				orden_compra, remision, factura, fecha, estado, digitado_por, fecha_digitado, caja_interno, 
				caja_recibo ) VALUES ('".$ClienteID."', 'Debito', '".$row["valor"]."', '".$row["valor"]."', 
				'".$Interno."', '".$row["nombre"]."', '".$row["nombre"]."', '".$row["nombre"]."', '".$row["fecha"]."', 
				'Pendiente', '".$DigitadorID."', NOW(), '".$Caja_Interno."', '".$Caja_Interno."')";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
			}
		}*/
	}
	
	if ($Estado == "Anulado")
	{
		$query = "UPDATE nom_prestamos_movs SET estado = 'Anulado' WHERE interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		
		$query = "SELECT * FROM mov_clientes WHERE orden_compra = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			
			$query = "UPDATE mov_clientes SET 
			estado = 'Anulado', 
			anulado_por = '".$_SESSION["UserCode"]."', 
			motivo_anulado = 'Anulado Automaticamente Desde Nomina -> Aprobar Prestamos.', 
			fecha_anulado = NOW() 
			WHERE orden_compra = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
			
			Anular_Caja_Recibo($row["caja_interno"], $row["caja_recibo"]);
		}
	}
}
else if (isset($_GET['Nomina_Aprobar_Novedades']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Pendiente";
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:die();
	
	$query = "UPDATE nom_novedades SET 
	estado = '".$Estado."'";
	$query .= ($Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
	$query .= ($Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
	$query .= " WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET['Nomina_Aprobar_Extras']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Pendiente";
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:die();
	
	$query = "UPDATE nom_extras SET 
	estado = '".$Estado."'";
	$query .= ($Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
	$query .= ($Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
	$query .= " WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	//---
	$query = "UPDATE nom_extras_movs SET estado = '".$Estado."' WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
}
else if (isset($_GET["Nomina_Salarios_Agregar"]))
{
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:die();
	
	$query = "INSERT INTO contratos (cliente_id) VALUES ('".$ClienteID."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Nomina_Salarios_Actualizar"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:die();
	$Cargo = isset($_GET["Cargo"]) ? $_GET["Cargo"]:die();
	$Basico = isset($_GET["Basico"]) ? $_GET["Basico"]:die();
	$Transp = isset($_GET["Transp"]) ? $_GET["Transp"]:die();
	$Bono = isset($_GET["Bono"]) ? $_GET["Bono"]:die();
	$Horas = isset($_GET["Horas"]) ? $_GET["Horas"]:die();
	$Hora_Ini1 = isset($_GET["Hora_Ini1"]) ? $_GET["Hora_Ini1"]:die();
	$Hora_Fin1 = isset($_GET["Hora_Fin1"]) ? $_GET["Hora_Fin1"]:die();
	$Hora_Ini2 = isset($_GET["Hora_Ini2"]) ? $_GET["Hora_Ini2"]:die();
	$Hora_Fin2 = isset($_GET["Hora_Fin2"]) ? $_GET["Hora_Fin2"]:die();
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:die();
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:die();
	$RH = isset($_GET["RH"]) ? $_GET["RH"]:die();
	$Salud = isset($_GET["Salud"]) ? $_GET["Salud"]:die();
	$Pension = isset($_GET["Pension"]) ? $_GET["Pension"]:die();
	$Contrato = isset($_GET["Contrato"]) ? $_GET["Contrato"]:die();
	$Cesantia = isset($_GET["Cesantia"]) ? $_GET["Cesantia"]:die();
	$Activo = isset($_GET["Activo"]) ? $_GET["Activo"]:die();
	
	$query = "UPDATE contratos SET 
	cliente_id = '".$ClienteID."', 
	charge = '".$Cargo."', 
	type = '".$Contrato."', 
	starts = '".$Fecha_Ini."', 
	ends = '".$Fecha_Fin."', 
	turn1_starts = '".$Hora_Ini1."', 
	turn1_ends = '".$Hora_Fin1."', 
	turn2_starts = '".$Hora_Ini2."', 
	turn2_ends = '".$Hora_Fin2."', 
	hlab = '".$Horas."', 
	basic = '".$Basico."', 
	transp = '".$Transp."', 
	bonus = '".$Bono."', 
	pension = '".$Pension."', 
	health = '".$Salud."', 
	rh = '".$RH."', 
	outgoing = '".$Cesantia."', 
	active = '".$Activo."' 
	WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Nomina_Salarios_Borrar"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM contratos WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() <= 0)
		echo "Error";
}
else if (isset($_POST["Nomina"]))
{
	$data = $_POST["Nomina"];
	$Quincenal = isset($_POST["Quincenal"]) ? $_POST["Quincenal"]:false;
	if ($Quincenal == "false")
		$Quincenal = false;
	else if ($Quincenal == "true")
		$Quincenal = true;
	$SubGrupo =  isset($_POST["SubGrupo"]) ? $_POST["SubGrupo"]:die();
	if ($SubGrupo == "Mensual")
		$SubGrupo = "Pago Mensual";
	else if ($SubGrupo == "Quincenal")
		$SubGrupo = "Pago quincena";
	$SubGrupo2 = isset($_POST["SubGrupo2"]) ? $_POST["SubGrupo2"]:die();
		if ($SubGrupo2 == "")
		$SubGrupo2 = "N/A";
	
	$Total = isset($_POST["Total"]) ? $_POST["Total"]:die();
	
	//CREAR RECIBO DE CAJA CON EL TOTAL A PAGAR.
	/*$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	
	$row = mysql_fetch_array($result);
	$id = $row['id'] + 1;
	$len = strlen($id);
	$zero = "";
	if ($len < 7)
	{
		$num = 7 - $len;
		for ($a = 0; $a < $num; $a++)
			$zero .= "0";
		$Caja_Interno = "MCJ".$zero."".$id."";
	}
	else
		$Caja_Interno = "MCJ".$id."";
	
	$query = "INSERT INTO caja_final 
	(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, efectivo, total, 
	digitado_por, fecha_digitado, estado) VALUES (NOW(), '".$Caja_Interno."', '".$Caja_Interno."', 'Egresos', 
	'Nómina', '".$SubGrupo."', '".$SubGrupo2."', '".$MAIN_ID."', '".$Total."', '".$Total."', 'SISTEMA', NOW(), 'Aprobado')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");*/
	
	$query = "SELECT * FROM par_nom_retencion ORDER BY salario DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$RetencionValor[] = array(
				"Salario" => $row["salario"],
				"Valor" => $row["valor"],
			);
		}
	}
	
	$query = "SELECT * FROM par_nom";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			switch($row["nombre"])
			{
				case "Salario Minimo":
					$BasicoMinimo = $row["valor"];
				break;
				case "Salud":
					$SaludValor = number_format($row["valor"],2);
				break;
				case "Pension":
					$PensionValor = number_format($row["valor"],2);
				break;
				case "Cesantia":
					$CesantiaValor = $row["valor"];
				break;
			}
		}
	}
	
	$Fecha_Ini = $data[0]["Fecha_Ini"];
	$Fecha_Fin = $data[0]["Fecha_Fin"];
	
	foreach($data as $item)
	{
		$Clientes[] = $item["ClienteID"];
		
		// INSERTAR NOMINA
		/*$query = "INSERT INTO nomina (fecha_ini, fecha_fin, nombre, cliente_id, basico, horas, horas_desc, horas_rep, horas_ext, 
		horas_lab, basico_final, transporte, bono, extras, licencias, devengado, salud, pension, retencion, prestamo, libranza, 
		anticipo, donacion, multa, deducido, deuda, neto, cesantia, digitado_por, fecha_digitado) VALUES ('".$item["Fecha_Ini"]."', 
		'".$item["Fecha_Fin"]."', '".$item["Nombre"]."', '".$item["ClienteID"]."', '".$item["Basico"]."', '".$item["Horas"]."', 
		'".$item["Horas_Desc"]."', '".$item["Horas_Rep"]."', '".$item["Horas_Ext"]."', '".$item["Horas_Lab"]."', 
		'".$item["Basico_Final"]."', '".$item["Transporte"]."', '".$item["Bono"]."', '".$item["Extras"]."', '".$item["Licencias"]."', 
		'".$item["Devengado"]."', '".$item["Salud"]."', '".$item["Pension"]."', '".$item["Retencion"]."', '".$item["Prestamo"]."', 
		'".$item["Libranza"]."', '".$item["Anticipo"]."', '".$item["Donacion"]."', '".$item["Multa"]."', '".$item["Deducido"]."', 
		'".$item["Deuda"]."', '".$item["Neto"]."', '".$item["Cesantia"]."', '".$_SESSION["UserCode"]."', NOW())";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");*/
	}
	
	$Clause = implode("', '", $Clientes);
	
	$Old_ID = "_empty_";
	//PRESTAMOS
	$query = "SELECT * FROM nom_prestamos_movs WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' 
	AND '".$Fecha_Fin."' AND estado = 'Aprobado' ORDER BY fecha";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$PrestamosInterno[] = $row["interno"];
		}
	}
	
	$Clause2 = implode("', '", $PrestamosInterno);
	
	$query = "SELECT * FROM nom_prestamos WHERE interno IN ('".$Clause2."') AND beneficiario_id IN ('".$Clause."') ORDER BY beneficiario_id";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			switch($row["tipo_mov"])
			{
				case "Préstamo":
					if (isset($TotalPrestamos[$row["beneficiario_id"]]))
						$TotalPrestamos[$row["beneficiario_id"]] += $row["valor"];
					else
						$TotalPrestamos[$row["beneficiario_id"]] = $row["valor"];
				break;
				
				case "Libranza":
					if (isset($TotalLibranzas[$row["beneficiario_id"]]))
						$TotalLibranzas[$row["beneficiario_id"]] += $row["valor"];
					else
						$TotalLibranzas[$row["beneficiario_id"]] = $row["valor"];
				break;
				
				case "Anticipo":
					if (isset($TotalAnticipos[$row["beneficiario_id"]]))
						$TotalAnticipos[$row["beneficiario_id"]] += $row["valor"];
					else
						$TotalAnticipos[$row["beneficiario_id"]] = $row["valor"];
				break;
				
				case "Donacion":
					if (isset($TotalDonacion[$row["beneficiario_id"]]))
						$TotalDonacion[$row["beneficiario_id"]] += $row["valor"];
					else
						$TotalDonacion[$row["beneficiario_id"]] = $row["valor"];
				break;
				
				case "Multas":
					if (isset($TotalMultas[$row["beneficiario_id"]]))
						$TotalMultas[$row["beneficiario_id"]] += $row["valor"];
					else
						$TotalMultas[$row["beneficiario_id"]] = $row["valor"];
				break;
				
				default:
				break;
			}
		}
	}
	
	$Old_ID = "_empty_";
	//NOVEDADES
	$query = "SELECT * FROM nom_novedades WHERE DATE(fecha_ini) BETWEEN '".$Fecha_Ini."' 
	AND '".$Fecha_Fin."' AND estado = 'Aprobado' AND empleado_id IN ('".$Clause."') ORDER BY empleado_id";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($Old_ID != $row["empleado_id"])
				$Old_ID = $row["empleado_id"];
			
			if ($row["novedad"] == "Vacaciones")
			{
				if (!isset($Vacaciones[$Old_ID]))
					$Vacaciones[$Old_ID] = true;
			}
			
			if ($row["descontable"] == "true")
			{
				if (isset($HorasDescontadas[$Old_ID]))
					$HorasDescontadas[$Old_ID] += $row["horas_novedad"];
				else
					$HorasDescontadas[$Old_ID] = $row["horas_novedad"];
			}
			
			if ($row["remunerado100"] == "true")
			{
				if (isset($Horas100[$Old_ID]))
					$Horas100[$Old_ID] += $row["horas_novedad"];
				else
					$Horas100[$Old_ID] = $row["horas_novedad"];
			}
			
			if ($row["remunerado66"] == "true")
			{
				if (isset($Horas66[$Old_ID]))
					$Horas66[$Old_ID] += $row["horas_novedad"];
				else
					$Horas66[$Old_ID] = $row["horas_novedad"];
			}
			
			if ($row["cesantia"] == "true")
			{
				if (isset($HorasCesantia[$Old_ID]))
					$HorasCesantia[$Old_ID] += $row["horas_novedad"];
				else
					$HorasCesantia[$Old_ID] = $row["horas_novedad"];
			}
			
			if ($row["reposicion"] == "Reponer tiempo")
			{
				if (isset($HorasReposicion[$Old_ID]))
					$HorasReposicion[$Old_ID] += $row["horas_reposicion"];
				else
					$HorasReposicion[$Old_ID] = $row["horas_reposicion"];
			}
			
			if ($row["reposicion"] == "Canje x hora extra")
			{
				if (isset($HorasCanje[$Old_ID]))
					$HorasCanje[$Old_ID] += $row["horas_novedad"];
				else
					$HorasCanje[$Old_ID] = $row["horas_novedad"];
			}
		}
	}
	
	$Old_ID = "_empty_";
	//HORAS EXTRAS
	$query = "SELECT * FROM nom_extras_movs WHERE DATE(turno) BETWEEN '".$Fecha_Ini."' 
	AND '".$Fecha_Fin."' AND estado = 'Aprobado' AND cliente_id IN ('".$Clause."') ORDER BY cliente_id";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($Old_ID != $row["cliente_id"])
				$Old_ID = $row["cliente_id"];
			
			if ($row["nocturno"] == "false" && $row["festivo"] == "false")
			{
				if (isset($HorasDiurnas[$Old_ID]))
					$HorasDiurnas[$Old_ID] += $row["total"];
				else
					$HorasDiurnas[$Old_ID] = $row["total"];
			}
			
			if ($row["nocturno"] == "true" && $row["festivo"] == "false")
			{
				if (isset($HorasNocturnas[$Old_ID]))
					$HorasNocturnas[$Old_ID] += $row["total"];
				else
					$HorasNocturnas[$Old_ID] = $row["total"];
			}
			
			if ($row["nocturno"] == "false" && $row["festivo"] == "true")
			{
				if (isset($HorasDiurnasFestivas[$Old_ID]))
					$HorasDiurnasFestivas[$Old_ID] += $row["total"];
				else
					$HorasDiurnasFestivas[$Old_ID] = $row["total"];
			}
			
			if ($row["nocturno"] == "true" && $row["festivo"] == "true")
			{
				if (isset($HorasNocturnasFestivas[$Old_ID]))
					$HorasNocturnasFestivas[$Old_ID] += $row["total"];
				else
					$HorasNocturnasFestivas[$Old_ID] = $row["total"];
			}
		}
	}
	
	//TOTAL
	$query = "SELECT * FROM contratos WHERE cliente_id IN ('".$Clause."') AND active = 'true' GROUP BY cliente_id";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			//Prestamos
			$Prestamos = isset($TotalPrestamos[$row["cliente_id"]]) ? $TotalPrestamos[$row["cliente_id"]]:0;
			$Libranzas = isset($TotalLibranzas[$row["cliente_id"]]) ? $TotalLibranzas[$row["cliente_id"]]:0;
			$Anticipos = isset($TotalAnticipos[$row["cliente_id"]]) ? $TotalAnticipos[$row["cliente_id"]]:0;
			$Donacion = isset($TotalDonacion[$row["cliente_id"]]) ? $TotalDonacion[$row["cliente_id"]]:0;
			$Multas = isset($TotalMultas[$row["cliente_id"]]) ? $TotalMultas[$row["cliente_id"]]:0;
			//Novedades
			$Remunerado100 = isset($Horas100[$row["cliente_id"]]) ? $Horas100[$row["cliente_id"]]:0;
			$Remunerado66 = isset($Horas66[$row["cliente_id"]]) ? $Horas66[$row["cliente_id"]]:0;
			$Horas_Desc = isset($HorasDescontadas[$row["cliente_id"]]) ? $HorasDescontadas[$row["cliente_id"]]:0;
			$Horas_Rep = isset($HorasReposicion[$row["cliente_id"]]) ? $HorasReposicion[$row["cliente_id"]]:0;
			$Horas_Can = isset($HorasCanje[$row["cliente_id"]]) ? $HorasCanje[$row["cliente_id"]]:0;
			$Horas_Cesantias = isset($HorasCesantia[$row["cliente_id"]]) ? $HorasCesantia[$row["cliente_id"]]:0;
			//Extras
			$Diurnas = isset($HorasDiurnas[$row["cliente_id"]]) ? $HorasDiurnas[$row["cliente_id"]]:0;
			$Nocturnas = isset($HorasNocturnas[$row["cliente_id"]]) ? $HorasNocturnas[$row["cliente_id"]]:0;
			$DiurnasFestivo = isset($HorasDiurnasFestivas[$row["cliente_id"]]) ? $HorasDiurnasFestivas[$row["cliente_id"]]:0;
			$NocturnasFestivo = isset($HorasNocturnasFestivas[$row["cliente_id"]]) ? $HorasNocturnasFestivas[$row["cliente_id"]]:0;
			//---
			$HorasTmp = $Diurnas - $Horas_Can;
			if ($HorasTmp < 0)
			{
				$Horas_Desc -= $Diurnas;
				$Diurnas = 0;
			}
			else
			{
				$Horas_Desc -= $Horas_Can;
				$Diurnas = $HorasTmp;
			}
			$HorasExt = $Diurnas + $Nocturnas + $DiurnasFestivo + $NocturnasFestivo;
			$ValorHora = ($row["basic"] <= $BasicoMinimo && $row["hlab"] == 240) ? ($BasicoMinimo / $row["hlab"]):($row["basic"] / $row["hlab"]);
			if ($Quincenal == true)
			{
				$HorasLab = ((($row["hlab"] / 2) - $Horas_Desc) + $Horas_Rep);
				$HorasLab2 = $row["hlab"] / 2;
				$HorasLab3 = (($row["hlab"] / 2) - $Horas_Cesantias);
			}
			else
			{
				$HorasLab = (($row["hlab"] - $Horas_Desc) + $Horas_Rep);
				$HorasLab2 = $row["hlab"];
				$HorasLab3 = ($row["hlab"] - $Horas_Cesantias);
			}
			
			$BasicoFinal = $ValorHora * $HorasLab;
			$BasicoFinal2 = $ValorHora * $HorasLab2;
			$Transporte = ($row["transp"] / $row["hlab"]) * $HorasLab;
			$Transporte2 = ($row["transp"] / $row["hlab"]) * $HorasLab2;
			$Bono = ($row["bonus"] / $row["hlab"]) * $HorasLab;
			//---
			$ValorHoraDiurna = ($ValorHora * $Diurnas) * 1.25;
			$ValorHoraNocturna = ($ValorHora * $Nocturnas) * 1.75;
			$ValorHoraDiurnaFestivo = ($ValorHora * $DiurnasFestivo) * 2;
			$ValorHoraNocturnaFestivo = ($ValorHora * $NocturnasFestivo) * 2.5;
			//---
			$Extras = $ValorHoraDiurna + $ValorHoraNocturna + $ValorHoraDiurnaFestivo + $ValorHoraNocturnaFestivo;
			//---
			$Horas66_1 = 0;
			$Horas66_2 = 0;
			
			if ($Remunerado66 > 24)
			{
				$Horas66_1 = 24;
				$Horas66_2 = $Remunerado66 - 24;
			}
			else
				$Horas66_1 = $Remunerado66;
			
			//basic mini / 30 = diario min
			//diario min /240 = valor hora minima.
									// 100%						// 1ros 3 dias = 66%			// +3 dias = 100%
			$row["basic"] <= $BasicoMinimo ? 
			$Licencias = ($ValorHora * $Remunerado100) + ($ValorHora * $Horas66_1) + ($ValorHora * $Horas66_2)
			:
			$Licencias = ($ValorHora * $Remunerado100) + ($ValorHora * $Horas66_1) + (($ValorHora * 0.6667) * $Horas66_2);
			
			$Devengado = $BasicoFinal + $Transporte + $Extras + $Bono + $Licencias;
			$Devengado2 = $BasicoFinal2 + $Transporte2;
			
			if ($Quincenal == true)
			{
				if ($DESC_SALUD == true && $BasicoFinal < ($BasicoMinimo / 2))
				{
					// Descuento Autorizado
					$Salud = (($row["basic"] / 2) + $Extras) * $SaludValor;//0.04;
					$Pension = (($row["basic"] / 2) + $Extras) * $PensionValor;//0.04;
				}
				else if ($BasicoFinal <= ($BasicoMinimo / 2))
				{
					$Salud = ($BasicoMinimo / 2) * $SaludValor;//0.04;
					$Pension = ($BasicoMinimo / 2) * $PensionValor;//0.04;
				}
				else
				{
					$Salud = (($row["basic"] / 2) + $Extras) * $SaludValor;//0.04;
					$Pension = (($row["basic"] / 2) + $Extras) * $PensionValor;//0.04;
				}
				
				if ($DESC_SALUD == true && $BasicoFinal2 < ($BasicoMinimo / 2))
				{
					// Descuento Autorizado
					$Salud2 = ($row["basic"] / 2) * $SaludValor;//0.04;
					$Pension2 = ($row["basic"] / 2) * $PensionValor;//0.04;
				}
				else if ($BasicoFinal2 <= ($BasicoMinimo / 2))
				{
					$Salud2 = ($BasicoMinimo / 2) * $SaludValor;//0.04;
					$Pension2 = ($BasicoMinimo / 2) * $PensionValor;//0.04;
				}
				else
				{
					$Salud2 = ($row["basic"] / 2) * $SaludValor;//0.04;
					$Pension2 = ($row["basic"] / 2) * $PensionValor;//0.04;
				}
			}
			else
			{
				if ($DESC_SALUD == true && $BasicoFinal < $BasicoMinimo)
				{
					// Descuento Autorizado
					$Salud = ($row["basic"] + $Extras) * $SaludValor;//0.04;
					$Pension = ($row["basic"] + $Extras) * $PensionValor;//0.04;
				}
				else if ($BasicoFinal <= $BasicoMinimo)
				{
					$Salud = ($Devengado - $Transporte) * $SaludValor;//0.04;
					$Pension = ($Devengado - $Transporte)  * $PensionValor;//0.04;
				}
				else
				{
					$Salud = ($row["basic"] + $Extras) * $SaludValor;//0.04;
					$Pension = ($row["basic"] + $Extras) * $PensionValor;//0.04;
				}
				
				if ($DESC_SALUD == true && $BasicoFinal2 < $BasicoMinimo)
				{
					// Descuento Autorizado
					$Salud2 = $row["basic"] * $SaludValor;//0.04;
					$Pension2 = $row["basic"] * $PensionValor;//0.04;
				}
				else if ($BasicoFinal2 <= $BasicoMinimo)
				{
					$Salud2 = $BasicoMinimo * $SaludValor;//0.04;
					$Pension2 = $BasicoMinimo * $PensionValor;//0.04;
				}
				else
				{
					$Salud2 = $row["basic"] * $SaludValor;//0.04;
					$Pension2 = $row["basic"] * $PensionValor;//0.04;
				}
			}
			
			$Cesantia = ($row["basic"] / $row["hlab"]) * $HorasLab3 * $CesantiaValor;//0.0833;
			
			//---
			$Retencion = 0;
			if (isset($RetencionValor))
			{
				$len = count($RetencionValor);
				for ($i = 0; $i < $len; $i++)
				{
					if ($row["basic"] >= $RetencionValor[$i]["Salario"]) {
						$Retencion = $RetencionValor[$i]["Valor"];
						break;
					}
				}
			}
			
			//---
			$Deducido = $Salud + $Pension + $Retencion + $Prestamos + $Libranzas + $Anticipos + $Donacion + $Multas;
			$Deducido2 = $Salud2 + $Pension2;
			$DeudaFutura = 0;
			
			//----------------------------------------------------------------
			//----------------------- SALDAR DEUDAS
			//----------------------------------------------------------------
			
			// Check Vacaciones
			if ($Devengado - $Deducido < 0 && isset($Vacaciones[$row["cliente_id"]]))
			{
				// SALUD, PENSION Y RETENCION
				$DevengadoTmp = $Devengado - $Salud;
				if ($DevengadoTmp > 0)
					$DevengadoTmp -= $Pension;
				else
				{
					//CREAR DEUDA SALUD
					$Day = date("d", strtotime($Fecha_Ini));
					$Month = date("n", strtotime($Fecha_Ini));
					$Year = date("Y", strtotime($Fecha_Ini));
					
					if ($Day < 15)
					{
						// to 27
						$Day = 27;
						$FechaPago = date("Y-n-d", strtotime("".$Year."-".$Month."-".$Day.""));
					}
					else
					{
						// to 14
						$Day = 14;
						$Month += 1;
						$FechaPago = date("Y-n-d", strtotime("".$Year."-".$Month."-".$Day.""));
					}
					
					$query = "SELECT id FROM nom_prestamos ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$id = $row["id"] + 1;
						$len = strlen($id);
						$zero = "";
						if ($len < 4)
						{
							$num = 4 - $len;
							for ($a = 0; $a < $num; $a++)
								$zero .= "0";
							$Interno = "PR".$zero."".$id."";
						}
						else
							$Interno = "PR".$id."";
					}
					$Nombre = "Cuota_1_".$Interno;
					
					$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$id = $row["id"] + 1;
						$len = strlen($id);
						$zero = "";
						if ($len < 7)
						{
							$num = 7 - $len;
							for ($a = 0; $a < $num; $a++)
								$zero .= "0";
							$Caja_Interno2 = "MCJ".$zero."".$id."";
						}
						else
							$Caja_Interno2 = "MCJ".$id."";
					}
					
					//-- Caja Final
					$query = "INSERT INTO caja_final 
					(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, efectivo, 
					total, digitado_por, fecha_digitado, estado) VALUES 
					('".$Fecha."', '".$Caja_Interno2."', '".$Caja_Interno2."', 'Egresos', 'Nómina', 'Préstamo', 'N/A', 
					'".$row["cliente_id"]."', '".$Salud."', '".$Salud."', '".$_SESSION["UserCode"]."', NOW(), 'Aprobado')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
					
					$query = "INSERT INTO nom_prestamos (interno, fecha, beneficiario_id, acreedor_id, tipo_mov, valor, 
					cuotas, valor_cuotas, caja, forma_pago, observacion, estado, digitado_por, fecha_digitado) VALUES 
					('".$Interno."', '".$Fecha."', '".$row["cliente_id"]."', '".$MAIN_ID."', '".$TipoMov."', '".$Salud."', '1', 
					'".$Salud."', '".$_SESSION["UserCode"]."', 'Efectivo', 'Generado Automaticamente desde Nomina por Insuficiente Saldo para Cubir Salud.', 
					'Aprobado', '".$_SESSION["UserCode"]."', NOW())";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
					
					$query = "INSERT INTO nom_prestamos_movs (interno, nombre, cuota, fecha, valor, estado) VALUES 
					('".$Interno."', '".$Nombre."', '1', '".$FechaPago."', '".$Salud."', 'Pendiente')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-1: ".mysql_error():"");
					
					$query = "INSERT INTO mov_clientes ( cliente_id, tipo_movimiento, valor, saldo, interno, 
					orden_compra, remision, factura, fecha, estado, digitado_por, fecha_digitado, caja_interno, 
					caja_recibo ) VALUES ('".$row["cliente_id"]."', 'Debito', '".$Salud."', '".$Salud."', 
					'".$Nombre."', '".$Interno."', '".$Nombre."', '".$Nombre."', '".$FechaPago."', 
					'Pendiente', '".$_SESSION["UserCode"]."', NOW(), '".$Caja_Interno2."', '".$Caja_Interno2."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
					
					continue;
				}
				
				if ($DevengadoTmp > 0)
					$DevengadoTmp -= $Retencion;
				else
				{
					//CREAR DEUDA PENSION
					$Day = date("d", strtotime($Fecha_Ini));
					$Month = date("n", strtotime($Fecha_Ini));
					$Year = date("Y", strtotime($Fecha_Ini));
					
					if ($Day < 15)
					{
						// to 27
						$Day = 27;
						$FechaPago = date("Y-n-d", strtotime("".$Year."-".$Month."-".$Day.""));
					}
					else
					{
						// to 14
						$Day = 14;
						$Month += 1;
						$FechaPago = date("Y-n-d", strtotime("".$Year."-".$Month."-".$Day.""));
					}
					
					$query = "SELECT id FROM nom_prestamos ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$id = $row["id"] + 1;
						$len = strlen($id);
						$zero = "";
						if ($len < 4)
						{
							$num = 4 - $len;
							for ($a = 0; $a < $num; $a++)
								$zero .= "0";
							$Interno = "PR".$zero."".$id."";
						}
						else
							$Interno = "PR".$id."";
					}
					$Nombre = "Cuota_1_".$Interno;
					
					$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$id = $row["id"] + 1;
						$len = strlen($id);
						$zero = "";
						if ($len < 7)
						{
							$num = 7 - $len;
							for ($a = 0; $a < $num; $a++)
								$zero .= "0";
							$Caja_Interno2 = "MCJ".$zero."".$id."";
						}
						else
							$Caja_Interno2 = "MCJ".$id."";
					}
					
					//-- Caja Final
					$query = "INSERT INTO caja_final 
					(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, efectivo, 
					total, digitado_por, fecha_digitado, estado) VALUES 
					('".$Fecha."', '".$Caja_Interno2."', '".$Caja_Interno2."', 'Egresos', 'Nómina', 'Préstamo', 'N/A', 
					'".$row["cliente_id"]."', '".$Pension."', '".$Pension."', '".$_SESSION["UserCode"]."', NOW(), 'Aprobado')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
					
					$query = "INSERT INTO nom_prestamos (interno, fecha, beneficiario_id, acreedor_id, tipo_mov, valor, 
					cuotas, valor_cuotas, caja, forma_pago, observacion, estado, digitado_por, fecha_digitado) VALUES 
					('".$Interno."', '".$Fecha."', '".$row["cliente_id"]."', '".$MAIN_ID."', '".$TipoMov."', '".$Pension."', '1', 
					'".$Pension."', '".$_SESSION["UserCode"]."', 'Efectivo', 'Generado Automaticamente desde Nomina por Insuficiente Saldo para Cubir Pension.', 
					'Aprobado', '".$_SESSION["UserCode"]."', NOW())";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
					
					$query = "INSERT INTO nom_prestamos_movs (interno, nombre, cuota, fecha, valor, estado) VALUES 
					('".$Interno."', '".$Nombre."', '1', '".$FechaPago."', '".$Pension."', 'Pendiente')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-1: ".mysql_error():"");
					
					$query = "INSERT INTO mov_clientes ( cliente_id, tipo_movimiento, valor, saldo, interno, 
					orden_compra, remision, factura, fecha, estado, digitado_por, fecha_digitado, caja_interno, 
					caja_recibo ) VALUES ('".$row["cliente_id"]."', 'Debito', '".$Pension."', '".$Pension."', 
					'".$Nombre."', '".$Interno."', '".$Nombre."', '".$Nombre."', '".$FechaPago."', 
					'Pendiente', '".$_SESSION["UserCode"]."', NOW(), '".$Caja_Interno2."', '".$Caja_Interno2."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
					
					continue;
				}
				
				if ($DevengadoTmp > 0)
				{
					//PRESTAMOS
					$query = "SELECT DISTINCT interno FROM nom_prestamos_movs WHERE DATE(fecha) BETWEEN 
					'".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado' ORDER BY fecha";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #10: ".mysql_error():"");
					if (mysql_num_rows($result1) > 0)
					{
						while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
						{
							$PrestamosInterno2[] = $row1["interno"];
						}
					}
					
					$Clause3 = implode("', '", $PrestamosInterno2);
					
					$query = "SELECT * FROM nom_prestamos WHERE interno IN ('".$Clause3."') 
					AND beneficiario_id = '".$row["cliente_id"]."' ";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-1: ".mysql_error():"");
					if (mysql_num_rows($result1) > 0)
					{
						while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
						{
							$query = "SELECT * FROM nom_prestamos_movs WHERE interno = '".$row1["interno"]."' 
							AND DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado' ORDER BY fecha";
							$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #10: ".mysql_error():"");
							if (mysql_num_rows($result2) > 0)
							{
								while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
								{
									$DevengadoTmp -= $row2["valor"];
									if ($DevengadoTmp > 0)
									{
										$query = "SELECT * FROM mov_clientes WHERE saldo > '0' AND tipo_movimiento = 'Debito' 
										AND interno = '".$row2["nombre"]."' AND cliente_id = '".$row["cliente_id"]."'";
										$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
										if (mysql_num_rows($result3) > 0)
										{
											$row3 = mysql_fetch_array($result3);
											//CREAR PAGO
											$query = "INSERT INTO mov_clientes (cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, 
											factura, fecha, estado, digitado_por, fecha_digitado, vendedor_codigo, cobrador_codigo, caja_interno, caja_recibo) 
											VALUES ('".$row["cliente_id"]."', 'Credito', '".$row3["valor"]."', '".$row3["valor"]."', '".$row3["interno"]."', 
											'".$row3["orden_compra"]."', '".$row3["remision"]."', '".$row3["factura"]."', NOW(), 'Aprobado', 
											'".$_SESSION["UserCode"]."', NOW(), '".$row3["vendedor_codigo"]."', '".$row3["cobrador_codigo"]."', 
											'".$Caja_Interno."', '".$Caja_Interno."')";
											$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
											
											//SALDAR
											$query = "UPDATE mov_clientes SET saldo = '0' WHERE interno = '".$row3["interno"]."' AND tipo_movimiento = 'Debito'";
											$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
										}
									}
									else
									{
										// Mover
										$Day = date("d", strtotime($row2["fecha"]));
										$Month = date("n", strtotime($row2["fecha"]));
										$Year = date("Y", strtotime($row2["fecha"]));
										
										if ($Day <= 15)
										{
											// to 27
											$Day = 27;
											$NextCuota = date("Y-n-d", strtotime("".$Year."-".$Month."-".$Day.""));
										}
										else
										{
											// to 14
											$Day = 14;
											$Month += 1;
											$NextCuota = date("Y-n-d", strtotime("".$Year."-".$Month."-".$Day.""));
										}
										$query = "UPDATE nom_prestamos_movs SET fecha = '".$NextCuota."' WHERE 
										id = '".$row2["id"]."' ";
										$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
										
										$query = "SELECT interno FROM mov_clientes WHERE interno = '".$row2["nombre"]."' 
										AND cliente_id = '".$row["cliente_id"]."'";
										$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
										if (mysql_num_rows($result3) > 0)
										{
											$row3 = mysql_fetch_array($result3);
											
											$query = "UPDATE mov_clientes SET fecha = '".$NextCuota."' WHERE 
											interno = '".$row3["interno"]."' AND tipo_movimiento = 'Debito'";
											$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
										}
									}
								}
							}
						}
						
					}
				}
				else
				{
					//CREAR DEUDA RETENCION
					$Day = date("d", strtotime($Fecha_Ini));
					$Month = date("n", strtotime($Fecha_Ini));
					$Year = date("Y", strtotime($Fecha_Ini));
					
					if ($Day < 15)
					{
						// to 27
						$Day = 27;
						$FechaPago = date("Y-n-d", strtotime("".$Year."-".$Month."-".$Day.""));
					}
					else
					{
						// to 14
						$Day = 14;
						$Month += 1;
						$FechaPago = date("Y-n-d", strtotime("".$Year."-".$Month."-".$Day.""));
					}
					
					$query = "SELECT id FROM nom_prestamos ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$id = $row["id"] + 1;
						$len = strlen($id);
						$zero = "";
						if ($len < 4)
						{
							$num = 4 - $len;
							for ($a = 0; $a < $num; $a++)
								$zero .= "0";
							$Interno = "PR".$zero."".$id."";
						}
						else
							$Interno = "PR".$id."";
					}
					$Nombre = "Cuota_1_".$Interno;
					
					$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$row = mysql_fetch_array($result);
						$id = $row["id"] + 1;
						$len = strlen($id);
						$zero = "";
						if ($len < 7)
						{
							$num = 7 - $len;
							for ($a = 0; $a < $num; $a++)
								$zero .= "0";
							$Caja_Interno2 = "MCJ".$zero."".$id."";
						}
						else
							$Caja_Interno2 = "MCJ".$id."";
					}
					
					//-- Caja Final
					$query = "INSERT INTO caja_final 
					(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, efectivo, 
					total, digitado_por, fecha_digitado, estado) VALUES 
					('".$Fecha."', '".$Caja_Interno2."', '".$Caja_Interno2."', 'Egresos', 'Nómina', 'Préstamo', 'N/A', 
					'".$row["cliente_id"]."', '".$Retencion."', '".$Retencion."', '".$_SESSION["UserCode"]."', NOW(), 'Aprobado')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
					
					$query = "INSERT INTO nom_prestamos (interno, fecha, beneficiario_id, acreedor_id, tipo_mov, valor, 
					cuotas, valor_cuotas, caja, forma_pago, observacion, estado, digitado_por, fecha_digitado) VALUES 
					('".$Interno."', '".$Fecha."', '".$row["cliente_id"]."', '".$MAIN_ID."', '".$TipoMov."', '".$Retencion."', '1', 
					'".$Retencion."', '".$_SESSION["UserCode"]."', 'Efectivo', 'Generado Automaticamente desde Nomina por Insuficiente Saldo para Cubir Retencion.', 
					'Aprobado', '".$_SESSION["UserCode"]."', NOW())";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
					
					$query = "INSERT INTO nom_prestamos_movs (interno, nombre, cuota, fecha, valor, estado) VALUES 
					('".$Interno."', '".$Nombre."', '1', '".$FechaPago."', '".$Retencion."', 'Pendiente')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-1: ".mysql_error():"");
					
					$query = "INSERT INTO mov_clientes ( cliente_id, tipo_movimiento, valor, saldo, interno, 
					orden_compra, remision, factura, fecha, estado, digitado_por, fecha_digitado, caja_interno, 
					caja_recibo ) VALUES ('".$row["cliente_id"]."', 'Debito', '".$Retencion."', '".$Retencion."', 
					'".$Nombre."', '".$Interno."', '".$Nombre."', '".$Nombre."', '".$FechaPago."', 
					'Pendiente', '".$_SESSION["UserCode"]."', NOW(), '".$Caja_Interno2."', '".$Caja_Interno2."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
					
					continue;
				}
			}
			else
			{
				$query = "SELECT DISTINCT interno FROM nom_prestamos_movs WHERE DATE(fecha) BETWEEN 
				'".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado' ORDER BY fecha";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #10: ".mysql_error():"");
				if (mysql_num_rows($result1) > 0)
				{
					while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
					{
						$PrestamosInterno2[] = $row1["interno"];
					}
				}
				
				$Clause3 = implode("', '", $PrestamosInterno2);
				
				$query = "SELECT * FROM nom_prestamos WHERE interno IN ('".$Clause3."') 
				AND beneficiario_id = '".$row["cliente_id"]."' ";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-1: ".mysql_error():"");
				if (mysql_num_rows($result1) > 0)
				{
					while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
					{
						$query = "SELECT nombre FROM nom_prestamos_movs WHERE interno = '".$row1["interno"]."' 
						AND DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado' ORDER BY fecha";
						$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #10: ".mysql_error():"");
						if (mysql_num_rows($result2) > 0)
						{
							while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
							{
								$query = "SELECT * FROM mov_clientes WHERE saldo > '0' AND tipo_movimiento = 'Debito' 
								AND interno = '".$row2["nombre"]."' AND cliente_id = '".$row["cliente_id"]."'";
								$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
								if (mysql_num_rows($result3) > 0)
								{
									$row3 = mysql_fetch_array($result3);
									//CREAR PAGO
									$query = "INSERT INTO mov_clientes (cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, 
									factura, fecha, estado, digitado_por, fecha_digitado, vendedor_codigo, cobrador_codigo, caja_interno, caja_recibo) 
									VALUES ('".$row["cliente_id"]."', 'Credito', '".$row3["valor"]."', '".$row3["valor"]."', '".$row3["interno"]."', 
									'".$row3["orden_compra"]."', '".$row3["remision"]."', '".$row3["factura"]."', NOW(), 'Aprobado', 
									'".$_SESSION["UserCode"]."', NOW(), '".$row3["vendedor_codigo"]."', '".$row3["cobrador_codigo"]."', 
									'".$Caja_Interno."', '".$Caja_Interno."')";
									$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
									
									//SALDAR
									$query = "UPDATE mov_clientes SET saldo = '0' WHERE interno = '".$row3["interno"]."' AND tipo_movimiento = 'Debito'";
									$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
								}
							}
						}
					}
				}
			}
		}
	}
}
/*else if (isset($_GET["Nomina"]))// borrar cuando termine el de arriba
{
	$data = $_GET["Nomina"];
	$SubGrupo =  isset($_GET["SubGrupo"]) ? $_GET["SubGrupo"]:die();
	if ($SubGrupo == "Mensual")
		$SubGrupo = "Pago Mensual";
	else if ($SubGrupo == "Quincenal")
		$SubGrupo = "Pago quincena";
	$SubGrupo2 = isset($_GET["SubGrupo2"]) ? $_GET["SubGrupo2"]:die();
		if ($SubGrupo2 == "")
		$SubGrupo2 = "N/A";
	
	$Total =  isset($_GET["Total"]) ? $_GET["Total"]:die();
	
	//CREAR RECIBO DE CAJA CON EL TOTAL A PAGAR.
	$query = "SELECT id FROM caja_final ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$row = mysql_fetch_array($result);
	$id = $row['id'] + 1;
	$len = strlen($id);
	$zero = "";
	if ($len < 7)
	{
		$num = 7 - $len;
		for ($a = 0; $a < $num; $a++) {
			$zero .= "0";
		}
		$Caja_Interno = "MCJ".$zero."".$id."";
	}
	else {
		$Caja_Interno = "MCJ".$id."";
	}
	
	$query = "INSERT INTO caja_final 
	(fecha, caja_interno, caja_recibo, categoria, grupo, subgrupo, subgrupo2, cliente_id, efectivo, total, 
	digitado_por, fecha_digitado, estado) VALUES (NOW(), '".$Caja_Interno."', '".$Caja_Interno."', 'Egresos', 
	'Nómina', '".$SubGrupo."', '".$SubGrupo2."', '".$MAIN_ID."', '".$Total."', '".$Total."', 'SISTEMA', NOW(), 'Aprobado')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	foreach($data as $item)
	{
		// INSERTAR NOMINA
		$query = "INSERT INTO nomina (fecha_ini, fecha_fin, nombre, cliente_id, basico, horas, horas_desc, horas_rep, horas_ext, 
		horas_lab, basico_final, transporte, bono, extras, licencias, devengado, salud, pension, retencion, prestamo, libranza, 
		anticipo, donacion, multa, deducido, neto, cesantia, digitado_por, fecha_digitado) VALUES ('".$item["Fecha_Ini"]."', 
		'".$item["Fecha_Fin"]."', '".$item["Nombre"]."', '".$item["ClienteID"]."', '".$item["Basico"]."', '".$item["Horas"]."', 
		'".$item["Horas_Desc"]."', '".$item["Horas_Rep"]."', '".$item["Horas_Ext"]."', '".$item["Horas_Lab"]."', 
		'".$item["Basico_Final"]."', '".$item["Transporte"]."', '".$item["Bono"]."', '".$item["Extras"]."', '".$item["Licencias"]."', 
		'".$item["Devengado"]."', '".$item["Salud"]."', '".$item["Pension"]."', '".$item["Retencion"]."', '".$item["Prestamo"]."', 
		'".$item["Libranza"]."', '".$item["Anticipo"]."', '".$item["Donacion"]."', '".$item["Multa"]."', '".$item["Deducido"]."', 
		'".$item["Neto"]."', '".$item["Cesantia"]."', '".$_SESSION["UserCode"]."', NOW())";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		
		//SALDAR DEUDAS
		$query = "SELECT * FROM mov_clientes WHERE DATE(fecha) BETWEEN '".$item["Fecha_Ini"]."' AND '".$item["Fecha_Fin"]."' 
		AND cliente_id = '".$item["ClienteID"]."' AND tipo_movimiento = 'Debito' AND saldo > '0' AND estado = 'Aprobado'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result))
			{
				//CREAR PAGO
				$query = "INSERT INTO mov_clientes (cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, 
				factura, fecha, estado, digitado_por, fecha_digitado, vendedor_codigo, cobrador_codigo, caja_interno, caja_recibo) 
				VALUES ('".$item["ClienteID"]."', 'Credito', '".$row["valor"]."', '".$row["valor"]."', '".$row["interno"]."', 
				'".$row["orden_compra"]."', '".$row["remision"]."', '".$row["factura"]."', NOW(), 'Aprobado', 
				'".$_SESSION["UserCode"]."', NOW(), '".$row["vendedor_codigo"]."', '".$row["cobrador_codigo"]."', 
				'".$Caja_Interno."', '".$Caja_Interno."')";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
				
				//SALDAR
				$query = "UPDATE mov_clientes SET saldo = '0' WHERE interno = '".$row["interno"]."' AND tipo_movimiento = 'Debito'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
			}
		}
	}
}*/
else if (isset($_POST["Nomina_Modificar"]))
{
	$data = $_POST["Nomina_Modificar"];
	
	foreach($data as $item)
	{
		$query = "UPDATE nomina SET 
		basico = '".$item["Basico"]."', 
		horas = '".$item["Horas"]."', 
		horas_desc = '".$item["Horas_Desc"]."', 
		horas_rep = '".$item["Horas_Rep"]."', 
		horas_ext = '".$item["Horas_Ext"]."', 
		horas_lab = '".$item["Horas_Lab"]."', 
		basico_final = '".$item["Basico_Final"]."', 
		transporte = '".$item["Transporte"]."', 
		bono = '".$item["Bono"]."', 
		extras = '".$item["Extras"]."', 
		licencias = '".$item["Licencias"]."', 
		devengado = '".$item["Devengado"]."', 
		salud = '".$item["Salud"]."', 
		pension = '".$item["Pension"]."', 
		retencion = '".$item["Retencion"]."', 
		prestamo = '".$item["Prestamo"]."', 
		libranza = '".$item["Libranza"]."', 
		anticipo = '".$item["Anticipo"]."', 
		donacion = '".$item["Donacion"]."', 
		multa = '".$item["Multa"]."', 
		deducido = '".$item["Deducido"]."', 
		deuda = '".$item["Deuda"]."', 
		neto = '".$item["Neto"]."', 
		cesantia = '".$item["Cesantia"]."', 
		modificado_por = '".$_SESSION["UserCode"]."', 
		fecha_modificado = NOW() 
		WHERE fecha_ini = '".$item["Fecha_Ini"]."' AND fecha_fin = '".$item["Fecha_Fin"]."' AND cliente_id = '".$item["ClienteID"]."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	}
}
else if (isset($_GET['Produccion_Crear']))
{
	$main_data = $_GET['MainData'];
	$data1 = $_GET['Data1'];
	$data2 = $_GET['Data2'];
	
	//Parte 1
	$Solicitud = $main_data[0]["Solicitud"];
	$Destino = $main_data[0]["Destino"];
	$ClienteID = isset($main_data[0]["ClienteID"]) ? $main_data[0]["ClienteID"]:$MAIN_ID;
	$Fecha = $main_data[0]["Fecha"];
	$Trefilado = isset($main_data[0]["Trefilado"]) ? $main_data[0]["Trefilado"]:"";
	$Enderezado = isset($main_data[0]["Enderezado"]) ? $main_data[0]["Enderezado"]:"";
	$Electrosoldado = isset($main_data[0]["Electrosoldado"]) ? $main_data[0]["Electrosoldado"]:"";
	$Figurado =isset($main_data[0]["Figurado"]) ? $main_data[0]["Figurado"]:"";
	//Parte 2
	$Codigo1 = 0;
	$Cantidad1 = 0;
	$Origen1 = 0;
	$Destino1 = 0;
	// Parte 3
	$Codigo2 = 0;
	$Cantidad2 = 0;
	$Origen2 = 0;
	$Destino2 = 0;
	//
	$Ord_Produccion = 0;
	
	$query = "SELECT id FROM produccion_final ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$row = mysql_fetch_array($result);
	$id = $row['id'] + 1;
	$len = strlen($id);
	$zero = "";
	if ($len < 6){
		$num = 6 - $len;
		for ($a = 0; $a < $num; $a++) {
			$zero .= "0";
		}
		$Ord_Produccion = "OP".$zero."".$id."";
	}
	else {
		$Ord_Produccion = "OP".$id."";
	}
	
	// PRODUCCION FINAL
	
	$query = "INSERT INTO produccion_final 
	(orden_produccion, solicitud, destino, cliente_id, fecha, estado, operario_trefilado, operario_enderezado, 
	operario_electrosoldado, operario_figurado, digitado_por, fecha_digitado) VALUES 
	('".$Ord_Produccion."', '".$Solicitud."', '".$Destino."', '".$ClienteID."', '".$Fecha."', 'Pendiente', '".$Trefilado."', 
	'".$Enderezado."', '".$Electrosoldado."', '".$Figurado."', '".$_SESSION["UserCode"]."', NOW())";
	$result = mysql_query($query) or die ("SQL Error 2: " . mysql_error());
	
	// PRODUCCION PROCESOS
	if ($Trefilado != "")
	{
		$query = "INSERT INTO produccion_proc 
		(orden_produccion, solicitud, estado, proceso) VALUES ('".$Ord_Produccion."', '".$Solicitud."', 'Pendiente', 'Trefilado')";
		$result = mysql_query($query) or die ("SQL Error 3: " . mysql_error());
	}
	
	if ($Enderezado != "")
	{
		$query = "INSERT INTO produccion_proc 
		(orden_produccion, solicitud, estado, proceso) VALUES ('".$Ord_Produccion."', '".$Solicitud."', 'Pendiente', 'Corte y Enderezado')";
		$result = mysql_query($query) or die ("SQL Error 3: " . mysql_error());
	}
	
	if ($Electrosoldado != "")
	{
		$query = "INSERT INTO produccion_proc 
		(orden_produccion, solicitud, estado, proceso) VALUES ('".$Ord_Produccion."', '".$Solicitud."', 'Pendiente', 'Electrosoldado')";
		$result = mysql_query($query) or die ("SQL Error 3: " . mysql_error());
	}
	
	if ($Figurado != "")
	{
		$query = "INSERT INTO produccion_proc 
		(orden_produccion, solicitud, estado, proceso) VALUES ('".$Ord_Produccion."', '".$Solicitud."', 'Pendiente', 'Figurado')";
		$result = mysql_query($query) or die ("SQL Error 3: " . mysql_error());
	}
	
	// PRODUCCION MOVS 1
	
	foreach($data1 as $item)
	{
		$Codigo1 = $item['CodFab'];
		$Cantidad1 = $item['Cantidad'];
		$Origen1 = $item['Origen'];
		$Destino1 = $item['Destino'];
		
		$query = "INSERT INTO produccion_movs 
		(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
		('".$Codigo1."', '".$Cantidad1."', 'Requerido', '".$Origen1."', '".$Destino1."', 
		'".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die ("SQL Error 3: " . mysql_error());
	}
	
	// PRODUCCION PROCESOS MOVS 1
	
	foreach($data1 as $item)
	{
		$Codigo1 = $item['CodFab'];
		$Cantidad1 = $item['Cantidad'];
		$Origen1 = $item['Origen'];
		$Destino1 = $item['Destino'];
		
		$query = "INSERT INTO produccion_proc_movs 
		(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
		('".$Codigo1."', '".$Cantidad1."', 'Requerido', '".$Origen1."', '".$Destino1."', 
		'".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die ("SQL Error 3: " . mysql_error());
	}
	
	// PRODUCCION MOVS 2
	
	foreach($data2 as $item)
	{
		$Codigo2 = $item['CodFab'];
		$Cantidad2 = $item['Cantidad'];
		$Origen2 = $item['Origen'];
		$Destino2 = $item['Destino'];
		
		$query = "INSERT INTO produccion_movs 
		(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
		('".$Codigo2."', '".$Cantidad2."', 'Obtener', '".$Origen2."', '".$Destino2."', 
		'".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die ("SQL Error 4: " . mysql_error());
	}
	
	// PRODUCCION PROCESOS MOVS 2
	
	foreach($data2 as $item)
	{
		$Codigo2 = $item['CodFab'];
		$Cantidad2 = $item['Cantidad'];
		$Origen2 = $item['Origen'];
		$Destino2 = $item['Destino'];
		
		$query = "INSERT INTO produccion_proc_movs 
		(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
		('".$Codigo2."', '".$Cantidad2."', 'Obtener', '".$Origen2."', '".$Destino2."', 
		'".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die ("SQL Error 4: " . mysql_error());
	}
	
	echo $Ord_Produccion;
}
else if (isset($_GET['Produccion_Modificar']))
{
	$main_data = $_GET['MainData'];
	$data1 = $_GET['Data1'];
	$data2 = $_GET['Data2'];
	
	//Parte 1
	$Solicitud = $main_data[0]["Solicitud"];
	$Destino = $main_data[0]["Destino"];
	$ClienteID = isset($main_data[0]["ClienteID"]) ? $main_data[0]["ClienteID"]:$MAIN_ID;
	$Fecha = $main_data[0]["Fecha"];
	$Trefilado = isset($main_data[0]["Trefilado"]) ? $main_data[0]["Trefilado"]:"";
	$Enderezado = isset($main_data[0]["Enderezado"]) ? $main_data[0]["Enderezado"]:"";
	$Electrosoldado = isset($main_data[0]["Electrosoldado"]) ? $main_data[0]["Electrosoldado"]:"";
	$Figurado = isset($main_data[0]["Figurado"]) ? $main_data[0]["Figurado"]:"";
	//Parte 2
	$Codigo1 = 0;
	$Cantidad1 = 0;
	$Origen1 = 0;
	$Destino1 = 0;
	// Parte 3
	$Codigo2 = 0;
	$Cantidad2 = 0;
	$Origen2 = 0;
	$Destino2 = 0;
	//
	$Ord_Produccion = $main_data[0]['Ord_Produccion'];
	$Ord_Compra = "";
	$Interno = "";
	$Procesos = false;
	$VentaMovs = false;
	$Notas = "- La Orden de Producción vinculada a este pedido, fué Modificada.";
	
	//-------- CHECK CHANGES
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		if ($data_session[0]["Lvl"] != "Administrador")
		{
			$query = "SELECT id FROM produccion_final WHERE orden_produccion = '".$main_data[0]['Ord_Produccion']."' 
			AND estado = 'Pendiente'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) <= 0)
			{
				$ReturnData[0] = array(
					"MESSAGE" => "CHANGED",
				);
				echo json_encode($ReturnData);
				die();
			}
		}
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	$query = "SELECT * FROM fact_final WHERE orden_produccion = '".$Ord_Produccion."'";
	$result = mysql_query($query) or die ("SQL ERROR #1: " . mysql_error());
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$VentaMovs = true;
		
		$Ord_Compra = $row["orden_compra"];
		$Interno = $row["interno"];
		$Factura = $row["factura"];
		$Remision = $row["remision"];
		$Servicio = $row["tipo_servicio_valor"];
		$Descuento = $row["tipo_descuento_valor"];
		$Old_Iva = $row["iva"];
		
		if ($row["estado"] == "Aprobado")
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR",
			);
			echo json_encode($ReturnData);
			die();
		}
	}
	
	$query = "SELECT cod_fab, peso, lista1 FROM productos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoPeso[$row["cod_fab"]] = $row["peso"];
			$ProductoPrecio[$row["cod_fab"]] = $row["lista1"];
		}
	}
	
	$query = "SELECT id FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."'";
	$result = mysql_query($query) or die ("SQL ERROR #3-1: " . mysql_error());
	if (mysql_num_rows($result) > 0)
	{
		$Procesos = true;
		
		$query = "DELETE FROM produccion_proc_movs WHERE orden_produccion = '".$main_data[0]['Ord_Produccion']."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
	}
	
	//-------- CHECK IF SOME ITEM HAS BEEN DELETED
	$delete = true;
	$query = "SELECT codigo FROM produccion_movs WHERE tipo = 'Requerido' AND 
	orden_produccion = '".$main_data[0]['Ord_Produccion']."' ";
	$result = mysql_query($query) or die ("SQL ERROR #4-1: " . mysql_error());
	$numrow = mysql_num_rows($result);
	if ($numrow > 0)
	{
		while ($rows = mysql_fetch_array($result))
		{
			$num = count($data1);
			for ($i=0; $i<$num; $i++) {
				//echo "".$rows['codigo']." ".$data[$i]['CodFab']."";
				if ($rows['codigo'] == $data1[$i]['CodFab1'])
				{
					$delete = false;
				}
			}
			if ($delete == true){
				//echo "borrar!!";
				$query2 = "DELETE FROM produccion_movs WHERE codigo = '".$rows['codigo']."' AND 
				tipo = 'Requerido' AND orden_produccion = '".$main_data[0]['Ord_Produccion']."' ";
				$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #4-2: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	Del_InventoryMovs($Ord_Produccion);
	
	$delete = true;
	$query = "SELECT codigo FROM produccion_movs WHERE tipo = 'Obtener' AND 
	orden_produccion = '".$main_data[0]['Ord_Produccion']."' ";
	$result = mysql_query($query) or die ("SQL ERROR #5-1: " . mysql_error());
	$numrow = mysql_num_rows($result);
	if ($numrow > 0)
	{
		while ($rows = mysql_fetch_array($result))
		{
			$num = count($data2);
			for ($i=0; $i<$num; $i++) {
				//echo "".$rows['codigo']." ".$data[$i]['CodFab']."";
				if ($rows['codigo'] == $data2[$i]['CodFab2'])
				{
					$delete = false;
				}
			}
			if ($delete == true){
				//echo "borrar!!";
				$query2 = "DELETE FROM produccion_movs WHERE codigo = '".$rows['codigo']."' AND 
				tipo = 'Obtener' AND orden_produccion = '".$main_data[0]['Ord_Produccion']."' ";
				$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
			}
			$delete = true;
		}
	}
	
	// PRODUCCION FINAL
	
	$query = "UPDATE produccion_final SET 
	solicitud = '".$Solicitud."', 
	destino = '".$Destino."', 
	fecha = '".$Fecha."', 
	estado = 'Pendiente', 
	operario_trefilado = '".$Trefilado."', 
	operario_enderezado = '".$Enderezado."',  
	operario_electrosoldado = '".$Electrosoldado."', 
	operario_figurado = '".$Figurado."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	fecha_modificado = NOW()
	WHERE orden_produccion = '".$Ord_Produccion."'";
	$result = mysql_query($query) or die ("SQL ERROR #6: " . mysql_error());
	
	// PRODUCCION PROCESOS
	if ($Procesos == false)
	{
		if ($Trefilado != "")
		{
			$query = "INSERT INTO produccion_proc 
			(orden_produccion, solicitud, estado, proceso) VALUES ('".$Ord_Produccion."', '".$Solicitud."', 'Pendiente', 'Trefilado')";
			$result = mysql_query($query) or die ("SQL ERROR #7-1: " . mysql_error());
		}
		
		if ($Enderezado != "")
		{
			$query = "INSERT INTO produccion_proc 
			(orden_produccion, solicitud, estado, proceso) VALUES ('".$Ord_Produccion."', '".$Solicitud."', 'Pendiente', 'Corte y Enderezado')";
			$result = mysql_query($query) or die ("SQL ERROR #7-2: " . mysql_error());
		}
		
		if ($Electrosoldado != "")
		{
			$query = "INSERT INTO produccion_proc 
			(orden_produccion, solicitud, estado, proceso) VALUES ('".$Ord_Produccion."', '".$Solicitud."', 'Pendiente', 'Electrosoldado')";
			$result = mysql_query($query) or die ("SQL ERROR #7-3: " . mysql_error());
		}
		
		if ($Figurado != "")
		{
			$query = "INSERT INTO produccion_proc 
			(orden_produccion, solicitud, estado, proceso) VALUES ('".$Ord_Produccion."', '".$Solicitud."', 'Pendiente', 'Figurado')";
			$result = mysql_query($query) or die ("SQL ERROR #7-4: " . mysql_error());
		}
	}
	else
	{
		$query = "UPDATE produccion_proc SET 
		solicitud = '".$Solicitud."', 
		estado = 'Pendiente', 
		rendimiento = '0.00', 
		avance = '0.00', 
		observaciones = '', 
		iniciado_por = '', 
		fecha_ini = '0000-00-00 00:00:00', 
		finalizado_por = '', 
		fecha_fin = '0000-00-00 00:00:00' 
		WHERE orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die ("SQL ERROR #7-1: " . mysql_error());
	}
	
	// PRODUCCION MOVS 1
	
	foreach($data1 as $item)
	{
		$Codigo1 = $item['CodFab1'];
		$Cantidad1 = $item['Cantidad1'];
		$Origen1 = $item['Origen1'];
		$Destino1 = $item['Destino1'];
		
		$query = "SELECT codigo FROM produccion_movs WHERE codigo = '".$Codigo1."' AND 
		tipo = 'Requerido' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-1: ".mysql_error():"");
		
		if (mysql_num_rows($result) > 0)
		{
			$query = "UPDATE produccion_movs SET 
			cantidad = '".$Cantidad1."', 
			origen = '".$Origen1."', 
			destino = '".$Destino1."', 
			orden_compra = '".$Ord_Compra."', 
			interno = '".$Interno."', 
			cliente_id = '".$ClienteID."' 
			WHERE codigo = '".$Codigo1."' AND tipo = 'Requerido' AND orden_produccion = '".$Ord_Produccion."' ";
			$result = mysql_query($query) or die ("SQL ERROR #8-2: " . mysql_error());
		}
		else
		{
			$query = "INSERT INTO produccion_movs 
			(codigo, cantidad, tipo, origen, destino, orden_produccion, orden_compra, interno, cliente_id) VALUES 
			('".$Codigo1."', '".$Cantidad1."', 'Requerido', '".$Origen1."', '".$Destino1."', 
			'".$Ord_Produccion."', '".$Ord_Compra."', '".$Interno."', '".$ClienteID."')";
			$result = mysql_query($query) or die ("SQL ERROR #8-3: " . mysql_error());
		}
		
		// PRODUCCION PROCESOS MOVS 1
		$query = "INSERT INTO produccion_proc_movs 
		(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
		('".$Codigo1."', '".$Cantidad1."', 'Requerido', '".$Origen1."', '".$Destino1."', 
		'".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die ("SQL ERROR #8-4: " . mysql_error());
	}
	
	// PRODUCCION MOVS 2
	
	foreach($data2 as $item)
	{
		$Codigo2 = $item['CodFab2'];
		$Cantidad2 = $item['Cantidad2'];
		$Origen2 = $item['Origen2'];
		$Destino2 = $item['Destino2'];
		
		if ($Destino2 == "Inventario" && $VentaMovs == true)
		{
			$query = "SELECT codigo FROM fact_movs WHERE codigo = '".$Codigo2."' AND interno = '".$Interno."' 
			AND orden_compra = '".$Ord_Compra."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$query = "UPDATE fact_movs SET cantidad = '".$Cantidad2."' WHERE codigo = '".$Codigo2."' 
				AND interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-2: ".mysql_error():"");
			}
			else
			{
				$query = "INSERT INTO fact_movs (codigo, cantidad, precio, interno, orden_compra, factura, remision, cliente_id) 
				VALUES ('".$Codigo2."', '".$Cantidad2."', '".$ProductoPrecio[$Codigo2]."', '".$Interno."', '".$Ord_Compra."', 
				'".$Factura."', '".$Remision."', '".$ClienteID."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-3: ".mysql_error():"");
			}
		}
		
		$query = "SELECT codigo FROM produccion_movs WHERE codigo = '".$Codigo2."' AND 
		tipo = 'Obtener' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-4: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$query = "UPDATE produccion_movs SET 
			cantidad = '".$Cantidad2."', 
			origen = '".$Origen2."', 
			destino = '".$Destino2."', 
			orden_compra = '".$Ord_Compra."', 
			interno = '".$Interno."', 
			cliente_id = '".$ClienteID."' 
			WHERE codigo = '".$Codigo2."' AND tipo = 'Obtener' AND orden_produccion = '".$Ord_Produccion."' ";
			$result = mysql_query($query) or die ("SQL ERROR #9-5: " . mysql_error());
		}
		else
		{
			$query = "INSERT INTO produccion_movs 
			(codigo, cantidad, tipo, origen, destino, orden_produccion, orden_compra, interno, cliente_id) VALUES 
			('".$Codigo2."', '".$Cantidad2."', 'Obtener', '".$Origen2."', '".$Destino2."', 
			'".$Ord_Produccion."', '".$Ord_Compra."', '".$Interno."', '".$ClienteID."')";
			$result = mysql_query($query) or die ("SQL ERROR #9-6: " . mysql_error());
		}
		
		// PRODUCCION PROCESOS MOVS 2
		$query = "INSERT INTO produccion_proc_movs 
		(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
		('".$Codigo2."', '".$Cantidad2."', 'Obtener', '".$Origen2."', '".$Destino2."', 
		'".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die ("SQL ERROR #9-7: " . mysql_error());
	}

	if ($VentaMovs == true)
	{
		$query = "SELECT codigo, cantidad, precio FROM fact_movs WHERE interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$SubTotal = 0;
			$Total = 0;
			$Iva = 0;
			$Peso = 0;
			
			while ($row = mysql_fetch_array($result))
			{
				$SubTotal += $row["cantidad"] * $row["precio"];
				$Peso += $row["cantidad"] * $ProductoPeso[$row["codigo"]];
			}
			
			$SubTotal = $SubTotal + $Servicio;
			$Iva = $SubTotal - ($SubTotal / 1.16);
			if($Descuento == $Old_Iva)
				$Descuento = $Iva;
			$Total =  $SubTotal - $Descuento;
			
			$query = "UPDATE fact_final SET 
			sub_total = '".$SubTotal."', 
			total = '".$Total."', 
			iva = '".$Iva."', 
			peso = '".$Peso."', 
			notas = notas + '".$Notas."' 
			WHERE interno = '".$Interno."' AND orden_compra = '".$Ord_Compra."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-2: ".mysql_error():"");
		}
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET["Produccion_Fix"]))
{
	$main_data = $_GET["MainData"];
	$data1 = $_GET["Data1"];
	$data2 = $_GET["Data2"];
	
	//Parte 1
	$ClienteID = isset($main_data[0]["ClienteID"]) ? $main_data[0]["ClienteID"]:$MAIN_ID;
	$Ord_Produccion = $main_data[0]["Ord_Produccion"];
	$Ord_Compra = "";
	$Interno = "";
	$FechaAprobado = "0000-00-00 00:00:00";
	//Parte 2
	$Codigo1 = 0;
	$Cantidad1 = 0;
	$Origen1 = 0;
	$Destino1 = 0;
	// Parte 3
	$Codigo2 = 0;
	$Cantidad2 = 0;
	$Origen2 = 0;
	$Destino2 = 0;
	
	$query = "SELECT fecha_aprobado FROM produccion_final 
	WHERE orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$row = mysql_fetch_array($result);
		$FechaAprobado = $row["fecha_aprobado"];
	}
	
	$query = "SELECT * FROM fact_final WHERE orden_produccion = '".$Ord_Produccion."'";
	$result = mysql_query($query) or die ("SQL ERROR #1-2: " . mysql_error());
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$Ord_Compra = $row["orden_compra"];
		$Interno = $row["interno"];
	}
	
	//-------- DELETE ALL
	$query = "DELETE FROM produccion_movs WHERE orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
	
	if ($FechaAprobado == "0000-00-00 00:00:00")
		$FechaAprobado = Del_InventoryMovs($Ord_Produccion);
	else
		Del_InventoryMovs($Ord_Produccion);
	
	//-------- PRODUCCION MOVS 1
	foreach($data1 as $item)
	{
		$Codigo1 = $item['CodFab1'];
		$Cantidad1 = $item['Cantidad1'];
		$Origen1 = $item['Origen1'];
		$Destino1 = $item['Destino1'];
		
		$query = "INSERT INTO produccion_movs 
		(codigo, cantidad, tipo, origen, destino, orden_produccion, orden_compra, interno, cliente_id) 
		VALUES ('".$Codigo1."', '".$Cantidad1."', 'Requerido', '".$Origen1."', '".$Destino1."', 
		'".$Ord_Produccion."', '".$Ord_Compra."', '".$Interno."', '".$ClienteID."')";
		$result = mysql_query($query) or die ("SQL ERROR #2: " . mysql_error());
		
		if ($FechaAprobado == "0000-00-00 00:00:00")
			AfectarInventario($Codigo1, $Cantidad1, 0, $Ord_Produccion, "", "Produccion", "Produccion_Consumir");
		else
			AfectarInventario($Codigo1, $Cantidad1, 0, $Ord_Produccion, $FechaAprobado, "Produccion", "Produccion_Consumir");
	}
	
	//-------- PRODUCCION MOVS 2
	foreach($data2 as $item)
	{
		$Codigo2 = $item['CodFab2'];
		$Cantidad2 = $item['Cantidad2'];
		$Origen2 = $item['Origen2'];
		$Destino2 = $item['Destino2'];
		
		$query = "INSERT INTO produccion_movs 
		(codigo, cantidad, tipo, origen, destino, orden_produccion, orden_compra, interno, cliente_id) 
		VALUES ('".$Codigo2."', '".$Cantidad2."', 'Obtener', '".$Origen2."', '".$Destino2."', 
		'".$Ord_Produccion."', '".$Ord_Compra."', '".$Interno."', '".$ClienteID."')";
		$result = mysql_query($query) or die ("SQL ERROR #3: " . mysql_error());
		
		if ($FechaAprobado == "0000-00-00 00:00:00")
			AfectarInventario($Codigo2, $Cantidad2, 0, $Ord_Produccion, "", "Produccion", "Produccion_Producir");
		else
			AfectarInventario($Codigo2, $Cantidad2, 0, $Ord_Produccion, $FechaAprobado, "Produccion", "Produccion_Producir");
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Produccion_Modificar_Anular']))
{
	$Ord_Produccion = isset($_GET['Ord_Produccion']) ? $_GET['Ord_Produccion']:"";
	$Motivo = isset($_GET['Motivo']) ? $_GET['Motivo']:"";
	
	//-------- CHECK CHANGES
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		if ($data_session[0]["Lvl"] != "Administrador")
		{
			$query = "SELECT id FROM produccion_final WHERE orden_produccion = '".$Ord_Produccion."' 
			AND estado = 'Pendiente'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) <= 0)
			{
				$ReturnData[0] = array(
					"MESSAGE" => "CHANGED",
				);
				echo json_encode($ReturnData);
				die();
			}
		}
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	if ($Ord_Produccion != "")
	{
		$query = "SELECT interno, orden_compra, cliente_id FROM fact_final WHERE orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die("SQL Error: 1" . mysql_error());
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			Anular_Final($row["cliente_id"], $row["orden_compra"], $row["interno"], "Produccion", $Motivo);
		}
		$query = "UPDATE produccion_final SET 
		estado = 'Anulado', 
		anulado_por = '".$_SESSION["UserCode"]."', 
		motivo_anulado = '".$Motivo."', 
		fecha_anulado = NOW() 
		WHERE orden_produccion = '".$Ord_Produccion."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		Del_InventoryMovs($Ord_Produccion);
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Produccion_Aprobar']))
{
	$Old_Estado = isset($_GET['Old_Estado']) ? $_GET['Old_Estado']:"Pendiente";
	$New_Estado = isset($_GET['New_Estado']) ? $_GET['New_Estado']:"Pendiente";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Ord_Compra = isset($_GET['Ord_Compra']) ? $_GET['Ord_Compra']:"";
	$Ord_Produccion = isset($_GET['Ord_Produccion']) ? $_GET['Ord_Produccion']:"";
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	
	//-------- CHECK CHANGES
	$query = "SELECT fecha_aprobado FROM produccion_final 
	WHERE orden_produccion = '".$Ord_Produccion."' AND estado = '".$Old_Estado."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
	}
	else
	{
		$row = mysql_fetch_array($result);
		/*if ($Interno == "")
		{
			$Interno = $Ord_Produccion;
		}*/
		
		if ($New_Estado == "Anulado")
		{
			if ($Interno != "")
				Anular_Final($ClienteID, $Ord_Compra, $Interno, "Produccion", "Anulado Automaticamente por Produccion -> Aprobar/Listado");
			
			$query = "UPDATE produccion_final SET 
			estado = 'Anulado', 
			anulado_por = '".$_SESSION["UserCode"]."', 
			fecha_anulado = NOW() 
			WHERE orden_produccion = '".$Ord_Produccion."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			Del_InventoryMovs($Ord_Produccion);
			
			$ReturnData[0] = array(
				"MESSAGE" => "OK",
			);
		}
		else if ($New_Estado == "Aprobado")
		{
			$Obtener = false;
			$Requerido = false;
			
			$query = "SELECT * FROM produccion_movs WHERE orden_produccion = '".$Ord_Produccion."' AND cliente_id = '".$ClienteID."'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
			$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
			
			if (mysql_num_rows($result1) > 0)
			{
				while($row1 = mysql_fetch_array($result1))
				{
					if ($row1["tipo"] == "Obtener")
						$Obtener = true;
					else if ($row1["tipo"] == "Requerido")
						$Requerido = true;
				}
				
				if ($Obtener == true && $Requerido == true)
				{
					while($row2 = mysql_fetch_array($result2))
					{
						if ($row2["tipo"] == "Obtener")
						{
							if ($row["fecha_aprobado"] == "0000-00-00 00:00:00")
								AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $Ord_Produccion, "", "Produccion", "Produccion_Producir");
							else
								AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $Ord_Produccion, $row["fecha_aprobado"], "Produccion", "Produccion_Producir");
						}
						else if ($row2["tipo"] == "Requerido")
						{
							if ($row["fecha_aprobado"] == "0000-00-00 00:00:00")
								AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $Ord_Produccion, "", "Produccion", "Produccion_Consumir");
							else
								AfectarInventario($row2["codigo"], $row2["cantidad"], 0, $Ord_Produccion, $row["fecha_aprobado"], "Produccion", "Produccion_Consumir");
						}
					}
					
					$query = "UPDATE produccion_final SET 
					estado = 'Aprobado', 
					aprobado_por = '".$_SESSION["UserCode"]."', 
					fecha_aprobado = NOW() 
					WHERE orden_produccion = '".$Ord_Produccion."' AND cliente_id = '".$ClienteID."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
					
					$ReturnData[0] = array(
						"MESSAGE" => "OK",
					);
				}
				else
				{
					$ReturnData[0] = array(
						"MESSAGE" => "INCOMPLETO",
					);
				}
			}
			else
			{
				$ReturnData[0] = array(
					"MESSAGE" => "ERROR",
				);
			}
		}
		//--
	}
	echo json_encode($ReturnData);
}
else if (isset($_GET['Produccion_Trefilado']))
{
	$main_data = $_GET['MainData'];
	$data1 = $_GET['Data1'];
	$data2 = $_GET['Data2'];
	$data3 = isset($_GET['Data3']) ? $_GET['Data3']:0;
	
	//Main Data
	$Type = isset($main_data[0]["Type"]) ? $main_data[0]["Type"]:die();
	$Estado = isset($main_data[0]["Estado"]) ? $main_data[0]["Estado"]:die();
	$ClienteID = (isset($main_data[0]["ClienteID"]) && $main_data[0]["ClienteID"] != "") ? $main_data[0]["ClienteID"]:"";
	$Fecha = $main_data[0]["Fecha"];
	$Ord_Produccion = isset($main_data[0]["Ord_Produccion"]) ? $main_data[0]["Ord_Produccion"]:"";
	$Ord_Compra = isset($main_data[0]["Ord_Compra"]) ? $main_data[0]["Ord_Compra"]:"";
	$Rendimiento = isset($main_data[0]["Rendimiento"]) ? $main_data[0]["Rendimiento"]:0;
	$Avance = isset($main_data[0]["Avance"]) ? $main_data[0]["Avance"]:0;
	$Observaciones = isset($main_data[0]["Observaciones"]) ? $main_data[0]["Observaciones"]:"";
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."'	
	AND estado = '".$Estado."' AND proceso = 'Trefilado' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	//-------- DELETE ALL
	// 1º
	$query = "DELETE FROM produccion_proc_movs WHERE tipo = 'Obtener' 
	AND origen = 'Trefilado' AND orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	// 2º
	$query = "DELETE FROM produccion_proc_movs WHERE tipo = 'Despunte' 
	AND origen = 'Trefilado' AND orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	
	
	// PRODUCCION PROCESOS
	
	if ($Type == "GUARDAR")
	{
		$query = "UPDATE produccion_proc SET 
		rendimiento = '".$Rendimiento."', 
		avance = '".$Avance."', 
		observaciones = '".$Observaciones."' 
		WHERE proceso = 'Trefilado' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
	}
	else if ($Type == "INICIAR")
	{
		$query = "UPDATE produccion_proc SET 
		estado = 'Proceso', 
		rendimiento = '".$Rendimiento."', 
		avance = '".$Avance."', 
		observaciones = '".$Observaciones."', 
		iniciado_por = '".$_SESSION["UserCode"]."', 
		fecha_ini = NOW() 
		WHERE proceso = 'Trefilado' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
	}
	else
	{
		$query = "SELECT iniciado_por FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."' 
		AND proceso = 'Trefilado' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			if ($row["iniciado_por"] == "")
			{
				$query = "UPDATE produccion_proc SET 
				estado = 'Finalizado', 
				rendimiento = '".$Rendimiento."', 
				avance = '".$Avance."', 
				observaciones = '".$Observaciones."', 
				iniciado_por = '".$_SESSION["UserCode"]."', 
				fecha_ini = NOW(), 
				finalizado_por = '".$_SESSION["UserCode"]."', 
				fecha_fin = NOW() 
				WHERE proceso = 'Trefilado' AND orden_produccion = '".$Ord_Produccion."'";
			}
			else
			{
				$query = "UPDATE produccion_proc SET 
				estado = 'Finalizado', 
				rendimiento = '".$Rendimiento."', 
				avance = '".$Avance."', 
				observaciones = '".$Observaciones."', 
				finalizado_por = '".$_SESSION["UserCode"]."', 
				fecha_fin = NOW() 
				WHERE proceso = 'Trefilado' AND orden_produccion = '".$Ord_Produccion."'";
			}
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
		}
	}
	
	// PRODUCCION PROCESOS MOVS 1 & 2
	
	$num = count($data1);
	for ($i = 0; $i < $num; $i++)
	{
		$query = "INSERT INTO produccion_proc_movs 
		(codigo, cantidad, tipo, origen, destino, maquinaria, operario, orden_produccion, cliente_id) VALUES 
		('".$data1[$i]['CodFab']."', '".$data1[$i]['Cantidad']."', 'Obtener', 'Trefilado', 'Trefilado', 
		'".$data2[$i]['Maquinaria']."', '".$data2[$i]['Operario']."', '".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
	}
	
	// PRODUCCION PROCESOS MOVS 3
	if ($data3 != 0)
	{
		foreach($data3 as $item)
		{
			$query = "INSERT INTO produccion_proc_movs 
			(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
			('".$item['CodFab']."', '".$item['Cantidad']."', 'Despunte', 'Trefilado', 'Trefilado', 
			'".$Ord_Produccion."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
			
			if ($Type == "FINALIZAR")
			{
				AfectarInventario($item['CodFab'], $item['Cantidad'], 0, $Ord_Produccion, "", "Despunte", "Produccion_Producir");
			}
		}
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Produccion_Enderezado']))
{
	$main_data = $_GET['MainData'];
	$data1 = $_GET['Data1'];
	$data2 = $_GET['Data2'];
	$data3 = isset($_GET['Data3']) ? $_GET['Data3']:0;
	
	//Main Data
	$Type = isset($main_data[0]["Type"]) ? $main_data[0]["Type"]:die();
	$Estado = isset($main_data[0]["Estado"]) ? $main_data[0]["Estado"]:die();
	$ClienteID = (isset($main_data[0]["ClienteID"]) && $main_data[0]["ClienteID"] != "") ? $main_data[0]["ClienteID"]:"";
	$Fecha = $main_data[0]["Fecha"];
	$Ord_Produccion = isset($main_data[0]["Ord_Produccion"]) ? $main_data[0]["Ord_Produccion"]:"";
	$Ord_Compra = isset($main_data[0]["Ord_Compra"]) ? $main_data[0]["Ord_Compra"]:"";
	$Rendimiento = isset($main_data[0]["Rendimiento"]) ? $main_data[0]["Rendimiento"]:0;
	$Avance = isset($main_data[0]["Avance"]) ? $main_data[0]["Avance"]:0;
	$Observaciones = isset($main_data[0]["Observaciones"]) ? $main_data[0]["Observaciones"]:"";
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."'	
	AND estado = '".$Estado."' AND proceso = 'Corte y Enderezado' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	//-------- DELETE ALL
	// 1º
	$query = "DELETE FROM produccion_proc_movs WHERE tipo = 'Obtener' 
	AND origen = 'Corte y Enderezado' AND orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	// 2º
	$query = "DELETE FROM produccion_proc_movs WHERE tipo = 'Despunte' 
	AND origen = 'Corte y Enderezado' AND orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	
	
	// PRODUCCION PROCESOS
	
	if ($Type == "GUARDAR")
	{
		$query = "UPDATE produccion_proc SET 
		rendimiento = '".$Rendimiento."', 
		avance = '".$Avance."', 
		observaciones = '".$Observaciones."' 
		WHERE proceso = 'Corte y Enderezado' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
	}
	else if ($Type == "INICIAR")
	{
		$query = "UPDATE produccion_proc SET 
		estado = 'Proceso', 
		rendimiento = '".$Rendimiento."', 
		avance = '".$Avance."', 
		observaciones = '".$Observaciones."', 
		iniciado_por = '".$_SESSION["UserCode"]."', 
		fecha_ini = NOW() 
		WHERE proceso = 'Corte y Enderezado' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
	}
	else
	{
		$query = "SELECT iniciado_por FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."' 
		AND proceso = 'Corte y Enderezado' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			if ($row["iniciado_por"] == "")
			{
				$query = "UPDATE produccion_proc SET 
				estado = 'Finalizado', 
				rendimiento = '".$Rendimiento."', 
				avance = '".$Avance."', 
				observaciones = '".$Observaciones."', 
				iniciado_por = '".$_SESSION["UserCode"]."', 
				fecha_ini = NOW(), 
				finalizado_por = '".$_SESSION["UserCode"]."', 
				fecha_fin = NOW() 
				WHERE proceso = 'Corte y Enderezado' AND orden_produccion = '".$Ord_Produccion."'";
			}
			else
			{
				$query = "UPDATE produccion_proc SET 
				estado = 'Finalizado', 
				rendimiento = '".$Rendimiento."', 
				avance = '".$Avance."', 
				observaciones = '".$Observaciones."', 
				finalizado_por = '".$_SESSION["UserCode"]."', 
				fecha_fin = NOW() 
				WHERE proceso = 'Corte y Enderezado' AND orden_produccion = '".$Ord_Produccion."'";
			}
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
		}
	}
	
	// PRODUCCION PROCESOS MOVS 1 & 2
	
	$num = count($data1);
	for ($i = 0; $i < $num; $i++)
	{
		$query = "INSERT INTO produccion_proc_movs 
		(codigo, cantidad, tipo, origen, destino, maquinaria, operario, orden_produccion, cliente_id) VALUES 
		('".$data1[$i]['CodFab']."', '".$data1[$i]['Cantidad']."', 'Obtener', 'Corte y Enderezado', 'Corte y Enderezado', 
		'".$data2[$i]['Maquinaria']."', '".$data2[$i]['Operario']."', '".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
	}
	
	// PRODUCCION PROCESOS MOVS 3
	if ($data3 != 0)
	{
		foreach($data3 as $item)
		{
			$query = "INSERT INTO produccion_proc_movs 
			(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
			('".$item['CodFab']."', '".$item['Cantidad']."', 'Despunte', 'Corte y Enderezado', 'Corte y Enderezado', 
			'".$Ord_Produccion."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
			
			if ($Type == "FINALIZAR")
			{
				AfectarInventario($item['CodFab'], $item['Cantidad'], 0, $Ord_Produccion, "", "Despunte", "Produccion_Producir");
			}
		}
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Produccion_ElectroSoldado']))
{
	$main_data = $_GET['MainData'];
	$data1 = $_GET['Data1'];
	$data2 = $_GET['Data2'];
	$data3 = isset($_GET['Data3']) ? $_GET['Data3']:0;
	
	//Parte 1
	$Type = isset($main_data[0]["Type"]) ? $main_data[0]["Type"]:die();
	$Estado = isset($main_data[0]["Estado"]) ? $main_data[0]["Estado"]:die();
	$ClienteID = (isset($main_data[0]["ClienteID"]) && $main_data[0]["ClienteID"] != "") ? $main_data[0]["ClienteID"]:"";
	$Fecha = $main_data[0]["Fecha"];
	$Ord_Produccion = isset($main_data[0]["Ord_Produccion"]) ? $main_data[0]["Ord_Produccion"]:"";
	$Ord_Compra = isset($main_data[0]["Ord_Compra"]) ? $main_data[0]["Ord_Compra"]:"";
	$Rendimiento = isset($main_data[0]["Rendimiento"]) ? $main_data[0]["Rendimiento"]:0;
	$Avance = isset($main_data[0]["Avance"]) ? $main_data[0]["Avance"]:0;
	$Observaciones = isset($main_data[0]["Observaciones"]) ? $main_data[0]["Observaciones"]:"";
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."'	
	AND estado = '".$Estado."' AND proceso = 'Electrosoldado' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	//-------- DELETE ALL
	// 1º
	$query = "DELETE FROM produccion_proc_movs WHERE tipo = 'Obtener' 
	AND origen = 'Electrosoldado' AND orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	// 2º
	$query = "DELETE FROM produccion_proc_movs WHERE tipo = 'Despunte' 
	AND origen = 'Electrosoldado' AND orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	
	
	// PRODUCCION PROCESOS
	
	if ($Type == "GUARDAR")
	{
		$query = "UPDATE produccion_proc SET 
		rendimiento = '".$Rendimiento."', 
		avance = '".$Avance."', 
		observaciones = '".$Observaciones."' 
		WHERE proceso = 'Electrosoldado' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
	}
	else if ($Type == "INICIAR")
	{
		$query = "UPDATE produccion_proc SET 
		estado = 'Proceso', 
		rendimiento = '".$Rendimiento."', 
		avance = '".$Avance."', 
		observaciones = '".$Observaciones."', 
		iniciado_por = '".$_SESSION["UserCode"]."', 
		fecha_ini = NOW() 
		WHERE proceso = 'Electrosoldado' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
	}
	else
	{
		$query = "SELECT iniciado_por FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."' 
		AND proceso = 'Electrosoldado' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			if ($row["iniciado_por"] == "")
			{
				$query = "UPDATE produccion_proc SET 
				estado = 'Finalizado', 
				rendimiento = '".$Rendimiento."', 
				avance = '".$Avance."', 
				observaciones = '".$Observaciones."', 
				iniciado_por = '".$_SESSION["UserCode"]."', 
				fecha_ini = NOW(), 
				finalizado_por = '".$_SESSION["UserCode"]."', 
				fecha_fin = NOW() 
				WHERE proceso = 'Electrosoldado' AND orden_produccion = '".$Ord_Produccion."'";
			}
			else
			{
				$query = "UPDATE produccion_proc SET 
				estado = 'Finalizado', 
				rendimiento = '".$Rendimiento."', 
				avance = '".$Avance."', 
				observaciones = '".$Observaciones."', 
				finalizado_por = '".$_SESSION["UserCode"]."', 
				fecha_fin = NOW() 
				WHERE proceso = 'Electrosoldado' AND orden_produccion = '".$Ord_Produccion."'";
			}
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
		}
	}
	
	// PRODUCCION PROCESOS MOVS 1 & 2
	
	$num = count($data1);
	for ($i = 0; $i < $num; $i++)
	{
		$query = "INSERT INTO produccion_proc_movs 
		(codigo, cantidad, tipo, origen, destino, maquinaria, operario, orden_produccion, cliente_id) VALUES 
		('".$data1[$i]['CodFab']."', '".$data1[$i]['Cantidad']."', 'Obtener', 'Electrosoldado', 'Electrosoldado', 
		'".$data2[$i]['Maquinaria']."', '".$data2[$i]['Operario']."', '".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
	}
	
	// PRODUCCION PROCESOS MOVS 3
	if ($data3 != 0)
	{
		foreach($data3 as $item)
		{
			$query = "INSERT INTO produccion_proc_movs 
			(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
			('".$item['CodFab']."', '".$item['Cantidad']."', 'Despunte', 'Electrosoldado', 
			'Electrosoldado', '".$Ord_Produccion."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
			
			if ($Type == "FINALIZAR")
			{
				AfectarInventario($item['CodFab'], $item['Cantidad'], 0, $Ord_Produccion, "", "Despunte", "Produccion_Producir");
			}
		}
	}

	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_GET['Produccion_Figurado']))
{
	$main_data = $_GET['MainData'];
	$data1 = $_GET['Data1'];
	$data2 = $_GET['Data2'];
	$data3 = isset($_GET['Data3']) ? $_GET['Data3']:0;
	
	//Parte 1
	$Type = isset($main_data[0]["Type"]) ? $main_data[0]["Type"]:die();
	$Estado = isset($main_data[0]["Estado"]) ? $main_data[0]["Estado"]:die();
	$ClienteID = (isset($main_data[0]["ClienteID"]) && $main_data[0]["ClienteID"] != "") ? $main_data[0]["ClienteID"]:"";
	$Fecha = $main_data[0]["Fecha"];
	$Ord_Produccion = isset($main_data[0]["Ord_Produccion"]) ? $main_data[0]["Ord_Produccion"]:"";
	$Ord_Compra = isset($main_data[0]["Ord_Compra"]) ? $main_data[0]["Ord_Compra"]:"";
	$Rendimiento = isset($main_data[0]["Rendimiento"]) ? $main_data[0]["Rendimiento"]:0;
	$Avance = isset($main_data[0]["Avance"]) ? $main_data[0]["Avance"]:0;
	$Desperdicio = isset($main_data[0]["Desperdicio"]) ? $main_data[0]["Desperdicio"]:0;
	$Observaciones = isset($main_data[0]["Observaciones"]) ? $main_data[0]["Observaciones"]:"";
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."' 
	AND estado = '".$Estado."' AND proceso = 'Figurado' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	//-------- DELETE ALL
	// 1º
	$query = "DELETE FROM produccion_proc_movs WHERE tipo = 'Obtener' 
	AND origen = 'Figurado' AND orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	// 2º
	$query = "DELETE FROM produccion_proc_movs WHERE tipo = 'Despunte' 
	AND origen = 'Figurado' AND orden_produccion = '".$Ord_Produccion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	
	
	// PRODUCCION PROCESOS
	
	if ($Type == "GUARDAR")
	{
		$query = "UPDATE produccion_proc SET 
		rendimiento = '".$Rendimiento."', 
		avance = '".$Avance."', 
		desperdicio = '".$Desperdicio."', 
		observaciones = '".$Observaciones."' 
		WHERE proceso = 'Figurado' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
	}
	else if ($Type == "INICIAR")
	{
		$query = "UPDATE produccion_proc SET 
		estado = 'Proceso', 
		rendimiento = '".$Rendimiento."', 
		avance = '".$Avance."', 
		desperdicio = '".$Desperdicio."', 
		observaciones = '".$Observaciones."', 
		iniciado_por = '".$_SESSION["UserCode"]."', 
		fecha_ini = NOW() 
		WHERE proceso = 'Figurado' AND orden_produccion = '".$Ord_Produccion."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
	}
	else
	{
		$query = "SELECT iniciado_por FROM produccion_proc WHERE orden_produccion = '".$Ord_Produccion."' 
		AND proceso = 'Figurado' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			if ($row["iniciado_por"] == "")
			{
				$query = "UPDATE produccion_proc SET 
				estado = 'Finalizado', 
				rendimiento = '".$Rendimiento."', 
				avance = '".$Avance."', 
				desperdicio = '".$Desperdicio."', 
				observaciones = '".$Observaciones."', 
				iniciado_por = '".$_SESSION["UserCode"]."', 
				fecha_ini = NOW(), 
				finalizado_por = '".$_SESSION["UserCode"]."', 
				fecha_fin = NOW() 
				WHERE proceso = 'Figurado' AND orden_produccion = '".$Ord_Produccion."'";
			}
			else
			{
				$query = "UPDATE produccion_proc SET 
				estado = 'Finalizado', 
				rendimiento = '".$Rendimiento."', 
				avance = '".$Avance."', 
				desperdicio = '".$Desperdicio."', 
				observaciones = '".$Observaciones."', 
				finalizado_por = '".$_SESSION["UserCode"]."', 
				fecha_fin = NOW() 
				WHERE proceso = 'Figurado' AND orden_produccion = '".$Ord_Produccion."'";
			}
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
		}
	}
	
	// PRODUCCION PROCESOS MOVS 1 & 2
	
	$num = count($data1);
	for ($i = 0; $i < $num; $i++)
	{
		$query = "INSERT INTO produccion_proc_movs 
		(codigo, cantidad, tipo, origen, destino, maquinaria, operario, orden_produccion, cliente_id) VALUES 
		('".$data1[$i]['CodFab']."', '".$data1[$i]['Cantidad']."', 'Obtener', 'Figurado', 'Figurado', '".$data2[$i]['Maquinaria']."', 
		'".$data2[$i]['Operario']."', '".$Ord_Produccion."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
	}
	
	// PRODUCCION PROCESOS MOVS 3
	if ($data3 != 0)
	{
		foreach($data3 as $item)
		{
			$query = "INSERT INTO produccion_proc_movs 
			(codigo, cantidad, tipo, origen, destino, orden_produccion, cliente_id) VALUES 
			('".$item['CodFab']."', '".$item['Cantidad']."', 'Despunte', 'Figurado', 'Figurado', '".$Ord_Produccion."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
			
			if ($Type == "FINALIZAR")
			{
				AfectarInventario($item['CodFab'], $item['Cantidad'], 0, $Ord_Produccion, "", "Despunte", "Produccion_Producir");
			}
		}
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Figuracion_Mallas"]))
{
	$data = $_POST["Figuracion_Mallas"];
	$Today = date('Y-m-d');
	$Generar = $data[0]["Generar"];
	if ($Generar == "true")
		$Generar = true;
	else
		$Generar = false;
	$VentaInterno = $data[0]["VentaInterno"];
	$Interno = "";
	$Interno2 = $data[0]["Interno2"];
	$ClienteID = $data[0]["ClienteID"];
	$Obra = $data[0]["Obra"];
	$Total_Peso = $data[0]["Total_Peso"];
	
	foreach($data as $item)
	{
		if ($Generar == true) {
			if ($final == false) {
				$final = true;
				
				$query = "INSERT INTO mallas (interno_venta, cliente_id, obra, pesoTotal, 
				digitado_por, fecha_digitado, estado) VALUES ('".$VentaInterno."', 
				'".$ClienteID."', '".$Obra."', '".$Total_Peso."', 
				'".$_SESSION["UserCode"]."', NOW(), 'Pendiente')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				
				$query = "SELECT cons FROM mallas ORDER BY cons DESC LIMIT 1";
				$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				$row1 = mysql_fetch_array($result2);
				$Interno= $row1['cons'];
			}
			
			$query = "INSERT INTO mallas_movs (interno_malla, iditem, tipo, ref, cant, arrA, finA, arrB, finB, ancho, largo, 
			sepA, sepB, cod1, cod2, varA, varB, pesoA, pesoB, pesoU) VALUES ('".$Interno."', '".$item["iditem"]."', '".$item["Tipo"]."', 
			'".$item["Ref"]."', '".$item["Cant"]."', '".$item["ArrA"]."', '".$item["FinA"]."', 
			'".$item["ArrB"]."', '".$item["FinB"]."', '".$item["Ancho"]."', '".$item["Largo"]."', 
			'".$item["SepA"]."', '".$item["SepB"]."', '".$item["Cod1"]."', '".$item["Cod2"]."', 
			'".$item["VarA"]."', '".$item["VarB"]."', '".$item["PesoA"]."', '".$item["PesoB"]."', '".$item["PesoU"]."') ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		} else {
			$query = "UPDATE mallas SET interno_venta = '".$VentaInterno."', cliente_id = '".$ClienteID."', 
			obra = '".$Obra."', pesoTotal = '".$Total_Peso."' WHERE cons = '".$Interno2."'";
			$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		}
	}
	
		$Data[] = array (
			"MESSAGE" => "OK",
			"Interno" => $Interno,
		);
		echo json_encode($Data);
	
}
else if (isset($_POST["Figuracion_MallasItem"]))
{
	$data = $_POST["Figuracion_MallasItem"];
	$Today = date('Y-m-d');
	$Generar = $data[0]["Generar"];
	if ($Generar == "true")
		$Generar = true;
	else
		$Generar = false;
	$Interno = "";
	$Interno2 = $data[0]["Interno2"];
	$iditem = $data[0]["iditem"];
	$Total_Peso = $data[0]["Total_Peso"];
	
	foreach($data as $item)
	{
		if ($Generar == true) {
			$query = "INSERT INTO mallas_movs (interno_malla, iditem, tipo, ref, cant, arrA, finA, arrB, finB, ancho, largo, 
			sepA, sepB, cod1, cod2, varA, varB, pesoA, pesoB, pesoU) VALUES ('".$Interno2."', '".$item["iditem"]."', '".$item["Tipo"]."', 
			'".$item["Ref"]."', '".$item["Cant"]."', '".$item["ArrA"]."', '".$item["FinA"]."', 
			'".$item["ArrB"]."', '".$item["FinB"]."', '".$item["Ancho"]."', '".$item["Largo"]."', 
			'".$item["SepA"]."', '".$item["SepB"]."', '".$item["Cod1"]."', '".$item["Cod2"]."', 
			'".$item["VarA"]."', '".$item["VarB"]."', '".$item["PesoA"]."', '".$item["PesoB"]."', '".$item["PesoU"]."') ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			
			$query2 = "UPDATE mallas SET pesoTotal = '".$Total_Peso."' WHERE cons = '".$Interno2."'";
			$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			
		} else {
			$query = "UPDATE mallas_movs SET ref = '".$item["Ref"]."', cant = '".$item["Cant"]."', 
			arrA = '".$item["ArrA"]."', finA = '".$item["FinA"]."', arrB = '".$item["ArrB"]."', 
			finB = '".$item["FinB"]."', ancho = '".$item["Ancho"]."', largo = '".$item["Largo"]."', 
			sepA = '".$item["SepA"]."', sepB = '".$item["SepB"]."', cod1 = '".$item["Cod1"]."', 
			cod2 = '".$item["Cod2"]."', varA = '".$item["VarA"]."', varB = '".$item["VarB"]."', 
			pesoA = '".$item["PesoA"]."', pesoB = '".$item["PesoB"]."', 
			pesoU = '".$item["PesoU"]."' WHERE interno_malla = '".$Interno2."' AND iditem = '".$iditem."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");			
			
			$query2 = "UPDATE mallas SET pesoTotal = '".$Total_Peso."' WHERE cons = '".$Interno2."'";
			$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		}
	}
	
		$Data[] = array (
			"MESSAGE" => "OK",
			"Interno" => $Interno,
		);
		echo json_encode($Data);
	
}
else if (isset($_POST["Figuracion_Crear"]))
{
	$data = $_POST["Figuracion_Crear"];
	$Today = date('Y-m-d');
	$Generar = $data[0]["Generar"];
	if ($Generar == "true")
		$Generar = true;
	else
		$Generar = false;
	$Interno = "";
	$Venta_Interno = "";
	$ClienteID = $data[0]["ClienteID"];
	$Obra = $data[0]["Obra"];
	$Total_Peso = $data[0]["Total_Peso"];
	$Total_Fig = $data[0]["Total_Fig"];
	$Desperdicio = $data[0]["Desperdicio"];
	
	foreach($data as $item)
	{
		/*
		// To Decode...
		$Dimensiones = $item["Dimensiones"];
		$Dimensiones = json_decode($Dimensiones, true);
		*/
		
		if ($final == false)
		{
			$final = true;
			
			// Generar Cotizacion
			if ($Generar)
			{
				$query = "SELECT id FROM fact_final ORDER BY id DESC LIMIT 1";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
				$row1 = mysql_fetch_array($result);
				$id = $row1['id'] + 1;
				$len = strlen($id);
				$zero = "";
				if ($len < 8)
				{
					$num = 8 - $len;
					for ($a = 0; $a < $num; $a++)
						$zero .= "0";
					$Venta_Interno = "FER".$zero."".$id."";
				}
				else
					$Venta_Interno = "FER".$id."";
				
				$query = "INSERT INTO fact_final 
				(`interno`, `remision`, `fecha_remision`, `factura`, `orden_compra`, `cliente_id`, `forma_pago`, 
				`notas`, `estado`, `digitado_por`, `fecha_digitado`, `tipo_pedido`) VALUES 
				('".$Venta_Interno."', '".$Venta_Interno."', '".$Today."', '".$Venta_Interno."', '".$Venta_Interno."', 
				'".$ClienteID."', 'Credito', 'Creado Automaticamente Desde Figuracion', 
				'Creado', '".$_SESSION["UserCode"]."', NOW(), 'Cotizacion')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
				
				// Get Product Info
				$query = "SELECT cod_fab, peso, lista1 FROM productos";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$ProductoPeso[$row["cod_fab"]] = $row["peso"];
						$ProductoUnitario[$row["cod_fab"]] = $row["lista1"];
					}
				}
				
				$SubTotal = 0;
				$Total = 0;
				$Iva = 0;
				$Peso = 0;
				$Total_Fig_Dec = json_decode($Total_Fig, true);
				
				// Movs
				foreach($Total_Fig_Dec as $Item)
				{
					$Codigo = $Item["Codigo"];
					$Cantidad = $Item["Peso"]; // El Resumen de Figuracion; la Cantidad es Peso
					//$Peso = isset($ProductoPeso[$Codigo]) ? $ProductoPeso[$Codigo]:0;
					$Unitario = isset($ProductoUnitario[$Codigo]) ? $ProductoUnitario[$Codigo]:0;
					
					$SubTotal += $Unitario * $Cantidad;
					$Peso += $Cantidad;
					
					//-- Ventas Movs
					$query = "INSERT INTO fact_movs
					(codigo, cantidad, precio, interno, orden_compra, factura, remision, cliente_id) VALUES
					('".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$Venta_Interno."', '".$Venta_Interno."', 
					'".$Venta_Interno."', '".$Venta_Interno."', '".$ClienteID."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
				}
				
				$Iva = $SubTotal - ($SubTotal / 1.16);
				$Total = $SubTotal;
				
				$query = "UPDATE fact_final SET 
				sub_total = '".$SubTotal."', 
				total = '".$Total."', 
				iva = '".$Iva."', 
				peso = '".$Peso."' 
				WHERE interno = '".$Venta_Interno."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-5: ".mysql_error():"");
			}
			
			$query = "SELECT id FROM figuracion_cartilla ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 6)
			{
				$num = 6 - $len;
				for ($a = 0; $a < $num; $a++)
					$zero .= "0";
				$Interno = "FIG".$zero."".$id."";
			}
			else
				$Interno = "FIG".$id."";
			
			$query = "INSERT INTO figuracion_cartilla (interno, venta_interno, cliente_id, obra, peso, 
			total_fig, desperdicio, digitado_por, fecha_digitado, estado) VALUES ('".$Interno."', 
			'".$Venta_Interno."', '".$ClienteID."', '".$Obra."', '".$Total_Peso."', '".$Total_Fig."', 
			'".$Desperdicio."', '".$_SESSION["UserCode"]."', NOW(), 'Pendiente')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		}
		
		$query = "INSERT INTO figuracion_cartilla_movs (figura, codigo, cantidad, dimensiones, longitud, 
		peso, ubicacion, interno, venta_interno, cliente_id, estado) VALUES ('".$item["Figura"]."', '".$item["Codigo"]."', 
		'".$item["Cantidad"]."', '".$item["Dimensiones"]."', '".$item["Longitud"]."', '".$item["Peso"]."', 
		'".$item["Ubicacion"]."', '".$Interno."', '".$Venta_Interno."', '".$ClienteID."', 'Pendiente') ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	}
	
	$Data[] = array (
		"MESSAGE" => "OK",
		"Interno" => $Interno,
		"Venta_Interno" => $Venta_Interno,
	);
	echo json_encode($Data);
}
else if (isset($_POST["Figuracion_Modificar"]))
{
	$data = $_POST["Figuracion_Modificar"];
	$Today = date('Y-m-d');
	$Generar = $data[0]["Generar"];
	if ($Generar == "true")
		$Generar = true;
	else
		$Generar = false;
	$Interno = isset($data[0]["Interno"]) ? $data[0]["Interno"]:die();
	$Venta_Interno = $data[0]["Venta_Interno"];
	$Orden_Produccion = $data[0]["Orden_Produccion"];
	$ClienteID = $data[0]["ClienteID"];
	$Obra = $data[0]["Obra"];
	$Total_Peso = $data[0]["Total_Peso"];
	$Total_Fig = $data[0]["Total_Fig"];
	$Desperdicio = $data[0]["Desperdicio"];
	
	foreach($data as $item)
	{
		if ($final == false)
		{
			$final = true;
			
			// Generar Cotizacion
			if ($Generar)
			{
				$query = "SELECT id FROM fact_final ORDER BY id DESC LIMIT 1";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
				$row1 = mysql_fetch_array($result);
				$id = $row1['id'] + 1;
				$len = strlen($id);
				$zero = "";
				if ($len < 8)
				{
					$num = 8 - $len;
					for ($a = 0; $a < $num; $a++)
						$zero .= "0";
					$Venta_Interno = "FER".$zero."".$id."";
				}
				else
					$Venta_Interno = "FER".$id."";
				
				$query = "INSERT INTO fact_final 
				(`interno`, `remision`, `fecha_remision`, `factura`, `orden_compra`, `cliente_id`, `forma_pago`, 
				`notas`, `estado`, `digitado_por`, `fecha_digitado`, `tipo_pedido`) VALUES 
				('".$Venta_Interno."', '".$Venta_Interno."', '".$Today."', '".$Venta_Interno."', '".$Venta_Interno."', 
				'".$ClienteID."', 'Credito', 'Creado Automaticamente Desde Figuracion', 
				'Creado', '".$_SESSION["UserCode"]."', NOW(), 'Cotizacion')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
				
				// Get Product Info
				$query = "SELECT cod_fab, peso, lista1 FROM productos";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$ProductoPeso[$row["cod_fab"]] = $row["peso"];
						$ProductoUnitario[$row["cod_fab"]] = $row["lista1"];
					}
				}
				
				$SubTotal = 0;
				$Total = 0;
				$Iva = 0;
				$Peso = 0;
				$Total_Fig_Dec = json_decode($Total_Fig, true);
				
				// Movs
				foreach($Total_Fig_Dec as $Item)
				{
					$Codigo = $Item["Codigo"];
					$Cantidad = $Item["Peso"]; // El Resumen de Figuracion; la Cantidad es Peso
					//$Peso = isset($ProductoPeso[$Codigo]) ? $ProductoPeso[$Codigo]:0;
					$Unitario = isset($ProductoUnitario[$Codigo]) ? $ProductoUnitario[$Codigo]:0;
					
					$SubTotal += $Unitario * $Cantidad;
					$Peso += $Cantidad;
					
					//-- Ventas Movs
					$query = "INSERT INTO fact_movs
					(codigo, cantidad, precio, interno, orden_compra, factura, remision, cliente_id) VALUES
					('".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$Venta_Interno."', '".$Venta_Interno."', 
					'".$Venta_Interno."', '".$Venta_Interno."', '".$ClienteID."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
				}
				
				$Iva = $SubTotal - ($SubTotal / 1.16);
				$Total = $SubTotal;
				
				$query = "UPDATE fact_final SET 
				sub_total = '".$SubTotal."', 
				total = '".$Total."', 
				iva = '".$Iva."', 
				peso = '".$Peso."' 
				WHERE interno = '".$Venta_Interno."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-5: ".mysql_error():"");
			}
			
			$query = "UPDATE figuracion_cartilla SET 
			venta_interno = '".$Venta_Interno."', 
			orden_produccion = '".$Orden_Produccion."', 
			cliente_id = '".$ClienteID."', 
			obra = '".$Obra."', 
			peso = '".$Total_Peso."', 
			total_fig = '".$Total_Fig."', 
			desperdicio = '".$Desperdicio."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado =  NOW()
			WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
			
			$query = "DELETE FROM figuracion_cartilla_movs WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		}
		
		$query = "INSERT INTO figuracion_cartilla_movs (figura, codigo, cantidad, dimensiones, longitud, 
		peso, ubicacion, interno, venta_interno, orden_produccion, cliente_id, estado) VALUES ('".$item["Figura"]."', '".$item["Codigo"]."', 
		'".$item["Cantidad"]."', '".$item["Dimensiones"]."', '".$item["Longitud"]."', '".$item["Peso"]."', 
		'".$item["Ubicacion"]."', '".$Interno."', '".$Venta_Interno."', '".$Orden_Produccion."', '".$ClienteID."', 'Pendiente') ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	}
	
	$Data[] = array (
		"MESSAGE" => "OK",
		"Venta_Interno" => $Venta_Interno,
	);
	echo json_encode($Data);
}
else if (isset($_POST["Figuracion_Aprobar"]))
{
	$Old_Estado = isset($_POST["Old_Estado"]) ? $_POST["Old_Estado"]:"Pendiente";
	$New_Estado = isset($_POST["New_Estado"]) ? $_POST["New_Estado"]:"Pendiente";
	$Interno = isset($_POST["Interno"]) ? $_POST["Interno"]:"";
	$Today = date('Y-m-d');
	
	//-------- CHECK CHANGES
	$query = "SELECT * FROM figuracion_cartilla WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		if ($row["estado"] != $Old_Estado)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "CHANGED",
			);
		}
		else
		{
			$query = "UPDATE figuracion_cartilla SET estado = '".$New_Estado."'";
			$query .= ($New_Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
			$query .= ($New_Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
			$query .= " WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			if (mysql_affected_rows() > 0)
			{
				switch($New_Estado)
				{
					case "Anulado":
						$ReturnData[0] = array(
							"MESSAGE" => "OK",
						);
					break;
					
					case "Aprobado":
						if ($row["venta_interno"] == "" && $row["orden_produccion"] == "")
						{
							// Venta
							$query = "SELECT id FROM fact_final ORDER BY id DESC LIMIT 1";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
							$row1 = mysql_fetch_array($result);
							$id = $row1['id'] + 1;
							$len = strlen($id);
							$zero = "";
							if ($len < 8)
							{
								$num = 8 - $len;
								for ($a = 0; $a < $num; $a++)
									$zero .= "0";
								$Venta_Interno = "FER".$zero."".$id."";
							}
							else
								$Venta_Interno = "FER".$id."";
							
							$query = "INSERT INTO fact_final 
							(`interno`, `remision`, `fecha_remision`, `factura`, `orden_compra`, `cliente_id`, `forma_pago`, 
							`notas`, `estado`, `digitado_por`, `fecha_digitado`, `tipo_pedido`) VALUES 
							('".$Venta_Interno."', '".$Venta_Interno."', '".$Today."', '".$Venta_Interno."', '".$Venta_Interno."', 
							'".$row["cliente_id"]."', 'Credito', 'Creado Automaticamente Desde Figuracion', 
							'Creado', '".$row["digitado_por"]."', NOW(), 'Pedido')";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
							
							// OP
							$query = "SELECT id FROM produccion_final ORDER BY id DESC LIMIT 1";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
							$row1 = mysql_fetch_array($result);
							$id = $row1['id'] + 1;
							$len = strlen($id);
							$zero = "";
							if ($len < 6)
							{
								$num = 6 - $len;
								for ($a = 0; $a < $num; $a++)
									$zero .= "0";
								$Orden_Produccion = "OP".$zero."".$id."";
							}
							else
								$Orden_Produccion = "OP".$id."";
							
							$query = "INSERT INTO produccion_final 
							(orden_produccion, cartilla, destino, cliente_id, fecha, estado, digitado_por, fecha_digitado) VALUES 
							('".$Orden_Produccion."', '".$Interno."', 'Cliente', '".$row["cliente_id"]."', '".$Today."', 'Pendiente', 
							'".$row["digitado_por"]."', NOW())";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-4: ".mysql_error():"");
							
							// Get Product Info
							$query = "SELECT cod_fab, peso, lista1 FROM productos";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-5: ".mysql_error():"");
							if (mysql_num_rows($result) > 0)
							{
								while ($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
								{
									$ProductoPeso[$row1["cod_fab"]] = $row1["peso"];
									$ProductoUnitario[$row1["cod_fab"]] = $row1["lista1"];
								}
							}
							
							$SubTotal = 0;
							$Total = 0;
							$Iva = 0;
							$Peso = 0;
							$Total_Fig_Dec = json_decode($row["total_fig"], true);
							
							// Movs
							foreach($Total_Fig_Dec as $Item)
							{
								$Codigo = $Item["Codigo"];
								$Cantidad = $Item["Peso"]; // El Resumen de Figuracion; la Cantidad es Peso
								//$Peso = isset($ProductoPeso[$Codigo]) ? $ProductoPeso[$Codigo]:0;
								$Unitario = isset($ProductoUnitario[$Codigo]) ? $ProductoUnitario[$Codigo]:0;
								
								$SubTotal += $Unitario * $Cantidad;
								$Peso += $Cantidad;
								
								//-- Ventas Movs
								$query = "INSERT INTO fact_movs
								(codigo, cantidad, precio, interno, orden_compra, factura, remision, cliente_id) VALUES
								('".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$Venta_Interno."', '".$Venta_Interno."', 
								'".$Venta_Interno."', '".$Venta_Interno."', '".$row["cliente_id"]."')";
								$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-6: ".mysql_error():"");
								
								//-- Produccion Movs
								$query = "INSERT INTO produccion_movs 
								(codigo, cantidad, tipo, orden_produccion, orden_compra, interno, cliente_id) VALUES 
								('".$Codigo."', '".$Cantidad."', 'Obtener', '".$Orden_Produccion."', '".$Venta_Interno."', 
								'".$Venta_Interno."', '".$row["cliente_id"]."')";
								$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-7: ".mysql_error():"");
							}
							
							$Iva = $SubTotal - ($SubTotal / 1.16);
							$Total = $SubTotal;
							
							$query = "UPDATE fact_final SET 
							orden_produccion = '".$Orden_Produccion."', 
							sub_total = '".$SubTotal."', 
							total = '".$Total."', 
							iva = '".$Iva."', 
							peso = '".$Peso."' 
							WHERE interno = '".$Venta_Interno."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-8: ".mysql_error():"");
							
							$query = "UPDATE produccion_final SET 
							orden_compra = '".$Venta_Interno."', 
							interno = '".$Venta_Interno."' 
							WHERE orden_produccion = '".$Orden_Produccion."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-9: ".mysql_error():"");
							
							$ReturnData[0] = array(
								"MESSAGE" => "OK",
							);
						}
						else
						{
							$ReturnData[0] = array(
								"MESSAGE" => "EXIST",
							);
						}
					break;
				}
			}
		}
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_POST["Figuracion_Visualizar"]))
{
	$Codigo = $_POST["Codigo"];
	$Figura = $_POST["Figura"];
	$Dimensiones = $_POST["Dimensiones"];
	$CantidadOriginal = $_POST["Cantidad"];
	$Cantidad = $CantidadOriginal;
	$CantidadFinal = $Cantidad;
	
	$query = "SELECT * FROM figuracion_cartilla_movs WHERE codigo = '".$Codigo."' AND figura = '".$Figura."' AND dimensiones = '".$Dimensiones."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($Cantidad <= 0)
			{
				if ($CantidadOriginal == 0)
				{
					$query = "UPDATE figuracion_cartilla_movs SET cantidad2 = '0', estado = 'Pendiente' WHERE id = '".$row["id"]."' ";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				}
				else
					break;
			}
			else
			{
				$CantidadTmp = $Cantidad - $row["cantidad"];
				
				if ($CantidadTmp < 1)
				{
					if ($Cantidad == $row["cantidad"])
						$query = "UPDATE figuracion_cartilla_movs SET cantidad2 = '".$Cantidad."', estado = 'Finalizado' WHERE id = '".$row["id"]."' ";
					else
						$query = "UPDATE figuracion_cartilla_movs SET cantidad2 = '".$Cantidad."', estado = 'Proceso' WHERE id = '".$row["id"]."' ";
					$Cantidad = 0;
				}
				else if ($Cantidad > $row["cantidad"])
				{
					$query = "UPDATE figuracion_cartilla_movs SET cantidad2 = cantidad, estado = 'Finalizado' WHERE id = '".$row["id"]."' ";
					$Cantidad -= $row["cantidad"];
				}
				else
				{
					$Cantidad -= $row["cantidad"];
					$query = "UPDATE figuracion_cartilla_movs SET cantidad2 = '".$Cantidad."', estado = 'Proceso' WHERE id = '".$row["id"]."' ";
				}
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
			}
		}
		
		$Data[] = array (
			"MESSAGE" => "OK",
		);
	}
	else
	{
		$Data[] = array (
			"MESSAGE" => "ERROR",
		);
	}
	
	echo json_encode($Data);
}
else if (isset($_POST["Figuracion_Generar_Tickets"]))
{
	$Today = date('Y-m-d H:i:s');
	$Cartilla = isset($_POST["Cartilla"]) ? $_POST["Cartilla"]:die();
	$Figura = isset($_POST["Figura"]) ? $_POST["Figura"]:die();
	$Codigo = isset($_POST["Codigo"]) ? $_POST["Codigo"]:die();
	$Detalle = isset($_POST["Detalle"]) ? $_POST["Detalle"]:die();
	$Cantidad1 = isset($_POST["Cantidad1"]) ? $_POST["Cantidad1"]:die();
	$Cantidad2 = isset($_POST["Cantidad2"]) ? $_POST["Cantidad2"]:die();
	$Tickets = isset($_POST["Tickets"]) ? $_POST["Tickets"]:die();
	
	$Tmp = intval($Cantidad1 / $Cantidad2);
	$Sobrante = $Cantidad1 - ($Tmp * $Cantidad2);
	
	$query = "SELECT id FROM figuracion_cartilla_tickets ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$row = mysql_fetch_array($result);
	
	for($i = 0; $i < $Tickets; $i++)
	{
		$id = $row["id"] + 1 + $i;
		$len = strlen($id);
		$zero = "";
		if ($len < 6)
		{
			$num = 6 - $len;
			for ($a = 0; $a < $num; $a++)
				$zero .= "0";
			$Interno = $zero."".$id."";
		}
		else
		{
			$Interno = $id."";
		}
	
		$Data[] = array (
			"MESSAGE" => $Interno,
		);
		
		if ($Tickets - $i == 1 && $Sobrante > 0)
		{
			$query = "INSERT INTO figuracion_cartilla_tickets (interno, ticket, figura, codigo, detalle, 
			cantidad1, cantidad2, cartilla, fecha) VALUES ('".$Interno."', '".($i+1)."', '".$Figura."', 
			'".$Codigo."', '".$Detalle."', '".$Cantidad1."', '".$Sobrante."', '".$Cartilla."', '".$Today."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		}
		else
		{
			$query = "INSERT INTO figuracion_cartilla_tickets (interno, ticket, figura, codigo, detalle, 
			cantidad1, cantidad2, cartilla, fecha) VALUES ('".$Interno."', '".($i+1)."', '".$Figura."', 
			'".$Codigo."', '".$Detalle."', '".$Cantidad1."', '".$Cantidad2."', '".$Cartilla."', '".$Today."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		}
	}
	
	if (isset($Data))
		echo json_encode($Data);
	else
		echo json_encode(Array());
}
else if (isset($_POST["Figuracion_Figuras_Agregar"]))
{
	$Imagen = $_POST["Imagen"];
	$Figura = $_POST["Figura"];
	$Dimensiones = $_POST["Dimensiones"];
	$Estribo = $_POST["Estribo"] == "true" ? 1:0;
	$SemiCirculo = $_POST["SemiCirculo"] == "true" ? 1:0;
	$Circular = $_POST["Circular"] == "true" ? 1:0;
	$Vueltas = $_POST["Vueltas"] == "true" ? 1:0;
	
	$FilePath = pathinfo($Imagen);
	$NewImage = "figuras/".$FilePath["basename"]."";
	if (rename("../".$Imagen."","../images/".$NewImage.""))
	{
		$Imagen = $NewImage;
		
		$query = "INSERT INTO figuracion_figuras (fig, img, dimensiones, estribo, semicirculo, circular, vueltas) 
		VALUES ('".$Figura."', '".$Imagen."', '".$Dimensiones."', '".$Estribo."', '".$SemiCirculo."', '".$Circular."', '".$Vueltas."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		$Data[] = array (
			"MESSAGE" => "OK",
		);
	}
	else
	{
		$Data[] = array (
			"MESSAGE" => "ERROR",
		);
	}
	echo json_encode($Data);
}
else if (isset($_POST["Figuracion_Figuras_Actualizar"]))
{
	$ID = $_POST["ID"];
	$Figura = $_POST["Figura"];
	$Dimensiones = $_POST["Dimensiones"];
	$Estribo = $_POST["Estribo"] == "true" ? 1:0;
	$SemiCirculo = $_POST["SemiCirculo"] == "true" ? 1:0;
	$Circular = $_POST["Circular"] == "true" ? 1:0;
	$Vueltas = $_POST["Vueltas"] == "true" ? 1:0;
	
	$query = "UPDATE figuracion_figuras SET 
	fig = '".$Figura."', 
	dimensiones = '".$Dimensiones."', 
	estribo = '".$Estribo."', 
	semicirculo = '".$SemiCirculo."', 
	circular = '".$Circular."', 
	vueltas = '".$Vueltas."' 
	WHERE id = '".$ID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$Data[] = array (
		"MESSAGE" => "OK",
	);
	echo json_encode($Data);
}
else if (isset($_POST["Figuracion_Figuras_Borrar"]))
{
	$ID = $_POST["ID"];
	$Imagen = $_POST["Imagen"];
	
	$TargetFile = "../images/".$Imagen;
	if ($WINDOWS)
	{
		if (chmod($TargetFile,0777))
		{
			if (!unlink($TargetFile))
				die();
		}
	}
	else
	{
		//Linux
		if (!unlink($TargetFile))
			die();
	}
	
	$query = "DELETE FROM figuracion_figuras WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$Data[] = array (
		"MESSAGE" => "OK",
	);
	echo json_encode($Data);
}
else if (isset($_POST["Productos_Para_Figuracion"]))
{
	$Codigo = isset($_POST["Codigo"]) ? $_POST["Codigo"]:die();
	$K = isset($_POST["K"]) ? $_POST["K"]:die();
	
	$query = "UPDATE productos SET k = '".$K."' WHERE cod_fab = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_POST["Repuestos_Agregar"]))
{
	$Codigo = isset($_POST["Codigo"]) ? $_POST["Codigo"]:die();
	$Nombre = isset($_POST["Nombre"]) ? $_POST["Nombre"]:die();
	$Parte = isset($_POST["Parte"]) ? $_POST["Parte"]:die();
	$Peso = isset($_POST["Peso"]) ? $_POST["Peso"]:die();
	$Valor = isset($_POST["Valor"]) ? $_POST["Valor"]:die();
	
	$query = "INSERT INTO repuestos (codigo, nombre, parte, peso, valor) VALUES 
	('".$Codigo."', '".$Nombre."', '".$Parte."', '".$Peso."', '".$Valor."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_POST["Repuestos_Actualizar"]))
{
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Codigo = isset($_POST["Codigo"]) ? $_POST["Codigo"]:die();
	$Nombre = isset($_POST["Nombre"]) ? $_POST["Nombre"]:die();
	$Parte = isset($_POST["Parte"]) ? $_POST["Parte"]:die();
	$Peso = isset($_POST["Peso"]) ? $_POST["Peso"]:die();
	$Valor = isset($_POST["Valor"]) ? $_POST["Valor"]:die();
	
	$query = "UPDATE repuestos SET codigo = '".$Codigo."', nombre = '".$Nombre."', 
	parte = '".$Parte."', peso = '".$Peso."', valor = '".$Valor."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_POST["Repuestos_Borrar"]))
{
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	
	$query = "DELETE FROM repuestos WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_POST["Repuestos_Partes_Agregar"]))
{
	$Parte = isset($_POST["Parte"]) ? $_POST["Parte"]:die();
	
	$query = "INSERT INTO repuestos_partes (parte) VALUES ('".$Parte."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_POST["Repuestos_Partes_Actualizar"]))
{
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Parte = isset($_POST["Parte"]) ? $_POST["Parte"]:die();
	
	$query = "UPDATE repuestos_partes SET parte = '".$Parte."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_POST["Repuestos_Partes_Borrar"]))
{
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	
	$query = "DELETE FROM repuestos_partes WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_POST["Maquinaria_Crear"]))
{
	$main_data = isset($_POST["MainData"]) ? $_POST["MainData"]:[[]];
	$data = isset($_POST["Array"]) ? $_POST["Array"]:[[]];
	$data1 = isset($_POST["Array1"]) ? $_POST["Array1"]:[[]];
	$data2 = isset($_POST["Array2"]) ? $_POST["Array2"]:[[]];
	$data3 = isset($_POST["Array3"]) ? $_POST["Array3"]:[[]];
	
	$Estado = $main_data[0]["Estado"];
	$Operador = $main_data[0]["Operador"];
	$Mecanico = $main_data[0]["Mecanico"];
	$Fecha_Ini = $main_data[0]["Fecha_Ini"];
	$Fecha_Fin = $main_data[0]["Fecha_Fin"];
	$Clasificacion = $main_data[0]["Clasificacion"];
	$Tipo = $main_data[0]["Tipo"];
	$Motivo = $main_data[0]["Motivo"];
	$Diagnostico = $main_data[0]["Diagnostico"];
	$Total = $main_data[0]["Total"];
	$Causa = $main_data[0]["Causa"];
	$Procedimiento = $main_data[0]["Procedimiento"];
	$Observaciones = $main_data[0]["Observaciones"];
	//
	$Codigo = "";
	$Cantidad = "";
	$Unitario = "";
	$ClienteID = "";
	$Total_Proveedor = "";
	
	$Ord_Reparacion = 0;
	
	$query = "SELECT id FROM maquinaria_final ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$row = mysql_fetch_array($result);
	$id = $row['id'] + 1;
	$len = strlen($id);
	$zero = "";
	if ($len < 7)
	{
		$num = 7 - $len;
		for ($a = 0; $a < $num; $a++)
			$zero .= "0";
		
		$Ord_Reparacion = "OR".$zero."".$id."";
	}
	else
		$Ord_Reparacion = "OR".$id."";
	
	if ($data1[0]["Factura"] == "")
		$Factura1 = $Ord_Reparacion;
	else
		$Factura1 = $data1[0]["Factura"];
	
	if (!isset($data2[0]["Factura"]))//if ($data2[0]["Factura"] == "")
		$Factura2 = $Ord_Reparacion;
	else
		$Factura2 = $data2[0]["Factura"];
	
	if (!isset($data3[0]["Factura"]))
		$Factura3 = $Ord_Reparacion;
	else
		$Factura3 = $data3[0]["Factura"];
	
	//--------------------------------------------------------------------
	//------------------------- INSERT COMMAND ---------------------------
	//--------------------------------------------------------------------
	
	$query = "INSERT INTO maquinaria_final (ord_reparacion, estado_maquina, operador, mecanico, fecha_ini, fecha_fin, 
	clasificacion, tipo, motivo, diagnostico, total, causa, procedimiento, observaciones, ";
	$query .= isset($data1[0]["ClienteID"]) ? "proveedor1, factura1, total1, ":"";
	$query .= isset($data2[0]["ClienteID"]) ? "proveedor2, factura2, total2, ":"";
	$query .= isset($data3[0]["ClienteID"]) ? "proveedor3, factura3, total3, ":"";
	$query .= "estado, digitado_por, fecha_digitado) VALUES ('".$Ord_Reparacion."', '".$Estado."', '".$Operador."', 
	'".$Mecanico."', '".$Fecha_Ini."', '".$Fecha_Fin."', '".$Clasificacion."', '".$Tipo."', '".$Motivo."', '".$Diagnostico."', 
	'".$Total."', '".$Causa."', '".$Procedimiento."', '".$Observaciones."', ";
	$query .= isset($data1[0]["ClienteID"]) ? "'".$data1[0]["ClienteID"]."', '".$Factura1."', '".$data1[0]["Total"]."', ":"";
	$query .= isset($data2[0]["ClienteID"]) ? "'".$data2[0]["ClienteID"]."', '".$Factura2."', '".$data2[0]["Total"]."', ":"";
	$query .= isset($data3[0]["ClienteID"]) ? "'".$data3[0]["ClienteID"]."', '".$Factura3."', '".$data3[0]["Total"]."', ":"";
	$query .= "'Pendiente', '".$_SESSION["UserCode"]."', NOW())";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	
	//CxP_Movs
	if (isset($data1[0]["ClienteID"]))
	{
		$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, valor, saldo, 
		estado, digitado_por, fecha_digitado, grupo, subgrupo, subgrupo2) VALUES ('".$Ord_Reparacion."', '".$Ord_Reparacion."', 
		'".$Factura1."', '".$data1[0]["ClienteID"]."', 'Compra', '".$data1[0]["Total"]."', '".$data1[0]["Total"]."', 'Pendiente', 
		'".$_SESSION["UserCode"]."', NOW(), '".$Clasificacion."', '".$Motivo."', '".$Tipo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	}
	
	if (isset($data2[0]["ClienteID"]))
	{
		$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, valor, saldo, 
		estado, digitado_por, fecha_digitado, grupo, subgrupo, subgrupo2) VALUES ('".$Ord_Reparacion."', '".$Ord_Reparacion."', 
		'".$Factura2."', '".$data2[0]["ClienteID"]."', 'Compra', '".$data2[0]["Total"]."', '".$data2[0]["Total"]."', 'Pendiente', 
		'".$_SESSION["UserCode"]."', NOW(), '".$Clasificacion."', '".$Motivo."', '".$Tipo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
	}
	
	if (isset($data3[0]["ClienteID"]))
	{
		$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, valor, saldo, 
		estado, digitado_por, fecha_digitado, grupo, subgrupo, subgrupo2) VALUES ('".$Ord_Reparacion."', '".$Ord_Reparacion."', 
		'".$Factura3."', '".$data3[0]["ClienteID"]."', 'Compra', '".$data3[0]["Total"]."', '".$data3[0]["Total"]."', 'Pendiente', 
		'".$_SESSION["UserCode"]."', NOW(), '".$Clasificacion."', '".$Motivo."', '".$Tipo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
	}
	
	//---
	
	$num = count($data);
	for ($i = 0; $i < $num; $i++)
	{
		$Parte = $data[$i]["Parte"];
		$Problema = $data[$i]["Problema"];
		$Diagnostico = $data[$i]["Diagnostico"];
		//---
		$query = "INSERT INTO maquinaria_partes (ord_reparacion, parte, problema, diagnostico) 
		VALUES ('".$Ord_Reparacion."', '".$Parte."', '".$Problema."', '".$Diagnostico."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	}
	
	$num = count($data1);
	for ($i = 0; $i < $num; $i++)
	{
		$Codigo = $data1[$i]["Codigo"];
		$Cantidad = $data1[$i]["Cantidad"];
		$Unitario = $data1[$i]["Unitario"];
		if ($i == 0)
			$ClienteID = $data1[$i]["ClienteID"];
		//---
		$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor)
		VALUES ('".$Ord_Reparacion."', '".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	}
	
	$num = count($data2);
	for ($i = 0; $i < $num; $i++)
	{
		if (isset($data2[$i]["Codigo"]))
		{
			$Codigo = $data2[$i]["Codigo"];
			$Cantidad = $data2[$i]["Cantidad"];
			$Unitario = $data2[$i]["Unitario"];
			if ($i == 0)
				$ClienteID = $data2[$i]["ClienteID"];
			//---
			$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor)
			VALUES ('".$Ord_Reparacion."', '".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		}
	}
	
	$num = count($data3);
	for ($i = 0; $i < $num; $i++)
	{
		if (isset($data3[$i]["Codigo"]))
		{
			$Codigo = $data3[$i]["Codigo"];
			$Cantidad = $data3[$i]["Cantidad"];
			$Unitario = $data3[$i]["Unitario"];
			if ($i == 0)
				$ClienteID = $data3[$i]["ClienteID"];
			//---
			$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor)
			VALUES ('".$Ord_Reparacion."', '".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$ClienteID."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
		}
	}
	echo $Ord_Reparacion;
}
else if (isset($_GET['Maquinaria_Modificar']))// Agregar Digitador Original
{
	$main_data = isset($_GET["MainData"]) ? $_GET["MainData"]:[[]];
	$data = isset($_GET["Array"]) ? $_GET["Array"]:[[]];
	$data1 = isset($_GET["Array1"]) ? $_GET["Array1"]:[[]];
	$data2 = isset($_GET["Array2"]) ? $_GET["Array2"]:[[]];
	$data3 = isset($_GET["Array3"]) ? $_GET["Array3"]:[[]];
	
	$Ord_Reparacion = $main_data[0]["Ord_Reparacion"];
	$Estado = $main_data[0]["Estado"];
	$Operador = $main_data[0]["Operador"];
	$Mecanico = $main_data[0]["Mecanico"];
	$Fecha_Ini = $main_data[0]["Fecha_Ini"];
	$Fecha_Fin = $main_data[0]["Fecha_Fin"];
	$Clasificacion = $main_data[0]["Clasificacion"];
	$Tipo = $main_data[0]["Tipo"];
	$Motivo = $main_data[0]["Motivo"];
	$Diagnostico = $main_data[0]["Diagnostico"];
	$Total = $main_data[0]["Total"];
	$Causa = $main_data[0]["Causa"];
	$Procedimiento = $main_data[0]["Procedimiento"];
	$Observaciones = $main_data[0]["Observaciones"];
	//
	$Codigo = "";
	$Cantidad = "";
	$Unitario = "";
	$ClienteID = "";
	$Total_Proveedor = "";
	
	
	//--------------------------------------------------------------------
	//------------------------- UPDATE COMMAND ---------------------------
	//--------------------------------------------------------------------
	
	if ($data1[0]["Factura"] == "")
		$Factura1 = $Ord_Reparacion;
	else
		$Factura1 = $data1[0]["Factura"];
	
	if (!isset($data2[0]["Factura"]))//if ($data2[0]["Factura"] == "")
		$Factura2 = $Ord_Reparacion;
	else
		$Factura2 = $data2[0]["Factura"];
	
	if (!isset($data3[0]["Factura"]))//if ($data3[0]["Factura"] == "")
		$Factura3 = $Ord_Reparacion;
	else
		$Factura3 = $data3[0]["Factura"];
	
	$query = "UPDATE maquinaria_final SET 
	estado_maquina = '".$Estado."', 
	operador = '".$Operador."', 
	mecanico = '".$Mecanico."', 
	fecha_ini = '".$Fecha_Ini."', 
	fecha_fin = '".$Fecha_Fin."', 
	clasificacion = '".$Clasificacion."', 
	tipo = '".$Tipo."', 
	motivo = '".$Motivo."', 
	diagnostico = '".$Diagnostico."', 
	total = '".$Total."', 
	causa = '".$Causa."', 
	procedimiento = '".$Procedimiento."', 
	observaciones = '".$Observaciones."', ";
	$query .= isset($data1[0]["ClienteID"]) ? "proveedor1 = '".$data1[0]["ClienteID"]."', factura1 = '".$Factura1."', total1 = '".$data1[0]["Total"]."', ":"proveedor1 = '', total1 = '', ";
	$query .= isset($data2[0]["ClienteID"]) ? "proveedor2 = '".$data2[0]["ClienteID"]."', factura2 = '".$Factura2."', total2 = '".$data2[0]["Total"]."', ":"proveedor2 = '', total2 = '', ";
	$query .= isset($data3[0]["ClienteID"]) ? "proveedor3 = '".$data3[0]["ClienteID"]."', factura3 = '".$Factura3."', total3 = '".$data3[0]["Total"]."', ":"proveedor3 = '', total3 = '', ";
	$query .= "
	modificado_por = '".$_SESSION["UserCode"]."', 
	fecha_modificado = NOW() 
	WHERE ord_reparacion = '".$Ord_Reparacion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	Anular_CxPMovs($Ord_Reparacion, "Anulado Automaticamente por Maquinaria -> Modificar");
	
	//CxP_Movs
	if (isset($data1[0]["ClienteID"]))
	{
		$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, valor, saldo, 
		estado, digitado_por, fecha_digitado, grupo, subgrupo, subgrupo2) VALUES ('".$Ord_Reparacion."', '".$Ord_Reparacion."', 
		'".$Factura1."', '".$data1[0]["ClienteID"]."', 'Compra', '".$data1[0]["Total"]."', '".$data1[0]["Total"]."', 'Pendiente', 
		'".$_SESSION["UserCode"]."', NOW(), '".$Clasificacion."', '".$Motivo."', '".$Tipo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	}
	
	if (isset($data2[0]["ClienteID"]))
	{
		$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, valor, saldo, 
		estado, digitado_por, fecha_digitado, grupo, subgrupo, subgrupo2) VALUES ('".$Ord_Reparacion."', '".$Ord_Reparacion."', 
		'".$Factura2."', '".$data2[0]["ClienteID"]."', 'Compra', '".$data2[0]["Total"]."', '".$data2[0]["Total"]."', 'Pendiente', 
		'".$_SESSION["UserCode"]."', NOW(), '".$Clasificacion."', '".$Motivo."', '".$Tipo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
	}
	
	if (isset($data3[0]["ClienteID"]))
	{
		$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, valor, saldo, 
		estado, digitado_por, fecha_digitado, grupo, subgrupo, subgrupo2) VALUES ('".$Ord_Reparacion."', '".$Ord_Reparacion."', 
		'".$Factura3."', '".$data3[0]["ClienteID"]."', 'Compra', '".$data3[0]["Total"]."', '".$data3[0]["Total"]."', 'Pendiente', 
		'".$_SESSION["UserCode"]."', NOW(), '".$Clasificacion."', '".$Motivo."', '".$Tipo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
	}
	
	//---
	$query = "DELETE FROM maquinaria_partes WHERE ord_reparacion = '".$Ord_Reparacion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
	
	$num = count($data);
	for ($i = 0; $i < $num; $i++)
	{
		$Parte = $data[$i]["Parte"];
		$Problema = $data[$i]["Problema"];
		$Diagnostico = $data[$i]["Diagnostico"];
		//---
		$query = "INSERT INTO maquinaria_partes (ord_reparacion, parte, problema, diagnostico) 
		VALUES ('".$Ord_Reparacion."', '".$Parte."', '".$Problema."', '".$Diagnostico."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
	}
	
	//--------------------------------------------------------------------
	//--------------------------- PROVEEDOR 1 ----------------------------
	//--------------------------------------------------------------------
	
	$num = count($data1);
	//--- CHECK IF SOME ITEM HAS BEEN DELETED
	$query = "SELECT codigo FROM maquinaria_movs WHERE proveedor = '".$data1[0]["ClienteID"]."' AND ord_reparacion = '".$Ord_Reparacion."' ";
	$result = mysql_query($query) or die ("SQL Error 2-1: " . mysql_error());
	if (mysql_num_rows($result) > 0)
	{
		while ($rows = mysql_fetch_array($result))
		{
			$delete = true;
			for ($i = 0; $i < $num; $i++)
			{
				if ($rows["codigo"] == $data1[$i]["Codigo"])
				{
					$delete = false;
				}
			}
			if ($delete == true)
			{
				$query = "DELETE FROM maquinaria_movs WHERE codigo = '".$rows["codigo"]."' AND ord_reparacion = '".$Ord_Reparacion."' 
				AND proveedor = '".$data1[0]["ClienteID"]."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
			}
		}
	}
	//--- ADD DATA
	for ($i = 0; $i < $num; $i++)
	{
		$Codigo = $data1[$i]["Codigo"];
		$Cantidad = $data1[$i]["Cantidad"];
		$Unitario = $data1[$i]["Unitario"];
		if ($i == 0)
			$ClienteID = $data1[$i]["ClienteID"];
		//---
		$query = "SELECT codigo FROM maquinaria_movs WHERE codigo = '".$Codigo."' AND ord_reparacion = '".$Ord_Reparacion."' AND proveedor = '".$ClienteID."'";
		$result = mysql_query($query) or die ("SQL Error 3-1: " . mysql_error());
		if (mysql_num_rows($result) > 0)
		{
			$query = "UPDATE maquinaria_movs SET 
			cantidad = '".$Cantidad."', 
			unitario = '".$Unitario."', 
			proveedor = '".$ClienteID."' 
			WHERE codigo = '".$Codigo."' AND ord_reparacion = '".$Ord_Reparacion."' AND proveedor = '".$ClienteID."'";
			$result = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
		}
		else
		{
			$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor)
			VALUES ('".$Ord_Reparacion."', '".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$ClienteID."')";
			$result = mysql_query($query) or die("SQL Error 3-3: " . mysql_error());
		}
	}
	
	//--------------------------------------------------------------------
	//--------------------------- PROVEEDOR 2 ----------------------------
	//--------------------------------------------------------------------
	
	if (isset($data2[0]["ClienteID"]))
	{
		$num = count($data2);
		//--- CHECK IF SOME ITEM HAS BEEN DELETED
		$query = "SELECT codigo FROM maquinaria_movs WHERE proveedor = '".$data2[0]["ClienteID"]."' AND ord_reparacion = '".$Ord_Reparacion."' ";
		$result = mysql_query($query) or die ("SQL Error 4-1: " . mysql_error());
		if (mysql_num_rows($result) > 0)
		{
			while ($rows = mysql_fetch_array($result))
			{
				$delete = true;
				for ($i = 0; $i < $num; $i++)
				{
					if ($rows["codigo"] == $data2[$i]["Codigo"])
					{
						$delete = false;
					}
				}
				if ($delete == true)
				{
					$query = "DELETE FROM maquinaria_movs WHERE codigo = '".$rows["codigo"]."' AND ord_reparacion = '".$Ord_Reparacion."' 
					AND proveedor = '".$data2[0]["ClienteID"]."'";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-2: ".mysql_error():"");
				}
			}
		}
		//--- ADD DATA
		for ($i = 0; $i < $num; $i++)
		{
			$Codigo = $data2[$i]["Codigo"];
			$Cantidad = $data2[$i]["Cantidad"];
			$Unitario = $data2[$i]["Unitario"];
			if ($i == 0)
				$ClienteID = $data2[$i]["ClienteID"];
			//---
			$query = "SELECT codigo FROM maquinaria_movs WHERE codigo = '".$Codigo."' AND ord_reparacion = '".$Ord_Reparacion."' AND proveedor = '".$ClienteID."'";
			$result = mysql_query($query) or die ("SQL Error 5-1: " . mysql_error());
			if (mysql_num_rows($result) > 0)
			{
				$query = "UPDATE maquinaria_movs SET 
				cantidad = '".$Cantidad."', 
				unitario = '".$Unitario."', 
				proveedor = '".$ClienteID."' 
				WHERE codigo = '".$Codigo."' AND ord_reparacion = '".$Ord_Reparacion."' AND proveedor = '".$ClienteID."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
			}
			else
			{
				$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor)
				VALUES ('".$Ord_Reparacion."', '".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$ClienteID."')";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-3: ".mysql_error():"");
			}
		}
		
	}
	
	//--------------------------------------------------------------------
	//--------------------------- PROVEEDOR 3 ----------------------------
	//--------------------------------------------------------------------
	
	if (isset($data2[0]["ClienteID"]))
	{
		$num = count($data3);
		//--- CHECK IF SOME ITEM HAS BEEN DELETED
		$query = "SELECT codigo FROM maquinaria_movs WHERE proveedor = '".$data3[0]["ClienteID"]."' AND ord_reparacion = '".$Ord_Reparacion."' ";
		$result = mysql_query($query) or die ("SQL Error 6-1: " . mysql_error());
		if (mysql_num_rows($result) > 0)
		{
			while ($rows = mysql_fetch_array($result))
			{
				$delete = true;
				for ($i = 0; $i < $num; $i++)
				{
					if ($rows["codigo"] == $data3[$i]["Codigo"])
					{
						$delete = false;
					}
				}
				if ($delete == true)
				{
					$query = "DELETE FROM maquinaria_movs WHERE codigo = '".$rows["codigo"]."' AND ord_reparacion = '".$Ord_Reparacion."' 
					AND proveedor = '".$data3[0]["ClienteID"]."'";
					$result1 = mysql_query($query) or die("SQL Error 6-2: " . mysql_error());
				}
			}
		}
		//--- ADD DATA
		for ($i = 0; $i < $num; $i++)
		{
			$Codigo = $data3[$i]["Codigo"];
			$Cantidad = $data3[$i]["Cantidad"];
			$Unitario = $data3[$i]["Unitario"];
			if ($i == 0)
				$ClienteID = $data3[$i]["ClienteID"];
			//---
			$query = "SELECT codigo FROM maquinaria_movs WHERE codigo = '".$Codigo."' AND ord_reparacion = '".$Ord_Reparacion."' AND proveedor = '".$ClienteID."'";
			$result = mysql_query($query) or die ("SQL Error 7-1: " . mysql_error());
			if (mysql_num_rows($result) > 0)
			{
				$query = "UPDATE maquinaria_movs SET 
				cantidad = '".$Cantidad."', 
				unitario = '".$Unitario."', 
				proveedor = '".$ClienteID."' 
				WHERE codigo = '".$Codigo."' AND ord_reparacion = '".$Ord_Reparacion."' AND proveedor = '".$ClienteID."'";
				$result = mysql_query($query) or die("SQL Error 7-2: " . mysql_error());
			}
			else
			{
				$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor)
				VALUES ('".$Ord_Reparacion."', '".$Codigo."', '".$Cantidad."', '".$Unitario."', '".$ClienteID."')";
				$result = mysql_query($query) or die("SQL Error 7-3: " . mysql_error());
			}
		}
	}
}
else if (isset($_GET['Maquinaria_Aprobar']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Creado";
	$Fecha = isset($_GET['Fecha']) ? $_GET['Fecha']:"0000-00-00";
	$Ord_Reparacion = isset($_GET['Ord_Reparacion']) ? $_GET['Ord_Reparacion']:0;
	
	$query = "UPDATE maquinaria_final SET 
	estado = '".$Estado."', 
	fecha_ini = '".$Fecha."', 
	fecha_modificado = NOW()";
	$query .= ($Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."' ":"";
	$query .= ($Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."' ":"";
	$query .= " WHERE ord_reparacion = '".$Ord_Reparacion."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		if ($Estado == "Anulado")
		{
			// Buscar Movimientos...
			// Posiblemente innecesario?
			$query = "SELECT compra_entrada FROM cxp_movs WHERE compra_interno = '".$Ord_Reparacion."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				Anular_CxPMovs($Ord_Reparacion, "Anulado Automaticamente por Maquinaria -> Aprobar");
			}
		}
		
		if ($Estado == "Aprobado")
		{
			$query = "UPDATE cxp_movs SET 
			estado = 'Aprobado', 
			aprobado_por = '".$_SESSION["UserCode"]."', 
			fecha_aprobado = NOW() 
			WHERE compra_interno = '".$Ord_Reparacion."' AND tipo_movimiento = 'Compra'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		}
	}
}
else if (isset($_GET['ActualizarEstado']))
{
	$query = "UPDATE clientes SET 
	estado_cuenta = '".$_GET['ActualizarEstado']."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	ultima_actualizacion = NOW() 
	WHERE cliente_id = '".$_GET['ClienteID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET['ActualizarCupo']))
{
	$query = "UPDATE clientes SET 
	credito = '".$_GET['Credito']."', 
	vigencia_credito = '".$_GET['Vigencia_Credito']."', 
	adicional = '".$_GET['Adicional']."', 
	vigencia_adicional = '".$_GET['Vigencia_Adicional']."', 
	credito_activo =  '".$_GET['Activo']."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	ultima_actualizacion = NOW() 
	WHERE cliente_id = '".$_GET['ClienteID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET['Actualizar_Garantias']))
{
	$query = "SELECT * FROM clientes_garant WHERE garantia = '".$_GET['Garantia']."' 
	AND cliente_id = '".$_GET['Actualizar_Garantias']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$query = "UPDATE clientes_garant SET ok = '".$_GET['Apply']."' WHERE garantia = '".$_GET['Garantia']."' 
		AND cliente_id = '".$_GET['Actualizar_Garantias']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	}
	//else
	//{
	//	$query = "INSERT INTO clientes_garant (cliente_id, garantia, ok) VALUES 
	//	('".$_GET['Actualizar_Garantias']."', '".$_GET['Garantia']."', '".$_GET['Apply']."')";
	//	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	//}
}
else if (isset($_FILES["fileToUpload"]))//Unused
{
	$target_dir = "../images/tmp/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			//echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			//echo "File is not an image.";
			$uploadOk = 0;
		}
	}
	// Check if file already exists
	//if (file_exists($target_file)) {
	//	echo "Sorry, file already exists.";
	//	$uploadOk = 0;
	//}
	// Check file size						//1mb
	if ($_FILES["fileToUpload"]["size"] > 1024000) {
		echo "[ERROR] Solo se permiten Archivos Menor a 1MB.";
		$uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
		echo "[ERROR] Solo se Permiten Archivos tipo: \"JPG\", \"JPEG\" y \"PNG\" ";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "[ERROR] El archivo no se ha procesado correctamente.<br/>Intente nuevamente luego de unos segundos...";
	// if everything is ok, try to upload file
	}
	else
	{
		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			//echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			echo "OK";
		}
		else {
			echo "[ERROR] Ah ocurrido un error mientras se intentaba cargar el archivo.<br/>Intente nuevamente luego de unos segundos...";
		}
	}
}
else if (isset($_FILES["File_Uploads"]))
{
	$target_dir = realpath("../uploads_tmp/")."\ ";
	$target_dir = str_replace(" ", "", $target_dir);
	$target_file = $target_dir . basename($_FILES["File_Uploads"]["name"]);
	$uploadOk = 0;
	$FileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if file already exists
	if (file_exists($target_file))
	{
		if ($WINDOWS)
		{
			if (chmod($target_file,0777)) //Insert an Invalid UserId to set to Nobody Owern; 0777 is my standard for "Nobody"
			{
				if (!unlink($target_file))
					$uploadOk = 1;
			}
			else
				$uploadOk = 1;
		}
		else
		{
			//Linux
			if (!unlink($target_file))
				$uploadOk = 1;
		}
	}
	
	// Check file size						//1mb
	if ($_FILES["File_Uploads"]["size"] > 1024000) {
		echo "[ERROR] Solo se permiten Archivos Menor a 1MB.<br />";
		$uploadOk = 1;
	}
	// Allow certain file formats
	if($FileType != "xlsx" && $FileType != "rar" && $FileType != "zip")
	{
		echo "[ERROR] Solo se Permiten Archivos tipo: \".rar, .zip, .xlsx\"<br />";
		$uploadOk = 1;
	}
	
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk > 0) {
		echo "[ERROR] El archivo no se ha procesado correctamente.<br/>Intente nuevamente luego de unos segundos...";
	// if everything is ok, try to upload file
	}
	else
	{
		if (move_uploaded_file($_FILES["File_Uploads"]["tmp_name"], $target_file)) {
			//echo "The file ". basename( $_FILES["File_Uploads"]["name"]). " has been uploaded.";
			echo "OK";
		}
		else {
			echo "[ERROR] Ah ocurrido un error mientras se intentaba cargar el archivo.<br/>Intente nuevamente luego de unos segundos...";
		}
	}
}
else if (isset($_FILES["Image_Uploads"]))
{
	$target_dir = realpath("../Uploads_Tmp/")."\ ";
	$target_dir = str_replace(" ", "", $target_dir);
	$target_file = $target_dir . basename($_FILES["Image_Uploads"]["name"]);
	$uploadOk = 0;
	$FileType = pathinfo($target_file, PATHINFO_EXTENSION);
	$FileType = strtolower($FileType);
	// Check if file already exists
	if (file_exists($target_file))
	{
		if ($WINDOWS)
		{
			if (chmod($target_file,0777)) //Insert an Invalid UserId to set to Nobody Owern; 0777 is my standard for "Nobody"
			{
				if (!unlink($target_file))
					$uploadOk = 1;
			}
			else
				$uploadOk = 1;
		}
		else
		{
			//Linux
			if (!unlink($target_file))
				$uploadOk = 1;
		}
	}
	
	// Check file size						//1mb
	if ($_FILES["Image_Uploads"]["size"] > 1024000) {
		echo "[ERROR] Solo se permiten Archivos Menor a 1MB.<br />";
		$uploadOk = 1;
	}
	// Allow certain file formats
	if($FileType != "jpg" && $FileType != "png" && $FileType != "bmp")
	{
		echo "[ERROR] Solo se Permiten Archivos tipo: \".jpg, .png, .bmp\"<br />";
		$uploadOk = 1;
	}
	
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk > 0) {
		echo "[ERROR] El archivo no se ha procesado correctamente.<br/>Intente nuevamente luego de unos segundos...";
	// if everything is ok, try to upload file
	}
	else
	{
		if (move_uploaded_file($_FILES["Image_Uploads"]["tmp_name"], $target_file)) {
			//echo "The file ". basename( $_FILES["Image_Uploads"]["name"]). " has been uploaded.";
			echo "OK";
		}
		else {
			echo "[ERROR] Ah ocurrido un error mientras se intentaba cargar el archivo.<br/>Intente nuevamente luego de unos segundos...";
		}
	}
}
else if (isset($_POST["Presupuesto_Crear"]))
{
	$data = $_POST["Presupuesto_Crear"];
	
	$Interno = $data[0]["Interno"];
	$Proyecto = $data[0]["Proyecto"];
	$Fecha = $data[0]["Fecha"];
	$ClienteID = $data[0]["ClienteID"];
	$SubTotal = $data[0]["SubTotal"];
	$Administracion1 = $data[0]["Administracion1"];
	$Administracion2 = $data[0]["Administracion2"];
	$Imprevistos1 = $data[0]["Imprevistos1"];
	$Imprevistos2 = $data[0]["Imprevistos2"];
	$Utilidades1 = $data[0]["Utilidades1"];
	$Utilidades2 = $data[0]["Utilidades2"];
	$Iva1 = $data[0]["Iva1"];
	$Iva1_Check = $data[0]["Iva1_Check"];
	$Iva2 = $data[0]["Iva2"];
	$Iva2_Check = $data[0]["Iva2_Check"];
	$Total = $data[0]["Total"];
	$Notas = $data[0]["Notas"];
	
	if (isset($data[0]["Actualizar"]) && $data[0]["Actualizar"] == "true")
	{
		$query = "SELECT * FROM presupuesto WHERE interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$query = "UPDATE presupuesto SET 
			proyecto = '".$Proyecto."', 
			fecha = '".$Fecha."', 
			cliente_id = '".$ClienteID."', 
			subtotal = '".$SubTotal."', 
			administracion1 = '".$Administracion1."', 
			administracion2 = '".$Administracion2."', 
			imprevistos1 = '".$Imprevistos1."', 
			imprevistos2 = '".$Imprevistos2."', 
			utilidades1 = '".$Utilidades1."', 
			utilidades2 = '".$Utilidades2."', 
			iva1 = '".$Iva1."', 
			iva1_check = '".$Iva1_Check."', 
			iva2 = '".$Iva2."', 
			iva2_check = '".$Iva2_Check."', 
			total = '".$Total."', 
			notas = '".$Notas."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW() 
			WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			
			$query = "DELETE FROM presupuesto_movs WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR",
			);
			echo json_encode($ReturnData);
			die();
		}
	}
	else
	{
		$query = "SELECT id FROM presupuesto ORDER BY id DESC LIMIT 1";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		$row = mysql_fetch_array($result);
		$id = $row['id'] + 1;
		$len = strlen($id);
		$zero = "";
		if ($len < 6)
		{
			$num = 6 - $len;
			for ($a = 0; $a < $num; $a++)
				$zero .= "0";
			$Interno = "PRE".$zero."".$id."";
		}
		else
			$Interno = "PRE".$id."";
		
		$query = "INSERT INTO presupuesto (interno, proyecto, fecha, cliente_id, subtotal, administracion1, administracion2, 
		imprevistos1, imprevistos2, utilidades1, utilidades2, iva1, iva1_check, iva2, iva2_check, total, notas, digitado_por, 
		fecha_digitado) VALUES ('".$Interno."', '".$Proyecto."', '".$Fecha."', '".$ClienteID."', '".$SubTotal."', '".$Administracion1."', 
		'".$Administracion2."', '".$Imprevistos1."', '".$Imprevistos2."', '".$Utilidades1."', '".$Utilidades2."', '".$Iva1."', 
		'".$Iva1_Check."', '".$Iva2."', '".$Iva2_Check."', '".$Total."', '".$Notas."', '".$_SESSION["UserCode"]."', NOW())";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
	}
	
	$Items = $data[0]["Items"];
	foreach($Items as $item)
	{
		$Item = $item["Item"];
		$Codigo = $item["Codigo"];
		$Nombre = $item["Nombre"];
		$Cantidad = $item["Cantidad"];
		$Valor = $item["Valor"];
		$Class = $item["Clasificacion"];
		
		$query = "INSERT INTO presupuesto_movs (interno, item, codigo, nombre, cantidad, valor, clasificacion) 
		VALUES ('".$Interno."', '".$Item."', '".$Codigo."', '".$Nombre."', '".$Cantidad."', '".$Valor."', '".$Class."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_Borrar"]))
{
	$Interno = isset($_POST["Interno"]) ? $_POST["Interno"]:die();
	
	$query = "DELETE FROM presupuesto_movs WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$query = "DELETE FROM presupuesto WHERE interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_affected_rows() > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "OK",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR",
			);
		}
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
	}
	
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_APU_Crear"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$Nombre = $_POST["Nombre"];
	$Valor = $_POST["Valor"];
	
	$query = "SELECT * FROM `apu_".$Clasificacion."` WHERE grupo = '90' AND subgrupo = '01' ORDER BY codigo2 DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$Codigo = $row["codigo2"] + 1;
		if ($Codigo < 9)
			$Codigo = "0".$Codigo;
		$Codigo2 = "90.01.".$Codigo;
	}
	else
	{
		$Codigo = "01";
		$Codigo2 = "90.01.01";
	}
	
	$query = "INSERT INTO `apu_".$Clasificacion."` (codigo, codigo2, nombre, categoria, grupo, subgrupo, unidad, 
	valor, digitado_por, fecha_digitado) VALUES ('".$Codigo2."', '".$Codigo."', '".$Nombre."','APU', '90', '01', 'und', 
	'".$Valor."', '".$_SESSION["UserCode"]."', NOW())";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	
	$query = "SELECT * FROM `apu_mat_".$Clasificacion."` WHERE grupo = '90' AND subgrupo = '01' ORDER BY codigo2 DESC LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$ObjetoCodigo = $row["codigo2"] + 1;
		if ($ObjetoCodigo < 9)
			$ObjetoCodigo = "0".$ObjetoCodigo;
		$ObjetoCodigo2 = "90.01.".$ObjetoCodigo;
	}
	else
	{
		$ObjetoCodigo = "01";
		$ObjetoCodigo2 = "90.01.01";
	}
	
	$query = "INSERT INTO `apu_mat_".$Clasificacion."` (codigo, codigo2, nombre, categoria, grupo, subgrupo, unidad, 
	peso, valor, uso, digitado_por, fecha_digitado) VALUES ('".$ObjetoCodigo2."', '".$ObjetoCodigo."', '".$Nombre."', 
	'Materiales', '90', '01', 'und', '1', '".$Valor."', '100', '".$_SESSION["UserCode"]."', NOW())";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
	
	$query = "INSERT INTO `apu_".$Clasificacion."_movs` (codigo, objeto_codigo, cantidad, uso, valor, total, tipo, clasificacion) 
	VALUES ('".$Codigo2."', '".$ObjetoCodigo2."', '1', '100', '".$Valor."', '".$Valor."', 'Materiales', '".$Clasificacion."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
		"Codigo" => $Codigo2,
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["APU_Crear"]))
{
	$data = $_POST["APU_Crear"];
	
	if (!isset($data[0]["Clasificacion"]))
		die();
	
	$Clasificacion = $data[0]["Clasificacion"];
	$Codigo = $data[0]["Codigo"];
	$Codigo2 = $data[0]["Codigo2"];
	$Nombre = $data[0]["Nombre"];
	$Grupo = $data[0]["Grupo"];
	$SubGrupo = $data[0]["SubGrupo"];
	$Unidad = $data[0]["Unidad"];
	$Desperdicios = $data[0]["Desperdicios"];
	$Gastos = $data[0]["Gastos"];
	$Valor = $data[0]["Valor"];
	
	if (isset($data[0]["ID"]))
	{
		$ID = $data[0]["ID"];
		
		$query = "SELECT codigo FROM `apu_".$Clasificacion."` WHERE id = '".$ID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$query = "DELETE FROM `apu_".$Clasificacion."_movs` WHERE codigo = '".$row["codigo"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			
			$query = "UPDATE `apu_".$Clasificacion."` SET 
			codigo = '".$Codigo."', 
			codigo2 = '".$Codigo2."', 
			nombre = '".$Nombre."', 
			grupo = '".$Grupo."', 
			subgrupo = '".$SubGrupo."', 
			unidad = '".$Unidad."', 
			valor = '".$Valor."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW() 
			WHERE id = '".$ID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
			// Ya hay Trigger
			/*if ($row["codigo"] != $Codigo)
			{
				$query = "UPDATE presupuesto_movs SET codigo = '".$Codigo."' 
				WHERE codigo = '".$row["codigo"]."' AND clasificacion = '".$Clasificacion."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
			}*/
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR",
			);
			echo json_encode($ReturnData);
			die();
		}
	}
	else
	{
		$query = "SELECT * FROM `apu_".$Clasificacion."` WHERE codigo = '".$Codigo."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "EXIST",
			);
			echo json_encode($ReturnData);
			die();
		}
		else
		{
			$query = "INSERT INTO `apu_".$Clasificacion."` (codigo, codigo2, nombre, categoria, grupo, subgrupo, unidad, 
			desperdicios, gastos, valor, digitado_por, fecha_digitado) VALUES ('".$Codigo."', '".$Codigo2."', '".$Nombre."', 
			'APU', '".$Grupo."', '".$SubGrupo."', '".$Unidad."', '".$Desperdicios."', '".$Gastos."', '".$Valor."', 
			'".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
		}
	}
	
	$Items = $data[0]["Items"];
	foreach($Items as $item)
	{
		$ObjetoCodigo = $item["Codigo"];
		$Cantidad = $item["Cantidad"];
		$Uso = $item["Uso"];
		$Valor = $item["Valor"];
		$Total = $item["Total"];
		$Tipo = $item["Tipo"];
		$Class = $item["Clasificacion"];
		
		$query = "INSERT INTO `apu_".$Clasificacion."_movs` (codigo, objeto_codigo, cantidad, uso, valor, total, tipo, clasificacion) 
		VALUES ('".$Codigo."', '".$ObjetoCodigo."', '".$Cantidad."', '".$Uso."', '".$Valor."', '".$Total."', '".$Tipo."', '".$Class."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_Listado_APU_Actualizar"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Nombre = isset($_POST["Nombre"]) ? $_POST["Nombre"]:die();
	$Unidad = isset($_POST["Unidad"]) ? $_POST["Unidad"]:die();
	$Desperdicios = isset($_POST["Desperdicios"]) ? $_POST["Desperdicios"]:die();
	$Gastos = isset($_POST["Gastos"]) ? $_POST["Gastos"]:die();
	$Valor = isset($_POST["Valor"]) ? $_POST["Valor"]:die();
	$Notas = isset($_POST["Notas"]) ? $_POST["Notas"]:die();
	
	$query = "UPDATE `apu_".$Clasificacion."` SET 
	nombre = '".$Nombre."', 
	unidad = '".$Unidad."', 
	desperdicios = '".$Desperdicios."', 
	gastos = '".$Gastos."', 
	valor = '".$Valor."', 
	notas = '".$Notas."' 
	WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_Listado_APU_Borrar"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Codigo = isset($_POST["Codigo"]) ? $_POST["Codigo"]:die();
	
	$query = "SELECT * FROM presupuesto_movs WHERE codigo = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "USED",
		);
	}
	else
	{
		$query = "DELETE FROM `apu_".$Clasificacion."` WHERE id = '".$ID."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_affected_rows() > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "OK",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR",
			);
		}
	}
	
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_APU_Agregar"]))
{
	if (!isset($_POST["APU_Clasificacion"]))
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	$Clasificacion = $_POST["APU_Clasificacion"];
	$Codigo = $_POST["APU_Codigo"];
	$ObjetoClasificacion = $_POST["Clasificacion"];;
	$ObjetoCodigo = $_POST["Codigo"];;
	$Cantidad = $_POST["Cantidad"];
	$Uso = $_POST["Uso"];
	$Valor = $_POST["Valor"];
	$Total = $Valor * $Cantidad;
	$Tipo = $_POST["Tipo"];
	$ValorTotal = 0;
	
	$query = "INSERT INTO `apu_".$Clasificacion."_movs` (codigo, objeto_codigo, cantidad, uso, valor, total, tipo, clasificacion) 
	VALUES ('".$Codigo."', '".$ObjetoCodigo."', '".$Cantidad."', '".$Uso."', '".$Valor."', '".$Total."', '".$Tipo."', '".$ObjetoClasificacion."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT * FROM `apu_".$Clasificacion."_movs` WHERE codigo = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ValorTotal += $row["total"];
		}
	}
	
	$query = "UPDATE `apu_".$Clasificacion."` SET 
	valor = '".$ValorTotal."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	fecha_modificado = NOW() 
	WHERE codigo = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_APU_Modificar"]))
{
	if (!isset($_POST["APU_Clasificacion"]))
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	$Clasificacion = $_POST["APU_Clasificacion"];
	$Codigo = $_POST["APU_Codigo"];
	$ObjetoClasificacion = $_POST["Clasificacion"];;
	$ObjetoCodigo = $_POST["Codigo"];;
	$Cantidad = $_POST["Cantidad"];
	$Uso = $_POST["Uso"];
	$Valor = $_POST["Valor"];
	$Total = $Valor * $Cantidad;
	$ValorTotal = 0;
	
	$query = "UPDATE `apu_".$Clasificacion."_movs` SET 
	cantidad = '".$Cantidad."', 
	uso = '".$Uso."', 
	valor = '".$Valor."', 
	total = '".$Total."' 
	WHERE codigo = '".$Codigo."' AND objeto_codigo = '".$ObjetoCodigo."' AND clasificacion = '".$ObjetoClasificacion."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	// No necesario... ya hay trigger
	/*
	$query = "SELECT * FROM `apu_".$Clasificacion."_movs` WHERE codigo = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ValorTotal += $row["total"];
		}
	}
	
	$query = "UPDATE `apu_".$Clasificacion."` SET 
	valor = '".$ValorTotal."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	fecha_modificado = NOW() 
	WHERE codigo = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	*/
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_APU_Borrar"]))
{
	if (!isset($_POST["APU_Clasificacion"]))
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	$Clasificacion = $_POST["APU_Clasificacion"];
	$Codigo = $_POST["APU_Codigo"];
	$ObjetoClasificacion = $_POST["Clasificacion"];;
	$ObjetoCodigo = $_POST["Codigo"];;
	$ValorTotal = 0;
	
	$query = "DELETE FROM `apu_".$Clasificacion."_movs` WHERE codigo = '".$Codigo."' 
	AND objeto_codigo = '".$ObjetoCodigo."' AND clasificacion = '".$ObjetoClasificacion."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT * FROM `apu_".$Clasificacion."_movs` WHERE codigo = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ValorTotal += $row["total"];
		}
	}
	
	$query = "UPDATE `apu_".$Clasificacion."` SET 
	valor = '".$ValorTotal."', 
	modificado_por = '".$_SESSION["UserCode"]."', 
	fecha_modificado = NOW() 
	WHERE codigo = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Materiales_Crear"]))
{
	$Today = date('Y-m-d');
	$data = $_POST["Materiales_Crear"];
	
	if (!isset($data[0]["Clasificacion"]))
		die();
	
	$Clasificacion = $data[0]["Clasificacion"];
	$Codigo = $data[0]["Codigo"];
	$Codigo2 = $data[0]["Codigo2"];
	$Nombre = $data[0]["Nombre"];
	$Valor = $data[0]["Valor"];
	$Valor_KM = $data[0]["Valor_KM"];
	$Uso = $data[0]["Uso"];
	$Grupo = $data[0]["Grupo"];
	$SubGrupo = $data[0]["SubGrupo"];
	$Unidad = $data[0]["Unidad"];
	$Peso = $data[0]["Peso"];
	$Notas = $data[0]["Notas"];
	$Imagen = $data[0]["Imagen"];
	
	if (isset($data[0]["ID"]))
	{
		$ID = $data[0]["ID"];
		
		$query = "SELECT codigo, imagen FROM `apu_mat_".$Clasificacion."` WHERE id = '".$ID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$query = "DELETE FROM `apu_mat_".$Clasificacion."_prov` WHERE codigo = '".$row["codigo"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			
			if ($Imagen != "")
			{
				if ($Imagen != $row["imagen"])
				{
					//-- Delete Old File... if there is one
					if ($row["imagen"] != "")
					{
						$TargetFile = "../images/".$row["imagen"];
						if ($WINDOWS)
						{
							if (chmod($TargetFile,0777))
							{
								if (!unlink($TargetFile))
									die();
							}
						}
						else
						{
							//Linux
							if (!unlink($TargetFile))
								die();
						}
					}
					
					//-- Rename Image
					$FilePath = pathinfo($Imagen);
					$EXT = $FilePath['extension'];
					$NewImage = "materiales/".$Codigo."_".$Today.".".$EXT."";
					rename("../".$Imagen."","../images/".$NewImage."");
					$Imagen = $NewImage;
				}
			}
			
			$query = "UPDATE `apu_mat_".$Clasificacion."` SET  
			codigo = '".$Codigo."', 
			codigo2 = '".$Codigo2."', 
			nombre = '".$Nombre."', 
			grupo = '".$Grupo."', 
			subgrupo = '".$SubGrupo."', 
			unidad = '".$Unidad."', 
			peso = '".$Peso."', 
			valor = '".$Valor."', 
			valor_km = '".$Valor_KM."', 
			uso = '".$Uso."', 
			notas = '".$Notas."',  
			imagen = '".$Imagen."',  
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW() 
			WHERE id = '".$ID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			// Ya hay Trigger
			/*if ($row["codigo"] != $Codigo)
			{
				$query = "UPDATE `apu_".$Clasificacion."_movs` SET objeto_codigo = '".$Codigo."' 
				WHERE objeto_codigo = '".$row["codigo"]."' AND clasificacion = '".$Clasificacion."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
			}*/
		}
	}
	else
	{
		$query = "SELECT * FROM `apu_mat_".$Clasificacion."` WHERE codigo = '".$Codigo."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "EXIST",
			);
			echo json_encode($ReturnData);
			die();
		}
		else
		{
			if ($Imagen != "")
			{
				//-- Rename Image
				$FilePath = pathinfo($Imagen);
				$EXT = $FilePath["extension"];
				$NewImage = "materiales/".$Codigo.".".$EXT."";
				rename("../".$Imagen."","../images/".$NewImage."");
				$Imagen = $NewImage;
			}
				
			$query = "INSERT INTO `apu_mat_".$Clasificacion."` (codigo, codigo2, nombre, categoria, grupo, subgrupo, unidad, peso, valor, 
			valor_km, uso, notas, imagen, digitado_por, fecha_digitado) VALUES ('".$Codigo."', '".$Codigo2."', '".$Nombre."', 'Materiales', 
			'".$Grupo."', '".$SubGrupo."', '".$Unidad."', '".$Peso."', '".$Valor."', '".$Valor_KM."', '".$Uso."', '".$Notas."', '".$Imagen."', 
			'".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
		}
	}
	
	$Proveedores = isset($data[0]["Proveedores"]) ? $data[0]["Proveedores"]:array();
	foreach($Proveedores as $item)
	{
		$Proveedor = $item["Proveedor"];
		$ProveedorID = $item["ProveedorID"];
		$ProveedorCodigo = $item["Codigo"];
		
		$query = "INSERT INTO `apu_mat_".$Clasificacion."_prov` (codigo, proveedor_id, proveedor_codigo) 
		VALUES ('".$Codigo."', '".$ProveedorID."', '".$ProveedorCodigo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_Materiales_Actualizar"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Nombre = isset($_POST["Nombre"]) ? $_POST["Nombre"]:die();
	$Unidad = isset($_POST["Unidad"]) ? $_POST["Unidad"]:die();
	$Peso = isset($_POST["Peso"]) ? $_POST["Peso"]:die();
	$Uso = isset($_POST["Uso"]) ? $_POST["Uso"]:die();
	$Valor = isset($_POST["Valor"]) ? $_POST["Valor"]:die();
	$Valor_KM = isset($_POST["Valor_KM"]) ? $_POST["Valor_KM"]:die();
	$Notas = isset($_POST["Notas"]) ? $_POST["Notas"]:die();
	
	$query = "UPDATE `apu_mat_".$Clasificacion."` SET 
	nombre = '".$Nombre."', 
	unidad = '".$Unidad."', 
	peso = '".$Peso."', 
	uso = '".$Uso."', 
	valor = '".$Valor."', 
	valor_km = '".$Valor_KM."', 
	notas = '".$Notas."' 
	WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_Materiales_Borrar"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Codigo = isset($_POST["Codigo"]) ? $_POST["Codigo"]:die();
	
	$query = "SELECT * FROM apu_clasificacion";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$query = "SELECT * FROM `apu_".$row["clasificacion"]."_movs` WHERE objeto_codigo = '".$Codigo."' 
			AND tipo = 'Materiales' AND clasificacion = '".$Clasificacion."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$ReturnData[0] = array(
					"MESSAGE" => "USED",
				);
				echo json_encode($ReturnData);
				die();
			}
		}
		
		$query = "DELETE FROM `apu_mat_".$Clasificacion."` WHERE id = '".$ID."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_affected_rows() > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "OK",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR",
			);
		}
		echo json_encode($ReturnData);
	}
}
else if (isset($_POST["ManodeObra_Crear"]))
{
	$data = $_POST["ManodeObra_Crear"];
	
	if (!isset($data[0]["Clasificacion"]))
		die();
	
	$Clasificacion = $data[0]["Clasificacion"];
	$Codigo = $data[0]["Codigo"];
	$Codigo2 = $data[0]["Codigo2"];
	$Nombre = $data[0]["Nombre"];
	$Grupo = $data[0]["Grupo"];
	$SubGrupo = $data[0]["SubGrupo"];
	$Unidad = $data[0]["Unidad"];
	$Uso = $data[0]["Uso"];
	$Valor_SC = $data[0]["Valor_SC"];
	$Valor_PD = $data[0]["Valor_PD"];
	$Valor_SCC = $data[0]["Valor_SCC"];
	
	if (isset($data[0]["ID"]))
	{
		$ID = $data[0]["ID"];
		
		$query = "SELECT codigo FROM `apu_mo_".$Clasificacion."` WHERE id = '".$ID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$query = "DELETE FROM `apu_mo_".$Clasificacion."_movs` WHERE codigo = '".$row["codigo"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			
			$query = "UPDATE `apu_mo_".$Clasificacion."` SET  
			codigo = '".$Codigo."', 
			codigo2 = '".$Codigo2."', 
			nombre = '".$Nombre."', 
			grupo = '".$Grupo."', 
			subgrupo = '".$SubGrupo."', 
			unidad = '".$Unidad."', 
			uso = '".$Uso."', 
			valor_sc = '".$Valor_SC."', 
			valor_pd = '".$Valor_PD."',  
			valor_scc = '".$Valor_SCC."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW() 
			WHERE id = '".$ID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
			// Ya hay Trigger
			/*if ($row["codigo"] != $Codigo)
			{
				$query = "UPDATE `apu_".$Clasificacion."_movs` SET objeto_codigo = '".$Codigo."' 
				WHERE objeto_codigo = '".$row["codigo"]."' AND clasificacion = '".$Clasificacion."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
			}*/
		}
	}
	else
	{
		$query = "SELECT * FROM `apu_mo_".$Clasificacion."` WHERE codigo = '".$Codigo."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "EXIST",
			);
			echo json_encode($ReturnData);
			die();
		}
		else
		{
			$query = "INSERT INTO `apu_mo_".$Clasificacion."` (codigo, codigo2, nombre, categoria, grupo, subgrupo, unidad, uso, valor_sc, 
			valor_pd, valor_scc, digitado_por, fecha_digitado) VALUES ('".$Codigo."', '".$Codigo2."', '".$Nombre."', 'Mano de Obra', '".$Grupo."', 
			'".$SubGrupo."', '".$Unidad."', '".$Uso."', '".$Valor_SC."', '".$Valor_PD."', '".$Valor_SCC."', '".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
		}
	}
	
	$Convencional = $data[0]["Convencional"];
	foreach($Convencional as $item)
	{
		$Concepto = $item["Concepto"];
		$Valor = $item["Valor"];
		$Uso = $item["Uso"];
		$Total = $item["Total"];
		$Fijo = $item["Fijo"];
		if ($Fijo == "")
			$Fijo = "false";
		
		$query = "INSERT INTO `apu_mo_".$Clasificacion."_movs` (codigo, concepto, uso, valor, total, tipo, fijo) VALUES 
		('".$Codigo."', '".$Concepto."', '".$Uso."', '".$Valor."', '".$Total."', 'SC', '".$Fijo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
	}
	
	$Trabajador = $data[0]["Trabajador"];
	foreach($Trabajador as $item)
	{
		$Concepto = $item["Concepto"];
		$Valor = $item["Valor"];
		$Uso = $item["Uso"];
		$Total = $item["Total"];
		$Fijo = $item["Fijo"];
		if ($Fijo == "")
			$Fijo = "false";
		
		$query = "INSERT INTO `apu_mo_".$Clasificacion."_movs` (codigo, concepto, uso, valor, total, tipo, fijo) VALUES 
		('".$Codigo."', '".$Concepto."', '".$Uso."', '".$Valor."', '".$Total."', 'PD', '".$Fijo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
	}
	
	$Contratista = $data[0]["Contratista"];
	foreach($Contratista as $item)
	{
		$Concepto = $item["Concepto"];
		$Valor = $item["Valor"];
		$Uso = $item["Uso"];
		$Total = $item["Total"];
		$Fijo = $item["Fijo"];
		if ($Fijo == "")
			$Fijo = "false";
		
		$query = "INSERT INTO `apu_mo_".$Clasificacion."_movs` (codigo, concepto, uso, valor, total, tipo, fijo) VALUES 
		('".$Codigo."', '".$Concepto."', '".$Uso."', '".$Valor."', '".$Total."', 'SCC', '".$Fijo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_ManodeObra_Actualizar"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Nombre = isset($_POST["Nombre"]) ? $_POST["Nombre"]:die();
	$Unidad = isset($_POST["Unidad"]) ? $_POST["Unidad"]:die();
	$Uso = isset($_POST["Uso"]) ? $_POST["Uso"]:die();
	$Valor = isset($_POST["Valor"]) ? $_POST["Valor"]:die();
	$Notas = isset($_POST["Notas"]) ? $_POST["Notas"]:die();
	
	$query = "UPDATE `apu_mo_".$Clasificacion."` SET 
	nombre = '".$Nombre."', 
	unidad = '".$Unidad."', 
	uso = '".$Uso."', 
	valor_scc = '".$Valor."', 
	notas = '".$Notas."' 
	WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_ManodeObra_Borrar"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Codigo = isset($_POST["Codigo"]) ? $_POST["Codigo"]:die();
	
	$query = "SELECT * FROM apu_clasificacion";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$query = "SELECT * FROM `apu_".$row["clasificacion"]."_movs` WHERE objeto_codigo = '".$Codigo."' 
			AND tipo = 'Mano de Obra' AND clasificacion = '".$Clasificacion."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$ReturnData[0] = array(
					"MESSAGE" => "USED",
				);
				echo json_encode($ReturnData);
				die();
			}
		}
		
		$query = "DELETE FROM `apu_mo_".$Clasificacion."` WHERE id = '".$ID."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_affected_rows() > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "OK",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR",
			);
		}
		echo json_encode($ReturnData);
	}
}
else if (isset($_POST["Equipos_Crear"]))
{
	$Today = date('Y-m-d');
	$data = $_POST["Equipos_Crear"];
	
	if (!isset($data[0]["Clasificacion"]))
		die();
	
	$Clasificacion = $data[0]["Clasificacion"];
	$Codigo = $data[0]["Codigo"];
	$Codigo2 = $data[0]["Codigo2"];
	$Nombre = $data[0]["Nombre"];
	$Valor = $data[0]["Valor"];
	$Uso = $data[0]["Uso"];
	$Grupo = $data[0]["Grupo"];
	$SubGrupo = $data[0]["SubGrupo"];
	$Unidad = $data[0]["Unidad"];
	$Notas = $data[0]["Notas"];
	$Imagen = $data[0]["Imagen"];
	
	if (isset($data[0]["ID"]))
	{
		$ID = $data[0]["ID"];
		
		$query = "SELECT codigo, imagen FROM `apu_equ_".$Clasificacion."` WHERE id = '".$ID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$query = "DELETE FROM `apu_equ_".$Clasificacion."_prov` WHERE codigo = '".$row["codigo"]."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
			
			if ($Imagen != "")
			{
				if ($Imagen != $row["imagen"])
				{
					//-- Delete Old File... if there is one
					if ($row["imagen"] != "")
					{
						$TargetFile = "../images/".$row["imagen"];
						if ($WINDOWS)
						{
							if (chmod($TargetFile,0777))
							{
								if (!unlink($TargetFile))
									die();
							}
						}
						else
						{
							//Linux
							if (!unlink($TargetFile))
								die();
						}
					}
					
					//-- Rename Image
					$FilePath = pathinfo($Imagen);
					$EXT = $FilePath['extension'];
					$NewImage = "equipos/".$Codigo."_".$Today.".".$EXT."";
					rename("../".$Imagen."","../images/".$NewImage."");
					$Imagen = $NewImage;
				}
			}
			
			$query = "UPDATE `apu_equ_".$Clasificacion."` SET  
			codigo = '".$Codigo."', 
			codigo2 = '".$Codigo2."', 
			nombre = '".$Nombre."', 
			grupo = '".$Grupo."', 
			subgrupo = '".$SubGrupo."', 
			unidad = '".$Unidad."', 
			valor = '".$Valor."', 
			uso = '".$Uso."', 
			notas = '".$Notas."',  
			imagen = '".$Imagen."',  
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW() 
			WHERE id = '".$ID."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			// Ya hay Trigger
			/*if ($row["codigo"] != $Codigo)
			{
				$query = "UPDATE `apu_".$Clasificacion."_movs` SET objeto_codigo = '".$Codigo."' 
				WHERE objeto_codigo = '".$row["codigo"]."' AND clasificacion = '".$Clasificacion."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
			}*/
		}
	}
	else
	{
		$query = "SELECT * FROM `apu_equ_".$Clasificacion."` WHERE codigo = '".$Codigo."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "EXIST",
			);
			echo json_encode($ReturnData);
			die();
		}
		else
		{
			if ($Imagen != "")
			{
				//-- Rename Image
				$FilePath = pathinfo($Imagen);
				$EXT = $FilePath['extension'];
				$NewImage = "equipos/".$Codigo.".".$EXT."";
				rename("../".$Imagen."","../images/".$NewImage."");
				$Imagen = $NewImage;
			}
				
			$query = "INSERT INTO `apu_equ_".$Clasificacion."` (codigo, codigo2, nombre, categoria, grupo, subgrupo, unidad, valor, uso, 
			notas, imagen, digitado_por, fecha_digitado) VALUES ('".$Codigo."', '".$Codigo2."', '".$Nombre."', 'Equipos', '".$Grupo."', 
			'".$SubGrupo."', '".$Unidad."', '".$Valor."', '".$Uso."', '".$Notas."', '".$Imagen."', '".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
		}
	}
	
	$Proveedores = isset($data[0]["Proveedores"]) ? $data[0]["Proveedores"]:array();
	foreach($Proveedores as $item)
	{
		$Proveedor = $item["Proveedor"];
		$ProveedorID = $item["ProveedorID"];
		$ProveedorCodigo = $item["Codigo"];
		
		$query = "INSERT INTO `apu_equ_".$Clasificacion."_prov` (codigo, proveedor_id, proveedor_codigo) 
		VALUES ('".$Codigo."', '".$ProveedorID."', '".$ProveedorCodigo."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
	}
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_Equipos_Actualizar"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Nombre = isset($_POST["Nombre"]) ? $_POST["Nombre"]:die();
	$Unidad = isset($_POST["Unidad"]) ? $_POST["Unidad"]:die();
	$Uso = isset($_POST["Uso"]) ? $_POST["Uso"]:die();
	$Valor = isset($_POST["Valor"]) ? $_POST["Valor"]:die();
	$Notas = isset($_POST["Notas"]) ? $_POST["Notas"]:die();
	
	$query = "UPDATE `apu_equ_".$Clasificacion."` SET 
	nombre = '".$Nombre."', 
	unidad = '".$Unidad."', 
	uso = '".$Uso."', 
	valor = '".$Valor."', 
	notas = '".$Notas."' 
	WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$ReturnData[0] = array(
		"MESSAGE" => "OK",
	);
	echo json_encode($ReturnData);
}
else if (isset($_POST["Presupuesto_Equipos_Borrar"]))
{
	$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"]:die();
	$ID = isset($_POST["ID"]) ? $_POST["ID"]:die();
	$Codigo = isset($_POST["Codigo"]) ? $_POST["Codigo"]:die();
	
	$query = "SELECT * FROM apu_clasificacion";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$query = "SELECT * FROM `apu_".$row["clasificacion"]."_movs` WHERE objeto_codigo = '".$Codigo."' 
			AND tipo = 'Equipos' AND clasificacion = '".$Clasificacion."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$ReturnData[0] = array(
					"MESSAGE" => "USED",
				);
				echo json_encode($ReturnData);
				die();
			}
		}
		
		$query = "DELETE FROM `apu_equ_".$Clasificacion."` WHERE id = '".$ID."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_affected_rows() > 0)
		{
			$ReturnData[0] = array(
				"MESSAGE" => "OK",
			);
		}
		else
		{
			$ReturnData[0] = array(
				"MESSAGE" => "ERROR",
			);
		}
		echo json_encode($ReturnData);
	}
}
else if (isset($_GET["Presupuesto_Clasificacion"]))
{
	if (!isset($_GET["Clasificacion"]))
		die();
	$Clasificacion = $_GET["Clasificacion"];
	$Notas = $_GET["Nota"];
	
	Presupuesto_CrearClasificacion($Clasificacion, $Notas);
}
else if (isset($_GET["Presupuesto_Copiar_Clasificacion"]))
{
	$From_Clasificacion = isset($_GET["From_Clasificacion"]) ? $_GET["From_Clasificacion"]:die();
	$New_Clasificacion = isset($_GET["New_Clasificacion"]) ? $_GET["New_Clasificacion"]:die();
	$Notas = $_GET["Nota"];
	
	Presupuesto_CopiarClasificacion($From_Clasificacion, $New_Clasificacion, $Notas);
}
else if (isset($_GET["Presupuesto_Importar"]))
{
	mb_internal_encoding("UTF-8");
	$Clasificacion = isset($_GET["Clasificacion"]) ? strtolower($_GET["Clasificacion"]):die();
	$File = isset($_GET["File"]) ? $_GET["File"]:die();
	$Extension = strtolower(SUBSTR($File, -3, 3));
	$Today = date('Y-m-d-hhmmss');
	
	if (ExtractFile("../Uploads_Tmp/".$File, $Extension, $Today))
	{
		$DirectoryList = scandir("../Uploads_Tmp/".$Today);
		//echo "Numero de Archivos: ".(count($DirectoryList) - 2)."<br />";
		for ($i = 2; $i < count($DirectoryList); $i++)
		{
			if (stristr($DirectoryList[$i], "bas.dbf") != false || 
				stristr($DirectoryList[$i], "uni.dbf") != false || 
				stristr($DirectoryList[$i], "gru.dbf") != false || 
				stristr($DirectoryList[$i], "grm.dbf") != false || 
				stristr($DirectoryList[$i], "mat.dbf") != false || 
				stristr($DirectoryList[$i], "man.dbf") != false || 
				stristr($DirectoryList[$i], "equ.dbf") != false )
			{
				$DataDBF[] = Array(
					"FilePath" => pathinfo(realpath("../Uploads_Tmp/".$Today."/".$DirectoryList[$i]), PATHINFO_DIRNAME)."\\".$DirectoryList[$i],//$DirectoryList[$i];
					"Type" => SUBSTR($DirectoryList[$i], -7, 3),
				);
			}
			
			if (stristr($DirectoryList[$i], "bas.xlsx") != false || 
				stristr($DirectoryList[$i], "uni.xlsx") != false || 
				stristr($DirectoryList[$i], "gru.xlsx") != false || 
				stristr($DirectoryList[$i], "grm.xlsx") != false || 
				stristr($DirectoryList[$i], "mat.xlsx") != false || 
				stristr($DirectoryList[$i], "man.xlsx") != false || 
				stristr($DirectoryList[$i], "equ.xlsx") != false )
			{
				$DataXLSX[] = Array(
					"FilePath" => pathinfo(realpath("../Uploads_Tmp/".$Today."/".$DirectoryList[$i]), PATHINFO_DIRNAME)."\\".$DirectoryList[$i],
					"Type" => SUBSTR($DirectoryList[$i], -8, 3),
				);
			}
		}
		
		if (isset($DataDBF))
		{
			if (Presupuesto_CrearClasificacion($Clasificacion, "Creado desde Presupuesto -> Importar") != 1)
				die();
			
			$TmpDataArray = Read_DBF($DataDBF);
			
			//Separar Datos para poder Insertarlos en el Orden Correcto
			for ($i = 0; $i < count($TmpDataArray); $i++)
			{
				switch($TmpDataArray[$i]["Type"])
				{
					case "BAS":
						$BAS_Array[] = $TmpDataArray[$i]["Data"];
					break;
					
					case "UNI":
						$UNI_Array[] = $TmpDataArray[$i]["Data"];
					break;
					
					case "GRU":
						//Grupo
						$Codigo = SUBSTR($TmpDataArray[$i]["Data"]["CODGRUU"], 0, 2);
						if ($Codigo == "." || $Codigo == "")
						{
							//echo "Grupo APU -> ".$TmpDataArray[$i]["Data"]["DESGRUU"]."<br />";
							break;
						}
						
						//SubGrupo
						if (strstr($TmpDataArray[$i]["Data"]["CODGRUU"], ".") > 0)
							$Codigo2 = SUBSTR($TmpDataArray[$i]["Data"]["CODGRUU"], 3, 2);
						else
							$Codigo2 = SUBSTR($TmpDataArray[$i]["Data"]["CODGRUU"], 2, 2);
						
						if ($Codigo2 == "." || $Codigo2 == "" || $Codigo2 == "00")
						{
							$GRU_Array[] = array(
								"CODIGO" => $Codigo,
								"NOMBRE" => $TmpDataArray[$i]["Data"]["DESGRUU"],
							);
						}
						else
						{
							$SUB_GRU_Array[] = array(
								"GRUPO" => $Codigo,
								"CODIGO" => $Codigo2,
								"NOMBRE" => $TmpDataArray[$i]["Data"]["DESGRUU"],
							);
						}
					break;
					
					case "GRM":
						//Grupo
						$Codigo = SUBSTR($TmpDataArray[$i]["Data"]["CODGRUM"], 0, 2);
						if ($Codigo == "." || $Codigo == "")
						{
							//echo "Grupo MAT -> ".$TmpDataArray[$i]["Data"]["DESGRUM"]."<br />";
							break;
						}
						
						//SubGrupo
						if (strstr($TmpDataArray[$i]["Data"]["CODGRUM"], ".") > 0)
							$Codigo2 = SUBSTR($TmpDataArray[$i]["Data"]["CODGRUM"], 3, 2);
						else
							$Codigo2 = SUBSTR($TmpDataArray[$i]["Data"]["CODGRUM"], 2, 2);
						
						if ($Codigo2 == "." || $Codigo2 == "" || $Codigo2 == "00")
						{
							$GRM_Array[] = array(
								"CODIGO" => $Codigo,
								"NOMBRE" => $TmpDataArray[$i]["Data"]["DESGRUM"],
							);
						}
						else
						{
							$SUB_GRM_Array[] = array(
								"GRUPO" => $Codigo,
								"CODIGO" => $Codigo2,
								"NOMBRE" => $TmpDataArray[$i]["Data"]["DESGRUM"],
							);
						}
					break;
					
					case "MAT":
						$MAT_Array[] = $TmpDataArray[$i]["Data"];
					break;
					
					case "MAN":
						if (strlen($TmpDataArray[$i]["Data"]["CODMAN"]) < 5)
							break;
						
						if (SUBSTR($TmpDataArray[$i]["Data"]["CODMAN"], 0, 2) == "00")
							break;
						
						$MAN_Array[] = $TmpDataArray[$i]["Data"];
					break;
					
					case "EQU":
						if (strlen($TmpDataArray[$i]["Data"]["CODEQU"]) < 6)
							break;
						
						$EQU_Array[] = $TmpDataArray[$i]["Data"];
					break;
					
					default:
					break;
				}
			}
			
			//APU -> Grupo
			$GRU_Array = array_reverse($GRU_Array);
			for ($i = 0; $i < count($GRU_Array); $i++)
			{
				$query = "SELECT * FROM par_presupuesto_grupo_".$Clasificacion." 
				WHERE codigo = '".$GRU_Array[$i]["CODIGO"]."' AND categoria = 'APU'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					$query = "UPDATE par_presupuesto_grupo_".$Clasificacion." SET grupo = '".$GRU_Array[$i]["NOMBRE"]."' 
					WHERE codigo = '".$GRU_Array[$i]["CODIGO"]."' AND categoria = 'APU'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
				}
				else
				{
					$query = "INSERT INTO par_presupuesto_grupo_".$Clasificacion." (codigo, grupo, categoria) VALUES 
					('".$GRU_Array[$i]["CODIGO"]."', '".$GRU_Array[$i]["NOMBRE"]."', 'APU')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
				}
				
				// Grupo Perdido para APU Elaborados
				if (count($GRU_Array) - $i == 1)
				{
					$query = "INSERT INTO par_presupuesto_grupo_".$Clasificacion." (codigo, grupo, categoria) VALUES 
					('80', 'ELABORADOS PARA ANALISIS COMPLEJOS', 'APU')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
				}
			}
			//APU -> SubGrupo
			$SUB_GRU_Array = array_reverse($SUB_GRU_Array);
			for ($i = 0; $i < count($SUB_GRU_Array); $i++)
			{
				$query = "SELECT * FROM par_presupuesto_grupo_".$Clasificacion." 
				WHERE codigo = '".$SUB_GRU_Array[$i]["GRUPO"]."' AND categoria = 'APU'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-1: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					$query = "INSERT INTO par_presupuesto_subgrupo_".$Clasificacion." (codigo, subgrupo, grupo, categoria) VALUES 
					('".$SUB_GRU_Array[$i]["CODIGO"]."', '".$SUB_GRU_Array[$i]["NOMBRE"]."', '".$SUB_GRU_Array[$i]["GRUPO"]."', 'APU')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-2: ".mysql_error():"");
				}
				//else
				//	echo "SubGrupo APU Perdido -> ".$SUB_GRU_Array[$i]["NOMBRE"]."<br />";
			
				// SubGrupo Perdido para APU Elaborados
				if (count($SUB_GRU_Array) - $i == 1)
				{
					$query = "INSERT INTO par_presupuesto_subgrupo_".$Clasificacion." (codigo, subgrupo, grupo, categoria) VALUES 
					('01', 'N/A', '80', 'APU')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2-3: ".mysql_error():"");
				}
			}
			//Materiales -> Grupo
			$GRM_Array = array_reverse($GRM_Array);
			for ($i = 0; $i < count($GRM_Array); $i++)
			{
				$query = "SELECT * FROM par_presupuesto_grupo_".$Clasificacion." 
				WHERE codigo = '".$GRM_Array[$i]["CODIGO"]."' AND categoria = 'Materiales'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					$query = "UPDATE par_presupuesto_grupo_".$Clasificacion." SET grupo = '".$GRM_Array[$i]["NOMBRE"]."' 
					WHERE codigo = '".$GRM_Array[$i]["CODIGO"]."' AND categoria = 'Materiales'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
				}
				else
				{
					$query = "INSERT INTO par_presupuesto_grupo_".$Clasificacion." (codigo, grupo, categoria) VALUES 
					('".$GRM_Array[$i]["CODIGO"]."', '".$GRM_Array[$i]["NOMBRE"]."', 'Materiales')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-3: ".mysql_error():"");
				}
			}
			//Materiales -> SubGrupo
			$SUB_GRM_Array = array_reverse($SUB_GRM_Array);
			for ($i = 0; $i < count($SUB_GRM_Array); $i++)
			{
				$query = "SELECT * FROM par_presupuesto_grupo_".$Clasificacion." 
				WHERE codigo = '".$SUB_GRM_Array[$i]["GRUPO"]."' AND categoria = 'Materiales'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-1: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					//$row = mysql_fetch_array($result, MYSQL_ASSOC);
					$query = "INSERT INTO par_presupuesto_subgrupo_".$Clasificacion." (codigo, subgrupo, grupo, categoria) VALUES 
					('".$SUB_GRM_Array[$i]["CODIGO"]."', '".$SUB_GRM_Array[$i]["NOMBRE"]."', '".$SUB_GRM_Array[$i]["GRUPO"]."', 'Materiales')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-2: ".mysql_error():"");
				}
				//else
				//	echo "SubGrupo Materiales Perdido -> ".$SUB_GRM_Array[$i]["NOMBRE"]."<br />";
				
				// SubGrupo Perdido para Materiales/APU Elaborados
				if (count($SUB_GRM_Array) - $i == 1)
				{
					$query = "INSERT INTO par_presupuesto_subgrupo_".$Clasificacion." (codigo, subgrupo, grupo, categoria) VALUES 
					('01', 'N/A', '80', 'Materiales')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4-3: ".mysql_error():"");
				}
			}
			//Equipos/Maquinas
			$EQU_Array = array_reverse($EQU_Array);
			for ($i = 0; $i < count($EQU_Array); $i++)
			{
				$Grupo = SUBSTR($EQU_Array[$i]["CODEQU"], 0, 2);
				$SubGrupo = SUBSTR($EQU_Array[$i]["CODEQU"], 2, 2);
				$Codigo2 = SUBSTR($EQU_Array[$i]["CODEQU"], 4);
				$Codigo = $Grupo.".".$SubGrupo.".".$Codigo2;
				
				if (!is_numeric($Grupo) || substr_count($Grupo, ".") > 0)
				{
					continue;
				}
				
				if (!is_numeric($SubGrupo))
				{
					continue;
				}
				
				$query = "INSERT INTO apu_equ_".$Clasificacion." (codigo, codigo2, nombre, categoria, grupo, subgrupo, 
				unidad, valor, uso, notas, fecha, digitado_por, fecha_digitado) VALUES ('".$Codigo."', '".$Codigo2."', 
				'".$EQU_Array[$i]["DESEQU"]."', 'Equipos', '".$Grupo."', '".$SubGrupo."', '".$EQU_Array[$i]["UNIEQU"]."', 
				'".$EQU_Array[$i]["TAREQU"]."', '100', '".$EQU_Array[$i]["OBSEQU"]."', '".$EQU_Array[$i]["FECEQU"]."', 
				'".$_SESSION["UserCode"]."', NOW())";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-1: ".mysql_error():"");
				
				if ($EQU_Array[$i]["PROV1"] != "")
				{
					$query = "INSERT INTO apu_equ_".$Clasificacion."_prov (codigo, proveedor_codigo) 
					VALUES ('".$Codigo."', '".$EQU_Array[$i]["PROV1"]."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-2: ".mysql_error():"");
				}
				
				if ($EQU_Array[$i]["PROV2"] != "")
				{
					$query = "INSERT INTO apu_equ_".$Clasificacion."_prov (codigo, proveedor_codigo) 
					VALUES ('".$Codigo."', '".$EQU_Array[$i]["PROV2"]."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-3: ".mysql_error():"");
				}
				
				if ($EQU_Array[$i]["PROV3"] != "")
				{
					$query = "INSERT INTO apu_equ_".$Clasificacion."_prov (codigo, proveedor_codigo) 
					VALUES ('".$Codigo."', '".$EQU_Array[$i]["PROV3"]."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5-4: ".mysql_error():"");
				}
			}
			//Equipos/Maquinas -> Grupos/SubGrupos
			$query = "SELECT DISTINCT grupo FROM apu_equ_".$Clasificacion." ORDER BY grupo";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$i = 1;
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					//Grupo
					$query = "INSERT INTO par_presupuesto_grupo_".$Clasificacion." (codigo, grupo, categoria) 
					VALUES ('".$row["grupo"]."', 'Grupo_Equipo_".$i."', 'Equipos')";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-2: ".mysql_error():"");
					
					//SubGrupo
					$query = "SELECT DISTINCT subgrupo FROM apu_equ_".$Clasificacion." WHERE grupo = '".$row["grupo"]."' ORDER BY subgrupo";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-3: ".mysql_error():"");
					if (mysql_num_rows($result1) > 0)
					{
						$a = 1;
						while($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
						{
							$query = "INSERT INTO par_presupuesto_subgrupo_".$Clasificacion." (codigo, subgrupo, grupo, categoria) 
							VALUES ('".$row1["subgrupo"]."', 'SubGrupo_Equipo_".$a."', '".$row["grupo"]."', 'Equipos')";
							$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6-4: ".mysql_error():"");
							$a++;
						}
					}
					$i++;
				}
			}
			//Mano de Obra
			$MAN_Array = array_reverse($MAN_Array);
			for ($i = 0; $i < count($MAN_Array); $i++)
			{
				if (strlen($MAN_Array[$i]["CODMAN"]) < 6)
				{
					$Grupo = "0".SUBSTR($MAN_Array[$i]["CODMAN"], 0, 1);
					$SubGrupo = SUBSTR($MAN_Array[$i]["CODMAN"], 1, 2);
					$Codigo2 = SUBSTR($MAN_Array[$i]["CODMAN"], 3);
				}
				else
				{
					$Grupo = SUBSTR($MAN_Array[$i]["CODMAN"], 0, 2);
					$SubGrupo = SUBSTR($MAN_Array[$i]["CODMAN"], 2, 2);
					$Codigo2 = SUBSTR($MAN_Array[$i]["CODMAN"], 4);
				}
				
				$Codigo = $Grupo.".".$SubGrupo.".".$Codigo2;
				
				if (!is_numeric($Grupo) || !is_numeric($SubGrupo))
					continue;
				
				if ($Codigo == "")
					continue;
				
				$query = "INSERT INTO apu_mo_".$Clasificacion." (codigo, codigo2, nombre, categoria, grupo, 
				subgrupo, unidad, uso, valor_scc, notas, fecha, digitado_por, fecha_digitado) 
				VALUES ('".$Codigo."', '".$Codigo2."', '".$MAN_Array[$i]["DESMAN"]."', 'Mano de Obra', 
				'".$Grupo."', '".$SubGrupo."', '".$MAN_Array[$i]["UNIMAN"]."', '100', 
				'".$MAN_Array[$i]["JORMAN"]."', '".$MAN_Array[$i]["OBSMAN"]."', '".$MAN_Array[$i]["FECMAN"]."', 
				'".$_SESSION["UserCode"]."', NOW())";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7-1: ".mysql_error():"");
			}
			//Mano de Obra -> Grupos/SubGrupos
			$query = "SELECT DISTINCT grupo FROM apu_mo_".$Clasificacion." ORDER BY grupo";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$i = 1;
				while($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					//Grupo
					$query = "INSERT INTO par_presupuesto_grupo_".$Clasificacion." (codigo, grupo, categoria) 
					VALUES ('".$row["grupo"]."', 'Grupo_Mano_de_Obra_".$i."', 'Mano de Obra')";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-2: ".mysql_error():"");
					
					//SubGrupo
					$query = "SELECT DISTINCT subgrupo FROM apu_mo_".$Clasificacion." WHERE grupo = '".$row["grupo"]."' ORDER BY subgrupo";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-3: ".mysql_error():"");
					if (mysql_num_rows($result1) > 0)
					{
						$a = 1;
						while($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
						{
							$query = "INSERT INTO par_presupuesto_subgrupo_".$Clasificacion." (codigo, subgrupo, grupo, categoria) 
							VALUES ('".$row1["subgrupo"]."', 'SubGrupo_Mano_de_Obra_".$a."', '".$row["grupo"]."', 'Mano de Obra')";
							$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #8-4: ".mysql_error():"");
							$a++;
						}
					}
					$i++;
				}
			}
			//Materiales
			$MAT_Array = array_reverse($MAT_Array);
			for ($i = 0; $i < count($MAT_Array); $i++)
			{
				if ($i == 0)
				{
					// Material (Global/de Pruebas) para ser usado en APU elaborados?
					$query = "INSERT INTO apu_mat_".$Clasificacion." (codigo, codigo2, nombre, categoria, 
					grupo, subgrupo, unidad, valor, uso, notas, fecha, digitado_por, fecha_digitado) VALUES 
					('80.01.0001', '0001', 'GLOBAL', 'Materiales', '80', '01', 'und', '1', '100', 'Material Global', 
					'".$Today."', '".$_SESSION["UserCode"]."', NOW())";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-1: ".mysql_error():"");
				}
				
				if (substr_count($MAT_Array[$i]["CODMAT"], ".") == 2)
				{
					$Grupo = SUBSTR($MAT_Array[$i]["CODMAT"], 0, 2);
					$SubGrupo = SUBSTR($MAT_Array[$i]["CODMAT"], 3, 2);
					$Codigo = $MAT_Array[$i]["CODMAT"];//SUBSTR($MAT_Array[$i]["CODMAT"], 6);
					$Codigo2 = SUBSTR($MAT_Array[$i]["CODMAT"], 6);
					$Nombre = mysql_real_escape_string($MAT_Array[$i]["DESMAT"]);
					
					if (!is_numeric($Grupo))
					{
						//echo "Material Perdido -> ".$MAT_Array[$i]["DESMAT"]."<br />";
						continue;
					}
					
					if (!is_numeric($SubGrupo))
					{
						//echo "Material Perdido -> ".$MAT_Array[$i]["DESMAT"]."<br />";
						continue;
					}
					
					$query = "INSERT INTO apu_mat_".$Clasificacion." (codigo, codigo2, nombre, categoria, grupo, 
					subgrupo, unidad, valor, uso, notas, fecha, digitado_por, fecha_digitado) VALUES 
					('".$Codigo."', '".$Codigo2."', '".$Nombre."', 'Materiales', '".$Grupo."', 
					'".$SubGrupo."', '".$MAT_Array[$i]["UNIMAT"]."', '".$MAT_Array[$i]["VALMAT"]."', '100', 
					'".$MAT_Array[$i]["OBSMAT"]."', '".$MAT_Array[$i]["FECMAT"]."', '".$_SESSION["UserCode"]."', NOW())";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-2: ".mysql_error():"");
					
					if ($MAT_Array[$i]["PROV1"] != "")
					{
						$query = "INSERT INTO apu_mat_".$Clasificacion."_prov (codigo, proveedor_codigo) 
						VALUES ('".$Codigo."', '".$MAT_Array[$i]["PROV1"]."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-3: ".mysql_error():"");
					}
					
					if ($MAT_Array[$i]["PROV2"] != "")
					{
						$query = "INSERT INTO apu_mat_".$Clasificacion."_prov (codigo, proveedor_codigo) 
						VALUES ('".$Codigo."', '".$MAT_Array[$i]["PROV2"]."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-4: ".mysql_error():"");
					}
					
					if ($MAT_Array[$i]["PROV3"] != "")
					{
						$query = "INSERT INTO apu_mat_".$Clasificacion."_prov (codigo, proveedor_codigo) 
						VALUES ('".$Codigo."', '".$MAT_Array[$i]["PROV3"]."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-5: ".mysql_error():"");
					}
					
					if ($MAT_Array[$i]["PROV4"] != "")
					{
						$query = "INSERT INTO apu_mat_".$Clasificacion."_prov (codigo, proveedor_codigo) 
						VALUES ('".$Codigo."', '".$MAT_Array[$i]["PROV4"]."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-6: ".mysql_error():"");
					}
					
					if ($MAT_Array[$i]["PROV5"] != "")
					{
						$query = "INSERT INTO apu_mat_".$Clasificacion."_prov (codigo, proveedor_codigo) 
						VALUES ('".$Codigo."', '".$MAT_Array[$i]["PROV5"]."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #9-7: ".mysql_error():"");
					}
				}
				//else
				//	echo "Material Perdido -> ".$MAT_Array[$i]["CODMAT"]."<br />";
			}
			//APU
			$UNI_Array = array_reverse($UNI_Array);
			for ($i = 0; $i < count($UNI_Array); $i++)
			{
				$Grupo = SUBSTR($UNI_Array[$i]["CODMAT"], 0, 2);
				$SubGrupo = SUBSTR($UNI_Array[$i]["CODMAT"], 2, 2);
				$Codigo2 = SUBSTR($UNI_Array[$i]["CODMAT"], 4);
				$Codigo = $Grupo.".".$SubGrupo.".".$Codigo2;
				$Nombre = mysql_real_escape_string($UNI_Array[$i]["DESANA"]);
				
				if ($Grupo == "VI" || $Grupo == "EL" || $Grupo == "G0")
				{
					//echo "APU Perdido ->".$UNI_Array[$i]["CODMAT"]."<br />";
					continue;
				}
				
				if (!is_numeric($Grupo) || !is_numeric($SubGrupo))
				{
					//echo "APU Perdido -> ".$UNI_Array[$i]["CODMAT"]."<br />";
					// Excepcion por APU elaborados...
					//$Grupo = "80";
					$SubGrupo = "01";
					$Codigo = SUBSTR($UNI_Array[$i]["CODMAT"], -3, 3);
					
					// excepcion de algunos
					if ($Grupo == "80" && $Codigo == "001")
						$Codigo = "101";
					else if ($Grupo == "80" && $Codigo == "002")
						$Codigo = "102";
					else if ($Grupo == "80" && $Codigo == "003")
						$Codigo = "103";
					else if ($Grupo == "80" && $Codigo == "004")
						$Codigo = "104";
					else if ($Grupo == "80" && $Codigo == "005")
						$Codigo = "105";
					else if ($Grupo == "80" && $Codigo == "006")
						$Codigo = "106";
					else if ($Grupo == "80" && $Codigo == "007")
						$Codigo = "107";
					else if ($Grupo == "80" && $Codigo == "009")
						$Codigo = "109";
					
					$Codigo = $Grupo.".".$SubGrupo.".".$Codigo;
					$Codigo2 = SUBSTR($UNI_Array[$i]["CODMAT"], -3, 3);
					
					$query = "SELECT * FROM apu_".$Clasificacion." WHERE codigo = '".$Codigo."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-1: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						$query = "UPDATE apu_".$Clasificacion." SET 
						nombre = '".$Nombre."', 
						unidad = '".$UNI_Array[$i]["UNIANA"]."', 
						desperdicios = '".$UNI_Array[$i]["PORMAT"]."', 
						gastos = '".$UNI_Array[$i]["PORMAO"]."', 
						valor = '".$UNI_Array[$i]["VALANA"]."', 
						notas = '".$UNI_Array[$i]["OBSANA"]."' 
						WHERE codigo = '".$Codigo."'";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-2: ".mysql_error():"");
					}
					else
					{
						$query = "INSERT INTO apu_".$Clasificacion." (codigo, codigo2, nombre, categoria, grupo, subgrupo, 
						unidad, desperdicios, gastos, valor, notas) VALUES ('".$Codigo."', '".$Codigo2."', 
						'".$Nombre."', 'APU', '".$Grupo."', '".$SubGrupo."', '".$UNI_Array[$i]["UNIANA"]."', 
						'".$UNI_Array[$i]["PORMAT"]."', '".$UNI_Array[$i]["PORMAO"]."', '".$UNI_Array[$i]["VALANA"]."', 
						'".$UNI_Array[$i]["OBSANA"]."')";
						$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-3: ".mysql_error():"");
						
						// Insertar Material Global como movimiento de este APU o su valor quedara en 0 por los triggers;
						// No se puede hacer ya que no se sabe si tendrá o no movimientos...
						//$query = "INSERT INTO apu_".$Clasificacion."_movs (codigo, objeto_codigo, cantidad, uso, valor, total, 
						//tipo, clasificacion) VALUES ('".$Codigo."', '80.01.0001', '".$BAS_Array[$i]["CANBAS"]."', 
						//'100', '".$Valor."', '".$Total."', 'APU', '".$Clasificacion."')";
						//$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-4: ".mysql_error():"");
					}
					continue;
				}
				
				$query = "SELECT * FROM apu_".$Clasificacion." WHERE codigo = '".$Codigo."'";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-5: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					$query = "UPDATE apu_".$Clasificacion." SET 
					nombre = '".$Nombre."', 
					unidad = '".$UNI_Array[$i]["UNIANA"]."', 
					desperdicios = '".$UNI_Array[$i]["PORMAT"]."', 
					gastos = '".$UNI_Array[$i]["PORMAO"]."', 
					valor = '".$UNI_Array[$i]["VALANA"]."', 
					notas = '".$UNI_Array[$i]["OBSANA"]."' 
					WHERE codigo = '".$Codigo."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-6: ".mysql_error():"");
				}
				else
				{
					$query = "INSERT INTO apu_".$Clasificacion." (codigo, codigo2, nombre, categoria, grupo, subgrupo, 
					unidad, desperdicios, gastos, valor, notas) VALUES ('".$Codigo."', '".$Codigo2."', 
					'".$Nombre."', 'APU', '".$Grupo."', '".$SubGrupo."', '".$UNI_Array[$i]["UNIANA"]."', 
					'".$UNI_Array[$i]["PORMAT"]."', '".$UNI_Array[$i]["PORMAO"]."', '".$UNI_Array[$i]["VALANA"]."', 
					'".$UNI_Array[$i]["OBSANA"]."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #10-7: ".mysql_error():"");
				}
			}
			//APU_Movs
			$BAS_Array = array_reverse($BAS_Array);
			for ($i = 0; $i < count($BAS_Array); $i++)
			{
				$Grupo = SUBSTR($BAS_Array[$i]["CODUNI_F"], 0, 2);
				$SubGrupo = SUBSTR($BAS_Array[$i]["CODUNI_F"], 2, 2);
				$Codigo = SUBSTR($BAS_Array[$i]["CODUNI_F"], 4);
				$Codigo = $Grupo.".".$SubGrupo.".".$Codigo;
				if ($Grupo == "VI" || $Grupo == "EL" || $Grupo == "G0")
				{
					//echo "APU_Mov Perdido ->".$BAS_Array[$i]["CODUNI_F"]."<br />";
					continue;
				}
				
				if (!is_numeric($Grupo) || !is_numeric($SubGrupo))
				{
					//echo "APU_Mov ->".$BAS_Array[$i]["CODUNI_F"]."<br />";
					if (strlen($BAS_Array[$i]["CODUNI_F"]) == 0)
					{
						//echo "APU_Mov Perdido ->".$BAS_Array[$i]["CODUNI_F"]."<br />";
						continue;
					}
					
					//$Grupo = "80";
					$SubGrupo = "01";
					$Codigo = SUBSTR($BAS_Array[$i]["CODUNI_F"], -3, 3);
					
					// excepcion de algunos
					if ($Grupo == "80" && $Codigo == "001")
						$Codigo = "101";
					else if ($Grupo == "80" && $Codigo == "002")
						$Codigo = "102";
					else if ($Grupo == "80" && $Codigo == "003")
						$Codigo = "103";
					else if ($Grupo == "80" && $Codigo == "004")
						$Codigo = "104";
					else if ($Grupo == "80" && $Codigo == "005")
						$Codigo = "105";
					else if ($Grupo == "80" && $Codigo == "006")
						$Codigo = "106";
					else if ($Grupo == "80" && $Codigo == "007")
						$Codigo = "107";
					else if ($Grupo == "80" && $Codigo == "009")
						$Codigo = "109";
					
					$Codigo = $Grupo.".".$SubGrupo.".".$Codigo;
					$Valor = 0;
					
					switch($BAS_Array[$i]["TIPOBAS"])
					{
						case "1":
							$Tipo = "Materiales";
							
							$Objeto_Grupo = SUBSTR($BAS_Array[$i]["CODBAS"], 0, 2);
							$Objeto_SubGrupo = SUBSTR($BAS_Array[$i]["CODBAS"], 3, 2);
							$Objeto_Codigo = $BAS_Array[$i]["CODBAS"];
							
							if (!is_numeric($Objeto_Grupo) || !is_numeric($Objeto_SubGrupo))
							{
								//echo "APU_Mov -> Material Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								//break;
								$Tipo = "APU";
								if ($Objeto_Grupo == "EL")
									$Objeto_Grupo = "80";
								$Objeto_SubGrupo = $SubGrupo;
								$Objeto_Codigo = $Grupo.".".$SubGrupo.".".SUBSTR($BAS_Array[$i]["CODBAS"], -3, 3);
								$Check = SUBSTR($Objeto_Codigo, -3, 1);
								if (!is_numeric($Check))
								{
									//echo "APU_Mov -> Material/APU Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
									break;
								}
								
								$query = "SELECT valor FROM apu_".$Clasificacion." WHERE codigo = '".$Objeto_Codigo."'";
								$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-1: ".mysql_error():"");
								if (mysql_num_rows($result) > 0)
								{
									$row = mysql_fetch_array($result, MYSQL_ASSOC);
									$Valor = $row["valor"];
								}
								break;
							}
							
							if ($Objeto_Codigo == "")
							{
								//echo "APU_Mov -> Material Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								break;
							}
							
							$query = "SELECT valor FROM apu_mat_".$Clasificacion." WHERE codigo = '".$Objeto_Codigo."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-2: ".mysql_error():"");
							if (mysql_num_rows($result) > 0)
							{
								$row = mysql_fetch_array($result, MYSQL_ASSOC);
								$Valor = $row["valor"];
							}
						break;
						
						case "2":
							$Tipo = "Mano de Obra";
							
							if (strlen($BAS_Array[$i]["CODBAS"]) < 5)
								break;
							
							if (SUBSTR($BAS_Array[$i]["CODBAS"], 0, 2) == "00")
								break;
							
							if (strlen($BAS_Array[$i]["CODBAS"]) < 6)
							{
								$Objeto_Grupo = "0".SUBSTR($BAS_Array[$i]["CODBAS"], 0, 1);
								$Objeto_SubGrupo = SUBSTR($BAS_Array[$i]["CODBAS"], 1, 2);
								$Objeto_Codigo = SUBSTR($BAS_Array[$i]["CODBAS"], 3);
							}
							else
							{
								$Objeto_Grupo = SUBSTR($BAS_Array[$i]["CODBAS"], 0, 2);
								$Objeto_SubGrupo = SUBSTR($BAS_Array[$i]["CODBAS"], 2, 2);
								$Objeto_Codigo = SUBSTR($BAS_Array[$i]["CODBAS"], 4);
							}
							
							$Objeto_Codigo = $Objeto_Grupo.".".$Objeto_SubGrupo.".".$Objeto_Codigo;
							
							if (!is_numeric($Objeto_Grupo) || !is_numeric($Objeto_SubGrupo) || $Objeto_Codigo == "")
							{
								//echo "APU_Mov -> Mano de Obra Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								break;
							}
							
							$query = "SELECT valor_scc FROM apu_mo_".$Clasificacion." WHERE codigo = '".$Objeto_Codigo."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-3: ".mysql_error():"");
							if (mysql_num_rows($result) > 0)
							{
								$row = mysql_fetch_array($result, MYSQL_ASSOC);
								$Valor = $row["valor_scc"];
							}
						break;
						
						case "3":
							$Tipo = "Equipos";
							$Objeto_Grupo = SUBSTR($BAS_Array[$i]["CODBAS"], 0, 2);
							$Objeto_SubGrupo = SUBSTR($BAS_Array[$i]["CODBAS"], 2, 2);
							$Objeto_Codigo = SUBSTR($BAS_Array[$i]["CODBAS"], 4);
							$Objeto_Codigo = $Objeto_Grupo.".".$Objeto_SubGrupo.".".$Objeto_Codigo;
							
							if (!is_numeric($Objeto_Grupo) || !is_numeric($Objeto_SubGrupo))
							{
								//echo "APU_Mov -> Equipo Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								break;
							}
							
							$query = "SELECT valor FROM apu_equ_".$Clasificacion." WHERE codigo = '".$Objeto_Codigo."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-4: ".mysql_error():"");
							if (mysql_num_rows($result) > 0)
							{
								$row = mysql_fetch_array($result, MYSQL_ASSOC);
								$Valor = $row["valor"];
							}
						break;
					}
					if (strlen($Objeto_Codigo) < 8 || !is_numeric(SUBSTR($BAS_Array[$i]["CODBAS"], -3, 1)) || $Objeto_SubGrupo == " ")
						continue;
					
					$Total = $Valor * $BAS_Array[$i]["CANBAS"];
					
					//test...
					//if ($Codigo == "80.01.005")
					//	echo $BAS_Array[$i]["CODUNI_F"]." - ".$BAS_Array[$i]["CODBAS"]."<br />";
					
					$query = "INSERT INTO apu_".$Clasificacion."_movs (codigo, objeto_codigo, cantidad, uso, valor, total, 
					tipo, clasificacion) VALUES ('".$Codigo."', '".$Objeto_Codigo."', '".$BAS_Array[$i]["CANBAS"]."', 
					'100', '".$Valor."', '".$Total."', '".$Tipo."', '".$Clasificacion."')";
					// Ignorar errores de movimientos con APU no existentes. (no se agregaran por el enlazado de las tablas)
					$result = mysql_query($query);// or die($DEBUG ? "SQL ERROR #11-5: ".mysql_error():"");
				}
				else
				{
					$Uso = 0;
					$Valor = 0;
					switch($BAS_Array[$i]["TIPOBAS"])
					{
						case "1":
							$Tipo = "Materiales";
							
							$Objeto_Grupo = SUBSTR($BAS_Array[$i]["CODBAS"], 0, 2);
							$Objeto_SubGrupo = SUBSTR($BAS_Array[$i]["CODBAS"], 3, 2);
							$Objeto_Codigo = $BAS_Array[$i]["CODBAS"];
							
							if (!is_numeric($Objeto_Grupo) || !is_numeric($Objeto_SubGrupo))
							{
								//echo "APU_Mov -> Material Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								//break;
								$Tipo = "APU";
								$Objeto_Grupo = "80";
								$Objeto_SubGrupo = "01";
								$Objeto_Codigo = $Objeto_Grupo.".".$Objeto_SubGrupo.".".SUBSTR($BAS_Array[$i]["CODBAS"], -3, 3);
								$Check = SUBSTR($Objeto_Codigo, -3, 1);
								if (!is_numeric($Check))
								{
									//echo "APU_Mov -> Material/APU Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
									break;
								}
								
								$query = "SELECT valor FROM apu_".$Clasificacion." WHERE codigo = '".$Objeto_Codigo."'";
								$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-1: ".mysql_error():"");
								if (mysql_num_rows($result) > 0)
								{
									$row = mysql_fetch_array($result, MYSQL_ASSOC);
									$Valor = $row["valor"];
								}
								break;
							}
							
							if ($Objeto_Codigo == "")
							{
								//echo "APU_Mov -> Material Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								break;
							}
							
							$query = "SELECT valor, uso FROM apu_mat_".$Clasificacion." WHERE codigo = '".$Objeto_Codigo."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-6: ".mysql_error():"");
							if (mysql_num_rows($result) > 0)
							{
								$row = mysql_fetch_array($result, MYSQL_ASSOC);
								$Uso = $row["uso"];
								$Valor = $row["valor"];
							}
						break;
						
						case "2":
							$Tipo = "Mano de Obra";
							
							if (strlen($BAS_Array[$i]["CODBAS"]) < 5)
								break;
							
							if (SUBSTR($BAS_Array[$i]["CODBAS"], 0, 2) == "00")
								break;
							
							if (strlen($BAS_Array[$i]["CODBAS"]) < 6)
							{
								$Objeto_Grupo = "0".SUBSTR($BAS_Array[$i]["CODBAS"], 0, 1);
								$Objeto_SubGrupo = SUBSTR($BAS_Array[$i]["CODBAS"], 1, 2);
								$Objeto_Codigo = SUBSTR($BAS_Array[$i]["CODBAS"], 3);
							}
							else
							{
								$Objeto_Grupo = SUBSTR($BAS_Array[$i]["CODBAS"], 0, 2);
								$Objeto_SubGrupo = SUBSTR($BAS_Array[$i]["CODBAS"], 2, 2);
								$Objeto_Codigo = SUBSTR($BAS_Array[$i]["CODBAS"], 4);
							}
							
							$Objeto_Codigo = $Objeto_Grupo.".".$Objeto_SubGrupo.".".$Objeto_Codigo;
							
							if (!is_numeric($Objeto_Grupo))
								break;
							
							if (!is_numeric($Objeto_SubGrupo))
								break;
							
							if ($Objeto_Codigo == "")
							{
								//echo "APU_Mov -> Mano de Obra Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								break;
							}
							
							$query = "SELECT valor_scc, uso FROM apu_mo_".$Clasificacion." WHERE codigo = '".$Objeto_Codigo."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-7: ".mysql_error():"");
							if (mysql_num_rows($result) > 0)
							{
								$row = mysql_fetch_array($result, MYSQL_ASSOC);
								$Uso = $row["uso"];
								$Valor = $row["valor_scc"];
							}
						break;
						
						case "3":
							$Tipo = "Equipos";
							$Objeto_Grupo = SUBSTR($BAS_Array[$i]["CODBAS"], 0, 2);
							$Objeto_SubGrupo = SUBSTR($BAS_Array[$i]["CODBAS"], 2, 2);
							$Objeto_Codigo = SUBSTR($BAS_Array[$i]["CODBAS"], 4);
							$Objeto_Codigo = $Objeto_Grupo.".".$Objeto_SubGrupo.".".$Objeto_Codigo;
							
							if (!is_numeric($Objeto_Grupo))
							{
								//echo "APU_Mov -> Equipo Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								break;
							}
							
							if (!is_numeric($Objeto_SubGrupo))
							{
								//echo "APU_Mov -> Equipo Perdido -> ".$BAS_Array[$i]["CODBAS"]."<br />";
								break;
							}
							
							$query = "SELECT valor, uso FROM apu_equ_".$Clasificacion." WHERE codigo = '".$Objeto_Codigo."'";
							$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-8: ".mysql_error():"");
							if (mysql_num_rows($result) > 0)
							{
								$row = mysql_fetch_array($result, MYSQL_ASSOC);
								$Uso = $row["uso"];
								$Valor = $row["valor"];
							}
						break;
					}
					if (strlen($Objeto_Codigo) < 8 || !is_numeric(SUBSTR($BAS_Array[$i]["CODBAS"], -3, 1)))
						continue;
					
					$Total = $Valor * $BAS_Array[$i]["CANBAS"];
					
					$query = "INSERT INTO apu_".$Clasificacion."_movs (codigo, objeto_codigo, cantidad, uso, valor, total, 
					tipo, clasificacion) VALUES ('".$Codigo."', '".$Objeto_Codigo."', '".$BAS_Array[$i]["CANBAS"]."', 
					'".$Uso."', '".$Valor."', '".$Total."', '".$Tipo."', '".$Clasificacion."')";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #11-9: ".mysql_error():"");
				}
			}
		}
		
		if (isset($DataXLSX))
		{
			//if (Presupuesto_CrearClasificacion($Clasificacion, "Creado desde Presupuesto -> Importar") != 1)
			//	die();
			//$TmpDataArray = Read_XLSX($DataXLSX);
		}
		echo "OK";
	}
	else
	{
		echo "EXTRACT";
	}
}
else if (isset($_POST["Requerimientos_Crear"]))
{
	$data = $_POST["Requerimientos_Crear"];
	
	foreach($data as $item)
	{
		$Codigo = $item["Codigo"];
		$Cantidad = $item["Cantidad"];
		$Valor = $item["Valor"];
		
		if ($final == false)
		{
			$ClienteID = $item["ClienteID"];
			$Fecha = $item["Fecha"];
			$Prioridad = $item["Prioridad"];
			$Observaciones = $item["Observaciones"];
			
			$query = "SELECT id FROM req_solicitud ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 6)
			{
				$num = 6 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Interno = "REQ".$zero."".$id."";
			}
			else {
				$Interno = "REQ".$id."";
			}
		}
		
		//--------------------------------------------------------------------------------//
		//-------------------------------- INSERT COMMAND --------------------------------//
		//--------------------------------------------------------------------------------//
		
		//-- Movimientos
		$query = "INSERT INTO req_solicitud_movs
		(codigo, cantidad, valor, interno, remision, cliente_id) VALUES 
		('".$Codigo."', '".$Cantidad."', '".$Valor."', '".$Interno."', '".$Interno."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		//-- Solicitud
		if ($final == false)
		{
			$query = "INSERT INTO req_solicitud 
			(interno, remision, fecha, cliente_id, observaciones, estado, prioridad, digitado_por, fecha_digitado) 
			VALUES ('".$Interno."', '".$Interno."', '".$Fecha."', '".$ClienteID."', '".$Observaciones."', 
			'Pendiente', '".$Prioridad."', '".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			$final = true;
		}
	}
	
	$Data[] = array (
		'MESSAGE' => $Interno,
	);
	echo json_encode($Data);
}
else if (isset($_POST["Requerimientos_Modificar"]))
{
	$data = $_POST["Requerimientos_Modificar"];
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM req_solicitud WHERE interno = '".$data[0]["Interno"]."' AND estado = 'Pendiente'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	foreach($data as $item)
	{
		$Codigo = $item["Codigo"];
		$Cantidad = $item["Cantidad"];
		$Valor = $item["Valor"];
		
		if ($final == false)
		{
			$Interno = $item["Interno"];
			$ClienteID = $item["ClienteID"];
			$Fecha = $item["Fecha"];
			$Prioridad = $item["Prioridad"];
			$Observaciones = $item["Observaciones"];
			
			$query = "DELETE FROM req_solicitud_movs WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		}
		
		//--------------------------------------------------------------------------------//
		//-------------------------------- INSERT COMMAND --------------------------------//
		//--------------------------------------------------------------------------------//
		
		//-- Movimientos
		$query = "INSERT INTO req_solicitud_movs
		(codigo, cantidad, valor, interno, remision, cliente_id) VALUES 
		('".$Codigo."', '".$Cantidad."', '".$Valor."', '".$Interno."', '".$Interno."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		
		//-- Solicitud
		if ($final == false)
		{
			$query = "UPDATE req_solicitud SET 
			fecha = '".$Fecha."', 
			cliente_id = '".$ClienteID."', 
			observaciones = '".$Observaciones."', 
			prioridad = '".$Prioridad."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW() 
			WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			$final = true;
		}
	}
	
	$Data[] = array (
		'MESSAGE' => "OK",
	);
	echo json_encode($Data);
}
else if (isset($_POST["Requerimientos_Compras"]))
{
	$data = $_POST["Requerimientos_Compras"];
	
	foreach($data as $item)
	{
		$Codigo = $item["Codigo"];
		$Cantidad = $item["Cantidad"];
		$Valor = $item["Valor"];
		
		if ($final == false)
		{
			$ClienteID = $item["ClienteID"];
			$Fecha = $item["Fecha"];
			$Factura = $item["Factura"];
			$Grupo = $item["Grupo"];
			$SubGrupo = $item["SubGrupo"];
			$SubGrupo2 = $item["SubGrupo2"];
			$SubTotal = $item["SubTotal"];
			$Servicio_Desc = $item["Servicio_Desc"];
			$Servicio = $item["Servicio"];
			$IVA = $item["IVA"];
			$Descuento_Desc = $item["Descuento_Desc"];
			$Descuento = $item["Descuento"];
			$Total = $item["Total"];
			$Observaciones = $item["Observaciones"];
			
			$query = "SELECT id FROM req_compras ORDER BY id DESC LIMIT 1";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			
			$row = mysql_fetch_array($result);
			$id = $row['id'] + 1;
			$len = strlen($id);
			$zero = "";
			if ($len < 6)
			{
				$num = 6 - $len;
				for ($a = 0; $a < $num; $a++) {
					$zero .= "0";
				}
				$Interno = "RCO".$zero."".$id."";
			}
			else {
				$Interno = "RCO".$id."";
			}
		}
		
		//--------------------------------------------------------------------------------//
		//-------------------------------- INSERT COMMAND --------------------------------//
		//--------------------------------------------------------------------------------//
		
		//-- Movimientos
		$query = "INSERT INTO req_compras_movs
		(codigo, cantidad, valor, interno, factura, cliente_id) VALUES 
		('".$Codigo."', '".$Cantidad."', '".$Valor."', '".$Interno."', '".$Factura."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		//-- Solicitud & CxP
		if ($final == false)
		{
			$query = "INSERT INTO req_compras 
			(fecha, cliente_id, interno, factura, grupo, subgrupo, subgrupo2, subtotal, tipo_servicio, tipo_servicio_valor, 
			iva, tipo_descuento, tipo_descuento_valor, total, observaciones, estado, digitado_por, fecha_digitado) 
			VALUES ('".$Fecha."', '".$ClienteID."', '".$Interno."', '".$Factura."', '".$Grupo."', '".$SubGrupo."', 
			'".$SubGrupo2."', '".$SubTotal."', '".$Servicio_Desc."', '".$Servicio."', '".$IVA."', '".$Descuento_Desc."', 
			'".$Descuento."', '".$Total."', '".$Observaciones."', 'Pendiente', '".$_SESSION["UserCode"]."', NOW())";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			
			$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, 
			valor, saldo, estado, digitado_por, fecha_digitado, grupo, subgrupo, subgrupo2) VALUES ('".$Interno."', 
			'".$Interno."', '".$Factura."', '".$ClienteID."', 'Compra', '".$Total."', '".$Total."', 'Pendiente', 
			'".$_SESSION["UserCode"]."', NOW(), '".$Grupo."', '".$SubGrupo."', '".$SubGrupo2."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			
			$final = true;
		}
	}
	
	if (isset($Interno))
	{
		$Data[] = array (
			'MESSAGE' => $Interno,
		);
	}
	else
	{
		$Data[] = array (
			'MESSAGE' => "ERROR",
		);
	}
	echo json_encode($Data);
}
else if (isset($_POST["Requerimientos_Compras_Mod"]))
{
	$data = $_POST["Requerimientos_Compras_Mod"];
	$DigitadorID = "";
	
	//-------- CHECK CHANGES
	$query = "SELECT digitado_por FROM req_compras WHERE interno = '".$data[0]["Interno"]."' AND estado = 'Pendiente'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) < 1)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
		echo json_encode($ReturnData);
		die();
	}
	else
	{
		$row = mysql_fetch_array($result);
		$DigitadorID = $row["digitado_por"];
	}
	
	foreach($data as $item)
	{
		$Codigo = $item["Codigo"];
		$Cantidad = $item["Cantidad"];
		$Valor = $item["Valor"];
		
		if ($final == false)
		{
			$Interno = $item["Interno"];
			$ClienteID = $item["ClienteID"];
			$Fecha = $item["Fecha"];
			$Factura = $item["Factura"];
			$Grupo = $item["Grupo"];
			$SubGrupo = $item["SubGrupo"];
			$SubGrupo2 = $item["SubGrupo2"];
			$SubTotal = $item["SubTotal"];
			$Servicio_Desc = $item["Servicio_Desc"];
			$Servicio = $item["Servicio"];
			$IVA = $item["IVA"];
			$Descuento_Desc = $item["Descuento_Desc"];
			$Descuento = $item["Descuento"];
			$Total = $item["Total"];
			$Observaciones = $item["Observaciones"];
			
			$query = "DELETE FROM req_compras_movs WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			
			Anular_CxPMovs($Interno, "Anulado Automaticamente por Requerimientos -> Compras Modificar");
		}
		
		//--------------------------------------------------------------------------------//
		//-------------------------------- INSERT COMMAND --------------------------------//
		//--------------------------------------------------------------------------------//
		
		//-- Movimientos
		$query = "INSERT INTO req_compras_movs
		(codigo, cantidad, valor, interno, factura, cliente_id) VALUES 
		('".$Codigo."', '".$Cantidad."', '".$Valor."', '".$Interno."', '".$Factura."', '".$ClienteID."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		
		//-- Solicitud
		if ($final == false)
		{
			$query = "UPDATE req_compras SET 
			fecha = '".$Fecha."', 
			cliente_id = '".$ClienteID."', 
			factura = '".$Factura."', 
			grupo = '".$Grupo."', 
			subgrupo = '".$SubGrupo."', 
			subgrupo2 = '".$SubGrupo2."', 
			subtotal = '".$SubTotal."', 
			tipo_servicio = '".$Servicio_Desc."', 
			tipo_servicio_valor = '".$Servicio."', 
			iva = '".$IVA."', 
			tipo_descuento = '".$Descuento_Desc."', 
			tipo_descuento_valor = '".$Descuento."', 
			total = '".$Total."', 
			observaciones = '".$Observaciones."', 
			modificado_por = '".$_SESSION["UserCode"]."', 
			fecha_modificado = NOW()  
			WHERE interno = '".$Interno."'";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			
			$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, 
			valor, saldo, estado, digitado_por, fecha_digitado, grupo, subgrupo, subgrupo2) VALUES ('".$Interno."', 
			'".$Interno."', '".$Factura."', '".$ClienteID."', 'Compra', '".$Total."', '".$Total."', 'Pendiente', 
			'".$DigitadorID."', NOW(), '".$Grupo."', '".$SubGrupo."', '".$SubGrupo2."')";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
			
			$final = true;
		}
	}
	
	$Data[] = array (
		'MESSAGE' => "OK",
	);
	echo json_encode($Data);
}
else if (isset($_POST["Requerimientos_Listado"]))
{
	$Old_Estado = isset($_POST["Old_Estado"]) ? $_POST["Old_Estado"]:"Pendiente";
	$New_Estado = isset($_POST["New_Estado"]) ? $_POST["New_Estado"]:"Pendiente";
	$Interno = isset($_POST["Interno"]) ? $_POST["Interno"]:"";
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM req_solicitud WHERE interno = '".$Interno."' AND estado = '".$Old_Estado."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
	}
	else
	{
		$query = "UPDATE req_solicitud SET 
		estado = '".$New_Estado."'";
		$query .= ($New_Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
		$query .= ($New_Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
		$query .= " WHERE interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		if (mysql_affected_rows() > 0)
		{
			switch($New_Estado)
			{
				case "Anulado":
					// Anular Inventario
					if ($Old_Estado == "Aprobado")
					{
						Del_InventoryMovs2($Interno);
					}
				break;
				
				case "Aprobado":
					$query = "SELECT * FROM req_solicitud_movs WHERE interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						while($row = mysql_fetch_array($result, MYSQL_ASSOC))
						{
							AfectarInventario2($row["codigo"], $row["cantidad"], $row["valor"], $Interno, "Solicitud", "Requerimientos_Solicitud");
						}
					}
					//AfectarInventario2($Codigo, $Cantidad, $Valor, $Interno, "Compra", "Requerimientos_Compra");
				break;
			}
		}
		
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_POST["Requerimientos_Aprobar"]))
{
	$Old_Estado = isset($_POST["Old_Estado"]) ? $_POST["Old_Estado"]:"Pendiente";
	$New_Estado = isset($_POST["New_Estado"]) ? $_POST["New_Estado"]:"Pendiente";
	$Interno = isset($_POST["Interno"]) ? $_POST["Interno"]:"";
	
	//-------- CHECK CHANGES
	$query = "SELECT id FROM req_compras WHERE interno = '".$Interno."' AND estado = '".$Old_Estado."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) <= 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "CHANGED",
		);
	}
	else
	{
		$query = "UPDATE req_compras SET 
		estado = '".$New_Estado."'";
		$query .= ($New_Estado == "Anulado") ? ", anulado_por = '".$_SESSION["UserCode"]."', fecha_anulado = NOW()":"";
		$query .= ($New_Estado == "Aprobado") ? ", aprobado_por = '".$_SESSION["UserCode"]."', fecha_aprobado = NOW()":"";
		$query .= " WHERE interno = '".$Interno."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		if (mysql_affected_rows() > 0)
		{
			switch($New_Estado)
			{
				case "Anulado":
					// Anular Inventario & CxP
					if ($Old_Estado == "Aprobado")
						Del_InventoryMovs2($Interno);
					
					$query = "SELECT compra_entrada FROM cxp_movs WHERE compra_interno = '".$Interno."' ";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
						Anular_CxPMovs($Interno, "Anulado Automaticamente por Requerimientos -> Aprobar");
					
				break;
				
				case "Aprobado":
					// Afectar Inventario & Aprobar CxP
					$query = "SELECT * FROM req_compras_movs WHERE interno = '".$Interno."'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
					if (mysql_num_rows($result) > 0)
					{
						while($row = mysql_fetch_array($result, MYSQL_ASSOC))
							AfectarInventario2($row["codigo"], $row["cantidad"], $row["valor"], $Interno, "Compra", "Requerimientos_Compra");
					}
					
					$query = "UPDATE cxp_movs SET 
					estado = 'Aprobado', 
					aprobado_por = '".$_SESSION["UserCode"]."', 
					fecha_aprobado = NOW() 
					WHERE compra_interno = '".$Interno."' AND tipo_movimiento = 'Compra'";
					$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
				break;
			}
		}
		
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_GET["Requerimientos_Productos_Agregar"]))
{
	$Codigo = isset($_GET["Codigo"]) ? $_GET["Codigo"]:die();
	$Nombre = isset($_GET["Nombre"]) ? $_GET["Nombre"]:die();
	$Categoria = isset($_GET["Categoria"]) ? $_GET["Categoria"]:die();
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:die();
	$SubGrupo = isset($_GET["SubGrupo"]) ? $_GET["SubGrupo"]:die();
	$Unidad = isset($_GET["Unidad"]) ? $_GET["Unidad"]:die();
	$Peso = isset($_GET["Peso"]) ? $_GET["Peso"]:die();
	$Valor = isset($_GET["Valor"]) ? $_GET["Valor"]:die();
	$Existencia = isset($_GET["Existencia"]) ? $_GET["Existencia"]:die();
	$Stock = isset($_GET["Stock"]) ? $_GET["Stock"]:die();
	$Vencimiento = isset($_GET["Vencimiento"]) ? $_GET["Vencimiento"]:die();
	
	$query = "SELECT id FROM requerimientos_productos WHERE codigo = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "EXIST",
		);
		echo json_encode($ReturnData);
		die();
	}
	
	$query = "INSERT INTO requerimientos_productos (codigo, nombre, categoria, grupo, subgrupo, unidad, peso, valor, 
	existencia, stock, vencimiento, digitado_por, fecha_digitado) VALUES ('".$Codigo."', '".$Nombre."', '".$Categoria."', 
	'".$Grupo."', '".$SubGrupo."', '".$Unidad."', '".$Peso."', '".$Valor."', '".$Existencia."', '".$Stock."',
	'".$Vencimiento."', '".$_SESSION["UserCode"]."', NOW())";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
	}
	
	echo json_encode($ReturnData);
}
else if (isset($_GET["Requerimientos_Productos_Actualizar"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$Codigo = isset($_GET["Codigo"]) ? $_GET["Codigo"]:die();
	$Nombre = isset($_GET["Nombre"]) ? $_GET["Nombre"]:die();
	$Categoria = isset($_GET["Categoria"]) ? $_GET["Categoria"]:die();
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:die();
	$SubGrupo = isset($_GET["SubGrupo"]) ? $_GET["SubGrupo"]:die();
	$Unidad = isset($_GET["Unidad"]) ? $_GET["Unidad"]:die();
	$Peso = isset($_GET["Peso"]) ? $_GET["Peso"]:die();
	$Valor = isset($_GET["Valor"]) ? $_GET["Valor"]:die();
	$Existencia = isset($_GET["Existencia"]) ? $_GET["Existencia"]:die();
	$Stock = isset($_GET["Stock"]) ? $_GET["Stock"]:die();
	$Vencimiento = isset($_GET["Vencimiento"]) ? $_GET["Vencimiento"]:die();
	$Notas = isset($_GET["Notas"]) ? $_GET["Notas"]:die();
	
	$query = "UPDATE requerimientos_productos SET 
	codigo = '".$Codigo."', 
	nombre = '".$Nombre."', 
	categoria = '".$Categoria."', 
	grupo = '".$Grupo."', 
	subgrupo = '".$SubGrupo."', 
	unidad = '".$Unidad."', 
	peso = '".$Peso."', 
	valor = '".$Valor."', 
	existencia = '".$Existencia."', 
	stock = '".$Stock."', 
	notas = '".$Notas."', 
	vencimiento = '".$Vencimiento."' 
	WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
	}
	
	echo json_encode($ReturnData);
}
else if (isset($_GET["Requerimientos_Productos_Borrar"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM requerimientos_productos WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
	}
	
	echo json_encode($ReturnData);
}
else if (isset($_GET["Kardex_Notas"]))
{
	$Observaciones = isset($_GET["Observaciones"]) ? $_GET["Observaciones"]:"";
	$Codigo = isset($_GET["Codigo"]) ? $_GET["Codigo"]:"";
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
	//--
	$query = "UPDATE inventario_movs SET observacion = '".$Observaciones."' 
	WHERE cod_fab = '".$Codigo."' AND interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_affected_rows() > 0)
	{
		$ReturnData[0] = array(
			"MESSAGE" => "OK",
		);
	}
	else
	{
		$ReturnData[0] = array(
			"MESSAGE" => "ERROR",
		);
	}
	echo json_encode($ReturnData);
}
else if (isset($_GET["Parametros_Agregar_Clasificacion"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_ter_clasificacion (clasificacion) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Clasificacion"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_clasificacion SET clasificacion = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Clasificacion"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_clasificacion WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Tipo"]))
{
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "INSERT INTO par_ter_tipo (clasificacion, tipo) VALUES ('".$VAL1."', '".$VAL2."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Tipo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_tipo SET tipo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Tipo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_tipo WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Grupo"]))
{
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "INSERT INTO par_ter_grupo (clasificacion, grupo) VALUES ('".$VAL1."', '".$VAL2."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Grupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_grupo SET grupo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Grupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_grupo WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Documento"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_ter_tipo_doc (tipo_doc) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Documento"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_tipo_doc SET tipo_doc = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Documento"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_tipo_doc WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Sociedad"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_ter_tipo_sociedad (tipo_sociedad) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Sociedad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_tipo_sociedad SET tipo_sociedad = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Sociedad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_tipo_sociedad WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Barrio"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_ter_barrio (barrio) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Barrio"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_barrio SET barrio = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Barrio"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_barrio WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Garantia"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_ter_gara (garantia) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Garantia"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_gara SET garantia = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Garantia"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_gara WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Motivo"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_ter_motivo (motivo) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Motivo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_motivo SET motivo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Motivo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_motivo WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Novedad"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_ter_novedad (novedad) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Novedad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_ter_novedad SET novedad = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Novedad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_ter_novedad WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Chofer"]))
{
	$Placa = isset($_GET["Placa"]) ? $_GET["Placa"]:die();
	
	$query = "INSERT INTO par_fac_vehiculo (placa) VALUES ('".$Placa."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Chofer"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$Placa = isset($_GET["Placa"]) ? $_GET["Placa"]:"";
	$Modelo = isset($_GET["Modelo"]) ? $_GET["Modelo"]:"";
	$Tipo = isset($_GET["Tipo"]) ? $_GET["Tipo"]:"";
	
	$query = "UPDATE par_fac_vehiculo SET  
	placa = '".$Placa."', 
	modelo = '".$Modelo."', 
	tipo = '".$Tipo."' 
	WHERE id = '".$ID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Chofer"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_fac_vehiculo WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Servicio"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	$query = "INSERT INTO par_fac_otr_ser (nombre) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Servicio"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_fac_otr_ser SET nombre = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Servicio"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_fac_otr_ser WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Ruta"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_fac_ruta (barrio) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Ruta"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "UPDATE par_fac_ruta SET barrio = '".$VAL1."', ruta = '".$VAL2."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Ruta"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_fac_ruta WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Descuento"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_fac_tipo_dcto (tipo_descuento) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Descuento"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_fac_tipo_dcto SET tipo_descuento = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Descuento"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_fac_tipo_dcto WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Motivo_Anulacion"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_fac_anula (concepto) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Motivo_Anulacion"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_fac_anula SET concepto = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Motivo_Anulacion"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_fac_anula WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Inventario_Categoria"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_inv_cat (categoria) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Inventario_Categoria"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_inv_cat SET categoria = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Inventario_Categoria"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_inv_cat WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Inventario_Grupo"]))
{
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "INSERT INTO par_inv_gr (grupo, categoria) VALUES ('".$VAL2."', '".$VAL1."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Inventario_Grupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_inv_gr SET grupo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Inventario_Grupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_inv_gr WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Inventario_SubGrupo"]))
{
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "INSERT INTO par_inv_subgr (subgrupo, grupo) VALUES ('".$VAL2."', '".$VAL1."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Inventario_SubGrupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_inv_subgr SET subgrupo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Inventario_SubGrupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_inv_subgr WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Inventario_Unidad"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_inv_und (unidad) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Inventario_Unidad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_inv_und SET unidad = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Inventario_Unidad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_inv_und WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Caja_Categoria"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_caja_cat (categoria) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Caja_Categoria"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_caja_cat SET categoria = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Caja_Categoria"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_caja_cat WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Caja_Grupo"]))
{
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "INSERT INTO par_caja_gr (grupo, categoria) VALUES ('".$VAL2."', '".$VAL1."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Caja_Grupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_caja_gr SET grupo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Caja_Grupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_caja_gr WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Caja_SubGrupo"]))
{
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "INSERT INTO par_caja_subgr (subgrupo, grupo) VALUES ('".$VAL2."', '".$VAL1."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Caja_SubGrupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_caja_subgr SET subgrupo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Caja_SubGrupo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_caja_subgr WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Caja_SubGrupo2"]))
{
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "INSERT INTO par_caja_subgr2 (subgrupo2, grupo) VALUES ('".$VAL2."', '".$VAL1."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Caja_SubGrupo2"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_caja_subgr2 SET subgrupo2 = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Caja_SubGrupo2"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_caja_subgr2 WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Banco"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_caja_bancos (banco) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Banco"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "UPDATE par_caja_bancos SET banco = '".$VAL1."', cuenta = '".$VAL2."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Banco"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_caja_bancos WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Caja_Descuento"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_caja_dcto (descuentos) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Caja_Descuento"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_caja_dcto SET descuentos = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Caja_Descuento"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_caja_dcto WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Estados"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_maq_estado (estado) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Estados"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_maq_estado SET estado = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Estados"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_maq_estado WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Salud"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_salud (nombre) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Salud"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_nom_salud SET nombre = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Salud"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_salud WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Pension"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_pension (nombre) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Pension"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_nom_pension SET nombre = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Pension"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_pension WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Cesantia"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_cesantia (nombre) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Cesantia"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_nom_cesantia SET nombre = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Cesantia"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_cesantia WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Salario"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_nom SET valor = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Retenciones"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_retencion (salario) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Retenciones"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "UPDATE par_nom_retencion SET salario = '".$VAL1."', valor = '".$VAL2."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Retenciones"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_retencion WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Novedad"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_nov (novedad) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Novedad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	$VAL3 = isset($_GET["VAL3"]) ? $_GET["VAL3"]:die();
	$VAL4 = isset($_GET["VAL4"]) ? $_GET["VAL4"]:die();
	$VAL5 = isset($_GET["VAL5"]) ? $_GET["VAL5"]:die();
	
	$query = "UPDATE par_nom_nov SET 
	novedad = '".$VAL1."', 
	descontable = '".$VAL2."', 
	remunerado1 = '".$VAL3."', 
	remunerado2 = '".$VAL4."', 
	cesantia = '".$VAL5."' 
	WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Novedad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_nov WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Justificacion1"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_justificacion (justificacion) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Justificacion1"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_nom_justificacion SET justificacion = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Justificacion1"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_justificacion WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Justificacion2"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_ext (tipo) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Justificacion2"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_nom_ext SET tipo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Justificacion2"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_ext WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Reposicion"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_reposicion (reposicion) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Reposicion"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "UPDATE par_nom_reposicion SET reposicion = '".$VAL1."', reponer = '".$VAL2."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Reposicion"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_reposicion WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Horarios"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "UPDATE par_nom_horario SET hora_ini = '".$VAL1."', hora_fin = '".$VAL2."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Festivo"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_fest (nombre) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Festivo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "UPDATE par_nom_fest SET nombre = '".$VAL1."', fecha = '".$VAL2."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Festivo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_fest WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Nomina_Cargo"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_nom_cargos (cargo) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Cargo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_nom_cargos SET cargo = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Nomina_Cargo"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_nom_cargos WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Nomina_Carnet"]))
{
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "UPDATE par_nom_carnet SET desc1 = '".$VAL1."', desc2 = '".$VAL2."' WHERE id = '1' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Presupuesto_Clasificacion"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL1 = strtolower($VAL1);
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "SELECT * FROM apu_clasificacion WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$query = "SELECT * FROM apu_clasificacion WHERE clasificacion = '".$VAL1."' AND id != '".$ID."'";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			echo "EXIST";
		}
		else
		{
			$query = "UPDATE apu_clasificacion SET clasificacion = '".$VAL1."', nota = '".$VAL2."' WHERE id = '".$ID."' ";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
			
			if ($VAL1 != $row["clasificacion"])
			{
				$query = "UPDATE presupuesto_movs SET clasificacion = '".$VAL1."' WHERE clasificacion = '".$row["clasificacion"]."' ";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
				
				$query = "RENAME TABLE `apu_".$row["clasificacion"]."` TO `apu_".$VAL1."`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-5: ".mysql_error():"");
				
				$query = "RENAME TABLE `apu_".$row["clasificacion"]."_movs` TO `apu_".$VAL1."_movs`;";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-6: ".mysql_error():"");
				
				$query = "RENAME TABLE `apu_mo_".$row["clasificacion"]."` TO `apu_mo_".$VAL1."`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-7: ".mysql_error():"");
				
				$query = "RENAME TABLE `apu_mo_".$row["clasificacion"]."_movs` TO `apu_mo_".$VAL1."_movs`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-8: ".mysql_error():"");
				
				$query = "RENAME TABLE `apu_equ_".$row["clasificacion"]."` TO `apu_equ_".$VAL1."`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-9: ".mysql_error():"");
				
				$query = "RENAME TABLE `apu_equ_".$row["clasificacion"]."_prov` TO `apu_equ_".$VAL1."_prov`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-10: ".mysql_error():"");
				
				$query = "RENAME TABLE `apu_mat_".$row["clasificacion"]."` TO `apu_mat_".$VAL1."`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-11: ".mysql_error():"");
				
				$query = "RENAME TABLE `apu_mat_".$row["clasificacion"]."_prov` TO `apu_mat_".$VAL1."_prov`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-12: ".mysql_error():"");
				
				$query = "RENAME TABLE `par_presupuesto_grupo_".$row["clasificacion"]."` TO `par_presupuesto_grupo_".$VAL1."`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-13: ".mysql_error():"");
				
				$query = "RENAME TABLE `par_presupuesto_subgrupo_".$row["clasificacion"]."` TO `par_presupuesto_subgrupo_".$VAL1."`";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-14: ".mysql_error():"");
				
				$query = "UPDATE `apu_".$VAL1."_movs` SET clasificacion = '".$VAL1."' 
				WHERE clasificacion = '".$row["clasificacion"]."' ";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-15: ".mysql_error():"");
			}
		}
	}
	else
	{
		echo "ERROR";
	}
}
else if (isset($_GET["Parametros_Borrar_Presupuesto_Clasificacion"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "SELECT * FROM apu_clasificacion WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$query = "DROP TABLE IF EXISTS `apu_".$row["clasificacion"]."_movs`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `apu_".$row["clasificacion"]."`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `apu_mo_".$row["clasificacion"]."_movs`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-4: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `apu_mo_".$row["clasificacion"]."`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-5: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `apu_equ_".$row["clasificacion"]."_prov`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-6: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `apu_equ_".$row["clasificacion"]."`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-7: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `apu_mat_".$row["clasificacion"]."_prov`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-8: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `apu_mat_".$row["clasificacion"]."`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-9: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `par_presupuesto_grupo_".$row["clasificacion"]."`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-10: ".mysql_error():"");
		
		$query = "DROP TABLE IF EXISTS `par_presupuesto_subgrupo_".$row["clasificacion"]."`";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-11: ".mysql_error():"");
	
		$query = "DELETE FROM apu_clasificacion WHERE id = '".$ID."' ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-12: ".mysql_error():"");
	}
}
else if (isset($_GET["Parametros_Agregar_Presupuesto_Grupo"]))
{
	$Clasificacion = isset($_GET["Clasificacion"]) ? $_GET["Clasificacion"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	$VAL3 = isset($_GET["VAL3"]) ? $_GET["VAL3"]:die();
	
	$query = "SELECT * FROM `par_presupuesto_grupo_".$Clasificacion."` WHERE codigo = '".$VAL2."' AND categoria = '".$VAL1."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		echo "EXIST";
	}
	else
	{
		$query = "INSERT INTO `par_presupuesto_grupo_".$Clasificacion."` (codigo, grupo, categoria) 
		VALUES ('".$VAL2."', '".$VAL3."', '".$VAL1."') ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	}
}
else if (isset($_GET["Parametros_Actualizar_Presupuesto_Grupo"]))
{
	$Clasificacion = isset($_GET["Clasificacion"]) ? $_GET["Clasificacion"]:die();
	$Categoria = isset($_GET["Categoria"]) ? $_GET["Categoria"]:die();
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "SELECT * FROM `par_presupuesto_grupo_".$Clasificacion."` WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$query = "SELECT * FROM `par_presupuesto_grupo_".$Clasificacion."` WHERE codigo = '".$VAL1."' 
		AND categoria = '".$Categoria."' AND id != '".$ID."'";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			echo "EXIST";
		}
		else
		{
			$query = "UPDATE `par_presupuesto_grupo_".$Clasificacion."` SET 
			codigo = '".$VAL1."', grupo = '".$VAL2."' WHERE id = '".$ID."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
		}
	}
	else
	{
		echo "ERROR";
	}
}
else if (isset($_GET["Parametros_Borrar_Presupuesto_Grupo"]))
{
	$Clasificacion = isset($_GET["Clasificacion"]) ? $_GET["Clasificacion"]:die();
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM `par_presupuesto_grupo_".$Clasificacion."` WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Presupuesto_SubGrupo"]))
{
	$Clasificacion = isset($_GET["Clasificacion"]) ? $_GET["Clasificacion"]:die();
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:die();
	$Categoria = isset($_GET["Categoria"]) ? $_GET["Categoria"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "SELECT * FROM `par_presupuesto_subgrupo_".$Clasificacion."` WHERE codigo = '".$VAL2."' 
	AND grupo = '".$Grupo."' AND categoria = '".$Categoria."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		echo "EXIST";
	}
	else
	{
		$query = "INSERT INTO `par_presupuesto_subgrupo_".$Clasificacion."` (codigo, subgrupo, grupo, categoria) 
		VALUES ('".$VAL1."', '".$VAL2."', '".$Grupo."', '".$Categoria."') ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	}
}
else if (isset($_GET["Parametros_Actualizar_Presupuesto_SubGrupo"]))
{
	$Clasificacion = isset($_GET["Clasificacion"]) ? $_GET["Clasificacion"]:die();
	$Categoria = isset($_GET["Categoria"]) ? $_GET["Categoria"]:die();
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:die();
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	
	$query = "SELECT * FROM `par_presupuesto_subgrupo_".$Clasificacion."` WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$query = "SELECT * FROM `par_presupuesto_subgrupo_".$Clasificacion."` WHERE codigo = '".$VAL1."' 
		AND grupo = '".$Grupo."' AND categoria = '".$Categoria."' AND id != '".$ID."'";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			echo "EXIST";
		}
		else
		{
			$query = "UPDATE `par_presupuesto_subgrupo_".$Clasificacion."` SET 
			codigo = '".$VAL1."', subgrupo = '".$VAL2."' WHERE id = '".$ID."' ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-3: ".mysql_error():"");
		}
	}
	else
	{
		echo "ERROR";
	}
}
else if (isset($_GET["Parametros_Borrar_Presupuesto_SubGrupo"]))
{
	$Clasificacion = isset($_GET["Clasificacion"]) ? $_GET["Clasificacion"]:die();
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM `par_presupuesto_subgrupo_".$Clasificacion."` WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Presupuesto_Unidad"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_presupuesto_unidad (unidad) VALUES ('".$VAL."') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Presupuesto_Unidad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "UPDATE par_presupuesto_unidad SET unidad = '".$VAL."' WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Presupuesto_Unidad"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_presupuesto_unidad WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Agregar_Presupuesto_Concepto"]))
{
	$VAL = isset($_GET["VAL"]) ? $_GET["VAL"]:die();
	
	$query = "INSERT INTO par_presupuesto_conceptos (concepto, uso, tipo) VALUES ('".$VAL."', '100', 'SC') ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Actualizar_Presupuesto_Concepto"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	$VAL1 = isset($_GET["VAL1"]) ? $_GET["VAL1"]:die();
	$VAL2 = isset($_GET["VAL2"]) ? $_GET["VAL2"]:die();
	$VAL3 = isset($_GET["VAL3"]) ? $_GET["VAL3"]:die();
	$VAL4 = isset($_GET["VAL4"]) ? $_GET["VAL4"]:die();
	$VAL5 = isset($_GET["VAL5"]) ? $_GET["VAL5"]:die();
	
	$query = "UPDATE par_presupuesto_conceptos SET 
	concepto = '".$VAL1."', 
	uso = '".$VAL2."', 
	valor = '".$VAL3."', 
	tipo = '".$VAL4."', 
	fijo = '".$VAL5."' 
	WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Parametros_Borrar_Presupuesto_Concepto"]))
{
	$ID = isset($_GET["ID"]) ? $_GET["ID"]:die();
	
	$query = "DELETE FROM par_presupuesto_conceptos WHERE id = '".$ID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
}
else if (isset($_GET["Fix_Existencias"]))
{
	$Affected_Rows = 0;
	
	$query = "SELECT * FROM inventario_movs WHERE motivo = 'Inicial' GROUP BY cod_fab";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
			$Inicial[$row["cod_fab"]] = $row["cantidad"];
	}
	
	$query = "SELECT * FROM inventario_movs ORDER BY cod_fab, fecha ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$CurrentCode = "_empty_";
		$Existencia = 0;
		while($row = mysql_fetch_array($result))
			$rows[] = $row;
		
		for ($i = 0; $i < count($rows); $i++)
		{
			/*if ($CurrentCode === "10000160.5")
			{
				echo "Fecha".$rows[$i]["fecha"]."Existencia".$Existencia."<br />";
			}*/
			
			if (strcmp($CurrentCode, $rows[$i]["cod_fab"]) != 0)
			{
				$Existencia = 0;
				$CurrentCode = $rows[$i]["cod_fab"]."";
				
				if (isset($Inicial[$rows[$i]["cod_fab"]]))
					$Existencia += $Inicial[$rows[$i]["cod_fab"]];
				else if ($rows[$i]["tipo"] == "Entrada")
					$Existencia += $rows[$i]["cantidad"];
				else if ($rows[$i]["tipo"] == "Salida")
					$Existencia -= $rows[$i]["cantidad"];
			}
			else
			{
				if ($rows[$i]["tipo"] == "Entrada" && $rows[$i]["motivo"] != "Inicial")
					$Existencia += $rows[$i]["cantidad"];
				else if ($rows[$i]["tipo"] == "Salida" && $rows[$i]["motivo"] != "Inicial")
					$Existencia -= $rows[$i]["cantidad"];
			}
			
			if (count($rows) - $i == 1)
			{
				$query = "UPDATE productos SET existencia = '".$Existencia."' WHERE cod_fab = '".$CurrentCode."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
				if (mysql_affected_rows() > 0)
					$Affected_Rows++;
			}
			//else if (strval($rows[$i+1]["cod_fab"]) != strval($CurrentCode) && $CurrentCode != "_empty_")
			else if (strcmp($CurrentCode, $rows[$i+1]["cod_fab"]) != 0)
			{
				$query = "UPDATE productos SET existencia = '".$Existencia."' WHERE cod_fab = '".$CurrentCode."'";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				if (mysql_affected_rows() > 0)
					$Affected_Rows++;
			}
		}
		if ($Affected_Rows > 0)
			$ReturnData[0] = array("MESSAGE" => "FIXED");
		else
			$ReturnData[0] = array("MESSAGE" => "OK");
	}
	if (isset($ReturnData))
		echo json_encode($ReturnData);
	else
		echo json_encode(Array());
}
else
{
	print_r($_GET);
	echo "<br/>";
	echo "No Data!";
}

?>