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
	var Body = document.getElementById("Figuracion_Figuras_Content");
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
				SystemMap("Figuras", true);
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
	var Guardar = false;
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
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Figuras" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Figuras" && $data[$i]["Modificar"] == "true")
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
	
	$("#figuracion_figuras_upload").jqxFileUpload({
		theme: mytheme,
		width: 250,
		multipleFilesUpload: false,
		browseTemplate: 'success',
		uploadTemplate: 'primary',
		cancelTemplate: 'danger',
		uploadUrl: 'modulos/guardar.php',
		fileInputName: 'Image_Uploads',
	});
	$("#figuracion_figuras_uploadBrowseButton").jqxButton({ width: '120px'});
	$("#figuracion_figuras_upload").jqxFileUpload({ localization: localizationobj });
	$("#figuracion_figuras_upload").jqxFileUpload({ localization: { browseButton: 'Subir Figura' } });
	$("#figuracion_figuras_upload").on('uploadEnd', function (event)
	{
		var args = event.args;
		var fileName = args.file;
		var serverResponce = args.response;
		
		if (serverResponce == "OK")
		{
			$("#figuracion_figuras_file").val(fileName);
		}
		else
		{
			Alerts_Box(""+serverResponce, 3);
		}
	});
	
	$("#figuracion_figuras_file").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
		disabled: true
	});
	
	$("#figuracion_figuras_nombre").jqxInput({
		theme: mytheme,
		width: 120,
		height: 20,
	});
	
	$("#figuracion_figuras_add").jqxButton({
		theme: mytheme,
		width: 70,
		height: 25,
		template: "success"
	});
	$("#figuracion_figuras_add").on("click", function()
	{
		if (!Admin && !Guardar)
			return;
		
		if (Locked == true)
			return;
		
		Locked = true;
		
		var File = $("#figuracion_figuras_file").val();
		var Name = $("#figuracion_figuras_nombre").val();
		
		if (File == "")
		{
			Alerts_Box("Favor Cargar una Imagen.", 3);
			return;
		}
		
		if (Name == "")
		{
			Alerts_Box("Favor Escribir un Nombre.", 3);
			WaitClick_Combobox("figuracion_figuras_nombre");
			return;
		}
		
		var datainfo = $("#figuracion_figuras_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var TmpArray = [{
			"ID":0,
			"Imagen":File,
			"Figura":Name,
			"Dimensiones":0,
			"Estribo":false,
			"SemiCirculo":false,
			"Circular":false,
			"Vueltas":false,
		}];
		
		$("#figuracion_figuras_items_grid").jqxGrid("addrow", count, TmpArray);
		$("#figuracion_figuras_file").val("");
		$("#figuracion_figuras_nombre").val("");
	});
	
	var GridSource =
	{
		datatype: "json",
		datafields:
		[
			{ name: "ID", type: "string" },
			{ name: "Imagen", type: "string" },
			{ name: "Figura", type: "string" },
			{ name: "Dimensiones", type: "decimal" },
			{ name: "Estribo", type: "bool" },
			{ name: "SemiCirculo", type: "bool" },
			{ name: "Circular", type: "bool" },
			{ name: "Vueltas", type: "bool" },
		],
		cache: false,
		url: "modulos/datos.php",
		data: {"Figuracion_Figuras":true},
		addrow: function (rowid, rowdata, position, commit)
		{
			if (rowdata[0]["Imagen"] != "")
			{
				$("#Loading_Mess").html("Procesando Solicitud...");
				$("#Loading").show();
				$.ajax({
					dataType: "json",
					type: "POST",
					url: "modulos/guardar.php",
					data: {
						"Figuracion_Figuras_Agregar":true,
						"Imagen":"Uploads_Tmp/"+rowdata[0]["Imagen"],
						"Figura":rowdata[0]["Figura"],
						"Dimensiones":rowdata[0]["Dimensiones"],
						"Estribo":rowdata[0]["Estribo"],
						"SemiCirculo":rowdata[0]["SemiCirculo"],
						"Circular":rowdata[0]["Circular"],
						"Vueltas":rowdata[0]["Vueltas"],
					},
					success: function (data, status, xhr)
					{
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						Locked = false;
						
						switch(data[0]["MESSAGE"])
						{
							case "ERROR":
								commit(false);
								Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
							break;
							
							default:
								commit(true);
								$("#figuracion_figuras_items_grid").jqxGrid("updatebounddata");
							break;
						}
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						commit(false);
						$("#Loading").hide();
						$("#Loading_Mess").html("Cargando...");
						Locked = false;
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
						+"Error: "+errorThrown, 3);
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
			var ID = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "ID");
			var Figura = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "Figura");
			var Dimensiones = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "Dimensiones");
			var Estribo = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "Estribo");
			var SemiCirculo = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "SemiCirculo");
			var Circular = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "Circular");
			var Vueltas = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "Vueltas");
			
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {
					"Figuracion_Figuras_Actualizar":true,
					"ID":ID,
					"Figura":Figura,
					"Dimensiones":Dimensiones,
					"Estribo":Estribo,
					"SemiCirculo":SemiCirculo,
					"Circular":Circular,
					"Vueltas":Vueltas,
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
					
					var ID = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "ID");
					var Imagen = $("#figuracion_figuras_items_grid").jqxGrid('getcellvalue', rowid, "Imagen");
					
					$.ajax({
						dataType: "json",
						type: "POST",
						url: "modulos/guardar.php",
						data: {
							"Figuracion_Figuras_Borrar":true,
							"ID":ID,
							"Imagen":Imagen,
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
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#figuracion_figuras_items_grid").jqxGrid(
	{
		theme: mytheme,
		width: 700,
		autoheight: true,
		//autorowheight: true,
		rowsheight: 100,
		source: GridDataAdapter,
		enabletooltips: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		pagesize: '20',
		pageable: true,
		sortable: true,
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
				width: "4%",
				filterable: false,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					if (!Admin && !Guardar)
						return;
					
					var selectedrowindex = $("#figuracion_figuras_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#figuracion_figuras_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#figuracion_figuras_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#figuracion_figuras_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'ID', width: "10%", editable: false, },
			{
				text: 'Imagen', 
				datafield: 'Imagen', 
				width: "30%", 
				filterable: false, 
				editable: false, 
				cellsrenderer: function (row, datafield, value) {
					return "<img style=\"margin-left: 5px;\" src=\"images/"+value+"\"/>";
				}, 
			},
			{ text: 'Nombre', datafield: 'Figura', width: "29%", editable: Admin ? true:Modificar, },
			{
				text: 'Puntos',
				datafield: 'Dimensiones',
				width: "7%",
				cellsformat: 'd',
				cellsalign: 'center',
				columntype: 'numberinput',
				editable: Admin ? true:Modificar,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 0, digits: 15, max: 999999999999999, spinButtons: false });
				}
			},
			{
				text: 'Estribo',
				datafield: 'Estribo',
				width: "7%",
				filtertype: 'bool',
				editable: Admin ? true:Modificar,
				columntype: 'checkbox',
			},
			{
				text: 'Semi-Cir',
				datafield: 'SemiCirculo',
				width: "8%",
				filtertype: 'bool',
				editable: Admin ? true:Modificar,
				columntype: 'checkbox',
			},
			{
				text: 'Circular',
				datafield: 'Circular',
				width: "8%",
				filtertype: 'bool',
				editable: Admin ? true:Modificar,
				columntype: 'checkbox',
			},
			{
				text: 'Vueltas',
				datafield: 'Vueltas',
				width: "7%",
				filtertype: 'bool',
				editable: Admin ? true:Modificar,
				columntype: 'checkbox',
			},
		]
	});
	$("#figuracion_figuras_items_grid").bind("bindingcomplete", function (event)
	{
		$("#figuracion_figuras_items_grid").jqxGrid("hidecolumn", "ID");
		$("#figuracion_figuras_items_grid").jqxGrid('localizestrings', localizationobj);
	});
	
	function SaveData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var FinalArray = new Array();
		var datainfo = $("#figuracion_figuras_items_grid").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_figuras_items_grid").jqxGrid("getrowdata", i);
			
			TmpArray["Imagen"] = "Uploads_Tmp/"+currentRow.Imagen;
			TmpArray["Nombre"] = currentRow.Nombre;
			TmpArray["Puntos"] = currentRow.Puntos;
			TmpArray["SemiCirculo"] = currentRow.SemiCirculo;
			TmpArray["Circular"] = currentRow.Circular;
			TmpArray["Vueltas"] = currentRow.Vueltas;
			
			FinalArray[i] = TmpArray;
		}
		
		/*alert(JSON.stringify(FinalArray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			type: "POST",
			url: "modulos/guardar.php",
			data: {"Figuracion_Figuras":FinalArray},
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
					break;
					
					default:
						$("#figuracion_figuras_guardar").jqxButton({disabled: true});
						Alerts_Box("Datos Guardados con Exito!", 2);
					break;
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	}
	
	if (!Admin && !Guardar)
	{
		$("#figuracion_figuras_add").jqxButton({ disabled: true });
	}
	
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="1" style="margin-bottom:15px;">
		<tr>
			<td rowspan="2" colspan="2">
				<div id="figuracion_figuras_upload"></div>
			</td>
			<td>
				Imagen
			</td>
			<td>
				<input type="text" id="figuracion_figuras_file"/>
			</td>
			<td>
				Nombre
			</td>
			<td>
				<input type="text" id="figuracion_figuras_nombre"/>
			</td>
			<td>
				<input type="button" id="figuracion_figuras_add" value="Añadir"/>
			</td>
		</tr>
		<tr>
			<td colspan="6">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="7">
				<div id="figuracion_figuras_items_grid"></div>
			</td>
		</tr>
	</table>
</div>