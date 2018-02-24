<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Prioridad = "Normal";
	var ClienteID = "";
	var UnidadArray = new Array();
	var ExistenciaArray = new Array();
	var ValorArray = new Array();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Requerimientos_Content");
	var Body = document.getElementById("Requerimientos_Crear");
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
				SystemMap("Crear Solicitud", true);
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
				if ($data[$i]["Modulo"] == "Requerimientos" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "true")
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
			{id:"requerimientos_crear_interno", type:""},
			{id:"requerimientos_crear_fecha", type:"jqxDateTimeInput"},
			{id:"requerimientos_crear_cliente", type:"jqxComboBox"},
			{id:"requerimientos_crear_cliente_ID", type:"jqxComboBox"},
			//-- part 2
			{id:"requerimientos_crear_codigo", type:"jqxComboBox"},
			{id:"requerimientos_crear_producto", type:"jqxComboBox"},
			{id:"requerimientos_crear_existencia", type:"jqxNumberInput"},
			{id:"requerimientos_crear_cantidad", type:"jqxNumberInput"},
			{id:"requerimientos_crear_items_grid", type:"jqxGrid"},
			//-- part 3
			{id:"requerimientos_crear_observaciones", type:""},
		];
		
		EnableDisableJSON = [
			//-- part 1
			{id:"requerimientos_crear_interno", type:""},
			{id:"requerimientos_crear_fecha", type:"jqxDateTimeInput"},
			{id:"requerimientos_crear_prioridad", type:"jqxComboBox"},
			{id:"requerimientos_crear_cliente", type:"jqxComboBox"},
			{id:"requerimientos_crear_cliente_ID", type:"jqxComboBox"},
			//-- part 2
			{id:"requerimientos_crear_codigo", type:"jqxComboBox"},
			{id:"requerimientos_crear_producto", type:"jqxComboBox"},
			{id:"requerimientos_crear_existencia", type:"jqxNumberInput"},
			{id:"requerimientos_crear_cantidad", type:"jqxNumberInput"},
			{id:"requerimientos_crear_add", type:"jqxButton"},
			//{id:"requerimientos_crear_items_grid", type:"jqxGrid"},
			//-- part 3
			{id:"requerimientos_crear_observaciones", type:""},
			{id:"requerimientos_crear_guardar", type:"jqxButton"},
			//{id:"requerimientos_crear_nuevo", type:"jqxButton"},
		];
	}
	
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Locked = false;
		Prioridad = "Normal";
		ClienteID = "";
		//---
		ClearAll();
		EnableDisableAll(false);
		
		if (!Admin && !Guardar)
		{
			$("#requerimientos_crear_guardar").jqxButton({ disabled: true });
			$("#requerimientos_crear_nuevo").jqxButton({ disabled: true });
		}
		
		/*if (!Admin && !Imprimir)
		{
			$("#requerimientos_crear_imprimir").jqxButton({ disabled: true });
		}*/
	}
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadParameters()
	{
		ClientesNominaSource.data = {"Clientes_Nomina":true};
		PrioridadSource.data = {"Requerimientos_Prioridad":true};
		ProductosSource.data = {"Requerimientos_Productos":true};
		
		var ClientesNominaDataAdapter = new $.jqx.dataAdapter(ClientesNominaSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#requerimientos_crear_cliente_ID").jqxComboBox({source: records});
				$("#requerimientos_crear_cliente").jqxComboBox({source: records});
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
				$("#requerimientos_crear_codigo").jqxComboBox({source: records});
				$("#requerimientos_crear_producto").jqxComboBox({source: records});
			},
		});
		
		var PrioridadDataAdapter = new $.jqx.dataAdapter(PrioridadSource);
		$("#requerimientos_crear_prioridad").jqxComboBox({source: PrioridadDataAdapter});
		
		$("#requerimientos_crear_existencia").val("0");
		$("#requerimientos_crear_cantidad").val("0");
	}
	
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
		
	$("#requerimientos_crear_interno").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
		disabled: true
	});
	
	$("#requerimientos_crear_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#requerimientos_crear_fecha").jqxDateTimeInput('setDate', new Date(currenttime));
	
	$("#requerimientos_crear_prioridad").jqxComboBox(
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
	$("#requerimientos_crear_prioridad").on("change", function (event)
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
			$("#requerimientos_crear_prioridad").jqxComboBox("clearSelection");
		}
	});
	$("#requerimientos_crear_prioridad").on("bindingComplete", function (event)
	{
		if (Prioridad != "")
			$("#requerimientos_crear_prioridad").jqxComboBox("selectItem", Prioridad);
	});
	
	$("#requerimientos_crear_cliente_ID").jqxComboBox(
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
	$("#requerimientos_crear_cliente_ID").on('change', function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			if ($("#requerimientos_crear_cliente").val() != event.args.item.value)
				$("#requerimientos_crear_cliente").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_crear_cliente_ID").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#requerimientos_crear_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_crear_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_crear_cliente_ID").val();
				var item = $("#requerimientos_crear_cliente_ID").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					ClienteID = "";
					$("#requerimientos_crear_cliente_ID").jqxComboBox('clearSelection');
					$("#requerimientos_crear_cliente").jqxComboBox('clearSelection');
				}
				else
					$("#requerimientos_crear_cliente_ID").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#requerimientos_crear_cliente_ID").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#requerimientos_crear_cliente_ID").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#requerimientos_crear_cliente").jqxComboBox(
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
	$("#requerimientos_crear_cliente").on('change', function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			if ($("#requerimientos_crear_cliente_ID").val() != event.args.item.value)
				$("#requerimientos_crear_cliente_ID").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_crear_cliente").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#requerimientos_crear_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_crear_cliente").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_crear_cliente").val();
				
				var item = $("#requerimientos_crear_cliente").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#requerimientos_crear_cliente").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				ClienteID = "";
				$("#requerimientos_crear_cliente_ID").jqxComboBox('clearSelection');
				$("#requerimientos_crear_cliente").jqxComboBox('clearSelection');
			}
		}
	});
	$("#requerimientos_crear_cliente").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#requerimientos_crear_cliente").jqxComboBox("selectItem", ClienteID);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Add_Row()
	{
		if (Locked == true)
			return;
			
		Locked = true;
		
		var Producto = $("#requerimientos_crear_producto").jqxComboBox("getSelectedItem");
		var Cantidad = $("#requerimientos_crear_cantidad").val();
		
		if (!Producto) {
			Alerts_Box("Favor Ingresar un Producto!", 3);
			WaitClick_Combobox("requerimientos_crear_producto");
			Locked = false;
			return;
		}
		
		if (Cantidad < 1) {
			Alerts_Box("Favor Ingresar una cantidad mayor a 0!", 3);
			WaitClick_NumberInput("requerimientos_crear_cantidad");
			Locked = false;
			return;
		}
		
		var Unidad = UnidadArray[Producto.value];
		var Existencia = ExistenciaArray[Producto.value];
		var Valor = ValorArray[Producto.value];
		
		var datainfo = $("#requerimientos_crear_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#requerimientos_crear_items_grid").jqxGrid("getrowdata", i);
			if (currentRow.Codigo == Producto.value)
			{
				var total = Cantidad + currentRow.Cantidad;
				var datarow = [{
					"Codigo":currentRow.Codigo,
					"Nombre":currentRow.Nombre,
					"Unidad":currentRow.Unidad,
					"Valor":currentRow.Valor,
					"Existencia":currentRow.Existencia,
					"Cantidad":total,
				}];
				var id = $("#requerimientos_crear_items_grid").jqxGrid("getrowid", i);
				$("#requerimientos_crear_items_grid").jqxGrid("deleterow", id);
				$("#requerimientos_crear_items_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#requerimientos_crear_codigo").jqxComboBox("clearSelection");
				$("#requerimientos_crear_producto").jqxComboBox("clearSelection");
				$("#requerimientos_crear_existencia").val("0");
				$("#requerimientos_crear_cantidad").val("0");
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
		
		$("#requerimientos_crear_items_grid").jqxGrid("addrow", count, datarow);
		$("#requerimientos_crear_codigo").jqxComboBox("clearSelection");
		$("#requerimientos_crear_producto").jqxComboBox("clearSelection");
		$("#requerimientos_crear_existencia").val("0");
		$("#requerimientos_crear_cantidad").val("0");
		Locked = false;
	};
	
	$("#requerimientos_crear_codigo").jqxComboBox(
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
	$("#requerimientos_crear_codigo").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_crear_producto").val() != event.args.item.value)
				$("#requerimientos_crear_producto").jqxComboBox('selectItem', event.args.item.value);
			
			var Existencia = ExistenciaArray[event.args.item.value];
			$("#requerimientos_crear_existencia").val(Existencia);
		}
		else
		{
			var item_value = $("#requerimientos_crear_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#requerimientos_crear_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_crear_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_crear_codigo").val();
				var item = $("#requerimientos_crear_codigo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#requerimientos_crear_codigo").jqxComboBox('clearSelection');
					$("#requerimientos_crear_producto").jqxComboBox('clearSelection');
				}
				else
					$("#requerimientos_crear_codigo").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#requerimientos_crear_producto").jqxComboBox(
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
	$("#requerimientos_crear_producto").on('change', function (event)
	{
		if (event.args)
		{
			if ($("#requerimientos_crear_codigo").val() != event.args.item.value)
				$("#requerimientos_crear_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#requerimientos_crear_producto").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#requerimientos_crear_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_crear_producto").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#requerimientos_crear_producto").val();
				
				var item = $("#requerimientos_crear_producto").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#requerimientos_crear_producto").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#requerimientos_crear_codigo").jqxComboBox('clearSelection');
				$("#requerimientos_crear_producto").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#requerimientos_crear_existencia").jqxNumberInput({
		theme: mytheme,
		width: 106,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:15,
		disabled: true
	});
	
	$("#requerimientos_crear_cantidad").jqxNumberInput({
		theme: mytheme,
		width: 80,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:6,
	});
	
	$("#requerimientos_crear_add").jqxButton({
		theme: mytheme,
		width: 70,
		height: 25,
		template: "success"
	});
	$("#requerimientos_crear_add").on('click', function()
	{
		Add_Row();
	});
	
	$("#requerimientos_crear_items_grid").jqxGrid(
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
					var selectedrowindex = $("#requerimientos_crear_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#requerimientos_crear_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#requerimientos_crear_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#requerimientos_crear_items_grid").jqxGrid('deleterow', id);
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
	$("#requerimientos_crear_items_grid").jqxGrid('hidecolumn', 'Valor');
	$("#requerimientos_crear_items_grid").jqxGrid('localizestrings', localizationobj);
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 3 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function SaveData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Cliente = $("#requerimientos_crear_cliente").jqxComboBox("getSelectedItem");
		var Fecha = GetFormattedDate($("#requerimientos_crear_fecha").jqxDateTimeInput("getDate"));
		var prioridad = $("#requerimientos_crear_prioridad").jqxComboBox("getSelectedItem");
		
		if (!prioridad) 
		{
			Alerts_Box("Favor Seleccionar una Prioridad!", 3);
			WaitClick_Combobox("requerimientos_crear_prioridad");
			Locked = false;
			return;
		}
		
		if (!Cliente) 
		{
			Alerts_Box("Favor Seleccionar un Empleado!", 3);
			WaitClick_Combobox("requerimientos_crear_cliente");
			Locked = false;
			return;
		}
		
		var FinalArray = new Array();
		var datainfo = $("#requerimientos_crear_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#requerimientos_crear_items_grid").jqxGrid("getrowdata", i);
			
			TmpArray["Codigo"] = currentRow.Codigo;
			TmpArray["Nombre"] = currentRow.Nombre;
			TmpArray["Valor"] = parseFloat(currentRow.Valor);
			TmpArray["Cantidad"] = parseFloat(currentRow.Cantidad);
			
			if (i == 0)
			{
				TmpArray["ClienteID"] = Cliente.value;
				TmpArray["Fecha"] = Fecha;
				TmpArray["Prioridad"] = prioridad.value;
				TmpArray["Observaciones"] = $("#requerimientos_crear_observaciones").val();
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
			data: {"Requerimientos_Crear":FinalArray},
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
						LoadParameters();
						Alerts_Box("Datos Guardados con Exito!", 2);
						$("#requerimientos_crear_interno").val(data[0]["MESSAGE"]);
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
	
	$("#requerimientos_crear_guardar").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "info"
	});
	$("#requerimientos_crear_guardar").on('click', function()
	{
		if (!Admin && !Guardar)
			return;
		SaveData();
	});
	
	$("#requerimientos_crear_nuevo").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "success"
	});
	$("#requerimientos_crear_nuevo").on('click', function()
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
				Solicitud Nº
			</td>
			<td>
				<input type="text" id="requerimientos_crear_interno"/>
			</td>
			<td>
				Fecha
			</td>
			<td>
				<div id="requerimientos_crear_fecha"></div>
			</td>
			<td>
				Prioridad
			</td>
			<td>
				<div id="requerimientos_crear_prioridad"></div>
			</td>
		</tr>
		<tr>
			<td>
				Empleado
			</td>
			<td colspan="3">
				<div id="requerimientos_crear_cliente"></div>
			</td>
			<td>
				Empleado ID
			</td>
			<td>
				<div id="requerimientos_crear_cliente_ID"></div>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="1" style="border-left: 1px solid #A4BED4; border-top: 1px solid #A4BED4; border-right: 1px solid #A4BED4; text-align:center; width:602px;">
		<tr>
			<td>
				<div id="requerimientos_crear_codigo"></div>
			</td>
			<td>
				<div id="requerimientos_crear_producto"></div>
			</td>
			<td>
				<div id="requerimientos_crear_existencia"></div>
			</td>
			<td>
				<div id="requerimientos_crear_cantidad"></div>
			</td>
			<td>
				<input type="button" id="requerimientos_crear_add" value="Añadir">
			</td>
		</tr>
	</table>
	<div id="requerimientos_crear_items_grid"></div>
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
				<textarea rows="5" cols="35" id="requerimientos_crear_observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td>
				<input type="button" id="requerimientos_crear_guardar" value="Guardar">
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="requerimientos_crear_nuevo" value="Nuevo">
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
	</table>
</div>