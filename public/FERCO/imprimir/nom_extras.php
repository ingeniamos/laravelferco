<?php
	session_start();
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
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
	
	if ("<?php echo $Interno; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {"Nomina_Extras_Modificar":"<?php echo $Interno; ?>"},
			success: function (data, status, xhr)
			{
				var table = "";
				table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; width:25%;\">";
				table += "			Fecha";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
				table += "			Hora Inicio";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
				table += "			Hora Final";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table += "			Total";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Fest.";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Noct.";
				table += "		</td>";
				table += "	</tr>";
				
				var len = data[0]["GridData"].length;
				for (var i = 0; i < len; i++)
				{
					if (data[0]["GridData"][i]["Turno"] != "" && data[0]["GridData"][i]["Turno"] != undefined)
					{
						if (i == 1)
						{
							$("#interno").val("<?php echo $Interno; ?>");
							$("#empleado").val(ClienteData[data[0]["EmpleadoID"]]);
							$("#empleado_id").val(data[0]["EmpleadoID"]);
							$("#justificacion").val(data[0]["Justificacion"]);
							$("#comentario").val(data[0]["Comentario"]);
							$("#observaciones").val(data[0]["Observacion"]);
							$("#autorizador").html(ClienteData[data[0]["AutorizadorID"]]);
							$("#autorizador_id").html(data[0]["AutorizadorID"]);
							$("#estado").val(data[0]["Estado"]);
							$("#digitado_por").val(data[0]["Digitado_Por"]);
							$("#fecha_digitado").val(data[0]["Fecha_Digitado"]);
							$("#aprobado_por").val(data[0]["Aprobado_Por"]);
							$("#modificado_por").val(data[0]["Modificado_Por"]);
						}
						
						if (data[0]["GridData"][i]["Festivo"] == "true")
							var Festivo = "checked";
						else
							var Festivo = "";
						
						if (data[0]["GridData"][i]["Nocturno"] == "true")
							var Nocturno = "checked";
						else
							var Nocturno = "";
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[0]["GridData"][i]["Turno"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[0]["GridData"][i]["Hora_Ini"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[0]["GridData"][i]["Hora_Fin"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[0]["GridData"][i]["Total"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			<input type=\"checkbox\" "+Festivo+" readonly/>";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table += "			<input type=\"checkbox\" "+Nocturno+" readonly/>";
						table += "		</td>";
						table += "	</tr>";
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
	
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
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
		width: 700px;
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
		width: 250px;
		font-family:calibri;
		margin-top: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 700px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	#bottom{
		font-family:calibri;
		font-size: 12px;
		margin-top: 5px;
		width: 700px;
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
		<li style="padding:7px 0px; margin-left: 110px;">
			Horas Extras
		</li>
		<li style="padding:2px 0px; margin-left: 80px;">
			<table  cellspacing="0" cellpadding="0" style="font-size:12px; text-align:center;">
				<tr>
					<td>
						Interno
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
			<td style="padding-right:6px;">
				Empleado
			</td>
			<td>
				<input type="text" id="empleado" style="width: 350px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td style="padding-left:30px; padding-right:10px;">
				Cedula
			</td>
			<td>
				<input type="text" id="empleado_id" style="width: 200px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="padding-right:6px;">
				Justificacion
			</td>
			<td>
				<input type="text" id="justificacion" style="width: 350px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td style="padding-left:30px; padding-right:10px;">
				¿Cual?
			</td>
			<td>
				<input type="text" id="comentario" style="width: 200px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:25%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					Hora Inicio
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					Hora Final
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Total
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fest.
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Noct.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					2015-09-01
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					10:30 A.M.
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					10:00 P.M.
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					09.50
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					<input type="checkbox" checked readonly/>
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					<input type="checkbox" readonly/>
				</td>
			</tr>
		</table>
	</div>
	<table class="table1" cellspacing="2" cellpadding="1">
		<tr>
			<td style="padding-right:5px;">
				Observ.
			</td>
			<td colspan="9" rowspan="2">
				<textarea readonly rows="3" cols="90" id="observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<input type="text" id="estado" style="width:80px;" class="myinput" readonly/>
			</td>
			<td>
				Dig.
			</td>
			<td>
				<input type="text" id="digitado_por" style="width:70px;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="fecha_digitado" style="width:120px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Apr.
			</td>
			<td>
				<input type="text" id="aprobado_por" style="width:70px;" class="myinput" readonly/>
			</td>
			<td>
				Mod.
			</td>
			<td>
				<input type="text" id="modificado_por" style="width:70px;" class="myinput" readonly/>
			</td>
			<td style="width:120px;">
				&nbsp;
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<p style="text-align:left; font-size:12px;">
		<b>Autorizado por:</b>
		<br /><br /><br />
		__________________________________
		<br />
		<div id="autorizador" style="text-align:left;"></div>
		<div id="autorizador_id" style="text-align:left;"></div>
		</p>
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />