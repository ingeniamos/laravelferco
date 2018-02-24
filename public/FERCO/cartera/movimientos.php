<?php
session_start();
?>
<style type="text/css">
.red {
	color: black;
	background-color: rgba(251,229,214,1);
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
	var ID_Cliente = "";
	var OldState = "";
	var NewState = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Interno = "";
	var Orden = "";
	var CurrentRow = -1;
	
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Cartera_Content");
	var Body = document.getElementById("Movimientos_Content");
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
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Movimientos" && $data[$i]["Supervisor"] == "true")
					{
			?>
						Supervisor = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Movimientos" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Movimientos" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Movimientos" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#movimientos_imprimir").jqxButton({ disabled: true });
						$("#movimientos_exportar").jqxButton({ disabled: true });
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
	
	$("#movimientos_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#movimientos_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#movimientos_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	$("#movimientos_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#movimientos_fecha_fin").on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	var ClienteData = new Array();
	
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
	
	/*function Cargar_Clientes ()
	{
		ClienteSource.data = {"Clientes_Deudores":ID_Cobrador};
		ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
		$("#cartera_aplicar_cliente").jqxComboBox({source: ClienteDataAdapter});
		$("#cartera_aplicar_cliente_ID").jqxComboBox({source: ClienteDataAdapter});
	};*/
	
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ClienteData.push(records[i]);
			}
		}
	});
	
	$("#movimientos_cliente").jqxComboBox(
	{
		width: 400,
		height: 20,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#movimientos_cliente").bind('change', function (event) {
		if (event.args)
		{
			ID_Cliente = event.args.item.value;
			$("#movimientos_cliente_ID").val(ID_Cliente);
		}
	});
	
	$("#movimientos_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 180,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#movimientos_cliente_ID").on('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#movimientos_cliente").val(ID_Cliente);
				Add_Row();
				clearTimeout(Timer1);
			},350);
		}
	});

	// $("#movimientos_cliente_ID").on('unselect', function (event) {
	// 	if (event.args)
	// 	{
	// 		clearTimeout(Timer1);
	// 		Timer1 = setTimeout(function()
	// 		{
	// 			Add_Row();
	// 			clearTimeout(Timer1);
	// 		},350);
	// 	}
	// });
	
	$("#movimientos_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
	});
	$("#movimientos_factura").on('change', function () {
		Add_Row();
	});
	
	$("#movimientos_interno").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
	});
	$("#movimientos_interno").on('change', function () {
		Add_Row();
	});
	
	$("#movimientos_recibo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
	});
	$("#movimientos_recibo").on('change', function () {
		Add_Row();
	});
	
	$("#movimientos_deuda").jqxNumberInput({
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

	$("#movimientos_cobrador").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 155,
		source: VendedorDataAdapter,
		//dropDownHeight: 100,
		promptText: 'Seleccionar Cobrador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#movimientos_cobrador").bind('change', function (event) {
		if (!event.args)
		{
			$("#movimientos_cobrador").jqxComboBox('clearSelection');
		}
		Add_Row();
	});
	$("#movimientos_cobrador").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#movimientos_cobrador").val("<?php echo $_SESSION["UserCode"]; ?>");
			$("#movimientos_cobrador").jqxComboBox({ disabled: true });
		}
	});
	
	$("#movimientos_tipo_mov").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		dropDownHeight: 130,
	});
	$("#movimientos_tipo_mov").bind('change', function (event) {
		if (!event.args)
		{
			$("#movimientos_tipo_mov").jqxComboBox('clearSelection');
		}
		Add_Row();
	});
	
	$("#movimientos_tipo_mov").jqxComboBox('addItem', {label: "Debito"});
	$("#movimientos_tipo_mov").jqxComboBox('addItem', {label: "Credito"});
	
	$("#movimientos_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	$("#movimientos_estado").bind('change', function (event) {
		if (event.args)
		{
			Add_Row();
		}
	});
	
	$("#movimientos_estado").jqxComboBox('addItem', {label: "Aprobado"});
	$("#movimientos_estado").jqxComboBox('addItem', {label: "Anulado"});
	$("#movimientos_estado").jqxComboBox('addItem', {label: "Pendiente"});
	
	//---------------------------------------------------------------- PARTE 2
	
	function Add_Row()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			clearTimeout(Timer2);
			
			GridSource.data = {
				"Cartera_Movimientos":true,
				"ClienteID":$("#movimientos_cliente_ID").val(),
				"TipoMovimiento":$("#movimientos_tipo_mov").val(),
				"Fecha_Ini":GetFormattedDate($('#movimientos_fecha_ini').jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($('#movimientos_fecha_fin').jqxDateTimeInput('getDate')),
				"CobradorID":$("#movimientos_cobrador").val(),
				"Estado":$("#movimientos_estado").val(),
				"Factura":$("#movimientos_factura").val(),
				"Interno":$("#movimientos_interno").val(),
				"Caja_Recibo":$("#movimientos_recibo").val()
			};
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#movimientos_items_grid").jqxGrid({source: GridDataAdapter});
			
			$.ajax({
				dataType: 'text',
				url: "modulos/datos.php",
				data: {
					"Cartera_Movimientos_Deuda":true,
					"ClienteID":$("#movimientos_cliente_ID").val(),
				},
				async: true,
				success: function (data) {
					if (data != "")
						$("#movimientos_deuda").val(data);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					Alerts_Box("Ocurrio un Error al intentar returar los datos!<br />Intente luego de unos segundos...", 3);
				}
			});
		},350);
	};
	
	var cellclass = function (row, columnfield, value)
	{
		var Motivo = $("#movimientos_items_grid").jqxGrid("getcellvalue", row, "Motivo_Anulado");
		if (Motivo != "")
			return 'red';
		else
			return '';
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ID', type: 'string' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Motivo_Anulado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Ord_Compra', type: 'string' },
			{ name: 'Remision', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'Caja_Interno', type: 'string' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'TipoMovimiento', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Saldo', type: 'decimal' },
			//{ name: 'Nombre', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'CobradorID', type: 'string' },
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
		
			if ( OldState == "Anulado")
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
						//var data = "Cartera_Movimientos=true&Estado=" + rowdata.Estado + "&Motivo_Anulado=" + rowdata.Motivo_Anulado;
						//data = data + "&CobradorID=" + rowdata.CobradorID + "&Ord_Compra=" + rowdata.Ord_Compra;
						//data = data + "&Interno=" + rowdata.Interno + "&ClienteID=" + rowdata.ClienteID + "&TipoMovimiento=" + rowdata.TipoMovimiento;
						$.ajax({
							dataType: 'text',
							data: {
								"Cartera_Movimientos":true,
								"Estado":rowdata.Estado,
								"Motivo_Anulado":rowdata.Motivo_Anulado,
								"CobradorID":rowdata.CobradorID,
								"Ord_Compra":rowdata.Ord_Compra,
								"Interno":rowdata.Interno,
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
						"Cartera_Movimientos":true,
						"Estado":rowdata.Estado,
						"Motivo_Anulado":rowdata.Motivo_Anulado,
						"CobradorID":rowdata.CobradorID,
						"Ord_Compra":rowdata.Ord_Compra,
						"Interno":rowdata.Interno,
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
	
	$("#movimientos_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		autoheight: true,
		showfilterrow: true,
		filterable: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: 'Estado',
				datafield: 'Estado',
				width: 65,
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
			{ text: 'Nota', datafield: 'Motivo_Anulado', width: 50, height: 20, editable: false, cellclassname: cellclass},
			{
				text: 'Fecha',
				datafield: 'Fecha',
				editable: false,
				width: 90,
				height: 20,
				pinned: true,
				cellsformat: 'dd-MMM-yyyy',
				filtertype: 'date',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Interno', datafield: 'Interno', editable: false, width: 100, height: 20 },
			{ text: 'Remision', datafield: 'Remision', editable: false, width: 100, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Factura', datafield: 'Factura', editable: false, width: 100, height: 20, pinned: true, },
			{ text: '', datafield: 'Caja_Interno', editable: false, width: 100, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>R. Caja', datafield: 'Caja_Recibo', editable: false, width: 100, height: 20, pinned: true, },
			{ text: 'T. Mov', datafield: 'TipoMovimiento', editable: false, width: 50, height: 20 },
			{
				text: 'Nombre',
				datafield: 'Cliente',
				displayfield: 'Nombre',
				editable: false,
				width: 150,
				height: 20,
				pinned: true,
				columntype: 'combobox',
				createeditor: function (row, value, editor) {
					editor.jqxComboBox({ source: ClienteData, displayMember: 'Nombre', valueMember: 'ClienteID' });
				}
			},
			{
				text: 'Valor',
				datafield: 'Valor',
				width: 110,
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
				width: 110,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			//{ text: 'Nombre', datafield: 'Nombre', editable: false, width: 150, height: 20 },
			{ text: 'ID', datafield: 'ClienteID', editable: false, width: 90, height: 20 },
			{ text: 'O. Comp.', datafield: 'Ord_Compra', editable: false, width: 100, height: 20 },
			{
				text: 'Cobr.',
				datafield: 'CobradorID',
				columntype: 'combobox',
				width: 50,
				height: 20,
				editable: Admin ? true:Modificar,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: VendedorDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Codigo',
						valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 40, height: 20 },
			{ text: 'Aut.', datafield: 'AutorizadorID', editable: false, width: 40, height: 20 },
			{ text: '', datafield: 'ID', editable: false, width: 0 },
		]
	});
	$("#movimientos_items_grid").jqxGrid('hidecolumn', 'ID');
	$("#movimientos_items_grid").jqxGrid('hidecolumn', 'Caja_Interno');
	$("#movimientos_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#movimientos_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Factura")
		{
			Interno = $("#movimientos_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Interno");
			$("#Cartera_Movimientos_Remision_Window").jqxWindow('open');
		}
		else if (datafield == "Caja_Recibo")
		{
			Interno = $("#movimientos_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Caja_Interno");
			$("#Cartera_Movimientos_Caja_Window").jqxWindow('open');
		}
		else if (datafield == "Motivo_Anulado")
		{
			if (Admin == false && Guardar == false)
				return;
			
			CurrentRow = rowBoundIndex;
			var offset = $("#movimientos_items_grid").offset();
			var bottom = offset.top + ($("#movimientos_items_grid").height() / 2 ) - 80;// 80 = tamaño aprox del popup/2
			$("#Cartera_Movimientos_Notas").jqxWindow({
				position: {
					x: parseInt(offset.left) + 400,
					y: parseInt(bottom) 
				}
			});
			var dataRecord = $("#movimientos_items_grid").jqxGrid("getrowdata", CurrentRow);
			$("#movimientos_notas").val(dataRecord.Motivo_Anulado);
			$("#Cartera_Movimientos_Notas").jqxWindow("open");
		}
	});
	$("#movimientos_items_grid").on('cellendedit', function (event) 
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
	
	/*$("#movimientos_items_grid").on("bindingcomplete", function (event)
	{
		if (ID_Cliente != "")
		{
			var total = 0;
			var datinfo = $("#movimientos_items_grid").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			for (i = 0; i < count; i++)
			{
				var currentRow = $("#movimientos_items_grid").jqxGrid('getrowdata', i);
				if (currentRow.TipoMovimiento == "Debito")
				{
					total = total + (currentRow.Valor - currentRow.Saldo);
					$("#movimientos_deuda").val(total);
				}
			}
			$("#movimientos_deuda").val(total);
		}
	});*/
	// ------------------------------------------ PARTE 3
	
	function Guardar ()
	{
		$("#movimientos_estado").val("Autorizado");
		Add_Row();
	};
	
	$('#movimientos_guardar').jqxButton({
		width: 150,
		template: "info"
	});
	// Prepare Save Changes...
	$("#movimientos_guardar").bind('click', function ()
	{
		$("#movimientos_items_grid").jqxGrid("updatebounddata");
	});
	
	$("#movimientos_imprimir").jqxButton({width: 150, template: "warning"});
	$("#movimientos_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/cartera_movimientos.php?ClienteID="+$("#movimientos_cliente_ID").val();
		data += "&TipoMovimiento="+$("#movimientos_tipo_mov").val();
		data += "&Fecha_Ini="+GetFormattedDate($('#movimientos_fecha_ini').jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($('#movimientos_fecha_fin').jqxDateTimeInput('getDate'));
		data += "&CobradorID="+$("#movimientos_cobrador").val()+"&Estado="+$("#movimientos_estado").val();
		data += "&Interno="+$("#movimientos_interno").val()+"&Factura="+$("#movimientos_factura").val();
		data += "&Caja_Recibo="+$("#movimientos_recibo").val();
		//
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	
	$("#movimientos_exportar").jqxButton({width: 150, template: "success"});
	$("#movimientos_exportar").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Cartera_Movimientos=true&ClienteID="+$("#movimientos_cliente_ID").val();
		data += "&TipoMovimiento="+$("#movimientos_tipo_mov").val();
		data += "&Fecha_Ini="+GetFormattedDate($('#movimientos_fecha_ini').jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($('#movimientos_fecha_fin').jqxDateTimeInput('getDate'));
		data += "&CobradorID="+$("#movimientos_cobrador").val()+"&Estado="+$("#movimientos_estado").val();
		data += "&Interno="+$("#movimientos_interno").val()+"&Factura="+$("#movimientos_factura").val();
		data += "&Caja_Recibo="+$("#movimientos_recibo").val();
		//
		window.location = data;
	});
	
	// ------------------------------------------ WINDOWS
	//--- Notas del Grid
	$("#Cartera_Movimientos_Notas").jqxWindow({
		theme: mytheme,
		width: 250,
		resizable: false,
		isModal: true,
		autoOpen: false,
		//okButton: $("#movimientos_notas_guardar"),
		cancelButton: $("#movimientos_notas_cancelar"),
		modalOpacity: 0.01           
	});
	$("#Cartera_Movimientos_Notas").on("open", function()
	{
		//
	});
	
	$("#movimientos_notas_cancelar").jqxButton({ theme: mytheme });
	$("#movimientos_notas_guardar").jqxButton({ theme: mytheme });
	$("#movimientos_notas_guardar").click(function()
	{
		if (CurrentRow >= 0)
		{
			NewState = "Passed";
			var dataRecord = $("#movimientos_items_grid").jqxGrid('getrowdata', CurrentRow);
			
			var row = {
				"ID":dataRecord.ID,
				"Estado":dataRecord.Estado,
				"Motivo_Anulado":$("#movimientos_notas").val(),
				"Fecha":dataRecord.Fecha,
				"Interno":dataRecord.Interno,
				"Ord_Compra":dataRecord.Ord_Compra,
				"Remision":dataRecord.Remision,
				"Factura":dataRecord.Factura,
				"Caja_Interno":dataRecord.Caja_Interno,
				"Caja_Recibo":dataRecord.Caja_Recibo,
				"Caja_Recibo":dataRecord.Caja_Recibo,
				"TipoMovimiento":dataRecord.TipoMovimiento,
				"Valor":dataRecord.Valor,
				"Saldo":dataRecord.Saldo,
				"Nombre":dataRecord.Nombre,
				"ClienteID":dataRecord.ClienteID,
				"CobradorID":dataRecord.CobradorID,
				"DigitadorID":dataRecord.DigitadorID,
				"AutorizadorID":dataRecord.AutorizadorID
			};
			
			var rowID = $("#movimientos_items_grid").jqxGrid("getrowid", CurrentRow);
			$("#movimientos_items_grid").jqxGrid("updaterow", rowID, row);
			$("#Cartera_Movimientos_Notas").jqxWindow("hide");
		}
	});
	
	//--- Remision
	$("#Cartera_Movimientos_Remision_Window").jqxWindow({
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
	$("#Cartera_Movimientos_Remision_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Interno":Interno,
			},
			url: "ventas/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Cartera_Movimientos_Remision_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	//--- Caja Recibo
	$("#Cartera_Movimientos_Caja_Window").jqxWindow({
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
	$("#Cartera_Movimientos_Caja_Window").on('open', function (event)
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
				$("#Cartera_Movimientos_Caja_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	// Load Default Values
	Add_Row();
	$("#movimientos_cliente").focus();
	//Cargar_Clientes();
	CheckRefresh();
});
</script>
<div id="Cartera_Movimientos_Notas">
	<div>Notas:</div>
	<div style="overflow: hidden;">
		<table>
			<tr>
				<td>
					<textarea rows="5" cols="30" id="movimientos_notas" maxlength="200" style="resize:none;"></textarea>
				</td>
			</tr>
			<tr>
				<td style="padding-top: 10px;">
					<input style="margin-right: 5px;" type="button" id="movimientos_notas_guardar" value="Guardar" />
					<input id="movimientos_notas_cancelar" type="button" value="Cancelar" />
				</td>
			</tr>
		</table>
	</div>
</div>
<div id="Cartera_Movimientos_Remision_Window">
	<div id="Cartera_Movimientos_Remision_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Cartera_Movimientos_Remision_Content" class="WindowContainer">
	</div>
</div>
<div id="Cartera_Movimientos_Caja_Window">
	<div id="Cartera_Movimientos_Caja_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Recibo de CAJA</div>
	</div>
	<div id="Cartera_Movimientos_Caja_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Cliente
			</td>
			<td colspan="3">
				<div id="movimientos_cliente"></div>
			</td>
			<td>
				Tipo Mov.
			</td>
			<td>
				<div id="movimientos_tipo_mov"></div>
			</td>
			<td>
				Fecha Ini.
			</td>
			<td>
				<div id="movimientos_fecha_ini"></div>
			</td>
			<td>
				Fecha Fin.
			</td>
			<td>
				<div id="movimientos_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				ID Cliente
			</td>
			<td>
				<div id="movimientos_cliente_ID"></div>
			</td>
			<td>
				Cobrador
			</td>
			<td>
				<div id="movimientos_cobrador"></div>
			</td>
			<td>
				Factura
			</td>
			<td>
				<input type="text" id="movimientos_factura"/>
			</td>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="movimientos_interno"/>
			</td>
			<td>
				R. de Caja
			</td>
			<td>
				<input type="text" id="movimientos_recibo"/>
			</td>
		</tr>
		<tr>
			<td>
				Deuda
			</td>
			<td colspan="3">
				<div id="movimientos_deuda"></div>
			</td>
			<td>
				Estado
			</td>
			<td>
				<div id="movimientos_estado"></div>
			</td>
			<td colspan="4">
				&nbsp;
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="movimientos_items_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="5" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="movimientos_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="movimientos_imprimir" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="movimientos_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>