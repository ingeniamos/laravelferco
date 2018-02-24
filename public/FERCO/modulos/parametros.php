<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
include('config.php');

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

if (isset($_GET["Chofer"]))
{
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	$query = "SELECT DISTINCT cliente_id FROM contratos WHERE charge = 'Conductor' AND active = 'true'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #2: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$data[] = array(
			'Chofer' => "Externo",
			'ClienteID' => "000000000-0",
		);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$newdata[] = array(
				'Chofer' => $ClienteNombre[$row["cliente_id"]],
				'ClienteID' => $row["cliente_id"],
			);
		}
		sort($newdata);
		echo json_encode(array_merge($data, $newdata));
	}
	else
	{
		$data[] = array(
			'Chofer' => "Externo",
			'ClienteID' => "000000000-0",
		);
		echo json_encode($data);
	}
}
else if (isset($_GET['Vehiculo']))
{
	$query = "SELECT * FROM par_fac_vehiculo";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Placa' => $row['placa'],
				'Modelo' => $row['modelo'],
				'Tipo' => $row['tipo']
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['Ruta']))
{
	$query = "SELECT * FROM par_fac_ruta";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Barrio' => $row['barrio'],
			'Ruta' => $row['ruta']
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['OtroSrv']))
{
	$query = "SELECT * FROM par_fac_otr_ser ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Nombre' => $row['nombre'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['TipoDcto']))
{
	$query = "SELECT * FROM par_fac_tipo_dcto ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ID = $row['id'];
			switch($row['tipo_descuento'])
			{
				case "Descuento por IVA":
					$ID = 0;
				break;
				
				default:
				break;
			}
			$data[] = array(
				'ID' => $ID,
				'Nombre' => $row['tipo_descuento'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['MotivoAnulacion']))
{
	$query = "SELECT * FROM par_fac_anula ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Motivo' => $row['concepto'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Venta']))
{
	$query = "SELECT nombre, cliente_id FROM clientes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ClienteNombre[$row["cliente_id"]] = $row["nombre"];
		}
	}
	
	//$query = "SELECT codigo, vendedor FROM par_ter_vend ";
	$query = "SELECT user_id, user_code FROM login WHERE active = 'true' AND user_lvl = 'Vendedor' OR user_lvl = 'Administrador'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if (isset($ClienteNombre[$row["user_id"]]))
		{
			$data[] = array(
				'Vendedor' => $ClienteNombre[$row["user_id"]],
				'Codigo' => $row['user_code'],
			);
		}
	}
	sort($data);
	echo json_encode($data);
}
else if (isset($_GET['Garantias']))
{
	$query = "SELECT * FROM par_ter_gara ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Apply' => false,
			'ID' => $row['id'],
			'Garantia' => $row['garantia'],
			'Imagen' => "",
			'Ok' => false,
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Clasificacion']))
{
	$query = "SELECT * FROM par_ter_clasificacion ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Clasificacion' => $row['clasificacion'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Tipo']))
{
	$query = "SELECT * FROM par_ter_tipo WHERE clasificacion = '".$_GET['Terceros_Tipo']."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Tipo' => $row['tipo'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Tipo']))
{
	$query = "SELECT * FROM par_ter_tipo ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Clasificacion' => $row['clasificacion'],
			'Tipo' => $row['tipo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Grupo']))
{
	$query = "SELECT * FROM par_ter_grupo WHERE clasificacion = '".$_GET['Terceros_Grupo']."' ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Grupo' => $row['grupo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Documento']))
{
	$query = "SELECT * FROM par_ter_tipo_doc ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$ID = $row['id'];
		switch($row['tipo_doc'])
		{
			case "Cédula":
			case "NIT":
			case "rut":
			case "tarjeta de identidad":
			case "Cédula de extranjería":
				$ID = 0;
			break;
			
			default:
			break;
		}
		$data[] = array(
			'ID' => $ID,
			'Documento' => $row['tipo_doc'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Tipo_Doc']))
{
	$query = "SELECT * FROM par_ter_tipo_sociedad ORDER BY tipo_sociedad ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$ID = $row['id'];
		switch($row['tipo_sociedad'])
		{
			case "Persona Jurídica":
			case "Persona Natural":
			case "RUT":
			case "T. I.":
				$ID = 0;
			break;
			
			default:
			break;
		}
		$data[] = array(
			'ID' => $ID,
			'Tipo_Sociedad' => $row['tipo_sociedad'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Barrio']))
{
	$query = "SELECT * FROM par_ter_barrio ORDER BY barrio ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Barrio' => $row['barrio'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Ciudad']))
{
	$query = "SELECT ciudad FROM par_ter_ciudad ORDER BY ciudad ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Ciudad' => $row['ciudad'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Departamento']))
{
	$query = "SELECT * FROM par_ter_ciudad_dep ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Departamento' => $row['departamento'],
			'Ciudad' => $row['ciudad'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Terceros_Depart_Pais']))
{
	$query = "SELECT par_ter_ciudad_dep.departamento, par_ter_departamento_pais.pais FROM par_ter_ciudad_dep 
	LEFT JOIN par_ter_departamento_pais ON par_ter_ciudad_dep.departamento = par_ter_departamento_pais.departamento 
	WHERE par_ter_ciudad_dep.ciudad = '".$_GET['Terceros_Depart_Pais']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Departamento' => $row['departamento'],
			'Pais' => $row['pais'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Motivo_Visita']))
{
	$query = "SELECT * FROM par_ter_motivo";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Motivo' => $row['motivo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Novedad_Visita']))
{
	$query = "SELECT * FROM par_ter_novedad";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Novedad' => $row['novedad'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Inventario_Categoria']))
{
	$query = "SELECT * FROM par_inv_cat ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$ID = $row['id'];
		switch($row['categoria'])
		{
			case "Hierro":
				$ID = 0;
			break;
			
			default:
			break;
		}
		$data[] = array(
			'ID' => $ID,
			'Categoria' => $row['categoria'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Inventario_Grupo']))
{
	$query = "SELECT * FROM par_inv_gr WHERE categoria = '".$_GET['Inventario_Grupo']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$ID = $row['id'];
		switch($row['grupo'])
		{
			case "despunte":
				$ID = 0;
			break;
			
			default:
			break;
		}
		$data[] = array(
			'ID' => $ID,
			'Grupo' => $row['grupo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Grupo']))
{
	$query = "SELECT grupo, categoria FROM par_inv_gr";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Grupo' => $row['grupo'],
			'Categoria' => $row['categoria'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Inventario_SubGrupo']))
{
	$query = "SELECT * FROM par_inv_subgr WHERE grupo = '".$_GET['Inventario_SubGrupo']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'SubGrupo' => $row['subgrupo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['SubGrupo']))
{
	$query = "SELECT grupo, subgrupo FROM par_inv_subgr";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'SubGrupo' => $row['subgrupo'],
			'Grupo' => $row['grupo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Inventario_Unidad']))
{
	$query = "SELECT * FROM par_inv_und ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Unidad' => $row['unidad'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Caja_Categoria']))
{
	$query = "SELECT * FROM par_caja_cat ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Categoria' => $row['categoria'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['Caja_Grupo']))
{
	$query = "SELECT * FROM par_caja_gr WHERE categoria = '".$_GET['Caja_Grupo']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ID = $row['id'];
			switch($row['grupo'])
			{
				case "Cliente"://Ingresos
				case "bancos"://Egresos
				case "Inventarios"://Egresos
				case "Maquinaria"://Egresos
				case "Nómina"://Egresos
				case "Vehículos"://Egresos
				case "Ajuste"://Egresos
					$ID = 0;
				break;
				
				default:
				break;
			}
			$data[] = array(
				'ID' => $ID,
				'Grupo' => $row['grupo'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['Caja_SubGrupo']))
{
	$query = "SELECT * FROM par_caja_subgr WHERE grupo = '".$_GET['Caja_SubGrupo']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ID = $row['id'];
			switch($row['subgrupo'])
			{
				case "Cartera"://Ingresos->Cliente
				case "Pago de contado"://Ingresos->Cliente
				case "bancos"://Egresos->bancos
				case "Inventarios"://Egresos->Inventarios
				case "Anticipo"://Egresos->Nómina
				case "Donacion"://Egresos->Nómina
				case "Libranza"://Egresos->Nómina
				case "Multas"://Egresos->Nómina
				case "Préstamo"://Egresos->Nómina
				case "Ajuste"://Egresos->Ajuste
					$ID = 0;
				break;
				
				default:
					if (stristr($row['subgrupo'], "Reparac"))//Egresos->Maquinaria
						$ID = 0;
				break;
			}
			$data[] = array(
				'ID' => $ID,
				'SubGrupo' => $row['subgrupo'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['Caja_SubGrupo2']))
{
	$query = "SELECT * FROM par_caja_subgr2 WHERE grupo = '".$_GET['Caja_SubGrupo2']."'";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ID = $row['id'];
			switch($row['subgrupo2'])
			{
				case "N/A"://Multiple
				case "Abono cheque devuelto"://Ingresos->Cliente
				case "Pago cheque devuelto"://Ingresos->Cliente
				case "Deposito"://Egresos->bancos
				case "Inventarios"://Egresos->Inventarios
				case "1ra. Quincena"://Egresos->Nómina
				case "2da. Quincena"://Egresos->Nómina
				case "vacaciones"://Egresos->Nómina
					$ID = 0;
				break;
				
				default:
				break;
			}
			$data[] = array(
				'ID' => $ID,
				'SubGrupo2' => $row['subgrupo2'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['Diagnostico']))
{
	$query = "SELECT * FROM par_maq_estado";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Tipo' => $row['estado'],
			);
		}
	}
	else
	{
		$data[] = array();
	}
	echo json_encode($data);
}
else if (isset($_GET['Caja_Dcto']))
{
	$query = "SELECT * FROM par_caja_dcto";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Dcto' => $row['descuentos'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Caja_Tipo']))
{
	$query = "SELECT tipo FROM par_caja_tipo";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'Tipo' => $row['tipo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Caja_Banco']))
{
	$query = "SELECT * FROM par_caja_bancos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Banco' => $row['banco'],
				'Cuenta' => $row['cuenta'],
			);
		}
	}
	else
		$data[] = array();
	
	echo json_encode($data);
}
else if (isset($_GET['Caja_Numero_Bancos']))
{
	$query = "SELECT * FROM par_caja_bancos_numero ORDER BY numero ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Banco' => $row['banco'],
				'Numero' => $row['numero'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Nomina_Salario']))
{
	$query = "SELECT * FROM par_nom";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Nombre' => $row['nombre'],
			'Valor' => $row['valor'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Retenciones']))
{
	$query = "SELECT * FROM par_nom_retencion";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Salario' => $row['salario'],
			'Valor' => $row['valor'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Cargo']))
{
	$query = "SELECT * FROM par_nom_cargos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ID = $row['id'];
			switch($row['cargo'])
			{
				case "Conductor":
					$ID = 0;
				break;
				
				default:
				break;
			}
			$data[] = array(
				'ID' => $ID,
				'Cargo' => $row['cargo'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['Nomina_Salud']))
{
	$query = "SELECT * FROM par_nom_salud";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Salud' => $row['nombre'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Pension']))
{
	$query = "SELECT * FROM par_nom_pension";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Pension' => $row['nombre'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Cesantia']))
{
	$query = "SELECT * FROM par_nom_cesantia";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Cesantia' => $row['nombre'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Contrato']))
{
	$query = "SELECT * FROM par_nom_contrato";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Contrato' => $row['nombre'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Justificacion'])){
	$query = "SELECT * FROM par_nom_ext";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Tipo' => $row['tipo'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Justificacion2'])){
	$query = "SELECT * FROM par_nom_justificacion";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Tipo' => $row['justificacion'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Horario']))
{	
	$query = "SELECT * FROM par_nom_horario";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if (isset($_GET['Formatted']) && $_GET['Formatted'] == true)
		{
			$Ini = explode(":", $row['hora_ini'], 3);
			$Fin = explode(":", $row['hora_fin'], 3);
			
			$Hora_Ini = "2000, 01, 01, ".$Ini[0].":".$Ini[1].":".$Ini[2]."";
			$Hora_Fin = "2000, 01, 01, ".$Fin[0].":".$Fin[1].":".$Fin[2]."";
			
			$data[] = array(
				'ID' => $row['id'],
				'Tipo' => $row['tipo'],
				'Hora_Ini' => $Hora_Ini,
				'Hora_Fin' => $Hora_Fin,
			);
		}
		else
		{
			$data[] = array(
				'ID' => $row['id'],
				'Tipo' => $row['tipo'],
				'Hora_Ini' => $row['hora_ini'],
				'Hora_Fin' => $row['hora_fin'],
			);
		}
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Festivos']))
{
	$query = "SELECT * FROM par_nom_fest";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if (isset($_GET['Formatted']) && $_GET['Formatted'] == true)
		{
			$data[] = array(
				'ID' => $row['id'],
				'Nombre' => $row['nombre'],
				'Fecha' => "".$row['fecha']." 00:00:00",
			);
		}
		else
		{
			$data[] = array(
				'ID' => $row['id'],
				'Nombre' => $row['nombre'],
				'Fecha' => $row['fecha'],
			);

		}
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Prestamos']))
{
	$query = "SELECT * FROM par_nom_prestamos";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Tipo' => $row['tipo'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['Nomina_Novedades']))
{
	$query = "SELECT * FROM par_nom_nov";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Novedad' => $row['novedad'],
			'Descontable' => $row['descontable'],
			'Remunerado100' => $row['remunerado1'],
			'Remunerado66' => $row['remunerado2'],
			'Cesantia' => $row['cesantia'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Nomina_Reposicion']))
{
	$query = "SELECT * FROM par_nom_reposicion";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ID = $row['id'];
			switch($row['reposicion'])
			{
				case "Reponer tiempo":
				case "Canje x hora extra":
					$ID = 0;
				break;
				
				default:
				break;
			}
			$data[] = array(
				'ID' => $ID,
				'Reposicion' => $row['reposicion'],
				'Reponer' => $row['reponer'],
			);
		}
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET["Nomina_Carnet"]))
{
	$query = "SELECT * FROM par_nom_carnet";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		
		$data[] = array(
			"ID" => $row["id"],
			"DESC1" => $row["desc1"],
			"DESC2" => $row["desc2"],
		);
		
		echo json_encode($data);
	}
	else
	{
		echo json_encode(array());
	}
}
else if (isset($_GET['Presupuesto_Unidad']))
{
	$query = "SELECT * FROM par_presupuesto_unidad ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Unidad' => $row['unidad'],
		);
	}
	echo json_encode($data);
}
else if (isset($_GET['Presupuesto_Clasificacion']))
{
	$query = "SELECT * FROM apu_clasificacion ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Clasificacion' => $row['clasificacion'],
			'Notas' => $row['nota'],
		);
	}
	/*$data[] = array(
		'ID' => 0,
		'Clasificacion' => "Titulo",
	);*/
	echo json_encode($data);
}
else if (isset($_GET['Presupuesto_Categoria']))
{
	$query = "SELECT * FROM par_presupuesto_categoria ";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Categoria' => $row['categoria'],
		);
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Presupuesto_Grupo']))
{
	$query = "SELECT * FROM `par_presupuesto_grupo_".$_GET['Clasificacion']."` 
	WHERE categoria = '".$_GET['Categoria']."' ORDER BY codigo ASC, grupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$data[] = array(
			'ID' => $row['id'],
			'Codigo' => $row['codigo'],
			'Grupo' => $row['grupo'],
		);
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Presupuesto_SubGrupo']))
{
	//Presupuesto_SubGrupo=true&Clasificacion=Originales&Categoria=APU&Grupo=10
	$query = "SELECT * FROM `par_presupuesto_subgrupo_".$_GET['P_Clasificacion']."` 
	WHERE grupo = '".$_GET['P_Grupo']."' AND categoria = '".$_GET['P_Categoria']."' ORDER BY codigo ASC, subgrupo ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Codigo' => $row['codigo'],
				'SubGrupo' => $row['subgrupo'],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET['Presupuesto_Conceptos']))
{
	$query = "SELECT * FROM par_presupuesto_conceptos ORDER BY concepto ASC";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				'ID' => $row['id'],
				'Concepto' => $row['concepto'],
				'Uso' => $row['uso'],
				'Valor' => $row['valor'],
				'Tipo' => $row['tipo'],
				'Fijo' => $row['fijo'],
			);
		}
		echo json_encode($data);
	}
}
else if (isset($_GET['Presupuesto_APU']))
{
	/*$query = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = 'ferco_new' AND table_name LIKE 'apu_mo_%'";
	$query = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = 'ferco_new' AND table_name LIKE 'apu_maq_%'";
	$query = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = 'ferco_new' AND table_name LIKE 'apu_%'";*/
	
	$Clasificacion = isset($_GET["Clasificacion"]) ? $_GET["Clasificacion"]:die();
	$APU_Only = isset($_GET["APU_Only"]) ? true:false;
	
	/*$query = "SELECT * FROM apu_clasificacion";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$Tmp[] = array(
				'Clasificacion' => $row['clasificacion'],
			);
		}
	}*/
	
	//foreach ($Tmp AS $item)
	//{
	//	$Clasificacion = $item["Clasificacion"];
		
		if ($APU_Only)
		{
			$query = "SELECT * FROM apu_".$Clasificacion." ORDER BY nombre ASC";
			$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
			if (mysql_num_rows($result) > 0)
			{
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
				{
					$data[] = array(
						'ID' => $row['id'],
						'Codigo' => $row['codigo'],
						'Nombre' => $row['nombre'],
						'Unidad' => $row['unidad'],
						'Uso' => 100,
						'Valor' => $row['valor'],
						'Tipo' => "APU",
					);
				}
			}
			if (isset($data))
				echo json_encode($data);
			else
				echo json_encode(array());
			die();
		}
		
		$query = "SELECT * FROM apu_equ_".$Clasificacion." ORDER BY nombre ASC";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$data[] = array(
					'ID' => $row['id'],
					'Codigo' => $row['codigo'],
					'Nombre' => $row['nombre'],
					'Unidad' => $row['unidad'],
					'Uso' => $row['uso'],
					'Valor' => $row['valor'],
					'Tipo' => "Equipos",
				);
			}
		}
		
		$query = "SELECT * FROM apu_mo_".$Clasificacion." ORDER BY nombre ASC";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$data[] = array(
					'ID' => $row['id'],
					'Codigo' => $row['codigo'],
					'Nombre' => $row['nombre'],
					'Unidad' => $row['unidad'],
					'Uso' => $row['uso'],
					'Valor' => $row['valor_scc'],
					'Tipo' => "Mano de Obra",
				);
			}
		}
		
		$query = "SELECT * FROM apu_mat_".$Clasificacion." ORDER BY nombre ASC";
		$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
		if (mysql_num_rows($result) > 0)
		{
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$data[] = array(
					'ID' => $row['id'],
					'Codigo' => $row['codigo'],
					'Nombre' => $row['nombre'],
					'Unidad' => $row['unidad'],
					'Uso' => $row['uso'],
					'Valor' => $row['valor'],
					'Tipo' => "Materiales",
				);
			}
		}
	//}
	
	echo json_encode($data);
}
else if (isset($_GET["Requerimientos_Prioridad"]))
{
	$query = "SELECT * FROM par_req_prioridad";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$ID = $row["id"];
			switch($row["prioridad"])
			{
				case "Normal":
					$ID = 0;
				break;
				
				default:
				break;
			}
			$data[] = array(
				"ID" => $ID,
				"Prioridad" => $row["prioridad"],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else if (isset($_GET["Maquinaria_Repuestos_Partes"]))
{
	$query = "SELECT * FROM repuestos_partes";
	$result = mysql_query($query) or die($DEBUG ? "SQL ERROR #1: ".mysql_error():"");
	if (mysql_num_rows($result) > 0)
	{
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$data[] = array(
				"ID" => $row["id"],
				"Parte" => $row["parte"],
			);
		}
	}
	if (isset($data))
		echo json_encode($data);
	else
		echo json_encode(array());
}
else
{
	//echo "asdasdasd";
}
?>