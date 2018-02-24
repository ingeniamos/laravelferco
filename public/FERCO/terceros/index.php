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
	var FaltantesLoaded = false;
	//---
	var Terceros_Crear_Access = false;
	var Terceros_Modificar_Access = false;
	var Terceros_Listado_Access = false;
	var Terceros_Faltantes_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#terceros_crear").addClass("disabled");
			$("#terceros_modificar").addClass("disabled");
			$("#terceros_listado").addClass("disabled");
			$("#terceros_faltantes").addClass("disabled");
		<?php
		}
		else { ?>
			Terceros_Crear_Access = true;
			Terceros_Modificar_Access = true;
			Terceros_Listado_Access = true;
			Terceros_Faltantes_Access = true;
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
	
	$("#terceros_crear").click(function()
	{
		if (Terceros_Crear_Access == false) {
			return;
		}
		
		ChangeElement("#Terceros_Crear_Content", this)
		if (CrearLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "terceros/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Terceros_Crear_Content").html(data);
					CrearLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear", true);
	});
	
	$("#terceros_modificar").click(function()
	{
		if (Terceros_Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#Terceros_Modificar_Content", this)
		if (ModificarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "terceros/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#Terceros_Modificar_Content").html(data);
					ModificarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	
	$("#terceros_listado").click(function()
	{
		if (Terceros_Listado_Access == false) {
			return;
		}
		
		ChangeElement("#Terceros_Listado_Content", this)
		if (ListadoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "terceros/listado.php",
				async: true,
				success: function(data) 
				{
					$("#Terceros_Listado_Content").html(data);
					ListadoLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Listado", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Terceros_Crear_Content").hide();
	$("#Terceros_Modificar_Content").hide();
	$("#Terceros_Listado_Content").hide();
	$("#Terceros_FaltantesC_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Terceros") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#terceros_crear").removeClass("disabled");
								Terceros_Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#terceros_modificar").removeClass("disabled");
								Terceros_Modificar_Access = true;
			<?php
							break;
							case "Listado":
			?>
								$("#terceros_listado").removeClass("disabled");
								Terceros_Listado_Access = true;
			<?php
							break;
							case "Faltantes":
			?>
								$("#terceros_faltantes").removeClass("disabled");
								Terceros_Faltantes_Access = true;
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
			<li id="terceros_crear" style="margin-right:2px">
				Crear
			</li>
			<li id="terceros_modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="terceros_listado" style="margin-right:2px">
				Listado
			</li>
			<li id="terceros_faltantes">
				Faltantes
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Terceros_Crear_Content">
		</div>
		<div id="Terceros_Modificar_Content">
		</div>
		<div id="Terceros_Listado_Content">
		</div>
		<div id="Terceros_FaltantesC_Content">
		</div>
	</div>
</div>