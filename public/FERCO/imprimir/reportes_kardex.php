<?php
	session_start();
	
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Motivo = isset($_GET['Motivo']) ? $_GET['Motivo']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"0000-00-00";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"0000-00-00";
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	var ClienteData = [];
	var ProductoNombre = [];
	var ProductoPeso = [];
	
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
	
	$.ajax(
	{
		dataType: 'json',
		url: "../modulos/datos_productos.php",
		async: false,
		success: function (data)
		{
			for (var i = 0; i < data.length; i++)
			{
				ProductoNombre[""+data[i]["CodFab"]+""] = data[i]["Nombre"];
				ProductoPeso[""+data[i]["CodFab"]+""] = data[i]["Peso"];
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
	
	if ("<?php echo $Codigo; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos_productos.php",
			data: {
				"Reportes_Kardex_Detalle":true,
				"Codigo":"<?php echo $Codigo; ?>",
				"ClienteID":"<?php echo $ClienteID; ?>",
				"Motivo":"<?php echo $Motivo; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
				"OrderBy":"<?php echo $OrderBy; ?>",
			},
			async: true,
			success: function (data, status, xhr)
			{
				if ("<?php echo $OrderBy; ?>" == "")
				{
					var table = "";
					table += "<table class=\"mytable\" cellspacing=\"0\" cellpadding=\"2\">";
					table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
					table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
					table += "			Fecha";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Tercero";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
					table += "			Tipo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
					table += "			Motivo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Cantidad";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Existencia";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Nuevo Saldo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Interno";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Factura";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Remision";
					table += "		</td>";
					table += "	</tr>";
					
					var len = data.length;
					for (var i = 0; i < len; i++)
					{
						if (data[i]["Cliente"] != "" && data[i]["Cliente"] != undefined)
						{
							var Cantidad = parseFloat(data[i]["Cantidad"]);
							var Existencia = parseFloat(data[i]["Existencia"]);
							var Saldo = parseFloat(data[i]["Saldo"]);
							
							table += "<tr>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+SetFormattedDate(data[i]["Fecha"]);
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+ClienteData[""+data[i]["Cliente"]+""];
							table += "	</td>";
							if (data[i]["Tipo"] == "Entrada")
							{
								table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; background-color: #CFDDE9;\">";
								table += "		"+data[i]["Tipo"];
								table += "	</td>";
							}
							else
							{
								table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
								table += "		"+data[i]["Tipo"];
								table += "	</td>";
							}
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+data[i]["Motivo"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "		"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "		"+Existencia.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "		"+Saldo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Interno"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Factura"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+data[i]["Remision"];
							table += "	</td>";
							table += "</tr>";
						}
					}
					
					table += "</table>";
					$("#title").html("Kardex TOTAL - "+ProductoNombre["<?php echo $Codigo; ?>"]);
				}
				else
				{
					var data2 = data;
					var table = "";
					var LastClientID = "_empty_";
					var len = data.length;
					for (var i = 0; i < len; i++)
					{
						if (data[i]["Cliente"] != undefined && data[i]["Cliente"] != "")
						{
							//alert(data[i]["Cliente"])
							if (LastClientID != data[i]["Cliente"])
							{
								LastClientID = data[i]["Cliente"];
								
								var Cantidad = 0;
								var TotalPeso = 0;
								var TotalFacturado = 0;
								var TotalCosto = 0;
								var TotalUtil = 0;
								
								var len2 = data2.length;
								for (var a = 0; a < len2; a++)
								{
									if (data2[a]["Cliente"] != data[i]["Cliente"])
										continue;
									
									Cantidad = + Cantidad + parseFloat(data2[a]["Cantidad"]);
									TotalPeso = + TotalPeso + parseFloat(Cantidad * ProductoPeso["<?php echo $Codigo; ?>"]);
									TotalFacturado = + TotalFacturado + parseFloat(data2[a]["Facturado"]);
									TotalCosto = + TotalCosto + parseFloat(data2[a]["Costo"]);
									TotalUtil = + TotalUtil + parseFloat(data2[a]["Utilidad"]);
									//alert(Cantidad +" - "+ data2[a]["Cliente"] +" - "+ data[i]["Cliente"])
								}
								
								table += "<table class=\"mytable\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
								table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
								table += "		<td style=\"width:350px;\">";
								table += "			Tercero";
								table += "		</td>";
								table += "		<td style=\"width:105px;\">";
								table += "			Total Mov";
								table += "		</td>";
								table += "		<td style=\"width:123px;\">";
								table += "			Total Peso";
								table += "		</td>";
								table += "		<td style=\"width:123px;\">";
								table += "			Total Facturado";
								table += "		</td>";
								table += "		<td style=\"width:123px;\">";
								table += "			Total Costo";
								table += "		</td>";
								table += "		<td style=\"width:40px;\">";
								table += "			Total %";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
								table += "		<td colspan=\"6\">";
								table += "			<li>";
								table += "				<input type=\"text\" id=\"cliente\" style=\"width: 355px; padding-right: 10px; height: 20px;\" class=\"myinput\" readonly value=\""+ClienteData[""+data[i]["Cliente"]+""]+"\"/>";
								table += "			</li>";
								table += "			<li style=\"margin-left:3px;\">";
								table += "				<input type=\"text\" id=\"cantidad\" style=\"width: 105px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "			</li>";
								table += "			<li style=\"margin-left:3px;\">";
								table += "				<input type=\"text\" id=\"peso\" style=\"width: 123px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+TotalPeso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "			</li>";
								table += "			<li style=\"margin-left:3px;\">";
								table += "				<input type=\"text\" id=\"facturado\" style=\"width: 123px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+TotalFacturado.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "			</li>";
								table += "			<li style=\"margin-left:3px;\">";
								table += "				<input type=\"text\" id=\"costo\" style=\"width: 123px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+TotalCosto.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "			</li>";
								table += "			<li style=\"margin-left:3px;\">";
								table += "				<input type=\"text\" id=\"util\" style=\"width: 50px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+TotalUtil.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "			</li>";
								table += "		</td>";
								table += "	</tr>";
								table += "</table>";
								table += "<table class=\"mytable\" cellspacing=\"0\" cellpadding=\"2\">";
								table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
								table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
								table += "			Producto";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
								table += "			Fecha";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
								table += "			Factura";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
								table += "			Mov";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
								table += "			Peso";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
								table += "			Facturado";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
								table += "			Costo";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
								table += "			% Util";
								table += "		</td>";
								table += "	</tr>";
							}
							
							var Peso = data[i]["Cantidad"] * ProductoPeso[""+data[i]["Producto"]+""];
							var Facturado = data[i]["Facturado"];
							var Costo = data[i]["Costo"];
							var Utilidad = data[i]["Utilidad"];;
							
							table += "<tr>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "		"+ProductoNombre[""+data[i]["Producto"]+""];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+SetFormattedDate(data[i]["Fecha"]);
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+data[i]["Factura"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "		"+data[i]["Cantidad"];
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "		"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "		$ "+Facturado.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "		$ "+Costo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "	</td>";
							table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "		"+Utilidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });+"%";
							table += "	</td>";
							table += "</tr>";
						}
					}
					$("#title").html("Kardex Por Tercero - "+ProductoNombre["<?php echo $Codigo; ?>"]);
				}
				
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
	
	.mytable{
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
	
	.mytable li{
		float: left;
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
		<li id="title" style="padding:7px 0px; width: 470px; margin-left: 60px; text-align: center; overflow:hidden; white-space: nowrap;">
			Kardex TOTAL - ?
		</li>
		<li style="padding:2px 0px; margin-left: 60px;">
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
				<td style="border-bottom:grey 1px solid; width:8%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Tercero
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Tipo
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Motvio
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Cantidad
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Existencia
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Nuevo Saldo
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Interno
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Factura
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Remision
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					SALIDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Produccion
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					99.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					99.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER0000052
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER0000052
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER0000052
				</td>
			</tr>
		</table>
		-->
		<!--
		<table class="mytable" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:350px;">
					Tercero
				</td>
				<td style="width:105px;">
					Total Mov
				</td>
				<td style="width:123px;">
					Total Peso
				</td>
				<td style="width:123px;">
					Total Facturado
				</td>
				<td style="width:123px;">
					Total Costo
				</td>
				<td style="width:40px;">
					Total %
				</td>
			</tr>
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td colspan="6">
					<li>
						<input type="text" id="cliente" style="width: 355px; padding-right: 10px; height: 20px;" class="myinput" />
					</li>
					<li style="margin-left:3px;">
						<input type="text" id="cantidad" style="width: 105px; height: 20px; text-align:right;" class="myinput" />
					</li>
					<li style="margin-left:3px;">
						<input type="text" id="peso" style="width: 123px; height: 20px; text-align:right;" class="myinput" />
					</li>
					<li style="margin-left:3px;">
						<input type="text" id="facturado" style="width: 123px; height: 20px; text-align:right;" class="myinput" />
					</li>
					<li style="margin-left:3px;">
						<input type="text" id="costo" style="width: 123px; height: 20px; text-align:right;" class="myinput" readonly/>
					</li>
					<li style="margin-left:3px;">
						<input type="text" id="util" style="width: 50px; height: 20px; text-align:right;" class="myinput" readonly/>
					</li>
				</td>
			</tr>
		</table>
		<table class="mytable" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:20%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Factura
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Mov
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Peso
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Facturado
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Costo
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					% Util
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER0000052
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					000.00%
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