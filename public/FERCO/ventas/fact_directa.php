<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var Locked = false;
	var mytheme = "energyblue";
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Ventas_Content");
	var Body = document.getElementById("Fact_Directa_Content");
	var Times = 0;
	var Refresh = 0;
	var Hide = 0;
	
	function CheckRefresh ()
	{
		clearInterval(Refresh);
		Refresh = setInterval(function()
		{
			if (Times <= 0 && (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none"))
				Times++;
			else if (Times > 0 && jQuery.css( Body, "display" ) === "block" && jQuery.css( Main, "display" ) === "block")
			{
				//Code... do something?
				SystemMap("Facturación Directa", true);
				
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
			if (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none")
			{
				CheckRefresh();
				clearInterval(Hide);
			}
		},200);
	};
	// END - Code for Refresh Data
	
	//---
	var Guardar = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Fact_Directa" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
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
	
	//-------------------------------------------------------------------------------------------------//
	//------------------------------------------- KEY JUMPS -------------------------------------------//
	//-------------------------------------------------------------------------------------------------//
	$("#ventas_fact_directa_cliente_ID").keyup(function(event)
	{
		if(event.which == 13)
			$("#ventas_fact_directa_cliente").jqxComboBox('focus');
	});
	$("#ventas_fact_directa_cliente").keyup(function(event)
	{
		if(event.which == 13)
			$("#ventas_fact_directa_addrow").jqxButton('focus');
	});
	/*$("#ventas_fact_directa_addrow").keyup(function(event)
	{
		if(event.which == 13)
			AddRow();
	});*/
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
		async: true
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
	
	$("#ventas_fact_directa_cliente_ID").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#ventas_fact_directa_cliente_ID").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#ventas_fact_directa_cliente").val() != event.args.item.value)
				$("#ventas_fact_directa_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var value = $("#ventas_fact_directa_cliente_ID").val();
			var item = $("#ventas_fact_directa_cliente_ID").jqxComboBox('getItemByValue', value);
			if (item == undefined)
			{
				$("#ventas_fact_directa_cliente_ID").jqxComboBox('clearSelection');
				$("#ventas_fact_directa_cliente").jqxComboBox('clearSelection');
			}
			else
				$("#ventas_fact_directa_cliente_ID").jqxComboBox('selectItem', item.value);
		}
	});
	
	$("#ventas_fact_directa_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#ventas_fact_directa_cliente").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#ventas_fact_directa_cliente_ID").val() != event.args.item.value)
				$("#ventas_fact_directa_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#ventas_fact_directa_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#ventas_fact_directa_cliente_ID").jqxComboBox('clearSelection');
				$("#ventas_fact_directa_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#ventas_fact_directa_cliente").val();
				
				var item = $("#ventas_fact_directa_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#ventas_fact_directa_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#ventas_fact_directa_cliente_ID").jqxComboBox('clearSelection');
				$("#ventas_fact_directa_cliente").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#ventas_fact_directa_addrow").jqxButton({
		width: 80,
		height: 25,
		template: "success"
	});
	$("#ventas_fact_directa_addrow").on('click', function ()
	{
		AddRow()
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
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
	
	function AddRow()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var Cliente = $("#ventas_fact_directa_cliente").jqxComboBox('getSelectedItem');
		
		if (!Cliente)
		{
			Alerts_Box("Favor Seleccionar un Cliente.", 3);
			WaitClick_Combobox("ventas_fact_directa_cliente");
			Locked = false;
			return;
		}
		
		var datarow = [{
			"ClienteID":Cliente.value,
			"Nombre":Cliente.label,
			"Fecha":new Date(Year, Month, Day),
			"Remision":"",
			"Factura":"",
			"Valor":0,
			"VendedorID":"",
			"CobradorID":"",
		}];
		
		$("#ventas_fact_directa_items_grid").jqxGrid("addrow", null, datarow, "first");
		$("#ventas_fact_directa_cliente_ID").jqxComboBox('clearSelection');
		$("#ventas_fact_directa_cliente").jqxComboBox('clearSelection');
		Locked = false;
	}
	
	var MainSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ClienteID', type: 'string'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'Fecha', type: 'string'},
			{ name: 'Remision', type: 'string'},
			{ name: 'Factura', type: 'decimal'},
			{ name: 'Valor', type: 'decimal'},
			{ name: 'VendedorID', type: 'string'},
			{ name: 'CobradorID', type: 'string'},
		],
	};
	var MainDataAdapter = new $.jqx.dataAdapter(MainSource);
	
	$("#ventas_fact_directa_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 300,
		source: MainDataAdapter,
		enabletooltips: true,
		enablebrowserselection: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '3%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#ventas_fact_directa_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#ventas_fact_directa_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#ventas_fact_directa_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#ventas_fact_directa_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'ID', datafield: 'ClienteID', editable: false, width: '11%', height: 20 },
			{ text: 'Nombre', datafield: 'Nombre', editable: false, width: '28%', height: 20 },
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: '10%',
				height: 20,
				editable: Admin,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Remision', datafield: 'Remision', editable: true, width: '10%', height: 20, cellsalign: 'center' },
			{ text: 'Factura', datafield: 'Factura', editable: true, width: '10%', height: 20, cellsalign: 'center' },
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: true,
				width: '18%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18, spinButtons: false });
				}
			},
			{
				text: 'Vend.', datafield: 'VendedorID', columntype: 'combobox', width: '5%', height: 20, editable: true,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: VendedorDataAdapter,
						selectedIndex: -1,
						displayMember: 'Codigo',
						valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Cobr.', datafield: 'CobradorID', columntype: 'combobox', width: '5%', height: 20, editable: true,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: VendedorDataAdapter,
						selectedIndex: -1,
						displayMember: 'Codigo',
						valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
		],
	});
	//$("#ventas_fact_directa_items_grid").jqxGrid('hidecolumn', 'ID');
	$("#ventas_fact_directa_items_grid").jqxGrid('localizestrings', localizationobj);
	
	function SaveData()
	{
		if (Locked)
			return;
		
		Locked = true;
		var Items = new Array();
		
		var datinfo = $("#ventas_fact_directa_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		if (count < 1)
		{
			Alerts_Box("Debe Ingresar al Menos un Cliente.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (var i = 0; i < count; i++)
		{
			var tmp_array = {};
			var currentRow = $("#ventas_fact_directa_items_grid").jqxGrid('getrowdata', i);
			
			if (currentRow.Valor < 1)
			{
				Alerts_Box("Uno más Valores, posee Valor de Facturación = \"0\"", 3);
				WaitClick();
				Locked = false;
				return;
			}
			
			if (currentRow.VendedorID == "")
			{
				Alerts_Box("Uno más Valores, no posee Vendedor.", 3);
				WaitClick();
				Locked = false;
				return;
			}
			
			if (currentRow.CobradorID == "")
			{
				Alerts_Box("Uno más Valores, no posee Cobrador.", 3);
				WaitClick();
				Locked = false;
				return;
			}
			
			tmp_array["ClienteID"] = currentRow.ClienteID;
			tmp_array["Nombre"] = currentRow.Nombre;
			tmp_array["Fecha"] = GetFormattedDate(currentRow.Fecha);
			tmp_array["Remision"] = currentRow.Remision;
			tmp_array["Factura"] = currentRow.Factura;
			tmp_array["Valor"] = currentRow.Valor;
			tmp_array["VendedorID"] = currentRow.VendedorID;
			tmp_array["CobradorID"] = currentRow.CobradorID;
			
			Items[i] = tmp_array;
		}
		
		/*alert(JSON.stringify(Items))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: {"Ventas_Fact_Directa":Items},
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				/*alert(data);
				return*/
				Alerts_Box("Datos Guardados con Exito!", 2);
				$("#ventas_fact_directa_items_grid").jqxGrid('clear');
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!<br />Intente luego de unos segundos...", 3);
			}
		});
	}
	
	$("#ventas_fact_directa_guardar").jqxButton({
		width: 150,
		height: 25,
		template: "info"
	});
	$("#ventas_fact_directa_guardar").on('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		SaveData()
	});
	
	if (!Admin && !Guardar)
	{
		$("#ventas_fact_directa_guardar").jqxButton({ disabled: true });
	}
	
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="0" cellspacing="0" style="border: 1px solid #A4BED4; margin-bottom:10px; text-align:center;">
		<tr style="background: #E0E9F5">
			<td style="border-bottom: 1px solid #A4BED4; padding:3px 0px;">
				ID
			</td>
			<td style="border-bottom: 1px solid #A4BED4; padding:3px 0px;">
				Nombre
			</td>
			<td style="border-bottom: 1px solid #A4BED4; padding:3px 0px;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td style="padding:3px 0px;">
				<div id="ventas_fact_directa_cliente_ID" style="margin-left:7px;"></div>
			</td>
			<td style="padding:3px 0px;">
				<div id="ventas_fact_directa_cliente" style="margin-left:7px;"></div>
			</td>
			<td style="padding:3px 0px;">
				<input type="button" id="ventas_fact_directa_addrow" value="Añadir" style="margin-left:7px; margin-right:7px;"/>
			</td>
		</tr>
	</table>
	<div id="ventas_fact_directa_items_grid"></div>
	<table cellpadding="2" cellspacing="1" style="margin-top:20px; text-align:center;">
		<tr>
			<td>
				<input type="button" id="ventas_fact_directa_guardar" value="Guardar"/>
			</td>
		</tr>
	</table>
</div>
