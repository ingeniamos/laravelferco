<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var CxP_CrearLoaded = false;
	var CxP_ModificarLoaded = false;
	var CxP_MovimientosLoaded = false;
	var CxP_AplicarLoaded = false;
	var CxP_ListadoLoaded = false;
	var CxP_EdadesLoaded = false;
	//---
	var CxP_Crear_Access = false;
	var CxP_Modificar_Access = false;
	var CxP_Movimientos_Access = false;
	var CxP_Aplicar_Access = false;
	var CxP_Listado_Access = false;
	var CxP_Edades_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#cxp_crear").addClass("disabled");
			$("#cxp_modificar").addClass("disabled");
			$("#cxp_movimientos").addClass("disabled");
			$("#cxp_aplicar").addClass("disabled");
			$("#cxp_listado").addClass("disabled");
			$("#cxp_edades").addClass("disabled");
		<?php
		}
		else { ?>
			CxP_Crear_Access = true;
			CxP_Modificar_Access = true;
			CxP_Movimientos_Access = true;
			CxP_Aplicar_Access = true;
			CxP_Listado_Access = true;
			CxP_Edades_Access = true;
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
	
	$("#cxp_crear").click(function()
	{
		if (CxP_Crear_Access == false) {
			return;
		}
		
		ChangeElement("#CxP_Crear_Content", this)
		if (CxP_CrearLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cxp/crear.php",
				async: true,
				success: function(data) 
				{
					$("#CxP_Crear_Content").html(data);
					CxP_CrearLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear", true);
	});
	
	$("#cxp_modificar").click(function()
	{
		if (CxP_Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#CxP_Modificar_Content", this)
		if (CxP_ModificarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cxp/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#CxP_Modificar_Content").html(data);
					CxP_ModificarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	
	$("#cxp_movimientos").click(function()
	{
		if (CxP_Movimientos_Access == false) {
			return;
		}
		
		ChangeElement("#CxP_Movimientos_Content", this)
		if (CxP_MovimientosLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cxp/movimientos.php",
				async: true,
				success: function(data) 
				{
					$("#CxP_Movimientos_Content").html(data);
					CxP_MovimientosLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Movimientos", true);
	});
	
	$("#cxp_aplicar").click(function()
	{
		if (CxP_Aplicar_Access == false) {
			return;
		}
		
		ChangeElement("#CxP_Aplicar_Content", this)
		if (CxP_AplicarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cxp/aplicar.php",
				async: true,
				success: function(data) 
				{
					$("#CxP_Aplicar_Content").html(data);
					CxP_AplicarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aplicar Pagos", true);
	});
	
	$("#cxp_listado").click(function()
	{
		if (CxP_Listado_Access == false) {
			return;
		}
		
		ChangeElement("#CxP_Listado_Content", this)
		if (CxP_ListadoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cxp/listado.php",
				async: true,
				success: function(data) 
				{
					$("#CxP_Listado_Content").html(data);
					CxP_ListadoLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Listado", true);
	});
	
	$("#cxp_edades").click(function()
	{
		if (CxP_Edades_Access == false) {
			return;
		}
		
		ChangeElement("#CxP_Edades_Content", this)
		if (CxP_EdadesLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cxp/edades.php",
				async: true,
				success: function(data) 
				{
					$("#CxP_Edades_Content").html(data);
					CxP_EdadesLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Edades", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#CxP_Crear_Content").hide();
	$("#CxP_Modificar_Content").hide();
	$("#CxP_Movimientos_Content").hide();
	$("#CxP_Aplicar_Content").hide();
	$("#CxP_Listado_Content").hide();
	$("#CxP_Edades_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "CxP") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#cxp_crear").removeClass("disabled");
								CxP_Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#cxp_modificar").removeClass("disabled");
								CxP_Modificar_Access = true;
			<?php
							break;
							case "Movimientos":
			?>
								$("#cxp_movimientos").removeClass("disabled");
								CxP_Movimientos_Access = true;
			<?php
							break;
							case "Aplicar":
			?>
								$("#cxp_aplicar").removeClass("disabled");
								CxP_Aplicar_Access = true;
			<?php
							break;
							case "Listado":
			?>
								$("#buscarRC").removeClass("disabled");
								CxP_Listado_Access = true;
			<?php
							break;
							case "Edades":
			?>
								$("#cxp_edades").removeClass("disabled");
								CxP_Edades_Access = true;
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
			<li id="cxp_crear" style="margin-right:2px">
				Crear
			</li>
			<li id="cxp_modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="cxp_movimientos" style="margin-right:2px">
				Movimientos
			</li>
			<li id="cxp_aplicar" style="margin-right:2px">
				Aplicar Pagos
			</li>
			<li id="cxp_listado" style="margin-right:2px">
				Listado
			</li>
			<li id="cxp_edades">
				Edades
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="CxP_Crear_Content">
		</div>
		<div id="CxP_Modificar_Content">
		</div>
		<div id="CxP_Movimientos_Content">
		</div>
		<div id="CxP_Aplicar_Content">
		</div>
		<div id="CxP_Listado_Content">
		</div>
		<div id="CxP_Edades_Content">
		</div>
	</div>
</div>