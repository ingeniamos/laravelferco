<?php
	session_start();
	//----
	$Interno = isset($_GET["Interno"]) ? $_GET["Interno"]:"";
	$Item = isset($_GET["Item"]) ? $_GET["Item"]:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/jqwidgets/jqxdraw.js"></script>
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
			data: {
				"Mallas_Imprimir":"<?php echo $Interno; ?>",
				"itemMalla":"<?php echo $Item; ?>",
			},
			success: function (data, status, xhr)
			{
				var table = "";
				var Total_Longitud = 0;
				var Total_Peso = 0;
				var Viejo_ID = "_empty_";
				
				// if (data[0]["Items"] != undefined)
				// {
					// table += "<table cellspacing=\"2\" cellpadding=\"3\" style=\"font-family:calibri; margin-top: 10px; margin-bottom: 10px; font-size:12px; text-align:left;\">";
					// for (var i = 0; i < data[0]["Items"].length; i++)
					// {
						// if (Viejo_ID != data[0]["Items"][i]["Nombre"] && i != 0)
						// {
							// table += "	<tr>";
							// table += "		<td>";
							// table += "			Figurado";
							// table += "		</td>";
							// table += "		<td>";
							// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 335px;\" readonly  value=\""+Viejo_ID+"\"/>";
							// table += "		</td>";
							// table += "		<td>";
							// table += "			Total Longitud (m)";
							// table += "		</td>";
							// table += "		<td>";
							// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							// table += "		</td>";
							// table += "		<td>";
							// table += "			Total Peso (kg)";
							// table += "		</td>";
							// table += "		<td>";
							// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
							// table += "		</td>";
							// table += "	</tr>";
						// }
						
						// if (Viejo_ID != data[0]["Items"][i]["Nombre"])
						// {
							// Viejo_ID = data[0]["Items"][i]["Nombre"];
							// var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]).toFixed(2);
							// var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]).toFixed(2);
							// var Peso = parseFloat(data[0]["Items"][i]["Peso"]).toFixed(3);
							
							// Total_Longitud = Math.round((Longitud * Cantidad) * 100) / 100;
							// Total_Peso = Math.round((Peso * Cantidad) * 100) / 100;
							
							// if ((data[0]["Items"].length - i) == 1)
							// {
								// table += "	<tr>";
								// table += "		<td>";
								// table += "			Figurado";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 335px;\" readonly  value=\""+Viejo_ID+"\"/>";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			Total Longitud (m)";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			Total Peso (kg)";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								// table += "		</td>";
								// table += "	</tr>";
							// }
						// }
						// else
						// {
							// var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]).toFixed(2);
							// var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]).toFixed(2);
							// var Peso = parseFloat(data[0]["Items"][i]["Peso"]).toFixed(3);
							
							// Total_Longitud += Math.round((Longitud * Cantidad) * 100) / 100;
							// Total_Peso += Math.round((Peso * Cantidad) * 100) / 100;
							// Total_Peso = Math.round(Total_Peso * 100) / 100;
							
							// if ((data[0]["Items"].length - i) == 1)
							// {
								// table += "	<tr>";
								// table += "		<td>";
								// table += "			Figurado";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 335px;\" readonly  value=\""+Viejo_ID+"\"/>";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			Total Longitud (m)";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			Total Peso (kg)";
								// table += "		</td>";
								// table += "		<td>";
								// table += "			<input type=\"text\" class=\"myinput\" style=\"width: 110px; text-align:right;\" readonly value=\""+Total_Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
								// table += "		</td>";
								// table += "	</tr>";
							// }
							/*table += "	<tr>";
							table += "		<td>";
							table += "			Hierro";
							table += "		</td>";
							table += "		<td>";
							table += "			<input type=\"text\" class=\"myinput\" style=\"width: 335px;\" readonly  value=\""+data[0]["Items"][i]["Nombre"]+"\"/>";
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
							table += "	</tr>";*/
						// }
					// }
					// table += "</table>";
				// }
				
				table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:20px;\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; width:185px;\">";
				table += "			Detalle";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
				table += "			Tipo";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:100px;\">";
				table += "			Calibre en A";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
				table += "			Varillas A";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
				table += "			CortesA";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:100px;\">";
				table += "			Calibre en B";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
				table += "			Varillas B";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
				table += "			CortesB";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
				table += "			Cant";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
				table += "			PesoU";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:80px;\">";
				table += "			Peso Parc";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:25px;\">";
				table += "			Ubic.";
				table += "		</td>";
				table += "	</tr>";
				
				Total_Longitud = 0;
				Total_Peso = 0;
				
				if (data[0]["Items"] != undefined)
				{
					var LineBreak = 0;
					for (var i = 0; i < data[0]["Items"].length; i++)
					{
						LineBreak++;
						if (LineBreak == 31)
						{
							LineBreak = 0;
							table += "</table>";
							table += "<div id=\"break\" ></div>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:20px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							table += "		<td style=\"border-bottom:grey 1px solid; width:185px;\">";
							table += "			Detalle";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
							table += "			Tipo";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:100px;\">";
							table += "			Calibre en A";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
							table += "			Varillas A";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
							table += "			Cortes";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:100px;\">";
							table += "			Calibre en B";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
							table += "			Varillas B";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
							table += "			Cortes";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
							table += "			Cant";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:45px;\">";
							table += "			PesoU";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:80px;\">";
							table += "			Peso Parc";
							table += "		</td>";
							table += "		<td style=\"border-bottom:grey 1px solid; width:25px;\">";
							table += "			Ubic.";
							table += "		</td>";
							table += "	</tr>";
						}
						
						var Cantidad = parseFloat(data[0]["Items"][i]["Cantidad"]);
						var Longitud = parseFloat(data[0]["Items"][i]["Longitud"]);
						var Peso = parseFloat(data[0]["Items"][i]["Peso"]);
						var contenedor = "content"+[i];
						var cantidadF = data[0]["Items"][i]["cant"];
						var varA = data[0]["Items"][i]["varA"];
						var arrA = parseFloat(data[0]["Items"][i]["arrA"]);
						var finA = parseFloat(data[0]["Items"][i]["finA"]);
						var varB = data[0]["Items"][i]["varB"];
						var arrB = parseFloat(data[0]["Items"][i]["arrB"]);
						var finB = parseFloat(data[0]["Items"][i]["finB"]);
						var ancho = parseInt(data[0]["Items"][i]["ancho"]);
						var largo = parseInt(data[0]["Items"][i]["largo"]);
						
						Total_Longitud += Math.round((Longitud * Cantidad) * 100) / 100;
						Total_Peso += Math.round((Peso * Cantidad) * 100) / 100;
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; overflow:hidden;\">";
						table += "			<b>Dim. en A:</b> Arr: "+arrA+", Fin: "+finA+", S: "+data[0]["Items"][i]["sepA"]+"<br/>";
						table += "			<b>Dim. en B:</b> Arr: "+arrB+", Fin: "+finB+", S: "+data[0]["Items"][i]["sepB"]+"";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid;\">";
						table += "			"+data[0]["Items"][i]["tipo"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
						table += "			"+data[0]["Items"][i]["producto1"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+varA+"<br/>"+ancho+"cm";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			<b>T</b>: "+(varA*cantidadF).toLocaleString('es-ES', { minimumFractionDigits: '0', maximumFractionDigits: '3' })+"<br/><b>U:</b> "+varA;
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
						table += "			"+data[0]["Items"][i]["producto2"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+varB+"<br/>"+largo+"cm";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			<b>T</b>: "+(varB*cantidadF).toLocaleString('es-ES', { minimumFractionDigits: '0', maximumFractionDigits: '3' })+"<br/><b>U:</b> "+varB;
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+cantidadF;
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+data[0]["Items"][i]["pesoU"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+(cantidadF*data[0]["Items"][i]["pesoU"]).toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '3' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:left;\">";
						table += "			"+data[0]["Items"][i]["ref"];
						table += "		</td>";
						table += "	</tr>";
					}	
										
					//Dibujar
					$('#contenedorx').jqxDraw();
					var renderer = $('#contenedorx').jqxDraw('getInstance');
					var size = renderer.getSize();
					var rel1 = 800/ancho;
					var rel2 = 800/largo;
					var d1 = (800-(arrA+finA)*rel1)/varB;
					var d2 = (800-(arrB+finB)*rel2)/varA;
					
					renderer.text("" + arrA + " cm >>", 10, (arrB*rel2), undefined, undefined, 0, { 'class': 'smallText' }, false, 'left', 'center', 'left');
					renderer.text("<< " + finA + " cm", 750, (arrB*rel2), undefined, undefined, 0, { 'class': 'smallText' }, false, 'left', 'center', 'left');
					renderer.text("<< " + arrB + " cm", d1+10+(arrA*rel1), 10, undefined, undefined, -90, { 'class': 'smallText' }, false, 'left', 'right', 'right');
					renderer.text("" + finB + " cm >>", d1+10+(arrA*rel1), 800, undefined, undefined, -90, { 'class': 'smallText' }, false, 'left', 'right', 'left');
					
					for (i = 1; i <= varA; i++) { 				
						renderer.line(10, arrB*rel2+d2*i, 810, arrB*rel2+d2*i, { stroke: 'red', 'stroke-width': 1 });
					}
					for (i = 1; i <= varB; i++) { 				
						renderer.line(arrA*rel1+d1*i, 10, arrA*rel1+d1*i, 810, { stroke: 'blue', 'stroke-width': 1 });
					}
					
					$("#interno").val(data[0]["interno_malla"]);
					$("#orden_produccion").val(data[0]["interno_venta"]);
					$("#cliente").val(data[0]["Cliente"]);
					$("#cliente_id").val(data[0]["cliente_id"]);
					$("#obra").val(data[0]["obra"]);
					$("#digitado_por").val(data[0]["Digitado_Por"]);
					$("#fecha_digitado").val(SetFormattedDate(data[0]["Fecha_Digitado"]));
					$("#modificado_por").val(data[0]["Modificado_Por"]);
					$("#fecha_modificado").val(SetFormattedDate(data[0]["Fecha_Modificado"]));
					$("#total_longitud").val(Total_Longitud.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
					$("#total_peso").val(data[0]["pesoTotal"]+" kg.");
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
			Cartilla de Mallas
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
				
			</td>
			<td colspan="2">
				
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
	<div id="mid">
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
		</table>-->
	</div>
	<div id="contenedorx" style='width:820px; height:820px; border: 1px solid gray; border-radius: 5px; margin: 25px 50px;'></div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
	<!--<div id="contenedorxx" style='width:180px; height:100px;'></div>-->
</div>
<br /><br />