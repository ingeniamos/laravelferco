<?php
	session_start();
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$CobradorID = isset($_GET['CobradorID']) ? $_GET['CobradorID']:"";
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
	
	$.ajax({
		dataType: 'json',
		url: "../modulos/datos.php",
		data: {
			"Cartera_Edades":true,
			"ClienteID":"<?php echo $ClienteID; ?>",
			"CobradorID":"<?php echo $CobradorID; ?>",
		},
		async: true,
		success: function (data, status, xhr)
		{
			var len = data.length;
			var table = "";
			
			table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
			table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
			table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
			table += "			Cliente ID";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:18%;\">";
			table += "			Cliente";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
			table += "			Total";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
			table += "			Corriente";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
			table += "			30 a 45";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
			table += "			45 a 60";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
			table += "			60 a 90";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
			table += "			Más de 90";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
			table += "			Cobr.";
			table += "		</td>";
			table += "	</tr>";
			
			for (var i = 0; i < len; i++)
			{
				if (data[i]["ClienteID"] != undefined && data[i]["ClienteID"] != "")
				{
					var Total = parseFloat(data[i]["Deuda"]);
					var A30 = parseFloat(data[i]["A30"]);
					var A45 = parseFloat(data[i]["A45"]);
					var A60 = parseFloat(data[i]["A60"]);
					var A90 = parseFloat(data[i]["A90"]);
					var Mas90 = parseFloat(data[i]["Mas90"]);
					
					table += "	<tr>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
					table += "			"+data[i]["ClienteID"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+ClienteData[data[i]["ClienteID"]];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+Total.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+A30.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+A45.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+A60.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+A90.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+Mas90.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["CobradorID"];
					table += "		</td>";
					table += "	</tr>";
				}
			}
			
			table += "</table>";
			
			var Total = parseFloat(data[0]["Total"]);
			var A30 = parseFloat(data[0]["Total_A30"]);
			var A45 = parseFloat(data[0]["Total_A45"]);
			var A60 = parseFloat(data[0]["Total_A60"]);
			var A90 = parseFloat(data[0]["Total_A90"]);
			var Mas90 = parseFloat(data[0]["Total_Mas90"]);
			
			$("#Total").val("$ "+Total.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#A30").val("$ "+A30.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#A45").val("$ "+A45.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#A60").val("$ "+A60.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#A90").val("$ "+A90.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#Mas90").val("$ "+Mas90.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			
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
		width: 900px;
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
		width: 900px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		width: 900px;
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
		width: 900px;
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
		<li style="padding:7px 0px; margin-left: 230px;">
			Reportes Edades Cartera
		</li>
		<li style="padding:2px 0px; margin-left: 160px;">
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
	<div align="center" style="width: 900px; border-bottom: #A9A9A9 1px solid; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
		Total General
	</div>
	<table class="table1" cellspacing="1" cellpadding="1" style="text-align:center;">
		<tr>
			<td>
				Total
			</td>
			<td>
				Corriente
			</td>
			<td>
				30 a 45
			</td>
			<td>
				45 a 60
			</td>
			<td>
				60 a 90
			</td>
			<td>
				Más de 90
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="Total" style="width: 140px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="A30" style="width: 140px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="A45" style="width: 140px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="A60" style="width: 140px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="A90" style="width: 140px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="Mas90" style="width: 140px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 900px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<div id="mid">
		<!--
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Cliente ID
				</td>
				<td style="border-bottom:grey 1px solid; width:18%;">
					Cliente
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Total
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Corriente
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					30 a 45
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					45 a 60
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					60 a 90
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Más de 90
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					0000000000-0
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 999.999.999.999.99
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