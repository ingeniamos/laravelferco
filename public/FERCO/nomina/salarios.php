<?php
session_start();
?>
<style>    
.orange {
	color: black;
	background-color: rgba(255,242,204,1);
}
.red {
	color: black;
	background-color: rgba(251,229,214,1);
}
.orange:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .orange:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(255,242,204,1);
}
.red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected), .jqx-widget .red:not(.jqx-grid-cell-hover):not(.jqx-grid-cell-selected) {
	color: black;
	background-color: rgba(251,229,214,1);
}
</style>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	
	var Timer1 = 0;
	var Timer2 = 0;
	var ClienteData = new Array();
	var CargoData = new Array();
	var SaludData = new Array();
	var PensionData = new Array();
	var CesantiaData = new Array();
	var ContratoData = new Array();
	var MyDate = new Date();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Nomina_Content");
	var Body = document.getElementById("Nomina_Salarios_Content");
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
				SystemMap("Salarios", true);
				
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
	var Modificar = false;
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Salarios" && $data[$i]["Modificar"] == "true")
				{
		?>
					Modificar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Salarios" && $data[$i]["Guardar"] == "true")
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
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		data: {"Clientes_Nomina":true},
		url: "modulos/datos.php",
		async: false
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ClienteData.push(records[i]);
			}
		}
	});
	
	var CargoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Cargo', type: 'string'},
		],
		data: {"Nomina_Cargo":true},
		url: "modulos/parametros.php",
		async: false
	};
	var CargoDataAdapter = new $.jqx.dataAdapter(CargoSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				CargoData.push(records[i]);
			}
		}
	});
	
	var SaludSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Salud', type: 'string'},
		],
		data: {"Nomina_Salud":true},
		url: "modulos/parametros.php",
		async: false
	};
	var SaludDataAdapter = new $.jqx.dataAdapter(SaludSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				SaludData.push(records[i]);
			}
		}
	});
	
	var PensionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Pension', type: 'string'},
		],
		data: {"Nomina_Pension":true},
		url: "modulos/parametros.php",
		async: false
	};
	var PensionDataAdapter = new $.jqx.dataAdapter(PensionSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				PensionData.push(records[i]);
			}
		}
	});
	
	var CesantiaSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Cesantia', type: 'string'},
		],
		data: {"Nomina_Cesantia":true},
		url: "modulos/parametros.php",
		async: false
	};
	var CesantiaDataAdapter = new $.jqx.dataAdapter(CesantiaSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				CesantiaData.push(records[i]);
			}
		}
	});
	
	var ContratoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Contrato', type: 'string'},
		],
		data: {"Nomina_Contrato":true},
		url: "modulos/parametros.php",
		async: false
	};
	var ContratoDataAdapter = new $.jqx.dataAdapter(ContratoSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ContratoData.push(records[i]);
			}
		}
	});
	
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
	
	$("#nomina_salarios_grupos").jqxComboBox(
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
	$("#nomina_salarios_grupos").bind('change', function (event) {
		if (!event.args)
		{
			$("#nomina_salarios_grupos").jqxComboBox('clearSelection');
		}
		SalariosSource.data = {
			"Nomina_Salarios":true,
			"Grupo":$("#nomina_salarios_grupos").val(),
		};
		$("#nomina_salarios_item_grid").jqxGrid("updatebounddata");
	});
	
	function Add_Row()
	{
		clearTimeout(Timer1);
		Timer1 = setTimeout(function()
		{
			var Val = $("#nomina_salarios_cliente").jqxComboBox('getSelectedItem');
		
			if (! Val || Val < 0) {
				Alerts_Box("Favor Seleccionar un Cliente!", 3);
				WaitClick_Combobox("nomina_salarios_cliente");
				return;
			}
			
			var datainfo = $("#nomina_salarios_item_grid").jqxGrid("getdatainformation");
			var count = datainfo.rowscount;
			
			var datarow = [{
				"ID":0,
				"Nombre":Val.label,
				"ClienteID":Val.value,
				"Horas":240,
			}];
			
			$("#nomina_salarios_item_grid").jqxGrid("addrow", count, datarow);
			$("#nomina_salarios_cliente").jqxComboBox("clearSelection");
			$("#nomina_salarios_cliente_ID").jqxComboBox("clearSelection");
		},500);
	};
	
	var cellclass = function (row, columnfield, value)
	{
		var Fecha = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', row, "Fin");
		var timeDiff = Math.abs(Fecha.getTime() - MyDate.getTime());
		var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
		diffDays = diffDays - 1;
		
		if (diffDays < 31)
		{
			return 'red';
		}
		else if (diffDays < 61)
		{
			return 'orange';
		}
		else
			return '';
	}
	
	var SalariosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'ID', type: 'string'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'},
			{ name: 'Cargo', type: 'string'},
			{ name: 'Basico', type: 'decimal'},
			{ name: 'Transp', type: 'decimal'},
			{ name: 'Bono', type: 'decimal'},
			{ name: 'Horas', type: 'int'},
			{ name: 'Hora_Ini1', type: 'date'},
			{ name: 'Hora_Fin1', type: 'date'},
			{ name: 'Hora_Ini2', type: 'date'},
			{ name: 'Hora_Fin2', type: 'date'},
			{ name: 'Inicio', type: 'date'},
			{ name: 'Fin', type: 'date'},
			{ name: 'Pension', type: 'string'},
			{ name: 'RH', type: 'string'},
			{ name: 'Salud', type: 'string'},
			{ name: 'Contrato', type: 'string'},
			{ name: 'Cesantia', type: 'string'},
			{ name: 'Tipo', type: 'string'},
			{ name: 'Activo', type: 'bool'},
		],
		data: {"Nomina_Salarios":true},
		url: "modulos/datos.php",
		async: true,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (Guardar == false && Admin == false)
			{
				commit(false);
				return;
			}
			
			if (rowdata[0]["ClienteID"] != "")
			{
				$.ajax({
					dataType: 'text',
					url: "modulos/guardar.php",
					data: {
						"Nomina_Salarios_Agregar":true,
						"ClienteID":rowdata[0]["ClienteID"],
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
							$("#nomina_salarios_item_grid").jqxGrid("updatebounddata");
							$("#nomina_salarios_item_grid").jqxGrid("ensurerowvisible", rowid);
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
			var ID = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "ID");
			var ClienteID = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "ClienteID");
			var Cargo = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Cargo");
			var Basico = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Basico");
			var Transp = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Transp");
			var Bono = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Bono");
			var Horas = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Horas");
			var Hora_Ini1 = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Hora_Ini1");
			var Hora_Fin1 = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Hora_Fin1");
			var Hora_Ini2 = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Hora_Ini2");
			var Hora_Fin2 = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Hora_Fin2");
			var Fecha_Ini = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Inicio");
			var Fecha_Fin = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Fin");
			var RH = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "RH");
			var Salud = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Salud");
			var Pension = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Pension");
			var Contrato = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Contrato");
			var Cesantia = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Cesantia");
			var Activo = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "Activo");
			//alert(ID+" - "+ClienteID+" - "+Basico+" - "+Transp+" - "+Bono+" - "+Horas+" - "+Fecha_Ini+" - "+Fecha_Fin+" - "+Salud+" - "+Pension+" - "+Contrato+" - "+Cesantia+" - "+Activo)
			$.ajax({
				dataType: 'text',
				url: "modulos/guardar.php",
				data: {
					"Nomina_Salarios_Actualizar":true,
					"ID":ID,
					"ClienteID":ClienteID,
					"Cargo":Cargo,
					"Basico":Basico,
					"Transp":Transp,
					"Bono":Bono,
					"Horas":Horas,
					"Hora_Ini1":(Hora_Ini1 == "00:00:00") ? Hora_Ini1:GetFormattedTime(Hora_Ini1),
					"Hora_Fin1":(Hora_Fin1 == "00:00:00") ? Hora_Fin1:GetFormattedTime(Hora_Fin1),
					"Hora_Ini2":(Hora_Ini2 == "00:00:00") ? Hora_Ini2:GetFormattedTime(Hora_Ini2),
					"Hora_Fin2":(Hora_Fin2 == "00:00:00") ? Hora_Fin2:GetFormattedTime(Hora_Fin2),
					"Fecha_Ini":(Fecha_Ini == "0000-00-00") ? Fecha_Ini:GetFormattedDate(Fecha_Ini),
					"Fecha_Fin":(Fecha_Fin == "0000-00-00") ? Fecha_Fin:GetFormattedDate(Fecha_Fin),
					"RH":RH,
					"Salud":Salud,
					"Pension":Pension,
					"Contrato":Contrato,
					"Cesantia":Cesantia,
					"Activo":Activo,
				},
				async: false,
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
			if (Guardar == false && Admin == false)
			{
				commit(false);
				return;
			}
			
			ClickOK = false;
			Alerts_Box("Esta a punto de Eliminar un Registro...<br /> Desea Continuar?", 4, true);
			
			var CheckTimer = setInterval(function()
			{
				if (ClickOK == true)
				{
					clearInterval(CheckTimer);
					ClickOK = false;
					
					var ID = $("#nomina_salarios_item_grid").jqxGrid('getcellvalue', rowid, "ID");
					$.ajax({
						dataType: 'text',
						url: "modulos/guardar.php",
						data: {
							"Nomina_Salarios_Borrar":true,
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
	var SalariosDataAdapter = new $.jqx.dataAdapter(SalariosSource);
	
	$("#nomina_salarios_item_grid").jqxGrid(
	{
		theme: mytheme,
		width: 1000,
		source: SalariosDataAdapter,
		enabletooltips: true,
		sortable: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '50', '100'],
		pagesize: 20,
		autoheight: true,
		editable: true,
		editmode: 'dblclick',
		showfilterrow: true,
		filterable: true,
		showtoolbar: true,
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<div id="nomina_salarios_cliente" class="GridButtons"></div><div id="nomina_salarios_cliente_ID" class="GridButtons"></div>'+
				'<input type="button" id="nomina_salarios_addrowbutton" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="nomina_salarios_deleterowbutton" class="GridButtons" value="Borrar Seleccionado"/>'
			);
			$("#nomina_salarios_cliente").jqxComboBox(
			{
				theme: mytheme,
				width: 300,
				height: 20,
				source: ClienteData,
				searchMode: 'containsignorecase',
				autoComplete: true,
				promptText: 'Seleccionar Nombre',
				selectedIndex: -1,
				displayMember: 'Nombre',
				valueMember: 'ClienteID'
			});
			$("#nomina_salarios_cliente").bind('change', function (event) {
				if (!event.args)
				{
					$("#nomina_salarios_cliente").jqxComboBox('clearSelection');
					$("#nomina_salarios_cliente_ID").jqxComboBox('clearSelection');
				}
				else
				{
					if ($("#nomina_salarios_cliente_ID").val() != event.args.item.value)
						$("#nomina_salarios_cliente_ID").val(event.args.item.value);
				}
			});
			$("#nomina_salarios_cliente").keyup(function(event) {
				if(event.which == 13)
				{
					Add_Row();
				}
			});
			
			$("#nomina_salarios_cliente_ID").jqxComboBox(
			{
				theme: mytheme,
				width: 150,
				height: 20,
				source: ClienteData,
				searchMode: 'containsignorecase',
				autoComplete: true,
				promptText: 'Seleccionar ID',
				selectedIndex: -1,
				displayMember: 'ClienteID',
				valueMember: 'ClienteID'
			});
			$("#nomina_salarios_cliente_ID").bind('change', function (event) {
				if (!event.args)
				{
					var item = $("#nomina_salarios_cliente_ID").jqxComboBox("getItemByValue", $("#nomina_salarios_cliente_ID").val());
					if (item != undefined)
					{
						$("#nomina_salarios_cliente").val(item.value);
						return;
					}
					$("#nomina_salarios_cliente").jqxComboBox('clearSelection');
					$("#nomina_salarios_cliente_ID").jqxComboBox('clearSelection');
				}
				else
				{
					if ($("#nomina_salarios_cliente").val() != event.args.item.value)
						$("#nomina_salarios_cliente").val(event.args.item.value);
				}
			});
			
			$("#nomina_salarios_addrowbutton").jqxButton({theme: mytheme, template: "success"});
			$("#nomina_salarios_deleterowbutton").jqxButton({theme: mytheme, template: "danger"});
			if (Admin == false && Guardar == false)
			{
				$("#nomina_salarios_addrowbutton").jqxButton({ disabled: true });
				$("#nomina_salarios_deleterowbutton").jqxButton({ disabled: true });
			}
			// create new row.
			$("#nomina_salarios_addrowbutton").on('click', function ()
			{
				Add_Row();
			});
			// delete row.
			$("#nomina_salarios_deleterowbutton").on('click', function ()
			{
				var selectedrowindex = $("#nomina_salarios_item_grid").jqxGrid('getselectedrowindex');
				var rowscount = $("#nomina_salarios_item_grid").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#nomina_salarios_item_grid").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#nomina_salarios_item_grid").jqxGrid('deleterow', id);
				}
			});
		},
		columns:
		[
			{ text: '', datafield: 'ID', editable: false, filterable: false, width: 0 },
			{ text: 'Nombre', datafield: 'Nombre', align: "center", editable: false, filtertype: 'input', width: 200, height: 20, pinned: true, cellclassname: cellclass },
			{ text: 'Cedula', datafield: 'ClienteID', align: "center", editable: false, filtertype: 'input', width: 120, height: 20, pinned: true, cellclassname: cellclass },
			{
				text: 'Cargo',
				datafield: 'Cargo',
				align: "center",
				columntype: 'combobox',
				cellclassname: cellclass,
				width: 120,
				height: 20,
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: CargoData,
						dropDownHeight: 125,
						displayMember: 'Cargo',
						valueMember: 'Cargo',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Basico',
				datafield: 'Basico',
				align: "center",
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				cellclassname: cellclass,
				filtertype: 'number',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'S. Transp.',
				datafield: 'Transp',
				align: "center",
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				cellclassname: cellclass,
				filtertype: 'number',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Bonificacion',
				datafield: 'Bono',
				align: "center",
				width: 120,
				height: 20,
				cellsformat: 'c2',
				cellsalign: 'right',
				columntype: 'numberinput',
				cellclassname: cellclass,
				filtertype: 'number',
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Horas',
				datafield: 'Horas',
				align: "center",
				width: 50,
				height: 20,
				cellsalign: 'center',
				columntype: 'numberinput',
				cellclassname: cellclass,
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 3, max: 999, spinButtons: false });
				}
			},
			{
				text: "Hora Ini",
				datafield: "Hora_Ini1",
				align: "center",
				columngroup: "TRUNO1",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: true,
				filterable: false,
				cellsformat: "HH:mm",
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ 
						showCalendarButton: false,
						showTimeButton: true
					});
				}
			},
			{
				text: "Hora Fin",
				datafield: "Hora_Fin1",
				align: "center",
				columngroup: "TRUNO1",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: true,
				filterable: false,
				cellsformat: "HH:mm",
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ 
						showCalendarButton: false,
						showTimeButton: true
					});
				}
			},
			{
				text: "Hora Ini",
				datafield: "Hora_Ini2",
				align: "center",
				columngroup: "TRUNO2",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: true,
				filterable: false,
				cellsformat: "HH:mm",
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ 
						showCalendarButton: false,
						showTimeButton: true
					});
				}
			},
			{
				text: "Hora Fin",
				datafield: "Hora_Fin2",
				align: "center",
				columngroup: "TRUNO2",
				columntype: "datetimeinput",
				width: "7%",
				height: 20,
				editable: true,
				filterable: false,
				cellsformat: "HH:mm",
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ 
						showCalendarButton: false,
						showTimeButton: true
					});
				}
			},
			{
				text: 'Tipo Contrato',
				datafield: 'Contrato',
				align: "center",
				columngroup: "CONTRATO",
				columntype: 'combobox',
				cellclassname: cellclass,
				width: 130,
				height: 20,
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: ContratoData,
						dropDownHeight: 125,
						displayMember: 'Contrato',
						valueMember: 'Contrato',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Fecha Ini.',
				datafield: 'Inicio',
				align: "center",
				columngroup: "CONTRATO",
				columntype: 'datetimeinput',
				cellclassname: cellclass,
				width: 90,
				height: 20,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{
				text: 'Fecha Fin.',
				datafield: 'Fin',
				align: "center",
				columngroup: "CONTRATO",
				columntype: 'datetimeinput',
				cellclassname: cellclass,
				width: 90,
				height: 20,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{ text: 'RH', datafield: 'RH', align: "center", editable: true, filterable: false, width: 40, height: 20, cellclassname: cellclass },
			{
				text: 'Salud',
				datafield: 'Salud',
				align: "center",
				columntype: 'combobox',
				cellclassname: cellclass,
				width: 150,
				height: 20,
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: SaludData,
						dropDownHeight: 125,
						displayMember: 'Salud',
						valueMember: 'Salud',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Pensiones',
				datafield: 'Pension',
				align: "center",
				columntype: 'combobox',
				cellclassname: cellclass,
				width: 150,
				height: 20,
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: PensionData,
						dropDownHeight: 125,
						displayMember: 'Pension',
						valueMember: 'Pension',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{
				text: 'Cesantias',
				datafield: 'Cesantia',
				align: "center",
				columntype: 'combobox',
				cellclassname: cellclass,
				width: 150,
				height: 20,
				filterable: false,
				editable: Admin ? true:Modificar,
				createeditor: function (row, column, editor) {
					editor.jqxComboBox({
						source: CesantiaData,
						dropDownHeight: 125,
						displayMember: 'Cesantia',
						valueMember: 'Cesantia',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{ text: 'Grupo', datafield: 'Tipo', align: "center", filterable: false, editable: false, width: 120, height: 20, cellclassname: cellclass },
			{
				text: 'Activo',
				datafield: 'Activo',
				align: "center",
				columntype: 'checkbox',
				cellclassname: cellclass,
				width: 50,
				editable: Admin ? true:Modificar,
				filtertype: 'bool',
				//filterable: false,
			},
		],
		columngroups: 
		[
			{ text: "TURNO 1", align: "center", name: "TRUNO1" },
			{ text: "TURNO 2", align: "center", name: "TRUNO2" },
			{ text: "CONTRATO", align: "center", name: "CONTRATO" },
		]
	});
	$("#nomina_salarios_item_grid").on("bindingcomplete", function (event)
	{
		$("#nomina_salarios_item_grid").jqxGrid('hidecolumn', 'ID');
		$("#nomina_salarios_item_grid").jqxGrid('localizestrings', localizationobj);
	});
	
	CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td>
				Grupo Nomina
			</td>
			<td>
				<div id="nomina_salarios_grupos"></div>
			</td>
		</tr>
	</table>
	<div id="nomina_salarios_item_grid"></div>
</div>