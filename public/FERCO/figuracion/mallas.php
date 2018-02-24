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
	var itemshow = "";
	
	// Dibujar
	$('#container').jqxDraw();
	var renderer = $('#container').jqxDraw('getInstance');
	var size = renderer.getSize();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Figuracion_Content");
	var Body = document.getElementById("Figuracion_Mallas_Content");
	var Times = 0;
	var Refresh = 0;
	var Hide = 0;
	
	function ceroVars() {
		pesoA = 0;
		pesoB = 0;
		cantidad = 0;
		ancho = 0;
		largo = 0;
		cuadr1 = 0;
		cuadr2 = 0;
		arrA = 0;
		finA = 0;
		arrB = 0;
		finB = 0;
		ref = "";
		varillaA = 0;
		varillaB = 0;
		varAR = 0;
		varBR = 0;
		pesoParcial = 0;
		Producto = "";
		Producto1 = "";
		hierroSelect = "";
		totalM = 0;
		totalMF = 0;
	}
	
	function totalMalla () {
		var datainfo = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		var total = 0;
		for (var i = 0; i < count; i++)
		{
			var currentRow = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			total += parseFloat(currentRow.Total_Peso);
		}
		return total;
	}
	
	function CheckRefresh () {
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
	
	function CheckHide () {
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
			{id:"figuracion_mallas_cartilla<?php echo $NumOfID; ?>", type:"jqxComboBox"},
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
			{id:"figuracion_mallas_items_grid<?php echo $NumOfID; ?>", type:"jqxGrid"},
			{id:"figuracion_mallas_peso<?php echo $NumOfID; ?>", type:"jqxNumberInput"},
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
			{id:"figuracion_mallas_items_grid<?php echo $NumOfID; ?>", type:"jqxGrid"},
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
		ceroVars();
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
	
	function clearItem() {
		Locked = false;
		OnSelect = false;
		SelectedRow = -1;
		$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid('unselectrow', 1);
		$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox('focus');
		$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox('open');
		//---
		$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox("clearSelection");
		$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox({disabled: false});
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
		$("#figuracion_mallas_varA<?php echo $NumOfID; ?>").val("0");
		$("#figuracion_mallas_varB<?php echo $NumOfID; ?>").val("0");
		cerosPeso(1);
		cerosPeso(2);
		ceroVars();
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
		$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox({ disabled: true });
		$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox({ disabled: true });
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
		$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox({ disabled: false });
		$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox({ disabled: false })
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
		renderer.clear();
		
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
				if(tipoMalla=="Especial")
				Alerts_Box("La malla cierre adecuadamente", 1);
			}
			ctx.font = "15px Arial";
			ctx.fillText("REF. " + ref + " <= " + ancho + "cm => Varillas longitud A: " + varillaA,10,15);
			renderer.text("" + arrA + " cm", 40, 85, undefined, undefined, 0, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			renderer.text("" + arrB + " cm", 100, 85, undefined, undefined, -90, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			renderer.text("" + cuadr1 + " cm", 98, 110, undefined, undefined, 0, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			renderer.text("" + cuadr2 + " cm", 98, 190, undefined, undefined, -90, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			renderer.text("" + finA + " cm", 270, 285, undefined, undefined, 0, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			renderer.text("" + finB + " cm", 275, 355, undefined, undefined, -90, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			ctx.fillText(arrA,25,65);
			ctx.fillText(finA,295,355);
			ctx.fillText(cuadr1,85,200);
		
			ctx.fillStyle = "rgb(200,0,0)";
				for (i = 1; i < 4; i++) { 				
					renderer.line(40, 100*i, 315, 100*i, { stroke: 'red', 'stroke-width': 1 });
				}
			ctx.fillStyle = "rgba(0, 0, 200, 0.5)";
				for (i = 1; i < 4; i++) { 				
					renderer.line(88*i, 40, 88*i, 370, { stroke: 'blue', 'stroke-width': 1 });
				}
			ctx.fillStyle = "rgb(0,0,0)";
			ctx.rotate(-90 * Math.PI / 180);
			ctx.fillText("<= " + largo + "cm => Varillas longitud B: " + varillaB,-380,15);
			ctx.fillText(arrB,-65,85);
			ctx.fillText(finB,-380,275);
			ctx.fillText(cuadr2,-180,155);
			
			renderer.text("REF. " + ref + " <= " + ancho + "cm => Varillas longitud A: " + varillaA, 10, 10, undefined, undefined, 0, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			renderer.text("Calibre lado A " + Producto.label, 10, 25, undefined, undefined, 0, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			renderer.text("<= " + largo + "cm => Varillas longitud B: " + varillaB, 10, 350, undefined, undefined, -90, { 'class': 'smallText' }, false, 'left', 'center', 'left');
			renderer.text("Calibre lado B " + Producto1.label, 25, 350, undefined, undefined, -90, { 'class': 'smallText' }, false, 'left', 'center', 'left');
		}
		calcularPeso();
		UpdateItems("Pesos");
    }	
	
	//--------------------------------------------------------------------------------//
	//------------------------------------ PARTE 1 -----------------------------------//
	//--------------------------------------------------------------------------------//
	
	function LoadValues(Interno)
	{
		// if (Locked == true)
			// return;
		
		// Locked = true;
		
		Interno = parseInt(Interno.replace(/\D/g,''));
		
		$.ajax({
			dataType: "json",
			type: "GET",
			url: "modulos/datos_productos.php",
			data: {"Figuracion_CargarMalla":Interno},
			success: function(data)
			{
				Locked = false;
				$("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val($("#figuracion_mallas_cartilla<?php echo $NumOfID; ?>").val());
				$("#figuracion_mallas_venta_interno<?php echo $NumOfID; ?>").val(data[0]["interno_venta"]);
				$("#figuracion_mallas_cliente_ID<?php echo $NumOfID; ?>").jqxComboBox("selectItem", data[0]["cliente_id"]);
				$("#figuracion_mallas_obra<?php echo $NumOfID; ?>").val(data[0]["obra"]);
				$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val(data[0]["pesoTotal"]);
				
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("clear");
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("clearSelection");
				var datarow = new Array();
				var len = data[0]["Items"].length;
				for (var i = 0; i < len; i++) {
					var TmpData = {
						"iditem":data[0]["Items"][i]["iditem"],
						"Tipo":data[0]["Items"][i]["tipo"],
						"Ref":data[0]["Items"][i]["ref"],
						"Cant":data[0]["Items"][i]["cant"],
						"Pelos1":data[0]["Items"][i]["arrA"],
						"Pelos2":data[0]["Items"][i]["finA"],
						"Pelos3":data[0]["Items"][i]["arrB"],
						"Pelos4":data[0]["Items"][i]["finB"],
						"Cantidad":data[0]["Items"][i]["ancho"],
						"Cantidad1":data[0]["Items"][i]["largo"],
						"Cuadricula1":data[0]["Items"][i]["sepA"],
						"Cuadricula2":data[0]["Items"][i]["sepB"],
						"Codigo":data[0]["Items"][i]["cod1"],
						"Producto":data[0]["Items"][i]["producto1"],
						"Codigo1":data[0]["Items"][i]["cod2"],
						"Producto1":data[0]["Items"][i]["producto2"],
						"VarA":data[0]["Items"][i]["varA"],
						"VarB":data[0]["Items"][i]["varB"],
						"PesoA":data[0]["Items"][i]["pesoA"],
						"PesoB":data[0]["Items"][i]["pesoB"],
						"PesoU":data[0]["Items"][i]["pesoU"],
						"Total_Peso":data[0]["Items"][i]["cant"]*data[0]["Items"][i]["pesoU"],
					};
					datarow[i] = TmpData;
				}
				datarow.reverse();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("addrow", null, datarow, "first");
				
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
		CartillasSource.data = {"Figuracion_MallasCartillas":true, "Type":"Modificar"};
		FigurasSource.data = {"Figuracion_Figuras":true};
		HierrosSource.data = {"Productos_Mallas":true};
		ProductosSource.data = {"Produccion_Hierro":true};
		
		var CartillasDataAdapter = new $.jqx.dataAdapter(CartillasSource,
		{
			autoBind: true,
			loadComplete: function (records) {
				$("#figuracion_mallas_cartilla<?php echo $NumOfID; ?>").jqxComboBox({source: records});
			},
		});
		
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
		var iditem = '';
		var txts = 'abcdefghijklmnopqrstuvwxyz';
		for (var i=0; i<4; i++)
		{
			iditem += txts.charAt(Math.floor(Math.random()*txts.length));
		}
		
		tipoMalla = $("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Ref = $("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val();
		cantidad = $("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val();
		arrA = $("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val();
		finA = $("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val();
		arrB = $("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val();
		finB = $("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val();
		ancho = $("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val();
		largo = $("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val();
		cuadr1 = $("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val();
		cuadr2 = $("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val();
		Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		if(tipoMalla.value=="Estándar"){
			Producto1 = Producto;
		} else {
			Producto1 = $("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		}
		varillaA = $("#figuracion_mallas_varA<?php echo $NumOfID; ?>").val();
		varillaB = $("#figuracion_mallas_varB<?php echo $NumOfID; ?>").val();
		pesoA = $("#figuracion_mallas_pesoA<?php echo $NumOfID; ?>").val();
		pesoB = $("#figuracion_mallas_pesoB<?php echo $NumOfID; ?>").val();
		totalM = $("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").val();
		totalMF = $("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val();
		
		if (!Producto) 
		{
			Alerts_Box("Favor Seleccionar un Figurado.", 3);
			WaitClick_Combobox("figuracion_mallas_producto<?php echo $NumOfID; ?>");
			return;
		}
		
		if (cantidad < 1) 
		{
			Alerts_Box("Favor Ingresar una Cantidad Mayor a 0.", 3);
			WaitClick_NumberInput("figuracion_mallas_cantidad<?php echo $NumOfID; ?>");
			return;
		}
		
		var FinalArray = new Array();
		
		var TmpArray = {
			"iditem":iditem,
			"Tipo":tipoMalla.value,
			"Ref":Ref,
			"Cant":cantidad,
			"Pelos1":arrA,
			"Pelos2":finA,
			"Pelos3":arrB,
			"Pelos4":finB,
			"Cantidad":ancho,
			"Cantidad1":largo,
			"Cuadricula1":cuadr1,
			"Cuadricula2":cuadr2,
			"Codigo":Producto.value,
			"Producto":Producto.label,
			"Codigo1":Producto1.value,
			"Producto1":Producto1.label,
			"VarA":varillaA,
			"VarB":varillaB,
			"PesoA":pesoA,
			"PesoB":pesoB,
			"PesoU":totalM,
			"Total_Peso":totalM*cantidad,			
		};
		FinalArray[0] = TmpArray;
		$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("addrow", null, FinalArray, "first");
		
		clearItem();
		if (Jump) {
			$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("selectrow", 0);
		}
		$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val(totalMalla());
		SaveItem(true);
	}
	
	var CartillasSource =
	{
		datatype: "json",
		datafields: [
			{ name: "cons", type: "string"},
		],
		url: "modulos/datos.php",
	};
	
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
		disabled: false
	});
	
	$("#figuracion_mallas_cartilla<?php echo $NumOfID; ?>").jqxComboBox({
		theme: mytheme,
		width: 110,
		height: 20,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Cartilla Nº',
		selectedIndex: -1,
		displayMember: 'cons',
		valueMember: 'cons'
	});
	
	$("#figuracion_mallas_cartilla<?php echo $NumOfID; ?>").on("change", function (event) {
		LoadValues(event.args.item.value);
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
		width: 230,
		height: 20,
	});
	
	$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 110,
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
				cerosPeso();
				$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val(0);
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
				Producto = "";
				mallaUnHide();
				dibujar();
				$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").jqxInput("focus");
			} else if (tipoMalla == "Especial") {
				mallaHide();
				$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val(0);
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
				$("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").val("");
				$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").val("");
				cerosPeso();
				ceroVars();
			}
			UpdateItems("Tipomalla");
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
			UpdateItems("Codigo");
			Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
			//pesoParcial = kArray[Producto.value];
			hierroSelect = Producto.label;
			calcularPeso(1);
			dibujar();
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
			
			UpdateItems("Codigo1");
			Producto1 = $("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
			pesoParcial = kArray[Producto1.value];
			hierroSelect = Producto1.label;
			calcularPeso(2);
			dibujar();
		}
	});	
	
	$("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 230,
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
		width: 230,
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
		width: 130,
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
		width: 130,
		height: 20,
		inputMode: 'simple',
		spinButtons: false,
		digits:12,
		max:999.99
	});
	$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").on("change", function (event)
	{
		cantidad = $("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val();
		UpdateItems("Cant");
		dibujar();
	});

	$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").jqxInput({
		theme: mytheme,
		width: 110,
		height: 20
	});
	$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").on("change", function (event)
	{
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
		UpdateItems("Cuadricula2");
		dibujar();
	});		
	
	$("#figuracion_mallas_add<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 110,
		height: 25,
		template: "success"
	});
	$("#figuracion_mallas_add<?php echo $NumOfID; ?>").on("click", function()
	{
		AddRow(false);
	});
	
	$("#figuracion_mallas_unselect<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 110,
		height: 25,
		template: "primary"
	});
	$("#figuracion_mallas_unselect<?php echo $NumOfID; ?>").on("click", function()
	{
		clearItem();
	});
	
	
	/*document.getElementById("figuracion_mallas_dimensiones<?php echo $NumOfID; ?>").addEventListener("focusin", function (event)
	{
		OnSelect = false;
	});*/
	
	var GridSource =
	{
		datafields:
		[
			{ name: 'iditem', type: 'string' },
			{ name: 'Tipo', type: 'string' },
			{ name: 'Ref', type: 'string' },
			{ name: 'Cant', type: 'decimal' },
			{ name: 'Pelos1', type: 'decimal' },
			{ name: 'Pelos2', type: 'decimal' },
			{ name: 'Pelos3', type: 'decimal' },
			{ name: 'Pelos4', type: 'decimal' },
			{ name: 'Cantidad', type: 'decimal' },
			{ name: 'Cantidad1', type: 'decimal' },
			{ name: 'Cuadricula1', type: 'decimal' },
			{ name: 'Cuadricula2', type: 'decimal' },
			{ name: 'Codigo', type: 'string' },
			{ name: 'Producto', type: 'string' },
			{ name: 'Codigo1', type: 'string' },
			{ name: 'Producto1', type: 'string' },
			{ name: 'VarA', type: 'decimal' },
			{ name: 'VarB', type: 'decimal' },
			{ name: 'PesoA', type: 'decimal' },
			{ name: 'PesoB', type: 'decimal' },
			{ name: 'PesoU', type: 'decimal' },
			{ name: 'Total_Peso', type: 'decimal' },
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
			$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val(totalMalla());
		},
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid(
	{
		theme: mytheme,
		width: 600,
		autoheight: true,
		source: GridDataAdapter,
		selectionmode: "singlerow",
		enabletooltips: true,
		enablebrowserselection: true,
		pageable: true,
		pagesizeoptions: ['10', '20', '30', '50', '100'],
		sortable: true,
		editable: true,
		editmode: 'dblclick',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				width: "10",
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
			{ text: 'ID', datafield: 'iditem', editable: false, width: 30, height: 20 },
			{ text: 'Tipo', datafield: 'Tipo', editable: false, width: 60, height: 20 },
			{ text: 'Ref', datafield: 'Ref', editable: false, width: 60, height: 20 },
			{
				text: 'Cant',
				datafield: 'Cant',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Arq A',
				datafield: 'Pelos1',
				editable: false,
				width: 40,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Fin A',
				datafield: 'Pelos2',
				editable: false,
				width: 40,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Arq B',
				datafield: 'Pelos3',
				editable: false,
				width: 40,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Fin B',
				datafield: 'Pelos4',
				editable: false,
				width: 40,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Ancho (A)',
				datafield: 'Cantidad',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Largo (B)',
				datafield: 'Cantidad1',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Sep A',
				datafield: 'Cuadricula1',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Sep B',
				datafield: 'Cuadricula2',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{ text: '', datafield: 'Codigo', editable: false, width: 80, height: 20 },
			{ text: 'Calibre A', datafield: 'Producto', editable: false, width: 130, height: 20 },
			{ text: '', datafield: 'Codigo1', editable: false, width: 80, height: 20 },
			{ text: 'Calibre B', datafield: 'Producto1', editable: false, width: 130, height: 20 },
			{
				text: '#Var A',
				datafield: 'VarA',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: '#Var B',
				datafield: 'VarB',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Peso A',
				datafield: 'PesoA',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Peso B',
				datafield: 'PesoB',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},
			{
				text: 'Peso/U',
				datafield: 'PesoU',
				editable: false,
				width: 60,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',
			},			
			{
				text: 'Peso',
				datafield: 'Total_Peso',
				editable: false,
				width: 90,
				height: 20,
				cellsalign: 'right',
				cellsformat: 'd2',
				columntype: 'numberinput',				
			},
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
	$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("hidecolumn", "Codigo1");
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
		SaveData();
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
		template: "primary",
		disabled: true
	});
	$("#figuracion_mallas_venta<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Modificar)
			return;
		//SaveData(true);
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
		var Interno = parseInt(($("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val()).replace(/\D/g,''));
		window.open("imprimir/cartilla_mallas.php?Interno="+Interno+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_mallas_imprimir2<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 110,
		height: 25,
		template: "warning"
	});
	$("#figuracion_mallas_imprimir2<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		var Interno = parseInt(($("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val()).replace(/\D/g,''));
		var Item = itemshow;
		window.open("imprimir/cartilla_mallaspq.php?Interno="+Interno+"&Item="+Item+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_mallas_imprimir3<?php echo $NumOfID; ?>").jqxButton({
		theme: mytheme,
		width: 110,
		height: 25,
		template: "warning"
	});
	$("#figuracion_mallas_imprimir3<?php echo $NumOfID; ?>").on("click", function()
	{
		if (!Admin && !Imprimir)
			return;
		var Interno = parseInt(($("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val()).replace(/\D/g,''));
		window.open("imprimir/cartilla_mallas.php?Interno="+Interno+"", "", "width=925, height=600, menubar=no, titlebar=no");
	});
	
	$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").jqxNumberInput({
		theme: mytheme,
		width: 110,
		height: 20,
		spinButtons: false,
		symbol: 'kg',
		symbolPosition: 'right',
		decimalDigits: 2,
		digits: 8,
		max: 99999999.99,
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
	
	function ShowItems(Index) {	
		itemshow = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "iditem");
		tipoMalla = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Tipo");
		var tmalla = tipoMalla;
		var Ref = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Ref");
		var cantidad = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Cant");
		var arrA = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Pelos1");
		var finA = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Pelos2");
		var arrB = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Pelos3");
		var finB = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Pelos4");
		var ancho = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Cantidad");
		var largo = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Cantidad1");
		var cuadr1 = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Cuadricula1");
		var cuadr2 = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Cuadricula2");
		var Producto = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Codigo");
		var Producto1 = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Codigo1");
		var varillaA = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "VarA");
		var varillaB = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "VarB");
		var pesoA = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "PesoA");
		var pesoB = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "PesoB");
		var totalM = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "PesoU");
		var totalMF = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getcellvalue", Index, "Total_Peso");
		
		var datarow = new Array();

		datarow.reverse();
		//$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox("selectItem", tipoMalla);
		$("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val(cantidad);
		$("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val(Ref);
		$("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val(arrA);
		$("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val(finA);
		$("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val(arrB);
		$("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val(finB);
		$("#figuracion_mallas_cantidad<?php echo $NumOfID; ?>").val(ancho);
		$("#figuracion_mallas_cantidad1<?php echo $NumOfID; ?>").val(largo);
		$("#figuracion_mallas_cuadricula1<?php echo $NumOfID; ?>").val(cuadr1);
		$("#figuracion_mallas_cuadricula2<?php echo $NumOfID; ?>").val(cuadr2);
		$("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").jqxComboBox("selectItem", Producto);
		$("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").jqxComboBox("selectItem", Producto1);
		$("#figuracion_mallas_varA<?php echo $NumOfID; ?>").val(varillaA);
		$("#figuracion_mallas_varB<?php echo $NumOfID; ?>").val(varillaB);
		$("#figuracion_mallas_pesoA<?php echo $NumOfID; ?>").val(pesoA);
		$("#figuracion_mallas_pesoB<?php echo $NumOfID; ?>").val(pesoB);
		$("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").val(totalM);
		$("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val(totalMF);
		Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		Producto1 = $("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		$("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox({disabled: true});
		
		
		if (tmalla=="Estándar"){
			mallaUnHide();
		} else if (tmalla=="Especial"){
			mallaHide();
		}
		obtener();
		//dibujar();
		//Calcular(false);
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
			case "Codigo":
				var Codigo = $("#figuracion_mallas_codigo<?php echo $NumOfID; ?>").val();
				var Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Codigo", Codigo);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Producto", Producto.label);				
			break;
			
			case "Codigo1":
				var Codigo1 = $("#figuracion_mallas_codigo1<?php echo $NumOfID; ?>").val();
				var Producto1 = $("#figuracion_mallas_producto1<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Codigo1", Codigo1);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Producto1", Producto1.label);
			break;
			
			case "Cant":
				var Cant = $("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Cant", Cant);
			break;
			
			case "Ref":
				var Ref = $("#figuracion_mallas_ref<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Ref", Ref);
			break;
			
			case "Pelos1":
				var Pelos1 = $("#figuracion_mallas_pelos1<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Pelos1", Pelos1);
			break;
			
			case "Pelos2":
				var Pelos2 = $("#figuracion_mallas_pelos2<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Pelos2", Pelos2);
			break;
			
			case "Pelos3":
				var Pelos3 = $("#figuracion_mallas_pelos3<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Pelos3", Pelos3);
			break;
			
			case "Pelos4":
				var Pelos4 = $("#figuracion_mallas_pelos4<?php echo $NumOfID; ?>").val();
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

			case "Tipomalla":
				var Tipomalla = $("#figuracion_mallas_tipomalla<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Tipo", Tipomalla.value);
			break;
			
			case "Pesos":
				var Cant = $("#figuracion_mallas_cant<?php echo $NumOfID; ?>").val();
				var VarA = $("#figuracion_mallas_varA<?php echo $NumOfID; ?>").val();
				var VarB = $("#figuracion_mallas_varB<?php echo $NumOfID; ?>").val();
				var PesoA = $("#figuracion_mallas_pesoA<?php echo $NumOfID; ?>").val();
				var PesoB = $("#figuracion_mallas_pesoB<?php echo $NumOfID; ?>").val();
				var PesoU = $("#figuracion_mallas_pesoT<?php echo $NumOfID; ?>").val();
				var Total_Peso = $("#figuracion_mallas_pesoFT<?php echo $NumOfID; ?>").val();
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "VarA", VarA);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "VarB", VarB);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "PesoA", PesoA);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "PesoB", PesoB);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "PesoU", PesoU);
				$("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("setcellvalue", index, "Total_Peso", Cant*PesoU);				
				$("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val(totalMalla());
			break;
		}
		
		setTimeout(function(){
			SaveItem(false);
		}, 300);
		
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
				L = LBase + LSum;
				Total += Math.round(((L * K) * C) * 100) / 100;
				//Total += (L * K) * C;
			}

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
		}
		else
		{
			var Producto = $("#figuracion_mallas_producto<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");

			
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
			
			L = LBase + LSum;
			// 2 decimals not used
			//Total = Math.round(((L * K) * C) * 100) / 100;
			Total += (((L * K) * C) * 100) / 100;
			Total = Total.toFixed(3);
			
		}
	}
	
	function SaveData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Cartilla = $("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val();
		var Generar = false;
		if (!Cartilla) {
			Generar = true;
		}
		
		
		var ventaInterno = $("#figuracion_mallas_venta_interno<?php echo $NumOfID; ?>").val();
		var Cliente = $("#figuracion_mallas_cliente<?php echo $NumOfID; ?>").jqxComboBox("getSelectedItem");
		var Obra = $("#figuracion_mallas_obra<?php echo $NumOfID; ?>").val();
		var Peso = $("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val();
		
		if (!Cliente) 
		{
			Alerts_Box("Por favor selecciona un cliente", 3);
			WaitClick_Combobox("figuracion_mallas_cliente<?php echo $NumOfID; ?>");
			Locked = false;
			return;
		}
		
		if (ventaInterno == "") 
		{
			Alerts_Box("Por favor la orden de venta vinculada", 3);
			WaitClick_Input("figuracion_mallas_venta_interno<?php echo $NumOfID; ?>");
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
		
		var datainfo = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		for (var i = 0; i < count; i++)
		{
			var TmpArray = {};
			var currentRow = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrowdata", i);
			
			if (i == 0)
			{
				TmpArray["Generar"] = Generar;
				TmpArray["Interno2"] = parseInt(Cartilla.replace(/\D/g,''));
				TmpArray["VentaInterno"] = ventaInterno;
				TmpArray["ClienteID"] = Cliente.value;
				TmpArray["Obra"] = Obra;
				TmpArray["Total_Peso"] = Peso;
			}
			
			TmpArray["iditem"] = currentRow.iditem;
			TmpArray["Tipo"] = currentRow.Tipo;
			TmpArray["Ref"] = currentRow.Ref;
			TmpArray["Cant"] = parseInt(currentRow.Cant);
			TmpArray["ArrA"] = parseFloat(currentRow.Pelos1);
			TmpArray["FinA"] = parseFloat(currentRow.Pelos2);
			TmpArray["ArrB"] = parseFloat(currentRow.Pelos3);
			TmpArray["FinB"] = parseFloat(currentRow.Pelos4);
			TmpArray["Ancho"] = parseFloat(currentRow.Cantidad);
			TmpArray["Largo"] = parseFloat(currentRow.Cantidad1);
			TmpArray["SepA"] = parseFloat(currentRow.Cuadricula1);
			TmpArray["SepB"] = parseFloat(currentRow.Cuadricula2);
			TmpArray["Cod1"] = currentRow.Codigo;
			TmpArray["Cod2"] = currentRow.Codigo1;
			TmpArray["VarA"] = parseInt(currentRow.VarA);
			TmpArray["VarB"] = parseInt(currentRow.VarB);
			TmpArray["PesoA"] = parseFloat(currentRow.PesoA);
			TmpArray["PesoB"] = parseFloat(currentRow.PesoB);
			TmpArray["PesoU"] = parseFloat(currentRow.PesoU);
			FinalArray[i] = TmpArray;
		}
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		
		if (!Cartilla)
		{
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {"Figuracion_Mallas":FinalArray},
				success: function (data)
				{
					//ReDefine();
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							//LoadParameters();
							Alerts_Box("Datos Guardados con Exito!", 2);
							$("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val("MALLA0" + data[0]["Interno"]);
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
				data: {"Figuracion_Mallas":FinalArray},
				success: function (data)
				{
					//ReDefine();
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					
					switch(data[0]["MESSAGE"])
					{
						case "ERROR":
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
						break;
						
						default:
							//LoadParameters();
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
	
	function SaveItem(Generar) {		
		var Cartilla = $("#figuracion_mallas_interno<?php echo $NumOfID; ?>").val();
		
		if (!Cartilla)
			return;
		
		var Peso = $("#figuracion_mallas_peso<?php echo $NumOfID; ?>").val();
		var FinalArray = new Array();
		var TotalArray = new Array();
		
		var datainfo = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getdatainformation");
		var count = datainfo.rowscount;
		
		var i = 0;
		var TmpArray = {};
		//var rows = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrows");
		if(SelectedRow<=0){
			SelectedRow = 0;
		}
		var currentRow = $("#figuracion_mallas_items_grid<?php echo $NumOfID; ?>").jqxGrid("getrowdata", SelectedRow);
		
		TmpArray["Generar"] = Generar;
		TmpArray["Interno2"] = parseInt(Cartilla.replace(/\D/g,''));
		TmpArray["Total_Peso"] = Peso;
		TmpArray["iditem"] =  currentRow.iditem;
		TmpArray["Tipo"] = currentRow.Tipo;
		TmpArray["Ref"] = currentRow.Ref;
		TmpArray["Cant"] = parseInt(currentRow.Cant);
		TmpArray["ArrA"] = parseFloat(currentRow.Pelos1);
		TmpArray["FinA"] = parseFloat(currentRow.Pelos2);
		TmpArray["ArrB"] = parseFloat(currentRow.Pelos3);
		TmpArray["FinB"] = parseFloat(currentRow.Pelos4);
		TmpArray["Ancho"] = parseFloat(currentRow.Cantidad);
		TmpArray["Largo"] = parseFloat(currentRow.Cantidad1);
		TmpArray["SepA"] = parseFloat(currentRow.Cuadricula1);
		TmpArray["SepB"] = parseFloat(currentRow.Cuadricula2);
		TmpArray["Cod1"] = currentRow.Codigo;
		TmpArray["Cod2"] = currentRow.Codigo1;
		TmpArray["VarA"] = parseInt(currentRow.VarA);
		TmpArray["VarB"] = parseInt(currentRow.VarB);
		TmpArray["PesoA"] = parseFloat(currentRow.PesoA);
		TmpArray["PesoB"] = parseFloat(currentRow.PesoB);
		TmpArray["PesoU"] = parseFloat(currentRow.PesoU);
		FinalArray[i] = TmpArray;
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		
		if (Generar) {
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {"Figuracion_MallasItem":FinalArray},
				success: function (data)
				{
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					
					switch(data[0]["MESSAGE"])
					{
						case "OK":
							//Alerts_Box("Ítem añadido con exito!", 2);
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
		} else {
			$.ajax({
				dataType: "json",
				type: "POST",
				url: "modulos/guardar.php",
				data: {"Figuracion_MallasItem":FinalArray},
				success: function (data)
				{
					//ReDefine();
					$("#Loading").hide();
					$("#Loading_Mess").html("Cargando...");
					Locked = false;
					
					switch(data[0]["MESSAGE"])
					{
						case "ERROR":
							Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br />Intente luego de unos segundos...", 3);
						break;
						
						default:
							//LoadParameters();
							//Alerts_Box("Datos Guardados con Exito!", 2);
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
				Cargar cartilla
			</td>	
			<td>
				Interno cartilla
			</td>		
			<td>
				Orden de venta
			</td>
			<td colspan="2">
				Cliente
			</td>
			<td>
				Cliente ID
			</td>
			<td colspan="2">
				Obra
			</td>
		</tr>
		<tr>
			<td>
				<div id="figuracion_mallas_cartilla<?php echo $NumOfID; ?>"></div>
			</td>
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
			<td colspan="2">
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
			<td colspan="2">
				<div id="figuracion_mallas_producto<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<input type="button" id="figuracion_mallas_add<?php echo $NumOfID; ?>" value="Guardar item"/>
			</td>
			<td>
				<input type="button" id="figuracion_mallas_imprimir2<?php echo $NumOfID; ?>" value="Imprimir malla"/>
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
			<td colspan="2">
				<div id="figuracion_mallas_producto1<?php echo $NumOfID; ?>"></div>
			</td>
			<td>
				<input type="button" id="figuracion_mallas_unselect<?php echo $NumOfID; ?>" value="Nuevo item"/>
			</td>
			<td>
				<input type="button" id="figuracion_mallas_imprimir3<?php echo $NumOfID; ?>" value="Imprimir cartilla"/>
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
				Peso item
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
			<td style="font-size: 14px;">
				<b>Total cartilla</b>
			</td>
			<td>
				<div id="figuracion_mallas_peso<?php echo $NumOfID; ?>"></div>
			</td>
		</tr>		

	</table>
	
	<section id="Mallas">
	<div id='container' style="width:345px; height:400px; background: white; border: 1px #a4bed4 solid; border-radius: 4px; display: inline-block;">
    </div>
		<div style="width: 360px; display: inline-block; display: none;">
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
		<tr style="height:25px;">
			<td>
				
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