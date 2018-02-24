<?php
header('Content-Type: text/html; charset=utf-8');
/*
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Connection: close");
*/
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
session_set_cookie_params(0);
session_start();
require_once('modulos/config.php');
date_default_timezone_set("America/Bogota");
$_SESSION["NumOfID"] = 0;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title id="Description"><?php echo $TITLE ?></title>
	<link rel="shortcut icon" href="favicon.ico"/>
	<link rel="stylesheet" href="jqwidgets-ver3.8.2/jqwidgets/styles/jqx.base.css" type="text/css" />
	<link rel="stylesheet" href="jqwidgets-ver3.8.2/jqwidgets/styles/jqx.energyblue.css" type="text/css" />
	<link rel="stylesheet" href="jqwidgets-ver3.8.2/jqwidgets/styles/jqx.darkblue.css" type="text/css" />
	<link rel="stylesheet" href="sty.css" type="text/css" />
	<script type="text/javascript" src="jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxcore.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxdata.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxbuttons.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxscrollbar.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxlistbox.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxcombobox.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxgrid.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxgrid.edit.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxgrid.selection.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxgrid.aggregates.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxgrid.sort.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxgrid.pager.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxgrid.filter.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxmenu.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxinput.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxmaskedinput.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxnumberinput.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxformattedinput.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxdatetimeinput.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxcalendar.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/globalization/globalize.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/globalization/globalize.culture.es-ES.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxcheckbox.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxdropdownlist.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxvalidator.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxwindow.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxfileupload.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxdraw.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxtooltip.js"></script>
	<script type="text/javascript" src="jqwidgets-ver3.8.2/jqwidgets/jqxpasswordinput.js"></script>
	<script type="text/javascript" src="jsPDF-master/dist/jspdf.min.js"></script>
	<!-- Main Script -->
	<script type="text/javascript">
	$(document).ready(function ()
	{
		$.jqx._jqxNumberInput.prototype.wheel = function () { return false; };
		$("#Loading").hide();
		
		var mytheme = "energyblue";
		
		// ------- GLOBAL VARIABLES
		var UserID = "<?php echo isset($_SESSION["UserID"]) ? $_SESSION["UserID"]:""; ?>";
		var UserCode = "<?php echo isset($_SESSION["UserCode"]) ? $_SESSION["UserCode"]:""; ?>";
		var UserPause = "<?php echo isset($_SESSION["UserPause"]) ? $_SESSION["UserPause"]:false; ?>";
		if (UserPause == "false")
			UserPause = false;
		else if (UserPause == "true")
			UserPause = true;
		
		var LastActive1 = "";
		var LastActive2 = "";
		var InicioLoaded = false;
		var TercerosLoaded = false;
		var VentasLoaded = false;
		var ComprasLoaded = false;
		var InventarioLoaded = false;
		var CarteraLoaded = false;
		var CxpLoaded = false;
		var CajaLoaded = false;
		var NominaLoaded = false;
		var ProduccionLoaded = false;
		var FiguracionLoaded = false;
		var MaquinariaLoaded = false;
		var PresupuestoLoaded = false;
		var RequerimientosLoaded = false;
		var ReportesLoaded = false;
		var ParametrosLoaded = false;
		
		//--- Permisos
		var Terceros_Access = false;
		var Ventas_Access = false;
		var Compras_Access = false;
		var Inventario_Access = false;
		var Cartera_Access = false;
		var CxP_Access = false;
		var Caja_Access = false;
		var Agenda_Access = false;
		var Nomina_Access = false;
		var Produccion_Access = false;
		var Figuracion_Access = false;
		var Maquinaria_Access = false;
		var Presupuesto_Access = false;
		var Requerimientos_Access = false;
		var Reportes_Access = false;
		var Parametros_Access = false;
		
		var menu_hidden = false;
		
		<?php if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "") { ?>
			var IDLE_TIMEOUT = 900; //seconds (15 mints)
			var CHECK_CHANGES_TIMEOUT = 10; //seconds
			var TimeCounter = 0;
			
			document.onclick = function() {
				TimeCounter = 0;
			};
			document.onmousemove = function() {
				TimeCounter = 0;
			};
			document.onkeypress = function() {
				TimeCounter = 0;
			};
			
			window.setInterval(CheckIdleTime, 1000);
			
			$.ajax({
				dataType: "text",
				url: "modulos/access.php",
				data: {"GetSystemStatus":true},
				success: function (data, status, xhr)
				{
					if (data == "OK")
					{
						window.setInterval(CheckChanges, (CHECK_CHANGES_TIMEOUT * 1000));
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					Alerts_Box("Ocurrió un Error al Intentar Verificar el Estado del Sistema.", 3);
					//alert(textStatus+ " - " +errorThrown);
				}
			});
			
			function CheckIdleTime()
			{
				if (UserPause == true)
					return;
				
				TimeCounter++;
				if (TimeCounter >= IDLE_TIMEOUT)
				{
					Alerts_Box("Se ha detectado una larga inactividad.<br />"+
					"Click en Aceptar para Continuar la sesion o<br />"+
					"Click en Cancelar para Cerrar la sesion.", 4, true);
					var CheckTimer1 = setInterval(function()
					{
						if (ClickOK == true) {
							clearInterval(CheckTimer1);
							clearTimeout(CheckTimer2);
							ClickOK = false;
							//code
							TimeCounter = 0;
						}
						if (ClickCANCEL == true) {
							clearInterval(CheckTimer1);
							clearTimeout(CheckTimer2);
							ClickCANCEL = false;
							//code
							document.location.href = "modulos/logout.php";
						}
					}, 10);
					var CheckTimer2 = setTimeout(function(){
						$("#Ask_Alert").jqxWindow("close");
						clearInterval(CheckTimer1);
						document.location.href = "modulos/logout.php";
					},15000);
				}
			}
			
			function CheckChanges()
			{
				$.ajax({
					dataType: "text",
					url: "modulos/access.php",
					data: {"CheckChanges":true},
					async: true,
					success: function (data, status, xhr)
					{
						if (data == "ACCESS")
						{
							document.title = "Nuevo Mensaje!!";
							Alerts_Box("Estimado usuario, se han detectado cambios en sus permisos al sistema.<br /><br />"+
							"La pagina se recargara para que estos cambios surgan efecto.", 4);
							var CheckTime1 = setInterval(function(){
								if (ClickOK == true)
								{
									ClickOK = false;
									clearInterval(CheckTime1);
									clearTimeout(CheckTime2);
									location.reload();
								}
							},10);
							var CheckTime2 = setTimeout(function(){
								$("#Normal_Alert").jqxWindow("close");
								clearInterval(CheckTime1);
								ClickOK = false;
								location.reload();
							},10000);
						}
						else if (data == "CLOSED")
						{
							document.title = "Nuevo Mensaje!!";
							Alerts_Box("Estimado usuario, el Sistema ha sido cerrado por un Administrador.<br /><br />"+
							"La pagina se recargara para que estos cambios surgan efecto.", 4);
							var CheckTime1 = setInterval(function(){
								if (ClickOK == true)
								{
									ClickOK = false;
									clearInterval(CheckTime1);
									clearTimeout(CheckTime2);
									location.reload();
								}
							},10);
							var CheckTime2 = setTimeout(function(){
								$("#Normal_Alert").jqxWindow("close");
								clearInterval(CheckTime1);
								ClickOK = false;
								location.reload();
							},10000);
						}
						else if (data == "LOGIN")
						{
							document.title = "Nuevo Mensaje!!";
							Alerts_Box("Session Cerrada!<br /><br />"+
							"Se ha detectado un inicio de session desde otra ubicacion y "+
							"hemos cerrado su session por motivos de seguridad "+
							"La pagina se recargara para que estos cambios surgan efecto.<br /><br />"+
							"<b>NOTA:</b> Si Desconoce este Inicio de Session, Notifiquelo cuanto antes al Administrador.", 4);
							var CheckTime1 = setInterval(function(){
								if (ClickOK == true)
								{
									ClickOK = false;
									clearInterval(CheckTime1);
									clearTimeout(CheckTime2);
									location.reload();
								}
							},10);
							var CheckTime2 = setTimeout(function(){
								$("#Normal_Alert").jqxWindow("close");
								clearInterval(CheckTime1);
								ClickOK = false;
								location.reload();
							},10000);
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
					}
				});
			}
			
			//--- Disable from init
			<?php if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
				$("#terceros").addClass("disabled");
				$("#ventas").addClass("disabled");
				$("#compras").addClass("disabled");
				$("#inventario").addClass("disabled");
				$("#cartera").addClass("disabled");
				$("#CxP").addClass("disabled");
				$("#caja").addClass("disabled");
				$("#agenda").addClass("disabled");
				$("#nomina").addClass("disabled");
				$("#produccion").addClass("disabled");
				$("#figuracion").addClass("disabled");
				$("#maquinaria").addClass("disabled");
				$("#presupuesto").addClass("disabled");
				$("#requerimientos").addClass("disabled");
				$("#reportes").addClass("disabled");
				$("#parametros").addClass("disabled");
			<?php
			}
			else { ?>
				Terceros_Access = true;
				Ventas_Access = true;
				Compras_Access = true;
				Inventario_Access = true;
				Cartera_Access = true;
				CxP_Access = true;
				Caja_Access = true;
				Agenda_Access = true;
				Nomina_Access = true;
				Produccion_Access = true;
				Figuracion_Access = true;
				Maquinaria_Access = true;
				Presupuesto_Access = true;
				Requerimientos_Access = true;
				Reportes_Access = true;
				Parametros_Access = true;
			<?php
			} ?>
		<?php } ?>
		
		function MenuHide (hide)
		{
			if (hide)
			{
				var Timer = setTimeout(function(){
					clearInterval(Timer);
					ContentSize(hide);
				},200);
				
				$("#inicio").html("&nbsp;");
				$("#inicio").animate({
					width: "30px"
				}, "fast");
				$("#terceros").html("&nbsp;");
				$("#terceros").animate({
					width: "30px"
				}, "fast");
				$("#ventas").html("&nbsp;");
				$("#ventas").animate({
					width: "30px"
				}, "fast");
				$("#compras").html("&nbsp;");
				$("#compras").animate({
					width: "30px"
				}, "fast");
				$("#inventario").html("&nbsp;");
				$("#inventario").animate({
					width: "30px"
				}, "fast");
				$("#cartera").html("&nbsp;");
				$("#cartera").animate({
					width: "30px"
				}, "fast");
				$("#CxP").html("&nbsp;");
				$("#CxP").animate({
					width: "30px"
				}, "fast");
				$("#caja").html("&nbsp;");
				$("#caja").animate({
					width: "30px"
				}, "fast");
				$("#agenda").html("&nbsp;");
				$("#agenda").animate({
					width: "30px"
				}, "fast");
				$("#nomina").html("&nbsp;");
				$("#nomina").animate({
					width: "30px"
				}, "fast");
				$("#produccion").html("&nbsp;");
				$("#produccion").animate({
					width: "30px"
				}, "fast");
				$("#figuracion").html("&nbsp;");
				$("#figuracion").animate({
					width: "30px"
				}, "fast");
				$("#maquinaria").html("&nbsp;");
				$("#maquinaria").animate({
					width: "30px"
				}, "fast");
				$("#presupuesto").html("&nbsp;");
				$("#presupuesto").animate({
					width: "30px"
				}, "fast");
				$("#requerimientos").html("&nbsp;");
				$("#requerimientos").animate({
					width: "30px"
				}, "fast");
				$("#reportes").html("&nbsp;");
				$("#reportes").animate({
					width: "30px"
				}, "fast");
				$("#parametros").html("&nbsp;");
				$("#parametros").animate({
					width: "30px"
				}, "fast");
				$("#menu_hide").animate({
					width: "30px"
				}, "fast");
				$("#Menu ul").animate({
					width: "30px"
				}, "fast");
				$("#Menu").animate({
					width: "30px"
				}, "fast");
			}
			else
			{
				ContentSize(hide);
				
				$("#inicio").html("Inicio");
				$("#inicio").animate({
					width: "110px"
				}, "fast");
				$("#terceros").html("Terceros");
				$("#terceros").animate({
					width: "110px"
				}, "fast");
				$("#ventas").html("Ventas");
				$("#ventas").animate({
					width: "110px"
				}, "fast");
				$("#compras").html("Compras");
				$("#compras").animate({
					width: "110px"
				}, "fast");
				$("#inventario").html("Inventario");
				$("#inventario").animate({
					width: "110px"
				}, "fast");
				$("#cartera").html("Cartera");
				$("#cartera").animate({
					width: "110px"
				}, "fast");
				$("#CxP").html("CxP");
				$("#CxP").animate({
					width: "110px"
				}, "fast");
				$("#caja").html("Caja");
				$("#caja").animate({
					width: "110px"
				}, "fast");
				$("#agenda").html("Agenda");
				$("#agenda").animate({
					width: "110px"
				}, "fast");
				$("#nomina").html("Nomina");
				$("#nomina").animate({
					width: "110px"
				}, "fast");
				$("#produccion").html("Produccion");
				$("#produccion").animate({
					width: "110px"
				}, "fast");
				$("#figuracion").html("Figuracion");
				$("#figuracion").animate({
					width: "110px"
				}, "fast");
				$("#maquinaria").html("Maquinaria");
				$("#maquinaria").animate({
					width: "110px"
				}, "fast");
				$("#presupuesto").html("Presupuesto");
				$("#presupuesto").animate({
					width: "110px"
				}, "fast");
				$("#requerimientos").html("Requerimientos");
				$("#requerimientos").animate({
					width: "110px"
				}, "fast");
				$("#reportes").html("Reportes");
				$("#reportes").animate({
					width: "110px"
				}, "fast");
				$("#parametros").html("Parametros");
				$("#parametros").animate({
					width: "110px"
				}, "fast");
				$("#menu_hide").animate({
					width: "110px"
				}, "fast");
				$("#Menu ul").animate({
					width: "110px"
				}, "fast");
				$("#Menu").animate({
					width: "110px"
				}, "fast");
			}
			
			/*var Timer = setTimeout(function(){
				clearInterval(Timer);
				WindowSize();
			},250);*/
		};
		
		function ChangeElement (ID1, ID2)
		{
			if (LastActive1 != "" && LastActive1 != ID1) {
				$(LastActive1).hide();
				$(LastActive2).toggleClass("active");
				$(ID1).show();
				$(ID2).toggleClass("active");
				LastActive1 = ID1;
				LastActive2 = ID2;
			}
			else if (LastActive1 != "" && LastActive1 == ID1) {
				return;
			}
			else {
				LastActive1 = ID1;
				LastActive2 = ID2;
				$(ID1).show();
				$(ID2).toggleClass("active");
			}
		};
			
		$("#menu_hide").click(function()
		{
			if (menu_hidden == false) {
				/*$("#Menu").animate({
					width: "toggle"
				}, "fast");*/
				menu_hidden = true;
				MenuHide(menu_hidden);
				
			}
			else {
				/*$("#Menu").animate({
					width: "toggle"
				}, "fast");*/
				menu_hidden = false;
				MenuHide(menu_hidden);
			}
		});
		$("#inicio").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			ChangeElement("#Inicio_Content", this);
			if (InicioLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "inicio/index.php",
					async: true,
					success: function(data) 
					{
						$("#Inicio_Content").html(data);
						InicioLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Inicio");
		});
		$("#inicio").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Inicio");
			}
		});
		$("#inicio").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#terceros").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Terceros_Access == false) {
				return;
			}
			
			ChangeElement("#Terceros_Content", this);
			if (TercerosLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "terceros/index.php",
					async: true,
					success: function(data) 
					{
						$("#Terceros_Content").html(data);
						TercerosLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Terceros");
		});
		$("#terceros").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Terceros");
			}
		});
		$("#terceros").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#ventas").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Ventas_Access == false) {
				return;
			}
			
			ChangeElement("#Ventas_Content", this);
			if (VentasLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "ventas/index.php",
					async: true,
					success: function(data) 
					{
						$("#Ventas_Content").html(data);
						VentasLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Ventas");
		});
		$("#ventas").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Ventas");
			}
		});
		$("#ventas").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#compras").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Compras_Access == false) {
				return;
			}
			
			ChangeElement("#Compras_Content", this);
			if (ComprasLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "compras/index.php",
					async: true,
					success: function(data) 
					{
						$("#Compras_Content").html(data);
						ComprasLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Compras");
		});
		$("#compras").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Compras");
			}
		});
		$("#compras").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#inventario").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Inventario_Access == false) {
				return;
			}
			
			ChangeElement("#Inventario_Content", this);
			if (InventarioLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "inventario/index.php",
					async: true,
					success: function(data) 
					{
						$("#Inventario_Content").html(data);
						InventarioLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Inventario");
		});
		$("#inventario").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Inventario");
			}
		});
		$("#inventario").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#cartera").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Cartera_Access == false) {
				return;
			}
			
			ChangeElement("#Cartera_Content", this);
			if (CarteraLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "cartera/index.php",
					async: true,
					success: function(data) 
					{
						$("#Cartera_Content").html(data);
						CarteraLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Cartera");
		});
		$("#cartera").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Cartera");
			}
		});
		$("#cartera").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#CxP").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (CxP_Access == false) {
				return;
			}
			
			ChangeElement("#CxP_Content", this);
			if (CxpLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "cxp/index.php",
					async: true,
					success: function(data) 
					{
						$("#CxP_Content").html(data);
						CxpLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Cuentas por Pagar");
		});
		$("#CxP").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("CxP");
			}
		});
		$("#CxP").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#caja").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Caja_Access == false) {
				return;
			}
			
			ChangeElement("#Caja_Content", this);
			if (CajaLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "caja/index.php",
					async: true,
					success: function(data) 
					{
						$("#Caja_Content").html(data);
						CajaLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Caja");
		});
		$("#caja").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Caja");
			}
		});
		$("#caja").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#agenda").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Agenda");
			}
		});
		$("#agenda").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#nomina").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Nomina_Access == false) {
				return;
			}
			
			ChangeElement("#Nomina_Content", this);
			if (NominaLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "nomina/index.php",
					async: true,
					success: function(data) 
					{
						$("#Nomina_Content").html(data);
						NominaLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Nomina");
		});
		$("#nomina").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Nomina");
			}
		});
		$("#nomina").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#produccion").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Produccion_Access == false) {
				return;
			}
			
			ChangeElement("#Produccion_Content", this);
			if (ProduccionLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "produccion/index.php",
					async: true,
					success: function(data) 
					{
						$("#Produccion_Content").html(data);
						ProduccionLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Produccion");
		});
		$("#produccion").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Produccion");
			}
		});
		$("#produccion").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#figuracion").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Figuracion_Access == false) {
				return;
			}
			
			ChangeElement("#Figuracion_Content", this);
			if (FiguracionLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "figuracion/index.php",
					async: true,
					success: function(data) 
					{
						$("#Figuracion_Content").html(data);
						FiguracionLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Figuracion");
		});
		$("#figuracion").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Figuracion");
			}
		});
		$("#figuracion").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#maquinaria").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Maquinaria_Access == false) {
				return;
			}
			
			ChangeElement("#Maquinaria_Content", this);
			if (MaquinariaLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "maquinaria/index.php",
					async: true,
					success: function(data) 
					{
						$("#Maquinaria_Content").html(data);
						MaquinariaLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Maquinaria");
		});
		$("#maquinaria").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Maquinaria");
			}
		});
		$("#maquinaria").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#presupuesto").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Presupuesto_Access == false) {
				return;
			}
			
			ChangeElement("#Presupuesto_Content", this);
			if (PresupuestoLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "presupuesto/index.php",
					async: true,
					success: function(data) 
					{
						$("#Presupuesto_Content").html(data);
						PresupuestoLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Presupuesto");
		});
		$("#presupuesto").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Presupuesto");
			}
		});
		$("#presupuesto").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#requerimientos").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Requerimientos_Access == false) {
				return;
			}
			
			ChangeElement("#Requerimientos_Content", this);
			if (RequerimientosLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "requerimientos/index.php",
					async: true,
					success: function(data) 
					{
						$("#Requerimientos_Content").html(data);
						RequerimientosLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Requerimientos");
		});
		$("#requerimientos").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Requerimientos");
			}
		});
		$("#requerimientos").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		
		$("#reportes").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Reportes_Access == false) {
				return;
			}
			
			ChangeElement("#Reportes_Content", this);
			if (ReportesLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "reportes/index.php",
					async: true,
					success: function(data) 
					{
						$("#Reportes_Content").html(data);
						ReportesLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Reportes");
		});
		$("#reportes").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Reportes");
			}
		});
		$("#reportes").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		$("#parametros").click(function()
		{
			if (UserID == "") {
				$("#Login_Content").show();
				return;
			}
			
			if (Parametros_Access == false) {
				return;
			}
			
			ChangeElement("#Parametros_Content", this);
			if (ParametrosLoaded == false)
			{
				$("#Loading").show();
				$.ajax({
					type:"POST",
					url: "parametros/index.php",
					async: true,
					success: function(data) 
					{
						$("#Parametros_Content").html(data);
						ParametrosLoaded = true;
					},
					complete: function(){
						$("#Loading").hide();
					}
				});
			}
			SystemMap("Parametros");
		});
		$("#parametros").mouseenter(function(){
			if (menu_hidden) {
				$(this).css("position", "relative");
				$(this).animate({
					width: "110px"
				}, "fast");
				$(this).html("Parametros");
			}
		});
		$("#parametros").mouseleave(function(){
			if (menu_hidden) {
				$(this).css("position", "initial");
				$(this).animate({
					width: "30px"
				}, "fast");
				$(this).html("&nbsp;");
			}
		});
		
		MenuHide(menu_hidden);
		
		// ---- START LOGIN FORM
		$("#username").jqxInput({
			theme: mytheme,
			height: 25,
			width: 150
		});
		$("#password").jqxInput({
			theme: mytheme,
			height: 25,
			width: 150
		});
		
		$("#LoginButton").jqxButton({
			theme: mytheme,
			width: 120,
			height: 30
		});
		//Validation
		$("#login_form").jqxValidator({
			rules: [
				{ input: "#username", message: "Se Requiere un Nombre de Usuario!", action: "keyup, blur", rule: "required" },
				{ input: "#username", message: "El Nombre de Usuario debe contener entre 3 a 16 caracteres!", action: "keyup, blur", rule: "length=3,16" },
				{ input: "#password", message: "Se Requiere una Contraseña!", action: "keyup, blur", rule: "required" },
				{ input: "#password", message: "La Contraseña debe contener entre 4 a 20 caracteres!", action: "keyup, blur", rule: "length=4,20" }
			]
		});
		//Validate Form
		$("#LoginButton").click(function ()
		{
			var validationResult = function (isValid)
			{
				//$("#login_form").submit();
				if (isValid)
				{
					$.ajax({
						dataType: "json",
						url: "modulos/login.php",
						type: "POST",
						data: {
							"username":$("#username").val(),
							"password":$("#password").val()
						},
						success: function (data, status, xhr)
						{
							switch(data[0]["Status"])
							{
								case "Success":
									location.reload();
								break;
								case "Error":
								case "Down":
									$("#login_mess").html(data[0]["Message"]);
								break;
							}
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							//alert(textStatus+ " - " +errorThrown);
							$("#login_mess").html("Ha ocurrido un Error Fatal... Contacte al Soporte Tecnico!");
						}
					});
				}
				else
				{
					return;
				}
			}
			$("#login_form").jqxValidator("validate", validationResult);
		});
		
		if (UserID != "")
		{
			$("#PauseButton").jqxButton({
				theme: mytheme,
				width: 60,
				height: 30,
				template: "info"
			});
			$("#PauseButton").bind("click", function ()
			{
				$.ajax({
					dataType: "text",
					url: "modulos/access.php",
					data: {"SetPause":true},
					success: function (data, status, xhr)
					{
						if (data == "Success")
						{
							UserPause = true;
							$("#Pause_Window").jqxWindow("open");
						}
						else
						{
							UserPause = false;
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						alert(textStatus+ " - " +errorThrown);
					}
				});
			});
			
			$("#LogOutButton").jqxButton({
				theme: mytheme,
				width: 50,
				height: 30,
				template: "primary"
			});
		
			$("#Pause_Window").jqxWindow({
				theme: mytheme,
				autoOpen: false,
				showCloseButton: false,
				height: 210,
				width: 300,
				resizable: false,
				isModal: true,
				modalOpacity: 0.3,
				initContent: function ()
				{
					$("#user_pass").jqxInput({
						theme: mytheme,
						height: 25,
						width: 150
					});
					
					$("#user_pass").keydown(function(event)
					{
						if(event.which == 13)
						{
							$("#UnPauseButton").trigger("click");
						}
					});
					
					$("#pause_form").jqxValidator({
						rules: [
							{ input: "#user_pass", message: "Se Requiere una Contraseña!", action: "keyup, blur", rule: "required" },
							{ input: "#user_pass", message: "La Contraseña debe contener entre 4 a 20 caracteres!", action: "keyup, blur", rule: "length=4,20" },
							{
								input: "#user_pass",
								message: "Contraseña Incorrecta!",
								action: "valueChanged",
								rule: function (input, commit)
								{
									$.ajax({
										dataType: "text",
										type: "POST",
										url: "modulos/access.php",
										data: {"ValidatePass":$("#user_pass").val()},
										success: function(data)
										{
											if (data == "Success")
											{
												commit(true);
											}
											else commit(false);
										},
										error: function()
										{
											commit(false);
										}
									});
								}
							},
						]
					});
			
					$("#UnPauseButton").jqxButton({
						theme: mytheme,
						width: 150,
						height: 30,
						template: "info"
					});
					$("#UnPauseButton").bind("click", function ()
					{
						var validationResult = function (isValid) {
							if (isValid)
							{
								$.ajax({
									dataType: "text",
									url: "modulos/access.php",
									data: {"SetPause":false},
									success: function (data, status, xhr)
									{
										if (data == "Success")
										{
											UserPause = false;
											$("#user_pass").val("");
											$("#Pause_Window").jqxWindow("close");
										}
										else
										{
											UserPause = false;
											$("#user_pass").val("");
											$("#Pause_Window").jqxWindow("close");
										}
									},
									error: function (jqXHR, textStatus, errorThrown) {
										alert(textStatus+ " - " +errorThrown);
										UserPause = false;
										$("#user_pass").val("");
										$("#Pause_Window").jqxWindow("close");
									}
								});
							}
						}
						$("#pause_form").jqxValidator("validate", validationResult);
					});
				}
			});
			
			$("#system").click(function()
			{
				<?php if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "") { ?>
					<?php if ($_SESSION["UserAccess"][0]["Lvl"] == "Administrador") { ?>
						$.ajax({
							dataType: "text",
							url: "modulos/access.php",
							data: {"GetSystemStatus":true},
							success: function (data, status, xhr)
							{
								if (data == "OK")
								{
									$("#OffLine_Window").jqxWindow("open");
								}
								else
								{
									Alerts_Box("El Sistema se encuentra Cerrado.<br />¿Desea Abrirlo?", 1, true);
									var Check_Sys_Timer = setInterval(function()
									{
										if (ClickOK == true)
										{
											clearInterval(Check_Sys_Timer);
											ClickOK = false;
											$.ajax({
												dataType: "text",
												url: "modulos/access.php",
												data: {
													"ShutDown":false,
													"Comment":"",
												},
												success: function (data, status, xhr)
												{
													if (data == "Success")
														Alerts_Box("El Sistema ahora se encuentra Abierto.", 2);
													else
													{
														Alerts_Box("Ocurrió un Error al Intentar Re-Arir el Sistema<br />"
														+"Intente nuevamente luego de unos segundos...", 3);
													}
												},
												error: function (jqXHR, textStatus, errorThrown)
												{
													Alerts_Box("Ocurrió un Error al Intentar Re-Arir el Sistema<br />"
													+"Intente nuevamente luego de unos segundos...", 3);
												}
											});
										}
										if (ClickCANCEL == true)
										{
											clearInterval(Check_Sys_Timer);
											ClickCANCEL = false;
										}
									}, 10);
								}
							},
							error: function (jqXHR, textStatus, errorThrown)
							{
								Alerts_Box("Ocurrió un Error al Intentar Verificar el Estado del Sistema<br />"
								+"Intente nuevamente luego de unos segundos...", 3);
								//alert(textStatus+ " - " +errorThrown);
							}
						});
					<?php } ?>
				<?php } ?>
			});
			
			$("#OffLine_Window").jqxWindow({
				theme: mytheme,
				autoOpen: false,
				showCloseButton: true,
				height: 250,
				width: 300,
				resizable: false,
				isModal: true,
				modalOpacity: 0.3,
				initContent: function ()
				{
					$("#comment").keydown(function(event)
					{
						if(event.which == 13)
						{
							$("#ShutdownButton").trigger("click");
						}
					});
					
					$("#offline_form").jqxValidator({
						rules: [
							{ input: "#comment", message: "Se Requiere un Motivo!", action: "keyup, blur", rule: "required" },
						]
					});
			
					$("#ShutdownButton").jqxButton({
						theme: mytheme,
						width: 150,
						height: 30,
						template: "info"
					});
					$("#ShutdownButton").bind("click", function ()
					{
						var validationResult = function (isValid) {
							if (isValid)
							{
								$.ajax({
									dataType: "text",
									url: "modulos/access.php",
									data: {
										"ShutDown":true,
										"Comment":$("#comment").val()
									},
									success: function (data, status, xhr)
									{
										if (data == "Success")
										{
											$("#comment").val("");
											$("#OffLine_Window").jqxWindow("close");
										}
										else
										{
											$("#comment").val("");
											$("#OffLine_Window").jqxWindow("close");
										}
									},
									error: function (jqXHR, textStatus, errorThrown) {
										alert(textStatus+ " - " +errorThrown);
										$("#comment").val("");
										$("#OffLine_Window").jqxWindow("close");
									}
								});
							}
						}
						$("#offline_form").jqxValidator("validate", validationResult);
					});
				}
			});
			
		}
		// ---- END LOGIN FORM
		
		<?php if (isset($REGISTER) && $REGISTER == true)
		{ ?>
			$("#RegisterButton").jqxButton({
				theme: mytheme,
				width: 120,
				height: 30,
				template: "success"
			});
			$("#RegisterButton").bind("click", function ()
			{
				$("#Register_Window").jqxWindow("open");
			});
			
			$("#Register_Window").jqxWindow(
			{
				theme: mytheme,
				autoOpen: false,
				showCloseButton: true,
				height: 450,
				width: 300,
				resizable: false,
				isModal: true,
				modalOpacity: 0.3,
				initContent: function ()
				{	
					$("#register_form").jqxValidator(
					{
						rules: [
							{ input: "#reg_user_id", message: "ID Requerido!", action: "keyup, blur", rule: "required" },
							{ input: "#reg_user_id", message: "ID debe ser de 4 a 16 Caracteres!", action: "keyup, blur", rule: "length=4,16" },
							{
								input: "#reg_user_id", message: "Solo se Permiten Caracteres AlfaNumericos", action: 'keyup, blur', rule: function (input, commit)
								{
									var id = $("#reg_user_id").val();
									if(/[^0-9a-zA-Z]/.test(id))
										return false;
									else
										return true;
								}
							},
							{
								input: "#reg_user_id", message: "El ID ya existe en el Sistema.", action: 'valueChanged, blur', rule: function (input, commit)
								{
									$.ajax({
										dataType: "json",
										type: "POST",
										url: "modulos/access.php",
										data: {
											"Validation":true,
											"ID":$("#reg_user_id").val(),
										},
										success: function (data, status, xhr)
										{
											if (data[0] != undefined)
											{
												if (data[0]["MESSAGE"] == "EXIST")
												{
													commit(false);
												}
												else commit(true);
											}
											else commit(true);
										},
										error: function (jqXHR, textStatus, errorThrown)
										{
											Alerts_Box("Ocurrió un error mientras se validaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
											+"Error: "+errorThrown, 3);
											commit(false);
										}
									});
								}
							},
							{ input: "#reg_user_code", message: "Codigo de Usuario Requerido!", action: "keyup, blur", rule: "required" },
							{ input: "#reg_user_code", message: "Codigo debe ser de 4 a 8 Caracteres!", action: "keyup, blur", rule: "length=4,8" },
							{
								input: "#reg_user_code", message: "Solo se Permiten Caracteres AlfaNumericos", action: 'keyup, blur', rule: function (input, commit)
								{
									var id = $("#reg_user_code").val();
									if(/[^0-9a-zA-Z]/.test(id))
										return false;
									else
										return true;
								}
							},
							{
								input: "#reg_user_code", message: "El Codigo ya existe en el Sistema.", action: 'valueChanged, blur', rule: function (input, commit)
								{
									$.ajax({
										dataType: "json",
										type: "POST",
										url: "modulos/access.php",
										data: {
											"Validation":true,
											"CODE":$("#reg_user_code").val(),
										},
										success: function (data, status, xhr)
										{
											if (data[0] != undefined)
											{
												if (data[0]["MESSAGE"] == "EXIST")
												{
													commit(false);
												}
												else commit(true);
											}
											else commit(true);
										},
										error: function (jqXHR, textStatus, errorThrown)
										{
											Alerts_Box("Ocurrió un error mientras se validaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
											+"Error: "+errorThrown, 3);
											commit(false);
										}
									});
								}
							},
							{ input: "#reg_user_pass", message: "Contraseña Requerida!", action: 'keyup, blur', rule: 'required' },
							{ input: "#reg_user_pass", message: "Contraseña debe ser de 4 a 16 Caracteres!", action: 'keyup, blur', rule: 'length=4,16' },
							{ input: "#reg_user_pass2", message: "Confirmacion Requerida!", action: 'keyup, blur', rule: 'required' },
							{
								input: "#reg_user_pass2", message: "Las Contraseñas no Coinciden!", action: 'keyup, blur', rule: function (input, commit) {
									var Password1 = $("#reg_user_pass").val();
									var Password2 = $("#reg_user_pass2").val();
									return Password1 == Password2;
								}
							},
							{ input: "#reg_user_email", message: "Correo Requerido!", action: "keyup, blur", rule: "required" },
							{ input: "#reg_user_email", message: "Correo Invalido!", action: "keyup, blur", rule: "email" },
							{
								input: "#reg_user_email", message: "El Correo ya existe en el Sistema.", action: 'valueChanged, blur', rule: function (input, commit)
								{
									$.ajax({
										dataType: "json",
										type: "POST",
										url: "modulos/access.php",
										data: {
											"Validation":true,
											"EMAIL":$("#reg_user_email").val(),
										},
										success: function (data, status, xhr)
										{
											if (data[0] != undefined)
											{
												if (data[0]["MESSAGE"] == "EXIST")
												{
													commit(false);
												}
												else commit(true);
											}
											else commit(true);
										},
										error: function (jqXHR, textStatus, errorThrown)
										{
											Alerts_Box("Ocurrió un error mientras se validaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
											+"Error: "+errorThrown, 3);
											commit(false);
										}
									});
								}
							},
							{ input: "#reg_user_email2", message: "Confirmacion Requerida!", action: "keyup, blur", rule: "required" },
							{ input: "#reg_user_email2", message: "Correo Invalido!", action: "keyup, blur", rule: "email" },
							{
								input: "#reg_user_email2", message: "Los Correos no Coinciden!", action: 'keyup, blur', rule: function (input, commit) {
									var Email1 = $("#reg_user_email").val();
									var Email2 = $("#reg_user_email2").val();
									return Email1 == Email2;
								}
							},
							
						]
					});
					
					$("#reg_user_id").jqxInput({
						theme: mytheme,
						height: 25,
						width: 250,
						placeHolder: "Ej: Su Cedula",
					});
					
					$("#reg_user_code").jqxInput({
						theme: mytheme,
						height: 25,
						width: 250,
						placeHolder: "Ej: ANDRESZ",
					});
					
					$("#reg_user_pass").jqxPasswordInput({
						theme: mytheme,
						height: 25,
						width: 250
					});
					
					$("#reg_user_pass2").jqxPasswordInput({
						theme: mytheme,
						height: 25,
						width: 250
					});
					
					$("#reg_user_email").jqxInput({
						theme: mytheme,
						height: 25,
						width: 250
					});
					
					$("#reg_user_email2").jqxInput({
						theme: mytheme,
						height: 25,
						width: 250
					});
			
					$("#RegButton").jqxButton({
						theme: mytheme,
						width: 250,
						height: 30,
						template: "success"
					});
					$("#RegButton").bind("click", function ()
					{
						var validationResult = function (isValid) {
							if (isValid)
							{
								$("#Loading_Mess").html("Procesando Solicitud...");
								$("#Loading").show();
								$.ajax({
									dataType: "json",
									type: "POST",
									url: "modulos/access.php",
									data: {
										"Registro":true,
										"UserID":$("#reg_user_id").val(),
										"UserPass":$("#reg_user_pass").val(),
										"UserCode":$("#reg_user_code").val(),
										"UserEmail":$("#reg_user_email").val(),
									},
									success: function (data, status, xhr)
									{
										$("#Loading").hide();
										$("#Loading_Mess").html("Cargando...");
										if (data[0]["MESSAGE"] == "SUCCESS")
										{
											$("#reg_user_id").val("");
											$("#reg_user_code").val("");
											$("#reg_user_pass").val("");
											$("#reg_user_pass2").val("");
											$("#reg_user_email").val("");
											$("#reg_user_email2").val("");
											Alerts_Box("Su Cuenta ha sido Creada con Exito.", 2);
											$("#Register_Window").jqxWindow("close");
										}
										else
										{
											$("#reg_user_id").val("");
											$("#reg_user_code").val("");
											$("#reg_user_pass").val("");
											$("#reg_user_pass2").val("");
											$("#reg_user_email").val("");
											$("#reg_user_email2").val("");
											Alerts_Box("Ocurrió un Error al Crear su Cuenta, Contacte al Soporte Tecnico", 3);
											$("#Register_Window").jqxWindow("close");
										}
									},
									error: function (jqXHR, textStatus, errorThrown)
									{
										$("#Loading").hide();
										$("#Loading_Mess").html("Cargando...");
										Alerts_Box("Ocurrió un error mientras se guardaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
										+"Error: "+errorThrown, 3);
										$("#reg_user_id").val("");
										$("#reg_user_code").val("");
										$("#reg_user_pass").val("");
										$("#reg_user_pass2").val("");
										$("#reg_user_email").val("");
										$("#reg_user_email2").val("");
										$("#Register_Window").jqxWindow("close");
									}
								});
							}
						}
						$("#register_form").jqxValidator("validate", validationResult);
					});
				}
			});
		<?php 
		} ?>
		
		WindowSize();
		
		$("#Inicio_Content").hide();
		$("#Terceros_Content").hide();
		$("#Ventas_Content").hide();
		$("#Compras_Content").hide();
		$("#Inventario_Content").hide();
		$("#Cartera_Content").hide();
		$("#CxP_Content").hide();
		$("#Caja_Content").hide();
		$("#Nomina_Content").hide();
		$("#Produccion_Content").hide();
		$("#Figuracion_Content").hide();
		$("#Maquinaria_Content").hide();
		$("#Presupuesto_Content").hide();
		$("#Requerimientos_Content").hide();
		$("#Reportes_Content").hide();
		$("#Parametros_Content").hide();
		
		<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
		?>
			$("#Login_Content").hide();
			$("#inicio").trigger("click");
			if (UserPause)
				$("#Pause_Window").jqxWindow("open");
			
			<?php
				$data = $_SESSION["UserAccess"];
				$num = count($data);
				
				if ($data[0]["Lvl"] != "Administrador") {
					for ($i = 0; $i < $num; $i++)
					{
						switch($data[$i]["Modulo"])
						{
							case "Terceros":
						
			?>
								$("#terceros").removeClass("disabled");
								Terceros_Access = true;
			<?php	
							break;
							case "Ventas":
			?>
								$("#ventas").removeClass("disabled");
								Ventas_Access = true;
			<?php	
							break;
							case "Compras":
			?>
								$("#compras").removeClass("disabled");
								Compras_Access = true;
			<?php	
							break;
							case "Inventario":
			?>
								$("#inventario").removeClass("disabled");
								Inventario_Access = true;
			<?php	
							break;
							case "Cartera":
			?>
								$("#cartera").removeClass("disabled");
								Cartera_Access = true;
			<?php	
							break;
							case "CxP":
			?>
								$("#CxP").removeClass("disabled");
								CxP_Access = true;
			<?php	
							break;
							case "Caja":
			?>
								$("#caja").removeClass("disabled");
								Caja_Access = true;
			<?php	
							break;
							case "Agenda":
			?>
								$("#agenda").removeClass("disabled");
								Agenda_Access = true;
			<?php	
							break;
							case "Nomina":
			?>
								$("#nomina").removeClass("disabled");
								Nomina_Access = true;
			<?php	
							break;
							case "Maquinaria":
			?>
								$("#maquinaria").removeClass("disabled");
								Maquinaria_Access = true;
			<?php	
							break;
							case "Produccion":
			?>
								$("#produccion").removeClass("disabled");
								Produccion_Access = true;
			<?php	
							break;
							case "Figuracion":
			?>
								$("#figuracion").removeClass("disabled");
								Figuracion_Access = true;
			<?php	
							break;
							case "Presupuesto":
			?>
								$("#presupuesto").removeClass("disabled");
								Presupuesto_Access = true;
			<?php	
							break;
							case "Requerimientos":
			?>
								$("#requerimientos").removeClass("disabled");
								Requerimientos_Access = true;
			<?php	
							break;
							case "Reportes":
			?>
								$("#reportes").removeClass("disabled");
								Reportes_Access = true;
			<?php	
							break;
							case "Parametros":
			?>
								$("#parametros").removeClass("disabled");
								Parametros_Access = true;
			<?php	
							break;
						}
					}
				}
			?>
	<?php
		}
		else
		{
	?>
		$("#Login_Content").show();
	<?php } ?>
	});
	</script>
	<script type="text/javascript">
		var currenttime = "<?php print date("F d, Y H:i:s", time())?>";
		var montharray=new Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		var serverdate=new Date(currenttime);
		
		function displaytime()
		{
			serverdate.setSeconds(serverdate.getSeconds()+1)
			
			var day = serverdate.getDate();
			day = day + "";// to string
			if (day.length == 1)
			{
				day = "0" + day;
			};
			
			var datestring = montharray[serverdate.getMonth()]+" "+day+", "+serverdate.getFullYear();
			
			var hour = serverdate.getHours();
			var minute = serverdate.getMinutes();
			var second = serverdate.getSeconds();
			
			hour = hour + "";// to string
			if (hour.length == 1)
			{
				hour = "0" + hour;
			};
			minute = minute + "";// to string
			if (minute.length == 1)
			{
				minute = "0" + minute;
			};
			second = second + "";// to string
			if (second.length == 1)
			{
				second = "0" + second;
			};
			
			var timestring = hour + ":" + minute + ":" + second;
			document.getElementById("Logo_Date").innerHTML = datestring + " " + timestring;
		}
		
		window.onload = function(){
			setInterval("displaytime()", 1000)
		}
	</script>
