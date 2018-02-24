<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Caja_Content");
	var Body = document.getElementById("CajaGeneral_Content");
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
				SystemMap("Caja General", true);
				LoadValues();
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
				if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "CajaGeneral" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}

				if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "CajaGeneral" && $data[$i]["Modificar"] == "true")
				{
		?>
					Modificar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Caja" && $data[$i]["SubModulo"] == "CajaGeneral" && $data[$i]["Imprimir"] == "true")
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	Day = Day + "";
	if (Day.length == 1)
	{
		Day = "0" + Day;
	};
	
	Month = Month + "";
	if (Month.length == 1)
	{
		Month = "0" + Month;
	};
	
	Year = Year - 1;
	
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
	var BancoDataAdapter1 = new $.jqx.dataAdapter(BancoSource);
	var BancoDataAdapter2 = new $.jqx.dataAdapter(BancoSource);
	var BancoDataAdapter3 = new $.jqx.dataAdapter(BancoSource);
	var BancoDataAdapter4 = new $.jqx.dataAdapter(BancoSource);
	var BancoDataAdapter5 = new $.jqx.dataAdapter(BancoSource);
	
	
	$("#caja_general_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#caja_general_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#caja_general_fecha_ini").on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#caja_general_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#caja_general_fecha_fin").on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#caja_general_banco").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		source: BancoDataAdapter5,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Banco',
		selectedIndex: -1,
		displayMember: 'Banco',
		valueMember: 'Banco'
	});
	$("#caja_general_banco").bind('change', function (event) {
		if (!event.args)
		{
			$("#caja_general_banco").jqxComboBox('clearSelection');
		}
	});
	$("#caja_general_banco").bind('select', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				LoadValues();
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#caja_general_imprimir_banco").jqxButton({width: 120, template: "warning"});
	$("#caja_general_imprimir_banco").bind('click', function ()
	{
		//
		var data = "";
		data += "imprimir/caja_general.php?Tipo=Banco";
		data += "&Banco="+$("#caja_general_banco").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#caja_general_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#caja_general_fecha_fin").jqxDateTimeInput('getDate'))+"";
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	
	$("#caja_general_imprimir_tipo").jqxButton({width: 120, template: "warning"});
	$("#caja_general_imprimir_tipo").bind('click', function ()
	{
		var data = "";
		data += "imprimir/caja_general.php?Tipo=Tipo";
		data += "&Banco="+$("#caja_general_banco").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#caja_general_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#caja_general_fecha_fin").jqxDateTimeInput('getDate'))+"";
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	
	$("#caja_general_total_efectivo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 260,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	$("#caja_general_total_ingresos").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 260,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	var GridSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Banco', type: 'string'},
			{ name: 'Saldo', type: 'decimal'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#caja_general_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 373,
		height: 160,
		enabletooltips: true,
		editable: false,
		editmode: 'click',
		columns:
		[
			{ text: 'Banco', datafield: 'Banco', width: "40%", height: 20 },
			{
				text: 'Saldo',
				datafield: 'Saldo',
				width: "60%",
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18 });
				}
			},
		]
	});
	
	$("#caja_general_total_egresos").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 260,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	$("#caja_general_total_bancos").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 260,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	$("#caja_general_total_cheques1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 260,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	$("#caja_general_total_cheques2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 260,
		spinButtons: false,
		symbol: '$',
		digits: 18,
		max: 999999999999999999.99,
		min: 0,
		disabled: true,
	});
	
	
	//--------------------------------------- Transacciones
	
	function Ajustes(Tipo)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		switch(Tipo)
		{
			case "Caja":
				var Valor = $("#caja_general_valor_ajustar1").val();
				
				if (Valor < 0)
				{
					Alerts_Box("Debe Ingresar un Valor Igual o Mayor a \"0\"", 3);
					WaitClick_NumberInput("caja_general_valor_ajustar1");
					Locked = false;
					return;
				}
				
				if (Valor == 0)
				{
					Alerts_Box("Esta a punto de inicializar el valor del Efectivo de Caja a \"0\"<br />¿Continuar?", 4, true);
					var CheckTimer = setInterval(function()
					{
						if (ClickOK == true)
						{
							clearInterval(CheckTimer);
							ClickOK = false;
							
							$("#Loading_Mess").html("Procesando Solicitud...");
							$("#Loading").show();
							
							$.ajax({
								dataType: 'text',
								url: "modulos/guardar.php",
								data: {
									"Caja_General_Ajuste":true,
									"Tipo":Tipo,
									"Valor":Valor,
								},
								async: true,
								success: function (data, status, xhr)
								{
									$("#Loading").hide();
									$("#Loading_Mess").html("Cargando...");
									Locked = false;
									
									if (data == "")
									{
										LoadValues();
										Alerts_Box("Ajuste Realizado con Exito!", 2);
									}
									else
									{
										Alerts_Box("Ocurrio un Error al intentar realizar el Ajuste<br />Intente luego de unos segundos...", 3);
									}
								},
								error: function (jqXHR, textStatus, errorThrown)
								{
									$("#Loading").hide();
									$("#Loading_Mess").html("Cargando...");
									Alerts_Box("Ocurrio un Error al intentar realizar el Ajuste<br />Intente luego de unos segundos...", 3);
									Locked = false;
								}
							});
							
							return;
						}
						if (ClickCANCEL == true)
						{
							clearInterval(CheckTimer);
							ClickCANCEL = false;
							Locked = false;
							return;
						}
					}, 10);
				}
				else
				{
					$("#Loading_Mess").html("Procesando Solicitud...");
					$("#Loading").show();
					
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Caja_General_Ajuste":true,
							"Tipo":Tipo,
							"Valor":Valor,
						},
						async: true,
						success: function (data, status, xhr)
						{
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							Locked = false;
							
							if (data == "")
							{
								LoadValues();
								Alerts_Box("Ajuste Realizado con Exito!", 2);
							}
							else
							{
								Alerts_Box("Ocurrio un Error al intentar realizar el Ajuste<br />Intente luego de unos segundos...", 3);
							}
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							Alerts_Box("Ocurrio un Error al intentar realizar el Ajuste<br />Intente luego de unos segundos...", 3);
							Locked = false;
						}
					});
				}
			break;
			
			case "Banco":
				var Banco = $("#caja_general_banco_destino3").jqxComboBox('getSelectedItem');
				var Valor = $("#caja_general_valor_ajustar2").val();
				
				if (!Banco)
				{
					Alerts_Box("Debe Seleccionar un Banco.", 3);
					WaitClick_Combobox("caja_general_banco_destino3");
					Locked = false;
					return;
				}
				
				if (Valor < 0)
				{
					Alerts_Box("Debe Ingresar un Valor Igual o Mayor a \"0\"", 3);
					WaitClick_NumberInput("caja_general_valor_ajustar2");
					Locked = false;
					return;
				}
				
				if (Valor == 0)
				{
					Alerts_Box("Esta a punto de inicializar el valor del banco seleccionado a \"0\"<br />¿Continuar?", 4, true);
					var CheckTimer = setInterval(function()
					{
						if (ClickOK == true)
						{
							clearInterval(CheckTimer);
							ClickOK = false;
							
							$("#Loading_Mess").html("Procesando Solicitud...");
							$("#Loading").show();
							
							$.ajax({
								dataType: 'text',
								url: "modulos/guardar.php",
								data: {
									"Caja_General_Ajuste":true,
									"Tipo":Tipo,
									"Banco":Banco.value,
									"Valor":Valor,
								},
								async: true,
								success: function (data, status, xhr)
								{
									$("#Loading").hide();
									$("#Loading_Mess").html("Cargando...");
									Locked = false;
									
									if (data == "")
									{
										LoadValues();
										Alerts_Box("Ajuste Realizado con Exito!", 2);
									}
									else
									{
										Alerts_Box("Ocurrio un Error al intentar realizar el Ajuste<br />Intente luego de unos segundos...", 3);
									}
								},
								error: function (jqXHR, textStatus, errorThrown)
								{
									$("#Loading").hide();
									$("#Loading_Mess").html("Cargando...");
									Alerts_Box("Ocurrio un Error al intentar realizar el Ajuste<br />Intente luego de unos segundos...", 3);
									Locked = false;
								}
							});
							
							return;
						}
						if (ClickCANCEL == true)
						{
							clearInterval(CheckTimer);
							ClickCANCEL = false;
							Locked = false;
							return;
						}
					}, 10);
				}
				else
				{
					$("#Loading_Mess").html("Procesando Solicitud...");
					$("#Loading").show();
					
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Caja_General_Ajuste":true,
							"Tipo":Tipo,
							"Banco":Banco.value,
							"Valor":Valor,
						},
						async: true,
						success: function (data, status, xhr)
						{
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							Locked = false;
							
							if (data == "")
							{
								LoadValues();
								Alerts_Box("Ajuste Realizado con Exito!", 2);
							}
							else
							{
								Alerts_Box("Ocurrio un Error al intentar realizar el Ajuste<br />Intente luego de unos segundos...", 3);
							}
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							Alerts_Box("Ocurrio un Error al intentar realizar el Ajuste<br />Intente luego de unos segundos...", 3);
							Locked = false;
						}
					});
				}
			break;
			
			default:
				Locked = false;
			break;
		}
	}
	
	function Transacciones(Tipo)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		switch(Tipo)
		{
			case "Caja":
				var Banco = $("#caja_general_banco_destino2").jqxComboBox('getSelectedItem');
				var Numero = $("#caja_general_consignacion2").val();
				var Valor = $("#caja_general_valor_transferir2").val();
				var Efectivo = $("#caja_general_total_efectivo").val();
				
				if (!Banco)
				{
					Alerts_Box("Debe Seleccionar un Banco.", 3);
					WaitClick_Combobox("caja_general_banco_destino2");
					Locked = false;
					return;
				}
				
				if (Numero == "")
				{
					Alerts_Box("Debe Ingresar un Numero de Consignacion.", 3);
					WaitClick_Input("caja_general_consignacion2");
					Locked = false;
					return;
				}
				
				if (Valor < 1)
				{
					Alerts_Box("Debe Ingresar un Valor Mayor a \"0\"", 3);
					WaitClick_NumberInput("caja_general_valor_transferir2");
					Locked = false;
					return;
				}
				
				if (Valor > Efectivo)
				{
					Alerts_Box("El Valor Ingresado Supera al Total Efectivo.", 3);
					WaitClick_NumberInput("caja_general_valor_transferir2");
					Locked = false;
					return;
				}
				
				$("#Loading_Mess").html("Procesando Solicitud...");
				$("#Loading").show();
				
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Caja_General_Transacciones":true,
						"Tipo":Tipo,
						"Banco":Banco.value,
						"Numero":Numero,
						"Valor":Valor,
					},
					async: true,
					success: function (data, status, xhr)
					{
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						Locked = false;
						
						if (data == "")
						{
							LoadValues();
							Alerts_Box("Transferencia Realizada con Exito!", 2);
						}
						else
						{
							Alerts_Box("Ocurrio un Error al intentar realizar la Transferencia<br />Intente luego de unos segundos...", 3);
						}
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						Alerts_Box("Ocurrio un Error al intentar realizar la Transferencia<br />Intente luego de unos segundos...", 3);
						Locked = false;
					}
				});
			break;
			
			case "Banco":
				var Banco_Origen = $("#caja_general_banco_origen").jqxComboBox('getSelectedItem');
				var Banco_Destino = $("#caja_general_banco_destino1").jqxComboBox('getSelectedItem');
				var Numero = $("#caja_general_consignacion1").val();
				var Valor = $("#caja_general_valor_transferir1").val();
				
				if (!Banco_Origen)
				{
					Alerts_Box("Debe Seleccionar un Banco de Origen.", 3);
					WaitClick_Combobox("caja_general_banco_origen");
					Locked = false;
					return;
				}
				
				if (!Banco_Destino)
				{
					Alerts_Box("Debe Seleccionar un Banco de Destino.", 3);
					WaitClick_Combobox("caja_general_banco_destino1");
					Locked = false;
					return;
				}
				
				if (Numero == "")
				{
					Alerts_Box("Debe Ingresar un Numero de Consignacion.", 3);
					WaitClick_Input("caja_general_consignacion1");
					Locked = false;
					return;
				}
				
				if (Valor < 1)
				{
					Alerts_Box("Debe Ingresar un Valor Mayor a \"0\"", 3);
					WaitClick_NumberInput("caja_general_valor_transferir1");
					Locked = false;
					return;
				}
				
				$("#Loading_Mess").html("Procesando Solicitud...");
				$("#Loading").show();
				
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Caja_General_Transacciones":true,
						"Tipo":Tipo,
						"Banco_Origen":Banco_Origen.value,
						"Banco_Destino":Banco_Destino.value,
						"Numero":Numero,
						"Valor":Valor,
					},
					async: true,
					success: function (data, status, xhr)
					{
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						Locked = false;
						
						if (data == "")
						{
							LoadValues();
							Alerts_Box("Transferencia Realizada con Exito!", 2);
						}
						else
						{
							Alerts_Box("Ocurrio un Error al intentar realizar la Transferencia<br />Intente luego de unos segundos...", 3);
						}
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						Alerts_Box("Ocurrio un Error al intentar realizar la Transferencia<br />Intente luego de unos segundos...", 3);
						Locked = false;
					}
				});
			break;
			
			default:
				Locked = false;
			break;
		}
	}
	
	$("#caja_general_banco_origen").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 110,
		source: BancoDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Banco Origen',
		selectedIndex: -1,
		displayMember: 'Banco',
		valueMember: 'Banco'
	});
	$("#caja_general_banco_origen").bind('change', function (event) {
		if (!event.args)
			$("#caja_general_banco_origen").jqxComboBox('clearSelection');
	});
	
	$("#caja_general_banco_destino1").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 110,
		source: BancoDataAdapter2,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Banco Destino',
		selectedIndex: -1,
		displayMember: 'Banco',
		valueMember: 'Banco'
	});
	$("#caja_general_banco_destino1").bind('change', function (event) {
		if (!event.args)
			$("#caja_general_banco_destino1").jqxComboBox('clearSelection');
	});
	
	$("#caja_general_consignacion1").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		placeHolder: "# Consignacion"
	});
	
	$("#caja_general_valor_transferir1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#caja_general_transferir1").jqxButton({width: 150, template: "info"});
	$("#caja_general_transferir1").bind('click', function ()
	{
		Transacciones("Banco");
	});
	
	$("#caja_general_banco_destino2").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 110,
		source: BancoDataAdapter3,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Banco Destino',
		selectedIndex: -1,
		displayMember: 'Banco',
		valueMember: 'Banco'
	});
	$("#caja_general_banco_destino2").bind('change', function (event) {
		if (!event.args)
			$("#caja_general_banco_destino2").jqxComboBox('clearSelection');
	});
	
	$("#caja_general_consignacion2").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		placeHolder: "# Consignacion"
	});
	
	$("#caja_general_valor_transferir2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});
	
	$("#caja_general_transferir2").jqxButton({width: 150, template: "info"});
	$("#caja_general_transferir2").bind('click', function ()
	{
		Transacciones("Caja");
	});
	
	$("#caja_general_banco_destino3").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 110,
		source: BancoDataAdapter4,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Banco Destino',
		selectedIndex: -1,
		displayMember: 'Banco',
		valueMember: 'Banco'
	});
	$("#caja_general_banco_destino3").bind('change', function (event) {
		if (!event.args)
			$("#caja_general_banco_destino3").jqxComboBox('clearSelection');
	});
	
	$("#caja_general_valor_ajustar1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999999,
	});
	
	$("#caja_general_valor_ajustar1").jqxTooltip(
	{
		content: '<b>Tip:</b> <i>Saldo Real en Efectivo</i>',
		position: 'mouse',
		name: 'CajaToolTip'
	});
	
	$("#caja_general_ajustar1").jqxButton({width: 150, template: "info"});
	$("#caja_general_ajustar1").bind('click', function ()
	{
		Ajustes("Caja");
	});
	
	$("#caja_general_valor_ajustar2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 150,
		spinButtons: false,
		symbol: '$',
		digits: 15,
		max: 999999999999999999,
	});
	
	$("#caja_general_valor_ajustar2").jqxTooltip(
	{
		content: '<b>Tip:</b> <i>Saldo Real del Banco</i>',
		position: 'mouse',
		name: 'BankToolTip'
	});
	
	$("#caja_general_ajustar2").jqxButton({width: 150, template: "info"});
	$("#caja_general_ajustar2").bind('click', function ()
	{
		Ajustes("Banco");
	});
	
	function LoadValues()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{
			clearTimeout(Timer1);
			
			var MainSource = 
			{
				datatype: "json",
				datafields:
				[
					{ name: 'Saldo_Efectivo', type: 'decimal' },
					{ name: 'Total_Ingresos', type: 'decimal' },
					{ name: 'Total_Egresos', type: 'decimal' },
					{ name: 'Saldo_Bancos', type: 'decimal' },
					{ name: 'Cheques_AlDia', type: 'decimal' },
					{ name: 'Cheques_PostFechados', type: 'decimal' },
				],
				data: { "Caja_General":true },
				url: "modulos/datos.php",
				async: true
			};
			var MainDataAdapter = new $.jqx.dataAdapter(MainSource,{
				autoBind: true,
				loadComplete: function ()
				{
					var myrecords = MainDataAdapter.records;
					// Add Data
					if (myrecords[0]["Total_Ingresos"] != "")
					{
						$("#caja_general_total_efectivo").val(myrecords[0]["Saldo_Efectivo"]);
						$("#caja_general_total_ingresos").val(myrecords[0]["Total_Ingresos"]);
						$("#caja_general_total_egresos").val(myrecords[0]["Total_Egresos"]);
						$("#caja_general_total_bancos").val(myrecords[0]["Saldo_Bancos"]);
						$("#caja_general_total_cheques1").val(myrecords[0]["Cheques_AlDia"]);
						$("#caja_general_total_cheques2").val(myrecords[0]["Cheques_PostFechados"]);
					}// Clean
					else
					{
						$("#caja_general_total_efectivo").val("");
						$("#caja_general_total_ingresos").val("");
						$("#caja_general_total_egresos").val("");
						$("#caja_general_total_bancos").val("");
						$("#caja_general_total_cheques1").val("");
						$("#caja_general_total_cheques2").val("");
					}
				},
				loadError: function(jqXHR, status, error) {
					alert("Request failed: \n" + error);
				},
			});
			
			GridSource.data = { "Caja_General_Bancos":true };
			
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#caja_general_items_grid").jqxGrid({source: GridDataAdapter});
			
		},350);
	};
	
	// Load Initial Values
	LoadValues();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="1" cellspacing="2">
		<tr>
			<td colspan="3" style="padding-bottom:10px;">
				<li class="parte1_li_txt">
					Fecha Ini.&nbsp;
				</li>
				<li style="padding-right:4px;">
					<div id="caja_general_fecha_ini"></div>
				</li>
				<li class="parte1_li_txt">
					Fecha Fin. &nbsp;
				</li>
				<li>
					<div id="caja_general_fecha_fin"></div>
				</li>
			</td>
			<td colspan="3" style="padding-left:10px; padding-bottom:10px;">
				<li style="padding-right:4px;">
					<div id="caja_general_banco"></div>
				</li>
				<li style="padding-right:4px;">
					<input type="button" id="caja_general_imprimir_banco" value="Por Banco"/>
				</li>
				<li>
					<input type="button" id="caja_general_imprimir_tipo" value="Por Tipo"/>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div style="width: 100%; height: 20px; background-color: #90AED9; color: white; text-align:center; font-size: 14px;">
					<p style="margin:0px; padding:1px 0px 0px 0px;">DETALLE DE CAJA GENERAL</p>
				</div>
			</td>
			<td colspan="3" style="padding-left:10px;">
				<div style="width: 100%; height: 20px; background-color: #3E72A2; color: white; text-align:center; font-size: 14px;">
					<p style="margin:0px; padding:1px 0px 0px 0px;">DETALLE DE SALDO EN BANCOS</p>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				Saldo en Efectivo
			</td>
			<td colspan="2">
				<div id="caja_general_total_efectivo"></div>
			</td>
			<td colspan="3" rowspan="6" style="padding-left:10px;">
				<div id="caja_general_items_grid"></div>
			</td>
		</tr>
		<tr>
			<td>
				Ingresos Totales
			</td>
			<td colspan="2">
				<div id="caja_general_total_ingresos"></div>
			</td>
		</tr>
		<tr>
			<td>
				Egresos Totales
			</td>
			<td colspan="2">
				<div id="caja_general_total_egresos"></div>
			</td>
		</tr>
		<tr>
			<td>
				Saldo en Bancos
			</td>
			<td colspan="2">
				<div id="caja_general_total_bancos"></div>
			</td>
		</tr>
		<tr>
			<td>
				Cheques al Dia
			</td>
			<td colspan="2">
				<div id="caja_general_total_cheques1"></div>
			</td>
		</tr>
		<tr>
			<td>
				Cheques Postf.
			</td>
			<td colspan="2">
				<div id="caja_general_total_cheques2"></div>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div style="width: 100%; height: 20px; background-color: #90AED9; color: white; text-align:center; font-size: 14px;">
					<p style="margin:0px; padding:1px 0px 0px 0px;">AJUSTES</p>
				</div>
			</td>
			<td colspan="3" style="padding-left:10px;">
				<div style="width: 100%; height: 20px; background-color: #90AED9; color: white; text-align:center; font-size: 14px;">
					<p style="margin:0px; padding:1px 0px 0px 0px;">TRANSACCIONES</p>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div style="width: 100%; height: 20px; background-color: #D9B290; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">Ajustar Saldo en Efectivo</p>
				</div>
			</td>
			<td colspan="3" style="padding-left:10px;">
				<div style="width: 100%; height: 20px; background-color: #D9B290; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">De Efectivo en Caja General a Otros Bancos</p>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				<div id="caja_general_valor_ajustar1"></div>
			</td>
			<td style="padding-left:10px;">
				<div id="caja_general_banco_destino2"></div>
			</td>
			<td>
				<input type="text" id="caja_general_consignacion2"/>
			</td>
			<td>
				<div id="caja_general_valor_transferir2"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				&nbsp;
			</td>
			<td>
				<input type="button" id="caja_general_ajustar1" value="Ajustar"/>
			</td>
			<td colspan="2" style="padding-left:10px;">
				&nbsp;
			</td>
			<td>
				<input type="button" id="caja_general_transferir2" value="Transferir"/>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div style="width: 100%; height: 20px; background-color: #D9B290; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">Ajustar Saldo Actual del Banco</p>
				</div>
			</td>
			<td colspan="3" style="padding-left:10px;">
				<div style="width: 100%; height: 20px; background-color: #D9B290; color: white; text-align:center;">
					<p style="margin:0px; padding:3px 0px 0px 3px;">Entre Bancos</p>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<div id="caja_general_banco_destino3"></div>
			</td>
			<td>
				<div id="caja_general_valor_ajustar2"></div>
			</td>
			<td style="padding-left:10px;">
				<div id="caja_general_banco_origen"></div>
			</td>
			<td>
				<div id="caja_general_banco_destino1"></div>
			</td>
			<td>
				<div id="caja_general_valor_transferir1"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				&nbsp;
			</td>
			<td>
				<input type="button" id="caja_general_ajustar2" value="Ajustar"/>
			</td>
			<td style="padding-left:10px;">
				&nbsp;
			</td>
			<td>
				<input type="text" id="caja_general_consignacion1"/>
			</td>
			<td>
				<input type="button" id="caja_general_transferir1" value="Transferir"/>
			</td>
		</tr>
	</table>
</div>