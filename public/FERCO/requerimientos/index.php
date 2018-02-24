<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var Requerimientos_Crear_Loaded = false;
	var Requerimientos_Modificar_Loaded = false;
	var Requerimientos_Listado_Loaded = false;
	var Requerimientos_Compras_Loaded = false;
	var Requerimientos_Compras_Mod_Loaded = false;
	var Requerimientos_Aprobar_Loaded = false;
	var Requerimientos_Productos_Loaded = false;
	var Requerimientos_Inventario_Loaded = false;
	//---
	var Requerimientos_Crear_Access = false;
	var Requerimientos_Modificar_Access = false;
	var Requerimientos_Listado_Access = false;
	var Requerimientos_Compras_Access = false;
	var Requerimientos_Compras_Mod_Access = false;
	var Requerimientos_Aprobar_Access = false;
	var Requerimientos_Productos_Access = false;
	var Requerimientos_Inventario_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#requerimientos_crear").addClass("disabled");
			$("#requerimientos_modificar").addClass("disabled");
			$("#requerimientos_listado").addClass("disabled");
			$("#requerimientos_compras").addClass("disabled");
			$("#requerimientos_compras_mod").addClass("disabled");
			$("#requerimientos_aprobar").addClass("disabled");
			$("#requerimientos_productos").addClass("disabled");
			$("#requerimientos_inventario").addClass("disabled");
		<?php
		}
		else { ?>
			Requerimientos_Crear_Access = true;
			Requerimientos_Modificar_Access = true;
			Requerimientos_Listado_Access = true;
			Requerimientos_Compras_Access = true;
			Requerimientos_Compras_Mod_Access = true;
			Requerimientos_Aprobar_Access = true;
			Requerimientos_Productos_Access = true;
			Requerimientos_Inventario_Access = true;
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
	
	$("#requerimientos_crear").click(function()
	{
		if (Requerimientos_Crear_Access == false) {
			return;
		}
		
		ChangeElement("#Requerimientos_Crear", this)
		if (Requerimientos_Crear_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "requerimientos/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Requerimientos_Crear").html(data);
					Requerimientos_Crear_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear Solicitud", true);
	});
	
	$("#requerimientos_modificar").click(function()
	{
		if (Requerimientos_Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#Requerimientos_Modificar", this)
		if (Requerimientos_Modificar_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "requerimientos/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#Requerimientos_Modificar").html(data);
					Requerimientos_Modificar_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar Solicitud", true);
	});
	
	$("#requerimientos_listado").click(function()
	{
		if (Requerimientos_Listado_Access == false) {
			return;
		}
		
		ChangeElement("#Requerimientos_Listado", this)
		if (Requerimientos_Listado_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "requerimientos/listado_solicitud.php",
				async: true,
				success: function(data) 
				{
					$("#Requerimientos_Listado").html(data);
					Requerimientos_Listado_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Listado", true);
	});
	
	$("#requerimientos_compras").click(function()
	{
		if (Requerimientos_Compras_Access == false) {
			return;
		}
		
		ChangeElement("#Requerimientos_Compras", this)
		if (Requerimientos_Compras_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "requerimientos/compras.php",
				async: true,
				success: function(data) 
				{
					$("#Requerimientos_Compras").html(data);
					Requerimientos_Compras_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Compras", true);
	});
	
	$("#requerimientos_compras_mod").click(function()
	{
		if (Requerimientos_Compras_Mod_Access == false) {
			return;
		}
		
		ChangeElement("#Requerimientos_Compras_Mod", this)
		if (Requerimientos_Compras_Mod_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "requerimientos/compras_mod.php",
				async: true,
				success: function(data) 
				{
					$("#Requerimientos_Compras_Mod").html(data);
					Requerimientos_Compras_Mod_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Compras Modificar", true);
	});
	
	$("#requerimientos_aprobar").click(function()
	{
		if (Requerimientos_Aprobar_Access == false) {
			return;
		}
		
		ChangeElement("#Requerimientos_Aprobar", this)
		if (Requerimientos_Aprobar_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "requerimientos/listado_compras.php",
				async: true,
				success: function(data) 
				{
					$("#Requerimientos_Aprobar").html(data);
					Requerimientos_Aprobar_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar", true);
	});
	
	$("#requerimientos_productos").click(function()
	{
		if (Requerimientos_Productos_Access == false) {
			return;
		}
		
		ChangeElement("#Requerimientos_Productos", this)
		if (Requerimientos_Productos_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "requerimientos/productos.php",
				async: true,
				success: function(data) 
				{
					$("#Requerimientos_Productos").html(data);
					Requerimientos_Productos_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Productos", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Requerimientos_Crear").hide();
	$("#Requerimientos_Modificar").hide();
	$("#Requerimientos_Listado").hide();
	$("#Requerimientos_Compras").hide();
	$("#Requerimientos_Compras_Mod").hide();
	$("#Requerimientos_Aprobar").hide();
	$("#Requerimientos_Productos").hide();
	$("#Requerimientos_Inventario").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador")
			{
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Requerimientos") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#requerimientos_crear").removeClass("disabled");
								Requerimientos_Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#requerimientos_modificar").removeClass("disabled");
								Requerimientos_Modificar_Access = true;
			<?php
							break;
							case "Listado":
			?>
								$("#requerimientos_listado").removeClass("disabled");
								Requerimientos_Listado_Access = true;
			<?php
							break;
							case "Compras":
			?>
								$("#requerimientos_compras").removeClass("disabled");
								Requerimientos_Compras_Access = true;
			<?php
							break;
							case "Compras_Mod":
			?>
								$("#requerimientos_compras_mod").removeClass("disabled");
								Requerimientos_Compras_Mod_Access = true;
			<?php
							break;
							case "Aprobar":
			?>
								$("#requerimientos_aprobar").removeClass("disabled");
								Requerimientos_Aprobar_Access = true;
			<?php
							break;
							case "Productos":
			?>
								$("#requerimientos_productos").removeClass("disabled");
								Requerimientos_Productos_Access = true;
			<?php
							break;
							case "Inventario":
			?>
								$("#requerimientos_inventario").removeClass("disabled");
								Requerimientos_Inventario_Access = true;
			<?php
							break;
						}
					}
				}
			}
		}
	?>
});
</script>
<div id="Container2">
	<div id="Tabs">
		<ul>
			<li id="requerimientos_crear" style="margin-right:2px">
				Crear
			</li>
			<li id="requerimientos_modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="requerimientos_listado" style="margin-right:2px">
				Listado
			</li>
			<li id="requerimientos_compras" style="margin-right:2px">
				Compras
			</li>
			<li id="requerimientos_compras_mod" style="margin-right:2px">
				Compras Mod
			</li>
			<li id="requerimientos_aprobar" style="margin-right:2px">
				Aprobar
			</li>
			<li id="requerimientos_productos" style="margin-right:2px">
				Productos
			</li>
			<li id="requerimientos_inventario">
				Inventario
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Requerimientos_Crear">
		</div>
		<div id="Requerimientos_Modificar">
		</div>
		<div id="Requerimientos_Listado">
		</div>
		<div id="Requerimientos_Compras">
		</div>
		<div id="Requerimientos_Compras_Mod">
		</div>
		<div id="Requerimientos_Aprobar">
		</div>
		<div id="Requerimientos_Productos">
		</div>
		<div id="Requerimientos_Inventario">
		</div>
	</div>
</div>
