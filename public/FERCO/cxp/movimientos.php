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
	var ClienteID = "";
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("CxP_Content");
	var Body = document.getElementById("CxP_Movimientos_Content");
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
				SystemMap("Movimientos", true);
				Add_Row();
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
	//var Imprimir = false;
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "CxP" && $data[$i]["SubModulo"] == "Movimientos" && $data[$i]["Supervisor"] == "true")
					{
			?>
						Supervisor = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "CxP" && $data[$i]["SubModulo"] == "Movimientos" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "CxP" && $data[$i]["SubModulo"] == "Movimientos" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "CxP" && $data[$i]["SubModulo"] == "Movimientos" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#cxp_movimientos_imprimir").jqxButton({ disabled: true });
						$("#cxp_movimientos_exportar").jqxButton({ disabled: true });
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
	
	$("#cxp_movimientos_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#cxp_movimientos_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#cxp_movimientos_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	$("#cxp_movimientos_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#cxp_movimientos_fecha_fin").on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
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
	var ClienteDataAdapter1 = new $.jqx.dataAdapter(ClienteSource,{ autoBind: true });
	
	$("#cxp_movimientos_cliente").jqxComboBox(
	{
		width: 345,
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
	$("#cxp_movimientos_cliente").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#cxp_movimientos_cliente_ID").val(ID_Cliente);
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#cxp_movimientos_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 190,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#cxp_movimientos_cliente_ID").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#cxp_movimientos_cliente").val(ID_Cliente);
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#cxp_movimientos_interno").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	
	$("#cxp_movimientos_recibo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	
	$("#cxp_movimientos_deuda").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 400,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 21
	});
	
	var EstadoValues = [
		{"Estado":"Aprobado"},
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

	$("#cxp_movimientos_entrada").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	
	$("#cxp_movimientos_tipo_mov").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		selectedIndex: -1,
		dropDownHeight: 130,
	});
	$("#cxp_movimientos_tipo_mov").bind('change', function (event) {
		if (!event.args)
		{
			$("#cxp_movimientos_tipo_mov").jqxComboBox('clearSelection');
		}
		Add_Row();
	});
	
	$("#cxp_movimientos_tipo_mov").jqxComboBox('addItem', {label: "Abono a Compra"});
	$("#cxp_movimientos_tipo_mov").jqxComboBox('addItem', {label: "Compra"});
	
	$("#cxp_movimientos_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	$("#cxp_movimientos_estado").bind('change', function (event) {
		if (event.args)
		{
			Add_Row();
		}
	});
	
	$("#cxp_movimientos_estado").jqxComboBox('addItem', {label: "Pendiente"});
	$("#cxp_movimientos_estado").jqxComboBox('addItem', {label: "Aprobado"});
	$("#cxp_movimientos_estado").jqxComboBox('addItem', {label: "Anulado"});
	
	//---------------------------------------------------------------- PARTE 2
	
	function Add_Row()
	{
		GridSource.data = {
			"CxP_Movimientos":true,
			"ClienteID":$("#cxp_movimientos_cliente_ID").val(),
			"TipoMovimiento":$("#cxp_movimientos_tipo_mov").val(),
			"Fecha_Ini":GetFormattedDate($('#cxp_movimientos_fecha_ini').jqxDateTimeInput('getDate')),
			"Fecha_Fin":GetFormattedDate($('#cxp_movimientos_fecha_fin').jqxDateTimeInput('getDate')),
			"Estado":$("#cxp_movimientos_estado").val(),
			"Compra_Interno":$("#cxp_movimientos_interno").val(),
			"Compra_Entrada":$("#cxp_movimientos_entrada").val(),
			"Caja_Interno":$("#cxp_movimientos_recibo").val()
		};
		GridDataAdapter = new $.jqx.dataAdapter(GridSource);
		$("#cxp_movimientos_items_grid").jqxGrid({source: GridDataAdapter});
		
		$.ajax({
			dataType: 'text',
			url: "modulos/datos.php",
			data: {
				"CxP_Movimientos_Deuda":true,
				"ClienteID":$("#cxp_movimientos_cliente_ID").val(),
			},
			async: true,
			success: function (data) {
				if (data != "")
					$("#cxp_movimientos_deuda").val(data);
				else
					$("#cxp_movimientos_deuda").val("0");
			},
			error: function (jqXHR, textStatus, errorThrown) {
				Alerts_Box("Ocurrio un Error al intentar modificar los datos!<br />Intente luego de unos segundos...", 3);
			}
		});
		
		/*
		var GridSource = 
		{
			datatype: "json",
			datafields:
			[
				{ name: 'Estado', type: 'string' },
				{ name: 'Fecha', type: 'string' },
				{ name: 'Compra_Interno', type: 'string' },
				{ name: 'Compra_Entrada', type: 'string' },
				{ name: 'Factura', type: 'string' },
				{ name: 'TipoMovimiento', type: 'string' },
				{ name: 'Valor', type: 'decimal' },
				{ name: 'Saldo', type: 'decimal' },
				{ name: 'Nombre', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Caja_Recibo', type: 'string' },
				{ name: 'DigitadorID', type: 'string' },
				{ name: 'AutorizadorID', type: 'string' },
				{ name: 'Deuda', type: 'decimal' },
			],
			type: 'GET',
			data: {
				"CxP_Movimientos":true,
				"ClienteID":$("#cxp_movimientos_cliente_ID").val(),
				"TipoMovimiento":$("#cxp_movimientos_tipo_mov").val(),
				"Fecha_Ini":GetFormattedDate($('#cxp_movimientos_fecha_ini').jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($('#cxp_movimientos_fecha_fin').jqxDateTimeInput('getDate')),
				"Estado":$("#cxp_movimientos_estado").val(),
				"Compra_Interno":$("#cxp_movimientos_interno").val(),
				"Compra_Entrada":$("#cxp_movimientos_entrada").val(),
				"Caja_Recibo":$("#cxp_movimientos_recibo").val(),
			},
			url: "modulos/datos.php",
			async: true
		};
		var GridDataAdapter = new $.jqx.dataAdapter(GridSource,{
			autoBind: true,
			loadComplete: function () {
				var myrecords = GridDataAdapter.records;
				var len = myrecords.length;
				$("#cxp_movimientos_deuda").val(myrecords[0]["Deuda"]);
				$("#cxp_movimientos_items_grid").jqxGrid('clear');
				for (var i = 0;i < len; i++)
				{
					if (myrecords[i]["Estado"] != "")
					{
						var datarow = [{
							"Estado":myrecords[i]["Estado"],
							"Fecha":myrecords[i]["Fecha"],
							"Interno":myrecords[i]["Compra_Interno"],
							"Compra_Interno":myrecords[i]["Compra_Interno"],
							"Compra_Entrada":myrecords[i]["Compra_Entrada"],
							"Factura":myrecords[i]["Factura"],
							"TipoMovimiento":myrecords[i]["TipoMovimiento"],
							"Valor":myrecords[i]["Valor"],
							"Saldo":myrecords[i]["Saldo"],
							"Nombre":myrecords[i]["Nombre"],
							"ClienteID":myrecords[i]["ClienteID"],
							"Caja_Recibo":myrecords[i]["Caja_Recibo"],
							"DigitadorID":myrecords[i]["DigitadorID"],
							"AutorizadorID":myrecords[i]["AutorizadorID"]
						}];
						$("#cxp_movimientos_items_grid").jqxGrid("addrow", null, datarow, "first");
						// Do the Math!
						// Deuda = ?
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
			{ name: 'ID', type: 'string' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Compra_Interno', type: 'string' },
			{ name: 'Compra_Entrada', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'TipoMovimiento', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Saldo', type: 'decimal' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter1.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Caja_Interno', type: 'string' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'AutorizadorID', type: 'string' },
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
			else if (NewState == "Anulado") {
				Alerts_Box("Una vez Anulado, no se podra revertir el proceso! Desea Continuar?", 4, true);
	
				var CheckTimer = setInterval(function() {
					if (ClickOK == true) {
						clearInterval(CheckTimer);
						ClickOK = false;
						
						$.ajax({
							dataType: 'text',
							data: {
								"CxP_Movimientos":true,
								"Estado":rowdata.Estado,
								"Interno":rowdata.Compra_Interno,
								"Entrada":rowdata.Compra_Entrada,
								"ClienteID":rowdata.ClienteID,
								"TipoMovimiento":rowdata.TipoMovimiento,
								"ID":rowdata.ID,
							},
							url: "modulos/guardar.php",
							async: true,
							success: function (data, status, xhr) {
								commit(true);
							},
							error: function (jqXHR, textStatus, errorThrown) {
								alert(textStatus+ " - " +errorThrown);
								commit(false);
							}
						});
					}
					
					if (ClickCANCEL == true) {
						clearInterval(CheckTimer);
						ClickCANCEL = false;
						commit(false);
					}
				}, 10);
			}
			else
			{
				$.ajax({
					dataType: 'text',
					data: {
						"CxP_Movimientos":true,
						"Estado":rowdata.Estado,
						"Interno":rowdata.Compra_Interno,
						"Entrada":rowdata.Compra_Entrada,
						"ClienteID":rowdata.ClienteID,
						"TipoMovimiento":rowdata.TipoMovimiento,
						"ID":rowdata.ID,
					},
					url: "modulos/guardar.php",
					async: true,
					success: function (data, status, xhr) {
						commit(true);
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
					}
				});
			}
			NewState = "";
		}
	};
	
	$("#cxp_movimientos_items_grid").jqxGrid(
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
			{
				text: 'Fecha',
				datafield: 'Fecha',
				editable: false,
				width: 95,
				height: 20,
				pinned: true,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Comp. Interno', datafield: 'Compra_Interno', editable: false, width: 100, height: 20 },
			{ text: 'Comp. Entrada', datafield: 'Compra_Entrada', editable: false, width: 90, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> Factura', datafield: 'Factura', editable: false, width: 100, height: 20, pinned: true, },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> R. Caja', datafield: 'Caja_Recibo', editable: false, width: 100, height: 20, pinned: true, },
			{ text: '', datafield: 'Caja_Interno', editable: false, width: 100, height: 20 },
			{ text: 'Tipo Mov.', datafield: 'TipoMovimiento', editable: false, width: 80, height: 20 },
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
				text: 'Saldo',
				datafield: 'Saldo',
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
			{ text: 'Nombre', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: 150, height: 20, pinned: true, },
			{ text: 'ID', datafield: 'ClienteID', editable: false, width: 90, height: 20 },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 40, height: 20 },
			{ text: 'Aut.', datafield: 'AutorizadorID', editable: false, width: 40, height: 20 },
			{ text: '', datafield: 'ID', editable: false, width: 0 },
		]
	});
	$("#cxp_movimientos_items_grid").jqxGrid('hidecolumn', 'ID');
	$("#cxp_movimientos_items_grid").jqxGrid('hidecolumn', 'Caja_Interno');
	$("#cxp_movimientos_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#cxp_movimientos_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Factura")
		{
			Interno = $("#cxp_movimientos_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Compra_Interno");
			ClienteID = $("#cxp_movimientos_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "ClienteID");
			if (Interno.indexOf("OR") < 0)
				$("#CxP_Movimientos_Compra_Window").jqxWindow('open');
			else
				$("#CxP_Movimientos_Maquinaria_Window").jqxWindow('open');
		}
		else if (datafield == "Caja_Recibo")
		{
			Interno = $("#cxp_movimientos_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Caja_Interno");
			$("#CxP_Movimientos_Caja_Window").jqxWindow('open');
		}
		
	});
	$("#cxp_movimientos_items_grid").on('cellendedit', function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Estado")
		{
			OldState = oldvalue;
			NewState = value;
		}
		else
		{
			NewState = "Passed";
		}
	});
	
	// ------------------------------------------ WINDOWS
	//--- Compra Interno
	$("#CxP_Movimientos_Compra_Window").jqxWindow({
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
	$("#CxP_Movimientos_Compra_Window").on('open', function (event)
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
				$("#CxP_Movimientos_Compra_Content").html(data);
			},
			complete: function()
			{
				$("#Loading").hide();
			}
		});
	});
	
	//--- Maquinaria Interno
	$("#CxP_Movimientos_Maquinaria_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 970,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#CxP_Movimientos_Maquinaria_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Ord_Reparacion":Interno,
				"ClienteID":ClienteID,
			},
			url: "maquinaria/crear.php",
			async: true,
			success: function(data) 
			{
				$("#CxP_Movimientos_Maquinaria_Content").html(data);
			},
			complete: function()
			{
				$("#Loading").hide();
			}
		});
	});
	
	//--- Caja Interno
	$("#CxP_Movimientos_Caja_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 1020,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#CxP_Movimientos_Caja_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Caja_Interno":Interno,
			},
			url: "caja/crear.php",
			async: true,
			success: function(data) 
			{
				$("#CxP_Movimientos_Caja_Content").html(data);
			},
			complete: function()
			{
				$("#Loading").hide();
			}
		});
	});

	// ------------------------------------------ PARTE 3
	
	function Guardar ()
	{
		$("#cxp_movimientos_estado").val("Autorizado");
		Add_Row();
	};
	
	$('#cxp_movimientos_guardar').jqxButton({
		width: 150,
		template: "info"
	});
	// Prepare Save Changes...
	$("#cxp_movimientos_guardar").bind('click', function ()
	{
		$("#cxp_movimientos_items_grid").jqxGrid("updatebounddata");
	});
	
	$("#cxp_movimientos_imprimir").jqxButton({width: 150, template: "warning"});
	$("#cxp_movimientos_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/cxp_movimientos.php?ClienteID="+$("#cxp_movimientos_cliente_ID").val()+"&TipoMovimiento="+$("#cxp_movimientos_tipo_mov").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#cxp_movimientos_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#cxp_movimientos_fecha_fin").jqxDateTimeInput('getDate'))+"";
		data += "&Estado="+$("#cxp_movimientos_estado").val()+"&Entrada="+$("#cxp_movimientos_entrada").val()+"";
		data += "&Interno="+$("#cxp_movimientos_interno").val()+"&Caja_Interno="+$("#cxp_movimientos_recibo").val()+"";
		window.open(data, "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#cxp_movimientos_exportar").jqxButton({width: 150, template: "success"});
	$("#cxp_movimientos_exportar").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?CxP_Movimientos=true&ClienteID="+$("#cxp_movimientos_cliente_ID").val()+"&TipoMovimiento="+$("#cxp_movimientos_tipo_mov").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#cxp_movimientos_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#cxp_movimientos_fecha_fin").jqxDateTimeInput('getDate'))+"";
		data += "&Estado="+$("#cxp_movimientos_estado").val()+"&Entrada="+$("#cxp_movimientos_entrada").val()+"";
		data += "&Interno="+$("#cxp_movimientos_interno").val()+"&Caja_Interno="+$("#cxp_movimientos_recibo").val()+"";
		window.location = data;
	});
	
	// Load Default Values
	Add_Row();
	$("#cxp_movimientos_cliente").focus();
	CheckRefresh();
});
</script>
<div id="CxP_Movimientos_Compra_Window">
	<div id="CxP_Movimientos_Compra_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 420px;">Ver Compra</div>
	</div>
	<div id="CxP_Movimientos_Compra_Content" class="WindowContainer">
	</div>
</div>
<div id="CxP_Movimientos_Maquinaria_Window">
	<div id="CxP_Movimientos_Maquinaria_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Orden de Maquinaria</div>
	</div>
	<div id="CxP_Movimientos_Maquinaria_Content" class="WindowContainer">
	</div>
</div>
<div id="CxP_Movimientos_Caja_Window">
	<div id="CxP_Movimientos_Caja_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 380px;">Ver Recibo de CAJA</div>
	</div>
	<div id="CxP_Movimientos_Caja_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Proveedor
			</td>
			<td colspan="3">
				<div id="cxp_movimientos_cliente"></div>
			</td>
			<td>
				Tipo Mov.
			</td>
			<td>
				<div id="cxp_movimientos_tipo_mov"></div>
			</td>
			<td>
				Fecha I.
			</td>
			<td>
				<div id="cxp_movimientos_fecha_ini"></div>
			</td>
			<td>
				Fecha F.
			</td>
			<td>
				<div id="cxp_movimientos_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				ID Proveedor
			</td>
			<td>
				<div id="cxp_movimientos_cliente_ID"></div>
			</td>
			<td>
				Estado&nbsp;
			</td>
			<td>
				<div id="cxp_movimientos_estado"></div>
			</td>
			<td>
				Entrada
			</td>
			<td>
				<input type="text" id="cxp_movimientos_entrada"/>
			</td>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="cxp_movimientos_interno"/>
			</td>
			<td>
				R. Caja
			</td>
			<td>
				<input type="text" id="cxp_movimientos_recibo"/>
			</td>
		</tr>
		<tr>
			<td>
				Deuda
			</td>
			<td colspan="9">
				<div id="cxp_movimientos_deuda"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="cxp_movimientos_items_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="5" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="cxp_movimientos_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="cxp_movimientos_imprimir" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="cxp_movimientos_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>