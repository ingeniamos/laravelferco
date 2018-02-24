<?php
session_start();
?>
<style type="text/css">
.red {
	color: black;
	background-color: rgba(251,229,214,1);
}
.red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(251,229,214,1);
}
</style>
<script type="text/javascript">
$(document).ready(function ()// Modulo no-finalizado
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Locked = false;
	var Interno = "";
	var CurrentRow = -1;
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Reportes_Content");
	var Body = document.getElementById("Reportes_Kardex_Content");
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
				SystemMap("Kardex", true);
				
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
	var Guardar = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Kardex" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Kardex" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#reportes_kardex_export").jqxButton({ disabled: true });
					$("#reportes_kardex_imprimir1").jqxButton({ disabled: true });
					$("#reportes_kardex_imprimir2").jqxButton({ disabled: true });
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
	
	Day = Day + "";
	if (Day.length == 1)
	{
		Day = "0" + Day;
	};
	
	Month = Month + "";
	if (Month.length == 1)
	{
		Month = "0" + Month;
	};
	
	Year = Year - 1;
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
		async: true
	};
	var ProductosDataAdapter1 = new $.jqx.dataAdapter(ProductosSource);
	var ProductosDataAdapter2 = ProductosDataAdapter1;
	
	$("#reportes_kardex_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		source: ProductosDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#reportes_kardex_producto").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_kardex_codigo").val() != event.args.item.value)
				$("#reportes_kardex_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#reportes_kardex_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ProductosDataAdapter2,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#reportes_kardex_codigo").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_kardex_producto").val() != event.args.item.value)
				$("#reportes_kardex_producto").jqxComboBox('selectItem', event.args.item.value);
			
			LoadValues();
		}
	});
	
	$("#reportes_kardex_categoria").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		disabled: true,
	});
	
	$("#reportes_kardex_grupo").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		disabled: true,
	});
	
	$("#reportes_kardex_subgrupo").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		disabled: true,
	});
	
	$("#reportes_kardex_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_kardex_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#reportes_kardex_fecha_ini").bind('change', function (event) {
		LoadValues();
	});
	
	$("#reportes_kardex_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_kardex_fecha_fin").bind('change', function (event) {
		LoadValues();
	});
	
	//-------------------------------------------------------------------------
	//------------------------------ PARTE 2 ----------------------------------
	//-------------------------------------------------------------------------
	
	var ClienteData = new Array();
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		id: "ClienteID",
		url: "modulos/datos.php",
		async: false
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ClienteData.push(records[i]);
			}
		}
	});
	
	$("#reportes_kardex_cliente").jqxComboBox(
	{
		width: 250,
		height: 20,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Tercero',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#reportes_kardex_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_kardex_cliente_ID").val() != event.args.item.value)
				$("#reportes_kardex_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#reportes_kardex_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#reportes_kardex_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_kardex_cliente").val() != event.args.item.value)
				$("#reportes_kardex_cliente").jqxComboBox('selectItem', event.args.item.value);
			
			LoadValues();
		}
	});
	
	/*
	var TipoValues = [
		{"Tipo":"Entrada"},
		{"Tipo":"Salida"},
	];
	
	$("#reportes_kardex_tipo").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: TipoValues,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#reportes_kardex_tipo").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_kardex_tipo").jqxComboBox('clearSelection');
		}
	});
	*/
	var MotivoValues = [
		//{"Motivo":"Inicial"},
		{"Motivo":"Factura"},
		{"Motivo":"Compra"},
		{"Motivo":"Produccion"},
		{"Motivo":"Despunte"},
		{"Motivo":"Ajuste"},
	];
	
	$("#reportes_kardex_motivo").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: MotivoValues,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Motivo',
		valueMember: 'Motivo'
	});
	$("#reportes_kardex_motivo").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_kardex_motivo").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	/*
	$("#reportes_kardex_interno").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
	});
	*/
	//-------------------------------------------------------------------------
	//------------------------------ PARTE 3 ----------------------------------
	//-------------------------------------------------------------------------
	
	$("#reportes_kardex_fix").jqxButton({
		width: 110,
		template: "danger"
	});
	$("#reportes_kardex_fix").bind('click', function ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			data: {"Fix_Existencias":true},
			url: "modulos/guardar.php",
			success: function (data, status, xhr)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						Alerts_Box("No se han detectado incoherencias.", 2);
					break;
					
					case "FIXED":
						Alerts_Box("Se han detectado incoherencias entre las existencias de 1 o mas productos y se han solucionado correctamente.", 2);
						LoadValues();
					break;
					
					default:
						Alerts_Box("Ocurrio un error mientras se validaban las existencias.<br />Intente luego de unos segundos", 3);
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
	});
	$("#reportes_kardex_export").jqxButton({
		width: 100,
		template: "success"
	});
	$("#reportes_kardex_export").bind('click', function ()
	{
		var data = "modulos/export_xls.php?Reportes_Kardex=true&Codigo="+$("#reportes_kardex_codigo").val();
		data += "&Motivo="+$("#reportes_kardex_motivo").val()+"&ClienteID="+$("#reportes_kardex_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_kardex_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_kardex_fecha_fin").jqxDateTimeInput('getDate'));
		window.location = data;
	});
	
	$("#reportes_kardex_imprimir1").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_kardex_imprimir1").bind('click', function ()
	{
		var data = "imprimir/reportes_kardex.php?Codigo="+$("#reportes_kardex_codigo").val();
		data += "&Motivo="+$("#reportes_kardex_motivo").val()+"&ClienteID="+$("#reportes_kardex_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_kardex_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_kardex_fecha_fin").jqxDateTimeInput('getDate'));
		
		window.open(data, "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_kardex_imprimir2").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_kardex_imprimir2").bind('click', function ()
	{
		var data = "imprimir/reportes_kardex.php?Codigo="+$("#reportes_kardex_codigo").val();
		data += "&Motivo="+$("#reportes_kardex_motivo").val()+"&ClienteID="+$("#reportes_kardex_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_kardex_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_kardex_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&OrderBy=Tercero";
		
		window.open(data, "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	//-------------------------------------------------------------------------
	//------------------------------ PARTE 4 ----------------------------------
	//-------------------------------------------------------------------------
	
	$("#reportes_kardex_valor").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#reportes_kardex_u_costo").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#reportes_kardex_costo_p").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#reportes_kardex_utilidad").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#reportes_kardex_porcentaje").jqxNumberInput({
		theme: mytheme,
		width: 60,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '%',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 3,
		max: 999,
		disabled: true,
	});
	
	$("#reportes_kardex_existencias").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#reportes_kardex_inicial").jqxNumberInput({
		theme: mytheme,
		width: 140,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	//-------------------------------------------------------------------------
	//------------------------------ PARTE 5 ----------------------------------
	//-------------------------------------------------------------------------
	
	function LoadValues()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			$.ajax({
				dataType: 'json',
				data: {
					"Reportes_Kardex":true,
					"Codigo":$("#reportes_kardex_codigo").val(),
					"Motivo":$("#reportes_kardex_motivo").val(),
					"Fecha_Ini":GetFormattedDate($("#reportes_kardex_fecha_ini").jqxDateTimeInput('getDate')),
					"Fecha_Fin":GetFormattedDate($("#reportes_kardex_fecha_fin").jqxDateTimeInput('getDate')),
				},
				url: "modulos/datos_productos.php",
				success: function (data, status, xhr)
				{
					if (data != undefined)
					{
						$("#reportes_kardex_valor").val(data[0]["Costo"]);
						$("#reportes_kardex_u_costo").val(data[0]["Viejo_Costo"]);
						$("#reportes_kardex_utilidad").val(data[0]["Utilidad"]);
						$("#reportes_kardex_porcentaje").val(data[0]["Porcentaje"]);
						//$("#reportes_kardex_existencias").val(data[0]["Existencia"]);
						//$("#reportes_kardex_inicial").val(data[0]["Inicial"]);
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert(textStatus+ " - " +errorThrown);
				}
			});
			
			$.ajax({
				dataType: 'json',
				data: {
					"Reportes_Kardex_Existencias":true,
					"Codigo":$("#reportes_kardex_codigo").val(),
				},
				url: "modulos/datos_productos.php",
				success: function (data, status, xhr)
				{
					if (data != undefined)
					{
						$("#reportes_kardex_existencias").val(data[0]["Existencia"]);
						$("#reportes_kardex_inicial").val(data[0]["Inicial"]);
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					alert(textStatus+ " - " +errorThrown);
				}
			});
			
			GridSource.data = {
				"Reportes_Kardex_Detalle":true,
				"Codigo":$("#reportes_kardex_codigo").val(),
				"Motivo":$("#reportes_kardex_motivo").val(),
				"ClienteID":$("#reportes_kardex_cliente_ID").val(),
				"Fecha_Ini":GetFormattedDate($("#reportes_kardex_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#reportes_kardex_fecha_fin").jqxDateTimeInput('getDate')),
			};
			
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#reportes_kardex_items_grid").jqxGrid({source: GridDataAdapter});

		},500);
	}
	
	var cellclass = function (row, columnfield, value)
	{
		var Observaciones = $("#reportes_kardex_items_grid").jqxGrid("getcellvalue", row, "Observaciones");
		if (Observaciones != "")
			return 'red';
		else
			return '';
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'Tipo', type: 'string' },
			{ name: 'Observaciones', type: 'string' },
			{ name: 'Motivo', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Existencia', type: 'decimal' },
			{ name: 'Saldo', type: 'decimal' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'Remision', type: 'string' },
		],
		url: "modulos/datos_productos.php",
		updaterow: function (rowid, rowdata, commit)
		{
			$.ajax({
				dataType: "json",
				data: {
					"Kardex_Notas":true,
					"Observaciones":rowdata.Observaciones,
					"Codigo":rowdata.Codigo,
					"Interno":rowdata.Interno,
				},
				url: "modulos/guardar.php",
				success: function (data, status, xhr)
				{
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							commit(true);
						break;
						
						case "ERROR":
							commit(false);
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
						break;
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					commit(false);
					Alerts_Box("Ocurrió un Error al Intentar Actualizar los Datos.<br />Contacte al Soporte Tecnico.<br />"
					+"Error: "+errorThrown, 3);
				}
			});
		}
	};
	
	$("#reportes_kardex_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '50', '100'],
		pagesize: 20,
		autoheight: true,
		editable: false,
		editmode: 'click',
		columns:
		[
			{
				text: 'Fecha',
				datafield: 'Fecha',
				editable: false,
				width: '9%',
				height: 20,
				pinned: true,
				columntype: 'datetimeinput',
				cellsformat: 'dd-MMM-yyyy',
			},
			{ text: 'Tercero', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: '20%', height: 20, pinned: true, },
			{
				text: 'Tipo',
				datafield: 'Tipo',
				editable: false,
				width: '6%',
				height: 20,
				pinned: true,
				cellsalign: 'left',
				cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties, rowdata)
				{
					if (value == "Entrada")
						return '<span style="padding: 6px 7px 6px 8px; margin-left: 1px; float: ' + columnproperties.cellsalign + '; background-color: #CFDDE9;">' + value + '</span>';
					else
						return '<span style="padding: 6px 7px 6px 8px; margin-left: 4px; float: ' + columnproperties.cellsalign + '; ">' + value + '</span>';
				}
			},
			{ text: 'Nota', datafield: 'Observaciones', editable: false, width: '5%', height: 20, cellclassname: cellclass },
			{ text: 'Motivo', datafield: 'Motivo', editable: false, width: '8%', height: 20 },
			{ text: 'Cantidad', datafield: 'Cantidad', editable: false, width: '10%', height: 20 },
			{ text: 'Existencia', datafield: 'Existencia', editable: false, width: '15%', height: 20 },
			{ text: 'Nuevo Saldo', datafield: 'Saldo', editable: false, width: '15%', height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Interno', datafield: 'Interno', editable: false, width: '12%', height: 20 },
			{ text: 'Factura', datafield: 'Factura', editable: false, width: '12%', height: 20 },
			{ text: 'Remision', datafield: 'Remision', editable: false, width: '12%', height: 20 },
		],
	});
	/*$("#reportes_kardex_items_grid").on("bindingcomplete", function (event)
	{//Bucle Infinito...
		var currentRow = $("#reportes_kardex_items_grid").jqxGrid("getrowdata", 0);
		if (currentRow == undefined)
			$("#reportes_kardex_items_grid").jqxGrid("clear");
	});*/
	$("#reportes_kardex_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Interno")
		{
			Interno = value;
			if (Interno.indexOf("FER") >= 0)
				$("#Reportes_Kardex_Remision_Window").jqxWindow('open');
			else if (Interno.indexOf("COMP") >= 0)
				$("#Reportes_Kardex_Compra_Window").jqxWindow('open');
			else if (Interno.indexOf("OP") >= 0)
				$("#Reportes_Kardex_Produccion_Window").jqxWindow('open');
			else
				alert("???");
		}
		else if (datafield == "Observaciones")
		{
			if (Admin == false && Guardar == false)
				return;
			
			CurrentRow = rowBoundIndex;
			var offset = $("#reportes_kardex_items_grid").offset();
			var bottom = offset.top + ($("#reportes_kardex_items_grid").height() / 2 ) - 80;// 80 = tamaño aprox del popup/2
			$("#Reportes_Kardex_Notas").jqxWindow({
				position: {
					x: parseInt(offset.left) + 400,
					y: parseInt(bottom) 
				}
			});
			var dataRecord = $("#reportes_kardex_items_grid").jqxGrid("getrowdata", CurrentRow);
			$("#reportes_kardex_notas").val(dataRecord.Observaciones);
			$("#Reportes_Kardex_Notas").jqxWindow("open");
		}
	});
	
	// ------------------------------------------ WINDOWS
	//--- Notas del Grid
	$("#Reportes_Kardex_Notas").jqxWindow({
		theme: mytheme,
		width: 250,
		resizable: false,
		isModal: true,
		autoOpen: false,
		cancelButton: $("#reportes_kardex_notas_cancelar"),
		modalOpacity: 0.01           
	});
	
	$("#reportes_kardex_notas_cancelar").jqxButton({ theme: mytheme });
	$("#reportes_kardex_notas_guardar").jqxButton({ theme: mytheme });
	$("#reportes_kardex_notas_guardar").click(function()
	{
		if (CurrentRow >= 0)
		{
			NewState = "Passed";
			var dataRecord = $("#reportes_kardex_items_grid").jqxGrid('getrowdata', CurrentRow);
			
			var row = {
				"Codigo":$("#reportes_kardex_codigo").val(),
				"Fecha":dataRecord.Fecha,
				"Nombre":dataRecord.Nombre,
				"Cliente":dataRecord.Cliente,
				"Tipo":dataRecord.Tipo,
				"Observaciones":$("#reportes_kardex_notas").val(),
				"Motivo":dataRecord.Motivo,
				"Cantidad":dataRecord.Cantidad,
				"Existencia":dataRecord.Existencia,
				"Saldo":dataRecord.Saldo,
				"Interno":dataRecord.Interno,
				"Factura":dataRecord.Factura,
				"Remision":dataRecord.Remision,
			};
			
			var rowID = $("#reportes_kardex_items_grid").jqxGrid("getrowid", CurrentRow);
			$("#reportes_kardex_items_grid").jqxGrid("updaterow", rowID, row);
			$("#Reportes_Kardex_Notas").jqxWindow("hide");
		}
	});
	
	//--- Remision
	$("#Reportes_Kardex_Remision_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 1030,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Reportes_Kardex_Remision_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Interno":Interno,
			},
			url: "ventas/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Reportes_Kardex_Remision_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	//--- Compra Interno
	$("#Reportes_Kardex_Compra_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 1030,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Reportes_Kardex_Compra_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Interno":Interno,
			},
			url: "compras/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Reportes_Kardex_Compra_Content").html(data);
			},
			complete: function()
			{
				$("#Loading").hide();
			}
		});
	});
	
	//--- Produccion
	$("#Reportes_Kardex_Produccion_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 960,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Reportes_Kardex_Produccion_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Ord_Produccion":Interno,
			},
			url: "produccion/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Reportes_Kardex_Produccion_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	CheckRefresh();
});
</script>
<div id="Reportes_Kardex_Notas">
	<div>Notas:</div>
	<div style="overflow: hidden;">
		<table>
			<tr>
				<td>
					<textarea rows="5" cols="30" id="reportes_kardex_notas" maxlength="200" style="resize:none;"></textarea>
				</td>
			</tr>
			<tr>
				<td style="padding-top: 10px;">
					<input style="margin-right: 5px;" type="button" id="reportes_kardex_notas_guardar" value="Guardar" />
					<input id="reportes_kardex_notas_cancelar" type="button" value="Cancelar" />
				</td>
			</tr>
		</table>
	</div>
