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
	
	$("#parametros_inventario_categoria").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Categoria",
	});
	$("#parametros_inventario_categoria").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
		}
	});
	
	
	function Add_Row1()
	{
		var Val = $("#parametros_inventario_categoria").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar una Categoria!", 3);
			WaitClick_Input("parametros_inventario_categoria");
			return;
		}
		
		var datainfo = $("#parametros_inventario_items_grid1").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Categoria":Val
		}];
		
		$("#parametros_inventario_items_grid1").jqxGrid("addrow", count, datarow);
		$("#parametros_inventario_categoria").val("");
	};
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Categoria', type: 'string'},
		],
		cache: false,
		data: {"Inventario_Categoria":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Categoria"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Inventario_Categoria":true,
						"VAL":rowdata[0]["Categoria"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#parametros_inventario_items_grid1").jqxGrid("updatebounddata");
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
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
			var ID = $("#parametros_inventario_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_inventario_items_grid1").jqxGrid('getcellvalue', rowid, "Categoria");
			
			if (ID == 0)
			{
				Alerts_Box("Este Parametro es Propiedad del Sistema y no Puede ser Modificado.", 3);
				commit(false);
				return;
			}
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Inventario_Categoria":true,
					"ID":ID,
					"VAL":VAL,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data != "")
					{
						Alerts_Box("Ocurrio un Error al Intentar Actualizar los Datos!<br/>Intente luego de unos segundos...", 3);
						commit(false);
					}
					else
						commit(true);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
					commit(false);
				}
			});
		},
		deleterow: function (rowid, commit)
		{
			ClickOK = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_inventario_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
					
					if (ID == 0)
					{
						Alerts_Box("Este Parametro es Propiedad del Sistema y no Puede ser Modificado.", 3);
						commit(false);
						return;
					}
					
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Inventario_Categoria":true,
							"ID":ID,
						},
						async: true,
						success: function (data, status, xhr)
						{
							if (data != "")
							{
								Alerts_Box("Ocurrio un Error al Intentar Borrar los Datos!<br/>Intente luego de unos segundos...", 3);
								commit(false);
							}
							else
								commit(true);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							alert(textStatus+ " - " +errorThrown);
							commit(false);
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
		},
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#parametros_inventario_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 180,
		height: 200,
		source: CategoriaDataAdapter,
		enabletooltips: true,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '18%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_inventario_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_inventario_items_grid1").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_inventario_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_inventario_items_grid1").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Categoria', datafield: 'Categoria', editable: true, width: '82%', height: 20 },
		]
	});
	$("#parametros_inventario_items_grid1").on("bindingcomplete", function (event)
	{
		$("#parametros_inventario_items_grid1").jqxGrid('hidecolumn', 'ID');
		$("#parametros_inventario_items_grid1").jqxGrid('localizestrings', localizationobj);
	});
	$("#parametros_inventario_items_grid1").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		if (datafield = "Categoria")
		{
			var Categoria = $("#parametros_inventario_items_grid1").jqxGrid("getcellvalue", rowBoundIndex, "Categoria");
			GrupoSource.data = {"Inventario_Grupo": Categoria};
			var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#parametros_inventario_items_grid2").jqxGrid({source: GrupoDataAdapter});
		}
	});
	
	$("#parametros_inventario_grupo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Grupo",
	});
	$("#parametros_inventario_grupo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row2();
		}
	});
	
	function Add_Row2()
	{
		var Val = $("#parametros_inventario_grupo").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Grupo!", 3);
			WaitClick_Input("parametros_inventario_grupo");
			return;
		}
		
		var datainfo = $("#parametros_inventario_items_grid2").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Grupo":Val
		}];
		
		$("#parametros_inventario_items_grid2").jqxGrid("addrow", count, datarow);
		$("#parametros_inventario_grupo").val("");
	};
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Grupo', type: 'string'},
		],
		cache: false,
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			var rowindex = $("#parametros_inventario_items_grid1").jqxGrid('getselectedrowindex');
			var value = $("#parametros_inventario_items_grid1").jqxGrid('getcellvalue', rowindex, "Categoria");
			
			if ( rowindex < 0 ) {
				Alerts_Box("Debe Seleccionar una Categoria Valida.", 3);
				commit(false);
				return;
			}
			
			if (rowdata[0]["Grupo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Inventario_Grupo":true,
						"VAL1":value,
						"VAL2":rowdata[0]["Grupo"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#parametros_inventario_items_grid2").jqxGrid("updatebounddata");
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
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
			var ID = $("#parametros_inventario_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_inventario_items_grid2").jqxGrid('getcellvalue', rowid, "Grupo");
			
			if (ID == 0)
			{
				Alerts_Box("Este Parametro es Propiedad del Sistema y no Puede ser Modificado.", 3);
				commit(false);
				return;
			}
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Inventario_Grupo":true,
					"ID":ID,
					"VAL":VAL,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data != "")
					{
						Alerts_Box("Ocurrio un Error al Intentar Actualizar los Datos!<br/>Intente luego de unos segundos...", 3);
						commit(false);
					}
					else
						commit(true);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
					commit(false);
				}
			});
		},
		deleterow: function (rowid, commit)
		{
			ClickOK = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_inventario_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
					
					if (ID == 0)
					{
						Alerts_Box("Este Parametro es Propiedad del Sistema y no Puede ser Modificado.", 3);
						commit(false);
						return;
					}
					
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Inventario_Grupo":true,
							"ID":ID,
						},
						async: true,
						success: function (data, status, xhr)
						{
							if (data != "")
							{
								Alerts_Box("Ocurrio un Error al Intentar Borrar los Datos!<br/>Intente luego de unos segundos...", 3);
								commit(false);
							}
							else
								commit(true);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							alert(textStatus+ " - " +errorThrown);
							commit(false);
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
		},
	};
	
	$("#parametros_inventario_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 180,
		height: 200,
		enabletooltips: true,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '15%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_inventario_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_inventario_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_inventario_items_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_inventario_items_grid2").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Grupo', datafield: 'Grupo', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_inventario_items_grid2").jqxGrid('hidecolumn', 'ID');
	$("#parametros_inventario_items_grid2").jqxGrid('localizestrings', localizationobj);
	$("#parametros_inventario_items_grid2").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		if (datafield = "Grupo")
		{
			var Grupo = $("#parametros_inventario_items_grid2").jqxGrid("getcellvalue", rowBoundIndex, "Grupo");
			SubGrupoSource.data = {"Inventario_SubGrupo": Grupo};
			var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#parametros_inventario_items_grid3").jqxGrid({source: SubGrupoDataAdapter});
		}
	});
	$("#parametros_inventario_items_grid2").on("bindingcomplete", function (event)
	{
		$("#parametros_inventario_items_grid3").jqxGrid("clear");
	});  
	
	$("#parametros_inventario_subgrupo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar SubGrupo",
	});
	$("#parametros_inventario_subgrupo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row3();
		}
	});
	
	function Add_Row3()
	{
		var Val = $("#parametros_inventario_subgrupo").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un SubGrupo!", 3);
			WaitClick_Input("parametros_inventario_subgrupo");
			return;
		}
		
		var datainfo = $("#parametros_inventario_items_grid3").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"SubGrupo":Val
		}];
		
		$("#parametros_inventario_items_grid3").jqxGrid("addrow", count, datarow);
		$("#parametros_inventario_subgrupo").val("");
	};
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'SubGrupo', type: 'string'},
		],
		cache: false,
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			var rowindex = $("#parametros_inventario_items_grid2").jqxGrid('getselectedrowindex');
			var value = $("#parametros_inventario_items_grid2").jqxGrid('getcellvalue', rowindex, "Grupo");
			
			if ( rowindex < 0 ) {
				Alerts_Box("Debe Seleccionar un Grupo Valido.", 3);
				commit(false);
				return;
			}
			
			if (rowdata[0]["SubGrupo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Inventario_SubGrupo":true,
						"VAL1":value,
						"VAL2":rowdata[0]["SubGrupo"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#parametros_inventario_items_grid3").jqxGrid("updatebounddata");
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
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
			var ID = $("#parametros_inventario_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_inventario_items_grid3").jqxGrid('getcellvalue', rowid, "SubGrupo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Inventario_SubGrupo":true,
					"ID":ID,
					"VAL":VAL,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data != "")
					{
						Alerts_Box("Ocurrio un Error al Intentar Actualizar los Datos!<br/>Intente luego de unos segundos...", 3);
						commit(false);
					}
					else
						commit(true);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
					commit(false);
				}
			});
		},
		deleterow: function (rowid, commit)
		{
			ClickOK = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_inventario_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Inventario_SubGrupo":true,
							"ID":ID,
						},
						async: true,
						success: function (data, status, xhr)
						{
							if (data != "")
							{
								Alerts_Box("Ocurrio un Error al Intentar Borrar los Datos!<br/>Intente luego de unos segundos...", 3);
								commit(false);
							}
							else
								commit(true);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							alert(textStatus+ " - " +errorThrown);
							commit(false);
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
		},
	};
	
	$("#parametros_inventario_items_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 180,
		height: 200,
		enabletooltips: true,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '15%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_inventario_items_grid3").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_inventario_items_grid3").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_inventario_items_grid3").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_inventario_items_grid3").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'SubGrupo', datafield: 'SubGrupo', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_inventario_items_grid3").jqxGrid('hidecolumn', 'ID');
	$("#parametros_inventario_items_grid3").jqxGrid('localizestrings', localizationobj);
	
	$("#parametros_inventario_medida").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Unidad de Medida",
	});
	$("#parametros_inventario_medida").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row4();
		}
	});
	
	
	function Add_Row4()
	{
		var Val = $("#parametros_inventario_medida").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Tipo de Medida!", 3);
			WaitClick_Input("parametros_inventario_medida");
			return;
		}
		
		var datainfo = $("#parametros_inventario_items_grid4").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Unidad":Val
		}];
		
		$("#parametros_inventario_items_grid4").jqxGrid("addrow", count, datarow);
		$("#parametros_inventario_medida").val("");
	};
	
	var MedidaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Unidad', type: 'string'},
		],
		cache: false,
		data: {"Inventario_Unidad":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Unidad"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Inventario_Unidad":true,
						"VAL":rowdata[0]["Unidad"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#parametros_inventario_items_grid4").jqxGrid("updatebounddata");
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
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
			var ID = $("#parametros_inventario_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_inventario_items_grid4").jqxGrid('getcellvalue', rowid, "Unidad");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Inventario_Unidad":true,
					"ID":ID,
					"VAL":VAL,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data != "")
					{
						Alerts_Box("Ocurrio un Error al Intentar Actualizar los Datos!<br/>Intente luego de unos segundos...", 3);
						commit(false);
					}
					else
						commit(true);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
					commit(false);
				}
			});
		},
		deleterow: function (rowid, commit)
		{
			ClickOK = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_inventario_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Inventario_Unidad":true,
							"ID":ID,
						},
						async: true,
						success: function (data, status, xhr)
						{
							if (data != "")
							{
								Alerts_Box("Ocurrio un Error al Intentar Borrar los Datos!<br/>Intente luego de unos segundos...", 3);
								commit(false);
							}
							else
								commit(true);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							alert(textStatus+ " - " +errorThrown);
							commit(false);
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
		},
	};
	var MedidaDataAdapter = new $.jqx.dataAdapter(MedidaSource);
	
	$("#parametros_inventario_items_grid4").jqxGrid(
	{
		theme: mytheme,
		width: 180,
		height: 200,
		source: MedidaDataAdapter,
		enabletooltips: true,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '18%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_inventario_items_grid4").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_inventario_items_grid4").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_inventario_items_grid4").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_inventario_items_grid4").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Unidad de Med.', datafield: 'Unidad', editable: true, width: '82%', height: 20 },
		]
	});
	$("#parametros_inventario_items_grid4").on("bindingcomplete", function (event)
	{
		$("#parametros_inventario_items_grid4").jqxGrid('hidecolumn', 'ID');
		$("#parametros_inventario_items_grid4").jqxGrid('localizestrings', localizationobj);
	});
	
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div style="float: left; margin-left:0px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Categoria
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_inventario_categoria"/>
		</div>
		<div id="parametros_inventario_items_grid1"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Grupo
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_inventario_grupo"/>
		</div>
		<div id="parametros_inventario_items_grid2"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			SubGrupo
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_inventario_subgrupo"/>
		</div>
		<div id="parametros_inventario_items_grid3"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Unidades de Medida
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_inventario_medida"/>
		</div>
		<div id="parametros_inventario_items_grid4"></div>
	</div>
</div>
