<?php
	session_start();
	require_once('../modulos/config.php');
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
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
	
	$("#interno").val("<?php echo $Interno; ?>");
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	if ("<?php echo $Interno; ?>" != "")
	{
		$.ajax({
			dataType: "json",
			url: "../modulos/datos_productos.php",
			data: {"Presupuesto_Imprimir_Detalles_APU":"<?php echo $Interno; ?>"},
			success: function (data, status, xhr)
			{
				var table = "<table class=\"table1\" cellspacing=\"0\" cellpadding=\"2\">";
				table += "	<tr style=\"background: #4E4E4E; font-size: 14px; color:#FFF;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 14%;\">";
				table += "			Codigo";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 42%;\">";
				table += "			Descripción";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 6%;\">";
				table += "			Unidad";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 8%;\">";
				table += "			Cantidad";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 15%;\">";
				table += "			Vr. Item";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; text-align: center; width: 15%;\">";
				table += "			Vr. Unitario";
				table += "		</td>";
				table += "	</tr>";
				var len = data.length;
				for (var i = 0; i < len; i++)
				{
					//alert(JSON.stringify(data[i]))
					var Valor = parseFloat(data[i]["Valor"]);
					table += "	<tr style=\"background: #DEDEDE; font-size: 14px; font-weight:700;\">";
					table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
					table += "			"+data[i]["Item"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
					table += "			"+data[i]["Nombre"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
					table += "			"+data[i]["Unidad"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center\">";
					table += "			&nbsp;";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;\">";
					table += "			&nbsp;";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; text-align: right;\">";
					table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "	</tr>";
					
					var LastType = "_empty_";
					var len2 = data[i]["Items"].length;
					for (var a = 0; a < len2; a++)
					{
						if (LastType != data[i]["Items"][a]["Tipo"])
						{
							if (LastType == "Materiales")
							{
								var Desperdicios = parseFloat(data[i]["Desperdicios"]);
								var Desperdicios_Val = parseFloat(data[i]["Desperdicios_Val"]);
								
								table += "	<tr>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			Desperdicios de Materiales";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			%";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			"+Desperdicios.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; text-align: right;\">";
								table += "			$ "+Desperdicios_Val.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "	</tr>";
							}
							/*else if (LastType == "Mano de Obra")
							{
								var Gastos = parseFloat(data[i]["Gastos"]);
								var Gastos_Val = parseFloat(data[i]["Gastos_Val"]);
								
								table += "	<tr>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			Herramienta Menor";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			%";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			"+Gastos.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; text-align: right;\">";
								table += "			$ "+Gastos_Val.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "	</tr>";
							}*/
							
							LastType = data[i]["Items"][a]["Tipo"];
							
							var Valor = parseFloat(data[i]["Items"][a]["Valor"]);
							var Total = parseFloat(data[i]["Items"][a]["Total"]);
							var Tipo = data[i]["Items"][a]["Tipo"];
							if (Tipo == "Mano de Obra")
								Tipo = "Personal";
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
							table += "			"+Tipo+":";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
							table += "			"+data[i]["Items"][a]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
							table += "			"+data[i]["Items"][a]["Unidad"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
							table += "			"+data[i]["Items"][a]["Cantidad"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; text-align: right;\">";
							table += "			$ "+Total.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
						else
						{
							var Valor = parseFloat(data[i]["Items"][a]["Valor"]);
							var Total = parseFloat(data[i]["Items"][a]["Total"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
							table += "			&nbsp;";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
							table += "			"+data[i]["Items"][a]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
							table += "			"+data[i]["Items"][a]["Unidad"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
							table += "			"+data[i]["Items"][a]["Cantidad"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; text-align: right;\">";
							table += "			$ "+Total.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
						
						if (len2 - a == 1)
						{
							if (LastType == "Materiales")
							{
								var Desperdicios = parseFloat(data[i]["Desperdicios"]);
								var Desperdicios_Val = parseFloat(data[i]["Desperdicios_Val"]);
								
								table += "	<tr>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			Desperdicios de Materiales";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			%";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			"+Desperdicios.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; text-align: right;\">";
								table += "			$ "+Desperdicios_Val.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "	</tr>";
							}
							else if (LastType == "Mano de Obra")
							{
								var Gastos = parseFloat(data[i]["Gastos"]);
								var Gastos_Val = parseFloat(data[i]["Gastos_Val"]);
								
								table += "	<tr>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			Herramienta Menor";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			%";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			"+Gastos.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; text-align: right;\">";
								table += "			$ "+Gastos_Val.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "	</tr>";
							}
							else if (LastType == "Equipos")
							{
								var Gastos = parseFloat(data[i]["Gastos"]);
								var Gastos_Val = parseFloat(data[i]["Gastos_Val"]);
								
								table += "	<tr>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid;\">";
								table += "			Herramienta Menor";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			%";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;\">";
								table += "			"+Gastos.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; text-align: right;\">";
								table += "			$ "+Gastos_Val.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								table += "		</td>";
								table += "	</tr>";
							}
						}
					}
					
					table += "		<tr>";
					table += "			<td colspan=\"6\" style=\"border-bottom:grey 1px solid;\">";
					table += "				&nbsp;";
					table += "			</td>";
					table += "		</tr>";
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
	
	$("#print_button").on("click", function ()
	{
		$("#fecha_impreso").val(FormatedDate());
		window.print()
	});
});
</script>
<style type="text/css">
	body{
		margin: 5px;
		padding: 0px;
	}
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
		font-family: calibri;
		margin-top: 10px;
		text-align: left;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 800px;
		font-style: italic;
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
	@media all {
		#break { display: none; }
	}
	@media print {
		#break { display: block; page-break-before: always; }
		.header{
			-webkit-print-color-adjust: exact;
		}
		.table1{
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
		size: portrait;
		margin: 0.5cm 0.5cm 1.0cm 0.4cm;
	}
</style>
<div>
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 48px;">
			Presupuesto - Lista Detallada de APU
		</li>
		<li style="padding:2px 0px; margin-left: 48px;">
			<table  cellspacing="0" cellpadding="0" style="font-size:12px; text-align:center;">
				<tr>
					<td>
						Presupuesto No.
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
					<td>
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
		<table class="table1" cellspacing="0" cellpadding="2">
			<tr style="background: #4E4E4E; font-size: 14px; color:#FFF;">
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 14%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 42%;">
					Descripción
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 6%;">
					Unidad
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 8%;">
					Cantidad
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center; width: 15%;">
					Vr. Item
				</td>
				<td style="border-bottom:grey 1px solid; text-align: center; width: 15%;">
					Vr. Unitario
				</td>
			</tr>
			<tr style="background: #DEDEDE; font-size: 14px; font-weight:700;">
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					01.30.50215
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					asdasasdasdasdas
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;">
					Ha
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center">
					25.00
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;">
					$ 10,202.00
				</td>
				<td style="border-bottom:grey 1px solid; text-align: right;">
					$ 1,250,202.00
				</td>
			</tr>
			<tr>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					Mano de Obra:
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					asdasasdasdasdas
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;">
					Ha
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center">
					25.00
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;">
					$ 10,202.00
				</td>
				<td style="border-bottom:grey 1px solid; text-align: right;">
					$ 1,250,202.00
				</td>
			</tr>
			<tr>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					Materiales:
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					asdasasdasdasdas
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center;">
					Ha
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: center">
					25.00
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; text-align: right;">
					$ 10,202.00
				</td>
				<td style="border-bottom:grey 1px solid; text-align: right;">
					$ 1,250,202.00
				</td>
			</tr>
			<tr>
				<td colspan="6" style="border-bottom:grey 1px solid;">
					&nbsp;
				</td>
			</tr>
		</table>
	</div>
</div>
<div id="bottom" align="center">
	<input type="button" id="print_button" value="Imprimir"/>
</div>