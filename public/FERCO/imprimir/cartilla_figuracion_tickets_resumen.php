<?php
	session_start();
	//---
	$Lista = isset($_GET["Lista"]) ? $_GET["Lista"]:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	var currenttime = "<?php print date("F d, Y H:i:s", time())?>";
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	function FormatedDate()
	{
		var MyDate = new Date(currenttime);
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
	
	if ("<?php echo $Lista; ?>" != "")
	{
		var Lista = "<?php echo $Lista; ?>";
		Lista = decodeURIComponent(escape(window.atob(Lista)));
		Lista = JSON.parse(Lista);
		
		$.ajax({
			dataType: "json",
			url: "../modulos/datos_productos.php",
			data: {"Figuracion_Imprimir_Hierros":Lista[0]["Interno"]},
			success: function (data, status, xhr)
			{
				var table = "";
				var TotalTickets = 0;
				var TotalItems = 0;
				var Viejo_ID = "_empty_";
				
				table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"1\" style=\"text-align: left;\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%; vertical-align: -webkit-baseline-middle;\">";
				table += "			Diametro";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
				table += "			Diametro";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:33%;\">";
				table += "			Detalle";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table += "			Cantidad";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table += "			Tickets";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table += "			Long (m)";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table += "			Peso (kg)";
				table += "		</td>";
				table += "	</tr>";
				
				if (data[0] != undefined)
				{
					$("#interno").val(Lista[0]["Interno"]);
					$("#cliente").val(data[0]["Cliente"]);
					$("#cliente_id").val(data[0]["ClienteID"]);
					$("#obra").val(data[0]["Obra"]);
					$("#ord_compra").val(data[0]["Orden_Compra"]);
					$("#ord_produccion").val(data[0]["Orden_Produccion"]);
					
					for (var i = 0; i < Lista.length; i++)
					{
						if (Viejo_ID != ""+Lista[i]["Codigo"]+Lista[i]["Figura"]+"")
						{
							var Tickets = 0;
							var Cantidad = 0;
							Viejo_ID = ""+Lista[i]["Codigo"]+Lista[i]["Figura"]+"";
							
							for (var Index = i; Index < Lista.length; Index++)
							{
								if (Viejo_ID != ""+Lista[Index]["Codigo"]+Lista[Index]["Figura"]+"")
									break;
								
								Cantidad += parseFloat(Lista[Index]["Cantidad"]);
								Tickets++;
							}
							
							for (var a = 0; a < data[0]["Items"].length; a++)
							{
								if (Lista[i]["Codigo"] == data[0]["Items"][a]["Codigo"] &&
								Lista[i]["Figura"] == data[0]["Items"][a]["Imagen"] )
								{
									var Longitud = parseFloat(data[0]["Items"][a]["Longitud"]);
									var Peso = parseFloat(data[0]["Items"][a]["Peso"]);
									var Detalle = data[0]["Items"][a]["Detalle"];
									var Ubicacion = data[0]["Items"][a]["Ubicacion"];
									
									TotalTickets += Tickets;
									TotalItems += Cantidad;
									
									table += "	<tr>";
									table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; overflow:hidden; white-space: nowrap;\">";
									table += "			<img src=\"../images/"+data[0]["Items"][a]["Imagen"]+"\" width=\"100px\"/>";
									table += "		</td>";
									table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; overflow:hidden; white-space: nowrap;\">";
									table += "			"+data[0]["Items"][a]["Nombre"];
									table += "		</td>";
									table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
									table += "			"+Detalle.replace(/<br \/>/g, " ");
									table += "		</td>";
									table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
									table += "			"+Cantidad;
									table += "		</td>";
									table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
									table += "			"+Tickets;
									table += "		</td>";
									table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
									table += "			"+Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
									table += "		</td>";
									table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
									table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
									table += "		</td>";
									table += "	</tr>";
									break;
								}
							}
						}
						
						if (Lista.length - i == 1)
						{
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
							table += "			&nbsp;";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
							table += "			&nbsp;";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
							table += "			<b>TOTAL</b>";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			<b>"+TotalItems+"</b>";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			<b>"+TotalTickets+"</b>";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
							table += "			&nbsp;";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid;\">";
							table += "			&nbsp;";
							table += "		</td>";
							table += "	</tr>";
						}
					}
				}
				else
				{
					alert("No hay datos que mostrar...");
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
		window.print();
	});
});
</script>
<style type="text/css">
	.myinput {
		font-size: 12px;
		padding: 2px;
		font-family:calibri;
		height: 18px;
	}
	
	#header{
		width: 700px;
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
		width: 700px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		width: 700px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 11px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 700px;
		list-style:none;
	}
	
	#print_button{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #FAA732;
		border-color: #FAA732;
		margin:auto;
	}
	
	@media all {
		#break { display: none; }
	}
	@media print
	{
		#break { display: block; page-break-before: always; }
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
		@page 
		{
			margin: 0.5cm 0cm 0.5cm 0.3cm;
			size: portrait;
		}
	}
</style>
<div>
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="font-size: 16px; padding:10px 0px; margin-left: 75px;">
			Resumen Figuracion
		</li>
		<li style="padding:5px 0px 0px 0px; margin-left: 75px;">
			<table  cellspacing="0" cellpadding="0" style="font-size: 12px; text-align:center;">
				<tr>
					<td>
						Cartilla
					</td>
					<td>
						Fecha
					</td>
					<td>
						Impreso
					</td>
				</tr>
				<tr>
					<td style="padding-right:3px;">
						<input type="text" id="interno" class="myinput" style="width: 100px; text-align:center;" readonly/>
					</td>
					<td style="padding-right:3px;">
						<input type="text" id="fecha_impreso" class="myinput" style="width: 115px; text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="impreso_por" class="myinput" style="width: 70px; text-align:center;" readonly/>
					</td>
				</tr>
			</table>
		</li>
	</div>
	<table class="table1" border="0" cellspacing="1" cellpadding="1" style="text-align: left;">
		<tr>
			<td>
				Cliente
			</td>
			<td>
				<input type="text" id="cliente" style="width: 330px; text-align:left;" class="myinput" readonly/>
			</td>
			<td>
				Cliente ID
			</td>
			<td>
				<input type="text" id="cliente_id" style="width: 100px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Ord. Compra
			</td>
			<td>
				<input type="text" id="ord_compra" style="width: 100px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Obra
			</td>
			<td colspan="3">
				<input type="text" id="obra" style="width: 487px; text-align:left;" class="myinput" readonly/>
			</td>
			<td>
				Ord. Prod.
			</td>
			<td>
				<input type="text" id="ord_produccion" style="width: 100px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<!--<table class="table2" cellspacing="0" cellpadding="1" style="text-align: left;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:30%;">
					Diametro
				</td>
				<td style="border-bottom:grey 1px solid; width:45%;">
					Detalle
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Cantidad
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Long (m)
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Peso (kg)
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDSADSAD ASDASDASDSADSAD ASDASDASDSADSAD
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;">
					A=10, B=59, C=95, D=21, E=62, F=56, G=20
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					9.999
				</td>
			</tr>
		</table>-->
	</div>
	<table class="table1" border="0" cellspacing="1" cellpadding="1" style="text-align: left; margin-top:20px;">
		<tr>
			<td style="text-align:center;">
				_______________________________
			</td>
			<td style="text-align:center;">
				_______________________________
			</td>
		</tr>
		<tr>
			<td style="text-align:center;">
				Recibió
			</td>
			<td style="text-align:center;">
				Revisó
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />