<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Timer1 = 0;
	var Timer2 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("CxP_Content");
	var Body = document.getElementById("CxP_Edades_Content");
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
						$("#cxp_edades_imprimir").jqxButton({ disabled: true });
						$("#cxp_edades_export").jqxButton({ disabled: true });
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
	
	$("#cxp_edades_cliente").jqxComboBox(
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
	$("#cxp_edades_cliente").bind('change', function (event) {
		if (!event.args)
		{
			$("#cxp_edades_cliente").jqxComboBox('clearSelection');
			$("#cxp_edades_cliente_ID").jqxComboBox('clearSelection');
			$("#cxp_edades_deuda").val("0");
		}
		else
			$("#cxp_edades_cliente_ID").val(event.args.item.value);
	});
	
	$("#cxp_edades_cliente_ID").jqxComboBox({
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
	$("#cxp_edades_cliente_ID").bind('change', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (event.args.item.value != $("#cxp_edades_cliente").val())
					$("#cxp_edades_cliente").val(event.args.item.value);
				
				$.ajax({
					dataType: 'json',
					url: "modulos/datos.php",
					data: {
						"CxP_Movimientos_Deuda":true,
						"ClienteID":event.args.item.value,
					},
					async: true,
					success: function (data) {
						if (data != "")
							$("#cxp_edades_deuda").val(data);
						else
							$("#cxp_edades_deuda").val("0");
					},
					error: function (jqXHR, textStatus, errorThrown) {
						Alerts_Box("Ocurrio un Error al intentar retirar los datos!<br />Intente luego de unos segundos...", 3);
					}
				});
				
			},350);
		}
		else
		{
			$("#cxp_edades_cliente").jqxComboBox('clearSelection');
			$("#cxp_edades_cliente_ID").jqxComboBox('clearSelection');
			$("#cxp_edades_deuda").val("0");
		}
	});
	
	$("#cxp_edades_deuda").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 300,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#cxp_edades_imprimir").jqxButton({
		width: 100,
		template: "warning"
	});
	$("#cxp_edades_imprimir").bind('click', function ()
	{
		window.open("imprimir/cxp_edades.php", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#cxp_edades_export").jqxButton({
		width: 100,
		template: "success"
	});
	$("#cxp_edades_export").bind('click', function ()
	{
		window.location = "modulos/export_xls.php?CxP_Edades=true";
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
			GridSource.data = { "CxP_Edades":true };
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#cxp_edades_items_grid").jqxGrid({ source: GridDataAdapter });
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
			{ name: 'A30', type: 'decimal' },
			{ name: 'A60', type: 'decimal' },
			{ name: 'A90', type: 'decimal' },
			{ name: 'Mas90', type: 'decimal' },
		],
		url: "modulos/datos.php",
	};
	
	$("#cxp_edades_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		sortable: true,
		pageable: true,
		pagesize: 20,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		autoheight: true,
		showfilterrow: true,
		filterable: true,
		editable: false,
		columns:
		[
			{
				text: 'ID Proveedor',
				datafield: 'ClienteID',
				width: "10%",
				height: 20,
			},
			{
				text: 'Proveedor',
				datafield: 'Nombre',
				//displayfield: 'Nombre',
				editable: false,
				width: "25%",
				height: 20,
			},
			{
				text: 'Deuda Actual',
				datafield: 'Deuda',
				width: "13%",
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
				text: '30 Dias',
				datafield: 'A30',
				width: "13%",
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
				text: '60 Dias',
				datafield: 'A60',
				width: "13%",
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
				text: '90 Dias',
				datafield: 'A90',
				width: "13%",
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
				text: 'Mas de 90 Dias',
				datafield: 'Mas90',
				width: "13%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
		]
	});
	$("#cxp_edades_items_grid").jqxGrid('localizestrings', localizationobj);
	
	LoadValues();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Proveedor
			</td>
			<td>
				<div id="cxp_edades_cliente"></div>
			</td>
			<td>
				Proveedor ID
			</td>
			<td>
				<div id="cxp_edades_cliente_ID"></div>
			</td>
		</tr>
		<tr>
			<td>
				Deuda
			</td>
			<td>
				<div id="cxp_edades_deuda"></div>
			</td>
			<td colspan="2">
				<li style="margin-right:12px;">
					<input type="button" id="cxp_edades_imprimir" value="Imprimir"/>
				</li>
				<li>
					<input type="button" id="cxp_edades_export" value="Export"/>
				</li>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="cxp_edades_items_grid"></div>
</div>

