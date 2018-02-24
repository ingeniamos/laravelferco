<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ID_Cliente = "";
	var ID_Interno = "";
	var CostoCargado = false;
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Compras_Content");
	var Body = document.getElementById("Compras_Modificar_Content");
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
				ReDefine();
				ClearDocument();
				// Actualizar Ordenes
				EntradaSource.data =  {"Compras_Modificar":true};
				var EntradaAdapter = new $.jqx.dataAdapter(EntradaSource);
				$("#compras_modificar_interno").jqxComboBox({source: EntradaAdapter});
				
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
	
	function ClearDocument()
	{
		// Clean Variables
		ID_Cliente = "";
		ID_Interno = "";
		CostoCargado = false;
		Locked = false;
		//
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	};
	
	$("#compras_modificar_fecha_comp").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#compras_modificar_fecha_comp").jqxDateTimeInput('setDate', new Date(currenttime));
	// prepare the data
	var EntradaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Interno', type: 'string'},
			{ name: 'Entrada', type: 'string'}
		],
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
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
	
	// Clean some Fields
	function ClearFields ()
	{
		//$("#compras_modificar_interno").jqxComboBox('clearSelection');
		$("#compras_modificar_entrada").val('');
		$("#compras_modificar_doc_transp").val('');
		$("#compras_modificar_pedido").val('');
		$("#compras_modificar_factura").val('');
		//$("#compras_modificar_fecha_comp").val('');
		$("#compras_modificar_cliente").jqxComboBox('clearSelection');
		$("#compras_modificar_cliente_ID").jqxComboBox('clearSelection');
		$("#compras_modificar_direccion").val('');
		$("#compras_modificar_telefono").val('');
		$("#compras_modificar_contacto_p").val('');
		$("#compras_modificar_products_grid").jqxGrid('clear');
	};
	
	function ReDefine()
	{
		ClearJSON = [
			//-1
			{id:"compras_modificar_interno", type:"jqxComboBox"},
			{id:"compras_modificar_entrada", type:""},
			{id:"compras_modificar_doc_transp", type:""},
			{id:"compras_modificar_factura", type:""},
			{id:"compras_modificar_pedido", type:""},
			{id:"compras_modificar_cliente", type:"jqxComboBox"},
			{id:"compras_modificar_cliente_ID", type:"jqxComboBox"},
			{id:"compras_modificar_direccion", type:""},
			{id:"compras_modificar_telefono", type:"jqxMaskedInput"},
			{id:"compras_modificar_e-mail", type:""},
			{id:"compras_modificar_contacto_p", type:""},
			//-2
			{id:"compras_modificar_codigo", type:"jqxComboBox"},
			{id:"compras_modificar_producto", type:"jqxComboBox"},
			{id:"compras_modificar_ultimo_costo", type:""},
			{id:"compras_modificar_cantidad", type:""},
			{id:"compras_modificar_nuevo_costo", type:""},
			{id:"compras_modificar_products_grid", type:"jqxGrid"},
			//-3
			{id:"compras_modificar_observaciones", type:""},
			{id:"compras_modificar_notas", type:""},
			{id:"compras_modificar_subtotal_total", type:""},
			{id:"compras_modificar_tipo_servicio", type:"jqxComboBox"},
			{id:"compras_modificar_tipo_servicio_precio", type:""},
			{id:"compras_modificar_tipo_descuento", type:"jqxComboBox"},
			{id:"compras_modificar_tipo_descuento_precio", type:""},
			{id:"compras_modificar_iva_precio", type:""},
			{id:"compras_modificar_total_total", type:""},
			{id:"compras_modificar_conductor", type:""},
			{id:"compras_modificar_placa", type:""},
			{id:"compras_modificar_formaP", type:"jqxComboBox"},
			{id:"compras_modificar_peso_bascula", type:""},
			{id:"compras_modificar_peso_remision", type:""},
			{id:"compras_modificar_pesokg", type:""},
		];

		EnableDisableJSON = [
			//-1
			{id:"compras_modificar_doc_transp", type:""},
			{id:"compras_modificar_factura", type:""},
			{id:"compras_modificar_pedido", type:""},
			{id:"compras_modificar_fecha_comp", type:"jqxDateTimeInput"},
			{id:"compras_modificar_cliente", type:"jqxComboBox"},
			{id:"compras_modificar_cliente_ID", type:"jqxComboBox"},
			//-2
			{id:"compras_modificar_codigo", type:"jqxComboBox"},
			{id:"compras_modificar_producto", type:"jqxComboBox"},
			{id:"compras_modificar_cantidad", type:"jqxNumberInput"},
			{id:"compras_modificar_nuevo_costo", type:"jqxNumberInput"},
			{id:"compras_modificar_addrowbutton", type:"jqxButton"},
			{id:"compras_modificar_deleterowbutton", type:"jqxButton"},
			//-3
			{id:"compras_modificar_subtotal_total", type:"jqxNumberInput"},
			{id:"compras_modificar_tipo_servicio", type:"jqxComboBox"},
			{id:"compras_modificar_tipo_servicio_precio", type:"jqxNumberInput"},
			{id:"compras_modificar_tipo_descuento", type:"jqxComboBox"},
			{id:"compras_modificar_tipo_descuento_precio", type:"jqxNumberInput"},
			{id:"compras_modificar_iva_precio", type:"jqxNumberInput"},
			{id:"compras_modificar_total_total", type:"jqxNumberInput"},
			{id:"compras_modificar_conductor", type:""},
			{id:"compras_modificar_placa", type:""},
			{id:"compras_modificar_formaP", type:"jqxComboBox"},
			{id:"compras_modificar_guardar", type:"jqxButton"},
			{id:"compras_modificar_convertir_pedido", type:"jqxButton"},
			{id:"compras_modificar_anular", type:"jqxButton"},
		];
	}
	ReDefine();
	
	function CargarCliente ()
	{
		var ValoresSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Direccion', type: 'string'},
				{ name: 'ContactoP', type: 'string'},
				{ name: 'Telefono', type: 'string'},
				{ name: 'Email', type: 'string'},
			],
			type: 'GET',
			data: {"Valores":ID_Cliente},
			url: "modulos/datos.php",
			async: true,
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function () {
				var records = ValoresDataAdapter.records;
				$("#compras_modificar_cliente").val(ID_Cliente);
				$("#compras_modificar_cliente_ID").val(ID_Cliente);
				$("#compras_modificar_direccion").val(records[0]["Direccion"]);
				$("#compras_modificar_telefono").val(records[0]["Telefono"]);
				$("#compras_modificar_contacto_p").val(records[0]["ContactoP"]);
				$("#compras_modificar_e-mail").val(records[0]["Email"]);
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
				{ name: 'Entrada', type: 'string'},
				{ name: 'Doc_Transp', type: 'string'},
				{ name: 'Factura', type: 'string' },
				{ name: 'Pedido', type: 'string' },
				{ name: 'Fecha', type: 'string'},
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Observaciones', type: 'string' },
				{ name: 'FormaP', type: 'string' },
				{ name: 'Conductor', type: 'string' },
				{ name: 'Placa', type: 'string'},
				{ name: 'TipoServicio', type: 'string' },
				{ name: 'TipoServicioValor', type: 'decimal' },
				{ name: 'TipoDescuento', type: 'string' },
				{ name: 'TipoDescuentoValor', type: 'decimal' },
				{ name: 'Peso_Bascula', type: 'decimal'},
				{ name: 'Peso_Remision', type: 'decimal'},
				//--
				{ name: 'CodFab', type: 'string'},
				{ name: 'Nombre', type: 'string' },
				{ name: 'UndMed', type: 'string'},
				{ name: 'Peso', type: 'decimal' },
				{ name: 'Cantidad', type: 'decimal'},
				{ name: 'Dcto', type: 'decimal'},
				{ name: 'UltCosto', type: 'decimal' },
				{ name: 'Unitario', type: 'decimal' },
			],
			data:{"Compras_Modificar":$("#compras_modificar_interno").val()},
			url: "modulos/datos_productos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var len = records.length;
				
				if (records[0]["Interno"] == undefined)
				{
					Alerts_Box("La Compra Ingresada, no posee datos...", 4);
					return;
				}
				
				for (var i=0;i<len;i++)
				{
					var datarow = [{
						"CodFab":records[i]["CodFab"],
						"Nombre":records[i]["Nombre"],
						"UndMed":records[i]["UndMed"],
						"Peso":records[i]["Peso"],
						"Cantidad":records[i]["Cantidad"],
						"Dcto":records[i]["Dcto"],
						"UltCosto":records[i]["UltCosto"],
						"Unitario":records[i]["Unitario"],
					}];
					$("#compras_modificar_products_grid").jqxGrid("addrow", null, datarow, "first");
				}
				$("#compras_modificar_entrada").val(records[0]["Entrada"]);
				$("#compras_modificar_doc_transp").val(records[0]["Doc_Transp"]);
				$("#compras_modificar_factura").val(records[0]["Factura"]);
				$("#compras_modificar_pedido").val(records[0]["Pedido"]);
				$("#compras_modificar_fecha_comp").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				ID_Cliente = records[0]["ClienteID"];
				$("#compras_modificar_observaciones").val(records[0]["Observaciones"]);
				$("#compras_modificar_formaP").val(records[0]["FormaP"]);
				$("#compras_modificar_conductor").val(records[0]["Conductor"]);
				$("#compras_modificar_placa").val(records[0]["Placa"]);
				$("#compras_modificar_tipo_servicio").val(records[0]["TipoServicio"]);
				$("#compras_modificar_tipo_servicio_precio").val(records[0]["TipoServicioValor"]);
				
				if (records[0]["TipoDescuento"] == "" || records[0]["TipoDescuento"] == undefined)
					$("#compras_modificar_tipo_descuento").jqxComboBox('clearSelection');
				else
					$("#compras_modificar_tipo_descuento").val(records[0]["TipoDescuento"]);
				
				$("#compras_modificar_tipo_descuento_precio").val(records[0]["TipoDescuentoValor"]);
				$("#compras_modificar_peso_bascula").val(records[0]["Peso_Bascula"]);
				$("#compras_modificar_peso_remision").val(records[0]["Peso_Remision"]);
				CargarCliente();
				Calcular(true);
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	$('#Compras_Modificar_HideButton').click(function() {
		$("#Compras_Modificar_Content_to_Hide").toggle();
	});
	
	//------------------------------------------- KEY JUMPS
	$('#compras_modificar_entrada').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_doc_transp").jqxInput('focus');
		}
	});
	$('#compras_modificar_doc_transp').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_factura").jqxInput('focus');
		}
	});
	$('#compras_modificar_factura').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_pedido").jqxInput('focus');
		}
	});
	$('#compras_modificar_pedido').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_fecha_comp").jqxDateTimeInput('focus');
		}
	});
	$('#compras_modificar_fecha_comp').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_cliente").jqxComboBox('focus');
		}
	});
	$('#compras_modificar_cliente').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_cliente_ID").jqxComboBox('focus');
		}
	});
	$('#compras_modificar_cliente_ID').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_codigo").jqxComboBox('focus');
		}
	});
	$('#compras_modificar_codigo').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_producto").jqxComboBox('focus');
		}
	});
	$('#compras_modificar_producto').keyup(function(event) {
		if(event.which == 13)
		{
			if (CostoCargado == false) {
				return;
			}
			else {
				$("#compras_modificar_cantidad").jqxNumberInput('focus');
				var input = $('#compras_modificar_cantidad input')[0];
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
	$('#compras_modificar_cantidad').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_modificar_nuevo_costo").jqxNumberInput('focus');
				var input = $('#compras_modificar_nuevo_costo input')[0];
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
	});
	$('#compras_modificar_nuevo_costo').keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row();
		}
	});

	$("#compras_modificar_cliente").jqxComboBox(
	{
		width: 410,
		height: 20,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#compras_modificar_cliente").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#compras_modificar_cliente_ID").val(ID_Cliente);
				CargarCliente();
				clearTimeout(Timer1);
			},350);
		}
	});

	$("#compras_modificar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 180,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#compras_modificar_cliente_ID").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#compras_modificar_cliente").val(ID_Cliente);
				CargarCliente();
				clearTimeout(Timer1);
			},350);
		}
	});

	$("#compras_modificar_direccion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 380,
		disabled: true,
	});

	$("#compras_modificar_telefono").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 112,
		mask: '(###)###-####',
		disabled: true,
	});

	$("#compras_modificar_contacto_p").jqxInput({
		theme: mytheme,
		height: 20,
		width: 160,
		disabled: true,
	});
	
	$("#compras_modificar_e-mail").jqxInput({
		theme: mytheme,
		height: 20,
		width: 160,
		disabled: true,
	});
	
	$("#compras_modificar_entrada").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true
	});
	
	$("#compras_modificar_doc_transp").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	
	$("#compras_modificar_interno").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Interno',
		selectedIndex: -1,
		displayMember: 'Interno',
		valueMember: 'Interno'
	});
	$("#compras_modificar_interno").bind('change', function (event) {
		if (event.args)
		{
			if (ID_Interno == event.args.item.value)
				return;
			
			ID_Interno = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				if (ClickOK == true) {
					EnableDisableAll(false);
					ClickOK = false;
				}
				ClearFields();
				CargarValores();
				clearTimeout(Timer1);
			},150);
		}
		else
			ClearDocument();
	});
	$("#compras_modificar_interno").bind('bindingComplete', function (event) {
		if (ID_Interno != "")
			$("#compras_modificar_interno").val(ID_Interno);
	});
	// Actualizar Ordenes
	EntradaSource.data =  {"Compras_Modificar":true};
	var EntradaAdapter = new $.jqx.dataAdapter(EntradaSource);
	$("#compras_modificar_interno").jqxComboBox({source: EntradaAdapter});
	
	$("#compras_modificar_pedido").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
	});
	
	$("#compras_modificar_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	
	//---------------------------------------------------------------- PARTE 2
	//-- GLOBAL
	var CodFabID = 0;
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
		CostoCargado = false;
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'UltCosto', type: 'decimal' },
			],
			type: 'GET',
			data:{"Compras_Precios":CodFabID},
			url: "modulos/datos_productos.php",
			async: true,
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				$("#compras_modificar_ultimo_costo").val(records[0]["UltCosto"]);
				CostoCargado = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: " + error, 3);
			},
		});
	};
	
	$("#compras_modificar_codigo").jqxComboBox(
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
	$("#compras_modificar_codigo").bind('change', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#compras_modificar_producto").val(CodFabID);
				GetPriceList();
				clearTimeout(Timer1);
			},150);
		}
	});
	
	$("#compras_modificar_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 320,
		height: 20,
		source: CB_ProductoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#compras_modificar_producto").bind('change', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#compras_modificar_codigo").val(CodFabID);
				GetPriceList();
				clearTimeout(Timer1);
			},150);
		}
	});
	
	$("#compras_modificar_ultimo_costo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 155,
		inputMode: 'simple',
		disabled: true,
	});
	
	$("#compras_modificar_cantidad").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 160,
		inputMode: 'simple',
		spinButtons: false
	});
	
	
	$("#compras_modificar_nuevo_costo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18
	});
	
	$('#compras_modificar_form_validation').jqxValidator({
		rules:
		[
			{
				input: '#compras_modificar_cantidad', message: 'Debe ser un valor mayor a 0!', action: 'keyup, blur', rule: function (input, commit) {
					if (input.val() < 0) {
						return false;
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
			{ name: 'UltCosto', type: 'decimal' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Dcto', type: 'decimal' },
			{ name: 'Unitario', type: 'decimal' },
			{ name: 'Subtotal', type: 'decimal' },
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

		var ProductName = $("#compras_modificar_producto").jqxComboBox('getSelectedItem');
		var ProductOldPrice = $("#compras_modificar_ultimo_costo").val();
		var ProductNewPrice = $("#compras_modificar_nuevo_costo").val();
		var CantidadNum = $("#compras_modificar_cantidad").val();
		
		if (! ProductName | ProductName <= 0) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("compras_modificar_producto");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("compras_modificar_cantidad");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#compras_modificar_products_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $('#compras_modificar_products_grid').jqxGrid('getrowdata', i);
			if (currentRow.CodFab == CodFabID)
			{
				var totalc = CantidadNum + currentRow.Cantidad;

				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"UndMed":currentRow.UndMed,
					"UltCosto":currentRow.UltCosto,
					"Cantidad":totalc,
					"Peso":currentRow.Peso,
					"Dcto":currentRow.Dcto,
					"Unitario":currentRow.Unitario,
				}];
				var id = $("#compras_modificar_products_grid").jqxGrid('getrowid', i);
				$("#compras_modificar_products_grid").jqxGrid('deleterow', id);
				$("#compras_modificar_products_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#compras_modificar_codigo").jqxComboBox('clearSelection');
				$("#compras_modificar_producto").jqxComboBox('clearSelection');
				$("#compras_modificar_ultimo_costo").val('');
				$("#compras_modificar_cantidad").val('');
				$("#compras_modificar_nuevo_costo").val('');
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
			],
			type: 'GET',
			data:{"Compras_Productos":CodFabID},
			url: "modulos/datos_productos.php",
			async: true,
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":CodFabID,
					"Nombre":ProductName.label,
					"UndMed":records[0]["UndMed"],
					"UltCosto":ProductOldPrice,
					"Cantidad":CantidadNum,
					"Peso":records[0]["Peso"],
					"Dcto":0,
					"Unitario":ProductNewPrice
				}];
				$("#compras_modificar_products_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#compras_modificar_codigo").jqxComboBox('clearSelection');
				$("#compras_modificar_producto").jqxComboBox('clearSelection');
				$("#compras_modificar_ultimo_costo").val('');
				$("#compras_modificar_cantidad").val('');
				$("#compras_modificar_nuevo_costo").val('');
				Calcular();
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: "+error, 3);
			},
		});
	};

	$("#compras_modificar_products_grid").jqxGrid(
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
				'<input type="button" id="compras_modificar_addrowbutton" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="compras_modificar_deleterowbutton" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#compras_modificar_addrowbutton").jqxButton({theme: mytheme, template: "success"})
			$("#compras_modificar_deleterowbutton").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#compras_modificar_addrowbutton").on('click', function () {
				Add_Row();
			});
			// delete row.
			$("#compras_modificar_deleterowbutton").on('click', function () {
				var selectedrowindex = $("#compras_modificar_products_grid").jqxGrid('getselectedrowindex');
				var rowscount = $("#compras_modificar_products_grid").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#compras_modificar_products_grid").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#compras_modificar_products_grid").jqxGrid('deleterow', id);
					Calcular();
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: 90, height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: 240, height: 20 },
			{ text: 'Und', datafield: 'UndMed', editable: false, width: 50, height: 20 },
			{ text: 'U. Costo', datafield: 'UltCosto', editable: false, width: 100, height: 20, cellsalign: 'right' },
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
					var total = parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad);
					var total2 = Math.round( total - (parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad)) / 100 * parseFloat(rowdata.Dcto) );
					
					return "<div style='margin: 4px;' class='jqx-right-align'>" + dataAdapter.formatNumber(total2, "c2") + "</div>";
				}
			}
		]
	});
	
	$("#compras_modificar_products_grid").on('cellvaluechanged', function (event) 
	{
		if (event.args) {
			var datafield = event.args.datafield;
			if (datafield == "Cantidad" || datafield == "Dcto" || datafield == "Unitario") {
				Calcular();
			}
		}
	});
	
	// ------------------------------------------ PARTE 3
	
	$("#compras_modificar_conductor").jqxInput({
		theme: mytheme,
		height: 20,
		width: 258,
	});
	
	$("#compras_modificar_placa").jqxInput({
		theme: mytheme,
		height: 20,
		width: 95,
	});
	
	$("#compras_modificar_formaP").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 130,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		dropDownHeight: 50
	});
	$("#compras_modificar_formaP").bind('change', function (event) {
		if (!event.args)
		{
			$("#compras_modificar_formaP").jqxComboBox('clearSelection');
		}
	});
	
	$("#compras_modificar_formaP").jqxComboBox('addItem', {label: "Efectivo"});
	$("#compras_modificar_formaP").jqxComboBox('addItem', {label: "Credito"});
	
	$("#compras_modificar_peso_bascula").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 138,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 12,
	});
	
	$("#compras_modificar_peso_remision").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 138,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 12,
	});
	
	$("#compras_modificar_pesokg").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 138,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 12,
		disabled: true
	});
	
	var iva = 0;
	var total = 0;
	
	$("#compras_modificar_subtotal_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "               SUBTOTAL",
	});
	
	$("#compras_modificar_subtotal_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999
	});
	
	function Calcular(Ignore) {
		var Calc_Timer = setTimeout(function()
		{
			var information = $('#compras_modificar_products_grid').jqxGrid('getdatainformation');
			var rowscounts = information.rowscount;
			var old_total = 0;
			var total_peso = 0;
			for (i=0; i<rowscounts; i++){
				var currentRow = $('#compras_modificar_products_grid').jqxGrid('getrowdata', i);
				var total1 = parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad);
				var total2 = Math.round( total1 - (parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad)) / 100 * parseFloat(currentRow.Dcto) );
				old_total = old_total + total2;
				total_peso = total_peso + parseFloat(currentRow.Peso) * parseFloat(currentRow.Cantidad);
			};
			
			var valor_tipo = $('#compras_modificar_tipo_servicio_precio').val();
			old_total = old_total + valor_tipo;
			
			if (total == old_total && Ignore == false)
				return;
			else
				total = old_total;
			
			if ($('#compras_modificar_fecha_comp').val() >= '01-11-2017')
				iva = Math.round(total - (total / 1.19));
			else
				iva = Math.round(total - (total / 1.16));
			/*var tipo_dcto = $("#compras_modificar_tipo_descuento_precio").val();
			if ($("#compras_modificar_tipo_descuento").val() != "" ) {
				tipo_dcto = iva;
				$("#compras_modificar_tipo_descuento_precio").val(tipo_dcto);
			}
			else
			{
				$("#compras_modificar_tipo_descuento_precio").val("0");
			}*/
			var tipo_dcto = $("#compras_modificar_tipo_descuento_precio").val();
			var dcto = $("#compras_modificar_tipo_descuento").val();
			if (tipo_dcto <= 0 && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#compras_modificar_tipo_descuento_precio").val(tipo_dcto);
			}
			else if (tipo_dcto != iva && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#compras_modificar_tipo_descuento_precio").val(tipo_dcto);
			}
			else if (tipo_dcto <= 0 && dcto.indexOf("IVA") < 1)
			{
				$("#compras_modificar_tipo_descuento_precio").val("0");
			}
			
			var subtotal = total;
			total = total - tipo_dcto;
			if (total < 0)
				total = 0;
			
			$("#compras_modificar_subtotal_total").val(subtotal);
			$("#compras_modificar_iva_precio").val(iva);
			$("#compras_modificar_total_total").val(total);
			$("#compras_modificar_pesokg").val(total_peso);
			$("#compras_modificar_codigo").jqxComboBox('focus');
			setTimeout(Calc_Timer);
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
	
	$("#compras_modificar_tipo_servicio").jqxComboBox({
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
	$("#compras_modificar_tipo_servicio").bind('change', function (event) {
		if (!event.args)
		{
			$("#compras_modificar_tipo_servicio").jqxComboBox('clearSelection');
		}
	});
	
	$("#compras_modificar_tipo_servicio_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18
	});
	$('#compras_modificar_tipo_servicio_precio').on('change', function (event) 
	{
		Calcular();
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("compras_modificar_tipo_servicio_precio");
		}
	});

	$("#compras_modificar_iva_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                     IVA",
	});
	
	$("#compras_modificar_iva_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18
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
	
	$("#compras_modificar_tipo_descuento").jqxComboBox({
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
	$("#compras_modificar_tipo_descuento").bind('change', function (event) {
		if (!event.args) {
			$("#compras_modificar_tipo_descuento").jqxComboBox('clearSelection');
			$("#compras_modificar_tipo_descuento_precio").val("0");
		}
		else
			Calcular(true);
	});
	
	$("#compras_modificar_tipo_descuento_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$("#compras_modificar_tipo_descuento_precio").on('change', function (event) 
	{
		Calcular(true);
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("compras_modificar_tipo_descuento_precio");
		}
	});
	
	$("#compras_modificar_total_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                  TOTAL",
	});
	
	$("#compras_modificar_total_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999
	});
	
	function ModificarPedido ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var datinfo = $("#compras_modificar_products_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		if ($("#compras_modificar_interno").val() <= 0 | $("#compras_modificar_interno").val() == "") {
			Alerts_Box("Debe Seleccionar un Interno de Compra!", 3);
			WaitClick_Combobox("compras_modificar_interno");
			Locked = false;
			return;
		}
		
		// if ($("#compras_modificar_entrada").val() <= 0 | $("#compras_modificar_entrada").val() == "") {
			// Alerts_Box("Debe Ingresar Un Numero de Entrada!", 3);
			// WaitClick_Input("compras_modificar_entrada");
			// Locked = false;
			// return;
		// }
		
		if ($("#compras_modificar_pedido").val() <= 0 | $("#compras_modificar_pedido").val() == "") {
			Alerts_Box("Debe Ingresar Un Numero de Pedido", 3);
			WaitClick_Input("compras_modificar_pedido");
			Locked = false;
			return;
		}
		
		if ($("#compras_modificar_cliente").val() <= 0 | $("#compras_modificar_cliente").val() == "") {
			Alerts_Box("Debe Ingresar un Nombre de Cliente!", 3);
			WaitClick_Combobox("compras_modificar_cliente");
			Locked = false;
			return;
		}
		
		if ($("#compras_modificar_cliente_ID").val() <= 0 | $("#compras_modificar_cliente_ID").val() == "") {
			Alerts_Box("Debe Ingresar un ID de Cliente!", 3);
			WaitClick_Combobox("compras_modificar_cliente");
			Locked = false;
			return;
		}
		if (count <= 0) {
			Alerts_Box("Debe Ingresar un Producto!", 3);
			WaitClick_Combobox("compras_modificar_producto");
			Locked = false;
			return;
		}
		
		if ($("#compras_modificar_tipo_servicio_precio").val() > 0) {
			if ($("#compras_modificar_tipo_servicio").val() <= 0 | $("#compras_modificar_tipo_servicio").val() == "") {
				Alerts_Box("Debe Ingresar Un Tipo de Servicio", 3);
				WaitClick_Combobox("compras_modificar_tipo_servicio");
				Locked = false;
				return;
			}
		}
		
		if ($("#compras_modificar_tipo_descuento_precio").val() > 0) {
			if ($("#compras_modificar_tipo_descuento").val() <= 0 | $("#compras_modificar_tipo_descuento").val() == "") {
				Alerts_Box("Debe Ingresar Un Tipo de Descuento", 3);
				WaitClick_Combobox("compras_modificar_tipo_descuento");
				Locked = false;
				return;
			}
		}
		
		if ($("#compras_modificar_conductor").val() <= 0 | $("#compras_modificar_conductor").val() == "") {
			Alerts_Box("Debe Ingresar Un Nombre de Conductor", 3);
			WaitClick_Input("compras_modificar_conductor");
			Locked = false;
			return;
		}
		
		if ($("#compras_modificar_placa").val() <= 0 | $("#compras_modificar_placa").val() == "") {
			Alerts_Box("Debe Ingresar Un Numero de Placa", 3);
			WaitClick_Input("compras_modificar_placa");
			Locked = false;
			return;
		}
		
		if ($("#compras_modificar_peso_bascula").val() <= 0 | $("#compras_modificar_peso_bascula").val() == "") {
			Alerts_Box("Debe Ingresar el Peso de Bascula", 3);
			WaitClick_NumberInput("compras_modificar_peso_bascula");
			Locked = false;
			return;
		}
		
		if ($("#compras_modificar_peso_remision").val() <= 0 | $("#compras_modificar_peso_remision").val() == "") {
			Alerts_Box("Debe Ingresar el Peso de Remision", 3);
			WaitClick_NumberInput("compras_modificar_peso_remision");
			Locked = false;
			return;
		}
		
		if ($("#compras_modificar_formaP").val() <= 0 | $("#compras_modificar_formaP").val() == "") {
			Alerts_Box("Debe Ingresar Una Forma de Pago!", 3);
			WaitClick_Combobox("compras_modificar_formaP");
			Locked = false;
			return;
		}
		
		for (i = 0; i < count; i++)
		{
			var array = {};
			var currentRow = $('#compras_modificar_products_grid').jqxGrid('getrowdata', i);
			
			array["CodFab"] = currentRow.CodFab;
			array["Cantidad"] = currentRow.Cantidad;
			array["Dcto"] = currentRow.Dcto;
			array["UltCosto"] = currentRow.UltCosto;
			array["Unitario"] = currentRow.Unitario;
			
			if (i==0) {
				array["Interno"] = $("#compras_modificar_interno").val();
				array["Entrada"] = $("#compras_modificar_entrada").val();
				array["Doc_Transp"] = $("#compras_modificar_doc_transp").val();
				array["Factura"] = $("#compras_modificar_factura").val();
				array["Pedido"] = $("#compras_modificar_pedido").val();
				array["Fecha"] = GetFormattedDate($('#compras_modificar_fecha_comp').jqxDateTimeInput('getDate'));
				array["ClienteID"] = ID_Cliente;
				//---
				array["Observaciones"] = $('#compras_modificar_observaciones').val();
				array["Subtotal"] = $("#compras_modificar_subtotal_total").val();
				array["TipoServicio"] = $("#compras_modificar_tipo_servicio").val();
				array["TipoServicioValor"] = $("#compras_modificar_tipo_servicio_precio").val();
				array["Iva"] = $("#compras_modificar_iva_precio").val();
				array["TipoDcto"] = $("#compras_modificar_tipo_descuento").val();
				array["TipoDctoValor"] = $("#compras_modificar_tipo_descuento_precio").val();
				array["Total"] = $("#compras_modificar_total_total").val();
				array["Conductor"] = $("#compras_modificar_conductor").val();
				array["Placa"] = $("#compras_modificar_placa").val();
				array["FormaPago"] = $("#compras_modificar_formaP").val();
				array["Peso"] = $("#compras_modificar_pesokg").val();
				array["Peso_Bascula"] = $("#compras_modificar_peso_bascula").val();
				array["Peso_Remision"] = $("#compras_modificar_peso_remision").val();
			}
			myarray[i] = array;
		};
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			url: "modulos/guardar.php",
			data: {"Compras_Modificar":myarray},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Alerts_Box("Datos Modificados con Exito!", 2);
				EnableDisableAll(true);
				Locked = false;
				// Actualizar Ordenes
				EntradaSource.data =  {"Compras_Modificar":true};
				var EntradaAdapter = new $.jqx.dataAdapter(EntradaSource);
				$("#compras_modificar_interno").jqxComboBox({source: EntradaAdapter});
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar compras_modificar_guardar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	};
	
	function AnularPedido ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if ($("#compras_modificar_interno").val() <= 0 | $("#compras_modificar_interno").val() == "") {
			Alerts_Box("Debe Seleccionar un Interno de Compra!", 3);
			WaitClick_Combobox("compras_modificar_interno");
			Locked = false;
			return;
		}
		var data = "Compras_Modificar_Anular=true&Interno=" + $("#compras_modificar_interno").val() + "&Motivo=" + $("#compras_modificar_motivo_anular").val();
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			data: data,
			url: "modulos/guardar.php",
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Alerts_Box("Datos Anulados con Exito!", 2);
				Locked = false;
				EnableDisableAll(true);
				//ClearDocument();
				// Actualizar Ordenes
				EntradaSource.data =  {"Compras_Modificar":true};
				var EntradaAdapter = new $.jqx.dataAdapter(EntradaSource);
				$("#compras_modificar_interno").jqxComboBox({source: EntradaAdapter});
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar anular los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	};
	
	function CrearPedido ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if ($("#compras_modificar_interno").val() <= 0 | $("#compras_modificar_interno").val() == "") {
			Alerts_Box("Debe Seleccionar un Interno de Compra", 3);
			WaitClick_Combobox("compras_modificar_interno");
			Locked = false;
			return;
		}
		var data = "Compras_Modificar_Pedido=true&Interno=" + $("#compras_modificar_interno").val();
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			data: data,
			url: "modulos/guardar.php",
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Alerts_Box("Datos Guardados con Exito!", 2);
				EnableDisableAll(true);
				Locked = false;
				// Actualizar Ordenes
				EntradaSource.data =  {"Compras_Modificar":true};
				var EntradaAdapter = new $.jqx.dataAdapter(EntradaSource);
				$("#compras_modificar_interno").jqxComboBox({source: EntradaAdapter});
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar modificar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	};
	
	$('#compras_modificar_guardar').jqxButton({ width: 150, template: "info" });
	$("#compras_modificar_guardar").bind('click', function ()
	{
		ModificarPedido();
	});
	
	$('#compras_modificar_convertir_pedido').jqxButton({width: 150, template: "success"});
	$("#compras_modificar_convertir_pedido").bind('click', function ()
	{
		CrearPedido();
	});
	
	$("#compras_modificar_imprimir").jqxButton({width: 150, template: "warning"});
	$("#compras_modificar_imprimir").bind('click', function ()
	{
		window.open("imprimir/compra.php?Interno="+$("#compras_modificar_interno").val()+"", "", "width=700, height=600, menubar=no, titlebar=no");
	});
	
	$('#compras_modificar_anular').jqxButton({width: 150, template: "inverse"});
	$("#compras_modificar_anular").bind('click', function ()
	{
		AnularPedido();
	});
	
	$("#compras_modificar_entrada").jqxComboBox('focus');
	CheckRefresh();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Compras" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Guardar"] == "false")
					{
			?>
						$("#compras_modificar_guardar").jqxButton({ disabled: true });
						$("#compras_modificar_convertir_pedido").jqxButton({ disabled: true });
						$("#compras_modificar_anular").jqxButton({ disabled: true });
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Compras" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#compras_modificar_imprimir").jqxButton({ disabled: true });
			<?php
					}
				}
			}
		} ?>
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<div id="Compras_Modificar_HideButton">&nbsp;&raquo;&nbsp;</div>
	<div id="Compras_Modificar_Content_to_Hide">
		<table cellpadding="1" cellspacing="2">
			<tr>
				<td>
					Interno
				</td>
				<td>
					<div id="compras_modificar_interno"></div>
				</td>
				<td colspan="8">
					<li class="parte1_li_txt">
						Entrada
					</li>
					<li>
						<input type="text" id="compras_modificar_entrada"/>&nbsp;
					</li>
					<li class="parte1_li_txt">
						Doc. Transp.&nbsp;
					</li>
					<li>
						<input type="text" id="compras_modificar_doc_transp"/>&nbsp;
					</li>
					<li class="parte1_li_txt">
						Factura&nbsp;
					</li>
					<li>
						<input type="text" id="compras_modificar_factura"/>&nbsp;
					</li>
					<li class="parte1_li_txt">
						Pedido&nbsp;
					</li>
					<li>
						<input type="text" id="compras_modificar_pedido"/>&nbsp;
					</li>
				</td>
			</tr>
			<tr>
				<td>
					F. Compra
				</td>
				<td>
					<div id="compras_modificar_fecha_comp"></div>
				</td>
				<td>
					Proveedor
				</td>
				<td colspan="3" style="padding-left:16px;">
					<div id="compras_modificar_cliente"></div>
				</td>
				<td>
					ID Proveedor
				</td>
				<td colspan="3">
					<div id="compras_modificar_cliente_ID"></div>
				</td>
			</tr>
			<tr>
				<td>
					Direcci&oacute;n
				</td>
				<td colspan="3">
					<input type="text" id="compras_modificar_direccion"/>
				</td>
				<td colspan="7">
					<li class="parte1_li_txt">
						Telf.&nbsp;
					</li>
					<li>
						<input id="compras_modificar_telefono"/>&nbsp;
					</li>
					<li>
						<input type="text" id="compras_modificar_e-mail"/>&nbsp;
					</li>
					<li>
						<input type="text" id="compras_modificar_contacto_p"/>
					</li>
				</td>
			</tr>
		</table>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<form id="compras_modificar_form_validation" action="./">
		<table cellpadding="0" cellspacing="0" style="margin:10px 0px 10px 0px; border: 1px solid #A4BED4; text-align: center;">
			<tr style="background: #E0E9F5">
				<td style="border-bottom: 1px solid #A4BED4;">
					Codigo
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Producto
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Ult. Costo
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Cantidad
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Unitario
				</td>
			</tr>
			<tr>
				<td>
					<div id="compras_modificar_codigo" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="compras_modificar_producto" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="compras_modificar_ultimo_costo" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="compras_modificar_cantidad" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="compras_modificar_nuevo_costo" style="margin-left:7px; margin-right:7px;"></div>
				</td>
			</tr>
		</table>
	</form>
	<div id="compras_modificar_products_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="0" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				Observaciones:
			</td>
			<td style="margin-left:15px;">
				Notas:
			</td>
			<td>
				<input type="text" id="compras_modificar_subtotal_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="compras_modificar_subtotal_total">
				</div>
			</td>
		</tr>
		<tr>
			<td rowspan="4">
				<textarea rows="7" cols="35" id="compras_modificar_observaciones" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td rowspan="4">
				<textarea rows="7" cols="25" id="compras_modificar_notas" readonly="true" style="resize:none; margin-left:15px;"></textarea>
			</td>
			<td>
				<div id="compras_modificar_tipo_servicio" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="compras_modificar_tipo_servicio_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="compras_modificar_iva_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="compras_modificar_iva_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="compras_modificar_tipo_descuento" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="compras_modificar_tipo_descuento_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="compras_modificar_total_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="compras_modificar_total_total">
				</div>
			</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="2" style="margin:5px 0px 10px 0px;">
		<tr>
			<td colspan="8">
				&nbsp;
			</td>
			<td style="text-align:center;">
				Motivo de Anulaci&oacute;n
			</td>
		</tr>
		<tr>
			<td>
				Conductor&nbsp;
			</td>
			<td colspan="4">
				<li>
					<input type="text" id="compras_modificar_conductor"/>
				</li>
				<li class="parte1_li_txt">
					Placa:
				</li>
				<li>
					<input type="text" id="compras_modificar_placa"/>
				</li>
			</td>
			<td style="margin-left:15px;">
				Forma de P.
			</td>
			<td>
				<div id="compras_modificar_formaP"></div>
			</td>
			<td style="padding-left:25px;">
				<input type="button" id="compras_modificar_guardar" value="Guardar"/>
			</td>
			<td rowspan="2">
				<textarea rows="3" cols="18" id="compras_modificar_motivo_anular" maxlength="100" style="resize:none;"></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="7">
				<li class="parte1_li_txt">
					P. B&aacute;scula
				</li>
				<li>
					<div id="compras_modificar_peso_bascula"></div>
				</li>
				<li class="parte1_li_txt">
					P. Remision
				</li>
				<li>
					<div id="compras_modificar_peso_remision"></div>
				</li>
				<li class="parte1_li_txt">
					Peso (calculado)
				</li>
				<li>
					<div id="compras_modificar_pesokg">
					</div>
				</li>
			</td>
			<td style="padding-left:25px;">
				<input type="button" id="compras_modificar_imprimir" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<td colspan="7">
				&nbsp;
			</td>
			<td style="padding-left:25px;">
				<input type="button" id="compras_modificar_convertir_pedido" value="Convertir a Pedido"/>
			</td>
			<td>
				<input type="button" id="compras_modificar_anular" value="Anular"/>
			</td>
		</tr>
	</table>
</div>