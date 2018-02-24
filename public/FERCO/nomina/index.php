<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var Nomina_ExtrasLoaded = false;
	var Nomina_PrestamosLoaded = false;
	var Nomina_NovedadesLoaded = false;
	var Nomina_ModExtrasLoaded = false;
	var Nomina_ModPrestamosLoaded = false;
	var Nomina_ModNovedadesLoaded = false;
	var Nomina_AprobarExtrasLoaded = false;
	var Nomina_AprobarPrestamosLoaded = false;
	var Nomina_AprobarNovedadesLoaded = false;
	var Nomina_SalariosLoaded = false;
	var Nomina_NominaLoaded = false;
	var Nomina_BioControl_Loaded = false;
	//---
	var Extras_Access = false;
	var Prestamos_Access = false;
	var Novedades_Access = false;
	var ModExtras_Access = false;
	var ModPrestamos_Access = false;
	var ModNovedades_Access = false;
	var AprobarExtras_Access = false;
	var AprobarPrestamos_Access = false;
	var AprobarNovedades_Access = false;
	var Salarios_Access = false;
	var Nomina_Access = false;
	var BioControl_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#nomina_extras").addClass("disabled");
			$("#nomina_prestamos").addClass("disabled");
			$("#nomina_novedades").addClass("disabled");
			$("#nomina_editar_extras").addClass("disabled");
			$("#nomina_editar_novedades").addClass("disabled");
			$("#nomina_aprobar_extras").addClass("disabled");
			$("#nomina_aprobar_prestamo").addClass("disabled");
			$("#nomina_aprobar_novedad").addClass("disabled");
			$("#nomina_salarios").addClass("disabled");
			$("#nomina_nomina").addClass("disabled");
			$("#nomina_biocontrol").addClass("disabled");
		<?php
		}
		else { ?>
			Extras_Access = true;
			Prestamos_Access = true;
			Novedades_Access = true;
			ModExtras_Access = true;
			ModPrestamos_Access = true;
			ModNovedades_Access = true;
			AprobarExtras_Access = true;
			AprobarPrestamos_Access = true;
			AprobarNovedades_Access = true;
			Salarios_Access = true;
			Nomina_Access = true;
			BioControl_Access = true;
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
	
	$("#nomina_extras").click(function()
	{
		if (Extras_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_Extras_Content", this)
		if (Nomina_ExtrasLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/extras.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_Extras_Content").html(data);
					Nomina_ExtrasLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Extras", true);
	});
	
	$("#nomina_prestamos").click(function()
	{
		if (Prestamos_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_Prestamos_Content", this)
		if (Nomina_PrestamosLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/prestamos.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_Prestamos_Content").html(data);
					Nomina_PrestamosLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Prestamos", true);
	});
	
	$("#nomina_novedades").click(function()
	{
		if (Novedades_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_Novedades_Content", this)
		if (Nomina_NovedadesLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/novedades.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_Novedades_Content").html(data);
					Nomina_NovedadesLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Novedades", true);
	});
	
	$("#nomina_editar_extras").click(function()
	{
		if (ModExtras_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_ModExtras_Content", this)
		if (Nomina_ModExtrasLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/modificar_extras.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_ModExtras_Content").html(data);
					Nomina_ModExtrasLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar Extras", true);
	});
	
	$("#nomina_editar_novedades").click(function()
	{
		if (ModNovedades_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_ModNovedades_Content", this)
		if (Nomina_ModNovedadesLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/modificar_novedades.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_ModNovedades_Content").html(data);
					Nomina_ModNovedadesLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar Novedades", true);
	});
	
	$("#nomina_aprobar_extras").click(function()
	{
		if (AprobarExtras_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_AprobarExtras_Content", this)
		if (Nomina_AprobarExtrasLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/aprobar_extras.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_AprobarExtras_Content").html(data);
					Nomina_AprobarExtrasLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar Extras", true);
	});
	
	$("#nomina_aprobar_prestamo").click(function()
	{
		if (AprobarPrestamos_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_AprobarPrestamos_Content", this)
		if (Nomina_AprobarPrestamosLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/aprobar_prestamos.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_AprobarPrestamos_Content").html(data);
					Nomina_AprobarPrestamosLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar Prestamos", true);
	});
	
	$("#nomina_aprobar_novedad").click(function()
	{
		if (AprobarNovedades_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_AprobarNovedades_Content", this)
		if (Nomina_AprobarNovedadesLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/aprobar_novedades.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_AprobarNovedades_Content").html(data);
					Nomina_AprobarNovedadesLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar Novedades", true);
	});
	
	$("#nomina_salarios").click(function()
	{
		if (Salarios_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_Salarios_Content", this)
		if (Nomina_SalariosLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/salarios.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_Salarios_Content").html(data);
					Nomina_SalariosLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Salarios", true);
	});
	
	$("#nomina_nomina").click(function()
	{
		if (Nomina_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_Nomina_Content", this)
		if (Nomina_NominaLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/nomina.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_Nomina_Content").html(data);
					Nomina_NominaLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Nomina Mes", true);
	});
	
	$("#nomina_biocontrol").click(function()
	{
		if (BioControl_Access == false) {
			return;
		}
		
		ChangeElement("#Nomina_BioControl_Content", this)
		if (Nomina_BioControl_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "nomina/biocontrol.php",
				async: true,
				success: function(data) 
				{
					$("#Nomina_BioControl_Content").html(data);
					Nomina_BioControl_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("BioControl", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Nomina_Extras_Content").hide();
	$("#Nomina_Prestamos_Content").hide();
	$("#Nomina_Novedades_Content").hide();
	$("#Nomina_ModExtras_Content").hide();
	$("#Nomina_ModNovedades_Content").hide();
	$("#Nomina_AprobarExtras_Content").hide();
	$("#Nomina_AprobarPrestamos_Content").hide();
	$("#Nomina_AprobarNovedades_Content").hide();
	$("#Nomina_Salarios_Content").hide();
	$("#Nomina_Nomina_Content").hide();
	$("#Nomina_BioControl_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Nomina") {
						switch($data[$i]["SubModulo"])
						{
							case "Extras":
			?>
								$("#nomina_extras").removeClass("disabled");
								Extras_Access = true;
			<?php
							break;
							case "Prestamos":
			?>
								$("#nomina_prestamos").removeClass("disabled");
								Prestamos_Access = true;
			<?php
							break;
							case "Novedades":
			?>
								$("#nomina_novedades").removeClass("disabled");
								Novedades_Access = true;
			<?php
							break;
							case "Mod_Extras":
			?>
								$("#nomina_editar_extras").removeClass("disabled");
								ModExtras_Access = true;
			<?php
							break;
							case "Mod_Novedades":
			?>
								$("#nomina_editar_novedades").removeClass("disabled");
								ModNovedades_Access = true;
			<?php
							break;
							case "Aprobar_Extras":
			?>
								$("#nomina_aprobar_extras").removeClass("disabled");
								AprobarExtras_Access = true;
			<?php
							break;
							case "Aprobar_Prestamos":
			?>
								$("#nomina_aprobar_prestamo").removeClass("disabled");
								AprobarPrestamos_Access = true;
			<?php
							break;
							case "Aprobar_Novedades":
			?>
								$("#nomina_aprobar_novedad").removeClass("disabled");
								AprobarNovedades_Access = true;
			<?php
							break;
							case "Salarios":
			?>
								$("#nomina_salarios").removeClass("disabled");
								Salarios_Access = true;
			<?php
							break;
							case "Nomina_Mes":
			?>
								$("#nomina_nomina").removeClass("disabled");
								Nomina_Access = true;
			<?php
							break;
							case "Nomina_BioControl":
			?>
								$("#nomina_biocontrol").removeClass("disabled");
								BioControl_Access = true;
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
			<li id="nomina_extras" style="margin-right:2px">
				Extras
			</li>
			<li id="nomina_prestamos" style="margin-right:2px">
				Prestamos
			</li>
			<li id="nomina_novedades" style="margin-right:2px">
				Novedades
			</li>
			<li id="nomina_editar_novedades" style="margin-right:2px">
				Ed. Novedades
			</li>
			<li id="nomina_editar_extras" style="margin-right:2px">
				Ed. Extras
			</li>
			<li id="nomina_aprobar_prestamo" style="margin-right:2px">
				Ap. Prestamo
			</li>
			<li id="nomina_aprobar_novedad" style="margin-right:2px">
				Ap. Novedad
			</li>
			<li id="nomina_aprobar_extras" style="margin-right:2px">
				Ap. Extras
			</li>
			<li id="nomina_salarios" style="margin-right:2px">
				Salarios
			</li>
			<li id="nomina_nomina" style="margin-right:2px">
				Nomina Mes
			</li>
			<li id="nomina_biocontrol">
				BioControl
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Nomina_Extras_Content">
		</div>
		<div id="Nomina_Prestamos_Content">
		</div>
		<div id="Nomina_Novedades_Content">
		</div>
		<div id="Nomina_ModExtras_Content">
		</div>
		<div id="Nomina_ModNovedades_Content">
		</div>
		<div id="Nomina_AprobarExtras_Content">
		</div>
		<div id="Nomina_AprobarPrestamos_Content">
		</div>
		<div id="Nomina_AprobarNovedades_Content">
		</div>
		<div id="Nomina_Salarios_Content">
		</div>
		<div id="Nomina_Nomina_Content">
		</div>
		<div id="Nomina_BioControl_Content">
		</div>
	</div>
</div>