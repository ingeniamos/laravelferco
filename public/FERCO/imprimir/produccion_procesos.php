<?php
	session_start();
	$Ord_Produccion = isset($_GET['Ord_Produccion']) ? $_GET['Ord_Produccion']:"";
	$Proceso = isset($_GET['Proceso']) ? $_GET['Proceso']:"";
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
	
	var ProductoNombre = [];
	var ProductoPeso = [];
	var ProductoUndMed = [];
	
	$.ajax(
	{
		dataType: 'json',
		url: "../modulos/datos_productos.php",
		async: false,
		success: function (data)
		{
			for (var i = 0; i < data.length; i++)
			{
				ProductoNombre[""+data[i]["CodFab"]+""] = data[i]["Nombre"];
				ProductoPeso[""+data[i]["CodFab"]+""] = data[i]["Peso"];
				ProductoUndMed[""+data[i]["CodFab"]+""] = data[i]["UndMed"];
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(textStatus+ " - " +errorThrown);
		}
	});
	
	function SetFormattedDate (MyDate)
	{
		if (MyDate == "0000-00-00")
			return MyDate;
		
		var MonthArray = new Array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic")
		MyDate = MyDate + ""; //to string
		
		var Tmp = MyDate.split(/[-]/);
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
	
	if ("<?php echo $Ord_Produccion; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data:{
				"Imprimir_Produccion_Procesos":true,
				"Ord_Produccion":"<?php echo $Ord_Produccion; ?>",
				"Proceso":"<?php echo $Proceso; ?>",
			},
			async: true,
			success: function (data, status, xhr)
			{
				if (data[0]["Destino"] == undefined)
					return; 
				
				var table1 = "";
				table1 += "<div>";
				table1 += "	<p style=\"font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;\">";
				table1 += "		Material Requerido";
				table1 += "	</p>";
				table1 += "</div>";
				table1 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table1 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table1 += "			Codigo";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:40%;\">";
				table1 += "			Producto";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
				table1 += "			Und";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
				table1 += "			Cant";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
				table1 += "			PesoT";
				table1 += "		</td>";
				table1 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table1 += "			Origen";
				table1 += "		</td>";
				table1 += "	</tr>";
				
				$("#destino").val(data[0]["Destino"]);
				$("#fecha_ini").val(SetFormattedDate(data[0]["Fecha_Ini"]));
				$("#fecha_fin").val(SetFormattedDate(data[0]["Fecha_Fin"]));
				$("#cliente").val(ClienteData[data[0]["ClienteID"]]);
				
				var PesoRequerido = 0;
				
				var len = data[0]["MaterialRequerido"].length;
				if (len <= 0)
					table1 += "</table>";
				for (var i = 0; i < len; i++)
				{
					var Peso = data[0]["MaterialRequerido"][i]["Cantidad"] * ProductoPeso[data[0]["MaterialRequerido"][i]["CodFab"]];
					PesoRequerido += Peso;
					
					table1 += "	<tr>";
					table1 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table1 += "			"+data[0]["MaterialRequerido"][i]["CodFab"];
					table1 += "		</td>";
					table1 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table1 += "			"+ProductoNombre[data[0]["MaterialRequerido"][i]["CodFab"]];
					table1 += "		</td>";
					table1 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table1 += "			"+ProductoUndMed[data[0]["MaterialRequerido"][i]["CodFab"]];
					table1 += "		</td>";
					table1 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table1 += "			"+data[0]["MaterialRequerido"][i]["Cantidad"];
					table1 += "		</td>";
					table1 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table1 += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table1 += "		</td>";
					table1 += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
					table1 += "			"+data[0]["MaterialRequerido"][i]["Origen"];
					table1 += "		</td>";
					table1 += "	</tr>";
					if (len - i == 1)
					{
						table1 += "</table>";
						$("#kg_requerido").val(PesoRequerido);
					}
				}
				
				var table2 = "";
				table2 += "<div>";
				table2 += "	<p style=\"font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;\">";
				table2 += "		Productos a Obtener";
				table2 += "	</p>";
				table2 += "</div>";
				table2 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table2 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table2 += "			Codigo";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:40%;\">";
				table2 += "			Producto";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:9%;\">";
				table2 += "			Und";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
				table2 += "			Cant";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
				table2 += "			PesoT";
				table2 += "		</td>";
				table2 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table2 += "			Enviar a";
				table2 += "		</td>";
				table2 += "	</tr>";
				
				var PesoObtener = 0;
				
				var len = data[0]["ProductosObtener"].length;
				if (len <= 0)
					table2 += "</table>";
				for (var i = 0; i < len; i++)
				{
					var Peso = data[0]["ProductosObtener"][i]["Cantidad"] * ProductoPeso[data[0]["ProductosObtener"][i]["CodFab"]];
					PesoObtener += Peso;
					
					table2 += "	<tr>";
					table2 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table2 += "			"+data[0]["ProductosObtener"][i]["CodFab"];
					table2 += "		</td>";
					table2 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table2 += "			"+ProductoNombre[data[0]["ProductosObtener"][i]["CodFab"]];
					table2 += "		</td>";
					table2 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table2 += "			"+ProductoUndMed[data[0]["ProductosObtener"][i]["CodFab"]];
					table2 += "		</td>";
					table2 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table2 += "			"+data[0]["ProductosObtener"][i]["Cantidad"];
					table2 += "		</td>";
					table2 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table2 += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table2 += "		</td>";
					table2 += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
					table2 += "			"+data[0]["ProductosObtener"][i]["Destino"];
					table2 += "		</td>";
					table2 += "	</tr>";
					if (len - i == 1)
					{
						table2 += "</table>";
						$("#kg_obtener").val(PesoObtener);
					}
				}
				
				// Table 3
				var table3 = "";
				table3 += "<div>";
				table3 += "	<p style=\"font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;\">";
				table3 += "		Despuntes";
				table3 += "	</p>";
				table3 += "</div>";
				table3 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table3 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table3 += "			Codigo";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:50%;\">";
				table3 += "			Producto";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table3 += "			Und";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table3 += "			Cant";
				table3 += "		</td>";
				table3 += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
				table3 += "			PesoT";
				table3 += "		</td>";
				table3 += "	</tr>";
				
				var PesoDespuntes = 0;
				
				var len = data[0]["Despuntes"].length;
				if (len <= 0)
					table3 += "</table>";
				for (var i = 0; i < len; i++)
				{
					var Peso = data[0]["Despuntes"][i]["Cantidad"] * ProductoPeso[data[0]["Despuntes"][i]["CodFab"]];
					PesoDespuntes += Peso;
					
					table3 += "	<tr>";
					table3 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table3 += "			"+data[0]["Despuntes"][i]["CodFab"];
					table3 += "		</td>";
					table3 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table3 += "			"+ProductoNombre[data[0]["Despuntes"][i]["CodFab"]];
					table3 += "		</td>";
					table3 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table3 += "			"+ProductoUndMed[data[0]["Despuntes"][i]["CodFab"]];
					table3 += "		</td>";
					table3 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table3 += "			"+data[0]["Despuntes"][i]["Cantidad"];
					table3 += "		</td>";
					table3 += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
					table3 += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
					table3 += "		</td>";
					table3 += "	</tr>";
					if (len - i == 1)
					{
						table3 += "</table>";
						$("#kg_despuntes").val(PesoDespuntes);
					}
				}
				
				// Table 4
				var table4 = "";
				table4 += "<div>";
				table4 += "	<p style=\"font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;\">";
				table4 += "		Operarios que Intervienen en el Proceso";
				table4 += "	</p>";
				table4 += "</div>";
				table4 += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table4 += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:60%;\">";
				table4 += "			Operario";
				table4 += "		</td>";
				table4 += "		<td style=\"border-bottom:grey 1px solid; width:40%;\">";
				table4 += "			Maquinaria";
				table4 += "		</td>";
				table4 += "	</tr>";
				
				var Old_Operario = "";
				var len = data[0]["ListaOperarios"].length;
				if (len <= 0)
					table4 += "</table>";
				for (var i = 0; i < len; i++)
				{
					if (data[0]["ListaOperarios"][i]["Operario"] == Old_Operario)
						continue;
					Old_Operario = data[0]["ListaOperarios"][i]["Operario"];
					table4 += "	<tr>";
					table4 += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table4 += "			"+data[0]["ListaOperarios"][i]["Operario"];
					table4 += "		</td>";
					table4 += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center; text-align:left; overflow:hidden; white-space: nowrap;\">";
					table4 += "			"+data[0]["ListaOperarios"][i]["Maquinaria"];
					table4 += "		</td>";
					table4 += "	</tr>";
					if (len - i == 1)
						table4 += "</table>";
				}
				
				// Table 5
				var table5 = "";
				table5 += "<table class=\"table1\" cellspacing=\"1\" cellpadding=\"1\" style=\"width:560px; margin-top:30px;\">";
				table5 += "	<tr>";
				table5 += "		<td>";
				table5 += "			Observaciones";
				table5 += "		</td>";
				table5 += "		<td rowspan=\"3\">";
				table5 += "			<textarea rows=\"4\" cols=\"25\" id=\"observaciones\" maxlength=\"200\" style=\"resize:none; width:200px;\" readonly>"+data[0]["Observaciones"]+"</textarea>";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			Digitado por";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			<input type=\"text\" id\=\"digitado_por\" style=\"width: 60px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["DigitadorID"]+"\"/>";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			<input type=\"text\" id=\"fecha_digitado\" style=\"width: 130px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Fecha_Digitado"]+"\"/>";
				table5 += "		</td>";
				table5 += "	</tr>";
				table5 += "	<tr>";
				table5 += "		<td>";
				table5 += "			&nbsp;";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			Aprobado por";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			<input type=\"text\" style=\"width: 60px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["AprobadorID"]+"\"/>";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			<input type=\"text\" style=\"width: 130px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Fecha_Aprobado"]+"\"/>";
				table5 += "		</td>";
				table5 += "	</tr>";
				table5 += "	<tr>";
				table5 += "		<td>";
				table5 += "			&nbsp;";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			Estado";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			<input type=\"text\" style=\"width: 60px; height: 20px;\" class=\"myinput\" readonly value=\""+data[0]["Estado"]+"\"/>";
				table5 += "		</td>";
				table5 += "		<td>";
				table5 += "			&nbsp;";
				table5 += "		</td>";
				table5 += "	</tr>";
				table5 += "</table>";
				
				var table_final = table1 + table2 + table3 + table4 + table5;
				$("#mid").html(table_final);
				$("#title").html("Orden de "+"<?php echo "".$Proceso." ".$Ord_Produccion.""; ?>");
				var Diferencia = parseFloat(PesoRequerido) - (parseFloat(PesoObtener) + parseFloat(PesoDespuntes) + parseFloat(data[0]["Rendimiento"]))
				$("#kg_diferencia").val(Diferencia);
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
		width: 710px;
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
		width: 710px;
		font-family:calibri;
		margin-top: 5px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 710px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 710px;
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
		<li id="title" style="padding:7px 0px; margin-left: 40px; width: 330px; text-align:center;">
			Orden de ??????????????? ?????????
		</li>
		<li style="padding:2px 0px; margin-left: 35px;">
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
			<td style="padding-right: 8px;">
				Destino
			</td>
			<td style="padding-right: 80px;">
				<input type="text" id="destino" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
			<td style="padding-right: 10px;">
				Cliente
			</td>
			<td colspan="3">
				<input type="text" id="cliente" style="width: 380px; height: 20px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Fecha Ini.
			</td>
			<td>
				<input type="text" id="fecha_ini" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				Kg. Requerido
			</td>
			<td>
				<input type="text" id="kg_requerido" style="width: 150px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Kg. a Obtener
			</td>
			<td>
				<input type="text" id="kg_obtener" style="width: 150px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Fecha Fin.
			</td>
			<td>
				<input type="text" id="fecha_fin" style="width: 110px; height: 20px;" class="myinput" readonly/>
			</td>
			<td>
				Kg. Despuntes
			</td>
			<td>
				<input type="text" id="kg_despuntes" style="width: 150px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Diferencia
			</td>
			<td>
				<input type="text" id="kg_diferencia" style="width: 150px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<div>
			<p style="font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;">
				Material Requerido
			</p>
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:40%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:9%;">
					Und
				</td>
				<td style="border-bottom:grey 1px solid; width:13%;">
					Cant
				</td>
				<td style="border-bottom:grey 1px solid; width:13%;">
					PesoT
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Origen
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Kilo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					ASDASDSADASDAS
				</td>
			</tr>
		</table>
		<div>
			<p style="font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;">
				Productos a Obtener
			</p>
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:40%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:9%;">
					Und
				</td>
				<td style="border-bottom:grey 1px solid; width:13%;">
					Cant
				</td>
				<td style="border-bottom:grey 1px solid; width:13%;">
					PesoT
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Enviar a
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Kilo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					ASDASDSADASDAS
				</td>
			</tr>
		</table>
		<div>
			<p style="font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;">
				Despuntes
			</p>
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:50%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Und
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Cant
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					PesoT
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Kilo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					9.999.999.99
				</td>
			</tr>
		</table>
		<div>
			<p style="font-family:calibri; font-size:18px; margin: 20px 0px 5px 0px;">
				Operarios que Intervienen en el Proceso
			</p>
		</div>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:60%;">
					Operario
				</td>
				<td style="border-bottom:grey 1px solid; width:40%;">
					Maquinaria
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDSADASDAS ADASDASDASDSADAS ADSADASD ASDASDA
				</td>
			</tr>
		</table>
		<table class="table1" cellspacing="1" cellpadding="1" style="width:560px; margin-top:30px;">
			<tr>
				<td>
					Observaciones
				</td>
				<td rowspan="3">
					<textarea rows="4" cols="25" id="observaciones" maxlength="200" style="resize:none; width:200px;" readonly></textarea>
				</td>
				<td>
					Digitado por
				</td>
				<td>
					<input type="text" id="digitado_por" style="width: 60px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="fecha_digitado" style="width: 130px; height: 20px;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					Aprobado por
				</td>
				<td>
					<input type="text" id="aprobado_por" style="width: 60px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="fecha_aprobado" style="width: 130px; height: 20px;" class="myinput" readonly/>
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
				<td>
					Estado
				</td>
				<td>
					<input type="text" id="estado" style="width: 60px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					&nbsp;
				</td>
			</tr>
		</table>
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>