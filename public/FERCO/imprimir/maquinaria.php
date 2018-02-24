<?php
	session_start();
	//---
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	var ClienteData = [];
	
	$.ajax(
	{
		dataType: "json",
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

	function FormatedDate(Type)
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
		
		var Today = "";
		
		switch(Type)
		{
			case "FULL":
				Today = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
			break;
			
			case "DATE":
				Today = year + "-" + month + "-" + day;
			break;
			
			case "TIME":
				Today = hours + ":" + minutes + ":" + seconds;
			break;
		}
		
		return Today;
	}
	
	//$("#fecha_impreso").val(FormatedDate());
	//$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	if ("<?php echo $Interno; ?>" != "")
	{
		$.ajax({
			dataType: "json",
			url: "../modulos/datos_productos.php",
			data: {"Maquinaria_Modificar":"<?php echo $Interno; ?>"},
			success: function (data, status, xhr)
			{
				var table = "";
				
				if (data != "")
				{
					table += "<table class=\"table\" border=\"1\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-bottom:20px;\">";
					table += "	<tr>";
					table += "		<td>";
					table += "			<img src=\"../images/logo.png\" alt=\"Logo\" width=\"150\" style=\"padding:1px;\">";
					table += "		</td>";
					table += "		<td colspan=\"3\" style=\"width:64%;\">";
					table += "			<span style=\"font-size: 16px;\">ORDEN DE SERVICIO MECANIA INICIAL</span>";
					table += "		</td>";
					table += "		<td style=\"width:15%;\">";
					table += "			<span style=\"font-size: 16px;\">No. <?php echo $Interno; ?></span>";
					table += "		</td>";
					table += "	</tr>";
					table += "	<tr>";
					table += "		<td style=\"text-align: left;\">";
					table += "			Fecha Reporte: "+FormatedDate("DATE");
					table += "		</td>";
					table += "		<td style=\"text-align: left;\">";
					table += "			Hora Reporte: "+FormatedDate("TIME");
					table += "		</td>";
					table += "		<td colspan=\"3\" style=\"text-align: left;\">";
					table += "			Vehículo o Maquinaria: "+data[0]["Tipo"];
					table += "		</td>";
					table += "	</tr>";
					table += "	<tr>";
					table += "		<td colspan=\"2\" style=\"text-align: left;\">";
					table += "			Operario Solicitante: "+ClienteData[data[0]["Operador"]];
					table += "		</td>";
					table += "		<td colspan=\"3\" style=\"text-align: left;\">";
					table += "			Firma y C.c:";
					table += "		</td>";
					table += "	</tr>";
					table += "	<tr>";
					table += "		<td colspan=\"5\" style=\"text-align: center;\">";
					table += "			<b>I. PARTE: DESCRIPCION INICIAL Y PARTES QUE PRESENTA EL PROBLEMA</b>";
					table += "		</td>";
					table += "	</tr>";
					table += "	<tr style=\"background: #DEDEDE;\">";
					table += "		<td style=\"text-align: center;\">";
					table += "			Parte";
					table += "		</td>";
					table += "		<td colspan=\"2\" style=\"text-align: center;\">";
					table += "			Problema que Presenta";
					table += "		</td>";
					table += "		<td colspan=\"2\" style=\"text-align: center;\">";
					table += "			Diagnostico Final";
					table += "		</td>";
					table += "	</tr>";
					
					for (var i = 0; i < data[0]["Items"].length; i++)
					{
						table += "	<tr>";
						table += "		<td style=\"text-align: left;\">";
						table += "			"+data[0]["Items"][i]["Parte"];
						table += "		</td>";
						table += "		<td colspan=\"2\" style=\"text-align: left;\">";
						table += "			"+data[0]["Items"][i]["Problema"];
						table += "		</td>";
						table += "		<td colspan=\"2\" style=\"text-align: left;\">";
						table += "			"+data[0]["Items"][i]["Diagnostico"];
						table += "		</td>";
						table += "	</tr>";
					}
					
					table += "	<tr>";
					table += "		<td colspan=\"5\" style=\"text-align: center;\">";
					table += "			<b>II. PARTE: RECIBIDO PARTE MECANICA</b>";
					table += "		</td>";
					table += "	</tr>";
					table += "	<tr>";
					table += "		<td style=\"text-align: left;\">";
					table += "			Fecha Recibido:";
					table += "		</td>";
					table += "		<td style=\"text-align: left;\">";
					table += "			Hora Recibido:";
					table += "		</td>";
					table += "		<td colspan=\"3\" style=\"text-align: left;\">";
					table += "			Vehículo o Maquinaria: "+data[0]["Tipo"];
					table += "		</td>";
					table += "	</tr>";
					table += "	<tr>";
					table += "		<td colspan=\"2\" style=\"text-align: left;\">";
					table += "			Mecanico: "+ClienteData[data[0]["Mecanico"]];
					table += "		</td>";
					table += "		<td colspan=\"3\" style=\"text-align: left;\">";
					table += "			Firma y C.c:";
					table += "		</td>";
					table += "	</tr>";
					table += "</table>";
					
					if (data[0]["Estado"] == "Aprobado")
					{
						table += "<table class=\"table\" border=\"1\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-bottom:20px;\">";
						table += "	<tr>";
						table += "		<td>";
						table += "			<img src=\"../images/logo.png\" alt=\"Logo\" width=\"150\" style=\"padding:1px;\">";
						table += "		</td>";
						table += "		<td colspan=\"4\" style=\"width:64%;\">";
						table += "			<span style=\"font-size: 16px;\">ORDEN DE SERVICIO MECANIA EJECUTADA</span>";
						table += "		</td>";
						table += "		<td style=\"width:15%;\">";
						table += "			<span style=\"font-size: 16px;\">No. <?php echo $Interno; ?></span>";
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td style=\"text-align: left;\">";
						table += "			Fecha Entrega:";
						table += "		</td>";
						table += "		<td colspan=\"2\" style=\"text-align: left;\">";
						table += "			Hora Entrega:";
						table += "		</td>";
						table += "		<td colspan=\"3\" style=\"text-align: left;\">";
						table += "			Vehículo o Maquinaria: "+data[0]["Tipo"];
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td colspan=\"2\" style=\"text-align: left;\">";
						table += "			Mecanico: "+ClienteData[data[0]["Mecanico"]];
						table += "		</td>";
						table += "		<td colspan=\"4\" style=\"text-align: left;\">";
						table += "			Firma y C.c:";
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td colspan=\"6\" style=\"text-align: center;\">";
						table += "			<b>I. PARTE: DIAGNOSTICO FINAL DEW LA REPARACION Y/O MANTENIMIENTO QUE LE HIZO MECANICA A LA MAQUINARIA O VECHICULO</b>";
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr style=\"background: #DEDEDE;\">";
						table += "		<td style=\"text-align: center;\">";
						table += "			Codigo";
						table += "		</td>";
						table += "		</td>";
						table += "		<td colspan=\"2\" style=\"text-align: center;\">";
						table += "			Repuesto";
						table += "		</td>";
						table += "		<td style=\"text-align: center;\">";
						table += "			Parte";
						table += "		</td>";
						table += "		<td style=\"text-align: center;\">";
						table += "			Cantidad";
						table += "		</td>";
						table += "		<td style=\"text-align: center;\">";
						table += "			Unitario";
						table += "		</td>";
						table += "	</tr>";
						
						for (var i = 0; i < data[0]["Proveedor1"].length; i++)
						{
							table += "	<tr>";
							table += "		<td style=\"text-align: left;\">";
							table += "			"+data[0]["Proveedor1"][i]["Codigo"];
							table += "		</td>";
							table += "		<td colspan=\"2\" style=\"text-align: left;\">";
							table += "			"+data[0]["Proveedor1"][i]["Repuesto"];
							table += "		</td>";
							table += "		<td style=\"text-align: left;\">";
							table += "			"+data[0]["Proveedor1"][i]["Parte"];
							table += "		</td>";
							table += "		<td style=\"text-align: right;\">";
							table += "			"+data[0]["Proveedor1"][i]["Cantidad"];
							table += "		</td>";
							table += "		<td style=\"text-align: right;\">";
							table += "			"+data[0]["Proveedor1"][i]["Unitario"];
							table += "		</td>";
							table += "	</tr>";
						}
						
						/*table += "	<tr>";
						table += "		<td colspan=\"6\" style=\"text-align: left;\">";
						table += "			&nbsp;";
						table += "		</td>";
						table += "	</tr>";*/
						
						table += "	<tr>";
						table += "		<td colspan=\"4\" style=\"text-align: center;\">";
						table += "			<b>II. PARTE: MATERIALES Y/O REPUESTOS</b>";
						table += "		</td>";
						table += "		<td style=\"text-align: left;\">";
						table += "			<b>SI:</b>";
						table += "		</td>";
						table += "		<td style=\"text-align: left;\">";
						table += "			<b>NO:</b>";
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td style=\"text-align: left; height: 35px; vertical-align: -webkit-baseline-middle;\">";
						table += "			<b>III. PARTE: SOPORTES</b>";
						table += "		</td>";
						table += "		<td colspan=\"2\" style=\"text-align: left; height: 35px; vertical-align: -webkit-baseline-middle;\">";
						table += "			NUMERO FACTURA(S):";
						if (data[0]["Proveedor1"][0]["Factura"] != undefined)
							table += "			"+data[0]["Proveedor1"][0]["Factura"]+", ";
						
						if (data[0]["Proveedor2"][0]["Factura"] != undefined)
							table += "			"+data[0]["Proveedor2"][0]["Factura"]+", ";
						
						if (data[0]["Proveedor3"][0]["Factura"] != undefined)
							table += "			"+data[0]["Proveedor3"][0]["Factura"];
						table += "		</td>";
						table += "		<td colspan=\"3\" style=\"text-align: left; height: 35px; vertical-align: -webkit-baseline-middle;\">";
						table += "			COMPROBANTE PROGRAMA CAJA: "+data[0]["Caja_Recibo"];
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td colspan=\"6\" style=\"text-align: center;\">";
						table += "			<b>IV. PARTE: RECIBIDO POR OPERARIO O CONDUCTOR</b>";
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td colspan=\"4\" style=\"text-align: center;\">";
						table += "			<b>ORDEN COMPLETA EJECUTADA</b>";
						table += "		</td>";
						table += "		<td style=\"text-align: left;\">";
						table += "			<b>SI:</b>";
						table += "		</td>";
						table += "		<td style=\"text-align: left;\">";
						table += "			<b>NO:</b>";
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td colspan=\"6\" style=\"text-align: left; height: 60px; vertical-align: -webkit-baseline-middle;\">";
						table += "			<b>OBSERVACION:</b>";
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td style=\"text-align: left;\">";
						table += "			Fecha Recibido:";
						table += "		</td>";
						table += "		<td colspan=\"2\" style=\"text-align: left;\">";
						table += "			Hora Recibido:";
						table += "		</td>";
						table += "		<td colspan=\"3\" style=\"text-align: left;\">";
						table += "			Operario Solicitante: "+ClienteData[data[0]["Operador"]];
						table += "		</td>";
						table += "	</tr>";
						table += "	<tr>";
						table += "		<td colspan=\"3\" style=\"text-align: left;\">";
						table += "			Firma y C.c:";
						table += "		</td>";
						table += "		<td colspan=\"3\" style=\"text-align: left;\">";
						table += "			Verifico, firma y C.c:";
						table += "		</td>";
						table += "	</tr>";
						table += "</table>";
					}
				}
				
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
		//$("#fecha_impreso").val(FormatedDate());
		window.print();
	});
});
</script>
<style type="text/css">
	.myinput
	{
		font-size: 12px;
		padding: 2px;
		font-family:calibri;
		height: 20px;
	}
	
	.table
	{
		table-layout:fixed;
		font-family:calibri;
		width: 800px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
		list-style:none;
	}
	
	#bottom
	{
		font-family:calibri;
		margin-top: 5px;
		width: 800px;
		list-style:none;
	}
	
	#print_button
	{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #FAA732;
		border-color: #FAA732;
	}
	
	@media print
	{
		#header{
			-webkit-print-color-adjust: exact;
		}
		.table{
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
		margin: 0.5cm 0.7cm 1.0cm 0.5cm;
	}
</style>
<div id="mid">
	<table class="table" border="1" cellspacing="0" cellpadding="2" style="margin-bottom:20px;">
		<tr>
			<td>
				<img src="../images/logo.png" alt="Logo" width="150" style="padding:1px;">
			</td>
			<td colspan="3" style="width:64%;">
				<span style="font-size: 16px;">ORDEN DE SERVICIO MECANIA INICIAL</span>
			</td>
			<td style="width:15%;">
				<span style="font-size: 16px;">No. 0000001</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: left;">
				Fecha Reporte:
			</td>
			<td style="text-align: left;">
				Hora Reporte:
			</td>
			<td colspan="3" style="text-align: left;">
				Vehículo o Maquinaria:
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: left;">
				Operario Solicitante:
			</td>
			<td colspan="3" style="text-align: left;">
				Firma y C.c:
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align: center;">
				<b>I. PARTE: DESCRIPCION INICIAL Y PARTES QUE PRESENTA EL PROBLEMA</b>
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align: left;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align: left;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align: left;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="5" style="text-align: center;">
				<b>II. PARTE: RECIBIDO PARTE MECANICA</b>
			</td>
		</tr>
		<tr>
			<td style="text-align: left;">
				Fecha Recibido:
			</td>
			<td style="text-align: left;">
				Hora Recibido:
			</td>
			<td colspan="3" style="text-align: left;">
				Vehículo o Maquinaria:
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: left;">
				Mecanico:
			</td>
			<td colspan="3" style="text-align: left;">
				Firma y C.c:
			</td>
		</tr>
	</table>
	<table class="table" border="1" cellspacing="0" cellpadding="2" style="margin-bottom:20px;">
		<tr>
			<td>
				<img src="../images/logo.png" alt="Logo" width="150" style="padding:1px;">
			</td>
			<td colspan="4" style="width:64%;">
				<span style="font-size: 16px;">ORDEN DE SERVICIO MECANIA EJECUTADA</span>
			</td>
			<td style="width:15%;">
				<span style="font-size: 16px;">No. 0000001</span>
			</td>
		</tr>
		<tr>
			<td style="text-align: left;">
				Fecha Entrega:
			</td>
			<td colspan="2" style="text-align: left;">
				Hora Entrega:
			</td>
			<td colspan="3" style="text-align: left;">
				Vehículo o Maquinaria:
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: left;">
				Mecanico:
			</td>
			<td colspan="4" style="text-align: left;">
				Firma y C.c:
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: center;">
				<b>I. PARTE: DIAGNOSTICO FINAL DEW LA REPARACION Y/O MANTENIMIENTO QUE LE HIZO MECANICA A LA MAQUINARIA O VECHICULO</b>
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: left;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: left;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: left;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: left;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: left;">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="4" style="text-align: center;">
				<b>II. PARTE: MATERIALES Y/O REPUESTOS</b>
			</td>
			<td style="text-align: left;">
				<b>SI:</b>
			</td>
			<td style="text-align: left;">
				<b>NO:</b>
			</td>
		</tr>
		<tr>
			<td style="text-align: left; height: 35px; vertical-align: -webkit-baseline-middle;">
				<b>III. PARTE: SOPORTES</b>
			</td>
			<td colspan="2" style="text-align: left; height: 35px; vertical-align: -webkit-baseline-middle;">
				NUMERO FACTURA(S):
			</td>
			<td colspan="3" style="text-align: left; height: 35px; vertical-align: -webkit-baseline-middle;">
				COMPROBANTE PROGRAMA CAJA:
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: center;">
				<b>IV. PARTE: RECIBIDO POR OPERARIO O CONDUCTOR</b>
			</td>
		</tr>
		<tr>
			<td colspan="4" style="text-align: center;">
				<b>ORDEN COMPLETA EJECUTADA</b>
			</td>
			<td style="text-align: left;">
				<b>SI:</b>
			</td>
			<td style="text-align: left;">
				<b>NO:</b>
			</td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: left; height: 60px; vertical-align: -webkit-baseline-middle;">
				<b>OBSERVACION:</b>
			</td>
		</tr>
		<tr>
			<td style="text-align: left;">
				Fecha Recibido:
			</td>
			<td colspan="2" style="text-align: left;">
				Hora Recibido:
			</td>
			<td colspan="3" style="text-align: left;">
				Operario Solicitante:
			</td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: left;">
				Firma y C.c:
			</td>
			<td colspan="3" style="text-align: left;">
				Verifico, firma y C.c:
			</td>
		</tr>
	</table>
</div>
<div id="bottom" align="center">
	<input type="button" id="print_button" value="Imprimir"/>
</div>