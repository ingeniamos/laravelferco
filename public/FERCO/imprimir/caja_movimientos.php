<?php
	session_start();
	$Caja_Recibo = isset($_GET['Caja_Recibo']) ? $_GET['Caja_Recibo']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$DigitadorID = isset($_GET['DigitadorID']) ? $_GET['DigitadorID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	var ClienteData = [];
	
	$.ajax(
	{
		dataType: 'json',
		url: "../modulos/datos.php",
		async: false,
		success: function (data)
		{
			for (var i = 0; i < data.length; i++)
			{
				ClienteData[""+data[i]["ClienteID"]+""] = data[i]["Nombre"];
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(textStatus+ " - " +errorThrown);
		}
	});
	
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
	
	if ("<?php echo $Fecha_Ini; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Imprimir_Caja_Movimientos":true,
				"Caja_Recibo":"<?php echo $Caja_Recibo; ?>",
				"ClienteID":"<?php echo $ClienteID; ?>",
				"DigitadorID":"<?php echo $DigitadorID; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
				"Estado":"<?php echo $Estado; ?>",
			},
			async: true,
			success: function (data, status, xhr)
			{
				var table = "";
				var LastClientID = "_empty_";
				var len = data.length;
				for (var i = 0; i < len; i++)
				{
					if (data[i]["ClienteID"] != undefined)
					{
						if (LastClientID != data[i]["ClienteID"])
						{
							LastClientID = data[i]["ClienteID"];
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:10%;\">";
							table += "			Beneficiario";
							table += "		</td>";
							table += "		<td style=\"width:43%; text-align:left;\">";
							table += "			<input type=\"text\" id=\"cliente\" style=\"width: 300px; height: 20px;\" class=\"myinput\" readonly value=\""+ClienteData[data[i]["ClienteID"]]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:10%;\">";
							table += "			Total Valor";
							table += "		</td>";
							table += "		<td style=\"width:37%; text-align:left;\">";
							table += "			<input type=\"text\" id=\"cliente_total_valor\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+data[i]["Cliente_Total_Valor"]+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Grupo";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			SubGrupo";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			R. Caja";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
							table += "			Valor";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Dig.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Apr.";
							table += "		</td>";
							table += "	</tr>";
						}
						
						if (data[i]["ClienteID"] != "")
						{
							table += "<tr>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+SetFormattedDate(data[i]["Fecha"]);
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Grupo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["SubGrupo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Caja_Recibo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "		$ "+data[i]["Valor"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+data[i]["DigitadorID"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+data[i]["AprobadorID"];
							table += "	</td>";
							table += "</tr>";
						}
					}
				}
				table += "</table>";
				
				$("#fecha_ini").val("<?php echo $Fecha_Ini; ?>");
				$("#fecha_fin").val("<?php echo $Fecha_Fin; ?>");
				
				$("#mid").html(table);
			}, 
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
	}	
	
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
		width: 710px;
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
		width: 320px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		margin-left: 200px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		width: 710px;
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
		width: 710px;
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
	}
</style>
<div>
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 110px;">
			Informe de Caja por Clientes
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
	<table class="table1" cellspacing="1" cellpadding="1" style="text-align:center;">
		<tr>
			<td>
				Fecha Inicio
			</td>
			<td>
				<input type="text" id="fecha_ini" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Fecha Fin
			</td>
			<td>
				<input type="text" id="fecha_fin" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:10%;">
					Beneficiario
				</td>
				<td style="width:43%; text-align:left;">
					<input type="text" id="cliente" style="width: 300px; height: 20px;" class="myinput" readonly/>
				</td>
				<td style="width:10%;">
					Total Valor
				</td>
				<td style="width:37%; text-align:left;">
					<input type="text" id="cliente_total_valor" style="width: 120px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:15%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Grupo
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					SubGrupo
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					R. Caja
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					Valor
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Dig.
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Apr.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					Nomina
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					Prestamo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					BOX
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					BOX
				</td>
			</tr>
		</table>
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />