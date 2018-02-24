<?php
	session_start();
	require_once('../modulos/config.php');
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	var ClienteNombre = [];
	var ClienteDireccion = [];
	var ClienteTelefono = [];
	
	$.ajax(
	{
		dataType: 'json',
		url: "../modulos/datos.php",
		async: false,
		success: function (data)
		{
			for (var i = 0; i < data.length; i++)
			{
				ClienteNombre[""+data[i]["ClienteID"]+""] = data[i]["Nombre"];
				ClienteDireccion[""+data[i]["ClienteID"]+""] = data[i]["Direccion"];
				ClienteTelefono[""+data[i]["ClienteID"]+""] = data[i]["Telefono"];
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(textStatus+ " - " +errorThrown);
		}
	});
	
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
	
	$("#interno").val("<?php echo $Interno; ?>");
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	if ("<?php echo $Interno; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos_productos.php",
			data: {"Presupuesto_Imprimir_Detalles":"<?php echo $Interno; ?>"},
			success: function (data, status, xhr)
			{
				var table = "";
				table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 13px;\">";
				table += "		<td rowspan=\"2\" style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid; width:5%;\">";
				table += "			Item";
				table += "		</td>";
				table += "		<td rowspan=\"2\" style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid; width:18%;\">";
				table += "			Nombre";
				table += "		</td>";
				table += "		<td rowspan=\"2\" style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid; width:3%;\">";
				table += "			Unidad";
				table += "		</td>";
				table += "		<td rowspan=\"2\" style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid; width:5%;\">";
				table += "			Cantidad";
				table += "		</td>";
				table += "		<td rowspan=\"2\" style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid; width:8%;\">";
				table += "			Vr. Unitario";
				table += "		</td>";
				table += "		<td rowspan=\"2\" style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid; width:10%;\">";
				table += "			Vr. Parcial";
				table += "		</td>";
				table += "		<td colspan=\"2\" style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid; width:17%;\">";
				table += "			Materiales";
				table += "		</td>";
				table += "		<td colspan=\"2\" style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid; width:17%;\">";
				table += "			Equipos";
				table += "		</td>";
				table += "		<td colspan=\"2\" style=\"border-bottom:#A9A9A9 1px solid; width:17%;\">";
				table += "			Mano de Obra";
				table += "		</td>";
				table += "	</tr>";
				table += "	<tr style=\"background: #DEDEDE; font-size: 13px;\">";
				table += "		<td style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid;\">";
				table += "			Vr. Unitario";
				table += "		</td>";
				table += "		<td style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid;\">";
				table += "			Vr. Parcial";
				table += "		</td>";
				table += "		<td style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid;\">";
				table += "			Vr. Unitario";
				table += "		</td>";
				table += "		<td style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid;\">";
				table += "			Vr. Parcial";
				table += "		</td>";
				table += "		<td style=\"border-right:#A9A9A9 1px solid; border-bottom:#A9A9A9 1px solid;\">";
				table += "			Vr. Unitario";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid;\">";
				table += "			Vr. Parcial";
				table += "		</td>";
				table += "	</tr>";
				
				var SubTotal = parseFloat(data[0]["SubTotal"]);
				var Administracion1 = parseFloat(data[0]["Administracion1"]);
				var Administracion2 = parseFloat(data[0]["Administracion2"]);
				var Imprevistos1 = parseFloat(data[0]["Imprevistos1"]);
				var Imprevistos2 = parseFloat(data[0]["Imprevistos2"]);
				var Utilidades1 = parseFloat(data[0]["Utilidades1"]);
				var Utilidades2 = parseFloat(data[0]["Utilidades2"]);
				
				if (data[0]["Iva1_Check"] == "true")
				{
					var Iva_Nombre = "IVA Utilidad";
					var Iva = parseFloat(data[0]["Iva1"]);
				}
				else if (data[0]["Iva2_Check"] == "true")
				{
					var Iva_Nombre = "IVA Sobre Total";
					var Iva = parseFloat(data[0]["Iva2"]);
				}
				else
				{
					var Iva_Nombre = "IVA";
					var Iva = 0;
				}
				
				var Total = parseFloat(data[0]["Total"]);
				
				$("#cliente").val(ClienteNombre[data[0]["ClienteID"]]);
				$("#cliente_ID").val(data[0]["ClienteID"]);
				$("#direccion").val(ClienteDireccion[data[0]["ClienteID"]]);
				$("#telefono").val(ClienteTelefono[data[0]["ClienteID"]]);
				$("#observaciones").val(data[0]["Notas"]);
				$("#subtotal").val("$ "+SubTotal.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#administracion1").val(Administracion1.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"%");
				$("#administracion2").val("$ "+Administracion2.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#imprevistos1").val(Imprevistos1.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"%");
				$("#imprevistos2").val("$ "+Imprevistos2.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#utilidades1").val(Utilidades1.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"%");
				$("#utilidades2").val("$ "+Utilidades2.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#iva_nombre").html(Iva_Nombre);
				$("#iva").val("$ "+Iva.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#total").val("$ "+Total.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				
				TotalSubtotal = 0;
				TotalMateriales = 0;
				TotalTotalMateriales = 0;
				TotalEquipos = 0;
				TotalTotalEquipos = 0;
				TotalManodeObras = 0;
				TotalTotalManodeObras = 0;
				
				var len = data[0]["Items"].length;
				for (var i = 0; i < len; i++)
				{
					var Codigo = data[0]["Items"][i]["Codigo"];
					var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
					var Valor = parseFloat(data[0]["Items"][i]["Valor"]);
					var SubTotal = Valor * Cantidad;
					var Materiales = parseFloat(data[0]["Items"][i]["Materiales"]);
					var Equipos = parseFloat(data[0]["Items"][i]["Equipos"]);
					var ManodeObra = parseFloat(data[0]["Items"][i]["ManodeObra"]);
					var SubTotalMateriales = Materiales * Cantidad;
					var SubTotalEquipos = Equipos * Cantidad;
					var SubTotalManodeObra = ManodeObra * Cantidad;
					
					TotalSubtotal += SubTotal;
					TotalMateriales += Materiales;
					TotalTotalMateriales += SubTotalMateriales;
					TotalEquipos += Equipos;
					TotalTotalEquipos += SubTotalEquipos;
					TotalManodeObras += ManodeObra;
					TotalTotalManodeObras += SubTotalManodeObra;
					
					table += "<tr>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "		"+data[0]["Items"][i]["Item"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table += "		"+data[0]["Items"][i]["Nombre"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "		"+data[0]["Items"][i]["Unidad"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		$ "+SubTotal.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		$ "+Materiales.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		$ "+SubTotalMateriales.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		$ "+Equipos.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		$ "+SubTotalEquipos.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		$ "+ManodeObra.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
					if (Codigo.indexOf("Titulo") != 0)
						table += "		$ "+SubTotalManodeObra.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					else
						table += "		&nbsp;";
					table += "	</td>";
					table += "</tr>";
				}
				table += "	<tr style=\"background: #DEDEDE; font-size: 13px;\">";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid;\">";
				table += "			&nbsp;";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid;\">";
				table += "			&nbsp;";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid;\">";
				table += "			&nbsp;";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid;\">";
				table += "			&nbsp;";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
				table += "			&nbsp;";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "			$ "+TotalSubtotal.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "			$ "+TotalMateriales.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "			$ "+TotalTotalMateriales.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "			$ "+TotalEquipos.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "			$ "+TotalTotalEquipos.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "			$ "+TotalManodeObras.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
				table += "			$ "+TotalTotalManodeObras.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
				table += "		</td>";
				table += "	</tr>";
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
	
	#header{
		width: 1300px;
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
		width: 700px;
		font-family:calibri;
		margin-top: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		font-family:calibri;
		margin-top: 10px;
		width: 1300px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	.table3{
		font-family:calibri;
		margin-top: 10px;
		width: 1300px;
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
		width: 1300px;
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
		size: landscape;
		margin: 0.5cm 0.5cm 0.5cm 0.5cm;
	}
</style>
<div>
	<div id="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 420px;">
			Detalles de Presupuesto
		</li>
		<li style="padding:2px 0px; margin-left: 280px;">
			<table  cellspacing="0" cellpadding="0" style="font-size:12px; text-align:center;">
				<tr>
					<td>
						Presupuesto No.
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
					<td>
						<input type="text" id="fecha_impreso" class="myinput" style="width: 120px; text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="impreso_por" class="myinput" style="width: 60px; text-align:center;" readonly/>
					</td>
				</tr>
			</table>
		</li>
	</div>
	<table class="table1" cellspacing="1" cellpadding="2">
		<tr>
			<td>
				Cliente
			</td>
			<td>
				<input type="text" id="cliente" style="width: 458px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				NIT
			</td>
			<td>
				<input type="text" id="cliente_ID" style="width: 95px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				Direcci&oacute;n
			</td>
			<td>
				<input type="text" id="direccion" style="width: 458px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				Tel&eacute;fono
			</td>
			<td>
				<input type="text" id="telefono" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<!--<table class="table2" cellspacing="0" cellpadding="3">
			<tr style="background: #DEDEDE; font-size: 14px;">
				<td style="border-bottom:grey 1px solid; width:8%;">
					Item
				</td>
				<td style="border-bottom:grey 1px solid; width:47%;">
					Nombre
				</td>
				<td style="border-bottom:grey 1px solid; width:5%;">
					Unidad
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Cantidad
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Valor
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					SubTotal
				</td>
			</tr>
			<tr>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					10.5.3
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid; overflow:hidden; white-space: nowrap;">
					Producto prueba con un nombre muy, muy, muy largo 1
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					UND
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					999.99
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					$ 99999999.99
				</td>
				<td style="border-bottom:grey 1px solid;">
					$ 999999999999.99
				</td>
			</tr>
		</table>-->
	</div>
	<table class="table3" cellspacing="0" cellpadding="1">
		<tr>
			<td>
				Observaciones
			</td>
			<td style="width: 955px;">
				&nbsp;
			</td>
			<td>
				SubTotal
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				<input type="text" id="subtotal" style="width:130px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td colspan="2" rowspan="4">
				<textarea readonly rows="5" cols="142" id="observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td>
				Administracion
			</td>
			<td>
				<input type="text" id="administracion1" style="width:50px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="administracion2" style="width:130px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Imprevistos
			</td>
			<td>
				<input type="text" id="imprevistos1" style="width:50px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="imprevistos2" style="width:130px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Utilidades
			</td>
			<td>
				<input type="text" id="utilidades1" style="width:50px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="utilidades2" style="width:130px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				<div id="iva_nombre">IVA</div>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				<input type="text" id="iva" style="width:130px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<li style="padding-top:3px;">
					Digitó
				</li>
				<li style="margin-left:5px;">
					<input type="text" id="digitado_por" style="width:80px;" class="myinput" readonly/>
				</li>
				<li style="margin-left:5px; padding-top:3px;">
					Modificó
				</li>
				<li style="margin-left:5px;">
					<input type="text" id="modificado_por" style="width:80px;" class="myinput" readonly/>
				</li>
			</td>
			<td>
				TOTAL
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				<input type="text" id="total" style="width:130px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<!--<p style="text-align:center; font-size:12px;">
		"Autorizo a <?php echo $COMPANY_NAME; ?> para que recolecte, almacene, use y administre mis datos personales,
		los cuales han sido suministrados<br />con la finalidad de desarrollar sus operaciones comerciales,
		de conformidad con lo establecido en la Politica de <br />Tratamiento de Datos Personales,
		Ley 1581/2012 y el decreto 1377/2013 disponible en nuestra p&aacute;gina web<br/><?php echo $COMPANY_WEB; ?>"
		<br /><br />
		Firma: __________________________________ c.c ____________________
		</p>-->
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>