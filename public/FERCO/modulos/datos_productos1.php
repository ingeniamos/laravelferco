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

function Get_APU_Info($Class)
{
	global $DEBUG;
	
	$query = "SELECT * FROM `apu_".$Class."` ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoNombre[$row["codigo"].$Class] = $row["nombre"];
			$ProductoUnidad[$row["codigo"].$Class] = $row["unidad"];
		}
		$data[] = array(
			"ProductoNombre" => $ProductoNombre,
			"ProductoUnidad" => $ProductoUnidad,
		);
	}
	else
	{
		$data[] = array(
			"ProductoNombre" => "",
			"ProductoUnidad" => "",
		);
	}
	return $data;
}

function CleanNumbers($Number)
{
	if(strstr($Number, ","))
	{
		$Number = str_replace(",", "", $Number);
	}
	
	/*if(strstr($Number, "."))
	{
		$Number = str_replace(".", "", $Number);
	}*/
	
	return $Number;
}

function GetProductFullInfo($Type)
{
	global $DEBUG;
	
	if ($Type == "Requerimientos")
	{
		$query = "SELECT * FROM requerimientos_productos ORDER BY nombre ";
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
		$query = "SELECT * FROM productos ORDER BY nombre ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result))
			{
				$data[$row["cod_fab"]] = array(
					"Nombre" => $row["nombre"],
					"Unidad" => $row["und_med"],
					"Peso" => $row["peso"],
					"Uso" => $row["uso"],
					"K" => $row["k"],
					"Valor" => $row["costo"],
					"Existencia" => $row["existencia"],
					"Stock" => $row["stock_minimo"],
				);
			}
		}
	}
	return $data;
}

if (isset($_GET['Productos']))
{
	// get data and store in a json array
	$query = "SELECT nombre, und_med, peso, produccion, factura_sin_existencia FROM productos WHERE cod_fab = '".$_GET['Productos']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Nombre' => $row['nombre'],
			'UndMed' => $row['und_med'],
			'Peso' => $row['peso'],
			'Produccion' => $row['produccion'],
			'FacturaExistencia' => $row['factura_sin_existencia'],
		);
	}
	if (!isset($data))
		echo json_encode(array());
	else
		echo json_encode($data);
}
else if (isset($_GET['Repuestos']))
{
	$query = "SELECT * FROM repuestos ORDER BY nombre";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Codigo' => $row['codigo'],
				'Nombre' => $row['nombre'],
				'Parte' => $row['parte'],
				'Peso' => $row['peso'],
				'Valor' => $row['valor'],
			);
		}
	}
	else
	{
		$data[] = array();
	}
	echo json_encode($data);
}
else if (isset($_GET['Compras_Productos']))
{
	// get data and store in a json array
	$query = "SELECT nombre, und_med, peso FROM productos WHERE cod_fab = '".$_GET['Compras_Productos']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Nombre' => $row['nombre'],
			'UndMed' => $row['und_med'],
			'Peso' => $row['peso'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Precios']))
{
	$query = "SELECT lista1, lista2, lista3, lista4, existencia, factura_sin_existencia FROM productos WHERE cod_fab = '".$_GET['Precios']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Existencia' => $row['existencia'],
			'Lista1' => $row['lista1'],
			'Lista2' => $row['lista2'],
			'Lista3' => $row['lista3'],
			'Lista4' => $row['lista4'],
			'FacturaExistencia' => $row['factura_sin_existencia'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Compras_Precios']))
{
	$query = "SELECT ultimo_costo FROM productos WHERE cod_fab = '".$_GET['Compras_Precios']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'UltCosto' => $row['ultimo_costo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Products_Movil']))
{
	// get data and store in a json array
	$query = "SELECT peso FROM productos WHERE cod_fab = '".$_GET['Products_Movil']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Peso' => $row['peso'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Ventas_Modificar']))
{
	$check = false;
	$query = "SELECT cod_fab, nombre, und_med, peso, existencia, cantidad, produccion, factura_sin_existencia, `desc`, precio 
	FROM fact_movs LEFT JOIN productos ON fact_movs.codigo = productos.cod_fab WHERE fact_movs.interno = '".$_GET['Ventas_Modificar']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		$query = "SELECT interno, remision, fecha_remision, factura, orden_produccion, cliente_id, ruta, direccion_entrega, 
		forma_pago, caja_interno, caja_recibo, vendedor_codigo, cobrador_codigo, chofer, placa, tipo_servicio, tipo_servicio_valor, tipo_descuento, 
		tipo_descuento_valor, observaciones, notas, estado, beneficiario_id FROM fact_final 
		WHERE interno = '".$_GET['Ventas_Modificar']."'";
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		
		if (mysql_num_rows($result2) > 0)
		{
			$row2 = mysql_fetch_array($result2);
			while ($row = mysql_fetch_array($result))
			{
				if ($check == false)
				{
					$data[] = array(
					'Interno' => $row2['interno'],
					'Remision' => $row2['remision'],
					'Fecha' => $row2['fecha_remision'],
					'Factura' => $row2['factura'],
					'Ord_Produccion' => $row2['orden_produccion'],
					'ClienteID' => $row2['cliente_id'],
					'Ruta' => $row2['ruta'],
					'Direccion' => $row2['direccion_entrega'],
					'FormaP' => $row2['forma_pago'],
					'Caja_Interno' => $row2['caja_interno'],
					'Caja_Recibo' => $row2['caja_recibo'],
					'VendedorID' => $row2['vendedor_codigo'],
					'CobradorID' => $row2['cobrador_codigo'],
					'Conductor' => $row2['chofer'],
					'Placa' => $row2['placa'],
					'TipoServicio' => $row2['tipo_servicio'],
					'TipoServicioValor' => $row2['tipo_servicio_valor'],
					'TipoDescuento' => $row2['tipo_descuento'],
					'TipoDescuentoValor' => $row2['tipo_descuento_valor'],
					'Notas' => $row2['notas'],
					'Observaciones' => $row2['observaciones'],
					'Estado' => $row2['estado'],
					'BeneficiarioID' => $row2['beneficiario_id'],
					//--
					'CodFab' => $row['cod_fab'],
					'Nombre' => $row['nombre'],
					'UndMed' => $row['und_med'],
					'Peso' => $row['peso'],
					'Existencia' => $row['existencia'],
					'Cantidad' => $row['cantidad'],
					'Produccion' => $row['produccion'],
					'FacturaExistencia' => $row['factura_sin_existencia'],
					'Dcto' => $row['desc'],
					'Unitario' => $row['precio'],
					);
					$check = true;
				}
				else {
					$data[] = array(
						'CodFab' => $row['cod_fab'],
						'Nombre' => $row['nombre'],
						'UndMed' => $row['und_med'],
						'Peso' => $row['peso'],
						'Existencia' => $row['existencia'],
						'Cantidad' => $row['cantidad'],
						'Produccion' => $row['produccion'],
						'FacturaExistencia' => $row['factura_sin_existencia'],
						'Dcto' => $row['desc'],
						'Unitario' => $row['precio'],
					);
				}
			}
			echo json_encode($data);
		}
	}
	else
	{
		$data[] = array();	
		echo json_encode($data);
	}
	
}
else if (isset($_GET['Ventas_Despachar']))
{
	$query = "SELECT interno, remision, fecha_remision, factura, fact_final.cliente_id, ruta, direccion_entrega, 
	forma_pago, caja_interno, caja_recibo, fact_final.vendedor_codigo, fact_final.cobrador_codigo, chofer, placa, tipo_servicio, 
	tipo_servicio_valor, tipo_descuento, tipo_descuento_valor, observaciones, clientes.nombre, clientes.direccion, 
	clientes.email, clientes.telefono, clientes.contacto_p FROM fact_final LEFT JOIN clientes ON 
	clientes.cliente_id = fact_final.cliente_id WHERE interno = '".$_GET['Ventas_Despachar']."' AND estado = 'Autorizado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$query = "SELECT cod_fab, nombre, und_med, peso, existencia, cantidad, cantidad_despachada, produccion, 
		factura_sin_existencia, `desc`, precio FROM fact_movs LEFT JOIN productos ON fact_movs.codigo = productos.cod_fab 
		WHERE fact_movs.interno = '".$_GET['Ventas_Despachar']."' ";
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result2) > 0)
		{
			$check = false;
			
			/*$find_olders = "SELECT interno FROM fact_final WHERE estado = 'Anulado' AND orden_compra = '".$_GET['Ventas_Despachar']."'";
			$find_result = mysql_query($find_olders) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			$find_row = mysql_fetch_array($find_result);
			$a = 0;*/
			while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
			{
				$cantidad_total = ($row2['cantidad'] - $row2['cantidad_despachada']);
				/*if (mysql_num_rows($find_result) > 0) {
					$quanty = "SELECT cantidad FROM fact_movs WHERE orden_compra = '".$_GET['Ventas_Despachar']."' 
					AND codigo = '".$row2['cod_fab']."' AND interno = '".$find_row['interno']."'";
					$res = mysql_query($quanty) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
					$qty = mysql_fetch_array($res, MYSQL_ASSOC);
					$cantidad_total = $cantidad_total + $qty['cantidad'];
				}*/
				
				if ($check == false)
				{
					$data[] = array(
						'Direccion' => $row['direccion'],
						'Email' => $row['email'],
						'Telefono' => $row['telefono'],
						'ContactoP' => $row['contacto_p'],
						'Interno' => $row['interno'],
						'Remision' => $row['remision'],
						'Fecha' => $row['fecha_remision'],
						'Factura' => $row['factura'],
						'ClienteNombre' => $row['nombre'],
						'ClienteID' => $row['cliente_id'],
						'Ruta' => $row['ruta'],
						'DireccionEntrega' => $row['direccion_entrega'],
						'FormaP' => $row['forma_pago'],
						'Caja_Interno' => $row['caja_interno'],
						'Caja_Recibo' => $row['caja_recibo'],
						'VendedorID' => $row['vendedor_codigo'],
						'CobradorID' => $row['cobrador_codigo'],
						'Conductor' => $row['chofer'],
						'Placa' => $row['placa'],
						'TipoServicio' => $row['tipo_servicio'],
						'TipoServicioValor' => $row['tipo_servicio_valor'],
						'TipoDescuento' => $row['tipo_descuento'],
						'TipoDescuentoValor' => $row['tipo_descuento_valor'],
						'Observaciones' => $row['observaciones'],
						//--
						'CodFab' => $row2['cod_fab'],
						'Nombre' => $row2['nombre'],
						'UndMed' => $row2['und_med'],
						'Peso' => $row2['peso'],
						'Existencia' => $row2['existencia'],
						'Cantidad' => $cantidad_total,
						'Produccion' => $row2['produccion'],
						'FacturaExistencia' => $row2['factura_sin_existencia'],
						'Dcto' => $row2['desc'],
						'Unitario' => $row2['precio'],
					);
					$check = true;
				}
				else {
					$data[] = array(
						'CodFab' => $row2['cod_fab'],
						'Nombre' => $row2['nombre'],
						'UndMed' => $row2['und_med'],
						'Peso' => $row2['peso'],
						'Existencia' => $row2['existencia'],
						'Cantidad' => $cantidad_total,
						'Produccion' => $row2['produccion'],
						'FacturaExistencia' => $row2['factura_sin_existencia'],
						'Dcto' => $row2['desc'],
						'Unitario' => $row2['precio'],
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
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Ventas_Ajustar']))
{
	$query = "SELECT interno, orden_compra, remision, fecha_remision, factura, fact_final.cliente_id, ruta, direccion_entrega, 
	forma_pago, fact_final.vendedor_codigo, chofer, placa, tipo_servicio, tipo_servicio_valor, tipo_descuento, tipo_descuento_valor, 
	total, observaciones, clientes.nombre, clientes.direccion, clientes.email, clientes.telefono, clientes.contacto_p FROM fact_final 
	LEFT JOIN clientes ON clientes.cliente_id = fact_final.cliente_id WHERE interno = '".$_GET['Ventas_Ajustar']."' AND estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$query = "SELECT cod_fab, nombre, und_med, peso, existencia, cantidad, cantidad_despachada, produccion, 
		factura_sin_existencia, `desc`, precio FROM fact_movs LEFT JOIN productos ON fact_movs.codigo = productos.cod_fab 
		WHERE interno = '".$_GET['Ventas_Ajustar']."' AND orden_compra = '".$row['orden_compra']."'";
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		if (mysql_num_rows($result2) > 0)
		{
			$check = false;
			
			while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
			{
				$cantidad_total = ($row2['cantidad'] - $row2['cantidad_despachada']);
				
				if ($check == false)
				{
					$data[] = array(
						'Direccion' => $row['direccion'],
						'Email' => $row['email'],
						'Telefono' => $row['telefono'],
						'ContactoP' => $row['contacto_p'],
						'Interno' => $row['interno'],
						'Remision' => $row['remision'],
						'Fecha' => $row['fecha_remision'],
						'Factura' => $row['factura'],
						'ClienteNombre' => $row['nombre'],
						'ClienteID' => $row['cliente_id'],
						'Ruta' => $row['ruta'],
						'DireccionEntrega' => $row['direccion_entrega'],
						'FormaP' => $row['forma_pago'],
						'VendedorID' => $row['vendedor_codigo'],
						'Conductor' => $row['chofer'],
						'Placa' => $row['placa'],
						'TipoServicio' => $row['tipo_servicio'],
						'TipoServicioValor' => $row['tipo_servicio_valor'],
						'TipoDescuento' => $row['tipo_descuento'],
						'TipoDescuentoValor' => $row['tipo_descuento_valor'],
						'Total' => $row['total'],
						'Observaciones' => $row['observaciones'],
						//--
						'CodFab' => $row2['cod_fab'],
						'Nombre' => $row2['nombre'],
						'UndMed' => $row2['und_med'],
						'Peso' => $row2['peso'],
						'Existencia' => $row2['existencia'],
						'Cantidad' => $cantidad_total,
						'Produccion' => $row2['produccion'],
						'FacturaExistencia' => $row2['factura_sin_existencia'],
						'Dcto' => $row2['desc'],
						'Unitario' => $row2['precio'],
					);
					$check = true;
				}
				else {
					$data[] = array(
						'CodFab' => $row2['cod_fab'],
						'Nombre' => $row2['nombre'],
						'UndMed' => $row2['und_med'],
						'Peso' => $row2['peso'],
						'Existencia' => $row2['existencia'],
						'Cantidad' => $cantidad_total,
						'Produccion' => $row2['produccion'],
						'FacturaExistencia' => $row2['factura_sin_existencia'],
						'Dcto' => $row2['desc'],
						'Unitario' => $row2['precio'],
					);
				}
			}			
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Inventario_Search_Filter']))
{
	$query = "SELECT cod_fab, nombre FROM productos WHERE categoria LIKE '%".$_GET['Categoria']."%' 
	AND grupo LIKE '%".$_GET['Grupo']."%' AND subgrupo LIKE '%".$_GET['SubGrupo']."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'CodFab' => $row['cod_fab'],
				'Nombre' => $row['nombre'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Inventario_Listado']))
{
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
			$data[] = array(
				"ID" => $row['id'],
				"Activo" => $row['activo'],
				"Motivo" => $row['motivo'],
				"Codigo" => $row['cod_fab'],
				"Categoria" => $row['categoria'],
				"Grupo" => $row['grupo'],
				"SubGrupo" => $row['subgrupo'],
				"Nombre" => $row['nombre'],
				"Und" => $row['und_med'],
				"Peso" => $row['peso'],
				"Stock" => $row['stock_minimo'],
				"Costo" => $row['costo'],
				"Ult_Costo" => $row['ultimo_costo'],
				"Costo_Prom" => $row['costo_promedio'],
				"Lista1" => $row['lista1'],
				"Lista2" => $row['lista2'],
				"Lista3" => $row['lista3'],
				"Lista4" => $row['lista4'],
				"Produccion" => $row['produccion'],
				"Facturar" => $row['factura_sin_existencia'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Inventario_Existencias']))
{
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	
	$query = "SELECT * FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
	AND subgrupo LIKE '%".$SubGrupo."%'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Codigo[] = $row["cod_fab"];
			$Nombre[$row["cod_fab"]] = $row["nombre"];
			$Existencia[$row["cod_fab"]] = $row["existencia"];
			$Costo[$row["cod_fab"]] = $row["costo"];
			$Ult_Costo[$row["cod_fab"]] = $row["ultimo_costo"];
			$Costo_Prom[$row["cod_fab"]] = $row["costo_promedio"];
			$Venta_Prom[$row["cod_fab"]] = $row["venta_promedio"];
			$Lista1[$row["cod_fab"]] = $row["lista1"];
			$Lista2[$row["cod_fab"]] = $row["lista2"];
			$Lista3[$row["cod_fab"]] = $row["lista3"];
			$Lista4[$row["cod_fab"]] = $row["lista4"];
		}
		
		$Clause = implode("', '", $Codigo);
		$query = "SELECT cod_fab, motivo, MAX(fecha) AS fecha FROM inventario_movs WHERE cod_fab IN ('".$Clause."') GROUP BY cod_fab";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		
		while ($row = mysql_fetch_array($result1))
		{
			$data[] = array(
				"Codigo" => $row["cod_fab"],
				"Nombre" => $Nombre[$row["cod_fab"]],
				"Existencia" => $Existencia[$row["cod_fab"]],
				"Fecha" => $row["fecha"],
				"Tipo" => $row["motivo"],
				"Costo" => $Costo[$row["cod_fab"]],
				"Ult_Costo" => $Ult_Costo[$row["cod_fab"]],
				"Costo_Prom" => $Costo_Prom[$row["cod_fab"]],
				"Venta_Prom" => $Venta_Prom[$row["cod_fab"]],
				"Lista1" => $Lista1[$row["cod_fab"]],
				"Lista2" => $Lista2[$row["cod_fab"]],
				"Lista3" => $Lista3[$row["cod_fab"]],
				"Lista4" => $Lista4[$row["cod_fab"]],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Inventario_Recientes']))
{
	//print_r($_GET);
	//die();
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
	//echo $query;
	//echo "<br/>";
	//echo mysql_num_rows($result);
	//echo "<br/>";
	//die();
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
			
			$data[] = array(
				"Fecha" => $row['fecha'],
				"Codigo" => $row['cod_fab'],
				"Nombre" => $ProductoNombre[$row["cod_fab"]],
				"Cantidad" => $row['cantidad'],
				"Tipo" => $row['tipo'],
				"Motivo" => $row['motivo'],
				"ClienteID" => $NewClienteID,
				"Factura" => $NewFactura,
				"Interno" => $row['interno'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Inventario_Ajustar_Movs']))
{
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
				$data[] = array(
					"Fecha" => $row["fecha"],
					"Codigo" => $row["cod_fab"],
					"Nombre" => $Nombre[$row["cod_fab"]],
					"Cantidad" => $row["cantidad"],
					"Motivo" => $row["observacion"],
				);
			}
			echo json_encode($data);
		}
	}
}
else if (isset($_GET['Inventario_Importar']))
{
	$File = "../Uploads_Tmp/";
	$File .= isset($_GET["FILE"]) ? $_GET["FILE"]:"";
	
	include '../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';
	//$File = '../images/tmp/Productos.xlsx';
	try {
		$objPHPExcel = PHPExcel_IOFactory::load($File);
	}
	catch(Exception $e) {
		die('Error loading file :' . $e->getMessage());
	}
	
	$XlsxData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	
	$query = "SELECT * FROM par_inv_cat";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoCategoria[$row["categoria"]] = $row["categoria"];
		}
	}
	
	$query = "SELECT * FROM par_inv_gr";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoGrupo[$row["grupo"]] = $row["grupo"];
			$ProductoGrupo2[$row["grupo"]] = $row["categoria"];
		}
	}
	
	$query = "SELECT * FROM par_inv_subgr";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoSubGrupo[$row["subgrupo"]] = $row["subgrupo"];
			$ProductoSubGrupo2[$row["subgrupo"]] = $row["grupo"];
		}
	}
	
	$query = "SELECT * FROM par_inv_und";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoUnidad[$row["unidad"]] = $row["unidad"];
			//$ProductoUnidad[strtolower($row["unidad"])] = $row["unidad"]; //Solucion para case insensitive
		}
	}
	
	/*$query = "SELECT cod_fab, categoria, grupo, subgrupo, und_med FROM productos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoCategoria[$row["cod_fab"]] = $row["categoria"];
			$ProductoGrupo[$row["cod_fab"]] = $row["grupo"];
			$ProductoSubGrupo[$row["cod_fab"]] = $row["subgrupo"];
			$ProductoUnidad[$row["cod_fab"]] = $row["und_med"];
		}
	}*/
	
	// 0 nothing - 1 columns name's - 2 real data..
	for ($i = 2; $i < count($XlsxData); $i++)
	{
		$CategoriaTmp = $XlsxData[$i]["C"] == NULL ? "":$XlsxData[$i]["C"];
		$Categoria = isset($ProductoCategoria[$CategoriaTmp]) ? $ProductoCategoria[$CategoriaTmp]:"";
		
		if ($Categoria == "")
		{
			$Grupo = "";
			$SubGrupo = "";
		}
		else
		{
			$GrupoTmp = $XlsxData[$i]["D"] == NULL ? "":$XlsxData[$i]["D"];
			$Grupo = isset($ProductoGrupo[$GrupoTmp]) ? $ProductoGrupo[$GrupoTmp]:"";
			if ($Grupo == "")
				$SubGrupo = "";
			else
			{
				$SubGrupoTmp = $XlsxData[$i]["E"] == NULL ? "":$XlsxData[$i]["E"];
				$SubGrupo = isset($ProductoSubGrupo[$SubGrupoTmp]) ? $ProductoSubGrupo[$SubGrupoTmp]:"";
			}
			
			if (isset($ProductoGrupo2[$Grupo]))
			{
				if ($Categoria != $ProductoGrupo2[$Grupo])
				{
					$Grupo = "";
					$SubGrupo = "";
				}
			}
			
			if (isset($ProductoSubGrupo2[$SubGrupo]))
			{
				if ($Grupo != $ProductoSubGrupo2[$SubGrupo])
				{
					$SubGrupo = "";
				}
			}
			
		}
		
		$UnidadTmp = $XlsxData[$i]["G"] == NULL ? "unidad":$XlsxData[$i]["G"];
		$Unidad = isset($ProductoUnidad[$UnidadTmp]) ? $ProductoUnidad[$UnidadTmp]:"";
		
		//---
		$Costo = $XlsxData[$i]["F"] == NULL ? 0:CleanNumbers($XlsxData[$i]["F"])+"";
		$Costo = round($Costo);
		$Costo = number_format($Costo, 2, '.', '');
		$Peso = $XlsxData[$i]["H"] == NULL ? 0:CleanNumbers($XlsxData[$i]["H"])+"";
		$Peso = number_format($Peso, 2, '.', '');
		$Lista1 = $XlsxData[$i]["I"] == NULL ? 0:CleanNumbers($XlsxData[$i]["I"])+"";
		$Lista1 = round($Lista1);
		$Lista1 = number_format($Lista1, 2, '.', '');
		$Lista2 = $XlsxData[$i]["J"] == NULL ? 0:CleanNumbers($XlsxData[$i]["J"])+"";
		$Lista2 = round($Lista2);
		$Lista2 = number_format($Lista2, 2, '.', '');
		$Lista3 = $XlsxData[$i]["K"] == NULL ? 0:CleanNumbers($XlsxData[$i]["K"])+"";
		$Lista3 = round($Lista3);
		$Lista3 = number_format($Lista3, 2, '.', '');
		$Lista4 = $XlsxData[$i]["L"] == NULL ? 0:CleanNumbers($XlsxData[$i]["L"])+"";
		$Lista4 = round($Lista4);
		$Lista4 = number_format($Lista4, 2, '.', '');
		
		$data[] = array(
			"Codigo" => $XlsxData[$i]["A"] == NULL ? "":$XlsxData[$i]["A"],
			"Nombre" => $XlsxData[$i]["B"] == NULL ? "":$XlsxData[$i]["B"],
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
	} 
	
	/*$data[] = array(
		"Codigo" => "1234",
		"Nombre" => "ASDASDSADSAD",
		"Categoria" => "",
		"Grupo" => "",
		"SubGrupo" => "",
		"Costo" => 0.00,
		"Unidad" => "galon",
		"Peso" => 0.000,
		"Lista1" => 0.00,
		"Lista2" => 0.00,
		"Lista3" => 0.00,
		"Lista4" => 0.00,
	);*/
	
	echo json_encode($data);
}
else if (isset($_GET['Compras_Modificar']))
{
	$ProductoNombre;
	$ProductoUndMed;
	$ProductoPeso;
	
	$query = "SELECT cod_fab, nombre, und_med, peso FROM productos ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ProductoNombre[$row["cod_fab"]] = $row["nombre"];
			$ProductoUndMed[$row["cod_fab"]] = $row["und_med"];
			$ProductoPeso[$row["cod_fab"]] = $row["peso"];
		}
	}
	
	$query = "SELECT codigo, cantidad, ultimo_costo, nuevo_costo, `desc` FROM compras_movs 
	WHERE interno = '".$_GET['Compras_Modificar']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		$check = false;
		$query = "SELECT * FROM compras_final WHERE interno = '".$_GET['Compras_Modificar']."'";
		$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
			$row2 = mysql_fetch_array($result2, MYSQL_ASSOC);
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($check == false)
			{
				$data[] = array(
				'Entrada' => $row2['entrada'],
				'Doc_Transp' => $row2['doc_transp'],
				'Pedido' => $row2['pedido'],
				'Factura' => $row2['factura'],
				'Interno' => $row2['interno'],
				'Fecha' => $row2['fecha_compra'],
				'ClienteID' => $row2['cliente_id'],
				'FormaP' => $row2['forma_pago'],
				'Peso_Bascula' => $row2['peso_bascula'],
				'Peso_Remision' => $row2['peso_remision'],
				'Conductor' => $row2['conductor'],
				'Placa' => $row2['placa'],
				'Observaciones' => $row2['observaciones'],
				'TipoServicio' => $row2['tipo_servicio'],
				'TipoServicioValor' => $row2['tipo_servicio_valor'],
				'TipoDescuento' => $row2['tipo_descuento'],
				'TipoDescuentoValor' => $row2['tipo_descuento_valor'],
				'DigitadorID' => $row2['digitado_por'],
				'AutorizadorID' => $row2['autorizado_por'],
				'AprobadorID' => $row2['aprobado_por'],
				'ModificadorID' => $row2['modificado_por'],
				//--
				'CodFab' => $row["codigo"],
				'Nombre' => $ProductoNombre[$row["codigo"]],
				'UndMed' => $ProductoUndMed[$row["codigo"]],
				'Peso' => $ProductoPeso[$row["codigo"]],
				'Cantidad' => $row['cantidad'],
				'Dcto' => $row['desc'],
				'UltCosto' => $row['ultimo_costo'],
				'Unitario' => $row['nuevo_costo'],
				);
				$check = true;
			}
			else {
				$data[] = array(
					'CodFab' => $row["codigo"],
					'Nombre' => $ProductoNombre[$row["codigo"]],
					'UndMed' => $ProductoUndMed[$row["codigo"]],
					'Peso' => $ProductoPeso[$row["codigo"]],
					'Cantidad' => $row['cantidad'],
					'Dcto' => $row['desc'],
					'UltCosto' => $row['ultimo_costo'],
					'Unitario' => $row['nuevo_costo'],
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
else if (isset($_GET['Produccion_Hierro']))
{
	$query = "SELECT cod_fab, nombre, peso FROM productos WHERE categoria = 'Hierro' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'CodFab' => $row['cod_fab'],
				'Nombre' => $row['nombre'],
				'Peso' => $row['peso'],
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
else if (isset($_GET['Produccion_Despunte']))
{
	$query = "SELECT cod_fab, nombre FROM productos WHERE categoria = 'Hierro' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'CodFab' => $row['cod_fab'],
				'Nombre' => $row['nombre'],
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
else if (isset($_GET["Productos_Para_Figuracion"]))
{
	$query = "SELECT cod_fab, nombre, categoria, grupo, k FROM productos 
	WHERE categoria = 'Hierro' AND grupo = 'Figurado' OR categoria = 'Alambre' AND grupo = 'Alambre' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"Codigo" => $row["cod_fab"],
				"Nombre" => $row["nombre"],
				"Categoria" => $row["categoria"],
				"Grupo" => $row["grupo"],
				"K" => $row["k"],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Productos_Figuracion"]))
{
	$query = "SELECT cod_fab, nombre, peso, k FROM productos WHERE k > 0 ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"Codigo" => $row["cod_fab"],
				"Nombre" => $row["nombre"],
				"K" => $row["k"],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Productos_Mallas"]))
{
	$query = "SELECT cod_fab, nombre, peso, k FROM productos WHERE categoria = 'Hierro' AND grupo LIKE '%malla%' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				"Codigo" => $row["cod_fab"],
				"Nombre" => $row["nombre"],
				"K" => $row["k"],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Figuracion_Cargar"]))
{
	$ClienteNombre = ClientesNombre();
	$ProductInfo = GetProductFullInfo("Productos");
	
	$query = "SELECT * FROM figuracion_cartilla_movs WHERE interno = '".$_GET["Figuracion_Cargar"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Items[] = array(
				"Figura" => $row["figura"],
				"Codigo" => $row["codigo"],
				"Nombre" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Nombre"]:"No Existe!",
				"K" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["K"]:0,
				"Cantidad" => $row["cantidad"],
				"Dimensiones" => json_decode($row["dimensiones"], true),
				"Longitud" => $row["longitud"],
				"Peso" => $row["peso"],
				"Ubicacion" => $row["ubicacion"],
			);
		}
	}
	
	$query = "SELECT * FROM figuracion_cartilla WHERE interno = '".$_GET["Figuracion_Cargar"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$data[] = array(
			"Venta_Interno" => $row["venta_interno"],
			"Orden_Produccion" => $row["orden_produccion"],
			"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
			"ClienteID" => $row["cliente_id"],
			"Obra" => $row["obra"],
			"Peso" => $row["peso"],
			"Total_Fig" => $row["total_fig"],
			"Desperdicio" => $row["desperdicio"],
			"Digitado_Por" => $row["digitado_por"],
			"Fecha_Digitado" => $row["fecha_digitado"],
			"Modificado_Por" => $row["modificado_por"],
			"Fecha_Modificado" => $row["fecha_modificado"],
			"Items" => isset($Items) ? $Items:array()
		);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Figuracion_Visualizar"]))
{
	$ClienteNombre = ClientesNombre();
	$ProductInfo = GetProductFullInfo("Productos");
	
	$query = "SELECT * FROM figuracion_figuras";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Figuras[$row["fig"]] = Array(
				"Imagen" => $row["img"],
				"Estribo" => $row["estribo"] == 1 ? true:false,
				"Circular" => $row["circular"] == 1 ? true:false,
				"Vueltas" => $row["vueltas"] == 1 ? true:false,
			);
		}
	}
	
	$query = "SELECT *, SUM(cantidad) AS nCantidad, SUM(cantidad2) AS nCantidad2, SUM(ROUND((cantidad * peso) * 100) / 100) AS peso, 
	GROUP_CONCAT('[', cantidad, '] ', ubicacion SEPARATOR ',<br/>') AS nUbicacion, COUNT(*) AS c FROM figuracion_cartilla_movs 
	WHERE interno = '".$_GET["Figuracion_Visualizar"]."' GROUP BY codigo, figura, dimensiones";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$LongitudBase = 0;
			$LongitudSum = 0;
			$Longitud = 0;
			$Detalle = "";
			$K = isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["K"]:0;
			$Dimensiones = json_decode($row["dimensiones"], true);
			
			for($i = 0; $i < count($Dimensiones); $i++)
			{
				if ($Figuras[$row["figura"]]["Estribo"] == true)
				{
					$LongitudBase += $Dimensiones[$i]["Dimension"] * 2;
				}
				else if ($Figuras[$row["figura"]]["Vueltas"] == true && count($Dimensiones) - $i == 1)
				{
					$LongitudBase = $LongitudBase * $Dimensiones[$i]["Dimension"];
				}
				else if ($Figuras[$row["figura"]]["Circular"] == true && $Dimensiones[$i]["Punto"] == "A")
				{
					$LongitudBase = PI() * $Dimensiones[$i]["Dimension"];
				}
				else
					$LongitudSum += $Dimensiones[$i]["Dimension"];
				
				//$Longitud += $Item["Dimension"];
				if ($Dimensiones[$i]["Angulo"] > 0)
					$Detalle .= "".$Dimensiones[$i]["Punto"]."=".$Dimensiones[$i]["Dimension"]." ?=".$Dimensiones[$i]["Angulo"].",<br /> ";
				else
					$Detalle .= "".$Dimensiones[$i]["Punto"]."=".$Dimensiones[$i]["Dimension"].",<br /> ";
			}
			$Detalle .= "<br />";
			
			$Longitud = $LongitudBase + $LongitudSum * $row["c"];
			
			if ($row["nCantidad2"] == 0)
				$Estado = "Pendiente";
			else if ($row["nCantidad2"] > 0 && $row["nCantidad2"] < $row["nCantidad"])
				$Estado = "Proceso";
			else
				$Estado = "Finalizado";
			
			$Items[] = array(
				"Figura" => $row["figura"],
				"Imagen" => isset($Figuras[$row["figura"]]["Imagen"]) ? $Figuras[$row["figura"]]["Imagen"]:"",
				"Detalle" => $Detalle,
				"Dimensiones" => $row["dimensiones"],
				"Codigo" => $row["codigo"],
				"Nombre" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Nombre"]:"No Existe!",
				"K" => $K,
				"Cantidad" => $row["nCantidad"],
				"Cantidad2" => $row["nCantidad2"],
				"Longitud" => $Longitud,
				"Peso" => $row["peso"],
				"Ubicacion" => $row["nUbicacion"],
				"Estado" => $Estado,
			);
		}
	}
	
	$query = "SELECT * FROM figuracion_cartilla WHERE interno = '".$_GET["Figuracion_Visualizar"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$data[] = array(
			"Orden_Produccion" => $row["orden_produccion"],
			"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
			"ClienteID" => $row["cliente_id"],
			"Obra" => $row["obra"],
			"Peso" => $row["peso"],
			"Digitado_Por" => $row["digitado_por"],
			"Fecha_Digitado" => $row["fecha_digitado"],
			"Modificado_Por" => $row["modificado_por"],
			"Fecha_Modificado" => $row["fecha_modificado"],
			"Items" => isset($Items) ? $Items:array()
		);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Figuracion_Imprimir"]))
{
	$ClienteNombre = ClientesNombre();
	$ProductInfo = GetProductFullInfo("Productos");
	
	$query = "SELECT * FROM figuracion_figuras";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Figuras[$row["fig"]] = Array(
				"Imagen" => $row["img"],
				"Estribo" => $row["estribo"] == 1 ? true:false,
				"Circular" => $row["circular"] == 1 ? true:false,
				"Vueltas" => $row["vueltas"] == 1 ? true:false,
			);
		}
	}
	
	$query = "SELECT * FROM figuracion_cartilla_movs WHERE interno = '".$_GET["Figuracion_Imprimir"]."' ORDER BY codigo ASC, figura ASC, dimensiones ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$LongitudBase = 0;
			$LongitudSum = 0;
			$Longitud = 0;
			$Detalle = "";
			$K = isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["K"]:0;
			$Dimensiones = json_decode($row["dimensiones"], true);
			
			for($i = 0; $i < count($Dimensiones); $i++)
			{
				if ($Figuras[$row["figura"]]["Estribo"] == true)
				{
					$LongitudBase += $Dimensiones[$i]["Dimension"] * 2;
				}
				else if ($Figuras[$row["figura"]]["Vueltas"] == true && count($Dimensiones) - $i == 1)
				{
					$LongitudBase = $LongitudBase * $Dimensiones[$i]["Dimension"];
				}
				else if ($Figuras[$row["figura"]]["Circular"] == true && $Dimensiones[$i]["Punto"] == "A")
				{
					$LongitudBase = PI() * $Dimensiones[$i]["Dimension"];
				}
				else
					$LongitudSum += $Dimensiones[$i]["Dimension"];
				
				//$Longitud += $Item["Dimension"];
				$Detalle .= "".$Dimensiones[$i]["Punto"]."=".$Dimensiones[$i]["Dimension"].", ";
			}
			
			$Longitud = $LongitudBase + $LongitudSum;
			//$Peso = $Longitud * $K;
			
			$Items[] = array(
				"Figura" => isset($Figuras[$row["figura"]]["Imagen"]) ? $Figuras[$row["figura"]]["Imagen"]:"",
				"Detalle" => $Detalle,
				"Codigo" => $row["codigo"],
				"Nombre" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Nombre"]:"No Existe!",
				"K" => $K,
				"Cantidad" => $row["cantidad"],
				"Longitud" => $Longitud,
				"Peso" => $row["peso"],
				"Ubicacion" => $row["ubicacion"],
			);
		}
	}
	
	$query = "SELECT * FROM figuracion_cartilla WHERE interno = '".$_GET["Figuracion_Imprimir"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$data[] = array(
			"Orden_Produccion" => $row["orden_produccion"],
			"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
			"ClienteID" => $row["cliente_id"],
			"Obra" => $row["obra"],
			"Peso" => $row["peso"],
			"Digitado_Por" => $row["digitado_por"],
			"Fecha_Digitado" => $row["fecha_digitado"],
			"Modificado_Por" => $row["modificado_por"],
			"Fecha_Modificado" => $row["fecha_modificado"],
			"Items" => isset($Items) ? $Items:array()
		);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Figuracion_Imprimir_Hierros"]))
{
	$ClienteNombre = ClientesNombre();
	$ProductInfo = GetProductFullInfo("Productos");
	
	$query = "SELECT * FROM figuracion_figuras";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Figuras[$row["fig"]] = Array(
				"Imagen" => $row["img"],
				"Estribo" => $row["estribo"] == 1 ? true:false,
				"Circular" => $row["circular"] == 1 ? true:false,
				"Vueltas" => $row["vueltas"] == 1 ? true:false,
			);
		}
	}
	
	$query = "SELECT *, SUM(cantidad) AS nCantidad, SUM(cantidad2) AS nCantidad2, SUM(ROUND((cantidad * peso) * 100) / 100) AS peso, 
	GROUP_CONCAT('[', cantidad, '] ', ubicacion SEPARATOR ',<br/>') AS nUbicacion, COUNT(*) AS c FROM figuracion_cartilla_movs 
	WHERE interno = '".$_GET["Figuracion_Imprimir_Hierros"]."' GROUP BY codigo, figura, dimensiones";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$LongitudBase = 0;
			$LongitudSum = 0;
			$Longitud = 0;
			$Detalle = "";
			$K = isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["K"]:0;
			$Dimensiones = json_decode($row["dimensiones"], true);
			
			for($i = 0; $i < count($Dimensiones); $i++)
			{
				if ($Figuras[$row["figura"]]["Estribo"] == true)
				{
					$LongitudBase += $Dimensiones[$i]["Dimension"] * 2;
				}
				else if ($Figuras[$row["figura"]]["Vueltas"] == true && count($Dimensiones) - $i == 1)
				{
					$LongitudBase = $LongitudBase * $Dimensiones[$i]["Dimension"];
				}
				else if ($Figuras[$row["figura"]]["Circular"] == true && $Dimensiones[$i]["Punto"] == "A")
				{
					$LongitudBase = PI() * $Dimensiones[$i]["Dimension"];
				}
				else
					$LongitudSum += $Dimensiones[$i]["Dimension"];
				
				//$Longitud += $Item["Dimension"];
				if ($Dimensiones[$i]["Angulo"] > 0)
					$Detalle .= "".$Dimensiones[$i]["Punto"]."=".$Dimensiones[$i]["Dimension"]." ?=".$Dimensiones[$i]["Angulo"].",<br /> ";
				else
					$Detalle .= "".$Dimensiones[$i]["Punto"]."=".$Dimensiones[$i]["Dimension"].",<br /> ";
			}
			$Detalle .= "<br />";
			
			$Longitud = $LongitudBase + $LongitudSum;
			
			if ($row["nCantidad2"] == 0)
				$Estado = "Pendiente";
			else if ($row["nCantidad2"] > 0 && $row["nCantidad2"] < $row["nCantidad"])
				$Estado = "Proceso";
			else
				$Estado = "Finalizado";
			
			$Items[] = array(
				"Figura" => $row["figura"],
				"Imagen" => isset($Figuras[$row["figura"]]["Imagen"]) ? $Figuras[$row["figura"]]["Imagen"]:"",
				"Detalle" => $Detalle,
				"Dimensiones" => $row["dimensiones"],
				"Codigo" => $row["codigo"],
				"Nombre" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Nombre"]:"No Existe!",
				"K" => $K,
				"Cantidad" => $row["nCantidad"],
				"Cantidad2" => $row["nCantidad2"],
				"Longitud" => $Longitud,
				"Peso" => $row["peso"],
				"Ubicacion" => $row["nUbicacion"],
				"Estado" => $Estado,
			);
		}
	}
	
	$query = "SELECT * FROM figuracion_cartilla WHERE interno = '".$_GET["Figuracion_Imprimir_Hierros"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$query = "SELECT * FROM produccion_final WHERE orden_produccion = '".$row["orden_produccion"]."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row2 = mysql_fetch_array($result);
		}
		
		$data[] = array(
			"Orden_Produccion" => $row["orden_produccion"],
			"Orden_Compra" => isset($row2["orden_compra"]) ? $row2["orden_compra"]:"",
			"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
			"ClienteID" => $row["cliente_id"],
			"Obra" => $row["obra"],
			"Peso" => $row["peso"],
			"Digitado_Por" => $row["digitado_por"],
			"Fecha_Digitado" => $row["fecha_digitado"],
			"Modificado_Por" => $row["modificado_por"],
			"Fecha_Modificado" => $row["fecha_modificado"],
			"Items" => isset($Items) ? $Items:array()
		);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Figuracion_Imprimir_Materiales"]))
{
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM figuracion_cartilla WHERE interno = '".$_GET["Figuracion_Imprimir_Materiales"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$query = "SELECT * FROM produccion_final WHERE orden_produccion = '".$row["orden_produccion"]."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			$row2 = mysql_fetch_array($result);
		}
		
		$data[] = array(
			"Orden_Produccion" => $row["orden_produccion"],
			"Orden_Compra" => isset($row2["orden_compra"]) ? $row2["orden_compra"]:"",
			"Cliente" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
			"ClienteID" => $row["cliente_id"],
			"Obra" => $row["obra"],
			"Peso" => $row["peso"],
			"Desperdicio" => $row["desperdicio"],
			"Digitado_Por" => $row["digitado_por"],
			"Fecha_Digitado" => $row["fecha_digitado"],
			"Modificado_Por" => $row["modificado_por"],
			"Fecha_Modificado" => $row["fecha_modificado"],
			"Hierros" => $row["total_fig"],
		);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Maquinaria_Modificar"]))
{
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	
	$query = "SELECT * FROM maquinaria_final WHERE ord_reparacion = '".$_GET["Maquinaria_Modificar"]."'";
	$query .= ($ClienteID != "") ? " AND proveedor1 = '".$ClienteID."' OR proveedor2 = '".$ClienteID."' OR proveedor3 = '".$ClienteID."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$query = "SELECT * FROM cxp_movs WHERE compra_interno = '".$_GET["Maquinaria_Modificar"]."' ";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1-2: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			while ($row1 = mysql_fetch_array($result1))
			{
				$Caja_Interno = $row1["caja_interno"];
				$Caja_Recibo = $row1["caja_recibo"];
			}
		}
		else
		{
			$Caja_Interno = "";
			$Caja_Recibo = "";
		}
		
		$query = "SELECT * FROM maquinaria_partes WHERE ord_reparacion = '".$_GET["Maquinaria_Modificar"]."' ";
		$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result1) > 0)
		{
			while ($row1 = mysql_fetch_array($result1))
			{
				$Items[] = array(
					"Parte" => $row1["parte"],
					"Problema" => $row1["problema"],
					"Diagnostico" => $row1["diagnostico"],
				);
			}
		}
		else
		{
			$Items[] = array();
		}
		
		if ($row["proveedor1"] != "")
		{
			$query = "SELECT maquinaria_movs.*, repuestos.nombre, repuestos.parte FROM maquinaria_movs LEFT JOIN repuestos ON 
			maquinaria_movs.codigo = repuestos.codigo WHERE maquinaria_movs.ord_reparacion = '".$_GET['Maquinaria_Modificar']."' 
			AND proveedor = '".$row["proveedor1"]."'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result1) > 0)
			{
				$i = 0;
				while ($row1 = mysql_fetch_array($result1))
				{
					if($i == 0)
					{
						$Proveedor1[] = array(
							"ClienteID" => $row["proveedor1"],
							"Factura" => $row["factura1"],
							"Total" => $row["total1"],
							"Codigo" => $row1["codigo"],
							"Repuesto" => $row1["nombre"],
							"Parte" => $row1["parte"],
							"Unitario" => $row1["unitario"],
							"Cantidad" => $row1["cantidad"],
						);
					}
					else
					{
						$Proveedor1[] = array(
							"Codigo" => $row1["codigo"],
							"Repuesto" => $row1["nombre"],
							"Parte" => $row1["parte"],
							"Unitario" => $row1["unitario"],
							"Cantidad" => $row1["cantidad"],
						);
					}
					$i++;
				}
				
				if ($row["proveedor1"] == $ClienteID)
				{
					$Proveedor = $Proveedor1;
				}
			}
			else
			{
				$Proveedor1[] = array();
			}
		}
		else
		{
			$Proveedor1[] = array();
		}
		//---
		if ($row["proveedor2"] != "")
		{
			$query = "SELECT maquinaria_movs.*, repuestos.nombre, repuestos.parte FROM maquinaria_movs LEFT JOIN repuestos ON 
			maquinaria_movs.codigo = repuestos.codigo WHERE maquinaria_movs.ord_reparacion = '".$_GET['Maquinaria_Modificar']."' 
			AND proveedor = '".$row["proveedor2"]."'";
			$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			if (mysql_num_rows($result2) > 0)
			{
				$i = 0;
				while ($row2 = mysql_fetch_array($result2))
				{
					if($i == 0)
					{
						$Proveedor2[] = array(
							"ClienteID" => $row["proveedor2"],
							"Factura" => $row["factura2"],
							"Total" => $row["total2"],
							"Codigo" => $row2["codigo"],
							"Repuesto" => $row2["nombre"],
							"Parte" => $row2["parte"],
							"Unitario" => $row2["unitario"],
							"Cantidad" => $row2["cantidad"],
						);
					}
					else
					{
						$Proveedor2[] = array(
							"Codigo" => $row2["codigo"],
							"Repuesto" => $row2["nombre"],
							"Parte" => $row2["parte"],
							"Unitario" => $row2["unitario"],
							"Cantidad" => $row2["cantidad"],
						);
					}
					$i++;
				}
				if ($row["proveedor2"] == $ClienteID)
				{
					$Proveedor = $Proveedor2;
				}
			}
			else
			{
				$Proveedor2[] = array();
			}
		}
		else
		{
			$Proveedor2[] = array();
		}
		//---
		if ($row["proveedor3"] != "")
		{
			$query = "SELECT maquinaria_movs.*, repuestos.nombre, repuestos.parte FROM maquinaria_movs LEFT JOIN repuestos ON 
			maquinaria_movs.codigo = repuestos.codigo WHERE maquinaria_movs.ord_reparacion = '".$_GET['Maquinaria_Modificar']."' 
			AND proveedor = '".$row["proveedor3"]."'";
			$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
			if (mysql_num_rows($result3) > 0)
			{
				$i = 0;
				while ($row3 = mysql_fetch_array($result3))
				{
					if($i == 0)
					{
						$Proveedor3[] = array(
							"ClienteID" => $row["proveedor3"],
							"Factura" => $row["factura3"],
							"Total" => $row["total3"],
							"Codigo" => $row3["codigo"],
							"Repuesto" => $row3["nombre"],
							"Parte" => $row3["parte"],
							"Unitario" => $row3["unitario"],
							"Cantidad" => $row3["cantidad"],
						);
					}
					else
					{
						$Proveedor3[] = array(
							"Codigo" => $row3["codigo"],
							"Repuesto" => $row3["nombre"],
							"Parte" => $row3["parte"],
							"Unitario" => $row3["unitario"],
							"Cantidad" => $row3["cantidad"],
						);
					}
					$i++;
				}
				if ($row["proveedor3"] == $ClienteID)
				{
					$Proveedor = $Proveedor3;
				}
			}
			else
			{
				$Proveedor3[] = array();
			}
		}
		else
		{
			$Proveedor3[] = array();
		}
		
		if ($ClienteID != "")
		{
			$data[] = array(
				"Estado_Maquina" => $row["estado_maquina"],
				"Operador" => $row["operador"],
				"Mecanico" => $row["mecanico"],
				"Fecha_Ini" => $row["fecha_ini"],
				"Fecha_Fin" => $row["fecha_fin"],
				"Clasificacion" => $row["clasificacion"],
				"Tipo" => $row["tipo"],
				"Motivo" => $row["motivo"],
				"Diagnostico" => $row["diagnostico"],
				"Total" => isset($Proveedor[0]["Total"]) ? $Proveedor[0]["Total"]:0,
				"Descripcion" => $row["causa"],
				"Procedimiento" => $row["procedimiento"],
				"Observaciones" => $row["observaciones"],
				"Caja_Interno" => $Caja_Interno,
				"Caja_Recibo" => $Caja_Recibo,
				"Estado" => $row["estado"],
				"Items" => $Items,
				"Proveedor1" => $Proveedor,
				"Proveedor2" => array(),
				"Proveedor3" => array(),
			);
			//print_r($Proveedor);
		}
		else
		{
			$data[] = array(
				"Estado_Maquina" => $row["estado_maquina"],
				"Operador" => $row["operador"],
				"Mecanico" => $row["mecanico"],
				"Fecha_Ini" => $row["fecha_ini"],
				"Fecha_Fin" => $row["fecha_fin"],
				"Clasificacion" => $row["clasificacion"],
				"Tipo" => $row["tipo"],
				"Motivo" => $row["motivo"],
				"Diagnostico" => $row["diagnostico"],
				"Total" => $row["total"],
				"Descripcion" => $row["causa"],
				"Procedimiento" => $row["procedimiento"],
				"Observaciones" => $row["observaciones"],
				"Caja_Interno" => $Caja_Interno,
				"Caja_Recibo" => $Caja_Recibo,
				"Estado" => $row["estado"],
				"Items" => $Items,
				"Proveedor1" => $Proveedor1,
				"Proveedor2" => $Proveedor2,
				"Proveedor3" => $Proveedor3,
			);
		}
		
	}
	else
	{
		$data[] = array();
	}
	echo json_encode($data);
}
else if (isset($_GET['Datos_Remision']))
{
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	//$Ord_Compra = isset($_GET['Ord_Compra']) ? $_GET['Ord_Compra']:"";
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT cod_fab, nombre, und_med, peso, existencia, cantidad, cantidad_despachada, produccion, factura_sin_existencia, 
	`desc`, precio FROM fact_movs LEFT JOIN productos ON fact_movs.codigo = productos.cod_fab WHERE 
	fact_movs.interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	$query = "SELECT * FROM fact_final WHERE interno = '".$Interno."'";
	$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	$check = false;
	
	if (mysql_num_rows($result) > 0) {
		$row2 = mysql_fetch_array($result2);
		while ($row = mysql_fetch_array($result))
		{
			if ($check == false)
			{
				if ($row2["chofer"] == "000000000-0")
				{
					$Chofer = "Externo";
				}
				else
				{
					$Chofer = isset($ClienteNombre[$row2["chofer"]]) ? $ClienteNombre[$row2["chofer"]]:"No Existe!";
				}
			
				$data[] = array(
					'Interno' => $row2['interno'],
					'Ord_Compra' => $row2['orden_compra'],
					'Remision' => $row2['remision'],
					'Fecha' => $row2['fecha_remision'],
					'Factura' => $row2['factura'],
					'Ord_Produccion' => $row2['orden_produccion'],
					'Cliente' => isset($ClienteNombre[$row2['cliente_id']]) ? $ClienteNombre[$row2['cliente_id']]:"No Existe!",
					'ClienteID' => $row2['cliente_id'],
					'Ruta' => $row2['ruta'],
					'Direccion' => $row2['direccion_entrega'],
					'FormaP' => $row2['forma_pago'],
					'Caja_Interno' => $row2['caja_interno'],
					'Caja_Recibo' => $row2['caja_recibo'],
					'VendedorID' => $row2['vendedor_codigo'],
					'CobradorID' => $row2['cobrador_codigo'],
					'Conductor' => $Chofer,
					'Placa' => $row2['placa'],
					'TipoServicio' => $row2['tipo_servicio'],
					'TipoServicioValor' => $row2['tipo_servicio_valor'],
					'TipoDescuento' => $row2['tipo_descuento'],
					'TipoDescuentoValor' => $row2['tipo_descuento_valor'],
					'Notas' => $row2['notas'],
					'Observaciones' => $row2['observaciones'],
					'Beneficiario' => ($row2['beneficiario_id'] != "") ? isset($ClienteNombre[$row2['beneficiario_id']]) ? $ClienteNombre[$row2['beneficiario_id']]:"No Existe!":"",
					'BeneficiarioID' => $row2['beneficiario_id'],
					'Digitado_Por' => $row2['digitado_por'],
					'Digitado_Fecha' => $row2['fecha_digitado'],
					'Modificado_Por' => $row2['modificado_por'],
					'Modificado_Fecha' => $row2['fecha_modificado'],
					'Aprobado_Por' => $row2['autorizado_por'],
					'Aprobado_Fecha' => $row2['fecha_autorizado'],
					'Despachado_Por' => $row2['aprobado_por'],
					'Despachado_Fecha' => $row2['fecha_aprobado'],
					//--
					'CodFab' => $row['cod_fab'],
					'Nombre' => $row['nombre'],
					'UndMed' => $row['und_med'],
					'Peso' => $row['peso'],
					'Existencia' => $row['existencia'],
					'Cantidad' => $row['cantidad'],
					'Cantidad2' => $row['cantidad_despachada'],
					'Produccion' => $row['produccion'],
					'FacturaExistencia' => $row['factura_sin_existencia'],
					'Dcto' => $row['desc'],
					'Unitario' => $row['precio'],
					'Costo' => $row['costo'],
				);
				$check = true;
			}
			else {
				$data[] = array(
					'CodFab' => $row['cod_fab'],
					'Nombre' => $row['nombre'],
					'UndMed' => $row['und_med'],
					'Peso' => $row['peso'],
					'Existencia' => $row['existencia'],
					'Cantidad' => $row['cantidad'],
					'Cantidad2' => $row['cantidad_despachada'],
					'Produccion' => $row['produccion'],
					'FacturaExistencia' => $row['factura_sin_existencia'],
					'Dcto' => $row['desc'],
					'Unitario' => $row['precio'],
					'Costo' => $row['costo'],
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
else if (isset($_GET['Datos_Producto']))
{
	$query = "SELECT * FROM productos WHERE cod_fab = '".$_GET['Datos_Producto']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$data[] = array(
			'CodFab' => $row['cod_fab'],
			'Nombre' => $row['nombre'],
			'UndMed' => $row['und_med'],
			'Peso' => $row['peso'],
			'Existencia' => $row['existencia'],
			'Costo' => $row['costo'],
		);
		echo json_encode($data);
	}
}
else if (isset($_GET['Imprimir_Inventario_Existencias']))
{
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	//$OrderBy = strtolower($OrderBy);
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	
	switch($OrderBy)
	{
		case "Categoria":
			$query = "SELECT categoria, costo, ultimo_costo, costo_promedio, 
			lista1, lista2, lista3, lista4, existencia FROM productos ORDER BY categoria";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$Tmp = "";
				$TmpUlt_Costo = 0;
				$TmpCosto_Prom = 0;
				$TmpLista1 = 0;
				$TmpLista2 = 0;
				$TmpLista3 = 0;
				$TmpLista4 = 0;
				
				while ($row = mysql_fetch_array($result))
					$TmpData[] = $row;
				
				for($i = 0; $i < count($TmpData); $i++)
				{
					if ($TmpData[$i]["categoria"] != $Tmp)
					{
						if ($Tmp != "")
						{
							$data[] = array(
								"Categoria" => $Tmp,
								"Ult_Costo" => $TmpUlt_Costo,
								"Costo_Prom" => $TmpCosto_Prom,
								"Lista1" => $TmpLista1,
								"Lista2" => $TmpLista2,
								"Lista3" => $TmpLista3,
								"Lista4" => $TmpLista4,
							);
						}
						
						$Tmp = $TmpData[$i]["categoria"];
						$TmpUlt_Costo = $TmpData[$i]["existencia"] * $TmpData[$i]["ultimo_costo"];
						$TmpCosto_Prom = $TmpData[$i]["existencia"] * $TmpData[$i]["costo_promedio"];
						$TmpLista1 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista1"];
						$TmpLista2 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista2"];
						$TmpLista3 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista3"];
						$TmpLista4 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista4"];
					}
					else
					{
						$TmpUlt_Costo += $TmpData[$i]["existencia"] * $TmpData[$i]["ultimo_costo"];
						$TmpCosto_Prom += $TmpData[$i]["existencia"] * $TmpData[$i]["costo_promedio"];
						$TmpLista1 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista1"];
						$TmpLista2 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista2"];
						$TmpLista3 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista3"];
						$TmpLista4 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista4"];
					}
					
					if (count($TmpData) - $i == 1)
					{
						$data[] = array(
							"Categoria" => $Tmp,
							"Ult_Costo" => $TmpUlt_Costo,
							"Costo_Prom" => $TmpCosto_Prom,
							"Lista1" => $TmpLista1,
							"Lista2" => $TmpLista2,
							"Lista3" => $TmpLista3,
							"Lista4" => $TmpLista4,
						);
					}
				}
				echo json_encode($data);
			}
		break;
		case "Grupo":
			$query = "SELECT categoria, grupo, costo,ultimo_costo, costo_promedio, lista1, 
			lista2, lista3, lista4, existencia FROM productos ORDER BY categoria, grupo";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$Tmp = "";
				$TmpCategoria = "";
				$TmpGrupo = "";
				$TmpUlt_Costo = 0;
				$TmpCosto_Prom = 0;
				$TmpLista1 = 0;
				$TmpLista2 = 0;
				$TmpLista3 = 0;
				$TmpLista4 = 0;
				
				while ($row = mysql_fetch_array($result))
					$TmpData[] = $row;
				
				for($i = 0; $i < count($TmpData); $i++)
				{
					if ($TmpData[$i]["categoria"].$TmpData[$i]["grupo"] != $Tmp)
					{
						if ($Tmp != "")
						{
							$data[] = array(
								"Categoria" => $TmpCategoria,
								"Grupo" => $TmpGrupo,
								"Ult_Costo" => $TmpUlt_Costo,
								"Costo_Prom" => $TmpCosto_Prom,
								"Lista1" => $TmpLista1,
								"Lista2" => $TmpLista2,
								"Lista3" => $TmpLista3,
								"Lista4" => $TmpLista4,
							);
						}
						
						$Tmp = $TmpData[$i]["categoria"].$TmpData[$i]["grupo"];
						$TmpCategoria = $TmpData[$i]["categoria"];
						$TmpGrupo = $TmpData[$i]["grupo"];
						$TmpUlt_Costo = $TmpData[$i]["existencia"] * $TmpData[$i]["ultimo_costo"];
						$TmpCosto_Prom = $TmpData[$i]["existencia"] * $TmpData[$i]["costo_promedio"];
						$TmpLista1 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista1"];
						$TmpLista2 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista2"];
						$TmpLista3 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista3"];
						$TmpLista4 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista4"];
					}
					else
					{
						$TmpUlt_Costo += $TmpData[$i]["existencia"] * $TmpData[$i]["ultimo_costo"];
						$TmpCosto_Prom += $TmpData[$i]["existencia"] * $TmpData[$i]["costo_promedio"];
						$TmpLista1 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista1"];
						$TmpLista2 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista2"];
						$TmpLista3 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista3"];
						$TmpLista4 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista4"];
					}
					
					if (count($TmpData) - $i == 1)
					{
						$data[] = array(
							"Categoria" => $TmpCategoria,
							"Grupo" => $TmpGrupo,
							"Ult_Costo" => $TmpUlt_Costo,
							"Costo_Prom" => $TmpCosto_Prom,
							"Lista1" => $TmpLista1,
							"Lista2" => $TmpLista2,
							"Lista3" => $TmpLista3,
							"Lista4" => $TmpLista4,
						);
					}
				}
				echo json_encode($data);
			}
		break;
		case "SubGrupo":
			$query = "SELECT categoria, grupo, subgrupo, costo, ultimo_costo, costo_promedio, lista1, 
			lista2, lista3, lista4, existencia FROM productos ORDER BY categoria, grupo, subgrupo";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				$Tmp = "";
				$TmpCategoria = "";
				$TmpGrupo = "";
				$TmpSubGrupo = "";
				$TmpUlt_Costo = 0;
				$TmpCosto_Prom = 0;
				$TmpLista1 = 0;
				$TmpLista2 = 0;
				$TmpLista3 = 0;
				$TmpLista4 = 0;
				
				while ($row = mysql_fetch_array($result))
					$TmpData[] = $row;
				
				for($i = 0; $i < count($TmpData); $i++)
				{
					if ($TmpData[$i]["categoria"].$TmpData[$i]["grupo"].$TmpData[$i]["subgrupo"] != $Tmp)
					{
						if ($Tmp != "")
						{
							$data[] = array(
								"Categoria" => $TmpCategoria,
								"Grupo" => $TmpGrupo,
								"SubGrupo" => $TmpSubGrupo,
								"Ult_Costo" => $TmpUlt_Costo,
								"Costo_Prom" => $TmpCosto_Prom,
								"Lista1" => $TmpLista1,
								"Lista2" => $TmpLista2,
								"Lista3" => $TmpLista3,
								"Lista4" => $TmpLista4,
							);
						}
						
						$Tmp = $TmpData[$i]["categoria"].$TmpData[$i]["grupo"].$TmpData[$i]["subgrupo"];
						$TmpCategoria = $TmpData[$i]["categoria"];
						$TmpGrupo = $TmpData[$i]["grupo"];
						$TmpSubGrupo = $TmpData[$i]["subgrupo"];
						$TmpUlt_Costo = $TmpData[$i]["existencia"] * $TmpData[$i]["ultimo_costo"];
						$TmpCosto_Prom = $TmpData[$i]["existencia"] * $TmpData[$i]["costo_promedio"];
						$TmpLista1 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista1"];
						$TmpLista2 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista2"];
						$TmpLista3 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista3"];
						$TmpLista4 = $TmpData[$i]["existencia"] * $TmpData[$i]["lista4"];
					}
					else
					{
						$TmpUlt_Costo += $TmpData[$i]["existencia"] * $TmpData[$i]["ultimo_costo"];
						$TmpCosto_Prom += $TmpData[$i]["existencia"] * $TmpData[$i]["costo_promedio"];
						$TmpLista1 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista1"];
						$TmpLista2 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista2"];
						$TmpLista3 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista3"];
						$TmpLista4 += $TmpData[$i]["existencia"] * $TmpData[$i]["lista4"];
					}
					
					if (count($TmpData) - $i == 1)
					{
						$data[] = array(
							"Categoria" => $TmpCategoria,
							"Grupo" => $TmpGrupo,
							"SubGrupo" => $TmpSubGrupo,
							"Ult_Costo" => $TmpUlt_Costo,
							"Costo_Prom" => $TmpCosto_Prom,
							"Lista1" => $TmpLista1,
							"Lista2" => $TmpLista2,
							"Lista3" => $TmpLista3,
							"Lista4" => $TmpLista4,
						);
					}
				}
				echo json_encode($data);
			}
		break;
		default:
			$query = "SELECT * FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
			AND subgrupo LIKE '%".$SubGrupo."%' AND cod_fab != '' ORDER BY categoria";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_array($result))
				{
					$data[] = array(
						"Codigo" => $row["cod_fab"],
						"Nombre" => $row["nombre"],
						"Categoria" => $row["categoria"],
						"Unidad" => $row["und_med"],
						"Costo" => $row["costo"],
						"Ult_Costo" => $row["existencia"] * $row["ultimo_costo"],
						"Costo_Prom" => $row["existencia"] * $row["costo_promedio"],
						"Lista1" => $row["existencia"] * $row["lista1"],
						"Lista2" => $row["existencia"] * $row["lista2"],
						"Lista3" => $row["existencia"] * $row["lista3"],
						"Lista4" => $row["existencia"] * $row["lista4"],
						"Lista1_Unitaria" => $row["lista1"],
						"Lista2_Unitaria" => $row["lista2"],
						"Lista3_Unitaria" => $row["lista3"],
						"Lista4_Unitaria" => $row["lista4"],
						"Existencia" => $row["existencia"],
					);
				}
				echo json_encode($data);
			}
		break;
	}
}
else if (isset($_GET['Reportes_Ventas_Mov']))
{
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
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
	BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND cod_fab IN ('".$Clause1."') AND interno LIKE 'FER%' ORDER BY interno";
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
			/*$Ult_Costo[] = array(
				"Interno" => $row["interno"];
				"Codigo" => $row["cod_fab"];
				"Ult_Costo" => $row["viejo_costo"];
			);
			
			$Costo_Prom[] = array(
				"Interno" => $row["interno"];
				"Codigo" => $row["cod_fab"];
				"Costo_Prom" => $row["costo_promedio"];
			);*/
		}
	}
	
	$Clause2 = implode("', '", $Internos1);
	
	$query = "SELECT * FROM fact_final WHERE interno IN ('".$Clause2."')";
	//$query = "SELECT * FROM fact_final WHERE DATE(fecha_remision) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."'";
	$query .= (isset($_GET['Estado']) && $_GET['Estado'] != "") ? " AND estado = '".$_GET['Estado']."'":"";
	$query .= (isset($_GET['ClienteID']) && $_GET['ClienteID'] != "") ? " AND cliente_id = '".$_GET['ClienteID']."'":"";
	$query .= (isset($_GET['Ord_Compra']) && $_GET['Ord_Compra'] != "") ? " AND orden_compra LIKE '%".$_GET['Ord_Compra']."%'":"";
	$query .= (isset($_GET['Factura']) && $_GET['Factura'] != "") ? " AND factura LIKE '%".$_GET['Factura']."%'":"";
	$query .= (isset($_GET['VendedorID']) && $_GET['VendedorID'] != "") ? " AND vendedor_codigo = '".$_GET['VendedorID']."'":"";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	//echo $query;
	//die ();
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Internos2[] = $row["interno"];
			$Vendedor[$row["interno"]] = $row["vendedor_codigo"];
			//$Fecha[$row["interno"]] = $row["fecha_remision"];
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
			
			//if (!isset($Fecha[$row["codigo"].$row["interno"]]))
			//	continue;
			
			$Cantidad = $row["cantidad"] - $row["cantidad_despachada"];
			
			//$CostoU = $Ult_Costo[$row["codigo"].$row["interno"]] * $Cantidad;
			//$CostoP = $Costo_Prom[$row["codigo"].$row["interno"]] * $Cantidad;
			
			$CostoU = isset($Ult_Costo[$row["codigo"].$row["interno"]]) ?
			$Ult_Costo[$row["codigo"].$row["interno"]] * $Cantidad:0;
			
			$CostoP = isset($Costo_Prom[$row["codigo"].$row["interno"]]) ?
			$Costo_Prom[$row["codigo"].$row["interno"]] * $Cantidad:0;
			
			/*$CostoU = isset($Ult_Costo[$row["codigo"].$row["interno"]]) ?
			$Ult_Costo[$row["codigo"].$row["interno"]] * $Cantidad:
			$ProductoUlt_Costo[$row["codigo"]] * $Cantidad;
			
			$CostoP = isset($Costo_Prom[$row["codigo"].$row["interno"]]) ?
			$Costo_Prom[$row["codigo"].$row["interno"]] * $Cantidad:
			$ProductoCosto_Prom[$row["codigo"]] * $Cantidad;*/
			
			if ($OrderBy == "Cliente")
			{
				$data[] = array(
					"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
					"Fecha" => isset($Fecha[$row["codigo"].$row["interno"]]) ? "".$Fecha[$row["codigo"].$row["interno"]]."":"1999-12-31 23:59:59",
					//"Fecha" => isset($Fecha[$row["interno"]]) ? "".$Fecha[$row["interno"]]." 00:00:00":"1999-12-31 23:59:59",
					"Interno" => $row["interno"],
					"Factura" => $row["factura"],
					"Ord_Compra" => $row["orden_compra"],
					"Remision" => $row["remision"],
					"ClienteID" => $row["cliente_id"],
					"VendedorID" => isset($Vendedor[$row["interno"]]) ? $Vendedor[$row["interno"]]:"",
					"Categoria" => isset($ProductoCategoria[$row["codigo"]]) ? $ProductoCategoria[$row["codigo"]]:"No Existe!",
					"Grupo" => isset($ProductoGrupo[$row["codigo"]]) ? $ProductoGrupo[$row["codigo"]]:"No Existe!",
					"SubGrupo" => isset($ProductoSubGrupo[$row["codigo"]]) ? $ProductoSubGrupo[$row["codigo"]]:"No Existe!",
					"Producto" => isset($ProductoNombre[$row["codigo"]]) ? $ProductoNombre[$row["codigo"]]:"No Existe!",
					"Cantidad" => $Cantidad,
					"Unitario" => $row["precio"],
					"Valor" => $row["precio"] * $Cantidad,
					"Ult_Costo" => $CostoU,
					"Costo_Prom" => $CostoP,
				);
			}
			else
			{
				$data[] = array(
					"Fecha" => isset($Fecha[$row["codigo"].$row["interno"]]) ? "".$Fecha[$row["codigo"].$row["interno"]]."":"1999-12-31 23:59:59",
					//"Fecha" => isset($Fecha[$row["interno"]]) ? "".$Fecha[$row["interno"]]." 00:00:00":"1999-12-31 23:59:59",
					"Interno" => $row["interno"],
					"Factura" => $row["factura"],
					"Ord_Compra" => $row["orden_compra"],
					"Remision" => $row["remision"],
					"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
					"ClienteID" => $row["cliente_id"],
					"VendedorID" => isset($Vendedor[$row["interno"]]) ? $Vendedor[$row["interno"]]:"",
					"Categoria" => isset($ProductoCategoria[$row["codigo"]]) ? $ProductoCategoria[$row["codigo"]]:"No Existe!",
					"Grupo" => isset($ProductoGrupo[$row["codigo"]]) ? $ProductoGrupo[$row["codigo"]]:"No Existe!",
					"SubGrupo" => isset($ProductoSubGrupo[$row["codigo"]]) ? $ProductoSubGrupo[$row["codigo"]]:"No Existe!",
					"Producto" => isset($ProductoNombre[$row["codigo"]]) ? $ProductoNombre[$row["codigo"]]:"No Existe!",
					"Cantidad" => $Cantidad,
					"Unitario" => $row["precio"],
					"Valor" => $row["precio"] * $Cantidad,
					"Ult_Costo" => $CostoU,
					"Costo_Prom" => $CostoP,
				);
			}
		}
	}
	else
	{
		die();
	}
	sort($data);
	echo json_encode($data);
}
else if (isset($_GET['Reportes_Ventas_Mov_Productos']))
{
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
	
	$query = "SELECT * FROM fact_movs WHERE codigo IN ('".$Clause1."') AND interno IN ('".$Clause3."') ORDER BY codigo";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			if ($row["cantidad"] < 1)
				continue;
			
			$Cantidad = $row["cantidad"] - $row["cantidad_despachada"];
			$Costo = $row["precio"] * $Cantidad;
			$CostoU = isset($Ult_Costo[$row["codigo"].$row["interno"]]) ?
			$Ult_Costo[$row["codigo"].$row["interno"]] * $Cantidad:0;
			
			if ($Costo > 0 && $CostoU > 0)
				$Utilidad = ((($Costo / $CostoU) - 1) * 100);
			else
				$Utilidad = 0;
			
			$data[] = array(
				"Producto" => isset($ProductoNombre[$row["codigo"]]) ? $ProductoNombre[$row["codigo"]]:"No Existe!",
				//"Fecha" => isset($Fecha[$row["interno"]]) ? "".$Fecha[$row["interno"]]." 00:00:00":"1999-12-31 23:59:59",
				"Fecha" => isset($Fecha[$row["codigo"].$row["interno"]]) ? "".$Fecha[$row["codigo"].$row["interno"]]." 00:00:00":"1999-12-31 23:59:59",
				"Cantidad" => $Cantidad,
				//"Interno" => $row["interno"],
				"Ord_Compra" => $row["orden_compra"],
				"Factura" => $row["factura"],
				"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
				//"ClienteID" => $row["cliente_id"],
				"VendedorID" => isset($Vendedor[$row["interno"]]) ? $Vendedor[$row["interno"]]:"",
				"Costo" => $Costo,
				"CostoU" => $CostoU,
				"Utilidad" => $Utilidad,
			);
		}
	}
	else
	{
		die();
	}
	sort($data);
	echo json_encode($data);
}
else if (isset($_GET['Reportes_Inventario']))
{
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	
	if ($Codigo == "")
	{
		$query = "SELECT * FROM productos WHERE categoria LIKE '%".$Categoria."%' AND grupo LIKE '%".$Grupo."%' 
		AND subgrupo LIKE '%".$SubGrupo."%'";
	}
	else
		$query = "SELECT * FROM productos WHERE cod_fab = '".$Codigo."'";
		
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				//"Codigo" => $row["cod_fab"],
				"Nombre" => $row["nombre"],
				"Categoria" => $row["categoria"],
				"Grupo" => $row["grupo"],
				"SubGrupo" => $row["subgrupo"],
				"Peso" => $row["peso"],
				"UndMed" => $row["und_med"],
				"Existencia" => $row["existencia"],
				"Ult_Costo" => $row["ultimo_costo"],
				"Total_Ult_Costo" => $row["ultimo_costo"] * $row["existencia"],
				"Costo_Prom" => $row["costo_promedio"],
				"Total_Costo_Prom" => $row["costo_promedio"] * $row["existencia"],
			);
		}
		//array_multisort("Nombre", SORT_ASC, $data);
		sort($data);
		echo json_encode($data);
	}
}
else if (isset($_GET['Reportes_Kardex']))
{
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	
	if ($Codigo == "")
	{
		$data[] = array();
		echo json_encode($data);
		die();
	}
	
	/*$query = "SELECT * FROM productos WHERE cod_fab = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Existencia[$row["cod_fab"]] = $row["existencia"];
		}
	}*/
	
	$query = "SELECT * FROM inventario_movs WHERE DATE(fecha) BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' 
	AND cod_fab = '".$Codigo."' AND motivo IN ('Factura', 'Inicial') ORDER BY fecha"; //AND estado IN ('Aprobado', '') 
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$Costo = 0;
		$Viejo_Costo = 0;
		$Utilidad = 0;
		$Porcentaje = 0;
		$Inicial = 0;
		$i = 0;
		while($row = mysql_fetch_array($result))
		{
			if ($i == 0)
				$Inicial = $row["cantidad"];
			
			$Costo += $row["costo"];
			$Viejo_Costo += $row["viejo_costo"];
			
			if ($row["motivo"] == "Inicial")
				$Inicial = $row["cantidad"];
			
			$i++;
		}
		
		if ($Viejo_Costo < 1) {
			$Utilidad = 0;
			$Porcentaje = 0;
		}
		else {
			$Utilidad = $Costo - $Viejo_Costo;
			$Porcentaje = ((($Costo / $Viejo_Costo) - 1) * 100);
		}
		
		/*$ItemData[] = array(
			"Costo" => $Costo,
			"Viejo_Costo" => $Viejo_Costo,
			"Utilidad" => $Utilidad,
			"Porcentaje" => $Porcentaje,
			"Existencia" => isset($Existencia[$Codigo]) ? $Existencia[$Codigo]:0,
			"Inicial" => isset($Inicial) ? $Inicial:0,
		);*/
		$ItemData[] = array(
			"Costo" => $Costo,
			"Viejo_Costo" => $Viejo_Costo,
			"Utilidad" => $Utilidad,
			"Porcentaje" => $Porcentaje,
		);
		echo json_encode($ItemData);
	}
	else
	{
		$data[] = array();
		echo json_encode($data);
	}
}
else if (isset($_GET['Reportes_Kardex_Existencias']))
{
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	if ($Codigo == "")
	{
		$data[] = array();
		echo json_encode($data);
		die();
	}
	
	$Existencia = 0;
	$Inicial = 0;
	
	$query = "SELECT * FROM productos WHERE cod_fab = '".$Codigo."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$Existencia = $row["existencia"];
	}
	
	$query = "SELECT * FROM inventario_movs WHERE cod_fab = '".$Codigo."' AND motivo = 'Inicial'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		$Inicial = $row["cantidad"];
	}
	
	$ItemData[] = array(
		"Existencia" => $Existencia,
		"Inicial" => $Inicial,
	);
	echo json_encode($ItemData);
}
else if (isset($_GET['Reportes_Kardex_Detalle']))
{
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Motivo = isset($_GET['Motivo']) ? $_GET['Motivo']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	$Fecha_Fin = date("Y-m-d", strtotime($Fecha_Fin . "+1 day"));// Fix Today's Problem...
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	
	if ($Codigo == "")
	{
		$data[] = array();
		echo json_encode($data);
		die();
	}
	
	if ($ClienteID != "")
		$query = "SELECT cliente_id, interno, factura, remision FROM fact_final WHERE cliente_id = '".$ClienteID."' AND estado = 'Aprobado'";
	else
		$query = "SELECT cliente_id, interno, factura, remision FROM fact_final WHERE estado = 'Aprobado'";
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
	
	if ($ClienteID != "")
		$query = "SELECT factura, interno, cliente_id FROM compras_final WHERE cliente_id = '".$ClienteID."' AND estado = 'Aprobado'";
	else
		$query = "SELECT factura, interno, cliente_id FROM compras_final WHERE estado = 'Aprobado'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$ID[$row["interno"]] = $row["cliente_id"];
			$Factura[$row["interno"]] = $row["factura"];
		}
	}
	
	if ($ClienteID != "")
		$query = "SELECT orden_produccion, cliente_id FROM produccion_final WHERE cliente_id = '".$ClienteID."' AND estado = 'Aprobado'";
	else
		$query = "SELECT orden_produccion, cliente_id FROM produccion_final WHERE estado = 'Aprobado'";
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
			if ($OrderBy == "")
			{
				$Existencia = $Saldo;
				if ($row["tipo"] == "Entrada" && $row["motivo"] != "Inicial")
					$Saldo += $row["cantidad"];
				else if ($row["tipo"] == "Salida" && $row["motivo"] != "Inicial")
					$Saldo -= $row["cantidad"];
				else
				{
					//$Saldo = $row["existencia"];
					$GridData[] = array(
						"Fecha" => $row["fecha"],
						"Cliente" => $MAIN_ID,
						"Tipo" => $row["tipo"],
						"Observaciones" => "",
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
				
				//--- Mostrar Ajustes
				if (!isset($ID[$row["interno"]]) && $ClienteID != "")
					continue;
				
				$GridData[] = array(
					"Fecha" => $row["fecha"],
					"Cliente" => isset($ID[$row["interno"]]) ? $ID[$row["interno"]]:"",
					"Tipo" => $row["tipo"],
					"Observaciones" => $row["observacion"],
					"Motivo" => $row["motivo"],
					"Cantidad" => $row["cantidad"],
					"Saldo" => $Saldo,
					"Existencia" => $Existencia,
					"Interno" => $row["interno"],
					"Factura" => isset($Factura[$row["interno"]]) ? $Factura[$row["interno"]]:"",
					"Remision" => isset($Remision[$row["interno"]]) ? $Remision[$row["interno"]]:"",
				);
			}
			else
			{
				//--- Client Filter
				if (!isset($ID[$row["interno"]]) && $ClienteID != "")
					continue;
				
				$Costo = $row["cantidad"] * $row["costo"];
				$Viejo_Costo = $row["cantidad"] * $row["viejo_costo"];
				
				if ($row["viejo_costo"] < 1)
					$Porcentaje = 0;
				else
					$Porcentaje = ((($Costo / $Viejo_Costo) - 1) * 100);
			
				$GridData[] = array(
					"Cliente" => isset($ID[$row["interno"]]) ? $ID[$row["interno"]]:"",
					"Motivo" => $row["motivo"],
					"Producto" => $Codigo,
					"Fecha" => $row["fecha"],
					"Factura" => isset($Factura[$row["interno"]]) ? $Factura[$row["interno"]]:"",
					"Cantidad" => $row["cantidad"],
					//"Peso" => "",
					"Facturado" => $Costo,
					"Costo" => $Viejo_Costo,
					"Utilidad" => $Porcentaje,
				);
			}
		}
		
		foreach($GridData AS $Item)
		{
			if ($Motivo != "")
			{
				if ($Item["Fecha"] >= $Fecha_Ini && $Item["Fecha"] <= $Fecha_Fin && $Item["Motivo"] == $Motivo)
					$NewGridData[] = $Item;
			}
			else
			{
				if ($Item["Fecha"] >= $Fecha_Ini && $Item["Fecha"] <= $Fecha_Fin)
					$NewGridData[] = $Item;
			}
		}
		
		if (isset($NewGridData))
		{
			if ($OrderBy != "")
				sort($NewGridData);
			else
				rsort($NewGridData);
			
			echo json_encode($NewGridData);
		}
		else
			echo json_encode(array());
	}
	else
		echo json_encode(array());
}
else if (isset($_GET['Reportes_Compras_Mov']))
{
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
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
	BETWEEN '".$Fecha_Ini."' AND '".$Fecha_Fin."' AND interno LIKE '%COMP%' AND cod_fab IN ('".$Clause1."') ORDER BY interno";
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
	$query .= (isset($_GET['Interno']) && $_GET['Interno'] != "") ? " AND interno LIKE '%".$_GET['Ord_Compra']."%'":"";
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
			
			//if (!isset($Fecha[$row["codigo"].$row["interno"]]))
			//	continue;
			
			$Cantidad = $row["cantidad"];
			$CostoU = isset($ProductoCosto[$row["codigo"]]) ? $ProductoCosto[$row["codigo"]]:0;
			$CostoP = isset($ProductoCosto_Prom[$row["codigo"]]) ? $ProductoCosto_Prom[$row["codigo"]]:0;
			
			if ($OrderBy == "Proveedor")
			{
				$data[] = array(
					"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
					"Fecha" => isset($Fecha[$row["codigo"].$row["interno"]]) ? "".$Fecha[$row["codigo"].$row["interno"]]."":"1999-12-31 23:59:59",
					"Interno" => $row["interno"],
					"Factura" => $row["factura"],
					"Entrada" => $row["entrada"],
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
			else if ($OrderBy == "Producto")
			{
				$data[] = array(
					"Producto" => isset($ProductoNombre[$row["codigo"]]) ? $ProductoNombre[$row["codigo"]]:"No Existe!",
					"Fecha" => isset($Fecha[$row["codigo"].$row["interno"]]) ? "".$Fecha[$row["codigo"].$row["interno"]]."":"1999-12-31 23:59:59",
					"Codigo" => $row["codigo"],
					"Categoria" => isset($ProductoCategoria[$row["codigo"]]) ? $ProductoCategoria[$row["codigo"]]:"No Existe!",
					"Grupo" => isset($ProductoGrupo[$row["codigo"]]) ? $ProductoGrupo[$row["codigo"]]:"No Existe!",
					"SubGrupo" => isset($ProductoSubGrupo[$row["codigo"]]) ? $ProductoSubGrupo[$row["codigo"]]:"No Existe!",
					"Interno" => $row["interno"],
					"Factura" => $row["factura"],
					"Entrada" => $row["entrada"],
					"Nombre" => isset($ClienteNombre[$row["cliente_id"]]) ? $ClienteNombre[$row["cliente_id"]]:"No Existe!",
					"ClienteID" => $row["cliente_id"],
					"Cantidad" => $Cantidad,
					"Unitario" => $row["nuevo_costo"],
					"Valor" => $row["nuevo_costo"] * $Cantidad,
					"Ult_Costo" => $CostoU,
					"Costo_Prom" => $CostoP,
				);
			}
			else
			{
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
	}
	else
	{
		die();
	}
	sort($data);
	echo json_encode($data);
}
else if (isset($_GET['Presupuesto']))
{
	$UserCode = "";
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data_session = $_SESSION["UserAccess"];
		$num = count($data_session);
		
		if ($data_session[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data_session[$i]["Modulo"] == "Presupuesto" && $data_session[$i]["SubModulo"] == "Presupuesto" && $data_session[$i]["Supervisor"] == "false")
				{
					$UserCode = $_SESSION["UserCode"];
				}
			}
		}
	}
	
	if ($UserCode == "")
		$query = "SELECT * FROM presupuesto ORDER BY interno ASC";
	else
		$query = "SELECT * FROM presupuesto WHERE digitado_por = '".$UserCode."' ORDER BY interno ASC";
	
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Interno' => $row['interno'],
				'Proyecto' => $row['proyecto'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Presupuesto_Cargar']))
{	
	$query = "SELECT * FROM presupuesto WHERE interno = '".$_GET['Presupuesto_Cargar']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$query = "SELECT DISTINCT clasificacion FROM presupuesto_movs WHERE interno = '".$_GET['Presupuesto_Cargar']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				if ($row1["clasificacion"] == "Titulo")
					continue;
				
				$query = "SELECT * FROM `apu_".$row1["clasificacion"]."` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while($row2 = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$ProductoNombre[$row2["codigo"].$row1["clasificacion"]] = $row2["nombre"];
						$ProductoUnidad[$row2["codigo"].$row1["clasificacion"]] = $row2["unidad"];
					}
				}
			}
		}
		
		$query = "SELECT * FROM presupuesto_movs WHERE interno = '".$_GET['Presupuesto_Cargar']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				if ($row1["clasificacion"] == "Titulo")
				{
					$Items[] = array(
						'Item' => $row1['item'],
						'Codigo' => $row1['codigo'],
						'Nombre' => $row1["nombre"],
						'Unidad' => "",
						'Cantidad' => $row1['cantidad'],
						'Valor' => $row1['valor'],
						'Clasificacion' => $row1['clasificacion'],
					);

				}
				else
				{
					$Items[] = array(
						'Item' => $row1['item'],
						'Codigo' => $row1['codigo'],
						'Nombre' => $row1["nombre"],
						'Unidad' => $ProductoUnidad[$row1["codigo"].$row1["clasificacion"]],
						'Cantidad' => $row1['cantidad'],
						'Valor' => $row1['valor'],
						'Clasificacion' => $row1['clasificacion'],
					);
				}
			}
		}
		
		if(isset($Items))
			sort($Items);
		
		$data[] = array(
			'ID' => $row['id'],
			'Fecha' => $row['fecha'],
			'Proyecto' => $row['proyecto'],
			'ClienteID' => $row['cliente_id'],
			'SubTotal' => $row['subtotal'],
			'Administracion1' => $row['administracion1'],
			'Administracion2' => $row['administracion2'],
			'Imprevistos1' => $row['imprevistos1'],
			'Imprevistos2' => $row['imprevistos2'],
			'Utilidades1' => $row['utilidades1'],
			'Utilidades2' => $row['utilidades2'],
			'Iva1' => $row['iva1'],
			'Iva1_Check' => $row['iva1_check'],
			'Iva2' => $row['iva2'],
			'Iva2_Check' => $row['iva2_check'],
			'Total' => $row['total'],
			'Notas' => $row['notas'],
			'Items' => isset($Items) ? $Items:array(),
		);
		
		echo json_encode($data);
	}
}
else if (isset($_GET["Presupuesto_Imprimir_Detalles"]))
{	
	$query = "SELECT * FROM presupuesto WHERE interno = '".$_GET['Presupuesto_Imprimir_Detalles']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$query = "SELECT DISTINCT clasificacion FROM presupuesto_movs WHERE interno = '".$_GET['Presupuesto_Imprimir_Detalles']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				if ($row1["clasificacion"] == "Titulo")
					continue;
				
				$query = "SELECT * FROM `apu_".$row1["clasificacion"]."` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while($row2 = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$ProductoNombre[$row2["codigo"].$row1["clasificacion"]] = $row2["nombre"];
						$ProductoUnidad[$row2["codigo"].$row1["clasificacion"]] = $row2["unidad"];
					}
				}
			}
		}
		
		$query = "SELECT * FROM presupuesto_movs WHERE interno = '".$_GET["Presupuesto_Imprimir_Detalles"]."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				if ($row1["clasificacion"] != "Titulo")
				{
					$query = "SELECT *, SUM(total) as TOTAL FROM `apu_".$row1["clasificacion"]."_movs` 
					WHERE codigo = '".$row1["codigo"]."' GROUP BY tipo ORDER BY codigo";
					$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
					if (mysql_num_rows($result1) > 0)
					{
						while($row2 = mysql_fetch_array($result1, MYSQL_ASSOC))
						{
							switch ($row2["tipo"])
							{
								case "APU":
								case "Materiales":
									if (!isset($Materiales[$row2["codigo"].$row1["clasificacion"]]))
										$Materiales[$row2["codigo"].$row1["clasificacion"]] = $row2["TOTAL"];
									else
										$Materiales[$row2["codigo"].$row1["clasificacion"]] += $row2["TOTAL"];
								break;
								
								case "Mano de Obra":
									$ManodeObra[$row2["codigo"].$row1["clasificacion"]] = $row2["TOTAL"];
								break;
								
								case "Equipos":
									$Equipos[$row2["codigo"].$row1["clasificacion"]] = $row2["TOTAL"];
								break;
							}
						}
					}
				}
				
				if ($row1["clasificacion"] == "Titulo")
				{
					$Items[] = array(
						"Item" => $row1["item"],
						"Codigo" => $row1["codigo"],
						"Nombre" => $row1["nombre"],
						"Unidad" => "",
						"Cantidad" => $row1["cantidad"],
						"Valor" => $row1["valor"],
						"Materiales" => 0,
						"ManodeObra" => 0,
						"Equipos" => 0,
						"Clasificacion" => $row1["clasificacion"],
					);

				}
				else
				{
					$Items[] = array(
						"Item" => $row1["item"],
						"Codigo" => $row1["codigo"],
						"Nombre" => $row1["nombre"],
						"Unidad" => $ProductoUnidad[$row1["codigo"].$row1["clasificacion"]],
						"Cantidad" => $row1["cantidad"],
						"Valor" => $row1["valor"],
						"Materiales" => isset($Materiales[$row1["codigo"].$row1["clasificacion"]]) ? $Materiales[$row1["codigo"].$row1["clasificacion"]]:0,
						"ManodeObra" => isset($ManodeObra[$row1["codigo"].$row1["clasificacion"]]) ? $ManodeObra[$row1["codigo"].$row1["clasificacion"]]:0,
						"Equipos" => isset($Equipos[$row1["codigo"].$row1["clasificacion"]]) ? $Equipos[$row1["codigo"].$row1["clasificacion"]]:0,
						"Clasificacion" => $row1["clasificacion"],
					);
				}
			}
		}
		
		if(isset($Items))
			sort($Items);
		
		$data[] = array(
			'ID' => $row['id'],
			'Fecha' => $row['fecha'],
			'Proyecto' => $row['proyecto'],
			'ClienteID' => $row['cliente_id'],
			'SubTotal' => $row['subtotal'],
			'Administracion1' => $row['administracion1'],
			'Administracion2' => $row['administracion2'],
			'Imprevistos1' => $row['imprevistos1'],
			'Imprevistos2' => $row['imprevistos2'],
			'Utilidades1' => $row['utilidades1'],
			'Utilidades2' => $row['utilidades2'],
			'Iva1' => $row['iva1'],
			'Iva1_Check' => $row['iva1_check'],
			'Iva2' => $row['iva2'],
			'Iva2_Check' => $row['iva2_check'],
			'Total' => $row['total'],
			'Notas' => $row['notas'],
			'Items' => $Items,
		);
		
		echo json_encode($data);
	}
}
else if (isset($_GET["Presupuesto_Imprimir_Detalles_APU"]))
{
	$query = "SELECT * FROM presupuesto_movs WHERE interno = '".$_GET["Presupuesto_Imprimir_Detalles_APU"]."' 
	AND clasificacion != 'Titulo' ORDER BY item, codigo";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$query = "SELECT * FROM `apu_".$row["clasificacion"]."` WHERE codigo = '".$row["codigo"]."'";
			$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
			if (mysql_num_rows($result1) > 0)
			{
				//while($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
				$row1 = mysql_fetch_array($result1, MYSQL_ASSOC);
				$Total_Apu = 0;
				$Total_Mat = 0;
				$Total_Mo = 0;
				$Total_Equ = 0;
				
				$query = "SELECT DISTINCT clasificacion FROM `apu_".$row["clasificacion"]."_movs` WHERE codigo = '".$row["codigo"]."'";
				$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-1: ".mysql_error():"");
				if (mysql_num_rows($result2) > 0)
				{
					while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
					{
						$query = "SELECT * FROM `apu_equ_".$row2["clasificacion"]."` ";
						$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-2: ".mysql_error():"");
						if (mysql_num_rows($result3) > 0)
						{
							while($row3 = mysql_fetch_array($result3, MYSQL_ASSOC))
							{
								$ProductoNombre["Equipos".$row3["codigo"].$row2["clasificacion"]] = $row3["nombre"];
								$ProductoUnidad["Equipos".$row3["codigo"].$row2["clasificacion"]] = $row3["unidad"];
							}
						}
						
						$query = "SELECT * FROM `apu_mo_".$row2["clasificacion"]."` ";
						$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-3: ".mysql_error():"");
						if (mysql_num_rows($result3) > 0)
						{
							while($row3 = mysql_fetch_array($result3, MYSQL_ASSOC))
							{
								$ProductoNombre["Mano de Obra".$row3["codigo"].$row2["clasificacion"]] = $row3["nombre"];
								$ProductoUnidad["Mano de Obra".$row3["codigo"].$row2["clasificacion"]] = $row3["unidad"];
							}
						}
						
						$query = "SELECT * FROM `apu_mat_".$row2["clasificacion"]."` ";
						$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-4: ".mysql_error():"");
						if (mysql_num_rows($result3) > 0)
						{
							while($row3 = mysql_fetch_array($result3, MYSQL_ASSOC))
							{
								$ProductoNombre["Materiales".$row3["codigo"].$row2["clasificacion"]] = $row3["nombre"];
								$ProductoUnidad["Materiales".$row3["codigo"].$row2["clasificacion"]] = $row3["unidad"];
							}
						}
						
						$query = "SELECT * FROM `apu_".$row2["clasificacion"]."` ";
						$result3 = mysql_query($query) or die($DEBUG ? "SQL ERROR #3-5: ".mysql_error():"");
						if (mysql_num_rows($result3) > 0)
						{
							while($row3 = mysql_fetch_array($result3, MYSQL_ASSOC))
							{
								$ProductoNombre["APU".$row3["codigo"].$row2["clasificacion"]] = $row3["nombre"];
								$ProductoUnidad["APU".$row3["codigo"].$row2["clasificacion"]] = $row3["unidad"];
							}
						}
					}
				}
				
				$query = "SELECT * FROM `apu_".$row["clasificacion"]."_movs` WHERE codigo = '".$row["codigo"]."' ORDER BY tipo DESC";
				$result2 = mysql_query($query) or die($DEBUG ? "SQL ERROR #4: ".mysql_error():"");
				if (mysql_num_rows($result2) > 0)
				{
					$APU_Items = Array();
					while($row2 = mysql_fetch_array($result2, MYSQL_ASSOC))
					{
						if ($row2["tipo"] == "APU")
							$Total_Apu += $row2["total"];
						if ($row2["tipo"] == "Materiales")
							$Total_Mat += $row2["total"];
						else if ($row2["tipo"] == "Mano de Obra")
							$Total_Mo += $row2["total"];
						else if ($row2["tipo"] == "Equipos")
							$Total_Equ += $row2["total"];
						
						$APU_Items[] = Array(
							"Codigo" => $row2["objeto_codigo"],
							"Nombre" => isset($ProductoNombre[$row2["tipo"].$row2["objeto_codigo"].$row2["clasificacion"]]) ? $ProductoNombre[$row2["tipo"].$row2["objeto_codigo"].$row2["clasificacion"]]:"No Posee Nombre.",
							"Cantidad" => $row2["cantidad"],
							"Unidad" => isset($ProductoUnidad[$row2["tipo"].$row2["objeto_codigo"].$row2["clasificacion"]]) ? $ProductoUnidad[$row2["tipo"].$row2["objeto_codigo"].$row2["clasificacion"]]:"No Posee Unidad.",
							"Uso" => $row2["uso"],
							"Valor" => $row2["valor"],
							"Total" => $row2["total"],
							"Tipo" => $row2["tipo"],
						);
					}
				}
				
				// Order
				$Apu_Items = Array();
				$Mat_Items = Array();
				$Mo_Items = Array();
				$Equ_Items = Array();
				
				foreach ($APU_Items AS $Items)
				{
					switch ($Items["Tipo"])
					{
						case "APU":
							$Apu_Items[] = Array(
								"Codigo" => $Items["Codigo"],
								"Nombre" => $Items["Nombre"],
								"Cantidad" => $Items["Cantidad"],
								"Unidad" => $Items["Unidad"],
								"Uso" => $Items["Uso"],
								"Valor" => $Items["Valor"],
								"Total" => $Items["Total"],
								"Tipo" => "Materiales",
							);
						break;
						
						case "Materiales":
							$Mat_Items[] = Array(
								"Codigo" => $Items["Codigo"],
								"Nombre" => $Items["Nombre"],
								"Cantidad" => $Items["Cantidad"],
								"Unidad" => $Items["Unidad"],
								"Uso" => $Items["Uso"],
								"Valor" => $Items["Valor"],
								"Total" => $Items["Total"],
								"Tipo" => $Items["Tipo"],
							);
						break;
						
						case "Mano de Obra":
							$Mo_Items[] = Array(
								"Codigo" => $Items["Codigo"],
								"Nombre" => $Items["Nombre"],
								"Cantidad" => $Items["Cantidad"],
								"Unidad" => $Items["Unidad"],
								"Uso" => $Items["Uso"],
								"Valor" => $Items["Valor"],
								"Total" => $Items["Total"],
								"Tipo" => $Items["Tipo"],
							);
						break;
						
						case "Equipos":
							$Equ_Items[] = Array(
								"Codigo" => $Items["Codigo"],
								"Nombre" => $Items["Nombre"],
								"Cantidad" => $Items["Cantidad"],
								"Unidad" => $Items["Unidad"],
								"Uso" => $Items["Uso"],
								"Valor" => $Items["Valor"],
								"Total" => $Items["Total"],
								"Tipo" => $Items["Tipo"],
							);
						break;
					}
				}
				
				$APU_Items = array_merge($Apu_Items, $Mat_Items, $Mo_Items, $Equ_Items);
				
				$Desperdicios = (($Total_Mat / 100) * $row1["desperdicios"]);
				$Gastos = (($Total_Mo / 100) * $row1["gastos"]);
				
				$APU[] = Array(
					"Item" => $row["item"],
					"Codigo" => $row["codigo"],
					"Nombre" => $row["nombre"],
					"Cantidad" => 1,
					"Grupo" => $row1["grupo"],
					"SubGrupo" => $row1["subgrupo"],
					"Unidad" => $row1["unidad"],
					"Desperdicios" => $row1["desperdicios"],
					"Desperdicios_Val" => $Desperdicios,
					"Gastos" => $row1["gastos"],
					"Gastos_Val" => $Gastos,
					"Valor" => $row1["valor"],
					"Notas" => $row1["notas"],
					"Total_Mat" => $Total_Mat + $Total_Apu,
					"Total_Mo" => $Total_Mo,
					"Total_Equ" => $Total_Equ,
					"Items" => isset($APU_Items) ? $APU_Items:array(),
				);
			}
		}
	}
	if (isset($APU))
		echo json_encode($APU);
	else
		echo json_encode(array());
}
else if (isset($_POST["Presupuesto_Valores"]))
{
	$data1 = $_POST["Presupuesto_Valores"];
	$data2 = $_POST["Presupuesto_Valores"];
	
	/*
	foreach($data1 AS $item)
	{
		if (isset($TmpClass[$item["Clasificacion"]]))
		{
			$Codigos[$Clasificacion][] = array(
				"Codigo" => $item["Codigo"],
			);
		}
		else {
			$TmpClass[$item["Clasificacion"]] = 1;
			$Clasificacion[] = array(
				"Clasificacion" => $item["Clasificacion"],
			);
			$Codigos[$Clasificacion][] = array(
				"Codigo" => $item["Codigo"],
			);
		}
	}
	
	foreach($Clasificacion AS $item)
	{
		if ($item == "Titulo")
			continue;
		
		$Clause = implode("', '", $Codigos[$item]);
		
		$query = "SELECT * FROM `apu_".$item."` codigo IN ('".$Clause."')";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$query = "SELECT * FROM `apu_".$item."_movs` codigo = '".$row["codigo"]."')";
				$result1 = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
				if (mysql_num_rows($result1) > 0)
				{
					while($row1 = mysql_fetch_array($result1, MYSQL_ASSOC))
					{
						// calculos de cada tipo de movimiento
					}
				}
				
				$data[] = array(
					"Codigo" => = $row["codigo"],
					"Nombre" => $row["nombre"],
					"Unidad" => $row["unidad"],
					"Valor" => $row["valor"], // reemplazar con la suma del calculo que aun no se ha hecho
					"Clasificacion" => $item,
				);
			}
		}
	}
	*/
	
	
	foreach($data1 AS $item)
	{
		if (isset($TmpClass[$item["Clasificacion"]]))
			continue;
		else {
			$TmpClass[$item["Clasificacion"]] = 1;
			$Clasificacion[] = array(
				"Clasificacion" => $item["Clasificacion"],
			);
		}
	}
	
	foreach($Clasificacion AS $item)
	{
		$Clasificacion = $item["Clasificacion"];
		
		if ($Clasificacion == "Titulo")
			continue;
		
		$query = "SELECT * FROM `apu_".$Clasificacion."` ";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$ProductoNombre[$row["codigo"].$Clasificacion] = $row["nombre"];
				$ProductoUnidad[$row["codigo"].$Clasificacion] = $row["unidad"];
				$ProductoValor[$row["codigo"].$Clasificacion] = $row["valor"];
			}
		}
	}
	
	foreach($data2 AS $item)
	{
		$Clasificacion = $item["Clasificacion"];
		$Codigo = $item["Codigo"];
		
		if ($Clasificacion == "Titulo")
			continue;
		
		$data[] = array(
			"Codigo" => $Codigo,
			"Nombre" => $ProductoNombre[$Codigo.$Clasificacion],
			"Unidad" => $ProductoUnidad[$Codigo.$Clasificacion],
			"Valor" => $ProductoValor[$Codigo.$Clasificacion],
			"Clasificacion" => $Clasificacion,
		);
	}
	
	echo json_encode($data);
}
else if (isset($_GET['Presupuesto_APU']))
{
	if (!isset($_GET['Clasificacion']))
		die();
	
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:"";
	$SubGrupo = isset($_GET["SubGrupo"]) ? $_GET["SubGrupo"]:"";
	
	$query = "SELECT * FROM `apu_".$_GET['Clasificacion']."` WHERE grupo LIKE '%".$Grupo."%' 
	AND subgrupo LIKE '%".$SubGrupo."%' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Codigo' => $row['codigo'],
				'Nombre' => $row['nombre'],
				'Unidad' => $row['unidad'],
				'Valor' => $row['valor'],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['APU_Items']))
{
	if (!isset($_GET['Clasificacion']))
	{
		echo json_encode(array());
		die();
	}
	
	$Total_Mat = 0;
	$Total_Mo = 0;
	$Desperdicios = 0;
	$Gastos = 0;
		
	$query = "SELECT desperdicios, gastos FROM `apu_".$_GET['Clasificacion']."` WHERE codigo = '".$_GET['Codigo']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$Desperdicios = $row["desperdicios"];
		$Gastos = $row["gastos"];
	}
	
	$query = "SELECT DISTINCT clasificacion FROM `apu_".$_GET['Clasificacion']."_movs` WHERE codigo = '".$_GET['Codigo']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$query = "SELECT * FROM `apu_".$row["clasificacion"]."` ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					$ProductoNombre[$row1["codigo"].$row["clasificacion"]] = $row1["nombre"];
					$ProductoUnidad[$row1["codigo"].$row["clasificacion"]] = $row1["unidad"];
				}
			}
			
			$query = "SELECT * FROM `apu_equ_".$row["clasificacion"]."` ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					$ProductoNombre[$row1["codigo"].$row["clasificacion"]] = $row1["nombre"];
					$ProductoUnidad[$row1["codigo"].$row["clasificacion"]] = $row1["unidad"];
				}
			}
			
			$query = "SELECT * FROM `apu_mo_".$row["clasificacion"]."` ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					$ProductoNombre[$row1["codigo"].$row["clasificacion"]] = $row1["nombre"];
					$ProductoUnidad[$row1["codigo"].$row["clasificacion"]] = $row1["unidad"];
				}
			}
			
			$query = "SELECT * FROM `apu_mat_".$row["clasificacion"]."` ";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					$ProductoNombre[$row1["codigo"].$row["clasificacion"]] = $row1["nombre"];
					$ProductoUnidad[$row1["codigo"].$row["clasificacion"]] = $row1["unidad"];
				}
			}
		}
	}
	
	$query = "SELECT * FROM `apu_".$_GET['Clasificacion']."_movs` WHERE codigo = '".$_GET['Codigo']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			if ($row["tipo"] == "Materiales")
				$Total_Mat += $row["total"];
			else if ($row["tipo"] == "Mano de Obra")
				$Total_Mo += $row["total"];
			
			$Items[] = array(
				'Codigo' => $row['objeto_codigo'],
				'Nombre' => $row["tipo"] == "Productos" ? $ProductoNombre[$row["objeto_codigo"]]:$ProductoNombre[$row["objeto_codigo"].$row["clasificacion"]],
				'Unidad' => $row["tipo"] == "Productos" ? $ProductoUnidad[$row["objeto_codigo"]]:$ProductoUnidad[$row["objeto_codigo"].$row["clasificacion"]],
				'Cantidad' => $row['cantidad'],
				'Valor' => $row['valor'],
				'Uso' => $row['uso'],
				'Total' => $row['total'],
				'Tipo' => $row['tipo'],
				'Clasificacion' => $row['clasificacion'],
			);
		}
		
		$Desperdicios = (($Total_Mat / 100) * $Desperdicios);
		$Gastos = (($Total_Mo / 100) * $Gastos);
		
		$Items[] = array(
			'Codigo' => "Desp",
			'Nombre' => "Desperdicio de Material",
			'Unidad' => "",
			'Cantidad' => "1",
			'Valor' => $Desperdicios,
			'Uso' => 100,
			'Total' => $Desperdicios,
			'Tipo' => "Materiales",
			'Clasificacion' => $row['clasificacion'],
		);
		$Items[] = array(
			'Codigo' => "Herr",
			'Nombre' => "Herramienta de Menor",
			'Unidad' => "",
			'Cantidad' => "1",
			'Valor' => $Gastos,
			'Uso' => 100,
			'Total' => $Gastos,
			'Tipo' => "Mano de Obra",
			'Clasificacion' => $row['clasificacion'],
		);
		echo json_encode($Items);
	}
	else
		echo json_encode(array());
}
else if (isset($_GET['APU_Cargar']))
{
	if (!isset($_GET['Clasificacion']))
		die();
	
	$query = "SELECT * FROM `apu_".$_GET['Clasificacion']."` WHERE codigo = '".$_GET['APU_Cargar']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$query = "SELECT DISTINCT clasificacion FROM `apu_".$_GET['Clasificacion']."_movs` WHERE codigo = '".$_GET['APU_Cargar']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$query = "SELECT * FROM `apu_".$row1["clasificacion"]."` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while($row2 = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$ProductoNombre[$row2["codigo"].$row1["clasificacion"]] = $row2["nombre"];
						$ProductoUnidad[$row2["codigo"].$row1["clasificacion"]] = $row2["unidad"];
					}
				}
				
				$query = "SELECT * FROM `apu_mo_".$row1["clasificacion"]."` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while($row3 = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$ProductoNombre[$row3["codigo"].$row1["clasificacion"]] = $row3["nombre"];
						$ProductoUnidad[$row3["codigo"].$row1["clasificacion"]] = $row3["unidad"];
					}
				}
				
				$query = "SELECT * FROM `apu_equ_".$row1["clasificacion"]."` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while($row4 = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$ProductoNombre[$row4["codigo"].$row1["clasificacion"]] = $row4["nombre"];
						$ProductoUnidad[$row4["codigo"].$row1["clasificacion"]] = $row4["unidad"];
					}
				}
				
				$query = "SELECT * FROM `apu_mat_".$row1["clasificacion"]."` ";
				$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
				if (mysql_num_rows($result) > 0)
				{
					while($row5 = mysql_fetch_array($result, MYSQL_ASSOC))
					{
						$ProductoNombre[$row5["codigo"].$row1["clasificacion"]] = $row5["nombre"];
						$ProductoUnidad[$row5["codigo"].$row1["clasificacion"]] = $row5["unidad"];
					}
				}
			}
		}
		
		$query = "SELECT * FROM `apu_".$_GET['Clasificacion']."_movs` WHERE codigo = '".$_GET['APU_Cargar']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Items[] = array(
					'Codigo' => $row1['objeto_codigo'],
					'Nombre' => $row1["tipo"] == "Productos" ? $ProductoNombre[$row1["objeto_codigo"]]:$ProductoNombre[$row1["objeto_codigo"].$row1["clasificacion"]],
					'Unidad' => $row1["tipo"] == "Productos" ? $ProductoUnidad[$row1["objeto_codigo"]]:$ProductoUnidad[$row1["objeto_codigo"].$row1["clasificacion"]],
					'Cantidad' => $row1['cantidad'],
					'Valor' => $row1['valor'],
					'Uso' => $row1['uso'],
					'Total' => $row1['total'],
					'Tipo' => $row1['tipo'],
					'Clasificacion' => $row1['clasificacion'],
				);
			}
		}
		
		$data[] = array(
			'ID' => $row['id'],
			'Grupo' => $row['grupo'],
			'SubGrupo' => $row['subgrupo'],
			'Unidad' => $row['unidad'],
			'Items' => isset($Items) ? $Items:array(),
			'Desperdicios' => $row['desperdicios'],
			'Gastos' => $row['gastos'],
			'Valor' => $row['valor'],
		);
		
		echo json_encode($data);
	}
}
else if (isset($_GET["Presupuesto_Listado_APU"]))
{
	if (!isset($_GET["Clasificacion"]))
		die();
	
	$query = "SELECT * FROM `par_presupuesto_grupo_".$_GET["Clasificacion"]."` 
	WHERE categoria = 'APU' ORDER BY codigo ASC, grupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Grupos[$row["codigo"]] = $row["grupo"];
		}
	}
	
	$query = "SELECT * FROM `par_presupuesto_subgrupo_".$_GET["Clasificacion"]."` 
	WHERE categoria = 'APU' ORDER BY codigo ASC, subgrupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$SubGrupos[$row["codigo"]] = $row["subgrupo"];
		}
	}
	
	$query = "SELECT * FROM `apu_".$_GET["Clasificacion"]."` ORDER BY codigo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"ID" => $row["id"],
				"Codigo" => $row["codigo"],
				"Nombre" => $row["nombre"],
				"Grupo" => isset($Grupos[$row["grupo"]]) ? $row["grupo"].". ".$Grupos[$row["grupo"]]:"No posee Grupo",
				"SubGrupo" => isset($SubGrupos[$row["subgrupo"]]) ? $row["subgrupo"].". ".$SubGrupos[$row["subgrupo"]]:"No posee SubGrupo",
				"Unidad" => $row["unidad"],
				"Desperdicios" => $row["desperdicios"],
				"Gastos" => $row["gastos"],
				"Valor" => $row["valor"],
				"Notas" => $row["notas"],
				"DigitadorID" => $row["digitado_por"],
				"Fecha_Digitado" => $row["fecha_digitado"],
				"ModificadorID" => $row["modificado_por"],
				"Fecha_Modificado" => $row["fecha_modificado"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET["Presupuesto_Materiales"]))
{
	if (!isset($_GET["Clasificacion"]))
		die();
	
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:"";
	$SubGrupo = isset($_GET["SubGrupo"]) ? $_GET["SubGrupo"]:"";
	
	$query = "SELECT * FROM `apu_mat_".$_GET["Clasificacion"]."` WHERE grupo LIKE '%".$Grupo."%' 
	AND subgrupo LIKE '%".$SubGrupo."%' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"Codigo" => $row["codigo"],
				"Nombre" => $row["nombre"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET["Presupuesto_Materiales_Cargar"]))
{
	if (!isset($_GET["Clasificacion"]))
		die();
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM `apu_mat_".$_GET["Clasificacion"]."` WHERE codigo = '".$_GET["Presupuesto_Materiales_Cargar"]."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$query = "SELECT * FROM `apu_mat_".$_GET["Clasificacion"]."_prov` WHERE codigo = '".$_GET["Presupuesto_Materiales_Cargar"]."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Proveedores[] = array(
					"Proveedor" => $row1["proveedor_id"] == "" ? "":$ClienteNombre[$row1["proveedor_id"]],
					"ProveedorID" => $row1["proveedor_id"],
					"Codigo" => $row1["proveedor_codigo"],
				);
			}
		}
		
		$data[] = array(
			"ID" => $row["id"],
			"Grupo" => $row["grupo"],
			"SubGrupo" => $row["subgrupo"],
			"Uso" => $row["uso"],
			"Unidad" => $row["unidad"],
			"Peso" => $row["peso"],
			"Valor" => $row["valor"],
			"Valor_KM" => $row["valor_km"],
			"Imagen" => $row["imagen"],
			"Notas" => $row["notas"],
			"Proveedores" => isset($Proveedores) ? $Proveedores:array(),
		);
		
		echo json_encode($data);
	}
}
else if (isset($_GET["Presupuesto_Listado_Materiales"]))
{
	if (!isset($_GET["Clasificacion"]))
		die();
	
	$query = "SELECT * FROM `par_presupuesto_grupo_".$_GET["Clasificacion"]."` 
	WHERE categoria = 'Materiales' ORDER BY codigo ASC, grupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Grupos[$row["codigo"]] = $row["grupo"];
		}
	}
	
	$query = "SELECT * FROM `par_presupuesto_subgrupo_".$_GET["Clasificacion"]."` 
	WHERE categoria = 'Materiales' ORDER BY codigo ASC, subgrupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$SubGrupos[$row["codigo"]] = $row["subgrupo"];
		}
	}
	
	$query = "SELECT * FROM `apu_mat_".$_GET["Clasificacion"]."` ORDER BY codigo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"ID" => $row["id"],
				"Codigo" => $row["codigo"],
				"Nombre" => $row["nombre"],
				"Grupo" => isset($Grupos[$row["grupo"]]) ? $row["grupo"].". ".$Grupos[$row["grupo"]]:"No posee Grupo",
				"SubGrupo" => isset($SubGrupos[$row["subgrupo"]]) ? $row["subgrupo"].". ".$SubGrupos[$row["subgrupo"]]:"No posee SubGrupo",
				"Unidad" => $row["unidad"],
				"Peso" => $row["peso"],
				"Uso" => $row["uso"],
				"Valor" => $row["valor"],
				"Valor_KM" => $row["valor_km"],
				"Notas" => $row["notas"],
				"DigitadorID" => $row["digitado_por"],
				"Fecha_Digitado" => $row["fecha_digitado"],
				"ModificadorID" => $row["modificado_por"],
				"Fecha_Modificado" => $row["fecha_modificado"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Presupuesto_ManodeObra']))
{
	if (!isset($_GET['Clasificacion']))
		die();
	
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:"";
	$SubGrupo = isset($_GET["SubGrupo"]) ? $_GET["SubGrupo"]:"";
	
	$query = "SELECT * FROM `apu_mo_".$_GET['Clasificacion']."` WHERE grupo LIKE '%".$Grupo."%' 
	AND subgrupo LIKE '%".$SubGrupo."%' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Codigo' => $row['codigo'],
				'Nombre' => $row['nombre'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['ManodeObra_Cargar']))
{
	if (!isset($_GET['Clasificacion']))
		die();

	$query = "SELECT * FROM `apu_mo_".$_GET['Clasificacion']."` WHERE codigo = '".$_GET['ManodeObra_Cargar']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$query = "SELECT * FROM `apu_mo_".$_GET['Clasificacion']."_movs` WHERE codigo = '".$_GET['ManodeObra_Cargar']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				switch($row1["tipo"])
				{
					case "SC":
						$Convencional[] = array(
							'Concepto' => $row1['concepto'],
							'Uso' => $row1['uso'],
							'Valor' => $row1['valor'],
							'Total' => $row1['total'],
							'Tipo' => $row1['tipo'],
							'Fijo' => $row1['fijo'],
						);
					break;
					case "PD":
						$Trabajador[] = array(
							'Concepto' => $row1['concepto'],
							'Uso' => $row1['uso'],
							'Valor' => $row1['valor'],
							'Total' => $row1['total'],
							'Tipo' => $row1['tipo'],
							'Fijo' => $row1['fijo'],
						);
					break;
					case "SCC":
						$Contratista[] = array(
							'Concepto' => $row1['concepto'],
							'Uso' => $row1['uso'],
							'Valor' => $row1['valor'],
							'Total' => $row1['total'],
							'Tipo' => $row1['tipo'],
							'Fijo' => $row1['fijo'],
						);
					break;
				}
			}
		}
		
		$data[] = array(
			'ID' => $row['id'],
			'Grupo' => $row['grupo'],
			'SubGrupo' => $row['subgrupo'],
			'Unidad' => $row['unidad'],
			'Uso' => $row['uso'],
			'Convencional' => isset($Convencional) ? $Convencional:array(),
			'Trabajador' => isset($Trabajador) ? $Trabajador:array(),
			'Contratista' => isset($Contratista) ? $Contratista:array(),
			'Valor_SC' => $row['valor_sc'],
			'Valor_PD' => $row['valor_pd'],
			'Valor_SCC' => $row['valor_scc'],
		);
		
		echo json_encode($data);
	}
}
else if (isset($_GET["Presupuesto_Listado_ManodeObra"]))
{
	if (!isset($_GET["Clasificacion"]))
		die();
	
	$query = "SELECT * FROM `par_presupuesto_grupo_".$_GET["Clasificacion"]."` 
	WHERE categoria = 'Mano de Obra' ORDER BY codigo ASC, grupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Grupos[$row["codigo"]] = $row["grupo"];
		}
	}
	
	$query = "SELECT * FROM `par_presupuesto_subgrupo_".$_GET["Clasificacion"]."` 
	WHERE categoria = 'Mano de Obra' ORDER BY codigo ASC, subgrupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$SubGrupos[$row["codigo"]] = $row["subgrupo"];
		}
	}
	
	$query = "SELECT * FROM `apu_mo_".$_GET["Clasificacion"]."` ORDER BY codigo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"ID" => $row["id"],
				"Codigo" => $row["codigo"],
				"Nombre" => $row["nombre"],
				"Grupo" => isset($Grupos[$row["grupo"]]) ? $row["grupo"].". ".$Grupos[$row["grupo"]]:"No posee Grupo",
				"SubGrupo" => isset($SubGrupos[$row["subgrupo"]]) ? $row["subgrupo"].". ".$SubGrupos[$row["subgrupo"]]:"No posee SubGrupo",
				"Unidad" => $row["unidad"],
				"Uso" => $row["uso"],
				"Valor" => $row["valor_scc"],
				"Notas" => $row["notas"],
				"DigitadorID" => $row["digitado_por"],
				"Fecha_Digitado" => $row["fecha_digitado"],
				"ModificadorID" => $row["modificado_por"],
				"Fecha_Modificado" => $row["fecha_modificado"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Presupuesto_Equipos']))
{
	if (!isset($_GET['Clasificacion']))
		die();
	
	$Grupo = isset($_GET["Grupo"]) ? $_GET["Grupo"]:"";
	$SubGrupo = isset($_GET["SubGrupo"]) ? $_GET["SubGrupo"]:"";
	
	$query = "SELECT * FROM `apu_equ_".$_GET['Clasificacion']."` WHERE grupo LIKE '%".$Grupo."%' 
	AND subgrupo LIKE '%".$SubGrupo."%' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'Codigo' => $row['codigo'],
				'Nombre' => $row['nombre'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Presupuesto_Equipos_Cargar']))
{
	if (!isset($_GET['Clasificacion']))
		die();
	
	$ClienteNombre = ClientesNombre();
	
	$query = "SELECT * FROM `apu_equ_".$_GET['Clasificacion']."` WHERE codigo = '".$_GET['Presupuesto_Equipos_Cargar']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$query = "SELECT * FROM `apu_equ_".$_GET['Clasificacion']."_prov` WHERE codigo = '".$_GET['Presupuesto_Equipos_Cargar']."'";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while($row1 = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$Proveedores[] = array(
					'Proveedor' => $row1['proveedor_id'] == "" ? "":$ClienteNombre[$row1['proveedor_id']],
					'ProveedorID' => $row1['proveedor_id'],
					'Codigo' => $row1['proveedor_codigo'],
				);
			}
		}
		
		$data[] = array(
			'ID' => $row['id'],
			'Valor' => $row['valor'],
			'Uso' => $row['uso'],
			'Grupo' => $row['grupo'],
			'SubGrupo' => $row['subgrupo'],
			'Unidad' => $row['unidad'],
			'Imagen' => $row['imagen'],
			'Notas' => $row['notas'],
			'Proveedores' => isset($Proveedores) ? $Proveedores:array(),
		);
		
		echo json_encode($data);
	}
}
else if (isset($_GET["Presupuesto_Listado_Equipos"]))
{
	if (!isset($_GET["Clasificacion"]))
		die();
	
	$query = "SELECT * FROM `par_presupuesto_grupo_".$_GET["Clasificacion"]."` 
	WHERE categoria = 'Equipos' ORDER BY codigo ASC, grupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Grupos[$row["codigo"]] = $row["grupo"];
		}
	}
	
	$query = "SELECT * FROM `par_presupuesto_subgrupo_".$_GET["Clasificacion"]."` 
	WHERE categoria = 'Equipos' ORDER BY codigo ASC, subgrupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$SubGrupos[$row["codigo"]] = $row["subgrupo"];
		}
	}
	
	$query = "SELECT * FROM `apu_equ_".$_GET["Clasificacion"]."` ORDER BY codigo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #3: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"ID" => $row["id"],
				"Codigo" => $row["codigo"],
				"Nombre" => $row["nombre"],
				"Grupo" => isset($Grupos[$row["grupo"]]) ? $row["grupo"].". ".$Grupos[$row["grupo"]]:"No posee Grupo",
				"SubGrupo" => isset($SubGrupos[$row["subgrupo"]]) ? $row["subgrupo"].". ".$SubGrupos[$row["subgrupo"]]:"No posee SubGrupo",
				"Unidad" => $row["unidad"],
				"Uso" => $row["uso"],
				"Valor" => $row["valor"],
				"Notas" => $row["notas"],
				"DigitadorID" => $row["digitado_por"],
				"Fecha_Digitado" => $row["fecha_digitado"],
				"ModificadorID" => $row["modificado_por"],
				"Fecha_Modificado" => $row["fecha_modificado"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Presupuesto_Check_Clasificacion']))
{
	if (!isset($_GET["Clasificacion"]))
		die();
	$Clasificacion = $_GET["Clasificacion"];
	
	$query = "SELECT * FROM apu_clasificacion WHERE clasificacion = '".$Clasificacion."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
		echo "EXIST";
}
else if (isset($_GET["Requerimientos_Datos_Solicitud"]))
{
	$Interno = $_GET["Requerimientos_Datos_Solicitud"];
	
	$ClienteNombre = ClientesNombre();
	$ProductInfo = GetProductFullInfo("Requerimientos");
	
	$query = "SELECT * FROM req_solicitud_movs WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Items[] = array(
				"Codigo" => $row["codigo"],
				"Nombre" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Nombre"]:"No Existe!",
				"Unidad" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Unidad"]:"No Existe!",
				"Valor" => $row["valor"],
				"Cantidad" => $row["cantidad"],
			);
		}
	}
	
	$query = "SELECT * FROM req_solicitud WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array(
			"Fecha" => $row["fecha"],
			"ClienteID" => $row["cliente_id"],
			"Prioridad" => $row["prioridad"],
			"Observaciones" => $row["observaciones"],
			"Productos" => isset($Items) ? $Items:array(),
		);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Requerimientos_Datos_Compra"]))
{
	$Interno = $_GET["Requerimientos_Datos_Compra"];
	
	$ClienteNombre = ClientesNombre();
	$ProductInfo = GetProductFullInfo("Requerimientos");
	
	$query = "SELECT * FROM req_compras_movs WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$Items[] = array(
				"Codigo" => $row["codigo"],
				"Nombre" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Nombre"]:"No Existe!",
				"Unidad" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Unidad"]:"No Existe!",
				"Valor" => $row["valor"],
				"Cantidad" => $row["cantidad"],
			);
		}
	}
	
	$query = "SELECT * FROM req_compras WHERE interno = '".$Interno."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result);
		
		$data[] = array(
			"Factura" => $row["factura"],
			"Grupo" => $row["grupo"],
			"SubGrupo" => $row["subgrupo"],
			"SubGrupo2" => $row["subgrupo2"],
			"Fecha" => $row["fecha"],
			"ClienteID" => $row["cliente_id"],
			"Observaciones" => $row["observaciones"],
			"SubTotal" => $row["subtotal"],
			"Servicio_Desc" => $row["tipo_servicio"],
			"Servicio" => $row["tipo_servicio_valor"],
			"IVA" => $row["iva"],
			"Descuento_Desc" => $row["tipo_descuento"],
			"Descuento" => $row["tipo_descuento_valor"],
			"Total" => $row["total"],
			"Productos" => isset($Items) ? $Items:array(),
		);
	}
	
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Requerimientos_Productos"]))
{
	$query = "SELECT * FROM requerimientos_productos ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"Codigo" => $row["codigo"],
				"Nombre" => $row["nombre"],
				"Unidad" => $row["unidad"],
				"Peso" => $row["peso"],
				"Existencia" => $row["existencia"],
				"Valor" => $row["valor"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET["Requerimientos_Productos_Listado"]))
{
	$query = "SELECT * FROM requerimientos_productos ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"ID" => $row["id"],
				"Codigo" => $row["codigo"],
				"Nombre" => $row["nombre"],
				"Categoria" => $row["categoria"],
				"Grupo" => $row["grupo"],
				"SubGrupo" => $row["subgrupo"],
				"Unidad" => $row["unidad"],
				"Peso" => $row["peso"],
				"Valor" => $row["valor"],
				"Existencia" => $row["existencia"],
				"Stock" => $row["stock"],
				"Notas" => $row["notas"],
				"Vencimiento" => $row["vencimiento"]." 00:00:00",
				"DigitadorID" => $row["digitado_por"],
				"Fecha_Digitado" => $row["fecha_digitado"],
				"ModificadorID" => $row["modificado_por"],
				"Fecha_Modificado" => $row["fecha_modificado"],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET["Requerimientos_Productos_Faltantes"]))
{
	$ProductInfo = GetProductFullInfo("Requerimientos");
	
	$query = "SELECT * FROM req_solicitud WHERE estado = 'Pendiente' ORDER BY fecha DESC";
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
				$Existencia = isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Existencia"]:0;
				$Stock = isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Stock"]:0;
				$Total = $Existencia - $row["total"];
				if ($Total < 0)
					$Total = ABS($Total);
				else if ($Total >= $Stock)
					continue;
				
				$data[] = array (
					"Codigo" => $row["codigo"],
					"Nombre" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Nombre"]:"No Existe!",
					"Unidad" => isset($ProductInfo[$row["codigo"]]) ? $ProductInfo[$row["codigo"]]["Unidad"]:"No Existe!",
					"Cantidad" => $Total,
				);
			}
		}
	}
	
	echo json_encode($data);
}
else
{
	$query = "SELECT * FROM productos WHERE activo = 'true' ORDER BY nombre ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'CodFab' => $row['cod_fab'],
				'Nombre' => $row['nombre'],
				'UndMed' => $row['und_med'],
				'Peso' => $row['peso'],
				'Existencia' => $row['existencia'],
			);
		}
		echo json_encode($data);
	}
}
?>