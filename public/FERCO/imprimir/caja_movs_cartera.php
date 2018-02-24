<?php
	session_start();
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$Caja_Interno = isset($_GET["Caja_Interno"]) ? $_GET["Caja_Interno"]:"";
	$Valor = isset($_GET["Valor"]) ? $_GET["Valor"]:0;
	$Abono = isset($_GET["Abono"]) ? $_GET["Abono"]:0;
	$Saldo = isset($_GET["Saldo"]) ? $_GET["Saldo"]:0;
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
	
	if ("<?php echo $ClienteID; ?>" != "" && "<?php echo $Caja_Interno; ?>" != "")
	{
		$.ajax({
			dataType: "json",
			url: "../modulos/datos.php",
			data: {
				"Cartera_Aplicar_Log":true,
				"ClienteID":"<?php echo $ClienteID; ?>",
				"Caja_Interno":"<?php echo $Caja_Interno; ?>",
			},
			success: function (data, status, xhr)
			{
				var table = "";
				table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Interno";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Remision";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Factura";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Ord. Compra";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table += "			Valor";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table += "			Saldo";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table += "			Abono";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table += "			Saldo Nuevo";
				table += "		</td>";
				table += "	</tr>";
				
				var len = data.length;
				var TotalDeuda = 0;
				for (var i = 0; i < len; i++)
				{
					if (data[i]["Interno"] != "")
					{
						if (i == 0)
						{
							$("#cliente").val(ClienteData["<?php echo $ClienteID; ?>"]);
							$("#cliente_ID").val("<?php echo $ClienteID; ?>");
							$("#recibo").val(data[0]["Caja_Recibo"]);
							
							var ValorRC = parseFloat("<?php echo $Valor; ?>");
							var AbonoRC = parseFloat("<?php echo $Abono; ?>");
							var SaldoRC = parseFloat("<?php echo $Saldo; ?>");
							
							$("#valor").val("$ "+ValorRC.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
							$("#abono").val("$ "+AbonoRC.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
							$("#saldo").val("$ "+SaldoRC.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
						}
						
						var Valor = parseFloat(data[i]["Valor"]);
						var Saldo = parseFloat(data[i]["Saldo"]);
						var Abono = parseFloat(data[i]["Abono"]);
						var Saldo_Nuevo = parseFloat(data[i]["Saldo_Nuevo"]);
						
						TotalDeuda = TotalDeuda + Saldo;
						
						table += "<tr>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+data[i]["Interno"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+data[i]["Remision"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+data[i]["Factura"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+data[i]["Ord_Compra"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "		$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "		$ "+Saldo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "		$ "+Abono.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
						table += "		$ "+Saldo_Nuevo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "	</td>";
						table += "</tr>";
					}
				}
				table += "</table>";
				$("#total_deuda").val("$ "+TotalDeuda.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
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
	
	.table1{
		width: 800px;
		font-family:calibri;
		margin-top: 10px;
		text-align: left;
		font-size: 12px;
		list-style:none;
	}
	
	.table2{
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
<div id="print_body">
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 190px;">
			Aplicar Pagos a Cartera
		</li>
		<li style="padding:2px 0px; margin-left: 115px;">
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
			<td style="width:65px;">
				Cliente
			</td>
			<td colspan="3">
				<input type="text" id="cliente" style="width: 308px; height: 20px;" class="myinput" readonly/>
			</td>
			<td style="width:75px;">
				Cliente ID
			</td>
			<td>
				<input type="text" id="cliente_ID" style="width: 130px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				R. Caja
			</td>
			<td>
				<input type="text" id="recibo" style="width: 130px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Total Deuda
			</td>
			<td>
				<input type="text" id="total_deuda" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				Valor RC
			</td>
			<td>
				<input type="text" id="valor" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				Total Abonos
			</td>
			<td>
				<input type="text" id="abono" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				Saldo Restante
			</td>
			<td>
				<input type="text" id="saldo" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Interno
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Remision
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Factura
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Ord. Compra
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Valor
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Saldo
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Abono
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Saldo Nuevo
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
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
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
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