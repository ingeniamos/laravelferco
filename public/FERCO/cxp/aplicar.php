<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Caja_Interno = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("CxP_Content");
	var Body = document.getElementById("CxP_Aplicar_Content");
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
				SystemMap("Aplicar Pagos", true);
				
				if ($("#cxp_aplicar_cliente").val() == "")
					Cargar_Clientes();
					
				ReDefine();
				Void();
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
	
	function Void()
	{
		Cargar_Cartera();
		$("#cxp_aplicar_forma_pago").jqxComboBox('clearSelection');
		$("#cxp_aplicar_fuente").jqxComboBox('clearSelection');
		$("#cxp_aplicar_tipo_pago").jqxComboBox('clearSelection');
		$("#cxp_aplicar_consignacion").val("");
		$("#cxp_aplicar_saldo").val("0");
		$("#cxp_aplicar_total_abono").val("0");
		$("#cxp_aplicar_restante").val("0");
		$("#cxp_aplicar_items_grid").jqxGrid("updatebounddata");
	}
	
	function ReDefine()
	{
		ClearJSON = [
			//-1
			{id:"cxp_aplicar_cliente", type:"jqxComboBox"},
			{id:"cxp_aplicar_cliente_ID", type:"jqxComboBox"},
			//
			{id:"cxp_aplicar_deuda", type:"jqxNumberInput"},
			{id:"cxp_aplicar_a30", type:"jqxNumberInput"},
			{id:"cxp_aplicar_a60", type:"jqxNumberInput"},
			{id:"cxp_aplicar_a90", type:"jqxNumberInput"},
			{id:"cxp_aplicar_mas90", type:"jqxNumberInput"},
			//
			{id:"cxp_aplicar_forma_pago", type:"jqxComboBox"},
			{id:"cxp_aplicar_fuente", type:"jqxComboBox"},
			{id:"cxp_aplicar_tipo_pago", type:"jqxComboBox"},
			{id:"cxp_aplicar_consignacion", type:""},
			{id:"cxp_aplicar_saldo", type:"jqxNumberInput"},
			{id:"cxp_aplicar_total_abono", type:"jqxNumberInput"},
			{id:"cxp_aplicar_restante", type:"jqxNumberInput"},
			//-2
			{id:"cxp_aplicar_items_grid", type:"jqxGrid"},
		];
		
		EnableDisableJSON = [
			//-1
			{id:"cxp_aplicar_cliente", type:"jqxComboBox"},
			{id:"cxp_aplicar_cliente_ID", type:"jqxComboBox"},
			//
			{id:"cxp_aplicar_deuda", type:"jqxNumberInput"},
			{id:"cxp_aplicar_a30", type:"jqxNumberInput"},
			{id:"cxp_aplicar_a60", type:"jqxNumberInput"},
			{id:"cxp_aplicar_a90", type:"jqxNumberInput"},
			{id:"cxp_aplicar_mas90", type:"jqxNumberInput"},
			//
			{id:"cxp_aplicar_forma_pago", type:"jqxComboBox"},
			{id:"cxp_aplicar_fuente", type:"jqxComboBox"},
			{id:"cxp_aplicar_tipo_pago", type:"jqxComboBox"},
			{id:"cxp_aplicar_consignacion", type:""},
			{id:"cxp_aplicar_saldo", type:"jqxNumberInput"},
			{id:"cxp_aplicar_total_abono", type:"jqxNumberInput"},
			{id:"cxp_aplicar_restante", type:"jqxNumberInput"},
			//-2
			{id:"cxp_aplicar_items_grid", type:"jqxGrid"},
			{id:"cxp_aplicar_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	
	function ClearDocument()
	{
		// Clean Variables
		Caja_Interno = "";
		Locked = false;
		//
		Cargar_Clientes();
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	}
	
	//******************************************************************************************************//
	//------------------------------------------------------------------------------------------------------//
	//-------------------------------------------- PARTE 1 -------------------------------------------------//
	//------------------------------------------------------------------------------------------------------//
	//******************************************************************************************************//
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
	};
	
	function Cargar_Clientes ()
	{
		ClienteSource.data = {"CxP_GetClients":true};
		var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
		$("#cxp_aplicar_cliente").jqxComboBox({source: ClienteDataAdapter});
		$("#cxp_aplicar_cliente_ID").jqxComboBox({source: ClienteDataAdapter});
	};
	Cargar_Clientes();
	
	$("#cxp_aplicar_cliente").jqxComboBox(
	{
		width: 260,
		height: 20,
		theme: mytheme,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Proveedor',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#cxp_aplicar_cliente").bind('change', function (event) {
		if (event.args)
		{
			$("#cxp_aplicar_cliente_ID").val(event.args.item.value);
		}
		LoadValues();
	});
	
	$("#cxp_aplicar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 115,
		theme: mytheme,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#cxp_aplicar_cliente_ID").bind('change', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#cxp_aplicar_cliente").val(event.args.item.value);
				 LoadValues();
				clearTimeout(Timer1);
			},350);
		}
	});
	/*
	$("#cxp_aplicar_deuda_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 175,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#cxp_aplicar_deuda_vencida").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 175,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	*/
	
	//**********************************************//
	//												//
	//					TABLE 2						//
	//												//
	//**********************************************//
	
	function Cargar_Cartera()
	{
		var ValoresSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Deuda', type: 'decimal'},
				{ name: 'A30', type: 'decimal'},
				{ name: 'A60', type: 'decimal'},
				{ name: 'A90', type: 'decimal'},
				{ name: 'Mas90', type: 'decimal'},
			],
			data: {"CxP_Cargar":$("#cxp_aplicar_cliente_ID").val()},
			url: "modulos/datos.php",
			async: true,
		};
		var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = ValoresDataAdapter.records;
				$("#cxp_aplicar_deuda").val(records[0]["Deuda"]);
				$("#cxp_aplicar_a30").val(records[0]["A30"]);
				$("#cxp_aplicar_a60").val(records[0]["A60"]);
				$("#cxp_aplicar_a90").val(records[0]["A90"]);
				$("#cxp_aplicar_mas90").val(records[0]["Mas90"]);
			}
		});
	};
	
	$("#cxp_aplicar_deuda").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 195,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#cxp_aplicar_a30").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 195,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#cxp_aplicar_a60").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 195,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#cxp_aplicar_a90").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 195,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#cxp_aplicar_mas90").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 195,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	//**********************************************//
	//												//
	//					TABLE 3						//
	//												//
	//**********************************************//
	
	var TipoValues = [
		{"Tipo":"Efectivo"},
		{"Tipo":"Bancos"},
	];
	
	$("#cxp_aplicar_forma_pago").jqxComboBox(
	{
		width: 100,
		height: 20,
		theme: mytheme,
		source: TipoValues,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#cxp_aplicar_forma_pago").bind('change', function (event) {
		if (event.args)
		{
			if (event.args.item.value == "Efectivo")
			{
				$("#cxp_aplicar_tipo_pago").jqxComboBox('clearSelection');
				$("#cxp_aplicar_tipo_pago").jqxComboBox({ disabled: true });
				$("#cxp_aplicar_consignacion").val("");
				$("#cxp_aplicar_consignacion").jqxInput({ disabled: true });
				$("#cxp_aplicar_saldo").val("");
				$("#cxp_aplicar_restante").val("");
				$("#cxp_aplicar_fuente").jqxComboBox('clearSelection');
				$("#cxp_aplicar_fuente").jqxComboBox({ source: EfectivoDataAdapter });
			}
			else
			{
				$("#cxp_aplicar_tipo_pago").jqxComboBox({ disabled: false });
				$("#cxp_aplicar_consignacion").val("");
				$("#cxp_aplicar_consignacion").jqxInput({ disabled: false });
				$("#cxp_aplicar_saldo").val("");
				$("#cxp_aplicar_restante").val("");
				$("#cxp_aplicar_fuente").jqxComboBox('clearSelection');
				$("#cxp_aplicar_fuente").jqxComboBox({ source: BancoDataAdapter });
			}
		}
		else
		{
			$("#cxp_aplicar_tipo_pago").jqxComboBox('clearSelection');
		}
	});
	
	var EfectivoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Banco', type: 'string'},
			{ name: 'Saldo', type: 'decimal'},
		],
		data: {"CxP_Efectivo":true},
		url: "modulos/datos.php",
		async: true
	};
	var EfectivoDataAdapter = new $.jqx.dataAdapter(EfectivoSource);
	
	var BancoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Banco', type: 'string'},
			{ name: 'Saldo', type: 'decimal'},
		],
		data: {"CxP_Bancos":true},
		url: "modulos/datos.php",
		async: true
	};
	var BancoDataAdapter = new $.jqx.dataAdapter(BancoSource);
	
	$("#cxp_aplicar_fuente").jqxComboBox(
	{
		width: 110,
		height: 20,
		theme: mytheme,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Banco',
		valueMember: 'Saldo'
	});
	$("#cxp_aplicar_fuente").bind('change', function (event) {
		if (event.args)
		{
			$("#cxp_aplicar_saldo").val(event.args.item.value);
			$("#cxp_aplicar_restante").val(event.args.item.value);
		}
		else
		{
			$("#cxp_aplicar_fuente").jqxComboBox('clearSelection');
			$("#cxp_aplicar_saldo").val("");
			$("#cxp_aplicar_restante").val("");
		}
	});
	
	var TipoValues = [
		{"Tipo":"Transferencia"},
		{"Tipo":"Cheque"},
	];
	
	$("#cxp_aplicar_tipo_pago").jqxComboBox(
	{
		width: 105,
		height: 20,
		theme: mytheme,
		source: TipoValues,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	
	$("#cxp_aplicar_consignacion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 90
	});
	
	$("#cxp_aplicar_saldo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 175,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	$("#cxp_aplicar_saldo").on('valueChanged', function (event) {
		//var value = event.args.value;
		//AutoPagarFacturas();
	});
	
	$("#cxp_aplicar_total_abono").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 174,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	$("#cxp_aplicar_restante").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 174,
		symbol: '$',
		disabled: true,
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
	});
	
	//******************************************************************************************************//
	//------------------------------------------------------------------------------------------------------//
	//-------------------------------------------- PARTE 2 -------------------------------------------------//
	//------------------------------------------------------------------------------------------------------//
	//******************************************************************************************************//
	
	function Do_TheMath ()
	{
		var Total_Slado = $("#cxp_aplicar_saldo").val();
		var Total_Abono = 0;
		var datinfo = $("#cxp_aplicar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		if (count <= 0) {
			return;
		}
		for (i=0; i<count; i++)
		{
			var currentRow = $("#cxp_aplicar_items_grid").jqxGrid('getrowdata', i);
			Total_Abono = Total_Abono + currentRow.Abono;
		}
		
		$("#cxp_aplicar_total_abono").val(Total_Abono);
		$("#cxp_aplicar_restante").val(Total_Slado - Total_Abono);
	};
	
	function AutoPagarFacturas()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{
			clearTimeout(Timer1);

			var datinfo = $("#cxp_aplicar_items_grid").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			for (var a = 0; a < count; a++)
			{
				$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", a, "Apply", false);
			}
			for (var b = 0; b < count; b++)
			{
				if ($("#cxp_aplicar_restante").val() > 0)
				{
					$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", b, "Apply", true);
				}
			}
		},100);
	};
	
	function LoadValues()
	{
		Cargar_Cartera();
		
		//Items Grid
		ItemsSource.data = {"CxP_AplicarPago": $("#cxp_aplicar_cliente_ID").val()};
		ItemsAdapter = new $.jqx.dataAdapter(ItemsSource);
		$("#cxp_aplicar_items_grid").jqxGrid({source: ItemsAdapter});
	};
	
	var ItemsSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Apply', type: 'bool'},
			{ name: 'Fecha', type: 'date'},
			{ name: 'Interno', type: 'string'},
			{ name: 'Entrada', type: 'string'},
			{ name: 'Factura', type: 'string'},
			{ name: 'Doc_Transp', type: 'string'},
			{ name: 'Valor', type: 'decimal'},
			{ name: 'Saldo', type: 'decimal'},
			{ name: 'Abono', type: 'decimal'},
			{ name: 'Saldo_Pendiente', type: 'decimal'},
			{ name: 'Digitador', type: 'string'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#cxp_aplicar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
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
			{ text: '', datafield: 'Apply', columntype: 'checkbox', width: '3%', pinned: true, editable: Admin ? true:Modificar },
			{
				text: 'Fecha',
				datafield: 'Fecha',
				editable: false,
				width: '9%',
				height: 20,
				pinned: true,
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Interno', datafield: 'Interno', editable: false, width: '9%', height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Entrada', datafield: 'Entrada', editable: false, width: '9%', height: 20, pinned: true, cellsalign: 'caja_crear_right' },
			{ text: 'Factura', datafield: 'Factura', editable: false, width: '9%', height: 20, pinned: true, },
			{ text: 'Doc. Transp.', datafield: 'Doc_Transp', editable: false, width: '9%', height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: false,
				width: '11%',
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
				width: '11%',
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
				width: '11%',
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				},
			},
			{
				text: 'Saldo Nuevo',
				datafield: 'Saldo_Pendiente',
				editable: false,
				width: '13%',
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
						
					return "<div style='margin: 4px; text-align:right;' class='jqx-caja_crear_right-align'>" + ItemsAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
			{ text: 'Digitador', datafield: 'Digitador', editable: false, width: '6%', height: 20 },
		]
	});
	$("#cxp_aplicar_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#cxp_aplicar_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Entrada")
		{
			Interno = $("#cxp_aplicar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Interno");
			if (Interno != "")
				$("#Compras_Autorizar_Compra_Window").jqxWindow('open');
			//alert("Muy Pronto, Ventana de Compra!")
			//$("#CxP_Aplicar_Compra_Window").jqxWindow('open');
		}
	});
	
	$("#cxp_aplicar_items_grid").bind('cellvaluechanged', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.newvalue;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Abono") {
			if (value > 0) {
				if ($("#cxp_aplicar_total_abono").val() <= $("#cxp_aplicar_saldo").val())
				{
					var total_disponible = $("#cxp_aplicar_saldo").val() - $("#cxp_aplicar_total_abono").val();
					var Valor = $("#cxp_aplicar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Valor");
					var Saldo = $("#cxp_aplicar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Saldo");
					var total_restante = 0;

					if (value > total_disponible)
					{
						if (total_disponible == 0) {
							if (value > oldvalue)
							{
								Alerts_Box("El Valor ingresado es Mayor al Disponible!", 3);
								$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
							}
							else
							{
								Do_TheMath();
							}
						}
						else {
							if ((total_disponible + oldvalue) != value) {
								Alerts_Box("El Valor ingresado es Mayor al Disponible!", 3);
								$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
							}
							else
							{
								if (Saldo > 0) {
									if (value <= Saldo) {
										Do_TheMath();
									}
									else {
										Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
										$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
									}
								}
								else {
									if (value <= Valor) {
										Do_TheMath();
									}
									else {
										Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
										$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
									}
								}
							}
						}
					}
					else {
						if (Saldo > 0) {
							if (value <= Saldo) {
								total_restante = total_disponible - value;
								$("#cxp_aplicar_total_abono").val($("#cxp_aplicar_total_abono").val() + value);
								$("#cxp_aplicar_restante").val(total_restante);
							}
							else {
								Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
								$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
							}
						}
						else {
							if (value <= Valor) {
								total_restante = total_disponible - value;
								$("#cxp_aplicar_total_abono").val($("#cxp_aplicar_total_abono").val() + value);
								$("#cxp_aplicar_restante").val(total_restante);
							}
							else {
								Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
								$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
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
				$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
			}
		}	
		else if (datafield == "Apply") {
			if (value == true) {
				if ($("#cxp_aplicar_total_abono").val() < $("#cxp_aplicar_saldo").val())
				{
					var total_disponible = $("#cxp_aplicar_saldo").val() - $("#cxp_aplicar_total_abono").val();
					var Valor = $("#cxp_aplicar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Valor");
					var Saldo = $("#cxp_aplicar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Saldo");
					var Abono = $("#cxp_aplicar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Abono");
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
						$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
					}
					$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", total_add);
				}
				else {
					Alerts_Box("Ya ha utilizado todo el saldo disponible!", 3);
					$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Apply", false);
				}
			}
			else {
				$("#cxp_aplicar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Abono", 0);
			}
		}
	});
	
	function Aplicar()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var datinfo = $("#cxp_aplicar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		var myarray = new Array();
		var a = 0;
		for (i = 0; i < count; i++)
		{
			var currentRow = $("#cxp_aplicar_items_grid").jqxGrid('getrowdata', i);
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
			array["Entrada"] = currentRow.Entrada;
			array["Abono"] = currentRow.Abono;
			if (a==0) {
				var item = $("#cxp_aplicar_fuente").jqxComboBox('getSelectedItem');
				array["ClienteID"] = $("#cxp_aplicar_cliente_ID").val();
				array["Forma_Pago"] = $("#cxp_aplicar_forma_pago").val();
				array["Fuente"] = item.label;
				array["Tipo"] = $("#cxp_aplicar_tipo_pago").val();
				array["Consignacion"] = $("#cxp_aplicar_consignacion").val();
				array["Total"] = $("#cxp_aplicar_total_abono").val();
			}
			myarray[a] = array;
			a++;
		}
		
		if (myarray.length <= 0) {
			Alerts_Box("Debe ingresar al menos un Abono para poder Guardar Cambios.", 3);
			Locked = false;
			return;
		}
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'json',
			data: {"CxP_AplicarPago":myarray},
			url: "modulos/guardar.php",
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Caja_Interno = data[0]["Caja_Interno"];
				Alerts_Box("Datos Guardados con Exito!", 2);
				EnableDisableAll(true);
				Locked = false;
				Timer1 = setInterval(function()
				{
					if (ClickOK == true)
					{
						clearInterval(Timer1);
						Locked	= false;
						EnableDisableAll(false);
						Void();
						$("#CxP_AplicarPago_Caja_Window").jqxWindow('open');
					}
				},10);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar caja_crear_guardar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	};
	
	$("#cxp_aplicar_guardar").jqxButton({
		width: 150,
		template: "success"
	});
	$("#cxp_aplicar_guardar").bind('click', function ()
	{
		if ($("#cxp_aplicar_cliente_ID").val() != "")
			Aplicar();
	});
	
	$("#cxp_aplicar_imprimir").jqxButton({
		width: 150,
		template: "warning"
	});
	$("#cxp_aplicar_imprimir").bind('click', function ()
	{
		window.open("imprimir/cxp_aplicar.php?ClienteID="+$("#cxp_aplicar_cliente_ID").val()+"", "", "width=825, height=600, menubar=no, titlebar=no");
	});
	
	// ------------------------------------------ WINDOWS
	//--- Caja Recibo
	$("#CxP_AplicarPago_Caja_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 1020,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#CxP_AplicarPago_Caja_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Caja_Interno":Caja_Interno,
			},
			url: "caja/crear.php",
			async: true,
			success: function(data) 
			{
				$("#CxP_AplicarPago_Caja_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	$("#CxP_AplicarPago_Caja_Window").on('close', function (event)
	{
		Caja_Interno = "";
	});
	
// ------------------------------------------ WINDOWS
	//--- Compra
	$("#Compras_Autorizar_Compra_Window").jqxWindow({
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
	$("#Compras_Autorizar_Compra_Window").on('open', function (event) {
		$("#Loading").show();
			$.ajax({
				type:"POST",
				data: {
					"Crear_Emergente":true,
					"Interno":Interno,
				},
				url: "compras/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Compras_Autorizar_Compra_Content").html(data);
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
	});	
	
	CheckRefresh();
});
</script>
<div id="CxP_AplicarPago_Caja_Window">
	<div id="CxP_AplicarPago_Caja_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Recibo de CAJA</div>
	</div>
	<div id="CxP_AplicarPago_Caja_Content" class="WindowContainer">
	</div>
</div>
<div id="Compras_Autorizar_Compra_Window">
	<div id="Compras_Autorizar_Compra_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Compras_Autorizar_Compra_Content" class="WindowContainer">
	</div>
</div>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="2">
		<tr>
			<td>
				Proveedor
			</td>
			<td>
				<div id="cxp_aplicar_cliente"></div>
			</td>
			<td>
				Proveedor ID
			</td>
			<td>
				<div id="cxp_aplicar_cliente_ID"></div>
			</td>
			<!--<td>
				Deuda Total
			</td>
			<td>
				<div id="cxp_aplicar_deuda_total"></div>
			</td>
			<td>
				Vencida
			</td>
			<td>
				<div id="cxp_aplicar_deuda_vencida"></div>
			</td>-->
		</tr>
	</table>
	<table cellpadding="0" cellspacing="0" style="margin-top:15px;">
		<tr style="text-align:center; ">
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#AAA; padding:4px 0px;">
				Deuda Total
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#CFE6C0; padding:4px 0px;">
				30 Dias
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#E6DFC0; padding:4px 0px;">
				60 Dias
			</td>
			<td style="border-bottom:1px #888 solid; border-left:1px #888 solid; border-top:1px #888 solid; background:#E2A0A0; padding:4px 0px;">
				90 Dias
			</td>
			<td style="border:1px #888 solid; background:#D45555; padding:4px 0px;">
				M&aacute;s de 90 Dias
			</td>
		</tr>
		<tr>
			<td style="padding: 0px 1px; 0px 2px">
				<div id="cxp_aplicar_deuda"></div>
			</td>
			<td style="padding: 0px 1px;">
				<div id="cxp_aplicar_a30"></div>
			</td>
			<td style="padding: 0px 1px;">
				<div id="cxp_aplicar_a60"></div>
			</td>
			<td style="padding: 0px 1px;">
				<div id="cxp_aplicar_a90"></div>
			</td>
			<td style="padding: 0px 2px; 0px 1px">
				<div id="cxp_aplicar_mas90"></div>
			</td>
		</tr>
	</table>
	<table cellpadding="4" cellspacing="0" style="margin-top:20px; border: 1px solid #A4BED4; text-align: center;">
		<tr style="background: #E0E9F5">
			<td style="border-bottom: 1px solid #A4BED4;">
				Forma de Pago
			</td>
			<td style="border-bottom: 1px solid #A4BED4;">
				Fuente
			</td>
			<td style="border-bottom: 1px solid #A4BED4;">
				Tipo
			</td>
			<td style="border-bottom: 1px solid #A4BED4;">
				# Documento
			</td>
			<td style="border-bottom: 1px solid #A4BED4;">
				Saldo Disponible
			</td>
			<td style="border-bottom: 1px solid #A4BED4;">
				Total Abonos
			</td>
			<td style="border-bottom: 1px solid #A4BED4;">
				Saldo Restante
			</td>
		</tr>
		<tr>
			<td>
				<div id="cxp_aplicar_forma_pago"></div>
			</td>
			<td>
				<div id="cxp_aplicar_fuente"></div>
			</td>
			<td>
				<div id="cxp_aplicar_tipo_pago"></div>
			</td>
			<td>
				<input type="text" id="cxp_aplicar_consignacion"/>
			</td>
			<td>
				<div id="cxp_aplicar_saldo"></div>
			</td>
			<td>
				<div id="cxp_aplicar_total_abono"></div>
			</td>
			<td>
				<div id="cxp_aplicar_restante"></div>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2" style="margin-top:15px;">
	<div id="cxp_aplicar_items_grid"></div>
	<table cellpadding="0" cellspacing="0" style="margin-top:20px;">
		<tr>
			<td>
				<input type="button" id="cxp_aplicar_guardar" value="Aplicar Pagos"/>
			</td>
			<td style="padding-left:15px;">
				<input type="button" id="cxp_aplicar_imprimir" value="Imprimir"/>
			</td>
		</tr>
	</table>
</div>