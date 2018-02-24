<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ID_Interno = "";
	var ID_OrdenCompra = "";
	var Total_Original = 0;
	var ErrorSaldo = false;
	var cantidad_exedida = false;
	var ExistenciaCargada = false;
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Ventas_Content");
	var Body = document.getElementById("Ajustar_Content");
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
				SystemMap("Modificar Despacho", true);
				ReDefine();
				ClearDocument();
				// Actualizar Ordenes
				/*OrdenSource.data =  {"Ventas_Ajustar":true};
				var OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
				$("#ventas_ajustar_ord_compra").jqxComboBox({source: OrdenAdapter});*/
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
	var Modificar = false;
	var Guardar = false;
	var Imprimir = false;
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{	
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Despacho" && $data[$i]["Guardar"] == "true")
					{
			?>
						Guardar = true;
			<?php
					}

					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Despacho" && $data[$i]["Modificar"] == "true")
					{
			?>
						Modificar = true;
			<?php
					}
					
					if ($data[$i]["Modulo"] == "Ventas" && $data[$i]["SubModulo"] == "Despacho" && $data[$i]["Imprimir"] == "true")
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
	
	$("#ventas_ajustar_fecha_rem").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#ventas_ajustar_fecha_rem").jqxDateTimeInput('setDate', new Date(currenttime));
	// prepare the data
	/*var OrdenSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'OrdenCompra', type: 'string'},
			{ name: 'Interno', type: 'string'}
		],
		//data: {"Ventas_Ajustar":true},
		url: "modulos/datos.php",
	};*/
	/*var OrdenSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'OrdenCompra', type: 'string'},
			{ name: 'Interno', type: 'string'}
		],
		url: "modulos/datos.php",
		data: {
			"Ventas_Ajustar":true,
			"Limit":10
		},
	};
	var OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource,
	{
		formatData: function (data)
		{
			if ($("#ventas_ajustar_ord_compra").jqxComboBox('searchString') != undefined)
			{
				ID_Interno = "";
				ID_OrdenCompra = "";
				// Para el Parametro LIKE en la consulta.
				data.Contains = $("#ventas_ajustar_ord_compra").jqxComboBox('searchString');
				return data;
			}
			else//Copy N' Paste Fix
			{
				var string = $("#ventas_ajustar_ord_compra").val();
				if (string != "")
				{
					data.Contains = string;
					return data;
				}
			}
		}
	});*/
	
	function ReDefine()
	{
		ClearJSON = [
			//-1
			{id:"ventas_ajustar_ord_compra", type:""},
			{id:"ventas_ajustar_cliente", type:""},
			{id:"ventas_ajustar_cliente_ID", type:""},
			{id:"ventas_ajustar_direccion", type:""},
			{id:"ventas_ajustar_telefono", type:""},
			{id:"ventas_ajustar_remision", type:""},
			{id:"ventas_ajustar_interno", type:""},
			{id:"ventas_ajustar_e-mail", type:""},
			{id:"ventas_ajustar_contacto_p", type:""},
			{id:"ventas_ajustar_factura", type:""},
			//-2
			{id:"ventas_ajustar_codigo", type:"jqxComboBox"},
			{id:"ventas_ajustar_producto", type:"jqxComboBox"},
			{id:"ventas_ajustar_existencia", type:""},
			{id:"ventas_ajustar_cantidad", type:""},
			{id:"ventas_ajustar_listap", type:"jqxDropDownList"},
			{id:"ventas_ajustar_items_grid", type:"jqxGrid"},
			//-3
			{id:"ventas_ajustar_observaciones", type:""},
			{id:"ventas_ajustar_notas", type:""},
			{id:"ventas_ajustar_subtotal_total", type:""},
			{id:"ventas_ajustar_tipo_servicio", type:"jqxComboBox"},
			{id:"ventas_ajustar_tipo_servicio_precio", type:""},
			//{id:"ventas_ajustar_tipo_descuento", type:"jqxComboBox"},
			{id:"ventas_ajustar_tipo_descuento_precio", type:""},
			{id:"ventas_ajustar_iva_precio", type:""},
			{id:"ventas_ajustar_total_total", type:""},
			{id:"ventas_ajustar_conductor", type:"jqxComboBox"},
			{id:"ventas_ajustar_placa", type:"jqxComboBox"},
			{id:"ventas_ajustar_formaP", type:""},
			{id:"ventas_ajustar_vendedor", type:"jqxComboBox"},
			{id:"ventas_ajustar_pesokg", type:""},
			{id:"ventas_ajustar_dir_entrega", type:""},
			{id:"ventas_ajustar_sector", type:"jqxComboBox"},
			{id:"ventas_ajustar_ruta", type:""},
		];
		
		EnableDisableJSON = [
			//-1
			{id:"ventas_ajustar_fecha_rem", type:"jqxDateTimeInput"},
			{id:"ventas_ajustar_remision", type:""},
			{id:"ventas_ajustar_factura", type:""},
			//-2
			{id:"ventas_ajustar_codigo", type:"jqxComboBox"},
			{id:"ventas_ajustar_producto", type:"jqxComboBox"},
			{id:"ventas_ajustar_cantidad", type:"jqxNumberInput"},
			{id:"ventas_ajustar_listap", type:"jqxDropDownList"},
			{id:"ventas_ventas_ajustar_addrowbutton", type:"jqxButton"},
			{id:"ventas_ventas_ajustar_deleterowbutton", type:"jqxButton"},
			//-3
			{id:"ventas_ajustar_subtotal_total", type:"jqxNumberInput"},
			{id:"ventas_ajustar_tipo_servicio", type:"jqxComboBox"},
			{id:"ventas_ajustar_tipo_servicio_precio", type:"jqxNumberInput"},
			{id:"ventas_ajustar_tipo_descuento", type:"jqxComboBox"},
			{id:"ventas_ajustar_tipo_descuento_precio", type:"jqxNumberInput"},
			{id:"ventas_ajustar_iva_precio", type:"jqxNumberInput"},
			{id:"ventas_ajustar_total_total", type:"jqxNumberInput"},
			{id:"ventas_ajustar_conductor", type:"jqxComboBox"},
			{id:"ventas_ajustar_placa", type:"jqxComboBox"},
			{id:"ventas_ajustar_vendedor", type:"jqxComboBox"},
			{id:"ventas_ajustar_dir_entrega", type:""},
			{id:"ventas_ajustar_sector", type:"jqxComboBox"},
			{id:"ventas_ajustar_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		// Variables
		ID_Interno = "";
		ID_OrdenCompra = "";
		Total_Original = 0;
		ErrorSaldo = false;
		cantidad_exedida = false;
		ExistenciaCargada = false;
		Locked = false;
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		
		//--- Access
		if (!Admin && !Guardar)
		{
			$("#ventas_ajustar_guardar").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Modificar)
		{
			$("#ventas_ajustar_codigo").jqxComboBox({ disabled: true });
			$("#ventas_ajustar_producto").jqxComboBox({ disabled: true });
			$("#ventas_ventas_ajustar_addrowbutton").jqxButton({ disabled: true });
			$("#ventas_ventas_ajustar_deleterowbutton").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#ventas_ajustar_imprimir").jqxButton({ disabled: true });
		}
	}
	
	function CargarValores (ID_Interno)
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Direccion', type: 'string'},
				{ name: 'Email', type: 'string'},
				{ name: 'Telefono', type: 'string'},
				{ name: 'ContactoP', type: 'string'},
				{ name: 'Interno', type: 'string'},
				{ name: 'Remision', type: 'string' },
				{ name: 'Fecha', type: 'string'},
				{ name: 'Factura', type: 'string' },
				{ name: 'ClienteNombre', type: 'string' },
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Ruta', type: 'string'},
				{ name: 'DireccionEntrega', type: 'string'},
				{ name: 'FormaP', type: 'string' },
				{ name: 'VendedorID', type: 'string' },
				{ name: 'Conductor', type: 'string'},
				{ name: 'Placa', type: 'string'},
				{ name: 'TipoServicio', type: 'string' },
				{ name: 'TipoServicioValor', type: 'decimal' },
				{ name: 'TipoDescuento', type: 'string' },
				{ name: 'TipoDescuentoValor', type: 'decimal' },
				{ name: 'Total', type: 'decimal' },
				{ name: 'Notas', type: 'string' },
				{ name: 'Observaciones', type: 'string' },
				//--
				{ name: 'CodFab', type: 'string'},
				{ name: 'Nombre', type: 'string' },
				{ name: 'UndMed', type: 'string'},
				{ name: 'Peso', type: 'decimal' },
				{ name: 'Existencia', type: 'decimal' },
				{ name: 'Cantidad', type: 'decimal'},
				{ name: 'Dcto', type: 'decimal'},
				{ name: 'Unitario', type: 'decimal' },
				{ name: 'Produccion', type: 'bool' },
				{ name: 'FacturaExistencia', type: 'bool' },
			],
			data:{"Ventas_Ajustar":ID_Interno},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				var len = records.length;
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"CodFab":records[i]["CodFab"],
						"Nombre":records[i]["Nombre"],
						"UndMed":records[i]["UndMed"],
						"Peso":records[i]["Peso"],
						"Existencia":records[i]["Existencia"],
						"Cantidad":records[i]["Cantidad"],
						"Dcto":records[i]["Dcto"],
						"Unitario":records[i]["Unitario"],
						"Precio":records[i]["Unitario"],
						"Produccion":records[i]["Produccion"],
						"FacturaExistencia":records[i]["FacturaExistencia"]
					}];
					$("#ventas_ajustar_items_grid").jqxGrid("addrow", null, datarow, "first");
				}
				$("#ventas_ajustar_cliente").val(records[0]["ClienteNombre"]);
				$("#ventas_ajustar_cliente_ID").val(records[0]["ClienteID"]);
				$("#ventas_ajustar_direccion").val(records[0]["Direccion"]);
				$("#ventas_ajustar_e-mail").val(records[0]["Email"]);
				$("#ventas_ajustar_telefono").val(records[0]["Telefono"]);
				$("#ventas_ajustar_contacto_p").val(records[0]["ContactoP"]);
				$("#ventas_ajustar_interno").val(records[0]["Interno"]);
				$("#ventas_ajustar_remision").val(records[0]["Remision"]);
				$("#ventas_ajustar_fecha_rem").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha"])));
				$("#ventas_ajustar_factura").val(records[0]["Factura"]);
				$("#ventas_ajustar_sector").val(records[0]["Ruta"]);
				$("#ventas_ajustar_ruta").val(records[0]["Ruta"]);
				$("#ventas_ajustar_dir_entrega").val(records[0]["DireccionEntrega"]);
				$("#ventas_ajustar_formaP").val(records[0]["FormaP"]);
				Total_Original = records[0]["Total"];
				/*if (records[0]["FormaP"] == "Efectivo")
				{
					$("#ventas_ajustar_codigo").jqxComboBox({ disabled: true });
					$("#ventas_ajustar_producto").jqxComboBox({ disabled: true });
					$("#ventas_ajustar_cantidad").jqxNumberInput({ disabled: true });
					$("#ventas_ajustar_listap").jqxDropDownList({ disabled: true });
					$("#ventas_ventas_ajustar_addrowbutton").jqxButton({ disabled: true });
					$("#ventas_ventas_ajustar_deleterowbutton").jqxButton({ disabled: true });
					$("#ventas_ajustar_items_grid").jqxGrid('setcolumnproperty', 'Cantidad', 'editable', false);
					$("#ventas_ajustar_tipo_servicio").jqxComboBox({ disabled: true });
					$("#ventas_ajustar_tipo_servicio_precio").jqxNumberInput({ disabled: true });
				}
				else
				{
					$("#ventas_ajustar_codigo").jqxComboBox({ disabled: false });
					$("#ventas_ajustar_producto").jqxComboBox({ disabled: false });
					$("#ventas_ajustar_cantidad").jqxNumberInput({ disabled: false });
					$("#ventas_ajustar_listap").jqxDropDownList({ disabled: false });
					$("#ventas_ventas_ajustar_addrowbutton").jqxButton({ disabled: false });
					$("#ventas_ventas_ajustar_deleterowbutton").jqxButton({ disabled: false });
					$("#ventas_ajustar_items_grid").jqxGrid('setcolumnproperty', 'Cantidad', 'editable', true);
					$("#ventas_ajustar_tipo_servicio").jqxComboBox({ disabled: false });
					$("#ventas_ajustar_tipo_servicio_precio").jqxNumberInput({ disabled: false });
				}*/
				$("#ventas_ajustar_vendedor").jqxComboBox('selectItem', records[0]["VendedorID"]);
				$("#ventas_ajustar_conductor").jqxComboBox('selectItem', records[0]["Conductor"]);
				$("#ventas_ajustar_placa").jqxComboBox('selectItem', records[0]["Placa"]);
				
				$("#ventas_ajustar_vendedor").val(records[0]["VendedorID"]);
				//$("#ventas_ajustar_conductor").val(records[0]["Placa"]);
				//$("#ventas_ajustar_placa").val(records[0]["Placa"]);
				$("#ventas_ajustar_tipo_servicio").jqxComboBox('selectItem', records[0]["TipoServicio"]);
				$("#ventas_ajustar_tipo_servicio_precio").val(records[0]["TipoServicioValor"]);
				if (records[0]["TipoDescuento"] == "")
					$("#ventas_ajustar_tipo_descuento").jqxComboBox('clearSelection');
				else
					$("#ventas_ajustar_tipo_descuento").jqxComboBox('selectItem', records[0]["TipoDescuento"]);
				
				$("#ventas_ajustar_tipo_descuento_precio").val(records[0]["TipoDescuentoValor"]);
				
				$("#ventas_ajustar_observaciones").val(records[0]["Observaciones"]);
				$("#ventas_ajustar_notas").val(records[0]["Notas"]);
				Calcular();
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	};
	
	$('#Ventas_Ajustar_HideButton').click(function() {
		$("#Ventas_Ajustar_Content_to_Hide").toggle();
	});
	//------------------------------------------- KEY JUMPS
	$('#ventas_ajustar_ord_compra').keyup(function(event) {
		if(event.which == 13)
		{
			$("#ventas_ajustar_cliente").jqxComboBox('focus');
		}
	});
	$('#ventas_ajustar_cliente').keyup(function(event) {
		if(event.which == 13)
		{
			$("#ventas_ajustar_cliente_ID").jqxComboBox('focus');
		}
	});
	$('#ventas_ajustar_cliente_ID').keyup(function(event) {
		if(event.which == 13)
		{
			$("#ventas_ajustar_fecha_rem").jqxDateTimeInput('focus');
		}
	});
	$('#ventas_ajustar_fecha_rem').keyup(function(event) {
		if(event.which == 13)
		{
			$("#ventas_ajustar_remision").jqxInput('focus');
		}
	});
	$('#ventas_ajustar_remision').keyup(function(event) {
		if(event.which == 13)
		{
			$("#ventas_ajustar_factura").jqxInput('focus');
		}
	});
	$('#ventas_ajustar_factura').keyup(function(event) {
		if(event.which == 13)
		{
			$("#ventas_ajustar_codigo").jqxComboBox('focus');
		}
	});
	$('#ventas_ajustar_codigo').keyup(function(event) {
		if(event.which == 13)
		{
			$("#ventas_ajustar_producto").jqxComboBox('focus');
		}
	});
	$('#ventas_ajustar_producto').keyup(function(event) {
		if(event.which == 13)
		{
			if (ExistenciaCargada == false) {
				return;
			}
			else {
				$("#ventas_ajustar_cantidad").jqxNumberInput('focus');
				var input = $('#ventas_ajustar_cantidad input')[0];
				if ('selectionStart' in input) {
					input.setSelectionRange(0, 0);
				} else {
					var range = input.createTextRange();
					range.collapse(true);
					range.moveEnd('character', 0);
					range.moveStart('character', 0);
					range.select();
				}
			}
		}
	});
	$('#ventas_ajustar_cantidad').keyup(function(event) {
		if(event.which == 13 && cantidad_exedida == false)
		{
			$("#ventas_ajustar_listap").jqxDropDownList('focus');
		}
	});
	$('#ventas_ajustar_listap').keyup(function(event) {
		if(event.which == 13)
		{
			Add_Row();
		}
	});
	
	$("#ventas_ajustar_cliente").jqxInput(
	{
		width: 420,
		height: 20,
		theme: mytheme,
		disabled: true,
	});
	
	$("#ventas_ajustar_cliente_ID").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true,
	});
	
	$("#ventas_ajustar_direccion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 420,
		disabled: true,
	});
	
	$("#ventas_ajustar_telefono").jqxInput({//.jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 112,
		//mask: '(###)###-####',
		disabled: true,
	});
	
	$("#ventas_ajustar_contacto_p").jqxInput({
		theme: mytheme,
		height: 20,
		width: 200,
		disabled: true,
	});
	
	$("#ventas_ajustar_e-mail").jqxInput({
		theme: mytheme,
		height: 20,
		width: 130,
		disabled: true,
	});
	
	$("#ventas_ajustar_remision").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	
	$("#ventas_ajustar_ord_compra").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		//placeHolder: "",
		source: function (query, response) {
			var dataAdapter = new $.jqx.dataAdapter
			(
				{
					datatype: "json",
					datafields: [
						{ name: 'OrdenCompra', type: 'string'},
						{ name: 'Interno', type: 'string'}
					],
					url: "modulos/datos.php",
					data: {
						"Ventas_Ajustar":true,
						"Limit":10
					},
				},
				{
					autoBind: true,
					formatData: function (data) {
						data.Contains = query;
						return data;
					},
					loadComplete: function (data) {
						if (data.length > 0) {
							response($.map(data, function (item) {
								return {
									label: item.OrdenCompra,
									value: item.Interno
								}
							}));
						}
					}
				}
			);
		}
	});
	$("#ventas_ajustar_ord_compra").bind('change', function (event)
	{
		if (event.args)
		{
			if (ID_Interno == event.args.item.value)
				return;
			
			ID_OrdenCompra = event.args.item.label;
			ID_Interno = event.args.item.value;
			var Tmp_Interno = ID_Interno;
			//---
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				
				if (ClickOK == true) {
					EnableDisableAll(false);
					ClickOK = false;
				}
				ClearAll("ventas_ajustar_ord_compra");
				CargarValores(Tmp_Interno);
			},500);
		}
		else
		{
			if ($("#ventas_ajustar_ord_compra").val() == "")
				ClearDocument();
			else
			{
				ID_OrdenCompra = ""
				ID_Interno = "";
			}
		}
	});
	
	/*$("#ventas_ajustar_ord_compra").jqxComboBox({
		theme: mytheme,
		width: 120,
		height: 20,
		source: OrdenDataAdapter,
		remoteAutoComplete: true,
		autoDropDownHeight: true,
		//searchMode: 'containsignorecase',
		//autoComplete: true,
		//promptText: 'Buscar Orden',
		selectedIndex: -1,
		displayMember: 'OrdenCompra',
		valueMember: 'Interno',
		renderer: function (index, label, value)
		{
			var item = OrdenDataAdapter.records[index];
			if (item != null)
			{
				//var label = item.OrdenCompra + "(" + item.Interno + ")";
				//return label;
				return item.OrdenCompra;
			}
			return "";
		},
		renderSelectedItem: function(index, item)
		{
			var item = OrdenDataAdapter.records[index];
			if (item != null) {
				return item.OrdenCompra;
			}
			return "";   
		},
		search: function (searchString) {
			OrdenDataAdapter.dataBind();
		}
	});
	$("#ventas_ajustar_ord_compra").bind('change', function (event)
	{
		if (event.args)
		{
			if (ID_Interno == event.args.item.value)
				return;
			
			ID_OrdenCompra = event.args.item.label;
			ID_Interno = event.args.item.value;
			//---
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (ClickOK == true) {
					EnableDisableAll(false);
					ClickOK = false;
				}
				ClearAll("ventas_ajustar_ord_compra");
				CargarValores();
			},500);
		}
		else
		{
			var item_value = $("#ventas_ajustar_ord_compra").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClearDocument();
			}
			else
			{
				var value = $("#ventas_ajustar_ord_compra").val();
				if (value != "")
				{
					ID_Interno = "";
					ID_OrdenCompra = "";
					OrdenDataAdapter.dataBind();
				}
				else
					ClearDocument();
			}
		}
	});*/
	/*$("#ventas_ajustar_ord_compra").bind('change', function (event) {
		if (event.args)
		{
			if (ID_Interno == event.args.item.value)
				return;
			
			ID_OrdenCompra = event.args.item.label;
			ID_Interno = event.args.item.value;
			//---
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				if (ClickOK == true) {
					EnableDisableAll(false);
					ClickOK = false;
				}
				ClearAll("ventas_ajustar_ord_compra");
				CargarValores();
				clearTimeout(Timer1);
			},500);
		}
		else
		{
			//ID_Interno = "";
			//ID_OrdenCompra = "";
			//$("#ventas_ajustar_ord_compra").jqxComboBox('clearSelection');
			OrdenDataAdapter.dataBind();
			//ClearDocument();
		}
	});*/
	/*$("#ventas_ajustar_ord_compra").bind('bindingComplete', function (event)
	{
		if (ID_Interno != "")
		{
			$("#ventas_ajustar_ord_compra").jqxComboBox('selectItem', ID_Interno);
			return;
		}
		
		var value = $("#ventas_ajustar_ord_compra").val();
		var item = $("#ventas_ajustar_ord_compra").jqxComboBox('getItems');
		//alert(JSON.stringify(item))
		//alert(item.length)
		if (item != undefined)
		{
			for (var i = 0; i < item.length; i++)
			{
				if (item[i].label == value)
				{
					$("#ventas_ajustar_ord_compra").jqxComboBox('selectItem', item[i].value);
					return;
				}
			}
		}
		else
		{
			//alert("clear!")
			//ClearDocument();
		}	
	});*/
	// Actualizar Ordenes
	/*OrdenSource.data =  {"Ventas_Ajustar":true};
	var OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
	$("#ventas_ajustar_ord_compra").jqxComboBox({source: OrdenAdapter});*/
	
	$("#ventas_ajustar_factura").jqxInput({
		theme: mytheme,
		height: 20,
		width: 120
	});
	
	$("#ventas_ajustar_interno").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true
	});
	
	//---------------------------------------------------------------- PARTE 2
	//-- GLOBAL
	var FacturaExistencia = false;
	var RowAdded = true;
	
	// prepare the data
	var CB_ProductoSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'CodFab', type: 'string'},
		{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
		async: true
	};
	
	var CB_ProductoDataAdapter = new $.jqx.dataAdapter(CB_ProductoSource);
	
	function GetPriceList ()
	{
		ExistenciaCargada = false;
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Existencia', type: 'decimal' },
				{ name: 'Lista1', type: 'decimal' },
				{ name: 'Lista2', type: 'decimal' },
				{ name: 'Lista3', type: 'decimal' },
				{ name: 'Lista4', type: 'decimal' },
				{ name: 'Lista4', type: 'decimal' },
				{ name: 'FacturaExistencia', type: 'bool' },
			],
			data:{"Precios":$("#ventas_ajustar_codigo").val()},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				$("#ventas_ajustar_listap").jqxDropDownList('clear');
				$("#ventas_ajustar_existencia").val('');
				var records = GetValuesAdapter.records;
				$("#ventas_ajustar_listap").jqxDropDownList('addItem', {label: "P1 $"+ records[0]["Lista1"], value: records[0]["Lista1"]});
				$("#ventas_ajustar_listap").jqxDropDownList('addItem', {label: "P2 $"+ records[0]["Lista2"], value: records[0]["Lista2"]});
				$("#ventas_ajustar_listap").jqxDropDownList('addItem', {label: "P3 $"+ records[0]["Lista3"], value: records[0]["Lista3"]});
				$("#ventas_ajustar_listap").jqxDropDownList('addItem', {label: "P4 $"+ records[0]["Lista4"], value: records[0]["Lista4"]});
				$("#ventas_ajustar_listap").jqxDropDownList('selectIndex', 0);
				
				var datinfo = $("#ventas_ajustar_items_grid").jqxGrid('getdatainformation');
				var count = datinfo.rowscount;
				var totalc = 0;
				for (var i = 0; i < count; i++)
				{
					var currentRow = $('#ventas_ajustar_items_grid').jqxGrid('getrowdata', i);
					if (currentRow.CodFab == $("#ventas_ajustar_codigo").val())
					{
						totalc = totalc + currentRow.Cantidad;
					}
				}
				var totala = records[0]["Existencia"] - totalc;
				if (totala < 0)
					totala = 0;
				$("#ventas_ajustar_existencia").val(totala);
				FacturaExistencia = records[0]["FacturaExistencia"];
				ExistenciaCargada = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: " + error, 3);
			},
		});
	};
	
	$("#ventas_ajustar_codigo").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: CB_ProductoDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar Codigo',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#ventas_ajustar_codigo").bind('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if ($("#ventas_ajustar_producto").val() != event.args.item.value)
					$("#ventas_ajustar_producto").jqxComboBox('selectItem', event.args.item.value);
				GetPriceList();
			},300);
		}
		else
		{
			var item_value = $("#ventas_ajustar_codigo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#ventas_ajustar_codigo").jqxComboBox('clearSelection');
				$("#ventas_ajustar_producto").jqxComboBox('clearSelection');
				$("#ventas_ajustar_existencia").val("");
			}
			else
			{
				var value = $("#ventas_ajustar_codigo").val();
				var item = $("#ventas_ajustar_codigo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#ventas_ajustar_codigo").jqxComboBox('clearSelection');
					$("#ventas_ajustar_producto").jqxComboBox('clearSelection');
					$("#ventas_ajustar_existencia").val("");
				}
				else
					$("#ventas_ajustar_codigo").jqxComboBox('selectItem', item.value);
			}
		}
	});
	
	$("#ventas_ajustar_producto").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		source: CB_ProductoDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Producto',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#ventas_ajustar_producto").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#ventas_ajustar_codigo").val() != event.args.item.value)
				$("#ventas_ajustar_codigo").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#ventas_ajustar_producto").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#ventas_ajustar_codigo").jqxComboBox('clearSelection');
				$("#ventas_ajustar_producto").jqxComboBox('clearSelection');
				$("#ventas_ajustar_existencia").val("");
			}
			else
			{
				var value = $("#ventas_ajustar_producto").val();
				var item = $("#ventas_ajustar_producto").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#ventas_ajustar_producto").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#ventas_ajustar_codigo").jqxComboBox('clearSelection');
				$("#ventas_ajustar_producto").jqxComboBox('clearSelection');
				$("#ventas_ajustar_existencia").val("");
			}
		}
	});
	
	$("#ventas_ajustar_existencia").jqxInput({
		theme: mytheme,
		height: 20,
		width: 140,
		disabled: true,
		rtl: true
	});
	
	$("#ventas_ajustar_cantidad").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 170,
		inputMode: 'simple',
		spinButtons: false
	});
	
	var Data =
	{};
	
	$("#ventas_ajustar_listap").jqxDropDownList({
		theme: mytheme,
		height: 20,
		width: 186,
		source: Data,
		placeHolder: 'Lista de Precios',
		selectedIndex: -1
	});
	
	$('#ventas_ajustar_form_validation').jqxValidator({
		rules:
		[
			{
				input: '#ventas_ajustar_cantidad', message: 'Debe ser un valor mayor a 0!', action: 'keyup, blur', rule: function (input, commit) {
					if (input.val() < 0) {
						return false;
					}
					return true;
				}
			},
			{
				input: '#ventas_ajustar_cantidad', message: 'Cantidad mayor a Existencia!', action: 'keyup, blur', rule: function (input, commit) {
					if (input.val() > $("#ventas_ajustar_existencia").val() && FacturaExistencia == false) {
						cantidad_exedida = true;
						return false;
					} else {
						cantidad_exedida = false;
					}
					return true;
				}
			}
		]
	});
	
	var source =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'CodFab', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'UndMed', type: 'string' },
			{ name: 'Existencia', type: 'decimal' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Dcto', type: 'decimal' },
			{ name: 'Unitario', type: 'decimal' },
			{ name: 'Subtotal', type: 'decimal' },
			{ name: 'Precio', type: 'decimal' },
			{ name: 'Produccion', type: 'bool' },
			{ name: 'FacturaExistencia', type: 'bool' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var dataAdapter = new $.jqx.dataAdapter(source);
	
	function Add_Row()
	{
		if (RowAdded == false) {
			return;
		}
		RowAdded = false;
	
		var Product = $("#ventas_ajustar_producto").jqxComboBox('getSelectedItem');
		var ProductPrice = $("#ventas_ajustar_listap").jqxDropDownList('getSelectedItem');
		var ExistenciaNum = $("#ventas_ajustar_existencia").val();
		var CantidadNum = $("#ventas_ajustar_cantidad").val();
		
		if (!Product)
		{
			Alerts_Box("Favor Seleccionar un Producto!", 4);
			WaitClick("ventas_ajustar_producto");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum > ExistenciaNum && FacturaExistencia == false) {
			Alerts_Box("Cantidad mayor a la Existencia!", 4);
			WaitClick("ventas_ajustar_cantidad");
			RowAdded = true;
			return;
		}
		
		if (CantidadNum <= 0) {
			Alerts_Box("Debe Ingresar una Cantidad Mayor a 0!", 4);
			WaitClick("ventas_ajustar_cantidad");
			RowAdded = true;
			return;
		}
		
		if (ExistenciaNum <= 0 && FacturaExistencia == false) {
			Alerts_Box("\"AGOTADO\" No hay ventas_ajustar_existencia!</br>Favor Seleccionar otro Producto", 4);
			WaitClick("ventas_ajustar_producto");
			RowAdded = true;
			return;
		}
		
		var datinfo = $("#ventas_ajustar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (i=0;i<count;i++) {
			var currentRow = $('#ventas_ajustar_items_grid').jqxGrid('getrowdata', i);
			if (currentRow.CodFab == Product.value)
			{
				var totalc = CantidadNum + currentRow.Cantidad;
				if (totalc > currentRow.Existencia) {
					Alerts_Box("Cantidad Mayor a Existencia!", 3);
					RowAdded = true;
					return;
				}
				var datarow = [{
					"CodFab":currentRow.CodFab,
					"Nombre":currentRow.Nombre,
					"UndMed":currentRow.UndMed,
					"Existencia":currentRow.Existencia,
					"Cantidad":totalc,
					"Peso":currentRow.Peso,
					"Dcto":currentRow.Dcto,
					"Unitario":currentRow.Unitario,
					"Precio":currentRow.Precio,
					"Produccion":currentRow.Produccion,
					"FacturaExistencia":currentRow.FacturaExistencia,
				}];
				var id = $("#ventas_ajustar_items_grid").jqxGrid('getrowid', i);
				$("#ventas_ajustar_items_grid").jqxGrid('deleterow', id);
				$("#ventas_ajustar_items_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#ventas_ajustar_codigo").jqxComboBox('clearSelection');
				$("#ventas_ajustar_producto").jqxComboBox('clearSelection');
				$("#ventas_ajustar_existencia").val('');
				$("#ventas_ajustar_cantidad").val('');
				$("#ventas_ajustar_listap").jqxDropDownList('clear');
				Calcular();
				RowAdded = true;
				return;
			}
		}
		
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'UndMed', type: 'string'},
				{ name: 'Peso', type: 'decimal' },
				{ name: 'Produccion', type: 'bool' },
				{ name: 'FacturaExistencia', type: 'bool' }
			],
			type: 'GET',
			data:{"Productos":Product.value},
			url: "modulos/datos_productos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function () {
				var records = GetValuesAdapter.records;
				var datarow = [{
					"CodFab":Product.value,
					"Nombre":Product.label,
					"UndMed":records[0]["UndMed"],
					"Existencia":ExistenciaNum,
					"Cantidad":CantidadNum,
					"Peso":records[0]["Peso"],
					"Dcto":0,
					"Unitario":ProductPrice.value,
					"Precio":ProductPrice.value,
					"Produccion":records[0]["Produccion"],
					"FacturaExistencia":records[0]["FacturaExistencia"]
				}];
				$("#ventas_ajustar_items_grid").jqxGrid("addrow", null, datarow, "first");
				// Clear Values
				$("#ventas_ajustar_codigo").jqxComboBox('clearSelection');
				$("#ventas_ajustar_producto").jqxComboBox('clearSelection');
				$("#ventas_ajustar_existencia").val('');
				$("#ventas_ajustar_cantidad").val('');
				$("#ventas_ajustar_listap").jqxDropDownList('clear');
				Calcular();
				RowAdded = true;
			},
			loadError: function(jqXHR, status, error) {
				Alerts_Box("Request failed: "+error, 3);
			},
		});
	};

	$("#ventas_ajustar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		height: 260,
		source: dataAdapter,
		showtoolbar: true,
		editable: true,
		editmode: 'dblclick',
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="ventas_ventas_ajustar_addrowbutton" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="ventas_ventas_ajustar_deleterowbutton" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#ventas_ventas_ajustar_addrowbutton").jqxButton({theme: mytheme, template: "success"});
			$("#ventas_ventas_ajustar_deleterowbutton").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#ventas_ventas_ajustar_addrowbutton").on('click', function () {
				Add_Row();
			});
			// delete row.
			$("#ventas_ventas_ajustar_deleterowbutton").on('click', function () {
				var selectedrowindex = $("#ventas_ajustar_items_grid").jqxGrid('getselectedrowindex');
				var rowscount = $("#ventas_ajustar_items_grid").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#ventas_ajustar_items_grid").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#ventas_ajustar_items_grid").jqxGrid('deleterow', id);
					Calcular();
				}
			});
		},
		columns:
		[
			{ text: 'Codigo', datafield: 'CodFab', editable: false, width: 90, height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: 240, height: 20 },
			{ text: 'Und', datafield: 'UndMed', editable: false, width: 50, height: 20 },
			{ text: 'Exist.', datafield: 'Existencia', editable: false, width: 100, height: 20, cellsalign: 'right' },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: 100,
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'right',
				columntype: 'numberinput',
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor o igual a 0!" };
					}
					return true;
				},
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					if (rowdata.Cantidad > rowdata.Existencia && rowdata.FacturaExistencia == false) {
						Alerts_Box("La Cantidad no debe ser mayor a la Existencia!", 3);
						return "<div id='row_error'>"+rowdata.Cantidad+"</div>";
					}
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 12 });
				}
			},
			{
				text: 'Peso',
				datafield: 'Peso',
				width: 100,
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
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
				text: '%Dcto',
				datafield: 'Dcto',
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'p',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				validation: function (cell, value) {
					if (value < 0) {
						return { result: false, message: "La Cantidad debe ser mayor o igual a 0!" };
					}
					return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 2, digits: 2 });
				}
			},
			{
				text: 'Unitario',
				datafield: 'Unitario',
				width: 110,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				editable: Admin ? true:Modificar,
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
				width: 150,
				height: 20,
				editable: false,
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = Math.round(parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad));
					//var total2 = Math.round( total - (parseFloat(rowdata.Unitario) * parseFloat(rowdata.Cantidad)) / 100 * parseFloat(rowdata.Dcto) );
					
					return "<div style='margin: 4px;' class='jqx-right-align'>" + dataAdapter.formatNumber(total, "c2") + "</div>";
				}
			},
			{ text: '', datafield: 'Precio', editable: false, columntype: 'numberinput', width: 0, height: 20},
			{ text: 'P', datafield: 'Produccion', columntype: 'checkbox', width: 15 },
			{ text: 'F', datafield: 'FacturaExistencia', columntype: 'checkbox', width: 15 },
		]
	});
	
	$('#ventas_ajustar_items_grid').jqxGrid('hidecolumn', 'Precio');
	$('#ventas_ajustar_items_grid').jqxGrid('hidecolumn', 'Produccion');
	$('#ventas_ajustar_items_grid').jqxGrid('hidecolumn', 'FacturaExistencia');
	
	$("#ventas_ajustar_items_grid").on('cellvaluechanged', function (event) 
	{
		if (event.args)
		{
			var args = event.args;
			var datafield = event.args.datafield;
			var rowBoundIndex = args.rowindex;
			var value = args.newvalue;
			var oldvalue = args.oldvalue;
			
			if (datafield == "Cantidad" || datafield == "Dcto" || datafield == "Unitario")
			{
				if (datafield == "Dcto")
				{
					var Precio = parseFloat($("#ventas_ajustar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Precio"));
					var Total = Precio - ((Precio / 100) * value);
					$("#ventas_ajustar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Unitario", Total);
				}
				else if (datafield == "Unitario")
				{
					var Precio = parseFloat($("#ventas_ajustar_items_grid").jqxGrid('getcellvalue', rowBoundIndex, "Precio"));
					var Total = (((Precio - value) / Precio) * 100).toFixed(2);

					if (Total < 0)
						Total = 0;
					
					$("#ventas_ajustar_items_grid").jqxGrid('setcellvalue', rowBoundIndex, "Dcto", Total);	
				}
				Calcular();
			}
		}
	});
	
	// ------------------------------------------ PARTE 3
	// prepare data
	var ConductorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Chofer', type: 'string'},
			{ name: 'ClienteID', type: 'string'},
		],
		data: {"Chofer":true},
		url: "modulos/parametros.php",
	};
	var ConductorDataAdapter = new $.jqx.dataAdapter(ConductorSource);
	
	var VehiculoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Placa', type: 'string'},
		],
		data: {"Vehiculo":true},
		url: "modulos/parametros.php",
	};
	var VehiculoDataAdapter = new $.jqx.dataAdapter(VehiculoSource);
	
	$("#ventas_ajustar_conductor").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: ConductorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Conductor',
		selectedIndex: -1,
		displayMember: 'Chofer',
		valueMember: 'ClienteID',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#ventas_ajustar_conductor").bind('change', function (event) {
		if (!event.args)
			$("#ventas_ajustar_conductor").jqxComboBox('clearSelection');
	});
	
	$("#ventas_ajustar_formaP").jqxInput({
		theme: mytheme,
		height: 20,
		width: 70,
		disabled: true
	});
	
	$("#ventas_ajustar_placa").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 135,
		source: VehiculoDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Placa',
		selectedIndex: -1,
		displayMember: 'Placa',
		valueMember: 'Placa',
		searchMode: 'startswithignorecase',
		autoComplete: true,
	});
	$("#ventas_ajustar_placa").bind('change', function (event) {
		if (!event.args)
			$("#ventas_ajustar_placa").jqxComboBox('clearSelection');
	});
	
	$("#ventas_ajustar_ruta").jqxInput({
		theme: mytheme,
		height: 20,
		width: 70,
		disabled: true
	});
	
	$("#ventas_ajustar_pesokg").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 135,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 12,
		disabled: true
	});
	
	$("#ventas_ajustar_dir_entrega").jqxInput({
		theme: mytheme,
		height: 20,
		width: 380
	});
	
	var SectorSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Barrio', type: 'string'},
		{ name: 'Ruta', type: 'string'}
		],
		type: 'GET',
		data: {"Ruta":true},
		url: "modulos/parametros.php",
		async: true
	};
	var SectorDataAdapter = new $.jqx.dataAdapter(SectorSource);
	
	var Sector =
	$("#ventas_ajustar_sector").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: SectorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Sector',
		selectedIndex: -1,
		displayMember: 'Barrio',
		valueMember: 'Ruta',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#ventas_ajustar_sector").bind('change', function (event) {
		if (!event.args) {
			$("#ventas_ajustar_sector").jqxComboBox('clearSelection');
			$("#ventas_ajustar_ruta").val('');
		}
	});
	$("#ventas_ajustar_sector").bind('select', function (event) {
		if (event.args) {
			var ruta_val = event.args.item.value;
			$("#ventas_ajustar_ruta").val(ruta_val);
		}
	});
	
	var VendedorSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Codigo', type: 'string'},
		{ name: 'Vendedor', type: 'string'}
		],
		type: 'GET',
		data: {"Venta":true},
		url: "modulos/parametros.php",
		async: true
	};
	var VendedorDataAdapter = new $.jqx.dataAdapter(VendedorSource);
	
	$("#ventas_ajustar_vendedor").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: VendedorDataAdapter,
		dropDownHeight: 100,
		promptText: 'Seleccionar Vendedor',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#ventas_ajustar_vendedor").bind('change', function (event) {
		if (!event.args) {
			$("#ventas_ajustar_vendedor").jqxComboBox('clearSelection');
		}
	});
	
	var iva = 0;
	var total = 0;
	
	var Subtotal_Text =
	$("#ventas_ajustar_subtotal_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		//textAlign: 'center',
		disabled: true,
		placeHolder: "               SUBTOTAL",
	});
	
	var Subtotal_Total =
	$("#ventas_ajustar_subtotal_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
		//decimalSeparator: ","
	});
	
	function Calcular(Ignore)
	{
		var Calc_Timer = setTimeout(function()
		{
			var information = $('#ventas_ajustar_items_grid').jqxGrid('getdatainformation');
			var rowscounts = information.rowscount;
			var old_total = 0;
			var total_peso = 0;
			for (i=0; i<rowscounts; i++){
				var currentRow = $('#ventas_ajustar_items_grid').jqxGrid('getrowdata', i);
				var total1 = parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad);
				//var total2 = Math.round( total1 - (parseFloat(currentRow.Unitario) * parseFloat(currentRow.Cantidad)) / 100 * parseFloat(currentRow.Dcto) );
				old_total = old_total + total1;
				total_peso = total_peso + parseFloat(currentRow.Peso) * parseFloat(currentRow.Cantidad);
			};
			
			var valor_tipo = $('#ventas_ajustar_tipo_servicio_precio').val();
			if ($("#ventas_ajustar_formaP").val() != "Efectivo")
				old_total = old_total + valor_tipo;
			
			if (total == old_total && Ignore == false)
				return;
			else
				total = old_total;
			
			iva = Math.round(total - (total / 1.19));
			
			// if ($('#ventas_ajustar_fecha_rem').val() >= '11-ene-2017') {
			// 	iva = Math.round(total - (total / 1.19));
			// }
			// else {
			// 	iva = Math.round(total - (total / 1.16));
			// }
			var tipo_dcto = $("#ventas_ajustar_tipo_descuento_precio").val();
			var dcto = $("#ventas_ajustar_tipo_descuento").val();
			if (tipo_dcto <= 0 && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#ventas_ajustar_tipo_descuento_precio").val(tipo_dcto);
			}
			else if (tipo_dcto != iva && dcto.indexOf("IVA") > 0)
			{
				tipo_dcto = iva;
				$("#ventas_ajustar_tipo_descuento_precio").val(tipo_dcto);
			}
			else if (tipo_dcto <= 0 && dcto.indexOf("IVA") < 1)
			{
				$("#ventas_ajustar_tipo_descuento_precio").val("0");
			}
			
			var subtotal = Math.round(total);
			total = Math.round(total - tipo_dcto);
			if (total < 0)
				total = 0;
			
			if (total > Total_Original && ($("#ventas_ajustar_formaP").val() == "Efectivo"))
			{
				ErrorSaldo = true;
				Alerts_Box("El Valor de la Factura es mayor al Valor Original.", 3);
			}
			else
				ErrorSaldo = false;
			
			$("#ventas_ajustar_subtotal_total").val(subtotal);
			$("#ventas_ajustar_iva_precio").val(iva);
			$("#ventas_ajustar_total_total").val(total);
			$("#ventas_ajustar_pesokg").val(total_peso);
			$("#ventas_ajustar_codigo").jqxComboBox('focus');
			clearTimeout(Calc_Timer);
		},200);
	};
	
	var ServicioSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'}
		],
		type: 'GET',
		data: {"OtroSrv":true},
		url: "modulos/parametros.php",
		async: true
	};
	var ServicioDataAdapter = new $.jqx.dataAdapter(ServicioSource);
	
	$("#ventas_ajustar_tipo_servicio").jqxComboBox({
		theme: mytheme,
		height: 25,
		width: 180,
		source: ServicioDataAdapter,
		dropDownHeight: 70,
		promptText: 'Tipo Servicio',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Nombre'
	});
	$("#ventas_ajustar_tipo_servicio").bind('change', function (event) {
		if (!event.args) {
			$("#ventas_ajustar_tipo_servicio").jqxComboBox('clearSelection');
		}
	});
	
	$("#ventas_ajustar_tipo_servicio_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$('#ventas_ajustar_tipo_servicio_precio').on('change', function (event) 
	{
		Calcular();
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("ventas_ajustar_tipo_servicio_precio");
		}
	});
	
	$("#ventas_ajustar_iva_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                     IVA",
	});
	
	$("#ventas_ajustar_iva_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	
	var DescuentoSource =
	{
		datatype: "json",
		datafields: [
		{ name: 'Nombre', type: 'string'}
		],
		type: 'GET',
		data: {"TipoDcto":true},
		url: "modulos/parametros.php",
		async: true
	};
	var DescuentoDataAdapter = new $.jqx.dataAdapter(DescuentoSource);
	
	$("#ventas_ajustar_tipo_descuento").jqxComboBox({
		theme: mytheme,
		height: 25,
		width: 180,
		source: DescuentoDataAdapter,
		dropDownHeight: 70,
		promptText: 'Tipo Descuento',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Nombre'
	});
	$("#ventas_ajustar_tipo_descuento").bind('change', function (event) {
		if (!event.args) {
			$("#ventas_ajustar_tipo_descuento").jqxComboBox('clearSelection');
			$("#ventas_ajustar_tipo_descuento_precio").val("");
		}
		else
			Calcular(true);
	});
	
	$("#ventas_ajustar_tipo_descuento_precio").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	$('#ventas_ajustar_tipo_descuento_precio').on('change', function (event) 
	{
		Calcular(true);
		if (event.args.value < 0) {
			Alerts_Box("Debe Ingresar un valor mayor a 0!", 3);
			WaitClick_NumberInput("ventas_ajustar_tipo_descuento_precio");
		}
	});
	
	$("#ventas_ajustar_total_text").jqxInput({
		theme: mytheme,
		height: 25,
		width: 180,
		disabled: true,
		placeHolder: "                  TOTAL",
	});
	
	$("#ventas_ajustar_total_total").jqxNumberInput({
		theme: mytheme,
		height: 25,
		width: 320,
		textAlign: 'right',
		symbol: '$',
		digits: 18,
		max: 999999999999999999,
	});
	
	function CrearDespacho ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ErrorSaldo) {
			Alerts_Box("El Valor de la Factura es mayor al valor original.", 3);
			Locked = false;
			return;
		}
		
		if ($("#ventas_ajustar_ord_compra").val() == "") {
			Alerts_Box("Debe Ingresar una Orden de Compra!", 3);
			WaitClick_Input("ventas_ajustar_ord_compra");
			Locked = false;
			return;
		}
		
		if ($("#ventas_ajustar_interno").val() == "") {
			Alerts_Box("Debe Ingresar una Orden de Compra!", 3);
			WaitClick_Input("ventas_ajustar_ord_compra");
			Locked = false;
			return;
		}
		
		var datinfo = $("#ventas_ajustar_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		if (count <= 0) {
			Alerts_Box("Debe Ingresar un Producto!", 3);
			WaitClick_Combobox("ventas_ajustar_producto");
			Locked = false;
			return;
		}
		
		if ($("#ventas_ajustar_tipo_servicio_precio").val() > 0) {
			if ($("#ventas_ajustar_tipo_servicio").val() < 0 | $("#ventas_ajustar_tipo_servicio").val() == "") {
				Alerts_Box("Debe Ingresar Una Tipo de Servicio", 3);
				WaitClick_Combobox("ventas_ajustar_tipo_servicio");
				Locked = false;
				return;
			}
		}
		
		if ($("#ventas_ajustar_conductor").val() < 0 | $("#ventas_ajustar_conductor").val() == "") {
			Alerts_Box("Debe Ingresar Un Conductor!", 3);
			WaitClick_Combobox("ventas_ajustar_conductor");
			Locked = false;
			return;
		}
		
		if ($("#ventas_ajustar_placa").val() < 0 | $("#ventas_ajustar_placa").val() == "") {
			Alerts_Box("Debe Ingresar Un Vehiculo!", 3);
			WaitClick_Combobox("ventas_ajustar_placa");
			Locked = false;
			return;
		}
		
		if ($("#ventas_ajustar_vendedor").val() < 0 | $("#ventas_ajustar_vendedor").val() == "") {
			Alerts_Box("Debe Ingresar Un Vendedor!", 3);
			WaitClick_Combobox("ventas_ajustar_vendedor");
			Locked = false;
			return;
		}
		if ($("#ventas_ajustar_sector").val() < 0 | $("#ventas_ajustar_sector").val() == "") {
			Alerts_Box("Debe Ingresar Un Sector!", 3);
			WaitClick_Combobox("ventas_ajustar_sector");
			Locked = false;
			return;
		}

		for (i=0; i<count; i++){
			var array = {};
			var currentRow = $('#ventas_ajustar_items_grid').jqxGrid('getrowdata', i);
			
			array["CodFab"] = currentRow.CodFab;
			array["Cantidad"] = currentRow.Cantidad;
			array["Dcto"] = currentRow.Dcto;
			array["Unitario"] = currentRow.Unitario;
			
			if (currentRow.Cantidad > currentRow.Existencia && currentRow.FacturaExistencia == false) {
				Alerts_Box("Error en Producto "+currentRow.CodFab+"<br/>La Cantidad es Mayor a la Existencia!", 3);
				Locked = false;
				return;
			}
			
			if (i==0) {
				array["ClienteID"] = $("#ventas_ajustar_cliente_ID").val();
				array["Interno"] = $("#ventas_ajustar_interno").val();
				array["Remision"] = $("#ventas_ajustar_remision").val();
				array["Ord_Compra"] = $("#ventas_ajustar_ord_compra").val();
				array["Factura"] = $("#ventas_ajustar_factura").val();
				//---
				array["Observaciones"] = $('#ventas_ajustar_observaciones').val();
				array["Subtotal2"] = $("#ventas_ajustar_subtotal_total").val();
				array["TipoServicio"] = $("#ventas_ajustar_tipo_servicio").val();
				array["TipoServicioValor"] = $("#ventas_ajustar_tipo_servicio_precio").val();
				array["Iva"] = $("#ventas_ajustar_iva_precio").val();
				array["TipoDcto"] = $("#ventas_ajustar_tipo_descuento").val();
				array["TipoDctoValor"] = $("#ventas_ajustar_tipo_descuento_precio").val();
				array["Total"] = $("#ventas_ajustar_total_total").val();
				array["Chofer"] = $("#ventas_ajustar_conductor").val();
				array["Placa"] = $("#ventas_ajustar_placa").val();
				array["Peso"] = $("#ventas_ajustar_pesokg").val();
				array["FormaPago"] = $("#ventas_ajustar_formaP").val();
				array["Ruta"] = $("#ventas_ajustar_ruta").val();
				array["Direccion"] = $("#ventas_ajustar_dir_entrega").val();
				array["VendedorID"] = $("#ventas_ajustar_vendedor").val();
			}
			myarray[i] = array;
		};
		
		/*alert(JSON.stringify(myarray))
		Locked = false;
		return;*/
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			type: 'POST',
			url: "modulos/guardar.php",
			data: {"Ventas_Ajustar":myarray},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				ReDefine();
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						EnableDisableAll(true);
						Alerts_Box("Datos Guardados con Exito!", 2);
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
					break;
				}
				
				// Actualizar Ordenes
				/*OrdenSource.data =  {"Ventas_Ajustar":true};
				var OrdenAdapter = new $.jqx.dataAdapter(OrdenSource);
				$("#ventas_ajustar_ord_compra").jqxComboBox({source: OrdenAdapter});*/
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
	};
	
	$('#ventas_ajustar_guardar').jqxButton({
		width: 135,
		template: "info"
	});
	// Prepare Save Changes...
	$("#ventas_ajustar_guardar").bind('click', function ()
	{
		if (!Admin && !Guardar)
			return;
		
		CrearDespacho();
	});
	
	$("#ventas_ajustar_imprimir").jqxButton({width: 135, template: "warning"});
	$("#ventas_ajustar_imprimir").bind('click', function ()
	{
		if (!Admin && !Imprimir)
			return;
		
		window.open("imprimir/factura.php?Interno="+$("#ventas_ajustar_interno").val()+"", "", "width=700, height=600, menubar=no, titlebar=no");
	});
	
	//--- Access
	if (!Admin && !Guardar)
	{
		$("#ventas_ajustar_guardar").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Modificar)
	{
		$("#ventas_ajustar_codigo").jqxComboBox({ disabled: true });
		$("#ventas_ajustar_producto").jqxComboBox({ disabled: true });
		$("#ventas_ventas_ajustar_addrowbutton").jqxButton({ disabled: true });
		$("#ventas_ventas_ajustar_deleterowbutton").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Imprimir)
	{
		$("#ventas_ajustar_imprimir").jqxButton({ disabled: true });
	}

	$("#ventas_ajustar_ord_compra").jqxInput('focus');
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<div id="Ventas_Ajustar_HideButton">&nbsp;&raquo;&nbsp;</div>
	<div id="Ventas_Ajustar_Content_to_Hide">
		<table cellpadding="1" cellspacing="1">
			<tr>
				<td>
					Ord. Compra
				</td>
				<td>
					<!--<div id="ventas_ajustar_ord_compra"></div>-->
					<input type="text" id="ventas_ajustar_ord_compra"/>
				</td>
				<td>
					Cliente
				</td>
				<td colspan="4">
					<input type="text" id="ventas_ajustar_cliente"/>
				</td>
				<td>
					ID Cliente
				</td>
				<td>
					<input type="text" id="ventas_ajustar_cliente_ID"/>
				</td>
			</tr>
			<tr>
				<td>
					Fecha Rem.
				</td>
				<td>
					<div id="ventas_ajustar_fecha_rem"></div>
				</td>
				<td>
					Direcci&oacute;n
				</td>
				<td colspan="4">
					<input type="text" id="ventas_ajustar_direccion"/>
				</td>
				<td>
					Tel&eacute;fono.
				</td>
				<td>
					<input type="text" id="ventas_ajustar_telefono"/>
				</td>
			</tr>
			<tr>
				<td>
					Remisi&oacute;n
				</td>
				<td>
					<input type="text" id="ventas_ajustar_remision"/>
				</td>
				<td>
					Factura
				</td>
				<td>
					<input type="text" id="ventas_ajustar_factura"/>
				</td>
				<td>
					Interno
				</td>
				<td>
					<input type="text" id="ventas_ajustar_interno"/>
				</td>
				<td>
					<input type="text" id="ventas_ajustar_e-mail"/>
				</td>
				<td colspan="2">
					<input type="text" id="ventas_ajustar_contacto_p"/>
				</td>
			</tr>
		</table>
	</div>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<form id="ventas_ajustar_form_validation" action="./">
		<table cellpadding="0" cellspacing="0" style="margin:10px 0px 10px 0px; border: 1px solid #A4BED4; text-align: center;">
			<tr style="background: #E0E9F5">
				<td style="border-bottom: 1px solid #A4BED4;">
					Codigo
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Producto
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Existencia
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Cantidad
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					Lista P.
				</td>
			</tr>
			<tr>
				<td>
					<div id="ventas_ajustar_codigo" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="ventas_ajustar_producto" style="margin-left:7px;"></div>
				</td>
				<td>
					<input type="text" id="ventas_ajustar_existencia" style="margin-left:7px;"/>
				</td>
				<td>
					<div id="ventas_ajustar_cantidad" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="ventas_ajustar_listap" style="margin:0px 7px;"></div>
				</td>
			</tr>
		</table>
	</form>
	<div id="ventas_ajustar_items_grid" style="margin:0px 0px 10px 0px;"></div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="0" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td>
				Observaciones:
			</td>
			<td style="padding-left:15px;">
				Notas:
			</td>
			<td>
				<input type="text" id="ventas_ajustar_subtotal_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="ventas_ajustar_subtotal_total">
				</div>
			</td>
		</tr>
		<tr>
			<td rowspan="4">
				<textarea rows="7" cols="35" id="ventas_ajustar_observaciones" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td rowspan="4">
				<textarea rows="7" cols="25" id="ventas_ajustar_notas" readonly="true" style="resize:none; margin-left:15px;"></textarea>
			</td>
			<td>
				<div id="ventas_ajustar_tipo_servicio" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="ventas_ajustar_tipo_servicio_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="ventas_ajustar_iva_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="ventas_ajustar_iva_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="ventas_ajustar_tipo_descuento" style="margin-left:15px;">
				</div>
			</td>
			<td>
				<div id="ventas_ajustar_tipo_descuento_precio">
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="ventas_ajustar_total_text" style="margin-left:15px;"/>
			</td>
			<td>
				<div id="ventas_ajustar_total_total">
				</div>
			</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="2" style="margin:5px 0px 10px 0px;">
		<tr>
			<td>
				Conductor
			</td>
			<td>
				<div id="ventas_ajustar_conductor"></div>
			</td>
			<td>
				Placa:
			</td>
			<td>
				<div id="ventas_ajustar_placa"></div>
			</td>
			<td>
				F. Pago
			</td>
			<td>
				<input type="text" id="ventas_ajustar_formaP"/>
			</td>
			<td style="padding-left:20px">
				<input type="button" id="ventas_ajustar_guardar" value="Ajustar Despacho"/>
			</td>
			<td>
				<input type="button" id="ventas_ajustar_imprimir" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<td>
				Vendedor
			</td>
			<td>
				<div id="ventas_ajustar_vendedor"></div>
			</td>
			<td>
				Peso
			</td>
			<td>
				<div id="ventas_ajustar_pesokg"></div>
			</td>
			<td>
				Ruta:
			</td>
			<td>
				<input type="text" id="ventas_ajustar_ruta"/>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td style="width:70px;">
				Dir. Entrega
			</td>
			<td colspan="3">
				<input type="text" id="ventas_ajustar_dir_entrega"/>
			</td>
			<td>
				Sector
			</td>
			<td>
				<div id="ventas_ajustar_sector"></div>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
	</table>
</div>