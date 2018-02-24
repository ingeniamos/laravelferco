<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var ClienteID = "";
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Nomina_Content");
	var Body = document.getElementById("Nomina_BioControl_Content");
	var Times = 0;
	var Refresh = 0;
	var Hide = 0;
	
	function CheckRefresh ()
	{
		clearInterval(Refresh);
		Refresh = setInterval(function()
		{
			if (Times <= 0 && (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none"))
			{
				Times++;
			}
			else if (Times > 0 && jQuery.css( Body, "display" ) === "block" && jQuery.css( Main, "display" ) === "block")
			{
				//Code... do something?
				SystemMap("BioControl", true);
				LoadParameters("Empleados");
				LoadParameters("Grid");
				
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
			if (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none")
			{
				CheckRefresh();
				clearInterval(Hide);
			}
		},200);
	};
	// END - Code for Refresh Data
	
	//---
	var Modificar = false;
	var Imprimir = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "BioControl" && $data[$i]["Modificar"] == "true")
				{
		?>
					Modificar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "BioControl" && $data[$i]["Imprimir"] == "true")
				{
		?>
					Imprimir = true;
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
	
	function LoadParameters(Val)
	{
		switch(Val)
		{
			case "Empleados":
				EmpleadosSource.data = {"Clientes_Nomina":true};
				var EmpleadosDataAdapter = new $.jqx.dataAdapter(EmpleadosSource,
				{
					autoBind: true,
					loadComplete: function (records)
					{
						$("#nomina_biocontrol_cliente").jqxComboBox({source: records});
						$("#nomina_biocontrol_cliente_ID").jqxComboBox({source: records});
					}
				});
			break;
			
			case "Grid":
				//$("#nomina_biocontrol_items_grid").jqxGrid("updatebounddata");
				GridSource.data = {
					"Nomina_BioControl":true,
					"ClienteID":ClienteID,
					"Fecha_Ini":GetFormattedDate($("#nomina_biocontrol_fecha_ini").jqxDateTimeInput("getDate")),
					"Fecha_Fin":GetFormattedDate($("#nomina_biocontrol_fecha_fin").jqxDateTimeInput("getDate")),
				};
				var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
				$("#nomina_biocontrol_items_grid").jqxGrid({source: GridDataAdapter});
			break;
		}
	}
	
	var EmpleadosSource =
	{
		datatype: "json",
		datafields: [
			{ name: "ClienteID", type: "string"},
			{ name: "Nombre", type: "string"},
		],
		url: "modulos/datos.php",
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields: [
			{ name: "ID", type: "int"},
			{ name: "Nombre", type: "string"},
			{ name: "ClienteID", type: "string"},
			{ name: "Fecha", type: "date"},
			{ name: "Horas", type: "decimal"},
			{ name: "Turno_Ini1", type: "date"},
			{ name: "Turno_Fin1", type: "date"},
			{ name: "Hora_Ini1", type: "date"},
			{ name: "Hora_Fin1", type: "date"},
			{ name: "Turno_Ini2", type: "date"},
			{ name: "Turno_Fin2", type: "date"},
			{ name: "Hora_Ini2", type: "date"},
			{ name: "Hora_Fin2", type: "date"},
		],
		url: "modulos/datos.php",
	};
	
	$("#nomina_biocontrol_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 270,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar Empleado",
		selectedIndex: -1,
		displayMember: "Nombre",
		valueMember: "ClienteID"
	});
	$("#nomina_biocontrol_cliente").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#nomina_biocontrol_cliente_ID").val() != event.args.item.value)
				$("#nomina_biocontrol_cliente_ID").jqxComboBox("selectItem", event.args.item.value);
		}
		else
		{
			var item_value = $("#nomina_biocontrol_cliente").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				ClienteID = "";
				$("#nomina_biocontrol_cliente_ID").jqxComboBox("clearSelection");
				$("#nomina_biocontrol_cliente").jqxComboBox("clearSelection");
			}
			else
			{
				var value = $("#nomina_biocontrol_cliente").val();
				
				var item = $("#nomina_biocontrol_cliente").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#nomina_biocontrol_cliente").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				ClienteID = "";
				$("#nomina_biocontrol_cliente_ID").jqxComboBox("clearSelection");
				$("#nomina_biocontrol_cliente").jqxComboBox("clearSelection");
			}
			LoadParameters("Grid");
		}
	});
	
	$("#nomina_biocontrol_cliente_ID").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar ID",
		selectedIndex: -1,
		displayMember: "ClienteID",
		valueMember: "ClienteID"
	});
	$("#nomina_biocontrol_cliente_ID").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#nomina_biocontrol_cliente_ID").jqxComboBox("selectItem", ClienteID);
	});
	$("#nomina_biocontrol_cliente_ID").on("change", function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			if ($("#nomina_biocontrol_cliente").val() != event.args.item.value)
				$("#nomina_biocontrol_cliente").jqxComboBox("selectItem", event.args.item.value);
			
			LoadParameters("Grid");
		}
		else
		{
			var item_value = $("#nomina_biocontrol_cliente_ID").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				ClienteID = "";
				$("#nomina_biocontrol_cliente_ID").jqxComboBox("clearSelection");
				$("#nomina_biocontrol_cliente").jqxComboBox("clearSelection");
				LoadParameters("Grid");
			}
			else
			{
				var value = $("#nomina_biocontrol_cliente_ID").val();
				var item = $("#nomina_biocontrol_cliente_ID").jqxComboBox("getItemByValue", value);
				if (item == undefined)
				{
					ClienteID = "";
					$("#nomina_biocontrol_cliente_ID").jqxComboBox("clearSelection");
					$("#nomina_biocontrol_cliente").jqxComboBox("clearSelection");
					LoadParameters("Grid");
				}
				else
					$("#nomina_biocontrol_cliente_ID").jqxComboBox("selectItem", item.value);
			}
		}
	});
	
	$("#nomina_biocontrol_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 105,
		height: 20,
		formatString: "dd-MMM-yyyy",
		culture: "es-ES",
	});
	//$("#nomina_biocontrol_fecha_ini").jqxDateTimeInput("setDate", new Date(Year, Month, Day));
	$("#nomina_biocontrol_fecha_ini").on("change", function (event) 
	{
		if(event.args)
			LoadParameters("Grid");
	});
	$("#nomina_biocontrol_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 105,
		height: 20,
		formatString: "dd-MMM-yyyy",
		culture: "es-ES",
	});
	$("#nomina_biocontrol_fecha_fin").on("change", function (event) 
	{
		if(event.args)
			LoadParameters("Grid");
	});
	
	$("#nomina_biocontrol_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		sortable: true,
		pageable: true,
		pagesizeoptions: ["10", "20", "50", "100"],
		pagesize: 20,
		editable: false,
		editmode: "dblclick",
		showfilterrow: true,
		filterable: true,
		columns:
		[
			{ text: "", datafield: "ID", editable: false, filterable: false, width: 0 },
			{
				text: "Fecha",
				datafield: "Fecha",
				align: "center",
				columntype: "datetime",
				width: 90,
				height: 20,
				filtertype: "date",
				cellsformat: "dd-MMM-yyyy",
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: "es-ES" });
				}
			},
			{ text: "Nombre", datafield: "Nombre", align: "center", width: "20%", height: 20, editable: false },
			{ text: "Cedula", datafield: "ClienteID", align: "center", width: "10%", height: 20, editable: false },
			{
				text: "H. Labs",
				datafield: "Horas",
				align: "center",
				width: 50,
				height: 20,
				cellsalign: "center",
				columntype: "numberinput",
				filterable: true,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, max: 999, spinButtons: false });
				}
			},
			{
				text: "Turno Ini",
				datafield: "Turno_Ini1",
				align: "center",
				columngroup: "TRUNO1",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: "HH:mm",
			},
			{
				text: "Hora Ini",
				datafield: "Hora_Ini1",
				align: "center",
				columngroup: "TRUNO1",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: "HH:mm",
			},
			{
				text: "Turno Fin",
				datafield: "Turno_Fin1",
				align: "center",
				columngroup: "TRUNO1",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: "HH:mm",
			},
			{
				text: "Hora Fin",
				datafield: "Hora_Fin1",
				align: "center",
				columngroup: "TRUNO1",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: "HH:mm",
			},
			{
				text: "Turno Ini",
				datafield: "Turno_Ini2",
				align: "center",
				columngroup: "TRUNO2",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: "HH:mm",
			},
			{
				text: "Hora Ini",
				datafield: "Hora_Ini2",
				align: "center",
				columngroup: "TRUNO2",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: "HH:mm",
			},
			{
				text: "Turno Fin",
				datafield: "Turno_Fin2",
				align: "center",
				columngroup: "TRUNO2",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: "HH:mm",
			},
			{
				text: "Hora Fin",
				datafield: "Hora_Fin2",
				align: "center",
				columngroup: "TRUNO2",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: "HH:mm",
			},
		],
		columngroups: 
		[
			{ text: "TURNO 1", align: "center", name: "TRUNO1" },
			{ text: "TURNO 2", align: "center", name: "TRUNO2" },
		]
	});
	$("#nomina_biocontrol_items_grid").jqxGrid("hidecolumn", "ID");
	$("#nomina_biocontrol_items_grid").jqxGrid("localizestrings", localizationobj);
	
	$("#nomina_biocontrol_imprimir").jqxButton({
		width: 130,
		height: 30,
		template: "warning",
	});
	$("#nomina_biocontrol_imprimir").on("click", function ()
	{
		//
	});
	
	LoadParameters("Empleados");
	LoadParameters("Grid");
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 20px 0px;">
		<tr>
			<td>
				Empleado
			</td>
			<td>
				<div id="nomina_biocontrol_cliente"></div>
			</td>
			<td>
				Empleado ID
			</td>
			<td>
				<div id="nomina_biocontrol_cliente_ID"></div>
			</td>
			<td>
				Fecha Ini.
			</td>
			<td>
				<div id="nomina_biocontrol_fecha_ini"></div>
			</td>
			<td>
				Fecha Fin.
			</td>
			<td>
				<div id="nomina_biocontrol_fecha_fin"></div>
			</td>
		</tr>
	</table>
	<div id="nomina_biocontrol_items_grid"></div>
	<input type="button" id="nomina_biocontrol_imprimir" value="Imprimir" style="margin-top:20px;"/>
</div>