<?php
session_start();
$Cartilla = isset($_POST["Cartilla"]) ? $_POST["Cartilla"]:"";
if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
{
	$_SESSION["NumOfID"]++;
}
$NumOfID = $_SESSION["NumOfID"];
?>
<style type="text/css">
.orange {
	color: black;
	background-color: #FFC895 !important;
}
/*.orange:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .orange:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: #FFC895;
}*/
.blue {
	color: black;
	background-color: #CFDDE9 !important;
}
/*.blue:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .blue:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: #CFDDE9;
}*/
.green {
	color: black;
	background-color: #B5F7C9 !important;
}
/*.green:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .green:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: #B5F7C9;
}*/
</style>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Timer = 0;
	var Interno = "";
	var State = "";
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Figuracion_Content");
	var Body = document.getElementById("Figuracion_Visualizar_Content");
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
				SystemMap("Visualizar", true);
				//ReDefine();
				LoadParameters();
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
	var Imprimir = false;
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
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Editar" && $data[$i]["Modificar"] == "true")
				{
		?>
					Modificar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Editar" && $data[$i]["Imprimir"] == "true")
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
	
	function LoadParameters()
	{
		CartillasSource.data = {"Figuracion_Cartillas":true, "Type":"Visualizar"};
		var CartillasDataAdapter = new $.jqx.dataAdapter(CartillasSource);
		$("#figuracion_visualizar_interno").jqxComboBox({source: CartillasDataAdapter});
	}
	
	function LoadValues(Interno)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		$.ajax({
			dataType: "json",
			type: "GET",
			url: "modulos/datos_productos.php",
			data: {"Figuracion_Visualizar":Interno},
			success: function(data)
			{
				Locked = false;
				$("#figuracion_visualizar_orden_produccion").val(data[0]["Orden_Produccion"]);
				$("#figuracion_visualizar_cliente").val(data[0]["Cliente"]);
				$("#figuracion_visualizar_cliente_ID").val(data[0]["ClienteID"]);
				$("#figuracion_visualizar_obra").val(data[0]["Obra"]);
				
				$("#figuracion_visualizar_items_grid").jqxGrid("clear");
				$("#figuracion_visualizar_items_grid").jqxGrid("clearSelection");
				var len = data[0]["Items"].length;
				data[0]["Items"].reverse();
				for (var i = 0; i < len; i++)
				{
					var datarow = [{
						"Imagen":data[0]["Items"][i]["Imagen"],
						"Figura":data[0]["Items"][i]["Figura"],
						"Codigo":data[0]["Items"][i]["Codigo"],
						"Nombre":data[0]["Items"][i]["Nombre"],
						"Detalle":data[0]["Items"][i]["Detalle"],
						"Dimensiones":data[0]["Items"][i]["Dimensiones"],
						"Cantidad":data[0]["Items"][i]["Cantidad"],
						"Cantidad2":data[0]["Items"][i]["Cantidad2"],
						"Longitud":data[0]["Items"][i]["Longitud"],
						"Peso":parseFloat(data[0]["Items"][i]["Peso"]).toFixed(2),
						"Ubicacion":data[0]["Items"][i]["Ubicacion"],
						"Estado":data[0]["Items"][i]["Estado"],
					}];
					$("#figuracion_visualizar_items_grid").jqxGrid("addrow", null, datarow, "first");
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se cargaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	}
	
	var CartillasSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Interno", type: "string"},
		],
		url: "modulos/datos.php",
	};
	
	$("#figuracion_visualizar_interno").jqxComboBox({
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Interno',
		valueMember: 'Interno'
	});
	$("#figuracion_visualizar_interno").on("change", function (event)
	{
		if (event.args)
		{
			if (Interno == event.args.item.value)
				return;
			
			Interno = event.args.item.value;
			
			clearTimeout(Timer);
			Timer = setTimeout(function()
			{
				if (ClickOK == true)
				{
					EnableDisableAll(false);
					ClickOK = false;
				}
				LoadValues(Interno);
			},500);
		}
		else
		{
			var item_value = $("#figuracion_visualizar_interno").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				ClearDocument();
			}
			else
			{
				var value = $("#figuracion_visualizar_interno").val();
				var item = $("#figuracion_visualizar_interno").jqxComboBox('getItemByValue', value);
				if (item == undefined)
				{
					ClearDocument();
				}
				else
					$("#figuracion_visualizar_interno").jqxComboBox('selectItem', item.value);
			}
		}
	});
	$("#figuracion_visualizar_interno").bind("bindingComplete", function (event) {
		if (Interno != "")
			$("#figuracion_visualizar_interno").jqxComboBox('selectItem', Interno);
	});
	
	$("#figuracion_visualizar_orden_produccion").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
		disabled: true,
	});
	
	$("#figuracion_visualizar_cliente").jqxInput({
		theme: mytheme,
		width: 300,
		height: 20,
		disabled: true,
	});
	
	$("#figuracion_visualizar_cliente_ID").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		disabled: true,
	});
	
	$("#figuracion_visualizar_obra").jqxInput({
		theme: mytheme,
		width: 300,
		height: 20,
		disabled: true,
	});
	
	var GridClass = function (row, columnfield, value)
	{
		var Estado = $("#figuracion_visualizar_items_grid").jqxGrid("getcellvalue", row, "Estado");
		
		if (Estado == "Pendiente")
			return "orange";
		else if (Estado == "Proceso")
			return "blue";
		else if (Estado == "Finalizado")
			return "green";
		else
			return "";
	}
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: "Codigo", type: "string" },
			{ name: "Figura", type: "string" },
			{ name: "Imagen", type: "string" },
			{ name: "Nombre", type: "string" },
			{ name: "Dimensiones", type: "string" },
			{ name: "Detalle", type: "string" },
			{ name: "Cantidad", type: "decimal" },
			{ name: "Cantidad2", type: "decimal" },
			{ name: "Longitud", type: "decimal" },
			{ name: "Peso", type: "decimal" },
			{ name: "Ubicacion", type: "string" },
			{ name: "Estado", type: "string" },
		],
		updaterow: function (rowid, rowdata, commit)
		{
			if (State != "")
			{
				commit(true);
				State = "";
				return;
			}
			
			if (rowdata["Cantidad2"] > rowdata["Cantidad"])
			{
				Alerts_Box("La Cantidad ingresada supera a la Cantidad Total.", 3);
				commit(false);
				return;
			}
			
			if (rowdata["Cantidad2"] < 0)
			{
				Alerts_Box("La Cantidad ingresada debe ser superior a 0", 3);
				commit(false);
				return;
			}
			
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {
					"Figuracion_Visualizar":true,
					"Codigo":rowdata["Codigo"],
					"Figura":rowdata["Figura"],
					"Dimensiones":rowdata["Dimensiones"],
					"Cantidad":rowdata["Cantidad2"],
				},
				success: function (data, status, xhr)
				{
					switch(data[0]["MESSAGE"])
					{
						case "ERROR":
							commit(false);
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
						break;
						
						default:
							commit(true);
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
		},
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#figuracion_visualizar_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		source: GridDataAdapter,
		//rowsheight: auto,
		autorowheight: true,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: '100',
		pageable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{ text: '', datafield: 'Figura', width: "0%", editable: false, },
			{ text: '', datafield: 'Dimensiones', width: "0%", editable: false, },
			{ text: '', datafield: 'Codigo', width: "0%", editable: false, },
			{
				text: 'Figura', 
				datafield: 'Imagen', 
				width: "20%", 
				editable: false, 
				cellsrenderer: function (row, datafield, value) {
					return "<img style=\"margin-left: 5px;\" src=\"images/"+value+"\"/>";
				}, 
			},
			{
				text: 'Detalle', 
				datafield: 'Detalle', 
				width: "10%", 
				cellsalign: 'left', 
				editable: false, 
				cellsrenderer: function (row, datafield, value) {
					return "<div style=\"min-height:100px; margin: 10px 5px; white-space: normal;\"><span>"+value+"</span></div>";
				}, 
			},
			{ text: 'Figurado', datafield: 'Nombre', width: "15%", editable: false, },
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				width: "9%",
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{
				text: 'Cantidad Proc.',
				datafield: 'Cantidad2',
				width: "9%",
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				validation: function (cell, value)
				{
					var rowdata = $("#figuracion_visualizar_items_grid").jqxGrid("getrowdata", cell.row);
					if (value < 0.01)
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					else if (value > rowdata.Cantidad)
						return { result: false, message: "La Cantidad Procesada no puede ser mayor a la Cantidad Fijada." };
					else
						return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Longitud (m)',
				datafield: 'Longitud',
				width: "8%",
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{
				text: 'Peso (kg)',
				datafield: 'Peso',
				width: "8%",
				cellsformat: 'd2',
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
			},
			{
				text: 'Ubicacion', 
				datafield: 'Ubicacion', 
				width: "14%", 
				cellsalign: 'left', 
				editable: false, 
				cellsrenderer: function (row, datafield, value) {
					return "<div style=\"min-height:100px; margin: 10px 5px; white-space: normal;\"><span>"+value+"</span></div>";
				}, 
			},
			{
				text: 'Estado', 
				datafield: 'Estado', 
				width: "7%", 
				editable: false, 
				cellclassname: GridClass, 
				/*cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata)
				{
					if (rowdata.Cantidad2 == 0)
					{
						return "<div style=\"margin: 4px;\" class=\"jqx-left-align\">Pendiente</div>";
					}
					else if (rowdata.Cantidad2 > 0 && rowdata.Cantidad2 < rowdata.Cantidad)
					{
						return "Proceso";
					}
					else
					{
						return "Finalizado";
					}
				}*/
			},
		]
	});
	$("#figuracion_visualizar_items_grid").jqxGrid("hidecolumn", "Figura");
	$("#figuracion_visualizar_items_grid").jqxGrid("hidecolumn", "Dimensiones");
	$("#figuracion_visualizar_items_grid").jqxGrid("hidecolumn", "Codigo");
	$("#figuracion_visualizar_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#figuracion_visualizar_items_grid").on("cellendedit", function (event) 
	{
		var args = event.args;
		var dataField = event.args.datafield;
		var rowBoundIndex = event.args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		var rowData = args.row;
		
		if (dataField == "Cantidad2")
		{
			var Cantidad = $("#figuracion_visualizar_items_grid").jqxGrid("getcellvalue", rowBoundIndex, "Cantidad");
			if (value == 0)
				State = "Pendiente";
			else if (value > 0 && value < Cantidad)
				State = "Proceso";
			else
				State = "Finalizado";
			$("#figuracion_visualizar_items_grid").jqxGrid("setcellvalue", rowBoundIndex, "Estado", State);
		}
	});
	
	$("#figuracion_visualizar_imprimir1").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "warning"
	});
	$("#figuracion_visualizar_imprimir1").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		window.open("imprimir/cartilla_figuracion.php?Interno="+$("#figuracion_visualizar_interno").val()+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_visualizar_imprimir2").jqxButton({
		theme: mytheme,
		width: 100,
		height: 25,
		template: "warning"
	});
	$("#figuracion_visualizar_imprimir2").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		window.open("imprimir/cartilla_figuracion_hierros.php?Interno="+$("#figuracion_visualizar_interno").val()+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	if (!Admin && !Imprimir)
	{
		$("#figuracion_visualizar_imprimir1").jqxButton({ disabled: true });
		$("#figuracion_visualizar_imprimir2").jqxButton({ disabled: true });
	}
	
	LoadParameters();
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px; width:999px;">
		<tr>
			<td>
				Cartilla
			</td>
			<td>
				Ord. Produccion
			</td>
			<td colspan="2">
				Cliente
			</td>
			<td>
				Cliente ID
			</td>
			<td colspan="3">
				Obra
			</td>
		</tr>
		<tr>
			<td>
				<div id="figuracion_visualizar_interno"></div>
			</td>
			<td>
				<input type="text" id="figuracion_visualizar_orden_produccion"/>
			</td>
			<td colspan="2">
				<input type="text" id="figuracion_visualizar_cliente"/>
			</td>
			<td>
				<input type="text" id="figuracion_visualizar_cliente_ID"/>
			</td>
			<td colspan="3">
				<input type="text" id="figuracion_visualizar_obra"/>
			</td>
		</tr>
	</table>
	<div id="figuracion_visualizar_items_grid"></div>
	<table cellpadding="2" cellspacing="1" style="margin:20px 0px; width:250px;">
		<tr>
			<td>
				<input type="button" id="figuracion_visualizar_imprimir1" value="Imprimir"/>
			</td>
			<td>
				<input type="button" id="figuracion_visualizar_imprimir2" value="por Hierro"/>
			</td>
		</tr>
	</table>
</div>