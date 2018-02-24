<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var Figuracion_Crear_Loaded = false;
	var Figuracion_Modificar_Loaded = false;
	var Figuracion_Aprobar_Loaded = false;
	var Figuracion_Visualizar_Loaded = false;
	var Figuracion_Resumen_Loaded = false;
	var Figuracion_Figuras_Loaded = false;
	var Figuracion_Hierros_Loaded = false;
	var Figuracion_Mallas_Loaded = false;
	//---
	var Figuracion_Crear_Access = false;
	var Figuracion_Modificar_Access = false;
	var Figuracion_Aprobar_Access = false;
	var Figuracion_Visualizar_Access = false;
	var Figuracion_Resumen_Access = false;
	var Figuracion_Figuras_Access = false;
	var Figuracion_Hierros_Access = false;
	var Figuracion_Mallas_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#figuracion_crear").addClass("disabled");
			$("#figuracion_modificar").addClass("disabled");
			$("#figuracion_aprobar").addClass("disabled");
			$("#figuracion_visualizar").addClass("disabled");
			$("#figuracion_resumen").addClass("disabled");
			$("#figuracion_figuras").addClass("disabled");
			$("#figuracion_hierros").addClass("disabled");
			$("#figuracion_mallas").addClass("disabled");
		<?php
		}
		else { ?>
			Figuracion_Crear_Access = true;
			Figuracion_Modificar_Access = true;
			Figuracion_Aprobar_Access = true;
			Figuracion_Visualizar_Access = true;
			Figuracion_Resumen_Access = true;
			Figuracion_Figuras_Access = true;
			Figuracion_Hierros_Access = true;
			Figuracion_Mallas_Access = true;
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
	
	$("#figuracion_crear").click(function()
	{
		if (Figuracion_Crear_Access == false) {
			return;
		}
		
		ChangeElement("#Figuracion_Crear_Content", this)
		if (Figuracion_Crear_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "figuracion/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Figuracion_Crear_Content").html(data);
					Figuracion_Crear_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear", true);
	});
	
	$("#figuracion_modificar").click(function()
	{
		if (Figuracion_Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#Figuracion_Modificar_Content", this)
		if (Figuracion_Modificar_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "figuracion/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#Figuracion_Modificar_Content").html(data);
					Figuracion_Modificar_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	
	$("#figuracion_aprobar").click(function()
	{
		if (Figuracion_Aprobar_Access == false) {
			return;
		}
		
		ChangeElement("#Figuracion_Aprobar_Content", this)
		if (Figuracion_Aprobar_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "figuracion/aprobar.php",
				async: true,
				success: function(data) 
				{
					$("#Figuracion_Aprobar_Content").html(data);
					Figuracion_Aprobar_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar", true);
	});
	
	$("#figuracion_visualizar").click(function()
	{
		if (Figuracion_Visualizar_Access == false) {
			return;
		}
		
		ChangeElement("#Figuracion_Visualizar_Content", this)
		if (Figuracion_Visualizar_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "figuracion/visualizar.php",
				async: true,
				success: function(data) 
				{
					$("#Figuracion_Visualizar_Content").html(data);
					Figuracion_Visualizar_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Editar", true);
	});
	
	$("#figuracion_resumen").click(function()
	{
		if (Figuracion_Resumen_Access == false) {
			return;
		}
		
		ChangeElement("#Figuracion_Resumen_Content", this)
		if (Figuracion_Resumen_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "figuracion/resumen.php",
				async: true,
				success: function(data) 
				{
					$("#Figuracion_Resumen_Content").html(data);
					Figuracion_Resumen_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Resumen", true);
	});
	
	$("#figuracion_figuras").click(function()
	{
		if (Figuracion_Figuras_Access == false) {
			return;
		}
		
		ChangeElement("#Figuracion_Figuras_Content", this)
		if (Figuracion_Figuras_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "figuracion/figuras.php",
				async: true,
				success: function(data) 
				{
					$("#Figuracion_Figuras_Content").html(data);
					Figuracion_Figuras_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Figuras", true);
	});
	
	$("#figuracion_hierros").click(function()
	{
		if (Figuracion_Hierros_Access == false) {
			return;
		}
		
		ChangeElement("#Figuracion_Hierros_Content", this)
		if (Figuracion_Hierros_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "figuracion/hierros.php",
				async: true,
				success: function(data) 
				{
					$("#Figuracion_Hierros_Content").html(data);
					Figuracion_Hierros_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Hierros", true);
	});

	$("#figuracion_mallas").click(function()
	{
		if (Figuracion_Mallas_Access == false) {
			return;
		}
		
		ChangeElement("#Figuracion_Mallas_Content", this)
		if (Figuracion_Mallas_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "figuracion/mallas.php",
				async: true,
				success: function(data) 
				{
					$("#Figuracion_Mallas_Content").html(data);
					Figuracion_Mallas_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Mallas", true);
	});
		
	// Start Hidden
	$("#Loading").hide();
	$("#Figuracion_Crear_Content").hide();
	$("#Figuracion_Modificar_Content").hide();
	$("#Figuracion_Aprobar_Content").hide();
	$("#Figuracion_Visualizar_Content").hide();
	$("#Figuracion_Resumen_Content").hide();
	$("#Figuracion_Figuras_Content").hide();
	$("#Figuracion_Hierros_Content").hide();
	$("#Figuracion_Mallas_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Figuracion") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#figuracion_crear").removeClass("disabled");
								Figuracion_Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#figuracion_modificar").removeClass("disabled");
								Figuracion_Modificar_Access = true;
			<?php
							break;
							case "Aprobar":
			?>
								$("#figuracion_aprobar").removeClass("disabled");
								Figuracion_Aprobar_Access = true;
			<?php
							break;
							case "Editar":
			?>
								$("#figuracion_visualizar").removeClass("disabled");
								Figuracion_Visualizar_Access = true;
			<?php
							break;
							case "Resumen":
			?>
								$("#figuracion_resumen").removeClass("disabled");
								Figuracion_Resumen_Access = true;
			<?php
							break;
							case "Figuras":
			?>
								$("#figuracion_figuras").removeClass("disabled");
								Figuracion_Figuras_Access = true;
			<?php
							break;
							case "Hierros":
			?>
								$("#figuracion_hierros").removeClass("disabled");
								Figuracion_Hierros_Access = true;
			<?php
							break;
							case "Mallas":
			?>
								$("#figuracion_mallas").removeClass("disabled");
								Figuracion_Mallas_Access = true;
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
			<li id="figuracion_crear" style="margin-right:2px">
				Crear
			</li>
			<li id="figuracion_modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="figuracion_aprobar" style="margin-right:2px">
				Aprobar
			</li>
			<li id="figuracion_visualizar" style="margin-right:2px">
				Editar
			</li>
			<li id="figuracion_resumen" style="margin-right:2px">
				Resumen
			</li>
			<li id="figuracion_figuras" style="margin-right:2px">
				Figuras
			</li>
			<li id="figuracion_hierros" style="margin-right:2px">
				Hierros
			</li>
			<li id="figuracion_mallas" style="background: red;">
				Mallas
			</li>			
		</ul>
	</div>
	<div id="Content">
		<div id="Figuracion_Crear_Content">
		</div>
		<div id="Figuracion_Modificar_Content">
		</div>
		<div id="Figuracion_Aprobar_Content">
		</div>
		<div id="Figuracion_Visualizar_Content">
		</div>
		<div id="Figuracion_Resumen_Content">
		</div>
		<div id="Figuracion_Figuras_Content">
		</div>
		<div id="Figuracion_Hierros_Content">
		</div>
		<div id="Figuracion_Mallas_Content">
		</div>		
	</div>
</div>