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

if ($bool === False){
	print "can't find ".$database."";
}

function ClientesNombre()
{
	global $DEBUG;
	
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

function CodigosNombre()
{
	global $DEBUG;
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT user_id, user_code FROM login WHERE active = 'true' AND user_lvl = 'Vendedor' OR user_lvl = 'Administrador'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$CodigoNombre[$row["user_code"]] = isset($ClienteNombre[$row["user_id"]]) ? $ClienteNombre[$row["user_id"]]:"No Existe!";
	}
	return $CodigoNombre;
}

function GetClient_Info($ClienteID)
{
	global $DEBUG;
	
	$query = "SELECT * FROM clientes WHERE cliente_id = '".$ClienteID."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #0: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array(
			'Nombre' => $row["nombre"],
			'Direccion' => $row["direccion"],
			'Telefono' => $row["telefono"],
			'Email' => $row["email"],
		);
		return $data;
	}
	else
		return array();
}

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
	global $DEBUG;
	
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

function Recibo_Info ($ID)
{
	global $DEBUG;
	
	$query = "SELECT * FROM caja_final WHERE caja_interno = '".$ID."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Fecha' => $row['fecha'],
				'Caja_Recibo' => $row['caja_recibo'],
				'Valor' => $row['total'],
				'Saldo' => $row['saldo'],
			);
		}
		return $data;
	}
	else {
		$data[] = array();
		return $data;
	}
}

function BuscarRecibosConSaldo ($ID)
{
	global $DEBUG;
	
	$query = "SELECT caja_interno, caja_recibo FROM caja_final WHERE categoria = 'Ingresos' AND grupo = 'Cliente' 
	AND cliente_id = '".$ID."' AND saldo > '0' AND estado = 'Aprobado' ORDER BY fecha ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Caja_Interno' => $row['caja_interno'],
				'Caja_Recibo' => $row['caja_recibo'],
			);
		}
		return $data;
	}
	else {
		$data[] = array(
			'Caja_Interno' => "",
			'Caja_Recibo' => "",
		);
		return $data;
	}
}

function BuscarCxPConDeuda ($ID)
{
	global $DEBUG;
	
	$query = "SELECT factura, compra_interno FROM cxp_movs WHERE cliente_id = '".$ID."' 
	AND tipo_movimiento = 'Compra' AND saldo > '0' AND estado = 'Aprobado' ORDER BY fecha_digitado ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Factura' => $row['factura'],
				'Interno' => $row['compra_interno'],
			);
		}
		return $data;
	}
	else {
		$data[] = array(
			'Factura' => "",
			'Interno' => "",
		);
		return $data;
	}
}

function Cargar_Deuda_Cartera($ID)
{
	global $DEBUG;
	
	$DeudaTotal = 0;
	$query = "SELECT saldo FROM mov_clientes WHERE cliente_id = '".$ID."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado' AND saldo > '0'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$DeudaTotal = $DeudaTotal + $row["saldo"];
			//echo "Deuda: ".$row["saldo"]."<br/>";
		}
	}
	//echo "Deuda Total: ".$DeudaTotal."<br/> resultados: ".mysql_num_rows($result);
	//die();
	$data[] = array(
		'DeudaTotal' => $DeudaTotal,
	);
	return $data;
}

function Cargar_Deuda_Compra($ID)
{
	global $DEBUG;
	
	$DeudaTotal = 0;
	$query = "SELECT saldo FROM cxp_movs WHERE cliente_id = '".$ID."' AND tipo_movimiento = 'Compra' AND estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
	while ($row = mysql_fetch_array($result))
	{
		$DeudaTotal = $DeudaTotal + $row["saldo"];
	}
	//echo "Deuda Total: ".$DeudaTotal."<br/>";
	
	$data[] = array(
		'DeudaTotal' => $DeudaTotal,
	);
	return $data;
}

function ClientesNomina($Tipo)
{
	global $DEBUG;
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT DISTINCT cliente_id, tipo FROM cliente_grupo WHERE clasificacion = 'Nómina'";
	$query .= ($Tipo != "") ? " AND tipo = '".$Tipo."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Nombre' => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				'ClienteID' => $row["cliente_id"],
				'Tipo' => $row["tipo"],
			);
		}
		sort($data);
		return $data;
	}
	else
	{
		$data[] = array();
		return $data;
	}
}

function GetChofer($Tipo)
{
	global $DEBUG;
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT DISTINCT cliente_id FROM contratos WHERE charge = 'Conductor' AND active = 'true'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Chofer' => $ClienteNombre[$row["cliente_id"]],
				'ClienteID' => $row["cliente_id"],
			);
		}
		sort($data);
		return $data;
	}
	else
	{
		$data[] = array();
		return $data;
	}
}

function GetProductFullInfo($Type)//Terminarla para que tome los productos normales
{
	global $DEBUG;
	
	if ($Type == "Requerimientos")
	{
		$query = "SELECT * FROM requerimientos_productos ORDER by nombre ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result))
			{
				$data[$row["codigo"]] = array(
					"Nombre" => $row["nombre"],
					"Unidad" => $row["unidad"],
					"Peso" => $row["peso"],
					"Valor" => $row["valor"],
					"Existencia" => $row["existencia"],
					"Stock" => $row["stock"],
				);
			}
		}
	}
	else
	{
		//
	}
	return $data;
}

