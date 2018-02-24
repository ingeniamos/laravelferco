<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Timer1 = 0;
	var Timer2 = 0;
	var OldState = "";
	var NewState = "";
	var Interno = "";
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	var ClienteData = [];
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Nomina_Content");
	var Body = document.getElementById("Nomina_AprobarExtras_Content");
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
				SystemMap("Aprobar Extras", true);
				
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
	var Guardar = false;
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Aprobar_Extras" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Aprobar_Extras" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#nomina_aprobar_extras_imprimir").jqxButton({ disabled: true });
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
		{"Estado":"Aprobado"},
		{"Estado":"Anulado"},
	];
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
		async: false
	};
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
	
	$("#nomina_aprobar_extras_interno").jqxInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
	});
	$("#nomina_aprobar_extras_interno").bind('change', function (event) {
		Add_Row();
	});
	
	$("#nomina_aprobar_extras_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 290,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Nombre',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#nomina_aprobar_extras_cliente").bind('change', function (event) {
		if (!event.args)
		{
			$("#nomina_aprobar_extras_cliente").jqxComboBox('clearSelection');
			$("#nomina_aprobar_extras_cliente_ID").jqxComboBox('clearSelection');
		}
		else
		{
			if ($("#nomina_aprobar_extras_cliente_ID").val() != event.args.item.value)
				$("#nomina_aprobar_extras_cliente_ID").val(event.args.item.value);
		}
	});
	
	$("#nomina_aprobar_extras_cliente_ID").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#nomina_aprobar_extras_cliente_ID").bind('change', function (event) {
		if (!event.args)
		{
			var item = $("#nomina_aprobar_extras_cliente_ID").jqxComboBox("getItemByValue", $("nomina_aprobar_extras_cliente_ID1").val());
			if (item != undefined)
			{
				$("#nomina_aprobar_extras_cliente").val(item.value);
				return;
			}
			$("#nomina_aprobar_extras_cliente").jqxComboBox('clearSelection');
			$("#nomina_aprobar_extras_cliente_ID").jqxComboBox('clearSelection');
		}
		else
		{
			if ($("#nomina_aprobar_extras_cliente").val() != event.args.item.value)
				$("#nomina_aprobar_extras_cliente").val(event.args.item.value);
		}
		Add_Row();
	});
	
	$("#nomina_aprobar_extras_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		selectedIndex: 0,
	});
	$("#nomina_aprobar_extras_estado").bind('change', function (event) {
		Add_Row();
	});
	
	$("#nomina_aprobar_extras_estado").jqxComboBox('addItem', {label: "Pendiente"});
	$("#nomina_aprobar_extras_estado").jqxComboBox('addItem', {label: "Aprobado"});
	$("#nomina_aprobar_extras_estado").jqxComboBox('addItem', {label: "Anulado"});
	
	$("#nomina_aprobar_extras_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 105,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_aprobar_extras_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#nomina_aprobar_extras_fecha_ini").on('change', function (event) 
	{
		if(event.args)
			Add_Row();
	});
	$("#nomina_aprobar_extras_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 105,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_aprobar_extras_fecha_fin").on('change', function (event) 
	{
		if(event.args)
			Add_Row();
	});
	
	function Add_Row()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{
			GridSource.data = {
				"Nomina_Aprobar_Extras":true,
				"Interno":$("#nomina_aprobar_extras_interno").val(),
				"ClienteID":$("#nomina_aprobar_extras_cliente_ID").val(),
				"Estado":$("#nomina_aprobar_extras_estado").val(),
				"Fecha_Ini":GetFormattedDate($("#nomina_aprobar_extras_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#nomina_aprobar_extras_fecha_fin").jqxDateTimeInput('getDate'))
			};
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#nomina_aprobar_extras_items_grid").jqxGrid({source: GridDataAdapter});
		},350);
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Aprobar', type: 'bool' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Nombre', value: 'ID', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'ID', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Justificacion', type: 'string' },
			{ name: 'Total', type: 'string' },
			{ name: 'AprobadorID', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
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
							url: "modulos/guardar.php",
							data: {
								"Nomina_Aprobar_Extras":true,
								"Estado":rowdata.Estado,
								"Interno":rowdata.Interno,
							},
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
					url: "modulos/guardar.php",
					data: {
						"Nomina_Aprobar_Extras":true,
						"Estado":rowdata.Estado,
						"Interno":rowdata.Interno,
					},
					success: function (data, status, xhr) {
						commit(true);
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
					}
				});
			}
			OldState = "";
			NewState = "";
		}
	};
	
	$("#nomina_aprobar_extras_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
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
				text: '',
				datafield: 'Aprobar',
				columntype: 'checkbox',
				width: 20,
				editable: Admin ? true:Guardar,
				filterable: false,
			},
			{
				text: 'Estado',
				datafield: 'Estado',
				width: 70,
				height: 20,
				editable: Admin ? true:Guardar,
				filterable: false,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoValues,
						dropDownHeight: 100,
						dropDownWidth: 70,
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
				columntype: 'datetimeinput',
				width: 90,
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Interno', datafield: 'Interno', editable: false, width: 100, height: 20 },
			{ text: 'Justificacion', datafield: 'Justificacion', editable: false, width: 180, height: 20 },
			{ text: 'Nombre', datafield: 'ID', displayfield: 'Nombre', editable: false, width: 265, height: 20 },
			{ text: 'Cedula', datafield: 'ClienteID', width: 100, height: 20, editable: false, },
			{
				text: 'Total Hrs',
				datafield: 'Total',
				width: 70,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 6 });
				}
			},
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 50, height: 20 },
			{ text: 'Apr.', datafield: 'AprobadorID', editable: false, width: 50, height: 20 },
		]
	});
	$("#nomina_aprobar_extras_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#nomina_aprobar_extras_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Interno")
		{
			Interno = $("#nomina_aprobar_extras_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Interno");
			$("#Nomina_Aprobar_Extras_Window").jqxWindow('open');
		}
		
	});
	$("#nomina_aprobar_extras_items_grid").on('cellendedit', function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Aprobar")
		{
			var EstadoVal = $("#nomina_aprobar_extras_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Estado");
			OldState = EstadoVal;
			switch (EstadoVal)
			{
				case "Aprobado":
					NewState = "Anulado";
					$("#nomina_aprobar_extras_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Anulado");
				break;
				case "Anulado":
					NewState = "Pendiente";
					$("#nomina_aprobar_extras_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Pendiente");
				break;
				case "Pendiente":
					NewState = "Aprobado";
					$("#nomina_aprobar_extras_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Estado", "Aprobado");
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
	
	// ------------------------------------------ WINDOWS
	//--- Extras
	$("#Nomina_Aprobar_Extras_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 550,
		minWidth: 500,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Nomina_Aprobar_Extras_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Interno":Interno,
			},
			url: "nomina/extras.php",
			async: true,
			success: function(data) 
			{
				$("#Nomina_Aprobar_Extras_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	$("#nomina_aprobar_extras_guardar").jqxButton({
		width: 150,
		template: "info"
	});
	$("#nomina_aprobar_extras_guardar").bind('click', function ()
	{
		$("#nomina_aprobar_extras_items_grid").jqxGrid("updatebounddata");
	});
	$("#nomina_aprobar_extras_imprimir").jqxButton({width: 150, template: "warning"});
	$("#nomina_aprobar_extras_imprimir").bind('click', function ()
	{
		//
	});
	
	$("#nomina_aprobar_extras_exportar").jqxButton({width: 150, template: "success"});
	$("#nomina_aprobar_extras_exportar").bind('click', function ()
	{
		//
	});
	
	
	// Load Default Values
	Add_Row();
	$("#nomina_aprobar_extras_interno").focus();
	CheckRefresh();
});
</script>
<div id="Nomina_Aprobar_Extras_Window">
	<div id="Nomina_Aprobar_Extras_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 220px;">Horas Extra</div>
	</div>
	<div id="Nomina_Aprobar_Extras_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 20px 0px;">
		<tr>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="nomina_aprobar_extras_interno"/>
			</td>
			<td>
				Empleado
			</td>
			<td colspan="2">
				<div id="nomina_aprobar_extras_cliente"></div>
			</td>
			<td>
				<div id="nomina_aprobar_extras_cliente_ID"></div>
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<div id="nomina_aprobar_extras_estado"></div>
			</td>
			<td>
				Fecha Ini
			</td>
			<td>
				<div id="nomina_aprobar_extras_fecha_ini"></div>
			</td>
			<td>
				<li class="parte1_li_txt">
					Fecha Fin
				</li>
				<li style="margin-left:15px;">
					<div id="nomina_aprobar_extras_fecha_fin"></div>
				</li>
			</td>
		</tr>
	</table>
	
	<div id="nomina_aprobar_extras_items_grid"></div>
	
	<table cellpadding="5" cellspacing="0" style="margin:20px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="nomina_aprobar_extras_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="nomina_aprobar_extras_imprimir" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="nomina_aprobar_extras_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>

