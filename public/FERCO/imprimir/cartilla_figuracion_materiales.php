<?php
	session_start();
	//---
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	var ProductosData = new Array();
	
	$.ajax(
	{
		dataType: "json",
		url: "../modulos/datos_productos.php",
		async: false,
		success: function (data)
		{
			for (var i = 0; i < data.length; i++)
			{
				ProductosData[""+data[i]["CodFab"]+""] = data[i]["Nombre"];
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
	
	if ("<?php echo $Interno; ?>" != "")
	{
		$.ajax({
			dataType: "json",
			url: "../modulos/datos_productos.php",
			data: {"Figuracion_Imprimir_Materiales":"<?php echo $Interno; ?>"},
			success: function (data, status, xhr)
			{
				var table = "";
				var Total_Peso = 0;
				var Desperdicio = 0;
				var Viejo_ID = "_empty_";
				
				if (data[0]["Hierros"] != undefined)
				{
					var Hierros = JSON.parse(data[0]["Hierros"]);
					for (var i = 0; i < Hierros.length; i++)
					{
						var Peso = parseFloat(Hierros[i]["Peso"]).toFixed(2);
						var Peso2 = parseFloat(Hierros[i]["Peso2"]).toFixed(2);
						
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:20px;\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"width:7%;\">";
						table += "			Figurado";
						table += "		</td>";
						table += "		<td style=\"width:38%;\">";
						table += "			<input type=\"text\" style=\"width: 250px; height: 20px;\" class=\"myinput\" readonly value=\""+ProductosData[Hierros[i]["Codigo"]]+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:11%;\">";
						table += "			Peso Total (kg)";
						table += "		</td>";
						table += "		<td style=\"width:14%;\">";
						table += "			<input type=\"text\" style=\"width: 90px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Peso+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:15%;\">";
						table += "			Peso Requerido (kg)";
						table += "		</td>";
						table += "		<td style=\"width:15%;\">";
						table += "			<input type=\"text\" style=\"width: 90px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Peso2+"\"/>";
						table += "		</td>";
						table += "	</tr>";
						table += "</table>";
						//---
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
						table += "			Codigo";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:46%;\">";
						table += "			Producto";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Peso (kg)";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Cantidad";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
						table += "			Peso Total (kg)";
						table += "		</td>";
						table += "	</tr>";
						
						var Items = Hierros[i]["Items"];
						for (var a = 0; a < Items.length; a++)
						{
							var Peso = parseFloat(Items[a]["Peso"]).toFixed(2);
							var Cantidad = parseFloat(Items[a]["Cantidad"]).toFixed(2);
							var Peso2 = parseFloat(Items[a]["Peso2"]).toFixed(2);
						
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
							table += "			"+Items[a]["Codigo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
							table += "			"+ProductosData[Items[a]["Codigo"]];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Peso2.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "	</tr>";
						}
						table += "</table>";
						if ((Hierros.length - i) == 1)
						{
							table += "</table>";
						}
					}
					
					Total_Peso = parseFloat(data[0]["Peso"]);
					Desperdicio = parseFloat(data[0]["Desperdicio"]);
					
					$("#interno").val("<?php echo $Interno; ?>");
					$("#orden_produccion").val(data[0]["Orden_Produccion"]);
					$("#cliente").val(data[0]["Cliente"]);
					$("#cliente_id").val(data[0]["ClienteID"]);
					$("#obra").val(data[0]["Obra"]);
					$("#digitado_por").val(data[0]["Digitado_Por"]);
					$("#fecha_digitado").val(SetFormattedDate(data[0]["Fecha_Digitado"]));
					$("#modificado_por").val(data[0]["Modificado_Por"]);
					$("#fecha_modificado").val(SetFormattedDate(data[0]["Fecha_Modificado"]));
					$("#total_peso").val(Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
					$("#desperdicio").val(Desperdicio.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" %");
				}
				
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
		width: 700px;
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
		width: 700px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.table2{
		table-layout:fixed;
		font-family:calibri;
		width: 700px;
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
		width: 700px;
		list-style:none;
	}
	
	#print_button{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #FAA732;
		border-color: #FAA732;
		margin:auto;
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
		<li style="padding:7px 0px; margin-left: 105px;">
			Materiales Requeridos
		</li>
		<li style="padding:2px 0px; margin-left: 105px;">
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
	<table class="table1" cellspacing="1" cellpadding="1" style="text-align: center;">
		<tr>
			<td style="text-align:left;">
				Cartilla
			</td>
			<td style="text-align:left;">
				<input type="text" id="interno" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="width:45px;">
				O.P.
			</td>
			<td style="text-align:left;">
				<input type="text" id="orden_produccion" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Digitado
			</td>
			<td style="text-align:left;">
				<input type="text" id="digitado_por" style="width: 50px; height: 20px; text-align:left;" class="myinput" readonly/>
				<input type="text" id="fecha_digitado" style="width: 75px; height: 20px; text-align:center; margin-left:3px;" class="myinput" readonly/>
			</td>
			<td>
				Modificado
			</td>
			<td style="text-align:left;">
				<input type="text" id="modificado_por" style="width: 50px; height: 20px; text-align:left;" class="myinput" readonly/>
				<input type="text" id="fecha_modificado" style="width: 75px; height: 20px; text-align:center; margin-left:3px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="text-align:left; width:55px;">
				Cliente ID
			</td>
			<td style="text-align:left;">
				<input type="text" id="cliente_id" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Cliente
			</td>
			<td colspan="3" style="text-align:left;">
				<input type="text" id="cliente" style="width: 281px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td>
				Total Peso
			</td>
			<td style="text-align:left;">
				<input type="text" id="total_peso" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="text-align:left;">
				Obra
			</td>
			<td colspan="5" style="text-align:left;">
				<input type="text" id="obra" style="width: 434px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				Porcentaje de Desperdicio
				<input type="text" id="desperdicio" style="width: 50px; height: 20px; text-align:right; margin-left:5px;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />