<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Caja_Content");
	var Body = document.getElementById("AprobarRC_Content");
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
	
	//---- GLOBAL VARIABLES
	var ID_Cliente = "";
	var Interno = "";
	var OldState = "";
	var NewState = "";
	var Timer = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
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
					if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Aprobar" && $data[$i]["Supervisor"] == "true")
					{
			?>
						Supervisor = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Aprobar" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Aprobar" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Aprobar" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#autorizar_imprimir").jqxButton({ disabled: true });
						$("#autorizar_exportar").jqxButton({ disabled: true });
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
	
	$("#caja_autorizar_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#caja_autorizar_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#caja_autorizar_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	$("#caja_autorizar_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#caja_autorizar_fecha_fin").on('change', function (event) 
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
	
	$("#caja_autorizar_cliente").jqxComboBox(
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
	$("#caja_autorizar_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#caja_autorizar_cliente_ID").val() != event.args.item.value)
				$("#caja_autorizar_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		// else
		// {
		// 	var item_value = $("#caja_autorizar_cliente").jqxComboBox('getSelectedItem');
		// 	if (item_value)
		// 	{
		// 		$("#caja_autorizar_cliente_ID").jqxComboBox('clearSelection');
		// 		$("#caja_autorizar_cliente").jqxComboBox('clearSelection');
		// 	}
		// 	else
		// 	{
		// 		var value = $("#caja_autorizar_cliente").val();
				
		// 		var item = $("#caja_autorizar_cliente").jqxComboBox('getItems');
		// 		for (var i = 0; i < item.length; i++)
		// 		{
		// 			if (item[i].label == value)
		// 			{
		// 				$("#caja_autorizar_cliente").jqxComboBox('selectItem', item[i].value);
		// 				return;
		// 			}
		// 		}
		// 		$("#caja_autorizar_cliente_ID").jqxComboBox('clearSelection');
		// 		$("#caja_autorizar_cliente").jqxComboBox('clearSelection');
		// 	}
		// 	clearTimeout(Timer);
		// 	Timer = setTimeout(function()
		// 	{
		// 		Add_Row();
		// 	},350);
		// }
	});
	
	$("#caja_autorizar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#caja_autorizar_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#caja_autorizar_cliente").val() != event.args.item.value)
				$("#caja_autorizar_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#caja_autorizar_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#caja_autorizar_cliente_ID").jqxComboBox('clearSelection');
				$("#caja_autorizar_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#caja_autorizar_cliente_ID").val();
				var item = $("#caja_autorizar_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#caja_autorizar_cliente_ID").jqxComboBox('clearSelection');
					$("#caja_autorizar_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#caja_autorizar_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
		clearTimeout(Timer);
		Timer = setTimeout(function()
		{
			Add_Row();
		},350);
	});
	
	$("#caja_autorizar_caja_recibo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	$("#caja_autorizar_caja_recibo").bind('change', function (event) {
		Add_Row();
	});
	
	var EstadoValues = [
		//{"Estado":"Pendiente"},
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
	
	$("#caja_autorizar_vendedor").jqxComboBox({
		theme: mytheme,
		width: 260,
		height: 20,
		source: VendedorDataAdapter,
		//dropDownHeight: 100,
		promptText: 'Seleccionar Digitador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#caja_autorizar_vendedor").bind('change', function (event) {
		if (!event.args)
		{
			$("#caja_autorizar_vendedor").jqxComboBox('clearSelection');
		}
		Add_Row();
	});
	$("#caja_autorizar_vendedor").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	$("#caja_autorizar_vendedor").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#caja_autorizar_vendedor").val("<?php echo $_SESSION["UserCode"]; ?>");
			$("#caja_autorizar_vendedor").jqxComboBox({ disabled: true });
		}
	});
	
	$("#caja_autorizar_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	$("#caja_autorizar_estado").bind('change', function (event) {
		if (event.args)
		{
			Add_Row();
		}
	});
	
	$("#caja_autorizar_estado").jqxComboBox('addItem', {label: "Pendiente"});
	$("#caja_autorizar_estado").jqxComboBox('addItem', {label: "Aprobado"});
	$("#caja_autorizar_estado").jqxComboBox('addItem', {label: "Anulado"});
	
	$("#caja_autorizar_ingresos").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	$("#caja_autorizar_egresos").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	$("#caja_autorizar_consignaciones").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	$("#caja_autorizar_efectivo").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	$("#caja_autorizar_cheques").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	//---------------------------------------------------------------- PARTE 2
	
	function Add_Row()
	{
		clearTimeout(Timer);
		Timer = setTimeout(function()
		{
			//if ($("#caja_autorizar_caja_recibo").val() == "")
			//{
				var MainSource = 
				{
					datatype: "json",
					datafields:
					[
						{ name: 'Total_Ingresos', type: 'decimal' },
						{ name: 'Total_Egresos', type: 'decimal' },
						{ name: 'Recaudos_Cartera', type: 'decimal' },
						{ name: 'Recaudos_Cheques', type: 'decimal' },
						{ name: 'Retenciones', type: 'decimal' },
						{ name: 'Descuentos', type: 'decimal' },
						{ name: 'Total_Efectivo', type: 'decimal' },
						{ name: 'Total_Cheques', type: 'decimal' },
						{ name: 'Saldo_Caja', type: 'decimal' },
						{ name: 'Total_Tarjetas', type: 'decimal' },
					],
					data: {
						"Caja_Cierre_Datos":true,
						"Caja_Recibo":$("#caja_autorizar_caja_recibo").val(),
						"ClienteID":$("#caja_autorizar_cliente_ID").val(),
						"DigitadorID":$("#caja_autorizar_vendedor").val(),
						"Fecha_Ini":GetFormattedDate($("#caja_autorizar_fecha_ini").jqxDateTimeInput("getDate")),
						"Fecha_Fin":GetFormattedDate($("#caja_autorizar_fecha_fin").jqxDateTimeInput("getDate")),
						"Estado":$("#caja_autorizar_estado").val()
					},
					url: "modulos/datos.php",
					async: true
				};
				var MainDataAdapter = new $.jqx.dataAdapter(MainSource,
				{
					autoBind: true,
					loadComplete: function ()
					{
						var myrecords = MainDataAdapter.records;
						// Add Data
						if (myrecords[0]["Total_Ingresos"] != "" || myrecords[0]["Total_Egresos"] != "")
						{
							$("#caja_autorizar_ingresos").val(myrecords[0]["Total_Ingresos"]);
							$("#caja_autorizar_egresos").val(myrecords[0]["Total_Egresos"]);
							$("#caja_autorizar_consignaciones").val(myrecords[0]["Total_Tarjetas"]);
							$("#caja_autorizar_efectivo").val(myrecords[0]["Total_Efectivo"]);
							$("#caja_autorizar_cheques").val(myrecords[0]["Total_Cheques"]);
						}// Clean
						else
						{
							$("#caja_autorizar_ingresos").val("0");
							$("#caja_autorizar_egresos").val("0");
							$("#caja_autorizar_consignaciones").val("0");
							$("#caja_autorizar_efectivo").val("0");
							$("#caja_autorizar_cheques").val("0");
						}
					},
					loadError: function(jqXHR, status, error) {
						alert("Request failed: \n" + error);
					},
				});
			//}
			//else
			//{
			//	$("#caja_autorizar_ingresos").val("0");
			//	$("#caja_autorizar_egresos").val("0");
			//	$("#caja_autorizar_consignaciones").val("0");
			//	$("#caja_autorizar_efectivo").val("0");
			//	$("#caja_autorizar_cheques").val("0");
			//}
			
			GridSource.data = {
				"Caja_Aprobar":true,
				"Caja_Recibo":$("#caja_autorizar_caja_recibo").val(),
				"ClienteID":$("#caja_autorizar_cliente_ID").val(),
				"DigitadorID":$("#caja_autorizar_vendedor").val(),
				"Estado":$("#caja_autorizar_estado").val(),
				"Fecha_Ini":GetFormattedDate($('#caja_autorizar_fecha_ini').jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($('#caja_autorizar_fecha_fin').jqxDateTimeInput('getDate'))
			};
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#autorizar_items_grid").jqxGrid({source: GridDataAdapter});
		},350);
		/*var GridSource = 
		{
			datatype: "json",
			datafields:
			[
				{ name: 'Estado', type: 'string' },
				{ name: 'Fecha', type: 'string' },
				{ name: 'CajaInterno', type: 'string' },
				{ name: 'ReciboCaja', type: 'string' },
				{ name: 'Dprov', type: 'string' },
				{ name: 'Total', type: 'decimal' },
				{ name: 'DigitadorID', type: 'string' },
				{ name: 'Tipo', type: 'string' },
				{ name: 'Cliente', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Email', type: 'string' },
				{ name: 'Enviado', type: 'bool' },
				{ name: 'Efectivo', type: 'decimal' },
				{ name: 'Cheque', type: 'decimal' },
				{ name: 'Consignacion', type: 'decimal' },
				{ name: 'Saldo', type: 'decimal' },
				{ name: 'AprobadorID', type: 'string' },
			],
			type: 'GET',
			data: {
				"Caja_Aprobar":true,
				"ReciboCaja":$("#caja_autorizar_caja_recibo").val(),
				"ClienteID":$("#caja_autorizar_cliente_ID").val(),
				"DigitadorID":$("#caja_autorizar_vendedor").val(),
				"Estado":$("#caja_autorizar_estado").val(),
				"Fecha_Ini":GetFormattedDate($('#caja_autorizar_fecha_ini').jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($('#caja_autorizar_fecha_fin').jqxDateTimeInput('getDate'))
			},
			url: "modulos/datos.php",
			async: true,
		};
		var GridDataAdapter = new $.jqx.dataAdapter(GridSource,{
			autoBind: true,
			loadComplete: function () {
				var myrecords = GridDataAdapter.records;
				var len = myrecords.length;
				$("#autorizar_items_grid").jqxGrid('clear');
				for (var i = 0; i < len; i++)
				{
					if (myrecords[i]["Estado"] != "")
					{
						var datarow = [{
							"Aprobar":false,
							"Estado":myrecords[i]["Estado"],
							"Fecha":new Date(SetFormattedDate(myrecords[i]["Fecha"])),
							"CajaInterno":myrecords[i]["CajaInterno"],
							"ReciboCaja":myrecords[i]["ReciboCaja"],
							"Dprov":myrecords[i]["Dprov"],
							"Total":myrecords[i]["Total"],
							"DigitadorID":myrecords[i]["DigitadorID"],
							"Tipo":myrecords[i]["Tipo"],
							"Cliente":myrecords[i]["Cliente"],
							"ClienteID":myrecords[i]["ClienteID"],
							"Email":myrecords[i]["Email"],
							"Enviado":myrecords[i]["Enviado"] ? "Enviado":"No Enviado",
							"Efectivo":myrecords[i]["Efectivo"],
							"Cheque":myrecords[i]["Cheque"],
							"Consignacion":myrecords[i]["Consignacion"],
							"Saldo":myrecords[i]["Saldo"],
							"AprobadorID":myrecords[i]["AprobadorID"],
						}];
						$("#autorizar_items_grid").jqxGrid("addrow", null, datarow, "first");
					}
				}
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});*/
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Aprobar', type: 'bool' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Caja_Interno', type: 'string' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'Dprov', type: 'string' },
			{ name: 'Total', type: 'decimal' },
			{ name: 'DigitadorID', type: 'string' },
			{ name: 'Tipo', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter1.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Email', type: 'string' },
			{ name: 'Enviado', type: 'bool' },
			{ name: 'Efectivo', type: 'decimal' },
			{ name: 'Cheque', type: 'decimal' },
			{ name: 'Consignacion', type: 'decimal' },
			{ name: 'Saldo', type: 'decimal' },
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
			
			else if (OldState == "Anulado")
			{
				Alerts_Box("Un Movimiento Anulado, no es posible Cambiar su estado.", 3);
				commit(false);
			}
			else if (NewState == "Anulado")
			{
				Alerts_Box("Una vez Anulado, no se podra revertir el proceso! Desea Continuar?", 4, true);
				var OldState2 = OldState;
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						//var data = "Caja_Aprobar=true&Estado=" + rowdata.Estado;
						//data = data + "&ClienteID=" + rowdata.ClienteID + "&CajaInterno=" + rowdata.CajaInterno + "&ReciboCaja=" + rowdata.ReciboCaja;
						$.ajax({
							dataType: "json",
							data: {
								"Caja_Aprobar":true,
								"Old_Estado":OldState2,
								"New_Estado":rowdata.Estado,
								"ClienteID":rowdata.ClienteID,
								"Caja_Interno":rowdata.Caja_Interno,
								"Caja_Recibo":rowdata.Caja_Recibo,
							},
							url: "modulos/guardar.php",
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
				//var data = "Caja_Aprobar=true&Estado=" + rowdata.Estado;
				//data = data + "&ClienteID=" + rowdata.ClienteID + "&CajaInterno=" + rowdata.CajaInterno + "&ReciboCaja=" + rowdata.ReciboCaja;
				$.ajax({
					dataType: "json",
					data: {
						"Caja_Aprobar":true,
						"Old_Estado":OldState,
						"New_Estado":rowdata.Estado,
						"ClienteID":rowdata.ClienteID,
						"Caja_Interno":rowdata.Caja_Interno,
						"Caja_Recibo":rowdata.Caja_Recibo,
					},
					url: "modulos/guardar.php",
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
	
	$("#autorizar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
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
				text: '',
				datafield: 'Aprobar',
				columntype: 'checkbox',
				width: 15,
				pinned: true,
				editable: Admin ? true:Guardar,
				filterable: false,
			},
			{
				text: 'Estado',
				datafield: 'Estado',
				width: 75,
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
				columntype: 'datetimeinput',
				width: 90,
				height: 20,
				pinned: true,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Mov. Caja', datafield: 'Caja_Interno', editable: false, width: 80, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> R. Caja', datafield: 'Caja_Recibo', editable: false, width: 80, height: 20, pinned: true, },
			{ text: 'Dprov', datafield: 'Dprov', editable: false, width: 80, height: 20 },
			{
				text: 'Valor Total',
				datafield: 'Total',
				width: 120,
				height: 20,
				pinned: true,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 40, height: 20, pinned: true, },
			{ text: 'Tipo', datafield: 'Tipo', editable: false, width: 70, height: 20 },
			{ text: 'Tercero', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: 170, height: 20, pinned: true, },
			{ text: 'Tercero ID', datafield: 'ClienteID', editable: false, width: 100, height: 20 },
			{ text: 'Enviado', datafield: 'Enviado', editable: false, width: 60, height: 20 },
			{
				text: 'Efectivo',
				datafield: 'Efectivo',
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
				text: 'Cheques',
				datafield: 'Cheque',
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
				text: 'Transacciones',
				datafield: 'Consignacion',
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
			{ text: 'Aut.', datafield: 'AprobadorID', editable: false, width: 40, height: 20 },
		]
	});
	$("#autorizar_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Caja_Recibo")
		{
			Interno = $("#autorizar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Caja_Interno");
			$("#Caja_Aprobar_Caja_Window").jqxWindow('open');
		}
		
	});
	$("#autorizar_items_grid").on('cellendedit', function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Aprobar")
		{
			var EstadoVal = $('#autorizar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			OldState = EstadoVal;
			switch (EstadoVal)
			{
				case "Aprobado":
					//NewState = "Aprobado";
					//$("#autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Aprobado");
				break;
				case "Anulado":
					NewState = "Pendiente";
					$("#autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Anulado");
				break;
				case "Pendiente":
					NewState = "Aprobado";
					$("#autorizar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Aprobado");
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
			NewState = "Passed";
		}
	});

	// ------------------------------------------ PARTE 3
	
	function Guardar ()
	{
		$("#caja_autorizar_estado").val("Pendiente");
		Add_Row();
	};
	
	$('#autorizar_guardar').jqxButton({
		width: 150,
		template: "info"
	});
	// Prepare Save Changes...
	$("#autorizar_guardar").bind('click', function ()
	{
		$("#autorizar_items_grid").jqxGrid("updatebounddata");
	});
	
	$("#autorizar_imprimir").jqxButton({width: 150, template: "warning"});
	$("#autorizar_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/caja_movimientos.php?Caja_Recibo="+$("#caja_autorizar_caja_recibo").val();
		data += "&ClienteID="+$("#caja_autorizar_cliente_ID").val()+"&DigitadorID="+$("#caja_autorizar_vendedor").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#caja_autorizar_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#caja_autorizar_fecha_fin").jqxDateTimeInput('getDate'))+"";
		data += "&Estado="+$("#caja_autorizar_estado").val();
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	
	$("#autorizar_exportar").jqxButton({width: 150, template: "success"});
	$("#autorizar_exportar").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Caja_Aprobar=true&Caja_Recibo="+$("#caja_autorizar_caja_recibo").val();
		data += "&ClienteID="+$("#caja_autorizar_cliente_ID").val()+"&DigitadorID="+$("#caja_autorizar_vendedor").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#caja_autorizar_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#caja_autorizar_fecha_fin").jqxDateTimeInput('getDate'))+"";
		data += "&Estado="+$("#caja_autorizar_estado").val();
		window.location = data;
	});
	
	// ------------------------------------------ WINDOWS
	//--- Caja Recibo
	$("#Caja_Aprobar_Caja_Window").jqxWindow({
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
	$("#Caja_Aprobar_Caja_Window").on('open', function (event)
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
				$("#Caja_Aprobar_Caja_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	$("#Caja_Aprobar_Caja_Window").on('close', function (event)
	{
		Interno = "";
	});
	
	// Load Default Values
	
	Add_Row();
	$("#caja_autorizar_caja_recibo").focus();
	CheckRefresh();
});
</script>
<div id="Caja_Aprobar_Caja_Window">
	<div id="Caja_Aprobar_Caja_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Recibo de CAJA</div>
	</div>
	<div id="Caja_Aprobar_Caja_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				ID Cliente
			</td>
			<td>
				<div id="caja_autorizar_cliente_ID"></div>
			</td>
			<td>
				Cliente
			</td>
			<td colspan="3">
				<div id="caja_autorizar_cliente"></div>
			</td>
			<td>
				Ingresos
			</td>
			<td>
				<div id="caja_autorizar_ingresos"></div>
			</td>
			<td>
				Egresos
			</td>
			<td>
				<div id="caja_autorizar_egresos"></div>
			</td>
		</tr>
		<tr>
			<td>
				R. Caja
			</td>
			<td>
				<input type="text" id="caja_autorizar_caja_recibo"/>
			</td>
			<td>
				Digitador
			</td>
			<td colspan="3">
				<div id="caja_autorizar_vendedor"></div>
			</td>
			<td>
				Consignaciones
			</td>
			<td>
				<div id="caja_autorizar_consignaciones"></div>
			</td>
			<td>
				Efectivo
			</td>
			<td>
				<div id="caja_autorizar_efectivo"></div>
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<div id="caja_autorizar_estado"></div>
			</td>
			<td>
				Fecha I.
			</td>
			<td>
				<div id="caja_autorizar_fecha_ini"></div>
			</td>
			<td>
				Fecha F.
			</td>
			<td>
				<div id="caja_autorizar_fecha_fin"></div>
			</td>
			<td>
				Cheques
			</td>
			<td>
				<div id="caja_autorizar_cheques"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="autorizar_items_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="5" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="autorizar_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="autorizar_imprimir" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="autorizar_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>