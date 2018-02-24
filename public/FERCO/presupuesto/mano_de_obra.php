<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var ConceptoData1 = new Array();
	var ConceptoData2 = new Array();
	var ConceptoData3 = new Array();
	var ID = "";
	var Clasificacion = "";
	var Grupo = "";
	var SubGrupo = "";
	var Grupo2 = "";
	var SubGrupo2 = "";
	var Codigo = "";
	var Unidad = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Timer3 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Presupuesto_Content");
	var Body = document.getElementById("Presupuesto_MO_Content");
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
				SystemMap("APU MO", true);
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
				if ($data[$i]["Modulo"] == "Presupuesto" && $data[$i]["SubModulo"] == "Mano de Obra" && $data[$i]["Guardar"] == "true")
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
	$("#presupuesto_mo_producto1").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
		}
	});
	$("#presupuesto_mo_producto2").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row2();
		}
	});
	$("#presupuesto_mo_producto3").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row3();
		}
	});
	
	function ReDefine()
	{
		ClearJSON = [
			//{id:"presupuesto_mo_clasificacion", type:"jqxComboBox"},
			{id:"presupuesto_mo_codigo1", type:"jqxComboBox"},
			{id:"presupuesto_mo_nombre1", type:"jqxComboBox"},
			{id:"presupuesto_mo_codigo2", type:""},
			{id:"presupuesto_mo_nombre2", type:""},
			{id:"presupuesto_mo_grupo1", type:"jqxComboBox"},
			{id:"presupuesto_mo_grupo2", type:"jqxComboBox"},
			{id:"presupuesto_mo_subgrupo1", type:"jqxComboBox"},
			{id:"presupuesto_mo_subgrupo2", type:"jqxComboBox"},
			{id:"presupuesto_mo_uso", type:"jqxNumberInput"},
			{id:"presupuesto_mo_items_grid1", type:"jqxGrid"},
			{id:"presupuesto_mo_items_grid2", type:"jqxGrid"},
			{id:"presupuesto_mo_items_grid3", type:"jqxGrid"},
			{id:"presupuesto_mo_convencional", type:"jqxNumberInput"},
			{id:"presupuesto_mo_trabajador", type:"jqxNumberInput"},
			{id:"presupuesto_mo_contratista", type:"jqxNumberInput"},
			{id:"presupuesto_mo_unidad", type:"jqxComboBox"},
		];
		
		EnableDisableJSON = [
			{id:"presupuesto_mo_clasificacion", type:"jqxComboBox"},
			{id:"presupuesto_mo_codigo1", type:"jqxComboBox"},
			{id:"presupuesto_mo_nombre1", type:"jqxComboBox"},
			{id:"presupuesto_mo_back", type:"jqxButton"},
			{id:"presupuesto_mo_next", type:"jqxButton"},
			{id:"presupuesto_mo_codigo2", type:""},
			{id:"presupuesto_mo_nombre2", type:""},
			{id:"presupuesto_mo_grupo1", type:"jqxComboBox"},
			{id:"presupuesto_mo_grupo2", type:"jqxComboBox"},
			{id:"presupuesto_mo_subgrupo1", type:"jqxComboBox"},
			{id:"presupuesto_mo_subgrupo2", type:"jqxComboBox"},
			{id:"presupuesto_mo_uso", type:"jqxNumberInput"},
			{id:"presupuesto_mo_add1", type:"jqxButton"},
			{id:"presupuesto_mo_items_grid1", type:"jqxGrid"},
			{id:"presupuesto_mo_add2", type:"jqxButton"},
			{id:"presupuesto_mo_items_grid2", type:"jqxGrid"},
			{id:"presupuesto_mo_add3", type:"jqxButton"},
			{id:"presupuesto_mo_items_grid3", type:"jqxGrid"},
			{id:"presupuesto_mo_guardar", type:"jqxButton"},
			{id:"presupuesto_mo_unidad", type:"jqxComboBox"},
		];
	}
	ReDefine();
	
	function ClearDocument()
	{
		Locked = false;
		ID = "";
		Clasificacion = "";
		Grupo = "";
		SubGrupo = "";
		Grupo2 = "";
		SubGrupo2 = "";
		Codigo = "";
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
		$("#presupuesto_mo_grupo1").jqxComboBox('clearSelection');
		$("#presupuesto_mo_subgrupo1").jqxComboBox('clearSelection');
		$("#presupuesto_mo_nombre1").jqxComboBox('clearSelection');
		$("#presupuesto_mo_codigo1").jqxComboBox('clearSelection');
		$("#presupuesto_mo_grupo1").jqxComboBox('clear');
		$("#presupuesto_mo_subgrupo1").jqxComboBox('clear');
		$("#presupuesto_mo_nombre1").jqxComboBox('clear');
		$("#presupuesto_mo_codigo1").jqxComboBox('clear');
		//2
		$("#presupuesto_mo_grupo2").jqxComboBox('clearSelection');
		$("#presupuesto_mo_subgrupo2").jqxComboBox('clearSelection');
		$("#presupuesto_mo_grupo2").jqxComboBox('clear');
		$("#presupuesto_mo_subgrupo2").jqxComboBox('clear');
		$("#presupuesto_mo_unidad").jqxComboBox('clearSelection');
		//3
		$("#presupuesto_mo_producto1").jqxComboBox('clearSelection');
		$("#presupuesto_mo_producto2").jqxComboBox('clearSelection');
		$("#presupuesto_mo_producto3").jqxComboBox('clearSelection');
		$("#presupuesto_mo_producto1").jqxComboBox('clear');
		$("#presupuesto_mo_producto2").jqxComboBox('clear');
		$("#presupuesto_mo_producto3").jqxComboBox('clear');
		
		ClasificacionSource.data = {"Presupuesto_Clasificacion":true};
		ConceptoSource.data = {"Presupuesto_Conceptos":true};
		UnidadSource.data = {"Presupuesto_Unidad":true};
		
		var ClasificacionDataAdapter = new $.jqx.dataAdapter(ClasificacionSource);
		$("#presupuesto_mo_clasificacion").jqxComboBox({source: ClasificacionDataAdapter});
		
		var ConceptoDataAdapter = new $.jqx.dataAdapter(ConceptoSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
				{
					if (records[i]["Tipo"] == "SC")
						ConceptoData1.push(records[i]);
					else if (records[i]["Tipo"] == "PD")
						ConceptoData2.push(records[i]);
					else if (records[i]["Tipo"] == "SCC")
						ConceptoData3.push(records[i]);
				}
			},
			loadComplete: function (records)
			{
				$("#presupuesto_mo_producto1").jqxComboBox({source: ConceptoData1});
				$("#presupuesto_mo_producto2").jqxComboBox({source: ConceptoData2});
				$("#presupuesto_mo_producto3").jqxComboBox({source: ConceptoData3});
			}
		});
		
		var UnidadDataAdapter = new $.jqx.dataAdapter(UnidadSource);
		$("#presupuesto_mo_unidad").jqxComboBox({source: UnidadDataAdapter});
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
				{ name: 'Uso', type: 'decimal'},
				//
				{ name: 'Convencional', type: 'string'},
				{ name: 'Trabajador', type: 'string' },
				{ name: 'Contratista', type: 'string'},
				//
				{ name: 'Valor_SC', type: 'decimal' },
				{ name: 'Valor_PD', type: 'decimal' },
				{ name: 'Valor_SCC', type: 'decimal'},
			],
			data:{
				"ManodeObra_Cargar":$("#presupuesto_mo_codigo1").val(),
				"Clasificacion":$("#presupuesto_mo_clasificacion").val(),
			},
			url: "modulos/datos_productos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				var Item = $("#presupuesto_mo_nombre1").jqxComboBox('getSelectedItem');
				if (Item)
				{
					//$("#presupuesto_mo_codigo2").val(Item.value);
					$("#presupuesto_mo_codigo2").val(Item.value.substr(6));
					$("#presupuesto_mo_nombre2").val(Item.label);
				}
				$("#presupuesto_mo_grupo2").jqxComboBox('selectItem', records[0]["Grupo"]);
				ID = records[0]["ID"];
				SubGrupo2 = records[0]["SubGrupo"];
				$("#presupuesto_mo_unidad").jqxComboBox('selectItem', records[0]["Unidad"]);
				$("#presupuesto_mo_uso").val(records[0]["Uso"]);
				$("#presupuesto_mo_convencional").val(records[0]["Valor_SC"]);
				$("#presupuesto_mo_trabajador").val(records[0]["Valor_PD"]);
				$("#presupuesto_mo_contratista").val(records[0]["Valor_SCC"]);
				// Clear Grid
				$("#presupuesto_mo_items_grid1").jqxGrid("clear");
				$("#presupuesto_mo_items_grid2").jqxGrid("clear");
				$("#presupuesto_mo_items_grid3").jqxGrid("clear");
				
				if (records[0]["Convencional"] == "")
					var len = 0;
				else
					var len = records[0]["Convencional"].length;
					
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"Concepto":records[0]["Convencional"][i]["Concepto"],
						"Valor":records[0]["Convencional"][i]["Valor"],
						"Uso":records[0]["Convencional"][i]["Uso"],
						"Total":records[0]["Convencional"][i]["Total"],
						"Fijo":records[0]["Convencional"][i]["Fijo"],
					}];
					$("#presupuesto_mo_items_grid1").jqxGrid("addrow", null, datarow, "first");
				}
				
				if (records[0]["Trabajador"] == "")
					var len = 0;
				else
					var len = records[0]["Trabajador"].length;
					
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"Concepto":records[0]["Trabajador"][i]["Concepto"],
						"Valor":records[0]["Trabajador"][i]["Valor"],
						"Uso":records[0]["Trabajador"][i]["Uso"],
						"Total":records[0]["Trabajador"][i]["Total"],
						"Fijo":records[0]["Trabajador"][i]["Fijo"],
					}];
					$("#presupuesto_mo_items_grid2").jqxGrid("addrow", null, datarow, "first");
				}
				
				if (records[0]["Contratista"] == "")
					var len = 0;
				else
					var len = records[0]["Contratista"].length;
					
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"Concepto":records[0]["Contratista"][i]["Concepto"],
						"Valor":records[0]["Contratista"][i]["Valor"],
						"Uso":records[0]["Contratista"][i]["Uso"],
						"Total":records[0]["Contratista"][i]["Total"],
						"Fijo":records[0]["Contratista"][i]["Fijo"],
					}];
					$("#presupuesto_mo_items_grid3").jqxGrid("addrow", null, datarow, "first");
				}
				if (records[0]["Convencional"] != "" || records[0]["Trabajador"] != "" || records[0]["Contratista"] != "")
					Calcular();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	}
	
	function AddGrupo(Val, ID)
	{
		$("#Presupuesto_MO_Grupos_Window").jqxWindow("open");
		var items = $("#presupuesto_mo_grupo2").jqxComboBox("getItems");
		if (items.length != undefined)
			var Cod = parseInt(items[(items.length - 1)].value) + 1;
		else
			var Cod = 1;
		
		if (Cod < 10)
			Cod = "0"+Cod;
		
		$("#presupuesto_mo_codigo_add").val(Cod);
		$("#presupuesto_mo_grupo_add").val(Val);
		
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
						"Clasificacion":$("#presupuesto_mo_selected_clasificacion2").val(),
						"VAL1":"Mano de Obra",
						"VAL2":$("#presupuesto_mo_codigo_add").val(),
						"VAL3":$("#presupuesto_mo_grupo_add").val(),
					},
					success: function (data)
					{
						switch(data)
						{
							case "":
								//Update Comboboxes
								$("#presupuesto_mo_grupo1").jqxComboBox("addItem", {
									label: $("#presupuesto_mo_codigo_add").val()+". "+$("#presupuesto_mo_grupo_add").val(), 
									value: $("#presupuesto_mo_codigo_add").val()
								});
								$("#presupuesto_mo_grupo2").jqxComboBox("addItem", {
									label: $("#presupuesto_mo_codigo_add").val()+". "+$("#presupuesto_mo_grupo_add").val(), 
									value: $("#presupuesto_mo_codigo_add").val()
								});
								//$("#presupuesto_mo_grupo1").jqxComboBox("selectItem", $("#presupuesto_mo_codigo_add").val());
								$("#presupuesto_mo_grupo2").jqxComboBox("selectItem", $("#presupuesto_mo_codigo_add").val());
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
						$("#Presupuesto_MO_Grupos_Window").jqxWindow("close");
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
		$("#Presupuesto_MO_Grupos_Window").jqxWindow("open");
		var grupo = $("#presupuesto_mo_grupo2").jqxComboBox("getSelectedItem");
		if (!grupo)
			grupo = "";
		else
			grupo = grupo.value;
		var items = $("#presupuesto_mo_subgrupo2").jqxComboBox("getItems");
		if (items.length != undefined && items.value != undefined)
			var Cod = parseInt(items[(items.length - 1)].value) + 1;
		else
			var Cod = 1;
		
		if (Cod < 10)
			Cod = "0"+Cod;
		
		$("#presupuesto_mo_codigo_add").val(Cod);
		$("#presupuesto_mo_grupo_add").val(Val);
		
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
						"Clasificacion":$("#presupuesto_mo_selected_clasificacion2").val(),
						"Categoria":"Mano de Obra",
						"Grupo":grupo,
						"VAL1":$("#presupuesto_mo_codigo_add").val(),
						"VAL2":$("#presupuesto_mo_grupo_add").val(),
					},
					success: function (data)
					{
						switch(data)
						{
							case "":
								//Update Comboboxes
								$("#presupuesto_mo_subgrupo1").jqxComboBox("addItem", {
									label: $("#presupuesto_mo_codigo_add").val()+". "+$("#presupuesto_mo_grupo_add").val(), 
									value: $("#presupuesto_mo_codigo_add").val()
								});
								$("#presupuesto_mo_subgrupo2").jqxComboBox("addItem", {
									label: $("#presupuesto_mo_codigo_add").val()+". "+$("#presupuesto_mo_grupo_add").val(), 
									value: $("#presupuesto_mo_codigo_add").val()
								});
								//$("#presupuesto_mo_subgrupo1").jqxComboBox("selectItem", $("#presupuesto_mo_codigo_add").val());
								$("#presupuesto_mo_subgrupo2").jqxComboBox("selectItem", $("#presupuesto_mo_codigo_add").val());
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
						$("#Presupuesto_MO_Grupos_Window").jqxWindow("close");
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
			ManodeObraSource.data = {
				"Presupuesto_ManodeObra":true,
				"Clasificacion":$("#presupuesto_mo_clasificacion").val(),
				"Grupo":$("#presupuesto_mo_grupo1").val(),
				"SubGrupo":$("#presupuesto_mo_subgrupo1").val(),
			};
			var ManodeObraDataAdapter = new $.jqx.dataAdapter(ManodeObraSource);
			$("#presupuesto_mo_codigo1").jqxComboBox({source: ManodeObraDataAdapter});
			$("#presupuesto_mo_nombre1").jqxComboBox({source: ManodeObraDataAdapter});
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
	
	var ManodeObraSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
	};
	
	var ConceptoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Concepto', type: 'string'},
			{ name: 'Uso', type: 'decimal'},
			{ name: 'Valor', type: 'decimal'},
			{ name: 'Fijo', type: 'bool'},
			{ name: 'Tipo', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	$("#presupuesto_mo_clasificacion").jqxComboBox(
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
	$("#presupuesto_mo_clasificacion").on("change", function (event)
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
					"Categoria":"Mano de Obra",
				};
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
						$("#presupuesto_mo_grupo1").jqxComboBox({source: data});
						$("#presupuesto_mo_grupo2").jqxComboBox({source: data});
					},
				});
				LoadFilter();
			},500);
		}
		else
		{
			//ID = "";
			Clasificacion = "";
			$("#presupuesto_mo_clasificacion").jqxComboBox('clearSelection');
			$("#presupuesto_mo_grupo1").jqxComboBox('clearSelection');
			$("#presupuesto_mo_grupo2").jqxComboBox('clearSelection');
			$("#presupuesto_mo_subgrupo1").jqxComboBox('clearSelection');
			$("#presupuesto_mo_subgrupo2").jqxComboBox('clearSelection');
			$("#presupuesto_mo_nombre1").jqxComboBox('clearSelection');
			$("#presupuesto_mo_codigo1").jqxComboBox('clearSelection');
			$("#presupuesto_mo_grupo1").jqxComboBox('clear');
			$("#presupuesto_mo_grupo2").jqxComboBox('clear');
			$("#presupuesto_mo_subgrupo1").jqxComboBox('clear');
			$("#presupuesto_mo_subgrupo2").jqxComboBox('clear');
			$("#presupuesto_mo_nombre1").jqxComboBox('clear');
			$("#presupuesto_mo_codigo1").jqxComboBox('clear');
		}
	});
	$("#presupuesto_mo_clasificacion").on("bindingComplete", function (event)
	{
		if (Clasificacion != "")
			$("#presupuesto_mo_clasificacion").jqxComboBox("selectItem", Clasificacion);
	});
	document.getElementById("presupuesto_mo_clasificacion").addEventListener("dblclick", function (event)
	{
		$("#Presupuesto_MO_Clasificacion_Window").jqxWindow('open');
	});
	
	$("#presupuesto_mo_grupo1").jqxComboBox(
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
	$("#presupuesto_mo_grupo1").on("change", function (event)
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
					"P_Clasificacion":$("#presupuesto_mo_clasificacion").val(),
					"P_Categoria":"Mano de Obra",
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
						$("#presupuesto_mo_subgrupo1").jqxComboBox({source: data});
					},
				});
				LoadFilter();
			},500);
		}
		else
		{
			Grupo = "";
			SubGrupo = "";
			$("#presupuesto_mo_grupo1").jqxComboBox('clearSelection');
			$("#presupuesto_mo_subgrupo1").jqxComboBox('clearSelection');
			$("#presupuesto_mo_subgrupo1").jqxComboBox('clear');
		}
	});
	$("#presupuesto_mo_grupo1").on("bindingComplete", function (event)
	{
		if (Grupo != "")
			$("#presupuesto_mo_grupo1").jqxComboBox("selectItem", Grupo);
	});
	
	$("#presupuesto_mo_subgrupo1").jqxComboBox(
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
	$("#presupuesto_mo_subgrupo1").on("change", function (event)
	{
		if (event.args)
			SubGrupo = event.args.item.value;
		else
		{
			SubGrupo = "";
			$("#presupuesto_mo_subgrupo1").jqxComboBox('clearSelection');
		}
		LoadFilter();
	});
	$("#presupuesto_mo_subgrupo1").on("bindingComplete", function (event)
	{
		if (SubGrupo != "")
			$("#presupuesto_mo_subgrupo1").jqxComboBox("selectItem", SubGrupo);
	});
	
	$("#presupuesto_mo_codigo1").jqxComboBox(
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
	$("#presupuesto_mo_codigo1").on("change", function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#presupuesto_mo_nombre1").val() != event.args.item.value)
					$("#presupuesto_mo_nombre1").jqxComboBox('selectItem', event.args.item.value);
				
				if (Codigo == event.args.item.value)
					return;
				
				Codigo = event.args.item.value;
				
				LoadValues();
			},500);
		}
		else
		{
			var item_value = $("#presupuesto_mo_codigo1").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				//ID = "";
				Codigo = "";
				$("#presupuesto_mo_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_mo_nombre1").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#presupuesto_mo_codigo1").val();
				var item = $("#presupuesto_mo_codigo1").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					//ID = "";
					Codigo = "";
					$("#presupuesto_mo_codigo1").jqxComboBox('clearSelection');
					$("#presupuesto_mo_nombre1").jqxComboBox('clearSelection');
				}
				else
					$("#presupuesto_mo_codigo1").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#presupuesto_mo_codigo1").on("bindingComplete", function (event)
	{
		if (Codigo != "")
			$("#presupuesto_mo_codigo1").jqxComboBox("selectItem", Codigo);
	});
	
	$("#presupuesto_mo_nombre1").jqxComboBox(
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
	$("#presupuesto_mo_nombre1").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_mo_codigo1").val() != event.args.item.value)
				$("#presupuesto_mo_codigo1").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#presupuesto_mo_nombre1").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				//ID = "";
				$("#presupuesto_mo_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_mo_nombre1").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#presupuesto_mo_nombre1").val();
				
				var item = $("#presupuesto_mo_nombre1").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_mo_nombre1").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				//ID = "";
				$("#presupuesto_mo_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_mo_nombre1").jqxComboBox('clearSelection');
			}
		}
	});
	
	// --- SEARCH FILTER
	
	function Steps (TO)
	{
		var items = $("#presupuesto_mo_codigo1").jqxComboBox('getItems');
		if (!items)
			return;
		
		var len = items.length - 1;
		
		var index = $("#presupuesto_mo_codigo1").jqxComboBox('getSelectedIndex');
		
		if (TO == "next")
		{
			if (index >= len)
				Alerts_Box("Ha llegado al Final de la Busqueda.", 1);
			else
				$("#presupuesto_mo_codigo1").jqxComboBox('selectIndex',(index + 1));
		}
		else if (TO == "back")
		{
			if (index < 1)
				Alerts_Box("Ha llegado al Principio de la Busqueda.", 1);
			else
				$("#presupuesto_mo_codigo1").jqxComboBox('selectIndex',(index - 1));
		}
	}
	
	$("#presupuesto_mo_back").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_mo_back").bind('click', function ()
	{
		Steps("back")
	});
	
	$("#presupuesto_mo_next").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_mo_next").bind('click', function ()
	{
		Steps("next");
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#presupuesto_mo_categoria").jqxInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: true
	});
	$("#presupuesto_mo_categoria").val("Mano de Obra");
	
	$("#presupuesto_mo_grupo2").jqxComboBox(
	{
		theme: mytheme,
		width: 165,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Codigo',
	});
	$("#presupuesto_mo_grupo2").on("change", function (event)
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
					"P_Clasificacion":$("#presupuesto_mo_clasificacion").val(),
					"P_Categoria":"Mano de Obra",
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
						$("#presupuesto_mo_subgrupo2").jqxComboBox({source: data});
					},
				});
			},500);
			$("#presupuesto_mo_codigo_grupo").val(event.args.item.value);
			$("#presupuesto_mo_codigo_subgrupo").val("");
		}
		else
		{
			var value = $("#presupuesto_mo_grupo2 input")[0].value;
			var item_value = $("#presupuesto_mo_grupo2").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				Grupo2 = "";
				SubGrupo2 = "";
				$("#presupuesto_mo_grupo2").jqxComboBox("clearSelection");
				$("#presupuesto_mo_subgrupo2").jqxComboBox("clearSelection");
				$("#presupuesto_mo_subgrupo2").jqxComboBox("clear");
				$("#presupuesto_mo_codigo_grupo").val("");
				$("#presupuesto_mo_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe. ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddGrupo(value, "presupuesto_mo_grupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
			else
			{
				//var value = $("#presupuesto_mo_grupo2").val();
				var item = $("#presupuesto_mo_grupo2").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_mo_grupo2").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				Grupo2 = "";
				SubGrupo2 = "";
				$("#presupuesto_mo_grupo2").jqxComboBox("clearSelection");
				$("#presupuesto_mo_subgrupo2").jqxComboBox("clearSelection");
				$("#presupuesto_mo_subgrupo2").jqxComboBox("clear");
				$("#presupuesto_mo_codigo_grupo").val("");
				$("#presupuesto_mo_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe! ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddGrupo(value, "presupuesto_mo_grupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
		}
	});
	$("#presupuesto_mo_grupo2").on("bindingComplete", function (event)
	{
		if (Grupo2 != "")
			$("#presupuesto_mo_grupo2").jqxComboBox("selectItem", Grupo2);
	});
	
	$("#presupuesto_mo_subgrupo2").jqxComboBox(
	{
		theme: mytheme,
		width: 165,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'Codigo',
	});
	$("#presupuesto_mo_subgrupo2").on("change", function (event)
	{
		if (event.args)
		{
			SubGrupo2 = event.args.item.value;
			$("#presupuesto_mo_codigo_subgrupo").val(event.args.item.value);
		}
		else
		{
			var value = $("#presupuesto_mo_subgrupo2 input")[0].value;
			var item_value = $("#presupuesto_mo_subgrupo2").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				SubGrupo2 = "";
				$("#presupuesto_mo_subgrupo2").jqxComboBox('clearSelection');
				$("#presupuesto_mo_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe. ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddSubGrupo(value, "presupuesto_mo_subgrupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
			else
			{
				//var value = $("#presupuesto_mo_subgrupo2").val();
				var item = $("#presupuesto_mo_subgrupo2").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_mo_subgrupo2").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				SubGrupo2 = "";
				$("#presupuesto_mo_subgrupo2").jqxComboBox('clearSelection');
				$("#presupuesto_mo_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe! ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddSubGrupo(value, "presupuesto_mo_subgrupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
		}
	});
	$("#presupuesto_mo_subgrupo2").on("bindingComplete", function (event)
	{
		if (SubGrupo2 != "")
			$("#presupuesto_mo_subgrupo2").jqxComboBox("selectItem", SubGrupo2);
	});
	
	$("#presupuesto_mo_codigo_grupo").jqxInput({
		theme: mytheme,
		width: 20,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_mo_codigo_subgrupo").jqxInput({
		theme: mytheme,
		width: 20,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_mo_codigo2").jqxInput({
		theme: mytheme,
		width: 50,
		height: 20,
	});
	
	$("#presupuesto_mo_nombre2").jqxInput({
		theme: mytheme,
		width: 300,
		height: 20,
	});
	
	$("#presupuesto_mo_uso").jqxNumberInput({
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Calcular()
	{
		var Total1 = 0;
		var Total2 = 0;
		var Total3 = 0;
		
		var Unidad = $("#presupuesto_mo_unidad").jqxComboBox('getSelectedItem');
		
		var datinfo1 = $("#presupuesto_mo_items_grid1").jqxGrid('getdatainformation');
		var count1 = datinfo1.rowscount;
		var datinfo2 = $("#presupuesto_mo_items_grid2").jqxGrid('getdatainformation');
		var count2 = datinfo2.rowscount;
		var datinfo3 = $("#presupuesto_mo_items_grid3").jqxGrid('getdatainformation');
		var count3 = datinfo3.rowscount;
		
		if (count1 > 0)
		{
			for (var i = 0; i < count1; i++)
			{
				var Valor = parseFloat($("#presupuesto_mo_items_grid1").jqxGrid('getcellvalue', i, "Valor"));
				var Uso = parseFloat($("#presupuesto_mo_items_grid1").jqxGrid('getcellvalue', i, "Uso"));
				var Total = ((Valor / 100) * Uso);
				Total1 += Total;
				$("#presupuesto_mo_items_grid1").jqxGrid('setcellvalue', i, "Total", Total);
			}
			
			if (!Unidad)
			{
				$("#presupuesto_mo_convencional").val("0");
			}
			else
			{
				switch(Unidad.label)
				{
					case "Hora":
						$("#presupuesto_mo_convencional").val(Total1/8);
					break;
					case "Mes":
						$("#presupuesto_mo_convencional").val(Total1*30);
					break;
					default:
						$("#presupuesto_mo_convencional").val(Total1);
					break;
				}
			}
		}
		else
		{
			$("#presupuesto_mo_convencional").val("0");
		}
		
		if (count2 > 0)
		{
			for (var i = 0; i < count2; i++)
			{
				var Fijo = $("#presupuesto_mo_items_grid2").jqxGrid('getcellvalue', i, "Fijo");
				if (Fijo == false)
				{
					var Valor = Total1;
					$("#presupuesto_mo_items_grid2").jqxGrid('setcellvalue', i, "Valor", Valor);
					var Uso = parseFloat($("#presupuesto_mo_items_grid2").jqxGrid('getcellvalue', i, "Uso"));
					var Total = ((Valor / 100) * Uso);
				}
				else
				{
					var Valor = parseFloat($("#presupuesto_mo_items_grid2").jqxGrid('getcellvalue', i, "Valor"));
					var Uso = parseFloat($("#presupuesto_mo_items_grid2").jqxGrid('getcellvalue', i, "Uso"));
					var Total = ((Valor / 100) * Uso);
				}
				Total2 += Total;
				$("#presupuesto_mo_items_grid2").jqxGrid('setcellvalue', i, "Total", Total);
			}
			
			if (!Unidad)
			{
				$("#presupuesto_mo_trabajador").val("0");
			}
			else
			{
				switch(Unidad.label)
				{
					case "Hora":
						$("#presupuesto_mo_trabajador").val(Total2/8);
					break;
					case "Mes":
						$("#presupuesto_mo_trabajador").val(Total2*30);
					break;
					default:
						$("#presupuesto_mo_trabajador").val(Total2);
					break;
				}
			}
		}
		else
		{
			$("#presupuesto_mo_trabajador").val("0");
		}
		
		if (count3 > 0)
		{
			Total3 = Total1 + Total2;
			
			for (var i = 0; i < count3; i++)
			{
				var Valor = Total3;
				$("#presupuesto_mo_items_grid3").jqxGrid('setcellvalue', i, "Valor", Valor);
				var Uso = parseFloat($("#presupuesto_mo_items_grid3").jqxGrid('getcellvalue', i, "Uso"));
				var Total = ((Valor / 100) * Uso);
				$("#presupuesto_mo_items_grid3").jqxGrid('setcellvalue', i, "Total", Total);
			}
			
			if (!Unidad)
			{
				$("#presupuesto_mo_contratista").val("0");
			}
			else
			{
				switch(Unidad.label)
				{
					case "Hora":
						$("#presupuesto_mo_contratista").val(Total3/8);
					break;
					case "Mes":
						$("#presupuesto_mo_contratista").val(Total3*30);
					break;
					default:
						$("#presupuesto_mo_contratista").val(Total3);
					break;
				}
			}
		}
		else
		{
			$("#presupuesto_mo_contratista").val("0");
		}
	}
	
	function Add_Row1()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var Concepto = $("#presupuesto_mo_producto1").val();
		var Val = $("#presupuesto_mo_valor1").val();
		var Uso = $("#presupuesto_mo_uso1").val();
		
		if ( Concepto < 0 || Concepto == "") {
			Alerts_Box("Favor Seleccionar un Concepto", 3);
			WaitClick_Combobox("presupuesto_mo_producto1");
			Locked = false;
			return;
		}
		
		var len = ConceptoData1.length;
		for (var i = 0; i < len; i++)
		{
			if (ConceptoData1[i]["Concepto"] == Concepto)
			{
				var Fijo = ConceptoData1[i]["Fijo"];
				break;
			}
		}
		
		var datinfo = $("#presupuesto_mo_items_grid1").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#presupuesto_mo_items_grid1").jqxGrid('getrowdata', i);
			if (currentRow.Concepto == Concepto)
			{
				var id = $("#presupuesto_mo_items_grid1").jqxGrid('getrowid', i);
				$("#presupuesto_mo_items_grid1").jqxGrid('deleterow', id);
			}
		}
		
		var datarow = [{
			"Concepto":Concepto,
			"Uso":Uso,
			"Valor":Val,
			"Total":0,
			"Fijo":Fijo
		}];
		
		$("#presupuesto_mo_items_grid1").jqxGrid("addrow", null, datarow, "first");
		$("#presupuesto_mo_producto1").jqxComboBox('clearSelection');
		$("#presupuesto_mo_valor1").val("");
		$("#presupuesto_mo_uso1").val("");
		Locked = false;
		Calcular();
	}
	
	$("#presupuesto_mo_producto1").jqxComboBox(
	{
		theme: mytheme,
		width: 200,
		height: 20,
		source: ConceptoData1,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Concepto',
		selectedIndex: -1,
		displayMember: 'Concepto',
		valueMember: 'Concepto'
	});
	$("#presupuesto_mo_producto1").bind('change', function (event)
	{
		if (event.args)
		{
			var len = ConceptoData1.length;
			for (i = 0; i < len; i++)
			{
				if (ConceptoData1[i]["Concepto"] == event.args.item.label)
				{
					$("#presupuesto_mo_uso1").val(ConceptoData1[i]["Uso"]);
					$("#presupuesto_mo_valor1").val(ConceptoData1[i]["Valor"]);
					break;
				}
			}
		}
		else
		{
			$("#presupuesto_mo_producto1").jqxComboBox('clearSelection');
			$("#presupuesto_mo_valor1").val("0");
			$("#presupuesto_mo_uso1").val("0");
		}
	});
	
	$("#presupuesto_mo_valor1").jqxNumberInput({
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
	
	$("#presupuesto_mo_uso1").jqxNumberInput({
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
	
	$("#presupuesto_mo_add1").jqxButton({
		width: 60,
		height: 25,
		template: "success"
	});
	$("#presupuesto_mo_add1").bind('click', function ()
	{
		Add_Row1();
	});
	
	var GridSource1 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Concepto', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Uso', type: 'decimal' },
			{ name: 'Total', type: 'decimal' },
			{ name: 'Fijo', type: 'bool' },
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
	
	$("#presupuesto_mo_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 465,
		height: 200,
		source: GridSource1,
		enabletooltips: true,
		enablebrowserselection: true,
		pagesizeoptions: ['5', '10', '20', '30', '50'],
		sortable: true,
		pageable: true,
		pagesize: 5,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '6%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#presupuesto_mo_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#presupuesto_mo_items_grid1").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#presupuesto_mo_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#presupuesto_mo_items_grid1").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Concepto', datafield: 'Concepto', editable: true, width: '28%', height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: true,
				width: '26%',
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
				width: '14%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'p2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, spinButtons: false });
				}
			},
			{
				text: 'Total',
				datafield: 'Total',
				editable: false,
				width: '26%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
			},
			{ text: '', datafield: 'Fijo', columntype: 'checkbox', width: '0%', editable:false },
		],
	});
	$("#presupuesto_mo_items_grid1").jqxGrid('hidecolumn', 'Fijo');
	
	function Add_Row2()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var Concepto = $("#presupuesto_mo_producto2").val();
		var Val = $("#presupuesto_mo_valor2").val();
		var Uso = $("#presupuesto_mo_uso2").val();
		
		if ( Concepto < 0 || Concepto == "") {
			Alerts_Box("Favor Seleccionar un Concepto", 3);
			WaitClick_Combobox("presupuesto_mo_producto2");
			Locked = false;
			return;
		}
		
		var len = ConceptoData2.length;
		for (var i = 0; i < len; i++)
		{
			if (ConceptoData2[i]["Concepto"] == Concepto)
			{
				var Fijo = ConceptoData2[i]["Fijo"];
				break;
			}
		}
		
		var datinfo = $("#presupuesto_mo_items_grid1").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#presupuesto_mo_items_grid1").jqxGrid('getrowdata', i);
			if (currentRow.Concepto == Concepto)
			{
				var id = $("#presupuesto_mo_items_grid1").jqxGrid('getrowid', i);
				$("#presupuesto_mo_items_grid1").jqxGrid('deleterow', id);
			}
		}
		
		var datarow = [{
			"Concepto":Concepto,
			"Uso":Uso,
			"Valor":Val,
			"Total":0,
			"Fijo":Fijo
		}];
		
		$("#presupuesto_mo_items_grid2").jqxGrid("addrow", null, datarow, "first");
		$("#presupuesto_mo_producto2").jqxComboBox('clearSelection');
		$("#presupuesto_mo_valor2").val("");
		$("#presupuesto_mo_uso2").val("");
		Locked = false;
		Calcular();
	}
	
	$("#presupuesto_mo_producto2").jqxComboBox(
	{
		theme: mytheme,
		width: 200,
		height: 20,
		source: ConceptoData2,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Concepto',
		selectedIndex: -1,
		displayMember: 'Concepto',
		valueMember: 'Concepto'
	});
	$("#presupuesto_mo_producto2").bind('change', function (event)
	{
		if (event.args)
		{
			var len = ConceptoData2.length;
			for (var i = 0; i < len; i++)
			{
				if (ConceptoData2[i]["Concepto"] == event.args.item.label)
				{
					$("#presupuesto_mo_uso2").val(ConceptoData2[i]["Uso"]);
					$("#presupuesto_mo_valor2").val(ConceptoData2[i]["Valor"]);
					break;
				}
			}
		}
		else
		{
			$("#presupuesto_mo_producto2").jqxComboBox('clearSelection');
			$("#presupuesto_mo_valor2").val("0");
			$("#presupuesto_mo_uso2").val("0");
		}
	});
	
	$("#presupuesto_mo_valor2").jqxNumberInput({
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
	
	$("#presupuesto_mo_uso2").jqxNumberInput({
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
	
	$("#presupuesto_mo_add2").jqxButton({
		width: 60,
		height: 25,
		template: "success"
	});
	$("#presupuesto_mo_add2").bind('click', function ()
	{
		Add_Row2();
	});
	
	var GridSource2 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Concepto', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Uso', type: 'decimal' },
			{ name: 'Total', type: 'decimal' },
			{ name: 'Fijo', type: 'bool' },
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
	
	$("#presupuesto_mo_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 465,
		height: 200,
		source: GridSource2,
		enabletooltips: true,
		enablebrowserselection: true,
		pagesizeoptions: ['5', '10', '20', '30', '50'],
		sortable: true,
		pageable: true,
		pagesize: 5,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '6%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#presupuesto_mo_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#presupuesto_mo_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#presupuesto_mo_items_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#presupuesto_mo_items_grid2").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Concepto', datafield: 'Concepto', editable: false, width: '28%', height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: false,
				width: '26%',
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
				width: '14%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'p2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, spinButtons: false });
				}
			},
			{
				text: 'Total',
				datafield: 'Total',
				editable: false,
				width: '26%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
			},
			{ text: '', datafield: 'Fijo', columntype: 'checkbox', width: '0%', editable:false },
		],
	});
	$("#presupuesto_mo_items_grid2").jqxGrid('hidecolumn', 'Fijo');
	
	function Add_Row3()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var Concepto = $("#presupuesto_mo_producto3").val();
		var Val = $("#presupuesto_mo_valor3").val();
		var Uso = $("#presupuesto_mo_uso3").val();
		
		if ( Concepto < 0 || Concepto == "") {
			Alerts_Box("Favor Seleccionar un Concepto", 3);
			WaitClick_Combobox("presupuesto_mo_producto3");
			Locked = false;
			return;
		}
		
		/*if ( Val < 0 || Val == "") {
			Alerts_Box("Ocurrio un Error al Cargar el Valor del Concepto!<br/>Intente Recargando la Pagina", 3);
			Locked = false;
			return;
		}
		
		if ( Uso < 0 || Uso == "") {
			Alerts_Box("Ocurrio un Error al Cargar el Uso(%) del Concepto!<br/>Intente Recargando la Pagina", 3);
			Locked = false;
			return;
		}*/
		
		var len = ConceptoData3.length;
		for (var i = 0; i < len; i++)
		{
			if (ConceptoData3[i]["Concepto"] == Concepto)
			{
				var Fijo = ConceptoData3[i]["Fijo"];
				break;
			}
		}
		
		var datinfo = $("#presupuesto_mo_items_grid1").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#presupuesto_mo_items_grid1").jqxGrid('getrowdata', i);
			if (currentRow.Concepto == Concepto)
			{
				var id = $("#presupuesto_mo_items_grid1").jqxGrid('getrowid', i);
				$("#presupuesto_mo_items_grid1").jqxGrid('deleterow', id);
			}
		}
		
		var datarow = [{
			"Concepto":Concepto,
			"Uso":Uso,
			"Valor":Val,
			"Total":0,
			"Fijo":Fijo
		}];
		
		$("#presupuesto_mo_items_grid3").jqxGrid("addrow", null, datarow, "first");
		$("#presupuesto_mo_producto3").jqxComboBox('clearSelection');
		$("#presupuesto_mo_valor3").val("");
		$("#presupuesto_mo_uso3").val("");
		Locked = false;
		Calcular();
	}
	
	$("#presupuesto_mo_producto3").jqxComboBox(
	{
		theme: mytheme,
		width: 200,
		height: 20,
		source: ConceptoData3,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Concepto',
		selectedIndex: -1,
		displayMember: 'Concepto',
		valueMember: 'Concepto'
	});
	$("#presupuesto_mo_producto3").bind('change', function (event)
	{
		if (event.args)
		{
			var len = ConceptoData3.length;
			for (i = 0; i < len; i++)
			{
				if (ConceptoData3[i]["Concepto"] == event.args.item.label)
				{
					$("#presupuesto_mo_uso3").val(ConceptoData3[i]["Uso"]);
					$("#presupuesto_mo_valor3").val(ConceptoData3[i]["Valor"]);
					break;
				}
			}
		}
		else
		{
			$("#presupuesto_mo_producto3").jqxComboBox('clearSelection');
			$("#presupuesto_mo_valor3").val("0");
			$("#presupuesto_mo_uso3").val("0");
		}
	});
	
	$("#presupuesto_mo_valor3").jqxNumberInput({
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
	
	$("#presupuesto_mo_uso3").jqxNumberInput({
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
	
	$("#presupuesto_mo_add3").jqxButton({
		width: 60,
		height: 25,
		template: "success"
	});
	$("#presupuesto_mo_add3").bind('click', function ()
	{
		Add_Row3();
	});
	
	var GridSource3 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Concepto', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Uso', type: 'decimal' },
			{ name: 'Total', type: 'decimal' },
			{ name: 'Fijo', type: 'bool' },
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
	
	$("#presupuesto_mo_items_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 465,
		height: 200,
		source: GridSource3,
		enabletooltips: true,
		enablebrowserselection: true,
		pagesizeoptions: ['5', '10', '20', '30', '50'],
		sortable: true,
		pageable: true,
		pagesize: 5,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '6%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#presupuesto_mo_items_grid3").jqxGrid('getselectedrowindex');
					var rowscount = $("#presupuesto_mo_items_grid3").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#presupuesto_mo_items_grid3").jqxGrid('getrowid', selectedrowindex);
						$("#presupuesto_mo_items_grid3").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Concepto', datafield: 'Concepto', editable: false, width: '28%', height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: false,
				width: '26%',
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
				editable: false,
				width: '14%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'p2',
				columntype: 'numberinput',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, spinButtons: false });
				}
			},
			{
				text: 'Total',
				datafield: 'Total',
				editable: false,
				width: '26%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
			},
			{ text: '', datafield: 'Fijo', columntype: 'checkbox', width: '0%', editable:false },
		],
	});
	$("#presupuesto_mo_items_grid3").jqxGrid('hidecolumn', 'Fijo');
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 4 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function SaveData(Crear)
	{
		if (Locked)
			return;
		
		Locked = true;
		var FinalArray = new Array();
		var Convencional = new Array();
		var Trabajador = new Array();
		var Contratista = new Array();
		
		var Clasificacion = $("#presupuesto_mo_clasificacion").jqxComboBox('getSelectedItem');
		if (!Clasificacion)
		{
			Alerts_Box("Debe Seleccionar una Clasificacion", 3);
			WaitClick_Combobox("presupuesto_mo_clasificacion");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_mo_codigo2").val() < 1 || $("#presupuesto_mo_codigo2").val() == "")
		{
			Alerts_Box("Debe Ingresar un Codigo.", 3);
			WaitClick_Input("presupuesto_mo_codigo2");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_mo_nombre2").val() < 1 || $("#presupuesto_mo_nombre2").val() == "")
		{
			Alerts_Box("Debe Ingresar un Nombre.", 3);
			WaitClick_Input("presupuesto_mo_nombre2");
			Locked = false;
			return;
		}
		
		var Grupo = $("#presupuesto_mo_grupo2").jqxComboBox('getSelectedItem');
		if (!Grupo)
		{
			Alerts_Box("Debe Seleccionar un Grupo", 3);
			WaitClick_Combobox("presupuesto_mo_grupo2");
			Locked = false;
			return;
		}
		
		var SubGrupo = $("#presupuesto_mo_subgrupo2").jqxComboBox('getSelectedItem');
		if (!SubGrupo)
		{
			Alerts_Box("Debe Seleccionar un SubGrupo", 3);
			WaitClick_Combobox("presupuesto_mo_subgrupo2");
			Locked = false;
			return;
		}
		
		var Unidad = $("#presupuesto_mo_unidad").jqxComboBox('getSelectedItem');
		if (!Unidad)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Unidad", 3);
			WaitClick_Combobox("presupuesto_mo_unidad");
			Locked = false;
			return;
		}
		
		var datinfo1 = $("#presupuesto_mo_items_grid1").jqxGrid('getdatainformation');
		var count1 = datinfo1.rowscount;
		var datinfo2 = $("#presupuesto_mo_items_grid2").jqxGrid('getdatainformation');
		var count2 = datinfo2.rowscount;
		var datinfo3 = $("#presupuesto_mo_items_grid3").jqxGrid('getdatainformation');
		var count3 = datinfo3.rowscount;

		if (count1 < 1) {
			Alerts_Box("Salario Convencional, debe poseer al menos 1 concepto.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		else
		{
			for (var i = 0; i < count1; i++)
			{
				var tmp_array = {};
				var currentRow = $("#presupuesto_mo_items_grid1").jqxGrid('getrowdata', i);
				
				tmp_array["Concepto"] = currentRow.Concepto;
				tmp_array["Valor"] = currentRow.Valor;
				tmp_array["Uso"] = currentRow.Uso;
				tmp_array["Total"] = currentRow.Total;
				tmp_array["Fijo"] = currentRow.Fijo;
				
				Convencional[i] = tmp_array;
			}
		}
		
		if (count2 < 1)
		{
			Alerts_Box("Pagos por dia de Trabajador, debe poseer al menos 1 concepto.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		else
		{
			for (var i = 0; i < count2; i++)
			{
				var tmp_array = {};
				var currentRow = $("#presupuesto_mo_items_grid2").jqxGrid('getrowdata', i);
				
				tmp_array["Concepto"] = currentRow.Concepto;
				tmp_array["Valor"] = currentRow.Valor;
				tmp_array["Uso"] = currentRow.Uso;
				tmp_array["Total"] = currentRow.Total;
				tmp_array["Fijo"] = currentRow.Fijo;
				
				Trabajador[i] = tmp_array;
			}
		}
		
		if (count3 < 1)
		{
			Alerts_Box("Salario Contratista, debe poseer al menos 1 concepto.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		else
		{
			for (var i = 0; i < count3; i++)
			{
				var tmp_array = {};
				var currentRow = $("#presupuesto_mo_items_grid3").jqxGrid('getrowdata', i);
				
				tmp_array["Concepto"] = currentRow.Concepto;
				tmp_array["Valor"] = currentRow.Valor;
				tmp_array["Uso"] = currentRow.Uso;
				tmp_array["Total"] = currentRow.Total;
				tmp_array["Fijo"] = currentRow.Fijo;
				
				Contratista[i] = tmp_array;
			}
		}
		
		var myarray = {};
		
		if (!Crear)
			myarray["ID"] = ID;
		
		myarray["Clasificacion"] = Clasificacion.value;
		myarray["Codigo"] = Grupo.value + "." + SubGrupo.value + "." + $("#presupuesto_mo_codigo2").val();
		myarray["Codigo2"] = $("#presupuesto_mo_codigo2").val();
		myarray["Nombre"] = $("#presupuesto_mo_nombre2").val();
		myarray["Grupo"] = Grupo.value;
		myarray["SubGrupo"] = SubGrupo.value;
		myarray["Unidad"] = Unidad.value;
		myarray["Uso"] = $("#presupuesto_mo_uso").val();
		myarray["Convencional"] = Convencional;
		myarray["Trabajador"] = Trabajador;
		myarray["Contratista"] = Contratista;
		myarray["Valor_SC"] = $("#presupuesto_mo_convencional").val();
		myarray["Valor_PD"] = $("#presupuesto_mo_trabajador").val();
		myarray["Valor_SCC"] = $("#presupuesto_mo_contratista").val();
		
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
			data: {"ManodeObra_Crear":FinalArray},
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
	
	var UnidadSource = [
		{"Unidad":"Hh", "Nombre":"Hora"},
		{"Unidad":"DIA", "Nombre":"Dia"},
		{"Unidad":"Mes", "Nombre":"Mes"},
	];
	
	$("#presupuesto_mo_unidad").jqxComboBox(
	{
		theme: mytheme,
		width: 70,
		height: 20,
		source: UnidadSource,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Selec...',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Unidad'
	});
	$("#presupuesto_mo_unidad").on("change", function (event)
	{
		if (event.args)
			Unidad = event.args.item.value;
		else
		{
			Unidad = "";
			$("#presupuesto_mo_unidad").jqxComboBox('clearSelection');
		}
		Calcular();
	});
	$("#presupuesto_mo_unidad").on("bindingComplete", function (event)
	{
		if (Unidad != "")
			$("#presupuesto_mo_unidad").jqxComboBox("selectItem", Unidad);
	});
	
	$("#presupuesto_mo_convencional").jqxNumberInput({
		theme: mytheme,
		width: 160,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_mo_trabajador").jqxNumberInput({
		theme: mytheme,
		width: 160,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_mo_contratista").jqxNumberInput({
		theme: mytheme,
		width: 160,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#presupuesto_mo_nuevo").jqxButton({
		width: 140,
		height: 25,
		template: "success"
	});
	$("#presupuesto_mo_nuevo").bind('click', function ()
	{
		ClearDocument()
	});
	
	$("#presupuesto_mo_guardar").jqxButton({
		width: 150,
		height: 25,
		template: "info"
	});
	$("#presupuesto_mo_guardar").bind('click', function ()
	{
		SaveData();
	});
	
	$("#presupuesto_mo_crear").jqxButton({
		width: 160,
		height: 25,
		template: "primary"
	});
	$("#presupuesto_mo_crear").bind('click', function ()
	{
		SaveData(true);
	});
	
	
	//----- Windows
	$("#Presupuesto_MO_Clasificacion_Window").jqxWindow({
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
			$("#presupuesto_mo_clasificacion_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_mo_clasificacion_button").trigger("click");
				}
			});
			
			$("#presupuesto_mo_clasificacion_form").jqxValidator({
				rules: [
					{ input: "#presupuesto_mo_clasificacion_add", message: "Se Requiere un Nombre de Clasificacion!", action: "keyup, blur", rule: "required" },
					{ input: "#presupuesto_mo_notas", message: "Se Requiere un Concepto!", action: "keyup, blur", rule: "required" },
				]
			});
			
			$("#presupuesto_mo_selected_clasificacion").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
				disabled: true,
			});
			
			$("#presupuesto_mo_clasificacion_add").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
			});
	
			$("#presupuesto_mo_clasificacion_button1").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "primary"
			});
			$("#presupuesto_mo_clasificacion_button1").bind("click", function ()
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
								"From_Clasificacion":$("#presupuesto_mo_selected_clasificacion").val(),
								"New_Clasificacion":$("#presupuesto_mo_clasificacion_add").val(),
								"Nota":$("#presupuesto_mo_notas").val()
							},
							success: function (data, status, xhr)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								if (data == "")
								{
									Alerts_Box("Datos Guardados con Exito!", 2);
									$("#presupuesto_mo_selected_clasificacion").val("");
									$("#presupuesto_mo_clasificacion_add").val("");
									$("#presupuesto_mo_notas").val("");
									$("#Presupuesto_MO_Clasificacion_Window").jqxWindow("close");
								}
								else if (data == "EXIST")
								{
									Alerts_Box("Ya existe una Clasificacion con el Mismo Nombre<br/>Favor Ingresar Otro.", 4);
								}
								else
								{
									Alerts_Box("Ha ocurrido un error mientras se guardaban los datos!<br/>Intente luego de unos segundos", 3);
									$("#presupuesto_mo_selected_clasificacion").val("");
									$("#presupuesto_mo_clasificacion_add").val("");
									$("#presupuesto_mo_notas").val("");
									$("#Presupuesto_MO_Clasificacion_Window").jqxWindow("close");
								}
							},
							error: function (jqXHR, textStatus, errorThrown)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								alert(textStatus+ " - " +errorThrown);
								$("#presupuesto_mo_selected_clasificacion").val("");
								$("#presupuesto_mo_clasificacion_add").val("");
								$("#presupuesto_mo_notas").val("");
								$("#Presupuesto_MO_Clasificacion_Window").jqxWindow("close");
							}
						});
					}
					else
						Locked = false;
				}
				$("#presupuesto_mo_clasificacion_form").jqxValidator("validate", validationResult);
			});
			
			$("#presupuesto_mo_clasificacion_button2").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "info"
			});
			$("#presupuesto_mo_clasificacion_button2").bind("click", function ()
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
								"Clasificacion":$("#presupuesto_mo_clasificacion_add").val(),
								"Nota":$("#presupuesto_mo_notas").val()
							},
							success: function (data, status, xhr)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								if (data == "")
								{
									Alerts_Box("Datos Guardados con Exito!", 2);
									$("#presupuesto_mo_selected_clasificacion").val("");
									$("#presupuesto_mo_clasificacion_add").val("");
									$("#presupuesto_mo_notas").val("");
									$("#Presupuesto_MO_Clasificacion_Window").jqxWindow("close");
								}
								else if (data == "EXIST")
								{
									Alerts_Box("Ya existe una Clasificacion con el Mismo Nombre<br/>Favor Ingresar Otro.", 4);
								}
								else
								{
									Alerts_Box("Ha ocurrido un error mientras se guardaban los datos!<br/>Intente luego de unos segundos", 3);
									$("#presupuesto_mo_selected_clasificacion").val("");
									$("#presupuesto_mo_clasificacion_add").val("");
									$("#presupuesto_mo_notas").val("");
									$("#Presupuesto_MO_Clasificacion_Window").jqxWindow("close");
								}
							},
							error: function (jqXHR, textStatus, errorThrown)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								alert(textStatus+ " - " +errorThrown);
								$("#presupuesto_mo_selected_clasificacion").val("");
								$("#presupuesto_mo_clasificacion_add").val("");
								$("#presupuesto_mo_notas").val("");
								$("#Presupuesto_MO_Clasificacion_Window").jqxWindow("close");
							}
						});
					}
					else
						Locked = false;
				}
				$("#presupuesto_mo_clasificacion_form").jqxValidator("validate", validationResult);
			});
		}
	});
	$("#Presupuesto_MO_Clasificacion_Window").on('open', function ()
	{
		var Item = $("#presupuesto_mo_clasificacion").jqxComboBox('getSelectedItem');
		if (Item)
			var Val = Item.value;
		else
			var Val = "";
		$("#presupuesto_mo_selected_clasificacion").val(Val);
	});
	$("#Presupuesto_MO_Clasificacion_Window").on('close', function () {
		LoadParameters();
	});
	
	$("#Presupuesto_MO_Grupos_Window").jqxWindow({
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
			$("#presupuesto_mo_codigo_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_mo_grupo_add").focus();
				}
			});
			
			$("#presupuesto_mo_grupo_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_mo_grupo_button").trigger("click");
				}
			});
			
			$("#presupuesto_mo_grupo_form").jqxValidator({
				rules: [
					{ input: "#presupuesto_mo_codigo_add", message: "Se Requiere un Codigo!", action: "keyup, blur", rule: "required" },
					{ input: "#presupuesto_mo_grupo_add", message: "Se Requiere un Nombre!", action: "keyup, blur", rule: "required" },
				]
			});
			
			$("#presupuesto_mo_selected_clasificacion2").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
				disabled: true,
			});
			
			$("#presupuesto_mo_codigo_add").jqxInput({
				theme: mytheme,
				width: 40,
				height: 20,
			});
			
			$("#presupuesto_mo_grupo_add").jqxInput({
				theme: mytheme,
				width: 190,
				height: 20,
			});
	
			$("#presupuesto_mo_grupo_button").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "primary"
			});
			$("#presupuesto_mo_grupo_button").on("click", function ()
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
				$("#presupuesto_mo_grupo_form").jqxValidator("validate", validationResult);
			});
		}
	});
	$("#Presupuesto_MO_Grupos_Window").on("open", function ()
	{
		var Item = $("#presupuesto_mo_clasificacion").jqxComboBox("getSelectedItem");
		if (Item)
			var Val = Item.value;
		else
			var Val = "";
		$("#presupuesto_mo_selected_clasificacion2").val(Val);
	});
	
	Clasificacion = "originales";
	
	LoadParameters();
	CheckRefresh();
});
</script>
<div id="Presupuesto_MO_Clasificacion_Window">
	<div id="Presupuesto_MO_Clasificacion_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 20px;">Crear Nueva Base de Datos</div>
	</div>
	<div id="Presupuesto_MO_Clasificacion_Window_Content" class="WindowContainer" align="center">
		<form id="presupuesto_mo_clasificacion_form" onsubmit="return false;">
			<table cellspacing="2" cellpadding="5" align="center">
				<tr>
					<td colspan="2">
						Base de Datos Seleccionada
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_mo_selected_clasificacion"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Nombre de la Base de Datos
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_mo_clasificacion_add"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Notas
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea rows="3" cols="32" maxlength="200" style="resize:none;" id="presupuesto_mo_notas"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<input id="presupuesto_mo_clasificacion_button1" type="button" value="Crear Copia" />
					</td>
					<td>
						<input id="presupuesto_mo_clasificacion_button2" type="button" value="Crear Nueva" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div id="Presupuesto_MO_Grupos_Window">
	<div id="Presupuesto_MO_Grupos_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 35px;">Agregar Nuevo Valor</div>
	</div>
	<div id="Presupuesto_MO_Grupos_Window_Content" class="WindowContainer" align="center">
		<form id="presupuesto_mo_grupo_form" onsubmit="return false;">
			<table cellspacing="2" cellpadding="5" align="center">
				<tr>
					<td colspan="2">
						Base de Datos Seleccionada
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_mo_selected_clasificacion2"/>
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
						<input type="text" id="presupuesto_mo_codigo_add"/>
					</td>
					<td>
						<input type="text" id="presupuesto_mo_grupo_add"/>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<input id="presupuesto_mo_grupo_button" type="button" value="Crear" />
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
				<div id="presupuesto_mo_clasificacion"></div>
			</td>
			<td>
				Grupo
			</td>
			<td>
				<li>
					<div id="presupuesto_mo_grupo1"></div>
				</li>
				<li style="padding-top:5px; padding-left:4px;">
					SubGrupo
				</li>
				<li style="padding-left:4px;">
					<div id="presupuesto_mo_subgrupo1"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td rowspan="2">
				<li>
					<input type="button" id="presupuesto_mo_back" value="<< Ant."/>
				</li>
				<li style="padding:0px 0px 0px 10px;">
					<input type="button" id="presupuesto_mo_next" value="Sig. >>"/>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Codigo
			</td>
			<td>
				<div id="presupuesto_mo_codigo1"></div>
			</td>
			<td>
				Nombre
			</td>
			<td>
				<div id="presupuesto_mo_nombre1"></div>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 950px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
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
				Rendimiento
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="presupuesto_mo_categoria" style="text-align:center;"/>
			</td>
			<td>
				<div id="presupuesto_mo_grupo2"></div>
			</td>
			<td>
				<div id="presupuesto_mo_subgrupo2"></div>
			</td>
			<td>
				<li>
					<input type="text" id="presupuesto_mo_codigo_grupo" style="text-align:center;"/>
				</li>
				<li style="margin-top:8px;">
					.
				</li>
				<li>
					<input type="text" id="presupuesto_mo_codigo_subgrupo" style="text-align:center;"/>
				</li>
				<li style="margin-top:8px;">
					.
				</li>
				<li>
					<input type="text" id="presupuesto_mo_codigo2"/>
				</li>
			</td>
			<!--<td>
				<input type="text" id="presupuesto_mo_codigo2"/>
			</td>-->
			<td>
				<input type="text" id="presupuesto_mo_nombre2"/>
			</td>
			<td>
				<div id="presupuesto_mo_uso"></div>
			</td>
		</tr>
	</table>
	
	<table cellpadding="0" cellspacing="0" style="margin-bottom:20px; text-align:center;">
		<tr>
			<td style="text-align:left;">
				<div style="width: 100%; height: 20px; background-color: lightslategray; color: white; text-align:left;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">1. SALARIO CONVENCIONAL<!--SALARIO BASE--> </p>
				</div>
				<li style="margin:5px 0px;">
					<div id="presupuesto_mo_producto1"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<div id="presupuesto_mo_valor1"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<div id="presupuesto_mo_uso1"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<input type="button" id="presupuesto_mo_add1" value="Añadir">
				</li>
			</td>
			<td colspan="3" style="text-align:left; padding-left:20px;">
				<div style="width: 100%; height: 20px; background-color: #27A227; color: white; text-align:left;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">2. PAGOS POR DIA TRABAJADOR<!--PRESTACIONES, PARAFISCALES Y OTROS--> </p>
				</div>
				<li style="margin:5px 0px;">
					<div id="presupuesto_mo_producto2"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<div id="presupuesto_mo_valor2"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<div id="presupuesto_mo_uso2"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<input type="button" id="presupuesto_mo_add2" value="Añadir">
				</li>
			</td>
		</tr>
		<tr>
			<td>
				<div id="presupuesto_mo_items_grid1"></div>
			</td>
			<td colspan="3" style="padding-left:20px;">
				<div id="presupuesto_mo_items_grid2"></div>
			</td>
		</tr>
		<tr>
			<td style="text-align:left; padding-top:20px;">
				<div style="width: 100%; height: 20px; background-color: #4788C5; color: white; text-align:left;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">3. SALARIO CONTRATISTA</p>
				</div>
				<li style="margin:5px 0px;">
					<div id="presupuesto_mo_producto3"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<div id="presupuesto_mo_valor3"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<div id="presupuesto_mo_uso3"></div>
				</li>
				<li style="padding-left:5px; margin:5px 0px;">
					<input type="button" id="presupuesto_mo_add3" value="Añadir">
				</li>
			</td>
			<td style="width:140px;">
				&nbsp;
			</td>
			<td style="text-align:left; padding-left:20px;">
				<b>VALORES TOTALES</b>
			</td>
			<td>
				<li style="padding-top:5px; text-align:right;">
					Tipo de Unidad
				</li>
				<li style="padding-left:5px;">
					<div id="presupuesto_mo_unidad"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td rowspan="6">
				<div id="presupuesto_mo_items_grid3"></div>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
			<td style="padding-left:10px;">
				<div style="width: 100%; height: 20px; background-color: #4788C5; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">Valores</p>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td style="text-align:left; padding-left:20px; width:152px;">
				Total Salario Convencional
			</td>
			<td style="padding-left:10px;">
				<div id="presupuesto_mo_convencional"></div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td style="text-align:left; padding-left:20px;">
				Total Pagos al Trabajador
			</td>
			<td style="padding-left:10px;">
				<div id="presupuesto_mo_trabajador"></div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td style="text-align:left; padding-left:20px;">
				Total Salario Contratista
			</td>
			<td style="padding-left:10px;">
				<div id="presupuesto_mo_contratista"></div>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="presupuesto_mo_nuevo" value="Nuevo" style="margin-left:10px;">
			</td>
			<td style="padding-left:10px;">
				<input type="button" id="presupuesto_mo_guardar" value="Guardar">
			</td>
			<td style="padding-left:10px;">
				<input type="button" id="presupuesto_mo_crear" value="Crear">
			</td>
		</tr>
	</table>
</div>

