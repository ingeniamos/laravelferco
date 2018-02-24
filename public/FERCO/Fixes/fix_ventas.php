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

//----------------------------------------------------------------
// Agregar el respectivo autoincrement a las siguientes 
// tablas al finalizar los datos...
//
// - caja_final
// - compras_final
// - fact_final
// - maquinaria_final
// - produccion_final
//
// * fact_movs y probablemente inventario_movs tienen mas de 16mil registros en cantidad '0'
// * produccion_proc_movs 2 movimientos con un producto inexistente, codigo -> chg5.5 cambiar a -> 10000160.2
// * produccion_proc_movs mas de 3mil records con operarios vacios.
// * no se puede usar SUBSTR para extraer el codigo de usuario ya que no todos poseen la misma cantidad de caracteres...
// * revisar produccion el avance de muchos dice 1% (deberia ser 100%)
//
// -------------------- SACADO DE PRODUCCION --------------------
// * OP001345 = Proceso de figurado que nunca finalizo y tiene errores en el avance mas del 100%
// * Productos Inexistentes = FE00729, FE00730, FE00732, FE00734, 1000070.7, chg5.5, mq
// * Clientes Inexistentes = 4272351, 88230899, 72017502 2, 23523286, 88244978 5, 4079227-01, 96192297, 1090374760, 13487059 1, 63342987-1, 
// 13373080 7, 1090626389, 882519333, 13484137 4, 2153668, 38242344 5
//----------------------------------------------------------------

/*
Tablas Afectadas	-	Tablas a Sacar Informacion
	NUEVO						FERCO

- bancos			-		bancos_copy
- caja_final		-		cajaf
- cheques			-		cheques_copy
- compras_final		-		compras_final_copy
- compras_movs		-		compra_movs
- cxp_movs			-		mov_cxp
- fact_final		-		fact_final_copy
- fact_movs			-		fact_movs_copy
- inventario_movs	-		movimientosf
- maquinaria_final	-		maqs_final1
- maquinaria_movs	-		maqs_final
- mov_clientes		-		mov_clientes_copy
- produccion_final	-		produccionf
- produccion_movs	-		produccion2r
- produccion_proc	-		produccionf -> producciont -> produccione -> produccions -> produccionfg
- produccion_proc_movs	-	produccion2r
- productos			-		productos_copy
- repuestos			-		maqs_repuestos
*/

// Time Check!
$now = time();
echo "Tiempo de Inicio-> ".$now." <br/><br/>";

// Inventarios Actualiza Productos
echo "17º Productos";
echo "<br/>";
echo "* Insertar Productos<br/>";

// Productos
$query = "INSERT INTO productos (cod_fab, categoria, grupo, subgrupo, nombre, und_med, peso, costo, ultimo_costo, costo_promedio, lista1, 
lista2, lista3, lista4, existencia, stock_minimo, notas, vencimiento, factura_sin_existencia, produccion) SELECT CodFab, Categoría, Grupo, 
Subgrupo, Nombre, UndMed, Peso, Costo, `Último costo`, `Costo promedio`, Lista1, Lista2, Lista3, Lista4, Existencia, `Stock Minimo`, Notas, 
`Fecha vencimiento`, IF(`Facturas sin existencia` > 0, 'true', 'false'), IF(Produccion > 0, 'true', 'false') FROM productos_copy";
$result = mysql_query($query) or die("SQL Error 17-1: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "18º Repuestos";
echo "<br/>";

$query = "INSERT INTO repuestos (codigo, nombre, valor, peso) SELECT Cod, Nombre, Valor, Peso FROM maqs_repuestos";
$result = mysql_query($query) or die("SQL Error 18: " . mysql_error());
echo "- Finalizado<br/><br/>";

//Bancos
echo "1º Bancos";
echo "<br/>";

$query = "INSERT INTO bancos (cliente_id, caja_interno, caja_recibo, fecha, tipo, numero, valor, banco, estado, tipo_mov, digitado_por) 
SELECT Idtercero, MC_Asignado, RC_asignado, Fecha_Mov, `Tipo documento`, `Número documento`, ABS(Valor), Banco, Estado, 
IF(Valor > 0, 'Entrada', 'Salida'), Digitó FROM bancos_copy";
$result = mysql_query($query) or die("SQL Error 1-1: " . mysql_error());

$query = "DELETE FROM bancos WHERE banco = ''";
$result = mysql_query($query) or die("SQL Error 1-2: " . mysql_error());

$query = "SELECT caja_interno FROM bancos WHERE cliente_id = '' AND caja_interno != ''";
$result = mysql_query($query) or die("SQL Error 1-3: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "SELECT IdTercero FROM cajaf WHERE RC = '".$row["caja_interno"]."'";
		$result1 = mysql_query($query) or die("SQL Error 1-4: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			$query = "UPDATE bancos SET cliente_id = '".$row1["IdTercero"]."' WHERE caja_interno = '".$row["caja_interno"]."'";
			$result2 = mysql_query($query) or die("SQL Error 1-5: " . mysql_error());
		}
	}
}

