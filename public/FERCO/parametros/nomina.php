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
	
	$("#parametros_nomina_salud").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
		placeHolder: "Ingresar Nombre",
	});
	$("#parametros_nomina_salud").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
		}
	});
	
	function Add_Row1()
	{
		var Val = $("#parametros_nomina_salud").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Nombre de EPS!", 3);
			WaitClick_Input("parametros_nomina_salud");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid1").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Salud":Val
		}];
		
		$("#parametros_nomina_items_grid1").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_salud").val("");
	};
	
	var SaludSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Salud', type: 'string'},
		],
		cache: false,
		data: {"Nomina_Salud":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Salud"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Salud":true,
						"VAL":rowdata[0]["Salud"],
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
							$("#parametros_nomina_items_grid1").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_nomina_items_grid1").jqxGrid('getcellvalue', rowid, "Salud");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Salud":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Nomina_Salud":true,
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
	var SaludDataAdapter = new $.jqx.dataAdapter(SaludSource);
	
	$("#parametros_nomina_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 150,
		height: 200,
		source: SaludDataAdapter,
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
				width: '20%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_nomina_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid1").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid1").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Nombre', datafield: 'Salud', editable: true, width: '80%', height: 20 },
		]
	});
	$("#parametros_nomina_items_grid1").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid1").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid1").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_nomina_pension").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
		placeHolder: "Ingresar Nombre",
	});
	$("#parametros_nomina_pension").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row2();
		}
	});
	
	function Add_Row2()
	{
		var Val = $("#parametros_nomina_pension").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Nombre de AFP!", 3);
			WaitClick_Input("parametros_nomina_pension");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid2").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Pension":Val
		}];
		
		$("#parametros_nomina_items_grid2").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_pension").val("");
	};
	
	var PensionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Pension', type: 'string'},
		],
		cache: false,
		data: {"Nomina_Pension":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Pension"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Pension":true,
						"VAL":rowdata[0]["Pension"],
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
							$("#parametros_nomina_items_grid2").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_nomina_items_grid2").jqxGrid('getcellvalue', rowid, "Pension");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Pension":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Nomina_Pension":true,
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
	var PensionDataAdapter = new $.jqx.dataAdapter(PensionSource);
	
	$("#parametros_nomina_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 150,
		height: 200,
		source: PensionDataAdapter,
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
				width: '20%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_nomina_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid2").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Nombre', datafield: 'Pension', editable: true, width: '80%', height: 20 },
		]
	});
	$("#parametros_nomina_items_grid2").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid2").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid2").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_nomina_cesantia").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
		placeHolder: "Ingresar Nombre",
	});
	$("#parametros_nomina_cesantia").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row3();
		}
	});
	
	function Add_Row3()
	{
		var Val = $("#parametros_nomina_cesantia").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Nombre de Cesantia!", 3);
			WaitClick_Input("parametros_nomina_cesantia");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid3").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Cesantia":Val
		}];
		
		$("#parametros_nomina_items_grid3").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_cesantia").val("");
	};
	
	var CesantiaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Cesantia', type: 'string'},
		],
		cache: false,
		data: {"Nomina_Cesantia":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Cesantia"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Cesantia":true,
						"VAL":rowdata[0]["Cesantia"],
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
							$("#parametros_nomina_items_grid3").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_nomina_items_grid3").jqxGrid('getcellvalue', rowid, "Cesantia");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Cesantia":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Nomina_Cesantia":true,
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
	var CesantiaDataAdapter = new $.jqx.dataAdapter(CesantiaSource);
	
	$("#parametros_nomina_items_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 150,
		height: 200,
		source: CesantiaDataAdapter,
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
				width: '20%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_nomina_items_grid3").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid3").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid3").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid3").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Nombre', datafield: 'Cesantia', editable: true, width: '80%', height: 20 },
		]
	});
	$("#parametros_nomina_items_grid3").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid3").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid3").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	var SalarioSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'Valor', type: 'decimal'},
		],
		cache: false,
		data: {"Nomina_Salario":true},
		url: "modulos/parametros.php",
		updaterow: function (rowid, rowdata, commit)
		{
			var ID = $("#parametros_nomina_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_nomina_items_grid4").jqxGrid('getcellvalue', rowid, "Valor");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Salario":true,
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
	};
	var SalarioDataAdapter = new $.jqx.dataAdapter(SalarioSource);
	
	$("#parametros_nomina_items_grid4").jqxGrid(
	{
		theme: mytheme,
		width: 200,
		height: 227,
		source: SalarioDataAdapter,
		enabletooltips: true,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Nombre', datafield: 'Nombre', editable: false, width: '45%', height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: true,
				width: '55%',
				height: 20,
				columntype: 'numberinput',
				cellsalign: 'right',
				cellsformat: 'd',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 4, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
		]
	});
	$("#parametros_nomina_items_grid4").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid4").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid4").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_nomina_salario").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 250,
		inputMode: 'simple',
		symbol: "$",
		spinButtons: false,
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		min: 0,
	});
	$("#parametros_nomina_salario").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row5();
		}
	});
	
	function Add_Row5()
	{
		var Val = $("#parametros_nomina_salario").val();
		
		if ( Val < 1 || Val == "") {
			Alerts_Box("Favor Ingresar un Salario Minimo para la Retencion", 3);
			WaitClick_Input("parametros_nomina_salario");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid5").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Salario":Val,
			"Valor":0
		}];
		
		$("#parametros_nomina_items_grid5").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_salario").val("0");
	};
	
	var RetencionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Salario', type: 'decimal'},
			{ name: 'Valor', type: 'decimal'},
		],
		cache: false,
		data: {"Nomina_Retenciones":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Salario"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Retenciones":true,
						"VAL":rowdata[0]["Salario"],
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
							$("#parametros_nomina_items_grid5").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_nomina_items_grid5").jqxGrid('getcellvalue', rowid, "Salario");
			var VAL2 = $("#parametros_nomina_items_grid5").jqxGrid('getcellvalue', rowid, "Valor");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Retenciones":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Nomina_Retenciones":true,
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
	var RetencionDataAdapter = new $.jqx.dataAdapter(RetencionSource);
	
	$("#parametros_nomina_items_grid5").jqxGrid(
	{
		theme: mytheme,
		width: 250,
		height: 200,
		source: RetencionDataAdapter,
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
					var selectedrowindex = $("#parametros_nomina_items_grid5").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid5").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid5").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid5").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{
				text: 'Salario',
				datafield: 'Salario',
				editable: true,
				width: '50%',
				height: 20,
				columntype: 'numberinput',
				cellsalign: 'right',
				cellsformat: 'c2',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: true,
				width: '40%',
				height: 20,
				columntype: 'numberinput',
				cellsalign: 'right',
				cellsformat: 'c2',
				//cellsformat: 'p2',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
		]
	});
	$("#parametros_nomina_items_grid5").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid5").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid5").jqxGrid('localizestrings', localizationobj);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#parametros_nomina_novedad").jqxInput({
		theme: mytheme,
		width: 200,
		height: 20,
		placeHolder: "Ingresar Novedad",
	});
	$("#parametros_nomina_novedad").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row6();
		}
	});
	
	function Add_Row6()
	{
		var Val = $("#parametros_nomina_novedad").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar una Novedad!", 3);
			WaitClick_Input("parametros_nomina_novedad");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid6").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Novedad":Val,
			"Descontable":false,
			"Remunerado100":false,
			"Remunerado66":false,
			"Cesantia":false
		}];
		
		$("#parametros_nomina_items_grid6").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_novedad").val("");
	};
	
	var NovedadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Novedad', type: 'string'},
			{ name: 'Descontable', type: 'bool'},
			{ name: 'Remunerado100', type: 'bool'},
			{ name: 'Remunerado66', type: 'bool'},
			{ name: 'Cesantia', type: 'bool'},
		],
		cache: false,
		data: {"Nomina_Novedades":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Novedad"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Novedad":true,
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
							$("#parametros_nomina_items_grid6").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid6").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_nomina_items_grid6").jqxGrid('getcellvalue', rowid, "Novedad");
			var VAL2 = $("#parametros_nomina_items_grid6").jqxGrid('getcellvalue', rowid, "Descontable");
			var VAL3 = $("#parametros_nomina_items_grid6").jqxGrid('getcellvalue', rowid, "Remunerado100");
			var VAL4 = $("#parametros_nomina_items_grid6").jqxGrid('getcellvalue', rowid, "Remunerado66");
			var VAL5 = $("#parametros_nomina_items_grid6").jqxGrid('getcellvalue', rowid, "Cesantia");
			
			//alert(ID+" - "+VAL1+" - "+VAL2+" - "+VAL3+" - "+VAL4+" - "+VAL5)
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Novedad":true,
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
					
					var ID = $("#parametros_nomina_items_grid6").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Nomina_Novedad":true,
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
	
	$("#parametros_nomina_items_grid6").jqxGrid(
	{
		theme: mytheme,
		width: 400,
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
				width: '8%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_nomina_items_grid6").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid6").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid6").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid6").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Novedad', datafield: 'Novedad', editable: true, width: '48%', height: 20 },
			{ text: 'Desc', datafield: 'Descontable', columntype: 'checkbox', editable: true, width: '11%', height: 20 },
			{ text: '100%', datafield: 'Remunerado100', columntype: 'checkbox', editable: true, width: '11%', height: 20 },
			{ text: '66%', datafield: 'Remunerado66', columntype: 'checkbox', editable: true, width: '11%', height: 20 },
			{ text: 'Ces', datafield: 'Cesantia', columntype: 'checkbox', editable: true, width: '11%', height: 20 },
		]
	});
	$("#parametros_nomina_items_grid6").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid6").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid6").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_nomina_justificacion1").jqxInput({
		theme: mytheme,
		width: 200,
		height: 20,
		placeHolder: "Ingresar Justificacion",
	});
	$("#parametros_nomina_justificacion1").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row7();
		}
	});
	
	function Add_Row7()
	{
		var Val = $("#parametros_nomina_justificacion1").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Nombre de Justificacion", 3);
			WaitClick_Input("parametros_nomina_justificacion1");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid7").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Tipo":Val
		}];
		
		$("#parametros_nomina_items_grid7").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_justificacion1").val("");
	};
	
	var Justificacion1Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Tipo', type: 'string'},
		],
		cache: false,
		data: {"Nomina_Justificacion2":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Tipo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Justificacion1":true,
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
							$("#parametros_nomina_items_grid7").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid7").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_nomina_items_grid7").jqxGrid('getcellvalue', rowid, "Tipo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Justificacion1":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid7").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Nomina_Justificacion1":true,
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
	var Justificacion1DataAdapter = new $.jqx.dataAdapter(Justificacion1Source);
	
	$("#parametros_nomina_items_grid7").jqxGrid(
	{
		theme: mytheme,
		width: 200,
		height: 200,
		source: Justificacion1DataAdapter,
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
				width: '13%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_nomina_items_grid7").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid7").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid7").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid7").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Justificacion', datafield: 'Tipo', editable: true, width: '87%', height: 20 },
		]
	});
	$("#parametros_nomina_items_grid7").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid7").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid7").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_nomina_justificacion2").jqxInput({
		theme: mytheme,
		width: 200,
		height: 20,
		placeHolder: "Ingresar Justificacion",
	});
	$("#parametros_nomina_justificacion2").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row8();
		}
	});
	
	function Add_Row8()
	{
		var Val = $("#parametros_nomina_justificacion2").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Nombre de Justificacion", 3);
			WaitClick_Input("parametros_nomina_justificacion2");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid8").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Tipo":Val
		}];
		
		$("#parametros_nomina_items_grid8").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_justificacion2").val("");
	};
	
	var Justificacion2Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Tipo', type: 'string'},
		],
		cache: false,
		data: {"Nomina_Justificacion":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Tipo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Justificacion2":true,
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
							$("#parametros_nomina_items_grid8").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid8").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_nomina_items_grid8").jqxGrid('getcellvalue', rowid, "Tipo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Justificacion2":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid8").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Nomina_Justificacion2":true,
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
	var Justificacion2DataAdapter = new $.jqx.dataAdapter(Justificacion2Source);
	
	$("#parametros_nomina_items_grid8").jqxGrid(
	{
		theme: mytheme,
		width: 200,
		height: 200,
		source: Justificacion2DataAdapter,
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
				width: '13%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_nomina_items_grid8").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid8").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid8").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid8").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Justificacion', datafield: 'Tipo', editable: true, width: '87%', height: 20 },
		]
	});
	$("#parametros_nomina_items_grid8").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid8").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid8").jqxGrid('localizestrings', localizationobj);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#parametros_nomina_reposicion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 250,
		placeHolder: "Ingresar Nombre",
	});
	$("#parametros_nomina_reposicion").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row9();
		}
	});
	
	function Add_Row9()
	{
		var Val = $("#parametros_nomina_reposicion").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Nombre!", 3);
			WaitClick_Input("parametros_nomina_reposicion");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid9").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Reposicion":Val,
			"Reponer":false
		}];
		
		$("#parametros_nomina_items_grid9").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_reposicion").val("");
	};
	
	var ReposicionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Reposicion', type: 'string'},
			{ name: 'Reponer', type: 'bool'},
		],
		cache: false,
		data: {"Nomina_Reposicion":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Reposicion"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Reposicion":true,
						"VAL":rowdata[0]["Reposicion"],
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
							$("#parametros_nomina_items_grid9").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid9").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_nomina_items_grid9").jqxGrid('getcellvalue', rowid, "Reposicion");
			var VAL2 = $("#parametros_nomina_items_grid9").jqxGrid('getcellvalue', rowid, "Reponer");
			
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
					"Parametros_Actualizar_Nomina_Reposicion":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid9").jqxGrid('getcellvalue', rowid, "ID");
					
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
							"Parametros_Borrar_Nomina_Reposicion":true,
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
	var ReposicionDataAdapter = new $.jqx.dataAdapter(ReposicionSource);
	
	$("#parametros_nomina_items_grid9").jqxGrid(
	{
		theme: mytheme,
		width: 250,
		height: 200,
		source: ReposicionDataAdapter,
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
					var selectedrowindex = $("#parametros_nomina_items_grid9").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid9").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid9").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid9").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Nombre', datafield: 'Reposicion', editable: true, width: '70%', height: 20 },
			{ text: 'Rep?', datafield: 'Reponer', columntype: 'checkbox', editable: true, width: '20%', height: 20 },
		]
	});
	$("#parametros_nomina_items_grid9").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid9").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid9").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	var HorariosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Tipo', type: 'string'},
			{ name: 'Hora_Ini', type: 'date'},
			{ name: 'Hora_Fin', type: 'date'},
		],
		cache: false,
		data: {"Nomina_Horario":true, "Formatted":true},
		url: "modulos/parametros.php",
		updaterow: function (rowid, rowdata, commit)
		{
			var ID = $("#parametros_nomina_items_grid10").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = GetFormattedTime($("#parametros_nomina_items_grid10").jqxGrid('getcellvalue', rowid, "Hora_Ini"));
			var VAL2 = GetFormattedTime($("#parametros_nomina_items_grid10").jqxGrid('getcellvalue', rowid, "Hora_Fin"));
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Horarios":true,
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
	};
	var HorariosDataAdapter = new $.jqx.dataAdapter(HorariosSource);
	
	$("#parametros_nomina_items_grid10").jqxGrid(
	{
		theme: mytheme,
		width: 250,
		height: 227,
		source: HorariosDataAdapter,
		enabletooltips: true,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Nombre', datafield: 'Tipo', editable: false, width: '36%', height: 20 },
			{
				text: 'Hora Inicial',
				datafield: 'Hora_Ini',
				columntype: 'datetimeinput',
				cellsformat: 'hh:mm tt',
				editable: true,
				width: '32%',
				height: 20,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({
						showCalendarButton: false,
						showTimeButton: true,
					});
				}
			},
			{
				text: 'Hora Final',
				datafield: 'Hora_Fin',
				columntype: 'datetimeinput',
				cellsformat: 'hh:mm tt',
				editable: true,
				width: '32%',
				height: 20,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({
						showCalendarButton: false,
						showTimeButton: true
					});
				}
			},
		]
	});
	$("#parametros_nomina_items_grid10").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid10").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid10").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_nomina_festivo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 350,
		placeHolder: "Ingresar Nombre",
	});
	$("#parametros_nomina_festivo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row11();
		}
	});
	
	function Add_Row11()
	{
		var Val = $("#parametros_nomina_festivo").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Nombre del Dia Festivo!", 3);
			WaitClick_Input("parametros_nomina_festivo");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid11").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Nombre":Val,
			"Fecha":"1999-12-31"
		}];
		
		$("#parametros_nomina_items_grid11").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_festivo").val("");
	};
	
	var FestivoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'Fecha', type: 'date'},
		],
		cache: false,
		data: {"Nomina_Festivos":true, "Formatted":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Nombre"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Festivo":true,
						"VAL":rowdata[0]["Nombre"],
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
							$("#parametros_nomina_items_grid11").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid11").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_nomina_items_grid11").jqxGrid('getcellvalue', rowid, "Nombre");
			var VAL2 = GetFormattedDate($("#parametros_nomina_items_grid11").jqxGrid('getcellvalue', rowid, "Fecha"));
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Nomina_Festivo":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid11").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Nomina_Festivo":true,
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
	var FestivoDataAdapter = new $.jqx.dataAdapter(FestivoSource);
	
	$("#parametros_nomina_items_grid11").jqxGrid(
	{
		theme: mytheme,
		width: 350,
		height: 200,
		source: FestivoDataAdapter,
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
					var selectedrowindex = $("#parametros_nomina_items_grid11").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid11").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid11").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid11").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Nombre', datafield: 'Nombre', editable: true, width: '62%', height: 20 },
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				cellsformat: 'dd-MMM-yyyy',
				editable: true,
				width: '30%',
				height: 20,
			},
		]
	});
	$("#parametros_nomina_items_grid11").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid11").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid11").jqxGrid('localizestrings', localizationobj);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 4 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#parametros_nomina_cargos").jqxInput({
		theme: mytheme,
		width: 200,
		height: 20,
		placeHolder: "Ingresar Cargo",
	});
	$("#parametros_nomina_cargos").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row8();
		}
	});
	
	function Add_Row8()
	{
		var Val = $("#parametros_nomina_cargos").val();
		
		if ( Val < 0 || Val == "") {
			Alerts_Box("Favor Ingresar un Nombre de Cargo", 3);
			WaitClick_Input("parametros_nomina_cargos");
			return;
		}
		
		var datainfo = $("#parametros_nomina_items_grid12").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Cargo":Val
		}];
		
		$("#parametros_nomina_items_grid12").jqxGrid("addrow", count, datarow);
		$("#parametros_nomina_cargos").val("");
	};
	
	var CargosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'Cargo', type: 'string'},
		],
		cache: false,
		data: {"Nomina_Cargo":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Cargo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Nomina_Cargo":true,
						"VAL":rowdata[0]["Cargo"],
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
							$("#parametros_nomina_items_grid12").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_nomina_items_grid12").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_nomina_items_grid12").jqxGrid('getcellvalue', rowid, "Cargo");
			
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
					"Parametros_Actualizar_Nomina_Cargo":true,
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
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#parametros_nomina_items_grid12").jqxGrid('getcellvalue', rowid, "ID");
					
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
							"Parametros_Borrar_Nomina_Cargo":true,
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
	var CargosDataAdapter = new $.jqx.dataAdapter(CargosSource);
	
	$("#parametros_nomina_items_grid12").jqxGrid(
	{
		theme: mytheme,
		width: 200,
		height: 200,
		source: CargosDataAdapter,
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
				width: '13%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_nomina_items_grid12").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_nomina_items_grid12").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_nomina_items_grid12").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_nomina_items_grid12").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Cargo', datafield: 'Cargo', editable: true, width: '87%', height: 20 },
		]
	});
	$("#parametros_nomina_items_grid12").on("bindingcomplete", function (event)
	{
		$("#parametros_nomina_items_grid12").jqxGrid('hidecolumn', 'ID');
		$("#parametros_nomina_items_grid12").jqxGrid('localizestrings', localizationobj);
	});
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
	$("#parametros_nomina_desc1").on("change", function (event)
	{
		UpdateCarnet();
	});
	
	$("#parametros_nomina_desc2").jqxInput({
		theme: mytheme,
		width: 265,
		height: 20,
		placeHolder: "",
	});
	$("#parametros_nomina_desc2").on("change", function (event)
	{
		UpdateCarnet();
	});
	
	var CarnetSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'int'},
			{ name: 'DESC1', type: 'string'},
			{ name: 'DESC2', type: 'string'},
		],
		data: {"Nomina_Carnet":true},
		url: "modulos/parametros.php",
	};
	var CarnetDataAdapter = new $.jqx.dataAdapter(CarnetSource,
	{
		autoBind: true,
		loadComplete: function (records)
		{
			$("#parametros_nomina_desc1").val(records[0]["DESC1"]);
			$("#parametros_nomina_desc2").val(records[0]["DESC2"]);
		},
	});
	
	function UpdateCarnet()
	{
		var VAL1 = $("#parametros_nomina_desc1").val();
		var VAL2 = $("#parametros_nomina_desc2").val();
		
		$.ajax({
			dataType: "text",
			url: "modulos/guardar.php",
			data: {
				"Parametros_Actualizar_Nomina_Carnet":true,
				"VAL1":VAL1,
				"VAL2":VAL2,
			},
			success: function (data, status, xhr)
			{
				if (data != "")
				{
					Alerts_Box("Ocurrio un Error al Intentar Actualizar los Datos!<br/>Intente luego de unos segundos...", 3);
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
	}
	
	//-----------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div style="float: left; margin-left:0px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			EPS (Salud)
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_salud"/>
		</div>
		<div id="parametros_nomina_items_grid1"></div>
	</div>
	
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			AFP (Pensión)
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_pension"/>
		</div>
		<div id="parametros_nomina_items_grid2"></div>
	</div>
	
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Cesantías
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_cesantia"/>
		</div>
		<div id="parametros_nomina_items_grid3"></div>
	</div>
	
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 13px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Salario Minimo y % EPS/AFP
		</p>
		<div id="parametros_nomina_items_grid4"></div>
	</div>
	
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Retenciones 
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<div id="parametros_nomina_salario"></div>
		</div>
		<div id="parametros_nomina_items_grid5"></div>
	</div>
	
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div style="float: left; margin-left:0px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Novedades
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_novedad"/>
		</div>
		<div id="parametros_nomina_items_grid6"></div>
	</div>
	
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Justificaciones (Novedad)
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_justificacion1"/>
		</div>
		<div id="parametros_nomina_items_grid7"></div>
	</div>
	
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Justificaciones (Horas Ext.)
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_justificacion2"/>
		</div>
		<div id="parametros_nomina_items_grid8"></div>
	</div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<div style="float: left; margin-left:0px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Opciones de Reposicion
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_reposicion"/>
		</div>
		<div id="parametros_nomina_items_grid9"></div>
	</div>
	
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Horarios
		</p>
		<div id="parametros_nomina_items_grid10"></div>
	</div>
	
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Dias Festivos
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_festivo"/>
		</div>
		<div id="parametros_nomina_items_grid11"></div>
	</div>
</div>
<!-- PART 4 -->
<div id="Parte3">
	<div style="float: left; margin-left:0px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Cargos
		</p>
		<div style="margin:5px 0px 5px 0px;">
			<input type="text" id="parametros_nomina_cargos"/>
		</div>
		<div id="parametros_nomina_items_grid12"></div>
	</div>
	
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Texto de Carnet
		</p>
		<li>
			<textarea rows="7" cols="35" id="parametros_nomina_desc1" maxlength="500" style="resize:none;"></textarea>
		</li>
		<li style="margin-top:5px;">
			<input type="text" id="parametros_nomina_desc2" />
		</li>
	</div>
</div>
