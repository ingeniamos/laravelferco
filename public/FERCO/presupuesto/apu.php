<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var ID = "";
	var Clasificacion = "";
	var Clasificacion2 = "";
	var Grupo = "";
	var SubGrupo = "";
	var Grupo2 = "";
	var SubGrupo2 = "";
	var Codigo = "";
	var Codigo2 = "";
	var Unidad = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Timer3 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Presupuesto_Content");
	var Body = document.getElementById("Presupuesto_APU_Content");
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
				SystemMap("APU", true);
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
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Presupuesto" && $data[$i]["SubModulo"] == "APU" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
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
	$("#presupuesto_apu_clasificacion").keyup(function(event) {
		if(event.which == 13)
			$("#presupuesto_apu_codigo3").jqxComboBox('focus');
	});
	$("#presupuesto_apu_codigo3").keyup(function(event) {
		if(event.which == 13)
			$("#presupuesto_apu_producto").jqxComboBox('focus');
	});
	$("#presupuesto_apu_producto").keyup(function(event) {
		if(event.which == 13)
		{
			$("#presupuesto_apu_cantidad").jqxNumberInput('focus');
			var input = $("#presupuesto_apu_cantidad input")[0];
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
	$("#presupuesto_apu_cantidad").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row();
		}
	});
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"presupuesto_apu_codigo1", type:"jqxComboBox"},
			{id:"presupuesto_apu_nombre1", type:"jqxComboBox"},
			{id:"presupuesto_apu_codigo_grupo", type:""},
			{id:"presupuesto_apu_codigo_subgrupo", type:""},
			{id:"presupuesto_apu_codigo2", type:""},
			{id:"presupuesto_apu_nombre2", type:""},
			{id:"presupuesto_apu_grupo1", type:"jqxComboBox"},
			{id:"presupuesto_apu_grupo2", type:"jqxComboBox"},
			{id:"presupuesto_apu_subgrupo1", type:"jqxComboBox"},
			{id:"presupuesto_apu_subgrupo2", type:"jqxComboBox"},
			{id:"presupuesto_apu_unidad", type:"jqxComboBox"},
			{id:"presupuesto_apu_total", type:"jqxNumberInput"},
			{id:"presupuesto_apu_items_grid", type:"jqxGrid"},
		];
		
		EnableDisableJSON = [
			{id:"presupuesto_apu_clasificacion", type:"jqxComboBox"},
			{id:"presupuesto_apu_codigo1", type:"jqxComboBox"},
			{id:"presupuesto_apu_nombre1", type:"jqxComboBox"},
			{id:"presupuesto_apu_back", type:"jqxButton"},
			{id:"presupuesto_apu_next", type:"jqxButton"},
			{id:"presupuesto_apu_codigo2", type:""},
			{id:"presupuesto_apu_nombre2", type:""},
			{id:"presupuesto_apu_grupo1", type:"jqxComboBox"},
			{id:"presupuesto_apu_grupo2", type:"jqxComboBox"},
			{id:"presupuesto_apu_subgrupo1", type:"jqxComboBox"},
			{id:"presupuesto_apu_subgrupo2", type:"jqxComboBox"},
			{id:"presupuesto_apu_unidad", type:"jqxComboBox"},
			{id:"presupuesto_apu_add", type:"jqxButton"},
			{id:"presupuesto_apu_items_grid", type:"jqxGrid"},
			{id:"presupuesto_apu_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	
	function ClearDocument()
	{
		Locked = false;
		ID = "";
		Clasificacion = "";
		Clasificacion2 = "";
		Grupo = "";
		SubGrupo = "";
		Grupo2 = "";
		SubGrupo2 = "";
		Codigo = "";
		Codigo2 = "";
		Unidad = "";
		Timer1 = 0;
		Timer2 = 0;
		Timer3 = 0;
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	}
	
	function LoadParameters()
	{
		//1
		$("#presupuesto_apu_grupo1").jqxComboBox('clearSelection');
		$("#presupuesto_apu_subgrupo1").jqxComboBox('clearSelection');
		$("#presupuesto_apu_nombre1").jqxComboBox('clearSelection');
		$("#presupuesto_apu_codigo1").jqxComboBox('clearSelection');
		$("#presupuesto_apu_grupo1").jqxComboBox('clear');
		$("#presupuesto_apu_subgrupo1").jqxComboBox('clear');
		$("#presupuesto_apu_nombre1").jqxComboBox('clear');
		$("#presupuesto_apu_codigo1").jqxComboBox('clear');
		//2
		$("#presupuesto_apu_grupo2").jqxComboBox('clearSelection');
		$("#presupuesto_apu_subgrupo2").jqxComboBox('clearSelection');
		$("#presupuesto_apu_grupo2").jqxComboBox('clear');
		$("#presupuesto_apu_subgrupo2").jqxComboBox('clear');
		$("#presupuesto_apu_unidad").jqxComboBox('clearSelection');
		//3
		$("#presupuesto_apu_producto").jqxComboBox('clearSelection');
		$("#presupuesto_apu_codigo3").jqxComboBox('clearSelection');
		$("#presupuesto_apu_producto").jqxComboBox('clear');
		$("#presupuesto_apu_codigo3").jqxComboBox('clear');
		$("#presupuesto_apu_cantidad").val("0");
		$("#presupuesto_apu_unidad2").val("");
		$("#presupuesto_apu_valor").val("0");
		$("#presupuesto_apu_uso").val("0");
		
		ClasificacionSource.data = {"Presupuesto_Clasificacion":true};
		UnidadSource.data = {"Presupuesto_Unidad":true};
		
		var ClasificacionDataAdapter = new $.jqx.dataAdapter(ClasificacionSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#presupuesto_apu_clasificacion").jqxComboBox({source: records});
				$("#presupuesto_apu_clasificacion2").jqxComboBox({source: records});
			},
		});
		
		var UnidadDataAdapter = new $.jqx.dataAdapter(UnidadSource);
		$("#presupuesto_apu_unidad").jqxComboBox({source: UnidadDataAdapter});
	}
	
	function LoadValues()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'ID', type: 'string'},
				{ name: 'Grupo', type: 'string'},
				{ name: 'SubGrupo', type: 'string'},
				{ name: 'Unidad', type: 'string'},
				{ name: 'Desperdicios', type: 'decimal'},
				{ name: 'Gastos', type: 'decimal'},
				{ name: 'Valor', type: 'decimal'},
				//
				{ name: 'Items', type: 'string'},
			],
			data:{
				"APU_Cargar":$("#presupuesto_apu_codigo1").val(),
				"Clasificacion":$("#presupuesto_apu_clasificacion").val(),
			},
			url: "modulos/datos_productos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				var Item = $("#presupuesto_apu_nombre1").jqxComboBox('getSelectedItem');
				if (Item)
				{
					$("#presupuesto_apu_codigo2").val(Item.value.substr(6));
					//$("#presupuesto_apu_codigo2").val(Item.value);
					$("#presupuesto_apu_nombre2").val(Item.label);
				}
				
				$("#presupuesto_apu_grupo2").jqxComboBox('selectItem', records[0]["Grupo"]);
				ID = records[0]["ID"];
				SubGrupo2 = records[0]["SubGrupo"];
				$("#presupuesto_apu_unidad").jqxComboBox('selectItem', records[0]["Unidad"]);
				
				// Clear Grid
				$("#presupuesto_apu_items_grid").jqxGrid("clear");
				
				if (records[0]["Items"] == "")
					var len = 0;
				else
					var len = records[0]["Items"].length;
				
				var TmpArray = new Array();
				
				for (var i = 0; i < len; i++)
				{
					var datarow = {};
					
					datarow["Codigo"] = records[0]["Items"][i]["Codigo"];
					datarow["Nombre"] = records[0]["Items"][i]["Nombre"];
					datarow["Categoria"] = records[0]["Items"][i]["Categoria"];
					datarow["Unidad"] = records[0]["Items"][i]["Unidad"];
					datarow["Cantidad"] = parseFloat(records[0]["Items"][i]["Cantidad"]);
					datarow["Valor"] = parseFloat(records[0]["Items"][i]["Valor"]);
					datarow["Uso"] = parseFloat(records[0]["Items"][i]["Uso"]);
					datarow["Total"] = parseFloat(records[0]["Items"][i]["Total"]);
					datarow["Tipo"] = records[0]["Items"][i]["Tipo"];
					datarow["Clasificacion"] = records[0]["Items"][i]["Clasificacion"];
					
					TmpArray[i] = datarow;
				}
				$("#presupuesto_apu_items_grid").jqxGrid("addrow", null, TmpArray, "first");
				$("#presupuesto_apu_desperdicios_valor").val(records[0]["Desperdicios"]);
				$("#presupuesto_apu_gastos_valor").val(records[0]["Gastos"]);
				$("#presupuesto_apu_total").val(records[0]["Valor"]);
				Calcular();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	}
	
	function AddGrupo(Val, ID)
	{
		$("#Presupuesto_APU_Grupos_Window").jqxWindow("open");
		var items = $("#presupuesto_apu_grupo2").jqxComboBox("getItems");
		if (items.length != undefined)
			var Cod = parseInt(items[(items.length - 1)].value) + 1;
		else
			var Cod = 1;
		
		if (Cod < 10)
			Cod = "0"+Cod;
		
		$("#presupuesto_apu_codigo_add").val(Cod);
		$("#presupuesto_apu_grupo_add").val(Val);
		
		var CheckTimer = setInterval(function()
		{
			if (ClickOK == true)
			{
				clearInterval(CheckTimer);
				
				$("#Loading_Mess").html("Procesando Solicitud...");
				$("#Loading").show();
				$.ajax({
					dataType: "text",
					//type: "POST",
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Presupuesto_Grupo":true,
						"Clasificacion":$("#presupuesto_apu_selected_clasificacion2").val(),
						"VAL1":"APU",
						"VAL2":$("#presupuesto_apu_codigo_add").val(),
						"VAL3":$("#presupuesto_apu_grupo_add").val(),
					},
					success: function (data)
					{
						switch(data)
						{
							case "":
								//Update Comboboxes
								$("#presupuesto_apu_grupo1").jqxComboBox("addItem", {
									label: $("#presupuesto_apu_codigo_add").val()+". "+$("#presupuesto_apu_grupo_add").val(), 
									value: $("#presupuesto_apu_codigo_add").val()
								});
								$("#presupuesto_apu_grupo2").jqxComboBox("addItem", {
									label: $("#presupuesto_apu_codigo_add").val()+". "+$("#presupuesto_apu_grupo_add").val(), 
									value: $("#presupuesto_apu_codigo_add").val()
								});
								//$("#presupuesto_apu_grupo1").jqxComboBox("selectItem", $("#presupuesto_apu_codigo_add").val());
								$("#presupuesto_apu_grupo2").jqxComboBox("selectItem", $("#presupuesto_apu_codigo_add").val());
							break;
							
							case "EXIST":
								Alerts_Box("El codigo Ingresado ya Existe", 3);
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
						$("#Presupuesto_APU_Grupos_Window").jqxWindow("close");
					}
				});
			}
			else if (ClickCANCEL == true)
			{
				clearInterval(CheckTimer);
			}
		}, 10);
	}
	
	function AddSubGrupo(Val, ID)
	{
		$("#Presupuesto_APU_Grupos_Window").jqxWindow("open");
		var grupo = $("#presupuesto_apu_grupo2").jqxComboBox("getSelectedItem");
		if (!grupo)
			grupo = "";
		else
			grupo = grupo.value;
		var items = $("#presupuesto_apu_subgrupo2").jqxComboBox("getItems");
		if (items.length != undefined && items.value != undefined)
			var Cod = parseInt(items[(items.length - 1)].value) + 1;
		else
			var Cod = 1;
		
		if (Cod < 10)
			Cod = "0"+Cod;
		
		$("#presupuesto_apu_codigo_add").val(Cod);
		$("#presupuesto_apu_grupo_add").val(Val);
		
		var CheckTimer = setInterval(function()
		{
			if (ClickOK == true)
			{
				clearInterval(CheckTimer);
				
				$("#Loading_Mess").html("Procesando Solicitud...");
				$("#Loading").show();
				$.ajax({
					dataType: "text",
					//type: "POST",
					url: "modulos/guardar.php",
					data: {
						"Parametros_Agregar_Presupuesto_SubGrupo":true,
						"Clasificacion":$("#presupuesto_apu_selected_clasificacion2").val(),
						"Categoria":"APU",
						"Grupo":grupo,
						"VAL1":$("#presupuesto_apu_codigo_add").val(),
						"VAL2":$("#presupuesto_apu_grupo_add").val(),
					},
					success: function (data)
					{
						switch(data)
						{
							case "":
								//Update Comboboxes
								$("#presupuesto_apu_subgrupo1").jqxComboBox("addItem", {
									label: $("#presupuesto_apu_codigo_add").val()+". "+$("#presupuesto_apu_grupo_add").val(), 
									value: $("#presupuesto_apu_codigo_add").val()
								});
								$("#presupuesto_apu_subgrupo2").jqxComboBox("addItem", {
									label: $("#presupuesto_apu_codigo_add").val()+". "+$("#presupuesto_apu_grupo_add").val(), 
									value: $("#presupuesto_apu_codigo_add").val()
								});
								//$("#presupuesto_apu_subgrupo1").jqxComboBox("selectItem", $("#presupuesto_apu_codigo_add").val());
								$("#presupuesto_apu_subgrupo2").jqxComboBox("selectItem", $("#presupuesto_apu_codigo_add").val());
							break;
							
							case "EXIST":
								Alerts_Box("El codigo Ingresado ya Existe", 3);
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
						$("#Presupuesto_APU_Grupos_Window").jqxWindow("close");
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
	
	function LoadFilter()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			APU_ID_Source.data = {
				"Presupuesto_APU":true,
				"Clasificacion":$("#presupuesto_apu_clasificacion").val(),
				"Grupo":$("#presupuesto_apu_grupo1").val(),
				"SubGrupo":$("#presupuesto_apu_subgrupo1").val(),
			};
			//var APU_ID_DataAdapter = new $.jqx.dataAdapter(APU_ID_Source);
			var APU_ID_DataAdapter = new $.jqx.dataAdapter(APU_ID_Source,
			{
				autoBind: true,
				loadComplete: function (records)
				{
					$("#presupuesto_apu_codigo1").jqxComboBox({source: records});
					$("#presupuesto_apu_nombre1").jqxComboBox({source: records});
				},
			});
			//$("#presupuesto_apu_codigo1").jqxComboBox({source: APU_ID_DataAdapter});
			//$("#presupuesto_apu_nombre1").jqxComboBox({source: APU_ID_DataAdapter});
		},350);
	}
	
	var ClasificacionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Clasificacion', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Grupo', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'SubGrupo', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	var UnidadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Unidad', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	var APU_ID_Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
	};
	
	var APU_Data = new Array();
	
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
		//data: {"Presupuesto_APU":true},
		url: "modulos/parametros.php",
		async: false
	};
	/*var APUDataAdapter = new $.jqx.dataAdapter(APUSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				APU_Data.push(records[i]);
			}
		}
	});*/
	
	$("#presupuesto_apu_clasificacion").jqxComboBox(
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
	$("#presupuesto_apu_clasificacion").on("change", function (event)
	{
		if (event.args)
		{
			Clasificacion = event.args.item.value;
			
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				GrupoSource.data = {
					"Presupuesto_Grupo":true,
					"Clasificacion":event.args.item.value,
					"Categoria":"APU",
				};
				//var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
				//$("#presupuesto_apu_grupo1").jqxComboBox({source: GrupoDataAdapter});
				//$("#presupuesto_apu_grupo2").jqxComboBox({source: GrupoDataAdapter});
				var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource,
				{
					autoBind: true,
					loadComplete: function (records)
					{
						var data = new Array();
						for (var i = 0; i < records.length; i++)
						{
							var TmpData = records[i];
							TmpData.Grupo = TmpData.Codigo + ". " + TmpData.Grupo;
							data.push(TmpData);
						}
						$("#presupuesto_apu_grupo1").jqxComboBox({source: data});
						$("#presupuesto_apu_grupo2").jqxComboBox({source: data});
					},
				});
				LoadFilter();
			},500);
		}
		else
		{
			//ID = "";
			Clasificacion = "";
			$("#presupuesto_apu_clasificacion").jqxComboBox('clearSelection');
			$("#presupuesto_apu_grupo1").jqxComboBox('clearSelection');
			$("#presupuesto_apu_grupo2").jqxComboBox('clearSelection');
			$("#presupuesto_apu_subgrupo1").jqxComboBox('clearSelection');
			$("#presupuesto_apu_subgrupo2").jqxComboBox('clearSelection');
			$("#presupuesto_apu_nombre1").jqxComboBox('clearSelection');
			$("#presupuesto_apu_codigo1").jqxComboBox('clearSelection');
			$("#presupuesto_apu_grupo1").jqxComboBox('clear');
			$("#presupuesto_apu_grupo2").jqxComboBox('clear');
			$("#presupuesto_apu_subgrupo1").jqxComboBox('clear');
			$("#presupuesto_apu_subgrupo2").jqxComboBox('clear');
			$("#presupuesto_apu_nombre1").jqxComboBox('clear');
			$("#presupuesto_apu_codigo1").jqxComboBox('clear');
		}
	});
	$("#presupuesto_apu_clasificacion").on("bindingComplete", function (event)
	{
		if (Clasificacion != "")
			$("#presupuesto_apu_clasificacion").jqxComboBox("selectItem", Clasificacion);
	});
	document.getElementById("presupuesto_apu_clasificacion").addEventListener("dblclick", function (event)
	{
		$("#Presupuesto_APU_Clasificacion_Window").jqxWindow('open');
	});
	
	$("#presupuesto_apu_grupo1").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Codigo',
	});
	$("#presupuesto_apu_grupo1").on("change", function (event)
	{
		if (event.args)
		{
			Grupo = event.args.item.value;
			
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				SubGrupoSource.data = {
					"Presupuesto_SubGrupo":true,
					"P_Grupo":event.args.item.value,
					"P_Clasificacion":$("#presupuesto_apu_clasificacion").val(),
					"P_Categoria":"APU",
				};
				//var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				//$("#presupuesto_apu_subgrupo1").jqxComboBox({source: SubGrupoDataAdapter});
				var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource,
				{
					autoBind: true,
					loadComplete: function (records)
					{
						var data = new Array();
						for (var i = 0; i < records.length; i++)
						{
							var TmpData = records[i];
							TmpData.SubGrupo = TmpData.Codigo + ". " + TmpData.SubGrupo;
							data.push(TmpData);
						}
						$("#presupuesto_apu_subgrupo1").jqxComboBox({source: data});
					},
				});
				LoadFilter();
			},350);
		}
		else
		{
			Grupo = "";
			SubGrupo = "";
			$("#presupuesto_apu_grupo1").jqxComboBox('clearSelection');
			$("#presupuesto_apu_subgrupo1").jqxComboBox('clearSelection');
			$("#presupuesto_apu_subgrupo1").jqxComboBox('clear');
		}
	});
	$("#presupuesto_apu_grupo1").on("bindingComplete", function (event)
	{
		if (Grupo != "")
			$("#presupuesto_apu_grupo1").jqxComboBox("selectItem", Grupo);
	});
	
	$("#presupuesto_apu_subgrupo1").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'Codigo',
	});
	$("#presupuesto_apu_subgrupo1").on("change", function (event)
	{
		if (event.args)
			SubGrupo = event.args.item.value;
		else
		{
			SubGrupo = "";
			$("#presupuesto_apu_subgrupo1").jqxComboBox('clearSelection');
		}
		LoadFilter();
	});
	$("#presupuesto_apu_subgrupo1").on("bindingComplete", function (event)
	{
		if (SubGrupo != "")
			$("#presupuesto_apu_subgrupo1").jqxComboBox("selectItem", SubGrupo);
	});
	
	$("#presupuesto_apu_codigo1").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#presupuesto_apu_codigo1").on("change", function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#presupuesto_apu_nombre1").val() != event.args.item.value)
					$("#presupuesto_apu_nombre1").jqxComboBox('selectItem', event.args.item.value);
				
				if (Codigo == event.args.item.value)
					return;
				
				Codigo = event.args.item.value;
			
				LoadValues();
			},500);
		}
		else
		{
			var item_value = $("#presupuesto_apu_codigo1").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				//ID = "";
				Codigo = "";
				$("#presupuesto_apu_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_apu_nombre1").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#presupuesto_apu_codigo1").val();
				var item = $("#presupuesto_apu_codigo1").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					//ID = "";
					Codigo = "";
					$("#presupuesto_apu_codigo1").jqxComboBox('clearSelection');
					$("#presupuesto_apu_nombre1").jqxComboBox('clearSelection');
				}
				else
					$("#presupuesto_apu_codigo1").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#presupuesto_apu_codigo1").on("bindingComplete", function (event)
	{
		if (Codigo != "")
			$("#presupuesto_apu_codigo1").jqxComboBox("selectItem", Codigo);
	});
	
	$("#presupuesto_apu_nombre1").jqxComboBox(
	{
		theme: mytheme,
		width: 347,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Nombre',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#presupuesto_apu_nombre1").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_apu_codigo1").val() != event.args.item.value)
				$("#presupuesto_apu_codigo1").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#presupuesto_apu_nombre1").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				//ID = "";
				$("#presupuesto_apu_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_apu_nombre1").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#presupuesto_apu_nombre1").val();
				
				var item = $("#presupuesto_apu_nombre1").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_apu_nombre1").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				//ID = "";
				$("#presupuesto_apu_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_apu_nombre1").jqxComboBox('clearSelection');
			}
		}
	});
	
	// --- SEARCH FILTER
	
	function Steps (TO)
	{
		var items = $("#presupuesto_apu_codigo1").jqxComboBox('getItems');
		if (!items)
			return;
		
		var len = items.length - 1;
		
		var index = $("#presupuesto_apu_codigo1").jqxComboBox('getSelectedIndex');
		
		if (TO == "next")
		{
			if (index >= len)
				Alerts_Box("Ha llegado al Final de la Busqueda.", 1);
			else
				$("#presupuesto_apu_codigo1").jqxComboBox('selectIndex',(index + 1));
		}
		else if (TO == "back")
		{
			if (index < 1)
				Alerts_Box("Ha llegado al Principio de la Busqueda.", 1);
			else
				$("#presupuesto_apu_codigo1").jqxComboBox('selectIndex',(index - 1));
		}
	}
	
	$("#presupuesto_apu_back").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_apu_back").bind('click', function ()
	{
		Steps("back")
	});
	
	$("#presupuesto_apu_next").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_apu_next").bind('click', function ()
	{
		Steps("next");
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#presupuesto_apu_categoria").jqxInput(
	{
		theme: mytheme,
		width: 90,
		height: 20,
		disabled: true
	});
	$("#presupuesto_apu_categoria").val("APU");
	
	$("#presupuesto_apu_grupo2").jqxComboBox(
	{
		theme: mytheme,
		width: 185,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Codigo',
	});
	$("#presupuesto_apu_grupo2").on("change", function (event)
	{
		if (event.args)
		{
			Grupo2 = event.args.item.value;
			
			clearTimeout(Timer3);
			Timer3 = setTimeout(function()
			{
				SubGrupoSource.data = {
					"Presupuesto_SubGrupo":true,
					"P_Grupo":event.args.item.value,
					"P_Clasificacion":$("#presupuesto_apu_clasificacion").val(),
					"P_Categoria":"APU",
				};
				var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource,
				{
					autoBind: true,
					loadComplete: function (records)
					{
						var data = new Array();
						for (var i = 0; i < records.length; i++)
						{
							var TmpData = records[i];
							TmpData.SubGrupo = TmpData.Codigo + ". " + TmpData.SubGrupo;
							data.push(TmpData);
						}
						$("#presupuesto_apu_subgrupo2").jqxComboBox({source: data});
					},
				});
			},500);
			$("#presupuesto_apu_codigo_grupo").val(event.args.item.value);
			$("#presupuesto_apu_codigo_subgrupo").val("");
		}
		else
		{
			var value = $("#presupuesto_apu_grupo2 input")[0].value;
			var item_value = $("#presupuesto_apu_grupo2").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				Grupo2 = "";
				SubGrupo2 = "";
				$("#presupuesto_apu_grupo2").jqxComboBox("clearSelection");
				$("#presupuesto_apu_subgrupo2").jqxComboBox("clearSelection");
				$("#presupuesto_apu_subgrupo2").jqxComboBox("clear");
				$("#presupuesto_apu_codigo_grupo").val("");
				$("#presupuesto_apu_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe. ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddGrupo(value, "presupuesto_apu_grupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
			else
			{
				//var value = $("#presupuesto_apu_grupo2").val();
				var item = $("#presupuesto_apu_grupo2").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_apu_grupo2").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				Grupo2 = "";
				SubGrupo2 = "";
				$("#presupuesto_apu_grupo2").jqxComboBox("clearSelection");
				$("#presupuesto_apu_subgrupo2").jqxComboBox("clearSelection");
				$("#presupuesto_apu_subgrupo2").jqxComboBox("clear");
				$("#presupuesto_apu_codigo_grupo").val("");
				$("#presupuesto_apu_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe! ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddGrupo(value, "presupuesto_apu_grupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
		}
	});
	$("#presupuesto_apu_grupo2").on("bindingComplete", function (event)
	{
		if (Grupo2 != "")
			$("#presupuesto_apu_grupo2").jqxComboBox("selectItem", Grupo2);
	});
	
	$("#presupuesto_apu_subgrupo2").jqxComboBox(
	{
		theme: mytheme,
		width: 185,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'Codigo',
	});
	$("#presupuesto_apu_subgrupo2").on("change", function (event)
	{
		if (event.args)
		{
			SubGrupo2 = event.args.item.value;
			$("#presupuesto_apu_codigo_subgrupo").val(event.args.item.value);
		}
		else
		{
			var value = $("#presupuesto_apu_subgrupo2 input")[0].value;
			var item_value = $("#presupuesto_apu_subgrupo2").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				SubGrupo2 = "";
				$("#presupuesto_apu_subgrupo2").jqxComboBox('clearSelection');
				$("#presupuesto_apu_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe. ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddSubGrupo(value, "presupuesto_apu_subgrupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
			else
			{
				//var value = $("#presupuesto_apu_subgrupo2").val();
				var item = $("#presupuesto_apu_subgrupo2").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_apu_subgrupo2").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				SubGrupo2 = "";
				$("#presupuesto_apu_subgrupo2").jqxComboBox('clearSelection');
				$("#presupuesto_apu_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe! ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddSubGrupo(value, "presupuesto_apu_subgrupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
		}
	});
	$("#presupuesto_apu_subgrupo2").on("bindingComplete", function (event)
	{
		if (SubGrupo2 != "")
			$("#presupuesto_apu_subgrupo2").jqxComboBox("selectItem", SubGrupo2);
	});
	
	$("#presupuesto_apu_codigo_grupo").jqxInput({
		theme: mytheme,
		width: 20,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_apu_codigo_subgrupo").jqxInput({
		theme: mytheme,
		width: 20,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_apu_codigo2").jqxInput({
		theme: mytheme,
		width: 50,
		height: 20,
	});
	
	$("#presupuesto_apu_nombre2").jqxInput({
		theme: mytheme,
		width: 300,
		height: 20,
	});
	
	$("#presupuesto_apu_unidad").jqxComboBox(
	{
		theme: mytheme,
		width: 90,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Unidad',
		valueMember: 'Unidad'
	});
	$("#presupuesto_apu_unidad").on("change", function (event)
	{
		if (event.args)
			Unidad = event.args.item.value;
		else
		{
			Unidad = "";
			$("#presupuesto_apu_unidad").jqxComboBox('clearSelection');
		}
	});
	$("#presupuesto_apu_unidad").on("bindingComplete", function (event)
	{
		if (Unidad != "")
			$("#presupuesto_apu_unidad").jqxComboBox("selectItem", Unidad);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Calcular()
	{ 
		var Total_APU = 0;
		var Total_MAT = 0;
		var Total_DESP = 0;
		var Total_MO = 0;
		var Total_HER = 0;
		var Total_EQU = 0;
		var Total = 0;
		
		var datinfo = $("#presupuesto_apu_items_grid").jqxGrid("getdatainformation");
		var count = datinfo.rowscount;
		
		if (count > 0)
		{
			for (var i = 0; i < count; i++)
			{
				var Cantidad = parseFloat($("#presupuesto_apu_items_grid").jqxGrid("getcellvalue", i, "Cantidad"));
				var Valor = parseFloat($("#presupuesto_apu_items_grid").jqxGrid("getcellvalue", i, "Valor"));
				var Uso = parseFloat($("#presupuesto_apu_items_grid").jqxGrid("getcellvalue", i, "Uso"));
				var Tipo = $("#presupuesto_apu_items_grid").jqxGrid("getcellvalue", i, "Tipo");
				var Tmp = (((Valor * Cantidad) / 100) * Uso);
				
				switch(Tipo)
				{
					case "APU":
						Total_APU += Tmp;
					break;
					
					case "Materiales":
						Total_MAT += Tmp;
					break;
					
					case "Mano de Obra":
						Total_MO += Tmp;
					break;
					
					case "Equipos":
						Total_EQU += Tmp;
					break;
				}
				Total += Tmp;
				$("#presupuesto_apu_items_grid").jqxGrid("setcellvalue", i, "Total", Tmp);
			}
			$("#presupuesto_apu_total_apu").val(Total_APU);
			$("#presupuesto_apu_total_mat").val(Total_MAT);
			var Desperdicio = parseFloat($("#presupuesto_apu_desperdicios_valor").val());
			Total_DESP = ((Total_MAT / 100) * Desperdicio);
			Total += Total_DESP;
			$("#presupuesto_apu_total_desperdicios").val(Total_DESP);
			$("#presupuesto_apu_total_mo").val(Total_MO);
			var Herramienta = parseFloat($("#presupuesto_apu_gastos_valor").val());
			Total_HER = ((Total_MO / 100) * Herramienta);
			Total += Total_HER;
			$("#presupuesto_apu_total_gastos").val(Total_HER);
			$("#presupuesto_apu_total_equ").val(Total_EQU);
			$("#presupuesto_apu_total").val(Total);
		}
		else
		{
			$("#presupuesto_apu_total_apu").val("0");
			$("#presupuesto_apu_total_mat").val("0");
			$("#presupuesto_apu_total_desperdicios").val("0");
			$("#presupuesto_apu_total_mo").val("0");
			$("#presupuesto_apu_total_gastos").val("0");
			$("#presupuesto_apu_total_equ").val("0");
			$("#presupuesto_apu_total").val("0");
		}
	}
	
	function Add_Row()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var Clasificacion = $("#presupuesto_apu_clasificacion2").jqxComboBox('getSelectedItem');
		var Producto = $("#presupuesto_apu_producto").jqxComboBox('getSelectedItem');
		var Unidad = $("#presupuesto_apu_unidad2").val();
		var Cantidad = $("#presupuesto_apu_cantidad").val();
		var Val = $("#presupuesto_apu_valor").val();
		var Uso = $("#presupuesto_apu_uso").val();
		
		if (!Clasificacion) {
			Alerts_Box("Favor Seleccionar un tipo de Clasificacion", 3);
			WaitClick_Combobox("presupuesto_apu_clasificacion2");
			Locked = false;
			return;
		}
		
		if (!Producto) {
			Alerts_Box("Favor Seleccionar un Producto.", 3);
			WaitClick_Combobox("presupuesto_apu_producto");
			Locked = false;
			return;
		}
		
		if (Cantidad < 0.001)
		{
			Alerts_Box("Debe añadir una Cantidad mayor a 0", 3);
			WaitClick_NumberInput("presupuesto_apu_cantidad");
			Locked = false;
			return;
		}
		
		var len = APU_Data.length;
		for (var i = 0; i < len; i++)
		{
			if (APU_Data[i]["Codigo"] == Producto.value)
			{
				var Tipo = APU_Data[i]["Tipo"];
				break;
			}
		}
		
		var datinfo = $("#presupuesto_apu_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#presupuesto_apu_items_grid").jqxGrid('getrowdata', i);
			if (currentRow.Codigo == Producto.value)
			{
				var id = $("#presupuesto_apu_items_grid").jqxGrid('getrowid', i);
				$("#presupuesto_apu_items_grid").jqxGrid('deleterow', id);
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
		
		$("#presupuesto_apu_items_grid").jqxGrid("addrow", null, datarow, "first");
		$("#presupuesto_apu_codigo3").jqxComboBox('clearSelection');
		$("#presupuesto_apu_producto").jqxComboBox('clearSelection');
		$("#presupuesto_apu_cantidad").val("0");
		$("#presupuesto_apu_unidad2").val("");
		$("#presupuesto_apu_valor").val("0");
		$("#presupuesto_apu_uso").val("0");
		Locked = false;
		Calcular();
	}
	
	$("#presupuesto_apu_clasificacion2").jqxComboBox(
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
	$("#presupuesto_apu_clasificacion2").on("change", function (event)
	{
		if (event.args)
		{
			Clasificacion2 = event.args.item.value;
			
			APUSource.data = {
				"Presupuesto_APU":true,
				"Clasificacion":$("#presupuesto_apu_clasificacion2").val(),
			};
			var APUDataAdapter = new $.jqx.dataAdapter(APUSource,
			{
				autoBind: true,
				/*beforeLoadComplete: function (records)
				{
					for (var i = 0; i < records.length; i++)
					{
						switch (records[i]["Tipo"])
						{
							case "APU":
							break;
							case "Mano de Obra":
							break;
							case "Maquinas":
							break;
							case "Productos":
							break;
						}
					}
				},*/
				loadComplete: function (records)
				{
					APU_Data = records;
				},
				loadError: function (jqXHR, status, error)
				{
					APU_Data = new Array();
				},
			});
			//var APUDataAdapter = new $.jqx.dataAdapter(APUSource);
			$("#presupuesto_apu_codigo3").jqxComboBox({source: APU_Data});
			$("#presupuesto_apu_producto").jqxComboBox({source: APU_Data});
		}
		else
		{
			Clasificacion2 = "";
			$("#presupuesto_apu_codigo3").jqxComboBox('clearSelection');
			$("#presupuesto_apu_producto").jqxComboBox('clearSelection');
			$("#presupuesto_apu_codigo3").jqxComboBox('clear');
			$("#presupuesto_apu_producto").jqxComboBox('clear');
			$("#presupuesto_apu_cantidad").val("0");
			$("#presupuesto_apu_unidad2").val("");
			$("#presupuesto_apu_valor").val("0");
			$("#presupuesto_apu_uso").val("0");
		}
	});
	$("#presupuesto_apu_clasificacion2").on("bindingComplete", function (event)
	{
		if (Clasificacion2 != "")
			$("#presupuesto_apu_clasificacion2").jqxComboBox("selectItem", Clasificacion2);
	});
	
	$("#presupuesto_apu_codigo3").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#presupuesto_apu_codigo3").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_apu_producto").val() != event.args.item.value)
				$("#presupuesto_apu_producto").val(event.args.item.value);
			
			var len = APU_Data.length;
			for (i = 0; i < len; i++)
			{
				if (APU_Data[i]["Codigo"] == event.args.item.value)
				{
					$("#presupuesto_apu_uso").val(APU_Data[i]["Uso"]);
					$("#presupuesto_apu_unidad2").val(APU_Data[i]["Unidad"]);
					$("#presupuesto_apu_valor").val(APU_Data[i]["Valor"]);
					break;
				}
			}
		}
		else
		{
			$("#presupuesto_apu_codigo3").jqxComboBox('clearSelection');
			$("#presupuesto_apu_producto").jqxComboBox('clearSelection');
			$("#presupuesto_apu_cantidad").val("0");
			$("#presupuesto_apu_unidad2").val("");
			$("#presupuesto_apu_valor").val("0");
			$("#presupuesto_apu_uso").val("0");
		}
	});
	
	$("#presupuesto_apu_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 280,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#presupuesto_apu_producto").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_apu_codigo3").val() != event.args.item.value)
				$("#presupuesto_apu_codigo3").val(event.args.item.value);
			
			var len = APU_Data.length;
			for (i = 0; i < len; i++)
			{
				if (APU_Data[i]["Codigo"] == event.args.item.value)
				{
					$("#presupuesto_apu_uso").val(APU_Data[i]["Uso"]);
					$("#presupuesto_apu_unidad2").val(APU_Data[i]["Unidad"]);
					$("#presupuesto_apu_valor").val(APU_Data[i]["Valor"]);
					break;
				}
			}
		}
		else
		{
			$("#presupuesto_apu_codigo3").jqxComboBox('clearSelection');
			$("#presupuesto_apu_producto").jqxComboBox('clearSelection');
			$("#presupuesto_apu_cantidad").val("0");
			$("#presupuesto_apu_unidad2").val("");
			$("#presupuesto_apu_valor").val("0");
			$("#presupuesto_apu_uso").val("0");
		}
	});
	
	$("#presupuesto_apu_cantidad").jqxNumberInput({
		theme: mytheme,
		width: 100,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		decimalDigits: 3,
		digits: 12,
		max: 999999999999,
	});
	
	$("#presupuesto_apu_unidad2").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#presupuesto_apu_valor").jqxNumberInput({
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
	
	$("#presupuesto_apu_uso").jqxNumberInput({
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
	
	$("#presupuesto_apu_add").jqxButton({
		width: 70,
		height: 25,
		template: "success"
	});
	$("#presupuesto_apu_add").bind('click', function ()
	{
		Add_Row();
	});
	
	var GridSource =
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
		addrow: function (rowid, rowdata, position, commit)
		{
			commit(true);
		},
		updaterow: function (rowid, rowdata, commit)
		{
			commit(true);
			Calcular();
		},
		deleterow: function (rowid, commit)
		{
			commit(true);
			Calcular();
		}
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#presupuesto_apu_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 300,
		source: GridDataAdapter,
		enabletooltips: true,
		enablebrowserselection: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		pageable: true,
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
					var selectedrowindex = $("#presupuesto_apu_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#presupuesto_apu_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#presupuesto_apu_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#presupuesto_apu_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '9%', height: 20 },
			{ text: 'Nombre', datafield: 'Nombre', editable: false, width: '28%', height: 20 },
			{ text: 'Categoria', datafield: 'Tipo', editable: false, width: '9%', height: 20, cellsalign: 'center' },
			{ text: 'Unidad', datafield: 'Unidad', editable: false, width: '5%', height: 20, cellsalign: 'center' },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '9%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value)
				{
					if (value < 0.001)
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 3, digits: 18, spinButtons: false });
				}
			},
			{
				text: 'Valor Unitario',
				datafield: 'Valor',
				editable: true,
				width: '14%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18, spinButtons: false });
				}
			},
			{
				text: 'Uso',
				datafield: 'Uso',
				editable: true,
				width: '6%',
				height: 20,
				cellsalign: 'center',
				cellsformat: 'p2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, spinButtons: false });
				}
			},
			{
				text: 'Valor Parcial',
				datafield: 'Total',
				editable: false,
				width: '17%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
			},
			//{ text: '', datafield: 'Tipo', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'Clasificacion', editable: false, width: '0%', height: 20 },
		],
	});
	//$("#presupuesto_apu_items_grid").jqxGrid('hidecolumn', 'Tipo');
	$("#presupuesto_apu_items_grid").jqxGrid('hidecolumn', 'Clasificacion');
	$("#presupuesto_apu_items_grid").jqxGrid('localizestrings', localizationobj);
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 4 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function SaveData(Crear)
	{
		if (Locked)
			return;
		
		Locked = true;
		var Actualizar = true;
		var FinalArray = new Array();
		var Items = new Array();
		
		var Clasificacion = $("#presupuesto_apu_clasificacion").jqxComboBox('getSelectedItem');
		if (!Clasificacion)
		{
			Alerts_Box("Debe Seleccionar una Clasificacion", 3);
			WaitClick_Combobox("presupuesto_apu_clasificacion");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_apu_codigo2").val() < 0 || $("#presupuesto_apu_codigo2").val() == "")
		{
			Alerts_Box("Debe Ingresar un Codigo.", 3);
			WaitClick_Input("presupuesto_apu_codigo2");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_apu_nombre2").val() < 0 || $("#presupuesto_apu_nombre2").val() == "")
		{
			Alerts_Box("Debe Ingresar un Nombre.", 3);
			WaitClick_Input("presupuesto_apu_nombre2");
			Locked = false;
			return;
		}
		
		var Grupo = $("#presupuesto_apu_grupo2").jqxComboBox('getSelectedItem');
		if (!Grupo)
		{
			Alerts_Box("Debe Seleccionar un Grupo", 3);
			WaitClick_Combobox("presupuesto_apu_grupo2");
			Locked = false;
			return;
		}
		
		var SubGrupo = $("#presupuesto_apu_subgrupo2").jqxComboBox('getSelectedItem');
		if (!SubGrupo)
		{
			Alerts_Box("Debe Seleccionar un SubGrupo", 3);
			WaitClick_Combobox("presupuesto_apu_subgrupo2");
			Locked = false;
			return;
		}
		
		var Unidad = $("#presupuesto_apu_unidad").jqxComboBox('getSelectedItem');
		if (!Unidad)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Unidad", 3);
			WaitClick_Combobox("presupuesto_apu_unidad");
			Locked = false;
			return;
		}
		
		var datinfo = $("#presupuesto_apu_items_grid").jqxGrid('getdatainformation');
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
			var currentRow = $("#presupuesto_apu_items_grid").jqxGrid('getrowdata', i);
			
			tmp_array["Codigo"] = currentRow.Codigo;
			tmp_array["Cantidad"] = currentRow.Cantidad;
			tmp_array["Valor"] = currentRow.Valor;
			tmp_array["Uso"] = currentRow.Uso;
			tmp_array["Total"] = currentRow.Total;
			tmp_array["Tipo"] = currentRow.Tipo;
			tmp_array["Clasificacion"] = currentRow.Clasificacion;
			
			Items[i] = tmp_array;
		}
		
		var myarray = {};
		
		if (!Crear)
			myarray["ID"] = ID;
		
		myarray["Clasificacion"] = Clasificacion.value;
		myarray["Grupo"] = Grupo.value;
		myarray["SubGrupo"] = SubGrupo.value;
		myarray["Codigo"] = Grupo.value + "." + SubGrupo.value + "." + $("#presupuesto_apu_codigo2").val();
		myarray["Codigo2"] = $("#presupuesto_apu_codigo2").val();
		myarray["Nombre"] = $("#presupuesto_apu_nombre2").val();
		myarray["Unidad"] = Unidad.value;
		myarray["Desperdicios"] = $("#presupuesto_apu_total_desperdicios").val();
		myarray["Gastos"] = $("#presupuesto_apu_total_gastos").val();
		myarray["Valor"] = $("#presupuesto_apu_total").val();
		myarray["Items"] = Items;
		
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
			data: {"APU_Crear":FinalArray},
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
						Alerts_Box("El Codigo ya existe en esta base de datos.<br />Favor seleccionar otra base de datos o cambiar el codigo para continuar.", 3);
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
	
	$("#presupuesto_apu_total_apu").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_apu_total_mat").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_apu_desperdicios_valor").jqxNumberInput({
		theme: mytheme,
		width: 60,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '%',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 3,
		max: 999.99,
	});
	$("#presupuesto_apu_desperdicios_valor").val("10");
	$("#presupuesto_apu_desperdicios_valor").on("change", function (event)
	{
		Calcular();
	});
	
	$("#presupuesto_apu_total_desperdicios").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 10,
		max: 9999999999,
		disabled: true,
	});
	
	$("#presupuesto_apu_total_mo").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_apu_gastos_valor").jqxNumberInput({
		theme: mytheme,
		width: 60,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '%',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 3,
		max: 999.99,
	});
	$("#presupuesto_apu_gastos_valor").val("10");
	$("#presupuesto_apu_gastos_valor").on("change", function (event)
	{
		Calcular();
	});
	
	$("#presupuesto_apu_total_gastos").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 10,
		max: 9999999999,
		disabled: true,
	});
	
	$("#presupuesto_apu_total_equ").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_apu_total").jqxNumberInput({
		theme: mytheme,
		width: 180,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_apu_nuevo").jqxButton({
		width: 100,
		height: 25,
		template: "success"
	});
	$("#presupuesto_apu_nuevo").bind('click', function ()
	{
		ClearDocument()
	});
	
	$("#presupuesto_apu_guardar").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_apu_guardar").bind('click', function ()
	{
		SaveData();
	});
	
	$("#presupuesto_apu_crear").jqxButton({
		width: 100,
		height: 25,
		template: "primary"
	});
	$("#presupuesto_apu_crear").bind('click', function ()
	{
		SaveData(true);
	});
	
	//----- Windows
	$("#Presupuesto_APU_Clasificacion_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 300,
		width: 300,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#presupuesto_apu_clasificacion_form").jqxValidator({
				rules: [
					{ input: "#presupuesto_apu_clasificacion_add", message: "Se Requiere un Nombre de Clasificacion!", action: "keyup, blur", rule: "required" },
					{ input: "#presupuesto_apu_notas", message: "Se Requiere un Concepto!", action: "keyup, blur", rule: "required" },
				]
			});
			
			$("#presupuesto_apu_selected_clasificacion").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
				disabled: true,
			});
			
			$("#presupuesto_apu_clasificacion_add").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
			});
	
			$("#presupuesto_apu_clasificacion_button1").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "primary"
			});
			$("#presupuesto_apu_clasificacion_button1").bind("click", function ()
			{
				if (Locked)
					return;
		
				Locked = true;
				var validationResult = function (isValid)
				{
					if (isValid)
					{
						$("#Loading_Mess").html("Procesando Solicitud...");
						$("#Loading").show();
						$.ajax({
							dataType: "text",
							url: "modulos/guardar.php",
							data: {
								"Presupuesto_Copiar_Clasificacion":true,
								"From_Clasificacion":$("#presupuesto_apu_selected_clasificacion").val(),
								"New_Clasificacion":$("#presupuesto_apu_clasificacion_add").val(),
								"Nota":$("#presupuesto_apu_notas").val()
							},
							success: function (data, status, xhr)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								if (data == "")
								{
									Alerts_Box("Datos Guardados con Exito!", 2);
									$("#presupuesto_apu_selected_clasificacion").val("");
									$("#presupuesto_apu_clasificacion_add").val("");
									$("#presupuesto_apu_notas").val("");
									$("#Presupuesto_APU_Clasificacion_Window").jqxWindow("close");
								}
								else if (data == "EXIST")
								{
									Alerts_Box("Ya existe una Clasificacion con el Mismo Nombre<br/>Favor Ingresar Otro.", 4);
								}
								else
								{
									Alerts_Box("Ha ocurrido un error mientras se guardaban los datos!<br/>Intente luego de unos segundos", 3);
									$("#presupuesto_apu_selected_clasificacion").val("");
									$("#presupuesto_apu_clasificacion_add").val("");
									$("#presupuesto_apu_notas").val("");
									$("#Presupuesto_APU_Clasificacion_Window").jqxWindow("close");
								}
							},
							error: function (jqXHR, textStatus, errorThrown)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								alert(textStatus+ " - " +errorThrown);
								$("#presupuesto_apu_selected_clasificacion").val("");
								$("#presupuesto_apu_clasificacion_add").val("");
								$("#presupuesto_apu_notas").val("");
								$("#Presupuesto_APU_Clasificacion_Window").jqxWindow("close");
							}
						});
					}
					else
						Locked = false;
				}
				$("#presupuesto_apu_clasificacion_form").jqxValidator("validate", validationResult);
			});
			
			$("#presupuesto_apu_clasificacion_button2").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "info"
			});
			$("#presupuesto_apu_clasificacion_button2").bind("click", function ()
			{
				if (Locked)
					return;
		
				Locked = true;
				var validationResult = function (isValid)
				{
					if (isValid)
					{
						$("#Loading_Mess").html("Procesando Solicitud...");
						$("#Loading").show();
						$.ajax({
							dataType: "text",
							url: "modulos/guardar.php",
							data: {
								"Presupuesto_Clasificacion":true,
								"Clasificacion":$("#presupuesto_apu_clasificacion_add").val(),
								"Nota":$("#presupuesto_apu_notas").val()
							},
							success: function (data, status, xhr)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								if (data == "")
								{
									Alerts_Box("Datos Guardados con Exito!", 2);
									$("#presupuesto_apu_selected_clasificacion").val("");
									$("#presupuesto_apu_clasificacion_add").val("");
									$("#presupuesto_apu_notas").val("");
									$("#Presupuesto_APU_Clasificacion_Window").jqxWindow("close");
								}
								else if (data == "EXIST")
								{
									Alerts_Box("Ya existe una Clasificacion con el Mismo Nombre<br/>Favor Ingresar Otro.", 4);
								}
								else
								{
									Alerts_Box("Ha ocurrido un error mientras se guardaban los datos!<br/>Intente luego de unos segundos", 3);
									$("#presupuesto_apu_selected_clasificacion").val("");
									$("#presupuesto_apu_clasificacion_add").val("");
									$("#presupuesto_apu_notas").val("");
									$("#Presupuesto_APU_Clasificacion_Window").jqxWindow("close");
								}
							},
							error: function (jqXHR, textStatus, errorThrown)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								alert(textStatus+ " - " +errorThrown);
								$("#presupuesto_apu_selected_clasificacion").val("");
								$("#presupuesto_apu_clasificacion_add").val("");
								$("#presupuesto_apu_notas").val("");
								$("#Presupuesto_APU_Clasificacion_Window").jqxWindow("close");
							}
						});
					}
					else
						Locked = false;
				}
				$("#presupuesto_apu_clasificacion_form").jqxValidator("validate", validationResult);
			});
		}
	});
	$("#Presupuesto_APU_Clasificacion_Window").on("open", function ()
	{
		var Item = $("#presupuesto_apu_clasificacion").jqxComboBox('getSelectedItem');
		if (Item)
			var Val = Item.value;
		else
			var Val = "";
		$("#presupuesto_apu_selected_clasificacion").val(Val);
	});
	$("#Presupuesto_APU_Clasificacion_Window").on("close", function () {
		LoadParameters();
	});
	
	$("#Presupuesto_APU_Grupos_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		width: 285,
		height: 210,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#presupuesto_apu_codigo_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_apu_grupo_add").focus();
				}
			});
			
			$("#presupuesto_apu_grupo_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_apu_grupo_button").trigger("click");
				}
			});
			
			$("#presupuesto_apu_grupo_form").jqxValidator({
				rules: [
					{ input: "#presupuesto_apu_codigo_add", message: "Se Requiere un Codigo!", action: "keyup, blur", rule: "required" },
					{ input: "#presupuesto_apu_grupo_add", message: "Se Requiere un Nombre!", action: "keyup, blur", rule: "required" },
				]
			});
			
			$("#presupuesto_apu_selected_clasificacion2").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
				disabled: true,
			});
			
			$("#presupuesto_apu_codigo_add").jqxInput({
				theme: mytheme,
				width: 40,
				height: 20,
			});
			
			$("#presupuesto_apu_grupo_add").jqxInput({
				theme: mytheme,
				width: 190,
				height: 20,
			});
	
			$("#presupuesto_apu_grupo_button").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "primary"
			});
			$("#presupuesto_apu_grupo_button").on("click", function ()
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
				$("#presupuesto_apu_grupo_form").jqxValidator("validate", validationResult);
			});
		}
	});
	$("#Presupuesto_APU_Grupos_Window").on("open", function ()
	{
		var Item = $("#presupuesto_apu_clasificacion").jqxComboBox("getSelectedItem");
		if (Item)
			var Val = Item.value;
		else
			var Val = "";
		$("#presupuesto_apu_selected_clasificacion2").val(Val);
	});
	
	$("#Presupuesto_APU_Unidad_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		width: 285,
		height: 210,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#presupuesto_apu_und_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_apu_und_button").trigger("click");
				}
			});
			
			$("#presupuesto_apu_und_form").jqxValidator({
				rules: [
					{ input: "#presupuesto_apu_und_add", message: "Se Requiere un Valor de Unidad!", action: "keyup, blur", rule: "required" },
				]
			});
			
			$("#presupuesto_apu_selected_clasificacion3").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
				disabled: true,
			});
			
			$("#presupuesto_apu_und_add").jqxInput({
				theme: mytheme,
				width: 190,
				height: 20,
			});
	
			$("#presupuesto_apu_und_button").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "primary"
			});
			$("#presupuesto_apu_und_button").on("click", function ()
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
				$("#presupuesto_apu_und_form").jqxValidator("validate", validationResult);
			});
		}
	});
	$("#Presupuesto_APU_Unidad_Window").on("open", function ()
	{
		var Item = $("#presupuesto_apu_clasificacion").jqxComboBox("getSelectedItem");
		if (Item)
			var Val = Item.value;
		else
			var Val = "";
		$("#presupuesto_apu_selected_clasificacion3").val(Val);
	});
	
	// Initial DB
	Clasificacion = "originales";
	Clasificacion2 = "originales";
	
	LoadParameters();
	CheckRefresh();
});
</script>
<div id="Presupuesto_APU_Clasificacion_Window">
	<div id="Presupuesto_APU_Clasificacion_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 20px;">Crear Nueva Base de Datos</div>
	</div>
	<div id="Presupuesto_APU_Clasificacion_Window_Content" class="WindowContainer" align="center">
		<form id="presupuesto_apu_clasificacion_form" onsubmit="return false;">
			<table cellspacing="2" cellpadding="5" align="center">
				<tr>
					<td colspan="2">
						Base de Datos Seleccionada
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_apu_selected_clasificacion"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Nombre de la Base de Datos
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_apu_clasificacion_add"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Notas
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea rows="3" cols="32" maxlength="200" style="resize:none;" id="presupuesto_apu_notas"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<input id="presupuesto_apu_clasificacion_button1" type="button" value="Crear Copia" />
					</td>
					<td>
						<input id="presupuesto_apu_clasificacion_button2" type="button" value="Crear Nueva" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div id="Presupuesto_APU_Grupos_Window">
	<div id="Presupuesto_APU_Grupos_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 35px;">Agregar Nuevo Valor</div>
	</div>
	<div id="Presupuesto_APU_Grupos_Window_Content" class="WindowContainer" align="center">
		<form id="presupuesto_apu_grupo_form" onsubmit="return false;">
			<table cellspacing="2" cellpadding="5" align="center">
				<tr>
					<td colspan="2">
						Base de Datos Seleccionada
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_apu_selected_clasificacion2"/>
					</td>
				</tr>
				<tr>
					<td>
						Codigo
					</td>
					<td>
						Nombre
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" id="presupuesto_apu_codigo_add"/>
					</td>
					<td>
						<input type="text" id="presupuesto_apu_grupo_add"/>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<input id="presupuesto_apu_grupo_button" type="button" value="Crear" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div id="Presupuesto_APU_Unidad_Window">
	<div id="Presupuesto_APU_Unidad_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 35px;">Agregar Nuevo Valor</div>
	</div>
	<div id="Presupuesto_APU_Unidad_Window_Content" class="WindowContainer" align="center">
		<form id="presupuesto_apu_und_form" onsubmit="return false;">
			<table cellspacing="2" cellpadding="5" align="center">
				<tr>
					<td colspan="2">
						Base de Datos Seleccionada
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_apu_selected_clasificacion3"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Unidad
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_apu_und_add"/>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<input id="presupuesto_apu_und_button" type="button" value="Crear" />
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
			<td rowspan="2" style="text-align:center;">
				<div style="background: #75858A;color: #FFF;font-size: 13px;padding: 5px 5px 0px 5px; height: 25px;">
					FILTRAR POR:
				</div>
			</td>
		</tr>
		<tr>
			<td>
				Base de Datos
			</td>
			<td>
				<div id="presupuesto_apu_clasificacion"></div>
			</td>
			<td>
				Grupo
			</td>
			<td>
				<li>
					<div id="presupuesto_apu_grupo1"></div>
				</li>
				<li style="padding-top:5px; padding-left:4px;">
					SubGrupo
				</li>
				<li style="padding-left:4px;">
					<div id="presupuesto_apu_subgrupo1"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td rowspan="2">
				<li>
					<input type="button" id="presupuesto_apu_back" value="<< Ant."/>
				</li>
				<li style="padding:0px 0px 0px 10px;">
					<input type="button" id="presupuesto_apu_next" value="Sig. >>"/>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Codigo
			</td>
			<td>
				<div id="presupuesto_apu_codigo1"></div>
			</td>
			<td>
				Nombre
			</td>
			<td>
				<div id="presupuesto_apu_nombre1"></div>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 1000px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px; text-align:center;">
		<tr>
			<td>
				Categoria
			</td>
			<td>
				Grupo
			</td>
			<td>
				SubGrupo
			</td>
			<td>
				Codigo
			</td>
			<td>
				Nombre
			</td>
			<td>
				Unidad
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="presupuesto_apu_categoria" style="text-align:center;"/>
			</td>
			<td>
				<div id="presupuesto_apu_grupo2"></div>
			</td>
			<td>
				<div id="presupuesto_apu_subgrupo2"></div>
			</td>
			<td>
				<li>
					<input type="text" id="presupuesto_apu_codigo_grupo" style="text-align:center;"/>
				</li>
				<li style="margin-top:8px;">
					.
				</li>
				<li>
					<input type="text" id="presupuesto_apu_codigo_subgrupo" style="text-align:center;"/>
				</li>
				<li style="margin-top:8px;">
					.
				</li>
				<li>
					<input type="text" id="presupuesto_apu_codigo2"/>
				</li>
				<!--<input type="text" id="presupuesto_apu_codigo2"/>-->
			</td>
			<td>
				<input type="text" id="presupuesto_apu_nombre2"/>
			</td>
			<td>
				<div id="presupuesto_apu_unidad"></div>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="1" style="border-left: 1px solid #A4BED4; border-top: 1px solid #A4BED4; border-right: 1px solid #A4BED4; text-align:center; width:1002px;">
		<tr>
			<td>
				<div id="presupuesto_apu_clasificacion2"></div>
			</td>
			<td>
				<div id="presupuesto_apu_codigo3"></div>
			</td>
			<td>
				<div id="presupuesto_apu_producto"></div>
			</td>
			<td>
				<div id="presupuesto_apu_cantidad"></div>
			</td>
			<td>
				<input type="text" id="presupuesto_apu_unidad2" style="text-align: center;"/>
			</td>
			<td>
				<div id="presupuesto_apu_valor"></div>
			</td>
			<td>
				<div id="presupuesto_apu_uso"></div>
			</td>
			<td>
				<input type="button" id="presupuesto_apu_add" value="Añadir">
			</td>
		</tr>
	</table>
	<div id="presupuesto_apu_items_grid"></div>
	
	<table cellpadding="2" cellspacing="1" style="margin-top:20px; margin-left: 680px;">
		<tr>
			<td>
				Total APU
			</td>
			<td colspan="2">
				<div id="presupuesto_apu_total_apu"></div>
			</td>
		</tr>
		<tr>
			<td>
				Total Materiales
			</td>
			<td colspan="2">
				<div id="presupuesto_apu_total_mat"></div>
			</td>
		</tr>
		<tr>
			<td style="width: 130px;">
				Desperdicio Materiales
			</td>
			<td>
				<div id="presupuesto_apu_desperdicios_valor"></div>
			</td>
			<td>
				<div id="presupuesto_apu_total_desperdicios"></div>
			</td>
		</tr>
		<tr>
			<td>
				Total Mano de Obra
			</td>
			<td colspan="2">
				<div id="presupuesto_apu_total_mo"></div>
			</td>
		</tr>
		<tr>
			<td>
				Herramienta Menor
			</td>
			<td>
				<div id="presupuesto_apu_gastos_valor"></div>
			</td>
			<td>
				<div id="presupuesto_apu_total_gastos"></div>
			</td>
		</tr>
		<tr>
			<td>
				Total Equipos
			</td>
			<td colspan="2">
				<div id="presupuesto_apu_total_equ"></div>
			</td>
		</tr>
		<tr>
			<td>
				Total
			</td>
			<td colspan="2">
				<div id="presupuesto_apu_total"></div>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<li>
					<input type="button" id="presupuesto_apu_nuevo" value="Nuevo">
				</li>
				<li style="padding-left:5px;">
					<input type="button" id="presupuesto_apu_guardar" value="Guardar">
				</li>
				<li style="padding-left:5px;">
					<input type="button" id="presupuesto_apu_crear" value="Crear">
				</li>
			</td>
		</tr>
	</table>
</div>

