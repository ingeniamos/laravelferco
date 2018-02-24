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
	var Main = document.getElementById("Figuracion_Content");
	var Body = document.getElementById("Figuracion_Hierros_Content");
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
				SystemMap("Hierros", true);
				$("#figuracion_hierros_items_grid").jqxGrid("updatebounddata");
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
	var Modificar = false;
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
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Hierros" && $data[$i]["Modificar"] == "true")
				{
		?>
					Modificar = true;
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
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: "Codigo", type: "string" },
			{ name: "Nombre", type: "string" },
			{ name: "K", type: "decimal" },
		],
		url: "modulos/datos_productos.php",
		data: {"Productos_Para_Figuracion":true},
		updaterow: function (rowid, rowdata, commit)
		{
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {
					"Productos_Para_Figuracion":true,
					"Codigo":rowdata.Codigo,
					"K":rowdata.K,
				},
				success: function (data, status, xhr)
				{
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							commit(true);
						break;
						
						case "ERROR":
							commit(false);
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
						break;
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					commit(false);
					Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
					+"Error: "+errorThrown, 3);
				}
			});
		}
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#figuracion_hierros_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 500,
		autoheight: true,
		source: GridDataAdapter,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: 20,
		sortable: true,
		pageable: true,
		showfilterrow: true,
		filterable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{ text: 'Codigo', datafield: 'Codigo', width: "20%", height: 20, editable: false, },
			{ text: 'Nombre', datafield: 'Nombre', width: "70%", height: 20, editable: false, },
			{
				text: 'Kg/m',
				datafield: 'K',
				width: "10%",
				height: 20,
				cellsformat: 'd3',
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 3, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
		]
	});
	$("#figuracion_hierros_items_grid").bind("bindingcomplete", function (event)
	{
		$("#figuracion_hierros_items_grid").jqxGrid('localizestrings', localizationobj);
	});
	
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<div id="figuracion_hierros_items_grid"></div>
</div>
