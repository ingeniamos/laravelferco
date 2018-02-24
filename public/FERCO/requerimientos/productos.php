<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Categoria = "";
	var Grupo = "";
	var SubGrupo = "";
	var Unidad = "";
	var CategoriaArray = new Array();
	var GrupoArray = new Array();
	var GrupoValue = new Array();
	var SubGrupoArray = new Array();
	var SubGrupoValue = new Array();
	var UnidadArray = new Array();
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear() + 1;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Requerimientos_Content");
	var Body = document.getElementById("Requerimientos_Productos");
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
				SystemMap("Productos", true);
				LoadParameters(true);
				
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
	var Guardar = false;
	var Modificar = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Requerimientos" && $data[$i]["SubModulo"] == "Productos" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Requerimientos" && $data[$i]["SubModulo"] == "Productos" && $data[$i]["Modificar"] == "true")
				{
		?>
					Modificar = true;
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
	
	function LoadParameters(Refresh)
	{
		CategoriaSource.data = {"Inventario_Categoria":true};
		GrupoSource.data = {"Grupo":true};
		SubGrupoSource.data = {"SubGrupo":true};
		UnidadSource.data = {"Inventario_Unidad":true};
		
		var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
				{
					CategoriaArray.push(records[i]);
				}
			},
			loadComplete: function (records)
			{
				$("#requerimientos_productos_categoria").jqxComboBox({source: records});
			}
		});
		
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
		
		var UnidadDataAdapter = new $.jqx.dataAdapter(UnidadSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
				{
					UnidadArray.push(records[i]);
				}
			},
			loadComplete: function (records)
			{
				$("#requerimientos_productos_unidad").jqxComboBox({source: records});
			}
		});
		
		if (Refresh)
		{
			$("#requerimientos_productos_items_grid").jqxGrid("updatebounddata");
		}
		else
		{
			GridSource.data = {"Requerimientos_Productos_Listado":true};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#requerimientos_productos_items_grid").jqxGrid({source: GridDataAdapter});
		}
	};
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Categoria', type: 'string'},
		],
		url: "modulos/parametros.php",
		//async: false
	};
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Categoria', type: 'string'},
			{ name: 'Grupo', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'},
			{ name: 'SubGrupo', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	var UnidadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Unidad', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	$("#requerimientos_productos_categoria").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Categoria',
		valueMember: 'Categoria'
	});
	$("#requerimientos_productos_categoria").on('change', function (event)
	{
		if (event.args)
		{
			if (Categoria == event.args.item.value)
				return;
			
			Categoria = event.args.item.value;
			
			GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
			GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#requerimientos_productos_grupo").jqxComboBox({source: GrupoAdapter});
		}
		else
		{
			Categoria = "";
			Grupo = "";
			SubGrupo = "";
			$("#requerimientos_productos_categoria").jqxComboBox('clearSelection');
			$("#requerimientos_productos_grupo").jqxComboBox('clearSelection');
			$("#requerimientos_productos_grupo").jqxComboBox('clear');
			$("#requerimientos_productos_subgrupo").jqxComboBox('clearSelection');
			$("#requerimientos_productos_subgrupo").jqxComboBox('clear');
		}
	});
	$("#requerimientos_productos_categoria").on("bindingComplete", function (event)
	{
		if (Categoria != "")
			$("#requerimientos_productos_categoria").jqxComboBox("selectItem", Categoria);
	});
	
	$("#requerimientos_productos_grupo").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
	});
	$("#requerimientos_productos_grupo").on('change', function (event)
	{
		if (event.args)
		{
			if (Grupo == event.args.item.value)
				return;
			
			Grupo = event.args.item.value;
			
			SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
			SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#requerimientos_productos_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
		}
		else
		{
			Grupo = "";
			SubGrupo = "";
			$("#requerimientos_productos_grupo").jqxComboBox('clearSelection');
			$("#requerimientos_productos_subgrupo").jqxComboBox('clearSelection');
			$("#requerimientos_productos_subgrupo").jqxComboBox('clear');
		}
	});
	$("#requerimientos_productos_grupo").on("bindingComplete", function (event)
	{
		if (Grupo != "")
			$("#requerimientos_productos_grupo").jqxComboBox("selectItem", Grupo);
	});
	
	$("#requerimientos_productos_subgrupo").jqxComboBox(
	{
		theme: mytheme,
		width: 170,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
	});
	$("#requerimientos_productos_subgrupo").on('change', function (event)
	{
		if (event.args)
			SubGrupo = event.args.item.value;
		else
		{
			SubGrupo = "";
			$("#requerimientos_productos_subgrupo").jqxComboBox('clearSelection');
		}
	});
	$("#requerimientos_productos_subgrupo").on("bindingComplete", function (event)
	{
		if (SubGrupo != "")
			$("#requerimientos_productos_subgrupo").jqxComboBox("selectItem", SubGrupo);
	});
	
	$("#requerimientos_productos_unidad").jqxComboBox(
	{
		theme: mytheme,
		width: 70,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Selec...',
		selectedIndex: -1,
		displayMember: 'Unidad',
		valueMember: 'Unidad',
	});
	$("#requerimientos_productos_unidad").on('change', function (event)
	{
		if (event.args)
			Unidad = event.args.item.value;
		else
		{
			Unidad = "";
			$("#requerimientos_productos_unidad").jqxComboBox('clearSelection');
		}
	});
	$("#requerimientos_productos_unidad").on("bindingComplete", function (event)
	{
		if (Unidad != "")
			$("#requerimientos_productos_unidad").jqxComboBox("selectItem", Unidad);
	});
	
	$("#requerimientos_productos_vencimiento").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#requerimientos_productos_vencimiento").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	
	$("#requerimientos_productos_codigo").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
	});
	
	$("#requerimientos_productos_nombre").jqxInput({
		theme: mytheme,
		width: 250,
		height: 20,
	});
	
	$("#requerimientos_productos_peso").jqxNumberInput({
		theme: mytheme,
		width: 135,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: 'Kg',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 12,
		max: 999999999999,
	});
	
	$("#requerimientos_productos_valor").jqxNumberInput({
		theme: mytheme,
		width: 155,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
	});
	
	$("#requerimientos_productos_existencia").jqxNumberInput({
		theme: mytheme,
		width: 120,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		decimalDigits: 2,
		digits: 12,
		max: 999999999999,
	});
	
	$("#requerimientos_productos_stock").jqxNumberInput({
		theme: mytheme,
		width: 120,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		decimalDigits: 2,
		digits: 12,
		max: 999999999999,
	});
	
	$("#requerimientos_productos_add").jqxButton({
		width: 100,
		height: 25,
		template: "success"
	});
	$("#requerimientos_productos_add").on('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		Add_Row();
	});
	
	function Add_Row()
	{
		var CAT = $("#requerimientos_productos_categoria").jqxComboBox("getSelectedItem");
		var GRU = $("#requerimientos_productos_grupo").jqxComboBox("getSelectedItem");
		var SGR = $("#requerimientos_productos_subgrupo").jqxComboBox("getSelectedItem");
		var UND = $("#requerimientos_productos_unidad").jqxComboBox("getSelectedItem");
		var Vence = GetFormattedDate($("#requerimientos_productos_vencimiento").jqxDateTimeInput("getDate"));
		var Codigo = $("#requerimientos_productos_codigo").val();
		var Nombre = $("#requerimientos_productos_nombre").val();
		var Peso = $("#requerimientos_productos_peso").val();
		var Valor = $("#requerimientos_productos_valor").val();
		var Existencia = $("#requerimientos_productos_existencia").val();
		var Stock = $("#requerimientos_productos_stock").val();
		
		
		if (!CAT) {
			Alerts_Box("Favor Ingresar una Categoria!", 3);
			WaitClick_Combobox("requerimientos_productos_categoria");
			return;
		}
		
		if (!GRU) {
			Alerts_Box("Favor Ingresar un Grupo!", 3);
			WaitClick_Combobox("requerimientos_productos_grupo");
			return;
		}
		
		if (!SGR) {
			Alerts_Box("Favor Ingresar un SubGrupo!", 3);
			WaitClick_Combobox("requerimientos_productos_subgrupo");
			return;
		}
		
		if (!UND) {
			Alerts_Box("Favor Ingresar un Tipo de Unidad!", 3);
			WaitClick_Combobox("requerimientos_productos_categoria");
			return;
		}
		
		if (Codigo == "") {
			Alerts_Box("Favor Ingresar un Codigo!", 3);
			WaitClick_Input("requerimientos_productos_codigo");
			return;
		}
		
		if (Nombre == "") {
			Alerts_Box("Favor Ingresar un Nombre!", 3);
			WaitClick_Input("requerimientos_productos_nombre");
			return;
		}
		
		if (Peso < 1) {
			Alerts_Box("Favor Ingresar el Peso del Producto!", 3);
			WaitClick_NumberInput("requerimientos_productos_peso");
			return;
		}
		
		if (Valor < 1) {
			Alerts_Box("Favor Ingresar un Valor!", 3);
			WaitClick_NumberInput("requerimientos_productos_valor");
			return;
		}
		
		if (Existencia < 0) {
			Alerts_Box("Favor Ingresar una Existencia Mayor o igual a 0", 3);
			WaitClick_NumberInput("requerimientos_productos_existencia");
			return;
		}
		
		if (Stock < 1) {
			Alerts_Box("Favor Ingresar un Stock Minimo!", 3);
			WaitClick_NumberInput("requerimientos_productos_stock");
			return;
		}
		
		var datainfo = $("#requerimientos_productos_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Codigo":Codigo,
			"Nombre":Nombre,
			"Categoria":CAT.value,
			"Grupo":GRU.value,
			"SubGrupo":SGR.value,
			"Unidad":UND.value,
			"Peso":Peso,
			"Valor":Valor,
			"Existencia":Existencia,
			"Stock":Stock,
			"Vencimiento":Vence,
		}];
		
		$("#requerimientos_productos_items_grid").jqxGrid("addrow", count, datarow);
		$("#requerimientos_productos_codigo").val("");
		$("#requerimientos_productos_nombre").val("");
		$("#requerimientos_productos_peso").val("0");
		$("#requerimientos_productos_valor").val("0");
		$("#requerimientos_productos_existencia").val("0");
		$("#requerimientos_productos_stock").val("0");
	};
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ID', type: 'string' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'Categoria', type: 'string' },
			{ name: 'Grupo', type: 'string' },
			{ name: 'SubGrupo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Unidad', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Existencia', type: 'decimal' },
			{ name: 'Stock', type: 'decimal' },
			{ name: 'Vencimiento', type: 'date' },
			{ name: 'Notas', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'ModificadorID', type: 'string' },
		],
		cache: false,
		url: "modulos/datos_productos.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Codigo"] != "")
			{
				$.ajax({
					dataType: "json",
					url: "modulos/guardar.php",
					data: {
						"Requerimientos_Productos_Agregar":true,
						"Codigo":rowdata[0]["Codigo"],
						"Nombre":rowdata[0]["Nombre"],
						"Categoria":rowdata[0]["Categoria"],
						"Grupo":rowdata[0]["Grupo"],
						"SubGrupo":rowdata[0]["SubGrupo"],
						"Unidad":rowdata[0]["Unidad"],
						"Peso":rowdata[0]["Peso"],
						"Valor":rowdata[0]["Valor"],
						"Existencia":rowdata[0]["Existencia"],
						"Stock":rowdata[0]["Stock"],
						"Vencimiento":rowdata[0]["Vencimiento"],
					},
					success: function (data, status, xhr)
					{
						switch(data[0]["MESSAGE"])
						{
							case "OK":
								commit(true);
								$("#requerimientos_productos_items_grid").jqxGrid("updatebounddata");
							break;
							
							case "EXIST":
								commit(false);
								Alerts_Box("No es posible agregar el producto debido a que ya existe otro producto con el mismo \"ID\"", 4);
							break;
							
							case "ERROR":
								commit(false);
								Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
							break;
						}
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						commit(false);
						Alerts_Box("Ocurrió un error mientras se guardaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
						+"Error: "+errorThrown, 3);
					}
				});
			}
			else
			{
				commit(false);
			}
		},
		updaterow: function (rowid, rowdata, commit)
		{
			$.ajax({
				dataType: "json",
				url: "modulos/guardar.php",
				data: {
					"Requerimientos_Productos_Actualizar":true,
					"ID":rowdata.ID,
					"Codigo":rowdata.Codigo,
					"Nombre":rowdata.Nombre,
					"Categoria":rowdata.Categoria,
					"Grupo":rowdata.Grupo,
					"SubGrupo":rowdata.SubGrupo,
					"Unidad":rowdata.Unidad,
					"Peso":rowdata.Peso,
					"Valor":rowdata.Valor,
					"Existencia":rowdata.Existencia,
					"Stock":rowdata.Stock,
					"Vencimiento":GetFormattedDate(rowdata.Vencimiento),
					"Notas":rowdata.Notas,
				},
				success: function (data, status, xhr)
				{
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							commit(true);
						break;
						
						case "ERROR":
							commit(false);
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
						break;
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					commit(false);
					Alerts_Box("Ocurrió un error mientras se guardaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
					+"Error: "+errorThrown, 3);
				}
			});
		},
		deleterow: function (rowid, commit)
		{
			ClickOK = false;
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#requerimientos_productos_items_grid").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: "json",
						url: "modulos/guardar.php",
						data: {
							"Requerimientos_Productos_Borrar":true,
							"ID":ID,
						},
						success: function (data, status, xhr)
						{
							switch(data[0]["MESSAGE"])
							{
								case "OK":
									commit(true);
								break;
								
								case "ERROR":
									commit(false);
									Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
								break;
							}
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							commit(false);
							Alerts_Box("Ocurrió un error mientras se guardaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
							+"Error: "+errorThrown, 3);
						}
					});
				}
				
				if (ClickCANCEL == true)
				{
					clearInterval(CheckTimer);
					ClickCANCEL = false;
					commit(false);
				}
			}, 10);
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
		var value = $("#requerimientos_productos_items_grid").jqxGrid('getcellvalue', row, "Categoria");
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
		var value = $("#requerimientos_productos_items_grid").jqxGrid('getcellvalue', row, "Grupo");
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
	
	$("#requerimientos_productos_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		sortable: true,
		pageable: true,
		showfilterrow: true,
		filterable: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				width: "3%",
				height: 20,
				pinned: true,
				filterable: false,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					if (Admin)
					{
						var selectedrowindex = $("#requerimientos_productos_items_grid").jqxGrid('getselectedrowindex');
						var rowscount = $("#requerimientos_productos_items_grid").jqxGrid('getdatainformation').rowscount;
						if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
							var id = $("#requerimientos_productos_items_grid").jqxGrid('getrowid', selectedrowindex);
							$("#requerimientos_productos_items_grid").jqxGrid('deleterow', id);
						}
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false },
			{ text: 'Codigo', datafield: 'Codigo', width: "8%", height: 20, pinned: true, editable: Admin, },
			{ text: 'Nombre', datafield: 'Nombre', width: "20%", height: 20, pinned: true, editable: Admin ? true:Modificar, },
			{
				text: 'Categoria',
				datafield: 'Categoria',
				width: "10%",
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
							$("#requerimientos_productos_items_grid").jqxGrid('setcellvalue', row, "Grupo", "");
							$("#requerimientos_productos_items_grid").jqxGrid('setcellvalue', row, "SubGrupo", "");
							return;
						}
					}
					return oldvalue;
				}
			},
			{
				text: 'Grupo',
				datafield: 'Grupo',
				width: "10%",
				height: 20,
				editable: Admin ? true:Modificar, 
				columntype: 'combobox',
				createeditor: GrupoCEditor,
				initeditor: GrupoIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (GrupoValue.indexOf(newvalue) < 0 || newvalue == "" || newvalue == oldvalue)
						return oldvalue;
					else
						$("#requerimientos_productos_items_grid").jqxGrid('setcellvalue', row, "SubGrupo", "");
				}
			},
			{
				text: 'SubGrupo',
				datafield: 'SubGrupo', 
				width: "10%",
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'combobox',
				createeditor: SubGrupoCEditor,
				initeditor: SubGrupoIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (SubGrupoValue.indexOf(newvalue) < 0 || newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Unidad',
				datafield: 'Unidad',
				width: "5%",
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
				width: "8%",
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Valor',
				datafield: 'Valor',
				width: "12%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Existencia',
				datafield: 'Existencia',
				width: "10%",
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Stock Min.',
				datafield: 'Stock',
				width: "10%",
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Fecha Venc.',
				datafield: 'Vencimiento',
				width: "9%",
				height: 20,
				columntype: 'datetimeinput',
				cellsformat: 'dd-MMM-yyyy',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Notas', datafield: 'Notas', width: "10%", height: 20, editable: Admin ? true:Modificar, filterable: false,},
			{ text: 'Dig.', datafield: 'DigitadorID', width: "5%", height: 20, editable: false, },
			{ text: 'Mod.', datafield: 'ModificadorID', width: "5%", height: 20, editable: false, },
		]
	});
	$("#requerimientos_productos_items_grid").jqxGrid('hidecolumn', 'ID');
	$("#requerimientos_productos_items_grid").jqxGrid('localizestrings', localizationobj);
	
	if (!Admin && !Guardar)
	{
		$("#requerimientos_productos_add").jqxButton({ disabled: true });
	}
	
	LoadParameters();
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="3" cellspacing="1" style="margin:5px 0px 20px 0px;">
		<tr>
			<td>
				Categoria
			</td>
			<td>
				<div id="requerimientos_productos_categoria"></div>
			</td>
			<td>
				Grupo
			</td>
			<td>
				<div id="requerimientos_productos_grupo"></div>
			</td>
			<td>
				SubGrupo
			</td>
			<td colspan="2">
				<div id="requerimientos_productos_subgrupo"></div>
			</td>
			<td>
				Unidad
			</td>
			<td>
				<div id="requerimientos_productos_unidad"></div>
			</td>
			<td>
				Vencimiento
			</td>
			<td>
				<div id="requerimientos_productos_vencimiento"></div>
			</td>
		</tr>
		<tr>
			<td>
				Codigo
			</td>
			<td>
				<input type="text" id="requerimientos_productos_codigo"/>
			</td>
			<td>
				Nombre
			</td>
			<td colspan="2">
				<input type="text" id="requerimientos_productos_nombre"/>
			</td>
			<td>
				Peso
			</td>
			<td>
				<div id="requerimientos_productos_peso"></div>
			</td>
			<td>
				Valor
			</td>
			<td colspan="3">
				<div id="requerimientos_productos_valor"></div>
			</td>
		</tr>
		<tr>
			<td>
				Existencia
			</td>
			<td>
				<div id="requerimientos_productos_existencia"></div>
			</td>
			<td>
				Stock
			</td>
			<td>
				<div id="requerimientos_productos_stock"></div>
			</td>
			<td colspan="6">
				&nbsp;
			</td>
			<td>
				<input type="button" id="requerimientos_productos_add" value="Añadir"/>
			</td>
		</tr>
	</table>
	
	<div id="requerimientos_productos_items_grid"></div>
</div>