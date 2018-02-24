<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	var mytheme2 = "darkblue";

	//---- GLOBAL VARIABLES
	var cantidad_exedida = false;
	var ExistenciaCargada = false;
	var ID_OrdenCompra = "";
	var ID_Incorrecto = false;
	var Caja_Interno = "";
	var Caja_Recibo = "";
	var Caja_Recibo_Original = "";
	var Cargar_Recibo = false;
	var ErrorDeuda = false;
	var ErrorSaldo = false;
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Ventas_Content");
	var Body = document.getElementById("Modificar_Content");
	var Times = 0;
	var Refresh = 0;
	var Hide = 0;
	
	function CheckRefresh ()
	{
		clearInterval(Refresh);
		Refresh = setInterval(function()
		{
			if (Times <= 0 && (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none")) {
				Times++;
			}
			else if (Times > 0 && jQuery.css( Body, "display" ) === "block" && jQuery.css( Main, "display" ) === "block")
			{
				//Code... do something?
				SystemMap("Modificar", true);
				// Clear & Disables
				ReDefine();
				ClearDocument();
				// Buscar Ordenes
				OrdenSource.data = {"Ventas_Modificar":true},
				OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
				$("#modificar_ord_compra").jqxComboBox({source: OrdenAdapter});
				//---
				clearInterval(Refresh);
				CheckHide();
			}
		},500);
	};
	
	function CheckHide ()
	{
		clearInterval(Hide);
		Hide = setInterval(function()
		{
			if (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none") {
				CheckRefresh();
				clearInterval(Hide);
			}
		},200);
	};
	// END - Code for Refresh Data
	
	var Admin = false;
	var Guardar = false;
	var Imprimir = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Imprimir"] == "true")
				{
		?>
					Imprimir = true;
		<?php
				}
			}
		}
		else
		{
		?>
			Admin = true;
		<?php	
		}
	}
	?>
	
	function ClearDocument(val)
	{
		ReDefine();
		// Clean Variables
		cantidad_exedida = false;
		ExistenciaCargada = false;
		ID_OrdenCompra = "";
		ID_Incorrecto = false;
		Caja_Interno = "";
		Caja_Recibo = "";
		Caja_Recibo_Original = "";
		Cargar_Recibo = false;
		ErrorDeuda = false;
		ErrorSaldo = false;
		Locked = false;
		// Clean Classes
		$("#modificar_cupo_cr").removeClass("jqx-validator-error-element");
		var Classes = document.getElementById("modificar_estado").className.split(/\s+/);
		for (i = 0; i < Classes.length; i ++)
		{
			switch (Classes[i])
			{
				case "GreenState":
					$("#modificar_estado").removeClass("GreenState");
				break;
				case "RedState":
					$("#modificar_estado").removeClass("RedState");
				break;
				case "VioletState":
					$("#modificar_estado").removeClass("VioletState");
				break;
			}
		}
		var Classes = document.getElementById("modificar_gar_docs").className.split(/\s+/);
		for (i = 0; i < Classes.length; i ++)
		{
			switch (Classes[i])
			{
				case "GreenState":
					$("#modificar_gar_docs").removeClass("GreenState");
				break;
				case "OrangeState":
					$("#modificar_gar_docs").removeClass("OrangeState");
				break;
			}
		}
		//
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		if (!Admin && !Guardar)
		{
			$("#modificar_guardar").jqxButton({ disabled: true });
			$("#modificar_cotizar").jqxButton({ disabled: true });
			$("#modificar_anular").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#modificar_imprimir").jqxButton({ disabled: true });
		}
	};
	
	function ReDefine()
	{
		ClearJSON = [
			//-1
			{id:"modificar_ord_compra", type:"jqxComboBox"},
			{id:"modificar_cliente", type:"jqxComboBox"},
			{id:"modificar_cliente_ID", type:"jqxComboBox"},
			{id:"modificar_direccion", type:""},
			{id:"modificar_telefono", type:""},
			{id:"modificar_cupo_cr", type:""},
			{id:"modificar_cupo_cr_check", type:"jqxCheckBox"},
			{id:"modificar_cupo_cr_check2", type:"jqxCheckBox"},
			{id:"modificar_remision", type:""},
			{id:"modificar_interno", type:""},
			{id:"modificar_e-mail", type:""},
			{id:"modificar_contacto_p", type:""},
			{id:"modificar_estado", type:""},
			{id:"modificar_gar_docs", type:""},
			{id:"modificar_factura", type:""},
			{id:"modificar_ord_produccion", type:""},
			{id:"modificar_beneficiario", type:"jqxComboBox"},
			{id:"modificar_beneficiario_ID", type:"jqxComboBox"},
			//-2
			{id:"modificar_codigo", type:"jqxComboBox"},
			{id:"modificar_producto", type:"jqxComboBox"},
			{id:"modificar_existencia", type:""},
			{id:"modificar_cantidad", type:""},
			{id:"modificar_listap", type:"jqxDropDownList"},
			{id:"modificar_products_grid", type:"jqxGrid"},
			//-3
			{id:"modificar_observaciones", type:""},
			{id:"modificar_motivo_anular", type:""},
			{id:"modificar_notas", type:""},
			{id:"modificar_subtotal_total", type:""},
			{id:"modificar_tipo_servicio", type:"jqxComboBox"},
			{id:"modificar_tipo_servicio_precio", type:""},
			{id:"modificar_tipo_descuento", type:"jqxComboBox"},
			{id:"modificar_tipo_descuento_precio", type:""},
			{id:"modificar_iva_precio", type:""},
			{id:"modificar_total_total", type:""},
			{id:"modificar_conductor", type:"jqxComboBox"},
			{id:"modificar_placa", type:"jqxComboBox"},
			{id:"modificar_formaP", type:"jqxComboBox"},
			{id:"modificar_vendedor", type:"jqxComboBox"},
			{id:"modificar_cobrador", type:"jqxComboBox"},
			{id:"modificar_pesokg", type:""},
			{id:"modificar_recibo", type:"jqxComboBox"},
			{id:"modificar_dir_entrega", type:""},
			{id:"modificar_valor_rc", type:""},
			{id:"modificar_sector", type:"jqxComboBox"},
			{id:"modificar_ruta", type:""},
			{id:"modificar_saldo", type:""},
			{id:"modificar_estado_remision", type:""},
			//-4
			{id:"ventas_modificar_deuda_total", type:""},
			{id:"ventas_modificar_corriente", type:""},
			{id:"ventas_modificar_de_30_a_45", type:""},
			{id:"ventas_modificar_de_45_a_60", type:""},
			{id:"ventas_modificar_de_60_a_90", type:""},
			{id:"ventas_modificar_mas_de_90", type:""},
		];

		EnableDisableJSON = [
			//-1
			{id:"modificar_cliente", type:"jqxComboBox"},
			{id:"modificar_cliente_ID", type:"jqxComboBox"},
			//{id:"modificar_fecha_rem", type:"jqxDateTimeInput"},
			{id:"modificar_remision", type:""},
			{id:"modificar_factura", type:""},
			{id:"modificar_beneficiario", type:"jqxComboBox"},
			{id:"modificar_beneficiario_ID", type:"jqxComboBox"},
			//-2
			{id:"modificar_codigo", type:"jqxComboBox"},
			{id:"modificar_producto", type:"jqxComboBox"},
			{id:"modificar_cantidad", type:"jqxNumberInput"},
			{id:"modificar_listap", type:"jqxDropDownList"},
			{id:"ventas_modificar_addrowbutton", type:"jqxButton"},
			{id:"ventas_modificar_deleterowbutton", type:"jqxButton"},
			{id:"ventas_modificar_addrowbutton", type:"jqxButton"},
			{id:"ventas_modificar_deleterowbutton", type:"jqxButton"},
			//-3
			{id:"modificar_subtotal_total", type:"jqxNumberInput"},
			{id:"modificar_tipo_servicio", type:"jqxComboBox"},
			{id:"modificar_tipo_servicio_precio", type:"jqxNumberInput"},
			{id:"modificar_tipo_descuento", type:"jqxComboBox"},
			{id:"modificar_tipo_descuento_precio", type:"jqxNumberInput"},
			{id:"modificar_iva_precio", type:"jqxNumberInput"},
			{id:"modificar_total_total", type:"jqxNumberInput"},
			{id:"modificar_conductor", type:"jqxComboBox"},
			{id:"modificar_placa", type:"jqxComboBox"},
			{id:"modificar_formaP", type:"jqxComboBox"},
			{id:"modificar_vendedor", type:"jqxComboBox"},
			{id:"modificar_cobrador", type:"jqxComboBox"},
			{id:"modificar_dir_entrega", type:""},
			{id:"modificar_sector", type:"jqxComboBox"},
			{id:"modificar_guardar", type:"jqxButton"},
			//id:"modificar_imprimir", type:"jqxButton"},
			//{id:"modificar_ver_historicos", type:"jqxButton"},
			{id:"modificar_cotizar", type:"jqxButton"},
			{id:"modificar_anular", type:"jqxButton"},
		];
	}
	ReDefine();
	
	$("#modificar_fecha_rem").jqxDateTimeInput({
		theme: mytheme,
		width: 110,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
		disabled: true
	});
	$("#modificar_fecha_rem").jqxDateTimeInput('setDate', new Date(currenttime));
	// prepare the data
	var OrdenSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'OrdenCompra', type: 'string'},
			{ name: 'Interno', type: 'string'}
		],
		type: 'GET',
		//data: {"Cargar_Ordenes_de_Compra":true},
		url: "modulos/datos.php",
		async: true
	};
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'},
		{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
		async: true
	};
	var ClienteDataAdapter1 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter2 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter3 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter4 = new $.jqx.dataAdapter(ClienteSource);
	
	// Clean some Fields
	function ClearFields (unchange)
	{
		if (unchange == 1){
			$("#modificar_direccion").jqxComboBox('clearSelection');
			$("#modificar_telefono").val('');
			$("#modificar_cupo_cr").val('');
			$("#modificar_cupo_cr_check").jqxCheckBox('uncheck');
			$("#modificar_cupo_cr_check2").jqxCheckBox('uncheck');
			$("#modificar_contacto_p").val('');
			$("#modificar_estado").val('');
			$("#modificar_gar_docs").val('');
			//$("#modificar_remision").val('');
			//$("#modificar_ord_produccion").jqxComboBox('clearSelection');
			//$("#modificar_factura").val('');
			//$("#modificar_interno").val('');
			$("#modificar_beneficiario").jqxComboBox('clearSelection');
			$("#modificar_beneficiario_ID").jqxComboBox('clearSelection');
			//$("#modificar_cobrador").jqxComboBox('clearSelection');
			$("#modificar_valor_rc").val('');
			$("#modificar_saldo").val('');
		}
		else if (unchange == 2){
			$("#modificar_cliente").jqxComboBox('clearSelection');
			$("#modificar_cliente_ID").jqxComboBox('clearSelection');
			$("#modificar_direccion").val('');
			$("#modificar_telefono").val('');
			$("#modificar_cupo_cr").val('');
			$("#modificar_cupo_cr_check").jqxCheckBox('uncheck');
			$("#modificar_cupo_cr_check2").jqxCheckBox('uncheck');
			$("#modificar_contacto_p").val('');
			$("#modificar_remision").val('');
			$("#modificar_estado").val('');
			$("#modificar_gar_docs").val('');
			$("#modificar_ord_produccion").jqxComboBox('clearSelection');
			$("#modificar_factura").val('');
			$("#modificar_interno").val('');
			$("#modificar_beneficiario").jqxComboBox('clearSelection');
			$("#modificar_beneficiario_ID").jqxComboBox('clearSelection');
			$("#modificar_products_grid").jqxGrid('clear');
			$("#modificar_cobrador").jqxComboBox('clearSelection');
			$("#modificar_valor_rc").val('');
			$("#modificar_saldo").val('');
			$("#modificar_tipo_descuento").jqxComboBox('clearSelection');
			$("#modificar_tipo_descuento_precio").val("0");
		}
		else {
			$("#modificar_cliente").jqxComboBox('clearSelection');
			$("#modificar_cliente_ID").jqxComboBox('clearSelection');
			$("#modificar_direccion").val('');
			$("#modificar_telefono").val('');
			$("#modificar_cupo_cr").val('');
			$("#modificar_cupo_cr_check").jqxCheckBox('uncheck');
			$("#modificar_cupo_cr_check2").jqxCheckBox('uncheck');
			$("#modificar_contacto_p").val('');
			$("#modificar_remision").val('');
			$("#modificar_estado").val('');
			$("#modificar_gar_docs").val('');
			$("#modificar_ord_compra").jqxComboBox('clearSelection');
			$("#modificar_ord_produccion").jqxComboBox('clearSelection');
			$("#modificar_factura").val('');
			$("#modificar_interno").val('');
			$("#modificar_beneficiario").jqxComboBox('clearSelection');
			$("#beneficiarioID").jqxComboBox('clearSelection');
			$("#modificar_products_grid").jqxGrid('clear');
			$("#modificar_cobrador").jqxComboBox('clearSelection');
			$("#modificar_valor_rc").val('');
			$("#modificar_saldo").val('');
		}
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
				$("#modificar_valor_rc").val(records[0]["Saldo"]);
				//$("#modificar_saldo").val(records[0]["Saldo"]);
				Calcular(true);
			}
		});
	};
	
	function ValoresParte1 ()
	{
		var ValoresSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Direccion', type: 'string'},
				{ name: 'ContactoP', type: 'string'},
				{ name: 'Telefono', type: 'string'},
				{ name: 'Email', type: 'string'},
				{ name: 'CupoCR', type: 'string'},
				{ name: 'CupoCR_Check', type: 'bool'},
				{ name: 'CupoAD_Check', type: 'bool'},
				{ name: 'Garantia', type: 'string'},
				{ name: 'EstadoC', type: 'string'},
			],
			type: 'GET',
			data: {"Valores":$("#modificar_cliente_ID").val()},
			url: "modulos/datos.php",
			async: true
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function () {
				var records = ValoresDataAdapter.records;
				
				$("#modificar_direccion").val(records[0]["Direccion"]);
				$("#modificar_telefono").val(records[0]["Telefono"]);
				$("#modificar_cupo_cr").val(records[0]["CupoCR"]);
				$("#modificar_cupo_cr_check").val(records[0]["CupoCR_Check"]);
				$("#modificar_cupo_cr_check2").val(records[0]["CupoAD_Check"]);
				$("#modificar_contacto_p").val(records[0]["ContactoP"]);
				$("#modificar_e-mail").val(records[0]["Email"]);
				$("#modificar_estado").val(records[0]["EstadoC"]);
				
				if (records[0]["EstadoC"] == "Al Dia")
					$("#modificar_estado").addClass("GreenState");
				else if (records[0]["EstadoC"] == "Mora")
					$("#modificar_estado").addClass("RedState");
				else
					$("#modificar_estado").addClass("VioletState");
				
				$("#modificar_gar_docs").val(records[0]["Garantia"]);
				
				if (records[0]["Garantia"] == "Al Dia")
					$("#modificar_gar_docs").addClass("GreenState");
				else
					$("#modificar_gar_docs").addClass("OrangeState");
				
				//RC Combobox
				RC_Source.data = {"RecibosConSaldo": $("#modificar_cliente_ID").val()};
				RC_Adapter = new $.jqx.dataAdapter(RC_Source);
				$("#modificar_recibo").jqxComboBox({source: RC_Adapter});
				Cargar_Cartera();
				Calcular(true);
			}
		});
	};
	
	function CargarValores ()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Interno', type: 'string'},
				{ name: 'Remision', type: 'string' },
				{ name: 'Fecha', type: 'string'},
				{ name: 'Factura', type: 'string' },
				{ name: 'Ord_Produccion', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Ruta', type: 'string'},
				{ name: 'Direccion', type: 'string'},
				{ name: 'FormaP', type: 'string' },
				{ name: 'Caja_Interno', type: 'string' },
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
				{ name: 'Estado', type: 'string' },
				{ name: 'BeneficiarioID', type: 'string'},
				//--
				{ name: 'CodFab', type: 'string'},
				{ name: 'Nombre', type: 'string' },
				{ name: 'UndMed', type: 'string'},
				{ name: 'Peso', type: 'decimal' },
				{ name: 'Existencia', type: 'decimal' },
				{ name: 'Cantidad', type: 'decimal'},
				{ name: 'Dcto', type: 'decimal'},
				{ name: 'Unitario', type: 'decimal' },
				{ name: 'Produccion', type: 'bool' },
				{ name: 'FacturaExistencia', type: 'bool' },
			],
			data:{"Ventas_Modificar":$("#modificar_ord_compra").val()},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var Classes = document.getElementById("modificar_estado").className.split(/\s+/);
				for (i = 0; i < Classes.length; i ++)
				{
					switch (Classes[i])
					{
						case "GreenState":
							$("#modificar_estado").removeClass("GreenState");
						break;
						case "RedState":
							$("#modificar_estado").removeClass("RedState");
						break;
						case "VioletState":
							$("#modificar_estado").removeClass("VioletState");
						break;
					}
				}
				var Classes = document.getElementById("modificar_gar_docs").className.split(/\s+/);
				for (i = 0; i < Classes.length; i ++)
				{
					switch (Classes[i])
					{
						case "GreenState":
							$("#modificar_gar_docs").removeClass("GreenState");
						break;
						case "OrangeState":
							$("#modificar_gar_docs").removeClass("OrangeState");
						break;
					}
				}
				
				var records = GetValuesAdapter.records;
				var len = records.length;
				for (var i=0;i<len;i++) {
					var datarow = [{
						"CodFab":records[i]["CodFab"],
						"Nombre":records[i]["Nombre"],
						"UndMed":records[i]["UndMed"],
						"Peso":(parseFloat(records[i]["Peso"]) * parseFloat(records[i]["Cantidad"])).toFixed(2),
						"Peso_Unitario":records[i]["Peso"],
						"Existencia":records[i]["Existencia"],
						"Cantidad":records[i]["Cantidad"],
						"Dcto":records[i]["Dcto"],
						"Unitario":records[i]["Unitario"],
						"Precio":records[i]["Unitario"],
						"Produccion":records[i]["Produccion"],
					}];
					$("#modificar_products_grid").jqxGrid("addrow", null, datarow, "first");
				}
				$("#modificar_cliente").jqxComboBox('selectItem', records[0]["ClienteID"]);
				$("#modificar_beneficiario").jqxComboBox('selectItem', records[0]["BeneficiarioID"]);
				$("#modificar_interno").val(records[0]["Interno"]);
				$("#modificar_remision").val(records[0]["Remision"]);
				$("#modificar_fecha_rem").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#modificar_factura").val(records[0]["Factura"]);
				$("#modificar_ord_produccion").val(records[0]["Ord_Produccion"]);
				$("#modificar_sector").val(records[0]["Ruta"]);
				$("#modificar_ruta").val(records[0]["Ruta"]);
				$("#modificar_dir_entrega").val(records[0]["Direccion"]);
				$("#modificar_formaP").val(records[0]["FormaP"]);
				//$("#modificar_recibo").val(records[0]["Caja_Recibo"]);
				
				Caja_Interno = records[0]["Caja_Interno"];
				Caja_Recibo_Original = records[0]["Caja_Recibo"];
				Caja_Recibo = records[0]["Caja_Recibo"];
				if (Caja_Recibo != "")
					Cargar_Recibo = true;
				$("#modificar_vendedor").jqxComboBox('selectItem', records[0]["VendedorID"]);
				$("#modificar_cobrador").jqxComboBox('selectItem', records[0]["CobradorID"]);
				$("#modificar_conductor").jqxComboBox('selectItem', records[0]["Conductor"]);
				$("#modificar_placa").jqxComboBox('selectItem', records[0]["Placa"]);
				$("#modificar_tipo_servicio").val(records[0]["TipoServicio"]);
				$("#modificar_tipo_servicio_precio").val(records[0]["TipoServicioValor"]);
				if (records[0]["TipoDescuento"] == "")
					$("#modificar_tipo_descuento").jqxComboBox('clearSelection');
				else
					$("#modificar_tipo_descuento").jqxComboBox('selectItem', records[0]["TipoDescuento"]);
				
				$("modificar_tipo_descuento_precio").val(records[0]["TipoDescuentoValor"]);
				
				$("#modificar_observaciones").val(records[0]["Observaciones"]);
				$("#modificar_notas").val(records[0]["Notas"]);
				
				$("#modificar_estado_remision").val(records[0]["Estado"]);
				
				ValoresParte1();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
			downloadComplete: function(edata, textStatus, jqXHR) {
				//alert("Data is Loaded");
			}
		});
	};
	
	$("#Modificar_HideButton").click(function()
	{
		$("#Modificar_Content_to_Hide").toggle();
	});
	
	//------------------------------------------- KEY JUMPS
	$('#modificar_ord_compra').keyup(function(event) {
		if(event.which == 13)
		{
			$("#modificar_cliente").jqxComboBox('focus');
		}
	});
	$('#modificar_cliente').keyup(function(event) {
		if(event.which == 13)
		{
			$("#modificar_cliente_ID").jqxComboBox('focus');
		}
	});
	$('#modificar_cliente_ID').keyup(function(event) {
		if(event.which == 13)
		{
			$("#modificar_fecha_rem").jqxDateTimeInput('focus');
		}
	});
	$('#modificar_fecha_rem').keyup(function(event) {
		if(event.which == 13)
		{
			$("#modificar_remision").jqxInput('focus');
		}
	});
	$('#modificar_remision').keyup(function(event) {
		if(event.which == 13)
		{
			$("#modificar_factura").jqxInput('focus');
		}
	});
	$('#modificar_factura').keyup(function(event) {
		if(event.which == 13)
		{
			$("#modificar_codigo").jqxComboBox('focus');
		}
	});
	$('#modificar_codigo').keyup(function(event) {
		if(event.which == 13)
		{
			$("#modificar_producto").jqxComboBox('focus');
		}
	});
	$('#modificar_producto').keyup(function(event) {
		if(event.which == 13)
		{
			if (ExistenciaCargada == false) {
				return;
			}
			else {
				$("#modificar_cantidad").jqxNumberInput('focus');
				var input = $('#modificar_cantidad input')[0];
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
	$('#modificar_cantidad').keyup(function(event) {
		if(event.which == 13 && cantidad_exedida == false)
		{
			$("#modificar_listap").jqxDropDownList('focus');
		}
	});
	$('#modificar_listap').keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row();
		}
	});
	
	$("#modificar_cliente").jqxComboBox(
	{
		width: 410,
		height: 20,
		theme: mytheme,
		source: ClienteDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#modificar_cliente").bind('change', function (event)
	{
		if (event.args) {
			if ($("#modificar_cliente_ID").val() != event.args.item.value)
				$("#modificar_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#modificar_cliente_ID").jqxComboBox(	{
		theme: mytheme,
		height: 20,
		width: 200,
		theme: mytheme,
		source: ClienteDataAdapter2,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#modificar_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#modificar_cliente").val() != event.args.item.value)
					$("#modificar_cliente").jqxComboBox('selectItem', event.args.item.value);
				
				ClearFields(1);
				ValoresParte1();
			},500);
		}
	});
	
	$("#modificar_direccion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 260,
		disabled: true,
	});
	
	$("#modificar_telefono").jqxInput({//jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 112,
		//mask: '(###)###-####',
		disabled: true,
	});
	
	$("#modificar_cupo_cr").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 145,
		symbol: '$',
		digits: 14,
		max: 99999999999999,
		disabled: true,
	});
	
	$("#modificar_cupo_cr_check").jqxCheckBox({
		theme: mytheme,
		boxSize: 18,
		disabled: true
	});
	$("#modificar_cupo_cr_check").bind('change', function (event) {
		var checked = event.args.checked;
	});
	
	$("#modificar_cupo_cr_check2").jqxCheckBox({
		theme: mytheme2,
		boxSize: 18,
		disabled: true
	});
	
	$("#modificar_contacto_p").jqxInput({
		theme: mytheme,
		height: 20,
		width: 145,
		disabled: true,
	});
	
	$("#modificar_e-mail").jqxInput({
		theme: mytheme,
		height: 20,
		width: 140,
		disabled: true,
	});
	
	$("#modificar_estado").jqxInput({
		theme: mytheme,
		height: 20,
		width: 65,
		disabled: true
	});

	$("#modificar_gar_docs").jqxInput({
		theme: mytheme,
		height: 20,
		width: 65,
		disabled: true
	});
	
	$("#modificar_remision").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110
	});
	
	$("#modificar_ord_compra").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 110,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Orden',
		selectedIndex: -1,
		displayMember: 'OrdenCompra',
		valueMember: 'Interno'
	});
	$("#modificar_ord_compra").bind('change', function (event)
	{
		if (event.args)
		{
			if (ID_OrdenCompra == event.args.item.value)
				return;
			
			ID_OrdenCompra = event.args.item.value;
			ErrorSaldo = false;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (ClickOK == true)
				{
					EnableDisableAll(false);
					ClickOK = false;
				}
				ClearFields(2);
				CargarValores();
			},500);
		}
		else
		{
			var item_value = $("#modificar_ord_compra").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#modificar_ord_compra").jqxComboBox('clearSelection');
				ClearDocument();
			}
			else
			{
				var value = $("#modificar_ord_compra").val();
				var item = $("#modificar_ord_compra").jqxComboBox('getItems');
				
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#modificar_ord_compra").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#modificar_ord_compra").jqxComboBox('clearSelection');
				ClearDocument();
			}
		}
	});
	$("#modificar_ord_compra").bind('bindingComplete', function (event) {
		if (ID_OrdenCompra != "")
			$("#modificar_ord_compra").jqxComboBox('selectItem', ID_OrdenCompra);
	});
	/*$("#modificar_ord_compra").on('bindingComplete', function (event) {
		if (ID_OrdenCompra != "")
		{
			var item = $("#modificar_ord_compra").jqxComboBox("getItemByValue", ID_OrdenCompra);
			if (item != undefined)
				$("#modificar_ord_compra").val(item.value);
			else
				ClearDocument();
		}
	});*/
	// Buscar Ordenes
	OrdenSource.data = {"Ventas_Modificar":true},
	OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
	$("#modificar_ord_compra").jqxComboBox({source: OrdenAdapter});
	
	$("#modificar_ord_produccion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true,
	});
	
	$("#modificar_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110
	});

	$("#modificar_interno").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true
	});
	
	$("#modificar_beneficiario").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 220,
		source: ClienteDataAdapter3,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Beneficiario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#modificar_beneficiario").bind('change', function (event) 
	{
		if (event.args)
		{
			if ($("#modificar_beneficiario_ID").val() != event.args.item.value)
				$("#modificar_beneficiario_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			$("#modificar_beneficiario").jqxComboBox('clearSelection');
			$("#modificar_beneficiario_ID").jqxComboBox('clearSelection');
		}
	});
	
	$("#modificar_beneficiario_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: ClienteDataAdapter4,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#modificar_beneficiario_ID").bind('change', function (event) 
	{
		if (event.args)
		{
			if ($("#modificar_beneficiario").val() != event.args.item.value)
				$("#modificar_beneficiario").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			$("#modificar_beneficiario").jqxComboBox('clearSelection');
			$("#modificar_beneficiario_ID").jqxComboBox('clearSelection');
		}
	});
	
	//---------------------------------------------------------------- PARTE 2
	//-- GLOBAL
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
	
	function GetPriceList ()
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
			type: 'GET',
			data:{"Precios":$("#modificar_codigo").val()},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				$("#modificar_listap").jqxDropDownList('clear');
				$("#modificar_existencia").val('');
				var records = GetValuesAdapter.records;
				$("#modificar_listap").jqxDropDownList('addItem', {label: "P1 $"+ records[0]["Lista1"], value: records[0]["Lista1"]});
				$("#modificar_listap").jqxDropDownList('addItem', {label: "P2 $"+ records[0]["Lista2"], value: records[0]["Lista2"]});
				$("#modificar_listap").jqxDropDownList('addItem', {label: "P3 $"+ records[0]["Lista3"], value: records[0]["Lista3"]});
				$("#modificar_listap").jqxDropDownList('addItem', {label: "P4 $"+ records[0]["Lista4"], value: records[0]["Lista4"]});
				$("#modificar_listap").jqxDropDownList('selectIndex', 0);
				
				var datinfo = $("#modificar_products_grid").jqxGrid('getdatainformation');
				var count = datinfo.rowscount;
				var totalc = 0;
				for (i=0;i<count;i++) {
					var currentRow = $('#modificar_products_grid').jqxGrid('getrowdata', i);
					if (currentRow.CodFab == $("#modificar_codigo").val())
					{
						totalc = totalc + currentRow.Cantidad;
					}
				}
				var totala = records[0]["Existencia"] - totalc;
				if (totala < 0)
					totala = 0;
				$("#modificar_existencia").val(totala);
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
	
	$("#modificar_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: CB_ProductoDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#modificar_codigo").bind('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#modificar_producto").val() != event.args.item.value)
					$("#modificar_producto").jqxComboBox('selectItem', event.args.item.value);
				GetPriceList();
			},300);
		}
	});
	
	$("#modificar_producto").jqxComboBox(
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
	$("#modificar_producto").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#modificar_codigo").val() != event.args.item.value)
				$("#modificar_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#modificar_existencia").jqxInput({
		theme: mytheme,
		height: 20,
		width: 140,
		disabled: true,
		rtl: true
	});
	
	$("#modificar_cantidad").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 170,
		inputMode: 'simple',
		spinButtons: false
	});
	
	var Data =
	{};
	
	$("#modificar_listap").jqxDropDownList({
		theme: mytheme,
		height: 20,
		width: 186,
		source: Data,
		placeHolder: 'Lista de Precios',
		selectedIndex: -1
	});
	
	$('#modificar_form_validation').jqxValidator({
		rules:
		[
			{
				input: '#modificar_cantidad', message: 'Debe ser un valor mayor a 0!', action: 'keyup, blur', rule: function (input, commit) {
					if (input.val() < 0) {
						return false;
					}
					return true;
				}
			},
			{
				input: '#modificar_cantidad', message: 'Cantidad mayor a Existencia!', action: 'keyup, blur', rule: function (input, commit) {
					if (input.val() > $("#modificar_existencia").val() && FacturaExistencia == false) {
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
		
		var Product = $("#modificar_producto").jqxComboBox('getSelectedItem');
		var ProductPrice = $("#modificar_listap").jqxDropDownList('getSelectedItem');
		var ExistenciaNum = $("#modificar_existencia").val();
		var CantidadNum = $("#modificar_cantidad").val();
		
		if (!Product) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("modificar_producto");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum > ExistenciaNum && FacturaExistencia == false) {
			Alerts_Box("Cantidad mayor a la Existencia!", 3);
			WaitClick_NumberInput("modificar_cantidad");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("modificar_cantidad");
			RowAdded = true;
			return;
		}
		
		if (ExistenciaNum <= 0 && FacturaExistencia == false) {
			Alerts_Box("\"AGOTADO\" No hay modificar_existencia!</br>Favor Seleccionar otro Producto", 4);
			WaitClick_Combobox("modificar_producto");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#modificar_products_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $('#modificar_products_grid').jqxGrid('getrowdata', i);
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
				var id = $("#modificar_products_grid").jqxGrid('getrowid', i);
				$("#modificar_products_grid").jqxGrid('deleterow', id);
				$("#modificar_products_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#modificar_codigo").jqxComboBox('clearSelection');
				$("#modificar_producto").jqxComboBox('clearSelection');
				$("#modificar_existencia").val('');
				$("#modificar_cantidad").val('');
				$("#modificar_listap").jqxDropDownList('clear');
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
				$("#modificar_products_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#modificar_codigo").jqxComboBox('clearSelection');
				$("#modificar_producto").jqxComboBox('clearSelection');
				$("#modificar_existencia").val('');
				$("#modificar_cantidad").val('');
				$("#modificar_listap").jqxDropDownList('clear');
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
	
	$("#modificar_products_grid").jqxGrid(
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
				'<input type="button" id="ventas_modificar_addrowbutton" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="ventas_modificar_deleterowbutton" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#ventas_modificar_addrowbutton").jqxButton({theme: mytheme, template: "success"});
			$("#ventas_modificar_deleterowbutton").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#ventas_modificar_addrowbutton").on('click', function () {
				Add_Row();
			});
			// delete row.
			$("#ventas_modificar_deleterowbutton").on('click', function () {
				var selectedrowindex = $("#modificar_products_grid").jqxGrid('getselectedrowindex');
				var rowscount = $("#modificar_products_grid").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#modificar_products_grid").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#modificar_products_grid").jqxGrid('deleterow', id);
					Calcular();
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: 90, height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: 240, height: 20 },
			{ text: 'Und', datafield: 'UndMed', editable: false, width: 50, height: 20 },
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
					if (rowdata.Cantidad > rowdata.Existencia && rowdata.FacturaExistencia == false) {
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
			{ text: 'P', datafield: 'Produccion', columntype: 'checkbox', width: 15 },
			{ text: 'F', datafield: 'FacturaExistencia', columntype: 'checkbox', width: 15 },
		]
	});

	$('#modificar_products_grid').jqxGrid('hidecolumn', 'Peso_Unitario');
	$('#modificar_products_grid').jqxGrid('hidecolumn', 'Precio');
	$('#modificar_products_grid').jqxGrid('hidecolumn', 'Produccion');
	$('#modificar_products_grid').jqxGrid('hidecolumn', 'FacturaExistencia');
	
	$("#modificar_products_grid").on('cellvaluechanged', function (event) 
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
					var Peso = parseFloat($("#modificar_products_grid").jqxGrid('getcellvalue', rowBoundIndex, "Peso_Unitario"));
					var Total = (Peso * value);

					if (Total < 0)
						Total = 0;
					
					$("#modificar_products_grid").jqxGrid('setcellvalue', rowBoundIndex, "Peso", Total);	
				}
				else if (datafield == "Dcto")
				{
					var Precio = parseFloat($("#modificar_products_grid").jqxGrid('getcellvalue', rowBoundIndex, "Precio"));
					var Total = Precio - ((Precio / 100) * value);
					$("#modificar_products_grid").jqxGrid('setcellvalue', rowBoundIndex, "Unitario", Total);
				}
				else if (datafield == "Unitario")
				{
					var Precio = parseFloat($("#modificar_products_grid").jqxGrid('getcellvalue', rowBoundIndex, "Precio"));
					var Total = (((Precio - value) / Precio) * 100).toFixed(2);

					if (Total < 0)
						Total = 0;
					
					$("#modificar_products_grid").jqxGrid('setcellvalue', rowBoundIndex, "Dcto", Total);	
				}
				Calcular();
			}
		}
	});
	
	// ------------------------------------------ PARTE 3
	// prepare data
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
	
	$("#modificar_conductor").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 160,
		source: ConductorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Conductor',
		selectedIndex: -1,
		displayMember: 'Chofer',
		valueMember: 'ClienteID',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#modificar_conductor").bind('change', function (event) {
		if (!event.args)
			$("#modificar_conductor").jqxComboBox('clearSelection');
	});
	
	$("#modificar_formaP").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 148,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		dropDownHeight: 50
	});
	$("#modificar_formaP").jqxComboBox('addItem', {label: "Efectivo"});
	$("#modificar_formaP").jqxComboBox('addItem', {label: "Credito"});
	
	$("#modificar_formaP").bind('change', function (event) {
		if (!event.args) {
			$("#modificar_formaP").jqxComboBox('clearSelection');
			$("#modificar_recibo").jqxComboBox('clearSelection');
			$("#modificar_recibo").jqxComboBox({ disabled: true });
		}
	});
	$("#modificar_formaP").bind('select', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				var Cliente = $("#modificar_cliente_ID").jqxComboBox('getSelectedItem');
				
				if (!Cliente)
				{
					Alerts_Box("Debe Seleccionar un Cliente!", 4);
					$("#modificar_formaP").jqxComboBox('clearSelection');
				}
				
				if (event.args.item.value == "Credito" && Cliente.value != "")
				{
					if ($("#modificar_cupo_cr_check").val() == true) {
						$("#modificar_recibo").jqxComboBox('clearSelection');
						$("#modificar_recibo").jqxComboBox({ disabled: true });
						$("#modificar_valor_rc").val('');
						$("#modificar_saldo").val('');
					}
					/*else {
						Alerts_Box("El Cliente Seleccionado no posee credito Activo!", 3);
						$("#modificar_formaP").jqxComboBox('clearSelection');
					}*/
					Calcular(true);
				}
				else if (event.args.item.value == "Efectivo" && Cliente.value != "") {
					$("#modificar_recibo").jqxComboBox({ disabled: false });
				}
				clearTimeout(Timer1);
			},500);
		}
	});
	
	$("#modificar_placa").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 95,
		source: VehiculoDataAdapter,
		dropDownHeight: 100,
		promptText: 'Sel. Placa',
		selectedIndex: -1,
		displayMember: 'Placa',
		valueMember: 'Placa',
		searchMode: 'startswithignorecase',
		autoComplete: true,
	});
	$("#modificar_placa").bind('change', function (event) {
		if (!event.args)
			$("#modificar_placa").jqxComboBox('clearSelection');
	});
	
	$("#modificar_ruta").jqxInput({
		theme: mytheme,
		height: 20,
		width: 95,
		disabled: true
	});
	
	$("#modificar_pesokg").jqxNumberInput({
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
	
	$("#modificar_recibo").jqxComboBox({
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
	$("#modificar_recibo").on('bindingComplete', function (event) {
		if (Cargar_Recibo)
		{
			var items = $("#modificar_recibo").jqxComboBox('getVisibleItems');
			var len = items.length;
			for (var i = 0; i < len; i++)
			{
				var tmp = items[i];
				if (tmp.label == Caja_Recibo) {
					$("#modificar_recibo").jqxComboBox("selectIndex", tmp.index);
					Cargar_RC();
					return;
				}
			}
			$("#modificar_recibo").jqxComboBox("addItem", { label: Caja_Recibo, value: Caja_Interno});
			$("#modificar_recibo").jqxComboBox("selectItem", Caja_Recibo);
		}
	});
	$("#modificar_recibo").on('change', function (event) {
		if (!event.args)
		{
			$("#modificar_recibo").jqxComboBox('clearSelection');
			$("#modificar_valor_rc").val('');
			$("#modificar_saldo").val('');
			Caja_Interno = "";
			Caja_Recibo = "";
		}
		else
		{
			Caja_Interno = event.args.item.value;
			Caja_Recibo = event.args.item.label;
		}
	});
	$("#modificar_recibo").on('select', function (event) {
		if (event.args)
		{
			Caja_Interno = event.args.item.value;
			Caja_Recibo = event.args.item.label;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				Cargar_RC();
			},500);
		}
	});
	
	$("#modificar_dir_entrega").jqxInput({
		theme: mytheme,
		height: 20,
		width: 430
	});
	
	$("#modificar_valor_rc").jqxNumberInput({
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
	
	$("#modificar_sector").jqxComboBox({
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
	$("#modificar_sector").bind('change', function (event) {
		if (!event.args) {
			$("#modificar_sector").jqxComboBox('clearSelection');
			$("#modificar_ruta").val('');
		}
	});
	$("#modificar_sector").bind('select', function (event) {
		if (event.args) {
			var ruta_val = event.args.item.value;
			$("#modificar_ruta").val(ruta_val);
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
	
	$("#modificar_vendedor").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 183,
		source: VendedorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Vendedor',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#modificar_vendedor").bind('change', function (event) {
		if (!event.args) {
			$("#modificar_vendedor").jqxComboBox('clearSelection');
		}
	});
	
	$("#modificar_cobrador").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 183,
		source: VendedorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Cobrador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#modificar_cobrador").bind('change', function (event) {
		if (!event.args) {
			$("#modificar_cobrador").jqxComboBox('clearSelection');
		}
	});
	
	$("#modificar_saldo").jqxNumberInput({
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
	
	$("#subtotal_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		//textAlign: 'center',
		disabled: true,
		placeHolder: "               SUBTOTAL",
	});
	
	$("#modificar_subtotal_total").jqxNumberInput({
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
			var information = $('#modificar_products_grid').jqxGrid('getdatainformation');
			var rowscounts = information.rowscount;
			var old_total = 0;
			var total_peso = 0;
			for (i = 0; i < rowscounts; i++)
			{
				var currentRow = $('#modificar_products_grid').jqxGrid('getrowdata', i);
				var total1 = parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad);
				//var total2 = Math.round( total1 - (parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad)) / 100 * parseFloat(currentRow.Dcto) );
				old_total = old_total + total1;
				total_peso = total_peso + parseFloat(currentRow.Peso_Unitario) * parseFloat(currentRow.Cantidad);
			};
			
			var valor_tipo = $('#modificar_tipo_servicio_precio').val();
			old_total = old_total + valor_tipo;
			
			if (total == old_total && Ignore == false)
			{
				//alert(total +"  -  "+ old_total)
				return;
			}
			else
				total = old_total;
			
			iva = Math.round(total - (total / 1.19));
			
			// if ($('#modificar_fecha_rem').val() >= '11-ene-2017')
			// 	iva = Math.round(total - (total / 1.19));
			// else
			// 	iva = Math.round(total - (total / 1.16));
			/*var tipo_dcto = $("#modificar_tipo_descuento_precio").val();
			if ($("#modificar_tipo_descuento").val() != "" ) {
				tipo_dcto = iva;
				$("#modificar_tipo_descuento_precio").val(tipo_dcto);
			}
			else
			{
				$("#modificar_tipo_descuento_precio").val("0");
			}*/
			var tipo_dcto = $("#modificar_tipo_descuento_precio").val();
			var dcto = $("#modificar_tipo_descuento").val();
			if (tipo_dcto <= 0 && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#modificar_tipo_descuento_precio").val(tipo_dcto);
			}
			else if (tipo_dcto != iva && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#modificar_tipo_descuento_precio").val(tipo_dcto);
			}
			else if (tipo_dcto <= 0 && dcto.indexOf("IVA") < 1)
			{
				$("#modificar_tipo_descuento_precio").val("0");
			}
			
			var subtotal = Math.round(total);
			total = Math.round(total - tipo_dcto);
			if (total < 0)
				total = 0;
			
			var item = $("#modificar_formaP").jqxComboBox('getSelectedItem');
			if (item)
			{
				if (item.label == "Efectivo")
				{
					var ValorRC = $("#modificar_valor_rc").val();
					
					if (Caja_Recibo_Original != Caja_Recibo)
					{
						if (total > ValorRC && $("#modificar_recibo").val() != "")
						{
							Alerts_Box("El Valor de la Factura es Mayor a el Saldo Disponible", 3);
							$("#modificar_valor_rc").addClass("jqx-validator-error-element");
							$("#modificar_saldo").val("0");
							ErrorSaldo = true;
						}
						else if (total < ValorRC && $("#modificar_recibo").val() != "")
						{
							$("#modificar_valor_rc").removeClass("jqx-validator-error-element");
							$("#modificar_saldo").val(ValorRC - total);
							ErrorSaldo = false;
						}
					}
				}
				else if (item.label == "Credito")
				{
					var ValorCupo = $("#modificar_cupo_cr").val();
					var Deuda = $("#ventas_modificar_deuda_total").val();
					var Exceso = $("#ventas_modificar_mas_de_90").val();
					
					if (Exceso > 0 && $("#modificar_cupo_cr_check").val() == true)
					{
						Alerts_Box("El Cliente Posee Deudas Mayores a 90 Dias.", 3);
						$("#modificar_cupo_cr").addClass("jqx-validator-error-element");
						ErrorDeuda = true;
					}
					else
					{
						ErrorDeuda = false;
						
						if (Deuda > ValorCupo && $("#modificar_cupo_cr_check").val() == true)
						{
							Alerts_Box("La Deuda del Cliente Supera al Cupo Credito Disponible.", 3);
							$("#modificar_cupo_cr").addClass("jqx-validator-error-element");
							ErrorSaldo = true;
						}
						else if (Deuda <= ValorCupo && $("#modificar_cupo_cr_check").val() == true)
						{
							if (total > (ValorCupo - Deuda))
							{
								Alerts_Box("El Valor de la Factura es Mayor que el Credito Disponible", 3);
								$("#modificar_cupo_cr").addClass("jqx-validator-error-element");
								ErrorSaldo = true;
							}
							else
							{
								$("#modificar_cupo_cr").removeClass("jqx-validator-error-element");
								ErrorSaldo = false;
							}
						}
					}
				}
			}
			
			$("#modificar_subtotal_total").val(subtotal);
			$("#modificar_iva_precio").val(iva);
			$("#modificar_total_total").val(total);
			$("#modificar_pesokg").val(total_peso);
			$("#modificar_codigo").jqxComboBox('focus');
			clearTimeout(Calc_Timer);
		},100);
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
	
	$("#modificar_tipo_servicio").jqxComboBox({
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
	
	$("#modificar_tipo_servicio_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$('#modificar_tipo_servicio_precio').on('change', function (event) 
	{
		Calcular();
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("modificar_tipo_servicio_precio");
		}
	});

	$("#modificar_iva_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                     IVA",
	});
	
	$("#modificar_iva_precio").jqxNumberInput({
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
	
	$("#modificar_tipo_descuento").jqxComboBox({
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
	$("#modificar_tipo_descuento").bind('change', function (event) {
		if (!event.args) {
			$("#modificar_tipo_descuento").jqxComboBox('clearSelection');
		}
		else
			Calcular(true);
	});
	
	$("#modificar_tipo_descuento_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$('#modificar_tipo_descuento_precio').on('change', function (event) 
	{
		Calcular();
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("modificar_tipo_descuento_precio");
		}
	});
	
	$("#modificar_total_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                  TOTAL",
	});
	
	$("#modificar_total_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#modificar_estado_remision").jqxInput({
		theme: mytheme,
		height: 25,
		width: 150,
		disabled: true,
	});
	
	function ModificarPedido ()
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
		if ($("#modificar_formaP").val() == "Efectivo" && $("#modificar_valor_rc").val() == 0)
		{
			Alerts_Box("Debe ingresar un Recibo de Caja.", 3);
			Locked = false;
			return;
		}
		*/
		
		var datinfo = $("#modificar_products_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		var Cliente = $("#modificar_cliente_ID").jqxComboBox('getSelectedItem');
		var OrdenCompra = $("#modificar_ord_compra").jqxComboBox('getSelectedItem');
		
		if (!OrdenCompra) {
			Alerts_Box("Debe Seleccionar una Orden de Compra!", 3);
			WaitClick_Combobox("modificar_ord_compra");
			Locked = false;
			return;
		}
		
		if (!Cliente) {
			Alerts_Box("Debe Ingresar un Cliente!", 3);
			WaitClick_Combobox("modificar_cliente");
			Locked = false;
			return;
		}

		if (count <= 0) {
			Alerts_Box("Debe Ingresar un Producto!", 3);
			WaitClick_Combobox("modificar_producto");
			Locked = false;
			return;
		}
		
		if ($("#modificar_tipo_servicio_precio").val() > 0) {
			if ($("#modificar_tipo_servicio").val() <= 0 | $("#modificar_tipo_servicio").val() == "") {
				Alerts_Box("Debe Ingresar Un Tipo de Servicio", 3);
				WaitClick_Combobox("modificar_tipo_servicio");
				Locked = false;
				return;
			}
		}
		
		if ($("#modificar_tipo_descuento_precio").val() > 0) {
			if ($("#modificar_tipo_descuento").val() <= 0 | $("#modificar_tipo_descuento").val() == "") {
				Alerts_Box("Debe Ingresar Un Tipo de Descuento", 3);
				WaitClick_Combobox("modificar_tipo_descuento");
				Locked = false;
				return;
			}
		}
		
		if ($("#modificar_formaP").val() <= 0 | $("#modificar_formaP").val() == "") {
			Alerts_Box("Debe Ingresar Una Forma de Pago!", 3);
			WaitClick_Combobox("modificar_formaP");
			Locked = false;
			return;
		}
		
		if ($("#modificar_vendedor").val() <= 0 | $("#modificar_vendedor").val() == "") {
			Alerts_Box("Debe Ingresar Un Vendedor!", 3);
			WaitClick_Combobox("modificar_vendedor");
			Locked = false;
			return;
		}
		
		if ($("#modificar_cobrador").val() <= 0 | $("#modificar_cobrador").val() == "") {
			Alerts_Box("Debe Ingresar Un Cobrador!", 3);
			WaitClick_Combobox("modificar_cobrador");
			Locked = false;
			return;
		}
		
		if ($("#modificar_sector").val() <= 0 | $("#modificar_sector").val() == "") {
			Alerts_Box("Debe Ingresar Un Sector!", 3);
			WaitClick_Combobox("modificar_sector");
			Locked = false;
			return;
		}
		
		for (i = 0; i < count; i++)
		{
			var array = {};
			var currentRow = $('#modificar_products_grid').jqxGrid('getrowdata', i);
			
			if (currentRow.Cantidad > currentRow.Existencia && currentRow.FacturaExistencia == false /*&& cotizar*/) {
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
				
				array["ClienteID"] = Cliente.value;
				array["Fecha"] = GetFormattedDate($('#modificar_fecha_rem').jqxDateTimeInput('getDate'));
				array["Remision"] = $("#modificar_remision").val();
				array["Ord_Compra"] = OrdenCompra.label;
				array["Ord_Produccion"] = $("#modificar_ord_produccion").val();
				array["Factura"] = $("#modificar_factura").val();
				array["Interno"] = OrdenCompra.value;
				array["BeneficiarioID"] = $("#modificar_beneficiario_ID").val();
				//array["Estado"] = $("#modificar_estado_remision").val();
				//---
				array["Observaciones"] = $('#modificar_observaciones').val();
				array["Subtotal2"] = $("#modificar_subtotal_total").val();
				array["TipoServicio"] = $("#modificar_tipo_servicio").val();
				array["TipoServicioValor"] = $("#modificar_tipo_servicio_precio").val();
				array["Iva"] = $("#modificar_iva_precio").val();
				array["TipoDcto"] = $("#modificar_tipo_descuento").val();
				array["TipoDctoValor"] = $("#modificar_tipo_descuento_precio").val();
				array["Total"] = $("#modificar_total_total").val();
				array["Chofer"] = $("#modificar_conductor").val();
				array["Placa"] = $("#modificar_placa").val();
				array["Peso"] = $("#modificar_pesokg").val();
				array["FormaPago"] = $("#modificar_formaP").val();
				array["Ruta"] = $("#modificar_ruta").val();
				if ($("#modificar_formaP").val() == "Efectivo")
				{
					array["Caja_Interno"] = Caja_Interno;
					array["Caja_Recibo"] = Caja_Recibo;
				}
				else
				{
					array["Caja_Interno"] = "";
					array["Caja_Recibo"] = "";
				}
				array["Direccion"] = $("#modificar_dir_entrega").val();
				array["ValorRC"] = $("#modificar_valor_rc").val();
				var Vendedor = $("#modificar_vendedor").jqxComboBox('getSelectedItem');
				var Cobrador = $("#modificar_cobrador").jqxComboBox('getSelectedItem');
				array["VendedorID"] = Vendedor.value;
				array["CobradorID"] = Cobrador.value;
				array["Saldo"] = $("#modificar_saldo").val();
			}
			myarray[i] = array;
		};
		
		/*alert(JSON.stringify(myarray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "modulos/guardar.php",
			data: {"Ventas_Modificar":myarray},
			async: true,
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						EnableDisableAll(true);
						Alerts_Box("Datos Guardados con Exito!", 2);
						$("#modificar_ord_produccion").val(data[0]["Ord_Produccion"]);
						// Buscar Ordenes
						OrdenSource.data = {"Ventas_Modificar":true},
						OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
						$("#modificar_ord_compra").jqxComboBox({source: OrdenAdapter});
					break;
					
					case "PRODUCCION":
						Alerts_Box("No es posible guardar cambios debido a que posee una Orden de Producción \"Aprobada\"", 4);
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
					break;
				}	
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	};
	
	function AnularPedido ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if ($("#modificar_ord_compra").val() < 1 | $("#modificar_ord_compra").val() == "") {
			Alerts_Box("Debe Ingresar Una Orden de Compra!", 3);
			WaitClick_Combobox("modificar_ord_compra");
			Locked = false;
			return;
		}
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: {
				"Ventas_Modificar_Anular":true,
				"Interno":$("#modificar_ord_compra").val(),
				"Motivo":$("#modificar_motivo_anular").val(),
			},
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						EnableDisableAll(true);
						Alerts_Box("Datos Anulados con Exito!", 2);
						// Buscar Ordenes
						OrdenSource.data = {"Ventas_Modificar":true},
						OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
						$("#modificar_ord_compra").jqxComboBox({source: OrdenAdapter});
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible Anular este movimiento debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("No es posible Anular este movimiento debido a que posee una Orden de Produccion \"Aprobada\"", 3);
					break;
				}	
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
		//
	};
	
	function CrearPedido ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if ($("#modificar_ord_compra").val() < 1 | $("#modificar_ord_compra").val() == "") {
			Alerts_Box("Debe Ingresar Una Orden de Compra!", 3);
			WaitClick_Combobox("modificar_ord_compra");
			Locked = false;
			return;
		}
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: {
				"Ventas_Modificar_Crear":true,
				"Interno":$("#modificar_ord_compra").val(),
			},
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						EnableDisableAll(true);
						Alerts_Box("Datos Guardados con Exito!", 2);
						// Buscar Ordenes
						OrdenSource.data = {"Ventas_Modificar":true},
						OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
						$("#modificar_ord_compra").jqxComboBox({source: OrdenAdapter});
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
					break;
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	};
	
	$('#modificar_guardar').jqxButton({ width: 140, template: "info" });
	$("#modificar_guardar").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		ModificarPedido();
	});
	
	$("#modificar_imprimir").jqxButton({width: 140, template: "warning"});
	$("#modificar_imprimir").bind('click', function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		window.open("imprimir/factura.php?Interno="+$("#modificar_interno").val()+"", "", "width=700, height=600, menubar=no, titlebar=no");
	});
	
	$("#modificar_ver_historicos").jqxButton({width: 140, template: "danger"});
	$("#modificar_ver_historicos").bind('click', function ()
	{
		$("#Ventas_Modificar_Cliente_Historicos").jqxWindow('open');
	});
	
	$("#modificar_anular").jqxButton({width: 143, template: "inverse"});
	$("#modificar_anular").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		AnularPedido();
	});
	
	$("#modificar_cotizar").jqxButton({width: 140, template: "default"});
	$("#modificar_cotizar").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		CrearPedido();
	});
	
	// ------------------------------------------ WINDOWS
	
	//--- Cliente Historicos
	$("#Ventas_Modificar_Cliente_Historicos").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: "100%",
		width: "100%",
		maxWidth: 513,
		maxHeight: 370,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Ventas_Modificar_Cliente_Historicos").on('open', function (event)
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
			data: {"Ventas_Cliente_Historicos":$("#modificar_cliente").val()},
			url: "modulos/datos.php",
			async: true
		};
		var HistoricosDataAdapter = new $.jqx.dataAdapter(HistoricosSource);
		
		$("#modificar_cliente_historicos_grid").jqxGrid({
			theme: mytheme,
			width: 500,
			source: HistoricosDataAdapter,
			sortable: true,
			pageable: true,
			autoheight: true,
			editable: false,
			editmode: 'click',
			columns:
			[
				{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '20%', height: 20 },
				{ text: 'Nombre', datafield: 'Nombre', editable: false, width: '60%', height: 20 },
				{ text: 'Precio', datafield: 'Precio', editable: false, width: '20%', height: 20, cellsalign: 'right' },
			]
		});
	});
	
	// ------------------------------------------ PARTE 4
	// prepare data
	
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
			data: {"Cargar_Cartera":$("#modificar_cliente_ID").val()},
			url: "modulos/datos.php",
			async: true,
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function () {
				var records = ValoresDataAdapter.records;
				$("#ventas_modificar_deuda_total").val(records[0]["DeudaTotal"]);
				$("#ventas_modificar_corriente").val(records[0]["Corriente"]);
				$("#ventas_modificar_de_30_a_45").val(records[0]["De30a45"]);
				$("#ventas_modificar_de_45_a_60").val(records[0]["De45a60"]);
				$("#ventas_modificar_de_60_a_90").val(records[0]["De60a90"]);
				$("#ventas_modificar_mas_de_90").val(records[0]["Mas90"]);
				//
				//$("#modificar_fecha_rem").jqxDateTimeInput('focus');
				// Clear Values Variables and Classes
				ErrorSaldo = false;
				// Clean Classes
				$("#modificar_cupo_cr").removeClass("jqx-validator-error-element");
				$("#modificar_valor_rc").removeClass("jqx-validator-error-element");
				Calcular(true);
			}
		});
	};
	
	$("#ventas_modificar_deuda_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});

	$("#ventas_modificar_corriente").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});

	$("#ventas_modificar_de_30_a_45").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});

	$("#ventas_modificar_de_45_a_60").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});

	$("#ventas_modificar_de_60_a_90").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});

	$("#ventas_modificar_mas_de_90").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});
	
	if (!Admin && !Guardar)
	{
		$("#modificar_guardar").jqxButton({ disabled: true });
		$("#modificar_cotizar").jqxButton({ disabled: true });
		$("#modificar_anular").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Imprimir)
	{
		$("#modificar_imprimir").jqxButton({ disabled: true });
	}
	
	CheckRefresh();
});
</script>
<div id="Ventas_Modificar_Cliente_Historicos">
	<div id="Ventas_Modificar_cliente_Historicos_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 160px;">Historicos de Cliente</div>
	</div>
	<div id="Ventas_Modificar_Cliente_Historicos_Content" class="WindowContainer">
		<div id="modificar_cliente_historicos_grid"></div>
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<div id="Modificar_HideButton">&nbsp;&raquo;&nbsp;</div>
	<div id="Modificar_Content_to_Hide">
		<table cellpadding="1" cellspacing="1">
			<tr>
				<td>
					Ord. Compra
				</td>
				<td>
					<div id="modificar_ord_compra"></div>
				</td>
				<td>
					Cliente
				</td>
				<td colspan="3">
					<div id="modificar_cliente"></div>
				</td>
				<td>
					ID Cliente
				</td>
				<td colspan="3">
					<div id="modificar_cliente_ID"></div>
				</td>
			</tr>
			<tr>
				<td>
					F&eacute;cha Rem.
				</td>
				<td>
					<div id="modificar_fecha_rem"></div>
				</td>
				<td>
					Direcci&oacute;n
				</td>
				<td colspan="2">
					<input type="text" id="modificar_direccion"/>
				</td>
				<td>
					<li class="parte1_li_txt">
						Telf.&nbsp;
					</li>
					<li>
						<input type="text" id="modificar_telefono"/>
					</li>
				</td>
				<td>
					<li class="parte1_li_txt">
						C&uacute;po Cr.&nbsp;
					</li>
					<li>
						<div id="modificar_cupo_cr_check"></div>
					</li>
				</td>
				<td colspan="3">
					<li>
						<div id="modificar_cupo_cr"></div>
					</li>
					<li style="padding:4px 0px 0px 2px;">
						Adic.
					</li>
					<li>
						<div id="modificar_cupo_cr_check2"></div>
					</li>
				</td>
			</tr>
			<tr>
				<td>
					Remisi&oacute;n
				</td>
				<td>
					<input type="text" id="modificar_remision"/>
				</td>
				<td>
					Interno
				</td>
				<td>
					<input type="text" id="modificar_interno"/>
				</td>
				<!--<td>
					E-Mail
				</td>-->
				<td>
					<input type="text" id="modificar_e-mail"/>
				</td>
				<!--<td>
					Contacto P.
				</td>-->
				<td>
					<input type="text" id="modificar_contacto_p"/>
				</td>
				<td>
					Estado
				</td>
				<td>
					<input type="text" id="modificar_estado"/>
				</td>
				<td>
					Gar./Docs.
				</td>
				<td>
					<input type="text" id="modificar_gar_docs"/>
				</td>
			</tr>
			<tr>
				<td>
					Factura
				</td>
				<td>
					<input type="text" id="modificar_factura"/>
				</td>
				<td>
					Ord. Prod.
				</td>
				<td>
					<input type="text" id="modificar_ord_produccion"/>
				</td>
				<td colspan="2">
					<li class="parte1_li_txt">
						Beneficiario&nbsp;
					</li>
					<li>
						<div id="modificar_beneficiario"></div>
					</li>
				</td>
				<td>
					ID Beneficiario
				</td>
				<td colspan="3">
					<div id="modificar_beneficiario_ID"></div>
				</td>
			</tr>
		</table>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<form id="modificar_form_validation" action="./">
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
			</tr>
			<tr>
				<td>
					<div id="modificar_codigo" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="modificar_producto" style="margin-left:7px;"></div>
				</td>
				<td>
					<input type="text" id="modificar_existencia" style="margin-left:7px;"/>
				</td>
				<td>
					<div id="modificar_cantidad" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="modificar_listap" style="margin:0px 7px;"></div>
				</td>
			</tr>
		</table>
	</form>
	<div id="modificar_products_grid" style="margin:0px 0px 10px 0px;"></div>
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
				<input type="text" id="subtotal_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="modificar_subtotal_total">
				</div>
			</td>
		</tr>
		<tr>
			<td rowspan="4">
				<textarea rows="7" cols="35" id="modificar_observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td rowspan="3">
				<textarea rows="5" cols="25" id="modificar_notas" readonly="true" style="resize:none; margin-left:15px;"></textarea>
			</td>
			<td>
				<div id="modificar_tipo_servicio" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="modificar_tipo_servicio_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="modificar_iva_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="modificar_iva_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="modificar_tipo_descuento" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="modificar_tipo_descuento_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<li style="margin-left:15px;" class="parte1_li_txt">
					Estado&nbsp;
				</li>
				<li>
					<input type="text" id="modificar_estado_remision"/>
				</li>
			</td>
			<td>
				<input type="text" id="modificar_total_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="modificar_total_total">
				</div>
			</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="2" style="margin:5px 0px 10px 0px;">
		<tr>
			<td colspan="9">
				&nbsp;
			</td>
			<td style="text-align:center;">
				Motivo de Anulaci&oacute;n
			</td>
		</tr>
		<tr>
			<td>
				Conductor
			</td>
			<td>
				<div id="modificar_conductor"></div>
			</td>
			<td>
				Placa:
			</td>
			<td>
				<div id="modificar_placa"></div>
			</td>
			<td>
				Peso
			</td>
			<td>
				<div id="modificar_pesokg"></div>
			</td>
			<td>
				F. Pago
			</td>
			<td>
				<div id="modificar_formaP"></div>
			</td>
			<td>
				<input type="button" id="modificar_guardar" value="Guardar"/>
			</td>
			<td rowspan="2">
				<textarea rows="3" cols="17" id="modificar_motivo_anular" maxlength="100" style="resize:none;"></textarea>
			</td>
		</tr>
		<tr>
			<td>
				Vendedor
			</td>
			<td colspan="5">
				<li>
					<div id="modificar_vendedor"></div>
				</li>
				<li class="parte1_li_txt">
					Cobrador
				</li>
				<li>
					<div id="modificar_cobrador"></div>
				</li>
			</td>
			<td>
				R. Caja
			</td>
			<td>
				<div id="modificar_recibo">
				</div>
			</td>
			<td>
				<input type="button" id="modificar_imprimir" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<td>
				Dir. Entrega
			</td>
			<td colspan="5">
				<input type="text" id="modificar_dir_entrega"/>
			</td>
			<td style="margin-left:15px;">
				Valor RC
			</td>
			<td>
				<div id="modificar_valor_rc"></div>
			</td>
			<td>
				<input type="button" id="modificar_ver_historicos" value="Ver Historicos"/>
			</td>
			<td>
				<input type="button" id="modificar_anular" value="Anular"/>
			</td>
		</tr>
		<tr>
			<td>
				Sector
			</td>
			<td colspan="3">
				<div id="modificar_sector">
				</div>
			</td>
			<td>
				Ruta:
			</td>
			<td>
				<input type="text" id="modificar_ruta"/>
			</td>
			<td style="margin-left:15px;">
				Saldo
			</td>
			<td>
				<div id="modificar_saldo"></div>
			</td>
			<td colspan="2">
				<input type="button" id="modificar_cotizar" value="Convertir Remision"/>
			</td>
		</tr>
	</table>
</div>
<!-- PART 4 -->
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
				<div id="ventas_modificar_deuda_total"></div>
			</td>
			<td>
				<div id="ventas_modificar_corriente"></div>
			</td>
			<td>
				<div id="ventas_modificar_de_30_a_45"></div>
			</td>
			<td>
				<div id="ventas_modificar_de_45_a_60"></div>
			</td>
			<td>
				<div id="ventas_modificar_de_60_a_90"></div>
			</td>
			<td>
				<div id="ventas_modificar_mas_de_90"></div>
			</td>
		</tr>
	</table>
</div>