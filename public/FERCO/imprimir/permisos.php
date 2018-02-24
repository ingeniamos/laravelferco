<?php
	session_start();
	$UserID = isset($_GET['UserID']) ? $_GET['UserID']:"";
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
	
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	if ("<?php echo $UserID; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/access.php",
			data: {
				"Usuarios":true, 
				"UserID":"<?php echo $UserID; ?>" 
			},
			success: function (data, status, xhr)
			{
				if (data[0]["UserID"] != "" && data[0]["UserID"] != undefined)
				{
					$("#nombre").val(data[0]["UserName"]);
					$("#id").val(data[0]["UserID"]);
					$("#codigo").val(data[0]["UserCode"]);
					$("#access").val(data[0]["UserLvl"]);
					
					var Check = data[0]["UserActive"];
						if (Check == "true")
							Check = true;
						else
							Check = false;
					$("#activo").attr("checked", Check);
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
		
		$.ajax({
			dataType: 'json',
			url: "../modulos/access.php",
			data: { "Accesos":"<?php echo $UserID; ?>" },
			success: function (data, status, xhr)
			{
				var table = "";
				table += "<table class=\"mytable\" cellspacing=\"0\" cellpadding=\"2\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
				table += "		<td style=\"border-bottom:grey 1px solid; width:25%;\">";
				table += "			Modulo";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:35%;\">";
				table += "			SubModulo";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Guardar";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Modificar";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Supervisor";
				table += "		</td>";
				table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
				table += "			Imprimir";
				table += "		</td>";
				table += "	</tr>";
				
				var len = data.length;
				for (var i = 0; i < len; i++)
				{
					if (data[i]["Modulo"] != "" && data[i]["Modulo"] != undefined)
					{
						if (data[i]["Guardar"] == "true")
							var Guardar = "checked";
						else
							var Guardar = "";
						
						if (data[i]["Modificar"] == "true")
							var Modificar = "checked";
						else
							var Modificar = "";
						
						if (data[i]["Supervisor"] == "true")
							var Supervisor = "checked";
						else
							var Supervisor = "";
						
						if (data[i]["Imprimir"] == "true")
							var Imprimir = "checked";
						else
							var Imprimir = "";
						
						table += "<tr>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
						table += "		"+data[i]["Modulo"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		"+data[i]["SubModulo"];
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		<input type=\"checkbox\" "+Guardar+" onclick=\"return false\"/>";
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		<input type=\"checkbox\" "+Modificar+" onclick=\"return false\"/>";
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
						table += "		<input type=\"checkbox\" "+Supervisor+" onclick=\"return false\"/>";
						table += "	</td>";
						table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:center;\">";
						table += "		<input type=\"checkbox\" "+Imprimir+" onclick=\"return false\"/>";
						table += "	</td>";
						table += "</tr>";
					}
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
		padding 2px;
	}
	
	.table1{
		width: 250px;
		font-family:calibri;
		margin-top: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	.mytable{
		table-layout:fixed;
		font-family:calibri;
		margin-top: 10px;
		width: 700px;
		text-align: center;
		font-size: 12px;
		border-top: #A9A9A9 1px solid;
		border-left: #A9A9A9 1px solid;
		border-right: #A9A9A9 1px solid;
	}
	
	#bottom{
		font-family:calibri;
		margin-top: 5px;
		width: 700px;
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
		<li style="padding:7px 0px; margin-left: 140px;">
			Permisos de Usuario
		</li>
		<li style="padding:2px 0px; margin-left: 85px;">
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
			<td style="text-align:center;">
				Nombre
			</td>
			<td style="text-align:center; padding-left:20px;">
				ID
			</td>
			<td style="text-align:center; padding-left:20px;">
				Codigo
			</td>
			<td style="text-align:center; padding-left:20px;">
				Acceso
			</td>
			<td style="text-align:center; padding-left:12px;">
				Activo
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="nombre" style="width: 330px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td style="padding-left:20px;">
				<input type="text" id="id" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="padding-left:20px;">
				<input type="text" id="codigo" style="width: 50px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="padding-left:20px;">
				<input type="text" id="access" style="width: 100px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td style="padding-left:20px;">
				<input type="checkbox" id="activo" checked onclick="return false" />
			</td>
		</tr>
	</table>
	<div id="mid">
		<table class="mytable" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:25%;">
					Modulo
				</td>
				<td style="border-bottom:grey 1px solid; width:35%;">
					SubModulo
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Guardar
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Modificar
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Supervisor
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Imprimir
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;">
					Ventas
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					Modificar
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					<input type="checkbox" checked onclick="return false" />
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					<input type="checkbox" onclick="return false"/>
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					<input type="checkbox" checked onclick="return false" />
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:center;">
					<input type="checkbox" checked onclick="return false" />
				</td>
			</tr>
		</table>
	</div>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />