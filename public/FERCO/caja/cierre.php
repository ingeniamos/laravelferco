<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Interno = "";
	var Timer1 = 0;
	var Timer2 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Caja_Content");
	var Body = document.getElementById("CierreCaja_Content");
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
				SystemMap("Cierre de Caja", true);
				LoadValues();
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
	var Supervisor = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "CierreCaja" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "CierreCaja" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#caja_cierre_imprimir").jqxButton({ disabled: true });
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
	
	$("#caja_cierre_cajero").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		source: VendedorDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#caja_cierre_cajero").bind('change', function (event) {
		if (!event.args)
		{	
			if ($("#caja_cierre_cajero").val() != "ss" )
				$("#caja_cierre_cajero").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	$("#caja_cierre_cajero").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#caja_cierre_cajero").val("<?php echo $_SESSION["UserCode"]; ?>");
			$("#caja_cierre_cajero").jqxComboBox({ disabled: true });
		}
	});
	
	$("#caja_cierre_total_ingresos").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	$("#caja_cierre_total_egresos").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	$("#caja_cierre_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#caja_cierre_fecha_ini").on("change", function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#caja_cierre_recaudos_ventas_cartera").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	$("#caja_cierre_recaudos_cheques_dev").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	$("#caja_cierre_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#caja_cierre_fecha_fin").on("change", function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#caja_cierre_retenciones").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	$("#caja_cierre_descuentos").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	var Values = [
		{"Tipo":"Pendiente"},
		{"Tipo":"Aprobado"},
		{"Tipo":"Todos"},
	];
	
	$("#caja_cierre_tipo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		source: Values,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: Admin ? 2:0,
		disabled: Admin ? false:true,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#caja_cierre_tipo").bind('change', function (event) {
		if (!event.args)
		{
			$("#caja_cierre_tipo").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#caja_cierre_total_efectivo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	$("#caja_cierre_total_cheques").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	$("#caja_cierre_imprimir").jqxButton({width: 120, template: "warning"});
	$("#caja_cierre_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/caja_cierre.php?DigitadorID="+$("#caja_cierre_cajero").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#caja_cierre_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#caja_cierre_fecha_fin").jqxDateTimeInput('getDate'))+"";
		data += "&Estado="+$("#caja_cierre_tipo").val();
		window.open(data, "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#caja_cierre_total_saldo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	$("#caja_cierre_total_tarjetas").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		min: 0,
		disabled: true,
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
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
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,{ autoBind: true });
	
	function LoadValues()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{
			clearTimeout(Timer1);
			
			var MainSource = 
			{
				datatype: "json",
				datafields:
				[
					{ name: 'Total_Ingresos', type: 'decimal' },
					{ name: 'Total_Egresos', type: 'decimal' },
					{ name: 'Recaudos_Cartera', type: 'decimal' },
					{ name: 'Recaudos_Cheques', type: 'decimal' },
					{ name: 'Retenciones', type: 'decimal' },
					{ name: 'Descuentos', type: 'decimal' },
					{ name: 'Total_Efectivo', type: 'decimal' },
					{ name: 'Total_Cheques', type: 'decimal' },
					{ name: 'Saldo_Caja', type: 'decimal' },
					{ name: 'Total_Tarjetas', type: 'decimal' },
				],
				data: {
					"Caja_Cierre_Datos":true,
					"DigitadorID":$("#caja_cierre_cajero").val(),
					"Fecha_Ini":GetFormattedDate($("#caja_cierre_fecha_ini").jqxDateTimeInput("getDate")),
					"Fecha_Fin":GetFormattedDate($("#caja_cierre_fecha_fin").jqxDateTimeInput("getDate")),
					"Estado":$("#caja_cierre_tipo").val()
				},
				url: "modulos/datos.php",
				async: true
			};
			var MainDataAdapter = new $.jqx.dataAdapter(MainSource,{
				autoBind: true,
				loadComplete: function ()
				{
					var myrecords = MainDataAdapter.records;
					// Add Data
					if (myrecords[0]["Total_Ingresos"] != "")
					{
						$("#caja_cierre_total_ingresos").val(myrecords[0]["Total_Ingresos"]);
						$("#caja_cierre_total_egresos").val(myrecords[0]["Total_Egresos"]);
						$("#caja_cierre_recaudos_ventas_cartera").val(myrecords[0]["Recaudos_Cartera"]);
						$("#caja_cierre_recaudos_cheques_dev").val(myrecords[0]["Recaudos_Cheques"]);
						$("#caja_cierre_retenciones").val(myrecords[0]["Retenciones"]);
						$("#caja_cierre_descuentos").val(myrecords[0]["Descuentos"]);
						$("#caja_cierre_total_efectivo").val(myrecords[0]["Total_Efectivo"]);
						$("#caja_cierre_total_cheques").val(myrecords[0]["Total_Cheques"]);
						$("#caja_cierre_total_saldo").val(myrecords[0]["Saldo_Caja"]);
						$("#caja_cierre_total_tarjetas").val(myrecords[0]["Total_Tarjetas"]);
					}// Clean
					else
					{
						$("#caja_cierre_total_ingresos").val("");
						$("#caja_cierre_total_egresos").val("");
						$("#caja_cierre_recaudos_ventas_cartera").val("");
						$("#caja_cierre_recaudos_cheques_dev").val("");
						$("#caja_cierre_retenciones").val("");
						$("#caja_cierre_descuentos").val("");
						$("#caja_cierre_total_efectivo").val("");
						$("#caja_cierre_total_cheques").val("");
						$("#caja_cierre_total_saldo").val("");
						$("#caja_cierre_total_tarjetas").val("");
					}
				},
				loadError: function(jqXHR, status, error) {
					alert("Request failed: \n" + error);
				},
			});
			
			GridSource1.data = {
				"Caja_Cierre_Todo":true,
				"DigitadorID":$("#caja_cierre_cajero").val(),
				"Fecha_Ini":GetFormattedDate($("#caja_cierre_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#caja_cierre_fecha_fin").jqxDateTimeInput("getDate")),
				"Estado":$("#caja_cierre_tipo").val()
			};
			
			GridSource2.data = {
				"Caja_Cierre_Cheques_Dev":true,
				"DigitadorID":$("#caja_cierre_cajero").val(),
				"Fecha_Ini":GetFormattedDate($("#caja_cierre_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#caja_cierre_fecha_fin").jqxDateTimeInput("getDate")),
				"Estado":$("#caja_cierre_tipo").val()
			};
			
			GridSource3.data = {
				"Caja_Cierre_Cheques":true,
				"DigitadorID":$("#caja_cierre_cajero").val(),
				"Fecha_Ini":GetFormattedDate($("#caja_cierre_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#caja_cierre_fecha_fin").jqxDateTimeInput("getDate")),
				"Estado":$("#caja_cierre_tipo").val()
			};
			
			GridSource4.data = {
				"Caja_Cierre_Consignaciones":true,
				"DigitadorID":$("#caja_cierre_cajero").val(),
				"Fecha_Ini":GetFormattedDate($("#caja_cierre_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#caja_cierre_fecha_fin").jqxDateTimeInput("getDate")),
				"Estado":$("#caja_cierre_tipo").val()
			};
			
			GridDataAdapter1 = new $.jqx.dataAdapter(GridSource1);
			GridDataAdapter2 = new $.jqx.dataAdapter(GridSource2);
			GridDataAdapter3 = new $.jqx.dataAdapter(GridSource3);
			GridDataAdapter4 = new $.jqx.dataAdapter(GridSource4);
			
			$("#caja_cierre_items_grid1").jqxGrid({source: GridDataAdapter1});
			$("#caja_cierre_items_grid2").jqxGrid({source: GridDataAdapter2});
			$("#caja_cierre_items_grid3").jqxGrid({source: GridDataAdapter3});
			$("#caja_cierre_items_grid4").jqxGrid({source: GridDataAdapter4});
			
		},350);
		
	};
	
	var GridSource1 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Caja_Interno', type: 'string' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'Categoria', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'Total', type: 'decimal' },
			{ name: 'Efectivo', type: 'decimal' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'AutorizadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
	};
	
	$("#caja_cierre_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 260,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		pageable: true,
		editable: false,
		editmode: 'click',
		columns:
		[
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: "10%",
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Interno Caja', datafield: 'Caja_Interno', editable: false, width: "10%", height: 20 },
			{ text: 'Recibo Caja', datafield: 'Caja_Recibo', editable: false, width: "10%", height: 20 },
			{ text: 'Categoria', datafield: 'Categoria', editable: false, width: "8%", height: 20 },
			{ text: 'Beneficiario', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: "26%", height: 20 },
			{
				text: 'Total',
				datafield: 'Total',
				width: "13%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{
				text: 'Efectivo',
				datafield: 'Efectivo',
				width: "13%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: "5%", height: 20 },
			{ text: 'Aut.', datafield: 'AutorizadorID', editable: false, width: "5%", height: 20 },
		]
	});
	$("#caja_cierre_items_grid1").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Caja_Interno")
		{
			Interno = $("#caja_cierre_items_grid1").jqxGrid('getcellvalue', rowBoundIndex, "Caja_Interno");
			$("#Caja_Cierre_Caja_Window").jqxWindow('open');
		}
	});
	
	var GridSource2 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Caja_Interno', type: 'string' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'Categoria', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'Total', type: 'decimal' },
			{ name: 'Efectivo', type: 'decimal' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'AutorizadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
	};
	
	$("#caja_cierre_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 260,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		pageable: true,
		editable: false,
		editmode: 'click',
		columns:
		[
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: "10%",
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Interno Caja', datafield: 'Caja_Interno', editable: false, width: "10%", height: 20 },
			{ text: 'Recibo Caja', datafield: 'Caja_Recibo', editable: false, width: "10%", height: 20 },
			{ text: 'Categoria', datafield: 'Categoria', editable: false, width: "8%", height: 20 },
			{ text: 'Beneficiario', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: "26%", height: 20 },
			{
				text: 'Total',
				datafield: 'Total',
				width: "13%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{
				text: 'Efectivo',
				datafield: 'Efectivo',
				width: "13%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: "5%", height: 20 },
			{ text: 'Aut.', datafield: 'AutorizadorID', editable: false, width: "5%", height: 20 },
		]
	});
	$("#caja_cierre_items_grid2").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Caja_Interno")
		{
			Interno = $("#caja_cierre_items_grid2").jqxGrid('getcellvalue', rowBoundIndex, "Caja_Interno");
			$("#Caja_Cierre_Caja_Window").jqxWindow('open');
		}
	});
	
	var GridSource3 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'NumeroCheque', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Banco', type: 'string' },
			{ name: 'NumeroCuenta', type: 'string' },
			{ name: 'FechaCheque', type: 'date' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
	};
	
	$("#caja_cierre_items_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 490,
		height: 180,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		pageable: true,
		editable: false,
		editmode: 'click',
		columns:
		[
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: 100,
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Recibo Caja', datafield: 'Caja_Recibo', editable: false, width: 100, height: 20 },
			{ text: 'Nmero Cheque', datafield: 'NumeroCheque', editable: false, width: 100, height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Banco', datafield: 'Banco', editable: false, width: 50, height: 20 },
			{ text: 'Numero Cuenta', datafield: 'NumeroCuenta', editable: false, width: 100, height: 20 },
			{
				text: 'Fecha Cheque',
				datafield: 'FechaCheque',
				columntype: 'datetimeinput',
				width: 100,
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Titular', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: 180, height: 20 },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 50, height: 20 },
		]
	});
	
	var GridSource4 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'Tipo', type: 'string' },
			{ name: 'Banco', type: 'string' },
			{ name: 'NumeroAprobacion', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
	};
	
	$("#caja_cierre_items_grid4").jqxGrid(
	{
		theme: mytheme,
		width: 490,
		height: 180,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		pageable: true,
		editable: false,
		editmode: 'click',
		columns:
		[
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: 100,
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Recibo Caja', datafield: 'Caja_Recibo', editable: false, width: 100, height: 20 },
			{ text: 'Tipo', datafield: 'Tipo', editable: false, width: 100, height: 20 },
			{ text: 'Banco', datafield: 'Banco', editable: false, width: 100, height: 20 },
			{ text: '# Aprobaci&oacute;n', datafield: 'NumeroAprobacion', editable: false, width: 100, height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Titular', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: 180, height: 20 },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 50, height: 20 },
		]
	});
	
	// ------------------------------------------ WINDOWS
	//--- Caja Recibo
	$("#Caja_Cierre_Caja_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 1020,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Caja_Cierre_Caja_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Caja_Interno":Interno,
			},
			url: "caja/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Caja_Cierre_Caja_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	$("#Caja_Cierre_Caja_Window").on('close', function (event)
	{
		Interno = "";
	});
	
	// Load Initial Values!
	LoadValues();
	CheckRefresh();
});
</script>
<div id="Caja_Cierre_Caja_Window">
	<div id="Caja_Cierre_Caja_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Recibo de CAJA</div>
	</div>
	<div id="Caja_Cierre_Caja_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="3">
		<tr>
			<td>
				Caja
			</td>
			<td style="padding-right:40px;">
				<div id="caja_cierre_cajero"></div>
			</td>
			<td>
				TOTAL INGRESOS
			</td>
			<td style="padding-right:40px;">
				<div id="caja_cierre_total_ingresos"></div>
			</td>
			<td>
				TOTAL EGRESOS
			</td>
			<td>
				<div id="caja_cierre_total_egresos"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha Ini.
			</td>
			<td>
				<div id="caja_cierre_fecha_ini"></div>
			</td>
			<td>
				Recaudos Venta/Cartera
			</td>
			<td>
				<div id="caja_cierre_recaudos_ventas_cartera"></div>
			</td>
			<td>
				Recaudos Cheques Dev.
			</td>
			<td>
				<div id="caja_cierre_recaudos_cheques_dev"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha Fin.
			</td>
			<td>
				<div id="caja_cierre_fecha_fin"></div>
			</td>
			<td>
				Retenciones FERCO
			</td>
			<td>
				<div id="caja_cierre_retenciones"></div>
			</td>
			<td>
				Descuentos
			</td>
			<td>
				<div id="caja_cierre_descuentos"></div>
			</td>
		</tr>
		<tr>
			<td>
				Ver Como:
			</td>
			<td>
				<div id="caja_cierre_tipo"></div>
			</td>
			<td>
				Total Efectivo
			</td>
			<td>
				<div id="caja_cierre_total_efectivo"></div>
			</td>
			<td>
				Total Cheques
			</td>
			<td>
				<div id="caja_cierre_total_cheques"></div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<input type="button" id="caja_cierre_imprimir" value="Imprimir Cierre"/>
			</td>
			<td>
				Saldo en Caja
			</td>
			<td>
				<div id="caja_cierre_total_saldo"></div>
			</td>
			<td>
				Total Tarjetas
			</td>
			<td>
				<div id="caja_cierre_total_tarjetas"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte3">
	<table cellpadding="0" cellspacing="0" style="margin-top:20px;">
		<tr>
			<td colspan="2">
				<div style="width: 100%; height: 20px; background-color: #5DBA5D; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">RESUMEN TOTAL</p>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-bottom:20px;">
				<div id="caja_cierre_items_grid1"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div style="width: 100%; height: 20px; background-color: #F18B0C; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">RESUMEN DE CHEQUES DEVUELTOS</p>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-bottom:20px;">
				<div id="caja_cierre_items_grid2"></div>
			</td>
		</tr>
		<tr>
			<td style="padding-right:18px;">
				<div style="width: 100%; height: 20px; background-color: #3E72A2; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">RESUMEN CHQEUES</p>
				</div>
			</td>
			<td>
				<div style="width: 100%; height: 20px; background-color: #3E72A2; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">RESUMEN TARJETAS/CONSIGNACI&Oacute;N/TRANSFERENCIAS</p>
				</div>
			</td>
		</tr>
		<tr>
			<td style="padding-right:18px;">
				<div id="caja_cierre_items_grid3"></div>
			</td>
			<td>
				<div id="caja_cierre_items_grid4"></div>
			</td>
		</tr>
	</table>
</div>