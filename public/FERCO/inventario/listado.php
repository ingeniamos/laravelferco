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
	var Body = document.getElementById("Inventario_Listado_Content");
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
	var Supervisor = false;
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
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#inventario_listado_imprimir1").jqxButton({ disabled: true });
					$("#inventario_listado_imprimir2").jqxButton({ disabled: true });
					$("#inventario_listado_exportar1").jqxButton({ disabled: true });
					$("#inventario_listado_exportar2").jqxButton({ disabled: true });
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
	
	$("#inventario_listado_codigo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
	});
	$("#inventario_listado_codigo").on('change', function (event) 
	{
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
		async: false
	};
	var CategoriaArray = new Array();
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				CategoriaArray.push(records[i]);
			}
		}
	});
	
	$("#inventario_listado_categoria").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: CategoriaArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Categoria',
		valueMember: 'Categoria'
	});
	$("#inventario_listado_categoria").bind('change', function (event) {
		if (event.args)
		{
			GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
			GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#inventario_listado_grupo").jqxComboBox({source: GrupoAdapter});
		}
		else
		{
			$("#inventario_listado_categoria").jqxComboBox('clearSelection');
			$("#inventario_listado_grupo").jqxComboBox('clearSelection');
			$("#inventario_listado_grupo").jqxComboBox('clear');
			$("#inventario_listado_subgrupo").jqxComboBox('clearSelection');
			$("#inventario_listado_subgrupo").jqxComboBox('clear');
		}
		LoadValues();
	});
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Categoria', type: 'string'},
			{ name: 'Grupo', type: 'string'},
		],
		data: {"Grupo": true},
		url: "modulos/parametros.php",
		async: true
	};
	var GrupoArray = new Array();
	var GrupoValue = new Array();
	var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				GrupoArray.push(records[i]);
			}
		}
	});
	
	$("#inventario_listado_grupo").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
	});
	$("#inventario_listado_grupo").bind('change', function (event) {
		if (event.args)
		{
			SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
			SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#inventario_listado_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
		}
		else
		{
			$("#inventario_listado_grupo").jqxComboBox('clearSelection');
			$("#inventario_listado_subgrupo").jqxComboBox('clearSelection');
			$("#inventario_listado_subgrupo").jqxComboBox('clear');
		}
		LoadValues();
	});
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'},
			{ name: 'SubGrupo', type: 'string'},
		],
		data: {"SubGrupo": true},
		url: "modulos/parametros.php",
		async: true
	};
	var SubGrupoArray = new Array();
	var SubGrupoValue = new Array();
	var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				SubGrupoArray.push(records[i]);
			}
		}
	});
	
	$("#inventario_listado_subgrupo").jqxComboBox(
	{
		theme: mytheme,
		width: 200,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
	});
	$("#inventario_listado_subgrupo").bind('change', function (event) {
		if (!event.args)
			$("#inventario_listado_subgrupo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	var UnidadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Unidad', type: 'string'},
		],
		data: {"Inventario_Unidad":true},
		url: "modulos/parametros.php",
		async: true
	};
	var UnidadArray = new Array();
	var UnidadDataAdapter = new $.jqx.dataAdapter(UnidadSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				UnidadArray.push(records[i]);
			}
		}
	});
	
	$("#inventario_listado_imprimir1").jqxButton({
		width: 160,
		template: "warning"
	});
	$("#inventario_listado_imprimir1").bind('click', function ()
	{
		var data = "";
		data += "imprimir/inventario_listado.php?Costo="+true+"&Categoria="+$("#inventario_listado_categoria").val()+"";
		data += "&Grupo="+$("#inventario_listado_grupo").val()+"&SubGrupo="+$("#inventario_listado_subgrupo").val()+"";
		data += "&Codigo="+$("#inventario_listado_codigo").val()+"";
		//alert(data);
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#inventario_listado_imprimir2").jqxButton({
		width: 160,
		template: "warning"
	});
	$("#inventario_listado_imprimir2").bind('click', function ()
	{
		var data = "";
		data += "imprimir/inventario_listado.php?Categoria="+$("#inventario_listado_categoria").val()+"";
		data += "&Grupo="+$("#inventario_listado_grupo").val()+"&SubGrupo="+$("#inventario_listado_subgrupo").val()+"";
		data += "&Codigo="+$("#inventario_listado_codigo").val()+"";
		//alert(data);
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#inventario_listado_exportar1").jqxButton({
		width: 160,
		template: "success"
	});
	$("#inventario_listado_exportar1").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Inventario_Listado=true&Costo="+true+"&Categoria="+$("#inventario_listado_categoria").val()+"";
		data += "&Grupo="+$("#inventario_listado_grupo").val()+"&SubGrupo="+$("#inventario_listado_subgrupo").val()+"";
		data += "&Codigo="+$("#inventario_listado_codigo").val()+"";
		//alert(data);
		window.location = data;
	});
	
	$("#inventario_listado_exportar2").jqxButton({
		width: 160,
		template: "success"
	});
	$("#inventario_listado_exportar2").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Inventario_Listado=true&Categoria="+$("#inventario_listado_categoria").val()+"";
		data += "&Grupo="+$("#inventario_listado_grupo").val()+"&SubGrupo="+$("#inventario_listado_subgrupo").val()+"";
		data += "&Codigo="+$("#inventario_listado_codigo").val()+"";
		//alert(data);
		window.location = data;
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
				"Inventario_Listado":true,
				"Categoria":$("#inventario_listado_categoria").val(),
				"Grupo":$("#inventario_listado_grupo").val(),
				"SubGrupo":$("#inventario_listado_subgrupo").val(),
				"Codigo":$("#inventario_listado_codigo").val()
			};
			
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#inventario_listado_items_grid").jqxGrid({source: GridDataAdapter});
			
		},350);
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ID', type: 'string' },
			{ name: 'Activo', type: 'bool' },
			{ name: 'Motivo', type: 'string' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'Categoria', type: 'string' },
			{ name: 'Grupo', type: 'string' },
			{ name: 'SubGrupo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Und', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Stock', type: 'decimal' },
			{ name: 'Costo', type: 'decimal' },
			{ name: 'Ult_Costo', type: 'decimal' },
			{ name: 'Costo_Prom', type: 'decimal' },
			{ name: 'Lista1', type: 'decimal' },
			{ name: 'Lista2', type: 'decimal' },
			{ name: 'Lista3', type: 'decimal' },
			{ name: 'Lista4', type: 'decimal' },
			{ name: 'Produccion', type: 'bool' },
			{ name: 'Facturar', type: 'bool' },
		],
		url: "modulos/datos_productos.php",
		async: true,
		updaterow: function (rowid, rowdata, commit)
		{
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Inventario_Listado":true,
					"ID":rowdata.ID,
					"Activo":rowdata.Activo,
					"Motivo":rowdata.Motivo,
					"Codigo":rowdata.Codigo,
					"Categoria":rowdata.Categoria,
					"Grupo":rowdata.Grupo,
					"SubGrupo":rowdata.SubGrupo,
					"Nombre":rowdata.Nombre,
					"Und":rowdata.Und,
					"Peso":rowdata.Peso,
					"Stock":rowdata.Stock,
					"Costo":rowdata.Costo,
					"Ult_Costo":rowdata.Ult_Costo,
					"Costo_Prom":rowdata.Costo_Prom,
					"Lista1":rowdata.Lista1,
					"Lista2":rowdata.Lista2,
					"Lista3":rowdata.Lista3,
					"Lista4":rowdata.Lista4,
					"Produccion":rowdata.Produccion,
					"Facturar":rowdata.Facturar,
				},
				async: true,
				success: function (data, status, xhr) {
					if (data == "OK")
						commit(true);
					else
					{
						Alerts_Box("Ha ocurrido un grave error! Favor Contactar al Servicio Tecnico", 3);
						commit(false);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
					commit(false);
				}
			});
		}
	};
	
	var GrupoCEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({
			selectedIndex: -1,
			displayMember: 'Grupo',
			valueMember: 'Grupo',
		});
	};
	
	var GrupoIEditor = function(row, cellValue, editor, cellText, width, height)
	{
		var value = $("#inventario_listado_items_grid").jqxGrid('getcellvalue', row, "Categoria");
		GrupoValue = [];
		var len = GrupoArray.length;
		for (i = 0; i < len; i++)
		{
			if (GrupoArray[i]["Categoria"] == value)
				GrupoValue.push(GrupoArray[i]["Grupo"])
		}
		editor.jqxComboBox({ source: GrupoValue });
		var item = editor.jqxComboBox('getItemByValue', cellValue);
		if (item != undefined)
			editor.jqxComboBox({ selectedIndex: item.index });
		else
			editor.jqxComboBox({ selectedIndex: -1 });
	};
	
	var SubGrupoCEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({
			selectedIndex: -1,
			displayMember: 'SubGrupo',
			valueMember: 'SubGrupo',
		});
	};
	
	var SubGrupoIEditor = function(row, cellValue, editor, cellText, width, height)
	{
		var value = $("#inventario_listado_items_grid").jqxGrid('getcellvalue', row, "Grupo");
		SubGrupoValue = [];
		var len = SubGrupoArray.length;
		for (i = 0; i < len; i++)
		{
			if (SubGrupoArray[i]["Grupo"] == value)
				SubGrupoValue.push(SubGrupoArray[i]["SubGrupo"])
		}
		editor.jqxComboBox({ source: SubGrupoValue });
		var item = editor.jqxComboBox('getItemByValue', cellValue);
		if (item != undefined)
			editor.jqxComboBox({ selectedIndex: item.index });
		else
			editor.jqxComboBox({ selectedIndex: -1 });
	};
	
	$("#inventario_listado_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'ID', editable: false },
			{
				text: 'Activo',
				datafield: 'Activo',
				width: 50,
				height: 20,
				pinned: true,
				editable: Admin ? true:false,
				columntype: 'checkbox',
			},
			{ text: 'Motivo', datafield: 'Motivo', editable: Admin ? true:Modificar, width: 100, height: 20 },
			{ text: 'CodFab', datafield: 'Codigo', width: 90, height: 20, pinned: true, editable: false, },
			{
				text: 'Categoria',
				datafield: 'Categoria',
				width: 120,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'combobox',
				createeditor: function(row, cellValue, editor, cellText, width, height) {
					editor.jqxComboBox({
						source: CategoriaArray,
						selectedIndex: -1,
						displayMember: 'Categoria',
						valueMember: 'Categoria',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "" || newvalue == oldvalue) return oldvalue;
					var len = CategoriaArray.length;
					for (i = 0; i < len; i++)
					{
						if (CategoriaArray[i]["Categoria"] == newvalue) {
							$("#inventario_listado_items_grid").jqxGrid('setcellvalue', row, "Grupo", "");
							$("#inventario_listado_items_grid").jqxGrid('setcellvalue', row, "SubGrupo", "");
							return;
						}
					}
					return oldvalue;
				}
			},
			{
				text: 'Grupo',
				datafield: 'Grupo',
				width: 120,
				height: 20,
				editable: Admin ? true:Modificar, 
				columntype: 'combobox',
				createeditor: GrupoCEditor,
				initeditor: GrupoIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (GrupoValue.indexOf(newvalue) < 0 || newvalue == "" || newvalue == oldvalue)
						return oldvalue;
					else
						$("#inventario_listado_items_grid").jqxGrid('setcellvalue', row, "SubGrupo", "");
				}
			},
			{
				text: 'SubGrupo',
				datafield: 'SubGrupo', 
				width: 120,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'combobox',
				createeditor: SubGrupoCEditor,
				initeditor: SubGrupoIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (SubGrupoValue.indexOf(newvalue) < 0 || newvalue == "") return oldvalue;
				}
			},
			{ text: 'Nombre', datafield: 'Nombre', width: 200, height: 20, pinned: true, editable: Admin ? true:Modificar, },
			{
				text: 'Unidad',
				datafield: 'Und',
				width: 60,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'combobox',
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: UnidadArray,
						dropDownWidth: 100,
						selectedIndex: -1,
						displayMember: 'Unidad',
						valueMember: 'Unidad',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
					var len = UnidadArray.length;
					for (i = 0; i < len; i++)
					{
						if (UnidadArray[i]["Unidad"] == newvalue)
							return;
					}
					return oldvalue;
				}
			},
			{
				text: 'Peso',
				datafield: 'Peso',
				width: 80,
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Stock Min',
				datafield: 'Stock',
				width: 80,
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Costo',
				datafield: 'Costo',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
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
				editable: Admin ? true:Modificar,
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
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Lista 1',
				datafield: 'Lista1',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Lista 2',
				datafield: 'Lista2',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Lista 3',
				datafield: 'Lista3',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Lista 4',
				datafield: 'Lista4',
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Prod.',
				datafield: 'Produccion',
				width: 40,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'checkbox',
			},
			{
				text: 'Fact.',
				datafield: 'Facturar',
				width: 40,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'checkbox',
			},
		]
	});
	$("#inventario_listado_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#inventario_listado_items_grid").jqxGrid('hidecolumn', 'ID');
	
	// Load Initial Values!
	LoadValues();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px; text-align:center;">
		<tr>
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
			<td>
				<input type="button" id="inventario_listado_imprimir1" value="Imprimir con Costos"/>
			</td>
			<td>
				<input type="button" id="inventario_listado_exportar1" value="Exportar con Costos"/>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="inventario_listado_codigo"/>
			</td>
			<td>
				<div id="inventario_listado_categoria"></div>
			</td>
			<td>
				<div id="inventario_listado_grupo"></div>
			</td>
			<td>
				<div id="inventario_listado_subgrupo"></div>
			</td>
			<td>
				<input type="button" id="inventario_listado_imprimir2" value="Imprimir sin Costos"/>
			</td>
			<td>
				<input type="button" id="inventario_listado_exportar2" value="Exportar sin Costos"/>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="inventario_listado_items_grid"></div>
</div>
