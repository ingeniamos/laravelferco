<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Reportes_Content");
	var Body = document.getElementById("Reportes_Cartera_Content");
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
				SystemMap("Cartera", true);
				
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
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Cartera" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}

				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Cartera" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#reportes_cartera_export").jqxButton({ disabled: true });
					$("#reportes_cartera_imprimir1").jqxButton({ disabled: true });
					$("#reportes_cartera_imprimir2").jqxButton({ disabled: true });
					$("#reportes_cartera_imprimir3").jqxButton({ disabled: true });
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
	
	$("#reportes_cartera_cliente").jqxComboBox(
	{
		width: 350,
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
	$("#reportes_cartera_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_cartera_cliente_ID").val() != event.args.item.value)
				$("#reportes_cartera_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_cartera_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_cartera_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_cartera_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_cartera_cliente").val();
				
				var item = $("#reportes_cartera_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_cartera_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#reportes_cartera_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_cartera_cliente").jqxComboBox('clearSelection');
			}
			LoadValues();
		}
	});
	
	$("#reportes_cartera_cliente_ID").jqxComboBox({
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
	$("#reportes_cartera_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_cartera_cliente").val() != event.args.item.value)
				$("#reportes_cartera_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_cartera_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_cartera_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_cartera_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_cartera_cliente_ID").val();
				var item = $("#reportes_cartera_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#reportes_cartera_cliente_ID").jqxComboBox('clearSelection');
					$("#reportes_cartera_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#reportes_cartera_cliente_ID").jqxComboBox('selectItem', item.value);
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
	
	$("#reportes_cartera_vendedor").jqxComboBox({
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
	$("#reportes_cartera_vendedor").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_cartera_vendedor").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#reportes_cartera_cobrador").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: VendedorDataAdapter,
		promptText: 'Seleccionar Cobrador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#reportes_cartera_cobrador").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_cartera_cobrador").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	$("#reportes_cartera_cobrador").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#reportes_cartera_cobrador").jqxComboBox("selectItem", "<?php echo $_SESSION["UserCode"]; ?>");
			$("#reportes_cartera_cobrador").jqxComboBox({ disabled: true });
		}
	});
	
	$("#reportes_cartera_tipo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 108,
		promptText: 'Seleccionar',
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	$("#reportes_cartera_tipo").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_cartera_tipo").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#reportes_cartera_tipo").jqxComboBox('addItem', {label: "Debito"});
	$("#reportes_cartera_tipo").jqxComboBox('addItem', {label: "Credito"});
	
	$("#reportes_cartera_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
	});
	$("#reportes_cartera_factura").on('change', function () {
		LoadValues();
	});
	
	$("#reportes_cartera_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_cartera_fecha_ini").jqxDateTimeInput('setDate', new Date(2013, 00, 01));
	$("#reportes_cartera_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			LoadValues();
	});
	
	$("#reportes_cartera_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_cartera_fecha_fin").on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#reportes_cartera_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 200,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#reportes_cartera_export").jqxButton({
		width: 110,
		template: "success"
	});
	$("#reportes_cartera_export").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Reportes_Cartera=true&ClienteID="+$("#reportes_cartera_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_cartera_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_cartera_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_cartera_vendedor").val()+"&CobradorID="+$("#reportes_cartera_cobrador").val();
		data += "&Factura="+$("#reportes_cartera_factura").val()+"&TipoMovimiento="+$("#reportes_cartera_tipo").val();
		//
		window.location = data;
	});
	
	$("#reportes_cartera_imprimir1").jqxButton({
		width: 110,
		template: "warning"
	});
	$("#reportes_cartera_imprimir1").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_cartera.php?ClienteID="+$("#reportes_cartera_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_cartera_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_cartera_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_cartera_vendedor").val()+"&CobradorID="+$("#reportes_cartera_cobrador").val()+"";
		data += "&Factura="+$("#reportes_cartera_factura").val()+"&TipoMovimiento="+$("#reportes_cartera_tipo").val();
		//
		window.open(data, "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_cartera_imprimir2").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_cartera_imprimir2").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_cartera.php?ClienteID="+$("#reportes_cartera_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_cartera_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_cartera_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_cartera_vendedor").val()+"&CobradorID="+$("#reportes_cartera_cobrador").val()+"";
		data += "&Factura="+$("#reportes_cartera_factura").val()+"&OrderBy=Cliente&TipoMovimiento=Debito";
		//
		window.open(data, "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_cartera_imprimir3").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_cartera_imprimir3").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_cartera.php?ClienteID="+$("#reportes_cartera_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_cartera_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_cartera_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_cartera_vendedor").val()+"&CobradorID="+$("#reportes_cartera_cobrador").val()+"";
		data += "&Factura="+$("#reportes_cartera_factura").val()+"&OrderBy=Cliente&TipoMovimiento=Debito&Zero_Filter=true";
		//
		window.open(data, "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_cartera_imprimir4").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_cartera_imprimir4").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_cartera.php?ClienteID="+$("#reportes_cartera_cliente_ID").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_cartera_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_cartera_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&VendedorID="+$("#reportes_cartera_vendedor").val()+"&CobradorID="+$("#reportes_cartera_cobrador").val();
		data += "&Factura="+$("#reportes_cartera_factura").val()+"&OrderBy=Cobrador&TipoMovimiento=Debito";
		//
		window.open(data, "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			GridSource.data = {
				"Cartera_Movimientos":true,
				"ClienteID":$("#reportes_cartera_cliente_ID").val(),
				"TipoMovimiento":$("#reportes_cartera_tipo").val(),
				"Fecha_Ini":GetFormattedDate($("#reportes_cartera_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#reportes_cartera_fecha_fin").jqxDateTimeInput('getDate')),
				"VendedorID":$("#reportes_cartera_vendedor").val(),
				"CobradorID":$("#reportes_cartera_cobrador").val(),
				"Estado":"Aprobado",
				"Factura":$("#reportes_cartera_factura").val()
			};
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#reportes_cartera_items_grid").jqxGrid({source: GridDataAdapter});
			
			if ($("#reportes_cartera_tipo").val() != "Credito")
			{
				$.ajax({
					dataType: 'json',
					url: "modulos/datos.php",
					data: {
						"Cartera_Movimientos_Deuda_Total":true,
						"ClienteID":$("#movimientos_cliente_ID").val(),
						"TipoMovimiento":"Debito",
						"Fecha_Ini":GetFormattedDate($("#reportes_cartera_fecha_ini").jqxDateTimeInput('getDate')),
						"Fecha_Fin":GetFormattedDate($("#reportes_cartera_fecha_fin").jqxDateTimeInput('getDate')),
						"VendedorID":$("#reportes_cartera_vendedor").val(),
						"CobradorID":$("#reportes_cartera_cobrador").val(),
						"Estado":"Aprobado",
						"Factura":$("#reportes_cartera_factura").val()
					},
					success: function (data)
					{
						$("#reportes_cartera_total").val(data[0]["Deuda_Total"]);
					},
					error: function (jqXHR, textStatus, errorThrown) {
						Alerts_Box("Ocurrio un Error al intentar retirar los datos!<br />Intente luego de unos segundos...", 3);
					}
				});
			}
			else
				$("#reportes_cartera_total").val("0");
		},500);
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Remision', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'TipoMovimiento', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Saldo', type: 'decimal' },
			{ name: 'Caja_Interno', type: 'string' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'VendedorID', type: 'string' },
			{ name: 'CobradorID', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
	};
	
	$("#reportes_cartera_items_grid").jqxGrid(
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
				pinned: true,
				columntype: 'datetimeinput',
				cellsformat: 'dd-MMM-yyyy',
			},
			{ text: 'Interno', datafield: 'Interno', editable: false, width: 120, height: 20 },
			{ text: 'Remision', datafield: 'Remision', editable: false, width: 100, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> Factura', datafield: 'Factura', editable: false, width: 100, height: 20, pinned: true, },
			{ text: 'Nombre', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: 180, height: 20, pinned: true, },
			{ text: 'ID', datafield: 'ClienteID', editable: false, width: 100, height: 20 },
			{ text: 'Tipo', datafield: 'TipoMovimiento', editable: false, cellsalign: 'center', width: 50, height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{
				text: 'Saldo',
				datafield: 'Saldo',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> R. Caja', datafield: 'Caja_Recibo', editable: false, width: 110, height: 20 },
			{ text: '', datafield: 'Caja_Interno', editable: false, width: 100, height: 20 },
			{ text: 'Vend.', datafield: 'VendedorID', editable: false, width: 50, height: 20 },
			{ text: 'Cobr.', datafield: 'CobradorID', editable: false, width: 50, height: 20 },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 50, height: 20 },
		]
	});
	$("#reportes_cartera_items_grid").jqxGrid('hidecolumn', 'Caja_Interno');
	$("#reportes_cartera_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#reportes_cartera_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Factura")
		{
			Interno = $("#reportes_cartera_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Interno");
			$("#Reportes_Cartera_Remision_Window").jqxWindow('open');
		}
		else if (datafield == "Caja_Recibo")
		{
			Interno = $("#reportes_cartera_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Caja_Interno");
			$("#Reportes_Cartera_Caja_Window").jqxWindow('open');
		}
	});
	
	// ------------------------------------------ WINDOWS
	//--- Remision
	$("#Reportes_Cartera_Remision_Window").jqxWindow({
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
	$("#Reportes_Cartera_Remision_Window").on('open', function (event)
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
				$("#Reportes_Cartera_Remision_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	//--- Caja Recibo
	$("#Reportes_Cartera_Caja_Window").jqxWindow({
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
	$("#Reportes_Cartera_Caja_Window").on('open', function (event)
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
				$("#Reportes_Cartera_Caja_Content").html(data);
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
<div id="Reportes_Cartera_Remision_Window">
	<div id="Reportes_Cartera_Remision_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Reportes_Cartera_Remision_Content" class="WindowContainer">
	</div>
</div>
<div id="Reportes_Cartera_Caja_Window">
	<div id="Reportes_Cartera_Caja_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Recibo de CAJA</div>
	</div>
	<div id="Reportes_Cartera_Caja_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px; text-align:center;">
		<tr>
			<td colspan="3">
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
		</tr>
		<tr>
			<td colspan="3">
				<div id="reportes_cartera_cliente"></div>
			</td>
			<td>
				<div id="reportes_cartera_cliente_ID"></div>
			</td>
			<td>
				<div id="reportes_cartera_vendedor"></div>
			</td>
			<td>
				<div id="reportes_cartera_cobrador"></div>
			</td>
		</tr>
		<tr>
			<td>
				Tipo Mov
			</td>
			<td>
				Factura
			</td>
			<td>
				Fecha Ini
			</td>
			<td>
				Fecha Fin
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				TOTAL
			</td>
		</tr>
		<tr>
			<td>
				<div id="reportes_cartera_tipo"></div>
			</td>
			<td>
				<input type="text" id="reportes_cartera_factura"/>
			</td>
			<td>
				<div id="reportes_cartera_fecha_ini"></div>
			</td>
			<td>
				<div id="reportes_cartera_fecha_fin"></div>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				<div id="reportes_cartera_total"></div>
			</td>
		</tr>
		<tr>
			<td colspan="6">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="reportes_cartera_export" value="Exportar"/>
			</td>
			<td>
				<input type="button" id="reportes_cartera_imprimir1" value="Listado"/>
			</td>
			<td>
				<input type="button" id="reportes_cartera_imprimir2" value="Por Cliente"/>
			</td>
			<td>
				<input type="button" id="reportes_cartera_imprimir3" value="Cliente sin $0"/>
			</td>
			<td style="text-align: left;">
				<input type="button" id="reportes_cartera_imprimir4" value="Por Cobrador"/>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
	</table>
	
	<div id="reportes_cartera_items_grid"></div>
</div>
