<?php
session_start();
$Cartilla = isset($_POST["Cartilla"]) ? $_POST["Cartilla"]:"";
if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
{
	$_SESSION["NumOfID"]++;
}
$NumOfID = $_SESSION["NumOfID"];
?>
<script type="text/javascript">
$(document).ready(function ()
{
	var mytheme = "energyblue";
	
	//---- GLOBAL VARIABLES
	var Locked = false;
	var OnSelect = false;
	var SelectedRow = -1;
	var ClienteID = "";
	var FiguraID = "";
	var Characters = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P"];
	var kArray = new Array();
	var DesperdicioArray = new Array();
	var DimensionesArray = new Array();
	var EstriboArray = new Array();
	var SemiCirculoArray = new Array();
	var CircularArray = new Array();
	var VueltasArray = new Array();
	var ImagenArray = new Array();
	var PesoArray = new Array();
	var pesoA = 0;
	var pesoB = 0;
	var cantidad = 0;
	var ancho = 0;
	var largo = 0;
	var cuadr1 = 0;
	var cuadr2 = 0;
	var arrA = 0;
	var finA = 0;
	var arrB = 0;
	var finB = 0;
	var ref = "";
	var varillaA = 0;
	var varillaB = 0;
	var varAR = 0;
	var varBR = 0;
	var pesoParcial = 0;
	var tipoMalla = "";
	var Producto = "";
	var Producto1 = "";
	var hierroSelect = "";
	var totalM = 0;
	var totalMF = 0;
	var canvas = document.getElementById("canvas");		
    var ctx = canvas.getContext("2d");
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Figuracion_Content");
	var Body = document.getElementById("Figuracion_Mallas_Content");
	var Times = 0;
	var Refresh = 0;
	var Hide = 0;
	
	function CheckRefresh ()
	{
		clearInterval(Refresh);
		Refresh = setInterval(function()
		{
			if (Times <= 0 && (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none")) {
				Times++;
			}
			else if (Times > 0 && jQuery.css( Body, "display" ) === "block" && jQuery.css( Main, "display" ) === "block")
			{
				//Code... do something?
				SystemMap("Mallas", true);
				ReDefine();
				LoadParameters();
				//---
				clearInterval(Refresh);
				CheckHide();
			}
		},500);
	};
	
	function CheckHide ()
	{
		clearInterval(Hide);
		Hide = setInterval(function()
		{
			if (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none") {
				CheckRefresh();
				clearInterval(Hide);
			}
		},200);
	};
	// END - Code for Refresh Data
	
	//---
	var Guardar = false;
	var Imprimir = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador")
		{
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Mallas" && $data[$i]["Guardar"] == "true")
				{
		?>
					Guardar = true;
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Figuracion" && $data[$i]["SubModulo"] == "Mallas" && $data[$i]["Imprimir"] == "true")
				{
		?>
					Imprimir = true;
		<?php
				}
			}
		}
		else
		{
		?>
			Admin = true;
		<?php	
		}
	} ?>
	
	//-------------------------------------------------------------------------------------------------//
	//------------------------------------------- KEY JUMPS -------------------------------------------//
	//-------------------------------------------------------------------------------------------------//
	$("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
			$("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox("focus");
	});
	$("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
			$("#figuracion_mallas_obra<?php echo $NumOfID; ?>").jqxInput("focus");
	});
	$("#figuracion_mallas_obra<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
			$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox("focus");
	});
	$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
			$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("focus");
	});
	$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
		{
			$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").jqxNumberInput("focus");
			var input = $("#figuracion_mallas_cantidad<?php echo $NumOfID; ?> input")[0];
			if ("selectionStart" in input)
				input.setSelectionRange(0, 0);
			else
			{
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd("character", 0);
				range.moveStart("character", 0);
				range.select();
			}
		}
	});
	$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
			$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxInput("focus");
	});
	$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").keyup(function(event) {
		if(event.which == 13)
			$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxInput("focus");
	});	
	// $("#figuracion_mallas_ubicacion<?php echo $NumOfID; ?>").keyup(function(event) {
		// if(event.which == 13)
			// AddRow(true);
	// });
	
	function ReDefine()
	{
		ClearJSON = [
			//-- part 1
			{id:"figuracion_mallas_interno<?php echo $NumOfID; ?>", type:""},
			{id:"figuracion_mallas_venta_interno<?php echo $NumOfID; ?>", type:""},
			{id:"figuracion_mallas_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_obra<?php echo $NumOfID; ?>", type:""},			
			//-- part 2
			{id:"figuracion_mallas_cant<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_varA<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_varB<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pesoA<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pesoB<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pesoT<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pesoFT<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_ref<?php echo $NumOfID; ?>", type:""},
			{id:"figuracion_mallas_pelos1<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pelos2<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pelos3<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pelos4<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_codigo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_producto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_codigo1<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_producto1<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_cantidad<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_cantidad1<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			//-- part 3
			{id:"figuracion_mallas_dimensiones<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_mallas_items_grid<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_mallas_items_grid2<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_mallas_items_grid3<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_mallas_longitud<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_peso<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_total<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_desperdicio<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
		];
		
		EnableDisableJSON = [
			//-- part 1
			{id:"figuracion_mallas_cliente<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_obra<?php echo $NumOfID; ?>", type:""},
			//-- part 2
			{id:"figuracion_mallas_cant<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_varA<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_varB<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pesoA<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pesoB<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pesoT<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pesoFT<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_ref<?php echo $NumOfID; ?>", type:""},
			{id:"figuracion_mallas_pelos1<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pelos2<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pelos3<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_pelos4<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_codigo<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_producto<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_codigo1<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_producto1<?php echo $NumOfID; ?>", type:"jqxComboBox"},
			{id:"figuracion_mallas_cantidad<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_cantidad1<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
			{id:"figuracion_mallas_add<?php echo $NumOfID; ?>", type:"jqxButton"},
			{id:"figuracion_mallas_unselect<?php echo $NumOfID; ?>", type:"jqxButton"},
			//-- part 3
			{id:"figuracion_mallas_dimensiones<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_mallas_items_grid<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_mallas_items_grid2<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_mallas_items_grid3<?php echo $NumOfID; ?>", type:"jqxGrid"},
		];
	}
	
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Locked = false;
		OnSelect = false;
		SelectedRow = -1;
		Orden_Produccion = "";
		ClienteID = "";
		FiguraID = "";
		
		//---
		ClearAll();
		EnableDisableAll(false);
		
		if (!Admin && !Guardar)
		{
			$("#figuracion_mallas_guardar<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
			$("#figuracion_mallas_nuevo<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
			$("#figuracion_mallas_venta<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		}
		
		if (!Admin && !Imprimir)
		{
			$("#figuracion_mallas_imprimir1<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
			$("#figuracion_mallas_imprimir2<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
			$("#figuracion_mallas_imprimir3<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		}
	}
	
	function mallaUnHide(){
		$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
	}
	
	function mallaHide(){
		$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: false });
	}
	
	function borrarCanvas() {		
		ctx.clearRect(0, 0, 200, 200);
	}
	
	function obtener(){
		ancho = $("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val();
		largo = $("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val();
		cuadr1 = $("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val();
		cuadr2 = $("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val();
		arrA = $("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val();
		finA = $("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val();
		arrB = $("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val();
		finB = $("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val();
		ref = $("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val();
	}
	
	function calcularPeso(prod){
		pesoA = kArray[Producto.value];
		pesoB = kArray[Producto1.value];
		var pesoAT = ancho*varillaA*pesoA/100;
		var pesoBT = largo*varillaB*pesoB/100;
		if(tipoMalla=="Especial"){
			if(hierroSelect.match(/.*esp.*/)){
				$("#figuracion_mallas_pesoA<?php echo $NumOfID; ?>").val(pesoAT);
				$("#figuracion_mallas_pesoB<?php echo $NumOfID; ?>").val(pesoBT);
				totalM = pesoAT+pesoBT;
				$("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").val(totalM);
				totalMF = totalM*cantidad;
				$("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val(totalMF);
			} else if(hierroSelect==""){
				return;
			} else {
				Alerts_Box("Debe seleccionar una malla especial", 3);
				cerosPeso(prod);
			}
		} else if(tipoMalla=="Estándar"){
			if(hierroSelect.match(/.*standar.*/)){
				totalM = pesoA;
				totalMF = totalM*cantidad;
				$("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").val(totalM);
				$("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val(totalMF);
			} else if(hierroSelect==""){
				return;
			} else {
				Alerts_Box("Debe seleccionar una malla estándar", 3);
				cerosPeso(prod);
			}
		}
	}
	
	function calcularCant(){
		totalMF = totalM*cantidad;
		$("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val(totalMF);
	}
	
	function cerosPeso(prod){
		$("#figuracion_mallas_pesoA<?php echo $NumOfID; ?>").val(0);
		$("#figuracion_mallas_pesoB<?php echo $NumOfID; ?>").val(0);
		$("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").val(0);
		$("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val(0);
		if(prod==1){
			$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
			$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
			pesoA = 0;
			Producto = "";
		} else if (prod==2){
			$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
			$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
			pesoB = 0;
			Producto1 = "";
		}
	}
	
    function dibujar() {
		obtener();
		varillaB = Math.round((ancho-arrA-finA)/cuadr1+1);
		varillaA = Math.round((largo-arrB-finB)/cuadr2+1);
		varAR = ((ancho-arrA-finA)/cuadr1)-Math.trunc((ancho-arrA-finA)/cuadr1);
		varBR = ((largo-arrB-finB)/cuadr2)-Math.trunc((largo-arrB-finB)/cuadr2);

		ctx.clearRect(0, 0, canvas.width, canvas.height);
		
		if(ancho==0 || largo==0 ||cuadr1==0 ||cuadr2==0 ||arrA==0 ||finA==0 ||arrB==0 ||finB==0 ||ref==""){
			return;
		} else {
			if(varAR > 0 || varBR > 0){
				Alerts_Box("Los valores de la malla no cuadran<br>Debe revisarlos", 3);
				$("#figuracion_mallas_varA<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_varB<?php echo $NumOfID; ?>").val(0);
				cerosPeso();
			} else {
				$("#figuracion_mallas_varA<?php echo $NumOfID; ?>").val(varillaA);
				$("#figuracion_mallas_varB<?php echo $NumOfID; ?>").val(varillaB);
				cerosPeso();
				Alerts_Box("La malla cierre adecuadamente", 1);
			}
			ctx.font = "15px Arial";
			ctx.fillText("REF. " + ref + " <= " + ancho + "cm => Varillas longitud A: " + varillaA,10,15);
			ctx.fillText(arrA,25,65);
			ctx.fillText(finA,295,355);
			ctx.fillText(cuadr1,85,200);
		
			ctx.fillStyle = "rgb(200,0,0)";
				for (i = 1; i < 6; i++) { 				
					ctx.fillRect (20, 68*i, 315, 2);
				}
			ctx.fillStyle = "rgba(0, 0, 200, 0.5)";
				for (i = 1; i < 5; i++) { 				
					ctx.fillRect (70*i, 20, 2, 370);
				}
			ctx.fillStyle = "rgb(0,0,0)";
			ctx.rotate(-90 * Math.PI / 180);
			ctx.fillText("<= " + largo + "cm => Varillas longitud B: " + varillaB,-380,15);
			ctx.fillText(arrB,-65,85);
			ctx.fillText(finB,-380,275);
			ctx.fillText(cuadr2,-180,155);
		}
		calcularPeso();
    }	
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues(Interno)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		$.ajax({
			dataType: "json",
			type: "GET",
			url: "modulos/datos_productos.php",
			data: {"Figuracion_Cargar":Interno},
			success: function(data)
			{
				Locked = false;
				$("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val(Interno);
				
				$("#figuracion_mallas_venta_interno<?php echo $NumOfID; ?>").val(data[0]["Venta_Interno"]);
				
				$("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").jqxComboBox("addItem", { label: data[0]["Cliente"], value: data[0]["ClienteID"] });
				$("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox("addItem", { label: data[0]["ClienteID"], value: data[0]["ClienteID"] });
				$("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").jqxComboBox("selectItem", data[0]["ClienteID"]);
				
				$("#figuracion_mallas_obra<?php echo $NumOfID; ?>").val(data[0]["Obra"]);
				$("#figuracion_mallas_total<?php echo $NumOfID; ?>").val(data[0]["Peso"]);
				
				$("#figuracion_mallas_desperdicio<?php echo $NumOfID; ?>").val(data[0]["Desperdicio"]);
				DesperdicioArray = new Array();
				var Total_Fig = JSON.parse(data[0]["Total_Fig"]);
				for (var i = 0; i < Total_Fig.length; i++)
				{
					DesperdicioArray[Total_Fig[i]["Codigo"]] = {
						"Items":JSON.stringify(Total_Fig[i]["Items"]),
						"Peso":Total_Fig[i]["Peso2"],
					}
				}
				
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("clear");
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("clearSelection");
				var datarow = new Array();
				var len = data[0]["Items"].length;
				for (var i = 0; i < len; i++) {
					var TmpData = {
						"Figura":data[0]["Items"][i]["Figura"],
						"Codigo":data[0]["Items"][i]["Codigo"],
						"Nombre":data[0]["Items"][i]["Nombre"],
						"K":data[0]["Items"][i]["K"],
						"Longitud":data[0]["Items"][i]["Longitud"],
						"Peso":data[0]["Items"][i]["Peso"],
						"Cantidad":data[0]["Items"][i]["Cantidad"],
						"Ubicacion":data[0]["Items"][i]["Ubicacion"],
						"Dimensiones":JSON.stringify(data[0]["Items"][i]["Dimensiones"]),
					};
					datarow[i] = TmpData;
				}
				datarow.reverse();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
				//Clear
				FiguraID = "";				
				$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val("0");
				//$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
				$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
				$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
				$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");				
				$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
				$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_imagen<?php echo $NumOfID; ?>").html("");
				$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("clear");
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se cargaban los datos...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	}
	
	function LoadParameters()
	{
		FigurasSource.data = {"Figuracion_Figuras":true};
		HierrosSource.data = {"Productos_Mallas":true};
		ProductosSource.data = {"Produccion_Hierro":true};
		
		
		
		var ClientesDataAdapter = new $.jqx.dataAdapter(ClientesSource,
		{
			autoBind: true,
			loadComplete: function (records)
			{
				$("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").jqxComboBox({source: records});
				$("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox({source: records});
			},
		});
		
		var HierrosDataAdapter = new $.jqx.dataAdapter(HierrosSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
					kArray[records[i]["Codigo"]] = records[i]["K"];
			},
			loadComplete: function (records)
			{
				$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox({source: records});
				$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox({source: records});
				$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox({source: records});
				$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox({source: records});
			},
		});
		
		var ProductosDataAdapter = new $.jqx.dataAdapter(ProductosSource,
		{
			autoBind: true,
			beforeLoadComplete: function (records)
			{
				for (var i = 0; i < records.length; i++)
					PesoArray[records[i]["CodFab"]] = records[i]["Peso"];
			},
			loadComplete: function (records)
			{
				$("#figuracion_mallas_codigo2<?php echo $NumOfID; ?>").jqxComboBox({source: records});
				$("#figuracion_mallas_producto2<?php echo $NumOfID; ?>").jqxComboBox({source: records});
			},
		});
		
		$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_varA<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_varB<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pesoA<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pesoB<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val("");
		$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val("0");
	}
	
	function AddRow(Jump)
	{
		var Cant = $("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val();
		var Ref = $("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val();
		var Pelos1 = $("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val();
		var Pelos2 = $("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val();
		var Pelos3 = $("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val();
		var Pelos4 = $("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val();
		Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Producto1 = $("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Cantidad = $("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val();
		var Cantidad1 = $("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val();
		var Cuadricula1 = $("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val();
		var Cuadricula2 = $("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val();
		var Longitud = $("#figuracion_mallas_longitud<?php echo $NumOfID; ?>").val();
		var Peso = $("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val();
		
		if (!Producto) 
		{
			Alerts_Box("Favor Seleccionar un Figurado.", 3);
			WaitClick_Combobox("figuracion_mallas_producto<?php echo $NumOfID; ?>");
			return;
		}
		
		if (Cantidad < 1) 
		{
			Alerts_Box("Favor Ingresar una Cantidad Mayor a 0.", 3);
			WaitClick_NumberInput("figuracion_mallas_cantidad<?php echo $NumOfID; ?>");
			return;
		}
		
		var FinalArray = new Array();
		var Dimensiones = new Array();
		var datainfo = $("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			
			TmpArray["Punto"] = currentRow.Punto;
			TmpArray["Dimension"] = parseFloat(currentRow.Dimension);
			TmpArray["Angulo"] = parseFloat(currentRow.Angulo);
			
			Dimensiones[i] = TmpArray;
		}
		
		var TmpArray = {
			"Figura":Figura.value,
			"Codigo":Producto.value,
			"K":kArray[Producto.value],
			"Nombre":Producto.label,
			"Longitud":Longitud,
			"Peso":Peso,
			"Cantidad":Cantidad,
			"Total_Peso":0,
			"Ubicacion":Ubicacion,
			"Dimensiones":JSON.stringify(Dimensiones),
		};
		FinalArray[0] = TmpArray;
		$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("addrow", null, FinalArray, "first");
		
		FiguraID = "";
		$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val("");
		$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_imagen<?php echo $NumOfID; ?>").html("");
		$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("clear");
		$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val("0");
		if (Jump)
		{
			$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("selectrow", 0);
			
			var Wait = setTimeout(function()
			{
				$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("selectcell", 0, "Dimension");
				$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("focus");
				clearTimeout(Wait);
			},200);
		}
	}
	
	var ClientesSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
		url: "modulos/datos.php",
	};
	
	var FigurasSource =
	{
		datatype: "json",
		datafields: [
			{ name: "Figura", type: "string"},
			{ name: "Dimensiones", type: "int"},
			{ name: "Estribo", type: "bool"},
			{ name: "SemiCirculo", type: "bool"},
			{ name: "Circular", type: "bool"},
			{ name: "Vueltas", type: "bool"},
			{ name: "Imagen", type: "string"},
		],
		url: "modulos/datos.php",
	};
	
	var HierrosSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Codigo', type: 'string'},
			{ name: 'Nombre', type: 'string'},
			{ name: 'K', type: 'decimal'},
		],
		url: "modulos/datos_productos.php",
	};
	
	var ProductosSource =
	{
		datatype: "json",
		datafields: [
			{ name: "CodFab", type: "string"},
			{ name: "Nombre", type: "string"},
			{ name: "Peso", type: "decimal"},
		],
		url: "modulos/datos_productos.php",
	};
	
	$("#figuracion_mallas_interno<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
		placeHolder: 'Malla Nº',
		disabled: true
	});
	
	$("#figuracion_mallas_venta_interno<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20,
		disabled: true
	});
	
	
	$("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{			
			if (ClienteID == event.args.item.value)
				return;
			
			ClienteID = event.args.item.value;
			
			if ($("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 250,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Cliente',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if (ClienteID == event.args.item.value)
				return;
			
			if ($("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#figuracion_mallas_obra<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		width: 315,
		height: 20,
	});
	
	$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
	});
	$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox('addItem', {label: "Estándar"});
	$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox('addItem', {label: "Especial"});
	$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").on('change', function (event)
	{
		if (event.args) {
		tipoMalla = event.args.item.value;
			if (tipoMalla == "Estándar") {
				$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val(15);
				$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val(15);
				$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val(235);
				$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val(600);
				$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val(5);
				$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val(5);
				$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val(7.5);
				$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val(7.5);
				$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox({ disabled: true });
				$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox({ disabled: true });
				Producto = "";
				mallaUnHide();
				dibujar();
				$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").jqxInput("focus");
			} else {
				mallaHide();
				$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_varA<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_varB<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val(0);
				$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox({ disabled: false });
				$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox({ disabled: false});
				Producto = "";
			}
		}
	});	
	
	$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#figuracion_mallas_producto<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
			Calcular(false);
			UpdateItems("Producto");
			Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
			//pesoParcial = kArray[Producto.value];
			hierroSelect = Producto.label;
			calcularPeso(1);
		}
	});
	
	$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 130,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Codigo',
		valueMember: 'Codigo'
	});
	$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
			
			Calcular(false);
			UpdateItems("Producto1");
			Producto1 = $("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
			pesoParcial = kArray[Producto1.value];
			hierroSelect = Producto1.label;
			calcularPeso(2);
		}
	});	
	
	$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar malla',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
	});
	
	$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 300,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar malla',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'Codigo'
	});
	$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").on("change", function (event)
	{
		if (event.args)
		{
			if ($("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").val() != event.args.item.value)
				$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
		}
	});	
	
	$("#figuracion_mallas_varA<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:3,
		max:999,
		disabled: true
	});
	$("#figuracion_mallas_varB<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:3,
		max:999,
		disabled: true
	});
	$("#figuracion_mallas_pesoA<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:3,
		max:999,
		disabled: true
	});
	$("#figuracion_mallas_pesoB<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:3,
		max:999,
		disabled: true
	});
	$("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:3,
		max:999,
		disabled: true
	});
	$("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:3,
		max:999,
		disabled: true
	});
	
	$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999.99
	});
	$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").on("change", function (event)
	{
		cantidad = $("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val();
		Calcular(false);
		UpdateItems("Cant");
		calcularCant();
	});
	
	$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20
	});
	$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Ref");
		dibujar();
	});	
	
	$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999.99,
		disabled: true
	});
	$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Pelos1");
		dibujar();
	});	
	
	$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999.99,
		disabled: true
	});
	$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Pelos2");
		dibujar();
	});	
		
	$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999.99,
		disabled: true
	});
	$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Pelos3");
		dibujar();
	});	

	$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999.99,
		disabled: true
	});
	$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Pelos4");
		dibujar();
	});	
		
	$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:235,
		disabled: true
	});
	$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Cantidad");
		dibujar();
	});
	
	$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:5000,
		disabled: true
	});
	$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Cantidad1");
		dibujar();
	});
	
	$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999.99,
		disabled: true
	});
	$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Cuadricula1");
		dibujar();
	});
	
	$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999.99,
		disabled: true
	});
	$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").on("change", function (event)
	{
		Calcular(false);
		UpdateItems("Cuadricula2");
		dibujar();
	});		
	
	$("#figuracion_mallas_add<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 60,
		height: 25,
		template: "success"
	});
	$("#figuracion_mallas_add<?php echo $NumOfID; ?>").on("click", function()
	{
		AddRow(false);
	});
	
	$("#figuracion_mallas_act<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 60,
		height: 25,
		template: "info"
	});
	$("#figuracion_mallas_act<?php echo $NumOfID; ?>").on("click", function()
	{
		dibujar();
	});	
	
	$("#figuracion_mallas_unselect<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 60,
		height: 25,
		template: "primary"
	});
	$("#figuracion_mallas_unselect<?php echo $NumOfID; ?>").on("click", function()
	{
		FiguraID = "";
		$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val("");
		$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("clearselection");
		$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_imagen<?php echo $NumOfID; ?>").html("");
		$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("clear");
		$("#figuracion_mallas_longitud<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val("0");
		dibujar();
	});
	
	var DimensionesSource =
	{
		datafields:
		[
			{ name: "Punto", type: "string" },
			{ name: "Dimension", type: "decimal" },
			{ name: "Angulo", type: "int" },
		],
		addrow: function (rowid, rowdata, position, commit)
		{
			commit(true);
			UpdateItems("Dimensiones");
		},
		updaterow: function (rowid, rowdata, commit)
		{
			commit(true);
			Calcular(false);
			UpdateItems("Dimensiones");
		},
	};
	var DimensionesDataAdapter = new $.jqx.dataAdapter(DimensionesSource);
	
	$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid(
	{
		theme: mytheme,
		width: 180,
		height: 200,
		source: DimensionesDataAdapter,
		selectionmode: "singlecell",
		editable: true,
		editmode: "dblclick",
		columns:
		[
			{ text: 'Punto', datafield: 'Punto', editable: false, width: '25%', height: 20 },
			{
				text: 'Dimension',
				datafield: 'Dimension',
				editable: true,
				width: '40%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'Angulo',
				datafield: 'Angulo',
				editable: true,
				width: '35%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'n',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, decimalDigits: 0, digits: 12 });
				}
			},
		]
	});
	$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("localizestrings", localizationobj);
	/*document.getElementById("figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").addEventListener("focusin", function (event)
	{
		OnSelect = false;
	});*/
	
	var GridSource =
	{
		datafields:
		[
			{ name: 'Figura', type: 'string' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'K', type: 'decimal' },
			{ name: 'Nombre', type: 'string' },
			{ name: 'Longitud', type: 'decimal' },
			{ name: 'Peso', type: 'decimal' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Total_Peso', type: 'decimal' },
			{ name: 'Ubicacion', type: 'string' },
			{ name: 'Dimensiones', type: 'string' },
		],
		addrow: function (rowid, rowdata, position, commit)
		{
			commit(true);
			Calcular(true);
		},
		updaterow: function (rowid, rowdata, commit)
		{
			commit(true);
			Calcular(true);
		},
		deleterow: function (rowid, commit)
		{
			commit(true);
			Calcular(true);
		},
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid(
	{
		theme: mytheme,
		width: 800,
		autoheight: true,
		source: GridDataAdapter,
		selectionmode: "singlerow",
		enabletooltips: true,
		enablebrowserselection: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		editable: false,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				width: "4%",
				height: 20,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid('getselectedrowindex');
					var rowscount = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid('getrowid', selectedrowindex);
						$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: 'Figura', datafield: 'Figura', editable: false, width: '12%', height: 20 },
			{ text: '', datafield: 'Codigo', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'K', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'Dimensiones', editable: false, width: '0%', height: 20 },
			{ text: 'Figurado', datafield: 'Nombre', editable: false, width: '30.5%', height: 20 },
			{
				text: 'Long (m)',
				datafield: 'Longitud',
				editable: false,
				width: '11%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: false,
				width: '11%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd3',
				columntype: 'numberinput',
			},
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: false,
				width: '15%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'Total Peso',
				datafield: 'Total_Peso',
				editable: false,
				width: '16%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata) {
					var total = Math.round((parseFloat(rowdata.Peso) * parseFloat(rowdata.Cantidad)) * 100) / 100;
					return "<div style='margin: 4px;' class='jqx-right-align'>" + GridDataAdapter.formatNumber(total, "d2") + "</div>";
				}
			},
			{ text: 'Ubicacion', datafield: 'Ubicacion', editable: false, width: '20%', height: 20 },
		]
	});
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").on("rowunselect", function (event) 
	{
		var args = event.args;
		var rowBoundIndex = args.rowindex;
		var rowData = args.row;
		if (SelectedRow >= 0)
			ShowItems(SelectedRow);
		
		OnSelect = false;
	});
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").on("rowselect", function (event)
	{
		var args = event.args;
		var rowBoundIndex = args.rowindex;
		var rowData = args.row;
		SelectedRow = rowBoundIndex;
		OnSelect = true;
	});
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Codigo");
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "K");
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Dimensiones");
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("localizestrings", localizationobj);
	
	$("#figuracion_mallas_guardar<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 120,
		height: 25,
		template: "info"
	});
	$("#figuracion_mallas_guardar<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Guardar)
			return;
		SaveData(false);
	});
	
	$("#figuracion_mallas_nuevo<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 120,
		height: 25,
		template: "success"
	});
	$("#figuracion_mallas_nuevo<?php echo $NumOfID; ?>").on("click", function()
	{
		ClearDocument();
	});
	
	$("#figuracion_mallas_venta<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 120,
		height: 25,
		template: "primary"
	});
	$("#figuracion_mallas_venta<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Modificar)
			return;
		SaveData(true);
	});
	
	$("#figuracion_mallas_imprimir1<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 120,
		height: 25,
		template: "warning"
	});
	$("#figuracion_mallas_imprimir1<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		window.open("imprimir/cartilla_figuracion.php?Interno="+$("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val()+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_mallas_imprimir2<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 120,
		height: 25,
		template: "warning"
	});
	$("#figuracion_mallas_imprimir2<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		window.open("imprimir/cartilla_figuracion_hierros.php?Interno="+$("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val()+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_mallas_imprimir3<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 120,
		height: 25,
		template: "warning"
	});
	$("#figuracion_mallas_imprimir3<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		window.open("imprimir/cartilla_figuracion_materiales.php?Interno="+$("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val()+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: 'kg',
		symbolPosition: 'right',
		decimalDigits: 3,
		digits: 8,
		max: 99999999.999,
		disabled: true
	});
	
	$("#figuracion_mallas_longitud<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: 'm',
		symbolPosition: 'right',
		digits: 9,
		max: 999999999.99,
		disabled: true
	});
	
	$("#figuracion_mallas_total<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 160,
		height: 20,
		//inputMode: 'simple',
		spinButtons: false,
		symbol: 'kg',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 12,
		max: 999999999999,
		disabled: true
	});
	
	var TotalSource =
	{
		datafields:
		[
			{ name: "Codigo", type: "string" },
			{ name: "Nombre", type: "string" },
			{ name: "Cantidad", type: "decimal" },
			{ name: "Peso", type: "decimal" },
			{ name: "Peso2", type: "decimal" },
			{ name: "Items", type: "string" },
		],
		/*addrow: function (rowid, rowdata, position, commit)
		{
			commit(true);
			Calcular2();
		},*/
		updaterow: function (rowid, rowdata, commit)
		{
			commit(true);
			Calcular2();
		},
		/*deleterow: function (rowid, commit)
		{
			commit(true);
			Calcular2();
		},*/
	};
	var TotalDataAdapter = new $.jqx.dataAdapter(TotalSource);
	
	$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid(
	{
		theme: mytheme,
		width: 425,
		autoheight: true,
		source: TotalDataAdapter,
		enabletooltips: true,
		enablebrowserselection: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		editable: false,
		columns:
		[
			{ text: '', datafield: 'Codigo', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'Items', editable: false, width: '0%', height: 20 },
			{ text: '', datafield: 'Cantidad', editable: false, width: '0%', height: 20 },
			{ text: 'Figurado', datafield: 'Nombre', editable: false, width: '56%', height: 20 },
			{
				text: 'Peso Total (kg)',
				datafield: 'Peso',
				editable: false,
				width: '22%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
			{
				text: 'Requerido (kg)',
				datafield: 'Peso2',
				editable: false,
				width: '22%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
		]
	});
	$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Codigo");
	$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Items");
	$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Cantidad");
	$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("localizestrings", localizationobj);
	$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").on("rowunselect", function (event) 
	{
		var args = event.args;
		var rowBoundIndex = args.rowindex;
		var rowData = args.row;
		
		if (SelectedRow >= 0)
			ShowItems2(SelectedRow);
		
		OnSelect = false;
	});
	$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").on("rowselect", function (event)
	{
		var args = event.args;
		var rowBoundIndex = args.rowindex;
		var rowData = args.row;
		SelectedRow = rowBoundIndex;
		OnSelect = true;
	});
	
	
	var ItemsSource =
	{
		datafields:
		[
			{ name: "Codigo", type: "string" },
			{ name: "Nombre", type: "string" },
			{ name: "Peso", type: "decimal" },
			{ name: "Cantidad", type: "decimal" },
			{ name: "Peso2", type: "decimal" },
		],
		addrow: function (rowid, rowdata, position, commit)
		{
			commit(true);
			Calcular3();
		},
		updaterow: function (rowid, rowdata, commit)
		{
			commit(true);
			Calcular3();
		},
		deleterow: function (rowid, commit)
		{
			commit(true);
			Calcular3();
		},
	};
	var ItemsDataAdapter = new $.jqx.dataAdapter(ItemsSource);
	
	$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid(
	{
		theme: mytheme,
		width: 560,
		autoheight: true,
		source: ItemsDataAdapter,
		enabletooltips: true,
		enablebrowserselection: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		editable: true,
		showtoolbar: true,
		rendertoolbar: function (toolbar)
		{
			var container = "<div style=\"margin-top: 4px; text-align: center; list-style:none;\">";
			container += "<li style=\"float:left; margin:0px 4px;\">";
			container += "<div id=\"figuracion_mallas_codigo2<?php echo $NumOfID; ?>\"></div>";
			container += "</li>";
			container += "<li style=\"float:left; margin:0px 4px;\">";
			container += "<div id=\"figuracion_mallas_producto2<?php echo $NumOfID; ?>\"></div>";
			container += "</li>";
			container += "<li style=\"float:left; margin:0px 4px;\">";
			container += "<div id=\"figuracion_mallas_cantidad2<?php echo $NumOfID; ?>\"></div>";
			container += "</li>";
			container += "<li style=\"float:left; margin:0px 4px;\">";
			container += "<input type=\"button\" id=\"figuracion_mallas_add2<?php echo $NumOfID; ?>\" value=\"Añadir\">";
			container += "</li>";
			container += "</div>";
			toolbar.append(container);
			
			$("#figuracion_mallas_codigo2<?php echo $NumOfID; ?>").jqxComboBox(
			{
				theme: mytheme,
				width: 110,
				height: 20,
				searchMode: 'containsignorecase',
				autoComplete: true,
				promptText: 'Seleccionar',
				selectedIndex: -1,
				displayMember: 'CodFab',
				valueMember: 'CodFab'
			});
			$("#figuracion_mallas_codigo2<?php echo $NumOfID; ?>").on("change", function (event)
			{
				if (event.args)
				{
					if ($("#figuracion_mallas_producto2<?php echo $NumOfID; ?>").val() != event.args.item.value)
						$("#figuracion_mallas_producto2<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
				}
			});
			
			$("#figuracion_mallas_producto2<?php echo $NumOfID; ?>").jqxComboBox(
			{
				theme: mytheme,
				width: 275,
				height: 20,
				searchMode: 'containsignorecase',
				autoComplete: true,
				promptText: 'Seleccionar Producto',
				selectedIndex: -1,
				displayMember: 'Nombre',
				valueMember: 'CodFab'
			});
			$("#figuracion_mallas_producto2<?php echo $NumOfID; ?>").on("change", function (event)
			{
				if (event.args)
				{
					if ($("#figuracion_mallas_codigo2<?php echo $NumOfID; ?>").val() != event.args.item.value)
						$("#figuracion_mallas_codigo2<?php echo $NumOfID; ?>").jqxComboBox('selectItem', event.args.item.value);
				}
			});
			
			$("#figuracion_mallas_cantidad2<?php echo $NumOfID; ?>").jqxNumberInput({
				theme: mytheme,
				width: 70,
				height: 20,
				inputMode: 'simple',
				spinButtons: false,
				digits:9,
				max:999999999.99
			});
	
			$("#figuracion_mallas_add2<?php echo $NumOfID; ?>").jqxButton({
				theme: mytheme,
				width: 60,
				height: 25,
				template: "success"
			});
			$("#figuracion_mallas_add2<?php echo $NumOfID; ?>").on("click", function()
			{
				var Producto = $("#figuracion_mallas_producto2<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
				var Cantidad = $("#figuracion_mallas_cantidad2<?php echo $NumOfID; ?>").val();
				if (!Producto) 
				{
					Alerts_Box("Favor Seleccionar un Producto.", 3);
					WaitClick_Combobox("figuracion_mallas_producto2<?php echo $NumOfID; ?>");
					return;
				}
				
				if (Cantidad < 1) 
				{
					Alerts_Box("Favor Ingresar una Cantidad Mayor a 0.", 3);
					WaitClick_NumberInput("figuracion_mallas_cantidad2<?php echo $NumOfID; ?>");
					return;
				}
				
				var TmpArray = {
					"Codigo":Producto.value,
					"Nombre":Producto.label,
					"Peso":PesoArray[Producto.value],
					"Cantidad":Cantidad,
					"Peso2":0,
				};
				
				$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("addrow", null, TmpArray, "first");
				
				$("#figuracion_mallas_codigo2<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
				$("#figuracion_mallas_producto2<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
				$("#figuracion_mallas_cantidad2<?php echo $NumOfID; ?>").val("0");
			});
		},
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				width: "5%",
				height: 20,
				editable: false,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function ()
				{
					var selectedrowindex = $("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid('getselectedrowindex');
					var rowscount = $("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid('getrowid', selectedrowindex);
						$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid('deleterow', id);
					}
				}
			},
			{ text: '', datafield: 'Codigo', editable: false, width: '0%', height: 20 },
			{ text: 'Producto', datafield: 'Nombre', editable: false, width: '40%', height: 20 },
			{
				text: 'Peso',
				datafield: 'Peso',
				editable: false,
				width: '11%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
			},
			{
				text: 'Cantidad',
				datafield: 'Cantidad',
				editable: true,
				width: '22%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				validation: function (cell, value)
				{
					if (value < 0)
						return { result: false, message: "La Cantidad debe ser mayor a 0!" };
					else
						return true;
				},
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ spinButtons:false, digits: 12 });
				}
			},
			{
				text: 'Peso Total (kg)',
				datafield: 'Peso2',
				editable: false,
				width: '22%',
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
				decimalSeparator: ",",
				cellsrenderer: function (index, datafield, value, defaultvalue, column, rowdata)
				{
					var total = Math.round((parseFloat(rowdata.Peso) * parseFloat(rowdata.Cantidad)) * 100) / 100;
					return "<div style='margin: 4px;' class='jqx-right-align'>" + ItemsDataAdapter.formatNumber(total, "d2") + "</div>";
				}
			},
		]
	});
	$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Codigo");
	$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("localizestrings", localizationobj);
	
	$("#figuracion_mallas_desperdicio<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 60,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: '%',
		symbolPosition: 'right',
		digits: 3,
		disabled: true
	});
	
	function ShowItems(Index)
	{		
		var Codigo = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Codigo");
		var Cantidad = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Cantidad");
		var Ubicacion = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Ubicacion");
		var Dimensiones = JSON.parse($("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Dimensiones"));
		var datarow = new Array();
		$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("clear");
		for (var i = 0; i < Dimensiones.length; i++)
		{
			var TmpData = {
				"Punto":Dimensiones[i]["Punto"],
				"Dimension":parseFloat(Dimensiones[i]["Dimension"]),
				"Angulo":parseFloat(Dimensiones[i]["Angulo"]),
			};
			datarow[i] = TmpData;
		}
		datarow.reverse();
		$("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
		$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox("selectItem", Codigo);
		$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox("selectItem", Codigo1);
		$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val(Cant);
		$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val(Ref);
		$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val(Pelos1);
		$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val(Pelos2);
		$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val(Pelos3);
		$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val(Pelos4);
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val(Cantidad);
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val(Cantidad1);
		$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val(Cuadricula1);
		$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val(Cuadricula2);
		Calcular(false);
	}
	
	function ShowItems2(Index)
	{
		var Items = JSON.parse($("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Items"));
		var datarow = new Array();
		$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("clear");
		for (var i = 0; i < Items.length; i++)
		{
			var TmpData = {
				"Codigo":Items[i]["Codigo"],
				"Nombre":Items[i]["Nombre"],
				"Peso":parseFloat(Items[i]["Peso"]),
				"Cantidad":Items[i]["Cantidad"],
				"Peso2":parseFloat(Items[i]["Peso2"]),
			};
			datarow[i] = TmpData;
		}
		datarow.reverse();
		$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
	}
	
	function UpdateItems(type)
	{
		if (OnSelect)
			return;
		
		var index = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getselectedrowindex");
		if (index < 0)
			return;
		
		switch(type)
		{			
			case "Dimensiones":
				var Longitud = $("#figuracion_mallas_longitud<?php echo $NumOfID; ?>").val();
				var Peso = $("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val();
				var Dimensiones = new Array();
				var datainfo = $("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
				var count = datainfo.rowscount;
				for (var i = 0; i < count; i++)
				{
					var TmpArray = {};
					var currentRow = $("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
					
					TmpArray["Punto"] = currentRow.Punto;
					TmpArray["Dimension"] = parseFloat(currentRow.Dimension);
					TmpArray["Angulo"] = parseFloat(currentRow.Angulo);
					
					Dimensiones[i] = TmpArray;
				}
				//alert(JSON.stringify(Dimensiones))
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Dimensiones", JSON.stringify(Dimensiones));
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Longitud", Longitud);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Peso", Peso);
			break;
			
			case "Producto":
				var Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
				var Longitud = $("#figuracion_mallas_longitud<?php echo $NumOfID; ?>").val();
				var Peso = $("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val();
				if (!Producto) 
				{
					Alerts_Box("Ocurrió un error al actualizar los datos del objeto seleccionado.<br />Intente nuevamente.", 4);
					return;
				}
				
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Codigo", Producto.value);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Nombre", Producto.label);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "K", kArray[Producto.value]);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Longitud", Longitud);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Peso", Peso);
			break;
			
			case "Cant":
				var Cant = $("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Cant", Cant);
			break;
			
			case "Ref":
				var Cant = $("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Ref", Ref);
			break;
			
			case "Pelos1":
				var Cant = $("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Pelos1", Pelos1);
			break;
			
			case "Pelos2":
				var Cant = $("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Pelos2", Pelos2);
			break;
			
			case "Pelos3":
				var Cant = $("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Pelos3", Pelos3);
			break;
			
			case "Pelos4":
				var Cant = $("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Pelos4", Pelos4);
			break;
			
			case "Cantidad":
				var Cantidad = $("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Cantidad", Cantidad);
			break;
			
			case "Cantidad1":
				var Cantidad1 = $("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Cantidad1", Cantidad1);
			break;

			case "Cuadricula1":
				var Cuadricula1 = $("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Cuadricula1", Cuadricula1);
			break;

			case "Cuadricula2":
				var Cuadricula2 = $("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Cuadricula2", Cuadricula2);
			break;				

		}
		//Keep row selected
		//Bug Infinite Loop
		//$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("selectrow", index);
	}
	
	function Calcular(Final)
	{
		if (Final)
		{
			var Total = 0;
			var datainfo = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
			var count = datainfo.rowscount;
			for (var i = 0; i < count; i++)
			{
				var currentRow = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
				
				var K = parseFloat(currentRow.K);
				var C = parseFloat(currentRow.Cantidad);
				var LBase = 0;
				var LSum = 0;
				var L = 0;
				
				var Dimensiones = JSON.parse(currentRow.Dimensiones);
				for (var a = 0; a < Dimensiones.length; a++)
				{
					if (EstriboArray[currentRow.Figura] == true)
					{
						LBase += parseFloat(Dimensiones[a]["Dimension"]) * 2;
					}
					else if (VueltasArray[currentRow.Figura] == true && Dimensiones.length - a == 1)
					{
						LBase = LBase * parseFloat(Dimensiones[a]["Dimension"]);
					}
					else if (SemiCirculoArray[currentRow.Figura] == true && Dimensiones[a]["Punto"] == "C")
					{
						LBase = 0.5 * Math.PI * parseFloat(Dimensiones[a]["Dimension"]);
					}
					else if (CircularArray[currentRow.Figura] == true && Dimensiones[a]["Punto"] == "A")
					{
						LBase = Math.PI * parseFloat(Dimensiones[a]["Dimension"]);
					}
					else
						LSum += parseFloat(Dimensiones[a]["Dimension"]);
				}
				
				L = LBase + LSum;
				Total += Math.round(((L * K) * C) * 100) / 100;
				//Total += (L * K) * C;
			}
			$("#figuracion_mallas_total<?php echo $NumOfID; ?>").val(Total);
			//Bug Infinite Loop
			//$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("clearSelection");
			var rows = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrows");
			var OrderArray = new Array();
			var FinalArray = new Array();
			
			for (var i = 0; i < rows.length; i++)
			{
				var TmpArray = {};
				
				var currentRow = rows[i];
				
				TmpArray["Codigo"] = currentRow.Codigo;
				TmpArray["Nombre"] = currentRow.Nombre;
				TmpArray["Cantidad"] = parseFloat(currentRow.Cantidad);
				TmpArray["Peso"] = parseFloat(currentRow.Peso);
				TmpArray["Total_Peso"] = Math.round((parseFloat(currentRow.Peso) * parseFloat(currentRow.Cantidad)) * 100) / 100;
				TmpArray["K"] = parseFloat(currentRow.K);
				
				OrderArray[i] = TmpArray;
			}
			OrderArray.sort(function(a, b)
			{
				if (a.Codigo > b.Codigo) {
					return 1;
				}
				else if (a.Codigo < b.Codigo) {
					return -1;
				}
				// a must be equal to b
				return 0;
			});
			var CurrentCode = "_empty_";
			var TotalCantidad = 0;
			var TotalPeso = 0;
			var a = 0;
			for (var i = 0; i < OrderArray.length; i++)
			{
				var TmpArray = {};
				if (OrderArray[i]["Codigo"] != CurrentCode)
				{
					CurrentCode = OrderArray[i]["Codigo"];
					TotalCantidad = parseFloat(OrderArray[i]["Cantidad"]);
					TotalPeso = parseFloat(OrderArray[i]["Total_Peso"]);
				}
				else
				{
					TotalCantidad += parseFloat(OrderArray[i]["Cantidad"]);
					TotalPeso += parseFloat(OrderArray[i]["Total_Peso"]);
				}
				
				if (OrderArray.length - i == 1)
				{
					TmpArray["Codigo"] = OrderArray[i]["Codigo"];
					TmpArray["Nombre"] = OrderArray[i]["Nombre"];
					TmpArray["Cantidad"] = TotalCantidad;//OrderArray[i]["Cantidad"];
					TmpArray["Peso"] = TotalPeso;
					TmpArray["Peso2"] = DesperdicioArray[OrderArray[i]["Codigo"]]== undefined ? "0.00":DesperdicioArray[OrderArray[i]["Codigo"]]["Peso"];
					TmpArray["K"] = parseFloat(OrderArray[i]["K"]);
					TmpArray["Items"] = DesperdicioArray[OrderArray[i]["Codigo"]] == undefined ? "[]":DesperdicioArray[OrderArray[i]["Codigo"]]["Items"];
					
					FinalArray[a] = TmpArray;
					a++;
				}
				else if (OrderArray[i+1]["Codigo"] != CurrentCode)
				{
					TmpArray["Codigo"] = OrderArray[i]["Codigo"];
					TmpArray["Nombre"] = OrderArray[i]["Nombre"];
					TmpArray["Cantidad"] = TotalCantidad;//OrderArray[i]["Cantidad"];
					TmpArray["Peso"] = TotalPeso;
					TmpArray["Peso2"] = DesperdicioArray[OrderArray[i]["Codigo"]]== undefined ? "0.00":DesperdicioArray[OrderArray[i]["Codigo"]]["Peso"];
					TmpArray["K"] = parseFloat(OrderArray[i]["K"]);
					TmpArray["Items"] = DesperdicioArray[OrderArray[i]["Codigo"]] == undefined ? "[]":DesperdicioArray[OrderArray[i]["Codigo"]]["Items"];
					
					FinalArray[a] = TmpArray;
					a++;
				}
			}
			FinalArray.sort(function(a, b)
			{
				if (a.K > b.K) {
					return 1;
				}
				else if (a.K < b.K) {
					return -1;
				}
				// a must be equal to b
				return 0;
			});
			//alert(JSON.stringify(FinalArray))
			$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("clear");
			$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("clearSelection");
			$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("clear");
			$("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("clearSelection");
			$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("addrow", null, FinalArray, "first");
		}
		else
		{
			var Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
			if (!Producto) 
			{
				$("#figuracion_mallas_longitud<?php echo $NumOfID; ?>").val("0");
				$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val("0");
				return;
			}
			
			var Cant = $("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val();
			var Ref = $("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val();
			var Pelos1 = $("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val();
			var Pelos2 = $("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val();
			var Pelos3 = $("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val();
			var Pelos4 = $("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val();
			var Cantidad = $("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val();
			var Cantidad1 = $("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val();
			var Cuadricula1 = $("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val();
			var Cuadricula2 = $("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val();
			var K = kArray[Producto.value];
			var C = 1;//Cantidad;
			var LBase = 0;
			var LSum = 0;
			var L = 0;
			var Total = 0;
			
			var datainfo = $("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
			var count = datainfo.rowscount;
			for (var i = 0; i < count; i++)
			{
				var currentRow = $("#figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
				
				if (EstriboArray[Figura.value] == true)
				{
					LBase += parseFloat(currentRow.Dimension) * 2;
				}
				else if (VueltasArray[Figura.value] == true && count - i == 1)
				{
					LBase = LBase * parseFloat(currentRow.Dimension);
				}
				else if (SemiCirculoArray[Figura.value] == true && currentRow.Punto == "C")
				{
					LBase = 0.5 * Math.PI * parseFloat(currentRow.Dimension);
				}
				else if (CircularArray[Figura.value] == true && currentRow.Punto == "A")
				{
					LBase = Math.PI * parseFloat(currentRow.Dimension);
				}
				else
					LSum += parseFloat(currentRow.Dimension);
			}
			L = LBase + LSum;
			// 2 decimals not used
			//Total = Math.round(((L * K) * C) * 100) / 100;
			Total += (((L * K) * C) * 100) / 100;
			Total = Total.toFixed(3);
			$("#figuracion_mallas_longitud<?php echo $NumOfID; ?>").val(L);
			$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val(Total);
		}
	}
	
	function Calcular2()
	{
		var Peso1 = 0;
		var Peso2 = 0;
		var Total = 0;
		DesperdicioArray = new Array();
		var datainfo = $("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			DesperdicioArray[currentRow.Codigo] = {
				"Items":currentRow.Items,
				"Peso":currentRow.Peso2,
			}
			Peso1 += currentRow.Peso;
			Peso2 += currentRow.Peso2;
		}
		Total = ((Peso2 / Peso1) - 1) * 100;
		$("#figuracion_mallas_desperdicio<?php echo $NumOfID; ?>").val(Total);
	}
	
	function Calcular3()
	{
		if (OnSelect)
			return;
		
		var index = $("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("getselectedrowindex");
		if (index < 0)
			return;
		
		var Total = 0;
		var FinalArray = new Array();
		var a = 0;
		
		var datainfo = $("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_mallas_items_grid3<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			
			var Peso = parseFloat(currentRow.Peso) * parseFloat(currentRow.Cantidad);
			
			TmpArray["Codigo"] = currentRow.Codigo;
			TmpArray["Nombre"] = currentRow.Nombre;
			TmpArray["Peso"] = currentRow.Peso;
			TmpArray["Cantidad"] = currentRow.Cantidad;
			TmpArray["Peso2"] = Peso;
			
			Total += Peso;
			FinalArray[a] = TmpArray;
			a++;
		}
		$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Items", JSON.stringify(FinalArray));
		$("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Peso2", Total);
	}
	
	function SaveData(Generar)
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Cartilla = $("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val();
		var Cliente = $("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Obra = $("#figuracion_mallas_obra<?php echo $NumOfID; ?>").val();
		var Peso = $("#figuracion_mallas_total<?php echo $NumOfID; ?>").val();
		var Desperdicio = $("#figuracion_mallas_desperdicio<?php echo $NumOfID; ?>").val();
		
		if (!Cliente) 
		{
			Alerts_Box("Por favor selecciona un cliente", 3);
			WaitClick_Combobox("figuracion_mallas_cliente<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if (Obra == "") 
		{
			Alerts_Box("Por favor ingresar el nombre de la obra", 3);
			WaitClick_Input("figuracion_mallas_obra<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if (Peso < 0.01) 
		{
			Alerts_Box("No se puede crear una cartilla con peso = 0.00 kg", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		var FinalArray = new Array();
		var TotalArray = new Array();
		
		var datainfo = $("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_mallas_items_grid2<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			
			TmpArray["Codigo"] = currentRow.Codigo;
			TmpArray["Cantidad"] = parseFloat(currentRow.Cantidad);
			TmpArray["Peso"] = parseFloat(currentRow.Peso);
			TmpArray["Items"] = JSON.parse(currentRow.Items);
			TmpArray["Peso2"] = parseFloat(currentRow.Peso2);
			
			TotalArray[i] = TmpArray;
		}
		
		var datainfo = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			
			if (i == 0)
			{
				TmpArray["Generar"] = Generar;
				TmpArray["Interno"] = Cartilla;
				TmpArray["ClienteID"] = Cliente.value;
				TmpArray["Obra"] = Obra;
				TmpArray["Total_Peso"] = Peso;
				TmpArray["Total_Fig"] = JSON.stringify(TotalArray);
				TmpArray["Desperdicio"] = Desperdicio;
			}
			
			TmpArray["Figura"] = currentRow.Figura;
			TmpArray["Codigo"] = currentRow.Codigo;
			TmpArray["Longitud"] = parseFloat(currentRow.Longitud);
			TmpArray["Peso"] = parseFloat(currentRow.Peso);
			TmpArray["Cantidad"] = parseFloat(currentRow.Cantidad);
			TmpArray["Ubicacion"] = currentRow.Ubicacion;
			TmpArray["Dimensiones"] = currentRow.Dimensiones;
			
			FinalArray[i] = TmpArray;
		}
		
		/*alert(JSON.stringify(FinalArray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		
		if (Cartilla == "")
		{
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {"figuracion_mallas":FinalArray},
				success: function (data)
				{
					ReDefine();
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							LoadParameters();
							Alerts_Box("Datos Guardados con Exito!", 2);
							$("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val(data[0]["Interno"]);
							$("#figuracion_mallas_venta_interno<?php echo $NumOfID; ?>").val(data[0]["Venta_Interno"]);
						break;
						
						case "ERROR":
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
						break;
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
					+"Error: "+errorThrown, 3);
				}
			});
		}
		else
		{
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {"Figuracion_Modificar":FinalArray},
				success: function (data)
				{
					ReDefine();
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					
					switch(data[0]["MESSAGE"])
					{
						case "ERROR":
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
						break;
						
						default:
							LoadParameters();
							Alerts_Box("Datos Guardados con Exito!", 2);
						break;
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
					+"Error: "+errorThrown, 3);
				}
			});
		}
	}
	
	if (!Admin && !Guardar)
	{
		$("#figuracion_mallas_guardar<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		$("#figuracion_mallas_nuevo<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		$("#figuracion_mallas_venta<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
	}
	
	if (!Admin && !Imprimir)
	{
		$("#figuracion_mallas_imprimir1<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		$("#figuracion_mallas_imprimir2<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		$("#figuracion_mallas_imprimir3<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
	}
	
	if ("<?php echo $Cartilla ?>" == "")
	{
		LoadParameters();
		CheckRefresh();
	}
	else
	{
		EnableDisableAll(true);
		$("#figuracion_mallas_guardar<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		$("#figuracion_mallas_nuevo<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		$("#figuracion_mallas_venta<?php echo $NumOfID; ?>").jqxButton({ disabled: true });
		LoadValues("<?php echo $Cartilla ?>");
	}
});
//$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").jqxNumberInput({ disabled: true });
</script>

<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="3" cellspacing="0" style="margin-bottom:15px; width:999px;">
		<tr>
			<td>
				Malla
			</td>		
			<td>
				Interno
			</td>
			<td colspan="2">
				Cliente
			</td>
			<td>
				Cliente ID
			</td>
			<td colspan="3">
				Obra
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="figuracion_mallas_interno<?php echo $NumOfID; ?>"/>
			</td>	
			<td>
				<input type="text" id="figuracion_mallas_venta_interno<?php echo $NumOfID; ?>"/>
			</td>
			<td colspan="2">
				<div id="figuracion_mallas_cliente<?php echo $NumOfID; ?>"></div>
			</td>
			<td style="width: 100px;">
				<div id="figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="3">
				<input type="text" id="figuracion_mallas_obra<?php echo $NumOfID; ?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="2">
				&nbsp;
			</td>
			<td>
				Ref. / Posición
			</td>
			<td>
				Cantidad
			</td>	
			<td>
				Arranque A (cm)
			</td>
			<td>
				Final A (cm)
			</td>
			<td>
				Arranque B (cm)
			</td>
			<td>
				Final B (cm)
			</td>
		</tr>
		<tr>
			<td style="font-size: 14px;">
				Tipo de malla
			</td>		
			<td>
				<div id="figuracion_mallas_tipomalla<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<input type="text" style="text-align: center;" id="figuracion_mallas_ref<?php echo $NumOfID; ?>"/>
			</td>
			
			<td>
				<div id="figuracion_mallas_cant<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_pelos1<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_pelos2<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_pelos3<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_pelos4<?php echo $NumOfID; ?>"></div>
			</td>
		</tr>			
		<tr>
			<td>
				&nbsp;
			</td>		
			<td>
				Medida (cm)
			</td>
			<td>
				Separación (cm)
			</td>			
			<td>
				Calibre malla
			</td>

		</tr>		
		<tr>
			<td style="font-size: 14px;">
				Ancho malla(A)
			</td>
			<td>
				<div id="figuracion_mallas_cantidad<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>"></div>
			</td>			
			<td>
				<div id="figuracion_mallas_codigo<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="3">
				<div id="figuracion_mallas_producto<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<input type="button" id="figuracion_mallas_add<?php echo $NumOfID; ?>" value="Añadir"/>
				<input type="button" id="figuracion_mallas_unselect<?php echo $NumOfID; ?>" value="Nuevo"/>
			</td>
		</tr>
		<tr>
			<td style="font-size: 14px;">
				Largo malla(B)
			</td>
			<td>
				<div id="figuracion_mallas_cantidad1<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>"></div>
			</td>				
			<td>
				<div id="figuracion_mallas_codigo1<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="3">
				<div id="figuracion_mallas_producto1<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<input type="button" id="figuracion_mallas_act<?php echo $NumOfID; ?>" value="Actualizar"/>			
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>		
			<td>
				Varillas Long. A
			</td>
			<td>
				Varillas Long. B
			</td>			
			<td>
				Peso en A
			</td>
			<td>
				Peso en B
			</td>
			<td>
				Peso unitario
			</td>
			<td>
				Peso total item
			</td>
		</tr>		
		<tr>
			<td style="font-size: 14px;">
				Detalles malla
			</td>
			<td>
				<div id="figuracion_mallas_varA<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_varB<?php echo $NumOfID; ?>"></div>
			</td>				
			<td>
				<div id="figuracion_mallas_pesoA<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_pesoB<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_pesoT<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_pesoFT<?php echo $NumOfID; ?>"></div>
			</td>
		</tr>	
		<tr>
			<td colspan="2">
			&nbsp;
			</td>
		</tr>
	</table>
	
	<section id="Mallas">
		<div style="width: 360px; display: inline-block; ">
			<canvas id="canvas" width="345" height="400" style="background: white; border: 1px #a4bed4 solid; border-radius: 4px;">
				Su navegador no tiene soporte para mostrar el contenido
			</canvas>
		</div>
		<div style="display: inline-block; vertical-align: top;">
			<td colspan="5" rowspan="5" style="vertical-align:-webkit-baseline-middle;">
				<div id="figuracion_mallas_items_grid<?php echo $NumOfID; ?>"></div>
			</td>
		</div>
	</section>
	
	<table cellpadding="3" cellspacing="0" style="margin-bottom:15px; width:999px;">	
		<tr>
			<td colspan="2">
				Imagen
			</td>
			<td style="width:122px;">
				Dimensiones (m)
			</td>
			<td colspan="5">
				Total
			</td>
		</tr>
		<tr>
			<td colspan="2" style="height:140px; vertical-align:-webkit-baseline-middle;">
				<div id="figuracion_mallas_imagen<?php echo $NumOfID; ?>" style="width:230px; height:140px; border:1px solid #d3d3d3; text-align:center; background:#FFF;"></div>
			</td>
			<td rowspan="3" style="vertical-align:-webkit-baseline-middle;">
				<div id="figuracion_mallas_dimensiones<?php echo $NumOfID; ?>"></div>
			</td>
			
		</tr>
		<tr style="height:25px;">
			<td style="text-align: center;">
				Peso Un.
			</td>
			<td style="text-align: center;">
				Longitud Un.
			</td>
		</tr>
		<tr style="height:25px;">
			<td>
				<div id="figuracion_mallas_peso<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<div id="figuracion_mallas_longitud<?php echo $NumOfID; ?>"></div>
			</td>
		</tr>
		<tr style="height:25px;">
			<td colspan="2">
				<li style="margin: 6px 8px 0px 0px;">
					Total Peso
				</li>
				<li style="margin-left:3px;">
					<div id="figuracion_mallas_total<?php echo $NumOfID; ?>"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3" style="vertical-align:-webkit-baseline-middle;">
				<div style="background-color: #6E93CE; color: white; font-size: 14px; text-align: center; padding: 4px 0px; border-radius: 5px 5px 0px 0px;">Totales por Calibre de Hierro de Refuerzos</div>
				<div id="figuracion_mallas_items_grid2<?php echo $NumOfID; ?>"></div>
			</td>
			<td colspan="5" style="vertical-align:-webkit-baseline-middle;">
				<div style="background-color: #629C62; color: white; font-size: 14px; text-align: center; padding: 4px 0px; border-radius: 5px 5px 0px 0px;">Materiales Requeridos</div>
				<div id="figuracion_mallas_items_grid3<?php echo $NumOfID; ?>"></div>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="height:25px;">
				<li style="margin-top: 6px;">
					Porcentaje de Desperdicio
				</li>
				<li style="margin-left:13px;">
					<div id="figuracion_mallas_desperdicio<?php echo $NumOfID; ?>"></div>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="height:10px;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<li>
					<input type="button" id="figuracion_mallas_guardar<?php echo $NumOfID; ?>" value="Guardar"/>
				</li>
				<li style="margin-left:4px;">
					<input type="button" id="figuracion_mallas_nuevo<?php echo $NumOfID; ?>" value="Nuevo"/>
				</li>
				<li style="margin-left:4px;">
					<input type="button" id="figuracion_mallas_venta<?php echo $NumOfID; ?>" value="Cotizacion"/>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<li>
					<input type="button" id="figuracion_mallas_imprimir1<?php echo $NumOfID; ?>" value="Imprimir"/>
				</li>
				<li style="margin-left:4px;">
					<input type="button" id="figuracion_mallas_imprimir2<?php echo $NumOfID; ?>" value="Resumen"/>
				</li>
				<li style="margin-left:4px;">
					<input type="button" id="figuracion_mallas_imprimir3<?php echo $NumOfID; ?>" value="Requeridos"/>
				</li>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
		</tr>
	</table>
</div>