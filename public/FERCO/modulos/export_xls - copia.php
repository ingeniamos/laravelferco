<?php
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

if ($bool === False){
	print "can't find ".$database."";
}

function ClientesNombre()
{
	$query = "SELECT nombre, cliente_id FROM clientes ORDER BY nombre";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	return $ClienteNombre;
}

/*function BuscarClientesDeudores ($ID)
{
	$query = "SELECT DISTINCT cliente_id FROM mov_clientes WHERE estado = 'Aprobado' AND tipo_movimiento = 'Debito' AND saldo > '0'";
	$query .= ($ID != "" && $ID != "\"\"") ? " AND cobrador_codigo = '".$ID."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)	
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				'ClienteID' => $row["cliente_id"],
			);
		}
		return $data;
	}
}*/
function BuscarClientesDeudores ($ID1, $ID2)
{
	global $DEBUG;
	
	$query = "SELECT DISTINCT cliente_id FROM mov_clientes WHERE estado = 'Aprobado' AND tipo_movimiento = 'Debito' AND saldo > '0'";
	$query .= ($ID1 != "" && $ID1 != "\"\"") ? " AND cliente_id = '".$ID1."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)	
	{
		while ($row = mysql_fetch_array($result))
		{
			$Clientes[] = $row["cliente_id"];
		}
	}
	
	if ($ID2 != "" && $ID2 != "\"\"")
	{
		$Clause = implode("', '", $Clientes);
		
		$query = "SELECT cliente_id, cobrador_codigo, fecha, interno FROM mov_clientes WHERE cliente_id IN ('".$Clause."') 
		AND tipo_movimiento = 'Debito' AND estado = 'Aprobado' ORDER BY cliente_id, fecha DESC";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				if (isset($TmpID[$row["cliente_id"]]))
					continue;
				
				$TmpID[$row["cliente_id"]] = 1;
				
				if ($row["cobrador_codigo"] != $ID2)
					continue;
				
				$data[] = array (
					'ClienteID' => $row["cliente_id"],
				);
			}
			return $data;
		}
	}
	else
	{
		for($i = 0; $i < count($Clientes); $i++)
		{
			$data[] = array (
				'ClienteID' => $Clientes[$i],
			);
		}
		return $data;
	}
}

function BuscarClientesAPagar ($ID)
{
	$query = "SELECT DISTINCT cliente_id FROM cxp_movs WHERE estado = 'Aprobado' AND tipo_movimiento = 'Compra' AND saldo > '0'";
	$query .= ($ID != "" && $ID != "\"\"") ? " AND cobrador_codigo = '".$ID."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)	
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				'ClienteID' => $row["cliente_id"],
			);
		}
		return $data;
	}
}

if (isset($_GET['Terceros_Listado']))
{
	$output = "";
	
	// Start Header
	$output .= "ID\t";
	$output .= "Clasificacion\t";
	$output .= "Tipo\t";
	$output .= "Nombre\t";
	$output .= "Direccion\t";
	$output .= "Telefono\t";
	$output .= "Barrio\t";
	$output .= "Ciudad\t";
	$output .= "Departamento\t";
	$output .= "ContactoCP\t";
	$output .= "TelefonoCP\t";
	$output .= "TipoDoc\t";
	$output .= "ClienteID\t";
	$output .= "VendedorID\t";
	$output .= "CobradorID\t";
	$output .= "Terminos\t";
	$output .= "ListaP\t";
	$output .= "CupoCR\t";
	$output .= "Vigencia\t";
	$output .= "Estado\t";
	// End Header
	
	$query = "SELECT cliente_id, clasificacion, tipo FROM cliente_grupo";
	if ( (isset($_GET['Clasificacion']) && $_GET['Clasificacion'] != "") || (isset($_GET['Tipo']) && $_GET['Tipo'] != ""))
		$query .=" WHERE";
	
	$query .= (isset($_GET['Clasificacion']) && $_GET['Clasificacion'] != "") ? " clasificacion = '".$_GET['Clasificacion']."'":"";
	if ( (isset($_GET['Clasificacion']) && $_GET['Clasificacion'] != "") && (isset($_GET['Tipo']) && $_GET['Tipo'] != ""))
		$query .=" AND";
	
	$query .= (isset($_GET['Tipo']) && $_GET['Tipo'] != "") ? " tipo = '".$_GET['Tipo']."'":"";
	
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Clasificacion[$row["cliente_id"]] = $row["clasificacion"];
			$Tipo[$row["cliente_id"]] = $row["tipo"];
		}
	}
	
	$query = "SELECT * FROM clientes WHERE id != '1'";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= (isset($_GET['EstadoC']) && $_GET['EstadoC'] != "") ? " AND estado_cuenta = '".$_GET['EstadoC']."'":"";
	$query .= (isset($_GET['Garantia']) && $_GET['Garantia'] != "") ? " AND garantia = '".$_GET['Garantia']."'":"";
	$query .= "ORDER BY nombre ASC;";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		$output .= "\n";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$schema_insert = "";
			
			if (!isset($Clasificacion[$row["cliente_id"]]))
				continue;
			
			if (!isset($Tipo[$row["cliente_id"]]))
				continue;
			
			$schema_insert .= "".$row["id"]."\t";
			$schema_insert .= "".$Clasificacion[$row["cliente_id"]]."\t";
			$schema_insert .= "".$Tipo[$row["cliente_id"]]."\t";
			$schema_insert .= ($row['nombre'] != "") ? "".$row['nombre']."\t":"NULL\t";
			$schema_insert .= ($row['direccion'] != "") ? "".$row['direccion']."\t":"NULL\t";
			$schema_insert .= ($row['telefono'] != "") ? "".$row['telefono']."\t":"NULL\t";
			$schema_insert .= ($row['barrio'] != "") ? "".$row['barrio']."\t":"NULL\t";
			$schema_insert .= ($row['ciudad'] != "") ? "".$row['ciudad']."\t":"NULL\t";
			$schema_insert .= ($row['departamento'] != "") ? "".$row['departamento']."\t":"NULL\t";
			$schema_insert .= ($row['contacto_p'] != "") ? "".$row['contacto_p']."\t":"NULL\t";
			$schema_insert .= ($row['telefono_cp'] != "") ? "".$row['telefono_cp']."\t":"NULL\t";
			$schema_insert .= ($row['tipo_doc'] != "") ? "".$row['tipo_doc']."\t":"NULL\t";
			$schema_insert .= ($row['cliente_id'] != "") ? "".$row['cliente_id']."\t":"NULL\t";
			$schema_insert .= ($row['vendedor_codigo'] != "") ? "".$row['vendedor_codigo']."\t":"NULL\t";
			$schema_insert .= ($row['cobrador_codigo'] != "") ? "".$row['cobrador_codigo']."\t":"NULL\t";
			$schema_insert .= ($row['terminos'] != "") ? "".$row['terminos']."\t":"NULL\t";
			$schema_insert .= ($row['lista_precio'] != "") ? "".$row['lista_precio']."\t":"NULL\t";
			$schema_insert .= ($row['credito'] != "") ? "".$row['credito']."\t":"NULL\t";
			$schema_insert .= ($row['vigencia_credito'] != "") ? "".$row['vigencia_credito']."\t":"NULL\t";
			$schema_insert .= ($row['estado_cuenta'] != "") ? "".$row['estado_cuenta']."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Terceros_Listado.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
	//echo $output;
}
else if (isset($_GET['Ventas_Listado']))
{
	$output = "";
	
	// Start Header
	$output .= "Fecha\t";
	$output .= "Ord_Compra\t";
	$output .= "Factura\t";
	$output .= "Valor\t";
	$output .= "Cliente\t";
	$output .= "ClienteID\t";
	$output .= "VendedorID\t";
	$output .= "\n";
	//$output .= "Interno\t";
	//$output .= "Estado\t";
	//$output .= "Motivo_Anulado\t";
	//$output .= "Remision\t";
	//$output .= "Ord_Produccion\t";
	//$output .= "F_Pago\t";
	//$output .= "Enviado\t";
	//$output .= "CobradorID\t";
	//$output .= "DigitadorID\t";
	//$output .= "AutorizadorID\t";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM fact_final WHERE DATE(fecha_remision) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Ord_Compra']) && $_GET['Ord_Compra'] != "") ? " AND orden_compra LIKE '%".$_GET['Ord_Compra']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['Ord_Produccion']) && $_GET['Ord_Produccion'] != "") ? " AND orden_produccion LIKE '%".$_GET['Ord_Produccion']."%'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND digitado_por = '".$_GET['VendedorID']."'":"";
	$query .= ";";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$schema_insert = "";
			//
			$schema_insert .= ($row["fecha_remision"] != "") ? "".$row["fecha_remision"]."\t":"NULL\t";
			$schema_insert .= ($row["orden_compra"] != "") ? "".$row["orden_compra"]."\t":"NULL\t";
			$schema_insert .= ($row['factura'] != "") ? "".$row['factura']."\t":"NULL\t";
			$schema_insert .= ($row['total'] != "") ? "".$row['total']."\t":"NULL\t";
			$schema_insert .= isset($ClienteNombre[$row['cliente_id']]) ? "".$ClienteNombre[$row['cliente_id']]."\t":"NULL\t";
			$schema_insert .= ($row['cliente_id'] != "") ? "".$row['cliente_id']."\t":"NULL\t";
			$schema_insert .= ($row['vendedor_codigo'] != "") ? "".$row['vendedor_codigo']."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Ventas_Listado.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Compras_Listado']))
{
	$output = "";
	
	// Start Header
	$output .= "Fecha\t";
	$output .= "Entrada\t";
	$output .= "Factura\t";
	$output .= "Valor\t";
	$output .= "Cliente\t";
	$output .= "ClienteID\t";
	$output .= "DigitadorID\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Creado";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM compras_final WHERE estado = '".$Estado."' AND DATE(fecha_compra) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= isset($_GET['ClienteID']) ? " AND compras_final.cliente_id LIKE '%".$_GET['ClienteID']."%'":"";
	$query .= isset($_GET['Entrada']) ? " AND entrada LIKE '%".$_GET['Entrada']."%'":"";
	$query .= isset($_GET['DigitadorID']) ? " AND compras_final.digitado_por LIKE '%".$_GET['DigitadorID']."%'":"";
	$query .= ";";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0)
	{	
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$schema_insert = "";
			//
			$schema_insert .= ($row["fecha_compra"] != "") ? "".$row["fecha_compra"]."\t":"NULL\t";
			$schema_insert .= ($row["entrada"] != "") ? "".$row["entrada"]."\t":"NULL\t";
			$schema_insert .= ($row['factura'] != "") ? "".$row['factura']."\t":"NULL\t";
			$schema_insert .= ($row['total'] != "") ? "".$row['total']."\t":"NULL\t";
			$schema_insert .= isset($ClienteNombre[$row['cliente_id']]) ? "".$ClienteNombre[$row['cliente_id']]."\t":"NULL\t";
			$schema_insert .= ($row['cliente_id'] != "") ? "".$row['cliente_id']."\t":"NULL\t";
			$schema_insert .= ($row['digitado_por'] != "") ? "".$row['digitado_por']."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Compras_Listado.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Inventario_Listado']))
{
	$output = "";
	
	// Start Header
	$output .= "Cod_Fab\t";
	$output .= "Producto\t";
	$output .= "Und\t";
	$output .= "Peso\t";
	if (isset($_GET['Costo']))
	{
		$output .= "Costo\t";
		$output .= "Ult_Costo\t";
	}
	$output .= "Lista1\t";
	$output .= "Lista2\t";
	$output .= "Lista3\t";
	$output .= "Lista4\t";
	$output .= "\n";
	// End Header
	
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	
	$query = "SELECT * FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
	AND subgrupo LIKE '%".$SubGrupo."%' AND cod_fab LIKE '%".$Codigo."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{	
			$schema_insert = "";
			//
			$schema_insert .= ($row["cod_fab"] != "") ? "".$row["cod_fab"]."\t":"NULL\t";
			$schema_insert .= ($row["nombre"] != "") ? "".$row["nombre"]."\t":"NULL\t";
			$schema_insert .= ($row['und_med'] != "") ? "".$row['und_med']."\t":"NULL\t";
			$schema_insert .= ($row['peso'] != "") ? "".$row['peso']."\t":"NULL\t";
			if (isset($_GET['Costo']))
			{
				$schema_insert .= ($row['costo'] != "") ? "".$row['costo']."\t":"NULL\t";
				$schema_insert .= ($row['ultimo_costo'] != "") ? "".$row['ultimo_costo']."\t":"NULL\t";
			}
			$schema_insert .= ($row['lista1'] != "") ? "".$row['lista1']."\t":"NULL\t";
			$schema_insert .= ($row['lista2'] != "") ? "".$row['lista2']."\t":"NULL\t";
			$schema_insert .= ($row['lista3'] != "") ? "".$row['lista3']."\t":"NULL\t";
			$schema_insert .= ($row['lista4'] != "") ? "".$row['lista4']."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Inventario_Listado.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Inventario_Existencias']))
{
	$output = "";
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	
	switch($OrderBy)
	{
		case "Categoria":
			$query = "SELECT categoria, SUM(costo) AS costo, SUM(ultimo_costo) AS ultimo_costo, SUM(costo_promedio) AS costo_promedio, 
			SUM(lista1) AS lista1, SUM(lista2) AS lista2, SUM(lista3) AS lista3, SUM(lista4) AS lista4 FROM productos GROUP BY categoria";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				// Start Header
				$output .= "Categoria\t";
				$output .= "Costo\t";
				$output .= "Ult_Costo\t";
				$output .= "Costo_Prom\t";
				$output .= "Lista1\t";
				$output .= "Lista2\t";
				$output .= "Lista3\t";
				$output .= "Lista4\t";
				$output .= "\n";
				// End Header
				while ($row = mysql_fetch_array($result))
				{
					$schema_insert = "";
					//
					$schema_insert .= "".$row["categoria"]."\t";
					$schema_insert .= "".$row["costo"]."\t";
					$schema_insert .= "".$row["ultimo_costo"]."\t";
					$schema_insert .= "".$row["costo_promedio"]."\t";
					$schema_insert .= "".$row["lista1"]."\t";
					$schema_insert .= "".$row["lista2"]."\t";
					$schema_insert .= "".$row["lista3"]."\t";
					$schema_insert .= "".$row["lista4"]."\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
				$filename = "Inventario_Existencias_Categoria.xls";
			}
		break;
		case "Grupo":
			$query = "SELECT categoria, grupo, SUM(costo) AS costo, SUM(ultimo_costo) AS ultimo_costo, 
			SUM(costo_promedio) AS costo_promedio, SUM(lista1) AS lista1, SUM(lista2) AS lista2, SUM(lista3) AS lista3, 
			SUM(lista4) AS lista4 FROM productos GROUP BY grupo ORDER BY categoria";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				// Start Header
				$output .= "Categoria\t";
				$output .= "Grupo\t";
				$output .= "Costo\t";
				$output .= "Ult_Costo\t";
				$output .= "Costo_Prom\t";
				$output .= "Lista1\t";
				$output .= "Lista2\t";
				$output .= "Lista3\t";
				$output .= "Lista4\t";
				$output .= "\n";
				// End Header
				while ($row = mysql_fetch_array($result))
				{
					$schema_insert = "";
					//
					$schema_insert .= "".$row["categoria"]."\t";
					$schema_insert .= "".$row["grupo"]."\t";
					$schema_insert .= "".$row["costo"]."\t";
					$schema_insert .= "".$row["ultimo_costo"]."\t";
					$schema_insert .= "".$row["costo_promedio"]."\t";
					$schema_insert .= "".$row["lista1"]."\t";
					$schema_insert .= "".$row["lista2"]."\t";
					$schema_insert .= "".$row["lista3"]."\t";
					$schema_insert .= "".$row["lista4"]."\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
				$filename = "Inventario_Existencias_Grupo.xls";
			}
		break;
		case "SubGrupo":
			$query = "SELECT categoria, grupo, subgrupo, SUM(costo) AS costo, SUM(ultimo_costo) AS ultimo_costo, 
			SUM(costo_promedio) AS costo_promedio, SUM(lista1) AS lista1, SUM(lista2) AS lista2, 
			SUM(lista3) AS lista3, SUM(lista4) AS lista4 FROM productos GROUP BY subgrupo ORDER BY categoria";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				// Start Header
				$output .= "Categoria\t";
				$output .= "Grupo\t";
				$output .= "SubGrupo\t";
				$output .= "Costo\t";
				$output .= "Ult_Costo\t";
				$output .= "Costo_Prom\t";
				$output .= "Lista1\t";
				$output .= "Lista2\t";
				$output .= "Lista3\t";
				$output .= "Lista4\t";
				$output .= "\n";
				// End Header
				while ($row = mysql_fetch_array($result))
				{
					$schema_insert = "";
					//
					$schema_insert .= "".$row["categoria"]."\t";
					$schema_insert .= "".$row["grupo"]."\t";
					$schema_insert .= "".$row["subgrupo"]."\t";
					$schema_insert .= "".$row["costo"]."\t";
					$schema_insert .= "".$row["ultimo_costo"]."\t";
					$schema_insert .= "".$row["costo_promedio"]."\t";
					$schema_insert .= "".$row["lista1"]."\t";
					$schema_insert .= "".$row["lista2"]."\t";
					$schema_insert .= "".$row["lista3"]."\t";
					$schema_insert .= "".$row["lista4"]."\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
				$filename = "Inventario_Existencias_SubGrupo.xls";
			}
		break;
		default:
			$query = "SELECT * FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
			AND subgrupo LIKE '%".$SubGrupo."%' AND cod_fab != '' ORDER BY categoria";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				// Start Header
				$output .= "Cod_Fab\t";
				$output .= "Nombre\t";
				$output .= "Categoria\t";
				$output .= "Costo\t";
				$output .= "Ult_Costo\t";
				$output .= "Costo_Prom\t";
				$output .= "Lista1\t";
				$output .= "Lista2\t";
				$output .= "Lista3\t";
				$output .= "Lista4\t";
				$output .= "Existencia\t";
				$output .= "\n";
				// End Header
				while ($row = mysql_fetch_array($result))
				{
					$schema_insert = "";
					//
					$schema_insert .= "".$row["cod_fab"]."\t";
					$schema_insert .= "".$row["nombre"]."\t";
					$schema_insert .= "".$row["categoria"]."\t";
					$schema_insert .= "".$row["costo"]."\t";
					$schema_insert .= "".$row["ultimo_costo"]."\t";
					$schema_insert .= "".$row["costo_promedio"]."\t";
					$schema_insert .= "".$row["lista1"]."\t";
					$schema_insert .= "".$row["lista2"]."\t";
					$schema_insert .= "".$row["lista3"]."\t";
					$schema_insert .= "".$row["lista4"]."\t";
					$schema_insert .= "".$row["existencia"]."\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
				$filename = "Inventario_Existencias.xls";
			}
		break;
	}
	
	// Download the file
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Inventario_Recientes']))
{
	$output = "";
	// Start Header
	$output .= "Fecha\t";
	$output .= "Cod_Fab\t";
	$output .= "Nombre\t";
	$output .= "Cantidad\t";
	$output .= "Tipo\t";
	$output .= "Motivo\t";
	$output .= "Cliente\t";
	$output .= "Factura\t";
	$output .= "Interno\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$Tipo = isset($_GET['Tipo']) ? $_GET['Tipo']:"";
	$Motivo = isset($_GET['Motivo']) ? $_GET['Motivo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT cod_fab, nombre FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
	AND subgrupo LIKE '%".$SubGrupo."%' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Codigo[] = $row["cod_fab"];
			$ProductoNombre[$row["cod_fab"]] = $row["nombre"];
		}
	}
	
	$Clause = implode("', '", $Codigo);
	
	$query = "SELECT * FROM inventario_movs WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'
	AND tipo LIKE '%".$Tipo."%' AND motivo LIKE '%".$Motivo."%' AND cod_fab IN ('".$Clause."') ORDER BY fecha DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (stristr($row["interno"], "FER") != false)
				$Interno[] = $row["interno"];
			else if (stristr($row["interno"], "COMP") != false)
				$Compra[] = $row["interno"];
			else if (stristr($row["interno"], "OP") != false)
				$Ord_Produccion[] = $row["interno"];
			else
				$Inventario[] = $row["interno"];
		}
		
		if (isset($Interno))
		{
			$Clause1 = implode("', '", $Interno);
			$query = "SELECT interno, factura, cliente_id FROM fact_final WHERE interno IN ('".$Clause1."')";
			$result2 = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());
			
			if (mysql_num_rows($result2) > 0)
			{
				while ($row2 = mysql_fetch_array($result2))
				{
					$Facura[$row2["interno"]] = $row2["factura"];
					$ClienteID[$row2["interno"]] = $row2["cliente_id"];
				}
			}
		}
		
		if (isset($Compra))
		{
			$Clause2 = implode("', '", $Compra);
			$query = "SELECT interno, factura, cliente_id FROM compras_final WHERE interno IN ('".$Clause2."')";
			$result3 = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());
			
			if (mysql_num_rows($result3) > 0)
			{
				while ($row3 = mysql_fetch_array($result3))
				{
					$Facura[$row3["interno"]] = $row3["factura"];
					$ClienteID[$row3["interno"]] = $row3["cliente_id"];
				}
			}
		}
		
		if (isset($Ord_Produccion))
		{
			$Clause3 = implode("', '", $Ord_Produccion);
			$query = "SELECT orden_produccion, cliente_id FROM produccion_final WHERE orden_produccion IN ('".$Clause3."')";
			$result4 = mysql_query($query) or die("SQL Error 3-3: " . mysql_error());
			
			if (mysql_num_rows($result4) > 0)
			{
				while ($row4 = mysql_fetch_array($result4))
				{
					$Facura[$row4["orden_produccion"]] = "";
					$ClienteID[$row4["orden_produccion"]] = $row4["cliente_id"];
				}
			}
		}
		
		while ($row = mysql_fetch_array($result1))
		{
			$schema_insert = "";
			
			if ($row["interno"] == "")
			{
				$NewFactura = "";
				$NewClienteID = "";
			}
			else
			{
				$NewFactura = isset($Facura[$row["interno"]]) ? $Facura[$row["interno"]]:"";
				$NewClienteID = isset($ClienteID[$row["interno"]]) ? $ClienteID[$row["interno"]]:"";
			}
			
			$schema_insert .= "".$row["fecha"]."\t";
			$schema_insert .= "".$row["cod_fab"]."\t";
			$schema_insert .= "".$ProductoNombre[$row["cod_fab"]]."\t";
			$schema_insert .= "".$row["cantidad"]."\t";
			$schema_insert .= "".$row["tipo"]."\t";
			$schema_insert .= "".$row["motivo"]."\t";
			//$schema_insert .= "".$NewClienteID."\t";
			$schema_insert .= isset($ClienteNombre[$NewClienteID]) ? "".$ClienteNombre[$NewClienteID]."\t":"NULL\t";
			$schema_insert .= ($NewFactura != "") ? "".$NewFactura."\t":"NULL\t";
			$schema_insert .= ($row["interno"] != "") ? "".$row["interno"]."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Inventario_Recientes.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Inventario_Ajustes']))
{
	$output = "";
	// Start Header
	$output .= "Fecha\t";
	$output .= "Cod_Fab\t";
	$output .= "Nombre\t";
	$output .= "Cantidad\t";
	$output .= "Motivo\t";
	$output .= "\n";
	// End Header
	
	$CodFab = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	if ($CodFab == "")
	{
		$query = "SELECT * FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
		AND subgrupo LIKE '%".$SubGrupo."%'";
	}
	else
		$query = "SELECT * FROM productos WHERE cod_fab = '".$CodFab."'";
	
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Codigo[] = $row["cod_fab"];
			$Nombre[$row["cod_fab"]] = $row["nombre"];
		}
		
		$Clause = implode("', '", $Codigo);
		$query = "SELECT * FROM inventario_movs WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' 
		AND cod_fab IN ('".$Clause."') AND motivo LIKE 'Ajust%' ORDER BY fecha DESC";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			while ($row = mysql_fetch_array($result1))
			{
				$schema_insert = "";
				//
				$schema_insert .= ($row["fecha"] != "") ? "".$row["fecha"]."\t":"NULL\t";
				$schema_insert .= ($row["cod_fab"] != "") ? "".$row["cod_fab"]."\t":"NULL\t";
				$schema_insert .= ($Nombre[$row["cod_fab"]] != "") ? "".$Nombre[$row["cod_fab"]]."\t":"NULL\t";
				$schema_insert .= ($row['cantidad'] != "") ? "".$row['cantidad']."\t":"NULL\t";
				$schema_insert .= ($row['observacion'] != "") ? "".$row['observacion']."\t":"NULL\t";
				//
				$schema_insert = str_replace("\t"."$", "", $schema_insert);
				$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
				$schema_insert .= "\t";
				$output .= trim($schema_insert);
				$output .= "\n"; 
			}
		}
	}
	// Download the file
	$filename = "Inventario_Ajustes.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1//IGNORE", $output);// excell charset
}
else if (isset($_GET['Cartera_Movimientos']))
{
	$output = "";
	
	// Start Header
	$output .= "Fecha\t";
	$output .= "Interno\t";
	$output .= "Ord_Compra\t";
	$output .= "Factura\t";
	$output .= "Caja_Recibo\t";
	$output .= "TipoMovimiento\t";
	$output .= "Valor\t";
	$output .= "Saldo\t";
	$output .= "Cliente\t";
	$output .= "ClienteID\t";
	$output .= "CobradorID\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM mov_clientes WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['TipoMovimiento']) && $_GET['TipoMovimiento'] != "") ? " AND tipo_movimiento = '".$_GET['TipoMovimiento']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['Caja_Recibo']) && $_GET['Caja_Recibo'] != "") ? " AND caja_recibo LIKE '%".$_GET['Caja_Recibo']."%'":"";
	$query .= "";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$schema_insert = "";
			//
			$schema_insert .= ($row["fecha"] != "") ? "".$row["fecha"]."\t":"NULL\t";
			$schema_insert .= ($row["interno"] != "") ? "".$row["interno"]."\t":"NULL\t";
			$schema_insert .= ($row["orden_compra"] != "") ? "".$row["orden_compra"]."\t":"NULL\t";
			$schema_insert .= ($row['factura'] != "") ? "".$row['factura']."\t":"NULL\t";
			$schema_insert .= ($row['caja_recibo'] != "") ? "'".$row['caja_recibo']."\t":"NULL\t";
			$schema_insert .= ($row['tipo_movimiento'] != "") ? "".$row['tipo_movimiento']."\t":"NULL\t";
			$schema_insert .= ($row['valor'] != "") ? "".$row['valor']."\t":"NULL\t";
			$schema_insert .= ($row['saldo'] != "") ? "".$row['saldo']."\t":"NULL\t";
			$schema_insert .= isset($ClienteNombre[$row['cliente_id']]) ? "".$ClienteNombre[$row['cliente_id']]."\t":"NULL\t";
			$schema_insert .= ($row['cliente_id'] != "") ? "".$row['cliente_id']."\t":"NULL\t";
			$schema_insert .= ($row['cobrador_codigo'] != "") ? "".$row['cobrador_codigo']."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Cartera_Movimientos.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['CxP_Movimientos']))
{
	$output = "";
	
	// Start Header
	$output .= "Fecha\t";
	$output .= "Compra_Interno\t";
	$output .= "Compra_Entrada\t";
	$output .= "Factura\t";
	$output .= "TipoMovimiento\t";
	$output .= "Valor\t";
	$output .= "Saldo\t";
	$output .= "Cliente\t";
	$output .= "ClienteID\t";
	$output .= "Caja_Interno\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT *, SUBSTR(fecha_digitado, 1, 10) AS fecha_digitado FROM cxp_movs WHERE DATE(fecha_digitado) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['TipoMovimiento']) && $_GET['TipoMovimiento'] != "") ? " AND tipo_movimiento = '".$_GET['TipoMovimiento']."'":"";
	$query .= (isset($_GET['Compra_Interno']) && $_GET['Compra_Interno'] != "") ? " AND compra_interno = '".$_GET['Compra_Interno']."'":"";
	$query .= (isset($_GET['Caja_Interno']) && $_GET['Caja_Interno'] != "") ? " AND caja_interno = '".$_GET['Caja_Interno']."'":"";
	$query .= (isset($_GET['Compra_Entrada']) && $_GET['Compra_Entrada'] != "") ? " AND compra_entrada = '".$_GET['Compra_Entrada']."'":"";
	$query .= ";";
	//echo "".$query."<br/><br/>";
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$schema_insert = "";
			//
			$schema_insert .= ($row["fecha_digitado"] != "") ? "".$row["fecha_digitado"]."\t":"NULL\t";
			$schema_insert .= ($row["compra_interno"] != "") ? "".$row["compra_interno"]."\t":"NULL\t";
			$schema_insert .= ($row['compra_entrada'] != "") ? "".$row['compra_entrada']."\t":"NULL\t";
			$schema_insert .= ($row['factura'] != "") ? "".$row['factura']."\t":"NULL\t";
			$schema_insert .= ($row['tipo_movimiento'] != "") ? "".$row['tipo_movimiento']."\t":"NULL\t";
			$schema_insert .= ($row['valor'] != "") ? "".$row['valor']."\t":"NULL\t";
			$schema_insert .= ($row['saldo'] != "") ? "".$row['saldo']."\t":"NULL\t";
			$schema_insert .= isset($ClienteNombre[$row['cliente_id']]) ? "".$ClienteNombre[$row['cliente_id']]."\t":"NULL\t";
			$schema_insert .= ($row['cliente_id'] != "") ? "".$row['cliente_id']."\t":"NULL\t";
			$schema_insert .= ($row['caja_interno'] != "") ? "".$row['caja_interno']."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "CxP_Movimientos.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['CxP_Listado']))
{
	$output = "";
	
	// Start Header
	$output .= "Cliente\t";
	$output .= "ClienteID\t";
	$output .= "Deuda\t";
	$output .= "Compra\t";
	$output .= "Fecha\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$data = BuscarClientesAPagar("");
	$num = count($data);
	
	if ($num > 0)
	{
		if ($ClienteID == "")
		{
			foreach($data as $item)
			{
				$Clientes[] = $item["ClienteID"];
			}
		}
		else
			$Clientes[] = $ClienteID;
		
		$Clause = implode("', '", $Clientes);
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Compra' AND estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Deuda[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(valor) AS valor FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Compra' AND estado = 'Aprobado' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Compra[$row["cliente_id"]] = $row["valor"];
			}
		}
		
		$query = "SELECT cliente_id, MAX(fecha_digitado) AS fecha FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Compra' AND estado = 'Aprobado' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Fecha[$row["cliente_id"]] = $row["fecha"];
			}
		}
		
		foreach($data as $item)
		{
			if ($ClienteID != "" && $item["ClienteID"] != $ClienteID)
				continue;
			
			$schema_insert = "";
			//
			$schema_insert .= isset($ClienteNombre[$item["ClienteID"]]) ? "".$ClienteNombre[$item["ClienteID"]]."\t":"NULL\t";
			$schema_insert .= "".$item["ClienteID"]."\t";
			$schema_insert .= "".$Deuda[$item["ClienteID"]]."\t";
			$schema_insert .= "".$Compra[$item["ClienteID"]]."\t";
			$schema_insert .= "".$Fecha[$item["ClienteID"]]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "CxP_Listado.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['CxP_Edades']))
{
	$output = "";
	
	// Start Header
	$output .= "Cliente\t";
	$output .= "ClienteID\t";
	$output .= "Deuda\t";
	$output .= "A30\t";
	$output .= "A60\t";
	$output .= "A90\t";
	$output .= "Mas90\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$Today = date('Y-m-d');
	$Until30 = date('Y-m-d', strtotime('-30 days'));
	$Until60 = date('Y-m-d', strtotime('-60 days'));
	$Until90 = date('Y-m-d', strtotime('-90 days'));
	
	$data = BuscarClientesAPagar("");
	$num = count($data);
	
	if ($num > 0) {
		foreach($data as $item)
		{
			$Clientes[] = $item["ClienteID"];
		}
		
		$Clause = implode("', '", $Clientes);
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Compra' AND estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Deuda[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha_digitado) BETWEEN '".$Until30."' AND '".$Today."' AND tipo_movimiento = 'Compra' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A30[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha_digitado) BETWEEN '".$Until60."' AND '".$Until30."' AND tipo_movimiento = 'Compra' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A60[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha_digitado) BETWEEN '".$Until90."' AND '".$Until60."' AND tipo_movimiento = 'Compra' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A90[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha_digitado) BETWEEN '0000-00-00' AND '".$Until90."' AND tipo_movimiento = 'Compra' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Mas90[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		foreach($data as $item)
		{
			$schema_insert = "";
			//
			$schema_insert .= isset($ClienteNombre[$item["ClienteID"]]) ? "".$ClienteNombre[$item["ClienteID"]]."\t":"NULL\t";
			$schema_insert .= "".$item["ClienteID"]."\t";
			$schema_insert .= "".$Deuda[$item["ClienteID"]]."\t";
			$schema_insert .= isset($A30[$item["ClienteID"]]) ? "".$A30[$item["ClienteID"]]."\t":"0\t";
			$schema_insert .= isset($A60[$item["ClienteID"]]) ? "".$A60[$item["ClienteID"]]."\t":"0\t";
			$schema_insert .= isset($A90[$item["ClienteID"]]) ? "".$A90[$item["ClienteID"]]."\t":"0\t";
			$schema_insert .= isset($Mas90[$item["ClienteID"]]) ? "".$Mas90[$item["ClienteID"]]."\t":"0\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "CxP_Edades.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Cartera_Listado']))
{
	$output = "";
	
	// Start Header
	$output .= "Cliente\t";
	$output .= "ClienteID\t";
	$output .= "Deuda\t";
	$output .= "Compra\t";
	$output .= "Fecha\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$data = BuscarClientesDeudores("", "");
	$num = count($data);
	
	if ($num > 0) {
		foreach($data as $item)
		{
			$Clientes[] = $item["ClienteID"];
		}
		
		$Clause = implode("', '", $Clientes);
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Debito' AND estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Deuda[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(valor) AS valor FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Debito' AND estado = 'Aprobado' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Compra[$row["cliente_id"]] = $row["valor"];
			}
		}
		
		$query = "SELECT cliente_id, MAX(fecha) AS fecha FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Debito' AND estado = 'Aprobado' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Fecha[$row["cliente_id"]] = $row["fecha"];
			}
		}
		
		foreach($data as $item)
		{
			$schema_insert = "";
			//
			$schema_insert .= isset($ClienteNombre[$item["ClienteID"]]) ? "".$ClienteNombre[$item["ClienteID"]]."\t":"NULL\t";
			$schema_insert .= "".$item["ClienteID"]."\t";
			$schema_insert .= "".$Deuda[$item["ClienteID"]]."\t";
			$schema_insert .= "".$Compra[$item["ClienteID"]]."\t";
			$schema_insert .= "".$Fecha[$item["ClienteID"]]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Cartera_Listado.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Caja_Aprobar']))
{
	$output = "";
	
	// Start Header
	$output .= "Caja Aprobar/Listado\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Beneficiario\t";
	$output .= "Fecha\t";
	$output .= "Grupo\t";
	$output .= "SubGrupo\t";
	$output .= "R. Caja\t";
	$output .= "Valor\t";
	$output .= "DigitadoPor\t";
	$output .= "AprobadoPor\t";
	$output .= "\n";
	// End Header
	
	$i = 0;
	$LastID = "_empty_";
	$TOTAL = 0;
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM caja_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Caja_Recibo']) && $_GET['Caja_Recibo'] != "") ? " AND caja_recibo = '".$_GET['Caja_Recibo']."'":"";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND digitado_por = '".$_GET['DigitadorID']."'":"";
	$query .= "ORDER BY cliente_id;";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($LastID != $row["cliente_id"])
			{
				if ($i > 0)
				{
					$schema_insert = "";
					//
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "".$TOTAL."\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
				//---
				$schema_insert = "";
				//
				$schema_insert .= "".$ClienteNombre[$row["cliente_id"]]."\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				//
				$schema_insert = str_replace("\t"."$", "", $schema_insert);
				$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
				$schema_insert .= "\t";
				$output .= trim($schema_insert);
				$output .= "\n";
				//---
				$LastID = $row["cliente_id"];
				$TOTAL = $row["total"];
			}
			else
				$TOTAL += $row["total"];
			
			$schema_insert = "";
			//
			$schema_insert .= "NULL\t";
			$schema_insert .= "".$row["fecha"]."\t";
			$schema_insert .= "".$row["grupo"]."\t";
			$schema_insert .= "".$row["subgrupo"]."\t";
			$schema_insert .= "".$row["caja_recibo"]."\t";
			$schema_insert .= "".$row["total"]."\t";
			$schema_insert .= "".$row["digitado_por"]."\t";
			$schema_insert .= "".$row["aprobado_por"]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
			$i++;
		}
	}
	// Download the file
	$filename = "Caja_Aprobar.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Produccion_Procesos']))
{
	$output = "";
	
	// Start Header
	$output .= "Produccion Procesos\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Operario\t";
	$output .= "Proceso\t";
	$output .= "Fecha\t";
	$output .= "Ord_Produccion\t";
	$output .= "Cliente\t";
	$output .= "Producto\t";
	$output .= "Peso\t";
	$output .= "Maquinaria\t";
	$output .= "DigitadoPor\t";
	$output .= "Estado\t";
	$output .= "\n";
	// End Header
	
	$i = 0;
	$LastID = "_empty_";
	$TOTAL = 0;
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT cod_fab, nombre, peso FROM productos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoNombre[$row["cod_fab"]] = $row["nombre"];
			$ProductoPeso[$row["cod_fab"]] = $row["peso"];
		}
	}
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT orden_produccion, estado, proceso, avance, SUBSTR(fecha_ini, 1, 10) AS fecha FROM produccion_proc
	WHERE DATE(fecha_ini) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if(!isset($Old_OrD_Produccion[$row["orden_produccion"]]))
			{
				$Old_OrD_Produccion[$row["orden_produccion"]] = 1;
				$Ord_Produccion[] = $row["orden_produccion"];
			}
			$ProduccionFecha[$row["orden_produccion"]] = $row["fecha"];
			$ProduccionEstado[$row["orden_produccion"]] = $row["estado"];
			$ProduccionProceso[$row["orden_produccion"]] = $row["proceso"];
		}
	}
	
	$Clause1 = implode("', '", $Ord_Produccion);
	
	$query = "SELECT orden_produccion, interno, digitado_por FROM produccion_final 
	WHERE orden_produccion IN ('".$Clause1."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProduccionInterno[$row["orden_produccion"]] = $row["interno"];
			$ProduccionDigitadoPor[$row["orden_produccion"]] = $row["digitado_por"];
		}
	}
	
	if (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "")	
	{
		$query = "SELECT * FROM produccion_proc_movs WHERE orden_produccion IN ('".$Clause1."') AND operario = '".$_GET['ClienteID']."'";
	}
	else
	{
		$query = "SELECT DISTINCT operario FROM produccion_proc_movs WHERE orden_produccion IN ('".$Clause1."') AND operario != ''";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Operarios[] = $row["operario"];
			}
		}
		$Clause2 = implode("', '", $Operarios);
		
		$query = "SELECT * FROM produccion_proc_movs WHERE orden_produccion IN ('".$Clause1."') 
		AND operario IN ('".$Clause2."') ORDER BY operario";
	}
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if (!isset($ClienteNombre[$row["operario"]]))
				continue;
			
			if ($LastID != $row["operario"])
			{
				if ($i > 0)
				{
					$schema_insert = "";
					//
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "".$TOTAL."\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					$schema_insert .= "NULL\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
				//---
				$schema_insert = "";
				//
				$schema_insert .= "".$ClienteNombre[$row["operario"]]."\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				$schema_insert .= "NULL\t";
				//
				$schema_insert = str_replace("\t"."$", "", $schema_insert);
				$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
				$schema_insert .= "\t";
				$output .= trim($schema_insert);
				$output .= "\n";
				//---
				$LastID = $row["operario"];
				$TOTAL = 0;
			}
			
			if (isset($ProductoPeso[$row["codigo"]]))
			{
				$Peso = $row["cantidad"] * $ProductoPeso[$row["codigo"]];
				$TOTAL += $row["cantidad"] * $ProductoPeso[$row["codigo"]];
			}
			else
				$Peso = 0;
				
			$schema_insert = "";
			//
			$schema_insert .= "NULL\t";
			//$schema_insert .= "".$row["origen"]."\t";
			$schema_insert .= "".$ProduccionProceso[$row["orden_produccion"]]."\t";
			$schema_insert .= "".$ProduccionFecha[$row["orden_produccion"]]."\t";
			$schema_insert .= "".$row["orden_produccion"]."\t";
			$schema_insert .= isset($ClienteNombre[$row["cliente_id"]]) ? "".$ClienteNombre[$row["cliente_id"]]."\t":"NULL\t";
			$schema_insert .= isset($ProductoNombre[$row["codigo"]]) ? "".$ProductoNombre[$row["codigo"]]."\t":"NULL\t";
			$schema_insert .= "".$Peso."\t";
			$schema_insert .= "".$row["maquinaria"]."\t";
			$schema_insert .= "".$ProduccionDigitadoPor[$row["orden_produccion"]]."\t";
			$schema_insert .= "".$ProduccionEstado[$row["orden_produccion"]]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
			$i++;
		}
		//echo $output;
	}
	// Download the file
	$filename = "Produccion_Procesos.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Reportes_Cartera']))
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Cartera\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Fecha\t";
	$output .= "Interno\t";
	$output .= "Factura\t";
	$output .= "Cliente\t";
	$output .= "ClienteID\t";
	$output .= "TipoMovimiento\t";
	$output .= "Valor\t";
	$output .= "Saldo\t";
	$output .= "Caja_Interno\t";
	$output .= "VendedorID\t";
	$output .= "CobradorID\t";
	$output .= "DigitadorID\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM mov_clientes WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado'";
	$query .= (isset($_GET['TipoMovimiento']) && $_GET['TipoMovimiento'] != "") ? " AND tipo_movimiento = '".$_GET['TipoMovimiento']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['Caja_Interno']) && $_GET['Caja_Interno'] != "") ? " AND caja_interno LIKE '%".$_GET['Caja_Interno']."%'":"";
	$query .= "ORDER BY fecha DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$schema_insert = "";
			//
			$schema_insert .= ($row["fecha"] != "") ? "".$row["fecha"]."\t":"NULL\t";
			$schema_insert .= ($row["interno"] != "") ? "".$row["interno"]."\t":"NULL\t";
			$schema_insert .= ($row['factura'] != "") ? "".$row['factura']."\t":"NULL\t";
			$schema_insert .= isset($ClienteNombre[$row['cliente_id']]) ? "".$ClienteNombre[$row['cliente_id']]."\t":"NULL\t";
			$schema_insert .= ($row['cliente_id'] != "") ? "".$row['cliente_id']."\t":"NULL\t";
			$schema_insert .= ($row['tipo_movimiento'] != "") ? "".$row['tipo_movimiento']."\t":"NULL\t";
			$schema_insert .= ($row['valor'] != "") ? "".$row['valor']."\t":"NULL\t";
			$schema_insert .= ($row['saldo'] != "") ? "".$row['saldo']."\t":"NULL\t";
			$schema_insert .= ($row['caja_interno'] != "") ? "".$row['caja_interno']."\t":"NULL\t";
			$schema_insert .= ($row['vendedor_codigo'] != "") ? "".$row['vendedor_codigo']."\t":"NULL\t";
			$schema_insert .= ($row['cobrador_codigo'] != "") ? "".$row['cobrador_codigo']."\t":"NULL\t";
			$schema_insert .= ($row['digitado_por'] != "") ? "".$row['digitado_por']."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Reportes_Cartera.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Cartera_Edades']))
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Edades Cartera\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "ID\t";
	$output .= "Nombre\t";
	$output .= "Total\t";
	$output .= "Corriente\t";
	$output .= "De 30 a 45\t";
	$output .= "De 45 a 60\t";
	$output .= "De 60 a 90\t";
	$output .= "Mas de 90\t";
	$output .= "Cobrador\t";
	$output .= "\n";
	// End Header
	
	$Today = date('Y-m-d');
	$Until30 = date('Y-m-d', strtotime('-30 days'));
	$Until45 = date('Y-m-d', strtotime('-45 days'));
	$Until60 = date('Y-m-d', strtotime('-60 days'));
	$Until90 = date('Y-m-d', strtotime('-90 days'));
	$Total = 0;
	$Total_A30 = 0;
	$Total_A45 = 0;
	$Total_A60 = 0;
	$Total_A90 = 0;
	$Total_Mas90 = 0;
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$CobradorID = isset($_GET['CobradorID']) ? $_GET['CobradorID']:"";
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT cliente_id, fecha, saldo, cobrador_codigo FROM mov_clientes WHERE estado = 'Aprobado' 
	AND tipo_movimiento = 'Debito' AND saldo > '0'";
	$query .= ($ClienteID != "") ? " AND cliente_id = '".$ClienteID."'":"";
	$query .= " ORDER BY cliente_id, cobrador_codigo";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if ($CobradorID != "" && $CobradorID != $row["cobrador_codigo"])
				continue;
			
			if (!isset($Data[$row["cliente_id"].$row["cobrador_codigo"]])) {
				$Data[$row["cliente_id"].$row["cobrador_codigo"]] = Array(
					"ClienteID" => $row["cliente_id"],
					"CobradorID" => $row["cobrador_codigo"],
				);
			}
			
			if (!isset($Deuda[$row["cliente_id"].$row["cobrador_codigo"]]))
				$Deuda[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
			else
				$Deuda[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
			
			$Total += $row["saldo"];
			
			if ($row["fecha"] >= $Until30 && $row["fecha"] <= $Today)
			{
				if (!isset($A30[$row["cliente_id"].$row["cobrador_codigo"]]))
					$A30[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$A30[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_A30 += $row["saldo"];
			}
			
			if ($row["fecha"] >= $Until45 && $row["fecha"] <= $Until30)
			{
				if (!isset($A45[$row["cliente_id"].$row["cobrador_codigo"]]))
					$A45[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$A45[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_A45 += $row["saldo"];
			}
			
			if ($row["fecha"] >= $Until60 && $row["fecha"] <= $Until45)
			{
				if (!isset($A60[$row["cliente_id"].$row["cobrador_codigo"]]))
					$A60[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$A60[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_A60 += $row["saldo"];
			}
			
			if ($row["fecha"] >= $Until90 && $row["fecha"] <= $Until60)
			{
				if (!isset($A90[$row["cliente_id"].$row["cobrador_codigo"]]))
					$A90[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$A90[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_A90 += $row["saldo"];
			}
			
			if ($row["fecha"] <= $Until90)
			{
				if (!isset($Mas90[$row["cliente_id"].$row["cobrador_codigo"]]))
					$Mas90[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$Mas90[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_Mas90 += $row["saldo"];
			}
		}
	}
	else
		die();
	
	if (count($Data) > 0)
	{
		foreach($Data as $item)
		{
			$newdata[] = array(
				"Cliente" => isset($ClienteNombre[$item["ClienteID"]]) ? $ClienteNombre[$item["ClienteID"]]:"No Existe!",
				"ClienteID" => $item["ClienteID"],
				"CobradorID" => $item["CobradorID"],
				"Deuda" => $Deuda[$item["ClienteID"].$item["CobradorID"]],
				"A30" => isset($A30[$item["ClienteID"].$item["CobradorID"]]) ? $A30[$item["ClienteID"].$item["CobradorID"]]:0,
				"A45" => isset($A45[$item["ClienteID"].$item["CobradorID"]]) ? $A45[$item["ClienteID"].$item["CobradorID"]]:0,
				"A60" => isset($A60[$item["ClienteID"].$item["CobradorID"]]) ? $A60[$item["ClienteID"].$item["CobradorID"]]:0,
				"A90" => isset($A90[$item["ClienteID"].$item["CobradorID"]]) ? $A90[$item["ClienteID"].$item["CobradorID"]]:0,
				"Mas90" => isset($Mas90[$item["ClienteID"].$item["CobradorID"]]) ? $Mas90[$item["ClienteID"].$item["CobradorID"]]:0,
				"Total" => $Total,
				"Total_A30" => $Total_A30,
				"Total_A45" => $Total_A45,
				"Total_A60" => $Total_A60,
				"Total_A90" => $Total_A90,
				"Total_Mas90" => $Total_Mas90,
			);
		}
		sort($newdata);
		
		foreach($newdata as $item)
		{
			$schema_insert = "";
			//
			$schema_insert .= ($item["ClienteID"] != "") ? $item["ClienteID"]."\t":"NULL\t";
			$schema_insert .= ($item["Cliente"] != "") ? $item["Cliente"]."\t":"No Existe!\t";
			$schema_insert .= $item["Deuda"]."\t";
			$schema_insert .= $item["A30"]."\t";
			$schema_insert .= $item["A45"]."\t";
			$schema_insert .= $item["A60"]."\t";
			$schema_insert .= $item["A90"]."\t";
			$schema_insert .= $item["Mas90"]."\t";
			$schema_insert .= ($item["CobradorID"] != "") ? $item["CobradorID"]."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
		
		/*foreach($Data as $item)
		{
			$schema_insert = "";
			//
			$schema_insert .= ($item["ClienteID"] != "") ? $item["ClienteID"]."\t":"NULL\t";
			$schema_insert .= isset($ClienteNombre[$item["ClienteID"]]) ? $ClienteNombre[$item["ClienteID"]]."\t":"No Existe!\t";
			$schema_insert .= isset($Deuda[$item["ClienteID"].$item["CobradorID"]]) ? $Deuda[$item["ClienteID"].$item["CobradorID"]]."\t":"0\t";
			$schema_insert .= isset($A30[$item["ClienteID"].$item["CobradorID"]]) ? $A30[$item["ClienteID"].$item["CobradorID"]]."\t":"0\t";
			$schema_insert .= isset($A45[$item["ClienteID"].$item["CobradorID"]]) ? $A45[$item["ClienteID"].$item["CobradorID"]]."\t":"0\t";
			$schema_insert .= isset($A60[$item["ClienteID"].$item["CobradorID"]]) ? $A60[$item["ClienteID"].$item["CobradorID"]]."\t":"0\t";
			$schema_insert .= isset($A90[$item["ClienteID"].$item["CobradorID"]]) ? $A90[$item["ClienteID"].$item["CobradorID"]]."\t":"0\t";
			$schema_insert .= isset($Mas90[$item["ClienteID"].$item["CobradorID"]]) ? $Mas90[$item["ClienteID"].$item["CobradorID"]]."\t":"0\t";
			$schema_insert .= ($item["CobradorID"] != "") ? $item["CobradorID"]."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}*/
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "\n";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= $Total."\t";
		$output .= $Total_A30."\t";
		$output .= $Total_A45."\t";
		$output .= $Total_A60."\t";
		$output .= $Total_A90."\t";
		$output .= $Total_Mas90."\t";
		$output .= "NULL\t";
		$output .= "\n";
	}
	// Download the file
	$filename = "Reportes_Edades_Cartera.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
	
	/*if ($num > 0)
	{
		foreach($data as $item)
		{
			$Clientes[] = $item["ClienteID"];
		}
		
		$Clause = implode("', '", $Clientes);
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Debito' AND estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Deuda[$row["cliente_id"]] = $row["saldo"];
				$Total += $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '".$Until30."' AND '".$Today."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A30[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '".$Until45."' AND '".$Until30."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A45[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '".$Until60."' AND '".$Until45."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A60[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '".$Until90."' AND '".$Until60."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A90[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '0000-00-00' AND '".$Until90."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Mas90[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		foreach($data as $item)
		{
			$schema_insert = "";
			//
			$schema_insert .= ($item["ClienteID"] != "") ? "".$item["ClienteID"]."\t":"NULL\t";
			$schema_insert .= isset($ClienteNombre[$item["ClienteID"]]) ? "".$ClienteNombre[$item["ClienteID"]]."\t":"NULL\t";
			$schema_insert .= isset($Deuda[$item["ClienteID"]]) ? "".$Deuda[$item["ClienteID"]]."\t":"0\t";
			$schema_insert .= isset($A30[$item["ClienteID"]]) ? "".$A30[$item["ClienteID"]]."\t":"0\t";
			$schema_insert .= isset($A45[$item["ClienteID"]]) ? "".$A45[$item["ClienteID"]]."\t":"0\t";
			$schema_insert .= isset($A60[$item["ClienteID"]]) ? "".$A60[$item["ClienteID"]]."\t":"0\t";
			$schema_insert .= isset($A90[$item["ClienteID"]]) ? "".$A90[$item["ClienteID"]]."\t":"0\t";
			$schema_insert .= isset($Mas90[$item["ClienteID"]]) ? "".$Mas90[$item["ClienteID"]]."\t":"0\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "\n";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "".$Total."\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "\n";
	}
	// Download the file
	$filename = "Reportes_Edades_Cartera.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
	*/
}
else if (isset($_GET['Reportes_Ventas']))// Cambiar a buscar el peso en la misma fact_final
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Facturacion\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Fecha\t";
	$output .= "Interno\t";
	$output .= "Remision\t";
	$output .= "Factura\t";
	$output .= "Cliente\t";
	$output .= "F_Pago\t";
	$output .= "Valor\t";
	$output .= "Peso\t";
	$output .= "Ruta\t";
	$output .= "VendedorID\t";
	$output .= "CobradorID\t";
	$output .= "\n";
	// End Header
	
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	/*$query = "SELECT cod_fab, nombre, peso FROM productos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoPeso[$row["cod_fab"]] = $row["peso"];
		}
	}*/
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM fact_final WHERE DATE(fecha_remision) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Ord_Compra']) && $_GET['Ord_Compra'] != "") ? " AND orden_compra LIKE '%".$_GET['Ord_Compra']."%'":"";
	$query .= (isset($_GET['Remision']) && $_GET['Remision'] != "") ? " AND remision LIKE '%".$_GET['Remision']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE %'".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= (isset($_GET['Vehiculo']) && $_GET['Vehiculo'] != "") ? " AND placa = '".$_GET['Vehiculo']."'":"";
	$query .= "ORDER BY fecha_remision DESC";
	
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$ValorTotal = 0;
	$PesoTotal = 0;
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Internos[] = $row["interno"];
			$ValorTotal += $row["total"];
			$PesoTotal += $row["peso"];
		}
	}
	else
	{
		die();
	}
	
	/*$Clause = implode("', '", $Internos);
	
	$query = "SELECT * FROM fact_movs WHERE interno IN ('".$Clause."') ORDER BY interno";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (isset($TotalPeso[$row["interno"]]))
				$TotalPeso[$row["interno"]] += $ProductoPeso[$row["codigo"]] * ($row["cantidad"] - $row["cantidad_despachada"]);
			else
				$TotalPeso[$row["interno"]] = $ProductoPeso[$row["codigo"]] * ($row["cantidad"] - $row["cantidad_despachada"]);
			
			$PesoTotal += $ProductoPeso[$row["codigo"]] * ($row["cantidad"] - $row["cantidad_despachada"]);
		}
	}*/
	
	if (mysql_num_rows($result1) > 0)
	{
		while ($row = mysql_fetch_array($result1))
		{
			$schema_insert = "";
			//
			$schema_insert .= ($row["fecha_remision"] != "") ? "".$row["fecha_remision"]."\t":"NULL\t";
			$schema_insert .= ($row["interno"] != "") ? "".$row["interno"]."\t":"NULL\t";
			$schema_insert .= ($row["remision"] != "") ? "".$row["remision"]."\t":"NULL\t";
			$schema_insert .= ($row['factura'] != "") ? "".$row['factura']."\t":"NULL\t";
			$schema_insert .= isset($ClienteNombre[$row['cliente_id']]) ? "".$ClienteNombre[$row['cliente_id']]."\t":"NULL\t";
			$schema_insert .= ($row['forma_pago'] != "") ? "".$row['forma_pago']."\t":"NULL\t";
			$schema_insert .= ($row['total'] != "") ? "".$row['total']."\t":"NULL\t";
			//$schema_insert .= isset($TotalPeso[$row['interno']]) ? "".$TotalPeso[$row['interno']]."\t":"NULL\t";
			$schema_insert .= ($row['peso'] != "") ? "".$row['peso']."\t":"NULL\t";
			$schema_insert .= ($row['ruta'] != "") ? "".$row['ruta']."\t":"NULL\t";
			$schema_insert .= ($row['vendedor_codigo'] != "") ? "".$row['vendedor_codigo']."\t":"NULL\t";
			$schema_insert .= ($row['cobrador_codigo'] != "") ? "".$row['cobrador_codigo']."\t":"NULL\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "\n";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "".$ValorTotal."\t";
		$output .= "".$PesoTotal."\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
	}
	// Download the file
	$filename = "Reportes_Facturacion.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Reportes_Ventas_Mov']))
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Ventas Mov\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Fecha\t";
	$output .= "Interno\t";
	$output .= "Factura\t";
	$output .= "Orden\t";
	$output .= "Remision\t";
	$output .= "Nombre\t";
	$output .= "Vend\t";
	$output .= "Categoria\t";
	$output .= "Grupo\t";
	$output .= "SubGrupo\t";
	$output .= "Producto\t";
	$output .= "Movimiento\t";
	$output .= "Facturado\t";
	$output .= "Ult. Costo\t";
	$output .= "Costo Prom.\t";
	$output .= "\n";
	// End Header
	
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	if ($Codigo == "")
	{
		$query = "SELECT * FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
		AND subgrupo LIKE '%".$SubGrupo."%' ORDER BY nombre ASC";
	}
	else
		$query = "SELECT * FROM productos WHERE cod_fab = '".$Codigo."'";
	
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoCodigo[] = $row["cod_fab"];
			$ProductoNombre[$row["cod_fab"]] = $row["nombre"];
			$ProductoCategoria[$row["cod_fab"]] = $row["categoria"];
			$ProductoGrupo[$row["cod_fab"]] = $row["grupo"];
			$ProductoSubGrupo[$row["cod_fab"]] = $row["subgrupo"];
			$ProductoUlt_Costo[$row["cod_fab"]] = $row["ultimo_costo"];
			$ProductoCosto_Prom[$row["cod_fab"]] = $row["costo_promedio"];
		}
	}
	
	$Clause1 = implode("', '", $ProductoCodigo);
	
	$query = "SELECT cod_fab, viejo_costo, costo_promedio, interno, fecha FROM inventario_movs WHERE DATE(fecha) 
	BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND cod_fab IN ('".$Clause1."') AND interno != '' ORDER BY interno";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (!isset($TmpInterno[$row["interno"]]))
			{
				$TmpInterno[$row["interno"]] = 1;
				$Internos1[] = $row["interno"];
			}
			
			$Ult_Costo[$row["cod_fab"].$row["interno"]] = $row["viejo_costo"];
			$Costo_Prom[$row["cod_fab"].$row["interno"]] = $row["costo_promedio"];
			$Fecha[$row["cod_fab"].$row["interno"]] = $row["fecha"];
		}
	}
	
	$Clause2 = implode("', '", $Internos1);
	
	$query = "SELECT * FROM fact_final WHERE interno IN ('".$Clause2."')";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Ord_Compra']) && $_GET['Ord_Compra'] != "") ? " AND orden_compra LIKE '%".$_GET['Ord_Compra']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Internos2[] = $row["interno"];
			$Vendedor[$row["interno"]] = $row["vendedor_codigo"];
		}
	}
	else
	{
		die();
	}
	
	$Clause3 = implode("', '", $Internos2);
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM fact_movs WHERE codigo IN ('".$Clause1."') AND interno IN ('".$Clause3."') ORDER BY interno";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if ($row["cantidad"] < 1)
				continue;
			
			$Cantidad = $row["cantidad"] - $row["cantidad_despachada"];
			
			$CostoU = isset($Ult_Costo[$row["codigo"].$row["interno"]]) ?
			$Ult_Costo[$row["codigo"].$row["interno"]] * $Cantidad:0;
			
			$CostoP = isset($Costo_Prom[$row["codigo"].$row["interno"]]) ?
			$Costo_Prom[$row["codigo"].$row["interno"]] * $Cantidad:0;
			
			$data[] = array(
				"Fecha" => isset($Fecha[$row["codigo"].$row["interno"]]) ? "".$Fecha[$row["codigo"].$row["interno"]]."":"1999-12-31 23:59:59",
				"Interno" => $row["interno"],
				"Factura" => $row["factura"],
				"Ord_Compra" => $row["orden_compra"],
				"Remision" => $row["remision"],
				"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				"VendedorID" => isset($Vendedor[$row["interno"]]) ? $Vendedor[$row["interno"]]:"",
				"Categoria" => isset($ProductoCategoria[$row["codigo"]]) ? $ProductoCategoria[$row["codigo"]]:"No Existe!",
				"Grupo" => isset($ProductoGrupo[$row["codigo"]]) ? $ProductoGrupo[$row["codigo"]]:"No Existe!",
				"SubGrupo" => isset($ProductoSubGrupo[$row["codigo"]]) ? $ProductoSubGrupo[$row["codigo"]]:"No Existe!",
				"Producto" => isset($ProductoNombre[$row["codigo"]]) ? $ProductoNombre[$row["codigo"]]:"No Existe!",
				"Cantidad" => $Cantidad,
				"Valor" => $row["precio"] * $Cantidad,
				"Ult_Costo" => $CostoU,
				"Costo_Prom" => $CostoP,
			);
		}
	}
	else
	{
		die();
	}
	sort($data);
	
	foreach ($data AS $row)
	{
		$schema_insert = "";
			//
			$schema_insert .= "".$row["Fecha"]."\t";
			$schema_insert .= "".$row["Interno"]."\t";
			$schema_insert .= "".$row["Factura"]."\t";
			$schema_insert .= "".$row["Ord_Compra"]."\t";
			$schema_insert .= "".$row["Remision"]."\t";
			$schema_insert .= "".$row["Nombre"]."\t";
			$schema_insert .= "".$row["VendedorID"]."\t";
			$schema_insert .= "".$row["Categoria"]."\t";
			$schema_insert .= "".$row["Grupo"]."\t";
			$schema_insert .= "".$row["SubGrupo"]."\t";
			$schema_insert .= "".$row["Producto"]."\t";
			$schema_insert .= "".$row["Cantidad"]."\t";
			$schema_insert .= "".$row["Valor"]."\t";
			$schema_insert .= "".$row["Ult_Costo"]."\t";
			$schema_insert .= "".$row["Costo_Prom"]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
	}
	
	// Download the file
	$filename = "Reportes_Ventas_Mov.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Reportes_Caja']))
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Caja\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Fecha\t";
	$output .= "Interno\t";
	$output .= "R. Caja\t";
	$output .= "Valor\t";
	$output .= "Saldo\t";
	$output .= "Categoria\t";
	$output .= "Grupo\t";
	$output .= "SubGrupo\t";
	$output .= "SubGrupo2\t";
	$output .= "Nombre\t";
	$output .= "DigitadorID\t";
	$output .= "\n";
	// End Header
	
	$ClienteNombre = ClientesNombre();
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM caja_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado'";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Categoria']) && $_GET['Categoria'] != "") ? " AND categoria = '".$_GET['Categoria']."'":"";
	$query .= (isset($_GET['Grupo']) && $_GET['Grupo'] != "") ? " AND grupo = '".$_GET['Grupo']."'":"";
	$query .= (isset($_GET['SubGrupo']) && $_GET['SubGrupo'] != "") ? " AND subgrupo = '".$_GET['SubGrupo']."'":"";
	$query .= (isset($_GET['SubGrupo2']) && $_GET['SubGrupo2'] != "") ? " AND subgrupo2 = '".$_GET['SubGrupo2']."'":"";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND digitado_por = '".$_GET['DigitadorID']."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Fecha' => "".$row["fecha"]." 00:00:00",
				'Caja_Interno' => $row["caja_interno"],
				'Caja_Recibo' => $row["caja_recibo"],
				'Valor' => $row["total"],
				'Saldo' => $row["saldo"],
				'Categoria' => $row["categoria"],
				'Grupo' => $row["grupo"],
				'SubGrupo' => $row["subgrupo"],
				'SubGrupo2' => $row["subgrupo2"],
				'Nombre' => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				'ClienteID' => $row["cliente_id"],
				'DigitadorID' => $row["digitado_por"],
			);
		}
		sort($data);
		
		foreach ($data AS $row)
		{
			$schema_insert = "";
			//
			$schema_insert .= "".$row["Fecha"]."\t";
			$schema_insert .= "".$row["Caja_Interno"]."\t";
			$schema_insert .= "".$row["Caja_Recibo"]."\t";
			$schema_insert .= "".$row["Valor"]."\t";
			$schema_insert .= "".$row["Saldo"]."\t";
			$schema_insert .= "".$row["Categoria"]."\t";
			$schema_insert .= "".$row["Grupo"]."\t";
			$schema_insert .= "".$row["SubGrupo"]."\t";
			$schema_insert .= "".$row["SubGrupo2"]."\t";
			$schema_insert .= "".$row["Nombre"]."\t";
			$schema_insert .= "".$row["DigitadorID"]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	
	// Download the file
	$filename = "Reportes_Caja.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET["Reportes_Maquinaria"]))
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Maquinaria\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Fecha Ini.\t";
	$output .= "Orden Rep.\t";
	$output .= "Maquina/Vehículo\t";
	$output .= "Motivo\t";
	$output .= "Diagnostico\t";
	$output .= "DigitadorID\t";
	$output .= "Total\t";
	$output .= "\n";
	// End Header
	
	$ClienteNombre = ClientesNombre();
	
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:"0000-00-00";
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:"0000-00-00";
	$Categoria = isset($_GET["Categoria"]) ? $_GET["Categoria"]:"";
	$Tipo = isset($_GET["Tipo"]) ? $_GET["Tipo"]:"";
	$Motivo = isset($_GET["Motivo"]) ? $_GET["Motivo"]:"";
	$Diagnostico = isset($_GET["Diagnostico"]) ? $_GET["Diagnostico"]:"";
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$DigitadorID = isset($_GET["DigitadorID"]) ? $_GET["DigitadorID"]:"";
	
	$query = "SELECT * FROM maquinaria_final WHERE DATE(fecha_ini) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado'";
	$query .= ($Categoria != "") ? " AND clasificacion = '".$Categoria."'":"";
	$query .= ($Tipo != "") ? " AND tipo = '".$Tipo."'":"";
	$query .= ($Motivo != "") ? " AND motivo = '".$Motivo."'":"";
	$query .= ($Diagnostico != "") ? " AND diagnostico = '".$Diagnostico."'":"";
	//$query .= ($ClienteID != "") ? " AND proveedor1 = '".$ClienteID."'":"";
	$query .= ($DigitadorID != "") ? " AND digitado_por = '".$DigitadorID."'":"";
	$query .= "ORDER BY fecha_ini ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$Total = 0;
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($ClienteID != "")
			{
				if ($row["proveedor1"] == $ClienteID || 
				$row["proveedor2"] == $ClienteID ||
				$row["proveedor3"] == $ClienteID)
				{
					$Total += $row["total"];
					$schema_insert = "";
					//
					$schema_insert .= "".$row["fecha_ini"]."\t";
					$schema_insert .= "".$row["ord_reparacion"]."\t";
					$schema_insert .= "".$row["tipo"]."\t";
					$schema_insert .= "".$row["motivo"]."\t";
					$schema_insert .= "".$row["diagnostico"]."\t";
					$schema_insert .= "".$row["digitado_por"]."\t";
					$schema_insert .= "".$row["total"]."\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
			}
			else
			{
				$Total += $row["total"];
				$schema_insert = "";
				//
				$schema_insert .= "".$row["fecha_ini"]."\t";
				$schema_insert .= "".$row["ord_reparacion"]."\t";
				$schema_insert .= "".$row["tipo"]."\t";
				$schema_insert .= "".$row["motivo"]."\t";
				$schema_insert .= "".$row["diagnostico"]."\t";
				$schema_insert .= "".$row["digitado_por"]."\t";
				$schema_insert .= "".$row["total"]."\t";
				//
				$schema_insert = str_replace("\t"."$", "", $schema_insert);
				$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
				$schema_insert .= "\t";
				$output .= trim($schema_insert);
				$output .= "\n";
			}
		}
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "\n";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "NULL\t";
		$output .= "TOTAL\t";
		$output .= "".$Total."\t";
	}
	
	// Download the file
	$filename = "Reportes_Maquinaria.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Reportes_Inventario']))
{
	$Existencia = isset($_GET['Existencia']) ? $_GET['Existencia']:"";
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	
	$output = "";
	// Start Header
	$output .= "Reportes - Inventario\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	
	if ($Existencia == true)
		$output .= "NULL\t";
	
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	
	if ($Existencia == true)
		$output .= "NULL\t";
	
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Nombre\t";
	$output .= "Categoria\t";
	$output .= "Grupo\t";
	$output .= "SubGrupo\t";
	$output .= "Peso\t";
	
	if ($Existencia == true)
		$output .= "Existencia\t";

	$output .= "Ult_Costo\t";
	$output .= "Total_Ult_Costo\t";
	$output .= "Costo_Prom\t";
	$output .= "Total_Costo_Prom\t";
	$output .= "\n";
	// End Header
	
	if ($Codigo == "")
	{
		$query = "SELECT * FROM productos WHERE nombre != '' AND categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
		AND subgrupo LIKE '%".$SubGrupo."%' ORDER BY nombre";
	}
	else
		$query = "SELECT * FROM productos WHERE nombre != '' AND cod_fab = '".$Codigo."' ORDER BY nombre";
		
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$schema_insert = "";
			//
			$schema_insert .= "".$row["nombre"]."\t";
			$schema_insert .= "".$row["categoria"]."\t";
			$schema_insert .= "".$row["grupo"]."\t";
			$schema_insert .= "".$row["subgrupo"]."\t";
			$schema_insert .= "".$row["peso"]."\t";
			
			if ($Existencia == true)
				$schema_insert .= "".$row["existencia"]."\t";
			
			$schema_insert .= "".$row["ultimo_costo"]."\t";
			$schema_insert .= "".$row["ultimo_costo"] * $row["existencia"]."\t";
			$schema_insert .= "".$row["costo_promedio"]."\t";
			$schema_insert .= "".$row["costo_promedio"] * $row["existencia"]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	
	// Download the file
	$filename = "Reportes_Inventario.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Reportes_Kardex']))
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Kardex\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Fecha\t";
	$output .= "Tercero\t";
	$output .= "Tercero ID\t";
	$output .= "Tipo\t";
	$output .= "Motivo\t";
	$output .= "Cantidad\t";
	$output .= "Existencia\t";
	$output .= "Nuevo Saldo\t";
	$output .= "Interno\t";
	$output .= "Factura\t";
	$output .= "Remision\t";
	$output .= "\n";
	// End Header
	
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Motivo = isset($_GET['Motivo']) ? $_GET['Motivo']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	$Fecha_Fin = date("Y-m-d", strtotime($Fecha_Fin . "+1 day"));// Fix Today's Problem...
	
	if ($Codigo == "")
	{
		// Download the file
		$filename = "Reportes_Kardex.xls";
		header('Content-type: application/xls');
		header('Content-Disposition: attachment; filename='.$filename);
		echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
	}
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT cliente_id, interno, factura, remision FROM fact_final WHERE 
	cliente_id LIKE '%".$ClienteID."%' AND estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$ID[$row["interno"]] = $row["cliente_id"];
			$Factura[$row["interno"]] = $row["factura"];
			$Remision[$row["interno"]] = $row["remision"];
		}
	}
	
	$query = "SELECT entrada, factura, interno, cliente_id FROM compras_final WHERE 
	cliente_id LIKE '%".$ClienteID."%' AND estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$ID[$row["interno"]] = $row["cliente_id"];
			$Factura[$row["interno"]] = $row["factura"];
		}
	}
	
	$query = "SELECT orden_produccion, cliente_id FROM produccion_final WHERE 
	cliente_id LIKE '%".$ClienteID."%' AND estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$ID[$row["orden_produccion"]] = $row["cliente_id"];
		}
	}
	
	/*$query = "SELECT * FROM inventario_movs WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' 
	AND motivo LIKE '%".$Motivo."%' AND cod_fab = '".$Codigo."' ORDER BY fecha ASC";*/
	$query = "SELECT * FROM inventario_movs WHERE cod_fab = '".$Codigo."' ORDER BY fecha ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$Saldo = 0;
		while($row = mysql_fetch_array($result))
		{
			$rows[] = $row;
			if ($row["motivo"] == "Inicial")
				$Saldo = $row["cantidad"];
		}
		
		foreach($rows AS $row)
		{
			$Existencia = $Saldo;
			if ($row["tipo"] == "Entrada" && $row["motivo"] != "Inicial")
				$Saldo += $row["cantidad"];
			else if ($row["tipo"] == "Salida" && $row["motivo"] != "Inicial")
				$Saldo -= $row["cantidad"];
			else
			{
				$GridData[] = array(
					"Fecha" => $row["fecha"],
					"Cliente" => $MAIN_ID,
					"Tipo" => $row["tipo"],
					"Motivo" => "Inicial",
					"Cantidad" => $row["cantidad"],
					"Saldo" => $Saldo,
					"Existencia" => 0,
					"Interno" => "",
					"Factura" => "",
					"Remision" => "",
				);
				continue;
			}
			
			if (!isset($ID[$row["interno"]]))
				continue;
			
			$GridData[] = array(
				"Fecha" => $row["fecha"],
				"Cliente" => isset($ID[$row["interno"]]) ? $ID[$row["interno"]]:"",
				"Tipo" => $row["tipo"],
				"Motivo" => $row["motivo"],
				"Cantidad" => $row["cantidad"],
				"Saldo" => $Saldo,
				"Existencia" => $Existencia,
				"Interno" => $row["interno"],
				"Factura" => isset($Factura[$row["interno"]]) ? $Factura[$row["interno"]]:"",
				"Remision" => isset($Remision[$row["interno"]]) ? $Remision[$row["interno"]]:"",
			);
		}
		
		foreach($GridData AS $Item)
		{
			if ($Motivo != "")
			{
				if ($Item["Fecha"] >= $Fecha_Ini && $Item["Fecha"] <= $Fecha_Fin && $Item["Motivo"] == $Motivo)
				{
					$schema_insert = "";
					//
					$schema_insert .= "".$Item["Fecha"]."\t";
					$schema_insert .= isset($ClienteNombre[$Item["Cliente"]]) ? "".$ClienteNombre[$Item["Cliente"]]."\t":"NULL\t";
					$schema_insert .= $Item["Cliente"] != "" ? "".$Item["Cliente"]."\t":"NULL\t";
					$schema_insert .= "".$Item["Tipo"]."\t";
					$schema_insert .= "".$Item["Motivo"]."\t";
					$schema_insert .= "".$Item["Cantidad"]."\t";
					$schema_insert .= "".$Item["Saldo"]."\t";
					$schema_insert .= "".$Item["Existencia"]."\t";
					$schema_insert .= "".$Item["Interno"]."\t";
					$schema_insert .= $Item["Factura"] != "" ? "".$Item["Factura"]."\t":"NULL\t";
					$schema_insert .= $Item["Remision"] != "" ? "".$Item["Remision"]."\t":"NULL\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
			}
			else
			{
				if ($Item["Fecha"] >= $Fecha_Ini && $Item["Fecha"] <= $Fecha_Fin)
				{
					$schema_insert = "";
					//
					$schema_insert .= "".$Item["Fecha"]."\t";
					$schema_insert .= isset($ClienteNombre[$Item["Cliente"]]) ? "".$ClienteNombre[$Item["Cliente"]]."\t":"NULL\t";
					$schema_insert .= $Item["Cliente"] != "" ? "".$Item["Cliente"]."\t":"NULL\t";
					$schema_insert .= "".$Item["Tipo"]."\t";
					$schema_insert .= "".$Item["Motivo"]."\t";
					$schema_insert .= "".$Item["Cantidad"]."\t";
					$schema_insert .= "".$Item["Saldo"]."\t";
					$schema_insert .= "".$Item["Existencia"]."\t";
					$schema_insert .= "".$Item["Interno"]."\t";
					$schema_insert .= $Item["Factura"] != "" ? "".$Item["Factura"]."\t":"NULL\t";
					$schema_insert .= $Item["Remision"] != "" ? "".$Item["Remision"]."\t":"NULL\t";
					//
					$schema_insert = str_replace("\t"."$", "", $schema_insert);
					$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
					$schema_insert .= "\t";
					$output .= trim($schema_insert);
					$output .= "\n";
				}
			}
		}
	}
	// Download the file
	$filename = "Reportes_Kardex.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Reportes_Compras']))
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Compras\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Fecha\t";
	$output .= "Interno\t";
	$output .= "Factura\t";
	$output .= "Doc_Transp\t";
	$output .= "Entrada\t";
	$output .= "Pedido\t";
	$output .= "Cliente\t";
	$output .= "Placa\t";
	$output .= "Peso\t";
	$output .= "Valor\t";
	$output .= "\n";
	// End Header
	
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	if ($OrderBy == "Cliente")
		$OrderBy = "cliente_id";
	else if ($OrderBy == "Vehiculo")
		$OrderBy = "placa";
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM compras_final WHERE DATE(fecha_compra) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['Entrada']) && $_GET['Entrada'] != "") ? " AND entrada LIKE '%".$_GET['Entrada']."%'":"";
	$query .= (isset($_GET['Vehiculo']) && $_GET['Vehiculo'] != "") ? " AND placa LIKE '%".$_GET['Vehiculo']."%'":"";
	$query .= ($OrderBy != "") ? " ORDER BY ".$OrderBy." ":"ORDER BY fecha_compra DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$ValorTotal = 0;
	$PesoTotal = 0;
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$ValorTotal += $row["total"];
			$PesoTotal += $row["peso"];
		}
	}
	else
	{
		die();
	}
	
	$ClienteNombre = ClientesNombre();
	
	if (mysql_num_rows($result1) > 0)
	{
		while ($row = mysql_fetch_array($result1))
		{
			$schema_insert = "";
			//
			$schema_insert .= "".$row["fecha_compra"]."\t";
			$schema_insert .= "".$row["interno"]."\t";
			$schema_insert .= "".$row["factura"]."\t";
			$schema_insert .= "".$row["doc_transp"]."\t";
			$schema_insert .= "".$row["entrada"]."\t";
			$schema_insert .= "".$row["pedido"]."\t";
			$schema_insert .= isset($ClienteNombre[$row["cliente_id"]]) ? "".$ClienteNombre[$row["cliente_id"]]."\t":"NULL\t";
			$schema_insert .= "".$row["placa"]."\t";
			$schema_insert .= "".$row["peso"]."\t";
			$schema_insert .= "".$row["total"]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
		}
	}
	// Download the file
	$filename = "Reportes_Compras.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}
