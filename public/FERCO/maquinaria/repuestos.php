<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var PartesArray = new Array();
	var Timer1 = 0;
	var Timer2 = 0;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Maquinaria_Content");
	var Body = document.getElementById("Maquinaria_Repuestos_Content");
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
				SystemMap("Repuestos", true);
				//ReDefine();
				//Reload();
				
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
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Maquinaria" && $data[$i]["SubModulo"] == "Repuestos" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
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
	
	$("#maquinaria_repuestos_codigo").jqxInput({
		theme: mytheme,
		width: 90,
		height: 20,
	});
	
	$("#maquinaria_repuestos_nombre").jqxInput({
		theme: mytheme,
		width: 220,
		height: 20,
	});
	
	$("#maquinaria_repuestos_parte1").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar...',
		selectedIndex: -1,
		displayMember: 'Parte',
		valueMember: 'ID'
	});
	$("#maquinaria_repuestos_parte1").on("change", function (event)
	{
		if (event.args)
		{
			//
		}
		else
		{
			var item_value = $("#maquinaria_repuestos_parte1").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#maquinaria_repuestos_parte1").jqxComboBox('clearSelection');
			}
			else
			{
				var value = $("#maquinaria_repuestos_parte1").val();
				
				var item = $("#maquinaria_repuestos_parte1").jqxComboBox('getItems');
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#maquinaria_repuestos_parte1").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#maquinaria_repuestos_parte1").jqxComboBox('clearSelection');
			}
		}
	});
	
	$("#maquinaria_repuestos_peso").jqxNumberInput({
		theme: mytheme,
		width: 80,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: 'Kg',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 6,
		max: 999999.99,
	});
	
	$("#maquinaria_repuestos_valor").jqxNumberInput({
		theme: mytheme,
		width: 120,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		symbol: '$',
		decimalDigits: 2,
		digits: 12,
		max: 999999999999,
	});
	
	$("#maquinaria_repuestos_add1").jqxButton({
		width: 70,
		height: 25,
		template: "success"
	});
	$("#maquinaria_repuestos_add1").on("click", function ()
	{
		Add_Row1();
	});
	
	function Add_Row1()
	{
		var Codigo = $("#maquinaria_repuestos_codigo").val();
		var Nombre = $("#maquinaria_repuestos_nombre").val();
		var Parte = $("#maquinaria_repuestos_parte1").jqxComboBox('getSelectedItem');
		var Peso = $("#maquinaria_repuestos_peso").val();
		var Valor = $("#maquinaria_repuestos_valor").val();
		
		if ( Codigo == "") {
			Alerts_Box("Favor Ingresar un Codigo.", 3);
			WaitClick_Input("maquinaria_repuestos_codigo");
			return;
		}
		
		if ( Nombre == "") {
			Alerts_Box("Favor Ingresar un Nombre.", 3);
			WaitClick_Input("maquinaria_repuestos_nombre");
			return;
		}
		
		if (!Parte)
		{
			Alerts_Box("Favor Seleccionar una Parte.", 3);
			WaitClick_Combobox("maquinaria_repuestos_parte1");
			return;
		}
		
		if ( Peso < 1 ) {
			Alerts_Box("Favor Ingresar el Peso del Repuesto.", 3);
			WaitClick_NumberInput("maquinaria_repuestos_peso");
			return;
		}
		
		if ( Valor < 1 ) {
			Alerts_Box("Favor Ingresar un Valor.", 3);
			WaitClick_NumberInput("maquinaria_repuestos_valor");
			return;
		}
		
		var datainfo = $("#maquinaria_repuestos_items_grid1").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Codigo":Codigo,
			"Nombre":Nombre,
			"Parte":Parte.label,
			"Peso":Peso,
			"Valor":Valor
		}];
		
		$("#maquinaria_repuestos_items_grid1").jqxGrid("addrow", count, datarow);
		$("#maquinaria_repuestos_items_grid1").jqxGrid("ensurerowvisible", count);
		$("#maquinaria_repuestos_codigo").val("");
		$("#maquinaria_repuestos_nombre").val("");
		$("#maquinaria_repuestos_parte1").jqxComboBox("clearSelection");
		$("#maquinaria_repuestos_peso").val("0");
		$("#maquinaria_repuestos_valor").val("0");
	};
	
	var ParteCEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({
			selectedIndex: -1,
			displayMember: 'Parte',
			valueMember: 'Parte'
		});
	};
	
	var ParteIEditor = function(row, cellValue, editor, cellText, width, height)
	{
		editor.jqxComboBox({ source: PartesArray });
		var item = editor.jqxComboBox("getItemByValue", cellValue);
		if (item != undefined)
			editor.jqxComboBox({ selectedIndex: item.index });
		else
			editor.jqxComboBox({ selectedIndex: -1 });
	};
	
	var GridSource1 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'ID', type: 'string' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Parte', type: 'string' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Valor', type: 'decimal' },
		],
		cache: false,
		data: {"Repuestos":true},
		url: "modulos/datos_productos.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Codigo"] != "")
			{
				$.ajax({
					dataType: 'text',
					type: 'POST',
					url: "modulos/guardar.php",
					data: {
						"Repuestos_Agregar":true,
						"Codigo":rowdata[0]["Codigo"],
						"Nombre":rowdata[0]["Nombre"],
						"Parte":rowdata[0]["Parte"],
						"Peso":rowdata[0]["Peso"],
						"Valor":rowdata[0]["Valor"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#maquinaria_repuestos_items_grid1").jqxGrid("updatebounddata");
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
					}
				});
			}
			else
			{
				commit(false);
			}
		},
		updaterow: function (rowid, rowdata, commit)
		{
			var ID = $("#maquinaria_repuestos_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
			var Codigo = $("#maquinaria_repuestos_items_grid1").jqxGrid('getcellvalue', rowid, "Codigo");
			var Nombre = $("#maquinaria_repuestos_items_grid1").jqxGrid('getcellvalue', rowid, "Nombre");
			var Parte = $("#maquinaria_repuestos_items_grid1").jqxGrid('getcellvalue', rowid, "Parte");
			var Peso = $("#maquinaria_repuestos_items_grid1").jqxGrid('getcellvalue', rowid, "Peso");
			var Valor = $("#maquinaria_repuestos_items_grid1").jqxGrid('getcellvalue', rowid, "Valor");
			
			$.ajax({
				dataType: 'text',
				type: 'POST',
				url: "modulos/guardar.php",
				data: {
					"Repuestos_Actualizar":true,
					"ID":ID,
					"Codigo":Codigo,
					"Nombre":Nombre,
					"Parte":Parte,
					"Peso":Peso,
					"Valor":Valor,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data != "")
					{
						Alerts_Box("Ocurrio un Error al Intentar Actualizar los Datos!<br/>Intente luego de unos segundos...", 3);
						commit(false);
					}
					else
						commit(true);
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
					commit(false);
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
					
					var ID = $("#maquinaria_repuestos_items_grid1").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						type: 'POST',
						url: "modulos/guardar.php",
						data: {
							"Repuestos_Borrar":true,
							"ID":ID,
						},
						async: true,
						success: function (data, status, xhr)
						{
							if (data != "")
							{
								Alerts_Box("Ocurrio un Error al Intentar Borrar los Datos!<br/>Intente luego de unos segundos...", 3);
								commit(false);
							}
							else
								commit(true);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							alert(textStatus+ " - " +errorThrown);
							commit(false);
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
	var GridDataAdapter1 = new $.jqx.dataAdapter(GridSource1);
	
	$("#maquinaria_repuestos_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 750,
		autoheight: true,
		source: GridDataAdapter1,
		enabletooltips: true,
		enablebrowserselection: true,
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
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				filterable: false,
				width: '4%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					if (Admin)
					{
						var selectedrowindex = $("#maquinaria_repuestos_items_grid1").jqxGrid('getselectedrowindex');
						var rowscount = $("#maquinaria_repuestos_items_grid1").jqxGrid('getdatainformation').rowscount;
						if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
							var id = $("#maquinaria_repuestos_items_grid1").jqxGrid('getrowid', selectedrowindex);
							$("#maquinaria_repuestos_items_grid1").jqxGrid('deleterow', id);
						}
					}
				}
			},
			{ text: 'Codigo', datafield: 'Codigo', editable: false, width: '12%', height: 20, filterable: true },
			{ text: 'Nombre', datafield: 'Nombre', editable: Admin ? true:Guardar, width: '37%', height: 20, filterable: true },
			//{ text: 'Parte', datafield: 'Parte', editable: Admin ? true:Guardar, width: '20%', height: 20, filterable: true },
			{
				text: 'Parte', datafield: 'Parte', columntype: 'combobox', width: '20%', height: 20, editable: Admin ? true:Guardar,
				createeditor: ParteCEditor,
				initeditor: ParteIEditor,
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue)
				{
					if (PartesArray.indexOf(newvalue) < 0 || newvalue == "" || newvalue == oldvalue)
						return oldvalue;
				}
			},
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: Admin ? true:Guardar,
				width: '7%',
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18, spinButtons: false });
				}
			},
			{
				text: 'Valor',
				datafield: 'Valor',
				editable: Admin ? true:Guardar,
				width: '20%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'c2',
				columntype: 'numberinput',
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 18, spinButtons: false });
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%', height: 20 },
		],
	});
	$("#maquinaria_repuestos_items_grid1").on("bindingcomplete", function (event)
	{
		$("#maquinaria_repuestos_items_grid1").jqxGrid('hidecolumn', 'ID');
		$("#maquinaria_repuestos_items_grid1").jqxGrid('localizestrings', localizationobj);
	});
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 2 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	$("#maquinaria_repuestos_parte2").jqxInput({
		theme: mytheme,
		width: 140,
		height: 20,
	});
	
	$("#maquinaria_repuestos_add2").jqxButton({
		width: 70,
		height: 25,
		template: "success"
	});
	$("#maquinaria_repuestos_add2").on("click", function ()
	{
		Add_Row2();
	});
	
	function Add_Row2()
	{
		var Parte = $("#maquinaria_repuestos_parte2").val();
		
		if ( Parte == "") {
			Alerts_Box("Favor Ingresar la Parte.", 3);
			WaitClick_Input("maquinaria_repuestos_parte2");
			return;
		}
		
		var datainfo = $("#maquinaria_repuestos_items_grid2").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var datarow = [{
			"ID":0,
			"Parte":Parte,
		}];
		
		$("#maquinaria_repuestos_items_grid2").jqxGrid("addrow", count, datarow);
		$("#maquinaria_repuestos_items_grid2").jqxGrid("ensurerowvisible", count);
		$("#maquinaria_repuestos_parte2").val("");
	};
	
	var GridSource2 =
	{
		datatype: "json",
		datafields: [
			{ name: "ID", type: "int"},
			{ name: "Parte", type: "string"},
		],
		cache: false,
		data: {"Maquinaria_Repuestos_Partes":true},
		url: "modulos/parametros.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Parte"] != "")
			{
				$.ajax({
					dataType: 'text',
					type: 'POST',
					url: "modulos/guardar.php",
					data: {
						"Repuestos_Partes_Agregar":true,
						"Parte":rowdata[0]["Parte"],
					},
					async: true,
					success: function (data, status, xhr)
					{
						if (data != "")
						{
							Alerts_Box("Ocurrio un Error al Intentar Agregar los Datos!<br/>Intente luego de unos segundos...", 3);
							commit(false);
						}
						else
						{
							commit(true);
							$("#maquinaria_repuestos_items_grid2").jqxGrid("updatebounddata");
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
						commit(false);
					}
				});
			}
			else
			{
				commit(false);
			}
		},
		updaterow: function (rowid, rowdata, commit)
		{
			var ID = $("#maquinaria_repuestos_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
			var Parte = $("#maquinaria_repuestos_items_grid2").jqxGrid('getcellvalue', rowid, "Parte");
			
			$.ajax({
				dataType: 'text',
				type: 'POST',
				url: "modulos/guardar.php",
				data: {
					"Repuestos_Partes_Actualizar":true,
					"ID":ID,
					"Parte":Parte,
				},
				async: true,
				success: function (data, status, xhr)
				{
					if (data != "")
					{
						Alerts_Box("Ocurrio un Error al Intentar Actualizar los Datos!<br/>Intente luego de unos segundos...", 3);
						commit(false);
					}
					else
					{
						commit(true);
						$("#maquinaria_repuestos_parte1").jqxComboBox("updateItem", { label:Parte, value:ID }, ID);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
					commit(false);
				}
			});
		},
		deleterow: function (rowid, commit)
		{
			ClickOK = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#maquinaria_repuestos_items_grid2").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						type: 'POST',
						url: "modulos/guardar.php",
						data: {
							"Repuestos_Partes_Borrar":true,
							"ID":ID,
						},
						async: true,
						success: function (data, status, xhr)
						{
							if (data != "")
							{
								Alerts_Box("Ocurrio un Error al Intentar Borrar los Datos!<br/>Intente luego de unos segundos...", 3);
								commit(false);
							}
							else
								commit(true);
						},
						error: function (jqXHR, textStatus, errorThrown) {
							alert(textStatus+ " - " +errorThrown);
							commit(false);
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
		},
	};
	var GridDataAdapter2 = new $.jqx.dataAdapter(GridSource2, 
	{
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				PartesArray.push(records[i]["Parte"]);
			}
		},
		loadComplete: function (records)
		{
			//PartesArray = records;
			$("#maquinaria_repuestos_parte1").jqxComboBox({source: records});
		},
	});
	
	$("#maquinaria_repuestos_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 220,
		height: 300,
		source: GridDataAdapter2,
		enabletooltips: true,
		enablebrowserselection: true,
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '12.5%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#maquinaria_repuestos_items_grid2").jqxGrid('getselectedrowindex');
					var rowscount = $("#maquinaria_repuestos_items_grid2").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#maquinaria_repuestos_items_grid2").jqxGrid('getrowid', selectedrowindex);
						$("#maquinaria_repuestos_items_grid2").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', editable: false, width: '0%' },
			{ text: 'Parte', datafield: 'Parte', editable: true, width: '87.5%', height: 20 },
		]
	});
	$("#maquinaria_repuestos_items_grid2").on("bindingcomplete", function (event) {
		$("#maquinaria_repuestos_items_grid2").jqxGrid("hidecolumn", "ID");
		$("#maquinaria_repuestos_items_grid2").jqxGrid("localizestrings", localizationobj);
	});
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<div style="float: left; margin-left:0px;">
		<table cellpadding="3" cellspacing="1" style="margin:5px 0px 20px 0px;">
			<tr>
				<td>
					Codigo
				</td>
				<td>
					Nombre
				</td>
				<td>
					Parte
				</td>
				<td>
					Peso
				</td>
				<td>
					Valor
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					<input type="text" id="maquinaria_repuestos_codigo"/>
				</td>
				<td>
					<input type="text" id="maquinaria_repuestos_nombre"/>
				</td>
				<td>
					<div id="maquinaria_repuestos_parte1"></div>
				</td>
				<td>
					<div id="maquinaria_repuestos_peso"></div>
				</td>
				<td>
					<div id="maquinaria_repuestos_valor"></div>
				</td>
				<td>
					<input type="button" id="maquinaria_repuestos_add1" value="Añadir"/>
				</td>
			</tr>
		</table>
		<div id="maquinaria_repuestos_items_grid1"></div>
	</div>
	<div style="float: left; margin-left:15px;">
		<table cellpadding="3" cellspacing="1" style="margin:5px 0px 20px 0px;">
			<tr>
				<td>
					Parte
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					<input type="text" id="maquinaria_repuestos_parte2"/>
				</td>
				<td>
					<input type="button" id="maquinaria_repuestos_add2" value="Añadir"/>
				</td>
			</tr>
		</table>
		<div id="maquinaria_repuestos_items_grid2"></div>
	</div>
</div>
