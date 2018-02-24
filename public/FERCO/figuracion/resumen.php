<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Interno = "";
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Figuracion_Content");
	var Body = document.getElementById("Figuracion_Resumen_Content");
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
				SystemMap("Resumen", true);
				LoadParameters();
				//---
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
	var Imprimir = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador")
		{
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Resumen" && $data[$i]["Imprimir"] == "true")
				{
		?>
					Imprimir = true;
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
	
	function LoadParameters()
	{
		CartillasSource.data = {"Figuracion_Cartillas":true, "Type":"Visualizar"};
		var CartillasDataAdapter = new $.jqx.dataAdapter(CartillasSource);
		$("#figuracion_resumen_interno").jqxComboBox({source: CartillasDataAdapter});
		
		$("#figuracion_resumen_items_grid").jqxGrid("updatebounddata");
	}
	
	var CartillasSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Interno", type: "string"},
		],
		url: "modulos/datos.php",
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: "Imprimir", type: "bool" },
			{ name: "ID", type: "string" },
			{ name: "Ticket", type: "string" },
			{ name: "Interno", type: "string" },
			{ name: "Figura", type: "string" },
			{ name: "Codigo", type: "string" },
			{ name: "Nombre", type: "string" },
			{ name: "Detalle", type: "string" },
			{ name: "Total", type: "decimal" },
			{ name: "Cantidad", type: "decimal" },
		],
		url: "modulos/datos.php",
	};
	
	function LoadValues(Interno)
	{
		GridSource.data = {"Figuracion_Tickets_Resumen":Interno};
		var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
		$("#figuracion_resumen_items_grid").jqxGrid({source: GridDataAdapter});
	}
	
	$("#figuracion_resumen_interno").jqxComboBox({
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Interno',
		valueMember: 'Interno'
	});
	$("#figuracion_resumen_interno").on("change", function (event)
	{
		if (event.args)
		{
			if (Interno == event.args.item.value)
				return;
			
			Interno = event.args.item.value;
			LoadValues(Interno);
		}
		else
		{
			var item_value = $("#figuracion_resumen_interno").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				Interno = "";
				$("#figuracion_resumen_interno").jqxComboBox("clearSelection");
				$("#figuracion_resumen_items_grid").jqxGrid("clear");
			}
			else
			{
				var value = $("#figuracion_resumen_interno").val();
				var item = $("#figuracion_resumen_interno").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					Interno = "";
					$("#figuracion_resumen_interno").jqxComboBox("clearSelection");
					$("#figuracion_resumen_items_grid").jqxGrid("clear");
				}
				else
					$("#figuracion_resumen_interno").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#figuracion_resumen_interno").bind("bindingComplete", function (event) {
		if (Interno != "")
			$("#figuracion_resumen_interno").jqxComboBox('selectItem', Interno);
	});
	
	$("#figuracion_resumen_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 800,
		autoheight: true,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: '20',
		pageable: true,
		editable: true,
		columns:
		[
			{
				text: 'Imp',
				datafield: 'Imprimir',
				width: "4%",
				height: 20,
				editable: Admin ? true:Imprimir,
				columntype: 'checkbox',
				renderer: function()
				{
					return "<div title=\"Seleccionar Todos para Imprimir?\" style=\"margin: 5px 0px 0px 5px;\"></div>";
				},
				rendered: function (element)
				{
					$(element).jqxCheckBox({ theme: mytheme, width: 16, height: 16, animationShowDelay: 0, animationHideDelay: 0 });
					columnCheckBox = $(element);
					$(element).on("change", function (event)
					{
						var checked = event.args.checked;
						var pageinfo = $("#figuracion_resumen_items_grid").jqxGrid("getpaginginformation");
						var pagenum = pageinfo.pagenum;
						var pagesize = pageinfo.pagesize;
						
						if (checked == null)
						{
							return false;
						}
						
						$("#figuracion_resumen_items_grid").jqxGrid("beginupdate");

						// select all rows when the column's checkbox is checked.
						if (checked)
						{
							$("#figuracion_resumen_items_grid").jqxGrid("selectallrows");
						}
						// unselect all rows when the column's checkbox is checked.
						else if (checked == false)
						{
							$("#figuracion_resumen_items_grid").jqxGrid("clearselection");
						}
						
						// update cells values.
						/*var startrow = pagenum * pagesize;
						for (var i = startrow; i < startrow + pagesize; i++)
						{
							// The bound index represents the row's unique index. 
							// Ex: If you have rows A, B and C with bound indexes 0, 1 and 2, afer sorting, the Grid will display C, B, A i.e the C's bound index will be 2, but its visible index will be 0.
							// The code below gets the bound index of the displayed row and updates the value of the row's Imprimir column.
							var boundindex = $("#reportes_carnet_items_grid").jqxGrid("getrowboundindex", i);
							$("#reportes_carnet_items_grid").jqxGrid("setcellvalue", boundindex, "Imprimir", event.args.checked);
						}*/
						var datainfo = $("#figuracion_resumen_items_grid").jqxGrid("getdatainformation");
						var count = datainfo.rowscount;
						for (var i = 0; i < count; i++)
						{
							// The bound index represents the row's unique index. 
							// Ex: If you have rows A, B and C with bound indexes 0, 1 and 2, afer sorting, the Grid will display C, B, A i.e the C's bound index will be 2, but its visible index will be 0.
							// The code below gets the bound index of the displayed row and updates the value of the row's Imprimir column.
							var boundindex = $("#figuracion_resumen_items_grid").jqxGrid("getrowboundindex", i);
							$("#figuracion_resumen_items_grid").jqxGrid("setcellvalue", boundindex, "Imprimir", event.args.checked);
						}

						$("#figuracion_resumen_items_grid").jqxGrid("endupdate");
					});
					return true;
				}
			},
			{ text: '', datafield: 'ID', width: "0%", editable: false, },
			{ text: '', datafield: 'Ticket', width: "0%", editable: false, },
			{ text: 'Ticket #', datafield: 'Interno', width: "11%", editable: false },
			{ text: '', datafield: 'Figura', width: "0%", editable: false },
			{ text: '', datafield: 'Codigo', width: "0%", editable: false },
			{ text: 'Producto', datafield: 'Nombre', width: "25%", editable: false },
			{ text: 'Detalle', datafield: 'Detalle', width: "40%", editable: false },
			{ text: 'Total', datafield: 'Total', width: "10%", cellsalign: 'right', editable: false, },
			{ text: 'Can/Emp', datafield: 'Cantidad', width: "10%", cellsalign: 'right', editable: false, },
		]
	});
	$("#figuracion_resumen_items_grid").jqxGrid("hidecolumn", "ID");
	$("#figuracion_resumen_items_grid").jqxGrid("hidecolumn", "Ticket");
	$("#figuracion_resumen_items_grid").jqxGrid("hidecolumn", "Figura");
	$("#figuracion_resumen_items_grid").jqxGrid("hidecolumn", "Codigo");
	$("#figuracion_resumen_items_grid").jqxGrid('localizestrings', localizationobj);
	
	$("#figuracion_resumen_imprimir1").jqxButton({
		theme: mytheme,
		width: 130,
		height: 25,
		template: "warning"
	});
	$("#figuracion_resumen_imprimir1").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		
		var datinfo = $("#figuracion_resumen_items_grid").jqxGrid("getdatainformation");
		var count = datinfo.rowscount;
		var Lista = new Array();
		var a = 0;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#figuracion_resumen_items_grid").jqxGrid("getrowdata", i);
			if (currentRow.Imprimir == true)
			{
				var tmp_array = {
					"Interno":Interno,
					"Ticket":currentRow.Interno,
					"Figura":currentRow.Figura,
					"Codigo":currentRow.Codigo,
					"Nombre":currentRow.Nombre,
					"Detalle":currentRow.Detalle,
					"Cantidad":currentRow.Cantidad,
				};
				Lista[a] = tmp_array;
				a++;
			}
		}
		
		var Tmp = JSON.stringify(Lista);
		Tmp = window.btoa(unescape(encodeURIComponent(Tmp)));
		
		var URL = "imprimir/cartilla_figuracion_tickets3.php?Lista="+Tmp;
		window.open(URL, "", "width=1000, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_resumen_imprimir2").jqxButton({
		theme: mytheme,
		width: 150,
		height: 25,
		template: "warning"
	});
	$("#figuracion_resumen_imprimir2").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		
		var datinfo = $("#figuracion_resumen_items_grid").jqxGrid("getdatainformation");
		var count = datinfo.rowscount;
		var Lista = new Array();
		var a = 0;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#figuracion_resumen_items_grid").jqxGrid("getrowdata", i);
			if (currentRow.Imprimir == true)
			{
				var tmp_array = {
					"Interno":Interno,
					"Ticket":currentRow.Interno,
					"Figura":currentRow.Figura,
					"Codigo":currentRow.Codigo,
					"Nombre":currentRow.Nombre,
					"Detalle":currentRow.Detalle,
					"Cantidad":currentRow.Cantidad,
				};
				Lista[a] = tmp_array;
				a++;
			}
		}
		
		var Tmp = JSON.stringify(Lista);
		Tmp = window.btoa(unescape(encodeURIComponent(Tmp)));
		
		var URL = "imprimir/cartilla_figuracion_tickets_resumen.php?Lista="+Tmp;
		window.open(URL, "", "width=1000, height=600, menubar=no, titlebar=no");
	});
	
	if (!Admin && !Imprimir)
	{
		$("#figuracion_resumen_imprimir1").jqxButton({ disabled: true });
		$("#figuracion_resumen_imprimir2").jqxButton({ disabled: true });
	}
	
	LoadParameters();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Cartilla
			</td>
		</tr>
		<tr>
			<td>
				<div id="figuracion_resumen_interno"></div>
			</td>
		</tr>
	</table>
	<div id="figuracion_resumen_items_grid"></div>
	<table cellpadding="2" cellspacing="1" style="margin:20px 0px;">
		<tr>
			<td>
				<input type="button" id="figuracion_resumen_imprimir1" value="Imprimir Tickets" title="Imprimir los Tickets Seleccionados... Estos se ordenan de menor a mayor."/>
			</td>
			<td>
				<input type="button" id="figuracion_resumen_imprimir2" value="Imprimir Resumen" title="Imprimir Resumen de los Tickets Seleccionados... Estos se ordenan de menor a mayor."/>
			</td>
		</tr>
	</table>
</div>