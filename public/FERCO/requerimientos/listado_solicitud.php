<?php
session_start();
?>
<style type="text/css">
#requerimientos_listado_spoiler
{
	width: 150px;
	height: 20px;
	padding: 5px 5px 0px 5px;
	display: block;
	font-size:13px;
	margin: 0px;
	color: #FFF;
	background-color: #5BB75B;
}
#requerimientos_listado_spoiler:hover
{
	cursor: pointer;
}
</style>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var OldState = "";
	var NewState = "";
	var Interno = "";
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Requerimientos_Content");
	var Body = document.getElementById("Requerimientos_Listado");
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
				LoadValues();
				
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
	var Supervisor = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador")
		{
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Requerimientos" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Requerimientos" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Modificar"] == "true")
				{
		?>
					Modificar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Requerimientos" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Supervisor"] == "true")
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
	
	Day = Day;
	Day = Day + "";
	if (Day.length == 1)
	{
		Day = "0" + Day;
	};
	
	Month = Month - 1;
	Month = Month + "";
	if (Month.length == 1)
	{
		Month = "0" + Month;
	};
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadParameters()
	{
		ClientesNominaSource.data = {"Clientes_Nomina":true};
		VendedoresSource.data = {"Venta":true};
		//ProductosSource.data = {"Requerimientos_Productos":true};
		
		var ClientesNominaDataAdapter = new $.jqx.dataAdapter(ClientesNominaSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#requerimientos_listado_cliente_ID").jqxComboBox({source: records});
				$("#requerimientos_listado_cliente").jqxComboBox({source: records});
			},
		});
		
		var VendedoresDataAdapter = new $.jqx.dataAdapter(VendedoresSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#requerimientos_listado_digitador").jqxComboBox({source: records});
			},
		});
		
		$("#requerimientos_listado_estado").jqxComboBox('addItem', {label: "Pendiente"});
		$("#requerimientos_listado_estado").jqxComboBox('addItem', {label: "Aprobado"});
		$("#requerimientos_listado_estado").jqxComboBox('addItem', {label: "Anulado"});
		
		/*var ProductosDataAdapter = new $.jqx.dataAdapter(ProductosSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++) {
					UnidadArray[records[i]["Codigo"]] = records[i]["Unidad"];
					ValorArray[records[i]["Codigo"]] = records[i]["Valor"];
				}
			},
			loadComplete: function (records)
			{
				$("#requerimientos_listado_codigo").jqxComboBox({source: records});
				$("#requerimientos_listado_producto").jqxComboBox({source: records});
			},
		});*/
	}
	
	var ClientesNominaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
	};
	
	var VendedoresSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Vendedor', type: 'string'}
		],
		url: "modulos/parametros.php",
	};
	
	var EstadoValues = [
		{"Estado":"Aprobado"},
		{"Estado":"Anulado"},
	];
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'Unidad', type: 'string'},
			{ name: 'Peso', type: 'decimal'},
			{ name: 'Valor', type: 'decimal'},
		],
		url: "modulos/datos_productos.php",
	};
	
	$("#requerimientos_listado_interno").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
	});
	$("#requerimientos_listado_interno").on("change", function (event) 
	{
		LoadValues();
	});
	
	$("#requerimientos_listado_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 270,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Empleado',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#requerimientos_listado_cliente").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_listado_cliente_ID").val() != event.args.item.value)
				$("#requerimientos_listado_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_listado_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#requerimientos_listado_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_listado_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_listado_cliente").val();
				
				var item = $("#requerimientos_listado_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#requerimientos_listado_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#requerimientos_listado_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_listado_cliente").jqxComboBox('clearSelection');
			}
			LoadValues();
		}
	});
	
	$("#requerimientos_listado_cliente_ID").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#requerimientos_listado_cliente_ID").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_listado_cliente").val() != event.args.item.value)
				$("#requerimientos_listado_cliente").jqxComboBox('selectItem', event.args.item.value);
			LoadValues();
		}
		else
		{
			var item_value = $("#requerimientos_listado_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#requerimientos_listado_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_listado_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_listado_cliente_ID").val();
				var item = $("#requerimientos_listado_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#requerimientos_listado_cliente_ID").jqxComboBox('clearSelection');
					$("#requerimientos_listado_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#requerimientos_listado_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#requerimientos_listado_estado").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		selectedIndex: 0,
	});
	$("#requerimientos_listado_estado").on('change', function (event)
	{
		if (event.args)
			LoadValues();
	});
	
	$("#requerimientos_listado_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: "dd-MMM-yyyy",
		culture: "es-ES",
	});
	$("#requerimientos_listado_fecha_ini").jqxDateTimeInput("setDate", new Date(Year, Month, Day));
	$("#requerimientos_listado_fecha_ini").on("change", function (event) 
	{
		if(event.args)
			LoadValues();
	});
	$("#requerimientos_listado_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: "dd-MMM-yyyy",
		culture: "es-ES",
	});
	$("#requerimientos_listado_fecha_fin").on("change", function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#requerimientos_listado_digitador").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Digitador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo'
	});
	$("#requerimientos_listado_digitador").on('change', function (event)
	{
		if (!event.args)
			$("#requerimientos_listado_digitador").jqxComboBox("clearSelection");
		LoadValues();
	});
	$("#requerimientos_listado_digitador").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#requerimientos_listado_digitador").jqxComboBox("selectItem", "<?php echo $_SESSION["UserCode"]; ?>");
			$("#requerimientos_listado_digitador").jqxComboBox({ disabled: true });
		}
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		var LoadTimer = setTimeout(function()
		{
			clearTimeout(LoadTimer);
			//$("#requerimientos_listado_items_grid").jqxGrid("clear");
			//$("#requerimientos_listado_items_grid2").jqxGrid("clear");
			
			GridSource.data = {
				"Requerimientos_Listado":true,
				"Interno":$("#requerimientos_listado_interno").val(),
				"ClienteID":$("#requerimientos_listado_cliente_ID").val(),
				"DigitadorID":$("#requerimientos_listado_digitador").val(),
				"Estado":$("#requerimientos_listado_estado").val(),
				"Fecha_Ini":GetFormattedDate($("#requerimientos_listado_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#requerimientos_listado_fecha_fin").jqxDateTimeInput("getDate"))
			};
			GridSource2.data = {
				"Requerimientos_Listado_Faltantes":true,
				"Interno":$("#requerimientos_listado_interno").val(),
				"ClienteID":$("#requerimientos_listado_cliente_ID").val(),
				"DigitadorID":$("#requerimientos_listado_digitador").val(),
				"Estado":$("#requerimientos_listado_estado").val(),
				"Fecha_Ini":GetFormattedDate($("#requerimientos_listado_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#requerimientos_listado_fecha_fin").jqxDateTimeInput("getDate"))
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			var GridDataAdapter2 = new $.jqx.dataAdapter(GridSource2);
			$("#requerimientos_listado_items_grid").jqxGrid({source: GridDataAdapter});
			$("#requerimientos_listado_items_grid2").jqxGrid({source: GridDataAdapter2});
		},500);
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Aprobar', type: 'bool' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'AprobadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		updaterow: function (rowid, rowdata, commit)
		{
			if (NewState == "" || NewState == undefined)
			{
				commit(true);
				if (NewState != "Passed")
				return;
			}
			
			if (OldState == "Anulado")
			{
				Alerts_Box("Un Movimiento Anulado, no es posible Cambiar su estado.", 3);
				commit(false);
			}
			else if (NewState == "Anulado")
			{
				Alerts_Box("Una vez Anulado, no se podra revertir el proceso! Desea Continuar?", 4, true);
				var OldState2 = OldState
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						$.ajax({
							dataType: "json",
							type: "POST",
							url: "modulos/guardar.php",
							data: {
								"Requerimientos_Listado":true,
								"Interno":rowdata.Interno,
								"Old_Estado":OldState2,
								"New_Estado":rowdata.Estado,
							},
							success: function (data, status, xhr)
							{
								switch(data[0]["MESSAGE"])
								{
									case "OK":
										commit(true);
									break;
									
									case "CHANGED":
										commit(false);
										Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
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
								Alerts_Box("Ocurrió un Error al Intentar Actualizar los Datos.<br />Contacte al Soporte Tecnico.<br />"
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
			else
			{
				$.ajax({
					dataType: "json",
					type: "POST",
					url: "modulos/guardar.php",
					data: {
						"Requerimientos_Listado":true,
						"Interno":rowdata.Interno,
						"Old_Estado":OldState,
						"New_Estado":rowdata.Estado,
					},
					success: function (data, status, xhr)
					{
						switch(data[0]["MESSAGE"])
						{
							case "OK":
								commit(true);
							break;
							
							case "CHANGED":
								commit(false);
								Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
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
						Alerts_Box("Ocurrió un Error al Intentar Actualizar los Datos.<br />Contacte al Soporte Tecnico.<br />"
						+"Error: "+errorThrown, 3);
					}
				});
			}
			OldState = "";
			NewState = "";
		}
	};
	
	var GridSource2 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Unidad', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Existencia', type: 'decimal' },
		],
		url: "modulos/datos.php",
	};
	
	$("#requerimientos_listado_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		showfilterrow: true,
		filterable: true,
		sortable: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Aprobar',
				columntype: 'checkbox',
				width: "3%",
				editable: Admin ? true:Guardar,
				filterable: false,
			},
			{
				text: 'Estado',
				datafield: 'Estado',
				width: "10%",
				height: 20,
				editable: Admin ? true:Guardar,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoValues,
						dropDownHeight: 125,
						dropDownWidth: 100,
						selectedIndex: -1,
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: "12%",
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
			},
			{ text: 'Empleado', datafield: 'Cliente', editable: false, width: "40%", height: 20 },
			{ text: 'Empleado ID', datafield: 'ClienteID', editable: false, width: "15%", height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> Interno', datafield: 'Interno', editable: false, width: "10%", height: 20 },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: "5%", height: 20 },
			{ text: 'Apr.', datafield: 'AprobadorID', editable: false, width: "5%", height: 20 },
		]
	});
	$("#requerimientos_listado_items_grid").jqxGrid("localizestrings", localizationobj);
	$("#requerimientos_listado_items_grid").on("celldoubleclick", function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		/*if (datafield == "Interno")
		{
			Interno = $("#requerimientos_listado_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Interno");
			if (Interno != "")
				$("#Requerimientos_Listado_Solicitud_Window").jqxWindow('open');
		}*/
	});
	$("#requerimientos_listado_items_grid").on("cellendedit", function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Aprobar")
		{
			var EstadoVal = $("#requerimientos_listado_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Estado");
			OldState = EstadoVal;
			switch (EstadoVal)
			{
				case "Aprobado":
					NewState = "Anulado";
					$("#requerimientos_listado_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Estado", "Anulado");
				break;
				case "Anulado":
					NewState = "Pendiente";
					$("#requerimientos_listado_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Estado", "Pendiente");
				break;
				case "Pendiente":
					NewState = "Aprobado";
					$("#requerimientos_listado_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Estado", "Aprobado");
				break;
			}
		}
		else if (datafield == "Estado")
		{
			OldState = oldvalue;
			NewState = value;
		}
		else
		{
			OldState = $("#requerimientos_listado_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Estado");
			NewState = "Passed";
		}
	});
	
	$("#requerimientos_listado_spoiler").click(function()
	{
		var Element = document.getElementById("requerimientos_listado_items_grid2");
		if (jQuery.css( Element, "display" ) === "none")
		{
			$("#requerimientos_listado_spoiler").html("Cerrar Total Detalles");
			$("#requerimientos_listado_items_grid2").show();
		}
		else
		{
			$("#requerimientos_listado_spoiler").html("Ver Total Detalles");
			$("#requerimientos_listado_items_grid2").hide();
		}	
	});
	
	$("#requerimientos_listado_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 10,
		sortable: true,
		editable: false,
		editmode: 'dblclick',
		columns:
		[
			{ text: 'Codigo', datafield: 'Codigo', width: '10%', height: 20 },
			{ text: 'Nombre', datafield: 'Nombre', width: '35%', height: 20 },
			{ text: 'Unidad', datafield: 'Unidad', width: '6%', height: 20 },
			{
				text: 'Total',
				datafield: 'Cantidad',
				width: '19%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
			{
				text: 'Existencia',
				datafield: 'Existencia',
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
			{
				text: 'Faltante',
				datafield: 'Faltante',
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
		]
	});
	$("#requerimientos_listado_items_grid2").jqxGrid('localizestrings', localizationobj);
	
	LoadParameters();
	LoadValues();
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Solicitud Nº
			</td>
			<td>
				<input type="text" id="requerimientos_listado_interno"></div>
			</td>
			<td>
				Empleado
			</td>
			<td colspan="3">
				<div id="requerimientos_listado_cliente"></div>
			</td>
			<td>
				Empleado ID
			</td>
			<td>
				<div id="requerimientos_listado_cliente_ID"></div>
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<div id="requerimientos_listado_estado"></div>
			</td>
			<td>
				Fecha Ini.
			</td>
			<td>
				<div id="requerimientos_listado_fecha_ini"></div>
			</td>
			<td>
				Fecha Fin.
			</td>
			<td>
				<div id="requerimientos_listado_fecha_fin"></div>
			</td>
			<td>
				Digitador
			</td>
			<td>
				<div id="requerimientos_listado_digitador"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="requerimientos_listado_items_grid" style="margin:0px 0px 10px 0px;"></div>
	
	<div align="left" style="width: 1000px; border-bottom: #A9A9A9 1px solid; margin:15px 0px;">
		<p id="requerimientos_listado_spoiler">Ver Total Detalles</p>
	</div>
	<div id="requerimientos_listado_items_grid2" style="dispaly: block;"></div>
</div>
