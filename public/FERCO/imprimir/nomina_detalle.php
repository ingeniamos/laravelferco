<?php
	session_start();
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:false;
	$Quincenal = isset($_GET['Quincenal']) ? $_GET['Quincenal']:false;
	$EmpleadoID = isset($_GET['EmpleadoID']) ? $_GET['EmpleadoID']:"";
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
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
	
	//$("#fecha_impreso").val(FormatedDate());
	//$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	$.ajax({
		dataType: 'json',
		url: "../modulos/datos.php",
		data: {
			"Nomina":true,
			"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
			"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			"Grupo":"<?php echo $Grupo; ?>",
			"Quincenal":"<?php echo $Quincenal; ?>",
			"EmpleadoID":"<?php echo $EmpleadoID; ?>",
			"Estado":"<?php echo $Estado; ?>",
		},
		success: function (data, status, xhr)
		{
			var num_space = 2;
			var table = "";
			var space = "<div style=\"margin-bottom:3.0cm\"></div>";
			
			var len = data.length;
			for (var i = 0; i < len; i++)
			{
				if (data[i]["ClienteID"] != "" && data[i]["ClienteID"] != undefined)
				{
					if (i == num_space)
					{
						table += space;
						num_space = +num_space + 2;
					}
					
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
			
					table += "<div id=\"page\" style=\"margin-bottom: 1.0cm;\">";
					table += "	<div id=\"header\">";
					table += "		<li>";
					table += "			<img src=\"../images/logo.png\" alt=\"Ferco LTDA\" width=\"134\" height=\"30\" style=\"padding:5px 0px 5px 5px;\">";
					table += "		</li>";
					table += "		<li style=\"padding:8px 0px; margin-left: 20px;\">";
					table += "			Detalle Liquidacion de Nomina";
					table += "		</li>";
					table += "		<li style=\"padding:5px 0px; margin-left: 20px;\">";
					table += "			<table  cellspacing=\"0\" cellpadding=\"0\" style=\"font-size:12px; text-align:center;\">";
					table += "				<tr>";
					table += "					<td>";
					table += "						Fecha";
					table += "					</td>";
					table += "					<td>";
					table += "						Impreso";
					table += "					</td>";
					table += "				</tr>";
					table += "				<tr>";
					table += "					<td style=\"padding-right:3px;\">";
					table += "						<input type=\"text\" id=\"fecha_impreso\" class=\"myinput\" style=\"width: 120px; text-align:center;\" readonly value=\""+FormatedDate()+"\" />";
					table += "					</td>";
					table += "					<td>";
					table += "						<input type=\"text\" id=\"impreso_por\" class=\"myinput\" style=\"width: 60px; text-align:center;\" readonly value=\"<?php echo $_SESSION["UserCode"]; ?>\" />";
					table += "					</td>";
					table += "				</tr>";
					table += "			</table>";
					table += "		</li>";
					table += "	</div>";
					
					table += "	<table class=\"table\" cellspacing=\"2\" cellpadding=\"2\">";
					table += "		<tr>";
					table += "			<td style=\"padding-right: 40px;\">";
					table += "				Empleado";
					table += "			</td>";
					table += "			<td colspan=\"3\">";
					table += "				<input type=\"text\" style=\"width: 373px; height: 20px; text-align:left;\" class=\"myinput\" readonly value=\""+data[i]["Nombre"]+"\" />";
					table += "			</td>";
					table += "			<td colspan=\"2\">";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+data[i]["ClienteID"]+"\" />";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td>";
					table += "				Salario (Mes)";
					table += "			</td>";
					table += "			<td style=\"padding-right: 45px;\">";
					table += "				<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Basico.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td style=\"padding-right: 20px;\">";
					table += "				Horas de Ley (Mes)";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 50px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+data[i]["Horas"]+"\" />";
					table += "			</td>";
					table += "			<td style=\"padding-right: 30px;\">";
					table += "				Horas Lab.";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 50px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+data[i]["Horas_Lab"]+"\" />";
					table += "			</td>";
					table += "		</tr>";
					table += "	</table>";
					
					table += "	<table class=\"table\" cellspacing=\"2\" cellpadding=\"2\">";
					table += "		<tr>";
					table += "			<td colspan=\"2\">";
					table += "				<div style=\"padding: 1px; background-color: steelblue; color: white; text-align:left;\">";
					table += "					Devengados";
					table += "				</div>";
					table += "			</td>";
					table += "			<td colspan=\"2\">";
					table += "				<div style=\"padding: 1px; background-color: firebrick; color: white; text-align:left;\">";
					table += "					Deducidos";
					table += "				</div>";
					table += "			</td>";
					table += "			<td colspan=\"2\">";
					table += "				<div style=\"padding: 1px; background-color: seagreen; color: white; text-align:left;\">";
					table += "					Detalle de Horas";
					table += "				</div>";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td>";
					table += "				Básico";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+BasicoF.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td>";
					table += "				Salud";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Salud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td>";
					table += "				Extras";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 50px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+data[i]["Horas_Ext"]+"\" />";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td style=\"padding-right: 5px;\">";
					table += "				Subs. Transporte";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Transporte.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td>";
					table += "				Pension";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$"+Pension.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td style=\"padding-right: 5px;\">";
					table += "				Permisos/Otros";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 50px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+data[i]["Horas_Desc"]+"\" />";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td>";
					table += "				Bonificación";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Bono.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";;
					table += "			</td>";
					table += "			<td>";
					table += "				Prestamo";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Prestamo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td>";
					table += "				Repuestas";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 50px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+data[i]["Horas_Rep"]+"\" />";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td>";
					table += "				Horas Extras";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Extras.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td>";
					table += "				Libranza";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Libranza.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td colspan=\"2\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td colspan=\"2\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "			<td>";
					table += "				Anticipos";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Anticipo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td colspan=\"2\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td colspan=\"2\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "			<td>";
					table += "				Donacion";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Donacion.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td colspan=\"2\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td colspan=\"2\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "			<td>";
					table += "				Multa";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Multa.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td colspan=\"2\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td>";
					table += "				Total Devengado";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right; background-color: lightsteelblue;\" class=\"myinput\" readonly value=\"$ "+Devengado.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td style=\"padding-right: 5px;\">";
					table += "				Total Deducido";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 135px; height: 20px; text-align:right; background-color: lightcoral;\" class=\"myinput\" readonly value=\"$ "+Deducido.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td>";
					table += "				Total Horas";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 50px; height: 20px; text-align:center; background-color: lightgreen;\" class=\"myinput\" readonly value=\""+data[i]["Horas_Lab"]+"\" />";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td colspan=\"6\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "		</tr>";
					table += "		<tr>";
					table += "			<td>";
					table += "				<div style=\"padding: 3px; background-color: royalblue; color: white; text-align:left;\">";
					table += "					TOTAL A PAGAR";
					table += "				</div>";
					table += "			</td>";
					table += "			<td>";
					table += "				<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right; background-color: lightblue;\" class=\"myinput\" readonly value=\"$ "+Neto.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\" />";
					table += "			</td>";
					table += "			<td colspan=\"4\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "		</tr>";
					table += "	</table>";
					table += "</div>";
				}
			}
			
			table += "<div id=\"bottom\" align=\"center\">";
			table +=	"<input type=\"button\" id=\"print_button\" value=\"Imprimir\"/>";
			table += "</div>";
			
			$("#main").html(table);
			
			$("#print_button").bind('click', function ()
			{
				$("#fecha_impreso").val(FormatedDate());
				window.print()
			});
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert(textStatus+ " - " +errorThrown);
		}
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
		width: 616px;
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
	
	.table{
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	#bottom{
		top: 10px;
		right: 10px;
		position: fixed;
	}
	
	#print_button{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #FAA732;
		border-color: #FAA732;
	}
	
	@media print {
		#header{
			-webkit-print-color-adjust: exact;
		}
		.table{
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
<div id="main">
	<div id="page" style="margin-bottom: 1.0cm;">
		<div id="header">
			<li>
				<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
			</li>
			<li style="padding:7px 0px; margin-left: 30px;">
				Detalle Liquidacion de Nomina
			</li>
			<li style="padding:2px 0px; margin-left: 30px;">
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
		
		<table class="table" cellspacing="2" cellpadding="2">
			<tr>
				<td style="padding-right: 40px;">
					Empleado
				</td>
				<td colspan="3">
					<input type="text" id="empleado" style="width: 373px; height: 20px; text-align:left;" class="myinput" readonly/>
				</td>
				<td colspan="2">
					<input type="text" id="empleado_id" style="width: 135px; height: 20px; text-align:center;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Salario (Mes)
				</td>
				<td style="padding-right: 45px;">
					<input type="text" id="salario" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td style="padding-right: 20px;">
					Horas de Ley (Mes)
				</td>
				<td>
					<input type="text" id="horas" style="width: 50px; height: 20px; text-align:center;" class="myinput" readonly/>
				</td>
				<td style="padding-right: 30px;">
					Horas Lab.
				</td>
				<td>
					<input type="text" id="horas_lab" style="width: 50px; height: 20px; text-align:center;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		
		<table class="table" cellspacing="2" cellpadding="2">
			<tr>
				<td colspan="2">
					<div style="padding: 1px; background-color: steelblue; color: white; text-align:left;">
						Devengados
					</div>
				</td>
				<td colspan="2">
					<div style="padding: 1px; background-color: firebrick; color: white; text-align:left;">
						Deducidos
					</div>
				</td>
				<td colspan="2">
					<div style="padding: 1px; background-color: seagreen; color: white; text-align:left;">
						Detalle de Horas
					</div>
				</td>
			</tr>
			<tr>
				<td>
					Básico
				</td>
				<td>
					<input type="text" id="basico" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					Salud
				</td>
				<td>
					<input type="text" id="salud" style="width: 135px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					Extras
				</td>
				<td>
					<input type="text" id="extras" style="width: 50px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td style="padding-right: 5px;">
					Subs. Transporte
				</td>
				<td>
					<input type="text" id="basico" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					Pension
				</td>
				<td>
					<input type="text" id="salud" style="width: 135px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td style="padding-right: 5px;">
					Permisos/Otros
				</td>
				<td>
					<input type="text" id="extras" style="width: 50px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Bonificación
				</td>
				<td>
					<input type="text" id="basico" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					Prestamo
				</td>
				<td>
					<input type="text" id="salud" style="width: 135px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					Repuestas
				</td>
				<td>
					<input type="text" id="extras" style="width: 50px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Horas Extras
				</td>
				<td>
					<input type="text" id="basico" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					Libranza
				</td>
				<td>
					<input type="text" id="salud" style="width: 135px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
				<td>
					Anticipos
				</td>
				<td>
					<input type="text" id="salud" style="width: 135px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
				<td>
					Donacion
				</td>
				<td>
					<input type="text" id="salud" style="width: 135px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
				<td>
					Multa
				</td>
				<td>
					<input type="text" id="salud" style="width: 135px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					Total Devengado
				</td>
				<td>
					<input type="text" id="salud" style="width: 150px; height: 20px; text-align:right; background-color: lightsteelblue;" class="myinput" readonly/>
				</td>
				<td style="padding-right: 5px;">
					Total Deducido
				</td>
				<td>
					<input type="text" id="salud" style="width: 135px; height: 20px; text-align:right; background-color: lightcoral;" class="myinput" readonly/>
				</td>
				<td>
					Total Horas
				</td>
				<td>
					<input type="text" id="salud" style="width: 50px; height: 20px; text-align:right; background-color: lightgreen;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					<div style="padding: 3px; background-color: royalblue; color: white; text-align:left;">
						TOTAL A PAGAR
					</div>
				</td>
				<td>
					<input type="text" id="salud" style="width: 150px; height: 20px; text-align:right; background-color: lightblue;" class="myinput" readonly/>
				</td>
				<td colspan="4">
					&nbsp;
				</td>
			</tr>
		</table>		
	</div>
	
	<div style="margin-bottom:3.0cm"></div>

	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>