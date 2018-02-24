<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var Presupuesto__Loaded = false;
	var Presupuesto_Lista_Loaded = false;
	var APU_Loaded = false;
	var Lista_APU_Loaded = false;
	var MO_Loaded = false;
	var Lista_MO_Loaded = false;
	var EQU_Loaded = false;
	var Lista_EQU_Loaded = false;
	var MAT_Loaded = false;
	var Lista_MAT_Loaded = false;
	var IMPORT_Loaded = false;
	//---
	var Presupuesto__Access = false;
	var Presupuesto_Lista_Access = false;
	var Presupuesto_APU_Access = false;
	var Presupuesto_Lista_APU_Access = false;
	var Presupuesto_MO_Access = false;
	var Presupuesto_Lista_MO_Access = false;
	var Presupuesto_EQU_Access = false;
	var Presupuesto_Lista_EQU_Access = false;
	var Presupuesto_MAT_Access = false;
	var Presupuesto_Lista_MAT_Access = false;
	var Presupuesto_IMPORT_Access = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		//--- Disable from init
		if ($_SESSION["UserAccess"][0]["Lvl"] != "Administrador") { ?>
			$("#presupuesto_presupuesto").addClass("disabled");
			$("#presupuesto_lista").addClass("disabled");
			$("#presupuesto_apu").addClass("disabled");
			$("#presupuesto_lista_apu").addClass("disabled");
			$("#presupuesto_mo").addClass("disabled");
			$("#presupuesto_lista_mo").addClass("disabled");
			$("#presupuesto_equ").addClass("disabled");
			$("#presupuesto_lista_equ").addClass("disabled");
			$("#presupuesto_mat").addClass("disabled");
			$("#presupuesto_lista_mat").addClass("disabled");
			$("#presupuesto_import").addClass("disabled");
		<?php
		}
		else { ?>
			Presupuesto__Access = true;
			Presupuesto_Lista_Access = true;
			Presupuesto_APU_Access = true;
			Presupuesto_Lista_APU_Access = true;
			Presupuesto_MO_Access = true;
			Presupuesto_Lista_MO_Access = true;
			Presupuesto_EQU_Access = true;
			Presupuesto_Lista_EQU_Access = true;
			Presupuesto_MAT_Access = true;
			Presupuesto_Lista_MAT_Access = true;
			Presupuesto_IMPORT_Access = true;
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
	
	$("#presupuesto_presupuesto").click(function()
	{
		if (Presupuesto__Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto__Content", this)
		if (Presupuesto__Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/presupuesto.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto__Content").html(data);
					Presupuesto__Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Crear Presupuesto", true);
	});
	
	$("#presupuesto_lista").click(function()
	{
		if (Presupuesto_Lista_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_Lista_Content", this)
		if (Presupuesto_Lista_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/lista_presupuesto.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_Lista_Content").html(data);
					Presupuesto_Lista_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Lista Presupuesto", true);
	});
	
	$("#presupuesto_apu").click(function()
	{
		if (Presupuesto_APU_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_APU_Content", this)
		if (APU_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/apu.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_APU_Content").html(data);
					APU_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("APU", true);
	});
	
	$("#presupuesto_lista_apu").click(function()
	{
		if (Presupuesto_Lista_APU_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_Lista_APU_Content", this)
		if (Lista_APU_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/lista_apu.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_Lista_APU_Content").html(data);
					Lista_APU_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Lista APU", true);
	});
	
	$("#presupuesto_mo").click(function()
	{
		if (Presupuesto_MO_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_MO_Content", this)
		if (MO_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/mano_de_obra.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_MO_Content").html(data);
					MO_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Mano de Obra", true);
	});
	
	$("#presupuesto_lista_mo").click(function()
	{
		if (Presupuesto_Lista_MO_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_Lista_MO_Content", this)
		if (Lista_MO_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/lista_mano_de_obra.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_Lista_MO_Content").html(data);
					Lista_MO_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Lista Mano de Obra", true);
	});
	
	$("#presupuesto_equ").click(function()
	{
		if (Presupuesto_EQU_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_EQU_Content", this)
		if (EQU_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/equipos.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_EQU_Content").html(data);
					EQU_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Equipos", true);
	});
	
	$("#presupuesto_lista_equ").click(function()
	{
		if (Presupuesto_Lista_EQU_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_Lista_EQU_Content", this)
		if (Lista_EQU_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/lista_equipos.php",
				success: function(data) 
				{
					$("#Presupuesto_Lista_EQU_Content").html(data);
					Lista_EQU_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Lista Equipos", true);
	});
	
	$("#presupuesto_mat").click(function()
	{
		if (Presupuesto_MAT_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_MAT_Content", this)
		if (MAT_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/materiales.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_MAT_Content").html(data);
					MAT_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Materiales", true);
	});
	
	$("#presupuesto_lista_mat").click(function()
	{
		if (Presupuesto_Lista_MAT_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_Lista_MAT_Content", this)
		if (Lista_MAT_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/lista_materiales.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_Lista_MAT_Content").html(data);
					Lista_MAT_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Lista Materiales", true);
	});
	
	$("#presupuesto_import").click(function()
	{
		if (Presupuesto_IMPORT_Access == false) {
			return;
		}
		
		ChangeElement("#Presupuesto_IMPORT_Content", this)
		if (IMPORT_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "presupuesto/import.php",
				async: true,
				success: function(data) 
				{
					$("#Presupuesto_IMPORT_Content").html(data);
					IMPORT_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Importar", true);
	});
	
	// Start Hidden
	$("#Loading").hide();
	$("#Presupuesto__Content").hide();
	$("#Presupuesto_Lista_Content").hide();
	$("#Presupuesto_APU_Content").hide();
	$("#Presupuesto_Lista_APU_Content").hide();
	$("#Presupuesto_MO_Content").hide();
	$("#Presupuesto_Lista_MO_Content").hide();
	$("#Presupuesto_EQU_Content").hide();
	$("#Presupuesto_Lista_EQU_Content").hide();
	$("#Presupuesto_MAT_Content").hide();
	$("#Presupuesto_Lista_MAT_Content").hide();
	$("#Presupuesto_IMPORT_Content").hide();
	<?php
		if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
		{
			$data = $_SESSION["UserAccess"];
			$num = count($data);
			
			if ($data[0]["Lvl"] != "Administrador") {
				for ($i = 0; $i < $num; $i++)
				{
					if ($data[$i]["Modulo"] == "Presupuesto") {
						switch($data[$i]["SubModulo"])
						{
							case "Presupuesto":
			?>
								$("#presupuesto_presupuesto").removeClass("disabled");
								Presupuesto__Access = true;
			<?php
							break;
							case "Lista_Presupuesto":
			?>
								$("#presupuesto_lista_presupuesto").removeClass("disabled");
								Presupuesto_Lista_Access = true;
			<?php
							break;
							case "APU":
			?>
								$("#presupuesto_apu").removeClass("disabled");
								Presupuesto_APU_Access = true;
			<?php
							break;
							case "Lista_APU":
			?>
								$("#presupuesto_lista_apu").removeClass("disabled");
								Presupuesto_Lista_APU_Access = true;
			<?php
							break;
							case "Mano_de_Obra":
			?>
								$("#presupuesto_mo").removeClass("disabled");
								Presupuesto_MO_Access = true;
			<?php
							break;
							case "Lista_Mano_de_Obra":
			?>
								$("#presupuesto_lista_mo").removeClass("disabled");
								Presupuesto_Lista_MO_Access = true;
			<?php
							break;
							case "Equipos":
			?>
								$("#presupuesto_equ").removeClass("disabled");
								Presupuesto_EQU_Access = true;
			<?php
							break;
							case "Lista_Equipos":
			?>
								$("#presupuesto_lista_equ").removeClass("disabled");
								Presupuesto_Lista_EQU_Access = true;
			<?php
							break;
							case "Materiales":
			?>
								$("#presupuesto_mat").removeClass("disabled");
								Presupuesto_MAT_Access = true;
			<?php
							break;
							case "Lista_Materiales":
			?>
								$("#presupuesto_lista_mat").removeClass("disabled");
								Presupuesto_Lista_MAT_Access = true;
			<?php
							break;
							case "Importar":
			?>
								$("#presupuesto_import").removeClass("disabled");
								Presupuesto_EQU_Access = true;
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
			<li id="presupuesto_presupuesto" style="margin-right:2px">
				Presupuesto
			</li>
			<li id="presupuesto_lista" style="margin-right:2px">
				Lista Presupuesto
			</li>
			<li id="presupuesto_apu" style="margin-right:2px">
				APU
			</li>
			<li id="presupuesto_lista_apu" style="margin-right:2px">
				Lista APU
			</li>
			<li id="presupuesto_mat" style="margin-right:2px">
				Materiales
			</li>
			<li id="presupuesto_lista_mat" style="margin-right:2px">
				Lista Materiales
			</li>
			<li id="presupuesto_mo" style="margin-right:2px">
				Mano de Obra
			</li>
			<li id="presupuesto_lista_mo" style="margin-right:2px">
				Lista Mano de Obra
			</li>
			<li id="presupuesto_equ" style="margin-right:2px">
				Equipos
			</li>
			<li id="presupuesto_lista_equ" style="margin-right:2px">
				Lista Equipos
			</li>
			<li id="presupuesto_import">
				Importar
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Presupuesto__Content">
		</div>
		<div id="Presupuesto_Lista_Content">
		</div>
		<div id="Presupuesto_APU_Content">
		</div>
		<div id="Presupuesto_Lista_APU_Content">
		</div>
		<div id="Presupuesto_MO_Content">
		</div>
		<div id="Presupuesto_Lista_MO_Content">
		</div>
		<div id="Presupuesto_EQU_Content">
		</div>
		<div id="Presupuesto_Lista_EQU_Content">
		</div>
		<div id="Presupuesto_MAT_Content">
		</div>
		<div id="Presupuesto_Lista_MAT_Content">
		</div>
		<div id="Presupuesto_IMPORT_Content">
		</div>
	</div>
</div>
