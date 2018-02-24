<?php
	session_start();
	$Caja_Interno = "";
	if (isset($_GET['Caja_Interno'])){
		$Caja_Interno = $_GET['Caja_Interno'];
	}
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
	
	if ("<?php echo $Caja_Interno; ?>" != "")
	{
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {
				"Imprimir_Caja_Recibo":true,
				"Caja_Interno":"<?php echo $Caja_Interno; ?>",
			},
			async: true,
			success: function (data, status, xhr)
			{
				$("#fecha").val(SetFormattedDate(data[0]["Fecha"]));
				$("#cliente").val(data[0]["Cliente"]);
				$("#caja_recibo").val(data[0]["Caja_Recibo"]);
				$("#concepto").val(data[0]["Aplicado_A"]);
				$("#observaciones").val(data[0]["Observaciones"]);
				$("#caja_interno").val("<?php echo $Caja_Interno; ?>");
				$("#estado").val(data[0]["Estado"]);
				$("#categoria").val(data[0]["Categoria"]);
				$("#grupo").val(data[0]["Grupo"]);
				$("#subgrupo").val(data[0]["SubGrupo"]);
				$("#subgrupo2").val(data[0]["SubGrupo2"]);
				$("#descuento_por").val(data[0]["Descuento_Concepto"]);
				$("#efectivo").val("$ "+data[0]["Efectivo"]);
				$("#bancos").val("$ "+data[0]["Bancos"]);
				$("#cheques").val("$ "+data[0]["Cheques"]);
				$("#rete_iva").val("$ "+data[0]["Rete_IVA"]);
				$("#rete_ica").val("$ "+data[0]["Rete_ICA"]);
				$("#rete_fuente").val("$ "+data[0]["Rete_Fuente"]);
				$("#descuento").val("$ "+data[0]["Descuento"]);
				$("#valor_total").val("$ "+data[0]["Valor_Total"]);
				$("#digitado_por").val(data[0]["Digitado_Por"]);
				//$("#digitado_fecha").val(SetFormattedDate(data[0]["Fecha_Digitado"]));
				$("#aprobado_por").val(data[0]["Aprobado_Por"]);
				//$("#aprobado_fecha").val(SetFormattedDate(data[0]["Fecha_Aprobado"]));
				if (data[0]["Categoria"] == "Ingresos")
					$("#titulo").html("Comprobante de Ingreso");
				else
					$("#titulo").html("Comprobante de Egreso");
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
		padding: 2px;
	}
	
	.mytable{
		width: 710px;
		font-family:calibri;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 12px;
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
		<li style="padding:7px 0px; margin-left: 105px;">
			<div id="titulo">Comprobante de Ingreso</div>
		</li>
		<li style="padding:2px 0px; margin-left: 95px;">
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
	<table class="mytable" cellspacing="1" cellpadding="1">
		<tr>
			<td>
				Fecha RC
			</td>
			<td>
				<input type="text" id="fecha" style="width: 90px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Beneficiario
			</td>
			<td colspan="4">
				<input type="text" id="cliente" style="width: 472px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Cons. Mov.
			</td>
			<td>
				<input type="text" id="caja_interno" style="width: 90px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
			<td>
				Concepto
			</td>
			<td rowspan="3">
				<textarea rows="5" cols="25" id="concepto" maxlength="200" style="resize:none; width:200px;" readonly></textarea>
			</td>
			<td style="padding-right:20px;">
				Observac.
			</td>
			<td rowspan="3">
				<textarea rows="5" cols="25" id="observaciones" maxlength="200" style="resize:none; width:199px;" readonly></textarea>
			</td>
		</tr>
		<tr>
			<td>
				Recibo
			</td>
			<td>
				<input type="text" id="caja_recibo" style="width: 90px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<input type="text" id="estado" style="width: 90px; height: 20px; text-align:center;" class="myinput" readonly/>
			</td>
		</tr>
		
		<tr>
			<td>
				Categoria
			</td>
			<td colspan="2">
				<input type="text" id="categoria" style="width: 150px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Efectivo
			</td>
			<td>
				<input type="text" id="efectivo" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Grupo
			</td>
			<td colspan="2">
				<input type="text" id="grupo" style="width: 150px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Bancos
			</td>
			<td>
				<input type="text" id="bancos" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				SubGrupo
			</td>
			<td colspan="2">
				<input type="text" id="subgrupo" style="width: 150px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Cheques
			</td>
			<td>
				<input type="text" id="cheques" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				<p style="width:80px;">SubGrupo Niv.2</p>
			</td>
			<td colspan="2">
				<input type="text" id="subgrupo2" style="width: 150px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Rete IVA
			</td>
			<td>
				<input type="text" id="rete_iva" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Descuento
			</td>
			<td colspan="2">
				<input type="text" id="descuento_por" style="width: 150px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				Rete ICA
			</td>
			<td>
				<input type="text" id="rete_ica" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Digitado Por
			</td>
			<td>
				<input type="text" id="digitado_por" style="width: 90px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
			<td>
				Rete Fuente
			</td>
			<td>
				<input type="text" id="rete_fuente" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Aprobado Por
			</td>
			<td>
				<input type="text" id="digitado_por" style="width: 90px; height: 20px; text-align:left;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				&nbsp;
			</td>
			<td>
				Descuentos
			</td>
			<td>
				<input type="text" id="descuento" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				&nbsp;
			</td>
			<td>
				Valor Total
			</td>
			<td>
				<input type="text" id="valor_total" style="width: 200px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
			<td>
				_________________________________
			</td>
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="3">
				&nbsp;
			</td>
			<td>
				Recib&iacute; Conforme - C.C.
			</td>
			<td colspan="2">
				&nbsp;
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>