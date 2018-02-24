<?php
	session_start();
	//---
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Interno = isset($_GET['Interno']) ? $_GET['Interno']:"";
	$Entrada = isset($_GET['Entrada']) ? $_GET['Entrada']:"";
	$Factura = isset($_GET['Factura']) ? $_GET['Factura']:"";
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
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
			"Reportes_Compras_Mov":true,
			"OrderBy":"<?php echo $OrderBy; ?>",
			"Codigo":"<?php echo $Codigo; ?>",
			"Categoria":"<?php echo $Categoria; ?>",
			"Grupo":"<?php echo $Grupo; ?>",
			"SubGrupo":"<?php echo $SubGrupo; ?>",
			"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
			"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			"ClienteID":"<?php echo $ClienteID; ?>",
			"Interno":"<?php echo $Interno; ?>",
			"Entrada":"<?php echo $Entrada; ?>",
			"Factura":"<?php echo $Factura; ?>",
			"Estado":"<?php echo $Estado; ?>",
		},
		async: true,
		success: function (data, status, xhr)
		{
			var len = data.length;
			var table = "";
			var myarray = new Array();
			var Viejo_ID = "_empty_";
			var Index = 0;
			var Cantidad = 0;
			var TotalMov = 0;
			var Unitario = 0;
			var Valor = 0;
			var TotalValor = 0;
			var UltCosto = 0;
			var CostoProm = 0;
			var TotalCostoProm = 0;
			
			if ("<?php echo $OrderBy; ?>" == "Producto")
			{
				for (var i = 0; i < len; i++)
				{
					if (data[i]["Codigo"] != undefined)
					{
						if (Viejo_ID != data[i]["Codigo"])
						{
							Cantidad = 0;
							Valor = 0;
							
							Viejo_ID = data[i]["Codigo"];
							
							for (Index = i; Index < len; Index++)
							{
								if (Viejo_ID != data[Index]["Codigo"])
									break;
								
								Cantidad = Cantidad + parseFloat(data[i]["Cantidad"]);
								Valor = Valor + parseFloat(data[Index]["Valor"]);
							}
							
							TotalMov = TotalMov + Cantidad;
							TotalValor = TotalValor + Valor;
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:15%;\">";
							table += "			Codigo";
							table += "		</td>";
							table += "		<td style=\"width:45%;\">";
							table += "			Producto";
							table += "		</td>";
							table += "		<td style=\"width:15%;\">";
							table += "			Mov.";
							table += "		</td>";
							table += "		<td style=\"width:27%;\">";
							table += "			Total";
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr style=\"background: #DEDEDE;\">";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 115px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Codigo"]+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 350px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Producto"]+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 120px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 200px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-bottom:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Entrada";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Factura";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:30%;\">";
							table += "			Tercero";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Mov.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
							table += "			Unitario";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Total";
							table += "		</td>";
							table += "	</tr>";
							
							Cantidad = parseFloat(data[i]["Cantidad"]);
							Unitario = parseFloat(data[i]["Unitario"]);
							Valor = parseFloat(data[i]["Valor"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Entrada"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Unitario.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
						else
						{
							Cantidad = parseFloat(data[i]["Cantidad"]);
							Unitario = parseFloat(data[i]["Unitario"]);
							Valor = parseFloat(data[i]["Valor"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Entrada"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Unitario.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
					}
				}
			}
			else
			{
				for (var i = 0; i < len; i++)
				{
					if (data[i]["ClienteID"] != undefined)
					{
						if (Viejo_ID != data[i]["ClienteID"])
						{
							Cantidad = 0;
							Valor = 0;
							
							Viejo_ID = data[i]["ClienteID"];
							
							for (Index = i; Index < len; Index++)
							{
								if (Viejo_ID != data[Index]["ClienteID"])
									break;
								
								Cantidad = Cantidad + parseFloat(data[i]["Cantidad"]);
								Valor = Valor + parseFloat(data[Index]["Valor"]);
							}
							
							TotalMov = TotalMov + Cantidad;
							TotalValor = TotalValor + Valor;
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:15%;\">";
							table += "			Tercero ID";
							table += "		</td>";
							table += "		<td style=\"width:45%;\">";
							table += "			Tercero";
							table += "		</td>";
							table += "		<td style=\"width:15%;\">";
							table += "			Mov.";
							table += "		</td>";
							table += "		<td style=\"width:27%;\">";
							table += "			Total";
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr style=\"background: #DEDEDE;\">";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 115px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["ClienteID"]+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 350px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Nombre"]+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 120px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 200px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-bottom:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Entrada";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
							table += "			Factura";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:30%;\">";
							table += "			Producto";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Mov.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
							table += "			Unitario";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Total";
							table += "		</td>";
							table += "	</tr>";
							
							Cantidad = parseFloat(data[i]["Cantidad"]);
							Unitario = parseFloat(data[i]["Unitario"]);
							Valor = parseFloat(data[i]["Valor"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Entrada"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Producto"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Unitario.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
						else
						{
							Cantidad = parseFloat(data[i]["Cantidad"]);
							Unitario = parseFloat(data[i]["Unitario"]);
							Valor = parseFloat(data[i]["Valor"]);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Entrada"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Producto"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Unitario.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
					}
				}
			}
				
			table += "</table>";
			
			$("#total_valor").val("$ "+TotalValor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#total_mov").val("$ "+TotalMov.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			if ("<?php echo $Codigo; ?>" != "")
			{
				UltCosto = parseFloat(data[0]["Ult_Costo"]);
				CostoProm = parseFloat(data[0]["Costo_Prom"]);
				TotalCostoProm = TotalValor / TotalMov;
				
				$("#ult_costo").val("$ "+UltCosto.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#costo_prom").val("$ "+CostoProm.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#total_costo_prom").val("$ "+TotalCostoProm.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			}
			else
			{
				$("#ult_costo").val("$ 0.00");
				$("#costo_prom").val("$ 0.00");
				$("#total_costo_prom").val("$ 0.00");
			}
			$("#fecha_ini").val("<?php echo $Fecha_Ini; ?>");
			$("#fecha_fin").val("<?php echo $Fecha_Fin; ?>");
			
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
		width: 800px;
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
		width:800px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 12px;
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
	
	.table2 li{
		float: left;
		padding: 2px;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 800px;
		list-style:none;
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
		<li style="padding:7px 0px; margin-left: 150px;">
			Movimientos de Productos
		</li>
		<li style="padding:2px 0px; margin-left: 120px;">
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
	<div align="center" style="width: 800px; border-bottom: #A9A9A9 1px solid; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
		Total General
	</div>
	<table class="table1" cellspacing="1" cellpadding="1" style="text-align:center;">
		<tr>
			<td>
				Fecha Inicio
			</td>
			<td>
				Fecha Fin
			</td>
			<td>
				Total Valor.
			</td>
			<td>
				Total Mov.
			</td>
			<td>
				Ult. Costo
			</td>
			<td>
				Costo Prom. Invent.
			</td>
			<td>
				Total Costo Prom.
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="fecha_ini" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="fecha_fin" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_valor" style="width: 180px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_mov" style="width: 140px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="ult_costo" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="costo_prom" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_costo_prom" style="width: 100; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 800px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<div id="mid">
		<!--
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:15%;">
					Codigo
				</td>
				<td style="width:45%;">
					Producto
				</td>
				<td style="width:15%;">
					Mov.
				</td>
				<td style="width:27%;">
					Total
				</td>
			</tr>
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td>
					<input type="text" style="width: 115px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 350px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 120px; height: 20px; text-align:center;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-bottom:20px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Entrada
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Factura
				</td>
				<td style="border-bottom:grey 1px solid; width:30%;">
					Tercero
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Mov.
				</td>
				<td style="border-bottom:grey 1px solid; width:13%;">
					Unitario
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
					FER00005555
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					9.999.999
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
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