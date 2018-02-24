<?php
session_start();
$Cartilla = isset($_POST["Cartilla"]) ? $_POST["Cartilla"]:"";
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
	var Locked = false;
	var Orden_Produccion = "";
	var ClienteID = "";
	var FiguraID = "";
	var Characters = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P"];
	var kArray = new Array();
	var ClientesArray = new Array();
	var DimensionesArray = new Array();
	var CircularArray = new Array();
	var VueltasArray = new Array();
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
			{id:"figuracion_crear_interno<?php echo $NumOfID; ?>", type:""},
			{id:"figuracion_crear_orden_produccion<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			//-- part 2
			{id:"figuracion_crear_figura<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_codigo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_producto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_cantidad<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_crear_ubicacion<?php echo $NumOfID; ?>", type:""},
			//-- part 3
			{id:"figuracion_crear_dimensiones<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_crear_items_grid<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_crear_peso<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
		];
		
		EnableDisableJSON = [
			//-- part 1
			{id:"figuracion_crear_orden_produccion<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			//-- part 2
			{id:"figuracion_crear_figura<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_codigo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_producto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_crear_cantidad<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_crear_ubicacion<?php echo $NumOfID; ?>", type:""},
			//-- part 3
			{id:"figuracion_crear_dimensiones<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_crear_items_grid<?php echo $NumOfID; ?>", type:"jqxGrid"},
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
			$("#figuracion_crear_guardar<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
			$("#figuracion_crear_nuevo<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#figuracion_crear_imprimir<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
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
				$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox({source: records});
				$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox({source: records});
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
				$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox({source: records});
				$("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox({source: records});
			},
		});
		
		var FigurasDataAdapter = new $.jqx.dataAdapter(FigurasSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
				{
					DimensionesArray[records[i]["Figura"]] = records[i]["Dimensiones"];
					ImagenArray[records[i]["Figura"]] = records[i]["Imagen"];
					CircularArray[records[i]["Figura"]] = records[i]["Circular"];
					VueltasArray[records[i]["Figura"]] = records[i]["Vueltas"];
				}
			},
			loadComplete: function (records)
			{
				$("#figuracion_crear_figura<?php echo $NumOfID; ?>").jqxComboBox({source: records});
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
				$("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").jqxComboBox({source: records});
			},
		});
		
		$("#figuracion_crear_cantidad<?php echo $NumOfID; ?>").val("0");
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
			{ name: "Circular", type: "bool"},
			{ name: "Vueltas", type: "bool"},
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
		
	$("#figuracion_crear_interno<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
		disabled: true
	});
	
	$("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").jqxComboBox({
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
	$("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if (Orden_Produccion == event.args.item.value)
				return;
			
			Orden_Produccion = event.args.item.value;
			$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox("selectItem", ClientesArray[event.args.item.value]);
		}
		else
		{
			Orden_Produccion = "";
			//ClienteID = "";
			$("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			//$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			//$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
		}
	});
	$("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").bind("bindingComplete", function (event) {
		if (Orden_Produccion != "")
			$("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").jqxComboBox('selectItem', Orden_Produccion);
	});
	
	$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox(
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
	$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			var item = $("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			if (item)
			{
				if (ClientesArray[item.value] != ClienteID)
					$("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
			
			if ($("#figuracion_crear_cliente<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").val();
				var item = $("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					ClienteID = "";
					$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
					$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				}
				else
					$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox(
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
	$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			if ($("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClienteID = "";
				$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#figuracion_crear_cliente<?php echo $NumOfID; ?>").val();
				
				var item = $("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				ClienteID = "";
				$("#figuracion_crear_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
		}
	});
	$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").on("bindingComplete", function (event)
	{
		if (ClienteID != "")
			$("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox("selectItem", ClienteID);
	});
	
	$("#figuracion_crear_figura<?php echo $NumOfID; ?>").jqxComboBox(
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
		renderer: function (index, label, value)
		{
			var imgurl = "images/" + ImagenArray[value];
			var img = "<img width=\"70\" height=\"40\" src=\""+imgurl+"\"/>";
			var table = "<table><tr><td>"+img+"</td></tr><tr><td>"+value+"</td></tr></table>";
			return table;
		}
	});
	$("#figuracion_crear_figura<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if (FiguraID == event.args.item.value)
				return;
			
			FiguraID = event.args.item.value;
			
			if (ImagenArray[event.args.item.value] != "")
			{
				var img = "<img id=\"figuracion_crear_imagen2<?php echo $NumOfID; ?>\" style=\"margin: auto;\" src=\"images/"+ImagenArray[event.args.item.value]+"\"/>";
				$("#figuracion_crear_imagen<?php echo $NumOfID; ?>").html(img);
			}
			else
				$("#figuracion_crear_imagen<?php echo $NumOfID; ?>").html("");
			
			var datarow = new Array();
			$("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("clear");
			for (var i = 0; i < DimensionesArray[event.args.item.value]; i++)
			{
				var TmpData = {
					"Punto":Characters[i],
					"Dimension":"0.00",
				};
				datarow[i] = TmpData;
			}
			datarow.reverse();
			$("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
		}
		else
		{
			FiguraID = "";
			$("#figuracion_crear_imagen<?php echo $NumOfID; ?>").html("");
			$("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("clear");
		}
	});
	$("#figuracion_crear_figura<?php echo $NumOfID; ?>").on("bindingComplete", function (event)
	{
		if (FiguraID != "")
			$("#figuracion_crear_figura<?php echo $NumOfID; ?>").jqxComboBox("selectItem", FiguraID);
	});
	
	$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox(
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
	$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#figuracion_crear_producto<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
			
		}
		else
		{
			var item_value = $("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#figuracion_crear_codigo<?php echo $NumOfID; ?>").val();
				var item = $("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
					$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				}
				else
					$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Figurado',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#figuracion_crear_producto<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#figuracion_crear_codigo<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#figuracion_crear_producto<?php echo $NumOfID; ?>").val();
				
				var item = $("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
				$("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#figuracion_crear_cantidad<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999999999999.99
	});
	
	$("#figuracion_crear_ubicacion<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		width: 220,
		height: 20,
	});
	
	$("#figuracion_crear_add<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 90,
		height: 25,
		template: "success"
	});
	$("#figuracion_crear_add<?php echo $NumOfID; ?>").on("click", function()
	{
		var Figura = $("#figuracion_crear_figura<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Producto = $("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Cantidad = $("#figuracion_crear_cantidad<?php echo $NumOfID; ?>").val();
		var Ubicacion = $("#figuracion_crear_ubicacion<?php echo $NumOfID; ?>").val();
		
		if (!Figura) 
		{
			Alerts_Box("Favor Seleccionar una Figura.", 3);
			WaitClick_Combobox("figuracion_crear_figura<?php echo $NumOfID; ?>");
			return;
		}
		
		if (!Producto) 
		{
			Alerts_Box("Favor Seleccionar un Figurado.", 3);
			WaitClick_Combobox("figuracion_crear_producto<?php echo $NumOfID; ?>");
			return;
		}
		
		if (Cantidad < 1) 
		{
			Alerts_Box("Favor Ingresar una Cantidad Mayor a 0.", 3);
			WaitClick_NumberInput("figuracion_crear_cantidad<?php echo $NumOfID; ?>");
			return;
		}
		
		var FinalArray = new Array();
		var Dimensiones = new Array();
		var datainfo = $("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			
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
		$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("addrow", null, FinalArray, "first");
		
		FiguraID = "";
		$("#figuracion_crear_figura<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_crear_producto<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_crear_cantidad<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_crear_ubicacion<?php echo $NumOfID; ?>").val("");
		$("#figuracion_crear_imagen<?php echo $NumOfID; ?>").html("");
		$("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("clear");
	});
	
	var DimensionesSource =
	{
		datafields:
		[
			{ name: 'Punto', type: 'string' },
			{ name: 'Dimension', type: 'decimal' },
		],
		updaterow: function (rowid, rowdata, commit)
		{
			commit(true);
			Calcular(false);
		},
	};
	var DimensionesDataAdapter = new $.jqx.dataAdapter(DimensionesSource);
	
	
	$("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid(
	{
		theme: mytheme,
		width: 120,
		height: 200,
		source: DimensionesDataAdapter,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{ text: 'Punto', datafield: 'Punto', editable: false, width: '40%', height: 20 },
			{
				text: 'Dimension',
				datafield: 'Dimension',
				editable: true,
				width: '60%',
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
	$("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("localizestrings", localizationobj);
	
	var GridSource =
	{
		datafields:
		[
			{ name: 'Figura', type: 'string' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'K', type: 'decimal' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Total_Peso', type: 'decimal' },
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
	
	$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid(
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
					var selectedrowindex = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('getselectedrowindex');
					var rowscount = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('getrowid', selectedrowindex);
						$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Figura', datafield: 'Figura', editable: false, width: '16%', height: 20 },
			{ text: '', datafield: 'Codigo', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'K', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'Dimensiones', editable: false, width: '0%', height: 20 },
			{ text: 'Figurado', datafield: 'Nombre', editable: false, width: '39.5%', height: 20 },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '20%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{ text: 'Ubicacion', datafield: 'Ubicacion', editable: false, width: '20%', height: 20 },
		]
	});
	$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").on("rowselect", function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		
		var Figura = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", rowBoundIndex, "Figura");
		$("#figuracion_crear_figura<?php echo $NumOfID; ?>").jqxComboBox("selectItem", Figura);
		
		var Codigo = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", rowBoundIndex, "Codigo");
		var Cantidad = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", rowBoundIndex, "Cantidad");
		var Ubicacion = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", rowBoundIndex, "Ubicacion");
		var Dimensiones = JSON.parse($("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", rowBoundIndex, "Dimensiones"));
		var datarow = new Array();
		$("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("clear");
		for (var i = 0; i < Dimensiones.length; i++)
		{
			var TmpData = {
				"Punto":Dimensiones[i]["Punto"],
				"Dimension":parseFloat(Dimensiones[i]["Dimension"]),
			};
			datarow[i] = TmpData;
		}
		datarow.reverse();
		$("#figuracion_crear_dimensiones<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
		$("#figuracion_crear_codigo<?php echo $NumOfID; ?>").jqxComboBox("selectItem", Codigo);
		$("#figuracion_crear_cantidad<?php echo $NumOfID; ?>").val(Cantidad);
		$("#figuracion_crear_ubicacion<?php echo $NumOfID; ?>").val(Ubicacion);
	});
	$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Codigo");
	$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "K");
	$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Dimensiones");
	$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("localizestrings", localizationobj);
	
	$("#figuracion_crear_guardar<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "info"
	});
	$("#figuracion_crear_guardar<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Guardar)
			return;
		SaveData();
	});
	
	$("#figuracion_crear_nuevo<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "success"
	});
	$("#figuracion_crear_nuevo<?php echo $NumOfID; ?>").on("click", function()
	{
		ClearDocument();
	});
	
	$("#figuracion_crear_imprimir<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "warning"
	});
	$("#figuracion_crear_imprimir<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		window.open("imprimir/cartilla_figuracion.php?Interno="+$("#figuracion_crear_interno<?php echo $NumOfID; ?>").val()+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_crear_peso<?php echo $NumOfID; ?>").jqxNumberInput({
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
		var datainfo = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			
			var K = parseFloat(currentRow.K);
			var C = parseFloat(currentRow.Cantidad);
			var LBase = 0;
			var LSum = 0;
			var L = 0;
			
			var Dimensiones = JSON.parse(currentRow.Dimensiones);
			for (var a = 0; a < Dimensiones.length; a++)
			{
				if (VueltasArray[currentRow.Figura] == true && Dimensiones.length - a == 1)
				{
					LBase = LBase * parseFloat(Dimensiones[a]["Dimension"]);
				}
				else if (CircularArray[currentRow.Figura] == true && Dimensiones[a]["Punto"] == "A")
				{
					LBase = Math.PI * parseFloat(Dimensiones[a]["Dimension"]);
				}
				else
					LSum += parseFloat(Dimensiones[a]["Dimension"]);
			}
			
			L = LBase + LSum;
			Total += Math.round(((L * K) * C) * 100) / 100;
			//Total += (L * K) * C;
		}
		$("#figuracion_crear_peso<?php echo $NumOfID; ?>").val(Total);
		$("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("clearSelection");
	}
	
	function SaveData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var OP = $("#figuracion_crear_orden_produccion<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Cliente = $("#figuracion_crear_cliente<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Peso = $("#figuracion_crear_peso<?php echo $NumOfID; ?>").val();
		
		if (!OP) 
			OP = "";
		else
			OP = OP.value;
		
		if (!Cliente) 
		{
			Alerts_Box("Favor Seleccionar un Cliente!", 3);
			WaitClick_Combobox("figuracion_crear_cliente<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if (Peso < 0.01) 
		{
			Alerts_Box("No se puede crear una Cartilla con Peso = 0", 3);
			WaitClick_NumberInput("figuracion_crear_peso<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		var FinalArray = new Array();
		var datainfo = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_crear_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			
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
						LoadParameters();
						Alerts_Box("Datos Guardados con Exito!", 2);
						$("#figuracion_crear_interno<?php echo $NumOfID; ?>").val(data[0]["MESSAGE"]);
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
	
	if (!Admin && !Guardar)
	{
		$("#figuracion_crear_guardar<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		$("#figuracion_crear_nuevo<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Imprimir)
	{
		$("#figuracion_crear_imprimir<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
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
				<input type="text" id="figuracion_crear_interno<?php echo $NumOfID; ?>"/>
			</td>
			<td>
				<div id="figuracion_crear_orden_produccion<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="2">
				<div id="figuracion_crear_cliente<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_crear_cliente_ID<?php echo $NumOfID; ?>"></div>
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
				Figurado
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
				<div id="figuracion_crear_figura<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_crear_codigo<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="2">
				<div id="figuracion_crear_producto<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_crear_cantidad<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<input type="text" id="figuracion_crear_ubicacion<?php echo $NumOfID; ?>"/>
			</td>
			<td>
				<input type="button" id="figuracion_crear_add<?php echo $NumOfID; ?>" value="Añadir"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Imagen
			</td>
			<td style="width:122px;">
				Dimensiones (m)
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
				<div id="figuracion_crear_imagen<?php echo $NumOfID; ?>" style="width:230px; height:200px; border:1px solid #d3d3d3; text-align:center; background:#FFF;"></div>
			</td>
			<td>
				<div id="figuracion_crear_dimensiones<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="4" rowspan="6" style="vertical-align:-webkit-baseline-middle;">
				<div id="figuracion_crear_items_grid<?php echo $NumOfID; ?>"></div>
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
					<div id="figuracion_crear_peso<?php echo $NumOfID; ?>"></div>
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
					<input type="button" id="figuracion_crear_guardar<?php echo $NumOfID; ?>" value="Guardar"/>
				</li>
				<li style="margin-left:10px;">
					<input type="button" id="figuracion_crear_imprimir<?php echo $NumOfID; ?>" value="Imprimir"/>
				</li>
				<li style="margin-left:10px;">
					<input type="button" id="figuracion_crear_nuevo<?php echo $NumOfID; ?>" value="Nuevo"/>
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