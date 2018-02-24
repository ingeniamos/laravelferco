<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var OldState = "";
	var NewState = "";
	var Timer = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Produccion_Content");
	var Body = document.getElementById("Produccion_Procesos_Content");
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
				SystemMap("Procesos", true);
				
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
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Produccion" && $data[$i]["SubModulo"] == "Procesos" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#produccion_procesos_imprimir").jqxButton({ disabled: true });
					$("#produccion_procesos_exportar").jqxButton({ disabled: true });
		<?php
				}
			}
		}
	} ?>
	
	Day = Day;
	Day = Day + "";
	if (Day.length == 1)
	{
		Day = "0" + Day;
	};
	
	Month = Month - 1;
	Month = Month + "";
	if (Month.length == 1)
	{
		Month = "0" + Month;
	};
	
	var EstadoValues = [
		{"Estado":"Pendiente"},
		{"Estado":"Proceso"},
		{"Estado":"Finalizado"},
	];
	
	/*var ProcesoValues = [
		{"Proceso":"Trefilado"},
		{"Proceso":"Corte y Enderezado"},
		{"Proceso":"Electrosoldado"},
		{"Proceso":"Figurado"},
	];
	
	var ProcesoSource = {
		localdata: ProcesoValues,
		datatype: "json",
		datafields:[
			{ name: 'Proceso', type: 'string' }
		]
	};
	var ProcesoDataAdapter = new $.jqx.dataAdapter(ProcesoSource);
	
	$("#produccion_procesos_proceso").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		source: ProcesoDataAdapter,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Proceso',
		valueMember: 'Proceso',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#produccion_procesos_proceso").bind('change', function (event) {
		if (event.args)
		{
			OrdenSource.data = {"Produccion_Ordenes_Proc2": event.args.item.value};
			OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
			$("#produccion_procesos_ord_produccion").jqxComboBox({source: OrdenDataAdapter});	
		}
		else
		{
			OrdenSource.data = {"Produccion_Ordenes_Proc2":""};
			OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
			$("#produccion_procesos_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
			$("#produccion_procesos_proceso").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	$("#produccion_procesos_proceso").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});*/
	
	var OrdenSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Orden_Produccion', type: 'string'},
		],
		url: "modulos/datos.php",
		async: true
	};
	
	$("#produccion_procesos_ord_produccion").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		dropDownHeight: 100,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Orden_Produccion',
		valueMember: 'Orden_Produccion',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#produccion_procesos_ord_produccion").bind('change', function (event) {
		if (!event.args)
		{
			$("#produccion_procesos_ord_produccion").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	$("#produccion_procesos_ord_produccion").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				Add_Row();
				clearTimeout(Timer);
			},500);
		}
	});
	
	/*Starting Val*/
	OrdenSource.data = {"Produccion_Ordenes_Proc":"Pendiente"};
	OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
	$("#produccion_procesos_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		id: "ClienteID",
		url: "modulos/datos.php",
		async: true
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource);
	
	$("#produccion_procesos_cliente").jqxComboBox(
	{
		width: 358,
		height: 20,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#produccion_procesos_cliente").bind('change', function (event) {
		if (!event.args)
		{
			$("#produccion_procesos_cliente").jqxComboBox('clearSelection');
			$("#produccion_procesos_cliente_ID").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	$("#produccion_procesos_cliente").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#produccion_procesos_cliente_ID").val(event.args.item.value);
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#produccion_procesos_cliente_ID").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		theme: mytheme,
		source: ClienteDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#produccion_procesos_cliente_ID").bind('change', function (event) {
		if (!event.args)
		{
			$("#produccion_procesos_cliente").jqxComboBox('clearSelection');
			$("#produccion_procesos_cliente_ID").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	$("#produccion_procesos_cliente_ID").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer);
			Timer = setTimeout(function(){
				$("#produccion_procesos_cliente").val(event.args.item.value);
				Add_Row();
				clearTimeout(Timer);
			},350);
		}
	});
	
	$("#produccion_procesos_estado").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 100,
		source: EstadoValues,
		dropDownHeight: 100,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Estado',
		valueMember: 'Estado',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#produccion_procesos_estado").bind('change', function (event)
	{
		if (event.args)
		{
			OrdenSource.data = {"Produccion_Ordenes_Proc": event.args.item.value};
			OrdenDataAdapter = new $.jqx.dataAdapter(OrdenSource);
			$("#produccion_procesos_ord_produccion").jqxComboBox({source: OrdenDataAdapter});
			Add_Row();
		}
		else
		{
			$("#produccion_procesos_ord_produccion").jqxComboBox('clearSelection');
			$("#produccion_procesos_ord_produccion").jqxComboBox('clear');
			$("#produccion_procesos_estado").jqxComboBox('clearSelection');
			Add_Row();
		}
	});
	
	$("#produccion_procesos_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$("#produccion_procesos_fecha_ini").jqxDateTimeInput('setDate', new Date(Year, Month, Day));
	$('#produccion_procesos_fecha_ini').on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	$("#produccion_procesos_fecha_fin").jqxDateTimeInput(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
	});
	$('#produccion_procesos_fecha_fin').on('change', function (event) 
	{  
		if(event.args)
			Add_Row();
	});
	
	// ------------------------------------------ PARTE 2
	
	function Add_Row()
	{
		ItemsSource1.data = {
			"Produccion_Procesos":true,
			"Proceso":"Trefilado",
			"Ord_Produccion":$("#produccion_procesos_ord_produccion").val(),
			"ClienteID":$("#produccion_procesos_cliente_ID").val(),
			"Estado":$("#produccion_procesos_estado").val(),
			"Fecha_Ini":GetFormattedDate($("#produccion_procesos_fecha_ini").jqxDateTimeInput('getDate')),
			"Fecha_Fin":GetFormattedDate($("#produccion_procesos_fecha_fin").jqxDateTimeInput('getDate'))
		};
		var ItemsDataAdapter1 = new $.jqx.dataAdapter(ItemsSource1);
		
		ItemsSource2.data = {
			"Produccion_Procesos":true,
			"Proceso":"Corte y Enderezado",
			"Ord_Produccion":$("#produccion_procesos_ord_produccion").val(),
			"ClienteID":$("#produccion_procesos_cliente_ID").val(),
			"Estado":$("#produccion_procesos_estado").val(),
			"Fecha_Ini":GetFormattedDate($("#produccion_procesos_fecha_ini").jqxDateTimeInput('getDate')),
			"Fecha_Fin":GetFormattedDate($("#produccion_procesos_fecha_fin").jqxDateTimeInput('getDate'))
		};
		var ItemsDataAdapter2 = new $.jqx.dataAdapter(ItemsSource2);
		
		ItemsSource3.data = {
			"Produccion_Procesos":true,
			"Proceso":"Electrosoldado",
			"Ord_Produccion":$("#produccion_procesos_ord_produccion").val(),
			"ClienteID":$("#produccion_procesos_cliente_ID").val(),
			"Estado":$("#produccion_procesos_estado").val(),
			"Fecha_Ini":GetFormattedDate($("#produccion_procesos_fecha_ini").jqxDateTimeInput('getDate')),
			"Fecha_Fin":GetFormattedDate($("#produccion_procesos_fecha_fin").jqxDateTimeInput('getDate'))
		};
		var ItemsDataAdapter3 = new $.jqx.dataAdapter(ItemsSource3);
		
		ItemsSource4.data = {
			"Produccion_Procesos":true,
			"Proceso":"Figurado",
			"Ord_Produccion":$("#produccion_procesos_ord_produccion").val(),
			"ClienteID":$("#produccion_procesos_cliente_ID").val(),
			"Estado":$("#produccion_procesos_estado").val(),
			"Fecha_Ini":GetFormattedDate($("#produccion_procesos_fecha_ini").jqxDateTimeInput('getDate')),
			"Fecha_Fin":GetFormattedDate($("#produccion_procesos_fecha_fin").jqxDateTimeInput('getDate'))
		};
		var ItemsDataAdapter4 = new $.jqx.dataAdapter(ItemsSource4);
		
		$("#produccion_procesos_productos_grid1").jqxGrid({source: ItemsDataAdapter1});
		$("#produccion_procesos_productos_grid2").jqxGrid({source: ItemsDataAdapter2});
		$("#produccion_procesos_productos_grid3").jqxGrid({source: ItemsDataAdapter3});
		$("#produccion_procesos_productos_grid4").jqxGrid({source: ItemsDataAdapter4});
		
		/*var GridSource = 
		{
			datatype: "json",
			datafields:
			[
				{ name: 'ClienteID', type: 'string' },
				{ name: 'Interno', type: 'string' },
				{ name: 'Ord_Produccion', type: 'string' },
				{ name: 'Estado', type: 'string' },
				{ name: 'Fecha', type: 'string' },
				{ name: 'Proceso', type: 'string' },
				{ name: 'Avance', type: 'decimal' },
				{ name: 'DigitadorID', type: 'string' },
			],
			type: 'GET',
			data: {
				"Produccion_Procesos":true,
				"Ord_Produccion":$("#produccion_procesos_ord_produccion").val(),
				"ClienteID":$("#produccion_procesos_cliente_ID").val(),
				"Estado":$("#produccion_procesos_estado").val(),
				"Fecha_Ini":GetFormattedDate($("#produccion_procesos_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#produccion_procesos_fecha_fin").jqxDateTimeInput('getDate'))
			},
			url: "modulos/datos.php",
			async: true
		};
		var GridDataAdapter = new $.jqx.dataAdapter(GridSource,{
			autoBind: true,
			loadComplete: function () {
				var myrecords = GridDataAdapter.records;
				var len = myrecords.length;
				$("#produccion_procesos_productos_grid1").jqxGrid('clear');
				$("#produccion_procesos_productos_grid2").jqxGrid('clear');
				$("#produccion_procesos_productos_grid3").jqxGrid('clear');
				$("#produccion_procesos_productos_grid4").jqxGrid('clear');
				
				for (i = 0; i < len; i++)
				{
					if (myrecords[i]["Estado"] != "")
					{
						var datarow = [{
							"ClienteID":myrecords[i]["ClienteID"],
							"Interno":myrecords[i]["Interno"],
							"Ord_Produccion":myrecords[i]["Ord_Produccion"],
							"Estado":myrecords[i]["Estado"],
							"Fecha":new Date(SetFormattedDate(myrecords[i]["Fecha"])),
							"Proceso":myrecords[i]["Proceso"],
							"Avance":myrecords[i]["Avance"],
							"DigitadorID":myrecords[i]["DigitadorID"],
						}];
						switch(myrecords[i]["Proceso"])
						{
							case "Trefilado":
								$("#produccion_procesos_productos_grid1").jqxGrid("addrow", null, datarow, "first");
							break;
							case "Corte y Enderezado":
								$("#produccion_procesos_productos_grid3").jqxGrid("addrow", null, datarow, "first");
							break;
							case "Electrosoldado":
								$("#produccion_procesos_productos_grid2").jqxGrid("addrow", null, datarow, "first");
							break;
							case "Figurado":
								$("#produccion_procesos_productos_grid4").jqxGrid("addrow", null, datarow, "first");
							break;
						}
					}
				}
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});*/
	};
	
	var ItemsSource1 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Ord_Produccion', type: 'string' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Avance', type: 'decimal' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
		/*updaterow: function (rowid, rowdata, commit)
		{
			if (NewState == "" || NewState == undefined)
			{
				commit(true);
				return;
			}
			
			if (OldState == "Finalizado")
			{
				Alerts_Box("Un Movimiento Finalizado, no es posible Cambiar su estado.", 3);
				commit(false);
			}

			Alerts_Box("Esta a punto de cambiar el estado de esta orden<br />¿Desea Continuar?", 4, true);

			var CheckTimer = setInterval(function() {
				if (ClickOK == true) {
					clearInterval(CheckTimer);
					ClickOK = false;
					var data = "Produccion_Procesos=true&Estado=" + NewState + "&Interno=" + rowdata.Interno;
					data = data + "&Ord_Produccion=" + rowdata.Ord_Produccion + "&Ord_Compra=" + rowdata.Ord_Compra + "&ClienteID=" + rowdata.ClienteID;
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: data,
						async: true,
						success: function (data, status, xhr) {
							commit(true);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							alert(textStatus+ " - " +errorThrown);
							commit(false);
						}
					});
				}
				if (ClickCANCEL == true) {
					clearInterval(CheckTimer);
					ClickCANCEL = false;
					commit(false);
				}
			}, 10);
			NewState = "";
		}*/
	};
	
	$("#produccion_procesos_productos_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 460,
		height: 250,
		//source: ItemsDataAdapter1,
		enabletooltips: true,
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		autoheight: false,
		editable: false,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'ClienteID', width: 0, height: 0 },
			{ text: '', datafield: 'Interno', width: 0, height: 0 },
			{ text: 'Orden Prod.', datafield: 'Ord_Produccion', editable: false, width: '25%', height: 20 },
			{
				text: 'Estado',
				datafield: 'Estado',
				width: '20%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoValues,
						dropDownHeight: 125,
						displayMember: 'Estado',
						valueMember: 'Estado',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: '34%',
				height: 20,
				editable: false,
				cellsformat: 'dd-MMM-yyyy hh:mm:ss',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Avance', datafield: 'Avance', editable: false, width: '11%', cellsalign: 'right', cellsformat: 'p' },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: '10%', height: 20 },
		]
	});
	$("#produccion_procesos_productos_grid1").jqxGrid('hidecolumn', 'ClienteID');
	$("#produccion_procesos_productos_grid1").jqxGrid('hidecolumn', 'Interno');
	$("#produccion_procesos_productos_grid1").jqxGrid('localizestrings', localizationobj);
	
	var ItemsSource2 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Ord_Produccion', type: 'string' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Avance', type: 'decimal' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
	};
	
	$("#produccion_procesos_productos_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 460,
		height: 250,
		//source: ItemsDataAdapter2,
		enabletooltips: true,
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		autoheight: false,
		editable: false,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'ClienteID', width: 0, height: 0 },
			{ text: '', datafield: 'Interno', width: 0, height: 0 },
			{ text: 'Orden Prod.', datafield: 'Ord_Produccion', editable: false, width: '25%', height: 20 },
			{
				text: 'Estado',
				datafield: 'Estado',
				width: '20%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoValues,
						dropDownHeight: 125,
						displayMember: 'Estado',
						valueMember: 'Estado',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: '34%',
				height: 20,
				editable: false,
				cellsformat: 'dd-MMM-yyyy hh:mm:ss',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Avance', datafield: 'Avance', editable: false, width: '11%', cellsalign: 'right', cellsformat: 'p' },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: '10%', height: 20 },
		]
	});
	$("#produccion_procesos_productos_grid2").jqxGrid('hidecolumn', 'ClienteID');
	$("#produccion_procesos_productos_grid2").jqxGrid('hidecolumn', 'Interno');
	$("#produccion_procesos_productos_grid2").jqxGrid('localizestrings', localizationobj);
	
	var ItemsSource3 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Ord_Produccion', type: 'string' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Avance', type: 'decimal' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
	};
	
	$("#produccion_procesos_productos_grid3").jqxGrid(
	{
		theme: mytheme,
		width: 460,
		height: 250,
		//source: ItemsDataAdapter3,
		enabletooltips: true,
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		autoheight: false,
		editable: false,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'ClienteID', width: 0, height: 0 },
			{ text: '', datafield: 'Interno', width: 0, height: 0 },
			{ text: 'Orden Prod.', datafield: 'Ord_Produccion', editable: false, width: '25%', height: 20 },
			{
				text: 'Estado',
				datafield: 'Estado',
				width: '20%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoValues,
						dropDownHeight: 125,
						displayMember: 'Estado',
						valueMember: 'Estado',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: '34%',
				height: 20,
				editable: false,
				cellsformat: 'dd-MMM-yyyy hh:mm:ss',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Avance', datafield: 'Avance', editable: false, width: '11%', cellsalign: 'right', cellsformat: 'p' },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: '10%', height: 20 },
		]
	});
	$("#produccion_procesos_productos_grid3").jqxGrid('hidecolumn', 'ClienteID');
	$("#produccion_procesos_productos_grid3").jqxGrid('hidecolumn', 'Interno');
	$("#produccion_procesos_productos_grid3").jqxGrid('localizestrings', localizationobj);
	
	var ItemsSource4 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Interno', type: 'string' },
			{ name: 'Ord_Produccion', type: 'string' },
			{ name: 'Estado', type: 'string' },
			{ name: 'Fecha', type: 'date' },
			{ name: 'Avance', type: 'decimal' },
			{ name: 'DigitadorID', type: 'string' },
		],
		url: "modulos/datos.php",
		async: true,
	};
	
	$("#produccion_procesos_productos_grid4").jqxGrid(
	{
		theme: mytheme,
		width: 460,
		height: 250,
		//source: ItemsDataAdapter4,
		enabletooltips: true,
		showfilterrow: true,
		filterable: true,
		sortable: true,
		pageable: true,
		autoheight: false,
		editable: false,
		editmode: 'click',
		columns:
		[
			{ text: '', datafield: 'ClienteID', width: 0, height: 0 },
			{ text: '', datafield: 'Interno', width: 0, height: 0 },
			{ text: 'Orden Prod.', datafield: 'Ord_Produccion', editable: false, width: '25%', height: 20 },
			{
				text: 'Estado',
				datafield: 'Estado',
				width: '20%',
				height: 20,
				columntype: 'dropdownlist',
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: EstadoValues,
						dropDownHeight: 125,
						displayMember: 'Estado',
						valueMember: 'Estado',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: '34%',
				height: 20,
				editable: false,
				cellsformat: 'dd-MMM-yyyy hh:mm:ss',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'Avance', datafield: 'Avance', editable: false, width: '11%', cellsalign: 'right', cellsformat: 'p' },
			{ text: 'Dig.', datafield: 'DigitadorID', editable: false, width: '10%', height: 20 },
		]
	});
	$("#produccion_procesos_productos_grid4").jqxGrid('hidecolumn', 'ClienteID');
	$("#produccion_procesos_productos_grid4").jqxGrid('hidecolumn', 'Interno');
	$("#produccion_procesos_productos_grid4").jqxGrid('localizestrings', localizationobj);
	
	// ------------------------------------------ PARTE 3
	
	$('#produccion_procesos_imprimir').jqxButton({
		width: 150,
		template: "warning"
	});
	// Prepare Save Changes...
	$("#produccion_procesos_imprimir").bind('click', function ()
	{
		var data = "";
		data += "imprimir/produccion_procesos_total.php?";
		data += "ClienteID="+$("#produccion_procesos_cliente_ID").val();
		data += "&Estado="+$("#produccion_procesos_estado").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#produccion_procesos_fecha_ini").jqxDateTimeInput("getDate"));
		data += "&Fecha_Fin="+GetFormattedDate($("#produccion_procesos_fecha_fin").jqxDateTimeInput("getDate"));
		//
		window.open(data, "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$('#produccion_procesos_exportar').jqxButton({
		width: 150,
		template: "success"
	});
	// Prepare Save Changes...
	$("#produccion_procesos_exportar").bind('click', function ()
	{
		var data = "";
		data += "modulos/export_xls.php?Produccion_Procesos=true";
		data += "&ClienteID="+$("#produccion_procesos_cliente_ID").val();
		data += "&Estado="+$("#produccion_procesos_estado").val();
		data += "&Fecha_Ini="+GetFormattedDate($("#produccion_procesos_fecha_ini").jqxDateTimeInput("getDate"))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#produccion_procesos_fecha_fin").jqxDateTimeInput("getDate"))+"";
		//
		window.location = data;

	});
	
	Add_Row();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:25px;">
		<tr>
			<!--<td>
				Proceso
			</td>
			<td>
				<div id="produccion_procesos_proceso"></div>
			</td> -->
			<td>
				Ord. Produccion
			</td>
			<td>
				<div id="produccion_procesos_ord_produccion"></div>
			</td>
			<td>
				Fecha I.
			</td>
			<td>
				<div id="produccion_procesos_fecha_ini"></div>
			</td>
			<td>
				Fecha F.
			</td>
			<td>
				<div id="produccion_procesos_fecha_fin"></div>
			</td>
		</tr>
		<tr>
			<td>
				Operario
			</td>
			<td colspan="3">
				<div id="produccion_procesos_cliente"></div>
			</td>
			<td>
				ID Operario
			</td>
			<td>
				<div id="produccion_procesos_cliente_ID"></div>
			</td>
			<td>
				Estado
			</td>
			<td>
				<div id="produccion_procesos_estado"></div>
			</td>
		</tr>
	</table>
</div>

<!-- PART 2 -->
<div id="Parte2">
	<div id="produccion_procesos_left" style="float: left; margin-left:0px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td style="background-color: #6E93CE; color: white; padding: 5px;">
					ORDENES DE TREFILADO
				</td>
			</tr>
			<tr>
				<td style="padding:0px 0px 30px 0px;">
					<div id="produccion_procesos_productos_grid1"></div>
				</td>
			</tr>
			<tr>
				<td style="background-color: #5DBA5D; color: white; padding: 5px;">
					ORDENES DE ELECTROSOLDADO
				</td>
			</tr>
			<tr>
				<td style="padding:0px 0px 30px 0px;">
					<div id="produccion_procesos_productos_grid3"></div>
				</td>
			</tr>
		</table>
	</div>
	<div id="produccion_procesos_right" style="float: left; margin-left:20px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td style="background-color: #F18B0C; color: white; padding: 5px;">
					ORDENES DE CORTE Y ENDEREZADO
				</td>
			</tr>
			<tr>
				<td style="padding:0px 0px 30px 0px;">
					<div id="produccion_procesos_productos_grid2"></div>
				</td>
			</tr>
			<tr>
				<td style="background-color: #3E72A2; color: white; padding: 5px;">
					ORDENES DE FIGURADO
				</td>
			</tr>
			<tr>
				<td style="padding:0px 0px 30px 0px;">
					<div id="produccion_procesos_productos_grid4"></div>
				</td>
			</tr>
		</table>
	</div>
</div>
<!-- PART 3 -->
<div id="Parte3">
	<table cellpadding="0" cellspacing="0" style="margin:0px 0px 10px 0px;">
		<tr>
			<td style="padding-right:20px;">
				<input type="button" id="produccion_procesos_imprimir" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="produccion_procesos_exportar" value="Exportar"/>
			</td>
		</tr>
	</table>
</div>