if (isset($_GET["Inicio_Ausencias"]))
{
	$ClienteNombre = ClientesNombre();
	$query = "SELECT *, SUBSTR(fecha_digitado, 1, 10) as fecha FROM nom_novedades WHERE fecha_fin >= CURDATE() AND novedad != 'Hora extra' AND novedad != 'Llegada tarde' AND novedad != 'retiro'";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				"ClienteID" => $row["empleado_id"],
				"Nombre" => isset($ClienteNombre[$row["empleado_id"]]) ? $ClienteNombre[$row["empleado_id"]]:"No Existe!",
				"Novedad" => $row["novedad"],
				"Fecha_Ini" => $row["fecha_ini"]." 00:00:00",
				"Fecha_Fin" => $row["fecha_fin"]." 00:00:00",
				'Horas' => $row["horas_novedad"],
				'AutorizadorID' => isset($ClienteNombre[$row["autorizador_id"]]) ? $ClienteNombre[$row["autorizador_id"]]:"No Existe!",
				'Observaciones' => $row["observacion"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Terceros_Check_SameID']))
{
	$query = "SELECT cliente_id FROM clientes WHERE cliente_id = '".$_GET['Terceros_Check_SameID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	if (mysql_num_rows($result)>0)
	{
		$data[] = array(
			'Same' => true,
		);
	}
	else
	{
		$data[] = array(
			'Same' => false,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Search_Filter']))
{
	$query = "SELECT DISTINCT cliente_id FROM cliente_grupo WHERE clasificacion LIKE '%".$_GET['Tipo']."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Grupo[$row['cliente_id']] = $row['cliente_id'];
		}
	}
	
	$query = "SELECT DISTINCT nombre, cliente_id FROM clientes WHERE vendedor_codigo LIKE '%".$_GET['VendedorID']."%' 
	ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (isset($Grupo[$row['cliente_id']]))
			{
				$data[] = array(
					'Nombre' => $row['nombre'],
					'ClienteID' => $Grupo[$row['cliente_id']],
				);
			}
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Terceros_Modificar']))
{
	$query = "SELECT clientes.*, cliente_grupo.* FROM clientes LEFT JOIN cliente_grupo ON 
	clientes.cliente_id = cliente_grupo.cliente_id WHERE clientes.cliente_id = '".$_GET['Terceros_Modificar']."'";
	$result = mysql_query($query) or die ("SQL Error 1: " . mysql_error());
	
	$query = "SELECT grupo FROM cliente_grupo WHERE cliente_id = '".$_GET['Terceros_Modificar']."'";
	$result1 = mysql_query($query) or die ("SQL Error 2: " . mysql_error());
	
	$query = "SELECT * FROM clientes_garant WHERE cliente_id = '".$_GET['Terceros_Modificar']."'";
	$result2 = mysql_query($query) or die ("SQL Error 3: " . mysql_error());
	
	if (mysql_num_rows($result1) > 0)
	{
		while ($row = mysql_fetch_array($result1))
		{
			$data1[] = array(
				'Grupo' => $row["grupo"],
			);
		}
	}
	else
	{
		$data1[] = array();
	}
	
	if (mysql_num_rows($result2) > 0)
	{
		while ($row = mysql_fetch_array($result2))
		{
			$data2[] = array(
				'Apply' => true,
				'Garantia' => $row["garantia"],
				'Imagen' => $row["image"],
				'Ok' => $row["ok"],
			);
		}
	}
	else
	{
		$data2[] = array();
	}
	
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		if ($row["tipo_doc"] == "NIT" || $row["tipo_doc"] == "RUT")
		{
			$len = strlen($row["cliente_id"]);
			$ClienteID = substr($row["cliente_id"], 0, ($len - 2));
			$ClienteID2 = substr($row["cliente_id"], ($len - 1), 1);
		}
		else
		{
			$ClienteID = $row["cliente_id"];
			$ClienteID2 = 0;
		}
		
		$data[] = array(
			'Clasificacion' => $row["clasificacion"],
			'Tipo' => $row["tipo"],
			//
			'Grupo' => $data1,
			//
			'Foto' => $row["foto"],
			'Nombre' => $row["nombre"],
			'Direccion' => $row["direccion"],
			'Barrio' => $row["barrio"],
			'Ciudad' => $row["ciudad"],
			'Departamento' => $row["departamento"],
			'Pais' => $row["pais"],
			'Telefono' => $row["telefono"],
			'Fax' => $row["fax"],
			'Email' => $row["email"],
			'Email2' => $row["email2"],
			'Tipo_Sociedad' => $row["tipo_sociedad"],
			'Contacto_P' => $row["contacto_p"],
			'Telefono_CP' => $row["telefono_cp"],
			'Tipo_Doc' => $row["tipo_doc"],
			'ClienteID' => $ClienteID,
			'ClienteID2' => $ClienteID2,
			'Contacto_S' => $row["contacto_s"],
			'Telefono_CS' => $row["telefono_cs"],
			'VendedorID' => $row["vendedor_codigo"],
			'CobradorID' => $row["cobrador_codigo"],
			'Terminos' => $row["terminos"],
			'ListaP' => $row["lista_precio"],
			'Digitador' => $row["creado_por"],
			'Fecha_Dig' => $row["fecha_creacion"],
			'Modificador' => $row["modificado_por"],
			'Fecha_Modif' => $row["ultima_actualizacion"],
			'Fecha_Notas' => $row["vigencia_notas"],
			//
			'Cupo_Credito' => $row["credito"],
			'Vigencia_Cupo_Credito' => $row["vigencia_credito"],
			'Cupo_Credito_Activado' => $row["credito_activo"],
			'Activado_Por' => $row["cupo_asignado_por"],
			'Cupo_Adicional' => $row["adicional"],
			'Vigencia_Cupo_Adicional' => $row["vigencia_adicional"],
			'Estado_Cuenta' => $row["estado_cuenta"],
			'Notas' => $row["notas"],
			//
			'Garantia' => $data2,
		);
	}
	else
	{
		$data[] = array();
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Listado']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Terceros" && $data_session[$i]["SubModulo"] == "Listado" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT * FROM cliente_grupo";
	if ( (isset($_GET['Clasificacion']) && $_GET['Clasificacion'] != "") || (isset($_GET['Tipo']) && $_GET['Tipo'] != ""))
		$query .=" WHERE";
	
	$query .= (isset($_GET['Clasificacion']) && $_GET['Clasificacion'] != "") ? " clasificacion = '".$_GET['Clasificacion']."'":"";
	if ( (isset($_GET['Clasificacion']) && $_GET['Clasificacion'] != "") && (isset($_GET['Tipo']) && $_GET['Tipo'] != ""))
		$query .=" AND";
	
	$query .= (isset($_GET['Tipo']) && $_GET['Tipo'] != "") ? " tipo = '".$_GET['Tipo']."'":"";
	//echo $query;
	//die();
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
	
	if ($UserCode == "")
		$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	else
		$query .= " AND vendedor_codigo = '".$UserCode."'";
	
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= (isset($_GET['EstadoC']) && $_GET['EstadoC'] != "") ? " AND estado_cuenta = '".$_GET['EstadoC']."'":"";
	$query .= (isset($_GET['Garantia']) && $_GET['Garantia'] != "") ? " AND garantia = '".$_GET['Garantia']."'":"";
	//echo $query;
	//die();
	$query .= "ORDER BY nombre ASC;";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if (!isset($Clasificacion[$row["cliente_id"]]))
				continue;
			
			if (!isset($Tipo[$row["cliente_id"]]))
				continue;
			
			$data[] = array(
				'ID' => $row['id'],
				'Activo' => $row['activo'],
				'Motivo' => $row['motivo'],
				'Clasificacion' => $Clasificacion[$row["cliente_id"]],
				'Tipo' => $Tipo[$row["cliente_id"]],
				'ClienteID' => $row['cliente_id'],
				'Nombre' => $row['nombre'],
				'Direccion' => $row['direccion'],
				'Telefono' => $row['telefono'],
				'Barrio' => $row['barrio'],
				'Ciudad' => $row['ciudad'],
				'Departamento' => $row['departamento'],
				'ContactoCP' => $row['contacto_p'],
				'TelefonoCP' => $row['telefono_cp'],
				'TipoSociedad' => $row['tipo_sociedad'],
				'TipoDoc' => $row['tipo_doc'],
				'VendedorID' => $row['vendedor_codigo'],
				'CobradorID' => $row['cobrador_codigo'],
				'Terminos' => $row['terminos'],
				'ListaP' => $row['lista_precio'],
				'CupoCR' => $row['credito'],
				'CupoActivo' => $row['credito_activo'],
				'Vigencia' => "".$row['vigencia_credito']." 00:00:00",
				'Estado' => $row['estado_cuenta'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Ventas_Check_SameID']))
{
	$query = "SELECT orden_compra FROM fact_final WHERE orden_compra = '".$_GET['Ventas_Check_SameID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	if (mysql_num_rows($result)>0)
	{
		$data[] = array(
			'Same' => true,
		);
	}
	else
	{
		$data[] = array(
			'Same' => false,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Compras_Check_SameID']))
{
	$query = "SELECT entrada FROM compras_final WHERE entrada = '".$_GET['Compras_Check_SameID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	if (mysql_num_rows($result)>0)
	{
		$data[] = array(
			'Same' => true,
		);
	}
	else
	{
		$data[] = array(
			'Same' => false,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Inventario_Check_SameID']))
{
	$query = "SELECT cod_fab FROM productos WHERE cod_fab = '".$_GET['Inventario_Check_SameID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	if (mysql_num_rows($result)>0)
	{
		$data[] = array(
			'Same' => true,
		);
	}
	else
	{
		$data[] = array(
			'Same' => false,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Inventario_Modificar']))
{
	$query = "SELECT * FROM productos WHERE cod_fab = '".$_GET['Inventario_Modificar']."'";
	$result = mysql_query($query) or die ("SQL Error 1: " . mysql_error());
	
	$query = "SELECT productos_prov.*, clientes.nombre FROM productos_prov LEFT JOIN clientes ON 
	productos_prov.cliente_id = clientes.cliente_id WHERE productos_prov.cod_fab = '".$_GET['Inventario_Modificar']."'";
	$result1 = mysql_query($query) or die ("SQL Error 2: " . mysql_error());
	
	if (mysql_num_rows($result1)>0)
	{
		while ($row = mysql_fetch_array($result1))
		{
			$data1[] = array(
				'Proveedor' => $row["nombre"],
				'ProveedorID' => $row["cliente_id"],
			);
		}
	}
	else
	{
		$data1[] = array();
	}
	
	if (mysql_num_rows($result)>0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array(
			'Fecha_Modif' => $row["ultima_actualizacion"],
			'Categoria' => $row["categoria"],
			'Grupo' => $row["grupo"],
			'SubGrupo' => $row["subgrupo"],
			'Unidad' => $row["und_med"],
			'Costo' => $row["costo"],
			'Ultimo_Costo' => $row["ultimo_costo"],
			'Costo_Promedio' => $row["costo_promedio"],
			'Peso' => $row["peso"],
			'Stock' => $row["stock_minimo"],
			'Existencia' => $row["existencia"],
			'Facturar_sin_Existencia' => $row["factura_sin_existencia"],
			'Produccion' => $row["produccion"],
			'ListaP1' => $row["lista1"],
			'ListaP2' => $row["lista2"],
			'ListaP3' => $row["lista3"],
			'ListaP4' => $row["lista4"],
			'Foto_Producto' => $row["image"],
			'Notas' => $row["notas"],
			//
			'Proveedor' => $data1,
		);
	}
	else
	{
		$data[] = array(
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Caja_Check_SameID']))
{
	$query = "SELECT caja_recibo FROM caja_final WHERE caja_recibo = '".$_GET['Caja_Check_SameID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	if (mysql_num_rows($result)>0)
	{
		$data[] = array(
			'Same' => true,
		);
	}
	else
	{
		$data[] = array(
			'Same' => false,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Clientes_Proveedores']))
{
	$query = "SELECT DISTINCT clientes.nombre, clientes.cliente_id FROM clientes LEFT JOIN cliente_grupo 
	ON cliente_grupo.cliente_id = clientes.cliente_id WHERE cliente_grupo.clasificacion = 'Proveedor' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Proveedor' => $row['nombre'],
				'ProveedorID' => $row['cliente_id'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Clientes_Nomina']))
{
	$query = "SELECT DISTINCT clientes.nombre, clientes.cliente_id FROM clientes LEFT JOIN cliente_grupo 
	ON cliente_grupo.cliente_id = clientes.cliente_id WHERE cliente_grupo.clasificacion = 'Nómina' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Nombre' => $row['nombre'],
				'ClienteID' => $row['cliente_id'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Maquinaria']))
{
	$query = "SELECT * FROM par_caja_subgr2 WHERE grupo = 'Maquinaria' OR grupo = 'Vehiculos' ORDER BY subgrupo2 ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result))
	{
		$data[] = array(
			'Maquina' => $row['subgrupo2'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Valores']))
{
	$Today = date('Y-m-d');
	
	$query = "SELECT nombre, direccion, contacto_p, telefono_cp, telefono, email, credito, vigencia_credito, adicional, 
	vigencia_adicional, credito_activo, garantia, estado_cuenta, cobrador_codigo FROM clientes WHERE cliente_id = '".$_GET['Valores']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if ($row['vigencia_credito'] < $Today)
			$Credito = 0;
		else
			$Credito = $row['credito'];
		
		if ($row['vigencia_adicional'] < $Today)
			$Adicional = 0;
		else
			$Adicional = $row['adicional'];
		
		$Total = $Credito + $Adicional;
		
		//$Total = $row['credito'] + $row['adicional'];
		
		$data[] = array(
			'Nombre' => $row['nombre'],
			'Direccion' => $row['direccion'],
			'ContactoP' => $row['contacto_p'],
			'TelefonoCP' => $row['telefono_cp'],
			'Telefono' => $row['telefono'],
			'Email' => $row['email'],
			'CupoCR' => $Total,
			'Cupo_Credito' => $row['credito'],
			'Cupo_Adicional' => $row['adicional'],
			'Vigencia_Credito' => $row['vigencia_credito'],
			'Vigencia_Adicional' => $row['vigencia_adicional'],
			'CupoCR_Check' => $row['credito_activo'],
			'CupoAD_Check' => ($Adicional > 0) ? true:false,
			'Garantia' => $row['garantia'],
			'EstadoC' => $row['estado_cuenta'],
			'CobradorID' => $row['cobrador_codigo'],
		);
	}
	echo json_encode($data);
}
/*else if (isset($_GET['Valores2']))
{
	$query = "SELECT nombre, direccion, contacto_p, telefono, email FROM clientes WHERE cliente_id = '".$_GET['Valores2']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Nombre' => $row['nombre'],
			'Direccion' => $row['direccion'],
			'ContactoP' => $row['contacto_p'],
			'Telefono' => $row['telefono'],
			'Email' => $row['email'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Valores3']))
{
	$Today = date('Y-m-d');
	
	$query = "SELECT email, credito, credito_activo, estado_cuenta FROM clientes WHERE cliente_id = '".$_GET['Valores3']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Email' => $row['email'],
			'CupoCR' => $row['credito'],
			'CupoCR_Check' => $row['credito_activo'],
			'EstadoC' => $row['estado_cuenta'],
		);
	}
	echo json_encode($data);
}*/
else if (isset($_GET['Cargar_Garantias']))
{
	$query = "SELECT * FROM clientes_garant WHERE cliente_id = '".$_GET['Cargar_Garantias']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Apply' => $row['ok'],
				'Garantia' => $row['garantia'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET["Cargar_Cartera"]))
{
	$Today = date("Y-m-d");
	$Until30 = date("Y-m-d", strtotime("-30 days"));
	$Until45 = date("Y-m-d", strtotime("-45 days"));
	$Until60 = date("Y-m-d", strtotime("-60 days"));
	$Until90 = date("Y-m-d", strtotime("-90 days"));
	$DeudaTotal = 0;
	$Corriente = 0;
	$De30a45 = 0;
	$De45a60 = 0;
	$De60a90 = 0;
	$Mas90 = 0;
	
	$query = "SELECT id, cliente_id, fecha, saldo FROM mov_clientes WHERE saldo > '0' AND cliente_id = '".$_GET["Cargar_Cartera"]."' 
	AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			//echo "id:".$row["id"]."<br/>";
			//echo "saldo:".$row["saldo"]."<br/>";
			//echo "fecha:".$row["fecha"]."<br/>";
			$DeudaTotal += $row["saldo"];
			
			if ($row["fecha"] >= $Until30 && $row["fecha"] <= $Today)
				$Corriente += $row["saldo"];
			else if ($row["fecha"] >= $Until45 && $row["fecha"] <= $Until30)
				$De30a45 += $row["saldo"];
			else if ($row["fecha"] >= $Until60 && $row["fecha"] <= $Until45)
				$De45a60 += $row["saldo"];
			else if ($row["fecha"] >= $Until90 && $row["fecha"] <= $Until60)
				$De60a90 += $row["saldo"];
			else if ($row["fecha"] <= $Until90)
				$Mas90 += $row["saldo"];
			else// Deuda Futura?
				$Corriente += $row["saldo"];
		}
	}
	//echo "<br/> Result: ".mysql_num_rows($result)."<br/>";
	/*
	$query1 = "SELECT saldo FROM mov_clientes WHERE saldo > '0' AND DATE(fecha) BETWEEN '".$Until30."' AND '".$Today."' AND 
	cliente_id = '".$_GET['Cargar_Cartera']."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
	$result1 = mysql_query($query1) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result1) > 0)
	{
		while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
		{
			$Corriente = $Corriente + $row1["saldo"];
			//echo "De 0 a 30 Dias: ".$Corriente."<br/>";
		}
	}
	
	$query2 = "SELECT saldo FROM mov_clientes WHERE saldo > '0' AND DATE(fecha) BETWEEN '".$Until45."' AND '".$Until30."' AND 
	cliente_id = '".$_GET['Cargar_Cartera']."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
	$result2 = mysql_query($query2) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result2) > 0)
	{
		while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
		{
			$De30a45 = $De30a45 + $row2["saldo"];
			//echo "De 30 a 45 Dias: ".$De30a45."<br/>";
		}
	}

	$query3 = "SELECT saldo FROM mov_clientes WHERE saldo > '0' AND DATE(fecha) BETWEEN '".$Until60."' AND '".$Until45."' 
	AND cliente_id = '".$_GET['Cargar_Cartera']."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
	$result3 = mysql_query($query3) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result3) > 0)
	{
		while ($row3 = mysql_fetch_array($result3, MYSQL_ASSOC))
		{
			$De45a60 = $De45a60 + $row3["saldo"];
			//echo "De 45 a 60 Dias: ".$De45a60."<br/>";
		}
	}

	$query4 = "SELECT saldo FROM mov_clientes WHERE saldo > '0' AND DATE(fecha) BETWEEN '".$Until90."' AND '".$Until60."' 
	AND cliente_id = '".$_GET['Cargar_Cartera']."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
	$result4 = mysql_query($query4) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	if (mysql_num_rows($result4) > 0)
	{
		while ($row4 = mysql_fetch_array($result4, MYSQL_ASSOC))
		{
			$De60a90 = $De60a90 + $row4["saldo"];
			//echo "De 60 a 90 Dias: ".$De60a90."<br/>";
		}
	}
	$query5 = "SELECT saldo FROM mov_clientes WHERE saldo > '0' AND DATE(fecha) < '".$Until90."' AND 
	cliente_id = '".$_GET['Cargar_Cartera']."' AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
	$result5 = mysql_query($query5) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
	if (mysql_num_rows($result5) > 0)
	{
		while ($row5 = mysql_fetch_array($result5, MYSQL_ASSOC))
		{
			$Mas90 = $Mas90 + $row5["saldo"];
			//echo "Mas de 90: ".$Mas90."<br/>";
		}
	}
	
	$query1 = "SELECT saldo FROM mov_clientes WHERE saldo > '0' AND cliente_id = '".$_GET['Cargar_Cartera']."' 
	AND tipo_movimiento = 'Debito' AND estado = 'Aprobado'";
	$result1 = mysql_query($query1) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
	while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
	{
		$DeudaTotal = $DeudaTotal + $row1["saldo"];
	}
	//echo "Deuda Total: ".$DeudaTotal."<br/>";
	*/
	$data[] = array(
		'Corriente' => $Corriente,
		'De30a45' => $De30a45,
		'De45a60' => $De45a60,
		'De60a90' => $De60a90,
		'Mas90' => $Mas90,
		'DeudaTotal' => $DeudaTotal,
	);
	echo json_encode($data);
}
else if (isset($_GET['Ventas_Modificar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Ventas" && $data_session[$i]["SubModulo"] == "Modificar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}

	$query = "SELECT interno, orden_compra FROM fact_final WHERE estado = 'Creado' OR estado = 'Cotizacion' 
	AND digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'OrdenCompra' => $row['orden_compra'],
				'Interno' => $row['interno'],
			);
		}
	}
	else {
		$data[] = array();
	}
	echo json_encode($data);
}
else if (isset($_GET['Compras_Modificar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Compras" && $data_session[$i]["SubModulo"] == "Modificar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT interno, entrada FROM compras_final WHERE interno != '' AND estado = 'Creado' OR 
	estado = 'Pedido' AND digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Interno' => $row['interno'],
				'Entrada' => $row['entrada'],
			);
		}
	}
	else
	{
		$data[] = array();
	}
	echo json_encode($data);
}
else if (isset($_GET['Compras_Ingresar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Compras" && $data_session[$i]["SubModulo"] == "Ingresar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT interno, entrada FROM compras_final WHERE interno != '' AND estado = 'Autorizado' 
	AND digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Entrada' => $row['entrada'],
			'Interno' => $row['interno'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Compras_Ajustes']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Compras" && $data_session[$i]["SubModulo"] == "Ajustar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT interno, entrada FROM compras_final WHERE interno != '' AND estado = 'Aprobado' 
	AND sub_total != '0' AND digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Entrada' => $row['entrada'],
			'Interno' => $row['interno'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Ventas_Despachar']))
{	
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Ventas" && $data_session[$i]["SubModulo"] == "Despacho" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT interno, orden_compra, cliente_id, tipo_pedido FROM fact_final WHERE 
	estado = 'Autorizado' AND digitado_por LIKE '%".$UserCode."%' ORDER BY fecha_remision ASC";
	
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$Interno = array();
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Interno[] = $row["interno"];
		}
		
		$Clause = implode("', '", $Interno);
		
		$query = "SELECT DISTINCT interno FROM produccion_final 
		WHERE interno IN ('".$Clause."') AND estado = 'Pendiente'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$ProduccionOrden[$row["interno"]] = $row["interno"];
			}
		}
		
		while ($row = mysql_fetch_array($result2, MYSQL_ASSOC))
		{
			if (!isset($ProduccionOrden[$row["interno"]]))
			{
				$data[] = array(
					'Interno' => $row['interno'],
					'OrdenCompra' => $row['orden_compra'],
				);
			}
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Ventas_Ajustar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Ventas" && $data_session[$i]["SubModulo"] == "DespachoMod" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$Contains = isset($_GET['Contains']) ? $_GET['Contains']:"";
	$Limit = isset($_GET['Limit']) ? $_GET['Limit']:"";
	
	$Fecha_Ini = date('Y-m-d', strtotime('-2 year'));
	$Fecha_Fin = date('Y-m-d');
	
	$query = "SELECT interno, orden_compra FROM fact_final WHERE DATE(fecha_remision) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' 
	AND estado = 'Aprobado' AND digitado_por LIKE '%".$UserCode."%' AND orden_compra LIKE '%".$Contains."%'";
	$query .= " ORDER BY fecha_remision ASC";
	$query .= $Limit > 0 ? " LIMIT ".$Limit:"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Interno' => $row['interno'],
				'OrdenCompra' => $row['orden_compra'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Ventas_Autorizar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Ventas" && $data_session[$i]["SubModulo"] == "Autorizar_Asignar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
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
	if ($UserCode == "")
		$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	else
		$query .= " AND vendedor_codigo = '".$UserCode."'";
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= " ORDER BY fecha_remision DESC";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				'Autorizar' => false,
				'ClienteID' => $row['cliente_id'],
				'Interno' => $row['interno'],
				'Estado' => $row['estado'],
				'Motivo_Anulado' => $row['motivo_anulado'],
				'Fecha' => "".$row['fecha_remision']." 00:00:00",
				'Ord_Compra' => $row['orden_compra'],
				'Ord_Produccion' => $row['orden_produccion'],
				'Remision' => $row['remision'],
				'Factura' => $row['factura'],
				'Valor' => $row['total'],
				'F_Pago' => $row['forma_pago'],
				'Cliente' => $row['cliente_id'],
				'Enviado' => $row['enviado'],
				'VendedorID' => $row['vendedor_codigo'],
				'CobradorID' => $row['cobrador_codigo'],
				'DigitadorID' => $row['digitado_por'],
				'AutorizadorID' => $row['autorizado_por'],
				'AprobadorID' => $row['aprobado_por'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array ();
		echo json_encode($data);
	}
}
else if (isset($_GET['Compras_Autorizar']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"Creado";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM compras_final WHERE estado = '".$Estado."' AND sub_total != '0' 
	AND DATE(fecha_compra) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= isset($_GET['ClienteID']) ? " AND compras_final.cliente_id LIKE '%".$_GET['ClienteID']."%'":"";
	$query .= isset($_GET['Entrada']) ? " AND entrada LIKE '%".$_GET['Entrada']."%'":"";
	$query .= isset($_GET['DigitadorID']) ? " AND compras_final.digitado_por LIKE '%".$_GET['DigitadorID']."%'":"";
	$query .= ";";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0)
	{	
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Autorizar' => false,
				'ClienteID' => $row['cliente_id'],
				'Estado' => $row['estado'],
				'Motivo_Anulado' => $row['motivo_anulado'],
				'Fecha' => "".$row['fecha_compra']." 00:00:00",
				'Interno' => $row['interno'],
				'Entrada' => $row['entrada'],
				'Doc_Transp' => $row['doc_transp'],
				'Factura' => $row['factura'],
				'Pedido' => $row['pedido'],
				'Valor' => $row['total'],
				'F_Pago' => $row['forma_pago'],
				'Cliente' => $ClienteNombre[$row['cliente_id']],
				'DigitadorID' => $row['digitado_por'],
				'AutorizadorID' => $row['autorizado_por'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array ();
		echo json_encode($data);
	}
}
else if (isset($_GET['Cartera_AplicarPago']))
{
	$query = "SELECT * FROM mov_clientes WHERE saldo > '0' AND estado = 'Aprobado' AND 
	cliente_id = '".$_GET['Cartera_AplicarPago']."' AND tipo_movimiento = 'Debito' ORDER BY fecha ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Apply' => false,
				'Fecha' => $row['fecha'],
				'Interno' => $row['interno'],
				'Remision' => $row['remision'],
				'Factura' => $row['factura'],
				'Ord_Compra' => $row['orden_compra'],
				'Valor' => $row['valor'],
				'Saldo' => $row["saldo"],
				'Abono' => 0,
				'Saldo_Pendiente' => 0,
				'Vendedor' => $row['vendedor_codigo'],
				'Cobrador' => $row['cobrador_codigo'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Cartera_Movimientos']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Cartera" && $data_session[$i]["SubModulo"] == "Movimientos" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM mov_clientes WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['TipoMovimiento']) && $_GET['TipoMovimiento'] != "") ? " AND tipo_movimiento = '".$_GET['TipoMovimiento']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	
	if ($UserCode == "")
		$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	else
		$query .= " AND cobrador_codigo = '".$UserCode."'";
	
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['Caja_Recibo']) && $_GET['Caja_Recibo'] != "") ? " AND caja_recibo LIKE '%".$_GET['Caja_Recibo']."%'":"";
	$query .= "ORDER BY fecha DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Estado' => $row['estado'],
				'ID' => $row['id'],
				'Motivo_Anulado' => $row['motivo_anulado'],
				'Fecha' => $row['fecha'],
				'Interno' => $row['interno'],
				'Ord_Compra' => $row['orden_compra'],
				'Remision' => $row['remision'],
				'Factura' => $row['factura'],
				'Caja_Interno' => $row['caja_interno'],
				'Caja_Recibo' => $row['caja_recibo'],
				'TipoMovimiento' => $row['tipo_movimiento'],
				'Valor' => $row['valor'],
				'Saldo' => $row['saldo'],
				'Cliente' => $row['cliente_id'],
				'ClienteID' => $row['cliente_id'],
				'VendedorID' => $row['vendedor_codigo'],
				'CobradorID' => $row['cobrador_codigo'],
				'DigitadorID' => $row['digitado_por'],
				'AutorizadorID' => $row['aprobado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Cartera_Movimientos_Deuda']))
{
	if (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "")
	{
		$Tmp = Cargar_Deuda_Cartera($_GET['ClienteID']);
		echo $Tmp[0]['DeudaTotal'];
	}
}
else if (isset($_GET['Cartera_Movimientos_Deuda_Total']))
{
	$DeudaTotal = 0;
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM mov_clientes WHERE saldo > '0' AND DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
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
		while ($row = mysql_fetch_array($result))
		{
			$DeudaTotal = $DeudaTotal + $row["saldo"];
			//echo "Deuda: ".$row["saldo"]."<br/>";
		}
	}
	//echo "Deuda Total: ".$DeudaTotal."<br/> resultados: ".mysql_num_rows($result);
	//die();
	$data[] = array(
		'Deuda_Total' => $DeudaTotal,
	);
	echo json_encode($data);
}
else if (isset($_GET['Cartera_Listado']))
{
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
		
		$ClienteNombre = ClientesNombre();
		
		foreach($data as $item)
		{
			$Clientes[] = $item["ClienteID"];
			$newdata[] = array(
				"Nombre" => $ClienteNombre[$item["ClienteID"]],
				"ClienteID" => $item["ClienteID"],
				"Deuda" => $Deuda[$item["ClienteID"]],
				"Compra" => $Compra[$item["ClienteID"]],
				"Fecha" => $Fecha[$item["ClienteID"]],
			);
		}
		sort($newdata);
		echo json_encode($newdata);
	}
}
else if (isset($_GET['Cartera_Edades']))
{
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
			else if ($row["fecha"] >= $Until45 && $row["fecha"] <= $Until30)
			{
				if (!isset($A45[$row["cliente_id"].$row["cobrador_codigo"]]))
					$A45[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$A45[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_A45 += $row["saldo"];
			}
			else if ($row["fecha"] >= $Until60 && $row["fecha"] <= $Until45)
			{
				if (!isset($A60[$row["cliente_id"].$row["cobrador_codigo"]]))
					$A60[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$A60[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_A60 += $row["saldo"];
			}
			else if ($row["fecha"] >= $Until90 && $row["fecha"] <= $Until60)
			{
				if (!isset($A90[$row["cliente_id"].$row["cobrador_codigo"]]))
					$A90[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$A90[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_A90 += $row["saldo"];
			}
			else if ($row["fecha"] <= $Until90)
			{
				if (!isset($Mas90[$row["cliente_id"].$row["cobrador_codigo"]]))
					$Mas90[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$Mas90[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_Mas90 += $row["saldo"];
			}
			else //Deuda Futura
			{
				if (!isset($A30[$row["cliente_id"].$row["cobrador_codigo"]]))
					$A30[$row["cliente_id"].$row["cobrador_codigo"]] = $row["saldo"];
				else
					$A30[$row["cliente_id"].$row["cobrador_codigo"]] += $row["saldo"];
				$Total_A30 += $row["saldo"];
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
		echo json_encode($newdata);
	}
	die();
	
	/*if (count($data) > 0)
	{
		foreach($data as $item)
		{
			$Clientes[] = $item["ClienteID"];
		}
		
		$Clause = implode("', '", $Clientes);
		
		if ($CobradorID == "")
		{
			$query = "SELECT cliente_id, cobrador_codigo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') 
			AND tipo_movimiento = 'Debito' AND estado = 'Aprobado' ORDER BY cliente_id, fecha DESC";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_array($result))
				{
					if (isset($TmpID[$row["cliente_id"]]))
						continue;
					
					$TmpID[$row["cliente_id"]] = 1;
					
					//if ($CobradorID != "")
					//{
					//	if ($row["cobrador_codigo"] != $CobradorID)
					//		continue;
					//}
				
					$Cobradores[$row["cliente_id"]] = $row["cobrador_codigo"];
				}
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		tipo_movimiento = 'Debito' AND estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id, cobrador_codigo";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Deuda[$row["cliente_id"]] = $row["saldo"];
				$DeudaCobrador[$row["cliente_id"]] = $row["cobrador_codigo"];
				$Total += $row["saldo"];
			}
		}
		else
		{
			die();
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '".$Until30."' AND '".$Today."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id, cobrador_codigo";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A30[$row["cliente_id"]] = $row["saldo"];
				$A30Cobrador[$row["cliente_id"]] = $row["cobrador_codigo"];
				$Total_A30 += $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '".$Until45."' AND '".$Until30."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id, cobrador_codigo";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A45[$row["cliente_id"]] = $row["saldo"];
				$A45Cobrador[$row["cliente_id"]] = $row["cobrador_codigo"];
				$Total_A45 += $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '".$Until60."' AND '".$Until45."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id, cobrador_codigo";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A60[$row["cliente_id"]] = $row["saldo"];
				$A60Cobrador[$row["cliente_id"]] = $row["cobrador_codigo"];
				$Total_A60 += $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '".$Until90."' AND '".$Until60."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id, cobrador_codigo";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A90[$row["cliente_id"]] = $row["saldo"];
				$A90Cobrador[$row["cliente_id"]] = $row["cobrador_codigo"];
				$Total_A90 += $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM mov_clientes WHERE cliente_id IN ('".$Clause."') AND 
		DATE(fecha) BETWEEN '0000-00-00' AND '".$Until90."' AND tipo_movimiento = 'Debito' AND 
		estado = 'Aprobado' AND saldo > '0' GROUP BY cliente_id, cobrador_codigo";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Mas90[$row["cliente_id"]] = $row["saldo"];
				$Mas90Cobrador[$row["cliente_id"]] = $row["cobrador_codigo"];
				$Total_Mas90 += $row["saldo"];
			}
		}
		
		foreach($data as $item)
		{
			if ($CobradorID != "")
				$Cobrador = $CobradorID;
			else if (isset($Cobradores[$item["ClienteID"]]))
				$Cobrador = $Cobradores[$item["ClienteID"]];
			else
				$Cobrador = "No Existe!";//continue;
			
			$newdata[] = array(
				"Cliente" => isset($ClienteNombre[$item["ClienteID"]]) ? $ClienteNombre[$item["ClienteID"]]:"No Existe!",
				"ClienteID" => $item["ClienteID"],
				"CobradorID" => $Cobrador,
				"Deuda" => $Deuda[$item["ClienteID"]],
				"A30" => isset($A30[$item["ClienteID"]]) ? $A30[$item["ClienteID"]]:0,
				"A45" => isset($A45[$item["ClienteID"]]) ? $A45[$item["ClienteID"]]:0,
				"A60" => isset($A60[$item["ClienteID"]]) ? $A60[$item["ClienteID"]]:0,
				"A90" => isset($A90[$item["ClienteID"]]) ? $A90[$item["ClienteID"]]:0,
				"Mas90" => isset($Mas90[$item["ClienteID"]]) ? $Mas90[$item["ClienteID"]]:0,
				"Total" => $Total,
				"Total_A30" => $Total_A30,
				"Total_A45" => $Total_A45,
				"Total_A60" => $Total_A60,
				"Total_A90" => $Total_A90,
				"Total_Mas90" => $Total_Mas90,
			);
		}
		sort($newdata);
		echo json_encode($newdata);
	}*/
}
else if (isset($_GET["Cartera_Aplicar_Log"]))
{
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$Caja_Interno = isset($_GET["Caja_Interno"]) ? $_GET["Caja_Interno"]:"";
	
	$query = "SELECT * FROM cartera_aplicar_log WHERE cliente_id = '".$ClienteID."' AND caja_interno = '".$Caja_Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"Interno" => $row["interno"],
				"Ord_Compra" => $row["orden_compra"],
				"Remision" => $row["remision"],
				"Factura" => $row["factura"],
				"Caja_Recibo" => $row["caja_recibo"],
				"Valor" => $row["valor"],
				"Abono" => $row["abono"],
				"Saldo" => $row["saldo"],
				"Saldo_Nuevo" => $row["saldo_nuevo"],
			);
		}
	}
	
	if (isset($data))
		echo json_encode($data);
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['CxP_Modificar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "CxP" && $data_session[$i]["SubModulo"] == "Modificar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT compra_interno, factura, tipo_movimiento, COUNT(*) AS num FROM cxp_movs WHERE compra_interno LIKE 'COMP%' 
	AND estado = 'Aprobado' AND digitado_por LIKE '%".$UserCode."%' GROUP BY compra_interno HAVING num = 1 ORDER BY fecha_digitado DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	//echo mysql_num_rows($result)."<br/>";
	while ($row = mysql_fetch_array($result))
	{
		if ($row['tipo_movimiento'] != "Compra")
			continue;
		
		$data[] = array(
			'Interno' => $row['compra_interno'],
			'Factura' => $row['factura'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['CxP_Cargar_Datos']))
{
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	
	$query = "SELECT * FROM cxp_movs WHERE compra_interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		if (strncmp($Interno, "COMP", 4) == 0)
			$query = "SELECT fecha_compra AS fecha, notas, observaciones FROM compras_final WHERE interno = '".$Interno."' ";
		else
		{
			$data[] = array ();
			echo json_encode($data);
			die();
		}
		
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result2) > 0)
		{
			while ($row2 = mysql_fetch_array($result2))
			{
				$data[] = array (
					'Fecha' => $row2["fecha"],
					'Interno' => $row["compra_interno"],
					'Factura' => $row["factura"],
					'Grupo' => $row["grupo"],
					'SubGrupo' => $row["subgrupo"],
					'SubGrupo2' => $row["subgrupo2"],
					'ClienteID' => $row["cliente_id"],
					'Valor' => $row["valor"],
					'Saldo' => $row["saldo"],
					'Notas' => $row2["notas"],
					'Observaciones' => $row2["observaciones"],
					'Digitado_Por' => $row["digitado_por"],
					'Fecha_Digitado' => $row["fecha_digitado"],
					'Aprobado_Por' => $row["aprobado_por"],
					'Fecha_Aprobado' => $row["fecha_aprobado"],
					'Modificado_Por' => $row["modificado_por"],
					'Fecha_Modificado' => $row["fecha_modificado"],
				);
			}
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array ();
		echo json_encode($data);
	}
}
else if (isset($_GET['CxP_Movimientos']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT *, SUBSTR(fecha_digitado, 1, 10) AS fecha_digitado FROM cxp_movs WHERE DATE(fecha_digitado) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['TipoMovimiento']) && $_GET['TipoMovimiento'] != "") ? " AND tipo_movimiento = '".$_GET['TipoMovimiento']."'":"";
	$query .= (isset($_GET['Compra_Interno']) && $_GET['Compra_Interno'] != "") ? " AND compra_interno LIKE '%".$_GET['Compra_Interno']."%'":"";
	$query .= (isset($_GET['Caja_Interno']) && $_GET['Caja_Interno'] != "") ? " AND caja_interno LIKE '%".$_GET['Caja_Interno']."%'":"";
	$query .= (isset($_GET['Compra_Entrada']) && $_GET['Compra_Entrada'] != "") ? " AND compra_entrada LIKE '%".$_GET['Compra_Entrada']."%'":"";
	$query .= "ORDER BY fecha_digitado DESC";
	//echo "".$query."<br/><br/>";
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				'ID' => $row['id'],
				'Estado' => $row['estado'],
				'Fecha' => "".$row['fecha_digitado']." 00:00:00",
				'Compra_Interno' => $row['compra_interno'],
				'Compra_Entrada' => $row['compra_entrada'],
				'Factura' => $row['factura'],
				'Doc_Transp' => $row['doc_transp'],
				'TipoMovimiento' => $row['tipo_movimiento'],
				'Valor' => $row['valor'],
				'Saldo' => $row['saldo'],
				'Cliente' => $row['cliente_id'],
				'ClienteID' => $row['cliente_id'],
				'Caja_Interno' => $row['caja_interno'],
				'Caja_Recibo' => $row['caja_recibo'],
				'DigitadorID' => $row['digitado_por'],
				'AutorizadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['CxP_Movimientos_Deuda']))
{
	$Deuda = 0;
	if (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "")
	{
		$Tmp = Cargar_Deuda_Compra($_GET['ClienteID']);
		echo $Tmp[0]['DeudaTotal'];
	}
}
else if (isset($_GET['CxP_Bancos']))
{
	$query = "SELECT DISTINCT banco FROM bancos ORDER BY banco";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Total = 0;
			
			$query = "SELECT * FROM bancos WHERE banco = '".$row["banco"]."' AND estado = 'Aprobado'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result1) > 0)
			{
				while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
				{
					if ($row1["tipo_mov"] == "Entrada")
						$Total += $row1["valor"];
					else
						$Total -= $row1["valor"];
				}
				
				if ($Total < 0)
					$Total = 0;
				
				$data[] = array (
					"Banco" => $row["banco"],
					"Saldo" => $Total,
				);
			}
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['CxP_Efectivo']))
{
	$query = "SELECT * FROM caja_final WHERE estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$Entrada = 0;
		$Salida = 0;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($row["categoria"] == "Ingresos")
				$Entrada += $row["efectivo"];
			
			if ($row["categoria"] == "Egresos")
				$Salida += $row["efectivo"];
		}
		
		$Saldo_Efectivo = ($Entrada - $Salida);
		$Saldo_Efectivo = round($Saldo_Efectivo);
		
		$data[] = array (
			"Banco" => "Caja_General",
			"Saldo" => $Saldo_Efectivo,
		);
		echo json_encode($data);
	}
	else
	{
		$data[] = array (
			"Banco" => "Caja_General",
			"Saldo" => 0,
		);
		echo json_encode($data);
	}
}
else if (isset($_GET['CxP_GetClients']))
{
	$data = BuscarClientesAPagar("");
	$num = count($data);
	
	if ($num > 0)
	{
		$query = "SELECT nombre, cliente_id FROM clientes ORDER BY nombre ASC";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Nombre[$row["cliente_id"]] = $row["nombre"];
		}
		
		foreach($data as $item)
		{
			$newdata[] = array(
				"Nombre" => isset($Nombre[$item["ClienteID"]]) ? $Nombre[$item["ClienteID"]]:"NoExiste/Modificado",
				"ClienteID" => $item["ClienteID"],
			);
		}
		sort($newdata);
		echo json_encode($newdata);
	}
	else {
		$newdata[] = array();
		echo json_encode($newdata);
	}
}
else if (isset($_GET['CxP_Cargar']))
{
	$Today = date('Y-m-d');
	$Until30 = date('Y-m-d', strtotime('-30 days'));
	$Until60 = date('Y-m-d', strtotime('-60 days'));
	$Until90 = date('Y-m-d', strtotime('-90 days'));
	$Until120 = date('Y-m-d', strtotime('-120 days'));
	$ClienteID = $_GET['CxP_Cargar'];
	if (1 > 0)
	{
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id = '".$ClienteID."' AND 
		tipo_movimiento = 'Compra' AND estado = 'Aprobado' AND saldo > '0'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Deuda[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id = '".$ClienteID."' 
		AND DATE(fecha_digitado) BETWEEN '".$Until30."' AND '".$Today."' AND tipo_movimiento = 'Compra' 
		AND estado = 'Aprobado' AND saldo > '0'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A30[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id = '".$ClienteID."' AND 
		DATE(fecha_digitado) BETWEEN '".$Until60."' AND '".$Until30."' AND tipo_movimiento = 'Compra' AND 
		estado = 'Aprobado' AND saldo > '0'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A60[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id = '".$ClienteID."' AND 
		DATE(fecha_digitado) BETWEEN '".$Until90."' AND '".$Until60."' AND tipo_movimiento = 'Compra' AND 
		estado = 'Aprobado' AND saldo > '0'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$A90[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$query = "SELECT cliente_id, SUM(saldo) AS saldo FROM cxp_movs WHERE cliente_id = '".$ClienteID."' AND 
		DATE(fecha_digitado) BETWEEN '0000-00-00' AND '".$Until90."' AND tipo_movimiento = 'Compra' AND 
		estado = 'Aprobado' AND saldo > '0'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Mas90[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$newdata[] = array(
			"Deuda" => isset($Deuda[$ClienteID]) ? $Deuda[$ClienteID]:0,
			"A30" => isset($A30[$ClienteID]) ? $A30[$ClienteID]:0,
			"A60" => isset($A60[$ClienteID]) ? $A60[$ClienteID]:0,
			"A90" => isset($A90[$ClienteID]) ? $A90[$ClienteID]:0,
			"Mas90" => isset($Mas90[$ClienteID]) ? $Mas90[$ClienteID]:0,
		);

		echo json_encode($newdata);
	}
}
else if (isset($_GET['CxP_AplicarPago']))
{
	$query = "SELECT * FROM cxp_movs WHERE saldo > '0' AND estado = 'Aprobado' AND 
	cliente_id = '".$_GET['CxP_AplicarPago']."' AND tipo_movimiento = 'Compra' ORDER BY fecha_digitado ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Apply' => false,
				'Fecha' => $row['fecha_digitado'],
				'Interno' => $row['compra_interno'],
				'Entrada' => $row['compra_entrada'],
				'Factura' => $row['factura'],
				'Doc_Transp' => $row['doc_transp'],
				'Valor' => $row['valor'],
				'Saldo' => $row["saldo"],
				'Abono' => 0,
				'Saldo_Pendiente' => 0,
				'Digitador' => $row["digitado_por"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['CxP_Listado']))
{
	$data = BuscarClientesAPagar("");
	$num = count($data);
	
	if ($num > 0)
	{
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
		
		$ClienteNombre = ClientesNombre();
		
		foreach($data as $item)
		{
			$newdata[] = array(
				"Nombre" => $ClienteNombre[$item["ClienteID"]],
				"ClienteID" => $item["ClienteID"],
				"Deuda" => $Deuda[$item["ClienteID"]],
				"Compra" => $Compra[$item["ClienteID"]],
				"Fecha" => $Fecha[$item["ClienteID"]],
			);
		}
		sort($newdata);
		echo json_encode($newdata);
	}
}
else if (isset($_GET['CxP_Edades']))
{
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
		
		$ClienteNombre = ClientesNombre();
		
		foreach($data as $item)
		{
			$newdata[] = array(
				"Nombre" => $ClienteNombre[$item["ClienteID"]],
				"ClienteID" => $item["ClienteID"],
				"Deuda" => $Deuda[$item["ClienteID"]],
				"A30" => isset($A30[$item["ClienteID"]]) ? $A30[$item["ClienteID"]]:0,
				"A60" => isset($A60[$item["ClienteID"]]) ? $A60[$item["ClienteID"]]:0,
				"A90" => isset($A90[$item["ClienteID"]]) ? $A90[$item["ClienteID"]]:0,
				"Mas90" => isset($Mas90[$item["ClienteID"]]) ? $Mas90[$item["ClienteID"]]:0,
			);
		}
		sort($newdata);
		echo json_encode($newdata);
	}
}
else if (isset($_GET['Caja_Modificar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Caja" && $data_session[$i]["SubModulo"] == "Modificar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	if ($data_session[0]["Lvl"] == "Administrador")
		$query = "SELECT caja_interno, caja_recibo FROM caja_final";
	else
		$query = "SELECT caja_interno, caja_recibo FROM caja_final WHERE estado = 'Pendiente' AND digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	while ($row = mysql_fetch_array($result))
	{
		$data[] = array(
			'Caja_Recibo' => $row['caja_recibo'],
			'Caja_Interno' => $row['caja_interno'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Datos_Caja'])) // Revisar (Muchos movimientos, no usan el recibo como referencia si no el interno y el recibo termia siendo una combinacion de CODIGO+FECHA)
{
	$Caja_Interno = isset($_GET['Caja_Interno']) ? $_GET['Caja_Interno']:"";
	
	$query = "SELECT * FROM caja_final WHERE caja_interno = '".$Caja_Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$query = "SELECT * FROM bancos WHERE caja_interno = '".$Caja_Interno."' ";
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result2) > 0)
		{
			while ($row2 = mysql_fetch_array($result2))
			{
				if ($row2["tipo"] == "Canje de Cheque")
					continue;
				
				$Bancos[] = array(
					'Tipo' => $row2["tipo"],
					'Numero' => $row2["numero"],
					'Valor' => $row2["valor"],
					'Banco' => $row2["banco"],
				);
			}
		}
		else
		{
			$Bancos[] = array();
		}
		
		$query = "SELECT * FROM cheques WHERE caja_interno = '".$Caja_Interno."' ";
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		
		if (mysql_num_rows($result2) > 0)
		{
			while ($row2 = mysql_fetch_array($result2))
			{
				$Cheques[] = array(
					'Cheque' => $row2["cheque"],
					'Valor' => $row2["valor"],
					'Banco' => $row2["banco"],
					'Cuenta' => $row2["cuenta"],
					'Estado' => $row2["estado_cheque"],
					'Fecha' => $row2["fecha_cheque"],
				);
			}
		}
		else
		{
			$Cheques[] = array();
		}
		
		$data[] = array (
			'Fecha' => $row["fecha"],
			'Caja_Interno' => $row["caja_interno"],
			'Caja_Recibo' => $row["caja_recibo"],
			'CxP' => $row["cxp"],
			'Categoria' => $row["categoria"],
			'Grupo' => $row["grupo"],
			'SubGrupo' => $row["subgrupo"],
			'SubGrupo2' => $row["subgrupo2"],
			'ClienteID' => $row["cliente_id"],
			'Aplicado_A' => $row["aplicado_a"],
			'Observaciones' => $row["observaciones"],
			'Efectivo' => $row["efectivo"],
			'Rete_IVA' => $row["rete_iva"],
			'Rete_ICA' => $row["rete_ica"],
			'Rete_Fuente' => $row["rete_fuente"],
			'Descuento' => $row["descuento"],
			'Descuento_Concepto' => $row["concepto_dcto"],
			'Bancos' => isset($Bancos) ? $Bancos:array(),
			'Cheques' => isset($Cheques) ? $Cheques:array(),
			'Estado' => $row["estado"],
			'Digitado_Por' => $row["digitado_por"],
		);
		echo json_encode($data);
	}
	else
	{
		$data[] = array ();
		echo json_encode($data);
	}
}
else if (isset($_GET['Caja_Aprobar']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM caja_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Caja_Recibo']) && $_GET['Caja_Recibo'] != "") ? " AND caja_recibo LIKE '%".$_GET['Caja_Recibo']."%'":"";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND digitado_por = '".$_GET['DigitadorID']."'":"";
	$query .= "ORDER BY fecha DESC;";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{	
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Aprobar' => false,
				'Estado' => $row['estado'],
				'Fecha' => "".$row['fecha']." 00:00:00",
				'Caja_Interno' => $row['caja_interno'],
				'Caja_Recibo' => $row['caja_recibo'],
				'Dprov' => $row['cxp'],
				'Total' => $row['total'],
				'DigitadorID' => $row['digitado_por'],
				'Tipo' => $row['categoria'],
				'Cliente' => $row['cliente_id'],
				'ClienteID' => $row['cliente_id'],
				'Enviado' => "",
				'Efectivo' => $row['efectivo'],
				'Cheque' => $row['cheque'],
				'Consignacion' => $row['consignacion'],
				'Saldo' => $row['saldo'],
				'AprobadorID' => $row['aprobado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Caja_Cierre_Datos']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Caja_Recibo = isset($_GET['Caja_Recibo']) ? $_GET['Caja_Recibo']:"";
	if ($Estado == "Todos")
		$Estado = "";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM caja_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= $Estado != "" ? " AND estado = '".$Estado."'":"";
	$query .= $ClienteID != "" ? " AND cliente_id = '".$ClienteID."'":"";
	$query .= $Caja_Recibo != "" ? " AND caja_recibo LIKE '%".$Caja_Recibo."%'":"";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND caja_final.digitado_por = '".$_GET['DigitadorID']."'":"";
	$query .= ";";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$Total_Ingresos = 0;
		$Total_Egresos = 0;
		$Recaudos_Cartera = 0;
		$Recaudos_Cheques = 0;
		$Retenciones = 0;
		$Descuentos = 0;
		$Total_Efectivo = 0;
		$Total_Cheques = 0;
		$Total_Tarjetas = 0;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($row["categoria"] == "Ingresos")
			{
				$Total_Ingresos += $row["total"];
				$Total_Efectivo += $row["efectivo"];
				$Descuentos += $row["rete_iva"] + $row["rete_ica"] + $row["rete_fuente"] + $row["descuento"];
			}
			
			if ($row["categoria"] == "Egresos")
			{
				$Total_Egresos += $row["total"];
				$Retenciones += $row["rete_iva"] + $row["rete_ica"] + $row["rete_fuente"] + $row["descuento"];
			}
			
			if (stristr($row["subgrupo2"],"Cheque Devuelto") != "")
				$Recaudos_Cheques += $row["total"];
			else
				$Recaudos_Cartera += $row["total"];
			
			$Total_Cheques += $row["cheque"];
			$Total_Tarjetas += $row["consignacion"];
		}
		
		$data[] = array(
			'Total_Ingresos' => $Total_Ingresos,
			'Total_Egresos' => $Total_Egresos,
			'Recaudos_Cartera' => ($Recaudos_Cartera - $Total_Egresos),
			'Recaudos_Cheques' => $Recaudos_Cheques,
			'Retenciones' => $Retenciones,
			'Descuentos' => $Descuentos,
			'Total_Efectivo' => ($Total_Efectivo - $Total_Egresos + $Retenciones),
			'Total_Cheques' => $Total_Cheques,
			'Saldo_Caja' => ($Total_Ingresos - $Total_Egresos),
			'Total_Tarjetas' => $Total_Tarjetas,
		);
		echo json_encode($data);
	}
	else
	{
		$data[] = array(
			'Total_Ingresos' => "",
			'Total_Egresos' => "",
			'Recaudos_Cartera' => "",
			'Recaudos_Cheques' => "",
			'Retenciones' => "",
			'Descuentos' => "",
			'Total_Efectivo' => "",
			'Total_Cheques' => "",
			'Saldo_Caja' => "",
			'Total_Tarjetas' => "",
		);
		echo json_encode($data);
	}
}
else if (isset($_GET['Caja_Cierre_Todo']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	if ($Estado == "Todos")
		$Estado = "";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM caja_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= $Estado != "" ? " AND estado = '".$Estado."'":"";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND caja_final.digitado_por = '".$_GET['DigitadorID']."'":"";
	$query .= ";";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Fecha' => $row['fecha']." 00:00:00",
				'Caja_Interno' => $row['caja_interno'],
				'Caja_Recibo' => $row['caja_recibo'],
				'Categoria' => $row['categoria'],
				'Cliente' => $row['cliente_id'],
				'Total' => $row['total'],
				'Efectivo' => $row['efectivo'],
				'DigitadorID' => $row['digitado_por'],
				'AutorizadorID' => $row['aprobado_por'],
			);
		}
		echo json_encode($data);
	}
	/*else
	{
		$data[] = array();
		echo json_encode($data);
	}*/
}
else if (isset($_GET['Caja_Cierre_Cheques_Dev']))
{
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	if ($Estado == "Todos")
		$Estado = "";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM caja_final WHERE subgrupo2 LIKE '%Cheque Devuelto%' AND DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= $Estado != "" ? " AND estado = '".$Estado."'":"";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND digitado_por = '".$_GET['DigitadorID']."'":"";
	$query .= ";";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Fecha' => $row['fecha']." 00:00:00",
				'Caja_Interno' => $row['caja_interno'],
				'Caja_Recibo' => $row['caja_recibo'],
				'Categoria' => $row['categoria'],
				'Cliente' => $row['cliente_id'],
				'Total' => $row['total'],
				'Efectivo' => $row['efectivo'],
				'DigitadorID' => $row['digitado_por'],
				'AutorizadorID' => $row['aprobado_por'],
			);
		}
		echo json_encode($data);
	}
	/*else
	{
		$data[] = array();
		echo json_encode($data);
	}*/
}
else if (isset($_GET['Caja_Cierre_Cheques']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM cheques WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND digitado_por = '".$_GET['DigitadorID']."'":"";
	$query .= ";";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Fecha' => $row['fecha']." 00:00:00",
				'Caja_Recibo' => $row['caja_recibo'],
				'NumeroCheque' => $row['cheque'],
				'Valor' => $row['valor'],
				'Banco' => $row['banco'],
				'NumeroCuenta' => $row['cuenta'],
				'FechaCheque' => $row['fecha_cheque'],
				'Cliente' => $row['cliente_id'],
				'DigitadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
	/*else
	{
		$data[] = array();
		echo json_encode($data);
	}*/
}
else if (isset($_GET['Caja_Cierre_Consignaciones']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM bancos WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND digitado_por = '".$_GET['DigitadorID']."'":"";
	$query .= ";";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Fecha' => $row['fecha']." 00:00:00",
				'Caja_Recibo' => $row['caja_recibo'],
				'Tipo' => $row['tipo'],
				'Banco' => $row['banco'],
				'NumeroAprobacion' => $row['numero'],
				'Valor' => $row['valor'],
				'Cliente' => $row['cliente_id'],
				'DigitadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
	/*else
	{
		$data[] = array();
		echo json_encode($data);
	}*/
}
else if (isset($_GET['Caja_General']))
{
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
		$Saldo_Bancos = round($Saldo_Bancos);
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
		$Cheques_AlDia = round($Cheques_AlDia);
		$Cheques_PostFechados = round($Cheques_PostFechados);
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
		
		$Total_Ingresos = round($Total_Ingresos);
		$Total_Egresos = round($Total_Egresos);
		
		//$Saldo_Efectivo = ($Total_Ingresos - $Total_Egresos) - $Saldo_Bancos - $Cheques_AlDia - $Cheques_PostFechados;
		$Saldo_Efectivo = ($Entrada - $Salida);
		$Saldo_Efectivo = round($Saldo_Efectivo);
		
		$data[] = array(
			'Saldo_Efectivo' => $Saldo_Efectivo,
			'Total_Ingresos' => $Total_Ingresos,
			'Total_Egresos' => $Total_Egresos,
			'Saldo_Bancos' => $Saldo_Bancos,
			'Cheques_AlDia' => $Cheques_AlDia,
			'Cheques_PostFechados' => $Cheques_PostFechados,
		);
		echo json_encode($data);
	}
	else
	{
		$data[] = array(
			'Saldo_Efectivo' => "",
			'Total_Ingresos' => "",
			'Total_Egresos' => "",
			'Saldo_Bancos' => "",
			'Cheques_AlDia' => "",
			'Cheques_PostFechados' => "",
		);
		echo json_encode($data);
	}
}
else if (isset($_GET['Caja_General_Bancos'])) // DONE? // Arreglar DB por duplicados? SELECT caja_interno, numero, valor, COUNT(*) c FROM bancos GROUP BY caja_interno HAVING c > 1;
{
	$query = "SELECT DISTINCT banco FROM bancos ORDER BY banco";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{	
			$Total = 0;
			
			$query = "SELECT * FROM bancos WHERE banco = '".$row["banco"]."' AND estado = 'Aprobado'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result1) > 0)
			{
				while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
				{
					if ($row1["tipo_mov"] == "Entrada")
						$Total += $row1["valor"];
					else
						$Total -= $row1["valor"];
				}
				
				/*if ($Total < 0)
					$Total = 0;*/
				
				$data[] = array (
					"Banco" => $row["banco"],
					"Saldo" => $Total,
				);
				
				
			}
		}
		echo json_encode($data);
	}
}
else if (isset($_GET["Caja_Recibo_Info"]))
{
	$data = Recibo_Info($_GET["Caja_Recibo_Info"]);
	echo json_encode($data);
}
else if (isset($_GET['Clientes_Nomina']))
{
	$Nomina = ClientesNomina("");
	echo json_encode($Nomina);
}
else if (isset($_GET['Nomina_Extras']))
{	
	$query = "SELECT * FROM nom_extras WHERE estado = 'Pendiente'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Interno' => $row["interno"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Extras_Modificar']))
{
	$Interno = isset($_GET['Nomina_Extras_Modificar']) ? $_GET['Nomina_Extras_Modificar']:die();
	
	$query = "SELECT *, SUBSTR(hora_ini, 1, 5) AS hora_ini, SUBSTR(hora_fin, 1, 5) AS hora_fin FROM nom_extras_movs WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			$GridData[] = array(
				'Turno' => $row["turno"],
				'Hora_Ini' => $row["hora_ini"],
				'Hora_Fin' => $row["hora_fin"],
				'Total' => $row["total"],
				'Nocturno' => $row["nocturno"],
				'Festivo' => $row["festivo"],
			);
		}
	}
	
	$query = "SELECT * FROM nom_extras WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$data[] = array(
			'EmpleadoID' => $row["empleado_id"],
			'AutorizadorID' => $row["autorizador_id"],
			'Justificacion' => $row["justificacion"],
			'Comentario' => $row["comentario"],
			'Observacion' => $row["observacion"],
			'Estado' => $row["estado"],
			'Digitado_Por' => $row["digitado_por"],
			'Fecha_Digitado' => $row["fecha_digitado"],
			'Aprobado_Por' => $row["aprobado_por"],
			'Modificado_Por' => $row["modificado_por"],
			'GridData' => isset($GridData) ? $GridData:array(),
		);
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Prestamos']))
{	
	$query = "SELECT * FROM nom_prestamos WHERE estado = 'Pendiente'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Interno' => $row["interno"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Prestamos_Modificar']))
{
	$Interno = isset($_GET['Nomina_Prestamos_Modificar']) ? $_GET['Nomina_Prestamos_Modificar']:die();
	
	$query = "SELECT * FROM nom_prestamos WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$data[] = array(
			'BeneficiarioID' => $row["beneficiario_id"],
			'AcreedorID' => $row["acreedor_id"],
			'Fecha' => $row["fecha"],
			'TipoMov' => $row["tipo_mov"],
			'Valor' => $row["valor"],
			'Cuotas' => $row["cuotas"],
			'Valor_Cuotas' => $row["valor_cuotas"],//($row["valor"] / $row["cuotas"]),
			'Forma_Pago' => $row["forma_pago"],
			'Caja' => $row["caja"],
			'Observacion' => $row["observacion"],
			'Estado' => $row["estado"],
			'Digitado_Por' => $row["digitado_por"],
			'Fecha_Digitado' => $row["fecha_digitado"],
			'Aprobado_Por' => $row["aprobado_por"],
			'Modificado_Por' => $row["modificado_por"],
		);
	}
	else
	{
		$data[] = array();
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Novedades']))
{	
	$query = "SELECT * FROM nom_novedades WHERE estado = 'Pendiente'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Interno' => $row["interno"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Novedades_Modificar']))
{
	$Interno = isset($_GET['Nomina_Novedades_Modificar']) ? $_GET['Nomina_Novedades_Modificar']:die();
	
	$query = "SELECT * FROM nom_novedades WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$data[] = array(
			'EmpleadoID' => $row["empleado_id"],
			'ReemplazoID' => $row["reemplazo_id"],
			'AutorizadorID' => $row["autorizador_id"],
			'Justificacion' => $row["justificacion"],
			'Comentario' => $row["comentario"],
			'Novedad' => $row["novedad"],
			'Fecha_Ini' => $row["fecha_ini"],
			'Fecha_Fin' => $row["fecha_fin"],
			'FechaHora_Ini' => $row["hora_ini"],
			'FechaHora_Fin' => $row["hora_fin"],
			'Total_Ausencia' => $row["horas_novedad"],
			'Reposicion' => $row["reposicion"],
			'Observacion' => $row["observacion"],
			'Estado' => $row["estado"],
			'Digitado_Por' => $row["digitado_por"],
			'Fecha_Digitado' => $row["fecha_digitado"],
			'Aprobado_Por' => $row["aprobado_por"],
			'Modificado_Por' => $row["modificado_por"],
		);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
		die();
	}
	
	$query = "SELECT *, SUBSTR(hora_ini, 1, 5) AS hora_ini, SUBSTR(hora_fin, 1, 5) AS hora_fin FROM nom_novedades_movs WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Fecha' => $row["fecha"],
				'Hora_Ini' => $row["hora_ini"],
				'Hora_Fin' => $row["hora_fin"],
				'Total' => $row["total"],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Aprobar_Prestamos']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT interno FROM nom_prestamos_movs WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' ";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= "GROUP BY interno";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$PrestamosInterno[] = $row["interno"];
		}
	}
		
	$Clause = implode("', '", $PrestamosInterno);
	
	$query = "SELECT * FROM nom_prestamos_movs WHERE interno IN ('".$Clause."') ORDER BY fecha";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($row["cuota"] == "1")
			{
				if (!isset($PrestamosFecha_Ini[$row["interno"]]))
					$PrestamosFecha_Ini[$row["interno"]] = $row["fecha"];
			}
			else
			{
				$PrestamosFecha_Fin[$row["interno"]] = $row["fecha"];
			}
		}
	}
	
	$query = "SELECT * FROM nom_prestamos WHERE interno IN ('".$Clause."')";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND beneficiario_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['TipoMov']) && $_GET['TipoMov'] != "") ? " AND tipo_mov = '".$_GET['TipoMov']."'":"";
	$query .= ";";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0) {
			
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Aprobar' => false,
				'Estado' => $row['estado'],
				'Interno' => $row['interno'],
				'Fecha_Ini' => $PrestamosFecha_Ini[$row["interno"]]." 00:00:00",//"".$row['fecha']." 00:00:00",
				'Fecha_Fin' => isset($PrestamosFecha_Fin[$row["interno"]]) ? $PrestamosFecha_Fin[$row["interno"]]." 00:00:00":$PrestamosFecha_Ini[$row["interno"]]." 00:00:00",
				'ID' => $row['beneficiario_id'],
				'BeneficiarioID' => $row['beneficiario_id'],
				'AcreedorID' => $row['acreedor_id'],
				'TipoMov' => $row['tipo_mov'],
				'Valor' => $row['valor'],
				'Cuotas' => $row['cuotas'],
				'Caja' => $row['caja'],
				'AprobadorID' => $row['aprobado_por'],
				'DigitadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Aprobar_Novedades']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT *, SUBSTR(fecha_digitado, 1, 10) as fecha FROM nom_novedades WHERE DATE(fecha_digitado) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND empleado_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= (isset($_GET['TipoMov']) && $_GET['TipoMov'] != "") ? " AND novedad = '".$_GET['TipoMov']."'":"";
	$query .= ";";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0) {
			
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Aprobar' => false,
				'Estado' => $row['estado'],
				'Interno' => $row['interno'],
				'Fecha' => $row['fecha']." 00:00:00",
				'ID' => $row['empleado_id'],
				'ClienteID' => $row['empleado_id'],
				'Novedad' => $row['novedad'],
				'Fecha1' => $row['fecha_ini']." 00:00:00",
				'Hora1' => $row['hora_ini'],
				'Fecha2' => $row['fecha_fin']." 00:00:00",
				'Hora2' => $row['hora_fin'],
				'Horas_Novedad' => $row['horas_novedad'],
				'Horas_Reposicion' => $row['horas_reposicion'],
				'AprobadorID' => $row['aprobado_por'],
				'DigitadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Aprobar_Extras']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT *, SUBSTR(fecha_digitado, 1, 10) as fecha FROM nom_extras WHERE DATE(fecha_digitado) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND empleado_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= ";";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	/*$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Interno[] = $row["interno"];
		}
		
		$Clause = implode("', '", $Clientes);
		
		$query = "SELECT MAX(turno) AS turno FROM nom_extras_movs WHERE interno IN ('".$Clause."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
	}*/
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array (
				'Aprobar' => false,
				'Estado' => $row['estado'],
				'Interno' => $row['interno'],
				'Fecha' => "".$row['fecha']." 00:00:00",
				'ID' => $row['empleado_id'],
				'ClienteID' => $row['empleado_id'],
				'Justificacion' => $row['justificacion'],
				'Total' => number_format(($row['total_diurnas'] + $row['total_nocturnas']), 2),
				'AprobadorID' => $row['aprobado_por'],
				'DigitadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Salarios']))
{
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$Nomina = ClientesNomina($Grupo);
	if (isset($Nomina[0]["ClienteID"]))
	{
		foreach($Nomina as $item)
		{
			$Clientes[] = $item["ClienteID"];
			$ClienteNombre[$item["ClienteID"]] = $item["Nombre"];
			$ClienteTipo[$item["ClienteID"]] = $item["Tipo"];
		}
		$Clause = implode("', '", $Clientes);
		
		$query = "SELECT * FROM contratos WHERE cliente_id IN ('".$Clause."') AND active = 'true'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if(mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result))
			{
				$StartDate = date_create($row['starts']);
				$EndDate = date_create($row['ends']);

				$data[] = array(
					'ID' => $row["id"],
					'Nombre' => $ClienteNombre[$row["cliente_id"]],
					'ClienteID' => $row["cliente_id"],
					'Cargo' => $row["charge"],
					'Basico' => $row["basic"],
					'Transp' => $row["transp"],
					'Bono' => $row["bonus"],
					'Horas' => $row["hlab"],
					'Hora_Ini1' => "1999-12-31 ".$row["turn1_starts"],
					'Hora_Fin1' => "1999-12-31 ".$row["turn1_ends"],
					'Hora_Ini2' => "1999-12-31 ".$row["turn2_starts"],
					'Hora_Fin2' => "1999-12-31 ".$row["turn2_ends"],
					'Inicio' => date_format($StartDate, "m-d-Y"),
					'Fin' => date_format($EndDate, "m-d-Y"),
					'Pension' => $row["pension"],
					'Salud' => $row["health"],
					'RH' => $row["rh"],
					'Contrato' => $row["type"],
					'Cesantia' => $row["outgoing"],
					'Tipo' => $ClienteTipo[$row["cliente_id"]],
					'Activo' => $row["active"],
				);
			}
			echo json_encode($data);
		}
	}
}
else if (isset($_GET['Nomina']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:die();
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:die();
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	if ($Estado == "Aprobados")
		$Estado = "Aprobado";
	else
		$Estado = "";
	$EmpleadoID = isset($_GET['EmpleadoID']) ? $_GET['EmpleadoID']:"";
	$Quincenal = isset($_GET['Quincenal']) ? $_GET['Quincenal']:false;
	if ($Quincenal == "false")
		$Quincenal = false;
	else if ($Quincenal == "true")
		$Quincenal = true;
	$ImprimirBase = isset($_GET['ImprimirExtra']) ? $_GET['ImprimirExtra']:false;
	$ImprimirExtra = isset($_GET['ImprimirExtra']) ? $_GET['ImprimirExtra']:false;
	
	if ($Fecha_Ini == "" || $Fecha_Fin == "")
	{
		$data[] = array();
		echo json_encode($data);
		die();
	}
	
	if ($EmpleadoID == "")
		$Nomina = ClientesNomina($Grupo);
	else
	{
		$query = "SELECT nombre FROM clientes WHERE cliente_id = '".$EmpleadoID."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$Nomina[] = array(
				'Nombre' => $row["nombre"],
				'ClienteID' => $EmpleadoID,
			);
		}
	}
	
	$query = "SELECT * FROM par_nom_retencion ORDER BY salario DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$RetencionValor[] = array(
				'Salario' => $row["salario"],
				'Valor' => $row["valor"],
			);
		}
	}
	
	$query = "SELECT * FROM par_nom";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
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
	
	if (isset($Nomina[0]["ClienteID"]))
	{
		foreach($Nomina as $item)
		{
			$Clientes[] = $item["ClienteID"];
			$ClienteNombre[$item["ClienteID"]] = $item["Nombre"];
		}
		$Clause = implode("', '", $Clientes);
		
		$Old_ID = "_empty_";
		//PRESTAMOS		
		// $query = "SELECT nom_prestamos_movs.*, nom_prestamos.tipo_mov, nom_prestamos.beneficiario_id FROM nom_prestamos_movs ";
		// $query .= "RIGHT JOIN nom_prestamos ON nom_prestamos_movs.interno = nom_prestamos.interno ";
		// $query .= "WHERE DATE(nom_prestamos_movs.fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' ";
		// $query .= ($Estado != "" && $Estado != "\"\"") ? "AND nom_prestamos_movs.estado = '".$Estado."'":"AND nom_prestamos_movs.estado != 'Anulado'";
		// $query .= " ORDER BY nom_prestamos_movs.fecha";
		
		$query = "SELECT * FROM nom_prestamos_movs WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' ";
		$query .= ($Estado != "" && $Estado != "\"\"") ? "AND estado = '".$Estado."'":"AND estado != 'Anulado'";
		$query .= " ORDER BY fecha";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$PrestamosInterno[] = $row["interno"];
				if (isset($PrestamosValor[$row["interno"]]))
					$PrestamosValor[$row["interno"]]++;
				else
					$PrestamosValor[$row["interno"]] = 1;
			}
		}
		
		$Clause2 = implode("', '", $PrestamosInterno);
		
		$query = "SELECT * FROM nom_prestamos WHERE interno IN ('".$Clause2."') ORDER BY beneficiario_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				if (isset($PrestamosValor[$row["interno"]]))
					$ValorCuota = $row["valor_cuotas"] * $PrestamosValor[$row["interno"]];
				else
					$ValorCuota = $row["valor_cuotas"];
				
				switch($row["tipo_mov"])
				{
					case "Préstamo":
						if (isset($TotalPrestamos[$row["beneficiario_id"]]))
							$TotalPrestamos[$row["beneficiario_id"]] += $ValorCuota;
						else
							$TotalPrestamos[$row["beneficiario_id"]] = $ValorCuota;
						
						/*if ($row["beneficiario_id"] == "88309848")
						{
							echo $row["valor_cuotas"];
							echo "<br />";
							echo $row["interno"];
							echo "<br />";
							echo $PrestamosValor[$row["interno"]];
							echo "<br />";
							echo $row["valor_cuotas"] * $PrestamosValor[$row["interno"]];
							echo "<br />";
							echo " ".$row["valor_cuotas"]." * ".$PrestamosValor[$row["interno"]]." ";
							echo "<br />";
						}*/
					break;
					
					case "Libranza":
						if (isset($TotalLibranzas[$row["beneficiario_id"]]))
							$TotalLibranzas[$row["beneficiario_id"]] += $ValorCuota;
						else
							$TotalLibranzas[$row["beneficiario_id"]] = $ValorCuota;
					break;
					
					case "Anticipo":
						if (isset($TotalAnticipos[$row["beneficiario_id"]]))
							$TotalAnticipos[$row["beneficiario_id"]] += $ValorCuota;
						else
							$TotalAnticipos[$row["beneficiario_id"]] = $ValorCuota;
					break;
					
					case "Donacion":
						if (isset($TotalDonacion[$row["beneficiario_id"]]))
							$TotalDonacion[$row["beneficiario_id"]] += $ValorCuota;
						else
							$TotalDonacion[$row["beneficiario_id"]] = $ValorCuota;
					break;
					
					case "Multas":
						if (isset($TotalMultas[$row["beneficiario_id"]]))
							$TotalMultas[$row["beneficiario_id"]] += $ValorCuota;
						else
							$TotalMultas[$row["beneficiario_id"]] = $ValorCuota;
					break;
					
					// default:
					// break;
				}
			}
		}
		
		$Old_ID = "_empty_";
		//NOVEDADES
		$query = "SELECT * FROM nom_novedades WHERE DATE(fecha_ini) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' ";
		$query .= ($Estado != "" && $Estado != "\"\"") ? "AND estado = '".$Estado."'":"AND estado != 'Anulado'";
		$query .= " AND empleado_id IN ('".$Clause."') ORDER BY empleado_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				//echo " ".$row["empleado_id"].", ".$row["interno"].", ".$row["reposicion"].", ".$row["horas_reposicion"]."<br />";
				//continue;
				
				if ($Old_ID != $row["empleado_id"])
					$Old_ID = $row["empleado_id"];
				
				if ($row["novedad"] == "Vacaciones")
				{
					if (!isset($Vacaciones[$Old_ID]))
						$Vacaciones[$Old_ID] = true;
				}
				
				if ($row["descontable"] == "true")
				{
					/*echo $Old_ID;
					echo " - ";
					echo $row["interno"];
					echo " - ";
					echo $row["horas_novedad"];
					echo "<br />";*/
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
					/*echo $Old_ID;
					echo " - ";
					echo $row["interno"];
					echo " - ";
					echo $row["horas_reposicion"];
					echo "<br />";*/
					if (isset($HorasReposicion[$Old_ID]))
						$HorasReposicion[$Old_ID] += $row["horas_reposicion"];
					else
						$HorasReposicion[$Old_ID] = $row["horas_reposicion"];
				}
				
				if ($row["reposicion"] == "Canje x hora extra")
				{
					/*echo $Old_ID;
					echo " - ";
					echo $row["interno"];
					echo " - ";
					echo $row["horas_reposicion"];
					echo "<br />";*/
					if (isset($HorasCanje[$Old_ID]))
						$HorasCanje[$Old_ID] += $row["horas_novedad"];
					else
						$HorasCanje[$Old_ID] = $row["horas_novedad"];
				}
			}
		}
		
		$Old_ID = "_empty_";
		//HORAS EXTRAS
		$query = "SELECT * FROM nom_extras_movs WHERE DATE(turno) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' ";
		$query .= ($Estado != "" && $Estado != "\"\"") ? "AND estado = '".$Estado."'":"AND estado != 'Anulado'";
		$query .= " AND cliente_id IN ('".$Clause."') ORDER BY cliente_id";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				if ($Old_ID != $row["cliente_id"])
					$Old_ID = $row["cliente_id"];
				
				if ($row["nocturno"] == "false" && $row["festivo"] == "false")
				{
					/*echo $Old_ID;
					echo " - ";
					echo $row["interno"];
					echo " - ";
					echo $row["turno"];
					echo " - ";
					echo $row["total"];
					echo "<br />";*/
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
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
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
				if ($Quincenal == true) {
					$HorasLab = ((($row["hlab"] / 2) - $Horas_Desc) + $Horas_Rep);
					$HorasLab2 = $row["hlab"] / 2;
					$HorasLab3 = (($row["hlab"] / 2) - $Horas_Cesantias);
				}
				else {
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
				
				//echo "".$Horas66_1." - ".$Horas66_2." - ".$Remunerado66." - ".$Remunerado100."<br/>";
				
				//basic mini / 30 = diario min
				//diario min /240 = valor hora minima.
										// 100%						// 1ros 3 dias = 66%			// +3 dias = 100%
				$row["basic"] <= $BasicoMinimo ? 
				$Licencias = ($ValorHora * $Remunerado100) + ($ValorHora * $Horas66_1) + ($ValorHora * $Horas66_2)
				:
				$Licencias = ($ValorHora * $Remunerado100) + ($ValorHora * $Horas66_1) + (($ValorHora * 0.6667) * $Horas66_2);
				// Access Code:
				//[Vrhora]*[l1]+(([Vrhora]*0,66)*[l2])+[Vrhora]*[l3]+[Vrhora]*[l4]
				
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
							//echo "".$row["cliente_id"]." - ".$RetencionValor[$i]["Salario"]." - ".$row["basic"]."<br/>";
							//$Retencion = $row["basic"] * ($RetencionValor[$i]["Valor"] / 100);
							$Retencion = $RetencionValor[$i]["Valor"];
							break;
						}
					}
				}
				
				//---
				$Deducido = $Salud + $Pension + $Retencion + $Prestamos + $Libranzas + $Anticipos + $Donacion + $Multas;
				$Deducido2 = $Salud2 + $Pension2;
				$DeudaFutura = 0;
				
				// Check Vacaciones
				if ($Devengado - $Deducido < 0 && isset($Vacaciones[$row["cliente_id"]]))
				{
					$TotalPrestamos = 0;
						$TotalLibranzas = 0;
						$TotalAnticipos = 0;
						$TotalDonacion = 0;
						$TotalMultas = 0;
					//PRESTAMOS
					$query = "SELECT * FROM nom_prestamos_movs WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' ";
					$query .= ($Estado != "" && $Estado != "\"\"") ? "AND estado = '".$Estado."'":"AND estado != 'Anulado'";
					$query .= " ORDER BY fecha";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
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
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
					if (mysql_num_rows($result1) > 0)
					{
						$DevengadoTmp = $Devengado - $Salud;
						if ($DevengadoTmp > 0)
						{
							$Salud_Pagada = true;
							$DevengadoTmp -= $Pension;
						}
						
						if ($DevengadoTmp > 0)
						{
							$Pension_Pagada = true;
							$DevengadoTmp -= $Retencion;
						}
						
						$TotalPrestamos = 0;
						$TotalLibranzas = 0;
						$TotalAnticipos = 0;
						$TotalDonacion = 0;
						$TotalMultas = 0;
						
						if ($DevengadoTmp > 0)
						{
							$Retencion_Pagada = true;
							while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
							{
								$query = "SELECT * FROM nom_prestamos_movs WHERE interno = '".$row1["interno"]."' 
								AND DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado' ORDER BY fecha";
								$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #10: ".mysql_error():"");
								if (mysql_num_rows($result2) > 0)
								{
									while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
									{
										switch($row1["tipo_mov"])
										{
											case "Préstamo":
												$DevengadoTmp -= $row2["valor"];
												if ($DevengadoTmp > 0)
													$TotalPrestamos += $row2["valor"];
												else
													$DeudaFutura += $row2["valor"];
											break;
											
											case "Libranza":
												$DevengadoTmp -= $row2["valor"];
												if ($DevengadoTmp > 0)
													$TotalLibranzas += $row2["valor"];
												else
													$DeudaFutura += $row2["valor"];
											break;
											
											case "Anticipo":
												$DevengadoTmp -= $row2["valor"];
												if ($DevengadoTmp > 0)
													$TotalAnticipos += $row2["valor"];
												else
													$DeudaFutura += $row2["valor"];
											break;
											
											case "Donacion":
												$DevengadoTmp -= $row2["valor"];
												if ($DevengadoTmp > 0)
													$TotalDonacion += $row2["valor"];
												else
													$DeudaFutura += $row2["valor"];
											break;
											
											case "Multas":
												$DevengadoTmp -= $row2["valor"];
												if ($DevengadoTmp > 0)
													$TotalMultas += $row2["valor"];
												else
													$DeudaFutura += $row2["valor"];
											break;
											
											default:
											break;
										}
									}
								}
							}
						}
					}
					
					$Deducido = 0;
					
					if (isset($Salud_Pagada))
						$Deducido += $Salud;
					if (isset($Pension_Pagada))
						$Deducido += $Pension;
					if (isset($Retencion_Pagada))
						$Deducido += $Retencion;
					
					//$Deducido += $Salud + $Pension + $Retencion;
					$Deducido += $TotalPrestamos + $TotalLibranzas + $TotalAnticipos + $TotalDonacion + $TotalMultas;
				}
				
				$data[] = array(
					'Nombre' => $ClienteNombre[$row["cliente_id"]],
					'ClienteID' => $row["cliente_id"],
					'Basico' => $row["basic"],
					'Horas' => $row["hlab"],
					'Horas_Desc' => $Horas_Desc,
					'Horas_Rep' => $Horas_Rep,
					'Horas_Ext' => $HorasExt,
					'Horas_Lab' => $HorasLab,
					'Basico_Final' => round($BasicoFinal),
					'Transporte' => round($Transporte),
					'Bono' => round($Bono),
					'Extras' => round($Extras),
					'Licencias' => round($Licencias),
					'Devengado' => round($Devengado),
					'Salud' => round($Salud),
					'Pension' => round($Pension),
					'Retencion' => round($Retencion),
					'Prestamo' => round($Prestamos),
					'Libranza' => round($Libranzas),
					'Anticipo' => round($Anticipos),
					'Donacion' => round($Donacion),
					'Multa' => round($Multas),
					'Deducido' => round($Deducido),
					'Deuda' => round($DeudaFutura),
					'Neto' => round($Devengado - $Deducido),
					'Cesantia' => round($Cesantia),
					//'ValorHora' => $ValorHora,
				);
				
				//imprimir base nomina -> salud y pension = minimo * 0.04 / 2 (quincena)
				$dataBase[] = array(
					'Nombre' => $ClienteNombre[$row["cliente_id"]],
					'ClienteID' => $row["cliente_id"],
					'Basico' => $row["basic"],
					'Horas_Lab' => $HorasLab2,
					'Basico_Final' => round($BasicoFinal2),
					'Transporte' => round($Transporte2),
					'Devengado' => round($Devengado2),
					'Salud' => round($Salud2),
					'Pension' => round($Pension2),
					'Deducido' => round($Deducido2),
					'Neto' => round($Devengado2 - $Deducido2),
				);
				
				$dataExtras[] = array(
					'Nombre' => $ClienteNombre[$row["cliente_id"]],
					'Basico' => $row["basic"],
					'HorasDiurnas' => $Diurnas,
					'HorasNocturnas' => $Nocturnas,
					'HorasDiurnasFestivas' => $DiurnasFestivo,
					'HorasNocturnasFestivas' => $NocturnasFestivo,
					'ValorHorasDiurnas' => round($ValorHoraDiurna),
					'ValorHorasNocturnas' => round($ValorHoraNocturna),
					'ValorHorasDiurnasFestivas' => round($ValorHoraDiurnaFestivo),
					'ValorHorasNocturnasFestivas' => round($ValorHoraNocturnaFestivo),
					'Total' => round($Extras),
				);
			}
			
			if ($ImprimirExtra)
			{
				sort($dataExtras);
				echo json_encode($dataExtras);
			}
			else if ($ImprimirBase)
			{
				sort($dataBase);
				echo json_encode($dataBase);
			}
			else
			{
				sort($data);
				echo json_encode($data);
			}
		}
	}
}
else if (isset($_GET['Nomina_Buscar']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:die();
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:die();
	
	$query = "SELECT * FROM nomina WHERE fecha_ini = '".$Fecha_Ini."' AND fecha_fin = '".$Fecha_Fin."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$data[] = array(
			"Existe" => "SI",
		);
	}
	else
	{
		$data[] = array(
			"Existe" => "NO",
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_BioControl']))
{
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:die();
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:die();
	
	if ($ClienteID != "")
		$query = "SELECT cliente_id, nombre FROM clientes WHERE cliente_id = '".$ClienteID."'";
	else
		$query = "SELECT cliente_id, nombre FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	if ($ClienteID != "")
		$query = "SELECT * FROM contratos WHERE active = 'true' AND cliente_id = '".$ClienteID."'";
	else
		$query = "SELECT * FROM contratos WHERE active = 'true'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			$Turno_Ini1[$row["cliente_id"]] = $row["turn1_starts"];
			$Turno_Fin1[$row["cliente_id"]] = $row["turn1_ends"];
			$Turno_Ini2[$row["cliente_id"]] = $row["turn2_starts"];
			$Turno_Fin2[$row["cliente_id"]] = $row["turn2_ends"];
		}
	}
	
	$query = "SELECT * FROM biocontrol WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= ($ClienteID != "") ? " AND cliente_id = '".$ClienteID."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"ID" => $row["id"],
				"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				"ClienteID" => $row["cliente_id"],
				"Fecha" => $row["fecha"],
				"Horas" => $row["horas"],
				"Turno_Ini1" => isset($Turno_Ini1[$row["cliente_id"]]) ? $Turno_Ini1[$row["cliente_id"]]:"00:00:00",
				"Hora_Ini1" => $row["turno1_hora_ini"],
				"Turno_Ini2" => isset($Turno_Ini2[$row["cliente_id"]]) ? $Turno_Ini2[$row["cliente_id"]]:"00:00:00",
				"Hora_Ini2" => $row["turno2_hora_ini"],
				"Turno_Fin1" => isset($Turno_Fin1[$row["cliente_id"]]) ? $Turno_Fin1[$row["cliente_id"]]:"00:00:00",
				"Hora_Fin1" => $row["turno1_hora_fin"],
				"Turno_Fin2" => isset($Turno_Fin2[$row["cliente_id"]]) ? $Turno_Fin2[$row["cliente_id"]]:"00:00:00",
				"Hora_Fin2" => $row["turno2_hora_fin"],
			);
		}
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Nomina_Carnet']))
{
	$query = "SELECT nombre, cliente_id, foto FROM clientes ORDER BY nombre";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
			$ClienteFoto[$row["cliente_id"]] = $row["foto"];
		}
	}
	
	$query = "SELECT * FROM contratos WHERE active = 'true'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if(mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result))
		{
			$EndDate = date_create($row['ends']);

			$data[] = array(
				"Cliente" => $ClienteNombre[$row["cliente_id"]],
				"ClienteID" => $row["cliente_id"],
				"Cargo" => $row["charge"],
				"RH" => $row["rh"],
				"Fecha" => $row["ends"]." 00:00:00",
				"Imagen" => $ClienteFoto[$row["cliente_id"]],
				"Imprimir" => false,
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Produccion_Check_SameID']))
{
	$query = "SELECT solicitud FROM produccion_final WHERE solicitud = '".$_GET['Produccion_Check_SameID']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	if (mysql_num_rows($result)>0)
	{
		$data[] = array(
			'Same' => true,
		);
	}
	else
	{
		$data[] = array(
			'Same' => false,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Produccion_Ordenes']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Produccion" && $data_session[$i]["SubModulo"] == "Modificar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT orden_produccion FROM produccion_final WHERE estado = '".$_GET['Produccion_Ordenes']."' AND 
	digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Orden_Produccion' => $row['orden_produccion'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Produccion_Ordenes_Total']))
{
	$query = "SELECT orden_produccion, cliente_id FROM produccion_final WHERE estado = '".$_GET['Produccion_Ordenes_Total']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Orden_Produccion' => $row['orden_produccion'],
				'ClienteID' => $row['cliente_id'],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Produccion_Ordenes_Proc']))
{
	$query = "SELECT DISTINCT orden_produccion FROM produccion_proc WHERE estado = '".$_GET['Produccion_Ordenes_Proc']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Orden_Produccion' => $row['orden_produccion'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}

}
/*else if (isset($_GET['Produccion_Ordenes_Proc2']))
{
	$query = "SELECT DISTINCT orden_produccion FROM produccion_proc WHERE proceso LIKE '%".$_GET['Produccion_Ordenes_Proc2']."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Orden_Produccion' => $row['orden_produccion'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}

}*/
else if (isset($_GET['Produccion_Ordenes_Estado']))
{
	$query = "SELECT produccion_final.estado FROM produccion_proc LEFT JOIN produccion_final ON 
	produccion_final.orden_produccion = produccion_proc.orden_produccion WHERE 
	produccion_proc.orden_produccion = '".$_GET['Produccion_Ordenes_Estado']."' LIMIT 1";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Estado_Real' => $row['estado'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Produccion_Ordenes_Trefilado']))
{
	$UserID = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Produccion" && $data_session[$i]["SubModulo"] == "Trefilado" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserID = $_SESSION["UserID"];
				}
			}
		}
	}
	
	$query = "SELECT produccion_proc.orden_produccion FROM produccion_proc LEFT JOIN produccion_final ON 
	produccion_final.orden_produccion = produccion_proc.orden_produccion WHERE produccion_proc.proceso = 'Trefilado' AND 
	produccion_proc.estado = '".$_GET['Produccion_Ordenes_Trefilado']."' AND produccion_final.operario_trefilado LIKE '%".$UserID."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Orden_Produccion' => $row['orden_produccion'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Produccion_Ordenes_Enderezado']))
{
	$UserID = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Produccion" && $data_session[$i]["SubModulo"] == "Corte y Enderezado" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserID = $_SESSION["UserID"];
				}
			}
		}
	}
	
	$query = "SELECT produccion_proc.orden_produccion FROM produccion_proc LEFT JOIN produccion_final ON 
	produccion_final.orden_produccion = produccion_proc.orden_produccion WHERE produccion_proc.proceso = 'Corte y Enderezado' AND 
	produccion_proc.estado = '".$_GET['Produccion_Ordenes_Enderezado']."' AND produccion_final.operario_enderezado LIKE '%".$UserID."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Orden_Produccion' => $row['orden_produccion'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Produccion_Ordenes_ElectroSoldado']))
{
	$UserID = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Produccion" && $data_session[$i]["SubModulo"] == "Electrosoldado" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserID = $_SESSION["UserID"];
				}
			}
		}
	}
	
	$query = "SELECT produccion_proc.orden_produccion FROM produccion_proc LEFT JOIN produccion_final ON 
	produccion_final.orden_produccion = produccion_proc.orden_produccion WHERE produccion_proc.proceso = 'Electrosoldado' AND 
	produccion_proc.estado = '".$_GET['Produccion_Ordenes_ElectroSoldado']."' AND produccion_final.operario_electrosoldado LIKE '%".$UserID."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Orden_Produccion' => $row['orden_produccion'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Produccion_Ordenes_Figurado']))
{
	$UserID = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Produccion" && $data_session[$i]["SubModulo"] == "Figurado" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserID = $_SESSION["UserID"];
				}
			}
		}
	}
	
	$query = "SELECT produccion_proc.orden_produccion FROM produccion_proc LEFT JOIN produccion_final ON 
	produccion_final.orden_produccion = produccion_proc.orden_produccion WHERE produccion_proc.proceso = 'Figurado' AND 
	produccion_proc.estado = '".$_GET['Produccion_Ordenes_Figurado']."' AND produccion_final.operario_figurado LIKE '%".$UserID."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Orden_Produccion' => $row['orden_produccion'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Produccion_Modificar']))
{
	$query = "SELECT produccion_final.orden_compra, produccion_final.solicitud, produccion_final.interno, produccion_final.cartilla, 
	produccion_final.destino, produccion_final.cliente_id, produccion_final.fecha, produccion_final.estado, produccion_final.operario_trefilado, 
	produccion_final.operario_enderezado, produccion_final.operario_electrosoldado, produccion_final.operario_figurado, 
	produccion_final.digitado_por, produccion_final.aprobado_por, produccion_final.modificado_por, clientes.nombre FROM 
	produccion_final LEFT JOIN clientes ON clientes.cliente_id = produccion_final.cliente_id WHERE 
	produccion_final.orden_produccion = '".$_GET['Produccion_Modificar']."'";
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT codigo, nombre, peso, und_med, cantidad, tipo, origen, destino, orden_compra, 
	interno, cliente_id FROM produccion_movs LEFT JOIN productos ON productos.cod_fab = produccion_movs.codigo 
	WHERE produccion_movs.orden_produccion = '".$_GET['Produccion_Modificar']."'";
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	if (mysql_num_rows($result1) > 0)
	{
		$row1 = mysql_fetch_array($result1);
		$i = 0;
		if (mysql_num_rows($result2) > 0)
		{
			while ($row2 = mysql_fetch_array($result2))
			{
				$i++;
				if ($i == 1)
				{
					$data[] = array(
						'Interno' => $row1['interno'],
						'Ord_Compra' => $row1['orden_compra'],
						'Cartilla' => $row1['cartilla'],
						'ClienteID' => $row1['cliente_id'],
						'Cliente' => $row1['nombre'],
						'Solicitud' => $row1['solicitud'],
						'DestinoOrden' => $row1['destino'],
						'Fecha' => $row1['fecha'],
						'Trefilado' => $row1['operario_trefilado'],
						'Enderezado' => $row1['operario_enderezado'],
						'Soldado' => $row1['operario_electrosoldado'],
						'Figurado' => $row1['operario_figurado'],
						'DigitadoPor' => $row1['digitado_por'],
						'AprobadoPor' => $row1['aprobado_por'],
						'ModificadoPor' => $row1['modificado_por'],
						'Estado' => $row1['estado'],
						//
						'Tipo' => $row2['tipo'],
						'CodFab' => $row2['codigo'],
						'Nombre' => $row2['nombre'],
						'Peso' => $row2['peso'],
						'UndMed' => $row2['und_med'],
						'Cantidad' => $row2['cantidad'],
						'Origen' => $row2['origen'],
						'Destino' => $row2['destino'],
					);
				}
				else
				{
					$data[] = array(
						'Tipo' => $row2['tipo'],
						'CodFab' => $row2['codigo'],
						'Nombre' => $row2['nombre'],
						'Peso' => $row2['peso'],
						'UndMed' => $row2['und_med'],
						'Cantidad' => $row2['cantidad'],
						'Origen' => $row2['origen'],
						'Destino' => $row2['destino'],
					);
				}
			}
			echo json_encode($data);
		}
		else
			echo json_encode(array());
	}
	else
		echo json_encode(array());
}
else if (isset($_GET['Produccion_Aprobar']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Produccion" && $data_session[$i]["SubModulo"] == "Modificar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT * FROM produccion_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND produccion_final.cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Ord_Produccion']) && $_GET['Ord_Produccion'] != "") ? " AND orden_produccion LIKE '%".$_GET['Ord_Produccion']."%'":"";
	$query .= (isset($_GET['Solicitud']) && $_GET['Solicitud'] != "") ? " AND solicitud LIKE '%".$_GET['Solicitud']."%'":"";
	$query .= " AND digitado_por LIKE '%".$UserCode."%'";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				'Aprobar' => false,
				'ClienteID' => $row['cliente_id'],
				'Interno' => $row['interno'],
				'Solicitud' => $row['solicitud'],
				'Estado' => $row['estado'],
				'Fecha' => "".$row['fecha']." 00:00:00",
				'Ord_Produccion' => $row['orden_produccion'],
				'Ord_Compra' => $row['orden_compra'],
				'Destino' => $row['destino'],
				'Cliente' => $row['cliente_id'],
				'Trefilado' => $row['operario_trefilado'] != "" ? true:false,
				'Enderezado' => $row['operario_enderezado'] != "" ? true:false,
				'Soldado' => $row['operario_electrosoldado'] != "" ? true:false,
				'Figurado' => $row['operario_figurado'] != "" ? true:false,
				'DigitadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Produccion_Trefilado']))
{
	$query = "SELECT * FROM produccion_final WHERE orden_produccion = '".$_GET['Produccion_Trefilado']."'";
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT * FROM produccion_proc WHERE orden_produccion = '".$_GET['Produccion_Trefilado']."'";
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	$query = "SELECT produccion_proc_movs.*, productos.nombre, productos.peso, und_med FROM produccion_proc_movs 
	LEFT JOIN productos ON productos.cod_fab = produccion_proc_movs.codigo WHERE produccion_proc_movs.orden_produccion = '".$_GET['Produccion_Trefilado']."'";
	$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	if (mysql_num_rows($result2) > 0)
	{
		while ($row = mysql_fetch_array($result2))
		{
			$data2[] = array(
				'Proceso' => $row['proceso'],
				'Estado' => $row['estado'],
				'Rendimiento' => $row['rendimiento'],
				'Avance' => $row['avance'],
				'IniciadorID' => $row['iniciado_por'],
				'FinalizadorID' => $row['finalizado_por'],
				'Observaciones' => $row['observaciones'],
			);
		}
		//echo json_encode($data2);
	}
	
	if (mysql_num_rows($result3) > 0)
	{
		while ($row = mysql_fetch_array($result3))
		{
			$Passed = false;
			switch($row['tipo'])
			{
				case "Requerido":
					if ($row['destino'] == "Trefilado")
						$Passed = true;
				break;
				case "Obtener":
					if ($row['origen'] == "Trefilado")
						$Passed = true;
				break;
				case "Despunte":
					if ($row['origen'] == "Trefilado")
						$Passed = true;
				break;
			}
			
			if ($Passed)
			{
				$data3[] = array(
					'Tipo' => $row['tipo'],
					'CodFab' => $row['codigo'],
					'Nombre' => $row['nombre'],
					'Peso' => $row['peso'],
					'UndMed' => $row['und_med'],
					'Cantidad' => $row['cantidad'],
					'Maquinaria' => $row['maquinaria'],
					'OperarioID' => $row['operario'],
				);
				$Passed = false;
			}
		}
		//echo json_encode($data3);
	}
	
	if (mysql_num_rows($result1) > 0)
	{
		$row = mysql_fetch_array($result1);
		
		$data1[] = array(
			'DestinoOrden' => $row['destino'],
			'Ord_Compra' => $row['orden_compra'],
			'Interno' => $row['interno'],
			'OperarioID' => $row['operario_trefilado'],
			'ClienteID' => $row['cliente_id'],
			'Fecha' => $row['fecha'],
			'DigitadorID' => $row['digitado_por'],
			'AprobadorID' => $row['aprobado_por'],
			'EstadoOrden' => isset($data2) ? $data2:array(),
			'ProductosOrden' => isset($data3) ? $data3:array(),
		);
	}
	else
	{
		$data1[] = array();
	}
	echo json_encode($data1);
}
else if (isset($_GET['Produccion_Enderezado']))
{
	$query = "SELECT * FROM produccion_final WHERE orden_produccion = '".$_GET['Produccion_Enderezado']."'";
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT * FROM produccion_proc WHERE orden_produccion = '".$_GET['Produccion_Enderezado']."'";
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	$query = "SELECT produccion_proc_movs.*, productos.nombre, productos.peso, und_med FROM produccion_proc_movs 
	LEFT JOIN productos ON productos.cod_fab = produccion_proc_movs.codigo WHERE produccion_proc_movs.orden_produccion = '".$_GET['Produccion_Enderezado']."'";
	$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	if (mysql_num_rows($result2) > 0)
	{
		while ($row = mysql_fetch_array($result2))
		{
			$data2[] = array(
				'Proceso' => $row['proceso'],
				'Estado' => $row['estado'],
				'Rendimiento' => $row['rendimiento'],
				'Avance' => $row['avance'],
				'IniciadorID' => $row['iniciado_por'],
				'FinalizadorID' => $row['finalizado_por'],
				'Observaciones' => $row['observaciones'],
			);
		}
		//echo json_encode($data2);
	}
	
	if (mysql_num_rows($result3) > 0)
	{
		while ($row = mysql_fetch_array($result3))
		{
			$Passed = false;
			switch($row['tipo'])
			{
				case "Requerido":
					if ($row['destino'] == "Corte y Enderezado")
						$Passed = true;
				break;
				case "Obtener":
					if ($row['origen'] == "Corte y Enderezado")
						$Passed = true;
				break;
				case "Despunte":
					if ($row['origen'] == "Corte y Enderezado")
						$Passed = true;
				break;
			}
			
			if ($Passed)
			{
				$data3[] = array(
					'Tipo' => $row['tipo'],
					'CodFab' => $row['codigo'],
					'Nombre' => $row['nombre'],
					'Peso' => $row['peso'],
					'UndMed' => $row['und_med'],
					'Cantidad' => $row['cantidad'],
					'Maquinaria' => $row['maquinaria'],
					'OperarioID' => $row['operario'],
				);
				$Passed = false;
			}
		}
		//echo json_encode($data3);
	}
	
	if (mysql_num_rows($result1) > 0)
	{
		$row = mysql_fetch_array($result1);
		
		$data1[] = array(
			'DestinoOrden' => $row['destino'],
			'Ord_Compra' => $row['orden_compra'],
			'Interno' => $row['interno'],
			'OperarioID' => $row['operario_enderezado'],
			'ClienteID' => $row['cliente_id'],
			'Fecha' => $row['fecha'],
			'DigitadorID' => $row['digitado_por'],
			'AprobadorID' => $row['aprobado_por'],
			'EstadoOrden' => isset($data2) ? $data2:array(),
			'ProductosOrden' => isset($data3) ? $data3:array(),
		);
	}
	else
	{
		$data1[] = array();
	}
	echo json_encode($data1);
}
else if (isset($_GET['Produccion_ElectroSoldado']))
{
	$query = "SELECT * FROM produccion_final WHERE orden_produccion = '".$_GET['Produccion_ElectroSoldado']."'";
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT * FROM produccion_proc WHERE orden_produccion = '".$_GET['Produccion_ElectroSoldado']."'";
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	$query = "SELECT produccion_proc_movs.*, productos.nombre, productos.peso, und_med FROM produccion_proc_movs 
	LEFT JOIN productos ON productos.cod_fab = produccion_proc_movs.codigo WHERE produccion_proc_movs.orden_produccion = '".$_GET['Produccion_ElectroSoldado']."'";
	$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	if (mysql_num_rows($result2) > 0)
	{
		while ($row = mysql_fetch_array($result2))
		{
			$data2[] = array(
				'Proceso' => $row['proceso'],
				'Estado' => $row['estado'],
				'Rendimiento' => $row['rendimiento'],
				'Avance' => $row['avance'],
				'IniciadorID' => $row['iniciado_por'],
				'FinalizadorID' => $row['finalizado_por'],
				'Observaciones' => $row['observaciones'],
			);
		}
		//echo json_encode($data2);
	}
	
	if (mysql_num_rows($result3) > 0)
	{
		while ($row = mysql_fetch_array($result3))
		{
			/*echo $row['tipo'];
			echo "<br/>";
			echo $row['codigo'];
			echo "<br/>";
			echo $row['origen'];
			echo "<br/>";*/
				
			$Passed = false;
			switch($row['tipo'])
			{
				case "Requerido":
					if ($row['destino'] == "Electrosoldado")
						$Passed = true;
				break;
				case "Obtener":
					if ($row['origen'] == "Electrosoldado")
						$Passed = true;
				break;
				case "Despunte":
					if ($row['origen'] == "Electrosoldado")
						$Passed = true;
				break;
			}
			
			if ($Passed)
			{
				$data3[] = array(
					'Tipo' => $row['tipo'],
					'CodFab' => $row['codigo'],
					'Nombre' => $row['nombre'],
					'Peso' => $row['peso'],
					'UndMed' => $row['und_med'],
					'Cantidad' => $row['cantidad'],
					'Maquinaria' => $row['maquinaria'],
					'OperarioID' => $row['operario'],
				);
				$Passed = false;
			}
		}
		//echo json_encode($data3);
	}
	
	if (mysql_num_rows($result1) > 0)
	{
		$row = mysql_fetch_array($result1);
		
		$data1[] = array(
			'DestinoOrden' => $row['destino'],
			'Ord_Compra' => $row['orden_compra'],
			'Interno' => $row['interno'],
			'OperarioID' => $row['operario_electrosoldado'],
			'ClienteID' => $row['cliente_id'],
			'Fecha' => $row['fecha'],
			'DigitadorID' => $row['digitado_por'],
			'AprobadorID' => $row['aprobado_por'],
			'EstadoOrden' => isset($data2) ? $data2:array(),
			'ProductosOrden' => isset($data3) ? $data3:array(),
		);
	}
	else
	{
		$data1[] = array();
	}
	echo json_encode($data1);
}
else if (isset($_GET['Produccion_Figurado']))
{
	$query = "SELECT * FROM produccion_final WHERE orden_produccion = '".$_GET['Produccion_Figurado']."'";
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT * FROM produccion_proc WHERE orden_produccion = '".$_GET['Produccion_Figurado']."'";
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	$query = "SELECT produccion_proc_movs.*, productos.nombre, productos.peso, und_med FROM produccion_proc_movs 
	LEFT JOIN productos ON productos.cod_fab = produccion_proc_movs.codigo WHERE produccion_proc_movs.orden_produccion = '".$_GET['Produccion_Figurado']."'";
	$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	if (mysql_num_rows($result2) > 0)
	{
		while ($row = mysql_fetch_array($result2))
		{
			$data2[] = array(
				'Proceso' => $row['proceso'],
				'Estado' => $row['estado'],
				'Rendimiento' => $row['rendimiento'],
				'Avance' => $row['avance'],
				'Desperdicio' => $row['desperdicio'],
				'IniciadorID' => $row['iniciado_por'],
				'FinalizadorID' => $row['finalizado_por'],
				'Observaciones' => $row['observaciones'],
			);
		}
		//echo json_encode($data2);
	}
	
	if (mysql_num_rows($result3) > 0)
	{
		while ($row = mysql_fetch_array($result3))
		{
			$Passed = false;
			switch($row['tipo'])
			{
				case "Requerido":
					if ($row['destino'] == "Figurado")
						$Passed = true;
				break;
				case "Obtener":
					if ($row['origen'] == "Figurado")
						$Passed = true;
				break;
				case "Despunte":
					if ($row['origen'] == "Figurado")
						$Passed = true;
				break;
			}
			
			if ($Passed)
			{
				$data3[] = array(
					'Tipo' => $row['tipo'],
					'CodFab' => $row['codigo'],
					'Nombre' => $row['nombre'],
					'Peso' => $row['peso'],
					'UndMed' => $row['und_med'],
					'Cantidad' => $row['cantidad'],
					'Maquinaria' => $row['maquinaria'],
					'OperarioID' => $row['operario'],
				);
				$Passed = false;
			}
		}
		//echo json_encode($data3);
	}
	
	if (mysql_num_rows($result1) > 0)
	{
		$row = mysql_fetch_array($result1);
		
		$data1[] = array(
			'DestinoOrden' => $row['destino'],
			'Ord_Compra' => $row['orden_compra'],
			'Interno' => $row['interno'],
			'Cartilla' => $row['cartilla'],
			'OperarioID' => $row['operario_figurado'],
			'ClienteID' => $row['cliente_id'],
			'Fecha' => $row['fecha'],
			'DigitadorID' => $row['digitado_por'],
			'AprobadorID' => $row['aprobado_por'],
			'EstadoOrden' => isset($data2) ? $data2:array(),
			'ProductosOrden' => isset($data3) ? $data3:array(),
		);
	}
	else
	{
		$data1[] = array();
	}
	echo json_encode($data1);
}
else if (isset($_GET['Produccion_Consultar']))
{	
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
	
	$query = "SELECT orden_produccion, interno, cliente_id, digitado_por FROM produccion_final";
	$query .= (isset($_GET['Ord_Produccion']) || isset($_GET['ClienteID'])) ? " WHERE ":"";
	$query .= isset($_GET['ClienteID']) ? " cliente_id LIKE '%".$_GET['ClienteID']."%'":"";
	$query .= (isset($_GET['Ord_Produccion']) && isset($_GET['ClienteID'])) ? " AND orden_produccion LIKE '%".$_GET['Ord_Produccion']."%'":"";
	$query .= (isset($_GET['Ord_Produccion']) && !isset($_GET['ClienteID'])) ? " orden_produccion LIKE '%".$_GET['Ord_Produccion']."%'":"";
	$query .= ";";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProduccionInterno[$row["orden_produccion"]] = $row["interno"];
			$ProduccionClienteID[$row["orden_produccion"]] = $row["cliente_id"];
			$ProduccionDigitadoPor[$row["orden_produccion"]] = $row["digitado_por"];
		}
	}
	
	$query = "SELECT codigo, cantidad, orden_produccion FROM produccion_proc_movs WHERE tipo = 'Obtener'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoCodigo[$row["orden_produccion"]] = $row["codigo"];
			$ProductoCantidad[$row["orden_produccion"]] = $row["cantidad"];
		}
	}
	
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM produccion_proc WHERE DATE(fecha_ini) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= " ORDER BY fecha_ini ASC;";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (!isset($ProduccionClienteID[$row["orden_produccion"]]))
				continue;
			
			$PesoTotal = $ProductoPeso[$ProductoCodigo[$row["orden_produccion"]]] * $ProductoCantidad[$row["orden_produccion"]];
			$data[] = array (
				'ClienteID' => $ProduccionClienteID[$row["orden_produccion"]],
				'Interno' => $ProduccionInterno[$row["orden_produccion"]],
				'Ord_Produccion' => $row['orden_produccion'],
				'Estado' => $row['estado'],
				'Fecha' => $row['fecha_ini'],
				'Proceso' => $row['proceso'],
				'Produciendo' => $ProductoNombre[$ProductoCodigo[$row["orden_produccion"]]],
				'Cliente' => $ProduccionClienteID[$row["orden_produccion"]],
				'Avance' => $row['avance'],
				'Peso' => $PesoTotal,
				'DigitadorID' => $ProduccionDigitadoPor[$row["orden_produccion"]],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Produccion_Procesos']))
{
	$Proceso = isset($_GET['Proceso']) ? $_GET['Proceso']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	if (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "")
	{
		$query = "SELECT DISTINCT orden_produccion FROM produccion_proc_movs 
		WHERE operario = '".$_GET['ClienteID']."' AND origen = '".$Proceso."'";
	}
	else
	{
		$query = "SELECT DISTINCT orden_produccion FROM produccion_proc_movs 
		WHERE operario != '' AND origen = '".$Proceso."'";
	}
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Ord_Produccion[] = $row["orden_produccion"];
		}
	}
	
	$Clause = implode("', '", $Ord_Produccion);
	
	$query = "SELECT orden_produccion, interno, cliente_id, digitado_por FROM produccion_final 
	WHERE orden_produccion IN ('".$Clause."')";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProduccionInterno[$row["orden_produccion"]] = $row["interno"];
			$ProduccionClienteID[$row["orden_produccion"]] = $row["cliente_id"];
			$ProduccionDigitadoPor[$row["orden_produccion"]] = $row["digitado_por"];
		}
	}
	
	$query = "SELECT DISTINCT orden_produccion, estado, proceso, avance, fecha_ini FROM produccion_proc 
	WHERE orden_produccion IN ('".$Clause."') AND proceso LIKE '%".$Proceso."%' 
	AND DATE(fecha_ini) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= " ORDER BY fecha_ini ASC;";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (!isset($ProduccionClienteID[$row["orden_produccion"]]))
				continue;
			
			$data[] = array (
				'ClienteID' => $ProduccionClienteID[$row["orden_produccion"]],
				'Interno' => $ProduccionInterno[$row["orden_produccion"]],
				'Ord_Produccion' => $row['orden_produccion'],
				'Estado' => $row['estado'],
				'Fecha' => $row['fecha_ini'],
				'Proceso' => $row['proceso'],
				'Avance' => $row['avance'],
				'DigitadorID' => $ProduccionDigitadoPor[$row["orden_produccion"]],
			);
		}
		if (!isset($data))
			$data[] = array ();
	
		echo json_encode($data);
	}
}
else if (isset($_GET["Figuracion_Figuras"]))
{
	$query = "SELECT * FROM figuracion_figuras ORDER BY fig ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = Array(
				"ID" => $row["id"],
				"Figura" => $row["fig"],
				"Dimensiones" => $row["dimensiones"],
				"Estribo" => $row["estribo"] == 1 ? true:false,
				"SemiCirculo" => $row["semicirculo"] == 1 ? true:false,
				"Circular" => $row["circular"] == 1 ? true:false,
				"Vueltas" => $row["vueltas"] == 1 ? true:false,
				"Imagen" => $row["img"],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Figuracion_Cartillas"]))
{
	if (isset($_GET["Type"]))
	{
		if ($_GET["Type"] == "Modificar")
			$query = "SELECT interno FROM figuracion_cartilla WHERE estado = 'Pendiente' ORDER BY interno ASC";
		else if ($_GET["Type"] == "Visualizar")
			$query = "SELECT interno FROM figuracion_cartilla WHERE estado = 'Aprobado' ORDER BY interno ASC";
		else
			$query = "SELECT interno FROM figuracion_cartilla ORDER BY interno ASC";
		
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$data[] = array(
					"Interno" => $row["interno"],
				);
			}
		}
		if (isset($data))
			echo json_encode($data);
		else
			echo json_encode(array());
	}
}
else if (isset($_GET["Figuracion_MallasCartillas"]))
{
	if (isset($_GET["Type"]))
	{
		if ($_GET["Type"] == "Modificar")
			$query = "SELECT interno_malla FROM mallas WHERE estado = 'Pendiente' ORDER BY cons DESC";
		else if ($_GET["Type"] == "Visualizar")
			$query = "SELECT interno_malla FROM mallas WHERE estado = 'Aprobado' ORDER BY cons DESC";
		else
			$query = "SELECT interno_malla FROM mallas ORDER BY cons DESC";
		
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$data[] = array(
					"cons" => $row["interno_malla"],
				);
			}
		}
		if (isset($data))
			echo json_encode($data);
		else
			echo json_encode(array());
	}
}
else if (isset($_GET["Figuracion_Aprobar"]))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Figuracion" && $data_session[$i]["SubModulo"] == "Aprobar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
	$Obra = isset($_GET["Obra"]) ? $_GET["Obra"]:"";
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$DigitadorID = isset($_GET["DigitadorID"]) ? $_GET["DigitadorID"]:"";
	$Estado = isset($_GET["Estado"]) ? $_GET["Estado"]:"";
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:"0000-00-00";
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:"0000-00-00";
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM figuracion_cartilla WHERE DATE(fecha_digitado) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= ($Estado != "") ? " AND estado = '".$Estado."'":"";
	$query .= ($ClienteID != "") ? " AND cliente_id = '".$ClienteID."'":"";
	$query .= ($Interno != "") ? " AND interno LIKE '%".$Interno."%'":"";
	$query .= ($Obra != "") ? " AND obra LIKE '%".$Obra."%'":"";
	if ($UserCode == "")
		$query .= ($DigitadorID != "") ? " AND digitado_por = '".$DigitadorID."'":"";
	else
		$query .= " AND digitado_por = '".$UserCode."'";
	$query .= " ORDER BY fecha_digitado DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				"Aprobar" => false,
				"Estado" => $row["estado"],
				"Fecha" => "".$row["fecha_digitado"]." 00:00:00",
				"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				"ClienteID" => $row["cliente_id"],
				"Interno" => $row["interno"],
				"Obra" => $row["obra"],
				"Peso" => $row["peso"],
				"DigitadorID" => $row["digitado_por"],
				"AprobadorID" => $row["aprobado_por"],
			);
		}
		sort($data);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Figuracion_Tickets_Resumen"]))
{
	$query = "SELECT cod_fab, nombre, peso FROM productos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoNombre[$row["cod_fab"]] = $row["nombre"];
		}
	}
	$Codigo = "";
	$query = "SELECT * FROM figuracion_cartilla_tickets WHERE cartilla = '".$_GET["Figuracion_Tickets_Resumen"]."' 
	GROUP BY fecha DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Tmp = $row["figura"].$row["codigo"].$row["detalle"].$row["cantidad1"];
			if ($Codigo != $Tmp)
			{
				$Fechas[] = $row["fecha"];
				$Codigo = $row["figura"].$row["codigo"].$row["detalle"].$row["cantidad1"];
			}
		}
		
		$Clause = implode("', '", $Fechas);
		$query = "SELECT * FROM figuracion_cartilla_tickets WHERE cartilla = '".$_GET["Figuracion_Tickets_Resumen"]."' 
		AND fecha IN ('".$Clause."') ORDER BY fecha ASC, ticket ASC";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Detalle = str_replace("<br />", "", $row["detalle"]);
				$Detalle = str_replace("?", "θ", $Detalle);
				
				$data[] = array(
					"Imprimir" => false,
					"ID" => $row["id"],
					"Interno" => $row["interno"],
					"Ticket" => $row["ticket"],
					"Figura" => $row["figura"],
					"Codigo" => $row["codigo"],
					"Nombre" => isset($ProductoNombre[$row["codigo"]]) ? $ProductoNombre[$row["codigo"]]:"No Existe!",
					"Detalle" => $Detalle,
					"Total" => $row["cantidad1"],
					"Cantidad" => $row["cantidad2"],
					"Fecha" => $row["fecha"],
				);
			}
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Maquinaria_Modificar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Maquinaria" && $data_session[$i]["SubModulo"] == "Modificar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}

	$query = "SELECT ord_reparacion FROM maquinaria_final WHERE estado = 'Pendiente' AND digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Ord_Reparacion' => $row['ord_reparacion'],
			);
		}
	}
	else {
		$data[] = array();
	}
	echo json_encode($data);
}
else if (isset($_GET['Maquinaria_Aprobar']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Maquinaria" && $data_session[$i]["SubModulo"] == "Aprobar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM maquinaria_final WHERE DATE(fecha_ini) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND Estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['Ord_Reparacion']) && $_GET['Ord_Reparacion'] != "") ? " AND ord_reparacion LIKE '%".$_GET['Ord_Reparacion']."%'":"";
	$query .= (isset($_GET['Clasificacion']) && $_GET['Clasificacion'] != "") ? " AND clasificacion = '".$_GET['Clasificacion']."'":"";
	$query .= (isset($_GET['Tipo']) && $_GET['Tipo'] != "") ? " AND tipo = '".$_GET['Tipo']."'":"";
	$query .= (isset($_GET['Motivo']) && $_GET['Motivo'] != "") ? " AND motivo = '".$_GET['Motivo']."'":"";
	$query .= (isset($_GET['Diagnostico']) && $_GET['Diagnostico'] != "") ? " AND diagnostico = '".$_GET['Diagnostico']."'":"";
	$query .= " AND digitado_por LIKE '%".$UserCode."%'";
	$query .= ";";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				'Aprobar' => false,
				'Estado' => $row['estado'],
				'Fecha' => "".$row['fecha_ini']." 00:00:00",
				'Ord_Reparacion' => $row['ord_reparacion'],
				'Maquina' => $row['tipo'],
				'Motivo' => $row['motivo'],
				'Diagnostico' => $row['diagnostico'],
				'Total' => $row['total'],
				'DigitadorID' => $row['digitado_por'],
				'AutorizadorID' => $row['aprobado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Reportes_Ventas']))// Cambiar a buscar el peso en la misma fact_final /*DONE?*/
{
	/*$query = "SELECT cod_fab, nombre, peso FROM productos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoPeso[$row["cod_fab"]] = $row["peso"];
		}
	}*/
	
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	if ($OrderBy == "Cliente")
		$OrderBy = "cliente_id";
	else if ($OrderBy == "Vendedor")
		$OrderBy = "vendedor_codigo";
	else if ($OrderBy == "Vehiculo")
		$OrderBy = "placa";
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM fact_final WHERE DATE(fecha_remision) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Ord_Compra']) && $_GET['Ord_Compra'] != "") ? " AND orden_compra LIKE '%".$_GET['Ord_Compra']."%'":"";
	$query .= (isset($_GET['Remision']) && $_GET['Remision'] != "") ? " AND remision LIKE '%".$_GET['Remision']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= (isset($_GET['Vehiculo']) && $_GET['Vehiculo'] != "") ? " AND placa = '".$_GET['Vehiculo']."'":"";
	$query .= ($OrderBy != "") ? " ORDER BY ".$OrderBy." ":" ORDER BY fecha_remision DESC";
	//$query .= "ORDER BY fecha_remision DESC";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$ValorTotal = 0;
	$GastoTotal = 0;
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
	
	$query = "SELECT * FROM caja_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' 
	AND categoria = 'Egresos' AND grupo = 'Vehículos'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Vehiculo']) && $_GET['Vehiculo'] != "") ? " AND subgrupo2 = '".$_GET['Vehiculo']."'":"";
	//$query .= " ORDER BY fecha DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$GastoTotal += $row["total"];
		}
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
			$data[] = array (
				'Cliente' => $row['cliente_id'],
				'ClienteID' => $row['cliente_id'],
				'Fecha' => "".$row['fecha_remision']." 00:00:00",
				'Interno' => $row['interno'],
				'Ord_Compra' => $row['orden_compra'],
				'Ord_Produccion' => $row['orden_produccion'],
				'Remision' => $row['remision'],
				'Factura' => $row['factura'],
				'F_Pago' => $row['forma_pago'],
				'Valor' => $row['total'],
				//'Peso' => isset($TotalPeso[$row["interno"]]) ? $TotalPeso[$row["interno"]]:0,
				'Peso' => $row["peso"],
				'Ruta' => $row['ruta'],
				'Vehiculo' => $row['placa'],
				'VendedorID' => $row['vendedor_codigo'],
				'CobradorID' => $row['cobrador_codigo'],
				'DigitadorID' => $row['digitado_por'],
				'AutorizadorID' => $row['autorizado_por'],
				'Estado' => $row['estado'],
				'ValorTotal' => $ValorTotal,
				'GastoTotal' => $GastoTotal,
				'PesoTotal' => $PesoTotal,
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array ();
		echo json_encode($data);
	}
}
else if (isset($_GET['Reportes_Ventas_Simple']))// Cambiar a buscar el peso en la misma fact_final /*DONE?*/
{
	/*$query = "SELECT cod_fab, peso FROM productos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoPeso[$row["cod_fab"]] = $row["peso"];
		}
	}*/
	
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:die();
	if ($OrderBy == "Cliente")
		$OrderBy = "cliente_id";
	else if ($OrderBy == "Vendedor")
		$OrderBy = "vendedor_codigo";
	else if ($OrderBy == "Vehiculo")
		$OrderBy = "placa";
	
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
	$query .= " ORDER BY ".$OrderBy."";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$ValorTotal = 0;
	$PesoTotal = 0;
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (isset($Num[$row[$OrderBy]]))
				$Num[$row[$OrderBy]] += 1;
			else
			{
				$Num[$row[$OrderBy]] = 1;
				$Order[] = array (
					$OrderBy => $row[$OrderBy]
				);
			}
			
			if (isset($Valor[$row[$OrderBy]]))
				$Valor[$row[$OrderBy]] += $row['total'];
			else
				$Valor[$row[$OrderBy]] = $row['total'];
			
			if (isset($Peso[$row[$OrderBy]]))
				$Peso[$row[$OrderBy]] += $row['peso'];
			else
				$Peso[$row[$OrderBy]] = $row['peso'];
			
			$Facturas[$row[$OrderBy]] = $row["interno"];
			
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
			if (isset($Peso[$row["interno"]]))
				$Peso[$row["interno"]] += $ProductoPeso[$row["codigo"]] * ($row["cantidad"] - $row["cantidad_despachada"]);
			else
				$Peso[$row["interno"]] = $ProductoPeso[$row["codigo"]] * ($row["cantidad"] - $row["cantidad_despachada"]);
			
			$PesoTotal += $ProductoPeso[$row["codigo"]] * ($row["cantidad"] - $row["cantidad_despachada"]);
		}
	}*/
	
	foreach ($Order AS $item)
	{
		$data[] = array (
			'ID' => $item[$OrderBy],
			'Valor' => isset($Valor[$item[$OrderBy]]) ? $Valor[$item[$OrderBy]]:0,
			//'Peso' => isset($Peso[$Facturas[$item[$OrderBy]]]) ? $Peso[$Facturas[$item[$OrderBy]]]:0,
			'Peso' => isset($Peso[$item[$OrderBy]]) ? $Peso[$item[$OrderBy]]:0,
			'Facturas' => isset($Num[$item[$OrderBy]]) ? $Num[$item[$OrderBy]]:0,
			'ValorTotal' => $ValorTotal,
			'PesoTotal' => $PesoTotal,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Reportes_Compras']))
{
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	if ($OrderBy == "Cliente")
		$OrderBy = "cliente_id";
	else if ($OrderBy == "Vehiculo")
		$OrderBy = "placa";
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT compra_interno FROM cxp_movs WHERE compra_interno LIKE 'COMP%' 
	AND grupo = 'Inventarios' AND subgrupo = 'Inventarios' AND subgrupo2 = 'Inventarios'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Internos[] = $row["compra_interno"];
		}
	}
	
	$Clause = implode("', '", $Internos);
	
	$query = "SELECT * FROM compras_final WHERE interno IN ('".$Clause."') AND DATE(fecha_compra) BETWEEN 
	'".$Fecha_Ini."' AND '".$Fecha_Fin."'";
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
			$data[] = array (
				'Fecha' => "".$row['fecha_compra']." 00:00:00",
				'Estado' => $row['estado'],
				'Conductor' => $row['conductor'],
				'Placa' => $row['placa'],
				'Nombre' => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				'ClienteID' => $row['cliente_id'],
				'Peso' => $row["peso"],
				'Valor' => $row['total'],
				'Interno' => $row['interno'],
				'Factura' => $row['factura'],
				'Doc_Transp' => $row['doc_transp'],
				'Entrada' => $row['entrada'],
				'Pedido' => $row['pedido'],
				'ValorTotal' => $ValorTotal,
				'PesoTotal' => $PesoTotal,
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array ();
		echo json_encode($data);
	}
}
else if (isset($_GET['Reportes_Compras_Simple']))
{
	$ClienteNombre = ClientesNombre();
	
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
	
	$ValorTotal = 0;
	$PesoTotal = 0;
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if (isset($Num[$row[$OrderBy]]))
				$Num[$row[$OrderBy]] += 1;
			else
			{
				$Num[$row[$OrderBy]] = 1;
				$Order[] = array (
					$OrderBy => $row[$OrderBy],
					"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
					"Conductor" => $row["conductor"] == "" ? "N/A":$row["conductor"],
				);
			}
			
			if (isset($Valor[$row[$OrderBy]]))
				$Valor[$row[$OrderBy]] += $row['total'];
			else
				$Valor[$row[$OrderBy]] = $row['total'];
			
			if (isset($Peso[$row[$OrderBy]]))
				$Peso[$row[$OrderBy]] += $row['peso'];
			else
				$Peso[$row[$OrderBy]] = $row['peso'];
			
			$ValorTotal += $row["total"];
			$PesoTotal += $row["peso"];
		}
	}
	else
	{
		die();
	}
	
	$ClienteNombre = ClientesNombre();
	
	foreach ($Order AS $item)
	{
		$data[] = array (
			'ID' => $item[$OrderBy] == "" ? "N/A":$item[$OrderBy],
			'Nombre' => $item["Nombre"],
			'Conductor' => $item["Conductor"],
			'Valor' => isset($Valor[$item[$OrderBy]]) ? $Valor[$item[$OrderBy]]:0,
			'Peso' => isset($Peso[$item[$OrderBy]]) ? $Peso[$item[$OrderBy]]:0,
			'Facturas' => isset($Num[$item[$OrderBy]]) ? $Num[$item[$OrderBy]]:0,
			'ValorTotal' => $ValorTotal,
			'PesoTotal' => $PesoTotal,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET["Requerimientos_Modificar"]))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Requerimientos" && $data_session[$i]["SubModulo"] == "Modificar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT interno FROM req_solicitud WHERE estado = 'Pendiente' AND digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"Interno" => $row["interno"],
			);
		}
	}
	else {
		$data[] = array();
	}
	
	echo json_encode($data);
}
else if (isset($_GET["Requerimientos_Listado"]))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Requerimientos" && $data_session[$i]["SubModulo"] == "Listado" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$DigitadorID = isset($_GET["DigitadorID"]) ? $_GET["DigitadorID"]:"";
	$Estado = isset($_GET["Estado"]) ? $_GET["Estado"]:"";
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:"0000-00-00";
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:"0000-00-00";
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM req_solicitud WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= ($Estado != "") ? " AND estado = '".$Estado."'":"";
	$query .= ($ClienteID != "") ? " AND cliente_id = '".$ClienteID."'":"";
	$query .= ($Interno != "") ? " AND interno LIKE '%".$Interno."%'":"";
	if ($UserCode == "")
		$query .= ($DigitadorID != "") ? " AND digitado_por = '".$DigitadorID."'":"";
	else
		$query .= " AND digitado_por = '".$UserCode."'";
	$query .= " ORDER BY fecha DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				"Aprobar" => false,
				"Estado" => $row["estado"],
				"Fecha" => "".$row["fecha"]." 00:00:00",
				"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				"ClienteID" => $row["cliente_id"],
				"Interno" => $row["interno"],
				"DigitadorID" => $row["digitado_por"],
				"AprobadorID" => $row["aprobado_por"],
			);
		}
		sort($data);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Requerimientos_Listado_Faltantes"]))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Requerimientos" && $data_session[$i]["SubModulo"] == "Listado" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$DigitadorID = isset($_GET["DigitadorID"]) ? $_GET["DigitadorID"]:"";
	$Estado = isset($_GET["Estado"]) ? $_GET["Estado"]:"";
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:"0000-00-00";
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:"0000-00-00";
	
	$ProductInfo = GetProductFullInfo("Requerimientos");
	
	$query = "SELECT * FROM req_solicitud WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= ($Estado != "") ? " AND estado = '".$Estado."'":"";
	$query .= ($ClienteID != "") ? " AND cliente_id = '".$ClienteID."'":"";
	$query .= ($Interno != "") ? " AND interno LIKE '%".$Interno."%'":"";
	if ($UserCode == "")
		$query .= ($DigitadorID != "") ? " AND digitado_por = '".$DigitadorID."'":"";
	else
		$query .= " AND digitado_por = '".$UserCode."'";
	$query .= " ORDER BY fecha DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Internos[] = $row["interno"];
		}
	}
	
	if (isset($Internos))
	{
		$Clause = implode("', '", $Internos);
		
		$query = "SELECT codigo, SUM(cantidad) AS total FROM req_solicitud_movs WHERE interno IN ('".$Clause."') GROUP BY codigo";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result))
			{
				$data[] = array (
					"Codigo" => $row["codigo"],
					"Nombre" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Nombre"]:"No Existe!",
					"Unidad" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Unidad"]:"No Existe!",
					"Cantidad" => $row["total"],
					"Existencia" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Existencia"]:"0",
					"Stock" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Stock"]:"0",
				);
			}
		}
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Requerimientos_Compras_Mod"]))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Requerimientos" && $data_session[$i]["SubModulo"] == "Compras_Mod" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$query = "SELECT interno FROM req_compras WHERE estado = 'Pendiente' AND digitado_por LIKE '%".$UserCode."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"Interno" => $row["interno"],
			);
		}
	}
	else {
		$data[] = array();
	}
	
	echo json_encode($data);
}
else if (isset($_GET["Requerimientos_Aprobar"]))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Requerimientos" && $data_session[$i]["SubModulo"] == "Aprobar" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
	$Factura = isset($_GET["Factura"]) ? $_GET["Factura"]:"";
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$DigitadorID = isset($_GET["DigitadorID"]) ? $_GET["DigitadorID"]:"";
	$Estado = isset($_GET["Estado"]) ? $_GET["Estado"]:"";
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:"0000-00-00";
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:"0000-00-00";
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM req_compras WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= ($Estado != "") ? " AND estado = '".$Estado."'":"";
	$query .= ($ClienteID != "") ? " AND cliente_id = '".$ClienteID."'":"";
	$query .= ($Interno != "") ? " AND interno LIKE '%".$Interno."%'":"";
	$query .= ($Factura != "") ? " AND factura LIKE '%".$Factura."%'":"";
	if ($UserCode == "")
		$query .= ($DigitadorID != "") ? " AND digitado_por = '".$DigitadorID."'":"";
	else
		$query .= " AND digitado_por = '".$UserCode."'";
	$query .= " ORDER BY fecha DESC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				"Aprobar" => false,
				"Estado" => $row["estado"],
				"Fecha" => "".$row["fecha"]." 00:00:00",
				"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				"ClienteID" => $row["cliente_id"],
				"Interno" => $row["interno"],
				"Factura" => $row["factura"],
				"Valor" => $row["total"],
				"DigitadorID" => $row["digitado_por"],
				"AprobadorID" => $row["aprobado_por"],
			);
		}
		sort($data);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Cheques_Consignados']))
{
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM cheques WHERE estado = 'Aprobado' AND estado_cheque = 'Pagado' ORDER BY fecha_cheque";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				'Cheque' => $row['cheque'],
				'Banco_Num' => $row['banco'],
				'Valor' => $row['valor'],
				'Fecha_Cheque' => $row['fecha_cheque'],
				'Banco' => $row['banco_destino'],
				'Cuenta' => $row['cuenta'],
				'Caja_Recibo' => $row['caja_recibo'],
				'Fecha' => $row['fecha'],
				'DigitadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array (
			'Cliente' => "",
			'Cheque' => "",
			'Banco_Num' => "",
			'Valor' => "",
			'Fecha_Cheque' => "",
			'Banco' => "",
			'Cuenta' => "",
			'Caja_Recibo' => "",
			'Fecha' => "",
			'DigitadorID' => "",
		);
		echo json_encode($data);
	}
}
else if (isset($_GET['Caja_Cheques']))
{
	$Today = date('Y-m-d');
	
	$query = "SELECT * FROM cheques";
	switch ($_GET['Estado'])
	{
		case "Al Dia":
			//$query .= " WHERE estado = 'Aprobado' AND estado_cheque = 'Al Dia' AND DATE(fecha_cheque) <= '".$Today."'";
			$query .= " WHERE estado = 'Aprobado' AND estado_cheque = 'Al Dia'";
		break;
		case "Postfechado":
			//$query .= " WHERE estado = 'Aprobado' AND estado_cheque = 'PostFechado'";
			$query .= " WHERE estado = 'Aprobado' AND estado_cheque like '%echado%' AND DATE(fecha_cheque) >= '".$Today."'";
		break;
		default:
			$query .= " WHERE estado = 'Aprobado' AND estado_cheque = '".$_GET['Estado']."'";
		break;
	}
	$query .= isset($_GET['ClienteID']) ? " AND cliente_id LIKE '%".$_GET['ClienteID']."%'":"";
	$query .= " ORDER BY fecha_cheque ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array (
				'TipoEstado' => false,
				'Caja_Interno' => $row['caja_interno'],
				'ClienteID' => $row['cliente_id'],
				'Cliente' => $row['cliente_id'],
				'Cheque' => $row['cheque'],
				'Banco_Num' => $row['banco'],
				'Valor' => $row['valor'],
				'Fecha_Cheque' => "".$row['fecha_cheque']." 00:00:00",
				'Estado' => $row['estado_cheque'],
				'Banco' => $row['banco_destino'],
				'Cuenta' => $row['cuenta'],
				'Caja_Recibo' => $row['caja_recibo'],
				'Fecha' => $row['fecha'],
				'DigitadorID' => $row['digitado_por'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['RecibosConSaldo']))
{
	$data = BuscarRecibosConSaldo($_GET['RecibosConSaldo']);
	echo json_encode($data);
}
else if (isset($_GET['RecibosConSaldo_Fecha']))
{
	$query = "SELECT fecha, saldo FROM caja_final WHERE caja_recibo = '".$_GET['RecibosConSaldo_Fecha']."' ORDER BY fecha ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Fecha' => $row['fecha'],
				'Saldo' => $row['saldo'],
			);
		}
		echo json_encode($data);
	}
	else {
		$data[] = array(
			'Fecha' => "",
			'Saldo' => "",
		);
		echo json_encode($data);
	}
}
else if (isset($_GET['Clientes_Deudores']))
{
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Nombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$data = BuscarClientesDeudores("", $_GET['Clientes_Deudores']);
	$num = count($data);
	
	if ($num > 0) {
		foreach($data as $item)
		{
			if ($item["ClienteID"] != "")
			{
				$newdata[] = array(
					'Nombre' => isset($Nombre[$item["ClienteID"]]) ? $Nombre[$item["ClienteID"]]:"NoExiste/Modificado",
					'ClienteID' => $item["ClienteID"],
				);
			}
		}
		sort($newdata);
		echo json_encode($newdata);
	}
}
else if (isset($_GET['CxP_Deudor']))
{
	$data = BuscarCxPConDeuda($_GET['CxP_Deudor']);
	echo json_encode($data);
}
else if (isset($_GET['CxP_Datos']))
{
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	$Factura = isset($_GET['Factura']) ? $_GET['Factura']:"";
	
	$query = "SELECT saldo, grupo, subgrupo, subgrupo2 FROM cxp_movs WHERE compra_interno = '".$Interno."' AND factura = '".$Factura."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array(
			'Saldo' => $row['saldo'],
			'Grupo' => $row['grupo'],
			'SubGrupo' => $row['subgrupo'],
			'SubGrupo2' => $row['subgrupo2'],
		);
		
		echo json_encode($data);
	}
	else
	{
		$data[] = array(
			'Saldo' => "",
			'Grupo' => "",
			'SubGrupo' => "",
			'SubGrupo2' => "",
		);
		
		echo json_encode($data);
	}
}
else if (isset($_GET['Ventas_Cliente_Historicos']))
{
	$query = "SELECT DISTINCT fact_movs.codigo, fact_movs.precio, productos.nombre FROM fact_movs 
	LEFT JOIN productos ON productos.cod_fab = fact_movs.codigo LEFT JOIN fact_final ON fact_final.interno = fact_movs.interno 
	WHERE fact_movs.cliente_id = '".$_GET['Ventas_Cliente_Historicos']."' 
	ORDER BY fact_final.fecha_digitado ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'Nombre' => $row['nombre'],
				'Codigo' => $row['codigo'],
				'Precio' => "$ ".number_format($row['precio'],2)."",
			);
		}
		sort($data);
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Reportes_Caja']))
{
	$ClienteNombre = ClientesNombre();
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	
	$query = "SELECT * FROM caja_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado'";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Categoria']) && $_GET['Categoria'] != "") ? " AND categoria = '".$_GET['Categoria']."'":"";
	$query .= (isset($_GET['Grupo']) && $_GET['Grupo'] != "") ? " AND grupo = '".$_GET['Grupo']."'":"";
	$query .= (isset($_GET['SubGrupo']) && $_GET['SubGrupo'] != "") ? " AND subgrupo = '".$_GET['SubGrupo']."'":"";
	$query .= (isset($_GET['SubGrupo2']) && $_GET['SubGrupo2'] != "") ? " AND subgrupo2 = '".$_GET['SubGrupo2']."'":"";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND digitado_por = '".$_GET['DigitadorID']."'":"";
	if ($OrderBy == "Cliente")
		$query .= "ORDER BY cliente_id, fecha ASC";
	else if ($OrderBy == "Categoria")
		$query .= "ORDER BY grupo, subgrupo2, subgrupo, fecha ASC";
	
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
		
		if ($OrderBy == "")
			sort($data);
		echo json_encode($data);
	}
}
else if (isset($_GET["Reportes_Maquinaria"]))
{
	$ClienteNombre = ClientesNombre();
	
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:"0000-00-00";
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:"0000-00-00";
	$OrderBy = isset($_GET["OrderBy"]) ? $_GET["OrderBy"]:"";
	if ($OrderBy == "Motivo")
		$OrderBy = "motivo";
	else if ($OrderBy == "Tipo")
		$OrderBy = "tipo";
	
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
	$query .= ($OrderBy != "") ? "ORDER BY ".$OrderBy." ASC":"ORDER BY fecha_ini ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($ClienteID != "")
			{
				if ($row["proveedor1"] == $ClienteID || 
				$row["proveedor2"] == $ClienteID ||
				$row["proveedor3"] == $ClienteID)
				{
					$data[] = array (
						"Fecha" => "".$row["fecha_ini"]." 00:00:00",
						"Ord_Reparacion" => $row["ord_reparacion"],
						"Tipo" => $row["tipo"],
						"Motivo" => $row["motivo"],
						"Diagnostico" => $row["diagnostico"],
						"Total" => $row["total"],
						"Proveedor1" => $row["proveedor1"],
						"Total1" => $row["total1"],
						"Proveedor2" => $row["proveedor2"],
						"Total2" => $row["total2"],
						"Proveedor3" => $row["proveedor3"],
						"Total3" => $row["total3"],
						"DigitadorID" => $row["digitado_por"],
					);
				}
			}
			else
			{
				$data[] = array (
					"Fecha" => "".$row["fecha_ini"]." 00:00:00",
					"Ord_Reparacion" => $row["ord_reparacion"],
					"Tipo" => $row["tipo"],
					"Motivo" => $row["motivo"],
					"Diagnostico" => $row["diagnostico"],
					"Total" => $row["total"],
					"Proveedor1" => $row["proveedor1"],
					"Total1" => $row["total1"],
					"Proveedor2" => $row["proveedor2"],
					"Total2" => $row["total2"],
					"Proveedor3" => $row["proveedor3"],
					"Total3" => $row["total3"],
					"DigitadorID" => $row["digitado_por"],
				);
			}
		}
	}
	
	if (!isset($data))
		echo json_encode(array());
	else
		echo json_encode($data);
}
else if (isset($_GET['Imprimir_Factura']))
{
	$PesoTotal = 0;
	$SubTotal = 0;
	
	//---
	/*$Tmp = GetClient_Info($row["cliente_id"]);
	$Nombre = $Tmp[0]['Nombre'];
	$Direccion = $Tmp[0]['Direccion'];
	$Telefono = $Tmp[0]['Telefono'];*/
	
	$ClienteNombre = ClientesNombre();
	
	
	$query = "SELECT fact_final.*, fact_movs.codigo, fact_movs.cantidad, fact_movs.precio, clientes.nombre,  
	clientes.direccion, clientes.telefono, productos.nombre AS nombre_producto, productos.peso FROM fact_final 
	LEFT JOIN fact_movs ON fact_final.interno = fact_movs.interno LEFT JOIN clientes ON fact_final.cliente_id = clientes.cliente_id 
	LEFT JOIN productos ON productos.cod_fab = fact_movs.codigo WHERE fact_final.interno = '".$_GET['Imprimir_Factura']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$query = "SELECT nombre from clientes WHERE cliente_id = '".$row["beneficiario_id"]."'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			$row1 = mysql_fetch_array($result1);
			/*$query = "SELECT chofer from par_fac_vehiculo WHERE placa = '".$row["placa"]."'";
			$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			$row2 = mysql_fetch_array($result2);*/
			$query = "SELECT barrio from par_fac_ruta WHERE ruta = '".$row["ruta"]."'";
			$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			$row3 = mysql_fetch_array($result3);
			
			$PesoTotal = $row["peso"] * $row["cantidad"];
			$SubTotal = $row["precio"] * $row["cantidad"];
			
			if ($row["chofer"] == "000000000-0")
			{
				$Chofer = "Externo";
			}
			else if ($row["chofer"] == "")
			{
				$Chofer = "";
			}
			else
			{
				$Chofer = isset($ClienteNombre[$row["chofer"]]) ? $ClienteNombre[$row["chofer"]]:"No Existe!";
			}
			
			$data[] = array(
				"Ord_Compra" => $row["orden_compra"],
				"Remision" => $row["remision"],
				"Factura" => $row["factura"],
				"Fecha" => $row["fecha_remision"],
				//
				"Cliente" => $row["nombre"],
				"ClienteID" => $row["cliente_id"],
				"Beneficiario" => $row1["nombre"],
				"BeneficiarioID" => $row["beneficiario_id"],
				"Direccion" => $row["direccion"],
				"Telefono" => $row["telefono"],
				//
				"Observaciones" => $row["observaciones"],
				"SubTotal2" => number_format($row["sub_total"], 2),
				"TipoServicio1" => $row["tipo_servicio"],
				"TipoServicio2" => $row["tipo_servicio_valor"],
				"Direccion_Entrega" => $row["direccion_entrega"],
				"IVA" => number_format($row["iva"], 2),
				"Sector" => $row3["barrio"],
				"Ruta" => $row["ruta"],
				"TipoDescuento1" => $row["tipo_descuento"],
				"TipoDescuento2" => number_format($row["tipo_descuento_valor"], 2),
				"Total" => number_format($row["total"], 2),
				"Conductor" => $Chofer,
				"Placa" => $row["placa"],
				"Fecha_Despacho" => $row["fecha_aprobado"],
				"Estado" => $row["estado"],
				"Modificado_Por" => $row["modificado_por"],
				"Forma_Pago" => $row["forma_pago"],
				"Vendedor" => $row["vendedor_codigo"],
				"Despachado_Por" => $row["aprobado_por"],
				"Fecha_Modificado" => $row["fecha_modificado"],
				"Ord_Produccion" => $row["orden_produccion"],
				"Caja_Recibo" => $row["caja_recibo"],
				"Peso" => $PesoTotal,
				//
				'Codigo' => $row["codigo"],
				'Nombre' => $row["nombre_producto"],
				'Cantidad' => $row["cantidad"],
				'Unitario' => number_format($row["precio"], 2),
				'SubTotal' => number_format($SubTotal, 2),
			);
		}
		
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Imprimir_Compra']))
{
	$PesoTotal = 0;
	$SubTotal = 0;
	
	$query = "SELECT compras_final.*, compras_movs.*, 
	clientes.nombre, clientes.tipo_doc, clientes.direccion, clientes.telefono, clientes.contacto_p, clientes.email,
	productos.nombre AS nombre_producto, productos.und_med AS producto_undmed, productos.peso, compras_final.peso AS peso_calculado
	FROM compras_final 
	LEFT JOIN compras_movs ON compras_final.interno = compras_movs.interno 
	LEFT JOIN clientes ON compras_final.cliente_id = clientes.cliente_id 
	LEFT JOIN productos ON productos.cod_fab = compras_movs.codigo 
	WHERE compras_final.interno = '".$_GET['Imprimir_Compra']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$PesoTotal = $row["peso"] * $row["cantidad"];
			$SubTotal = $row["nuevo_costo"] * $row["cantidad"];
			
			$data[] = array(
				"Pedido" => $row["pedido"],
				"Entrada" => $row["entrada"],
				"Factura" => $row["factura"],
				"Interno" => $row["interno"],
				"Fecha" => $row["fecha_compra"],
				//
				"Cliente" => $row["nombre"],
				"ClienteID" => $row["cliente_id"],
				"ContactoP" => $row["contacto_p"],
				"Email" => $row["email"],
				"Direccion" => $row["direccion"],
				"Telefono" => $row["telefono"],
				"Digitado_Por" => $row["digitado_por"],
				"Aprobado_Por" => $row["autorizado_por"],
				//
				"Observaciones" => $row["observaciones"],
				"SubTotal2" => number_format($row["sub_total"], 2),
				"TipoServicio1" => $row["tipo_servicio"],
				"TipoServicio2" => $row["tipo_servicio_valor"],
				"Peso_Bascula" => $row["peso_bascula"],
				"IVA" => number_format($row["iva"], 2),
				"TipoDescuento1" => $row["tipo_descuento"],
				"TipoDescuento2" => number_format($row["tipo_descuento_valor"], 2),
				"Peso_Remision" => $row["peso_remision"],
				"Peso_Calculado" => $row["peso_calculado"],
				"Total" => number_format($row["total"], 2),
				"Conductor" => $row["conductor"],
				"Placa" => $row["placa"],
				"Estado" => $row["estado"],
				//
				'Codigo' => $row["codigo"],
				'Nombre' => $row["nombre_producto"],
				'Unidad' => $row["producto_undmed"],
				'Cantidad' => $row["cantidad"],
				'Unitario' => number_format($row["nuevo_costo"], 2),
				'SubTotal' => number_format($SubTotal, 2),
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Cartera_Estado']))
{
	$data1[] = array (
		'ClienteID' => "",
		'Fecha' => "",
		'Interno' => "",
		'Remision' => "",
		'Factura' => "",
		'Valor' => 0,
		'Saldo' => 0,
		'VendedorID' => "",
		'CobradorID' => "",
		'TOTAL' => 0,
	);

	$query = "SELECT * FROM mov_clientes WHERE estado = 'Aprobado' AND cliente_id = '".$_GET['Cartera_Estado']."' 
	AND tipo_movimiento = 'Debito' ORDER BY fecha ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$rowadded = 0;
		$TotalDeuda = 0;
		while ($row = mysql_fetch_array($result))
		{
			if ($row["saldo"] > 0) {
				$TotalDeuda = $TotalDeuda + $row["saldo"];
				$data[] = array (
					'ClienteID' => $row['cliente_id'],
					'Fecha' => $row['fecha'],
					'Interno' => $row['interno'],
					'Remision' => $row['remision'],
					'Factura' => $row['factura'],
					'Valor' => number_format($row['valor'], 2),
					'Saldo' => number_format($row["saldo"], 2),
					'VendedorID' => $row['vendedor_codigo'],
					'CobradorID' => $row['cobrador_codigo'],
				);
				$rowadded++;
			}
		}
		$data[] = array (
			'ClienteID' => "",
			'Fecha' => "",
			'Interno' => "",
			'Remision' => "",
			'Factura' => "",
			'Valor' => 0,
			'Saldo' => 0,
			'VendedorID' => "",
			'CobradorID' => "",
			'TOTAL' => number_format($TotalDeuda, 2),
		);
		if ($rowadded > 0)
			echo json_encode($data);
		else
			echo json_encode($data1);
	}
	else {
		echo json_encode($data1);
	}
}
else if (isset($_GET['Imprimir_Cartera_Movimientos']))
{
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$data1[] = array (
		'Fecha' => "",
		'Remision' => "",
		'Factura' => "",
		'Caja_Recibo' => "",
		'TipoMovimiento' => "",
		'Valor' => "",
		'Saldo' => "",
		'ClienteID' => "",
		'CobradorID' => "",
	);
	
	$query = "SELECT * FROM mov_clientes WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['TipoMovimiento']) && $_GET['TipoMovimiento'] != "") ? " AND tipo_movimiento = '".$_GET['TipoMovimiento']."'":"";
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Interno']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['Caja_Recibo']) && $_GET['Caja_Recibo'] != "") ? " AND caja_recibo LIKE '%".$_GET['Caja_Recibo']."%'":"";
	$query .= " ORDER BY cobrador_codigo ASC, fecha DESC;";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	//---
	$Total_Valor = 0;
	$Total_Saldo = 0;
	$Cobrador_Total_Valor = 0;
	$Cobrador_Total_Saldo = 0;
	$Old_CobradorID = "_empty_";
	//---
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if ($Old_CobradorID != $row['cobrador_codigo'])
			{
				$Old_CobradorID = $row['cobrador_codigo'];
				$Cobrador_Total_Valor = 0;
				$Cobrador_Total_Saldo = 0;
			}
			
			$Total_Valor = $Total_Valor + $row['valor'];
			$Total_Saldo = $Total_Saldo + $row['saldo'];
			$Cobrador_Total_Valor = $Cobrador_Total_Valor + $row['valor'];
			$Cobrador_Total_Saldo = $Cobrador_Total_Saldo + $row['saldo'];
			
			$data[] = array (
				'Fecha' => $row['fecha'],
				'Remision' => $row['remision'],
				'Factura' => $row['factura'],
				'Caja_Recibo' => $row['caja_recibo'],
				'TipoMovimiento' => $row['tipo_movimiento'],
				'Valor' => number_format($row['valor'], 2),
				'Saldo' => number_format($row['saldo'], 2),
				'ClienteID' => $row['cliente_id'],
				'CobradorID' => $row["cobrador_codigo"],
				'Cobrador_Total_Valor' => number_format($Cobrador_Total_Valor, 2),
				'Cobrador_Total_Saldo' => number_format($Cobrador_Total_Saldo, 2),
			);
		}
		$data[] = array (
			'Fecha' => "",
			'Remision' => "",
			'Factura' => "",
			'Caja_Recibo' => "",
			'TipoMovimiento' => "",
			'Valor' => "",
			'Saldo' => "",
			'Nombre' => "",
			'ClienteID' => "",
			'CobradorID' => "",
			'Total_Valor' => number_format($Total_Valor, 2),
			'Total_Saldo' => number_format($Total_Saldo, 2),
		);
		echo json_encode($data);
	}
	else {
		echo json_encode($data1);
	}
}
else if (isset($_GET['Imprimir_Reportes_Cartera']))
{
	$Zero_Filter = isset($_GET['Zero_Filter']) ? $_GET['Zero_Filter']:false;
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	if ($OrderBy == "Cliente")
		$OrderBy = "cliente_id";
	else if ($OrderBy == "Cobrador")
		$OrderBy = "cobrador_codigo";
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$CodigosNombre = CodigosNombre();
	$ClienteNombre = ClientesNombre();
	
	$data1[] = array (
		'Fecha' => "",
		'Remision' => "",
		'Factura' => "",
		'ClienteID' => "",
		'CobradorID' => "",
		'TipoMovimiento' => "",
		'Valor' => "",
		'Saldo' => "",
	);
	
	$query = "SELECT * FROM mov_clientes WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND estado = 'Aprobado'";
	$query .= (isset($_GET['TipoMovimiento']) && $_GET['TipoMovimiento'] != "") ? " AND tipo_movimiento = '".$_GET['TipoMovimiento']."'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	$query .= (isset($_GET['CobradorID']) && $_GET['CobradorID'] != "") ? " AND cobrador_codigo = '".$_GET['CobradorID']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura = '".$_GET['Factura']."'":"";
	$query .= (isset($_GET['Zero_Filter']) && $_GET['Zero_Filter'] == "true") ? " AND saldo > '0'":"";
	$query .= ($OrderBy != "") ? " ORDER BY ".$OrderBy." ASC":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	//---
	$Total_Valor = 0;
	$Total_Saldo = 0;
	$Old_ID = "_empty_";
	//---
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$rows[] = $row;
		}
		
		foreach($rows AS $row)
		{
			if (isset($ID_Total_Valor[$row['cliente_id']]))
				$ID_Total_Valor[$row['cliente_id']] += $row['valor'];
			else
				$ID_Total_Valor[$row['cliente_id']] = $row['valor'];
			
			if (isset($ID_Total_Saldo[$row['cliente_id']]))
				$ID_Total_Saldo[$row['cliente_id']] += $row['saldo'];
			else
				$ID_Total_Saldo[$row['cliente_id']] = $row['saldo'];
		}
		
		foreach($rows AS $row)
		{
			if ($OrderBy != "")
			{
				if ($Old_ID != $row[$OrderBy])
				{
					$Old_ID = $row[$OrderBy];
					//$ID_Total_Valor = 0;
					//$ID_Total_Saldo = 0;
				}
			}
			
			$Total_Valor = $Total_Valor + $row['valor'];
			$Total_Saldo = $Total_Saldo + $row['saldo'];
			
			if ($OrderBy == "cliente_id")
			{
				$data[] = array (
					'Cliente' => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
					'ClienteID' => $row['cliente_id'],
					'Fecha' => $row['fecha'],
					'Remision' => $row['remision'],
					'Factura' => $row['factura'],
					'VendedorID' => $row['vendedor_codigo'],
					'Cobrador' => isset($CodigosNombre[$row["cobrador_codigo"]]) ? $CodigosNombre[$row["cobrador_codigo"]]:"No Existe!",
					'CobradorID' => $row['cobrador_codigo'],
					'TipoMovimiento' => $row['tipo_movimiento'],
					'Valor' => number_format($row['valor'], 2),
					'Saldo' => number_format($row['saldo'], 2),
					'ID_Total_Valor' => number_format($ID_Total_Valor[$row['cliente_id']], 2),
					'ID_Total_Saldo' => number_format($ID_Total_Saldo[$row['cliente_id']], 2),
				);
			}
			else if ($OrderBy == "cobrador_codigo")
			{
				$data[] = array (
					'Cobrador' => isset($CodigosNombre[$row["cobrador_codigo"]]) ? $CodigosNombre[$row["cobrador_codigo"]]:"No Existe!",
					'CobradorID' => $row['cobrador_codigo'],
					'Fecha' => $row['fecha'],
					'Remision' => $row['remision'],
					'Factura' => $row['factura'],
					'Cliente' => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
					'ClienteID' => $row['cliente_id'],
					'VendedorID' => $row['vendedor_codigo'],
					'TipoMovimiento' => $row['tipo_movimiento'],
					'Valor' => number_format($row['valor'], 2),
					'Saldo' => number_format($row['saldo'], 2),
					'ID_Total_Valor' => number_format($ID_Total_Valor[$row['cliente_id']], 2),
					'ID_Total_Saldo' => number_format($ID_Total_Saldo[$row['cliente_id']], 2),
				);
			}
			else
			{
				$data[] = array (
					'Fecha' => $row['fecha'],
					'Remision' => $row['remision'],
					'Factura' => $row['factura'],
					'Cliente' => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
					'ClienteID' => $row['cliente_id'],
					'VendedorID' => $row['vendedor_codigo'],
					'Cobrador' => isset($CodigosNombre[$row["cobrador_codigo"]]) ? $CodigosNombre[$row["cobrador_codigo"]]:"No Existe!",
					'CobradorID' => $row['cobrador_codigo'],
					'TipoMovimiento' => $row['tipo_movimiento'],
					'Valor' => number_format($row['valor'], 2),
					'Saldo' => number_format($row['saldo'], 2),
					'ID_Total_Valor' => number_format($ID_Total_Valor[$row['cliente_id']], 2),
					'ID_Total_Saldo' => number_format($ID_Total_Saldo[$row['cliente_id']], 2),
				);
			}
		}
		sort($data);
		if ($OrderBy == "cliente_id")
		{
			$data[] = array (
				'Cliente' => "",
				'ClienteID' => "",
				'Fecha' => "",
				'Remision' => "",
				'Factura' => "",
				'VendedorID' => "",
				'Cobrador' => "",
				'CobradorID' => "",
				'TipoMovimiento' => "",
				'Valor' => "",
				'Saldo' => "",
				'Total_Valor' => number_format($Total_Valor, 2),
				'Total_Saldo' => number_format($Total_Saldo, 2),
			);
		}
		else if ($OrderBy == "cobrador_codigo")
		{
			$data[] = array (
				'Cobrador' => "",
				'CobradorID' => "",
				'Fecha' => "",
				'Remision' => "",
				'Factura' => "",
				'Cliente' => "",
				'ClienteID' => "",
				'VendedorID' => "",
				'TipoMovimiento' => "",
				'Valor' => "",
				'Saldo' => "",
				'Total_Valor' => number_format($Total_Valor, 2),
				'Total_Saldo' => number_format($Total_Saldo, 2),
			);
		}
		else
		{
			$data[] = array (
				'Fecha' => "",
				'Remision' => "",
				'Factura' => "",
				'Cliente' => "",
				'ClienteID' => "",
				'VendedorID' => "",
				'Cobrador' => "",
				'CobradorID' => "",
				'TipoMovimiento' => "",
				'Valor' => "",
				'Saldo' => "",
				'Total_Valor' => number_format($Total_Valor, 2),
				'Total_Saldo' => number_format($Total_Saldo, 2),
			);
		}
		
		echo json_encode($data);
	}
	else {
		echo json_encode($data1);
	}
}
else if (isset($_GET['Imprimir_CxP_Movimientos']))
{
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
	
	$Total_Valor = 0;
	$Total_Saldo = 0;
	
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Total_Valor += $row['valor'];
			$Total_Saldo += $row['saldo'];
			
			$data[] = array (
				'Fecha' => $row['fecha_digitado'],
				'Compra_Interno' => $row['compra_interno'],
				//'Compra_Entrada' => $row['compra_entrada'],
				'Factura' => $row['factura'],
				'Caja_Interno' => $row['caja_interno'],
				'ClienteID' => $row['cliente_id'],
				'TipoMovimiento' => $row['tipo_movimiento'],
				'Valor' => number_format($row['valor'], 2),
				'Saldo' => number_format($row['saldo'], 2),
				'Total_Valor' => number_format($Total_Valor, 2),
				'Total_Saldo' => number_format($Total_Saldo, 2),
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Imprimir_CxP_Aplicar']))
{
	$data1[] = array (
		'ClienteID' => "",
		'Fecha' => "",
		'Interno' => "",
		'Entrada' => "",
		'Factura' => "",
		'Doc_Transp' => "",
		'Valor' => 0,
		'Saldo' => 0,
		'DigitadoPor' => "",
		'TOTAL' => 0,
	);

	$query = "SELECT *, SUBSTR(fecha_digitado, 1, 11) AS fecha_digitado FROM cxp_movs WHERE estado = 'Aprobado' 
	AND cliente_id = '".$_GET['Imprimir_CxP_Aplicar']."' AND tipo_movimiento = 'Compra' ORDER BY fecha_digitado ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$rowadded = 0;
		$TotalDeuda = 0;
		while ($row = mysql_fetch_array($result))
		{
			if ($row["saldo"] > 0) {
				$TotalDeuda = $TotalDeuda + $row["saldo"];
				$data[] = array (
					'ClienteID' => $row['cliente_id'],
					'Fecha' => $row['fecha_digitado'],
					'Interno' => $row['compra_interno'],
					'Entrada' => $row['compra_entrada'],
					'Factura' => $row['factura'],
					'Doc_Transp' => $row['doc_transp'],
					'Valor' => number_format($row['valor'], 2),
					'Saldo' => number_format($row["saldo"], 2),
					'DigitadoPor' => $row['digitado_por'],
				);
				$rowadded++;
			}
		}
		$data[] = array (
			'ClienteID' => "",
			'Fecha' => "",
			'Interno' => "",
			'Entrada' => "",
			'Factura' => "",
			'Doc_Transp' => "",
			'Valor' => 0,
			'Saldo' => 0,
			'DigitadoPor' => "",
			'TOTAL' => number_format($TotalDeuda, 2),
		);
		if ($rowadded > 0)
			echo json_encode($data);
		else
			echo json_encode($data1);
	}
	else {
		echo json_encode($data1);
	}
}
else if (isset($_GET['Imprimir_CxP_Listado']))
{
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$TotalDeuda = 0;
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
				$TotalDeuda += $row["saldo"];
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
		
		$ClienteNombre = ClientesNombre();
		
		foreach($data as $item)
		{
			if ($ClienteID != "" && $item["ClienteID"] != $ClienteID)
				continue;
			
			$newdata[] = array(
				"Nombre" => $ClienteNombre[$item["ClienteID"]],
				"ClienteID" => $item["ClienteID"],
				"Deuda" => number_format($Deuda[$item["ClienteID"]], 2),
				"Compra" => number_format($Compra[$item["ClienteID"]], 2),
				"Fecha" => $Fecha[$item["ClienteID"]],
				'TOTAL' => number_format($TotalDeuda, 2),
			);
		}
		sort($newdata);
		echo json_encode($newdata);
	}
}
else if (isset($_GET['Imprimir_CxP_Edades']))
{
	$Today = date('Y-m-d');
	$Until30 = date('Y-m-d', strtotime('-30 days'));
	$Until60 = date('Y-m-d', strtotime('-60 days'));
	$Until90 = date('Y-m-d', strtotime('-90 days'));
	
	$Total_Deuda = 0;
	$Total_A30 = 0;
	$Total_A60 = 0;
	$Total_A90 = 0;
	$Total_Mas90 = 0;
	
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
				$Total_Deuda += $row["saldo"];
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
				$Total_A30 += $row["saldo"];
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
				$Total_A60 += $row["saldo"];
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
				$Total_A90 += $row["saldo"];
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
				$Total_Mas90 += $row["saldo"];
				$Mas90[$row["cliente_id"]] = $row["saldo"];
			}
		}
		
		$ClienteNombre = ClientesNombre();
		
		foreach($data as $item)
		{
			$newdata[] = array(
				"Nombre" => $ClienteNombre[$item["ClienteID"]],
				"ClienteID" => $item["ClienteID"],
				"Deuda" => number_format($Deuda[$item["ClienteID"]], 2),
				"A30" => isset($A30[$item["ClienteID"]]) ? number_format($A30[$item["ClienteID"]], 2):0,
				"A60" => isset($A60[$item["ClienteID"]]) ? number_format($A60[$item["ClienteID"]], 2):0,
				"A90" => isset($A90[$item["ClienteID"]]) ? number_format($A90[$item["ClienteID"]], 2):0,
				"Mas90" => isset($Mas90[$item["ClienteID"]]) ? number_format($Mas90[$item["ClienteID"]], 2):0,
				"Total_Deuda" => number_format($Total_Deuda, 2),
				"Total_A30" => number_format($Total_A30, 2),
				"Total_A60" => number_format($Total_A60, 2),
				"Total_A90" => number_format($Total_A90, 2),
				"Total_Mas90" => number_format($Total_Mas90, 2),
			);
		}
		sort($newdata);
		echo json_encode($newdata);
	}
}
else if (isset($_GET['Imprimir_Caja_Recibo']))
{
	$Caja_Interno = isset($_GET['Caja_Interno']) ? $_GET['Caja_Interno']:"";
	
	$query = "SELECT caja_final.*, clientes.nombre FROM caja_final LEFT JOIN clientes ON clientes.cliente_id = caja_final.cliente_id 
	WHERE caja_interno = '".$Caja_Interno."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array (
			'Caja_Recibo' => $row["caja_recibo"],
			'Fecha' => $row["fecha"],
			'Categoria' => $row["categoria"],
			'Grupo' => $row["grupo"],
			'SubGrupo' => $row["subgrupo"],
			'SubGrupo2' => $row["subgrupo2"],
			'Cliente' => $row["nombre"],
			'ClienteID' => $row["cliente_id"],
			'Aplicado_A' => $row["aplicado_a"],
			'Observaciones' => $row["observaciones"],
			'Efectivo' => number_format($row["efectivo"], 2),
			'Bancos' => number_format($row["consignacion"], 2),
			'Cheques' => number_format($row["cheque"], 2),
			'Rete_IVA' => number_format($row["rete_iva"], 2),
			'Rete_ICA' => number_format($row["rete_ica"], 2),
			'Rete_Fuente' => number_format($row["rete_fuente"], 2),
			'Descuento' => number_format($row["descuento"], 2),
			'Descuento_Concepto' => $row["concepto_dcto"],
			'Valor_Total' => number_format($row["total"], 2),
			'Total' => $row["total"],//Sin Formato
			'Estado' => $row["estado"],
			'Digitado_Por' => $row["digitado_por"],
			'Fecha_Digitado' => $row["fecha_digitado"],
			'Aprobado_Por' => $row["aprobado_por"],
			'Fecha_Aprobado' => $row["fecha_aprobado"],
		);
		echo json_encode($data);
	}
}
else if (isset($_GET['Imprimir_Caja_Movimientos']))
{
	/*$test1["1234"] = 1;
	$test1["1234"] = 2;
	print_r($test1);
	echo "<br/>";
	$test2["1234"] = 1;
	$test2["1234"] += 5;
	print_r($test2);
	echo "<br/>";
	die();*/
	
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT * FROM caja_final WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Caja_Recibo']) && $_GET['Caja_Recibo'] != "") ? " AND caja_recibo LIKE '%".$_GET['Caja_Recibo']."%'":"";
	$query .= (isset($_GET['DigitadorID']) && $_GET['DigitadorID'] != "") ? " AND digitado_por = '".$_GET['DigitadorID']."'":"";
	$query .= "ORDER BY cliente_id ASC;";
	//echo $query;
	//die ();
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$LastClient = "_empty_";
	$Value = 0;
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($LastClient != $row['cliente_id'])
			{
				$LastClient = $row['cliente_id'];
				$Value = $row['total'];
			}
			else
			{
				$Value += $row['total'];
			}
			
			$ClienteID[$row['cliente_id']] = $LastClient;
			$Total[$row['cliente_id']] = $Value;
		}
		
		while ($row = mysql_fetch_array($result1, MYSQL_ASSOC))
		{
			$data[] = array (
				'Fecha' => $row['fecha'],
				'Grupo' => $row['grupo'],
				'SubGrupo' => $row['subgrupo'],
				'Caja_Recibo' => $row['caja_recibo'],
				'Valor' => number_format($row['total'], 2),
				'AprobadorID' => $row['aprobado_por'],
				'DigitadorID' => $row['digitado_por'],
				'ClienteID' => $ClienteID[$row['cliente_id']],
				'Cliente_Total_Valor' => number_format($Total[$row['cliente_id']], 2),
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Imprimir_Caja_Cheques']))
{
	$Today = date('Y-m-d');
	
	$query = "SELECT * FROM cheques";
	switch ($_GET['Estado'])
	{
		case "Al Dia":
			$query .= " WHERE estado = 'Aprobado' AND estado_cheque = 'Al Dia'  AND DATE(fecha_cheque) <= '".$Today."'";
		break;
		case "Postfechado":
			$query .= " WHERE estado = 'Aprobado' AND estado_cheque like '%echado%' AND DATE(fecha_cheque) > '".$Today."'";
		break;
		default:
			$query .= " WHERE estado = 'Aprobado' AND estado_cheque = '".$_GET['Estado']."'";
		break;
	}
	$query .= isset($_GET['ClienteID']) ? " AND cliente_id LIKE '%".$_GET['ClienteID']."%'":"";
	$query .= " ORDER BY cliente_id ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$LastClient = "_empty_";
	$Value = 0;
	
	if (mysql_num_rows($result)>0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($LastClient != $row['cliente_id'])
			{
				$LastClient = $row['cliente_id'];
				$Value = $row['valor'];
			}
			else
			{
				$Value += $row['valor'];
			}
			
			$ClienteID[$row['cliente_id']] = $LastClient;
			$Total[$row['cliente_id']] = $Value;
		}
		
		while ($row = mysql_fetch_array($result1, MYSQL_ASSOC))
		{
			$data[] = array (
				'Cheque' => $row['cheque'],
				'Banco_Num' => $row['banco'],
				'Valor' => number_format($row['valor'], 2),
				'Fecha_Cheque' => $row['fecha_cheque'],
				'Banco' => $row['banco_destino'],
				'Cuenta' => $row['cuenta'],
				'Caja_Recibo' => $row['caja_recibo'],
				'Fecha' => $row['fecha'],
				'ClienteID' => $ClienteID[$row['cliente_id']],
				'Cliente_Total_Valor' => number_format($Total[$row['cliente_id']], 2),
			);
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array ();
		echo json_encode($data);
	}
}
else if (isset($_GET['Imprimir_Caja_General']))
{
	$LastType = "_empty_";
	$Value = 0;
	$TOTAL = 0;
	
	$Tipo = isset($_GET['Tipo']) ? $_GET['Tipo']:"banco";
	$Tipo = strtolower($Tipo);
	$Banco = isset($_GET['Banco']) ? $_GET['Banco']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	$query = "SELECT DISTINCT ".$Tipo." FROM bancos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$query = "SELECT * FROM bancos WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' 
			AND ".$Tipo." = '".$row[0]."' AND banco LIKE '%".$Banco."%' ORDER BY ".$Tipo." ASC";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result1) > 0)
			{
				while ($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
				{
					if ($LastType != $row1[$Tipo])
					{
						$LastType = $row1[$Tipo];
						$Value = $row1["valor"];
					}
					else
					{
						$Value += $row1["valor"];
					}
					
					$TOTAL += $row1["valor"];
					$Type[$row1[$Tipo]] = $LastType;
					$Total[$row1[$Tipo]] = $Value;
				}
				
				while ($row1 = mysql_fetch_array($result2, MYSQL_ASSOC))
				{
					$data[] = array (
						"Fecha" => $row1["fecha"],
						"Tipo" => $row1["tipo"],
						"Banco" => $row1["banco"],
						"Valor" => number_format($row1["valor"], 2),
						"Numero" => $row1["numero"],
						"ClienteID" => $row1["cliente_id"],
						"Caja_Interno" => $row1["caja_interno"],
						"Caja_Recibo" => $row1["caja_recibo"],
						"DigitadorID" => $row1["digitado_por"],
						"Tipo2" => $Type[$row1[$Tipo]],
						"Tipo_Total_Valor" => number_format($Total[$row1[$Tipo]], 2),
						"TOTAL" => number_format($TOTAL, 2),
					);
				}
				//print_r($data);
				//echo json_encode($data);
			}
		}
		echo json_encode($data);
	}
	else
	{
		$data[] = array ();
		echo json_encode($data);
	}
}
else if (isset($_GET['Imprimir_Produccion']))
{
	$query = "SELECT produccion_final.orden_compra, produccion_final.solicitud, produccion_final.interno, produccion_final.destino, 
	produccion_final.cliente_id, produccion_final.fecha, produccion_final.estado, produccion_final.operario_trefilado, 
	produccion_final.operario_enderezado, produccion_final.operario_electrosoldado, produccion_final.operario_figurado, 
	produccion_final.digitado_por, produccion_final.fecha_digitado, produccion_final.aprobado_por, produccion_final.fecha_aprobado, 
	produccion_final.modificado_por, produccion_final.fecha_modificado, clientes.nombre FROM produccion_final 
	LEFT JOIN clientes ON clientes.cliente_id = produccion_final.cliente_id WHERE produccion_final.orden_produccion = '".$_GET['Imprimir_Produccion']."'";
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT codigo, nombre, peso, und_med, cantidad, tipo, origen, destino, orden_compra, interno, cliente_id FROM produccion_movs 
	LEFT JOIN productos ON productos.cod_fab = produccion_movs.codigo WHERE produccion_movs.orden_produccion = '".$_GET['Imprimir_Produccion']."'";
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	if (mysql_num_rows($result1) > 0)
	{
		$row1 = mysql_fetch_array($result1);
		$i = 0;
		
		$query = "SELECT nombre FROM clientes WHERE cliente_id = '".$row1['operario_trefilado']."'";
		$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result3) > 0)
			$row3 = mysql_fetch_array($result3);
		
		$query = "SELECT nombre FROM clientes WHERE cliente_id = '".$row1['operario_enderezado']."'";
		$result4 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
		if (mysql_num_rows($result4) > 0)
			$row4 = mysql_fetch_array($result4);
		
		$query = "SELECT nombre FROM clientes WHERE cliente_id = '".$row1['operario_electrosoldado']."'";
		$result5 = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
		if (mysql_num_rows($result5) > 0)
			$row5 = mysql_fetch_array($result5);
		
		$query = "SELECT nombre FROM clientes WHERE cliente_id = '".$row1['operario_figurado']."'";
		$result6 = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
		if (mysql_num_rows($result6) > 0)
			$row6 = mysql_fetch_array($result6);
		
		
		while ($row2 = mysql_fetch_array($result2))
		{
			$i++;
			$PesoTotal = $row2['peso'] * $row2['cantidad'];
			if ($i == 1) {
				$data[] = array(
					'Interno' => $row1['interno'],
					'ClienteID' => $row1['cliente_id'],
					'Cliente' => $row1['nombre'],
					'Solicitud' => $row1['solicitud'],
					'DestinoOrden' => $row1['destino'],
					'Fecha' => $row1['fecha'],
					'Trefilado' => isset($row3['nombre']) ? $row3['nombre']:"",
					'Enderezado' => isset($row4['nombre']) ? $row4['nombre']:"",
					'Soldado' => isset($row5['nombre']) ? $row5['nombre']:"",
					'Figurado' => isset($row6['nombre']) ? $row6['nombre']:"",
					'DigitadoPor' => $row1['digitado_por'],
					'FechaDigitado' => $row1['fecha_digitado'],
					'AprobadoPor' => $row1['aprobado_por'],
					'FechaAprobado' => $row1['fecha_aprobado'],
					'ModificadoPor' => $row1['modificado_por'],
					'FechaModificado' => $row1['fecha_modificado'],
					'Estado' => $row1['estado'],
					//
					'Tipo' => $row2['tipo'],
					'CodFab' => $row2['codigo'],
					'Nombre' => $row2['nombre'],
					'Peso' => $PesoTotal,
					'UndMed' => $row2['und_med'],
					'Cantidad' => number_format($row2['cantidad'], 2),
					'Origen' => $row2['origen'],
					'Destino' => $row2['destino'],
				);
			}
			else
			{
				$data[] = array(
					'Tipo' => $row2['tipo'],
					'CodFab' => $row2['codigo'],
					'Nombre' => $row2['nombre'],
					'Peso' => $PesoTotal,
					'UndMed' => $row2['und_med'],
					'Cantidad' => number_format($row2['cantidad'], 2),
					'Origen' => $row2['origen'],
					'Destino' => $row2['destino'],
				);
			}
		}
		echo json_encode($data);
	}
	else
	{
		$data = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Imprimir_Produccion_Procesos']))
{
	$Proceso = isset($_GET['Proceso']) ? $_GET['Proceso']:"";
	
	switch($Proceso)
	{
		case "Trefilado":
			$query = "SELECT *, operario_trefilado AS operario FROM produccion_final WHERE 
			orden_produccion = '".$_GET['Ord_Produccion']."' AND operario_trefilado != ''";
		break;
		case "Corte y Enderezado":
			$query = "SELECT *, operario_enderezado AS operario FROM produccion_final WHERE 
			orden_produccion = '".$_GET['Ord_Produccion']."' AND operario_enderezado != ''";
		break;
		case "Electrosoldado":
			$query = "SELECT *, operario_electrosoldado AS operario FROM produccion_final WHERE 
			orden_produccion = '".$_GET['Ord_Produccion']."' AND operario_electrosoldado != ''";
		break;
		case "Figurado":
			$query = "SELECT *, operario_figurado AS operario FROM produccion_final WHERE 
			orden_produccion = '".$_GET['Ord_Produccion']."' AND operario_figurado != ''";
		break;
		default:
			die();
		break;
	}
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT SUBSTR(fecha_ini, 1, 10) AS fecha_ini, SUBSTR(fecha_fin, 1, 10) AS fecha_fin, estado, observaciones, 
	rendimiento FROM produccion_proc WHERE orden_produccion = '".$_GET['Ord_Produccion']."' AND proceso = '".$Proceso."'";
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	
	$query = "SELECT * FROM produccion_proc_movs WHERE orden_produccion = '".$_GET['Ord_Produccion']."'";
	$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	
	if (mysql_num_rows($result2) > 0)
	{
		$row = mysql_fetch_array($result2);
		$data1[] = array(
			'Fecha_Ini' => $row['fecha_ini'],
			'Fecha_Fin' => $row['fecha_fin'],
			'Observaciones' => $row['observaciones'],
			'Rendimiento' => $row['rendimiento'],
			'Estado' => $row['estado'],
		);
	}
	
	if (mysql_num_rows($result3) > 0)
	{
		while ($row = mysql_fetch_array($result3))
		{
			$Passed = false;
			switch($row['tipo'])
			{
				case "Requerido":
					if ($row['destino'] == $Proceso)
					{
						$Requerido[] = array(
							'CodFab' => $row['codigo'],
							'Cantidad' => $row['cantidad'],
							'Origen' => $row['origen'],
						);
					}
				break;
				case "Obtener":
					if ($row['origen'] == $Proceso)
					{
						$Obtener[] = array(
							'CodFab' => $row['codigo'],
							'Cantidad' => $row['cantidad'],
							'Destino' => $row['destino'],
						);
						
						if ($row['operario'] != "")
						{
							$Operarios[] = array(
								'Maquinaria' => $row['maquinaria'],
								'Operario' => $row['operario'],
							);
						}
					}
				break;
				case "Despunte":
					if ($row['origen'] == $Proceso)
					{
						$Despunte[] = array(
							'CodFab' => $row['codigo'],
							'Cantidad' => $row['cantidad'],
						);
					}
				break;
			}
		}
	}
	
	if (mysql_num_rows($result1) > 0)
	{
		$row = mysql_fetch_array($result1);
		
		$data[] = array(
			'Destino' => $row['destino'],
			'ClienteID' => $row['cliente_id'],
			'Fecha_Ini' => $data1[0]["Fecha_Ini"],
			'Fecha_Fin' => $data1[0]["Fecha_Fin"],
			'DigitadorID' => $row['digitado_por'],
			'Fecha_Digitado' => $row['fecha_digitado'],
			'AprobadorID' => $row['aprobado_por'],
			'Fecha_Aprobado' => $row['fecha_aprobado'],
			'Operario' => $row['operario'],
			'Rendimiento' => $data1[0]["Rendimiento"],
			'Observaciones' => $data1[0]["Observaciones"],
			'Estado' => $data1[0]["Estado"],
			'MaterialRequerido' => isset($Requerido) ? $Requerido:array(),
			'ProductosObtener' => isset($Obtener) ? $Obtener:array(),
			'Despuntes' => isset($Despunte) ? $Despunte:array(),
			'ListaOperarios' => isset($Operarios) ? $Operarios:array(),
		);
	}
	else
	{
		$data[] = array();
	}
	
	echo json_encode($data);
}
else if (isset($_GET['Imprimir_Produccion_Total']))
{
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
			
			$data[] = array (
				"Operario" => $ClienteNombre[$row["operario"]],
				"Proceso" => $ProduccionProceso[$row["orden_produccion"]],//$row["origen"],
				"Fecha" => $ProduccionFecha[$row["orden_produccion"]],
				"Ord_Produccion" => $row["orden_produccion"],
				"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				"Producto" => isset($ProductoNombre[$row["codigo"]]) ? $ProductoNombre[$row["codigo"]]:"",
				"Peso" => $Peso,
				"Maquinaria" => $row["maquinaria"],
				"DigitadoPor" => $ProduccionDigitadoPor[$row["orden_produccion"]],
				"Estado" => $ProduccionEstado[$row["orden_produccion"]],
				"Total" => $TOTAL,
			);
		}
		sort($data);
	}
	else
	{
		$data[] = array ();
	}
	echo json_encode($data);
}
else if (isset($_GET['GetFullData']))
{
	// Clientes
	$query = "SELECT * FROM clientes WHERE id != '1' AND activo = 'true' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
			
			/*if ($row['vigencia_credito'] < $Today)
				$Credito = 0;
			else
				$Credito = $row['credito'];
			
			if ($row['vigencia_adicional'] < $Today)
				$Adicional = 0;
			else
				$Adicional = $row['adicional'];
			
			$Total = $Credito + $Adicional;
			*/
			$Total = $row['credito'] + $row['adicional'];
			
			$GlobalClientesFullData[] = array(
				'Nombre' => $row['nombre'],
				'ClienteID' => $row['cliente_id'],
				'Direccion' => $row['direccion'],
				'ContactoP' => $row['contacto_p'],
				'TelefonoCP' => $row['telefono_cp'],
				'Telefono' => $row['telefono'],
				'Email' => $row['email'],
				'CupoCR' => $Total,
				'Cupo_Credito' => $row['credito'],
				'Cupo_Adicional' => $row['adicional'],
				'Vigencia_Credito' => $row['vigencia_credito'],
				'Vigencia_Adicional' => $row['vigencia_adicional'],
				'CupoCR_Check' => $row['credito_activo'],
				'Garantia' => $row['garantia'],
				'EstadoC' => $row['estado_cuenta'],
				'CobradorID' => $row['cobrador_codigo'],
			);
		}
	}
	
	// Vendedores/Cobradores -> Codigo
	$query = "SELECT user_id, user_code FROM login WHERE active = 'true' AND user_lvl = 'Vendedor' OR user_lvl = 'Administrador'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$GlobalVendedoresData[] = array(
				'Nombre' => isset($ClienteNombre[$row["user_id"]]) ? $ClienteNombre[$row["user_id"]]:"No Existe!",
				'Codigo' => $row['user_code'],
			);
		}
		sort($GlobalVendedoresData);
	}
	
	// Chofer/Conductor
	$query = "SELECT DISTINCT cliente_id FROM contratos WHERE charge = 'Conductor' AND active = 'true'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$dataTmp1[] = array(
			'Chofer' => "Externo",
			'ClienteID' => "000000000-0",
		);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$dataTmp2[] = array(
				'Chofer' => $ClienteNombre[$row["cliente_id"]],
				'ClienteID' => $row["cliente_id"],
			);
		}
		sort($dataTmp2);
		$GlobalChoferData = array_merge($dataTmp1, $dataTmp2);
	}
	else
	{
		$GlobalChoferData[] = array(
			'Chofer' => "Externo",
			'ClienteID' => "000000000-0",
		);
	}
	
	// Vehiculos
	$query = "SELECT * FROM par_fac_vehiculo";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #5: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$GlobalVehiculosData[] = array(
				'ID' => $row['id'],
				'Placa' => $row['placa'],
				'Modelo' => $row['modelo'],
				'Tipo' => $row['tipo']
			);
		}
	}
	
	// Productos && Produccion->Despuntes
	$query = "SELECT * FROM productos WHERE activo = 'true' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #6: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$GlobalProductosFullData[] = array(
				'Codigo' => $row['cod_fab'],
				'Categoria' => $row['categoria'],
				'Grupo' => $row['grupo'],
				'SubGrupo' => $row['subgrupo'],
				'Nombre' => $row['nombre'],
				'UndMed' => $row['und_med'],
				'Peso' => $row['peso'],
				'Uso' => $row['uso'],
				'Costo' => $row['costo'],
				'UltCosto' => $row['ultimo_costo'],
				'CostoProm' => $row['costo_promedio'],
				'Lista1' => $row['lista1'],
				'Lista2' => $row['lista2'],
				'Lista3' => $row['lista3'],
				'Lista4' => $row['lista4'],
				'Existencia' => $row['existencia'],
				'FacturaExistencia' => $row['factura_sin_existencia'],
				'Produccion' => $row['produccion'],
			);
			
			if ($row["categoria"] == "Hierro" && $row["grupo"] == "despunte")
			{
				$GlobalDespuntesData[] = array(
					'Codigo' => $row['cod_fab'],
					'Nombre' => $row['nombre'],
				);
			}
		}
	}
	
	// Repuestos
	$query = "SELECT * FROM repuestos ORDER BY nombre";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #7: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$GlobalRepuestosFullData[] = array(
				'ID' => $row['id'],
				'Codigo' => $row['codigo'],
				'Nombre' => $row['nombre'],
				'Peso' => $row['peso'],
				'Valor' => $row['valor'],
			);
		}
	}
	
	//tambien puede usarse -> serialize();
	$FullData[] = Array(
		"GlobalClientesFullData" => isset($GlobalClientesFullData) ? $GlobalClientesFullData:array(),
		"GlobalClientesFullData_MD5" => isset($GlobalClientesFullData) ? md5(json_encode($GlobalClientesFullData)):"",
		"GlobalVendedoresData" => isset($GlobalVendedoresData) ? $GlobalVendedoresData:array(),
		"GlobalVendedoresData_MD5" => isset($GlobalVendedoresData) ? md5(json_encode($GlobalVendedoresData)):"",
		"GlobalChoferData" => isset($GlobalChoferData) ? $GlobalChoferData:array(),
		"GlobalChoferData_MD5" => isset($GlobalChoferData) ? md5(json_encode($GlobalChoferData)):"",
		"GlobalVehiculosData" => isset($GlobalVehiculosData) ? $GlobalVehiculosData:array(),
		"GlobalVehiculosData_MD5" => isset($GlobalVehiculosData) ? md5(json_encode($GlobalVehiculosData)):"",
		"GlobalProductosFullData" => isset($GlobalProductosFullData) ? $GlobalProductosFullData:array(),
		"GlobalProductosFullData_MD5" => isset($GlobalProductosFullData) ? md5(json_encode($GlobalProductosFullData)):"",
		"GlobalRepuestosFullData" => isset($GlobalRepuestosFullData) ? $GlobalRepuestosFullData:array(),
		"GlobalRepuestosFullData_MD5" => isset($GlobalRepuestosFullData) ? md5(json_encode($GlobalRepuestosFullData)):"",
	);
	
	echo json_encode($FullData);
}
else if (isset($_GET["Clientes"]))
{
	$Contains = isset($_GET['Contains']) ? $_GET['Contains']:"";
	$Limit = isset($_GET['Limit']) ? $_GET['Limit']:0;
	
	$query = "SELECT * FROM clientes WHERE id != '1' AND activo = 'true' 
	AND nombre LIKE '%".$Contains."%' ORDER BY nombre ASC";
	$query .= $Limit > 0 ? " LIMIT ".$Limit:"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Nombre' => $row['nombre'],
				'ClienteID' => $row['cliente_id'],
			);
		}
		echo json_encode($data);
	}
}
else
{
	$query = "SELECT nombre, cliente_id, direccion, telefono FROM clientes WHERE id != '1' AND activo = 'true' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Nombre' => $row['nombre'],
				'ClienteID' => $row['cliente_id'],
				'Direccion' => $row['direccion'],
				'Telefono' => $row['telefono'],
			);
		}
		echo json_encode($data);
	}
	else {
		$data[] = array();
		echo json_encode($data);
	}
	
    /*switch(json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - Sin errores';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Excedido tamaño máximo de la pila';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Desbordamiento de buffer o los modos no coinciden';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Encontrado carácter de control no esperado';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Error de sintaxis, JSON mal formado';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
        break;
        default:
            echo ' - Error desconocido';
        break;
    };*/
	//echo "nada";
}
?>