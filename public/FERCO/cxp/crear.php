<?php
session_start();
$ClienteID = isset($_POST["ClienteID"]) ? $_POST["ClienteID"]:"";

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
	var Grupo = "";
	var SubGrupo = "";
	var SubGrupo2 = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("CxP_Content");
	var Body = document.getElementById("CxP_Crear_Content");
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
	
	function ReDefine()
	{
		<?php
		if (isset($_POST["Crear_Emergente"]))
		{
		?>
		ClearJSON = [
			{id:"cxp_crear_interno<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_factura<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_grupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_subgrupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_subgrupo2<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_fecha<?php echo $NumOfID; ?>", type:"jqxDateTimeInput"},
			{id:"cxp_crear_valor<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"cxp_crear_concepto<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_observaciones<?php echo $NumOfID; ?>", type:""},
		];
		
		EnableDisableJSON = [
			{id:"cxp_crear_factura<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_grupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_subgrupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_subgrupo2<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_fecha<?php echo $NumOfID; ?>", type:"jqxDateTimeInput"},
			{id:"cxp_crear_valor<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"cxp_crear_concepto<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_observaciones<?php echo $NumOfID; ?>", type:""},
			//
			{id:"cxp_crear_guardar<?php echo $NumOfID; ?>", type:"jqxButton"},
			{id:"cxp_crear_tercero<?php echo $NumOfID; ?>", type:"jqxButton"},
		];
		<?php
		}
		else
		{
		?>
		ClearJSON = [
			{id:"cxp_crear_interno<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_factura<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_grupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_subgrupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_subgrupo2<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_fecha<?php echo $NumOfID; ?>", type:"jqxDateTimeInput"},
			{id:"cxp_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_valor<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"cxp_crear_concepto<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_observaciones<?php echo $NumOfID; ?>", type:""},
		];
		
		EnableDisableJSON = [
			{id:"cxp_crear_factura<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_grupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_subgrupo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_subgrupo2<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_fecha<?php echo $NumOfID; ?>", type:"jqxDateTimeInput"},
			{id:"cxp_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"cxp_crear_valor<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"cxp_crear_concepto<?php echo $NumOfID; ?>", type:""},
			{id:"cxp_crear_observaciones<?php echo $NumOfID; ?>", type:""},
			//
			{id:"cxp_crear_guardar<?php echo $NumOfID; ?>", type:"jqxButton"},
			{id:"cxp_crear_tercero<?php echo $NumOfID; ?>", type:"jqxButton"},
		];
		<?php
		}
		?>
	}
	ReDefine();
	
	//------------------------------------------- KEY JUMPS
	$("#cxp_crear_factura<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$("#cxp_crear_grupo<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput('focus');
		}
	});
	$("#cxp_crear_fecha<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$("#cxp_crear_cliente<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('focus');
		}
	});
	$("#cxp_crear_cliente_ID<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_valor<?php echo $NumOfID; ?>").jqxNumberInput('focus');
			var input = $("#cxp_crear_valor<?php echo $NumOfID; ?> input")[0];
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
	/*$("#cxp_crear_valor<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_concepto<?php echo $NumOfID; ?>").focus();
		}
	});
	$("#cxp_crear_concepto<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_observaciones<?php echo $NumOfID; ?>").focus();
		}
	});
	$("#cxp_crear_observaciones<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#cxp_crear_guardar<?php echo $NumOfID; ?>").focus();
		}
	});*/
	//---------------------------------------------------------------
	
	$("#cxp_crear_interno<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true,
	});
	
	$("#cxp_crear_factura<?php echo $NumOfID; ?>").jqxInput({
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
	
	$("#cxp_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#cxp_crear_grupo<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args) {
			Grupo = event.args.item.value;
			$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
		else
		{
			Grupo = "";
			$("#cxp_crear_grupo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
	});
	$("#cxp_crear_grupo<?php echo $NumOfID; ?>").bind('select', function (event) {
		if (event.args) {
			Grupo = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox({ disabled: false});
				SubGrupoSource.data = {"Caja_SubGrupo": Grupo};
				SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox({source: SubGrupoDataAdapter});
			},350);
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
	
	$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args) {
			SubGrupo = event.args.item.value;
			$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
		else
		{
			SubGrupo = "";
			$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
	});
	$("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").bind('select', function (event) {
		if (event.args) {
			SubGrupo = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox({ disabled: false});
				SubGrupo2Source.data = {"Caja_SubGrupo2": Grupo};
				SubGrupo2DataAdapter = new $.jqx.dataAdapter(SubGrupo2Source);
				$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox({source: SubGrupo2DataAdapter});
			},350);
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
	
	$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args) {
			SubGrupo2 = event.args.item.value;
		}
		else
		{
			SubGrupo2 = "";
			$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
	});
	$("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").bind('select', function (event) {
		if (event.args) {
			SubGrupo2 = event.args.item.value;
		}
	});
	
	$("#cxp_crear_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 110,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#cxp_crear_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	
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
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
	{
		loadComplete: function (records)
		{
			<?php
				if (isset($ClienteID) && $ClienteID != "")
				{
			?>
					$("#cxp_crear_cliente<?php echo $NumOfID ?>").jqxComboBox('selectItem', "<?php echo $ClienteID; ?>");
			<?php
				}
			?>
		}
	});

	$("#cxp_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox(
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
	$("#cxp_crear_cliente<?php echo $NumOfID; ?>").bind('change', function (event) {
		if (event.args)
		{
			$("#cxp_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		} else {
			var item_value = $("#cxp_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			if (!item_value) {
				$("#cxp_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#cxp_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#cxp_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#cxp_crear_cliente_ID<?php echo $NumOfID; ?>").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#cxp_crear_cliente<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#cxp_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#cxp_crear_valor<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 160,
		//inputMode: 'simple',
		symbol: '$',
		digits: 15,
		max: 999999999999999,
	});
	
	function Crear()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if ($("#cxp_crear_factura<?php echo $NumOfID; ?>").val() == "")
		{
			Alerts_Box("Debe ingresar un numero de Factura.", 3);
			WaitClick_Input("cxp_crear_factura<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if ($("#cxp_crear_grupo<?php echo $NumOfID; ?>").val() == "")
		{
			Alerts_Box("Debe ingresar un Grupo.", 3);
			WaitClick_Combobox("cxp_crear_grupo<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if ($("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").val() == "")
		{
			Alerts_Box("Debe ingresar un SubGrupo.", 3);
			WaitClick_Combobox("cxp_crear_subgrupo<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if ($("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").val() == "")
		{
			Alerts_Box("Debe ingresar un SubGrupo de Nivel 2.", 3);
			WaitClick_Combobox("cxp_crear_subgrupo2<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if ($("#cxp_crear_cliente<?php echo $NumOfID; ?>").val() == "")
		{
			Alerts_Box("Debe ingresar un Cliente.", 3);
			WaitClick_Combobox("cxp_crear_cliente<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if ($("#cxp_crear_valor<?php echo $NumOfID; ?>").val() <= 0)
		{
			Alerts_Box("Debe ingresar un Valor de Factura.", 3);
			WaitClick_NumberInput("cxp_crear_valor<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		var array = {};

		array["ClienteID"] = $("#cxp_crear_cliente<?php echo $NumOfID; ?>").val();
		array["Fecha"] = GetFormattedDate($("#cxp_crear_fecha<?php echo $NumOfID; ?>").jqxDateTimeInput('getDate'));
		array["Factura"] = $("#cxp_crear_factura<?php echo $NumOfID; ?>").val();
		array["Grupo"] = $("#cxp_crear_grupo<?php echo $NumOfID; ?>").val();
		array["SubGrupo"] = $("#cxp_crear_subgrupo<?php echo $NumOfID; ?>").val();
		array["SubGrupo2"] = $("#cxp_crear_subgrupo2<?php echo $NumOfID; ?>").val();
		array["Valor"] = $("#cxp_crear_valor<?php echo $NumOfID; ?>").val();
		array["Concepto"] = $("#cxp_crear_concepto<?php echo $NumOfID; ?>").val();
		array["Observaciones"] = $("#cxp_crear_observaciones<?php echo $NumOfID; ?>").val();
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			url: "modulos/guardar.php",
			data: {"CxP_Crear":array},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				document.getElementById("ok").value = "Nuevo";
				document.getElementById("cancel").value = "Aceptar";
				Alerts_Box("Datos Guardados con Exito!<br/>Codigo Interno Generado = \""+data+"\"", 2, true);
				$("#cxp_crear_interno<?php echo $NumOfID; ?>").val(data);
				EnableDisableAll(true);
				Timer1 = setInterval(function(){
					if (ClickOK == true)
					{
						document.getElementById("ok").value = "Aceptar";
						document.getElementById("cancel").value = "Cancelar";
						ClearAll();
						EnableDisableAll(false);
						ClickOK = false;
						clearInterval(Timer1);
						clearTimeout(Timer2);
					}
					else if (ClickCANCEL == true)
					{
						document.getElementById("ok").value = "Aceptar";
						document.getElementById("cancel").value = "Cancelar";
						clearInterval(Timer1);
						clearTimeout(Timer2);
					}
				},10);
				Timer2 = setTimeout(function(){
					document.getElementById("ok").value = "Aceptar";
					document.getElementById("cancel").value = "Cancelar";
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
	
	$("#cxp_crear_guardar<?php echo $NumOfID; ?>").jqxButton({ width: 90, template: "info" });
	$("#cxp_crear_guardar<?php echo $NumOfID; ?>").bind('click', function ()
	{
		Crear();
	});
	
	$("#cxp_crear_nuevo<?php echo $NumOfID; ?>").jqxButton({width: 90, template: "success"});
	$("#cxp_crear_nuevo<?php echo $NumOfID; ?>").bind('click', function ()
	{
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	});
	
	$("#cxp_crear_imprimir<?php echo $NumOfID; ?>").jqxButton({width: 90, template: "warning"});
	$("#cxp_crear_categorias<?php echo $NumOfID; ?>").jqxButton({width: 90, template: "primary"});
	$("#cxp_crear_tercero<?php echo $NumOfID; ?>").jqxButton({width: 184, template: "warning"});
	
	<?php
	if (!isset($_POST["Crear_Emergente"]))
	{
	?>
		CheckRefresh();
	<?php
	}
	else
	{
	?>
		$("#cxp_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox({ disabled: true});
		$("#cxp_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox({ disabled: true});
	<?php
	}
	?>
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "CxP" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "false")
					{
			?>
						$("#cxp_crear_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
						$("#cxp_crear_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
					
					if ($data[$i]["Modulo"] == "CxP" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Imprimir"] == "false")
					{
			?>
						$("#cxp_crear_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
			<?php
					}
				}
			}
		} ?>
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
				<input type="text" id="cxp_crear_interno<?php echo $NumOfID; ?>"/>
			</td>
			<td>
				<input type="text" id="cxp_crear_factura<?php echo $NumOfID; ?>"/>
			</td>
			<td>
				<div id="cxp_crear_grupo<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="cxp_crear_subgrupo<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="cxp_crear_subgrupo2<?php echo $NumOfID; ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<div id="cxp_crear_fecha<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="4">
				<li class="parte1_li_txt">
					Tercero&nbsp;
				</li>
				<li>
					<div id="cxp_crear_cliente<?php echo $NumOfID; ?>"></div>
				</li>
				<li class="parte1_li_txt">
					&nbsp; &nbsp;Tercero ID&nbsp;&nbsp;
				</li>
				<li>
					<div id="cxp_crear_cliente_ID<?php echo $NumOfID; ?>"></div>
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
				&nbsp;
				<li class="parte1_li_txt">
					Valor de la Factura &nbsp;
				</li>
				<li>
					<div id="cxp_crear_valor<?php echo $NumOfID; ?>"></div>
				</li>
			</td>
			<td rowspan="4">
				<textarea rows="6" cols="25" id="cxp_crear_concepto<?php echo $NumOfID; ?>" style="resize:none;"></textarea>
			</td>
			<td rowspan="4">
				<textarea rows="6" cols="25" id="cxp_crear_observaciones<?php echo $NumOfID; ?>" style="resize:none;"></textarea>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="5">
				&nbsp;
			</td>
			<td>
				<li style="padding-right:4px;">
					<input type="button" id="cxp_crear_guardar<?php echo $NumOfID; ?>" value="Guardar"/>
				</li>
				<li style="padding-right:4px;">
					<input type="button" id="cxp_crear_nuevo<?php echo $NumOfID; ?>" value="Nuevo"/>
				</li>
				<li>
					<input type="button" id="cxp_crear_imprimir<?php echo $NumOfID; ?>" value="Imprimir"/>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="5">
				&nbsp;
			</td>
			<td>
				<li style="padding-right:4px;">
					<input type="button" id="cxp_crear_categorias<?php echo $NumOfID; ?>" value="Categorias"/>
				</li>
				<li>
					<input type="button" id="cxp_crear_tercero<?php echo $NumOfID; ?>" value="Crear Tercero"/>
				</li>
			</td>
		</tr>
	</table>
</div>