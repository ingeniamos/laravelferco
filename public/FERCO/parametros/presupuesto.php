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
	
	$("#parametros_presupuesto_db_nombre").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
	});
	$("#parametros_presupuesto_db_nombre").keyup(function(event)
	{
		if(event.which == 13)
		{
			$("#parametros_presupuesto_db_notas").focus();
		}
	});
	
	$("#parametros_presupuesto_db_notas").jqxInput({
		theme: mytheme,
		height: 20,
		width: 215,
		placeHolder: "Ingresar Nota/Descripccion",
	});
	$("#parametros_presupuesto_db_notas").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
		}
	});
	
	function Add_Row1()
	{
		var Val1 = $("#parametros_presupuesto_db_nombre").val();
		var Val2 = $("#parametros_presupuesto_db_notas").val();
		
		if ( Val1 < 0 || Val1 == "") {
			Alerts_Box("Favor Ingresar un Nombre de Base de Datos.", 3);
			WaitClick_Input("parametros_presupuesto_db_nombre");
			return;
		}
		
		if ( Val2 < 0 || Val2 == "") {
			Alerts_Box("Favor Ingresar una Nota/Descripcion.", 3);
			WaitClick_Input("parametros_presupuesto_db_notas");
			return;
		}
		
		var datainfo = $("#parametros_presupuesto_items_grid3").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Clasificacion":Val1,
			"Notas":Val2,
		}];
		
		$("#parametros_presupuesto_items_grid1").jqxGrid("addrow", count, datarow);
		$("#parametros_presupuesto_db_nombre").val("");
		$("#parametros_presupuesto_db_notas").val("");
	};
	
	var DBSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Clasificacion', type: 'string'},
			{ name: 'Notas', type: 'string'},
		],
		data: {"Presupuesto_Clasificacion":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Clasificacion"] != "")
			{
				$("#Loading_Mess").html("Procesando Solicitud...");
				$("#Loading").show();
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Presupuesto_Clasificacion":true,
						"Clasificacion":rowdata[0]["Clasificacion"],
						"Nota":rowdata[0]["Notas"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						if (data == "EXIST")
						{
							Alerts_Box("El Nobre ingresado ya Existe en la Base de Datos.", 3);
							commit(false);
						}
						else if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#parametros_presupuesto_items_grid1").jqxGrid("updatebounddata");
						}
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						commit(false);
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
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
			var ID = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowid, "Clasificacion");
			var VAL2 = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowid, "Notas");
			
			$("#Loading_Mess").html("Procesando Solicitud...");
			$("#Loading").show();
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Presupuesto_Clasificacion":true,
					"ID":ID,
					"VAL1":VAL1,
					"VAL2":VAL2,
				},
				async: true,
				success: function (data, status, xhr)
				{
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					if (data == "EXIST")
					{
						Alerts_Box("El Nobre ingresado ya Existe en la Base de Datos.", 3);
						commit(false);
					}
					else if (data != "")
					{
						alert(data)
						Alerts_Box("Ocurrio un Error al Intentar Actualizar los Datos!<br/>Intente luego de unos segundos...", 3);
						commit(false);
					}
					else
						commit(true);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					commit(false);
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
					+"Error: "+errorThrown, 3);
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
					
					var ID = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
					
					$("#Loading_Mess").html("Procesando Solicitud...");
					$("#Loading").show();
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Presupuesto_Clasificacion":true,
							"ID":ID,
						},
						async: true,
						success: function (data, status, xhr)
						{
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							if (data != "")
							{
								Alerts_Box("Ocurrio un Error al Intentar Borrar los Datos!<br/>Intente luego de unos segundos...", 3);
								commit(false);
							}
							else
								commit(true);
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							commit(false);
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
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
		},
	};
	var DBDataAdapter = new $.jqx.dataAdapter(DBSource);
	
	$("#parametros_presupuesto_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 320,
		height: 200,
		source: DBDataAdapter,
		enabletooltips: true,
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
					var selectedrowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_presupuesto_items_grid1").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_presupuesto_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_presupuesto_items_grid1").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Nombre', datafield: 'Clasificacion', editable: true, width: '30%', height: 20 },
			{ text: 'Notas', datafield: 'Notas', editable: true, width: '62%', height: 20 },
		]
	});
	$("#parametros_presupuesto_items_grid1").on("bindingcomplete", function (event)
	{
		$("#parametros_presupuesto_items_grid1").jqxGrid('hidecolumn', 'ID');
		$("#parametros_presupuesto_items_grid1").jqxGrid('localizestrings', localizationobj);
	});
	$("#parametros_presupuesto_items_grid1").on("rowselect", function (event)
	{
		$("#parametros_presupuesto_items_grid2").jqxGrid("clearselection");
		$("#parametros_presupuesto_items_grid3").jqxGrid("clearselection");
		$("#parametros_presupuesto_items_grid4").jqxGrid("clearselection");
	});
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Categoria', type: 'string'},
		],
		data: {"Presupuesto_Categoria":true},
		url: "modulos/parametros.php",
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#parametros_presupuesto_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 180,
		height: 226,
		source: CategoriaDataAdapter,
		enabletooltips: true,
		editable: false,
		columns:
		[
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Categoria', datafield: 'Categoria', editable: false, width: '100%', height: 20 },
		]
	});
	$("#parametros_presupuesto_items_grid2").on("bindingcomplete", function (event)
	{
		$("#parametros_presupuesto_items_grid2").jqxGrid('hidecolumn', 'ID');
		$("#parametros_presupuesto_items_grid2").jqxGrid('localizestrings', localizationobj);
	});
	$("#parametros_presupuesto_items_grid2").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		if (datafield = "Categoria")
		{
			var rowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
			var Clasificacion = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar un Clasificacion.", 3);
				return;
			}
			
			var Categoria = $("#parametros_presupuesto_items_grid2").jqxGrid("getcellvalue", rowBoundIndex, "Categoria");
			GrupoSource.data = {
				"Presupuesto_Grupo": true,
				"Clasificacion": Clasificacion,
				"Categoria": Categoria,
			};
			var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#parametros_presupuesto_items_grid3").jqxGrid("clearselection");
			$("#parametros_presupuesto_items_grid3").jqxGrid({source: GrupoDataAdapter});
			$("#parametros_presupuesto_items_grid4").jqxGrid("clearselection");
		}
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_presupuesto_grupo_codigo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 35,
	});
	$("#parametros_presupuesto_grupo_codigo").keyup(function(event)
	{
		if(event.which == 13)
		{
			$("#parametros_presupuesto_grupo").focus();
		}
	});
	
	$("#parametros_presupuesto_grupo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Grupo",
	});
	$("#parametros_presupuesto_grupo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row2();
		}
	});
	
	function Add_Row2()
	{
		var Val1 = $("#parametros_presupuesto_grupo_codigo").val();
		var Val2 = $("#parametros_presupuesto_grupo").val();
		
		if ( Val1 < 0 || Val1 == "") {
			Alerts_Box("Favor Ingresar un Codigo!", 3);
			WaitClick_Input("parametros_presupuesto_grupo_codigo");
			return;
		}
		
		if ( Val2 < 0 || Val2 == "") {
			Alerts_Box("Favor Ingresar un Grupo!", 3);
			WaitClick_Input("parametros_presupuesto_grupo");
			return;
		}
		
		var datainfo = $("#parametros_presupuesto_items_grid3").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Codigo":Val1,
			"Grupo":Val2,
		}];
		
		$("#parametros_presupuesto_items_grid3").jqxGrid("addrow", count, datarow);
		$("#parametros_presupuesto_grupo_codigo").val("");
		$("#parametros_presupuesto_grupo").val("");
	};
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Codigo', type: 'string'},
			{ name: 'Grupo', type: 'string'},
		],
		cache: false,
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			var rowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
			var Clasificacion = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar un Clasificacion.", 3);
				commit(false);
				return;
			}
			
			var rowindex = $("#parametros_presupuesto_items_grid2").jqxGrid('getselectedrowindex');
			var Categoria = $("#parametros_presupuesto_items_grid2").jqxGrid('getcellvalue', rowindex, "Categoria");
			
			if (rowindex < 0) {
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
						"Parametros_Agregar_Presupuesto_Grupo":true,
						"Clasificacion":Clasificacion,
						"VAL1":Categoria,
						"VAL2":rowdata[0]["Codigo"],
						"VAL3":rowdata[0]["Grupo"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						if (data == "EXIST")
						{
							Alerts_Box("El Codigo ingresado ya Existe en la Base de Datos.", 3);
							commit(false);
						}
						else if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#parametros_presupuesto_items_grid3").jqxGrid("updatebounddata");
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
			var rowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
			var Clasificacion = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar un Clasificacion.", 3);
				commit(false);
				return;
			}
			
			var rowindex = $("#parametros_presupuesto_items_grid2").jqxGrid('getselectedrowindex');
			var Categoria = $("#parametros_presupuesto_items_grid2").jqxGrid('getcellvalue', rowindex, "Categoria");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar una Categoria Valida.", 3);
				commit(false);
				return;
			}
			
			var ID = $("#parametros_presupuesto_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_presupuesto_items_grid3").jqxGrid('getcellvalue', rowid, "Codigo");
			var VAL2 = $("#parametros_presupuesto_items_grid3").jqxGrid('getcellvalue', rowid, "Grupo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Presupuesto_Grupo":true,
					"Clasificacion":Clasificacion,
					"Categoria":Categoria,
					"ID":ID,
					"VAL1":VAL1,
					"VAL2":VAL2,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data == "EXIST")
					{
						Alerts_Box("El Codigo ingresado ya Existe en la Base de Datos.", 3);
						commit(false);
					}
					else if (data != "")
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
					
					var rowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
					var Clasificacion = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
					
					if (rowindex < 0) {
						Alerts_Box("Debe Seleccionar un Clasificacion.", 3);
						commit(false);
						return;
					}
					
					var ID = $("#parametros_presupuesto_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Presupuesto_Grupo":true,
							"Clasificacion":Clasificacion,
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
	
	$("#parametros_presupuesto_items_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 220,
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
				width: '12%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_presupuesto_items_grid3").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_presupuesto_items_grid3").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_presupuesto_items_grid3").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_presupuesto_items_grid3").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Cod.', datafield: 'Codigo', editable: true, width: '20%', height: 20 },
			{ text: 'Grupo', datafield: 'Grupo', editable: true, width: '68%', height: 20 },
		]
	});
	$("#parametros_presupuesto_items_grid3").jqxGrid('hidecolumn', 'ID');
	$("#parametros_presupuesto_items_grid3").jqxGrid('localizestrings', localizationobj);
	$("#parametros_presupuesto_items_grid3").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		if (datafield = "Grupo")
		{
			var rowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
			var Clasificacion = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar un Clasificacion.", 3);
				return;
			}
			
			var rowindex2 = $("#parametros_presupuesto_items_grid2").jqxGrid('getselectedrowindex');
			var Categoria = $("#parametros_presupuesto_items_grid2").jqxGrid('getcellvalue', rowindex2, "Categoria");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar un Categoria.", 3);
				return;
			}
			
			var Grupo = $("#parametros_presupuesto_items_grid3").jqxGrid("getcellvalue", rowBoundIndex, "Codigo");
			SubGrupoSource.data = {
				"Presupuesto_SubGrupo": true,
				"P_Clasificacion": Clasificacion,
				"P_Categoria": Categoria,
				"P_Grupo": Grupo,
			};
			var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#parametros_presupuesto_items_grid4").jqxGrid("clearselection");
			$("#parametros_presupuesto_items_grid4").jqxGrid({source: SubGrupoDataAdapter});
		}
	});
	$("#parametros_presupuesto_items_grid3").on("bindingcomplete", function (event)
	{
		$("#parametros_presupuesto_items_grid4").jqxGrid("clear");
	});  
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_presupuesto_subgrupo_codigo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 35,
	});
	$("#parametros_presupuesto_subgrupo_codigo").keyup(function(event)
	{
		if(event.which == 13)
		{
			$("#parametros_presupuesto_subgrupo").focus();
		}
	});
	
	$("#parametros_presupuesto_subgrupo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar SubGrupo",
	});
	$("#parametros_presupuesto_subgrupo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row3();
		}
	});
	
	function Add_Row3()
	{
		var Val1 = $("#parametros_presupuesto_subgrupo_codigo").val();
		var Val2 = $("#parametros_presupuesto_subgrupo").val();
		
		if ( Val1 < 0 || Val1 == "") {
			Alerts_Box("Favor Ingresar un Codigo!", 3);
			WaitClick_Input("parametros_presupuesto_subgrupo_codigo");
			return;
		}
		
		if ( Val2 < 0 || Val2 == "") {
			Alerts_Box("Favor Ingresar un SubGrupo!", 3);
			WaitClick_Input("parametros_presupuesto_subgrupo");
			return;
		}
		
		var datainfo = $("#parametros_presupuesto_items_grid4").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Codigo":Val1,
			"SubGrupo":Val2,
		}];
		
		$("#parametros_presupuesto_items_grid4").jqxGrid("addrow", count, datarow);
		$("#parametros_presupuesto_subgrupo_codigo").val("");
		$("#parametros_presupuesto_subgrupo").val("");
	};
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Codigo', type: 'string'},
			{ name: 'SubGrupo', type: 'string'},
		],
		cache: false,
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			var rowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
			var Clasificacion = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar un Clasificacion.", 3);
				commit(false);
				return;
			}
			
			var rowindex = $("#parametros_presupuesto_items_grid2").jqxGrid('getselectedrowindex');
			var Categoria = $("#parametros_presupuesto_items_grid2").jqxGrid('getcellvalue', rowindex, "Categoria");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar una Categoria Valido.", 3);
				commit(false);
				return;
			}
			
			var rowindex = $("#parametros_presupuesto_items_grid3").jqxGrid('getselectedrowindex');
			var Grupo = $("#parametros_presupuesto_items_grid3").jqxGrid('getcellvalue', rowindex, "Codigo");
			
			if (rowindex < 0) {
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
						"Parametros_Agregar_Presupuesto_SubGrupo":true,
						"Clasificacion":Clasificacion,
						"Grupo":Grupo,
						"Categoria":Categoria,
						"VAL1":rowdata[0]["Codigo"],
						"VAL2":rowdata[0]["SubGrupo"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						if (data == "EXIST")
						{
							Alerts_Box("El Codigo ingresado ya Existe en la Base de Datos.", 3);
							commit(false);
						}
						else if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#parametros_presupuesto_items_grid4").jqxGrid("updatebounddata");
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
			var rowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
			var Clasificacion = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar un Clasificacion.", 3);
				commit(false);
				return;
			}
			
			var rowindex = $("#parametros_presupuesto_items_grid2").jqxGrid('getselectedrowindex');
			var Categoria = $("#parametros_presupuesto_items_grid2").jqxGrid('getcellvalue', rowindex, "Categoria");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar una Categoria Valida.", 3);
				commit(false);
				return;
			}
			
			var rowindex = $("#parametros_presupuesto_items_grid3").jqxGrid('getselectedrowindex');
			var Grupo = $("#parametros_presupuesto_items_grid3").jqxGrid('getcellvalue', rowindex, "Codigo");
			
			if (rowindex < 0) {
				Alerts_Box("Debe Seleccionar un Grupo Valido.", 3);
				commit(false);
				return;
			}
			
			var ID = $("#parametros_presupuesto_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_presupuesto_items_grid4").jqxGrid('getcellvalue', rowid, "Codigo");
			var VAL2 = $("#parametros_presupuesto_items_grid4").jqxGrid('getcellvalue', rowid, "SubGrupo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Presupuesto_SubGrupo":true,
					"Clasificacion":Clasificacion,
					"Categoria":Categoria,
					"Grupo":Grupo,
					"ID":ID,
					"VAL1":VAL1,
					"VAL2":VAL2,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data == "EXIST")
					{
						Alerts_Box("El Codigo ingresado ya Existe en la Base de Datos.", 3);
						commit(false);
					}
					else if (data != "")
					{
						alert(data)
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
					
					var rowindex = $("#parametros_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
					var Clasificacion = $("#parametros_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
					
					if (rowindex < 0) {
						Alerts_Box("Debe Seleccionar un Clasificacion.", 3);
						commit(false);
						return;
					}
					
					var ID = $("#parametros_presupuesto_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Presupuesto_SubGrupo":true,
							"Clasificacion":Clasificacion,
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
	
	$("#parametros_presupuesto_items_grid4").jqxGrid(
	{
		theme: mytheme,
		width: 220,
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
				width: '12%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_presupuesto_items_grid4").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_presupuesto_items_grid4").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_presupuesto_items_grid4").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_presupuesto_items_grid4").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Cod.', datafield: 'Codigo', editable: true, width: '20%', height: 20 },
			{ text: 'SubGrupo', datafield: 'SubGrupo', editable: true, width: '68%', height: 20 },
		]
	});
	$("#parametros_presupuesto_items_grid4").jqxGrid('hidecolumn', 'ID');
	$("#parametros_presupuesto_items_grid4").jqxGrid('localizestrings', localizationobj);
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_presupuesto_medida").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Unidad de Medida",
	});
	$("#parametros_presupuesto_medida").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row4();
		}
	});
	
	function Add_Row4()
	{
		var Val = $("#parametros_presupuesto_medida").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Tipo de Medida!", 3);
			WaitClick_Input("parametros_presupuesto_medida");
			return;
		}
		
		var datainfo = $("#parametros_presupuesto_items_grid5").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Unidad":Val
		}];
		
		$("#parametros_presupuesto_items_grid5").jqxGrid("addrow", count, datarow);
		$("#parametros_presupuesto_medida").val("");
	};
	
	var MedidaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Unidad', type: 'string'},
		],
		cache: false,
		data: {"Presupuesto_Unidad":true},
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
						"Parametros_Agregar_Presupuesto_Unidad":true,
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
							$("#parametros_presupuesto_items_grid5").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_presupuesto_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_presupuesto_items_grid5").jqxGrid('getcellvalue', rowid, "Unidad");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Presupuesto_Unidad":true,
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
					
					var ID = $("#parametros_presupuesto_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Presupuesto_Unidad":true,
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
	
	$("#parametros_presupuesto_items_grid5").jqxGrid(
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
					var selectedrowindex = $("#parametros_presupuesto_items_grid5").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_presupuesto_items_grid5").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_presupuesto_items_grid5").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_presupuesto_items_grid5").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Unidad de Med.', datafield: 'Unidad', editable: true, width: '82%', height: 20 },
		]
	});
	$("#parametros_presupuesto_items_grid5").on("bindingcomplete", function (event)
	{
		$("#parametros_presupuesto_items_grid5").jqxGrid('hidecolumn', 'ID');
		$("#parametros_presupuesto_items_grid5").jqxGrid('localizestrings', localizationobj);
	});
	
	var TipoValues = [
		{"Tipo":"SC"},
		{"Tipo":"PD"},
		{"Tipo":"SCC"},
	];
	
	$("#parametros_presupuesto_concepto").jqxInput({
		theme: mytheme,
		width: 300,
		height: 20,
		placeHolder: "Ingresar Concepto",
	});
	$("#parametros_presupuesto_concepto").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row5();
		}
	});
	
	function Add_Row5()
	{
		var Val = $("#parametros_presupuesto_concepto").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Concepto!", 3);
			WaitClick_Input("parametros_presupuesto_concepto");
			return;
		}
		
		var datainfo = $("#parametros_presupuesto_items_grid6").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Concepto":Val,
			"Uso":0,
			"Valor":0,
			"Tipo":"SC",
			"Fijo":false
		}];
		
		$("#parametros_presupuesto_items_grid6").jqxGrid("addrow", count, datarow);
		$("#parametros_presupuesto_concepto").val("");
	};
	
	var ConceptoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Concepto', type: 'string'},
			{ name: 'Uso', type: 'decimal'},
			{ name: 'Valor', type: 'decimal'},
			{ name: 'Fijo', type: 'bool'},
			{ name: 'Tipo', type: 'string'},
		],
		cache: false,
		data: {"Presupuesto_Conceptos":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Concepto"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Presupuesto_Concepto":true,
						"VAL":rowdata[0]["Concepto"],
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
							$("#parametros_presupuesto_items_grid6").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_presupuesto_items_grid6").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_presupuesto_items_grid6").jqxGrid('getcellvalue', rowid, "Concepto");
			var VAL2 = $("#parametros_presupuesto_items_grid6").jqxGrid('getcellvalue', rowid, "Uso");
			var VAL3 = $("#parametros_presupuesto_items_grid6").jqxGrid('getcellvalue', rowid, "Valor");
			var VAL4 = $("#parametros_presupuesto_items_grid6").jqxGrid('getcellvalue', rowid, "Tipo");
			var VAL5 = $("#parametros_presupuesto_items_grid6").jqxGrid('getcellvalue', rowid, "Fijo");
			
			//alert(ID+" - "+VAL1+" - "+VAL2+" - "+VAL3+" - "+VAL4+" - "+VAL5)
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Presupuesto_Concepto":true,
					"ID":ID,
					"VAL1":VAL1,
					"VAL2":VAL2,
					"VAL3":VAL3,
					"VAL4":VAL4,
					"VAL5":VAL5,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_presupuesto_items_grid6").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Presupuesto_Concepto":true,
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
	var ConceptoDataAdapter = new $.jqx.dataAdapter(ConceptoSource);
	
	$("#parametros_presupuesto_items_grid6").jqxGrid(
	{
		theme: mytheme,
		width: 575,
		height: 200,
		source: ConceptoDataAdapter,
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
				width: '5%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_presupuesto_items_grid6").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_presupuesto_items_grid6").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_presupuesto_items_grid6").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_presupuesto_items_grid6").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Concepto', datafield: 'Concepto', editable: true, width: '40%', height: 20 },
			{
				text: 'Uso',
				datafield: 'Uso',
				editable: true,
				width: '12%',
				height: 20,
				cellsalign: 'center',
				cellsformat: 'p2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, spinButtons: false });
				}
			},
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: true,
				width: '25%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18, spinButtons: false });
				}
			},
			//{ text: 'Tipo', datafield: 'Tipo', editable: true, width: '10%', height: 20, cellsalign: 'center', },
			{
				text: 'Tipo',
				datafield: 'Tipo',
				width: '10%',
				height: 20,
				cellsalign: 'center',
				editable: true,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: TipoValues,
						dropDownHeight: 100,
						dropDownWidth: 54,
						selectedIndex: -1,
						displayMember: 'Tipo',
						valueMember: 'Tipo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{ text: 'Fijo?', datafield: 'Fijo', columntype: 'checkbox', editable: true, width: '8%', height: 20 },
		]
	});
	$("#parametros_presupuesto_items_grid6").on("bindingcomplete", function (event)
	{
		$("#parametros_presupuesto_items_grid6").jqxGrid('hidecolumn', 'ID');
		$("#parametros_presupuesto_items_grid6").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div style="float: left; margin-left:0px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Base de Datos
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_presupuesto_db_nombre"/>
			<input type="text" id="parametros_presupuesto_db_notas"/>
		</div>
		<div id="parametros_presupuesto_items_grid1"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Categoria
		</p>
		<div id="parametros_presupuesto_items_grid2"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Grupo
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_presupuesto_grupo_codigo"/>
			<input type="text" id="parametros_presupuesto_grupo"/>
		</div>
		<div id="parametros_presupuesto_items_grid3"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			SubGrupo
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_presupuesto_subgrupo_codigo"/>
			<input type="text" id="parametros_presupuesto_subgrupo"/>
		</div>
		<div id="parametros_presupuesto_items_grid4"></div>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div style="float: left; margin-left:0px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Unidades de Medida
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_presupuesto_medida"/>
		</div>
		<div id="parametros_presupuesto_items_grid5"></div>
	</div>
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Conceptos de Mano de Obra
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_presupuesto_concepto"/>
		</div>
		<div id="parametros_presupuesto_items_grid6"></div>
	</div>
</div>
