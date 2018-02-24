<?php
	session_start();
	
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$VendedorID = isset($_GET['VendedorID']) ? $_GET['VendedorID']:"";
	$CobradorID = isset($_GET['CobradorID']) ? $_GET['CobradorID']:"";
	$Clasificacion = isset($_GET['Clasificacion']) ? $_GET['Clasificacion']:"";
	$Tipo = isset($_GET['Tipo']) ? $_GET['Tipo']:"";
	$Estado = isset($_GET['EstadoC']) ? $_GET['EstadoC']:"";
	$Garantias = isset($_GET['Garantia']) ? $_GET['Garantia']:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	
	function SetFormattedDate (MyDate)
	{
		var MonthArray = new Array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic")
		MyDate = MyDate + ""; //to string
		
		if (MyDate.indexOf(":") < 0)
		{
			var Tmp = MyDate.split(/[-]/);
		}
		else
		{
			var Tmp = MyDate.split(/[- :]/);
		}
		
		var day = Tmp[2];
		var month = MonthArray[Tmp[1]-1];
		var year = Tmp[0];
		
		day = day + "";// to string
		if (day.length == 1)
		{
			day = "0" + day;
		};
		
		var FormatedDate = day + "-" + month + "-" +  year;
		return FormatedDate;
	};

	function FormatedDate()
	{
		var MyDate = new Date();
		var day = MyDate.getDate();
		var month = MyDate.getMonth();
		var year = MyDate.getFullYear();
		var seconds = MyDate.getSeconds();
		var minutes = MyDate.getMinutes();
		var hours = MyDate.getHours();
		
		//day = day + 1;
		day = day + "";// to string
		if (day.length == 1)
		{
			day = "0" + day;
		};
		
		month = month + 1;
		month = month + "";// to string
		if (month.length == 1)
		{
			month = "0" + month;
		};
		
		seconds = seconds + "";// to string
		if (seconds.length == 1)
		{
			seconds = "0" + seconds;
		};
		
		minutes = minutes + "";// to string
		if (minutes.length == 1)
		{
			minutes = "0" + minutes;
		};
		
		hours = hours + "";// to string
		if (hours.length == 1)
		{
			hours = "0" + hours;
		};
		
		var Today = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
		return Today;
	}
	
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	$.ajax({
		dataType: 'json',
		url: "../modulos/datos.php",
		data: {
			"Terceros_Listado":true,
			"ClienteID":"<?php echo $ClienteID; ?>",
			"VendedorID":"<?php echo $VendedorID; ?>",
			"CobradorID":"<?php echo $CobradorID; ?>",
			"Clasificacion":"<?php echo $Clasificacion; ?>",
			"Tipo":"<?php echo $Tipo; ?>",
			"EstadoC":"<?php echo $Estado; ?>",
			"Garantia":"<?php echo $Garantias; ?>"
		},
		async: true,
		success: function (data, status, xhr)
		{
			if (data[0]["ClienteID"] == undefined)
				return;
			
			var table = "";
			table += "<table class=\"mytable\" cellspacing=\"0\" cellpadding=\"2\">";
			table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
			table += "		<td style=\"border-bottom:grey 1px solid; width:4%;\">";
			table += "			Vend";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:4%;\">";
			table += "			Cobr";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
			table += "			Cliente";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
			table += "			Cliente ID";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
			table += "			Contacto Principal";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
			table += "			Telefono(CP)";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
			table += "			Clasificacion";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
			table += "			Tipo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Estado";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
			table += "			Direccion";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
			table += "			Barrio";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
			table += "			Ciudad";
			table += "		</td>";
			table += "	</tr>";
			//table += "	<tr>";
			
			var len = data.length;
			for (var i = 0; i < len; i++)
			{
				// data[i]["Clasificacion"]
				table += "<tr>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["VendedorID"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["CobradorID"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Nombre"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
				table += "		"+data[i]["ClienteID"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["ContactoCP"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["TelefonoCP"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Clasificacion"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Tipo"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Estado"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Direccion"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Barrio"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Ciudad"];
				table += "	</td>";
				table += "</tr>";
			}
			table += "</table>";
			$("#mid").html(table);
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert(textStatus+ " - " +errorThrown);
		}
	});
	
	$("#print_button").bind('click', function ()
	{
		$("#fecha_impreso").val(FormatedDate());
		window.print()
	});
});
</script>
<style type="text/css">
	.myinput {
		font-size: 12px;
		padding: 2px;
		font-family:calibri;
		height: 20px;
	}
	
	#header{
		width: 900px;
		height: 40px;
		font-family:calibri;
		background: #DEDEDE;
		font-size: 20px;
		list-style:none;
	}
	#header li{
		float: left;
		padding 2px;
	}
	
	.mytable{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 900px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 900px;
	}
	
	#print_button{
		width:100px;
		height:25px;
		margin-top:20px;
		display: block;
		color: #FFF;
		background-color: #FAA732;
		border-color: #FAA732;
	}
	
	@media print {
		#header{
			-webkit-print-color-adjust: exact;
		}
		.mytable{
			-webkit-print-color-adjust: exact;
		}
		input,
		textarea {
			border: #A9A9A9 1px solid !important;
			box-shadow: none !important;
			outline: none !important;
		}
		#print_button {
			display: none;
		}
	}
	@page 
	{
		margin: 0.5cm 0.5cm 1.0cm 0.5cm;
	}
</style>
<div>
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 240px;">
			Listado de Clientes
		</li>
		<li style="padding:2px 0px; margin-left: 205px;">
			<table  cellspacing="0" cellpadding="0" style="font-size:12px; text-align:center;">
				<tr>
					<td>
						Fecha
					</td>
					<td>
						Impreso
					</td>
				</tr>
				<tr>
					<td style="padding-right:3px;">
						<input type="text" id="fecha_impreso" class="myinput" style="width: 120px; text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="impreso_por" class="myinput" style="width: 60px; text-align:center;" readonly/>
					</td>
				</tr>
			</table>
		</li>
	</div>
	<div id="mid">
		<!--
		<table class="mytable" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:4%;">
					Vend
				</td>
				<td style="border-bottom:grey 1px solid; width:4%;">
					Cobr
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Cliente
				</td>
				<td style="border-bottom:grey 1px solid; width:9%;">
					Cliente ID
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Contacto Principal
				</td>
				<td style="border-bottom:grey 1px solid; width:9%;">
					Telefono(CP)
				</td>
				<td style="border-bottom:grey 1px solid; width:7%;">
					Clasificacion
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Tipo
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Estado
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Direccion
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Barrio
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Ciudad
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					BOX
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					BOX
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDASDASDASDASDASDASDASDSADASDASDASDASDSA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					132456789-0
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FGHFGHFGHGFHGFHGFHGFHGFHGFHGFHGFHGFHGFHGFHGFHGF
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					000-1111-22
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					CLIENTE
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					OCACIONAL
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					Al Dia
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					SFSDF34R3445G547645Y45YHRYE457YE7E
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					JKLJKLJKLJKLJKLJLKJLJKLJKLJKLJKLJK
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					CUCUTA
				</td>
			</tr>
		</table>
		-->
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />