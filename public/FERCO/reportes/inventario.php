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
	var Body = document.getElementById("Reportes_Inventario_Content");
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
				SystemMap("Inventario", true);
				
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
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Inventario" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#reportes_inventario_imprimir1").jqxButton({ disabled: true });
					$("#reportes_inventario_imprimir2").jqxButton({ disabled: true });
		<?php
				}
			}
		}
	} ?>
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
		async: true
	};
	var ProductosDataAdapter1 = new $.jqx.dataAdapter(ProductosSource);
	var ProductosDataAdapter2 = ProductosDataAdapter1;
	
	$("#reportes_inventario_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		source: ProductosDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#reportes_inventario_producto").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_inventario_codigo").val() != event.args.item.value)
				$("#reportes_inventario_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_inventario_producto").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_inventario_codigo").jqxComboBox('clearSelection');
				$("#reportes_inventario_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_inventario_producto").val();
				
				var item = $("#reportes_inventario_producto").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_inventario_producto").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#reportes_inventario_codigo").jqxComboBox('clearSelection');
				$("#reportes_inventario_producto").jqxComboBox('clearSelection');
			}
			LoadValues();
		}
	});
	
	$("#reportes_inventario_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ProductosDataAdapter2,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#reportes_inventario_codigo").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_inventario_producto").val() != event.args.item.value)
				$("#reportes_inventario_producto").jqxComboBox('selectItem', event.args.item.value);
			
			$("#reportes_inventario_categoria").jqxComboBox('clearSelection');
			$("#reportes_inventario_grupo").jqxComboBox('clearSelection');
			$("#reportes_inventario_subgrupo").jqxComboBox('clearSelection');
		}
		else
		{
			var item_value = $("#reportes_inventario_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_inventario_codigo").jqxComboBox('clearSelection');
				$("#reportes_inventario_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_inventario_codigo").val();
				var item = $("#reportes_inventario_codigo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#reportes_inventario_codigo").jqxComboBox('clearSelection');
					$("#reportes_inventario_producto").jqxComboBox('clearSelection');
				}
				else
					$("#reportes_inventario_codigo").jqxComboBox('selectItem', item.value);
			}
		}
		LoadValues();
	});
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Categoria', type: 'string'},
		],
		data: {"Inventario_Categoria":true},
		url: "modulos/parametros.php",
		async: true
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#reportes_inventario_categoria").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: CategoriaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Categoria',
		valueMember: 'Categoria'
	});
	$("#reportes_inventario_categoria").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
				GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
				$("#reportes_inventario_grupo").jqxComboBox({source: GrupoAdapter});
				
				$("#reportes_inventario_producto").jqxComboBox('clearSelection');
				$("#reportes_inventario_codigo").jqxComboBox('clearSelection');
				
				LoadValues();
			},350);
		}
		else
		{
			$("#reportes_inventario_categoria").jqxComboBox('clearSelection');
			$("#reportes_inventario_grupo").jqxComboBox('clearSelection');
			$("#reportes_inventario_grupo").jqxComboBox('clear');
			$("#reportes_inventario_subgrupo").jqxComboBox('clearSelection');
			$("#reportes_inventario_subgrupo").jqxComboBox('clear');
			
			LoadValues();
		}
	});
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'},
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#reportes_inventario_grupo").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
	});
	$("#reportes_inventario_grupo").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
				SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				$("#reportes_inventario_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
				
				$("#reportes_inventario_producto").jqxComboBox('clearSelection');
				$("#reportes_inventario_codigo").jqxComboBox('clearSelection');
				
				LoadValues();
			},350);
		}
		else
		{
			$("#reportes_inventario_grupo").jqxComboBox('clearSelection');
			$("#reportes_inventario_subgrupo").jqxComboBox('clearSelection');
			$("#reportes_inventario_subgrupo").jqxComboBox('clear');
			
			LoadValues();
		}
	});
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo', type: 'string'},
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#reportes_inventario_subgrupo").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
	});
	$("#reportes_inventario_subgrupo").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_inventario_producto").jqxComboBox('clearSelection');
			$("#reportes_inventario_codigo").jqxComboBox('clearSelection');
		}
		else
			$("#reportes_inventario_subgrupo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	$("#reportes_inventario_imprimir1").jqxButton({
		width: 122,
		template: "warning"
	});
	$("#reportes_inventario_imprimir1").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_inventario.php?Categoria="+$("#reportes_inventario_categoria").val()+"";
		data += "&Grupo="+$("#reportes_inventario_grupo").val()+"&SubGrupo="+$("#reportes_inventario_subgrupo").val()+"";
		data += "&Codigo="+$("#reportes_inventario_codigo").val()+"";
		//alert(data);
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#reportes_inventario_imprimir2").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_inventario_imprimir2").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_inventario.php?Categoria="+$("#reportes_inventario_categoria").val()+"";
		data += "&Grupo="+$("#reportes_inventario_grupo").val()+"&SubGrupo="+$("#reportes_inventario_subgrupo").val()+"";
		data += "&Codigo="+$("#reportes_inventario_codigo").val()+"&Existencia=true";
		
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			GridSource.data = {
				"Reportes_Inventario":true,
				"Codigo":$("#reportes_inventario_codigo").val(),
				"Categoria":$("#reportes_inventario_categoria").val(),
				"Grupo":$("#reportes_inventario_grupo").val(),
				"SubGrupo":$("#reportes_inventario_subgrupo").val()
			};
			
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#reportes_inventario_items_grid").jqxGrid({source: GridDataAdapter});
			
		},500);
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			//{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Categoria', type: 'string' },
			{ name: 'Grupo', type: 'string' },
			{ name: 'SubGrupo', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'Existencia', type: 'decimal' },
			{ name: 'Existencia', type: 'decimal' },
			{ name: 'Ult_Costo', type: 'decimal' },
			{ name: 'Total_Ult_Costo', type: 'decimal' },
			{ name: 'Costo_Prom', type: 'decimal' },
			{ name: 'Total_Costo_Prom', type: 'decimal' },
		],
		url: "modulos/datos_productos.php",
		async: true,
	};
	
	$("#reportes_inventario_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#reportes_inventario_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: false,
		columns:
		[
			{ text: 'Nombre', datafield: 'Nombre', width: 180, height: 20, pinned: true, },
			{ text: 'Categoria', datafield: 'Categoria', width: 90, height: 20 },
			{ text: 'Grupo', datafield: 'Grupo', width: 90, height: 20 },
			{ text: 'SubGrupo', datafield: 'SubGrupo', width: 90, height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				columntype: 'datetimeinput',
				width: 80,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				cellsformat: 'd2',
			},
			{ text: 'UndMed', datafield: 'UndMed', width: 60, height: 20, cellsalign: 'center' },
			{
				text: 'Existencia',
				datafield: 'Existencia',
				width: 90,
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'center',
				columntype: 'numberinput', 
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Ult Costo',
				datafield: 'Ult_Costo',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Total Ult Costo',
				datafield: 'Total_Ult_Costo',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Costo Prom',
				datafield: 'Costo_Prom',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Total Costo Prom',
				datafield: 'Total_Costo_Prom',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
		]
	});
	
	// Load Initial Values!
	LoadValues();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td colspan="2">
				Producto
			</td>
			<td>
				Codigo
			</td>
			<td>
				Categoria
			</td>
			<td>
				Grupo
			</td>
			<td>
				SubGrupo
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="reportes_inventario_producto"></div>
			</td>
			<td>
				<div id="reportes_inventario_codigo"></div>
			</td>
			<td>
				<div id="reportes_inventario_categoria"></div>
			</td>
			<td>
				<div id="reportes_inventario_grupo"></div>
			</td>
			<td>
				<div id="reportes_inventario_subgrupo"></div>
			</td>
		</tr>
		<tr>
			<td colspan="5">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="reportes_inventario_imprimir1" value="Imprimir Listado"/>
			</td>
			<td>
				<input type="button" id="reportes_inventario_imprimir2" value="con Existncias"/>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="reportes_inventario_items_grid"></div>
</div>
