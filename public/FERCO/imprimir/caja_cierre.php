<?php
	session_start();
	$DigitadorID = isset($_GET['DigitadorID']) ? $_GET['DigitadorID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
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
	
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	if ("<?php echo $Estado; ?>" != "")
	{
		var FinalDiv = "";
		var table1 = "";
		var finished1 = false;
		var table2 = "";
		var finished2 = false;
		var table3 = "";
		var finished3 = false;
		var table4 = "";
		var finished4 = false;
		
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Caja_Cierre_Datos":true,
				"DigitadorID":"<?php echo $DigitadorID; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
				"Estado":"<?php echo $Estado; ?>",
			},
			async: true,
			success: function (data, status, xhr)
			{
				if (data[0]["Total_Ingresos"] != undefined && data[0]["Total_Ingresos"] != "")
				{
					$("#total_ingresos").val(data[0]["Total_Ingresos"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_egresos").val(data[0]["Total_Egresos"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_recaudos").val(data[0]["Recaudos_Cartera"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_cheques_dev").val(data[0]["Recaudos_Cheques"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_retenciones").val(data[0]["Retenciones"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_descuentos").val(data[0]["Descuentos"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_efectivo").val(data[0]["Total_Efectivo"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_cheques").val(data[0]["Total_Cheques"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_saldo").val(data[0]["Saldo_Caja"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					$("#total_tarjetas").val(data[0]["Total_Tarjetas"].toLocaleString('es-ES', { minimumFractionDigits: '2' }));
					
					$("#caja").val("<?php echo $DigitadorID == "" ? "Todos":$DigitadorID; ?>");
					$("#fecha_ini").val("<?php echo $Fecha_Ini; ?>");
					$("#fecha_fin").val("<?php echo $Fecha_Fin; ?>");
					$("#estado").val("<?php echo $Estado; ?>");
				}
			}, 
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
		
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Caja_Cierre_Todo":true,
				"DigitadorID":"<?php echo $DigitadorID; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
				"Estado":"<?php echo $Estado; ?>",
			},
			async: true,
			success: function (data1, status, xhr)
			{
				table1 += "<div align=\"center\" style=\"width: 900px; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;\">";
				table1 += "	Resumen Total";
				table1 += "</div>";
				table1 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table1 += "<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table1 += "		Fecha";
				table1 += "	</td>";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table1 += "		Interno";
				table1 += "	</td>";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table1 += "		Recibo";
				table1 += "	</td>";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table1 += "		Categoria";
				table1 += "	</td>";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:24%;\">";
				table1 += "		Beneficiario";
				table1 += "	</td>";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table1 += "		Total";
				table1 += "	</td>";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table1 += "		Efectivo";
				table1 += "	</td>";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table1 += "		Dig.";
				table1 += "	</td>";
				table1 += "	<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table1 += "		Aut.";
				table1 += "	</td>";
				table1 += "</tr>";
				
				var len = data1.length;
				for (var i = 0; i < len; i++)
				{
					if (data1[i]["Total"] != "" && data1[i]["Total"] != undefined)
					{
						var Total1 = parseFloat(data1[i]["Total"]);
						var Efectivo1 = parseFloat(data1[i]["Efectivo"]);
						table1 += "<tr>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+SetFormattedDate(data1[i]["Fecha"]);
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data1[i]["Caja_Interno"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table1 += "		"+data1[i]["Caja_Recibo"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data1[i]["Categoria"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table1 += "		"+ClienteData[""+data1[i]["Cliente"]+""];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table1 += "		$ "+Total1.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table1 += "		$ "+Efectivo1.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data1[i]["DigitadorID"];
						table1 += "	</td>";
						table1 += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table1 += "		"+data1[i]["AutorizadorID"];
						table1 += "	</td>";
						table1 += "</tr>";
					}
				}
				table1 += "</table>";
				finished1 = true;
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
		
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Caja_Cierre_Cheques_Dev":true,
				"DigitadorID":"<?php echo $DigitadorID; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
				"Estado":"<?php echo $Estado; ?>",
			},
			async: true,
			success: function (data2, status, xhr)
			{
				table2 += "<div align=\"center\" style=\"width: 900px; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;\">";
				table2 += "	Resumen de Cheques Devueltos";
				table2 += "</div>";
				table2 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table2 += "<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table2 += "		Fecha";
				table2 += "	</td>";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table2 += "		Interno";
				table2 += "	</td>";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table2 += "		Recibo";
				table2 += "	</td>";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table2 += "		Categoria";
				table2 += "	</td>";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:24%;\">";
				table2 += "		Beneficiario";
				table2 += "	</td>";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table2 += "		Total";
				table2 += "	</td>";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table2 += "		Efectivo";
				table2 += "	</td>";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table2 += "		Dig.";
				table2 += "	</td>";
				table2 += "	<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table2 += "		Aut.";
				table2 += "	</td>";
				table2 += "</tr>";
				
				var len = data2.length;
				for (var i = 0; i < len; i++)
				{
					if (data2[i]["Cliente"] != undefined)
					{
						var Total2 = parseFloat(data2[i]["Total"]);
						var Efectivo2 = parseFloat(data2[i]["Efectivo"]);
						table2 += "<tr>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+SetFormattedDate(data2[i]["Fecha"]);
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data2[i]["Caja_Interno"];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data2[i]["Caja_Recibo"];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data2[i]["Categoria"];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table2 += "		"+ClienteData[""+data2[i]["Cliente"]+""];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table2 += "		$ "+Total2.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table2 += "		$ "+Efectivo2.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data2[i]["DigitadorID"];
						table2 += "	</td>";
						table2 += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table2 += "		"+data2[i]["AutorizadorID"];
						table2 += "	</td>";
						table2 += "</tr>";
					}
				}
				table2 += "</table>";
				finished2 = true;
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				finished2 = true;
				//alert(textStatus+ " - " +errorThrown);
			}
		});
		
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Caja_Cierre_Cheques":true,
				"DigitadorID":"<?php echo $DigitadorID; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			},
			async: true,
			success: function (data3, status, xhr)
			{
				table3 += "<div align=\"center\" style=\"width: 900px; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;\">";
				table3 += "	Resumen de Cheques";
				table3 += "</div>";
				table3 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table3 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table3 += "			Fecha";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table3 += "			Recibo";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table3 += "			Numero Cheque";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table3 += "			Valor";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
				table3 += "			Banco";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table3 += "			Numero Cuenta";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table3 += "			Fecha Cheque";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:24%;\">";
				table3 += "			Titular";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table3 += "			Dig.";
				table3 += "		</td>";
				table3 += "	</tr>";
				
				var len = data3.length;
				for (var i = 0; i < len; i++)
				{
					if (data3[i]["Cliente"] != undefined)
					{
						var Total3 = parseFloat(data3[i]["Valor"]);
						table3 += "<tr>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table3 += "		"+SetFormattedDate(data3[i]["Fecha"]);
						table3 += "	</td>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table3 += "		"+data3[i]["Caja_Recibo"];
						table3 += "	</td>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table3 += "		"+data3[i]["NumeroCheque"];
						table3 += "	</td>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table3 += "		$ "+Total3.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table3 += "	</td>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table3 += "		"+data3[i]["Banco"];
						table3 += "	</td>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table3 += "		"+data3[i]["NumeroCuenta"];
						table3 += "	</td>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table3 += "		"+SetFormattedDate(data3[i]["FechaCheque"]);
						table3 += "	</td>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table3 += "		"+ClienteData[""+data3[i]["Cliente"]+""];
						table3 += "	</td>";
						table3 += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table3 += "		"+data3[i]["DigitadorID"];
						table3 += "	</td>";
						table3 += "</tr>";
					}
				}
				table3 += "</table>";
				finished3 = true;
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				finished3 = true;
				//alert(textStatus+ " - " +errorThrown);
			}
		});
		
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Caja_Cierre_Consignaciones":true,
				"DigitadorID":"<?php echo $DigitadorID; ?>",
				"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
				"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			},
			async: true,
			success: function (data4, status, xhr)
			{
				table4 += "<div align=\"center\" style=\"width: 900px; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;\">";
				table4 += "	Resumen de Consignaciones";
				table4 += "</div>";
				table4 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table4 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table4 += "			Fecha";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table4 += "			Recibo";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
				table4 += "			Tipo";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
				table4 += "			Banco";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table4 += "			# Aprobacion";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
				table4 += "			Valor";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:24%;\">";
				table4 += "			Titular";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table4 += "			Dig.";
				table4 += "		</td>";
				table4 += "	</tr>";
				
				var len = data4.length;
				for (var i = 0; i < len; i++)
				{
					if (data4[i]["Cliente"] != undefined)
					{
						var Total4 = parseFloat(data4[i]["Valor"]);
						table4 += "<tr>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+SetFormattedDate(data4[i]["Fecha"]);
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data4[i]["Caja_Recibo"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data4[i]["Tipo"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table4 += "		"+data4[i]["Banco"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data4[i]["NumeroAprobacion"];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table4 += "		$ "+Total4.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table4 += "		"+ClienteData[""+data4[i]["Cliente"]+""];
						table4 += "	</td>";
						table4 += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table4 += "		"+data4[i]["DigitadorID"];
						table4 += "	</td>";
						table4 += "</tr>";
					}
				}
				table4 += "</table>";
				finished4 = true;
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				finished4 = true;
				//alert(textStatus+ " - " +errorThrown);
			}
		});
		
		var Timer = setInterval(function()
		{
			if (finished1 == true && finished2 == true && finished3 == true && finished4 == true)
			{
				FinalDiv += table1 + table2 + table3 + table4;
				$("#mid").html(FinalDiv);
				clearInterval(Timer);
			}
		},10);
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
		padding: 2px;
	}
	
	.table1{
		width: 900px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: center;
		font-size: 12px;
	}
	
	.table2{
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
	
	.table2 li{
		float: left;
		padding: 2px;
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
		<li style="padding:7px 0px; margin-left: 260px;">
			Cierre de Caja
		</li>
		<li style="padding:2px 0px; margin-left: 215px;">
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
			<td colspan="2">
				&nbsp;
			</td>
			<td style="text-align:left;">
				Total Ingresos 
			</td>
			<td>
				<input type="text" id="total_ingresos" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Total Egresos
			</td>
			<td>
				<input type="text" id="total_egresos" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="text-align:left;">
				Caja
			</td>
			<td>
				<input type="text" id="caja" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Recaudos Venta/Cartera
			</td>
			<td>
				<input type="text" id="total_recaudos" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Recaudos Cheques Dev.
			</td>
			<td>
				<input type="text" id="total_cheques_dev" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="text-align:left;">
				Fecha Inicial
			</td>
			<td>
				<input type="text" id="fecha_ini" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Retenciones Ferco
			</td>
			<td>
				<input type="text" id="total_retenciones" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Descuentos
			</td>
			<td>
				<input type="text" id="total_descuentos" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="text-align:left;">
				Fecha Final
			</td>
			<td>
				<input type="text" id="fecha_fin" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Total Efectivo
			</td>
			<td>
				<input type="text" id="total_efectivo" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Total Cheques
			</td>
			<td>
				<input type="text" id="total_cheques" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="text-align:left;">
				Estado
			</td>
			<td>
				<input type="text" id="estado" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Saldo en Caja
			</td>
			<td>
				<input type="text" id="total_saldo" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td style="text-align:left;">
				Total Tarjetas
			</td>
			<td>
				<input type="text" id="total_tarjetas" style="width: 170px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<div align="center" style="width: 900px; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
			Resumen Total
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:8%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Interno
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Recibo
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Categoria
				</td>
				<td style="border-bottom:grey 1px solid; width:24%;">
					Beneficiario
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Total
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Efectivo
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Dig.
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Aut.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Ingresos
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					BOX
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					BOX
				</td>
			</tr>
		</table>
		<div align="center" style="width: 900px; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
			Resumen de Cheques Devueltos
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:8%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Interno
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Recibo
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Categoria
				</td>
				<td style="border-bottom:grey 1px solid; width:24%;">
					Beneficiario
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Total
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Efectivo
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Dig.
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Aut.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Ingresos
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					BOX
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					BOX
				</td>
			</tr>
		</table>
		<div align="center" style="width: 900px; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
			Resumen de Cheques
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Recibo
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Numero Cheque
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Valor
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Banco
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Numero Cuenta
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fecha Cheque
				</td>
				<td style="border-bottom:grey 1px solid; width:24%;">
					Titular
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Dig.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					000-00-00000-00
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					00
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					000-00-00000-00
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					BOX
				</td>
			</tr>
		</table>
		<div align="center" style="width: 900px; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
			Resumen de Consignaciones
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Recibo
				</td>
				<td style="border-bottom:grey 1px solid; width:9%;">
					Tipo
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Banco
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					# Aprobacion
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Valor
				</td>
				<td style="border-bottom:grey 1px solid; width:24%;">
					Titular
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Dig.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Consignacion
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					BanColombia
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					000-00-00000-00
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					BOX
				</td>
			</tr>
		</table>
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />