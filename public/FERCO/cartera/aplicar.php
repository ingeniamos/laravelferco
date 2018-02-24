<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ID_Cliente = "";
	var ID_Cobrador = "";
	var Caja_Interno = "";
	var Caja_Recibo = "";
	var Timer = 0;
	var Interno = "";
	var Orden = "";
	var Codigos = false;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Cartera_Content");
	var Body = document.getElementById("Aplicar_Content");
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
				if (ID_Cliente == "")
					Cargar_Clientes();
				
				SystemMap("Aplicar", true);
				ReDefine();
				VOID();
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
	var Modificar = false;
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
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Aplicar" && $data[$i]["Supervisor"] == "true")
					{
			?>
						Supervisor = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Aplicar" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Aplicar" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Cartera" && $data[$i]["SubModulo"] == "Aplicar" && $data[$i]["Imprimir"] == "true")
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
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"cartera_aplicar_cliente", type:"jqxComboBox"},
			{id:"cartera_aplicar_cliente_ID", type:"jqxComboBox"},
			{id:"cartera_aplicar_cobrador", type:"jqxComboBox"},
			{id:"cartera_aplicar_cupo_credito", type:""},
			{id:"cartera_aplicar_cupo_credito_activo", type:"jqxCheckBox"},
			{id:"cartera_aplicar_estado", type:""},
			{id:"cartera_aplicar_deuda_total", type:""},
			{id:"cartera_aplicar_corriente", type:""},
			{id:"cartera_aplicar_de_30_a_45", type:""},
			{id:"cartera_aplicar_de_45_a_60", type:""},
			{id:"cartera_aplicar_de_60_a_90", type:""},
			{id:"cartera_aplicar_mas_de_90", type:""},
			{id:"cartera_aplicar_items_grid", type:"jqxGrid"},
			{id:"cartera_aplicar_email", type:""},
			{id:"cartera_aplicar_caja_recibo", type:"jqxComboBox"},
			{id:"cartera_aplicar_saldo", type:""},
			{id:"cartera_aplicar_total_abono", type:""},
			{id:"cartera_aplicar_restante", type:""},
			{id:"cartera_aplicar_docs_grid", type:"jqxGrid"},
		];
	}
	ReDefine();
	function VOID ()
	{
		$("#cartera_aplicar_items_grid").jqxGrid("updatebounddata");
		$("#cartera_aplicar_caja_recibo").jqxComboBox('clearSelection');
		$("#cartera_aplicar_caja_recibo").jqxComboBox('clear');
		$("#cartera_aplicar_fecha").jqxDateTimeInput('setDate', new Date());
		$("#cartera_aplicar_saldo").val("0");
		$("#cartera_aplicar_total_abono").val("0");
		$("#cartera_aplicar_restante").val("0");
		Cargar_Caja_Recibo();
		Cargar_Cartera();
	};
	
	function ClearDocument()
	{
		ID_Cliente = "";
		ID_Cobrador = "";
		Caja_Interno = "";
		Caja_Recibo = "";
		ClearAll();
		Cargar_Clientes();
	};
	
	// prepare data
	function Cargar_Clientes ()
	{
		ClienteSource.data = {"Clientes_Deudores":ID_Cobrador};
		ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
		$("#cartera_aplicar_cliente").jqxComboBox({source: ClienteDataAdapter});
		$("#cartera_aplicar_cliente_ID").jqxComboBox({source: ClienteDataAdapter});
	};
	
	function Cargar_Garantias ()
	{
		GarantiaSource.data = {"Cargar_Garantias":ID_Cliente};
		var GarantiaDataAdapter = new $.jqx.dataAdapter(GarantiaSource);
		$("#cartera_aplicar_docs_grid").jqxGrid({ source: GarantiaDataAdapter });
	};
	
	function Cargar_Caja_Recibo()
	{
		RC_Source.data = {"RecibosConSaldo": ID_Cliente};
		RC_Adapter = new $.jqx.dataAdapter(RC_Source);
		$("#cartera_aplicar_caja_recibo").jqxComboBox({source: RC_Adapter});
	}
	
	function Cargar_Cartera ()
	{
		var ValoresSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'DeudaTotal', type: 'decimal'},
				{ name: 'Corriente', type: 'decimal'},
				{ name: 'De30a45', type: 'decimal'},
				{ name: 'De45a60', type: 'decimal'},
				{ name: 'De60a90', type: 'decimal'},
				{ name: 'Mas90', type: 'decimal'},
			],
			type: 'GET',
			data: {"Cargar_Cartera":ID_Cliente},
			url: "modulos/datos.php",
			async: true,
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function () {
				var records = ValoresDataAdapter.records;
				$("#cartera_aplicar_deuda_total").val(records[0]["DeudaTotal"]);
				$("#cartera_aplicar_corriente").val(records[0]["Corriente"]);
				$("#cartera_aplicar_de_30_a_45").val(records[0]["De30a45"]);
				$("#cartera_aplicar_de_45_a_60").val(records[0]["De45a60"]);
				$("#cartera_aplicar_de_60_a_90").val(records[0]["De60a90"]);
				$("#cartera_aplicar_mas_de_90").val(records[0]["Mas90"]);
			}
		});
	};
	
	function Cargar_Valores ()
	{
		var ValoresSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'ContactoP', type: 'string'},
				{ name: 'TelefonoCP', type: 'string'},
				{ name: 'Email', type: 'string'},
				{ name: 'Cupo_Credito', type: 'decimal'},
				{ name: 'Cupo_Adicional', type: 'decimal'},
				{ name: 'Vigencia_Credito', type: 'string'},
				{ name: 'Vigencia_Adicional', type: 'string'},
				{ name: 'CupoCR_Check', type: 'bool'},
				{ name: 'EstadoC', type: 'string'},
				{ name: 'Apply', type: 'bool'},
				{ name: 'Garantia', type: 'string'},
			],
			data: {"Valores":ID_Cliente},
			url: "modulos/datos.php",
			async: true,
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function () {
				var records = ValoresDataAdapter.records;

				$("#cartera_aplicar_cupo_credito").val(records[0]["Cupo_Credito"]);
				$("#cartera_aplicar_cupo_adicional").val(records[0]["Cupo_Adicional"]);
				$("#cartera_aplicar_vigencia_credito").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Vigencia_Credito"])));
				$("#cartera_aplicar_vigencia_adicional").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Vigencia_Adicional"])));
				if (records[0]["CupoCR_Check"]) {
					$("#cartera_aplicar_cupo_credito_activo").jqxCheckBox('check');
					Credito_Activo = true;
				}
				else {
					$("#cartera_aplicar_cupo_credito_activo").jqxCheckBox('uncheck');
					Credito_Activo = false;
				}
				$("#cartera_aplicar_contacto").val(records[0]["ContactoP"]);
				$("#cartera_aplicar_telefono").val(records[0]["TelefonoCP"]);
				$("#cartera_aplicar_email").val(records[0]["Email"]);
				$("#cartera_aplicar_estado").val(records[0]["EstadoC"]);
				
				Cargar_Garantias();
				Cargar_Cartera();
				
				//Items Grid
				ItemsSource.data = {"Cartera_AplicarPago": ID_Cliente};
				ItemsAdapter = new $.jqx.dataAdapter(ItemsSource);
				$("#cartera_aplicar_items_grid").jqxGrid({source: ItemsAdapter});
				//RC Combobox
				Cargar_Caja_Recibo();
			}
		});
	};
	
	function AutoPagarFacturas ()
	{
		var datinfo = $("#cartera_aplicar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var i;
		for (i = 0; i < count; i++)
		{
			$("#cartera_aplicar_items_grid").jqxGrid("setcellvalue", i, "Apply", false);
		}
		for (i = 0; i < count; i++)
		{
			if ($("#cartera_aplicar_restante").val() > 0) {
				$("#cartera_aplicar_items_grid").jqxGrid("setcellvalue", i, "Apply", true);
			}
		}
	};
	
	function Cargar_RC ()
	{
		var Fecha_Recibo_Source =
		{
			datatype: "json",
			datafields: [
				{ name: 'Fecha', type: 'string'},
				{ name: 'Saldo', type: 'decimal'},
			],
			type: 'GET',
			data: {"RecibosConSaldo_Fecha":Caja_Recibo},
			url: "modulos/datos.php",
			async: true,
		};
		var Fecha_Recibo_DataAdapter = new $.jqx.dataAdapter(Fecha_Recibo_Source,{
			autoBind: true,
			loadComplete: function () {
				var records = Fecha_Recibo_DataAdapter.records;
				$("#cartera_aplicar_fecha").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#cartera_aplicar_saldo").val(records[0]["Saldo"]);
				$("#cartera_aplicar_restante").val(records[0]["Saldo"]);
				//AutoPagarFacturas();
			}
		});
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
	
	$("#cartera_aplicar_cliente").jqxComboBox(
	{
		width: 350,
		height: 20,
		theme: mytheme,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#cartera_aplicar_cliente").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#cartera_aplicar_cliente_ID").val(ID_Cliente);
				Cargar_Valores();
				clearTimeout(Timer);
			},350);
		}
	});

	document.getElementById("cartera_aplicar_cliente").addEventListener("dblclick", function (event)
	{
		var Value = $("#cartera_aplicar_cliente_ID").val();
		if (Value == "")
			return;
		else
			$("#Cartera_Aplicar_Tercero_Window").jqxWindow('open');
	});
	
	$("#cartera_aplicar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		theme: mytheme,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#cartera_aplicar_cliente_ID").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#cartera_aplicar_cliente").val(ID_Cliente);
				Cargar_Valores();
				clearTimeout(Timer);
			},350);
		}
	});
	document.getElementById("cartera_aplicar_cliente_ID").addEventListener("dblclick", function (event)
	{
		var Value = $("#cartera_aplicar_cliente_ID").val();
		if (Value == "")
			return;
		else
			$("#Cartera_Aplicar_Tercero_Window").jqxWindow('open');
	});
	
	$("#cartera_aplicar_cupo_credito").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 180,
		symbol: '$',
		digits: 18,
		max: 999999999999999999
	});
	
	$("#cartera_aplicar_vigencia_credito").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#cartera_aplicar_cupo_adicional").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 180,
		symbol: '$',
		digits: 18,
		max: 999999999999999999
	});
	
	$("#cartera_aplicar_vigencia_adicional").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	
	var CobradorData = new Array();
	
	var CobradorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Vendedor', type: 'string'}
		],
		data: {"Venta":true},
		url: "modulos/parametros.php",
		//async: false
	};
	//var CobradorDataAdapter = new $.jqx.dataAdapter(CobradorSource);
	var CobradorDataAdapter = new $.jqx.dataAdapter(CobradorSource,{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				CobradorData.push(records[i]["Codigo"]);
			}
		},
		loadComplete: function (records)
		{
			$("#cartera_aplicar_cobrador").jqxComboBox({ source: records });
		}
	});
	
	$("#cartera_aplicar_cobrador").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		//dropDownHeight: 100,
		promptText: 'Seleccionar Cobrador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#cartera_aplicar_cobrador").on('change', function (event)
	{
		if (event.args)
		{
			ID_Cobrador = event.args.item.value;
		}
		else
		{
			ID_Cobrador = "";
			$("#cartera_aplicar_cobrador").jqxComboBox('clearSelection');
			Cargar_Clientes();
		}
	});
	$("#cartera_aplicar_cobrador").on('select', function (event) {
		if (event.args) {
			ID_Cobrador = event.args.item.value;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				Cargar_Clientes();
				clearTimeout(Timer);
			},350);
		}
	});
	$("#cartera_aplicar_cobrador").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			ID_Cobrador = "<?php echo $_SESSION["UserCode"]; ?>";
			//$("#cartera_aplicar_cobrador").val("<?php echo $_SESSION["UserCode"]; ?>");
			$("#cartera_aplicar_cobrador").jqxComboBox("selectItem", "<?php echo $_SESSION["UserCode"]; ?>");
			$("#cartera_aplicar_cobrador").jqxComboBox({ disabled: true });
		}
	});
	
	$("#cartera_aplicar_contacto").jqxInput({
		theme: mytheme,
		width: 350,
		height: 20,
		disabled: true
	});
	
	$("#cartera_aplicar_telefono").jqxInput({//.jqxMaskedInput({
		theme: mytheme,
		width: 120,
		height: 20,
		//mask: '(###)###-####',
		disabled: true,
	});
	
	// --- SEARCH FILTER
	
	function Steps (TO)
	{
		var items = $("#cartera_aplicar_cliente").jqxComboBox('getItems');
		var len = items.length - 1;
		
		var index = $("#cartera_aplicar_cliente").jqxComboBox('getSelectedIndex');
		
		if (TO == "next")
		{
			if (index >= len)
				Alerts_Box("Ha llegado al Final de la Busqueda.", 1);
			else
				$("#cartera_aplicar_cliente").jqxComboBox('selectIndex',(index + 1));
		}
		else if (TO == "back")
		{
			if (index < 1)
				Alerts_Box("Ha llegado al Principio de la Busqueda.", 1);
			else
				$("#cartera_aplicar_cliente").jqxComboBox('selectIndex',(index - 1));
		}
	}
	
	$("#cartera_aplicar_back").jqxButton({
		width: 60,
		height: 25,
		template: "info"
	});
	$("#cartera_aplicar_back").bind('click', function ()
	{
		Steps("back")
	});
	
	$("#cartera_aplicar_next").jqxButton({
		width: 60,
		height: 25,
		template: "info"
	});
	$("#cartera_aplicar_next").bind('click', function ()
	{
		Steps("next");
	});
	
	$("#cartera_aplicar_estado").jqxInput({
		theme: mytheme,
		height: 20,
		width: 60,
		disabled: true
	});
	
	function ActualizarCupo()
	{
		if (ID_Cliente != "")
		{
			$("#Loading_Mess").html("Procesando Solicitud...");
			$("#Loading").show();
			$.ajax({
				dataType: "text",
				url: "modulos/guardar.php",
				data: {
					"ActualizarCupo":true,
					"Credito":$("#cartera_aplicar_cupo_credito").val(),
					"Adicional":$("#cartera_aplicar_cupo_adicional").val(),
					"Vigencia_Credito":GetFormattedDate($("#cartera_aplicar_vigencia_credito").jqxDateTimeInput('getDate')),
					"Vigencia_Adicional":GetFormattedDate($("#cartera_aplicar_vigencia_adicional").jqxDateTimeInput('getDate')),
					"Activo":$("#cartera_aplicar_cupo_credito_activo").val(),
					"ClienteID":ID_Cliente
				},
				async: true,
				success: function (data, status, xhr)
				{
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Alerts_Box("El Cupo Credito del Cliente se ha Actualizado.", 2);
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Alerts_Box("Ocurrio un Error al Intentar Actualizar el Cupo Credito<br/>Intente luego de unos Segundos", 3);
				}
			});
		}
	}
	
	function ActualizarEstado(Type)
	{
		if (ID_Cliente != "") {
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {"ActualizarEstado":Type,"ClienteID":ID_Cliente},
				async: true,
				success: function (data, status, xhr) {
					Alerts_Box("El Estado del Cliente se ha Actualizado.", 2);
					$("#cartera_aplicar_estado").val(Type);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
				}
			});
		}
	}
	
	$("#cartera_aplicar_estado_aldia").jqxButton({width: 60, template: "success"});
	$("#cartera_aplicar_estado_aldia").bind('click', function ()
	{
		ActualizarEstado("Al Dia");
	});
	
	$("#cartera_aplicar_estado_mora").jqxButton({width: 60, template: "danger"});
	$("#cartera_aplicar_estado_mora").bind('click', function ()
	{
		ActualizarEstado("Mora");
	});
	$("#cartera_aplicar_estado_juridico").jqxButton({width: 65, template: "info"});
	$("#cartera_aplicar_estado_juridico").bind('click', function ()
	{
		ActualizarEstado("Juridico");
	});
	
	$("#cartera_aplicar_cupo_credito_activo").jqxCheckBox({
		theme: mytheme,
		boxSize: 20,
	});
	
	$("#cartera_aplicar_guardar_cupo").jqxButton({width: 120, template: "success"});
	$("#cartera_aplicar_guardar_cupo").bind('click', function ()
	{
		ActualizarCupo();
	});
	
	//-------------- 2º TABLA
	
	$("#cartera_aplicar_deuda_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});

	$("#cartera_aplicar_corriente").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});

	$("#cartera_aplicar_de_30_a_45").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});

	$("#cartera_aplicar_de_45_a_60").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});

	$("#cartera_aplicar_de_60_a_90").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});

	$("#cartera_aplicar_mas_de_90").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});
	
	//-------------------------------------- PARTE 2
	
	var ItemsSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Apply', type: 'bool'},
			{ name: 'Fecha', type: 'date'},
			{ name: 'Interno', type: 'string'},
			{ name: 'Remision', type: 'string'},
			{ name: 'Factura', type: 'string'},
			{ name: 'Ord_Compra', type: 'string'},
			{ name: 'Valor', type: 'decimal'},
			{ name: 'Saldo', type: 'decimal'},
			{ name: 'Abono', type: 'decimal'},
			{ name: 'Saldo_Pendiente', type: 'decimal'},
			{ name: 'Vendedor', type: 'string'},
			{ name: 'Cobrador', type: 'string'},
		],
		url: "modulos/datos.php",
		updaterow: function (rowid, rowdata, commit)
		{
			if (Codigos == false)
			{
				commit(true);
				return;
			}
			
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Cartera_Aplicar_Codigos":true,
					"Interno":rowdata.Interno,
					"VendedorID":rowdata.Vendedor,
					"CobradorID":rowdata.Cobrador,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data == "")
						commit(true);
					else
					{
						Alerts_Box("Ocurrió un Error al Intentar Actualizar los Datos.<br/>Intente luego de unos segundos.", 3);
						commit(false);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
					commit(false);
				}
			});
		}
	};
	
	$("#cartera_aplicar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 800,
		enabletooltips: true,
		enablebrowserselection: true,
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		autoheight: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'Apply', columntype: 'checkbox', width: '1%', pinned: true, editable: Admin ? true:Modificar },
			{
				text: 'Fecha',
				datafield: 'Fecha',
				editable: false,
				width: '10%',
				height: 20,
				pinned: true,
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Remision', datafield: 'Remision', editable: false, width: '10%', height: 20, pinned: true, },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Factura', datafield: 'Factura', editable: false, width: '10%', height: 20, pinned: true, },
			{ text: 'Ord. Compra', datafield: 'Ord_Compra', editable: false, width: '12%', height: 20, },
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: false,
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{
				text: 'Saldo',
				datafield: 'Saldo',
				editable: false,
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
			{
				text: 'Abono',
				datafield: 'Abono',
				width: '15%',
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				},
				/*validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor o igual a 0!" };
					}
					return true;
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var maxval = 0;
					if ( rowdata.Saldo > 0)
						maxval = rowdata.Saldo;
					else
						maxval = rowdata.Valor;
						
					if (rowdata.Abono > maxval) {
						Alerts_Box("El Abono ingresado excede el Valor de la Deuda<br/>Ingrese un caja_crear_valor Igual o menor a: "+maxval, 3);
						return "<div id='row_error'>"+ItemsAdapter.formatNumber(rowdata.Abono, "c2")+"</div>";
					}
				}*/
			},
			{
				text: 'Saldo Nuevo',
				datafield: 'Saldo_Pendiente',
				editable: false,
				width: '14%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = 0;
					if ( rowdata.Saldo > 0)
						total = parseFloat(rowdata.Saldo) - parseFloat(rowdata.Abono);
					else
						total = parseFloat(rowdata.Valor) - parseFloat(rowdata.Abono);
						
					return "<div style='margin: 4px;' class='jqx-caja_crear_right-align'>" + ItemsAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
			{ text: 'Interno', datafield: 'Interno', editable: false, width: '12%', height: 20 },
			{
				text: 'Vend.',
				datafield: 'Vendedor',
				columntype: 'combobox',
				editable: Admin,
				width: '6%',
				height: 20,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: CobradorData,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Codigo',
						valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Cobr.',
				datafield: 'Cobrador',
				columntype: 'combobox',
				editable: Admin,
				width: '6%',
				height: 20,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: CobradorData,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Codigo',
						valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
		]
	});
	$("#cartera_aplicar_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#cartera_aplicar_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Factura")
		{
			Interno = $("#cartera_aplicar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Interno");
			Orden = $("#cartera_aplicar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Ord_Compra");
			$("#Cartera_Aplicar_Remision_Window").jqxWindow('open');
		}
	});
	$("#cartera_aplicar_items_grid").on('cellendedit', function (event) 
	{
		var datafield = event.args.datafield;
		
		if (datafield == "Vendedor" || datafield == "Cobrador")
			Codigos = true;
		else
			Codigos = false;
	});
	$("#cartera_aplicar_items_grid").bind('cellvaluechanged', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.newvalue;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Abono") {
			if (value > 0) {
				if ($('#cartera_aplicar_total_abono').val() <= $('#cartera_aplicar_saldo').val())
				{
					var total_disponible = $('#cartera_aplicar_saldo').val() - $('#cartera_aplicar_total_abono').val();
					var Valor = $('#cartera_aplicar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Valor");
					var Saldo = $('#cartera_aplicar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Saldo");
					var total_restante = 0;

					if (value > total_disponible)
					{
						if (total_disponible == 0) {
							if (value > oldvalue)
							{
								Alerts_Box("El Valor ingresado es Mayor al Disponible!", 3);
								$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
							}
							else
							{
								Do_TheMath();
							}
						}
						else {
							if ((total_disponible + oldvalue) != value) {
								Alerts_Box("El Valor ingresado es Mayor al Disponible!", 3);
								$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
							}
							else
							{
								if (Saldo > 0) {
									if (value <= Saldo) {
										Do_TheMath();
									}
									else {
										Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
										$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
									}
								}
								else {
									if (value <= Valor) {
										Do_TheMath();
									}
									else {
										Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
										$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
									}
								}
							}
						}
					}
					else {
						if (Saldo > 0) {
							if (value <= Saldo) {
								total_restante = total_disponible - value;
								$('#cartera_aplicar_total_abono').val($('#cartera_aplicar_total_abono').val() + value);
								$('#cartera_aplicar_restante').val(total_restante);
							}
							else {
								Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
								$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
							}
						}
						else {
							if (value <= Valor) {
								total_restante = total_disponible - value;
								$('#cartera_aplicar_total_abono').val($('#cartera_aplicar_total_abono').val() + value);
								$('#cartera_aplicar_restante').val(total_restante);
							}
							else {
								Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
								$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
							}
						}
					}
				}
				else
					Alerts_Box("Ya ha utilizado todo el saldo disponible!", 3);
			}
			else if (value == 0)
			{
				Do_TheMath();
			}
			else
			{
				Alerts_Box("No se permiten valores Negativos!", 3);
				$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
			}
		}	
		else if (datafield == "Apply") {
			if (value == true) {
				if ($('#cartera_aplicar_total_abono').val() < $('#cartera_aplicar_saldo').val())
				{
					var total_disponible = $('#cartera_aplicar_saldo').val() - $('#cartera_aplicar_total_abono').val();
					var Valor = $('#cartera_aplicar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Valor");
					var Saldo = $('#cartera_aplicar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Saldo");
					var Abono = $('#cartera_aplicar_items_grid').jqxGrid('getcellvalue', rowBoundIndex, "Abono");
					var total_add = 0;
					
					if (Saldo > 0) {
						if (total_disponible < Saldo) {
							total_add = total_disponible;
						}
						else {
							total_add = Saldo;
						}
					}
					else {
						if (total_disponible < Valor) {
							total_add = total_disponible;
						}
						else {
							total_add = Valor;
						}
					}
					if (Abono > 0) {
						total_add = total_add;
						$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
					}
					$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", total_add);
				}
				else {
					Alerts_Box("Ya ha utilizado todo el saldo disponible!", 3);
					$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Apply", false);
				}
			}
			else {
				$("#cartera_aplicar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
			}
		}
	});
	
	function Do_TheMath ()
	{
		var Total_Slado = $('#cartera_aplicar_saldo').val();
		var Total_Abono = 0;
		var datinfo = $("#cartera_aplicar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		if (count <= 0) {
			return;
		}
		for (i=0; i<count; i++)
		{
			var currentRow = $('#cartera_aplicar_items_grid').jqxGrid('getrowdata', i);
			Total_Abono = Total_Abono + currentRow.Abono;
		}
		
		$('#cartera_aplicar_total_abono').val(Total_Abono);
		$('#cartera_aplicar_restante').val(Total_Slado - Total_Abono);
	};
	
	var GarantiaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Apply', type: 'bool'},
			{ name: 'Garantia', type: 'string'},
		],
		//data: {"Garantias":true},
		//url: "modulos/parametros.php",
		url: "modulos/datos.php",
		async: true,
		updaterow: function (rowid, rowdata, commit)
		{
			if (ID_Cliente != "") {
				$.ajax({
					dataType: 'text',
					data: {
						"Actualizar_Garantias":ID_Cliente,
						"Apply":rowdata.Apply,
						"Garantia":rowdata.Garantia,
					},
					url: "modulos/guardar.php",
					async: true,
					success: function (data, status, xhr) {
						commit(true);
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
					}
				});
			}
			else
			{
				commit(false);
			}
		}
	};
	var GarantiaDataAdapter = new $.jqx.dataAdapter(GarantiaSource);
	
	$("#cartera_aplicar_docs_grid").jqxGrid({
		theme: mytheme,
		width: 150,
		height: 180,
		//source: GarantiaDataAdapter,
		//autoheight: true,
		editable: true,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'Apply', columntype: 'checkbox', width: '5%' },
			{ text: 'Garantia', datafield: 'Garantia', editable: false, width: '145%', height: 20 },
		]
	});
	
	var RC_Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'Caja_Interno', type: 'string'},
			{ name: 'Caja_Recibo', type: 'string'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#cartera_aplicar_caja_recibo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		dropDownHeight: 100,
		promptText: 'Seleccionar RC',
		selectedIndex: -1,
		displayMember: 'Caja_Recibo',
		valueMember: 'Caja_Interno',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#cartera_aplicar_caja_recibo").bind('change', function (event) {
		if (event.args)
		{
			Caja_Interno = event.args.item.value;
			Caja_Recibo = event.args.item.label;
		}
		else
		{
			Caja_Interno = 0;
			Caja_Recibo = 0;
			$("#cartera_aplicar_caja_recibo").jqxComboBox('clearSelection');
			$("#cartera_aplicar_saldo").val("0");
			$("#cartera_aplicar_total_abono").val("0");
			$("#cartera_aplicar_restante").val("0");
		}
	});
	$("#cartera_aplicar_caja_recibo").bind('select', function (event) {
		if (event.args) {
			Caja_Interno = event.args.item.value;
			Caja_Recibo = event.args.item.label;
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				Cargar_RC();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#cartera_aplicar_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
		showCalendarButton: false,
		disabled: true,
	});
	
	$("#cartera_aplicar_saldo").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 150,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#cartera_aplicar_total_abono").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 150,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#cartera_aplicar_restante").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 150,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999999,
		disabled: true,
	});
	
	//-------------------------------------- PARTE 3
	
	function Aplicar()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var ClienteID = $("#cartera_aplicar_cliente").jqxComboBox("getSelectedItem");
		if (!ClienteID)
		{
			Alerts_Box("Favor Seleccionar un Cliente.", 3);
			WaitClick_Combobox("cartera_aplicar_cliente");
			Locked = false;
			return;
		}
		
		var Caja = $("#cartera_aplicar_caja_recibo").jqxComboBox("getSelectedItem");
		if (!Caja)
		{
			Alerts_Box("Favor Seleccionar un Recibo de Caja.", 3);
			WaitClick_Combobox("cartera_aplicar_caja_recibo");
			Locked = false;
			return;
		}
		
		var datinfo = $("#cartera_aplicar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		var myarray = new Array();
		var a = 0;
		for (i = 0; i < count; i++)
		{
			var currentRow = $('#cartera_aplicar_items_grid').jqxGrid('getrowdata', i);
			if (currentRow.Abono == 0)
				continue;
			
			if (currentRow.Saldo > 0) {
				if (currentRow.Abono > currentRow.Saldo) {
					Alerts_Box("Uno o mas Abonos es Superior a la Deuda.", 3);
					Locked = false;
					return;
				}
			}
			else {
				if (currentRow.Abono > currentRow.Valor) {
					Alerts_Box("Uno o mas Abonos es Superior a la Deuda.", 3);
					Locked = false;
					return;
				}
			}
			
			var array = {};
			array["Interno"] = currentRow.Interno;
			array["Abono"] = parseFloat(currentRow.Abono).toFixed(2);
			if (a==0) {
				array["ClienteID"] = ClienteID.value;
				array["Caja_Interno"] = Caja.value;
				array["Caja_Recibo"] = Caja.label;
				array["Sobrante"] = $('#cartera_aplicar_restante').val();
				array["Total"] = $('#cartera_aplicar_saldo').val();
			}
			myarray[a] = array;
			a++;
		}
		
		if (myarray.length < 1) {
			Alerts_Box("Debe ingresar al menos un Abono para poder Guardar Cambios.", 3);
			Locked = false;
			return;
		}
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			data: {"Cartera_AplicarPago":myarray},
			url: "modulos/guardar.php",
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				ReDefine();
				VOID();
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						Alerts_Box("Datos Guardados con Exito!", 2);
						//Cargar_Clientes();
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
					break;
					
					default:
						Alerts_Box(data[0]["MESSAGE"], 3);
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
	
	$("#cartera_aplicar_guardar").jqxButton({
		width: 150,
		template: "info"
	});
	$("#cartera_aplicar_guardar").bind('click', function ()
	{
		if (ID_Cliente != "")
			Aplicar();
	});
	
	$("#cartera_aplicar_imprimir").jqxButton({
		width: 130,
		template: "warning"
	});
	$("#cartera_aplicar_imprimir").bind('click', function ()
	{
		window.open("imprimir/cartera_estado.php?ClienteID="+$("#cartera_aplicar_cliente_ID").val()+"", "", "width=725, height=600, menubar=no, titlebar=no");
	});

	$("#cartera_aplicar_exportar").jqxButton({
		width: 130,
		template: "success"
	});		
	$("#cartera_aplicar_exportar").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Cartera_AplicarPago=true&ClienteID="+$("#cartera_aplicar_cliente_ID").val();
		//data += "&TipoMovimiento="+$("#movimientos_tipo_mov").val();
		data += "&CobradorID="+$("#cartera_aplicar_cobrador").val();
		//data += "&Interno="+$("#movimientos_interno").val()+"&Factura="+$("#movimientos_factura").val();
		//data += "&Caja_Recibo="+$("#movimientos_recibo").val();
		//
		window.location = data;
	});
	
	$("#cartera_aplicar_enviar").jqxButton({
		width: 130,
		template: "info"
	});
	$("#cartera_aplicar_enviar").bind('click', function ()
	{
		/*$.ajax({
			type:"POST",
			url: "imprimir/cartera_estado.php",
			data: { "ClienteID":$("#cartera_aplicar_cliente_ID").val() },
			async: true,
			success: function(data) 
			{
				
				$.ajax({
					datatype: "json",
					type:"POST",
					url: "modulos/mail_sender.php",
					data: {
						"Email":"kuromomotaro_14@hotmail.com",
						"Message":data,
					},
					success: function(newdata) 
					{
						if (newdata[0]["Success"] == true)
							alert("Enviado!")
						else
							alert("No Enviado!")
					}
				});
			},
			complete: function()
			{
				//
			}
		});*/
	});
	$("#cartera_aplicar_email").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true,
	});
	
	// ------------------------------------------ WINDOWS
	//--- Terceros
	$("#Cartera_Aplicar_Tercero_Window").jqxWindow({
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
	});
	$("#Cartera_Aplicar_Tercero_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"ClienteID":ID_Cliente,
			},
			url: "terceros/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Cartera_Aplicar_Tercero_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	//--- Remision
	$("#Cartera_Aplicar_Remision_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 1030,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Cartera_Aplicar_Remision_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Interno":Interno,
				"Ord_Compra":Orden,
			},
			url: "ventas/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Cartera_Aplicar_Remision_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	if (Guardar == false && Admin == false)
	{
		$("#cartera_aplicar_guardar").jqxButton({ disabled: true });
	}
	
	if (Modificar == false && Admin == false)
	{
		$("#cartera_aplicar_caja_recibo").jqxComboBox({ disabled: true });
	}
	
	if (Imprimir == false && Admin == false)
	{
		$("#cartera_aplicar_imprimir").jqxButton({ disabled: true });
	}
	
	if (Admin == false)
	{
		$("#cartera_aplicar_estado_aldia").jqxButton({ disabled: true });
		$("#cartera_aplicar_estado_juridico").jqxButton({ disabled: true });
		$("#cartera_aplicar_estado_mora").jqxButton({ disabled: true });
		//---
		$("#cartera_aplicar_guardar_cupo").jqxButton({ disabled: true });
		$("#cartera_aplicar_docs_grid").jqxGrid({ disabled: true });
	}
	
	//Load All Clients
	Cargar_Clientes();
	CheckRefresh();
});
</script>
<div id="Cartera_Aplicar_Tercero_Window">
	<div id="Cartera_Aplicar_Tercero_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 340px;">Datos Cliente</div>
	</div>
	<div id="Cartera_Aplicar_Tercero_Content" class="WindowContainer">
	</div>
