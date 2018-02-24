<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var SubGrupo = "";
	var SubGrupo2 = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("CxP_Content");
	var Body = document.getElementById("CxP_Modificar_Content");
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
	
	function ClearDocument()
	{
		SubGrupo = "";
		SubGrupo2 = "";
		Timer1 = 0;
		Timer2 = 0;
		Locked = false;
		//
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	}
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"cxp_modificar_interno", type:"jqxComboBox"},
			{id:"cxp_modificar_factura", type:""},
			{id:"cxp_modificar_grupo", type:"jqxComboBox"},
			{id:"cxp_modificar_subgrupo", type:"jqxComboBox"},
			{id:"cxp_modificar_subgrupo2", type:"jqxComboBox"},
			{id:"cxp_modificar_fecha", type:"jqxDateTimeInput"},
			{id:"cxp_modificar_cliente", type:"jqxComboBox"},
			{id:"cxp_modificar_cliente_ID", type:"jqxComboBox"},
			{id:"cxp_modificar_valor", type:"jqxNumberInput"},
			{id:"cxp_modificar_concepto", type:""},
			{id:"cxp_modificar_observaciones", type:""},
			//---
			{id:"cxp_modificar_digitado_por", type:""},
			{id:"cxp_modificar_aprobado_por", type:""},
			{id:"cxp_modificar_modificado_por", type:""},
			{id:"cxp_modificar_fecha_digitado", type:""},
			{id:"cxp_modificar_fecha_aprobado", type:""},
			{id:"cxp_modificar_fecha_modificado", type:""},
		];
		
		EnableDisableJSON = [
			{id:"cxp_modificar_factura", type:""},
			{id:"cxp_modificar_grupo", type:"jqxComboBox"},
			{id:"cxp_modificar_subgrupo", type:"jqxComboBox"},
			{id:"cxp_modificar_subgrupo2", type:"jqxComboBox"},
			{id:"cxp_modificar_fecha", type:"jqxDateTimeInput"},
			{id:"cxp_modificar_cliente", type:"jqxComboBox"},
			{id:"cxp_modificar_cliente_ID", type:"jqxComboBox"},
			{id:"cxp_modificar_valor", type:"jqxNumberInput"},
			{id:"cxp_modificar_concepto", type:""},
			{id:"cxp_modificar_observaciones", type:""},
			//
			{id:"cxp_modificar_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	
	//------------------------------------------- KEY JUMPS
	$("#cxp_modificar_factura").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_modificar_grupo").jqxComboBox('focus');
		}
	});
	$("#cxp_modificar_grupo").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_modificar_subgrupo").jqxComboBox('focus');
		}
	});
	$("#cxp_modificar_subgrupo").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_modificar_subgrupo2").jqxComboBox('focus');
		}
	});
	$("#cxp_modificar_subgrupo2").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_modificar_fecha").jqxDateTimeInput('focus');
		}
	});
	$("#cxp_modificar_fecha").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_modificar_cliente").jqxComboBox('focus');
		}
	});
	$("#cxp_modificar_cliente").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_modificar_cliente_ID").jqxComboBox('focus');
		}
	});
	$("#cxp_modificar_cliente_ID").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_modificar_valor").jqxNumberInput('focus');
			var input = $("#cxp_modificar_valor input")[0];
			if ("selectionStart" in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd("character", 0);
				range.moveStart("character", 0);
				range.select();
			}
		}
	});
	
	//---------------------------------------------------------------
	
	function LoadValues()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var MainSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Fecha', type: 'string'},
				{ name: 'Factura', type: 'string'},
				{ name: 'Grupo', type: 'string'},
				{ name: 'SubGrupo', type: 'string'},
				{ name: 'SubGrupo2', type: 'string'},
				{ name: 'ClienteID', type: 'string'},
				{ name: 'Valor', type: 'decimal'},
				{ name: 'Notas', type: 'string'},
				{ name: 'Observaciones', type: 'string'},
				{ name: 'Digitado_Por', type: 'string'},
				{ name: 'Fecha_Digitado', type: 'string'},
				{ name: 'Aprobado_Por', type: 'string'},
				{ name: 'Fecha_Aprobado', type: 'string'},
				{ name: 'Modificado_Por', type: 'string'},
				{ name: 'Fecha_Modificado', type: 'string'},
			],
			data: {
				"CxP_Cargar_Datos":true,
				"Interno":$("#cxp_modificar_interno").val()
			},
			url: "modulos/datos.php",
		};
		var MainDataAdapter = new $.jqx.dataAdapter(MainSource,
		{
			autoBind: true,
			loadComplete: function ()
			{
				var records = MainDataAdapter.records;
				$("#cxp_modificar_fecha").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#cxp_modificar_factura").val(records[0]["Factura"]);
				$("#cxp_modificar_grupo").jqxComboBox('selectItem', records[0]["Grupo"]);
				SubGrupo = records[0]["SubGrupo"];
				SubGrupo2 = records[0]["SubGrupo2"];
				$("#cxp_modificar_cliente").jqxComboBox('selectItem', records[0]["ClienteID"]);
				$("#cxp_modificar_valor").val(records[0]["Valor"]);
				$("#cxp_modificar_concepto").val(records[0]["Notas"]);
				$("#cxp_modificar_observaciones").val(records[0]["Observaciones"]);
				$("#cxp_modificar_digitado_por").val(records[0]["Digitado_Por"]);
				if (records[0]["Fecha_Digitado"] == "0000-00-00 00:00:00")
					$("#cxp_modificar_fecha_digitado").val("");
				else
					$("#cxp_modificar_fecha_digitado").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Digitado"])));
				$("#cxp_modificar_aprobado_por").val(records[0]["Aprobado_Por"]);
				if (records[0]["Fecha_Aprobado"] == "0000-00-00 00:00:00")
					$("#cxp_modificar_fecha_aprobado").val("");
				else
					$("#cxp_modificar_fecha_aprobado").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Aprobado"])));
				$("#cxp_modificar_modificado_por").val(records[0]["Modificado_Por"]);
				if (records[0]["Fecha_Modificado"] == "0000-00-00 00:00:00")
					$("#cxp_modificar_fecha_modificado").val("");
				else
					$("#cxp_modificar_fecha_modificado").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Modificado"])));
				
				Locked = false;
			},
			loadError: function(jqXHR, status, error)
			{
				Alerts_Box("Ocurrió un Error al Intentar Obtener los Datos.<br/>Intente luego de unos segundos...",3);
				Locked = false;
			}
		});
	}
	
	var CxP_Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'Interno', type: 'string'},
			{ name: 'Factura', type: 'string'}
		],
		url: "modulos/datos.php",
	};
	
	$("#cxp_modificar_interno").jqxComboBox({
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Interno',
		valueMember: 'Interno'
	});
	$("#cxp_modificar_interno").on('change', function (event)
	{
		if (event.args)
			LoadValues();
		else {
			$("#cxp_modificar_interno").jqxComboBox('clearSelection');
			ClearDocument();
		}
		
		if (ClickOK == true)
		{
			EnableDisableAll(false);
			ClickOK = false;
		}
	});
	
	// Buscar Ordenes
	CxP_Source.data =  {"CxP_Modificar":true};
	var CxP_DataAdapter = new $.jqx.dataAdapter(CxP_Source);
	$("#cxp_modificar_interno").jqxComboBox({source: CxP_DataAdapter});
	
	$("#cxp_modificar_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120,
	});
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'}
		],
		data: {"Caja_Grupo":"Egresos"},
		url: "modulos/parametros.php",
		async: true
	};
	GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
	
	$("#cxp_modificar_grupo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: GrupoAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Grupo',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
	});
	$("#cxp_modificar_grupo").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#cxp_modificar_subgrupo").jqxComboBox({ disabled: false});
				$("#cxp_modificar_subgrupo2").jqxComboBox({ disabled: false});
				SubGrupoSource.data = {"Caja_SubGrupo": event.args.item.value};
				SubGrupo2Source.data = {"Caja_SubGrupo2": event.args.item.value};
				var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				var SubGrupo2DataAdapter = new $.jqx.dataAdapter(SubGrupo2Source);
				$("#cxp_modificar_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
				$("#cxp_modificar_subgrupo2").jqxComboBox({source: SubGrupo2DataAdapter});
			},350);
			
			$("#cxp_modificar_subgrupo").jqxComboBox('clearSelection');
			$("#cxp_modificar_subgrupo2").jqxComboBox('clearSelection');
		}
		else
		{
			$("#cxp_modificar_grupo").jqxComboBox('clearSelection');
			$("#cxp_modificar_subgrupo").jqxComboBox('clear');
			$("#cxp_modificar_subgrupo").jqxComboBox('clearSelection');
			$("#cxp_modificar_subgrupo2").jqxComboBox('clearSelection');
			$("#cxp_modificar_subgrupo2").jqxComboBox('clear');
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
	
	$("#cxp_modificar_subgrupo").jqxComboBox({
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
	$("#cxp_modificar_subgrupo").bind('change', function (event)
	{
		if (!event.args)
			$("#cxp_modificar_subgrupo").jqxComboBox('clearSelection');
	});
	$("#cxp_modificar_subgrupo").bind('bindingComplete', function (event)
	{
		if (SubGrupo != "")
		{
			$("#cxp_modificar_subgrupo").jqxComboBox('selectItem', SubGrupo);
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
	
	$("#cxp_modificar_subgrupo2").jqxComboBox({
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
	$("#cxp_modificar_subgrupo2").bind('change', function (event)
	{
		if (!event.args)
			$("#cxp_modificar_subgrupo2").jqxComboBox('clearSelection');
	});
	$("#cxp_modificar_subgrupo2").bind('bindingComplete', function (event)
	{
		if (SubGrupo2 != "")
		{
			$("#cxp_modificar_subgrupo2").jqxComboBox('selectItem', SubGrupo2);
			SubGrupo2 = "";
		}
	});
	
	$("#cxp_modificar_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 110,
		height: 20,
		formatString: 'dd-MMM-yyyy',
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
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);

	$("#cxp_modificar_cliente").jqxComboBox(
	{
		width: 410,
		height: 20,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Tercero',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#cxp_modificar_cliente").bind('change', function (event) {
		if (event.args) {
			$("#cxp_modificar_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		} else {
			var item_value = $("#cxp_modificar_cliente").jqxComboBox('getSelectedItem');
			if (!item_value) {
				$("#cxp_modificar_cliente").jqxComboBox('clearSelection');
				$("#cxp_modificar_cliente_ID").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#cxp_modificar_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#cxp_modificar_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#cxp_modificar_cliente").val() != event.args.item.value)
				$("#cxp_modificar_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		// else
		// {
		// 	$("#cxp_modificar_cliente").jqxComboBox('clearSelection');
		// 	$("#cxp_modificar_cliente_ID").jqxComboBox('clearSelection');
		// }
	});
	
	$("#cxp_modificar_valor").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 160,
		//inputMode: 'simple',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});
	
	function Guardar_Cambios()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var TmpInterno = $("#cxp_modificar_interno").jqxComboBox('getSelectedItem');
		var TmpGrupo = $("#cxp_modificar_grupo").jqxComboBox('getSelectedItem');
		var TmpSubGrupo = $("#cxp_modificar_subgrupo").jqxComboBox('getSelectedItem');
		var TmpSubGrupo2 = $("#cxp_modificar_subgrupo").jqxComboBox('getSelectedItem');
		var TmpCliente = $("#cxp_modificar_cliente_ID").jqxComboBox('getSelectedItem');
		
		if (!TmpInterno)
		{
			Alerts_Box("Debe Seleccionar un CxP.", 3);
			WaitClick_Combobox("cxp_modificar_interno");
			Locked = false;
			return;
		}
		
		if ($("#cxp_modificar_factura").val() == "")
		{
			Alerts_Box("Debe ingresar un numero de Factura.", 3);
			WaitClick_Input("cxp_modificar_factura");
			Locked = false;
			return;
		}
		
		if (!TmpGrupo)
		{
			Alerts_Box("Debe ingresar un Grupo.", 3);
			WaitClick_Combobox("cxp_modificar_grupo");
			Locked = false;
			return;
		}
		
		if (!TmpSubGrupo)
		{
			Alerts_Box("Debe ingresar un SubGrupo.", 3);
			WaitClick_Combobox("cxp_modificar_subgrupo");
			Locked = false;
			return;
		}
		
		if (!TmpSubGrupo2)
		{
			Alerts_Box("Debe ingresar un SubGrupo de Nivel 2.", 3);
			WaitClick_Combobox("cxp_modificar_subgrupo2");
			Locked = false;
			return;
		}
		
		if (!TmpCliente)
		{
			Alerts_Box("Debe ingresar un Cliente.", 3);
			WaitClick_Combobox("cxp_modificar_cliente");
			Locked = false;
			return;
		}
		
		if ($("#cxp_modificar_valor").val() < 1)
		{
			Alerts_Box("Debe ingresar un Valor de Factura.", 3);
			WaitClick_NumberInput("cxp_modificar_valor");
			Locked = false;
			return;
		}
		
		var array = {};
		
		array["Interno"] = TmpInterno.value;
		array["ClienteID"] = TmpCliente.value;
		array["Fecha"] = GetFormattedDate($("#cxp_modificar_fecha").jqxDateTimeInput('getDate'));
		array["Factura"] = $("#cxp_modificar_factura").val();
		array["Grupo"] = TmpGrupo.value;
		array["SubGrupo"] = TmpSubGrupo.value;
		array["SubGrupo2"] = TmpSubGrupo2.value;
		array["Valor"] = $("#cxp_modificar_valor").val();
		array["Concepto"] = $("#cxp_modificar_concepto").val();
		array["Observaciones"] = $("#cxp_modificar_observaciones").val();
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			url: "modulos/guardar.php",
			data: {"CxP_Modificar":array},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				if (data == "OK")
					Alerts_Box("Datos Guardados con Exito!", 2);
				else
					Alerts_Box("Ocurrio un Error al intentar guardar los datos!<br />Intente luego de unos segundos...", 3);
				EnableDisableAll(true);
				Locked = false;
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!<br />Intente luego de unos segundos...", 3);
			}
		});
	};
	
	$("#cxp_modificar_guardar").jqxButton({ width: 90, template: "info" });
	$("#cxp_modificar_guardar").bind('click', function ()
	{
		Guardar_Cambios();
	});
	
	$("#cxp_modificar_nuevo").jqxButton({width: 90, template: "success"});
	$("#cxp_modificar_nuevo").bind('click', function ()
	{
		ClearDocument();
	});
	
	$("#cxp_modificar_imprimir").jqxButton({width: 90, template: "warning"});
	
	$("#cxp_modificar_digitado_por").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#cxp_modificar_fecha_digitado").jqxDateTimeInput({
		theme: mytheme,
		width: 135,
		height: 20,
		formatString: 'dd-MMM-yyyy HH:mm:ss',
		culture: 'es-ES',
		showCalendarButton: false,
		allowNullDate: true,
		disabled: true
	});
	$("#cxp_modificar_fecha_digitado").val("");
	
	$("#cxp_modificar_aprobado_por").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#cxp_modificar_fecha_aprobado").jqxDateTimeInput({
		theme: mytheme,
		width: 135,
		height: 20,
		formatString: 'dd-MMM-yyyy HH:mm:ss',
		culture: 'es-ES',
		showCalendarButton: false,
		allowNullDate: true,
		disabled: true
	});
	$("#cxp_modificar_fecha_aprobado").val("");
	
	$("#cxp_modificar_modificado_por").jqxInput({
		theme: mytheme,
		width: 60,
		height: 20,
		disabled: true
	});
	
	$("#cxp_modificar_fecha_modificado").jqxDateTimeInput({
		theme: mytheme,
		width: 135,
		height: 20,
		formatString: 'dd-MMM-yyyy HH:mm:ss',
		culture: 'es-ES',
		showCalendarButton: false,
		allowNullDate: true,
		disabled: true
	});
	$("#cxp_modificar_fecha_modificado").val("");
	
	
	CheckRefresh();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "CxP" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Guardar"] == "false")
					{
			?>
						$("#cxp_modificar_guardar").jqxButton({ disabled: true });
						$("#cxp_modificar_nuevo").jqxButton({ disabled: true });
			<?php
					}
					
					if ($data[$i]["Modulo"] == "CxP" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#cxp_modificar_imprimir").jqxButton({ disabled: true });
			<?php
					}
				}
			}
		}
	?>
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Factura
			</td>
			<td>
				Grupo
			</td>
			<td>
				SubGrupo
			</td>
			<td>
				SubGrupo Nivel 2
			</td>
		</tr>
		<tr>
			<td>
				Cons.
			</td>
			<td>
				<div id="cxp_modificar_interno"></div>
			</td>
			<td>
				<input type="text" id="cxp_modificar_factura"/>
			</td>
			<td>
				<div id="cxp_modificar_grupo"></div>
			</td>
			<td>
				<div id="cxp_modificar_subgrupo"></div>
			</td>
			<td>
				<div id="cxp_modificar_subgrupo2"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<div id="cxp_modificar_fecha"></div>
			</td>
			<td colspan="4">
				<li class="parte1_li_txt">
					Tercero&nbsp;
				</li>
				<li>
					<div id="cxp_modificar_cliente"></div>
				</li>
				<li class="parte1_li_txt">
					&nbsp;&nbsp;Tercero ID&nbsp;&nbsp;
				</li>
				<li>
					<div id="cxp_modificar_cliente_ID"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="padding-top:30px;">
				&nbsp;
			</td>
			<td style="padding-top:30px;">
				Concepto
			</td>
			<td style="padding-top:30px;">
				Observaciones
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<li class="parte1_li_txt" style="width:60px;">
					Digitó &nbsp;
				</li>
				<li style="margin-right:4px;">
					<input type="text" id="cxp_modificar_digitado_por"/>
				</li>
				<li>
					<div id="cxp_modificar_fecha_digitado"></div>
				</li>
			</td>
			<td rowspan="4">
				<textarea rows="6" cols="25" id="cxp_modificar_concepto" style="resize:none;"></textarea>
			</td>
			<td rowspan="4">
				<textarea rows="6" cols="25" id="cxp_modificar_observaciones" style="resize:none;"></textarea>
			</td>
			<td>
				<li class="parte1_li_txt">
					Valor de la Factura
				</li>
				<li>
					<div id="cxp_modificar_valor"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<li class="parte1_li_txt" style="width:60px;">
					Aprobó
				</li>
				<li style="margin-right:4px;">
					<input type="text" id="cxp_modificar_aprobado_por"/>
				</li>
				<li>
					<div id="cxp_modificar_fecha_aprobado"></div>
				</li>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<li class="parte1_li_txt" style="width:60px;">
					Modificó
				</li>
				<li style="margin-right:4px;">
					<input type="text" id="cxp_modificar_modificado_por"/>
				</li>
				<li>
					<div id="cxp_modificar_fecha_modificado"></div>
				</li>
			</td>
			<td>
				<li style="padding-right:4px;">
					<input type="button" id="cxp_modificar_guardar" value="Guardar"/>
				</li>
				<li style="padding-right:4px;">
					<input type="button" id="cxp_modificar_nuevo" value="Nuevo"/>
				</li>
				<li>
					<input type="button" id="cxp_modificar_imprimir" value="Imprimir"/>
				</li>
			</td>
		</tr>
	</table>
</div>