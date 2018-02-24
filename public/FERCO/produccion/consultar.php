<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var OldState = "";
	var NewState = "";
	var Interno = "";
	var Timer = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Produccion_Content");
	var Body = document.getElementById("Produccion_Consultar_Content");
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
				SystemMap("Consultar", true);
				
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
	
	Day = Day;
	Day = Day + "";
	if (Day.length == 1)
	{
		Day = "0" + Day;
	};
	
	Month = Month - 1;
	Month = Month + "";
	if (Month.length == 1)
	{
		Month = "0" + Month;
	};
	
	var EstadoValues = [
		{"Estado":"Pendiente"},
		{"Estado":"Proceso"},
		{"Estado":"Finalizado"},
	];
	
	var EstadoSource = {
		localdata: EstadoValues,
		datatype: "json",
		datafields:[
			{ name: 'Estado', type: 'string' }
		]
	};
	var EstadoDataAdapter = new $.jqx.dataAdapter(EstadoSource);
	var EstadoDataAdapter2 = new $.jqx.dataAdapter(EstadoSource);
	
	$("#produccion_consultar_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		source: EstadoDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Estado',
		valueMember: 'Estado',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#produccion_consultar_estado").bind('change', function (event) {
		if (event.args)
		{
			OrdenSource.data = {"Produccion_Ordenes_Proc": event.args.item.value};
			OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
			$("#produccion_consultar_ord_produccion").jqxComboBox({source: OrdenDataAdapter});	
		}
		else
		{
			$("#produccion_consultar_ord_produccion").jqxComboBox('clearSelection');
			$("#produccion_consultar_estado").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	$("#produccion_consultar_estado").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				Add_Row();
				clearTimeout(Timer);
			},350);
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
	
	$("#produccion_consultar_ord_produccion").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		dropDownHeight: 100,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Orden_Produccion',
		valueMember: 'Orden_Produccion',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#produccion_consultar_ord_produccion").bind('change', function (event) {
		if (!event.args)
		{
			$("#produccion_consultar_ord_produccion").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	$("#produccion_consultar_ord_produccion").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				Add_Row();
				clearTimeout(Timer);
			},500);
		}
	});
	
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
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter1 = new $.jqx.dataAdapter(ClienteSource,{ autoBind: true });
	
	$("#produccion_consultar_cliente").jqxComboBox(
	{
		width: 353,
		height: 20,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#produccion_consultar_cliente").bind('change', function (event) {
		if (!event.args)
		{
			$("#produccion_consultar_cliente").jqxComboBox('clearSelection');
			$("#produccion_consultar_cliente_ID").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	$("#produccion_consultar_cliente").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#produccion_consultar_cliente_ID").val(event.args.item.value);
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#produccion_consultar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#produccion_consultar_cliente_ID").bind('change', function (event) {
		if (!event.args)
		{
			$("#produccion_consultar_cliente").jqxComboBox('clearSelection');
			$("#produccion_consultar_cliente_ID").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	$("#produccion_consultar_cliente_ID").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#produccion_consultar_cliente").val(event.args.item.value);
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#produccion_consultar_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$("#produccion_consultar_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$('#produccion_consultar_fecha_ini').on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	$("#produccion_consultar_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$('#produccion_consultar_fecha_fin').on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	
	// ------------------------------------------ PARTE 2
	
	function Add_Row()
	{
		ItemsSource.data = {
			"Produccion_Consultar":true,
			"Ord_Produccion":$("#produccion_consultar_ord_produccion").val(),
			"ClienteID":$("#produccion_consultar_cliente_ID").val(),
			"Estado":$("#produccion_consultar_estado").val(),
			"Fecha_Ini":GetFormattedDate($("#produccion_consultar_fecha_ini").jqxDateTimeInput('getDate')),
			"Fecha_Fin":GetFormattedDate($("#produccion_consultar_fecha_fin").jqxDateTimeInput('getDate'))
		};
		var ItemsDataAdapter = new $.jqx.dataAdapter(ItemsSource);
		$("#produccion_consultar_items_grid").jqxGrid({source: ItemsDataAdapter});
	};
	
	var ItemsSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Ord_Produccion', type: 'string' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Proceso', type: 'string' },
			{ name: 'Produciendo', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter1.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'Avance', type: 'decimal' },
			{ name: 'Peso', type: 'decimal' },
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#produccion_consultar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		editable: false,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'ClienteID', width: 0, height: 0 },
			{ text: '', datafield: 'Interno', width: 0, height: 0 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Orden Prod.', datafield: 'Ord_Produccion', editable: false, width: '8%', height: 20 },
			{ text: 'Estado', datafield: 'Estado', editable: false, width: '7%', height: 20 },
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: '10%',
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Proceso', datafield: 'Proceso', editable: false, width: '10%', height: 20 },
			{ text: 'Produciendo', datafield: 'Produciendo', editable: false, width: '20%', height: 20 },
			{ text: 'Cliente', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: '25%', height: 20 },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: '5%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: false,
				width: '10%',
				cellsformat: 'd2',
				cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties)
				{
					return '<span style="margin: 4px; float: ' + columnproperties.cellsalign + ';">' + parseFloat(value).toFixed(2) + ' Kg</span>';
				}
			},
			{ text: 'Avance', datafield: 'Avance', editable: false, width: '5%', cellsalign: 'right', cellsformat: 'p' },
		]
	});
	$("#produccion_consultar_items_grid").jqxGrid('hidecolumn', 'ClienteID');
	$("#produccion_consultar_items_grid").jqxGrid('hidecolumn', 'Interno');
	$("#produccion_consultar_items_grid").jqxGrid('localizestrings', localizationobj);
	
	$("#produccion_consultar_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Ord_Produccion")
		{
			Interno = $("#produccion_consultar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Ord_Produccion");
			if (Interno != "")
				$("#Produccion_Consultar_Produccion_Window").jqxWindow('open');
		}
		
	});
	
	// ------------------------------------------ PARTE 3
	
	$('#produccion_consultar_imprimir').jqxButton({
		width: 150,
		template: "warning"
	});
	// Prepare Save Changes...
	$("#produccion_consultar_imprimir").bind('click', function ()
	{
		//
	});
	
	// ------------------------------------------ WINDOWS
	//--- Produccion
	$("#Produccion_Consultar_Produccion_Window").jqxWindow({
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
	$("#Produccion_Consultar_Produccion_Window").on('open', function (event)
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
				$("#Produccion_Consultar_Produccion_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	Add_Row();
	CheckRefresh();
});
</script>
<div id="Produccion_Consultar_Produccion_Window">
	<div id="Produccion_Consultar_Produccion_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Orden de Produccion</div>
	</div>
	<div id="Produccion_Consultar_Produccion_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Estado
			</td>
			<td>
				<div id="produccion_consultar_estado"></div>
			</td>
			<td>
				Ord. Produccion
			</td>
			<td>
				<div id="produccion_consultar_ord_produccion"></div>
			</td>
			<td>
				Fecha I.
			</td>
			<td>
				<div id="produccion_consultar_fecha_ini"></div>
			</td>
			<td>
				Fecha F.
			</td>
			<td>
				<div id="produccion_consultar_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				Cliente
			</td>
			<td colspan="3">
				<div id="produccion_consultar_cliente"></div>
			</td>
			<td>
				ID Cliente
			</td>
			<td>
				<div id="produccion_consultar_cliente_ID"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="produccion_consultar_items_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="5" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="produccion_consultar_imprimir" value="Imprimir"/>
			</td>
		</tr>
	</table>
</div>