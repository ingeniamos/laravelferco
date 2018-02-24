<?php
session_start();
?>
<style type="text/css">
.yellow {
	color: black;
	background-color: rgba(205,212,100,0.2);
}
.yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .yellow:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(205,212,100,0.2);
}
#presupuesto_presupuesto_spoiler
{
	width: 135px;
	height: 20px;
	padding: 5px 5px 0px 5px;
	display: block;
	font-size:13px;
	margin: 0px;
	color: #FFF;
	background-color: #75858A;
}
#presupuesto_presupuesto_spoiler:hover
{
	cursor: pointer;
}
</style>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var ComboboxLock = false;
	var SelectedRow = -1;
	var APU_Data = new Array();
	var APU_Data2 = new Array();
	var Interno = "";
	var ClienteID = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Clasificacion = "";
	var Clasificacion2 = "";
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Presupuesto_Content");
	var Body = document.getElementById("Presupuesto__Content");
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
				SystemMap("Crear Presupuesto", true);
				ReDefine();
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
	var Guardar = false;
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
				if ($data[$i]["Modulo"] == "Presupuesto" && $data[$i]["SubModulo"] == "Presupuesto" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Presupuesto" && $data[$i]["SubModulo"] == "Presupuesto" && $data[$i]["Imprimir"] == "true")
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
	
	//-------------------------------------------------------------------------------------------------//
	//------------------------------------------- KEY JUMPS -------------------------------------------//
	//-------------------------------------------------------------------------------------------------//
	$("#presupuesto_presupuesto_clasificacion").keyup(function(event) {
		if(event.which == 13)
			$("#presupuesto_presupuesto_codigo").jqxComboBox('focus');
	});
	$("#presupuesto_presupuesto_codigo").keyup(function(event) {
		if(event.which == 13)
			$("#presupuesto_presupuesto_producto").jqxComboBox('focus');
	});
	$("#presupuesto_presupuesto_producto").keyup(function(event) {
		if(event.which == 13)
		{
			$("#presupuesto_presupuesto_cantidad").jqxNumberInput('focus');
			var input = $("#presupuesto_presupuesto_cantidad input")[0];
			if ("selectionStart" in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd("character", 0);
				range.moveStart("character", 0);
				range.select();
			}
		}
	});
	$("#presupuesto_presupuesto_cantidad").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
		}
	});
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"presupuesto_presupuesto_interno1", type:"jqxComboBox"},
			{id:"presupuesto_presupuesto_fecha", type:"jqxDateTimeInput"},
			{id:"presupuesto_presupuesto_proyecto", type:""},
			{id:"presupuesto_presupuesto_cliente", type:"jqxComboBox"},
			{id:"presupuesto_presupuesto_cliente_ID", type:"jqxComboBox"},
			{id:"presupuesto_presupuesto_interno2", type:""},
			{id:"presupuesto_presupuesto_telefono", type:""},
			{id:"presupuesto_presupuesto_direccion", type:""},
			{id:"presupuesto_presupuesto_email", type:""},
			{id:"presupuesto_presupuesto_items_grid1", type:"jqxGrid"},
			{id:"presupuesto_presupuesto_items_grid2", type:"jqxGrid"},
			{id:"presupuesto_presupuesto_observaciones", type:""},
			{id:"presupuesto_presupuesto_subtotal", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_administracion1", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_administracion2", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_imprevistos1", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_imprevistos2", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_utilidades1", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_utilidades2", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_iva1", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_iva2", type:"jqxNumberInput"},
			{id:"presupuesto_presupuesto_total", type:"jqxNumberInput"},
		];
		
		EnableDisableJSON = [
			{id:"presupuesto_presupuesto_fecha", type:"jqxDateTimeInput"},
			{id:"presupuesto_presupuesto_proyecto", type:""},
			{id:"presupuesto_presupuesto_cliente", type:"jqxComboBox"},
			{id:"presupuesto_presupuesto_cliente_ID", type:"jqxComboBox"},
			{id:"presupuesto_presupuesto_add", type:"jqxButton"},
			{id:"presupuesto_presupuesto_add2", type:"jqxButton"},
			{id:"presupuesto_presupuesto_items_grid1", type:"jqxGrid"},
			{id:"presupuesto_presupuesto_items_grid2", type:"jqxGrid"},
			{id:"presupuesto_presupuesto_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	
	function LoadInitialValues()
	{
		$("#presupuesto_presupuesto_administracion1").val("6.00");
		$("#presupuesto_presupuesto_imprevistos1").val("5.00");
		$("#presupuesto_presupuesto_utilidades1").val("5.00");
		$("#presupuesto_presupuesto_iva1_check").jqxCheckBox({ checked: true });
		$("#presupuesto_presupuesto_guardar").jqxButton({ disabled: true });
	}
	
	function ClearDocument()
	{
		Locked = false;
		SelectedRow = -1;
		Clasificacion = "";
		Clasificacion2 = "";
		Interno = "";
		ClienteID = "";
		Timer1 = 0;
		Timer2 = 0;
		//---
		ClearAll();
		LoadInitialValues();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		if (!Admin && !Imprimir)
		{
			$("#presupuesto_presupuesto_imprimir1").jqxButton({ disabled: true });
			$("#presupuesto_presupuesto_imprimir2").jqxButton({ disabled: true });
			$("#presupuesto_presupuesto_imprimir3").jqxButton({ disabled: true });
			$("#presupuesto_presupuesto_imprimir4").jqxButton({ disabled: true });
		}
	}
	
	function LoadParameters()
	{
		ClasificacionSource.data = {"Presupuesto_Clasificacion":true};
		PresupuestoSource.data = {"Presupuesto":true};
		
		var PresupuestoDataAdapter = new $.jqx.dataAdapter(PresupuestoSource);
		$("#presupuesto_presupuesto_interno1").jqxComboBox({source: PresupuestoDataAdapter});
		
		var ClasificacionDataAdapter = new $.jqx.dataAdapter(ClasificacionSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#presupuesto_presupuesto_clasificacion").jqxComboBox({source: records});
				$("#presupuesto_presupuesto_clasificacion2").jqxComboBox({source: records});
			},
		});
		
		var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#presupuesto_presupuesto_cliente").jqxComboBox({source: records});
				$("#presupuesto_presupuesto_cliente_ID").jqxComboBox({source: records});
			},
		});
		
		
	}
	
	function LoadValues()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Fecha', type: 'string'},
				{ name: 'Proyecto', type: 'string'},
				{ name: 'ClienteID', type: 'string'},
				{ name: 'Notas', type: 'string'},
				{ name: 'SubTotal', type: 'decimal'},
				{ name: 'Administracion1', type: 'decimal'},
				{ name: 'Administracion2', type: 'decimal'},
				{ name: 'Imprevistos1', type: 'decimal'},
				{ name: 'Imprevistos2', type: 'decimal'},
				{ name: 'Utilidades1', type: 'decimal'},
				{ name: 'Utilidades2', type: 'decimal'},
				{ name: 'Iva1', type: 'decimal'},
				{ name: 'Iva1_Check', type: 'bool'},
				{ name: 'Iva2', type: 'decimal'},
				{ name: 'Iva2_Check', type: 'bool'},
				{ name: 'Total', type: 'decimal'},
				//
				{ name: 'Items', type: 'string'},
			],
			data:{ "Presupuesto_Cargar":$("#presupuesto_presupuesto_interno1").val() },
			url: "modulos/datos_productos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				//alert(JSON.stringify(records))
				var Item = $("#presupuesto_presupuesto_interno1").jqxComboBox('getSelectedItem');
				if (Item)
				{
					$("#presupuesto_presupuesto_interno2").val(Item.value);
					$("#presupuesto_presupuesto_guardar").jqxButton({ disabled: false });
				}
					
				
				//alert(SetFormattedDate(records[0]["Fecha"]));
				//return;
				$("#presupuesto_presupuesto_fecha").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#presupuesto_presupuesto_proyecto").val(records[0]["Proyecto"]);
				$("#presupuesto_presupuesto_cliente").jqxComboBox('selectItem', records[0]["ClienteID"]);
				$("#presupuesto_presupuesto_observaciones").val(records[0]["Notas"]);
				
				// Clear Grid
				$("#presupuesto_presupuesto_items_grid1").jqxGrid("clear");
				
				if (records[0]["Items"] == "")
					var len = 0;
				else
					var len = records[0]["Items"].length;
				
				var TmpArray = new Array();
				
				for (var i = 0; i < len; i++)
				{
					var datarow = {};
					
					datarow["Item"] = records[0]["Items"][i]["Item"];
					datarow["Codigo"] = records[0]["Items"][i]["Codigo"];
					datarow["Nombre"] = records[0]["Items"][i]["Nombre"];
					datarow["Unidad"] = records[0]["Items"][i]["Unidad"];
					datarow["Cantidad"] = parseFloat(records[0]["Items"][i]["Cantidad"]);
					datarow["Valor"] = parseFloat(records[0]["Items"][i]["Valor"]);
					datarow["Total"] = 0;
					datarow["Clasificacion"] = records[0]["Items"][i]["Clasificacion"];
					
					TmpArray[i] = datarow;
				}
				TmpArray.reverse();// ya viene con sort desde el php, pero se necesita revertirlo aca para que de el sort ASC
				$("#presupuesto_presupuesto_items_grid1").jqxGrid("addrow", null, TmpArray, "first");
				
				$("#presupuesto_presupuesto_subtotal").val(records[0]["SubTotal"]);
				$("#presupuesto_presupuesto_administracion1").val(records[0]["Administracion1"]);
				$("#presupuesto_presupuesto_administracion2").val(records[0]["Administracion2"]);
				$("#presupuesto_presupuesto_imprevistos1").val(records[0]["Imprevistos1"]);
				$("#presupuesto_presupuesto_imprevistos2").val(records[0]["Imprevistos2"]);
				$("#presupuesto_presupuesto_utilidades1").val(records[0]["Utilidades1"]);
				$("#presupuesto_presupuesto_utilidades2").val(records[0]["Utilidades2"]);
				$("#presupuesto_presupuesto_iva1").val(records[0]["Iva1"]);
				$("#presupuesto_presupuesto_iva1_check").jqxCheckBox({ checked: records[0]["Iva1_Check"] });
				$("#presupuesto_presupuesto_iva2").val(records[0]["Iva2"]);
				$("#presupuesto_presupuesto_iva2_check").jqxCheckBox({ checked: records[0]["Iva2_Check"] });
				$("#presupuesto_presupuesto_total").val(records[0]["Total"]);
				Calcular();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	}
	
	function Add_APU(Nombre)
	{
		$("#Presupuesto_APU_Agregar_Window").jqxWindow("open");
		$("#presupuesto_presupuesto_apu_add").val(Nombre);
		var CheckTimer = setInterval(function()
		{
			if (ClickOK == true)
			{
				clearInterval(CheckTimer);
				
				$("#Loading_Mess").html("Procesando Solicitud...");
				$("#Loading").show();
				$.ajax({
					dataType: "json",
					type: "POST",
					url: "modulos/guardar.php",
					data: {
						"Presupuesto_APU_Crear":true,
						"Clasificacion":$("#presupuesto_presupuesto_selected_clasificacion").val(),
						"Nombre":$("#presupuesto_presupuesto_apu_add").val(),
						"Valor":$("#presupuesto_presupuesto_val_add").val(),
					},
					success: function (data)
					{
						switch(data[0]["MESSAGE"])
						{
							case "OK":
								//Update Comboboxes
								$("#presupuesto_presupuesto_codigo").jqxComboBox("addItem", {
									label: data[0]["Codigo"], 
									value: data[0]["Codigo"]
								});
								$("#presupuesto_presupuesto_producto").jqxComboBox("addItem", {
									label: $("#presupuesto_presupuesto_apu_add").val(), 
									value: data[0]["Codigo"]
								});
								$("#presupuesto_presupuesto_codigo").jqxComboBox("selectItem", data[0]["Codigo"]);
							break;
							
							case "ERROR":
								Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
							break;
						}
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
						+"Error: "+errorThrown, 3);
					},
					complete: function ()
					{
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						Locked = false;
						//ReDefine();
						$("#Presupuesto_APU_Agregar_Window").jqxWindow("close");
					}
				});
			}
			else if (ClickCANCEL == true)
			{
				clearInterval(CheckTimer);
			}
		}, 10);
	}
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var PresupuestoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Interno', type: 'string'},
			{ name: 'Proyecto', type: 'string'},
		],
		url: "modulos/datos_productos.php",
	};
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
	};
	
	var ClasificacionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Clasificacion', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	var APUSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'Unidad', type: 'string'},
			{ name: 'Uso', type: 'decimal'},
			{ name: 'Valor', type: 'decimal'},
			{ name: 'Tipo', type: 'string'},
		],
		url: "modulos/parametros.php",
		async: false
	};
	
	$("#presupuesto_presupuesto_interno1").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Proyecto',
		valueMember: 'Interno'
	});
	$("#presupuesto_presupuesto_interno1").on("change", function (event)
	{
		if (event.args)
		{
			if (Interno == event.args.item.value)
				return;
			
			Interno = event.args.item.value;
			LoadValues();
		}
		else
		{
			Interno = "";
			$("#presupuesto_presupuesto_interno1").jqxComboBox('clearSelection');
			$("#presupuesto_presupuesto_interno2").val("");
			$("#presupuesto_presupuesto_guardar").jqxButton({ disabled: true });
		}
	});
	$("#presupuesto_presupuesto_interno1").on("bindingComplete", function (event)
	{
		if (Interno != "")
			$("#presupuesto_presupuesto_interno1").jqxComboBox("selectItem", Interno);
	});
	
	// --- SEARCH FILTER
	
	function Steps (TO)
	{
		var items = $("#presupuesto_presupuesto_interno1").jqxComboBox('getItems');
		if (!items)
			return;
		
		var len = items.length - 1;
		
		var index = $("#presupuesto_presupuesto_interno1").jqxComboBox('getSelectedIndex');
		
		if (TO == "next")
		{
			if (index >= len)
				Alerts_Box("Ha llegado al Final de la Busqueda.", 1);
			else
				$("#presupuesto_presupuesto_interno1").jqxComboBox('selectIndex',(index + 1));
		}
		else if (TO == "back")
		{
			if (index < 1)
				Alerts_Box("Ha llegado al Principio de la Busqueda.", 1);
			else
				$("#presupuesto_presupuesto_interno1").jqxComboBox('selectIndex',(index - 1));
		}
	}
	
	$("#presupuesto_presupuesto_back").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_presupuesto_back").bind('click', function ()
	{
		Steps("back")
	});
	
	$("#presupuesto_presupuesto_next").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_presupuesto_next").bind('click', function ()
	{
		Steps("next");
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Cargar_Valores ()
	{
		var ValoresSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Direccion', type: 'string'},
				{ name: 'Telefono', type: 'string'},
				{ name: 'Email', type: 'string'},
			],
			data: {"Valores":$("#presupuesto_presupuesto_cliente_ID").val()},
			url: "modulos/datos.php",
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function () {
				var records = ValoresDataAdapter.records;

				$("#presupuesto_presupuesto_direccion").val(records[0]["Direccion"]);
				$("#presupuesto_presupuesto_telefono").val(records[0]["Telefono"]);
				$("#presupuesto_presupuesto_email").val(records[0]["Email"]);
			}
		});
	};
	
	$("#presupuesto_presupuesto_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#presupuesto_presupuesto_proyecto").jqxInput({
		theme: mytheme,
		width: 385,
		height: 20,
	});
	
	$("#presupuesto_presupuesto_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 310,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Nombre',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#presupuesto_presupuesto_cliente").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_presupuesto_cliente_ID").val() != event.args.item.value)
				$("#presupuesto_presupuesto_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#presupuesto_presupuesto_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#presupuesto_presupuesto_cliente_ID").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#presupuesto_presupuesto_cliente").val();
				
				var item = $("#presupuesto_presupuesto_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_presupuesto_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#presupuesto_presupuesto_cliente_ID").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_cliente").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#presupuesto_presupuesto_cliente_ID").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#presupuesto_presupuesto_cliente_ID").on("change", function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#presupuesto_presupuesto_cliente").val() != event.args.item.value)
					$("#presupuesto_presupuesto_cliente").val(event.args.item.value);
				
				if (ClienteID == event.args.item.value)
					return;
				
				ClienteID = event.args.item.value;
				
				Cargar_Valores();
			},350);
		}
		else
		{
			var item_value = $("#presupuesto_presupuesto_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#presupuesto_presupuesto_cliente_ID").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#presupuesto_presupuesto_cliente_ID").val();
				var item = $("#presupuesto_presupuesto_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					ClienteID = "";
					$("#presupuesto_presupuesto_cliente_ID").jqxComboBox('clearSelection');
					$("#presupuesto_presupuesto_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#presupuesto_presupuesto_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#presupuesto_presupuesto_cliente_ID").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#presupuesto_presupuesto_cliente_ID").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#presupuesto_presupuesto_interno2").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_telefono").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_direccion").jqxInput({
		theme: mytheme,
		width: 200,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_email").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		disabled: true,
	});

	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Calcular()
	{
		var SubTotal = 0;
		
		var datinfo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		if (count > 0)
		{
			for (var i = 0; i < count; i++)
			{
				var currentRow = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowdata', i);
				
				if (currentRow.Codigo.indexOf("Titulo") != 0)
				{
					var Tmp = parseFloat(currentRow.Valor) * parseFloat(currentRow.Cantidad);
					SubTotal += Tmp;
					$("#presupuesto_presupuesto_items_grid1").jqxGrid('setcellvalue', i, "Total", Tmp);
				}
				else
					continue;
				
				/*var Codigo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', i, "Codigo");
				if (Codigo.indexOf("Titulo") != 0)
				{
					var Cantidad = parseFloat($("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', i, "Cantidad"));
					var Valor = parseFloat($("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', i, "Valor"));
					var Tmp = Valor * Cantidad;
					SubTotal += Tmp;
					$("#presupuesto_presupuesto_items_grid1").jqxGrid('setcellvalue', i, "Total", Tmp);
				}
				else
					continue;*/
			}
			
			$("#presupuesto_presupuesto_subtotal").val(SubTotal);
			
			var percent1 = $("#presupuesto_presupuesto_administracion1").val();
			var total_percent1 = Math.round(((SubTotal / 100) * percent1));
			var percent2 = $("#presupuesto_presupuesto_imprevistos1").val();
			var total_percent2 = Math.round(((SubTotal / 100) * percent2));
			var percent3 = $("#presupuesto_presupuesto_utilidades1").val();
			var total_percent3 = Math.round(((SubTotal / 100) * percent3));
			
			$("#presupuesto_presupuesto_administracion2").val(total_percent1);
			$("#presupuesto_presupuesto_imprevistos2").val(total_percent2);
			$("#presupuesto_presupuesto_utilidades2").val(total_percent3);
			
			var iva1 = Math.round(((total_percent3 / 100) * 16));
			var iva2 = Math.round(((SubTotal / 100) * 16));
			
			if ($("#presupuesto_presupuesto_iva1_check").jqxCheckBox('checked') == true)
			{
				$("#presupuesto_presupuesto_iva1").val(iva1);
				$("#presupuesto_presupuesto_iva2").val("0");
				$("#presupuesto_presupuesto_total").val(SubTotal + iva1 + total_percent1 + total_percent2 +total_percent3);
			}
			
			if ($("#presupuesto_presupuesto_iva2_check").jqxCheckBox('checked') == true)
			{
				$("#presupuesto_presupuesto_iva1").val("0");
				$("#presupuesto_presupuesto_iva2").val(iva2);
				$("#presupuesto_presupuesto_total").val(SubTotal + iva2 + total_percent1 + total_percent2 +total_percent3);
			}
		}
		else
		{
			$("#presupuesto_presupuesto_subtotal").val("0");
			//$("#presupuesto_presupuesto_administracion1").val("0");
			$("#presupuesto_presupuesto_administracion2").val("0");
			//$("#presupuesto_presupuesto_imprevistos1").val("0");
			$("#presupuesto_presupuesto_imprevistos2").val("0");
			//$("#presupuesto_presupuesto_utilidades1").val("0");
			$("#presupuesto_presupuesto_utilidades2").val("0");
			$("#presupuesto_presupuesto_iva1").val("0");
			$("#presupuesto_presupuesto_iva2").val("0");
			$("#presupuesto_presupuesto_total").val("0");
		}
	
		var datinfo = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		if (count > 0)
		{
			SubTotal = 0;
			for (var i = 0; i < count; i++)
			{
				var Cantidad = parseFloat($("#presupuesto_presupuesto_items_grid2").jqxGrid('getcellvalue', i, "Cantidad"));
				var Valor = parseFloat($("#presupuesto_presupuesto_items_grid2").jqxGrid('getcellvalue', i, "Valor"));
				var Tmp = Valor * Cantidad;
				SubTotal += Tmp;
				$("#presupuesto_presupuesto_items_grid2").jqxGrid('setcellvalue', i, "Total", Tmp);
			}
			var rowindex = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getselectedrowindex");
			if (rowindex >= 0)
			{
				var Codigo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', rowindex, "Codigo");
				if (Codigo.indexOf("Titulo") != 0)
				{
					var Val = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", rowindex, "Valor");
					if (Val != SubTotal)
						$("#presupuesto_presupuesto_items_grid1").jqxGrid("setcellvalue", rowindex, "Valor", SubTotal);
				}
			}
		}
	}
	
	function Add_Row1()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var Clasificacion = $("#presupuesto_presupuesto_clasificacion").jqxComboBox('getSelectedItem');
		var Item = $("#presupuesto_presupuesto_item").val();
		var Producto = $("#presupuesto_presupuesto_producto").jqxComboBox('getSelectedItem');
		var Unidad = $("#presupuesto_presupuesto_unidad").val();
		var Cantidad = $("#presupuesto_presupuesto_cantidad").val();
		var Val = $("#presupuesto_presupuesto_valor").val();
		
		if (!Clasificacion) {
			Alerts_Box("Favor Seleccionar un tipo de Clasificacion", 3);
			WaitClick_Combobox("presupuesto_presupuesto_clasificacion");
			Locked = false;
			return;
		}
		
		if (!Producto) {
			Alerts_Box("Favor Seleccionar un Producto.", 3);
			WaitClick_Combobox("presupuesto_presupuesto_producto");
			Locked = false;
			return;
		}
		
		if (Producto.value != "Titulo")
		{
			if (Cantidad < 0.001)
			{
				Alerts_Box("Debe añadir una Cantidad mayor a 0", 3);
				WaitClick_NumberInput("presupuesto_presupuesto_cantidad");
				Locked = false;
				return;
			}
			
			// Permitir Duplicados (ademas de que daba error)
			/*var datinfo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			for (var i = 0; i < count; i++)
			{
				var currentRow = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowdata', i);
				if (currentRow.Codigo == Producto.value)
				{
					var id = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowid', i);
					$("#presupuesto_presupuesto_items_grid1").jqxGrid('deleterow', id);
				}
			}*/
			
			var datarow = [{
				"Item":Item,
				"Codigo":Producto.value,
				"Nombre":Producto.label,
				"Cantidad":Cantidad,
				"Unidad":Unidad,
				"Valor":Val,
				"Total":0,
				"Clasificacion":Clasificacion.label,
			}];
		}
		else
		{
			var NumofRows = 0;
			//var Item = 0;
			var datinfo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			for (var i = 0; i < count; i++)
			{
				var currentRow = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowdata', i);
				if (currentRow.Codigo.indexOf(Producto.value) == 0)
					NumofRows++;
				
				//if ((i + 1) == count)
				//	Item = parseFloat(currentRow.Item);
			}
			//alert(Item)
			var Tmp = ""+Producto.value+NumofRows;
			
			var datarow = [{
				"Item":Item,
				"Codigo":Tmp,
				"Nombre":Producto.label,
				"Cantidad":0,
				"Unidad":"",
				"Valor":0,
				"Total":0,
				"Clasificacion":Clasificacion.label,
			}];
		}
		
		var datainfo = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		$("#presupuesto_presupuesto_items_grid1").jqxGrid("addrow", count, datarow);
		var index = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getrowboundindexbyid", count);
		$("#presupuesto_presupuesto_items_grid1").jqxGrid("ensurerowvisible", index);
		
		$("#presupuesto_presupuesto_codigo").jqxComboBox('clearSelection');
		$("#presupuesto_presupuesto_producto").jqxComboBox('clearSelection');
		$("#presupuesto_presupuesto_cantidad").val("0");
		$("#presupuesto_presupuesto_unidad").val("");
		$("#presupuesto_presupuesto_valor").val("0");
		Locked = false;
		$("#presupuesto_presupuesto_items_grid1").jqxGrid("clearselection");
		$("#presupuesto_presupuesto_items_grid2").jqxGrid("clearselection");
		$("#presupuesto_presupuesto_items_grid2").jqxGrid("clear");
		//Calcular();
	}
	
	function Update_Row()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var FinalArray = new Array;
		
		var datinfo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var Tmp = {}
			var currentRow = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowdata', i);
			Tmp["Clasificacion"] = currentRow.Clasificacion;
			Tmp["Codigo"] = currentRow.Codigo;
			FinalArray[i] = Tmp;
		}
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "modulos/datos_productos.php",
			data: {"Presupuesto_Valores":FinalArray},
			async: true,
			success: function (data)
			{
				if (data.length < 1)
					return;
				
				var datinfo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getdatainformation');
				var count = datinfo.rowscount;
				if (count > 0)
				{
					for (var i = 0; i < count; i++)
					{
						var currentRow = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowdata', i);
						//var Codigo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', i, "Codigo");
						//var Clasificacion = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', i, "Clasificacion");
						
						for (var a = 0; a < data.length; a++)
						{
							if (data[a]["Codigo"] == currentRow.Codigo && data[a]["Clasificacion"] == currentRow.Clasificacion)
							{
								//$("#presupuesto_presupuesto_items_grid1").jqxGrid('setcellvalue', i, "Nombre", data[a]["Nombre"]);
								$("#presupuesto_presupuesto_items_grid1").jqxGrid('setcellvalue', i, "Unidad", data[a]["Unidad"]);
								$("#presupuesto_presupuesto_items_grid1").jqxGrid('setcellvalue', i, "Valor", data[a]["Valor"]);
								break;
							}
						}
					}
				}
				
				Locked = false;
				return;
			},
			error: function (jqXHR, textStatus, errorThrown) {
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
			},
			complete: function ()
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
			}
		});
		
		Locked = false;
		Calcular();
	}
	
	$("#presupuesto_presupuesto_clasificacion").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Clasificacion',
		valueMember: 'Clasificacion'
	});
	$("#presupuesto_presupuesto_clasificacion").on('bindingComplete', function (event)
	{
		$("#presupuesto_presupuesto_clasificacion").jqxComboBox('addItem', {"ID":"0", "Clasificacion":"Titulo"});
	});
	$("#presupuesto_presupuesto_clasificacion").on('change', function (event)
	{
		if (event.args)
		{
			Clasificacion = event.args.item.value;
				
			if (event.args.item.value == "Titulo")
			{
				$("#presupuesto_presupuesto_codigo").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_producto").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_codigo").jqxComboBox('clear');
				$("#presupuesto_presupuesto_producto").jqxComboBox('clear');
				//--
				$("#presupuesto_presupuesto_codigo").jqxComboBox('addItem', {"Codigo":"Titulo", "Nombre":"Titulo"});
				$("#presupuesto_presupuesto_producto").jqxComboBox('addItem', {"Codigo":"Titulo", "Nombre":"Titulo de Proyecto"});
				$("#presupuesto_presupuesto_codigo").jqxComboBox('selectItem', "Titulo");
				return;
			}
				
			APUSource.data = {
				"Presupuesto_APU":true,
				"Clasificacion":event.args.item.value,
				"APU_Only":true,
			};
			var APUDataAdapter = new $.jqx.dataAdapter(APUSource,
			{
				autoBind: true,
				loadComplete: function (records)
				{
					APU_Data = records;
				},
				loadError: function (jqXHR, status, error)
				{
					APU_Data = new Array();
				},
			});
			$("#presupuesto_presupuesto_codigo").jqxComboBox({source: APU_Data});
			$("#presupuesto_presupuesto_producto").jqxComboBox({source: APU_Data});
		}
		else
		{
			Clasificacion = "";
			$("#presupuesto_presupuesto_codigo").jqxComboBox('clearSelection');
			$("#presupuesto_presupuesto_producto").jqxComboBox('clearSelection');
			$("#presupuesto_presupuesto_codigo").jqxComboBox('clear');
			$("#presupuesto_presupuesto_producto").jqxComboBox('clear');
			$("#presupuesto_presupuesto_cantidad").val("0");
			$("#presupuesto_presupuesto_unidad").val("");
			$("#presupuesto_presupuesto_valor").val("0");
			$("#presupuesto_presupuesto_codigo").jqxComboBox('addItem', {"Codigo":"Titulo", "Nombre":"Titulo"});
			$("#presupuesto_presupuesto_producto").jqxComboBox('addItem', {"Codigo":"Titulo", "Nombre":"Titulo de Proyecto"});
			$("#presupuesto_presupuesto_codigo").jqxComboBox('selectItem', "Titulo");
		}
	});
	$("#presupuesto_presupuesto_clasificacion").on("bindingComplete", function (event)
	{
		if (Clasificacion != "")
			$("#presupuesto_presupuesto_clasificacion").jqxComboBox("selectItem", Clasificacion);
	});
	
	$("#presupuesto_presupuesto_item").jqxInput({
		theme: mytheme,
		width: 50,
		height: 20,
		placeHolder: "Item"
	});
	
	$("#presupuesto_presupuesto_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#presupuesto_presupuesto_codigo").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_presupuesto_producto").val() != event.args.item.value)
			{
				if (event.args.item.value == "Titulo")
				{
					$("#presupuesto_presupuesto_producto").jqxComboBox('selectItem', "Titulo");
					$("#presupuesto_presupuesto_uso").val("0");
					$("#presupuesto_presupuesto_unidad").val("");
					$("#presupuesto_presupuesto_valor").val("");
					return;
				}
				else
					$("#presupuesto_presupuesto_producto").jqxComboBox('selectItem', event.args.item.value);
				ComboboxLock = true;
				$("#presupuesto_presupuesto_producto").jqxComboBox("close");
			}
			
			var len = APU_Data.length;
			for (i = 0; i < len; i++)
			{
				if (APU_Data[i]["Codigo"] == event.args.item.value)
				{
					$("#presupuesto_presupuesto_uso").val(APU_Data[i]["Uso"]);
					$("#presupuesto_presupuesto_unidad").val(APU_Data[i]["Unidad"]);
					$("#presupuesto_presupuesto_valor").val(APU_Data[i]["Valor"]);
					break;
				}
			}
		}
		else
		{
			var item_value = $("#presupuesto_presupuesto_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				if (ComboboxLock == true)
				{
					ComboboxLock = false;
					return;
				}
				
				$("#presupuesto_presupuesto_codigo").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_producto").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_cantidad").val("0");
				$("#presupuesto_presupuesto_unidad").val("");
				$("#presupuesto_presupuesto_valor").val("0");
			}
			else
			{
				var value = $("#presupuesto_presupuesto_codigo").val();
				var item = $("#presupuesto_presupuesto_codigo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#presupuesto_presupuesto_codigo").jqxComboBox('clearSelection');
					$("#presupuesto_presupuesto_producto").jqxComboBox('clearSelection');
					$("#presupuesto_presupuesto_cantidad").val("0");
					$("#presupuesto_presupuesto_unidad").val("");
					$("#presupuesto_presupuesto_valor").val("0");
				}
				else
				{
					$("#presupuesto_presupuesto_codigo").jqxComboBox('selectItem', item.value);
				
					var len = APU_Data.length;
					for (i = 0; i < len; i++)
					{
						if (APU_Data[i]["Codigo"] == item.value)
						{
							$("#presupuesto_presupuesto_uso").val(APU_Data[i]["Uso"]);
							$("#presupuesto_presupuesto_unidad").val(APU_Data[i]["Unidad"]);
							$("#presupuesto_presupuesto_valor").val(APU_Data[i]["Valor"]);
							break;
						}
					}
				}
			}
		}
	});
	
	$("#presupuesto_presupuesto_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#presupuesto_presupuesto_producto").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_presupuesto_codigo").val() != event.args.item.value)
				$("#presupuesto_presupuesto_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var value = $("#presupuesto_presupuesto_producto input")[0].value;
			var item_value = $("#presupuesto_presupuesto_producto").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#presupuesto_presupuesto_codigo").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_producto").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_uso").val("0");
				$("#presupuesto_presupuesto_unidad").val("");
				$("#presupuesto_presupuesto_valor").val("");
				
				Alerts_Box("El valor ingresado no existe. ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						Add_APU(value);
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
			else
			{
				//var value = $("#presupuesto_presupuesto_producto").val();
				var item = $("#presupuesto_presupuesto_producto").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_presupuesto_producto").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#presupuesto_presupuesto_codigo").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_producto").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_uso").val("0");
				$("#presupuesto_presupuesto_unidad").val("");
				$("#presupuesto_presupuesto_valor").val("");
				
				Alerts_Box("El valor ingresado no existe. ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						Add_APU(value);
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
		}
	});
	
	$("#presupuesto_presupuesto_cantidad").jqxNumberInput({
		theme: mytheme,
		width: 80,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		decimalDigits: 3,
		digits: 12,
		max: 999999999999,
	});
	
	$("#presupuesto_presupuesto_unidad").jqxInput({
		theme: mytheme,
		width: 40,
		height: 20,
		disabled: true
	});
	
	$("#presupuesto_presupuesto_valor").jqxNumberInput({
		theme: mytheme,
		width: 130,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_add").jqxButton({
		width: 70,
		height: 25,
		template: "success"
	});
	$("#presupuesto_presupuesto_add").bind('click', function ()
	{
		Add_Row1();
	});
	
	$("#presupuesto_presupuesto_update").jqxButton({
		width: 95,
		height: 25,
		template: "primary"
	});
	$("#presupuesto_presupuesto_update").bind('click', function ()
	{
		Update_Row();
	});
	
	var PresupuestoGridClass = function (row, columnfield, value)
	{
		var Clasificacion = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', row, "Clasificacion");
		
		if (Clasificacion == "Titulo")
		{
			return 'yellow';
		}
		else
			return '';
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Item', type: 'string' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Unidad', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Total', type: 'decimal' },
			{ name: 'Clasificacion', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit)
		{
			commit(true);
			Calcular();
		},
		updaterow: function (rowid, rowdata, commit)
		{
			commit(true);
			//Calcular();
		},
		deleterow: function (rowid, commit)
		{
			commit(true);
			Calcular();
			$("#presupuesto_presupuesto_items_grid2").jqxGrid("clearselection");
			$("#presupuesto_presupuesto_items_grid2").jqxGrid("clear");
		}
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#presupuesto_presupuesto_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		source: GridDataAdapter,
		enabletooltips: true,
		enablebrowserselection: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		pageable: true,
		showfilterrow: true,
		filterable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				width: '3%',
				height: 20,
				columntype: 'button',
				filterable: false,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getdatainformation').rowscount;
					//if (selectedrowindex >= 0 && selectedrowindex < rowscount) { // Bug with RowFilter
					if (selectedrowindex >= 0) {
						var id = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#presupuesto_presupuesto_items_grid1").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Item', datafield: 'Item', editable: true, width: '8%', height: 20, cellsalign: 'left', cellclassname: PresupuestoGridClass },
			{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '8%', height: 20, cellclassname: PresupuestoGridClass },
			{ text: 'Nombre', datafield: 'Nombre', editable: true, width: '32%', height: 20, cellclassname: PresupuestoGridClass },
			{ text: 'Unidad', datafield: 'Unidad', editable: false, width: '6%', height: 20, cellsalign: 'center', cellclassname: PresupuestoGridClass },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '10%',
				height: 20,
				cellclassname: PresupuestoGridClass,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value)
				{
					var rowdata = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getrowdata", cell.row);
					if (rowdata.Codigo.indexOf("Titulo") != 0)
					{
						if (value < 0.001)
							return { result: false, message: "La Cantidad debe ser mayor a 0!" };
						return true;
					}
					else
						return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 3, digits: 18, spinButtons: false });
				}
			},
			{
				text: 'Valor Unitario',
				datafield: 'Valor',
				editable: false,
				filterable: false,
				width: '15%',
				height: 20,
				cellclassname: PresupuestoGridClass,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18, spinButtons: false });
				}
			},
			{
				text: 'Valor Parcial',
				datafield: 'Total',
				editable: false,
				filterable: false,
				width: '18%',
				height: 20,
				cellclassname: PresupuestoGridClass,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
			},
			{ text: '', datafield: 'Clasificacion', editable: false, width: '0%', height: 20 },
		],
	});
	//$("#presupuesto_presupuesto_items_grid1").jqxGrid('hidecolumn', 'Codigo');
	$("#presupuesto_presupuesto_items_grid1").jqxGrid('hidecolumn', 'Clasificacion');
	$("#presupuesto_presupuesto_items_grid1").jqxGrid('localizestrings', localizationobj);
	$("#presupuesto_presupuesto_items_grid1").on("rowunselect", function (event) 
	{
		var args = event.args;
		var rowBoundIndex = args.rowindex;
		var rowData = args.row;
		
		if (SelectedRow >= 0)
			ShowItems(SelectedRow);
	});
	$("#presupuesto_presupuesto_items_grid1").on("rowselect", function (event)
	{
		var args = event.args;
		var rowBoundIndex = args.rowindex;
		var rowData = args.row;
		SelectedRow = rowBoundIndex;
	});
	$("#presupuesto_presupuesto_items_grid1").on("cellvaluechanged", function (event) 
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Item")
		{
			var Items = new Array();
			var Item = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', rowBoundIndex, "Item");
			var Codigo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getcellvalue', rowBoundIndex, "Codigo");
			var datinfo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			if (count < 1)
				return;
			
			if (value == "")
				return;
			
			for (var i = 0; i < count; i++)
			{
				var tmp_array = {};
				var currentRow = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowdata', i);
				
				if (currentRow.Codigo == Codigo && currentRow.Item == Item)
					tmp_array["Item"] = value;
				else
					tmp_array["Item"] = currentRow.Item;
					
				tmp_array["Codigo"] = currentRow.Codigo;
				tmp_array["Nombre"] = currentRow.Nombre;
				tmp_array["Unidad"] = currentRow.Unidad;
				tmp_array["Cantidad"] = currentRow.Cantidad;
				tmp_array["Valor"] = currentRow.Valor;
				tmp_array["Total"] = currentRow.Total;
				tmp_array["Clasificacion"] = currentRow.Clasificacion;
				
				Items[i] = tmp_array;
			}
			
			Items.sort(function(a, b)
			{
				if (a.Item > b.Item)
					return -1;
				else if (a.Item < b.Item)
					return 1;
				else
					return 0;
			});
			
			$("#presupuesto_presupuesto_items_grid1").jqxGrid("clearSelection");
			$("#presupuesto_presupuesto_items_grid1").jqxGrid("clear");
			$("#presupuesto_presupuesto_items_grid1").jqxGrid("addrow", null, Items, "first");
			$("#presupuesto_presupuesto_items_grid2").jqxGrid("clearSelection");
			$("#presupuesto_presupuesto_items_grid2").jqxGrid("clear");
			//Calcular();
		}
		else if (datafield == "Cantidad" || datafield == "Valor")
			Calcular();
	});
	
	function ShowItems(Index)
	{
		var Codigo = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", Index, "Codigo");
		var Clasificacion = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", Index, "Clasificacion");
		
		if (Codigo.indexOf("Titulo") != 0)
		{
			GridSource2.data = {
				"APU_Items":true,
				"Codigo":Codigo,
				"Clasificacion":Clasificacion,
			};
			var GridDataAdapter2 = new $.jqx.dataAdapter(GridSource2);
			$("#presupuesto_presupuesto_items_grid2").jqxGrid({source: GridDataAdapter2});
		}
		else
		{
			$("#presupuesto_presupuesto_items_grid2").jqxGrid("clear");
		}
	}
	
	$("#presupuesto_presupuesto_spoiler").click(function()
	{
		var Element = document.getElementById("presupuesto_presupuesto_items_grid2");
		if (jQuery.css( Element, "display" ) === "none")
		{
			$("#presupuesto_presupuesto_spoiler").html("Cerrar Detalles APU");
			$("#presupuesto_presupuesto_items_grid2_table").show();
			$("#presupuesto_presupuesto_items_grid2").show();
		}
		else
		{
			$("#presupuesto_presupuesto_spoiler").html("Ver Detalles APU");
			$("#presupuesto_presupuesto_items_grid2_table").hide();
			$("#presupuesto_presupuesto_items_grid2").hide();
		}	
	});
	
	function Add_Row2()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var Clasificacion = $("#presupuesto_presupuesto_clasificacion2").jqxComboBox('getSelectedItem');
		var Producto = $("#presupuesto_presupuesto_producto2").jqxComboBox('getSelectedItem');
		var Unidad = $("#presupuesto_presupuesto_unidad2").val();
		var Cantidad = $("#presupuesto_presupuesto_cantidad2").val();
		var Val = $("#presupuesto_presupuesto_valor2").val();
		var Uso = $("#presupuesto_presupuesto_uso2").val();
		
		if (!Clasificacion) {
			Alerts_Box("Favor Seleccionar un tipo de Clasificacion", 3);
			WaitClick_Combobox("presupuesto_presupuesto_clasificacion2");
			Locked = false;
			return;
		}
		
		if (!Producto) {
			Alerts_Box("Favor Seleccionar un Producto.", 3);
			WaitClick_Combobox("presupuesto_presupuesto_producto2");
			Locked = false;
			return;
		}
		
		if (Cantidad < 0.001)
		{
			Alerts_Box("Debe añadir una Cantidad mayor a 0", 3);
			WaitClick_NumberInput("presupuesto_presupuesto_cantidad2");
			Locked = false;
			return;
		}
		
		var len = APU_Data2.length;
		for (var i = 0; i < len; i++)
		{
			if (APU_Data2[i]["Codigo"] == Producto.value)
			{
				var Tipo = APU_Data2[i]["Tipo"];
				break;
			}
		}
		
		var datinfo = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getrowdata', i);
			if (currentRow.Codigo == Producto.value)
			{
				var id = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getrowid', i);
				$("#presupuesto_presupuesto_items_grid2").jqxGrid('deleterow', id);
				break;
			}
		}
		
		var datarow = [{
			"Codigo":Producto.value,
			"Nombre":Producto.label,
			"Cantidad":Cantidad,
			"Unidad":Unidad,
			"Uso":Uso,
			"Valor":Val,
			"Total":0,
			"Tipo":Tipo,
			"Clasificacion":Clasificacion.label,
		}];
		
		$("#presupuesto_presupuesto_items_grid2").jqxGrid("addrow", null, datarow, "first");
		$("#presupuesto_presupuesto_codigo2").jqxComboBox('clearSelection');
		$("#presupuesto_presupuesto_producto2").jqxComboBox('clearSelection');
		$("#presupuesto_presupuesto_cantidad2").val("0");
		$("#presupuesto_presupuesto_unidad2").val("");
		$("#presupuesto_presupuesto_valor2").val("0");
		$("#presupuesto_presupuesto_uso2").val("0");
		Locked = false;
		Calcular();
	}
	
	$("#presupuesto_presupuesto_clasificacion2").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Clasificacion',
		valueMember: 'Clasificacion'
	});
	$("#presupuesto_presupuesto_clasificacion2").bind('change', function (event)
	{
		if (event.args)
		{
			APUSource.data = {
				"Presupuesto_APU":true,
				"Clasificacion":$("#presupuesto_presupuesto_clasificacion2").val(),
			};
			var APUDataAdapter2 = new $.jqx.dataAdapter(APUSource,
			{
				autoBind: true,
				loadComplete: function (records)
				{
					APU_Data2 = records;
				},
				loadError: function (jqXHR, status, error)
				{
					APU_Data2 = new Array();
				},
			});
			$("#presupuesto_presupuesto_codigo2").jqxComboBox({source: APU_Data2});
			$("#presupuesto_presupuesto_producto2").jqxComboBox({source: APU_Data2});
		}
		else
		{
			$("#presupuesto_presupuesto_codigo2").jqxComboBox('clearSelection');
			$("#presupuesto_presupuesto_producto2").jqxComboBox('clearSelection');
			$("#presupuesto_presupuesto_codigo2").jqxComboBox('clear');
			$("#presupuesto_presupuesto_producto2").jqxComboBox('clear');
			$("#presupuesto_presupuesto_cantidad2").val("0");
			$("#presupuesto_presupuesto_unidad2").val("");
			$("#presupuesto_presupuesto_valor2").val("0");
			$("#presupuesto_presupuesto_uso2").val("0");
		}
	});
	
	$("#presupuesto_presupuesto_codigo2").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#presupuesto_presupuesto_codigo2").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_presupuesto_producto2").val() != event.args.item.value)
			{
				if (event.args.item.value == "Titulo")
				{
					$("#presupuesto_presupuesto_producto2").jqxComboBox('selectItem', "Titulo");
					$("#presupuesto_presupuesto_uso2").val("0");
					$("#presupuesto_presupuesto_unidad2").val("");
					$("#presupuesto_presupuesto_valor2").val("");
					return;
				}
				else
					$("#presupuesto_presupuesto_producto2").jqxComboBox('selectItem', event.args.item.value);
				ComboboxLock = true;
				$("#presupuesto_presupuesto_producto2").jqxComboBox("close");
			}
			
			var len = APU_Data2.length;
			for (i = 0; i < len; i++)
			{
				if (APU_Data2[i]["Codigo"] == event.args.item.value)
				{
					$("#presupuesto_presupuesto_unidad2").val(APU_Data2[i]["Unidad"]);
					$("#presupuesto_presupuesto_valor2").val(APU_Data2[i]["Valor"]);
					$("#presupuesto_presupuesto_uso2").val(APU_Data2[i]["Uso"]);
					break;
				}
			}
		}
		else
		{
			var item_value = $("#presupuesto_presupuesto_codigo2").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				if (ComboboxLock == true)
				{
					ComboboxLock = false;
					return;
				}
				
				$("#presupuesto_presupuesto_codigo2").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_producto2").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_cantidad2").val("0");
				$("#presupuesto_presupuesto_unidad2").val("");
				$("#presupuesto_presupuesto_valor2").val("0");
				$("#presupuesto_presupuesto_uso2").val("0");
			}
			else
			{
				var value = $("#presupuesto_presupuesto_codigo2").val();
				var item = $("#presupuesto_presupuesto_codigo2").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#presupuesto_presupuesto_codigo2").jqxComboBox('clearSelection');
					$("#presupuesto_presupuesto_producto2").jqxComboBox('clearSelection');
					$("#presupuesto_presupuesto_cantidad2").val("0");
					$("#presupuesto_presupuesto_unidad2").val("");
					$("#presupuesto_presupuesto_valor2").val("0");
					$("#presupuesto_presupuesto_uso2").val("0");
				}
				else
				{
					$("#presupuesto_presupuesto_codigo2").jqxComboBox('selectItem', item.value);
				
					var len = APU_Data2.length;
					for (i = 0; i < len; i++)
					{
						if (APU_Data2[i]["Codigo"] == item.value)
						{
							$("#presupuesto_presupuesto_unidad2").val(APU_Data2[i]["Unidad"]);
							$("#presupuesto_presupuesto_valor2").val(APU_Data2[i]["Valor"]);
							$("#presupuesto_presupuesto_uso2").val(APU_Data2[i]["Uso"]);
							break;
						}
					}
				}
			}
		}
	});
	
	$("#presupuesto_presupuesto_producto2").jqxComboBox(
	{
		theme: mytheme,
		width: 280,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#presupuesto_presupuesto_producto2").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_presupuesto_codigo2").val() != event.args.item.value)
				$("#presupuesto_presupuesto_codigo2").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#presupuesto_presupuesto_producto2").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#presupuesto_presupuesto_codigo2").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_producto2").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_cantidad2").val("0");
				$("#presupuesto_presupuesto_unidad2").val("");
				$("#presupuesto_presupuesto_valor2").val("");
				$("#presupuesto_presupuesto_uso2").val("0");
			}
			else
			{
				var value = $("#presupuesto_presupuesto_producto2").val();
				
				var item = $("#presupuesto_presupuesto_producto2").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_presupuesto_producto2").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#presupuesto_presupuesto_codigo2").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_producto2").jqxComboBox('clearSelection');
				$("#presupuesto_presupuesto_cantidad2").val("0");
				$("#presupuesto_presupuesto_unidad2").val("");
				$("#presupuesto_presupuesto_valor2").val("");
				$("#presupuesto_presupuesto_uso2").val("0");
			}
		}
	});
	
	$("#presupuesto_presupuesto_cantidad2").jqxNumberInput({
		theme: mytheme,
		width: 100,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		decimalDigits: 3,
		digits: 12,
		max: 999999999999,
	});
	
	$("#presupuesto_presupuesto_unidad2").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#presupuesto_presupuesto_valor2").jqxNumberInput({
		theme: mytheme,
		width: 135,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_uso2").jqxNumberInput({
		theme: mytheme,
		width: 50,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '%',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 3,
		max: 999,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_add2").jqxButton({
		width: 70,
		height: 25,
		template: "success"
	});
	$("#presupuesto_presupuesto_add2").bind('click', function ()
	{
		Add_Row2();
	});
	
	var GridSource2 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Unidad', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Uso', type: 'decimal' },
			{ name: 'Total', type: 'decimal' },
			{ name: 'Tipo', type: 'string' },
			{ name: 'Clasificacion', type: 'string' },
		],
		url: "modulos/datos_productos.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			var rowindex = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getselectedrowindex");
			if (rowindex < 0)
			{
				Alerts_Box("Debe Seleccionar un APU.", 3);
				WaitClick();
				commit(false);
				return;
			}
			var APU_Codigo = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", rowindex, "Codigo");
			var APU_Clasificacion = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", rowindex, "Clasificacion");
			
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {
					"Presupuesto_APU_Agregar":true,
					"APU_Codigo":APU_Codigo,
					"APU_Clasificacion":APU_Clasificacion,
					"Codigo":rowdata[0]["Codigo"],
					"Nombre":rowdata[0]["Nombre"],
					"Unidad":rowdata[0]["Unidad"],
					"Cantidad":rowdata[0]["Cantidad"],
					"Valor":rowdata[0]["Valor"],
					"Uso":rowdata[0]["Uso"],
					"Tipo":rowdata[0]["Tipo"],
					"Clasificacion":rowdata[0]["Clasificacion"],
				},
				success: function (data, status, xhr)
				{
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							commit(true);
							Calcular();
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
		},
		updaterow: function (rowid, rowdata, commit)
		{
			var rowindex = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getselectedrowindex");
			if (rowindex < 0)
			{
				//Alerts_Box("Debe Seleccionar un APU.", 3);
				//WaitClick();
				commit(false);
				return;
			}
			var APU_Codigo = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", rowindex, "Codigo");
			var APU_Clasificacion = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", rowindex, "Clasificacion");
			
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {
					"Presupuesto_APU_Modificar":true,
					"APU_Codigo":APU_Codigo,
					"APU_Clasificacion":APU_Clasificacion,
					"Codigo":rowdata.Codigo,
					"Nombre":rowdata.Nombre,
					"Unidad":rowdata.Unidad,
					"Cantidad":rowdata.Cantidad,
					"Valor":rowdata.Valor,
					"Uso":rowdata.Uso,
					"Total":rowdata.Total,
					"Tipo":rowdata.Tipo,
					"Clasificacion":rowdata.Clasificacion,
				},
				success: function (data, status, xhr)
				{
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							commit(true);
							Calcular();
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
		},
		deleterow: function (rowid, commit)
		{
			ClickOK = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var rowindex = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getselectedrowindex");
					if (rowindex < 0)
					{
						Alerts_Box("Debe Seleccionar un APU.", 3);
						WaitClick();
						commit(false);
						return;
					}
					var APU_Codigo = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", rowindex, "Codigo");
					var APU_Clasificacion = $("#presupuesto_presupuesto_items_grid1").jqxGrid("getcellvalue", rowindex, "Clasificacion");
					
					var rowBoundIndex = $("#presupuesto_presupuesto_items_grid2").jqxGrid("getrowboundindexbyid", rowid);
					var Codigo = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getcellvalue', rowBoundIndex, "Codigo");
					var Clasificacion = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getcellvalue', rowBoundIndex, "Clasificacion");
					
					$.ajax({
						dataType: "json",
						type: "POST",
						url: "modulos/guardar.php",
						data: {
							"Presupuesto_APU_Borrar":true,
							"APU_Codigo":APU_Codigo,
							"APU_Clasificacion":APU_Clasificacion,
							"Codigo":Codigo,
							"Clasificacion":Clasificacion,
						},
						success: function (data, status, xhr)
						{
							switch(data[0]["MESSAGE"])
							{
								case "OK":
									commit(true);
									Calcular();
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
	};
	//var GridDataAdapter2 = new $.jqx.dataAdapter(GridSource2);
	
	$("#presupuesto_presupuesto_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 150,
		//source: GridDataAdapter2,
		enabletooltips: true,
		enablebrowserselection: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		showstatusbar: true,
		statusbarheight: 25,
		showaggregates: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '3%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount)
					{
						var id = $("#presupuesto_presupuesto_items_grid2").jqxGrid('getrowid', selectedrowindex);
						var Codigo = $("#presupuesto_presupuesto_items_grid2").jqxGrid("getcellvalue", id, "Codigo");
						if (Codigo == "Desp" || Codigo == "Herr")
							return;
						else
							$("#presupuesto_presupuesto_items_grid2").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '10%', height: 20 },
			{ text: 'Nombre', datafield: 'Nombre', editable: false, width: '30%', height: 20 },
			{ text: 'Unidad', datafield: 'Unidad', editable: false, width: '6%', height: 20, cellsalign: 'center' },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 3, digits: 18, spinButtons: false });
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					var Codigo = $("#presupuesto_presupuesto_items_grid2").jqxGrid("getcellvalue", row, "Codigo");
					if (Codigo == "Desp" || Codigo == "Herr")
					{
						return oldvalue;
					}
					else
						return newvalue;
				}
			},
			{
				text: 'Valor Unitario',
				datafield: 'Valor',
				editable: true,
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18, spinButtons: false });
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					var Codigo = $("#presupuesto_presupuesto_items_grid2").jqxGrid("getcellvalue", row, "Codigo");
					if (Codigo == "Desp" || Codigo == "Herr")
					{
						return oldvalue;
					}
					else
						return newvalue;
				}
			},
			{
				text: 'Uso',
				datafield: 'Uso',
				editable: true,
				width: '8%',
				height: 20,
				cellsalign: 'center',
				cellsformat: 'p2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, spinButtons: false });
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					var Codigo = $("#presupuesto_presupuesto_items_grid2").jqxGrid("getcellvalue", row, "Codigo");
					if (Codigo == "Desp" || Codigo == "Herr")
					{
						return oldvalue;
					}
					else
						return newvalue;
				}
			},
			{
				text: 'Valor Parcial',
				datafield: 'Total',
				editable: false,
				width: '18%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				aggregates: [{ '<b>Total</b>': 
					function (aggregatedValue, currentValue, column, record)
					{
						return aggregatedValue + currentValue;
					}
				}]
			},
			{ text: '', datafield: 'Tipo', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'Clasificacion', editable: false, width: '0%', height: 20 },
		],
	});
	$("#presupuesto_presupuesto_items_grid2").jqxGrid('hidecolumn', 'Tipo');
	$("#presupuesto_presupuesto_items_grid2").jqxGrid('hidecolumn', 'Clasificacion');
	$("#presupuesto_presupuesto_items_grid2").jqxGrid('localizestrings', localizationobj);
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 4 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function SaveData(Crear)
	{
		if (Locked)
			return;
		
		Locked = true;
		var FinalArray = new Array();
		var Items = new Array();
		
		var Cliente = $("#presupuesto_presupuesto_cliente_ID").jqxComboBox('getSelectedItem');
		if (!Cliente)
		{
			Alerts_Box("Debe Seleccionar un Cliente.", 3);
			WaitClick_Combobox("presupuesto_presupuesto_cliente");
			Locked = false;
			return;
		}
		
		if ($("#presupuesto_presupuesto_proyecto").val() == "")
		{
			Alerts_Box("Debe Ingresar un Nombre de Proyecto.", 3);
			WaitClick_Input("presupuesto_presupuesto_proyecto");
			Locked = false;
			return;
		}
		
		var datinfo = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		if (count < 1)
		{
			Alerts_Box("Debe Ingresar al Menos un Producto.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (var i = 0; i < count; i++)
		{
			var tmp_array = {};
			var currentRow = $("#presupuesto_presupuesto_items_grid1").jqxGrid('getrowdata', i);
			
			tmp_array["Item"] = currentRow.Item;
			tmp_array["Codigo"] = currentRow.Codigo;
			tmp_array["Nombre"] = currentRow.Nombre;
			//tmp_array["Unidad"] = currentRow.Unidad;
			tmp_array["Cantidad"] = currentRow.Cantidad;
			tmp_array["Valor"] = currentRow.Valor;
			tmp_array["Clasificacion"] = currentRow.Clasificacion;
			
			Items[i] = tmp_array;
		}
		
		if ($("presupuesto_presupuesto_total").val() < 1 || $("#presupuesto_presupuesto_total").val() == "")
		{
			Alerts_Box("Ocurrio un Extraño Error... Intente Actualizar la pagina.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		var myarray = {};
		
		if (!Crear)
			myarray["Actualizar"] = true;
		
		myarray["Interno"] = $("#presupuesto_presupuesto_interno2").val();
		myarray["Fecha"] = GetFormattedDate($("#presupuesto_presupuesto_fecha").jqxDateTimeInput('getDate'));
		myarray["Proyecto"] = $("#presupuesto_presupuesto_proyecto").val();
		myarray["ClienteID"] = $("#presupuesto_presupuesto_cliente_ID").val();
		myarray["Items"] = Items;
		myarray["Notas"] = $("#presupuesto_presupuesto_observaciones").val();
		myarray["SubTotal"] = $("#presupuesto_presupuesto_subtotal").val();
		myarray["Administracion1"] = $("#presupuesto_presupuesto_administracion1").val();
		myarray["Administracion2"] = $("#presupuesto_presupuesto_administracion2").val();
		myarray["Imprevistos1"] = $("#presupuesto_presupuesto_imprevistos1").val();
		myarray["Imprevistos2"] = $("#presupuesto_presupuesto_imprevistos2").val();
		myarray["Utilidades1"] = $("#presupuesto_presupuesto_utilidades1").val();
		myarray["Utilidades2"] = $("#presupuesto_presupuesto_utilidades2").val();
		myarray["Iva1"] = $("#presupuesto_presupuesto_iva1").val();
		myarray["Iva1_Check"] = $("#presupuesto_presupuesto_iva1_check").jqxCheckBox('checked');
		myarray["Iva2"] = $("#presupuesto_presupuesto_iva2").val();
		myarray["Iva2_Check"] = $("#presupuesto_presupuesto_iva2_check").jqxCheckBox('checked');
		myarray["Total"] = $("#presupuesto_presupuesto_total").val();
		
		FinalArray[0] = myarray;
		
		/*alert(JSON.stringify(FinalArray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "modulos/guardar.php",
			data: {"Presupuesto_Crear":FinalArray},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				ReDefine();
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						Alerts_Box("Datos Guardados con Exito!", 2);
					break;
					
					case "EXIST":
						Alerts_Box("El Codigo ya existe en esta base de datos.<br />Favor cambiar el codigo para continuar.", 3);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un Error al validar la Información...<br />Favor Contactar al Soporte Tecnico.", 3);
					break;
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	}
	
	function DeleteData()// Agregar confirmacion de borrado!
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var TmpInterno = $("#presupuesto_presupuesto_interno2").val();
		if (TmpInterno == "")
		{
			Alerts_Box("Debe Seleccionar un Presupuesto.", 3);
			WaitClick_Combobox("presupuesto_presupuesto_interno1");
			Locked = false;
			return;
		}
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "modulos/guardar.php",
			data: {
				"Presupuesto_Borrar":true,
				"Interno":TmpInterno,
			},
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				ReDefine();
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						Alerts_Box("Presupuesto Eliminado.", 2);
						ClearDocument();
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un Error al validar la Información...<br />Favor Contactar al Soporte Tecnico.", 3);
					break;
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	}
	
	$("#presupuesto_presupuesto_subtotal").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999.99,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_administracion1").jqxNumberInput({
		theme: mytheme,
		width: 50,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '%',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 3,
		max: 999.99,
	});
	$("#presupuesto_presupuesto_administracion1").on("change", function (event) {
		Calcular();
	});
	
	$("#presupuesto_presupuesto_administracion2").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999.99,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_imprevistos1").jqxNumberInput({
		theme: mytheme,
		width: 50,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '%',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 3,
		max: 999.99,
	});
	$("#presupuesto_presupuesto_imprevistos1").on("change", function (event) {
		Calcular();
	});
	
	$("#presupuesto_presupuesto_imprevistos2").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999.99,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_utilidades1").jqxNumberInput({
		theme: mytheme,
		width: 50,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '%',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 3,
		max: 999.99,
	});
	$("#presupuesto_presupuesto_utilidades1").on("change", function (event) {
		Calcular();
	});

	$("#presupuesto_presupuesto_utilidades2").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999.99,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_iva1").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999.99,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_iva1_check").jqxCheckBox({
		theme: mytheme,
		boxSize: 18,
	});
	$("#presupuesto_presupuesto_iva1_check").bind('change', function (event)
	{
		if (event.args.checked == false)
			$("#presupuesto_presupuesto_iva2_check").jqxCheckBox({ checked: true });
		else
			$("#presupuesto_presupuesto_iva2_check").jqxCheckBox({ checked: false });
		
		Calcular();
	});
	
	$("#presupuesto_presupuesto_iva2").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999.99,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_iva2_check").jqxCheckBox({
		theme: mytheme,
		boxSize: 18,
	});
	$("#presupuesto_presupuesto_iva2_check").bind('change', function (event)
	{
		if (event.args.checked == false)
			$("#presupuesto_presupuesto_iva1_check").jqxCheckBox({ checked: true });
		else
			$("#presupuesto_presupuesto_iva1_check").jqxCheckBox({ checked: false });
		
		Calcular();
	});
	
	$("#presupuesto_presupuesto_total").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999.99,
		disabled: true,
	});
	
	$("#presupuesto_presupuesto_guardar").jqxButton({
		width: 120,
		height: 25,
		template: "info"
	});
	$("#presupuesto_presupuesto_guardar").bind('click', function ()
	{
		SaveData();
	});
	
	$("#presupuesto_presupuesto_crear").jqxButton({
		width: 120,
		height: 25,
		template: "primary"
	});
	$("#presupuesto_presupuesto_crear").bind('click', function ()
	{
		SaveData(true);
	});
	
	$("#presupuesto_presupuesto_eliminar").jqxButton({
		width: 120,
		height: 25,
		template: "danger"
	});
	$("#presupuesto_presupuesto_eliminar").bind('click', function ()
	{
		DeleteData();
	});
	
	$("#presupuesto_presupuesto_nuevo").jqxButton({
		width: 120,
		height: 25,
		template: "success"
	});
	$("#presupuesto_presupuesto_nuevo").bind('click', function ()
	{
		ClearDocument();
	});
	
	$("#presupuesto_presupuesto_imprimir1").jqxButton({
		width: 120,
		height: 25,
		template: "warning"
	});
	$("#presupuesto_presupuesto_imprimir1").bind('click', function ()
	{
		var data = "imprimir/presupuesto.php?Interno="+$("#presupuesto_presupuesto_interno2").val()+"&Month=true";
		window.open(data, "", "width=730, height=600, menubar=no, titlebar=no");
	});
	
	$("#presupuesto_presupuesto_imprimir2").jqxButton({
		width: 120,
		height: 25,
		template: "warning"
	});
	$("#presupuesto_presupuesto_imprimir2").bind('click', function ()
	{
		var data = "imprimir/presupuesto_detalles.php?Interno="+$("#presupuesto_presupuesto_interno2").val();
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#presupuesto_presupuesto_imprimir3").jqxButton({
		width: 120,
		height: 25,
		template: "warning"
	});
	$("#presupuesto_presupuesto_imprimir3").bind('click', function ()
	{
		var data = "imprimir/presupuesto_detalles_apu.php?Interno="+$("#presupuesto_presupuesto_interno2").val();
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	$("#presupuesto_presupuesto_imprimir4").jqxButton({
		width: 120,
		height: 25,
		template: "warning"
	});
	$("#presupuesto_presupuesto_imprimir4").bind('click', function ()
	{
		var data = "imprimir/presupuesto_listado_detallado_apu.php?Interno="+$("#presupuesto_presupuesto_interno2").val();
		window.open(data, "", "width=930, height=600, menubar=no, titlebar=no");
	});
	
	if (!Admin && !Imprimir)
	{
		$("#presupuesto_presupuesto_imprimir1").jqxButton({ disabled: true });
		$("#presupuesto_presupuesto_imprimir2").jqxButton({ disabled: true });
		$("#presupuesto_presupuesto_imprimir3").jqxButton({ disabled: true });
	}
	
	//--- Windows
	$("#Presupuesto_APU_Agregar_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		width: 285,
		height: 230,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#presupuesto_presupuesto_apu_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_presupuesto_val_add").focus();
				}
			});
			
			$("#presupuesto_presupuesto_val_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_mat_grupo_button").trigger("click");
				}
			});
			
			$("#presupuesto_presupuesto_apu_form").jqxValidator({
				rules: [
					{ input: "#presupuesto_presupuesto_apu_add", message: "Se Requiere un Nombre!", action: "keyup, blur", rule: "required" },
					//{ input: "#presupuesto_presupuesto_val_add", message: "Se Requiere un Valor!", action: "keyup, blur", rule: "required" },
				]
			});
			
			$("#presupuesto_presupuesto_selected_clasificacion").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
				disabled: true,
			});
			
			$("#presupuesto_presupuesto_apu_add").jqxInput({
				theme: mytheme,
				width: 188,
				height: 20,
			});
			
			$("#presupuesto_presupuesto_val_add").jqxNumberInput({
				theme: mytheme,
				width: 188,
				height: 20,
				inputMode: 'simple',
				spinButtons: false,
				symbol: '$',
				decimalDigits: 2,
				digits: 18,
				max: 999999999999999999,
			});
	
			$("#presupuesto_presupuesto_apu_add_button").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "primary"
			});
			$("#presupuesto_presupuesto_apu_add_button").on("click", function ()
			{
				if (Locked)
					return;
		
				Locked = true;
				var validationResult = function (isValid)
				{
					if (isValid)
					{
						ClickOK = true;
					}
					else
						Locked = false;
				}
				$("#presupuesto_presupuesto_apu_form").jqxValidator("validate", validationResult);
			});
		}
	});
	$("#Presupuesto_APU_Agregar_Window").on("open", function ()
	{
		var Item = $("#presupuesto_presupuesto_clasificacion").jqxComboBox("getSelectedItem");
		if (Item)
			var Val = Item.value;
		else
			var Val = "";
		$("#presupuesto_presupuesto_selected_clasificacion").val(Val);
	});
	
	// Initial Values
	Clasificacion = "originales";
	
	// Valores Iniciales
	LoadInitialValues();
	LoadParameters();
	CheckRefresh();
});
</script>
<div id="Presupuesto_APU_Agregar_Window">
	<div id="Presupuesto_APU_Agregar_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 35px;">Crear Nuevo APU</div>
	</div>
	<div id="Presupuesto_APU_Agregar_Window_Content" class="WindowContainer" align="center">
		<form id="presupuesto_presupuesto_apu_form" onsubmit="return false;">
			<table cellspacing="2" cellpadding="5" align="center">
				<tr>
					<td colspan="2">
						Base de Datos Seleccionada
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_presupuesto_selected_clasificacion"/>
					</td>
				</tr>
				<tr>
					<td>
						Nombre
					</td>
					<td>
						<input type="text" id="presupuesto_presupuesto_apu_add"/>
					</td>
				</tr>
				<tr>
					<td>
						Valor
					</td>
					<td>
						<div id="presupuesto_presupuesto_val_add"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<input id="presupuesto_presupuesto_apu_add_button" type="button" value="Crear" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="3" cellspacing="1" style="margin:5px 0px 20px 0px;">
		<tr>
			<td style="text-align:center;">
				<div style="background: #75858A;color: #FFF;font-size: 13px;padding: 5px 5px 0px 5px; height: 25px;">
					Cargar Presupuesto:
				</div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_interno1"></div>
			</td>
			<td>
				<input type="button" id="presupuesto_presupuesto_back" value="<< Ant."/>
			</td>
			<td>
				<input type="button" id="presupuesto_presupuesto_next" value="Sig. >>"/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 1000px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<table cellpadding="2" cellspacing="2">
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<div id="presupuesto_presupuesto_fecha"></div>
			</td>
			<td>
				Proyecto
			</td>
			<td colspan="3">
				<input type="text" id="presupuesto_presupuesto_proyecto"/>
			</td>
			<td>
				Cliente
			</td>
			<td colspan="3">
				<div id="presupuesto_presupuesto_cliente"></div>
			</td>
		</tr>
		<tr>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="presupuesto_presupuesto_interno2"/>
			</td>
			<td>
				Telefono
			</td>
			<td>
				<input type="text" id="presupuesto_presupuesto_telefono"/>
			</td>
			<td>
				Direccion
			</td>
			<td>
				<input type="text" id="presupuesto_presupuesto_direccion"/>
			</td>
			<td>
				Email
			</td>
			<td>
				<input type="text" id="presupuesto_presupuesto_email"/>
			</td>
			<td>
				Cliente ID
			</td>
			<td>
				<div id="presupuesto_presupuesto_cliente_ID"></div>
			</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="1" style="border-left: 1px solid #A4BED4; border-top: 1px solid #A4BED4; border-right: 1px solid #A4BED4; margin-top: 20px; text-align:center; width:1002px;">
		<tr>
			<td>
				<div id="presupuesto_presupuesto_clasificacion"></div>
			</td>
			<td>
				<input type="text" id="presupuesto_presupuesto_item"/>
			</td>
			<td>
				<div id="presupuesto_presupuesto_codigo"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_producto"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_cantidad"></div>
			</td>
			<td>
				<input type="text" id="presupuesto_presupuesto_unidad" style="text-align: center;"/>
			</td>
			<td>
				<div id="presupuesto_presupuesto_valor"></div>
			</td>
			<td>
				<input type="button" id="presupuesto_presupuesto_add" value="Añadir">
			</td>
			<td>
				<input type="button" id="presupuesto_presupuesto_update" value="Act. Valores">
			</td>
		</tr>
	</table>
	
	<div id="presupuesto_presupuesto_items_grid1"></div>

	<div align="left" style="width: 1000px; border-bottom: #A9A9A9 1px solid; margin:15px 0px;">
		<p id="presupuesto_presupuesto_spoiler">Cerrar Detalles APU</p>
	</div>
	<table id="presupuesto_presupuesto_items_grid2_table" cellpadding="2" cellspacing="1" style="border-left: 1px solid #A4BED4; border-top: 1px solid #A4BED4; border-right: 1px solid #A4BED4; text-align:center; width:1002px;">
		<tr>
			<td>
				<div id="presupuesto_presupuesto_clasificacion2"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_codigo2"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_producto2"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_cantidad2"></div>
			</td>
			<td>
				<input type="text" id="presupuesto_presupuesto_unidad2" style="text-align: center;"/>
			</td>
			<td>
				<div id="presupuesto_presupuesto_valor2"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_uso2"></div>
			</td>
			<td>
				<input type="button" id="presupuesto_presupuesto_add2" value="Añadir">
			</td>
		</tr>
	</table>
	<div id="presupuesto_presupuesto_items_grid2" style="dispaly: none;"></div>
	
	<table cellpadding="2" cellspacing="2" style="margin-top:15px; margin-bottom:30px;">
		<tr>
			<td>
				Observaciones
			</td>
			<td rowspan="6" style="width:380px;">
				&nbsp;
			</td>
			<td colspan="2">
				SUBTOTAL
			</td>
			<td>
				<div id="presupuesto_presupuesto_subtotal"></div>
			</td>
		</tr>
		<tr>
			<td rowspan="4">
				<textarea rows="7" cols="35" id="presupuesto_presupuesto_observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td style="width:90px;">
				Administracion
			</td>
			<td>
				<div id="presupuesto_presupuesto_administracion1"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_administracion2"></div>
			</td>
		</tr>
		<tr>
			<td>
				Imprevistos
			</td>
			<td>
				<div id="presupuesto_presupuesto_imprevistos1"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_imprevistos2"></div>
			</td>
		</tr>
		<tr>
			<td>
				Utilidades
			</td>
			<td>
				<div id="presupuesto_presupuesto_utilidades1"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_utilidades2"></div>
			</td>
		</tr>
		<tr>
			<td>
				IVA Utilidad
			</td>
			<td>
				<div id="presupuesto_presupuesto_iva1_check"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_iva1"></div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				IVA sobre Total
			</td>
			<td>
				<div id="presupuesto_presupuesto_iva2_check"></div>
			</td>
			<td>
				<div id="presupuesto_presupuesto_iva2"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<li>
					<input type="button" id="presupuesto_presupuesto_crear" value="Crear"/>
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="presupuesto_presupuesto_guardar" value="Guardar"/>
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="presupuesto_presupuesto_eliminar" value="Eliminar"/>
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="presupuesto_presupuesto_nuevo" value="Nuevo"/>
				</li>
			</td>
			<td colspan="2">
				TOTAL
			</td>
			<td>
				<div id="presupuesto_presupuesto_total"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<li>
					<input type="button" id="presupuesto_presupuesto_imprimir1" value="Imprimir"/>
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="presupuesto_presupuesto_imprimir2" value="Detalles"/>
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="presupuesto_presupuesto_imprimir3" value="Detalles APU"/>
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="presupuesto_presupuesto_imprimir4" value="Listado APU"/>
				</li>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
	</table>
</div>

