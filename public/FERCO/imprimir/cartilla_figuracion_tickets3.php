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
				var header = "";
				var table = "";
				
				var Break = 0;
				if (data[0] != undefined)
				{
					for (var i = 0; i < Lista.length; i++)
					{
						Break++;
						if (Break > 6 && Break < 9) {
							if (Break == 8)
								Break = 0;
							header += "<div style=\"float: left; margin: 0px 20px 8px 5px;\">";
						}
						else
							header += "<div style=\"float: left; margin: 0px 20px 26px 5px;\">";
						header += "<div id=\"header\">";
						header += "	<li>";
						header += "		<img src=\"../images/logo.png\" alt=\"Logo\" width=\"100\" style=\"padding:1px 0px 0px 5px;\">";
						header += "	</li>";
						header += "	<li style=\"padding:4px 0px 0px 0px; margin-left: 33px;\">";
						header += "		<table cellspacing=\"0\" cellpadding=\"0\" style=\"font-size: 9px; text-align:center;\">";
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
						header += "					<input type=\"text\" id=\"interno\" class=\"myinput\" style=\"width: 60px; text-align:center; font-size: 9px;\" readonly value=\""+Lista[i]["Ticket"]+"\"/>";
						header += "				</td>";
						header += "				<td style=\"padding-right:3px;\">";
						header += "					<input type=\"text\" id=\"fecha_impreso\" class=\"myinput\" style=\"width: 95px; text-align:center; font-size: 9px;\" readonly value=\""+FormatedDate()+"\"/>";
						header += "				</td>";
						header += "				<td>";
						header += "					<input type=\"text\" id=\"impreso_por\" class=\"myinput\" style=\"width: 45px; text-align:center; font-size: 9px;\" readonly value=\"<?php echo isset($_SESSION["UserCode"]) ? $_SESSION["UserCode"]:""; ?>\"/>";
						header += "				</td>";
						header += "			</tr>";
						header += "		</table>";
						header += "	</li>";
						header += "</div>";
				
						for (var a = 0; a < data[0]["Items"].length; a++)
						{
							if (Lista[i]["Codigo"] == data[0]["Items"][a]["Codigo"] &&
							Lista[i]["Figura"] == data[0]["Items"][a]["Imagen"] )
							{
								var CantidadEmpaque = parseFloat(Lista[i]["Cantidad"]);
								var Cantidad = parseFloat(data[0]["Items"][a]["Cantidad"]);
								var Longitud = parseFloat(data[0]["Items"][a]["Longitud"]) * Cantidad;
								var Peso = parseFloat(data[0]["Items"][a]["Peso"]);
								var Detalle = data[0]["Items"][a]["Detalle"];
								var Ubicacion = data[0]["Items"][a]["Ubicacion"];
								
								table += header;
								table += "<table class=\"table1\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" style=\"text-align: center;\">";
								table += "	<tr>";
								table += "		<td style=\"text-align: left; width: 30px;\">";
								table += "			Cartilla";
								table += "		</td>";
								table += "		<td colspan=\"5\" style=\"text-align: left; list-style: none;\">";
								table += "			<li style=\"float: left; margin: 0px 4px 0px 0px;\">";
								table += "				<input type=\"text\" style=\"width: 58px; text-align:left;\" class=\"myinput\" readonly value=\""+Lista[i]["Interno"]+"\"/>";
								table += "			</li>";
								table += "			<li style=\"float: left; margin: 3px 6px 0px 0px;\">";
								table += "				Orden Compra";
								table += "			</li>";
								table += "			<li style=\"float: left;\">";
								table += "				<input type=\"text\" style=\"width: 65px; text-align:center;\" class=\"myinput\" readonly value=\""+data[0]["Orden_Compra"]+"\"/>";
								table += "			</li>";
								table += "			<li style=\"float: left; margin: 3px 6px 0px 6px;\">";
								table += "				Orden Produccion";
								table += "			</li>";
								table += "			<li style=\"float: left;\">";
								table += "				<input type=\"text\" style=\"width: 60px; text-align:center;\" class=\"myinput\" readonly value=\""+data[0]["Orden_Produccion"]+"\"/>";
								table += "			</li>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td style=\"text-align: left;\">";
								table += "			Cliente";
								table += "		</td>";
								table += "		<td style=\"text-align: left;\" colspan=\"3\">";
								table += "			<input type=\"text\" style=\"width: 200px; text-align:left;\" class=\"myinput\" readonly value=\""+data[0]["Cliente"]+"\"/>";
								table += "		</td>";
								table += "		<td style=\"width: 50px;\">";
								table += "			Cliente ID";
								table += "		</td>";
								table += "		<td style=\"width: 70px;\">";
								table += "			<input type=\"text\" style=\"width: 60px; text-align:center;\" class=\"myinput\" readonly value=\""+data[0]["ClienteID"]+"\"/>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"6\" style=\"list-style: none;\">";
								table += "			<li style=\"float: left; margin: 3px 15px 0px 0px;\">";
								table += "				Obra";
								table += "			</li>";
								table += "			<li style=\"float: left;\">";
								table += "				<input type=\"text\" style=\"width: 120px; text-align:left;\" class=\"myinput\" readonly value=\""+data[0]["Obra"]+"\"/>";
								table += "			</li>";
								table += "			<li style=\"float: left; margin: 3px 7px 0px 7px;\">";
								table += "				Diam.";
								table += "			</li>";
								table += "			<li style=\"float: left;\">";
								table += "				<input type=\"text\" style=\"width: 162px; text-align:left;\" class=\"myinput\" readonly value=\""+data[0]["Items"][a]["Nombre"]+"\"/>";
								table += "			</li>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"4\" style=\"list-style: none;\">";
								table += "			<li style=\"float: left; margin: 3px 5px 0px 0px;\">";
								table += "				No. Elementos en Empaque";
								table += "			</li>";
								table += "			<li>";
								table += "				<input type=\"text\" style=\"width: 133px; text-align:left;\" class=\"myinput\" readonly value=\""+CantidadEmpaque.toLocaleString('es-ES')+"\"/>";
								table += "			</li>";
								table += "		</td>";
								table += "		<td>";
								table += "			Peso (kg)";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" style=\"width: 60px; text-align:right;\" class=\"myinput\" readonly value=\""+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"6\" style=\"list-style: none;\">";
								table += "			<li style=\"float: left; margin: 3px 5px 0px 0px;\">";
								table += "				Dimensiones";
								table += "			</li>";
								table += "			<li>";
								table += "				<input type=\"text\" style=\"width: 294px; text-align:left;\" class=\"myinput\" readonly value=\""+Detalle.replace(/<br \/>/g, " ")+"\"/>";
								table += "			</li>";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"2\" style=\"text-align: left;\">";
								table += "			Figura";
								table += "		</td>";
								table += "		<td colspan=\"4\" style=\"text-align: left;\">";
								table += "			Descripcion";
								table += "		</td>";
								table += "	</tr>";
								table += "	<tr>";
								table += "		<td colspan=\"6\" style=\"list-style: none; height:57px; text-align: left; vertical-align: -webkit-baseline-middle;\">";
								table += "			<li style=\"float: left; margin-right: 35px;\">";
								table += "				<img src=\"../images/"+data[0]["Items"][a]["Imagen"]+"\" width=\"100px\"/>";
								table += "			</li>";
								table += "			<li>";
								table += "				<textarea style=\"width:213px; height:50px; resize:none; text-align:left;\" class=\"myinput\" readonly >"+Ubicacion.replace(/<br\/>/g, " ")+"</textarea>";
								table += "			</li>";
								table += "		</td>";
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
	body {
		margin: 0px;
		padding: 0px;
	}
	
	.myinput {
		font-size: 8px;
		padding: 2px;
		font-family:sans-serif;
		height: 16px;
	}
	
	#header{
		width: 355px;
		height: 35px;
		font-family:sans-serif;
		font-size: 16px;
		background: #DEDEDE;
		list-style:none;
	}
	#header li{
		float: left;
		padding: 2px;
	}
	
	.table1{
		width: 355px;
		font-family:sans-serif;
		margin-top: 5px;
		text-align: left;
		font-size: 8px;
	}
	
	#bottom{
		font-family:sans-serif;
		margin-top: 5px;
		width: 355px;
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
			margin: 0.3cm;
		}
	}
</style>
<div>
	<div id="mid" style="width: 760px;">
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />