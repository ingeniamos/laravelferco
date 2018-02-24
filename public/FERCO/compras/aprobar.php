<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ID_Cliente = "";
	var OldState = "";
	var NewState = "";
	var Timer = 0;
	var Interno = "";
	
	var MyDate = new Date(currenttime);
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Compras_Content");
	var Body = document.getElementById("Compras_Autorizar_Content");
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
				SystemMap("Aprobar", true);
				
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
	var Modificar = false;
	var Guardar = false;
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Compras" && $data[$i]["SubModulo"] == "Aprobar_Asignar" && $data[$i]["Supervisor"] == "true")
					{
			?>
						Supervisor = true;
			<?php
					}

					if ($data[$i]["Modulo"] == "Compras" && $data[$i]["SubModulo"] == "Aprobar_Asignar" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Compras" && $data[$i]["SubModulo"] == "Aprobar_Asignar" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Compras" && $data[$i]["SubModulo"] == "Aprobar_Asignar" && $data[$i]["Imprimir"] == "true")
					{
			?>
						$("#compras_autorizar_imprimir").jqxButton({ disabled: true });
						$("#compras_autorizar_exportar").jqxButton({ disabled: true });
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
	
	$("#compras_autorizar_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$("#compras_autorizar_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#compras_autorizar_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	$("#compras_autorizar_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$("#compras_autorizar_fecha_fin").jqxDateTimeInput('setDate', new Date(currenttime));
	$("#compras_autorizar_fecha_fin").on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	// prepare the data
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
		async: true
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
	
	$("#compras_autorizar_cliente").jqxComboBox(
	{
		width: 380,
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
	$("#compras_autorizar_cliente").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#compras_autorizar_cliente_ID").val(ID_Cliente);
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#compras_autorizar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 180,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#compras_autorizar_cliente_ID").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#compras_autorizar_cliente").val(ID_Cliente);
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#compras_autorizar_entrada").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150
	});
	
	var EstadoValues = [
		//{"Estado":"Creado"},
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
	
	var VendedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Vendedor', type: 'string'}
		],
		type: 'GET',
		data: {"Venta":true},
		url: "modulos/parametros.php",
		async: true
	};
	var VendedorDataAdapter = new $.jqx.dataAdapter(VendedorSource);
	
	$("#compras_autorizar_digitador").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		source: VendedorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Digitador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#compras_autorizar_digitador").bind('change', function (event) {
		if (!event.args)
		{
			$("#compras_autorizar_digitador").jqxComboBox('clearSelection');
		}
		Add_Row();
	});
	$("#compras_autorizar_digitador").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#compras_autorizar_digitador").val("<?php echo $_SESSION["UserCode"]; ?>");
			$("#compras_autorizar_digitador").jqxComboBox({ disabled: true });
		}
	});
	
	$("#compras_autorizar_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	$("#compras_autorizar_estado").bind('change', function (event) {
		if (event.args)
		{
			Add_Row();
		}
	});
	
	$("#compras_autorizar_estado").jqxComboBox('addItem', {label: "Creado"});
	$("#compras_autorizar_estado").jqxComboBox('addItem', {label: "Autorizado"});
	$("#compras_autorizar_estado").jqxComboBox('addItem', {label: "Pedido"});
	$("#compras_autorizar_estado").jqxComboBox('addItem', {label: "Aprobado"});
	$("#compras_autorizar_estado").jqxComboBox('addItem', {label: "Anulado"});
	
	//---------------------------------------------------------------- PARTE 2
	
	function Add_Row()
	{
		GridSource.data = {
			"Compras_Autorizar":true,
			"Entrada":$("#compras_autorizar_entrada").val(),
			"ClienteID":ID_Cliente,
			"DigitadorID":$("#compras_autorizar_digitador").val(),
			"Estado":$("#compras_autorizar_estado").val(),
			"Fecha_Ini":GetFormattedDate($("#compras_autorizar_fecha_ini").jqxDateTimeInput('getDate')),
			"Fecha_Fin":GetFormattedDate($("#compras_autorizar_fecha_fin").jqxDateTimeInput('getDate'))
		};
		GridDataAdapter = new $.jqx.dataAdapter(GridSource);
		$("#compras_autorizar_items_grid").jqxGrid({source: GridDataAdapter});
		
		/*
		var GridSource = 
		{
			datatype: "json",
			datafields:
			[
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Estado', type: 'string' },
				{ name: 'Motivo_Anulado', type: 'string' },
				{ name: 'Fecha', type: 'string' },
				{ name: 'Interno', type: 'string' },
				{ name: 'Entrada', type: 'string' },
				{ name: 'Doc_Transp', type: 'bool' },
				{ name: 'Pedido', type: 'string' },
				{ name: 'Factura', type: 'string' },
				{ name: 'Valor', type: 'decimal' },
				{ name: 'F_Pago', type: 'string' },
				{ name: 'Cliente', type: 'string' },
				{ name: 'DigitadorID', type: 'string' },
				{ name: 'AutorizadorID', type: 'string' },
			],
			type: 'GET',
			data: {
				"Compras_Autorizar":true,
				"Entrada":$("#compras_autorizar_entrada").val(),
				"ClienteID":ID_Cliente,
				"DigitadorID":$("#compras_autorizar_digitador").val(),
				"Estado":$("#compras_autorizar_estado").val(),
				"Fecha_Ini":GetFormattedDate($("#compras_autorizar_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#compras_autorizar_fecha_fin").jqxDateTimeInput('getDate'))
			},
			url: "modulos/datos.php",
			async: true
		};
		var GridDataAdapter = new $.jqx.dataAdapter(GridSource,{
			autoBind: true,
			loadComplete: function () {
				var myrecords = GridDataAdapter.records;
				var len = myrecords.length;
				$("#compras_autorizar_items_grid").jqxGrid('clear');
				for (var i = 0; i < len; i++) {
					if (myrecords[i]["Estado"] != "") {
						var datarow = [{
							"ClienteID":myrecords[i]["ClienteID"],
							"Autorizar":false,
							"Estado":myrecords[i]["Estado"],
							"Motivo_Anulado":myrecords[i]["Motivo_Anulado"],
							"Fecha":new Date(SetFormattedDate(myrecords[i]["Fecha"])),
							"Interno":myrecords[i]["Interno"],
							"Entrada":myrecords[i]["Entrada"],
							"Doc_Transp":myrecords[i]["Doc_Transp"],
							"Factura":myrecords[i]["Factura"],
							"Valor":myrecords[i]["Valor"],
							"F_Pago":myrecords[i]["F_Pago"],
							"Cliente":myrecords[i]["Cliente"],
							"DigitadorID":myrecords[i]["DigitadorID"],
							"AutorizadorID":myrecords[i]["AutorizadorID"],
						}];
						$("#compras_autorizar_items_grid").jqxGrid("addrow", null, datarow, "first");
					}
				}
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
		*/
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Autorizar', type: 'bool' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Motivo_Anulado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Entrada', type: 'string' },
			{ name: 'Doc_Transp', type: 'string' },
			{ name: 'Pedido', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'F_Pago', type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'AutorizadorID', type: 'string' },
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
			
			if ( OldState == "Anulado")
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
							data: {
								"Compras_Autorizar":true,
								"Interno":rowdata.Interno,
								"Entrada":rowdata.Entrada,
								"Doc_Transp":rowdata.Doc_Transp,
								"Factura":rowdata.Factura,
								"Fecha":GetFormattedDate(rowdata.Fecha),
								"Old_Estado":OldState2,
								"New_Estado":rowdata.Estado,
								"Motivo_Anulado":rowdata.Motivo_Anulado,
								"ClienteID":rowdata.ClienteID,
							},
							url: "modulos/guardar.php",
							async: true,
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
				//var data = "Compras_Autorizar=true&Estado=" + rowdata.Estado + "&Motivo_Anulado=" + rowdata.Motivo_Anulado + "&Fecha=" + GetFormattedDate(rowdata.Fecha);
				//data = data + "&Interno=" + rowdata.Interno + "&Entrada=" + rowdata.Entrada + "&Doc_Transp=" + rowdata.Doc_Transp;
				//data = data + "&Factura=" + rowdata.Factura + "&ClienteID=" + rowdata.ClienteID;
				$.ajax({
					dataType: "json",
					data: {
						"Compras_Autorizar":true,
						"Interno":rowdata.Interno,
						"Entrada":rowdata.Entrada,
						"Doc_Transp":rowdata.Doc_Transp,
						"Factura":rowdata.Factura,
						"Fecha":GetFormattedDate(rowdata.Fecha),
						"Old_Estado":OldState,
						"New_Estado":rowdata.Estado,
						"Motivo_Anulado":rowdata.Motivo_Anulado,
						"ClienteID":rowdata.ClienteID,
					},
					url: "modulos/guardar.php",
					async: true,
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
	
	$("#compras_autorizar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		//height: 300,
		//source: dataAdapter,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		autoheight: true,
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
				text: 'Fecha Comp.',
				datafield: 'Fecha',
				editable: false,
				width: 90,
				height: 20,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
				
			},
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Interno', datafield: 'Interno', editable: false, width: 100, height: 20 },
			{ text: 'Entrada', datafield: 'Entrada', editable: false, width: 100, height: 20 },
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
						if (oldvalue == "" || oldvalue.indexOf("COMP") == 0)
							return newvalue;
						else
							return oldvalue;
					}
					else
						return newvalue;
				}
			},
			{ text: 'Proveedor', datafield: 'Cliente', editable: false, width: 200, height: 20, pinned: true, },
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
			{
				text: 'Doc. Transp.',
				datafield: 'Doc_Transp',
				width: 100,
				height: 20,
				editable: Admin ? true:Modificar,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					if (!Admin)
					{
						if (oldvalue == "" || oldvalue.indexOf("COMP") == 0)
							return newvalue;
						else
							return oldvalue;
					}
					else
						return newvalue;
				}
			},
			{ text: 'Pedido', datafield: 'Pedido', editable: false, width: 100, height: 20 },
			{ text: 'F. Pago', datafield: 'F_Pago', editable: false, width: 70, height: 20 },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 40, height: 20 },
			{ text: 'Aut.', datafield: 'AutorizadorID', editable: false, width: 40, height: 20 },
		]
	});
	$("#compras_autorizar_items_grid").jqxGrid('hidecolumn', 'ClienteID');
	
	$("#compras_autorizar_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Interno")
		{
			Interno = $("#compras_autorizar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Interno");
			if (Interno != "")
				$("#Compras_Autorizar_Compra_Window").jqxWindow('open');
		}
	});
	
	$("#compras_autorizar_items_grid").on('cellendedit', function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Autorizar")
		{
			var EstadoVal = $('#compras_autorizar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			OldState = EstadoVal;
			switch (EstadoVal)
			{
				case "Creado":
					NewState = "Autorizado";
					$("#compras_autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Autorizado");
				break;
				case "Anulado":
					NewState = "Creado";
					$("#ventas_autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Anulado");
				break;
				case "Autorizado":
					NewState = "Creado";
					$("#compras_autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Creado");
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
			OldState = $('#compras_autorizar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			NewState = "Passed";
		}
	});

	// ------------------------------------------ PARTE 3
	
	function Guardar ()
	{
		$("#compras_autorizar_estado").val("Creado");
		Add_Row();
	};
	
	$('#compras_autorizar_guardar').jqxButton({
		width: 150,
		template: "info"
	});
	// Prepare Save Changes...
	$("#compras_autorizar_guardar").bind('click', function ()
	{
		$("#compras_autorizar_items_grid").jqxGrid("updatebounddata");
	});
	
	$("#compras_autorizar_imprimir").jqxButton({width: 150, template: "warning"});
	$("#compras_autorizar_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/compras_listado.php?Entrada="+$("#compras_autorizar_entrada").val()+"&ClienteID="+ID_Cliente+"";
		data += "&DigitadorID="+$("#compras_autorizar_digitador").val()+"&Estado="+$("#compras_autorizar_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#compras_autorizar_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#compras_autorizar_fecha_fin").jqxDateTimeInput('getDate'))+"";
		//alert(data);
		window.open(data, "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	$("#compras_autorizar_exportar").jqxButton({width: 150, template: "success"});
	$("#compras_autorizar_exportar").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Compras_Listado=true&Entrada="+$("#compras_autorizar_entrada").val()+"&ClienteID="+ID_Cliente+"";
		data += "&DigitadorID="+$("#compras_autorizar_digitador").val()+"&Estado="+$("#compras_autorizar_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#compras_autorizar_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#compras_autorizar_fecha_fin").jqxDateTimeInput('getDate'))+"";
		//alert(data);
		window.location = data;
	});
	
	// ------------------------------------------ WINDOWS
	//--- Compra
	$("#Compras_Autorizar_Compra_Window").jqxWindow({
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
	$("#Compras_Autorizar_Compra_Window").on('open', function (event)
	{
		$("#Loading").show();
			$.ajax({
				type:"POST",
				data: {
					"Crear_Emergente":true,
					"Interno":Interno,
				},
				url: "compras/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Compras_Autorizar_Compra_Content").html(data);
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
	});
	
	// Load Default Values
	Add_Row();
	$("#compras_autorizar_entrada").focus();
	CheckRefresh();
});
</script>
<div id="Compras_Autorizar_Compra_Window">
	<div id="Compras_Autorizar_Compra_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Compras_Autorizar_Compra_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Entrada
			</td>
			<td>
				<input type="text" id="compras_autorizar_entrada"/>
			</td>
			<td>
				Cliente
			</td>
			<td>
				<div id="compras_autorizar_cliente"></div>
			</td>
			<td>
				ID Cliente
			</td>
			<td>
				<div id="compras_autorizar_cliente_ID"></div>
			</td>
		</tr>
		<tr>
			<td>
				Digitador
			</td>
			<td>
				<div id="compras_autorizar_digitador"></div>
			</td>
			<td>
				Fecha Ini.
			</td>
			<td>
				<li>
					<div id="compras_autorizar_fecha_ini"></div>
				</li>
				<li class="parte1_li_txt" style="margin-left:10px;">
					Fecha Fin. &nbsp;
				</li>
				<li>
					<div id="compras_autorizar_fecha_fin"></div>
				</li>
			</td>
			<td>
				Estado
			</td>
			<td>
				<div id="compras_autorizar_estado"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="compras_autorizar_items_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="5" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="compras_autorizar_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="compras_autorizar_imprimir" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="compras_autorizar_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>