<?php
	session_start();
	//---
	$Simple = isset($_GET['Simple']) ? $_GET['Simple']:"";
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$VendedorID = isset($_GET['VendedorID']) ? $_GET['VendedorID']:"";
	$F_Pago = isset($_GET['F_Pago']) ? $_GET['F_Pago']:"";
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	$Factura = isset($_GET['Factura']) ? $_GET['Factura']:"";
	$Ord_Compra = isset($_GET['Ord_Compra']) ? $_GET['Ord_Compra']:"";
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
			"Reportes_Ventas_Mov_Productos":true,
			"Codigo":"<?php echo $Codigo; ?>",
			"Categoria":"<?php echo $Categoria; ?>",
			"Grupo":"<?php echo $Grupo; ?>",
			"SubGrupo":"<?php echo $SubGrupo; ?>",
			"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
			"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			"ClienteID":"<?php echo $ClienteID; ?>",
			"VendedorID":"<?php echo $VendedorID; ?>",
			"F_Pago":"<?php echo $F_Pago; ?>",
			"Estado":"<?php echo $Estado; ?>",
			"Factura":"<?php echo $Factura; ?>",
			"Ord_Compra":"<?php echo $Ord_Compra; ?>",
		},
		async: true,
		success: function (data, status, xhr)
		{
			var len = data.length;
			var table = "";
			var myarray = new Array();
			var Viejo_ID = "_empty_";
			var Index = 0;
			var Remisiones = 0;
			var Cantidad = 0;
			var TotalCosto = 0;
			var TotalCostoU = 0;
			var TotalCantidad = 0;
			var Costo = 0;
			var CostoU = 0;
			var Utilidad = 0;
			
			if ("<?php echo $Simple; ?>" != "")
			{
				table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; width:35%;\">";
				table += "			Producto";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table += "			Mov.";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Remisiones";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table += "			Vr Facturado";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table += "			Costo U.";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			% Util.";
				table += "		</td>";
				table += "	</tr>";
				
				for (var i = 0; i < len; i++)
				{
					if (data[i]["Producto"] != undefined)
					{
						if (Viejo_ID != data[i]["Producto"])
						{
							Cantidad = 0;
							Remisiones = 0;
							Costo = 0;
							CostoU = 0;
							Utilidad = 0;
							Viejo_ID = data[i]["Producto"];
							
							for (Index = i; Index < len; Index++)
							{
								if (Viejo_ID != data[Index]["Producto"])
									break;
								
								Cantidad += parseFloat(data[Index]["Cantidad"]);
								Costo += parseFloat(data[Index]["Costo"]);
								CostoU += parseFloat(data[Index]["CostoU"]);
								Remisiones++;
							}
							
							TotalCosto += Costo;
							TotalCostoU += CostoU;
							TotalCantidad += Cantidad;
							
							Utilidad = parseFloat(((Costo / CostoU) - 1) * 100);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Producto"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+Remisiones;
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Costo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+CostoU.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+Utilidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" %";
							table += "		</td>";
							table += "	</tr>";
							
						}
						else
							continue;
					}
				}
			}
			else 
			{
				for (var i = 0; i < len; i++)
				{
					if (data[i]["Producto"] != undefined)
					{
						if (Viejo_ID != data[i]["Producto"])
						{
							Cantidad = 0;
							Costo = 0;
							CostoU = 0;
							Utilidad = 0;
							Viejo_ID = data[i]["Producto"];
							
							for (Index = i; Index < len; Index++)
							{
								if (Viejo_ID != data[Index]["Producto"])
									break;
								
								Cantidad += parseFloat(data[Index]["Cantidad"]);
								Costo += parseFloat(data[Index]["Costo"]);
								CostoU += parseFloat(data[Index]["CostoU"]);
							}
							
							TotalCosto += Costo;
							TotalCostoU += CostoU;
							TotalCantidad += Cantidad;
							
							Utilidad = parseFloat(((Costo / CostoU) - 1) * 100);
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:44%;\">";
							table += "			Producto";
							table += "		</td>";
							table += "		<td style=\"width:15%;\">";
							table += "			Mov";
							table += "		</td>";
							table += "		<td style=\"width:17%;\">";
							table += "			Vr Facturado";
							table += "		</td>";
							table += "		<td style=\"width:16%;\">";
							table += "			Costo U.";
							table += "		</td>";
							table += "		<td style=\"width:8%;\">";
							table += "			% Util.";
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr style=\"background: #DEDEDE;\">";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 345px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Producto"]+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 110px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 130px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Costo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 130px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+CostoU.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" style=\"width: 50px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Utilidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"%\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-bottom:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Fecha";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Mov";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			O. Compra";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Factura";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:18%;\">";
							table += "			Tercero";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
							table += "			Vend";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
							table += "			Vr Facturado";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:14%;\">";
							table += "			Costo U.";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
							table += "			% Util.";
							table += "		</td>";
							table += "	</tr>";
							
							Cantidad = parseFloat(data[i]["Cantidad"]);
							Costo = parseFloat(data[i]["Costo"]);
							CostoU = parseFloat(data[i]["CostoU"]);
							Utilidad = parseFloat(((Costo / CostoU) - 1) * 100);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Ord_Compra"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["VendedorID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Costo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+CostoU.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+Utilidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"%";
							table += "		</td>";
							table += "	</tr>";
						}
						else
						{
							Cantidad = parseFloat(data[i]["Cantidad"]);
							Costo = parseFloat(data[i]["Costo"]);
							CostoU = parseFloat(data[i]["CostoU"]);
							Utilidad = parseFloat(((Costo / CostoU) - 1) * 100);
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Ord_Compra"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Factura"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["VendedorID"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+Costo.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+CostoU.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+Utilidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"%";
							table += "		</td>";
							table += "	</tr>";
						}
					}
				}
			}
			
			table += "</table>";
			
			var TotalUtilidad = parseFloat(((TotalCosto / TotalCostoU) - 1) * 100);
			
			$("#total_valor").val("$ "+TotalCosto.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#total_costo").val("$ "+TotalCostoU.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
			$("#total_utilidad").val(TotalUtilidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" %");
			$("#total_movimientos").val(TotalCantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
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
		width: 800px;
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
				Total Fact.
			</td>
			<td>
				Total CostoU.
			</td>
			<td>
				Total % Uti.
			</td>
			<td>
				Total Mov.
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
				<input type="text" id="total_valor" style="width: 160px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_costo" style="width: 160px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_utilidad" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_movimientos" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 800px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<div id="mid">
		<!--
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:20px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:40%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Mov.
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Remisiones
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Vr Facturado
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Costo U.
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					% Util.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					9.999.999
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					9.999.999
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					100.00%
				</td>
			</tr>
		</table>
		-->
		<!--
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:48%;">
					Producto
				</td>
				<td style="width:11%;">
					Mov
				</td>
				<td style="width:17%;">
					Vr Facturado
				</td>
				<td style="width:16%;">
					Costo U.
				</td>
				<td style="width:8%;">
					% Util.
				</td>
			</tr>
			<tr style="background: #DEDEDE;">
				<td>
					<input type="text" style="width: 380px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 80px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" style="width: 50px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Mov
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					O. Compra
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Factura
				</td>
				<td style="border-bottom:grey 1px solid; width:18%;">
					Tercero
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					Vend
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Vr Facturado
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Costo U.
				</td>
				<td style="border-bottom:grey 1px solid; width:7%;">
					% Util.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					SISTEMA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					100.00 %
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