<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Ord_Reparacion = "";
	var Tipo = "";
	var Motivo = "";
	var Diagnostico = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Maquinaria_Content");
	var Body = document.getElementById("Maquinaria_Modificar_Content");
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
				SystemMap("Modificar", true);
				ReDefine();
				ClearDocument();
				// Buscar Ordenes
				OrdenSource.data =  {"Maquinaria_Modificar":true},
				OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
				$("#maquinaria_modificar_ord_reparacion").jqxComboBox({source: OrdenAdapter});
				//---
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
	
	var Admin = false;
	var Guardar = false;
	var Imprimir = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Maquinaria" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Maquinaria" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Imprimir"] == "true")
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
	}
	?>
	
	function ReDefine()
	{
		ClearJSON = [
			//-1
			{id:"maquinaria_modificar_ord_reparacion", type:"jqxComboBox"},
			{id:"maquinaria_modificar_estado", type:"jqxComboBox"},
			{id:"maquinaria_modificar_operador", type:"jqxComboBox"},
			{id:"maquinaria_modificar_mecanico", type:"jqxComboBox"},
			{id:"maquinaria_modificar_fecha_ini", type:"jqxDateTimeInput"},
			{id:"maquinaria_modificar_clasificacion", type:"jqxComboBox"},
			{id:"maquinaria_modificar_tipo", type:"jqxComboBox"},
			{id:"maquinaria_modificar_fecha_fin", type:"jqxDateTimeInput"},
			{id:"maquinaria_modificar_motivo", type:"jqxComboBox"},
			{id:"maquinaria_modificar_diagnostico", type:"jqxComboBox"},
			{id:"maquinaria_modificar_partes", type:"jqxComboBox"},
			{id:"maquinaria_modificar_partes_problema", type:"jqxComboBox"},
			{id:"maquinaria_modificar_partes_diagnostico", type:"jqxComboBox"},
			{id:"maquinaria_modificar_partes_items_grid", type:"jqxGrid"},
			//
			{id:"maquinaria_modificar_codigo1", type:"jqxComboBox"},
			{id:"maquinaria_modificar_producto1", type:"jqxComboBox"},
			{id:"maquinaria_modificar_cantidad1", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_unitario1", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_items_grid1", type:"jqxGrid"},
			{id:"maquinaria_modificar_proveedor1", type:"jqxComboBox"},
			{id:"maquinaria_modificar_proveedor_ID1", type:"jqxComboBox"},
			{id:"maquinaria_modificar_factura_proveedor1", type:""},
			{id:"maquinaria_modificar_total_proveedor1", type:"jqxNumberInput"},
			//
			{id:"maquinaria_modificar_codigo2", type:"jqxComboBox"},
			{id:"maquinaria_modificar_producto2", type:"jqxComboBox"},
			{id:"maquinaria_modificar_cantidad2", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_unitario2", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_items_grid2", type:"jqxGrid"},
			{id:"maquinaria_modificar_proveedor2", type:"jqxComboBox"},
			{id:"maquinaria_modificar_proveedor_ID2", type:"jqxComboBox"},
			{id:"maquinaria_modificar_factura_proveedor2", type:""},
			{id:"maquinaria_modificar_total_proveedor2", type:"jqxNumberInput"},
			//
			{id:"maquinaria_modificar_codigo3", type:"jqxComboBox"},
			{id:"maquinaria_modificar_producto3", type:"jqxComboBox"},
			{id:"maquinaria_modificar_cantidad3", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_unitario3", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_items_grid3", type:"jqxGrid"},
			{id:"maquinaria_modificar_proveedor3", type:"jqxComboBox"},
			{id:"maquinaria_modificar_proveedor_ID3", type:"jqxComboBox"},
			{id:"maquinaria_modificar_factura_proveedor3", type:""},
			{id:"maquinaria_modificar_total_proveedor3", type:"jqxNumberInput"},
			//
			{id:"maquinaria_modificar_total", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_causa", type:""},
			{id:"maquinaria_modificar_procedimiento", type:""},
			{id:"maquinaria_modificar_observaciones", type:""},
		];
		
		EnableDisableJSON = [
			//-1
			{id:"maquinaria_modificar_estado", type:"jqxComboBox"},
			{id:"maquinaria_modificar_operador", type:"jqxComboBox"},
			{id:"maquinaria_modificar_mecanico", type:"jqxComboBox"},
			{id:"maquinaria_modificar_fecha_ini", type:"jqxDateTimeInput"},
			{id:"maquinaria_modificar_fecha_fin", type:"jqxDateTimeInput"},
			{id:"maquinaria_modificar_clasificacion", type:"jqxComboBox"},
			{id:"maquinaria_modificar_tipo", type:"jqxComboBox"},
			{id:"maquinaria_modificar_motivo", type:"jqxComboBox"},
			{id:"maquinaria_modificar_diagnostico", type:"jqxComboBox"},
			{id:"maquinaria_modificar_partes", type:"jqxComboBox"},
			{id:"maquinaria_modificar_partes_problema", type:""},
			{id:"maquinaria_modificar_partes_diagnostico", type:""},
			{id:"maquinaria_modificar_partes_add", type:"jqxButton"},
			{id:"maquinaria_modificar_partes_items_grid", type:"jqxGrid"},
			//
			{id:"maquinaria_modificar_codigo1", type:"jqxComboBox"},
			{id:"maquinaria_modificar_producto1", type:"jqxComboBox"},
			{id:"maquinaria_modificar_cantidad1", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_unitario1", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_addrowbutton1", type:"jqxButton"},
			//{id:"maquinaria_modificar_items_grid1", type:"jqxGrid"},
			{id:"maquinaria_modificar_proveedor1", type:"jqxComboBox"},
			{id:"maquinaria_modificar_proveedor_ID1", type:"jqxComboBox"},
			//
			{id:"maquinaria_modificar_codigo2", type:"jqxComboBox"},
			{id:"maquinaria_modificar_producto2", type:"jqxComboBox"},
			{id:"maquinaria_modificar_cantidad2", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_unitario2", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_addrowbutton2", type:"jqxButton"},
			//{id:"maquinaria_modificar_items_grid2", type:"jqxGrid"},
			{id:"maquinaria_modificar_proveedor2", type:"jqxComboBox"},
			{id:"maquinaria_modificar_proveedor_ID2", type:"jqxComboBox"},
			//
			{id:"maquinaria_modificar_codigo3", type:"jqxComboBox"},
			{id:"maquinaria_modificar_producto3", type:"jqxComboBox"},
			{id:"maquinaria_modificar_cantidad3", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_unitario3", type:"jqxNumberInput"},
			{id:"maquinaria_modificar_addrowbutton3", type:"jqxButton"},
			//{id:"maquinaria_modificar_items_grid3", type:"jqxGrid"},
			{id:"maquinaria_modificar_proveedor3", type:"jqxComboBox"},
			{id:"maquinaria_modificar_proveedor_ID3", type:"jqxComboBox"},
			//
			{id:"maquinaria_modificar_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	
	function ClearDocument()
	{
		// Clean Variables
		Ord_Reparacion = "";
		Timer1 = 0;
		Timer2 = 0;
		//
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		if (!Admin && !Guardar)
		{
			$("#maquinaria_modificar_guardar").jqxButton({ disabled: true });
			//$("#maquinaria_modificar_nuevo").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#maquinaria_modificar_imprimir").jqxButton({ disabled: true });
		}
	};
	
	function LoadValues()
	{
		var LoadTimer = setTimeout(function()
		{
			var GetValuesSource =
			{
				datatype: "json",
				datafields: [
					{ name: 'Estado_Maquina', type: 'string'},
					{ name: 'Operador', type: 'string'},
					{ name: 'Mecanico', type: 'string'},
					{ name: 'Fecha_Ini', type: 'string'},
					{ name: 'Fecha_Fin', type: 'string' },
					{ name: 'Clasificacion', type: 'string'},
					{ name: 'Tipo', type: 'string' },
					{ name: 'Motivo', type: 'string' },
					{ name: 'Diagnostico', type: 'string' },
					{ name: 'Total', type: 'decimal' },
					{ name: 'Descripcion', type: 'string' },
					{ name: 'Procedimiento', type: 'string' },
					{ name: 'Observaciones', type: 'string' },
					{ name: 'Items', type: 'string'},
					{ name: 'Proveedor1', type: 'string'},
					{ name: 'Proveedor2', type: 'string'},
					{ name: 'Proveedor3', type: 'string' },
				],
				data:{"Maquinaria_Modificar":$("#maquinaria_modificar_ord_reparacion").val()},
				url: "modulos/datos_productos.php",
			};
			var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
				autoBind: true,
				loadComplete: function ()
				{
					var records = GetValuesAdapter.records;
					$("#maquinaria_modificar_partes_items_grid").jqxGrid('clear');
					$("#maquinaria_modificar_items_grid1").jqxGrid('clear');
					$("#maquinaria_modificar_items_grid2").jqxGrid('clear');
					$("#maquinaria_modificar_items_grid3").jqxGrid('clear');
					
					$("#maquinaria_modificar_estado").jqxComboBox("selectItem", records[0]["Estado_Maquina"]);
					$("#maquinaria_modificar_operador").jqxComboBox("selectItem", records[0]["Operador"]);
					$("#maquinaria_modificar_mecanico").jqxComboBox("selectItem", records[0]["Mecanico"]);
					$("#maquinaria_modificar_fecha_ini").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Ini"])));
					$("#maquinaria_modificar_fecha_fin").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Fin"])));
					$("#maquinaria_modificar_clasificacion").jqxComboBox("selectItem", records[0]["Clasificacion"]);
					Tipo = records[0]["Tipo"];
					Motivo = records[0]["Motivo"];
					//$("#maquinaria_modificar_tipo").val(records[0]["Tipo"]);
					//$("#maquinaria_modificar_motivo").val(records[0]["Motivo"]);
					$("#maquinaria_modificar_diagnostico").jqxComboBox("selectItem", records[0]["Diagnostico"]);
					$("#maquinaria_modificar_total").val(records[0]["Total"]);
					$("#maquinaria_modificar_causa").val(records[0]["Descripcion"]);
					$("#maquinaria_modificar_procedimiento").val(records[0]["Procedimiento"]);
					$("#maquinaria_modificar_observaciones").val(records[0]["Observaciones"]);
					
					if (records[0]["Items"].length > 0)
					{
						var datarow = new Array();
						for (var i = 0; i < records[0]["Items"].length; i++)
						{
							var tmp_array = {};
							tmp_array["Parte"] = records[0]["Items"][i]["Parte"];
							tmp_array["Problema"] = records[0]["Items"][i]["Problema"];
							tmp_array["Diagnostico"] = records[0]["Items"][i]["Diagnostico"];
							
							datarow[i] = tmp_array;
						}
						datarow.reverse();
						$("#maquinaria_modificar_partes_items_grid").jqxGrid("addrow", null, datarow, "first");
					}
						
					var records1 = records[0]["Proveedor1"];
					var len1 = records1.length;
					if (len1 > 0 && records[0]["Proveedor1"] != "") {
						for (var i = 0; i < len1; i++)
						{
							if (i == 0)
							{
								$("#maquinaria_modificar_proveedor1").val(records1[i]["ClienteID"]);
								$("#maquinaria_modificar_factura_proveedor1").val(records1[i]["Factura"]);
								$("#maquinaria_modificar_total_proveedor1").val(records1[i]["Total"]);
							}
							var datarow = [{
								"Codigo":records1[i]["Codigo"],
								"Repuesto":records1[i]["Repuesto"],
								"Unitario":records1[i]["Unitario"],
								"Cantidad":records1[i]["Cantidad"],
							}];
							$("#maquinaria_modificar_items_grid1").jqxGrid("addrow", null, datarow, "first");
						}
					}
					
					var records2 = records[0]["Proveedor2"];
					var len2 = records2.length;
					if (len2 > 0 && records[0]["Proveedor2"] != "") {
						for (var j = 0; j < len2; j++)
						{
							if (j == 0)
							{
								$("#maquinaria_modificar_proveedor2").val(records2[j]["ClienteID"]);
								$("#maquinaria_modificar_factura_proveedor2").val(records1[i]["Factura"]);
								$("#maquinaria_modificar_total_proveedor2").val(records2[j]["Total"]);
							}
							var datarow = [{
								"Codigo":records2[j]["Codigo"],
								"Repuesto":records2[j]["Repuesto"],
								"Unitario":records2[j]["Unitario"],
								"Cantidad":records2[j]["Cantidad"],
							}];
							$("#maquinaria_modificar_items_grid2").jqxGrid("addrow", null, datarow, "first");
						}
					}
					
					var records3 = records[0]["Proveedor3"];
					var len3 = records3.length;
					if (len3 > 0 && records[0]["Proveedor3"] != "") {
						for (var k = 0; k < len3; k++)
						{
							if (k == 0)
							{
								$("#maquinaria_modificar_proveedor3").val(records3[k]["ClienteID"]);
								$("#maquinaria_modificar_factura_proveedor3").val(records1[i]["Factura"]);
								$("#maquinaria_modificar_total_proveedor3").val(records3[k]["Total"]);
							}
							var datarow = [{
								"Codigo":records3[k]["Codigo"],
								"Repuesto":records3[k]["Repuesto"],
								"Unitario":records3[k]["Unitario"],
								"Cantidad":records3[k]["Cantidad"],
							}];
							$("#maquinaria_modificar_items_grid3").jqxGrid("addrow", null, datarow, "first");
						}
					}
				},
				loadError: function(jqXHR, status, error) {
					alert("Request failed: \n" + error);
				},
			});
			clearTimeout(LoadTimer);
		},250);
	};
	
	var OrdenSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Ord_Reparacion', type: 'string'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#maquinaria_modificar_ord_reparacion").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Orden',
		selectedIndex: -1,
		displayMember: 'Ord_Reparacion',
		valueMember: 'Ord_Reparacion'
	});
	$("#maquinaria_modificar_ord_reparacion").bind('change', function (event) {
		if (event.args)
		{
			if (Ord_Reparacion == event.args.item.value)
				return;
			
			Ord_Reparacion = event.args.item.value;
			if (ClickOK == true) {
				EnableDisableAll(false);
				ClickOK = false;
			}
			LoadValues();
		}
		else
			ClearDocument();
	});
	$("#maquinaria_modificar_ord_reparacion").bind('bindingComplete', function (event) {
		if (Ord_Reparacion != "")
			$("#maquinaria_modificar_ord_reparacion").val(Ord_Reparacion);
	});
	// Buscar Ordenes
	OrdenSource.data =  {"Maquinaria_Modificar":true},
	OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
	$("#maquinaria_modificar_ord_reparacion").jqxComboBox({source: OrdenAdapter});
	
	var EstadoValues = [
		{"Estado":"En Operación"},
		{"Estado":"Inhabilitada"},
		{"Estado":"En Mantenimiento (En Operación)"},
	];
	
	var EstadoSource = {
		localdata: EstadoValues,
		datatype: "json",
		datafields:[
			{ name: 'Estado', type: 'string' }
		]
	};
	var EstadoDataAdapter = new $.jqx.dataAdapter(EstadoSource);
	
	$("#maquinaria_modificar_estado").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: EstadoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Estado',
		valueMember: 'Estado'
	});
	
	var NominaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		data: {"Clientes_Nomina":true},
		url: "modulos/datos.php",
	};
	var NominaDataAdapter = new $.jqx.dataAdapter(NominaSource,
	{
		autoBind: true,
		loadComplete: function (records)
		{
			$("#maquinaria_modificar_operador").jqxComboBox({source: records});
			$("#maquinaria_modificar_mecanico").jqxComboBox({source: records});
		}
	});
	
	$("#maquinaria_modificar_operador").jqxComboBox(
	{
		theme: mytheme,
		width: 225,
		height: 20,
		//source: EstadoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	
	$("#maquinaria_modificar_mecanico").jqxComboBox(
	{
		theme: mytheme,
		width: 260,
		height: 20,
		//source: EstadoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	
	var ClasificacionValues = [
		{"Tipo":"Maquinaria"},
		{"Tipo":"Vehículos"},
	];
	
	var ClasificacionSource = {
		localdata: ClasificacionValues,
		datatype: "json",
		datafields:[
			{ name: 'Tipo', type: 'string' }
		]
	};
	var ClasificacionDataAdapter = new $.jqx.dataAdapter(ClasificacionSource);
	
	$("#maquinaria_modificar_clasificacion").jqxComboBox(
	{
		theme: mytheme,
		width: 225,
		height: 20,
		source: ClasificacionDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#maquinaria_modificar_clasificacion").on("change", function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer2);
			Timer2 = setTimeout(function()
			{
				TipoSource.data = {"Caja_SubGrupo2":event.args.item.value};
				MotivoSource.data = {"Caja_SubGrupo":event.args.item.value};
				DataAdapter1 = new $.jqx.dataAdapter(TipoSource);
				DataAdapter2 = new $.jqx.dataAdapter(MotivoSource);
				$("#maquinaria_modificar_tipo").jqxComboBox({source: DataAdapter1});
				$("#maquinaria_modificar_motivo").jqxComboBox({source: DataAdapter2});
			},350);
		}
		else
		{
			$("#maquinaria_modificar_clasificacion").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_tipo").jqxComboBox('clearSelection');
		}
	});
	
	var TipoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo2', type: 'string'}
		],
		url: "modulos/parametros.php",
	};
	
	var MotivoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo', type: 'string'}
		],
		url: "modulos/parametros.php",
	};
	
	$("#maquinaria_modificar_tipo").jqxComboBox(
	{
		theme: mytheme,
		width: 260,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo2',
		valueMember: 'SubGrupo2'
	});
	$("#maquinaria_modificar_tipo").on("bindingComplete", function (event)
	{
		if (Tipo != "")
			$("#maquinaria_modificar_tipo").jqxComboBox("selectItem", Tipo);
	});
	
	$("#maquinaria_modificar_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#maquinaria_modificar_motivo").jqxComboBox(
	{
		theme: mytheme,
		width: 225,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo'
	});
	$("#maquinaria_modificar_motivo").on("bindingComplete", function (event)
	{
		if (Motivo != "")
			$("#maquinaria_modificar_motivo").jqxComboBox("selectItem", Motivo);
	});
	
	var DiagnosticoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo', type: 'string'},
		],
		url: "modulos/parametros.php",
		data: {"Diagnostico":true},
		async: true
	};
	var DiagnosticoDataAdapter = new $.jqx.dataAdapter(DiagnosticoSource);
	
	$("#maquinaria_modificar_diagnostico").jqxComboBox(
	{
		theme: mytheme,
		width: 260,
		height: 20,
		source: DiagnosticoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo',
	});
	
	$("#maquinaria_modificar_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	
	var PartesSource =
	{
		datatype: "json",
		datafields: [
			{ name: "ID", type: "int"},
			{ name: "Parte", type: "string"},
		],
		data: {"Maquinaria_Repuestos_Partes":true},
		url: "modulos/parametros.php",
	};
	var PartesDataAdapter = new $.jqx.dataAdapter(PartesSource);
	
	$("#maquinaria_modificar_partes").jqxComboBox(
	{
		theme: mytheme,
		source: PartesDataAdapter,
		width: 150,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Parte',
		selectedIndex: -1,
		displayMember: 'Parte',
		valueMember: 'ID'
	});
	$("#maquinaria_modificar_partes").on("change", function (event)
	{
		if (event.args)
		{
			//
		}
		else
		{
			var item_value = $("#maquinaria_modificar_partes").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#maquinaria_modificar_partes").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#maquinaria_modificar_partes").val();
				
				var item = $("#maquinaria_modificar_partes").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#maquinaria_modificar_partes").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#maquinaria_modificar_partes").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#maquinaria_modificar_partes_add").jqxButton({
		theme: mytheme, 
		template: "success", 
		width: 55,
		height: 50
	});
	$("#maquinaria_modificar_partes_add").on("click", function ()
	{
		var Parte = $("#maquinaria_modificar_partes").jqxComboBox('getSelectedItem');
		var Problema = $("#maquinaria_modificar_partes_problema").val();
		var Diagnostico = $("#maquinaria_modificar_partes_diagnostico").val();
		
		if (!Parte)
		{
			Alerts_Box("Favor Ingresar la Parte.", 3);
			WaitClick_Combobox("maquinaria_modificar_partes");
			return;
		}
		
		if (Problema == "")
		{
			Alerts_Box("Favor Ingresar el Problema que Presenta.", 3);
			WaitClick_Input("maquinaria_modificar_partes_problema");
			return;
		}
		
		/*if (Diagnostico == "")
		{
			Alerts_Box("Favor Ingresar el Diagnostico Final.", 3);
			WaitClick_Input("maquinaria_modificar_partes_diagnostico");
			return;
		}*/
		
		//var datainfo = $("#maquinaria_modificar_partes_items_grid").jqxGrid("getdatainformation");
		//var count = datainfo.rowscount;
		
		var datarow = [{
			"Parte":Parte.label,
			"Problema":Problema,
			"Diagnostico":Diagnostico,
		}];
		
		$("#maquinaria_modificar_partes_items_grid").jqxGrid("addrow", null, datarow);
		$("#maquinaria_modificar_partes").jqxComboBox('clearSelection');
		$("#maquinaria_modificar_partes_problema").val("");
		$("#maquinaria_modificar_partes_diagnostico").val("");
	});
	
	$("#maquinaria_modificar_partes_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 930,
		autoheight: true,
		enabletooltips: true,
		enablebrowserselection: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				filterable: false,
				width: '3%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#maquinaria_modificar_partes_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#maquinaria_modificar_partes_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#maquinaria_modificar_partes_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#maquinaria_modificar_partes_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Parte', datafield: 'Parte', editable: false, width: '15%', height: 20, filterable: true },
			{ text: 'Problema que Presenta', datafield: 'Problema', editable: true, width: '41%', height: 20, filterable: true },
			{ text: 'Diagnostico Final', datafield: 'Diagnostico', editable: true, width: '41%', height: 20, filterable: true },
		],
	});
	$("#maquinaria_modificar_partes_items_grid").jqxGrid('localizestrings', localizationobj);
	
	//******************************************************************************************************//
	//------------------------------------------------------------------------------------------------------//
	//-------------------------------------------- PARTE 2 -------------------------------------------------//
	//------------------------------------------------------------------------------------------------------//
	//******************************************************************************************************//

	//**********************************************//
	//												//
	//					KEY JUMPS					//
	//												//
	//**********************************************//
	
	$("#maquinaria_modificar_codigo1").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_producto1").jqxComboBox('focus');
		}
	});
	
	$("#maquinaria_modificar_producto1").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_cantidad1").jqxNumberInput('focus');
			var input = $("#maquinaria_modificar_cantidad1 input")[0];
			if ('selectionStart' in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', 0);
				range.select();
			}
		}
	});
	
	$("#maquinaria_modificar_cantidad1").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_unitario1").jqxNumberInput('focus');
			var input = $("#maquinaria_modificar_unitario1 input")[0];
			if ('selectionStart' in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', 0);
				range.select();
			}
		}
	});
	
	$("#maquinaria_modificar_unitario1").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row(1);
		}
	});
	
	//---
	
	$("#maquinaria_modificar_codigo2").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_producto2").jqxComboBox('focus');
		}
	});
	
	$("#maquinaria_modificar_producto2").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_cantidad2").jqxNumberInput('focus');
			var input = $("#maquinaria_modificar_cantidad2 input")[0];
			if ('selectionStart' in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', 0);
				range.select();
			}
		}
	});
	
	$("#maquinaria_modificar_cantidad2").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_unitario2").jqxNumberInput('focus');
			var input = $("#maquinaria_modificar_unitario2 input")[0];
			if ('selectionStart' in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', 0);
				range.select();
			}
		}
	});
	
	$("#maquinaria_modificar_unitario2").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row(2);
		}
	});
	
	//---
	
	$("#maquinaria_modificar_codigo3").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_producto3").jqxComboBox('focus');
		}
	});
	
	$("#maquinaria_modificar_producto3").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_cantidad3").jqxNumberInput('focus');
			var input = $("#maquinaria_modificar_cantidad3 input")[0];
			if ('selectionStart' in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', 0);
				range.select();
			}
		}
	});
	
	$("#maquinaria_modificar_cantidad3").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_modificar_unitario3").jqxNumberInput('focus');
			var input = $("#maquinaria_modificar_unitario3 input")[0];
			if ('selectionStart' in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', 0);
				range.select();
			}
		}
	});
	
	$("#maquinaria_modificar_unitario3").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row(3);
		}
	});
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'},
		{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
		async: true
	};
	var ClienteDataAdapter1 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter2 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter3 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter4 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter5 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter6 = new $.jqx.dataAdapter(ClienteSource);
	
	var RepuestosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'},
		],
		url: "modulos/datos_productos.php",
		data: {"Repuestos":true},
		async: true
	};
	var RepuestosDataAdapter1 = new $.jqx.dataAdapter(RepuestosSource);
	var RepuestosDataAdapter2 = new $.jqx.dataAdapter(RepuestosSource);
	var RepuestosDataAdapter3 = new $.jqx.dataAdapter(RepuestosSource);
	var RepuestosDataAdapter4 = new $.jqx.dataAdapter(RepuestosSource);
	var RepuestosDataAdapter5 = new $.jqx.dataAdapter(RepuestosSource);
	var RepuestosDataAdapter6 = new $.jqx.dataAdapter(RepuestosSource);
	
	function Calcular (ID)
	{
		var Total = 0;
		
		var datainfo = $("#maquinaria_modificar_items_grid"+ID+"").jqxGrid('getdatainformation');
		var count = datainfo.rowscount;
		
		if (count <= 0) {
			return;
		}
		for (i=0; i<count; i++)
		{
			var currentRow = $("#maquinaria_modificar_items_grid"+ID+"").jqxGrid('getrowdata', i);
			var SubTotal = currentRow.Cantidad * currentRow.Unitario;
			Total = Total + SubTotal;
		}
		
		$("#maquinaria_modificar_total_proveedor"+ID+"").val(Total);
		
		var Total_Total = $("#maquinaria_modificar_total_proveedor1").val() + $("#maquinaria_modificar_total_proveedor2").val() + $("#maquinaria_modificar_total_proveedor3").val();
		
		$("#maquinaria_modificar_total").val(Total_Total);
		$("#maquinaria_modificar_producto"+ID+"").jqxComboBox('focus');
	};
	
	//**********************************************//
	//												//
	//					GRID 1						//
	//												//
	//**********************************************//
	
	function Add_Row(ID)
	{
		var Repuesto = $("#maquinaria_modificar_producto"+ID+"").jqxComboBox('getSelectedItem');
		var Cantidad = $("#maquinaria_modificar_cantidad"+ID+"").val();
		var Precio = $("#maquinaria_modificar_unitario"+ID+"").val();
		
		if (! Repuesto  | Repuesto <= 0) {
			Alerts_Box("Favor Seleccionar un Repuesto/Servicio", 3);
			WaitClick_Combobox("maquinaria_modificar_producto"+ID+"");
			return;
		}
		
		if (Cantidad <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("maquinaria_modificar_cantidad"+ID+"");
			return;
		}
		
		if (Precio <= 0) {
			Alerts_Box("Debe Ingresar un Precio Mayor a 0!", 3);
			WaitClick_NumberInput("maquinaria_modificar_unitario"+ID+"");
			return;
		}
		
		// Check for Duplicates
		var datinfo = $("#maquinaria_modificar_items_grid"+ID+"").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i = 0; i < count; i++) {
			var currentRow = $("#maquinaria_modificar_items_grid"+ID+"").jqxGrid('getrowdata', i);
			if (currentRow.Codigo == Repuesto.value)
			{
				var totalc = Cantidad + currentRow.Cantidad;
				var datarow = [{
					"Codigo":Repuesto.value,
					"Repuesto":Repuesto.label,
					"Unitario":Precio,
					"Cantidad":totalc
				}];
				var id = $("#maquinaria_modificar_items_grid"+ID+"").jqxGrid('getrowid', i);
				$("#maquinaria_modificar_items_grid"+ID+"").jqxGrid('deleterow', id);
				$("#maquinaria_modificar_items_grid"+ID+"").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#maquinaria_modificar_codigo"+ID+"").jqxComboBox('clearSelection');
				$("#maquinaria_modificar_producto"+ID+"").jqxComboBox('clearSelection');
				$("#maquinaria_modificar_cantidad"+ID+"").val('');
				$("#maquinaria_modificar_unitario"+ID+"").val('');
				Calcular(ID);
				return;
			}
		}
		// if not, then...
		var datarow = [{
			"Codigo":Repuesto.value,
			"Repuesto":Repuesto.label,
			"Unitario":Precio,
			"Cantidad":Cantidad
		}];
		$("#maquinaria_modificar_items_grid"+ID+"").jqxGrid("addrow", null, datarow, "first");
		// Clear Values
		$("#maquinaria_modificar_codigo"+ID+"").jqxComboBox('clearSelection');
		$("#maquinaria_modificar_producto"+ID+"").jqxComboBox('clearSelection');
		$("#maquinaria_modificar_cantidad"+ID+"").val('');
		$("#maquinaria_modificar_unitario"+ID+"").val('');
		Calcular(ID);
	};
	
	$("#maquinaria_modificar_codigo1").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: RepuestosDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Codigo',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#maquinaria_modificar_codigo1").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_modificar_codigo1").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_producto1").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_codigo1").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_producto1").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_producto1").jqxComboBox(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		source: RepuestosDataAdapter2,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Repuesto/Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#maquinaria_modificar_producto1").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_modificar_codigo1").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_producto1").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_producto1").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_codigo1").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_cantidad1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 80,
		inputMode: 'simple',
		textAlign: 'right',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#maquinaria_modificar_unitario1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 120,
		symbol: '$',
		digits: 11,
		max: 99999999999,
		
	});
	
	$("#maquinaria_modificar_addrowbutton1").jqxButton({theme: mytheme, template: "success", width: 55,});
	$("#maquinaria_modificar_addrowbutton1").on('click', function () {
		Add_Row(1);
	});
	
	var Grid1Source =
	{
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Repuesto ', type: 'string'},
			{ name: 'Cantidad ', type: 'decimal'},
			{ name: 'Unitario ', type: 'decimal'},
		],
	};
	var Grid1DataAdapter = new $.jqx.dataAdapter(Grid1Source);
	var Grid2DataAdapter = new $.jqx.dataAdapter(Grid1Source);
	var Grid3DataAdapter = new $.jqx.dataAdapter(Grid1Source);
	
	$("#maquinaria_modificar_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 768,
		source: Grid1DataAdapter,
		enabletooltips: true,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: "4%",
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function (row) {
					var selectedrowindex = $("#maquinaria_modificar_items_grid1").jqxGrid('getselectedrowindex');
					var rowscount = $("#maquinaria_modificar_items_grid1").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#maquinaria_modificar_items_grid1").jqxGrid('getrowid', selectedrowindex);
						$("#maquinaria_modificar_items_grid1").jqxGrid('deleterow', id);
						Calcular(1);
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', width: "13%", height: 20, editable: false, },
			{ text: 'Repuesto/Servicio', datafield: 'Repuesto', width: "29%", height: 20, editable: false, },
			{
				text: 'Unitario',
				datafield: 'Unitario',
				width: "17%",
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "El precio debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: "15%",
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Total',
				datafield: 'Total',
				width: "22%",
				height: 20,
				editable: false,
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total =  Math.round(parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad));
					return "<div style='margin: 4px;' class='jqx-right-align'>" + Grid1DataAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
		]
	});
	$("#maquinaria_modificar_items_grid1").jqxGrid('localizestrings', localizationobj);
	
	$("#maquinaria_modificar_proveedor1").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_modificar_proveedor1").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_modificar_proveedor1").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_proveedor_ID1").jqxComboBox('clearSelection');
		}
		else
			$("#maquinaria_modificar_proveedor_ID1").val(event.args.item.value);
	});
	
	$("#maquinaria_modificar_proveedor_ID1").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteDataAdapter2,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_modificar_proveedor_ID1").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_modificar_proveedor1").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_proveedor_ID1").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_proveedor_ID1").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_proveedor1").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_factura_proveedor1").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#maquinaria_modificar_total_proveedor1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '$ ',
		digits: 15,
		max: 999999999999999,
	});
	
	//**********************************************//
	//												//
	//					GRID 2						//
	//												//
	//**********************************************//
	
	$("#maquinaria_modificar_codigo2").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: RepuestosDataAdapter3,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Codigo',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#maquinaria_modificar_codigo2").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_modificar_codigo2").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_producto2").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_codigo2").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_producto2").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_producto2").jqxComboBox(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		source: RepuestosDataAdapter4,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Repuesto/Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#maquinaria_modificar_producto2").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_modificar_codigo2").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_producto2").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_producto2").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_codigo2").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_cantidad2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 80,
		inputMode: 'simple',
		textAlign: 'right',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#maquinaria_modificar_unitario2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 120,
		symbol: '$',
		digits: 11,
		max: 99999999999,
	});
	
	$("#maquinaria_modificar_addrowbutton2").jqxButton({theme: mytheme, template: "success", width: 55,});
	$("#maquinaria_modificar_addrowbutton2").on('click', function () {
		Add_Row(2);
	});
	
	$("#maquinaria_modificar_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 768,
		source: Grid2DataAdapter,
		enabletooltips: true,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: "4%",
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function (row) {
					var selectedrowindex = $("#maquinaria_modificar_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#maquinaria_modificar_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#maquinaria_modificar_items_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#maquinaria_modificar_items_grid2").jqxGrid('deleterow', id);
						Calcular(2);
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', width: "13%", height: 20 },
			{ text: 'Repuesto/Servicio', datafield: 'Repuesto', width: "29%", height: 20 },
			{
				text: 'Unitario',
				datafield: 'Unitario',
				width: "17%",
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "El precio debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: "15%",
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Total',
				datafield: 'Total',
				width: "22%",
				height: 20,
				editable: false,
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total =  Math.round(parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad));
					return "<div style='margin: 4px;' class='jqx-right-align'>" + Grid2DataAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
		]
	});
	$("#maquinaria_modificar_items_grid2").jqxGrid('localizestrings', localizationobj);
	
	$("#maquinaria_modificar_proveedor2").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteDataAdapter3,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_modificar_proveedor2").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_modificar_proveedor2").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_proveedor_ID2").jqxComboBox('clearSelection');
		}
		else
			$("#maquinaria_modificar_proveedor_ID2").val(event.args.item.value);
	});
	
	$("#maquinaria_modificar_proveedor_ID2").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteDataAdapter4,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_modificar_proveedor_ID2").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_modificar_proveedor2").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_proveedor_ID2").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_proveedor_ID2").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_proveedor2").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_factura_proveedor2").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#maquinaria_modificar_total_proveedor2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '$ ',
		digits: 15,
		max: 999999999999999,
	});
	
	//**********************************************//
	//												//
	//					GRID 3						//
	//												//
	//**********************************************//
	
	$("#maquinaria_modificar_codigo3").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: RepuestosDataAdapter5,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Codigo',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#maquinaria_modificar_codigo3").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_modificar_codigo3").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_producto3").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_codigo3").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_producto3").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_producto3").jqxComboBox(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		source: RepuestosDataAdapter6,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Repuesto/Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#maquinaria_modificar_producto3").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_modificar_codigo3").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_producto3").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_producto3").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_codigo3").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_cantidad3").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 80,
		inputMode: 'simple',
		textAlign: 'right',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#maquinaria_modificar_unitario3").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 120,
		symbol: '$',
		digits: 11,
		max: 99999999999,
	});
	
	$("#maquinaria_modificar_addrowbutton3").jqxButton({theme: mytheme, template: "success", width: 55,});
	$("#maquinaria_modificar_addrowbutton3").on('click', function () {
		Add_Row(3);
	});
	
	$("#maquinaria_modificar_items_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 768,
		source: Grid3DataAdapter,
		enabletooltips: true,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: "3%",
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function (row) {
					var selectedrowindex = $("#maquinaria_modificar_items_grid3").jqxGrid('getselectedrowindex');
					var rowscount = $("#maquinaria_modificar_items_grid3").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#maquinaria_modificar_items_grid3").jqxGrid('getrowid', selectedrowindex);
						$("#maquinaria_modificar_items_grid3").jqxGrid('deleterow', id);
						Calcular(3);
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', width: "13%", height: 20 },
			{ text: 'Repuesto/Servicio', datafield: 'Repuesto', width: "30%", height: 20 },
			{
				text: 'Unitario',
				datafield: 'Unitario',
				width: "17%",
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "El precio debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: "15%",
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Total',
				datafield: 'Total',
				width: "22%",
				height: 20,
				editable: false,
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total =  Math.round(parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad));
					return "<div style='margin: 4px;' class='jqx-right-align'>" + Grid3DataAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
		]
	});
	$("#maquinaria_modificar_items_grid3").jqxGrid('localizestrings', localizationobj);
	
	$("#maquinaria_modificar_proveedor3").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteDataAdapter5,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_modificar_proveedor3").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_modificar_proveedor3").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_proveedor_ID3").jqxComboBox('clearSelection');
		}
		else
			$("#maquinaria_modificar_proveedor_ID3").val(event.args.item.value);
	});
	
	$("#maquinaria_modificar_proveedor_ID3").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteDataAdapter6,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_modificar_proveedor_ID3").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_modificar_proveedor3").jqxComboBox('clearSelection');
			$("#maquinaria_modificar_proveedor_ID3").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_modificar_proveedor_ID3").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_modificar_proveedor3").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_modificar_factura_proveedor3").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#maquinaria_modificar_total_proveedor3").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '$ ',
		digits: 15,
		max: 999999999999999,
	});
	
	//**********************************************//
	//												//
	//					FINAL						//
	//												//
	//**********************************************//
	
	function Guardar_Cambios()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Estado = $("#maquinaria_modificar_estado").jqxComboBox('getSelectedItem');
		var Operador = $("#maquinaria_modificar_operador").jqxComboBox('getSelectedItem');
		var Mecanico = $("#maquinaria_modificar_mecanico").jqxComboBox('getSelectedItem');
		var Clasificacion = $("#maquinaria_modificar_clasificacion").jqxComboBox('getSelectedItem');
		var Tipo = $("#maquinaria_modificar_tipo").jqxComboBox('getSelectedItem');
		var Motivo = $("#maquinaria_modificar_motivo").jqxComboBox('getSelectedItem');
		var Diagnostico = $("#maquinaria_modificar_diagnostico").jqxComboBox('getSelectedItem');
		
		if (!Estado)
		{
			Alerts_Box("Debe Seleccionar un Estado.", 3);
			WaitClick_Combobox("maquinaria_modificar_estado");
			Locked = false;
			return;
		}
		
		if (!Operador)
		{
			Alerts_Box("Debe Seleccionar un Operador.", 3);
			WaitClick_Combobox("maquinaria_modificar_operador");
			Locked = false;
			return;
		}
		
		if (!Mecanico)
		{
			Alerts_Box("Debe Seleccionar un Mecanico.", 3);
			WaitClick_Combobox("maquinaria_modificar_mecanico");
			Locked = false;
			return;
		}
		
		if (!Clasificacion)
		{
			Alerts_Box("Debe Seleccionar una Clasificacion.", 3);
			WaitClick_Combobox("maquinaria_modificar_clasificacion");
			Locked = false;
			return;
		}
		
		if (!Tipo)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Clasificacion.", 3);
			WaitClick_Combobox("maquinaria_modificar_tipo");
			Locked = false;
			return;
		}
		
		if (!Motivo)
		{
			Alerts_Box("Debe Seleccionar un Motivo de Solicitud.", 3);
			WaitClick_Combobox("maquinaria_modificar_motivo");
			Locked = false;
			return;
		}
		
		if (!Diagnostico)
		{
			Alerts_Box("Debe Seleccionar un Diagnostico.", 3);
			WaitClick_Combobox("maquinaria_modificar_diagnostico");
			Locked = false;
			return;
		}
		
		var datainfo = $("#maquinaria_modificar_partes_items_grid").jqxGrid('getdatainformation');
		var count = datainfo.rowscount;
		var datainfo1 = $("#maquinaria_modificar_items_grid1").jqxGrid('getdatainformation');
		var count1 = datainfo1.rowscount;
		var datainfo2 = $("#maquinaria_modificar_items_grid2").jqxGrid('getdatainformation');
		var count2 = datainfo2.rowscount;
		var datainfo3 = $("#maquinaria_modificar_items_grid3").jqxGrid('getdatainformation');
		var count3 = datainfo3.rowscount;
		
		var Proveedor1 = $("#maquinaria_modificar_proveedor1").jqxComboBox('getSelectedItem');
		var Factura1 = $("#maquinaria_modificar_factura_proveedor1").val();
		var Total1 = $("#maquinaria_modificar_total_proveedor1").val();
		var Proveedor2 = $("#maquinaria_modificar_proveedor2").jqxComboBox('getSelectedItem');
		var Factura2 = $("#maquinaria_modificar_factura_proveedor2").val();
		var Total2 = $("#maquinaria_modificar_total_proveedor2").val();
		var Proveedor3 = $("#maquinaria_modificar_proveedor3").jqxComboBox('getSelectedItem');
		var Factura3 = $("#maquinaria_modificar_factura_proveedor3").val();
		var Total3 = $("#maquinaria_modificar_total_proveedor3").val();
		
		if (count < 1)
		{
			Alerts_Box("Debe ingresar al menos una parte con el problema que presenta.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count1 < 1 && Proveedor1)
		{
			Alerts_Box("El Proveedor 1 debe tener algun Repuesto/Servicio", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count1 > 0 && !Proveedor1)
		{
			Alerts_Box("Favor Seleccionar un Proveedor.", 3);
			WaitClick_Combobox("maquinaria_modificar_proveedor1");
			Locked = false;
			return;
		}
		
		if (count2 < 1 && Proveedor2)
		{
			Alerts_Box("El Proveedor 2 debe tener algun Repuesto/Servicio", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count2 > 0 && !Proveedor2)
		{
			Alerts_Box("Favor Seleccionar un Proveedor.", 3);
			WaitClick_Combobox("maquinaria_modificar_proveedor2");
			Locked = false;
			return;
		}
		
		if (count3 < 1 && Proveedor3)
		{
			Alerts_Box("El Proveedor 3 debe tener algun Repuesto/Servicio", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count3 > 0 && !Proveedor3)
		{
			Alerts_Box("Favor Seleccionar un Proveedor.", 3);
			WaitClick_Combobox("maquinaria_modificar_proveedor3");
			Locked = false;
			return;
		}
		
		if ($("#maquinaria_modificar_causa").val() == "")
		{
			Alerts_Box("Debe ingresar una Descripcion y Causa de la Falla.", 3);
			WaitClick_TextArea("maquinaria_modificar_causa");
			Locked = false;
			return;
		}
		
		if ($("#maquinaria_modificar_procedimiento").val() == "")
		{
			Alerts_Box("Debe ingresar un Procedimiento a Realizar.", 3);
			WaitClick_TextArea("maquinaria_modificar_procedimiento");
			Locked = false;
			return;
		}
		
		//---
		var MainArray = new Array();
		var GridArray = new Array();
		var GridArray1 = new Array();
		var GridArray2 = new Array();
		var GridArray3 = new Array();
		
		var myarray = {};
		
		myarray["Ord_Reparacion"] = $("#maquinaria_modificar_ord_reparacion").val();
		myarray["Estado"] = Estado.value;
		myarray["Operador"] = Operador.value;
		myarray["Mecanico"] = Mecanico.value;
		myarray["Fecha_Ini"] = GetFormattedDate($("#maquinaria_modificar_fecha_ini").jqxDateTimeInput('getDate'));
		myarray["Fecha_Fin"] = GetFormattedDate($("#maquinaria_modificar_fecha_fin").jqxDateTimeInput('getDate'));
		myarray["Clasificacion"] = Clasificacion.value;
		myarray["Tipo"] = Tipo.value;
		myarray["Motivo"] = Motivo.value;
		myarray["Diagnostico"] = Diagnostico.value;
		myarray["Total"] = $("#maquinaria_modificar_total").val();
		myarray["Causa"] = $("#maquinaria_modificar_causa").val();
		myarray["Procedimiento"] = $("#maquinaria_modificar_procedimiento").val();
		myarray["Observaciones"] = $("#maquinaria_modificar_observaciones").val();
		MainArray[0] = myarray;
		
		// Partes Grid
		for (var i = 0; i < count; i++)
		{
			var array = {};
			var currentRow = $("#maquinaria_modificar_partes_items_grid").jqxGrid("getrowdata", i);
			
			array["Parte"] = currentRow.Parte;
			array["Problema"] = currentRow.Problema;
			array["Diagnostico"] = currentRow.Diagnostico;
			
			GridArray[i] = array;
		};
		// Grid 1
		for (var a = 0; a < count1; a++)
		{
			var array = {};
			var currentRow1 = $("#maquinaria_modificar_items_grid1").jqxGrid('getrowdata', a);
			
			array["Codigo"] = currentRow1.Codigo;
			array["Cantidad"] = currentRow1.Cantidad;
			array["Unitario"] = currentRow1.Unitario;
			if (a == 0) {
				array["ClienteID"] = Proveedor1.value;
				array["Factura"] = Factura1;
				array["Total"] = Total1;
			}
			
			GridArray1[a] = array;
		};
		// Grid 2
		for (var b = 0; b < count2; b++)
		{
			var array = {};
			var currentRow2 = $("#maquinaria_modificar_items_grid2").jqxGrid('getrowdata', b);
			
			array["Codigo"] = currentRow2.Codigo;
			array["Cantidad"] = currentRow2.Cantidad;
			array["Unitario"] = currentRow2.Unitario;
			
			if (b == 0) {
				array["ClienteID"] = Proveedor2.value;
				array["Factura"] = Factura2;
				array["Total"] = Total2;
			}
			
			GridArray2[b] = array;
		};
		// Grid 3
		for (var c = 0; c < count3; c++)
		{
			var array = {};
			var currentRow3 = $("#maquinaria_modificar_items_grid3").jqxGrid('getrowdata', c);
			
			array["Codigo"] = currentRow3.Codigo;
			array["Cantidad"] = currentRow3.Cantidad;
			array["Unitario"] = currentRow3.Unitario;
			
			if (c == 0) {
				array["ClienteID"] = Proveedor3.value;
				array["Factura"] = Factura3;
				array["Total"] = Total3;
			}
			
			GridArray3[c] = array;
		};
		
		//---
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			url: "modulos/guardar.php",
			data: {
				"Maquinaria_Modificar":true,
				"MainData":MainArray,
				"Array":GridArray,
				"Array1":GridArray1,
				"Array2":GridArray2,
				"Array3":GridArray3
			},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Alerts_Box("Datos Guardados con Exito!", 2);
				$("#maquinaria_modificar_ord_reparacion").val(data);
				EnableDisableAll(true);
				Locked = false;
				// Buscar Ordenes
				OrdenSource.data =  {"Maquinaria_Modificar":true},
				OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
				$("#maquinaria_modificar_ord_reparacion").jqxComboBox({source: OrdenAdapter});
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!<br />Intente luego de unos segundos...", 3);
			}
		});
	};
	
	$("#maquinaria_modificar_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '$ ',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#maquinaria_modificar_guardar").jqxButton({
		width: 150,
		template: "info"
	});
	$("#maquinaria_modificar_guardar").on("click", function ()
	{
		if (!Admin && !Guardar)
			return;
		Guardar_Cambios();
	});
	
	$("#maquinaria_modificar_nuevo").jqxButton({
		width: 150,
		template: "success"
	});
	$("#maquinaria_modificar_nuevo").on("click", function ()
	{
		ClearDocument();
	});
	
	$("#maquinaria_modificar_imprimir").jqxButton({
		width: 150,
		template: "warning"
	});
	$("#maquinaria_modificar_imprimir").on("click", function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		window.open("imprimir/maquinaria.php?Interno="+$("#maquinaria_modificar_ord_reparacion").val()+"", "", "width=830, height=600, menubar=no, titlebar=no");
	});
	
	if (!Admin && !Guardar)
	{
		$("#maquinaria_modificar_guardar").jqxButton({ disabled: true });
		//$("#maquinaria_modificar_nuevo").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Imprimir)
	{
		$("#maquinaria_modificar_imprimir").jqxButton({ disabled: true });
	}
	
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Orden de Rep.
			</td>
			<td>
				<div id="maquinaria_modificar_ord_reparacion"></div>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
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
				Estado Actual
			</td>
			<td>
				<div id="maquinaria_modificar_estado"></div>
			</td>
			<td style="padding-right:40px;">
				Operario
			</td>
			<td>
				<div id="maquinaria_modificar_operador"></div>
			</td>
			<td>
				Mecánico
			</td>
			<td>
				<div id="maquinaria_modificar_mecanico"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha Inicial
			</td>
			<td>
				<div id="maquinaria_modificar_fecha_ini"></div>
			</td>
			<td>
				Clasificaci&oacute;n
			</td>
			<td>
				<div id="maquinaria_modificar_clasificacion"></div>
			</td>
			<td>
				Seleccionar
			</td>
			<td>
				<div id="maquinaria_modificar_tipo"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha Final
			</td>
			<td>
				<div id="maquinaria_modificar_fecha_fin"></div>
			</td>
			<td>
				Motivo de Solicitud
			</td>
			<td>
				<div id="maquinaria_modificar_motivo"></div>
			</td>
			<td>
				Diagnostico
			</td>
			<td>
				<div id="maquinaria_modificar_diagnostico"></div>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Parte
			</td>
			<td>
				Problema que Presenta
			</td>
			<td>
				Diagnostico Final
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td style="vertical-align:-webkit-baseline-middle;">
				<div id="maquinaria_modificar_partes"></div>
			</td>
			<td>
				<textarea rows="3" cols="47" id="maquinaria_modificar_partes_problema" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td>
				<textarea rows="3" cols="47" id="maquinaria_modificar_partes_diagnostico" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td>
				<input type="button" id="maquinaria_modificar_partes_add" value="Añadir"/>
			</td>
		</tr>
	</table>
	<div id="maquinaria_modificar_partes_items_grid"></div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<table cellpadding="1" cellspacing="0" style="margin-bottom:15px;">
		<tr>
			<td colspan="3" style="padding-right:10px; list-style:none; width:770px;">
				<li style="float:left;">
					<div style="width: 80px; height: 36px; background-color: #6E93CE; color: white; text-align:center;">
						<p style="margin:0px; padding-top:12px;">Proveedor 1</p>
					</div>
				</li>
				<li style="float:left;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr>
							<td>
								<div id="maquinaria_modificar_codigo1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_modificar_producto1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div style="margin-left:5px;">Cantidad</div>
							</td>
							<td>
								<div id="maquinaria_modificar_cantidad1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_modificar_unitario1" style="margin-left:5px;"></div>
							</td>
							<td>
								<input type="button" id="maquinaria_modificar_addrowbutton1" value="A&ntilde;adir" style="margin:0px 5px;">
							</td>
						</tr>
					</table>
				</li>
				<li style="float:left; margin-top:5px;">
					<div id="maquinaria_modificar_items_grid1"></div>
				</li>
			</td>
			<td style="list-style:none;">
				<li style="margin: 4px 0px 5px 0px;">
					Proveedor
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_modificar_proveedor1"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Proveedor ID
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_modificar_proveedor_ID1"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Factura Proveedor 1
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<input type="text" id="maquinaria_modificar_factura_proveedor1"/>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Total Proveedor 1
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<div id="maquinaria_modificar_total_proveedor1"></div>
				</li>
			</td>
		</tr>
		<!--  -->
		<tr>
			<td colspan="3" style="padding-right:10px; list-style:none; width:770px;">
				<li style="float:left;">
					<div style="width: 80px; height: 36px; background-color: #F18B0C; color: white; text-align:center;">
						<p style="margin:0px; padding-top:12px;">Proveedor 2</p>
					</div>
				</li>
				<li style="float:left;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr>
							<td>
								<div id="maquinaria_modificar_codigo2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_modificar_producto2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div style="margin-left:5px;">Cantidad</div>
							</td>
							<td>
								<div id="maquinaria_modificar_cantidad2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_modificar_unitario2" style="margin-left:5px;"></div>
							</td>
							<td>
								<input type="button" id="maquinaria_modificar_addrowbutton2" value="A&ntilde;adir" style="margin:0px 5px;">
							</td>
						</tr>
					</table>
				</li>
				<li style="float:left; margin-top:5px;">
					<div id="maquinaria_modificar_items_grid2"></div>
				</li>
			</td>
			<td style="list-style:none;">
				<li style="margin: 10px 0px 5px 0px;">
					Proveedor
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_modificar_proveedor2"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Proveedor ID
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_modificar_proveedor_ID2"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Factura Proveedor 2
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<input type="text" id="maquinaria_modificar_factura_proveedor2"/>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Total Proveedor 2
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<div id="maquinaria_modificar_total_proveedor2"></div>
				</li>
			</td>
		</tr>
		<!--  -->
		<tr>
			<td colspan="3" style="padding-right:10px; padding-top:15px; list-style:none; width:770px;">
				<li style="float:left;">
					<div style="width: 80px; height: 36px; background-color: #A08F41; color: white; text-align:center;">
						<p style="margin:0px; padding-top:12px;">Proveedor 3</p>
					</div>
				</li>
				<li style="float:left;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr>
							<td>
								<div id="maquinaria_modificar_codigo3" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_modificar_producto3" style="margin-left:5px;"></div>
							</td>
							<td>
								<div style="margin-left:5px;">Cantidad</div>
							</td>
							<td>
								<div id="maquinaria_modificar_cantidad3" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_modificar_unitario3" style="margin-left:5px;"></div>
							</td>
							<td>
								<input type="button" id="maquinaria_modificar_addrowbutton3" value="A&ntilde;adir" style="margin:0px 5px;">
							</td>
						</tr>
					</table>
				</li>
				<li style="float:left; margin-top:5px;">
					<div id="maquinaria_modificar_items_grid3"></div>
				</li>
			</td>
			<td style="list-style:none;">
				<li style="margin: 10px 0px 5px 0px;">
					Proveedor
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_modificar_proveedor3"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Proveedor ID
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_modificar_proveedor_ID3"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Factura Proveedor 3
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<input type="text" id="maquinaria_modificar_factura_proveedor3"/>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Total Proveedor 3
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<div id="maquinaria_modificar_total_proveedor3"></div>
				</li>
			</td>
		</tr>
		<!--  -->
		<tr>
			<td style="padding-top:10px;">
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Valor Total de la Orden
			</td>
		</tr>
		<tr>
			<td>
				Descripci&oacute;n y Causa de la Falla
			</td>
			<td>
				Procediimiento a Realizar
			</td>
			<td>
				Observaciones
			</td>
			<td>
				<div id="maquinaria_modificar_total"></div>
			</td>
		</tr>
		<tr>
			<td rowspan="3" style="padding:0px;">
				<textarea rows="5" cols="32" id="maquinaria_modificar_causa" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td rowspan="3" style="padding:0px;">
				<textarea rows="5" cols="32" id="maquinaria_modificar_procedimiento" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td rowspan="3" style="padding:0px;">
				<textarea rows="5" cols="32" id="maquinaria_modificar_observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td style="padding:0px;">
				<input type="button" id="maquinaria_modificar_guardar" value="Guardar"/>
			</td>
		</tr>
		<tr>
			<td style="padding:0px;">
				<input type="button" id="maquinaria_modificar_nuevo" value="Nuevo"/>
			</td>
		</tr>
		<tr>
			<td style="padding:0px;">
				<input type="button" id="maquinaria_modificar_imprimir" value="Imprimir"/>
			</td>
		</tr>
	</table>
</div>
