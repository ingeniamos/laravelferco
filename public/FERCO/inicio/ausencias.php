<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Inicio_Content");
	var Body = document.getElementById("Inicio_Ausencias");
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
				SystemMap("Funcionarios Ausentes", true);
				LoadValues();
				
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
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ClienteID', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Novedad', type: 'string' },
			{ name: 'Fecha_Ini', type: 'date' },
			{ name: 'Fecha_Fin', type: 'date' },
			{ name: 'Horas', type: 'decimal' },
			{ name: 'AutorizadorID', type: 'string' },
			{ name: 'Observaciones', type: 'string' },
		],
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#inicio_ausencias_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		source: GridDataAdapter,
		enabletooltips: true,
		enablebrowserselection: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		sortable: true,
		pageable: true,
		showfilterrow: true,
		filterable: true,
		editable: false,
		columns:
		[
			{ text: 'Cedula', datafield: 'ClienteID', width: "10%", height: 20, filterable: true },
			{ text: 'Nombre', datafield: 'Nombre', width: "25%", height: 20, filterable: true },
			{ text: 'Novedad', datafield: 'Novedad', width: "10%", height: 20, filterable: true },
			{
				text: 'Inicio',
				datafield: 'Fecha_Ini',
				columntype: 'datetimeinput',
				width: "10%",
				height: 20,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				filterable: false,
			},
			{
				text: 'Fin',
				datafield: 'Fecha_Fin',
				columntype: 'datetimeinput',
				width: "10%",
				height: 20,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				filterable: false,
			},
			{
				text: 'Hrs.',
				datafield: 'Horas',
				width: 50,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				filterable: false,
			},
			{ text: 'Autoriza', datafield: 'AutorizadorID', width: "10%", height: 20, filterable: true },
			{ text: 'Observaciones', datafield: 'Observaciones', width: "30%", height: 20, filterable: false },
		],
	});
	$("#inicio_ausencias_items_grid").jqxGrid('localizestrings', localizationobj);
	
	function LoadValues()
	{
		GridSource.url = "modulos/datos.php";
		GridSource.data = {"Inicio_Ausencias":true};
		var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
		$("#inicio_ausencias_items_grid").jqxGrid({source: GridDataAdapter});
	}
	
	LoadValues();
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div id="inicio_ausencias_items_grid"></div>
</div>
