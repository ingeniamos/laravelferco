<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{
	// ------- GLOBAL VARIABLES
	var LastActive1 = "";
	var LastActive2 = "";
	var Ausencias_Loaded = false;
	var Requerimientos_Loaded = false;
	
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
	
	// Not Finish Yet...
	$("#inicio_tardansas").addClass("disabled");
	$("#inicio_estados").addClass("disabled");
	$("#inicio_requerimientos").addClass("disabled");
	
	$("#inicio_ausencias").click(function()
	{
		ChangeElement("#Inicio_Ausencias", this)
		if (Ausencias_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inicio/ausencias.php",
				async: true,
				success: function(data) 
				{
					$("#Inicio_Ausencias").html(data);
					Ausencias_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Funcionarios Ausentes", true);
	});
	
	/*$("#inicio_requerimientos").click(function()
	{
		ChangeElement("#Inicio_Requerimientos", this)
		if (Requerimientos_Loaded == false)
		{
			$("#Loading").show();
			$.ajax({
				type:"POST",
				url: "inicio/requerimientos.php",
				async: true,
				success: function(data) 
				{
					$("#Inicio_Requerimientos").html(data);
					Requerimientos_Loaded = true;
				},
				complete: function(){
					$("#Loading").hide();
				}
			});
		}
		SystemMap("Requerimientos", true);
	});*/
	
	// Start Hidden
	$("#Loading").hide();
	$("#Inicio_Ausencias").hide();
	$("#Inicio_Tardansas").hide();
	$("#Inicio_Estados").hide();
	$("#Inicio_Requerimientos").hide();
});
</script>
<div id="Container2">
	<div id="Tabs">
		<ul>
			<li id="inicio_ausencias" style="margin-right:2px">
				Ausencias
			</li>
			<li id="inicio_tardansas" style="margin-right:2px">
				Llegadas Tardes
			</li>
			<li id="inicio_estados" style="margin-right:2px">
				Estado Maquinas/Vehiculos
			</li>
			<li id="inicio_requerimientos">
				Requerimientos
			</li>
		</ul>
	</div>
	<div id="Content">
		<div id="Inicio_Ausencias">
		</div>
		<div id="Inicio_Tardansas">
		</div>
		<div id="Inicio_Estados">
		</div>
		<div id="Inicio_Requerimientos">
		</div>
	</div>
</div>
