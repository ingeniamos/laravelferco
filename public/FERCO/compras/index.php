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
	var IngresarLoaded = false;
	var AjustesLoaded = false;
	//---
	var Compras_Crear_Access = false;
	var Compras_Modificar_Access = false;
	var Compras_Aprobar_Access = false;
	var Compras_Ingresar_Access = false;
	var Compras_Ajustes_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#compras_crear").addClass("disabled");
			$("#compras_modificar").addClass("disabled");
			$("#compras_autorizar").addClass("disabled");
			$("#compras_ingresar").addClass("disabled");
			$("#compras_ajustes").addClass("disabled");
		<?php
		}
		else { ?>
			Compras_Crear_Access = true;
			Compras_Modificar_Access = true;
			Compras_Aprobar_Access = true;
			Compras_Ingresar_Access = true;
			Compras_Ajustes_Access = true;
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
	
	$("#compras_crear").click(function()
	{
		if (Compras_Crear_Access == false) {
			return;
		}
		
		ChangeElement("#Compras_Crear_Content", this)
		if (CrearLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "compras/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Compras_Crear_Content").html(data);
					CrearLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear", true);
	});
	$("#compras_modificar").click(function()
	{
		if (Compras_Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#Compras_Modificar_Content", this)
		if (ModificarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "compras/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#Compras_Modificar_Content").html(data);
					ModificarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	$("#compras_autorizar").click(function()
	{
		if (Compras_Aprobar_Access == false) {
			return;
		}
		
		ChangeElement("#Compras_Autorizar_Content", this)
		if (AutorizarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "compras/aprobar.php",
				async: true,
				success: function(data) 
				{
					$("#Compras_Autorizar_Content").html(data);
					AutorizarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar", true);
	});
	$("#compras_ingresar").click(function()
	{
		if (Compras_Ingresar_Access == false) {
			return;
		}
		
		ChangeElement("#Compras_Ingresar_Content", this)
		if (IngresarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "compras/ingresar.php",
				async: true,
				success: function(data) 
				{
					$("#Compras_Ingresar_Content").html(data);
					IngresarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Ingresar", true);
	});
	$("#compras_ajustes").click(function()
	{
		if (Compras_Ajustes_Access == false) {
			return;
		}
		
		ChangeElement("#Compras_Ajustes_Content", this)
		if (AjustesLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "compras/ajustes.php",
				async: true,
				success: function(data) 
				{
					$("#Compras_Ajustes_Content").html(data);
					AjustesLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Ajustes", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Compras_Crear_Content").hide();
	$("#Compras_Modificar_Content").hide();
	$("#Compras_Autorizar_Content").hide();
	$("#Compras_Ingresar_Content").hide();
	$("#Compras_Ajustes_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Compras") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#compras_crear").removeClass("disabled");
								Compras_Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#compras_modificar").removeClass("disabled");
								Compras_Modificar_Access = true;
			<?php
							break;
							case "Aprobar_Asignar":
			?>
								$("#compras_autorizar").removeClass("disabled");
								Compras_Aprobar_Access = true;
			<?php
							break;
							case "Ingresar":
			?>
								$("#compras_ingresar").removeClass("disabled");
								Compras_Ingresar_Access = true;
			<?php
							break;
							case "Ajustar":
			?>
								$("#compras_ajustes").removeClass("disabled");
								Compras_Ajustes_Access = true;
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
			<li id="compras_crear" style="margin-right:2px">
				Crear
			</li>
			<li id="compras_modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="compras_autorizar" style="margin-right:2px">
				Aprobar/Asignar
			</li>
			<li id="compras_ingresar" style="margin-right:2px">
				Ingresar
			</li>
			<li id="compras_ajustes">
				Ajustes
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Compras_Crear_Content">
		</div>
		<div id="Compras_Modificar_Content">
		</div>
		<div id="Compras_Autorizar_Content">
		</div>
		<div id="Compras_Ingresar_Content">
		</div>
		<div id="Compras_Ajustes_Content">
		</div>
	</div>
</div>