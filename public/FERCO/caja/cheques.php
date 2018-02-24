<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Check = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Caja_Content");
	var Body = document.getElementById("ChequesRC_Content");
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
				SystemMap("Cheques", true);
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
	var Admin = false;
	var Modificar = false;
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Cheques" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Cheques" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#caja_cheques_imprimir").jqxButton({ disabled: true });
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
	
	// prepare the data
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
	
	$("#caja_cheques_cliente").jqxComboBox(
	{
		width: 350,
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
	$("#caja_cheques_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#caja_cheques_cliente_ID").val() != event.args.item.value)
				$("#caja_cheques_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		// else
		// {
		// 	var item_value = $("#caja_cheques_cliente").jqxComboBox('getSelectedItem');
		// 	if (item_value)
		// 	{
		// 		$("#caja_cheques_cliente_ID").jqxComboBox('clearSelection');
		// 		$("#caja_cheques_cliente").jqxComboBox('clearSelection');
		// 	}
		// 	else
		// 	{
		// 		var value = $("#caja_cheques_cliente").val();
				
		// 		var item = $("#caja_cheques_cliente").jqxComboBox('getItems');
		// 		for (var i = 0; i < item.length; i++)
		// 		{
		// 			if (item[i].label == value)
		// 			{
		// 				$("#caja_cheques_cliente").jqxComboBox('selectItem', item[i].value);
		// 				return;
		// 			}
		// 		}
		// 		$("#caja_cheques_cliente_ID").jqxComboBox('clearSelection');
		// 		$("#caja_cheques_cliente").jqxComboBox('clearSelection');
		// 	}
		// }
	});
	
	$("#caja_cheques_cliente_ID").jqxComboBox({
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
	$("#caja_cheques_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#caja_cheques_cliente").val() != event.args.item.value)
				$("#caja_cheques_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		// else
		// {
		// 	var item_value = $("#caja_cheques_cliente_ID").jqxComboBox('getSelectedItem');
		// 	if (item_value)
		// 	{
		// 		$("#caja_cheques_cliente_ID").jqxComboBox('clearSelection');
		// 		$("#caja_cheques_cliente").jqxComboBox('clearSelection');
		// 	}
		// 	else
		// 	{
		// 		var value = $("#caja_cheques_cliente_ID").val();
		// 		var item = $("#caja_cheques_cliente_ID").jqxComboBox('getItemByValue', value);
		// 		if (item == undefined)
		// 		{
		// 			$("#caja_cheques_cliente_ID").jqxComboBox('clearSelection');
		// 			$("#caja_cheques_cliente").jqxComboBox('clearSelection');
		// 		}
		// 		else
		// 			$("#caja_cheques_cliente_ID").jqxComboBox('selectItem', item.value);
		// 	}
		// }
		LoadValues();
	});
	
	$("#caja_cheques_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	
	$("#caja_cheques_estado").bind('close', function (event) {
		LoadValues();
	});
	
	$("#caja_cheques_estado").jqxComboBox('addItem', {label: "Al Dia"});
	$("#caja_cheques_estado").jqxComboBox('addItem', {label: "Postfechado"});
	$("#caja_cheques_estado").jqxComboBox('addItem', {label: "Pagado"});
	$("#caja_cheques_estado").jqxComboBox('addItem', {label: "Devuelto"});
	
	function LoadValues()
	{
		ChequesSource.data = {
			"Caja_Cheques":true,
			"ClienteID":$("#caja_cheques_cliente_ID").val(),
			"Estado":$("#caja_cheques_estado").val()
		};
		ChequesAdapter = new $.jqx.dataAdapter(ChequesSource);
		$("#caja_cheques_grid").jqxGrid({source: ChequesAdapter});
	};
	
	var EstadoValues = [
		{"Estado":"Al Dia"},
		{"Estado":"Pagado"},
		{"Estado":"Devuelto"},
	];
	
	var EstadoSource = {
		localdata: EstadoValues,
		datatype: "json",
		datafields:[
			{ name: 'Estado', type: 'string' }
		]
	};
	var EstadoAdapter = new $.jqx.dataAdapter(EstadoSource);
	
	var BancoSource = {
		datatype: "json",
		datafields:[
			{ name: 'Banco', type: 'string' }
		],
		url: "modulos/parametros.php",
		data: {"Caja_Banco":true},
		async: true
	};
	var BancoAdapter = new $.jqx.dataAdapter(BancoSource);
	
	var ChequesSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Caja_Interno', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'TipoEstado', type: 'bool' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter1.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'Cheque', type: 'string' },
			{ name: 'Banco_Num', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Fecha_Cheque', type: 'date' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Banco', type: 'string' },
			{ name: 'Cuenta', type: 'string' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'DigitadorID', type: 'string' },
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
			if (Check == false) { // Evitar Doble Actualizacion --- Colocar en Cartera -> Movimientos y Venta -> Aprobar
				if (rowdata.Estado == "Devuelto") {
					Alerts_Box("Esta seguro de querer Devolver este Cheque?", 4, true);
		
					var Timer = setInterval(function() {
						if (ClickOK == true) {
							clearInterval(Timer);
							ClickOK = false;
							var data = "Caja_Cheques=true&Estado=" + rowdata.Estado + "&Fecha=" + GetFormattedDate(rowdata.Fecha_Cheque);
							data = data + "&Banco=" + rowdata.Banco + "&ClienteID=" + rowdata.ClienteID + "&Caja_Interno=" + rowdata.Caja_Interno;
							data = data + "&Caja_Recibo=" + rowdata.Caja_Recibo + "&Valor=" + rowdata.Valor + "&Cheque=" + rowdata.Cheque;
							data = data + "&Banco_Num=" + rowdata.Banco_Num;
							$.ajax({
								dataType: 'text',
								url: "modulos/guardar.php",
								data: data,
								async: true,
								success: function (data, status, xhr) {
									//alert("devuelto -> "+data);
									commit(true);
								},
								error: function (jqXHR, textStatus, errorThrown) {
									alert(textStatus+ " - " +errorThrown);
									commit(false);
								}
							});
						}
						if (ClickCANCEL == true) {
							clearInterval(Timer);
							ClickCANCEL = false;
							commit(false);
						}
					}, 10);
				}
				else
				{
					var data = "Caja_Cheques=true&Estado=" + rowdata.Estado + "&Fecha=" + GetFormattedDate(rowdata.Fecha_Cheque);
					data = data + "&Banco=" + rowdata.Banco + "&ClienteID=" + rowdata.ClienteID + "&Caja_Interno=" + rowdata.Caja_Interno;
					data = data + "&Caja_Recibo=" + rowdata.Caja_Recibo + "&Valor=" + rowdata.Valor + "&Cheque=" + rowdata.Cheque;
					data = data + "&Banco_Num=" + rowdata.Banco_Num;
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: data,
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
			}
			else
			{
				Check = false;
				commit(true);
			}
		}
	};
	
	$("#caja_cheques_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		//source: ChequesAdapter,
		enabletooltips: true,
		showfilterrow: true,
		filterable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'Caja_Interno', width: 0, height: 0 },
			{ text: '', datafield: 'ClienteID', width: 0, height: 0 },
			{
				text: '',
				datafield: 'TipoEstado',
				columntype: 'checkbox',
				width: '1%',
				pinned: true,
				editable: Admin ? true:Modificar,
				filterable: false,
			},
			{
				text: 'Estado',
				datafield: 'Estado',
				width: '8%',
				height: 20,
				pinned: true,
				editable: Admin ? true:Modificar,
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
			{ text: 'Titular', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: '18%', height: 20, pinned: true, },
			{ text: '# Cheque', datafield: 'Cheque', editable: false, width: '10%', height: 20, pinned: true, },
			{ text: 'Banco', datafield: 'Banco_Num', editable: false, width: '5%', height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				width: '15%',
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
				text: 'Fecha Cheque',
				datafield: 'Fecha_Cheque',
				columntype: 'datetimeinput',
				width: '10%',
				height: 20,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{
				text: 'Banco Destino',
				datafield: 'Banco',
				width: '12%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: BancoAdapter,
						dropDownHeight: 125,
						dropDownWidth: 100,
						selectedIndex: -1,
						promptText: 'Seleccionar Banco',
						displayMember: 'Banco',
						valueMember: 'Banco',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
				
			},
			{ text: '# Cuenta', datafield: 'Cuenta', editable: false, width: '10%', height: 20 },
			{ text: 'Recibo Caja', datafield: 'Caja_Recibo', editable: false, width: '10%', height: 20 },
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: '10%',
				height: 20,
				cellsformat: 'dd-MMM-yyyy',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: '4%', height: 20 },
		]
	});
	$("#caja_cheques_grid").jqxGrid('localizestrings', localizationobj);
	$('#caja_cheques_grid').jqxGrid('hidecolumn', 'Caja_Interno');
	$('#caja_cheques_grid').jqxGrid('hidecolumn', 'ClienteID');

	$("#caja_cheques_grid").on('cellbeginedit', function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.newvalue;
		var oldvalue = args.oldvalue;
		
		if (datafield == "TipoEstado") {
			Check = true;
		}
	});
	$("#caja_cheques_grid").on('cellvaluechanged', function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.newvalue;
		var oldvalue = args.oldvalue;
		
		if (datafield == "TipoEstado")
		{
			var EstadoVal = $('#caja_cheques_grid').jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			switch (EstadoVal)
			{
				case "Al Dia":
					$("#caja_cheques_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Pagado");
				break;
				case "Pagado":
					$("#caja_cheques_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Al Dia");
				break;
			}
		}
	});
	
	$('#caja_cheques_guardar').jqxButton({
		width: 150,
		template: "info"
	});
	// Prepare Save Changes...
	$("#caja_cheques_guardar").bind('click', function ()
	{
		$("#caja_cheques_grid").jqxGrid("updatebounddata");
	});
	
	$("#caja_cheques_imprimir").jqxButton({width: 150, template: "warning"});
	$("#caja_cheques_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/caja_cheques.php?ClienteID="+$("#caja_cheques_cliente_ID").val();
		data += "&Estado="+$("#caja_cheques_estado").val();
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	
	$("#autorizar_exportar").jqxButton({width: 150, template: "success"});
	$("#autorizar_exportar").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Caja_cheques=true&Estado="+$("#caja_cheques_estado").val();
		data += "&ClienteID="+$("#caja_cheques_cliente_ID").val();
		window.location = data;
	});

	LoadValues();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:10px;">
		<tr>
			<td>
				Tercero
			</td>
			<td>
				<div id="caja_cheques_cliente"></div>
			</td>
			<td>
				ID Tercero
			</td>
			<td>
				<div id="caja_cheques_cliente_ID"></div>
			</td>
			<td>
				Estado
			</td>
			<td>
				<div id="caja_cheques_estado"></div>
			</td>
		</tr>
	</table>
	<div id="caja_cheques_grid" style="margin:5px 0px 15px 0px;"></div>
	<table cellpadding="0" cellspacing="0" style="margin:0px 0px 15px 0px;">
		<tr>
			<td>
				<input type="button" id="caja_cheques_guardar" value="Actualizar"/>
			</td>
			<td style="padding-left:10px">
				<input type="button" id="caja_cheques_imprimir" value="Imprimir"/>
			</td>
			<td style="padding-left:10px">
				<input type="button" id="autorizar_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>