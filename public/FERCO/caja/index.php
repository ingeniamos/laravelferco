<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var CrearRCLoaded = false;
	var ModificarRCLoaded = false;
	var AprobarRCLoaded = false;
	var CierreCajaLoaded = false;
	var ChequesRCLoaded = false;
	var CajaGeneralLoaded = false;
	//---
	var Crear_Access = false;
	var Modificar_Access = false;
	var Aprobar_Access = false;
	var CierreCaja_Access = false;
	var Cheques_Access = false;
	var CajaGeneral_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#crearRC").addClass("disabled");
			$("#modificarRC").addClass("disabled");
			$("#aprobarRC").addClass("disabled");
			$("#cierre_caja").addClass("disabled");
			$("#cheques_caja").addClass("disabled");
			$("#caja_general").addClass("disabled");
		<?php
		}
		else { ?>
			Crear_Access = true;
			Modificar_Access = true;
			Aprobar_Access = true;
			CierreCaja_Access = true;
			Cheques_Access = true;
			CajaGeneral_Access = true;
		<?php
			} ?>
	<?php
	} ?>
	
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
	
	$("#crearRC").click(function()
	{
		if (Crear_Access == false) {
			return;
		}
		
		ChangeElement("#CrearRC_Content", this)
		if (CrearRCLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "caja/crear.php",
				async: true,
				success: function(data) 
				{
					$("#CrearRC_Content").html(data);
					CrearRCLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear", true);
	});
	
	$("#modificarRC").click(function()
	{
		if (Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#ModificarRC_Content", this)
		if (ModificarRCLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "caja/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#ModificarRC_Content").html(data);
					ModificarRCLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	
	$("#aprobarRC").click(function()
	{
		if (Aprobar_Access == false) {
			return;
		}
		
		ChangeElement("#AprobarRC_Content", this)
		if (AprobarRCLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "caja/aprobar.php",
				async: true,
				success: function(data) 
				{
					$("#AprobarRC_Content").html(data);
					AprobarRCLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar", true);
	});
	
	$("#cierre_caja").click(function()
	{
		if (CierreCaja_Access == false) {
			return;
		}
		
		ChangeElement("#CierreCaja_Content", this)
		if (CierreCajaLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "caja/cierre.php",
				async: true,
				success: function(data) 
				{
					$("#CierreCaja_Content").html(data);
					CierreCajaLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Cierre de Caja", true);
	});
	
	$("#cheques_caja").click(function()
	{
		if (Cheques_Access == false) {
			return;
		}
		
		ChangeElement("#ChequesRC_Content", this)
		if (ChequesRCLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "caja/cheques.php",
				async: true,
				success: function(data) 
				{
					$("#ChequesRC_Content").html(data);
					ChequesRCLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Cheques", true);
	});
	
	$("#caja_general").click(function()
	{
		if (CajaGeneral_Access == false) {
			return;
		}
		
		ChangeElement("#CajaGeneral_Content", this)
		if (CajaGeneralLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "caja/general.php",
				async: true,
				success: function(data) 
				{
					$("#CajaGeneral_Content").html(data);
					CajaGeneralLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Caja General", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#CrearRC_Content").hide();
	$("#ModificarRC_Content").hide();
	$("#AprobarRC_Content").hide();
	$("#CierreCaja_Content").hide();
	$("#ChequesRC_Content").hide();
	$("#CajaGeneral_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Caja") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#crearRC").removeClass("disabled");
								Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#modificarRC").removeClass("disabled");
								Modificar_Access = true;
			<?php
							break;
							case "Aprobar":
			?>
								$("#aprobarRC").removeClass("disabled");
								Aprobar_Access = true;
			<?php
							break;
							case "CierreCaja":
			?>
								$("#cierre_caja").removeClass("disabled");
								CierreCaja_Access = true;
			<?php
							break;
							case "Cheques":
			?>
								$("#cheques_caja").removeClass("disabled");
								Cheques_Access = true;
			<?php
							break;
							case "CajaGeneral":
			?>
								$("#caja_general").removeClass("disabled");
								CajaGeneral_Access = true;
			<?php
							break;
						}
					}
				}
			}
		} ?>
});
</script>
<div id="Container2">
	<div id="Tabs">
		<ul>
			<li id="crearRC" style="margin-right:2px">
				Crear RC
			</li>
			<li id="modificarRC" style="margin-right:2px">
				Editar RC
			</li>
			<li id="aprobarRC" style="margin-right:2px">
				Aprobar RC
			</li>
			<li id="cierre_caja" style="margin-right:2px">
				Cierre de Caja
			</li>
			<li id="cheques_caja" style="margin-right:2px">
				Cheques
			</li>
			<li id="caja_general">
				Caja General
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="CrearRC_Content">
		</div>
		<div id="ModificarRC_Content">
		</div>
		<div id="AprobarRC_Content">
		</div>
		<div id="CierreCaja_Content">
		</div>
		<div id="ChequesRC_Content">
		</div>
		<div id="CajaGeneral_Content">
		</div>
	</div>
</div>