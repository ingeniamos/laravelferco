<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Interno = "";
	var ClienteID = "";
	var Timer1 = 0;
	var Timer2 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Cartera_Content");
	var Body = document.getElementById("Listado_Content");
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
				SystemMap("Listado", true);
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
	var Admin = false;
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#cartera_listado_export").jqxButton({ disabled: true });
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
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'},
		{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
		async: false
	};
	var ClienteArray = new Array();
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ClienteArray.push(records[i]);
			}
		}
	});
	
	$("#cartera_listado_cliente").jqxComboBox(
	{
		width: 300,
		height: 20,
		theme: mytheme,
		source: ClienteArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#cartera_listado_cliente").bind('change', function (event) {
		if (!event.args)
		{
			$("#cartera_listado_cliente").jqxComboBox('clearSelection');
			$("#cartera_listado_cliente_ID").jqxComboBox('clearSelection');
			$("#cartera_listado_deuda").val("0");
		}
		else
			$("#cartera_listado_cliente_ID").val(event.args.item.value);
	});
	
	$("#cartera_listado_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		theme: mytheme,
		source: ClienteArray,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#cartera_listado_cliente_ID").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (event.args.item.value != $("#cartera_listado_cliente").val())
					$("#cartera_listado_cliente").val(event.args.item.value);
				
				$.ajax({
					dataType: 'text',
					url: "modulos/datos.php",
					data: {
						"Cartera_Movimientos_Deuda":true,
						"ClienteID":event.args.item.value,
					},
					async: true,
					success: function (data) {
						if (data != "")
							$("#cartera_listado_deuda").val(data);
						else
							$("#cartera_listado_deuda").val("0");
					},
					error: function (jqXHR, textStatus, errorThrown) {
						Alerts_Box("Ocurrio un Error al intentar retirar los datos!<br />Intente luego de unos segundos...", 3);
					}
				});
				
			},350);
		}
		else
		{
			$("#cartera_listado_cliente").jqxComboBox('clearSelection');
			$("#cartera_listado_cliente_ID").jqxComboBox('clearSelection');
			$("#cartera_listado_deuda").val("0");
		}
	});
	
	$("#cartera_listado_deuda").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 300,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 21
	});
	
	$("#cartera_listado_export").jqxButton({
		width: 150,
		template: "success"
	});
	$("#cartera_listado_export").bind('click', function ()
	{
		window.location = "modulos/export_xls.php?Cartera_Listado=true";
	});
	
	//******************************************************************************************************//
	//------------------------------------------------------------------------------------------------------//
	//-------------------------------------------- PARTE 2 -------------------------------------------------//
	//------------------------------------------------------------------------------------------------------//
	//******************************************************************************************************//
	
	function LoadValues()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			GridSource.data = { "Cartera_Listado":true };
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#cartera_listado_items_grid").jqxGrid({ source: GridDataAdapter });
		},250);
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			//{ name: 'Nombre', value: 'ID', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Nombre', type: 'string' },
			//{ name: 'ID', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Deuda', type: 'decimal' },
			{ name: 'Compra', type: 'decimal' },
			{ name: 'Fecha', type: 'date' },
		],
		//data: { "Cartera_Listado":true },
		url: "modulos/datos.php",
		async: true
	};
	
	$("#cartera_listado_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		//source: GridDataAdapter,
		enabletooltips: true,
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		autoheight: true,
		showfilterrow: true,
		filterable: true,
		editable: false,
		columns:
		[
			{
				text: 'ID del Cliente',
				datafield: 'ClienteID',
				width: "10%",
				height: 20,
			},
			{
				text: 'Nombre del Cliente',
				datafield: 'Nombre',
				//displayfield: 'Nombre',
				editable: false,
				width: "30%",
				height: 20,
			},
			{
				text: 'Deuda Actual',
				datafield: 'Deuda',
				width: "25%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{
				text: 'Compras Totales',
				datafield: 'Compra',
				width: "25%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{
				text: 'Ultima Compra',
				datafield: 'Fecha',
				editable: false,
				width: "10%",
				height: 20,
				cellsformat: 'dd-MMM-yyyy',
				filtertype: 'date',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
		]
	});
	$("#cartera_listado_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#cartera_listado_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "ClienteID")
		{
			ClienteID = value;
			$("#Cartera_Listado_Movs_Window").jqxWindow('open');
		}
	});
	
	// ------------------------------------------ WINDOWS
	//--- Deudas del Cliente
	$("#Cartera_Listado_Movs_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: "100%",
		width: "100%",
		maxWidth: 925,
		maxHeight: 500,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#cartera_listado_movs_grid").jqxGrid({
				theme: mytheme,
				width: 900,
				autoheight: true,
				enabletooltips: true,
				pagesizeoptions: ['10', '20', '30', '50', '100'],
				pagesize: 20,
				sortable: true,
				pageable: true,
				editable: false,
				columns:
				[
					{
						text: 'Fecha',
						datafield: 'Fecha',
						editable: false,
						width: "10%",
						height: 20,
						filtertype: 'date',
						cellsformat: 'dd-MMM-yyyy',
						createeditor: function (row, cellvalue, editor) {
							editor.jqxDateTimeInput({ culture: 'es-ES' });
						}
					},
					{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> Interno', datafield: 'Interno', editable: false, width: "13%", height: 20 },
					{ text: 'Remision', datafield: 'Remision', editable: false, width: "10%", height: 20 },
					{ text: 'Factura', datafield: 'Factura', editable: false, width: "11%", height: 20 },
					{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> R. Caja', datafield: 'Caja_Recibo', editable: false, width: "10%", height: 20 },
					{ text: '', datafield: 'Caja_Interno', editable: false, width: "0%", height: 20 },
					{
						text: 'Valor',
						datafield: 'Valor',
						width: "17%",
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
						text: 'Saldo',
						datafield: 'Saldo',
						width: "17%",
						height: 20,
						cellsformat: 'c2',
						cellsalign: 'right',
						columntype: 'numberinput',
						editable: false,
						createeditor: function (row, cellvalue, editor) {
							editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
						}
					},
					{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: "6%", height: 20 },
					{ text: 'Apr.', datafield: 'AutorizadorID', editable: false, width: "6%", height: 20 },
				]
			});
			$("#cartera_listado_movs_grid").jqxGrid('hidecolumn', 'Caja_Interno');
			$("#cartera_listado_movs_grid").on('celldoubleclick', function (event)
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
						$("#Cartera_Listado_Remision_Window").jqxWindow('open');
				}
				else if (datafield == "Caja_Recibo")
				{
					Interno = $("#cartera_listado_movs_grid").jqxGrid('getcellvalue', rowBoundIndex, "Caja_Interno");
					if (Interno != "")
						$("#Cartera_Listado_Caja_Window").jqxWindow('open');
				}
			});
		}
	});
	$("#Cartera_Listado_Movs_Window").on('open', function (event)
	{
		var MovsSource =
		{
			datatype: "json",
			datafields:
			[
				{ name: 'Fecha', type: 'date' },
				{ name: 'Interno', type: 'string' },
				{ name: 'Remision', type: 'string' },
				{ name: 'Factura', type: 'string' },
				{ name: 'Caja_Recibo', type: 'string' },
				{ name: 'Caja_Interno', type: 'string' },
				{ name: 'Valor', type: 'decimal' },
				{ name: 'Saldo', type: 'decimal' },
				{ name: 'DigitadorID', type: 'string' },
				{ name: 'AutorizadorID', type: 'string' },
			],
			data: {
				"Cartera_Movimientos":true,
				"ClienteID":ClienteID,
				"TipoMovimiento":"Debito",
				"Fecha_Ini":"0000-00-00",
				"Fecha_Fin":GetFormattedDate(new Date()),
				"Estado":"Aprobado",
			},
			url: "modulos/datos.php",
		};
		var MovsDataAdapter = new $.jqx.dataAdapter(MovsSource);
		$("#cartera_listado_movs_grid").jqxGrid({source: MovsDataAdapter});
	});
	$("#Cartera_Listado_Movs_Window").on('close', function (event)
	{
		$("#cartera_listado_movs_grid").jqxGrid('clear');
	});
	
	//--- Remision
	$("#Cartera_Listado_Remision_Window").jqxWindow({
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
	$("#Cartera_Listado_Remision_Window").on('open', function (event)
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
				$("#Cartera_Listado_Remision_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	//--- Caja Recibo
	$("#Cartera_Listado_Caja_Window").jqxWindow({
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
	$("#Cartera_Listado_Caja_Window").on('open', function (event)
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
				$("#Cartera_Listado_Caja_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	LoadValues();
	CheckRefresh();
});
</script>
<div id="Cartera_Listado_Movs_Window">
	<div id="Cartera_Listado_Movs_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 360px;">Movimientos Cartera</div>
	</div>
	<div id="Cartera_Listado_Movs_Content<?php echo $NumOfID ?>" class="WindowContainer">
		<div id="cartera_listado_movs_grid"></div>
	</div>
</div>
<div id="Cartera_Listado_Remision_Window">
	<div id="Cartera_Listado_Remision_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Cartera_Listado_Remision_Content" class="WindowContainer">
	</div>
</div>
<div id="Cartera_Listado_Caja_Window">
	<div id="Cartera_Listado_Caja_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Recibo de CAJA</div>
	</div>
	<div id="Cartera_Listado_Caja_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Cliente
			</td>
			<td>
				<div id="cartera_listado_cliente"></div>
			</td>
			<td>
				Cliente ID
			</td>
			<td>
				<div id="cartera_listado_cliente_ID"></div>
			</td>
		</tr>
		<tr>
			<td>
				Deuda
			</td>
			<td>
				<div id="cartera_listado_deuda"></div>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				<input type="button" id="cartera_listado_export" value="Export"/>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="cartera_listado_items_grid"></div>
</div>

