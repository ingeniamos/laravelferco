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
	
	if ("<?php echo $Interno; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {"Nomina_Prestamos_Modificar":"<?php echo $Interno; ?>"},
			success: function (data, status, xhr)
			{
				var Valor = parseFloat(data[0]["Valor"]);
				var Valor_Cuotas = parseFloat(data[0]["Valor_Cuotas"]);
				
				$("#interno").val("<?php echo $Interno; ?>");
				$("#tipo_mov").html(data[0]["TipoMov"]);
				$("#valor").val("$ "+Valor.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#cuotas").val(parseInt(data[0]["Cuotas"]));
				$("#valor_cuotas").val("$ "+Valor_Cuotas.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
				$("#fpago").val(data[0]["Forma_Pago"]);
				$("#acreedor").val(ClienteData[data[0]["AcreedorID"]]);
				$("#acreedor_id").val(data[0]["AcreedorID"]);
				$("#beneficiario").val(ClienteData[data[0]["BeneficiarioID"]]);
				$("#beneficiario_id").val(data[0]["BeneficiarioID"]);
				$("#observaciones").val(data[0]["Observacion"]);
				$("#estado").val(data[0]["Estado"]);
				$("#digitado_por").val(data[0]["Digitado_Por"]);
				$("#fecha_digitado").val(data[0]["Fecha_Digitado"]);
				$("#aprobado_por").val(data[0]["Aprobado_Por"]);
				$("#modificado_por").val(data[0]["Modificado_Por"]);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
	}
	
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
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
	
	.table{
		width: 250px;
		font-family:calibri;
		margin-top: 10px;
		text-align: left;
		font-size: 12px;
	}
	
	#bottom{
		font-family:calibri;
		font-size: 12px;
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
		.table{
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
		<li id="tipo_mov" style="width:250px; padding:7px 0px; margin-left: 20px; text-align:center;">
			Novedades
		</li>
		<li style="padding:2px 0px; margin-left: 20px;">
			<table  cellspacing="0" cellpadding="0" style="font-size:12px; text-align:center;">
				<tr>
					<td>
						Interno
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
	<table class="table" cellspacing="2" cellpadding="2">
		<tr>
			<td>
				Valor
			</td>
			<td colspan="8" style="list-style:none;">
				<li style="float:left; margin: 0px 5px 0px 0px;">
					<input type="text" id="valor" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</li>
				<li style="float:left; margin: 4px 5px 0px 3px;">
					Cuotas
				</li>
				<li style="float:left; margin: 0px 5px 0px 3px;">
					<input type="text" id="cuotas" style="width: 40px; height: 20px; text-align:center;" class="myinput" readonly/>
				</li>
				<li style="float:left; margin: 4px 5px 0px 3px;">
					Valor Cuotas
				</li>
				<li style="float:left; margin: 0px 5px 0px 3px;">
					<input type="text" id="valor_cuotas" style="width: 150px; height: 20px; text-align:right;" class="myinput" readonly/>
				</li>
				<li style="float:left; margin: 4px 5px 0px 3px;">
					F. Pago
				</li>
				<li style="float:left; margin: 0px 5px 0px 3px;">
					<input type="text" id="fpago" style="width: 80px; height: 20px; text-align:center;" class="myinput" readonly/>
				</li>
			</li>
		</tr>
		<tr>
			<td style="padding-right:5px;">
				Beneficiario
			</td>
			<td colspan="6">
				<input type="text" id="beneficiario" style="width: 400px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td style="padding-left:10px; padding-right:10px;">
				Cedula
			</td>
			<td>
				<input type="text" id="beneficiario_id" style="width: 150px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="padding-right:5px;">
				Acreedor
			</td>
			<td colspan="6">
				<input type="text" id="acreedor" style="width: 400px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td style="padding-left:10px; padding-right:10px;">
				NIT
			</td>
			<td>
				<input type="text" id="acreedor_id" style="width: 150px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td style="padding-right:5px;">
				Observ.
			</td>
			<td colspan="9" rowspan="2">
				<textarea readonly rows="3" cols="86" id="observaciones" maxlength="200" style="resize:none;"></textarea>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<input type="text" id="estado" style="width:80px;" class="myinput" readonly/>
			</td>
			<td>
				Dig.
			</td>
			<td>
				<input type="text" id="digitado_por" style="width:70px;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="fecha_digitado" style="width:120px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Apr.
			</td>
			<td>
				<input type="text" id="aprobado_por" style="width:70px;" class="myinput" readonly/>
			</td>
			<td colspan="2" style="list-style:none;">
				<li style="float:left; margin: 4px 5px 0px 3px;">
					Mod.
				</li>
				<li>
					<input type="text" id="modificado_por" style="width:70px;" class="myinput" readonly/>
				</li>
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<p style="text-align:left; font-size:12px;">
		<br /><br />
		Autoriza: __________________________________
		<br /><br /><br /><br />
		</p>
		<p style="text-align:center; font-size:12px;">
		Firma: __________________________________ c.c ____________________
		</p>
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
<br /><br />