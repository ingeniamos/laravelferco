<?php
	session_start();
	
	$Costo = isset($_GET['Costo']) ? $_GET['Costo']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Codigo = isset($_GET['Codigo']) ? $_GET['Codigo']:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
	
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
			"Inventario_Listado":true,
			"Categoria":"<?php echo $Categoria; ?>",
			"Grupo":"<?php echo $Grupo; ?>",
			"SubGrupo":"<?php echo $SubGrupo; ?>",
			"Codigo":"<?php echo $Codigo; ?>"
		},
		async: true,
		success: function (data, status, xhr)
		{
			var table = "";
			table += "<table class=\"mytable\" cellspacing=\"0\" cellpadding=\"2\">";
			table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
			table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
			table += "			Codigo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:20%;\">";
			table += "			Producto";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:4%;\">";
			table += "			Und";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
			table += "			Peso";
			table += "		</td>";
			<?php if ($Costo != "") { ?>
			table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
			table += "			Costo";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
			table += "			Ult Costo";
			table += "		</td>";
			<?php } ?>
			table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
			table += "			Lista 1";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
			table += "			Lista 2";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
			table += "			Lista 3";
			table += "		</td>";
			table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
			table += "			Lista 4";
			table += "		</td>";
			table += "	</tr>";
			
			var len = data.length;
			for (var i = 0; i < len; i++)
			{
				table += "<tr>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Codigo"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Nombre"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;\">";
				table += "		"+data[i]["Und"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "		"+data[i]["Peso"];
				table += "	</td>";
				<?php if ($Costo != "") { ?>
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "		$ "+data[i]["Costo"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "		$ "+data[i]["Ult_Costo"];
				table += "	</td>";
				<?php } ?>
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "		$ "+data[i]["Lista1"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "		$ "+data[i]["Lista2"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
				table += "		$ "+data[i]["Lista3"];
				table += "	</td>";
				table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
				table += "		$ "+data[i]["Lista4"];
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
		width: 900px;
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
	
	.mytable{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 900px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 900px;
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
		.mytable{
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
		<li style="padding:7px 0px; margin-left: 250px;">
			Listado de Precios
		</li>
		<li style="padding:2px 0px; margin-left: 200px;">
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
	<!--
		<table class="mytable" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:8%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:4%;">
					Und
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Peso
				</td>
			<?php if ($Costo != "") { ?>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Costo
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Ult Costo
				</td>
			<?php } ?>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Lista 1
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Lista 2
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Lista 3
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Lista 4
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					00000-00
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					Cemento Gris 42.5
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center; overflow:hidden; white-space: nowrap;">
					Kilos
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					42.5
				</td>
			<?php if ($Costo != "") { ?>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.99
				</td>
			<?php } ?>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 99.999.999.99
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