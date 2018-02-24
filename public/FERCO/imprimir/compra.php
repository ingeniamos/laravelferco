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
			data: {"Imprimir_Compra":"<?php echo $Interno; ?>"},
			async: true,
			success: function (data, status, xhr)
			{
				var table = "";
				table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 13px;\">";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:12%;\">";
				table += "			Codigo";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:43%;\">";
				table += "			Producto";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:10%;\">";
				table += "			Present.";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:10%;\">";
				table += "			Cant.";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:15%;\">";
				table += "			Unitario";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:20%;\">";
				table += "			SubTotal";
				table += "		</td>";
				table += "	</tr>";
				
				var len = data.length;
				for (var i = 0; i < len; i++)
				{
					if (i == 0)
					{
						$("#pedido").val(data[i]["Pedido"]);
						$("#entrada").val(data[i]["Entrada"]);
						$("#factura").val(data[i]["Factura"]);
						$("#interno").val(data[i]["Interno"]);
						$("#fecha").val(data[i]["Fecha"]);
						//
						$("#cliente").val(data[i]["Cliente"]);
						$("#cliente_ID").val(data[i]["ClienteID"]);
						$("#digitado_por").val(data[i]["Digitado_Por"]);
						$("#aprobado_por").val(data[i]["Aprobado_Por"]);
						$("#direccion").val(data[i]["Direccion"]);
						$("#telefono").val(data[i]["Telefono"]);
						$("#contactop").val(data[i]["ContactoP"]);
						$("#email").val(data[i]["Email"]);
						//
						$("#observaciones").val(data[i]["Observaciones"]);
						$("#subtotal").val("$ "+data[i]["SubTotal2"]);
						if (data[i]["TipoServicio1"] == "")
							$("#tipo_servicio1").html("Servicios");
						else
							$("#tipo_servicio1").html(data[i]["TipoServicio1"]);
						
						$("#tipo_servicio2").val("$ "+data[i]["TipoServicio2"]);
						$("#peso_bascula").val(data[i]["Peso_Bascula"]);
						$("#iva").val("$ "+data[i]["IVA"]);
						if (data[i]["TipoDescuento1"] == "")
							$("#tipo_descuento").html("Descuento");
						else
							$("#tipo_descuento").html(data[i]["TipoDescuento1"]);
						
						$("#descuento").val("$ "+data[i]["TipoDescuento2"]);
						$("#peso_remision").val(data[i]["Peso_Remision"]);
						$("#peso_calculado").val(data[i]["Peso_Calculado"]);
						$("#total").val("$ "+data[i]["Total"]);
						$("#conductor").val(data[i]["Conductor"]);
						$("#placa").val(data[i]["Placa"]);
						$("#estado").val(data[i]["Estado"]);
						$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
						$("#fecha_impreso").val(FormatedDate())
					}
					
					table += "<tr>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "		"+data[i]["Codigo"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "		"+data[i]["Nombre"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "		"+data[i]["Unidad"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "		"+data[i]["Cantidad"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+data[i]["Unitario"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+data[i]["SubTotal"];
					table += "	</td>";
					table += "</tr>";
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
	
	.header{
		width: 686px;
		height: 40px;
		font-family:calibri;
		background: #DEDEDE;
		text-align: center;
		font-size: 12px;
	}
	.header input {
		width: 120px;
		height: 20px;
	}
	
	.table1{
		width: 685px;
		font-family:calibri;
		margin-top: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 685px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	.table3{
		font-family:calibri;
		margin-top: 5px;
		width: 655px;
		text-align: left;
		font-size: 13px;
		list-style: none;
	}
	.table3 li{
		float: left;
		padding: 0px 2px;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 655px;
		font-style: italic;
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
		.header{
			-webkit-print-color-adjust: exact;
		}
		.table2{
			-webkit-print-color-adjust: exact;
		}
		.table3{
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
	<table  border="1" class="table2" cellspacing="1" cellpadding="2" style="margin-bottom: 15px; border-collapse: collapse;">
		<tr>
			<td rowspan="3" style="padding-right:10px; width: 20%;">
				<img src="../images/logo.png" alt="Logo" width="100%" height="30">
			</td>
			<td style="width: 60%;">
				<b>FERCO LTDA. FERRETERÍA Y CONSTRUCCIÓN</b>
			</td>
			<td>
				Formato: 001
			</td>
		</tr>
		<tr>
			<td style="width: 60%;">
				<b>SISTEMA DE ASEGURAMIENTO DE LA CALIDAD</b>
			</td>
			<td>
				Versión: 1.0
			</td>
		</tr>
		<tr>
			<td style="width: 60%;">
				<b>FORMATO DE PEDIDO DE MATERIALES</b>
			</td>
			<td>
				Fecha:09-06-2017
			</td>
		</tr>
	</table>
	<table class="header" cellspacing="0" cellpadding="1">
		<tr>
			<td rowspan="2" style="padding-right:10px;">
				<img src="../images/logo.png" alt="Logo" width="110" height="30">
			</td>
			<td>
				Interno
			</td>
			<td>
				Pedido
			</td>
			<td>
				Factura
			</td>
			<td>
				Fecha
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="interno" class="myinput" style="padding: 1px 0px; text-align:center;" readonly/>
			</td>
			<td>
				<input type="text" id="pedido" class="myinput" style="padding: 1px 0px; text-align:center;" readonly/>
			</td>
			<td>
				<input type="text" id="factura" class="myinput" style="padding: 1px 0px; text-align:center;" readonly/>
			</td>
			<td>
				<input type="text" id="fecha" class="myinput" style="padding: 1px 0px; text-align:center;" readonly/>
			</td>
		</tr>
	</table>
	<table class="table1" cellspacing="1" cellpadding="1">
		<tr>
			<td>
				Cliente
			</td>
			<td>
				<input type="text" id="cliente" style="width: 458px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				Telefono
			</td>
			<td>
				<input type="text" id="telefono" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				NIT
			</td>
			<td>
				<input type="text" id="cliente_ID" style="width: 458px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				Digit&oacute;
			</td>
			<td>
				<input type="text" id="digitado_por" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Direcci&oacute;n
			</td>
			<td>
				<input type="text" id="direccion" style="width: 458px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				Aprob&oacute;
			</td>
			<td>
				<input type="text" id="aprobado_por" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
		</tr>		
	</table>
	<table class="table1" style="margin-top: 0;" cellspacing="1" cellpadding="1">
		<tr>
			<td>
				Contacto&nbsp;
			</td>
			<td>
				<input type="text" id="contactop" style="width: 290px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				E-mail
			</td>
			<td>
				<input type="text" id="email" style="width: 300px; height: 20px;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 13px;">
				<td style="border-bottom:#A9A9A9 1px solid; width:12%;">
					Codigo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; width:43%;">
					Producto
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; width:10%;">
					Present.
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; width:10%;">
					Cantidad
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; width:15%;">
					Unitario
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; width:20%;">
					SubTotal
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					12321312321
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDADASDASDASD DASD ASDASDSADASDSAD ASADASDASDSADSAADADSADADASASDSADASDADASDASDASDASDS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					unidad
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					2323
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.991.232.213,00
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.991.232.213,00
				</td>
			</tr>
		</table>
	</div>
	<table class="table3" cellspacing="0" cellpadding="1">
		<tr>
			<td style="padding-right:10px;">
				Observaciones
			</td>
			<td colspan="4" rowspan="2">
				<textarea readonly rows="3" cols="30" id="observaciones" maxlength="200" style="resize:none; width:347px;"></textarea>
			</td>
			<td>
				SubTotal
			</td>
			<td>
				<input type="text" id="subtotal" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<div id="tipo_servicio1">Servicios</div>
			</td>
			<td>
				<input type="text" id="tipo_servicio2" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Peso B&aacute;scula
			</td>
			<td>
				<input type="text" id="peso_bascula" style="width:125px;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				&nbsp;&nbsp; Peso Remisi&oacute;n
			</td>
			<td>
				<input type="text" id="peso_remision" style="width:115px;" class="myinput" readonly/>
			</td>
			<td>
				IVA
			</td>
			<td>
				<input type="text" id="iva" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Peso Calculado
			</td>
			<td>
				<input type="text" id="peso_calculado" style="width:125px;" class="myinput" readonly/>
			</td>
			<td colspan="3">
				&nbsp;
			</td>
			<td>
				<div id="tipo_descuento" style="width:120px;">Descuento</div>
			</td>
			<td>
				<input type="text" id="descuento" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Conductor
			</td>
			<td colspan="3">
				<input type="text" id="conductor" style="width:230px;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="placa" style="width:115px;" class="myinput" readonly/>
			</td>
			<td>
				TOTAL
			</td>
			<td>
				<input type="text" id="total" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<input type="text" id="estado" style="width:125px;" class="myinput" readonly/>
			</td>
			<td>
				Impr.
			</td>
			<td>
				<input type="text" id="impreso_por" style="width:70px;" class="myinput" readonly/>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Impreso
			</td>
			<td>
				<input type="text" id="fecha_impreso" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>