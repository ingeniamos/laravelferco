<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES 
	var OldState = "";
	var NewState = "";
	var Timer1 = 0;
	var Timer2 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Inventario_Content");
	var Body = document.getElementById("Inventario_Existencias_Content");
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
				SystemMap("Existencias", true);
				
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
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Existencias" && $data[$i]["Imprimir"] == "true")
				{
		?>
					Imprimir = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Existencias" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
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
	
	$("#inventario_existencias_categoria").jqxComboBox(
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
	$("#inventario_existencias_categoria").on("change", function (event)
	{
		if (event.args)
		{
			GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
			GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#inventario_existencias_grupo").jqxComboBox({source: GrupoAdapter});
		}
		else
		{
			$("#inventario_existencias_categoria").jqxComboBox('clearSelection');
			$("#inventario_existencias_grupo").jqxComboBox('clearSelection');
			$("#inventario_existencias_grupo").jqxComboBox('clear');
			$("#inventario_existencias_subgrupo").jqxComboBox('clearSelection');
			$("#inventario_existencias_subgrupo").jqxComboBox('clear');
		}
		LoadValues();
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
	
	$("#inventario_existencias_grupo").jqxComboBox(
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
	$("#inventario_existencias_grupo").bind('change', function (event) {
		if (event.args)
		{
			SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
			SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#inventario_existencias_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
		}
		else
		{
			$("#inventario_existencias_grupo").jqxComboBox('clearSelection');
			$("#inventario_existencias_subgrupo").jqxComboBox('clearSelection');
			$("#inventario_existencias_subgrupo").jqxComboBox('clear');
		}
		LoadValues();
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
	
	$("#inventario_existencias_subgrupo").jqxComboBox(
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
	$("#inventario_existencias_subgrupo").bind('change', function (event) {
		if (!event.args)
			$("#inventario_existencias_subgrupo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	$("#inventario_existencias_imprimir1").jqxButton({
		width: 100,
		template: "warning"
	});
	$("#inventario_existencias_imprimir1").bind('click', function ()
	{
		if (!Admin && !Supervisor && !Imprimir)
			return;
		var data = "";
		data += "imprimir/inventario_existencias.php?Categoria="+$("#inventario_existencias_categoria").val()+"";
		data += "&Grupo="+$("#inventario_existencias_grupo").val()+"&SubGrupo="+$("#inventario_existencias_subgrupo").val()+"";
		//data += "&Codigo="+$("#inventario_listado_codigo").val()+"";
		//alert(data);
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#inventario_existencias_imprimir2").jqxButton({
		width: 100,
		template: "warning"
	});
	$("#inventario_existencias_imprimir2").bind('click', function ()
	{
		if (!Admin && !Supervisor)
			return;
		var data = "imprimir/inventario_existencias.php?OrderBy=Categoria";
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#inventario_existencias_imprimir3").jqxButton({
		width: 100,
		template: "warning"
	});
	$("#inventario_existencias_imprimir3").bind('click', function ()
	{
		if (!Admin && !Supervisor)
			return;
		var data = "imprimir/inventario_existencias.php?OrderBy=Grupo";
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#inventario_existencias_imprimir4").jqxButton({
		width: 100,
		template: "warning"
	});
	$("#inventario_existencias_imprimir4").bind('click', function ()
	{
		if (!Admin && !Supervisor)
			return;
		var data = "imprimir/inventario_existencias.php?OrderBy=SubGrupo";
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{
			clearTimeout(Timer1);
			
			GridSource.data = {
				"Inventario_Existencias":true,
				"Categoria":$("#inventario_existencias_categoria").val(),
				"Grupo":$("#inventario_existencias_grupo").val(),
				"SubGrupo":$("#inventario_existencias_subgrupo").val()
			};
			
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#inventario_existencias_items_grid").jqxGrid({source: GridDataAdapter});
			
		},350);
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Existencia', type: 'decimal' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Tipo', type: 'string' },
			{ name: 'Costo', type: 'decimal' },
			{ name: 'Ult_Costo', type: 'decimal' },
			{ name: 'Costo_Prom', type: 'decimal' },
			{ name: 'Venta_Prom', type: 'decimal' },
			{ name: 'Lista1', type: 'decimal' },
			{ name: 'Lista2', type: 'decimal' },
			{ name: 'Lista3', type: 'decimal' },
			{ name: 'Lista4', type: 'decimal' },
		],
		url: "modulos/datos_productos.php",
		async: true,
	};
	
	$("#inventario_existencias_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#inventario_existencias_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: false,
		columns:
		[
			{ text: 'CodFab', datafield: 'Codigo', width: 90, height: 20, pinned: true, },
			{ text: 'Nombre', datafield: 'Nombre', width: 180, height: 20, pinned: true, },
			{
				text: 'Existencia',
				datafield: 'Existencia',
				width: 80,
				height: 20,
				pinned: true,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput', 
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Ultimo Mov.',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: 90,
				height: 20,
				pinned: true,
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Tipo de Mov.', datafield: 'Tipo', width: 120, height: 20 },
			{
				text: 'Costo',
				datafield: 'Costo',
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
				text: 'Venta Prom',
				datafield: 'Venta_Prom',
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
				text: 'Lista1',
				datafield: 'Lista1',
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
				text: 'Lista2',
				datafield: 'Lista2',
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
				text: 'Lista3',
				datafield: 'Lista3',
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
				text: 'Lista4',
				datafield: 'Lista4',
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
	
	if (Admin == false && Supervisor == false)
	{
		$("#inventario_existencias_items_grid").jqxGrid('hidecolumn', 'Costo');
		$("#inventario_existencias_items_grid").jqxGrid('hidecolumn', 'Ult_Costo');
		$("#inventario_existencias_items_grid").jqxGrid('hidecolumn', 'Costo_Prom');
		$("#inventario_existencias_items_grid").jqxGrid('hidecolumn', 'Venta_Prom');
		
		$("#inventario_existencias_imprimir2").jqxButton({ disabled: true });
		$("#inventario_existencias_imprimir3").jqxButton({ disabled: true });
		$("#inventario_existencias_imprimir4").jqxButton({ disabled: true });
		
		//$("#inventario_existencias_imprimir2").jqxButton({ disabled: true });
		
		if (!Imprimir)
		{
			$("#inventario_existencias_imprimir1").jqxButton({ disabled: true });
		}
	}
	
	
	
	// Load Initial Values!
	LoadValues();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Categoria
			</td>
			<td>
				<div id="inventario_existencias_categoria"></div>
			</td>
			<td>
				Grupo
			</td>
			<td>
				<div id="inventario_existencias_grupo"></div>
			</td>
			<td>
				SubGrupo
			</td>
			<td>
				<div id="inventario_existencias_subgrupo"></div>
			</td>
			<td>
				<input type="button" id="inventario_existencias_imprimir1" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="inventario_existencias_imprimir2" value="Por Categoria"/>
			</td>
			<td>
				<input type="button" id="inventario_existencias_imprimir3" value="Por Grupo"/>
			</td>
			<td>
				<input type="button" id="inventario_existencias_imprimir4" value="Por SubGrupo"/>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="inventario_existencias_items_grid"></div>
</div>
