<?php
	session_start();
	//---
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$SubGrupo2 = isset($_GET['SubGrupo2']) ? $_GET['SubGrupo2']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$DigitadorID = isset($_GET['DigitadorID']) ? $_GET['DigitadorID']:"";
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
			"Reportes_Caja":true,
			"OrderBy":"<?php echo $OrderBy; ?>",
			"Categoria":"<?php echo $Categoria; ?>",
			"Grupo":"<?php echo $Grupo; ?>",
			"SubGrupo":"<?php echo $SubGrupo; ?>",
			"SubGrupo2":"<?php echo $SubGrupo2; ?>",
			"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
			"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			"ClienteID":"<?php echo $ClienteID; ?>",
			"DigitadorID":"<?php echo $DigitadorID; ?>",
		},
		async: true,
		success: function (data, status, xhr)
		{
			var len = data.length;
			var table = "";
			var myarray = new Array();
			var Viejo_ID = "_empty_";
			var Index = 0;
			var Valor = 0;
			var Total = 0;
			
			if ("<?php echo $OrderBy; ?>" == "Cliente")
			{
				for (var i = 0; i < len; i++)
				{
					if (data[i]["ClienteID"] != undefined)
					{
						if (Viejo_ID != data[i]["ClienteID"])
						{
							Valor = 0;
							Viejo_ID = data[i]["ClienteID"];
							
							for (Index = i; Index < len; Index++)
							{
								if (Viejo_ID != data[Index]["ClienteID"])
									break;
								
								Valor = + Valor + parseFloat(data[Index]["Valor"]);
							}
							
							Total = + Total + Valor;
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:10%;\">";
							table += "			Beneficiario";
							table += "		</td>";
							table += "		<td style=\"width:45%;\">";
							table += "			<input type=\"text\" style=\"width: 300px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Nombre"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:10%;\">";
							table += "			Valor";
							table += "		</td>";
							table += "		<td style=\"width:25%;\">";
							table += "			<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-bottom:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
							table += "			Grupo";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
							table += "			SubGrupo";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:18%;\">";
							table += "			RC";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:26%;\">";
							table += "			Valor";
							table += "		</td>";
							table += "	</tr>";
							
							Valor = parseFloat(data[i]["Valor"]);
							
							table += "<tr>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Grupo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["SubGrupo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Caja_Recibo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+SetFormattedDate(data[i]["Fecha"]);
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "		$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "	</td>";
							table += "</tr>";
						}
						else
						{
							Valor = parseFloat(data[i]["Valor"]);
							
							table += "<tr>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Grupo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["SubGrupo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Caja_Recibo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+SetFormattedDate(data[i]["Fecha"]);
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "		$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "	</td>";
							table += "</tr>";
						}
					}
				}
			}
			else 
			{
				for (var i = 0; i < len; i++)
				{
					if (data[i]["SubGrupo"] != undefined)
					{
						if (Viejo_ID != ""+data[i]["Grupo"]+data[i]["SubGrupo2"]+data[i]["SubGrupo"]+"")
						{
							Valor = 0;
							Viejo_ID = ""+data[i]["Grupo"]+data[i]["SubGrupo2"]+data[i]["SubGrupo"]+"";
							
							for (Index = i; Index < len; Index++)
							{
								if (Viejo_ID != ""+data[Index]["Grupo"]+data[Index]["SubGrupo2"]+data[Index]["SubGrupo"]+"")
									break;
								
								Valor = + Valor + parseFloat(data[Index]["Valor"]);
							}
							
							Total = Total + Valor;
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:20%;\">";
							table += "			Grupo";
							table += "		</td>";
							table += "		<td style=\"width:25%;\">";
							table += "			SubGrupo 2";
							table += "		</td>";
							table += "		<td style=\"width:25%;\">";
							table += "			SubGrupo";
							table += "		</td>";
							table += "		<td style=\"width:30%;\">";
							table += "			Total";
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr style=\"background: #DEDEDE;\">";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 115px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Grupo"]+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 140px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["SubGrupo2"]+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 145px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["SubGrupo"]+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 170px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-bottom:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
							table += "			RC";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:45%;\">";
							table += "			Beneficiario";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
							table += "			Valor";
							table += "		</td>";
							table += "	</tr>";
							
							Valor = parseFloat(data[i]["Valor"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Caja_Recibo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
							
						}
						else
						{
							Valor = parseFloat(data[i]["Valor"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Caja_Recibo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
					}
				}
			}
			
			table += "</table>";
			
			$("#total_valor").val("$ "+Total.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			if ("<?php echo $Categoria; ?>" == "")
				$("#tipo_mov").val("Todos");
			else
				$("#tipo_mov").val("<?php echo $Categoria; ?>");
			$("#fecha_ini").val("<?php echo $Fecha_Ini; ?>");
			$("#fecha_fin").val("<?php echo $Fecha_Fin; ?>");
				
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
		window.print();
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
		width: 600px;
		height: 40px;
		font-family:calibri;
		background: #DEDEDE;
		font-size: 20px;
		list-style:none;
	}
	#header li{
		float: left;
		padding: 2px;
	}
	
	.table1{
		width:500px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		margin-left: 50px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		width: 600px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
		list-style:none;
	}
	
	.table2 li{
		float: left;
		padding: 2px;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 600px;
		list-style:none;
	}
	
	#print_button{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #FAA732;
		border-color: #FAA732;
	}
	
	#export_button{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #5BB75B;
		border-color: #5BB75B;
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
		#export_button {
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
		<li style="padding:7px 0px; margin-left: 105px;">
			Informe de Caja
		</li>
		<li style="padding:2px 0px; margin-left: 55px;">
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
	<div align="center" style="width: 600px; border-bottom: #A9A9A9 1px solid; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
		Total General
	</div>
	<table class="table1" cellspacing="1" cellpadding="1" style="text-align:center;">
		<tr>
			<td>
				Fecha Inicio
			</td>
			<td>
				Fecha Fin
			</td>
			<td>
				Tipo Mov.
			</td>
			<td>
				Total Valor
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
				<input type="text" id="tipo_mov" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_valor" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 600px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<div id="mid">
		<!--
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:10%;">
					Beneficiario
				</td>
				<td style="width:45%;">
					<input type="text" style="width: 300px; height: 20px;" class="myinput" readonly/>
				</td>
				<td style="width:10%;">
					Valor
				</td>
				<td style="width:25%;">
					<input type="text" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:20%;">
					Grupo
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					SubGrupo
				</td>
				<td style="border-bottom:grey 1px solid; width:18%;">
					RC
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:26%;">
					Valor
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER00005555
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					2015-10-10
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
			</tr>
		</table>
		-->
		<!--
		<table class="table2" cellspacing="0" cellpadding="2" style="width:600px; margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:20%;">
					Grupo
				</td>
				<td style="width:25%;">
					SubGrupo 2
				</td>
				<td style="width:25%;">
					SubGrupo
				</td>
				<td style="width:30%;">
					Total
				</td>
			</tr>
			<tr style="background: #DEDEDE;">
				<td>
					<input type="text" style="width: 115px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 140px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 145px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2" style="width:600px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:15%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					RC
				</td>
				<td style="border-bottom:grey 1px solid; width:45%;">
					Beneficiario
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					Valor
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					2015-10-10
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER00005555
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
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