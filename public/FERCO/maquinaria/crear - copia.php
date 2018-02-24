<?php
session_start();
$Ord_Reparacion = isset($_POST["Ord_Reparacion"]) ? $_POST["Ord_Reparacion"]:"";
$ClienteID = isset($_POST["ClienteID"]) ? $_POST["ClienteID"]:"";
	if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
	{
		$_SESSION["NumOfID"]++;
	}
$NumOfID = $_SESSION["NumOfID"];
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Locked = false;
	var Timer1 = 0;
	var Timer2 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Maquinaria_Content");
	var Body = document.getElementById("Maquinaria_Crear_Content");
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
				SystemMap("Crear", true);
				ReDefine();
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
	
	function ReDefine()
	{
		ClearJSON = [
			//-1
			{id:"maquinaria_crear_ord_reparacion<?php echo $NumOfID ?>", type:""},
			{id:"maquinaria_crear_fecha_ini<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"maquinaria_crear_clasificacion<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_tipo<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_fecha_fin<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"maquinaria_crear_motivo<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_diagnostico<?php echo $NumOfID ?>", type:"jqxComboBox"},
			//
			{id:"maquinaria_crear_codigo1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_producto1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_cantidad1<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_unitario1<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_items_grid1<?php echo $NumOfID ?>", type:"jqxGrid"},
			{id:"maquinaria_crear_proveedor1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_total_proveedor1<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			//
			{id:"maquinaria_crear_codigo2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_producto2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_cantidad2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_unitario2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_items_grid2<?php echo $NumOfID ?>", type:"jqxGrid"},
			{id:"maquinaria_crear_proveedor2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_total_proveedor2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			//
			{id:"maquinaria_crear_codigo3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_producto3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_cantidad3<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_unitario3<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_items_grid3<?php echo $NumOfID ?>", type:"jqxGrid"},
			{id:"maquinaria_crear_proveedor3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_total_proveedor3<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			//
			{id:"maquinaria_crear_total<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_causa<?php echo $NumOfID ?>", type:""},
			{id:"maquinaria_crear_procedimiento<?php echo $NumOfID ?>", type:""},
		];
		
		EnableDisableJSON = [
			//-1
			{id:"maquinaria_crear_fecha_ini<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"maquinaria_crear_clasificacion<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_tipo<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_fecha_fin<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"maquinaria_crear_motivo<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_diagnostico<?php echo $NumOfID ?>", type:"jqxComboBox"},
			//
			{id:"maquinaria_crear_codigo1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_producto1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_cantidad1<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_unitario1<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_addrowbutton1<?php echo $NumOfID ?>", type:"jqxButton"},
			//{id:"maquinaria_crear_items_grid1<?php echo $NumOfID ?>", type:"jqxGrid"},
			{id:"maquinaria_crear_proveedor1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			//
			{id:"maquinaria_crear_codigo2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_producto2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_cantidad2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_unitario2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_addrowbutton2<?php echo $NumOfID ?>", type:"jqxButton"},
			//{id:"maquinaria_crear_items_grid2<?php echo $NumOfID ?>", type:"jqxGrid"},
			{id:"maquinaria_crear_proveedor2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			//
			{id:"maquinaria_crear_codigo3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_producto3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_cantidad3<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_unitario3<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"maquinaria_crear_addrowbutton3<?php echo $NumOfID ?>", type:"jqxButton"},
			//{id:"maquinaria_crear_items_grid3<?php echo $NumOfID ?>", type:"jqxGrid"},
			{id:"maquinaria_crear_proveedor3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			//
			{id:"maquinaria_crear_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
		];
	}
	ReDefine();
	
	function ClearDocument()
	{
		// Clean Variables
		Timer1 = 0;
		Timer2 = 0;
		//
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	};
	
	function LoadValues()
	{
		var Loaded = setInterval(function()
		{
			if (ClienteLoaded == true)
			{
				clearInterval(Loaded);
			
				var GetValuesSource =
				{
					datatype: "json",
					datafields: [
						{ name: 'Fecha_Ini', type: 'string'},
						{ name: 'Fecha_Fin', type: 'string' },
						{ name: 'Clasificacion', type: 'string'},
						{ name: 'Tipo', type: 'string' },
						{ name: 'Motivo', type: 'string' },
						{ name: 'Diagnostico', type: 'string' },
						{ name: 'Total', type: 'decimal' },
						{ name: 'Descripcion', type: 'string' },
						{ name: 'Procedimiento', type: 'string' },
						{ name: 'Proveedor1', type: 'string'},
						{ name: 'Proveedor2', type: 'string'},
						{ name: 'Proveedor3', type: 'string' },
					],
					data:{
						"Maquinaria_Modificar":"<?php echo $Ord_Reparacion ?>",
						"ClienteID":"<?php echo $ClienteID ?>"
					},
					url: "modulos/datos_productos.php",
					async: true
				};
				var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
					autoBind: true,
					loadComplete: function ()
					{
						var records = GetValuesAdapter.records;
						$("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid('clear');
						$("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid('clear');
						$("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid('clear');

						$("#maquinaria_crear_ord_reparacion<?php echo $NumOfID ?>").val("<?php echo $Ord_Reparacion ?>");
						$("#maquinaria_crear_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Ini"])));
						$("#maquinaria_crear_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Fin"])));
						$("#maquinaria_crear_clasificacion<?php echo $NumOfID ?>").val(records[0]["Clasificacion"]);
						$("#maquinaria_crear_tipo<?php echo $NumOfID ?>").val(records[0]["Tipo"]);
						$("#maquinaria_crear_motivo<?php echo $NumOfID ?>").val(records[0]["Motivo"]);
						$("#maquinaria_crear_diagnostico<?php echo $NumOfID ?>").val(records[0]["Diagnostico"]);
						$("#maquinaria_crear_total<?php echo $NumOfID ?>").val(records[0]["Total"]);
						$("#maquinaria_crear_causa<?php echo $NumOfID ?>").val(records[0]["Descripcion"]);
						$("#maquinaria_crear_procedimiento<?php echo $NumOfID ?>").val(records[0]["Procedimiento"]);
						
						var records1 = records[0]["Proveedor1"];
						var len1 = records1.length;
						if (len1 > 0 && records[0]["Proveedor1"] != "") {
							for (var i = 0; i < len1; i++)
							{
								if (i == 0)
								{
									$("#maquinaria_crear_proveedor1<?php echo $NumOfID ?>").val(records1[i]["ClienteID"]);
									$("#maquinaria_crear_factura_proveedor1<?php echo $NumOfID ?>").val(records1[i]["Factura"]);
									$("#maquinaria_crear_total_proveedor1<?php echo $NumOfID ?>").val(records1[i]["Total"]);
								}
								var datarow = [{
									"Codigo":records1[i]["Codigo"],
									"Repuesto":records1[i]["Repuesto"],
									"Unitario":records1[i]["Unitario"],
									"Cantidad":records1[i]["Cantidad"],
								}];
								$("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
							}
						}
						
						var records2 = records[0]["Proveedor2"];
						var len2 = records2.length;
						if (len2 > 0 && records[0]["Proveedor2"] != "") {
							for (var j = 0; j < len2; j++)
							{
								if (j == 0)
								{
									$("#maquinaria_crear_proveedor2<?php echo $NumOfID ?>").val(records2[j]["ClienteID"]);
									$("#maquinaria_crear_factura_proveedor2<?php echo $NumOfID ?>").val(records1[i]["Factura"]);
									$("#maquinaria_crear_total_proveedor2<?php echo $NumOfID ?>").val(records2[j]["Total"]);
								}
								var datarow = [{
									"Codigo":records2[j]["Codigo"],
									"Repuesto":records2[j]["Repuesto"],
									"Unitario":records2[j]["Unitario"],
									"Cantidad":records2[j]["Cantidad"],
								}];
								$("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
							}
						}
						
						var records3 = records[0]["Proveedor3"];
						var len3 = records3.length;
						if (len3 > 0 && records[0]["Proveedor3"] != "") {
							for (var k = 0; k < len3; k++)
							{
								if (k == 0)
								{
									$("#maquinaria_crear_proveedor3<?php echo $NumOfID ?>").val(records3[k]["ClienteID"]);
									$("#maquinaria_crear_factura_proveedor3<?php echo $NumOfID ?>").val(records1[i]["Factura"]);
									$("#maquinaria_crear_total_proveedor3<?php echo $NumOfID ?>").val(records3[k]["Total"]);
								}
								var datarow = [{
									"Codigo":records3[k]["Codigo"],
									"Repuesto":records3[k]["Repuesto"],
									"Unitario":records3[k]["Unitario"],
									"Cantidad":records3[k]["Cantidad"],
								}];
								$("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
							}
						}
					},
					loadError: function(jqXHR, status, error) {
						alert("Request failed: \n" + error);
					},
				});
			}
		},50);
	};
	
	$("#maquinaria_crear_ord_reparacion<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
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
	
	$("#maquinaria_crear_clasificacion<?php echo $NumOfID ?>").jqxComboBox(
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
	$("#maquinaria_crear_clasificacion<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_crear_clasificacion<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_tipo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_clasificacion<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				TipoSource.data = {"Caja_SubGrupo2":event.args.item.value};
				MotivoSource.data = {"Caja_SubGrupo":event.args.item.value};
				DataAdapter1 = new $.jqx.dataAdapter(TipoSource);
				DataAdapter2 = new $.jqx.dataAdapter(MotivoSource);
				$("#maquinaria_crear_tipo<?php echo $NumOfID ?>").jqxComboBox({source: DataAdapter1});
				$("#maquinaria_crear_motivo<?php echo $NumOfID ?>").jqxComboBox({source: DataAdapter2});
			},350);
		}
	});
	
	var TipoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo2', type: 'string'}
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	var MotivoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo', type: 'string'}
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#maquinaria_crear_tipo<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 270,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo2',
		valueMember: 'SubGrupo2'
	});
	
	$("#maquinaria_crear_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$("#maquinaria_crear_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	
	$("#maquinaria_crear_motivo<?php echo $NumOfID ?>").jqxComboBox(
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
	
	$("#maquinaria_crear_diagnostico<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 270,
		height: 20,
		source: DiagnosticoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo',
	});
	
	$("#maquinaria_crear_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$("#maquinaria_crear_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	
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
	
	$("#maquinaria_crear_codigo1<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_producto1<?php echo $NumOfID ?>").jqxComboBox('focus');
		}
	});
	
	$("#maquinaria_crear_producto1<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_cantidad1<?php echo $NumOfID ?>").jqxNumberInput('focus');
			var input = $("#maquinaria_crear_cantidad1<?php echo $NumOfID ?> input")[0];
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
	
	$("#maquinaria_crear_cantidad1<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_unitario1<?php echo $NumOfID ?>").jqxNumberInput('focus');
			var input = $("#maquinaria_crear_unitario1<?php echo $NumOfID ?> input")[0];
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
	
	$("#maquinaria_crear_unitario1<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row(1);
		}
	});
	
	//---
	
	$("#maquinaria_crear_codigo2<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_producto2<?php echo $NumOfID ?>").jqxComboBox('focus');
		}
	});
	
	$("#maquinaria_crear_producto2<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_cantidad2<?php echo $NumOfID ?>").jqxNumberInput('focus');
			var input = $("#maquinaria_crear_cantidad2<?php echo $NumOfID ?> input")[0];
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
	
	$("#maquinaria_crear_cantidad2<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_unitario2<?php echo $NumOfID ?>").jqxNumberInput('focus');
			var input = $("#maquinaria_crear_unitario2<?php echo $NumOfID ?> input")[0];
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
	
	$("#maquinaria_crear_unitario2<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row(2);
		}
	});
	
	//---
	
	$("#maquinaria_crear_codigo3<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_producto3<?php echo $NumOfID ?>").jqxComboBox('focus');
		}
	});
	
	$("#maquinaria_crear_producto3<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_cantidad3<?php echo $NumOfID ?>").jqxNumberInput('focus');
			var input = $("#maquinaria_crear_cantidad3<?php echo $NumOfID ?> input")[0];
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
	
	$("#maquinaria_crear_cantidad3<?php echo $NumOfID ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#maquinaria_crear_unitario3<?php echo $NumOfID ?>").jqxNumberInput('focus');
			var input = $("#maquinaria_crear_unitario3<?php echo $NumOfID ?> input")[0];
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
	
	$("#maquinaria_crear_unitario3<?php echo $NumOfID ?>").keyup(function(event) {
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
		async: false
	};
	var ClienteArray = new Array();
	var ClienteLoaded = false;
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ClienteArray.push(records[i]);
			}
		},
		loadComplete: function () {
			ClienteLoaded = true;
		}
	});
	
	var RepuestosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'},
		],
		url: "modulos/datos_productos.php",
		data: {"Repuestos":true},
		async: false
	};
	var RepuestosArray = new Array();
	var RepuestosDataAdapter = new $.jqx.dataAdapter(RepuestosSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				RepuestosArray.push(records[i]);
			}
		}
	});
	
	function Calcular (ID)
	{
		var Total = 0;
		
		var datainfo = $("#maquinaria_crear_items_grid"+ID+"<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count = datainfo.rowscount;
		
		if (count <= 0) {
			return;
		}
		for (i=0; i<count; i++)
		{
			var currentRow = $("#maquinaria_crear_items_grid"+ID+"<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
			var SubTotal = currentRow.Cantidad * currentRow.Unitario;
			Total = Total + SubTotal;
		}
		
		$("#maquinaria_crear_total_proveedor"+ID+"<?php echo $NumOfID ?>").val(Total);
		
		var Total_Total = $("#maquinaria_crear_total_proveedor1<?php echo $NumOfID ?>").val() + $("#maquinaria_crear_total_proveedor2<?php echo $NumOfID ?>").val() + $("#maquinaria_crear_total_proveedor3<?php echo $NumOfID ?>").val();
		
		$("#maquinaria_crear_total<?php echo $NumOfID ?>").val(Total_Total);
		$("#maquinaria_crear_producto"+ID+"<?php echo $NumOfID ?>").jqxComboBox('focus');
	};
	
	//**********************************************//
	//												//
	//					GRID 1						//
	//												//
	//**********************************************//
	
	function Add_Row(ID)
	{
		var Repuesto = $("#maquinaria_crear_producto"+ID+"<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Cantidad = $("#maquinaria_crear_cantidad"+ID+"<?php echo $NumOfID ?>").val();
		var Precio = $("#maquinaria_crear_unitario"+ID+"<?php echo $NumOfID ?>").val();
		
		if (! Repuesto  | Repuesto <= 0) {
			Alerts_Box("Favor Seleccionar un Repuesto/Servicio", 3);
			WaitClick_Combobox("maquinaria_crear_producto"+ID+"<?php echo $NumOfID ?>");
			return;
		}
		
		if (Cantidad <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("maquinaria_crear_cantidad"+ID+"<?php echo $NumOfID ?>");
			return;
		}
		
		if (Precio <= 0) {
			Alerts_Box("Debe Ingresar un Precio Mayor a 0!", 3);
			WaitClick_NumberInput("maquinaria_crear_unitario"+ID+"<?php echo $NumOfID ?>");
			return;
		}
		
		// Check for Duplicates
		var datinfo = $("#maquinaria_crear_items_grid"+ID+"<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i = 0; i < count; i++) {
			var currentRow = $("#maquinaria_crear_items_grid"+ID+"<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
			if (currentRow.Codigo == Repuesto.value)
			{
				var totalc = Cantidad + currentRow.Cantidad;
				var datarow = [{
					"Codigo":Repuesto.value,
					"Repuesto":Repuesto.label,
					"Unitario":Precio,
					"Cantidad":totalc
				}];
				var id = $("#maquinaria_crear_items_grid"+ID+"<?php echo $NumOfID ?>").jqxGrid('getrowid', i);
				$("#maquinaria_crear_items_grid"+ID+"<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
				$("#maquinaria_crear_items_grid"+ID+"<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#maquinaria_crear_codigo"+ID+"<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#maquinaria_crear_producto"+ID+"<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#maquinaria_crear_cantidad"+ID+"<?php echo $NumOfID ?>").val('');
				$("#maquinaria_crear_unitario"+ID+"<?php echo $NumOfID ?>").val('');
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
		$("#maquinaria_crear_items_grid"+ID+"<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
		// Clear Values
		$("#maquinaria_crear_codigo"+ID+"<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		$("#maquinaria_crear_producto"+ID+"<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		$("#maquinaria_crear_cantidad"+ID+"<?php echo $NumOfID ?>").val('');
		$("#maquinaria_crear_unitario"+ID+"<?php echo $NumOfID ?>").val('');
		Calcular(ID);
	};
	
	$("#maquinaria_crear_codigo1<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: RepuestosArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Codigo',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#maquinaria_crear_codigo1<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_crear_codigo1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_producto1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_codigo1<?php echo $NumOfID ?>").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_crear_producto1<?php echo $NumOfID ?>").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_producto1<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		source: RepuestosArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Repuesto/Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#maquinaria_crear_producto1<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_crear_codigo1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_producto1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_producto1<?php echo $NumOfID ?>").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_crear_codigo1<?php echo $NumOfID ?>").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_cantidad1<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 80,
		inputMode: 'simple',
		textAlign: 'right',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#maquinaria_crear_unitario1<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 120,
		symbol: '$',
		digits: 11,
		max: 99999999999,
		
	});
	
	$("#maquinaria_crear_addrowbutton1<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "success", width: 55,});
	$("#maquinaria_crear_addrowbutton1<?php echo $NumOfID ?>").on('click', function () {
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
	
	$("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid(
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
					var selectedrowindex = $("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid('getselectedrowindex');
					var rowscount = $("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid('getrowid', selectedrowindex);
						$("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
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
	$("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid('localizestrings', localizationobj);
	
	$("#maquinaria_crear_proveedor1<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_crear_proveedor1<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_crear_proveedor1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
		else
			$("#maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>").val(event.args.item.value);
	});
	
	$("#maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_crear_proveedor1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#maquinaria_crear_proveedor1<?php echo $NumOfID ?>").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_factura_proveedor1<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#maquinaria_crear_total_proveedor1<?php echo $NumOfID ?>").jqxNumberInput({
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
	
	$("#maquinaria_crear_codigo2<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: RepuestosArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Codigo',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#maquinaria_crear_codigo2<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_crear_codigo2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_producto2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_codigo2<?php echo $NumOfID ?>").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_crear_producto2").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_producto2<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		source: RepuestosArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Repuesto/Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#maquinaria_crear_producto2<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_crear_codigo2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_producto2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_producto2<?php echo $NumOfID ?>").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_crear_codigo2<?php echo $NumOfID ?>").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_cantidad2<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 80,
		inputMode: 'simple',
		textAlign: 'right',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#maquinaria_crear_unitario2<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 120,
		symbol: '$',
		digits: 11,
		max: 99999999999,
	});
	
	$("#maquinaria_crear_addrowbutton2<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "success", width: 55,});
	$("#maquinaria_crear_addrowbutton2<?php echo $NumOfID ?>").on('click', function () {
		Add_Row(2);
	});
	
	$("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid(
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
					var selectedrowindex = $("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid('getselectedrowindex');
					var rowscount = $("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid('getrowid', selectedrowindex);
						$("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
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
	$("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid('localizestrings', localizationobj);
	
	$("#maquinaria_crear_proveedor2<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_crear_proveedor2<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_crear_proveedor2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
		else
			$("#maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>").val(event.args.item.value);
	});
	
	$("#maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_crear_proveedor2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#maquinaria_crear_proveedor2<?php echo $NumOfID ?>").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_factura_proveedor2<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#maquinaria_crear_total_proveedor2<?php echo $NumOfID ?>").jqxNumberInput({
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
	
	$("#maquinaria_crear_codigo3<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: RepuestosArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Codigo',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#maquinaria_crear_codigo3<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_crear_codigo3<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_producto3<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_codigo3<?php echo $NumOfID ?>").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_crear_producto3<?php echo $NumOfID ?>").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_producto3<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 220,
		height: 20,
		source: RepuestosArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Repuesto/Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#maquinaria_crear_producto3<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#maquinaria_crear_codigo3<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_producto3<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_producto3<?php echo $NumOfID ?>").bind('select', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#maquinaria_crear_codigo3<?php echo $NumOfID ?>").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_cantidad3<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 80,
		inputMode: 'simple',
		textAlign: 'right',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#maquinaria_crear_unitario3<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 120,
		symbol: '$',
		digits: 11,
		max: 99999999999,
	});
	
	$("#maquinaria_crear_addrowbutton3<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "success", width: 55,});
	$("#maquinaria_crear_addrowbutton3<?php echo $NumOfID ?>").on('click', function () {
		Add_Row(3);
	});
	
	$("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid(
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
					var selectedrowindex = $("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid('getselectedrowindex');
					var rowscount = $("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid('getrowid', selectedrowindex);
						$("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
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
	$("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid('localizestrings', localizationobj);
	
	$("#maquinaria_crear_proveedor3<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_crear_proveedor3<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_crear_proveedor3<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
		else
			$("#maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>").val(event.args.item.value);
	});
	
	$("#maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#maquinaria_crear_proveedor3<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	$("#maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#maquinaria_crear_proveedor3<?php echo $NumOfID ?>").val(event.args.item.value);
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#maquinaria_crear_factura_proveedor3<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#maquinaria_crear_total_proveedor3<?php echo $NumOfID ?>").jqxNumberInput({
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
	
	function Crear()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if ($("#maquinaria_crear_clasificacion<?php echo $NumOfID ?>").val() <= 0 | $("#maquinaria_crear_clasificacion<?php echo $NumOfID ?>").val() == "") {
			Alerts_Box("Debe Seleccionar una Clasificacion.", 3);
			WaitClick_Combobox("maquinaria_crear_clasificacion<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#maquinaria_crear_tipo<?php echo $NumOfID ?>").val() <= 0 | $("#maquinaria_crear_tipo<?php echo $NumOfID ?>").val() == "") {
			Alerts_Box("Debe Seleccionar un Tipo de Clasificacion.", 3);
			WaitClick_Combobox("maquinaria_crear_tipo<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#maquinaria_crear_motivo<?php echo $NumOfID ?>").val() <= 0 | $("#maquinaria_crear_motivo<?php echo $NumOfID ?>").val() == "") {
			Alerts_Box("Debe Seleccionar un Motivo de Solicitud.", 3);
			WaitClick_Combobox("maquinaria_crear_motivo<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#maquinaria_crear_diagnostico<?php echo $NumOfID ?>").val() <= 0 | $("#maquinaria_crear_diagnostico<?php echo $NumOfID ?>").val() == "") {
			Alerts_Box("Debe Seleccionar un Diagnostico.", 3);
			WaitClick_Combobox("maquinaria_crear_diagnostico<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		var datainfo1 = $("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count1 = datainfo1.rowscount;
		var datainfo2 = $("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count2 = datainfo2.rowscount;
		var datainfo3 = $("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count3 = datainfo3.rowscount;
		
		var Proveedor1 = $("#maquinaria_crear_proveedor1<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Factura1 = $("#maquinaria_crear_factura_proveedor1<?php echo $NumOfID ?>").val();
		var Total1 = $("#maquinaria_crear_total_proveedor1<?php echo $NumOfID ?>").val();
		var Proveedor2 = $("#maquinaria_crear_proveedor2<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Factura2 = $("#maquinaria_crear_factura_proveedor2<?php echo $NumOfID ?>").val();
		var Total2 = $("#maquinaria_crear_total_proveedor2<?php echo $NumOfID ?>").val();
		var Proveedor3 = $("#maquinaria_crear_proveedor3<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Factura3 = $("#maquinaria_crear_factura_proveedor3<?php echo $NumOfID ?>").val();
		var Total3 = $("#maquinaria_crear_total_proveedor3<?php echo $NumOfID ?>").val();
		
		if (count1 <= 0 && Proveedor1) {
			Alerts_Box("El Proveedor 1 debe tener algun Repuesto/Servicio", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count1 > 0 && !Proveedor1) {
			Alerts_Box("Favor Seleccionar un Proveedor.", 3);
			WaitClick_Combobox("maquinaria_crear_proveedor1<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (count2 <= 0 && Proveedor2) {
			Alerts_Box("El Proveedor 2 debe tener algun Repuesto/Servicio", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count2 > 0 && !Proveedor2) {
			Alerts_Box("Favor Seleccionar un Proveedor.", 3);
			WaitClick_Combobox("maquinaria_crear_proveedor2<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (count3 <= 0 && Proveedor3) {
			Alerts_Box("El Proveedor 3 debe tener algun Repuesto/Servicio", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count3 > 0 && !Proveedor3) {
			Alerts_Box("Favor Seleccionar un Proveedor.", 3);
			WaitClick_Combobox("maquinaria_crear_proveedor3<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#maquinaria_crear_causa<?php echo $NumOfID ?>").val() == "")
		{
			Alerts_Box("Debe ingresar una Descripcion y Causa de la Falla.", 3);
			WaitClick_TextArea("maquinaria_crear_causa<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#maquinaria_crear_procedimiento<?php echo $NumOfID ?>").val() == "")
		{
			Alerts_Box("Debe ingresar un Procedimiento a Realizar.", 3);
			WaitClick_TextArea("maquinaria_crear_procedimiento<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		//---
		var MainArray = new Array();
		var GridArray1 = new Array();
		var GridArray2 = new Array();
		var GridArray3 = new Array();
		
		var myarray = {};
		
		myarray["Fecha_Ini"] = GetFormattedDate($("#maquinaria_crear_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
		myarray["Fecha_Fin"] = GetFormattedDate($("#maquinaria_crear_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
		myarray["Clasificacion"] = $("#maquinaria_crear_clasificacion<?php echo $NumOfID ?>").val();
		myarray["Tipo"] = $("#maquinaria_crear_tipo<?php echo $NumOfID ?>").val();
		myarray["Motivo"] = $("#maquinaria_crear_motivo<?php echo $NumOfID ?>").val();
		myarray["Diagnostico"] = $("#maquinaria_crear_diagnostico<?php echo $NumOfID ?>").val();
		myarray["Total"] = $("#maquinaria_crear_total<?php echo $NumOfID ?>").val();
		myarray["Causa"] = $("#maquinaria_crear_causa<?php echo $NumOfID ?>").val();
		myarray["Procedimiento"] = $("#maquinaria_crear_procedimiento<?php echo $NumOfID ?>").val();
		MainArray[0] = myarray;
		
		// Grid 1
		for (a = 0; a < count1; a++)
		{
			var array = {};
			var currentRow1 = $("#maquinaria_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid('getrowdata', a);
			
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
		for (b = 0; b < count2; b++)
		{
			var array = {};
			var currentRow2 = $("#maquinaria_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid('getrowdata', b);
			
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
		for (c = 0; c < count3; c++)
		{
			var array = {};
			var currentRow3 = $("#maquinaria_crear_items_grid3<?php echo $NumOfID ?>").jqxGrid('getrowdata', c);
			
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
				"Maquinaria_Crear":true,
				"MainData":MainArray,
				"Array1":GridArray1,
				"Array2":GridArray2,
				"Array3":GridArray3
			},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Alerts_Box("Datos Guardados con Exito!<br/>Orden de Reparacion Generado = \""+data+"\"", 2, true);
				$("#maquinaria_crear_ord_reparacion<?php echo $NumOfID ?>").val(data);
				EnableDisableAll(true);
				Locked = false;
				Timer1 = setInterval(function(){
					if (ClickOK == true)
					{
						ClearDocument();
						ClickOK = false;
						clearInterval(Timer1);
						clearTimeout(Timer2);
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(Timer1);
						clearTimeout(Timer2);
					}
				},10);
				Timer2 = setTimeout(function(){
					$("#Ask_Alert").jqxWindow('close');
					clearInterval(Timer1);
					ClickCANCEL = true;
				},10000);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!<br />Intente luego de unos segundos...", 3);
				Locked = false;
			}
		});
	};
	
	$("#maquinaria_crear_total<?php echo $NumOfID ?>").jqxNumberInput({
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
	
	$("#maquinaria_crear_guardar<?php echo $NumOfID ?>").jqxButton({
		width: 150,
		template: "info"
	});
	$("#maquinaria_crear_guardar<?php echo $NumOfID ?>").bind('click', function ()
	{
		Crear();
	});
	
	$("#maquinaria_crear_nuevo<?php echo $NumOfID ?>").jqxButton({
		width: 150,
		template: "success"
	});
	$("#maquinaria_crear_nuevo<?php echo $NumOfID ?>").bind('click', function ()
	{
		ClearDocument();
	});
	
	$("#maquinaria_crear_imprimir<?php echo $NumOfID ?>").jqxButton({
		width: 150,
		template: "warning"
	});
	$("#maquinaria_crear_imprimir<?php echo $NumOfID ?>").bind('click', function ()
	{
		//
	});
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Maquinaria" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "false")
					{
			?>
						$("#maquinaria_crear_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
						$("#maquinaria_crear_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#maquinaria_crear_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
				}
			}
		} ?>
		
	if ("<?php echo $Ord_Reparacion ?>" != "")
	{
		EnableDisableAll(true);
		$("#maquinaria_crear_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		LoadValues();
	}
	else
		CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				<p style="width: 85px; margin: 0px;">
					Orden de Rep.
				</p>
			</td>
			<td>
				<input type="text" id="maquinaria_crear_ord_reparacion<?php echo $NumOfID ?>"/>
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
				Fecha Inicial
			</td>
			<td>
				<div id="maquinaria_crear_fecha_ini<?php echo $NumOfID ?>"></div>
			</td>
			<td style="padding-right:40px;">
				Clasificaci&oacute;n
			</td>
			<td>
				<div id="maquinaria_crear_clasificacion<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Seleccionar
			</td>
			<td>
				<div id="maquinaria_crear_tipo<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha Final
			</td>
			<td>
				<div id="maquinaria_crear_fecha_fin<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Motivo de Solicitud
			</td>
			<td>
				<div id="maquinaria_crear_motivo<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Diagnostico
			</td>
			<td>
				<div id="maquinaria_crear_diagnostico<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
	</table>
</div>
<div id="Parte2">
	<table cellpadding="1" cellspacing="0" style="margin-bottom:15px;">
		<tr>
			<td colspan="2" style="padding-right:10px; list-style:none; width:770px;">
				<li style="float:left;">
					<div style="width: 80px; height: 36px; background-color: #6E93CE; color: white; text-align:center;">
						<p style="margin:0px; padding-top:12px;">Proveedor 1</p>
					</div>
				</li>
				<li style="float:left;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr>
							<td>
								<div id="maquinaria_crear_codigo1<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_crear_producto1<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div style="margin-left:5px;">Cantidad</div>
							</td>
							<td>
								<div id="maquinaria_crear_cantidad1<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_crear_unitario1<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<input type="button" id="maquinaria_crear_addrowbutton1<?php echo $NumOfID ?>" value="A&ntilde;adir" style="margin:0px 5px;">
							</td>
						</tr>
					</table>
				</li>
				<li style="float:left; margin-top:5px;">
					<div id="maquinaria_crear_items_grid1<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<td style="list-style:none;">
				<li style="margin: 4px 0px 5px 0px;">
					Proveedor
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_crear_proveedor1<?php echo $NumOfID ?>"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Proveedor ID
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_crear_proveedor_ID1<?php echo $NumOfID ?>"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Factura Proveedor 1
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<input type="text" id="maquinaria_crear_factura_proveedor1<?php echo $NumOfID ?>"/>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Total Proveedor 1
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<div id="maquinaria_crear_total_proveedor1<?php echo $NumOfID ?>"></div>
				</li>
			</td>
		</tr>
		<!--  -->
		<tr>
			<td colspan="2" style="padding-right:10px; list-style:none; width:770px;">
				<li style="float:left;">
					<div style="width: 80px; height: 36px; background-color: #F18B0C; color: white; text-align:center;">
						<p style="margin:0px; padding-top:12px;">Proveedor 2</p>
					</div>
				</li>
				<li style="float:left;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr>
							<td>
								<div id="maquinaria_crear_codigo2<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_crear_producto2<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div style="margin-left:5px;">Cantidad</div>
							</td>
							<td>
								<div id="maquinaria_crear_cantidad2<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_crear_unitario2<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<input type="button" id="maquinaria_crear_addrowbutton2<?php echo $NumOfID ?>" value="A&ntilde;adir" style="margin:0px 5px;">
							</td>
						</tr>
					</table>
				</li>
				<li style="float:left; margin-top:5px;">
					<div id="maquinaria_crear_items_grid2<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<td style="list-style:none;">
				<li style="margin: 10px 0px 5px 0px;">
					Proveedor
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_crear_proveedor2<?php echo $NumOfID ?>"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Proveedor ID
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_crear_proveedor_ID2<?php echo $NumOfID ?>"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Factura Proveedor 2
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<input type="text" id="maquinaria_crear_factura_proveedor2<?php echo $NumOfID ?>"/>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Total Proveedor 2
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<div id="maquinaria_crear_total_proveedor2<?php echo $NumOfID ?>"></div>
				</li>
			</td>
		</tr>
		<!--  -->
		<tr>
			<td colspan="2" style="padding-right:10px; padding-top:15px; list-style:none; width:770px;">
				<li style="float:left;">
					<div style="width: 80px; height: 36px; background-color: #A08F41; color: white; text-align:center;">
						<p style="margin:0px; padding-top:12px;">Proveedor 3</p>
					</div>
				</li>
				<li style="float:left;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr>
							<td>
								<div id="maquinaria_crear_codigo3<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_crear_producto3<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div style="margin-left:5px;">Cantidad</div>
							</td>
							<td>
								<div id="maquinaria_crear_cantidad3<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="maquinaria_crear_unitario3<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<input type="button" id="maquinaria_crear_addrowbutton3<?php echo $NumOfID ?>" value="A&ntilde;adir" style="margin:0px 5px;">
							</td>
						</tr>
					</table>
				</li>
				<li style="float:left; margin-top:5px;">
					<div id="maquinaria_crear_items_grid3<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<td style="list-style:none;">
				<li style="margin: 10px 0px 5px 0px;">
					Proveedor
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_crear_proveedor3<?php echo $NumOfID ?>"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Proveedor ID
				</li>
				<li style="margin: 0px 0px 10px 0px;">
					<div id="maquinaria_crear_proveedor_ID3<?php echo $NumOfID ?>"></div>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Factura Proveedor 3
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<input type="text" id="maquinaria_crear_factura_proveedor3<?php echo $NumOfID ?>"/>
				</li>
				<li style="margin: 0px 0px 5px 0px;">
					Total Proveedor 3
				</li>
				<li style="margin: 0px 0px 0px 0px;">
					<div id="maquinaria_crear_total_proveedor3<?php echo $NumOfID ?>"></div>
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
				<div id="maquinaria_crear_total<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td rowspan="3" style="padding:0px;">
				<textarea rows="5" cols="51" id="maquinaria_crear_causa<?php echo $NumOfID ?>" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td rowspan="3" style="padding:0px;">
				<textarea rows="5" cols="51" id="maquinaria_crear_procedimiento<?php echo $NumOfID ?>" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td style="padding:0px;">
				<input type="button" id="maquinaria_crear_guardar<?php echo $NumOfID ?>" value="Guardar"/>
			</td>
		</tr>
		<tr>
			<td style="padding:0px;">
				<input type="button" id="maquinaria_crear_nuevo<?php echo $NumOfID ?>" value="Nuevo"/>
			</td>
		</tr>
		<tr>
			<td style="padding:0px;">
				<input type="button" id="maquinaria_crear_imprimir<?php echo $NumOfID ?>" value="Imprimir"/>
			</td>
		</tr>
	</table>
</div>
