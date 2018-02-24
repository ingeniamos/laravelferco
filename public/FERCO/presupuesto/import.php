<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var ID_Incorrecto = false;
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Presupuesto_Content");
	var Body = document.getElementById("Presupuesto_IMPORT_Content");
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
				SystemMap("Importar", true);
				
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
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Importar" && $data[$i]["Guardar"] == "true")
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
	
	function Import()
	{
		if (Locked == true)
			return;
			
		Locked = true;
		
		if (ID_Incorrecto)
		{
			Alerts_Box("Ya existe una Base de Datos con el mismo Nombre.<br/>Favor Ingresar Otra para Continuar.", 3);
			WaitClick_Input("presupuesto_importar_clasificacion");
			Locked = false;
			return;
		}
		
		var Clasificacion = $("#presupuesto_importar_clasificacion").val();
		var File = $("#presupuesto_importar_archivo").val();
		
		if (Clasificacion == "")
		{
			Alerts_Box("Favor Ingresar una Clasificacion.", 3);
			WaitClick_Input("presupuesto_importar_clasificacion");
			Locked = false;
			return;
		}
		
		if (File == "")
		{
			Alerts_Box("Favor Cargar un Archivo.", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "text",
			url: "modulos/guardar.php",
			data: {
				"Presupuesto_Importar":true,
				"Clasificacion":Clasificacion,
				"File":File,
			},
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				alert(data)
				switch (data)
				{
					case "OK":
						Alerts_Box("Datos Guardados con Exito!", 2);
						//Clean
						$("#presupuesto_importar_clasificacion").val("");
						$("#presupuesto_importar_archivo").val("");
					break;
					
					case "EXTRACT":
						Alerts_Box("Ocurrió un error en la extraccion de los archivos.<br />Intente luego de unos segundos.", 3);
					break;
					
					default:
						Alerts_Box("Ocurrió un grave error mientras se guardaban los datos.<br />Contacte al Soporte Técnico.", 3);
					break;
				}
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
	
	$("#presupuesto_importar_clasificacion").jqxInput({
		theme: mytheme,
		width: 150,
		height: 20,
	});
	$("#presupuesto_importar_clasificacion").on("change", function (event)
	{
		var value = $("#presupuesto_importar_clasificacion").val();
		
		$.ajax({
			dataType: "text",
			url: "modulos/datos_productos.php",
			data: {
				"Presupuesto_Check_Clasificacion":true,
				"Clasificacion":value,
			},
			success: function (data)
			{
				if (data == "")
				{
					$("#presupuesto_importar_clasificacion").removeClass("jqx-validator-error-element");
					ID_Incorrecto = false;
				}
				else
				{
					$("#presupuesto_importar_clasificacion").addClass("jqx-validator-error-element");
					ID_Incorrecto = true;
					Alerts_Box("Ya existe una Base de Datos con el mismo Nombre.<br/>Favor Ingresar Otra para Continuar.", 3);
					WaitClick_Input("presupuesto_importar_clasificacion");
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				Locked = false;
				Alerts_Box("Ocurrio un Error al comprobar el nombre de la base de datos.", 3);
			}
		});
	});
	
	$("#presupuesto_importar_upload").jqxFileUpload({
		theme: mytheme,
		width: 200,
		multipleFilesUpload: false,
		browseTemplate: 'success',
		uploadTemplate: 'primary',
		cancelTemplate: 'danger',
		uploadUrl: 'modulos/guardar.php',
		fileInputName: 'File_Uploads',
	});
	$("#presupuesto_importar_uploadBrowseButton").jqxButton({ width: '186px'});
	$("#presupuesto_importar_upload").jqxFileUpload({ localization: localizationobj });
	$("#presupuesto_importar_upload").jqxFileUpload({ localization: { browseButton: 'Subir Archivo RAR/ZIP' } });
	$("#presupuesto_importar_upload").on('uploadEnd', function (event)
	{
		var args = event.args;
		var fileName = args.file;
		var serverResponce = args.response;
		
		if (serverResponce == "OK")
		{
			$("#presupuesto_importar_archivo").val(fileName);
		}
		else
		{
			Alerts_Box(""+serverResponce, 3);
		}
		
	});
	
	$("#presupuesto_importar_archivo").jqxInput({
		theme: mytheme,
		width: 130,
		height: 20,
		disabled: true
	});
	
	$("#presupuesto_importar_guardar").jqxButton({
		width: 120,
		height: 25,
		template: "info"
	});
	$("#presupuesto_importar_guardar").on('click', function ()
	{
		Import();
	});
	
	$("#presupuesto_importar_descargar").jqxButton({
		width: 180,
		height: 25,
		template: "warning"
	});
	$("#presupuesto_importar_descargar").on('click', function ()
	{
		//
	});
	
	CheckRefresh();
});
</script>

<!-- PART 1 -->
<div id="Parte1">
	<p style="font-size: 12px;">
		<b>*</b> Para Importar, debe cargar el archivo <b>ZIP</b> o <b>RAR</b> que contenga la informacion total de los APU.<br />
		Estos archivos pueden estar tanto en formato <b>DBF</b> como en <b>XLSX</b>. (los archivos no deben estar dentro de carpetas)<br /><br />
		<b>NOTA:</b> el proceso de importacion puede tardar varios minutos dependiendo del tamaño de la base de datos.
	</p>
	<table cellpadding="2" cellspacing="1" style="margin-bottom:20px;">
		<tr>
			<td>
				Base de Datos
			</td>
			<td>
				<input type="text" id="presupuesto_importar_clasificacion"/>
			</td>
			<td>
				<div id="presupuesto_importar_upload"></div>
			</td>
			<td>
				Archivo Cargado
			</td>
			<td>
				<input type="text" id="presupuesto_importar_archivo" style="text-align: center;"/>
			</td>
			<td>
				<input type="button" id="presupuesto_importar_guardar" value="Importar">
			</td>
			<td>
				<input type="button" id="presupuesto_importar_descargar" value="Descargar Modelo Excel">
			</td>
		</tr>
	</table>
</div>
