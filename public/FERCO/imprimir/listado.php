<?php
	session_start();
	
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Ord_Compra = isset($_GET['Ord_Compra']) ? $_GET['Ord_Compra']:"";
	$Factura = isset($_GET['Factura']) ? $_GET['Factura']:"";
	$Ord_Produccion = isset($_GET['Ord_Produccion']) ? $_GET['Ord_Produccion']:"";
	$VendedorID = isset($_GET['VendedorID']) ? $_GET['VendedorID']:"";
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
	
	if ("<?php echo $Estado; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Ventas_Autorizar":true,
				"Ord_Compra":"<?php echo $Ord_Compra; ?>",
				"Factura":"<?php echo $Factura; ?>",
				"Ord_Produccion":"<?php echo $Ord_Produccion; ?>",
				"ClienteID":"<?php echo $ClienteID; ?>",
				"VendedorID":"<?php echo $VendedorID; ?>",
				"Estado":"<?php echo $Estado; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>"
			},
			async: true,
			success: function (data, status, xhr)
			{
				var table = "";
				table += "<table class=\"mytable\" cellspacing=\"0\" cellpadding=\"2\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table += "			Estado";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Fecha";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table += "			Ord. Comp.";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
				table += "			Factura";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
				table += "			Valor";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:28%;\">";
				table += "			Cliente";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table += "			Cliente ID";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table += "			Vend";
				table += "		</td>";
				table += "	</tr>";
				
				var len = data.length;
				for (var i = 0; i < len; i++)
				{
					if (data[i]["ClienteID"] != "" && data[i]["ClienteID"] != undefined)
					{
						var Valor = parseFloat(data[i]["Valor"]);
						
						table += "<tr>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
						table += "		"+data[i]["Estado"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+SetFormattedDate(data[i]["Fecha"]);
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+data[i]["Ord_Compra"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+data[i]["Factura"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "		$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "		"+ClienteData[""+data[i]["ClienteID"]+""];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+data[i]["ClienteID"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "		"+data[i]["VendedorID"];
						table += "	</td>";
						table += "</tr>";
					}
				}
				table += "</table>";
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
		padding 2px;
	}
	
	.mytable{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 800px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
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
		<li style="padding:7px 0px; margin-left: 180px;">
			Listado de Remisiones
		</li>
		<li style="padding:2px 0px; margin-left: 135px;">
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
		<table class="mytable" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:8%;">
					Estado
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Ord. Comp.
				</td>
				<td style="border-bottom:grey 1px solid; width:9%;">
					Factura
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Valor
				</td>
				<td style="border-bottom:grey 1px solid; width:28%;">
					Cliente
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Cliente ID
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Vend
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;">
					Finalizado
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					1234567890-1
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					JZ
				</td>
			</tr>
		</table>
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />