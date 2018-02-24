<?php
session_start();
require_once("../modulos/config.php");
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var Parametros_InicioLoaded = false;
	var Parametros_TercerosLoaded = false;
	var Parametros_FacturacionLoaded = false;
	var Parametros_InventarioLoaded = false;
	var Parametros_CajaLoaded = false;
	var Parametros_NominaLoaded = false;
	var Parametros_PresupuestoLoaded = false;
	//---
	var Inicio_Access = false;
	var Terceros_Access = false;
	var Facturacion_Access = false;
	var Inventario_Access = false;
	var Caja_Access = false;
	var Nomina_Access = false;
	var Presupuesto_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#parametros_inicio").addClass("disabled");
			$("#parametros_terceros").addClass("disabled");
			$("#parametros_facturacion").addClass("disabled");
			$("#parametros_inventarios").addClass("disabled");
			$("#parametros_caja").addClass("disabled");
			$("#parametros_nomina").addClass("disabled");
			$("#parametros_presupuesto").addClass("disabled");
		<?php
		}
		else { ?>
			Inicio_Access = true;
			Terceros_Access = true;
			Facturacion_Access = true;
			Inventario_Access = true;
			Caja_Access = true;
			Nomina_Access = true;
			Presupuesto_Access = true;
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
	
	$("#parametros_inicio").click(function()
	{
		if (Inicio_Access == false) {
			return;
		}
		
		ChangeElement("#Parametros_Inicio_Content", this)
		if (Parametros_InicioLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "parametros/inicio.php",
				async: true,
				success: function(data) 
				{
					$("#Parametros_Inicio_Content").html(data);
					Parametros_InicioLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Inicio", true);
	});
	
	$("#parametros_terceros").click(function()
	{
		if (Terceros_Access == false) {
			return;
		}
		
		ChangeElement("#Parametros_Terceros_Content", this)
		if (Parametros_TercerosLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "parametros/terceros.php",
				async: true,
				success: function(data) 
				{
					$("#Parametros_Terceros_Content").html(data);
					Parametros_TercerosLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Terceros", true);
	});
	
	$("#parametros_facturacion").click(function()
	{
		if (Facturacion_Access == false) {
			return;
		}
		
		ChangeElement("#Parametros_Facturacion_Content", this)
		if (Parametros_FacturacionLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "parametros/facturacion.php",
				async: true,
				success: function(data) 
				{
					$("#Parametros_Facturacion_Content").html(data);
					Parametros_FacturacionLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Facturacion", true);
	});
	
	$("#parametros_inventarios").click(function()
	{
		if (Inventario_Access == false) {
			return;
		}
		
		ChangeElement("#Parametros_Inventario_Content", this)
		if (Parametros_InventarioLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "parametros/inventario.php",
				async: true,
				success: function(data) 
				{
					$("#Parametros_Inventario_Content").html(data);
					Parametros_InventarioLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Inventario", true);
	});
	
	$("#parametros_caja").click(function()
	{
		if (Caja_Access == false) {
			return;
		}
		
		ChangeElement("#Parametros_Caja_Content", this)
		if (Parametros_CajaLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "parametros/caja.php",
				async: true,
				success: function(data) 
				{
					$("#Parametros_Caja_Content").html(data);
					Parametros_CajaLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Caja", true);
	});
	
	$("#parametros_nomina").click(function()
	{
		if (Nomina_Access == false) {
			return;
		}
		
		ChangeElement("#Parametros_Nomina_Content", this)
		if (Parametros_NominaLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "parametros/nomina.php",
				async: true,
				success: function(data) 
				{
					$("#Parametros_Nomina_Content").html(data);
					Parametros_NominaLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Nomina", true);
	});
	
	$("#parametros_presupuesto").click(function()
	{
		if (Presupuesto_Access == false) {
			return;
		}
		
		ChangeElement("#Parametros_Presupuesto_Content", this)
		if (Parametros_PresupuestoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "parametros/presupuesto.php",
				async: true,
				success: function(data) 
				{
					$("#Parametros_Presupuesto_Content").html(data);
					Parametros_PresupuestoLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Presupuesto", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Parametros_Inicio_Content").hide();
	$("#Parametros_Terceros_Content").hide();
	$("#Parametros_Facturacion_Content").hide();
	$("#Parametros_Inventario_Content").hide();
	$("#Parametros_Caja_Content").hide();
	$("#Parametros_Nomina_Content").hide();
	$("#Parametros_Presupuesto_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Parametros") {
						switch($data[$i]["SubModulo"])
						{
							case "Inicio":
			?>
								$("#parametros_inicio").removeClass("disabled");
								Inicio_Access = true;
			<?php
							break;
							case "Terceros":
			?>
								$("#parametros_terceros").removeClass("disabled");
								Terceros_Access = true;
			<?php
							break;
							case "Facturacion":
			?>
								$("#parametros_facturacion").removeClass("disabled");
								Facturacion_Access = true;
			<?php
							break;
							case "Inventario":
			?>
								$("#parametros_inventarios").removeClass("disabled");
								Inventario_Access = true;
			<?php
							break;
							case "Caja":
			?>
								$("#parametros_caja").removeClass("disabled");
								Caja_Access = true;
			<?php
							break;
							case "Nomina":
			?>
								$("#parametros_nomina").removeClass("disabled");
								Nomina_Access = true;
			<?php
							break;
							case "Presupuesto":
			?>
								$("#parametros_presupuesto").removeClass("disabled");
								Presupuesto_Access = true;
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
			<li id="parametros_inicio" style="margin-right:2px">
				Inicio
			</li>
			<?php 
			if (isset($TERCEROS) && $TERCEROS == true)
			{
			?>
			<li id="parametros_terceros" style="margin-right:2px">
				Terceros
			</li>
			<?php 
			}
			
			if (isset($VENTAS) && $VENTAS == true)
			{
			?>
			<li id="parametros_facturacion" style="margin-right:2px">
				Facturacion
			</li>
			<?php 
			}
			
			if (isset($INVENTARIO) && $INVENTARIO == true)
			{
			?>
			<li id="parametros_inventarios" style="margin-right:2px">
				Inventario
			</li>
			<?php 
			}
			
			if (isset($CAJA) && $CAJA == true)
			{
			?>
			<li id="parametros_caja" style="margin-right:2px">
				Caja
			</li>
			<?php 
			}
			
			if (isset($NOMINA) && $NOMINA == true)
			{
			?>
			<li id="parametros_nomina" style="margin-right:2px">
				Nomina
			</li>
			<?php 
			}
			
			if (isset($PRESUPUESTO) && $PRESUPUESTO == true)
			{
			?>
			<li id="parametros_presupuesto">
				Presupuesto
			</li>
			<?php 
			}
			?>
		</ul>
	</div>
	<div id="Content">
		<div id="Parametros_Inicio_Content">
		</div>
		<div id="Parametros_Terceros_Content">
		</div>
		<div id="Parametros_Facturacion_Content">
		</div>
		<div id="Parametros_Inventario_Content">
		</div>
		<div id="Parametros_Caja_Content">
		</div>
		<div id="Parametros_Nomina_Content">
		</div>
		<div id="Parametros_Presupuesto_Content">
		</div>
	</div>
</div>