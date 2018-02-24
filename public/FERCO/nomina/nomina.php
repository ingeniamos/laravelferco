<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Locked = false;
	var Existente = false;
	var Timer1 = 0;
	var Timer2 = 0;
	var MyDate = new Date();
	var Day = MyDate.getDate();
	var Month = MyDate.getMonth();
	var Year = MyDate.getFullYear();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Nomina_Content");
	var Body = document.getElementById("Nomina_Nomina_Content");
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
				SystemMap("Nomina Mes", true);
				
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
	var Guardar = false;
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Nomina_Mes" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				else
				{
		?>
					$("#nomina_nomina_guardar").jqxButton({ disabled: true });
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Nomina_Mes" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#nomina_nomina_imprimir1").jqxButton({ disabled: true });
					$("#nomina_nomina_imprimir2").jqxButton({ disabled: true });
					$("#nomina_nomina_imprimir3").jqxButton({ disabled: true });
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
	
	var EstadoData = [
		{"Estado":"Aprobados"},
		{"Estado":"Todos"},
	];
	
	var TipoData = [
		//{"Tipo":"Semanal"},
		{"Tipo":"Quincenal"},
		{"Tipo":"Mensual"},
	];
	
	var SemanaData = [
		{"Tipo":"1ra Semana"},
		{"Tipo":"2da Semana"},
		{"Tipo":"3ra Semana"},
		{"Tipo":"4ta Semana"},
	];
	
	var QuincenaData = [
		{"Tipo":"1ra. Quincena", "Number":"1"},
		{"Tipo":"2da. Quincena", "Number":"2"},
	];
	
	var YearsData = [
		{"Year":"2010"},
		{"Year":"2011"},
		{"Year":"2012"},
		{"Year":"2013"},
		{"Year":"2014"},
		{"Year":"2015"},
		{"Year":"2016"},
		{"Year":"2017"},
		{"Year":"2018"},
		{"Year":"2019"},
		{"Year":"2020"},
	];
	
	var MonthsData = [
		{"Month":"Enero", "Number":"0"},
		{"Month":"Febrero", "Number":"1"},
		{"Month":"Marzo", "Number":"2"},
		{"Month":"Abril", "Number":"3"},
		{"Month":"Mayo", "Number":"4"},
		{"Month":"Junio", "Number":"5"},
		{"Month":"Julio", "Number":"6"},
		{"Month":"Agosto", "Number":"7"},
		{"Month":"Septiembre", "Number":"8"},
		{"Month":"Octubre", "Number":"9"},
		{"Month":"Noviembre", "Number":"10"},
		{"Month":"Diciembre", "Number":"11"},
	];
	
	function CheckNomina()
	{
		$.ajax({
			dataType: 'json',
			url: "modulos/datos.php",
			data: {
				"Nomina_Buscar":true,
				"Fecha_Ini":GetFormattedDate($("#nomina_nomina_fecha_ini").jqxDateTimeInput('getDate')),
				"Fecha_Fin":GetFormattedDate($("#nomina_nomina_fecha_fin").jqxDateTimeInput('getDate')),
			},
			success: function (data)
			{
				if (data[0]["Existe"] == "SI")
					Existente = true;
				else
					Existente = false;
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				Alerts_Box("Ocurrio un Error al intentar Buscar Nominas Existentes!", 3)
				Existente = false;
			}
		});
	}
	
	function LoadValues()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Grupo = $("#nomina_nomina_grupos").jqxComboBox('getSelectedItem');
		var Estado = $("#nomina_nomina_estado").jqxComboBox('getSelectedItem');
		var Year = $("#nomina_nomina_year").jqxComboBox('getSelectedItem');
		var Month = $("#nomina_nomina_month").jqxComboBox('getSelectedItem');
		var Tipo = $("#nomina_nomina_tipo").jqxComboBox('getSelectedItem');
		var Seleccion = $("#nomina_nomina_select").jqxComboBox('getSelectedItem');
		
		if (!Grupo)
			Grupo = "";
		
		if (!Estado)
			Estado = "";
		
		if (!Year)
		{
			//Alerts_Box("Debe Seleccionar un Año!", 3);
			//WaitClick_Combobox("nomina_nomina_year");
			Locked = false;
			return;
		}
		
		if (!Month)
		{
			//Alerts_Box("Debe Seleccionar un Mes!", 3);
			//WaitClick_Combobox("nomina_nomina_month");
			Locked = false;
			return;
		}
		
		if (!Tipo)
		{
			//Alerts_Box("Debe Seleccionar un Tipo de Nomina!", 3);
			//WaitClick_Combobox("nomina_nomina_tipo");
			Locked = false;
			return;
		}
		else
		{
			if (Tipo.value == "Quincenal")
			{
				if (!Seleccion)
				{
					//Alerts_Box("Debe Seleccionar una Quincena!", 3);
					//WaitClick_Combobox("nomina_nomina_select");
					Locked = false;
					return;
				}
			}
		}
		
		NominaSource.data = {
			"Nomina":true,
			"Quincenal":Tipo.value == "Quincenal" ? true:false,
			"Fecha_Ini":GetFormattedDate($("#nomina_nomina_fecha_ini").jqxDateTimeInput('getDate')),
			"Fecha_Fin":GetFormattedDate($("#nomina_nomina_fecha_fin").jqxDateTimeInput('getDate')),
			"Grupo":$("#nomina_nomina_grupos").val(),
			"Estado":$("#nomina_nomina_estado").val(),
		};
		var NominaDataAdapter = new $.jqx.dataAdapter(NominaSource,{
			loadComplete: function ()
			{
				Locked = false;
			}
		});
		$("#nomina_nomina_item_grid").jqxGrid({source: NominaDataAdapter});
		CheckNomina();
	}
	
	var GrupoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo', type: 'string'},
		],
		data: {"Terceros_Tipo":"Nómina"},
		url: "modulos/parametros.php",
	};
	var GrupoDataAdapter = new $.jqx.dataAdapter(GrupoSource);
	
	$("#nomina_nomina_grupos").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: GrupoDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Grupo',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#nomina_nomina_grupos").bind('change', function (event) {
		if (event.args)
			LoadValues();
		else
			$("#nomina_nomina_grupos").jqxComboBox('clearSelection');
	});
	
	$("#nomina_nomina_estado").jqxComboBox(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		source: EstadoData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: 0,
		displayMember: 'Estado',
		valueMember: 'Estado'
	});
	$("#nomina_nomina_estado").bind('change', function (event) {
		if (event.args)
			LoadValues();
		else
			$("#nomina_nomina_estado").jqxComboBox('selectIndex', 0);
	});
	
	$("#nomina_nomina_year").jqxComboBox(
	{
		theme: mytheme,
		width: 90,
		height: 20,
		source: YearsData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Year',
		valueMember: 'Year'
	});
	$("#nomina_nomina_year").bind('change', function (event) {
		if (event.args)
			LoadValues();
		else
			$("#nomina_nomina_year").jqxComboBox('clearSelection');
	});
	
	$("#nomina_nomina_month").jqxComboBox(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		source: MonthsData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Month',
		valueMember: 'Number'
	});
	$("#nomina_nomina_month").bind('change', function (event) {
		if (event.args)
			LoadValues();
		else
			$("#nomina_nomina_month").jqxComboBox('clearSelection');
		
		$("#nomina_nomina_select").jqxComboBox('clearSelection');
	});
	
	$("#nomina_nomina_tipo").jqxComboBox(
	{
		theme: mytheme,
		width: 90,
		height: 20,
		source: TipoData,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo'
	});
	$("#nomina_nomina_tipo").on('change', function (event)
	{
		if (event.args)
		{
			switch(event.args.item.value)
			{
				case "Semanal":
					$("#nomina_nomina_text").html("Semana");
					$("#nomina_nomina_select").jqxComboBox({ source: SemanaData});
					$("#nomina_nomina_select").jqxComboBox({ disabled: false});
				break;
				
				case "Quincenal":
					$("#nomina_nomina_text").html("Quincena");
					$("#nomina_nomina_select").jqxComboBox({ source: QuincenaData});
					$("#nomina_nomina_select").jqxComboBox({ disabled: false});
				break;
				
				case "Mensual":
					$("#nomina_nomina_select").jqxComboBox('clearSelection');
					$("#nomina_nomina_select").jqxComboBox({ disabled: true});
					
					var Y = $("#nomina_nomina_year").jqxComboBox("getSelectedItem");
					var M = $("#nomina_nomina_month").jqxComboBox("getSelectedItem");
					if (Y == undefined)
					{
						alert("Año Indefinido")
						return;
					}
					else if (M == undefined)
					{
						alert("Mes Indefinido!")
						return;
					}
					else
					{
						$("#nomina_nomina_text").html("Mes");
						$("#nomina_nomina_fecha_ini").jqxDateTimeInput('setDate', new Date(Y.value, M.value, 1));
						$("#nomina_nomina_fecha_fin").jqxDateTimeInput('setDate', new Date(Y.value, parseInt(M.value) + 1, 0));
					}
					
					LoadValues();
				break;
			}
		}
		else {
			$("#nomina_nomina_tipo").jqxComboBox('clearSelection');
			$("#nomina_nomina_text").html("Seleccionar");
		}
	});
	
	$("#nomina_nomina_select").jqxComboBox(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Number',
		disabled: true,
	});
	$("#nomina_nomina_select").on('change', function (event)
	{
		if (event.args)
		{
			if (event.args.item.value == "1")
			{
				var Y = $("#nomina_nomina_year").jqxComboBox("getSelectedItem");
				var M = $("#nomina_nomina_month").jqxComboBox("getSelectedItem");
				if (Y == undefined)
				{
					alert("Año Indefinido")
					return;
				}
				else if (M == undefined)
				{
					alert("Mes Indefinido!")
					return;
				}
				else
				{
					$("#nomina_nomina_fecha_ini").jqxDateTimeInput('setDate', new Date(Y.value, parseInt(M.value), 1));
					$("#nomina_nomina_fecha_fin").jqxDateTimeInput('setDate', new Date(Y.value, parseInt(M.value), 15));
				}
			}
			
			if (event.args.item.value == "2")
			{
				var Y = $("#nomina_nomina_year").jqxComboBox("getSelectedItem");
				var M = $("#nomina_nomina_month").jqxComboBox("getSelectedItem");
				if (Y == undefined)
				{
					alert("Año Indefinido")
					return;
				}
				else if (M == undefined)
				{
					alert("Mes Indefinido!")
					return;
				}
				else
				{
					$("#nomina_nomina_fecha_ini").jqxDateTimeInput('setDate', new Date(Y.value, parseInt(M.value), 16));
					$("#nomina_nomina_fecha_fin").jqxDateTimeInput('setDate', new Date(Y.value, parseInt(M.value) + 1, 0));
				}
			}
			
			LoadValues();
		}
		else
			$("#nomina_nomina_select").jqxComboBox('clearSelection');
	});
	
	$("#nomina_nomina_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
		disabled: true,
		showCalendarButton: false,
	});
	
	$("#nomina_nomina_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 120,
		height: 20,
		formatString: 'dd-MMMM-yyyy',
		culture: 'es-ES',
		disabled: true,
		showCalendarButton: false,
	});
	
	//----------------------------------------------------------------------------------------------------------//
	//-------------------------------------------------- GRID --------------------------------------------------//
	//----------------------------------------------------------------------------------------------------------//
	var NominaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'},
			{ name: 'Basico', type: 'decimal'},
			{ name: 'Horas', type: 'decimal'},
			{ name: 'Horas_Desc', type: 'decimal'},
			{ name: 'Horas_Rep', type: 'decimal'},
			{ name: 'Horas_Ext', type: 'decimal'},
			{ name: 'Horas_Lab', type: 'decimal'},
			//--
			{ name: 'Basico_Final', type: 'decimal'},
			{ name: 'Transporte', type: 'decimal'},
			{ name: 'Bono', type: 'decimal'},
			{ name: 'Extras', type: 'decimal'},
			{ name: 'Licencias', type: 'decimal'},
			{ name: 'Devengado', type: 'decimal'},
			//--
			{ name: 'Salud', type: 'decimal'},
			{ name: 'Pension', type: 'decimal'},
			{ name: 'Retencion', type: 'decimal'},
			{ name: 'Prestamo', type: 'decimal'},
			{ name: 'Libranza', type: 'decimal'},
			{ name: 'Anticipo', type: 'decimal'},
			{ name: 'Donacion', type: 'decimal'},
			{ name: 'Multa', type: 'decimal'},
			//--
			{ name: 'Deducido', type: 'decimal'},
			{ name: 'Deuda', type: 'decimal'},
			{ name: 'Neto', type: 'decimal'},
			{ name: 'Cesantia', type: 'decimal'},
		],
		data: {"Nomina":true},
		url: "modulos/datos.php",
		async: true,
	};
	var NominaDataAdapter = new $.jqx.dataAdapter(NominaSource);
	
	$("#nomina_nomina_item_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		source: NominaDataAdapter,
		enabletooltips: true,
		sortable: false,
		pageable: true,
		pagesizeoptions: ['10', '20', '50', '100'],
		pagesize: 20,
		autoheight: true,
		editable: true,
		editmode: 'dblclick',
		showfilterrow: true,
		filterable: true,
		columns:
		[
			{
				text: '',
				datafield: 'Print',
				editable: false,
				width: '4%',
				height: 20,
				filterable: false,
				pinned: true,
				renderer: function () {
					return "<img src=\"images/print.png\" style=\"margin:0px 6px;\" id=\"print_button\" />";
				},
				cellsrenderer: function (row, datafield, value) {
					return "<img src=\"images/print.png\" style=\"margin:0px 6px;\" />";
				},
			},
			{ text: '', datafield: 'ID', editable: false, filterable: false, width: 0 },
			{ text: 'Nombre', datafield: 'Nombre', editable: false, width: 200, height: 20, pinned: true, },
			{ text: 'Cedula', datafield: 'ClienteID', editable: false, width: 120, height: 20, pinned: true, },
			{
				text: 'B Mensual',
				datafield: 'Basico',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'H. Mes',
				datafield: 'Horas',
				width: 55,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, max: 999, spinButtons: false });
				}
			},
			{
				text: 'H. Desc.',
				datafield: 'Horas_Desc',
				width: 55,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, max: 999, spinButtons: false });
				}
			},
			{
				text: 'H. Rep.',
				datafield: 'Horas_Rep',
				width: 55,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, max: 999, spinButtons: false });
				}
			},
			{
				text: 'Extras',
				datafield: 'Horas_Ext',
				width: 55,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, max: 999, spinButtons: false });
				}
			},
			{
				text: 'H. Labs',
				datafield: 'Horas_Lab',
				width: 55,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, max: 999, spinButtons: false });
				}
			},
			{
				text: 'Basico',
				datafield: 'Basico_Final',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'S. Transp.',
				datafield: 'Transporte',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Bonificacion',
				datafield: 'Bono',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Extras',
				datafield: 'Extras',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Licencias - Inc',
				datafield: 'Licencias',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Devengado',
				datafield: 'Devengado',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Salud',
				datafield: 'Salud',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Pension',
				datafield: 'Pension',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Retencion',
				datafield: 'Retencion',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Prestamo',
				datafield: 'Prestamo',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Libranza',
				datafield: 'Libranza',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Anticipo',
				datafield: 'Anticipo',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Donacion',
				datafield: 'Donacion',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Multa',
				datafield: 'Multa',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Deducido',
				datafield: 'Deducido',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Deuda Pospuesta',
				datafield: 'Deuda',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Neto',
				datafield: 'Neto',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Cesantia',
				datafield: 'Cesantia',
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false, 
				editable: true,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
		]
	});
	$("#nomina_nomina_item_grid").on("bindingcomplete", function (event)
	{
		$("#nomina_nomina_item_grid").jqxGrid('hidecolumn', 'ID');
		$("#nomina_nomina_item_grid").jqxGrid('localizestrings', localizationobj);
	});
	$("#nomina_nomina_item_grid").on('columnclick', function (event)
	{
		// event arguments.
		var args = event.args;
		// column's setting
		var column = args.column;
		// column data field.
		var datafield = args.datafield;
		// original event.
		var ev = args.originalEvent;
		
		if (datafield == "Print")
		{
			var datinfo = $("#nomina_nomina_item_grid").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			
			if (count < 1)
			{
				Alerts_Box("Debe Generar la Nomina antes de poder Imprimir.", 3);
				WaitClick();
				return;
			}
			
			if ($("#nomina_nomina_tipo").val() == "Quincenal")
				var value = true;
			else
				var value = false;
			
			var data = "";
			data += "imprimir/nomina_detalle.php?Grupo="+$("#nomina_nomina_grupos").val()+"&Quincenal="+value+"&Estado="+$("#nomina_nomina_estado").val()+"";
			data += "&Fecha_Ini="+GetFormattedDate($("#nomina_nomina_fecha_ini").jqxDateTimeInput('getDate'))+"";
			data += "&Fecha_Fin="+GetFormattedDate($("#nomina_nomina_fecha_fin").jqxDateTimeInput('getDate'))+"";
			
			window.open(data, "", "width=760, height=500, menubar=no, titlebar=no");
		}
	});
	$("#nomina_nomina_item_grid").on('cellclick', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		if (datafield == "Print")
		{
			var id = $("#nomina_nomina_item_grid").jqxGrid('getcellvalue', rowBoundIndex, "ClienteID");
			if ($("#nomina_nomina_tipo").val() == "Quincenal")
				var value = true;
			else
				var value = false;
			
			var data = "";
			data += "imprimir/nomina_detalle.php?EmpleadoID="+id+"&Quincenal="+value+"&Estado="+$("#nomina_nomina_estado").val()+"";
			data += "&Fecha_Ini="+GetFormattedDate($("#nomina_nomina_fecha_ini").jqxDateTimeInput('getDate'))+"";
			data += "&Fecha_Fin="+GetFormattedDate($("#nomina_nomina_fecha_fin").jqxDateTimeInput('getDate'))+"";
			
			window.open(data, "", "width=760, height=500, menubar=no, titlebar=no");
		}
	});
	
	//----------------------------------------------------------------------------------
	//									GUARDAR
	//----------------------------------------------------------------------------------
	function GuardarNomina()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		if (Guardar == false && Admin == false)
			return;
		
		var Tipo = $("#nomina_nomina_tipo").jqxComboBox("getSelectedItem");
		if (!Tipo)
		{
			Locked = false;
			return;
		}
		
		var SubGrupo = $("#nomina_nomina_tipo").jqxComboBox('getSelectedItem');
		var SubGrupo2 = $("#nomina_nomina_select").jqxComboBox('getSelectedItem');
		
		if (! SubGrupo || SubGrupo < 0) {
			Alerts_Box("Favor Seleccionar un Tipo de Nomina!", 3);
			WaitClick_Combobox("nomina_nomina_tipo");
			return;
		}
		
		if (! SubGrupo2 || SubGrupo2 < 0) {
			Alerts_Box("Favor Seleccionar un Rango de Fecha de Nomina!", 3);
			WaitClick_Combobox("nomina_nomina_select");
			return;
		}
			
		var datinfo = $("#nomina_nomina_item_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		var TotalNomina = 0;
		
		if (count < 1)
		{
			Alerts_Box("Debe Generar la Nomina antes de poder Guardar.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (i = 0; i < count; i++)
		{
			var array = {};
			var currentRow = $("#nomina_nomina_item_grid").jqxGrid('getrowdata', i);
			
			array["Nombre"] = currentRow.Nombre;
			array["ClienteID"] = currentRow.ClienteID;
			array["Basico"] = currentRow.Basico;
			array["Horas"] = currentRow.Horas;
			array["Horas_Desc"] = currentRow.Horas_Desc;
			array["Horas_Rep"] = currentRow.Horas_Rep;
			array["Horas_Ext"] = currentRow.Horas_Ext;
			array["Horas_Lab"] = currentRow.Horas_Lab;
			array["Basico_Final"] = currentRow.Basico_Final;
			array["Transporte"] = currentRow.Transporte;
			array["Bono"] = currentRow.Bono;
			array["Extras"] = currentRow.Extras;
			array["Licencias"] = currentRow.Licencias;
			array["Devengado"] = currentRow.Devengado;
			array["Salud"] = currentRow.Salud;
			array["Pension"] = currentRow.Pension;
			array["Retencion"] = currentRow.Retencion;
			array["Prestamo"] = currentRow.Prestamo;
			array["Libranza"] = currentRow.Libranza;
			array["Anticipo"] = currentRow.Anticipo;
			array["Donacion"] = currentRow.Donacion;
			array["Multa"] = currentRow.Multa;
			array["Deducido"] = currentRow.Deducido;
			array["Deuda"] = currentRow.Deuda;
			array["Neto"] = currentRow.Neto;
			array["Cesantia"] = currentRow.Cesantia;
			//---
			array["Fecha_Ini"] = GetFormattedDate($("#nomina_nomina_fecha_ini").jqxDateTimeInput('getDate'));
			array["Fecha_Fin"] = GetFormattedDate($("#nomina_nomina_fecha_fin").jqxDateTimeInput('getDate'));
			//---
			TotalNomina += parseFloat(currentRow.Neto);
			myarray[i] = array;
		};
		
		if (Existente == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			Alerts_Box("Ya existe una Nomina de Este periodo, si continua esta se sobreescribirá.<br/>¿Desea Continuar?", 4, true);
			
			Timer2 = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(Timer2);
					ClickOK = false;
					$("#Loading_Mess").html("Procesando Solicitud...");
					$("#Loading").show();
					$.ajax({
						dataType: 'text',
						type: "POST",
						url: "modulos/guardar.php",
						data: {
							"Nomina_Modificar":myarray,
						},
						success: function (data)
						{
							alert(data)
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							ClickOK = false;
							ClickCANCEL = false;
							Alerts_Box("Nomina Modificada con Exito!", 2);
							Locked = false;
							CheckNomina();
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							$("#Loading").hide();
							$("#Loading_Mess").html("Cargando...");
							Locked = false;
							Alerts_Box("Ocurrio un Error al intentar modificar los datos!\nIntente luego de unos segundos...", 3);
						}
					});
				}
				if (ClickCANCEL == true)
				{
					clearInterval(Timer2);
					ClickCANCEL = false;
					Locked = false;
				}
			}, 10);
		}
		else
		{
			$("#Loading_Mess").html("Procesando Solicitud...");
			$("#Loading").show();
			$.ajax({
				dataType: 'text',
				type: "POST",
				url: "modulos/guardar.php",
				data: {
					"Nomina":myarray,
					"Quincenal":Tipo.value == "Quincenal" ? true:false,
					"SubGrupo":SubGrupo.label,
					"SubGrupo2":SubGrupo2.label,
					"Total":TotalNomina,
				},
				success: function (data)
				{
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					alert(data);
					ClickOK = false;
					ClickCANCEL = false;
					Alerts_Box("Nomina Guardada con Exito!", 2);
					Locked = false;
					CheckNomina();
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
				}
			});
		}
	}
	
	$("#nomina_nomina_guardar").jqxButton({
		width: 150,
		template: "info"
	});
	$("#nomina_nomina_guardar").bind('click', function ()
	{
		GuardarNomina();
	});
	$("#nomina_nomina_imprimir1").jqxButton({width: 150, template: "warning"});
	$("#nomina_nomina_imprimir1").bind('click', function ()
	{
		if ($("#nomina_nomina_tipo").val() == "Quincenal")
			var value = true;
		else
			var value = false;
		
		var data = "";
		data += "imprimir/nomina.php?Grupo="+$("#nomina_nomina_grupos").val()+"&Quincenal="+value+"&Estado="+$("#nomina_nomina_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#nomina_nomina_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#nomina_nomina_fecha_fin").jqxDateTimeInput('getDate'))+"";
		
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#nomina_nomina_imprimir2").jqxButton({width: 150, template: "warning"});
	$("#nomina_nomina_imprimir2").bind('click', function ()
	{
		if ($("#nomina_nomina_tipo").val() == "Quincenal")
			var value = true;
		else
			var value = false;
		
		var data = "";
		data += "imprimir/nomina_base.php?Grupo="+$("#nomina_nomina_grupos").val()+"&Quincenal="+value+"&Estado="+$("#nomina_nomina_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#nomina_nomina_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#nomina_nomina_fecha_fin").jqxDateTimeInput('getDate'))+"";
		
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	$("#nomina_nomina_imprimir3").jqxButton({width: 150, template: "warning"});
	$("#nomina_nomina_imprimir3").bind('click', function ()
	{
		if ($("#nomina_nomina_tipo").val() == "Quincenal")
			var value = true;
		else
			var value = false;
		
		var data = "";
		data += "imprimir/nomina_extras.php?Grupo="+$("#nomina_nomina_grupos").val()+"&Quincenal="+value+"&Estado="+$("#nomina_nomina_estado").val()+"";
		data += "&Fecha_Ini="+GetFormattedDate($("#nomina_nomina_fecha_ini").jqxDateTimeInput('getDate'))+"";
		data += "&Fecha_Fin="+GetFormattedDate($("#nomina_nomina_fecha_fin").jqxDateTimeInput('getDate'))+"";
		
		window.open(data, "", "width=925, height=800, menubar=no, titlebar=no");
	});
	
	//Load Initial Values
	$("#nomina_nomina_year").val(Year);
	$("#nomina_nomina_month").val(Month);
	
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px; text-align:center;">
		<tr>
			<td>
				Grupo Nomina
			</td>
			<td>
				Ver Como
			</td>
			<td>
				A&ntilde;o
			</td>
			<td>
				Mes
			</td>
			<td>
				Tipo
			</td>
			<td>
				<div id="nomina_nomina_text">Seleccionar</div>
			</td>
			<td>
				Desde
			</td>
			<td>
				Hasta
			</td>
		</tr>
		<tr>	
			<td>
				<div id="nomina_nomina_grupos"></div>
			</td>
			<td>
				<div id="nomina_nomina_estado"></div>
			</td>
			<td>
				<div id="nomina_nomina_year"></div>
			</td>
			<td>
				<div id="nomina_nomina_month"></div>
			</td>
			<td>
				<div id="nomina_nomina_tipo"></div>
			</td>
			<td>
				<div id="nomina_nomina_select"></div>
			</td>
			<td>
				<div id="nomina_nomina_fecha_ini"></div>
			</td>
			<td>
				<div id="nomina_nomina_fecha_fin"></div>
			</td>
		</tr>
	</table>
	<div id="nomina_nomina_item_grid"></div>
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td>
				<input type="button" id="nomina_nomina_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="nomina_nomina_imprimir1" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="nomina_nomina_imprimir2" value="Nomina Base"/>
			</td>
			<td>
				<input type="button" id="nomina_nomina_imprimir3" value="Horas Extras"/>
			</td>
		</tr>
	</table>
</div>