</head>
<body>
	<div id="Logo">
		<div id="Logo_Icon">
			<img src="images/logo.png" alt="<?php echo $COMPANY_NAME ?>" width="110" height="30">
		</div>
		<div id="Logo_SystemMes">
			<?php echo $NAME ?>
		</div>
		<div id="Logo_Right">
			<div id="Logo_Welcome">
				Bienvenido 
			</div>
			<div id="Logo_User">
				<?php echo isset($_SESSION["UserCode"]) ? $_SESSION["UserCode"]:"Invitado"?>
			</div>
			<?php if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "") { ?>
				<div style="width: auto; margin: 7px 10px 0px 0px; float: left;">
					<input id="PauseButton" type="submit" value="Pausar" />
				</div>
				<form id="logout_form" method="POST" action="modulos/logout.php">
					<input id="LogOutButton" type="submit" value="Salir" />
				</form>
			<?php } ?>
			<div id="Logo_Date">
			</div>
		</div>
	</div>
	<div id="Menu">
		<ul>
			<li id="menu_hide" style="padding:0px 0px 1px 0px; font-size:23px;">
				&#8633;
			</li>
			<?php 
			if (isset($INICIO) && $INICIO == true)
			{
			?>
			<li id="inicio">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($TERCEROS) && $TERCEROS == true)
			{
			?>
			<li id="terceros">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($VENTAS) && $VENTAS == true)
			{
			?>
			<li id="ventas">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($COMPRAS) && $COMPRAS == true)
			{
			?>
			<li id="compras">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($INVENTARIO) && $INVENTARIO == true)
			{
			?>
			<li id="inventario">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($CARTERA) && $CARTERA == true)
			{
			?>
			<li id="cartera">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($CXP) && $CXP == true)
			{
			?>
			<li id="CxP">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($CAJA) && $CAJA == true)
			{
			?>
			<li id="caja">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($AGENDA) && $AGENDA == true)
			{
			?>
			<li id="agenda">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($NOMINA) && $NOMINA == true)
			{
			?>
			<li id="nomina">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($PRODUCCION) && $PRODUCCION == true)
			{
			?>
			<li id="produccion">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($FIGURACION) && $FIGURACION == true)
			{
			?>
			<li id="figuracion">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($MAQUINARIA) && $MAQUINARIA == true)
			{
			?>
			<li id="maquinaria">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($PRESUPUESTO) && $PRESUPUESTO == true)
			{
			?>
			<li id="presupuesto">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($REQUERIMIENTOS) && $REQUERIMIENTOS == true)
			{
			?>
			<li id="requerimientos">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($REPORTES) && $REPORTES == true)
			{
			?>
			<li id="reportes">
				&nbsp;
			</li>
			<?php 
			}
			
			if (isset($PARAMETROS) && $PARAMETROS == true)
			{
			?>
			<li id="parametros">
				&nbsp;
			</li>
			<?php 
			}
			?>
		</ul>
	</div>
	<div id="Ask_Alert" style="display:none;">
		<div id="ask_alert_title">
		</div>
		<div id="ask_alert_container">
			<div id="ask_alert_text" style="text-align:center;">
			</div>
			<div>
				<div align="right" style="margin: 15px auto auto auto; padding: 0px;">
					<input type="button" id="ok" value="Aceptar" style="margin-right: 10px" />
					<input type="button" id="cancel" value="Cancelar" />
				</div>
			</div>
		</div>
	</div>
	<div id="Normal_Alert" style="display:none; min-height:115px; max-height:215px; ">
		<div id="normal_alert_title">
		</div>
		<div id="normal_alert_container">
			<div id="normal_alert_text" style="text-align:center;">
			</div>
			<div>
				<div align="right" style="margin: 15px auto auto auto; padding: 0px;">
					<input type="button" id="ok2" value="OK" />
				</div>
			</div>
		</div>
	</div>
	<div id="Loading">
		<div id="Loading_Center" align="center" style="list-style:none;">
			<img src="images/loader.gif" alt="Cargando" height="32" width="32">
			<span id="Loading_Mess">
				Cargando...
			</span>
		</div>
	</div>
	<?php if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "") { ?>
	<div id="Pause_Window" style="display:none;">
		<div id="Pause_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
			<div style="margin-left: 70px;">Session Pausada</div>
		</div>
		<div id="Pause_Window_Content" class="WindowContainer" align="center">
			<p style="text-align:center;">
				Su session se encuentra pausada.<br />para continuar, favor ingresar su contrase&ntilde;a.
			</p>
			<form id="pause_form" onsubmit="return false;">
				<table cellspacing="2" cellpadding="5" align="center">
					<tr>
						<td>
						<p style="text-align:center; font-size:14px; margin:0px;">
							Contraseña
						</p>
						</td>
					</tr>
					<tr>
						<td>
							<input type="password" id="user_pass" />
						</td>
					</tr>
					<tr>
						<td>
							<div align="center">
								<input id="UnPauseButton" type="button" value="Continuar Sesion" />
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div id="OffLine_Window" style="display:none;">
		<div id="OffLine_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
			<div style="margin-left: 70px;">Cerrar Sistema</div>
		</div>
		<div id="OffLine_Window_Content" class="WindowContainer" align="center">
			<p style="text-align:center;">
				Ingrese un motivo por el cual se cerrará<br/>el sistema y todos los usuarios quedaran<br />
				fuera de linea en los proximos<br/>5 minutos.
			</p>
			<form id="offline_form" onsubmit="return false;">
				<table cellspacing="2" cellpadding="5" align="center">
					<tr>
						<td>
							<textarea rows="3" cols="30" maxlength="200" style="resize:none;" id="comment"></textarea>
						</td>
					</tr>
					<tr>
						<td>
							<div align="center">
								<input id="ShutdownButton" type="button" value="Cerrar Sistema" />
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<?php }
	
	if (isset($REGISTER) && $REGISTER == true)
	{ ?>
	<div id="Register_Window" style="display:none;">
		<div id="Register_Window_Title" style="height:22px; font-size: 16px; font-weight:700; color: gray;">
			<div style="margin-left: 105px;">Registrarse</div>
		</div>
		<div id="Register_Window_Content" class="WindowContainer" align="center">
			<form id="register_form" onsubmit="return false;">
				<table cellspacing="0" cellpadding="4" align="center" style="font-size:12px;">
					<tr>
						<td>
							Nombre de Usuario
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" id="reg_user_id"/>
						</td>
					</tr>
					<tr>
						<td>
							Codigo
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" id="reg_user_code"/>
						</td>
					</tr>
					<tr>
						<td>
							Contraseña
						</td>
					</tr>
					<tr>
						<td>
							<input type="password" id="reg_user_pass"/>
						</td>
					</tr>
					<tr>
						<td>
							Confirmar Contraseña
						</td>
					</tr>
					<tr>
						<td>
							<input type="password" id="reg_user_pass2"/>
						</td>
					</tr>
					<tr>
						<td>
							Correo / E-mail
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" id="reg_user_email"/>
						</td>
					</tr>
					<tr>
						<td>
							Confirmar Correo / E-mail
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" id="reg_user_email2"/>
						</td>
					</tr>
					<tr>
						<td style="text-align: center;">
							<input id="RegButton" type="button" value="Registrarse" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<?php } ?>
	<div id="Container1">
		<div id="Login_Content" style="display:none;">
			<div style="padding:0px; margin:7px 0px">
				<img src="images/logo.png" alt="<?php echo $COMPANY_NAME ?>" width="300" height="82">
			</div>
			<form id="login_form" onsubmit="return false;">
				<table cellspacing="2" cellpadding="5">
					<tr>
						<td>
							Usuario:
						</td>
						<td>
							<input type="text" id="username" name="username" />
						</td>
					</tr>
					<tr>
						<td>
							Contraseña:
						</td>
						<td>
							<input type="password" id="password" name="password" />
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width:250px;">
							<div id="login_mess" style="margin:10px 0px; color:red; font-size:14px;"></div>
						</td>
					</tr>
				<?php if (isset($REGISTER) && $REGISTER == true) { ?>
					<tr>
						<td colspan="2" style="text-align: center; list-style:none;">
							<li style="float:left;">
								<input id="LoginButton" type="submit" value="Ingresar" />
							</li>
							<li style="float:left; margin-left: 5px;">
								<input id="RegisterButton" type="submit" value="Registrarse" />
							</li>
						</td>
					</tr>
				<?php 
				}
				else
				{ ?>
					<tr>
						<td colspan="2" style="text-align: center;">
							<input id="LoginButton" type="submit" value="Ingresar" />
						</td>
					</tr>
				<?php } ?>
				<?php if (isset($AUTO_REG) && $AUTO_REG == true) { ?>
					<tr>
						<td colspan="2" style="width:250px;">
							<div style="margin:10px 0px; color:#4684BB; font-size:11px;">¿Problemas para Acceder? Envienos un correo a: web@ingeniamosweb.com</div>
						</td>
					</tr>
				<?php } ?>
				</table>
			</form>
		</div>
		<div id="Inicio_Content">
		</div>
		<div id="Terceros_Content">
		</div>
		<div id="Ventas_Content">
		</div>
		<div id="Compras_Content">
		</div>
		<div id="Inventario_Content">
		</div>
		<div id="Cartera_Content">
		</div>
		<div id="CxP_Content">
		</div>
		<div id="Caja_Content">
		</div>
		<div id="Nomina_Content">
		</div>
		<div id="Produccion_Content">
		</div>
		<div id="Figuracion_Content">
		</div>
		<div id="Maquinaria_Content">
		</div>
		<div id="Presupuesto_Content">
		</div>
		<div id="Requerimientos_Content">
		</div>
		<div id="Reportes_Content">
		</div>
		<div id="Parametros_Content">
		</div>
	</div>
	<div id="copyright">
		<div id="system_mess" style="width:98%;">
			<img id="system" style="padding: 5px; display: block; margin-right:20px;" width="105" height="30" src="images/ingeniamos_logo_small.png" alt="Ingeniamos - web y software">
			<!-- Zona para Mensajes del Sistema con Animacion de Derecha a Izquierda -->
		</div>
	</div>
	<script type="text/javascript" src="scripts/AlertBox.js"></script>
	<script type="text/javascript">
		//--- GLOBAL VARS
		/*var CHECK_CHANGES_TIMEOUT2 = 10; //seconds
		var CHECK_LOCK = false; //seconds
		
		var GlobalClientesFullData = new Array();//--
		var GlobalClientesData = new Array();//--
		var GlobalVendedoresData = new Array();//--
		var GlobalEmpleadosData = new Array();
		var GlobalChoferData = new Array();//--
		var GlobalVehiculosData = new Array();//--
		var GlobalProductosFullData = new Array();//--
		var GlobalProductosData = new Array();//--
		var GlobalDespuntesData = new Array();//--
		var GlobalRepuestosFullData = new Array();//--
		var GlobalRepuestosData = new Array();//--
		var GlobalTercerosCategoria = new Array();
		var GlobalTercerosGrupo = new Array();
		var GlobalTercerosSubGrupo = new Array();
		var GlobalInventarioCategoria = new Array();
		var GlobalInventarioGrupo = new Array();
		var GlobalInventarioSubGrupo = new Array();
		var GlobalCajaCategoria = new Array();
		var GlobalCajaGrupo = new Array();
		var GlobalCajaSubGrupo = new Array();
		var GlobalCajaSubGrupo2 = new Array();
		
		//--- 
		$(document).ready(function ()
		{
		<?php if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
			{ ?>
			
				if (sessionStorage.getItem("is_reloaded"))
				{
					//sessionStorage.removeItem("is_reloaded");
					//var Tmp1 = sessionStorage.getItem("GlobalClientesFullData");
					//var Tmp2 = JSON.parse(Tmp1);
					GlobalClientesFullData = JSON.parse(sessionStorage.getItem("GlobalClientesFullData"));
					GlobalClientesData = JSON.parse(sessionStorage.getItem("GlobalClientesData"));
					GlobalVendedoresData = JSON.parse(sessionStorage.getItem("GlobalVendedoresData"));
					GlobalChoferData = JSON.parse(sessionStorage.getItem("GlobalChoferData"));
					GlobalVehiculosData = JSON.parse(sessionStorage.getItem("GlobalVehiculosData"));
					GlobalProductosFullData = JSON.parse(sessionStorage.getItem("GlobalProductosFullData"));
					GlobalProductosData = JSON.parse(sessionStorage.getItem("GlobalProductosData"));
					GlobalDespuntesData = JSON.parse(sessionStorage.getItem("GlobalDespuntesData"));
					GlobalRepuestosFullData = JSON.parse(sessionStorage.getItem("GlobalRepuestosFullData"));
					GlobalRepuestosData = JSON.parse(sessionStorage.getItem("GlobalRepuestosData"));
					
					//alert(GlobalClientesData[0]["Nombre"]+" - "+GlobalClientesData[0]["ClienteID"])
					//alert(GlobalVendedoresData[0]["Nombre"]+" - "+GlobalVendedoresData[0]["Codigo"])
					//alert(GlobalChoferData[0]["Chofer"]+" - "+GlobalChoferData[0]["ClienteID"])
					//alert(GlobalVehiculosData[0]["Placa"]+" - "+GlobalVehiculosData[0]["Modelo"])
					//alert(GlobalProductosData[0]["Nombre"]+" - "+GlobalProductosData[0]["Codigo"])
					//alert(GlobalRepuestosData[0]["Nombre"]+" - "+GlobalRepuestosData[0]["Codigo"])
					window.setInterval(CheckDataChanges, (CHECK_CHANGES_TIMEOUT2 * 1000));
				}
				else
				{
					sessionStorage.setItem("is_reloaded", true);
					window.setInterval(CheckDataChanges, (CHECK_CHANGES_TIMEOUT2 * 1000));
					GetFullData();
				}
				
				function GetFullData()
				{
					var CheckLoadingHide = setInterval(function()
					{
						if ($("#Loading_Mess").is(":hidden"))
						{
							clearInterval(CheckLoadingHide);
							
							sessionStorage.removeItem("GlobalClientesFullData");
							sessionStorage.removeItem("GlobalClientesFullData_MD5");
							sessionStorage.removeItem("GlobalClientesData");
							sessionStorage.removeItem("GlobalVendedoresData");
							sessionStorage.removeItem("GlobalVendedoresData_MD5");
							sessionStorage.removeItem("GlobalChoferData");
							sessionStorage.removeItem("GlobalChoferData_MD5");
							sessionStorage.removeItem("GlobalVehiculosData");
							sessionStorage.removeItem("GlobalVehiculosData_MD5");
							sessionStorage.removeItem("GlobalProductosFullData");
							sessionStorage.removeItem("GlobalProductosFullData_MD5");
							sessionStorage.removeItem("GlobalProductosData");
							sessionStorage.removeItem("GlobalDespuntesData");
							sessionStorage.removeItem("GlobalRepuestosFullData");
							sessionStorage.removeItem("GlobalRepuestosFullData_MD5");
							sessionStorage.removeItem("GlobalRepuestosData");
							
							$.ajax({
								dataType: "json",
								url: "modulos/datos.php",
								data: {"GetFullData":true},
								beforeSend: function(jqXHR, PlainObject)
								{
									$("#Loading_Mess").html("PreCargando Datos...");
									$("#Loading").show();
								},
								success: function (data, status, xhr)
								{
									// Clientes
									GlobalClientesFullData = data[0]["GlobalClientesFullData"];
									for(var i = 0; i < data[0]["GlobalClientesFullData"].length; i++)
									{
										var array = {};
										array["ClienteID"] = data[0]["GlobalClientesFullData"][i]["ClienteID"];
										array["Nombre"] = data[0]["GlobalClientesFullData"][i]["Nombre"];
										GlobalClientesData[i] = array;
										if ((i + 1) >= data[0]["GlobalClientesFullData"].length)
											sessionStorage.setItem("GlobalClientesData", JSON.stringify(GlobalClientesData));
									}
									sessionStorage.setItem("GlobalClientesFullData", JSON.stringify(data[0]["GlobalClientesFullData"]));
									sessionStorage.setItem("GlobalClientesFullData_MD5", data[0]["GlobalClientesFullData_MD5"]);
									
									// Vendedores/Cobradores
									GlobalVendedoresData = data[0]["GlobalVendedoresData"];
									sessionStorage.setItem("GlobalVendedoresData", JSON.stringify(data[0]["GlobalVendedoresData"]));
									sessionStorage.setItem("GlobalVendedoresData_MD5", data[0]["GlobalVendedoresData_MD5"]);
									
									// Choferes/Conducotres
									GlobalChoferData = data[0]["GlobalChoferData"];
									sessionStorage.setItem("GlobalChoferData", JSON.stringify(data[0]["GlobalChoferData"]));
									sessionStorage.setItem("GlobalChoferData_MD5", data[0]["GlobalChoferData_MD5"]);
									
									// Vehiculos
									GlobalVehiculosData = data[0]["GlobalVehiculosData"];
									sessionStorage.setItem("GlobalVehiculosData", JSON.stringify(data[0]["GlobalVehiculosData"]));
									sessionStorage.setItem("GlobalVehiculosData_MD5", data[0]["GlobalVehiculosData_MD5"]);
									
									// Productos
									GlobalProductosFullData = data[0]["GlobalProductosFullData"];
									var a = 0;
									for(var i = 0; i < data[0]["GlobalProductosFullData"].length; i++)
									{
										var array = {};
										array["Codigo"] = data[0]["GlobalProductosFullData"][i]["Codigo"];
										array["Nombre"] = data[0]["GlobalProductosFullData"][i]["Nombre"];
										GlobalProductosData[i] = array;
										
										if (data[0]["GlobalProductosFullData"][i]["Categoria"] == "Hierro" &&
										data[0]["GlobalProductosFullData"][i]["Grupo"] == "despunte")
										{
											GlobalDespuntesData[a] = array;
											a++;
										}
										
										if ((i + 1) >= data[0]["GlobalProductosFullData"].length)
										{
											sessionStorage.setItem("GlobalProductosData", JSON.stringify(GlobalProductosData));
											sessionStorage.setItem("GlobalDespuntesData", JSON.stringify(GlobalDespuntesData));
										}
									}
									sessionStorage.setItem("GlobalProductosFullData", JSON.stringify(data[0]["GlobalProductosFullData"]));
									sessionStorage.setItem("GlobalProductosFullData_MD5", data[0]["GlobalProductosFullData_MD5"]);
									
									// Repuestos
									GlobalRepuestosFullData = data[0]["GlobalRepuestosFullData"];
									for(var i = 0; i < data[0]["GlobalRepuestosFullData"].length; i++)
									{
										var array = {};
										array["Codigo"] = data[0]["GlobalRepuestosFullData"][i]["Codigo"];
										array["Nombre"] = data[0]["GlobalRepuestosFullData"][i]["Nombre"];
										GlobalRepuestosData[i] = array;
										
										if ((i + 1) >= data[0]["GlobalRepuestosFullData"].length)
											sessionStorage.setItem("GlobalRepuestosData", JSON.stringify(GlobalRepuestosData));
									}
									sessionStorage.setItem("GlobalRepuestosFullData", JSON.stringify(data[0]["GlobalRepuestosFullData"]));
									sessionStorage.setItem("GlobalRepuestosFullData_MD5", data[0]["GlobalRepuestosFullData_MD5"]);
									
								},
								error: function (jqXHR, textStatus, errorThrown)
								{
									Alerts_Box("Ocurrió un Error al Intentar Actualizar los Datos PreCargados.<br />"
									+"Error: "+errorThrown, 3);
								},
								complete: function(jqXHR, textStatus)
								{
									$("#Loading").hide();
									$("#Loading_Mess").html("Cargando...");
								}
							});
						}
					}, 10);
				}
				
				function CheckDataChanges()
				{
					if (CHECK_LOCK == true)
						return;
					
					CHECK_LOCK = true;
					
					$.ajax({
						dataType: "json",
						url: "modulos/datos.php",
						data: {"GetFullData":true},
						success: function (data, status, xhr)
						{
							//-- Clientes
							var MD5_Tmp1 = sessionStorage.getItem("GlobalClientesFullData_MD5");
							if (MD5_Tmp1 != data[0]["GlobalClientesFullData_MD5"])
							{
								alert("cambiando clientes")
								sessionStorage.removeItem("GlobalClientesFullData");
								sessionStorage.removeItem("GlobalClientesFullData_MD5");
								sessionStorage.removeItem("GlobalClientesData");
								
								GlobalClientesFullData = data[0]["GlobalClientesFullData"];
								for(var i = 0; i < data[0]["GlobalClientesFullData"].length; i++)
								{
									var array = {};
									array["ClienteID"] = data[0]["GlobalClientesFullData"][i]["ClienteID"];
									array["Nombre"] = data[0]["GlobalClientesFullData"][i]["Nombre"];
									GlobalClientesData[i] = array;
									if ((i + 1) >= data[0]["GlobalClientesFullData"].length)
										sessionStorage.setItem("GlobalClientesData", JSON.stringify(GlobalClientesData));
								}
									
								sessionStorage.setItem("GlobalClientesFullData", JSON.stringify(data[0]["GlobalClientesFullData"]));
								sessionStorage.setItem("GlobalClientesFullData_MD5", data[0]["GlobalClientesFullData_MD5"]);
							}
							
							//-- Vendedores/Cobradores
							var MD5_Tmp2 = sessionStorage.getItem("GlobalVendedoresData_MD5");
							if (MD5_Tmp2 != data[0]["GlobalVendedoresData_MD5"])
							{
								sessionStorage.removeItem("GlobalVendedoresData");
								sessionStorage.removeItem("GlobalVendedoresData_MD5");
								
								GlobalVendedoresData = data[0]["GlobalVendedoresData"];
								sessionStorage.setItem("GlobalVendedoresData", JSON.stringify(data[0]["GlobalVendedoresData"]));
								sessionStorage.setItem("GlobalVendedoresData_MD5", data[0]["GlobalVendedoresData_MD5"]);
							}
							
							//-- Choferes/Conducotres
							var MD5_Tmp4 = sessionStorage.getItem("GlobalChoferData_MD5");
							if (MD5_Tmp4 != data[0]["GlobalChoferData_MD5"])
							{
								sessionStorage.removeItem("GlobalChoferData");
								sessionStorage.removeItem("GlobalChoferData_MD5");
								
								GlobalChoferData = data[0]["GlobalChoferData"];
								sessionStorage.setItem("GlobalChoferData", JSON.stringify(data[0]["GlobalChoferData"]));
								sessionStorage.setItem("GlobalChoferData_MD5", data[0]["GlobalChoferData_MD5"]);
							}
							
							//-- Vehiculos
							var MD5_Tmp5 = sessionStorage.getItem("GlobalVehiculosDataa_MD5");
							if (MD5_Tmp5 != data[0]["GlobalVehiculosDataa_MD5"])
							{
								sessionStorage.removeItem("GlobalVehiculosData");
								sessionStorage.removeItem("GlobalVehiculosDataa_MD5");
								
								GlobalVehiculosData = data[0]["GlobalVehiculosData"];
								sessionStorage.setItem("GlobalVehiculosData", JSON.stringify(data[0]["GlobalVehiculosData"]));
								sessionStorage.setItem("GlobalVehiculosDataa_MD5", data[0]["GlobalVehiculosDataa_MD5"]);
							}
							
							//-- Productos
							var MD5_Tmp6 = sessionStorage.getItem("GlobalProductosFullData_MD5");
							if (MD5_Tmp6 != data[0]["GlobalProductosFullData_MD5"])
							{
								sessionStorage.removeItem("GlobalProductosFullData");
								sessionStorage.removeItem("GlobalProductosFullData_MD5");
								sessionStorage.removeItem("GlobalProductosData");
								sessionStorage.removeItem("GlobalDespuntesData");
								
								GlobalProductosFullData = data[0]["GlobalProductosFullData"];
								var a = 0;
								for(var i = 0; i < data[0]["GlobalProductosFullData"].length; i++)
								{
									var array = {};
									array["Codigo"] = data[0]["GlobalProductosFullData"][i]["Codigo"];
									array["Nombre"] = data[0]["GlobalProductosFullData"][i]["Nombre"];
									GlobalProductosData[i] = array;
									
									if (data[0]["GlobalProductosFullData"][i]["Categoria"] == "Hierro" &&
									data[0]["GlobalProductosFullData"][i]["Grupo"] == "despunte")
									{
										GlobalDespuntesData[a] = array;
										a++;
									}
									
									if ((i + 1) >= data[0]["GlobalProductosFullData"].length)
									{
										sessionStorage.setItem("GlobalProductosData", JSON.stringify(GlobalProductosData));
										sessionStorage.setItem("GlobalDespuntesData", JSON.stringify(GlobalDespuntesData));
									}
								}
								sessionStorage.setItem("GlobalProductosFullData", JSON.stringify(data[0]["GlobalProductosFullData"]));
								sessionStorage.setItem("GlobalProductosFullData_MD5", data[0]["GlobalProductosFullData_MD5"]);
							}
							
							//-- Repuestos
							var MD5_Tmp7 = sessionStorage.getItem("GlobalRepuestosFullData_MD5");
							if (MD5_Tmp7 != data[0]["GlobalRepuestosFullData_MD5"])
							{
								sessionStorage.removeItem("GlobalRepuestosFullData");
								sessionStorage.removeItem("GlobalRepuestosFullData_MD5");
								sessionStorage.removeItem("GlobalRepuestosData");
								
								GlobalRepuestosFullData = data[0]["GlobalRepuestosFullData"];
								for(var i = 0; i < data[0]["GlobalRepuestosFullData"].length; i++)
								{
									var array = {};
									array["Codigo"] = data[0]["GlobalRepuestosFullData"][i]["Codigo"];
									array["Nombre"] = data[0]["GlobalRepuestosFullData"][i]["Nombre"];
									GlobalRepuestosData[i] = array;
									
									if ((i + 1) >= data[0]["GlobalRepuestosFullData"].length)
										sessionStorage.setItem("GlobalRepuestosData", JSON.stringify(GlobalRepuestosData));
								}
								sessionStorage.setItem("GlobalRepuestosFullData", JSON.stringify(data[0]["GlobalRepuestosFullData"]));
								sessionStorage.setItem("GlobalRepuestosFullData_MD5", data[0]["GlobalRepuestosFullData_MD5"]);
							}
							
						},
						error: function (jqXHR, textStatus, errorThrown)
						{
							Alerts_Box("Ocurrió un Error al Intentar Actualizar los Datos Pre-Cargados.<br />"
							+"Error: "+errorThrown, 3);
						},
						complete: function(jqXHR, textStatus)
						{
							CHECK_LOCK = false;
						}
					});
				}
				
		<?php } ?>
		});*/
		
		
		$(window).resize(function(event) {
			if(this.resizeTO) clearTimeout(this.resizeTO);
			this.resizeTO = setTimeout(function() {
				WindowSize();
			}, 300);
		});
		
		function WindowSize()
		{
			var WindowWidth = $(window).width();
			var MenuWidth = document.getElementById("Menu").offsetWidth;
			var Container = document.getElementById("Container1").offsetWidth;
			
			/*if ($(window).width() < 1050)
			{
				var TotalWidth = WindowWidth - MenuWidth;
				$("#Container1").css("width", ""+TotalWidth+"px");
			}
			else if ($(window).width() < 1220)
			{
				var TotalWidth = WindowWidth - MenuWidth - 53;
				$("#Container1").css("width", ""+TotalWidth+"px");
				alert("Window ->"+WindowWidth+"<br/> Menu ->"+MenuWidth+"<br/> Container ->"+Container+"<br/> Total ->"+TotalWidth);
			}
			else
			{
				if (MenuWidth < 110)
					$("#Container1").css("width", "93%");
				else
					$("#Container1").css("width", "87%");
			}*/
			var Max = 93.5;
			var Min = 87;
			if ($(window).width() < 1220)
			{
				Max = 93;
				Min = 86;
			}
			
			if ($(window).width() < 1150)
			{
				Max = 92;
				Min = 84;
			}
			
			if (MenuWidth < 110)
				$("#Container1").css("width", ""+Max+"%");
			else
				$("#Container1").css("width", ""+Min+"%");
		};
		
		function ContentSize(Value)
		{
			var WindowWidth = $(window).width();
			var WindowHeight = $(window).height();
			var Max = 93.5;
			var Min = 87;
			
			if ($(window).width() < 1220)
			{
				Max = 93;
				Min = 86;
			}
			
			if ($(window).width() < 1150)
			{
				Max = 92;
				Min = 84;
			}
			
			if (Value)
				$("#Container1").css("width", ""+Max+"%");
			else
				$("#Container1").css("width", ""+Min+"%");
		};
	</script>
</body>