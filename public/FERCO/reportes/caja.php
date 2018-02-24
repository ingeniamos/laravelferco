<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var Interno = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Reportes_Content");
	var Body = document.getElementById("Reportes_Caja_Content");
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
				SystemMap("Caja", true);
				
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
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Caja" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Caja" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#reportes_caja_export").jqxButton({ disabled: true });
					$("#reportes_caja_imprimir1").jqxButton({ disabled: true });
					$("#reportes_caja_imprimir2").jqxButton({ disabled: true });
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
	
	Day = Day;
	Day = Day + "";
	if (Day.length == 1)
	{
		Day = "0" + Day;
	};
	
	Month = Month - 1;
	Month = Month + "";
	if (Month.length == 1)
	{
		Month = "0" + Month;
	};
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Categoria', type: 'string'},
		],
		data: {"Caja_Categoria":true},
		url: "modulos/parametros.php",
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#reportes_caja_categoria").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: CategoriaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Categoria',
		valueMember: 'Categoria'
	});
	$("#reportes_caja_categoria").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				GrupoSource.data = {"Caja_Grupo": event.args.item.value};
				GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
				$("#reportes_caja_grupo").jqxComboBox({source: GrupoAdapter});
				
				$("#reportes_caja_subgrupo").jqxComboBox('clearSelection');
				$("#reportes_caja_subgrupo2").jqxComboBox('clearSelection');
				
				LoadValues();
			},350);
		}
		else
		{
			$("#reportes_caja_categoria").jqxComboBox('clearSelection');
			$("#reportes_caja_grupo").jqxComboBox('clearSelection');
			$("#reportes_caja_grupo").jqxComboBox('clear');
			$("#reportes_caja_subgrupo").jqxComboBox('clearSelection');
			$("#reportes_caja_subgrupo").jqxComboBox('clear');
			$("#reportes_caja_subgrupo2").jqxComboBox('clearSelection');
			$("#reportes_caja_subgrupo2").jqxComboBox('clear');
			
			LoadValues();
		}
	});
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	$("#reportes_caja_grupo").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
	});
	$("#reportes_caja_grupo").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				SubGrupoSource.data = {"Caja_SubGrupo": event.args.item.value};
				SubGrupoSource2.data = {"Caja_SubGrupo2": event.args.item.value};
				var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				var SubGrupoDataAdapter2 = new $.jqx.dataAdapter(SubGrupoSource2);
				$("#reportes_caja_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
				$("#reportes_caja_subgrupo2").jqxComboBox({source: SubGrupoDataAdapter2});
				
				LoadValues();
			},350);
		}
		else
		{
			$("#reportes_caja_grupo").jqxComboBox('clearSelection');
			$("#reportes_caja_subgrupo").jqxComboBox('clearSelection');
			$("#reportes_caja_subgrupo").jqxComboBox('clear');
			$("#reportes_caja_subgrupo2").jqxComboBox('clearSelection');
			$("#reportes_caja_subgrupo2").jqxComboBox('clear');
			
			LoadValues();
		}
	});
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	$("#reportes_caja_subgrupo").jqxComboBox(
	{
		theme: mytheme,
		width: 142,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
	});
	$("#reportes_caja_subgrupo").bind('change', function (event) {
		if (!event.args)
			$("#reportes_caja_subgrupo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	var SubGrupoSource2 =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo2', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	$("#reportes_caja_subgrupo2").jqxComboBox(
	{
		theme: mytheme,
		width: 142,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo2',
		valueMember: 'SubGrupo2',
	});
	$("#reportes_caja_subgrupo2").bind('change', function (event) {
		if (!event.args)
			$("#reportes_caja_subgrupo2").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	$("#reportes_caja_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_caja_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#reportes_caja_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			LoadValues();
	});
	
	$("#reportes_caja_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_caja_fecha_fin").on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var ClienteData = new Array();
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		id: "ClienteID",
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
	
	$("#reportes_caja_cliente").jqxComboBox(
	{
		width: 255,
		height: 20,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#reportes_caja_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_caja_cliente_ID").val() != event.args.item.value)
				$("#reportes_caja_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#reportes_caja_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 140,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#reportes_caja_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_caja_cliente").val() != event.args.item.value)
				$("#reportes_caja_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		LoadValues();
	});
	
	var VendedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Vendedor', type: 'string'}
		],
		data: {"Venta":true},
		url: "modulos/parametros.php",
	};
	var VendedorDataAdapter = new $.jqx.dataAdapter(VendedorSource);
	
	$("#reportes_caja_digitador").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 80,
		source: VendedorDataAdapter,
		promptText: 'Selecc...',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#reportes_caja_digitador").bind('change', function (event) {
		if (!event.args)
			$("#reportes_caja_digitador").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	$("#reportes_caja_digitador").on("bindingComplete", function (event)
	{
		if (Supervisor == false && Admin == false)
		{
			$("#reportes_caja_digitador").jqxComboBox("selectItem", "<?php echo $_SESSION["UserCode"]; ?>");
			$("#reportes_caja_digitador").jqxComboBox({ disabled: true });
		}
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#reportes_caja_export").jqxButton({
		width: 110,
		template: "success"
	});
	$("#reportes_caja_export").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Reportes_Caja=true";
		data += "&Categoria="+$("#reportes_caja_categoria").val()+"&Grupo="+$("#reportes_caja_grupo").val();
		data += "&SubGrupo="+$("#reportes_caja_subgrupo").val()+"&SubGrupo2="+$("#reportes_caja_subgrupo2").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_caja_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_caja_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&ClienteID="+$("#reportes_caja_cliente_ID").val()+"&DigitadorID="+$("#reportes_caja_digitador").val();
		//
		window.location = data;
	});
	
	$("#reportes_caja_imprimir1").jqxButton({
		width: 130,
		template: "warning"
	});
	$("#reportes_caja_imprimir1").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_caja.php?OrderBy=Categoria";
		data += "&Categoria="+$("#reportes_caja_categoria").val()+"&Grupo="+$("#reportes_caja_grupo").val();
		data += "&SubGrupo="+$("#reportes_caja_subgrupo").val()+"&SubGrupo2="+$("#reportes_caja_subgrupo2").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_caja_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_caja_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&ClienteID="+$("#reportes_caja_cliente_ID").val()+"&DigitadorID="+$("#reportes_caja_digitador").val();
		//
		window.open(data, "", "width=630, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_caja_imprimir2").jqxButton({
		width: 130,
		template: "warning"
	});
	$("#reportes_caja_imprimir2").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_caja.php?OrderBy=Cliente";
		data += "&Categoria="+$("#reportes_caja_categoria").val()+"&Grupo="+$("#reportes_caja_grupo").val();
		data += "&SubGrupo="+$("#reportes_caja_subgrupo").val()+"&SubGrupo2="+$("#reportes_caja_subgrupo2").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_caja_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_caja_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&ClienteID="+$("#reportes_caja_cliente_ID").val()+"&DigitadorID="+$("#reportes_caja_digitador").val();
		//
		window.open(data, "", "width=630, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_caja_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 200,
		symbol: '$',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 4 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		//return;
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			GridSource.data = {
				"Reportes_Caja":true,
				"Categoria":$("#reportes_caja_categoria").val(),
				"Grupo":$("#reportes_caja_grupo").val(),
				"SubGrupo":$("#reportes_caja_subgrupo").val(),
				"SubGrupo2":$("#reportes_caja_subgrupo2").val(),
				"Fecha_Ini":GetFormattedDate($("#reportes_caja_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#reportes_caja_fecha_fin").jqxDateTimeInput('getDate')),
				"ClienteID":$("#reportes_caja_cliente_ID").val(),
				"DigitadorID":$("#reportes_caja_digitador").val(),
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource, {
				autoBind: true,
				beforeLoadComplete: function (records)
				{
					if (records[0]["Valor"] != undefined)
					{
						var ValorTotal = 0;
						var len = records.length;
						for (var i = 0; i < len; i++)
							ValorTotal = ValorTotal + parseFloat(records[i]["Valor"]);
						
						$("#reportes_caja_total").val(ValorTotal);
					}
					else
						$("#reportes_caja_total").val("0");
				},
				loadError(jqXHR, status, error)
				{
					//alert("No Existen Datos!")
					$("#reportes_caja_total").val("0");
				}
			});
			$("#reportes_caja_items_grid").jqxGrid({source: GridDataAdapter});
		},500);
		
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Caja_Interno', type: 'string' },
			{ name: 'Caja_Recibo', type: 'string' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Saldo', type: 'decimal' },
			{ name: 'Categoria', type: 'string' },
			{ name: 'Grupo', type: 'string' },
			{ name: 'SubGrupo', type: 'string' },
			{ name: 'SubGrupo2', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
	};
	
	$("#reportes_caja_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		enablebrowserselection: true,
		selectionmode: 'singlecell',
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		autoheight: true,
		editable: false,
		columns:
		[
			{
				text: 'Fecha',
				datafield: 'Fecha',
				editable: false,
				width: 90,
				height: 20,
				pinned: true,
				columntype: 'datetimeinput',
				cellsformat: 'dd-MMM-yyyy',
			},
			{ text: 'Interno', datafield: 'Caja_Interno', editable: false, width: 120, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> R. Caja', datafield: 'Caja_Recibo', editable: false, width: 100, height: 20, pinned: true, },
			{
				text: 'Valor',
				datafield: 'Valor',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{
				text: 'Saldo',
				datafield: 'Saldo',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{ text: 'Categoria', datafield: 'Categoria', editable: false, width: 100, height: 20 },
			{ text: 'Grupo', datafield: 'Grupo', editable: false, width: 100, height: 20 },
			{ text: 'SubGrupo', datafield: 'SubGrupo', editable: false, width: 100, height: 20 },
			{ text: 'SubGrupo 2', datafield: 'SubGrupo2', editable: false, width: 100, height: 20 },
			{ text: 'Beneficiario', datafield: 'Nombre', editable: false, width: 200, height: 20, pinned: true, },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: 60, height: 20 },
		]
	});
	$("#reportes_caja_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#reportes_caja_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Caja_Recibo")
		{
			Interno = $("#reportes_caja_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Caja_Interno");
			$("#Reportes_Caja_Caja_Window").jqxWindow('open');
		}
	});
	
	// ------------------------------------------ WINDOWS
	//--- Caja Recibo
	$("#Reportes_Caja_Caja_Window").jqxWindow({
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
	$("#Reportes_Caja_Caja_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Caja_Interno":Interno,
			},
			url: "caja/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Reportes_Caja_Caja_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	// Load Initial Values!
	LoadValues();
	CheckRefresh();
});
</script>
<div id="Reportes_Caja_Caja_Window">
	<div id="Reportes_Caja_Caja_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Recibo de CAJA</div>
	</div>
	<div id="Reportes_Caja_Caja_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Categoria
			</td>
			<td>
				Grupo
			</td>
			<td>
				SubGrupo
			</td>
			<td>
				SubGrupo 2
			</td>
			<td>
				Fecha Ini.
			</td>
			<td>
				Fecha Fin.
			</td>
		</tr>
		<tr>
			<td>
				<div id="reportes_caja_categoria"></div>
			</td>
			<td>
				<div id="reportes_caja_grupo"></div>
			</td>
			<td>
				<div id="reportes_caja_subgrupo"></div>
			</td>
			<td>
				<div id="reportes_caja_subgrupo2"></div>
			</td>
			<td>
				<div id="reportes_caja_fecha_ini"></div>
			</td>
			<td>
				<div id="reportes_caja_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Beneficiario
			</td>
			<td>
				Beneficiario ID
			</td>
			<td>
				Digitador
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div id="reportes_caja_cliente"></div>
			</td>
			<td>
				<div id="reportes_caja_cliente_ID"></div>
			</td>
			<td>
				<div id="reportes_caja_digitador"></div>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="6">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="reportes_caja_export" value="Exportar"/>
			</td>
			<td>
				<input type="button" id="reportes_caja_imprimir1" value="Por Categoria"/>
			</td>
			<td>
				<input type="button" id="reportes_caja_imprimir2" value="Por Beneficiario"/>
			</td>
			<td style="text-align:right;">
				TOTALES
			</td>
			<td colspan="2">
				<div id="reportes_caja_total"></div>
			</td>
		</tr>
	</table>
	
	<div id="reportes_caja_items_grid"></div>
</div>