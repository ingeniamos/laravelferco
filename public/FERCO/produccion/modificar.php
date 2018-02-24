<?php
session_start();
?>
<style type="text/css">
#produccion_modificar_cartilla {
	cursor: pointer;
}
</style>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var Ord_Produccion = "";
	var Solicitud = "";
	var CodFabID = "";
	var ID_Incorrecto = false;
	var RowAdded = true;
	var Timer = 0;
	var Locked = false;
	var ComboboxLock = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Produccion_Content");
	var Body = document.getElementById("Produccion_Modificar_Content");
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
				//
				OrdenSource.data = {"Produccion_Ordenes": $("#produccion_modificar_estado").val()};
				OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
				$("#produccion_modificar_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
			
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
				if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Imprimir"] == "true")
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
	
	function ReDefine()
	{
		ClearJSON = [
			//{id:"produccion_modificar_estado", type:"jqxComboBox"},
			{id:"produccion_modificar_ord_produccion", type:"jqxComboBox"},
			{id:"produccion_modificar_fecha", type:"jqxDateTimeInput"},
			{id:"produccion_modificar_solicitud", type:""},
			{id:"produccion_modificar_destino", type:""},
			{id:"produccion_modificar_cliente", type:""},
			{id:"produccion_modificar_cliente_ID", type:""},
			{id:"produccion_modificar_interno", type:""},
			{id:"produccion_modificar_cartilla", type:""},
			{id:"produccion_modificar_digitado_por", type:""},
			{id:"produccion_modificar_aprobado_por", type:""},
			{id:"produccion_modificar_modificado_por", type:""},
			//--left - top
			{id:"produccion_modificar_codigo1", type:"jqxComboBox"},
			{id:"produccion_modificar_producto1", type:"jqxComboBox"},
			{id:"produccion_modificar_cantidad1", type:""},
			{id:"produccion_modificar_origen1", type:"jqxComboBox"},
			{id:"produccion_modificar_destino1", type:"jqxComboBox"},
			{id:"produccion_modificar_productos_grid1", type:"jqxGrid"},
			//-- left - bot
			{id:"produccion_modificar_codigo2", type:"jqxComboBox"},
			{id:"produccion_modificar_producto2", type:"jqxComboBox"},
			{id:"produccion_modificar_cantidad2", type:""},
			{id:"produccion_modificar_origen2", type:"jqxComboBox"},
			{id:"produccion_modificar_destino2", type:"jqxComboBox"},
			{id:"produccion_modificar_productos_grid2", type:"jqxGrid"},
			//-- right
			{id:"produccion_modificar_trefilado_operario", type:"jqxComboBox"},
			{id:"produccion_modificar_enderezado_operario", type:"jqxComboBox"},
			{id:"produccion_modificar_soldado_operario", type:"jqxComboBox"},
			{id:"produccion_modificar_figurado_operario", type:"jqxComboBox"},
		];
		
		EnableDisableJSON = [
			{id:"produccion_modificar_estado", type:"jqxComboBox"},
			{id:"produccion_modificar_ord_produccion", type:"jqxComboBox"},
			{id:"produccion_modificar_fecha", type:"jqxDateTimeInput"},
			//--left - top
			{id:"produccion_modificar_codigo1", type:"jqxComboBox"},
			{id:"produccion_modificar_producto1", type:"jqxComboBox"},
			{id:"produccion_modificar_cantidad1", type:"jqxNumberInput"},
			{id:"produccion_modificar_origen1", type:"jqxComboBox"},
			{id:"produccion_modificar_destino1", type:"jqxComboBox"},
			//-- left - bot
			{id:"produccion_modificar_codigo2", type:"jqxComboBox"},
			{id:"produccion_modificar_producto2", type:"jqxComboBox"},
			{id:"produccion_modificar_cantidad2", type:"jqxNumberInput"},
			{id:"produccion_modificar_origen2", type:"jqxComboBox"},
			{id:"produccion_modificar_destino2", type:"jqxComboBox"},
			//-- right
			{id:"produccion_modificar_trefilado_operario", type:"jqxComboBox"},
			{id:"produccion_modificar_enderezado_operario", type:"jqxComboBox"},
			{id:"produccion_modificar_soldado_operario", type:"jqxComboBox"},
			{id:"produccion_modificar_figurado_operario", type:"jqxComboBox"},
			// end
			{id:"produccion_modificar_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		// Clean All Variables
		ID_Cliente = "";
		Ord_Produccion = "";
		Solicitud = "";
		CodFabID = "";
		RowAdded = true;
		ID_Incorrecto = false;
		Timer = 0;
		Locked = false;
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		if (!Admin)
		{
			$("#produccion_modificar_estado").jqxComboBox({ disabled: true });
		}
		
		if (!Admin && !Guardar)
		{
			$("#produccion_modificar_guardar").jqxButton({ disabled: true });
			$("#produccion_modificar_nuevo").jqxButton({ disabled: true });
			$("#produccion_modificar_anular").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#produccion_modificar_imprimir").jqxButton({ disabled: true });
		}
	}
	
	function LoadValues ()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Destino', type: 'string'},
				{ name: 'Cliente', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Nombre', type: 'string'},
				{ name: 'Solicitud', type: 'string'},
				{ name: 'DestinoOrden', type: 'string'},
				{ name: 'Fecha', type: 'string'},
				{ name: 'Trefilado', type: 'string' },
				{ name: 'Enderezado', type: 'string' },
				{ name: 'Soldado', type: 'string'},
				{ name: 'Figurado', type: 'string' },
				{ name: 'Interno', type: 'string' },
				{ name: 'Cartilla', type: 'string' },
				{ name: 'DigitadoPor', type: 'string' },
				{ name: 'AprobadoPor', type: 'string' },
				{ name: 'ModificadoPor', type: 'string' },
				//--
				{ name: 'Tipo', type: 'string'},
				{ name: 'CodFab', type: 'string'},
				{ name: 'Nombre', type: 'string' },
				{ name: 'Cantidad', type: 'decimal' },
				{ name: 'Peso', type: 'decimal' },
				{ name: 'UndMed', type: 'string'},
				{ name: 'Origen', type: 'string' },
				{ name: 'Destino', type: 'string'},
			],
			data:{"Produccion_Modificar":Ord_Produccion},
			url: "modulos/datos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () 
			{
				var records = GetValuesAdapter.records;
				var len = records.length;
				
				$("#produccion_modificar_productos_grid1").jqxGrid("clear");
				$("#produccion_modificar_productos_grid2").jqxGrid("clear");
				
				for (var i= 0; i < len; i++)
				{
					var datarow = [{
						"CodFab":records[i]["CodFab"],
						"Nombre":records[i]["Nombre"],
						"Peso":parseFloat(records[i]["Peso"]),
						"UndMed":records[i]["UndMed"],
						"Cantidad":parseFloat(records[i]["Cantidad"]),
						"PesoTotal":0,
						"Origen":records[i]["Origen"],
						"Destino":records[i]["Destino"],
					}];
					if (records[i]["Tipo"] == "Requerido")
						$("#produccion_modificar_productos_grid1").jqxGrid("addrow", null, datarow, "first");
					else if (records[i]["Tipo"] == "Obtener")
						$("#produccion_modificar_productos_grid2").jqxGrid("addrow", null, datarow, "first");
				}
				$("#produccion_modificar_fecha").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				Solicitud = records[0]["Solicitud"];
				$("#produccion_modificar_solicitud").val(records[0]["Solicitud"]);
				$("#produccion_modificar_destino").val(records[0]["DestinoOrden"]);
				$("#produccion_modificar_cliente").val(records[0]["Cliente"]);
				$("#produccion_modificar_cliente_ID").val(records[0]["ClienteID"]);
				//
				$("#produccion_modificar_interno").val(records[0]["Interno"]);
				$("#produccion_modificar_cartilla").val(records[0]["Cartilla"]);
				$("#produccion_modificar_digitado_por").val(records[0]["DigitadoPor"]);
				$("#produccion_modificar_aprobado_por").val(records[0]["AprobadoPor"]);
				$("#produccion_modificar_modificado_por").val(records[0]["ModificadoPor"]);
				//
				$("#produccion_modificar_trefilado_operario").val(records[0]["Trefilado"]);
				$("#produccion_modificar_enderezado_operario").val(records[0]["Enderezado"]);
				$("#produccion_modificar_soldado_operario").val(records[0]["Soldado"]);
				$("#produccion_modificar_figurado_operario").val(records[0]["Figurado"]);
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
			downloadComplete: function(edata, textStatus, jqXHR) {
				//alert("Data is Loaded");
			}
		});
	};

	$("#produccion_modificar_estado").jqxComboBox({
		theme: mytheme,
		width: 100,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: 0,
		disabled: Admin ? false:true,
	});
	$("#produccion_modificar_estado").jqxComboBox('addItem', {label: "Pendiente", value: "Pendiente"});
	$("#produccion_modificar_estado").jqxComboBox('addItem', {label: "Aprobado", value: "Aprobado"});
	
	$("#produccion_modificar_estado").bind('change', function (event) {
		if (event.args && event.args.item != undefined)
		{
			OrdenSource.data = {"Produccion_Ordenes": event.args.item.value};
			OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
			$("#produccion_modificar_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
		}
		else
		{
			$("#produccion_modificar_estado").jqxComboBox({selectedIndex: 0});
		}
	});
	
	var OrdenSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Orden_Produccion', type: 'string'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#produccion_modificar_ord_produccion").jqxComboBox({
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Orden_Produccion',
		valueMember: 'Orden_Produccion'
	});
	$("#produccion_modificar_ord_produccion").bind('change', function (event)
	{
		if (event.args)
		{
			if (Ord_Produccion == event.args.item.value)
				return;
			
			Ord_Produccion = event.args.item.value;
			ComboboxLock = true;
			clearTimeout(Timer);
			Timer = setTimeout(function()
			{
				if (ClickOK == true)
				{
					EnableDisableAll(false);
					ClickOK = false;
				}
				LoadValues();
			},500);
		}
	});
	OrdenSource.data = {"Produccion_Ordenes": $("#produccion_modificar_estado").val()};
	OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
	$("#produccion_modificar_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
	
	$("#produccion_modificar_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#produccion_modificar_destino").jqxInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: true
	});
	
	$("#produccion_modificar_cliente").jqxInput(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		disabled: true
	});
	
	$("#produccion_modificar_cliente_ID").jqxInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: true
	});
	
	$("#produccion_modificar_solicitud").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
	});
	$("#produccion_modificar_solicitud").on("change", function (event)
	{
		var value = $("#produccion_modificar_solicitud").val();
		if (value == "")
			return;
		
		if (value == Solicitud)
			return;
		
		var FindSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Same', type: 'bool'},
			],
			type: 'GET',
			data: {
				"Produccion_Check_SameID":value,
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
					$("#produccion_modificar_solicitud").addClass("jqx-validator-error-element");
					ID_Incorrecto = true;
					Alerts_Box("El numero de la Solicitud de Material, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
					WaitClick_Input("produccion_modificar_solicitud");
				}
				else
				{
					$("#produccion_modificar_solicitud").removeClass("jqx-validator-error-element");
					ID_Incorrecto = false;
				}
			}
		});
	});
	
	$("#produccion_modificar_interno").jqxInput({
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: true
	});
	
	$("#produccion_modificar_cartilla").jqxInput({
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: false
	});
	$("#produccion_modificar_cartilla").on("dblclick", function (event)
	{
		if ($("#produccion_modificar_cartilla").val() != "")
			$("#Produccion_Modificar_Cartilla_Window").jqxWindow("open");
	});
	
	$("#produccion_modificar_digitado_por").jqxInput({
		theme: mytheme,
		width: 50,
		height: 20,
		disabled: true
	});
	
	$("#produccion_modificar_aprobado_por").jqxInput({
		theme: mytheme,
		width: 50,
		height: 20,
		disabled: true
	});
	
	$("#produccion_modificar_modificado_por").jqxInput({
		theme: mytheme,
		width: 50,
		height: 20,
		disabled: true
	});
	
	//----------------------------------------------------- PARTE 2
	
	//--------- LEFT
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		type: 'GET',
		data: {"Produccion_Hierro":true},
		url: "modulos/datos_productos.php",
		async: true
	};
	var ProductosDataAdapter = new $.jqx.dataAdapter(ProductosSource);
	
	$("#produccion_modificar_codigo1").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: ProductosDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#produccion_modificar_codigo1").bind('change', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
		}
		else
		{
			CodFabID = -1;
			$("#produccion_modificar_codigo1").jqxComboBox('clearSelection');
			$("#produccion_modificar_producto1").jqxComboBox('clearSelection');
			//Load
		}
	});
	$("#produccion_modificar_codigo1").bind('select', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				if ($("#produccion_modificar_producto1").val() != CodFabID)
					$("#produccion_modificar_producto1").val(CodFabID);
					
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#produccion_modificar_codigo1").bind('close', function () {
		if (CodFabID != "" && CodFabID != -1) {
			$("#produccion_modificar_producto1").val(CodFabID);
			//Load
		}
	});
	
	$("#produccion_modificar_producto1").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		source: ProductosDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#produccion_modificar_producto1").bind('change', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
		}
		else
		{
			CodFabID = -1;
			$("#produccion_modificar_codigo1").jqxComboBox('clearSelection');
			$("#produccion_modificar_producto1").jqxComboBox('clearSelection');
			//Load
		}
	});
	$("#produccion_modificar_producto1").bind('select', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				if ($("#produccion_modificar_codigo1").val() != CodFabID)
					$("#produccion_modificar_codigo1").val(CodFabID);
					
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#produccion_modificar_producto1").bind('close', function () {
		if (CodFabID != "" && CodFabID != -1) {
			$("#produccion_modificar_codigo1").val(CodFabID);
			//Load
		}
	});
	
	$("#produccion_modificar_cantidad1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 70,
		inputMode: 'simple',
		spinButtons: false,
		digits:6,
	});
	
	var OrigenValues = [
		{"Nombre":"Inventario", "Valor":"Inventario"},
		{"Nombre":"Figurado", "Valor":"Figurado"},
		{"Nombre":"Trefilado", "Valor":"Trefilado"},
		{"Nombre":"Corte y Enderezado", "Valor":"Enderezado"},
		{"Nombre":"Electrosoldado", "Valor":"Electrosoldado"},
	];
	
	var OrigenSource = {
		localdata: OrigenValues,
		datatype: "json",
		datafields:[
			{ name: 'Nombre', type: 'string' },
			{ name: 'Valor', type: 'string' }
		]
	};
	var OrigenDataAdapter = new $.jqx.dataAdapter(OrigenSource);
	
	$("#produccion_modificar_origen1").jqxComboBox(
	{
		theme: mytheme,
		width: 140,
		height: 20,
		source: OrigenDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Valor'
	});
	
	var DestinoValues = [
		{"Nombre":"Trefilado", "Valor":"Trefilado"},
		{"Nombre":"Corte y Enderezado", "Valor":"Enderezado"},
		{"Nombre":"Electrosoldado", "Valor":"Electrosoldado"},
		{"Nombre":"Figurado", "Valor":"Figurado"},
	];
	
	var DestinoSource = {
		localdata: DestinoValues,
		datatype: "json",
		datafields:[
			{ name: 'Nombre', type: 'string' },
			{ name: 'Valor', type: 'string' }
		]
	};
	var DestinoDataAdapter = new $.jqx.dataAdapter(DestinoSource);
	
	$("#produccion_modificar_destino1").jqxComboBox(
	{
		theme: mytheme,
		width: 140,
		height: 20,
		source: DestinoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Valor'
	});
	
	var Source1 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'PesoTotal', type: 'decimal' },
			{ name: 'Origen', type: 'string' },
			{ name: 'Destino', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var DataAdapter1 = new $.jqx.dataAdapter(Source1);
	
	function Add_Row1()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;

		var ProductName = $("#produccion_modificar_producto1").jqxComboBox('getSelectedItem');
		var CantidadNum = $("#produccion_modificar_cantidad1").val();
		var Origen = $("#produccion_modificar_origen1").jqxComboBox('getSelectedItem');
		var Destino = $("#produccion_modificar_destino1").jqxComboBox('getSelectedItem');
		
		if (! ProductName | ProductName <= 0) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("produccion_modificar_producto1");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("produccion_modificar_cantidad1");
			RowAdded = true;
			return;
		}
		
		if (! Origen | Origen <= 0) {
			Alerts_Box("Favor Seleccionar un Origen!", 3);
			WaitClick_Combobox("produccion_modificar_origen1");
			RowAdded = true;
			return;
		}
		
		if (! Destino | Destino <= 0) {
			Alerts_Box("Favor Seleccionar un Proceso!", 3);
			WaitClick_Combobox("produccion_modificar_destino1");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#produccion_modificar_productos_grid1").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $('#produccion_modificar_productos_grid1').jqxGrid('getrowdata', i);
			if (currentRow.CodFab == CodFabID)
			{
				var totalc = CantidadNum + currentRow.Cantidad;
				
				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"Peso":currentRow.Peso,
					"UndMed":currentRow.UndMed,
					"Cantidad":totalc,
					"PesoTotal":currentRow.PesoTotal,
					"Origen":Origen.label,
					"Destino":Destino.label,
				}];
				var id = $("#produccion_modificar_productos_grid1").jqxGrid('getrowid', i);
				$("#produccion_modificar_productos_grid1").jqxGrid('deleterow', id);
				$("#produccion_modificar_productos_grid1").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_modificar_codigo1").jqxComboBox('clearSelection');
				$("#produccion_modificar_producto1").jqxComboBox('clearSelection');
				$("#produccion_modificar_cantidad1").val('');
				$("#produccion_modificar_origen1").jqxComboBox('clearSelection');
				$("#produccion_modificar_destino1").jqxComboBox('clearSelection');
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
			data:{"Productos":CodFabID},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":CodFabID,
					"Nombre":ProductName.label,
					"Peso":records[0]["Peso"],
					"UndMed":records[0]["UndMed"],
					"Cantidad":CantidadNum,
					"PesoTotal":0,
					"Origen":Origen.label,
					"Destino":Destino.label,
				}];
				$("#produccion_modificar_productos_grid1").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_modificar_codigo1").jqxComboBox('clearSelection');
				$("#produccion_modificar_producto1").jqxComboBox('clearSelection');
				$("#produccion_modificar_cantidad1").val('');
				$("#produccion_modificar_origen1").jqxComboBox('clearSelection');
				$("#produccion_modificar_destino1").jqxComboBox('clearSelection');
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
	
	$("#produccion_modificar_productos_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 750,
		source: DataAdapter1,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'click',
		showtoolbar: true,
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="produccion_modificar_addrowbutton1" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="produccion_modificar_deleterowbutton1" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#produccion_modificar_addrowbutton1").jqxButton({theme: mytheme, template: "success"});
			$("#produccion_modificar_deleterowbutton1").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#produccion_modificar_addrowbutton1").on('click', function () {
				Add_Row1();
			});
			// delete row.
			$("#produccion_modificar_deleterowbutton1").on('click', function () {
				var selectedrowindex = $("#produccion_modificar_productos_grid1").jqxGrid('getselectedrowindex');
				var rowscount = $("#produccion_modificar_productos_grid1").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#produccion_modificar_productos_grid1").jqxGrid('getrowid', selectedrowindex);
					$("#produccion_modificar_productos_grid1").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: '10%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '20%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Und', datafield: 'UndMed', editable: false, width: '7%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
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
				text: 'Peso Total',
				datafield: 'PesoTotal',
				editable: false,
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = (parseFloat(rowdata.Peso) * parseFloat(rowdata.Cantidad)).toFixed(2);
					return "<div style='margin: 4px;' class='jqx-right-align'>" + total + "</div>";
					//return "<div style='margin: 4px;' class='jqx-right-align'>" + total.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2, useGrouping: true }); + "</div>";
				}
			},
			{
				text: 'Origen',
				datafield: 'Origen',
				width: '14%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: OrigenDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Valor'
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
				
			},
			{
				text: 'Proceso',
				datafield: 'Destino',
				width: '14%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: DestinoDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Valor'
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
		]
	});
	
	
	$("#produccion_modificar_codigo2").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: ProductosDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#produccion_modificar_codigo2").bind('change', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				if ($("#produccion_modificar_producto2").val() != CodFabID)
					$("#produccion_modificar_producto2").val(CodFabID);
					
				clearTimeout(Timer);
			},350);
		}
	});

	$("#produccion_modificar_producto2").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		source: ProductosDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#produccion_modificar_producto2").bind('change', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				if ($("#produccion_modificar_codigo2").val() != CodFabID)
					$("#produccion_modificar_codigo2").val(CodFabID);
					
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#produccion_modificar_cantidad2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 70,
		inputMode: 'simple',
		spinButtons: false,
		digits:6,
	});
	
	$("#produccion_modificar_origen2").jqxComboBox(
	{
		theme: mytheme,
		width: 140,
		height: 20,
		source: DestinoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Valor'
	});
	
	$("#produccion_modificar_destino2").jqxComboBox(
	{
		theme: mytheme,
		width: 140,
		height: 20,
		source: OrigenDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Valor'
	});
	
	var Source2 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'PesoTotal', type: 'decimal' },
			{ name: 'Origen', type: 'string' },
			{ name: 'Destino', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var DataAdapter2 = new $.jqx.dataAdapter(Source2);
	
	function Add_Row2()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;

		var ProductName = $("#produccion_modificar_producto2").jqxComboBox('getSelectedItem');
		var CantidadNum = $("#produccion_modificar_cantidad2").val();
		var Origen = $("#produccion_modificar_origen2").jqxComboBox('getSelectedItem');
		var Destino = $("#produccion_modificar_destino2").jqxComboBox('getSelectedItem');
		
		if (! ProductName | ProductName <= 0) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("produccion_modificar_producto2");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("produccion_modificar_cantidad2");
			RowAdded = true;
			return;
		}
		
		if (! Origen | Origen <= 0) {
			Alerts_Box("Favor Seleccionar un Origen!", 3);
			WaitClick_Combobox("produccion_modificar_origen2");
			RowAdded = true;
			return;
		}
		
		if (! Destino | Destino <= 0) {
			Alerts_Box("Favor Seleccionar un Destino!", 3);
			WaitClick_Combobox("produccion_modificar_destino2");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#produccion_modificar_productos_grid2").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $('#produccion_modificar_productos_grid2').jqxGrid('getrowdata', i);
			if (currentRow.CodFab == CodFabID)
			{
				var totalc = CantidadNum + currentRow.Cantidad;
				
				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"Peso":currentRow.Peso,
					"UndMed":currentRow.UndMed,
					"Cantidad":totalc,
					"PesoTotal":currentRow.PesoTotal,
					"Origen":Origen.label,
					"Destino":Destino.label,
				}];
				var id = $("#produccion_modificar_productos_grid2").jqxGrid('getrowid', i);
				$("#produccion_modificar_productos_grid2").jqxGrid('deleterow', id);
				$("#produccion_modificar_productos_grid2").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_modificar_codigo2").jqxComboBox('clearSelection');
				$("#produccion_modificar_producto2").jqxComboBox('clearSelection');
				$("#produccion_modificar_cantidad2").val('');
				$("#produccion_modificar_origen2").jqxComboBox('clearSelection');
				$("#produccion_modificar_destino2").jqxComboBox('clearSelection');
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
			data:{"Productos":CodFabID},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":CodFabID,
					"Nombre":ProductName.label,
					"Peso":records[0]["Peso"],
					"UndMed":records[0]["UndMed"],
					"Cantidad":CantidadNum,
					"PesoTotal":0,
					"Origen":Origen.label,
					"Destino":Destino.label,
				}];
				$("#produccion_modificar_productos_grid2").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_modificar_codigo2").jqxComboBox('clearSelection');
				$("#produccion_modificar_producto2").jqxComboBox('clearSelection');
				$("#produccion_modificar_cantidad2").val('');
				$("#produccion_modificar_origen2").jqxComboBox('clearSelection');
				$("#produccion_modificar_destino2").jqxComboBox('clearSelection');
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
	
	$("#produccion_modificar_productos_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 750,
		source: DataAdapter2,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'click',
		showtoolbar: true,
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="produccion_modificar_addrowbutton2" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="produccion_modificar_deleterowbutton2" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#produccion_modificar_addrowbutton2").jqxButton({theme: mytheme, template: "success"});
			$("#produccion_modificar_deleterowbutton2").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#produccion_modificar_addrowbutton2").on('click', function () {
				Add_Row2();
			});
			// delete row.
			$("#produccion_modificar_deleterowbutton2").on('click', function () {
				var selectedrowindex = $("#produccion_modificar_productos_grid2").jqxGrid('getselectedrowindex');
				var rowscount = $("#produccion_modificar_productos_grid2").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#produccion_modificar_productos_grid2").jqxGrid('getrowid', selectedrowindex);
					$("#produccion_modificar_productos_grid2").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: '10%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '20%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Und', datafield: 'UndMed', editable: false, width: '7%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
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
				text: 'Peso Total',
				datafield: 'PesoTotal',
				editable: false,
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 12 });
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = (parseFloat(rowdata.Peso) * parseFloat(rowdata.Cantidad)).toFixed(2);
					return "<div style='margin: 4px;' class='jqx-right-align'>" + total + "</div>";
				}
			},
			{
				text: 'Proceso',
				datafield: 'Origen',
				width: '14%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: DestinoDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Valor'
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
				
			},
			{
				text: 'Enviar a',
				datafield: 'Destino',
				width: '14%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: OrigenDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Valor'
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
		]
	});
	
	// -------- RIGHT
	
	var NominaSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'},
		{ name: 'ClienteID', type: 'string'}
		],
		type: 'GET',
		data: {"Clientes_Nomina":true},
		url: "modulos/datos.php",
		async: true
	};
	var NominaDataAdapter = new $.jqx.dataAdapter(NominaSource);
	
	$("#produccion_modificar_trefilado_operario").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: NominaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Operario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID',
	});
	$("#produccion_modificar_trefilado_operario").bind('change', function (event) {
		if (!event.args) {
			$("#produccion_modificar_trefilado_operario").jqxComboBox('clearSelection');
		}
	});
	
	$("#produccion_modificar_enderezado_operario").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: NominaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Operario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID',
	});
	$("#produccion_modificar_enderezado_operario").bind('change', function (event) {
		if (!event.args) {
			$("#produccion_modificar_enderezado_operario").jqxComboBox('clearSelection');
		}
	});
	
	$("#produccion_modificar_soldado_operario").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: NominaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Operario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID',
	});
	$("#produccion_modificar_soldado_operario").bind('change', function (event) {
		if (!event.args) {
			$("#produccion_modificar_soldado_operario").jqxComboBox('clearSelection');
		}
	});
	
	$("#produccion_modificar_figurado_operario").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: NominaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Operario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID',
	});
	$("#produccion_modificar_figurado_operario").bind('change', function (event) {
		if (!event.args) {
			$("#produccion_modificar_figurado_operario").jqxComboBox('clearSelection');
		}
	});
	
	function CrearProduccion ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ID_Incorrecto) {
			Alerts_Box("El numero de la Solicitud de Material, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
			Locked = false;
			return;
		}
		
		if ($("#produccion_modificar_solicitud").val() == "") {
			Alerts_Box("Debe Ingresar una Solicitud de Material.", 3);
			WaitClick_Input("produccion_modificar_solicitud");
			Locked = false;
			return;
		}
		
		if ($("#produccion_modificar_destino").val() < 0 | $("#produccion_modificar_destino").val() == "") {
			Alerts_Box("Debe Seleccionar un Destino.", 3);
			WaitClick_Combobox("produccion_modificar_destino");
			Locked = false;
			return;
		}
		
		if ($("#produccion_modificar_destino").val() == "Cliente") {
			if ($("#produccion_modificar_cliente").val() < 0 | $("#produccion_modificar_cliente").val() == "") {
				Alerts_Box("Debe Seleccionar un Cliente.", 3);
				WaitClick_Combobox("produccion_modificar_cliente");
				Locked = false;
				return;
			}
			
			if ($("#produccion_modificar_cliente_ID").val() < 0 | $("#produccion_modificar_cliente_ID").val() == "") {
				Alerts_Box("Debe Seleccionar un Cliente.", 3);
				WaitClick_Combobox("produccion_modificar_cliente_ID");
				Locked = false;
				return;
			}
		}
		
		var datinfo1 = $("#produccion_modificar_productos_grid1").jqxGrid('getdatainformation');
		var count1 = datinfo1.rowscount;
		var datinfo2 = $("#produccion_modificar_productos_grid2").jqxGrid('getdatainformation');
		var count2 = datinfo2.rowscount;
		var myarray = new Array();
		var tmp_array = {};
		var gridarray1 = new Array();
		var gridarray2 = new Array();
		
		if (count1 <= 0) {
			Alerts_Box("Debe Ingresar al menos un Producto Requerido!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count2 <= 0) {
			Alerts_Box("Debe Ingresar al menos un Producto a Obtener!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (i = 0; i < count1; i++)
		{
			var array = {};
			var currentRow = $('#produccion_modificar_productos_grid1').jqxGrid('getrowdata', i);
			
			switch (currentRow.Destino)
			{
				case "Trefilado":
					if ($("#produccion_modificar_trefilado_operario").val() < 0 | $("#produccion_modificar_trefilado_operario").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Trefilado.", 3);
						WaitClick_Combobox("produccion_modificar_trefilado_operario");
						Locked = false;
						return;
					}
				break;
				case "Corte y Enderezado":
					if ($("#produccion_modificar_enderezado_operario").val() < 0 | $("#produccion_modificar_enderezado_operario").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Corte y Enderezado.", 3);
						WaitClick_Combobox("produccion_modificar_enderezado_operario");
						Locked = false;
						return;
					}
				break;
				case "Electrosoldado":
					if ($("#produccion_modificar_soldado_operario").val() < 0 | $("#produccion_modificar_soldado_operario").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Electrosoldado.", 3);
						WaitClick_Combobox("produccion_modificar_soldado_operario");
						Locked = false;
						return;
					}
				break;
				case "Figurado":
					if ($("#produccion_modificar_figurado_operario").val() < 0 | $("#produccion_modificar_figurado_operario").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Figurado.", 3);
						WaitClick_Combobox("produccion_modificar_figurado_operario");
						Locked = false;
						return;
					}
				break;
			}
			
			array["CodFab1"] = currentRow.CodFab;
			array["Cantidad1"] = currentRow.Cantidad;
			array["Origen1"] = currentRow.Origen;
			array["Destino1"] = currentRow.Destino;
			//---
			gridarray1[i] = array;
		}
		
		for (i = 0; i < count2; i++)
		{
			var array = {};
			var currentRow = $('#produccion_modificar_productos_grid2').jqxGrid('getrowdata', i);
			
			array["CodFab2"] = currentRow.CodFab;
			array["Cantidad2"] = currentRow.Cantidad;
			array["Origen2"] = currentRow.Origen;
			array["Destino2"] = currentRow.Destino;
			//---
			gridarray2[i] = array;
		}
		
		//tmp_array["Interno"] = $("#produccion_modificar_interno").val(); buscamos el dato al momento de guardar junto con la ord_compra
		tmp_array["Ord_Produccion"] = $("#produccion_modificar_ord_produccion").val();
		tmp_array["Solicitud"] = $("#produccion_modificar_solicitud").val();
		tmp_array["Destino"] = $("#produccion_modificar_destino").val();
		tmp_array["ClienteID"] = $("#produccion_modificar_cliente_ID").val();
		tmp_array["Fecha"] = GetFormattedDate($("#produccion_modificar_fecha").jqxDateTimeInput('getDate'));
		tmp_array["Trefilado"] = $("#produccion_modificar_trefilado_operario").val();
		tmp_array["Enderezado"] = $("#produccion_modificar_enderezado_operario").val();
		tmp_array["Electrosoldado"] = $("#produccion_modificar_soldado_operario").val();
		tmp_array["Figurado"] = $("#produccion_modificar_figurado_operario").val();
		myarray[0] = tmp_array;
		
		//---
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: {
				"Produccion_Modificar":true,
				"MainData":myarray,
				"Data1":gridarray1,
				"Data2":gridarray2
			},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						EnableDisableAll(true);
						Alerts_Box("Datos Guardados con Exito!", 2);
						//---
						OrdenSource.data = {"Produccion_Ordenes": $("#produccion_modificar_estado").val()};
						OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
						$("#produccion_modificar_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("No es posible Modificar la Orden ya que posee una Factura \"Aprobada\"", 3);
					break;
					
					default:
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
	
	function FixProduccion()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var datinfo1 = $("#produccion_modificar_productos_grid1").jqxGrid('getdatainformation');
		var count1 = datinfo1.rowscount;
		var datinfo2 = $("#produccion_modificar_productos_grid2").jqxGrid('getdatainformation');
		var count2 = datinfo2.rowscount;
		var myarray = new Array();
		var tmp_array = {};
		var gridarray1 = new Array();
		var gridarray2 = new Array();
		
		if (count1 < 1)
		{
			Alerts_Box("Debe Ingresar al menos un Producto Requerido!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count2 < 1)
		{
			Alerts_Box("Debe Ingresar al menos un Producto a Obtener!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (i = 0; i < count1; i++)
		{
			var array = {};
			var currentRow = $("#produccion_modificar_productos_grid1").jqxGrid('getrowdata', i);
			
			switch (currentRow.Destino)
			{
				case "Trefilado":
					if ($("#produccion_modificar_trefilado_operario").val() < 0 | $("#produccion_modificar_trefilado_operario").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Trefilado.", 3);
						WaitClick_Combobox("produccion_modificar_trefilado_operario");
						Locked = false;
						return;
					}
				break;
				case "Corte y Enderezado":
					if ($("#produccion_modificar_enderezado_operario").val() < 0 | $("#produccion_modificar_enderezado_operario").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Corte y Enderezado.", 3);
						WaitClick_Combobox("produccion_modificar_enderezado_operario");
						Locked = false;
						return;
					}
				break;
				case "Electrosoldado":
					if ($("#produccion_modificar_soldado_operario").val() < 0 | $("#produccion_modificar_soldado_operario").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Electrosoldado.", 3);
						WaitClick_Combobox("produccion_modificar_soldado_operario");
						Locked = false;
						return;
					}
				break;
				case "Figurado":
					if ($("#produccion_modificar_figurado_operario").val() < 0 | $("#produccion_modificar_figurado_operario").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Figurado.", 3);
						WaitClick_Combobox("produccion_modificar_figurado_operario");
						Locked = false;
						return;
					}
				break;
			}
			
			array["CodFab1"] = currentRow.CodFab;
			array["Cantidad1"] = currentRow.Cantidad;
			array["Origen1"] = currentRow.Origen;
			array["Destino1"] = currentRow.Destino;
			//---
			gridarray1[i] = array;
		}
		
		for (i = 0; i < count2; i++)
		{
			var array = {};
			var currentRow = $('#produccion_modificar_productos_grid2').jqxGrid('getrowdata', i);
			
			array["CodFab2"] = currentRow.CodFab;
			array["Cantidad2"] = currentRow.Cantidad;
			array["Origen2"] = currentRow.Origen;
			array["Destino2"] = currentRow.Destino;
			//---
			gridarray2[i] = array;
		}
		
		tmp_array["Ord_Produccion"] = $("#produccion_modificar_ord_produccion").val();
		tmp_array["Solicitud"] = $("#produccion_modificar_solicitud").val();
		tmp_array["Destino"] = $("#produccion_modificar_destino").val();
		tmp_array["ClienteID"] = $("#produccion_modificar_cliente_ID").val();
		tmp_array["Fecha"] = GetFormattedDate($("#produccion_modificar_fecha").jqxDateTimeInput('getDate'));
		tmp_array["Trefilado"] = $("#produccion_modificar_trefilado_operario").val();
		tmp_array["Enderezado"] = $("#produccion_modificar_enderezado_operario").val();
		tmp_array["Electrosoldado"] = $("#produccion_modificar_soldado_operario").val();
		tmp_array["Figurado"] = $("#produccion_modificar_figurado_operario").val();
		myarray[0] = tmp_array;
		
		//---
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: {
				"Produccion_Fix":true,
				"MainData":myarray,
				"Data1":gridarray1,
				"Data2":gridarray2
			},
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						Alerts_Box("Datos Guardados con Exito!", 2);
					break;
					
					case "ERROR":
						Alerts_Box("No es posible Modificar la Orden ya que posee una Factura \"Aprobada\"", 3);
					break;
					
					default:
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
	
	function Anular()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if ($("#produccion_modificar_ord_produccion").val() < 1 || $("#produccion_modificar_ord_produccion").val() == "") {
			Alerts_Box("Debe Ingresar Una Orden de Produccion!", 3);
			WaitClick_Combobox("produccion_modificar_ord_produccion");
			Locked = false;
			return;
		}
		var data = "Produccion_Modificar_Anular=true&Ord_Produccion=" + $("#produccion_modificar_ord_produccion").val() + "&Motivo=" + $("#produccion_modificar_motivo_anular").val();
		//
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: data,
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						EnableDisableAll(true);
						Alerts_Box("Orden Anulada con Exito!", 2);
						//---
						OrdenSource.data = {"Produccion_Ordenes": $("#produccion_modificar_estado").val()};
						OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
						$("#produccion_modificar_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
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
	
	$("#produccion_modificar_guardar").jqxButton({
		width: 180,
		height: 30,
		template: "info",
	});
	$("#produccion_modificar_guardar").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		CrearProduccion();
	});
	
	<?php 
	if ($_SESSION["UserAccess"][0]["Lvl"] == "Administrador")
	{
	?>
		$("#produccion_modificar_fix").jqxButton({
			width: 180,
			height: 30,
			template: "primary",
		});
		$("#produccion_modificar_fix").on("click", function ()
		{
			if (!Admin)
				return;
			
			Alerts_Box("Este proceso solo afecta el inventario y los movimientos de produccion "+
			"sin tomar en cuenta el estado, la venta o procesos de produccion.<br/>"+
			"¿Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					//---
					FixProduccion();
				}
				if (ClickCANCEL == true)
				{
					clearInterval(CheckTimer);
					ClickCANCEL = false;
				}
			}, 10);
		});
	<?php 
	}
	?>
	
	$("#produccion_modificar_nuevo").jqxButton({
		width: 180,
		height: 30,
		template: "success",
	});
	$("#produccion_modificar_nuevo").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		ClearDocument();
	});
	
	$("#produccion_modificar_imprimir").jqxButton({
		width: 180,
		height: 30,
		template: "warning",
	});
	$("#produccion_modificar_imprimir").bind('click', function ()
	{
		if (!Admin && !Imprimir)
			return;
		window.open("imprimir/produccion_orden.php?Ord_Produccion="+$("#produccion_modificar_ord_produccion").val()+"", "", "width=740, height=600, menubar=no, titlebar=no");
	});
	
	$("#produccion_modificar_anular").jqxButton({
		width: 180,
		height: 30,
		template: "inverse",
	});
	$("#produccion_modificar_anular").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		Anular();
	});
	
	// ------------------------------------------ WINDOWS
	//--- Figuracion
	$("#Produccion_Modificar_Cartilla_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 1025,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Produccion_Modificar_Cartilla_Window").on("open", function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Cartilla":$("#produccion_modificar_cartilla").val(),
			},
			url: "figuracion/visualizar.php",
			success: function(data) 
			{
				$("#Produccion_Modificar_Cartilla_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	if (!Admin)
	{
		$("#produccion_modificar_estado").jqxComboBox({ disabled: true });
	}
	
	if (!Admin && !Guardar)
	{
		$("#produccion_modificar_guardar").jqxButton({ disabled: true });
		$("#produccion_modificar_nuevo").jqxButton({ disabled: true });
		$("#produccion_modificar_anular").jqxButton({ disabled: true });
	}
		
	if (!Admin && !Imprimir)
	{
		$("#produccion_modificar_imprimir").jqxButton({ disabled: true });
	}
		
	$("#produccion_modificar_estado").jqxComboBox('selectIndex', 0 );
	
	CheckRefresh();
});
</script>
<div id="Produccion_Modificar_Cartilla_Window">
	<div id="Produccion_Modificar_Cartilla_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 430px;">Visualizar Cartilla</div>
	</div>
	<div id="Produccion_Modificar_Cartilla_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin:5px 0px 20px 0px;">
		<tr>
			<td>
				Estado
			</td>
			<td>
				<div id="produccion_modificar_estado"></div>
			</td>
			<td>
				Ord. Produccion
			</td>
			<td>
				<div id="produccion_modificar_ord_produccion"></div>
			</td>
			<td>
				Destino
			</td>
			<td>
				<input type="text" id="produccion_modificar_destino"/>
			</td>
			<td>
				Cliente
			</td>
			<td colspan="4">
				<input type="text" id="produccion_modificar_cliente"/>
			</td>
			<td>
				ID Cliente
			</td>
			<td colspan="2">
				<input type="text" id="produccion_modificar_cliente_ID"/>
			</td>
		</tr>
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<div id="produccion_modificar_fecha"></div>
			</td>
			<td>
				Solicitud Material
			</td>
			<td>
				<input type="text" id="produccion_modificar_solicitud"/>
			</td>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="produccion_modificar_interno"/>
			</td>
			<td>
				Cartilla
			</td>
			<td>
				<input type="text" id="produccion_modificar_cartilla" readonly/>
			</td>
			<td>
				Digitó
			</td>
			<td>
				<input type="text" id="produccion_modificar_digitado_por"/>
			</td>
			<td>
				Aprobó
			</td>
			<td>
				<input type="text" id="produccion_modificar_aprobado_por"/>
			</td>
			<td>
				Modificó
			</td>
			<td>
				<input type="text" id="produccion_modificar_modificado_por"/>
			</td>
			<!--<td colspan="8">
				<li class="parte1_li_txt">
					Solicitud de Material
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_modificar_solicitud"/>
				</li>
				<li class="parte1_li_txt">
					Interno&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_modificar_interno"/>
				</li>
				<li class="parte1_li_txt">
					Digitado por&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_modificar_digitado_por"/>
				</li>
				<li class="parte1_li_txt">
					Aprobado por&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_modificar_aprobado_por"/>
				</li>
				<li class="parte1_li_txt">
					Modificado por&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_modificar_modificado_por"/>
				</li>
			</td>-->
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2" style="margin-top:15px;">
	<div id="produccion_modificar_left" style="float: left; margin-left:0px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 150px; background-color: #F18B0C; color: white; padding: 5px;">
					INSUMOS REQUERIDOS
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px 0px 10px 0px;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr style="background: #E0E9F5">
							<td style="border-bottom: 1px solid #A4BED4;">
								Codigo
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Producto
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Cantidad
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Origen
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Proceso
							</td>
						</tr>
						<tr>
							<td>
								<div id="produccion_modificar_codigo1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_modificar_producto1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_modificar_cantidad1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_modificar_origen1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_modificar_destino1" style="margin:0px 5px;"></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px 0px 30px 0px;">
					<div id="produccion_modificar_productos_grid1"></div>
				</td>
			</tr>
			<tr>
				<td style="width: 150px; background-color: #6E93CE; color: white; padding: 5px;">
					PRODUCTOS A OBTENER
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px 0px 10px 0px;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr style="background: #E0E9F5">
							<td style="border-bottom: 1px solid #A4BED4;">
								Codigo
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Producto
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Cantidad
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Proceso
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Enviar a
							</td>
						</tr>
						<tr>
							<td>
								<div id="produccion_modificar_codigo2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_modificar_producto2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_modificar_cantidad2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_modificar_origen2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_modificar_destino2" style="margin:0px 5px;"></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px;">
					<div id="produccion_modificar_productos_grid2"></div>
				</td>
			</tr>
		</table>
	</div>
	<div id="produccion_modificar_right" style="float: left; margin-left:10px;">
		<table style="margin: 0px 0px 10px 0px;" cellpadding="0" cellspacing="0">
			<tr style="background-color: #629C62; color: white;">
				<td style="padding:5px;">
					ASIGNAR OPERARIOS
				</td>
			</tr>
			<tr>
				<td>
					Trefilado
				</td>
			</tr>
			<tr>
				<td>
					<div id="produccion_modificar_trefilado_operario"></div>
				</td>
			</tr>
			<tr>
				<td>
					Corte y Enderezado
				</td>
			</tr>
			<tr>
				<td>
					<div id="produccion_modificar_enderezado_operario"></div>
				</td>
			</tr>
			<tr>
				<td>
					Electrosoldado
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div id="produccion_modificar_soldado_operario"></div>
				</td>
			</tr>
			<tr>
				<td>
					Figurado
				</td>
			</tr>
			<tr>
				<td>
					<div id="produccion_modificar_figurado_operario"></div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:30px;">
					<input type="button" id="produccion_modificar_guardar" value="Guardar"/>
				</td>
			</tr>
			<?php 
				if ($_SESSION["UserAccess"][0]["Lvl"] == "Administrador")
				{
			?>
			<tr>
				<td>
					<input type="button" id="produccion_modificar_fix" value="Arreglar Inventario"/>
				</td>
			</tr>
			<?php 
				}
			?>
			<tr>
				<td>
					<input type="button" id="produccion_modificar_nuevo" value="Nuevo"/>
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" id="produccion_modificar_imprimir" value="Imprimir"/>
				</td>
			</tr>
			<tr>
				<td>
					<textarea rows="3" cols="22" id="produccion_modificar_motivo_anular" maxlength="200" style="resize:none;"></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" id="produccion_modificar_anular" value="Anular"/>
				</td>
			</tr>
		</table>
	</div>
</div>