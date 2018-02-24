<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var ID_Incorrecto = false;
	var Categoria = -1;
	var Grupo = -1;
	var ProveedorID = -1;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Inventario_Content");
	var Body = document.getElementById("Inventario_Crear_Content");
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
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Crear" && $data[$i]["Guardar"] == "false")
				{
		?>
					$("#inventario_crear_guardar").jqxButton({ disabled: true });
					$("#inventario_crear_nuevo").jqxButton({ disabled: true });
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
			{id:"inventario_crear_producto_ID", type:""},
			{id:"inventario_crear_producto_nombre", type:""},
			{id:"inventario_crear_producto_categoria", type:"jqxComboBox"},
			{id:"inventario_crear_producto_grupo", type:"jqxComboBox"},
			{id:"inventario_crear_producto_sub_grupo", type:"jqxComboBox"},
			{id:"inventario_crear_producto_unidad", type:"jqxComboBox"},
			{id:"inventario_crear_costo", type:""},
			{id:"inventario_crear_producto_peso", type:""},
			{id:"inventario_crear_existencia_ini", type:""},
			{id:"inventario_crear_stock_min", type:""},
			{id:"inventario_crear_fac_sin_existencia", type:"jqxCheckBox"},
			{id:"inventario_crear_produccion", type:"jqxCheckBox"},
			{id:"inventario_crear_lista1", type:""},
			{id:"inventario_crear_lista2", type:""},
			{id:"inventario_crear_lista3", type:""},
			{id:"inventario_crear_lista4", type:""},
			{id:"inventario_crear_listap1", type:""},
			{id:"inventario_crear_listap2", type:""},
			{id:"inventario_crear_listap3", type:""},
			{id:"inventario_crear_listap4", type:""},
			{id:"inventario_crear_proveedor_addrow", type:"jqxComboBox"},
			{id:"inventario_crear_proveedorID_addrow", type:"jqxComboBox"},
			{id:"inventario_crear_proveedores", type:"jqxGrid"},
			{id:"inventario_crear_notas", type:""},
		];
		
		EnableDisableJSON = [
			{id:"inventario_crear_producto_ID", type:""},
			{id:"inventario_crear_producto_nombre", type:""},
			{id:"inventario_crear_producto_categoria", type:"jqxComboBox"},
			{id:"inventario_crear_producto_grupo", type:"jqxComboBox"},
			{id:"inventario_crear_producto_sub_grupo", type:"jqxComboBox"},
			{id:"inventario_crear_producto_unidad", type:"jqxComboBox"},
			{id:"inventario_crear_costo", type:"jqxNumberInput"},
			{id:"inventario_crear_producto_peso", type:"jqxNumberInput"},
			{id:"inventario_crear_existencia_ini", type:"jqxNumberInput"},
			{id:"inventario_crear_stock_min", type:"jqxNumberInput"},
			{id:"inventario_crear_fac_sin_existencia", type:"jqxCheckBox"},
			{id:"inventario_crear_produccion", type:"jqxCheckBox"},
			{id:"inventario_crear_lista1", type:"jqxNumberInput"},
			{id:"inventario_crear_lista2", type:"jqxNumberInput"},
			{id:"inventario_crear_lista3", type:"jqxNumberInput"},
			{id:"inventario_crear_lista4", type:"jqxNumberInput"},
			{id:"inventario_crear_listap1", type:"jqxNumberInput"},
			{id:"inventario_crear_listap2", type:"jqxNumberInput"},
			{id:"inventario_crear_listap3", type:"jqxNumberInput"},
			{id:"inventario_crear_listap4", type:"jqxNumberInput"},
			{id:"inventario_crear_proveedor_addrow", type:"jqxComboBox"},
			{id:"inventario_crear_proveedorID_addrow", type:"jqxComboBox"},
			{id:"inventario_crear_addrowbutton", type:"jqxButton"},
			{id:"inventario_crear_deleterowbutton", type:"jqxButton"},
			{id:"inventario_crear_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	
	$("#inventario_crear_producto_ID").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
	});
	$("#inventario_crear_producto_ID").on("change", function (event)
	{
		var value = $("#inventario_crear_producto_ID").val();
		
		var FindSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Same', type: 'bool'},
			],
			type: 'GET',
			data: {
				"Inventario_Check_SameID":value,
			},
			url: "modulos/datos.php",
			async: true,
		};
		var FindDataAdapter = new $.jqx.dataAdapter(FindSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = FindDataAdapter.records;
				if (records[0]["Same"] == true)
				{
					$("#inventario_crear_producto_ID").addClass("jqx-validator-error-element");
					ID_Incorrecto = true;
					
					Alerts_Box("El Codigo de Fabrica Ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
					WaitClick_Input("inventario_crear_producto_ID");
				}
				else
				{
					$("#inventario_crear_producto_ID").removeClass("jqx-validator-error-element");
					ID_Incorrecto = false;
				}
			}
		});
	});
	
	$("#inventario_crear_producto_nombre").jqxInput({
		theme: mytheme,
		height: 20,
		width: 440,
	});
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Categoria', type: 'string'},
		],
		type: 'GET',
		data: {"Inventario_Categoria":true},
		url: "modulos/parametros.php",
		async: true
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#inventario_crear_producto_categoria").jqxComboBox(
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
	$("#inventario_crear_producto_categoria").bind('select', function (event) {
		if (event.args) {
			Categoria = event.args.item.value;
		}
	});
	$("#inventario_crear_producto_categoria").bind('close', function (event) {
		if (Categoria != "" && Categoria != -1 ) {
			$("#inventario_crear_producto_grupo").jqxComboBox({ disabled: false});
			GrupoSource.data = {"Inventario_Grupo": Categoria};
			GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#inventario_crear_producto_grupo").jqxComboBox({source: GrupoAdapter});
		}
	});
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Grupo', type: 'string'}
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#inventario_crear_producto_grupo").jqxComboBox(
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
		disabled: true
	});
	$("#inventario_crear_producto_grupo").bind('select', function (event) {
		if (event.args) {
			Grupo = event.args.item.value;
		}
	});
	
	$("#inventario_crear_producto_grupo").bind('close', function (event) {
		if (Grupo != "" && Grupo != 0) {
			$("#inventario_crear_producto_sub_grupo").jqxComboBox({ disabled: false});
			SubGrupoSource.data = {"Inventario_SubGrupo": Grupo};
			SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#inventario_crear_producto_sub_grupo").jqxComboBox({source: SubGrupoDataAdapter});
		}
	});
	
	var SubGrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubGrupo', type: 'string'}
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#inventario_crear_producto_sub_grupo").jqxComboBox(
	{
		theme: mytheme,
		width: 200,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubGrupo',
		valueMember: 'SubGrupo',
		disabled: true
	});
	
	var UnidadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Unidad', type: 'string'},
		],
		type: 'GET',
		data: {"Inventario_Unidad":true},
		url: "modulos/parametros.php",
		async: true
	};
	var UnidadDataAdapter = new $.jqx.dataAdapter(UnidadSource);
	
	$("#inventario_crear_producto_unidad").jqxComboBox(
	{
		theme: mytheme,
		width: 90,
		height: 20,
		source: UnidadDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Unidad',
		valueMember: 'Unidad'
	});
	
	$("#inventario_crear_costo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 110,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_crear_costo").on('change', function (event) 
	{
		//var value = event.args.value;
		DotheMath();
	});
	
	$("#inventario_crear_producto_peso").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 70,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 6,
	});
	
	$("#inventario_crear_existencia_ini").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 90,
		inputMode: 'simple',
		digits: 9,
		max: 999999999
	});
	
	$("#inventario_crear_stock_min").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 90,
		inputMode: 'simple',
		digits: 9,
		max: 999999999
	});
	
	$("#inventario_crear_fac_sin_existencia").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
	});
	
	$("#inventario_crear_produccion").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
	});
	
	$("#inventario_crear_lista1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
	});
	$("#inventario_crear_lista1").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_crear_lista2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
	});
	$("#inventario_crear_lista2").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_crear_lista3").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
	});
	$("#inventario_crear_lista3").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_crear_lista4").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
	});
	$("#inventario_crear_lista4").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_crear_listap1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_crear_listap1").on('change', function (event) 
	{
		DotheMath(1);
	});

	
	$("#inventario_crear_listap2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_crear_listap2").on('change', function (event) 
	{
		DotheMath(1);
	});
	
	$("#inventario_crear_listap3").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_crear_listap3").on('change', function (event) 
	{
		DotheMath(1);
	});
	
	$("#inventario_crear_listap4").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_crear_listap4").on('change', function (event) 
	{
		DotheMath(1);
	});
	
	function DotheMath (method)
	{
		if (method > 0)
		{
			var value = parseFloat($("#inventario_crear_costo").val());
			var value1 = parseFloat($("#inventario_crear_listap1").val());
			var value2 = parseFloat($("#inventario_crear_listap2").val());
			var value3 = parseFloat($("#inventario_crear_listap3").val());
			var value4 = parseFloat($("#inventario_crear_listap4").val());
			
			var percent1 = (((value1 - value) / value) * 100).toFixed(2);
			if (percent1 < 0)
				percent1 = 0;
			var percent2 = (((value2 - value) / value) * 100).toFixed(2);
			if (percent2 < 0)
				percent2 = 0;
			var percent3 = (((value3 - value) / value) * 100).toFixed(2);
			if (percent3 < 0)
				percent3 = 0;
			var percent4 = (((value4 - value) / value) * 100).toFixed(2);
			if (percent4 < 0)
				percent4 = 0;
			
			$("#inventario_crear_lista1").val(percent1);
			$("#inventario_crear_lista2").val(percent2);
			$("#inventario_crear_lista3").val(percent3);
			$("#inventario_crear_lista4").val(percent4);
		}
		else
		{
			var value = $("#inventario_crear_costo").val();
			var percent1 = $("#inventario_crear_lista1").val();
			var percent2 = $("#inventario_crear_lista2").val();
			var percent3 = $("#inventario_crear_lista3").val();
			var percent4 = $("#inventario_crear_lista4").val();

			var total1 = Math.round(value + (percent1 / 100) * value);
			var total2 = Math.round(value + (percent2 / 100) * value);
			var total3 = Math.round(value + (percent3 / 100) * value);
			var total4 = Math.round(value + (percent4 / 100) * value);
			
			$("#inventario_crear_listap1").val(total1);
			$("#inventario_crear_listap2").val(total2);
			$("#inventario_crear_listap3").val(total3);
			$("#inventario_crear_listap4").val(total4);
		}
	};
	
	//----------------------------------------- PARTE 2
	
	var ProveedorSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Proveedor', type: 'string'},
			{ name: 'ProveedorID', type: 'string'},
		],
		type: 'GET',
		data: {"Clientes_Proveedores":true},
		url: "modulos/datos.php",
		async: true
	};
	var ProveedorDataAdapter = new $.jqx.dataAdapter(ProveedorSource);
	
	$("#inventario_crear_proveedor_addrow").jqxComboBox(
	{
		theme: mytheme,
		width: 190,
		height: 20,
		source: ProveedorDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Proveedor',
		valueMember: 'ProveedorID'
	});
	$("#inventario_crear_proveedor_addrow").bind('select', function (event) {
		if (event.args) {
			ProveedorID = event.args.item.value;
		}
	});
	$("#inventario_crear_proveedor_addrow").bind('close', function (event) {
		if (ProveedorID == -1)
			return;
		$("#inventario_crear_proveedorID_addrow").val(ProveedorID);
	});
	
	$("#inventario_crear_proveedorID_addrow").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ProveedorDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ProveedorID',
		valueMember: 'ProveedorID'
	});
	$("#inventario_crear_proveedorID_addrow").bind('select', function (event) {
		if (event.args) {
			ProveedorID = event.args.item.value;
		}
	});
	$("#inventario_crear_proveedorID_addrow").bind('close', function (event) {
		if (ProveedorID == -1)
			return;
		$("#inventario_crear_proveedor_addrow").val(ProveedorID);
	});
	
	function Add_Row()
	{
		var item = $("#inventario_crear_proveedor_addrow").jqxComboBox('getSelectedItem');
		var datinfo = $("#inventario_crear_proveedores").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		if (count <= 0) {
			var datarow = [{
				"Proveedor":item.label,
				"ProveedorID":ProveedorID,
			}];
			$("#inventario_crear_proveedores").jqxGrid("addrow", null, datarow, "first");
		}
		else {
			var exist = false;
			for (i=0;i<count;i++) {
				var currentRow = $("#inventario_crear_proveedores").jqxGrid('getrowdata', i);
				if (currentRow.ProveedorID == ProveedorID)
				{
					exist = true;
				}
			}
			if (exist == false)
			{
				var datarow = [{
					"Proveedor":item.label,
					"ProveedorID":ProveedorID,
				}];
				$("#inventario_crear_proveedores").jqxGrid("addrow", null, datarow, "first");
			}
		}
	};

	var ProveedoresSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Proveedor', type: 'string' },
			{ name: 'ProveedorID', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit) {
			commit(true);
		},
		deleterow: function (rowid, commit) {
			commit(true);
		},
	};
	var ProveedoresDataAdapter = new $.jqx.dataAdapter(ProveedoresSource);
	
	$("#inventario_crear_proveedores").jqxGrid({
		theme: mytheme,
		width: 330,
		height: 150,
		source: ProveedoresDataAdapter,
		editable: true,
		editmode: 'click',
		showtoolbar: true,
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px; font-size:11px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="inventario_crear_addrowbutton" class="GridButtons" value="Agregar"/>'+
				'<input type="button" id="inventario_crear_deleterowbutton" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#inventario_crear_addrowbutton").jqxButton({theme: mytheme, template: "success"});
			$("#inventario_crear_deleterowbutton").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#inventario_crear_addrowbutton").on('click', function () {
				Add_Row();
			});
			// delete row.
			$("#inventario_crear_deleterowbutton").on('click', function () {
				var selectedrowindex = $("#inventario_crear_proveedores").jqxGrid('getselectedrowindex');
				var rowscount = $("#inventario_crear_proveedores").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#inventario_crear_proveedores").jqxGrid('getrowid', selectedrowindex);
					$("#inventario_crear_proveedores").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Proveedor', datafield: 'Proveedor', editable: false, width: '70%', height: 20 },
			{ text: 'Proveedor ID', datafield: 'ProveedorID', editable: false, width: '30%', height: 20 },
		]
	});
	
	$("#inventario_crear_producto_foto").click(function()
	{
		var img_src = document.getElementById("inventario_crear_producto_fotoID").src;
		var img = "<img src=\""+img_src+"\" alt=\"Imagen del Producto\"/>";
		$("#inventario_crear_show_docs_content").html(img);
		$("#inventario_crear_show_docs_window").jqxWindow('open');
	});
	
	$("#inventario_crear_show_docs_window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 600,
		width: 800,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	
	$("#inventario_crear_producto_foto_delete").click(function() {
		//Clean Img
		var img = "<img id=\"inventario_crear_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/search.png\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
		$("#inventario_crear_producto_foto").html(img);
	});
	
	$("#inventario_crear_producto_foto_edit").click(function() {
		$("#inventario_crear_upload_docs_window").jqxWindow('open');
	});
	
	$("#inventario_crear_upload_docs_window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 160,
		title: "Cargar Archivo",
		width: 300,
		minWidth: 300,
		maxWidth: 400,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			$("#inventario_crear_producto_upload_docs").jqxFileUpload({
				theme: mytheme,
				width: 290,
				multipleFilesUpload: false,
				browseTemplate: 'success',
				uploadTemplate: 'primary',
				cancelTemplate: 'danger',
				uploadUrl: 'modulos/guardar.php',
				fileInputName: 'Image_Uploads',
			});
			$("#inventario_crear_producto_upload_docs").on('uploadEnd', function (event)
			{
				var args = event.args;
				var fileName = args.file;
				var serverResponce = args.response;
				
				if (serverResponce == "OK")
				{
					var Tmp = "Uploads_Tmp/"+fileName;
					var img = "<img id=\"inventario_crear_producto_fotoID\" width=\"150\" height=\"150\" src=\""+Tmp+"\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
					$("#inventario_crear_producto_foto").html(img);
					$("#inventario_crear_upload_docs_window").jqxWindow('close');
				}
				else
				{
					Alerts_Box(""+serverResponce, 3);
					WaitClick_WindowClose("inventario_crear_upload_docs_window");
				}
				
			});
		}
	});
	
	function CrearProducto ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ID_Incorrecto == true) {
			Alerts_Box("El Codigo de Fabrica Ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
			WaitClick_Input("inventario_crear_producto_ID");
			Locked = false;
			return;
		}
	
		if ($("#inventario_crear_producto_ID").val() == "" || $("#inventario_crear_producto_ID").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un Codigo de Producto", 3);
			WaitClick_Input("inventario_crear_producto_ID");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_producto_nombre").val() == "" || $("#inventario_crear_producto_nombre").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un Nombre de Producto", 3);
			WaitClick_Input("inventario_crear_producto_nombre");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_producto_categoria").val() == "" || $("#inventario_crear_producto_categoria").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar una Categoria", 3);
			WaitClick_Combobox("inventario_crear_producto_categoria");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_producto_grupo").val() == "" || $("#inventario_crear_producto_grupo").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar un Grupo", 3);
			WaitClick_Combobox("inventario_crear_producto_grupo");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_producto_sub_grupo").val() == "" || $("#inventario_crear_producto_sub_grupo").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar un SubGrupo", 3);
			WaitClick_Combobox("inventario_crear_producto_sub_grupo");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_producto_unidad").val() == "" || $("#inventario_crear_producto_unidad").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Unidad", 3);
			WaitClick_Combobox("inventario_crear_producto_unidad");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_costo").val() == "" || $("#inventario_crear_costo").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un valor de Costo!", 3);
			WaitClick_NumberInput("inventario_crear_costo");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_existencia_ini").val() < 0)
		{
			Alerts_Box("El valor no puede ser inferior a 1!", 3);
			WaitClick_NumberInput("inventario_crear_existencia_ini");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_stock_min").val() == "" || $("#inventario_crear_stock_min").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un valor de Stock Minimo!", 3);
			WaitClick_NumberInput("inventario_crear_stock_min");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_listap1").val() <= $("#inventario_crear_costo").val())
		{
			Alerts_Box("El Valor del precio de la Lista 1 debe ser Mayor al Costo del Producto.", 3);
			WaitClick_NumberInput("inventario_crear_listap1");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_listap2").val() <= $("#inventario_crear_costo").val())
		{
			Alerts_Box("El Valor del precio de la Lista 2 debe ser Mayor al Costo del Producto.", 3);
			WaitClick_NumberInput("inventario_crear_listap2");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_listap3").val() <= $("#inventario_crear_costo").val())
		{
			Alerts_Box("El Valor del precio de la Lista 3 debe ser Mayor al Costo del Producto.", 3);
			WaitClick_NumberInput("inventario_crear_listap3");
			Locked = false;
			return;
		}
		
		if ($("#inventario_crear_listap1").val() <= $("#inventario_crear_costo").val())
		{
			Alerts_Box("El Valor del precio de la Lista 3 debe ser Mayor al Costo del Producto.", 3);
			WaitClick_NumberInput("inventario_crear_listap3");
			Locked = false;
			return;
		}
		
		var datinfo = $("#inventario_crear_proveedores").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		if (count < 1)
		{
			var array = {};
			var Imagen = GetImage("inventario_crear_producto_fotoID");
			if (Imagen == "images/search.png")
				Imagen = "";
			array["Foto_Producto"] = Imagen;
			array["ProductoID"] = $("#inventario_crear_producto_ID").val();
			array["Producto"] = $("#inventario_crear_producto_nombre").val();
			array["Categoria"] = $("#inventario_crear_producto_categoria").val();
			array["Grupo"] = $("#inventario_crear_producto_grupo").val();
			array["SubGrupo"] = $("#inventario_crear_producto_sub_grupo").val();
			array["Unidad"] = $("#inventario_crear_producto_unidad").val();
			array["Costo"] = $("#inventario_crear_costo").val();
			array["Peso"] = $("#inventario_crear_producto_peso").val();
			array["ExistenciaIni"] = $("#inventario_crear_existencia_ini").val();
			array["Stock"] = $("#inventario_crear_stock_min").val();
			array["Facturar_sin_Existencia"] = $("#inventario_crear_fac_sin_existencia").val();
			array["Produccion"] = $("#inventario_crear_produccion").val();
			array["ListaP1"] = $("#inventario_crear_listap1").val();
			array["ListaP2"] = $("#inventario_crear_listap2").val();
			array["ListaP3"] = $("#inventario_crear_listap3").val();
			array["ListaP4"] = $("#inventario_crear_listap4").val();
			array["Notas"] = $("#inventario_crear_notas").val();
			//
			myarray[0] = array;
		}
		else
		{
			for (var i = 0; i < count; i++)
			{
				var array = {};
				var currentRow = $('#inventario_crear_proveedores').jqxGrid('getrowdata', i);
				
				if (i==0) // static values
				{
					var Imagen = GetImage("inventario_crear_producto_fotoID");
					if (Imagen == "images/search.png")
						Imagen = "";
					array["Foto_Producto"] = Imagen;
					array["ProductoID"] = $("#inventario_crear_producto_ID").val();
					array["Producto"] = $("#inventario_crear_producto_nombre").val();
					array["Categoria"] = $("#inventario_crear_producto_categoria").val();
					array["Grupo"] = $("#inventario_crear_producto_grupo").val();
					array["SubGrupo"] = $("#inventario_crear_producto_sub_grupo").val();
					array["Unidad"] = $("#inventario_crear_producto_unidad").val();
					array["Costo"] = $("#inventario_crear_costo").val();
					array["Peso"] = $("#inventario_crear_producto_peso").val();
					array["ExistenciaIni"] = $("#inventario_crear_existencia_ini").val();
					array["Stock"] = $("#inventario_crear_stock_min").val();
					array["Facturar_sin_Existencia"] = $("#inventario_crear_fac_sin_existencia").val();
					array["Produccion"] = $("#inventario_crear_produccion").val();
					array["ListaP1"] = $("#inventario_crear_listap1").val();
					array["ListaP2"] = $("#inventario_crear_listap2").val();
					array["ListaP3"] = $("#inventario_crear_listap3").val();
					array["ListaP4"] = $("#inventario_crear_listap4").val();
					array["Notas"] = $("#inventario_crear_notas").val();
				}
				array["ProveedorID"] = currentRow.ProveedorID;
				
				myarray[i] = array;
			}
		}
		/*alert(JSON.stringify(myarray))
		Locked = false;
		return;*/
		//---
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'json',
			url: "modulos/guardar.php",
			data: {"Inventario_Crear":myarray},
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				if (data[0]["MESSAGE"] == "OK")
					Alerts_Box("Datos Guardados con Exito!", 2);
				else
					Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
		EnableDisableAll(true);
	};
	
	$("#inventario_crear_guardar").jqxButton({
		width: 130,
		height: 30,
		template: "info"
	});
	$("#inventario_crear_guardar").bind('click', function ()
	{
		CrearProducto();
	});
	
	$("#inventario_crear_nuevo").jqxButton({
		width: 130,
		height: 30,
		template: "success"
	});
	$("#inventario_crear_nuevo").bind('click', function ()
	{
		ClearAll();
		// Clean All Variables
		Categoria = -1;
		Grupo = -1;
		ProveedorID = -1;
		// Clean Img
		var img = "<img id=\"inventario_crear_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/search.png\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
		$("#inventario_crear_producto_foto").html(img);
		
		EnableDisableAll(false);
		if (ClickCANCEL == true)
			ClickCANCEL = false;
	});
	CheckRefresh();
});
</script>

<div id="inventario_crear_upload_docs_window">
	<div style="height:20px; font-size: 16px; color: #707070;">
	</div>
	<div>
		<div id="inventario_crear_producto_upload_docs"></div>
	</div>
</div>
<div id="inventario_crear_show_docs_window">
	<div style="height:15px;">
	</div>
	<div>
		<div id="inventario_crear_show_docs_content"></div>
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="3" cellspacing="1" style="margin-bottom:10px;">
		<tr>
			<td>
				Cod Fabrica
			</td>
			<td>
				<input type="text" id="inventario_crear_producto_ID"/>
			</td>
			<td>
				Nombre
			</td>
			<td colspan="3">
				<input type="text" id="inventario_crear_producto_nombre"/>
			</td>
		</tr>
		<tr>
			<td>
				Categoria
			</td>
			<td>
				<div id="inventario_crear_producto_categoria"></div>
			</td>
			<td>
				Grupo
			</td>
			<td>
				<div id="inventario_crear_producto_grupo"></div>
			</td>
			<td colspan="2">
				<li class="parte1_li_txt">
					Sub Grupo&nbsp;
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_producto_sub_grupo"></div>
				</li>
				<li class="parte1_li_txt">
					Undidad
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_producto_unidad"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Costo
			</td>
			<td>
				<div id="inventario_crear_costo"></div>
			</td>
			<td colspan="4">
				<li class="parte1_li_txt">
					Peso (Kg)
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_producto_peso"></div>
				</li>
				<li class="parte1_li_txt">
					Existencia Ini.
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_existencia_ini"></div>
				</li>
				<li class="parte1_li_txt">
					Stock Min.
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_stock_min"></div>
				</li>
				<li class="parte1_li_txt">
					Fac. sin Exist.&nbsp;
				</li>
				<li>
					<div id="inventario_crear_fac_sin_existencia"></div>
				</li>
				<li class="parte1_li_txt">
					Produccion&nbsp;
				</li>
				<li>
					<div id="inventario_crear_produccion"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td rowspan="2" colspan="2">
				Listas de Precio de Venta
			</td>
			<td colspan="4">
				<li style="padding:0px 80px;">
					Lista 1
				</li>
				<li style="padding:0px 50px;">
					Lista 2
				</li>
				<li style="padding:0px 80px;">
					Lista 3	
				</li>
				<li style="padding:0px 50px;">
					Lista 4
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<li style="padding:0px 3px;">
					<div id="inventario_crear_lista1"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_listap1"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_lista2"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_listap2"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_lista3"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_listap3"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_lista4"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_crear_listap4"></div>
				</li>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td style="padding:0px;">
				<table cellpadding="0" cellspacing="0" style="margin:0px; border: 1px solid #A4BED4; text-align: center;">
					<tr style="background: #E0E9F5">
						<td style="border-bottom: 1px solid #A4BED4;">
							Proveedor
						</td>
						<td style="border-bottom: 1px solid #A4BED4;">
							ID
						</td>
					</tr>
					<tr>
						<td>
							<div id="inventario_crear_proveedor_addrow" style="margin-left:5px;"></div>
						</td>
						<td>
							<div id="inventario_crear_proveedorID_addrow" style="margin-left:5px; margin-right:5px;"></div>
						</td>
					</tr>
				</table>
			</td>
			<td style="padding: 45px 0px 0px 10px;">
				Imagen del Producto
			</td>
			<td colspan="2" style="padding: 45px 0px 0px 0px;">
				Notas
			</td>
		</tr>
		<tr>
			<td>
				<div id="inventario_crear_proveedores"></div>
			</td>
			<td rowspan="5" style="padding-left:10px;">
				<div id="inventario_crear_producto_foto_delete" style="width:16px; height:16px; margin: 0px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/close_white.png'" onmouseout="this.src='images/close_black.png'" width="16" height="16" src="images/close_black.png" alt="Eliminar Imagen"/>
				</div>
				<div id="inventario_crear_producto_foto_edit" style="width:16px; height:16px; margin: 0px 0px 0px 20px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/edit_white.png'" onmouseout="this.src='images/edit_black.png'" width="16" height="16" src="images/edit_black.png" alt="Cambiar Imagen"/>
				</div>
				<div id="inventario_crear_producto_foto" style="cursor:pointer;">
					<img id="inventario_crear_producto_fotoID" width="150" height="150" src="images/search.png" alt="Imagen del Producto" style="margin: 0px 10px 0px 0px;"/>
				</div>
			</td>
			<td>
				<textarea rows="10" cols="28" id="inventario_crear_notas" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td style="padding-left:10px; padding-top: 90px; list-style:none;">
				<li style="margin-bottom:10px;">
					<input type="button" id="inventario_crear_guardar" value="Guardar"/>
				</li>
				<li>
					<input type="button" id="inventario_crear_nuevo" value="Nuevo"/>
				</li>
			</td>
		</tr>
	</table>
</div>