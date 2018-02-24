<?php
	session_start();
	$Ord_Produccion = isset($_GET['Ord_Produccion']) ? $_GET['Ord_Produccion']:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	function SetFormattedDate (MyDate)
	{
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
	
	if ("<?php echo $Ord_Produccion; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data:{"Imprimir_Produccion":"<?php echo $Ord_Produccion; ?>"},
			async: true,
			success: function (data, status, xhr)
			{
				var table1 = "";
				table1 += "<div>";
				table1 += "	<p style=\"font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;\">";
				table1 += "		Productos Requeridos en Cada Proceso";
				table1 += "	</p>";
				table1 += "</div>";
				table1 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table1 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table1 += "			Codigo";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:33%;\">";
				table1 += "			Producto";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
				table1 += "			Und";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table1 += "			Cant";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table1 += "			PesoT";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table1 += "			Origen";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table1 += "			Destino";
				table1 += "		</td>";
				table1 += "	</tr>";
				
				$("#destino").val(data[0]["DestinoOrden"]);
				$("#fecha").val(SetFormattedDate(data[0]["Fecha"]));
				$("#cliente").val(data[0]["Cliente"]);
				$("#cliente_ID").val(data[0]["ClienteID"]);
				
				var Total_Requerido = 0;
				var Total_Obtener = 0;
				
				var len = data.length;
				for (var i = 0; i < len; i++)
				{
					if (data[i]["Tipo"] == "Requerido")
					{
						var Peso = parseFloat(data[i]["Peso"]).toFixed(2);
						Total_Requerido += parseFloat(Peso);
						// Table 1
						table1 += "<tr>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data[i]["CodFab"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table1 += "		"+data[i]["Nombre"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data[i]["UndMed"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data[i]["Cantidad"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data[i]["Origen"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data[i]["Destino"];
						table1 += "	</td>";
						table1 += "</tr>";
					}
					if (len - i == 1)
						table1 += "</table>";
				}
				
				var table2 = "";
				table2 += "<div>";
				table2 += "	<p style=\"font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;\">";
				table2 += "		Productos a Obtener al Final de Cada Proceso";
				table2 += "	</p>";
				table2 += "</div>";
				table2 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table2 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table2 += "			Codigo";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:45%;\">";
				table2 += "			Producto";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table2 += "			Und";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table2 += "			Cant";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table2 += "			PesoT";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table2 += "			Proceso";
				table2 += "		</td>";
				table2 += "	</tr>";
					
				for (var i = 0; i < len; i++)
				{
					if (data[i]["Tipo"] == "Obtener")
					{
						var Peso = parseFloat(data[i]["Peso"]).toFixed(2);
						Total_Obtener += parseFloat(Peso);
						// Table 2
						table2 += "<tr>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data[i]["CodFab"];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table2 += "		"+data[i]["Nombre"];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data[i]["UndMed"];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data[i]["Cantidad"];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data[i]["Destino"];
						table2 += "	</td>";
						table2 += "</tr>";
					}
					if (len - i == 1)
						table2 += "</table>";
				}
				
				var Desperdicio = (1 - (Total_Obtener / Total_Requerido)) * 100;
				
				// Table 3
				var table3 = "";
				table3 += "<div>";
				table3 += "	<p style=\"font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;\">";
				table3 += "		Procesos en que Intervienen/Operarios";
				table3 += "	</p>";
				table3 += "</div>";
				table3 += "<table class=\"table1\" cellspacing=\"1\" cellpadding=\"1\">";
				table3 += "	<tr>";
				table3 += "		<td style=\"padding-right: 32px;\">";
				table3 += "			Trefilado";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"cliente\" style=\"width: 380px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Trefilado"]+"\"/>";
				table3 += "		</td>";
				table3 += "		<td colspan=\"2\">";
				table3 += "			Porcentaje de Desp.";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" style=\"width: 50px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Desperdicio.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"%\"/>"
				table3 += "		</td>";
				table3 += "	</tr>";
				table3 += "	<tr>";
				table3 += "		<td>";
				table3 += "			Corte y End.";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"enderezado\" style=\"width: 380px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Enderezado"]+"\"/>";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			Digitado";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"digitado_por\" style=\"width: 60px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["DigitadoPor"]+"\"/>"
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"fecha_digitado\" style=\"width: 130px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["FechaDigitado"]+"\"/>"
				table3 += "		</td>";
				table3 += "	</tr>";
				table3 += "	<tr>";
				table3 += "		<td>";
				table3 += "			Electrosoldado";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"electrosoldado\" style=\"width: 380px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Soldado"]+"\"/>";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			Aprobado";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"aprobado_por\" style=\"width: 60px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["AprobadoPor"]+"\"/>";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"fecha_aprobado\" style=\"width: 130px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["FechaAprobado"]+"\"/>";
				table3 += "		</td>";
				table3 += "	</tr>";
				table3 += "	<tr>";
				table3 += "		<td>";
				table3 += "			Figurado";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"figurado\" style=\"width: 380px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Figurado"]+"\"/>";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			Estado";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			<input type=\"text\" id=\"estado\" style=\"width: 60px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Estado"]+"\"/>";
				table3 += "		</td>";
				table3 += "		<td>";
				table3 += "			&nbsp;";
				table3 += "		</td>";
				table3 += "	</tr>";
				table3 += "</table>";
				
			
				// Table 4
				var table4 = "";
				table4 += "<div>";
				table4 += "	<p style=\"font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;\">";
				table4 += "		Comprobante de Salida de Inventario";
				table4 += "	</p>";
				table4 += "</div>";
				table4 += "<table class=\"table1\" cellspacing=\"1\" cellpadding=\"1\">";
				table4 += "	<tr>";
				table4 += "		<td>";
				table4 += "			Solicitud de Material";
				table4 += "		</td>";
				table4 += "		<td>";
				table4 += "			<input type=\"text\" id=\"solicitud\" style=\"width: 100px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Solicitud"]+"\"/>";
				table4 += "		</td>";
				table4 += "		<td>";
				table4 += "			Orden de Produccion";
				table4 += "		</td>";
				table4 += "		<td>";
				table4 += "			<input type=\"text\" id=\"ord_produccion\" style=\"width: 100px; height: 20px;\" class=\"myinput\" readonly value=\"<?php echo $Ord_Produccion; ?>\"/>";
				table4 += "		</td>";
				table4 += "		<td>";
				table4 += "			Digitado";
				table4 += "		</td>";
				table4 += "		<td>";
				table4 += "			<input type=\"text\" id=\"digitado_por\" style=\"width: 60px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["DigitadoPor"]+"\"/>";
				table4 += "		</td>";
				table4 += "		<td>";
				table4 += "			<input type=\"text\" id=\"fecha_digitado\" style=\"width: 130px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["FechaDigitado"]+"\"/>";
				table4 += "		</td>";
				table4 += "	</tr>";
				table4 += "</table>";
				table4 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table4 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table4 += "			Codigo";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:45%;\">";
				table4 += "			Producto";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table4 += "			Und";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table4 += "			Cant";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table4 += "			PesoT";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table4 += "			Entregar a";
				table4 += "		</td>";
				table4 += "	</tr>";
				
				for (var i = 0; i < len; i++)
				{
					// Table 4
					if (data[i]["Tipo"] == "Requerido")
					{
						table4 += "<tr>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data[i]["CodFab"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table4 += "		"+data[i]["Nombre"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data[i]["UndMed"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data[i]["Cantidad"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data[i]["Peso"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data[i]["Destino"];
						table4 += "	</td>";
						table4 += "</tr>";
					}
					if (len - i == 1)
						table4 += "</table>";
				}
				var table_final = table1 + table2 + table3 + table4;
				$("#mid").html(table_final);
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
		padding 2px;
	}
	
	.table1{
		width: 710px;
		font-family:calibri;
		margin-top: 5px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 710px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
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
		<li style="padding:7px 0px; margin-left: 70px;">
			Orden de Produccion &nbsp;&nbsp;&nbsp; <?php echo $Ord_Produccion; ?>
		</li>
		<li style="padding:2px 0px; margin-left: 45px;">
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
			<td style="padding-right: 8px;">
				Destino
			</td>
			<td>
				<input type="text" id="destino" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				Cliente
			</td>
			<td>
				<input type="text" id="cliente" style="width: 490px; height: 20px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Fecha
			</td>
			<td>
				<input type="text" id="fecha" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
			<td style="padding-right: 5px;">
				Cliente ID
			</td>
			<td>
				<input type="text" id="cliente_ID" style="width: 490px; height: 20px;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<!--<div>
			<p style="font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;">
				Productos Requeridos en Cada Proceso
			</p>
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:33%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:7%;">
					Und
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Cant
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					PesoT
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Origen
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Destino
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Kilo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					ASDASDSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					ASDASDSADASDAS
				</td>
			</tr>
		</table>
		<div>
			<p style="font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;">
				Productos a Obtener al Final de Cada Proceso
			</p>
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:45%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Und
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Cant
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					PesoT
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Proceso
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Kilo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					ASDASDSADASDAS
				</td>
			</tr>
		</table>
		<div>
			<p style="font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;">
				Procesos en que Intervienen/Operarios
			</p>
		</div>
		<table class="table1" cellspacing="1" cellpadding="1">
			<tr>
				<td style="padding-right: 32px;">
					Trefilado
				</td>
				<td>
					<input type="text" id="cliente" style="width: 380px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					Digitado
				</td>
				<td>
					<input type="text" id="digitado_por" style="width: 60px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="fecha_digitado" style="width: 130px; height: 20px;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Corte y End.
				</td>
				<td>
					<input type="text" id="enderezado" style="width: 380px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					Aprobado
				</td>
				<td>
					<input type="text" id="aprobado_por" style="width: 60px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="fecha_aprobado" style="width: 130px; height: 20px;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Electrosoldado
				</td>
				<td>
					<input type="text" id="electrosoldado" style="width: 380px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					Estado
				</td>
				<td>
					<input type="text" id="estado" style="width: 60px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
			<tr>
				<td>
					Figurado
				</td>
				<td>
					<input type="text" id="figurado" style="width: 380px; height: 20px;" class="myinput" readonly/>
				</td>
				<td colspan="3">
					&nbsp;
				</td>
			</tr>
		</table>
		<div>
			<p style="font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;">
				Comprobante de Salida de Inventario
			</p>
		</div>
		<table class="table1" cellspacing="1" cellpadding="1">
			<tr>
				<td>
					Solicitud de Material
				</td>
				<td>
					<input type="text" id="solicitud" style="width: 100px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					Orden de Produccion
				</td>
				<td>
					<input type="text" id="ord_produccion" style="width: 100px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					Digitado
				</td>
				<td>
					<input type="text" id="digitado_por" style="width: 60px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="fecha_digitado" style="width: 130px; height: 20px;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:45%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Und
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Cant
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					PesoT
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Entregar a
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Kilo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					ASDASDSADASDAS
				</td>
			</tr>
		</table>-->
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>