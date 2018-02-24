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
	var Body = document.getElementById("Reportes_Facturacion_Content");
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
				SystemMap("Facturacion", true);
				
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
	var Supervisor = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Facturacion" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Facturacion" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#reportes_facturacion_export").jqxButton({ disabled: true });
					$("#reportes_facturacion_imprimir1").jqxButton({ disabled: true });
					$("#reportes_facturacion_imprimir2").jqxButton({ disabled: true });
					$("#reportes_facturacion_imprimir3").jqxButton({ disabled: true });
					$("#reportes_facturacion_imprimir4").jqxButton({ disabled: true });
					$("#reportes_facturacion_imprimir5").jqxButton({ disabled: true });
					$("#reportes_facturacion_imprimir6").jqxButton({ disabled: true });
					$("#reportes_facturacion_imprimir7").jqxButton({ disabled: true });
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
	
	$("#reportes_facturacion_cliente").jqxComboBox(
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
	$("#reportes_facturacion_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_facturacion_cliente_ID").val() != event.args.item.value)
				$("#reportes_facturacion_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_facturacion_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_facturacion_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_facturacion_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_facturacion_cliente").val();
				
				var item = $("#reportes_facturacion_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_facturacion_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#reportes_facturacion_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_facturacion_cliente").jqxComboBox('clearSelection');
			}
			LoadValues();
		}
	});
	
	$("#reportes_facturacion_cliente_ID").jqxComboBox({
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
	$("#reportes_facturacion_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_facturacion_cliente").val() != event.args.item.value)
				$("#reportes_facturacion_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_facturacion_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_facturacion_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_facturacion_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_facturacion_cliente_ID").val();
				var item = $("#reportes_facturacion_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#reportes_facturacion_cliente_ID").jqxComboBox('clearSelection');
					$("#reportes_facturacion_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#reportes_facturacion_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
		LoadValues();
	});
	
	var VendedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Vendedor', type: 'string'}
		],
		data: {"Venta":true},
		url: "modulos/parametros.php",
	};
	var VendedorDataAdapter = new $.jqx.dataAdapter(VendedorSource);
	
	$("#reportes_facturacion_vendedor").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: VendedorDataAdapter,
		promptText: 'Seleccionar Vendedor',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#reportes_facturacion_vendedor").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_facturacion_vendedor").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	$("#reportes_facturacion_vendedor").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#reportes_facturacion_vendedor").jqxComboBox("selectItem", "<?php echo $_SESSION["UserCode"]; ?>");
			$("#reportes_facturacion_vendedor").jqxComboBox({ disabled: true });
		}
	});
	
	$("#reportes_facturacion_cobrador").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 190,
		source: VendedorDataAdapter,
		promptText: 'Seleccionar Cobrador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#reportes_facturacion_cobrador").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_facturacion_cobrador").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#reportes_facturacion_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_facturacion_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#reportes_facturacion_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			LoadValues();
	});
	
	$("#reportes_facturacion_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_facturacion_fecha_fin").on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var EstadoValues = [
		{"Estado":"Aprobado"},
		{"Estado":"Autorizado"},
		{"Estado":"Creado"},
	];
	
	$("#reportes_facturacion_estado").jqxComboBox({
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
	$("#reportes_facturacion_estado").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_facturacion_estado").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#reportes_facturacion_ord_compra").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	$("#reportes_facturacion_ord_compra").on('change', function () {
		LoadValues();
	});
	
	$("#reportes_facturacion_remision").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	$("#reportes_facturacion_remision").on('change', function () {
		LoadValues();
	});
	
	$("#reportes_facturacion_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	$("#reportes_facturacion_factura").on('change', function () {
		LoadValues();
	});
	
	var ConductorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Placa', type: 'string'},
			{ name: 'Chofer', type: 'string'}
		],
		data: {"Vehiculo":true},
		url: "modulos/parametros.php",
	};
	var ConductorDataAdapter = new $.jqx.dataAdapter(ConductorSource);
	
	$("#reportes_facturacion_vehiculo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		source: ConductorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Placa',
		valueMember: 'Placa',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#reportes_facturacion_vehiculo").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_facturacion_vehiculo").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#reportes_facturacion_peso").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		symbol: 'Kg',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 12,
		max: 999999999999,
		disabled: true,
	});
	
	$("#reportes_facturacion_gasto").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	
	$("#reportes_facturacion_export").jqxButton({
		width: 120,
		template: "success"
	});
	$("#reportes_facturacion_export").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Reportes_Ventas=true&ClienteID="+$("#reportes_facturacion_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_facturacion_vendedor").val()+"&CobradorID="+$("#reportes_facturacion_cobrador").val()+"";
		data += "&Estado="+$("#reportes_facturacion_estado").val()+"&Ord_Compra="+$("#reportes_facturacion_ord_compra").val()+"";
		data += "&Remision="+$("#reportes_facturacion_remision").val()+"&Factura="+$("#reportes_facturacion_factura").val()+"";
		data += "&Vehiculo="+$("#reportes_facturacion_vehiculo").val();
		//
		window.location = data;
	});
	
	$("#reportes_facturacion_imprimir1").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_facturacion_imprimir1").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_facturacion.php?ClienteID="+$("#reportes_facturacion_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_facturacion_vendedor").val()+"&CobradorID="+$("#reportes_facturacion_cobrador").val()+"";
		data += "&Estado="+$("#reportes_facturacion_estado").val()+"&Ord_Compra="+$("#reportes_facturacion_ord_compra").val()+"";
		data += "&Remision="+$("#reportes_facturacion_remision").val()+"&Factura="+$("#reportes_facturacion_factura").val()+"";
		data += "&Vehiculo="+$("#reportes_facturacion_vehiculo").val();
		//
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_facturacion_imprimir2").jqxButton({
		width: 120,
		template: "primary"
	});
	$("#reportes_facturacion_imprimir2").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_facturacion_simple.php?ClienteID="+$("#reportes_facturacion_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_facturacion_vendedor").val()+"&CobradorID="+$("#reportes_facturacion_cobrador").val()+"";
		data += "&Estado="+$("#reportes_facturacion_estado").val()+"&Ord_Compra="+$("#reportes_facturacion_ord_compra").val()+"";
		data += "&Remision="+$("#reportes_facturacion_remision").val()+"&Factura="+$("#reportes_facturacion_factura").val()+"";
		data += "&Vehiculo="+$("#reportes_facturacion_vehiculo").val()+"&OrderBy=Cliente";
		//
		window.open(data, "", "width=625, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_facturacion_imprimir3").jqxButton({
		width: 120,
		template: "primary"
	});
	$("#reportes_facturacion_imprimir3").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_facturacion.php?ClienteID="+$("#reportes_facturacion_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_facturacion_vendedor").val()+"&CobradorID="+$("#reportes_facturacion_cobrador").val()+"";
		data += "&Estado="+$("#reportes_facturacion_estado").val()+"&Ord_Compra="+$("#reportes_facturacion_ord_compra").val()+"";
		data += "&Remision="+$("#reportes_facturacion_remision").val()+"&Factura="+$("#reportes_facturacion_factura").val()+"";
		data += "&Vehiculo="+$("#reportes_facturacion_vehiculo").val()+"&OrderBy=Cliente";
		//
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_facturacion_imprimir4").jqxButton({
		width: 120,
		template: "info"
	});
	$("#reportes_facturacion_imprimir4").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_facturacion_simple.php?ClienteID="+$("#reportes_facturacion_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_facturacion_vendedor").val()+"&CobradorID="+$("#reportes_facturacion_cobrador").val()+"";
		data += "&Estado="+$("#reportes_facturacion_estado").val()+"&Ord_Compra="+$("#reportes_facturacion_ord_compra").val()+"";
		data += "&Remision="+$("#reportes_facturacion_remision").val()+"&Factura="+$("#reportes_facturacion_factura").val()+"";
		data += "&Vehiculo="+$("#reportes_facturacion_vehiculo").val()+"&OrderBy=Vendedor";
		//
		window.open(data, "", "width=625, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_facturacion_imprimir5").jqxButton({
		width: 120,
		template: "info"
	});
	$("#reportes_facturacion_imprimir5").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_facturacion.php?ClienteID="+$("#reportes_facturacion_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_facturacion_vendedor").val()+"&CobradorID="+$("#reportes_facturacion_cobrador").val()+"";
		data += "&Estado="+$("#reportes_facturacion_estado").val()+"&Ord_Compra="+$("#reportes_facturacion_ord_compra").val()+"";
		data += "&Remision="+$("#reportes_facturacion_remision").val()+"&Factura="+$("#reportes_facturacion_factura").val()+"";
		data += "&Vehiculo="+$("#reportes_facturacion_vehiculo").val()+"&OrderBy=Vendedor";
		//
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_facturacion_imprimir6").jqxButton({
		width: 120,
		template: "default"
	});
	$("#reportes_facturacion_imprimir6").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_facturacion_simple.php?ClienteID="+$("#reportes_facturacion_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_facturacion_vendedor").val()+"&CobradorID="+$("#reportes_facturacion_cobrador").val()+"";
		data += "&Estado="+$("#reportes_facturacion_estado").val()+"&Ord_Compra="+$("#reportes_facturacion_ord_compra").val()+"";
		data += "&Remision="+$("#reportes_facturacion_remision").val()+"&Factura="+$("#reportes_facturacion_factura").val()+"";
		data += "&Vehiculo="+$("#reportes_facturacion_vehiculo").val()+"&OrderBy=Vehiculo";
		//
		window.open(data, "", "width=625, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_facturacion_imprimir7").jqxButton({
		width: 120,
		template: "default"
	});
	$("#reportes_facturacion_imprimir7").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_facturacion.php?ClienteID="+$("#reportes_facturacion_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_facturacion_vendedor").val()+"&CobradorID="+$("#reportes_facturacion_cobrador").val()+"";
		data += "&Estado="+$("#reportes_facturacion_estado").val()+"&Ord_Compra="+$("#reportes_facturacion_ord_compra").val()+"";
		data += "&Remision="+$("#reportes_facturacion_remision").val()+"&Factura="+$("#reportes_facturacion_factura").val()+"";
		data += "&Vehiculo="+$("#reportes_facturacion_vehiculo").val()+"&OrderBy=Vehiculo";
		//
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_facturacion_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 210,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#reportes_facturacion_total_gasto").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 185,
		symbol: '$',
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
				"Reportes_Ventas":true,
				"ClienteID":$("#reportes_facturacion_cliente_ID").val(),
				"Fecha_Ini":GetFormattedDate($("#reportes_facturacion_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#reportes_facturacion_fecha_fin").jqxDateTimeInput('getDate')),
				"VendedorID":$("#reportes_facturacion_vendedor").val(),
				"CobradorID":$("#reportes_facturacion_cobrador").val(),
				"Estado":$("#reportes_facturacion_estado").val(),
				"Ord_Compra":$("#reportes_facturacion_ord_compra").val(),
				"Remision":$("#reportes_facturacion_remision").val(),
				"Factura":$("#reportes_facturacion_factura").val(),
				"Vehiculo":$("#reportes_facturacion_vehiculo").val(),
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource, {
				autoBind: true,
				beforeLoadComplete: function (records)
				{
					if (records[0]["ValorTotal"] != undefined)
					{
						$("#reportes_facturacion_peso").val(records[0]["PesoTotal"]);
						$("#reportes_facturacion_gasto").val(records[0]["GastoTotal"]);
						$("#reportes_facturacion_total").val(records[0]["ValorTotal"]);
						$("#reportes_facturacion_total_gasto").val((records[0]["GastoTotal"] / records[0]["PesoTotal"]));
					}
					else
					{
						$("#reportes_facturacion_peso").val("0");
						$("#reportes_facturacion_gasto").val("0");
						$("#reportes_facturacion_total").val("0");
						$("#reportes_facturacion_total_gasto").val("0");
					}
				}
			});
			$("#reportes_facturacion_items_grid").jqxGrid({source: GridDataAdapter});
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
			{ name: 'Remision', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			//{ name: 'ClienteID', type: 'string' },
			{ name: 'F_Pago', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Ruta', type: 'string' },
			{ name: 'VendedorID', type: 'string' },
			{ name: 'CobradorID', type: 'string' },
			{ name: 'ValorTotal', type: 'decimal' },
			{ name: 'GastoTotal', type: 'decimal' },
			{ name: 'PesoTotal', type: 'decimal' },
		],
		url: "modulos/datos.php",
	};
	
	$("#reportes_facturacion_items_grid").jqxGrid(
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
			{ text: 'Remision', datafield: 'Remision', editable: false, width: 100, height: 20 },
			{ text: 'Nombre', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: 180, height: 20 },
			//{ text: 'ID', datafield: 'ClienteID', editable: false, width: 100, height: 20 },
			{ text: 'F. Pago', datafield: 'F_Pago', editable: false, cellsalign: 'center', width: 60, height: 20 },
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
			{ text: 'Ruta', datafield: 'Ruta', editable: false, cellsalign: 'center', width: 60, height: 20 },
			{ text: 'Vend', datafield: 'VendedorID', editable: false, cellsalign: 'center', width: 60, height: 20 },
			{ text: 'Cobr', datafield: 'CobradorID', editable: false, cellsalign: 'center', width: 60, height: 20 },
		]
	});
	$("#reportes_facturacion_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#reportes_facturacion_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Interno")
		{
			Interno = value;
			$("#Reportes_Ventas_Remision_Window").jqxWindow('open');
		}
	});
	
	// ------------------------------------------ WINDOWS
	//--- Remision
	$("#Reportes_Ventas_Remision_Window").jqxWindow({
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
	$("#Reportes_Ventas_Remision_Window").on('open', function (event)
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
				$("#Reportes_Ventas_Remision_Content").html(data);
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
<div id="Reportes_Ventas_Remision_Window">
	<div id="Reportes_Ventas_Remision_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Reportes_Ventas_Remision_Content" class="WindowContainer">
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
				Vendedor
			</td>
			<td>
				Cobrador
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
				<div id="reportes_facturacion_cliente"></div>
			</td>
			<td>
				<div id="reportes_facturacion_cliente_ID"></div>
			</td>
			<td>
				<div id="reportes_facturacion_vendedor"></div>
			</td>
			<td>
				<div id="reportes_facturacion_cobrador"></div>
			</td>
			<td>
				<div id="reportes_facturacion_fecha_ini"></div>
			</td>
			<td>
				<div id="reportes_facturacion_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				Ord Compra
			</td>
			<td>
				Remision
			</td>
			<td colspan="4">
				<li style="padding-left:40px;">
					Factura
				</li>
				<li style="padding-left:70px;">
					Vehiculo
				</li>
				<li style="padding-left:90px;">
					Peso
				</li>
				<li style="padding-left:130px;">
					Gasto
				</li>
			</td>
		</tr>
		<tr>
			<td>
				<div id="reportes_facturacion_estado"></div>
			</td>
			<td>
				<input type="text" id="reportes_facturacion_ord_compra"/>
			</td>
			<td>
				<input type="text" id="reportes_facturacion_remision"/>
			</td>
			<td colspan="4">
				<li style="padding:0px;">
					<input type="text" id="reportes_facturacion_factura"/>
				</li>
				<li style="padding-left:4px;">
					<div id="reportes_facturacion_vehiculo"></div>
				</li>
				<li style="padding-left:4px;">
					<div id="reportes_facturacion_peso"></div>
				</li>
				<li style="padding-left:4px;">
					<div id="reportes_facturacion_gasto"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="7">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="reportes_facturacion_export" value="Exportar"/>
			</td>
			<td>
				<input type="button" id="reportes_facturacion_imprimir2" value="Por Cliente"/>
			</td>
			<td>
				<input type="button" id="reportes_facturacion_imprimir4" value="Por Vendedor"/>
			</td>
			<td style="text-align: left;">
				<input type="button" id="reportes_facturacion_imprimir6" value="Por Vehiculo"/>
			</td>
			<td style="text-align: right;">
				TOTALES
			</td>
			<td colspan="2">
				<div id="reportes_facturacion_total"></div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="reportes_facturacion_imprimir1" value="Listado"/>
			</td>
			<td>
				<input type="button" id="reportes_facturacion_imprimir3" value="Cliente Detalle"/>
			</td>
			<td>
				<input type="button" id="reportes_facturacion_imprimir5" value="Vendedor Detalle"/>
			</td>
			<td style="text-align: left;">
				<input type="button" id="reportes_facturacion_imprimir7" value="Vehiculo Detalle"/>
			</td>
			<td style="text-align: right;">
				Rel. Gasto
			</td>
			<td colspan="2">
				<div id="reportes_facturacion_total_gasto" style="float:left;"></div><span style="float:right; margin-top:4px;">/Kg</span>
			</td>
		</tr>
	</table>
	
	<div id="reportes_facturacion_items_grid"></div>
	
</div>
