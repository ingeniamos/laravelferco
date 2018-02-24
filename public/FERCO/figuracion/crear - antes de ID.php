<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Orden_Produccion = "";
	var ClienteID = "";
	var FiguraID = "";
	var Characters = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P"];
	var kArray = new Array();
	var ClientesArray = new Array();
	var DimensionesArray = new Array();
	var ImagenArray = new Array();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Figuracion_Content");
	var Body = document.getElementById("Figuracion_Crear_Content");
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
				LoadParameters();
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
	var Imprimir = false;
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
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Imprimir"] == "true")
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
	
	function ReDefine()
	{
		ClearJSON = [
			//-- part 1
			{id:"figuracion_crear_interno", type:""},
			{id:"figuracion_crear_orden_produccion", type:"jqxComboBox"},
			{id:"figuracion_crear_cliente", type:"jqxComboBox"},
			{id:"figuracion_crear_cliente_ID", type:"jqxComboBox"},
			//-- part 2
			{id:"figuracion_crear_figura", type:"jqxComboBox"},
			{id:"figuracion_crear_codigo", type:"jqxComboBox"},
			{id:"figuracion_crear_producto", type:"jqxComboBox"},
			{id:"figuracion_crear_cantidad", type:"jqxNumberInput"},
			{id:"figuracion_crear_ubicacion", type:""},
			//-- part 3
			{id:"figuracion_crear_dimensiones", type:"jqxGrid"},
			{id:"figuracion_crear_items_grid", type:"jqxGrid"},
			{id:"figuracion_crear_peso", type:"jqxNumberInput"},
		];
		
		EnableDisableJSON = [
			//-- part 1
			{id:"figuracion_crear_orden_produccion", type:"jqxComboBox"},
			{id:"figuracion_crear_cliente", type:"jqxComboBox"},
			{id:"figuracion_crear_cliente_ID", type:"jqxComboBox"},
			//-- part 2
			{id:"figuracion_crear_figura", type:"jqxComboBox"},
			{id:"figuracion_crear_codigo", type:"jqxComboBox"},
			{id:"figuracion_crear_producto", type:"jqxComboBox"},
			{id:"figuracion_crear_cantidad", type:"jqxNumberInput"},
			{id:"figuracion_crear_ubicacion", type:""},
			//-- part 3
			{id:"figuracion_crear_dimensiones", type:"jqxGrid"},
			{id:"figuracion_crear_items_grid", type:"jqxGrid"},
		];
	}
	
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Locked = false;
		Orden_Produccion = "";
		ClienteID = "";
		FiguraID = "";
		//---
		ClearAll();
		EnableDisableAll(false);
		
		if (!Admin && !Guardar)
		{
			$("#figuracion_crear_guardar").jqxButton({ disabled: true });
			$("#figuracion_crear_nuevo").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#figuracion_crear_imprimir").jqxButton({ disabled: true });
		}
	}
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadParameters()
	{
		FigurasSource.data = {"Figuracion_Figuras":true};
		ProductosSource.data = {"Productos_Figuracion":true};
		OPSource.data = {"Produccion_Ordenes_Total":"Pendiente"};
		
		var ClientesDataAdapter = new $.jqx.dataAdapter(ClientesSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#figuracion_crear_cliente").jqxComboBox({source: records});
				$("#figuracion_crear_cliente_ID").jqxComboBox({source: records});
			},
		});
		
		var ProductosDataAdapter = new $.jqx.dataAdapter(ProductosSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
					kArray[records[i]["Codigo"]] = records[i]["K"];
			},
			loadComplete: function (records)
			{
				$("#figuracion_crear_codigo").jqxComboBox({source: records});
				$("#figuracion_crear_producto").jqxComboBox({source: records});
			},
		});
		
		var FigurasDataAdapter = new $.jqx.dataAdapter(FigurasSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++) {
					DimensionesArray[records[i]["Figura"]] = records[i]["Dimensiones"];
					ImagenArray[records[i]["Figura"]] = records[i]["Imagen"];
				}
			},
			loadComplete: function (records)
			{
				$("#figuracion_crear_figura").jqxComboBox({source: records});
			},
		});
		
		var OPDataAdapter = new $.jqx.dataAdapter(OPSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
					ClientesArray[records[i]["Orden_Produccion"]] = records[i]["ClienteID"];
			},
			loadComplete: function (records)
			{
				$("#figuracion_crear_orden_produccion").jqxComboBox({source: records});
			},
		});
		
		$("#figuracion_crear_cantidad").val("0");
	}
	
	var OPSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Orden_Produccion", type: "string"},
			{ name: "ClienteID", type: "string"},
		],
		url: "modulos/datos.php",
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
	
	var FigurasSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Figura", type: "string"},
			{ name: "Dimensiones", type: "int"},
			{ name: "Imagen", type: "string"},
		],
		url: "modulos/datos.php",
	};
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'K', type: 'decimal'},
		],
		url: "modulos/datos_productos.php",
	};
		
	$("#figuracion_crear_interno").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
		disabled: true
	});
	
	$("#figuracion_crear_orden_produccion").jqxComboBox({
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar OP',
		selectedIndex: -1,
		displayMember: 'Orden_Produccion',
		valueMember: 'Orden_Produccion'
	});
	$("#figuracion_crear_orden_produccion").on("change", function (event)
	{
		if (event.args)
		{
			if (Orden_Produccion == event.args.item.value)
				return;
			
			Orden_Produccion = event.args.item.value;
			$("#figuracion_crear_cliente_ID").jqxComboBox("selectItem", ClientesArray[event.args.item.value]);
		}
		else
		{
			Orden_Produccion = "";
			ClienteID = "";
			$("#figuracion_crear_orden_produccion").jqxComboBox('clearSelection');
			$("#figuracion_crear_cliente_ID").jqxComboBox('clearSelection');
			$("#figuracion_crear_cliente").jqxComboBox('clearSelection');
		}
	});
	$("#figuracion_crear_orden_produccion").bind("bindingComplete", function (event) {
		if (Orden_Produccion != "")
			$("#figuracion_crear_orden_produccion").jqxComboBox('selectItem', Orden_Produccion);
	});
	
	$("#figuracion_crear_cliente_ID").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#figuracion_crear_cliente_ID").on("change", function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			if ($("#figuracion_crear_cliente").val() != event.args.item.value)
				$("#figuracion_crear_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#figuracion_crear_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#figuracion_crear_cliente_ID").jqxComboBox('clearSelection');
				$("#figuracion_crear_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#figuracion_crear_cliente_ID").val();
				var item = $("#figuracion_crear_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					ClienteID = "";
					$("#figuracion_crear_cliente_ID").jqxComboBox('clearSelection');
					$("#figuracion_crear_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#figuracion_crear_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#figuracion_crear_cliente_ID").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#figuracion_crear_cliente_ID").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#figuracion_crear_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#figuracion_crear_cliente").on("change", function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			if ($("#figuracion_crear_cliente_ID").val() != event.args.item.value)
				$("#figuracion_crear_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#figuracion_crear_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#figuracion_crear_cliente_ID").jqxComboBox('clearSelection');
				$("#figuracion_crear_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#figuracion_crear_cliente").val();
				
				var item = $("#figuracion_crear_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#figuracion_crear_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				ClienteID = "";
				$("#figuracion_crear_cliente_ID").jqxComboBox('clearSelection');
				$("#figuracion_crear_cliente").jqxComboBox('clearSelection');
			}
		}
	});
	$("#figuracion_crear_cliente").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#figuracion_crear_cliente").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#figuracion_crear_figura").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Figura',
		valueMember: 'Figura',
	});
	$("#figuracion_crear_figura").on("change", function (event)
	{
		if (event.args)
		{
			if (FiguraID == event.args.item.value)
				return;
			
			FiguraID = event.args.item.value;
			
			if (ImagenArray[event.args.item.value] != "")
			{
				var img = "<img id=\"figuracion_crear_imagen2\" style=\"width:200px; height:200px; margin: 0px 15px;\" src=\"images/"+ImagenArray[event.args.item.value]+"\"/>";
				$("#figuracion_crear_imagen").html(img);
			}
			else
				$("#figuracion_crear_imagen").html("");
			
			var datarow = new Array();
			$("#figuracion_crear_dimensiones").jqxGrid("clear");
			for (var i = 0; i < DimensionesArray[event.args.item.value]; i++)
			{
				var TmpData = {
					"Punto":Characters[i],
					"Dimension":"0.00",
				};
				datarow[i] = TmpData;
			}
			datarow.reverse();
			$("#figuracion_crear_dimensiones").jqxGrid("addrow", null, datarow, "first");
		}
		else
		{
			$("#figuracion_crear_imagen").html("");
			$("#figuracion_crear_dimensiones").jqxGrid("clear");
		}
	});
	$("#figuracion_crear_figura").on("bindingComplete", function (event)
	{
		if (FiguraID != "")
			$("#figuracion_crear_figura").jqxComboBox("selectItem", FiguraID);
	});
	
	$("#figuracion_crear_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#figuracion_crear_codigo").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#figuracion_crear_producto").val() != event.args.item.value)
				$("#figuracion_crear_producto").jqxComboBox('selectItem', event.args.item.value);
			
		}
		else
		{
			var item_value = $("#figuracion_crear_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#figuracion_crear_codigo").jqxComboBox('clearSelection');
				$("#figuracion_crear_codigo").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#figuracion_crear_codigo").val();
				var item = $("#figuracion_crear_codigo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#figuracion_crear_codigo").jqxComboBox('clearSelection');
					$("#figuracion_crear_codigo").jqxComboBox('clearSelection');
				}
				else
					$("#figuracion_crear_codigo").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#figuracion_crear_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Hierro',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#figuracion_crear_producto").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#figuracion_crear_codigo").val() != event.args.item.value)
				$("#figuracion_crear_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#figuracion_crear_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#figuracion_crear_codigo").jqxComboBox('clearSelection');
				$("#figuracion_crear_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#figuracion_crear_producto").val();
				
				var item = $("#figuracion_crear_producto").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#figuracion_crear_producto").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#figuracion_crear_codigo").jqxComboBox('clearSelection');
				$("#figuracion_crear_producto").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#figuracion_crear_cantidad").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999999999999.99
	});
	
	$("#figuracion_crear_ubicacion").jqxInput({
		theme: mytheme,
		width: 220,
		height: 20,
	});
	
	$("#figuracion_crear_add").jqxButton({
		theme: mytheme,
		width: 90,
		height: 25,
		template: "success"
	});
	$("#figuracion_crear_add").on("click", function()
	{
		var Figura = $("#figuracion_crear_figura").jqxComboBox("getSelectedItem");
		var Producto = $("#figuracion_crear_producto").jqxComboBox("getSelectedItem");
		var Cantidad = $("#figuracion_crear_cantidad").val();
		var Ubicacion = $("#figuracion_crear_ubicacion").val();
		
		if (!Figura) 
		{
			Alerts_Box("Favor Seleccionar una Figura.", 3);
			WaitClick_Combobox("figuracion_crear_figura");
			return;
		}
		
		if (!Producto) 
		{
			Alerts_Box("Favor Seleccionar un Producto.", 3);
			WaitClick_Combobox("figuracion_crear_producto");
			return;
		}
		
		if (Cantidad < 1) 
		{
			Alerts_Box("Favor Ingresar una Cantidad Mayor a 0.", 3);
			WaitClick_NumberInput("figuracion_crear_cantidad");
			return;
		}
		
		var FinalArray = new Array();
		var Dimensiones = new Array();
		var datainfo = $("#figuracion_crear_dimensiones").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_crear_dimensiones").jqxGrid("getrowdata", i);
			
			TmpArray["Punto"] = currentRow.Punto;
			TmpArray["Dimension"] = parseFloat(currentRow.Dimension);
			
			Dimensiones[i] = TmpArray;
		}
		
		var TmpArray = {
			"Figura":Figura.value,
			"Codigo":Producto.value,
			"K":kArray[Producto.value],
			"Nombre":Producto.label,
			"Cantidad":Cantidad,
			"Ubicacion":Ubicacion,
			"Dimensiones":JSON.stringify(Dimensiones),
		};
		FinalArray[0] = TmpArray;
		$("#figuracion_crear_items_grid").jqxGrid("addrow", null, FinalArray, "first");
		
		$("#figuracion_crear_figura").jqxComboBox("clearSelection");
		$("#figuracion_crear_codigo").jqxComboBox("clearSelection");
		$("#figuracion_crear_producto").jqxComboBox("clearSelection");
		$("#figuracion_crear_cantidad").val("0");
		$("#figuracion_crear_ubicacion").val("");
		$("#figuracion_crear_imagen").html("");
		$("#figuracion_crear_dimensiones").jqxGrid("clear");
	});
	
	$("#figuracion_crear_dimensiones").jqxGrid(
	{
		theme: mytheme,
		width: 140,
		height: 200,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{ text: 'Punto', datafield: 'Punto', editable: false, width: '30%', height: 20 },
			{
				text: 'Dimension (m)',
				datafield: 'Dimension',
				editable: true,
				width: '70%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
		]
	});
	$("#figuracion_crear_dimensiones").jqxGrid("localizestrings", localizationobj);
	
	var GridSource =
	{
		datafields:
		[
			{ name: 'Figura', type: 'string' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'K', type: 'decimal' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Ubicacion', type: 'string' },
			{ name: 'Dimensiones', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit)
		{
			commit(true);
			Calcular();
		},
		updaterow: function (rowid, rowdata, commit)
		{
			commit(true);
			Calcular();
		},
		deleterow: function (rowid, commit)
		{
			commit(true);
			Calcular();
		},
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#figuracion_crear_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 600,
		height: 200,
		source: GridDataAdapter,
		autoheight: true,
		enabletooltips: true,
		enablebrowserselection: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				width: "4.5%",
				height: 20,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#figuracion_crear_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#figuracion_crear_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#figuracion_crear_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#figuracion_crear_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Figura', datafield: 'Figura', editable: false, width: '16%', height: 20 },
			{ text: '', datafield: 'Codigo', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'K', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'Dimensiones', editable: false, width: '0%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '39.5%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: false,
				width: '20%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd3',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12, decimalDigits: 3 });
				}
			},
			{ text: 'Ubicacion', datafield: 'Ubicacion', editable: false, width: '20%', height: 20 },
		]
	});
	$("#figuracion_crear_items_grid").on("rowselect", function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		var Figura = $("#figuracion_crear_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Figura");
		$("#figuracion_crear_figura").jqxComboBox("selectItem", Figura);
		
		var Codigo = $("#figuracion_crear_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Codigo");
		var Cantidad = $("#figuracion_crear_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Cantidad");
		var Ubicacion = $("#figuracion_crear_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Ubicacion");
		var Dimensiones = JSON.parse($("#figuracion_crear_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Dimensiones"));
		var datarow = new Array();
		$("#figuracion_crear_dimensiones").jqxGrid("clear");
		for (var i = 0; i < Dimensiones.length; i++)
		{
			var TmpData = {
				"Punto":Dimensiones[i]["Punto"],
				"Dimension":parseFloat(Dimensiones[i]["Dimension"]),
			};
			datarow[i] = TmpData;
		}
		datarow.reverse();
		$("#figuracion_crear_dimensiones").jqxGrid("addrow", null, datarow, "first");
		$("#figuracion_crear_codigo").jqxComboBox("selectItem", Codigo);
		$("#figuracion_crear_cantidad").val(Cantidad);
		$("#figuracion_crear_ubicacion").val(Ubicacion);
	});
	$("#figuracion_crear_items_grid").jqxGrid("hidecolumn", "Codigo");
	$("#figuracion_crear_items_grid").jqxGrid("hidecolumn", "K");
	$("#figuracion_crear_items_grid").jqxGrid("hidecolumn", "Dimensiones");
	$("#figuracion_crear_items_grid").jqxGrid("localizestrings", localizationobj);
	
	$("#figuracion_crear_guardar").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "info"
	});
	$("#figuracion_crear_guardar").on("click", function()
	{
		if (!Admin && !Guardar)
			return;
		SaveData();
	});
	
	$("#figuracion_crear_imprimir").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "warning"
	});
	$("#figuracion_crear_imprimir").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
	});
	
	$("#figuracion_crear_nuevo").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "success"
	});
	$("#figuracion_crear_nuevo").on("click", function()
	{
		ClearDocument();
	});
	
	$("#figuracion_crear_peso").jqxNumberInput({
		theme: mytheme,
		width: 190,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: 'kg',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 18,
		max: 999999999999999999,
		disabled: true
	});
	
	function Calcular()
	{
		var Total = 0;
		var datainfo = $("#figuracion_crear_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#figuracion_crear_items_grid").jqxGrid("getrowdata", i);
			
			var K = parseFloat(currentRow.K);
			var C = parseFloat(currentRow.Cantidad);
			var L = 0;
			
			var Dimensiones = JSON.parse(currentRow.Dimensiones);
			for (var a = 0; a < Dimensiones.length; a++)
			{
				L += parseFloat(Dimensiones[a]["Dimension"]);
			}
			
			Total += (L * K) * C;
		}
		$("#figuracion_crear_peso").val(Total);
		$("#figuracion_crear_items_grid").jqxGrid("clearSelection");
		//L * K * cantidad
		// añadir columna de K en productos y hacer el check antes de añadirlos.
	}
	
	function SaveData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var OP = $("#figuracion_crear_orden_produccion").jqxComboBox("getSelectedItem");
		var Cliente = $("#figuracion_crear_cliente").jqxComboBox("getSelectedItem");
		var Peso = $("#figuracion_crear_peso").val();
		
		if (!OP) 
			OP = "";
		else
			OP = OP.value;
		
		if (!Cliente) 
		{
			Alerts_Box("Favor Seleccionar un Cliente!", 3);
			WaitClick_Combobox("figuracion_crear_cliente");
			Locked = false;
			return;
		}
		
		if (Peso < 0.01) 
		{
			Alerts_Box("No se puede crear una Cartilla con Peso = 0", 3);
			WaitClick_NumberInput("figuracion_crear_peso");
			Locked = false;
			return;
		}
		
		var FinalArray = new Array();
		var datainfo = $("#figuracion_crear_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_crear_items_grid").jqxGrid("getrowdata", i);
			
			TmpArray["Figura"] = currentRow.Figura;
			TmpArray["Codigo"] = currentRow.Codigo;
			TmpArray["Cantidad"] = parseFloat(currentRow.Cantidad);
			TmpArray["Ubicacion"] = currentRow.Ubicacion;
			TmpArray["Dimensiones"] = currentRow.Dimensiones;
			
			if (i == 0)
			{
				TmpArray["Orden_Produccion"] = OP;
				TmpArray["ClienteID"] = Cliente.value;
				TmpArray["Peso"] = Peso;
			}
			
			FinalArray[i] = TmpArray;
		}
		
		/*alert(JSON.stringify(FinalArray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "text",
			type: "POST",
			url: "modulos/guardar.php",
			data: {"Figuracion_Crear":FinalArray},
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
					break;
					
					default:
						EnableDisableAll(true);
						LoadParameters();
						Alerts_Box("Datos Guardados con Exito!", 2);
						$("#figuracion_crear_interno").val(data[0]["MESSAGE"]);
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
	
	LoadParameters();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Interno
			</td>
			<td>
				Enlazar O.P.
			</td>
			<td colspan="2">
				Cliente
			</td>
			<td>
				Cliente ID
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="figuracion_crear_interno"/>
			</td>
			<td>
				<div id="figuracion_crear_orden_produccion"></div>
			</td>
			<td colspan="2">
				<div id="figuracion_crear_cliente"></div>
			</td>
			<td>
				<div id="figuracion_crear_cliente_ID"></div>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Figura
			</td>
			<td>
				Codigo
			</td>
			<td colspan="2">
				Hierro
			</td>
			<td>
				Cantidad	
			</td>
			<td>
				Ubicacion
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<div id="figuracion_crear_figura"></div>
			</td>
			<td>
				<div id="figuracion_crear_codigo"></div>
			</td>
			<td colspan="2">
				<div id="figuracion_crear_producto"></div>
			</td>
			<td>
				<div id="figuracion_crear_cantidad"></div>
			</td>
			<td>
				<input type="text" id="figuracion_crear_ubicacion"/>
			</td>
			<td>
				<input type="button" id="figuracion_crear_add" value="Añadir"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Imagen
			</td>
			<td style="width:142px;">
				Dimensiones
			</td>
			<td colspan="4">
				Total
			</td>
			<!--<td colspan="6">
				<li>
					Imagen
				</li>
				<li style="margin-left:200px;">
					Dimensiones
				</li>
				<li style="margin-left:80px;">
					Total
				</li>
			</td>-->
		</tr>
		<tr>
			<td colspan="2">
				<div id="figuracion_crear_imagen" style="width:230px; height:200px; border:1px solid #d3d3d3;"></div>
			</td>
			<td>
				<div id="figuracion_crear_dimensiones"></div>
			</td>
			<td colspan="4" rowspan="6" style="vertical-align:-webkit-baseline-middle;">
				<div id="figuracion_crear_items_grid"></div>
			</td>
			<!--<td colspan="6">
				<li>
					<div id="figuracion_crear_imagen" style="width:230px; height:200px; border:1px solid #d3d3d3;"></div>
				</li>
				<li style="margin-left:10px;">
					<div id="figuracion_crear_dimensiones"></div>
				</li>
				<li style="margin-left:10px;">
					<div id="figuracion_crear_items_grid"></div>
				</li>
			</td>-->
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<li style="margin-top: 6px; margin-left: 150px;">
					Total
				</li>
				<li style="margin-left:10px;">
					<div id="figuracion_crear_peso"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<li>
					<input type="button" id="figuracion_crear_guardar" value="Guardar"/>
				</li>
				<li style="margin-left:10px;">
					<input type="button" id="figuracion_crear_imprimir" value="Imprimir"/>
				</li>
				<li style="margin-left:10px;">
					<input type="button" id="figuracion_crear_nuevo" value="Nuevo"/>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
		</tr>
	</table>
</div>