</div>
<div id="Cartera_Aplicar_Remision_Window">
	<div id="Cartera_Aplicar_Remision_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Cartera_Aplicar_Remision_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="2">
		<tr>
			<td>
				Nombre Cliente
			</td>
			<td colspan="3">
				<div id="cartera_aplicar_cliente"></div>
			</td>
			<td style="width: 75px;">
				Cliente ID
			</td>
			<td>
				<div id="cartera_aplicar_cliente_ID"></div>
			</td>
			<td>
				Cobrador
			</td>
			<td colspan="2">
				<div id="cartera_aplicar_cobrador"></div>
			</td>
		</tr>
		<tr>
			<td>
				Contacto Ppal
			</td>
			<td colspan="3">
				<input type="text" id="cartera_aplicar_contacto"/>
			</td>
			<td>
				Telefono Cp
			</td>
			<td>
				<input type="text" id="cartera_aplicar_telefono"/>
			</td>
			<td>
				<input type="button" id="cartera_aplicar_back" value="<< Ant."/>
			</td>
			<td>
				<input type="button" id="cartera_aplicar_next" value="Sig. >>"/>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Cupo Credito
			</td>
			<td>
				<div id="cartera_aplicar_cupo_credito"></div>
			</td>
			<td>
				Vigencia
			</td>
			<td>
				<div id="cartera_aplicar_vigencia_credito"></div>
			</td>
			<td>
				Activar Cupo
			</td>
			<td>
				<li>
					<div id="cartera_aplicar_cupo_credito_activo"></div>
				</li>
				<li class="parte1_li_txt" style="margin-left:10px;">
					Estado Cuenta
				</li>
			</td>
			<td>
				<input type="text" id="cartera_aplicar_estado" style="text-align:center;"/>
			</td>
			<td colspan="2">
				<li style="margin-left:5px;">
					<input type="button" id="cartera_aplicar_estado_aldia" value="Al Dia"/>
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="cartera_aplicar_estado_mora" value="Mora"/>
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="cartera_aplicar_estado_juridico" value="Juridico"/>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Cupo Adicional
			</td>
			<td>
				<div id="cartera_aplicar_cupo_adicional"></div>
			</td>
			<td>
				Vigencia
			</td>
			<td>
				<div id="cartera_aplicar_vigencia_adicional"></div>
			</td>
			<td colspan="4">
				<input type="button" id="cartera_aplicar_guardar_cupo" value="Actualizar Cupo"/>
			</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="0" style="margin-top:10px;">
		<tr style="text-align:center; ">
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#AAA; padding:4px 0px;">
				Deuda Total
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#CFE6C0; padding:4px 0px;">
				Corriente
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#E6DFC0; padding:4px 0px;">
				30 a 45
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#E6CFC0; padding:4px 0px;">
				45 a 60
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#E2A0A0; padding:4px 0px;">
				60 a 90
			</td>
			<td style="border:1px #888 solid; background:#D45555; padding:4px 0px;">
				M&aacute;s de 90
			</td>
		</tr>
		<tr>
			<td>
				<div id="cartera_aplicar_deuda_total"></div>
			</td>
			<td>
				<div id="cartera_aplicar_corriente"></div>
			</td>
			<td>
				<div id="cartera_aplicar_de_30_a_45"></div>
			</td>
			<td>
				<div id="cartera_aplicar_de_45_a_60"></div>
			</td>
			<td>
				<div id="cartera_aplicar_de_60_a_90"></div>
			</td>
			<td>
				<div id="cartera_aplicar_mas_de_90"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2" style="margin-top:15px;">
	<div id="cartera_aplicar_left" style="float: left; margin-left:0px;">
		<div id="cartera_aplicar_items_grid"></div>
		<div style="width:800px; float: left; margin-top:20px;">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<input type="button" id="cartera_aplicar_guardar" value="Guardar Cambios"/>
					</td>
					<td style="padding-left:15px;">
						<input type="button" id="cartera_aplicar_imprimir" value="Imprimir"/>
					</td>
					<td style="padding-left:15px;">
						<input type="button" id="cartera_aplicar_exportar" value="Exportar"/>
					</td>
					<td style="padding-left:15px;">
						<input type="button" id="cartera_aplicar_enviar" value="Enviar E-Mail"/>
					</td>					
					<td style="padding-left:15px;">
						<input type="text" id="cartera_aplicar_email" />
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div id="cartera_aplicar_right" style="float: left; margin-left:10px;">
		<table style="margin: 0px 0px 10px 0px;" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					Recibo de Caja
				</td>
			</tr>
			<tr>
				<td>
					<div id="cartera_aplicar_caja_recibo"></div>
				</td>
			</tr>
			<tr>
				<td>
					Fecha
				</td>
			</tr>
			<tr>
				<td>
					<div id="cartera_aplicar_fecha"></div>
				</td>
			</tr>
			<tr>
				<td>
					Saldo Disponible
				</td>
			</tr>
			<tr>
				<td>
					<div id="cartera_aplicar_saldo"></div>
				</td>
			</tr>
			<tr>
				<td>
					Total Abonos
				</td>
			
			</tr>
			<tr>
				<td>
					<div id="cartera_aplicar_total_abono"></div>
				</td>
			</tr>
			<tr>
				<td>
					Saldo Restante
				</td>
			</tr>
			<tr>
				<td>
					<div id="cartera_aplicar_restante"></div>
				</td>
			</tr>
			<tr>
				<td>
					<div id="cartera_aplicar_docs_grid"></div>
				</td>
			</tr>
		</table>
	</div>
</div>