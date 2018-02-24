<?php
	session_start();
	$Interno = "";
	if (isset($_GET['Interno'])){
		$Interno = $_GET['Interno'];
	}
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
			data: {"Nomina_Novedades_Modificar":"<?php echo $Interno; ?>"},
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
				table += "	</tr>";
				
				var len = data.length;
				for (var i = 0; i < len; i++)
				{
					if (i == 1)
					{
						$("#interno").val("<?php echo $Interno; ?>");
						$("#novedad").html(data[0]["Novedad"]);
						$("#fecha_ini").val(data[0]["Fecha_Ini"]);
						$("#fecha_fin").val(data[0]["Fecha_Fin"]);
						$("#hora_ini").val(data[0]["FechaHora_Ini"]);
						$("#hora_fin").val(data[0]["FechaHora_Fin"]);
						$("#total").val(data[0]["Total_Ausencia"]);
						//---
						$("#justificacion").val(data[0]["Justificacion"]);
						$("#comentario").val(data[0]["Comentario"]);
						$("#reposicion").val(data[0]["Reposicion"]);
						$("#reemplazo").val(ClienteData[data[0]["ReemplazoID"]]);
						$("#reemplazo_id").val(data[0]["ReemplazoID"]);
						$("#observaciones").val(data[0]["Observacion"]);
						$("#estado").val(data[0]["Estado"]);
						$("#digitado_por").val(data[0]["Digitado_Por"]);
						$("#fecha_digitado").val(data[0]["Fecha_Digitado"]);
						$("#aprobado_por").val(data[0]["Aprobado_Por"]);
						$("#modificado_por").val(data[0]["Modificado_Por"]);
						//---
						$("#solicitante").html(ClienteData[data[0]["EmpleadoID"]]);
						$("#solicitante_id").html(data[0]["EmpleadoID"]);
						$("#autorizador").html(ClienteData[data[0]["AutorizadorID"]]);
						$("#autorizador_id").html(data[0]["AutorizadorID"]);
					}
					
					if (data[i]["Fecha"] != "" && data[i]["Fecha"] != undefined)
					{
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[i]["Fecha"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[i]["Hora_Ini"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[i]["Hora_Fin"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[i]["Total"];
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
		width: 515px;
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
		<li id="novedad" style="width:250px; padding:7px 0px; margin-left: 20px; text-align: center;">
			Novedades
		</li>
		<li style="padding:2px 0px; margin-left: 20px;">
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
	<div style="width: 700px; height: 60px;">
		<p style="text-align:left; font-size:12px;">
		Señores<br />
		<b>FERCO LTDA.</b><br />
		Por medio de la presente me permito solicitar su autorización para ausentarme de la Empresa para realizar una actividad personal sin responsabilidad laboral de la empresa y/o contratante, según la siguiente información:
		</p>
	</div>
	<table class="table1" cellspacing="2" cellpadding="2">
		<tr>
			<td style="padding-right:5px;">
				<b>Ausencia</b>
			</td>
			<td style="padding-right:5px;">
				Inicio
			</td>
			<td>
				<input type="text" id="fecha_ini" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="hora_ini" style="width: 70px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total" style="width: 50px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="padding-right:5px;">
				&nbsp;
			</td>
			<td style="padding-right:5px;">
				Final
			</td>
			<td>
				<input type="text" id="fecha_fin" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="hora_fin" style="width: 70px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="width:520px;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td style="padding-right:10px;">
				<b>Reposicion</b>
			</td>
			<td colspan="6" rowspan="20">
				<div id="mid" style="width:515px;">
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
							<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
								09.50
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td rowspan="19">
				&nbsp;
			</td>
		</tr>
	</table>
	<table class="table1" cellspacing="2" cellpadding="2" style="margin:0px;">
		<tr>
			<td style="padding-right:5px;">
				Justificacion
			</td>
			<td colspan="3">
				<input type="text" id="justificacion" style="width: 220px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td style="padding-right:5px;">
				Reposicion
			</td>
			<td>
				<input type="text" id="reposicion" style="width: 150px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="padding-right:5px;">
				¿Cual?
			</td>
			<td colspan="3">
				<input type="text" id="comentario" style="width: 220px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td style="padding-right:5px;">
				Reemplazo
			</td>
			<td colspan="3">
				<input type="text" id="reemplazo" style="width: 220px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td style="padding-right:5px;">
				Cedula
			</td>
			<td>
				<input type="text" id="reemplazo_id" style="width: 150px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<table class="table1" cellspacing="2" cellpadding="2" style="margin:0px;">
		<tr>
			<td style="padding-right:25px;">
				Observ.
			</td>
			<td colspan="8" rowspan="2">
				<textarea readonly rows="3" cols="71" id="observaciones" maxlength="200" style="resize:none;"></textarea>
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
		</tr>
	</table>
	<div id="bottom" align="center">
		<div style="width: 100%; height: 100px;">
			<div style="width:33%; display: inline-block;">
				<p style="text-align:left; font-size:12px;">
				<b>Solicitante:</b>
				<br /><br /><br />
				__________________________________
				<br />
				<div id="solicitante" style="margin-top: 5px; text-align:left;"></div>
				<div id="solicitante_id" style="margin-top: 5px; text-align:left;"></div>
				</p>
			</div>
			<div style="width:33%; display: inline-block; vertical-align: top;">
				<p style="text-align:left; font-size:12px;">
				<b>Recursos humanos:</b>
				<br /><br /><br />
				__________________________________
				<br />

				</p>
			</div>
			<div style="width:33%; display: inline-block;">
				<p style="text-align:left; font-size:12px;">
				<b>Autorizado por:</b>
				
				<br /><br /><br />
				__________________________________
				<br />
				<div id="autorizador" style="text-align:left;"></div>
				<div id="autorizador_id" style="text-align:left;"></div>
				</p>
			</div>
		</div>
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />