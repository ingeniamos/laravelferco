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
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Reportes_Content");
	var Body = document.getElementById("Reportes_Cartera_Edades_Content");
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
				SystemMap("Edades", true);
				
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
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Cartera_Edades" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Cartera_Edades" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#reportes_cartera_edades_export").jqxButton({ disabled: true });
					$("#reportes_cartera_edades_imprimir").jqxButton({ disabled: true });
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
	
	$("#reportes_cartera_edades_cliente").jqxComboBox(
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
	$("#reportes_cartera_edades_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_cartera_edades_cliente_ID").val() != event.args.item.value)
				$("#reportes_cartera_edades_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_cartera_edades_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_cartera_edades_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_cartera_edades_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_cartera_edades_cliente").val();
				
				var item = $("#reportes_cartera_edades_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_cartera_edades_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#reportes_cartera_edades_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_cartera_edades_cliente").jqxComboBox('clearSelection');
			}
			LoadValues();
		}
	});
	
	$("#reportes_cartera_edades_cliente_ID").jqxComboBox({
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
	$("#reportes_cartera_edades_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_cartera_edades_cliente").val() != event.args.item.value)
				$("#reportes_cartera_edades_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_cartera_edades_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_cartera_edades_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_cartera_edades_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_cartera_edades_cliente_ID").val();
				var item = $("#reportes_cartera_edades_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#reportes_cartera_edades_cliente_ID").jqxComboBox('clearSelection');
					$("#reportes_cartera_edades_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#reportes_cartera_edades_cliente_ID").jqxComboBox('selectItem', item.value);
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
	
	$("#reportes_cartera_edades_cobrador").jqxComboBox({
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
	$("#reportes_cartera_edades_cobrador").bind('change', function (event) {
		if (!event.args)
			$("#reportes_cartera_edades_cobrador").jqxComboBox('clearSelection');
		LoadValues();
	});
	$("#reportes_cartera_edades_cobrador").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#reportes_cartera_edades_cobrador").jqxComboBox("selectItem", "<?php echo $_SESSION["UserCode"]; ?>");
			$("#reportes_cartera_edades_cobrador").jqxComboBox({ disabled: true });
		}
	});
	
	$("#reportes_cartera_edades_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 210,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#reportes_cartera_edades_export").jqxButton({
		width: 120,
		template: "success"
	});
	$("#reportes_cartera_edades_export").bind('click', function ()
	{
		var data = "modulos/export_xls.php?Cartera_Edades=true";
		data += "&ClienteID="+$("#reportes_cartera_edades_cliente_ID").val();
		data += "&CobradorID="+$("#reportes_cartera_edades_cobrador").val();
		window.location = data;
	});
	
	$("#reportes_cartera_edades_imprimir").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_cartera_edades_imprimir").bind('click', function ()
	{
		var data = "imprimir/reportes_edades.php";
		data += "?ClienteID="+$("#reportes_cartera_edades_cliente_ID").val();
		data += "&CobradorID="+$("#reportes_cartera_edades_cobrador").val();
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_cartera_edades_update").jqxButton({
		width: 120,
		template: "info"
	});
	$("#reportes_cartera_edades_update").bind('click', function ()
	{
		$("#reportes_cartera_edades_items_grid").jqxGrid("updatebounddata");
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
				"Cartera_Edades":true,
				"ClienteID":$("#reportes_cartera_edades_cliente_ID").val(),
				"CobradorID":$("#reportes_cartera_edades_cobrador").val(),
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource, {
				autoBind: true,
				beforeLoadComplete: function (records)
				{
					if (records[0]["Total"] != undefined)
						$("#reportes_cartera_edades_total").val(records[0]["Total"]);
					else
						$("#reportes_cartera_edades_total").val("0");
				}
			});
			
			
			$("#reportes_cartera_edades_items_grid").jqxGrid({source: GridDataAdapter});
		},350);
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Cliente', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'CobradorID', type: 'string' },
			{ name: 'Deuda', type: 'decimal' },
			{ name: 'A30', type: 'decimal' },
			{ name: 'A45', type: 'decimal' },
			{ name: 'A60', type: 'decimal' },
			{ name: 'A90', type: 'decimal' },
			{ name: 'Mas90', type: 'decimal' },
			{ name: 'Total', type: 'decimal' },
		],
		url: "modulos/datos.php",
	};
	
	$("#reportes_cartera_edades_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		showfilterrow: true,
		filterable: true,
		editable: false,
		columns:
		[
			{ text: 'Nombre', datafield: 'Cliente', editable: false, width: 180, height: 20, pinned: true, filterable: true, },
			{ text: 'ID', datafield: 'ClienteID', editable: false, width: 100, height: 20, pinned: true, filterable: true, },
			{
				text: 'Total',
				datafield: 'Deuda',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				filterable: false,
			},
			{
				text: 'Corriente',
				datafield: 'A30',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				filterable: false,
			},
			{
				text: 'de 30 a 45',
				datafield: 'A45',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				filterable: false,
			},
			{
				text: 'de 45 a 60',
				datafield: 'A60',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				filterable: false,
			},
			{
				text: 'de 60 a 90',
				datafield: 'A90',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				filterable: false,
			},
			{
				text: 'Mas de 90',
				datafield: 'Mas90',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				filterable: false,
			},
			{ text: 'Cobr.', datafield: 'CobradorID', editable: false, width: 60, height: 20, filterable: true, },
		]
	});
	$("#reportes_cartera_edades_items_grid").jqxGrid('localizestrings', localizationobj);
	// Load Initial Values!
	LoadValues();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="3" cellspacing="1" style="margin:5px 0px 20px 0px;">
		<tr>
			<td style="text-align:center;">
				<div style="background: #75858A;color: #FFF;font-size: 13px;padding: 5px 5px 0px 5px; height: 25px;">
					FILTRAR POR:
				</div>
			</td>
			<td>
				Cliente
			</td>
			<td>
				<div id="reportes_cartera_edades_cliente"></div>
			</td>
			<td>
				Cliente ID
			</td>
			<td>
				<div id="reportes_cartera_edades_cliente_ID"></div>
			</td>
			<td>
				Cobrador
			</td>
			<td>
				<div id="reportes_cartera_edades_cobrador"></div>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 1000px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px; text-align:center;">
		<tr>
			<td>
				<input type="button" id="reportes_cartera_edades_export" value="Exportar"/>
			</td>
			<td>
				<input type="button" id="reportes_cartera_edades_imprimir" value="Listado"/>
			</td>
			<td>
				<input type="button" id="reportes_cartera_edades_update" value="Actualizar"/>
			</td>
			<td style="text-align:right;">
				TOTAL
			</td>
			<td colspan="2">
				<div id="reportes_cartera_edades_total"></div>
			</td>
		</tr>
	</table>
	
	<div id="reportes_cartera_edades_items_grid"></div>
</div>
