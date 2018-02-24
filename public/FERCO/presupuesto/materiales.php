<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var ProveedoresData = new Array();
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
	var Body = document.getElementById("Presupuesto_MAT_Content");
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
				SystemMap("Materiales", true);
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
				if ($data[$i]["Modulo"] == "Presupuesto" && $data[$i]["SubModulo"] == "Materiales" && $data[$i]["Guardar"] == "true")
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
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"presupuesto_mat_codigo1", type:"jqxComboBox"},
			{id:"presupuesto_mat_nombre1", type:"jqxComboBox"},
			{id:"presupuesto_mat_codigo2", type:""},
			{id:"presupuesto_mat_nombre2", type:""},
			{id:"presupuesto_mat_grupo1", type:"jqxComboBox"},
			{id:"presupuesto_mat_grupo2", type:"jqxComboBox"},
			{id:"presupuesto_mat_subgrupo1", type:"jqxComboBox"},
			{id:"presupuesto_mat_subgrupo2", type:"jqxComboBox"},
			{id:"presupuesto_mat_unidad", type:"jqxComboBox"},
			{id:"presupuesto_mat_valor", type:"jqxNumberInput"},
			{id:"presupuesto_mat_uso", type:"jqxNumberInput"},
			{id:"presupuesto_mat_items_grid", type:"jqxGrid"},
			{id:"presupuesto_mat_notas", type:""},
		];
		
		EnableDisableJSON = [
			{id:"presupuesto_mat_clasificacion", type:"jqxComboBox"},
			{id:"presupuesto_mat_codigo1", type:"jqxComboBox"},
			{id:"presupuesto_mat_nombre1", type:"jqxComboBox"},
			{id:"presupuesto_mat_back", type:"jqxButton"},
			{id:"presupuesto_mat_next", type:"jqxButton"},
			{id:"presupuesto_mat_codigo2", type:""},
			{id:"presupuesto_mat_nombre2", type:""},
			{id:"presupuesto_mat_grupo1", type:"jqxComboBox"},
			{id:"presupuesto_mat_grupo2", type:"jqxComboBox"},
			{id:"presupuesto_mat_subgrupo1", type:"jqxComboBox"},
			{id:"presupuesto_mat_subgrupo2", type:"jqxComboBox"},
			{id:"presupuesto_mat_unidad", type:"jqxComboBox"},
			{id:"presupuesto_mat_valor", type:"jqxNumberInput"},
			{id:"presupuesto_mat_uso", type:"jqxNumberInput"},
			{id:"presupuesto_mat_add", type:"jqxButton"},
			{id:"presupuesto_mat_items_grid", type:"jqxGrid"},
			{id:"presupuesto_mat_guardar", type:"jqxButton"},
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
		$("#presupuesto_mat_uso").val("100");
		var img = "<img id=\"presupuesto_mat_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/search.png\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
		$("#presupuesto_mat_producto_foto").html(img);
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	}
	
	function LoadParameters()
	{
		//1
		$("#presupuesto_mat_grupo1").jqxComboBox('clearSelection');
		$("#presupuesto_mat_subgrupo1").jqxComboBox('clearSelection');
		$("#presupuesto_mat_nombre1").jqxComboBox('clearSelection');
		$("#presupuesto_mat_codigo1").jqxComboBox('clearSelection');
		$("#presupuesto_mat_grupo1").jqxComboBox('clear');
		$("#presupuesto_mat_subgrupo1").jqxComboBox('clear');
		$("#presupuesto_mat_nombre1").jqxComboBox('clear');
		$("#presupuesto_mat_codigo1").jqxComboBox('clear');
		//2
		$("#presupuesto_mat_grupo2").jqxComboBox('clearSelection');
		$("#presupuesto_mat_subgrupo2").jqxComboBox('clearSelection');
		$("#presupuesto_mat_grupo2").jqxComboBox('clear');
		$("#presupuesto_mat_subgrupo2").jqxComboBox('clear');
		$("#presupuesto_mat_unidad").jqxComboBox('clearSelection');
		
		ClasificacionSource.data = {"Presupuesto_Clasificacion":true};
		UnidadSource.data = {"Presupuesto_Unidad":true};
		ProveedorSource.data = {"Clientes_Proveedores":true};
		
		var ClasificacionDataAdapter = new $.jqx.dataAdapter(ClasificacionSource);
		$("#presupuesto_mat_clasificacion").jqxComboBox({source: ClasificacionDataAdapter});
		
		var UnidadDataAdapter = new $.jqx.dataAdapter(UnidadSource);
		$("#presupuesto_mat_unidad").jqxComboBox({source: UnidadDataAdapter});
		
		var ProveedorDataAdapter = new $.jqx.dataAdapter(ProveedorSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
					ProveedoresData.push(records[i]);
			},
			loadComplete: function (records)
			{
				$("#presupuesto_mat_proveedor").jqxComboBox({source: records});
				$("#presupuesto_mat_proveedorID").jqxComboBox({source: records});
			}
		});
		$("#presupuesto_mat_uso").val("100");
	}
	
	function LoadValues()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: "ID", type: "string"},
				{ name: "Grupo", type: "string"},
				{ name: "SubGrupo", type: "string"},
				{ name: "Uso", type: "decimal"},
				{ name: "Unidad", type: "string"},
				{ name: "Peso", type: "decimal"},
				{ name: "Valor", type: "decimal"},
				{ name: "Valor_KM", type: "decimal"},
				{ name: "Imagen", type: "string"},
				{ name: "Notas", type: "string"},
				//
				{ name: "Proveedores", type: "string"},
			],
			data:{
				"Presupuesto_Materiales_Cargar":$("#presupuesto_mat_codigo1").val(),
				"Clasificacion":$("#presupuesto_mat_clasificacion").val(),
			},
			url: "modulos/datos_productos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				var Item = $("#presupuesto_mat_nombre1").jqxComboBox('getSelectedItem');
				if (Item)
				{
					$("#presupuesto_mat_codigo2").val(Item.value.substr(6));
					$("#presupuesto_mat_nombre2").val(Item.label);
				}
				$("#presupuesto_mat_grupo2").jqxComboBox('selectItem', records[0]["Grupo"]);
				ID = records[0]["ID"];
				SubGrupo2 = records[0]["SubGrupo"];
				$("#presupuesto_mat_unidad").jqxComboBox('selectItem', records[0]["Unidad"]);
				$("#presupuesto_mat_peso").val(records[0]["Peso"]);
				$("#presupuesto_mat_valor").val(records[0]["Valor"]);
				$("#presupuesto_mat_valor_km").val(records[0]["Valor_KM"]);
				$("#presupuesto_mat_uso").val(records[0]["Uso"]);
				
				if (records[0]["Imagen"] != "")
				{
					var img = "<img id=\"presupuesto_mat_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/"+records[0]["Imagen"]+"\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
					$("#presupuesto_mat_producto_foto").html(img);
				}
				else
				{
					var img = "<img id=\"presupuesto_mat_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/search.png\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
					$("#presupuesto_mat_producto_foto").html(img);
				}
				
				$("#presupuesto_mat_notas").val(records[0]["Notas"]);
				// Clear Grid
				$("#presupuesto_mat_items_grid").jqxGrid("clear");
				
				if (records[0]["Proveedores"] == "")
					var len = 0;
				else
					var len = records[0]["Proveedores"].length;
					
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"Proveedor":records[0]["Proveedores"][i]["Proveedor"],
						"ProveedorID":records[0]["Proveedores"][i]["ProveedorID"],
						"Codigo":records[0]["Proveedores"][i]["Codigo"],
					}];
					$("#presupuesto_mat_items_grid").jqxGrid("addrow", null, datarow, "first");
				}
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	}
	
	function AddGrupo(Val, ID)
	{
		$("#Presupuesto_MAT_Grupos_Window").jqxWindow("open");
		var items = $("#presupuesto_mat_grupo2").jqxComboBox("getItems");
		if (items.length != undefined)
			var Cod = parseInt(items[(items.length - 1)].value) + 1;
		else
			var Cod = 1;
		
		if (Cod < 10)
			Cod = "0"+Cod;
		
		$("#presupuesto_mat_codigo_add").val(Cod);
		$("#presupuesto_mat_grupo_add").val(Val);
		
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
						"Clasificacion":$("#presupuesto_mat_selected_clasificacion2").val(),
						"VAL1":"Materiales",
						"VAL2":$("#presupuesto_mat_codigo_add").val(),
						"VAL3":$("#presupuesto_mat_grupo_add").val(),
					},
					success: function (data)
					{
						switch(data)
						{
							case "":
								//Update Comboboxes
								$("#presupuesto_mat_grupo1").jqxComboBox("addItem", {
									label: $("#presupuesto_mat_codigo_add").val()+". "+$("#presupuesto_mat_grupo_add").val(), 
									value: $("#presupuesto_mat_codigo_add").val()
								});
								$("#presupuesto_mat_grupo2").jqxComboBox("addItem", {
									label: $("#presupuesto_mat_codigo_add").val()+". "+$("#presupuesto_mat_grupo_add").val(), 
									value: $("#presupuesto_mat_codigo_add").val()
								});
								//$("#presupuesto_mat_grupo1").jqxComboBox("selectItem", $("#presupuesto_mat_codigo_add").val());
								$("#presupuesto_mat_grupo2").jqxComboBox("selectItem", $("#presupuesto_mat_codigo_add").val());
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
						$("#Presupuesto_MAT_Grupos_Window").jqxWindow("close");
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
		$("#Presupuesto_MAT_Grupos_Window").jqxWindow("open");
		var grupo = $("#presupuesto_mat_grupo2").jqxComboBox("getSelectedItem");
		if (!grupo)
			grupo = "";
		else
			grupo = grupo.value;
		var items = $("#presupuesto_mat_subgrupo2").jqxComboBox("getItems");
		if (items.length != undefined && items.value != undefined)
			var Cod = parseInt(items[(items.length - 1)].value) + 1;
		else
			var Cod = 1;
		
		if (Cod < 10)
			Cod = "0"+Cod;
		
		$("#presupuesto_mat_codigo_add").val(Cod);
		$("#presupuesto_mat_grupo_add").val(Val);
		
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
						"Clasificacion":$("#presupuesto_mat_selected_clasificacion2").val(),
						"Categoria":"Materiales",
						"Grupo":grupo,
						"VAL1":$("#presupuesto_mat_codigo_add").val(),
						"VAL2":$("#presupuesto_mat_grupo_add").val(),
					},
					success: function (data)
					{
						switch(data)
						{
							case "":
								//Update Comboboxes
								$("#presupuesto_mat_subgrupo1").jqxComboBox("addItem", {
									label: $("#presupuesto_mat_codigo_add").val()+". "+$("#presupuesto_mat_grupo_add").val(), 
									value: $("#presupuesto_mat_codigo_add").val()
								});
								$("#presupuesto_mat_subgrupo2").jqxComboBox("addItem", {
									label: $("#presupuesto_mat_codigo_add").val()+". "+$("#presupuesto_mat_grupo_add").val(), 
									value: $("#presupuesto_mat_codigo_add").val()
								});
								//$("#presupuesto_mat_subgrupo1").jqxComboBox("selectItem", $("#presupuesto_mat_codigo_add").val());
								$("#presupuesto_mat_subgrupo2").jqxComboBox("selectItem", $("#presupuesto_mat_codigo_add").val());
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
						$("#Presupuesto_MAT_Grupos_Window").jqxWindow("close");
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
			EquiposSource.data = {
				"Presupuesto_Materiales":true,
				"Clasificacion":$("#presupuesto_mat_clasificacion").val(),
				"Grupo":$("#presupuesto_mat_grupo1").val(),
				"SubGrupo":$("#presupuesto_mat_subgrupo1").val(),
			};
			var EquiposDataAdapter = new $.jqx.dataAdapter(EquiposSource);
			$("#presupuesto_mat_codigo1").jqxComboBox({source: EquiposDataAdapter});
			$("#presupuesto_mat_nombre1").jqxComboBox({source: EquiposDataAdapter});
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
	
	var EquiposSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
	};
	
	var ProveedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Proveedor', type: 'string'},
			{ name: 'ProveedorID', type: 'string'},
		],
		url: "modulos/datos.php",
	};
	
	$("#presupuesto_mat_clasificacion").jqxComboBox(
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
	$("#presupuesto_mat_clasificacion").on("change", function (event)
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
					"Categoria":"Materiales",
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
						$("#presupuesto_mat_grupo1").jqxComboBox({source: data});
						$("#presupuesto_mat_grupo2").jqxComboBox({source: data});
					},
				});
				LoadFilter();
			},500);
		}
		else
		{
			//ID = "";
			Clasificacion = "";
			$("#presupuesto_mat_clasificacion").jqxComboBox('clearSelection');
			$("#presupuesto_mat_grupo1").jqxComboBox('clearSelection');
			$("#presupuesto_mat_grupo2").jqxComboBox('clearSelection');
			$("#presupuesto_mat_subgrupo1").jqxComboBox('clearSelection');
			$("#presupuesto_mat_subgrupo2").jqxComboBox('clearSelection');
			$("#presupuesto_mat_nombre1").jqxComboBox('clearSelection');
			$("#presupuesto_mat_codigo1").jqxComboBox('clearSelection');
			$("#presupuesto_mat_grupo1").jqxComboBox('clear');
			$("#presupuesto_mat_grupo2").jqxComboBox('clear');
			$("#presupuesto_mat_subgrupo1").jqxComboBox('clear');
			$("#presupuesto_mat_subgrupo2").jqxComboBox('clear');
			$("#presupuesto_mat_nombre1").jqxComboBox('clear');
			$("#presupuesto_mat_codigo1").jqxComboBox('clear');
		}
	});
	$("#presupuesto_mat_clasificacion").on("bindingComplete", function (event)
	{
		if (Clasificacion != "")
			$("#presupuesto_mat_clasificacion").jqxComboBox("selectItem", Clasificacion);
	});
	document.getElementById("presupuesto_mat_clasificacion").addEventListener("dblclick", function (event)
	{
		$("#Presupuesto_MAT_Clasificacion_Window").jqxWindow('open');
	});
	
	$("#presupuesto_mat_grupo1").jqxComboBox(
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
	$("#presupuesto_mat_grupo1").on("change", function (event)
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
					"P_Clasificacion":$("#presupuesto_mat_clasificacion").val(),
					"P_Categoria":"Materiales",
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
						$("#presupuesto_mat_subgrupo1").jqxComboBox({source: data});
					},
				});
				LoadFilter();
			},500);
		}
		else
		{
			Grupo = "";
			SubGrupo = "";
			$("#presupuesto_mat_grupo1").jqxComboBox('clearSelection');
			$("#presupuesto_mat_subgrupo1").jqxComboBox('clearSelection');
			$("#presupuesto_mat_subgrupo1").jqxComboBox('clear');
		}
	});
	$("#presupuesto_mat_grupo1").on("bindingComplete", function (event)
	{
		if (Grupo != "")
			$("#presupuesto_mat_grupo1").jqxComboBox("selectItem", Grupo);
	});
	
	$("#presupuesto_mat_subgrupo1").jqxComboBox(
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
	$("#presupuesto_mat_subgrupo1").on("change", function (event)
	{
		if (event.args)
			SubGrupo = event.args.item.value;
		else
		{
			SubGrupo = "";
			$("#presupuesto_mat_subgrupo1").jqxComboBox('clearSelection');
		}
		LoadFilter();
	});
	$("#presupuesto_mat_subgrupo1").on("bindingComplete", function (event)
	{
		if (SubGrupo != "")
			$("#presupuesto_mat_subgrupo1").jqxComboBox("selectItem", SubGrupo);
	});
	
	$("#presupuesto_mat_codigo1").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#presupuesto_mat_codigo1").on("change", function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#presupuesto_mat_nombre1").val() != event.args.item.value)
					$("#presupuesto_mat_nombre1").jqxComboBox('selectItem', event.args.item.value);
				
				if (Codigo == event.args.item.value)
					return;
				
				Codigo = event.args.item.value;
				
				LoadValues();
			},500);
		}
		else
		{
			var item_value = $("#presupuesto_mat_codigo1").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				//ID = "";
				Codigo = "";
				$("#presupuesto_mat_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_mat_nombre1").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#presupuesto_mat_codigo1").val();
				var item = $("#presupuesto_mat_codigo1").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					//ID = "";
					Codigo = "";
					$("#presupuesto_mat_codigo1").jqxComboBox('clearSelection');
					$("#presupuesto_mat_nombre1").jqxComboBox('clearSelection');
				}
				else
					$("#presupuesto_mat_codigo1").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#presupuesto_mat_codigo1").on("bindingComplete", function (event)
	{
		if (Codigo != "")
			$("#presupuesto_mat_codigo1").jqxComboBox("selectItem", Codigo);
	});
	
	$("#presupuesto_mat_nombre1").jqxComboBox(
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
	$("#presupuesto_mat_nombre1").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_mat_codigo1").val() != event.args.item.value)
				$("#presupuesto_mat_codigo1").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#presupuesto_mat_nombre1").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				//ID = "";
				$("#presupuesto_mat_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_mat_nombre1").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#presupuesto_mat_nombre1").val();
				
				var item = $("#presupuesto_mat_nombre1").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_mat_nombre1").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				//ID = "";
				$("#presupuesto_mat_codigo1").jqxComboBox('clearSelection');
				$("#presupuesto_mat_nombre1").jqxComboBox('clearSelection');
			}
		}
	});
	
	// --- SEARCH FILTER
	
	function Steps (TO)
	{
		var items = $("#presupuesto_mat_codigo1").jqxComboBox('getItems');
		if (!items)
			return;
		
		var len = items.length - 1;
		
		var index = $("#presupuesto_mat_codigo1").jqxComboBox('getSelectedIndex');
		
		if (TO == "next")
		{
			if (index >= len)
				Alerts_Box("Ha llegado al Final de la Busqueda.", 1);
			else
				$("#presupuesto_mat_codigo1").jqxComboBox('selectIndex',(index + 1));
		}
		else if (TO == "back")
		{
			if (index < 1)
				Alerts_Box("Ha llegado al Principio de la Busqueda.", 1);
			else
				$("#presupuesto_mat_codigo1").jqxComboBox('selectIndex',(index - 1));
		}
	}
	
	$("#presupuesto_mat_back").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_mat_back").bind('click', function ()
	{
		Steps("back")
	});
	
	$("#presupuesto_mat_next").jqxButton({
		width: 100,
		height: 25,
		template: "info"
	});
	$("#presupuesto_mat_next").bind('click', function ()
	{
		Steps("next");
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#presupuesto_mat_categoria").jqxInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: true
	});
	$("#presupuesto_mat_categoria").val("Materiales");
	
	$("#presupuesto_mat_grupo2").jqxComboBox(
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
	$("#presupuesto_mat_grupo2").on("change", function (event)
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
					"P_Clasificacion":$("#presupuesto_mat_clasificacion").val(),
					"P_Categoria":"Materiales",
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
						$("#presupuesto_mat_subgrupo2").jqxComboBox({source: data});
					},
				});
			},500);
			$("#presupuesto_mat_codigo_grupo").val(event.args.item.value);
			$("#presupuesto_mat_codigo_subgrupo").val("");
		}
		else
		{
			var value = $("#presupuesto_mat_grupo2 input")[0].value;
			var item_value = $("#presupuesto_mat_grupo2").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				Grupo2 = "";
				SubGrupo2 = "";
				$("#presupuesto_mat_grupo2").jqxComboBox("clearSelection");
				$("#presupuesto_mat_subgrupo2").jqxComboBox("clearSelection");
				$("#presupuesto_mat_subgrupo2").jqxComboBox("clear");
				$("#presupuesto_mat_codigo_grupo").val("");
				$("#presupuesto_mat_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe. ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddGrupo(value, "presupuesto_mat_grupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
			else
			{
				//var value = $("#presupuesto_mat_grupo2").val();
				var item = $("#presupuesto_mat_grupo2").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_mat_grupo2").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				Grupo2 = "";
				SubGrupo2 = "";
				$("#presupuesto_mat_grupo2").jqxComboBox("clearSelection");
				$("#presupuesto_mat_subgrupo2").jqxComboBox("clearSelection");
				$("#presupuesto_mat_subgrupo2").jqxComboBox("clear");
				$("#presupuesto_mat_codigo_grupo").val("");
				$("#presupuesto_mat_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe! ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddGrupo(value, "presupuesto_mat_grupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
		}
	});
	$("#presupuesto_mat_grupo2").on("bindingComplete", function (event)
	{
		if (Grupo2 != "")
			$("#presupuesto_mat_grupo2").jqxComboBox("selectItem", Grupo2);
	});
	
	$("#presupuesto_mat_subgrupo2").jqxComboBox(
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
	$("#presupuesto_mat_subgrupo2").on("change", function (event)
	{
		if (event.args)
		{
			SubGrupo2 = event.args.item.value;
			$("#presupuesto_mat_codigo_subgrupo").val(event.args.item.value);
		}
		else
		{
			var value = $("#presupuesto_mat_subgrupo2 input")[0].value;
			var item_value = $("#presupuesto_mat_subgrupo2").jqxComboBox("getSelectedItem");
			if (item_value)
			{
				SubGrupo2 = "";
				$("#presupuesto_mat_subgrupo2").jqxComboBox('clearSelection');
				$("#presupuesto_mat_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe. ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddSubGrupo(value, "presupuesto_mat_subgrupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
			else
			{
				//var value = $("#presupuesto_mat_subgrupo2").val();
				var item = $("#presupuesto_mat_subgrupo2").jqxComboBox("getItems");
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#presupuesto_mat_subgrupo2").jqxComboBox("selectItem", item[i].value);
						return;
					}
				}
				SubGrupo2 = "";
				$("#presupuesto_mat_subgrupo2").jqxComboBox('clearSelection');
				$("#presupuesto_mat_codigo_subgrupo").val("");
				
				Alerts_Box("El valor ingresado no existe! ¿Desea Crearlo?", 4, true);
				var CheckTimer = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(CheckTimer);
						ClickOK = false;
						AddSubGrupo(value, "presupuesto_mat_subgrupo2");
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(CheckTimer);
					}
				}, 10);
			}
		}
	});
	$("#presupuesto_mat_subgrupo2").on("bindingComplete", function (event)
	{
		if (SubGrupo2 != "")
			$("#presupuesto_mat_subgrupo2").jqxComboBox("selectItem", SubGrupo2);
	});
	
	$("#presupuesto_mat_codigo_grupo").jqxInput({
		theme: mytheme,
		width: 20,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_mat_codigo_subgrupo").jqxInput({
		theme: mytheme,
		width: 20,
		height: 20,
		disabled: true,
	});
	
	$("#presupuesto_mat_codigo2").jqxInput({
		theme: mytheme,
		width: 50,
		height: 20,
	});
	
	$("#presupuesto_mat_nombre2").jqxInput({
		theme: mytheme,
		width: 365,
		height: 20,
	});
	
	$("#presupuesto_mat_valor").jqxNumberInput({
		theme: mytheme,
		width: 168,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#presupuesto_mat_valor_km").jqxNumberInput({
		theme: mytheme,
		width: 120,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#presupuesto_mat_uso").jqxNumberInput({
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
	
	$("#presupuesto_mat_unidad").jqxComboBox(
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
	$("#presupuesto_mat_unidad").on("change", function (event)
	{
		if (event.args)
			Unidad = event.args.item.value;
		else
		{
			Unidad = "";
			$("#presupuesto_mat_unidad").jqxComboBox('clearSelection');
		}
	});
	$("#presupuesto_mat_unidad").on("bindingComplete", function (event)
	{
		if (Unidad != "")
			$("#presupuesto_mat_unidad").jqxComboBox("selectItem", Unidad);
	});
	
	$("#presupuesto_mat_peso").jqxNumberInput({
		theme: mytheme,
		width: 70,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		decimalDigits: 2,
		digits: 9,
		max: 999999999.99,
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Add_Row()
	{
		if (Locked)
			return;
		
		Locked = true;
		
		var ClienteID = $("#presupuesto_mat_proveedor").jqxComboBox('getSelectedItem');
		
		if (!ClienteID) {
			Alerts_Box("Favor Seleccionar un Proveedor", 3);
			WaitClick_Combobox("presupuesto_mat_proveedor");
			Locked = false;
			return;
		}
		
		var len = ProveedoresData.length;
		for (var i = 0; i < len; i++)
		{
			if (ProveedoresData[i]["ClienteID"] == ClienteID.value)
			{
				var Codigo = ProveedoresData[i]["Codigo"];
				break;
			}
		}
		
		var datinfo = $("#presupuesto_mat_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#presupuesto_mat_items_grid").jqxGrid('getrowdata', i);
			if (currentRow.ProveedorID == ClienteID.value)
			{
				var id = $("#presupuesto_mat_items_grid").jqxGrid('getrowid', i);
				$("#presupuesto_mat_items_grid").jqxGrid('deleterow', id);
			}
		}
		
		var datarow = [{
			"Proveedor":ClienteID.label,
			"ProveedorID":ClienteID.value,
			"Codigo":Codigo,
		}];
		
		$("#presupuesto_mat_items_grid").jqxGrid("addrow", null, datarow, "first");
		$("#presupuesto_mat_proveedor").jqxComboBox('clearSelection');
		$("#presupuesto_mat_proveedorID").jqxComboBox('clearSelection');
		Locked = false;
	}
	
	$("#presupuesto_mat_proveedor").jqxComboBox(
	{
		theme: mytheme,
		width: 200,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Proveedor',
		valueMember: 'ProveedorID'
	});
	$("#presupuesto_mat_proveedor").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#presupuesto_mat_proveedorID").val() != event.args.item.value)
				$("#presupuesto_mat_proveedorID").val(event.args.item.value);
		}
		else
		{
			$("#presupuesto_mat_proveedor").jqxComboBox('clearSelection');
			$("#presupuesto_mat_proveedorID").jqxComboBox('clearSelection');
		}
	});
	
	$("#presupuesto_mat_proveedorID").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ProveedorID',
		valueMember: 'ProveedorID'
	});
	$("#presupuesto_mat_proveedorID").bind('change', function (event) {
		if (event.args)
		{
			if ($("#presupuesto_mat_proveedor").val() != event.args.item.value)
				$("#presupuesto_mat_proveedor").val(event.args.item.value);
		}
		else
		{
			$("#presupuesto_mat_proveedor").jqxComboBox('clearSelection');
			$("#presupuesto_mat_proveedorID").jqxComboBox('clearSelection');
		}
	});
	
	$("#presupuesto_mat_add").jqxButton({
		width: 60,
		height: 25,
		template: "success"
	});
	$("#presupuesto_mat_add").bind('click', function ()
	{
		Add_Row();
	});
	
	var ProveedoresSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Proveedor', type: 'string' },
			{ name: 'ProveedorID', type: 'string' },
			{ name: 'Codigo', type: 'string' },
		],
	};
	var ProveedoresDataAdapter = new $.jqx.dataAdapter(ProveedoresSource);
	
	$("#presupuesto_mat_items_grid").jqxGrid({
		theme: mytheme,
		width: 400,
		height: 150,
		source: ProveedoresDataAdapter,
		editable: false,
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '8%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#presupuesto_mat_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#presupuesto_mat_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#presupuesto_mat_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#presupuesto_mat_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Proveedor', datafield: 'Proveedor', editable: false, width: '47%', height: 20 },
			{ text: 'ID', datafield: 'ProveedorID', editable: false, width: '30%', height: 20 },
			{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '15%', height: 20 },
		]
	});
	
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
		var Proveedores = new Array();
		
		var Clasificacion = $("#presupuesto_mat_clasificacion").jqxComboBox('getSelectedItem');
		if (!Clasificacion)
		{
			Alerts_Box("Debe Seleccionar una Clasificacion", 3);
			WaitClick_Combobox("presupuesto_mat_clasificacion");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_mat_codigo2").val() < 1 || $("#presupuesto_mat_codigo2").val() == "")
		{
			Alerts_Box("Debe Ingresar un Codigo.", 3);
			WaitClick_Input("presupuesto_mat_codigo2");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_mat_nombre2").val() < 1 || $("#presupuesto_mat_nombre2").val() == "")
		{
			Alerts_Box("Debe Ingresar un Nombre.", 3);
			WaitClick_Input("presupuesto_mat_nombre2");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_mat_valor").val() < 1 || $("presupuesto_mat_valor").val() == "")
		{
			Alerts_Box("Debe Ingresar un Valor.", 3);
			WaitClick_NumberInput("presupuesto_mat_valor");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_mat_valor_km").val() < 0)
		{
			Alerts_Box("Debe Ingresar un Valor de Kilometraje.", 3);
			WaitClick_NumberInput("presupuesto_mat_valor_km");
			Locked = false;
			return;
		}
		
		if ($("presupuesto_mat_uso").val() < 1 || $("presupuesto_mat_uso").val() == "")
		{
			Alerts_Box("Debe Ingresar un Valor de Rendimiento de Uso.", 3);
			WaitClick_NumberInput("presupuesto_mat_uso");
			Locked = false;
			return;
		}
		
		var Grupo = $("#presupuesto_mat_grupo2").jqxComboBox('getSelectedItem');
		if (!Grupo)
		{
			Alerts_Box("Debe Seleccionar un Grupo", 3);
			WaitClick_Combobox("presupuesto_mat_grupo2");
			Locked = false;
			return;
		}
		
		var SubGrupo = $("#presupuesto_mat_subgrupo2").jqxComboBox('getSelectedItem');
		if (!SubGrupo)
		{
			Alerts_Box("Debe Seleccionar un SubGrupo", 3);
			WaitClick_Combobox("presupuesto_mat_subgrupo2");
			Locked = false;
			return;
		}
		
		var Unidad = $("#presupuesto_mat_unidad").jqxComboBox('getSelectedItem');
		if (!Unidad)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Unidad", 3);
			WaitClick_Combobox("presupuesto_mat_unidad");
			Locked = false;
			return;
		}
		
		var datinfo = $("#presupuesto_mat_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		for (var i = 0; i < count; i++)
		{
			var tmp_array = {};
			var currentRow = $("#presupuesto_mat_items_grid").jqxGrid('getrowdata', i);
			
			tmp_array["Proveedor"] = currentRow.Proveedor;
			tmp_array["ProveedorID"] = currentRow.ProveedorID;
			tmp_array["Codigo"] = currentRow.Codigo;
			
			Proveedores[i] = tmp_array;
		}
		
		var myarray = {};
		
		if (!Crear)
			myarray["ID"] = ID;
		
		myarray["Clasificacion"] = Clasificacion.value;
		myarray["Codigo"] = Grupo.value + "." + SubGrupo.value + "." + $("#presupuesto_mat_codigo2").val();
		myarray["Codigo2"] = $("#presupuesto_mat_codigo2").val();
		myarray["Nombre"] = $("#presupuesto_mat_nombre2").val();
		myarray["Valor"] = $("#presupuesto_mat_valor").val();
		myarray["Valor_KM"] = $("#presupuesto_mat_valor_km").val();
		myarray["Uso"] = $("#presupuesto_mat_uso").val();
		myarray["Grupo"] = Grupo.value;
		myarray["SubGrupo"] = SubGrupo.value;
		myarray["Unidad"] = Unidad.value;
		myarray["Peso"] = $("#presupuesto_mat_peso").val();
		var Imagen = GetImage("presupuesto_mat_producto_fotoID");
		if (Imagen == "images/search.png")
			Imagen = "";
		myarray["Imagen"] = Imagen;
		myarray["Notas"] = $("#presupuesto_mat_notas").val();
		myarray["Proveedores"] = Proveedores;
		
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
			data: {"Materiales_Crear":FinalArray},
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
	
	$("#presupuesto_mat_producto_foto").click(function()
	{
		var img_src = document.getElementById("presupuesto_mat_producto_fotoID").src;
		var img = "<img src=\""+img_src+"\" alt=\"Imagen del Producto\"/>";
		$("#presupuesto_mat_show_docs_content").html(img);
		$("#presupuesto_mat_show_docs_window").jqxWindow('open');
	});
	
	$("#presupuesto_mat_show_docs_window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 600,
		width: 800,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	
	$("#presupuesto_mat_producto_foto_delete").click(function() {
		//Clean Img
		var img = "<img id=\"presupuesto_mat_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/search.png\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
		$("#presupuesto_mat_producto_foto").html(img);
	});
	
	$("#presupuesto_mat_producto_foto_edit").click(function() {
		$("#presupuesto_mat_upload_docs_window").jqxWindow('open');
	});
	
	$("#presupuesto_mat_upload_docs_window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 160,
		title: "Cargar Archivo",
		width: 300,
		minWidth: 300,
		maxWidth: 400,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#presupuesto_mat_producto_upload_docs").jqxFileUpload(
			{
				theme: mytheme,
				width: 290,
				multipleFilesUpload: false,
				browseTemplate: 'success',
				uploadTemplate: 'primary',
				cancelTemplate: 'danger',
				uploadUrl: 'modulos/guardar.php',
				fileInputName: 'Image_Uploads',
			});
			$("#presupuesto_mat_producto_upload_docs").on('uploadEnd', function (event)
			{
				var args = event.args;
				var fileName = args.file;
				var serverResponce = args.response;
				
				if (serverResponce == "OK")
				{
					var Tmp = "Uploads_Tmp/"+fileName;
					var img = "<img id=\"presupuesto_mat_producto_fotoID\" width=\"150\" height=\"150\" src=\""+Tmp+"\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
					$("#presupuesto_mat_producto_foto").html(img);
					$("#presupuesto_mat_upload_docs_window").jqxWindow('close');
				}
				else
				{
					Alerts_Box(""+serverResponce, 3);
					WaitClick_WindowClose("presupuesto_mat_upload_docs_window");
				}
			});
		}
	});
	
	$("#presupuesto_mat_nuevo").jqxButton({
		width: 120,
		height: 25,
		template: "success"
	});
	$("#presupuesto_mat_nuevo").bind('click', function ()
	{
		ClearDocument()
	});
	
	$("#presupuesto_mat_guardar").jqxButton({
		width: 120,
		height: 25,
		template: "info"
	});
	$("#presupuesto_mat_guardar").bind('click', function ()
	{
		SaveData();
	});
	
	$("#presupuesto_mat_crear").jqxButton({
		width: 120,
		height: 25,
		template: "primary"
	});
	$("#presupuesto_mat_crear").bind('click', function ()
	{
		SaveData(true);
	});
	
	//----- Windows
	$("#Presupuesto_MAT_Clasificacion_Window").jqxWindow({
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
			$("#presupuesto_mat_clasificacion_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_mat_clasificacion_button").trigger("click");
				}
			});
			
			$("#presupuesto_mat_clasificacion_form").jqxValidator({
				rules: [
					{ input: "#presupuesto_mat_clasificacion_add", message: "Se Requiere un Nombre de Clasificacion!", action: "keyup, blur", rule: "required" },
					{ input: "#presupuesto_mat_notas2", message: "Se Requiere un Concepto!", action: "keyup, blur", rule: "required" },
				]
			});
			
			$("#presupuesto_mat_selected_clasificacion").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
				disabled: true,
			});
			
			$("#presupuesto_mat_clasificacion_add").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
			});
	
			$("#presupuesto_mat_clasificacion_button1").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "primary"
			});
			$("#presupuesto_mat_clasificacion_button1").bind("click", function ()
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
								"From_Clasificacion":$("#presupuesto_mat_selected_clasificacion").val(),
								"New_Clasificacion":$("#presupuesto_mat_clasificacion_add").val(),
								"Nota":$("#presupuesto_mat_notas2").val()
							},
							success: function (data, status, xhr)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								if (data == "")
								{
									Alerts_Box("Datos Guardados con Exito!", 2);
									$("#presupuesto_mat_selected_clasificacion").val("");
									$("#presupuesto_mat_clasificacion_add").val("");
									$("#presupuesto_mat_notas2").val("");
									$("#Presupuesto_MAT_Clasificacion_Window").jqxWindow("close");
								}
								else if (data == "EXIST")
								{
									Alerts_Box("Ya existe una Clasificacion con el Mismo Nombre<br/>Favor Ingresar Otro.", 4);
								}
								else
								{
									Alerts_Box("Ha ocurrido un error mientras se guardaban los datos!<br/>Intente luego de unos segundos", 3);
									$("#presupuesto_mat_selected_clasificacion").val("");
									$("#presupuesto_mat_clasificacion_add").val("");
									$("#presupuesto_mat_notas2").val("");
									$("#Presupuesto_MAT_Clasificacion_Window").jqxWindow("close");
								}
							},
							error: function (jqXHR, textStatus, errorThrown)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								alert(textStatus+ " - " +errorThrown);
								$("#presupuesto_mat_selected_clasificacion").val("");
								$("#presupuesto_mat_clasificacion_add").val("");
								$("#presupuesto_mat_notas2").val("");
								$("#Presupuesto_MAT_Clasificacion_Window").jqxWindow("close");
							}
						});
					}
					else
						Locked = false;
				}
				$("#presupuesto_mat_clasificacion_form").jqxValidator("validate", validationResult);
			});
			
			$("#presupuesto_mat_clasificacion_button2").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "info"
			});
			$("#presupuesto_mat_clasificacion_button2").bind("click", function ()
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
								"Clasificacion":$("#presupuesto_mat_clasificacion_add").val(),
								"Nota":$("#presupuesto_mat_notas2").val()
							},
							success: function (data, status, xhr)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								if (data == "")
								{
									Alerts_Box("Datos Guardados con Exito!", 2);
									$("#presupuesto_mat_selected_clasificacion").val("");
									$("#presupuesto_mat_clasificacion_add").val("");
									$("#presupuesto_mat_notas2").val("");
									$("#Presupuesto_MAT_Clasificacion_Window").jqxWindow("close");
								}
								else if (data == "EXIST")
								{
									Alerts_Box("Ya existe una Clasificacion con el Mismo Nombre<br/>Favor Ingresar Otro.", 4);
								}
								else
								{
									Alerts_Box("Ha ocurrido un error mientras se guardaban los datos!<br/>Intente luego de unos segundos", 3);
									$("#presupuesto_mat_selected_clasificacion").val("");
									$("#presupuesto_mat_clasificacion_add").val("");
									$("#presupuesto_mat_notas2").val("");
									$("#Presupuesto_MAT_Clasificacion_Window").jqxWindow("close");
								}
							},
							error: function (jqXHR, textStatus, errorThrown)
							{
								$("#Loading").hide();
								$("#Loading_Mess").html("Cargando...");
								Locked = false;
								alert(textStatus+ " - " +errorThrown);
								$("#presupuesto_mat_selected_clasificacion").val("");
								$("#presupuesto_mat_clasificacion_add").val("");
								$("#presupuesto_mat_notas2").val("");
								$("#Presupuesto_MAT_Clasificacion_Window").jqxWindow("close");
							}
						});
					}
					else
						Locked = false;
				}
				$("#presupuesto_mat_clasificacion_form").jqxValidator("validate", validationResult);
			});
		}
	});
	$("#Presupuesto_MAT_Clasificacion_Window").on('open', function ()
	{
		var Item = $("#presupuesto_mat_clasificacion").jqxComboBox('getSelectedItem');
		if (Item)
			var Val = Item.value;
		else
			var Val = "";
		$("#presupuesto_mat_selected_clasificacion").val(Val);
	});
	$("#Presupuesto_MAT_Clasificacion_Window").on('close', function () {
		LoadParameters();
	});
	
	$("#Presupuesto_MAT_Grupos_Window").jqxWindow({
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
			$("#presupuesto_mat_codigo_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_mat_grupo_add").focus();
				}
			});
			
			$("#presupuesto_mat_grupo_add").keydown(function(event)
			{
				if(event.which == 13)
				{
					$("#presupuesto_mat_grupo_button").trigger("click");
				}
			});
			
			$("#presupuesto_mat_grupo_form").jqxValidator({
				rules: [
					{ input: "#presupuesto_mat_codigo_add", message: "Se Requiere un Codigo!", action: "keyup, blur", rule: "required" },
					{ input: "#presupuesto_mat_grupo_add", message: "Se Requiere un Nombre!", action: "keyup, blur", rule: "required" },
				]
			});
			
			$("#presupuesto_mat_selected_clasificacion2").jqxInput({
				theme: mytheme,
				width: 245,
				height: 20,
				disabled: true,
			});
			
			$("#presupuesto_mat_codigo_add").jqxInput({
				theme: mytheme,
				width: 40,
				height: 20,
			});
			
			$("#presupuesto_mat_grupo_add").jqxInput({
				theme: mytheme,
				width: 190,
				height: 20,
			});
	
			$("#presupuesto_mat_grupo_button").jqxButton({
				theme: mytheme,
				width: 116,
				height: 30,
				template: "primary"
			});
			$("#presupuesto_mat_grupo_button").on("click", function ()
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
				$("#presupuesto_mat_grupo_form").jqxValidator("validate", validationResult);
			});
		}
	});
	$("#Presupuesto_MAT_Grupos_Window").on("open", function ()
	{
		var Item = $("#presupuesto_mat_clasificacion").jqxComboBox("getSelectedItem");
		if (Item)
			var Val = Item.value;
		else
			var Val = "";
		$("#presupuesto_mat_selected_clasificacion2").val(Val);
	});
	
	Clasificacion = "originales";
	
	LoadParameters();
	CheckRefresh();
});
</script>
<div id="Presupuesto_MAT_Clasificacion_Window">
	<div id="Presupuesto_MAT_Clasificacion_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 20px;">Crear Nueva Base de Datos</div>
	</div>
	<div id="Presupuesto_MAT_Clasificacion_Window_Content" class="WindowContainer" align="center">
		<form id="presupuesto_mat_clasificacion_form" onsubmit="return false;">
			<table cellspacing="2" cellpadding="5" align="center">
				<tr>
					<td colspan="2">
						Base de Datos Seleccionada
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_mat_selected_clasificacion"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Nombre de la Base de Datos
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_mat_clasificacion_add"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						Notas
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea rows="3" cols="32" maxlength="200" style="resize:none;" id="presupuesto_mat_notas2"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<input id="presupuesto_mat_clasificacion_button1" type="button" value="Crear Copia" />
					</td>
					<td>
						<input id="presupuesto_mat_clasificacion_button2" type="button" value="Crear Nueva" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div id="Presupuesto_MAT_Grupos_Window">
	<div id="Presupuesto_MAT_Grupos_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 35px;">Agregar Nuevo Valor</div>
	</div>
	<div id="Presupuesto_MAT_Grupos_Window_Content" class="WindowContainer" align="center">
		<form id="presupuesto_mat_grupo_form" onsubmit="return false;">
			<table cellspacing="2" cellpadding="5" align="center">
				<tr>
					<td colspan="2">
						Base de Datos Seleccionada
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="presupuesto_mat_selected_clasificacion2"/>
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
						<input type="text" id="presupuesto_mat_codigo_add"/>
					</td>
					<td>
						<input type="text" id="presupuesto_mat_grupo_add"/>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<input id="presupuesto_mat_grupo_button" type="button" value="Crear" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<div id="presupuesto_mat_upload_docs_window">
	<div style="height:20px; font-size: 16px; color: #707070;">
	</div>
	<div>
		<div id="presupuesto_mat_producto_upload_docs"></div>
	</div>
</div>
<div id="presupuesto_mat_show_docs_window">
	<div style="height:15px;">
	</div>
	<div>
		<div id="presupuesto_mat_show_docs_content"></div>
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
				<div id="presupuesto_mat_clasificacion"></div>
			</td>
			<td>
				Grupo
			</td>
			<td>
				<li>
					<div id="presupuesto_mat_grupo1"></div>
				</li>
				<li style="padding-top:5px; padding-left:4px;">
					SubGrupo
				</li>
				<li style="padding-left:4px;">
					<div id="presupuesto_mat_subgrupo1"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td rowspan="2">
				<li>
					<input type="button" id="presupuesto_mat_back" value="<< Ant."/>
				</li>
				<li style="padding:0px 0px 0px 10px;">
					<input type="button" id="presupuesto_mat_next" value="Sig. >>"/>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Codigo
			</td>
			<td>
				<div id="presupuesto_mat_codigo1"></div>
			</td>
			<td>
				Nombre
			</td>
			<td>
				<div id="presupuesto_mat_nombre1"></div>
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
				Unidad
			</td>
			<td>
				Peso
			</td>
			<td>
				Uso
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="presupuesto_mat_categoria" style="text-align:center;"/>
			</td>
			<td>
				<div id="presupuesto_mat_grupo2"></div>
			</td>
			<td>
				<div id="presupuesto_mat_subgrupo2"></div>
			</td>
			<td>
				<div id="presupuesto_mat_unidad"></div>
			</td>
			<td>
				<div id="presupuesto_mat_peso"></div>
			</td>
			<td>
				<div id="presupuesto_mat_uso"></div>
			</td>
		</tr>
		<tr>
			<td>
				Codigo
			</td>
			<td colspan="2">
				Nombre
			</td>
			<td colspan="2">
				Valor Unitario
			</td>
			<td>
				Valor Km
			</td>
		</tr>
		<tr>
			<td>
				<li>
					<input type="text" id="presupuesto_mat_codigo_grupo" style="text-align:center;"/>
				</li>
				<li style="margin-top:8px;">
					.
				</li>
				<li>
					<input type="text" id="presupuesto_mat_codigo_subgrupo" style="text-align:center;"/>
				</li>
				<li style="margin-top:8px;">
					.
				</li>
				<li>
					<input type="text" id="presupuesto_mat_codigo2"/>
				</li>
			</td>
			<!--<td>
				<input type="text" id="presupuesto_mat_codigo2"/>
			</td>-->
			<td colspan="2">
				<input type="text" id="presupuesto_mat_nombre2"/>
			</td>
			<td colspan="2">
				<div id="presupuesto_mat_valor"></div>
			</td>
			<td>
				<div id="presupuesto_mat_valor_km"></div>
			</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td style="padding:0px;">
				<table cellpadding="3" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
					<tr style="background: #E0E9F5">
						<td style="border-bottom: 1px solid #A4BED4;">
							Proveedor
						</td>
						<td style="border-bottom: 1px solid #A4BED4;">
							ID
						</td>
						<td style="border-bottom: 1px solid #A4BED4;">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td>
							<div id="presupuesto_mat_proveedor"></div>
						</td>
						<td>
							<div id="presupuesto_mat_proveedorID"></div>
						</td>
						<td>
							<input type="button" id="presupuesto_mat_add" value="Añadir"/>
						</td>
					</tr>
				</table>
			</td>
			<td style="padding: 45px 0px 0px 10px;">
				Imagen de el Equipo
			</td>
			<td colspan="2" style="padding: 45px 0px 0px 0px;">
				Notas
			</td>
		</tr>
		<tr>
			<td>
				<div id="presupuesto_mat_items_grid"></div>
			</td>
			<td rowspan="5" style="padding-left:10px;">
				<div id="presupuesto_mat_producto_foto_delete" style="width:16px; height:16px; margin: 0px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/close_white.png'" onmouseout="this.src='images/close_black.png'" width="16" height="16" src="images/close_black.png" alt="Eliminar Imagen"/>
				</div>
				<div id="presupuesto_mat_producto_foto_edit" style="width:16px; height:16px; margin: 0px 0px 0px 20px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/edit_white.png'" onmouseout="this.src='images/edit_black.png'" width="16" height="16" src="images/edit_black.png" alt="Cambiar Imagen"/>
				</div>
				<div id="presupuesto_mat_producto_foto" style="cursor:pointer;">
					<img id="presupuesto_mat_producto_fotoID" width="150" height="150" src="images/search.png" alt="Imagen del Producto" style="margin: 0px 10px 0px 0px;"/>
				</div>
			</td>
			<td>
				<textarea rows="10" cols="28" id="presupuesto_mat_notas" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td style="padding-left:10px; padding-top: 60px; list-style:none;">
				<li style="margin-bottom:10px;">
					<input type="button" id="presupuesto_mat_guardar" value="Guardar"/>
				</li>
				<li style="margin-bottom:10px;">
					<input type="button" id="presupuesto_mat_crear" value="Crear"/>
				</li>
				<li>
					<input type="button" id="presupuesto_mat_nuevo" value="Nuevo"/>
				</li>
			</td>
		</tr>
	</table>
</div>

