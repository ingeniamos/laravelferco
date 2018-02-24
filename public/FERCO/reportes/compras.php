<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var Interno = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Reportes_Content");
	var Body = document.getElementById("Reportes_Compras_Content");
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
				SystemMap("Compras", true);
				
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
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Compras" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#reportes_compras_export").jqxButton({ disabled: true });
					$("#reportes_compras_imprimir1").jqxButton({ disabled: true });
					$("#reportes_compras_imprimir2").jqxButton({ disabled: true });
					$("#reportes_compras_imprimir3").jqxButton({ disabled: true });
					$("#reportes_compras_imprimir4").jqxButton({ disabled: true });
					$("#reportes_compras_imprimir5").jqxButton({ disabled: true });
		<?php
				}
			}
		}
	} ?>
	
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
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
	
	$("#reportes_compras_cliente").jqxComboBox(
	{
		width: 250,
		height: 20,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#reportes_compras_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_compras_cliente_ID").val() != event.args.item.value)
				$("#reportes_compras_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		LoadValues();
	});
	
	$("#reportes_compras_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#reportes_compras_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_compras_cliente").val() != event.args.item.value)
				$("#reportes_compras_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		
		LoadValues();
	});
	
	var EstadoValues = [
		{"Estado":"Aprobado"},
		{"Estado":"Autorizado"},
		{"Estado":"Creado"},
	];
	
	$("#reportes_compras_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		source: EstadoValues,
		promptText: 'Seleccionar',
		displayMember: 'Estado',
		valueMember: 'Estado',
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	$("#reportes_compras_estado").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_compras_estado").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#reportes_compras_vehiculo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	$("#reportes_compras_vehiculo").on('change', function () {
		LoadValues();
	});
	
	$("#reportes_compras_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_compras_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#reportes_compras_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			LoadValues();
	});
	
	$("#reportes_compras_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_compras_fecha_fin").on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#reportes_compras_interno").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	$("#reportes_compras_interno").on('change', function () {
		LoadValues();
	});
	
	$("#reportes_compras_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	$("#reportes_compras_factura").on('change', function () {
		LoadValues();
	});
	
	$("#reportes_compras_entrada").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	$("#reportes_compras_entrada").on('change', function () {
		LoadValues();
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	
	$("#reportes_compras_export").jqxButton({
		width: 120,
		template: "success"
	});
	$("#reportes_compras_export").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Reportes_Compras=true&ClienteID="+$("#reportes_compras_cliente_ID").val();
		data += "&Vehiculo="+$("#reportes_compras_vehiculo").val()+"&Estado="+$("#reportes_compras_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&Interno="+$("#reportes_compras_interno").val()+"&Factura="+$("#reportes_compras_factura").val()+"";
		data += "&Entrada="+$("#reportes_compras_entrada").val();
		//
		window.location = data;
	});
	
	$("#reportes_compras_imprimir1").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_compras_imprimir1").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_compras.php?ClienteID="+$("#reportes_compras_cliente_ID").val();
		data += "&Vehiculo="+$("#reportes_compras_vehiculo").val()+"&Estado="+$("#reportes_compras_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&Interno="+$("#reportes_compras_interno").val()+"&Factura="+$("#reportes_compras_factura").val()+"";
		data += "&Entrada="+$("#reportes_compras_entrada").val();
		//
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_compras_imprimir2").jqxButton({
		width: 120,
		template: "primary"
	});
	$("#reportes_compras_imprimir2").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_compras_simple.php?ClienteID="+$("#reportes_compras_cliente_ID").val();
		data += "&Vehiculo="+$("#reportes_compras_vehiculo").val()+"&Estado="+$("#reportes_compras_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&Interno="+$("#reportes_compras_interno").val()+"&Factura="+$("#reportes_compras_factura").val()+"";
		data += "&Entrada="+$("#reportes_compras_entrada").val()+"&OrderBy=Cliente";
		//
		window.open(data, "", "width=625, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_compras_imprimir3").jqxButton({
		width: 120,
		template: "primary"
	});
	$("#reportes_compras_imprimir3").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_compras.php?ClienteID="+$("#reportes_compras_cliente_ID").val();
		data += "&Vehiculo="+$("#reportes_compras_vehiculo").val()+"&Estado="+$("#reportes_compras_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&Interno="+$("#reportes_compras_interno").val()+"&Factura="+$("#reportes_compras_factura").val()+"";
		data += "&Entrada="+$("#reportes_compras_entrada").val()+"&OrderBy=Cliente";
		//
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_compras_imprimir4").jqxButton({
		width: 120,
		template: "info"
	});
	$("#reportes_compras_imprimir4").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_compras_simple.php?ClienteID="+$("#reportes_compras_cliente_ID").val();
		data += "&Vehiculo="+$("#reportes_compras_vehiculo").val()+"&Estado="+$("#reportes_compras_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&Interno="+$("#reportes_compras_interno").val()+"&Factura="+$("#reportes_compras_factura").val()+"";
		data += "&Entrada="+$("#reportes_compras_entrada").val()+"&OrderBy=Vehiculo";
		//
		window.open(data, "", "width=625, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_compras_imprimir5").jqxButton({
		width: 120,
		template: "info"
	});
	$("#reportes_compras_imprimir5").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_compras.php?ClienteID="+$("#reportes_compras_cliente_ID").val();
		data += "&Vehiculo="+$("#reportes_compras_vehiculo").val()+"&Estado="+$("#reportes_compras_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&Interno="+$("#reportes_compras_interno").val()+"&Factura="+$("#reportes_compras_factura").val()+"";
		data += "&Entrada="+$("#reportes_compras_entrada").val()+"&OrderBy=Vehiculo";
		//
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_compras_valor_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 210,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#reportes_compras_peso_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 210,
		symbol: 'Kg',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 4 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			GridSource.data = {
				"Reportes_Compras":true,
				"ClienteID":$("#reportes_compras_cliente_ID").val(),
				"Vehiculo":$("#reportes_compras_vehiculo").val(),
				"Estado":$("#reportes_compras_estado").val(),
				"Fecha_Ini":GetFormattedDate($("#reportes_compras_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#reportes_compras_fecha_fin").jqxDateTimeInput('getDate')),
				"Interno":$("#reportes_compras_interno").val(),
				"Factura":$("#reportes_compras_factura").val(),
				"Entrada":$("#reportes_compras_entrada").val(),
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource, {
				autoBind: true,
				beforeLoadComplete: function (records)
				{
					//alert(JSON.stringify(records))
					if (records[0]["ValorTotal"] != undefined)
					{
						$("#reportes_compras_valor_total").val(records[0]["ValorTotal"]);
						$("#reportes_compras_peso_total").val(records[0]["PesoTotal"]);
					}
					else
					{
						$("#reportes_compras_valor_total").val("0");
						$("#reportes_compras_peso_total").val("0");
					}
				}
			});
			$("#reportes_compras_items_grid").jqxGrid({source: GridDataAdapter});
		},500);
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'Doc_Transp', type: 'string' },
			{ name: 'Entrada', type: 'string' },
			{ name: 'Pedido', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Placa', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'PesoTotal', type: 'decimal' },
			{ name: 'ValorTotal', type: 'decimal' },
		],
		url: "modulos/datos.php",
	};
	
	$("#reportes_compras_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		autoheight: true,
		editable: false,
		columns:
		[
			{
				text: 'Fecha',
				datafield: 'Fecha',
				editable: false,
				width: 90,
				height: 20,
				columntype: 'datetimeinput',
				cellsformat: 'dd-MMM-yyyy',
			},
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> Interno', datafield: 'Interno', editable: false, width: 120, height: 20 },
			{ text: 'Factura', datafield: 'Factura', editable: false, width: 100, height: 20 },
			{ text: 'Doc. Transp.', datafield: 'Doc_Transp', editable: false, width: 100, height: 20 },
			{ text: 'Entrada', datafield: 'Entrada', editable: false, width: 100, height: 20 },
			{ text: 'Pedido', datafield: 'Pedido', editable: false, width: 100, height: 20 },
			{ text: 'Proveedor', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: 180, height: 20 },
			//{ text: 'ID', datafield: 'ClienteID', editable: false, width: 100, height: 20 },
			{ text: 'Placa', datafield: 'Placa', editable: false, cellsalign: 'center', width: 60, height: 20 },
			{
				text: 'Peso Kg',
				datafield: 'Peso',
				width: 100,
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{
				text: 'Total',
				datafield: 'Valor',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
		]
	});
	$("#reportes_compras_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#reportes_compras_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Interno")
		{
			Interno = value;
			if (Interno != "")
				$("#Reportes_Compras_Compra_Window").jqxWindow('open');
		}
		
	});	
	
	// ------------------------------------------ WINDOWS
	//--- Compra
	$("#Reportes_Compras_Compra_Window").jqxWindow({
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
	$("#Reportes_Compras_Compra_Window").on('open', function (event)
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
				$("#Reportes_Compras_Compra_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	// Load Initial Values!
	LoadValues();
	CheckRefresh();
});
</script>
<div id="Reportes_Compras_Compra_Window">
	<div id="Reportes_Compras_Compra_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Reportes_Compras_Compra_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px; text-align:center;">
		<tr>
			<td colspan="2">
				Cliente
			</td>
			<td>
				Cliente ID
			</td>
			<td>
				Vehículo
			</td>
			<td>
				Estado
			</td>
			<td>
				Fecha Ini
			</td>
			<td>
				Fecha Fin
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="reportes_compras_cliente"></div>
			</td>
			<td>
				<div id="reportes_compras_cliente_ID"></div>
			</td>
			<td>
				<input type="text" id="reportes_compras_vehiculo"/>
			</td>
			<td>
				<div id="reportes_compras_estado"></div>
			</td>
			<td>
				<div id="reportes_compras_fecha_ini"></div>
			</td>
			<td>
				<div id="reportes_compras_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				Interno
			</td>
			<td>
				Factura
			</td>
			<td>
				Entrada
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="reportes_compras_interno"/>
			</td>
			<td>
				<input type="text" id="reportes_compras_factura"/>
			</td>
			<td>
				<input type="text" id="reportes_compras_entrada"/>
			</td>
		</tr>
		<tr>
			<td colspan="7">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="reportes_compras_export" value="Exportar"/>
			</td>
			<td>
				<input type="button" id="reportes_compras_imprimir2" value="Por Cliente"/>
			</td>
			<td style="text-align: left;">
				<input type="button" id="reportes_compras_imprimir4" value="Por Vehiculo"/>
			</td>
			<td>
				&nbsp;
			</td>
			<td style="text-align: right;">
				Valor Total
			</td>
			<td colspan="2">
				<div id="reportes_compras_valor_total"></div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="reportes_compras_imprimir1" value="Listado"/>
			</td>
			<td>
				<input type="button" id="reportes_compras_imprimir3" value="Cliente Detalle"/>
			</td>
			<td style="text-align: left;">
				<input type="button" id="reportes_compras_imprimir5" value="Vehiculo Detalle"/>
			</td>
			<td>
				&nbsp;
			</td>
			<td style="text-align: right;">
				Peso Total
			</td>
			<td colspan="2">
				<div id="reportes_compras_peso_total"></div>
			</td>
		</tr>
	</table>
	
	<div id="reportes_compras_items_grid"></div>
</div>
