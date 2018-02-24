<?php
	session_start();
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$Quincenal = isset($_GET['Quincenal']) ? $_GET['Quincenal']:false;
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:false;
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
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
			"Nomina":true,
			"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
			"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			"Grupo":"<?php echo $Grupo; ?>",
			"Quincenal":"<?php echo $Quincenal; ?>",
			"Estado":"<?php echo $Estado; ?>",
			//"ImprimirBase":true,
		},
		success: function (data, status, xhr)
		{
			var table = "";
			table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
			table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
			table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
			table += "			Nombre";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
			table += "			Basico";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:3%;\">";
			table += "			Horas";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
			table += "			Basico";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
			table += "			Bono";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Sub Transp";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Extras";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
			table += "			Devengado";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Salud";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Pension";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Retencion";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Prestamo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Libranza";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Anticipo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Donacion";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
			table += "			Multa";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
			table += "			Deducido";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
			table += "			Neto";
			table += "		</td>";
			table += "	</tr>";
			
			var TotalDevengado = 0;
			var TotalDeducido = 0;
			var TotalNeto = 0;
			
			var len = data.length;
			for (var i = 0; i < len; i++)
			{
				if (data[i]["ClienteID"] != "" && data[i]["ClienteID"] != undefined)
				{
					var Basico = parseFloat(data[i]["Basico"]);
					var BasicoF = parseFloat(data[i]["Basico_Final"]);
					var Bono = parseFloat(data[i]["Bono"]);
					var Transporte = parseFloat(data[i]["Transporte"]);
					var Extras = parseFloat(data[i]["Extras"]);
					var Devengado = parseFloat(data[i]["Devengado"]);
					var Salud = parseFloat(data[i]["Salud"]);
					var Pension = parseFloat(data[i]["Pension"]);
					var Retencion = parseFloat(data[i]["Retencion"]);
					var Prestamo = parseFloat(data[i]["Prestamo"]);
					var Libranza = parseFloat(data[i]["Libranza"]);
					var Anticipo = parseFloat(data[i]["Anticipo"]);
					var Donacion = parseFloat(data[i]["Donacion"]);
					var Multa = parseFloat(data[i]["Multa"]);
					var Deducido = parseFloat(data[i]["Deducido"]);
					var Neto = parseFloat(data[i]["Neto"]);
					
					TotalDevengado = TotalDevengado + Devengado;
					TotalDeducido = TotalDeducido + Deducido;
					TotalNeto = TotalNeto + Neto;
					
					table += "<tr>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "		"+data[i]["Nombre"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Basico.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "		"+data[i]["Horas_Lab"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+BasicoF.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Bono.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Transporte.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Extras.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Devengado.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Salud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Pension.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Retencion.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Prestamo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Libranza.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Anticipo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Donacion.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Multa.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Deducido.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+Neto.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "	</td>";
					table += "</tr>";
				}
			}
			table += "</table>";
			
			$("#fecha_ini").val("<?php echo $Fecha_Ini; ?>");
			$("#fecha_fin").val("<?php echo $Fecha_Fin; ?>");
			$("#total_devengado").val("$ "+TotalDevengado.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#total_deducido").val("$ "+TotalDeducido.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#total_neto").val("$ "+TotalNeto.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			
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
		width: 1200px;
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
	
	.table1{
		width: 500px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		margin-left: 340px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 1200px;
		text-align: center;
		font-size: 10px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 1200px;
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
		.table1{
			-webkit-print-color-adjust: exact;
		}
		.table2{
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
		size: landscape;
	}
</style>
<div>
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 450px;">
			Nomina
		</li>
		<li style="padding:2px 0px; margin-left: 380px;">
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
	
	<table class="table1" cellspacing="1" cellpadding="1" style="text-align:center;">
		<tr style="text-align:center; font-size: 11px;">
			<td>
				Desde
			</td>
			<td>
				Hasta
			</td>
			<td>
				Total Devengado
			</td>
			<td>
				Total Deducido
			</td>
			<td>
				Neto
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="fecha_ini" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="fecha_fin" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_devengado" style="width: 120px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_deducido" style="width: 120px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_neto" style="width: 120px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	
	<div id="mid">
		<!--
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 11px;">
				<td style="border-bottom:grey 1px solid; width:8%;">
					Nombre
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Basico
				</td>
				<td style="border-bottom:grey 1px solid; width:3%;">
					Horas
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Basico
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Bono
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Sub Transp
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Extras
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Devengado
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Salud
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Pension
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Retencion
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Prestamo
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Libranza
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Anticipo
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Donacion
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Multa
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Deducido
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Neto
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Cesantia
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDASDASDSADASD ADASDASDAS ASDASDASDASDASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					999
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.99
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