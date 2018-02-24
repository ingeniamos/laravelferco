<?php
	session_start();
	//---
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
	$Consecutivo = isset($_GET["Consecutivo"]) ? $_GET["Consecutivo"]:"";
	$Figura = isset($_GET["Figura"]) ? $_GET["Figura"]:"";
	$Detalles = isset($_GET["Detalles"]) ? $_GET["Detalles"]:"";
	$Codigo = isset($_GET["Codigo"]) ? $_GET["Codigo"]:"";
	$CantidadTotal = isset($_GET["CantidadTotal"]) ? $_GET["CantidadTotal"]:"";
	$Cantidad = isset($_GET["Cantidad"]) ? $_GET["Cantidad"]:"";
	$Individual = isset($_GET["Individual"]) ? $_GET["Individual"]:"false";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	var currenttime = "<?php print date("F d, Y H:i:s", time())?>";
	//var serverdate = new Date(currenttime);
	
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
	
	if ("<?php echo $Interno; ?>" != "")
	{
		var Consecutivos = "<?php echo $Consecutivo; ?>";
		Consecutivos = Consecutivos.split(",");
		//alert(JSON.stringify(Consecutivos))

		$.ajax({
			dataType: "json",
			url: "../modulos/datos_productos.php",
			data: {"Figuracion_Imprimir_Hierros":"<?php echo $Interno; ?>"},
			success: function (data, status, xhr)
			{
				var header = "";
				var table = "";
				
				var CantidadTotal = parseInt("<?php echo $CantidadTotal; ?>");
				var CantidadEmpaque = parseInt("<?php echo $Cantidad; ?>");
				
				var TmpTickets = parseInt(CantidadTotal / CantidadEmpaque);
				var Sobrante = CantidadTotal - (TmpTickets * CantidadEmpaque);
				var Tickets = TmpTickets;
				Tickets += Sobrante > 0 ? 1:0;
					
				if (data[0] != undefined)
				{
					for (var i = 0; i < Tickets; i++)
					{
						<?php
						if ($Individual == "false")
						{
						?>
						header += "<div style=\"float: left; margin: 0px 25px 50px 5px;\">";
						<?php
						}
						else
						{
						?>
						header += "<div style=\"margin-bottom: 65px;\">";
						<?php
						}
						?>
						header += "<div id=\"header\">";
						header += "	<li>";
						header += "		<img src=\"../images/logo.png\" alt=\"Logo\" width=\"110\" height=\"30\" style=\"padding:5px 0px 5px 5px;\">";
						header += "	</li>";
						header += "	<li style=\"font-size: 16px; padding:10px 0px; margin-left: 6px;\">";
						header += "		Figuraciones";
						header += "	</li>";
						header += "	<li style=\"padding:5px 0px 0px 0px; margin-left: 6px;\">";
						header += "		<table cellspacing=\"0\" cellpadding=\"0\" style=\"font-size: 11px; text-align:center;\">";
						header += "			<tr>";
						header += "				<td>";
						header += "					Ticket #";
						header += "				</td>";
						header += "				<td>";
						header += "					Fecha";
						header += "				</td>";
						header += "				<td>";
						header += "					Impreso";
						header += "				</td>";
						header += "			</tr>";
						header += "			<tr>";
						header += "				<td style=\"padding-right:3px;\">";
						header += "					<input type=\"text\" id=\"interno\" class=\"myinput\" style=\"width: 70px; text-align:center;\" readonly value=\""+Consecutivos[i]+"\"/>";
						header += "				</td>";
						header += "				<td style=\"padding-right:3px;\">";
						header += "					<input type=\"text\" id=\"fecha_impreso\" class=\"myinput\" style=\"width: 105px; text-align:center;\" readonly value=\""+FormatedDate()+"\"/>";
						header += "				</td>";
						header += "				<td>";
						header += "					<input type=\"text\" id=\"impreso_por\" class=\"myinput\" style=\"width: 50px; text-align:center;\" readonly value=\"<?php echo isset($_SESSION["UserCode"]) ? $_SESSION["UserCode"]:""; ?>\"/>";
						header += "				</td>";
						header += "			</tr>";
						header += "		</table>";
						header += "	</li>";
						header += "</div>";
				
						for (var a = 0; a < data[0]["Items"].length; a++)
						{
							if ("<?php echo $Codigo; ?>" == data[0]["Items"][a]["Codigo"] &&
							"<?php echo $Figura; ?>" == data[0]["Items"][a]["Imagen"] )
							{
								if (Tickets - i == 1 && Sobrante > 0)
									CantidadEmpaque = Sobrante;
								
								var Cantidad = parseFloat(data[0]["Items"][a]["Cantidad"]);
								var Longitud = parseFloat(data[0]["Items"][a]["Longitud"]) * Cantidad;
								var Peso = parseFloat(data[0]["Items"][a]["Peso"]);
								var Detalle = data[0]["Items"][a]["Detalle"];
								var Ubicacion = data[0]["Items"][a]["Ubicacion"];
								
								// 1º TABLE
								table += header;
								table += "<table class=\"table1\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" style=\"text-align: center;\">";
								table += "	<tr>";
								table += "		<td style=\"text-align: left;\">";
								table += "			Cartilla";
								table += "		</td>";
								table += "		<td colspan=\"5\" style=\"text-align: left; list-style: none;\">";
								table += "			<li style=\"float: left; margin: 0px 5px 0px 0px;\">";
								table += "				<input type=\"text\" style=\"width: 72px; text-align:left;\" class=\"myinput\" readonly value=\"<?php echo $Interno; ?>\"/>";
								table += "			</li>";
								table += "			<li style=\"float: left; margin: 3px 5px 0px 0px;\">";
								table += "				Orden de Compra";
								table += "			</li>";
								table += "			<li style=\"float: left;\">";
								table += "				<input type=\"text\" style=\"width: 75px; text-align:center;\" class=\"myinput\" readonly value=\""+data[0]["Orden_Compra"]+"\"/>";
								table += "			</li>";
								table += "			<li style=\"float: left; margin: 3px 5px 0px 5px;\">";
								table += "				Orden de Produccion";
								table += "			</li>";
								table += "			<li style=\"float: left;\">";
								table += "				<input type=\"text\" style=\"width: 70px; text-align:center;\" class=\"myinput\" readonly value=\""+data[0]["Orden_Produccion"]+"\"/>";
								table += "			</li>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td style=\"text-align: left;\">";
								table += "			Cliente";
								table += "		</td>";
								table += "		<td style=\"text-align: left;\" colspan=\"3\">";
								table += "			<input type=\"text\" style=\"width: 280px; text-align:left;\" class=\"myinput\" readonly value=\""+data[0]["Cliente"]+"\"/>";
								table += "		</td>";
								table += "		<td style=\"width: 50px;\">";
								table += "			Cliente ID";
								table += "		</td>";
								table += "		<td style=\"width: 70px;\">";
								table += "			<input type=\"text\" style=\"width: 70px; text-align:center;\" class=\"myinput\" readonly value=\""+data[0]["ClienteID"]+"\"/>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td style=\"text-align: left;\">";
								table += "			Obra";
								table += "		</td>";
								table += "		<td style=\"text-align: left;\">";
								table += "			<input type=\"text\" style=\"width: 140px; text-align:left;\" class=\"myinput\" readonly value=\""+data[0]["Obra"]+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			Descripcion";
								table += "		</td>";
								table += "		<td colspan=\"3\" rowspan=\"3\">";
								table += "			<textarea style=\"width:200px; height:60px; resize:none; text-align:left;\" class=\"myinput\" readonly >"+Ubicacion.replace(/<br\/>/g, " ")+"</textarea>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td style=\"text-align: left;\">";
								table += "			Diam.";
								table += "		</td>";
								table += "		<td colspan=\"2\" style=\"text-align: left; width: 140px;\">";
								table += "			<input type=\"text\" style=\"width: 207px; text-align:left;\" class=\"myinput\" readonly value=\""+data[0]["Items"][a]["Nombre"]+"\"/>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"3\" style=\"list-style: none;\">";
								table += "			<li style=\"float: left; margin: 3px 5px 0px 0px;\">";
								table += "				No. Elementos en Empaque";
								table += "			</li>";
								table += "			<li>";
								table += "				<input type=\"text\" style=\"width: 112px; text-align:left;\" class=\"myinput\" readonly value=\""+CantidadEmpaque.toLocaleString('es-ES')+"\"/>";
								table += "			</li>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"4\" style=\"list-style: none;\">";
								table += "			<li style=\"float: left; margin: 3px 5px 0px 0px;\">";
								table += "				Dimensiones";
								table += "			</li>";
								table += "			<li>";
								table += "				<input type=\"text\" style=\"width: 250px; text-align:left;\" class=\"myinput\" readonly value=\""+Detalle.replace(/<br \/>/g, " ")+"\"/>";
								table += "			</li>";
								table += "		</td>";
								table += "		<td>";
								table += "			Peso (kg)";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" style=\"width: 70px; text-align:right;\" class=\"myinput\" readonly value=\""+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td rowspan=\"4\" style=\"text-align: left; vertical-align: -webkit-baseline-middle;\">";
								table += "			Figura";
								table += "		</td>";
								table += "		<td colspan=\"3\" rowspan=\"4\" style=\"height:100px; text-align: right; vertical-align: -webkit-baseline-middle;\">";
								table += "			<img src=\"../images/"+data[0]["Items"][a]["Imagen"]+"\"/>";
								table += "		</td>";
								table += "		<td colspan=\"2\" rowspan=\"4\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "	</tr>";
								table += "	<tr>";
								table += "	</tr>";
								table += "	<tr>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"2\" style=\"text-align: left;\">";
								table += "			______________________";
								table += "		</td>";
								table += "		<td colspan=\"2\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td colspan=\"2\">";
								table += "			______________________";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"2\" style=\"text-align: left;\">";
								table += "			<span style=\"margin-left: 45px;\">Recibe</span>";
								table += "		</td>";
								table += "		<td colspan=\"2\">";
								table += "			&nbsp;";
								table += "		</td>";
								table += "		<td colspan=\"2\">";
								table += "			Revisó";
								table += "		</td>";
								table += "	</tr>";
								table += "</table>";
								table += "</div>";
								
								header = "";
								//add check para no imprimir una ultima hoja en blanco?
								break;
							}
						}
						
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
		window.print();
	});
});
</script>
<style type="text/css">
	.myinput {
		font-size: 11px;
		padding: 2px;
		font-family:calibri;
		height: 18px;
	}
	
	#header{
		width: 450px;
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
		width: 450px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 11px;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 450px;
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
	
	<?php
	if ($Individual == "false")
	{
	?>
	@media print
	{
		#header{
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
		@page 
		{
			margin: 0.5cm 0cm 0.5cm 0.3cm;
			size: landscape;
		}
	}
	<?php
	}
	else
	{
	?>
	@media print
	{
		#header{
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
		@page 
		{
			size: portrait;
			/*size: 14cm 10.8cm;*/
			margin: 0.5cm 0.5cm 0cm 0.3cm;
		}
	}
	<?php
	}
	?>
/*
style sheet for "A4" printing
@media print and (width: 21cm) and (height: 29.7cm) {
	@page {
	   margin: 3cm;
	}
}
style sheet for "letter" printing
@media print and (width: 8.5in) and (height: 11in) {
	@page {
		margin: 1in;
	}
}
*/
</style>
<div>
	<div id="mid">
		<!--
		<div id="header">
			<li>
				<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
			</li>
			<li style="font-size: 16px; padding:10px 0px; margin-left: 6px;">
				Figuraciones
			</li>
			<li style="padding:5px 0px 0px 0px; margin-left: 6px;">
				<table  cellspacing="0" cellpadding="0" style="font-size: 11px; text-align:center;">
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
							<input type="text" id="interno" class="myinput" style="width: 70px; text-align:center;" readonly/>
						</td>
						<td style="padding-right:3px;">
							<input type="text" id="fecha_impreso" class="myinput" style="width: 105px; text-align:center;" readonly/>
						</td>
						<td>
							<input type="text" id="impreso_por" class="myinput" style="width: 50px; text-align:center;" readonly/>
						</td>
					</tr>
				</table>
			</li>
		</div>
		<table class="table1" border="0" cellspacing="1" cellpadding="1" style="text-align: center;">
			<tr>
				<td>
					Cliente
				</td>
				<td colspan="3">
					<input type="text" style="width: 280px; text-align:left;" class="myinput" readonly/>
				</td>
				<td style="width: 50px;">
					Cliente ID
				</td>
				<td>
					<input type="text" style="width: 70px; text-align:center;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Obra
				</td>
				<td>
					<input type="text" style="width: 140px; text-align:left;" class="myinput" readonly/>
				</td>
				<td style="width: 60px;">
					Ord. Compra
				</td>
				<td>
					<input type="text" style="width: 70px; text-align:center;" class="myinput" readonly/>
				</td>
				<td>
					Ord. Prod.
				</td>
				<td>
					<input type="text" style="width: 70px; text-align:center;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Diam.
				</td>
				<td>
					<input type="text" style="width: 140px; text-align:left;" class="myinput" readonly/>
				</td>
				<td>
					Descripcion
				</td>
				<td colspan="3">
					<input type="text" style="width: 200px; text-align:left;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="list-style: none;">
					<li style="float: left; margin: 3px 5px 0px 0px;">
						No. Elementos en Empaque
					</li>
					<li>
						<input type="text" style="width: 185px; text-align:left;" class="myinput" readonly/>
					</li>
				</td>
				<td>
					Peso (kg)
				</td>
				<td>
					<input type="text" style="width: 70px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td colspan="6" style="list-style: none;">
					<li style="float: left; margin: 3px 5px 0px 0px;">
						Dimensiones
					</li>
					<li>
						<input type="text" style="width: 380px; text-align:left;" class="myinput" readonly/>
					</li>
				</td>
			</tr>
			<tr>
				<td rowspan="4" style="vertical-align: -webkit-baseline-middle;">
					Figura
				</td>
				<td colspan="3" rowspan="4" style="height:100px; vertical-align: -webkit-baseline-middle;">
					&nbsp;
				</td>
				<td colspan="2" rowspan="4">
					&nbsp;
				</td>
			</tr>
			<tr>
			</tr>
			<tr>
			</tr>
			<tr>
			</tr>
			<tr>
				<td colspan="4">
					&nbsp;
				</td>
				<td colspan="2">
					______________________
				</td>
			</tr>
			<tr>
				<td colspan="4">
					&nbsp;
				</td>
				<td colspan="2">
					Revisó
				</td>
			</tr>
		</table>
		<br /><br />
		<div id="header">
			<li>
				<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
			</li>
			<li style="font-size: 16px; padding:10px 0px; margin-left: 6px;">
				Figuraciones
			</li>
			<li style="padding:5px 0px 0px 0px; margin-left: 6px;">
				<table  cellspacing="0" cellpadding="0" style="font-size: 11px; text-align:center;">
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
							<input type="text" id="interno" class="myinput" style="width: 70px; text-align:center;" readonly/>
						</td>
						<td style="padding-right:3px;">
							<input type="text" id="fecha_impreso" class="myinput" style="width: 105px; text-align:center;" readonly/>
						</td>
						<td>
							<input type="text" id="impreso_por" class="myinput" style="width: 50px; text-align:center;" readonly/>
						</td>
					</tr>
				</table>
			</li>
		</div>
		<table class="table1" border="0" cellspacing="1" cellpadding="1" style="text-align: center;">
			<tr>
				<td>
					Cliente
				</td>
				<td colspan="3">
					<input type="text" style="width: 280px; text-align:left;" class="myinput" readonly/>
				</td>
				<td style="width: 50px;">
					Cliente ID
				</td>
				<td>
					<input type="text" style="width: 70px; text-align:center;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td rowspan="4" style="vertical-align: -webkit-baseline-middle;">
					Figura
				</td>
				<td colspan="2" rowspan="4" style="height:100px; vertical-align: -webkit-baseline-middle;">
					&nbsp;
				</td>
				<td>
					Diametro
				</td>
				<td colspan="2">
					<input type="text" style="width: 123px; height: 20px; text-align:left;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Cantidad
				</td>
				<td colspan="2">
					<input type="text" style="width: 123px; height: 20px; text-align:left;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="3">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="4" style="list-style: none;">
					<li style="float: left; margin: 3px 5px 0px 0px;">
						Dimensiones
					</li>
					<li>
						<input type="text" style="width: 255px; height: 20px; text-align:left;" class="myinput" readonly/>
					</li>
				</td>
				<td>
					Peso (kg)
				</td>
				<td>
					<input type="text" style="width: 70px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Revisó
				</td>
				<td>
					<input type="text" style="width: 141px; height: 20px; text-align:left;" class="myinput" readonly/>
				</td>
				<td style="width: 64px;">
					Ord. Compra
				</td>
				<td>
					<input type="text" style="width: 70px; height: 20px; text-align:center;" class="myinput" readonly/>
				</td>
				<td>
					Ord. Prod.
				</td>
				<td>
					<input type="text" style="width: 70px; height: 20px; text-align:center;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					Recibe
				</td>
				<td colspan="3" style="list-style: none;">
					<li style="float: left; margin-left: 2px;">
						<input type="text" style="width: 171px; height: 20px; text-align:left;" class="myinput" readonly/>
					</li>
					<li style="float: left; margin: 3px 5px 0px 9px;">
						Firma
					</li>
					<li>
						<input type="text" style="width: 70px; height: 20px; text-align:center;" class="myinput" readonly/>
					</li>
				</td>
				<td>
					C.C.
				</td>
				<td>
					<input type="text" style="width: 70px; height: 20px; text-align:center;" class="myinput" readonly/>
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