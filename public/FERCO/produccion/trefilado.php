<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var Ord_Produccion = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var RowAdded = true;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Produccion_Content");
	var Body = document.getElementById("Produccion_Trefilado_Content");
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
				SystemMap("Trefilado", true);
				ReDefine();
				ClearDocument();
				//---
				OrdenSource.data = {"Produccion_Ordenes_Trefilado": $("#produccion_trefilado_estado").val()};
				var OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
				$("#produccion_trefilado_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
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
				if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Trefilado" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Trefilado" && $data[$i]["Imprimir"] == "true")
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
			{id:"produccion_trefilado_ord_produccion", type:"jqxComboBox"},
			{id:"produccion_trefilado_destino", type:""},
			{id:"produccion_trefilado_ord_compra", type:""},
			{id:"produccion_trefilado_operario", type:""},
			{id:"produccion_trefilado_cliente", type:""},
			{id:"produccion_trefilado_cliente_ID", type:""},
			{id:"produccion_trefilado_fecha", type:"jqxDateTimeInput"},
			//-- left - grid 1
			{id:"produccion_trefilado_productos_grid1", type:"jqxGrid"},
			//-- left - grid 2
			{id:"produccion_trefilado_codigo1", type:"jqxComboBox"},
			{id:"produccion_trefilado_producto1", type:"jqxComboBox"},
			{id:"produccion_trefilado_cantidad1", type:""},
			{id:"produccion_trefilado_productos_grid2", type:"jqxGrid"},
			//-- left - grid 3
			{id:"produccion_trefilado_productos_grid3", type:"jqxGrid"},
			//-- left - grid 4
			{id:"produccion_trefilado_codigo2", type:"jqxComboBox"},
			{id:"produccion_trefilado_producto2", type:"jqxComboBox"},
			{id:"produccion_trefilado_cantidad2", type:""},
			{id:"produccion_trefilado_productos_grid4", type:"jqxGrid"},
			//-- right
			{id:"produccion_trefilado_estado_trefilado", type:""},
			{id:"produccion_trefilado_avance_trefilado", type:"jqxNumberInput"},
			{id:"produccion_trefilado_estado_enderezado", type:""},
			{id:"produccion_trefilado_avance_enderezado", type:"jqxNumberInput"},
			{id:"produccion_trefilado_estado_soldado", type:""},
			{id:"produccion_trefilado_avance_soldado", type:"jqxNumberInput"},
			//
			{id:"produccion_trefilado_requerido", type:"jqxNumberInput"},
			{id:"produccion_trefilado_obtener", type:"jqxNumberInput"},
			{id:"produccion_trefilado_despunte", type:"jqxNumberInput"},
			{id:"produccion_trefilado_rendimiento", type:"jqxNumberInput"},
			{id:"produccion_trefilado_diferencia", type:"jqxNumberInput"},
			{id:"produccion_trefilado_obtener", type:"jqxNumberInput"},
			{id:"produccion_trefilado_avance", type:"jqxNumberInput"},
			//
			{id:"produccion_trefilado_digitador", type:""},
			{id:"produccion_trefilado_aprobador", type:""},
			{id:"produccion_trefilado_finalizador", type:""},
			//
			{id:"produccion_trefilado_observaciones", type:""},
		];
		
		EnableDisableJSON = [
			//-- left - grid 2
			{id:"produccion_trefilado_codigo1", type:"jqxComboBox"},
			{id:"produccion_trefilado_producto1", type:"jqxComboBox"},
			{id:"produccion_trefilado_cantidad1", type:"jqxNumberInput"},
			{id:"produccion_trefilado_addrowbutton1", type:"jqxButton"},
			//-- left - grid 4
			{id:"produccion_trefilado_codigo2", type:"jqxComboBox"},
			{id:"produccion_trefilado_producto2", type:"jqxComboBox"},
			{id:"produccion_trefilado_cantidad2", type:"jqxNumberInput"},
			{id:"produccion_trefilado_addrowbutton2", type:"jqxButton"},
			//-- right
			{id:"produccion_trefilado_observaciones", type:""},
			{id:"produccion_trefilado_iniciar", type:"jqxButton"},
			{id:"produccion_trefilado_finalizar", type:"jqxButton"},
			{id:"produccion_trefilado_imprimir", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Ord_Produccion = "";
		Locked = false;
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		if (!Admin && !Guardar)
		{
			$("#produccion_trefilado_iniciar").jqxButton({ disabled: true });
			$("#produccion_trefilado_finalizar").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#produccion_trefilado_imprimir").jqxButton({ disabled: true });
		}
	}
	
	//------------------------------------------- KEY JUMPS
	//-- 1º
	$("#produccion_trefilado_codigo1").keyup(function(event) {
		if(event.which == 13)
		{
			$("#produccion_trefilado_producto1").jqxComboBox('focus');
		}
	});
	$("#produccion_trefilado_producto1").keyup(function(event) {
		if(event.which == 13)
		{
			$("#produccion_trefilado_cantidad1").jqxNumberInput('focus');
			var input = $('#produccion_trefilado_cantidad1 input')[0];
			if ('selectionStart' in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', 0);
				range.select();
			}
			//$("#produccion_trefilado_cantidad1").jqxComboBox('focus');
		}
	});
	$("#produccion_trefilado_cantidad1").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row1();
			$("#produccion_trefilado_producto1").jqxComboBox('focus');
		}
	});
	//-- 2º
	$("#produccion_trefilado_codigo2").keyup(function(event) {
		if(event.which == 13)
		{
			$("#produccion_trefilado_producto2").jqxComboBox('focus');
		}
	});
	$("#produccion_trefilado_producto2").keyup(function(event) {
		if(event.which == 13)
		{
			$("#produccion_trefilado_cantidad2").jqxNumberInput('focus');
			var input = $('#produccion_trefilado_cantidad2 input')[0];
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
	$("#produccion_trefilado_cantidad2").keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row2();
			$("#produccion_trefilado_producto2").jqxComboBox('focus');
		}
	});
	
	var ClienteData = [];
	
	$.ajax(
	{
		dataType: 'json',
		url: "modulos/datos.php",
		async: false,
		success: function (data)
		{
			for (var i = 0; i < data.length; i++)
			{
				ClienteData[""+data[i]["ClienteID"]+""] = data[i]["Nombre"];
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(textStatus+ " - " +errorThrown);
		}
	});
	
	var EstadoValues = [
		{"Estado":"Pendiente"},
		{"Estado":"Proceso"},
		{"Estado":"Finalizado"},
	];
	
	$("#produccion_trefilado_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		source: EstadoValues,
		dropDownHeight: 100,
		promptText: 'Seleccionar',
		selectedIndex: 0,
		displayMember: 'Estado',
		valueMember: 'Estado',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#produccion_trefilado_estado").bind('change', function (event) {
		if (event.args && event.args.item != undefined)
		{
			var item = $("#produccion_trefilado_ord_produccion").jqxComboBox('getSelectedItem');
			if (item)
				ClearDocument();
			
			OrdenSource.data = {"Produccion_Ordenes_Trefilado": event.args.item.value};
			var OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
			$("#produccion_trefilado_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
		}
		else
		{
			$("#produccion_trefilado_estado").jqxComboBox({selectedIndex: 0});
		}
	});
	
	var OrdenSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Orden_Produccion', type: 'string'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#produccion_trefilado_ord_produccion").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 130,
		selectedIndex: -1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		displayMember: 'Orden_Produccion',
		valueMember: 'Orden_Produccion'
	});
	$("#produccion_trefilado_ord_produccion").bind('change', function (event) {
		if (event.args)
		{
			if (Ord_Produccion == event.args.item.value)
				return;
			
			Ord_Produccion = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (ClickOK == true)
				{
					EnableDisableAll(false);
					ClickOK = false;
				}
				//ClearAll("produccion_trefilado_ord_produccion");
				Add_Rows();
			},350);
		}
		else
			ClearDocument();
	});
	$("#produccion_trefilado_ord_produccion").bind('bindingComplete', function (event) {
		if (Ord_Produccion != "")
			$("#produccion_trefilado_ord_produccion").jqxComboBox('selectItem', Ord_Produccion);
	});
	OrdenSource.data = {"Produccion_Ordenes_Trefilado": $("#produccion_trefilado_estado").val()};
	var OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
	$("#produccion_trefilado_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
	
	$("#produccion_trefilado_destino").jqxInput({
		theme: mytheme,
		height: 20,
		width: 60,
		disabled: true,
	});
	
	$("#produccion_trefilado_ord_compra").jqxInput({
		theme: mytheme,
		height: 20,
		width: 90,
		disabled: true,
	});
	
	$("#produccion_trefilado_operario").jqxInput(
	{
		theme: mytheme,
		height: 20,
		width: 200,
		disabled: true,
	});
	
	$("#produccion_trefilado_cliente").jqxInput(
	{
		theme: mytheme,
		width: 333,
		height: 20,
		disabled: true,
	});
	
	$("#produccion_trefilado_cliente_ID").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
	});
	
	$("#produccion_trefilado_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 130,
		height: 20,
		showCalendarButton: false,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
		disabled: true
	});
	$("#produccion_trefilado_fecha").jqxDateTimeInput('setDate', new Date(currenttime));
	
	function CheckRealState()
	{
		var CheckSource = 
		{
			datatype: "json",
			datafields:
			[
				{ name: 'Estado_Real', type: 'string' },
			],
			type: 'GET',
			data: {"Produccion_Ordenes_Estado":Ord_Produccion},
			url: "modulos/datos.php",
			async: true
		};
		var CheckDataAdapter = new $.jqx.dataAdapter(CheckSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var myrecords = CheckDataAdapter.records;
				
				if (myrecords[0]["Estado_Real"] == "Pendiente")
				{
					Alerts_Box("La Orden Seleccionada, no se encuentra Arprobada.",4);
					
					document.getElementById("produccion_trefilado_iniciar").value = "GUARDAR";
					//$("#produccion_trefilado_iniciar").jqxButton({ disabled: true });
					$("#produccion_trefilado_finalizar").jqxButton({ disabled: true });
					//$("#produccion_trefilado_imprimir").jqxButton({ disabled: true });
				}
				else
				{
					if ($("#produccion_trefilado_estado").val() == "Pendiente")
						document.getElementById("produccion_trefilado_iniciar").value = "INICIAR";
					//$("#produccion_trefilado_imprimir").jqxButton({ disabled: false });
				}
				
				if (!Admin && !Guardar)
				{
					$("#produccion_trefilado_iniciar").jqxButton({ disabled: true });
					$("#produccion_trefilado_finalizar").jqxButton({ disabled: true });
				}
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	function Add_Rows()
	{
		if (Ord_Produccion == "")
		{
			return;
		}
		
		if (RowAdded === false)
		{
			RowAdded = true;
			return;
		}
		
		RowAdded = false;
		
		var GridSource = 
		{
			datatype: "json",
			datafields:
			[
				{ name: 'DestinoOrden', type: 'string' },
				{ name: 'Ord_Compra', type: 'string' },
				{ name: 'Interno', type: 'string' },
				{ name: 'OperarioID', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Fecha', type: 'string' },
				{ name: 'DigitadorID', type: 'string' },
				{ name: 'AprobadorID', type: 'string' },
				{ name: 'Observaciones', type: 'string' },
				{ name: 'EstadoOrden', type: 'string' },
				{ name: 'ProductosOrden', type: 'string' },
			],
			type: 'GET',
			data: {"Produccion_Trefilado":Ord_Produccion},
			url: "modulos/datos.php",
			async: true
		};
		var GridDataAdapter = new $.jqx.dataAdapter(GridSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var myrecords = GridDataAdapter.records;
				var len = myrecords.length;
				$("#produccion_trefilado_productos_grid1").jqxGrid('clear');
				$("#produccion_trefilado_productos_grid2").jqxGrid('clear');
				$("#produccion_trefilado_productos_grid3").jqxGrid('clear');
				$("#produccion_trefilado_productos_grid4").jqxGrid('clear');
				
				//---
				
				$("#produccion_trefilado_destino").val(myrecords[0]["DestinoOrden"]);
				$("#produccion_trefilado_ord_compra").val(myrecords[0]["Ord_Compra"]);
				$("#produccion_trefilado_operario").val(ClienteData[myrecords[0]["OperarioID"]]);
				$("#produccion_trefilado_cliente").val(ClienteData[myrecords[0]["ClienteID"]]);
				$("#produccion_trefilado_cliente_ID").val(myrecords[0]["ClienteID"]);
				$("#produccion_trefilado_fecha").jqxDateTimeInput("setDate", new Date(SetFormattedDate(myrecords[0]["Fecha"])));
				$("#produccion_trefilado_digitador").val(myrecords[0]["DigitadorID"]);
				$("#produccion_trefilado_aprobador").val(myrecords[0]["AprobadorID"]);
				
				if (myrecords[0]["EstadoOrden"] != "")
				{
					var len = myrecords[0]["EstadoOrden"].length;
					for (var i = 0; i < len; i++)
					{
						switch(myrecords[0]["EstadoOrden"][i]["Proceso"])
						{
							case "Trefilado":
								$("#produccion_trefilado_estado_trefilado").val(myrecords[0]["EstadoOrden"][i]["Estado"]);
								$("#produccion_trefilado_avance_trefilado").val(myrecords[0]["EstadoOrden"][i]["Avance"]);
								$("#produccion_trefilado_rendimiento").val(myrecords[0]["EstadoOrden"][i]["Rendimiento"]);
								$("#produccion_trefilado_finalizador").val(myrecords[0]["EstadoOrden"][i]["FinalizadorID"]);
								$("#produccion_trefilado_observaciones").val(myrecords[0]["EstadoOrden"][i]["Observaciones"]);
								// Disable Buttons
								if (myrecords[0]["EstadoOrden"][i]["Estado"] == "Proceso")
								{
									//$("#produccion_trefilado_iniciar").jqxButton({ disabled: true });
									document.getElementById("produccion_trefilado_iniciar").value = "GUARDAR";
									$("#produccion_trefilado_iniciar").jqxButton({ disabled: false });
									$("#produccion_trefilado_finalizar").jqxButton({ disabled: false });
								}
								else if (myrecords[0]["EstadoOrden"][i]["Estado"] == "Finalizado")
								{
									$("#produccion_trefilado_iniciar").jqxButton({ disabled: true });
									$("#produccion_trefilado_finalizar").jqxButton({ disabled: true });
								}
								else {
									document.getElementById("produccion_trefilado_iniciar").value = "INICIAR";
									$("#produccion_trefilado_iniciar").jqxButton({ disabled: false });
									$("#produccion_trefilado_finalizar").jqxButton({ disabled: false });
								}
							break;
							case "Corte y Enderezado":
								$("#produccion_trefilado_estado_enderezado").val(myrecords[0]["EstadoOrden"][i]["Estado"]);
								$("#produccion_trefilado_avance_enderezado").val(myrecords[0]["EstadoOrden"][i]["Avance"]);
							break;
							case "Electrosoldado":
								$("#produccion_trefilado_estado_soldado").val(myrecords[0]["EstadoOrden"][i]["Estado"]);
								$("#produccion_trefilado_avance_soldado").val(myrecords[0]["EstadoOrden"][i]["Avance"]);
							break;
						}
					}
				}
				
				if (myrecords[0]["ProductosOrden"] != "")
				{
					var Grid1_Array = new Array();
					var Grid2_Array = new Array();
					var Grid3_Array = new Array();
					var Index1 = 0;
					var Index2 = 0;
					var Index3 = 0;
					var len = myrecords[0]["ProductosOrden"].length;
					for (var i = 0; i < len; i++)
					{
						switch (myrecords[0]["ProductosOrden"][i]["Tipo"])
						{
							case "Requerido":
								var array = {};
								
								array["CodFab"] = myrecords[0]["ProductosOrden"][i]["CodFab"];
								array["Nombre"] = myrecords[0]["ProductosOrden"][i]["Nombre"];
								array["Peso"] = myrecords[0]["ProductosOrden"][i]["Peso"];
								array["UndMed"] = myrecords[0]["ProductosOrden"][i]["UndMed"];
								array["Cantidad"] = myrecords[0]["ProductosOrden"][i]["Cantidad"];
								array["PesoTotal"] = 0;
								
								Grid1_Array[Index1] = array;
								Index1++;
							break;
							case "Obtener":
								var array = {};
								
								array["CodFab"] = myrecords[0]["ProductosOrden"][i]["CodFab"];
								array["Nombre"] = myrecords[0]["ProductosOrden"][i]["Nombre"];
								array["Peso"] = myrecords[0]["ProductosOrden"][i]["Peso"];
								array["UndMed"] = myrecords[0]["ProductosOrden"][i]["UndMed"];
								array["Cantidad"] = myrecords[0]["ProductosOrden"][i]["Cantidad"];
								array["Maquinaria"] = myrecords[0]["ProductosOrden"][i]["Maquinaria"];
								array["OperarioID"] = myrecords[0]["ProductosOrden"][i]["OperarioID"];
								array["PesoTotal"] = 0;
								
								Grid2_Array[Index2] = array;
								Index2++;
							break;
							case "Despunte":
								var array = {};
								
								array["CodFab"] = myrecords[0]["ProductosOrden"][i]["CodFab"];
								array["Nombre"] = myrecords[0]["ProductosOrden"][i]["Nombre"];
								array["Peso"] = myrecords[0]["ProductosOrden"][i]["Peso"];
								array["UndMed"] = myrecords[0]["ProductosOrden"][i]["UndMed"];
								array["Cantidad"] = myrecords[0]["ProductosOrden"][i]["Cantidad"];
								array["PesoTotal"] = 0;
								
								Grid3_Array[Index3] = array;
								Index3++;
							break;
						}
					}
					GridSource1.localdata = { Grid1_Array };
					var GridDataAdapter1 = new $.jqx.dataAdapter(GridSource1);
					
					GridSource2.localdata = { Grid2_Array };
					var GridDataAdapter2 = new $.jqx.dataAdapter(GridSource2);
					
					GridSource3.localdata = { Grid2_Array };
					var GridDataAdapter3 = new $.jqx.dataAdapter(GridSource3);
					
					GridSource4.localdata = { Grid3_Array };
					var GridDataAdapter4 = new $.jqx.dataAdapter(GridSource4);
					
					$("#produccion_trefilado_productos_grid1").jqxGrid({source: GridDataAdapter1});
					$("#produccion_trefilado_productos_grid2").jqxGrid({source: GridDataAdapter2});
					$("#produccion_trefilado_productos_grid3").jqxGrid({source: GridDataAdapter3});
					$("#produccion_trefilado_productos_grid4").jqxGrid({source: GridDataAdapter4});
				}
				Calcular();
				RowAdded = true;
				CheckRealState();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	// ------------------------------------------ PARTE 2
	
	var ProductosSource1 =
	{
		datatype: "json",
		datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		data: {"Produccion_Hierro":true},
		url: "modulos/datos_productos.php",
		async: true
	};
	var ProductosDataAdapter1 = new $.jqx.dataAdapter(ProductosSource1);
	
	var ProductosSource2 =
	{
		datatype: "json",
		datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		data: {"Produccion_Despunte":true},
		url: "modulos/datos_productos.php",
		async: true
	};
	var ProductosDataAdapter2 = new $.jqx.dataAdapter(ProductosSource2);
	
	var NominaData = [];
	var NominaSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Nombre", type: "string"},
			{ name: "ClienteID", type: "string"}
		],
		id: "ClienteID",
		data: {"Clientes_Nomina":true},
		url: "modulos/datos.php",
		async: false
	};
	var NominaDataAdapter = new $.jqx.dataAdapter(NominaSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				NominaData.push(records[i]);
			}
		}
	});
	
	var MaquinariaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Maquina', type: 'string'},
		],
		type: 'GET',
		data: {"Maquinaria":true},
		url: "modulos/datos.php",
		async: true
	};
	var MaquinariaDataAdapter = new $.jqx.dataAdapter(MaquinariaSource);
	
	var GridSource1 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'PesoTotal', type: 'decimal' },
		],
	};
	
	var GridSource2 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'PesoTotal', type: 'decimal' },
		],
	};
	
	var GridSource3 = 
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'PesoTotal', type: 'decimal' },
			{ name: 'Maquinaria', type: 'string' },
			{ name: 'OperarioID', type: 'string' },
			{
				name: 'Operario',
				value: 'OperarioID',
				values: {
					source: NominaDataAdapter.records,
					value: 'ClienteID',
					name: 'Nombre'
				},
				type: 'string'
			},
		],
	};
	
	var GridSource4 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'PesoTotal', type: 'decimal' },
		],
	};
	
	// -------- LEFT
	
	$("#produccion_trefilado_productos_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 600,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: false,
		editmode: 'click',
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: '14%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '30%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: true,
				width: '13%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Und', datafield: 'UndMed', editable: false, width: '8%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '15%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Peso Total',
				datafield: 'PesoTotal',
				editable: false,
				width: '20%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 12 });
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = (parseFloat(rowdata.Peso) * parseFloat(rowdata.Cantidad)).toFixed(2);
					return "<div style='margin: 4px;' class='jqx-right-align'>" + total + "</div>";
				}
			},
		]
	});
	$("#produccion_trefilado_productos_grid1").jqxGrid('localizestrings', localizationobj);
		
	$("#produccion_trefilado_codigo1").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: ProductosDataAdapter1,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#produccion_trefilado_codigo1").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#produccion_trefilado_producto1").val() != event.args.item.value)
				$("#produccion_trefilado_producto1").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var value = $("#produccion_trefilado_codigo1").val();
			var item = $("#produccion_trefilado_codigo1").jqxComboBox('getItemByValue', value);
			if (item == undefined)
			{
				$("#produccion_trefilado_codigo1").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto1").jqxComboBox('clearSelection');
			}
			else
				$("#produccion_trefilado_codigo1").jqxComboBox('selectItem', item.value);
		}
	});
	
	$("#produccion_trefilado_producto1").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		source: ProductosDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#produccion_trefilado_producto1").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#produccion_trefilado_codigo1").val() != event.args.item.value)
				$("#produccion_trefilado_codigo1").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#produccion_trefilado_producto1").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#produccion_trefilado_codigo1").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto1").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#produccion_trefilado_producto1").val();
				
				var item = $("#produccion_trefilado_producto1").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#produccion_trefilado_producto1").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#produccion_trefilado_codigo1").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto1").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#produccion_trefilado_cantidad1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 70,
		inputMode: 'simple',
		spinButtons: false,
		digits:6,
	});
	
	function Add_Row1()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;
		
		var Product = $("#produccion_trefilado_producto1").jqxComboBox('getSelectedItem');
		var CantidadNum = $("#produccion_trefilado_cantidad1").val();
		
		if (!Product)
		{
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("produccion_trefilado_producto1");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum < 1)
		{
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("produccion_trefilado_cantidad1");
			RowAdded = true;
			return;
		}
		
		// Permitir Duplicados (Para repartir el trabajo entre diferentes empleados)
		/*
		var datinfo = $("#produccion_trefilado_productos_grid2").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i = 0; i < count; i++)
		{
			var currentRow = $("#produccion_trefilado_productos_grid2").jqxGrid('getrowdata', i);
			if (currentRow.CodFab == Product.value)
			{
				var totalc = CantidadNum + currentRow.Cantidad;
				
				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"Peso":currentRow.Peso,
					"UndMed":currentRow.UndMed,
					"Cantidad":totalc,
					"PesoTotal":currentRow.PesoTotal,
				}];
				var id = $("#produccion_trefilado_productos_grid2").jqxGrid('getrowid', i);
				$("#produccion_trefilado_productos_grid2").jqxGrid('deleterow', id);
				$("#produccion_trefilado_productos_grid3").jqxGrid('deleterow', id);
				$("#produccion_trefilado_productos_grid2").jqxGrid("addrow", null, datarow, "first");
				$("#produccion_trefilado_productos_grid3").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_trefilado_codigo1").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto1").jqxComboBox('clearSelection');
				$("#produccion_trefilado_cantidad1").val("0");
				RowAdded = true;
				Calcular();
				return;
			}
		}
		*/
		
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'UndMed', type: 'string'},
				{ name: 'Peso', type: 'decimal' },
			],
			type: 'GET',
			data:{"Productos":Product.value},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":Product.value,
					"Nombre":Product.label,
					"Peso":records[0]["Peso"],
					"UndMed":records[0]["UndMed"],
					"Cantidad":CantidadNum,
					"PesoTotal":0,
				}];
				$("#produccion_trefilado_productos_grid2").jqxGrid("addrow", null, datarow, "first");
				$("#produccion_trefilado_productos_grid3").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_trefilado_codigo1").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto1").jqxComboBox('clearSelection');
				$("#produccion_trefilado_cantidad1").val("0");
				Calcular();
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: "+error, 3);
			},
			downloadComplete: function(edata, textStatus, jqXHR) {
				//alert("Data is Loaded");
			}
		});
	};
	
	$("#produccion_trefilado_addrowbutton1").jqxButton({theme: mytheme, template: "success", width: 55,});
	$("#produccion_trefilado_addrowbutton1").on('click', function () {
		Add_Row1();
	});
	
	$("#produccion_trefilado_productos_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 600,
		//source: DataAdapter1,
		editable: true,
		sortable: true,
		pageable: true,
		autoheight: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '5%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					//return '<img style="margin:4px;" height="16" width="16" src="../images/close1.png" onclick="DelRow(' + row + ')"/>';
					return "X";
				},
				buttonclick: function (row) {
					var selectedrowindex = $("#produccion_trefilado_productos_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#produccion_trefilado_productos_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#produccion_trefilado_productos_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#produccion_trefilado_productos_grid2").jqxGrid('deleterow', id);
						$("#produccion_trefilado_productos_grid3").jqxGrid('deleterow', id);
						//---
						Calcular();
					}
				}
			},
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: '12%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '30%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: false,
				width: '13%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Und', datafield: 'UndMed', editable: false, width: '8%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '12%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Peso Total',
				datafield: 'PesoTotal',
				editable: false,
				width: '20%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 12 });
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = (parseFloat(rowdata.Peso) * parseFloat(rowdata.Cantidad)).toFixed(2);
					return "<div style='margin: 4px;' class='jqx-right-align'>" + total + "</div>";
				}
			},
		]
	});
	$("#produccion_trefilado_productos_grid2").jqxGrid('localizestrings', localizationobj);
	$("#produccion_trefilado_productos_grid2").on('cellvaluechanged', function (event) 
	{
		if (event.args)
		{
			if (event.args.datafield == "Cantidad")
				Calcular();
		}
	});
	
	$("#produccion_trefilado_productos_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 600,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: 0, height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '30%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: false,
				width: 0,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: false,
				width: 0,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Peso Total',
				datafield: 'PesoTotal',
				editable: false,
				width: '20%',
				height: 20,
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = (parseFloat(rowdata.Peso) * parseFloat(rowdata.Cantidad)).toFixed(2);
					return "<div style='margin: 4px;' class='jqx-right-align'>" + total + "</div>";
				},
			},
			{
				text: 'Maquinaria',
				datafield: 'Maquinaria',
				width: '25%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: MaquinariaDataAdapter,
						dropDownHeight: 125,
						dropDownWidth: 150,
						selectedIndex: -1,
						displayMember: 'Maquina',
						valueMember: 'Maquina',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Operario',
				datafield: 'OperarioID',
				displayfield: 'Operario',
				width: '25%',
				height: 20,
				columntype: 'combobox',
				createeditor: function (row, value, editor) {
					editor.jqxComboBox({
						source: NominaData,
						dropDownHeight: 125,
						dropDownWidth: 150,
						displayMember: 'Nombre',
						valueMember: 'ClienteID',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
		]
	});
	$("#produccion_trefilado_productos_grid3").jqxGrid('hidecolumn', 'CodFab');
	$("#produccion_trefilado_productos_grid3").jqxGrid('hidecolumn', 'Peso');
	$("#produccion_trefilado_productos_grid3").jqxGrid('hidecolumn', 'Cantidad');
	$("#produccion_trefilado_productos_grid3").jqxGrid('localizestrings', localizationobj);
	
	$("#produccion_trefilado_codigo2").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: ProductosDataAdapter2,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#produccion_trefilado_codigo2").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#produccion_trefilado_producto2").val() != event.args.item.value)
				$("#produccion_trefilado_producto2").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var value = $("#produccion_trefilado_codigo2").val();
			var item = $("#produccion_trefilado_codigo2").jqxComboBox('getItemByValue', value);
			if (item == undefined)
			{
				$("#produccion_trefilado_codigo2").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto2").jqxComboBox('clearSelection');
			}
			else
				$("#produccion_trefilado_codigo2").jqxComboBox('selectItem', item.value);
		}
	});
	
	$("#produccion_trefilado_producto2").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		source: ProductosDataAdapter2,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#produccion_trefilado_producto2").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#produccion_trefilado_codigo2").val() != event.args.item.value)
				$("#produccion_trefilado_codigo2").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#produccion_trefilado_producto2").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#produccion_trefilado_codigo2").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto2").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#produccion_trefilado_producto2").val();
				
				var item = $("#produccion_trefilado_producto2").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#produccion_trefilado_producto2").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#produccion_trefilado_codigo2").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto2").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#produccion_trefilado_cantidad2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 70,
		inputMode: 'simple',
		spinButtons: false,
		digits:6,
	});
	
	function Add_Row2()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;
		
		var Product = $("#produccion_trefilado_producto2").jqxComboBox('getSelectedItem');
		var CantidadNum = $("#produccion_trefilado_cantidad2").val();
		
		if (!Product) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("produccion_trefilado_producto2");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum < 1)
		{
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("produccion_trefilado_cantidad2");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#produccion_trefilado_productos_grid4").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#produccion_trefilado_productos_grid4").jqxGrid('getrowdata', i);
			if (currentRow.CodFab == Product.value)
			{
				var totalc = CantidadNum + currentRow.Cantidad;
				
				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"Peso":currentRow.Peso,
					"UndMed":currentRow.UndMed,
					"Cantidad":totalc,
					"PesoTotal":currentRow.PesoTotal,
				}];
				var id = $("#produccion_trefilado_productos_grid4").jqxGrid('getrowid', i);
				$("#produccion_trefilado_productos_grid4").jqxGrid('deleterow', id);
				$("#produccion_trefilado_productos_grid4").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_trefilado_codigo2").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto2").jqxComboBox('clearSelection');
				$("#produccion_trefilado_cantidad2").val("0");
				RowAdded = true;
				Calcular();
				return;
			}
		}
		
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'UndMed', type: 'string'},
				{ name: 'Peso', type: 'decimal' },
			],
			type: 'GET',
			data:{"Productos":Product.value},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":Product.value,
					"Nombre":Product.label,
					"Peso":records[0]["Peso"],
					"UndMed":records[0]["UndMed"],
					"Cantidad":CantidadNum,
					"PesoTotal":0,
				}];
				$("#produccion_trefilado_productos_grid4").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_trefilado_codigo2").jqxComboBox('clearSelection');
				$("#produccion_trefilado_producto2").jqxComboBox('clearSelection');
				$("#produccion_trefilado_cantidad2").val("0");
				Calcular();
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: "+error, 3);
			},
			downloadComplete: function(edata, textStatus, jqXHR) {
				//alert("Data is Loaded");
			}
		});
	};
	
	$("#produccion_trefilado_addrowbutton2").jqxButton({theme: mytheme, template: "success", width: 55,});
	$("#produccion_trefilado_addrowbutton2").on('click', function () {
		Add_Row2();
	});
	
	$("#produccion_trefilado_productos_grid4").jqxGrid(
	{
		theme: mytheme,
		width: 600,
		//source: DataAdapter2,
		editable: true,
		sortable: true,
		pageable: true,
		autoheight: true,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '5%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#produccion_trefilado_productos_grid4").jqxGrid('getselectedrowindex');
					var rowscount = $("#produccion_trefilado_productos_grid4").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#produccion_trefilado_productos_grid4").jqxGrid('getrowid', selectedrowindex);
						$("#produccion_trefilado_productos_grid4").jqxGrid('deleterow', id);
						Calcular();
					}
				}
			},
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: '12%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '30%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: false,
				width: '13%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Und', datafield: 'UndMed', editable: false, width: '8%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '12%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Peso Total',
				datafield: 'PesoTotal',
				editable: false,
				width: '20%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 12 });
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = (parseFloat(rowdata.Peso) * parseFloat(rowdata.Cantidad)).toFixed(2);
					return "<div style='margin: 4px;' class='jqx-right-align'>" + total + "</div>";
				}
			},
		]
	});
	$("#produccion_trefilado_productos_grid4").jqxGrid('localizestrings', localizationobj);
	$("#produccion_trefilado_productos_grid4").on('cellvaluechanged', function (event) 
	{
		if (event.args)
		{
			if (event.args.datafield == "Cantidad")
				Calcular();
		}
	});
	// -------- RIGHT
	
	function Calcular()
	{
		var Calc_Timer = setTimeout(function()
		{
			$("#produccion_trefilado_productos_grid3").jqxGrid('refreshdata');
			var datinfo = $("#produccion_trefilado_productos_grid2").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			for (var i = 0; i < count; i++)
			{
				var currentRow1 = $("#produccion_trefilado_productos_grid2").jqxGrid('getrowdata', i);
				var currentRow2 = $("#produccion_trefilado_productos_grid3").jqxGrid('getrowdata', i);
				//alert(currentRow1.Cantidad+" - "+currentRow2.Cantidad)
				if (currentRow1.Cantidad != currentRow2.Cantidad)
					$("#produccion_trefilado_productos_grid3").jqxGrid('setcellvalue', i, "Cantidad", currentRow1.Cantidad);
			}
			//---
			var Total1 = 0;
			var Total2 = 0;
			var Total3 = 0;
			var Total4 = 0;
			var datinfo1 = $("#produccion_trefilado_productos_grid1").jqxGrid('getdatainformation');
			var count1 = datinfo1.rowscount;
			var datinfo2 = $("#produccion_trefilado_productos_grid2").jqxGrid('getdatainformation');
			var count2 = datinfo2.rowscount;
			var datinfo3 = $("#produccion_trefilado_productos_grid4").jqxGrid('getdatainformation');
			var count3 = datinfo3.rowscount;

			if (count1 > 0)
			{
				for (i = 0; i < count1; i++)
				{
					var currentRow = $("#produccion_trefilado_productos_grid1").jqxGrid('getrowdata', i);
					var Tmp = currentRow.Peso * currentRow.Cantidad;
					Total1 += Math.round(Tmp * 100) / 100;//round 2 decimals
				}
				$("#produccion_trefilado_requerido").val(Total1);
			}
			
			if (count2 > 0)
			{
				for (i = 0; i < count2; i++)
				{
					var currentRow = $("#produccion_trefilado_productos_grid2").jqxGrid('getrowdata', i);
					var Tmp = currentRow.Peso * currentRow.Cantidad;
					Total2 += Math.round(Tmp * 100) / 100;//round 2 decimals
				}
				$("#produccion_trefilado_obtener").val(Total2);
			}
			
			if (count3 > 0)
			{
				for (i = 0; i < count3; i++)
				{
					var currentRow = $("#produccion_trefilado_productos_grid4").jqxGrid('getrowdata', i);
					var Tmp = currentRow.Peso * currentRow.Cantidad;
					Total3 += Math.round(Tmp * 100) / 100;//round 2 decimals
				}
				$("#produccion_trefilado_despunte").val(Total3);
			}
			
			Total4 = $("#produccion_trefilado_rendimiento").val();
			var Tmp = Total1 - (Total2 + Total3 + Total4);
			var Diferencia = Math.round(Tmp * 100) / 100;//round 2 decimals 
			var Element1 = document.getElementById("produccion_trefilado_diferencia");
			var Element2 = Element1.getElementsByTagName("input")[0];
			
			$("#produccion_trefilado_diferencia").val(Diferencia);
			
			/*if (Diferencia < 0)
			{
				ChangeClass("RedState2");
			}
			else if (Diferencia > 0)
			{
				ChangeClass("OrangeState2");
			}
			else
			{
				ChangeClass("GreenState2");
			}*/
			
			var Percent = (((Total1 - Diferencia) / Total1) * 100).toFixed(2);
			if (Percent < 0)
				Percent = 0;
				
			$("#produccion_trefilado_avance").val(Percent);
			clearTimeout(Calc_Timer);
		},200);
	}
	
	$("#produccion_trefilado_estado_trefilado").jqxInput(
	{
		theme: mytheme,
		width: 75,
		height: 20,
		disabled: true
	});
	
	$("#produccion_trefilado_avance_trefilado").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 60,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
		disabled: true
	});
	
	$("#produccion_trefilado_estado_enderezado").jqxInput(
	{
		theme: mytheme,
		width: 75,
		height: 20,
		disabled: true
	});
	
	$("#produccion_trefilado_avance_enderezado").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 60,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
		disabled: true
	});
	
	$("#produccion_trefilado_estado_soldado").jqxInput(
	{
		theme: mytheme,
		width: 75,
		height: 20,
		disabled: true
	});
	
	$("#produccion_trefilado_avance_soldado").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 60,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
		disabled: true
	});
	
	// -----
	
	$("#produccion_trefilado_requerido").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 7,
		disabled: true
	});
	
	$("#produccion_trefilado_obtener").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 7,
		disabled: true
	});
	
	$("#produccion_trefilado_despunte").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 7,
		disabled: true
	});
	
	$("#produccion_trefilado_rendimiento").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 7,
	});
	$("#produccion_trefilado_rendimiento").on('change', function (event)
	{
		Calcular();
	}); 
	
	$("#produccion_trefilado_diferencia").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 7,
		disabled: true
	});
	
	$("#produccion_trefilado_avance").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 60,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
		disabled: true
	});
	
	//-----
	
	$("#produccion_trefilado_digitador").jqxInput(
	{
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#produccion_trefilado_aprobador").jqxInput(
	{
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#produccion_trefilado_finalizador").jqxInput(
	{
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	function ChangeClass(Color)
	{
		var Element1 = document.getElementById("produccion_trefilado_diferencia");
		var Element2 = Element1.getElementsByTagName("input")[0];
		
		var Element3 = Element1.className.split(/\s+/);
		var Element4 = Element2.className.split(/\s+/);

		/*for (i = 0; i < Element3.length; i ++)
		{
			switch (Element3[i])
			{
				case "GreenState2":
					Element1[i].className -= "GreenState2";
				break;
				case "RedState2":
					Element1[i].className -= "RedState2";
				break;
				case "OrangeState2":
					Element1[i].className -= "OrangeState2";
				break;
			}
		}
		
		for (i = 0; i < Element4.length; i ++)
		{
			switch (Element4[i])
			{
				case "GreenState2":
					Element2[i].className -= "GreenState2";
				break;
				case "RedState2":
					Element2[i].className -= "RedState2";
				break;
				case "OrangeState2":
					Element2[i].className -= "OrangeState2";
				break;
			}
		}*/
		
		Element1.className += " "+Color+"";
		Element2.className += " "+Color+"";
	};
	
	function Procesar(Type)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Estado = $("#produccion_trefilado_estado").jqxComboBox('getSelectedItem');
		if (!Estado)
		{
			Alerts_Box("Ocurrió un error al intentar buscar el estado del movimiento.<br />Recarge la pagina e intente nuevamente.", 3);
			Locked = false;
			return;
		}
		
		if (Type == "FINALIZAR" && ($("#produccion_trefilado_diferencia").val() < 0 || $("#produccion_trefilado_diferencia").val() > 0))
		{
			Alerts_Box("La Diferencia del Proceso, debe ser \"0\".", 3);
			Locked = false;
			return;
		}
		
		if (Type == "INICIAR" && $("#produccion_trefilado_diferencia").val() < 0)
		{
			Alerts_Box("La Diferencia del Proceso no puede ser superior a \"0\".", 3);
			Locked = false;
			return;
		}
		
		var datinfo1 = $("#produccion_trefilado_productos_grid2").jqxGrid('getdatainformation');
		var count1 = datinfo1.rowscount;
		var datinfo2 = $("#produccion_trefilado_productos_grid3").jqxGrid('getdatainformation');
		var count2 = datinfo2.rowscount;
		var datinfo3 = $("#produccion_trefilado_productos_grid4").jqxGrid('getdatainformation');
		var count3 = datinfo3.rowscount;
		var myarray = new Array();
		var tmp_array = {};
		var gridarray1 = new Array();
		var gridarray2 = new Array();
		var gridarray3 = new Array();
		
		if (count1 > 0)
		{
			for (i = 0; i < count1; i++)
			{
				var array = {};
				var currentRow = $("#produccion_trefilado_productos_grid2").jqxGrid('getrowdata', i);
				
				array["CodFab"] = currentRow.CodFab;
				array["Cantidad"] = currentRow.Cantidad;
				//---
				gridarray1[i] = array;
			}
		}
		else
		{
			Alerts_Box("Debe Agregar Productos a Obtener", 3);
			Locked = false;
			return;
		}
		
		if (count2 > 0)
		{
			for (i = 0; i < count2; i++)
			{
				var array = {};
				var currentRow = $("#produccion_trefilado_productos_grid3").jqxGrid('getrowdata', i);
				
				if (currentRow.Maquinaria == "" || currentRow.Maquinaria == undefined)
				{
					Alerts_Box("Debe Ingresar un tipo de Maquinaria a usar.", 3);
					WaitClick();
					Locked = false;
					return;
				}
				
				if (currentRow.Operario == "" || currentRow.Operario == undefined)
				{
					Alerts_Box("Debe Ingresar un Operario de Maquinaria.", 3);
					WaitClick();
					Locked = false;
					return;
				}
				var Operario = $("#produccion_trefilado_productos_grid3").jqxGrid('getcellvalue', i, 'OperarioID');
				
				array["CodFab"] = currentRow.CodFab;
				array["Cantidad"] = currentRow.Cantidad;
				array["Maquinaria"] = currentRow.Maquinaria;
				array["Operario"] = Operario;
				//---
				gridarray2[i] = array;
			}
		}
		else
		{
			Alerts_Box("Debe Agregar Maquinaria y Operario", 3);
			Locked = false;
			return;
		}
		
		if (count3 > 0)
		{
			for (i = 0; i < count3; i++)
			{
				var array = {};
				var currentRow = $("#produccion_trefilado_productos_grid4").jqxGrid('getrowdata', i);
				
				array["CodFab"] = currentRow.CodFab;
				array["Cantidad"] = currentRow.Cantidad;
				//---
				gridarray3[i] = array;
			}
		}
		
		tmp_array["Type"] = Type;
		tmp_array["Ord_Produccion"] = Ord_Produccion;
		tmp_array["Estado"] = Estado.value;
		tmp_array["Ord_Compra"] = $("#produccion_trefilado_ord_compra").val();
		tmp_array["ClienteID"] = $("#produccion_trefilado_cliente_ID").val();
		tmp_array["Fecha"] = GetFormattedDate($("#produccion_trefilado_fecha").jqxDateTimeInput('getDate'));
		tmp_array["Rendimiento"] = $("#produccion_trefilado_rendimiento").val();
		tmp_array["Avance"] = $("#produccion_trefilado_avance").val();
		tmp_array["Observaciones"] = $("#produccion_trefilado_observaciones").val();
		myarray[0] = tmp_array;
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: {
				"Produccion_Trefilado":true,
				"MainData":myarray,
				"Data1":gridarray1,
				"Data2":gridarray2,
				"Data3":gridarray3
			},
			async: true,
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						switch(Type)
						{
							case "GUARDAR":
								Alerts_Box("Orden Guardada con Exito!", 2);
							break;
							case "INICIAR":
								EnableDisableAll(true);
								Alerts_Box("Orden Iniciada con Exito!", 2);
							break;
							case "FINALIZAR":
								EnableDisableAll(true);
								Alerts_Box("Orden Finalizada con Exito", 2);
							break;
						}
						//---
						OrdenSource.data = {"Produccion_Ordenes_Trefilado": $("#produccion_trefilado_estado").val()};
						var OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
						$("#produccion_trefilado_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
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
	};
	
	$("#produccion_trefilado_iniciar").jqxButton({ width: 304, height: 30, template: "success"});
	$("#produccion_trefilado_iniciar").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		var Tipo = document.getElementById("produccion_trefilado_iniciar").value;
		Procesar(Tipo);
	});
	
	$("#produccion_trefilado_finalizar").jqxButton({width: 304, height: 30, template: "info"});
	$("#produccion_trefilado_finalizar").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		if (!Admin)
		{
			var estado = $("#produccion_trefilado_estado").jqxComboBox('getSelectedItem');
			if (!estado)
				return;
			else
			{
				if (estado.value == "Pendiente" || estado.value == "Finalizado")
					return;
				else
					Procesar("FINALIZAR");
			}
		}
		else
			Procesar("FINALIZAR");
	});
	
	$("#produccion_trefilado_imprimir").jqxButton({width: 304, height: 30, template: "warning"});
	$("#produccion_trefilado_imprimir").bind('click', function ()
	{
		/*var datinfo2 = $("#produccion_trefilado_productos_grid3").jqxGrid('getdatainformation');
		var count2 = datinfo2.rowscount;
		
		if (count2 > 0)
		{
			for (i = 0; i < count2; i++)
			{
				var currentRow = $("#produccion_trefilado_productos_grid3").jqxGrid('getrowdata', i);
				var val = $("#produccion_trefilado_productos_grid3").jqxGrid('getcellvalue', i, 'OperarioID');
				alert(val)
				alert(currentRow.Operario)
			}
		}*/
		
		if (!Admin && !Imprimir)
			return;
		
		var data = "";
		data += "imprimir/produccion_procesos.php?Proceso=Trefilado";
		data += "&Ord_Produccion="+$("#produccion_trefilado_ord_produccion").val();
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	
	if (!Admin && !Guardar)
	{
		$("#produccion_trefilado_iniciar").jqxButton({ disabled: true });
		$("#produccion_trefilado_finalizar").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Imprimir)
	{
		$("#produccion_trefilado_imprimir").jqxButton({ disabled: true });
	}
	
	$("#produccion_trefilado_estado").val("Pendiente");
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Estado
			</td>
			<td>
				<div id="produccion_trefilado_estado"></div>
			</td>
			<td>
				Ord. Produccion
			</td>
			<td>
				<div id="produccion_trefilado_ord_produccion"></div>
			</td>
			<td>
				Destino
			</td>
			<td>
				<input type="text" id="produccion_trefilado_destino"/>
			</td>
			<td>
				Ord. Compra
			</td>
			<td>
				<input type="text" id="produccion_trefilado_ord_compra"/>
			</td>
			<td>
				Operario
			</td>
			<td>
				<input type="text" id="produccion_trefilado_operario"/>
			</td>
		</tr>
		<tr>
			<td>
				Cliente
			</td>
			<td colspan="3">
				<input type="text" id="produccion_trefilado_cliente"/>
			</td>
			<td colspan="8">
				<li class="parte1_li_txt">
					ID Cliente&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_trefilado_cliente_ID"/>
				</li>
				<li class="parte1_li_txt">
					Fecha&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<div id="produccion_trefilado_fecha"></div>
				</li>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="produccion_trefilado_left" style="float: left; margin-left:0px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2" style="padding: 0px;">
					<div style="width: 100%; height: 20px; background-color: #F18B0C; color: white; text-align:left;">
						<p style="margin:0px; padding:3px 0px 0px 3px;">INSUMOS REQUERIDOS</p>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px 0px 30px 0px;">
					<div id="produccion_trefilado_productos_grid1"></div>
				</td>
			</tr>
			<tr>
				<td style="width: 70px; padding:0px 0px 10px 0px;">
					<div style="width: 83px; height: 60px; background-color: #6E93CE; color: white; text-align:center;">
						<br/>PRODUCTOS A OBTENER
					</div>
				</td>
				<td style="padding:0px 0px 10px 0px;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr style="background: #E0E9F5">
							<td style="border-bottom: 1px solid #A4BED4;">
								Codigo
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Producto
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Cantidad
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td>
								<div id="produccion_trefilado_codigo1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_trefilado_producto1" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_trefilado_cantidad1" style="margin-left:5px;"></div>
							</td>
							<td>
								<input type="button" id="produccion_trefilado_addrowbutton1" value="A&ntilde;adir" style="margin:0px 5px;"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px 0px 30px 0px;">
					<div id="produccion_trefilado_productos_grid2"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="width: 70px; padding: 0px;">
					<div style="width: 100%; height: 20px; background-color: #3E72A2; color: white; text-align:left;">
						<p style="margin:0px; padding:3px 0px 0px 3px;">MAQUINAS / OPERARIOS</p>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px 0px 30px 0px;">
					<div id="produccion_trefilado_productos_grid3"></div>
				</td>
			</tr>
			<tr>
				<td style="width: 70px; padding:0px 0px 10px 0px;">
					<div style="width: 83px; height: 60px; background-color: #5DBA5D; color: white; text-align:center;">
						<p style="margin:0px; padding-top:23px;">DESPUNTES</p>
					</div>
				</td>
				<td style="padding:0px 0px 10px 0px;">
					<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
						<tr style="background: #E0E9F5">
							<td style="border-bottom: 1px solid #A4BED4;">
								Codigo
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Producto
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Cantidad
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								&nbsp;
							</td>
						</tr>
						<tr>
							<td>
								<div id="produccion_trefilado_codigo2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_trefilado_producto2" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_trefilado_cantidad2" style="margin-left:5px;"></div>
							</td>
							<td>
								<input type="button" id="produccion_trefilado_addrowbutton2" value="A&ntilde;adir" style="margin:0px 5px;"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding:0px;">
					<div id="produccion_trefilado_productos_grid4"></div>
				</td>
			</tr>
		</table>
	</div>
	<div id="produccion_trefilado_right" style="float: left; margin-left:20px;">
		<table style="margin: 0px 0px 10px 0px;" cellpadding="0" cellspacing="0">
			<tr style="background-color: #3E72A2; color: white;">
				<td colspan="3" style="padding:5px;">
					Estado de la Orden de Produccion por Procesos
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					Estado
				</td>
				<td>
					Avance
				</td>
			</tr>
			<tr>
				<td>
					Trefilado
				</td>
				<td>
					<input type="text" id="produccion_trefilado_estado_trefilado"/>
				</td>
				<td>
					<div id="produccion_trefilado_avance_trefilado"></div>
				</td>
			</tr>
			<tr>
				<td>
					Corte y Enderezado
				</td>
				<td>
					<input type="text" id="produccion_trefilado_estado_enderezado"/>
				</td>
				<td>
					<div id="produccion_trefilado_avance_enderezado"></div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:20px;">
					ElectroSoldado
				</td>
				<td style="padding-bottom:20px;">
					<input type="text" id="produccion_trefilado_estado_soldado"/>
				</td>
				<td style="padding-bottom:20px;">
					<div id="produccion_trefilado_avance_soldado"></div>
				</td>
			</tr>
			<tr style="background-color: #A08F41; color: white;">
				<td colspan="3" style="padding:5px;">
					DETALLES
				</td>
			</tr>
			<tr>
				<td colspan="3" style="list-style: none;">
					<li style="float: left;" class="parte1_li_txt">
						Peso Total de Material Requerido&nbsp;
					</li>
					<li style="padding: 0px 0px 0px 10px; float: left;">
						<div id="produccion_trefilado_requerido"></div>
					</li>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="list-style: none;">
					<li style="float: left;" class="parte1_li_txt">
						Peso Total de Productos a Obtener&nbsp;
					</li>
					<li style="float: left;">
						<div id="produccion_trefilado_obtener"></div>
					</li>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="list-style: none;">
					<li style="float: left;" class="parte1_li_txt">
						Peso Total de Despuntes
					</li>
					<li style="padding: 0px 0px 0px 62px; float: left;">
						<div id="produccion_trefilado_despunte"></div>
					</li>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="list-style: none;">
					<li style="float: left;" class="parte1_li_txt">
						Rendimiento Obtenido
					</li>
					<li style="padding: 0px 0px 0px 76px; float: left;">
						<div id="produccion_trefilado_rendimiento"></div>
					</li>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="list-style: none;">
					<li style="float: left;" class="parte1_li_txt">
						Diferencias despues del Proceso
					</li>
					<li style="padding: 0px 0px 0px 18px; float: left;">
						<div id="produccion_trefilado_diferencia"></div>
					</li>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="list-style: none; padding-bottom:20px;">
					<li style="float: left;" class="parte1_li_txt">
						Avance del Proceso
					</li>
					<li style="padding: 0px 0px 0px 130px; float: left;">
						<div id="produccion_trefilado_avance"></div>
					</li>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="padding-bottom:20px; list-style: none;">
					<li style="float: left;" class="parte1_li_txt">
						Dig.&nbsp;
					</li>
					<li style="padding: 0px 3px; float: left;">
						<input type="text" id="produccion_trefilado_digitador"/>
					</li>
					<li style="float: left;" class="parte1_li_txt">
						&nbsp;Apro.&nbsp;
					</li>
					<li style="padding: 0px 3px; float: left;">
						<input type="text" id="produccion_trefilado_aprobador"/>
					</li>
					<li style="float: left;" class="parte1_li_txt">
						&nbsp;Fin.&nbsp;
					</li>
					<li style="padding: 0px 3px; float: left;">
						<input type="text" id="produccion_trefilado_finalizador"/>
					</li>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					Observaciones
				</td>
			</tr>
			<tr>
				<td colspan="3" style="padding-bottom:20px;">
					<textarea rows="5" cols="40" id="produccion_trefilado_observaciones" maxlength="100" style="resize:none;"></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="button" id="produccion_trefilado_iniciar" value="INICIAR"/>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="button" id="produccion_trefilado_finalizar" value="FINALIZAR"/>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="button" id="produccion_trefilado_imprimir" value="IMPRIMIR"/>
				</td>
			</tr>
		</table>
	</div>
</div>