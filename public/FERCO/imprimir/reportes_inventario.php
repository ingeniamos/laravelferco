<?php
	session_start();
	//---
	$Existencia = isset($_GET['Existencia']) ? $_GET['Existencia']:"";
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
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
		dataType: 'json',
		url: "../modulos/datos_productos.php",
		data: {
			"Reportes_Inventario":true,
			"Codigo":"<?php echo $Codigo; ?>",
			"Categoria":"<?php echo $Categoria; ?>",
			"Grupo":"<?php echo $Grupo; ?>",
			"SubGrupo":"<?php echo $SubGrupo; ?>",
		},
		async: true,
		success: function (data, status, xhr)
		{
			var len = data.length;
			var table = "";
			table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:20px;\">";
			table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
			table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
			table += "			Producto";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
			table += "			Categoria";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
			table += "			Grupo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
			table += "			SubGrupo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
			table += "			Peso";
			table += "		</td>";
			<?php if ($Existencia == true) { ?>
			table += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
			table += "			Existencias";
			table += "		</td>";
			<?php } ?>
			table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
			table += "			Ult. Costo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
			table += "			Total Ult. Costo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
			table += "			Costo Prom";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
			table += "			Total Costo Prom";
			table += "		</td>";
			table += "	</tr>";
			
			var Total_Ult_Costo = 0;
			var Total_Costo_Prom = 0;
			var Ult_Costo = 0;
			var Costo_Prom = 0;
			
			for (var i = 0; i < len; i++)
			{
				if (data[i]["Nombre"] != undefined && data[i]["Nombre"] != "")
				{
					Ult_Costo += parseFloat(data[i]["Ult_Costo"]);
					Total_Ult_Costo = parseFloat(data[i]["Total_Ult_Costo"]);
					Costo_Prom += parseFloat(data[i]["Costo_Prom"]);
					Total_Costo_Prom = parseFloat(data[i]["Total_Costo_Prom"]);
					
					table += "	<tr>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["Nombre"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["Categoria"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["Grupo"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "			"+data[i]["SubGrupo"];
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "			"+data[i]["Peso"];
					table += "		</td>";
					<?php if ($Existencia == true) { ?>
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "			"+data[i]["Existencia"];
					table += "		</td>";
					<?php } ?>
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+Ult_Costo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+Total_Ult_Costo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+Costo_Prom.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table += "		</td>";
					table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
					table += "			$ "+Total_Costo_Prom.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
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
	
	$("#print_button").bind('click', function ()
	{
		$("#fecha_impreso").val(FormatedDate());
		window.print();
	});
	
	$("#export_button").bind('click', function ()
	{
		var data = "";
		data += "../modulos/export_xls.php?Reportes_Inventario=true&Existencia=<?php echo $Existencia; ?>&Codigo=<?php echo $Codigo; ?>";
		data += "&Categoria=<?php echo $Categoria; ?>&Grupo=<?php echo $Grupo; ?>&SubGrupo=<?php echo $SubGrupo; ?>";
		window.location = data;
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
		text-align: left;
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
		list-style:none;
	}
	#bottom ul{
		width:220px;
		height:25px;
		padding:0px;
	}
	#bottom li{
		float:left;
		margin:0px 5px;
	}
	
	#print_button{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #FAA732;
		border-color: #FAA732;
	}
	
	#export_button{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #5BB75B;
		border-color: #5BB75B;
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
<div>
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 240px;">
			Reportes Inventario
		</li>
		<li style="padding:2px 0px; margin-left: 190px;">
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
	<div id="mid">
		<!--<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:20px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:14%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Categoria
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Grupo
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					SubGrupo
				</td>
				<td style="border-bottom:grey 1px solid; width:9%;">
					Peso
				</td>
				<td style="border-bottom:grey 1px solid; width:9%;">
					Existencias
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Ult. Costo
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Total Ult. Costo
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Costo Prom
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Total Costo Prom
				</td>
			</tr>
			<tr>
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
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					9.999.999.99
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
		</table>-->
	</div>
	<div id="bottom" align="center">
		<ul>
			<li>
				<input type="button" id="print_button" value="Imprimir"/>
			</li>
			<li>
				<input type="button" id="export_button" value="Exportar"/>
			</li>
		</ul>
	</div>
</div>
<br /><br />