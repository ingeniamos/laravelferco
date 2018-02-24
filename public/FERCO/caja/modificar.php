<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// GLOBALS
	var mytheme = "energyblue";
	var Locked = false;
	var ComboboxLock = false;
	var CxP = "";
	var Grupo = "";
	var SubGrupo = "";
	var SubGrupo2 = "";
	var Caja_Interno = "";
	var Caja_Recibo = "";
	var Active = true;
	var Total = 0;
	var Maquinaria = false;
	var Timer1 = 0;
	var Timer2 = 0;
	var RowAdded = true;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Caja_Content");
	var Body = document.getElementById("ModificarRC_Content");
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
				// Actualizar Ordenes
				ReciboSource.data =  {"Caja_Modificar":true},
				ReciboDataAdapter = new $.jqx.dataAdapter(ReciboSource);
				$("#caja_modificar_recibo").jqxComboBox({source: ReciboDataAdapter});
				
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
		
		if ($data[0]["Lvl"] != "Administrador")
		{
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				else if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Imprimir"] == "true")
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
	
	function ClearFields () {
		$("#caja_modificar_d_prov_valor").val('');
		$("#caja_modificar_recaudo_efectivo").val('');
		$("#caja_modificar_transferencias").val('');
		$("#caja_modificar_subtotal_cheques").val('');
		//$("#caja_modificar_items_grid1").jqxGrid('clear');
		//$("#caja_modificar_items_grid2").jqxGrid('clear');
		$("#caja_modificar_efectivo").val('');
		$("#caja_modificar_rete_iva").val('');
		$("#caja_modificar_rete_iva_total").val('');
		$("#caja_modificar_rete_ica").val('');
		$("#caja_modificar_rete_ica_total").val('');
		$("#caja_modificar_rete_fuente").val('');
		$("#caja_modificar_rete_fuente_total").val('');
		$("#caja_modificar_otro_dcto").val('');
		$("#caja_modificar_total").val('');
	};
	
	function ReDefine()
	{
		ClearJSON = [
			//--- Parte 1
			{id:"caja_modificar_caja_interno", type:""},
			{id:"caja_modificar_recibo", type:"jqxComboBox"},
			{id:"caja_modificar_d_prov", type:"jqxComboBox"},
			{id:"caja_modificar_tipo_mov", type:"jqxComboBox"},
			{id:"caja_modificar_grupo", type:"jqxComboBox"},
			{id:"caja_modificar_subgrupo", type:"jqxComboBox"},
			{id:"caja_modificar_subgrupo2", type:"jqxComboBox"},
			{id:"caja_modificar_cliente", type:"jqxComboBox"},
			{id:"caja_modificar_cliente_ID", type:"jqxComboBox"},
			{id:"caja_modificar_d_prov_valor", type:""},
			{id:"caja_modificar_estado", type:""},
			{id:"caja_modificar_digitado_por", type:""},
			//--- Parte 2
			//Left
			{id:"caja_modificar_recaudo_efectivo", type:""},
			{id:"caja_modificar_transferencias", type:""},
			{id:"caja_modificar_subtotal_cheques", type:""},
			{id:"caja_modificar_efectivo", type:""},
			{id:"caja_modificar_rete_iva", type:""},
			{id:"caja_modificar_rete_iva_total", type:""},
			{id:"caja_modificar_rete_ica", type:""},
			{id:"caja_modificar_rete_ica_total", type:""},
			{id:"caja_modificar_rete_fuente", type:""},
			{id:"caja_modificar_rete_fuente_total", type:""},
			{id:"caja_modificar_otro_dcto", type:""},
			{id:"caja_modificar_dcto_concepto", type:"jqxComboBox"},
			{id:"caja_modificar_total", type:""},
			//Right Top
			{id:"caja_modificar_tipo", type:"jqxComboBox"},
			{id:"caja_modificar_valor", type:""},
			{id:"caja_modificar_num_consignacion", type:""},
			{id:"caja_modificar_banco", type:"jqxComboBox"},
			{id:"caja_modificar_items_grid1", type:"jqxGrid"},
			//Right Bottom
			{id:"caja_modificar_num_cheque", type:""},
			{id:"caja_modificar_valor_cheque", type:""},
			{id:"caja_modificar_banco_cheque", type:"jqxComboBox"},
			{id:"caja_modificar_num_cuenta", type:""},
			{id:"caja_modificar_estado_fecha", type:"jqxComboBox"},
			{id:"caja_modificar_items_grid2", type:"jqxGrid"},
			//--- Parte 3
			{id:"caja_modificar_aplicado_a", type:""},
			{id:"caja_modificar_observaciones", type:""},
		];
		
		EnableDisableJSON = [
			//--- Parte 1
			//{id:"caja_modificar_recibo", type:"jqxComboBox"},
			{id:"caja_modificar_tipo_mov", type:"jqxComboBox"},
			{id:"caja_modificar_grupo", type:"jqxComboBox"},
			{id:"caja_modificar_subgrupo", type:"jqxComboBox"},
			{id:"caja_modificar_subgrupo2", type:"jqxComboBox"},
			{id:"caja_modificar_fecha", type:"jqxDateTimeInput"},
			{id:"caja_modificar_cliente", type:"jqxComboBox"},
			{id:"caja_modificar_cliente_ID", type:"jqxComboBox"},
			//--- Parte 2
			//Left
			{id:"caja_modificar_recaudo_efectivo", type:"jqxNumberInput"},
			{id:"caja_modificar_rete_iva", type:"jqxNumberInput"},
			{id:"caja_modificar_rete_iva_total", type:"jqxNumberInput"},
			{id:"caja_modificar_rete_ica", type:"jqxNumberInput"},
			{id:"caja_modificar_rete_ica_total", type:"jqxNumberInput"},
			{id:"caja_modificar_rete_fuente", type:"jqxNumberInput"},
			{id:"caja_modificar_rete_fuente_total", type:"jqxNumberInput"},
			{id:"caja_modificar_otro_dcto", type:"jqxNumberInput"},
			{id:"caja_modificar_dcto_concepto", type:"jqxComboBox"},
			//{id:"caja_modificar_total", type:"jqxNumberInput"},
			//Right Top
			{id:"caja_modificar_tipo", type:"jqxComboBox"},
			{id:"caja_modificar_valor", type:"jqxNumberInput"},
			{id:"caja_modificar_num_consignacion", type:""},
			{id:"caja_modificar_banco", type:"jqxComboBox"},
			{id:"caja_modificar_addrowbutton1", type:"jqxButton"},
			{id:"caja_modificar_deleterowbutton1", type:"jqxButton"},
			//Right Bottom
			{id:"caja_modificar_num_cheque", type:""},
			{id:"caja_modificar_valor_cheque", type:"jqxNumberInput"},
			{id:"caja_modificar_banco_cheque", type:"jqxComboBox"},
			{id:"caja_modificar_num_cuenta", type:""},
			{id:"caja_modificar_estado_fecha", type:"jqxComboBox"},
			{id:"caja_modificar_addrowbutton2", type:"jqxButton"},
			{id:"caja_modificar_deleterowbutton2", type:"jqxButton"},
			//--- Parte 3
			{id:"caja_modificar_aplicado_a", type:""},
			{id:"caja_modificar_observaciones", type:""},
			{id:"caja_modificar_guardar", type:"jqxButton"},
			//{id:"caja_modificar_imprimir", type:"jqxButton"},
			{id:"caja_modificar_categorias", type:"jqxButton"},
			{id:"caja_modificar_crear_tercero", type:"jqxButton"},
			//{id:"caja_modificar_impresora", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		ReDefine();
		// Clean Variables
		Locked = false;
		CxP = "";
		Grupo = "";
		SubGrupo = "";
		SubGrupo2 = "";
		Caja_Interno = "";
		Caja_Recibo = "";
		Active = true;
		Total = 0;
		Maquinaria = false;
		Timer1 = 0;
		Timer2 = 0;
		RowAdded = true;
		//
		document.getElementById("ok").value = "Aceptar";
		document.getElementById("cancel").value = "Cancelar";
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		if (!Admin && !Guardar)
		{
			$("#caja_modificar_guardar").jqxButton({ disabled: true });
			$("#caja_modificar_nuevo").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#caja_modificar_imprimir").jqxButton({ disabled: true });
		}
	}
	
	//---------------------------------------------- PARTE 1 ----------------------------------------------\\
	
	function LoadValues ()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Fecha', type: 'string'},
				{ name: 'Caja_Interno', type: 'string'},
				{ name: 'CxP', type: 'string'},
				{ name: 'Categoria', type: 'string' },
				{ name: 'Grupo', type: 'string' },
				{ name: 'SubGrupo', type: 'string' },
				{ name: 'SubGrupo2', type: 'string'},
				{ name: 'ClienteID', type: 'string'},
				{ name: 'Aplicado_A', type: 'string' },
				{ name: 'Observaciones', type: 'string' },
				{ name: 'Efectivo', type: 'decimal' },
				{ name: 'Rete_IVA', type: 'decimal' },
				{ name: 'Rete_ICA', type: 'decimal' },
				{ name: 'Rete_Fuente', type: 'decimal' },
				{ name: 'Descuento', type: 'string' },
				{ name: 'Descuento_Concepto', type: 'string'},
				{ name: 'Estado', type: 'string'},
				{ name: 'Digitado_Por', type: 'string'},
				//--
				{ name: 'Bancos', type: 'string'},
				{ name: 'Cheques', type: 'string' },
			],
			data:{
				"Datos_Caja":true,
				"Caja_Interno":Caja_Interno,
			},
			url: "modulos/datos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				/*
				if (records[0]["Estado"] == "Anulado")
					EnableDisableAll(true);
				else
					EnableDisableAll(false);
				*/
				
				if (records[0]["Caja_Interno"] == undefined)
					return;
				
				//$("#caja_modificar_recibo").val(records[0]["Caja_Recibo"]);
				$("#caja_modificar_caja_interno").val(records[0]["Caja_Interno"]);
				$("#caja_modificar_fecha").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#caja_modificar_tipo_mov").jqxComboBox('selectItem', records[0]["Categoria"]);
				$("#caja_modificar_grupo").jqxComboBox({ disabled: false});
				$("#caja_modificar_subgrupo").jqxComboBox({ disabled: false});
				$("#caja_modificar_subgrupo2").jqxComboBox({ disabled: false});
				Grupo = records[0]["Grupo"];
				SubGrupo = records[0]["SubGrupo"];
				SubGrupo2 = records[0]["SubGrupo2"];
				
				CxP = records[0]["CxP"];
				$("#caja_modificar_cliente").jqxComboBox('selectItem', records[0]["ClienteID"]);
				//$("#caja_modificar_cliente_ID").jqxComboBox('selectItem', records[0]["ClienteID"]);
				$("#caja_modificar_aplicado_a").val(records[0]["Aplicado_A"]);
				$("#caja_modificar_observaciones").val(records[0]["Observaciones"]);
				$("#caja_modificar_recaudo_efectivo").val(records[0]["Efectivo"]);
				$("#caja_modificar_rete_iva_total").val(records[0]["Rete_IVA"]);
				$("#caja_modificar_rete_ica_total").val(records[0]["Rete_ICA"]);
				$("#caja_modificar_rete_fuente_total").val(records[0]["Rete_Fuente"]);
				$("#caja_modificar_otro_dcto").val(records[0]["Descuento"]);
				$("#caja_modificar_dcto_concepto").val(records[0]["Descuento_Concepto"]);
				$("#caja_modificar_estado").val(records[0]["Estado"]);
				$("#caja_modificar_digitado_por").val(records[0]["Digitado_Por"]);
				
				//---
				var len1 = records[0]["Bancos"].length;
				for (var i = 0; i < len1; i++)
				{
					if (records[0]["Bancos"][i]["Valor"] != undefined)
					{
						
						var datarow = [{
							"Tipo":records[0]["Bancos"][i]["Tipo"],
							"Valor":parseFloat(records[0]["Bancos"][i]["Valor"]),
							"Consignacion":records[0]["Bancos"][i]["Numero"],
							"Banco":records[0]["Bancos"][i]["Banco"],
						}];
						$("#caja_modificar_items_grid1").jqxGrid("addrow", null, datarow, "first");
					}
				}
				
				var len2 = records[0]["Cheques"].length;
				for (var i = 0; i < len2; i++)
				{
					if (records[0]["Cheques"][i]["Valor"] != undefined)
					{
						var datarow = [{
							"NumCheque":records[0]["Cheques"][i]["Cheque"],
							"Valor":parseFloat(records[0]["Cheques"][i]["Valor"]),
							"NumBanco":records[0]["Cheques"][i]["Banco"],
							"NumCuenta":records[0]["Cheques"][i]["Cuenta"],
							"EstadoFecha":records[0]["Cheques"][i]["Estado"],
							"FechaCheque":records[0]["Cheques"][i]["Fecha"],
						}];
						$("#caja_modificar_items_grid2").jqxGrid("addrow", null, datarow, "first");
					}
				}
				Calcular();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
			downloadComplete: function(edata, textStatus, jqXHR) {
				//alert("Data is Loaded");
			}
		});
	};
	
	function Cargar_CxP(ID1, ID2)
	{
		if (RowAdded === false)
		{
			RowAdded = true;
			return;
		}
		
		RowAdded = false;
		
		var BoxSource = 
		{
			datatype: "json",
			datafields:
			[
				{ name: 'Saldo', type: 'decimal' },
				{ name: 'Grupo', type: 'string' },
				{ name: 'SubGrupo', type: 'string' },
				{ name: 'SubGrupo2', type: 'string' },
			],
			data: {
				"CxP_Datos":true,
				"Factura":ID1,
				"Interno":ID2
			},
			url: "modulos/datos.php",
		};
		var BoxDataAdapter = new $.jqx.dataAdapter(BoxSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var myrecords = BoxDataAdapter.records;
				
				$("#caja_modificar_recaudo_efectivo").val(myrecords[0]["Saldo"]);
				$("#caja_modificar_d_prov_valor").val(myrecords[0]["Saldo"]);
				$("#caja_modificar_grupo").val(myrecords[0]["Grupo"]);
				SubGrupo = myrecords[0]["SubGrupo"];
				SubGrupo2 = myrecords[0]["SubGrupo2"];
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	$("#caja_modificar_caja_interno").jqxInput({
		theme: mytheme,
		height: 20,
		width: 170,
		disabled: true
	});
	
	var ReciboSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Caja_Recibo', type: 'string'},
			{ name: 'Caja_Interno', type: 'string'},
		],
		url: "modulos/datos.php",
	};
	
	$("#caja_modificar_recibo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 170,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Recibo',
		selectedIndex: -1,
		displayMember: 'Caja_Recibo',
		valueMember: 'Caja_Interno'
	});
	$("#caja_modificar_recibo").bind('change', function (event)
	{
		if (event.args)
		{
			if (Caja_Interno == event.args.item.value)
				return;
			
			Caja_Recibo = event.args.item.label;
			Caja_Interno = event.args.item.value;
			ComboboxLock = true;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (ClickOK == true) {
					EnableDisableAll(false);
					ClickOK = false;
				}
				$("#caja_modificar_recibo").jqxComboBox("close");
				ClearAll("caja_modificar_recibo");
				LoadValues();
			},500);
		}
		else
		{
			var item_value = $("#caja_modificar_recibo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				if (ComboboxLock == true)
				{
					ComboboxLock = false;
					return;
				}
				$("#caja_modificar_recibo").jqxComboBox("close");
				ClearDocument();
			}
			else
			{
				var value = $("#caja_modificar_recibo").val();
				var item = $("#caja_modificar_recibo").jqxComboBox('getItems');
				
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#caja_modificar_recibo").jqxComboBox('selectItem', item[i].value);
						$("#caja_modificar_recibo").jqxComboBox("close");
						return;
					}
				}
				/*Caja_Recibo = "";
				Caja_Interno = "";
				$("#caja_modificar_recibo").jqxComboBox('clearSelection');*/
				$("#caja_modificar_recibo").jqxComboBox("close");
				ClearDocument();
			}
		}
	});
	$("#caja_modificar_recibo").bind('bindingComplete', function (event) {
		if (Caja_Interno != "")
			$("#caja_modificar_recibo").jqxComboBox('selectItem', Caja_Interno);
			//$("#caja_modificar_recibo").val(Caja_Interno);
	});
	ReciboSource.data =  {"Caja_Modificar":true},
	ReciboDataAdapter = new $.jqx.dataAdapter(ReciboSource);
	$("#caja_modificar_recibo").jqxComboBox({source: ReciboDataAdapter});
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Categoria', type: 'string'}
		],
		data: {"Caja_Categoria":true},
		url: "modulos/parametros.php",
		async: true
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#caja_modificar_tipo_mov").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		source: CategoriaDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Categoria',
		valueMember: 'Categoria'
	});
	$("#caja_modificar_tipo_mov").bind('change', function (event) {
		if (event.args)
		{
			$("#caja_modificar_grupo").jqxComboBox({ disabled: false });
			GrupoSource.data = {"Caja_Grupo": event.args.item.value};
			var GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#caja_modificar_grupo").jqxComboBox({source: GrupoAdapter});
			
			if (event.args.item.value == "Egresos")
			{
				$("#caja_modificar_tipo_change").html("Valor a Pagar");
				Active = false;
				$("#caja_modificar_rete_iva").jqxNumberInput({ disabled: false});
				$("#caja_modificar_rete_ica").jqxNumberInput({ disabled: false});
				$("#caja_modificar_rete_fuente").jqxNumberInput({ disabled: false});
				$("#caja_modificar_rete_iva_total").jqxNumberInput({ disabled: true});
				$("#caja_modificar_rete_ica_total").jqxNumberInput({ disabled: true});
				$("#caja_modificar_rete_fuente_total").jqxNumberInput({ disabled: true});
				//$("#caja_modificar_recaudo_efectivo").jqxNumberInput({ disabled: true});
				$("#caja_modificar_d_prov").jqxComboBox({ disabled: false});
			}
			else
			{
				$("#caja_modificar_tipo_change").html("Recaudo en Efectivo");
				Active = true;
				$("#caja_modificar_rete_iva").jqxNumberInput({ disabled: true});
				$("#caja_modificar_rete_ica").jqxNumberInput({ disabled: true});
				$("#caja_modificar_rete_fuente").jqxNumberInput({ disabled: true});
				$("#caja_modificar_rete_iva_total").jqxNumberInput({ disabled: false});
				$("#caja_modificar_rete_ica_total").jqxNumberInput({ disabled: false});
				$("#caja_modificar_rete_fuente_total").jqxNumberInput({ disabled: false});
				//$("#caja_modificar_recaudo_efectivo").jqxNumberInput({ disabled: false});
				$("#caja_modificar_d_prov").jqxComboBox({ disabled: true});
			}
		}
		else
			$("#caja_modificar_tipo_mov").jqxComboBox('clearSelection');
	});
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'}
		],
		data: {"Caja_Grupo":true},
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#caja_modificar_grupo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Grupo',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
		disabled: true
	});
	$("#caja_modificar_grupo").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#caja_modificar_subgrupo").jqxComboBox({ disabled: false});
				$("#caja_modificar_subgrupo2").jqxComboBox({ disabled: false});
				SubGrupoSource.data = {"Caja_SubGrupo": event.args.item.value};
				SubGrupo2Source.data = {"Caja_SubGrupo2": event.args.item.value};
				SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				SubGrupo2DataAdapter = new $.jqx.dataAdapter(SubGrupo2Source);
				$("#caja_modificar_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
				$("#caja_modificar_subgrupo2").jqxComboBox({source: SubGrupo2DataAdapter});
			},350);
		}
		else
		{
			$("#caja_modificar_grupo").jqxComboBox('clearSelection');
			$("#caja_modificar_subgrupo").jqxComboBox('clearSelection');
			$("#caja_modificar_subgrupo2").jqxComboBox('clearSelection');
		}
	});
	$("#caja_modificar_grupo").bind('bindingComplete', function (event)
	{
		if (Grupo != "")
		{
			$("#caja_modificar_grupo").jqxComboBox('selectItem', Grupo);
			Grupo = "";
		}
	});
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo', type: 'string'}
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#caja_modificar_subgrupo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar SubGrupo',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
		disabled: true
	});
	$("#caja_modificar_subgrupo").bind('change', function (event) {
		if (!event.args)
			$("#caja_modificar_subgrupo").jqxComboBox('clearSelection');
	});
	$("#caja_modificar_subgrupo").bind('bindingComplete', function (event)
	{
		if (SubGrupo != "")
		{
			$("#caja_modificar_subgrupo").jqxComboBox('selectItem', SubGrupo);
			SubGrupo = "";
		}
	});
	
	var SubGrupo2Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo2', type: 'string'}
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#caja_modificar_subgrupo2").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar SubGrupo 2',
		selectedIndex: -1,
		displayMember: 'SubGrupo2',
		valueMember: 'SubGrupo2',
		disabled: true
	});
	$("#caja_modificar_subgrupo2").bind('change', function (event) {
		if (!event.args)
			$("#caja_modificar_subgrupo2").jqxComboBox('clearSelection');
	});
	$("#caja_modificar_subgrupo2").bind('bindingComplete', function (event)
	{
		if (SubGrupo2 != "")
		{
			$("#caja_modificar_subgrupo2").jqxComboBox('selectItem', SubGrupo2);
			SubGrupo2 = "";
		}
	});
	
	$("#caja_modificar_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 170,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
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
	
	$("#caja_modificar_cliente").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 509,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#caja_modificar_cliente").bind('change', function (event) {
		if (event.args)
		{
			if ($("#caja_modificar_cliente_ID").val() != event.args.item.value)
			{
				clearTimeout(Timer2);
				Timer2 = setTimeout(function()
				{
					$("#caja_modificar_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
					CxPSource.data = {"CxP_Deudor": event.args.item.value};
					var CxPDataAdapter = new $.jqx.dataAdapter(CxPSource);
					$("#caja_modificar_d_prov").jqxComboBox({source: CxPDataAdapter});
				},350);
			}
		}
	});

	$("#caja_modificar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#caja_modificar_cliente_ID").bind('change', function (event) {
		if (event.args)
		{
			if ($("#caja_modificar_cliente").val() != event.args.item.value)
			{
				clearTimeout(Timer1);
				Timer1 = setTimeout(function()
				{
					$("#caja_modificar_cliente").jqxComboBox('selectItem', event.args.item.value);
					CxPSource.data = {"CxP_Deudor": event.args.item.value};
					var CxPDataAdapter = new $.jqx.dataAdapter(CxPSource);
					$("#caja_modificar_d_prov").jqxComboBox({source: CxPDataAdapter});
				},350);
			}
		}
	});
	
	var CxPSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Factura', type: 'string'},
			{ name: 'Interno', type: 'string'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#caja_modificar_d_prov").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 170,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Factura',
		valueMember: 'Interno',
		disabled: true
	});
	$("#caja_modificar_d_prov").bind('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				Cargar_CxP(event.args.item.label, event.args.item.value);
			},350);
		}
		else
		{
			ClearFields();
			$("#caja_modificar_d_prov").jqxComboBox('clearSelection');
		}
	});
	$("#caja_modificar_d_prov").bind('bindingComplete', function (event)
	{
		if (CxP != "")
		{
			$("#caja_modificar_d_prov").jqxComboBox('selectItem', CxP);
			CxP = "";
		}
	});
	document.getElementById("caja_modificar_d_prov").addEventListener("dblclick", function (event)
	{
		var Disable = $("#caja_modificar_d_prov").jqxComboBox("disabled");
		if (!Disable)
		{
			var Cliente = $("#caja_modificar_cliente_ID").jqxComboBox('getSelectedItem');
			var TmpGrupo = $("#caja_modificar_grupo").jqxComboBox('getSelectedItem');
			var TmpSubGrupo = $("#caja_modificar_subgrupo").jqxComboBox('getSelectedItem');
			var Factura = $("#caja_modificar_d_prov").jqxComboBox('getSelectedItem');
			
			if (!Cliente)
			{
				Alerts_Box("Debe Seleccionar un Cliente.", 3);
				WaitClick_Combobox("caja_modificar_cliente");
				return;
			}
			
			if (!TmpGrupo)
			{
				Alerts_Box("Debe Seleccionar un Grupo.", 3);
				WaitClick_Combobox("caja_modificar_grupo");
				return;
			}
			
			var Result1 = TmpGrupo.value.indexOf("Maquinaria");
			var Result2 = TmpGrupo.value.indexOf("Vehículos");
			var Result3 = -1;
			var Result4 = -1;
			
			if ( Result1 >= 0 || Result2 >= 0)
			{
				if (!TmpSubGrupo)
				{
					Alerts_Box("Debe Seleccionar un SubGrupo.", 3);
					WaitClick_Combobox("caja_modificar_subgrupo");
					return;
				}
				
				Result3 = TmpSubGrupo.value.indexOf("Reparac");
				Result4 = TmpSubGrupo.value.indexOf("reparac");
			}
			
			if ( Result1 >= 0 && Result3 >= 0 || Result2 >= 0 && Result3 >= 0)
			{
				if (!Factura)
				{
					Alerts_Box("Debe Seleccionar una Factura.", 3);
					WaitClick_Combobox("caja_modificar_d_prov");
				}
				$("#Caja_Modificar_Maquinaria_Window").jqxWindow('open');
			}
			else if ( Result1 >= 0 && Result4 >= 0 || Result2 >= 0 && Result4 >= 0)
			{
				if (!Factura)
				{
					Alerts_Box("Debe Seleccionar una Factura.", 3);
					WaitClick_Combobox("caja_modificar_d_prov");
				}
				$("#Caja_Modificar_Maquinaria_Window").jqxWindow('open');
			}
			else
			{
				$("#Caja_Modificar_CxP_Window").jqxWindow('open');
			}
		}
	});
	
	$("#caja_modificar_d_prov_valor").jqxNumberInput({
		theme: mytheme,
		width: 150,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
		disabled: true
	});
	
	$("#caja_modificar_estado").jqxInput({
		theme: mytheme,
		height: 20,
		width: 80,
		disabled: true,
	});
	
	$("#caja_modificar_digitado_por").jqxInput({
		theme: mytheme,
		height: 20,
		width: 60,
		disabled: true,
	});
	
	//---------------------------------------------- PARTE 2 ----------------------------------------------\\
	// --- GLOVAL VALUES
	
	
	//--------- LEFT
	$("#caja_modificar_recaudo_efectivo").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999
	});
	$('#caja_modificar_recaudo_efectivo').on('valueChanged', function (event)
	{
		if ($("#caja_modificar_recaudo_efectivo").jqxNumberInput('disabled') == true)
			DoTheMath();
	});
	$('#caja_modificar_recaudo_efectivo').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_modificar_efectivo").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		disabled: true
	});
	$("#caja_modificar_transferencias").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$("#caja_modificar_subtotal_cheques").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$("#caja_modificar_rete_iva").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
		disabled: true
	});
	$('#caja_modificar_rete_iva').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_modificar_rete_iva_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$('#caja_modificar_rete_iva_total').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_modificar_rete_ica").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
		disabled: true
	});
	$('#caja_modificar_rete_ica').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_modificar_rete_ica_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$('#caja_modificar_rete_ica_total').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_modificar_rete_fuente").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
		disabled: true
	});
	$('#caja_modificar_rete_fuente').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_modificar_rete_fuente_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$('#caja_modificar_rete_fuente_total').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_modificar_otro_dcto").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
	});
	$('#caja_modificar_otro_dcto').on('change', function (event) 
	{
		DoTheMath();
	});
	
	var DctoSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Dcto', type: 'string'},
		],
		data: {"Caja_Dcto":true},
		url: "modulos/parametros.php",
		async: true
	};
	var DctoDataAdapter = new $.jqx.dataAdapter(DctoSource);
	
	$("#caja_modificar_dcto_concepto").jqxComboBox({
		theme: mytheme,
		height: 25,
		width: 180,
		source: DctoDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Dcto',
		valueMember: 'Dcto',
	});
	
	$("#caja_modificar_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	function DoTheMath ()
	{
		var caja_modificar_efectivo = parseFloat($('#caja_modificar_recaudo_efectivo').val());
		var caja_modificar_transferencias = parseFloat($('#caja_modificar_transferencias').val());
		var cheques = parseFloat($('#caja_modificar_subtotal_cheques').val());
		var caja_modificar_otro_dcto = parseFloat($('#caja_modificar_otro_dcto').val());
		
		var iva_percent = parseFloat($('#caja_modificar_rete_iva').val());
		var ica_percent = parseFloat($('#caja_modificar_rete_ica').val());
		var fuente_percent = parseFloat($('#caja_modificar_rete_fuente').val());
		var iva_percent_total = parseFloat($('#caja_modificar_rete_iva_total').val());
		var ica_percent_total = parseFloat($('#caja_modificar_rete_ica_total').val());
		var fuente_percent_total = parseFloat($('#caja_modificar_rete_fuente_total').val());
		var total_percent = 0;
		
		if (Active)
		{
			total_percent = iva_percent_total + ica_percent_total + fuente_percent_total;
			Total = caja_modificar_efectivo + caja_modificar_transferencias + cheques + total_percent + caja_modificar_otro_dcto;
			$('#caja_modificar_efectivo').val(caja_modificar_efectivo);
			$('#caja_modificar_total').val(Total);
		}
		else
		{
			iva_percent_total = Math.round(caja_modificar_efectivo / 100 * parseFloat(iva_percent));
			ica_percent_total = Math.round(caja_modificar_efectivo / 100 * parseFloat(ica_percent));
			fuente_percent_total = Math.round(caja_modificar_efectivo / 100 * parseFloat(fuente_percent));
			total_percent = iva_percent_total + ica_percent_total + fuente_percent_total;
			Total = caja_modificar_efectivo + caja_modificar_otro_dcto;
			$('#caja_modificar_efectivo').val(caja_modificar_efectivo - total_percent);
			$('#caja_modificar_rete_iva_total').val(iva_percent_total);
			$('#caja_modificar_rete_ica_total').val(ica_percent_total);
			$('#caja_modificar_rete_fuente_total').val(fuente_percent_total);
			$('#caja_modificar_total').val(Total);
			$("#caja_modificar_total").blur();
		}
	};
	
	function Calcular ()
	{
		var ValorGrid1 = 0;
		var ValorGrid2 = 0;
		var information1 = $('#caja_modificar_items_grid1').jqxGrid('getdatainformation');
		var rowscounts1 = information1.rowscount;
		var information2 = $('#caja_modificar_items_grid2').jqxGrid('getdatainformation');
		var rowscounts2 = information2.rowscount;
		var old_total1 = 0;
		var old_total2 = 0;
		for (i1=0; i1<rowscounts1; i1++){
			var currentRow1 = $('#caja_modificar_items_grid1').jqxGrid('getrowdata', i1);
			old_total1 = old_total1 + parseFloat(currentRow1.Valor);
		};
		for (i2=0; i2<rowscounts2; i2++){
			var currentRow2 = $('#caja_modificar_items_grid2').jqxGrid('getrowdata', i2);
			old_total2 = old_total2 + parseFloat(currentRow2.Valor);
		};
		var exit = 0;
		if (ValorGrid1 == old_total1)
			exit++;
		else
			ValorGrid1 = old_total1;
		
		if (ValorGrid2 == old_total2)
			exit++;
		else
			ValorGrid2 = old_total2;
			
		if (exit >= 2) {
			return;
		}
		else {
			$('#caja_modificar_transferencias').val(ValorGrid1);
			$('#caja_modificar_subtotal_cheques').val(ValorGrid2);
			DoTheMath();
		}
	};
	
	//--------- MIDLE
	
	var TipoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo', type: 'string'},
		],
		data: {"Caja_Tipo":true},
		url: "modulos/parametros.php",
		async: true
	};
	var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource);
	
	$("#caja_modificar_tipo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 140,
		source: TipoDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo',
	});
	
	$("#caja_modificar_valor").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 165,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999999
	});
	
	$("#caja_modificar_num_consignacion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 160,
	});
	
	var BancoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Banco', type: 'string'},
		],
		data: {"Caja_Banco":true},
		url: "modulos/parametros.php",
		async: true
	};
	var BancoDataAdapter = new $.jqx.dataAdapter(BancoSource);
	
	$("#caja_modificar_banco").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 160,
		source: BancoDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Banco',
		valueMember: 'Banco',
	});
	
	var Grid1Source =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Tipo', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Consignacion', type: 'string' },
			{ name: 'Banco', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var Grid1DataAdapter = new $.jqx.dataAdapter(Grid1Source);
	
	$("#caja_modificar_items_grid1").jqxGrid({
		theme: mytheme,
		width: 650,
		height: 120,
		source: Grid1DataAdapter,
		showtoolbar: true,
		editable: true,
		editmode: 'click',
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="caja_modificar_addrowbutton1" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="caja_modificar_deleterowbutton1" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#caja_modificar_addrowbutton1").jqxButton({theme: mytheme, template: "success"});
			$("#caja_modificar_deleterowbutton1").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#caja_modificar_addrowbutton1").on('click', function () {
				Add_Row1();
			});
			// delete row.
			$("#caja_modificar_deleterowbutton1").on('click', function () {
				var selectedrowindex = $("#caja_modificar_items_grid1").jqxGrid('getselectedrowindex');
				var rowscount = $("#caja_modificar_items_grid1").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#caja_modificar_items_grid1").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#caja_modificar_items_grid1").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Tipo', datafield: 'Tipo', editable: false, width: '20%', height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				width: "30%",
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
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
			{ text: 'Num. Aprob/Consig.', datafield: 'Consignacion', editable: false, width: '25%', height: 20 },
			{ text: 'Banco Destino', datafield: 'Banco', editable: false, width: '25%', height: 20, cellsalign: 'caja_modificar_right' }
		]
	});
	$("#caja_modificar_items_grid1").on('cellvaluechanged', function (event) 
	{
		if (event.args)
		{
			var args = event.args;
			var datafield = event.args.datafield;
			var rowBoundIndex = args.rowindex;
			var value = args.newvalue;
			var oldvalue = args.oldvalue;

			if (datafield == "Valor")
			{
				Calcular();
			}
		}
	});
	
	function Add_Row1()
	{
		if (Active == false)
			return;
			
		var TipoPago = $('#caja_modificar_tipo').val();
		var TipoBanco = $('#caja_modificar_banco').val();
		var Valor = $("#caja_modificar_valor").val();
		var NumConsignacion = $("#caja_modificar_num_consignacion").val();
		
		if (! TipoPago | TipoPago <= 0) {
			Alerts_Box("Favor Seleccionar un Tipo de Pago!", 4);
			WaitClick_Combobox("caja_modificar_tipo");
			return;
		}
		
		if (Valor <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 4);
			WaitClick_NumberInput("caja_modificar_valor");
			return;
		}
		
		if (NumConsignacion <= 0 | NumConsignacion == "") {
			Alerts_Box("Debe Ingresar un Numero de Consignacion/Aprobacion!", 4);
			WaitClick_Input("caja_modificar_num_consignacion");
			return;
		}
		
		if (! TipoBanco | TipoBanco <= 0) {
			Alerts_Box("Favor Seleccionar un Banco!", 4);
			WaitClick_Combobox("caja_modificar_banco");
			return;
		}
		
		var datarow = [{
			"Tipo":TipoPago,
			"Valor":Valor,
			"Consignacion":NumConsignacion,
			"Banco":TipoBanco
		}];
		
		$("#caja_modificar_items_grid1").jqxGrid("addrow", null, datarow, "first");
		// Clear Values
		$('#caja_modificar_tipo').jqxComboBox('clearSelection');
		$('#caja_modificar_valor').val('');
		$('#caja_modificar_num_consignacion').val('');
		$('#caja_modificar_banco').jqxComboBox('clearSelection');
		Calcular();
	};
	
	//--------- RIGHT
	
	$("#caja_modificar_num_cheque").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
	});
	
	$("#caja_modificar_valor_cheque").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999999
	});
	
	var BankNumSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Numero', type: 'string'}
		],
		data: {"Caja_Numero_Bancos":true},
		url: "modulos/parametros.php",
		async: true
	};
	var BankNumDataAdapter = new $.jqx.dataAdapter(BankNumSource);
	
	$("#caja_modificar_banco_cheque").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 45,
		source: BankNumDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: '#',
		selectedIndex: -1,
		displayMember: 'Numero',
		valueMember: 'Numero'
	});
	$("#caja_modificar_banco_cheque").bind('change', function (event)
	{
		if (!event.args)
			$("#caja_modificar_banco_cheque").jqxComboBox('clearSelection');
	});
	
	$("#caja_modificar_num_cuenta").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
	});
	
	$("#caja_modificar_estado_fecha").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 95,
		promptText: 'Seleccionar',
		selectedIndex: -1,
	});
	
	$("#caja_modificar_estado_fecha").jqxComboBox('addItem', {label: "Al Dia", value: "Al Dia"});
	$("#caja_modificar_estado_fecha").jqxComboBox('addItem', {label: "PostFechado", value: "PostFechado"});
	
	$("#caja_modificar_estado_fecha").bind('select', function (event) {
		if (event.args) {
			if (event.args.item.value == "PostFechado"){
				$("#caja_modificar_cheque_fecha").jqxDateTimeInput({ disabled: false});
			}
			else {
				$("#caja_modificar_cheque_fecha").jqxDateTimeInput({ disabled: true});
			}
		}
	});
	
	$("#caja_modificar_cheque_fecha").jqxDateTimeInput({
		theme: mytheme,
		height: 20,
		width: 100,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
		disabled: true
	});
	
	var Grid2Source =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'NumCheque', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'NumBanco', type: 'string' },
			{ name: 'NumCuenta', type: 'string' },
			{ name: 'EstadoFecha', type: 'string' },
			{ name: 'FechaCheque', type: 'date' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var Grid2DataAdapter = new $.jqx.dataAdapter(Grid2Source);
	
	$("#caja_modificar_items_grid2").jqxGrid({
		theme: mytheme,
		width: 650,
		height: 120,
		source: Grid2DataAdapter,
		showtoolbar: true,
		editable: true,
		editmode: 'click',
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="caja_modificar_addrowbutton2" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="caja_modificar_deleterowbutton2" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#caja_modificar_addrowbutton2").jqxButton({theme: mytheme, template: "success"});
			$("#caja_modificar_deleterowbutton2").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#caja_modificar_addrowbutton2").on('click', function () {
				Add_Row2();
			});
			// delete row.
			$("#caja_modificar_deleterowbutton2").on('click', function () {
				var selectedrowindex = $("#caja_modificar_items_grid2").jqxGrid('getselectedrowindex');
				var rowscount = $("#caja_modificar_items_grid2").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#caja_modificar_items_grid2").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#caja_modificar_items_grid2").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Numero de cheque', datafield: 'NumCheque', editable: false, width: '20%', height: 20 },
			{
				text: 'Valor',
				datafield: 'Valor',
				width: "23%",
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
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
			{ text: 'Banco', datafield: 'NumBanco', editable: false, width: '7%', height: 20 },
			{ text: 'Num. Cuenta', datafield: 'NumCuenta', editable: false, width: '20%', height: 20, cellsalign: 'caja_modificar_right' },
			{ text: 'Estado Fecha', datafield: 'EstadoFecha', editable: false, width: '15%', height: 20, cellsalign: 'caja_modificar_right' },
			{
				text: 'Fecha Cheque',
				datafield: 'FechaCheque',
				columntype: 'datetimeinput',
				editable: false,
				width: '15%',
				height: 20,
				cellsalign: 'caja_modificar_right',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
		]
	});
	$("#caja_modificar_items_grid2").on('cellvaluechanged', function (event) 
	{
		if (event.args)
		{
			var args = event.args;
			var datafield = event.args.datafield;
			var rowBoundIndex = args.rowindex;
			var value = args.newvalue;
			var oldvalue = args.oldvalue;

			if (datafield == "Valor")
			{
				Calcular();
			}
		}
	});
	
	function Add_Row2()
	{
		if (Active == false)
			return;
			
		var Cheque = $('#caja_modificar_num_cheque').val();
		var ValorCheque = $('#caja_modificar_valor_cheque').val();
		var BancoCheque = $("#caja_modificar_banco_cheque").val();
		var NumCuenta = $("#caja_modificar_num_cuenta").val();
		var EstadoFecha = $('#caja_modificar_estado_fecha').val();
		var FechaCheque = GetFormattedDate($('#caja_modificar_cheque_fecha').jqxDateTimeInput('getDate'));
		
		if (!Cheque | Cheque == "") {
			Alerts_Box("Debe Ingresar un Numero de Cheque!", 4);
			WaitClick_Input("caja_modificar_num_cheque");
			return;
		}
		
		if (ValorCheque <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 4);
			WaitClick_NumberInput("caja_modificar_valor_cheque");
			return;
		}
		
		if (!BancoCheque | BancoCheque == "") {
			Alerts_Box("Debe Ingresar un Numero de Banco!", 4);
			WaitClick_Combobox("caja_modificar_banco_cheque");
			return;
		}
		
		if (!NumCuenta | NumCuenta == "") {
			Alerts_Box("Debe Ingresar un Numero de Cuenta!", 4);
			WaitClick_Input("caja_modificar_num_cuenta");
			return;
		}
		
		if (!EstadoFecha | EstadoFecha <= 0) {
			Alerts_Box("Favor Seleccionar un Estado de Fecha", 4);
			WaitClick_Combobox("caja_modificar_estado_fecha");
			return;
		}
		
		var datarow = [{
			"NumCheque":Cheque,
			"Valor":ValorCheque,
			"NumBanco":BancoCheque,
			"NumCuenta":NumCuenta,
			"EstadoFecha":EstadoFecha,
			"FechaCheque":FechaCheque
		}];
		
		$("#caja_modificar_items_grid2").jqxGrid("addrow", null, datarow, "first");
		// Clear Values
		$('#caja_modificar_num_cheque').val('');
		$('#caja_modificar_valor_cheque').val('');
		$('#caja_modificar_banco_cheque').val('');
		$('#caja_modificar_num_cuenta').val('');
		$('#caja_modificar_estado_fecha').jqxComboBox('clearSelection');
		//$('#fecha_cheque').jqxDateTimeInput('getText');
		Calcular();
	};
	//---------------------------------------------- PARTE 3 ----------------------------------------------\\
	
	function Save()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var datinfo1 = $("#caja_modificar_items_grid1").jqxGrid('getdatainformation');
		var datinfo2 = $("#caja_modificar_items_grid2").jqxGrid('getdatainformation');
		var count1 = datinfo1.rowscount;
		var count2 = datinfo2.rowscount;
		
		var myarray = new Array();
		var myarray1 = new Array();
		var myarray2 = new Array();
		
		var RC = $("#caja_modificar_recibo").jqxComboBox('getSelectedItem');
		var zTipo = $("#caja_modificar_tipo_mov").jqxComboBox('getSelectedItem');
		var zGrupo = $("#caja_modificar_grupo").jqxComboBox('getSelectedItem');
		var zSubGrupo = $("#caja_modificar_subgrupo").jqxComboBox('getSelectedItem');
		var zSubGrupo2 = $("#caja_modificar_subgrupo2").jqxComboBox('getSelectedItem');
		var zCliente = $("#caja_modificar_cliente").jqxComboBox('getSelectedItem');
		
		if (!zTipo)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Movimiento.", 3);
			WaitClick_Combobox("caja_modificar_tipo_mov");
			Locked = false;
			return;
		}
		
		if (zTipo.value == "Egresos" && zGrupo.value != "Nómina")
		{
			var Factura = $("#caja_modificar_d_prov").jqxComboBox('getSelectedItem');
			if (!Factura)
			{
				Alerts_Box("Debe Seleccionar una Factura(CxP).", 3);
				WaitClick_Combobox("caja_modificar_d_prov");
				Locked = false;
				return;
			}
		}
		
		if (!RC)
		{
			Alerts_Box("Debe Seleccionar un Recibo!", 3);
			WaitClick_Combobox("caja_modificar_recibo");
			Locked = false;
			return;
		}
		
		if (!zGrupo)
		{
			Alerts_Box("Debe Ingresar un Grupo!", 3);
			WaitClick_Combobox("caja_modificar_grupo");
			Locked = false;
			return;
		}
		
		if (!zSubGrupo)
		{
			Alerts_Box("Debe Ingresar un SubGrupo!", 3);
			WaitClick_Combobox("caja_modificar_subgrupo");
			Locked = false;
			return;
		}
		
		if (!zSubGrupo2)
		{
			Alerts_Box("Debe Ingresar un SubGrupo Nivel 2!", 3);
			WaitClick_Combobox("caja_modificar_subgrupo2");
			Locked = false;
			return;
		}
		
		if (!zCliente)
		{
			Alerts_Box("Debe Ingresar un Cliente!", 3);
			WaitClick_Combobox("caja_modificar_cliente");
			Locked = false;
			return;
		}
		
		if ($("#caja_modificar_otro_dcto").val() > 0)
		{
			if ($("#caja_modificar_dcto_concepto").val() < 1 | $("#caja_modificar_dcto_concepto").val() == "")
			{
				Alerts_Box("Ha ingresado un descuento sin especificar el Concepto!", 3);
				WaitClick_Combobox("caja_modificar_dcto_concepto");
				Locked = false;
				return;
			}
		}
		
		if ($("#caja_modificar_total").val() < 1 | $("#caja_modificar_total").val() == "")
		{
			Alerts_Box("Debe Ingresar el Efectivo", 3);
			WaitClick();
			Locked = false;
			return;
		}

		// Grid1
		for (a1 = 0; a1 < count1; a1++)
		{
			var array1 = {};
			var currentRow1 = $('#caja_modificar_items_grid1').jqxGrid('getrowdata', a1);
			
			array1["Tipo1"] = currentRow1.Tipo;
			array1["Valor1"] = currentRow1.Valor;
			array1["Num1"] = currentRow1.Consignacion;
			array1["Banco1"] = currentRow1.Banco;
			myarray1[a1] = array1;
		}
		// Grid2
		for (a2 = 0; a2 < count2; a2++)
		{
			var array2 = {};
			var currentRow2 = $('#caja_modificar_items_grid2').jqxGrid('getrowdata', a2);
			
			array2["ChequeNum"] = currentRow2.NumCheque;
			array2["Valor2"] = currentRow2.Valor;
			array2["Banco2"] = currentRow2.NumBanco;
			array2["Num2"] = currentRow2.NumCuenta;
			array2["EstadoFecha"] = currentRow2.EstadoFecha;
			array2["FechaCheque"] = currentRow2.FechaCheque;
			myarray2[a2] = array2;
		}
		var array = {};
		var CxP_Values = $("#caja_modificar_d_prov").jqxComboBox('getSelectedItem');
		
		array["ClienteID"] = zCliente.value;
		array["Fecha"] = GetFormattedDate($('#caja_modificar_fecha').jqxDateTimeInput('getDate'));
		array["CajaInterno"] = $('#caja_modificar_caja_interno').val();
		array["ReciboCaja"] = RC.label;
		if (!CxP_Values)
		{
			array["CxP_Factura"] = "";
			array["CxP_Interno"] = "";
		}
		else
		{
			array["CxP_Factura"] = CxP_Values.label;
			array["CxP_Interno"] = CxP_Values.value;
		}
		array["Tipo"] = zTipo.value;
		array["Grupo"] = zGrupo.value;
		array["SubGrupo"] = zSubGrupo.value;
		array["SubGrupo2"] = zSubGrupo2.value;
		array["Efectivo"] = $('#caja_modificar_efectivo').val();
		array["Cheque"] = $('#caja_modificar_subtotal_cheques').val();
		array["Consignacion"] = $('#caja_modificar_transferencias').val();
		array["ReteIva"] = $('#caja_modificar_rete_iva_total').val();
		array["ReteIca"] = $('#caja_modificar_rete_ica_total').val();
		array["ReteFuente"] = $('#caja_modificar_rete_fuente_total').val();
		array["Descuento"] = $('#caja_modificar_otro_dcto').val();
		array["ConceptoDcto"] = $('#caja_modificar_dcto_concepto').val();
		array["Total"] = $('#caja_modificar_total').val();
		array["Observaciones"] = $('#caja_modificar_observaciones').val();
		
		myarray[0] = array;
		
		/*
		alert(JSON.stringify(myarray))
		Locked = false;
		return;
		*/
		
		Alerts_Box("Al Guardar los Datos se eliminar&aacute;n todos los movimientos relacionados con este recibo<br />"+
		"(en caso de existir alguno).<br />¿Desea Continuar?", 4, true);
		var CheckTimer = setInterval(function()
		{
			if (ClickOK == true)
			{
				clearInterval(CheckTimer);
				ClickOK = false;
				$("#Loading_Mess").html("Procesando Solicitud...");
				$("#Loading").show();
				$.ajax({
					dataType: "json",
					data: {
						"Caja_Modificar":myarray,
						"Grid1":myarray1,
						"Grid2":myarray2
					},
					url: "modulos/guardar.php",
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
								EnableDisableAll(true);
								Alerts_Box("Datos Guardados con Exito!", 2);
								//ClearDocument();
								// Actualizar Ordenes
								ReciboSource.data =  {"Caja_Modificar":true},
								ReciboDataAdapter = new $.jqx.dataAdapter(ReciboSource);
								$("#caja_modificar_recibo").jqxComboBox({source: ReciboDataAdapter});
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
			}
			if (ClickCANCEL == true) {
				clearInterval(CheckTimer);
				ClickCANCEL = false;
				Locked = false;
			}
		}, 10);
	};
	
	$('#caja_modificar_guardar').jqxButton({
		width: 150,
		height: 35,
		template: "info"
	});
	$("#caja_modificar_guardar").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		Save();
	});
	
	$('#caja_modificar_nuevo').jqxButton({width: 150, height: 35, template: "success"});
	$("#caja_modificar_nuevo").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		ClearDocument();
	});
	
	$("#caja_modificar_imprimir").jqxButton({width: 150, height: 35, template: "warning"});
	$("#caja_modificar_imprimir").bind('click', function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		window.open("imprimir/caja_recibo.php?Caja_Interno="+$("#caja_modificar_caja_interno").val()+"", "", "width=730, height=600, menubar=no, titlebar=no");
	});
	
	$("#caja_modificar_categorias").jqxButton({width: 150, height: 35, template: "primary"});
	
	$("#caja_modificar_crear_tercero").jqxButton({width: 150, height: 35, template: "inverse"});
	$("#caja_modificar_crear_tercero").bind('click', function ()
	{
		$("#Caja_Modificar_Tercero_Window").jqxWindow('open');
	});
	
	$('#caja_modificar_impresora').jqxButton({width: 150, height: 35, template: "warning"});
	$("#caja_modificar_impresora").bind('click', function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		window.open("imprimir/caja_recibo_a_punto.php?Caja_Interno="+$("#caja_modificar_caja_interno").val()+"", "", "width=730, height=600, menubar=no, titlebar=no");
	});
	
	// ------------------------------------------ WINDOWS
	//--- Maquinaria
	$("#Caja_Modificar_Maquinaria_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 970,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Caja_Modificar_Maquinaria_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Ord_Reparacion":$("#caja_modificar_d_prov").val(),
				"ClienteID":$("#caja_modificar_cliente_ID").val(),
			},
			url: "maquinaria/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Caja_Modificar_Maquinaria_Content").html(data);
			},
			complete: function()
			{
				$("#Loading").hide();
			}
		});
	});
	
	//--- CxP
	$("#Caja_Modificar_CxP_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 350,
		width: 1000,
		minWidth: 900,
		maxWidth: 1000,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Caja_Modificar_CxP_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"ClienteID":$("#caja_modificar_cliente_ID").val(),
			},
			url: "cxp/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Caja_Modificar_CxP_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	$("#Caja_Modificar_CxP_Window").on('close', function (event)
	{
		CxPSource.data = {"CxP_Deudor":$("#caja_modificar_cliente_ID").val()};
		var CxPDataAdapter = new $.jqx.dataAdapter(CxPSource);
		$("#caja_modificar_d_prov").jqxComboBox({source: CxPDataAdapter});
	});
	
	//--- Crear Terceros
	$("#Caja_Modificar_Tercero_Window").jqxWindow({
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
					$("#Caja_Modificar_Tercero_Content").html(data);
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
	});
	
	if (!Admin && !Guardar)
	{
		$("#caja_modificar_guardar").jqxButton({ disabled: true });
		$("#caja_modificar_nuevo").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Imprimir)
	{
		$("#caja_modificar_imprimir").jqxButton({ disabled: true });
	}
		
	CheckRefresh();
});
</script>
<div id="Caja_Modificar_Maquinaria_Window">
	<div id="Caja_Modificar_Maquinaria_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Orden de Maquinaria</div>
	</div>
	<div id="Caja_Modificar_Maquinaria_Content" class="WindowContainer">
	</div>
