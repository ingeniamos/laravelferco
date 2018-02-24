<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ID_OrdenCompra = "";
	var cantidad_exedida = false;
	var ExistenciaCargada = false;
	var Caja_Interno = "";
	var Caja_Recibo = "";
	var Original_Caja_Recibo = "";
	var Cargar_Recibo = false;
	var ErrorSaldo = true;
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Ventas_Content");
	var Body = document.getElementById("Despachar_Content");
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
				SystemMap("Despacho", true);
				ReDefine();
				ClearDocument();
				// Buscar Ordenes
				OrdenSource.data =  {"Ventas_Despachar":true};
				OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
				$("#despachar_ord_compra").jqxComboBox({source: OrdenAdapter});
				
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
	
	//---
	var Admin = false;
	var Modificar = false;
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
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Despacho" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}

					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Despacho" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Despacho" && $data[$i]["Imprimir"] == "true")
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
		} ?>
	
	$("#despachar_fecha_rem").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
		disabled: true
	});
	$("#despachar_fecha_rem").jqxDateTimeInput('setDate', new Date(currenttime));
	// prepare the data
	var OrdenSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Interno', type: 'string'},
			{ name: 'OrdenCompra', type: 'string'}
		],
		//type: 'GET',
		//data: {"Ventas_Despachar":true},
		url: "modulos/datos.php",
		//async: true
	};
	
	function ReDefine()
	{
		ClearJSON = [
			//-1
			{id:"despachar_ord_compra", type:"jqxComboBox"},
			{id:"despachar_cliente", type:""},
			{id:"despachar_cliente_ID", type:""},
			{id:"despachar_direccion", type:""},
			{id:"despachar_telefono", type:""},
			{id:"despachar_remision", type:""},
			{id:"despachar_interno", type:""},
			{id:"despachar_e-mail", type:""},
			{id:"despachar_contacto_p", type:""},
			{id:"despachar_factura", type:""},
			//-2
			{id:"despachar_codigo", type:"jqxComboBox"},
			{id:"despachar_producto", type:"jqxComboBox"},
			{id:"despachar_existencia", type:""},
			{id:"despachar_cantidad", type:""},
			{id:"despachar_listap", type:"jqxDropDownList"},
			{id:"despachar_items_grid", type:"jqxGrid"},
			//-3
			{id:"despachar_observaciones", type:""},
			{id:"despachar_notas", type:""},
			{id:"despachar_subtotal_total", type:""},
			{id:"despachar_tipo_servicio", type:"jqxComboBox"},
			{id:"despachar_tipo_servicio_precio", type:""},
			{id:"despachar_tipo_descuento", type:"jqxComboBox"},
			{id:"despachar_tipo_descuento_precio", type:""},
			{id:"despachar_iva_precio", type:""},
			{id:"despachar_total_total", type:""},
			{id:"despachar_conductor", type:"jqxComboBox"},
			{id:"despachar_placa", type:"jqxComboBox"},
			{id:"despachar_formaP", type:""},
			{id:"despachar_vendedor", type:"jqxComboBox"},
			{id:"despachar_cobrador", type:"jqxComboBox"},
			{id:"despachar_valor_rc", type:"jqxNumberInput"},
			{id:"despachar_saldo", type:"jqxNumberInput"},
			{id:"despachar_pesokg", type:""},
			{id:"despachar_dir_entrega", type:""},
			{id:"despachar_sector", type:"jqxComboBox"},
			{id:"despachar_ruta", type:""},
		];
		
		EnableDisableJSON = [
			//-1
			{id:"despachar_fecha_rem", type:"jqxDateTimeInput"},
			{id:"despachar_remision", type:""},
			{id:"despachar_factura", type:""},
			//-2
			{id:"despachar_codigo", type:"jqxComboBox"},
			{id:"despachar_producto", type:"jqxComboBox"},
			{id:"despachar_cantidad", type:"jqxNumberInput"},
			{id:"despachar_listap", type:"jqxDropDownList"},
			{id:"ventas_despachar_addrowbutton", type:"jqxButton"},
			{id:"ventas_despachar_deleterowbutton", type:"jqxButton"},
			//-3
			{id:"despachar_subtotal_total", type:"jqxNumberInput"},
			{id:"despachar_tipo_servicio", type:"jqxComboBox"},
			{id:"despachar_tipo_servicio_precio", type:"jqxNumberInput"},
			{id:"despachar_tipo_descuento", type:"jqxComboBox"},
			{id:"despachar_tipo_descuento_precio", type:"jqxNumberInput"},
			{id:"despachar_iva_precio", type:"jqxNumberInput"},
			{id:"despachar_total_total", type:"jqxNumberInput"},
			{id:"despachar_conductor", type:"jqxComboBox"},
			{id:"despachar_placa", type:"jqxComboBox"},
			{id:"despachar_vendedor", type:"jqxComboBox"},
			{id:"despachar_cobrador", type:"jqxComboBox"},
			{id:"despachar_recibo", type:"jqxComboBox"},
			{id:"despachar_dir_entrega", type:""},
			{id:"despachar_sector", type:"jqxComboBox"},
			{id:"despachar_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		// Variables
		ID_OrdenCompra = "";
		cantidad_exedida = false;
		ExistenciaCargada = false;
		Caja_Interno = "";
		Caja_Recibo = "";
		Original_Caja_Recibo = "";
		Cargar_Recibo = false;
		ErrorSaldo = true;
		
		Locked = false;
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		//--- Access
		if (!Admin && !Guardar)
		{
			$("#despachar_guardar").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Modificar)
		{
			$("#despachar_codigo").jqxComboBox({ disabled: true });
			$("#despachar_producto").jqxComboBox({ disabled: true });
			$("#ventas_despachar_addrowbutton").jqxButton({ disabled: true });
			$("#ventas_despachar_deleterowbutton").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#despachar_imprimir").jqxButton({ disabled: true });
		}
	}
	
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
				$("#despachar_valor_rc").val(records[0]["Saldo"]);
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
				{ name: 'Direccion', type: 'string'},
				{ name: 'Email', type: 'string'},
				{ name: 'Telefono', type: 'string'},
				{ name: 'ContactoP', type: 'string'},
				{ name: 'Interno', type: 'string'},
				{ name: 'Remision', type: 'string' },
				{ name: 'Fecha', type: 'string'},
				{ name: 'Factura', type: 'string' },
				{ name: 'ClienteNombre', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Ruta', type: 'string'},
				{ name: 'DireccionEntrega', type: 'string'},
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
			data:{"Ventas_Despachar":ID_OrdenCompra},
			url: "modulos/datos_productos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
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
						"Cantidad2":records[i]["Cantidad"],
						"Dcto":records[i]["Dcto"],
						"Unitario":records[i]["Unitario"],
						"Precio":records[i]["Unitario"],
						"Produccion":records[i]["Produccion"],
						"FacturaExistencia":records[i]["FacturaExistencia"]
					}];
					$("#despachar_items_grid").jqxGrid("addrow", null, datarow, "first");
				}
				$("#despachar_cliente").val(records[0]["ClienteNombre"]);
				$("#despachar_cliente_ID").val(records[0]["ClienteID"]);
				$("#despachar_direccion").val(records[0]["Direccion"]);
				$("#despachar_e-mail").val(records[0]["Email"]);
				$("#despachar_telefono").val(records[0]["Telefono"]);
				$("#despachar_contacto_p").val(records[0]["ContactoP"]);
				$("#despachar_interno").val(records[0]["Interno"]);
				$("#despachar_remision").val(records[0]["Remision"]);
				$("#despachar_fecha_rem").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#despachar_factura").val(records[0]["Factura"]);
				$("#despachar_sector").val(records[0]["Ruta"]);
				$("#despachar_ruta").val(records[0]["Ruta"]);
				$("#despachar_dir_entrega").val(records[0]["DireccionEntrega"]);
				$("#despachar_formaP").val(records[0]["FormaP"]);
				if (records[0]["FormaP"] == "Efectivo")
				{
					$("#despachar_recibo").jqxComboBox({ disabled: false });
					$("#despachar_codigo").jqxComboBox({ disabled: true });
					$("#despachar_producto").jqxComboBox({ disabled: true });
					$("#despachar_cantidad").jqxNumberInput({ disabled: true });
					$("#despachar_listap").jqxDropDownList({ disabled: true });
					$("#ventas_despachar_addrowbutton").jqxButton({ disabled: true });
					$("#ventas_despachar_deleterowbutton").jqxButton({ disabled: true });
					$("#despachar_items_grid").jqxGrid('setcolumnproperty', 'Cantidad', 'editable', false);
					$("#despachar_tipo_servicio").jqxComboBox({ disabled: true });
					$("#despachar_tipo_servicio_precio").jqxNumberInput({ disabled: true });
				}
				else
				{
					$("#despachar_recibo").jqxComboBox({ disabled: true });
					$("#despachar_tipo_servicio").jqxComboBox({ disabled: false });
					$("#despachar_tipo_servicio_precio").jqxNumberInput({ disabled: false });
				}
				
				Caja_Interno = records[0]["Caja_Interno"];
				Original_Caja_Recibo = records[0]["Caja_Recibo"];
				Caja_Recibo = records[0]["Caja_Recibo"];
				if (Caja_Recibo != "")
					Cargar_Recibo = true;
				
				$("#despachar_vendedor").jqxComboBox('selectItem', records[0]["VendedorID"]);
				$("#despachar_cobrador").jqxComboBox('selectItem', records[0]["CobradorID"]);
				$("#despachar_conductor").jqxComboBox('selectItem', records[0]["Conductor"]);
				$("#despachar_placa").jqxComboBox('selectItem', records[0]["Placa"]);
				$("#despachar_tipo_servicio").val(records[0]["TipoServicio"]);
				$("#despachar_tipo_servicio_precio").val(records[0]["TipoServicioValor"]);
				if (records[0]["TipoDescuento"] == "")
					$("#despachar_tipo_descuento").jqxComboBox('clearSelection');
				else
					$("#despachar_tipo_descuento").val(records[0]["TipoDescuento"]);
				
				$("#despachar_tipo_descuento_precio").val(records[0]["TipoDescuentoValor"]);
				
				$("#despachar_observaciones").val(records[0]["Observaciones"]);
				$("#despachar_notas").val(records[0]["Notas"]);
				//RC Combobox
				RC_Source.data = {"RecibosConSaldo": $("#despachar_cliente_ID").val()};
				RC_Adapter = new $.jqx.dataAdapter(RC_Source);
				$("#despachar_recibo").jqxComboBox({source: RC_Adapter});
				Calcular(true);
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	$('#Despachar_HideButton').click(function() {
		$("#Despachar_Content_to_Hide").toggle();
	});
	//------------------------------------------- KEY JUMPS
	$('#despachar_ord_compra').keyup(function(event) {
		if(event.which == 13)
		{
			$("#despachar_cliente").jqxComboBox('focus');
		}
	});
	$('#despachar_cliente').keyup(function(event) {
		if(event.which == 13)
		{
			$("#despachar_cliente_ID").jqxComboBox('focus');
		}
	});
	$('#despachar_cliente_ID').keyup(function(event) {
		if(event.which == 13)
		{
			$("#despachar_fecha_rem").jqxDateTimeInput('focus');
		}
	});
	$('#despachar_fecha_rem').keyup(function(event) {
		if(event.which == 13)
		{
			$("#despachar_remision").jqxInput('focus');
		}
	});
	$('#despachar_remision').keyup(function(event) {
		if(event.which == 13)
		{
			$("#despachar_factura").jqxInput('focus');
		}
	});
	$('#despachar_factura').keyup(function(event) {
		if(event.which == 13)
		{
			$("#despachar_codigo").jqxComboBox('focus');
		}
	});
	$('#despachar_codigo').keyup(function(event) {
		if(event.which == 13)
		{
			$("#despachar_producto").jqxComboBox('focus');
		}
	});
	$('#despachar_producto').keyup(function(event) {
		if(event.which == 13)
		{
			if (ExistenciaCargada == false) {
				return;
			}
			else {
				$("#despachar_cantidad").jqxNumberInput('focus');
				var input = $('#despachar_cantidad input')[0];
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
	$('#despachar_cantidad').keyup(function(event) {
		if(event.which == 13 && cantidad_exedida == false)
		{
			$("#despachar_listap").jqxDropDownList('focus');
		}
	});
	$('#despachar_listap').keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row();
		}
	});
	
	$("#despachar_cliente").jqxInput(
	{
		width: 420,
		height: 20,
		theme: mytheme,
		disabled: true,
	});
	
	$("#despachar_cliente_ID").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true,
	});
	
	$("#despachar_direccion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 420,
		disabled: true,
	});
	
	$("#despachar_telefono").jqxInput({//.jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 112,
		//mask: '(###)###-####',
		disabled: true,
	});
	
	$("#despachar_contacto_p").jqxInput({
		theme: mytheme,
		height: 20,
		width: 200,
		disabled: true,
	});
	
	$("#despachar_e-mail").jqxInput({
		theme: mytheme,
		height: 20,
		width: 130,
		disabled: true,
	});
	
	$("#despachar_remision").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	
	$("#despachar_ord_compra").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Orden',
		selectedIndex: -1,
		displayMember: 'OrdenCompra',
		valueMember: 'Interno'
	});
	$("#despachar_ord_compra").bind('change', function (event)
	{
		if (event.args)
		{
			if (ID_OrdenCompra == event.args.item.value)
				return;
			
			ID_OrdenCompra = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (ClickOK == true)
				{
					EnableDisableAll(false);
					ClickOK = false;
				}
				ClearAll("despachar_ord_compra");
				CargarValores();
			},800);
		}
		else
		{
			var item_value = $("#despachar_ord_compra").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClearDocument();
			}
			else
			{
				var value = $("#despachar_ord_compra").val();
				var item = $("#despachar_ord_compra").jqxComboBox('getItems');
				
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#despachar_ord_compra").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				ClearDocument();
			}
		}
	});
	// $("#despachar_ord_compra").bind('bindingComplete', function (event) {
	// 	if (ID_OrdenCompra != "")
	// 		$("#despachar_ord_compra").jqxComboBox('selectItem', ID_OrdenCompra);
	// });
	// Buscar Ordenes
	OrdenSource.data =  {"Ventas_Despachar":true};
	OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
	$("#despachar_ord_compra").jqxComboBox({source: OrdenAdapter});
	
	$("#despachar_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	
	$("#despachar_interno").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true
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
			data:{"Precios":$("#despachar_codigo").val()},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				$("#despachar_listap").jqxDropDownList('clear');
				$("#despachar_existencia").val('');
				var records = GetValuesAdapter.records;
				$("#despachar_listap").jqxDropDownList('addItem', {label: "P1 $"+ records[0]["Lista1"], value: records[0]["Lista1"]});
				$("#despachar_listap").jqxDropDownList('addItem', {label: "P2 $"+ records[0]["Lista2"], value: records[0]["Lista2"]});
				$("#despachar_listap").jqxDropDownList('addItem', {label: "P3 $"+ records[0]["Lista3"], value: records[0]["Lista3"]});
				$("#despachar_listap").jqxDropDownList('addItem', {label: "P4 $"+ records[0]["Lista4"], value: records[0]["Lista4"]});
				$("#despachar_listap").jqxDropDownList('selectIndex', 0);
				
				var datinfo = $("#despachar_items_grid").jqxGrid('getdatainformation');
				var count = datinfo.rowscount;
				var totalc = 0;
				for (i=0;i<count;i++) {
					var currentRow = $('#despachar_items_grid').jqxGrid('getrowdata', i);
					if (currentRow.CodFab == $("#despachar_codigo").val())
					{
						totalc = totalc + currentRow.Cantidad;
					}
				}
				var totala = records[0]["Existencia"] - totalc;
				if (totala < 0)
					totala = 0;
				$("#despachar_existencia").val(totala);
				FacturaExistencia = records[0]["FacturaExistencia"];
				ExistenciaCargada = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: " + error, 3);
			},
		});
	};
	
	$("#despachar_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: CB_ProductoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#despachar_codigo").bind('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#despachar_producto").val() != event.args.item.value)
					$("#despachar_producto").jqxComboBox('selectItem', event.args.item.value);
				GetPriceList();
			},300);
		}
		else
		{
			var item_value = $("#despachar_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#despachar_codigo").jqxComboBox('clearSelection');
				$("#despachar_producto").jqxComboBox('clearSelection');
				$("#despachar_existencia").val("");
			}
			else
			{
				var value = $("#despachar_codigo").val();
				var item = $("#despachar_codigo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#despachar_codigo").jqxComboBox('clearSelection');
					$("#despachar_producto").jqxComboBox('clearSelection');
					$("#despachar_existencia").val("");
				}
				else
					$("#despachar_codigo").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#despachar_producto").jqxComboBox(
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
	$("#despachar_producto").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#despachar_codigo").val() != event.args.item.value)
				$("#despachar_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#despachar_producto").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#despachar_codigo").jqxComboBox('clearSelection');
				$("#despachar_producto").jqxComboBox('clearSelection');
				$("#despachar_existencia").val("");
			}
			else
			{
				var value = $("#despachar_producto").val();
				var item = $("#despachar_producto").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#despachar_producto").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#despachar_codigo").jqxComboBox('clearSelection');
				$("#despachar_producto").jqxComboBox('clearSelection');
				$("#despachar_existencia").val("");
			}
		}
	});
	
	$("#despachar_existencia").jqxInput({
		theme: mytheme,
		height: 20,
		width: 140,
		disabled: true,
		rtl: true
	});
	
	$("#despachar_cantidad").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 170,
		inputMode: 'simple',
		spinButtons: false
	});
	
	var Data = {};
	
	$("#despachar_listap").jqxDropDownList({
		theme: mytheme,
		height: 20,
		width: 186,
		source: Data,
		placeHolder: 'Lista de Precios',
		selectedIndex: -1
	});
	
	$('#despachar_form_validation').jqxValidator({
		rules:
		[
			{
				input: '#despachar_cantidad', message: 'Debe ser un valor mayor a 0!', action: 'keyup, blur', rule: function (input, commit) {
					if (input.val() < 0) {
						return false;
					}
					return true;
				}
			},
			{
				input: '#despachar_cantidad', message: 'Cantidad mayor a Existencia!', action: 'keyup, blur', rule: function (input, commit) {
					if (input.val() > $("#despachar_existencia").val() && FacturaExistencia == false) {
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
	
		var Product = $("#despachar_producto").jqxComboBox('getSelectedItem');
		var ProductPrice = $("#despachar_listap").jqxDropDownList('getSelectedItem');
		var ExistenciaNum = $("#despachar_existencia").val();
		var CantidadNum = $("#despachar_cantidad").val();
		
		if (!Product) {
			Alerts_Box("Favor Seleccionar un Producto!", 4);
			WaitClick("despachar_producto");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum > ExistenciaNum && FacturaExistencia == false) {
			Alerts_Box("Cantidad mayor a la Existencia!", 4);
			WaitClick("despachar_cantidad");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 4);
			WaitClick("despachar_cantidad");
			RowAdded = true;
			return;
		}
		
		if (ExistenciaNum <= 0 && FacturaExistencia == false) {
			Alerts_Box("\"AGOTADO\" No hay Existencias.</br>Favor Seleccionar otro Producto", 4);
			WaitClick("despachar_producto");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#despachar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $('#despachar_items_grid').jqxGrid('getrowdata', i);
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
				var id = $("#despachar_items_grid").jqxGrid('getrowid', i);
				$("#despachar_items_grid").jqxGrid('deleterow', id);
				$("#despachar_items_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#despachar_codigo").jqxComboBox('clearSelection');
				$("#despachar_producto").jqxComboBox('clearSelection');
				$("#despachar_existencia").val('');
				$("#despachar_cantidad").val('');
				$("#despachar_listap").jqxDropDownList('clear');
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
					"Cantidad2":0,
					"Peso":(parseFloat(records[0]["Peso"]) * CantidadNum).toFixed(2),
					"Peso_Unitario":records[0]["Peso"],
					"Dcto":0,
					"Unitario":ProductPrice.value,
					"Precio":ProductPrice.value,
					"Produccion":records[0]["Produccion"],
					"FacturaExistencia":records[0]["FacturaExistencia"]
				}];
				$("#despachar_items_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#despachar_codigo").jqxComboBox('clearSelection');
				$("#despachar_producto").jqxComboBox('clearSelection');
				$("#despachar_existencia").val('');
				$("#despachar_cantidad").val('');
				$("#despachar_listap").jqxDropDownList('clear');
				Calcular();
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: "+error, 3);
			},
		});
	};

	$("#despachar_items_grid").jqxGrid(
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
				'<input type="button" id="ventas_despachar_addrowbutton" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="ventas_despachar_deleterowbutton" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#ventas_despachar_addrowbutton").jqxButton({theme: mytheme, template: "success"});
			$("#ventas_despachar_deleterowbutton").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#ventas_despachar_addrowbutton").on('click', function () {
				Add_Row();
			});
			// delete row.
			$("#ventas_despachar_deleterowbutton").on('click', function () {
				var selectedrowindex = $("#despachar_items_grid").jqxGrid('getselectedrowindex');
				var rowscount = $("#despachar_items_grid").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#despachar_items_grid").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#despachar_items_grid").jqxGrid('deleterow', id);
					Calcular();
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: 90, height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: 200, height: 20 },
			{ text: 'Und', datafield: 'UndMed', editable: false, width: 50, height: 20 },
			{ text: 'Exist.', datafield: 'Existencia', editable: false, width: 100, height: 20, cellsalign: 'right' },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: 80,
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor o igual a 0!" };
					}
					return true;
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
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor o igual a 0!" };
					}
					return true;
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					if (rowdata.Cantidad2 == 0)
						return;
					else if (rowdata.Cantidad2 > rowdata.Cantidad) {
						Alerts_Box("La Cantidad Despachada no puede ser mayor a la cantidad aprobada!", 3);
						return "<div id='row_error'>"+rowdata.Cantidad2+"</div>";
					}
					else if (rowdata.Cantidad2 > rowdata.Existencia && rowdata.FacturaExistencia == false) {
						Alerts_Box("La Cantidad Despachada no debe ser mayor a la Existencia!", 3);
						return "<div id='row_error'>"+rowdata.Cantidad2+"</div>";
					}
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Peso',
				datafield: 'Peso',
				width: 90,
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
				width: 50,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'p',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor o igual a 0!" };
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
				editable: Admin ? true:Modificar,
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
					var total = Math.round(parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad2));
					//var total2 = Math.round( total - (parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad2)) / 100 * parseFloat(rowdata.Dcto) );
					
					return "<div style='margin: 4px;' class='jqx-right-align'>" + dataAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
			{ text: '', datafield: 'Peso_Unitario', editable: false, columntype: 'numberinput', width: 0, height: 20},
			{ text: '', datafield: 'Precio', editable: false, columntype: 'numberinput', width: 0, height: 20},
			{ text: 'P', datafield: 'Produccion', columntype: 'checkbox', width: 15 },
			{ text: 'F', datafield: 'FacturaExistencia', columntype: 'checkbox', width: 15 },
		]
	});
	
	$('#despachar_items_grid').jqxGrid('hidecolumn', 'Peso_Unitario');
	$('#despachar_items_grid').jqxGrid('hidecolumn', 'Precio');
	$('#despachar_items_grid').jqxGrid('hidecolumn', 'Produccion');
	$('#despachar_items_grid').jqxGrid('hidecolumn', 'FacturaExistencia');
	
	$("#despachar_items_grid").on('cellvaluechanged', function (event) 
	{
		if (event.args)
		{
			var args = event.args;
			var datafield = event.args.datafield;
			var rowBoundIndex = args.rowindex;
			var value = args.newvalue;
			var oldvalue = args.oldvalue;
			
			if (datafield == "Cantidad" || datafield == "Cantidad2" || datafield == "Dcto" || datafield == "Unitario")
			{
				if (datafield == "Cantidad")
				{
					var Peso = parseFloat($("#despachar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Peso_Unitario"));
					var Total = (Peso * value);

					if (Total < 0)
						Total = 0;
					
					$("#despachar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Peso", Total);	
				}
				else if (datafield == "Dcto")
				{
					var Precio = parseFloat($("#despachar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Precio"));
					var Total = Precio - ((Precio / 100) * value);
					$("#despachar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Unitario", Total);
				}
				else if (datafield == "Unitario")
				{
					var Precio = parseFloat($("#despachar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Precio"));
					var Total = (((Precio - value) / Precio) * 100).toFixed(2);

					if (Total < 0)
						Total = 0;
					
					$("#despachar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Dcto", Total);	
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
	
	$("#despachar_conductor").jqxComboBox({
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
	$("#despachar_conductor").bind('change', function (event) {
		if (!event.args)
			$("#despachar_conductor").jqxComboBox('clearSelection');
	});
	
	$("#despachar_formaP").jqxInput({
		theme: mytheme,
		height: 20,
		width: 148,
		disabled: true
	});
	
	$("#despachar_placa").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 95,
		source: VehiculoDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Placa',
		selectedIndex: -1,
		displayMember: 'Placa',
		valueMember: 'Placa',
		searchMode: 'startswithignorecase',
		autoComplete: true,
	});
	$("#despachar_placa").bind('change', function (event) {
		if (!event.args)
			$("#despachar_placa").jqxComboBox('clearSelection');
	});
	
	$("#despachar_ruta").jqxInput({
		theme: mytheme,
		height: 20,
		width: 95,
		disabled: true
	});
	
	$("#despachar_pesokg").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 95,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 12,
		disabled: true
	});
	
	$("#despachar_recibo").jqxComboBox({
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
	$("#despachar_recibo").on('bindingComplete', function (event){
		if (Cargar_Recibo)
		{
			var items = $("#despachar_recibo").jqxComboBox('getVisibleItems');
			var len = items.length;
			for (var i = 0; i < len; i++)
			{
				var tmp = items[i];
				if (tmp.label == Caja_Recibo) {
					$("#despachar_recibo").jqxComboBox("selectIndex", tmp.index);
					$("#despachar_recibo").jqxComboBox({ disabled: true });
					Cargar_RC();
					return;
				}
			}
			$("#despachar_recibo").jqxComboBox("addItem", { label: Caja_Recibo, value: Caja_Interno});
			$("#despachar_recibo").jqxComboBox("selectItem", Caja_Recibo);
			$("#despachar_recibo").jqxComboBox({ disabled: true });
		}
	});
	$("#despachar_recibo").bind('change', function (event) {
		if (!event.args) {
			$("#despachar_recibo").jqxComboBox('clearSelection');
			$("#despachar_valor_rc").val('');
			$("#despachar_saldo").val('');
			Caja_Interno = "";
			Caja_Recibo = "";
		}
		else
		{
			Caja_Interno = event.args.item.value;
			Caja_Recibo = event.args.item.label;
		}
	});
	$("#despachar_recibo").bind('select', function (event) {
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
	
	$("#despachar_dir_entrega").jqxInput({
		theme: mytheme,
		height: 20,
		width: 430
	});
	
	$("#despachar_valor_rc").jqxNumberInput({
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
	
	$("#despachar_sector").jqxComboBox({
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
	$("#despachar_sector").bind('change', function (event) {
		if (!event.args) {
			$("#despachar_sector").jqxComboBox('clearSelection');
			$("#despachar_ruta").val('');
		}
	});
	$("#despachar_sector").bind('select', function (event) {
		if (event.args) {
			var ruta_val = event.args.item.value;
			$("#despachar_ruta").val(ruta_val);
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
	
	$("#despachar_vendedor").jqxComboBox({
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
	$("#despachar_vendedor").bind('change', function (event) {
		if (!event.args) {
			$("#despachar_vendedor").jqxComboBox('clearSelection');
		}
	});
	
	$("#despachar_cobrador").jqxComboBox({
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
	$("#despachar_cobrador").bind('change', function (event) {
		if (!event.args) {
			$("#despachar_cobrador").jqxComboBox('clearSelection');
		}
	});
	
	$("#despachar_saldo").jqxNumberInput({
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
	
	var Subtotal_Text =
	$("#despachar_subtotal_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		//textAlign: 'center',
		disabled: true,
		placeHolder: "               SUBTOTAL",
	});
	
	var Subtotal_Total =
	$("#despachar_subtotal_total").jqxNumberInput({
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
			var information = $('#despachar_items_grid').jqxGrid('getdatainformation');
			var rowscounts = information.rowscount;
			var old_total = 0;
			var total_peso = 0;
			for (i=0; i<rowscounts; i++){
				var currentRow = $('#despachar_items_grid').jqxGrid('getrowdata', i);
				var total1 = parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad2);
				//var total2 = Math.round( total1 - (parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad2)) / 100 * parseFloat(currentRow.Dcto) );
				old_total = old_total + total1;
				total_peso = total_peso + parseFloat(currentRow.Peso_Unitario) * parseFloat(currentRow.Cantidad2);
			};
			
			var valor_tipo = $("#despachar_tipo_servicio_precio").val();
			if ($("#despachar_formaP").val() != "Efectivo")
				old_total = old_total + valor_tipo;
			
			if (total == old_total && Ignore == false)
				return;
			else
				total = old_total;
			
			iva = Math.round(total - (total / 1.19));
			
			// if ($('#despachar_fecha_rem').val() >= '11-ene-2017')
			// 	iva = Math.round(total - (total / 1.19));
			// else
			// 	iva = Math.round(total - (total / 1.16));

			var tipo_dcto = $("#despachar_tipo_descuento_precio").val();
			var dcto = $("#despachar_tipo_descuento").val();
			if (tipo_dcto <= 0 && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#despachar_tipo_descuento_precio").val(tipo_dcto);
			}
			else if (tipo_dcto != iva && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#despachar_tipo_descuento_precio").val(tipo_dcto);
			}
			else if (tipo_dcto <= 0 && dcto.indexOf("IVA") < 1)
			{
				$("#despachar_tipo_descuento_precio").val("0");
			}
			
			var subtotal = Math.round(total);
			total = Math.round(total - tipo_dcto);
			if (total < 0)
				total = 0;
			
			if ($("#despachar_formaP").val() == "Efectivo")
			{
				var ValorRC = parseFloat($("#despachar_valor_rc").val());
				
				if ($("#despachar_recibo").val() != "")
				{
					if (Original_Caja_Recibo != Caja_Recibo)
					{
						if (total > ValorRC)
						{
							Alerts_Box("El Valor de la Factura es Mayor a el Saldo Disponible", 3);
							$("#despachar_valor_rc").addClass("jqx-validator-error-element");
							$("#despachar_saldo").val("0");
							ErrorSaldo = true;
						}
						else
						{
							$("#despachar_valor_rc").removeClass("jqx-validator-error-element");
							$("#despachar_saldo").val(ValorRC - total);
							ErrorSaldo = false;
						}
					}
				}
			}
			else
			{
				$("#despachar_valor_rc").removeClass("jqx-validator-error-element");
				ErrorSaldo = false;
			}
			
			$("#despachar_subtotal_total").val(subtotal);
			$("#despachar_iva_precio").val(iva);
			$("#despachar_total_total").val(total);
			$("#despachar_pesokg").val(total_peso);
			$("#despachar_codigo").jqxComboBox('focus');
			clearTimeout(Calc_Timer);
		},500);
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
	
	$("#despachar_tipo_servicio").jqxComboBox({
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
	$("#despachar_tipo_servicio").bind('change', function (event) {
		if (!event.args) {
			$("#despachar_tipo_servicio").jqxComboBox('clearSelection');
		}
	});
	
	$("#despachar_tipo_servicio_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$('#despachar_tipo_servicio_precio').on('change', function (event) 
	{
		Calcular();
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("despachar_tipo_servicio_precio");
		}
	});
	
	$("#despachar_iva_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                     IVA",
	});
	
	$("#despachar_iva_precio").jqxNumberInput({
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
	
	$("#despachar_tipo_descuento").jqxComboBox({
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
	$("#despachar_tipo_descuento").bind('change', function (event) {
		if (!event.args) {
			$("#despachar_tipo_descuento").jqxComboBox('clearSelection');
			$("#despachar_tipo_descuento_precio").val("0");
		}
		else
			Calcular(true);
	});
	
	$("#despachar_tipo_descuento_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$('#despachar_tipo_descuento_precio').on('change', function (event) 
	{
		Calcular(true);
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("despachar_tipo_descuento_precio");
		}
	});
	
	$("#despachar_total_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                  TOTAL",
	});
	
	$("#despachar_total_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	
	function CrearDespacho ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ErrorSaldo) {
			Alerts_Box("El Valor de la Factura es mayor al Saldo Disponible", 3);
			Locked = false;
			return;
		}
		
		var Orden = $("#despachar_ord_compra").jqxComboBox('getSelectedItem');
		if (!Orden) {
			Alerts_Box("Debe Ingresar una Orden de Compra!", 3);
			WaitClick_Combobox("despachar_ord_compra");
			Locked = false;
			return;
		}
		
		var Recibo = $("#despachar_recibo").jqxComboBox('getSelectedItem');
		if ($("#despachar_formaP").val() == "Efectivo") {
			if (!Recibo || ErrorSaldo == true) {
				Alerts_Box("Debe ingresar un Recibo de Caja.", 3);
				WaitClick();
				Locked = false;
				return;
			}
		}
		
		var datinfo = $("#despachar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		if (count <= 0) {
			Alerts_Box("Debe Ingresar un Producto!", 3);
			WaitClick_Combobox("despachar_producto");
			Locked = false;
			return;
		}
		
		if ($("#despachar_tipo_servicio_precio").val() > 0) {
			if ($("#despachar_tipo_servicio").val() < 0 | $("#despachar_tipo_servicio").val() == "") {
				Alerts_Box("Debe Ingresar Una Tipo de Servicio", 3);
				WaitClick_Combobox("despachar_tipo_servicio");
				Locked = false;
				return;
			}
		}
		
		if ($("#despachar_conductor").val() < 0 | $("#despachar_conductor").val() == "") {
			Alerts_Box("Debe Ingresar Un Conductor!", 3);
			WaitClick_Combobox("despachar_conductor");
			Locked = false;
			return;
		}
		
		if ($("#despachar_placa").val() < 0 | $("#despachar_placa").val() == "") {
			Alerts_Box("Debe Ingresar Un Vehiculo!", 3);
			WaitClick_Combobox("despachar_placa");
			Locked = false;
			return;
		}
		
		if ($("#despachar_formaP").val() == "") {
			Alerts_Box("Debe Ingresar Una Forma de Pago!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		var Vendedor = $("#despachar_vendedor").jqxComboBox('getSelectedItem');
		var Cobrador = $("#despachar_cobrador").jqxComboBox('getSelectedItem');
		
		if (!Vendedor) {
			Alerts_Box("Debe Ingresar Un Vendedor!", 3);
			WaitClick_Combobox("despachar_vendedor");
			Locked = false;
			return;
		}
		
		if (!Cobrador) {
			Alerts_Box("Debe Ingresar Un Cobrador!", 3);
			WaitClick_Combobox("despachar_cobrador");
			Locked = false;
			return;
		}
		
		if ($("#despachar_sector").val() < 0 | $("#despachar_sector").val() == "") {
			Alerts_Box("Debe Ingresar Un Sector!", 3);
			WaitClick_Combobox("despachar_sector");
			Locked = false;
			return;
		}

		for (var i = 0; i < count; i++)
		{
			var array = {};
			var currentRow = $('#despachar_items_grid').jqxGrid('getrowdata', i);
			
			array["CodFab"] = currentRow.CodFab;
			array["Cantidad"] = currentRow.Cantidad;
			array["Cantidad2"] = currentRow.Cantidad2;
			array["Dcto"] = currentRow.Dcto;
			array["Unitario"] = currentRow.Unitario;
			
			if (currentRow.Cantidad2 > 0)
			{
				if (currentRow.Cantidad2 > currentRow.Cantidad) {
					Alerts_Box("Error en Producto "+currentRow.CodFab+"<br/>La Cantidad a Despachar es Mayor a la Cantidad Definida!", 3);
					Locked = false;
					return;
				}
				
				if (currentRow.Cantidad2 > currentRow.Existencia && currentRow.FacturaExistencia == false) {
					Alerts_Box("Error en Producto "+currentRow.CodFab+"<br/>La Cantidad a Despachar es Mayor a la Existencia!", 3);
					Locked = false;
					return;
				}
			}
			
			if (i == 0)
			{
				array["ClienteID"] = $("#despachar_cliente_ID").val();
				array["Fecha"] = GetFormattedDate($('#despachar_fecha_rem').jqxDateTimeInput('getDate'));
				array["Remision"] = $("#despachar_remision").val();
				array["Ord_Compra"] = Orden.label;
				array["Factura"] = $("#despachar_factura").val();
				array["Interno"] = $("#despachar_interno").val();
				//---
				array["Observaciones"] = $('#despachar_observaciones').val();
				array["Subtotal2"] = $("#despachar_subtotal_total").val();
				array["TipoServicio"] = $("#despachar_tipo_servicio").val();
				array["TipoServicioValor"] = $("#despachar_tipo_servicio_precio").val();
				array["Iva"] = $("#despachar_iva_precio").val();
				array["TipoDcto"] = $("#despachar_tipo_descuento").val();
				array["TipoDctoValor"] = $("#despachar_tipo_descuento_precio").val();
				array["Total"] = $("#despachar_total_total").val();
				array["Chofer"] = $("#despachar_conductor").val();
				array["Placa"] = $("#despachar_placa").val();
				array["Peso"] = $("#despachar_pesokg").val();
				array["FormaPago"] = $("#despachar_formaP").val();
				array["Ruta"] = $("#despachar_ruta").val();
				//var item = $("#despachar_recibo").jqxComboBox('getSelectedItem');
				if ($("#despachar_formaP").val() == "Efectivo")
				{
					array["Caja_Interno"] = Caja_Interno;
					array["Caja_Recibo"] = Caja_Recibo;
				}
				else
				{
					array["Caja_Interno"] = "";
					array["Caja_Recibo"] = "";
				}
				array["Direccion"] = $("#despachar_dir_entrega").val();
				array["ValorRC"] = $("#despachar_valor_rc").val();
				array["VendedorID"] = Vendedor.value;
				array["CobradorID"] = Cobrador.value;
				array["Saldo"] = $("#despachar_saldo").val();
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
			type: 'POST',
			url: "modulos/guardar.php",
			data: {"Ventas_Despachar":myarray},
			async: true,
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				/*alert(data);
				return;*/
				
				switch(data[0]["MESSAGE"])
				{
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
					break;
					
					default:
						EnableDisableAll(true);
						Alerts_Box("Datos Guardados con Exito!", 2);
						$("#despachar_interno").val(data[0]["MESSAGE"]);
						$("#despachar_recibo").jqxComboBox('clearSelection');
						$("#despachar_recibo").jqxComboBox('clear');
						// Actualizar Ordenes
						OrdenSource.data =  {"Ventas_Despachar":true};
						OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
						$("#despachar_ord_compra").jqxComboBox({source: OrdenAdapter});
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
	
	$('#despachar_guardar').jqxButton({
		width: 135,
		template: "info"
	});
	// Prepare Save Changes...
	$("#despachar_guardar").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		CrearDespacho();
	});
	
	$("#despachar_imprimir").jqxButton({width: 135, template: "warning"});
	$("#despachar_imprimir").bind('click', function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		window.open("imprimir/factura.php?Interno="+$("#despachar_interno").val()+"", "", "width=700, height=600, menubar=no, titlebar=no");
	});
	
	//--- Access
	if (!Admin && !Guardar)
	{
		$("#despachar_guardar").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Modificar)
	{
		$("#despachar_codigo").jqxComboBox({ disabled: true });
		$("#despachar_producto").jqxComboBox({ disabled: true });
		$("#ventas_despachar_addrowbutton").jqxButton({ disabled: true });
		$("#ventas_despachar_deleterowbutton").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Imprimir)
	{
		$("#despachar_imprimir").jqxButton({ disabled: true });
	}

	$("#despachar_ord_compra").jqxInput('focus');
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<div id="Despachar_HideButton">&nbsp;&raquo;&nbsp;</div>
	<div id="Despachar_Content_to_Hide">
		<table cellpadding="1" cellspacing="1">
			<tr>
				<td>
					Ord. Compra
				</td>
				<td>
					<div id="despachar_ord_compra"></div>
				</td>
				<td>
					Cliente
				</td>
				<td colspan="4">
					<input type="text" id="despachar_cliente"/>
				</td>
				<td>
					ID Cliente
				</td>
				<td>
					<input type="text" id="despachar_cliente_ID"/>
				</td>
			</tr>
			<tr>
				<td>
					F&eacute;cha Rem.
				</td>
				<td>
					<div id="despachar_fecha_rem"></div>
				</td>
				<td>
					Direcci&oacute;n
				</td>
				<td colspan="4">
					<input type="text" id="despachar_direccion"/>
				</td>
				<td>
					Tel&eacute;fono.
				</td>
				<td>
					<input type="text" id="despachar_telefono"/>
				</td>
			</tr>
			<tr>
				<td>
					Remisi&oacute;n
				</td>
				<td>
					<input type="text" id="despachar_remision"/>
				</td>
				<td>
					Factura
				</td>
				<td>
					<input type="text" id="despachar_factura"/>
				</td>
				<td>
					Interno
				</td>
				<td>
					<input type="text" id="despachar_interno"/>
				</td>
				<td>
					<input type="text" id="despachar_e-mail"/>
				</td>
				<td colspan="2">
					<!--E-Mail>-->
					<!--<li>
						<input type="text" id="despachar_e-mail"/>
					</li>-->
					<!--Contacto P.-->
					<!--<li>-->
						<input type="text" id="despachar_contacto_p"/>
					<!--</li>-->
				</td>
			</tr>
		</table>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<form id="despachar_form_validation" action="./">
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
					<div id="despachar_codigo" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="despachar_producto" style="margin-left:7px;"></div>
				</td>
				<td>
					<input type="text" id="despachar_existencia" style="margin-left:7px;"/>
				</td>
				<td>
					<div id="despachar_cantidad" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="despachar_listap" style="margin:0px 7px;"></div>
				</td>
			</tr>
		</table>
	</form>
	<div id="despachar_items_grid" style="margin:0px 0px 10px 0px;"></div>
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
				<input type="text" id="despachar_subtotal_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="despachar_subtotal_total">
				</div>
			</td>
		</tr>
		<tr>
			<td rowspan="4">
				<textarea rows="7" cols="35" id="despachar_observaciones" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td rowspan="4">
				<textarea rows="7" cols="25" id="despachar_notas" readonly="true" style="resize:none; margin-left:15px;"></textarea>
			</td>
			<td>
				<div id="despachar_tipo_servicio" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="despachar_tipo_servicio_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="despachar_iva_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="despachar_iva_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="despachar_tipo_descuento" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="despachar_tipo_descuento_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="despachar_total_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="despachar_total_total">
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
				<div id="despachar_conductor"></div>
			</td>
			<td>
				Placa:
			</td>
			<td>
				<div id="despachar_placa"></div>
			</td>
			<td>
				Peso
			</td>
			<td>
				<div id="despachar_pesokg"></div>
			</td>
			<td>
				F. Pago
			</td>
			<td>
				<input type="text" id="despachar_formaP"/>
			</td>
			<td>
				<input type="button" id="despachar_guardar" value="Despachar"/>
			</td>
			<td>
				<input type="button" id="despachar_imprimir" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<td>
				Vendedor
			</td>
			<td colspan="5">
				<li>
					<div id="despachar_vendedor"></div>
				</li>
				<li class="parte1_li_txt">
					Cobrador
				</li>
				<li>
					<div id="despachar_cobrador"></div>
				</li>
			</td>
			<td>
				R. Caja
			</td>
			<td>
				<div id="despachar_recibo">
				</div>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Dir. Entrega
			</td>
			<td colspan="5">
				<input type="text" id="despachar_dir_entrega"/>
			</td>
			<td>
				Valor RC
			</td>
			<td>
				<div id="despachar_valor_rc"></div>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Sector
			</td>
			<td colspan="3">
				<div id="despachar_sector">
				</div>
			</td>
			<td>
				Ruta:
			</td>
			<td>
				<input type="text" id="despachar_ruta"/>
			</td>
			<td style="margin-left:15px;">
				Saldo
			</td>
			<td>
				<div id="despachar_saldo"></div>
			</td>
		</tr>
	</table>
</div>