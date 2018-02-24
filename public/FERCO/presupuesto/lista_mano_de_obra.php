<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var Clasificacion = "";
	var UnidadArray = new Array();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Presupuesto_Content");
	var Body = document.getElementById("Presupuesto_Lista_MO_Content");
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
				SystemMap("Lista Mano de Obra", true);
				LoadParameters(true);
				
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
	var Guardar = false;
	var Modificar = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Presupuesto" && $data[$i]["SubModulo"] == "Lista_Mano_de_Obra" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Presupuesto" && $data[$i]["SubModulo"] == "Lista_Mano_de_Obra" && $data[$i]["Modificar"] == "true")
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
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadParameters()
	{
		ClasificacionSource.data = {"Presupuesto_Clasificacion":true};
		UnidadSource.data = {"Presupuesto_Unidad":true};
		
		var ClasificacionDataAdapter = new $.jqx.dataAdapter(ClasificacionSource);
		$("#presupuesto_lista_mano_de_obra_clasificacion").jqxComboBox({source: ClasificacionDataAdapter});
		
		var UnidadDataAdapter = new $.jqx.dataAdapter(UnidadSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
				{
					UnidadArray.push(records[i]);
				}
			},
		});
		
		var Item = $("#presupuesto_lista_mano_de_obra_clasificacion").jqxComboBox("getSelectedItem");
		if (!Item)
			return;
		$("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("updatebounddata");
	};
	
	var ClasificacionSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Clasificacion", type: "string"},
		],
		url: "modulos/parametros.php",
	};
	
	var UnidadSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Unidad", type: "string"},
		],
		url: "modulos/parametros.php",
	};
	
	$("#presupuesto_lista_mano_de_obra_clasificacion").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: "startswithignorecase",
		autoComplete: true,
		promptText: "Seleccionar",
		selectedIndex: -1,
		displayMember: "Clasificacion",
		valueMember: "Clasificacion"
	});
	$("#presupuesto_lista_mano_de_obra_clasificacion").on("change", function (event)
	{
		if (event.args)
		{
			if (Clasificacion == event.args.item.value)
				return;
			
			Clasificacion = event.args.item.value;
			
			GridSource.data = {
				"Presupuesto_Listado_ManodeObra":true,
				"Clasificacion":Clasificacion,
			};
			var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
			$("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid({source: GridDataAdapter});
		}
		else
		{
			Clasificacion = "";
			$("#presupuesto_lista_mano_de_obra_clasificacion").jqxComboBox("clearSelection");
			$("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("clearSelection");
			$("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("clear");
		}
	});
	$("#presupuesto_lista_mano_de_obra_clasificacion").on("bindingComplete", function (event)
	{
		if (Clasificacion != "")
			$("#presupuesto_lista_mano_de_obra_clasificacion").jqxComboBox("selectItem", Clasificacion);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: "ID", type: "string" },
			{ name: "Codigo", type: "string" },
			{ name: "Nombre", type: "string" },
			{ name: "Grupo", type: "string" },
			{ name: "SubGrupo", type: "string" },
			{ name: "Unidad", type: "string" },
			{ name: "Uso", type: "decimal" },
			{ name: "Valor", type: "decimal" },
			{ name: "Notas", type: "string" },
		],
		cache: false,
		url: "modulos/datos_productos.php",
		updaterow: function (rowid, rowdata, commit)
		{
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {
					"Presupuesto_ManodeObra_Actualizar":true,
					"Clasificacion":Clasificacion,
					"ID":rowdata.ID,
					"Nombre":rowdata.Nombre,
					"Unidad":rowdata.Unidad,
					"Uso":rowdata.Uso,
					"Valor":rowdata.Valor,
					"Notas":rowdata.Notas,
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
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
						break;
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					commit(false);
					Alerts_Box("Ocurrió un error mientras se guardaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
					+"Error: "+errorThrown, 3);
				}
			});
		},
		deleterow: function (rowid, commit)
		{
			ClickOK = false;
			ClickCANCEL = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("getcellvalue", rowid, "ID");
					var Codigo = $("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("getcellvalue", rowid, "Codigo");
					
					$.ajax({
						dataType: "json",
						type: "POST",
						url: "modulos/guardar.php",
						data: {
							"Presupuesto_ManodeObra_Borrar":true,
							"Clasificacion":Clasificacion,
							"ID":ID,
							"Codigo":Codigo,
						},
						success: function (data, status, xhr)
						{
							switch(data[0]["MESSAGE"])
							{
								case "OK":
									commit(true);
								break;
								
								case "USED":
									commit(false);
									Alerts_Box("No es posible borrar este elemento debido a que esta siendo usado por un APU.", 4);
								break;
								
								case "ERROR":
									commit(false);
									Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
								break;
							}
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							commit(false);
							Alerts_Box("Ocurrió un error mientras se guardaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
							+"Error: "+errorThrown, 3);
						}
					});
				}
				
				if (ClickCANCEL == true)
				{
					clearInterval(CheckTimer);
					ClickCANCEL = false;
					commit(false);
				}
			}, 10);
		}
	};
	
	$("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		autoheight: true,
		enabletooltips: true,
		pagesizeoptions: ["10", "20", "30", "50", "100", "200"],
		pagesize: 20,
		sortable: true,
		pageable: true,
		showfilterrow: true,
		filterable: true,
		editable: true,
		editmode: "click",
		columns:
		[
			{
				text: "",
				datafield: "Del",
				columntype: "button",
				width: "3%",
				height: 20,
				pinned: true,
				filterable: false,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					if (Admin)
					{
						var selectedrowindex = $("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("getselectedrowindex");
						var rowscount = $("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("getdatainformation").rowscount;
						//if (selectedrowindex >= 0 && selectedrowindex < rowscount) { // Bug with RowFilter
						if (selectedrowindex >= 0) {
							var id = $("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("getrowid", selectedrowindex);
							$("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("deleterow", id);
						}
					}
				}
			},
			{ text: "", datafield: "ID", editable: false },
			{ text: "Codigo", datafield: "Codigo", width: "10%", height: 20, pinned: true, editable: false, },
			{ text: "Nombre", datafield: "Nombre", width: "20%", height: 20, pinned: true, editable: Admin ? true:Modificar, },
			{ text: "Grupo", datafield: "Grupo", width: "15%", height: 20, pinned: true, editable: false, },
			{ text: "SubGrupo", datafield: "SubGrupo", width: "15%", height: 20, pinned: true, editable: false, },
			{
				text: "Und",
				datafield: "Unidad",
				width: "4%",
				height: 20,
				editable: Admin ? true:Modificar,
				columntype: "combobox",
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: UnidadArray,
						dropDownWidth: 100,
						selectedIndex: -1,
						displayMember: "Unidad",
						valueMember: "Unidad",
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
					var len = UnidadArray.length;
					for (i = 0; i < len; i++)
					{
						if (UnidadArray[i]["Unidad"] == newvalue)
							return;
					}
					return oldvalue;
				}
			},
			{
				text: "Uso",
				datafield: "Uso",
				width: "6%",
				height: 20,
				cellsformat: "p2",
				cellsalign: "right",
				columntype: "numberinput",
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: "Unitario",
				datafield: "Valor",
				width: "12%",
				height: 20,
				cellsformat: "c2",
				cellsalign: "right",
				columntype: "numberinput",
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{ text: "Notas", datafield: "Notas", width: "30%", height: 20, editable: Admin ? true:Modificar, filterable: false,},
		]
	});
	$("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("hidecolumn", "ID");
	$("#presupuesto_lista_mano_de_obra_items_grid").jqxGrid("localizestrings", localizationobj);
	
	LoadParameters();
	CheckRefresh();
});
</script>
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
				Base de Datos
			</td>
			<td>
				<div id="presupuesto_lista_mano_de_obra_clasificacion"></div>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 1000px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<div id="presupuesto_lista_mano_de_obra_items_grid"></div>
	<br />
</div>