else if (isset($_GET['Reportes_Compras_Mov']))
{
	$output = "";
	
	// Start Header
	$output .= "Reportes - Compras Mov\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "NULL\t";
	$output .= "\n";
	//
	$output .= "Fecha\t";
	$output .= "Interno\t";
	$output .= "Entrada\t";
	$output .= "Factura\t";
	$output .= "Proveedor\t";
	$output .= "Categoria\t";
	$output .= "Grupo\t";
	$output .= "SubGrupo\t";
	$output .= "Producto\t";
	$output .= "Movimiento\t";
	$output .= "Facturado\t";
	$output .= "Ult. Costo\t";
	$output .= "Costo Prom.\t";
	$output .= "\n";
	// End Header
	
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	if ($Codigo == "")
	{
		$query = "SELECT * FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
		AND subgrupo LIKE '%".$SubGrupo."%' ORDER BY nombre ASC";
	}
	else
		$query = "SELECT * FROM productos WHERE cod_fab = '".$Codigo."'";
	
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoCodigo[] = $row["cod_fab"];
			$ProductoNombre[$row["cod_fab"]] = $row["nombre"];
			$ProductoCategoria[$row["cod_fab"]] = $row["categoria"];
			$ProductoGrupo[$row["cod_fab"]] = $row["grupo"];
			$ProductoSubGrupo[$row["cod_fab"]] = $row["subgrupo"];
			$ProductoCosto[$row["cod_fab"]] = $row["costo"];
			$ProductoUlt_Costo[$row["cod_fab"]] = $row["ultimo_costo"];
			$ProductoCosto_Prom[$row["cod_fab"]] = $row["costo_promedio"];
		}
	}
	
	$Clause1 = implode("', '", $ProductoCodigo);
	
	$query = "SELECT cod_fab, viejo_costo, costo_promedio, interno, fecha FROM inventario_movs WHERE DATE(fecha) 
	BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND cod_fab IN ('".$Clause1."') AND interno != '' ORDER BY interno";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (!isset($TmpInterno[$row["interno"]]))
			{
				$TmpInterno[$row["interno"]] = 1;
				$Internos1[] = $row["interno"];
			}
			
			//$Ult_Costo[$row["cod_fab"].$row["interno"]] = $row["viejo_costo"];
			//$Costo_Prom[$row["cod_fab"].$row["interno"]] = $row["costo_promedio"];
			$Fecha[$row["cod_fab"].$row["interno"]] = $row["fecha"];
		}
	}
	
	$Clause2 = implode("', '", $Internos1);
	
	$query = "SELECT * FROM compras_final WHERE interno IN ('".$Clause2."')";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['Pedido']) && $_GET['Pedido'] != "") ? " AND pedido LIKE '%".$_GET['Pedido']."%'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Internos2[] = $row["interno"];
		}
	}
	else
	{
		die();
	}
	
	$Clause3 = implode("', '", $Internos2);
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM compras_movs WHERE codigo IN ('".$Clause1."') AND interno IN ('".$Clause3."') ORDER BY interno";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if ($row["cantidad"] < 1)
				continue;
			
			$Cantidad = $row["cantidad"];
			
			$CostoU = isset($ProductoCosto[$row["codigo"]]) ? $ProductoCosto[$row["codigo"]]:0;
			$CostoP = isset($ProductoCosto_Prom[$row["codigo"]]) ? $ProductoCosto_Prom[$row["codigo"]]:0;
			
			$data[] = array(
				"Fecha" => isset($Fecha[$row["codigo"].$row["interno"]]) ? "".$Fecha[$row["codigo"].$row["interno"]]."":"1999-12-31 23:59:59",
				"Interno" => $row["interno"],
				"Factura" => $row["factura"],
				"Entrada" => $row["entrada"],
				"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				"ClienteID" => $row["cliente_id"],
				"Categoria" => isset($ProductoCategoria[$row["codigo"]]) ? $ProductoCategoria[$row["codigo"]]:"No Existe!",
				"Grupo" => isset($ProductoGrupo[$row["codigo"]]) ? $ProductoGrupo[$row["codigo"]]:"No Existe!",
				"SubGrupo" => isset($ProductoSubGrupo[$row["codigo"]]) ? $ProductoSubGrupo[$row["codigo"]]:"No Existe!",
				"Codigo" => $row["codigo"],
				"Producto" => isset($ProductoNombre[$row["codigo"]]) ? $ProductoNombre[$row["codigo"]]:"No Existe!",
				"Cantidad" => $Cantidad,
				"Unitario" => $row["nuevo_costo"],
				"Valor" => $row["nuevo_costo"] * $Cantidad,
				"Ult_Costo" => $CostoU,
				"Costo_Prom" => $CostoP,
			);
		}
	}
	else
	{
		die();
	}
	sort($data);
	
	foreach ($data AS $row)
	{
		$schema_insert = "";
			//
			$schema_insert .= "".$row["Fecha"]."\t";
			$schema_insert .= "".$row["Interno"]."\t";
			$schema_insert .= "".$row["Entrada"]."\t";
			$schema_insert .= "".$row["Factura"]."\t";
			$schema_insert .= "".$row["Nombre"]."\t";
			$schema_insert .= "".$row["Categoria"]."\t";
			$schema_insert .= "".$row["Grupo"]."\t";
			$schema_insert .= "".$row["SubGrupo"]."\t";
			$schema_insert .= "".$row["Producto"]."\t";
			$schema_insert .= "".$row["Cantidad"]."\t";
			$schema_insert .= "".$row["Valor"]."\t";
			$schema_insert .= "".$row["Ult_Costo"]."\t";
			$schema_insert .= "".$row["Costo_Prom"]."\t";
			//
			$schema_insert = str_replace("\t"."$", "", $schema_insert);
			$schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
			$schema_insert .= "\t";
			$output .= trim($schema_insert);
			$output .= "\n";
	}
	
	// Download the file
	$filename = "Reportes_Compras_Mov.xls";
	header('Content-type: application/xls');
	header('Content-Disposition: attachment; filename='.$filename);
	echo iconv("UTF-8", "ISO-8859-1", $output);// excell charset
}

exit;
?>