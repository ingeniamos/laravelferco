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
	var Main = document.getElementById("CxP_Content");
	var Body = document.getElementById("CxP_Listado_Content");
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
						$("#cxp_listado_imprimir").jqxButton({ disabled: true });
						$("#cxp_listado_export").jqxButton({ disabled: true });
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
	
	$("#cxp_listado_cliente").jqxComboBox(
	{
		width: 300,
		height: 20,
		theme: mytheme,
		source: ClienteArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Proveedor',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#cxp_listado_cliente").bind('change', function (event) {
		if (event.args)
		{
			$("#cxp_listado_cliente_ID").val(event.args.item.value);
		}

	});
	
	$("#cxp_listado_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		theme: mytheme,
		source: ClienteArray,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#cxp_listado_cliente_ID").bind('change', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (event.args.item.value != $("#cxp_listado_cliente").val())
					$("#cxp_listado_cliente").val(event.args.item.value);
				
				$.ajax({
					dataType: 'text',
					url: "modulos/datos.php",
					data: {
						"CxP_Movimientos_Deuda":true,
						"ClienteID":event.args.item.value,
					},
					async: true,
					success: function (data) {
						if (data != "")
							$("#cxp_listado_deuda").val(data);
						else
							$("#cxp_listado_deuda").val("0");
					},
					error: function (jqXHR, textStatus, errorThrown) {
						Alerts_Box("Ocurrio un Error al intentar retirar los datos!<br />Intente luego de unos segundos...", 3);
					}
				});
				
			},350);
		}
		else
		{
			$("#cxp_listado_cliente").jqxComboBox('clearSelection');
			$("#cxp_listado_cliente_ID").jqxComboBox('clearSelection');
			$("#cxp_listado_deuda").val("0");
		}
	});
	
	$("#cxp_listado_deuda").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 300,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 21
	});
	
	$("#cxp_listado_imprimir").jqxButton({
		width: 100,
		template: "warning"
	});
	$("#cxp_listado_imprimir").bind('click', function ()
	{
		window.open("imprimir/cxp_listado.php?ClienteID="+$("#cxp_listado_cliente_ID").val(), "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	$("#cxp_listado_export").jqxButton({
		width: 100,
		template: "success"
	});
	$("#cxp_listado_export").bind('click', function ()
	{
		window.location = "modulos/export_xls.php?CxP_Listado=true&ClienteID="+$("#cxp_listado_cliente_ID").val();
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
			GridSource.data = { "CxP_Listado":true };
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#cxp_listado_items_grid").jqxGrid({ source: GridDataAdapter });
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
		url: "modulos/datos.php",
	};
	
	$("#cxp_listado_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
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
				text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>ID Proveedor',
				datafield: 'ClienteID',
				width: "10%",
				height: 20,
			},
			{
				text: 'Proveedor',
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
	$("#cxp_listado_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#cxp_listado_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "ClienteID")
		{
			ClienteID = value;
			$("#CxP_Listado_Movs_Window").jqxWindow('open');
		}
	});
	
	// ------------------------------------------ WINDOWS
	//--- Deudas del Proveedor
	$("#CxP_Listado_Movs_Window").jqxWindow({
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
			$("#cxp_listado_movs_grid").jqxGrid({
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
					{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Interno', datafield: 'Compra_Interno', editable: false, width: "14%", height: 20 },
					{ text: 'Entrada', datafield: 'Compra_Entrada', editable: false, width: "8%", height: 20 },
					{ text: 'Factura', datafield: 'Factura', editable: false, width: "11%", height: 20 },
					{ text: 'Doc. Transp.', datafield: 'Doc_Transp', editable: false, width: "11%", height: 20 },
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
					{ text: 'Aut.', datafield: 'AutorizadorID', editable: false, width: "6%", height: 20 },
				]
			});
			$("#cxp_listado_movs_grid").on('celldoubleclick', function (event)
			{
				var args = event.args;
				var datafield = event.args.datafield;
				var rowBoundIndex = args.rowindex;
				var value = args.value;
				var oldvalue = args.oldvalue;
				
				if (datafield == "Compra_Interno")
				{
					Interno = value;
					$("#CxP_Listado_Compra_Window").jqxWindow('open');
					/*if (Interno.indexOf("OR") < 0)
						$("#CxP_Movimientos_Compra_Window").jqxWindow('open');
					else
						$("#CxP_Movimientos_Maquinaria_Window").jqxWindow('open');*/
				}
			});
		}
	});
	$("#CxP_Listado_Movs_Window").on('open', function (event)
	{
		var MovsSource =
		{
			datatype: "json",
			datafields:
			[
				{ name: 'Fecha', type: 'date' },
				{ name: 'Compra_Interno', type: 'string' },
				{ name: 'Compra_Entrada', type: 'string' },
				{ name: 'Factura', type: 'string' },
				{ name: 'Doc_Transp', type: 'string' },
				{ name: 'Valor', type: 'decimal' },
				{ name: 'Saldo', type: 'decimal' },
				{ name: 'DigitadorID', type: 'string' },
				{ name: 'AutorizadorID', type: 'string' },
			],
			data: {
				"CxP_Movimientos":true,
				"ClienteID":ClienteID,
				"TipoMovimiento":"Compra",
				"Fecha_Ini":"0000-00-00",
				"Fecha_Fin":GetFormattedDate(new Date()),
				"Estado":"Aprobado",
			},
			url: "modulos/datos.php",
		};
		var MovsDataAdapter = new $.jqx.dataAdapter(MovsSource);
		$("#cxp_listado_movs_grid").jqxGrid({source: MovsDataAdapter});
	});
	$("#CxP_Listado_Movs_Window").on('close', function (event)
	{
		$("#cxp_listado_movs_grid").jqxGrid('clear');
	});
	
	//--- Compra Interno
	$("#CxP_Listado_Compra_Window").jqxWindow({
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
	$("#CxP_Listado_Compra_Window").on('open', function (event)
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
				$("#CxP_Listado_Compra_Content").html(data);
			},
			complete: function()
			{
				$("#Loading").hide();
			}
		});
	});
	
	LoadValues();
	CheckRefresh();
});
</script>
<div id="CxP_Listado_Movs_Window">
	<div id="CxP_Listado_Movs_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 380px;">Movimientos CxP</div>
	</div>
	<div id="CxP_Listado_Movs_Content<?php echo $NumOfID ?>" class="WindowContainer">
		<div id="cxp_listado_movs_grid"></div>
	</div>
</div>
<div id="CxP_Listado_Compra_Window">
	<div id="CxP_Listado_Compra_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 420px;">Ver Compra</div>
	</div>
	<div id="CxP_Listado_Compra_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Proveedor
			</td>
			<td>
				<div id="cxp_listado_cliente"></div>
			</td>
			<td>
				Proveedor ID
			</td>
			<td>
				<div id="cxp_listado_cliente_ID"></div>
			</td>
		</tr>
		<tr>
			<td>
				Deuda
			</td>
			<td>
				<div id="cxp_listado_deuda"></div>
			</td>
			<td colspan="2">
				<li style="margin-right:12px;">
					<input type="button" id="cxp_listado_imprimir" value="Imprimir"/>
				</li>
				<li>
					<input type="button" id="cxp_listado_export" value="Export"/>
				</li>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="cxp_listado_items_grid"></div>
</div>

