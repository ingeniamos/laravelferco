<?php
	session_start();
	//---
	$Clientes = isset($_GET["Clientes"]) ? $_GET["Clientes"]:"";
?>
<script type="text/javascript" src="../jqwidgets-ver3.8.2/scripts/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function ()
{
	var Clientes = "<?php echo $Clientes; ?>";
	Clientes = Clientes.split(",");
	
	var Desc1 = "";
	var Desc2 = "";
	
	$.ajax(
	{
		dataType: "json",
		url: "../modulos/parametros.php",
		data: {"Nomina_Carnet":true},
		async: false,
		success: function (data)
		{
			if (data[0] != undefined)
			{
				Desc1 = data[0]["DESC1"];
				Desc2 = data[0]["DESC2"];
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert(textStatus+ " - " +errorThrown);
		}
	});
	
	var currenttime = "<?php print date("F d, Y H:i:s", time())?>";
	
	function SetFormattedDate (MyDate)
	{
		var MonthArray = new Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre")
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
		
		//var FormatedDate = day + "-" + month + "-" +  year;
		var FormatedDate = month + " " + day + " de " + year;
		return FormatedDate;
	};
	
	$.ajax({
		dataType: "json",
		url: "../modulos/datos.php",
		data: {"Nomina_Carnet":true},
		success: function (data, status, xhr)
		{
			var table = "";
			
			if (data[0]["ClienteID"] != undefined)
			{
				for (var i = 0; i < data.length; i++)
				{
					for (var a = 0; a < (Clientes.length - 1); a++)
					{
						if (data[i]["ClienteID"] == Clientes[a])
						{
							table += "<table class=\"table1\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
							table += "	<tr>";
							table += "		<td rowspan=\"9\" style=\"width: 5px;\">";
							table += "		</td>";
							table += "		<td colspan=\"6\" style=\"width: 5px; height: 5px;\">";
							table += "		</td>";
							table += "		<td rowspan=\"9\" style=\"width: 5px;\">";
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr>";
							table += "		<td colspan=\"5\" style=\"text-align:left;\">";
							table += "			<img src=\"../images/logo.png\" alt=\"Logo\" style=\"padding:5px 0px 5px 5px; width:4cm;\">";
							table += "		</td>";
							table += "		<td rowspan=\"5\" style=\"width:2.8cm; background-color:lightgray; border: #666 1px solid;\">";
							if (data[i]["Imagen"] != "")
								table += "			<img src=\"../images/"+data[i]["Imagen"]+"\" style=\"width:107px; height:132px;\">";
							else
								table += "			&nbsp;";
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr>";
							table += "		<td colspan=\"4\" rowspan=\"3\" style=\"text-align: left; padding:0px 5px; font-size: 14px; font-weight: 700;\">";
							table += "			"+data[i]["Cliente"].toUpperCase();
							table += "		</td>";
							table += "		<td rowspan=\"3\" style=\"width:1cm; height:1cm; background-color:red; color: #FFF;\">";
							table += "			R. H. <span style=\"font-weight:700;\">"+data[i]["RH"]+"</span>";
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr>";
							table += "	</tr>";
							table += "	<tr>";
							table += "	</tr>";
							table += "	<tr>";
							table += "		<td style=\"width:20px; text-align:left; padding-left:5px;\">";
							table += "			C.C.";
							table += "		</td>";
							table += "		<td colspan=\"4\" style=\"text-align:left; padding-left:5px;\">";
							table += "			"+data[i]["ClienteID"];
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr>";
							table += "		<td colspan=\"6\" style=\"height:5px;\">";
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr>";
							table += "		<td colspan=\"6\" style=\"color: #FFF; font-weight:700; background-color:gray; border: #666 1px solid;\">";
							table += "			"+data[i]["Cargo"].toUpperCase();
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr>";
							table += "		<td colspan=\"6\">";
							table += "			Vencimiento: "+SetFormattedDate(data[i]["Fecha"]);
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
							table += "<table class=\"table2\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
							table += "	<tr>";
							table += "		<td style=\"padding: 0px 20px; height: 4cm;\">";
							table += "			"+Desc1;
							table += "		</td>";
							table += "	</tr>";
							table += "	<tr>";
							table += "		<td>";
							table += "			"+Desc2;
							table += "		</td>";
							table += "	</tr>";
							table += "</table>";
						}
					}
				}
			}
			$("#mid").html(table);
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			alert(textStatus+ " - " +errorThrown);
		}
	});
	
	$("#print_button").on("click", function ()
	{
		window.print();
	});
});
</script>
<style type="text/css">
	.table1{
		width: 8.6cm;
		height: 5cm;
		font-family:calibri;
		text-align: center;
		color:#333;
		font-size: 13px;
		border: #808080 1px solid;
		float: left;
	}
	
	.table2{
		width: 8.6cm;
		height: 5cm;
		font-family:calibri;
		text-align: center;
		color:#333;
		font-size: 13px;
		border: #808080 1px solid;
		float: left;
	}
	
	#bottom{
		font-family:calibri;
		margin: 10px 0px;
		width: 18cm;
		float:left;
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
		margin: 0.5cm 0.5cm 0.5cm 0.7cm;
	}
</style>
<div style="width: 18cm;">
	<div id="mid" style="width: 18cm;">
		<!--<table class="table1" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td rowspan="9" style="width: 5px;">
				</td>
				<td colspan="6" style="width: 5px; height: 5px;">
				</td>
				<td rowspan="9" style="width: 5px;">
				</td>
			</tr>
			<tr>
				<td colspan="5" style="text-align:left;">
					<img src="../images/logo.png" alt="Logo" style="padding:5px 0px 5px 5px; width:4cm;">
				</td>
				<td rowspan="5" style="width:2.8cm; background-color:lightgray; border: #666 1px solid;">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="4" rowspan="3" style="text-align: left; padding:0px 5px; font-size: 14px; font-weight: 700;">
					JUAN FERNANDO BOTERO GOMEZ
				</td>
				<td rowspan="3" style="width:1cm; height:1cm; background-color:red; color: #FFF;">
					R. H. <span style="font-weight:700;">0+</span>
				</td>
			</tr>
			<tr>
			</tr>
			<tr>
			</tr>
			<tr>
				<td style="width:20px; text-align:left; padding-left:5px;">
					C.C.
				</td>
				<td colspan="4" style="text-align:left; padding-left:5px;">
					17667782
				</td>
			</tr>
			<tr>
				<td colspan="6" style="height:5px;">
				</td>
			</tr>
			<tr>
				<td colspan="6" style="color: #FFF; font-weight:700; background-color:gray; border: #666 1px solid;">
					GERENTE
				</td>
			</tr>
			<tr>
				<td colspan="6">
					Vencimiento: Junio 20 de 2016
				</td>
			</tr>
		</table>
		<table class="table2" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td style="padding: 0px 20px; height: 4cm;">
					Este carné es personal e intransferible, en caso de pérdida por favor hacerlo llegar hasta Ferco Ltda.
				</td>
			</tr>
			<tr>
				<td>
					Jefe de Personal
				</td>
			</tr>
		</table>-->
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />