<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var Reportes_CarteraLoaded = false;
	var Reportes_Cartera_EdadesLoaded = false;
	var Reportes_FacturacionLoaded = false;
	var Reportes_Ventas_MovLoaded = false;
	var Reportes_CajaLoaded = false;
	var Reportes_Maquinaria_Loaded = false;
	var Reportes_InventarioLoaded = false;
	var Reportes_KardexLoaded = false;
	var Reportes_ComprasLoaded = false;
	var Reportes_Compras_MovLoaded = false;
	var Reportes_CarnetLoaded = false;
	//---
	var Cartera_Access = false;
	var Cartera_Edades_Access = false;
	var Facturacion_Access = false;
	var Ventas_Mov_Access = false;
	var Caja_Access = false;
	var Maquinaria_Access = false;
	var Inventario_Access = false;
	var Kardex_Access = false;
	var Compras_Access = false;
	var Compras_Mov_Access = false;
	var Carnet_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#reportes_cartera").addClass("disabled");
			$("#reportes_edades").addClass("disabled");
			$("#reportes_facturacion").addClass("disabled");
			$("#reportes_ventas_mov").addClass("disabled");
			$("#reportes_caja").addClass("disabled");
			$("#reportes_maquinaria").addClass("disabled");
			$("#reportes_inventario").addClass("disabled");
			$("#reportes_kardex").addClass("disabled");
			$("#reportes_compras").addClass("disabled");
			$("#reportes_compras_mov").addClass("disabled");
			$("#reportes_carnet").addClass("disabled");
		<?php
		}
		else { ?>
			Cartera_Access = true;
			Cartera_Edades_Access = true;
			Facturacion_Access = true;
			Ventas_Mov_Access = true;
			Caja_Access = true;
			Maquinaria_Access = true;
			Inventario_Access = true;
			Kardex_Access = true;
			Compras_Access = true;
			Compras_Mov_Access = true;
			Carnet_Access = true;
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
	
	$("#reportes_cartera").click(function()
	{
		if (Cartera_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Cartera_Content", this)
		if (Reportes_CarteraLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/cartera.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Cartera_Content").html(data);
					Reportes_CarteraLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Cartera", true);
	});
	
	$("#reportes_edades").click(function()
	{
		if (Cartera_Edades_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Cartera_Edades_Content", this)
		if (Reportes_Cartera_EdadesLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/edades.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Cartera_Edades_Content").html(data);
					Reportes_Cartera_EdadesLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Edades", true);
	});
	
	$("#reportes_facturacion").click(function()
	{
		if (Facturacion_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Facturacion_Content", this)
		if (Reportes_FacturacionLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/ventas.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Facturacion_Content").html(data);
					Reportes_FacturacionLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Facturacion", true);
	});
	
	$("#reportes_ventas_mov").click(function()
	{
		if (Ventas_Mov_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Ventas_Mov_Content", this)
		if (Reportes_Ventas_MovLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/ventas_mov.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Ventas_Mov_Content").html(data);
					Reportes_Ventas_MovLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Ventas Mov.", true);
	});
	
	$("#reportes_caja").click(function()
	{
		if (Caja_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Caja_Content", this)
		if (Reportes_CajaLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/caja.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Caja_Content").html(data);
					Reportes_CajaLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Caja", true);
	});
	
	$("#reportes_maquinaria").click(function()
	{
		if (Maquinaria_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Maquinaria_Content", this)
		if (Reportes_Maquinaria_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/maquinaria.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Maquinaria_Content").html(data);
					Reportes_Maquinaria_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Maquinaria", true);
	});
	
	$("#reportes_inventario").click(function()
	{
		if (Inventario_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Inventario_Content", this)
		if (Reportes_InventarioLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/inventario.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Inventario_Content").html(data);
					Reportes_InventarioLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Inventario", true);
	});
	
	$("#reportes_kardex").click(function()
	{
		if (Kardex_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Kardex_Content", this)
		if (Reportes_KardexLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/kardex.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Kardex_Content").html(data);
					Reportes_KardexLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Kardex", true);
	});
	
	$("#reportes_compras").click(function()
	{
		if (Compras_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Compras_Content", this)
		if (Reportes_ComprasLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/compras.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Compras_Content").html(data);
					Reportes_ComprasLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Compras", true);
	});
	
	$("#reportes_compras_mov").click(function()
	{
		if (Compras_Mov_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Compras_Mov_Content", this)
		if (Reportes_Compras_MovLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/compras_mov.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Compras_Mov_Content").html(data);
					Reportes_Compras_MovLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Compras Mov.", true);
	});
	
	$("#reportes_carnet").click(function()
	{
		if (Carnet_Access == false) {
			return;
		}
		
		ChangeElement("#Reportes_Carnet_Content", this)
		if (Reportes_CarnetLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "reportes/carnet.php",
				async: true,
				success: function(data) 
				{
					$("#Reportes_Carnet_Content").html(data);
					Reportes_CarnetLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Carnet", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Reportes_Cartera_Content").hide();
	$("#Reportes_Cartera_Edades_Content").hide();
	$("#Reportes_Facturacion_Content").hide();
	$("#Reportes_Ventas_Mov_Content").hide();
	$("#Reportes_Caja_Content").hide();
	$("#Reportes_Maquinaria_Content").hide();
	$("#Reportes_Inventario_Content").hide();
	$("#Reportes_Kardex_Content").hide();
	$("#Reportes_Compras_Content").hide();
	$("#Reportes_Compras_Mov_Content").hide();
	$("#Reportes_Carnet_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Reportes") {
						switch($data[$i]["SubModulo"])
						{
							case "Cartera":
			?>
								$("#reportes_cartera").removeClass("disabled");
								Cartera_Access = true;
			<?php
							break;
							case "Cartera_Edades":
			?>
								$("#reportes_edades").removeClass("disabled");
								Cartera_Edades_Access = true;
			<?php
							break;
							case "Facturacion":
			?>
								$("#reportes_facturacion").removeClass("disabled");
								Facturacion_Access = true;
			<?php
							break;
							case "Ventas_Mov":
			?>
								$("#reportes_ventas_mov").removeClass("disabled");
								Ventas_Mov_Access = true;
			<?php
							break;
							case "Caja":
			?>
								$("#reportes_caja").removeClass("disabled");
								Caja_Access = true;
			<?php
							break;
							case "Maquinaria":
			?>
								$("#reportes_maquinaria").removeClass("disabled");
								Maquinaria_Access = true;
			<?php
							break;
							case "Inventario":
			?>
								$("#reportes_inventario").removeClass("disabled");
								Inventarioa_Access = true;
			<?php
							break;
							case "Kardex":
			?>
								$("#reportes_kardex").removeClass("disabled");
								Kardex_Access = true;
			<?php
							break;
							case "Compras":
			?>
								$("#reportes_compras").removeClass("disabled");
								Compras_Access = true;
			<?php
							break;
							case "Compras_Mov":
			?>
								$("#reportes_compras_mov").removeClass("disabled");
								Compras_Mov_Access = true;
			<?php
							break;
							case "Carnet":
			?>
								$("#reportes_carnet").removeClass("disabled");
								Carnet_Access = true;
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
			<li id="reportes_cartera" style="margin-right:2px">
				Cartera
			</li>
			<li id="reportes_edades" style="margin-right:2px">
				Cartera Edades
			</li>
			<li id="reportes_facturacion" style="margin-right:2px">
				Facturacion
			</li>
			<li id="reportes_ventas_mov" style="margin-right:2px">
				Ventas Mov.
			</li>
			<li id="reportes_caja" style="margin-right:2px">
				Caja
			</li>
			<li id="reportes_maquinaria" style="margin-right:2px">
				Maquinaria
			</li>
			<li id="reportes_inventario" style="margin-right:2px">
				Inventario
			</li>
			<li id="reportes_kardex" style="margin-right:2px">
				Kardex
			</li>
			<li id="reportes_compras" style="margin-right:2px">
				Compras
			</li>
			<li id="reportes_compras_mov" style="margin-right:2px">
				Compras Mov.
			</li>
			<li id="reportes_carnet">
				Carnet
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Reportes_Cartera_Content">
		</div>
		<div id="Reportes_Cartera_Edades_Content">
		</div>
		<div id="Reportes_Facturacion_Content">
		</div>
		<div id="Reportes_Ventas_Mov_Content">
		</div>
		<div id="Reportes_Caja_Content">
		</div>
		<div id="Reportes_Maquinaria_Content">
		</div>
		<div id="Reportes_Inventario_Content">
		</div>
		<div id="Reportes_Kardex_Content">
		</div>
		<div id="Reportes_Compras_Content">
		</div>
		<div id="Reportes_Compras_Mov_Content">
		</div>
		<div id="Reportes_Carnet_Content">
		</div>
	</div>
</div>