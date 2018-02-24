<?php
session_start();
$Interno = isset($_POST['Interno']) ? $_POST['Interno']:"";
$Ord_Compra = isset($_POST['Ord_Compra']) ? $_POST['Ord_Compra']:"";
$Estado = isset($_POST['Estado']) ? $_POST['Estado']:"";

	if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
	{
		$_SESSION["NumOfID"]++;
	}
$NumOfID = $_SESSION["NumOfID"];
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ClientesData = new Array();
	var ClientesData_MD5 = "";
	var cantidad_exedida = false;
	var ExistenciaCargada = false;
	var ID_Incorrecto = false;
	var Caja_Interno = "";
	var Caja_Recibo = "";
	var Cargar_Recibo = false;
	var ErrorDeuda = false;
	var ErrorSaldo = false;
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Ventas_Content");
	var Body = document.getElementById("Crear_Content");
	var Times = 0;
	var Refresh = 0;
	var Hide = 0;
	
	function CheckRefresh ()
	{
		clearInterval(Refresh);
		Refresh = setInterval(function()
		{
			if (Times <= 0 && (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none")) {
			//if (Times <= 0 && jQuery.css( Body, "display" ) === "none") {
				Times++;
			}
			//else if (Times > 0 && (jQuery.css( Body, "display" ) === "block" || jQuery.css( Main, "display" ) === "block"))
			else if (Times > 0 && jQuery.css( Body, "display" ) === "block" && jQuery.css( Main, "display" ) === "block")
			{
				//Code... do something?
				SystemMap("Crear", true);
				ReDefine();
				clearInterval(Refresh);
				CheckHide();
				CheckDataChanges();
			}
		},500);
	};
	
	function CheckHide ()
	{
		clearInterval(Hide);
		Hide = setInterval(function()
		{
			if (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none") {
			//if (jQuery.css( Body, "display" ) === "none") {
				CheckRefresh();
				clearInterval(Hide);
			}
		},200);
	};
	// END - Code for Refresh Data
	
	function SetData()
	{
		$("#crear_cliente<?php echo $NumOfID ?>").jqxComboBox({source: ClientesData})
		$("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox({source: ClientesData})
		$("#crear_beneficiario<?php echo $NumOfID ?>").jqxComboBox({source: ClientesData})
		$("#crear_beneficiario_ID<?php echo $NumOfID ?>").jqxComboBox({source: ClientesData})
	}
	
	function CheckDataChanges(NoChange)
	{
		if (ClientesData_MD5 != sessionStorage.getItem("GlobalClientesFullData_MD5"))
		{
			ClientesData = GlobalClientesData;
			ClientesData_MD5 = sessionStorage.getItem("GlobalClientesFullData_MD5");
			if (!NoChange)
				SetData();
		}
	}
	CheckDataChanges(true);
	
	function ClearDocument()
	{
		ReDefine();
		// Clean Variables
		cantidad_exedida = false;
		ExistenciaCargada = false;
		ID_Incorrecto = false;
		Caja_Interno = "";
		Caja_Recibo = "";
		Cargar_Recibo = false;
		ErrorDeuda = false;
		ErrorSaldo = false;
		// Clean Classes
		$("#crear_cupo_cr<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
		$("#crear_valor_rc<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
		
		var Classes = document.getElementById("crear_estado<?php echo $NumOfID ?>").className.split(/\s+/);
		for (i = 0; i < Classes.length; i ++)
		{
			switch (Classes[i])
			{
				case "GreenState":
					$("#crear_estado<?php echo $NumOfID ?>").removeClass("GreenState");
				break;
				case "RedState":
					$("#crear_estado<?php echo $NumOfID ?>").removeClass("RedState");
				break;
				case "VioletState":
					$("#crear_estado<?php echo $NumOfID ?>").removeClass("VioletState");
				break;
			}
		}
		var Classes = document.getElementById("crear_gar_docs<?php echo $NumOfID ?>").className.split(/\s+/);
		for (i = 0; i < Classes.length; i ++)
		{
			switch (Classes[i])
			{
				case "GreenState":
					$("#crear_gar_docs<?php echo $NumOfID ?>").removeClass("GreenState");
				break;
				case "OrangeState":
					$("#crear_gar_docs<?php echo $NumOfID ?>").removeClass("OrangeState");
				break;
			}
		}
		//
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	};
	
	$("#crear_fecha_rem<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 110,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	
	/*var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		//url: "modulos/datos.php",
		//async: true
		localdata: ClientesData,
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);*/
	
	function ReDefine()
	{
		ClickOK = false;
		ClickCANCEL = false;
		
		if ("<?php echo $Interno ?>" == "")
		{
			ClearJSON = [
				//-1
				{id:"crear_ord_compra<?php echo $NumOfID ?>", type:""},
				{id:"crear_cliente<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_direccion<?php echo $NumOfID ?>", type:""},
				{id:"crear_telefono<?php echo $NumOfID ?>", type:""},
				{id:"crear_cupo_cr<?php echo $NumOfID ?>", type:""},
				{id:"crear_cupo_cr_check<?php echo $NumOfID ?>", type:"jqxCheckBox"},
				{id:"crear_remision<?php echo $NumOfID ?>", type:""},
				{id:"crear_interno<?php echo $NumOfID ?>", type:""},
				{id:"crear_e-mail<?php echo $NumOfID ?>", type:""},
				{id:"crear_contacto_p<?php echo $NumOfID ?>", type:""},
				{id:"crear_estado<?php echo $NumOfID ?>", type:""},
				{id:"crear_gar_docs<?php echo $NumOfID ?>", type:""},
				{id:"crear_factura<?php echo $NumOfID ?>", type:""},
				{id:"crear_ord_produccion<?php echo $NumOfID ?>", type:""},
				{id:"crear_beneficiario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_beneficiario_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				//-2
				{id:"crear_codigo<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_producto<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_existencia<?php echo $NumOfID ?>", type:""},
				{id:"crear_cantidad<?php echo $NumOfID ?>", type:""},
				{id:"crear_listap<?php echo $NumOfID ?>", type:"jqxDropDownList"},
				{id:"crear_products_grid<?php echo $NumOfID ?>", type:"jqxGrid"},
				//-3
				{id:"crear_observaciones<?php echo $NumOfID ?>", type:""},
				{id:"crear_notas<?php echo $NumOfID ?>", type:""},
				{id:"crear_subtotal_total<?php echo $NumOfID ?>", type:""},
				{id:"crear_tipo_servicio<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_tipo_servicio_precio<?php echo $NumOfID ?>", type:""},
				{id:"crear_tipo_descuento<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_tipo_descuento_precio<?php echo $NumOfID ?>", type:""},
				{id:"crear_iva_precio<?php echo $NumOfID ?>", type:""},
				{id:"crear_total_total<?php echo $NumOfID ?>", type:""},
				{id:"crear_conductor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_placa<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_formaP<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_vendedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cobrador<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_pesokg<?php echo $NumOfID ?>", type:""},
				{id:"crear_recibo<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_dir_entrega<?php echo $NumOfID ?>", type:""},
				{id:"crear_valor_rc<?php echo $NumOfID ?>", type:""},
				{id:"crear_sector<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_ruta<?php echo $NumOfID ?>", type:""},
				{id:"crear_saldo<?php echo $NumOfID ?>", type:""},
				//-4
				{id:"ventas_crear_deuda_total<?php echo $NumOfID ?>", type:""},
				{id:"ventas_crear_corriente<?php echo $NumOfID ?>", type:""},
				{id:"ventas_crear_de_30_a_45<?php echo $NumOfID ?>", type:""},
				{id:"ventas_crear_de_45_a_60<?php echo $NumOfID ?>", type:""},
				{id:"ventas_crear_de_60_a_90<?php echo $NumOfID ?>", type:""},
				{id:"ventas_crear_mas_de_90<?php echo $NumOfID ?>", type:""},
			];

			EnableDisableJSON = [
				//-1
				{id:"crear_ord_compra<?php echo $NumOfID ?>", type:""},
				{id:"crear_cliente<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_fecha_rem<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
				{id:"crear_remision<?php echo $NumOfID ?>", type:""},
				{id:"crear_factura<?php echo $NumOfID ?>", type:""},
				{id:"crear_beneficiario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_beneficiario_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				//-2
				{id:"crear_codigo<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_producto<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cantidad<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_listap<?php echo $NumOfID ?>", type:"jqxDropDownList"},
				{id:"crear_cotizar<?php echo $NumOfID ?>", type:"jqxCheckBox"},
				{id:"ventas_crear_addrowbutton<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"ventas_crear_deleterowbutton<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"ventas_crear_addrowbutton<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"ventas_crear_deleterowbutton<?php echo $NumOfID ?>", type:"jqxButton"},
				//-3
				{id:"crear_subtotal_total<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_tipo_servicio<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_tipo_servicio_precio<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_tipo_descuento<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_tipo_descuento_precio<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_iva_precio<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_total_total<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_conductor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_placa<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_formaP<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_vendedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cobrador<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_dir_entrega<?php echo $NumOfID ?>", type:""},
				{id:"crear_sector<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
				//{id:"crear_imprimir<?php echo $NumOfID ?>", type:"jqxButton"},
				//{id:"crear_client_benef<?php echo $NumOfID ?>", type:"jqxButton"},
				//{id:"crear_ver_historicos<?php echo $NumOfID ?>", type:"jqxButton"},
			];
		}
		else
		{
			ClearJSON = [
				//-1
				{id:"crear_ord_compra<?php echo $NumOfID ?>", type:""},
				{id:"crear_cliente<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_direccion<?php echo $NumOfID ?>", type:""},
				{id:"crear_telefono<?php echo $NumOfID ?>", type:""},
				{id:"crear_cupo_cr<?php echo $NumOfID ?>", type:""},
				{id:"crear_cupo_cr_check<?php echo $NumOfID ?>", type:"jqxCheckBox"},
				{id:"crear_remision<?php echo $NumOfID ?>", type:""},
				{id:"crear_interno<?php echo $NumOfID ?>", type:""},
				{id:"crear_e-mail<?php echo $NumOfID ?>", type:""},
				{id:"crear_contacto_p<?php echo $NumOfID ?>", type:""},
				{id:"crear_estado<?php echo $NumOfID ?>", type:""},
				{id:"crear_gar_docs<?php echo $NumOfID ?>", type:""},
				{id:"crear_factura<?php echo $NumOfID ?>", type:""},
				{id:"crear_ord_produccion<?php echo $NumOfID ?>", type:""},
				{id:"crear_beneficiario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_beneficiario_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				//-2
				{id:"crear_products_grid<?php echo $NumOfID ?>", type:"jqxGrid"},
				//-3
				{id:"crear_observaciones<?php echo $NumOfID ?>", type:""},
				{id:"crear_notas<?php echo $NumOfID ?>", type:""},
				{id:"crear_subtotal_total<?php echo $NumOfID ?>", type:""},
				{id:"crear_tipo_servicio<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_tipo_servicio_precio<?php echo $NumOfID ?>", type:""},
				{id:"crear_tipo_descuento<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_tipo_descuento_precio<?php echo $NumOfID ?>", type:""},
				{id:"crear_iva_precio<?php echo $NumOfID ?>", type:""},
				{id:"crear_total_total<?php echo $NumOfID ?>", type:""},
				{id:"crear_conductor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_placa<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_formaP<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_vendedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cobrador<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_pesokg<?php echo $NumOfID ?>", type:""},
				{id:"crear_recibo<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_dir_entrega<?php echo $NumOfID ?>", type:""},
				{id:"crear_valor_rc<?php echo $NumOfID ?>", type:""},
				{id:"crear_sector<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_ruta<?php echo $NumOfID ?>", type:""},
				{id:"crear_saldo<?php echo $NumOfID ?>", type:""},
			];
			
			EnableDisableJSON = [
				//-1
				{id:"crear_ord_compra<?php echo $NumOfID ?>", type:""},
				{id:"crear_cliente<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_fecha_rem<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
				{id:"crear_remision<?php echo $NumOfID ?>", type:""},
				{id:"crear_factura<?php echo $NumOfID ?>", type:""},
				{id:"crear_beneficiario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_beneficiario_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				//-2
				{id:"ventas_crear_addrowbutton<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"ventas_crear_deleterowbutton<?php echo $NumOfID ?>", type:"jqxButton"},
				//{id:"crear_products_grid<?php echo $NumOfID ?>", type:"jqxGrid"},
				//-3
				{id:"crear_subtotal_total<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_tipo_servicio<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_tipo_servicio_precio<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_tipo_descuento<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_tipo_descuento_precio<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_iva_precio<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_total_total<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"crear_conductor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_placa<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_formaP<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_vendedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_cobrador<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_dir_entrega<?php echo $NumOfID ?>", type:""},
				{id:"crear_sector<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"crear_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"crear_nuevo<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"crear_client_benef<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"crear_ver_historicos<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"crear_duplicar<?php echo $NumOfID ?>", type:"jqxButton"},
			];
		}
	}
	ReDefine();
	
	function LoadValues ()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Interno', type: 'string'},
				{ name: 'Ord_Compra', type: 'string' },
				{ name: 'Remision', type: 'string' },
				{ name: 'Fecha', type: 'string'},
				{ name: 'Factura', type: 'string' },
				{ name: 'Ord_Produccion', type: 'string' },
				{ name: 'Cliente', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Ruta', type: 'string'},
				{ name: 'Direccion', type: 'string'},
				{ name: 'FormaP', type: 'string' },
				{ name: 'Caja_Recibo', type: 'string' },
				{ name: 'VendedorID', type: 'string' },
				{ name: 'CobradorID', type: 'string' },
				{ name: 'Conductor', type: 'string'},
				{ name: 'Placa', type: 'string'},
				{ name: 'TipoServicio', type: 'string' },
				{ name: 'TipoServicioValor', type: 'decimal' },
				{ name: 'TipoDescuento', type: 'string' },
				{ name: 'TipoDescuentoValor', type: 'decimal' },
				{ name: 'Notas', type: 'string' },
				{ name: 'Observaciones', type: 'string' },
				{ name: 'Beneficiario', type: 'string'},
				{ name: 'BeneficiarioID', type: 'string'},
				{ name: 'Digitado_Por', type: 'string'},
				{ name: 'Digitado_Fecha', type: 'string'},
				{ name: 'Modificado_Por', type: 'string'},
				{ name: 'Modificado_Fecha', type: 'string'},
				{ name: 'Aprobado_Por', type: 'string'},
				{ name: 'Aprobado_Fecha', type: 'string'},
				{ name: 'Despachado_Por', type: 'string'},
				{ name: 'Despachado_Fecha', type: 'string'},
				//--
				{ name: 'CodFab', type: 'string'},
				{ name: 'Nombre', type: 'string' },
				{ name: 'UndMed', type: 'string'},
				{ name: 'Peso', type: 'decimal' },
				{ name: 'Existencia', type: 'decimal' },
				{ name: 'Cantidad', type: 'decimal'},
				{ name: 'Cantidad2', type: 'decimal'},
				{ name: 'Dcto', type: 'decimal'},
				{ name: 'Unitario', type: 'decimal' },
				{ name: 'Produccion', type: 'bool' },
				{ name: 'FacturaExistencia', type: 'bool' },
			],
			type: 'GET',
			data:{
				"Datos_Remision":true,
				"Interno":"<?php echo $Interno ?>",
				//"Ord_Compra":"<?php echo $Ord_Compra ?>",
			},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				if (records[0]["ClienteID"] == undefined)
				{
					Alerts_Box("La Factura Ingresada, no posee datos...", 4);
					return;
				}
				
				var len = records.length;
				
				for (var i = 0 ; i < len; i++)
				{
					var datarow = [{
						"CodFab":records[i]["CodFab"],
						"Nombre":records[i]["Nombre"],
						"UndMed":records[i]["UndMed"],
						"Peso":(parseFloat(records[i]["Peso"]) * parseFloat(records[i]["Cantidad"])).toFixed(2),
						"Peso_Unitario":records[i]["Peso"],
						"Existencia":records[i]["Existencia"],
						"Cantidad":records[i]["Cantidad"],
						"Cantidad2":records[i]["Cantidad2"],
						"Dcto":records[i]["Dcto"],
						"Unitario":records[i]["Unitario"],
						"Precio":records[i]["Unitario"],
						"Produccion":records[i]["Produccion"],
					}];
					$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				}
				$("#crear_ord_compra<?php echo $NumOfID ?>").val(records[0]["Ord_Compra"]);
				//$("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('selectItem', records[0]["ClienteID"]);
				//$("#crear_cliente<?php echo $NumOfID ?>").jqxComboBox('selectItem', records[0]["ClienteID"]);
				$("#crear_cliente<?php echo $NumOfID ?>").val(records[0]["Cliente"]);
				$("#crear_cliente_ID<?php echo $NumOfID ?>").val(records[0]["ClienteID"]);
				$("#crear_beneficiario<?php echo $NumOfID ?>").val(records[0]["Beneficiario"]);
				$("#crear_beneficiario_ID<?php echo $NumOfID ?>").val(records[0]["BeneficiarioID"]);
				$("#crear_interno<?php echo $NumOfID ?>").val(records[0]["Interno"]);
				$("#crear_remision<?php echo $NumOfID ?>").val(records[0]["Remision"]);
				$("#crear_fecha_rem<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#crear_factura<?php echo $NumOfID ?>").val(records[0]["Factura"]);
				$("#crear_ord_produccion<?php echo $NumOfID ?>").val(records[0]["Ord_Produccion"]);
				$("#crear_sector<?php echo $NumOfID ?>").val(records[0]["Ruta"]);
				$("#crear_ruta<?php echo $NumOfID ?>").val(records[0]["Ruta"]);
				$("#crear_dir_entrega<?php echo $NumOfID ?>").val(records[0]["Direccion"]);
				$("#crear_formaP<?php echo $NumOfID ?>").val(records[0]["FormaP"]);
				Caja_Recibo = records[0]["Caja_Recibo"];
			<?php 
				if (!isset($_POST["Crear_Emergente"]))
				{
			?>
				if (Caja_Recibo != "")
					Cargar_Recibo = true;
				
			<?php
				}
				else
				{
			?>
				$("#crear_digitado_por<?php echo $NumOfID ?>").val(records[0]["Digitado_Por"]);
				if (records[0]["Digitado_Fecha"] == "0000-00-00 00:00:00")
					$("#crear_digitado_fecha<?php echo $NumOfID ?>").val("");
				else
					$("#crear_digitado_fecha<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Digitado_Fecha"])));
				$("#crear_modificado_por<?php echo $NumOfID ?>").val(records[0]["Modificado_Por"]);
				if (records[0]["Modificado_Fecha"] == "0000-00-00 00:00:00")
					$("#crear_modificado_fecha<?php echo $NumOfID ?>").val("");
				else
					$("#crear_modificado_fecha<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Modificado_Fecha"])));
				$("#crear_aprobado_por<?php echo $NumOfID ?>").val(records[0]["Aprobado_Por"]);
				if (records[0]["Aprobado_Fecha"] == "0000-00-00 00:00:00")
					$("#crear_aprobado_fecha<?php echo $NumOfID ?>").val("");
				else
					$("#crear_aprobado_fecha<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Aprobado_Fecha"])));
				$("#crear_despachado_por<?php echo $NumOfID ?>").val(records[0]["Despachado_Por"]);
				if (records[0]["Despachado_Fecha"] == "0000-00-00 00:00:00")
					$("#crear_despachado_fecha<?php echo $NumOfID ?>").val("");
				else
					$("#crear_despachado_fecha<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Despachado_Fecha"])));
			<?php
				}
			?>
				$("#crear_vendedor<?php echo $NumOfID ?>").jqxComboBox('selectItem', records[0]["VendedorID"]);
				$("#crear_cobrador<?php echo $NumOfID ?>").jqxComboBox('selectItem', records[0]["CobradorID"]);
				$("#crear_conductor<?php echo $NumOfID ?>").jqxComboBox('selectItem', records[0]["Conductor"]);
				$("#crear_placa<?php echo $NumOfID ?>").jqxComboBox('selectItem', records[0]["Placa"]);
				$("#crear_tipo_servicio<?php echo $NumOfID ?>").val(records[0]["TipoServicio"]);
				$("#crear_tipo_servicio_precio<?php echo $NumOfID ?>").val(records[0]["TipoServicioValor"]);
				if (records[0]["TipoDescuento"] == "")
					$("#crear_tipo_descuento<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				else
					$("#crear_tipo_descuento<?php echo $NumOfID ?>").val(records[0]["TipoDescuento"]);
				
				$("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val(records[0]["TipoDescuentoValor"]);
				$("#crear_observaciones<?php echo $NumOfID ?>").val(records[0]["Observaciones"]);
				$("#crear_notas<?php echo $NumOfID ?>").val(records[0]["Notas"]);
				CargarValores();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	var RC_Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'Caja_Interno', type: 'string'},
			{ name: 'Caja_Recibo', type: 'string'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	function Cargar_RC ()
	{
		var Fecha_Recibo_Source =
		{
			datatype: "json",
			datafields: [
				{ name: 'Saldo', type: 'decimal'},
			],
			type: 'GET',
			data: {"RecibosConSaldo_Fecha":Caja_Recibo},
			url: "modulos/datos.php",
			async: true,
		};
		var Fecha_Recibo_DataAdapter = new $.jqx.dataAdapter(Fecha_Recibo_Source,{
			autoBind: true,
			loadComplete: function () {
				var records = Fecha_Recibo_DataAdapter.records;
				$("#crear_valor_rc<?php echo $NumOfID ?>").val(records[0]["Saldo"]);
				Calcular(true);
			}
		});
	};
	
	function CargarValores ()
	{
		var Classes = document.getElementById("crear_estado<?php echo $NumOfID ?>").className.split(/\s+/);
		for (var i = 0; i < Classes.length; i ++)
		{
			switch (Classes[i])
			{
				case "GreenState":
					$("#crear_estado<?php echo $NumOfID ?>").removeClass("GreenState");
				break;
				case "RedState":
					$("#crear_estado<?php echo $NumOfID ?>").removeClass("RedState");
				break;
				case "VioletState":
					$("#crear_estado<?php echo $NumOfID ?>").removeClass("VioletState");
				break;
			}
		}
		var Classes = document.getElementById("crear_gar_docs<?php echo $NumOfID ?>").className.split(/\s+/);
		for (var i = 0; i < Classes.length; i ++)
		{
			switch (Classes[i])
			{
				case "GreenState":
					$("#crear_gar_docs<?php echo $NumOfID ?>").removeClass("GreenState");
				break;
				case "OrangeState":
					$("#crear_gar_docs<?php echo $NumOfID ?>").removeClass("OrangeState");
				break;
			}
		}
		
		var a = 0;
		for (var i = 0; i < GlobalClientesFullData.length; i++)
		{
			if (GlobalClientesFullData[i]["ClienteID"] == $("#crear_cliente_ID<?php echo $NumOfID ?>").val())
			{
				a = i;
				break;
			}
		}
		
		$("#crear_direccion<?php echo $NumOfID ?>").val(GlobalClientesFullData[a]["Direccion"]);
		$("#crear_telefono<?php echo $NumOfID ?>").val(GlobalClientesFullData[a]["Telefono"]);
		$("#crear_cupo_cr<?php echo $NumOfID ?>").val(GlobalClientesFullData[a]["CupoCR"]);
		$("#crear_cupo_cr_check<?php echo $NumOfID ?>").val(GlobalClientesFullData[a]["CupoCR_Check"])
		$("#crear_contacto_p<?php echo $NumOfID ?>").val(GlobalClientesFullData[a]["ContactoP"]);
		$("#crear_e-mail<?php echo $NumOfID ?>").val(GlobalClientesFullData[a]["Email"]);
		$("#crear_estado<?php echo $NumOfID ?>").val(GlobalClientesFullData[a]["EstadoC"]);
		
		if (GlobalClientesFullData[a]["EstadoC"] == "Al Dia")
			$("#crear_estado<?php echo $NumOfID ?>").addClass("GreenState");
		else if (GlobalClientesFullData[a]["EstadoC"] == "Mora")
			$("#crear_estado<?php echo $NumOfID ?>").addClass("RedState");
		else
			$("#crear_estado<?php echo $NumOfID ?>").addClass("VioletState");
		
		$("#crear_gar_docs<?php echo $NumOfID ?>").val(GlobalClientesFullData[a]["Garantia"]);
		
		if (GlobalClientesFullData[a]["Garantia"] == "Al Dia")
			$("#crear_gar_docs<?php echo $NumOfID ?>").addClass("GreenState");
		else
			$("#crear_gar_docs<?php echo $NumOfID ?>").addClass("OrangeState");
		
		var Tmp = $("#crear_cobrador<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		if (!Tmp)
			$("#crear_cobrador<?php echo $NumOfID ?>").jqxComboBox('selectItem', GlobalClientesFullData[a]["CobradorID"]);
		
	<?php 
		if (!isset($_POST["Crear_Emergente"]))
		{
	?>
		
		//RC Combobox
		RC_Source.data = {"RecibosConSaldo": $("#crear_cliente_ID<?php echo $NumOfID ?>").val()};
		RC_Adapter = new $.jqx.dataAdapter(RC_Source);
		$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox({source: RC_Adapter});
		if ("<?php echo $Interno ?>" != "")
		{
			Calcular();
		}
		else
		{
			Cargar_Cartera();
		}
		
	<?php
		}
		else
		{
	?>
		$("#crear_recibo<?php echo $NumOfID ?>").val(Caja_Recibo);
		Calcular();
		//$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox('addItem', {"Caja_Recibo":Caja_Recibo});
	<?php
		}
	?>
		/*var ValoresSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Direccion', type: 'string'},
				{ name: 'ContactoP', type: 'string'},
				{ name: 'Telefono', type: 'string'},
				{ name: 'Email', type: 'string'},
				{ name: 'CupoCR', type: 'string'},
				{ name: 'CupoCR_Check', type: 'bool'},
				{ name: 'Garantia', type: 'string'},
				{ name: 'EstadoC', type: 'string'},
				{ name: 'CobradorID', type: 'string'},
			],
			data: {"Valores":$("#crear_cliente_ID<?php echo $NumOfID ?>").val()},
			url: "modulos/datos.php",
			async: true
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var Classes = document.getElementById("crear_estado<?php echo $NumOfID ?>").className.split(/\s+/);
				for (i = 0; i < Classes.length; i ++)
				{
					switch (Classes[i])
					{
						case "GreenState":
							$("#crear_estado<?php echo $NumOfID ?>").removeClass("GreenState");
						break;
						case "RedState":
							$("#crear_estado<?php echo $NumOfID ?>").removeClass("RedState");
						break;
						case "VioletState":
							$("#crear_estado<?php echo $NumOfID ?>").removeClass("VioletState");
						break;
					}
				}
				var Classes = document.getElementById("crear_gar_docs<?php echo $NumOfID ?>").className.split(/\s+/);
				for (i = 0; i < Classes.length; i ++)
				{
					switch (Classes[i])
					{
						case "GreenState":
							$("#crear_gar_docs<?php echo $NumOfID ?>").removeClass("GreenState");
						break;
						case "OrangeState":
							$("#crear_gar_docs<?php echo $NumOfID ?>").removeClass("OrangeState");
						break;
					}
				}
					
				var records = ValoresDataAdapter.records;
				$("#crear_direccion<?php echo $NumOfID ?>").val(records[0]["Direccion"]);
				$("#crear_telefono<?php echo $NumOfID ?>").val(records[0]["Telefono"]);
				$("#crear_cupo_cr<?php echo $NumOfID ?>").val(records[0]["CupoCR"]);
				$("#crear_cupo_cr_check<?php echo $NumOfID ?>").val(records[0]["CupoCR_Check"])
				$("#crear_contacto_p<?php echo $NumOfID ?>").val(records[0]["ContactoP"]);
				$("#crear_e-mail<?php echo $NumOfID ?>").val(records[0]["Email"]);
				$("#crear_estado<?php echo $NumOfID ?>").val(records[0]["EstadoC"]);
				
				if (records[0]["EstadoC"] == "Al Dia")
					$("#crear_estado<?php echo $NumOfID ?>").addClass("GreenState");
				else if (records[0]["EstadoC"] == "Mora")
					$("#crear_estado<?php echo $NumOfID ?>").addClass("RedState");
				else
					$("#crear_estado<?php echo $NumOfID ?>").addClass("VioletState");
				
				$("#crear_gar_docs<?php echo $NumOfID ?>").val(records[0]["Garantia"]);
				
				if (records[0]["Garantia"] == "Al Dia")
					$("#crear_gar_docs<?php echo $NumOfID ?>").addClass("GreenState");
				else
					$("#crear_gar_docs<?php echo $NumOfID ?>").addClass("OrangeState");
				
				var Tmp = $("#crear_cobrador<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
				if (!Tmp)
					$("#crear_cobrador<?php echo $NumOfID ?>").jqxComboBox('selectItem', records[0]["CobradorID"]);
				
			<?php 
				if (!isset($_POST["Crear_Emergente"]))
				{
			?>
				
				//RC Combobox
				RC_Source.data = {"RecibosConSaldo": $("#crear_cliente_ID<?php echo $NumOfID ?>").val()};
				RC_Adapter = new $.jqx.dataAdapter(RC_Source);
				$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox({source: RC_Adapter});
				if ("<?php echo $Interno ?>" != "")
				{
					Calcular();
				}
				else
				{
					Cargar_Cartera();
				}
				
			<?php
				}
				else
				{
			?>
				$("#crear_recibo<?php echo $NumOfID ?>").val(Caja_Recibo);
				Calcular();
				//$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox('addItem', {"Caja_Recibo":Caja_Recibo});
			<?php
				}
			?>
			}
		});
		*/
	};
	
	$('#Crear_HideButton<?php echo $NumOfID ?>').click(function() {
		$("#Crear_Content_to_Hide<?php echo $NumOfID ?>").toggle();
	});
	
	//------------------------------------------- KEY JUMPS
	$('#crear_ord_compra<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#crear_cliente<?php echo $NumOfID ?>").jqxComboBox('focus');
		}
	});
	$('#crear_cliente<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('focus');
		}
	});
	$('#crear_cliente_ID<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#crear_fecha_rem<?php echo $NumOfID ?>").jqxDateTimeInput('focus');
		}
	});
	$('#crear_fecha_rem<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#crear_remision<?php echo $NumOfID ?>").jqxInput('focus');
		}
	});
	$('#crear_remision<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#crear_factura<?php echo $NumOfID ?>").jqxInput('focus');
		}
	});
	$('#crear_factura<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('focus');
		}
	});
	$('#crear_codigo<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('focus');
		}
	});
	$('#crear_producto<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			if (ExistenciaCargada == false) {
				return;
			}
			else {
				$("#crear_cantidad<?php echo $NumOfID ?>").jqxNumberInput('focus');
				var input = $('#crear_cantidad<?php echo $NumOfID ?> input')[0];
				if ('selectionStart' in input) {
					input.setSelectionRange(0, 0);
				} else {
					var range = input.createTextRange();
					range.collapse(true);
					range.moveEnd('character', 0);
					range.moveStart('character', 0);
					range.select();
				}
			}
		}
	});
	$('#crear_cantidad<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13 && cantidad_exedida == false)
		{
			$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('focus');
		}
	});
	$('#crear_listap<?php echo $NumOfID ?>').keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row();
		}
	});

	$("#crear_cliente<?php echo $NumOfID ?>").jqxComboBox(
	{
		width: 410,
		height: 20,
		theme: mytheme,
		source: ClientesData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#crear_cliente<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#crear_cliente_ID<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			$("#crear_cliente<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		theme: mytheme,
		source: ClientesData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#crear_cliente_ID<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#crear_cliente<?php echo $NumOfID ?>").val() != event.args.item.value)
					$("#crear_cliente<?php echo $NumOfID ?>").jqxComboBox('selectItem', event.args.item.value);
			
				CargarValores();
			},500);
		}
		else
		{
			$("#crear_cliente<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#crear_direccion<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 260,
		disabled: true,
	});

	$("#crear_telefono<?php echo $NumOfID ?>").jqxInput({//.jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 112,
		//mask: '(###)###-####',
		disabled: true,
	});

	$("#crear_cupo_cr<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 175,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});

	$("#crear_cupo_cr_check<?php echo $NumOfID ?>").jqxCheckBox({
		theme: mytheme,
		boxSize: 19,
		disabled: true
	});
	$("#crear_cupo_cr_check<?php echo $NumOfID ?>").bind('change', function (event) {
		var checked = event.args.checked;
	});

	$("#crear_contacto_p<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 145,
		disabled: true,
	});
	
	$("#crear_e-mail<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 140,
		disabled: true,
	});
	
	$("#crear_estado<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 65,
		disabled: true
	});

	$("#crear_gar_docs<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 65,
		disabled: true
	});
	
	$("#crear_remision<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110
	});
	
	$("#crear_ord_compra<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110
	});
	$("#crear_ord_compra<?php echo $NumOfID ?>").on("change", function (event)
	{
		if ("<?php echo $Interno ?>" == "")
		{
			var value = $("#crear_ord_compra<?php echo $NumOfID ?>").val();

			var FindSource =
			{
				datatype: "json",
				datafields: [
					{ name: 'Same', type: 'bool'},
				],
				type: 'GET',
				data: {
					"Ventas_Check_SameID":value,
				},
				url: "modulos/datos.php",
				async: true,
			};
			var FindDataAdapter = new $.jqx.dataAdapter(FindSource,{
				autoBind: true,
				loadComplete: function ()
				{	
					var records = FindDataAdapter.records;
					if (records[0]["Same"] == true)
					{
						$("#crear_ord_compra<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
						ID_Incorrecto = true;
						Alerts_Box("La Orden de Compra Ingresada, ya Existe!<br/>Favor Ingresar Otra para Continuar.", 3);
						WaitClick_Input("crear_ord_compra<?php echo $NumOfID ?>");
					}
					else
					{
						$("#crear_ord_compra<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
						ID_Incorrecto = false;
					}
				}
			});
		}
	});
	
	$("#crear_ord_produccion<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true,
	});
	
	$("#crear_factura<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110
	});
	
	$("#crear_interno<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true
	});
	
	$("#crear_beneficiario<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 220,
		source: ClientesData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Beneficiario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#crear_beneficiario<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#crear_beneficiario_ID<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#crear_beneficiario_ID<?php echo $NumOfID ?>").val(event.args.item.value);
		}
		else
		{
			$("#crear_beneficiario<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_beneficiario_ID<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#crear_beneficiario_ID<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: ClientesData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	
	$("#crear_beneficiario_ID<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#crear_beneficiario<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#crear_beneficiario<?php echo $NumOfID ?>").val(event.args.item.value);

		}
		else
		{
			$("#crear_beneficiario<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_beneficiario_ID<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	//---------------------------------------------------------------- PARTE 2
	if ("<?php echo $Interno ?>" == "")
	{
		//-- GLOBAL
		var CodFabID = 0;
		var FacturaExistencia = false;
		var RowAdded = true;
		
		// prepare the data
		var CB_ProductoSource =
		{
			datatype: "json",
			datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
			],
			url: "modulos/datos_productos.php",
			async: true
		};
		
		var CB_ProductoDataAdapter = new $.jqx.dataAdapter(CB_ProductoSource);
		
		function GetPriceList()
		{
			ExistenciaCargada = false;
			var GetValuesSource =
			{
				datatype: "json",
				datafields: [
					{ name: 'Existencia', type: 'decimal' },
					{ name: 'Lista1', type: 'decimal' },
					{ name: 'Lista2', type: 'decimal' },
					{ name: 'Lista3', type: 'decimal' },
					{ name: 'Lista4', type: 'decimal' },
					{ name: 'Lista4', type: 'decimal' },
					{ name: 'FacturaExistencia', type: 'bool' },
				],
				data:{"Precios":$("#crear_codigo<?php echo $NumOfID ?>").val()},
				url: "modulos/datos_productos.php",
			};
			var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
				autoBind: true,
				loadComplete: function ()
				{
					$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('clear');
					$("#crear_existencia<?php echo $NumOfID ?>").val('');
					var records = GetValuesAdapter.records;
					$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('addItem', {label: "P1 $"+ records[0]["Lista1"], value: records[0]["Lista1"]});
					$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('addItem', {label: "P2 $"+ records[0]["Lista2"], value: records[0]["Lista2"]});
					$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('addItem', {label: "P3 $"+ records[0]["Lista3"], value: records[0]["Lista3"]});
					$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('addItem', {label: "P4 $"+ records[0]["Lista4"], value: records[0]["Lista4"]});
					$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('selectIndex', 0);
					
					var datinfo = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
					var count = datinfo.rowscount;
					var totalc = 0;
					for (var i = 0; i < count; i++)
					{
						var currentRow = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
						if (currentRow.CodFab == $("#crear_codigo<?php echo $NumOfID ?>").val())
						{
							totalc = totalc + currentRow.Cantidad;
						}
					}
					var totala = records[0]["Existencia"] - totalc;
					if (totala < 0)
						totala = 0;
					$("#crear_existencia<?php echo $NumOfID ?>").val(totala);
					FacturaExistencia = records[0]["FacturaExistencia"];
					ExistenciaCargada = true;
				},
				loadError: function(jqXHR, status, error) {
					Alerts_Box("Request failed: " + error, 3);
				},
				downloadComplete: function(edata, textStatus, jqXHR) {
					//alert("Data is Loaded");
				}
			});
		};
		
		$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 130,
			height: 20,
			source: CB_ProductoDataAdapter,
			searchMode: 'startswithignorecase',
			autoComplete: true,
			promptText: 'Buscar Codigo',
			selectedIndex: -1,
			displayMember: 'CodFab',
			valueMember: 'CodFab'
		});
		$("#crear_codigo<?php echo $NumOfID ?>").bind('change', function (event)
		{
			if (event.args)
			{
				clearTimeout(Timer1);
				Timer1 = setTimeout(function()
				{
					if ($("#crear_producto<?php echo $NumOfID ?>").val() != event.args.item.value)
						$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('selectItem', event.args.item.value);
					GetPriceList();
				},300);
			}
			else
			{
				var item_value = $("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
				if (item_value)
				{
					$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
					$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
					$("#crear_existencia<?php echo $NumOfID ?>").val("");
				}
				else
				{
					var value = $("#crear_codigo<?php echo $NumOfID ?>").val();
					var item = $("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('getItemByValue', value);
					if (item == undefined)
					{
						$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
						$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
						$("#crear_existencia<?php echo $NumOfID ?>").val("");
					}
					else
						$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('selectItem', item.value);
				}
			}
		});
		
		$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 300,
			height: 20,
			source: CB_ProductoDataAdapter,
			searchMode: 'containsignorecase',
			autoComplete: true,
			promptText: 'Buscar Producto',
			selectedIndex: -1,
			displayMember: 'Nombre',
			valueMember: 'CodFab'
		});
		$("#crear_producto<?php echo $NumOfID ?>").bind('change', function (event)
		{
			if (event.args)
			{
				if ($("#crear_codigo<?php echo $NumOfID ?>").val() != event.args.item.value)
					$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('selectItem', event.args.item.value);
			}
			else
			{
				var item_value = $("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
				if (item_value)
				{
					$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
					$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
					$("#crear_existencia<?php echo $NumOfID ?>").val("");
				}
				else
				{
					var value = $("#crear_producto<?php echo $NumOfID ?>").val();
					
					var item = $("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('getItems');
					for (var i = 0; i < item.length; i++)
					{
						if (item[i].label == value)
						{
							$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('selectItem', item[i].value);
							return;
						}
					}
					$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
					$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
					$("#crear_existencia<?php echo $NumOfID ?>").val("");
				}
			}
		});
		
		$("#crear_existencia<?php echo $NumOfID ?>").jqxInput({
			theme: mytheme,
			height: 20,
			width: 140,
			disabled: true,
			rtl: true
		});
		
		$("#crear_cantidad<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 160,
			inputMode: 'simple',
			spinButtons: false
		});
		
		var Data = {};
		
		$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList({
			theme: mytheme,
			height: 20,
			width: 180,
			source: Data,
			placeHolder: 'Lista de Precios',
			selectedIndex: -1
		});
		
		$("#crear_cotizar<?php echo $NumOfID ?>").jqxCheckBox({
			theme: mytheme,
			height: 20,
			width: 30,
			boxSize: 20,
		});
		
		$('#crear_form_validation<?php echo $NumOfID ?>').jqxValidator({
			rules:
			[
				{
					input: '#crear_cantidad<?php echo $NumOfID ?>', message: 'Debe ser un valor mayor a 0!', action: 'keyup, blur', rule: function (input, commit) {
						if (input.val() < 0) {
							return false;
						}
						return true;
					}
				},
				{
					input: '#crear_cantidad<?php echo $NumOfID ?>', message: 'Cantidad mayor a Existencia!', action: 'keyup, blur', rule: function (input, commit) {
						if (input.val() > $("#crear_existencia").val() && FacturaExistencia == false && $("#crear_cotizar<?php echo $NumOfID ?>").val() == false) {
							cantidad_exedida = true;
							return false;
						} else {
							cantidad_exedida = false;
						}
						return true;
					}
				}
			]
		});
	}
	var source =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'Existencia', type: 'decimal' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Cantidad2', type: 'decimal' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Dcto', type: 'decimal' },
			{ name: 'Unitario', type: 'decimal' },
			{ name: 'Subtotal', type: 'decimal' },
			{ name: 'Precio', type: 'decimal' },
			{ name: 'Produccion', type: 'bool' },
			{ name: 'FacturaExistencia', type: 'bool' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var dataAdapter = new $.jqx.dataAdapter(source);
	
	function Add_Row()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;

		var Product = $("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var ProductPrice = $("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('getSelectedItem');
		var ExistenciaNum = $("#crear_existencia<?php echo $NumOfID ?>").val();
		var CantidadNum = $("#crear_cantidad<?php echo $NumOfID ?>").val();
		
		if (!Product) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("crear_producto<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum > ExistenciaNum && FacturaExistencia == false && $("#crear_cotizar<?php echo $NumOfID ?>").val() == false) {
			Alerts_Box("Cantidad mayor a la Existencia!", 3);
			WaitClick_NumberInput("crear_cantidad<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum < 1) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("crear_cantidad<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (ExistenciaNum < 1 && FacturaExistencia == false && $("#crear_cotizar<?php echo $NumOfID ?>").val() == false) {
			Alerts_Box("\"AGOTADO\" No hay crear_existencia!</br>Favor Seleccionar otro Producto", 4);
			WaitClick_Combobox("crear_producto<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i = 0; i < count; i++)
		{
			var currentRow = $('#crear_products_grid<?php echo $NumOfID ?>').jqxGrid('getrowdata', i);
			if (currentRow.CodFab == Product.value)
			{
				var totalc = CantidadNum + currentRow.Cantidad;
				if (totalc > currentRow.Existencia) {
					Alerts_Box("Cantidad Mayor a Existencia!", 3);
					RowAdded = true;
					return;
				}
				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"UndMed":currentRow.UndMed,
					"Existencia":currentRow.Existencia,
					"Cantidad":totalc,
					"Peso":(parseFloat(currentRow.Peso_Unitario) * totalc).toFixed(2),
					"Peso_Unitario":currentRow.Peso_Unitario,
					"Dcto":currentRow.Dcto,
					"Unitario":currentRow.Unitario,
					"Precio":currentRow.Precio,
					"Produccion":currentRow.Produccion,
					"FacturaExistencia":currentRow.FacturaExistencia,
				}];
				var id = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getrowid', i);
				$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
				$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#crear_existencia<?php echo $NumOfID ?>").val('');
				$("#crear_cantidad<?php echo $NumOfID ?>").val('');
				$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('clear');
				Calcular();
				RowAdded = true;
				return;
			}
		}
		
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
			{ name: 'UndMed', type: 'string'},
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Produccion', type: 'bool' },
			{ name: 'FacturaExistencia', type: 'bool' }
			],
			type: 'GET',
			data:{"Productos":Product.value},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":Product.value,
					"Nombre":Product.label,
					"UndMed":records[0]["UndMed"],
					"Existencia":ExistenciaNum,
					"Cantidad":CantidadNum,
					"Peso":(parseFloat(records[0]["Peso"]) * CantidadNum).toFixed(2),
					"Peso_Unitario":records[0]["Peso"],
					"Dcto":0,
					"Unitario":ProductPrice.value,
					"Precio":ProductPrice.value,
					"Produccion":records[0]["Produccion"],
					"FacturaExistencia":records[0]["FacturaExistencia"]
				}];
				$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#crear_producto<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#crear_existencia<?php echo $NumOfID ?>").val('');
				$("#crear_cantidad<?php echo $NumOfID ?>").val('');
				$("#crear_listap<?php echo $NumOfID ?>").jqxDropDownList('clear');
				Calcular();
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: "+error, 3);
			},
			downloadComplete: function(edata, textStatus, jqXHR) {
				//alert("Data is Loaded");
			}
		});
	};

	$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 260,
		source: dataAdapter,
		showtoolbar: true,
		editable: true,
		editmode: 'dblclick',
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="ventas_crear_addrowbutton<?php echo $NumOfID ?>" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="ventas_crear_deleterowbutton<?php echo $NumOfID ?>" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#ventas_crear_addrowbutton<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "success"});
			$("#ventas_crear_deleterowbutton<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#ventas_crear_addrowbutton<?php echo $NumOfID ?>").on('click', function () {
				Add_Row();
			});
			// delete row.
			$("#ventas_crear_deleterowbutton<?php echo $NumOfID ?>").on('click', function () {
				var selectedrowindex = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getselectedrowindex');
				var rowscount = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
					Calcular();
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: 90, height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: 240, height: 20 },
			{ text: 'Und', datafield: 'UndMed', editable: false, width: 50, height: 20 },
			<?php
			if ($Estado == "Autorizado")
			{
			?>
			{ text: 'Exist.', datafield: 'Existencia', editable: false, width: 60, height: 20, cellsalign: 'right' },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: 80,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					if (rowdata.Cantidad > rowdata.Existencia && rowdata.FacturaExistencia == false && $("#crear_cotizar<?php echo $NumOfID ?>").val() == false) {
						Alerts_Box("La Cantidad no debe ser mayor a la Existencia!", 3);
						return "<div id='row_error'>"+rowdata.Cantidad+"</div>";
					}
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Cant. Desp.',
				datafield: 'Cantidad2',
				width: 80,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
			},
			{
				text: 'Peso',
				datafield: 'Peso',
				width: 80,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			<?php
			}
			else
			{
			?>
			{ text: 'Exist.', datafield: 'Existencia', editable: false, width: 100, height: 20, cellsalign: 'right' },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: 100,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					if (rowdata.Cantidad > rowdata.Existencia && rowdata.FacturaExistencia == false && $("#crear_cotizar<?php echo $NumOfID ?>").val() == false) {
						Alerts_Box("La Cantidad no debe ser mayor a la Existencia!", 3);
						return "<div id='row_error'>"+rowdata.Cantidad+"</div>";
					}
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Peso',
				datafield: 'Peso',
				width: 100,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			<?php
			}
			?>
			{
				text: '%Dcto',
				datafield: 'Dcto',
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'p',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 2 });
				}
			},
			{
				text: 'Unitario',
				datafield: 'Unitario',
				width: 110,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "El precio del Producto debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'SubTotal',
				datafield: 'Subtotal',
				width: 150,
				height: 20,
				editable: false,
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = Math.round(parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad));
					//var total2 = Math.round( total - (parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad)) / 100 * parseFloat(rowdata.Dcto) );
					
					return "<div style='margin: 4px;' class='jqx-right-align'>" + dataAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
			{ text: '', datafield: 'Peso_Unitario', editable: false, columntype: 'numberinput', width: 0, height: 20},
			{ text: '', datafield: 'Precio', editable: false, columntype: 'numberinput', width: 0, height: 20},
			{ text: 'P', datafield: 'Produccion', editable: false, columntype: 'checkbox', width: 15 },
			{ text: 'F', datafield: 'FacturaExistencia', editable: false, columntype: 'checkbox', width: 15 },
		]
	});
	<?php
	if ($Interno != "")
	{
	?>
	$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid({ editable: false });
	<?php
	}
	?>
	$('#crear_products_grid<?php echo $NumOfID ?>').jqxGrid('hidecolumn', 'Peso_Unitario');
	$('#crear_products_grid<?php echo $NumOfID ?>').jqxGrid('hidecolumn', 'Precio');
	$('#crear_products_grid<?php echo $NumOfID ?>').jqxGrid('hidecolumn', 'Produccion');
	$('#crear_products_grid<?php echo $NumOfID ?>').jqxGrid('hidecolumn', 'FacturaExistencia');
	
	$("#crear_products_grid<?php echo $NumOfID ?>").on('cellvaluechanged', function (event) 
	{
		if (event.args)
		{
			var args = event.args;
			var datafield = event.args.datafield;
			var rowBoundIndex = args.rowindex;
			var value = args.newvalue;
			var oldvalue = args.oldvalue;

			if (datafield == "Cantidad" || datafield == "Dcto" || datafield == "Unitario")
			{
				if (datafield == "Cantidad")
				{
					var Peso = parseFloat($("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getcellvalue', rowBoundIndex, "Peso_Unitario"));
					var Total = (Peso * value);

					if (Total < 0)
						Total = 0;
					
					$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('setcellvalue', rowBoundIndex, "Peso", Total);	
				}
				else if (datafield == "Dcto")
				{
					var Precio = parseFloat($("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getcellvalue', rowBoundIndex, "Precio"));
					var Total = Precio - ((Precio / 100) * value);
					$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('setcellvalue', rowBoundIndex, "Unitario", Total);
				}
				else if (datafield == "Unitario")
				{
					var Precio = parseFloat($("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getcellvalue', rowBoundIndex, "Precio"));
					var Total = (((Precio - value) / Precio) * 100).toFixed(2);

					if (Total < 0)
						Total = 0;
					
					$("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('setcellvalue', rowBoundIndex, "Dcto", Total);	
				}
				Calcular();
			}
		}
	});
	
	// ------------------------------------------ PARTE 3
	
	<?php 
	if (isset($_POST["Crear_Emergente"]))
	{
	?>
	
	$("#crear_digitado_por<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#crear_digitado_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 135,
		height: 20,
		formatString: 'dd-MMM-yyyy HH:mm:ss',
		culture: 'es-ES',
		showCalendarButton: false,
		allowNullDate: true,
		disabled: true
	});
	
	$("#crear_modificado_por<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#crear_modificado_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 135,
		height: 20,
		formatString: 'dd-MMM-yyyy HH:mm:ss',
		culture: 'es-ES',
		showCalendarButton: false,
		allowNullDate: true,
		disabled: true
	});
	
	$("#crear_aprobado_por<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#crear_aprobado_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 135,
		height: 20,
		formatString: 'dd-MMM-yyyy HH:mm:ss',
		culture: 'es-ES',
		showCalendarButton: false,
		allowNullDate: true,
		disabled: true
	});
	
	$("#crear_despachado_por<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#crear_despachado_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 135,
		height: 20,
		formatString: 'dd-MMM-yyyy HH:mm:ss',
		culture: 'es-ES',
		showCalendarButton: false,
		allowNullDate: true,
		disabled: true
	});
	
	<?php 
	}
	?>
	
	var ConductorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Chofer', type: 'string'},
			{ name: 'ClienteID', type: 'string'},
		],
		data: {"Chofer":true},
		url: "modulos/parametros.php",
	};
	var ConductorDataAdapter = new $.jqx.dataAdapter(ConductorSource);
	
	var VehiculoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Placa', type: 'string'},
		],
		data: {"Vehiculo":true},
		url: "modulos/parametros.php",
	};
	var VehiculoDataAdapter = new $.jqx.dataAdapter(VehiculoSource);
	
	$("#crear_conductor<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 160,
		source: ConductorDataAdapter,
		dropDownHeight: 150,
		promptText: 'Seleccionar Conductor',
		selectedIndex: -1,
		displayMember: 'Chofer',
		valueMember: 'ClienteID',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#crear_conductor<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
			$("#crear_conductor<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
	});
	
	$("#crear_formaP<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 148,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Forma',
		valueMember: 'Forma',
		dropDownHeight: 50
	});
	$("#crear_formaP<?php echo $NumOfID ?>").jqxComboBox('addItem', {"Forma":"Efectivo"});
	$("#crear_formaP<?php echo $NumOfID ?>").jqxComboBox('addItem', {"Forma":"Credito"});
	
	$("#crear_formaP<?php echo $NumOfID ?>").on('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				var Cliente = $("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
				
				if (!Cliente) {
					Alerts_Box("Debe Seleccionar un Cliente!", 4);
					$("#crear_formaP<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				}
				
				if (event.args.item.value == "Credito")
				{
					//if ($("#crear_cupo_cr_check<?php echo $NumOfID ?>").val() == true)
					//{
						$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
						$("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
						$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox({ disabled: true });
						$("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox({ disabled: true });
						$("#crear_valor_rc<?php echo $NumOfID ?>").val('');
						$("#crear_saldo<?php echo $NumOfID ?>").val('');
					//}
					/*else if ($("#crear_cupo_cr_check<?php echo $NumOfID ?>").val() == true && $("#crear_cupo_cr<?php echo $NumOfID ?>").val() < 1 ) {
						Alerts_Box("El Cliente Seleccionado no posee credito Activo!", 3);
						$("#crear_formaP<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
					}*/
					Calcular(true);
				}
				else if (event.args.item.value == "Efectivo")
				{
					$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox({ disabled: false });
					$("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox({ disabled: false });
				}
				clearTimeout(Timer1);
			},500);
		}
		else {
			$("#crear_formaP<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox({ disabled: true });
			$("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox({ disabled: true });
		}
	});
	
	$("#crear_placa<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 95,
		source: VehiculoDataAdapter,
		dropDownHeight: 150,
		promptText: 'Sel. Placa',
		selectedIndex: -1,
		displayMember: 'Placa',
		valueMember: 'Placa',
		searchMode: 'startswithignorecase',
		autoComplete: true,
	});
	$("#crear_placa<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
			$("#crear_placa<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
	});
	
	$("#crear_ruta<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 95,
		disabled: true
	});
	
	$("#crear_pesokg<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 95,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 7,
		disabled: true
	});
	
	$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 148,
		promptText: 'Sin Recibo',
		selectedIndex: -1,
		disabled: true,
		displayMember: 'Caja_Recibo',
		valueMember: 'Caja_Interno',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#crear_recibo<?php echo $NumOfID ?>").on('bindingComplete', function (event) {
		if (Cargar_Recibo) {
			$("#crear_recibo<?php echo $NumOfID ?>").val(Caja_Recibo);
			Cargar_RC();
		}
	});
	$("#crear_recibo<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_valor_rc<?php echo $NumOfID ?>").val('');
			$("#crear_saldo<?php echo $NumOfID ?>").val('');
			Caja_Interno = "";
			Caja_Recibo = "";
		}
	});
	$("#crear_recibo<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			Caja_Interno = event.args.item.value;
			Caja_Recibo = event.args.item.label;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				Cargar_RC();
				clearTimeout(Timer1);
			},500);
		}
	});
	
	$("#crear_dir_entrega<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 430
	});
	
	$("#crear_valor_rc<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 148,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
		disabled: true
	});
	
	var SectorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Barrio', type: 'string'},
			{ name: 'Ruta', type: 'string'}
		],
		type: 'GET',
		data: {"Ruta":true},
		url: "modulos/parametros.php",
		async: true
	};
	var SectorDataAdapter = new $.jqx.dataAdapter(SectorSource);
	
	$("#crear_sector<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 295,
		source: SectorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Sector',
		selectedIndex: -1,
		displayMember: 'Barrio',
		valueMember: 'Ruta',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#crear_sector<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#crear_sector<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#crear_ruta<?php echo $NumOfID ?>").val('');
		}
	});
	$("#crear_sector<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			var ruta_val = event.args.item.value;
			$("#crear_ruta<?php echo $NumOfID ?>").val(ruta_val);
		}
	});
	
	var VendedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Vendedor', type: 'string'}
		],
		type: 'GET',
		data: {"Venta":true},
		url: "modulos/parametros.php",
		async: true
	};
	var VendedorDataAdapter = new $.jqx.dataAdapter(VendedorSource);

	$("#crear_vendedor<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 118,
		dropDownHeight: 150,
		source: VendedorDataAdapter,
		promptText: 'Selecionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#crear_vendedor<?php echo $NumOfID ?>").on('change', function (event)
	{
		if (!event.args)
			$("#crear_vendedor<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
	});
	
	$("#crear_cobrador<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 118,
		dropDownHeight: 150,
		source: VendedorDataAdapter,
		promptText: 'Selecionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#crear_cobrador<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
			$("#crear_cobrador<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
	});
	
	$("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 84,
		dropDownHeight: 150,
		source: VendedorDataAdapter,
		promptText: 'Selecionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
		disabled: true,
	});
	$("#crear_cajero<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
			$("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
	});
	
	$("#crear_saldo<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 148,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
		disabled: true
	});
	
	var iva = 0;
	var total = 0;
	
	$("#crear_subtotal_text<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		//textAlign: 'center',
		disabled: true,
		placeHolder: "               SUBTOTAL",
	});
	
	$("#crear_subtotal_total<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		//decimalSeparator: ","
	});
	
	function Calcular(Ignore)
	{
		var Calc_Timer = setTimeout(function()
		{
			var information = $('#crear_products_grid<?php echo $NumOfID ?>').jqxGrid('getdatainformation');
			var rowscounts = information.rowscount;
			var old_total = 0;
			var total_peso = 0;
			for (i = 0; i < rowscounts; i++)
			{
				var currentRow = $('#crear_products_grid<?php echo $NumOfID ?>').jqxGrid('getrowdata', i);
				var total1 = parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad);
				//var total2 = Math.round( total1 - (parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad)) / 100 * parseFloat(currentRow.Dcto) );
				old_total = old_total + total1;
				total_peso = total_peso + parseFloat(currentRow.Peso_Unitario) * parseFloat(currentRow.Cantidad);
			};
			
			var valor_tipo = $('#crear_tipo_servicio_precio<?php echo $NumOfID ?>').val();
			old_total = old_total + valor_tipo;
			
			if (total == old_total && Ignore == false)
				return;
			else
				total = old_total;
			
			iva = Math.round(total - (total / 1.16));
			/*var tipo_dcto = $("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val();
			if ($("#crear_tipo_descuento<?php echo $NumOfID ?>").val() != "" ) {
				tipo_dcto = iva;
				$("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val(tipo_dcto);
			}*/
			
			var tipo_dcto = $("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val();
			var dcto = $("#crear_tipo_descuento<?php echo $NumOfID ?>").val();
			if (tipo_dcto <= 0 && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val(tipo_dcto);
			}
			else if (tipo_dcto != iva && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val(tipo_dcto);
			}
			else if (tipo_dcto <= 0 && dcto.indexOf("IVA") < 1)
			{
				$("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val("0");
			}
			
			var subtotal = Math.round(total);
			total = Math.round(total - tipo_dcto);
			if (total < 0)
				total = 0;
			
			var item = $("#crear_formaP<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		<?php 
			if (!isset($_POST["Crear_Emergente"]))
			{
		?>
			if (item)
			{
				//alert("1")
				if (item.label == "Efectivo")
				{
					//alert("2")
					var ValorRC = $("#crear_valor_rc<?php echo $NumOfID ?>").val();
					
					if (total > ValorRC && $("#crear_recibo<?php echo $NumOfID ?>").val() != "")
					{
						Alerts_Box("El Valor de la Factura es Mayor a el Saldo Disponible", 3);
						$("#crear_valor_rc<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
						$("#crear_saldo<?php echo $NumOfID ?>").val("0");
						ErrorSaldo = true;
					}
					else if (total < ValorRC && $("#crear_recibo<?php echo $NumOfID ?>").val() != "")
					{
						$("#crear_valor_rc<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
						$("#crear_saldo<?php echo $NumOfID ?>").val(ValorRC - total);
						ErrorSaldo = false;
					}
				}
				else if (item.label == "Credito")
				{
					//alert("3")
					var ValorCupo = $("#crear_cupo_cr<?php echo $NumOfID ?>").val();
					var Deuda = $("#ventas_crear_deuda_total<?php echo $NumOfID ?>").val();
					var Exceso = $("#ventas_crear_mas_de_90<?php echo $NumOfID ?>").val();
					
					if (Exceso > 0 && $("#crear_cupo_cr_check<?php echo $NumOfID ?>").val() == true)
					{
						/*Alerts_Box("El Cliente Posee Deudas Mayores a 90 Dias.", 3);
						$("#crear_cupo_cr<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
						ErrorDeuda = true;*/
					}
					else
					{
						ErrorDeuda = false;
						
						if (Deuda > ValorCupo && $("#crear_cupo_cr_check<?php echo $NumOfID ?>").val() == true)
						{
							Alerts_Box("La Deuda del Cliente Supera al Cupo Credito Disponible.", 3);
							$("#crear_cupo_cr<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
							ErrorSaldo = true;
						}
						else if (Deuda <= ValorCupo && $("#crear_cupo_cr_check<?php echo $NumOfID ?>").val() == true)
						{
							if (total > (ValorCupo - Deuda))
							{
								Alerts_Box("El Valor de la Factura es Mayor que el Credito Disponible", 3);
								$("#crear_cupo_cr<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
								ErrorSaldo = true;
							}
							else
							{
								$("#crear_cupo_cr<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
								ErrorSaldo = false;
							}
						}
					}
				}
			}
		<?php
			}
		?>
			
			$("#crear_subtotal_total<?php echo $NumOfID ?>").val(subtotal);
			$("#crear_iva_precio<?php echo $NumOfID ?>").val(iva);
			$("#crear_total_total<?php echo $NumOfID ?>").val(total);
			$("#crear_pesokg<?php echo $NumOfID ?>").val(total_peso);
			if ("<?php echo $Interno ?>" == "")
			{
				$("#crear_codigo<?php echo $NumOfID ?>").jqxComboBox('focus');
			}
			clearTimeout(Calc_Timer);
		},200);
	};
	
	var ServicioSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'}
		],
		type: 'GET',
		data: {"OtroSrv":true},
		url: "modulos/parametros.php",
		async: true
	};
	var ServicioDataAdapter = new $.jqx.dataAdapter(ServicioSource);
	
	$("#crear_tipo_servicio<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 25,
		width: 180,
		source: ServicioDataAdapter,
		dropDownHeight: 70,
		promptText: 'Tipo Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Nombre'
	});
	$("#crear_tipo_servicio<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#crear_tipo_servicio<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#crear_tipo_servicio_precio<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$('#crear_tipo_servicio_precio<?php echo $NumOfID ?>').on('change', function (event) 
	{
		Calcular();
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("crear_tipo_servicio_precio");
		}
	});

	$("#crear_iva_text<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                     IVA",
	});
	
	$("#crear_iva_precio<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	
	var DescuentoSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'}
		],
		type: 'GET',
		data: {"TipoDcto":true},
		url: "modulos/parametros.php",
		async: true
	};
	var DescuentoDataAdapter = new $.jqx.dataAdapter(DescuentoSource);
	
	$("#crear_tipo_descuento<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 25,
		width: 180,
		source: DescuentoDataAdapter,
		dropDownHeight: 70,
		promptText: 'Tipo Descuento',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Nombre'
	});
	$("#crear_tipo_descuento<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#crear_tipo_descuento<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
		else
			Calcular(true);
	});
	
	$("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$('#crear_tipo_descuento_precio<?php echo $NumOfID ?>').on('change', function (event) 
	{
		Calcular();
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("crear_tipo_descuento_precio<?php echo $NumOfID ?>");
		}
	});
	
	$("#crear_total_text<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                  TOTAL",
	});
	
	$("#crear_total_total<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	
	function CrearPedido (Duplicar)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ID_Incorrecto) {
			Alerts_Box("La Orden de Compra Ingresada, ya Existe en el Sistema...<br/>Favor Ingresar Otra para Continuar.", 3);
			Locked = false;
			return;
		}
		
		if (ErrorDeuda) {
			Alerts_Box("El Cliente Posee Deudas Mayores a 90 Dias.", 3);
			Locked = false;
			return;
		}
		
		if (ErrorSaldo) {
			Alerts_Box("El Valor de la Factura es mayor al Saldo Disponible", 3);
			Locked = false;
			return;
		}
		
		// Se permite el Paso y en Despacho se puede colocar el RC
		/*
		if ($("#crear_formaP<?php echo $NumOfID ?>").val() == "Efectivo" && $("#crear_valor_rc<?php echo $NumOfID ?>").val() == 0)
		{
			Alerts_Box("Debe ingresar un Recibo de Caja.", 3);
			Locked = false;
			return;
		}
		*/
		
		var datinfo = $("#crear_products_grid<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		var Cliente = $("#crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var FormaPago = $("#crear_formaP<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Recibo = $("#crear_recibo<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Sector = $("#crear_sector<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Vendedor = $("#crear_vendedor<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Cobrador = $("#crear_cobrador<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Cajero = $("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		
		if (!Cliente)
		{
			Alerts_Box("Debe Ingresar un Cliente!", 3);
			WaitClick_Combobox("crear_cliente<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}

		if (count < 1)
		{
			Alerts_Box("Debe Ingresar un Producto!", 3);
			WaitClick_Combobox("crear_producto<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#crear_tipo_servicio_precio<?php echo $NumOfID ?>").val() > 0) {
			if ($("#crear_tipo_servicio<?php echo $NumOfID ?>").val() < 1 | $("#crear_tipo_servicio<?php echo $NumOfID ?>").val() == "") {
				Alerts_Box("Debe Ingresar Un Tipo de Servicio", 3);
				WaitClick_Combobox("crear_tipo_servicio<?php echo $NumOfID ?>");
				Locked = false;
				return;
			}
		}
		
		if ($("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val() > 0) {
			if ($("#crear_tipo_descuento<?php echo $NumOfID ?>").val() < 1 | $("#crear_tipo_descuento<?php echo $NumOfID ?>").val() == "") {
				Alerts_Box("Debe Ingresar Un Tipo de Descuento", 3);
				WaitClick_Combobox("crear_tipo_descuento<?php echo $NumOfID ?>");
				Locked = false;
				return;
			}
		}
		
		if (!FormaPago)
		{
			Alerts_Box("Debe Ingresar Una Forma de Pago!", 3);
			WaitClick_Combobox("crear_formaP<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Vendedor)
		{
			Alerts_Box("Debe Ingresar Un Vendedor!", 3);
			WaitClick_Combobox("crear_vendedor<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Cobrador)
		{
			Alerts_Box("Debe Ingresar Un Cobrador!", 3);
			WaitClick_Combobox("crear_cobrador<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Sector)
		{
			Alerts_Box("Debe Ingresar Un Sector!", 3);
			WaitClick_Combobox("crear_sector<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (Recibo && Cajero)
		{
			Alerts_Box("No puede seleccionar un Cajero y un Recibo de Caja a la vez.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (var i = 0; i < count; i++)
		{
			var array = {};
			var currentRow = $('#crear_products_grid<?php echo $NumOfID ?>').jqxGrid('getrowdata', i);
			
			if (currentRow.Cantidad > currentRow.Existencia && currentRow.FacturaExistencia == false && $("#crear_cotizar<?php echo $NumOfID ?>").val() == false) {
				Alerts_Box("Uno o mas Productos tienen una cantidad mayor a la existencia!", 3);
				WaitClick();
				Locked = false;
				return;
			}
			
			array["CodFab"] = currentRow.CodFab;
			array["Cantidad"] = currentRow.Cantidad;
			array["Dcto"] = currentRow.Dcto;
			array["Unitario"] = currentRow.Unitario;
			array["Produccion"] = currentRow.Produccion;
			
			if (i==0) {
				if (currentRow.Produccion == true) {
					array["Tipo_Pedido"] = "Produccion";
				}
				else {
					array["Tipo_Pedido"] = "Pedido";
				}
				array["Duplicar"] = Duplicar;
				array["ClienteID"] = Cliente.value;
				array["Fecha"] = GetFormattedDate($('#crear_fecha_rem<?php echo $NumOfID ?>').jqxDateTimeInput('getDate'));
				array["Remision"] = $("#crear_remision<?php echo $NumOfID ?>").val();
				array["Ord_Compra"] = $("#crear_ord_compra<?php echo $NumOfID ?>").val();
				array["Factura"] = $("#crear_factura<?php echo $NumOfID ?>").val();
				array["BeneficiarioID"] = $("#crear_beneficiario_ID<?php echo $NumOfID ?>").val();
				//---
				array["Observaciones"] = $('#crear_observaciones<?php echo $NumOfID ?>').val();
				array["Subtotal2"] = $("#crear_subtotal_total<?php echo $NumOfID ?>").val();
				array["TipoServicio"] = $("#crear_tipo_servicio<?php echo $NumOfID ?>").val();
				array["TipoServicioValor"] = $("#crear_tipo_servicio_precio<?php echo $NumOfID ?>").val();
				array["Iva"] = $("#crear_iva_precio<?php echo $NumOfID ?>").val();
				array["TipoDcto"] = $("#crear_tipo_descuento<?php echo $NumOfID ?>").val();
				array["TipoDctoValor"] = $("#crear_tipo_descuento_precio<?php echo $NumOfID ?>").val();
				array["Total"] = $("#crear_total_total<?php echo $NumOfID ?>").val();
				array["Chofer"] = $("#crear_conductor<?php echo $NumOfID ?>").val();
				array["Placa"] = $("#crear_placa<?php echo $NumOfID ?>").val();
				array["Peso"] = $("#crear_pesokg<?php echo $NumOfID ?>").val();
				array["FormaPago"] = FormaPago.value;
				array["Ruta"] = $("#crear_ruta<?php echo $NumOfID ?>").val();
				array["Caja_Interno"] = Recibo ? Recibo.value:"";
				array["Caja_Recibo"] = Recibo ? Recibo.label:"";
				array["Direccion"] = $("#crear_dir_entrega<?php echo $NumOfID ?>").val();
				array["ValorRC"] = $("#crear_valor_rc<?php echo $NumOfID ?>").val();
				array["VendedorID"] = Vendedor.value;
				array["CobradorID"] = Cobrador.value;
				array["CajeroID"] = Cajero ? Cajero.value:"";
				array["Saldo"] = $("#crear_saldo<?php echo $NumOfID ?>").val();
				if ($("#crear_cotizar<?php echo $NumOfID ?>").val() == false)
					array["Estado"] = Duplicar ? "Cotizacion":"Creado";
				else
					array["Estado"] = "Cotizacion";
			}
			myarray[i] = array;
		};
		
		/*
		alert(JSON.stringify(myarray))
		Locked = false;
		return;
		*/
		
		if (Cajero)
		{
			Alerts_Box("Desea Crear un Recibo de Caja con el Cajero Seleccionado?", 1, true);
			var Ventas_Crear_CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(Ventas_Crear_CheckTimer);
					ClickOK = false;
					$("#Loading_Mess").html("Procesando Solicitud...");
					$("#Loading").show();
					$.ajax({
						dataType: 'json',
						type: 'POST',
						url: "modulos/guardar.php",
						data: {"Ventas_Crear":myarray},
						async: true,
						success: function (data)
						{
							ReDefine();
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							ClickOK = false;
							ClickCANCEL = false;
							document.getElementById("ok").value = "Nuevo";
							document.getElementById("cancel").value = "Aceptar";
							Duplicar ? 
							Alerts_Box("Remision Duplicada con Exito!<br/>Codigo Interno Generado = \""+data[0]["Interno"]+"\"", 2, true)
							:
							Alerts_Box("Datos Guardados con Exito!<br/>Codigo Interno Generado = \""+data[0]["Interno"]+"\"", 2, true);
							$("#crear_interno<?php echo $NumOfID ?>").val(data[0]["Interno"]);
							$("#crear_ord_produccion<?php echo $NumOfID ?>").val(data[0]["Ord_Produccion"]);
							if (data[0]["Caja_Interno"] != "")
								$("#crear_notas<?php echo $NumOfID ?>").val("RC Generado: "+data[0]["Caja_Interno"]);
							
							EnableDisableAll(true);
							Timer1 = setInterval(function(){
								if (ClickOK == true)
								{
									document.getElementById("ok").value = "Aceptar";
									document.getElementById("cancel").value = "Cancelar";
									ClearDocument();
									clearInterval(Timer1);
									clearTimeout(Timer2);
								}
								else if (ClickCANCEL == true)
								{
									document.getElementById("ok").value = "Aceptar";
									document.getElementById("cancel").value = "Cancelar";
									clearInterval(Timer1);
									clearTimeout(Timer2);
									Locked = false;
								}
							},10);
							Timer2 = setTimeout(function(){
								$("#Ask_Alert").jqxWindow('close');
								document.getElementById("ok").value = "Aceptar";
								document.getElementById("cancel").value = "Cancelar";
								clearInterval(Timer1);
								ClickOK = false;
								ClickCANCEL = true;
								Locked = false;
							},5000);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							Locked = false;
							Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
						}
					});
				}
				if (ClickCANCEL == true)
				{
					clearInterval(Ventas_Crear_CheckTimer);
					ClickCANCEL = false;
					Locked = false;
					$("#crear_cajero<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				}
			}, 10);
		}
		else
		{
			$("#Loading_Mess").html("Procesando Solicitud...");
			$("#Loading").show();
			$.ajax({
				dataType: 'json',
				type: 'POST',
				url: "modulos/guardar.php",
				data: {"Ventas_Crear":myarray},
				async: true,
				success: function (data)
				{
					ReDefine();
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					ClickOK = false;
					ClickCANCEL = false;
					document.getElementById("ok").value = "Nuevo";
					document.getElementById("cancel").value = "Aceptar";
					Duplicar ? 
					Alerts_Box("Remision Duplicada con Exito!<br/>Codigo Interno Generado = \""+data[0]["Interno"]+"\"", 2, true)
					:
					Alerts_Box("Datos Guardados con Exito!<br/>Codigo Interno Generado = \""+data[0]["Interno"]+"\"", 2, true);
					$("#crear_interno<?php echo $NumOfID ?>").val(data[0]["Interno"]);
					$("#crear_ord_produccion<?php echo $NumOfID ?>").val(data[0]["Ord_Produccion"]);
					EnableDisableAll(true);
					Timer1 = setInterval(function(){
						if (ClickOK == true)
						{
							document.getElementById("ok").value = "Aceptar";
							document.getElementById("cancel").value = "Cancelar";
							ClearDocument();
							clearInterval(Timer1);
							clearTimeout(Timer2);
						}
						else if (ClickCANCEL == true)
						{
							document.getElementById("ok").value = "Aceptar";
							document.getElementById("cancel").value = "Cancelar";
							clearInterval(Timer1);
							clearTimeout(Timer2);
							Locked = false;
						}
					},10);
					Timer2 = setTimeout(function(){
						$("#Ask_Alert").jqxWindow('close');
						document.getElementById("ok").value = "Aceptar";
						document.getElementById("cancel").value = "Cancelar";
						clearInterval(Timer1);
						ClickOK = false;
						ClickCANCEL = true;
						Locked = false;
					},5000);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
				}
			});
		}
	};
	
	$("#crear_guardar<?php echo $NumOfID ?>").jqxButton({ width: 140, template: "info" });
	// Prepare Save Changes...
	$("#crear_guardar<?php echo $NumOfID ?>").bind('click', function ()
	{
		CrearPedido(false);
	});
	
	$("#crear_imprimir<?php echo $NumOfID ?>").jqxButton({width: 140, template: "warning"});
	$("#crear_imprimir<?php echo $NumOfID ?>").bind('click', function ()
	{
		window.open("imprimir/factura.php?Interno="+$("#crear_interno<?php echo $NumOfID ?>").val()+"", "", "width=700, height=600, menubar=no, titlebar=no");
	});
	
	$("#crear_nuevo<?php echo $NumOfID ?>").jqxButton({width: 140, template: "success"});
	$("#crear_nuevo<?php echo $NumOfID ?>").bind('click', function ()
	{
		ClearDocument();
	});
	
	$("#crear_client_benef<?php echo $NumOfID ?>").jqxButton({width: 140, template: "warning"});
	$("#crear_client_benef<?php echo $NumOfID ?>").bind('click', function ()
	{
		$("#Crear_Tercero_Window<?php echo $NumOfID ?>").jqxWindow('open');
	});
	
	$("#crear_ver_historicos<?php echo $NumOfID ?>").jqxButton({width: 140, template: "danger"});
	$("#crear_ver_historicos<?php echo $NumOfID ?>").bind('click', function ()
	{
		$("#Ventas_Crear_Cliente_Historicos<?php echo $NumOfID ?>").jqxWindow('open');
	});
	
	if ("<?php echo $Interno ?>" != "")
	{
		$("#crear_duplicar<?php echo $NumOfID ?>").jqxButton({width: 140, template: "inverse"});
		$("#crear_duplicar<?php echo $NumOfID ?>").bind('click', function ()
		{
			CrearPedido(true);
		});
	}
	// ------------------------------------------ PARTE 4
	// prepare data
	if ("<?php echo $Interno ?>" == "")
	{
		function Cargar_Cartera ()
		{
			var ValoresSource =
			{
				datatype: "json",
				datafields: [
					{ name: 'DeudaTotal', type: 'decimal'},
					{ name: 'Corriente', type: 'decimal'},
					{ name: 'De30a45', type: 'decimal'},
					{ name: 'De45a60', type: 'decimal'},
					{ name: 'De60a90', type: 'decimal'},
					{ name: 'Mas90', type: 'decimal'},
				],
				type: 'GET',
				data: {"Cargar_Cartera":$("#crear_cliente_ID<?php echo $NumOfID ?>").val()},
				url: "modulos/datos.php",
				async: true,
			};
			var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
				autoBind: true,
				loadComplete: function () {
					var records = ValoresDataAdapter.records;
					$("#ventas_crear_deuda_total<?php echo $NumOfID ?>").val(records[0]["DeudaTotal"]);
					$("#ventas_crear_corriente<?php echo $NumOfID ?>").val(records[0]["Corriente"]);
					$("#ventas_crear_de_30_a_45<?php echo $NumOfID ?>").val(records[0]["De30a45"]);
					$("#ventas_crear_de_45_a_60<?php echo $NumOfID ?>").val(records[0]["De45a60"]);
					$("#ventas_crear_de_60_a_90<?php echo $NumOfID ?>").val(records[0]["De60a90"]);
					$("#ventas_crear_mas_de_90<?php echo $NumOfID ?>").val(records[0]["Mas90"]);
					//
					//$("#crear_fecha_rem<?php echo $NumOfID ?>").jqxDateTimeInput('focus');
					// Clear Values Variables and Classes
					ErrorSaldo = false;
					// Clean Classes
					$("#crear_cupo_cr<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
					$("#crear_valor_rc<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
					Calcular(true);
				}
			});
		};
		
		$("#ventas_crear_deuda_total<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 180,
			disabled: true,
			textAlign: 'right',
			symbol: '$',
			digits: 18,
			max: 999999999999999999,
		});

		$("#ventas_crear_corriente<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 150,
			disabled: true,
			textAlign: 'right',
			symbol: '$',
			digits: 15,
			max: 999999999999999,
		});

		$("#ventas_crear_de_30_a_45<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 150,
			disabled: true,
			textAlign: 'right',
			symbol: '$',
			digits: 15,
			max: 999999999999999,
		});

		$("#ventas_crear_de_45_a_60<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 150,
			disabled: true,
			textAlign: 'right',
			symbol: '$',
			digits: 15,
			max: 999999999999999,
		});

		$("#ventas_crear_de_60_a_90<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 150,
			disabled: true,
			textAlign: 'right',
			symbol: '$',
			digits: 15,
			max: 999999999999999,
		});

		$("#ventas_crear_mas_de_90<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 150,
			disabled: true,
			textAlign: 'right',
			symbol: '$',
			digits: 15,
			max: 999999999999999,
		});
	}
	
	// ------------------------------------------ WINDOWS
	//--- Crear Terceros
	$("#Crear_Tercero_Window<?php echo $NumOfID ?>").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 900,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				data: {"Crear_Emergente":true},
				url: "terceros/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Crear_Tercero_Content<?php echo $NumOfID ?>").html(data);
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
	});
	
	//--- Cliente Historicos
	$("#Ventas_Crear_Cliente_Historicos<?php echo $NumOfID ?>").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: "100%",
		width: "100%",
		maxWidth: 530,
		maxHeight: 390,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Ventas_Crear_Cliente_Historicos<?php echo $NumOfID ?>").on('open', function (event)
	{
		var HistoricosSource =
		{
			datatype: "json",
			datafields:
			[
				{ name: 'Codigo', type: 'string' },
				{ name: 'Nombre', type: 'string' },
				{ name: 'Precio', type: 'string' },
			],
			data: {"Ventas_Cliente_Historicos":$("#crear_cliente<?php echo $NumOfID ?>").val()},
			url: "modulos/datos.php",
			async: true
		};
		var HistoricosDataAdapter = new $.jqx.dataAdapter(HistoricosSource);
		
		$("#crear_cliente_historicos_grid<?php echo $NumOfID ?>").jqxGrid({
			theme: mytheme,
			width: 500,
			source: HistoricosDataAdapter,
			sortable: true,
			pageable: true,
			autoheight: true,
			editable: false,
			filterable: true,
			showfilterrow: true,
			editmode: 'click',
			columns:
			[
				{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '20%', height: 20 },
				{ text: 'Nombre', datafield: 'Nombre', editable: false, width: '60%', height: 20 },
				{ text: 'Precio', datafield: 'Precio', editable: false, filterable: false, width: '20%', height: 20, cellsalign: 'right' },
			]
		});
	});
	
	if ("<?php echo $Interno ?>" != "")
	{
		EnableDisableAll(true);
		$("#crear_duplicar<?php echo $NumOfID ?>").jqxButton({ disabled: false });
		LoadValues();
	}
	else
		CheckRefresh();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "false")
					{
			?>
						$("#crear_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#crear_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
				}
			}
		} ?>
});
</script>
<style type="text/css">
	#Crear_HideButton<?php echo $NumOfID ?> {
		width: 21px;
		height: 21px;
		padding-top: 2px;
		padding-right: 2px;
		float: left;
		display: block;
		border: 1px solid #A4BED4;
		font-size:15px;
		font-weight:700;
		margin-right: 10px;
		color: #FFF;
		position: absolute;
		background-color: #5BB75B;
		cursor: pointer;
	}

	#Crear_HideButton<?php echo $NumOfID ?>:hover {
		background: #79CF79;
	}
	
	#Crear_Content_to_Hide<?php echo $NumOfID ?> {
		float: left;
		margin-left: 35px;
		padding-bottom: 10px;
	}
