<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var Locked = false;
	var Categoria = "";
	var Maquina = "";
	var Motivo = "";
	var Diagnostico = "";
	var ClienteID = "";
	var Digitador = "";
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Reportes_Content");
	var Body = document.getElementById("Reportes_Maquinaria_Content");
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
				SystemMap("Maquinaria", true);
				LoadParameters();
				
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
	var Imprimir = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Maquinaria" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Maquinaria" && $data[$i]["Imprimir"] == "true")
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
	
	function LoadParameters()
	{
		$("#reportes_maquinaria_categoria").jqxComboBox({source: CategoriaValues});
		
		DiagnosticoSource.data = {"Diagnostico":true};
		var DiagnosticoDataAdapter = new $.jqx.dataAdapter(DiagnosticoSource);
		$("#reportes_maquinaria_diagnostico").jqxComboBox({source: DiagnosticoDataAdapter});
		
		var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
		{
			autoBind: true,
			/*beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
				{
					ClienteData.push(records[i]);
				}
			},*/
			loadComplete: function (records)
			{
				$("#reportes_maquinaria_cliente").jqxComboBox({source: records});
				$("#reportes_maquinaria_cliente_ID").jqxComboBox({source: records});
			},
		});
		
		VendedorSource.data = {"Venta":true};
		var VendedorDataAdapter = new $.jqx.dataAdapter(VendedorSource);
		$("#reportes_maquinaria_digitador").jqxComboBox({source: VendedorDataAdapter});
	}
	
	var CategoriaValues = [
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
	};
	
	var DiagnosticoSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Tipo", type: "string"},
		],
		url: "modulos/parametros.php",
		data: {"Diagnostico":true},
	};
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Nombre", type: "string"},
			{ name: "ClienteID", type: "string"}
		],
		url: "modulos/datos.php",
	};
	
	var VendedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Codigo", type: "string"},
			{ name: "Vendedor", type: "string"}
		],
		url: "modulos/parametros.php",
	};
	
	$("#reportes_maquinaria_categoria").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "Tipo",
		valueMember: "Tipo"
	});
	$("#reportes_maquinaria_categoria").on("change", function (event)
	{
		if (event.args)
		{
			if (Categoria == event.args.item.value)
				return;
			
			Categoria = event.args.item.value;
			
			TipoSource.data = {"Caja_SubGrupo2":event.args.item.value};
			MotivoSource.data = {"Caja_SubGrupo":event.args.item.value};
			DataAdapter1 = new $.jqx.dataAdapter(TipoSource);
			DataAdapter2 = new $.jqx.dataAdapter(MotivoSource);
			$("#reportes_maquinaria_tipo").jqxComboBox({source: DataAdapter1});
			$("#reportes_maquinaria_motivo").jqxComboBox({source: DataAdapter2});
			LoadValues();
		}
		else
		{
			var item_value = $("#reportes_maquinaria_categoria").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				Categoria = "";
				$("#reportes_maquinaria_categoria").jqxComboBox("clearSelection");
				$("#reportes_maquinaria_tipo").jqxComboBox("clearSelection");
				$("#reportes_maquinaria_tipo").jqxComboBox("clear");
				$("#reportes_maquinaria_motivo").jqxComboBox("clearSelection");
				$("#reportes_maquinaria_motivo").jqxComboBox("clear");
				LoadValues();
			}
			else
			{
				var value = $("#reportes_maquinaria_categoria").val();
				var item = $("#reportes_maquinaria_categoria").jqxComboBox("getItemByValue", value);
				if (item == undefined)
				{
					Categoria = "";
					$("#reportes_maquinaria_categoria").jqxComboBox("clearSelection");
					$("#reportes_maquinaria_tipo").jqxComboBox("clearSelection");
					$("#reportes_maquinaria_tipo").jqxComboBox("clear");
					$("#reportes_maquinaria_motivo").jqxComboBox("clearSelection");
					$("#reportes_maquinaria_motivo").jqxComboBox("clear");
					LoadValues();
				}
				else
					$("#reportes_maquinaria_categoria").jqxComboBox("selectItem", item.value);
			}
		}
	});
	$("#reportes_maquinaria_categoria").on("bindingComplete", function (event)
	{
		if (Categoria != "")
			$("#reportes_maquinaria_categoria").jqxComboBox("selectItem", Categoria);
	});
	
	$("#reportes_maquinaria_tipo").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "SubGrupo2",
		valueMember: "SubGrupo2"
	});
	$("#reportes_maquinaria_tipo").on("change", function (event)
	{
		if (event.args)
		{
			if (Maquina == event.args.item.value)
				return;
			
			Maquina = event.args.item.value;
			LoadValues();
		}
		else
		{
			var item_value = $("#reportes_maquinaria_tipo").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				Maquina = "";
				$("#reportes_maquinaria_tipo").jqxComboBox("clearSelection");
				LoadValues();
			}
			else
			{
				var value = $("#reportes_maquinaria_tipo").val();
				
				var item = $("#reportes_maquinaria_tipo").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_maquinaria_tipo").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				Maquina = "";
				$("#reportes_maquinaria_tipo").jqxComboBox("clearSelection");
				LoadValues();
			}
		}
	});
	$("#reportes_maquinaria_tipo").on("bindingComplete", function (event)
	{
		if (Maquina != "")
			$("#reportes_maquinaria_tipo").jqxComboBox("selectItem", Maquina);
	});
	
	$("#reportes_maquinaria_motivo").jqxComboBox(
	{
		theme: mytheme,
		width: 206,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "SubGrupo",
		valueMember: "SubGrupo"
	});
	$("#reportes_maquinaria_motivo").on("change", function (event)
	{
		if (event.args)
		{
			if (Motivo == event.args.item.value)
				return;
			
			Motivo = event.args.item.value;
			LoadValues();
		}
		else
		{
			var item_value = $("#reportes_maquinaria_motivo").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				Motivo = "";
				$("#reportes_maquinaria_motivo").jqxComboBox("clearSelection");
				LoadValues();
			}
			else
			{
				var value = $("#reportes_maquinaria_motivo").val();
				
				var item = $("#reportes_maquinaria_motivo").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_maquinaria_motivo").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				Motivo = "";
				$("#reportes_maquinaria_motivo").jqxComboBox("clearSelection");
				LoadValues();
			}
		}
	});
	$("#reportes_maquinaria_motivo").on("bindingComplete", function (event)
	{
		if (Motivo != "")
			$("#reportes_maquinaria_motivo").jqxComboBox("selectItem", Motivo);
	});
	
	$("#reportes_maquinaria_diagnostico").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "Tipo",
		valueMember: "Tipo",
	});
	$("#reportes_maquinaria_diagnostico").on("change", function (event)
	{
		if (event.args)
		{
			if (Diagnostico == event.args.item.value)
				return;
			
			Diagnostico = event.args.item.value;
			LoadValues();
		}
		else
		{
			var item_value = $("#reportes_maquinaria_diagnostico").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				Diagnostico = "";
				$("#reportes_maquinaria_diagnostico").jqxComboBox("clearSelection");
				LoadValues();
			}
			else
			{
				var value = $("#reportes_maquinaria_diagnostico").val();
				
				var item = $("#reportes_maquinaria_diagnostico").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_maquinaria_diagnostico").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				Diagnostico = "";
				$("#reportes_maquinaria_diagnostico").jqxComboBox("clearSelection");
				LoadValues();
			}
		}
	});
	$("#reportes_maquinaria_diagnostico").on("bindingComplete", function (event)
	{
		if (Diagnostico != "")
			$("#reportes_maquinaria_diagnostico").jqxComboBox("selectItem", Diagnostico);
	});
	
	$("#reportes_maquinaria_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 378,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Seleccionar Proveedor",
		selectedIndex: -1,
		displayMember: "Nombre",
		valueMember: "ClienteID"
	});
	$("#reportes_maquinaria_cliente").on("change", function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			if ($("#reportes_maquinaria_cliente_ID").val() != event.args.item.value)
				$("#reportes_maquinaria_cliente_ID").jqxComboBox("selectItem", event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_maquinaria_cliente").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				ClienteID = "";
				$("#reportes_maquinaria_cliente_ID").jqxComboBox("clearSelection");
				$("#reportes_maquinaria_cliente").jqxComboBox("clearSelection");
				LoadValues();
			}
			else
			{
				var value = $("#reportes_maquinaria_cliente").val();
				
				var item = $("#reportes_maquinaria_cliente").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_maquinaria_cliente").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				ClienteID = "";
				$("#reportes_maquinaria_cliente_ID").jqxComboBox("clearSelection");
				$("#reportes_maquinaria_cliente").jqxComboBox("clearSelection");
				LoadValues();
			}
		}
	});
	$("#reportes_maquinaria_cliente").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#reportes_maquinaria_cliente").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#reportes_maquinaria_cliente_ID").jqxComboBox(
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
	$("#reportes_maquinaria_cliente_ID").on("change", function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			if ($("#reportes_maquinaria_cliente").val() != event.args.item.value)
				$("#reportes_maquinaria_cliente").jqxComboBox("selectItem", event.args.item.value);
			
			LoadValues();
		}
		else
		{
			var item_value = $("#reportes_maquinaria_cliente_ID").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				ClienteID = "";
				$("#reportes_maquinaria_cliente_ID").jqxComboBox("clearSelection");
				$("#reportes_maquinaria_cliente").jqxComboBox("clearSelection");
				LoadValues();
			}
			else
			{
				var value = $("#reportes_maquinaria_cliente_ID").val();
				var item = $("#reportes_maquinaria_cliente_ID").jqxComboBox("getItemByValue", value);
				if (item == undefined)
				{
					ClienteID = "";
					$("#reportes_maquinaria_cliente_ID").jqxComboBox("clearSelection");
					$("#reportes_maquinaria_cliente").jqxComboBox("clearSelection");
					LoadValues();
				}
				else
					$("#reportes_maquinaria_cliente_ID").jqxComboBox("selectItem", item.value);
			}
		}
	});
	$("#reportes_maquinaria_cliente_ID").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#reportes_maquinaria_cliente_ID").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#reportes_maquinaria_digitador").jqxComboBox(
	{
		theme: mytheme,
		width: 80,
		height: 20,
		searchMode: "containsignorecase",
		autoComplete: true,
		promptText: "Selec...",
		selectedIndex: -1,
		displayMember: "Codigo",
		valueMember: "Codigo",
	});
	$("#reportes_maquinaria_digitador").on("change", function (event)
	{
		if (event.args)
		{
			if (Digitador == event.args.item.value)
				return;
			
			Digitador = event.args.item.value;
			LoadValues();
		}
		else
		{
			var item_value = $("#reportes_maquinaria_digitador").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				Digitador = "";
				$("#reportes_maquinaria_digitador").jqxComboBox("clearSelection");
				LoadValues();
			}
			else
			{
				var value = $("#reportes_maquinaria_digitador").val();
				
				var item = $("#reportes_maquinaria_digitador").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_maquinaria_digitador").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				Digitador = "";
				$("#reportes_maquinaria_digitador").jqxComboBox("clearSelection");
				LoadValues();
			}
		}
	});
	$("#reportes_maquinaria_digitador").on("bindingComplete", function (event)
	{
		if (Digitador != "")
			$("#reportes_maquinaria_digitador").jqxComboBox("selectItem", Digitador);
	});
	
	$("#reportes_maquinaria_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: "dd-MMM-yyyy",
		culture: "es-ES",
	});
	$("#reportes_maquinaria_fecha_ini").jqxDateTimeInput("setDate", new Date(Year, Month, Day));
	$("#reportes_maquinaria_fecha_ini").on("change", function (event) 
	{  
		if(event.args)
			LoadValues();
	});
	
	$("#reportes_maquinaria_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: "dd-MMM-yyyy",
		culture: "es-ES",
	});
	$("#reportes_maquinaria_fecha_fin").on("change", function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	function LoadValues()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var Cat = $("#reportes_maquinaria_categoria").jqxComboBox("getSelectedItem");
		var Tip = $("#reportes_maquinaria_tipo").jqxComboBox("getSelectedItem");
		var Mot = $("#reportes_maquinaria_motivo").jqxComboBox("getSelectedItem");
		var Diag = $("#reportes_maquinaria_diagnostico").jqxComboBox("getSelectedItem");
		var Client = $("#reportes_maquinaria_cliente").jqxComboBox("getSelectedItem");
		var Dig = $("#reportes_maquinaria_digitador").jqxComboBox("getSelectedItem");
		
		if (!Cat)
			Cat = "";
		else
			Cat = Cat.value;
		
		if (!Tip)
			Tip = "";
		else
			Tip = Tip.value;
		
		if (!Mot)
			Mot = "";
		else
			Mot = Mot.value;
		
		if (!Diag)
			Diag = "";
		else
			Diag = Diag.value;
		
		if (!Client)
			Client = "";
		else
			Client = Client.value;
		
		if (!Dig)
			Dig = "";
		else
			Dig = Dig.value;
		
		GridSource.data = {
			"Reportes_Maquinaria":true,
			"Fecha_Ini":GetFormattedDate($("#reportes_maquinaria_fecha_ini").jqxDateTimeInput("getDate")),
			"Fecha_Fin":GetFormattedDate($("#reportes_maquinaria_fecha_fin").jqxDateTimeInput("getDate")),
			"Categoria":Cat,
			"Tipo":Tip,
			"Motivo":Mot,
			"Diagnostico":Diag,
			"ClienteID":Client,
			"DigitadorID":Dig,
		};
		
		var GridDataAdapter = new $.jqx.dataAdapter(GridSource,
		{
			loadComplete: function (records)
			{
				Locked = false;
			},
		});
		$("#reportes_maquinaria_items_grid").jqxGrid({source: GridDataAdapter});
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: "Fecha", type: "date" },
			{ name: "Ord_Reparacion", type: "string" },
			{ name: "Tipo", type: "string" },
			{ name: "Motivo", type: "string" },
			{ name: "Diagnostico", type: "string" },
			{ name: "Total", type: "decimal" },
			{ name: "DigitadorID", type: "string" },
		],
		url: "modulos/datos.php",
	};
	
	$("#reportes_maquinaria_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: "singlecell",
		sortable: true,
		pageable: true,
		pagesizeoptions: ["10", "20", "30", "50", "100"],
		pagesize: 20,
		editable: false,
		showstatusbar: true,
		statusbarheight: 25,
		showaggregates: true,
		columns:
		[
			{
				text: "Fecha Ini.",
				datafield: "Fecha",
				editable: false,
				width: "10%",
				height: 20,
				pinned: true,
				columntype: "datetimeinput",
				cellsformat: "dd-MMM-yyyy",
			},
			{
				text: "<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Orden Rep.", 
				datafield: "Ord_Reparacion", 
				editable: false, 
				width: "10%",
				height: 20, 
			},
			{ text: "Maquinaria/Vehiculo", datafield: "Tipo", editable: false, width: "20%", height: 20 },
			{ text: "Motivo Reparacion", datafield: "Motivo", width: "20%", height: 20, editable: false, },
			{ text: "Diagnostico", datafield: "Diagnostico", width: "20%", height: 20, editable: false, },
			{ text: "Dig.", datafield: "DigitadorID", editable: false, width: "5%", height: 20 },
			{
				text: "Total",
				datafield: "Total",
				width: "15%",
				height: 20,
				cellsformat: "c2",
				cellsalign: "right",
				columntype: "numberinput",
				editable: false,
				aggregates: [{ "<b>Total</b>": 
					function (aggregatedValue, currentValue, column, record)
					{
						return aggregatedValue + currentValue;
					}
				}]
			},
		]
	});
	$("#reportes_maquinaria_items_grid").jqxGrid("localizestrings", localizationobj);
	
	$("#reportes_maquinaria_export").jqxButton({
		width: 110,
		template: "success"
	});
	$("#reportes_maquinaria_export").on("click", function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		var Cat = $("#reportes_maquinaria_categoria").jqxComboBox("getSelectedItem");
		var Tip = $("#reportes_maquinaria_tipo").jqxComboBox("getSelectedItem");
		var Mot = $("#reportes_maquinaria_motivo").jqxComboBox("getSelectedItem");
		var Diag = $("#reportes_maquinaria_diagnostico").jqxComboBox("getSelectedItem");
		var Client = $("#reportes_maquinaria_cliente").jqxComboBox("getSelectedItem");
		var Dig = $("#reportes_maquinaria_digitador").jqxComboBox("getSelectedItem");
		
		if (!Cat)
			Cat = "";
		else
			Cat = Cat.value;
		
		if (!Tip)
			Tip = "";
		else
			Tip = Tip.value;
		
		if (!Mot)
			Mot = "";
		else
			Mot = Mot.value;
		
		if (!Diag)
			Diag = "";
		else
			Diag = Diag.value;
		
		if (!Client)
			Client = "";
		else
			Client = Client.value;
		
		if (!Dig)
			Dig = "";
		else
			Dig = Dig.value;
		
		var data = "modulos/export_xls.php?Reportes_Maquinaria=true";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_maquinaria_fecha_ini").jqxDateTimeInput("getDate"));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_maquinaria_fecha_fin").jqxDateTimeInput("getDate"));
		data += "&Categoria="+Cat+"&Tipo="+Tip+"&Motivo="+Mot+"&Diagnostico="+Diag+"&ClienteID="+Client+"&DigitadorID="+Dig;
		window.location = data;
	});
	
	$("#reportes_maquinaria_imprimir1").jqxButton({
		width: 100,
		template: "warning"
	});
	$("#reportes_maquinaria_imprimir1").on("click", function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		var Cat = $("#reportes_maquinaria_categoria").jqxComboBox("getSelectedItem");
		var Tip = $("#reportes_maquinaria_tipo").jqxComboBox("getSelectedItem");
		var Mot = $("#reportes_maquinaria_motivo").jqxComboBox("getSelectedItem");
		var Diag = $("#reportes_maquinaria_diagnostico").jqxComboBox("getSelectedItem");
		var Client = $("#reportes_maquinaria_cliente").jqxComboBox("getSelectedItem");
		var Dig = $("#reportes_maquinaria_digitador").jqxComboBox("getSelectedItem");
		
		if (!Cat)
			Cat = "";
		else
			Cat = Cat.value;
		
		if (!Tip)
			Tip = "";
		else
			Tip = Tip.value;
		
		if (!Mot)
			Mot = "";
		else
			Mot = Mot.value;
		
		if (!Diag)
			Diag = "";
		else
			Diag = Diag.value;
		
		if (!Client)
			Client = "";
		else
			Client = Client.value;
		
		if (!Dig)
			Dig = "";
		else
			Dig = Dig.value;
		
		var data = "imprimir/reportes_maquinaria.php?OrderBy=";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_maquinaria_fecha_ini").jqxDateTimeInput("getDate"));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_maquinaria_fecha_fin").jqxDateTimeInput("getDate"));
		data += "&Categoria="+Cat+"&Tipo="+Tip+"&Motivo="+Mot+"&Diagnostico="+Diag+"&ClienteID="+Client+"&DigitadorID="+Dig;
		window.open(data, "", "width=830, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_maquinaria_imprimir2").jqxButton({
		width: 160,
		template: "warning"
	});
	$("#reportes_maquinaria_imprimir2").on("click", function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		var Cat = $("#reportes_maquinaria_categoria").jqxComboBox("getSelectedItem");
		var Tip = $("#reportes_maquinaria_tipo").jqxComboBox("getSelectedItem");
		var Mot = $("#reportes_maquinaria_motivo").jqxComboBox("getSelectedItem");
		var Diag = $("#reportes_maquinaria_diagnostico").jqxComboBox("getSelectedItem");
		var Client = $("#reportes_maquinaria_cliente").jqxComboBox("getSelectedItem");
		var Dig = $("#reportes_maquinaria_digitador").jqxComboBox("getSelectedItem");
		
		if (!Cat)
			Cat = "";
		else
			Cat = Cat.value;
		
		if (!Tip)
			Tip = "";
		else
			Tip = Tip.value;
		
		if (!Mot)
			Mot = "";
		else
			Mot = Mot.value;
		
		if (!Diag)
			Diag = "";
		else
			Diag = Diag.value;
		
		if (!Client)
			Client = "";
		else
			Client = Client.value;
		
		if (!Dig)
			Dig = "";
		else
			Dig = Dig.value;
		
		var data = "imprimir/reportes_maquinaria.php?OrderBy=Tipo";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_maquinaria_fecha_ini").jqxDateTimeInput("getDate"));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_maquinaria_fecha_fin").jqxDateTimeInput("getDate"));
		data += "&Categoria="+Cat+"&Tipo="+Tip+"&Motivo="+Mot+"&Diagnostico="+Diag+"&ClienteID="+Client+"&DigitadorID="+Dig;
		window.open(data, "", "width=630, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_maquinaria_imprimir3").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#reportes_maquinaria_imprimir3").on("click", function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		var Cat = $("#reportes_maquinaria_categoria").jqxComboBox("getSelectedItem");
		var Tip = $("#reportes_maquinaria_tipo").jqxComboBox("getSelectedItem");
		var Mot = $("#reportes_maquinaria_motivo").jqxComboBox("getSelectedItem");
		var Diag = $("#reportes_maquinaria_diagnostico").jqxComboBox("getSelectedItem");
		var Client = $("#reportes_maquinaria_cliente").jqxComboBox("getSelectedItem");
		var Dig = $("#reportes_maquinaria_digitador").jqxComboBox("getSelectedItem");
		
		if (!Cat)
			Cat = "";
		else
			Cat = Cat.value;
		
		if (!Tip)
			Tip = "";
		else
			Tip = Tip.value;
		
		if (!Mot)
			Mot = "";
		else
			Mot = Mot.value;
		
		if (!Diag)
			Diag = "";
		else
			Diag = Diag.value;
		
		if (!Client)
			Client = "";
		else
			Client = Client.value;
		
		if (!Dig)
			Dig = "";
		else
			Dig = Dig.value;
		
		var data = "imprimir/reportes_maquinaria.php?OrderBy=Motivo";
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_maquinaria_fecha_ini").jqxDateTimeInput("getDate"));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_maquinaria_fecha_fin").jqxDateTimeInput("getDate"));
		data += "&Categoria="+Cat+"&Tipo="+Tip+"&Motivo="+Mot+"&Diagnostico="+Diag+"&ClienteID="+Client+"&DigitadorID="+Dig;
		window.open(data, "", "width=630, height=600, menubar=no, titlebar=no");
	});
	
	if (!Admin && !Imprimir)
	{
		$("#reportes_maquinaria_export").jqxButton({ disabled: true });
		$("#reportes_maquinaria_imprimir1").jqxButton({ disabled: true });
		$("#reportes_maquinaria_imprimir2").jqxButton({ disabled: true });
		$("#reportes_maquinaria_imprimir3").jqxButton({ disabled: true });
	}
	
	LoadParameters();
	LoadValues();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Tipo
			</td>
			<td>
				Maquina/Vehículo
			</td>
			<td colspan="2">
				Motivo
			</td>
			<td>
				Diagnostico
			</td>
		</tr>
		<tr>
			<td>
				<div id="reportes_maquinaria_categoria"></div>
			</td>
			<td>
				<div id="reportes_maquinaria_tipo"></div>
			</td>
			<td colspan="2">
				<div id="reportes_maquinaria_motivo"></div>
			</td>
			<td colspan="3">
				<div id="reportes_maquinaria_diagnostico"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Proveedor
			</td>
			<td>
				Proveedor ID
			</td>
			<td>
				Digitador
			</td>
			<td>
				Fecha Ini.
			</td>
			<td>
				Fecha Fin.
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="reportes_maquinaria_cliente"></div>
			</td>
			<td>
				<div id="reportes_maquinaria_cliente_ID"></div>
			</td>
			<td>
				<div id="reportes_maquinaria_digitador"></div>
			</td>
			<td>
				<div id="reportes_maquinaria_fecha_ini"></div>
			</td>
			<td>
				<div id="reportes_maquinaria_fecha_fin"></div>
			</td>
			<td style="width:30px;">
				&nbsp;
			</td>
		</tr>
	</table>
	<div id="reportes_maquinaria_items_grid"></div>
	<table cellpadding="2" cellspacing="1" style="margin:20px 0px; text-align:center;">
		<tr>
			<td>
				<input type="button" id="reportes_maquinaria_export" value="Exportar"/>
			</td>
			<td>
				<input type="button" id="reportes_maquinaria_imprimir1" value="Listado"/>
			</td>
			<td>
				<input type="button" id="reportes_maquinaria_imprimir2" value="Por Maquina/Vehículo"/>
			</td>
			<td>
				<input type="button" id="reportes_maquinaria_imprimir3" value="Por Motivo"/>
			</td>
		</tr>
	</table>
</div>