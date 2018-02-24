<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var CrearLoaded = false;
	var ModificarLoaded = false;
	var AutorizarLoaded = false;
	var DespacharLoaded = false;
	var AjustarLoaded = false;
	var Fact_Directa_Loaded = false;
	//---
	var Crear_Access = false;
	var Modificar_Access = false;
	var Autorizar_Access = false;
	var Despacho_Access = false;
	var DespachoMOD_Access = false;
	var Fact_Directa_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#crear").addClass("disabled");
			$("#modificar").addClass("disabled");
			$("#autorizar").addClass("disabled");
			$("#despachar").addClass("disabled");
			$("#modificar_despacho").addClass("disabled");
			$("#fact_directa").addClass("disabled");
		<?php
		}
		else { ?>
			Crear_Access = true;
			Modificar_Access = true;
			Autorizar_Access = true;
			Despacho_Access = true;
			DespachoMOD_Access = true;
			Fact_Directa_Access = true;
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
	
	$("#crear").click(function()
	{
		if (Crear_Access == false) {
			return;
		}
		
		ChangeElement("#Crear_Content", this)
		if (CrearLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "ventas/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Crear_Content").html(data);
					CrearLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
					WindowSize();
				}
			});
		}
		SystemMap("Crear", true);
	});
	
	$("#modificar").click(function()
	{
		if (Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#Modificar_Content", this)
		if (ModificarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "ventas/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#Modificar_Content").html(data);
					ModificarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
					WindowSize();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	
	$("#autorizar").click(function()
	{
		if (Autorizar_Access == false) {
			return;
		}
		
		ChangeElement("#Autorizar_Content", this)
		if (AutorizarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "ventas/autorizar.php",
				async: true,
				success: function(data) 
				{
					$("#Autorizar_Content").html(data);
					AutorizarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Autorizar", true);
	});
	
	$("#despachar").click(function()
	{
		if (Despacho_Access == false) {
			return;
		}
		
		ChangeElement("#Despachar_Content", this)
		if (DespacharLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "ventas/despachar.php",
				async: true,
				success: function(data) 
				{
					$("#Despachar_Content").html(data);
					DespacharLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Despacho", true);
	});
	
	$("#modificar_despacho").click(function()
	{
		if (DespachoMOD_Access == false) {
			return;
		}
		
		ChangeElement("#Ajustar_Content", this)
		if (AjustarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "ventas/ajustar.php",
				async: true,
				success: function(data) 
				{
					$("#Ajustar_Content").html(data);
					AjustarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar Despacho", true);
	});
	
	$("#fact_directa").click(function()
	{
		if (Fact_Directa_Access == false) {
			return;
		}
		
		ChangeElement("#Fact_Directa_Content", this)
		if (Fact_Directa_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "ventas/fact_directa.php",
				async: true,
				success: function(data) 
				{
					$("#Fact_Directa_Content").html(data);
					Fact_Directa_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Facturación Directa", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Crear_Content").hide();
	$("#Modificar_Content").hide();
	$("#Autorizar_Content").hide();
	$("#Despachar_Content").hide();
	$("#Ajustar_Content").hide();
	$("#Fact_Directa_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Ventas") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#crear").removeClass("disabled");
								Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#modificar").removeClass("disabled");
								Modificar_Access = true;
			<?php
							break;
							case "Autorizar_Asignar":
			?>
								$("#autorizar").removeClass("disabled");
								Autorizar_Access = true;
			<?php
							break;
							case "Despacho":
			?>
								$("#despachar").removeClass("disabled");
								Despacho_Access = true;
			<?php
							break;
							case "DespachoMod":
			?>
								$("#modificar_despacho").removeClass("disabled");
								DespachoMOD_Access = true;
			<?php
							break;
							case "Fact_Directa":
			?>
								$("#fact_directa").removeClass("disabled");
								Fact_Directa_Access = true;
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
			<li id="crear" style="margin-right:2px">
				Crear
			</li>
			<li id="modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="autorizar" style="margin-right:2px">
				Autorizar/Asignar
			</li>
			<li id="despachar" style="margin-right:2px">
				Despacho
			</li>
			<li id="modificar_despacho" style="margin-right:2px">
				Modificar Desp
			</li>
			<li id="fact_directa">
				Fact. Directa
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Crear_Content">
		</div>
		<div id="Modificar_Content">
		</div>
		<div id="Autorizar_Content">
		</div>
		<div id="Despachar_Content">
		</div>
		<div id="Ajustar_Content">
		</div>
		<div id="Fact_Directa_Content">
		</div>
	</div>
</div>