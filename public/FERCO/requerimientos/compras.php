<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Timer = 0;
	var Grupo = "";
	var SubGrupo = "";
	var SubGrupo2 = "";
	var ClienteID = "";
	var TipoServicio = "";
	var TipoDescuento = "";
	var UnidadArray = new Array();
	var ValorArray = new Array();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Requerimientos_Content");
	var Body = document.getElementById("Requerimientos_Compras");
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
				SystemMap("Compras", true);
				ReDefine();
				LoadParameters(true);
				//---
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
	var Guardar = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador")
		{
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Requerimientos" && $data[$i]["SubModulo"] == "Compras" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
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
	
	function ReDefine()
	{
		ClearJSON = [
			//-- part 1
			{id:"requerimientos_compras_interno", type:""},
			{id:"requerimientos_compras_factura", type:""},
			{id:"requerimientos_compras_grupo", type:"jqxComboBox"},
			{id:"requerimientos_compras_subgrupo", type:"jqxComboBox"},
			{id:"requerimientos_compras_subgrupo2", type:"jqxComboBox"},
			{id:"requerimientos_compras_fecha", type:"jqxDateTimeInput"},
			{id:"requerimientos_compras_cliente", type:"jqxComboBox"},
			{id:"requerimientos_compras_cliente_ID", type:"jqxComboBox"},
			//-- part 2
			{id:"requerimientos_compras_codigo", type:"jqxComboBox"},
			{id:"requerimientos_compras_producto", type:"jqxComboBox"},
			{id:"requerimientos_compras_cantidad", type:"jqxNumberInput"},
			{id:"requerimientos_compras_items_grid", type:"jqxGrid"},
			//-- part 3
			{id:"requerimientos_compras_observaciones", type:""},
			{id:"requerimientos_compras_subtotal", type:"jqxNumberInput"},
			{id:"requerimientos_compras_tipo_servicio_text", type:"jqxComboBox"},
			{id:"requerimientos_compras_tipo_servicio", type:"jqxNumberInput"},
			{id:"requerimientos_compras_iva", type:"jqxNumberInput"},
			{id:"requerimientos_compras_tipo_descuento_text", type:"jqxComboBox"},
			{id:"requerimientos_compras_tipo_descuento", type:"jqxNumberInput"},
			{id:"requerimientos_compras_total", type:"jqxNumberInput"},
		];
		
		EnableDisableJSON = [
			//-- part 1
			{id:"requerimientos_compras_interno", type:""},
			{id:"requerimientos_compras_factura", type:""},
			{id:"requerimientos_compras_grupo", type:"jqxComboBox"},
			{id:"requerimientos_compras_subgrupo", type:"jqxComboBox"},
			{id:"requerimientos_compras_subgrupo2", type:"jqxComboBox"},
			{id:"requerimientos_compras_fecha", type:"jqxDateTimeInput"},
			{id:"requerimientos_compras_cliente", type:"jqxComboBox"},
			{id:"requerimientos_compras_cliente_ID", type:"jqxComboBox"},
			//-- part 2
			{id:"requerimientos_compras_codigo", type:"jqxComboBox"},
			{id:"requerimientos_compras_producto", type:"jqxComboBox"},
			{id:"requerimientos_compras_cantidad", type:"jqxNumberInput"},
			{id:"requerimientos_compras_add", type:"jqxButton"},
			//{id:"requerimientos_compras_items_grid", type:"jqxGrid"},
			//-- part 3
			{id:"requerimientos_compras_observaciones", type:""},
			{id:"requerimientos_compras_tipo_servicio_text", type:"jqxComboBox"},
			{id:"requerimientos_compras_tipo_servicio", type:"jqxNumberInput"},
			{id:"requerimientos_compras_tipo_descuento_text", type:"jqxComboBox"},
			{id:"requerimientos_compras_tipo_descuento", type:"jqxNumberInput"},
			{id:"requerimientos_compras_guardar", type:"jqxButton"},
			//{id:"requerimientos_compras_nuevo", type:"jqxButton"},
		];
	}
	
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Locked = false;
		Grupo = "";
		SubGrupo = "";
		SubGrupo2 = "";
		ClienteID = "";
		TipoServicio = "";
		TipoDescuento = "";
		//---
		ClearAll();
		EnableDisableAll(false);
		
		if (!Admin && !Guardar)
		{
			$("#requerimientos_compras_guardar").jqxButton({ disabled: true });
			$("#requerimientos_compras_nuevo").jqxButton({ disabled: true });
		}
		
		/*if (!Admin && !Imprimir)
		{
			$("#requerimientos_compras_imprimir").jqxButton({ disabled: true });
		}*/
	}
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadParameters(Refresh)
	{
		GrupoSource.data = {"Caja_Grupo":"Egresos"};
		//ClientesSource.data = {"Clientes_Nomina":true};
		ProductosSource.data = {"Requerimientos_Productos":true};
		ServicioSource.data = {"OtroSrv":true};
		DescuentoSource.data = {"TipoDcto":true};
		
		var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
		$("#requerimientos_compras_grupo").jqxComboBox({source: GrupoDataAdapter});
		
		var ClientesDataAdapter = new $.jqx.dataAdapter(ClientesSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#requerimientos_compras_cliente_ID").jqxComboBox({source: records});
				$("#requerimientos_compras_cliente").jqxComboBox({source: records});
			},
		});
		
		var ProductosDataAdapter = new $.jqx.dataAdapter(ProductosSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++) {
					UnidadArray[records[i]["Codigo"]] = records[i]["Unidad"];
					ValorArray[records[i]["Codigo"]] = records[i]["Valor"];
				}
			},
			loadComplete: function (records)
			{
				$("#requerimientos_compras_codigo").jqxComboBox({source: records});
				$("#requerimientos_compras_producto").jqxComboBox({source: records});
			},
		});
		
		var ServicioDataAdapter = new $.jqx.dataAdapter(ServicioSource);
		$("#requerimientos_compras_tipo_servicio_text").jqxComboBox({source: ServicioDataAdapter});
		
		var DescuentoDataAdapter = new $.jqx.dataAdapter(DescuentoSource);
		$("#requerimientos_compras_tipo_descuento_text").jqxComboBox({source: DescuentoDataAdapter});
		
		if (Refresh)
		{
			$("#requerimientos_compras_items_grid2").jqxGrid("updatebounddata");
		}
		else
		{
			Grid2Source.data = {"Requerimientos_Productos_Faltantes":true};
			var Grid2DataAdapter = new $.jqx.dataAdapter(Grid2Source);
			$("#requerimientos_compras_items_grid2").jqxGrid({source: Grid2DataAdapter});
		}
	}
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'}
		],
		url: "modulos/parametros.php",
	};
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo', type: 'string'}
		],
		url: "modulos/parametros.php",
	};
	
	var SubGrupo2Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo2', type: 'string'}
		],
		url: "modulos/parametros.php",
	};
	
	var ClientesSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
	};
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'Unidad', type: 'string'},
			{ name: 'Peso', type: 'decimal'},
			{ name: 'Valor', type: 'decimal'},
		],
		url: "modulos/datos_productos.php",
	};
	
	var ServicioSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/parametros.php",
	};
	
	var DescuentoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/parametros.php",
	};
		
	$("#requerimientos_compras_interno").jqxInput({
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: true
	});
	
	$("#requerimientos_compras_factura").jqxInput({
		theme: mytheme,
		width: 100,
		height: 20,
	});
	
	$("#requerimientos_compras_grupo").jqxComboBox
	({
		theme: mytheme,
		height: 20,
		width: 130,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Grupo',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
	});
	$("#requerimientos_compras_grupo").on('change', function (event)
	{
		if (event.args)
		{
			if (Grupo == event.args.item.value)
				return;
			
			Grupo = event.args.item.value;
			
			$("#requerimientos_compras_subgrupo").jqxComboBox('clearSelection');
			$("#requerimientos_compras_subgrupo2").jqxComboBox('clearSelection');
			
			clearTimeout(Timer);
			Timer = setTimeout(function()
			{
				SubGrupoSource.data = {"Caja_SubGrupo": event.args.item.value};
				SubGrupo2Source.data = {"Caja_SubGrupo2": event.args.item.value};
				SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
				SubGrupo2DataAdapter = new $.jqx.dataAdapter(SubGrupo2Source);
				$("#requerimientos_compras_subgrupo").jqxComboBox({source: SubGrupoDataAdapter});
				$("#requerimientos_compras_subgrupo2").jqxComboBox({source: SubGrupo2DataAdapter});
			},500);
		}
		else
		{
			Grupo = "";
			SubGrupo = "";
			SubGrupo2 = "";
			$("#requerimientos_compras_grupo").jqxComboBox('clearSelection');
			$("#requerimientos_compras_subgrupo").jqxComboBox('clearSelection');
			$("#requerimientos_compras_subgrupo2").jqxComboBox('clearSelection');
		}
	});
	$("#requerimientos_compras_grupo").on("bindingComplete", function (event)
	{
		if (Grupo != "")
			$("#requerimientos_compras_grupo").jqxComboBox("selectItem", Grupo);
	});
	
	$("#requerimientos_compras_subgrupo").jqxComboBox
	({
		theme: mytheme,
		height: 20,
		width: 160,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar SubGrupo',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
	});
	$("#requerimientos_compras_subgrupo").on('change', function (event)
	{
		if (event.args)
			SubGrupo = event.args.item.value;
		else
		{
			SubGrupo = "";
			$("#requerimientos_compras_subgrupo").jqxComboBox('clearSelection');
		}
	});
	$("#requerimientos_compras_subgrupo").on("bindingComplete", function (event)
	{
		if (SubGrupo != "")
			$("#requerimientos_compras_subgrupo").jqxComboBox("selectItem", SubGrupo);
	});
	
	$("#requerimientos_compras_subgrupo2").jqxComboBox
	({
		theme: mytheme,
		height: 20,
		width: 160,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar SubGrupo 2',
		selectedIndex: -1,
		displayMember: 'SubGrupo2',
		valueMember: 'SubGrupo2',
	});
	$("#requerimientos_compras_subgrupo2").on('change', function (event)
	{
		if (event.args)
			SubGrupo2 = event.args.item.value;
		else
		{
			SubGrupo2 = "";
			$("#requerimientos_compras_subgrupo2").jqxComboBox('clearSelection');
		}
	});
	$("#requerimientos_compras_subgrupo2").on("bindingComplete", function (event)
	{
		if (SubGrupo2 != "")
			$("#requerimientos_compras_subgrupo2").jqxComboBox("selectItem", SubGrupo2);
	});
	
	$("#requerimientos_compras_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#requerimientos_compras_fecha").jqxDateTimeInput('setDate', new Date(currenttime));
	
	$("#requerimientos_compras_cliente_ID").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#requerimientos_compras_cliente_ID").on('change', function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			if ($("#requerimientos_compras_cliente").val() != event.args.item.value)
				$("#requerimientos_compras_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_compras_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#requerimientos_compras_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_compras_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_compras_cliente_ID").val();
				var item = $("#requerimientos_compras_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					ClienteID = "";
					$("#requerimientos_compras_cliente_ID").jqxComboBox('clearSelection');
					$("#requerimientos_compras_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#requerimientos_compras_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#requerimientos_compras_cliente_ID").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#requerimientos_compras_cliente_ID").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#requerimientos_compras_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 278,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Empleado',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#requerimientos_compras_cliente").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_compras_cliente_ID").val() != event.args.item.value)
				$("#requerimientos_compras_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_compras_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#requerimientos_compras_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_compras_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_compras_cliente").val();
				
				var item = $("#requerimientos_compras_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#requerimientos_compras_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				ClienteID = "";
				$("#requerimientos_compras_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_compras_cliente").jqxComboBox('clearSelection');
			}
		}
	});
	$("#requerimientos_compras_cliente").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#requerimientos_compras_cliente").jqxComboBox("selectItem", ClienteID);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Add_Row()
	{
		if (Locked == true)
			return;
			
		Locked = true;
		
		var Producto = $("#requerimientos_compras_producto").jqxComboBox("getSelectedItem");
		var Cantidad = $("#requerimientos_compras_cantidad").val();
		
		if (!Producto) {
			Alerts_Box("Favor Ingresar un Producto!", 3);
			WaitClick_Combobox("requerimientos_compras_producto");
			Locked = false;
			return;
		}
		
		if (Cantidad < 1) {
			Alerts_Box("Favor Ingresar una cantidad mayor a 0!", 3);
			WaitClick_NumberInput("requerimientos_compras_cantidad");
			Locked = false;
			return;
		}
		
		var Unidad = UnidadArray[Producto.value];
		var Valor = ValorArray[Producto.value];
		
		var datainfo = $("#requerimientos_compras_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#requerimientos_compras_items_grid").jqxGrid("getrowdata", i);
			if (currentRow.Codigo == Producto.value)
			{
				var total = Cantidad + currentRow.Cantidad;
				var datarow = [{
					"Codigo":currentRow.Codigo,
					"Nombre":currentRow.Nombre,
					"Unidad":currentRow.Unidad,
					"Valor":currentRow.Valor,
					"Cantidad":total,
				}];
				var id = $("#requerimientos_compras_items_grid").jqxGrid("getrowid", i);
				$("#requerimientos_compras_items_grid").jqxGrid("deleterow", id);
				$("#requerimientos_compras_items_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#requerimientos_compras_codigo").jqxComboBox("clearSelection");
				$("#requerimientos_compras_producto").jqxComboBox("clearSelection");
				$("#requerimientos_compras_cantidad").val("0");
				Locked = false;
				return;
			}
		}
		
		var datarow = [{
			"Codigo":Producto.value,
			"Nombre":Producto.label,
			"Unidad":Unidad,
			"Valor":Valor,
			"Cantidad":Cantidad,
		}];
		
		$("#requerimientos_compras_items_grid").jqxGrid("addrow", count, datarow);
		$("#requerimientos_compras_codigo").jqxComboBox("clearSelection");
		$("#requerimientos_compras_producto").jqxComboBox("clearSelection");
		$("#requerimientos_compras_cantidad").val("0");
		Locked = false;
	};
	
	$("#requerimientos_compras_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar...',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#requerimientos_compras_codigo").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_compras_producto").val() != event.args.item.value)
				$("#requerimientos_compras_producto").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_compras_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#requerimientos_compras_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_compras_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_compras_codigo").val();
				var item = $("#requerimientos_compras_codigo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#requerimientos_compras_codigo").jqxComboBox('clearSelection');
					$("#requerimientos_compras_producto").jqxComboBox('clearSelection');
				}
				else
					$("#requerimientos_compras_codigo").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#requerimientos_compras_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#requerimientos_compras_producto").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_compras_codigo").val() != event.args.item.value)
				$("#requerimientos_compras_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_compras_producto").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#requerimientos_compras_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_compras_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_compras_producto").val();
				
				var item = $("#requerimientos_compras_producto").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#requerimientos_compras_producto").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#requerimientos_compras_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_compras_producto").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#requerimientos_compras_cantidad").jqxNumberInput({
		theme: mytheme,
		width: 80,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:6,
	});
	
	$("#requerimientos_compras_add").jqxButton({
		theme: mytheme,
		width: 70,
		height: 25,
		template: "success"
	});
	$("#requerimientos_compras_add").on('click', function()
	{
		Add_Row();
	});
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Codigo', type: 'string' },
			{ name: 'Producto', type: 'string' },
			{ name: 'Unidad', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Valor', type: 'decimal' },
			{ name: 'Subtotal', type: 'decimal' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
			Calcular();
		},
		updaterow: function (rowid, rowdata, commit) {
			commit(true);
			Calcular();
		},
		deleterow: function (rowid, commit) {
			commit(true);
			Calcular();
		},
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#requerimientos_compras_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 300,
		source: GridDataAdapter,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				width: "3%",
				height: 20,
				pinned: true,
				filterable: false,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#requerimientos_compras_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#requerimientos_compras_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#requerimientos_compras_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#requerimientos_compras_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '10%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '35%', height: 20 },
			{ text: 'Und', datafield: 'Unidad', editable: false, width: '6%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: '15%',
				height: 20,
				editable: true,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Unitario',
				datafield: 'Valor',
				width: "15%",
				height: 20,
				editable: true,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value) {
					if (value <= 0) {
						return { result: false, message: "El precio del Producto debe ser mayor a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'SubTotal',
				datafield: 'Subtotal',
				width: "16%",
				height: 20,
				editable: false,
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = Math.round(parseFloat(rowdata.Valor) * parseFloat(rowdata.Cantidad));
					return "<div style='margin: 4px;' class='jqx-right-align'>" + GridDataAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
		]
	});
	$("#requerimientos_compras_items_grid").jqxGrid('localizestrings', localizationobj);
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function SaveData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Grupo = $("#requerimientos_compras_grupo").jqxComboBox("getSelectedItem");
		var SubGrupo = $("#requerimientos_compras_subgrupo").jqxComboBox("getSelectedItem");
		var SubGrupo2 = $("#requerimientos_compras_subgrupo2").jqxComboBox("getSelectedItem");
		var Fecha = GetFormattedDate($("#requerimientos_compras_fecha").jqxDateTimeInput("getDate"));
		var Cliente = $("#requerimientos_compras_cliente").jqxComboBox("getSelectedItem");
		
		if (!Grupo) 
		{
			Alerts_Box("Favor Ingresar un Grupo.", 3);
			WaitClick_Combobox("requerimientos_compras_grupo");
			Locked = false;
			return;
		}
		
		if (!SubGrupo) 
		{
			Alerts_Box("Favor Ingresar un SubGrupo.", 3);
			WaitClick_Combobox("requerimientos_compras_subgrupo");
			Locked = false;
			return;
		}
		
		if (!SubGrupo2) 
		{
			Alerts_Box("Favor Ingresar un SubGrupo2.", 3);
			WaitClick_Combobox("requerimientos_compras_subgrupo2");
			Locked = false;
			return;
		}
		
		if (!Cliente) 
		{
			Alerts_Box("Favor Ingresar un Empleado.", 3);
			WaitClick_Combobox("requerimientos_compras_cliente");
			Locked = false;
			return;
		}
		
		var FinalArray = new Array();
		var datainfo = $("#requerimientos_compras_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#requerimientos_compras_items_grid").jqxGrid("getrowdata", i);
			
			TmpArray["Codigo"] = currentRow.Codigo;
			TmpArray["Valor"] = parseFloat(currentRow.Valor);
			TmpArray["Cantidad"] = parseFloat(currentRow.Cantidad);
			
			if (i == 0)
			{
				TmpArray["Factura"] = $("#requerimientos_compras_factura").val();
				TmpArray["Grupo"] = Grupo.value;
				TmpArray["SubGrupo"] = SubGrupo.value;
				TmpArray["SubGrupo2"] = SubGrupo2.value;
				TmpArray["ClienteID"] = Cliente.value;
				TmpArray["Fecha"] = Fecha;
				TmpArray["Observaciones"] = $("#requerimientos_compras_observaciones").val();
				TmpArray["SubTotal"] = parseFloat($("#requerimientos_compras_subtotal").val());
				TmpArray["Servicio_Desc"] = $("#requerimientos_compras_tipo_servicio_text").val();
				TmpArray["Servicio"] = parseFloat($("#requerimientos_compras_tipo_servicio").val());
				TmpArray["IVA"] = parseFloat($("#requerimientos_compras_iva").val());
				TmpArray["Descuento_Desc"] = $("#requerimientos_compras_tipo_descuento_text").val();
				TmpArray["Descuento"] = parseFloat($("#requerimientos_compras_tipo_descuento").val());
				TmpArray["Total"] = parseFloat($("#requerimientos_compras_total").val());
			}
			
			FinalArray[i] = TmpArray;
		}
		
		/*alert(JSON.stringify(FinalArray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "modulos/guardar.php",
			data: {"Requerimientos_Compras":FinalArray},
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
					break;
					
					default:
						EnableDisableAll(true);
						Alerts_Box("Datos Guardados con Exito!", 2);
						$("#requerimientos_compras_interno").val(data[0]["MESSAGE"]);
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
	
	function Calcular(toFocus, type)
	{
		var information = $("#requerimientos_compras_items_grid").jqxGrid("getdatainformation");
		var rowscount = information.rowscount;
		var Total = 0;
		var SubTotal = 0;
		var TmpTotal = 0;
		for (i = 0; i < rowscount; i++)
		{
			var currentRow = $("#requerimientos_compras_items_grid").jqxGrid("getrowdata", i);
			TmpTotal += parseFloat(currentRow.Valor) * parseFloat(currentRow.Cantidad);
		};
		
		TmpTotal = Math.round(TmpTotal);
		SubTotal = TmpTotal;
		var Servicio = 0;
		var Descuento = 0;
		
		var ServicioText = $("#requerimientos_compras_tipo_servicio_text").jqxComboBox("getSelectedItem");
		if (ServicioText)
		{
			Servicio = $("#requerimientos_compras_tipo_servicio").val();
		}
		else
		{
			Servicio = $("#requerimientos_compras_tipo_servicio").val();
			if (Servicio > 0) {
				$("#requerimientos_compras_tipo_servicio").val("0");
				Servicio = 0;
			}
		}
		
		TmpTotal = Math.round(TmpTotal + Servicio);
		var IVA = Math.round(TmpTotal - (TmpTotal / 1.16));
		
		var DescuentoText = $("#requerimientos_compras_tipo_descuento_text").jqxComboBox("getSelectedItem");
		if (DescuentoText)
		{
			Descuento = $("#requerimientos_compras_tipo_descuento").val();
			
			if (Descuento <= 0 && DescuentoText.value.indexOf("IVA") > 0)
			{
				Descuento = IVA;
				$("#requerimientos_compras_tipo_descuento").val(Descuento);
			}
			else if (Descuento != IVA && DescuentoText.value.indexOf("IVA") > 0)
			{
				Descuento = IVA;
				$("#requerimientos_compras_tipo_descuento").val(Descuento);
			}
			else if (Descuento <= 0 && DescuentoText.value.indexOf("IVA") < 1)
			{
				$("#requerimientos_compras_tipo_descuento").val("0");
				Descuento = 0;
			}
		}
		else
		{
			Descuento = $("#requerimientos_compras_tipo_descuento").val();
			if (Descuento > 0)
				$("#requerimientos_compras_tipo_descuento").val("0");
		}
		
		Total = Math.round(TmpTotal - Descuento);
		if (Total < 0)
			Total = 0;
		
		$("#requerimientos_compras_subtotal").val(SubTotal);
		$("#requerimientos_compras_iva").val(IVA);
		$("#requerimientos_compras_total").val(Total);
		if (toFocus != undefined && toFocus != "")
		{
			switch(type)
			{
				case "jqxComboBox":
					$("#"+toFocus+"").jqxComboBox('focus');
				break;
				
				case "jqxNumberInput":
					$("#"+toFocus+"").jqxNumberInput('focus');
				break;
				
				default:
				break;
			}
		}
	};
	
	$("#requerimientos_compras_subtotal_text").jqxInput({
		theme: mytheme,
		height: 22,
		width: 180,
		//textAlign: 'center',
		disabled: true,
		placeHolder: "               SUBTOTAL",
	});
	
	$("#requerimientos_compras_subtotal").jqxNumberInput({
		theme: mytheme,
		height: 22,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		//decimalSeparator: ","
		disabled: true,
	});
	
	$("#requerimientos_compras_tipo_servicio_text").jqxComboBox({
		theme: mytheme,
		height: 22,
		width: 180,
		dropDownHeight: 100,
		promptText: 'Tipo Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Nombre'
	});
	$("#requerimientos_compras_tipo_servicio_text").on('change', function (event)
	{
		if (event.args)
			TipoServicio = event.args.item.value;
		else
		{
			TipoServicio = "";
			$("#requerimientos_compras_tipo_servicio_text").jqxComboBox('clearSelection');
		}
		Calcular("requerimientos_compras_tipo_servicio_text", "jqxComboBox");
	});
	$("#requerimientos_compras_tipo_servicio_text").on("bindingComplete", function (event)
	{
		if (TipoServicio != "")
			$("#requerimientos_compras_tipo_servicio_text").jqxComboBox("selectItem", TipoServicio);
	});
	
	$("#requerimientos_compras_tipo_servicio").jqxNumberInput({
		theme: mytheme,
		height: 22,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$("#requerimientos_compras_tipo_servicio").on('change', function (event) 
	{
		if (event.args.value < 0)
		{
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("requerimientos_compras_tipo_servicio");
		}
		Calcular("requerimientos_compras_tipo_servicio", "jqxNumberInput");
	});
	
	$("#requerimientos_compras_iva_text").jqxInput({
		theme: mytheme,
		height: 22,
		width: 180,
		disabled: true,
		placeHolder: "                     IVA",
	});
	
	$("#requerimientos_compras_iva").jqxNumberInput({
		theme: mytheme,
		height: 22,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	$("#requerimientos_compras_tipo_descuento_text").jqxComboBox({
		theme: mytheme,
		height: 22,
		width: 180,
		dropDownHeight: 100,
		promptText: 'Tipo Descuento',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Nombre'
	});
	$("#requerimientos_compras_tipo_descuento_text").on('change', function (event)
	{
		if (event.args)
			TipoDescuento = event.args.item.value;
		else
		{
			TipoDescuento = "";
			$("#requerimientos_compras_tipo_descuento_text").jqxComboBox('clearSelection');
		}
		Calcular("requerimientos_compras_tipo_descuento_text", "jqxComboBox");
	});
	$("#requerimientos_compras_tipo_descuento_text").on("bindingComplete", function (event)
	{
		if (TipoDescuento != "")
			$("#requerimientos_compras_tipo_descuento_text").jqxComboBox("selectItem", TipoDescuento);
	});
	
	$("#requerimientos_compras_tipo_descuento").jqxNumberInput({
		theme: mytheme,
		height: 22,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$("#requerimientos_compras_tipo_descuento").on('change', function (event) 
	{
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("requerimientos_compras_tipo_descuento");
		}
		Calcular("requerimientos_compras_tipo_descuento", "jqxNumberInput");
	});
	
	$("#requerimientos_compras_total_text").jqxInput({
		theme: mytheme,
		height: 22,
		width: 180,
		disabled: true,
		placeHolder: "                  TOTAL",
	});
	
	$("#requerimientos_compras_total").jqxNumberInput({
		theme: mytheme,
		height: 22,
		width: 180,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		disabled: true,
	});
	
	var Grid2Source =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Unidad', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
		],
		url: "modulos/datos_productos.php",
	};
	
	$("#requerimientos_compras_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 420,
		height: 220,
		//pageable: true,
		//pagesizeoptions: ['5', '20', '30', '50', '100'],
		//pagesize: 5,
		sortable: true,
		editable: false,
		columns:
		[
			{ text: 'Codigo', datafield: 'Codigo', width: '20%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', width: '50%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: '30%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
		]
	});
	$("#requerimientos_compras_items_grid2").jqxGrid('localizestrings', localizationobj);
	
	$("#requerimientos_compras_guardar").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "info"
	});
	$("#requerimientos_compras_guardar").on('click', function()
	{
		if (!Admin && !Guardar)
			return;
		SaveData();
	});
	
	$("#requerimientos_compras_nuevo").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "success"
	});
	$("#requerimientos_compras_nuevo").on('click', function()
	{
		ClearDocument();
	});
	
	LoadParameters();
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin:5px 0px 20px 0px;">
		<tr>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="requerimientos_compras_interno"/>
			</td>
			<td>
				Factura
			</td>
			<td>
				<input type="text" id="requerimientos_compras_factura"/>
			</td>
			<td>
				Grupo
			</td>
			<td>
				<div id="requerimientos_compras_grupo"></div>
			</td>
			<td>
				SubGrupo
			</td>
			<td>
				<div id="requerimientos_compras_subgrupo"></div>
			</td>
			<td>
				SubGrupo 2
			</td>
			<td>
				<div id="requerimientos_compras_subgrupo2"></div>
			</td>
		</tr>
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<div id="requerimientos_compras_fecha"></div>
			</td>
			<td>
				Proveedor
			</td>
			<td colspan="3">
				<div id="requerimientos_compras_cliente"></div>
			</td>
			<td>
				Proveedor ID
			</td>
			<td>
				<div id="requerimientos_compras_cliente_ID"></div>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="1" style="border-left: 1px solid #A4BED4; border-top: 1px solid #A4BED4; border-right: 1px solid #A4BED4; text-align:center; width:602px;">
		<tr>
			<td>
				<div id="requerimientos_compras_codigo"></div>
			</td>
			<td>
				<div id="requerimientos_compras_producto"></div>
			</td>
			<td>
				<div id="requerimientos_compras_cantidad"></div>
			</td>
			<td>
				<input type="button" id="requerimientos_compras_add" value="Añadir">
			</td>
		</tr>
	</table>
	
	<div id="requerimientos_compras_items_grid"></div>
	
	<table cellpadding="2" cellspacing="1">
		<tr>
			<td>
				Poductos Faltantes
			</td>
			<td>
				Observaciones
			</td>
			<!--<td style="width: 370px;">
				&nbsp;
			</td>-->
			<td>
				<input type="text" id="requerimientos_compras_subtotal_text"/>
			</td>
			<td>
				<div id="requerimientos_compras_subtotal">
				</div>
			</td>
		</tr>
		<tr>
			<td rowspan="16">
				<div id="requerimientos_compras_items_grid2"></div>
			</td>
			<td rowspan="4">
				<textarea rows="7" cols="25" id="requerimientos_compras_observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
			<!--<td rowspan="4">
				&nbsp;
			</td>-->
			<td>
				<div id="requerimientos_compras_tipo_servicio_text"></div>
			</td>
			<td>
				<div id="requerimientos_compras_tipo_servicio"></div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="requerimientos_compras_iva_text"/>
			</td>
			<td>
				<div id="requerimientos_compras_iva"></div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="requerimientos_compras_tipo_descuento_text"></div>
			</td>
			<td>
				<div id="requerimientos_compras_tipo_descuento"></div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="requerimientos_compras_total_text"/>
			</td>
			<td>
				<div id="requerimientos_compras_total"></div>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td colspan="2">
				<li style="margin-left:160px;">
					<input type="button" id="requerimientos_compras_guardar" value="Guardar">
				</li>
				<li style="margin-left:5px;">
					<input type="button" id="requerimientos_compras_nuevo" value="Nuevo">
				</li>
			</td>
		</tr>
		<tr>
			<td rowspan="10" colspan="3">
				&nbsp;
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<!--<div id="Parte2">
	<table cellpadding="2" cellspacing="1">
		<tr>
			<td style="width: 785px;">
				&nbsp;
			</td>
			<td>
				<input type="button" id="requerimientos_compras_guardar" value="Guardar">
			</td>
			<td>
				<input type="button" id="requerimientos_compras_nuevo" value="Nuevo">
			</td>
		</tr>
	</table>
</div>-->