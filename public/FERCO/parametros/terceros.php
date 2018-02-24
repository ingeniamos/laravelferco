<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()// Modulo no-finalizado
{
	//----- GOLBALS
	var mytheme = "energyblue";
	
	/*$("#parametros_terceros_clasificacion").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
		}
	});
	
	$("#parametros_terceros_clasificacion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
		placeHolder: "Ingresar Clasificacion",
	});
	
	function Add_Row1()
	{
		var Val = $("#parametros_terceros_clasificacion").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar una Clasificacion!", 3);
			WaitClick_Input("parametros_terceros_clasificacion");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid1").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Clasificacion":Val
		}];
		
		$("#parametros_terceros_items_grid1").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_clasificacion").val("");
	};
	*/
	
	var ClasificacionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Clasificacion', type: 'string'},
		],
		cache: false,
		data: {"Terceros_Clasificacion":true},
		url: "modulos/parametros.php",
		async: true,
		/*
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Clasificacion"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Clasificacion":true,
						"VAL":rowdata[0]["Clasificacion"],
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
							$("#parametros_terceros_items_grid1").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid1").jqxGrid('getcellvalue', rowid, "Clasificacion");
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Clasificacion":true,
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
					
					var ID = $("#parametros_terceros_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Clasificacion":true,
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
	var ClasificacionDataAdapter = new $.jqx.dataAdapter(ClasificacionSource);
	
	$("#parametros_terceros_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 150,
		height: 226,
		source: ClasificacionDataAdapter,
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
				width: '18%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_terceros_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid1").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid1").jqxGrid('deleterow', id);
					}
				}
			},*/
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Clasificacion', datafield: 'Clasificacion', editable: false, width: '100%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid1").on("bindingcomplete", function (event) {
		$("#parametros_terceros_items_grid1").jqxGrid('hidecolumn', 'ID');
		$("#parametros_terceros_items_grid1").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#parametros_terceros_items_grid1").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield = "Clasificacion")
		{
			var Clasificacion = $("#parametros_terceros_items_grid1").jqxGrid("getcellvalue", rowBoundIndex, "Clasificacion");
			TipoSource.data = {"Terceros_Tipo": Clasificacion};
			var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource);
			$("#parametros_terceros_items_grid2").jqxGrid({source: TipoDataAdapter});
			GrupoSource.data = {"Terceros_Grupo": Clasificacion};
			var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#parametros_terceros_items_grid3").jqxGrid({source: GrupoDataAdapter});
		}
	});
	
	$("#parametros_terceros_tipo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row2();
		}
	});
	$("#parametros_terceros_tipo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Tipo",
	});
	
	function Add_Row2()
	{
		var Val = $("#parametros_terceros_tipo").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar una Tipo!", 3);
			WaitClick_Input("parametros_terceros_tipo");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid2").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Tipo":Val
		}];
		
		$("#parametros_terceros_items_grid2").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_tipo").val("");
	};
	
	var TipoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Tipo', type: 'string'},
		],
		cache: false,
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			var rowindex = $("#parametros_terceros_items_grid1").jqxGrid('getselectedrowindex');
			var value = $("#parametros_terceros_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
			
			if ( rowindex < 0 ) {
				Alerts_Box("Debe Seleccionar una Clasificacion Valida.", 3);
				commit(false);
				return;
			}
			
			if (rowdata[0]["Tipo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Tipo":true,
						"VAL1":value,
						"VAL2":rowdata[0]["Tipo"],
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
							$("#parametros_terceros_items_grid2").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid2").jqxGrid('getcellvalue', rowid, "Tipo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Tipo":true,
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
					
					var ID = $("#parametros_terceros_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Tipo":true,
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
	
	$("#parametros_terceros_items_grid2").jqxGrid(
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
					var selectedrowindex = $("#parametros_terceros_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid2").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Tipo', datafield: 'Tipo', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid2").jqxGrid('hidecolumn', 'ID');
	$("#parametros_terceros_items_grid2").jqxGrid('localizestrings', localizationobj);
	
	$("#parametros_terceros_grupo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row3();
		}
	});
	$("#parametros_terceros_grupo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Grupo",
	});
	
	function Add_Row3()
	{
		var Val = $("#parametros_terceros_grupo").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar una Grupo!", 3);
			WaitClick_Input("parametros_terceros_grupo");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid3").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Grupo":Val
		}];
		
		$("#parametros_terceros_items_grid3").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_grupo").val("");
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
			var rowindex = $("#parametros_terceros_items_grid1").jqxGrid('getselectedrowindex');
			var value = $("#parametros_terceros_items_grid1").jqxGrid('getcellvalue', rowindex, "Clasificacion");
			
			if ( rowindex < 0 ) {
				Alerts_Box("Debe Seleccionar una Clasificacion Valida.", 3);
				commit(false);
				return;
			}
			
			if (rowdata[0]["Grupo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Grupo":true,
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
							$("#parametros_terceros_items_grid3").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid3").jqxGrid('getcellvalue', rowid, "Grupo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Grupo":true,
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
					
					var ID = $("#parametros_terceros_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Grupo":true,
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
	
	$("#parametros_terceros_items_grid3").jqxGrid(
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
					var selectedrowindex = $("#parametros_terceros_items_grid3").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid3").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid3").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid3").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Grupo de Proveedor', datafield: 'Grupo', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid3").jqxGrid('hidecolumn', 'ID');
	$("#parametros_terceros_items_grid3").jqxGrid('localizestrings', localizationobj);
	
	$("#parametros_terceros_documento").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row4();
		}
	});
	$("#parametros_terceros_documento").jqxInput({
		theme: mytheme,
		height: 20,
		width: 200,
		placeHolder: "Ingresar Tipo Documento",
	});
	
	function Add_Row4()
	{
		var Val = $("#parametros_terceros_documento").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un tipo de Documento!", 3);
			WaitClick_Input("parametros_terceros_documento");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid4").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Documento":Val
		}];
		
		$("#parametros_terceros_items_grid4").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_documento").val("");
	};
	
	var DocumentoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Documento', type: 'string'},
		],
		cache: false,
		data: {"Terceros_Documento":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Documento"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Documento":true,
						"VAL":rowdata[0]["Documento"],
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
							$("#parametros_terceros_items_grid4").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid4").jqxGrid('getcellvalue', rowid, "Documento");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Documento":true,
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
					
					var ID = $("#parametros_terceros_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Documento":true,
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
	var DocumentoDataAdapter = new $.jqx.dataAdapter(DocumentoSource);
	
	$("#parametros_terceros_items_grid4").jqxGrid(
	{
		theme: mytheme,
		width: 200,
		height: 200,
		source: DocumentoDataAdapter,
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
					var selectedrowindex = $("#parametros_terceros_items_grid4").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid4").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid4").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid4").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Documento', datafield: 'Documento', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid4").on("bindingcomplete", function (event) {
		$("#parametros_terceros_items_grid4").jqxGrid('hidecolumn', 'ID');
		$("#parametros_terceros_items_grid4").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#parametros_terceros_sociedad").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row5();
		}
	});
	$("#parametros_terceros_sociedad").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		placeHolder: "Ingresar Tipo Sociedad",
	});
	
	function Add_Row5()
	{
		var Val = $("#parametros_terceros_sociedad").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un tipo de Sociedad!", 3);
			WaitClick_Input("parametros_terceros_sociedad");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid5").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Tipo_Sociedad":Val
		}];
		
		$("#parametros_terceros_items_grid5").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_sociedad").val("");
	};
	
	var SociedadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Tipo_Sociedad', type: 'string'},
		],
		cache: false,
		data: {"Terceros_Tipo_Doc":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Tipo_Sociedad"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Sociedad":true,
						"VAL":rowdata[0]["Tipo_Sociedad"],
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
							$("#parametros_terceros_items_grid5").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid5").jqxGrid('getcellvalue', rowid, "Tipo_Sociedad");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Sociedad":true,
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
					
					var ID = $("#parametros_terceros_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Sociedad":true,
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
	var SociedadDataAdapter = new $.jqx.dataAdapter(SociedadSource);
	
	$("#parametros_terceros_items_grid5").jqxGrid(
	{
		theme: mytheme,
		width: 180,
		height: 200,
		source: SociedadDataAdapter,
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
					var selectedrowindex = $("#parametros_terceros_items_grid5").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid5").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid5").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid5").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Tipo Sociedad', datafield: 'Tipo_Sociedad', editable: true, width: '85%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid5").on("bindingcomplete", function (event) {
		$("#parametros_terceros_items_grid5").jqxGrid('hidecolumn', 'ID');
		$("#parametros_terceros_items_grid5").jqxGrid('localizestrings', localizationobj);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#parametros_terceros_barrios").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row6();
		}
	});
	$("#parametros_terceros_barrios").jqxInput(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		placeHolder: "Ingresar Barrio",
	});
	
	function Add_Row6()
	{
		var Val = $("#parametros_terceros_barrios").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Barrio!", 3);
			WaitClick_Input("parametros_terceros_barrios");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid6").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Barrio":Val
		}];
		
		$("#parametros_terceros_items_grid6").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_barrios").val("");
	};
	
	var BarrioSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Barrio', type: 'string'},
		],
		cache: false,
		data: {"Terceros_Barrio":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Barrio"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Barrio":true,
						"VAL":rowdata[0]["Barrio"],
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
							$("#parametros_terceros_items_grid6").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid6").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid6").jqxGrid('getcellvalue', rowid, "Barrio");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Barrio":true,
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
					
					var ID = $("#parametros_terceros_items_grid6").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Barrio":true,
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
	var BarrioDataAdapter = new $.jqx.dataAdapter(BarrioSource);
	
	$("#parametros_terceros_items_grid6").jqxGrid(
	{
		theme: mytheme,
		width: 250,
		height: 200,
		source: BarrioDataAdapter,
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
					var selectedrowindex = $("#parametros_terceros_items_grid6").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid6").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid6").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid6").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Barrio', datafield: 'Barrio', editable: true, width: '90%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid6").on("bindingcomplete", function (event) {
		$("#parametros_terceros_items_grid6").jqxGrid('hidecolumn', 'ID');
		$("#parametros_terceros_items_grid6").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#parametros_terceros_garantias").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row7();
		}
	});
	$("#parametros_terceros_garantias").jqxInput(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		placeHolder: "Ingresar Garantia/Documento",
	});
	
	function Add_Row7()
	{
		var Val = $("#parametros_terceros_garantias").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un tipo de Garantia/Documento!", 3);
			WaitClick_Input("parametros_terceros_garantias");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid7").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Garantia":Val
		}];
		
		$("#parametros_terceros_items_grid7").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_garantias").val("");
	};
	
	var GarantiasSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Garantia', type: 'string'},
		],
		cache: false,
		data: {"Garantias":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Garantia"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Garantia":true,
						"VAL":rowdata[0]["Garantia"],
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
							$("#parametros_terceros_items_grid7").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid7").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid7").jqxGrid('getcellvalue', rowid, "Garantia");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Garantia":true,
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
					
					var ID = $("#parametros_terceros_items_grid7").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Garantia":true,
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
	var GarantiasDataAdapter = new $.jqx.dataAdapter(GarantiasSource);
	
	$("#parametros_terceros_items_grid7").jqxGrid(
	{
		theme: mytheme,
		width: 250,
		height: 200,
		source: GarantiasDataAdapter,
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
					var selectedrowindex = $("#parametros_terceros_items_grid7").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid7").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid7").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid7").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Garantia/Documento', datafield: 'Garantia', editable: true, width: '90%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid7").on("bindingcomplete", function (event) {
		$("#parametros_terceros_items_grid7").jqxGrid('hidecolumn', 'ID');
		$("#parametros_terceros_items_grid7").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#parametros_terceros_motivo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row8();
		}
	});
	$("#parametros_terceros_motivo").jqxInput(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		placeHolder: "Ingresar Motivo de Visita",
	});
	
	function Add_Row8()
	{
		var Val = $("#parametros_terceros_motivo").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Motivo de Visita!", 3);
			WaitClick_Input("parametros_terceros_motivo");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid8").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Motivo":Val
		}];
		
		$("#parametros_terceros_items_grid8").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_motivo").val("");
	};
	
	var MotivoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Motivo', type: 'string'},
		],
		cache: false,
		data: {"Motivo_Visita":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Motivo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Motivo":true,
						"VAL":rowdata[0]["Motivo"],
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
							$("#parametros_terceros_items_grid8").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid8").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid8").jqxGrid('getcellvalue', rowid, "Motivo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Motivo":true,
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
					
					var ID = $("#parametros_terceros_items_grid8").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Motivo":true,
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
	var MotivoDataAdapter = new $.jqx.dataAdapter(MotivoSource);
	
	$("#parametros_terceros_items_grid8").jqxGrid(
	{
		theme: mytheme,
		width: 220,
		height: 200,
		source: MotivoDataAdapter,
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
					var selectedrowindex = $("#parametros_terceros_items_grid8").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid8").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid8").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid8").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Motivo', datafield: 'Motivo', editable: true, width: '88%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid8").on("bindingcomplete", function (event) {
		$("#parametros_terceros_items_grid8").jqxGrid('hidecolumn', 'ID');
		$("#parametros_terceros_items_grid8").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#parametros_terceros_novedad").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row9();
		}
	});
	$("#parametros_terceros_novedad").jqxInput(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		placeHolder: "Ingresar Novedad de Visita",
	});
	
	function Add_Row9()
	{
		var Val = $("#parametros_terceros_novedad").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar una Novedad de Visita!", 3);
			WaitClick_Input("parametros_terceros_novedad");
			return;
		}
		
		var datainfo = $("#parametros_terceros_items_grid9").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Novedad":Val
		}];
		
		$("#parametros_terceros_items_grid9").jqxGrid("addrow", count, datarow);
		$("#parametros_terceros_novedad").val("");
	};
	
	var NovedadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Novedad', type: 'string'},
		],
		cache: false,
		data: {"Novedad_Visita":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Novedad"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Novedad":true,
						"VAL":rowdata[0]["Novedad"],
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
							$("#parametros_terceros_items_grid9").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_terceros_items_grid9").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_terceros_items_grid9").jqxGrid('getcellvalue', rowid, "Novedad");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Novedad":true,
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
					
					var ID = $("#parametros_terceros_items_grid9").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Novedad":true,
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
	var NovedadDataAdapter = new $.jqx.dataAdapter(NovedadSource);
	
	$("#parametros_terceros_items_grid9").jqxGrid(
	{
		theme: mytheme,
		width: 220,
		height: 200,
		source: NovedadDataAdapter,
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
					var selectedrowindex = $("#parametros_terceros_items_grid9").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_terceros_items_grid9").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_terceros_items_grid9").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_terceros_items_grid9").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Novedad', datafield: 'Novedad', editable: true, width: '88%', height: 20 },
		]
	});
	$("#parametros_terceros_items_grid9").on("bindingcomplete", function (event) {
		$("#parametros_terceros_items_grid9").jqxGrid('hidecolumn', 'ID');
		$("#parametros_terceros_items_grid9").jqxGrid('localizestrings', localizationobj);
	});
	
	
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div style="float: left; margin-left:0px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Clasificacion
		</p>
		<!--<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_clasificacion"/>
		</div>-->
		<div id="parametros_terceros_items_grid1"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Tipo
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_tipo"/>
		</div>
		<div id="parametros_terceros_items_grid2"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Grupo
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_grupo"/>
		</div>
		<div id="parametros_terceros_items_grid3"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Tipo de Documento
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_documento"/>
		</div>
		<div id="parametros_terceros_items_grid4"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Tipo de Sociedad
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_sociedad"/>
		</div>
		<div id="parametros_terceros_items_grid5"></div>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div style="float: left; margin-left:0px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Barrios
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_barrios"/>
		</div>
		<div id="parametros_terceros_items_grid6"></div>
	</div>
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Garantias/Documentos
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_garantias"/>
		</div>
		<div id="parametros_terceros_items_grid7"></div>
	</div>
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Motivo de Visita
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_motivo"/>
		</div>
		<div id="parametros_terceros_items_grid8"></div>
	</div>
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Novedad de Visita
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_terceros_novedad"/>
		</div>
		<div id="parametros_terceros_items_grid9"></div>
	</div>
</div>