</style>
<div id="Crear_Tercero_Window<?php echo $NumOfID ?>">
	<div id="Crear_Tercero_Title<?php echo $NumOfID ?>" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 340px;">Crear Cliente/Tercero</div>
	</div>
	<div id="Crear_Tercero_Content<?php echo $NumOfID ?>" class="WindowContainer">
	</div>
</div>
<div id="Ventas_Crear_Cliente_Historicos<?php echo $NumOfID ?>">
	<div id="Ventas_Crear_Cliente_Historicos_Title<?php echo $NumOfID ?>" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 160px;">Historicos de Cliente</div>
	</div>
	<div id="Ventas_Crear_Cliente_Historicos_Content<?php echo $NumOfID ?>" class="WindowContainer">
		<div id="crear_cliente_historicos_grid<?php echo $NumOfID ?>"></div>
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<div id="Crear_HideButton<?php echo $NumOfID ?>">&nbsp;&raquo;&nbsp;</div>
	<!--<input type="button" id="crear_HideButton" value="Guardar"/>-->
	<div id="Crear_Content_to_Hide<?php echo $NumOfID ?>">
		<table cellpadding="1" cellspacing="1">
			<tr>
				<td>
					Ord. Compra
				</td>
				<td>
					<input type="text" id="crear_ord_compra<?php echo $NumOfID ?>"/>
				</td>
				<td>
					Cliente
				</td>
				<td colspan="3">
					<div id="crear_cliente<?php echo $NumOfID ?>"></div>
				</td>
				<td>
					ID Cliente
				</td>
				<td colspan="3">
					<div id="crear_cliente_ID<?php echo $NumOfID ?>"></div>
				</td>
			</tr>
			<tr>
				<td>
					F&eacute;cha Rem.
				</td>
				<td>
					<div id="crear_fecha_rem<?php echo $NumOfID ?>"></div>
				</td>
				<td>
					Direcci&oacute;n
				</td>
				<td colspan="2">
					<input type="text" id="crear_direccion<?php echo $NumOfID ?>"/>
				</td>
				<td>
					<li class="parte1_li_txt">
						Telf.&nbsp;
					</li>
					<li>
						<input type="text" id="crear_telefono<?php echo $NumOfID ?>"/>
					</li>
				</td>
				<td>
					C&uacute;po Cr.
				</td>
				<td colspan="3">
					<li>
						<div id="crear_cupo_cr<?php echo $NumOfID ?>"></div>
					</li>
					<li>
						<div id="crear_cupo_cr_check<?php echo $NumOfID ?>"></div>
					</li>
				</td>
			</tr>
			<tr>
				<td>
					Remisi&oacute;n
				</td>
				<td>
					<input type="text" id="crear_remision<?php echo $NumOfID ?>"/>
				</td>
				<td>
					Interno
				</td>
				<td>
					<input type="text" id="crear_interno<?php echo $NumOfID ?>"/>
				</td>
				<!--<td>
					E-Mail
				</td>-->
				<td>
					<input type="text" id="crear_e-mail<?php echo $NumOfID ?>"/>
				</td>
				<!--<td>
					Contacto P.
				</td>-->
				<td>
					<input type="text" id="crear_contacto_p<?php echo $NumOfID ?>"/>
				</td>
				<td>
					Estado
				</td>
				<td>
					<input type="text" id="crear_estado<?php echo $NumOfID ?>"/>
				</td>
				<td>
					Gar./Docs.
				</td>
				<td>
					<input type="text" id="crear_gar_docs<?php echo $NumOfID ?>"/>
				</td>
			</tr>
			<tr>
				<td>
					Factura
				</td>
				<td>
					<input type="text" id="crear_factura<?php echo $NumOfID ?>"/>
				</td>
				<td>
					Ord. Prod.
				</td>
				<td>
					<input type="text" id="crear_ord_produccion<?php echo $NumOfID ?>"/>
				</td>
				<td colspan="2">
					<li class="parte1_li_txt">
						Beneficiario&nbsp;
					</li>
					<li>
						<div id="crear_beneficiario<?php echo $NumOfID ?>"></div>
					</li>
				</td>
				<td>
					ID Beneficiario
				</td>
				<td colspan="3">
					<div id="crear_beneficiario_ID<?php echo $NumOfID ?>"></div>
				</td>
			</tr>
		</table>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<?php
	if ($Interno == "")
	{
	?>
	<form id="crear_form_validation<?php echo $NumOfID ?>" action="./">
		<table cellpadding="0" cellspacing="0" style="margin:10px 0px 10px 0px; border: 1px solid #A4BED4; text-align: center;">
			<tr style="background: #E0E9F5">
				<td style="border-bottom: 1px solid #A4BED4;">
					Codigo
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Producto
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Existencia
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Cantidad
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Lista P.
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Cotizar
				</td>
			</tr>
			<tr>
				<td>
					<div id="crear_codigo<?php echo $NumOfID ?>" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="crear_producto<?php echo $NumOfID ?>" style="margin-left:7px;"></div>
				</td>
				<td>
					<input type="text" id="crear_existencia<?php echo $NumOfID ?>" style="margin-left:7px;"/>
				</td>
				<td>
					<div id="crear_cantidad<?php echo $NumOfID ?>" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="crear_listap<?php echo $NumOfID ?>" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="crear_cotizar<?php echo $NumOfID ?>" style="margin-left:7px; padding:0px 7px 0px 0px;"></div>
				</td>
			</tr>
		</table>
	</form>
	<?php
	}
	?>
	<div id="crear_products_grid<?php echo $NumOfID ?>" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="0" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				Observaciones:
			</td>
			<td style="padding-left:15px;">
				Notas:
			</td>
			<td>
				<input type="text" id="crear_subtotal_text<?php echo $NumOfID ?>" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="crear_subtotal_total<?php echo $NumOfID ?>">
				</div>
			</td>
		</tr>
		<tr>
			<?php
			if (isset($_POST["Crear_Emergente"]))
			{
			?>
			<td rowspan="2">
				<textarea rows="3" cols="30" id="crear_observaciones<?php echo $NumOfID ?>" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td rowspan="2">
				<textarea rows="3" cols="30" id="crear_notas<?php echo $NumOfID ?>" readonly="true" style="resize:none; margin-left:15px;"></textarea>
			</td>
			<?php
			}
			else
			{
			?>
			<td rowspan="4">
				<textarea rows="7" cols="35" id="crear_observaciones<?php echo $NumOfID ?>" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td rowspan="4">
				<textarea rows="7" cols="25" id="crear_notas<?php echo $NumOfID ?>" readonly="true" style="resize:none; margin-left:15px;"></textarea>
			</td>
			<?php
			}
			?>
			<td>
				<div id="crear_tipo_servicio<?php echo $NumOfID ?>" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="crear_tipo_servicio_precio<?php echo $NumOfID ?>">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="crear_iva_text<?php echo $NumOfID ?>" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="crear_iva_precio<?php echo $NumOfID ?>">
				</div>
			</td>
		</tr>
		<tr>
			<?php
			if (isset($_POST["Crear_Emergente"]))
			{
			?>
			<td colspan="2">
				<li class="parte1_li_txt" style="width:25px;">
					Dig.
				</li>
				<li>
					<input type="text" id="crear_digitado_por<?php echo $NumOfID ?>"/>
				</li>
				<li>
					<div id="crear_digitado_fecha<?php echo $NumOfID ?>"></div>
				</li>
				<li style="margin-left:15px; width:30px;" class="parte1_li_txt">
					Mod.
				</li>
				<li>
					<input type="text" id="crear_modificado_por<?php echo $NumOfID ?>"/>
				</li>
				<li>
					<div id="crear_modificado_fecha<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<?php
			}
			?>
			<td>
				<div id="crear_tipo_descuento<?php echo $NumOfID ?>" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="crear_tipo_descuento_precio<?php echo $NumOfID ?>">
				</div>
			</td>
		</tr>
		<tr>
			<?php
			if (isset($_POST["Crear_Emergente"]))
			{
			?>
			<td colspan="2">
				<li class="parte1_li_txt" style="width:25px;">
					Apr.
				</li>
				<li>
					<input type="text" id="crear_aprobado_por<?php echo $NumOfID ?>"/>
				</li>
				<li>
					<div id="crear_aprobado_fecha<?php echo $NumOfID ?>"></div>
				</li>
				<li style="margin-left:15px; width:30px;" class="parte1_li_txt">
					Desp.
				</li>
				<li>
					<input type="text" id="crear_despachado_por<?php echo $NumOfID ?>"/>
				</li>
				<li>
					<div id="crear_despachado_fecha<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<?php
			}
			?>
			<td>
				<input type="text" id="crear_total_text<?php echo $NumOfID ?>" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="crear_total_total<?php echo $NumOfID ?>">
				</div>
			</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="2" style="margin:5px 0px 10px 0px;">
		<tr>
			<td>
				Conductor
			</td>
			<td>
				<div id="crear_conductor<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Placa:
			</td>
			<td>
				<div id="crear_placa<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Peso
			</td>
			<td>
				<div id="crear_pesokg<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				F. Pago
			</td>
			<td>
				<div id="crear_formaP<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<input type="button" id="crear_guardar<?php echo $NumOfID ?>" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="crear_imprimir<?php echo $NumOfID ?>" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<td>
				Vendedor
			</td>
			<td colspan="5">
				<li>
					<div id="crear_vendedor<?php echo $NumOfID ?>"></div>
				</li>
				<li class="parte1_li_txt">
					Cobrador
				</li>
				<li>
					<div id="crear_cobrador<?php echo $NumOfID ?>"></div>
				</li>
				<li class="parte1_li_txt">
					Cajero
				</li>
				<li>
					<div id="crear_cajero<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<td>
				R. Caja
			</td>
			<td>
				<div id="crear_recibo<?php echo $NumOfID ?>">
				</div>
			</td>
			<td>
				<input type="button" id="crear_nuevo<?php echo $NumOfID ?>" value="Nuevo"/>
			</td>
			<td>
				<input type="button" id="crear_client_benef<?php echo $NumOfID ?>" value="Crear Client/Benef."/>
			</td>
		</tr>
		<tr>
			<td>
				Dir. Entrega
			</td>
			<td colspan="5">
				<input type="text" id="crear_dir_entrega<?php echo $NumOfID ?>"/>
			</td>
			<td>
				Valor RC
			</td>
			<td>
				<div id="crear_valor_rc<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<input type="button" id="crear_ver_historicos<?php echo $NumOfID ?>" value="Ver Historicos"/>
			</td>
			<td>
				<?php
				if ($Interno != "")
				{
				?>
				<input type="button" id="crear_duplicar<?php echo $NumOfID ?>" value="Duplicar-Cotizar"/>
				<?php
				}
				else
				{
				?>
				&nbsp;
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<td>
				Sector
			</td>
			<td colspan="3">
				<div id="crear_sector<?php echo $NumOfID ?>">
				</div>
			</td>
			<td>
				Ruta:
			</td>
			<td>
				<input type="text" id="crear_ruta<?php echo $NumOfID ?>"/>
			</td>
			<td style="margin-left:15px;">
				Saldo
			</td>
			<td>
				<div id="crear_saldo<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 4 -->
<?php
if ($Interno == "")
{
?>
<div id="Parte4">
	<table cellpadding="2" cellspacing="0" style="margin-top:20px;">
		<tr style="text-align:center; ">
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#AAA; padding:4px 0px;">
				Deuda Total
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#CFE6C0; padding:4px 0px;">
				Corriente
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#E6DFC0; padding:4px 0px;">
				30 a 45
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#E6CFC0; padding:4px 0px;">
				45 a 60
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#E2A0A0; padding:4px 0px;">
				60 a 90
			</td>
			<td style="border:1px #888 solid; background:#D45555; padding:4px 0px;">
				M&aacute;s de 90
			</td>
		</tr>
		<tr>
			<td>
				<div id="ventas_crear_deuda_total<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="ventas_crear_corriente<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="ventas_crear_de_30_a_45<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="ventas_crear_de_45_a_60<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="ventas_crear_de_60_a_90<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="ventas_crear_mas_de_90<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
	</table>
</div>
<?php
}
?>