<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//---- GLOBAL VARIABLES
	var mytheme = "energyblue";
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Reportes_Content");
	var Body = document.getElementById("Reportes_Carnet_Content");
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
				SystemMap("Carnet", true);
				Reload();
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
	var Imprimir = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Reportes" && $data[$i]["SubModulo"] == "Carnet" && $data[$i]["Imprimir"] == "true")
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function Reload()
	{
		$("#reportes_carnet_items_grid").jqxGrid("updatebounddata");
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Cliente", type: "string"},
			{ name: "ClienteID", type: "string"},
			{ name: "RH", type: "string"},
			{ name: "Cargo", type: "string"},
			{ name: "Fecha", type: "date"},
			{ name: "Imprimir", type: "bool"},
		],
		url: "modulos/datos.php",
		data: {"Nomina_Carnet":true},
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#reportes_carnet_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 800,
		autoheight: true,
		source: GridDataAdapter,
		enabletooltips: true,
		sortable: false,
		pageable: true,
		pagesizeoptions: ['10', '20', '50', '100'],
		pagesize: 20,
		editable: true,
		showfilterrow: true,
		filterable: true,
		columns:
		[
			{ text: 'Nombre', datafield: 'Cliente', editable: false, width: "40%", height: 20, },
			{ text: 'Cedula', datafield: 'ClienteID', editable: false, width: "15%", height: 20, },
			{ text: 'R. H.', datafield: 'RH', editable: false, filterable: false, width: "5%", height: 20, },
			{
				text: 'Cargo',
				datafield: 'Cargo',
				width: "20%",
				height: 20,
				filterable: false,
				editable: false,
			},
			{
				text: 'Fecha Vencimiento',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: "15%",
				height: 20,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				editable: false,
			},
			{
				text: 'Imp.',
				datafield: 'Imprimir',
				columntype: 'checkbox',
				width: "5%",
				editable: true,
				filtertype: 'bool',
				renderer: function()
				{
					return "<div style=\"margin: 5px 0px 0px 9px;\"></div>";
				},
				rendered: function (element)
				{
					$(element).jqxCheckBox({ theme: mytheme, width: 16, height: 16, animationShowDelay: 0, animationHideDelay: 0 });
					columnCheckBox = $(element);
					$(element).on("change", function (event)
					{
						var checked = event.args.checked;
						var pageinfo = $("#reportes_carnet_items_grid").jqxGrid("getpaginginformation");
						var pagenum = pageinfo.pagenum;
						var pagesize = pageinfo.pagesize;
						
						if (checked == null)
						{
							return false;
						}
						
						$("#reportes_carnet_items_grid").jqxGrid("beginupdate");

						// select all rows when the column's checkbox is checked.
						if (checked)
						{
							$("#reportes_carnet_items_grid").jqxGrid("selectallrows");
						}
						// unselect all rows when the column's checkbox is checked.
						else if (checked == false)
						{
							$("#reportes_carnet_items_grid").jqxGrid("clearselection");
						}
						
						// update cells values.
						/*var startrow = pagenum * pagesize;
						for (var i = startrow; i < startrow + pagesize; i++)
						{
							// The bound index represents the row's unique index. 
							// Ex: If you have rows A, B and C with bound indexes 0, 1 and 2, afer sorting, the Grid will display C, B, A i.e the C's bound index will be 2, but its visible index will be 0.
							// The code below gets the bound index of the displayed row and updates the value of the row's Imprimir column.
							var boundindex = $("#reportes_carnet_items_grid").jqxGrid("getrowboundindex", i);
							$("#reportes_carnet_items_grid").jqxGrid("setcellvalue", boundindex, "Imprimir", event.args.checked);
						}*/
						var datainfo = $("#reportes_carnet_items_grid").jqxGrid("getdatainformation");
						var count = datainfo.rowscount;
						for (var i = 0; i < count; i++)
						{
							// The bound index represents the row's unique index. 
							// Ex: If you have rows A, B and C with bound indexes 0, 1 and 2, afer sorting, the Grid will display C, B, A i.e the C's bound index will be 2, but its visible index will be 0.
							// The code below gets the bound index of the displayed row and updates the value of the row's Imprimir column.
							var boundindex = $("#reportes_carnet_items_grid").jqxGrid("getrowboundindex", i);
							$("#reportes_carnet_items_grid").jqxGrid("setcellvalue", boundindex, "Imprimir", event.args.checked);
						}

						$("#reportes_carnet_items_grid").jqxGrid("endupdate");
					});
					return true;
				}
			},
		]
	});
	$("#reportes_carnet_items_grid").on("bindingcomplete", function (event)
	{
		$("#reportes_carnet_items_grid").jqxGrid('hidecolumn', 'ID');
		$("#reportes_carnet_items_grid").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#reportes_carnet_imprimir").jqxButton({
		theme: mytheme,
		width: 200,
		height: 25,
		template: "warning"
	});
	$("#reportes_carnet_imprimir").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		
		var datinfo = $("#reportes_carnet_items_grid").jqxGrid("getdatainformation");
		var count = datinfo.rowscount;
		var Listado = "";
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#reportes_carnet_items_grid").jqxGrid("getrowdata", i);
			if (currentRow.Imprimir == true)
			{
				var tmp_array = {"ClienteID": currentRow.ClienteID};
				Listado += currentRow.ClienteID+",";
			}
		}
		//Listado = JSON.stringify(Listado);
		//alert(JSON.stringify(Listado))
		window.open("imprimir/nomina_carnets.php?Clientes="+Listado+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	if (!Admin && !Imprimir)
	{
		$("#reportes_carnet_imprimir").jqxButton({ disabled: true });
	}
	
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div id="reportes_carnet_items_grid"></div>
	<input type="button" id="reportes_carnet_imprimir" value="Imprimir Seleccionados" style="margin-top:20px;"/>
</div>