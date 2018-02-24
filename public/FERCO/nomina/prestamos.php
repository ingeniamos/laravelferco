<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
$NumOfID = 0;
$Interno = isset($_POST['Interno']) ? $_POST['Interno']:"";
if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
{
	$NumOfID++;
}
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	var ClienteData = new Array();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Nomina_Content");
	var Body = document.getElementById("Nomina_Prestamos_Content");
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
				SystemMap("Prestamos", true);
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
	
	//---
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Prestamos" && $data[$i]["Guardar"] == "false")
				{
		?>
					$("#nomina_prestamos_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
					$("#nomina_prestamos_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Prestamos" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#nomina_prestamos_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		<?php
				}
			}
		}
	} ?>
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"nomina_prestamos_interno<?php echo $NumOfID ?>", type:""},
			{id:"nomina_prestamos_fecha<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_prestamos_beneficiario<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_beneficiario_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_acreedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_acreedor_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_tipo_mov<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_valor<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"nomina_prestamos_cuotas<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"nomina_prestamos_cuotas_valor<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			//{id:"nomina_prestamos_forma_pago<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_caja<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_observacion<?php echo $NumOfID ?>", type:""},
		];
		
		EnableDisableJSON = [
			{id:"nomina_prestamos_fecha<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_prestamos_beneficiario<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_beneficiario_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_acreedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_acreedor_ID<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_tipo_mov<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_valor<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"nomina_prestamos_cuotas<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			//{id:"nomina_prestamos_forma_pago<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_caja<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_prestamos_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Locked = false;
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	}
	
	function LoadValues()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: "BeneficiarioID", type: 'string'},
				{ name: "AcreedorID", type: 'string'},
				{ name: "Fecha", type: 'string'},
				{ name: "TipoMov", type: 'string'},
				{ name: "Valor", type: 'decimal'},
				{ name: "Cuotas", type: 'int'},
				{ name: "Valor_Cuotas", type: 'decimal'},
				{ name: "Forma_Pago", type: 'string'},
				{ name: "Caja", type: 'string'},
				{ name: "Observacion", type: 'string'},
			],
			data:{"Nomina_Prestamos_Modificar":"<?php echo $Interno ?>"},
			url: "modulos/datos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				$("#nomina_prestamos_acreedor_ID<?php echo $NumOfID ?>").val(records[0]["AcreedorID"]);
				$("#nomina_prestamos_beneficiario_ID<?php echo $NumOfID ?>").val(records[0]["BeneficiarioID"]);
				$("#nomina_prestamos_fecha<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#nomina_prestamos_tipo_mov<?php echo $NumOfID ?>").val(records[0]["TipoMov"]);
				$("#nomina_prestamos_valor<?php echo $NumOfID ?>").val(records[0]["Valor"]);
				$("#nomina_prestamos_cuotas<?php echo $NumOfID ?>").val(records[0]["Cuotas"]);
				$("#nomina_prestamos_cuotas_valor<?php echo $NumOfID ?>").val(records[0]["Valor_Cuotas"]);
				$("#nomina_prestamos_forma_pago<?php echo $NumOfID ?>").val(records[0]["Forma_Pago"]);
				$("#nomina_prestamos_caja<?php echo $NumOfID ?>").val(records[0]["Caja"]);
				$("#nomina_prestamos_observacion<?php echo $NumOfID ?>").val(records[0]["Observacion"]);
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	}
	
	function Calc()
	{
		var Valor = $("#nomina_prestamos_valor<?php echo $NumOfID ?>").val();
		var Cuotas = $("#nomina_prestamos_cuotas<?php echo $NumOfID ?>").val();
		
		$("#nomina_prestamos_cuotas_valor<?php echo $NumOfID ?>").val(Valor/Cuotas);
	}
	
	$("#nomina_prestamos_interno<?php echo $NumOfID ?>").jqxInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: true
	});
	
	$("#nomina_prestamos_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_prestamos_fecha<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	
	var TipoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo', type: 'string'}
		],
		data: {"Nomina_Prestamos":true},
		url: "modulos/parametros.php",
	};
	var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource);
	
	$("#nomina_prestamos_tipo_mov<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 160,
		source: TipoDataAdapter,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#nomina_prestamos_tipo_mov<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
			$("#nomina_prestamos_tipo_mov<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
	});
	
	$("#nomina_prestamos_valor<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 130,
		//inputMode: 'simple',
		textAlign: 'right',
		symbol: '$',
		digits: 12,
		min: 0,
		max: 999999999999,
	});
	$("#nomina_prestamos_valor<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
			Calc();
	});
	
	$("#nomina_prestamos_cuotas<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 35,
		inputMode: 'simple',
		textAlign: 'right',
		digits: 2,
		min: 0,
		max: 99,
	});
	$("#nomina_prestamos_cuotas<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
			Calc();
	});
	
	$("#nomina_prestamos_cuotas_valor<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 130,
		//inputMode: 'simple',
		textAlign: 'right',
		symbol: '$',
		digits: 12,
		min: 0,
		max: 999999999999,
		disabled: true
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
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ClienteData.push(records[i]);
			}
		}
	});
	
	$("#nomina_prestamos_beneficiario<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 370,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Nombre',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#nomina_prestamos_beneficiario<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_prestamos_beneficiario_ID<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_prestamos_beneficiario_ID<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_prestamos_beneficiario_ID<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#nomina_prestamos_beneficiario_ID<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_prestamos_beneficiario<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_prestamos_beneficiario<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_prestamos_acreedor<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 370,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Nombre',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#nomina_prestamos_acreedor<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_prestamos_acreedor_ID<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_prestamos_acreedor_ID<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_prestamos_acreedor_ID<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#nomina_prestamos_acreedor_ID<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_prestamos_acreedor<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_prestamos_acreedor<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	var FormaPagoValues = [
		{"Forma":"Efectivo"},
		{"Forma":"Transferencia"},
		{"Forma":"Cheque"},
	];
	
	$("#nomina_prestamos_forma_pago<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 110,
		source: FormaPagoValues,
		promptText: 'Seleccionar',
		selectedIndex: 0,
		displayMember: 'Forma',
		valueMember: 'Forma',
		searchMode: 'containsignorecase',
		autoComplete: true,
		disabled: true,
	});
	$("#nomina_prestamos_forma_pago<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
			$("#nomina_prestamos_forma_pago<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
	});
	
	var CajaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
		],
		data: {"Venta":true},
		url: "modulos/parametros.php",
	};
	var CajaDataAdapter = new $.jqx.dataAdapter(CajaSource);
	
	$("#nomina_prestamos_caja<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 70,
		source: CajaDataAdapter,
		promptText: 'Selec...',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#nomina_prestamos_caja<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
			$("#nomina_prestamos_caja<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
	});
	
	/*
	**************************************************************
	************************** GUARDAR ***************************
	**************************************************************
	*/
	
	function CrearPrestamo()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Beneficiario = $("#nomina_prestamos_beneficiario<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Acreedor = $("#nomina_prestamos_acreedor<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var TipoMov = $("#nomina_prestamos_tipo_mov<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Caja = $("#nomina_prestamos_caja<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Valor = $("#nomina_prestamos_valor<?php echo $NumOfID ?>").val();
		var Cuotas = $("#nomina_prestamos_cuotas<?php echo $NumOfID ?>").val();
		var ValorCuotas = $("#nomina_prestamos_cuotas_valor<?php echo $NumOfID ?>").val();
		
		if (!Beneficiario)
		{
			Alerts_Box("Debe Seleccionar un Beneficiario!", 3);
			WaitClick_Combobox("nomina_prestamos_beneficiario<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Acreedor)
		{
			Alerts_Box("Debe Seleccionar un Acreedor!", 3);
			WaitClick_Combobox("nomina_prestamos_acreedor<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!TipoMov)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Movimiento!", 3);
			WaitClick_Combobox("nomina_prestamos_tipo_mov<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Caja)
		{
			Alerts_Box("Debe Seleccionar una Caja!", 3);
			WaitClick_Combobox("nomina_prestamos_caja<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (Valor == 0 || Valor < 0)
		{
			Alerts_Box("Debe Ingresar un Valor!", 3);
			WaitClick_NumberInput("nomina_prestamos_valor<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (Cuotas == 0 || Cuotas < 0)
		{
			Alerts_Box("Debe Ingresar un Numero de Cuotas!", 3);
			WaitClick_NumberInput("nomina_prestamos_cuotas<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (ValorCuotas == 0 || ValorCuotas < 0)
		{
			Alerts_Box("Hay un error en el calculo de las cuotas...<br/>Intente Ingresar el Valor Nuevamente.", 3);
			WaitClick_NumberInput("nomina_prestamos_valor<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		var array = {};
		var myarray = new Array();
		
		array["Fecha"] = GetFormattedDate($("#nomina_prestamos_fecha<?php echo $NumOfID ?>").jqxDateTimeInput("getDate"));
		array["Beneficiario"] = Beneficiario.value;
		array["Acreedor"] = Acreedor.value;
		array["TipoMov"] = TipoMov.value; 
		array["Valor"] = Valor;
		array["Cuotas"] = Cuotas;
		array["ValorCuotas"] = ValorCuotas;
		array["Caja"] = Caja.value;
		array["Observacion"] = $("#nomina_prestamos_observacion<?php echo $NumOfID ?>").val();
		myarray[0] = array;
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'json',
			url: "modulos/guardar.php",
			data: {"Nomina_Prestamos_Crear":myarray},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				ClickOK = false;
				ClickCANCEL = false;
				Alerts_Box("Prestamo Creado con Exito!<br/>Codigo Interno Generado = \""+data[0]["Interno"]+"\"", 2, true);
				$("#nomina_prestamos_interno<?php echo $NumOfID ?>").val(data[0]["Interno"]);
				EnableDisableAll(true);
				Timer1 = setInterval(function()
				{
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
						Locked = false;
					}
				},10);
				Timer2 = setTimeout(function()
				{
					$("#Ask_Alert").jqxWindow('close');
					clearInterval(Timer1);
					ClickOK = false;
					ClickCANCEL = true;
					Locked = false;
				},5000);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	}
	
	$("#nomina_prestamos_guardar<?php echo $NumOfID ?>").jqxButton({
		width: 130,
		template: "info"
	});
	$("#nomina_prestamos_guardar<?php echo $NumOfID ?>").bind('click', function ()
	{
		CrearPrestamo();
	});
	$("#nomina_prestamos_imprimir<?php echo $NumOfID ?>").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#nomina_prestamos_imprimir<?php echo $NumOfID ?>").bind('click', function ()
	{
		var data = "imprimir/nom_prestamos.php?Interno="+$("#nomina_prestamos_interno<?php echo $NumOfID ?>").val()+"";
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	$("#nomina_prestamos_nuevo<?php echo $NumOfID ?>").jqxButton({
		width: 120,
		template: "success"
	});
	$("#nomina_prestamos_nuevo<?php echo $NumOfID ?>").bind('click', function ()
	{
		ClearDocument();
	});
	
	if ("<?php echo $Interno ?>" != "")
	{
		EnableDisableAll(true);
		$("#nomina_prestamos_interno<?php echo $NumOfID ?>").val("<?php echo $Interno ?>");
		$("#nomina_prestamos_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		LoadValues();
	}
	else
		CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="nomina_prestamos_interno<?php echo $NumOfID ?>"/>
			</td>
			<td colspan="6">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<div id="nomina_prestamos_fecha<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Tipo Mov.
			</td>
			<td>
				<div id="nomina_prestamos_tipo_mov<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Valor
			</td>
			<td>
				<div id="nomina_prestamos_valor<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<li class="parte1_li_txt">
					Cuotas &nbsp;
				</li>
				<li>
					<div id="nomina_prestamos_cuotas<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<td>
				<li class="parte1_li_txt">
					V. Cuota &nbsp;
				</li>
				<li>
					<div id="nomina_prestamos_cuotas_valor<?php echo $NumOfID ?>"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Beneficiario
			</td>
			<td colspan="4">
				<div id="nomina_prestamos_beneficiario<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_prestamos_beneficiario_ID<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Forma Pago
			</td>
			<td>
				<div id="nomina_prestamos_forma_pago<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Acreedor
			</td>
			<td colspan="4">
				<div id="nomina_prestamos_acreedor<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_prestamos_acreedor_ID<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Caja de Pago
			</td>
			<td>
				<div id="nomina_prestamos_caja<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Observacion
			</td>
			<td rowspan="3" colspan="4">
				<textarea rows="5" cols="50" id="nomina_prestamos_observacion<?php echo $NumOfID ?>" maxlength="200" style="resize:none;"></textarea>
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
				&nbsp;
			</td>
			<td>
				<input type="button" id="nomina_prestamos_guardar<?php echo $NumOfID ?>" value="Guardar"/>
			</td>
			<td colspan="2">
				<li style="margin-right:10px;">
					<input type="button" id="nomina_prestamos_imprimir<?php echo $NumOfID ?>" value="Imprimir"/>
				</li>
				<li>
					<input type="button" id="nomina_prestamos_nuevo<?php echo $NumOfID ?>" value="Nuevo"/>
				</li>
			</td>
		</tr>
	</table>
</div>