$query = "SELECT caja_interno FROM bancos WHERE caja_recibo = '' AND caja_interno != ''";
$result = mysql_query($query) or die("SQL Error 1-6: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "SELECT RC1 FROM cajaf WHERE RC = '".$row["caja_interno"]."'";
		$result1 = mysql_query($query) or die("SQL Error 1-7: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			$query = "UPDATE bancos SET caja_recibo = '".$row1["RC1"]."' WHERE caja_interno = '".$row["caja_interno"]."'";
			$result2 = mysql_query($query) or die("SQL Error 1-8: " . mysql_error());
		}
	}
}

$query = "SELECT RC FROM cajaf WHERE Estado = 'Anulado'";
$result = mysql_query($query) or die("SQL Error 1-9: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "UPDATE bancos SET estado = 'Anulado' WHERE caja_interno = '".$row["RC"]."'";
		$result1 = mysql_query($query) or die("SQL Error 1-10: " . mysql_error());
	}
}

$query = "SELECT RC FROM cajaf WHERE Estado = 'Pendiente'";
$result = mysql_query($query) or die("SQL Error 1-11: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "UPDATE bancos SET estado = 'Pendiente' WHERE caja_interno = '".$row["RC"]."'";
		$result1 = mysql_query($query) or die("SQL Error 1-12: " . mysql_error());
	}
}

echo "* Actualizar Estados<br/>";

$query = "UPDATE bancos SET tipo = 'Tarjeta de Credito' WHERE tipo = 'Tarjeta de crédito' OR tipo = 'Tarjeta crédito'";
$result = mysql_query($query) or die("SQL Error 1-13: " . mysql_error());
$query = "UPDATE bancos SET tipo = 'Tarjeta de Debito' WHERE tipo = 'Tarjeta débito'";
$result = mysql_query($query) or die("SQL Error 1-14: " . mysql_error());
$query = "UPDATE bancos SET tipo = 'Consignacion' WHERE tipo = 'Consignación'";
$result = mysql_query($query) or die("SQL Error 1-15: " . mysql_error());
$query = "UPDATE bancos SET tipo = 'Canje de Cheque' WHERE tipo = 'Caje de cheque'";
$result = mysql_query($query) or die("SQL Error 1-16: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "2º Caja Final";
echo "<br/>";

// Caja Final
$query = "INSERT INTO caja_final (id, fecha, caja_interno, caja_recibo, cxp, categoria, grupo, subgrupo, subgrupo2, cliente_id, aplicado_a, 
observaciones, efectivo, cheque, consignacion, total, saldo, digitado_por, aprobado_por, rete_iva, rete_ica, rete_fuente, descuento, 
concepto_dcto, estado) SELECT Id, Fecha, RC, RC1, Dprov, Categoría, Grupo, Subgrupo, Subg2, IdTercero, Concepto, Obs, ABS(Efectivo), ABS(T_cheques), 
ABS(Consignación), ABS(Total1), ABS(Saldo), Digitó, Aprobó, reteiva, reteica, retefuente, descuento, concepto_dcto, Estado FROM cajaf";
$result = mysql_query($query) or die("SQL Error 2-1: " . mysql_error());

$query = "UPDATE caja_final SET estado = 'Anulado' WHERE estado = ''";
$result = mysql_query($query) or die("SQL Error 2-2: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "3º Cheques";
echo "<br/>";

// Cheques
$query = "INSERT INTO cheques (cliente_id, caja_interno, caja_recibo, fecha, cheque, valor, banco, cuenta, estado, estado_cheque, 
fecha_cheque, banco_destino, digitado_por) SELECT Idtercero, MC_Asignado, RC_asignado, Fecha_Mov, `Número cheque`, ABS(Valor), 
Banco, Cuenta, Estado, Condición, Fecha_cheque, `Banco destino`, Digitó FROM cheques_copy";
$result = mysql_query($query) or die("SQL Error 3-1: " . mysql_error());

$query = "UPDATE cheques SET estado = 'PostFechado' WHERE estado = '' OR estado = 'Postfechado'";
$result = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());

$query = "UPDATE cheques SET estado = 'Al Dia' WHERE estado = 'Al día'";
$result = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());

