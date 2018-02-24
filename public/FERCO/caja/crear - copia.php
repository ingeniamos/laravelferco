<?php
session_start();
$NumOfID = 0;
$Caja_Interno = isset($_POST['Caja_Interno']) ? $_POST['Caja_Interno']:"";

	if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
	{
		$_SESSION["NumOfID"]++;
	}
$NumOfID = $_SESSION["NumOfID"];
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// GLOBALS
	var mytheme = "energyblue";
	var Locked = false;
	var CxP = "";
	var SubGrupo = "";
	var SubGrupo2 = "";
	var Caja_Interno = "";
	var Active = true;
	var Total = 0;
	var ID_Incorrecto = false;
	var Maquinaria = false;
	var Timer1 = 0;
	var Timer2 = 0;
	RowAdded = true;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Caja_Content");
	var Body = document.getElementById("CrearRC_Content");
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
	
	function ClearFields ()
	{
		if (Locked == true)
			return;
		
		$("#caja_crear_d_prov_valor<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_transferencias<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_subtotal_cheques<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid('clear');
		$("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid('clear');
		$("#caja_crear_efectivo<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_rete_iva<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_rete_iva_total<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_rete_ica<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_rete_ica_total<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_rete_fuente<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_otro_dcto<?php echo $NumOfID; ?>").val("0");
		$("#caja_crear_total<?php echo $NumOfID; ?>").val("0");
	};
	
	function ReDefine()
	{
		ClearJSON = [
			//--- Parte 1
			{id:"caja_crear_caja_interno<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_recibo<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_d_prov<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_tipo_mov<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_grupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_subgrupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_subgrupo2<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			//--- Parte 2
			//Left
			{id:"caja_crear_d_prov_valor<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_transferencias<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_subtotal_cheques<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_efectivo<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_rete_iva<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_rete_iva_total<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_rete_ica<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_rete_ica_total<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_rete_fuente<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_rete_fuente_total<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_otro_dcto<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_dcto_concepto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_total<?php echo $NumOfID; ?>", type:""},
			//Right Top
			{id:"caja_crear_tipo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_valor<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_num_consignacion<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_banco<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_items_grid1<?php echo $NumOfID; ?>", type:"jqxGrid"},
			//Right Bottom
			{id:"caja_crear_num_cheque<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_valor_cheque<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_banco_cheque<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_num_cuenta<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_estado_fecha<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"caja_crear_items_grid2<?php echo $NumOfID; ?>", type:"jqxGrid"},
			//--- Parte 3
			{id:"caja_crear_aplicado_a<?php echo $NumOfID; ?>", type:""},
			{id:"caja_crear_observaciones<?php echo $NumOfID; ?>", type:""},
		];
		if ("<?php echo $Caja_Interno ?>" == "")
		{
			EnableDisableJSON = [
				//--- Parte 1
				{id:"caja_crear_recibo<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_tipo_mov<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_grupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_subgrupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_subgrupo2<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_fecha<?php echo $NumOfID; ?>", type:"jqxDateTimeInput"},
				{id:"caja_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				//--- Parte 2
				//Left
				{id:"caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_iva<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_iva_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_ica<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_ica_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_fuente<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_fuente_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_otro_dcto<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_dcto_concepto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				//{id:"caja_crear_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				//Right Top
				{id:"caja_crear_tipo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_valor<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_num_consignacion<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_banco<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_addrowbutton1<?php echo $NumOfID; ?>", type:"jqxButton"},
				{id:"caja_crear_deleterowbutton1<?php echo $NumOfID; ?>", type:"jqxButton"},
				//Right Bottom
				{id:"caja_crear_num_cheque<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_valor_cheque<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_banco_cheque<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_num_cuenta<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_estado_fecha<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_addrowbutton2<?php echo $NumOfID; ?>", type:"jqxButton"},
				{id:"caja_crear_deleterowbutton2<?php echo $NumOfID; ?>", type:"jqxButton"},
				//--- Parte 3
				{id:"caja_crear_aplicado_a<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_observaciones<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_guardar<?php echo $NumOfID; ?>", type:"jqxButton"},
				//{id:"caja_crear_imprimir", type:"jqxButton"},
				//{id:"caja_crear_aplicar<?php echo $NumOfID; ?>", type:"jqxButton"},
				{id:"caja_crear_crear_tercero<?php echo $NumOfID; ?>", type:"jqxButton"},
				//{id:"caja_crear_impresora", type:"jqxButton"},
			];
		}
		else
		{
			EnableDisableJSON = [
				//--- Parte 1
				{id:"caja_crear_recibo<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_tipo_mov<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_grupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_subgrupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_subgrupo2<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_fecha<?php echo $NumOfID; ?>", type:"jqxDateTimeInput"},
				{id:"caja_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				//--- Parte 2
				//Left
				{id:"caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_iva<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_iva_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_ica<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_ica_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_fuente<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_rete_fuente_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_otro_dcto<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_dcto_concepto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				//{id:"caja_crear_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				//Right Top
				{id:"caja_crear_tipo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_valor<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_num_consignacion<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_banco<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_addrowbutton1<?php echo $NumOfID; ?>", type:"jqxButton"},
				{id:"caja_crear_deleterowbutton1<?php echo $NumOfID; ?>", type:"jqxButton"},
				//Right Bottom
				{id:"caja_crear_num_cheque<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_valor_cheque<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
				{id:"caja_crear_banco_cheque<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_num_cuenta<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_estado_fecha<?php echo $NumOfID; ?>", type:"jqxComboBox"},
				{id:"caja_crear_addrowbutton2<?php echo $NumOfID; ?>", type:"jqxButton"},
				{id:"caja_crear_deleterowbutton2<?php echo $NumOfID; ?>", type:"jqxButton"},
				//--- Parte 3
				{id:"caja_crear_aplicado_a<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_observaciones<?php echo $NumOfID; ?>", type:""},
				{id:"caja_crear_guardar<?php echo $NumOfID; ?>", type:"jqxButton"},
				{id:"caja_crear_nuevo<?php echo $NumOfID; ?>", type:"jqxButton"},
				{id:"caja_crear_aplicar<?php echo $NumOfID; ?>", type:"jqxButton"},
				{id:"caja_crear_crear_tercero<?php echo $NumOfID; ?>", type:"jqxButton"},
				//{id:"caja_crear_impresora", type:"jqxButton"},
			];
		}
	}
	ReDefine();
	
	//---------------------------------------------- PARTE 1 ----------------------------------------------\\
	
	function LoadValues ()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Fecha', type: 'string'},
				{ name: 'Caja_Interno', type: 'string'},
				{ name: 'Caja_Recibo', type: 'string'},
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
				//--
				{ name: 'Bancos', type: 'string'},
				{ name: 'Cheques', type: 'string' },
			],
			data:{
				"Datos_Caja":true,
				"Caja_Interno":"<?php echo $Caja_Interno; ?>",
			},
			url: "modulos/datos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				Locked = true;
				var records = GetValuesAdapter.records;
				if (records[0]["Caja_Recibo"] == undefined)
				{
					Alerts_Box("El Recibo Ingresado, no posee datos...", 4);
					Locked = false;
					return;
				}
				CxP = records[0]["CxP"];
				$("#caja_crear_caja_interno<?php echo $NumOfID; ?>").val("<?php echo $Caja_Interno; ?>");
				$("#caja_crear_recibo<?php echo $NumOfID; ?>").val(records[0]["Caja_Recibo"]);
				$("#caja_crear_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#caja_crear_tipo_mov<?php echo $NumOfID; ?>").val(records[0]["Categoria"]);
				$("#caja_crear_grupo<?php echo $NumOfID; ?>").val(records[0]["Grupo"]);
				$("#caja_crear_subgrupo<?php echo $NumOfID; ?>").val(records[0]["SubGrupo"]);
				$("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").val(records[0]["SubGrupo2"]);
				$("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('selectItem', records[0]["ClienteID"]);
				//$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val(records[0]["ClienteID"]);
				$("#caja_crear_aplicado_a<?php echo $NumOfID; ?>").val(records[0]["Aplicado_A"]);
				$("#caja_crear_observaciones<?php echo $NumOfID; ?>").val(records[0]["Observaciones"]);
				$("#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>").val(records[0]["Efectivo"]);
				$("#caja_crear_rete_iva_total<?php echo $NumOfID; ?>").val(records[0]["Rete_IVA"]);
				$("#caja_crear_rete_ica_total<?php echo $NumOfID; ?>").val(records[0]["Rete_ICA"]);
				$("#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>").val(records[0]["Rete_Fuente"]);
				$("#caja_crear_otro_dcto<?php echo $NumOfID; ?>").val(records[0]["Descuento"]);
				$("#caja_crear_dcto_concepto<?php echo $NumOfID; ?>").val(records[0]["Descuento_Concepto"]);
				
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
						$("#caja_crear_items_grid1<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
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
						$("#caja_crear_items_grid2<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
					}
				}
				Calcular();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
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
		var BoxDataAdapter = new $.jqx.dataAdapter(BoxSource,
		{
			autoBind: true,
			loadComplete: function ()
			{
				var myrecords = BoxDataAdapter.records;
				$("#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>").val(myrecords[0]["Saldo"]);
				$("#caja_crear_d_prov_valor<?php echo $NumOfID; ?>").val(myrecords[0]["Saldo"]);
				$("#caja_crear_grupo<?php echo $NumOfID; ?>").val(myrecords[0]["Grupo"]);
				SubGrupo = myrecords[0]["SubGrupo"];
				SubGrupo2 = myrecords[0]["SubGrupo2"];
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	$("#caja_crear_caja_interno<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 170,
		disabled: true
	});
	
	$("#caja_crear_recibo<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 170,
	});
	$("#caja_crear_recibo<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if ("<?php echo $Caja_Interno ?>" == "")
		{
			var value = $("#caja_crear_recibo<?php echo $NumOfID; ?>").val();
			if (value == "")
			{
				$("#caja_crear_recibo<?php echo $NumOfID; ?>").removeClass("jqx-validator-error-element");
				ID_Incorrecto = false;
				return;
			}
			
			var FindSource =
			{
				datatype: "json",
				datafields: [
					{ name: 'Same', type: 'bool'},
				],
				data: {
					"Caja_Check_SameID":value,
				},
				url: "modulos/datos.php",
			};
			var FindDataAdapter = new $.jqx.dataAdapter(FindSource,{
				autoBind: true,
				loadComplete: function ()
				{	
					var records = FindDataAdapter.records;
					if (records[0]["Same"] == true)
					{
						$("#caja_crear_recibo<?php echo $NumOfID; ?>").addClass("jqx-validator-error-element");
						ID_Incorrecto = true;
						Alerts_Box("El Recibo Ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
						WaitClick_Input("caja_crear_recibo<?php echo $NumOfID; ?>");
					}
					else
					{
						$("#caja_crear_recibo<?php echo $NumOfID; ?>").removeClass("jqx-validator-error-element");
						ID_Incorrecto = false;
					}
				}
			});
		}
	});
	
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
	
	$("#caja_crear_tipo_mov<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#caja_crear_tipo_mov<?php echo $NumOfID; ?>").bind('change', function (event)
	{
		if (event.args)
		{
			<?php 
			if (!isset($_POST["Crear_Emergente"]))
			{
			?>
				$("#caja_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox({ disabled: false});
				GrupoSource.data = {"Caja_Grupo": event.args.item.value};
				GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
				$("#caja_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox({source: GrupoAdapter});
			<?php
			}
			?>
			
			if (event.args.item.value == "Egresos")
			{
				$("#caja_crear_tipo_change<?php echo $NumOfID; ?>").html("Valor a Pagar");
				ClearFields();
				Active = false;
				$("#caja_crear_rete_iva<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false});
				$("#caja_crear_rete_ica<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false});
				$("#caja_crear_rete_fuente<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false});
				$("#caja_crear_rete_iva_total<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true});
				$("#caja_crear_rete_ica_total<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true});
				$("#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true});
				//$("#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true});
				$("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox({ disabled: false});
				//---
				$("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid({ disabled: true});
				$("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid({ disabled: true});
				$("#caja_crear_addrowbutton1<?php echo $NumOfID; ?>").jqxButton({ disabled: true});
				$("#caja_crear_deleterowbutton1<?php echo $NumOfID; ?>").jqxButton({ disabled: true});
				$("#caja_crear_addrowbutton2<?php echo $NumOfID; ?>").jqxButton({ disabled: true});
				$("#caja_crear_deleterowbutton2<?php echo $NumOfID; ?>").jqxButton({ disabled: true});
			}
			else
			{
				$("#caja_crear_tipo_change<?php echo $NumOfID; ?>").html("Recaudo en Efectivo");
				ClearFields();
				Active = true;
				$("#caja_crear_rete_iva<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true});
				$("#caja_crear_rete_ica<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true});
				$("#caja_crear_rete_fuente<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true});
				$("#caja_crear_rete_iva_total<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false});
				$("#caja_crear_rete_ica_total<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false});
				$("#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false});
				//$("#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false});
				$("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox({ disabled: true});
				//---
				$("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid({ disabled: false});
				$("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid({ disabled: false});
				$("#caja_crear_addrowbutton1<?php echo $NumOfID; ?>").jqxButton({ disabled: false});
				$("#caja_crear_deleterowbutton1<?php echo $NumOfID; ?>").jqxButton({ disabled: false});
				$("#caja_crear_addrowbutton2<?php echo $NumOfID; ?>").jqxButton({ disabled: false});
				$("#caja_crear_deleterowbutton2<?php echo $NumOfID; ?>").jqxButton({ disabled: false});
			}
		}
		else
			$("#caja_crear_tipo_mov<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
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
	
	$("#caja_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#caja_crear_grupo<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				SubGrupoSource.data = {"Caja_SubGrupo": event.args.item.value};
				SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				$("#caja_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox({source: SubGrupoDataAdapter});
				SubGrupo2Source.data = {"Caja_SubGrupo2": event.args.item.value};
				SubGrupo2DataAdapter = new $.jqx.dataAdapter(SubGrupo2Source);
				$("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox({source: SubGrupo2DataAdapter});
			},300);
		}
		else
		{
			$("#caja_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			$("#caja_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			$("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
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
	
	$("#caja_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar SubGrupo',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
	});
	$("#caja_crear_subgrupo<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (!event.args)
			$("#caja_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
	});
	$("#caja_crear_subgrupo<?php echo $NumOfID; ?>").bind('bindingComplete', function (event)
	{
		if (SubGrupo != "")
		{
			$("#caja_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('selectItem', SubGrupo);
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
	
	$("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar SubGrupo 2',
		selectedIndex: -1,
		displayMember: 'SubGrupo2',
		valueMember: 'SubGrupo2',
	});
	$("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (!event.args)
			$("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
	});
	$("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").bind('bindingComplete', function (event)
	{
		if (SubGrupo2 != "")
		{
			$("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('selectItem', SubGrupo2);
			SubGrupo2 = "";
		}
	});
	
	$("#caja_crear_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 170,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$("#caja_crear_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	
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
	
	$("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#caja_crear_cliente<?php echo $NumOfID; ?>").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#caja_crear_cliente<?php echo $NumOfID; ?>").val();
				
				var item = $("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
		}
	});

	$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").bind('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#caja_crear_cliente<?php echo $NumOfID; ?>").val() != event.args.item.value)
					$("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
				
				CxPSource.data = {"CxP_Deudor": event.args.item.value};
				var CxPDataAdapter = new $.jqx.dataAdapter(CxPSource);
				$("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox({source: CxPDataAdapter});
			},350);
		}
		else
		{
			var value = $("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val();
			var item = $("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('getItemByValue', value);
			if (item == undefined)
			{
				$("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
			else
				$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('selectItem', item.value);
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
	
	$("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#caja_crear_d_prov<?php echo $NumOfID; ?>").bind('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				Cargar_CxP(event.args.item.label, event.args.item.value);
			},350);
		}
		else
		{
			ClearFields();
			$("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
	});
	$("#caja_crear_d_prov<?php echo $NumOfID; ?>").on('bindingComplete', function (event)
	{
		if (CxP != "")
		{
			<?php if(isset($_POST["Crear_Emergente"])) ?>
			$("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox({ disabled: true });
			$("#caja_crear_d_prov<?php echo $NumOfID; ?>").val(CxP);
			CxP = "";
		}
	});
	document.getElementById("caja_crear_d_prov<?php echo $NumOfID; ?>").addEventListener("dblclick", function (event)
	{
		var Disable = $("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox("disabled");
		if (!Disable)
		{
			var Cliente = $("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			var TmpGrupo = $("#caja_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			var TmpSubGrupo = $("#caja_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			var Factura = $("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			
			if (!Cliente)
			{
				Alerts_Box("Debe Seleccionar un Cliente.", 3);
				WaitClick_Combobox("caja_crear_cliente<?php echo $NumOfID; ?>");
				return;
			}
			
			if (!TmpGrupo)
			{
				Alerts_Box("Debe Seleccionar un Grupo.", 3);
				WaitClick_Combobox("caja_crear_grupo<?php echo $NumOfID; ?>");
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
					WaitClick_Combobox("caja_crear_subgrupo<?php echo $NumOfID; ?>");
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
					WaitClick_Combobox("caja_crear_d_prov<?php echo $NumOfID; ?>");
				}
				$("#Caja_Crear_Maquinaria_Window<?php echo $NumOfID ?>").jqxWindow('open');
			}
			else if ( Result1 >= 0 && Result4 >= 0 || Result2 >= 0 && Result4 >= 0)
			{
				if (!Factura)
				{
					Alerts_Box("Debe Seleccionar una Factura.", 3);
					WaitClick_Combobox("caja_crear_d_prov<?php echo $NumOfID; ?>");
				}
				$("#Caja_Crear_Maquinaria_Window<?php echo $NumOfID ?>").jqxWindow('open');
			}
			else
			{
				$("#Caja_Crear_CxP_Window<?php echo $NumOfID ?>").jqxWindow('open');
			}
		}
	});
	
	$("#caja_crear_d_prov_valor<?php echo $NumOfID; ?>").jqxNumberInput({
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
	
	$("#caja_crear_caja<?php echo $NumOfID; ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 225,
		theme: mytheme,
		//source: ,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Caja',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID',
		disabled: true,
	});
	
	$("#caja_crear_caja_saldo<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 200,
		disabled: true,
	});
	
	//---------------------------------------------- PARTE 2 ----------------------------------------------\\
	// --- GLOVAL VALUES
	
	
	//--------- LEFT
	$("#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999
	});
	$('#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>').on('valueChanged', function (event)
	{
		if ($("#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>").jqxNumberInput('disabled') == true)
			DoTheMath();
	});
	$('#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_crear_efectivo<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		disabled: true
	});
	$("#caja_crear_transferencias<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$("#caja_crear_subtotal_cheques<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$("#caja_crear_rete_iva<?php echo $NumOfID; ?>").jqxNumberInput({
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
	$('#caja_crear_rete_iva<?php echo $NumOfID; ?>').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_crear_rete_iva_total<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$('#caja_crear_rete_iva_total<?php echo $NumOfID; ?>').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_crear_rete_ica<?php echo $NumOfID; ?>").jqxNumberInput({
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
	$('#caja_crear_rete_ica<?php echo $NumOfID; ?>').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_crear_rete_ica_total<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$('#caja_crear_rete_ica_total<?php echo $NumOfID; ?>').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_crear_rete_fuente<?php echo $NumOfID; ?>").jqxNumberInput({
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
	$('#caja_crear_rete_fuente<?php echo $NumOfID; ?>').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		disabled: true
	});
	$('#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>').on('change', function (event) 
	{
		DoTheMath();
	});
	
	$("#caja_crear_otro_dcto<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
	});
	$('#caja_crear_otro_dcto<?php echo $NumOfID; ?>').on('change', function (event) 
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
	
	$("#caja_crear_dcto_concepto<?php echo $NumOfID; ?>").jqxComboBox({
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
	
	$("#caja_crear_total<?php echo $NumOfID; ?>").jqxNumberInput({
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
		var caja_crear_efectivo = parseFloat($('#caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>').val());
		var caja_crear_transferencias = parseFloat($('#caja_crear_transferencias<?php echo $NumOfID; ?>').val());
		var cheques = parseFloat($('#caja_crear_subtotal_cheques<?php echo $NumOfID; ?>').val());
		var caja_crear_otro_dcto = parseFloat($('#caja_crear_otro_dcto<?php echo $NumOfID; ?>').val());
		
		var iva_percent = parseFloat($('#caja_crear_rete_iva<?php echo $NumOfID; ?>').val());
		var ica_percent = parseFloat($('#caja_crear_rete_ica<?php echo $NumOfID; ?>').val());
		var fuente_percent = parseFloat($('#caja_crear_rete_fuente<?php echo $NumOfID; ?>').val());
		var iva_percent_total = parseFloat($('#caja_crear_rete_iva_total<?php echo $NumOfID; ?>').val());
		var ica_percent_total = parseFloat($('#caja_crear_rete_ica_total<?php echo $NumOfID; ?>').val());
		var fuente_percent_total = parseFloat($('#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>').val());
		var total_percent = 0;
		
		if (Active)
		{
			total_percent = iva_percent_total + ica_percent_total + fuente_percent_total;
			Total = caja_crear_efectivo + caja_crear_transferencias + cheques + total_percent + caja_crear_otro_dcto;
			$('#caja_crear_efectivo<?php echo $NumOfID; ?>').val(caja_crear_efectivo);
			$('#caja_crear_total<?php echo $NumOfID; ?>').val(Total);
		}
		else
		{
			iva_percent_total = Math.round(caja_crear_efectivo / 100 * parseFloat(iva_percent));
			ica_percent_total = Math.round(caja_crear_efectivo / 100 * parseFloat(ica_percent));
			fuente_percent_total = Math.round(caja_crear_efectivo / 100 * parseFloat(fuente_percent));
			total_percent = iva_percent_total + ica_percent_total + fuente_percent_total;
			Total = caja_crear_efectivo + caja_crear_otro_dcto;
			$('#caja_crear_efectivo<?php echo $NumOfID; ?>').val(caja_crear_efectivo - total_percent);
			$('#caja_crear_rete_iva_total<?php echo $NumOfID; ?>').val(iva_percent_total);
			$('#caja_crear_rete_ica_total<?php echo $NumOfID; ?>').val(ica_percent_total);
			$('#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>').val(fuente_percent_total);
			$('#caja_crear_total<?php echo $NumOfID; ?>').val(Total);
		}
	};
	
	function Calcular ()
	{
		var ValorGrid1 = 0;
		var ValorGrid2 = 0;
		var information1 = $('#caja_crear_items_grid1<?php echo $NumOfID; ?>').jqxGrid('getdatainformation');
		var rowscounts1 = information1.rowscount;
		var information2 = $('#caja_crear_items_grid2<?php echo $NumOfID; ?>').jqxGrid('getdatainformation');
		var rowscounts2 = information2.rowscount;
		var old_total1 = 0;
		var old_total2 = 0;
		for (i1=0; i1<rowscounts1; i1++){
			var currentRow1 = $('#caja_crear_items_grid1<?php echo $NumOfID; ?>').jqxGrid('getrowdata', i1);
			old_total1 = old_total1 + parseFloat(currentRow1.Valor);
		};
		for (i2=0; i2<rowscounts2; i2++){
			var currentRow2 = $('#caja_crear_items_grid2<?php echo $NumOfID; ?>').jqxGrid('getrowdata', i2);
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
			$('#caja_crear_transferencias<?php echo $NumOfID; ?>').val(ValorGrid1);
			$('#caja_crear_subtotal_cheques<?php echo $NumOfID; ?>').val(ValorGrid2);
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
	
	$("#caja_crear_tipo<?php echo $NumOfID; ?>").jqxComboBox({
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
	
	$("#caja_crear_valor<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 165,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999999
	});
	
	$("#caja_crear_num_consignacion<?php echo $NumOfID; ?>").jqxInput({
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
	
	$("#caja_crear_banco<?php echo $NumOfID; ?>").jqxComboBox({
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
	
	$("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid({
		theme: mytheme,
		width: 645,
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
				'<input type="button" id="caja_crear_addrowbutton1<?php echo $NumOfID; ?>" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="caja_crear_deleterowbutton1<?php echo $NumOfID; ?>" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#caja_crear_addrowbutton1<?php echo $NumOfID; ?>").jqxButton({theme: mytheme, template: "success"});
			$("#caja_crear_deleterowbutton1<?php echo $NumOfID; ?>").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#caja_crear_addrowbutton1<?php echo $NumOfID; ?>").on('click', function () {
				Add_Row1();
			});
			// delete row.
			$("#caja_crear_deleterowbutton1<?php echo $NumOfID; ?>").on('click', function () {
				var selectedrowindex = $("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid('getselectedrowindex');
				var rowscount = $("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Tipo', datafield: 'Tipo', editable: false, width: '20%', height: 20 },
			//{ text: 'Valor', datafield: 'Valor', editable: false, width: '30%', height: 20 },
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
			{ text: 'Banco Destino', datafield: 'Banco', editable: false, width: '25%', height: 20, cellsalign: 'caja_crear_right' }
		]
	});
	
	function Add_Row1()
	{
		if (Active == false)
			return;
			
		var TipoPago = $('#caja_crear_tipo<?php echo $NumOfID; ?>').val();
		var TipoBanco = $('#caja_crear_banco<?php echo $NumOfID; ?>').val();
		var Valor = $("#caja_crear_valor<?php echo $NumOfID; ?>").val();
		var NumConsignacion = $("#caja_crear_num_consignacion<?php echo $NumOfID; ?>").val();
		
		if (! TipoPago | TipoPago <= 0) {
			Alerts_Box("Favor Seleccionar un Tipo de Pago!", 4);
			WaitClick_Combobox("caja_crear_tipo<?php echo $NumOfID; ?>");
			return;
		}
		
		if (Valor <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 4);
			WaitClick_NumberInput("caja_crear_valor<?php echo $NumOfID; ?>");
			return;
		}
		
		if (NumConsignacion <= 0 | NumConsignacion == "") {
			Alerts_Box("Debe Ingresar un Numero de Consignacion/Aprobacion!", 4);
			WaitClick_Input("caja_crear_num_consignacion<?php echo $NumOfID; ?>");
			return;
		}
		
		if (! TipoBanco | TipoBanco <= 0) {
			Alerts_Box("Favor Seleccionar un Banco!", 4);
			WaitClick_Combobox("caja_crear_banco<?php echo $NumOfID; ?>");
			return;
		}
		
		var datarow = [{
			"Tipo":TipoPago,
			"Valor":Valor,
			"Consignacion":NumConsignacion,
			"Banco":TipoBanco
		}];
		
		$("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
		// Clear Values
		$('#caja_crear_tipo<?php echo $NumOfID; ?>').jqxComboBox('clearSelection');
		$('#caja_crear_valor<?php echo $NumOfID; ?>').val('');
		$('#caja_crear_num_consignacion<?php echo $NumOfID; ?>').val('');
		$('#caja_crear_banco<?php echo $NumOfID; ?>').jqxComboBox('clearSelection');
		Calcular();
	};
	
	//--------- RIGHT
	
	$("#caja_crear_num_cheque<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
	});
	
	$("#caja_crear_valor_cheque<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		textAlign: 'right',
		symbol: '$',
		digits: 15,
		max: 999999999999999999
	});
	
	/*
	$("#caja_crear_banco_cheque<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 35,
	});
	*/
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
	
	$("#caja_crear_banco_cheque<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#caja_crear_banco_cheque<?php echo $NumOfID; ?>").bind('change', function (event)
	{
		if (!event.args)
			$("#caja_crear_banco_cheque<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
	});
	
	$("#caja_crear_num_cuenta<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
	});
	
	$("#caja_crear_estado_fecha<?php echo $NumOfID; ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 95,
		promptText: 'Seleccionar',
		selectedIndex: -1,
	});
	
	$("#caja_crear_estado_fecha<?php echo $NumOfID; ?>").jqxComboBox('addItem', {label: "Al Dia", value: "Al Dia"});
	$("#caja_crear_estado_fecha<?php echo $NumOfID; ?>").jqxComboBox('addItem', {label: "PostFechado", value: "PostFechado"});
	
	$("#caja_crear_estado_fecha<?php echo $NumOfID; ?>").bind('select', function (event) {
		if (event.args) {
			if (event.args.item.value == "PostFechado"){
				$("#caja_crear_cheque_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput({ disabled: false});
			}
			else {
				$("#caja_crear_cheque_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput({ disabled: true});
			}
		}
	});
	
	$("#caja_crear_cheque_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput({
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
	
	$("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid({
		theme: mytheme,
		width: 645,
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
				'<input type="button" id="caja_crear_addrowbutton2<?php echo $NumOfID; ?>" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="caja_crear_deleterowbutton2<?php echo $NumOfID; ?>" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#caja_crear_addrowbutton2<?php echo $NumOfID; ?>").jqxButton({theme: mytheme, template: "success"});
			$("#caja_crear_deleterowbutton2<?php echo $NumOfID; ?>").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#caja_crear_addrowbutton2<?php echo $NumOfID; ?>").on('click', function () {
				Add_Row2();
			});
			// delete row.
			$("#caja_crear_deleterowbutton2<?php echo $NumOfID; ?>").on('click', function () {
				var selectedrowindex = $("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid('getselectedrowindex');
				var rowscount = $("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid('deleterow', id);
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
			{ text: 'Num. Cuenta', datafield: 'NumCuenta', editable: false, width: '20%', height: 20, cellsalign: 'caja_crear_right' },
			{ text: 'Estado Fecha', datafield: 'EstadoFecha', editable: false, width: '15%', height: 20, cellsalign: 'caja_crear_right' },
			{
				text: 'Fecha Cheque',
				datafield: 'FechaCheque',
				columntype: 'datetimeinput',
				editable: false,
				width: '15%',
				height: 20,
				cellsalign: 'caja_crear_right',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
		]
	});
	
	function Add_Row2()
	{
		if (Active == false)
			return;
			
		var Cheque = $('#caja_crear_num_cheque<?php echo $NumOfID; ?>').val();
		var ValorCheque = $('#caja_crear_valor_cheque<?php echo $NumOfID; ?>').val();
		var BancoCheque = $("#caja_crear_banco_cheque<?php echo $NumOfID; ?>").val();
		var NumCuenta = $("#caja_crear_num_cuenta<?php echo $NumOfID; ?>").val();
		var EstadoFecha = $('#caja_crear_estado_fecha<?php echo $NumOfID; ?>').val();
		var FechaCheque = GetFormattedDate($('#caja_crear_cheque_fecha<?php echo $NumOfID; ?>').jqxDateTimeInput('getDate'));
		
		if (!Cheque | Cheque == "") {
			Alerts_Box("Debe Ingresar un Numero de Cheque!", 4);
			WaitClick_Input("caja_crear_num_cheque<?php echo $NumOfID; ?>");
			return;
		}
		
		if (ValorCheque <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 4);
			WaitClick_NumberInput("caja_crear_valor_cheque<?php echo $NumOfID; ?>");
			return;
		}
		
		if (!BancoCheque | BancoCheque == "") {
			Alerts_Box("Debe Ingresar un Numero de Banco!", 4);
			WaitClick_Combobox("caja_crear_banco_cheque<?php echo $NumOfID; ?>");
			return;
		}
		
		if (!NumCuenta | NumCuenta == "") {
			Alerts_Box("Debe Ingresar un Numero de Cuenta!", 4);
			WaitClick_Input("caja_crear_num_cuenta<?php echo $NumOfID; ?>");
			return;
		}
		
		if (!EstadoFecha | EstadoFecha <= 0) {
			Alerts_Box("Favor Seleccionar un Estado de Fecha", 4);
			WaitClick_Combobox("caja_crear_estado_fecha<?php echo $NumOfID; ?>");
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
		
		$("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
		// Clear Values
		$('#caja_crear_num_cheque<?php echo $NumOfID; ?>').val('');
		$('#caja_crear_valor_cheque<?php echo $NumOfID; ?>').val('');
		$('#caja_crear_banco_cheque<?php echo $NumOfID; ?>').jqxComboBox('clearSelection');
		$('#caja_crear_num_cuenta<?php echo $NumOfID; ?>').val('');
		$('#caja_crear_estado_fecha<?php echo $NumOfID; ?>').jqxComboBox('clearSelection');
		//$('#fecha_cheque').jqxDateTimeInput('getText');
		Calcular();
	};
	//---------------------------------------------- PARTE 3 ----------------------------------------------\\
	
	function Save()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ID_Incorrecto) {
			Alerts_Box("El Recibo Ingresado, ya Existe!<br />Favor Ingresar Otro para Continuar.", 3);
			Locked = false;
			return;
		}
		
		var datinfo1 = $("#caja_crear_items_grid1<?php echo $NumOfID; ?>").jqxGrid('getdatainformation');
		var datinfo2 = $("#caja_crear_items_grid2<?php echo $NumOfID; ?>").jqxGrid('getdatainformation');
		var count1 = datinfo1.rowscount;
		var count2 = datinfo2.rowscount;
		
		var myarray = new Array();
		var myarray1 = new Array();
		var myarray2 = new Array();
		
		var Tipo = $("#caja_crear_tipo_mov<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
		var Grupo = $("#caja_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
		var SubGrupo = $("#caja_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
		var SubGrupo2 = $("#caja_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
		var Cliente = $("#caja_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
		
		if (!Tipo)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Movimiento.", 3);
			WaitClick_Combobox("caja_crear_tipo_mov<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if (Tipo.value == "Egresos" && Grupo.value != "Nómina")
		{
			var Factura = $("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			if (!Factura)
			{
				Alerts_Box("Debe Seleccionar una Factura(CxP).", 3);
				WaitClick_Combobox("caja_crear_d_prov<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
		}
		
		if (!Grupo)
		{
			Alerts_Box("Debe Ingresar un Grupo!", 3);
			WaitClick_Combobox("caja_crear_grupo<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if (!SubGrupo)
		{
			Alerts_Box("Debe Ingresar un SubGrupo!", 3);
			WaitClick_Combobox("caja_crear_subgrupo<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if (!SubGrupo2)
		{
			Alerts_Box("Debe Ingresar un SubGrupo Nivel 2!", 3);
			WaitClick_Combobox("caja_crear_subgrupo2<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if (!Cliente)
		{
			Alerts_Box("Debe Ingresar un Cliente!", 3);
			WaitClick_Combobox("caja_crear_cliente<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if ($("#caja_crear_otro_dcto<?php echo $NumOfID; ?>").val() > 0)
		{
			if ($("#caja_crear_dcto_concepto<?php echo $NumOfID; ?>").val() <= 0 | $("#caja_crear_dcto_concepto<?php echo $NumOfID; ?>").val() == "") {
				Alerts_Box("Ha ingresado un descuento sin especificar el Concepto!", 3);
				WaitClick_Combobox("caja_crear_dcto_concepto<?php echo $NumOfID; ?>");
				Locked = false;
				return;
			}
		}
		
		if ($("#caja_crear_total<?php echo $NumOfID; ?>").val() <= 0 | $("#caja_crear_total<?php echo $NumOfID; ?>").val() == "") {
			Alerts_Box("Debe Ingresar el Efectivo", 3);
			WaitClick();
			Locked = false;
			return;
		}

		// Grid1
		for (a1 = 0; a1 < count1; a1++){
			var array1 = {};
			var currentRow1 = $('#caja_crear_items_grid1<?php echo $NumOfID; ?>').jqxGrid('getrowdata', a1);
			
			array1["Tipo1"] = currentRow1.Tipo;
			array1["Valor1"] = currentRow1.Valor;
			array1["Num1"] = currentRow1.Consignacion;
			array1["Banco1"] = currentRow1.Banco;
			myarray1[a1] = array1;
		}
		// Grid2
		for (a2 = 0; a2 < count2; a2++){
			var array2 = {};
			var currentRow2 = $('#caja_crear_items_grid2<?php echo $NumOfID; ?>').jqxGrid('getrowdata', a2);
			
			array2["ChequeNum"] = currentRow2.NumCheque;
			array2["Valor2"] = currentRow2.Valor;
			array2["Banco2"] = currentRow2.NumBanco;
			array2["Num2"] = currentRow2.NumCuenta;
			array2["EstadoFecha"] = currentRow2.EstadoFecha;
			array2["FechaCheque"] = currentRow2.FechaCheque;
			myarray2[a2] = array2;
		}
		var array = {};
		var CxP_Values = $("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
		
		array["ClienteID"] = Cliente.value;
		array["Fecha"] = GetFormattedDate($('#caja_crear_fecha<?php echo $NumOfID; ?>').jqxDateTimeInput('getDate'));
		array["ReciboCaja"] = $('#caja_crear_recibo<?php echo $NumOfID; ?>').val();
		array["CxP_Factura"] = CxP_Values ? CxP_Values.label:"";
		array["CxP_Interno"] = CxP_Values ? CxP_Values.value:"";
		array["Tipo"] = Tipo.value;
		array["Grupo"] = Grupo.value;
		array["SubGrupo"] = SubGrupo.value;
		array["SubGrupo2"] = SubGrupo2.value;
		array["Efectivo"] = $('#caja_crear_efectivo<?php echo $NumOfID; ?>').val();
		array["Cheque"] = $('#caja_crear_subtotal_cheques<?php echo $NumOfID; ?>').val();
		array["Consignacion"] = $('#caja_crear_transferencias<?php echo $NumOfID; ?>').val();
		array["ReteIva"] = $('#caja_crear_rete_iva_total<?php echo $NumOfID; ?>').val();
		array["ReteIca"] = $('#caja_crear_rete_ica_total<?php echo $NumOfID; ?>').val();
		array["ReteFuente"] = $('#caja_crear_rete_fuente_total<?php echo $NumOfID; ?>').val();
		array["Descuento"] = $('#caja_crear_otro_dcto<?php echo $NumOfID; ?>').val();
		array["ConceptoDcto"] = $('#caja_crear_dcto_concepto<?php echo $NumOfID; ?>').val();
		array["Total"] = $('#caja_crear_total<?php echo $NumOfID; ?>').val();
		array["Observaciones"] = $('#caja_crear_observaciones<?php echo $NumOfID; ?>').val();
		
		myarray[0] = array;
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		
		$.ajax({
			dataType: 'text',
			data: {
				"Caja_Crear":myarray,
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
				ClickOK = false;
				ClickCANCEL = false;
				document.getElementById("ok").value = "Nuevo";
				document.getElementById("cancel").value = "Aceptar";
				Alerts_Box("Datos Guardados con Exito!<br />Codigo Interno Generado = \""+data+"\"<br />Haga Click en Aceptar para Limpiar los campos.", 2, true);
				$("#caja_crear_caja_interno<?php echo $NumOfID; ?>").val(data);
				Caja_Interno = data;
				EnableDisableAll(true);
				Timer1 = setInterval(function()
				{
					if (ClickOK == true)
					{
						document.getElementById("ok").value = "Aceptar";
						document.getElementById("cancel").value = "Cancelar";
						//Conflicto con la Limpieza.
						/*
						if ($("#caja_crear_d_prov<?php echo $NumOfID; ?>").val() == "") {
							if ($("#caja_crear_grupo<?php echo $NumOfID; ?>").val() == "Cliente" && $("#caja_crear_subgrupo<?php echo $NumOfID; ?>").val() == "Cartera") {
								WaitClick_WindowOpen("Caja_Crear_Apply_Window<?php echo $NumOfID; ?>");
								$("#Loading").show();
							}
						}
						*/
						ClearAll();
						EnableDisableAll(false);
						ClickOK = false;
						clearInterval(Timer1);
						clearTimeout(Timer2);
						var Disabled = $("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('disabled'); 
						if (Disabled)
						{
							$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid({ disabled: false });
							$("#caja_crear_apply<?php echo $NumOfID; ?>").jqxButton({ disabled: false });
						}
					}
					else if (ClickCANCEL == true)
					{
						document.getElementById("ok").value = "Aceptar";
						document.getElementById("cancel").value = "Cancelar";
						if ($("#caja_crear_d_prov<?php echo $NumOfID; ?>").val() == "") {
							if ($("#caja_crear_grupo<?php echo $NumOfID; ?>").val() == "Cliente" && $("#caja_crear_subgrupo<?php echo $NumOfID; ?>").val() == "Cartera") {
								WaitClick_WindowOpen("Caja_Crear_Apply_Window<?php echo $NumOfID; ?>");
								$("#Loading").show();
							}
						}
						clearInterval(Timer1);
						clearTimeout(Timer2);
					}
				},10);
				Timer2 = setTimeout(function()
				{
					$("#Ask_Alert").jqxWindow('close');
					clearInterval(Timer1);
					ClickOK = false;
					ClickCANCEL = true;
					document.getElementById("ok").value = "Aceptar";
					document.getElementById("cancel").value = "Cancelar";
					if ($("#caja_crear_d_prov<?php echo $NumOfID; ?>").val() == "") {
						if ($("#caja_crear_grupo<?php echo $NumOfID; ?>").val() == "Cliente" && $("#caja_crear_subgrupo<?php echo $NumOfID; ?>").val() == "Cartera") {
							WaitClick_WindowOpen("Caja_Crear_Apply_Window<?php echo $NumOfID; ?>");
							$("#Loading").show();
						}
					}
				},10000);
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
	
	$('#caja_crear_guardar<?php echo $NumOfID; ?>').jqxButton({
		width: 150,
		height: 35,
		template: "info"
	});
	
	$("#caja_crear_guardar<?php echo $NumOfID; ?>").bind('click', function ()
	{
		Save();
	});
	$('#caja_crear_nuevo<?php echo $NumOfID; ?>').jqxButton({width: 150, height: 35, template: "success"});
	$("#caja_crear_nuevo<?php echo $NumOfID; ?>").bind('click', function ()
	{
		ReDefine();
		ClearAll();
		EnableDisableAll(false);
		var Disabled = $("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('disabled'); 
		if (Disabled)
		{
			$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid({ disabled: false });
			$("#caja_crear_apply<?php echo $NumOfID; ?>").jqxButton({ disabled: false });
		}
	});
	$("#caja_crear_imprimir<?php echo $NumOfID; ?>").jqxButton({width: 150, height: 35, template: "warning"});
	$("#caja_crear_imprimir<?php echo $NumOfID; ?>").bind('click', function ()
	{
		window.open("imprimir/caja_recibo.php?Caja_Interno="+$("#caja_crear_caja_interno<?php echo $NumOfID ?>").val()+"", "", "width=730, height=600, menubar=no, titlebar=no");
	});
	
	$("#caja_crear_aplicar<?php echo $NumOfID; ?>").jqxButton({width: 150, height: 35, template: "primary"});
	$("#caja_crear_aplicar<?php echo $NumOfID; ?>").bind('click', function ()
	{
		if ($("#caja_crear_caja_interno<?php echo $NumOfID; ?>").val() == "")
		{
			Alerts_Box("Debe Crear un Recibo.", 3);
			return;
		}
		WaitClick_WindowOpen("Caja_Crear_Apply_Window<?php echo $NumOfID; ?>");
		$("#Loading").show();
	});
	
	$("#caja_crear_crear_tercero<?php echo $NumOfID; ?>").jqxButton({width: 150, height: 35, template: "inverse"});
	$("#caja_crear_crear_tercero<?php echo $NumOfID; ?>").bind('click', function ()
	{
		$("#Caja_Crear_Tercero_Window<?php echo $NumOfID ?>").jqxWindow('open');
	});
	
	$('#caja_crear_impresora<?php echo $NumOfID; ?>').jqxButton({width: 150, height: 35, template: "warning"});
	$("#caja_crear_impresora<?php echo $NumOfID; ?>").bind('click', function ()
	{
		window.open("imprimir/caja_recibo_a_punto.php?Caja_Interno="+$("#caja_crear_caja_interno<?php echo $NumOfID ?>").val()+"", "", "width=730, height=600, menubar=no, titlebar=no");
		//WaitClick_WindowOpen("Caja_Crear_Apply_Window<?php echo $NumOfID; ?>");
		//$("#Loading").show();
	});
	
	function Guardar2 ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var datinfo = $("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		
		var myarray = new Array();
		var a = 0;
		for (i = 0; i < count; i++)
		{
			var currentRow = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getrowdata', i);
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
			array["Remision"] = currentRow.Remision;
			array["Factura"] = currentRow.Factura;
			array["Ord_Compra"] = currentRow.Ord_Compra;
			array["Valor"] = currentRow.Valor;
			array["Saldo"] = currentRow.Saldo;
			array["Abono"] = currentRow.Abono;
			
			var Saldo_Nuevo = 0;
			if ( currentRow.Saldo > 0)
				Saldo_Nuevo = parseFloat(currentRow.Saldo) - parseFloat(currentRow.Abono);
			else
				Saldo_Nuevo = parseFloat(currentRow.Valor) - parseFloat(currentRow.Abono);
			
			array["Saldo_Nuevo"] = Saldo_Nuevo;
			
			if (a==0)
			{
				array["ClienteID"] = $("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val();
				array["CajaInterno"] = Caja_Interno;
				array["ReciboCaja"] = $('#caja_crear_recibo<?php echo $NumOfID; ?>').val();
				array["Sobrante"] = $('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val();
				array["Total"] = $('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val();
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
			dataType: 'text',
			data: {"Caja_Crear2":myarray},
			url: "modulos/guardar.php",
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				$("#caja_crear_aplicado_a<?php echo $NumOfID; ?>").val(data);
				Alerts_Box("Datos Guardados con Exito!<br />", 2);
				$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid({ disabled: true });
				$("#caja_crear_apply<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
				//WaitClick_WindowClose("Caja_Crear_Apply_Window<?php echo $NumOfID; ?>");
				Locked = false;
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
	
	//------------------------------------------ NEW WINDOW
	$('#Caja_Crear_Apply_Window<?php echo $NumOfID; ?>').jqxWindow(
	{
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 500,
		width: 900,
		minWidth: 800,
		maxWidth: 900,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#caja_crear_apply_cliente<?php echo $NumOfID; ?>").jqxInput({
				theme: mytheme,
				height: 25,
				width: 280,
				disabled: true,
			});
			$("#caja_crear_apply_cliente_ID<?php echo $NumOfID; ?>").jqxInput({
				theme: mytheme,
				height: 25,
				width: 150,
				disabled: true,
			});
			
			$('#caja_crear_imprimir2<?php echo $NumOfID; ?>').jqxButton({ theme: mytheme, template: "warning", width: '150px', height: '30px' });
			$("#caja_crear_imprimir2<?php echo $NumOfID; ?>").bind('click', function ()
			{
				var data = "imprimir/caja_movs_cartera.php?ClienteID="+$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val()+"&Caja_Interno="+Caja_Interno;
				data += "&Valor="+$('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val();
				data += "&Abono="+$('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val();
				data += "&Saldo="+$('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val();
				window.open(data, "", "width=830, height=600, menubar=no, titlebar=no");
				/*
				var datinfo = $("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation');
				var count = datinfo.rowscount;
				
				if (count > 0)
				{
					var myarray = new Array();
					var a = 0;
					for (i = 0; i < count; i++)
					{
						var currentRow = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getrowdata', i);
						//if (currentRow.Abono == 0)
						//	continue;
						
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
						array["Remision"] = currentRow.Remision;
						array["Factura"] = currentRow.Factura;
						array["Ord_Compra"] = currentRow.Ord_Compra;
						array["Valor"] = currentRow.Valor;
						array["Saldo"] = currentRow.Saldo;
						array["Abono"] = currentRow.Abono;
						
						var Saldo_Nuevo = 0;
						if ( currentRow.Saldo > 0)
							Saldo_Nuevo = parseFloat(currentRow.Saldo) - parseFloat(currentRow.Abono);
						else
							Saldo_Nuevo = parseFloat(currentRow.Valor) - parseFloat(currentRow.Abono);
						
						array["Saldo_Nuevo"] = Saldo_Nuevo;
						if (a == 0)
						{
							array["Cliente"] = $('#caja_crear_apply_cliente<?php echo $NumOfID; ?>').val();
							array["ClienteID"] = ID_Cliente;
							array["CajaInterno"] = Caja_Interno;
							array["Recibo"] = $('#caja_crear_recibo<?php echo $NumOfID; ?>').val();
							array["ValorRC"] = $('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val();
							array["Abono"] = $('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val();
							array["Saldo"] = $('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val();
						}
						myarray[a] = array;
						a++;
					}
					window.open("imprimir/caja_movs_cartera.php?Movs="+JSON.stringify(myarray)+"", "", "width=830, height=600, menubar=no, titlebar=no");
				}*/
			});
			
			$("#caja_crear_caja_recibo2<?php echo $NumOfID; ?>").jqxInput({
				theme: mytheme,
				width: 150,
				height: 25,
				disabled: true,
			});
			
			$("#caja_crear_apply_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput({
				theme: mytheme,
				width: '150px',
				height: '25px',
				formatString: 'dd-MMMM-yyyy',
				culture: 'es-ES',
				disabled: true,
			});
			$("#caja_crear_apply_saldo<?php echo $NumOfID; ?>").jqxNumberInput({
				theme: mytheme,
				height: 25,
				width: 150,
				textAlign: 'right',
				symbol: '$',
				digits: 15,
				max: 999999999999999999,
				disabled: true,
			});
			$("#caja_crear_apply_total_abono<?php echo $NumOfID; ?>").jqxNumberInput({
				theme: mytheme,
				height: 25,
				width: 150,
				textAlign: 'right',
				symbol: '$',
				digits: 15,
				max: 999999999999999999,
				disabled: true,
			});
			$("#caja_crear_apply_restante<?php echo $NumOfID; ?>").jqxNumberInput({
				theme: mytheme,
				height: 25,
				width: 150,
				textAlign: 'right',
				symbol: '$',
				digits: 15,
				max: 999999999999999999,
				disabled: true,
			});
		
			$('#caja_crear_apply<?php echo $NumOfID; ?>').jqxButton({ theme: mytheme, template: "info", width: '150px', height: '30px' });
			$("#caja_crear_apply<?php echo $NumOfID; ?>").bind('click', function ()
			{
				Guardar2();
			});
			
			$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid(
			{
				theme: mytheme,
				width: 700,
				height: 400,
				editable: true,
				columns:
				[
					{ text: '', datafield: 'Apply', columntype: 'checkbox', width: '1%' },
					{ text: 'Interno', datafield: 'Interno', editable: false, width: '10%', height: 20 },
					{ text: 'Remision', datafield: 'Remision', editable: false, width: '10%', height: 20 },
					{ text: 'Factura', datafield: 'Factura', editable: false, width: '10%', height: 20 },
					{ text: 'Ord. Compra', datafield: 'Ord_Compra', editable: false, width: '10%', height: 20, cellsalign: 'caja_crear_right' },
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
							var caja_crear_total = 0;
							if ( rowdata.Saldo > 0)
								caja_crear_total = parseFloat(rowdata.Saldo) - parseFloat(rowdata.Abono);
							else
								caja_crear_total = parseFloat(rowdata.Valor) - parseFloat(rowdata.Abono);
								
							return "<div style='margin: 4px;' class='jqx-caja_crear_right-align'>" + ItemsAdapter.formatNumber(caja_crear_total, "c2") + "</div>";
						}
					},
				]
			});
			$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('localizestrings', localizationobj);
			$("#caja_crear_items_grid<?php echo $NumOfID; ?>").bind('bindingcomplete', function (event)
			{
				var datinfo = $("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation');
				var currentRow = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getrowdata', 0);
				
				if (currentRow == undefined) {
					Alerts_Box("El Cliente no posee Deudas.", 4);
					//$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('clear');
				}
				else if (currentRow.Interno == "" || currentRow.Ord_Compra == "")
				{
					Alerts_Box("El Cliente no posee Deudas.", 4);
					$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('clear');
				}
			});
			$("#caja_crear_items_grid<?php echo $NumOfID; ?>").bind('cellvaluechanged', function (event) {
				var args = event.args;
				var datafield = event.args.datafield;
				var rowBoundIndex = args.rowindex;
				var value = args.newvalue;
				var oldvalue = args.oldvalue;
				
				if (datafield == "Abono") {
					if (value > 0) {
						if ($('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val() <= $('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val())
						{
							var total_disponible = $('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val() - $('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val();
							var Valor = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getcellvalue', rowBoundIndex, "Valor");
							var Saldo = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getcellvalue', rowBoundIndex, "Saldo");
							var total_restante = 0;

							if (value > total_disponible)
							{
								if (total_disponible == 0) {
									if (value > oldvalue)
									{
										Alerts_Box("El Valor ingresado es Mayor al Disponible!", 3);
										$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
									}
									else
									{
										Do_TheMath();
									}
								}
								else {
									if ((total_disponible + oldvalue) != value) {
										Alerts_Box("El Valor ingresado es Mayor al Disponible!", 3);
										$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
									}
									else
									{
										if (Saldo > 0) {
											if (value <= Saldo) {
												Do_TheMath();
											}
											else {
												Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
												$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
											}
										}
										else {
											if (value <= Valor) {
												Do_TheMath();
											}
											else {
												Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
												$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
											}
										}
									}
								}
							}
							else
							{
								if (Saldo > 0) {
									if (value <= Saldo) {
										total_restante = total_disponible - value;
										$('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val($('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val() + value);
										$('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val(total_restante);
									}
									else {
										Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
										$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
									}
								}
								else {
									if (value <= Valor) {
										total_restante = total_disponible - value;
										$('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val($('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val() + value);
										$('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val(total_restante);
									}
									else {
										Alerts_Box("El valor ingresado excede la deuda del cliente!", 3);
										$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
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
						$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
					}
				}
				else if (datafield == "Apply") {
					if (value == true) {
						if ($('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val() < $('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val())
						{
							var total_disponible = $('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val() - $('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val();
							var Valor = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getcellvalue', rowBoundIndex, "Valor");
							var Saldo = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getcellvalue', rowBoundIndex, "Saldo");
							var Abono = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getcellvalue', rowBoundIndex, "Abono");
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
								$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
							}
							$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", total_add);
						}
						else {
							Alerts_Box("Ya ha utilizado todo el saldo disponible!", 3);
							$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Apply", false);
						}
					}
					else {
						$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('setcellvalue', rowBoundIndex, "Abono", 0);
					}
				}
			});
			
			function Do_TheMath ()
			{
				var Total_Slado = $('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val();
				var Total_Abono = 0;
				var datinfo = $("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation');
				var count = datinfo.rowscount;
				
				if (count <= 0) {
					return;
				}
				for (i=0; i<count; i++)
				{
					var currentRow = $('#caja_crear_items_grid<?php echo $NumOfID; ?>').jqxGrid('getrowdata', i);
					Total_Abono = Total_Abono + currentRow.Abono;
				}
				
				$('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val(Total_Abono);
				$('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val(Total_Slado - Total_Abono);
			};
		}
	});
	$('#Caja_Crear_Apply_Window<?php echo $NumOfID; ?>').on('open', function (event)
	{
		$("#Loading").hide();
		ItemsSource.data = {"Cartera_AplicarPago": $("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val()};
		ItemsAdapter = new $.jqx.dataAdapter(ItemsSource);
		$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid({source: ItemsAdapter});
		
		$.ajax(
		{
			dataType: "json",
			url: "modulos/datos.php",
			data: {"Caja_Recibo_Info":$("#caja_crear_caja_interno<?php echo $NumOfID; ?>").val()},
			success: function (data)
			{
				$("#caja_crear_apply_cliente<?php echo $NumOfID; ?>").val($("#caja_crear_cliente<?php echo $NumOfID; ?>").val());
				$("#caja_crear_apply_cliente_ID<?php echo $NumOfID; ?>").val($("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val());
				var RC = $("#caja_crear_recibo<?php echo $NumOfID; ?>").val();
				if (RC == "")
					RC = $("#caja_crear_caja_interno<?php echo $NumOfID; ?>").val();
				$('#caja_crear_caja_recibo2<?php echo $NumOfID; ?>').val(RC);
				$('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val(data[0]["Saldo"]);
				$('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val('0');
				$('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val(data[0]["Saldo"]);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				Alerts_Box("Ocurrió un Error al Intentar validar el Saldo del Recibo<br/>Intente nuevamente luego de unos Segundos...",3);
				//alert(textStatus+ " - " +errorThrown);
				$("#caja_crear_apply_cliente<?php echo $NumOfID; ?>").val("");
				$("#caja_crear_apply_cliente_ID<?php echo $NumOfID; ?>").val("");
				$('#caja_crear_caja_recibo2<?php echo $NumOfID; ?>').val("");
				$('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val("0");
				$('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val("0");
				$('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val("0");
			}
		});
	});
	$('#Caja_Crear_Apply_Window<?php echo $NumOfID; ?>').on('close', function (event)
	{
		//$("#caja_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('clear');
		$("#caja_crear_apply_cliente<?php echo $NumOfID; ?>").val("");
		$("#caja_crear_apply_cliente_ID<?php echo $NumOfID; ?>").val("");
		$('#caja_crear_caja_recibo2<?php echo $NumOfID; ?>').val("");
		$('#caja_crear_apply_saldo<?php echo $NumOfID; ?>').val("0");
		$('#caja_crear_apply_total_abono<?php echo $NumOfID; ?>').val("0");
		$('#caja_crear_apply_restante<?php echo $NumOfID; ?>').val("0");
	});
	
	var ItemsSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Apply', type: 'bool'},
			{ name: 'Interno', type: 'string'},
			{ name: 'Remision', type: 'string'},
			{ name: 'Factura', type: 'string'},
			{ name: 'Ord_Compra', type: 'string'},
			{ name: 'Valor', type: 'decimal'},
			{ name: 'Saldo', type: 'decimal'},
			{ name: 'Abono', type: 'decimal'},
			{ name: 'Saldo_Pendiente', type: 'decimal'},
		],
		//data: {"Caja_AplicarPago":true},
		url: "modulos/datos.php",
		async: true
	};
	
	
	// ------------------------------------------ WINDOWS
	//--- Maquinaria
	$("#Caja_Crear_Maquinaria_Window<?php echo $NumOfID ?>").jqxWindow({
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
	$("#Caja_Crear_Maquinaria_Window<?php echo $NumOfID ?>").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Ord_Reparacion":$("#caja_crear_d_prov<?php echo $NumOfID; ?>").val(),
				"ClienteID":$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val(),
			},
			url: "maquinaria/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Caja_Crear_Maquinaria_Content<?php echo $NumOfID ?>").html(data);
			},
			complete: function()
			{
				$("#Loading").hide();
			}
		});
	});
	
	//--- CxP
	$("#Caja_Crear_CxP_Window<?php echo $NumOfID ?>").jqxWindow({
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
		// Usar esta funcion, solo inicia el contenido una vez, lo que puede resultar en problemas al trabajar con multiples clientes...
		/*initContent: function ()
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				data: {
					"Crear_Emergente":true,
					"ClienteID":$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val(),
				},
				url: "cxp/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Caja_Crear_CxP_Content<?php echo $NumOfID ?>").html(data);
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}*/
	});
	$("#Caja_Crear_CxP_Window<?php echo $NumOfID ?>").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"ClienteID":$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val(),
			},
			url: "cxp/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Caja_Crear_CxP_Content<?php echo $NumOfID ?>").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	$("#Caja_Crear_CxP_Window<?php echo $NumOfID ?>").on('close', function (event)
	{
		//$("#Caja_Crear_CxP_Window<?php echo $NumOfID ?>").jqxWindow('destroy');
		CxPSource.data = {"CxP_Deudor":$("#caja_crear_cliente_ID<?php echo $NumOfID; ?>").val()};
		var CxPDataAdapter = new $.jqx.dataAdapter(CxPSource);
		$("#caja_crear_d_prov<?php echo $NumOfID; ?>").jqxComboBox({source: CxPDataAdapter});
	});
	
	//--- Crear Terceros
	$("#Caja_Crear_Tercero_Window<?php echo $NumOfID ?>").jqxWindow({
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
					$("#Caja_Crear_Tercero_Content<?php echo $NumOfID ?>").html(data);
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
	});
	
	if ("<?php echo $Caja_Interno ?>" != "")
	{
		EnableDisableAll(true);
		//$("#crear_duplicar<?php echo $NumOfID ?>").jqxButton({ disabled: false });
		LoadValues();
	}
	else
		CheckRefresh();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "false")
					{
			?>
						$("#caja_crear_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
						$("#caja_crear_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#caja_crear_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
				}
			}
		} ?>
});
</script>
<div id="Caja_Crear_Maquinaria_Window<?php echo $NumOfID ?>">
	<div id="Caja_Crear_Maquinaria_Title<?php echo $NumOfID ?>" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Orden de Maquinaria</div>
	</div>
	<div id="Caja_Crear_Maquinaria_Content<?php echo $NumOfID ?>" class="WindowContainer">
	</div>
</div>
<div id="Caja_Crear_Apply_Window<?php echo $NumOfID; ?>">
	<div id="Caja_Crear_ApplyTitle<?php echo $NumOfID; ?>" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 340px;">Aplicar pago a Cartera</div>
	</div>
	<div id="Caja_Crear_ApplyContent<?php echo $NumOfID; ?>">
		<div id="Caja_Crear_ApplyLeft<?php echo $NumOfID; ?>" style="float: left; width:715px;">
			<table style="margin:0px;" cellpadding="0" cellspacing="5">
				<tr>
					<td>
						Nombre Cliente
					</td>
					<td>
						<input type="text" id="caja_crear_apply_cliente<?php echo $NumOfID; ?>"/>
					</td>
					<td>
						Cliente ID
					</td>
					<td>
						<input type="text" id="caja_crear_apply_cliente_ID<?php echo $NumOfID; ?>"/>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div id="caja_crear_items_grid<?php echo $NumOfID; ?>"></div>
					</td>
				</tr>
			</table>
		</div>
		<div id="Caja_Crear_ApplyRight<?php echo $NumOfID; ?>" style="float: right; width:175px; margin: 0px 0px 10px 0px;">
			<table cellpadding="0" cellspacing="" style="margin-left:10px;">
				<tr>
					<td style="padding-bottom:10px;">
						<input type="button" id="caja_crear_imprimir2<?php echo $NumOfID; ?>" value="Imprimir"/>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						Recibo Caja
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						<input type="text" id="caja_crear_caja_recibo2<?php echo $NumOfID; ?>"/>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						Fecha
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						<div id="caja_crear_apply_fecha<?php echo $NumOfID; ?>"></div>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						Valor RC
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						<div id="caja_crear_apply_saldo<?php echo $NumOfID; ?>"></div>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						Total Abonos
					</td>
				
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						<div id="caja_crear_apply_total_abono<?php echo $NumOfID; ?>"></div>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						Saldo Restante
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						<div id="caja_crear_apply_restante<?php echo $NumOfID; ?>"></div>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:10px;">
						<input type="button" id="caja_crear_apply<?php echo $NumOfID; ?>" value="Guardar Cambios"/>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<div id="Caja_Crear_CxP_Window<?php echo $NumOfID ?>">
	<div id="Caja_Crear_CxP_Title<?php echo $NumOfID ?>" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 250px;">Crear Recibo de Cuenta por Pagar</div>
	</div>
	<div id="Caja_Crear_CxP_Content<?php echo $NumOfID ?>" class="WindowContainer">
	</div>
</div>
<div id="Caja_Crear_Tercero_Window<?php echo $NumOfID ?>">
	<div id="Caja_Crear_Tercero_Title<?php echo $NumOfID ?>" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 340px;">Crear Cliente/Tercero</div>
	</div>
	<div id="Caja_Crear_Tercero_Content<?php echo $NumOfID ?>" class="WindowContainer">
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
				<input type="text" id="caja_crear_caja_interno<?php echo $NumOfID; ?>"/>
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
				<input type="text" id="caja_crear_recibo<?php echo $NumOfID; ?>"/>
			</td>
			<td>
				<div id="caja_crear_tipo_mov<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="caja_crear_grupo<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="caja_crear_subgrupo<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="caja_crear_subgrupo2<?php echo $NumOfID; ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<div id="caja_crear_fecha<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="3">
				<li class="parte1_li_txt">
					Tercero&nbsp;
				</li>
				<li>
					<div id="caja_crear_cliente<?php echo $NumOfID; ?>"></div>
				</li>
			</td>
			<td>
				<div id="caja_crear_cliente_ID<?php echo $NumOfID; ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				D. Prov
			</td>
			<td>
				<div id="caja_crear_d_prov<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="caja_crear_d_prov_valor<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="3">
				<li class="parte1_li_txt">
					Origen de Fondos&nbsp;
				</li>
				<li>
					<div id="caja_crear_caja<?php echo $NumOfID; ?>"></div>
				</li>
				<li class="parte1_li_txt">
					Saldo Actual&nbsp;
				</li>
				<li>
					<input type="text" id="caja_crear_caja_saldo<?php echo $NumOfID; ?>"/>
				</li>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2" style="margin-top:15px;">
	<div id="caja_crear_left" style="float: left; margin-left:0px;">
		<table cellpadding="0" cellspacing="2">
			<tr>
				<td colspan="2">
					<div id="caja_crear_tipo_change<?php echo $NumOfID; ?>">Valor</div>
				</td>
				<td style="padding:0px;">
					<div id="caja_crear_recaudo_efectivo<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Efectivo Total
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_efectivo<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Transferencias/Tarjetas
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_transferencias<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					SubTotal Cheques
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_subtotal_cheques<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td>
					Rete. IVA
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_rete_iva<?php echo $NumOfID; ?>"></div>
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_rete_iva_total<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td>
					Rete. ICA
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_rete_ica<?php echo $NumOfID; ?>"></div>
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_rete_ica_total<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td>
					Rete. Fuente
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_rete_fuente<?php echo $NumOfID; ?>"></div>
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_rete_fuente_total<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Otro Descuento
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_otro_dcto<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Concepto Descuento
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_dcto_concepto<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					TOTAL
				</td>
				<td style="padding:2px 0px;">
					<div id="caja_crear_total<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
		</table>
	</div>
	<div id="caja_crear_middle" style="float: left; margin-left:10px;">
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
					<div id="caja_crear_tipo<?php echo $NumOfID; ?>"></div>
				</td>
				<td>
					<div id="caja_crear_valor<?php echo $NumOfID; ?>"></div>
				</td>
				<td style="padding:0px 5px">
					<input type="text" id="caja_crear_num_consignacion<?php echo $NumOfID; ?>"/>
				</td>
				<td>
					<div id="caja_crear_banco<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
		</table>
		<div id="caja_crear_items_grid1<?php echo $NumOfID; ?>"></div>
	</div>
	<div id="caja_crear_right" style="float: left; margin-left:10px; margin-bottom:15px;">
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
					<input type="text" id="caja_crear_num_cheque<?php echo $NumOfID; ?>"/>
				</td>
				<td>
					<div id="caja_crear_valor_cheque<?php echo $NumOfID; ?>"></div>
				</td>
				<td style="padding:0px 5px">
					<div id="caja_crear_banco_cheque<?php echo $NumOfID; ?>"></div>
				</td>
				<td>
					<input type="text" id="caja_crear_num_cuenta<?php echo $NumOfID; ?>"/>
				</td>
				<td style="padding:0px 5px">
					<div id="caja_crear_estado_fecha<?php echo $NumOfID; ?>"></div>
				</td>
				<td>
					<div id="caja_crear_cheque_fecha<?php echo $NumOfID; ?>"></div>
				</td>
			</tr>
		</table>
		<div id="caja_crear_items_grid2<?php echo $NumOfID; ?>"></div>
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
				<textarea readonly rows="5" cols="33" id="caja_crear_aplicado_a<?php echo $NumOfID; ?>" maxlength="100" class="DisabledTextArea" style="resize:none;"></textarea>
			</td>
			<td rowspan="2">
				<textarea rows="5" cols="34" id="caja_crear_observaciones<?php echo $NumOfID; ?>" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td>
				<input type="button" id="caja_crear_guardar<?php echo $NumOfID; ?>" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="caja_crear_nuevo<?php echo $NumOfID; ?>" value="Nuevo"/>
			</td>
			<td>
				<input type="button" id="caja_crear_imprimir<?php echo $NumOfID; ?>" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<!--<td colspan="2">
				&nbsp;
			</td>-->
			<td>
				<input type="button" id="caja_crear_aplicar<?php echo $NumOfID; ?>" value="Aplicar Pago"/>
			</td>
			<td>
				<input type="button" id="caja_crear_crear_tercero<?php echo $NumOfID; ?>" value="Crear Tercero"/>
			</td>
			<td>
				<input type="button" id="caja_crear_impresora<?php echo $NumOfID; ?>" value="Impresora Punto"/>
			</td>
		</tr>
	</table>
</div>