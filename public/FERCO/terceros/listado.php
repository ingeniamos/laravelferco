<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Timer1 = 0;
	var Timer2 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Terceros_Content");
	var Body = document.getElementById("Terceros_Listado_Content");
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
				SystemMap("Despacho", true);
				
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
	var Supervisor = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{	
				if ($data[$i]["Modulo"] == "Terceros" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Terceros" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}

				if ($data[$i]["Modulo"] == "Terceros" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Modificar"] == "true")
				{
		?>
					Modificar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Terceros" && $data[$i]["SubModulo"] == "Listado" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#terceros_listado_imprimir").jqxButton({ disabled: true });
					$("#terceros_listado_exportar").jqxButton({ disabled: true });
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var SociedadData = new Array();
	
	var TipoIDSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo_Sociedad', type: 'string'}
		],
		data: {"Terceros_Tipo_Doc":true},
		url: "modulos/parametros.php",
		async: false
	};
	var TipoIDDataDataAdapter = new $.jqx.dataAdapter(TipoIDSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				SociedadData.push(records[i]);
			}
		}
	});
	
	var ListaValues = [
		{"Lista":"1"},
		{"Lista":"2"},
		{"Lista":"3"},
		{"Lista":"4"},
	];
	
	var TerminosValues = [
		{"Terminos":"Efectivo"},
		{"Terminos":"15 Dias"},
		{"Terminos":"30 Dias"},
		{"Terminos":"45 Dias"},
		{"Terminos":"60 Dias"},
		{"Terminos":"90 Dias"},
	];
	
	var BarrioData = new Array();
	
	var BarrioSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Barrio', type: 'string'}
		],
		data: {"Terceros_Barrio":true},
		url: "modulos/parametros.php",
		async: false
	};
	var BarrioDataDataAdapter = new $.jqx.dataAdapter(BarrioSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				BarrioData.push(records[i]);
			}
		}
	});
	
	var CiudadData = new Array();
	
	var CiudadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Ciudad', type: 'string'}
		],
		data: {"Terceros_Ciudad":true},
		url: "modulos/parametros.php",
		async: false
	};
	var CiudadDataDataAdapter = new $.jqx.dataAdapter(CiudadSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				CiudadData.push(records[i]);
			}
		}
	});
	
	var DepartamentoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Departamento', type: 'string'},
			{ name: 'Ciudad', type: 'string'},
		],
		data: {"Terceros_Departamento":true},
		url: "modulos/parametros.php",
	};
	var DepartamentoArray = new Array();
	var DepartamentoValue = new Array();
	var DepartamentoDataAdapter = new $.jqx.dataAdapter(DepartamentoSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				DepartamentoArray.push(records[i]);
			}
		}
	});
	
	var ClienteData = new Array();
	var VendedorData = new Array();
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
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
	
	var VendedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Vendedor', type: 'string'}
		],
		data: {"Venta":true},
		url: "modulos/parametros.php",
		async: false
	};
	var VendedorDataAdapter = new $.jqx.dataAdapter(VendedorSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				VendedorData.push(records[i]);
			}
		},
		loadComplete: function (records)
		{
			if (!Admin && !Supervisor)
			{
				var CambiarVendedor = setInterval(function()
				{
					var items = $("#terceros_listado_vendedor").jqxComboBox('getItems'); 
					if (items) {
						$("#terceros_listado_vendedor").jqxComboBox("selectItem", "<?php echo $_SESSION["UserCode"]; ?>");
						$("#terceros_listado_vendedor").jqxComboBox({ disabled: true });
						clearInterval(CambiarVendedor);
					}
				}, 10);
			}
		}
	});
	
	$("#terceros_listado_cliente").jqxComboBox(
	{
		width: 305,
		height: 20,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#terceros_listado_cliente").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#terceros_listado_cliente_ID").val(event.args.item.value);
				LoadValues();
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#terceros_listado_cliente_ID").jqxComboBox(
	{
		width: 120,
		height: 20,
		theme: mytheme,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#terceros_listado_cliente_ID").bind('change', function (event) {
		if (event.args)
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				$("#terceros_listado_cliente").val(event.args.item.value);
				LoadValues();
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#terceros_listado_vendedor").jqxComboBox(
	{
		width: 120,
		height: 20,
		theme: mytheme,
		source: VendedorDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo'
	});
	$("#terceros_listado_vendedor").on('change', function (event) {
		if (!event.args)
		{
			$("#terceros_listado_vendedor").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#terceros_listado_cobrador").jqxComboBox(
	{
		width: 120,
		height: 20,
		theme: mytheme,
		source: VendedorData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo'
	});
	$("#terceros_listado_cobrador").bind('select', function (event) {
		if (!event.args)
		{
			$("#terceros_listado_cobrador").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	var ClasificacionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Clasificacion', type: 'string'},
		],
		data: {"Terceros_Clasificacion":true},
		url: "modulos/parametros.php",
		async: false
	};
	var ClasificacionArray = new Array();
	var ClasificacionDataAdapter = new $.jqx.dataAdapter(ClasificacionSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ClasificacionArray.push(records[i]);
			}
		}
	});
	
	$("#terceros_listado_clasificacion").jqxComboBox(
	{
		width: 120,
		height: 20,
		theme: mytheme,
		source: ClasificacionArray,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Clasificacion',
		valueMember: 'Clasificacion'
	});
	$("#terceros_listado_clasificacion").bind('select', function (event) {
		if (!event.args)
		{
			$("#terceros_listado_clasificacion").jqxComboBox('clearSelection');
			var Empty = [];
			$("#terceros_listado_tipo").jqxComboBox({source: Empty});
		}
		else
		{
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				TipoSource.data = {"Terceros_Tipo": event.args.item.value};
				var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource);
				$("#terceros_listado_tipo").jqxComboBox({source: TipoDataAdapter});
				clearTimeout(Timer1);
			},350);
		}
		LoadValues();
	});
	
	var TipoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Clasificacion', type: 'string'},
			{ name: 'Tipo', type: 'string'}
		],
		data: {"Tipo": true},
		url: "modulos/parametros.php",
	};
	var TipoArray = new Array();
	var TipoValue = new Array();
	var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				TipoArray.push(records[i]);
			}
		}
	});
	
	$("#terceros_listado_tipo").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#terceros_listado_tipo").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_listado_tipo").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	var Estados = [
		{"Estado":"Al Dia"},
		{"Estado":"Mora"},
		{"Estado":"Juridico"},
	];
	
	$("#terceros_listado_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		source: Estados,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Estado',
		valueMember: 'Estado'
	});
	$("#terceros_listado_estado").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_listado_estado").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	var Values = [
		{"Estado":"Completo"},
		{"Estado":"Incompleto"},
	];
	
	$("#terceros_listado_garantias").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 120,
		source: Values,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Estado',
		valueMember: 'Estado'
	});
	$("#terceros_listado_garantias").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_listado_garantias").jqxComboBox('clearSelection');
		}
		LoadValues();
	});
	
	$("#terceros_listado_imprimir").jqxButton({width: 90, template: "warning"});
	$("#terceros_listado_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/terceros.php?ClienteID="+$("#terceros_listado_cliente_ID").val()+"&VendedorID="+$("#terceros_listado_vendedor").val()+"";
		data += "&CobradorID="+$("#terceros_listado_cobrador").val()+"&Clasificacion="+$("#terceros_listado_clasificacion").val()+"";
		data += "&Tipo="+$("#terceros_listado_tipo").val()+"&EstadoC="+$("#terceros_listado_estado").val()+"";
		data += "&Garantia="+$("#terceros_listado_garantias").val()+"";
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#terceros_listado_exportar").jqxButton({width: 90, template: "success"});
	$("#terceros_listado_exportar").bind('click', function ()
	{
		if (!Admin)
			return;
		
		var data = "";
		data += "modulos/export_xls.php?Terceros_Listado=true&ClienteID="+$("#terceros_listado_cliente_ID").val()+"";
		data += "&VendedorID="+$("#terceros_listado_vendedor").val()+"&CobradorID="+$("#terceros_listado_cobrador").val()+"";
		data += "&Clasificacion="+$("#terceros_listado_clasificacion").val()+"&Tipo="+$("#terceros_listado_tipo").val()+"";
		data += "&EstadoC="+$("#terceros_listado_estado").val()+"&Garantia="+$("#terceros_listado_garantias").val()+"";
		window.location = data;
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			clearTimeout(Timer2);
			GridSource.data = {
				"Terceros_Listado":true,
				"ClienteID":$("#terceros_listado_cliente_ID").val(),
				"VendedorID":$("#terceros_listado_vendedor").val(),
				"CobradorID":$("#terceros_listado_cobrador").val(),
				"Clasificacion":$("#terceros_listado_clasificacion").val(),
				"Tipo":$("#terceros_listado_tipo").val(),
				"EstadoC":$("#terceros_listado_estado").val(),
				"Garantia":$("#terceros_listado_garantias").val(),
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#terceros_listado_items_grid").jqxGrid({source: GridDataAdapter});
		},350);
	};
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ID', type: 'string' },
			{ name: 'Activo', type: 'bool' },
			{ name: 'Motivo', type: 'string' },
			{ name: 'Clasificacion', type: 'string' },
			{ name: 'Tipo', type: 'string' },
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Direccion', type: 'string' },
			{ name: 'Telefono', type: 'string' },
			{ name: 'Barrio', type: 'string' },
			{ name: 'Ciudad', type: 'string' },
			{ name: 'Departamento', type: 'string' },
			{ name: 'ContactoCP', type: 'string' },
			{ name: 'TelefonoCP', type: 'string' },
			{ name: 'TipoDoc', type: 'string' },
			{ name: 'VendedorID', type: 'string' },
			{ name: 'CobradorID', type: 'string' },
			{ name: 'Terminos', type: 'string' },
			{ name: 'ListaP', type: 'string' },
			{ name: 'CupoCR', type: 'decimal' },
			{ name: 'CupoActivo', type: 'bool' },
			{ name: 'Vigencia', type: 'date' },
		],
		url: "modulos/datos.php",
		updaterow: function (rowid, rowdata, commit)
		{
			$.ajax({
				dataType: "json",
				url: "modulos/guardar.php",
				data: {
					"Terceros_Listado":true,
					"ID":rowdata.ID,
					"Activo":rowdata.Activo,
					"Motivo":rowdata.Motivo,
					"Clasificacion":rowdata.Clasificacion,
					"Tipo":rowdata.Tipo,
					"ClienteID":rowdata.ClienteID,
					"Nombre":rowdata.Nombre,
					"Direccion":rowdata.Direccion,
					"Telefono":rowdata.Telefono,
					"Barrio":rowdata.Barrio,
					"Ciudad":rowdata.Ciudad,
					"Departamento":rowdata.Departamento,
					"ContactoCP":rowdata.ContactoCP,
					"TelefonoCP":rowdata.TelefonoCP,
					"TipoDoc":rowdata.TipoDoc,
					"VendedorID":rowdata.VendedorID,
					"CobradorID":rowdata.CobradorID,
					"Terminos":rowdata.Terminos,
					"ListaP":rowdata.ListaP,
					"CupoCR":rowdata.CupoCR,
					"CupoActivo":rowdata.CupoActivo,
					"Vigencia":GetFormattedDate(rowdata.Vigencia),
				},
				async: true,
				success: function (data, status, xhr)
				{
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							commit(true);
						break;
						
						case "EXIST":
							commit(false);
							Alerts_Box("No es posible guardar cambios debido a que ya existe otro tercero con el mismo ID.", 4);
						break;
						
						case "ERROR":
							commit(false);
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
						break;
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					Alerts_Box("Ocurrió un Error al Intentar Actualizar los Datos.<br />Contacte al Soporte Tecnico.<br />"
					+"Error: "+errorThrown, 3);
					commit(false);
				}
			});
		},
	};
	
	var TipoCEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({
			selectedIndex: -1,
			displayMember: 'Tipo',
			valueMember: 'Tipo',
		});
	};
	
	var TipoIEditor = function(row, cellValue, editor, cellText, width, height)
	{
		var value = $("#terceros_listado_items_grid").jqxGrid('getcellvalue', row, "Clasificacion");
		TipoValue = [];
		var len = TipoArray.length;
		for (i = 0; i < len; i++)
		{
			if (TipoArray[i]["Clasificacion"] == value)
				TipoValue.push(TipoArray[i]["Tipo"])
		}
		editor.jqxComboBox({ source: TipoValue });
		var item = editor.jqxComboBox('getItemByValue', cellValue);
		if (item != undefined)
			editor.jqxComboBox({ selectedIndex: item.index });
		else
			editor.jqxComboBox({ selectedIndex: -1 });
	};
	
	var DepartamentoCEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({
			selectedIndex: -1,
			displayMember: 'Departamento',
			valueMember: 'Departamento',
		});
	};
	
	var DepartamentoIEditor = function(row, cellValue, editor, cellText, width, height)
	{
		var value = $("#terceros_listado_items_grid").jqxGrid('getcellvalue', row, "Ciudad");
		DepartamentoValue = [];
		var len = DepartamentoArray.length;
		for (i = 0; i < len; i++)
		{
			if (DepartamentoArray[i]["Ciudad"] == value)
				DepartamentoValue.push(DepartamentoArray[i]["Departamento"]);
		}
		editor.jqxComboBox({ source: DepartamentoValue });
		var item = editor.jqxComboBox('getItemByValue', cellValue);
		if (item != undefined)
			editor.jqxComboBox({ selectedIndex: item.index });
		else
			editor.jqxComboBox({ selectedIndex: -1 });
	};
	
	$("#terceros_listado_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		enablebrowserselection: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		sortable: true,
		pageable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{ text: '', datafield: 'ID', editable: false },
			{
				text: 'Activo',
				datafield: 'Activo',
				width: 50,
				height: 20,
				editable: Admin ? true:false,
				columntype: 'checkbox',
				pinned: true,
			},
			{ text: 'Motivo', datafield: 'Motivo', editable: Admin ? true:Modificar, width: 100, height: 20 },
			{
				text: 'Clasificacion',
				datafield: 'Clasificacion',
				width: 100,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'combobox',
				createeditor: function(row, cellValue, editor, cellText, width, height) {
					editor.jqxComboBox({
						source: ClasificacionArray,
						selectedIndex: -1,
						displayMember: 'Clasificacion',
						valueMember: 'Clasificacion',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "" || newvalue == oldvalue) return oldvalue;
					var len = ClasificacionArray.length;
					for (i = 0; i < len; i++)
					{
						if (ClasificacionArray[i]["Clasificacion"] == newvalue) {
							$("#terceros_listado_items_grid").jqxGrid('setcellvalue', row, "Tipo", "");
							return;
						}
					}
					return oldvalue;
				}
			},
			{
				text: 'Tipo',
				datafield: 'Tipo',
				width: 100,
				height: 20,
				editable: Admin ? true:Modificar, 
				columntype: 'combobox',
				createeditor: TipoCEditor,
				initeditor: TipoIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (TipoValue.indexOf(newvalue) < 0 || newvalue == "" || newvalue == oldvalue)
						return oldvalue;
				}
			},
			{ text: 'ID', datafield: 'ClienteID', editable: Admin, width: 100, height: 20, pinned: true, },
			{ text: 'Nombre', datafield: 'Nombre', editable: Admin ? true:Modificar, width: 200, height: 20, pinned: true, },
			{ text: 'Direccion', datafield: 'Direccion', editable: Admin ? true:Modificar, width: 200, height: 20 },
			{ text: 'Telefono', datafield: 'Telefono', editable: Admin ? true:Modificar, width: 100, height: 20 },
			{
				text: 'Barrio',
				datafield: 'Barrio',
				width: 120,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'combobox',
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: BarrioData,
						dropDownWidth: 140,
						selectedIndex: -1,
						displayMember: 'Barrio',
						valueMember: 'Barrio',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
					var len = BarrioData.length;
					for (i = 0; i < len; i++)
					{
						if (BarrioData[i]["Barrio"] == newvalue)
							return;
					}
					return oldvalue;
				}
			},
			{
				text: 'Ciudad',
				datafield: 'Ciudad',
				width: 120,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'combobox',
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: CiudadData,
						dropDownWidth: 140,
						selectedIndex: -1,
						displayMember: 'Ciudad',
						valueMember: 'Ciudad',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "" || newvalue == oldvalue) return oldvalue;
					var len = CiudadData.length;
					for (i = 0; i < len; i++)
					{
						if (CiudadData[i]["Ciudad"] == newvalue) {
							$("#terceros_listado_items_grid").jqxGrid('setcellvalue', row, "Departamento", "");
							return;
						}
					}
					return oldvalue;
				}
			},
			{
				text: 'Departamento',
				datafield: 'Departamento',
				width: 120,
				height: 20,
				editable: Admin ? true:Modificar, 
				columntype: 'combobox',
				createeditor: DepartamentoCEditor,
				initeditor: DepartamentoIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (DepartamentoValue.indexOf(newvalue) < 0 || newvalue == "" || newvalue == oldvalue)
						return oldvalue;
				}
			},
			{ text: 'Contacto Ppal.', datafield: 'ContactoCP', editable: Admin ? true:Modificar, width: 150, height: 20 },
			{ text: 'Telefono CP.', datafield: 'TelefonoCP', editable: Admin ? true:Modificar, width: 100, height: 20 },
			{
				text: 'Tipo Soc.',
				datafield: 'TipoDoc',
				width: 60,
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'center',
				columntype: 'combobox',
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: SociedadData,
						dropDownWidth: 80,
						selectedIndex: -1,
						displayMember: 'Tipo_Sociedad',
						valueMember: 'Tipo_Sociedad',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
					var len = SociedadData.length;
					for (i = 0; i < len; i++)
					{
						if (SociedadData[i]["Tipo_Sociedad"] == newvalue)
							return;
					}
					return oldvalue;
				}
			},
			{
				text: 'Vend.',
				datafield: 'VendedorID',
				width: 40,
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'center',
				columntype: 'combobox',
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: VendedorData,
						dropDownWidth: 60,
						selectedIndex: -1,
						displayMember: 'Codigo',
						valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
					var len = VendedorData.length;
					for (i = 0; i < len; i++)
					{
						if (VendedorData[i]["Codigo"] == newvalue)
							return;
					}
					return oldvalue;
				}
			},
			{
				text: 'Cobr.',
				datafield: 'CobradorID',
				width: 40,
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'center',
				columntype: 'combobox',
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: VendedorData,
						dropDownWidth: 60,
						selectedIndex: -1,
						displayMember: 'Codigo',
						valueMember: 'Codigo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
					var len = VendedorData.length;
					for (i = 0; i < len; i++)
					{
						if (VendedorData[i]["Codigo"] == newvalue)
							return;
					}
					return oldvalue;
				}
			},
			{
				text: 'Terminos',
				datafield: 'Terminos',
				width: 60,
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'center',
				columntype: 'combobox',
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: TerminosValues,
						dropDownWidth: 60,
						selectedIndex: -1,
						displayMember: 'Terminos',
						valueMember: 'Terminos',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
					var len = TerminosValues.length;
					for (i = 0; i < len; i++)
					{
						if (TerminosValues[i]["Terminos"] == newvalue)
							return;
					}
					return oldvalue;
				}
			},
			{
				text: 'Lista P',
				datafield: 'ListaP',
				width: 50,
				height: 20,
				editable: Admin ? true:Modificar,
				cellsalign: 'center',
				columntype: 'combobox',
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: ListaValues,
						dropDownWidth: 70,
						selectedIndex: -1,
						displayMember: 'Lista',
						valueMember: 'Lista',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
					var len = ListaValues.length;
					for (i = 0; i < len; i++)
					{
						if (ListaValues[i]["Lista"] == newvalue)
							return;
					}
					return oldvalue;
				}
			},
			{
				text: 'Cupo Credito',
				datafield: 'CupoCR',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18, max: 999999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Vigencia',
				datafield: 'Vigencia',
				width: 80,
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: 'datetimeinput',
				cellsformat: 'dd-MMM-yyyy',
			},
			{
				text: 'Cupo',
				datafield: 'CupoActivo',
				width: 50,
				height: 20,
				editable: Admin ? true:false,
				columntype: 'checkbox',
			},
		]
	});
	$("#terceros_listado_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#terceros_listado_items_grid").jqxGrid('hidecolumn', 'ID');
	
	// Load Initial Values
	LoadValues();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="1" cellspacing="2">
		<tr>
			<td>
				Cliente
			</td>
			<td>
				<div id="terceros_listado_cliente"></div>
			</td>
			<td>
				Cliente ID
			</td>
			<td>
				<div id="terceros_listado_cliente_ID"></div>
			</td>
			<td>
				Vendedor
			</td>
			<td>
				<div id="terceros_listado_vendedor"></div>
			</td>
			<td colspan="2">
				<li class="parte1_li_txt">
					Cobrador &nbsp;
				</li>
				<li>
					<div id="terceros_listado_cobrador"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Clasificacion
			</td>
			<td>
				<li>
					<div id="terceros_listado_clasificacion"></div>
				</li>
				<li class="parte1_li_txt">
					&nbsp;Tipo&nbsp;
				</li>
				<li>
					<div id="terceros_listado_tipo"></div>
				</li>
			</td>
			<td>
				Estado
			</td>
			<td>
				<div id="terceros_listado_estado"></div>
			</td>
			<td>
				Garantias
			</td>
			<td>
				<div id="terceros_listado_garantias"></div>
			</td>
			<td>
				<input type="button" id="terceros_listado_imprimir" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="terceros_listado_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2" style="margin-top:20px;">
	<div id="terceros_listado_items_grid"></div>
</div>

