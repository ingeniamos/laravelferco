<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	//----- GOLBALS
	var mytheme = "energyblue";
	var Timer1 = 0;
	var Timer2 = 0;
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		//data: {"Clientes_Nomina":true},
		url: "modulos/datos.php",
	};
	var ClienteDataAdapter1 = new $.jqx.dataAdapter(ClienteSource);
	var ClienteDataAdapter2 = new $.jqx.dataAdapter(ClienteSource);
	
	$("#parametros_inicio_cliente").jqxComboBox(
	{
		theme: mytheme,
		width: 355,
		height: 20,
		source: ClienteDataAdapter1,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#parametros_inicio_cliente").bind('change', function (event) {
		if (!event.args)
		{
			$("#parametros_inicio_cliente").jqxComboBox('clearSelection');
			$("#parametros_inicio_cliente_ID").jqxComboBox('clearSelection');
		}
	});
	$("#parametros_inicio_cliente").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#parametros_inicio_cliente_ID").val(event.args.item.value);
				clearTimeout(Timer1);
			},250);
		}
	});
	
	$("#parametros_inicio_cliente_ID").jqxComboBox(
	{
		theme: mytheme,
		width: 150,
		height: 20,
		source: ClienteDataAdapter2,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#parametros_inicio_cliente_ID").bind('change', function (event) {
		if (!event.args)
		{
			$("#parametros_inicio_cliente").jqxComboBox('clearSelection');
			$("#parametros_inicio_cliente_ID").jqxComboBox('clearSelection');
		}
	});
	$("#parametros_inicio_cliente_ID").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				$("#parametros_inicio_cliente").val(event.args.item.value);
				clearTimeout(Timer1);
			},250);
		}
	});
	
	function Add_Row1()
	{
		var Usuario = $("#parametros_inicio_cliente").jqxComboBox('getSelectedItem');
		if (!Usuario)
		{
			Alerts_Box("Favor Seleccionar un Usuario!", 3);
			WaitClick_Combobox("parametros_inicio_cliente");
			return;
		}
		
		var datinfo = $("#parametros_inicio_items_grid1").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#parametros_inicio_items_grid1").jqxGrid('getrowdata', i);
			if (currentRow.UserID == Usuario.value)
			{
				var id = $("#parametros_inicio_items_grid1").jqxGrid('getrowid', i);
				$("#parametros_inicio_items_grid1").jqxGrid('deleterow', id);
			}
		}
		
		var datarow = [{
			"UserName":Usuario.label,
			"UserID":Usuario.value,
			"UserPW":"1234",
			"UserCode":GetRandomWord(5),
			"UserLvl":"General"
		}];
		
		$("#parametros_inicio_items_grid1").jqxGrid("addrow", null, datarow, "first");
		$("#parametros_inicio_cliente").jqxComboBox('clearSelection');
		$("#parametros_inicio_cliente_ID").jqxComboBox('clearSelection');
	};
	
	var GridSource1 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'UserName', type: 'string' },
			{ name: 'UserID', type: 'string' },
			{ name: 'UserPW', type: 'string' },
			{ name: 'UserCode', type: 'string' },
			{ name: 'UserLvl', type: 'string' },
			{ name: 'UserActive', type: 'bool' },
		],
		data: {"Usuarios":true},
		url: "modulos/access.php",
		cache: false,
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["UserID"] < 0 || rowdata[0]["UserID"] != "")
			{
				var data = "Agregar_Usuarios=true&UserID=" + rowdata[0]["UserID"] + "&UserPW=" + rowdata[0]["UserPW"];
				data = data + "&UserCode=" + rowdata[0]["UserCode"] + "&UserLvl=" + rowdata[0]["UserLvl"];
				//alert(JSON.stringify(rowdata));
				$.ajax({
					dataType: 'text',
					url: "modulos/access.php",
					data: data,
					async: true,
					success: function (data, status, xhr)
					{
						commit(true);
						$("#parametros_inicio_items_grid1").jqxGrid("updatebounddata");
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
			var data = "Guardar_Usuarios=true&UserID=" + rowdata.UserID + "&UserPW=" + rowdata.UserPW;
			data = data + "&UserCode=" + rowdata.UserCode + "&UserLvl=" + rowdata.UserLvl + "&UserActive=" + rowdata.UserActive;
			$.ajax({
				dataType: 'text',
				url: "modulos/access.php",
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
		},
		deleterow: function (rowid, commit)
		{
			var UserID = $("#parametros_inicio_items_grid1").jqxGrid('getcellvalue', rowid, "UserID");
			
			if (UserID < 0 || UserID != "")
			{
				var data = "Borrar_Usuarios=true&UserID=" + UserID;
				$.ajax({
					dataType: 'text',
					url: "modulos/access.php",
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
			else
			{
				commit(false);
			}
		},
	};
	var GirdDataAdapter1 = new $.jqx.dataAdapter(GridSource1);
	
	var AccessLvl = [
		{"Nombre":"General"},
		{"Nombre":"Cliente"},
		{"Nombre":"Vendedor"},
		{"Nombre":"Administrador"},
	];
	
	$("#parametros_inicio_items_grid1").jqxGrid(
	{
		theme: mytheme,
		width: 530,
		source: GirdDataAdapter1,
		//enabletooltips: true, <-- Password Conflict
		sortable: true,
		pageable: true,
		pagesizeoptions: ['5', '10', '20', '30', '50'],
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
				'<input type="button" id="parametros_inicio_addrowbutton1" class="GridButtons" value="A&ntilde;adir a la Lista"/>'+
				'<input type="button" id="parametros_inicio_deleterowbutton1" class="GridButtons" value="Borrar Seleccionado"/>'+
				'<input type="button" id="parametros_inicio_imprimir" class="GridButtons" value="Imprimir Seleccionado"/>'
			);
			$("#parametros_inicio_addrowbutton1").jqxButton({theme: mytheme, template: "success"});
			$("#parametros_inicio_addrowbutton1").on('click', function () {
				Add_Row1();
			});
			
			$("#parametros_inicio_deleterowbutton1").jqxButton({theme: mytheme, template: "danger"});
			$("#parametros_inicio_deleterowbutton1").on('click', function () {
				var selectedrowindex = $("#parametros_inicio_items_grid1").jqxGrid('getselectedrowindex');
				var rowscount = $("#parametros_inicio_items_grid1").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#parametros_inicio_items_grid1").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#parametros_inicio_items_grid1").jqxGrid('deleterow', id);
				}
			});
			
			$("#parametros_inicio_imprimir").jqxButton({theme: mytheme, template: "warning"});
			$("#parametros_inicio_imprimir").on('click', function ()
			{
				var RowIndex = $("#parametros_inicio_items_grid1").jqxGrid('getselectedrowindex');
				if (RowIndex < 0)
				{
					Alerts_Box("Debe Seleccionar un Usuario.", 3);
					return;
				}
				
				var data = $("#parametros_inicio_items_grid1").jqxGrid("getrowdata", RowIndex);
				if (data.UserLvl == "Administrador")
				{
					Alerts_Box("Usuario Administrador, no necesita permisos.", 4);
					return;
				}
				
				window.open("imprimir/permisos.php?UserID="+data.UserID+"", "", "width=730, height=600, menubar=no, titlebar=no");
			});
		},
		columns:
		[
			{ text: 'Nombre', datafield: 'UserName', editable: false, width: '28%', height: 20 },
			{ text: 'ID', datafield: 'UserID', editable: false, width: '18%', height: 20 },
			//{ text: 'Contrase&ntilde;a', datafield: 'UserPW', width: '15%', height: 20 },
			{
				text: 'Contrase&ntilde;a', columntype: 'custom', datafield: 'UserPW', width: '15%', filterable: false,
				createeditor: function (row, cellValue, editor, cellText, width, height)
				{
					var element = $('<input type="password" style="width: 100%; height: 100%;"/>');
					editor.append(element);
					element.jqxPasswordInput();
				},
				initeditor: function (row, cellValue, editor, cellText, width, height)
				{
					var element = editor.find('input:first');
					element.jqxPasswordInput('val', cellValue);
				},
				geteditorvalue: function (row, cellvalue, editor)
				{
					var element = editor.find('input:first');
					return element.val();
				},
				cellsrenderer: function (row, columnfield, value, defaulthtml, columnproperties)
				{
					var hiddenValue = "<div style=\"margin:8px 0px 0px 5px;\">****</div>";
					/*for (var i = 0; i < value.length; i++) {
						hiddenValue += "*";
					};*/
					return hiddenValue;
				}
			},
			{ text: 'Codigo', datafield: 'UserCode', width: '13%', height: 20 },
			{
				text: 'Acceso', datafield: 'UserLvl', columntype: 'dropdownlist', width: '18%', height: 20,
				createeditor: function (row, column, editor) {
					editor.jqxDropDownList({
						source: AccessLvl,
						dropDownHeight: 125,
						selectedIndex: -1,
						displayMember: 'Nombre',
						valueMember: 'Nombre',
					});
				},
				cellvaluechanging: function (row, column, columntype, oldvalue, newvalue) {
					if (newvalue == "") return oldvalue;
				}
			},
			{ text: 'Activo', datafield: 'UserActive', columntype: 'checkbox', width: '8%', filtertype: 'bool'},
		]
	});
	$("#parametros_inicio_items_grid1").on("bindingcomplete", function (event) {
		$("#parametros_inicio_items_grid1").jqxGrid('localizestrings', localizationobj);
	});
	
	$("#parametros_inicio_items_grid1").on('rowselect', function (event)
	{
		var args = event.args;
		var datafield = event.args.datafield;
		var rowBoundIndex = args.rowindex;
		var value = args.value;
		var oldvalue = args.oldvalue;
		
		var UserID = $("#parametros_inicio_items_grid1").jqxGrid("getcellvalue", rowBoundIndex, "UserID");
		var UserLvl = $("#parametros_inicio_items_grid1").jqxGrid("getcellvalue", rowBoundIndex, "UserLvl");
		if (UserLvl == "Administrador")
		{
			//Alerts_Box("Usuario Administrador.", 1);
			$("#parametros_inicio_items_grid2").jqxGrid("clear");
		}
		else
		{
			//Alerts_Box("Usuario General.", 1);
			GridSource2.data = {"Accesos": UserID};
			GridSourceDataAdapter = new $.jqx.dataAdapter(GridSource2);
			$("#parametros_inicio_items_grid2").jqxGrid({source: GridSourceDataAdapter});
		}
	});
	
	//-----------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------
	
	var ModuleSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Module', type: 'string'},
		],
		data: {"Modules":true},
		url: "modulos/access.php",
		async: true
	};
	var ModuleDataAdapter = new $.jqx.dataAdapter(ModuleSource);
	
	$("#parametros_inicio_modulo").jqxComboBox(
	{
		theme: mytheme,
		width: 212,
		height: 20,
		source: ModuleDataAdapter,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Module',
		valueMember: 'Module'
	});
	$("#parametros_inicio_modulo").bind('change', function (event) {
		if (!event.args)
		{
			$("#parametros_inicio_modulo").jqxComboBox('clearSelection');
			$("#parametros_inicio_submodulo").jqxComboBox('clearSelection');
		}
	});
	$("#parametros_inicio_modulo").bind('select', function (event) {
		if (event.args) {
			clearTimeout(Timer1);
			Timer1 = setTimeout(function(){
				SubModuleSource.data = {"SubModule": event.args.item.value};
				SubModuleDataAdapter = new $.jqx.dataAdapter(SubModuleSource);
				$("#parametros_inicio_submodulo").jqxComboBox({source: SubModuleDataAdapter});
				
				clearTimeout(Timer1);
			},250);
		}
	});
	
	var SubModuleSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'SubModule', type: 'string'},
		],
		url: "modulos/access.php",
		async: true
	};
	
	$("#parametros_inicio_submodulo").jqxComboBox(
	{
		theme: mytheme,
		width: 212,
		height: 20,
		searchMode: 'startswithignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'SubModule',
		valueMember: 'SubModule'
	});
	$("#parametros_inicio_submodulo").bind('change', function (event) {
		if (!event.args)
		{
			$("#parametros_inicio_modulo").jqxComboBox('clearSelection');
			$("#parametros_inicio_submodulo").jqxComboBox('clearSelection');
		}
	});
	
	function Add_Row2()
	{
		var Tipo = $("#parametros_inicio_access_type").val();
		var Modulo = $("#parametros_inicio_modulo").val();
		var SubModulo = $("#parametros_inicio_submodulo").val();
		
		if ( Tipo < 0 || Tipo == "")
		{
			if ( Modulo < 0 || Modulo == "") {
				Alerts_Box("Favor Seleccionar un Modulo!", 3);
				WaitClick_Combobox("parametros_inicio_modulo");
				return;
			}
			
			if ( SubModulo < 0 || SubModulo == "") {
				Alerts_Box("Favor Seleccionar un SubModulo!", 3);
				WaitClick_Combobox("parametros_inicio_submodulo");
				return;
			}
			
			var datinfo = $("#parametros_inicio_items_grid2").jqxGrid('getdatainformation');
			var count = datinfo.rowscount;
			for (var i = 0; i < count; i++)
			{
				var currentRow = $("#parametros_inicio_items_grid2").jqxGrid('getrowdata', i);
				if (currentRow.Modulo == Modulo && currentRow.SubModulo == SubModulo)
				{
					var id = $("#parametros_inicio_items_grid2").jqxGrid('getrowid', i);
					$("#parametros_inicio_items_grid2").jqxGrid('deleterow', id);
				}
			}
			
			var datarow = [{
				"Modulo":Modulo,
				"SubModulo":SubModulo,
				"Guardar":false,
				"Modificar":false,
				"Supervisor":false,
				"Imprimir":false,
			}];
			
			$("#parametros_inicio_items_grid2").jqxGrid("addrow", null, datarow, "first");
		}
		else
		{
			$.ajax(
			{
				dataType: 'json',
				url: "modulos/access.php",
				data: {"AccessType":Tipo},
				async: true,
				success: function (data, status, xhr)
				{
					var len = data.length;
					for (var i = 0; i < len; i++)
					{
						if (data[i]["Modulo"] == "" || data[i]["Modulo"] == undefined)
							continue;
						
						var datarow = [{
							"Modulo":data[i]["Modulo"],
							"SubModulo":data[i]["SubModulo"],
							"Guardar":data[i]["Guardar"],
							"Modificar":data[i]["Modificar"],
							"Supervisor":data[i]["Supervisor"],
							"Imprimir":data[i]["Imprimir"],
						}];
						
						var datainfo = $("#parametros_inicio_items_grid2").jqxGrid("getdatainformation");
						var count = datainfo.rowscount;
						for (var a = 0; a < count; a++)
						{
							var currentRow = $("#parametros_inicio_items_grid2").jqxGrid("getrowdata", a);
							
							if (currentRow.Modulo == data[i]["Modulo"] && currentRow.SubModulo == data[i]["SubModulo"])
							{
								var id = $("#parametros_inicio_items_grid2").jqxGrid("getrowid", a);
								$("#parametros_inicio_items_grid2").jqxGrid("deleterow", id);
								$("#parametros_inicio_items_grid2").jqxGrid("addrow", null, datarow, "first");
							}
						}
						
						$("#parametros_inicio_items_grid2").jqxGrid("addrow", null, datarow, "first");
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					alert(textStatus+ " - " +errorThrown);
				}
			});
		}
		$("#parametros_inicio_modulo").jqxComboBox('clearSelection');
		$("#parametros_inicio_submodulo").jqxComboBox('clearSelection');
		$("#parametros_inicio_access_type").jqxComboBox('clearSelection');
	};
	
	var GridSource2 =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Modulo', type: 'string' },
			{ name: 'SubModulo', type: 'string' },
			{ name: 'Guardar', type: 'bool' },
			{ name: 'Modificar', type: 'bool' },
			{ name: 'Supervisor', type: 'bool' },
			{ name: 'Imprimir', type: 'bool' },
		],
		url: "modulos/access.php",
		addrow: function (rowid, rowdata, position, commit)
		{
			var rowindex = $("#parametros_inicio_items_grid1").jqxGrid('getselectedrowindex');
			var value = $("#parametros_inicio_items_grid1").jqxGrid('getcellvalue', rowindex, "UserID");
			
			if (value < 0 || value != "")
			{
				var data = "Agregar_Accesos=true&UserID=" + value + "&Modulo=" + rowdata[0]["Modulo"] + "&SubModulo=" + rowdata[0]["SubModulo"];
				data = data + "&Guardar=" + rowdata[0]["Guardar"] + "&Modificar=" + rowdata[0]["Modificar"];
				data = data + "&Supervisor=" + rowdata[0]["Supervisor"] + "&Imprimir=" + rowdata[0]["Imprimir"];
				//alert(JSON.stringify(rowdata));
				$.ajax({
					dataType: 'text',
					url: "modulos/access.php",
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
			else
			{
				commit(false);
			}
		},
		deleterow: function (rowid, commit)
		{
			var rowindex = $("#parametros_inicio_items_grid1").jqxGrid('getselectedrowindex');
			var value = $("#parametros_inicio_items_grid1").jqxGrid('getcellvalue', rowindex, "UserID");
			
			var Modulo = $("#parametros_inicio_items_grid2").jqxGrid('getcellvalue', rowid, "Modulo");
			var SubModulo = $("#parametros_inicio_items_grid2").jqxGrid('getcellvalue', rowid, "SubModulo");
			var Guardar = $("#parametros_inicio_items_grid2").jqxGrid('getcellvalue', rowid, "Guardar");
			var Modificar = $("#parametros_inicio_items_grid2").jqxGrid('getcellvalue', rowid, "Modificar");
			var Supervisor = $("#parametros_inicio_items_grid2").jqxGrid('getcellvalue', rowid, "Supervisor");
			var Imprimir = $("#parametros_inicio_items_grid2").jqxGrid('getcellvalue', rowid, "Imprimir");
			
			if (value != "")
			{
				var data = "Borrar_Accesos=true&UserID=" + value + "&Modulo=" + Modulo + "&SubModulo=" + SubModulo;
				data = data + "&Guardar=" + Guardar + "&Modificar=" + Modificar;
				data = data + "&Supervisor=" + Supervisor + "&Imprimir=" + Imprimir;
				$.ajax({
					dataType: 'text',
					url: "modulos/access.php",
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
			else
			{
				commit(false);
			}
		},
		updaterow: function (rowid, rowdata, commit)
		{
			var rowindex = $("#parametros_inicio_items_grid1").jqxGrid('getselectedrowindex');
			var value = $("#parametros_inicio_items_grid1").jqxGrid('getcellvalue', rowindex, "UserID");
			
			if (value != "")
			{
				var data = "Guardar_Accesos=true&UserID=" + value + "&Modulo=" + rowdata.Modulo + "&SubModulo=" + rowdata.SubModulo;
				data = data + "&Guardar=" + rowdata.Guardar + "&Modificar=" + rowdata.Modificar;
				data = data + "&Supervisor=" + rowdata.Supervisor + "&Imprimir=" + rowdata.Imprimir;
				$.ajax({
					dataType: 'text',
					url: "modulos/access.php",
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
			else
			{
				commit(false);
			}
		},
	};
	
	var AccessTypeSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: 'Type', type: 'string' },
		],
		url: "modulos/access.php",
		data: {"Par_Access_Type":true},
		async: true,
	};
	var AccessTypeDataAdapter = new $.jqx.dataAdapter(AccessTypeSource);
	
	$("#parametros_inicio_items_grid2").jqxGrid(
	{
		theme: mytheme,
		width: 450,
		sortable: true,
		pageable: true,
		pagesizeoptions: ['5', '10', '20', '30', '50'],
		autoheight: true,
		editable: true,
		showfilterrow: true,
		filterable: true,
		showtoolbar: true,
		rendertoolbar: function (toolbar) {
			var me = this;
			var container = $("<div style='margin: 5px;'></div>");
			toolbar.append(container);
			container.append(
				'<input type="button" id="parametros_inicio_addrowbutton2" class="GridButtons" value="A&ntilde;adir"/>'+
				'<input type="button" id="parametros_inicio_deleterowbutton2" class="GridButtons" value="Borrar Selec."/>'+
				'<input type="button" id="parametros_inicio_deleteallrowbutton" class="GridButtons" value="Borrar Todo"/>'+
				'<div id="parametros_inicio_access_type" style="margin: 0px 5px 0px 0px; float: right;" ></div>'
			);
			$("#parametros_inicio_addrowbutton2").jqxButton({theme: mytheme, template: "success"});
			$("#parametros_inicio_deleterowbutton2").jqxButton({theme: mytheme, template: "danger"});
			$("#parametros_inicio_deleteallrowbutton").jqxButton({theme: mytheme, template: "inverse"});
			// create new row.
			$("#parametros_inicio_addrowbutton2").on('click', function () {
				var rowindex = $("#parametros_inicio_items_grid1").jqxGrid('getselectedrowindex');
				var value = $("#parametros_inicio_items_grid1").jqxGrid('getcellvalue', rowindex, "UserLvl");
				if (rowindex < 0)
					Alerts_Box("Primero Debe Seleccionar un Usuario!", 4);
				else if (value == "Administrador")
					Alerts_Box("Los Administradores no necesitan Permisos.", 3);
				else
					Add_Row2();
			});
			// delete row.
			$("#parametros_inicio_deleterowbutton2").on('click', function () {
				var selectedrowindex = $("#parametros_inicio_items_grid2").jqxGrid('getselectedrowindex');
				var rowscount = $("#parametros_inicio_items_grid2").jqxGrid('getdatainformation').rowscount;
				if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
					var id = $("#parametros_inicio_items_grid2").jqxGrid('getrowid', selectedrowindex);
					var commit = $("#parametros_inicio_items_grid2").jqxGrid('deleterow', id);
				}
			});
			// delete all rows
			$("#parametros_inicio_deleteallrowbutton").on('click', function () {
				Alerts_Box("Este Proceso dejara al Usuario seleccionado sin Acceso al Sistema.<br />Desea Continuar?", 4, true);
				var CheckTimer = setInterval(function() {
					if (ClickOK == true) {
						clearInterval(CheckTimer);
						ClickOK = false;
						var rowindex = $("#parametros_inicio_items_grid1").jqxGrid('getselectedrowindex');
						var User = $("#parametros_inicio_items_grid1").jqxGrid('getcellvalue', rowindex, "UserID");
						$.ajax({
							dataType: 'text',
							url: "modulos/access.php",
							data: {
								"Borrar_Accesos_Todo":true,
								"UserID":User
							},
							async: true,
							success: function (data, status, xhr) {
								$("#parametros_inicio_items_grid2").jqxGrid('clear');
							},
							error: function (jqXHR, textStatus, errorThrown) {
								alert(textStatus+ " - " +errorThrown);
							}
						});
					}
					if (ClickCANCEL == true) {
						clearInterval(CheckTimer);
						ClickCANCEL = false;
						commit(false);
					}
				}, 10);
			});
			// combobox
			$("#parametros_inicio_access_type").jqxComboBox({
				theme: mytheme,
				width: 165,
				height: 20,
				source: AccessTypeDataAdapter,
				searchMode: 'startswithignorecase',
				autoComplete: true,
				promptText: 'Seleccionar Perfil',
				selectedIndex: -1,
				displayMember: 'Type',
				valueMember: 'Type'
			});
		},
		columns:
		[
			{ text: 'Modulo', datafield: 'Modulo', editable: false, width: '20%', height: 20 },
			{ text: 'SubModulo', datafield: 'SubModulo', editable: false, width: '20%', height: 20 },
			{
				text: 'Guardar',
				datafield: 'Guardar',
				columntype: 'checkbox',
				width: "14%",
				filtertype: 'bool'
			},
			{
				text: 'Modificar',
				datafield: 'Modificar',
				columntype: 'checkbox',
				width: "15%",
				filtertype: 'bool'
			},
			{
				text: 'Supervisor',
				datafield: 'Supervisor',
				columntype: 'checkbox',
				width: "16%",
				filtertype: 'bool'
			},
			{
				text: 'Imprimir',
				datafield: 'Imprimir',
				columntype: 'checkbox',
				width: "15%",
				filtertype: 'bool'
			},
		]
	});
	$("#parametros_inicio_items_grid2").jqxGrid('localizestrings', localizationobj);
	
});
</script>
<!-- PART 1 -->
<div id="Parte2">
	<div id="parametros_inicio_left1" style="float: left; margin-left:0px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
		Usuarios del Sistema</p>
		<table cellpadding="0" cellspacing="0" style="margin:10px 0px 10px 0px; border: 1px solid #A4BED4; text-align: center;">
			<tr style="background: #E0E9F5">
				<td style="border-bottom: 1px solid #A4BED4;">
					Nombre
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					ID
				</td>
			</tr>
			<tr>
				<td>
					<div id="parametros_inicio_cliente" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="parametros_inicio_cliente_ID" style="margin:0px 7px 0px 7px;"></div>
				</td>
			</tr>
		</table>
		<div id="parametros_inicio_items_grid1"></div>
	</div>
	<div id="parametros_inicio_right1" style="float: left; margin-left:10px;">
		<p style="font-size: 14px; background-color: cornflowerblue; color: #FFF; height: 25px; padding-top: 5px; margin: 5px 0px; text-align: center;">
		Permisos de los Usuarios</p>
		<table cellpadding="0" cellspacing="0" style="margin:10px 0px 10px 0px; border: 1px solid #A4BED4; text-align: center;">
			<tr style="background: #E0E9F5">
				<td style="border-bottom: 1px solid #A4BED4;">
					Modulo
				</td>
				<td style="border-bottom: 1px solid #A4BED4;">
					SubModulo
				</td>
			</tr>
			<tr>
				<td>
					<div id="parametros_inicio_modulo" style="margin-left:7px;"></div>
				</td>
				<td>
					<div id="parametros_inicio_submodulo" style="margin:0px 7px 0px 7px;"></div>
				</td>
			</tr>
		</table>
		<div id="parametros_inicio_items_grid2"></div>
	</div>
</div>