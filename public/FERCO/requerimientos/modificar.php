<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Interno = "";
	var Prioridad = "Normal";
	var ClienteID = "";
	var Timer = 0;
	var UnidadArray = new Array();
	var ExistenciaArray = new Array();
	var ValorArray = new Array();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Requerimientos_Content");
	var Body = document.getElementById("Requerimientos_Modificar");
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
				SystemMap("Modificar Solicitud", true);
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
				if ($data[$i]["Modulo"] == "Requerimientos" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Guardar"] == "true")
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
			{id:"requerimientos_modificar_interno", type:"jqxComboBox"},
			{id:"requerimientos_modificar_fecha", type:"jqxDateTimeInput"},
			{id:"requerimientos_modificar_cliente", type:"jqxComboBox"},
			{id:"requerimientos_modificar_cliente_ID", type:"jqxComboBox"},
			//-- part 2
			{id:"requerimientos_modificar_codigo", type:"jqxComboBox"},
			{id:"requerimientos_modificar_producto", type:"jqxComboBox"},
			{id:"requerimientos_modificar_existencia", type:"jqxNumberInput"},
			{id:"requerimientos_modificar_cantidad", type:"jqxNumberInput"},
			{id:"requerimientos_modificar_items_grid", type:"jqxGrid"},
			//-- part 3
			{id:"requerimientos_modificar_observaciones", type:""},
		];
		
		EnableDisableJSON = [
			//-- part 1
			//{id:"requerimientos_modificar_interno", type:"jqxComboBox"},
			{id:"requerimientos_modificar_fecha", type:"jqxDateTimeInput"},
			{id:"requerimientos_modificar_prioridad", type:"jqxComboBox"},
			{id:"requerimientos_modificar_cliente", type:"jqxComboBox"},
			{id:"requerimientos_modificar_cliente_ID", type:"jqxComboBox"},
			//-- part 2
			{id:"requerimientos_modificar_codigo", type:"jqxComboBox"},
			{id:"requerimientos_modificar_producto", type:"jqxComboBox"},
			{id:"requerimientos_modificar_existencia", type:"jqxNumberInput"},
			{id:"requerimientos_modificar_cantidad", type:"jqxNumberInput"},
			{id:"requerimientos_modificar_add", type:"jqxButton"},
			//{id:"requerimientos_modificar_items_grid", type:"jqxGrid"},
			//-- part 3
			{id:"requerimientos_modificar_observaciones", type:""},
			{id:"requerimientos_modificar_guardar", type:"jqxButton"},
			//{id:"requerimientos_modificar_nuevo", type:"jqxButton"},
		];
	}
	
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Locked = false;
		Interno = "";
		Prioridad = "Normal";
		ClienteID = "";
		//---
		ClearAll();
		EnableDisableAll(false);
		
		if (!Admin && !Guardar)
		{
			$("#requerimientos_modificar_guardar").jqxButton({ disabled: true });
			$("#requerimientos_modificar_nuevo").jqxButton({ disabled: true });
		}
		
		/*if (!Admin && !Imprimir)
		{
			$("#requerimientos_modificar_imprimir").jqxButton({ disabled: true });
		}*/
	}
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues(Interno)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		$.ajax({
			dataType: "json",
			type: "GET",
			url: "modulos/datos_productos.php",
			data: {"Requerimientos_Datos_Solicitud":Interno},
			success: function(data)
			{
				Locked = false;
				$("#requerimientos_modificar_fecha").jqxDateTimeInput("setDate", new Date(SetFormattedDate(data[0]["Fecha"])));
				$("#requerimientos_modificar_prioridad").jqxComboBox("selectItem", data[0]["Prioridad"]);
				$("#requerimientos_modificar_cliente_ID").jqxComboBox("selectItem", data[0]["ClienteID"]);
				$("#requerimientos_modificar_observaciones").val(data[0]["Observaciones"]);
				
				$("#requerimientos_modificar_items_grid").jqxGrid("clear");
				var len = data[0]["Productos"].length;
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"Codigo":data[0]["Productos"][i]["Codigo"],
						"Nombre":data[0]["Productos"][i]["Nombre"],
						"Unidad":data[0]["Productos"][i]["Unidad"],
						"Valor":data[0]["Productos"][i]["Valor"],
						"Existencia":ExistenciaArray[data[0]["Productos"][i]["Codigo"]],
						"Cantidad":data[0]["Productos"][i]["Cantidad"],
					}];
					$("#requerimientos_modificar_items_grid").jqxGrid("addrow", null, datarow, "first");
				}
				
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se cargaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	}
	
	function LoadParameters()
	{
		SolicitudSource.data = {"Requerimientos_Modificar":true};
		ClientesNominaSource.data = {"Clientes_Nomina":true};
		PrioridadSource.data = {"Requerimientos_Prioridad":true};
		ProductosSource.data = {"Requerimientos_Productos":true};
		
		var ClientesNominaDataAdapter = new $.jqx.dataAdapter(ClientesNominaSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#requerimientos_modificar_cliente_ID").jqxComboBox({source: records});
				$("#requerimientos_modificar_cliente").jqxComboBox({source: records});
			},
		});
		
		var ProductosDataAdapter = new $.jqx.dataAdapter(ProductosSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++) {
					UnidadArray[records[i]["Codigo"]] = records[i]["Unidad"];
					ExistenciaArray[records[i]["Codigo"]] = records[i]["Existencia"];
					ValorArray[records[i]["Codigo"]] = records[i]["Valor"];
				}
			},
			loadComplete: function (records)
			{
				$("#requerimientos_modificar_codigo").jqxComboBox({source: records});
				$("#requerimientos_modificar_producto").jqxComboBox({source: records});
			},
		});
		
		var SolicitudDataAdapter = new $.jqx.dataAdapter(SolicitudSource);
		$("#requerimientos_modificar_interno").jqxComboBox({source: SolicitudDataAdapter});
		
		var PrioridadDataAdapter = new $.jqx.dataAdapter(PrioridadSource);
		$("#requerimientos_modificar_prioridad").jqxComboBox({source: PrioridadDataAdapter});
		
		$("#requerimientos_modificar_existencia").val("0");
		$("#requerimientos_modificar_cantidad").val("0");
	}
	
	var SolicitudSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Interno", type: "string"}
		],
		url: "modulos/datos.php",
	};
	
	var ClientesNominaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
	};
	
	var PrioridadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Prioridad', type: 'string'},
		],
		url: "modulos/parametros.php",
	};
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'Unidad', type: 'string'},
			{ name: 'Peso', type: 'decimal'},
			{ name: 'Existencia', type: 'decimal'},
			{ name: 'Valor', type: 'decimal'},
		],
		url: "modulos/datos_productos.php",
	};
	
	$("#requerimientos_modificar_interno").jqxComboBox({
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar...',
		selectedIndex: -1,
		displayMember: 'Interno',
		valueMember: 'Interno'
	});
	$("#requerimientos_modificar_interno").on("change", function (event)
	{
		if (event.args)
		{
			if (Interno == event.args.item.value)
				return;
			
			Interno = event.args.item.value;
			
			clearTimeout(Timer);
			Timer = setTimeout(function()
			{
				if (ClickOK == true)
				{
					EnableDisableAll(false);
					ClickOK = false;
				}
				LoadValues(Interno);
			},500);
		}
		else
		{
			var item_value = $("#requerimientos_modificar_interno").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				//$("#requerimientos_modificar_interno").jqxComboBox("clearSelection");
				ClearDocument();
			}
			else
			{
				var value = $("#requerimientos_modificar_interno").val();
				var item = $("#requerimientos_modificar_interno").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					//$("#requerimientos_modificar_interno").jqxComboBox('clearSelection');
					ClearDocument();
				}
				else
					$("#requerimientos_modificar_interno").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#requerimientos_modificar_interno").on("bindingComplete", function (event) {
		if (Interno != "")
			$("#requerimientos_modificar_interno").jqxComboBox("selectItem", Interno);
	});
	
	$("#requerimientos_modificar_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#requerimientos_modificar_prioridad").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Prioridad',
		valueMember: 'Prioridad'
	});
	$("#requerimientos_modificar_prioridad").on("change", function (event)
	{
		if (event.args)
		{
			if (Prioridad == event.args.item.value)
				return;
			
			Prioridad = event.args.item.value;
		}
		else
		{
			Prioridad = "";
			$("#requerimientos_modificar_prioridad").jqxComboBox("clearSelection");
		}
	});
	$("#requerimientos_modificar_prioridad").on("bindingComplete", function (event)
	{
		if (Prioridad != "")
			$("#requerimientos_modificar_prioridad").jqxComboBox("selectItem", Prioridad);
	});
	
	$("#requerimientos_modificar_cliente_ID").jqxComboBox(
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
	$("#requerimientos_modificar_cliente_ID").on('change', function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			if ($("#requerimientos_modificar_cliente").val() != event.args.item.value)
				$("#requerimientos_modificar_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_modificar_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#requerimientos_modificar_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_modificar_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_modificar_cliente_ID").val();
				var item = $("#requerimientos_modificar_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					ClienteID = "";
					$("#requerimientos_modificar_cliente_ID").jqxComboBox('clearSelection');
					$("#requerimientos_modificar_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#requerimientos_modificar_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#requerimientos_modificar_cliente_ID").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#requerimientos_modificar_cliente_ID").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#requerimientos_modificar_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 260,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Empleado',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#requerimientos_modificar_cliente").on('change', function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			if ($("#requerimientos_modificar_cliente_ID").val() != event.args.item.value)
				$("#requerimientos_modificar_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_modificar_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#requerimientos_modificar_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_modificar_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_modificar_cliente").val();
				
				var item = $("#requerimientos_modificar_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#requerimientos_modificar_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				ClienteID = "";
				$("#requerimientos_modificar_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_modificar_cliente").jqxComboBox('clearSelection');
			}
		}
	});
	$("#requerimientos_modificar_cliente").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#requerimientos_modificar_cliente").jqxComboBox("selectItem", ClienteID);
	});
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Add_Row()
	{
		if (Locked == true)
			return;
			
		Locked = true;
		
		var Producto = $("#requerimientos_modificar_producto").jqxComboBox("getSelectedItem");
		var Cantidad = $("#requerimientos_modificar_cantidad").val();
		
		if (!Producto) {
			Alerts_Box("Favor Ingresar un Producto!", 3);
			WaitClick_Combobox("requerimientos_modificar_producto");
			Locked = false;
			return;
		}
		
		if (Cantidad < 1) {
			Alerts_Box("Favor Ingresar una cantidad mayor a 0!", 3);
			WaitClick_NumberInput("requerimientos_modificar_cantidad");
			Locked = false;
			return;
		}
		
		var Unidad = UnidadArray[Producto.value];
		var Existencia = ExistenciaArray[Producto.value];
		var Valor = ValorArray[Producto.value];
		
		var datainfo = $("#requerimientos_modificar_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#requerimientos_modificar_items_grid").jqxGrid("getrowdata", i);
			if (currentRow.Codigo == Producto.value)
			{
				var total = Cantidad + currentRow.Cantidad;
				var datarow = [{
					"Codigo":currentRow.Codigo,
					"Nombre":currentRow.Nombre,
					"Unidad":currentRow.Unidad,
					"Existencia":currentRow.Existencia,
					"Valor":currentRow.Valor,
					"Cantidad":total,
				}];
				var id = $("#requerimientos_modificar_items_grid").jqxGrid("getrowid", i);
				$("#requerimientos_modificar_items_grid").jqxGrid("deleterow", id);
				$("#requerimientos_modificar_items_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#requerimientos_modificar_codigo").jqxComboBox("clearSelection");
				$("#requerimientos_modificar_producto").jqxComboBox("clearSelection");
				$("#requerimientos_modificar_existencia").val("0");
				$("#requerimientos_modificar_cantidad").val("0");
				Locked = false;
				return;
			}
		}
		
		var datarow = [{
			"Codigo":Producto.value,
			"Nombre":Producto.label,
			"Unidad":Unidad,
			"Valor":Valor,
			"Existencia":Existencia,
			"Cantidad":Cantidad,
		}];
		
		$("#requerimientos_modificar_items_grid").jqxGrid("addrow", count, datarow);
		$("#requerimientos_modificar_codigo").jqxComboBox("clearSelection");
		$("#requerimientos_modificar_producto").jqxComboBox("clearSelection");
		$("#requerimientos_modificar_existencia").val("0");
		$("#requerimientos_modificar_cantidad").val("0");
		Locked = false;
	};
	
	$("#requerimientos_modificar_codigo").jqxComboBox(
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
	$("#requerimientos_modificar_codigo").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_modificar_producto").val() != event.args.item.value)
				$("#requerimientos_modificar_producto").jqxComboBox('selectItem', event.args.item.value);
			
			var Existencia = ExistenciaArray[event.args.item.value];
			$("#requerimientos_modificar_existencia").val(Existencia);
		}
		else
		{
			var item_value = $("#requerimientos_modificar_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#requerimientos_modificar_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_modificar_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_modificar_codigo").val();
				var item = $("#requerimientos_modificar_codigo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#requerimientos_modificar_codigo").jqxComboBox('clearSelection');
					$("#requerimientos_modificar_producto").jqxComboBox('clearSelection');
				}
				else
					$("#requerimientos_modificar_codigo").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#requerimientos_modificar_producto").jqxComboBox(
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
	$("#requerimientos_modificar_producto").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_modificar_codigo").val() != event.args.item.value)
				$("#requerimientos_modificar_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_modificar_producto").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#requerimientos_modificar_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_modificar_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_modificar_producto").val();
				
				var item = $("#requerimientos_modificar_producto").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#requerimientos_modificar_producto").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#requerimientos_modificar_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_modificar_producto").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#requerimientos_modificar_existencia").jqxNumberInput({
		theme: mytheme,
		width: 106,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:15,
		disabled: true
	});
	
	$("#requerimientos_modificar_cantidad").jqxNumberInput({
		theme: mytheme,
		width: 80,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:6,
	});
	
	$("#requerimientos_modificar_add").jqxButton({
		theme: mytheme,
		width: 70,
		height: 25,
		template: "success"
	});
	$("#requerimientos_modificar_add").on('click', function()
	{
		Add_Row();
	});
	
	$("#requerimientos_modificar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 700,
		height: 300,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				width: "5%",
				height: 20,
				pinned: true,
				filterable: false,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#requerimientos_modificar_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#requerimientos_modificar_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#requerimientos_modificar_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#requerimientos_modificar_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '15%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '40%', height: 20 },
			{ text: 'Und', datafield: 'Unidad', editable: false, width: '10%', height: 20 },
			{
				text: '',
				datafield: 'Valor',
				width: 110,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
			{
				text: 'Existencia',
				datafield: 'Existencia',
				editable: false,
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '15%',
				height: 20,
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
		]
	});
	$("#requerimientos_modificar_items_grid").jqxGrid('hidecolumn', 'Valor');
	$("#requerimientos_modificar_items_grid").jqxGrid('localizestrings', localizationobj);
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function SaveData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Solicitud = $("#requerimientos_modificar_interno").jqxComboBox("getSelectedItem");
		var Cliente = $("#requerimientos_modificar_cliente").jqxComboBox("getSelectedItem");
		var Fecha = GetFormattedDate($("#requerimientos_modificar_fecha").jqxDateTimeInput("getDate"));
		var prioridad = $("#requerimientos_modificar_prioridad").jqxComboBox("getSelectedItem");
		
		if (!Solicitud) 
		{
			Alerts_Box("Favor Seleccionar una Solicitud!", 3);
			WaitClick_Combobox("requerimientos_modificar_interno");
			Locked = false;
			return;
		}
		
		if (!prioridad) 
		{
			Alerts_Box("Favor Seleccionar una Prioridad!", 3);
			WaitClick_Combobox("requerimientos_modificar_cliente");
			Locked = false;
			return;
		}
		
		if (!Cliente) 
		{
			Alerts_Box("Favor Seleccionar un Empleado!", 3);
			WaitClick_Combobox("requerimientos_modificar_cliente");
			Locked = false;
			return;
		}
		
		var FinalArray = new Array();
		var datainfo = $("#requerimientos_modificar_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#requerimientos_modificar_items_grid").jqxGrid("getrowdata", i);
			
			TmpArray["Codigo"] = currentRow.Codigo;
			TmpArray["Nombre"] = currentRow.Nombre;
			TmpArray["Valor"] = parseFloat(currentRow.Valor);
			TmpArray["Cantidad"] = parseFloat(currentRow.Cantidad);
			
			if (i == 0)
			{
				TmpArray["Interno"] = Solicitud.value;
				TmpArray["ClienteID"] = Cliente.value;
				TmpArray["Fecha"] = Fecha;
				TmpArray["Prioridad"] = prioridad.value;
				TmpArray["Observaciones"] = $("#requerimientos_modificar_observaciones").val();
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
			data: {"Requerimientos_Modificar":FinalArray},
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						EnableDisableAll(true);
						LoadParameters();
						Alerts_Box("Datos Modificados con Exito!", 2);
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
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
	
	$("#requerimientos_modificar_guardar").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "info"
	});
	$("#requerimientos_modificar_guardar").on('click', function()
	{
		if (!Admin && !Guardar)
			return;
		SaveData();
	});
	
	$("#requerimientos_modificar_nuevo").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "success"
	});
	$("#requerimientos_modificar_nuevo").on('click', function()
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
				<div id="requerimientos_modificar_interno"></div>
			</td>
			<td>
				Fecha
			</td>
			<td>
				<div id="requerimientos_modificar_fecha"></div>
			</td>
			<td>
				Prioridad
			</td>
			<td>
				<div id="requerimientos_modificar_prioridad"></div>
			</td>
		</tr>
		<tr>
			<td>
				Empleado
			</td>
			<td colspan="3">
				<div id="requerimientos_modificar_cliente"></div>
			</td>
			<td>
				Empleado ID
			</td>
			<td>
				<div id="requerimientos_modificar_cliente_ID"></div>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="1" style="border-left: 1px solid #A4BED4; border-top: 1px solid #A4BED4; border-right: 1px solid #A4BED4; text-align:center; width:602px;">
		<tr>
			<td>
				<div id="requerimientos_modificar_codigo"></div>
			</td>
			<td>
				<div id="requerimientos_modificar_producto"></div>
			</td>
			<td>
				<div id="requerimientos_modificar_existencia"></div>
			</td>
			<td>
				<div id="requerimientos_modificar_cantidad"></div>
			</td>
			<td>
				<input type="button" id="requerimientos_modificar_add" value="Añadir">
			</td>
		</tr>
	</table>
	<div id="requerimientos_modificar_items_grid"></div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<table cellpadding="2" cellspacing="1">
		<tr>
			<td>
				Observaciones
			</td>
			<td rowspan="4" style="width: 330px;">
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td rowspan="3">
				<textarea rows="5" cols="35" id="requerimientos_modificar_observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td>
				<input type="button" id="requerimientos_modificar_guardar" value="Guardar">
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="requerimientos_modificar_nuevo" value="Nuevo">
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
	</table>
</div>