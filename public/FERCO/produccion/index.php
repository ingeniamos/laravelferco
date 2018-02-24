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
	var AprobarLoaded = false;
	var TrefiladoLoaded = false;
	var EnderezadoLoaded = false;
	var ElectroSoldadoLoaded = false;
	var FiguradoLoaded = false;
	var ConsultarLoaded = false;
	var ProcesosLoaded = false;
	//---
	var Crear_Access = false;
	var Modificar_Access = false;
	var Aprobar_Access = false;
	var Trefilado_Access = false;
	var Enderezado_Access = false;
	var Electrosoldado_Access = false;
	var Figurado_Access = false;
	var Consultar_Access = false;
	var Procesos_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#produccion_crear").addClass("disabled");
			$("#produccion_modificar").addClass("disabled");
			$("#produccion_aprobar").addClass("disabled");
			$("#produccion_trefilado").addClass("disabled");
			$("#produccion_enderezado").addClass("disabled");
			$("#produccion_electrosoldado").addClass("disabled");
			$("#produccion_figurado").addClass("disabled");
			$("#produccion_consultar").addClass("disabled");
			$("#produccion_procesos").addClass("disabled");
		<?php
		}
		else { ?>
			Crear_Access = true;
			Modificar_Access = true;
			Aprobar_Access = true;
			Trefilado_Access = true;
			Enderezado_Access = true;
			Electrosoldado_Access = true;
			Figurado_Access = true;
			Consultar_Access = true;
			Procesos_Access = true;
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
	
	$("#produccion_crear").click(function()
	{
		if (Crear_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_Crear_Content", this)
		if (CrearLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/crear.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_Crear_Content").html(data);
					CrearLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear", true);
	});
	
	$("#produccion_modificar").click(function()
	{
		if (Modificar_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_Modificar_Content", this)
		if (ModificarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/modificar.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_Modificar_Content").html(data);
					ModificarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Modificar", true);
	});
	
	$("#produccion_aprobar").click(function()
	{
		if (Aprobar_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_Aprobar_Content", this)
		if (AprobarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/aprobar.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_Aprobar_Content").html(data);
					AprobarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Aprobar", true);
	});
	
	$("#produccion_trefilado").click(function()
	{
		if (Trefilado_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_Trefilado_Content", this)
		if (TrefiladoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/trefilado.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_Trefilado_Content").html(data);
					TrefiladoLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
					WindowSize();
				}
			});
		}
		SystemMap("Trefilado", true);
	});
	
	$("#produccion_enderezado").click(function()
	{
		if (Enderezado_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_Enderezado_Content", this)
		if (EnderezadoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/enderezado.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_Enderezado_Content").html(data);
					EnderezadoLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
					WindowSize();
				}
			});
		}
		SystemMap("Corte y Enderezado", true);
	});
	
	$("#produccion_electrosoldado").click(function()
	{
		if (Electrosoldado_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_ElectroSoldado_Content", this)
		if (ElectroSoldadoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/electrosoldado.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_ElectroSoldado_Content").html(data);
					ElectroSoldadoLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
					WindowSize();
				}
			});
		}
		SystemMap("Electrosoldado", true);
	});
	
	$("#produccion_figurado").click(function()
	{
		if (Figurado_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_Figurado_Content", this)
		if (FiguradoLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/figurado.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_Figurado_Content").html(data);
					FiguradoLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
					WindowSize();
				}
			});
		}
		SystemMap("Figurado", true);
	});
	
	$("#produccion_consultar").click(function()
	{
		if (Consultar_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_Consultar_Content", this)
		if (ConsultarLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/consultar.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_Consultar_Content").html(data);
					ConsultarLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Consultar", true);
	});
	
	$("#produccion_procesos").click(function()
	{
		if (Procesos_Access == false) {
			return;
		}
		
		ChangeElement("#Produccion_Procesos_Content", this)
		if (ProcesosLoaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "produccion/procesos.php",
				async: true,
				success: function(data) 
				{
					$("#Produccion_Procesos_Content").html(data);
					ProcesosLoaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Procesos", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Produccion_Crear_Content").hide();
	$("#Produccion_Modificar_Content").hide();
	$("#Produccion_Aprobar_Content").hide();
	$("#Produccion_Trefilado_Content").hide();
	$("#Produccion_Enderezado_Content").hide();
	$("#Produccion_ElectroSoldado_Content").hide();
	$("#Produccion_Figurado_Content").hide();
	$("#Produccion_Consultar_Content").hide();
	$("#Produccion_Procesos_Content").hide();
	
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Produccion") {
						switch($data[$i]["SubModulo"])
						{
							case "Crear":
			?>
								$("#produccion_crear").removeClass("disabled");
								Crear_Access = true;
			<?php
							break;
							case "Modificar":
			?>
								$("#produccion_modificar").removeClass("disabled");
								Modificar_Access = true;
			<?php
							break;
							case "Aprobar":
			?>
								$("#produccion_aprobar").removeClass("disabled");
								Aprobar_Access = true;
			<?php
							break;
							case "Trefilado":
			?>
								$("#produccion_trefilado").removeClass("disabled");
								Trefilado_Access = true;
			<?php
							break;
							case "Corte y Enderezado":
			?>
								$("#produccion_enderezado").removeClass("disabled");
								Enderezado_Access = true;
			<?php
							break;
							case "Electrosoldado":
			?>
								$("#produccion_electrosoldado").removeClass("disabled");
								Electrosoldado_Access = true;
			<?php
							break;
							case "Figurado":
			?>
								$("#produccion_figurado").removeClass("disabled");
								Figurado_Access = true;
			<?php
							break;
							case "Consultar":
			?>
								$("#produccion_consultar").removeClass("disabled");
								Consultar_Access = true;
			<?php
							break;
							case "Procesos":
			?>
								$("#produccion_procesos").removeClass("disabled");
								Procesos_Access = true;
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
			<li id="produccion_crear" style="margin-right:2px">
				Crear
			</li>
			<li id="produccion_modificar" style="margin-right:2px">
				Modificar
			</li>
			<li id="produccion_aprobar" style="margin-right:2px">
				Aprobar/Listado
			</li>
			<li id="produccion_trefilado" style="margin-right:2px">
				Trefilado
			</li>
			<li id="produccion_enderezado" style="margin-right:2px">
				Corte y End.
			</li>
			<li id="produccion_electrosoldado" style="margin-right:2px">
				ElectroSoldado
			</li>
			<li id="produccion_figurado" style="margin-right:2px">
				Figurado
			</li>
			<li id="produccion_consultar" style="margin-right:2px">
				Consultar
			</li>
			<li id="produccion_procesos" style="margin-right:2px">
				Procesos
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Produccion_Crear_Content">
		</div>
		<div id="Produccion_Modificar_Content">
		</div>
		<div id="Produccion_Aprobar_Content">
		</div>
		<div id="Produccion_Trefilado_Content">
		</div>
		<div id="Produccion_Enderezado_Content">
		</div>
		<div id="Produccion_ElectroSoldado_Content">
		</div>
		<div id="Produccion_Figurado_Content">
		</div>
		<div id="Produccion_Consultar_Content">
		</div>
		<div id="Produccion_Procesos_Content">
		</div>
	</div>
</div>