<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";

	//---- GLOBAL VARIABLES
	var OldState = "";
	var NewState = "";
	var Interno = "";
	var Timer = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Produccion_Content");
	var Body = document.getElementById("Produccion_Aprobar_Content");
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
					if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Aprobar" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Aprobar" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Aprobar" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#produccion_aprobar_imprimir").jqxButton({ disabled: true });
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
	
	var EstadoValues = [
		{"Estado":"Pendiente"},
		{"Estado":"Aprobado"},
		{"Estado":"Anulado"},
	];
	
	var EstadoValues2 = [
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
	var EstadoDataAdapter = new $.jqx.dataAdapter(EstadoSource);
	
	$("#produccion_aprobar_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		source: EstadoDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar',
		selectedIndex: 0,
		displayMember: 'Estado',
		valueMember: 'Estado',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#produccion_aprobar_estado").bind('change', function (event) {
		if (!event.args)
		{
			$("#produccion_aprobar_estado").jqxComboBox('selectItem', "Pendiente");
		}
		LoadValues();
	});
	
	$("#produccion_aprobar_ord_produccion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
		//placeHolder: 'Orden Produccion',
	});
	$("#produccion_aprobar_ord_produccion").on('change', function (event) {
		LoadValues();
	});
	
	$("#produccion_aprobar_solicitud").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	$("#produccion_aprobar_solicitud").on('change', function (event)
	{
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
	var ClienteDataAdapter1 = new $.jqx.dataAdapter(ClienteSource,{ autoBind: true });
	
	$("#produccion_aprobar_cliente").jqxComboBox(
	{
		width: 323,
		height: 20,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#produccion_aprobar_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#produccion_aprobar_cliente_ID").val() != event.args.item.value)
				$("#produccion_aprobar_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#produccion_aprobar_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#produccion_aprobar_cliente_ID").jqxComboBox('clearSelection');
				$("#produccion_aprobar_cliente").jqxComboBox('clearSelection');
				LoadValues();
			}
			else
			{
				var value = $("#produccion_aprobar_cliente").val();
				
				var item = $("#produccion_aprobar_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#produccion_aprobar_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#produccion_aprobar_cliente_ID").jqxComboBox('clearSelection');
				$("#produccion_aprobar_cliente").jqxComboBox('clearSelection');
				LoadValues();
			}
		}
	});
	
	$("#produccion_aprobar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#produccion_aprobar_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#produccion_aprobar_cliente").val() != event.args.item.value)
				$("#produccion_aprobar_cliente").jqxComboBox('selectItem', event.args.item.value);
			LoadValues();
		}
		else
		{
			var item_value = $("#produccion_aprobar_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#produccion_aprobar_cliente_ID").jqxComboBox('clearSelection');
				$("#produccion_aprobar_cliente").jqxComboBox('clearSelection');
				LoadValues();
			}
			else
			{
				var value = $("#produccion_aprobar_cliente_ID").val();
				var item = $("#produccion_aprobar_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#produccion_aprobar_cliente_ID").jqxComboBox('clearSelection');
					$("#produccion_aprobar_cliente").jqxComboBox('clearSelection');
					LoadValues();
				}
				else
					$("#produccion_aprobar_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#produccion_aprobar_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 145,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$("#produccion_aprobar_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$('#produccion_aprobar_fecha_ini').on('change', function (event) 
	{  
		if(event.args)
			LoadValues();
	});
	
	$("#produccion_aprobar_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 145,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$('#produccion_aprobar_fecha_fin').on('change', function (event) 
	{  
		if(event.args)
			LoadValues();
	});
	
	// ------------------------------------------ PARTE 2
	
	function LoadValues()
	{
		clearTimeout(Timer);
		Timer = setTimeout(function()
		{
			ItemsSource.data = {
				"Produccion_Aprobar":true,
				"Ord_Produccion":$("#produccion_aprobar_ord_produccion").val(),
				"Solicitud":$("#produccion_aprobar_solicitud").val(),
				"ClienteID":$("#produccion_aprobar_cliente_ID").val(),
				"Estado":$("#produccion_aprobar_estado").val(),
				"Fecha_Ini":GetFormattedDate($("#produccion_aprobar_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#produccion_aprobar_fecha_fin").jqxDateTimeInput('getDate'))
			};
			ItemsDataAdapter = new $.jqx.dataAdapter(ItemsSource);
			$("#produccion_aprobar_items_grid").jqxGrid({source: ItemsDataAdapter});
		},500);
	};
	
	var ItemsSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Solicitud', type: 'string' },
			{ name: 'Aprobar', type: 'bool' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Ord_Produccion', type: 'string' },
			{ name: 'Ord_Compra', type: 'string' },
			{ name: 'Destino', type: 'string' },
			{ name: 'Nombre', value: 'Cliente', values: { source: ClienteDataAdapter1.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'Cliente', type: 'string' },
			{ name: 'Trefilado', type: 'bool' },
			{ name: 'Enderezado', type: 'bool' },
			{ name: 'Soldado', type: 'bool' },
			{ name: 'Figurado', type: 'bool' },
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
	
				var CheckTimer = setInterval(function(OldState)
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						//var data = "Produccion_Aprobar=true&Estado=" + NewState + "&Fecha=" + GetFormattedDate(rowdata.Fecha);
						//data = data + "&Ord_Produccion=" + rowdata.Ord_Produccion + "&Ord_Compra=" + rowdata.Ord_Compra + "&ClienteID=" + rowdata.ClienteID;
						//data = data + "&Interno=" + rowdata.Interno;
						$.ajax({
							dataType: "json",
							url: "modulos/guardar.php",
							data: {
								"Produccion_Aprobar":true,
								"Old_Estado":OldState,
								"New_Estado":rowdata.Estado,
								"Fecha":GetFormattedDate(rowdata.Fecha),
								"Ord_Produccion":rowdata.Ord_Produccion,
								"Interno":rowdata.Interno,
								"Ord_Compra":rowdata.Ord_Compra,
								"ClienteID":rowdata.ClienteID,
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
									
									case "INCOMPLETO":
										commit(false);
										Alerts_Box("Esta Orden de Produccion esta Incompleta!<br />Favor completar la orden desde la pestaña"
										+" \"Modificar\" para continuar el proceso.", 4);
									break;
									
									case "ERROR":
										commit(false);
										Alerts_Box("No se han encontrado Movimientos en esta Orden.<br />Favor modificar la orden desde la pestaña"
										+" \"Modificar\" para continuar el proceso.", 4);
									break;
									
									default:
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
				//var data = "Produccion_Aprobar=true&Estado=" + NewState + "&Fecha=" + GetFormattedDate(rowdata.Fecha);
				//data = data + "&Ord_Produccion=" + rowdata.Ord_Produccion + "&Ord_Compra=" + rowdata.Ord_Compra + "&ClienteID=" + rowdata.ClienteID;
				//data = data + "&Interno=" + rowdata.Interno;
				$.ajax({
					dataType: "json",
					url: "modulos/guardar.php",
					data: {
						"Produccion_Aprobar":true,
						"Old_Estado":OldState,
						"New_Estado":rowdata.Estado,
						"Fecha":GetFormattedDate(rowdata.Fecha),
						"Ord_Produccion":rowdata.Ord_Produccion,
						"Interno":rowdata.Interno,
						"Ord_Compra":rowdata.Ord_Compra,
						"ClienteID":rowdata.ClienteID,
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
							
							case "INCOMPLETO":
								commit(false);
								Alerts_Box("Esta Orden de Produccion esta Incompleta!<br />Favor completar la orden desde la pestaña"
								+" \"Modificar\" para continuar el proceso.", 4);
							break;
							
							case "ERROR":
								commit(false);
								Alerts_Box("No se han encontrado Movimientos en esta Orden.<br />Favor modificar la orden desde la pestaña"
								+" \"Modificar\" para continuar el proceso.", 4);
							break;
							
							default:
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
	
	$("#produccion_aprobar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'ClienteID', width: 0, height: 0 },
			{ text: '', datafield: 'Interno', width: 0, height: 0 },
			{
				text: '',
				datafield: 'Aprobar',
				columntype: 'checkbox',
				width: '2%',
				editable: Admin ? true:Guardar,
				filterable: false,
			},
			{
				text: 'Estado',
				datafield: 'Estado',
				width: '7%',
				height: 20,
				editable: Admin ? true:Guardar,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoValues2,
						dropDownHeight: 125,
						//dropDownWidth: 100,
						//selectedIndex: -1,
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
				columntype: 'datetimeinput',
				width: '10%',
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Ord. Prod.', datafield: 'Ord_Produccion', editable: false, width: '9%', height: 20 },
			{ text: 'Solicitud', datafield: 'Solicitud', editable: false, width: '8%', height: 20 },
			{ text: 'Pedido', datafield: 'Ord_Compra', editable: false, width: '10%', height: 20 },
			{ text: 'Destino', datafield: 'Destino', editable: false, width: '7%', height: 20 },
			{ text: 'Cliente', datafield: 'Cliente', displayfield: 'Nombre', editable: false, width: '23.5%', height: 20 },
			{
				text: 'Tref',
				datafield: 'Trefilado',
				columntype: 'checkbox',
				width: '4.5%',
				editable: false,
				filtertype: 'bool',
			},
			{
				text: 'C. y E.',
				datafield: 'Enderezado',
				columntype: 'checkbox',
				width: '4.5%',
				editable: false,
				filtertype: 'bool',
			},
			{
				text: 'Elect.',
				datafield: 'Soldado',
				columntype: 'checkbox',
				width: '4.5%',
				editable: false,
				filtertype: 'bool',
			},
			{
				text: 'Fig.',
				datafield: 'Figurado',
				columntype: 'checkbox',
				width: '4.5%',
				editable: false,
				filtertype: 'bool',
			},
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: '5%', height: 20 },
		]
	});
	$("#produccion_aprobar_items_grid").jqxGrid('hidecolumn', 'ClienteID');
	$("#produccion_aprobar_items_grid").jqxGrid('hidecolumn', 'Interno');
	$("#produccion_aprobar_items_grid").jqxGrid('localizestrings', localizationobj);
	
	$("#produccion_aprobar_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Ord_Produccion")
		{
			Interno = $("#produccion_aprobar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Ord_Produccion");
			if (Interno != "")
				$("#Produccion_Aprobar_Produccion_Window").jqxWindow('open');
		}
		
	});
	
	$("#produccion_aprobar_items_grid").on('cellendedit', function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Aprobar")
		{
			var EstadoVal = $('#produccion_aprobar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			OldState = EstadoVal;
			switch (EstadoVal)
			{
				case "Aprobado":
					//NewState = "Pendiente";
					//$("#produccion_aprobar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Pendiente");
				break;
				case "Anulado":
					NewState = "Pendiente";
					$("#produccion_aprobar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Anulado");
				break;
				case "Pendiente":
					NewState = "Aprobado";
					$("#produccion_aprobar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Aprobado");
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
		$("#produccion_aprobar_estado").val("Pendiente");
		//$("#produccion_aprobar_ord_produccion").val('');
		$("#produccion_aprobar_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
		$("#produccion_aprobar_fecha_fin").jqxDateTimeInput('setDate', new Date());
		$("#produccion_aprobar_cliente").jqxComboBox('clearSelection');
		$("#produccion_aprobar_cliente_ID").jqxComboBox('clearSelection');
		LoadValues();
	};
	
	$('#produccion_aprobar_guardar').jqxButton({
		width: 150,
		template: "info"
	});
	// Prepare Save Changes...
	$("#produccion_aprobar_guardar").bind('click', function ()
	{
		$("#produccion_aprobar_items_grid").jqxGrid("updatebounddata");
	});
	
	$("#produccion_aprobar_imprimir").jqxButton({width: 150, template: "warning"});
	$("#produccion_aprobar_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/produccion_listado.php?Ord_Produccion="+$("#produccion_aprobar_ord_produccion").val();
		data += "&ClienteID="+$("#produccion_aprobar_cliente_ID").val()+"&Solicitud="+$("#produccion_aprobar_solicitud").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#produccion_aprobar_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#produccion_aprobar_fecha_fin").jqxDateTimeInput('getDate'))+"";
		data += "&Estado="+$("#produccion_aprobar_estado").val();
		window.open(data, "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	// ------------------------------------------ WINDOWS
	//--- Produccion
	$("#Produccion_Aprobar_Produccion_Window").jqxWindow({
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
	$("#Produccion_Aprobar_Produccion_Window").on('open', function (event)
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
				$("#Produccion_Aprobar_Produccion_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	LoadValues();
	CheckRefresh();
});
</script>
<div id="Produccion_Aprobar_Produccion_Window">
	<div id="Produccion_Aprobar_Produccion_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Orden de Produccion</div>
	</div>
	<div id="Produccion_Aprobar_Produccion_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Estado
			</td>
			<td>
				<div id="produccion_aprobar_estado"></div>
			</td>
			<td>
				Ord. Produccion
			</td>
			<td>
				<input type="text" id="produccion_aprobar_ord_produccion"/>
			</td>
			<td>
				Solicitud
			</td>
			<td>
				<input type="text" id="produccion_aprobar_solicitud"/>
			</td>
			<td>
				Fecha I.
			</td>
			<td>
				<div id="produccion_aprobar_fecha_ini"></div>
			</td>
			<td>
				Fecha F.
			</td>
			<td>
				<div id="produccion_aprobar_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				Cliente
			</td>
			<td colspan="3">
				<div id="produccion_aprobar_cliente"></div>
			</td>
			<td>
				ID Cliente
			</td>
			<td>
				<div id="produccion_aprobar_cliente_ID"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="produccion_aprobar_items_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="5" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="produccion_aprobar_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="produccion_aprobar_imprimir" value="Imprimir"/>
			</td>
		</tr>
	</table>
</div>