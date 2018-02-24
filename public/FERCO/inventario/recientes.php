<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var OldState = "";
	var NewState = "";
	var Interno = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Inventario_Content");
	var Body = document.getElementById("Inventario_Recientes_Content");
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
				SystemMap("Movimientos Recientes", true);
				
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
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Recientes" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#inventario_recientes_imprimir").jqxButton({ disabled: true });
		<?php
				}
			}
		}
	} ?>
	
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
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,{ autoBind: true });
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var TipoValues = [
		{"Tipo":"Salida"},
		{"Tipo":"Entrada"},
	];
	
	$("#inventario_recientes_tipo").jqxComboBox(
	{
		theme: mytheme,
		width: 90,
		height: 20,
		source: TipoValues,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#inventario_recientes_tipo").bind('change', function (event) {
		if (!event.args)
			$("#inventario_recientes_tipo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	var MotivoValues = [
		{"Motivo":"Factura"},
		{"Motivo":"Compra"},
		{"Motivo":"Producción"},
	];
	
	$("#inventario_recientes_motivo").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: MotivoValues,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Motivo',
		valueMember: 'Motivo'
	});
	$("#inventario_recientes_motivo").bind('change', function (event) {
		if (!event.args)
			$("#inventario_recientes_motivo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Categoria', type: 'string'},
		],
		data: {"Inventario_Categoria":true},
		url: "modulos/parametros.php",
		async: true
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#inventario_recientes_categoria").jqxComboBox(
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
	$("#inventario_recientes_categoria").bind('change', function (event) {
		if (event.args)
		{
			GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
			GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#inventario_recientes_grupo").jqxComboBox({source: GrupoAdapter});
		}
		else
		{
			$("#inventario_recientes_categoria").jqxComboBox('clearSelection');
			$("#inventario_recientes_grupo").jqxComboBox('clearSelection');
			$("#inventario_recientes_grupo").jqxComboBox('clear');
			$("#inventario_recientes_subgrupo").jqxComboBox('clearSelection');
			$("#inventario_recientes_subgrupo").jqxComboBox('clear');
		}
		LoadValues();
	});
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'},
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#inventario_recientes_grupo").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
	});
	$("#inventario_recientes_grupo").bind('change', function (event) {
		if (event.args)
		{
			SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
			SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#inventario_recientes_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
		}
		else
		{
			$("#inventario_recientes_grupo").jqxComboBox('clearSelection');
			$("#inventario_recientes_subgrupo").jqxComboBox('clearSelection');
			$("#inventario_recientes_subgrupo").jqxComboBox('clear');
		}
		LoadValues();
	});
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo', type: 'string'},
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#inventario_recientes_subgrupo").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
	});
	$("#inventario_recientes_subgrupo").bind('change', function (event) {
		if (!event.args)
			$("#inventario_recientes_subgrupo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	$("#inventario_recientes_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#inventario_recientes_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#inventario_recientes_fecha_ini").on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#inventario_recientes_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#inventario_recientes_fecha_fin").on('change', function (event) 
	{
		if(event.args)
			LoadValues();
	});
	
	$("#inventario_recientes_imprimir").jqxButton({
		width: 110,
		template: "warning"
	});
	$("#inventario_recientes_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/inventario_recientes.php?Tipo="+$("#inventario_recientes_tipo").val()+"";
		data += "&Motivo="+$("#inventario_recientes_motivo").val()+"&Categoria="+$("#inventario_recientes_categoria").val()+"";
		data += "&Grupo="+$("#inventario_recientes_grupo").val()+"&SubGrupo="+$("#inventario_recientes_subgrupo").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#inventario_recientes_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#inventario_recientes_fecha_fin").jqxDateTimeInput('getDate'))+"";
		//alert(data);
		window.open(data, "", "width=825, height=800, menubar=no, titlebar=no");
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{
			clearTimeout(Timer1);
			
			GridSource.data = {
				"Inventario_Recientes":true,
				"Tipo":$("#inventario_recientes_tipo").val(),
				"Motivo":$("#inventario_recientes_motivo").val(),
				"Categoria":$("#inventario_recientes_categoria").val(),
				"Grupo":$("#inventario_recientes_grupo").val(),
				"SubGrupo":$("#inventario_recientes_subgrupo").val(),
				"Fecha_Ini":GetFormattedDate($("#inventario_recientes_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#inventario_recientes_fecha_fin").jqxDateTimeInput("getDate"))
			};
			
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#inventario_recientes_items_grid").jqxGrid({source: GridDataAdapter});
			
		},350);
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Tipo', type: 'string' },
			{ name: 'Motivo', type: 'string' },
			{ name: 'Tercero', value: 'ClienteID', values: { source: ClienteDataAdapter.records, value: 'ClienteID', name: 'Nombre'}, type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'Interno', type: 'string' },
		],
		url: "modulos/datos_productos.php",
		async: true,
	};
	
	$("#inventario_recientes_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		selectionmode: 'singlecell',
		enablebrowserselection: true,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: false,
		columns:
		[
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: 90,
				height: 20,
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'CodFab', datafield: 'Codigo', width: 90, height: 20 },
			{ text: 'Nombre', datafield: 'Nombre', width: 180, height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: 80,
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput', 
				/*createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}*/
			},
			{ text: 'Tipo', datafield: 'Tipo', width: 50, height: 20 },
			{ text: 'Motivo', datafield: 'Motivo', width: 120, height: 20 },
			{ text: 'Tercero', datafield: 'ClienteID', displayfield: 'Tercero', width: 170, height: 20 },
			{ text: 'Factura', datafield: 'Factura', width: 110, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/>Interno', datafield: 'Interno', width: 110, height: 20 },
		]
	});
	$("#inventario_recientes_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#inventario_recientes_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Interno")
		{
			Interno = $("#inventario_recientes_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Interno");
			
			if (Interno.indexOf("FER") >= 0)
				$("#Inventario_Recientes_Remision_Window").jqxWindow('open');
			else if (Interno.indexOf("COMP") >= 0)
				$("#Inventario_Recientes_Compra_Window").jqxWindow('open');
			else if (Interno.indexOf("OP") >= 0)
				$("#Inventario_Recientes_Produccion_Window").jqxWindow('open');
			else
				alert("???");
			//$("#Inventario_Recientes_Window").jqxWindow('open');
		}
	});
	
	// ------------------------------------------ WINDOWS
	//--- Remision
	$("#Inventario_Recientes_Remision_Window").jqxWindow({
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
	$("#Inventario_Recientes_Remision_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Interno":Interno,
			},
			url: "ventas/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Inventario_Recientes_Remision_Content").html(data);
			},
			complete: function(){
				$("#Loading").hide();
			}
		});
	});
	
	//--- Compra Interno
	$("#Inventario_Recientes_Compra_Window").jqxWindow({
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
	$("#Inventario_Recientes_Compra_Window").on('open', function (event)
	{
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
				$("#Inventario_Recientes_Compra_Content").html(data);
			},
			complete: function()
			{
				$("#Loading").hide();
			}
		});
	});
	
	//--- Produccion
	$("#Inventario_Recientes_Produccion_Window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 900,
		width: 960,
		minWidth: 800,
		maxWidth: 1100,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	$("#Inventario_Recientes_Produccion_Window").on('open', function (event)
	{
		$("#Loading").show();
		$.ajax({
			type:"POST",
			data: {
				"Crear_Emergente":true,
				"Ord_Produccion":Interno,
			},
			url: "produccion/crear.php",
			async: true,
			success: function(data) 
			{
				$("#Inventario_Recientes_Produccion_Content").html(data);
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

<div id="Inventario_Recientes_Remision_Window">
	<div id="Inventario_Recientes_Remision_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Inventario_Recientes_Remision_Content" class="WindowContainer">
	</div>
</div>
<div id="Inventario_Recientes_Compra_Window">
	<div id="Inventario_Recientes_Compra_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 420px;">Ver Compra</div>
	</div>
	<div id="Inventario_Recientes_Compra_Content" class="WindowContainer">
	</div>
</div>
<div id="Inventario_Recientes_Produccion_Window">
	<div id="Inventario_Recientes_Produccion_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 370px;">Ver Orden de Produccion</div>
	</div>
	<div id="Inventario_Recientes_Produccion_Content" class="WindowContainer">
	</div>
</div>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px; text-align:center;">
		<tr>
			<td>
				Tipo
			</td>
			<td>
				Motivo
			</td>
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
				Fecha Ini
			</td>
			<td>
				Fecha Fin
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<div id="inventario_recientes_tipo"></div>
			</td>
			<td>
				<div id="inventario_recientes_motivo"></div>
			</td>
			<td>
				<div id="inventario_recientes_categoria"></div>
			</td>
			<td>
				<div id="inventario_recientes_grupo"></div>
			</td>
			<td>
				<div id="inventario_recientes_subgrupo"></div>
			</td>
			<td>
				<div id="inventario_recientes_fecha_ini"></div>
			</td>
			<td>
				<div id="inventario_recientes_fecha_fin"></div>
			</td>
			<td>
				<input type="button" id="inventario_recientes_imprimir" value="Imprimir"/>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="inventario_recientes_items_grid"></div>
</div>
