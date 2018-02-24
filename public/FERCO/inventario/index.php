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
	var ListadoLoaded = false;
	var ExistenciasLoaded = false;
	var RecientesLoaded = false;
	var Ajustar_MovsLoaded = false;
	var Ajustar_PreciosLoaded = false;
	var Importar_Loaded = false;
	//---
	var Inventario_Crear_Access = false;
	var Inventario_Modificar_Access = false;
	var Inventario_Listado_Access = false;
	var Inventario_Existencias_Access = false;
	var Inventario_Recientes_Access = false;
	var Inventario_Ajustar_Movs_Access = false;
	var Inventario_Ajustar_Precios_Access = false;
	var Inventario_Importar_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#inventario_crear").addClass("disabled");
			$("#inventario_modificar").addClass("disabled");
			$("#inventario_listado").addClass("disabled");
			$("#inventario_existencias").addClass("disabled");
			$("#inventario_recientes").addClass("disabled");
			$("#inventario_ajustar_movs").addClass("disabled");
			$("#inventario_ajustar_precios").addClass("disabled");
			$("#inventario_importar").addClass("disabled");
		<?php
		}
		else { ?>
			Inventario_Crear_Access = true;
			Inventario_Modificar_Access = true;
			Inventario_Listado_Access = true;
			Inventario_Existencias_Access = true;
			Inventario_Recientes_Access = true;
			Inventario_Ajustar_Movs_Access = true;
			Inventario_Ajustar_Precios_Access = true;
			Inventario_Importar_Access = true;
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
	
	$("#inventario_crear").click(function()
	{
		if (Inventario_Crear_Access == false) {
			return;
		}
		
		ChangeElement("#Inventario_Crear_Content", this)
		if (CrearLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inventario/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Inventario_Crear_Content").html(data);
					CrearLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear", true);
	});
	
	$("#inventario_modificar").click(function()
	{
		if (Inventario_Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#Inventario_Modificar_Content", this)
		if (ModificarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inventario/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#Inventario_Modificar_Content").html(data);
					ModificarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	
	$("#inventario_listado").click(function()
	{
		if (Inventario_Listado_Access == false) {
			return;
		}
		
		ChangeElement("#Inventario_Listado_Content", this)
		if (ListadoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inventario/listado.php",
				async: true,
				success: function(data) 
				{
					$("#Inventario_Listado_Content").html(data);
					ListadoLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Listado", true);
	});
	
	$("#inventario_existencias").click(function()
	{
		if (Inventario_Existencias_Access == false) {
			return;
		}
		
		ChangeElement("#Inventario_Existencias_Content", this)
		if (ExistenciasLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inventario/existencias.php",
				async: true,
				success: function(data) 
				{
					$("#Inventario_Existencias_Content").html(data);
					ExistenciasLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Existencias", true);
	});
	
	$("#inventario_recientes").click(function()
	{
		if (Inventario_Recientes_Access == false) {
			return;
		}
		
		ChangeElement("#Inventario_Recientes_Content", this)
		if (RecientesLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inventario/recientes.php",
				async: true,
				success: function(data) 
				{
					$("#Inventario_Recientes_Content").html(data);
					RecientesLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Movimientos Recientes", true);
	});
	
	$("#inventario_ajustar_movs").click(function()
	{
		if (Inventario_Ajustar_Movs_Access == false) {
			return;
		}
		
		ChangeElement("#Inventario_Ajustar_Movs_Content", this)
		if (Ajustar_MovsLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inventario/ajustar_movs.php",
				async: true,
				success: function(data) 
				{
					$("#Inventario_Ajustar_Movs_Content").html(data);
					Ajustar_MovsLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Ajustar Movimientos", true);
	});
	
	$("#inventario_ajustar_precios").click(function()
	{
		if (Inventario_Ajustar_Precios_Access == false) {
			return;
		}
		
		ChangeElement("#Inventario_Ajustar_Precios_Content", this)
		if (Ajustar_PreciosLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inventario/ajustar_precios.php",
				async: true,
				success: function(data) 
				{
					$("#Inventario_Ajustar_Precios_Content").html(data);
					Ajustar_PreciosLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Ajustar Precios", true);
	});
	
	$("#inventario_importar").click(function()
	{
		if (Inventario_Importar_Access == false) {
			return;
		}
		
		ChangeElement("#Inventario_Importar_Content", this)
		if (Importar_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inventario/import.php",
				async: true,
				success: function(data) 
				{
					$("#Inventario_Importar_Content").html(data);
					Importar_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Importar Productos", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Inventario_Crear_Content").hide();
	$("#Inventario_Modificar_Content").hide();
	$("#Inventario_Listado_Content").hide();
	$("#Inventario_Existencias_Content").hide();
	$("#Inventario_Recientes_Content").hide();
	$("#Inventario_Ajustar_Movs_Content").hide();
	$("#Inventario_Ajustar_Precios_Content").hide();
	$("#Inventario_Importar_Content").hide();
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Inventario") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#inventario_crear").removeClass("disabled");
								Inventario_Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#inventario_modificar").removeClass("disabled");
								Inventario_Modificar_Access = true;
			<?php
							break;
							case "Listado":
			?>
								$("#inventario_listado").removeClass("disabled");
								Inventario_Listado_Access = true;
			<?php
							break;
							case "Existencias":
			?>
								$("#inventario_existencias").removeClass("disabled");
								Inventario_Existencias_Access = true;
			<?php
							break;
							case "Recientes":
			?>
								$("#inventario_recientes").removeClass("disabled");
								Inventario_Recientes_Access = true;
			<?php
							break;
							case "Ajustar Movimientos":
			?>
								$("#inventario_ajustar_movs").removeClass("disabled");
								Inventario_Ajustar_Movs_Access = true;
			<?php
							break;
							case "Ajustar Precios":
			?>
								$("#inventario_ajustar_precios").removeClass("disabled");
								Inventario_Ajustar_Precios_Access = true;
			<?php
							break;
							case "Importar":
			?>
								$("#inventario_importar").removeClass("disabled");
								Inventario_Importar_Access = true;
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
			<li id="inventario_crear" style="margin-right:2px">
				Crear
			</li>
			<li id="inventario_modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="inventario_listado" style="margin-right:2px">
				Listado <!-- Union de Grupo y Listado (Inv Inicial No se Necesita) -->
			</li>
			<li id="inventario_existencias" style="margin-right:2px">
				Existencias
			</li>
			<li id="inventario_recientes" style="margin-right:2px">
				Recientes
			</li>
			<li id="inventario_ajustar_movs" style="margin-right:2px">
				Ajustar Movs
			</li>
			<li id="inventario_ajustar_precios" style="margin-right:2px">
				Ajustar Precios
			</li>
			<li id="inventario_importar">
				Importar Productos
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Inventario_Crear_Content">
		</div>
		<div id="Inventario_Modificar_Content">
		</div>
		<div id="Inventario_Listado_Content">
		</div>
		<div id="Inventario_Existencias_Content">
		</div>
		<div id="Inventario_Recientes_Content">
		</div>
		<div id="Inventario_Ajustar_Movs_Content">
		</div>
		<div id="Inventario_Ajustar_Precios_Content">
		</div>
		<div id="Inventario_Importar_Content">
		</div>
	</div>
</div>