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
	var Orden = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Maquinaria_Content");
	var Body = document.getElementById("Maquinaria_Aprobar_Content");
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
	var Supervisor = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "CierreCaja" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "CierreCaja" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#caja_cierre_imprimir").jqxButton({ disabled: true });
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
		DiagnosticoSource.data = {"Diagnostico":true};
		var DiagnosticoDataAdapter = new $.jqx.dataAdapter(DiagnosticoSource);
		$("#maquinaria_aprobar_diagnostico").jqxComboBox({ source: DiagnosticoDataAdapter });
		//---
	}
	
	var EstadoValues1 = [
		{"Estado":"Pendiente"},
		{"Estado":"Aprobado"},
		{"Estado":"Anulado"},
	];
	
	var EstadoValues2 = [
		{"Estado":"Aprobado"},
		{"Estado":"Anulado"},
	];
	
	var ClasificacionValues = [
		{"Tipo":"Maquinaria"},
		{"Tipo":"Vehículos"},
	];
	
	var TipoSource =
	{
		datatype: "json",
		datafields: [
			{ name: "SubGrupo2", type: "string"}
		],
		url: "modulos/parametros.php",
	};
	
	var MotivoSource =
	{
		datatype: "json",
		datafields: [
			{ name: "SubGrupo", type: "string"}
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	var DiagnosticoSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Tipo", type: "string"},
		],
		url: "modulos/parametros.php",
	};
	
	$("#maquinaria_aprobar_ord_reparacion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	$("#maquinaria_aprobar_ord_reparacion").on("change", function (event) 
	{
		LoadValues();
	});
	
	$("#maquinaria_aprobar_estado").jqxComboBox(
	{
		theme: mytheme,
		width: 225,
		height: 20,
		source: EstadoValues1,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: 0,
		displayMember: "Estado",
		valueMember: "Estado"
	});
	$("#maquinaria_aprobar_estado").on("change", function (event)
	{
		if (event.args)
			LoadValues();
		else
			$("#maquinaria_aprobar_estado").jqxComboBox({selectedIndex: 0 }); 
	});
	
	$("#maquinaria_aprobar_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: "dd-MMMM-yyyy",
		culture: "es-ES",
	});
	$("#maquinaria_aprobar_fecha_ini").jqxDateTimeInput("setDate", new Date(Year, Month, Day));
	$("#maquinaria_aprobar_fecha_ini").on("change", function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#maquinaria_aprobar_clasificacion").jqxComboBox(
	{
		theme: mytheme,
		width: 225,
		height: 20,
		source: ClasificacionValues,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "Tipo",
		valueMember: "Tipo"
	});
	$("#maquinaria_aprobar_clasificacion").on("change", function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_aprobar_clasificacion").jqxComboBox("clearSelection");
			$("#maquinaria_aprobar_clasificacion").jqxComboBox("clear");
			$("#maquinaria_aprobar_tipo").jqxComboBox("clearSelection");
			$("#maquinaria_aprobar_tipo").jqxComboBox("clear");
		}
	});
	$("#maquinaria_aprobar_clasificacion").on("select", function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				LoadValues();
				TipoSource.data = {"Caja_SubGrupo2":event.args.item.value};
				MotivoSource.data = {"Caja_SubGrupo":event.args.item.value};
				DataAdapter1 = new $.jqx.dataAdapter(TipoSource);
				DataAdapter2 = new $.jqx.dataAdapter(MotivoSource);
				$("#maquinaria_aprobar_tipo").jqxComboBox({source: DataAdapter1});
				$("#maquinaria_aprobar_motivo").jqxComboBox({source: DataAdapter2});
			},350);
		}
		else
			LoadValues();
	});
	
	$("#maquinaria_aprobar_tipo").jqxComboBox(
	{
		theme: mytheme,
		width: 270,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "SubGrupo2",
		valueMember: "SubGrupo2"
	});
	$("#maquinaria_aprobar_tipo").on("change", function (event) 
	{
		if (!event.args)
			$("#maquinaria_aprobar_tipo").jqxComboBox("clearSelection");
		LoadValues();
	});
	
	$("#maquinaria_aprobar_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: "dd-MMMM-yyyy",
		culture: "es-ES",
	});
	$("#maquinaria_aprobar_fecha_fin").on("change", function (event) 
	{
		LoadValues();
	});
	
	$("#maquinaria_aprobar_motivo").jqxComboBox(
	{
		theme: mytheme,
		width: 225,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "SubGrupo",
		valueMember: "SubGrupo"
	});
	$("#maquinaria_aprobar_motivo").on("change", function (event) 
	{
		if (!event.args)
			$("#maquinaria_aprobar_motivo").jqxComboBox("clearSelection");
		LoadValues();
	});
	
	$("#maquinaria_aprobar_diagnostico").jqxComboBox(
	{
		theme: mytheme,
		width: 270,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "Tipo",
		valueMember: "Tipo"
	});
	$("#maquinaria_aprobar_diagnostico").on("change", function (event) 
	{
		if (!event.args)
			$("#maquinaria_aprobar_diagnostico").jqxComboBox("clearSelection");
		LoadValues();
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{
			GridSource.data = {
				"Maquinaria_Aprobar":true,
				"Ord_Reparacion":$("#maquinaria_aprobar_ord_reparacion").val(),
				"Estado":$("#maquinaria_aprobar_estado").val(),
				"Fecha_Ini":GetFormattedDate($("#maquinaria_aprobar_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#maquinaria_aprobar_fecha_fin").jqxDateTimeInput("getDate")),
				"Clasificacion":$("#maquinaria_aprobar_clasificacion").val(),
				"Tipo":$("#maquinaria_aprobar_tipo").val(),
				"Motivo":$("#maquinaria_aprobar_motivo").val(),
				"Diagnostico":$("#maquinaria_aprobar_diagnostico").val()
			};
			
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#maquinaria_aprobar_items_grid").jqxGrid({source: GridDataAdapter});
		},500);
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: "Aprobar", type: "bool" },
			{ name: "Estado", type: "string" },
			{ name: "Fecha", type: "date" },
			{ name: "Ord_Reparacion", type: "string" },
			{ name: "Maquina", type: "string" },
			{ name: "Motivo", type: "string" },
			{ name: "Diagnostico", type: "string" },
			{ name: "Total", type: "decimal" },
			{ name: "DigitadorID", type: "string" },
			{ name: "AutorizadorID", type: "string" },
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
			else if (NewState == "Anulado") {
				Alerts_Box("Una vez Anulado, no se podra revertir el proceso! Desea Continuar?", 4, true);
	
				var CheckTimer = setInterval(function() {
					if (ClickOK == true) {
						clearInterval(CheckTimer);
						ClickOK = false;
						var data = "Maquinaria_Aprobar=true&Estado=" + rowdata.Estado + "&Fecha=" + GetFormattedDate(rowdata.Fecha);
						data = data + "&Ord_Reparacion=" + rowdata.Ord_Reparacion;
						$.ajax({
							dataType: "text",
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
					if (ClickCANCEL == true) {
						clearInterval(CheckTimer);
						ClickCANCEL = false;
						commit(false);
					}
				}, 10);
			}
			else
			{
				var data = "Maquinaria_Aprobar=true&Estado=" + rowdata.Estado + "&Fecha=" + GetFormattedDate(rowdata.Fecha);
				data = data + "&Ord_Reparacion=" + rowdata.Ord_Reparacion;
				$.ajax({
					dataType: "text",
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
			OldState = "";
			NewState = "";
		}
	};
	
	$("#maquinaria_aprobar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		pagesizeoptions: ["10", "20", "30", "50", "100"],
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: "click",
		columns:
		[
			{
				text: "",
				datafield: "Aprobar",
				columntype: "checkbox",
				width: 15,
				editable: Admin ? true:Guardar,
				filterable: false,
			},
			{
				text: "Estado",
				datafield: "Estado",
				width: 70,
				height: 20,
				editable: Admin ? true:Guardar,
				columntype: "dropdownlist",
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoValues2,
						dropDownHeight: 125,
						dropDownWidth: 100,
						selectedIndex: -1,
						displayMember: "Estado",
						valueMember: "Estado",
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: "Fecha Inicial",
				datafield: "Fecha",
				columntype: "datetimeinput",
				width: 90,
				height: 20,
				editable: Admin ? true:false,
				filtertype: "date",
				cellsformat: "dd-MMM-yyyy",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: "es-ES" });
				}
			},
			{ text: "<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Orden Rep.", datafield: "Ord_Reparacion", editable: false, width: 100, height: 20 },
			{ text: "Maquinaria/Vehiculo", datafield: "Maquina", editable: false, width: 150, height: 20 },
			{ text: "Motivo Reparacion", datafield: "Motivo", width: 180, height: 20, editable: Admin ? true:Modificar, },
			{ text: "Diagnostico", datafield: "Diagnostico", width: 180, height: 20, editable: Admin ? true:Modificar, },
			{
				text: "Total",
				datafield: "Total",
				width: 125,
				height: 20,
				cellsformat: "c2",
				cellsalign: "right",
				columntype: "numberinput",
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: "Dig.", datafield: "DigitadorID", editable: false, width: 40, height: 20 },
			{ text: "Apr.", datafield: "AutorizadorID", editable: false, width: 40, height: 20 },
		]
	});
	$("#maquinaria_aprobar_items_grid").jqxGrid("localizestrings", localizationobj);
	$("#maquinaria_aprobar_items_grid").on("celldoubleclick", function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Ord_Reparacion")
		{
			Orden = $("#maquinaria_aprobar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Ord_Reparacion");
			$("#Maquinaria_Aprobar_Orden_Window").jqxWindow("open");
		}
	});
	$("#maquinaria_aprobar_items_grid").on("cellendedit", function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Aprobar")
		{
			var EstadoVal = $("#maquinaria_aprobar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Estado");
			OldState = EstadoVal;
			switch (EstadoVal)
			{
				case "Anulado":
					NewState = "Pendiente";
					$("#maquinaria_aprobar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Estado", "Anulado");
				break;
				case "Pendiente":
					NewState = "Aprobado";
					$("#maquinaria_aprobar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Estado", "Aprobado");
				break;
				case "Aprobado":
					//NewState = "Aprobado";
					//$("#maquinaria_aprobar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Estado", "Aprobado");
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#maquinaria_aprobar_guardar").jqxButton({
		width: 150,
		template: "info"
	});
	$("#maquinaria_aprobar_guardar").bind("click", function ()
	{
		$("#maquinaria_aprobar_items_grid").jqxGrid("updatebounddata");
	});
	
	$("#maquinaria_aprobar_imprimir").jqxButton({
		width: 150,
		template: "warning"
	});
	$("#maquinaria_aprobar_imprimir").bind("click", function ()
	{
		//
	});
	
	// ------------------------------------------ WINDOWS
	//--- Ord_Reparacion
	$("#Maquinaria_Aprobar_Orden_Window").jqxWindow({
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
	$("#Maquinaria_Aprobar_Orden_Window").on("open", function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Ord_Reparacion":Orden,
			},
			url: "maquinaria/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Maquinaria_Aprobar_Orden_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	// Load Initial Values!
	LoadParameters();
	LoadValues();
});
</script>
<div id="Maquinaria_Aprobar_Orden_Window">
	<div id="Maquinaria_Aprobar_Orden_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Reparacion</div>
	</div>
	<div id="Maquinaria_Aprobar_Orden_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Orden de Rep.
			</td>
			<td>
				<input type="text" id="maquinaria_aprobar_ord_reparacion"/>
			</td>
			<td>
				Estado de Orden
			</td>
			<td>
				<div id="maquinaria_aprobar_estado"></div>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Fecha Inicial
			</td>
			<td>
				<div id="maquinaria_aprobar_fecha_ini"></div>
			</td>
			<td>
				Clasificaci&oacute;n
			</td>
			<td>
				<div id="maquinaria_aprobar_clasificacion"></div>
			</td>
			<td>
				Seleccionar
			</td>
			<td>
				<div id="maquinaria_aprobar_tipo"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha Final
			</td>
			<td>
				<div id="maquinaria_aprobar_fecha_fin"></div>
			</td>
			<td>
				Motivo de Solicitud
			</td>
			<td>
				<div id="maquinaria_aprobar_motivo"></div>
			</td>
			<td>
				Diagnostico
			</td>
			<td>
				<div id="maquinaria_aprobar_diagnostico"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="maquinaria_aprobar_items_grid"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="5" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="maquinaria_aprobar_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="maquinaria_aprobar_imprimir" value="Imprimir"/>
			</td>
		</tr>
	</table>
</div>


