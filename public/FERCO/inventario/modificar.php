<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	var Grupo = "";
	var SubGrupo = "";
	var ProveedorID = -1;
	var MaxSteps = 0;
	var Timer1 = 0;
	var Timer2 = 0;
	var FinishTime = false;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Inventario_Content");
	var Body = document.getElementById("Inventario_Modificar_Content");
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
				SystemMap("Modificar", true);
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
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Modificar" && $data[$i]["Guardar"] == "false")
				{
		?>
					$("#inventario_modificar_guardar").jqxButton({ disabled: true });
					$("#inventario_modificar_nuevo").jqxButton({ disabled: true });
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
			{id:"inventario_modificar_producto_ID", type:""},
			{id:"inventario_modificar_producto_nombre", type:""},
			{id:"inventario_modificar_producto_categoria", type:"jqxComboBox"},
			{id:"inventario_modificar_producto_grupo", type:"jqxComboBox"},
			{id:"inventario_modificar_producto_sub_grupo", type:"jqxComboBox"},
			{id:"inventario_modificar_producto_unidad", type:"jqxComboBox"},
			{id:"inventario_modificar_crear_costo", type:""},
			{id:"inventario_modificar_producto_peso", type:""},
			{id:"inventario_modificar_stock_min", type:""},
			{id:"inventario_modificar_fac_sin_existencia", type:"jqxCheckBox"},
			{id:"inventario_modificar_produccion", type:"jqxCheckBox"},
			{id:"inventario_modificar_lista1", type:""},
			{id:"inventario_modificar_lista2", type:""},
			{id:"inventario_modificar_lista3", type:""},
			{id:"inventario_modificar_lista4", type:""},
			{id:"inventario_modificar_listap1", type:""},
			{id:"inventario_modificar_listap2", type:""},
			{id:"inventario_modificar_listap3", type:""},
			{id:"inventario_modificar_listap4", type:""},
			{id:"inventario_modificar_proveedor_addrow", type:"jqxComboBox"},
			{id:"inventario_modificar_proveedorID_addrow", type:"jqxComboBox"},
			{id:"inventario_modificar_proveedores", type:"jqxGrid"},
			{id:"inventario_modificar_notas", type:""},
		];
		
		EnableDisableJSON = [
			{id:"inventario_modificar_producto_ID", type:""},
			{id:"inventario_modificar_producto_nombre", type:""},
			{id:"inventario_modificar_producto_categoria", type:"jqxComboBox"},
			{id:"inventario_modificar_producto_grupo", type:"jqxComboBox"},
			{id:"inventario_modificar_producto_sub_grupo", type:"jqxComboBox"},
			{id:"inventario_modificar_producto_unidad", type:"jqxComboBox"},
			{id:"inventario_modificar_crear_costo", type:"jqxNumberInput"},
			{id:"inventario_modificar_producto_peso", type:"jqxNumberInput"},
			{id:"inventario_modificar_stock_min", type:"jqxNumberInput"},
			{id:"inventario_modificar_fac_sin_existencia", type:"jqxCheckBox"},
			{id:"inventario_modificar_produccion", type:"jqxCheckBox"},
			{id:"inventario_modificar_lista1", type:"jqxNumberInput"},
			{id:"inventario_modificar_lista2", type:"jqxNumberInput"},
			{id:"inventario_modificar_lista3", type:"jqxNumberInput"},
			{id:"inventario_modificar_lista4", type:"jqxNumberInput"},
			{id:"inventario_modificar_listap1", type:"jqxNumberInput"},
			{id:"inventario_modificar_listap2", type:"jqxNumberInput"},
			{id:"inventario_modificar_listap3", type:"jqxNumberInput"},
			{id:"inventario_modificar_listap4", type:"jqxNumberInput"},
			{id:"inventario_modificar_proveedor_addrow", type:"jqxComboBox"},
			{id:"inventario_modificar_proveedorID_addrow", type:"jqxComboBox"},
			{id:"inventario_modificar_addrowbutton", type:"jqxButton"},
			{id:"inventario_modificar_deleterowbutton", type:"jqxButton"},
			{id:"inventario_modificar_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	
	$("#inventario_modificar_producto_ID").jqxInput({
		theme: mytheme,
		height: 20,
		width: 110,
		disabled: true
	});
	
	$("#inventario_modificar_producto_nombre").jqxInput({
		theme: mytheme,
		height: 20,
		width: 425,
	});
	
	$("#inventario_modificar_fecha_modificacion").jqxDateTimeInput({
		theme: mytheme,
		width: 135,
		height: 20,
		showCalendarButton: false,
		formatString: 'dd-MMM-yyyy HH:mm:ss',
		culture: 'es-ES',
		disabled: true
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
	
	$("#inventario_modificar_producto_categoria").jqxComboBox(
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
	$("#inventario_modificar_producto_categoria").bind('change', function (event)
	{
		if (event.args)
		{
			GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
			var GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#inventario_modificar_producto_grupo").jqxComboBox({source: GrupoAdapter});
		}
		else
		{
			var item_value = $("#inventario_modificar_producto_categoria").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#inventario_modificar_producto_categoria").jqxComboBox('clearSelection');
				$("#inventario_modificar_producto_grupo").jqxComboBox('clearSelection');
				$("#inventario_modificar_producto_grupo").jqxComboBox('clear');
				$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clearSelection');
				$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clear');
			}
			else
			{
				var value = $("#inventario_modificar_producto_categoria").val();
				var item = $("#inventario_modificar_producto_categoria").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#inventario_modificar_producto_categoria").jqxComboBox('clearSelection');
					$("#inventario_modificar_producto_grupo").jqxComboBox('clearSelection');
					$("#inventario_modificar_producto_grupo").jqxComboBox('clear');
					$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clearSelection');
					$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clear');
				}
				else
					$("#inventario_modificar_producto_categoria").jqxComboBox('selectItem', item.value);
			}
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
	
	$("#inventario_modificar_producto_grupo").jqxComboBox(
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
	});
	$("#inventario_modificar_producto_grupo").bind('change', function (event)
	{
		if (event.args)
		{
			SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
			var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#inventario_modificar_producto_sub_grupo").jqxComboBox({source: SubGrupoDataAdapter});
		}
		else
		{
			var item_value = $("#inventario_modificar_producto_grupo").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#inventario_modificar_producto_grupo").jqxComboBox('clearSelection');
				$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clearSelection');
				$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clear');
			}
			else
			{
				var value = $("#inventario_modificar_producto_grupo").val();
				var item = $("#inventario_modificar_producto_grupo").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#inventario_modificar_producto_grupo").jqxComboBox('clearSelection');
					$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clearSelection');
					$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clear');
				}
				else
					$("#inventario_modificar_producto_grupo").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#inventario_modificar_producto_grupo").bind('bindingComplete', function (event)
	{
		if (Grupo != "")
		{
			$("#inventario_modificar_producto_grupo").jqxComboBox('selectItem', Grupo);
			Grupo = "";
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
	
	$("#inventario_modificar_producto_sub_grupo").jqxComboBox(
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
	});
	$("#inventario_modificar_producto_sub_grupo").bind('change', function (event)
	{
		if (!event.args)
			$("#inventario_modificar_producto_sub_grupo").jqxComboBox('clearSelection');
	});
	$("#inventario_modificar_producto_sub_grupo").bind('bindingComplete', function (event)
	{
		if (SubGrupo != "")
		{
			$("#inventario_modificar_producto_sub_grupo").jqxComboBox('selectItem', SubGrupo);
			SubGrupo = "";
		}
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
	
	$("#inventario_modificar_producto_unidad").jqxComboBox(
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
	
	$("#inventario_modificar_crear_costo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 110,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_modificar_crear_costo").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_modificar_crear_ult_costo").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 110,
		symbol: '$',
		digits: 9,
		max: 999999999,
		disabled: true
	});
	
	$("#inventario_modificar_crear_costo_prom").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 110,
		symbol: '$',
		digits: 9,
		max: 999999999,
		disabled: true
	});
	
	$("#inventario_modificar_producto_peso").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 70,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: 'Kg',
		symbolPosition: 'right',
		digits: 6,
	});
	
	$("#inventario_modificar_stock_min").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 90,
		inputMode: 'simple',
		digits: 9,
		max: 999999999
	});
	
	$("#inventario_modificar_fac_sin_existencia").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
	});
	
	$("#inventario_modificar_produccion").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
	});
	
	$("#inventario_modificar_lista1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
	});
	$("#inventario_modificar_lista1").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_modificar_lista2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
	});
	$("#inventario_modificar_lista2").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_modificar_lista3").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
	});
	$("#inventario_modificar_lista3").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_modificar_lista4").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 55,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
	});
	$("#inventario_modificar_lista4").on('change', function (event) 
	{
		DotheMath();
	});
	
	$("#inventario_modificar_listap1").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_modificar_listap1").on('change', function (event) 
	{
		DotheMath(1);
	});

	
	$("#inventario_modificar_listap2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_modificar_listap2").on('change', function (event) 
	{
		DotheMath(1);
	});
	
	$("#inventario_modificar_listap3").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_modificar_listap3").on('change', function (event) 
	{
		DotheMath(1);
	});
	
	$("#inventario_modificar_listap4").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 100,
		symbol: '$',
		digits: 9,
		max: 999999999
	});
	$("#inventario_modificar_listap4").on('change', function (event) 
	{
		DotheMath(1);
	});
	
	function DotheMath (method)
	{
		if (method > 0)
		{
			var value = $("#inventario_modificar_crear_costo").val();
			var value1 = $("#inventario_modificar_listap1").val();
			var value2 = $("#inventario_modificar_listap2").val();
			var value3 = $("#inventario_modificar_listap3").val();
			var value4 = $("#inventario_modificar_listap4").val();
			
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
			
			$("#inventario_modificar_lista1").val(percent1);
			$("#inventario_modificar_lista2").val(percent2);
			$("#inventario_modificar_lista3").val(percent3);
			$("#inventario_modificar_lista4").val(percent4);
		}
		else
		{
			var value = $("#inventario_modificar_crear_costo").val();
			var percent1 = $("#inventario_modificar_lista1").val();
			var percent2 = $("#inventario_modificar_lista2").val();
			var percent3 = $("#inventario_modificar_lista3").val();
			var percent4 = $("#inventario_modificar_lista4").val();

			var total1 = Math.round(value + ((parseFloat(percent1) / 100) * parseFloat(value)));
			var total2 = Math.round(value + ((parseFloat(percent2) / 100) * parseFloat(value)));
			var total3 = Math.round(value + ((parseFloat(percent3) / 100) * parseFloat(value)));
			var total4 = Math.round(value + ((parseFloat(percent4) / 100) * parseFloat(value)));
			
			$("#inventario_modificar_listap1").val(total1);
			$("#inventario_modificar_listap2").val(total2);
			$("#inventario_modificar_listap3").val(total3);
			$("#inventario_modificar_listap4").val(total4);
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
	
	$("#inventario_modificar_proveedor_addrow").jqxComboBox(
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
	$("#inventario_modificar_proveedor_addrow").bind('select', function (event) {
		if (event.args) {
			ProveedorID = event.args.item.value;
		}
	});
	$("#inventario_modificar_proveedor_addrow").bind('close', function (event) {
		if (ProveedorID == -1)
			return;
		$("#inventario_modificar_proveedorID_addrow").val(ProveedorID);
	});
	
	$("#inventario_modificar_proveedorID_addrow").jqxComboBox(
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
	$("#inventario_modificar_proveedorID_addrow").bind('select', function (event) {
		if (event.args) {
			ProveedorID = event.args.item.value;
		}
	});
	$("#inventario_modificar_proveedorID_addrow").bind('close', function (event) {
		if (ProveedorID == -1)
			return;
		$("#inventario_modificar_proveedor_addrow").val(ProveedorID);
	});
	
	function Add_Row()
	{
		var item = $("#inventario_modificar_proveedor_addrow").jqxComboBox('getSelectedItem');
		var datinfo = $("#inventario_modificar_proveedores").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		if (count <= 0) {
			var datarow = [{
				"Proveedor":item.label,
				"ProveedorID":ProveedorID,
			}];
			$("#inventario_modificar_proveedores").jqxGrid("addrow", null, datarow, "first");
		}
		else {
			var exist = false;
			for (i=0;i<count;i++) {
				var currentRow = $("#inventario_modificar_proveedores").jqxGrid('getrowdata', i);
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
				$("#inventario_modificar_proveedores").jqxGrid("addrow", null, datarow, "first");
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
	
	$("#inventario_modificar_proveedores").jqxGrid({
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
				'<input type="button" id="inventario_modificar_addrowbutton" class="GridButtons" value="Agregar"/>'+
				'<input type="button" id="inventario_modificar_deleterowbutton" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#inventario_modificar_addrowbutton").jqxButton({theme: mytheme, template: "success"});
			$("#inventario_modificar_deleterowbutton").jqxButton({theme: mytheme, template: "danger"});
			// create new row.
			$("#inventario_modificar_addrowbutton").on('click', function () {
				Add_Row();
			});
			// delete row.
			$("#inventario_modificar_deleterowbutton").on('click', function () {
				var selectedrowindex = $("#inventario_modificar_proveedores").jqxGrid('getselectedrowindex');
				var rowscount = $("#inventario_modificar_proveedores").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#inventario_modificar_proveedores").jqxGrid('getrowid', selectedrowindex);
					$("#inventario_modificar_proveedores").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: 'Proveedor', datafield: 'Proveedor', editable: false, width: '70%', height: 20 },
			{ text: 'Proveedor ID', datafield: 'ProveedorID', editable: false, width: '30%', height: 20 },
		]
	});
	
	$("#inventario_modificar_producto_foto").click(function()
	{
		var img_src = document.getElementById("inventario_modificar_producto_fotoID").src;
		var img = "<img src=\""+img_src+"\" alt=\"Imagen del Producto\"/>";
		$("#inventario_modificar_show_docs_content").html(img);
		$("#inventario_modificar_show_docs_window").jqxWindow('open');
	});
	
	$("#inventario_modificar_show_docs_window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 600,
		width: 800,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
	});
	
	$("#inventario_modificar_producto_foto_delete").click(function() {
		//Clean Img
		var img = "<img id=\"inventario_modificar_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/search.png\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
		$("#inventario_modificar_producto_foto").html(img);
	});
	
	$("#inventario_modificar_producto_foto_edit").click(function() {
		$("#inventario_modificar_upload_docs_window").jqxWindow('open');
	});
	
	$("#inventario_modificar_upload_docs_window").jqxWindow({
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
			$("#inventario_modificar_producto_upload_docs").jqxFileUpload({
				theme: mytheme,
				width: 290,
				multipleFilesUpload: false,
				browseTemplate: 'success',
				uploadTemplate: 'primary',
				cancelTemplate: 'danger',
				uploadUrl: 'modulos/guardar.php',
				fileInputName: 'Image_Uploads',
			});
			$("#inventario_modificar_producto_upload_docs").on('uploadEnd', function (event)
			{
				var args = event.args;
				var fileName = args.file;
				var serverResponce = args.response;
				
				if (serverResponce == "OK")
				{
					var Tmp = "Uploads_Tmp/"+fileName;
					var img = "<img id=\"inventario_modificar_producto_fotoID\" width=\"150\" height=\"150\" src=\""+Tmp+"\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
					$("#inventario_modificar_producto_foto").html(img);
					$("#inventario_modificar_upload_docs_window").jqxWindow('close');
				}
				else
				{
					Alerts_Box(""+serverResponce, 3);
					WaitClick_WindowClose("inventario_modificar_upload_docs_window");
				}
				
			});
		}
	});
	
	function CrearProducto ()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if ($("#inventario_modificar_producto_ID").val() == "" || $("#inventario_modificar_producto_ID").val() < 1)
		{
			Alerts_Box("Debe Ingresar un Codigo de Producto", 3);
			WaitClick_Input("inventario_modificar_producto_ID");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_producto_nombre").val() == "" || $("#inventario_modificar_producto_nombre").val() < 1)
		{
			Alerts_Box("Debe Ingresar un Nombre de Producto", 3);
			WaitClick_Input("inventario_modificar_producto_nombre");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_producto_categoria").val() == "" || $("#inventario_modificar_producto_categoria").val() < 1)
		{
			Alerts_Box("Debe Seleccionar una Categoria", 3);
			WaitClick_Combobox("inventario_modificar_producto_categoria");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_producto_grupo").val() == "" || $("#inventario_modificar_producto_grupo").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar un Grupo", 3);
			WaitClick_Combobox("inventario_modificar_producto_grupo");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_producto_sub_grupo").val() == "" || $("#inventario_modificar_producto_sub_grupo").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar un SubGrupo", 3);
			WaitClick_Combobox("inventario_modificar_producto_sub_grupo");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_producto_unidad").val() == "" || $("#inventario_modificar_producto_unidad").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Unidad", 3);
			WaitClick_Combobox("inventario_modificar_producto_unidad");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_crear_costo").val() == "" || $("#inventario_modificar_crear_costo").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un valor de Costo!", 3);
			WaitClick_NumberInput("inventario_modificar_crear_costo");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_stock_min").val() == "" || $("#inventario_modificar_stock_min").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un valor de Stock Minimo!", 3);
			WaitClick_NumberInput("inventario_modificar_stock_min");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_listap1").val() <= $("#inventario_modificar_crear_costo").val())
		{
			Alerts_Box("El Valor del precio de la Lista 1 debe ser Mayor al Costo del Producto.", 3);
			WaitClick_NumberInput("inventario_modificar_listap1");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_listap2").val() <= $("#inventario_modificar_crear_costo").val())
		{
			Alerts_Box("El Valor del precio de la Lista 2 debe ser Mayor al Costo del Producto.", 3);
			WaitClick_NumberInput("inventario_modificar_listap2");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_listap3").val() <= $("#inventario_modificar_crear_costo").val())
		{
			Alerts_Box("El Valor del precio de la Lista 3 debe ser Mayor al Costo del Producto.", 3);
			WaitClick_NumberInput("inventario_modificar_listap3");
			Locked = false;
			return;
		}
		
		if ($("#inventario_modificar_listap1").val() <= $("#inventario_modificar_crear_costo").val())
		{
			Alerts_Box("El Valor del precio de la Lista 3 debe ser Mayor al Costo del Producto.", 3);
			WaitClick_NumberInput("inventario_modificar_listap3");
			Locked = false;
			return;
		}
		
		var datinfo = $("#inventario_modificar_proveedores").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		if (count < 1)
		{
			var array = {};
			var Imagen = GetImage("inventario_modificar_producto_fotoID");
			if (Imagen == "images/search.png")
				Imagen = "";
			array["Foto_Producto"] = Imagen;
			array["ProductoID"] = $("#inventario_modificar_producto_ID").val();
			array["Producto"] = $("#inventario_modificar_producto_nombre").val();
			array["Categoria"] = $("#inventario_modificar_producto_categoria").val();
			array["Grupo"] = $("#inventario_modificar_producto_grupo").val();
			array["SubGrupo"] = $("#inventario_modificar_producto_sub_grupo").val();
			array["Unidad"] = $("#inventario_modificar_producto_unidad").val();
			array["Costo"] = $("#inventario_modificar_crear_costo").val();
			array["Peso"] = $("#inventario_modificar_producto_peso").val();
			array["Stock"] = $("#inventario_modificar_stock_min").val();
			array["Facturar_sin_Existencia"] = $("#inventario_modificar_fac_sin_existencia").val();
			array["Produccion"] = $("#inventario_modificar_produccion").val();
			array["ListaP1"] = $("#inventario_modificar_listap1").val();
			array["ListaP2"] = $("#inventario_modificar_listap2").val();
			array["ListaP3"] = $("#inventario_modificar_listap3").val();
			array["ListaP4"] = $("#inventario_modificar_listap4").val();
			array["Notas"] = $("#inventario_modificar_notas").val();
			array["ProveedorID"] = "";
			//
			myarray[0] = array;
		}
		else
		{
			for (var i = 0; i < count; i++)
			{
				var array = {};
				var currentRow = $("#inventario_modificar_proveedores").jqxGrid('getrowdata', i);
				
				if (i==0) // static values
				{
					var Imagen = GetImage("inventario_modificar_producto_fotoID");
					if (Imagen == "images/search.png")
						Imagen = "";
					array["Foto_Producto"] = Imagen;
					array["ProductoID"] = $("#inventario_modificar_producto_ID").val();
					array["Producto"] = $("#inventario_modificar_producto_nombre").val();
					array["Categoria"] = $("#inventario_modificar_producto_categoria").val();
					array["Grupo"] = $("#inventario_modificar_producto_grupo").val();
					array["SubGrupo"] = $("#inventario_modificar_producto_sub_grupo").val();
					array["Unidad"] = $("#inventario_modificar_producto_unidad").val();
					array["Costo"] = $("#inventario_modificar_crear_costo").val();
					array["Peso"] = $("#inventario_modificar_producto_peso").val();
					array["Stock"] = $("#inventario_modificar_stock_min").val();
					array["Facturar_sin_Existencia"] = $("#inventario_modificar_fac_sin_existencia").val();
					array["Produccion"] = $("#inventario_modificar_produccion").val();
					array["ListaP1"] = $("#inventario_modificar_listap1").val();
					array["ListaP2"] = $("#inventario_modificar_listap2").val();
					array["ListaP3"] = $("#inventario_modificar_listap3").val();
					array["ListaP4"] = $("#inventario_modificar_listap4").val();
					array["Notas"] = $("#inventario_modificar_notas").val();
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
			data: {"Inventario_Modificar":myarray},
			async: true,
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
	};
	
	$("#inventario_modificar_guardar").jqxButton({
		width: 130,
		height: 30,
		template: "info"
	});
	$("#inventario_modificar_guardar").bind('click', function ()
	{
		CrearProducto();
	});
	
	$("#inventario_modificar_nuevo").jqxButton({
		width: 130,
		height: 30,
		template: "success"
	});
	$("#inventario_modificar_nuevo").bind('click', function ()
	{
		ClearAll();
		// Clean All Variables
		ProveedorID = -1;
		// Clean Img
		var img = "<img id=\"inventario_modificar_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/search.png\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
		$("#inventario_modificar_producto_foto").html(img);
		
		EnableDisableAll(false);
		if (ClickCANCEL == true)
			ClickCANCEL = false;
	});
	
	// --- SEARCH FILTER
	
	function Steps (TO)
	{
		var items = $("#inventario_modificar_producto_buscar").jqxComboBox('getItems');
		if (!items)
			var len = 0;
		else
			var len = items.length - 1;
		
		var index = $("#inventario_modificar_producto_buscar").jqxComboBox('getSelectedIndex');
		
		if (TO == "next")
		{
			if (index >= len)
				Alerts_Box("Ha llegado al Final de la Busqueda.", 1);
			else
				$("#inventario_modificar_producto_buscar").jqxComboBox('selectIndex',(index + 1));
		}
		else if (TO == "back")
		{
			if (index < 1)
				Alerts_Box("Ha llegado al Principio de la Busqueda.", 1);
			else
				$("#inventario_modificar_producto_buscar").jqxComboBox('selectIndex',(index - 1));
		}
	}
	
	$("#inventario_modificar_back").jqxButton({
		width: 70,
		height: 25,
		template: "info"
	});
	$("#inventario_modificar_back").bind('click', function ()
	{
		Steps("back")
	});
	
	$("#inventario_modificar_next").jqxButton({
		width: 70,
		height: 25,
		template: "info"
	});
	$("#inventario_modificar_next").bind('click', function ()
	{
		Steps("next");
	});
	
	function Filter ()
	{
		clearTimeout(Timer2);
		Timer2 = setTimeout(function()
		{
			ProductoSource.data = {
				"Inventario_Search_Filter": true,
				"Categoria": $("#inventario_modificar_categoria_buscar").val(),
				"Grupo": $("#inventario_modificar_grupo_buscar").val(),
				"SubGrupo": $("#inventario_modificar_subgrupo_buscar").val(),
			};
			ProductoDataAdapter = new $.jqx.dataAdapter(ProductoSource);
			$("#inventario_modificar_producto_buscar").jqxComboBox({source: ProductoDataAdapter});
			$("#inventario_modificar_producto_ID_buscar").jqxComboBox({source: ProductoDataAdapter});
		},300);
	};
	
	$("#inventario_modificar_categoria_buscar").jqxComboBox(
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
	$("#inventario_modificar_categoria_buscar").bind('change', function (event)
	{
		if (event.args)
		{
			GrupoSource.data = {"Inventario_Grupo": event.args.item.value};
			var GrupoAdapter = new $.jqx.dataAdapter(GrupoSource);
			$("#inventario_modificar_grupo_buscar").jqxComboBox({source: GrupoAdapter});
		}
		else
		{
			var item_value = $("#inventario_modificar_categoria_buscar").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#inventario_modificar_categoria_buscar").jqxComboBox('clearSelection');
				$("#inventario_modificar_grupo_buscar").jqxComboBox('clearSelection');
				$("#inventario_modificar_grupo_buscar").jqxComboBox('clear');
				$("#inventario_modificar_subgrupo_buscar").jqxComboBox('clearSelection');
				$("#inventario_modificar_subgrupo_buscar").jqxComboBox('clear');
			}
			else
			{
				var value = $("#inventario_modificar_categoria_buscar").val();
				var item = $("#inventario_modificar_categoria_buscar").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#inventario_modificar_categoria_buscar").jqxComboBox('clearSelection');
					$("#inventario_modificar_grupo_buscar").jqxComboBox('clearSelection');
					$("#inventario_modificar_grupo_buscar").jqxComboBox('clear');
					$("#inventario_modificar_subgrupo_buscar").jqxComboBox('clearSelection');
					$("#inventario_modificar_subgrupo_buscar").jqxComboBox('clear');
				}
				else
					$("#inventario_modificar_categoria_buscar").jqxComboBox('selectItem', item.value);
			}
		}
		Filter();
	});
	
	$("#inventario_modificar_grupo_buscar").jqxComboBox(
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
	});
	$("#inventario_modificar_grupo_buscar").bind('change', function (event)
	{
		if (event.args)
		{
			SubGrupoSource.data = {"Inventario_SubGrupo": event.args.item.value};
			var SubGrupoDataAdapter = new $.jqx.dataAdapter(SubGrupoSource);
			$("#inventario_modificar_subgrupo_buscar").jqxComboBox({source: SubGrupoDataAdapter});
		}
		else
		{
			var item_value = $("#inventario_modificar_grupo_buscar").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#inventario_modificar_grupo_buscar").jqxComboBox('clearSelection');
				$("#inventario_modificar_subgrupo_buscar").jqxComboBox('clearSelection');
				$("#inventario_modificar_subgrupo_buscar").jqxComboBox('clear');
			}
			else
			{
				var value = $("#inventario_modificar_grupo_buscar").val();
				var item = $("#inventario_modificar_grupo_buscar").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#inventario_modificar_grupo_buscar").jqxComboBox('clearSelection');
					$("#inventario_modificar_subgrupo_buscar").jqxComboBox('clearSelection');
					$("#inventario_modificar_subgrupo_buscar").jqxComboBox('clear');
				}
				else
					$("#inventario_modificar_grupo_buscar").jqxComboBox('selectItem', item.value);
			}
		}
		Filter();
	});
	
	$("#inventario_modificar_subgrupo_buscar").jqxComboBox(
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
	$("#inventario_modificar_subgrupo_buscar").on('change', function (event)
	{
		if (!event.args)
			$("#inventario_modificar_subgrupo_buscar").jqxComboBox("clearSelection");
		Filter();
	});
	
	var ProductoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'CodFab', type: 'string'},
			{ name: 'Nombre', type: 'string'}
		],
		url: "modulos/datos_productos.php",
		async: true
	};
	
	$("#inventario_modificar_producto_buscar").jqxComboBox(
	{
		theme: mytheme,
		width: 310,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'CodFab'
	});
	$("#inventario_modificar_producto_buscar").bind('change', function (event)
	{
		if (event.args)
		{
			if ($("#inventario_modificar_producto_ID_buscar").val() != event.args.item.value)
				$("#inventario_modificar_producto_ID_buscar").jqxComboBox('selectItem', event.args.item.value);
		}
		else
		{
			var item_value = $("#inventario_modificar_producto_buscar").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#inventario_modificar_producto_ID_buscar").jqxComboBox('clearSelection');
				$("#inventario_modificar_producto_buscar").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#inventario_modificar_producto_buscar").val();
				
				var item = $("#inventario_modificar_producto_buscar").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#inventario_modificar_producto_buscar").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#inventario_modificar_producto_ID_buscar").jqxComboBox('clearSelection');
				$("#inventario_modificar_producto_buscar").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#inventario_modificar_producto_ID_buscar").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'CodFab',
		valueMember: 'CodFab'
	});
	$("#inventario_modificar_producto_ID_buscar").bind('change', function (event)
	{
		if (event.args)
		{
			clearTimeout(Timer2);
			Timer2 = setTimeout(function()
			{
				if ($("#inventario_modificar_producto_buscar").val() != event.args.item.value)
					$("#inventario_modificar_producto_buscar").jqxComboBox('selectItem', event.args.item.value);
				
				LoadValues();
			},300);
		}
		else
		{
			var item_value = $("#inventario_modificar_producto_ID_buscar").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#inventario_modificar_producto_ID_buscar").jqxComboBox('clearSelection');
				$("#inventario_modificar_producto_buscar").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#inventario_modificar_producto_ID_buscar").val();
				var item = $("#inventario_modificar_producto_ID_buscar").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					$("#inventario_modificar_producto_ID_buscar").jqxComboBox('clearSelection');
					$("#inventario_modificar_producto_buscar").jqxComboBox('clearSelection');
				}
				else
					$("#inventario_modificar_producto_ID_buscar").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#inventario_modificar_producto_ID_buscar").bind('bindingComplete', function (event)
	{
		var item1 = $("#inventario_modificar_categoria_buscar").jqxComboBox('getSelectedItem');
		if (item1)
		{
			var item2 = $("#inventario_modificar_producto_ID_buscar").jqxComboBox('getSelectedItem');
			if (!item2)
				Steps("next");
		}
	});
	
	function LoadValues ()
	{
		var item = $("#inventario_modificar_producto_buscar").jqxComboBox('getSelectedItem');
		if (!item)
			return;
		
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Fecha_Modif', type: 'string'},
				{ name: 'Categoria', type: 'string'},
				{ name: 'Grupo', type: 'string'},
				{ name: 'SubGrupo', type: 'string'},
				{ name: 'Unidad', type: 'string'},
				{ name: 'Costo', type: 'decimal' },
				{ name: 'Ultimo_Costo', type: 'decimal'},
				{ name: 'Costo_Promedio', type: 'decimal' },
				{ name: 'Peso', type: 'decimal' },
				{ name: 'Stock', type: 'decimal'},
				{ name: 'Existencia', type: 'decimal'},
				{ name: 'Facturar_sin_Existencia', type: 'bool'},
				{ name: 'Produccion', type: 'bool' },
				{ name: 'ListaP1', type: 'decimal' },
				{ name: 'ListaP2', type: 'decimal' },
				{ name: 'ListaP3', type: 'decimal'},
				{ name: 'ListaP4', type: 'decimal' },
				{ name: 'Foto_Producto', type: 'string' },
				{ name: 'Notas', type: 'string' },
				//
				{ name: 'Proveedor', type: 'string'},
			],
			data:{"Inventario_Modificar":item.value},
			url: "modulos/datos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				if (records[0]["Foto_Producto"] != "")
				{
					var img = "<img id=\"inventario_modificar_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/"+records[0]["Foto_Producto"]+"\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
					$("#inventario_modificar_producto_foto").html(img);
				}
				else
				{
					var img = "<img id=\"inventario_modificar_producto_fotoID\" width=\"150\" height=\"150\" src=\"images/search.png\" alt=\"Imagen del Producto\" style=\"margin: 0px 10px 0px 0px;\"/>";
					$("#inventario_modificar_producto_foto").html(img);
				}
				
				$("#inventario_modificar_producto_ID").val(item.value);
				$("#inventario_modificar_producto_nombre").val(item.label);
				$("#inventario_modificar_fecha_modificacion").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Modif"])));
				$("#inventario_modificar_producto_categoria").jqxComboBox('selectItem', records[0]["Categoria"]);
				Grupo = records[0]["Grupo"];
				SubGrupo = records[0]["SubGrupo"];
				$("#inventario_modificar_producto_unidad").jqxComboBox('selectItem', records[0]["Unidad"]);
				$("#inventario_modificar_crear_costo").val(records[0]["Costo"]);
				$("#inventario_modificar_crear_ult_costo").val(records[0]["Ultimo_Costo"]);
				$("#inventario_modificar_crear_costo_prom").val(records[0]["Costo_Promedio"]);
				$("#inventario_modificar_producto_peso").val(records[0]["Peso"]);
				$("#inventario_modificar_stock_min").val(records[0]["Stock"]);
				$("#inventario_modificar_fac_sin_existencia").val(records[0]["Facturar_sin_Existencia"]);
				$("#inventario_modificar_produccion").val(records[0]["Produccion"]);
				$("#inventario_modificar_listap1").val(records[0]["ListaP1"]);
				$("#inventario_modificar_listap2").val(records[0]["ListaP2"]);
				$("#inventario_modificar_listap3").val(records[0]["ListaP3"]);
				$("#inventario_modificar_listap4").val(records[0]["ListaP4"]);
				$("#inventario_modificar_notas").val(records[0]["Notas"]);
				// Clear Grid
				$("#inventario_modificar_proveedores").jqxGrid("clear");
				
				if (records[0]["Proveedor"] == "")
					var len = 0;
				else
					var len = records[0]["Proveedor"].length;
					
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"Proveedor":records[0]["Proveedor"][i]["Proveedor"],
						"ProveedorID":records[0]["Proveedor"][i]["ProveedorID"],
					}];
					$("#inventario_modificar_proveedores").jqxGrid("addrow", null, datarow, "first");
				}
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
			downloadComplete: function(edata, textStatus, jqXHR) {
				//alert("Data is Loaded");
			}
		});
	};
	
	Filter();
	CheckRefresh();
});
</script>

<div id="inventario_modificar_upload_docs_window">
	<div style="height:20px; font-size: 16px; color: #707070;">
	</div>
	<div>
		<div id="inventario_modificar_producto_upload_docs"></div>
	</div>
</div>
<div id="inventario_modificar_show_docs_window">
	<div style="height:15px;">
	</div>
	<div>
		<div id="inventario_modificar_show_docs_content"></div>
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="3" cellspacing="1" style="margin:5px 0px 20px 0px;">
		<tr>
			<td rowspan="2" style="text-align:center;">
				<div style="background: #75858A;color: #FFF;font-size: 13px;padding: 5px 5px 0px 5px; height: 25px;">
					FILTRAR POR:
				</div>
			</td>
		</tr>
		<tr>
			<td>
				Categoria
			</td>
			<td>
				<div id="inventario_modificar_categoria_buscar"></div>
			</td>
			<td>
				Grupo
			</td>
			<td>
				<div id="inventario_modificar_grupo_buscar"></div>
			</td>
			<td>
				SubGrupo
			</td>
			<td>
				<div id="inventario_modificar_subgrupo_buscar"></div>
			</td>
		</tr>
		<tr>
			<td rowspan="2">
				<li>
					<input type="button" id="inventario_modificar_back" value="<< Ant."/>
				</li>
				<li style="padding:0px 0px 0px 10px;">
					<input type="button" id="inventario_modificar_next" value="Sig. >>"/>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Nombre
			</td>
			<td colspan="3">
				<div id="inventario_modificar_producto_buscar"></div>
			</td>
			<td>
				Codigo
			</td>
			<td>
				<div id="inventario_modificar_producto_ID_buscar"></div>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 950px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<table cellpadding="3" cellspacing="1" style="margin-bottom:10px;">
		<tr>
			<td>
				Cod Fabrica
			</td>
			<td>
				<input type="text" id="inventario_modificar_producto_ID"/>
			</td>
			<td>
				Nombre
			</td>
			<td>
				<input type="text" id="inventario_modificar_producto_nombre"/>
			</td>
			<td>
				Ultima Modificacion
			</td>
			<td>
				<div id="inventario_modificar_fecha_modificacion"></div>
			</td>
		</tr>
		<tr>
			<td>
				Categoria
			</td>
			<td>
				<div id="inventario_modificar_producto_categoria"></div>
			</td>
			<td>
				Grupo
			</td>
			<td colspan="3">
				<li style="padding:0px 3px 0px 0px;">
					<div id="inventario_modificar_producto_grupo"></div>
				</li>
				<li class="parte1_li_txt">
					Sub Grupo&nbsp;
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_producto_sub_grupo"></div>
				</li>
				<li class="parte1_li_txt">
					&nbsp;Undidad
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_producto_unidad"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Costo
			</td>
			<td>
				<div id="inventario_modificar_crear_costo"></div>
			</td>
			<td colspan="4">
				<li class="parte1_li_txt">
					Ult. Costo
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_crear_ult_costo"></div>
				</li>
				<li class="parte1_li_txt">
					Costo Prom.
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_crear_costo_prom"></div>
				</li>
				<li class="parte1_li_txt">
					Peso (Kg)
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_producto_peso"></div>
				</li>
				<li class="parte1_li_txt">
					Stock Min.
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_stock_min"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<li class="parte1_li_txt">
					Fac. sin Exist.&nbsp;
				</li>
				<li>
					<div id="inventario_modificar_fac_sin_existencia"></div>
				</li>
				<li class="parte1_li_txt">
					Produccion&nbsp;
				</li>
				<li>
					<div id="inventario_modificar_produccion"></div>
				</li>
			</td>
			<td colspan="4">
				<li style="padding:0px 75px;">
					Lista 1
				</li>
				<li style="padding:0px 55px;">
					Lista 2
				</li>
				<li style="padding:0px 80px;">
					Lista 3	
				</li>
				<li style="padding:0px 55px;">
					Lista 4
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				Listas de Precio de Venta
			</td>
			<td colspan="4">
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_lista1"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_listap1"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_lista2"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_listap2"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_lista3"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_listap3"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_lista4"></div>
				</li>
				<li style="padding:0px 3px;">
					<div id="inventario_modificar_listap4"></div>
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
							<div id="inventario_modificar_proveedor_addrow" style="margin-left:5px;"></div>
						</td>
						<td>
							<div id="inventario_modificar_proveedorID_addrow" style="margin-left:5px; margin-right:5px;"></div>
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
				<div id="inventario_modificar_proveedores"></div>
			</td>
			<td rowspan="5" style="padding-left:10px;">
				<div id="inventario_modificar_producto_foto_delete" style="width:16px; height:16px; margin: 0px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/close_white.png'" onmouseout="this.src='images/close_black.png'" width="16" height="16" src="images/close_black.png" alt="Eliminar Imagen"/>
				</div>
				<div id="inventario_modificar_producto_foto_edit" style="width:16px; height:16px; margin: 0px 0px 0px 20px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/edit_white.png'" onmouseout="this.src='images/edit_black.png'" width="16" height="16" src="images/edit_black.png" alt="Cambiar Imagen"/>
				</div>
				<div id="inventario_modificar_producto_foto" style="cursor:pointer;">
					<img id="inventario_modificar_producto_fotoID" width="150" height="150" src="images/search.png" alt="Imagen del Producto" style="margin: 0px 10px 0px 0px;"/>
				</div>
			</td>
			<td>
				<textarea rows="10" cols="28" id="inventario_modificar_notas" maxlength="100" style="resize:none;"></textarea>
			</td>
			<td style="padding-left:10px; padding-top: 90px; list-style:none;">
				<li style="margin-bottom:10px;">
					<input type="button" id="inventario_modificar_guardar" value="Guardar"/>
				</li>
				<li>
					<input type="button" id="inventario_modificar_nuevo" value="Nuevo"/>
				</li>
			</td>
		</tr>
	</table>
</div>