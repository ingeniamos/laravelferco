<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var AplicarLoaded = false;
	var MovimientosLoaded = false;
	var ListadoLoaded = false;
	//---
	var Cartera_Aplicar_Access = false;
	var Cartera_Movimientos_Access = false;
	var Cartera_Listado_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#aplicar").addClass("disabled");
			$("#movimientos").addClass("disabled");
			$("#listado").addClass("disabled");
		<?php
		}
		else { ?>
			Cartera_Aplicar_Access = true;
			Cartera_Movimientos_Access = true;
			Cartera_Listado_Access = true;
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
	
	$("#aplicar").click(function()
	{
		if (Cartera_Aplicar_Access == false) {
			return;
		}
		
		ChangeElement("#Aplicar_Content", this)
		if (AplicarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cartera/aplicar.php",
				async: true,
				success: function(data) 
				{
					$("#Aplicar_Content").html(data);
					AplicarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aplicar", true);
	});
	$("#movimientos").click(function()
	{
		if (Cartera_Movimientos_Access == false) {
			return;
		}
		
		ChangeElement("#Movimientos_Content", this)
		if (MovimientosLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cartera/movimientos.php",
				async: true,
				success: function(data) 
				{
					$("#Movimientos_Content").html(data);
					MovimientosLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Movimientos", true);
	});
	$("#listado").click(function()
	{
		if (Cartera_Listado_Access == false) {
			return;
		}
		
		ChangeElement("#Listado_Content", this)
		if (ListadoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "cartera/listado.php",
				async: true,
				success: function(data) 
				{
					$("#Listado_Content").html(data);
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
	$("#Aplicar_Content").hide();
	$("#Movimientos_Content").hide();
	$("#Listado_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Cartera") {
						switch($data[$i]["SubModulo"])
						{
							case "Aplicar":
			?>
								$("#aplicar").removeClass("disabled");
								Cartera_Aplicar_Access = true;
			<?php
							break;
							case "Movimientos":
			?>
								$("#movimientos").removeClass("disabled");
								Cartera_Movimientos_Access = true;
			<?php
							break;
							case "Listado":
			?>
								$("#listado").removeClass("disabled");
								Cartera_Listado_Access = true;
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
			<li id="aplicar" style="margin-right:2px">
				Aplicar Pagos
			</li>
			<li id="movimientos" style="margin-right:2px">
				Movimientos
			</li>
			<li id="listado">
				Listado
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Aplicar_Content">
		</div>
		<div id="Movimientos_Content">
		</div>
		<div id="Listado_Content">
		</div>
	</div>
</div>