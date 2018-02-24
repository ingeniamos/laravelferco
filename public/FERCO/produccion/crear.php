<?php
session_start();
require_once('../modulos/config.php');
$Ord_Produccion = isset($_POST["Ord_Produccion"]) ? $_POST["Ord_Produccion"]:"";

	if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
	{
		$_SESSION["NumOfID"]++;
	}
$NumOfID = $_SESSION["NumOfID"];
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var ID_Cliente = "";
	var CodFabID = "";
	var ID_Incorrecto = false;
	var RowAdded = true;
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Produccion_Content");
	var Body = document.getElementById("Produccion_Crear_Content");
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
				ClearDocument();
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
				if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Imprimir"] == "true")
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
			{id:"produccion_crear_ord_produccion<?php echo $NumOfID ?>", type:""},
			{id:"produccion_crear_solicitud<?php echo $NumOfID ?>", type:""},
			{id:"produccion_crear_fecha<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"produccion_crear_destino<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_cliente<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
			//--left - top
			{id:"produccion_crear_codigo1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_producto1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_cantidad1<?php echo $NumOfID ?>", type:""},
			{id:"produccion_crear_origen1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_destino1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_productos_grid1<?php echo $NumOfID ?>", type:"jqxGrid"},
			//-- left - bot
			{id:"produccion_crear_codigo2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_producto2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_cantidad2<?php echo $NumOfID ?>", type:""},
			{id:"produccion_crear_origen2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_destino2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_productos_grid2<?php echo $NumOfID ?>", type:"jqxGrid"},
			//-- right
			{id:"produccion_crear_trefilado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_enderezado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_soldado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"produccion_crear_figurado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
		];
		
		if ("<?php echo $Ord_Produccion ?>" == "")
		{
			EnableDisableJSON = [
				//{id:"produccion_crear_ord_produccion<?php echo $NumOfID ?>", type:""},
				{id:"produccion_crear_solicitud<?php echo $NumOfID ?>", type:""},
				{id:"produccion_crear_fecha<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
				{id:"produccion_crear_destino<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_cliente<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				//--left - top
				{id:"produccion_crear_codigo1<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_producto1<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_cantidad1<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"produccion_crear_origen1<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_destino1<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_addrowbutton1<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"produccion_crear_deleterowbutton1<?php echo $NumOfID ?>", type:"jqxButton"},
				//-- left - bot
				{id:"produccion_crear_codigo2<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_producto2<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_cantidad2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"produccion_crear_origen2<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_destino2<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_addrowbutton2<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"produccion_crear_deleterowbutton2<?php echo $NumOfID ?>", type:"jqxButton"},
				//-- right
				{id:"produccion_crear_trefilado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_enderezado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_soldado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_figurado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				// end
				{id:"produccion_crear_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
			];
		}
		else
		{
			EnableDisableJSON = [
				//{id:"produccion_crear_ord_produccion<?php echo $NumOfID ?>", type:""},
				{id:"produccion_crear_solicitud<?php echo $NumOfID ?>", type:""},
				{id:"produccion_crear_fecha<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
				{id:"produccion_crear_destino<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_cliente<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
				//--left - top
				{id:"produccion_crear_addrowbutton1<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"produccion_crear_deleterowbutton1<?php echo $NumOfID ?>", type:"jqxButton"},
				//-- left - bot
				{id:"produccion_crear_addrowbutton2<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"produccion_crear_deleterowbutton2<?php echo $NumOfID ?>", type:"jqxButton"},
				//-- right
				{id:"produccion_crear_trefilado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_enderezado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_soldado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"produccion_crear_figurado_operario<?php echo $NumOfID ?>", type:"jqxComboBox"},
				// end
				{id:"produccion_crear_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"produccion_crear_nuevo<?php echo $NumOfID ?>", type:"jqxButton"},
			];
		}
	}
	ReDefine();
	function ClearDocument()
	{
		// Variables
		ID_Cliente = "";
		CodFabID = "";
		RowAdded = true;
		Timer1 = 0;
		Timer2 = 0;
		Locked = false;
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		if (!Admin && !Guardar)
		{
			$("#produccion_crear_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			$("#produccion_crear_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#produccion_crear_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		}
	}
	
	function LoadValues ()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Destino', type: 'string'},
				{ name: 'Cliente', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Nombre', type: 'string'},
				{ name: 'DestinoOrden', type: 'string'},
				{ name: 'Fecha', type: 'string'},
				{ name: 'Trefilado', type: 'string' },
				{ name: 'Enderezado', type: 'string' },
				{ name: 'Soldado', type: 'string'},
				{ name: 'Figurado', type: 'string' },
				{ name: 'Interno', type: 'string' },
				{ name: 'Solicitud', type: 'string' },
				{ name: 'DigitadoPor', type: 'string' },
				{ name: 'AprobadoPor', type: 'string' },
				{ name: 'ModificadoPor', type: 'string' },
				{ name: 'Estado', type: 'string' },
				//--
				{ name: 'Tipo', type: 'string'},
				{ name: 'CodFab', type: 'string'},
				{ name: 'Nombre', type: 'string' },
				{ name: 'Cantidad', type: 'decimal' },
				{ name: 'Peso', type: 'decimal' },
				{ name: 'UndMed', type: 'string'},
				{ name: 'Origen', type: 'string' },
				{ name: 'Destino', type: 'string'},
			],
			type: 'GET',
			data:{"Produccion_Modificar":"<?php echo $Ord_Produccion ?>"},
			url: "modulos/datos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var len = records.length;
				for (var i=0;i<len;i++) {
					var datarow = [{
						"CodFab":records[i]["CodFab"],
						"Nombre":records[i]["Nombre"],
						"Peso":parseFloat(records[i]["Peso"]),
						"UndMed":records[i]["UndMed"],
						"Cantidad":parseFloat(records[i]["Cantidad"]),
						"PesoTotal":0,
						"Origen":records[i]["Origen"],
						"Destino":records[i]["Destino"],
					}];
					if (records[i]["Tipo"] == "Requerido")
						$("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
					else
						$("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				}
				$("#produccion_crear_ord_produccion<?php echo $NumOfID ?>").val("<?php echo $Ord_Produccion ?>");
				$("#produccion_crear_fecha<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#produccion_crear_destino<?php echo $NumOfID ?>").val(records[0]["DestinoOrden"]);
				$("#produccion_crear_cliente<?php echo $NumOfID ?>").val(records[0]["Cliente"]);
				$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").val(records[0]["ClienteID"]);
				//
				$("#produccion_crear_estado<?php echo $NumOfID ?>").val(records[0]["Estado"]);
				$("#produccion_crear_interno<?php echo $NumOfID ?>").val(records[0]["Interno"]);
				$("#produccion_crear_solicitud<?php echo $NumOfID ?>").val(records[0]["Solicitud"]);
				$("#produccion_crear_digitado_por<?php echo $NumOfID ?>").val(records[0]["DigitadoPor"]);
				$("#produccion_crear_aprobado_por<?php echo $NumOfID ?>").val(records[0]["AprobadoPor"]);
				$("#produccion_crear_modificado_por<?php echo $NumOfID ?>").val(records[0]["ModificadoPor"]);
				//
				$("#produccion_crear_trefilado_operario<?php echo $NumOfID ?>").val(records[0]["Trefilado"]);
				$("#produccion_crear_enderezado_operario<?php echo $NumOfID ?>").val(records[0]["Enderezado"]);
				$("#produccion_crear_soldado_operario<?php echo $NumOfID ?>").val(records[0]["Soldado"]);
				$("#produccion_crear_figurado_operario<?php echo $NumOfID ?>").val(records[0]["Figurado"]);
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
			downloadComplete: function(edata, textStatus, jqXHR) {
				//alert("Data is Loaded");
			}
		});
	};
	
	$("#produccion_crear_ord_produccion<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
		disabled: true
	});
	
	$("#produccion_crear_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#produccion_crear_fecha<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	
	$("#produccion_crear_destino<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 90,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
	});
	$("#produccion_crear_destino<?php echo $NumOfID ?>").jqxComboBox('addItem', {label: "Cliente", value: "Cliente"});
	$("#produccion_crear_destino<?php echo $NumOfID ?>").jqxComboBox('addItem', {label: "Inventario", value: "Inventario"});
	
	$("#produccion_crear_destino<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args) {
			if(event.args.item.value == "Inventario")
			{
				$("#produccion_crear_cliente<?php echo $NumOfID ?>").val("<?php echo $MAIN_ID ?>");
				$("#produccion_crear_cliente<?php echo $NumOfID ?>").jqxComboBox({ disabled: true});
				$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").val("<?php echo $MAIN_ID ?>");
				$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox({ disabled: true});
			}
			else
			{
				if ("<?php echo $Ord_Produccion ?>" == "")
				{
					$("#produccion_crear_cliente<?php echo $NumOfID ?>").jqxComboBox({ disabled: false});
					$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox({ disabled: false});
				}
			}
		}
		else
		{
			$("#produccion_crear_destino<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
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
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
	
	$("#produccion_crear_cliente<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 200,
		height: 20,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#produccion_crear_cliente<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
		}
		else
		{
			ID_Cliente = "";
			$("#produccion_crear_cliente<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			//Load
		}
	});
	$("#produccion_crear_cliente<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				if ($("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").val() != ID_Cliente)
					$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").val(ID_Cliente);
					
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#produccion_crear_cliente<?php echo $NumOfID ?>").bind('close', function () {
		if (ID_Cliente != "") {
			$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").val(ID_Cliente);
			//Load
		}
	});
	
	$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
		}
		else
		{
			ID_Cliente = "";
			$("#produccion_crear_cliente<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			//Load
		}
	});
	$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				if ($("#produccion_crear_cliente<?php echo $NumOfID ?>").val() != ID_Cliente)
					$("#produccion_crear_cliente<?php echo $NumOfID ?>").val(ID_Cliente);
					
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").bind('close', function () {
		if (ID_Cliente != "") {
			$("#produccion_crear_cliente<?php echo $NumOfID ?>").val(ID_Cliente);
			//Load
		}
	});
	
	$("#produccion_crear_solicitud<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		width: 100,
		height: 20,
	});
	$("#produccion_crear_solicitud<?php echo $NumOfID ?>").on("change", function (event)
	{
		if ("<?php echo $Ord_Produccion ?>" == "")
		{
			var value = $("#produccion_crear_solicitud<?php echo $NumOfID ?>").val();
			if (value == '')
				return;
			
			var FindSource =
			{
				datatype: "json",
				datafields: [
					{ name: 'Same', type: 'bool'},
				],
				type: 'GET',
				data: {
					"Produccion_Check_SameID":value,
				},
				url: "modulos/datos.php",
				async: true,
			};
			var FindDataAdapter = new $.jqx.dataAdapter(FindSource,{
				autoBind: true,
				loadComplete: function ()
				{
					var records = FindDataAdapter.records;
					if (records[0]["Same"] == true)
					{
						$("#produccion_crear_solicitud<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
						ID_Incorrecto = true;
						Alerts_Box("El numero de la Solicitud de Material, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
						WaitClick_Input("produccion_crear_solicitud");
					}
					else
					{
						$("#produccion_crear_solicitud<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
						ID_Incorrecto = false;
					}
				}
			});
		}
	});
	
	if ("<?php echo $Ord_Produccion ?>" != "")
	{
		$("#produccion_crear_estado<?php echo $NumOfID ?>").jqxInput({
			theme: mytheme,
			width: 75,
			height: 20,
			disabled: true
		});
		
		$("#produccion_crear_interno<?php echo $NumOfID ?>").jqxInput({
			theme: mytheme,
			width: 100,
			height: 20,
			disabled: true
		});
		
		$("#produccion_crear_digitado_por<?php echo $NumOfID ?>").jqxInput({
			theme: mytheme,
			width: 55,
			height: 20,
			disabled: true
		});
		
		$("#produccion_crear_aprobado_por<?php echo $NumOfID ?>").jqxInput({
			theme: mytheme,
			width: 55,
			height: 20,
			disabled: true
		});
		
		$("#produccion_crear_modificado_por<?php echo $NumOfID ?>").jqxInput({
			theme: mytheme,
			width: 55,
			height: 20,
			disabled: true
		});
	}
	//----------------------------------------------------- PARTE 2
	
	//--------- LEFT
	if ("<?php echo $Ord_Produccion ?>" == "")
	{
		var ProductosSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'CodFab', type: 'string'},
				{ name: 'Nombre', type: 'string'}
			],
			type: 'GET',
			data: {"Produccion_Hierro":true},
			url: "modulos/datos_productos.php",
			async: true
		};
		var ProductosDataAdapter = new $.jqx.dataAdapter(ProductosSource);
		
		$("#produccion_crear_codigo1<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 110,
			height: 20,
			source: ProductosDataAdapter,
			searchMode: 'startswithignorecase',
			autoComplete: true,
			promptText: 'Buscar Codigo',
			selectedIndex: -1,
			displayMember: 'CodFab',
			valueMember: 'CodFab'
		});
		$("#produccion_crear_codigo1<?php echo $NumOfID ?>").bind('change', function (event) {
			if (event.args) {
				CodFabID = event.args.item.value;
			}
			else
			{
				CodFabID = "";
				$("#produccion_crear_codigo1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_producto1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				//Load
			}
		});
		$("#produccion_crear_codigo1<?php echo $NumOfID ?>").bind('select', function (event) {
			if (event.args) {
				CodFabID = event.args.item.value;
				clearTimeout(Timer1);
				Timer1 = setTimeout(function(){
					if ($("#produccion_crear_producto1<?php echo $NumOfID ?>").val() != CodFabID)
						$("#produccion_crear_producto1<?php echo $NumOfID ?>").val(CodFabID);
						
					clearTimeout(Timer1);
				},350);
			}
		});
		
		$("#produccion_crear_codigo1<?php echo $NumOfID ?>").bind('close', function () {
			if (CodFabID != "") {
				$("#produccion_crear_producto1<?php echo $NumOfID ?>").val(CodFabID);
				//Load
			}
		});
		
		$("#produccion_crear_producto1<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 250,
			height: 20,
			source: ProductosDataAdapter,
			searchMode: 'containsignorecase',
			autoComplete: true,
			promptText: 'Buscar Producto',
			selectedIndex: -1,
			displayMember: 'Nombre',
			valueMember: 'CodFab'
		});
		$("#produccion_crear_producto1<?php echo $NumOfID ?>").bind('change', function (event) {
			if (event.args) {
				CodFabID = event.args.item.value;
			}
			else
			{
				CodFabID = "";
				$("#produccion_crear_codigo1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_producto1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				//Load
			}
		});
		$("#produccion_crear_producto1<?php echo $NumOfID ?>").bind('select', function (event) {
			if (event.args) {
				CodFabID = event.args.item.value;
				clearTimeout(Timer1);
				Timer1 = setTimeout(function(){
					if ($("#produccion_crear_codigo1<?php echo $NumOfID ?>").val() != CodFabID)
						$("#produccion_crear_codigo1<?php echo $NumOfID ?>").val(CodFabID);
						
					clearTimeout(Timer1);
				},350);
			}
		});
		
		$("#produccion_crear_producto1<?php echo $NumOfID ?>").bind('close', function () {
			if (CodFabID != "") {
				$("#produccion_crear_codigo1<?php echo $NumOfID ?>").val(CodFabID);
				//Load
			}
		});
		
		$("#produccion_crear_cantidad1<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 70,
			inputMode: 'simple',
			spinButtons: false,
			digits:6,
		});
		
		var OrigenValues = [
			{"Nombre":"Inventario", "Valor":"Inventario"},
			{"Nombre":"Trefilado", "Valor":"Trefilado"},
			{"Nombre":"Corte y Enderezado", "Valor":"Corte y Enderezado"},
			{"Nombre":"Electrosoldado", "Valor":"Electrosoldado"},
			{"Nombre":"Figurado", "Valor":"Figurado"},
		];
		
		var OrigenSource = {
			localdata: OrigenValues,
			datatype: "json",
			datafields:[
				{ name: 'Nombre', type: 'string' },
				{ name: 'Valor', type: 'string' }
			]
		};
		var OrigenDataAdapter = new $.jqx.dataAdapter(OrigenSource);
		
		$("#produccion_crear_origen1<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 140,
			height: 20,
			source: OrigenDataAdapter,
			searchMode: 'containsignorecase',
			autoComplete: true,
			promptText: 'Seleccionar',
			selectedIndex: -1,
			displayMember: 'Nombre',
			valueMember: 'Valor'
		});
		
		var DestinoValues = [
			{"Nombre":"Trefilado", "Valor":"Trefilado"},
			{"Nombre":"Corte y Enderezado", "Valor":"Corte y Enderezado"},
			{"Nombre":"Electrosoldado", "Valor":"Electrosoldado"},
			{"Nombre":"Figurado", "Valor":"Figurado"},
		];
		
		var DestinoSource = {
			localdata: DestinoValues,
			datatype: "json",
			datafields:[
				{ name: 'Nombre', type: 'string' },
				{ name: 'Valor', type: 'string' }
			]
		};
		var DestinoDataAdapter = new $.jqx.dataAdapter(DestinoSource);
		
		$("#produccion_crear_destino1<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 140,
			height: 20,
			source: DestinoDataAdapter,
			searchMode: 'containsignorecase',
			autoComplete: true,
			promptText: 'Seleccionar',
			selectedIndex: -1,
			displayMember: 'Nombre',
			valueMember: 'Valor'
		});
	}
	
	var Source1 =
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
			{ name: 'Origen', type: 'string' },
			{ name: 'Destino', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var DataAdapter1 = new $.jqx.dataAdapter(Source1);
	
	function Add_Row1()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;

		var ProductName = $("#produccion_crear_producto1<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var CantidadNum = $("#produccion_crear_cantidad1<?php echo $NumOfID ?>").val();
		var Origen = $("#produccion_crear_origen1<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Destino = $("#produccion_crear_destino1<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		
		if (! ProductName | ProductName <= 0) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("produccion_crear_producto1<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("produccion_crear_cantidad1<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (! Origen | Origen <= 0) {
			Alerts_Box("Favor Seleccionar un Origen!", 3);
			WaitClick_Combobox("produccion_crear_origen1<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (! Destino | Destino <= 0) {
			Alerts_Box("Favor Seleccionar un Proceso!", 3);
			WaitClick_Combobox("produccion_crear_destino1<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
			if (currentRow.CodFab == CodFabID)
			{
				var totalc = CantidadNum + currentRow.Cantidad;
				
				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"Peso":currentRow.Peso,
					"UndMed":currentRow.UndMed,
					"Cantidad":totalc,
					"PesoTotal":currentRow.PesoTotal,
					"Origen":Origen.label,
					"Destino":Destino.label,
				}];
				var id = $("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('getrowid', i);
				$("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
				$("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_crear_codigo1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_producto1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_cantidad1<?php echo $NumOfID ?>").val('');
				$("#produccion_crear_origen1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_destino1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				RowAdded = true;
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
			data:{"Productos":CodFabID},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":CodFabID,
					"Nombre":ProductName.label,
					"Peso":records[0]["Peso"],
					"UndMed":records[0]["UndMed"],
					"Cantidad":CantidadNum,
					"PesoTotal":0,
					"Origen":Origen.label,
					"Destino":Destino.label,
				}];
				$("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_crear_codigo1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_producto1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_cantidad1<?php echo $NumOfID ?>").val('');
				$("#produccion_crear_origen1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_destino1<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
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
	
	$("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid(
	{
		theme: mytheme,
		width: 750,
		source: DataAdapter1,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'dblclick',
		showtoolbar: true,
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="produccion_crear_addrowbutton1<?php echo $NumOfID ?>" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="produccion_crear_deleterowbutton1<?php echo $NumOfID ?>" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#produccion_crear_addrowbutton1<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "success"});
			$("#produccion_crear_deleterowbutton1<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#produccion_crear_addrowbutton1<?php echo $NumOfID ?>").on('click', function () {
				Add_Row1();
			});
			// delete row.
			$("#produccion_crear_deleterowbutton1<?php echo $NumOfID ?>").on('click', function () {
				var selectedrowindex = $("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('getselectedrowindex');
				var rowscount = $("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('getrowid', selectedrowindex);
					$("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: '10%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '20%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Und', datafield: 'UndMed', editable: false, width: '7%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
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
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
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
			{
				text: 'Origen',
				datafield: 'Origen',
				width: '14%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: OrigenDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Valor'
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Proceso',
				datafield: 'Destino',
				width: '14%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: DestinoDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Valor'
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
		]
	});
	$("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('localizestrings', localizationobj);
	
	if ("<?php echo $Ord_Produccion ?>" == "")
	{
		$("#produccion_crear_codigo2<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 110,
			height: 20,
			source: ProductosDataAdapter,
			searchMode: 'startswithignorecase',
			autoComplete: true,
			promptText: 'Buscar Codigo',
			selectedIndex: -1,
			displayMember: 'CodFab',
			valueMember: 'CodFab'
		});
		$("#produccion_crear_codigo2<?php echo $NumOfID ?>").bind('change', function (event) {
			if (event.args) {
				CodFabID = event.args.item.value;
			}
			else
			{
				CodFabID = "";
				$("#produccion_crear_codigo2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_producto2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				//Load
			}
		});
		$("#produccion_crear_codigo2<?php echo $NumOfID ?>").bind('select', function (event) {
			if (event.args) {
				CodFabID = event.args.item.value;
				clearTimeout(Timer1);
				Timer1 = setTimeout(function(){
					if ($("#produccion_crear_producto2<?php echo $NumOfID ?>").val() != CodFabID)
						$("#produccion_crear_producto2<?php echo $NumOfID ?>").val(CodFabID);
						
					clearTimeout(Timer1);
				},350);
			}
		});
		
		$("#produccion_crear_codigo2<?php echo $NumOfID ?>").bind('close', function () {
			if (CodFabID != "") {
				$("#produccion_crear_producto2<?php echo $NumOfID ?>").val(CodFabID);
				//Load
			}
		});
		
		$("#produccion_crear_producto2<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 250,
			height: 20,
			source: ProductosDataAdapter,
			searchMode: 'containsignorecase',
			autoComplete: true,
			promptText: 'Buscar Producto',
			selectedIndex: -1,
			displayMember: 'Nombre',
			valueMember: 'CodFab'
		});
		$("#produccion_crear_producto2<?php echo $NumOfID ?>").bind('change', function (event) {
			if (event.args) {
				CodFabID = event.args.item.value;
			}
			else
			{
				CodFabID = "";
				$("#produccion_crear_codigo2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_producto2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				//Load
			}
		});
		$("#produccion_crear_producto2<?php echo $NumOfID ?>").bind('select', function (event) {
			if (event.args) {
				CodFabID = event.args.item.value;
				clearTimeout(Timer1);
				Timer1 = setTimeout(function(){
					if ($("#produccion_crear_codigo2<?php echo $NumOfID ?>").val() != CodFabID)
						$("#produccion_crear_codigo2<?php echo $NumOfID ?>").val(CodFabID);
						
					clearTimeout(Timer1);
				},350);
			}
		});
		
		$("#produccion_crear_producto2<?php echo $NumOfID ?>").bind('close', function () {
			if (CodFabID != "") {
				$("#produccion_crear_codigo2<?php echo $NumOfID ?>").val(CodFabID);
				//Load
			}
		});
		
		$("#produccion_crear_cantidad2<?php echo $NumOfID ?>").jqxNumberInput({
			theme: mytheme,
			height: 20,
			width: 70,
			inputMode: 'simple',
			spinButtons: false,
			digits:6,
		});
		
		$("#produccion_crear_origen2<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 140,
			height: 20,
			source: DestinoDataAdapter,
			searchMode: 'containsignorecase',
			autoComplete: true,
			promptText: 'Seleccionar',
			selectedIndex: -1,
			displayMember: 'Nombre',
			valueMember: 'Valor'
		});
		
		$("#produccion_crear_destino2<?php echo $NumOfID ?>").jqxComboBox(
		{
			theme: mytheme,
			width: 140,
			height: 20,
			source: OrigenDataAdapter,
			searchMode: 'containsignorecase',
			autoComplete: true,
			promptText: 'Seleccionar',
			selectedIndex: -1,
			displayMember: 'Nombre',
			valueMember: 'Valor'
		});
	}
	var Source2 =
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
			{ name: 'Origen', type: 'string' },
			{ name: 'Destino', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var DataAdapter2 = new $.jqx.dataAdapter(Source2);
	
	function Add_Row2()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;

		var ProductName = $("#produccion_crear_producto2<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var CantidadNum = $("#produccion_crear_cantidad2<?php echo $NumOfID ?>").val();
		var Origen = $("#produccion_crear_origen2<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Destino = $("#produccion_crear_destino2<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		
		if (! ProductName | ProductName <= 0) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("produccion_crear_producto2<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("produccion_crear_cantidad2<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (! Origen | Origen <= 0) {
			Alerts_Box("Favor Seleccionar un Origen!", 3);
			WaitClick_Combobox("produccion_crear_origen2<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		if (! Destino | Destino <= 0) {
			Alerts_Box("Favor Seleccionar un Destino!", 3);
			WaitClick_Combobox("produccion_crear_destino2<?php echo $NumOfID ?>");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
			if (currentRow.CodFab == CodFabID)
			{
				var totalc = CantidadNum + currentRow.Cantidad;
				
				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"Peso":currentRow.Peso,
					"UndMed":currentRow.UndMed,
					"Cantidad":totalc,
					"PesoTotal":currentRow.PesoTotal,
					"Origen":Origen.label,
					"Destino":Destino.label,
				}];
				var id = $("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('getrowid', i);
				$("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
				$("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_crear_codigo2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_producto2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_cantidad2<?php echo $NumOfID ?>").val('');
				$("#produccion_crear_origen2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_destino2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				RowAdded = true;
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
			data:{"Productos":CodFabID},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":CodFabID,
					"Nombre":ProductName.label,
					"Peso":records[0]["Peso"],
					"UndMed":records[0]["UndMed"],
					"Cantidad":CantidadNum,
					"PesoTotal":0,
					"Origen":Origen.label,
					"Destino":Destino.label,
				}];
				$("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#produccion_crear_codigo2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_producto2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_cantidad2<?php echo $NumOfID ?>").val('');
				$("#produccion_crear_origen2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
				$("#produccion_crear_destino2<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
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
	
	$("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid(
	{
		theme: mytheme,
		width: 750,
		source: DataAdapter2,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
		editmode: 'dblclick',
		showtoolbar: true,
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="produccion_crear_addrowbutton2<?php echo $NumOfID ?>" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="produccion_crear_deleterowbutton2<?php echo $NumOfID ?>" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#produccion_crear_addrowbutton2<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "success"});
			$("#produccion_crear_deleterowbutton2<?php echo $NumOfID ?>").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#produccion_crear_addrowbutton2<?php echo $NumOfID ?>").on('click', function () {
				Add_Row2();
			});
			// delete row.
			$("#produccion_crear_deleterowbutton2<?php echo $NumOfID ?>").on('click', function () {
				var selectedrowindex = $("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('getselectedrowindex');
				var rowscount = $("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('getrowid', selectedrowindex);
					$("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: '10%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '20%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{ text: 'Und', datafield: 'UndMed', editable: false, width: '7%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '10%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
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
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
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
			{
				text: 'Proceso',
				datafield: 'Origen',
				width: '14%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: OrigenDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Valor'
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
				
			},
			{
				text: 'Enviar a',
				datafield: 'Destino',
				width: '14%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: DestinoDataAdapter,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Valor'
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
		]
	});
	$("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('localizestrings', localizationobj);
	// -------- RIGHT
	
	var NominaSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'},
		{ name: 'ClienteID', type: 'string'}
		],
		type: 'GET',
		data: {"Clientes_Nomina":true},
		url: "modulos/datos.php",
		async: true
	};
	var NominaDataAdapter = new $.jqx.dataAdapter(NominaSource);
	
	$("#produccion_crear_trefilado_operario<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: NominaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Operario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID',
	});
	$("#produccion_crear_trefilado_operario<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#produccion_crear_trefilado_operario<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#produccion_crear_enderezado_operario<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: NominaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Operario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID',
	});
	$("#produccion_crear_enderezado_operario<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#produccion_crear_enderezado_operario<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#produccion_crear_soldado_operario<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: NominaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Operario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID',
	});
	$("#produccion_crear_soldado_operario<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#produccion_crear_soldado_operario<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#produccion_crear_figurado_operario<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: NominaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Operario',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID',
	});
	$("#produccion_crear_figurado_operario<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#produccion_crear_figurado_operario<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	function CrearProduccion ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ID_Incorrecto) {
			Alerts_Box("El numero de la Solicitud de Material, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
			Locked = false;
			return;
		}
		
		if ($("#produccion_crear_solicitud<?php echo $NumOfID ?>").val() == "") {
			Alerts_Box("Debe Ingresar una Solicitud de Material.", 3);
			WaitClick_Input("produccion_crear_solicitud<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#produccion_crear_destino<?php echo $NumOfID ?>").val() < 0 | $("#produccion_crear_destino<?php echo $NumOfID ?>").val() == "") {
			Alerts_Box("Debe Seleccionar un Destino.", 3);
			WaitClick_Combobox("produccion_crear_destino<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#produccion_crear_destino<?php echo $NumOfID ?>").val() == "Cliente") {
			if ($("#produccion_crear_cliente<?php echo $NumOfID ?>").val() < 0 | $("#produccion_crear_cliente<?php echo $NumOfID ?>").val() == "") {
				Alerts_Box("Debe Seleccionar un Cliente.", 3);
				WaitClick_Combobox("produccion_crear_cliente<?php echo $NumOfID ?>");
				Locked = false;
				return;
			}
			
			if ($("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").val() < 0 | $("#produccion_crear_cliente_ID<?php echo $NumOfID ?>").val() == "") {
				Alerts_Box("Debe Seleccionar un Cliente.", 3);
				WaitClick_Combobox("produccion_crear_cliente_ID");
				Locked = false;
				return;
			}
		}
		
		var datinfo1 = $("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count1 = datinfo1.rowscount;
		var datinfo2 = $("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count2 = datinfo2.rowscount;
		var myarray = new Array();
		var tmp_array = {};
		var gridarray1 = new Array();
		var gridarray2 = new Array();
		
		if (count1 <= 0) {
			Alerts_Box("Debe Ingresar al menos un Producto Requerido!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if (count2 <= 0) {
			Alerts_Box("Debe Ingresar al menos un Producto a Obtener!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (i = 0; i < count1; i++)
		{
			var array = {};
			var currentRow = $("#produccion_crear_productos_grid1<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
			
			switch (currentRow.Destino)
			{
				case "Trefilado":
					if ($("#produccion_crear_trefilado_operario<?php echo $NumOfID ?>").val() < 0 | $("#produccion_crear_trefilado_operario<?php echo $NumOfID ?>").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Trefilado.", 3);
						WaitClick_Combobox("produccion_crear_trefilado_operario<?php echo $NumOfID ?>");
						Locked = false;
						return;
					}
				break;
				case "Corte y Enderezado":
					if ($("#produccion_crear_enderezado_operario<?php echo $NumOfID ?>").val() < 0 | $("#produccion_crear_enderezado_operario<?php echo $NumOfID ?>").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Corte y Enderezado.", 3);
						WaitClick_Combobox("produccion_crear_enderezado_operario<?php echo $NumOfID ?>");
						Locked = false;
						return;
					}
				break;
				case "Electrosoldado":
					if ($("#produccion_crear_soldado_operario<?php echo $NumOfID ?>").val() < 0 | $("#produccion_crear_soldado_operario<?php echo $NumOfID ?>").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Electrosoldado.", 3);
						WaitClick_Combobox("produccion_crear_soldado_operario<?php echo $NumOfID ?>");
						Locked = false;
						return;
					}
				break;
				case "Figurado":
					if ($("#produccion_crear_figurado_operario<?php echo $NumOfID ?>").val() < 0 | $("#produccion_crear_figurado_operario<?php echo $NumOfID ?>").val() == "") {
						Alerts_Box("Debe Seleccionar Operario para Figurado.", 3);
						WaitClick_Combobox("produccion_crear_figurado_operario<?php echo $NumOfID ?>");
						Locked = false;
						return;
					}
				break;
			}
			
			array["CodFab"] = currentRow.CodFab;
			array["Cantidad"] = currentRow.Cantidad;
			array["Origen"] = currentRow.Origen;
			array["Destino"] = currentRow.Destino;
			//---
			gridarray1[i] = array;
		}
		
		for (i = 0; i < count2; i++)
		{
			var array = {};
			var currentRow = $("#produccion_crear_productos_grid2<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
			
			array["CodFab"] = currentRow.CodFab;
			array["Cantidad"] = currentRow.Cantidad;
			array["Origen"] = currentRow.Origen;
			array["Destino"] = currentRow.Destino;
			//---
			gridarray2[i] = array;
		}
		
		tmp_array["Solicitud"] = $("#produccion_crear_solicitud<?php echo $NumOfID ?>").val();
		tmp_array["Destino"] = $("#produccion_crear_destino<?php echo $NumOfID ?>").val();
		tmp_array["ClienteID"] = ID_Cliente;
		tmp_array["Fecha"] = GetFormattedDate($("#produccion_crear_fecha<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
		tmp_array["Trefilado"] = $("#produccion_crear_trefilado_operario<?php echo $NumOfID ?>").val();
		tmp_array["Enderezado"] = $("#produccion_crear_enderezado_operario<?php echo $NumOfID ?>").val();
		tmp_array["Electrosoldado"] = $("#produccion_crear_soldado_operario<?php echo $NumOfID ?>").val();
		tmp_array["Figurado"] = $("#produccion_crear_figurado_operario<?php echo $NumOfID ?>").val();
		myarray[0] = tmp_array;
		
		//---
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			type: 'GET',
			url: "modulos/guardar.php",
			data: {
				"Produccion_Crear":true,
				"MainData":myarray,
				"Data1":gridarray1,
				"Data2":gridarray2
			},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				ClickOK = false;
				ClickCANCEL = false;
				Alerts_Box("Datos Guardados con Exito!\nCodigo Orden de Produccion Generado = \""+data+"\"", 2, true);
				$("#produccion_crear_ord_produccion<?php echo $NumOfID ?>").val(data);
				EnableDisableAll(true);
				Timer1 = setInterval(function(){
					if (ClickOK == true)
					{
						ClearDocument();
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
					ClickOK = false;
					ClickCANCEL = true;
				},5000);
				Locked = false;
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	};
	
	$("#produccion_crear_guardar<?php echo $NumOfID ?>").jqxButton({
		width: 180,
		height: 30,
		template: "info"
	});
	$("#produccion_crear_guardar<?php echo $NumOfID ?>").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		CrearProduccion();
	});
	
	$("#produccion_crear_nuevo<?php echo $NumOfID ?>").jqxButton({
		width: 180,
		height: 30,
		template: "success"
	});
	$("#produccion_crear_nuevo<?php echo $NumOfID ?>").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		ClearDocument();
	});
	
	$("#produccion_crear_imprimir<?php echo $NumOfID ?>").jqxButton({
		width: 180,
		height: 30,
		template: "warning"
	});
	$("#produccion_crear_imprimir<?php echo $NumOfID ?>").bind('click', function ()
	{
		if (!Admin && !Imprimir)
			return;
		window.open("imprimir/produccion_orden.php?Ord_Produccion="+$("#produccion_crear_ord_produccion<?php echo $NumOfID ?>").val()+"", "", "width=740, height=600, menubar=no, titlebar=no");
	});
	
	if ("<?php echo $Ord_Produccion ?>" != "")
	{
		EnableDisableAll(true);
		LoadValues();
	}
	else
	{
		if (!Admin && !Guardar)
		{
			$("#produccion_crear_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			$("#produccion_crear_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#produccion_crear_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		}
		
		CheckRefresh();
	}
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin:5px 0px 20px 0px;">
		<tr>
			<td>
				Ord. Produccion
			</td>
			<td>
				<input type="text" id="produccion_crear_ord_produccion<?php echo $NumOfID ?>"/>
			</td>
			<td>
				Fecha
			</td>
			<td>
				<div id="produccion_crear_fecha<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Destino
			</td>
			<td>
				<div id="produccion_crear_destino<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Cliente
			</td>
			<td>
				<div id="produccion_crear_cliente<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				ID Cliente
			</td>
			<td>
				<div id="produccion_crear_cliente_ID<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td colspan="10">
				<li class="parte1_li_txt">
					Solicitud de Material
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_crear_solicitud<?php echo $NumOfID ?>"/>
				</li>
				<?php
				if ($Ord_Produccion != "")
				{
				?>
				<li class="parte1_li_txt">
					Estado
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_crear_estado<?php echo $NumOfID ?>"/>
				</li>
				<li class="parte1_li_txt">
					Interno&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_crear_interno<?php echo $NumOfID ?>"/>
				</li>
				<li class="parte1_li_txt">
					Digitado por&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_crear_digitado_por<?php echo $NumOfID ?>"/>
				</li>
				<li class="parte1_li_txt">
					Aprobado por&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_crear_aprobado_por<?php echo $NumOfID ?>"/>
				</li>
				<li class="parte1_li_txt">
					Modificado por&nbsp;
				</li>
				<li style="padding: 0px 3px;">
					<input type="text" id="produccion_crear_modificado_por<?php echo $NumOfID ?>"/>
				</li>
				<?php
				}
				?>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2" style="margin-top:15px;">
	<div id="produccion_crear_left<?php echo $NumOfID ?>" style="float: left; margin-left:0px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td style="width: 155px; background-color: #F18B0C; color: white; padding: 5px;">
					INSUMOS REQUERIDOS
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<?php
			if ($Ord_Produccion == "")
			{
			?>
			<tr>
				<td colspan="2" style="padding:0px 0px 10px 0px;">
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
								Origen
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Proceso
							</td>
						</tr>
						<tr>
							<td>
								<div id="produccion_crear_codigo1<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_crear_producto1<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_crear_cantidad1<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_crear_origen1<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_crear_destino1<?php echo $NumOfID ?>" style="margin:0px 5px;"></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="2" style="padding:0px 0px 30px 0px;">
					<div id="produccion_crear_productos_grid1<?php echo $NumOfID ?>"></div>
				</td>
			</tr>
			<tr>
				<td style="width: 155px; background-color: #6E93CE; color: white; padding: 5px;">
					PRODUCTOS A OBTENER
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<?php
			if ($Ord_Produccion == "")
			{
			?>
			<tr>
				<td colspan="2" style="padding:0px 0px 10px 0px;">
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
								Proceso
							</td>
							<td style="border-bottom: 1px solid #A4BED4;">
								Enviar a
							</td>
						</tr>
						<tr>
							<td>
								<div id="produccion_crear_codigo2<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_crear_producto2<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_crear_cantidad2<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_crear_origen2<?php echo $NumOfID ?>" style="margin-left:5px;"></div>
							</td>
							<td>
								<div id="produccion_crear_destino2<?php echo $NumOfID ?>" style="margin:0px 5px;"></div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="2" style="padding:0px;">
					<div id="produccion_crear_productos_grid2<?php echo $NumOfID ?>"></div>
				</td>
			</tr>
		</table>
	</div>
	<div id="produccion_crear_right<?php echo $NumOfID ?>" style="float: left; margin-left:10px;">
		<table style="margin: 0px 0px 10px 0px;" cellpadding="0" cellspacing="0">
			<tr style="background-color: #629C62; color: white;">
				<td style="padding:5px;">
					ASIGNAR OPERARIOS
				</td>
			</tr>
			<tr>
				<td>
					Trefilado
				</td>
			</tr>
			<tr>
				<td>
					<div id="produccion_crear_trefilado_operario<?php echo $NumOfID ?>"></div>
				</td>
			</tr>
			<tr>
				<td>
					Corte y Enderezado
				</td>
			</tr>
			<tr>
				<td>
					<div id="produccion_crear_enderezado_operario<?php echo $NumOfID ?>"></div>
				</td>
			</tr>
			<tr>
				<td>
					Electrosoldado
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div id="produccion_crear_soldado_operario<?php echo $NumOfID ?>"></div>
				</td>
			</tr>
			<tr>
				<td>
					Figurado
				</td>
			</tr>
			<tr>
				<td>
					<div id="produccion_crear_figurado_operario<?php echo $NumOfID ?>"></div>
				</td>
			</tr>
			<tr>
				<td style="padding-top:30px;">
					<input type="button" id="produccion_crear_guardar<?php echo $NumOfID ?>" value="Guardar"/>
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" id="produccion_crear_nuevo<?php echo $NumOfID ?>" value="Nuevo"/>
				</td>
			</tr>
			<tr>
				<td>
					<input type="button" id="produccion_crear_imprimir<?php echo $NumOfID ?>" value="Imprimir"/>
				</td>
			</tr>
		</table>
	</div>
</div>