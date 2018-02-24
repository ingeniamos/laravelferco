<?php
session_start();
$Interno = isset($_POST['Interno']) ? $_POST['Interno']:"";

	if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
	{
		$_SESSION["NumOfID"]++;
	}
$NumOfID = $_SESSION["NumOfID"];
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ID_Incorrecto = false;
	var CostoCargado = false;
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	<?php if (!isset($_POST["Crear_Emergente"]) || $_POST["Crear_Emergente"] == false) { ?>
		var Main = document.getElementById("Compras_Content");
		var Body = document.getElementById("Compras_Crear_Content");
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
	<?php } ?>
	// END - Code for Refresh Data
	
	function ClearDocument()
	{
		// Clean Variables
		ID_Incorrecto = false;
		CostoCargado = false;
		//
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	};
	
	$("#compras_crear_fecha_comp<?php echo $NumOfID; ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#compras_crear_fecha_comp<?php echo $NumOfID; ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	// prepare the data
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
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
	
	function ReDefine()
	{
		ClearJSON = [
			//-1
			{id:"compras_crear_entrada<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_doc_transp<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_factura<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_pedido<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_interno<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_direccion<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_telefono<?php echo $NumOfID; ?>", type:"jqxMaskedInput"},
			{id:"compras_crear_e-mail<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_contacto_p<?php echo $NumOfID; ?>", type:""},
			//-2
			{id:"compras_crear_codigo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_producto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_ultimo_costo<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_cantidad<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_nuevo_costo<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_products_grid<?php echo $NumOfID; ?>", type:"jqxGrid"},
			//-3
			{id:"compras_crear_observaciones<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_notas<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_subtotal_total<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_tipo_servicio<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_tipo_descuento<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>", type:""},
			{id:"compras_crear_iva_precio<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_total_total<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_conductor<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_placa<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_formaP<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_peso_bascula<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_peso_remision<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_pesokg<?php echo $NumOfID; ?>", type:""},
		];

		EnableDisableJSON = [
			//-1
			{id:"compras_crear_entrada<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_doc_transp<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_factura<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_pedido<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_fecha_comp<?php echo $NumOfID; ?>", type:"jqxDateTimeInput"},
			{id:"compras_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			//-2
			{id:"compras_crear_codigo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_producto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_cantidad<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"compras_crear_nuevo_costo<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"crear_products_addrowbutton<?php echo $NumOfID; ?>", type:"jqxButton"},
			{id:"crear_products_deleterowbutton<?php echo $NumOfID; ?>", type:"jqxButton"},
			{id:"compras_crear_products_grid<?php echo $NumOfID; ?>", type:"jqxGrid"},
			//-3
			{id:"compras_crear_subtotal_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"compras_crear_tipo_servicio<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"compras_crear_tipo_descuento<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"compras_crear_iva_precio<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"compras_crear_total_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"compras_crear_conductor<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_placa<?php echo $NumOfID; ?>", type:""},
			{id:"compras_crear_formaP<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"compras_crear_peso_bascula<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"compras_crear_peso_remision<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"compras_crear_guardar<?php echo $NumOfID; ?>", type:"jqxButton"},
			{id:"compras_crear_guardar_pedido<?php echo $NumOfID; ?>", type:"jqxButton"},
			{id:"compras_crear_client_benef<?php echo $NumOfID; ?>", type:"jqxButton"},
		];
	}
	ReDefine();
	
	function CargarCliente (ID_Cliente)
	{
		var ValoresSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Direccion', type: 'string'},
				{ name: 'ContactoP', type: 'string'},
				{ name: 'Telefono', type: 'string'},
				{ name: 'Email', type: 'string'},
			],
			type: 'GET',
			data: {"Valores":ID_Cliente},
			url: "modulos/datos.php",
			async: true,
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function () {
				var records = ValoresDataAdapter.records;
				$("#compras_crear_direccion<?php echo $NumOfID; ?>").val(records[0]["Direccion"]);
				$("#compras_crear_telefono<?php echo $NumOfID; ?>").val(records[0]["Telefono"]);
				$("#compras_crear_contacto_p<?php echo $NumOfID; ?>").val(records[0]["ContactoP"]);
				$("#compras_crear_e-mail<?php echo $NumOfID; ?>").val(records[0]["Email"]);
			}
		});
	};
	
	function CargarValores ()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Interno', type: 'string'},
				{ name: 'Entrada', type: 'string'},
				{ name: 'Doc_Transp', type: 'string'},
				{ name: 'Factura', type: 'string' },
				{ name: 'Pedido', type: 'string' },
				{ name: 'Fecha', type: 'string'},
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Observaciones', type: 'string' },
				{ name: 'FormaP', type: 'string' },
				{ name: 'Conductor', type: 'string' },
				{ name: 'Placa', type: 'string'},
				{ name: 'TipoServicio', type: 'string' },
				{ name: 'TipoServicioValor', type: 'decimal' },
				{ name: 'TipoDescuento', type: 'string' },
				{ name: 'TipoDescuentoValor', type: 'decimal' },
				{ name: 'Peso_Bascula', type: 'decimal'},
				{ name: 'Peso_Remision', type: 'decimal'},
				//--
				{ name: 'CodFab', type: 'string'},
				{ name: 'Nombre', type: 'string' },
				{ name: 'UndMed', type: 'string'},
				{ name: 'Peso', type: 'decimal' },
				{ name: 'Cantidad', type: 'decimal'},
				{ name: 'Dcto', type: 'decimal'},
				{ name: 'UltCosto', type: 'decimal' },
				{ name: 'Unitario', type: 'decimal' },
			],
			type: 'GET',
			data:{"Compras_Modificar":"<?php echo $Interno; ?>"},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				if (records[0]["Interno"] == undefined)
				{
					Alerts_Box("La Compra Ingresada, no posee datos...", 4);
					return;
				}
				
				var len = records.length;
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"CodFab":records[i]["CodFab"],
						"Nombre":records[i]["Nombre"],
						"UndMed":records[i]["UndMed"],
						"Peso":records[i]["Peso"],
						"Cantidad":records[i]["Cantidad"],
						"Dcto":records[i]["Dcto"],
						"UltCosto":records[i]["UltCosto"],
						"Unitario":records[i]["Unitario"],
					}];
					$("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
				}
				$("#compras_crear_entrada<?php echo $NumOfID; ?>").val(records[0]["Entrada"])
				$("#compras_crear_doc_transp<?php echo $NumOfID; ?>").val(records[0]["Doc_Transp"]);
				$("#compras_crear_factura<?php echo $NumOfID; ?>").val(records[0]["Factura"]);
				$("#compras_crear_pedido<?php echo $NumOfID; ?>").val(records[0]["Pedido"]);
				$("#compras_crear_interno<?php echo $NumOfID; ?>").val("<?php echo $Interno; ?>");
				$("#compras_crear_fecha_comp<?php echo $NumOfID; ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#compras_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('selectItem', records[0]["ClienteID"]);
				$("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('selectItem', records[0]["ClienteID"]);
				$("#compras_crear_observaciones<?php echo $NumOfID; ?>").val(records[0]["Observaciones"]);
				$("#compras_crear_formaP<?php echo $NumOfID; ?>").val(records[0]["FormaP"]);
				$("#compras_crear_conductor<?php echo $NumOfID; ?>").val(records[0]["Conductor"]);
				$("#compras_crear_placa<?php echo $NumOfID; ?>").val(records[0]["Placa"]);
				$("#compras_crear_tipo_servicio<?php echo $NumOfID; ?>").val(records[0]["TipoServicio"]);
				$("#compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>").val(records[0]["TipoServicioValor"]);
				
				if (records[0]["TipoDescuento"] == "" || records[0]["TipoDescuento"] == undefined)
					$("#compras_crear_tipo_descuento<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				else
					$("#compras_crear_tipo_descuento<?php echo $NumOfID; ?>").val(records[0]["TipoDescuento"]);
				
				$("#compras_crear_tipo_descuento_precio<?php echo $NumOfID; ?>").val(records[0]["TipoDescuentoValor"]);
				$("#compras_crear_peso_bascula<?php echo $NumOfID; ?>").val(records[0]["Peso_Bascula"]);
				$("#compras_crear_peso_remision<?php echo $NumOfID; ?>").val(records[0]["Peso_Remision"]);
				CargarCliente(records[0]["ClienteID"]);
				Calcular();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	$("#Compras_Crear_HideButton<?php echo $NumOfID; ?>").click(function() {
		$("#Compras_Crear_Content_to_Hide<?php echo $NumOfID; ?>").toggle();
	});
	
	//------------------------------------------- KEY JUMPS
	$('#compras_crear_entrada<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_doc_transp<?php echo $NumOfID; ?>").jqxInput('focus');
		}
	});
	$('#compras_crear_doc_transp<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_factura<?php echo $NumOfID; ?>").jqxInput('focus');
		}
	});
	$('#compras_crear_factura<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_pedido<?php echo $NumOfID; ?>").jqxInput('focus');
		}
	});
	$('#compras_crear_pedido<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_fecha_comp<?php echo $NumOfID; ?>").jqxDateTimeInput('focus');
		}
	});
	$('#compras_crear_fecha_comp<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$('#compras_crear_cliente<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$('#compras_crear_cliente_ID<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$('#compras_crear_codigo<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$('#compras_crear_producto<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			if (CostoCargado == false) {
				return;
			}
			else {
				$("#compras_crear_cantidad<?php echo $NumOfID; ?>").jqxNumberInput('focus');
				var input = $('#compras_crear_cantidad<?php echo $NumOfID; ?> input')[0];
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
		}
	});
	$('#compras_crear_cantidad<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			$("#compras_crear_nuevo_costo<?php echo $NumOfID; ?>").jqxNumberInput('focus');
				var input = $('#compras_crear_nuevo_costo<?php echo $NumOfID; ?> input')[0];
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
	$('#compras_crear_nuevo_costo<?php echo $NumOfID; ?>').keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row();
		}
	});

	$("#compras_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox(
	{
		width: 410,
		height: 20,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#compras_crear_cliente<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").val(event.args.item.value);
		}
	});
	
	$("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 180,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#compras_crear_cliente<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#compras_crear_cliente<?php echo $NumOfID; ?>").val(event.args.item.value);
			CargarCliente(event.args.item.value);
		}
	});

	$("#compras_crear_direccion<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 380,
		disabled: true,
	});

	$("#compras_crear_telefono<?php echo $NumOfID; ?>").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 112,
		mask: '(###)###-####',
		disabled: true,
	});

	$("#compras_crear_contacto_p<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 160,
		disabled: true,
	});
	
	$("#compras_crear_e-mail<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 160,
		disabled: true,
	});
	
	$("#compras_crear_doc_transp<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	
	$("#compras_crear_entrada<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	$("#compras_crear_entrada<?php echo $NumOfID ?>").on("change", function ()
	{
		if ("<?php echo $Interno ?>" == "")
		{
			var value = $("#compras_crear_entrada<?php echo $NumOfID ?>").val();
			if (value == "")
				return;
			
			var FindSource =
			{
				datatype: "json",
				datafields: [
					{ name: 'Same', type: 'bool'},
				],
				data: {
					"Compras_Check_SameID":value,
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
						$("#compras_crear_entrada<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
						ID_Incorrecto = true;
						Alerts_Box("El Numero de Entrada Ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
						WaitClick_Input("compras_crear_entrada<?php echo $NumOfID ?>");
					}
					else
					{
						$("#compras_crear_entrada<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
						ID_Incorrecto = false;
					}
				}
			});
		}
	});
	
	$("#compras_crear_pedido<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	
	$("#compras_crear_factura<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	
	$("#compras_crear_interno<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
		disabled: true
	});
	
	//---------------------------------------------------------------- PARTE 2
	//-- GLOBAL
	var CodFabID = 0;
	var RowAdded = true;
	
	// prepare the data
	var CB_ProductoSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'CodFab', type: 'string'},
		{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
		async: true
	};
	
	var CB_ProductoDataAdapter = new $.jqx.dataAdapter(CB_ProductoSource);
	
	function GetPriceList ()
	{
		CostoCargado = false;
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'UltCosto', type: 'decimal' },
			],
			type: 'GET',
			data:{"Compras_Precios":CodFabID},
			url: "modulos/datos_productos.php",
			async: true,
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				$("#compras_crear_ultimo_costo<?php echo $NumOfID; ?>").val(records[0]["UltCosto"]);
				CostoCargado = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: " + error, 3);
			},
		});
	};
	
	$("#compras_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		source: CB_ProductoDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#compras_crear_codigo<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#compras_crear_producto<?php echo $NumOfID; ?>").val(CodFabID);
				GetPriceList();
				clearTimeout(Timer1);
			},150);
		}
	});
	
	$("#compras_crear_producto<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 320,
		height: 20,
		source: CB_ProductoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#compras_crear_producto<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args) {
			CodFabID = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#compras_crear_codigo<?php echo $NumOfID; ?>").val(CodFabID);
				GetPriceList();
				clearTimeout(Timer1);
			},150);
		}
	});
	
	$("#compras_crear_ultimo_costo<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 155,
		inputMode: 'simple',
		disabled: true,
	});
	
	$("#compras_crear_cantidad<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 160,
		inputMode: 'simple',
		spinButtons: false
	});
	
	$("#compras_crear_nuevo_costo<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18
	});
	
	$('#compras_crear_form_validation<?php echo $NumOfID; ?>').jqxValidator({
		rules:
		[
			{
				input: '#compras_crear_cantidad<?php echo $NumOfID; ?>', message: 'Debe ser un valor mayor a 0!', action: 'keyup, blur', rule: function (input, commit) {
					if (input.val() < 0) {
						return false;
					}
					return true;
				}
			}
		]
	});
	
	var source =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'UltCosto', type: 'decimal' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Dcto', type: 'decimal' },
			{ name: 'Unitario', type: 'decimal' },
			{ name: 'Subtotal', type: 'decimal' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var dataAdapter = new $.jqx.dataAdapter(source);
	
	function Add_Row()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;

		var ProductName = $("#compras_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
		var ProductOldPrice = $("#compras_crear_ultimo_costo<?php echo $NumOfID; ?>").val();
		var ProductNewPrice = $("#compras_crear_nuevo_costo<?php echo $NumOfID; ?>").val();
		var CantidadNum = $("#compras_crear_cantidad<?php echo $NumOfID; ?>").val();
		
		if (! ProductName | ProductName <= 0) {
			Alerts_Box("Favor Seleccionar un Producto!", 3);
			WaitClick_Combobox("compras_crear_producto<?php echo $NumOfID; ?>");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 3);
			WaitClick_NumberInput("compras_crear_cantidad<?php echo $NumOfID; ?>");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $('#compras_crear_products_grid<?php echo $NumOfID; ?>').jqxGrid('getrowdata', i);
			if (currentRow.CodFab == CodFabID)
			{
				var totalc = CantidadNum + currentRow.Cantidad;

				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"UndMed":currentRow.UndMed,
					"UltCosto":currentRow.UltCosto,
					"Cantidad":totalc,
					"Peso":currentRow.Peso,
					"Dcto":currentRow.Dcto,
					"Unitario":currentRow.Unitario,
				}];
				var id = $("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid('getrowid', i);
				$("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid('deleterow', id);
				$("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#compras_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#compras_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#compras_crear_ultimo_costo<?php echo $NumOfID; ?>").val('');
				$("#compras_crear_cantidad<?php echo $NumOfID; ?>").val('');
				$("#compras_crear_nuevo_costo<?php echo $NumOfID; ?>").val('');
				Calcular();
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
			data:{"Compras_Productos":CodFabID},
			url: "modulos/datos_productos.php",
			async: true,
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":CodFabID,
					"Nombre":ProductName.label,
					"UndMed":records[0]["UndMed"],
					"UltCosto":ProductOldPrice,
					"Cantidad":CantidadNum,
					"Peso":records[0]["Peso"],
					"Dcto":0,
					"Unitario":ProductNewPrice
				}];
				$("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#compras_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#compras_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#compras_crear_ultimo_costo<?php echo $NumOfID; ?>").val('');
				$("#compras_crear_cantidad<?php echo $NumOfID; ?>").val('');
				$("#compras_crear_nuevo_costo<?php echo $NumOfID; ?>").val('');
				Calcular();
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: "+error, 3);
			},
		});
	};

	$("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 260,
		source: dataAdapter,
		showtoolbar: true,
		editable: true,
		editmode: 'dblclick',
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="crear_products_addrowbutton<?php echo $NumOfID; ?>" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="crear_products_deleterowbutton<?php echo $NumOfID; ?>" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#crear_products_addrowbutton<?php echo $NumOfID; ?>").jqxButton({theme: mytheme, template: "success"});
			$("#crear_products_deleterowbutton<?php echo $NumOfID; ?>").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#crear_products_addrowbutton<?php echo $NumOfID; ?>").on('click', function () {
				Add_Row();
			});
			// delete row.
			$("#crear_products_deleterowbutton<?php echo $NumOfID; ?>").on('click', function () {
				var selectedrowindex = $("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid('getselectedrowindex');
				var rowscount = $("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid('deleterow', id);
					Calcular();
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: 90, height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: 240, height: 20 },
			{ text: 'Und', datafield: 'UndMed', editable: false, width: 50, height: 20 },
			{ text: 'U. Costo', datafield: 'UltCosto', editable: false, width: 100, height: 20, cellsalign: 'right' },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: 100,
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
				text: 'Peso',
				datafield: 'Peso',
				width: 100,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
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
				text: '%Dcto',
				datafield: 'Dcto',
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'p',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 2 });
				}
			},
			{
				text: 'Unitario',
				datafield: 'Unitario',
				width: 110,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "El precio del Producto debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'SubTotal',
				datafield: 'Subtotal',
				width: 150,
				height: 20,
				editable: false,
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad);
					var total2 = Math.round( total - (parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad)) / 100 * parseFloat(rowdata.Dcto) );
					
					return "<div style='margin: 4px;' class='jqx-right-align'>" + dataAdapter.formatNumber(total2, "c2") + "</div>";
				}
			}
		]
	});
	
	$("#compras_crear_products_grid<?php echo $NumOfID; ?>").on('cellvaluechanged', function (event) 
	{
		if (event.args) {
			var datafield = event.args.datafield;
			if (datafield == "Cantidad" || datafield == "Dcto" || datafield == "Unitario") {
				Calcular();
			}
		}
	});
	
	// ------------------------------------------ PARTE 3
	
	$("#compras_crear_conductor<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 258,
	});
	
	$("#compras_crear_placa<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 95,
	});
	
	$("#compras_crear_formaP<?php echo $NumOfID; ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 130,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		dropDownHeight: 50
	});
	$("#compras_crear_formaP<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#compras_crear_formaP<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#compras_crear_formaP<?php echo $NumOfID; ?>").jqxComboBox('addItem', {label: "Efectivo"});
	$("#compras_crear_formaP<?php echo $NumOfID; ?>").jqxComboBox('addItem', {label: "Credito"});
	
	$("#compras_crear_peso_bascula<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 138,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 12,
	});
	
	$("#compras_crear_peso_remision<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 138,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 12,
	});
	
	$("#compras_crear_pesokg<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 138,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 12,
		disabled: true
	});
	
	var iva = 0;
	var total = 0;
	
	$("#compras_crear_subtotal_text<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "               SUBTOTAL",
	});
	
	$("#compras_crear_subtotal_total<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999
	});
	
	function Calcular(Ignore)
	{
		var Calc_Timer = setTimeout(function()
		{
			var information = $('#compras_crear_products_grid<?php echo $NumOfID; ?>').jqxGrid('getdatainformation');
			var rowscounts = information.rowscount;
			var old_total = 0;
			var total_peso = 0;
			for (i=0; i<rowscounts; i++){
				var currentRow = $('#compras_crear_products_grid<?php echo $NumOfID; ?>').jqxGrid('getrowdata', i);
				var total1 = parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad);
				var total2 = Math.round( total1 - (parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad)) / 100 * parseFloat(currentRow.Dcto) );
				old_total = old_total + total2;
				total_peso = total_peso + parseFloat(currentRow.Peso) * parseFloat(currentRow.Cantidad);
			};
			
			var valor_tipo = $('#compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>').val();
			old_total = old_total + valor_tipo;
			
			if (total == old_total && Ignore == false)
				return;
			else
				total = old_total;
			if ($('#compras_crear_fecha_comp<?php echo $NumOfID ?>').val() >= '01-11-2017')
				iva = Math.round(total - (total / 1.19));
			else
				iva = Math.round(total - (total / 1.16));			
			/*var tipo_dcto = $("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val();
			if (tipo_dcto <= 0 && $("#compras_crear_tipo_descuento<?php echo $NumOfID ?>").val() != "" ) {
				tipo_dcto = iva;
				$("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val(tipo_dcto);
			}*/
			var tipo_dcto = $("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val();
			var dcto = $("#compras_crear_tipo_descuento<?php echo $NumOfID ?>").val();
			if (tipo_dcto <= 0 && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val(tipo_dcto);
			}
			else if (tipo_dcto != iva && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val(tipo_dcto);
			}
			else if (tipo_dcto <= 0 && dcto.indexOf("IVA") < 1)
			{
				$("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val("0");
			}
			
			var subtotal = total;
			total = total - tipo_dcto;
			if (total < 0)
				total = 0;
			
			$("#compras_crear_subtotal_total<?php echo $NumOfID; ?>").val(subtotal);
			$("#compras_crear_iva_precio<?php echo $NumOfID; ?>").val(iva);
			$("#compras_crear_total_total<?php echo $NumOfID; ?>").val(total);
			$("#compras_crear_pesokg<?php echo $NumOfID; ?>").val(total_peso);
			$("#compras_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('focus');
			clearTimeout(Calc_Timer);
		},200);
	};

	var ServicioSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'}
		],
		type: 'GET',
		data: {"OtroSrv":true},
		url: "modulos/parametros.php",
		async: true
	};
	var ServicioDataAdapter = new $.jqx.dataAdapter(ServicioSource);
	
	$("#compras_crear_tipo_servicio<?php echo $NumOfID; ?>").jqxComboBox({
		theme: mytheme,
		height: 25,
		width: 180,
		source: ServicioDataAdapter,
		dropDownHeight: 70,
		promptText: 'Tipo Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Nombre'
	});
	$("#compras_crear_tipo_servicio<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#compras_crear_tipo_servicio<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18
	});
	$('#compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>').on('change', function (event) 
	{
		Calcular();
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("compras_crear_tipo_servicio_precio");
		}
	});

	$("#compras_crear_iva_text<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                     IVA",
	});
	
	$("#compras_crear_iva_precio<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18
	});
	
	var DescuentoSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'}
		],
		type: 'GET',
		data: {"TipoDcto":true},
		url: "modulos/parametros.php",
		async: true
	};
	var DescuentoDataAdapter = new $.jqx.dataAdapter(DescuentoSource);
	
	$("#compras_crear_tipo_descuento<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 25,
		width: 180,
		source: DescuentoDataAdapter,
		dropDownHeight: 70,
		promptText: 'Tipo Descuento',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Nombre'
	});
	$("#compras_crear_tipo_descuento<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args) {
			$("#compras_crear_tipo_descuento<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val("0");
		}
		else
			Calcular(true);
	});
	
	$("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").on('change', function (event) 
	{
		Calcular(true);
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>");
		}
	});
	
	$("#compras_crear_total_text<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                  TOTAL",
	});
	
	$("#compras_crear_total_total<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999
	});
	
	function CrearPedido (type)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ID_Incorrecto) {
			Alerts_Box("El Numero de Entrada Ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
			Locked = false;
			return;
		}
		
		var datinfo = $("#compras_crear_products_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		if (type == 1)
		{
			if ($("#compras_crear_entrada<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_entrada<?php echo $NumOfID; ?>").val() == "") {
				Alerts_Box("Debe Ingresar Un Numero de Entrada!", 3);
				WaitClick_Input("compras_crear_entrada<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
			
			if ($("#compras_crear_pedido<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_pedido<?php echo $NumOfID; ?>").val() == "") {
				Alerts_Box("Debe Ingresar Un Numero de Pedido", 3);
				WaitClick_Input("compras_crear_pedido<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
		}
		
		if ($("#compras_crear_cliente<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_cliente<?php echo $NumOfID; ?>").val() == "") {
			Alerts_Box("Debe Ingresar un Nombre de Cliente!", 3);
			WaitClick_Combobox("compras_crear_cliente<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if ($("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").val() == "") {
			Alerts_Box("Debe Ingresar un ID de Cliente!", 3);
			WaitClick_Combobox("compras_crear_cliente<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		if (count <= 0) {
			Alerts_Box("Debe Ingresar un Producto!", 3);
			WaitClick_Combobox("compras_crear_producto<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if ($("#compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>").val() > 0) {
			if ($("#compras_crear_tipo_servicio<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_tipo_servicio<?php echo $NumOfID; ?>").val() == "") {
				Alerts_Box("Debe Ingresar Un Tipo de Servicio", 3);
				WaitClick_Combobox("compras_crear_tipo_servicio<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
		}
		
		if ($("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val() > 0) {
			if ($("#compras_crear_tipo_descuento<?php echo $NumOfID ?>").val() <= 0 | $("#compras_crear_tipo_descuento<?php echo $NumOfID ?>").val() == "") {
				Alerts_Box("Debe Ingresar Un Tipo de Descuento", 3);
				WaitClick_Combobox("compras_crear_tipo_descuento<?php echo $NumOfID ?>");
				Locked = false;
				return;
			}
		}
		
		if (type == 1)
		{
			if ($("#compras_crear_conductor<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_conductor<?php echo $NumOfID; ?>").val() == "") {
				Alerts_Box("Debe Ingresar Un Nombre de Conductor", 3);
				WaitClick_Input("compras_crear_conductor<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
		
			if ($("#compras_crear_placa<?php echo $NumOfID; ?>").val() == "") {
				Alerts_Box("Debe Ingresar Un Numero de Placa", 3);
				WaitClick_Input("compras_crear_placa<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
			
			if ($("#compras_crear_peso_bascula<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_peso_bascula<?php echo $NumOfID; ?>").val() == "") {
				Alerts_Box("Debe Ingresar el Peso de Bascula", 3);
				WaitClick_NumberInput("compras_crear_peso_bascula<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
			
			if ($("#compras_crear_peso_remision<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_peso_remision<?php echo $NumOfID; ?>").val() == "") {
				Alerts_Box("Debe Ingresar el Peso de Remision", 3);
				WaitClick_NumberInput("compras_crear_peso_remision<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
		
		}
		
		if ($("#compras_crear_formaP<?php echo $NumOfID; ?>").val() <= 0 | $("#compras_crear_formaP<?php echo $NumOfID; ?>").val() == "") {
			Alerts_Box("Debe Ingresar Una Forma de Pago!", 3);
			WaitClick_Combobox("compras_crear_formaP<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		for (i = 0; i < count; i++)
		{
			var array = {};
			var currentRow = $('#compras_crear_products_grid<?php echo $NumOfID; ?>').jqxGrid('getrowdata', i);
			
			array["CodFab"] = currentRow.CodFab;
			array["Cantidad"] = currentRow.Cantidad;
			array["Dcto"] = currentRow.Dcto;
			array["UltCosto"] = currentRow.UltCosto;
			array["Unitario"] = currentRow.Unitario;
			
			if (i==0) {
				array["Entrada"] = $("#compras_crear_entrada<?php echo $NumOfID; ?>").val();
				array["Doc_Transp"] = $("#compras_crear_doc_transp<?php echo $NumOfID; ?>").val();
				array["Factura"] = $("#compras_crear_factura<?php echo $NumOfID; ?>").val();
				array["Pedido"] = $("#compras_crear_pedido<?php echo $NumOfID; ?>").val();
				array["Interno"] = $("#compras_crear_interno<?php echo $NumOfID; ?>").val();
				array["Fecha"] = GetFormattedDate($('#compras_crear_fecha_comp<?php echo $NumOfID; ?>').jqxDateTimeInput('getDate'));
				array["ClienteID"] = $("#compras_crear_cliente_ID<?php echo $NumOfID; ?>").val();
				//---
				array["Observaciones"] = $('#compras_crear_observaciones<?php echo $NumOfID; ?>').val();
				array["Subtotal"] = $("#compras_crear_subtotal_total<?php echo $NumOfID; ?>").val();
				array["TipoServicio"] = $("#compras_crear_tipo_servicio<?php echo $NumOfID; ?>").val();
				array["TipoServicioValor"] = $("#compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>").val();
				array["Iva"] = $("#compras_crear_iva_precio<?php echo $NumOfID; ?>").val();
				array["TipoDcto"] = $("#compras_crear_tipo_descuento<?php echo $NumOfID ?>").val();
				array["TipoDctoValor"] = $("#compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>").val();
				array["Total"] = $("#compras_crear_total_total<?php echo $NumOfID; ?>").val();
				array["Conductor"] = $("#compras_crear_conductor<?php echo $NumOfID; ?>").val();
				array["Placa"] = $("#compras_crear_placa<?php echo $NumOfID; ?>").val();
				array["FormaPago"] = $("#compras_crear_formaP<?php echo $NumOfID; ?>").val();
				array["Peso"] = $("#compras_crear_pesokg<?php echo $NumOfID; ?>").val();
				array["Peso_Bascula"] = $("#compras_crear_peso_bascula<?php echo $NumOfID; ?>").val();
				array["Peso_Remision"] = $("#compras_crear_peso_remision<?php echo $NumOfID; ?>").val();
				if (type == 1)
					array["Estado"] = "Creado";
				else
					array["Estado"] = "Pedido";
			}
			myarray[i] = array;
		};
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			url: "modulos/guardar.php",
			data: {"Compras_Crear":myarray},
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				ClickOK = false;
				ClickCANCEL = false;
				Alerts_Box("Datos Guardados con Exito!\nCodigo Interno Generado = \""+data+"\"\nClick en Aceptar para Limpiar los Campos.", 2, true);
				$("#compras_crear_interno<?php echo $NumOfID; ?>").val(data);
				EnableDisableAll(true);
				clearInterval(Timer1);
				Timer1 = setInterval(function(){
					if (ClickOK == true)
					{
						ClearDocument();
						clearInterval(Timer1);
						clearTimeout(Timer2);
					}
					else if (ClickCANCEL == true)
					{
						ClickOK = false;
						clearInterval(Timer1);
						clearTimeout(Timer2);
						Locked = false;
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
				Alerts_Box("Ocurrio un Error al intentar compras_crear_guardar los datos!<br/>Intente luego de unos segundos...<br/>ERROR: "+ textStatus +" - "+ errorThrown, 3);
				Locked = false;
			}
		});
	};
	
	$('#compras_crear_guardar<?php echo $NumOfID; ?>').jqxButton({ width: 150, template: "info" });
	$("#compras_crear_guardar<?php echo $NumOfID; ?>").bind('click', function ()
	{
		CrearPedido(1);
	});
	
	$('#compras_crear_guardar_pedido<?php echo $NumOfID; ?>').jqxButton({width: 150, template: "primary"});
	$("#compras_crear_guardar_pedido<?php echo $NumOfID; ?>").bind('click', function ()
	{
		CrearPedido(2);
	});
	
	$('#compras_crear_nuevo<?php echo $NumOfID; ?>').jqxButton({width: 150, template: "success"});
	$("#compras_crear_nuevo<?php echo $NumOfID; ?>").bind('click', function ()
	{
		ClearDocument();
	});
	
	$("#compras_crear_client_benef<?php echo $NumOfID; ?>").jqxButton({width: 150, template: "warning"});
	$("#compras_crear_client_benef<?php echo $NumOfID ?>").bind('click', function ()
	{
		$("#Compras_Crear_Tercero_Window<?php echo $NumOfID ?>").jqxWindow('open');
	});
	
	$("#compras_crear_imprimir<?php echo $NumOfID; ?>").jqxButton({width: 150, template: "warning"});
	$("#compras_crear_imprimir<?php echo $NumOfID; ?>").bind('click', function ()
	{
		window.open("imprimir/compra.php?Interno="+$("#compras_crear_interno<?php echo $NumOfID; ?>").val()+"", "", "width=700, height=600, menubar=no, titlebar=no");
	})
	
	// ------------------------------------------ WINDOWS
	//--- Crear Terceros
	$("#Compras_Crear_Tercero_Window<?php echo $NumOfID ?>").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 900,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				data: {"Crear_Emergente":true},
				url: "terceros/crear.php",
				async: true,
				success: function(data) 
				{
					
					$("#Compras_Crear_Tercero_Content<?php echo $NumOfID ?>").html(data);
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
	});
	
	//---
	$("#compras_crear_entrada<?php echo $NumOfID; ?>").focus();
	
	if ("<?php echo $Interno ?>" != "")
	{
		EnableDisableAll(true);
		$("#compras_crear_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		CargarValores();
	}
	
	<?php if (!isset($_POST["Crear_Emergente"]) || $_POST["Crear_Emergente"] == false) { ?>
		CheckRefresh();
	<?php } ?>
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Compras" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "false")
					{
			?>
						$("#compras_crear_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
						$("#compras_crear_guardar_pedido<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Compras" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#compras_crear_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
				}
			}
		} ?>
});
</script>
<style type="text/css">
	#Compras_Crear_HideButton<?php echo $NumOfID; ?> {
		width: 21px;
		height: 21px;
		padding-top: 2px;
		padding-right: 2px;
		float: left;
		display: block;
		border: 1px solid #A4BED4;
		font-size:15px;
		font-weight:700;
		margin-right: 10px;
		color: #FFF;
		position: absolute;
		background-color: #5BB75B;
		cursor: pointer;
	}

	#Compras_Crear_HideButton<?php echo $NumOfID; ?>:hover {
		background: #79CF79;
	}
	
	#Compras_Crear_Content_to_Hide<?php echo $NumOfID; ?> {
		float: left;
		margin-left: 35px;
		padding-bottom: 10px;
	}
</style>
<div id="Compras_Crear_Tercero_Window<?php echo $NumOfID ?>">
	<div id="Compras_Crear_Tercero_Title<?php echo $NumOfID ?>" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 340px;">Crear Cliente/Tercero</div>
	</div>
	<div id="Compras_Crear_Tercero_Content<?php echo $NumOfID ?>" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<div id="Compras_Crear_HideButton<?php echo $NumOfID; ?>">&nbsp;&raquo;&nbsp;</div>
	<div id="Compras_Crear_Content_to_Hide<?php echo $NumOfID; ?>">
		<table cellpadding="1" cellspacing="2">
			<tr>
				<td>
					Interno
				</td>
				<td>
					<input type="text" id="compras_crear_interno<?php echo $NumOfID; ?>"/>
				</td>
				<td colspan="8">
					<li class="parte1_li_txt">
						Entrada&nbsp;
					</li>
					<li>
						<input type="text" id="compras_crear_entrada<?php echo $NumOfID; ?>"/>&nbsp;
					</li>
					<li class="parte1_li_txt">
						Doc. Transp.&nbsp;
					</li>
					<li>
						<input type="text" id="compras_crear_doc_transp<?php echo $NumOfID; ?>"/>&nbsp;
					</li>
					<li class="parte1_li_txt">
						Factura&nbsp;
					</li>
					<li>
						<input type="text" id="compras_crear_factura<?php echo $NumOfID; ?>"/>&nbsp;
					</li>
					<li class="parte1_li_txt">
						Pedido&nbsp;
					</li>
					<li>
						<input type="text" id="compras_crear_pedido<?php echo $NumOfID; ?>"/>&nbsp;
					</li>
				</td>
			</tr>
			<tr>
				<td>
					F. Compra
				</td>
				<td>
					<div id="compras_crear_fecha_comp<?php echo $NumOfID; ?>"></div>
				</td>
				<td>
					Proveedor
				</td>
				<td colspan="3" style="padding-left:16px;">
					<div id="compras_crear_cliente<?php echo $NumOfID; ?>"></div>
				</td>
				<td>
					ID Proveedor
				</td>
				<td colspan="3">
					<div id="compras_crear_cliente_ID<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td>
					Direcci&oacute;n
				</td>
				<td colspan="3">
					<input type="text" id="compras_crear_direccion<?php echo $NumOfID; ?>"/>
				</td>
				<td colspan="7">
					<li class="parte1_li_txt">
						Telf.&nbsp;
					</li>
					<li>
						<input id="compras_crear_telefono<?php echo $NumOfID; ?>"/>&nbsp;
					</li>
					<li>
						<input type="text" id="compras_crear_e-mail<?php echo $NumOfID; ?>"/>&nbsp;
					</li>
					<li>
						<input type="text" id="compras_crear_contacto_p<?php echo $NumOfID; ?>"/>
					</li>
				</td>
			</tr>
		</table>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<form id="compras_crear_form_validation<?php echo $NumOfID; ?>" action="./">
		<table cellpadding="0" cellspacing="0" style="margin:10px 0px 10px 0px; border: 1px solid #A4BED4; text-align: center;">
			<tr style="background: #E0E9F5">
				<td style="border-bottom: 1px solid #A4BED4;">
					Codigo
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Producto
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Ult. Costo
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Cantidad
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Unitario
				</td>
			</tr>
			<tr>
				<td>
					<div id="compras_crear_codigo<?php echo $NumOfID; ?>" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="compras_crear_producto<?php echo $NumOfID; ?>" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="compras_crear_ultimo_costo<?php echo $NumOfID; ?>" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="compras_crear_cantidad<?php echo $NumOfID; ?>" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="compras_crear_nuevo_costo<?php echo $NumOfID; ?>" style="margin-left:7px; margin-right:7px;"></div>
				</td>
			</tr>
		</table>
	</form>
	<div id="compras_crear_products_grid<?php echo $NumOfID; ?>" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="0" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				Observaciones:
			</td>
			<td style="padding-left:15px;">
				Notas:
			</td>
			<td>
				<input type="text" id="compras_crear_subtotal_text<?php echo $NumOfID; ?>" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="compras_crear_subtotal_total<?php echo $NumOfID; ?>">
				</div>
			</td>
		</tr>
		<tr>
			<td rowspan="4">
				<textarea rows="7" cols="35" id="compras_crear_observaciones<?php echo $NumOfID; ?>" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td rowspan="4">
				<textarea rows="7" cols="25" id="compras_crear_notas<?php echo $NumOfID; ?>" readonly="true" style="resize:none; margin-left:15px;"></textarea>
			</td>
			<td>
				<div id="compras_crear_tipo_servicio<?php echo $NumOfID; ?>" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="compras_crear_tipo_servicio_precio<?php echo $NumOfID; ?>">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="compras_crear_iva_text<?php echo $NumOfID; ?>" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="compras_crear_iva_precio<?php echo $NumOfID; ?>">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="compras_crear_tipo_descuento<?php echo $NumOfID ?>" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="compras_crear_tipo_descuento_precio<?php echo $NumOfID ?>">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="compras_crear_total_text<?php echo $NumOfID; ?>" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="compras_crear_total_total<?php echo $NumOfID; ?>">
				</div>
			</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="2" style="margin:5px 0px 10px 0px;">
		<tr>
			<td>
				Conductor&nbsp;
			</td>
			<td colspan="4">
				<li>
					<input type="text" id="compras_crear_conductor<?php echo $NumOfID; ?>"/>
				</li>
				<li class="parte1_li_txt">
					Placa:
				</li>
				<li>
					<input type="text" id="compras_crear_placa<?php echo $NumOfID; ?>"/>
				</li>
			</td>
			<td style="margin-left:15px;">
				Forma de P.
			</td>
			<td>
				<div id="compras_crear_formaP<?php echo $NumOfID; ?>"></div>
			</td>
			<td style="padding-left:25px;">
				<input type="button" id="compras_crear_guardar<?php echo $NumOfID; ?>" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="compras_crear_guardar_pedido<?php echo $NumOfID; ?>" value="Guardar como Pedido"/>
			</td>
		</tr>
		<tr>
			<td colspan="7">
				<li class="parte1_li_txt">
					P. B&aacute;scula
				</li>
				<li>
					<div id="compras_crear_peso_bascula<?php echo $NumOfID; ?>"></div>
				</li>
				<li class="parte1_li_txt">
					P. Remision
				</li>
				<li>
					<div id="compras_crear_peso_remision<?php echo $NumOfID; ?>"></div>
				</li>
				<li class="parte1_li_txt">
					Peso (calculado)
				</li>
				<li>
					<div id="compras_crear_pesokg<?php echo $NumOfID; ?>">
					</div>
				</li>
			</td>
			<td style="padding-left:25px;">
				<input type="button" id="compras_crear_nuevo<?php echo $NumOfID; ?>" value="Nuevo"/>
			</td>
			<td>
				<input type="button" id="compras_crear_client_benef<?php echo $NumOfID; ?>" value="Crear Client/Benef."/>
			</td>
		</tr>
		<tr>
			<td colspan="7">
			</td>
			<td style="padding-left:25px;">
				<input type="button" id="compras_crear_imprimir<?php echo $NumOfID; ?>" value="Imprimir"/>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
	</table>
</div>
