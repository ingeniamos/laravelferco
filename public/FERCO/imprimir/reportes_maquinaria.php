<?php
	session_start();
	//---
	$OrderBy = isset($_GET["OrderBy"]) ? $_GET["OrderBy"]:"";
	$Fecha_Ini = isset($_GET["Fecha_Ini"]) ? $_GET["Fecha_Ini"]:"0000-00-00";
	$Fecha_Fin = isset($_GET["Fecha_Fin"]) ? $_GET["Fecha_Fin"]:"0000-00-00";
	$Categoria = isset($_GET["Categoria"]) ? $_GET["Categoria"]:"";
	$Tipo = isset($_GET["Tipo"]) ? $_GET["Tipo"]:"";
	$Motivo = isset($_GET["Motivo"]) ? $_GET["Motivo"]:"";
	$Diagnostico = isset($_GET["Diagnostico"]) ? $_GET["Diagnostico"]:"";
	$ClienteID = isset($_GET["ClienteID"]) ? $_GET["ClienteID"]:"";
	$DigitadorID = isset($_GET["DigitadorID"]) ? $_GET["DigitadorID"]:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{	
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
	
	$.ajax({
		dataType: "json",
		url: "../modulos/datos.php",
		data: {
			"Reportes_Maquinaria":true,
			"OrderBy":"<?php echo $OrderBy; ?>",
			"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
			"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			"Categoria":"<?php echo $Categoria; ?>",
			"Tipo":"<?php echo $Tipo; ?>",
			"Motivo":"<?php echo $Motivo; ?>",
			"Diagnostico":"<?php echo $Diagnostico; ?>",
			"ClienteID":"<?php echo $ClienteID; ?>",
			"DigitadorID":"<?php echo $DigitadorID; ?>",
		},
		success: function (data, status, xhr)
		{
			var table = "";
			var Total = 0;
			var Viejo_ID = "_empty_";
			
			if (data != "")
			{
				if ("<?php echo $OrderBy; ?>" == "")
				{
					table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:20px;\">";
					table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Fecha";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Orden Rep.";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Maquina/Vehiculo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
					table += "			Motivo Reparacion";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:24%;\">";
					table += "			Diagnostico";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
					table += "			Dig.";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Total";
					table += "		</td>";
					table += "	</tr>";
			
					for (var i = 0; i < data.length; i++)
					{
						var Valor = parseFloat(data[i]["Total"]);
						Total += Valor;
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+SetFormattedDate(data[i]["Fecha"]);
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Ord_Reparacion"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Tipo"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Motivo"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Diagnostico"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["DigitadorID"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "		</td>";
						table += "	</tr>";
					}
					
					table += "	<tr>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "			&nbsp;";
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			&nbsp;";
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			&nbsp;";
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			&nbsp;";
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			&nbsp;";
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			<b>TOTAL</b>";
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
					table += "			<b>$ "+Total.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"</b>";
					table += "		</td>";
					table += "	</tr>";
				}
				else if ("<?php echo $OrderBy; ?>" == "Tipo")
				{
					for (var i = 0; i < data.length; i++)
					{
						Total += parseFloat(data[i]["Total"]);
						
						if (Viejo_ID != ""+data[i]["Tipo"]+"")
						{
							var Valor = 0;
							Viejo_ID = ""+data[i]["Tipo"]+"";
							
							for (var Index = i; Index < data.length; Index++)
							{
								if (Viejo_ID != ""+data[i]["Tipo"]+"")
									break;
								
								Valor += parseFloat(data[Index]["Total"]);
							}
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"width:600px; margin-top:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:18%\">";
							table += "			Maquina/Vehiculo";
							table += "		</td>";
							table += "		<td style=\"width:54%\">";
							table += "			<input type=\"text\" style=\"width: 290px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Tipo"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:8%\">";
							table += "			Total";
							table += "		</td>";
							table += "		<td style=\"width:30%\">";
							table += "			<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Orden Rep.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:25%;\">";
							table += "			Motivo";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:25%;\">";
							table += "			Diagnostico";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
							table += "			Dig.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:18%;\">";
							table += "			Total";
							table += "		</td>";
							table += "	</tr>";
							
							Valor = parseFloat(data[i]["Total"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Ord_Reparacion"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Motivo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Diagnostico"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["DigitadorID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
						else
						{
							var Valor = parseFloat(data[i]["Total"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Ord_Reparacion"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Motivo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Diagnostico"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["DigitadorID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
					}
				}
				else
				{
					for (var i = 0; i < data.length; i++)
					{
						Total += parseFloat(data[i]["Total"]);
						
						if (Viejo_ID != ""+data[i]["Motivo"]+"")
						{
							var Valor = 0;
							Viejo_ID = ""+data[i]["Motivo"]+"";
							
							for (var Index = i; Index < data.length; Index++)
							{
								if (Viejo_ID != ""+data[i]["Motivo"]+"")
									break;
								
								Valor += parseFloat(data[Index]["Total"]);
							}
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"width:600px; margin-top:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:18%\">";
							table += "			Motivo";
							table += "		</td>";
							table += "		<td style=\"width:54%\">";
							table += "			<input type=\"text\" style=\"width: 290px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Motivo"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:8%\">";
							table += "			Total";
							table += "		</td>";
							table += "		<td style=\"width:30%\">";
							table += "			<input type=\"text\" style=\"width: 150px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Orden Rep.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
							table += "			Maquina/Vehiculo";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:30%;\">";
							table += "			Diagnostico";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
							table += "			Dig.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:18%;\">";
							table += "			Total";
							table += "		</td>";
							table += "	</tr>";
							
							Valor = parseFloat(data[i]["Total"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Ord_Reparacion"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Tipo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Diagnostico"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["DigitadorID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
						else
						{
							var Valor = parseFloat(data[i]["Total"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Ord_Reparacion"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Tipo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Diagnostico"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["DigitadorID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
					}
				}
			}
			
			table += "</table>";
			$("#mid").html(table);
			
			if ("<?php echo $OrderBy; ?>" != "")
			{
				$("#total").val("$ "+Total.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#fecha_ini").val(SetFormattedDate("<?php echo $Fecha_Ini; ?>"));
				$("#fecha_fin").val(SetFormattedDate("<?php echo $Fecha_Fin; ?>"));
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert(textStatus+ " - " +errorThrown);
		}
	});
	
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
		height: 20px;
	}
<?php
	if ($OrderBy == "")
	{
?>
	#header{
		width: 800px;
		height: 40px;
		font-family:calibri;
		background: #DEDEDE;
		font-size: 20px;
		list-style:none;
	}
	
	.table2{
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
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 800px;
		list-style:none;
	}
	
<?php
	}
	else
	{
?>
	#header{
		width: 600px;
		height: 40px;
		font-family:calibri;
		background: #DEDEDE;
		font-size: 20px;
		list-style:none;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		width: 600px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
		list-style:none;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 600px;
		list-style:none;
	}
	
<?php
	}
?>
	
	#header li{
		float: left;
		padding: 2px;
	}
	
	.table1{
		width: 400px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2 li{
		float: left;
		padding: 2px;
	}
	
	#print_button{
		width:100px;
		height:25px;
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
		#export_button {
			display: none;
		}
	}
	@page 
	{
		margin: 0.5cm 0.5cm 1.0cm 0.5cm;
	}
</style>
<?php
	if ($OrderBy == "")
	{
?>
	<div style="width:800px;">
<?php
	}
	else
	{
?>	
	<div style="width:600px;">
<?php
	}
?>
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
	<?php
		if ($OrderBy == "")
		{
	?>
		<li style="padding:7px 0px; margin-left: 150px;">
	<?php
		}
		else
		{
	?>	
		<li style="padding:7px 0px; margin-left: 52px;">
	<?php
		}
	?>
			Reporte de Maquinaria
		</li>
	<?php
		if ($OrderBy == "")
		{
	?>
		<li style="padding:2px 0px; margin-left: 150px;">
	<?php
		}
		else
		{
	?>	
		<li style="padding:2px 0px; margin-left: 52px;">
	<?php
		}
	?>
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
<?php
	if ($OrderBy != "")
	{
?>
	<div align="center" style="width: 600px; border-bottom: #A9A9A9 1px solid; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
		Total General
	</div>
	<table class="table1" cellspacing="1" cellpadding="1" align="center" style="text-align:center;">
		<tr>
			<td>
				Fecha Ini.
			</td>
			<td>
				Fecha Fin.
			</td>
			<td>
				Total
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="fecha_ini" style="width: 90px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="fecha_fin" style="width: 90px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total" style="width: 180px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 600px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
<?php
	}
?>
	<div id="mid">
		<!--<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:20px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Orden Rep.
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Maquina/Vehiculo
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					Motivo Reparacion
				</td>
				<td style="border-bottom:grey 1px solid; width:24%;">
					Diagnostico
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Dig.
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Total
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					2015-10-10
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER00005555
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					SISTEMA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
			</tr>
		</table>-->
		<!--<table class="table2" cellspacing="0" cellpadding="2" style="width:600px; margin-top:20px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:18%">
					Maquina/Vehiculo
				</td>
				<td style="width:54%">
					<input type="text" style="width: 290px; height: 20px;" class="myinput" readonly/>
				</td>
				<td style="width:8%">
					Total
				</td>
				<td style="width:30%">
					<input type="text" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:15%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Orden Rep.
				</td>
				<td style="border-bottom:grey 1px solid; width:40%;">
					Diagnostico
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Dig.
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					Total
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					2015-10-10
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER00005555
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					SISTEMA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
			</tr>
		</table>-->
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />