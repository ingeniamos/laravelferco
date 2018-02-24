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
	var Body = document.getElementById("Reportes_Compras_Mov_Content");
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
				SystemMap("Compras Mov.", true);
				
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
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Compras_Mov" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#reportes_compras_mov_export").jqxButton({ disabled: true });
					$("#reportes_compras_mov_imprimir1").jqxButton({ disabled: true });
					$("#reportes_compras_mov_imprimir2").jqxButton({ disabled: true });
					$("#reportes_compras_mov_imprimir3").jqxButton({ disabled: true });
					$("#reportes_compras_mov_imprimir4").jqxButton({ disabled: true });
		<?php
				}
			}
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
	
	var ProductoData = new Array();
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		id: "CodFab",
		url: "modulos/datos_productos.php",
		async: false
	};
	var ProductosDataAdapter = new $.jqx.dataAdapter(ProductosSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ProductoData.push(records[i]);
			}
		}
	});
	
	$("#reportes_compras_mov_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		source: ProductoData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#reportes_compras_mov_producto").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_compras_mov_codigo").val() != event.args.item.value)
				$("#reportes_compras_mov_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		LoadValues();
	});
	
	$("#reportes_compras_mov_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ProductoData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#reportes_compras_mov_codigo").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_compras_mov_producto").val() != event.args.item.value)
				$("#reportes_compras_mov_producto").jqxComboBox('selectItem', event.args.item.value);
		}
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
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#reportes_compras_mov_categoria").jqxComboBox(
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
	$("#reportes_compras_mov_categoria").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
				GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
				$("#reportes_compras_mov_grupo").jqxComboBox({source: GrupoAdapter});
				
				$("#reportes_compras_mov_producto").jqxComboBox('clearSelection');
				$("#reportes_compras_mov_codigo").jqxComboBox('clearSelection');
				
				LoadValues();
			},350);
		}
		else
		{
			$("#reportes_compras_mov_categoria").jqxComboBox('clearSelection');
			$("#reportes_compras_mov_grupo").jqxComboBox('clearSelection');
			$("#reportes_compras_mov_grupo").jqxComboBox('clear');
			$("#reportes_compras_mov_subgrupo").jqxComboBox('clearSelection');
			$("#reportes_compras_mov_subgrupo").jqxComboBox('clear');
			
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
	
	$("#reportes_compras_mov_grupo").jqxComboBox(
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
	$("#reportes_compras_mov_grupo").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
				SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				$("#reportes_compras_mov_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
				
				$("#reportes_compras_mov_producto").jqxComboBox('clearSelection');
				$("#reportes_compras_mov_codigo").jqxComboBox('clearSelection');
				
				LoadValues();
			},350);
		}
		else
		{
			$("#reportes_compras_mov_grupo").jqxComboBox('clearSelection');
			$("#reportes_compras_mov_subgrupo").jqxComboBox('clearSelection');
			$("#reportes_compras_mov_subgrupo").jqxComboBox('clear');
			
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
	
	$("#reportes_compras_mov_subgrupo").jqxComboBox(
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
	$("#reportes_compras_mov_subgrupo").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_compras_mov_producto").jqxComboBox('clearSelection');
			$("#reportes_compras_mov_codigo").jqxComboBox('clearSelection');
		}
		else
			$("#reportes_compras_mov_subgrupo").jqxComboBox('clearSelection');
		
		LoadValues();
	});
	
	$("#reportes_compras_mov_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_compras_mov_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$("#reportes_compras_mov_fecha_ini").on('change', function (event) 
	{  
		if(event.args)
			LoadValues();
	});
	
	$("#reportes_compras_mov_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#reportes_compras_mov_fecha_fin").on('change', function (event) 
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
	
	$("#reportes_compras_mov_cliente").jqxComboBox(
	{
		width: 250,
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
	$("#reportes_compras_mov_cliente").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_compras_mov_cliente_ID").val() != event.args.item.value)
				$("#reportes_compras_mov_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_compras_mov_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_compras_mov_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_compras_mov_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_compras_mov_cliente").val();
				
				var item = $("#reportes_compras_mov_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#reportes_compras_mov_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#reportes_compras_mov_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_compras_mov_cliente").jqxComboBox('clearSelection');
			}
			LoadValues();
		}
	});
	
	$("#reportes_compras_mov_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#reportes_compras_mov_cliente_ID").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#reportes_compras_mov_cliente").val() != event.args.item.value)
				$("#reportes_compras_mov_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#reportes_compras_mov_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#reportes_compras_mov_cliente_ID").jqxComboBox('clearSelection');
				$("#reportes_compras_mov_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#reportes_compras_mov_cliente_ID").val();
				var item = $("#reportes_compras_mov_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#reportes_compras_mov_cliente_ID").jqxComboBox('clearSelection');
					$("#reportes_compras_mov_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#reportes_compras_mov_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
		LoadValues();
	});
	
	var EstadoValues = [
		{"Estado":"Aprobado"},
		{"Estado":"Autorizado"},
		{"Estado":"Creado"},
	];
	
	$("#reportes_compras_mov_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 90,
		source: EstadoValues,
		promptText: 'Seleccionar',
		displayMember: 'Estado',
		valueMember: 'Estado',
		selectedIndex: 0,
		dropDownHeight: 130,
	});
	$("#reportes_compras_mov_estado").bind('change', function (event) {
		if (!event.args)
		{
			$("#reportes_compras_mov_estado").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#reportes_compras_mov_interno").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
	});
	$("#reportes_compras_mov_interno").on('change', function () {
		LoadValues();
	});
	
	$("#reportes_compras_mov_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
	});
	$("#reportes_compras_mov_factura").on('change', function () {
		LoadValues();
	});
	
	$("#reportes_compras_mov_pedido").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
	});
	$("#reportes_compras_mov_pedido").on('change', function () {
		LoadValues();
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#reportes_compras_mov_export").jqxButton({
		width: 120,
		template: "success"
	});
	$("#reportes_compras_mov_export").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Reportes_Compras_Mov=true";
		data += "&Codigo="+$("#reportes_compras_mov_codigo").val()+"&Categoria="+$("#reportes_compras_mov_categoria").val();
		data += "&Grupo="+$("#reportes_compras_mov_grupo").val()+"&SubGrupo="+$("#reportes_compras_mov_subgrupo").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_mov_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_mov_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&ClienteID="+$("#reportes_compras_mov_cliente_ID").val()+"&Estado="+$("#reportes_compras_mov_estado").val();
		data += "&Interno="+$("#reportes_compras_mov_interno").val()+"&Factura="+$("#reportes_compras_mov_factura").val();
		data += "&Pedido="+$("#reportes_compras_mov_pedido").val();
		//
		window.location = data;
	});
	
	$("#reportes_compras_mov_imprimir1").jqxButton({
		width: 128,
		template: "warning"
	});
	$("#reportes_compras_mov_imprimir1").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_compras_mov.php?OrderBy=Proveedor";
		data += "&Codigo="+$("#reportes_compras_mov_codigo").val()+"&Categoria="+$("#reportes_compras_mov_categoria").val();
		data += "&Grupo="+$("#reportes_compras_mov_grupo").val()+"&SubGrupo="+$("#reportes_compras_mov_subgrupo").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_mov_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_mov_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&ClienteID="+$("#reportes_compras_mov_cliente_ID").val()+"&Estado="+$("#reportes_compras_mov_estado").val();
		data += "&Interno="+$("#reportes_compras_mov_interno").val()+"&Factura="+$("#reportes_compras_mov_factura").val();
		data += "&Pedido="+$("#reportes_compras_mov_pedido").val();
		//
		window.open(data, "", "width=830, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_compras_mov_imprimir2").jqxButton({
		width: 120,
		template: "primary"
	});
	$("#reportes_compras_mov_imprimir2").bind('click', function ()
	{
		var data = "";
		data += "imprimir/reportes_compras_mov.php?OrderBy=Producto";
		data += "&Codigo="+$("#reportes_compras_mov_codigo").val()+"&Categoria="+$("#reportes_compras_mov_categoria").val();
		data += "&Grupo="+$("#reportes_compras_mov_grupo").val()+"&SubGrupo="+$("#reportes_compras_mov_subgrupo").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#reportes_compras_mov_fecha_ini").jqxDateTimeInput('getDate'));
		data += "&Fecha_Fin="+GetFormattedDate($("#reportes_compras_mov_fecha_fin").jqxDateTimeInput('getDate'));
		data += "&ClienteID="+$("#reportes_compras_mov_cliente_ID").val()+"&Estado="+$("#reportes_compras_mov_estado").val();
		data += "&Interno="+$("#reportes_compras_mov_interno").val()+"&Factura="+$("#reportes_compras_mov_factura").val();
		data += "&Pedido="+$("#reportes_compras_mov_pedido").val();
		//
		window.open(data, "", "width=830, height=600, menubar=no, titlebar=no");
	});
	
	$("#reportes_compras_mov_total").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 148,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	$("#reportes_compras_mov_ultimo_costo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 130,
		symbol: '$',
		decimalDigits: 2,
		digits: 12,
		max: 999999999999,
		disabled: true,
	});
	
	$("#reportes_compras_mov_costo_promedio").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 130,
		symbol: '$',
		decimalDigits: 2,
		digits: 12,
		max: 999999999999,
		disabled: true,
	});
	
	$("#reportes_compras_mov_total_costo_promedio").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 148,
		symbol: '$',
		decimalDigits: 2,
		digits: 15,
		max: 999999999999999,
		disabled: true,
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 4 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			$("#reportes_compras_mov_items_grid").jqxGrid("beginupdate", true);
			
			GridSource.data = {
				"Reportes_Compras_Mov":true,
				"Codigo":$("#reportes_compras_mov_codigo").val(),
				"Categoria":$("#reportes_compras_mov_categoria").val(),
				"Grupo":$("#reportes_compras_mov_grupo").val(),
				"SubGrupo":$("#reportes_compras_mov_subgrupo").val(),
				"Fecha_Ini":GetFormattedDate($("#reportes_compras_mov_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#reportes_compras_mov_fecha_fin").jqxDateTimeInput('getDate')),
				"ClienteID":$("#reportes_compras_mov_cliente_ID").val(),
				"Interno":$("#reportes_compras_mov_interno").val(),
				"Factura":$("#reportes_compras_mov_factura").val(),
				"Pedido":$("#reportes_compras_mov_pedido").val(),
				"Estado":$("#reportes_compras_mov_estado").val(),
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource, {
				autoBind: true,
				beforeLoadComplete: function (records)
				{
					if (records[0]["Valor"] != undefined)
					{
						var Cantidad = 0;
						var ValorTotal = 0;
						var Ult_Costo = records[0]["Ult_Costo"];
						var Costo_Prom = records[0]["Costo_Prom"];
						var Costo_PromTotal = 0;
						var len = records.length;
						for (var i = 0; i < len; i++)
						{
							Cantidad = Cantidad + records[i]["Cantidad"];
							ValorTotal = ValorTotal + records[i]["Valor"];
						}
						
						Costo_PromTotal = ValorTotal / Cantidad;
						
						$("#reportes_compras_mov_total").val(ValorTotal);
						if ($("#reportes_compras_mov_codigo").val() != "")
						{
							$("#reportes_compras_mov_ultimo_costo").val(Ult_Costo);
							$("#reportes_compras_mov_costo_promedio").val(Costo_Prom);
							$("#reportes_compras_mov_total_costo_promedio").val(Costo_PromTotal);
						}
						else
						{
							$("#reportes_compras_mov_ultimo_costo").val("0");
							$("#reportes_compras_mov_costo_promedio").val("0");
							$("#reportes_compras_mov_total_costo_promedio").val("0");
						}
					}
					else
					{
						$("#reportes_compras_mov_total").val("0");
						$("#reportes_compras_mov_ultimo_costo").val("0");
						$("#reportes_compras_mov_costo_promedio").val("0");
						$("#reportes_compras_mov_total_costo_promedio").val("0");
					}
				},
				loadError(jqXHR, status, error)
				{
					//alert("No Existen Datos!")
					$("#reportes_compras_mov_total").val("0");
					$("#reportes_compras_mov_ultimo_costo").val("0");
					$("#reportes_compras_mov_costo_promedio").val("0");
					$("#reportes_compras_mov_total_costo_promedio").val("0");
				}
			});
			$("#reportes_compras_mov_items_grid").jqxGrid({source: GridDataAdapter});
			$("#reportes_compras_mov_items_grid").jqxGrid("endupdate");
		},500);
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Fecha', type: 'date' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Factura', type: 'string' },
			{ name: 'Entrada', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			//{ name: 'ClienteID', type: 'string' },
			{ name: 'Categoria', type: 'string' },
			{ name: 'Grupo', type: 'string' },
			{ name: 'SubGrupo', type: 'string' },
			{ name: 'Producto', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Peso', type: 'string' },
			{ name: 'Unitario', type: 'decimal' },
			{ name: 'Ult_Costo', type: 'decimal' },
			{ name: 'Costo_Prom', type: 'decimal' },
		],
		url: "modulos/datos_productos.php",
	};
	
	$("#reportes_compras_mov_items_grid").jqxGrid(
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
			{ text: 'Interno', datafield: 'Interno', editable: false, width: 100, height: 20 },
			{ text: '<img width=\"17\" height=\"17\" src=\"images/icon-up.png\" style=\"float: left;\"/> Factura', datafield: 'Factura', editable: false, width: 100, height: 20, pinned: true, },
			{ text: 'Entrada', datafield: 'Entrada', editable: false, width: 80, height: 20 },
			{ text: 'Proveedor', datafield: 'Nombre', editable: false, width: 150, height: 20, pinned: true, },
			{ text: 'Producto', datafield: 'Producto', editable: false, width: 150, height: 20 },
			{
				text: 'Movimiento',
				datafield: 'Cantidad',
				width: 80,
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'center',
				columntype: 'numberinput',
				editable: false,
			},
			{
				text: 'Peso',
				datafield: 'peso',
				width: 60,
				height: 20,
				cellsformat: 'd2',
				cellsalign: 'center',
				columntype: 'numberinput',
				editable: false,
			},			
			{
				text: 'Facturado',
				datafield: 'Valor',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{
				text: 'Unitario',
				datafield: 'Unitario',
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
		]
	});
	$("#reportes_compras_mov_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#reportes_compras_mov_items_grid").on('celldoubleclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Factura")
		{
			Interno = value;
			if (Interno != "")
				$("#Reportes_Compras_Movs_Compra_Window").jqxWindow('open');
		}
		
	});
	
	// ------------------------------------------ WINDOWS
	//--- Compra
	$("#Reportes_Compras_Movs_Compra_Window").jqxWindow({
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
	$("#Reportes_Compras_Movs_Compra_Window").on('open', function (event)
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
				$("#Reportes_Compras_Movs_Compra_Content").html(data);
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
<div id="Reportes_Compras_Movs_Compra_Window">
	<div id="Reportes_Compras_Movs_Compra_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
		<div style="margin-left: 400px;">Ver Orden de Compra</div>
	</div>
	<div id="Reportes_Compras_Movs_Compra_Content" class="WindowContainer">
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px; text-align:center;">
		<tr>
			<td>
				Producto
			</td>
			<td>
				Codigo
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
		</tr>
		<tr>
			<td>
				<div id="reportes_compras_mov_producto"></div>
			</td>
			<td>
				<div id="reportes_compras_mov_codigo"></div>
			</td>
			<td>
				<div id="reportes_compras_mov_categoria"></div>
			</td>
			<td>
				<div id="reportes_compras_mov_grupo"></div>
			</td>
			<td>
				<div id="reportes_compras_mov_subgrupo"></div>
			</td>
			<td>
				<div id="reportes_compras_mov_fecha_ini"></div>
			</td>
			<td>
				<div id="reportes_compras_mov_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				Tercero
			</td>
			<td>
				Tercero ID
			</td>
			<td colspan="5">
				<li style="padding-left:30px;">
					Estado
				</li>
				<li style="padding-left:60px;">
					Interno
				</li>
				<li style="padding-left:60px;">
					Factura
				</li>
				<li style="padding-left:65px;">
					Pedido
				</li>
			</td>
		</tr>
		<tr>
			<td>
				<div id="reportes_compras_mov_cliente"></div>
			</td>
			<td>
				<div id="reportes_compras_mov_cliente_ID"></div>
			</td>
			<td colspan="5">
				<li>
					<div id="reportes_compras_mov_estado"></div>
				</li>
				<li style="padding-left:4px;">
					<input type="text" id="reportes_compras_mov_interno"/>
				</li>
				<li style="padding-left:4px;">
					<input type="text" id="reportes_compras_mov_factura"/>
				</li>
				<li style="padding-left:4px;">
					<input type="text" id="reportes_compras_mov_pedido"/>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			</td>
			<td colspan="5">
				<li style="margin-left: 35px;">
					Total Facturado
				</li>
				<li style="margin-left: 65px;">
					Ultimo Costo
				</li>
				<li style="margin-left: 40px;">
					Costo Prom. Invent.
				</li>
				<li style="margin-left: 35px;">
					Total Costo Prom.
				</li>
			</td>
		</tr>
		<tr>
			<td>
				<li>
					<input type="button" id="reportes_compras_mov_export" value="Exportar"/>
				</li>
				<li style="padding-left:4px;">
					<input type="button" id="reportes_compras_mov_imprimir1" value="Proveedor"/>
				</li>
			</td>
			<td>
				<input type="button" id="reportes_compras_mov_imprimir2" value="Producto"/>
			</td>
			<td colspan="5">
				<li>
					<div id="reportes_compras_mov_total"></div>
				</li>
				<li style="padding-left:4px;">
					<div id="reportes_compras_mov_ultimo_costo"></div>
				</li>
				<li style="padding-left:4px;">
					<div id="reportes_compras_mov_costo_promedio"></div>
				</li>
				<li style="padding-left:4px;">
					<div id="reportes_compras_mov_total_costo_promedio"></div>
				</li>
			</td>
		</tr>
	</table>
	
	<div id="reportes_compras_mov_items_grid"></div>
	
</div>
