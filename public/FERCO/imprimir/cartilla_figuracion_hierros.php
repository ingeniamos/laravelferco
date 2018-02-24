<?php
	session_start();
	//---
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
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
	
	if ("<?php echo $Interno; ?>" != "")
	{
		$.ajax({
			dataType: "json",
			url: "../modulos/datos_productos.php",
			data: {"Figuracion_Imprimir_Hierros":"<?php echo $Interno; ?>"},
			success: function (data, status, xhr)
			{
				var table = "";
				var myarray = new Array();
				var Total_Cantidad = 0;
				var Total_Longitud = 0;
				var Total_Peso = 0;
				var Viejo_ID = "_empty_";
				var Viejo_ID2 = "_empty_";
				
				if (data[0]["Items"] != undefined)
				{
					table += "<table cellspacing=\"2\" cellpadding=\"3\" style=\"font-family:calibri; margin-top: 10px; margin-bottom: 10px; font-size:12px; text-align:left;\">";
					for (var i = 0; i < data[0]["Items"].length; i++)
					{
						if (Viejo_ID != data[0]["Items"][i]["Nombre"] && i != 0)
						{
							table += "	<tr>";
							table += "		<td>";
							table += "			Figurado";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" class=\"myinput\" style=\"width: 427px;\" readonly  value=\""+Viejo_ID+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			Total Longitud (m)";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "		<td>";
							table += "			Total Peso (kg)";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							
							var array = {};
							array["Cantidad"] = Total_Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							array["Longitud"] = Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							array["Peso"] = Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							myarray[""+Viejo_ID+""] = array;
						}
						
						if (Viejo_ID != data[0]["Items"][i]["Nombre"])
						{
							Viejo_ID = data[0]["Items"][i]["Nombre"];
							var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
							var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]);
							var Peso = parseFloat(data[0]["Items"][i]["Peso"]);
							
							Total_Cantidad = Cantidad;
							Total_Longitud = Math.round((Longitud * Cantidad) * 100) / 100;
							Total_Peso = Peso;
							
							if ((data[0]["Items"].length - i) == 1)
							{
								table += "	<tr>";
								table += "		<td>";
								table += "			Figurado";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" class=\"myinput\" style=\"width: 427px;\" readonly  value=\""+Viejo_ID+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			Total Longitud (m)";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			Total Peso (kg)";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "	</tr>";
								
								var array = {};
								array["Cantidad"] = Total_Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								array["Longitud"] = Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								array["Peso"] = Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								myarray[""+Viejo_ID+""] = array;
							}
						}
						else
						{
							var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
							var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]);
							var Peso = parseFloat(data[0]["Items"][i]["Peso"]);
							
							Total_Cantidad += Cantidad;
							Total_Longitud += Math.round((Longitud * Cantidad) * 100) / 100;
							Total_Peso += Peso;
							
							if ((data[0]["Items"].length - i) == 1)
							{
								table += "	<tr>";
								table += "		<td>";
								table += "			Figurado";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" class=\"myinput\" style=\"width: 427px;\" readonly  value=\""+Viejo_ID+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			Total Longitud (m)";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			Total Peso (kg)";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "	</tr>";
								
								var array = {};
								array["Cantidad"] = Total_Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								array["Longitud"] = Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								array["Peso"] = Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
								myarray[""+Viejo_ID+""] = array;
							}
						}
					}
					table += "</table>";
				}
				
				Viejo_ID = "_empty_";
				Total_Cantidad = 0;
				Total_Longitud = 0;
				Total_Peso = 0;
				
				if (data[0]["Items"] != undefined)
				{
					/*for (var i = 0; i < data[0]["Items"].length; i++)
					{
						if (Viejo_ID != data[0]["Items"][i]["Figura"]+data[0]["Items"][i]["Detalle"]+data[0]["Items"][i]["Nombre"] && i != 0)
						{
							//
						}
						
						if (Viejo_ID != data[0]["Items"][i]["Figura"]+data[0]["Items"][i]["Detalle"]+data[0]["Items"][i]["Nombre"])
						{
							Viejo_ID = data[0]["Items"][i]["Figura"]+data[0]["Items"][i]["Detalle"]+data[0]["Items"][i]["Nombre"];
							
							var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
							var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]);
							var Peso = parseFloat(data[0]["Items"][i]["Peso"]);
							
							Total_Cantidad = Cantidad;
							Total_Longitud = Longitud * Cantidad;
							Total_Peso = Peso * Cantidad;
							
							if ((data[0]["Items"].length - i) == 1)
							{
								//
							}
						}
						else
						{
							var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
							var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]);
							var Peso = parseFloat(data[0]["Items"][i]["Peso"]);
							
							Total_Cantidad += Cantidad;
							Total_Longitud += Longitud * Cantidad;
							Total_Peso += Peso * Cantidad;
							
							if ((data[0]["Items"].length - i) == 1)
							{
								//
							}
						}
					}*/
					
					var LineBreak = 0;
					for (var i = 0; i < data[0]["Items"].length; i++)
					{
						LineBreak++;
						if (Viejo_ID != data[0]["Items"][i]["Nombre"] && i != 0)
						{
							Viejo_ID = data[0]["Items"][i]["Nombre"];
							var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
							var Longitud_Un = parseFloat(data[0]["Items"][i]["Longitud"]);
							var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]) * Cantidad;
							var Peso = parseFloat(data[0]["Items"][i]["Peso"]);
							
							Total_Cantidad += Cantidad;
							Total_Longitud += Longitud;
							Total_Peso += Peso;
							//alert("Peso -> "+Total_Peso)
							//alert("entrada secundaria si no es el mismo ->"+data[0]["Items"][i]["Nombre"])
							
							table += "</table><br />";
							
							if (LineBreak > 12)
							{
								LineBreak = 0;
								//table += "<div id=\"break\" ></div>";
							}
							LineBreak++;
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:7%;\">";
							table += "			Figurado";
							table += "		</td>";
							table += "		<td style=\"width:25%;\">";
							table += "			<input type=\"text\" style=\"width: 220px; height: 20px;\" class=\"myinput\" readonly value=\""+Viejo_ID+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:10%;\">";
							table += "			Total Cantidad";
							table += "		</td>";
							table += "		<td style=\"width:12%;\">";
							table += "			<input type=\"text\" style=\"width: 100px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+Viejo_ID+""]["Cantidad"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:11%;\">";
							table += "			Total Longitud (m)";
							table += "		</td>";
							table += "		<td style=\"width:12%;\">";
							table += "			<input type=\"text\" style=\"width: 100px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+Viejo_ID+""]["Longitud"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:10%;\">";
							table += "			Total Peso (kg)";
							table += "		</td>";
							table += "		<td style=\"width:13%;\">";
							table += "			<input type=\"text\" style=\"width: 100px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+Viejo_ID+""]["Peso"]+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							//---
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:25%;\">";
							table += "			Diagrama";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Detalle (m)";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:22%;\">";
							table += "			Figurado";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Cantidad";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
							table += "			Long (m)";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Peso (kg)";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Ubicacion";
							table += "		</td>";
							table += "	</tr>";
							//---
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; overflow:hidden;\">";
							table += "			<img src=\"../images/"+data[0]["Items"][i]["Imagen"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
							table += "			"+data[0]["Items"][i]["Detalle"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
							table += "			"+data[0]["Items"][i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Longitud_Un.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:left;\">";
							table += "			"+data[0]["Items"][i]["Ubicacion"];
							table += "		</td>";
							table += "	</tr>";
						}
						else if (Viejo_ID != data[0]["Items"][i]["Nombre"])
						{
							LineBreak++;
							Viejo_ID = data[0]["Items"][i]["Nombre"];
							var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
							var Longitud_Un = parseFloat(data[0]["Items"][i]["Longitud"]);
							var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]) * Cantidad;
							var Peso = parseFloat(data[0]["Items"][i]["Peso"]);
							
							Total_Cantidad += Cantidad;
							Total_Longitud += Longitud;
							Total_Peso += Peso;
							//alert("entrada primaria si no es el mismo ->"+data[0]["Items"][i]["Nombre"])
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"width:7%;\">";
							table += "			Figurado";
							table += "		</td>";
							table += "		<td style=\"width:25%;\">";
							table += "			<input type=\"text\" style=\"width: 220px; height: 20px;\" class=\"myinput\" readonly value=\""+Viejo_ID+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:10%;\">";
							table += "			Total Cantidad";
							table += "		</td>";
							table += "		<td style=\"width:12%;\">";
							table += "			<input type=\"text\" style=\"width: 100px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+Viejo_ID+""]["Cantidad"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:11%;\">";
							table += "			Total Longitud (m)";
							table += "		</td>";
							table += "		<td style=\"width:12%;\">";
							table += "			<input type=\"text\" style=\"width: 100px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+Viejo_ID+""]["Longitud"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"width:10%;\">";
							table += "			Total Peso (kg)";
							table += "		</td>";
							table += "		<td style=\"width:13%;\">";
							table += "			<input type=\"text\" style=\"width: 100px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+myarray[""+Viejo_ID+""]["Peso"]+"\"/>";
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							//---
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:25%;\">";
							table += "			Diagrama";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Detalle (m)";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:22%;\">";
							table += "			Figurado";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Cantidad";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
							table += "			Long (m)";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
							table += "			Peso (kg)";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
							table += "			Ubicacion";
							table += "		</td>";
							table += "	</tr>";
							//---
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; overflow:hidden;\">";
							table += "			<img src=\"../images/"+data[0]["Items"][i]["Imagen"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
							table += "			"+data[0]["Items"][i]["Detalle"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
							table += "			"+data[0]["Items"][i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Longitud_Un.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:left;\">";
							table += "			"+data[0]["Items"][i]["Ubicacion"];
							table += "		</td>";
							table += "	</tr>";
								
							if ((data[0]["Items"].length - i) == 1)
							{
								//alert("fin2")
								table += "</table><br />";
							}
						}
						else
						{
							if (LineBreak > 14)//ACA ME QUEDE: Tabla con fix-layout + max height en imagen para poder cuadrar esto...
							{
								LineBreak = 0;
								table += "</table>";
								//table += "<div id=\"break\" ></div>";
								table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
								table += "	<tr>";
								table += "		<td style=\"height: 0%; width:25%;\">";
								table += "			";
								table += "		</td>";
								table += "		<td style=\"height: 0%; width:10%;\">";
								table += "			";
								table += "		</td>";
								table += "		<td style=\"height: 0%; width:22%;\">";
								table += "			";
								table += "		</td>";
								table += "		<td style=\"height: 0%; width:10%;\">";
								table += "			";
								table += "		</td>";
								table += "		<td style=\"height: 0%; width:8%;\">";
								table += "			";
								table += "		</td>";
								table += "		<td style=\"height: 0%; width:10%;\">";
								table += "			";
								table += "		</td>";
								table += "		<td style=\"height: 0%; width:15%;\">";
								table += "			";
								table += "		</td>";
								table += "	</tr>";
							}
							
							var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
							var Longitud_Un = parseFloat(data[0]["Items"][i]["Longitud"]);
							var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]) * Cantidad;
							var Peso = parseFloat(data[0]["Items"][i]["Peso"]);
							
							Total_Cantidad += Cantidad;
							Total_Longitud += Longitud;
							Total_Peso += Peso;
							//alert("sumando ->"+data[0]["Items"][i]["Nombre"])
							
							table += "	<tr>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; overflow:hidden;\">";
							table += "			<img src=\"../images/"+data[0]["Items"][i]["Imagen"]+"\"/>";
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
							table += "			"+data[0]["Items"][i]["Detalle"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
							table += "			"+data[0]["Items"][i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Longitud_Un.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:left;\">";
							table += "			"+data[0]["Items"][i]["Ubicacion"];
							table += "		</td>";
							table += "	</tr>";
							
							if ((data[0]["Items"].length - i) == 1)
							{
								//alert("fin3")
								table += "</table><br />";
							}
						}
						/*
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; overflow:hidden;\">";
						table += "			<img src=\"../images/"+data[0]["Items"][i]["Imagen"]+"\"/>";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
						table += "			"+data[0]["Items"][i]["Detalle"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
						table += "			"+data[0]["Items"][i]["Nombre"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+Cantidad.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:left;\">";
						table += "			"+data[0]["Items"][i]["Ubicacion"];
						table += "		</td>";
						table += "	</tr>";
						*/
					}
					
					$("#interno").val("<?php echo $Interno; ?>");
					$("#orden_produccion").val(data[0]["Orden_Produccion"]);
					$("#cliente").val(data[0]["Cliente"]);
					$("#cliente_id").val(data[0]["ClienteID"]);
					$("#obra").val(data[0]["Obra"]);
					$("#digitado_por").val(data[0]["Digitado_Por"]);
					$("#fecha_digitado").val(SetFormattedDate(data[0]["Fecha_Digitado"]));
					$("#modificado_por").val(data[0]["Modificado_Por"]);
					$("#fecha_modificado").val(SetFormattedDate(data[0]["Fecha_Modificado"]));
					$("#total_longitud").val(Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
					$("#total_peso").val(Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
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
		margin:auto;
	}
	
	#export_button{
		width:100px;
		height:25px;
		display: block;
		color: #FFF;
		background-color: #5BB75B;
		border-color: #5BB75B;
	}
	
	@media all {
		#break { display: none; }
	}
	@media print
	{
		#break { display: block; page-break-before: always; }
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
		<li style="padding:7px 0px; margin-left: 80px;">
			Cartilla de Figuracion
		</li>
		<li style="padding:2px 0px; margin-left: 80px;">
			<table  cellspacing="0" cellpadding="0" style="font-size:12px; text-align:center;">
				<tr>
					<td>
						Digitado
					</td>
					<td>
						Modificado
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
						<input type="text" id="digitado_por" style="width: 50px; height: 20px; text-align:left;" class="myinput" readonly/>
						<input type="text" id="fecha_digitado" style="width: 75px; height: 20px; text-align:center;" class="myinput" readonly/>
					</td>
					<td style="padding-right:3px;">
						<input type="text" id="modificado_por" style="width: 50px; height: 20px; text-align:left;" class="myinput" readonly/>
						<input type="text" id="fecha_modificado" style="width: 75px; height: 20px; text-align:center;" class="myinput" readonly/>
					</td>
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
			<td>
				Cartilla
			</td>
			<td colspan="2">
				<input type="text" id="interno" style="width: 124px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="width: 80px;">
				Ord. Produccion
			</td>
			<td colspan="2">
				<input type="text" id="orden_produccion" style="width: 124px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="width: 70px;">
				Cliente ID
			</td>
			<td style="text-align:left;">
				<input type="text" id="cliente_id" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Cliente
			</td>
			<td colspan="2">
				<input type="text" id="cliente" style="width: 290px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="width: 70px;">
				Total Longitud
			</td>
			<td colspan="2">
				<input type="text" id="total_longitud" style="width: 125px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				Total Peso
			</td>
			<td colspan="2" style="text-align:left;">
				<input type="text" id="total_peso" style="width: 125px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				Obra
			</td>
			<td colspan="4">
				<input type="text" id="obra" style="width: 412px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid" style="width: 900px;">
		<!--<table cellspacing="2" cellpadding="3" style="font-family:calibri; margin-top: 10px; margin-bottom: 10px; font-size:12px; text-align:left;">
			<tr>
				<td>
					Hierro
				</td>
				<td>
					<input type="text" class="myinput" style="width: 335px;" readonly/>
				</td>
				<td>
					Total Longitud (m)
				</td>
				<td>
					<input type="text" class="myinput" style="width: 110px; text-align:right;" readonly/>
				</td>
				<td>
					Total Peso (kg)
				</td>
				<td>
					<input type="text" class="myinput" style="width: 110px; text-align:right;" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:7%;">
					Figurado
				</td>
				<td style="width:25%;">
					<input type="text" style="width: 220px; height: 20px;" class="myinput" readonly/>
				</td>
				<td style="width:10%;">
					Total Cantidad
				</td>
				<td style="width:12%;">
					<input type="text" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td style="width:11%;">
					Total Longitud (m)
				</td>
				<td style="width:12%;">
					<input type="text" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td style="width:10%;">
					Total Peso (kg)
				</td>
				<td style="width:13%;">
					<input type="text" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:25%;">
					Diagrama
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Detalle
				</td>
				<td style="border-bottom:grey 1px solid; width:22%;">
					Hierro
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Cantidad
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Long (m)
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Peso (kg)
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Ubicacion
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;">
					ASDASDASDSADSAD ASDASDASDSADSAD ASDASDASDSADSAD ASDASDASDSADSAD
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;">
					A=10, B=59, C=95, D=21, E=62, F=56, G=20
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;">
					SDASDASDSADSAD ASDASDASDSADSAD
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					9.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid;">
					ASDASDASDSADSAD ASDASDASDSADSAD ASDASDASDSADSAD ASDASDASDSADSAD
				</td>
			</tr>
		</table>-->
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />