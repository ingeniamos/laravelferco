<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ID_Cliente = -1;
	var ID_Incorrecto = false;
	var ID_Exception = 0;
	var VendedorID = -1;
	var Categoria = -1;
	var Timer1 = 0;
	var Timer2 = 0;
	var UploadID = -1;
	var Clasificacion = "";
	var Tipo = "";
	var Ciudad = "";
	var GrupoArray = new Array();
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Terceros_Content");
	var Body = document.getElementById("Terceros_Modificar_Content");
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
				Filter();
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
	
	var MyDate = new Date();
	var Year = MyDate.getFullYear();
	Day = "31";
	Month = "11";
	
	function ClearDocument()
	{
		ID_Cliente = -1;
		ID_Incorrecto = false;
		ID_Exception = 0;
		VendedorID = -1;
		Categoria = -1;
		UploadID = -1;
		Clasificacion = "";
		Tipo = "";
		Ciudad = "";
		GrupoArray = new Array();
		
		var img = "";
		img = "<img width=\"200\" height=\"200\" src=\"images/search.png\" alt=\"Foto del Cliente\" style=\"margin: 0px 15px;\"/>";
		$("#terceros_modificar_docs").html(img);
		img = "<img id=\"terceros_modificar_cliente_foto_ID\"  width=\"200\" height=\"200\" src=\"images/UserIcon.png\" alt=\"Foto del Cliente\" style=\"margin: 0px 20px;\"/>";
		$("#terceros_modificar_cliente_foto").html(img);
		// Clean Grid
		var datinfo = $("#terceros_modificar_garantias").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		if (count >= 0)
		{
			for (i = 0; i < count; i++)
			{
				var datarow = {};
				var currentRow = $('#terceros_modificar_garantias').jqxGrid('getrowdata', i);
				datarow["Apply"] = false;
				datarow["Garantia"] = currentRow.Garantia;
				datarow["Imagen"] = "";
				datarow["Ok"] = false;
				$("#terceros_modificar_garantias").jqxGrid('updaterow', i, datarow);
			}
		}
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		SetDefaultDate();
	}
	
	function SetDefaultDate ()
	{
		$("#terceros_modificar_vigencia_cupo").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
		$("#terceros_modificar_vigencia_adicional").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
		$("#terceros_modificar_fecha_notas").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	};
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"terceros_modificar_buscador_cliente", type:"jqxComboBox"},
			{id:"terceros_modificar_buscador_cliente_ID", type:"jqxComboBox"},
			{id:"terceros_modificar_buscador_vendedor", type:"jqxComboBox"},
			{id:"terceros_modificar_buscador_tipo_cliente", type:"jqxComboBox"},
			{id:"terceros_modificar_clasificacion", type:"jqxComboBox"},
			{id:"terceros_modificar_tipo", type:"jqxComboBox"},
			{id:"terceros_modificar_grupo", type:"jqxComboBox"},
			{id:"terceros_modificar_cliente", type:""},
			{id:"terceros_modificar_direccion", type:""},
			{id:"terceros_modificar_barrio", type:"jqxComboBox"},
			{id:"terceros_modificar_ciudad", type:"jqxComboBox"},
			{id:"terceros_modificar_departamento", type:""},
			{id:"terceros_modificar_pais", type:""},
			{id:"terceros_modificar_telefono", type:"jqxMaskedInput"},
			{id:"terceros_modificar_fax", type:"jqxMaskedInput"},
			{id:"terceros_modificar_email", type:""},
			{id:"terceros_modificar_email2", type:""},
			{id:"terceros_modificar_tipo_sociedad", type:"jqxComboBox"},
			{id:"terceros_modificar_contacto_Ppal", type:""},
			{id:"terceros_modificar_telefono_cp", type:"jqxMaskedInput"},
			{id:"terceros_modificar_cliente_ID", type:""},
			{id:"terceros_modificar_cliente_ID2", type:""},
			{id:"terceros_modificar_contacto_sec", type:""},
			{id:"terceros_modificar_telefono_cs", type:"jqxMaskedInput"},
			{id:"terceros_modificar_vendedor", type:"jqxComboBox"},
			{id:"terceros_modificar_cobrador", type:"jqxComboBox"},
			{id:"terceros_modificar_terminos", type:"jqxComboBox"},
			{id:"terceros_modificar_cupo_credito", type:""},
			{id:"terceros_modificar_lista_precios", type:"jqxComboBox"},
			{id:"terceros_modificar_digitado_por", type:""},
			//{id:"terceros_modificar_fecha_dig", type:"jqxDateTimeInput"},
			{id:"terceros_modificar_modificado_por", type:""},
			//{id:"terceros_modificar_fecha_modifi", type:"jqxDateTimeInput"},
			//{id:"terceros_modificar_fecha_notas", type:"jqxDateTimeInput"},
			{id:"terceros_modificar_cupo_activado", type:"jqxCheckBox"},
			{id:"terceros_modificar_cupo_activado_por", type:""},
			{id:"terceros_modificar_cartera", type:"jqxComboBox"},
			{id:"terceros_modificar_cupo_adicional", type:""},
			{id:"terceros_modificar_notas", type:""},
		];
		
		EnableDisableJSON = [
			{id:"terceros_modificar_clasificacion", type:"jqxComboBox"},
			{id:"terceros_modificar_tipo", type:"jqxComboBox"},
			{id:"terceros_modificar_grupo", type:"jqxComboBox"},
			{id:"terceros_modificar_cliente", type:""},
			{id:"terceros_modificar_direccion", type:""},
			{id:"terceros_modificar_barrio", type:"jqxComboBox"},
			{id:"terceros_modificar_ciudad", type:"jqxComboBox"},
			{id:"terceros_modificar_departamento", type:""},
			{id:"terceros_modificar_pais", type:""},
			{id:"terceros_modificar_telefono", type:"jqxMaskedInput"},
			{id:"terceros_modificar_fax", type:"jqxMaskedInput"},
			{id:"terceros_modificar_email", type:""},
			{id:"terceros_modificar_email2", type:""},
			{id:"terceros_modificar_tipo_sociedad", type:"jqxComboBox"},
			{id:"terceros_modificar_contacto_Ppal", type:""},
			{id:"terceros_modificar_telefono_cp", type:"jqxMaskedInput"},
			{id:"terceros_modificar_cliente_ID", type:"jqxNumberInput"},
			{id:"terceros_modificar_cliente_ID2", type:"jqxNumberInput"},
			{id:"terceros_modificar_contacto_sec", type:""},
			{id:"terceros_modificar_telefono_cs", type:"jqxMaskedInput"},
			{id:"terceros_modificar_vendedor", type:"jqxComboBox"},
			{id:"terceros_modificar_cobrador", type:"jqxComboBox"},
			{id:"terceros_modificar_terminos", type:"jqxComboBox"},
			{id:"terceros_modificar_cupo_credito", type:"jqxNumberInput"},
			{id:"terceros_modificar_vigencia_cupo", type:"jqxDateTimeInput"},
			{id:"terceros_modificar_lista_precios", type:"jqxComboBox"},
			{id:"terceros_modificar_cupo_activado", type:"jqxCheckBox"},
			{id:"terceros_modificar_cartera", type:"jqxComboBox"},
			{id:"terceros_modificar_cupo_adicional", type:"jqxNumberInput"},
			{id:"terceros_modificar_vigencia_adicional", type:"jqxDateTimeInput"},
			{id:"terceros_modificar_fecha_notas", type:"jqxDateTimeInput"},
			{id:"terceros_modificar_notas", type:""},
			{id:"terceros_modificar_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	
	function LoadValues ()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: 'Clasificacion', type: 'string'},
				{ name: 'Tipo', type: 'string'},
				{ name: 'Grupo', type: 'string'},
				{ name: 'Foto', type: 'string'},
				{ name: 'Nombre', type: 'string'},
				{ name: 'Direccion', type: 'string' },
				{ name: 'Barrio', type: 'string'},
				{ name: 'Ciudad', type: 'string' },
				{ name: 'Departamento', type: 'string' },
				{ name: 'Pais', type: 'string'},
				{ name: 'Telefono', type: 'string'},
				{ name: 'Fax', type: 'string' },
				{ name: 'Email', type: 'string' },
				{ name: 'Email2', type: 'string' },
				{ name: 'Contacto_P', type: 'string'},
				{ name: 'Telefono_CP', type: 'string' },
				{ name: 'Contacto_S', type: 'string' },
				{ name: 'Telefono_CS', type: 'string' },
				{ name: 'Tipo_Sociedad', type: 'string'},
				{ name: 'Tipo_Doc', type: 'string'},
				{ name: 'ClienteID', type: 'string' },
				{ name: 'ClienteID2', type: 'string'},
				{ name: 'VendedorID', type: 'string' },
				{ name: 'CobradorID', type: 'string' },
				{ name: 'Terminos', type: 'string'},
				{ name: 'ListaP', type: 'string' },
				{ name: 'Digitador', type: 'string' },
				{ name: 'Fecha_Dig', type: 'string' },
				{ name: 'Modificador', type: 'string' },
				{ name: 'Fecha_Modif', type: 'string' },
				{ name: 'Fecha_Notas', type: 'string' },
				//
				{ name: 'Cupo_Credito', type: 'decimal'},
				{ name: 'Vigencia_Cupo_Credito', type: 'string' },
				{ name: 'Cupo_Credito_Activado', type: 'bool' },
				{ name: 'Activado_Por', type: 'string' },
				{ name: 'Cupo_Adicional', type: 'decimal' },
				{ name: 'Vigencia_Cupo_Adicional', type: 'string' },
				{ name: 'Estado_Cuenta', type: 'string' },
				{ name: 'Garantia', type: 'string' },
				{ name: 'Notas', type: 'string' },
			],
			type: 'GET',
			data:{"Terceros_Modificar":ID_Cliente},
			url: "modulos/datos.php",
			async: true
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				if (records[0]["Foto"] != "")
					ShowPhoto(records[0]["Foto"], 1);
					
				ID_Exception = records[0]["ClienteID"]+"-"+records[0]["ClienteID2"];
				$("#terceros_modificar_clasificacion").val(records[0]["Clasificacion"]);
				//
				TipoSource.data = {"Terceros_Tipo": records[0]["Clasificacion"]};
				var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource);
				$("#terceros_modificar_tipo").jqxComboBox({source: TipoDataAdapter});
				$("#terceros_modificar_tipo").on('bindingComplete', function (event) {
					$("#terceros_modificar_tipo").val(records[0]["Tipo"]);
				});
				//
				GrupoSource.data = {"Terceros_Grupo": records[0]["Clasificacion"]};
				var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
				$("#terceros_modificar_grupo").jqxComboBox({source: GrupoDataAdapter});
				//
				var len = records[0]["Grupo"].length;
				for (var i = 0; i < len; i++) {
					$("#terceros_modificar_grupo").jqxComboBox('checkItem', records[0]["Grupo"][i]["Grupo"]);
				}
				
				$("#terceros_modificar_cliente").val(records[0]["Nombre"]);
				$("#terceros_modificar_direccion").val(records[0]["Direccion"]);
				$("#terceros_modificar_barrio").val(records[0]["Barrio"]);
				$("#terceros_modificar_ciudad").val(records[0]["Ciudad"]);
				$("#terceros_modificar_departamento").val(records[0]["Departamento"]);
				$("#terceros_modificar_pais").val(records[0]["Pais"]);
				$("#terceros_modificar_telefono").val(records[0]["Telefono"]);
				$("#terceros_modificar_fax").val(records[0]["Fax"]);
				$("#terceros_modificar_email").val(records[0]["Email"]);
				$("#terceros_modificar_email2").val(records[0]["Email2"]);
				$("#terceros_modificar_tipo_sociedad").jqxComboBox('selectItem', records[0]["Tipo_Sociedad"]);
				//$("#terceros_modificar_tipo_sociedad").val(records[0]["Tipo_Sociedad"]);
				$("#terceros_modificar_contacto_Ppal").val(records[0]["Contacto_P"]);
				$("#terceros_modificar_telefono_cp").val(records[0]["Telefono_CP"]);
				$("#terceros_modificar_tipo_id").val(records[0]["Tipo_Doc"]);
				$("#terceros_modificar_cliente_ID").val(records[0]["ClienteID"]);
				$("#terceros_modificar_cliente_ID2").val(records[0]["ClienteID2"]);
				$("#terceros_modificar_contacto_sec").val(records[0]["Contacto_S"]);
				$("#terceros_modificar_telefono_cs").val(records[0]["Telefono_CS"]);
				$("#terceros_modificar_vendedor").val(records[0]["VendedorID"]);
				$("#terceros_modificar_cobrador").val(records[0]["CobradorID"]);
				$("#terceros_modificar_terminos").val(records[0]["Terminos"]);
				$("#terceros_modificar_lista_precios").val(records[0]["ListaP"]);
				$("#terceros_modificar_digitado_por").val(records[0]["Digitador"]);
				$("#terceros_modificar_fecha_dig").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Dig"])));
				$("#terceros_modificar_modificado_por").val(records[0]["Modificador"]);
				$("#terceros_modificar_fecha_modifi").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Modif"])));
				$("#terceros_modificar_fecha_notas").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Notas"])));
				//
				$("#terceros_modificar_cupo_credito").val(records[0]["Cupo_Credito"]);
				$("#terceros_modificar_vigencia_cupo").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Vigencia_Cupo_Credito"])));
				$("#terceros_modificar_cupo_activado").val(records[0]["Cupo_Credito_Activado"]);
				$("#terceros_modificar_cupo_activado_por").val(records[0]["Activado_Por"]);
				$("#terceros_modificar_cupo_adicional").val(records[0]["Cupo_Adicional"]);
				$("#terceros_modificar_vigencia_adicional").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Vigencia_Cupo_Adicional"])));
				$("#terceros_modificar_cartera").val(records[0]["Estado_Cuenta"]);
				$("#terceros_modificar_notas").val(records[0]["Notas"]);
				//
				var len = records[0]["Garantia"].length;
				for (var i = 0; i < len; i++)
				{
					var datinfo = $("#terceros_modificar_garantias").jqxGrid('getdatainformation');
					var count = datinfo.rowscount;
					for (var a = 0; a < count; a++) {
						var value = $("#terceros_modificar_garantias").jqxGrid('getcellvalue', a, "Garantia");
						if (value == records[0]["Garantia"][i]["Garantia"]) {
							var datarow = {};
							datarow["Apply"] = records[0]["Garantia"][i]["Apply"];
							datarow["Garantia"] = records[0]["Garantia"][i]["Garantia"];
							datarow["Imagen"] = records[0]["Garantia"][i]["Imagen"];
							datarow["Ok"] = records[0]["Garantia"][i]["Ok"];
							$("#terceros_modificar_garantias").jqxGrid('updaterow', a, datarow);
						}
					}
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
	
	// --- SEARCH FILTER
	
	function Steps (TO)
	{
		var items = $("#terceros_modificar_buscador_cliente").jqxComboBox('getItems');
		var len = items.length - 1;
		
		var index = $("#terceros_modificar_buscador_cliente").jqxComboBox('getSelectedIndex');
		
		if (TO == "next")
		{
			if (index >= len)
				Alerts_Box("Ha llegado al Final de la Busqueda.", 1);
			else
				$("#terceros_modificar_buscador_cliente").jqxComboBox('selectIndex',(index + 1));
		}
		else if (TO == "back")
		{
			if (index < 1)
				Alerts_Box("Ha llegado al Principio de la Busqueda.", 1);
			else
				$("#terceros_modificar_buscador_cliente").jqxComboBox('selectIndex',(index - 1));
		}
	}
	
	$("#terceros_modificar_back").jqxButton({
		width: 70,
		height: 25,
		template: "info"
	});
	$("#terceros_modificar_back").bind('click', function ()
	{
		Steps("back")
	});
	
	$("#terceros_modificar_next").jqxButton({
		width: 70,
		height: 25,
		template: "info"
	});
	$("#terceros_modificar_next").bind('click', function ()
	{
		Steps("next");
	});
	
	function Filter ()
	{
		ClienteSource.data = {
			"Terceros_Search_Filter": true,
			"VendedorID": $("#terceros_modificar_buscador_vendedor").val(),
			"Tipo": $("#terceros_modificar_buscador_tipo_cliente").val(),
		};
		var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
		$("#terceros_modificar_buscador_cliente").jqxComboBox({source: ClienteDataAdapter});
		$("#terceros_modificar_buscador_cliente_ID").jqxComboBox({source: ClienteDataAdapter});
	};
	
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
	
	$("#terceros_modificar_buscador_vendedor").jqxComboBox(
	{
		theme: mytheme,
		width: 350,
		height: 20,
		source: VendedorDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Vendedor',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo'
	});
	$("#terceros_modificar_buscador_vendedor").bind('change', function (event) {
		if (event.args) {
			VendedorID = event.args.item.value;
		}
		else {
			VendedorID = -1;
			$("#terceros_modificar_buscador_vendedor").jqxComboBox('clearSelection');
			Filter();
		}
	});
	$("#terceros_modificar_buscador_vendedor").bind('select', function (event) {
		if (event.args) {
			VendedorID = event.args.item.value;
		}
	});
	$("#terceros_modificar_buscador_vendedor").bind('close', function (event) {
		if (VendedorID != "" && VendedorID != -1 ) {
			Filter();
		}
	});
	
	var CategoriaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Clasificacion', type: 'string'}
		],
		data: {"Terceros_Clasificacion":true},
		url: "modulos/parametros.php",
		async: true
	};
	var CategoriaDataAdapter = new $.jqx.dataAdapter(CategoriaSource);
	
	$("#terceros_modificar_buscador_tipo_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 140,
		height: 20,
		source: CategoriaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Clasificacion',
		valueMember: 'Clasificacion',
	});
	$("#terceros_modificar_buscador_tipo_cliente").bind('change', function (event) {
		if (event.args) {
			Categoria = event.args.item.value;
		}
		else {
			Categoria = -1;
			$("#terceros_modificar_buscador_tipo_cliente").jqxComboBox('clearSelection');
			Filter();
		}
	});
	$("#terceros_modificar_buscador_tipo_cliente").bind('select', function (event) {
		if (event.args) {
			Categoria = event.args.item.value;
		}
	});
	$("#terceros_modificar_buscador_tipo_cliente").bind('close', function (event) {
		if (Categoria != "" && Categoria != -1 ) {
			Filter();
		}
	});
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		cache: false,
		url: "modulos/datos.php",
	};
	
	$("#terceros_modificar_buscador_cliente").jqxComboBox(
	{
		width: 350,
		height: 20,
		theme: mytheme,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Buscar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#terceros_modificar_buscador_cliente").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				if ($("#terceros_modificar_buscador_cliente_ID").val() != ID_Cliente)
					$("#terceros_modificar_buscador_cliente_ID").val(ID_Cliente);
					
				clearTimeout(Timer1);
			},300);
		}
	});
	
	$("#terceros_modificar_buscador_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		theme: mytheme,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Buscar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#terceros_modificar_buscador_cliente_ID").bind('change', function (event) {
		if (event.args) {
			ID_Cliente = event.args.item.value;
			clearTimeout(Timer2);
			Timer2 = setTimeout(function()
			{
				LoadValues();
				clearTimeout(Timer2);
			},300);
		}
	});

	
	$("#terceros_modificar_clasificacion").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: CategoriaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Clasificacion',
		valueMember: 'Clasificacion'
	});
	$("#terceros_modificar_clasificacion").on('change', function (event) {
		if (event.args) {
			var index = event.args.index;
			var item = event.args.item;
			var label = item.label;
			Clasificacion = item.value;
			if (Clasificacion == "Proveedor")
				$("#terceros_modificar_grupo").jqxComboBox({ disabled: false});
			else
				$("#terceros_modificar_grupo").jqxComboBox({ disabled: true});
				
			$("#terceros_modificar_grupo").jqxComboBox('clearSelection');
			//$("#terceros_modificar_grupo").jqxComboBox('uncheckAll'); 
		}
		else
		{
			$("#terceros_modificar_clasificacion").jqxComboBox('clearSelection');
			$("#terceros_modificar_grupo").jqxComboBox({ disabled: true});
		}
	});
	$("#terceros_modificar_clasificacion").bind('select', function (event) {
		if (event.args) {
			Clasificacion = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				TipoSource.data = {"Terceros_Tipo": Clasificacion};
				var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource);
				$("#terceros_modificar_tipo").jqxComboBox({source: TipoDataAdapter});
				GrupoSource.data = {"Terceros_Grupo": Clasificacion};
				var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
				$("#terceros_modificar_grupo").jqxComboBox({source: GrupoDataAdapter});
				clearTimeout(Timer1);
			},350);
		}
	});
	
	var TipoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo', type: 'string'}
		],
		url: "modulos/parametros.php",
		async: true
	};
	
	$("#terceros_modificar_tipo").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo',
	});
	$("#terceros_modificar_tipo").on('change', function (event) {
		if (!event.args) {
			$("#terceros_modificar_tipo").jqxComboBox('clearSelection');
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
	
	$("#terceros_modificar_grupo").jqxComboBox(
	{
		theme: mytheme,
		width: 90,
		height: 20,
		checkboxes: true,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Grupo',
		valueMember: 'Grupo',
		disabled: true
	});
	$("#terceros_modificar_grupo").on('checkChange', function (event) {
		if (event.args) {
			var item = event.args.item;
			if (item)
			{
				var valueelement = item.value;
				var labelelement = item.label;
				var checkedelement = item.checked;

				var items = $("#terceros_modificar_grupo").jqxComboBox('getCheckedItems');
				var GrupoValues = {};
				//var checkedItems = "";
				$.each(items, function (index) {
					GrupoValues["Grupo"] = this.label;
					//checkedItems += this.label + ", ";                          
				});
				//alert(checkedItems);
				//alert(GrupoValues["Grupo"]);
			}
		}
	});
	
	$("#terceros_modificar_cliente").jqxInput({
		theme: mytheme,
		height: 20,
		width: 465,
	});
	
	$("#terceros_modificar_direccion").jqxInput({
		theme: mytheme,
		height: 20,
		width: 465,
	});
	
	var BarrioSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Barrio', type: 'string'}
		],
		data: {"Terceros_Barrio":true},
		url: "modulos/parametros.php",
		async: true
	};
	var BarrioDataDataAdapter = new $.jqx.dataAdapter(BarrioSource);
	
	$("#terceros_modificar_barrio").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: BarrioDataDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Barrio',
		selectedIndex: -1,
		displayMember: 'Barrio',
		valueMember: 'Barrio'
	});
	
	var CiudadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Ciudad', type: 'string'}
		],
		data: {"Terceros_Ciudad":true},
		url: "modulos/parametros.php",
		async: true
	};
	var CiudadDataDataAdapter = new $.jqx.dataAdapter(CiudadSource);
	
	$("#terceros_modificar_ciudad").jqxComboBox(
	{
		theme: mytheme,
		height: 20,
		width: 180,
		source: CiudadDataDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Ciudad',
		selectedIndex: -1,
		displayMember: 'Ciudad',
		valueMember: 'Ciudad'
	});
	$("#terceros_modificar_ciudad").bind('change', function (event) {
		if (event.args) {
			Ciudad = event.args.item.value;
		}
	});
	$("#terceros_modificar_ciudad").bind('select', function (event) {
		if (event.args) {
			Ciudad = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#terceros_modificar_departamento").val(Ciudad);
				var ValoresSource =
				{
					datatype: "json",
					datafields: [
						{ name: 'Departamento', type: 'string'},
						{ name: 'Pais', type: 'string'},
					],
					type: 'GET',
					data: {"Terceros_Depart_Pais":Ciudad},
					url: "modulos/parametros.php",
					async: true
				};
				var ValoresDataAdapter = new $.jqx.dataAdapter(ValoresSource,{
					autoBind: true,
					loadComplete: function ()
					{
						var records = ValoresDataAdapter.records;
						$("#terceros_modificar_departamento").val(records[0]["Departamento"]);
						$("#terceros_modificar_pais").val(records[0]["Pais"]);
					}
				});
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#terceros_modificar_departamento").jqxInput(
	{
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true
	});
	
	$("#terceros_modificar_pais").jqxInput(
	{
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true
	});
	
	$("#terceros_modificar_telefono").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 180,
		mask: '(###)#######',
	});
	
	$("#terceros_modificar_fax").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 180,
		mask: '(###)#######',
	});
	
	$("#terceros_modificar_email").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
	});
	
	$("#terceros_modificar_email2").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
	});
	
	var TipoIDSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo_Sociedad', type: 'string'}
		],
		data: {"Terceros_Tipo_Doc":true},
		url: "modulos/parametros.php",
		async: true
	};
	var TipoIDDataDataAdapter = new $.jqx.dataAdapter(TipoIDSource);
	
	$("#terceros_modificar_tipo_sociedad").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: TipoIDDataDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Tipo',
		selectedIndex: -1,
		displayMember: 'Tipo_Sociedad',
		valueMember: 'Tipo_Sociedad'
	});
	$("#terceros_modificar_tipo_sociedad").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_modificar_tipo_sociedad").jqxComboBox('clearSelection');
			$("#terceros_modificar_tipo_id").html("Documento");
			$("#terceros_modificar_cliente_ID").val('0');
			$("#terceros_modificar_cliente_ID2").val('0');
			$("#terceros_modificar_cliente_ID2").jqxNumberInput({ disabled: true });
		}
	});
	$("#terceros_modificar_tipo_sociedad").bind('select', function (event)
	{
		if (event.args) {
			//switch(event.args.item.value)
			switch(event.args.item.index)
			{
				case 0:
					$("#terceros_modificar_tipo_id").html("NIT");
					$("#terceros_modificar_cliente_ID").jqxNumberInput({ digits: 9 });
					$("#terceros_modificar_cliente_ID2").jqxNumberInput({ disabled: false });
				break;
				case 1:
					$("#terceros_modificar_tipo_id").html("Cedula");
					$("#terceros_modificar_cliente_ID").jqxNumberInput({ digits: 10 });
					$("#terceros_modificar_cliente_ID2").jqxNumberInput({ disabled: true });
				break;
				case 2:
					$("#terceros_modificar_tipo_id").html("RUT");
					$("#terceros_modificar_cliente_ID").jqxNumberInput({ digits: 10 });
					$("#terceros_modificar_cliente_ID2").jqxNumberInput({ disabled: false });
				break;
				case 3:
					$("#terceros_modificar_tipo_id").html("T. I.");
					$("#terceros_modificar_cliente_ID").jqxNumberInput({ digits: 11/*, groupSize: 6, groupSeparator: "-"*/ });
					$("#terceros_modificar_cliente_ID2").jqxNumberInput({ disabled: true });
				break;
			}
		}
	});
	
	$("#terceros_modificar_cliente_ID").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 120,
		inputMode: 'simple',
		digits: 10,
		decimalDigits: 0,
		//groupSize: 0,
		//groupSeparator: ".",
		max: 99999999999
	});
	$("#terceros_modificar_cliente_ID").on("change", function (event)
	{
		if (event.args)
		{
			var value = event.args.value;
			if ($("#terceros_modificar_cliente_ID2").jqxNumberInput('disabled') == false)
				value = value + "-" +$("#terceros_modificar_cliente_ID2").val();
			if (ID_Exception != value)
			{
				var FindSource =
				{
					datatype: "json",
					datafields: [
						{ name: 'Same', type: 'bool'},
					],
					type: 'GET',
					data: {
						"Terceros_Check_SameID":value,
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
							$("#terceros_modificar_cliente_ID").addClass("jqx-validator-error-element");
							ID_Incorrecto = true;
							Alerts_Box("El ID ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
							WaitClick_NumberInput("terceros_modificar_cliente_ID");
						}
						else
						{
							$("#terceros_modificar_cliente_ID").removeClass("jqx-validator-error-element");
							ID_Incorrecto = false;
						}
					}
				});
			}
		}
	});
	
	$("#terceros_modificar_cliente_ID2").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 12,
		digits: 1,
		decimalDigits: 0,
		disabled: true
	});
	$("#terceros_modificar_cliente_ID2").on("change", function (event)
	{
		if (event.args)
		{
			var value = $("#terceros_modificar_cliente_ID").val();
			value = value + "-" + event.args.value;
			
			if (ID_Exception != value)
			{
				var FindSource =
				{
					datatype: "json",
					datafields: [
						{ name: 'Same', type: 'bool'},
					],
					type: 'GET',
					data: {
						"Terceros_Check_SameID":value,
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
							$("#terceros_modificar_cliente_ID").addClass("jqx-validator-error-element");
							ID_Incorrecto = true;
							Alerts_Box("El ID ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
							WaitClick_NumberInput("terceros_modificar_cliente_ID");
						}
						else
						{
							$("#terceros_modificar_cliente_ID").removeClass("jqx-validator-error-element");
							ID_Incorrecto = false;
						}
					}
				});
			}
		}
	});
	
	$("#terceros_modificar_telefono_cp").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 180,
		mask: '(###)#######',
	});
	
	$("#terceros_modificar_contacto_Ppal").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
	});
	
	$("#terceros_modificar_contacto_sec").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
	});
	
	$("#terceros_modificar_telefono_cs").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 180,
		mask: '(###)#######',
	});
	
	$("#terceros_modificar_vendedor").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: VendedorDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Vendedor',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo'
	});
	$("#terceros_modificar_vendedor").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_modificar_vendedor").jqxComboBox('clearSelection');
		}
	});
	
	$("#terceros_modificar_cobrador").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: VendedorDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cobrador',
		selectedIndex: -1,
		displayMember: 'Vendedor',
		valueMember: 'Codigo'
	});
	$("#terceros_modificar_cobrador").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_modificar_cobrador").jqxComboBox('clearSelection');
		}
	});
	
	var TerminosValues = [
		{"Termino":"Efectivo"},
		{"Termino":"15 Dias"},
		{"Termino":"30 Dias"},
		{"Termino":"45 Dias"},
		{"Termino":"60 Dias"},
		{"Termino":"90 Dias"},
	];
	
	var TerminosSource =
	{
		localdata: TerminosValues,
		datatype: "json",
		datafields: [
			{ name: 'Termino', type: 'string'},
		],
	};
	var TerminosDataAdapter = new $.jqx.dataAdapter(TerminosSource);
	
	$("#terceros_modificar_terminos").jqxComboBox(
	{
		theme: mytheme,
		width: 180,
		height: 20,
		source: TerminosDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Terminos',
		selectedIndex: -1,
		displayMember: 'Termino',
		valueMember: 'Termino'
	});
	$("#terceros_modificar_terminos").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_modificar_terminos").jqxComboBox('clearSelection');
		}
	});
	
	$("#terceros_modificar_cupo_credito").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 165,
		symbol: '$',
		digits: 15,
		max: 999999999999999
	});
	
	$("#terceros_modificar_cupo_activado").jqxCheckBox({
		theme: mytheme,
		boxSize: 16,
	});
	
	$("#terceros_modificar_cupo_activado_por").jqxInput({
		theme: mytheme,
		height: 20,
		width: 100,
		disabled: true,
	});
	
	$("#terceros_modificar_cupo_adicional").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 165,
		symbol: '$',
		digits: 15,
		max: 999999999999999
	});
	
	var ListaValues = [
		{"Lista":"Lista1", "Num":"1"},
		{"Lista":"Lista2", "Num":"2"},
		{"Lista":"Lista3", "Num":"3"},
		{"Lista":"Lista4", "Num":"4"},
	];
	
	var ListaSource =
	{
		localdata: ListaValues,
		datatype: "json",
		datafields: [
			{ name: 'Lista', type: 'string'},
			{ name: 'Num', type: 'int'},
		],
	};
	var ListaDataAdapter = new $.jqx.dataAdapter(ListaSource);
	
	$("#terceros_modificar_lista_precios").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ListaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Precio',
		selectedIndex: -1,
		displayMember: 'Lista',
		valueMember: 'Num'
	});
	$("#terceros_modificar_lista_precios").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_modificar_lista_precios").jqxComboBox('clearSelection');
		}
	});
	
	$("#terceros_modificar_digitado_por").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true,
	});
	
	$("#terceros_modificar_fecha_dig").jqxDateTimeInput({
		theme: mytheme,
		width: 180,
		height: 20,
		formatString: 'dd-MMMM-yyyy hh:mm:ss',
		culture: 'es-ES',
		disabled: true,
		showCalendarButton: false
	});
	
	$("#terceros_modificar_modificado_por").jqxInput({
		theme: mytheme,
		height: 20,
		width: 180,
		disabled: true,
	});
	
	$("#terceros_modificar_fecha_modifi").jqxDateTimeInput({
		theme: mytheme,
		width: 180,
		height: 20,
		formatString: 'dd-MMMM-yyyy hh:mm:ss',
		culture: 'es-ES',
		disabled: true,
		showCalendarButton: false
	});
	
	$("#terceros_modificar_vigencia_cupo").jqxDateTimeInput({
		theme: mytheme,
		width: 180,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#terceros_modificar_vigencia_adicional").jqxDateTimeInput({
		theme: mytheme,
		width: 180,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#terceros_modificar_cartera").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		selectedIndex: 0,
		dropDownHeight: 100
	});
	$("#terceros_modificar_cartera").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_modificar_cartera").jqxComboBox('clearSelection');
		}
	});
	
	$("#terceros_modificar_cartera").jqxComboBox('addItem', {label: "Al Dia"});
	$("#terceros_modificar_cartera").jqxComboBox('addItem', {label: "Mora"});
	$("#terceros_modificar_cartera").jqxComboBox('addItem', {label: "Juridico"});
	
	$("#terceros_modificar_fecha_notas").jqxDateTimeInput({
		theme: mytheme,
		width: 180,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	
	var GarantiaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Apply', type: 'bool'},
			{ name: 'Garantia', type: 'string'},
			{ name: 'Imagen', type: 'string'},
			{ name: 'Ok', type: 'bool'},
		],
		type: 'GET',
		data: {"Garantias":true},
		url: "modulos/parametros.php",
		async: true,
	};
	var GarantiaDataAdapter = new $.jqx.dataAdapter(GarantiaSource);
	
	$("#terceros_modificar_garantias").jqxGrid({
		theme: mytheme,
		width: 330,
		height: 200,
		source: GarantiaDataAdapter,
		editable: true,
		editmode: 'click',
		//selectionmode: 'singlecell',
		columns:
		[
			{ text: '**', datafield: 'Apply', columntype: 'checkbox', width: '1%' },
			{ text: 'Garantia', datafield: 'Garantia', editable: false, width: '82%', height: 20 },
			{ text: 'Imagen/Doc', datafield: 'Imagen', editable: false, width: '30%', height: 20 },
			{ text: 'Ok?', datafield: 'Ok', columntype: 'checkbox', width: '10%' },
		]
	});
	$("#terceros_modificar_garantias").on("bindingcomplete", function (event)
	{
		$("#terceros_modificar_garantias").jqxGrid('hidecolumn', 'Imagen');
	});
	
	$("#terceros_modificar_garantias").bind('cellvaluechanged', function (event)
	{
		if (event.args)
		{
			var args = event.args;
			var datafield = event.args.datafield;
			var rowBoundIndex = args.rowindex;
			var value = args.newvalue;
			var oldvalue = args.oldvalue;
			
			if (datafield == "Apply")
			{
				UploadID = rowBoundIndex;
				
				if (value == true) {
					$("#terceros_modificar_upload_docs_window").jqxWindow('open');
				}
			}
		}
	});
	$('#terceros_modificar_garantias').on('rowselect', function (event)
	{
		var args = event.args;
		var rowBoundIndex = args.rowindex;
		var rowData = args.row;
		//---
		ShowPhoto(rowData.Imagen, 2);
	});
	
	$("#terceros_modificar_docs_delete").click(function() {
		//Clean
		var index = $("#terceros_modificar_garantias").jqxGrid('getselectedrowindex');
		var data = $("#terceros_modificar_garantias").jqxGrid('getrowdata', index);
		var datarow = {};
		datarow["Apply"] = false;
		datarow["Garantia"] = data.Garantia
		datarow["Imagen"] = "";
		datarow["Ok"] = data.Ok;
		$("#terceros_modificar_garantias").jqxGrid('updaterow', index, datarow);
		
		var img = "<img width=\"200\" height=\"200\" src=\"images/search.png\" alt=\"Foto del Cliente\" style=\"margin: 0px 15px;\"/>";
		$("#terceros_modificar_docs").html(img);
	});
	
	$("#terceros_modificar_cliente_foto_delete").click(function() {
		//Clean Img
		var img = "<img id=\"terceros_modificar_cliente_foto_ID\" width=\"200\" height=\"200\" src=\"images/UserIcon.png\" alt=\"Foto del Cliente\" style=\"margin: 0px 20px;\"/>";
		$("#terceros_modificar_cliente_foto").html(img);
	});
	
	$("#terceros_modificar_cliente_foto_edit").click(function() {
		$("#terceros_modificar_upload_docs_window").jqxWindow('open');
	});
	
	$("#terceros_modificar_cliente_foto").click(function()
	{
		var img_src = document.getElementById("terceros_modificar_cliente_foto_ID").src;
		var img = "<img src=\""+img_src+"\" alt=\"Foto del Cliente\"/>";
		$("#terceros_modificar_show_docs_content").html(img);
		$("#terceros_modificar_show_docs_window").jqxWindow('open');
	});
	
	$("#terceros_modificar_upload_docs_window").jqxWindow({
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
			$("#terceros_modificar_cliente_upload_docs").jqxFileUpload({
				theme: mytheme,
				width: 290,
				multipleFilesUpload: false,
				browseTemplate: 'success',
				uploadTemplate: 'primary',
				cancelTemplate: 'danger',
				uploadUrl: 'modulos/guardar.php',
				fileInputName: 'Image_Uploads',
			});
			$("#terceros_modificar_cliente_upload_docs").jqxFileUpload({ localization: localizationobj });
			$("#terceros_modificar_cliente_upload_docs").on('uploadEnd', function (event)
			{
				var args = event.args;
				var fileName = args.file;
				var serverResponce = args.response;
				
				if (serverResponce == "OK")
				{
					if (UploadID < 0) // Foto
					{
						var Tmp = "Uploads_Tmp/"+fileName;
						ShowPhoto(Tmp, 1, true);
					}
					else // Documento
					{
						var Tmp = "Uploads_Tmp/"+fileName;
						$("#terceros_modificar_garantias").jqxGrid('setcellvalue', UploadID, "Imagen", Tmp);
						ShowPhoto(Tmp, 2, true);
					}
					$("#terceros_modificar_upload_docs_window").jqxWindow('close');
				}
				else
				{
					Alerts_Box(""+serverResponce, 3);
					WaitClick_WindowClose("terceros_modificar_upload_docs_window");
				}
				
			});
		}
	});
	$("#terceros_modificar_upload_docs_window").on('close', function (event) {
		// Clean Variable
		UploadID = -1;
	});
	
	function ShowPhoto(name, type, temp)
	{
		if (type == 1)
		{
			if (temp)
				var img = "<img id=\"terceros_modificar_cliente_foto_ID\" width=\"200\" height=\"200\" src=\""+name+"\" alt=\"Foto del Cliente\" style=\"margin: 0px 20px;\"/>";
			else
				var img = "<img id=\"terceros_modificar_cliente_foto_ID\" width=\"200\" height=\"200\" src=\"images/"+name+"\" alt=\"Foto del Cliente\" style=\"margin: 0px 20px;\"/>";
			$("#terceros_modificar_cliente_foto").html(img);
		}
		else
		{
			if (name != "") {
				if (temp)
					var img = "<img id=\"terceros_modificar_docs_img\" width=\"200\" height=\"200\" src=\""+name+"\" alt=\"Documento del Cliente\" style=\"margin: 0px 15px;\"/>";
				else
					var img = "<img id=\"terceros_modificar_docs_img\" width=\"200\" height=\"200\" src=\"images/"+name+"\" alt=\"Documento del Cliente\" style=\"margin: 0px 15px;\"/>";
				$("#terceros_modificar_docs").html(img);
			}
			else {
				var img = "<img id=\"terceros_modificar_docs_img\" width=\"200\" height=\"200\" src=\"images/search.png\" alt=\"Documento del Cliente\" style=\"margin: 0px 15px;\"/>";
				$("#terceros_modificar_docs").html(img);
			}
		}
	};
	
	$("#terceros_modificar_docs").click(function()
	{
		var img_src = document.getElementById("terceros_modificar_docs_img").src;
		if (img_src.indexOf("images/search.png") > 0)
		{
			Alerts_Box("Debe Cargar una Imagen desde Garantias.", 4);
			return;
		}
		
		var img = "<img src=\""+img_src+"\" alt=\"Documento del Cliente\"/>";
		$("#terceros_modificar_show_docs_content").html(img);
		$("#terceros_modificar_show_docs_window").jqxWindow('open');
	});
	
	$("#terceros_modificar_show_docs_window").jqxWindow({
		theme: mytheme,
		autoOpen: false,
		showCloseButton: true,
		height: 600,
		width: 800,
		resizable: false,
		isModal: true,
		modalOpacity: 0.3,
		initContent: function ()
		{
			//
		}
	});
	
	// -------------------------------------------------- PART 2
	
	function ModificarTercero()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ID_Incorrecto) {
			Alerts_Box("El ID ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
			WaitClick_NumberInput("terceros_modificar_cliente_ID");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_clasificacion").val() == "" || $("#terceros_modificar_clasificacion").val() < 1)
		{
			Alerts_Box("Debe Seleccionar un tipo de Clasificacion.", 3);
			WaitClick_Combobox("terceros_modificar_clasificacion");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_cliente").val() == "" || $("#terceros_modificar_cliente").val() < 1)
		{
			Alerts_Box("Debe Ingresar un Nombre.", 3);
			WaitClick_Input("terceros_modificar_cliente");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_direccion").val() == "" || $("#terceros_modificar_direccion").val() < 1)
		{
			Alerts_Box("Debe Ingresar una Direccion.", 3);
			WaitClick_Input("terceros_modificar_direccion");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_telefono").val() == "(___)_______" )
		{
			Alerts_Box("Debe Ingresar un Telefono.", 3);
			WaitClick_MaskedInput("terceros_modificar_telefono");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_contacto_Ppal").val() == "" || $("#terceros_modificar_contacto_Ppal").val() < 1)
		{
			Alerts_Box("Debe Ingresar un Contacto Principal.", 3);
			WaitClick_Input("terceros_modificar_contacto_Ppal");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_telefono_cp").val() == "(___)_______")
		{
			Alerts_Box("Debe Ingresar un Telefono del Contacto Principal.", 3);
			WaitClick_MaskedInput("terceros_modificar_telefono_cp");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_tipo_sociedad").val() == "" || $("#terceros_modificar_tipo_sociedad").val() < 1)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Sociedad.", 3);
			WaitClick_Combobox("terceros_modificar_tipo_sociedad");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_cliente_ID").val() == "" || $("#terceros_modificar_cliente_ID").val() < 1)
		{
			Alerts_Box("Debe Ingresar un ID.", 3);
			WaitClick_NumberInput("terceros_modificar_cliente_ID");
			Locked = false;
			return;
		}
		
		if ($("#terceros_modificar_cliente_ID2").jqxNumberInput('disabled') == false)
		{
			if ($("#terceros_modificar_cliente_ID").val() < 0)
			{
				Alerts_Box("Debe Ingresar el Numero de Verificacion del ID.", 3);
				WaitClick_NumberInput("terceros_modificar_cliente_ID2");
				Locked = false;
				return;
			}
		}
		
		var datinfo = $("#terceros_modificar_garantias").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		// Get Checked Group
		var items = $("#terceros_modificar_grupo").jqxComboBox('getCheckedItems');
		$.each(items, function (index) {
			var GrupoValues = {};
			GrupoValues["Grupo"] = this.label;
			GrupoArray[index] = GrupoValues; 
		});
		
		if (count < 1)
		{
			var array = {};
			array["Foto_Cliente"] = GetImage("terceros_modificar_cliente_foto_ID");
			array["Clasificacion"] = $("#terceros_modificar_clasificacion").val();
			array["Tipo"] = $("#terceros_modificar_tipo").val();
			array["Grupo"] = GrupoArray;
			array["Nombre"] = $("#terceros_modificar_cliente").val();
			array["Direccion"] = $("#terceros_modificar_direccion").val();
			array["Barrio"] = $("#terceros_modificar_barrio").val();
			array["Ciudad"] = $("#terceros_modificar_ciudad").val();
			array["Departamento"] = $("#terceros_modificar_departamento").val();
			array["Pais"] = $("#terceros_modificar_pais").val();
			array["Telefono"] = $("#terceros_modificar_telefono").val();
			array["Fax"] = $("#terceros_modificar_fax").val();
			array["Email"] = $("#terceros_modificar_email").val();
			array["Email2"] = $("#terceros_modificar_email2").val();
			array["Tipo_Sociedad"] = $("#terceros_modificar_tipo_sociedad").val();
			var Doc = document.getElementById('terceros_modificar_tipo_id').innerHTML;
			array["Tipo_Doc"] = Doc;
			var Final_ClienteID = "" + $("#terceros_modificar_cliente_ID").val();
			if (Doc == "NIT" || Doc == "RUT")
				Final_ClienteID = Final_ClienteID +"-"+ $("#terceros_modificar_cliente_ID2").val();
			array["ClienteID"] = Final_ClienteID;
			array["Old_ClientID"] = ID_Cliente;
			array["Telefono_CP"] = $("#terceros_modificar_telefono_cp").val();
			array["Contacto_P"] = $("#terceros_modificar_contacto_Ppal").val();
			array["Contacto_S"] = $("#terceros_modificar_contacto_sec").val();
			array["Telefono_CS"] = $("#terceros_modificar_telefono_cs").val();
			array["VendedorID"] = $("#terceros_modificar_vendedor").val();
			array["CobradorID"] = $("#terceros_modificar_cobrador").val();
			array["Terminos"] = $("#terceros_modificar_terminos").val();
			array["Cupo_Credito"] = $("#terceros_modificar_cupo_credito").val();
			array["Cupo_Adicional"] = $("#terceros_modificar_cupo_adicional").val();
			array["ListaP"] = $("#terceros_modificar_lista_precios").val();
			array["Vigencia_Cupo_Credito"] = GetFormattedDate($("#terceros_modificar_vigencia_cupo").jqxDateTimeInput('getDate'));
			array["Vigencia_Cupo_Adicional"] = GetFormattedDate($("#terceros_modificar_vigencia_adicional").jqxDateTimeInput('getDate'));
			array["Estado_Cuenta"] = $("#terceros_modificar_cartera").val();
			array["Fecha_Notas"] = GetFormattedDate($("#terceros_modificar_fecha_notas").jqxDateTimeInput('getDate'));
			array["Notas"] = $("#terceros_modificar_notas").val();
			//
			myarray[0] = array;
		}
		else
		{
			for (i = 0; i < count; i++)
			{
				var array = {};
				var currentRow = $('#terceros_modificar_garantias').jqxGrid('getrowdata', i);
				
				if (currentRow.Apply == true)
				{
					array["Garantia"] = currentRow.Garantia;
					array["Imagen"] = currentRow.Imagen;
					array["Ok"] = currentRow.Ok;
				}
				if (i==0) // static values
				{
					var Imagen = GetImage("terceros_modificar_cliente_foto_ID");
					if (Imagen == "images/UserIcon.png")
						Imagen = "";
					array["Foto_Cliente"] = Imagen;
					array["Clasificacion"] = $("#terceros_modificar_clasificacion").val();
					array["Tipo"] = $("#terceros_modificar_tipo").val();
					array["Grupo"] = GrupoArray;
					array["Nombre"] = $("#terceros_modificar_cliente").val();
					array["Direccion"] = $("#terceros_modificar_direccion").val();
					array["Barrio"] = $("#terceros_modificar_barrio").val();
					array["Ciudad"] = $("#terceros_modificar_ciudad").val();
					array["Departamento"] = $("#terceros_modificar_departamento").val();
					array["Pais"] = $("#terceros_modificar_pais").val();
					array["Telefono"] = $("#terceros_modificar_telefono").val();
					array["Fax"] = $("#terceros_modificar_fax").val();
					array["Email"] = $("#terceros_modificar_email").val();
					array["Email2"] = $("#terceros_modificar_email2").val();
					array["Tipo_Sociedad"] = $("#terceros_modificar_tipo_sociedad").val();
					var Doc = document.getElementById('terceros_modificar_tipo_id').innerHTML;
					array["Tipo_Doc"] = Doc;
					var Final_ClienteID = "" + $("#terceros_modificar_cliente_ID").val();
					if (Doc == "NIT" || Doc == "RUT")
						Final_ClienteID = Final_ClienteID +"-"+ $("#terceros_modificar_cliente_ID2").val();
					array["ClienteID"] = Final_ClienteID;
					array["Old_ClienteID"] = ID_Cliente;
					array["Telefono_CP"] = $("#terceros_modificar_telefono_cp").val();
					array["Contacto_P"] = $("#terceros_modificar_contacto_Ppal").val();
					array["Contacto_S"] = $("#terceros_modificar_contacto_sec").val();
					array["Telefono_CS"] = $("#terceros_modificar_telefono_cs").val();
					array["VendedorID"] = $("#terceros_modificar_vendedor").val();
					array["CobradorID"] = $("#terceros_modificar_cobrador").val();
					array["Terminos"] = $("#terceros_modificar_terminos").val();
					array["Cupo_Credito"] = $("#terceros_modificar_cupo_credito").val();
					array["Cupo_Adicional"] = $("#terceros_modificar_cupo_adicional").val();
					array["ListaP"] = $("#terceros_modificar_lista_precios").val();
					array["Vigencia_Cupo_Credito"] = GetFormattedDate($("#terceros_modificar_vigencia_cupo").jqxDateTimeInput('getDate'));
					array["Vigencia_Cupo_Adicional"] = GetFormattedDate($("#terceros_modificar_vigencia_adicional").jqxDateTimeInput('getDate'));
					array["Cupo_Activo"] = $("#terceros_modificar_cupo_activado").val();
					array["Cupo_Asignado_Por"] = $("#terceros_modificar_cupo_activado_por").val();
					array["Estado_Cuenta"] = $("#terceros_modificar_cartera").val();
					array["Fecha_Notas"] = GetFormattedDate($("#terceros_modificar_fecha_notas").jqxDateTimeInput('getDate'));
					array["Notas"] = $("#terceros_modificar_notas").val();
				}
				myarray[i] = array;
			}
		}
		/*
		alert(JSON.stringify(myarray))
		Locked = false;
		return;
		*/
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'json',
			url: "modulos/guardar.php",
			data: {"Terceros_Modificar":myarray},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				if (data[0]["MESSAGE"] == "OK")
				{
					Alerts_Box("Datos Guardados con Exito!<br />Presione Aceptar para Limpiar o Cancelar para mantener los datos en Pantalla.", 2, true);
					//EnableDisableAll(true);
					Timer1 = setInterval(function()
					{
						if (ClickOK == true)
						{
							ClearDocument();
							clearInterval(Timer1);
							clearTimeout(Timer2);
							
						}
						else if (ClickCANCEL == true)
						{
							ClickOK = false;
							ClickCANCEL = false;
							clearInterval(Timer1);
							clearTimeout(Timer2);
						}
					},10);
					Timer2 = setTimeout(function()
					{
						$("#Ask_Alert").jqxWindow('close');
						clearInterval(Timer1);
						ClickOK = false;
						ClickCANCEL = false;
					},5000);
				}
				else
					Alerts_Box("Ocurrio un Error al intentar Guardar los datos!<br />Intente luego de unos segundos...", 3);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar crear_guardar los datos!<br />Intente luego de unos segundos...", 3);
			}
		});
	};
	
	function EliminarTercero()
	{
		if (ID_Cliente == 0 || ID_Cliente == "") {
			Alerts_Box("Debe Seleccionar un Tercero!", 3);
			return;
		}
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'text',
			type: 'GET',
			url: "modulos/guardar.php",
			data: {"Terceros_Modificar_Borrar":ID_Cliente},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				
				if (data == "OK")
				{
					ClearDocument();
					Alerts_Box("Datos Guardados con Exito!", 2);
				}
				else
					Alerts_Box("Ocurrio un Error al intentar Borrar los Datos!\nIntente luego de unos segundos...", 3);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Alerts_Box("Ocurrio un Error al intentar crear_guardar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	};
	
	$("#terceros_modificar_guardar").jqxButton({width: 150, template: "info"});
	$("#terceros_modificar_guardar").bind('click', function ()
	{
		ModificarTercero();
	});
	
	$("#terceros_modificar_eliminar").jqxButton({width: 150, template: "danger"});
	$("#terceros_modificar_eliminar").bind('click', function ()
	{
		ClickOK = false;
		ClickCANCEL = false;
		
		if (ID_Cliente == -1 || ID_Cliente == "") {
			Alerts_Box("Debe Seleccionar un Tercero!", 3);
			return;
		}
		
		Alerts_Box("Realmente Desea Borrar este Cliente?", 4, 3);
		var Timer = setInterval(function() {
			if (ClickOK == true) {
				clearInterval(Timer);
				ClickOK = false;
				EliminarTercero();
			}
			else if (ClickCANCEL == true) {
				clearInterval(Timer);
				ClickCANCEL = false;
			}
		}, 10);
	});
	
	SetDefaultDate();
	Filter();
	CheckRefresh();
});
</script>
<div id="terceros_modificar_upload_docs_window">
	<div style="height:20px; font-size: 16px; color: #707070;">
	</div>
	<div>
		<div id="terceros_modificar_cliente_upload_docs"></div>
	</div>
</div>
<div id="terceros_modificar_show_docs_window">
	<div style="height:15px;">
	</div>
	<div>
		<div id="terceros_modificar_show_docs_content"></div>
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="3" cellspacing="1" style="margin:5px 0px 20px 0px;">
		<tr>
			<td rowspan="2" colspan="2" style="text-align:center;">
				<div style="background: #75858A;color: #FFF;font-size: 13px;padding: 5px 5px 0px 5px; height: 25px;">
					FILTRAR POR:
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<li class="parte1_li_txt">
					Vendedor&nbsp;
				</li>
				<li style="padding:0px 3px;">
					<div id="terceros_modificar_buscador_vendedor"></div>
				</li>
				<li class="parte1_li_txt">
					Tipo Tercero&nbsp;
				</li>
				<li style="padding:0px 3px;">
					<div id="terceros_modificar_buscador_tipo_cliente"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td rowspan="2">
				<li>
					<input type="button" id="terceros_modificar_back" value="<< Ant."/>
				</li>
				<li style="padding:0px 0px 0px 10px;">
					<input type="button" id="terceros_modificar_next" value="Sig. >>"/>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<li class="parte1_li_txt">
					&nbsp; Nombre&nbsp;
				</li>
				<li style="padding:0px 3px 0px 6px">
					<div id="terceros_modificar_buscador_cliente"></div>
				</li>
				<li class="parte1_li_txt">
					ID&nbsp;
				</li>
				<li style="padding:0px 3px;">
					<div id="terceros_modificar_buscador_cliente_ID"></div>
				</li>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 830px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td colspan="2" rowspan="7">
				<div id="terceros_modificar_cliente_foto_delete" style="width:16px; height:16px; margin: 0px 0px 0px 20px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/close_white.png'" onmouseout="this.src='images/close_black.png'" width="16" height="16" src="images/close_black.png" alt="Eliminar Foto" style="margin: 0px;"/>
				</div>
				<div id="terceros_modificar_cliente_foto_edit" style="width:16px; height:16px; margin: 0px 0px 0px 40px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/edit_white.png'" onmouseout="this.src='images/edit_black.png'" width="16" height="16" src="images/edit_black.png" alt="Cambiar Imagen"/>
				</div>
				<div id="terceros_modificar_cliente_foto" style="cursor:pointer;">
					<img id="terceros_modificar_cliente_foto_ID" width="200" height="200" src="images/UserIcon.png" alt="Foto del Cliente" style="margin: 0px 20px;"/>
				</div>
			</td>
			<td>
				Clasificacion
			</td>
			<td>
				<div id="terceros_modificar_clasificacion"></div>
			</td>
			<td colspan="2">
				<li class="parte1_li_txt">
					Tipo&nbsp;
				</li>
				<li>
					<div id="terceros_modificar_tipo"></div>
				</li>
				<li class="parte1_li_txt">
					&nbsp;Grupo&nbsp;
				</li>
				<li>
					<div id="terceros_modificar_grupo"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Nombre
			</td>
			<td colspan="3">
				<input type="text" id="terceros_modificar_cliente"/>
			</td>
		</tr>
		<tr>
			<td>
				Direccion
			</td>
			<td colspan="3">
				<input type="text" id="terceros_modificar_direccion"/>
			</td>
		</tr>
		<tr>
			<td>
				Barrio
			</td>
			<td>
				<div id="terceros_modificar_barrio"></div>
			</td>
			<td>
				Ciudad
			</td>
			<td>
				<div id="terceros_modificar_ciudad"></div>
			</td>
		</tr>
		<tr>
			<td>
				Departamento
			</td>
			<td>
				<input type="text" id="terceros_modificar_departamento"/>
			</td>
			<td>
				Pais
			</td>
			<td>
				<input type="text" id="terceros_modificar_pais"/>
			</td>
		</tr>
		<tr>
			<td>
				Telefono
			</td>
			<td>
				<div id="terceros_modificar_telefono"></div>
			</td>
			<td>
				Fax
			</td>
			<td>
				<div id="terceros_modificar_fax"></div>
			</td>
		</tr>
		<tr>
			<td>
				E-mail(1)
			</td>
			<td>
				<input type="text" id="terceros_modificar_email"/>
			</td>
			<td>
				E-mail(2)
			</td>
			<td>
				<input type="text" id="terceros_modificar_email2"/>
			</td>
		</tr>
		<tr>
			<td>
				Tipo Sociedad
			</td>
			<td>
				<div id="terceros_modificar_tipo_sociedad"></div>
			</td>
			<td>
				Contacto(1)
			</td>
			<td>
				<input type="text" id="terceros_modificar_contacto_Ppal"/>
			</td>
			<td>
				Telefono(1)
			</td>
			<td>
				<div id="terceros_modificar_telefono_cp"></div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="terceros_modificar_tipo_id">
					Seleccionar
				</div>
			</td>
			<td>
				<li>
					<div id="terceros_modificar_cliente_ID"></div>
				</li>
				<li class="parte1_li_txt">
				&nbsp;-&nbsp;
				</li>
				<li>
					<div id="terceros_modificar_cliente_ID2"></div>
				</li>
			</td>
			<td>
				Contacto(2)
			</td>
			<td>
				<input type="text" id="terceros_modificar_contacto_sec"/>
			</td>
			<td>
				Telefono(2)
			</td>
			<td>
				<div id="terceros_modificar_telefono_cs"></div>
			</td>
		</tr>
		<tr>
			<td>
				Vendedor As
			</td>
			<td>
				<div id="terceros_modificar_vendedor"></div>
			</td>
			<td>
				Terminos
			</td>
			<td>
				<div id="terceros_modificar_terminos"></div>
			</td>
			<td>
				Vigencia Notas
			</td>
			<td>
				<div id="terceros_modificar_fecha_notas"></div>
			</td>
		</tr>
		<tr>
			<td>
				Cobrador As
			</td>
			<td>
				<div id="terceros_modificar_cobrador"></div>
			</td>
			<td>
				Digitado Por
			</td>
			<td>
				<input type="text" id="terceros_modificar_digitado_por"/>
			</td>
			<td>
				Fecha Dig.
			</td>
			<td>
				<div id="terceros_modificar_fecha_dig"></div>
			</td>
		</tr>
		<tr>
			<td>
				Lista de Precios
			</td>
			<td>
				<div id="terceros_modificar_lista_precios"></div>
			</td>
			<td>
				Modificado Por
			</td>
			<td>
				<input type="text" id="terceros_modificar_modificado_por"/>
			</td>
			<td>
				Fecha Modif.
			</td>
			<td>
				<div id="terceros_modificar_fecha_modifi"></div>
			</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Cupo Credito
			</td>
			<td>
				<div id="terceros_modificar_cupo_credito"></div>
			</td>
			<td>
				Vigencia Cupo
			</td>
			<td>
				<div id="terceros_modificar_vigencia_cupo"></div>
			</td>
			<td colspan="2">
				<li class="parte1_li_txt">
					Asignado &nbsp;
				</li>
				<li>
					<div id="terceros_modificar_cupo_activado"></div>
				</li>
				<li class="parte1_li_txt">
					&nbsp; Asignado por&nbsp;
				</li>
				<li>
					<input type="text" id="terceros_modificar_cupo_activado_por"/>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Adicional
			</td>
			<td>
				<div id="terceros_modificar_cupo_adicional"></div>
			</td>
			<td>
				Vigencia Ad.
			</td>
			<td>
				<div id="terceros_modificar_vigencia_adicional"></div>
			</td>
			<td>
				Cartera
			</td>
			<td>
				<div id="terceros_modificar_cartera"></div>
			</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td>
				Garantias -> (**) Obligatorio
			</td>
			<td>
				Documentos
			</td>
			<td>
				Notas
			</td>
		</tr>
		<tr>
			<td>
				<div id="terceros_modificar_garantias"></div>
			</td>
			<td>
				<div id="terceros_modificar_docs_delete" style="cursor:pointer; position: absolute;">
					<img width="16" height="16" src="images/close2.png" alt="Eliminar Foto" style="margin: 0px;"/>
				</div>
				<div id="terceros_modificar_docs" style="cursor:pointer;">
					<img id="terceros_modificar_docs_img" width="200" height="200" src="images/search.png" alt="Documento del Cliente" style="margin: 0px 15px;"/>
				</div>
			</td>
			<td>
				<textarea rows="12" cols="30" id="terceros_modificar_notas" maxlength="100" style="resize:none;"></textarea>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px; margin-left: 520px;">
		<tr>
			<td>
				<input type="button" id="terceros_modificar_guardar" value="Guardar"/>
			</td>
			<td style="padding-left:10px;">
				<input type="button" id="terceros_modificar_eliminar" value="Eliminar"/>
			</td>
		</tr>
	</table>
</div>