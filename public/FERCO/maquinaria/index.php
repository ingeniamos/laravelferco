<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var Maquinaria_Crear_Loaded = false
	var Maquinaria_Modificar_Loaded = false
	var Maquinaria_Aprobar_Loaded = false
	var Maquinaria_Repuestos_Loaded = false
	//---
	var Crear_Access = false;
	var Modificar_Access = false;
	var Aprobar_Access = false;
	var Repuestos_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#maquinaria_crear").addClass("disabled");
			$("#maquinaria_modificar").addClass("disabled");
			$("#maquinaria_aprobar").addClass("disabled");
			$("#maquinaria_repuestos").addClass("disabled");
		<?php
		}
		else
		{ ?>
			Crear_Access = true;
			Modificar_Access = true;
			Aprobar_Access = true;
			Repuestos_Access = true;
		<?php
		} 
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
	
	$("#maquinaria_crear").click(function()
	{
		if (Crear_Access == false) {
			return;
		}
		
		ChangeElement("#Maquinaria_Crear_Content", this)
		if (Maquinaria_Crear_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "maquinaria/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Maquinaria_Crear_Content").html(data);
					Maquinaria_Crear_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear", true);
	});
	
	$("#maquinaria_modificar").click(function()
	{
		if (Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#Maquinaria_Modificar_Content", this)
		if (Maquinaria_Modificar_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "maquinaria/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#Maquinaria_Modificar_Content").html(data);
					Maquinaria_Modificar_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	
	$("#maquinaria_aprobar").click(function()
	{
		if (Aprobar_Access == false) {
			return;
		}
		
		ChangeElement("#Maquinaria_Aprobar_Content", this)
		if (Maquinaria_Aprobar_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "maquinaria/aprobar.php",
				async: true,
				success: function(data) 
				{
					$("#Maquinaria_Aprobar_Content").html(data);
					Maquinaria_Aprobar_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar", true);
	});
	
	$("#maquinaria_repuestos").click(function()
	{
		if (Repuestos_Access == false) {
			return;
		}
		
		ChangeElement("#Maquinaria_Repuestos_Content", this)
		if (Maquinaria_Repuestos_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "maquinaria/repuestos.php",
				async: true,
				success: function(data) 
				{
					$("#Maquinaria_Repuestos_Content").html(data);
					Maquinaria_Repuestos_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Repuestos", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Maquinaria_Crear_Content").hide();
	$("#Maquinaria_Modificar_Content").hide();
	$("#Maquinaria_Aprobar_Content").hide();
	$("#Maquinaria_Repuestos_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Maquinaria") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#maquinaria_crear").removeClass("disabled");
								Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#maquinaria_modificar").removeClass("disabled");
								Modificar_Access = true;
			<?php
							break;
							case "Aprobar":
			?>
								$("#maquinaria_aprobar").removeClass("disabled");
								Aprobar_Access = true;
			<?php
							break;
							case "Repuestos":
			?>
								$("#maquinaria_repuestos").removeClass("disabled");
								Repuestos_Access = true;
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
			<li id="maquinaria_crear" style="margin-right:2px">
				Crear
			</li>
			<li id="maquinaria_modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="maquinaria_aprobar" style="margin-right:2px">
				Aprobar
			</li>
			<li id="maquinaria_repuestos">
				Repuestos
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Maquinaria_Crear_Content">
		</div>
		<div id="Maquinaria_Modificar_Content">
		</div>
		<div id="Maquinaria_Aprobar_Content">
		</div>
		<div id="Maquinaria_Repuestos_Content">
		</div>
	</div>
</div>