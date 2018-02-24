<?php
session_start();
$CupoHide = "false";
	if (isset($_POST['Crear_Emergente']) && $_POST['Crear_Emergente'] == true)
	{
		$CupoHide = "true";
		$_SESSION["NumOfID"]++;
	}
$ClienteID = isset($_POST['ClienteID']) ? $_POST['ClienteID']:"";
$NumOfID = $_SESSION["NumOfID"];
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var ID_Cliente = -1;
	var ID_Incorrecto = false;
	var UploadID = -1;
	var Clasificacion = "";
	var Tipo = "";
	var Ciudad = "";
	var GrupoArray = new Array();
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Terceros_Content");
	var Body = document.getElementById("Terceros_Crear_Content");
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
	
	var MyDate = new Date();
	var Year = MyDate.getFullYear();
	Day = "31";
	Month = "11";
	
	function ClearDocument()
	{
		ID_Cliente = -1;
		ID_Incorrecto = false;
		UploadID = -1;
		Clasificacion = "";
		Tipo = "";
		Ciudad = "";
		GrupoArray = new Array();
		// Clean Img
		var img = "";
		img = "<img width=\"200\" height=\"200\" src=\"images/search.png\" alt=\"Foto del Cliente\" style=\"margin: 0px 15px;\"/>";
		$("#terceros_crear_docs<?php echo $NumOfID ?>").html(img);
		img = "<img id=\"terceros_crear_cliente_foto_ID<?php echo $NumOfID ?>\" width=\"200\" height=\"200\" src=\"images/UserIcon.png\" alt=\"Foto del Cliente\" style=\"margin: 0px 20px;\"/>";
		$("#terceros_crear_cliente_foto<?php echo $NumOfID ?>").html(img);
		// Clean Grid
		var datinfo = $("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		if (count >= 0)
		{
			for (i = 0; i < count; i++)
			{
				var datarow = {};
				var currentRow = $("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
				datarow["Apply"] = false;
				datarow["Garantia"] = currentRow.Garantia;
				datarow["Imagen"] = "";
				datarow["Ok"] = false;
				$("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('updaterow', i, datarow);
			}
		}
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
		SetDefaultDate();
	};
	
	function SetDefaultDate ()
	{
		$("#terceros_crear_vigencia_cupo<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
		$("#terceros_crear_vigencia_adicional<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
		$("#terceros_crear_fecha_notas<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	};
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"terceros_crear_clasificacion<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_tipo<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_grupo<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_cliente<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_direccion<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_barrio<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_ciudad<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_departamento<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_pais<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_telefono<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
			{id:"terceros_crear_fax<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
			{id:"terceros_crear_email<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_email2<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_tipo_sociedad<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_contacto_Ppal<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_telefono_cp<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
			{id:"terceros_crear_cliente_ID<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_cliente_ID2<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_contacto_sec<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_telefono_cs<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
			{id:"terceros_crear_vendedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_cobrador<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_terminos<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_cupo_credito<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_lista_precios<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"terceros_crear_cupo_adicional<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_notas<?php echo $NumOfID ?>", type:""},
			{id:"terceros_crear_garantias<?php echo $NumOfID ?>", type:"jqxGrid"},
		];
		
		if (true == <?php echo $CupoHide ?>)
		{
			EnableDisableJSON = [
				{id:"terceros_crear_clasificacion<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_tipo<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_grupo<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_cliente<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_direccion<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_barrio<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_ciudad<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_departamento<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_pais<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_telefono<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
				{id:"terceros_crear_fax<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
				{id:"terceros_crear_email<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_email2<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_tipo_sociedad<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_contacto_Ppal<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_telefono_cp<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
				{id:"terceros_crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"terceros_crear_cliente_ID2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"terceros_crear_contacto_sec<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_telefono_cs<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
				{id:"terceros_crear_vendedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_cobrador<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_terminos<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_lista_precios<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_fecha_notas<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
				//{id:"terceros_crear_notas<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"terceros_crear_garantias<?php echo $NumOfID ?>", type:"jqxGrid"},
			];
		}
		else
		{
			EnableDisableJSON = [
				{id:"terceros_crear_clasificacion<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_tipo<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_grupo<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_cliente<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_direccion<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_barrio<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_ciudad<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_departamento<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_pais<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_telefono<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
				{id:"terceros_crear_fax<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
				{id:"terceros_crear_email<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_email2<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_tipo_sociedad<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_contacto_Ppal<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_telefono_cp<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
				{id:"terceros_crear_cliente_ID<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"terceros_crear_cliente_ID2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"terceros_crear_contacto_sec<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_telefono_cs<?php echo $NumOfID ?>", type:"jqxMaskedInput"},
				{id:"terceros_crear_vendedor<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_cobrador<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_terminos<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_cupo_credito<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"terceros_crear_vigencia_cupo<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
				{id:"terceros_crear_cupo_adicional<?php echo $NumOfID ?>", type:"jqxNumberInput"},
				{id:"terceros_crear_vigencia_adicional<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
				{id:"terceros_crear_lista_precios<?php echo $NumOfID ?>", type:"jqxComboBox"},
				{id:"terceros_crear_fecha_notas<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
				//{id:"terceros_crear_notas<?php echo $NumOfID ?>", type:""},
				{id:"terceros_crear_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
				{id:"terceros_crear_garantias<?php echo $NumOfID ?>", type:"jqxGrid"},
			];
		}
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
			data:{"Terceros_Modificar":"<?php echo $ClienteID ?>"},
			url: "modulos/datos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				
				if (records[0]["Foto"] != "")
					ShowPhoto(records[0]["Foto"], 1);
					
				ID_Exception = records[0]["ClienteID"]+"-"+records[0]["ClienteID2"];
				$("#terceros_crear_clasificacion<?php echo $NumOfID ?>").val(records[0]["Clasificacion"]);
				//
				TipoSource.data = {"Terceros_Tipo": records[0]["Clasificacion"]};
				var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource);
				$("#terceros_crear_tipo<?php echo $NumOfID ?>").jqxComboBox({source: TipoDataAdapter});
				$("#terceros_crear_tipo<?php echo $NumOfID ?>").on('bindingComplete', function (event) {
					$("#terceros_crear_tipo<?php echo $NumOfID ?>").val(records[0]["Tipo"]);
				});
				//
				GrupoSource.data = {"Terceros_Grupo": records[0]["Clasificacion"]};
				var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
				$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox({source: GrupoDataAdapter});
				//
				var len = records[0]["Grupo"].length;
				for (var i = 0; i < len; i++) {
					$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox('checkItem', records[0]["Grupo"][i]["Grupo"]);
				}
				
				$("#terceros_crear_cliente<?php echo $NumOfID ?>").val(records[0]["Nombre"]);
				$("#terceros_crear_direccion<?php echo $NumOfID ?>").val(records[0]["Direccion"]);
				$("#terceros_crear_barrio<?php echo $NumOfID ?>").val(records[0]["Barrio"]);
				$("#terceros_crear_ciudad<?php echo $NumOfID ?>").val(records[0]["Ciudad"]);
				$("#terceros_crear_departamento<?php echo $NumOfID ?>").val(records[0]["Departamento"]);
				$("#terceros_crear_pais<?php echo $NumOfID ?>").val(records[0]["Pais"]);
				$("#terceros_crear_telefono<?php echo $NumOfID ?>").val(records[0]["Telefono"]);
				$("#terceros_crear_fax<?php echo $NumOfID ?>").val(records[0]["Fax"]);
				$("#terceros_crear_email<?php echo $NumOfID ?>").val(records[0]["Email"]);
				$("#terceros_crear_email2<?php echo $NumOfID ?>").val(records[0]["Email2"]);
				$("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").jqxComboBox('selectItem', records[0]["Tipo_Sociedad"]);
				$("#terceros_crear_contacto_Ppal<?php echo $NumOfID ?>").val(records[0]["Contacto_P"]);
				$("#terceros_crear_telefono_cp<?php echo $NumOfID ?>").val(records[0]["Telefono_CP"]);
				$("#terceros_crear_tipo_id<?php echo $NumOfID ?>").val(records[0]["Tipo_Doc"]);
				$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").val(records[0]["ClienteID"]);
				$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").val(records[0]["ClienteID2"]);
				$("#terceros_crear_contacto_sec<?php echo $NumOfID ?>").val(records[0]["Contacto_S"]);
				$("#terceros_crear_telefono_cs<?php echo $NumOfID ?>").val(records[0]["Telefono_CS"]);
				$("#terceros_crear_vendedor<?php echo $NumOfID ?>").val(records[0]["VendedorID"]);
				$("#terceros_crear_cobrador<?php echo $NumOfID ?>").val(records[0]["CobradorID"]);
				$("#terceros_crear_terminos<?php echo $NumOfID ?>").val(records[0]["Terminos"]);
				$("#terceros_crear_lista_precios<?php echo $NumOfID ?>").val(records[0]["ListaP"]);
				$("#terceros_crear_fecha_notas<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Notas"])));
				//
				$("#terceros_crear_cupo_credito<?php echo $NumOfID ?>").val(records[0]["Cupo_Credito"]);
				$("#terceros_crear_vigencia_cupo<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Vigencia_Cupo_Credito"])));
				$("#terceros_crear_cupo_activado<?php echo $NumOfID ?>").val(records[0]["Cupo_Credito_Activado"]);
				$("#terceros_crear_cupo_activado_por<?php echo $NumOfID ?>").val(records[0]["Activado_Por"]);
				$("#terceros_crear_cupo_adicional<?php echo $NumOfID ?>").val(records[0]["Cupo_Adicional"]);
				$("#terceros_crear_vigencia_adicional<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Vigencia_Cupo_Adicional"])));
				$("#terceros_crear_cartera<?php echo $NumOfID ?>").val(records[0]["Estado_Cuenta"]);
				$("#terceros_crear_notas<?php echo $NumOfID ?>").val(records[0]["Notas"]);
				//
				var len = records[0]["Garantia"].length;
				for (var i = 0; i < len; i++)
				{
					var datinfo = $("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
					var count = datinfo.rowscount;
					for (var a = 0; a < count; a++) {
						var value = $("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('getcellvalue', a, "Garantia");
						if (value == records[0]["Garantia"][i]["Garantia"]) {
							var datarow = {};
							datarow["Apply"] = records[0]["Garantia"][i]["Apply"];
							datarow["Garantia"] = records[0]["Garantia"][i]["Garantia"];
							datarow["Imagen"] = records[0]["Garantia"][i]["Imagen"];
							datarow["Ok"] = records[0]["Garantia"][i]["Ok"];
							$("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('updaterow', a, datarow);
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
	
	$("#terceros_crear_clasificacion<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: CategoriaDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Clasificacion',
		valueMember: 'Clasificacion'
	});
	$("#terceros_crear_clasificacion<?php echo $NumOfID ?>").on('change', function (event) {
		if (event.args) {
			var index = event.args.index;
			var item = event.args.item;
			var label = item.label;
			Clasificacion = item.value;
			if (Clasificacion == "Proveedor")
				$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox({ disabled: false});
			else
				$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox({ disabled: true});
			
			//$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox('uncheckAll');
			$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
		else
		{
			$("#terceros_crear_clasificacion<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox({ disabled: true});
		}
	});
	$("#terceros_crear_clasificacion<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			Clasificacion = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				TipoSource.data = {"Terceros_Tipo": Clasificacion};
				var TipoDataAdapter = new $.jqx.dataAdapter(TipoSource);
				$("#terceros_crear_tipo<?php echo $NumOfID ?>").jqxComboBox({source: TipoDataAdapter});
				GrupoSource.data = {"Terceros_Grupo": Clasificacion};
				var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
				$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox({source: GrupoDataAdapter});
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
	
	$("#terceros_crear_tipo<?php echo $NumOfID ?>").jqxComboBox(
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
	$("#terceros_crear_tipo<?php echo $NumOfID ?>").on('change', function (event) {
		if (!event.args) {
			$("#terceros_crear_tipo<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
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
	
	$("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
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
	$("#terceros_crear_grupo<?php echo $NumOfID ?>").on('checkChange', function (event) {
		if (event.args) {
			var item = event.args.item;
			if (item)
			{
				var valueelement = item.value;
				var labelelement = item.label;
				var checkedelement = item.checked;

				var items = $("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox('getCheckedItems');
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
	
	$("#terceros_crear_cliente<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 485,
	});
	
	$("#terceros_crear_direccion<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 485,
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
	
	$("#terceros_crear_barrio<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
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
	
	$("#terceros_crear_ciudad<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		height: 20,
		width: 150,
		source: CiudadDataDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Ciudad',
		selectedIndex: -1,
		displayMember: 'Ciudad',
		valueMember: 'Ciudad'
	});
	$("#terceros_crear_ciudad<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args) {
			Ciudad = event.args.item.value;
		}
	});
	$("#terceros_crear_ciudad<?php echo $NumOfID ?>").bind('select', function (event) {
		if (event.args) {
			Ciudad = event.args.item.value;
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#terceros_crear_departamento<?php echo $NumOfID ?>").val(Ciudad);
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
						$("#terceros_crear_departamento<?php echo $NumOfID ?>").val(records[0]["Departamento"]);
						$("#terceros_crear_pais<?php echo $NumOfID ?>").val(records[0]["Pais"]);
					}
				});
				clearTimeout(Timer1);
			},350);
		}
	});
	
	$("#terceros_crear_departamento<?php echo $NumOfID ?>").jqxInput(
	{
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true
	});
	
	$("#terceros_crear_pais<?php echo $NumOfID ?>").jqxInput(
	{
		theme: mytheme,
		height: 20,
		width: 150,
		disabled: true
	});
	
	$("#terceros_crear_telefono<?php echo $NumOfID ?>").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 150,
		mask: '(###)#######',
	});
	
	$("#terceros_crear_fax<?php echo $NumOfID ?>").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 150,
		mask: '(###)#######',
	});
	
	$("#terceros_crear_email<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#terceros_crear_email2<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
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
	
	$("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").jqxComboBox(
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
	$("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").bind('change', function (event)
	{
		if (!event.args)
		{
			$("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#terceros_crear_tipo_id<?php echo $NumOfID ?>").html("Documento");
			$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").val('0');
			$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").val('0');
			$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").jqxNumberInput({ disabled: true });
		}
	});
	$("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").bind('select', function (event)
	{
		if (event.args)
		{
			switch(event.args.item.index)
			{
				case 0:
					$("#terceros_crear_tipo_id<?php echo $NumOfID ?>").html("NIT");
					$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").jqxNumberInput({ digits: 9 });
					$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").jqxNumberInput({ disabled: false });
				break;
				case 1:
					$("#terceros_crear_tipo_id<?php echo $NumOfID ?>").html("Cedula");
					$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").jqxNumberInput({ digits: 10 });
					$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").jqxNumberInput({ disabled: true });
				break;
				case 2:
					$("#terceros_crear_tipo_id<?php echo $NumOfID ?>").html("RUT");
					$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").jqxNumberInput({ digits: 10 });
					$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").jqxNumberInput({ disabled: false });
				break;
				case 3:
					$("#terceros_crear_tipo_id<?php echo $NumOfID ?>").html("T. I.");
					$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").jqxNumberInput({ digits: 11/*, groupSize: 6, groupSeparator: "-"*/ });
					$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").jqxNumberInput({ disabled: true });
				break;
			}
		}
	});
	
	$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").jqxNumberInput({
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
	$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").on("change", function (event)
	{
		if (event.args)
		{
			var value = event.args.value;
			if ($("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").jqxNumberInput('disabled') == false)
				value = value + "-" +$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").val();
			
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
						$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
						ID_Incorrecto = true;
						Alerts_Box("El ID ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
						WaitClick_NumberInput("terceros_crear_cliente_ID<?php echo $NumOfID ?>");
					}
					else
					{
						$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
						ID_Incorrecto = false;
					}
				}
			});
		}
	});
	
	$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 12,
		digits: 1,
		decimalDigits: 0,
		disabled: true
	});
	$("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").on("change", function (event)
	{
		if (event.args)
		{
			var value = $("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").val();
			value = value + "-" + event.args.value;
			
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
						$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").addClass("jqx-validator-error-element");
						ID_Incorrecto = true;
						Alerts_Box("El ID ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
						WaitClick_NumberInput("terceros_crear_cliente_ID<?php echo $NumOfID ?>");
					}
					else
					{
						$("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").removeClass("jqx-validator-error-element");
						ID_Incorrecto = false;
					}
				}
			});
		}
	});
	
	$("#terceros_crear_telefono_cp<?php echo $NumOfID ?>").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 150,
		mask: '(###)#######',
	});
	
	$("#terceros_crear_contacto_Ppal<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#terceros_crear_contacto_sec<?php echo $NumOfID ?>").jqxInput({
		theme: mytheme,
		height: 20,
		width: 150,
	});
	
	$("#terceros_crear_telefono_cs<?php echo $NumOfID ?>").jqxMaskedInput({
		theme: mytheme,
		height: 20,
		width: 150,
		mask: '(###)#######',
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
	
	$("#terceros_crear_vendedor<?php echo $NumOfID ?>").jqxComboBox(
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
	$("#terceros_crear_vendedor<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_crear_vendedor<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#terceros_crear_cobrador<?php echo $NumOfID ?>").jqxComboBox(
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
	$("#terceros_crear_cobrador<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_crear_cobrador<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
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
	
	$("#terceros_crear_terminos<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: TerminosDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Terminos',
		selectedIndex: -1,
		displayMember: 'Termino',
		valueMember: 'Termino'
	});
	$("#terceros_crear_terminos<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_crear_terminos<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#terceros_crear_cupo_credito<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 160,
		symbol: '$',
		digits: 15,
		max: 999999999999999
	});
	
	$("#terceros_crear_cupo_adicional<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 160,
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
	
	$("#terceros_crear_lista_precios<?php echo $NumOfID ?>").jqxComboBox(
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
	$("#terceros_crear_lista_precios<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#terceros_crear_lista_precios<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#terceros_crear_vigencia_cupo<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#terceros_crear_vigencia_adicional<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	
	$("#terceros_crear_fecha_notas<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
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
	
	$("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid({
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
	$("#terceros_crear_garantias<?php echo $NumOfID ?>").on("bindingcomplete", function (event)
	{
		$("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('hidecolumn', 'Imagen');
	});
	
	$("#terceros_crear_garantias<?php echo $NumOfID ?>").bind('cellvaluechanged', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.newvalue;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Apply") {
			if (value == true) {
				UploadID = rowBoundIndex;
				$("#terceros_crear_upload_docs_window<?php echo $NumOfID ?>").jqxWindow('open');
			}
		}
	});
	$("#terceros_crear_garantias<?php echo $NumOfID ?>").on('rowselect', function (event)
	{
		var args = event.args;
		var rowBoundIndex = args.rowindex;
		var rowData = args.row;
		//---
		ShowPhoto(rowData.Imagen, 2);
	});
	
	$("#terceros_crear_docs_delete<?php echo $NumOfID ?>").click(function() {
		//Clean
		var index = $("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('getselectedrowindex');
		var data = $("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('getrowdata', index);
		var datarow = {};
		datarow["Apply"] = false;
		datarow["Garantia"] = data.Garantia
		datarow["Imagen"] = "";
		datarow["Ok"] = data.Ok;
		$("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('updaterow', index, datarow);
		
		var img = "<img width=\"200\" height=\"200\" src=\"images/search.png\" alt=\"Foto del Cliente\" style=\"margin: 0px 15px;\"/>";
		$("#terceros_crear_docs<?php echo $NumOfID ?>").html(img);
	});
	
	$("#terceros_crear_cliente_foto_delete<?php echo $NumOfID ?>").click(function() {
		//Clean Img
		var img = "<img id=\"terceros_crear_cliente_foto_ID<?php echo $NumOfID ?>\" width=\"200\" height=\"200\" src=\"images/UserIcon.png\" alt=\"Foto del Cliente\" style=\"margin: 0px 20px;\"/>";
		$("#terceros_crear_cliente_foto<?php echo $NumOfID ?>").html(img);
	});
	
	$("#terceros_crear_cliente_foto_edit<?php echo $NumOfID ?>").click(function() {
		$("#terceros_crear_upload_docs_window<?php echo $NumOfID ?>").jqxWindow('open');
	});
	
	$("#terceros_crear_cliente_foto<?php echo $NumOfID ?>").click(function()
	{
		var img_src = document.getElementById("terceros_crear_cliente_foto_ID<?php echo $NumOfID ?>").src;
		var img = "<img src=\""+img_src+"\" alt=\"Foto del Cliente\"/>";
		$("#terceros_crear_show_docs_content<?php echo $NumOfID ?>").html(img);
		$("#terceros_crear_show_docs_window<?php echo $NumOfID ?>").jqxWindow('open');
	});
	
	
	$("#terceros_crear_upload_docs_window<?php echo $NumOfID ?>").jqxWindow({
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
			$("#terceros_crear_cliente_upload_docs<?php echo $NumOfID ?>").jqxFileUpload({
				theme: mytheme,
				width: 290,
				multipleFilesUpload: false,
				browseTemplate: 'success',
				uploadTemplate: 'primary',
				cancelTemplate: 'danger',
				uploadUrl: 'modulos/guardar.php',
				fileInputName: 'Image_Uploads',
			});
			$("#terceros_crear_cliente_upload_docs<?php echo $NumOfID ?>").on('uploadEnd', function (event)
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
						$("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('setcellvalue', UploadID, "Imagen", Tmp);
						ShowPhoto(Tmp, 2, true);
					}
					$("#terceros_crear_upload_docs_window<?php echo $NumOfID ?>").jqxWindow('close');
				}
				else
				{
					Alerts_Box(""+serverResponce, 3);
					WaitClick_WindowClose("terceros_crear_upload_docs_window<?php echo $NumOfID ?>");
				}
			});
		}
	});
	$("#terceros_crear_upload_docs_window<?php echo $NumOfID ?>").on('close', function (event) {
		// Clean Variable
		UploadID = -1;
	});
	
	function ShowPhoto (name, type, temp)
	{
		if (type == 1)
		{
			if (temp)
				var img = "<img id=\"terceros_crear_cliente_foto_ID<?php echo $NumOfID ?>\" width=\"200\" height=\"200\" src=\""+name+"\" alt=\"Foto del Cliente\" style=\"margin: 0px 20px;\"/>";
			else
				var img = "<img id=\"terceros_crear_cliente_foto_ID<?php echo $NumOfID ?>\" width=\"200\" height=\"200\" src=\"images/"+name+"\" alt=\"Foto del Cliente\" style=\"margin: 0px 20px;\"/>";
			$("#terceros_crear_cliente_foto<?php echo $NumOfID ?>").html(img);
		}
		else
		{
			if (name != "") {
				if (temp)
					var img = "<img id=\"terceros_crear_docs_img<?php echo $NumOfID ?>\" width=\"200\" height=\"200\" src=\""+name+"\" alt=\"Documento del Cliente\" style=\"margin: 0px 15px;\"/>";
				else
					var img = "<img id=\"terceros_crear_docs_img<?php echo $NumOfID ?>\" width=\"200\" height=\"200\" src=\"images/"+name+"\" alt=\"Documento del Cliente\" style=\"margin: 0px 15px;\"/>";
				$("#terceros_crear_docs<?php echo $NumOfID ?>").html(img);
			}
			else {
				var img = "<img id=\"terceros_crear_docs_img<?php echo $NumOfID ?>\" width=\"200\" height=\"200\" src=\"images/search.png\" alt=\"Documento del Cliente\" style=\"margin: 0px 15px;\"/>";
				$("#terceros_crear_docs<?php echo $NumOfID ?>").html(img);
			}
		}
	};
	
	$("#terceros_crear_docs<?php echo $NumOfID ?>").click(function()
	{
		var img_src = document.getElementById("terceros_crear_docs_img<?php echo $NumOfID ?>").src;
		if (img_src.indexOf("images/search.png") > 0)
		{
			Alerts_Box("Debe Cargar una Imagen desde Garantias.", 4);
			return;
		}
		
		var img = "<img src=\""+img_src+"\" alt=\"Documento del Cliente\"/>";
		$("#terceros_crear_show_docs_content<?php echo $NumOfID ?>").html(img);
		$("#terceros_crear_show_docs_window<?php echo $NumOfID ?>").jqxWindow('open');
	});
	
	$("#terceros_crear_show_docs_window<?php echo $NumOfID ?>").jqxWindow({
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
	
	function CrearTercero()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (ID_Incorrecto == true) {
			Alerts_Box("El ID ingresado, ya Existe!<br/>Favor Ingresar Otro para Continuar.", 3);
			WaitClick_NumberInput("terceros_crear_cliente_ID<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_clasificacion<?php echo $NumOfID ?>").val() == "" || $("#terceros_crear_clasificacion<?php echo $NumOfID ?>").val() <= 0)
		{
			Alerts_Box("Debe Seleccionar un tipo de Clasificacion.", 3);
			WaitClick_Combobox("terceros_crear_clasificacion<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_cliente<?php echo $NumOfID ?>").val() == "" || $("#terceros_crear_cliente<?php echo $NumOfID ?>").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un Nombre.", 3);
			WaitClick_Input("terceros_crear_cliente<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_direccion<?php echo $NumOfID ?>").val() == "" || $("#terceros_crear_direccion<?php echo $NumOfID ?>").val() <= 0)
		{
			Alerts_Box("Debe Ingresar una Direccion.", 3);
			WaitClick_Input("terceros_crear_direccion<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_telefono<?php echo $NumOfID ?>").val() == "(___)_______" )
		{
			Alerts_Box("Debe Ingresar un Telefono.", 3);
			WaitClick_MaskedInput("terceros_crear_telefono<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_contacto_Ppal<?php echo $NumOfID ?>").val() == "" || $("#terceros_crear_contacto_Ppal<?php echo $NumOfID ?>").val() <= 0)
		{
			Alerts_Box("Debe Ingresar un Contacto Principal.", 3);
			WaitClick_Input("terceros_crear_contacto_Ppal<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_telefono_cp<?php echo $NumOfID ?>").val() == "(___)_______")
		{
			Alerts_Box("Debe Ingresar un Telefono del Contacto Principal.", 3);
			WaitClick_MaskedInput("terceros_crear_telefono_cp<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").val() == "" || $("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").val() < 1)
		{
			Alerts_Box("Debe Seleccionar un Tipo de Sociedad.", 3);
			WaitClick_Combobox("terceros_crear_tipo_sociedad<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").val() == "" || $("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").val() < 1)
		{
			Alerts_Box("Debe Ingresar un ID.", 3);
			WaitClick_NumberInput("terceros_crear_cliente_ID<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if ($("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").jqxNumberInput('disabled') == false)
		{
			if ($("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").val() < 0)
			{
				Alerts_Box("Debe Ingresar el Numero de Verificacion del ID.", 3);
				WaitClick_NumberInput("terceros_crear_cliente_ID2<?php echo $NumOfID ?>");
				Locked = false;
				return;
			}
		}
		
		var datinfo = $("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		// Get Checked Group
		var items = $("#terceros_crear_grupo<?php echo $NumOfID ?>").jqxComboBox('getCheckedItems');
		$.each(items, function (index) {
			var GrupoValues = {};
			GrupoValues["Grupo"] = this.label;
			GrupoArray[index] = GrupoValues; 
		});
		
		if (count <= 0)
		{
			var array = {};
			array["Foto_Cliente"] = GetImage("terceros_crear_cliente_foto_ID<?php echo $NumOfID ?>");
			array["Clasificacion"] = $("#terceros_crear_clasificacion<?php echo $NumOfID ?>").val();
			array["Tipo"] = $("#terceros_crear_tipo<?php echo $NumOfID ?>").val();
			array["Grupo"] = GrupoArray;
			array["Nombre"] = $("#terceros_crear_cliente<?php echo $NumOfID ?>").val();
			array["Direccion"] = $("#terceros_crear_direccion<?php echo $NumOfID ?>").val();
			array["Barrio"] = $("#terceros_crear_barrio<?php echo $NumOfID ?>").val();
			array["Ciudad"] = $("#terceros_crear_ciudad<?php echo $NumOfID ?>").val();
			array["Departamento"] = $("#terceros_crear_departamento<?php echo $NumOfID ?>").val();
			array["Pais"] = $("#terceros_crear_pais<?php echo $NumOfID ?>").val();
			array["Telefono"] = $("#terceros_crear_telefono<?php echo $NumOfID ?>").val();
			array["Fax"] = $("#terceros_crear_fax<?php echo $NumOfID ?>").val();
			array["Email"] = $("#terceros_crear_email<?php echo $NumOfID ?>").val();
			array["Email2"] = $("#terceros_crear_email2<?php echo $NumOfID ?>").val();
			array["Tipo_Sociedad"] = $("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").val();
			var Doc = document.getElementById("terceros_crear_tipo_id<?php echo $NumOfID ?>").innerHTML;
			array["Tipo_Doc"] = Doc;
			var Final_ClienteID = "" + $("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").val();
			if (Doc == "NIT" || Doc == "RUT")
				Final_ClienteID = Final_ClienteID +"-"+ $("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").val();
			array["ClienteID"] = Final_ClienteID;
			array["Telefono_CP"] = $("#terceros_crear_telefono_cp<?php echo $NumOfID ?>").val();
			array["Contacto_P"] = $("#terceros_crear_contacto_Ppal<?php echo $NumOfID ?>").val();
			array["Contacto_S"] = $("#terceros_crear_contacto_sec<?php echo $NumOfID ?>").val();
			array["Telefono_CS"] = $("#terceros_crear_telefono_cs<?php echo $NumOfID ?>").val();
			array["VendedorID"] = $("#terceros_crear_vendedor<?php echo $NumOfID ?>").val();
			array["CobradorID"] = $("#terceros_crear_cobrador<?php echo $NumOfID ?>").val();
			array["Terminos"] = $("#terceros_crear_terminos<?php echo $NumOfID ?>").val();
			array["Cupo_Credito"] = $("#terceros_crear_cupo_credito<?php echo $NumOfID ?>").val();
			array["Cupo_Adicional"] = $("#terceros_crear_cupo_adicional<?php echo $NumOfID ?>").val();
			array["ListaP"] = $("#terceros_crear_lista_precios<?php echo $NumOfID ?>").val();
			array["Vigencia_Cupo_Credito"] = GetFormattedDate($("#terceros_crear_vigencia_cupo<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
			array["Vigencia_Cupo_Adicional"] = GetFormattedDate($("#terceros_crear_vigencia_adicional<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
			array["Fecha_Notas"] = GetFormattedDate($("#terceros_crear_fecha_notas<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
			array["Notas"] = $("#terceros_crear_notas<?php echo $NumOfID ?>").val();
			//
			myarray[0] = array;
		}
		else
		{
			for (i = 0; i < count; i++)
			{
				var array = {};
				var currentRow = $("#terceros_crear_garantias<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
				
				if (currentRow.Apply == true)
				{
					array["Garantia"] = currentRow.Garantia;
					array["Imagen"] = currentRow.Imagen;
					array["Ok"] = currentRow.Ok;
				}
				if (i==0) // static values
				{
					var Imagen = GetImage("terceros_crear_cliente_foto_ID<?php echo $NumOfID ?>");
					if (Imagen == "images/UserIcon.png")
						Imagen = "";
					array["Foto_Cliente"] = Imagen;
					array["Clasificacion"] = $("#terceros_crear_clasificacion<?php echo $NumOfID ?>").val();
					array["Tipo"] = $("#terceros_crear_tipo<?php echo $NumOfID ?>").val();
					array["Grupo"] = GrupoArray;
					array["Nombre"] = $("#terceros_crear_cliente<?php echo $NumOfID ?>").val();
					array["Direccion"] = $("#terceros_crear_direccion<?php echo $NumOfID ?>").val();
					array["Barrio"] = $("#terceros_crear_barrio<?php echo $NumOfID ?>").val();
					array["Ciudad"] = $("#terceros_crear_ciudad<?php echo $NumOfID ?>").val();
					array["Departamento"] = $("#terceros_crear_departamento<?php echo $NumOfID ?>").val();
					array["Pais"] = $("#terceros_crear_pais<?php echo $NumOfID ?>").val();
					array["Telefono"] = $("#terceros_crear_telefono<?php echo $NumOfID ?>").val();
					array["Fax"] = $("#terceros_crear_fax<?php echo $NumOfID ?>").val();
					array["Email"] = $("#terceros_crear_email<?php echo $NumOfID ?>").val();
					array["Email2"] = $("#terceros_crear_email2<?php echo $NumOfID ?>").val();
					array["Tipo_Sociedad"] = $("#terceros_crear_tipo_sociedad<?php echo $NumOfID ?>").val();
					var Doc = document.getElementById("terceros_crear_tipo_id<?php echo $NumOfID ?>").innerHTML;
					array["Tipo_Doc"] = Doc;
					var Final_ClienteID = "" + $("#terceros_crear_cliente_ID<?php echo $NumOfID ?>").val();
					if (Doc == "NIT" || Doc == "RUT")
						Final_ClienteID = Final_ClienteID +"-"+ $("#terceros_crear_cliente_ID2<?php echo $NumOfID ?>").val();
					array["ClienteID"] = Final_ClienteID;
					array["Telefono_CP"] = $("#terceros_crear_telefono_cp<?php echo $NumOfID ?>").val();
					array["Contacto_P"] = $("#terceros_crear_contacto_Ppal<?php echo $NumOfID ?>").val();
					array["Contacto_S"] = $("#terceros_crear_contacto_sec<?php echo $NumOfID ?>").val();
					array["Telefono_CS"] = $("#terceros_crear_telefono_cs<?php echo $NumOfID ?>").val();
					array["VendedorID"] = $("#terceros_crear_vendedor<?php echo $NumOfID ?>").val();
					array["CobradorID"] = $("#terceros_crear_cobrador<?php echo $NumOfID ?>").val();
					array["Terminos"] = $("#terceros_crear_terminos<?php echo $NumOfID ?>").val();
					array["Cupo_Credito"] = $("#terceros_crear_cupo_credito<?php echo $NumOfID ?>").val();
					array["Cupo_Adicional"] = $("#terceros_crear_cupo_adicional<?php echo $NumOfID ?>").val();
					array["ListaP"] = $("#terceros_crear_lista_precios<?php echo $NumOfID ?>").val();
					array["Vigencia_Cupo_Credito"] = GetFormattedDate($("#terceros_crear_vigencia_cupo<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
					array["Vigencia_Cupo_Adicional"] = GetFormattedDate($("#terceros_crear_vigencia_adicional<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
					array["Fecha_Notas"] = GetFormattedDate($("#terceros_crear_fecha_notas<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
					array["Notas"] = $("#terceros_crear_notas<?php echo $NumOfID ?>").val();
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
			dataType: 'text',
			type: 'GET',
			url: "modulos/guardar.php",
			data: {"Terceros_Crear":myarray},
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				if (data[0]["MESSAGE"] == "OK")
				{
					ClickOK = false;
					Alerts_Box("Datos Guardados con Exito!<br />Presione Aceptar para Limpiar o Cancelar para mantener los datos en Pantalla.", 2, true);
					EnableDisableAll(true);
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
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!<br />Intente luego de unos segundos...", 3);
			}
		});
	};
	
	$("#terceros_crear_guardar<?php echo $NumOfID ?>").jqxButton({width: 150, template: "info"});
	$("#terceros_crear_guardar<?php echo $NumOfID ?>").bind('click', function ()
	{
		CrearTercero();
	});
	
	$("#terceros_crear_nuevo<?php echo $NumOfID ?>").jqxButton({width: 150, template: "success"});
	$("#terceros_crear_nuevo<?php echo $NumOfID ?>").bind('click', function ()
	{
		ClearDocument();
	});
	SetDefaultDate();
	
	if ("<?php echo $ClienteID ?>" != "")
	{
		EnableDisableAll(true);
		$("#terceros_crear_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		LoadValues();
	}
	else
		CheckRefresh();
	
	if (true == <?php echo $CupoHide ?>)
	{
		$("#terceros_crear_cupo_credito<?php echo $NumOfID ?>").jqxNumberInput({disabled: <?php echo $CupoHide ?>});
		$("#terceros_crear_vigencia_cupo<?php echo $NumOfID ?>").jqxDateTimeInput({disabled: <?php echo $CupoHide ?>});
		$("#terceros_crear_cupo_adicional<?php echo $NumOfID ?>").jqxNumberInput({disabled: <?php echo $CupoHide ?>});
		$("#terceros_crear_vigencia_adicional<?php echo $NumOfID ?>").jqxDateTimeInput({disabled: <?php echo $CupoHide ?>});
	
		/*var All_IDs = document.querySelectorAll('*[id^="terceros_crear_"]');
		var len = All_IDs.length;
		var Tmp = "";
		
		for (i = 0; i < len; i++)
		{
			Tmp = All_IDs[0].id;
			Tmp += "2";
			All_IDs[0].id = Tmp;
		}*/
	}
});
</script>
<div id="terceros_crear_upload_docs_window<?php echo $NumOfID ?>">
	<div style="height:20px; font-size: 16px; color: #707070;">
	</div>
	<div>
		<div id="terceros_crear_cliente_upload_docs<?php echo $NumOfID ?>"></div>
	</div>
</div>
<div id="terceros_crear_show_docs_window<?php echo $NumOfID ?>">
	<div style="height:15px;">
	</div>
	<div>
		<div id="terceros_crear_show_docs_content<?php echo $NumOfID ?>"></div>
	</div>
</div>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td colspan="2" rowspan="7">
				<div id="terceros_crear_cliente_foto_delete<?php echo $NumOfID ?>" style="width:16px; height:16px; margin: 0px 0px 0px 20px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/close_white.png'" onmouseout="this.src='images/close_black.png'" width="16" height="16" src="images/close_black.png" alt="Eliminar Foto" style="margin: 0px;"/>
				</div>
				<div id="terceros_crear_cliente_foto_edit<?php echo $NumOfID ?>" style="width:16px; height:16px; margin: 0px 0px 0px 40px; cursor:pointer; position: absolute;">
					<img onmouseover="this.src='images/edit_white.png'" onmouseout="this.src='images/edit_black.png'" width="16" height="16" src="images/edit_black.png" alt="Cambiar Imagen"/>
				</div>
				<div id="terceros_crear_cliente_foto<?php echo $NumOfID ?>" style="cursor:pointer;">
					<img id="terceros_crear_cliente_foto_ID<?php echo $NumOfID ?>" width="200" height="200" src="images/UserIcon.png" alt="Foto del Cliente" style="margin: 0px 20px;"/>
				</div>
			</td>
			<td>
				Clasificacion
			</td>
			<td>
				<div id="terceros_crear_clasificacion<?php echo $NumOfID ?>"></div>
			</td>
			<td colspan="2">
				<li class="parte1_li_txt">
					Tipo&nbsp;
				</li>
				<li>
					<div id="terceros_crear_tipo<?php echo $NumOfID ?>"></div>
				</li>
				<li class="parte1_li_txt">
					&nbsp;Grupo&nbsp;
				</li>
				<li>
					<div id="terceros_crear_grupo<?php echo $NumOfID ?>"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td>
				Nombre
			</td>
			<td colspan="3">
				<input type="text" id="terceros_crear_cliente<?php echo $NumOfID ?>"/>
			</td>
		</tr>
		<tr>
			<td>
				Direccion
			</td>
			<td colspan="3">
				<input type="text" id="terceros_crear_direccion<?php echo $NumOfID ?>"/>
			</td>
		</tr>
		<tr>
			<td>
				Barrio
			</td>
			<td>
				<div id="terceros_crear_barrio<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Ciudad
			</td>
			<td>
				<div id="terceros_crear_ciudad<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Departamento
			</td>
			<td>
				<input type="text" id="terceros_crear_departamento<?php echo $NumOfID ?>"/>
			</td>
			<td>
				Pais
			</td>
			<td>
				<input type="text" id="terceros_crear_pais<?php echo $NumOfID ?>"/>
			</td>
		</tr>
		<tr>
			<td>
				Telefono
			</td>
			<td>
				<div id="terceros_crear_telefono<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Fax
			</td>
			<td>
				<div id="terceros_crear_fax<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				E-mail(1)
			</td>
			<td>
				<input type="text" id="terceros_crear_email<?php echo $NumOfID ?>"/>
			</td>
			<td>
				E-mail(2)
			</td>
			<td>
				<input type="text" id="terceros_crear_email2<?php echo $NumOfID ?>"/>
			</td>
		</tr>
		<tr>
			<td>
				Tipo Sociedad
			</td>
			<td>
				<div id="terceros_crear_tipo_sociedad<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Contacto(1)
			</td>
			<td>
				<input type="text" id="terceros_crear_contacto_Ppal<?php echo $NumOfID ?>"/>
			</td>
			<td>
				Telefono(1)
			</td>
			<td>
				<div id="terceros_crear_telefono_cp<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="terceros_crear_tipo_id<?php echo $NumOfID ?>">
					Documento
				</div>
			</td>
			<td>
				<li>
					<div id="terceros_crear_cliente_ID<?php echo $NumOfID ?>"></div>
				</li>
				<li class="parte1_li_txt">
				&nbsp;-&nbsp;
				</li>
				<li>
					<div id="terceros_crear_cliente_ID2<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<td>
				Contacto(2)
			</td>
			<td>
				<input type="text" id="terceros_crear_contacto_sec<?php echo $NumOfID ?>"/>
			</td>
			<td>
				Telefono(2)
			</td>
			<td>
				<div id="terceros_crear_telefono_cs<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Vendedor As
			</td>
			<td>
				<div id="terceros_crear_vendedor<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Cobrador As
			</td>
			<td>
				<div id="terceros_crear_cobrador<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Terminos
			</td>
			<td>
				<div id="terceros_crear_terminos<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">	
		<tr>
			<td>
				Cupo Credito
			</td>
			<td>
				<div id="terceros_crear_cupo_credito<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Vigencia Cupo
			</td>
			<td>
				<div id="terceros_crear_vigencia_cupo<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Lista de Precios &nbsp;
			</td>
			<td>
				<div id="terceros_crear_lista_precios<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Adicional
			</td>
			<td>
				<div id="terceros_crear_cupo_adicional<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Vigencia Ad.
			</td>
			<td>
				<div id="terceros_crear_vigencia_adicional<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Vigencia Notas
			</td>
			<td>
				<div id="terceros_crear_fecha_notas<?php echo $NumOfID ?>"></div>
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
				<div id="terceros_crear_garantias<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="terceros_crear_docs_delete<?php echo $NumOfID ?>" style="cursor:pointer; position: absolute;">
					<img width="16" height="16" src="images/close2.png" alt="Eliminar Foto" style="margin: 0px;"/>
				</div>
				<div id="terceros_crear_docs<?php echo $NumOfID ?>" style="cursor:pointer;">
					<img id="terceros_crear_docs_img<?php echo $NumOfID ?>" width="200" height="200" src="images/search.png" alt="Documento del Cliente" style="margin: 0px 15px;"/>
				</div>
			</td>
			<td>
				<textarea rows="12" cols="30" id="terceros_crear_notas<?php echo $NumOfID ?>" maxlength="100" style="resize:none;"></textarea>
			</td>
		</tr>
	</table>
</div>
<!-- PART 2 -->
<div id="Parte2">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px; margin-left: 545px;">
		<tr>
			<td>
				<input type="button" id="terceros_crear_guardar<?php echo $NumOfID ?>" value="Guardar"/>
			</td>
			<td style="padding-left:10px;">
				<input type="button" id="terceros_crear_nuevo<?php echo $NumOfID ?>" value="Nuevo"/>
			</td>
		</tr>
	</table>
</div>