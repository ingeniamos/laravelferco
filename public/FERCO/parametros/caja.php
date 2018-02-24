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
	
	/*
	$("#parametros_caja_categoria").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Categoria",
	});
	$("#parametros_caja_categoria").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
		}
	});
	
	function Add_Row1()
	{
		var Val = $("#parametros_caja_categoria").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar una Categoria!", 3);
			WaitClick_Input("parametros_caja_categoria");
			return;
		}
		
		var datainfo = $("#parametros_caja_items_grid1").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Categoria":Val
		}];
		
		$("#parametros_caja_items_grid1").jqxGrid("addrow", count, datarow);
		$("#parametros_caja_categoria").val("");
	};
	*/
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Categoria', type: 'string'},
		],
		cache: false,
		data: {"Caja_Categoria":true},
		url: "modulos/parametros.php",
		async: true,
		/*
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Categoria"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Caja_Categoria":true,
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
							$("#parametros_caja_items_grid1").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_caja_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_caja_items_grid1").jqxGrid('getcellvalue', rowid, "Categoria");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Caja_Categoria":true,
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
					
					var ID = $("#parametros_caja_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Caja_Categoria":true,
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
		*/
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#parametros_caja_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 180,
		height: 226,
		source: CategoriaDataAdapter,
		enabletooltips: true,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			/*{
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
					var selectedrowindex = $("#parametros_caja_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_caja_items_grid1").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_caja_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_caja_items_grid1").jqxGrid('deleterow', id);
					}
				}
			},*/
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Categoria', datafield: 'Categoria', editable: false, width: '100%', height: 20 },
		]
	});
	$("#parametros_caja_items_grid1").on("bindingcomplete", function (event)
	{
		$("#parametros_caja_items_grid1").jqxGrid('hidecolumn', 'ID');
		$("#parametros_caja_items_grid1").jqxGrid('localizestrings', localizationobj);
	});
	$("#parametros_caja_items_grid1").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		if (datafield = "Categoria")
		{
			var Categoria = $("#parametros_caja_items_grid1").jqxGrid("getcellvalue", rowBoundIndex, "Categoria");
			GrupoSource.data = {"Caja_Grupo": Categoria};
			var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#parametros_caja_items_grid2").jqxGrid({source: GrupoDataAdapter});
		}
	});
	
	$("#parametros_caja_grupo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 200,
		placeHolder: "Ingresar Grupo",
	});
	$("#parametros_caja_grupo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row2();
		}
	});
	
	function Add_Row2()
	{
		var Val = $("#parametros_caja_grupo").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Grupo!", 3);
			WaitClick_Input("parametros_caja_grupo");
			return;
		}
		
		var datainfo = $("#parametros_caja_items_grid2").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Grupo":Val
		}];
		
		$("#parametros_caja_items_grid2").jqxGrid("addrow", count, datarow);
		$("#parametros_caja_grupo").val("");
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
			var rowindex = $("#parametros_caja_items_grid1").jqxGrid('getselectedrowindex');
			var value = $("#parametros_caja_items_grid1").jqxGrid('getcellvalue', rowindex, "Categoria");
			
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
						"Parametros_Agregar_Caja_Grupo":true,
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
							$("#parametros_caja_items_grid2").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_caja_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_caja_items_grid2").jqxGrid('getcellvalue', rowid, "Grupo");
			
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
					"Parametros_Actualizar_Caja_Grupo":true,
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
					
					var ID = $("#parametros_caja_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
					
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
							"Parametros_Borrar_Caja_Grupo":true,
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
	
	$("#parametros_caja_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 200,
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
					var selectedrowindex = $("#parametros_caja_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_caja_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_caja_items_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_caja_items_grid2").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Grupo', datafield: 'Grupo', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_caja_items_grid2").jqxGrid('hidecolumn', 'ID');
	$("#parametros_caja_items_grid2").jqxGrid('localizestrings', localizationobj);
	$("#parametros_caja_items_grid2").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		if (datafield = "Grupo")
		{
			var Grupo = $("#parametros_caja_items_grid2").jqxGrid("getcellvalue", rowBoundIndex, "Grupo");
			SubGrupoSource.data = {"Caja_SubGrupo": Grupo};
			var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#parametros_caja_items_grid3").jqxGrid({source: SubGrupoDataAdapter});
			SubGrupoSource2.data = {"Caja_SubGrupo2": Grupo};
			var SubGrupoDataAdapter2 = new $.jqx.dataAdapter(SubGrupoSource2);
			$("#parametros_caja_items_grid4").jqxGrid({source: SubGrupoDataAdapter2});
		}
	});
	$("#parametros_caja_items_grid2").on("bindingcomplete", function (event)
	{
		$("#parametros_caja_items_grid3").jqxGrid("clear");
		$("#parametros_caja_items_grid4").jqxGrid("clear");
	});  
	
	$("#parametros_caja_subgrupo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 200,
		placeHolder: "Ingresar SubGrupo",
	});
	$("#parametros_caja_subgrupo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row3();
		}
	});
	
	function Add_Row3()
	{
		var Val = $("#parametros_caja_subgrupo").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un SubGrupo!", 3);
			WaitClick_Input("parametros_caja_subgrupo");
			return;
		}
		
		var datainfo = $("#parametros_caja_items_grid3").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"SubGrupo":Val
		}];
		
		$("#parametros_caja_items_grid3").jqxGrid("addrow", count, datarow);
		$("#parametros_caja_subgrupo").val("");
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
			var rowindex = $("#parametros_caja_items_grid2").jqxGrid('getselectedrowindex');
			var value = $("#parametros_caja_items_grid2").jqxGrid('getcellvalue', rowindex, "Grupo");
			
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
						"Parametros_Agregar_Caja_SubGrupo":true,
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
							$("#parametros_caja_items_grid3").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_caja_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_caja_items_grid3").jqxGrid('getcellvalue', rowid, "SubGrupo");
			
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
					"Parametros_Actualizar_Caja_SubGrupo":true,
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
					
					var ID = $("#parametros_caja_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
					
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
							"Parametros_Borrar_Caja_SubGrupo":true,
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
	
	$("#parametros_caja_items_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 200,
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
					var selectedrowindex = $("#parametros_caja_items_grid3").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_caja_items_grid3").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_caja_items_grid3").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_caja_items_grid3").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'SubGrupo', datafield: 'SubGrupo', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_caja_items_grid3").jqxGrid('hidecolumn', 'ID');
	$("#parametros_caja_items_grid3").jqxGrid('localizestrings', localizationobj);
	
	$("#parametros_caja_subgrupo2").jqxInput({
		theme: mytheme,
		height: 20,
		width: 200,
		placeHolder: "Ingresar SubGrupo Niv. 2",
	});
	$("#parametros_caja_subgrupo2").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row4();
		}
	});
	
	function Add_Row4()
	{
		var Val = $("#parametros_caja_subgrupo2").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un SubGrupo Nivel 2!", 3);
			WaitClick_Input("parametros_caja_subgrupo2");
			return;
		}
		
		var datainfo = $("#parametros_caja_items_grid4").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"SubGrupo2":Val
		}];
		
		$("#parametros_caja_items_grid4").jqxGrid("addrow", count, datarow);
		$("#parametros_caja_subgrupo2").val("");
	};
	
	var SubGrupoSource2 =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'SubGrupo2', type: 'string'},
		],
		cache: false,
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			var rowindex = $("#parametros_caja_items_grid2").jqxGrid('getselectedrowindex');
			var value = $("#parametros_caja_items_grid2").jqxGrid('getcellvalue', rowindex, "Grupo");
			
			if ( rowindex < 0 ) {
				Alerts_Box("Debe Seleccionar un Grupo Valido.", 3);
				commit(false);
				return;
			}
			
			if (rowdata[0]["SubGrupo2"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Caja_SubGrupo2":true,
						"VAL1":value,
						"VAL2":rowdata[0]["SubGrupo2"],
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
							$("#parametros_caja_items_grid4").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_caja_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_caja_items_grid4").jqxGrid('getcellvalue', rowid, "SubGrupo2");
			
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
					"Parametros_Actualizar_Caja_SubGrupo2":true,
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
					
					var ID = $("#parametros_caja_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
					
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
							"Parametros_Borrar_Caja_SubGrupo2":true,
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
	
	$("#parametros_caja_items_grid4").jqxGrid(
	{
		theme: mytheme,
		width: 200,
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
					var selectedrowindex = $("#parametros_caja_items_grid4").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_caja_items_grid4").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_caja_items_grid4").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_caja_items_grid4").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'SubGrupo Niv. 2', datafield: 'SubGrupo2', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_caja_items_grid4").jqxGrid('hidecolumn', 'ID');
	$("#parametros_caja_items_grid4").jqxGrid('localizestrings', localizationobj);
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#parametros_caja_bancos").jqxInput(
	{
		theme: mytheme,
		width: 350,
		height: 20,
		placeHolder: "Ingresar Banco",
	});
	$("#parametros_caja_bancos").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row5();
		}
	});
	
	function Add_Row5()
	{
		var Val = $("#parametros_caja_bancos").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Barrio!", 3);
			WaitClick_Input("parametros_caja_bancos");
			return;
		}
		
		var datainfo = $("#parametros_caja_items_grid5").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Banco":Val,
			"Cuenta":""
		}];
		
		$("#parametros_caja_items_grid5").jqxGrid("addrow", count, datarow);
		$("#parametros_caja_bancos").val("");
	};
	
	var BancoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Banco', type: 'string'},
			{ name: 'Cuenta', type: 'string'},
		],
		cache: false,
		data: {"Caja_Banco":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Banco"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Banco":true,
						"VAL":rowdata[0]["Banco"],
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
							$("#parametros_caja_items_grid5").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_caja_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_caja_items_grid5").jqxGrid('getcellvalue', rowid, "Banco");
			var VAL2 = $("#parametros_caja_items_grid5").jqxGrid('getcellvalue', rowid, "Cuenta");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Banco":true,
					"ID":ID,
					"VAL1":VAL1,
					"VAL2":VAL2,
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
					
					var ID = $("#parametros_caja_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Banco":true,
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
	var BancoDataAdapter = new $.jqx.dataAdapter(BancoSource);
	
	$("#parametros_caja_items_grid5").jqxGrid(
	{
		theme: mytheme,
		width: 350,
		height: 200,
		source: BancoDataAdapter,
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
				width: '8%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_caja_items_grid5").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_caja_items_grid5").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_caja_items_grid5").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_caja_items_grid5").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Banco', datafield: 'Banco', editable: false, width: '60%', height: 20 },
			{ text: 'Cuenta', datafield: 'Cuenta', editable: true, width: '32%', height: 20 },
		]
	});
	$("#parametros_caja_items_grid5").on("bindingcomplete", function (event) {
		$("#parametros_caja_items_grid5").jqxGrid('hidecolumn', 'ID');
		$("#parametros_caja_items_grid5").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#parametros_caja_descuentos").jqxInput(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		placeHolder: "Ingresar Descuento",
	});
	$("#parametros_caja_descuentos").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row6();
		}
	});
	
	function Add_Row6()
	{
		var Val = $("#parametros_caja_descuentos").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Descuento!", 3);
			WaitClick_Input("parametros_caja_descuentos");
			return;
		}
		
		var datainfo = $("#parametros_caja_items_grid6").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Dcto":Val,
		}];
		
		$("#parametros_caja_items_grid6").jqxGrid("addrow", count, datarow);
		$("#parametros_caja_descuentos").val("");
	};
	
	var DescuentoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Dcto', type: 'string'},
		],
		cache: false,
		data: {"Caja_Dcto":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Dcto"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Caja_Descuento":true,
						"VAL":rowdata[0]["Dcto"],
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
							$("#parametros_caja_items_grid6").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_caja_items_grid6").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_caja_items_grid6").jqxGrid('getcellvalue', rowid, "Dcto");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Caja_Descuento":true,
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
					
					var ID = $("#parametros_caja_items_grid6").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Caja_Descuento":true,
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
	var DescuentoDataAdapter = new $.jqx.dataAdapter(DescuentoSource);
	
	$("#parametros_caja_items_grid6").jqxGrid(
	{
		theme: mytheme,
		width: 250,
		height: 200,
		source: DescuentoDataAdapter,
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
				width: '10%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_caja_items_grid6").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_caja_items_grid6").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_caja_items_grid6").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_caja_items_grid6").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Descuento', datafield: 'Dcto', editable: true, width: '90%', height: 20 },
		]
	});
	$("#parametros_caja_items_grid6").on("bindingcomplete", function (event) {
		$("#parametros_caja_items_grid6").jqxGrid('hidecolumn', 'ID');
		$("#parametros_caja_items_grid6").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#parametros_caja_maquinaria").jqxInput(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		placeHolder: "Ingresar Estado",
	});
	$("#parametros_caja_maquinaria").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row7();
		}
	});
	
	function Add_Row7()
	{
		var Val = $("#parametros_caja_maquinaria").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Estado!", 3);
			WaitClick_Input("parametros_caja_maquinaria");
			return;
		}
		
		var datainfo = $("#parametros_caja_items_grid7").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Tipo":Val,
		}];
		
		$("#parametros_caja_items_grid7").jqxGrid("addrow", count, datarow);
		$("#parametros_caja_maquinaria").val("");
	};
	
	var EstadosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Tipo', type: 'string'},
		],
		cache: false,
		data: {"Diagnostico":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Tipo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Estados":true,
						"VAL":rowdata[0]["Tipo"],
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
							$("#parametros_caja_items_grid7").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_caja_items_grid7").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_caja_items_grid7").jqxGrid('getcellvalue', rowid, "Tipo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Estados":true,
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
					
					var ID = $("#parametros_caja_items_grid7").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Estados":true,
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
	var EstadosDataAdapter = new $.jqx.dataAdapter(EstadosSource);
	
	$("#parametros_caja_items_grid7").jqxGrid(
	{
		theme: mytheme,
		width: 250,
		height: 200,
		source: EstadosDataAdapter,
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
				width: '10%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_caja_items_grid7").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_caja_items_grid7").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_caja_items_grid7").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_caja_items_grid7").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Estado', datafield: 'Tipo', editable: true, width: '90%', height: 20 },
		]
	});
	$("#parametros_caja_items_grid7").on("bindingcomplete", function (event) {
		$("#parametros_caja_items_grid7").jqxGrid('hidecolumn', 'ID');
		$("#parametros_caja_items_grid7").jqxGrid('localizestrings', localizationobj);
	});
	
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div style="float: left; margin-left:0px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Categoria
		</p>
		<!--<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_caja_categoria"/>
		</div>-->
		<div id="parametros_caja_items_grid1"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Grupo
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_caja_grupo"/>
		</div>
		<div id="parametros_caja_items_grid2"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			SubGrupo
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_caja_subgrupo"/>
		</div>
		<div id="parametros_caja_items_grid3"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			SubGrupo Nivel 2
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_caja_subgrupo2"/>
		</div>
		<div id="parametros_caja_items_grid4"></div>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div style="float: left; margin-left:0px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Cuentas Bancarias
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_caja_bancos"/>
		</div>
		<div id="parametros_caja_items_grid5"></div>
	</div>
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Descuentos
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_caja_descuentos"/>
		</div>
		<div id="parametros_caja_items_grid6"></div>
	</div>
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Estados de Maquinaria/Vehiculos
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_caja_maquinaria"/>
		</div>
		<div id="parametros_caja_items_grid7"></div>
	</div>
</div>