</div>
<div id="Reportes_Kardex_Remision_Window">
	<div id="Reportes_Kardex_Remision_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Reportes_Kardex_Remision_Content" class="WindowContainer">
	</div>
</div>
<div id="Reportes_Kardex_Compra_Window">
	<div id="Reportes_Kardex_Compra_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 420px;">Ver Compra</div>
	</div>
	<div id="Reportes_Kardex_Compra_Content" class="WindowContainer">
	</div>
</div>
<div id="Reportes_Kardex_Produccion_Window">
	<div id="Reportes_Kardex_Produccion_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Orden de Produccion</div>
	</div>
	<div id="Reportes_Kardex_Produccion_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Producto
			</td>
			<td>
				Codigo
			</td>
			<td>
				Categoria
			</td>
			<td>
				Grupo
			</td>
			<td>
				SubGrupo
			</td>
			<td>
				Fecha Ini.
			</td>
			<td>
				Fecha Fin.
			</td>
		</tr>
		<tr>
			<td>
				<div id="reportes_kardex_producto"></div>
			</td>
			<td>
				<div id="reportes_kardex_codigo"></div>
			</td>
			<td>
				<input type="text" id="reportes_kardex_categoria"/>
			</td>
			<td>
				<input type="text" id="reportes_kardex_grupo"/>
			</td>
			<td>
				<input type="text" id="reportes_kardex_subgrupo"/>
			</td>
			<td>
				<div id="reportes_kardex_fecha_ini"></div>
			</td>
			<td>
				<div id="reportes_kardex_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				Tercero
			</td>
			<td>
				Tercero ID
			</td>
			<td>
				Motivo
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<div id="reportes_kardex_cliente"></div>
			</td>
			<td>
				<div id="reportes_kardex_cliente_ID"></div>
			</td>
			<td>
				<div id="reportes_kardex_motivo"></div>
			</td>
			<!--<td>
				<div id="reportes_kardex_tipo"></div>
			</td>
			<td>
				<input type="text" id="reportes_kardex_interno"/>
			</td>-->
			<td>
				<input type="button" id="reportes_kardex_imprimir1" value="Listado"/>
			</td>
			<td>
				<input type="button" id="reportes_kardex_imprimir2" value="Por Terceros"/>
			</td>
			<td>
				<input type="button" id="reportes_kardex_export" value="Exportar"/>
			</td>
			<td>
				<input type="button" id="reportes_kardex_fix" value="Corregir Exist." title="Puede tardar algunos minutos..."/>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<!--<tr>
			<td colspan="7">
				OPCIONES DE IMPRESIÓN
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="reportes_kardex_export" value="Exportar"/>
			</td>
			<td>
				<input type="button" id="reportes_kardex_imprimir1" value="Listado"/>
			</td>
			<td>
				<input type="button" id="reportes_kardex_imprimir2" value="Por Terceros"/>
			</td>
			<td colspan="4">
				&nbsp;
			</td>
		</tr>-->
		<tr>
			<td>
				Total Facturado
			</td>
			<td>
				Total Costo
			</td>
			<td>
				Costo Promedio
			</td>
			<td>
				Utilidad
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Existencias
			</td>
			<td>
				Inv. Inicial
			</td>
		</tr>
		<tr>
			<td>
				<div id="reportes_kardex_valor"></div>
			</td>
			<td>
				<div id="reportes_kardex_u_costo"></div>
			</td>
			<td>
				<div id="reportes_kardex_costo_p"></div>
			</td>
			<td>
				<div id="reportes_kardex_utilidad"></div>
			</td>
			<td>
				<div id="reportes_kardex_porcentaje"></div>
			</td>
			<td>
				<div id="reportes_kardex_existencias"></div>
			</td>
			<td>
				<div id="reportes_kardex_inicial"></div>
			</td>
		</tr>
	</table>
	
	<div id="reportes_kardex_items_grid"></div>
</div>
