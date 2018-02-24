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
	
	$("#parametros_facturacion_placa").jqxInput(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		placeHolder: "Ingresar Placa",
	});
	$("#parametros_facturacion_placa").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
		}
	});
	
	function Add_Row1()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			var Val = $("#parametros_facturacion_placa").val();
		
			if (Val == "") {
				Alerts_Box("Favor Ingresar una Placa!", 3);
				WaitClick_Input("parametros_facturacion_placa");
				return;
			}
			
			var datainfo = $("#parametros_facturacion_items_grid1").jqxGrid("getdatainformation");
			var count = datainfo.rowscount;
			
			var datarow = [{
				"ID":0,
				"Placa":Val,
			}];
			
			$("#parametros_facturacion_items_grid1").jqxGrid("addrow", count, datarow);
			$("#parametros_facturacion_cliente").val("");
		},500);
	};
	
	var VehiculosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'string'},
			{ name: 'Placa', type: 'string'},
			{ name: 'Modelo', type: 'string'},
			{ name: 'Tipo', type: 'string'}
		],
		data: {"Vehiculo":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Placa"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Chofer":true,
						"Placa":rowdata[0]["Placa"],
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
							$("#parametros_facturacion_items_grid1").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_facturacion_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
			var Placa = $("#parametros_facturacion_items_grid1").jqxGrid('getcellvalue', rowid, "Placa");
			var Modelo = $("#parametros_facturacion_items_grid1").jqxGrid('getcellvalue', rowid, "Modelo");
			var Tipo = $("#parametros_facturacion_items_grid1").jqxGrid('getcellvalue', rowid, "Tipo");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Chofer":true,
					"ID":ID,
					"Placa":Placa,
					"Modelo":Modelo,
					"Tipo":Tipo,
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
					
					var ID = $("#parametros_facturacion_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Chofer":true,
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
	var VehiculosDataAdapter = new $.jqx.dataAdapter(VehiculosSource);
	
	$("#parametros_facturacion_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 320,
		height: 200,
		source: VehiculosDataAdapter,
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
					var selectedrowindex = $("#parametros_facturacion_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_facturacion_items_grid1").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_facturacion_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_facturacion_items_grid1").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Placa', datafield: 'Placa', editable: true, width: '25%', height: 20 },
			{ text: 'Modelo', datafield: 'Modelo', editable: true, width: '20%', height: 20 },
			{ text: 'Tipo', datafield: 'Tipo', editable: true, width: '47%', height: 20 },
		]
	});
	$("#parametros_facturacion_items_grid1").on("bindingcomplete", function (event) {
		$("#parametros_facturacion_items_grid1").jqxGrid('localizestrings', localizationobj);
		$("#parametros_facturacion_items_grid1").jqxGrid('hidecolumn', 'ID');
	});
	
	$("#parametros_facturacion_servicio").jqxInput(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		placeHolder: "Ingresar un Tipo de Servicio",
	});
	$("#parametros_facturacion_servicio").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row2();
		}
	});
	
	function Add_Row2()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			var Val = $("#parametros_facturacion_servicio").val();
		
			if ( Val == "" || Val <= 0) {
				Alerts_Box("Favor Ingresar un Tipo de Servicio", 3);
				WaitClick_Input("parametros_facturacion_servicio");
				return;
			}
			
			var datainfo = $("#parametros_facturacion_items_grid2").jqxGrid("getdatainformation");
			var count = datainfo.rowscount;
			
			var datarow = [{
				"ID":0,
				"Nombre":Val,
			}];
			
			$("#parametros_facturacion_items_grid2").jqxGrid("addrow", count, datarow);
			$("#parametros_facturacion_servicio").val("");
		},500);
	};
	
	var ServicioSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		data: {"OtroSrv":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Nombre"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Servicio":true,
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
							$("#parametros_facturacion_items_grid2").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_facturacion_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_facturacion_items_grid2").jqxGrid('getcellvalue', rowid, "Nombre");
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Servicio":true,
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
					
					var ID = $("#parametros_facturacion_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
					
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Servicio":true,
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
	var ServicioDataAdapter = new $.jqx.dataAdapter(ServicioSource);
	
	$("#parametros_facturacion_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 220,
		height: 200,
		source: ServicioDataAdapter,
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
					var selectedrowindex = $("#parametros_facturacion_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_facturacion_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_facturacion_items_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_facturacion_items_grid2").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Servicio', datafield: 'Nombre', editable: true, width: '88%', height: 20 },
		]
	});
	$("#parametros_facturacion_items_grid2").on("bindingcomplete", function (event) {
		$("#parametros_facturacion_items_grid2").jqxGrid('localizestrings', localizationobj);
		$("#parametros_facturacion_items_grid2").jqxGrid('hidecolumn', 'ID');
	});
	
	$("#parametros_facturacion_barrio").jqxInput(
	{
		theme: mytheme,
		width: 400,
		height: 20,
		placeHolder: "Ingresar un Barrio",
	});
	$("#parametros_facturacion_barrio").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row3();
		}
	});
	
	function Add_Row3()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			var Val = $("#parametros_facturacion_barrio").val();
		
			if ( Val == "" || Val <= 0) {
				Alerts_Box("Favor Ingresar un Barrio", 3);
				WaitClick_Input("parametros_facturacion_barrio");
				return;
			}
			
			var datainfo = $("#parametros_facturacion_items_grid3").jqxGrid("getdatainformation");
			var count = datainfo.rowscount;
			
			var datarow = [{
				"ID":0,
				"Barrio":Val,
				"Ruta":"",
			}];
			
			$("#parametros_facturacion_items_grid3").jqxGrid("addrow", count, datarow);
			$("#parametros_facturacion_barrio").val("");
		},500);
	};
	
	var SectorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'string'},
			{ name: 'Barrio', type: 'string'},
			{ name: 'Ruta', type: 'string'}
		],
		data: {"Ruta":true},
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
						"Parametros_Agregar_Ruta":true,
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
							$("#parametros_facturacion_items_grid3").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_facturacion_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
			var VAL1 = $("#parametros_facturacion_items_grid3").jqxGrid('getcellvalue', rowid, "Barrio");
			var VAL2 = $("#parametros_facturacion_items_grid3").jqxGrid('getcellvalue', rowid, "Ruta");
			/*alert("ID-> "+ID+" - VAL-> "+VAL)
			commit(false);
			return;*/
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Ruta":true,
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
					
					var ID = $("#parametros_facturacion_items_grid3").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Ruta":true,
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
	var SectorDataAdapter = new $.jqx.dataAdapter(SectorSource);
	
	$("#parametros_facturacion_items_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 400,
		height: 200,
		source: SectorDataAdapter,
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
				width: '7%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_facturacion_items_grid3").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_facturacion_items_grid3").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_facturacion_items_grid3").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_facturacion_items_grid3").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Barrio', datafield: 'Barrio', editable: true, width: '68%', height: 20 },
			{ text: 'Cod. Ruta', datafield: 'Ruta', editable: true, width: '25%', height: 20 },
		]
	});
	$("#parametros_facturacion_items_grid3").on("bindingcomplete", function (event) {
		$("#parametros_facturacion_items_grid3").jqxGrid('localizestrings', localizationobj);
		$("#parametros_facturacion_items_grid3").jqxGrid('hidecolumn', 'ID');
	});
	
	$("#parametros_facturacion_descuento").jqxInput(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		placeHolder: "Ingresar Tipo de Descuento",
	});
	$("#parametros_facturacion_descuento").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row4();
		}
	});
	
	function Add_Row4()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			var Val = $("#parametros_facturacion_descuento").val();
		
			if ( Val == "" || Val < 1) {
				Alerts_Box("Favor Ingresar un Barrio", 3);
				WaitClick_Input("parametros_facturacion_descuento");
				return;
			}
			
			var datainfo = $("#parametros_facturacion_items_grid4").jqxGrid("getdatainformation");
			var count = datainfo.rowscount;
			
			var datarow = [{
				"ID":0,
				"Nombre":Val,
			}];
			
			$("#parametros_facturacion_items_grid4").jqxGrid("addrow", count, datarow);
			$("#parametros_facturacion_descuento").val("");
		},500);
	};
	
	var DescuentoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'string'},
			{ name: 'Nombre', type: 'string'},
		],
		data: {"TipoDcto":true},
		url: "modulos/parametros.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Nombre"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Descuento":true,
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
							$("#parametros_facturacion_items_grid4").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_facturacion_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_facturacion_items_grid4").jqxGrid('getcellvalue', rowid, "Nombre");
			
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
					"Parametros_Actualizar_Descuento":true,
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
					
					var ID = $("#parametros_facturacion_items_grid4").jqxGrid('getcellvalue', rowid, "ID");
					
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
							"Parametros_Borrar_Descuento":true,
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
	
	$("#parametros_facturacion_items_grid4").jqxGrid(
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
					var selectedrowindex = $("#parametros_facturacion_items_grid4").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_facturacion_items_grid4").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_facturacion_items_grid4").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_facturacion_items_grid4").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Tipo Descuento', datafield: 'Nombre', editable: true, width: '90%', height: 20 },
		]
	});
	$("#parametros_facturacion_items_grid4").on("bindingcomplete", function (event) {
		$("#parametros_facturacion_items_grid4").jqxGrid('localizestrings', localizationobj);
		$("#parametros_facturacion_items_grid4").jqxGrid('hidecolumn', 'ID');
	});
	
	$("#parametros_facturacion_motivo").jqxInput(
	{
		theme: mytheme,
		width: 280,
		height: 20,
		placeHolder: "Ingresar Motivo de Modificacion/Anulacion",
	});
	$("#parametros_facturacion_motivo").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row5();
		}
	});
	
	function Add_Row5()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			var Val = $("#parametros_facturacion_motivo").val();
		
			if ( Val == "" || Val < 1) {
				Alerts_Box("Favor Ingresar un Motivo de Anulacion", 3);
				WaitClick_Input("parametros_facturacion_motivo");
				return;
			}
			
			var datainfo = $("#parametros_facturacion_items_grid5").jqxGrid("getdatainformation");
			var count = datainfo.rowscount;
			
			var datarow = [{
				"ID":0,
				"Motivo":Val,
			}];
			
			$("#parametros_facturacion_items_grid5").jqxGrid("addrow", count, datarow);
			$("#parametros_facturacion_motivo").val("");
		},500);
	};
	
	var MotivoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'string'},
			{ name: 'Motivo', type: 'string'},
		],
		data: {"MotivoAnulacion":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Motivo"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Motivo_Anulacion":true,
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
							$("#parametros_facturacion_items_grid5").jqxGrid("updatebounddata");
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
			var ID = $("#parametros_facturacion_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
			var VAL = $("#parametros_facturacion_items_grid5").jqxGrid('getcellvalue', rowid, "Motivo");
			/*alert("ID-> "+ID+" - VAL-> "+VAL)
			commit(false);
			return;*/
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Parametros_Actualizar_Motivo_Anulacion":true,
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
					
					var ID = $("#parametros_facturacion_items_grid5").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Parametros_Borrar_Motivo_Anulacion":true,
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
	
	$("#parametros_facturacion_items_grid5").jqxGrid(
	{
		theme: mytheme,
		width: 280,
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
				width: '10%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#parametros_facturacion_items_grid5").jqxGrid('getselectedrowindex');
					var rowscount = $("#parametros_facturacion_items_grid5").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#parametros_facturacion_items_grid5").jqxGrid('getrowid', selectedrowindex);
						$("#parametros_facturacion_items_grid5").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Motivo', datafield: 'Motivo', editable: true, width: '90%', height: 20 },
		]
	});
	$("#parametros_facturacion_items_grid5").on("bindingcomplete", function (event) {
		$("#parametros_facturacion_items_grid5").jqxGrid('hidecolumn', 'ID');
		$("#parametros_facturacion_items_grid5").jqxGrid('localizestrings', localizationobj);
	});
	
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div style="float: left; margin-left:0px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Vehiculos
		</p>
		<div style="margin:10px 0px 10px 0px;">
			<input type="text" id="parametros_facturacion_placa"/>
		</div>
		<div id="parametros_facturacion_items_grid1"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Servicios Adicionales
		</p>
		<div style="margin:10px 0px 10px 0px;">
			<input type="text" id="parametros_facturacion_servicio"/>
		</div>
		<div id="parametros_facturacion_items_grid2"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Rutas Camiones
		</p>
		<div style="margin:10px 0px 10px 0px;">
			<input type="text" id="parametros_facturacion_barrio"/>
		</div>
		<div id="parametros_facturacion_items_grid3"></div>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div style="float: left; margin-left:0px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Tipo de Descuento
		</p>
		<div style="margin:10px 0px 10px 0px;">
			<input type="text" id="parametros_facturacion_descuento"/>
		</div>
		<div id="parametros_facturacion_items_grid4"></div>
	</div>
	<div style="float: left; margin-left:15px; margin-top: 20px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
			Motivo de Modificacion/Anulacion
		</p>
		<div style="margin:10px 0px 10px 0px;">
			<input type="text" id="parametros_facturacion_motivo"/>
		</div>
		<div id="parametros_facturacion_items_grid5"></div>
	</div>
</div>

