<?php
	session_start();
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	function SetFormattedDate (MyDate)
	{
		if (MyDate == "0000-00-00")
			return MyDate;
		
		var MonthArray = new Array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic")
		MyDate = MyDate + ""; //to string
		
		var Tmp = MyDate.split(/[-]/);
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
			data:{
				"Imprimir_Produccion_Total":true,
				"ClienteID":"<?php echo $ClienteID; ?>",
				"Estado":"<?php echo $Estado; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			},
			async: true,
			success: function (data, status, xhr)
			{
				if (data[0]["Operario"] == undefined)
					return;
				
				var len = data.length;
				Viejo_Operario = "_empty_";
				var table = "";
				var TotalPeso = 0;
				var OperarioPeso = [];
				for (var i = 0; i < len; i++)
				{
					if (Viejo_Operario != data[i]["Operario"])
					{
						OperarioPeso[""+data[i]["Operario"]+""] = data[i]["Peso"];
						Viejo_Operario = data[i]["Operario"];
						TotalPeso = data[i]["Peso"];
					}
					else
					{
						TotalPeso += data[i]["Peso"];
						OperarioPeso[""+data[i]["Operario"]+""] += data[i]["Peso"];
					}
				}
				Viejo_Operario = "_empty_";
				for (var i = 0; i < len; i++)
				{
					if (Viejo_Operario != data[i]["Operario"])
					{
						Viejo_Operario = data[i]["Operario"];
						TotalPeso = OperarioPeso[""+data[i]["Operario"]+""];
						
						if (i != 0)
							table += "</table><br />";
						
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px; text-align:left;\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"width:7%;\">";
						table += "			Operario";
						table += "		</td>";
						table += "		<td style=\"width:40%;\">";
						table += "			<input type=\"text\" id=\"cobrador\" style=\"width: 350px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Operario"]+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:7%;\">";
						table += "			Total Kg";
						table += "		</td>";
						table += "		<td style=\"width:46%;\">";
						table += "			<input type=\"text\" id=\"cobrador_total_valor\" style=\"width: 200px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+TotalPeso.toLocaleString('es-ES')+"\"/>";
						table += "		</td>";
						table += "	</tr>";
						table += "</table>";
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
						table += "			Proceso";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
						table += "			Fecha";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
						table += "			Orden Prod.";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:19%;\">";
						table += "			Cliente";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:19%;\">";
						table += "			Producto";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
						table += "			Kg.";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
						table += "			Maquinaria";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
						table += "			Estado";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
						table += "			Dig.";
						table += "		</td>";
						table += "	</tr>";
					}
					
					var Peso = data[i]["Peso"];
					
					table += "	<tr>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["Proceso"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "			"+SetFormattedDate(data[i]["Fecha"]);
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "			"+data[i]["Ord_Produccion"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["Cliente"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["Producto"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			"+Peso.toLocaleString('es-ES');
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["Maquinaria"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "			"+data[i]["Estado"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["DigitadoPor"];
					table += "		</td>";
					table += "	</tr>";
				}
				table += "</table>";
				$("#fecha_ini").val(SetFormattedDate("<?php echo $Fecha_Ini; ?>"));
				$("#fecha_fin").val(SetFormattedDate("<?php echo $Fecha_Fin; ?>"));
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
	
	.table1{
		width: 320px;
		font-family:calibri;
		margin-top: 5px;
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
	<div id="header" style="margin-bottom:10px;">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li id="title" style="padding:7px 0px; margin-left: 160px; width: 330px; text-align:center;">
			Produccion por Operario
		</li>
		<li style="padding:2px 0px; margin-left: 105px;">
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
	<table class="table1" cellspacing="1" cellpadding="1">
		<tr>
			<td>
				Fecha Inicio
			</td>
			<td>
				<input type="text" id="fecha_ini" style="width: 90px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Fecha Fin
			</td>
			<td>
				<input type="text" id="fecha_fin" style="width: 90px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px; text-align:left;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:7%;">
					Operario
				</td>
				<td style="width:40%;">
					<input type="text" id="cobrador" style="width: 350px; height: 20px;" class="myinput" readonly/>
				</td>
				<td style="width:7%;">
					Total Kg
				</td>
				<td style="width:46%;">
					<input type="text" id="cobrador_total_valor" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:8%;">
					Proceso
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Orden Prod.
				</td>
				<td style="border-bottom:grey 1px solid; width:19%;">
					Cliente
				</td>
				<td style="border-bottom:grey 1px solid; width:19%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Kg.
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Maquinaria
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Estado
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Dig.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					Trefilado
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					01-Ene-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					OR0001520
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					Trefiladora
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Aprobado
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					ASDAS
				</td>
			</tr>
		</table>
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>