</div>
<div id="Caja_Modificar_CxP_Window">
	<div id="Caja_Modificar_CxP_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 250px;">Crear Recibo de Cuenta por Pagar</div>
	</div>
	<div id="Caja_Modificar_CxP_Content" class="WindowContainer">
	</div>
</div>
<div id="Caja_Modificar_Tercero_Window">
	<div id="Caja_Modificar_Tercero_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 340px;">Crear Cliente/Tercero</div>
	</div>
	<div id="Caja_Modificar_Tercero_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="0" cellspacing="2">
		<tr>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="caja_modificar_caja_interno"/>
			</td>
			<td>
				Tipo Mov.
			</td>
			<td>
				Grupo
			</td>
			<td>
				SubGrupo
			</td>
			<td>
				SubGrupo Niv 2
			</td>
		</tr>
		<tr>
			<td>
				Recibo
			</td>
			<td>
				<div id="caja_modificar_recibo"></div>
			</td>
			<td>
				<div id="caja_modificar_tipo_mov"></div>
			</td>
			<td>
				<div id="caja_modificar_grupo"></div>
			</td>
			<td>
				<div id="caja_modificar_subgrupo"></div>
			</td>
			<td>
				<div id="caja_modificar_subgrupo2"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<div id="caja_modificar_fecha"></div>
			</td>
			<td colspan="3">
				<li class="parte1_li_txt">
					Tercero&nbsp;
				</li>
				<li>
					<div id="caja_modificar_cliente"></div>
				</li>
			</td>
			<td>
				<div id="caja_modificar_cliente_ID"></div>
			</td>
		</tr>
		<tr>
			<td>
				D. Prov
			</td>
			<td>
				<div id="caja_modificar_d_prov"></div>
			</td>
			<td>
				<div id="caja_modificar_d_prov_valor"></div>
			</td>
			<td colspan="3">
				<li class="parte1_li_txt" style="padding-right:11px">
					Estado
				</li>
				<li>
					<input type="text" id="caja_modificar_estado"/>
				</li>
				<li class="parte1_li_txt">
					&nbsp;Digit&oacute;&nbsp;
				</li>
				<li>
					<input type="text" id="caja_modificar_digitado_por"/>
				</li>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2" style="margin-top:15px;">
	<div id="caja_modificar_left" style="float: left; margin-left:0px;">
		<table cellpadding="0" cellspacing="2">
			<tr>
				<td colspan="2">
					<div id="caja_modificar_tipo_change">Valor</div>
				</td>
				<td style="padding:0px;">
					<div id="caja_modificar_recaudo_efectivo"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Efectivo Total
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_efectivo"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Transferencias/Tarjetas
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_transferencias"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					SubTotal Cheques
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_subtotal_cheques"></div>
				</td>
			</tr>
			<tr>
				<td>
					Rete. IVA
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_rete_iva"></div>
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_rete_iva_total"></div>
				</td>
			</tr>
			<tr>
				<td>
					Rete. ICA
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_rete_ica"></div>
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_rete_ica_total"></div>
				</td>
			</tr>
			<tr>
				<td>
					Rete. Fuente
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_rete_fuente"></div>
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_rete_fuente_total"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Otro Descuento
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_otro_dcto"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Concepto Descuento
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_dcto_concepto"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					TOTAL
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_modificar_total"></div>
				</td>
			</tr>
		</table>
	</div>
	<div id="caja_modificar_middle" style="float: left; margin-left:10px;">
		<table cellpadding="0" cellspacing="0" style="text-align:center;">
			<tr style="background: #9FC2F0">
				<td colspan="4" style="border-top: 1px solid #A4BED4; border-top: 1px solid #A4BED4;">
					Relaci&oacute;n de transacciones, Pago con Tarjeta, Transferencia o Consignaci&oacute;n
				</td>
			</tr>
			<tr style="background: #E0E9F5">
				<td style="border-left: 1px solid #A4BED4; border-bottom: 1px solid #A4BED4;">
					Tipo
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Valor
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Num. Aprob/Consing
				</td>
				<td style="border-right: 1px solid #A4BED4; border-bottom: 1px solid #A4BED4;">
					Banco Destino
				</td>
			</tr>
			<tr>
				<td style="padding-right:5px">
					<div id="caja_modificar_tipo"></div>
				</td>
				<td>
					<div id="caja_modificar_valor"></div>
				</td>
				<td style="padding:0px 5px">
					<input type="text" id="caja_modificar_num_consignacion"/>
				</td>
				<td>
					<div id="caja_modificar_banco"></div>
				</td>
			</tr>
		</table>
		<div id="caja_modificar_items_grid1"></div>
	</div>
	<div id="caja_modificar_right" style="float: left; margin-left:10px; margin-bottom:15px;">
		<table cellpadding="0" cellspacing="0" style="text-align:center; margin-top: 10px;">
			<tr style="background: #9FC2F0">
				<td colspan="6" style="border-top: 1px solid #A4BED4; border-top: 1px solid #A4BED4;">
					Relaci&oacute;n de Cheques
				</td>
			</tr>
			<tr style="background: #E0E9F5">
				<td style="border-left: 1px solid #A4BED4; border-bottom: 1px solid #A4BED4;">
					Numero de cheque
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Valor
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Banco
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Num. Cuenta
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Estado Fecha
				</td>
				<td style="border-right: 1px solid #A4BED4; border-bottom: 1px solid #A4BED4;">
					Fecha Cheque
				</td>
			</tr>
			<tr>
				<td style="padding-right:5px">
					<input type="text" id="caja_modificar_num_cheque"/>
				</td>
				<td>
					<div id="caja_modificar_valor_cheque"></div>
				</td>
				<td style="padding:0px 5px">
					<div id="caja_modificar_banco_cheque"></div>
				</td>
				<td>
					<input type="text" id="caja_modificar_num_cuenta"/>
				</td>
				<td style="padding:0px 5px">
					<div id="caja_modificar_estado_fecha"></div>
				</td>
				<td>
					<div id="caja_modificar_cheque_fecha"></div>
				</td>
			</tr>
		</table>
		<div id="caja_modificar_items_grid2"></div>
	</div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table style="margin-left: 0px;">
		<tr>
			<td>
				Aplicado a:
			</td>
			<td colspan="4">
				Observaciones:
			</td>
		</tr>
		<tr>
			<td rowspan="2">
				<textarea readonly rows="5" cols="33" id="caja_modificar_aplicado_a" maxlength="100" class="DisabledTextArea" style="resize:none;"></textarea>
			</td>
			<td rowspan="2">
				<textarea rows="5" cols="34" id="caja_modificar_observaciones" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td>
				<input type="button" id="caja_modificar_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="caja_modificar_nuevo" value="Nuevo"/>
			</td>
			<td>
				<input type="button" id="caja_modificar_imprimir" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<!--<td colspan="2">
				&nbsp;
			</td>-->
			<td>
				<input type="button" id="caja_modificar_categorias" value="Categorias"/>
			</td>
			<td>
				<input type="button" id="caja_modificar_crear_tercero" value="Crear Tercero"/>
			</td>
			<td>
				<input type="button" id="caja_modificar_impresora" value="Impresora Punto"/>
			</td>
		</tr>
	</table>
</div>