$query = "UPDATE cheques SET estado = 'Pagado' WHERE estado = 'Pagado2'";
$result = mysql_query($query) or die("SQL Error 3-2: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "4º Compras Final";
echo "<br/>";

$query = "UPDATE compras_final_copy SET `Forma de pago` = 'Debito' WHERE `Forma de pago` = 'Débito'";
$result = mysql_query($query) or die("SQL Error 4-1: " . mysql_error());

$query = "UPDATE compras_final_copy SET `Forma de pago` = 'Credito' WHERE `Forma de pago` = 'Crédito'";
$result = mysql_query($query) or die("SQL Error 4-2: " . mysql_error());

$query = "UPDATE compras_final_copy SET Estado = 'Anulado' WHERE Estado = 'Cancelado'";
$result = mysql_query($query) or die("SQL Error 4-3: " . mysql_error());

$query = "UPDATE compras_final_copy SET Estado = 'Autorizado' WHERE Estado = 'Aprobado'";
$result = mysql_query($query) or die("SQL Error 4-4: " . mysql_error());

$query = "UPDATE compras_final_copy SET Estado = 'Aprobado' WHERE Estado = 'Despachado' OR Estado = 'Debitado'";
$result = mysql_query($query) or die("SQL Error 4-5: " . mysql_error());

//Compras Final
$query = "INSERT INTO compras_final (id, entrada, doc_transp, factura, pedido, interno, fecha_compra, cliente_id, forma_pago, peso, 
peso_bascula, peso_remision, conductor, placa, notas, observaciones, tipo_servicio, tipo_servicio_valor, sub_total, total, iva, 
estado, digitado_por, autorizado_por, modificado_por, fecha_modificado, aprobado_por) SELECT Id, Pedido, `Orden de compra`, Factura, 
Pedido2, Remision, `Fecha Compra`, Documento, `Forma de pago`, Peso, `Peso báscula`, `Peso remisión`, Conductor, Placa, NOTAS, OBS, 
Otros, ABS(Otros_Valor), ABS(SUBTOTAL), ABS(TOTAL), ABS(GIVAF), Estado, Digitó, Aprobó, Modificó, Fmod, Ingresó FROM compras_final_copy";
$result = mysql_query($query) or die("SQL Error 4-6: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "5º Compras Movs";
echo "<br/>";

//Compras Movs
$query = "INSERT INTO compras_movs (codigo, cantidad, ultimo_costo, nuevo_costo, interno, entrada, doc_transp, factura, cliente_id) 
SELECT Codigo, Cantidad, ABS(Ucosto), ABS(Unitario), Remisión, Pedido, RemVta, Factura, `Id Proveedor` FROM compra_movs";
$result = mysql_query($query) or die("SQL Error 5-1: " . mysql_error());

$query = "SELECT interno FROM compras_movs WHERE entrada = ''";
$result = mysql_query($query) or die("SQL Error 5-2: " . mysql_error());

while ($row = mysql_fetch_array($result))
{
	$query = "SELECT entrada FROM compras_final WHERE interno = '".$row["interno"]."'";
	$result1 = mysql_query($query) or die("SQL Error 5-3: " . mysql_error());
	if (mysql_num_rows($result1) > 0)
	{
		$row1 = mysql_fetch_array($result1);
		$query = "UPDATE compras_movs SET entrada = '".$row1["entrada"]."' WHERE interno = '".$row["interno"]."'";
		$result2 = mysql_query($query) or die("SQL Error 5-4: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";

echo "6º CxP Movs";
echo "<br/>";
echo "* Insertar Ordenes de Compra<br/>";

$query = "INSERT INTO cxp_movs (compra_interno, compra_entrada, factura, cliente_id, tipo_movimiento, valor, saldo, 
estado, digitado_por, fecha_digitado, aprobado_por, origen1, origen2, origen3, origen_documento, caja_interno, grupo, subgrupo, subgrupo2) 
SELECT Consecutivo, t1, Factura, Proveedor_id, Tipo_movimiento, ABS(Valor), ABS(Saldo), Estado, Digitó, Fecha_Mov, Aprobó, `Origen de los fondos`, 
Origen2, Origen3, Documento, RC1, Grupo, Subgrupo, Subgrupo2 FROM mov_cxp";
$result = mysql_query($query) or die("SQL Error 6-1: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "* Actualizar Caja_Recibo, Grupo, SubGrupo y SubGrupo2<br/>";

$query = "SELECT RC, RC1, Grupo, Subgrupo, Subg2 FROM cajaf";
$result = mysql_query($query) or die("SQL Error 6-3: " . mysql_error());
while ($row = mysql_fetch_array($result))
{
	$query = "UPDATE cxp_movs SET caja_recibo = '".$row["RC1"]."', grupo = '".$row["Grupo"]."', subgrupo = '".$row["Subgrupo"]."', 
	subgrupo2 = '".$row["Subg2"]."' WHERE caja_interno = '".$row["RC"]."'";
	$result1 = mysql_query($query) or die("SQL Error 6-4: " . mysql_error());
}

$query = "UPDATE cxp_movs SET tipo_movimiento = 'Compra' WHERE tipo_movimiento = 'Compras'";
$result = mysql_query($query) or die("SQL Error 6-5: " . mysql_error());

$query = "UPDATE cxp_movs SET tipo_movimiento = 'Abono a Compra' WHERE tipo_movimiento = 'Abono a compra'";
$result = mysql_query($query) or die("SQL Error 6-6: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "7º Fact Final";
echo "<br/>";

$query = "UPDATE fact_final_copy SET `Forma de pago` = 'Debito' WHERE `Forma de pago` = 'Débito'";
$result = mysql_query($query) or die("SQL Error 7-1: " . mysql_error());

$query = "UPDATE fact_final_copy SET `Forma de pago` = 'Credito' WHERE `Forma de pago` = 'Crédito'";
$result = mysql_query($query) or die("SQL Error 7-2: " . mysql_error());

// Fact Final
$query = "INSERT INTO fact_final (id, interno, remision, fecha_remision, factura, fecha_factura, orden_compra, orden_produccion, 
cliente_id, ruta, direccion_entrega, forma_pago, recibo_caja, vendedor_codigo, cobrador_codigo, placa, notas, observaciones, 
tipo_servicio, tipo_servicio_valor, tipo_descuento, tipo_descuento_valor, sub_total, total, iva, estado, peso, digitado_por, 
anulado_por, fecha_anulado, autorizado_por, fecha_autorizado, modificado_por, fecha_modificado, aprobado_por, fecha_aprobado, 
tipo_pedido ) SELECT Id, Remision, `Orden de compra`, `Fecha Remisión`, Factura, `Fecha Factura`, Remision2, OP, Documento, Ruta, 
`Dirección de Entrega`, `Forma de pago`, `Recibo de caja`, Vendedor, Cobrador, Placa, NOTAS, OBS, Otros, ABS(Otros_Valor), 
(CASE WHEN Descuentos = GIVAF AND Descuentos > 0 THEN 'Descuento por IVA' WHEN Descuentos > GIVAF AND Descuentos > 0 THEN 'Descuento' ELSE '' END), 
ABS(Descuentos), ABS(SUBTOTAL), (CASE WHEN TOTAL IS NULL THEN ABS(TOTAL11) ELSE ABS(TOTAL) END), ABS(GIVAF), Estado, Peso, 
Digitó, Anula, FAnula, Aprobó, Faprob, Modifico, Fmodifico, Despachó, Despachado, 
(CASE WHEN Tipopedido = 'Producción' THEN 'Produccion' WHEN Tipopedido = 'Producido' THEN 'Produccion' ELSE 'Pedido' END) FROM fact_final_copy";
$result = mysql_query($query) or die("SQL Error 7-3: " . mysql_error());

$query = "DELETE FROM fact_final WHERE cliente_id = ''";
$result = mysql_query($query) or die("SQL Error 7-4: " . mysql_error());

$query = "UPDATE fact_final SET vendedor_codigo = 'JF' WHERE vendedor_codigo = ''";
$result = mysql_query($query) or die("SQL Error 7-5: " . mysql_error());

$query = "UPDATE fact_final SET orden_compra = interno WHERE orden_compra = ''";
$result = mysql_query($query) or die("SQL Error 7-6: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "8º Fact Movs";
echo "<br/>";

// Fact Movs
$query = "INSERT INTO fact_movs (id, codigo, cantidad, `desc`, precio, interno, orden_compra, factura, remision, cliente_id) 
SELECT Id, Codigo, ABS(Cantidad), `Desc`, Unitario, Remisión, Remision2, Factura, `Orden de Compra`, Id_tercero FROM fact_movs_copy";
$result = mysql_query($query) or die("SQL Error 8-1: " . mysql_error());

$query = "DELETE FROM fact_movs WHERE interno = ''";
$result = mysql_query($query) or die("SQL Error 8-2: " . mysql_error());

$query = "DELETE FROM fact_movs WHERE cantidad = '0'";
$result = mysql_query($query) or die("SQL Error 8-3: " . mysql_error());

// Fix Orden_Compra
$query = "SELECT interno, orden_compra FROM fact_final";
$result = mysql_query($query) or die("SQL Error 8-4: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		$query = "UPDATE fact_movs SET orden_compra = '".$row["orden_compra"]."' WHERE interno = '".$row["interno"]."' ";
		$result1 = mysql_query($query) or die("SQL Error 8-5: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";
echo "9º Inventario";
echo "<br/>";

// Inventario
$query = "INSERT INTO inventario_movs (cod_fab, cantidad, costo, viejo_costo, costo_promedio, existencia, interno, motivo, fecha, 
tipo, observacion) SELECT Cod, ABS(Mov), Vrunitario, Costoul, Costop, IF(Existencia > 0, Existencia, '0'), Documento, Motivo, 
Fecha, IF(Mov > 0, 'Entrada', 'Salida'), Fpago FROM movimientosf WHERE Estado = 'Aprobado'";
$result = mysql_query($query) or die("SQL Error 9-1: " . mysql_error());

echo "* Borrar Movimientos en 0<br/>";

$query = "DELETE FROM inventario_movs WHERE cantidad = '0'";
$result = mysql_query($query) or die("SQL Error 9-2: " . mysql_error());

echo "* Actualizar Existencias -> Sumar y restar cantidades para obtener la existencia real.<br/>";

$query = "SELECT DISTINCT cod_fab FROM inventario_movs";
$result = mysql_query($query) or die("SQL Error 9-3: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while ($row = mysql_fetch_array($result))
	{
		$Entrada = 0;
		$Salida = 0;
		$query = "SELECT SUM(cantidad) AS cantidad FROM inventario_movs WHERE cod_fab = '".$row["cod_fab"]."' AND tipo = 'Entrada'";
		$result1 = mysql_query($query) or die("SQL Error 9-4: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			$Entrada = $row1["cantidad"];
		}
		
		$query = "SELECT SUM(cantidad) AS cantidad FROM inventario_movs WHERE cod_fab = '".$row["cod_fab"]."' AND tipo = 'Salida'";
		$result1 = mysql_query($query) or die("SQL Error 9-5: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			$Salida = $row1["cantidad"];
		}
		
		$Total = $Entrada - $Salida;
		if ($Total < 0)
			$Total = 0;
		
		$query = "UPDATE productos SET existencia = '".$Total."' WHERE cod_fab = '".$row["cod_fab"]."'";
		$result1 = mysql_query($query) or die("SQL Error 9-6: " . mysql_error());
	}
}

// Motivos Fix
$query = "UPDATE inventario_movs SET motivo = 'Inicial' WHERE motivo = 'Inventario inicial'";
$result = mysql_query($query) or die("SQL Error 9-7: " . mysql_error());

$query = "UPDATE inventario_movs SET motivo = 'Produccion' WHERE motivo = 'Producción'";
$result = mysql_query($query) or die("SQL Error 9-8: " . mysql_error());

$query = "UPDATE inventario_movs SET motivo = 'Ajuste' WHERE motivo = 'Ajuste de inventario'";
$result = mysql_query($query) or die("SQL Error 9-9: " . mysql_error());

$query = "UPDATE inventario_movs SET motivo = 'Produccion' WHERE motivo = 'Prod. Figurado'";
$result = mysql_query($query) or die("SQL Error 9-10: " . mysql_error());

$query = "UPDATE inventario_movs SET motivo = 'Despunte' WHERE motivo = 'Despunte de producción'";
$result = mysql_query($query) or die("SQL Error 9-11: " . mysql_error());

echo "* Arreglar Existencias del Momento en los Movimientos.<br/>";

$query = "SELECT * FROM inventario_movs WHERE cod_fab != '' AND motivo = 'Inicial' GROUP BY cod_fab";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());
if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$Inicial[$row["cod_fab"]] = $row["cantidad"];
	}
}

$query = "SELECT * FROM inventario_movs ORDER BY cod_fab, fecha ASC";
$result = mysql_query($query) or die("SQL Error 1: " . mysql_error());

if (mysql_num_rows($result) > 0)
{
	$LastCode = "_empty_";
	$Existencia = 0;
	while($row = mysql_fetch_array($result))
	{
		if ($LastCode != $row["cod_fab"])
		{
			$LastCode = $row["cod_fab"];
			$Existencia = isset($Inicial[$row["cod_fab"]]) ? $Inicial[$row["cod_fab"]]:0;
			
			if ($row["motivo"] != "Inicial")
			{
				$query = "UPDATE inventario_movs SET existencia = '".$Existencia."' WHERE id = '".$row["id"]."'";
				$result1 = mysql_query($query) or die("SQL Error 2-1: " . mysql_error());
			}
		}
		else
		{
			$query = "UPDATE inventario_movs SET existencia = '".$Existencia."' WHERE id = '".$row["id"]."'";
			$result1 = mysql_query($query) or die("SQL Error 2-2: " . mysql_error());
			
			if ($row["tipo"] == "Entrada" && $row["motivo"] != "Inicial")
				$Existencia += $row["cantidad"];
			else if ($row["tipo"] == "Salida" && $row["motivo"] != "Inicial")
				$Existencia -= $row["cantidad"];
		}
	}
}

echo "- Finalizado<br/><br/>";
echo "10º Maquinaria Final";
echo "<br/>";

// Maquinaria Final
$query = "INSERT INTO maquinaria_final (id, ord_reparacion, fecha_ini, fecha_fin, clasificacion, tipo, motivo, diagnostico, total, causa, 
procedimiento, proveedor1, total1, proveedor2, total2, proveedor3, total3, estado, digitado_por, fecha_digitado, aprobado_por, fecha_aprobado, 
modificado_por, fecha_modificado) SELECT Id, orden, fechai, fechaf, clasificacion, maquina, motivo, diagnostico, ABS(TOTAL), descripcion, 
proceso, P1, total1, P2, total2, P3, total3, Estado, digito, fechadi, aprobo, fechaap, modifico, fechamo FROM maqs_final1";
$result = mysql_query($query) or die("SQL Error 10: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "11º Maquinaria Movs";
echo "<br/>";
echo "* Proveedor 1<br/>";

// Maquinaria Movs (Proveedor 1)
$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor) SELECT maqs_final.orden, maqs_final.codigo, 
maqs_final.cantidad, maqs_final.unitario, maqs_final1.P1 FROM maqs_final LEFT JOIN maqs_final1 ON maqs_final1.orden = maqs_final.orden 
WHERE maqs_final.proveedor = 'prov1'";
$result = mysql_query($query) or die("SQL Error 11-1: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "* Proveedor 2<br/>";

// Maquinaria Movs (Proveedor 2)
$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor) SELECT maqs_final.orden, maqs_final.codigo, 
maqs_final.cantidad, maqs_final.unitario, maqs_final1.P2 FROM maqs_final LEFT JOIN maqs_final1 ON maqs_final1.orden = maqs_final.orden 
WHERE maqs_final.proveedor = 'prov2'";
$result = mysql_query($query) or die("SQL Error 11-2: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "* Proveedor 3<br/>";

// Maquinaria Movs (Proveedor 3)
$query = "INSERT INTO maquinaria_movs (ord_reparacion, codigo, cantidad, unitario, proveedor) SELECT maqs_final.orden, maqs_final.codigo, 
maqs_final.cantidad, maqs_final.unitario, maqs_final1.P3 FROM maqs_final LEFT JOIN maqs_final1 ON maqs_final1.orden = maqs_final.orden 
WHERE maqs_final.proveedor = 'prov3'";
$result = mysql_query($query) or die("SQL Error 11-3: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "12º Mov Clientes";
echo "<br/>";
echo "* Actualizar debito y credito sin acentos<br/>";

// Mov Clientes
$query = "UPDATE mov_clientes_copy SET Tipo_movimiento = 'Debito' WHERE Tipo_movimiento = 'Débito'";
$result = mysql_query($query) or die("SQL Error 12-1: " . mysql_error());

$query = "UPDATE mov_clientes_copy SET Tipo_movimiento = 'Credito' WHERE Tipo_movimiento = 'Crédito'";
$result = mysql_query($query) or die("SQL Error 12-2: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "* Insertar Datos<br/>";

$query = "INSERT INTO mov_clientes (cliente_id, tipo_movimiento, valor, saldo, interno, orden_compra, remision, factura, fecha, estado, 
digitado_por, aprobado_por, vendedor_codigo, cobrador_codigo, caja_interno, caja_recibo) SELECT Client_id, Tipo_movimiento, ABS(Valor), 
ABS(Saldo), Doc_afectado, Ordendec, Remisiones, Temp1, Fecha_mov, Estado, Digitó, Aprobó, Vendedor, Cobrador, RC, RC1 FROM mov_clientes_copy";
$result = mysql_query($query) or die("SQL Error 12-3: " . mysql_error());

$query = "SELECT interno FROM mov_clientes WHERE orden_compra = ''";
$result = mysql_query($query) or die("SQL Error 12-4: " . mysql_error());

while ($row = mysql_fetch_array($result))
{
	$query = "SELECT orden_compra FROM fact_final WHERE interno = '".$row["interno"]."'";
	$result1 = mysql_query($query) or die("SQL Error 12-5: " . mysql_error());
	if (mysql_num_rows($result1) > 0)
	{
		$row1 = mysql_fetch_array($result1);
		$query = "UPDATE mov_clientes SET orden_compra = '".$row1["orden_compra"]."' WHERE interno = '".$row["interno"]."'";
		$result2 = mysql_query($query) or die("SQL Error 12-6: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";
echo "13º Produccion Final";
echo "<br/>";
echo "* Operario de Trefilado<br/>";

// Produccion Final
// REVISAR
// Operario de Trefilado
$query = "INSERT INTO produccion_final (id, orden_produccion, solicitud, orden_compra, interno, destino, cliente_id, fecha, estado, 
operario_trefilado, digitado_por, aprobado_por, anulado_por, motivo_anulado, fecha_anulado, modificado_por, fecha_modificado) 
SELECT produccionf.Id, produccionf.Op, produccionf.Grupo, produccionf.Oc, produccionf.Interno, produccionf.Destino, produccionf.Idcliente, 
produccionf.Fini, produccionf.P1o, clientes_copy.Cedula, produccionf.Digito, produccionf.Aprobo, produccionf.Anula, 
produccionf.anula_concepto, produccionf.FAnula, produccionf.Modifico, produccionf.fmod FROM produccionf LEFT JOIN clientes_copy ON 
clientes_copy.Nombre = produccionf.P1op1 WHERE produccionf.P1op1 IS NOT NULL";
$result = mysql_query($query) or die("SQL Error 13-1: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "* Operario de Corte y Enderezado<br/>";

// Operario de Corte y Enderezado
$query = "SELECT produccionf.*, clientes_copy.Cedula FROM produccionf LEFT JOIN clientes_copy ON 
clientes_copy.Nombre = produccionf.P1op2 WHERE produccionf.P1op2 IS NOT NULL";
$result = mysql_query($query) or die("SQL Error 13-2: " . mysql_error());

while ($row = mysql_fetch_array($result))
{
	$query = "SELECT id FROM produccion_final WHERE id = '".$row["Id"]."'";
	$result1 = mysql_query($query) or die("SQL Error 13-3: " . mysql_error());
	if (mysql_num_rows($result1) > 0)
	{
		$query = "UPDATE produccion_final SET operario_enderezado = '".$row["Cedula"]."' WHERE id = '".$row["Id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 13-4: " . mysql_error());
	}
	else
	{
		$query = "INSERT INTO produccion_final (id, orden_produccion, solicitud, orden_compra, interno, destino, cliente_id, fecha, 
		estado, operario_enderezado, digitado_por, aprobado_por, motivo_anulado, modificado_por, fecha_modificado) VALUES 
		('".$row["Id"]."', '".$row["Op"]."', '".$row["Grupo"]."', '".$row["Oc"]."', '".$row["Interno"]."', '".$row["Destino"]."', 
		'".$row["Idcliente"]."', '".$row["Fini"]."', '".$row["P1o"]."', '".$row["Cedula"]."', '".$row["Digito"]."', '".$row["Aprobo"]."', 
		'".$row["anula_concepto"]."', '".$row["Modifico"]."', '".$row["fmod"]."')";
		$result1 = mysql_query($query) or die("SQL Error 13-5: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";
echo "* Operario de Electrosoldado<br/>";

// Operario de Electrosoldado
$query = "SELECT produccionf.*, clientes_copy.Cedula FROM produccionf LEFT JOIN clientes_copy ON 
clientes_copy.Nombre = produccionf.P1op4 WHERE produccionf.P1op4 IS NOT NULL";
$result = mysql_query($query) or die("SQL Error 13-6: " . mysql_error());

while ($row = mysql_fetch_array($result))
{
	$query = "SELECT id FROM produccion_final WHERE id = '".$row["Id"]."'";
	$result1 = mysql_query($query) or die("SQL Error 13-7: " . mysql_error());
	if (mysql_num_rows($result1) > 0)
	{
		$query = "UPDATE produccion_final SET operario_electrosoldado = '".$row["Cedula"]."' WHERE id = '".$row["Id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 13-8: " . mysql_error());
	}
	else
	{
		$query = "INSERT INTO produccion_final (id, orden_produccion, solicitud, orden_compra, interno, destino, cliente_id, fecha, 
		estado, operario_electrosoldado, digitado_por, aprobado_por, motivo_anulado, modificado_por, fecha_modificado) VALUES 
		('".$row["Id"]."', '".$row["Op"]."', '".$row["Grupo"]."', '".$row["Oc"]."', '".$row["Interno"]."', '".$row["Destino"]."', 
		'".$row["Idcliente"]."', '".$row["Fini"]."', '".$row["P1o"]."', '".$row["Cedula"]."', '".$row["Digito"]."', '".$row["Aprobo"]."', 
		'".$row["anula_concepto"]."', '".$row["Modifico"]."', '".$row["fmod"]."')";
		$result1 = mysql_query($query) or die("SQL Error 13-9: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";
echo "* Operario de Figurado<br/>";

// Operario de Figurado
$query = "SELECT produccionf.*, clientes_copy.Cedula FROM produccionf LEFT JOIN clientes_copy ON 
clientes_copy.Nombre = produccionf.P1op5 WHERE produccionf.P1op5 IS NOT NULL";
$result = mysql_query($query) or die("SQL Error 13-10: " . mysql_error());

while ($row = mysql_fetch_array($result))
{
	$query = "SELECT id FROM produccion_final WHERE id = '".$row["Id"]."'";
	$result1 = mysql_query($query) or die("SQL Error 13-11: " . mysql_error());
	if (mysql_num_rows($result1) > 0)
	{
		$query = "UPDATE produccion_final SET operario_figurado = '".$row["Cedula"]."' WHERE id = '".$row["Id"]."'";
		$result1 = mysql_query($query) or die("SQL Error 13-12: " . mysql_error());
	}
	else
	{
		$query = "INSERT INTO produccion_final (id, orden_produccion, solicitud, orden_compra, interno, destino, cliente_id, fecha, 
		estado, operario_figurado, digitado_por, aprobado_por, motivo_anulado, modificado_por, fecha_modificado) VALUES 
		('".$row["Id"]."', '".$row["Op"]."', '".$row["Grupo"]."', '".$row["Oc"]."', '".$row["Interno"]."', '".$row["Destino"]."', 
		'".$row["Idcliente"]."', '".$row["Fini"]."', '".$row["P1o"]."', '".$row["Cedula"]."', '".$row["Digito"]."', '".$row["Aprobo"]."', 
		'".$row["anula_concepto"]."', '".$row["Modifico"]."', '".$row["fmod"]."')";
		$result1 = mysql_query($query) or die("SQL Error 13-13: " . mysql_error());
	}
}

echo "- Finalizado<br/><br/>";
echo "14º Produccion Movs";
echo "<br/>";

// Produccion Movs
$query = "INSERT INTO produccion_movs (codigo, cantidad, tipo, origen, destino, orden_produccion, orden_compra, interno, cliente_id) SELECT 
produccion2r.P1ocod, produccion2r.P1ocant, produccion2r.Tipo, produccion2r.Origen, produccion2r.Destino, produccion2r.Op, produccionf.Oc, 
produccionf.Interno, produccion2r.Idcliente FROM produccion2r LEFT JOIN produccionf on produccionf.Op = produccion2r.Op";
$result = mysql_query($query) or die("SQL Error 14: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "15º Produccion Procesos";
echo "<br/>";
echo "* Proceso de Trefilado<br/>";

// Produccion Procesos (Trefialdo)
$query = "INSERT INTO produccion_proc (orden_produccion, solicitud, estado, proceso, rendimiento, avance, observaciones, fecha_ini, finalizado_por, 
fecha_fin) SELECT producciont.Op, produccionf.Grupo, producciont.Estado, 'Trefilado', producciont.tdiferencia, producciont.Avance, producciont.Obs, 
producciont.Fini, producciont.Finalizo, producciont.Ffin FROM producciont LEFT JOIN produccionf ON produccionf.Op = producciont.Op WHERE 
producciont.Op IS NOT NULL";
$result = mysql_query($query) or die("SQL Error 15-1: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "* Proceso de Corte y Enderezado<br/>";

// Produccion Procesos (Corte y Enderezado)
$query = "INSERT INTO produccion_proc (orden_produccion, solicitud, estado, proceso, rendimiento, avance, observaciones, fecha_ini, finalizado_por, 
fecha_fin) SELECT produccione.Op, produccionf.Grupo, produccione.Estado, 'Corte y Enderezado', produccione.tdiferencia, produccione.Avance, 
produccione.Obs, produccione.Fini, produccione.Finalizo, produccione.Ffin FROM produccione LEFT JOIN produccionf ON 
produccionf.Op = produccione.Op WHERE produccione.Op IS NOT NULL";
$result = mysql_query($query) or die("SQL Error 15-2: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "* Proceso de Electrosoldado<br/>";

// Produccion Procesos (Electrosoldado)
$query = "INSERT INTO produccion_proc (orden_produccion, solicitud, estado, proceso, rendimiento, avance, observaciones, fecha_ini, finalizado_por, 
fecha_fin) SELECT produccions.Op, produccionf.Grupo, produccions.Estado, 'Electrosoldado', produccions.tdiferencia, produccions.Avance, 
produccions.Obs, produccions.Fini, produccions.Finalizo, produccions.Ffin FROM produccions LEFT JOIN produccionf ON 
produccionf.Op = produccions.Op WHERE produccions.Op IS NOT NULL";
$result = mysql_query($query) or die("SQL Error 15-3: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "* Proceso de Figurado<br/>";

// Produccion Procesos (Figurado)
$query = "INSERT INTO produccion_proc (orden_produccion, solicitud, estado, proceso, rendimiento, avance, observaciones, fecha_ini, finalizado_por, 
fecha_fin) SELECT produccionfg.Op, produccionf.Grupo, produccionfg.Estado, 'Figurado', produccionfg.tdiferencia, produccionfg.Avance, 
produccionfg.Obs, produccionfg.Fini, produccionfg.Finalizo, produccionfg.Ffin FROM produccionfg LEFT JOIN produccionf ON 
produccionf.Op = produccionfg.Op WHERE produccionfg.Op IS NOT NULL";
$result = mysql_query($query) or die("SQL Error 15-4: " . mysql_error());

echo "- Finalizado<br/><br/>";
echo "16º Produccion Procesos Movimientos";
echo "<br/>";

$query = "UPDATE produccion_proc SET avance = '100.00' WHERE avance = '1.00'";
$result = mysql_query($query) or die("SQL Error 15-5: " . mysql_error());

// Produccion Procesos Movimientos

$query = "INSERT INTO produccion_proc_movs (codigo, cantidad, tipo, origen, destino, maquinaria, operario, orden_produccion, cliente_id) 
SELECT P1ocod, P1ocant, Tipo, Destino, Origen, Maquinaria, Operario, Op, Idcliente FROM produccion2r WHERE Tipo = 'Obtener'";
$result = mysql_query($query) or die("SQL Error 16-1: " . mysql_error());

$query = "INSERT INTO produccion_proc_movs (codigo, cantidad, tipo, origen, destino, maquinaria, operario, orden_produccion, cliente_id) 
SELECT P1ocod, P1ocant, Tipo, Origen, Destino, Maquinaria, Operario, Op, Idcliente FROM produccion2r WHERE Tipo != 'Obtener'";
$result = mysql_query($query) or die("SQL Error 16-2: " . mysql_error());

echo "* Actualizar el operario para que use su ID y no el nombre.<br/>";

// Usar cliente_id en vez de nombre
$query = "SELECT DISTINCT operario FROM produccion_proc_movs WHERE operario != ''";
$result = mysql_query($query) or die("SQL Error 16-3: " . mysql_error());

if (mysql_num_rows($result) > 0)
{
	while($row = mysql_fetch_array($result))
	{
		$query = "SELECT cliente_id FROM clientes WHERE nombre = '".$row[0]."'";
		$result1 = mysql_query($query) or die("SQL Error 16-4: " . mysql_error());
		if (mysql_num_rows($result1) > 0)
		{
			$row1 = mysql_fetch_array($result1);
			$query = "UPDATE produccion_proc_movs SET operario = '".$row1[0]."' WHERE operario = '".$row[0]."'";
			$result2 = mysql_query($query) or die("SQL Error 16-5: " . mysql_error());
		}
	}
}

echo "- Finalizado<br/><br/>";

echo "<br />Tiempo Final -> ".time()." <br/> Tiempo Transcurrido -> ".(time() - $now)." ";
?>