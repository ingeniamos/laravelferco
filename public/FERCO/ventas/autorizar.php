<?php
session_start();
?>
<style type="text/css">
.blue {
	color: black;
	background-color: rgba(207,221,233,1);
}
.red {
	color: black;
	background-color: rgba(251,229,214,1);
}
.blue:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .blue:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(207,221,233,1);
}
.red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(251,229,214,1);
}
</style>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var OldState = "";
	var NewState = "";
	var Timer = 0;
	var Interno = "";
	var Estado = "";
	var Listado = new Array();
	var MyDate = new Date(currenttime);
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Ventas_Content");
	var Body = document.getElementById("Autorizar_Content");
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
				SystemMap("Autorizar", true);
				
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
	var Admin = false;
	var Supervisor = false;
	var Guardar = false;
	var Modificar = false;
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Autorizar_Asignar" && $data[$i]["Supervisor"] == "true")
					{
			?>
						Supervisor = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Autorizar_Asignar" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Autorizar_Asignar" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Autorizar_Asignar" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#ventas_autorizar_imprimir").jqxButton({ disabled: true });
						$("#ventas_autorizar_exportar").jqxButton({ disabled: true });
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
	
	ClearJSON = [
		{id:"ventas_autorizar_ord_compra", type:""},
		{id:"ventas_autorizar_cliente", type:"jqxComboBox"},
		{id:"ventas_autorizar_cliente_ID", type:"jqxComboBox"},
		{id:"ventas_autorizar_vendedor", type:"jqxComboBox"},
		{id:"ventas_autorizar_fecha_ini", type:"jqxDateTimeInput"},
		{id:"ventas_autorizar_fecha_fin", type:"jqxDateTimeInput"},
		{id:"ventas_autorizar_estado", type:"jqxComboBox"},
		{id:"ventas_autorizar_items_grid", type:"jqxGrid"},
	];
	
	$("#ventas_autorizar_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#ventas_autorizar_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$('#ventas_autorizar_fecha_ini').on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	$("#ventas_autorizar_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#ventas_autorizar_fecha_fin").jqxDateTimeInput('setDate', new Date(currenttime));
	$('#ventas_autorizar_fecha_fin').on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		id: "ClienteID",
		url: "modulos/datos.php",
		async: false
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter1 = new $.jqx.dataAdapter(ClienteSource,{ autoBind: true })
	
	$("#ventas_autorizar_cliente").jqxComboBox(
	{
		width: 260,
		height: 20,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#ventas_autorizar_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#ventas_autorizar_cliente_ID").val() != event.args.item.value) {
				$("#ventas_autorizar_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
			}
			LoadValues();
		}
	});
	
	$("#ventas_autorizar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#ventas_autorizar_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#ventas_autorizar_cliente").val() != event.args.item.value){
				$("#ventas_autorizar_cliente").jqxComboBox('selectItem', event.args.item.value);
			}
			LoadValues();
		}

	});
	
	$("#ventas_autorizar_ord_compra").jqxInput({
		theme: mytheme,
		height: 20,
		width: 83
	});
	$("#ventas_autorizar_ord_compra").on('change', function (event) 
	{
		LoadValues();
	});
	
	$("#ventas_autorizar_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 83
	});
	$("#ventas_autorizar_factura").on('change', function (event) 
	{
		LoadValues();
	});
	
	$("#ventas_autorizar_ord_produccion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 83
	});
	$("#ventas_autorizar_ord_produccion").on('change', function (event) 
	{
		LoadValues();
	});
	
	var EstadoValues = [
		{"Estado":"Creado"},
		{"Estado":"Autorizado"},
		{"Estado":"Anulado"},
	];
	
	var EstadoSource = {
		localdata: EstadoValues,
		datatype: "json",
		datafields:[
			{ name: 'Estado', type: 'string' }
		]
	};
	var EstadoAdapter = new $.jqx.dataAdapter(EstadoSource);
	
	var VendedorData = new Array();
	
	var VendedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Vendedor', type: 'string'}
		],
		data: {"Venta":true},
		url: "modulos/parametros.php",
		async: false
	};
	var VendedorDataAdapter = new $.jqx.dataAdapter(VendedorSource,{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				VendedorData.push(records[i]["Codigo"]);
			}
		}
	});
	
	$("#ventas_autorizar_vendedor").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		source: VendedorDataAdapter,
		//dropDownHeight: 100,
		promptText: 'Seleccionar Vendedor',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#ventas_autorizar_vendedor").bind('change', function (event) {
		if (!event.args)
		{
			$("#ventas_autorizar_vendedor").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	$("#ventas_autorizar_vendedor").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#ventas_autorizar_vendedor").jqxComboBox('selectItem', "<?php echo $_SESSION["UserCode"]; ?>");
			$("#ventas_autorizar_vendedor").jqxComboBox({ disabled: true });
		}
	});
	
	$("#ventas_autorizar_cobrador").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		source: VendedorDataAdapter,
		promptText: 'Seleccionar Cobrador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#ventas_autorizar_cobrador").bind('change', function (event) {
		if (!event.args)
		{
			$("#ventas_autorizar_cobrador").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#ventas_autorizar_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	$("#ventas_autorizar_estado").bind('change', function (event) {
		if (event.args)
		{
			LoadValues();
		}
	});
	
	$("#ventas_autorizar_estado").jqxComboBox('addItem', {label: "Creado"});
	$("#ventas_autorizar_estado").jqxComboBox('addItem', {label: "Cotizacion"});
	$("#ventas_autorizar_estado").jqxComboBox('addItem', {label: "Autorizado"});
	$("#ventas_autorizar_estado").jqxComboBox('addItem', {label: "Aprobado"});
	$("#ventas_autorizar_estado").jqxComboBox('addItem', {label: "Anulado"});
	$("#ventas_autorizar_estado").jqxComboBox('addItem', {label: "Pendiente"});
	
	//---------------------------------------------------------------- PARTE 2
	
	function LoadValues()
	{
		clearTimeout(Timer);
		Timer = setTimeout(function()
		{
			GridSource.data = {
				"Ventas_Autorizar":true,
				"Ord_Compra":$("#ventas_autorizar_ord_compra").val(),
				"Factura":$("#ventas_autorizar_factura").val(),
				"Ord_Produccion":$("#ventas_autorizar_ord_produccion").val(),
				"ClienteID":$("#ventas_autorizar_cliente_ID").val(),
				"VendedorID":$("#ventas_autorizar_vendedor").val(),
				"CobradorID":$("#ventas_autorizar_cobrador").val(),
				"Estado":$("#ventas_autorizar_estado").val(),
				"Fecha_Ini":GetFormattedDate($('#ventas_autorizar_fecha_ini').jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($('#ventas_autorizar_fecha_fin').jqxDateTimeInput('getDate'))
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#ventas_autorizar_items_grid").jqxGrid({source: GridDataAdapter});
		},500);
	};
	
	var cellclass = function (row, columnfield, value)
	{
		var OP = $("#ventas_autorizar_items_grid").jqxGrid('getcellvalue', row, "Ord_Produccion");
		if (OP != "")
			return 'blue';
		else
			return '';
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Autorizar', type: 'bool' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Motivo_Anulado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Ord_Compra', type: 'string' },
			{ name: 'Ord_Produccion', type: 'string' },
			{ name: 'Remision', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'F_Pago', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter1.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'Enviado', type: 'bool' },
			{ name: 'VendedorID', type: 'string' },
			{ name: 'CobradorID', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'AutorizadorID', type: 'string' },
			{ name: 'AprobadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
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
						//var data = "Ventas_Autorizar=true&Estado=" + rowdata.Estado + "&Motivo_Anulado=" + rowdata.Motivo_Anulado + "&Fecha=" + GetFormattedDate(rowdata.Fecha);
						//data = data + "&Remision=" + rowdata.Remision + "&Factura=" + rowdata.Factura + "&VendedorID=" + rowdata.VendedorID + "&CobradorID=" + rowdata.CobradorID;
						//data = data + "&Ord_Compra=" + rowdata.Ord_Compra + "&Ord_Produccion=" + rowdata.Ord_Produccion + "&ClienteID=" + rowdata.ClienteID + "&Interno=" + rowdata.Interno;
						$.ajax({
							dataType: "json",
							url: "modulos/guardar.php",
							data: {
								"Ventas_Autorizar":true,
								"Interno":rowdata.Interno,
								"Ord_Compra":rowdata.Ord_Compra,
								"Remision":rowdata.Remision,
								"Factura":rowdata.Factura,
								"Ord_Produccion":rowdata.Ord_Produccion,
								"Fecha":GetFormattedDate(rowdata.Fecha),
								"Old_Estado":OldState2,
								"New_Estado":rowdata.Estado,
								"Motivo_Anulado":rowdata.Motivo_Anulado,
								"ClienteID":rowdata.ClienteID,
								"VendedorID":rowdata.VendedorID,
								"CobradorID":rowdata.CobradorID,
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
				//var data = "Ventas_Autorizar=true&Estado=" + rowdata.Estado + "&Motivo_Anulado=" + rowdata.Motivo_Anulado + "&Fecha=" + GetFormattedDate(rowdata.Fecha);
				//data = data + "&Remision=" + rowdata.Remision + "&Factura=" + rowdata.Factura + "&VendedorID=" + rowdata.VendedorID + "&CobradorID=" + rowdata.CobradorID;
				//data = data + "&Ord_Compra=" + rowdata.Ord_Compra + "&Ord_Produccion=" + rowdata.Ord_Produccion + "&ClienteID=" + rowdata.ClienteID + "&Interno=" + rowdata.Interno;
				$.ajax({
					dataType: "json",
					url: "modulos/guardar.php",
					data: {
						"Ventas_Autorizar":true,
						"Interno":rowdata.Interno,
						"Ord_Compra":rowdata.Ord_Compra,
						"Remision":rowdata.Remision,
						"Factura":rowdata.Factura,
						"Ord_Produccion":rowdata.Ord_Produccion,
						"Fecha":GetFormattedDate(rowdata.Fecha),
						"Old_Estado":OldState,
						"New_Estado":rowdata.Estado,
						"Motivo_Anulado":rowdata.Motivo_Anulado,
						"ClienteID":rowdata.ClienteID,
						"VendedorID":rowdata.VendedorID,
						"CobradorID":rowdata.CobradorID,
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
	
	$("#ventas_autorizar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		showfilterrow: true,
		filterable: true,
		sortable: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'ClienteID', width: 0, height: 0 },
			{
				text: '',
				datafield: 'Autorizar',
				columntype: 'checkbox',
				width: 15,
				pinned: true,
				editable: Admin ? true:Guardar,
				filterable: false,
			},
			{
				text: 'Estado',
				datafield: 'Estado',
				width: 70,
				height: 20,
				pinned: true,
				editable: Admin ? true:Guardar,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoAdapter,
						dropDownHeight: 125,
						dropDownWidth: 100,
						selectedIndex: -1,
						displayMember: 'Estado',
						valueMember: 'Estado',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{ text: 'Motivo. Anul.', datafield: 'Motivo_Anulado', width: 100, height: 20 },
			{
				text: 'Fecha Rem.',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: 90,
				height: 20,
				editable: Admin,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>O. Comp.', datafield: 'Ord_Compra', editable: false, width: 100, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>O. Prod.', datafield: 'Ord_Produccion', editable: false, width: 100, height: 20, cellclassname: cellclass },
			{
				text: 'Remision',
				datafield: 'Remision',
				width: 100,
				height: 20,
				editable: Admin ? true:Modificar,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					if (!Admin)
					{
						if (oldvalue == "" || oldvalue.indexOf("FER") == 0)
							return newvalue;
						else
							return oldvalue;
					}
					else
						return newvalue;
				}
			},
			{
				text: 'Factura',
				datafield: 'Factura',
				width: 100,
				height: 20,
				editable: Admin ? true:Modificar,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					if (!Admin)
					{
						if (oldvalue == "" || oldvalue.indexOf("FER") == 0)
							return newvalue;
						else
							return oldvalue;
					}
					else
						return newvalue;
				}
			},
			{
				text: 'Valor',
				datafield: 'Valor',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'F. Pago', datafield: 'F_Pago', editable: false, width: 70, height: 20 },
			{ text: 'Cliente', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: 150, height: 20, pinned: true, },
			//{ text: 'Enviado', datafield: 'Enviado', editable: false, width: 60, height: 20 },
			{ text: 'Interno', datafield: 'Interno', editable: false, width: 100, height: 20 },
			{
				text: 'Vend.', datafield: 'VendedorID', columntype: 'combobox', width: 50, height: 20, editable: Admin,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: VendedorData,
						dropDownHeight: 125,
						selectedIndex: -1,
						//displayMember: 'Codigo',
						//valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Cobr.', datafield: 'CobradorID', columntype: 'combobox', width: 50, height: 20, editable: Admin,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: VendedorData,
						dropDownHeight: 125,
						selectedIndex: -1,
						//displayMember: 'Codigo',
						//valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 40, height: 20 },
			{ text: 'Aut.', datafield: 'AutorizadorID', editable: false, width: 40, height: 20 },
			{ text: 'Desp.', datafield: 'AprobadorID', editable: false, width: 40, height: 20 },
		]
	});
	$("#ventas_autorizar_items_grid").jqxGrid('hidecolumn', 'ClienteID');
	//$("#ventas_autorizar_items_grid").jqxGrid('hidecolumn', 'Interno');
	$("#ventas_autorizar_items_grid").jqxGrid('localizestrings', localizationobj);
	
	$("#ventas_autorizar_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Ord_Compra")
		{
			var datinfo = $("#ventas_autorizar_items_grid").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			var ListadoTmp = new Array();
			for (var i = 0; i < count; i++)
			{
				var tmp_array = {};
				var currentRow = $("#ventas_autorizar_items_grid").jqxGrid('getrowdata', i);
				tmp_array["Interno"] = currentRow.Interno;
				tmp_array["Estado"] = currentRow.Estado;
				ListadoTmp[i] = tmp_array;
			}
			Listado = ListadoTmp;
			
			Interno = $('#ventas_autorizar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Interno");
			Estado = $('#ventas_autorizar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			if (Interno != "")
				$("#Ventas_Autorizar_Remision_Window").jqxWindow('open');
		}
		else if (datafield == "Ord_Produccion")
		{
			Interno = $('#ventas_autorizar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Ord_Produccion");
			if (Interno != "")
				$("#Ventas_Autorizar_Produccion_Window").jqxWindow('open');
			/*else
				Alerts_Box("Esta Factura no posee Orden de Produccion.", 1);*/
		}
		
	});
	
	$("#ventas_autorizar_items_grid").on('cellendedit', function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Autorizar")
		{
			var EstadoVal = $('#ventas_autorizar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			OldState = EstadoVal;
			switch (EstadoVal)
			{
				case "Aprobado":
					NewState = "Anulado";
					$("#ventas_autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Anulado");
				break;
				case "Anulado":
					NewState = "Pendiente";
					$("#ventas_autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Pendiente");
				break;
				case "Pendiente":
				case "Creado":
					NewState = "Autorizado";
					$("#ventas_autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Autorizado");
				break;
				case "Autorizado":
					NewState = "Creado";
					$("#ventas_autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Creado");
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
			OldState = $('#ventas_autorizar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			NewState = "Passed";
		}
		
	});

	// ------------------------------------------ PARTE 3
	
	/*function Guardar()
	{
		$("#ventas_autorizar_estado").val("Creado");
		LoadValues();
	};*/
	
	$('#ventas_autorizar_guardar').jqxButton({
		width: 150,
		template: "info"
	});
	// Prepare Save Changes...
	$("#ventas_autorizar_guardar").bind('click', function ()
	{
		$("#ventas_autorizar_items_grid").jqxGrid("updatebounddata");
	});
	$("#ventas_autorizar_imprimir").jqxButton({width: 150, template: "warning"});
	$("#ventas_autorizar_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/listado.php?Ord_Compra="+$("#ventas_autorizar_ord_compra").val()+"&Factura=";
		data += $("#ventas_autorizar_factura").val()+"&Ord_Produccion=";
		data += $("#ventas_autorizar_ord_produccion").val()+"&ClienteID="+$("#ventas_autorizar_cliente_ID").val();
		data += "&VendedorID="+$("#ventas_autorizar_vendedor").val()+"&Estado="+$("#ventas_autorizar_estado").val();
		data += "&Fecha_Ini="+GetFormattedDate($('#ventas_autorizar_fecha_ini').jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($('#ventas_autorizar_fecha_fin').jqxDateTimeInput('getDate'));
		//alert(data);
		window.open(data, "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	$("#ventas_autorizar_exportar").jqxButton({width: 150, template: "success"});
	$("#ventas_autorizar_exportar").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Ventas_Listado=true&Ord_Compra="+$("#ventas_autorizar_ord_compra").val();
		data += "&Factura="+$("#ventas_autorizar_factura").val();
		data += "&ClienteID="+$("#ventas_autorizar_cliente_ID").val()+"&Ord_Produccion="+$("#ventas_autorizar_ord_produccion").val();
		data += "&VendedorID="+$("#ventas_autorizar_vendedor").val()+"&Estado="+$("#ventas_autorizar_estado").val();
		data += "&Fecha_Ini="+GetFormattedDate($('#ventas_autorizar_fecha_ini').jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($('#ventas_autorizar_fecha_fin').jqxDateTimeInput('getDate'));
		//alert(data);
		window.location = data;
	});
	
	// ------------------------------------------ WINDOWS
	//--- Remision
	$("#Ventas_Autorizar_Remision_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 1030,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Ventas_Autorizar_Remision_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Interno":Interno,
				"Estado":Estado,
				"Listado":Listado,
			},
			url: "ventas/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Ventas_Autorizar_Remision_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	//--- Produccion
	$("#Ventas_Autorizar_Produccion_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 960,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Ventas_Autorizar_Produccion_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Ord_Produccion":Interno,
			},
			url: "produccion/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Ventas_Autorizar_Produccion_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	// Load Default Values
	LoadValues();
	$("#ventas_autorizar_ord_compra").focus();
	CheckRefresh();
});
</script>
<div id="Ventas_Autorizar_Remision_Window">
	<div id="Ventas_Autorizar_Remision_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Ventas_Autorizar_Remision_Content" class="WindowContainer">
	</div>
</div>

<div id="Ventas_Autorizar_Produccion_Window">
	<div id="Ventas_Autorizar_Produccion_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Orden de Produccion</div>
	</div>
	<div id="Ventas_Autorizar_Produccion_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				O. Comp.
			</td>
			<td colspan="5">
				<li>
					<input type="text" id="ventas_autorizar_ord_compra"/>
				</li>
				<li class="parte1_li_txt" style="margin:0px 4px;">
					Factura
				</li>
				<li>
					<input type="text" id="ventas_autorizar_factura"/>
				</li>
				<li class="parte1_li_txt" style="margin:0px 4px;">
					O. Prod.
				</li>
				<li>
					<input type="text" id="ventas_autorizar_ord_produccion"/>
				</li>
			</td>
			<td>
				Cliente
			</td>
			<td>
				<div id="ventas_autorizar_cliente"></div>
			</td>
			<td>
				ID Cliente
			</td>
			<td>
				<div id="ventas_autorizar_cliente_ID"></div>
			</td>
		</tr>
		<tr>
			<td>
				Vendedor
			</td>
			<td>
				<div id="ventas_autorizar_vendedor"></div>
			</td>
			<td>
				Cobrador
			</td>
			<td>
				<div id="ventas_autorizar_cobrador"></div>
			</td>
			<td colspan="6">
				<li class="parte1_li_txt" style="margin-right:5px;">
					Estado
				</li>
				<li>
					<div id="ventas_autorizar_estado"></div>
				</li>
				<li class="parte1_li_txt" style="margin:0px 4px;">
					Fecha I.
				</li>
				<li>
					<div id="ventas_autorizar_fecha_ini"></div>
				</li>
				<li class="parte1_li_txt" style="margin:0px 9px;">
					Fecha F.
				</li>
				<li>
					<div id="ventas_autorizar_fecha_fin"></div>
				</li>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="ventas_autorizar_items_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="5" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="ventas_autorizar_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="ventas_autorizar_imprimir" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="ventas_autorizar_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>