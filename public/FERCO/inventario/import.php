<?php
session_start();
?>
<style>    
.orange {
	color: black;
	background-color: rgba(255,242,204,1);
}
.red {
	color: black;
	background-color: rgba(251,229,214,1);
}
.yellow {
	color: black;
	background-color: rgba(205,212,100,0.2);
}
.orange:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .orange:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(255,242,204,1);
}
.red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(251,229,214,1);
}
.yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(205,212,100,0.2);
}
</style>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var LastRow = -1;
	var GridErrors = 0;
	var Locked = false;
	var Timer1 = 0;
	var Timer2 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Inventario_Content");
	var Body = document.getElementById("Inventario_Importar_Content");
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
				SystemMap("Importar Productos", true);
				//ReDefine();
				
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
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Importar" && $data[$i]["Guardar"] == "true")
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
	
	$("#inventario_importar_upload").jqxFileUpload({
		theme: mytheme,
		width: 250,
		multipleFilesUpload: false,
		browseTemplate: 'success',
		uploadTemplate: 'primary',
		cancelTemplate: 'danger',
		uploadUrl: 'modulos/guardar.php',
		fileInputName: 'File_Uploads',
	});
	$("#inventario_importar_uploadBrowseButton").jqxButton({ width: '120px'});
	$("#inventario_importar_upload").jqxFileUpload({ localization: localizationobj });
	$("#inventario_importar_upload").jqxFileUpload({ localization: { browseButton: 'Subir Excel' } });
	$("#inventario_importar_upload").on('uploadEnd', function (event)
	{
		var args = event.args;
		var fileName = args.file;
		var serverResponce = args.response;
		
		if (serverResponce == "OK")
		{
			LoadValues(fileName, 1);
		}
		else
		{
			Alerts_Box(""+serverResponce, 3);
		}
	});
	
	$("#inventario_importar_nombre").jqxInput(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		disabled: true
	});
	
	$("#inventario_importar_errores").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#inventario_importar_descargar").jqxButton({
		width: 200,
		height: 25,
		template: "primary"
	});
	$("#inventario_importar_descargar").bind('click', function ()
	{
		var data = "downloads/productos.xlsx";
		window.location = data;
	});
	
	function LoadValues(Data, Type)
	{
		if (Locked == true)
			return;
			
		Locked = true;
		
		if (Type == 1)
		{
			GridErrors = 0;
			GridSource.data = {
				"Inventario_Importar":true,
				"FILE":Data,
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource,
			{
				loadComplete: function (records)
				{
					for (var i = 0; i < records.length; i++)
					{
						if (records[i]["Categoria"] == "" || records[i]["Grupo"] == "" 
						|| records[i]["SubGrupo"] == "" || records[i]["Unidad"] == "" 
						|| records[i]["Peso"] < 0)
						{
							GridErrors++;
						}
					}
					
					$("#inventario_importar_nombre").val(Data);
					$("#inventario_importar_errores").val(GridErrors);
					
					if (GridErrors > 0)
						Alerts_Box("Se han encontrado \""+GridErrors+"\" Errores en los datos", 4);
				}
			});
			$("#inventario_importar_items_grid").jqxGrid({source: GridDataAdapter});
		}
		else
		{
			GridErrors = 0;
			GridSource.localdata = Data;
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource,
			{
				loadComplete: function (records)
				{
					for (var i = 0; i < records.length; i++)
					{
						if (records[i]["Categoria"] == "" || records[i]["Grupo"] == "" 
						|| records[i]["SubGrupo"] == "" || records[i]["Unidad"] == "" 
						|| records[i]["Peso"] < 0)
						{
							GridErrors++;
						}
					}
					
					$("#inventario_importar_errores").val(GridErrors);
					
					if (GridErrors > 0)
						Alerts_Box("Datos Guardados con Exito!<br/>[\"Advertencia\"]<br/>\""+GridErrors+"\" Productos, no se han podido procesar porque poseen errores.", 1);
					else
						Alerts_Box("Datos Guardados con Exito!", 2);
				}
			});
			$("#inventario_importar_items_grid").jqxGrid({source: GridDataAdapter});
		}
		Locked = false;
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Categoria', type: 'string' },
			{ name: 'Grupo', type: 'string' },
			{ name: 'SubGrupo', type: 'string' },
			{ name: 'Costo', type: 'decimal' },
			{ name: 'Unidad', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Lista1', type: 'decimal' },
			{ name: 'Lista2', type: 'decimal' },
			{ name: 'Lista3', type: 'decimal' },
			{ name: 'Lista4', type: 'decimal' },
		],
		//data: {
		//	"Inventario_Importar":true,
		//	"FILE":"Productos.xlsx",
		//},
		url: "modulos/datos_productos.php",
	};
	
	var cellclass = function (row, columnfield, value)
	{
		var Categoria = $("#inventario_importar_items_grid").jqxGrid('getcellvalue', row, "Categoria");
		var Grupo = $("#inventario_importar_items_grid").jqxGrid('getcellvalue', row, "Grupo");
		var SubGrupo = $("#inventario_importar_items_grid").jqxGrid('getcellvalue', row, "SubGrupo");
		var Peso = $("#inventario_importar_items_grid").jqxGrid('getcellvalue', row, "Peso");
		var Unidad = $("#inventario_importar_items_grid").jqxGrid('getcellvalue', row, "Unidad");
		
		if (Categoria == "" || Grupo == "" || SubGrupo == "" || Unidad == "" || Peso < 0)
			return 'red';
		else
			return '';
	}
	
	var CategoriaCEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({
			selectedIndex: -1,
			displayMember: 'Categoria',
			valueMember: 'Categoria',
		});
	};
	
	var CategoriaIEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({ source: CategoriaArray });
		var item = editor.jqxComboBox('getItemByValue', cellValue);
		if (item != undefined)
			editor.jqxComboBox({ selectedIndex: item.index });
		else
			editor.jqxComboBox({ selectedIndex: -1 });
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
		var value = $("#inventario_importar_items_grid").jqxGrid('getcellvalue', row, "Categoria");
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
		var value = $("#inventario_importar_items_grid").jqxGrid('getcellvalue', row, "Grupo");
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
	
	var UnidadCEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({
			selectedIndex: -1,
			displayMember: 'Unidad',
			valueMember: 'Unidad',
		});
	};
	
	var UnidadIEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({ source: UnidadArray });
		var item = editor.jqxComboBox('getItemByValue', cellValue);
		if (item != undefined)
			editor.jqxComboBox({ selectedIndex: item.index });
		else
			editor.jqxComboBox({ selectedIndex: -1 });
	};
	
	$("#inventario_importar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		enablebrowserselection: true,
		//selectionmode: 'singlecell',
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
				editable: false,
				width: '3%',
				height: 20,
				pinned: true,
				filterable: false,
				cellclassname: cellclass,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#inventario_importar_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#inventario_importar_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#inventario_importar_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#inventario_importar_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '10%', height: 20, pinned: true, cellclassname: cellclass },
			{ text: 'Nombre', datafield: 'Nombre', editable: true, width: '26%', height: 20, pinned: true, cellclassname: cellclass },
			{
				text: 'Categoria',
				datafield: 'Categoria',
				width: '10%',
				height: 20,
				editable: true,
				cellclassname: cellclass,
				columntype: 'combobox',
				createeditor: CategoriaCEditor,
				initeditor: CategoriaIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					if (newvalue == oldvalue)
					{
						if (newvalue == "")
							return "";
						
						return oldvalue;
					}
					var len = CategoriaArray.length;
					for (i = 0; i < len; i++)
					{
						if (CategoriaArray[i]["Categoria"] == newvalue)
						{
							$("#inventario_importar_items_grid").jqxGrid('setcellvalue', row, "Grupo", "");
							$("#inventario_importar_items_grid").jqxGrid('setcellvalue', row, "SubGrupo", "");
							return;
						}
					}
					$("#inventario_importar_items_grid").jqxGrid('setcellvalue', row, "Grupo", "");
					$("#inventario_importar_items_grid").jqxGrid('setcellvalue', row, "SubGrupo", "");
					return "";
				}
			},
			{
				text: 'Grupo',
				datafield: 'Grupo',
				width: '10%',
				height: 20,
				editable: true,
				cellclassname: cellclass,
				columntype: 'combobox',
				createeditor: GrupoCEditor,
				initeditor: GrupoIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (GrupoValue.indexOf(newvalue) < 0)
					{
						$("#inventario_importar_items_grid").jqxGrid('setcellvalue', row, "SubGrupo", "");
						return "";
					}
				}
			},
			{
				text: 'SubGrupo',
				datafield: 'SubGrupo', 
				width: '10%',
				height: 20,
				editable: true,
				cellclassname: cellclass,
				columntype: 'combobox',
				createeditor: SubGrupoCEditor,
				initeditor: SubGrupoIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (SubGrupoValue.indexOf(newvalue) < 0) return "";
				}
			},
			{
				text: 'Costo',
				datafield: 'Costo',
				width: '12%',
				height: 20,
				editable: true,
				filterable: false,
				cellclassname: cellclass,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Unidad',
				datafield: 'Unidad',
				width: '5%',
				height: 20,
				editable: true,
				filterable: false,
				cellclassname: cellclass,
				columntype: 'combobox',
				createeditor: UnidadCEditor,
				initeditor: UnidadIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					var len = UnidadArray.length;
					for (i = 0; i < len; i++)
					{
						if (UnidadArray[i]["Unidad"] == newvalue)
							return;
					}
					return "";
				}
			},
			{
				text: 'Peso',
				datafield: 'Peso',
				width: '10%',
				height: 20,
				editable: true,
				filterable: false,
				cellclassname: cellclass,
				cellsalign: 'right',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 3, digits: 18, spinButtons: false });
				}
			},
			{
				text: 'Lista 1',
				datafield: 'Lista1',
				width: '15%',
				height: 20,
				cellclassname: cellclass,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: true,
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Lista 2',
				datafield: 'Lista2',
				width: '15%',
				height: 20,
				cellclassname: cellclass,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: true,
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Lista 3',
				datafield: 'Lista3',
				width: '15%',
				height: 20,
				cellclassname: cellclass,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: true,
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Lista 4',
				datafield: 'Lista4',
				width: '15%',
				height: 20,
				cellclassname: cellclass,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: true,
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
		],
	});
	//$("#inventario_importar_items_grid").jqxGrid('hidecolumn', 'Clasificacion');
	$("#inventario_importar_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#inventario_importar_items_grid").on("cellvaluechanged", function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		var Categoria = $("#inventario_importar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Categoria");
		var Grupo = $("#inventario_importar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Grupo");
		var SubGrupo = $("#inventario_importar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "SubGrupo");
		var Peso = $("#inventario_importar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Peso");
		var Unidad = $("#inventario_importar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Unidad");
		
		if (Categoria != "" && Grupo != "" && SubGrupo != "" && Unidad != "" && Peso > 0)
		{
			var TmpVal = $("#inventario_importar_errores").val();
			if (TmpVal > 0)
			{
				TmpVal--;
				$("#inventario_importar_errores").val(TmpVal);
			}
		}
	});
	
	function SaveData()
	{
		if (Locked)
			return;
		
		Locked = true;
		var Items = new Array();
		
		var datinfo = $("#inventario_importar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		if (count < 1)
		{
			Alerts_Box("Debe Importar al Menos un Producto.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (var i = 0; i < count; i++)
		{
			var tmp_array = {};
			var currentRow = $("#inventario_importar_items_grid").jqxGrid('getrowdata', i);
			
			tmp_array["Codigo"] = currentRow.Codigo;
			tmp_array["Nombre"] = currentRow.Nombre;
			tmp_array["Categoria"] = currentRow.Categoria;
			tmp_array["Grupo"] = currentRow.Grupo;
			tmp_array["SubGrupo"] = currentRow.SubGrupo;
			tmp_array["Costo"] = parseFloat(currentRow.Costo);
			tmp_array["Unidad"] = currentRow.Unidad;
			tmp_array["Peso"] = parseFloat(currentRow.Peso);
			tmp_array["Lista1"] = parseFloat(currentRow.Lista1);
			tmp_array["Lista2"] = parseFloat(currentRow.Lista2);
			tmp_array["Lista3"] = parseFloat(currentRow.Lista3);
			tmp_array["Lista4"] = parseFloat(currentRow.Lista4);
			
			Items[i] = tmp_array;
		}
		
		/*alert(JSON.stringify(Items))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "modulos/guardar.php",
			data: {"Inventario_Importar":Items},
			async: true,
			success: function (data)
			{
				/*
				alert(data);
				Locked = false;
				return;
				*/
				
				Locked = false;
				$("#inventario_importar_items_grid").jqxGrid("clear");
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				
				if (data[0]["Codigo"] == undefined)
					Alerts_Box("Datos Guardados con Exito!", 2);
				else
					LoadValues(data, 2);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	}
	
	$("#inventario_importar_nuevo").jqxButton({
		width: 150,
		height: 25,
		template: "success"
	});
	$("#inventario_importar_nuevo").bind('click', function ()
	{
		$("#inventario_importar_nombre").val("");
		$("#inventario_importar_errores").val("");
		$("#inventario_importar_items_grid").jqxGrid("clear");
	});
	
	$("#inventario_importar_guardar").jqxButton({
		width: 150,
		height: 25,
		template: "info"
	});
	$("#inventario_importar_guardar").bind('click', function ()
	{
		SaveData();
	});
	
	
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">

	<table cellpadding="2" cellspacing="1" style="margin-bottom:10px;">
		<tr>
			<td rowspan="5">
				<div id="inventario_importar_upload"></div>
			</td>
			<td>
				Archivo Cargado
			</td>
			<td>
				<input type="text" id="inventario_importar_nombre" />
			</td>
			<td>
				Numero de Errores
			</td>
			<td>
				<input type="text" id="inventario_importar_errores"  style="text-align:center;"/>
			</td>
			<td style="padding-left:105px;">
				<input type="button" id="inventario_importar_descargar" value="Descargar Modelo Excel">
			</td>
		</tr>
		<tr>
			<td colspan="5" rowspan="4">&nbsp;</td>
		</tr>
	</table>

	<div id="inventario_importar_items_grid"></div>
	
	<table cellpadding="2" cellspacing="1" style="margin-top:20px; text-align:center; margin-left:695px;">
		<tr>
			<td>
				<input type="button" id="inventario_importar_nuevo" value="Nuevo">
			</td>
			<td>
				<input type="button" id="inventario_importar_guardar" value="Guardar">
			</td>
		</tr>
	</table>
	
</div>
