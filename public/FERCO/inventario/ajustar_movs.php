<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()// Modulo no-finalizado
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Locked = false;
	var OldState = "";
	var NewState = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Inventario_Content");
	var Body = document.getElementById("Inventario_Ajustar_Movs_Content");
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
				SystemMap("Ajustar Movimientos", true);
				
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
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "AjustarMovs" && $data[$i]["Guardar"] == "false")
				{
		?>
					$("#inventario_ajustar_movs_ajustar").jqxButton({ disabled: true });
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "AjustarMovs" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#inventario_ajustar_movs_imprimir").jqxButton({ disabled: true });
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
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
	$("#inventario_ajustar_movs_categoria").jqxComboBox(
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
	$("#inventario_ajustar_movs_categoria").bind('change', function (event) {
		if (event.args)
		{
			$("#inventario_ajustar_movs_codigo").jqxComboBox('clearSelection');
			$("#inventario_ajustar_movs_producto").jqxComboBox('clearSelection');
			
			GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
			GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#inventario_ajustar_movs_grupo").jqxComboBox({source: GrupoAdapter});
		}
		else
		{
			$("#inventario_ajustar_movs_categoria").jqxComboBox('clearSelection');
			$("#inventario_ajustar_movs_grupo").jqxComboBox('clearSelection');
			$("#inventario_ajustar_movs_grupo").jqxComboBox('clear');
			$("#inventario_ajustar_movs_subgrupo").jqxComboBox('clearSelection');
			$("#inventario_ajustar_movs_subgrupo").jqxComboBox('clear');
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
	
	$("#inventario_ajustar_movs_grupo").jqxComboBox(
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
	$("#inventario_ajustar_movs_grupo").bind('change', function (event) {
		if (event.args)
		{
			SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
			SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#inventario_ajustar_movs_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
		}
		else
		{
			$("#inventario_ajustar_movs_grupo").jqxComboBox('clearSelection');
			$("#inventario_ajustar_movs_subgrupo").jqxComboBox('clearSelection');
			$("#inventario_ajustar_movs_subgrupo").jqxComboBox('clear');
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
	
	$("#inventario_ajustar_movs_subgrupo").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
	});
	$("#inventario_ajustar_movs_subgrupo").bind('change', function (event) {
		if (!event.args)
			$("#inventario_ajustar_movs_subgrupo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
		async: true
	};
	var ProductosDataAdapter1 = new $.jqx.dataAdapter(ProductosSource);
	var ProductosDataAdapter2 = new $.jqx.dataAdapter(ProductosSource);
	
	$("#inventario_ajustar_movs_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		source: ProductosDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#inventario_ajustar_movs_producto").bind('change', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#inventario_ajustar_movs_codigo").val(event.args.item.value);
			},350);
		}
	});
	
	$("#inventario_ajustar_movs_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: ProductosDataAdapter2,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#inventario_ajustar_movs_codigo").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#inventario_ajustar_movs_producto").val() != event.args.item.value)
					$("#inventario_ajustar_movs_producto").val(event.args.item.value);
				
				$("#inventario_ajustar_movs_codigo_cargado").val(event.args.item.value);
				Cargar_Existencias(event.args.item.value);
				LoadValues();
			},350);
		}
	});
	
	$("#inventario_ajustar_movs_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#inventario_ajustar_movs_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	
	$("#inventario_ajustar_movs_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	
	//----------------------------------------
	
	var TipoValues = [
		{"Tipo":"Entrada"},
		{"Tipo":"Salida"},
	];
	
	$("#inventario_ajustar_movs_tipo").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		source: TipoValues,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#inventario_ajustar_movs_tipo").bind('change', function (event) {
		if (!event.args)
		{
			$("#inventario_ajustar_movs_tipo").jqxComboBox('clearSelection');
		}
	});
	
	function Cargar_Existencias(ID)
	{
		$.ajax(
		{
			dataType: "json",
			data: {"Datos_Producto":ID},
			url: "modulos/datos_productos.php",
			success: function (data)
			{
				$("#inventario_ajustar_movs_existencias").val(data[i]["Existencia"]);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
	}
	
	$("#inventario_ajustar_movs_valor").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		inputMode: 'simple',
		digits: 9,
		min: 0,
		max: 999999999,
	});
	
	$("#inventario_ajustar_movs_motivo").jqxInput({
		theme: mytheme,
		height: 20,
		width: 364,
	});
	
	$("#inventario_ajustar_movs_codigo_cargado").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
		disabled: true,
	});
	
	$("#inventario_ajustar_movs_existencias").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		inputMode: 'simple',
		digits: 9,
		min: 0,
		max: 999999999,
		disabled: true
	});
	
	$("#inventario_ajustar_movs_ajustar").jqxButton({
		width: 90,
		template: "success"
	});
	$("#inventario_ajustar_movs_ajustar").bind('click', function ()
	{
		AjustarProducto();
	});
		
	$("#inventario_ajustar_movs_imprimir").jqxButton({
		width: 90,
		template: "warning"
	});
	$("#inventario_ajustar_movs_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/inventario_ajustes.php?Categoria="+$("#inventario_ajustar_movs_categoria").val()+"";
		data += "&Grupo="+$("#inventario_ajustar_movs_grupo").val()+"&SubGrupo="+$("#inventario_ajustar_movs_subgrupo").val()+"";
		data += "&Codigo="+$("#inventario_ajustar_movs_codigo").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#inventario_ajustar_movs_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#inventario_ajustar_movs_fecha_fin").jqxDateTimeInput('getDate'))+"";
		//alert(data);
		window.open(data, "", "width=825, height=800, menubar=no, titlebar=no");
	});
	
	function AjustarProducto()
	{
		if (Locked == true) {
			return;
		}
		
		Locked = true;
		
		if ($("#inventario_ajustar_movs_codigo_cargado").val() == "" || $("#inventario_ajustar_movs_codigo_cargado").val() <= 0)
		{
			Alerts_Box("Debe Cargar un Producto!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		if ($("#inventario_ajustar_movs_tipo").val() == "" || $("#inventario_ajustar_movs_tipo").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Movimiento de Ajuste", 3);
			WaitClick_Combobox("inventario_ajustar_movs_tipo");
			Locked = false;
			return;
		}
		
		if ($("#inventario_ajustar_movs_valor").val() == "" || $("#inventario_ajustar_movs_valor").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un Valor de Movimiento", 3);
			WaitClick_NumberInput("inventario_ajustar_movs_valor");
			Locked = false;
			return;
		}
		
		if ($("#inventario_ajustar_movs_motivo").val() == "" || $("#inventario_ajustar_movs_motivo").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un Motivo de Ajuste", 3);
			WaitClick_Input("inventario_ajustar_movs_motivo");
			Locked = false;
			return;
		}
		
		//---
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			url: "modulos/guardar.php",
			data: {
				"Inventario_Ajustar_Movs":true,
				"Codigo":$("#inventario_ajustar_movs_codigo_cargado").val(),
				"Tipo":$("#inventario_ajustar_movs_tipo").val(),
				"Cantidad":$("#inventario_ajustar_movs_valor").val(),
				"Motivo":$("#inventario_ajustar_movs_motivo").val(),
			},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Alerts_Box("Datos Guardados con Exito!", 2);
				$("#inventario_ajustar_movs_categoria").jqxComboBox("clearSelection");
				$("#inventario_ajustar_movs_grupo").jqxComboBox("clearSelection");
				$("#inventario_ajustar_movs_subgrupo").jqxComboBox("clearSelection");
				$("#inventario_ajustar_movs_producto").jqxComboBox("clearSelection");
				$("#inventario_ajustar_movs_codigo").jqxComboBox("clearSelection");
				//
				LoadValues();
				//$("#inventario_ajustar_movs_items_grid").jqxGrid("updatebounddata");
				//$("#inventario_ajustar_movs_items_grid").jqxGrid("clearselection");
				$("#inventario_ajustar_movs_codigo_cargado").val("");
				$("#inventario_ajustar_movs_valor").val("");
				$("#inventario_ajustar_movs_motivo").val("");
				$("#inventario_ajustar_movs_tipo").jqxComboBox("clearSelection");
				//
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{	
			GridSource.data = {
				"Inventario_Ajustar_Movs":true,
				"Codigo":$("#inventario_ajustar_movs_codigo").val(),
				"Categoria":$("#inventario_ajustar_movs_categoria").val(),
				"Grupo":$("#inventario_ajustar_movs_grupo").val(),
				"SubGrupo":$("#inventario_ajustar_movs_subgrupo").val(),
				"Fecha_Ini":GetFormattedDate($("#inventario_ajustar_movs_fecha_ini").jqxDateTimeInput("getDate")),
				"Fecha_Fin":GetFormattedDate($("#inventario_ajustar_movs_fecha_fin").jqxDateTimeInput("getDate"))
			};
			
			GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#inventario_ajustar_movs_items_grid").jqxGrid({source: GridDataAdapter});
			
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
			{ name: 'Motivo', type: 'string' },
		],
		url: "modulos/datos_productos.php",
		async: true,
	};
	
	$("#inventario_ajustar_movs_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		sortable: true,
		pageable: true,
		autoheight: true,
		editable: true,
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
			{ text: 'Nombre', datafield: 'Nombre', width: 250, height: 20 },
			{
				text: 'Movimiento',
				datafield: 'Cantidad',
				editable: true,
				width: 120,
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput', 
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 15 });
				}				
			},
			{ text: 'Motivo del Ajuste', datafield: 'Motivo', width: 450, height: 20 },
		]
	});
	$("#inventario_ajustar_movs_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#inventario_ajustar_movs_items_grid").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		var Codigo = $("#inventario_ajustar_movs_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Codigo");
		$("#inventario_ajustar_movs_codigo_cargado").val(Codigo);
		Cargar_Existencias(Codigo);
	});
	
	// Load Initial Values!
	LoadValues();
	CheckRefresh();
});
</script>
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
				Producto
			</td>
			<td>
				Codigo
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
				<div id="inventario_ajustar_movs_categoria"></div>
			</td>
			<td>
				<div id="inventario_ajustar_movs_grupo"></div>
			</td>
			<td>
				<div id="inventario_ajustar_movs_subgrupo"></div>
			</td>
			<td>
				<div id="inventario_ajustar_movs_producto"></div>
			</td>
			<td>
				<div id="inventario_ajustar_movs_codigo"></div>
			</td>
			<td>
				<div id="inventario_ajustar_movs_fecha_ini"></div>
			</td>
			<td>
				<div id="inventario_ajustar_movs_fecha_fin"></div>
			</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Tipo de Ajuste
			</td>
			<td>
				Valor del Ajuste
			</td>
			<td>
				Motivo del Ajuste
			</td>
			<td>
				Codigo Cargado
			</td>
			<td>
				Existencias
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
				<div id="inventario_ajustar_movs_tipo"></div>
			</td>
			<td>
				<div id="inventario_ajustar_movs_valor"></div>
			</td>
			<td>
				<input type="text" id="inventario_ajustar_movs_motivo"/>
			</td>
			<td>
				<input type="text" id="inventario_ajustar_movs_codigo_cargado"/>
			</td>
			<td>
				<div id="inventario_ajustar_movs_existencias"></div>
			</td>
			<td>
				<input type="button" id="inventario_ajustar_movs_ajustar" value="Ajustar"/>
			</td>
			<td>
				<input type="button" id="inventario_ajustar_movs_imprimir" value="Imprimir"/>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<div id="inventario_ajustar_movs_items_grid"></div>
</div>

