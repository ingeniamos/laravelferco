<?php
	session_start();
	//---
	$ClienteID = isset($_GET['ClienteID']) ? $_GET['ClienteID']:"";
	$Fecha_Ini = isset($_GET['Fecha_Ini']) ? $_GET['Fecha_Ini']:"";
	$Fecha_Fin = isset($_GET['Fecha_Fin']) ? $_GET['Fecha_Fin']:"";
	$VendedorID = isset($_GET['VendedorID']) ? $_GET['VendedorID']:"";
	$CobradorID = isset($_GET['CobradorID']) ? $_GET['CobradorID']:"";
	$Estado = isset($_GET['Estado']) ? $_GET['Estado']:"";
	$Ord_Compra = isset($_GET['Ord_Compra']) ? $_GET['Ord_Compra']:"";
	$Remision = isset($_GET['Remision']) ? $_GET['Remision']:"";
	$Factura = isset($_GET['Factura']) ? $_GET['Factura']:"";
	$Vehiculo = isset($_GET['Vehiculo']) ? $_GET['Vehiculo']:"";
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
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
		url: "../modulos/datos.php",
		data: {
			"Reportes_Ventas":true,
			"ClienteID":"<?php echo $ClienteID; ?>",
			"Fecha_Ini":"<?php echo $Fecha_Ini; ?>",
			"Fecha_Fin":"<?php echo $Fecha_Fin; ?>",
			"VendedorID":"<?php echo $VendedorID; ?>",
			"CobradorID":"<?php echo $CobradorID; ?>",
			"Estado":"<?php echo $Estado; ?>",
			"Ord_Compra":"<?php echo $Ord_Compra; ?>",
			"Remision":"<?php echo $Remision; ?>",
			"Factura":"<?php echo $Factura; ?>",
			"Vehiculo":"<?php echo $Vehiculo; ?>",
			"OrderBy":"<?php echo $OrderBy; ?>",
		},
		async: true,
		success: function (data, status, xhr)
		{
			var len = data.length;
			var table = "";
			var myarray = new Array();
			var first_row = true;
			var Nuevo_ID;
			var Viejo_ID = "_empty_";
			var TmpPeso = 0;
			var TmpValor = 0;
			
			if ("<?php echo $OrderBy; ?>" == "")
			{
				
				table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
				table += "			Fecha";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table += "			Interno";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table += "			Remision";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
				table += "			Factura";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:17%;\">";
				table += "			Cliente";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
				table += "			F. Pago";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
				table += "			Valor";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Peso";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
				table += "			Placa";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
				table += "			Vend.";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
				table += "			Cobr.";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:5%;\">";
				table += "			Dig.";
				table += "		</td>";
				table += "	</tr>";
						
				for (var i = 0; i < len; i++)
				{
					if (data[i]["ClienteID"] != undefined && data[i]["ClienteID"] != "")
					{
						var Valor = parseFloat(data[i]["Valor"]);
						var Peso = parseFloat(data[i]["Peso"]);
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+SetFormattedDate(data[i]["Fecha"]);
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Interno"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Remision"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Factura"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+ClienteData[data[i]["ClienteID"]];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[i]["F_Pago"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" Kg";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Vehiculo"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["VendedorID"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["CobradorID"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["DigitadorID"];
						table += "		</td>";
						table += "	</tr>";
					}
				}
			}
			else if ("<?php echo $OrderBy; ?>" == "Cliente")
			{
				for (var i = 0; i < len; i++)
				{
					if (Viejo_ID != data[i]["ClienteID"] && data[i]["ClienteID"] != undefined && data[i]["ClienteID"] != "")
					{
						var array = {};
						array["Peso"] = TmpPeso;
						array["Valor"] = TmpValor;
						
						if (i == 0)
							myarray[""+data[i]["ClienteID"]+""] = array;
						else
							myarray[""+data[i-1]["ClienteID"]+""] = array;
						
						Viejo_ID = data[i]["ClienteID"];
						TmpPeso = data[i]["Peso"];
						TmpValor = data[i]["Valor"];
					}
					else if (Viejo_ID == data[i]["ClienteID"] && data[i]["ClienteID"] != undefined && data[i]["ClienteID"] != "")
					{
						TmpPeso = + TmpPeso + parseFloat(data[i]["Peso"]);
						TmpValor = + TmpValor + parseFloat(data[i]["Valor"]);
					}
					
					if (len - i == 1)
					{
						var array = {};
						array["Peso"] = TmpPeso;
						array["Valor"] = TmpValor;
						
						myarray[""+data[i]["ClienteID"]+""] = array;
					}
				}
				
				Viejo_ID = "_empty_";
					
				for (var i = 0; i < len; i++)
				{
					if (Viejo_ID != data[i]["ClienteID"] && data[i]["ClienteID"] != "")
					{
						Viejo_ID = data[i]["ClienteID"];
						if (i != 0)
							table += "</table><br />";
						
						if (ClienteData[""+data[i]["ClienteID"]+""] == undefined)
						{
							Nuevo_ID = "";
						}
						else
						{
							Nuevo_ID = ClienteData[""+data[i]["ClienteID"]+""];
						}
						
						var Peso = parseFloat(myarray[""+data[i]["ClienteID"]+""]["Peso"]);
						var Valor = parseFloat(myarray[""+data[i]["ClienteID"]+""]["Valor"]);
						
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"width:8%;\">";
						table += "			Cliente ID";
						table += "		</td>";
						table += "		<td style=\"width:18%;\">";
						table += "			<input type=\"text\" style=\"width: 120px; height: 20px; text-align:center;\" class=\"myinput\" readonly value=\""+data[i]["ClienteID"]+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:9%;\">";
						table += "			Cliente";
						table += "		</td>";
						table += "		<td style=\"width:36%;\">";
						table += "			<input type=\"text\" style=\"width: 250px; height: 20px; text-align:left;\" class=\"myinput\" readonly value=\""+Nuevo_ID+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:9%;\">";
						table += "			Total Valor";
						table += "		</td>";
						table += "		<td style=\"width:18%;\">";
						table += "			<input type=\"text\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:9%;\">";
						table += "			Total Peso";
						table += "		</td>";
						table += "		<td style=\"width:18%;\">";
						table += "			<input type=\"text\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" Kg\"/>";
						table += "		</td>";
						table += "	</tr>";
						table += "</table>";
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
						table += "			Fecha";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Interno";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Remision";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Factura";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
						table += "			F. Pago";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
						table += "			Valor";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Peso";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
						table += "			Vend.";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
						table += "			Cobr.";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:7%;\">";
						table += "			Dig.";
						table += "		</td>";
						table += "	</tr>";
					}
					
					if (data[i]["ClienteID"] != undefined && data[i]["ClienteID"] != "")
					{
						var Valor = parseFloat(data[i]["Valor"]);
						var Peso = parseFloat(data[i]["Peso"]);
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+SetFormattedDate(data[i]["Fecha"]);
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Interno"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Remision"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Factura"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[i]["F_Pago"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" Kg";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["VendedorID"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["CobradorID"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["DigitadorID"];
						table += "		</td>";
						table += "	</tr>";
					}
				}
			}
			else if ("<?php echo $OrderBy; ?>" == "Vendedor")
			{
				for (var i = 0; i < len; i++)
				{
					if (Viejo_ID != data[i]["VendedorID"] && data[i]["VendedorID"] != undefined && data[i]["ClienteID"] != "")
					{
						var array = {};
						array["Peso"] = TmpPeso;
						array["Valor"] = TmpValor;
						
						if (i == 0)
							myarray[""+data[i]["VendedorID"]+""] = array;
						else
							myarray[""+data[i-1]["VendedorID"]+""] = array;
						
						Viejo_ID = data[i]["VendedorID"];
						TmpPeso = data[i]["Peso"];
						TmpValor = data[i]["Valor"];
					}
					else if (Viejo_ID == data[i]["VendedorID"] && data[i]["VendedorID"] != undefined && data[i]["ClienteID"] != "")
					{
						TmpPeso = + TmpPeso + parseFloat(data[i]["Peso"]);
						TmpValor = + TmpValor + parseFloat(data[i]["Valor"]);
					}
					
					if (len - i == 1)
					{
						var array = {};
						array["Peso"] = TmpPeso;
						array["Valor"] = TmpValor;
						
						myarray[""+data[i]["VendedorID"]+""] = array;
					}
				}
				
				Viejo_ID = "_empty_";
					
				for (var i = 0; i < len; i++)
				{
					if (Viejo_ID != data[i]["VendedorID"] && data[i]["VendedorID"] != "")
					{
						Viejo_ID = data[i]["VendedorID"];
						if (i != 0)
							table += "</table><br />";
						
						var Peso = parseFloat(myarray[""+data[i]["VendedorID"]+""]["Peso"]);
						var Valor = parseFloat(myarray[""+data[i]["VendedorID"]+""]["Valor"]);
						
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"width:9%;\">";
						table += "			Vendedor";
						table += "		</td>";
						table += "		<td style=\"width:36%;\">";
						table += "			<input type=\"text\" style=\"width: 250px; height: 20px; text-align:left;\" class=\"myinput\" readonly value=\""+data[i]["VendedorID"]+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:9%;\">";
						table += "			Total Valor";
						table += "		</td>";
						table += "		<td style=\"width:28%;\">";
						table += "			<input type=\"text\" style=\"width: 180px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:9%;\">";
						table += "			Total Peso";
						table += "		</td>";
						table += "		<td style=\"width:28%;\">";
						table += "			<input type=\"text\" style=\"width: 180px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" Kg\"/>";
						table += "		</td>";
						table += "	</tr>";
						table += "</table>";
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
						table += "			Fecha";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Interno";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Remision";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Factura";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:16%;\">";
						table += "			Cliente";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
						table += "			F. Pago";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
						table += "			Valor";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Peso";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
						table += "			Dig.";
						table += "		</td>";
						table += "	</tr>";
					}
					
					if (data[i]["ClienteID"] != undefined && data[i]["ClienteID"] != "")
					{
						var Valor = parseFloat(data[i]["Valor"]);
						var Peso = parseFloat(data[i]["Peso"]);
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+SetFormattedDate(data[i]["Fecha"]);
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Interno"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Remision"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Factura"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+ClienteData[data[i]["ClienteID"]];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[i]["F_Pago"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" Kg";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["DigitadorID"];
						table += "		</td>";
						table += "	</tr>";
					}
				}
			}
			else if ("<?php echo $OrderBy; ?>" == "Vehiculo")
			{
				for (var i = 0; i < len; i++)
				{
					if (Viejo_ID != data[i]["Vehiculo"] && data[i]["Vehiculo"] != undefined && data[i]["ClienteID"] != "")
					{
						var array = {};
						array["Peso"] = TmpPeso;
						array["Valor"] = TmpValor;
						
						if (i == 0)
							myarray[""+data[i]["Vehiculo"]+""] = array;
						else
							myarray[""+data[i-1]["Vehiculo"]+""] = array;
						
						Viejo_ID = data[i]["Vehiculo"];
						TmpPeso = data[i]["Peso"];
						TmpValor = data[i]["Valor"];
					}
					else if (Viejo_ID == data[i]["Vehiculo"] && data[i]["Vehiculo"] != undefined && data[i]["ClienteID"] != "")
					{
						TmpPeso = + TmpPeso + parseFloat(data[i]["Peso"]);
						TmpValor = + TmpValor + parseFloat(data[i]["Valor"]);
					}
					
					if (len - i == 1)
					{
						var array = {};
						array["Peso"] = TmpPeso;
						array["Valor"] = TmpValor;
						
						myarray[""+data[i]["Vehiculo"]+""] = array;
					}
				}
				
				Viejo_ID = "_empty_";
					
				for (var i = 0; i < len; i++)
				{
					if (Viejo_ID != data[i]["Vehiculo"] && data[i]["Vehiculo"] != "")
					{
						Viejo_ID = data[i]["Vehiculo"];
						if (i != 0)
							table += "</table><br />";
						
						var Peso = parseFloat(myarray[""+data[i]["Vehiculo"]+""]["Peso"]);
						var Valor = parseFloat(myarray[""+data[i]["Vehiculo"]+""]["Valor"]);
						
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"width:9%;\">";
						table += "			Vehiculo";
						table += "		</td>";
						table += "		<td style=\"width:36%;\">";
						table += "			<input type=\"text\" style=\"width: 250px; height: 20px; text-align:left;\" class=\"myinput\" readonly value=\""+data[i]["Vehiculo"]+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:9%;\">";
						table += "			Total Valor";
						table += "		</td>";
						table += "		<td style=\"width:28%;\">";
						table += "			<input type=\"text\" style=\"width: 180px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+"\"/>";
						table += "		</td>";
						table += "		<td style=\"width:9%;\">";
						table += "			Total Peso";
						table += "		</td>";
						table += "		<td style=\"width:28%;\">";
						table += "			<input type=\"text\" style=\"width: 180px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\""+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" Kg\"/>";
						table += "		</td>";
						table += "	</tr>";
						table += "</table>";
						table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
						table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
						table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
						table += "			Fecha";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Interno";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Remision";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Factura";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:16%;\">";
						table += "			Cliente";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
						table += "			F. Pago";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
						table += "			Valor";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
						table += "			Peso";
						table += "		</td>";
						table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
						table += "			Dig.";
						table += "		</td>";
						table += "	</tr>";
					}
					
					if (data[i]["ClienteID"] != undefined && data[i]["ClienteID"] != "")
					{
						var Valor = parseFloat(data[i]["Valor"]);
						var Peso = parseFloat(data[i]["Peso"]);
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+SetFormattedDate(data[i]["Fecha"]);
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Interno"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Remision"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Factura"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+ClienteData[data[i]["ClienteID"]];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "			"+data[i]["F_Pago"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			"+Peso.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" Kg";
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["DigitadorID"];
						table += "		</td>";
						table += "	</tr>";
					}
				}
			}
			
			table += "</table>";
			
			var ValorTotal = parseFloat(data[0]["ValorTotal"]);
			var PesoTotal = parseFloat(data[0]["PesoTotal"]);
			
			$("#total_peso").val(PesoTotal.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' })+" Kg");
			$("#total_valor").val("$ "+ValorTotal.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
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
			Reportes Facturacion
		</li>
		<li style="padding:2px 0px; margin-left: 180px;">
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
	<div align="center" style="width: 900px; border-bottom: #A9A9A9 1px solid; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
		Total General
	</div>
	<table class="table1" cellspacing="1" cellpadding="1" style="text-align:center; width: 500px; margin-left: 200px;">
		<tr>
			<td>
				Fecha Inicio
			</td>
			<td>
				Fecha Fin
			</td>

			<td>
				Total Peso
			</td>
			<td>
				Total Valor
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
				<input type="text" id="total_peso" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_valor" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 900px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<div id="mid">
	<!--
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:8%;">
					Cliente ID
				</td>
				<td style="width:18%;">
					<input type="text" style="width: 120px; height: 20px;" class="myinput" readonly/>
				</td>
				<td style="width:9%;">
					Cliente
				</td>
				<td style="width:36%;">
					<input type="text" style="width: 250px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td style="width:9%;">
					Total Peso
				</td>
				<td style="width:18%;">
					<input type="text" style="width: 120px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td style="width:9%;">
					Total Valor
				</td>
				<td style="width:18%;">
					<input type="text" style="width: 120px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:10%;">
					Fecha
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Interno
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Remision
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Factura
				</td>
				<td style="border-bottom:grey 1px solid; width:6%;">
					F. Pago
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Valor
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Peso
				</td>
				<td style="border-bottom:grey 1px solid; width:7%;">
					Vend.
				</td>
				<td style="border-bottom:grey 1px solid; width:7%;">
					Cobr.
				</td>
				<td style="border-bottom:grey 1px solid; width:7%;">
					Dig.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					11-Sep-2015
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					FER000552
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Efectivo
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					9.999.999.999.99 Kg
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					SISTEMA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					SISTEMA
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					SISTEMA
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