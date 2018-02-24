<?php
	session_start();
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$Factura = isset($_GET['Factura']) ? $_GET['Factura']:"";
	$VendedorID = isset($_GET['VendedorID']) ? $_GET['VendedorID']:"";
	$CobradorID = isset($_GET['CobradorID']) ? $_GET['CobradorID']:"";
	$TipoMovimiento = isset($_GET['TipoMovimiento']) ? $_GET['TipoMovimiento']:"";
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	$Zero_Filter = isset($_GET['Zero_Filter']) ? $_GET['Zero_Filter']:"false";
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
	
	if ("<?php echo $Fecha_Ini; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Imprimir_Reportes_Cartera":true,
				"ClienteID":"<?php echo $ClienteID; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
				"Factura":"<?php echo $Factura; ?>",
				"VendedorID":"<?php echo $VendedorID; ?>",
				"CobradorID":"<?php echo $CobradorID; ?>",
				"TipoMovimiento":"<?php echo $TipoMovimiento; ?>",
				"OrderBy":"<?php echo $OrderBy; ?>",
				"Zero_Filter":"<?php echo $Zero_Filter; ?>",
			},
			async: true,
			success: function (data, status, xhr)
			{
				var len = data.length;
				var table = "";
				var Viejo_ID = "_empty_";
				var myarray = new Array();
				var first_row = true;
				
				<?php 
				if ($OrderBy == "")
				{
				?>
					table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
					table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
					table += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
					table += "			Fecha";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
					table += "			Remision";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
					table += "			Factura";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Cliente";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Cliente ID";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
					table += "			T. Mov.";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
					table += "			Valor";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
					table += "			Saldo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
					table += "			Vend";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
					table += "			Cobr";
					table += "		</td>";
					table += "	</tr>";
					
					for (var i = 0; i < len; i++)
					{
						if (data[i]["ClienteID"] != "")
						{
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Remision"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Cliente"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["ClienteID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["TipoMovimiento"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+data[i]["Valor"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+data[i]["Saldo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["VendedorID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["CobradorID"];
							table += "		</td>";
							table += "	</tr>";
						}
					}
				<?php
				}
				else if ($OrderBy == "Cobrador")
				{
				?>
					for (var i = 0; i < len; i++)
					{
						var array = {};
						array["Valor"] = data[i]["ID_Total_Valor"];
						array["Saldo"] = data[i]["ID_Total_Saldo"];
						
						if (Viejo_ID != data[i]["CobradorID"] && data[i]["ClienteID"] != "" && first_row == true)
						{
							Viejo_ID = data[i]["CobradorID"];
							first_row = false;
							if (Viejo_ID != data[i+1]["CobradorID"] && data[i+1]["ClienteID"] != "" || len - 1 == 1)
							{
								myarray[""+data[i]["CobradorID"]+""] = array;
							}
						}
						else if (Viejo_ID != data[i]["CobradorID"] && data[i]["ClienteID"] != "" && first_row == false)
						{
							myarray[""+data[i]["CobradorID"]+""] = array;
						}
						else if (data[i]["ClienteID"] != "")
						{
							myarray[""+data[i]["CobradorID"]+""] = array;
						}
					}
					
					Viejo_ID = "_empty_";
					for (var i = 0; i < len; i++)
					{
						if (Viejo_ID != data[i]["CobradorID"] && data[i]["ClienteID"] != "")
						{
							Viejo_ID = data[i]["CobradorID"];
							if (i != 0)
								table += "</table><br />";
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:8%;\">";
							table += "			Cobrador";
							table += "		</td>";
							table += "		<td style=\"width:36%;\">";
							table += "			<input type=\"text\" id=\"cobrador\" style=\"width: 250px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Cobrador"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:9%;\">";
							table += "			Total Valor";
							table += "		</td>";
							table += "		<td style=\"width:18%;\">";
							table += "			<input type=\"text\" id=\"cobrador_total_valor\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+data[i]["CobradorID"]+""]["Valor"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:9%;\">";
							table += "			Total Saldo";
							table += "		</td>";
							table += "		<td style=\"width:18%;\">";
							table += "			<input type=\"text\" id=\"cobrador_total_saldo\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+data[i]["CobradorID"]+""]["Saldo"]+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							//---
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Remision";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Factura";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
							table += "			Cliente";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Cliente ID";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
							table += "			T. Mov.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
							table += "			Valor";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
							table += "			Saldo";
							table += "		</td>";
							table += "	</tr>";
						}
						
						if (data[i]["ClienteID"] != "")
						{
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Remision"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Cliente"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["ClienteID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["TipoMovimiento"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+data[i]["Valor"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+data[i]["Saldo"];
							table += "		</td>";
							table += "	</tr>";
						}
					}
				<?php 
				}
				else if ($OrderBy == "Cliente")
				{
				?>
					for (var i = 0; i < len; i++)
					{
						var array = {};
						array["Valor"] = data[i]["ID_Total_Valor"];
						array["Saldo"] = data[i]["ID_Total_Saldo"];
						
						if (Viejo_ID != data[i]["ClienteID"] && data[i]["ClienteID"] != "" && first_row == true)
						{
							Viejo_ID = data[i]["ClienteID"];
							first_row = false;
							if (Viejo_ID != data[i+1]["ClienteID"] && data[i+1]["ClienteID"] != "" || len - 1 == 1)
							{
								myarray[""+data[i]["ClienteID"]+""] = array;
							}
						}
						else if (Viejo_ID != data[i]["ClienteID"] && data[i]["ClienteID"] != "" && first_row == false)
						{
							myarray[""+data[i]["ClienteID"]+""] = array;
						}
						else if (data[i]["ClienteID"] != "")
						{
							myarray[""+data[i]["ClienteID"]+""] = array;
						}
					}
					
					Viejo_ID = "_empty_";
					for (var i = 0; i < len; i++)
					{
						if (Viejo_ID != data[i]["ClienteID"] && data[i]["ClienteID"] != "")
						{
							//if ("<?php echo $Zero_Filter; ?>" == "true" && parseFloat(myarray[""+data[i]["ClienteID"]+""]["Saldo"]) < 1)
								//continue;
							
							Viejo_ID = data[i]["ClienteID"];
							if (i != 0)
								table += "</table><br />";
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:8%;\">";
							table += "			Cliente";
							table += "		</td>";
							table += "		<td style=\"width:36%;\">";
							table += "			<input type=\"text\" id=\"cobrador\" style=\"width: 250px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Cliente"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:9%;\">";
							table += "			Total Valor";
							table += "		</td>";
							table += "		<td style=\"width:18%;\">";
							table += "			<input type=\"text\" id=\"cobrador_total_valor\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+data[i]["ClienteID"]+""]["Valor"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:9%;\">";
							table += "			Total Saldo";
							table += "		</td>";
							table += "		<td style=\"width:18%;\">";
							table += "			<input type=\"text\" id=\"cobrador_total_saldo\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+data[i]["ClienteID"]+""]["Saldo"]+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							//---
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Remision";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Factura";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
							table += "			Cobrador";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
							table += "			T. Mov.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:16%;\">";
							table += "			Valor";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:16%;\">";
							table += "			Saldo";
							table += "		</td>";
							table += "	</tr>";
						}
						
						if (data[i]["ClienteID"] != "")
						{
							//if ("<?php echo $Zero_Filter; ?>" == "true" && parseFloat(data[i]["Saldo"]) < 1)
								//continue;
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Remision"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Cobrador"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["TipoMovimiento"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+data[i]["Valor"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+data[i]["Saldo"];
							table += "		</td>";
							table += "	</tr>";
						}
					}
				<?php } ?>
				
				table += "</table>";
				
				$("#total_valor").val(data[len-1]["Total_Valor"]);
				$("#total_saldo").val(data[len-1]["Total_Saldo"]);
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
		width: 800px;
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
		width: 800px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		width: 800px;
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
		width: 800px;
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
		<li style="padding:7px 0px; margin-left: 190px;">
			Reporte de Cartera
		</li>
		<li style="padding:2px 0px; margin-left: 140px;">
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
	<div align="center" style="width: 800px; border-bottom: #A9A9A9 1px solid; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
		Total General de Cartera
	</div>
	<table class="table1" cellspacing="1" cellpadding="1">
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
			<td>
				Total Valor
			</td>
			<td>
				<input type="text" id="total_valor" style="width: 180px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				Total Saldo
			</td>
			<td>
				<input type="text" id="total_saldo" style="width: 180px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 800px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<div id="mid">
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:8%;">
					Cobrador
				</td>
				<td style="width:36%;">
					<input type="text" id="cobrador" style="width: 250px; height: 20px;" class="myinput" readonly/>
				</td>
				<td style="width:9%;">
					Total Valor
				</td>
				<td style="width:18%;">
					<input type="text" id="cobrador_total_valor" style="width: 120px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td style="width:9%;">
					Total Saldo
				</td>
				<td style="width:18%;">
					<input type="text" id="cobrador_total_saldo" style="width: 120px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:12%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Factura
				</td>
				<td style="border-bottom:grey 1px solid; width:30%;">
					Cliente
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Cliente ID
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					T. Mov.
				</td>
				<td style="border-bottom:grey 1px solid; width:18%;">
					Valor
				</td>
				<td style="border-bottom:grey 1px solid; width:18%;">
					Saldo
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					000000000-0
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Credito
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
			</tr>
		</table>
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />