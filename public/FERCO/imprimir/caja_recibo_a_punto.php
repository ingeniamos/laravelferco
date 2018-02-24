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
		
		var Today = year + "-" + month + "-" + day;
		return Today;
	}
	
	function FormatedTime()
	{
		var MyDate = new Date();
		var seconds = MyDate.getSeconds();
		var minutes = MyDate.getMinutes();
		var hours = MyDate.getHours();
		var am_pm = "";
		
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
		
		if (hours >= 12)
			am_pm = "p.m.";
		else
			am_pm = "a.m.";
		
		hours = hours + "";// to string
		if (hours.length == 1)
		{
			hours = "0" + hours;
		};
		
		//var Now = hours + ":" + minutes + ":" + seconds;
		var Now = hours + ":" + minutes + " " + am_pm;
		return Now;
	}
	
	$("#fecha_impreso").html(SetFormattedDate(FormatedDate()));
	$("#hora_impreso").html(FormatedTime());
	$("#impreso_por").html("<?php echo $_SESSION["UserCode"]; ?>");
	
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
				$("#cliente").html(data[0]["Cliente"]);
				$("#cliente_id").html(data[0]["ClienteID"]);
				$("#caja_recibo").html(data[0]["Caja_Recibo"]);
				$("#caja_interno").html("<?php echo $Caja_Interno; ?>");
				$("#notas").html(data[0]["Aplicado_A"]);
				$("#observaciones").html(data[0]["Observaciones"]);
				$("#categoria").html(data[0]["Categoria"]);
				$("#grupo").html(data[0]["Grupo"]);
				$("#subgrupo").html(data[0]["SubGrupo"]);
				$("#efectivo").html("$ "+data[0]["Efectivo"]);
				$("#bancos").html("$ "+data[0]["Bancos"]);
				$("#cheques").html("$ "+data[0]["Cheques"]);
				//$("#rete_iva").html("$ "+data[0]["Rete_IVA"]); No se necesita?
				$("#rete_ica").html("$ "+data[0]["Rete_ICA"]);
				$("#rete_fuente").html("$ "+data[0]["Rete_Fuente"]);
				$("#descuento").html("$ "+data[0]["Descuento"]);
				$("#valor_numero").html("$ "+data[0]["Valor_Total"]);
				
				var centavos = Math.round(data[0]["Total"] * 100) - (Math.floor(data[0]["Total"]) * 100);
				if (centavos)
					$("#valor_letras").html(getNumberLiteral(data[0]["Total"]).toUpperCase()+" PESOS "+ centavos +"/100 M.L.");
				else
					$("#valor_letras").html(getNumberLiteral(data[0]["Total"]).toUpperCase()+" PESOS 00/100 M.L.");
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
	}
	
	$("#print_button").bind('click', function ()
	{
		$("#fecha_impreso").html(SetFormattedDate(FormatedDate()));
		$("#hora_impreso").html(FormatedTime());
		window.print()
	});
	
	function letras(c, d, u)
	{
		var centenas,decenas,decom;
		var lc = "";
		var ld = "";
		var lu = "";
		
		centenas = eval(c);
		decenas = eval(d);
		decom = eval(u);
		
		switch(centenas)
		{
			case 0:
				lc = "";
			break;
			
			case 1:
				if (decenas == 0 && decom == 0)
					lc = "Cien";
				else
					lc = "Ciento ";
			break;
			
			case 2: lc = "Doscientos ";break;
			case 3: lc = "Trescientos ";break;
			case 4: lc = "Cuatrocientos ";break;
			case 5: lc = "Quinientos ";break;
			case 6: lc = "Seiscientos ";break;
			case 7: lc = "Setecientos ";break;
			case 8: lc = "Ochocientos ";break;
			case 9: lc = "Novecientos ";break;
		}
		
		switch(decenas)
		{
			case 0: ld = "";break;
			
			case 1:
				switch(decom)
				{
					case 0:ld = "Diez";break;
					case 1:ld = "Once";break;
					case 2:ld = "Doce";break;
					case 3:ld = "Trece";break;
					case 4:ld = "Catorce";break;
					case 5:ld = "Quince";break;
					case 6:ld = "Dieciseis";break;
					case 7:ld = "Diecisiete";break;
					case 8:ld = "Dieciocho";break;
					case 9:ld = "Diecinueve";break;
				}
			break;
			
			case 2:ld = "Veinte";break;
			case 3:ld = "Treinta";break;
			case 4:ld = "Cuarenta";break;
			case 5:ld = "Cincuenta";break;
			case 6:ld = "Sesenta";break;
			case 7:ld = "Setenta";break;
			case 8:ld = "Ochenta";break;
			case 9:ld = "Noventa";break;
		}
		
		switch(decom)
		{
			case 0: lu  = "";break;
			case 1: lu = "Un";break;
			case 2: lu = "Dos";break;
			case 3: lu = "Tres";break;
			case 4: lu = "Cuatro";break;
			case 5: lu = "Cinco";break;
			case 6: lu = "Seis";break;
			case 7: lu = "Siete";break;
			case 8: lu = "Ocho";break;
			case 9: lu = "Nueve";break;
		}
		
		if (decenas == 1)
		{
			return lc + ld;
		}
		
		if (decenas == 0 || decom == 0)
		{
			return lc + " " + ld + lu;
		}
		else
		{
			if (decenas == 2)
			{
				ld = "Veinti";
				return lc + ld + lu.toLowerCase();
			}
			else
			{
				return lc + ld + " y " + lu
			}
		}
	}

	function getNumberLiteral(n)
	{
		var m0,cm,dm,um,cmi,dmi,umi,ce,de,un,hlp,decimal;

		if (isNaN(n))
		{
			alert("La Cantidad debe ser un valor Numérico.");
			return null;
		}
		
		m0 = parseInt(n / 1000000000000); rm0 = n % 1000000000000;
		m1 = parseInt(rm0 / 100000000000); rm1 = rm0 % 100000000000;
		m2 = parseInt(rm1 / 10000000000); rm2= rm1 % 10000000000;
		m3 = parseInt(rm2 / 1000000000); rm3 = rm2 % 1000000000;
		cm = parseInt(rm3 / 100000000); r1 = rm3 % 100000000;
		dm = parseInt(r1 / 10000000); r2 = r1 % 10000000;
		um = parseInt(r2 / 1000000); r3 = r2 % 1000000;
		cmi = parseInt(r3 / 100000); r4 = r3 % 100000;
		dmi = parseInt(r4 / 10000); r5 = r4 % 10000;
		umi = parseInt(r5 / 1000); r6 = r5 % 1000;
		ce = parseInt(r6 / 100); r7 = r6 % 100;
		de = parseInt(r7 / 10); r8 = r7 % 10;
		un = parseInt(r8 / 1);
		//r9=r8%1;
		
		if (n >= 1000000000)
		{
			tmp = n.toString();
			s = tmp.length;
			tmp1 = tmp.slice(0, s - 9)
			tmp2 = tmp.slice(s - 9, s);
			
			tmpn1 = getNumberLiteral(tmp1);
			tmpn2 = getNumberLiteral(tmp2);

			if (tmpn1.indexOf("Un") >= 0)
				pred = " Millón ";//pred = " Billón ";
			else
				pred = " Millones ";//pred = " Billones ";
			return tmpn1 + pred + tmpn2;
		}

		if (n >= 1000000)
		{
			mldata = letras(cm, dm, um);
			hlp = mldata.replace("Un", "*");
			if (hlp.indexOf("*") > 0)
			{
				mldata = mldata.replace("Uno", "un");
				mldata += " Millón ";
				//mldata += " Millones ";
			}
			else
			{
				mldata += " Millones ";
				//mldata = "Un Millón ";
			}
			
			mdata = letras(cmi, dmi, umi);
			cdata = letras(ce, de, un);
			
			if(mdata != " ")
			{
				if (n == 1000000)
					mdata = mdata.replace("Uno", "un") + "de";
				else
					mdata = mdata.replace("Uno", "un") + " mil ";
			}
			
			return (mldata + mdata + cdata);
		}
		
		if (n >= 1000)
		{
			mdata = letras(cmi, dmi, umi);
			cdata = letras(ce, de, un);
			hlp = mdata.replace("Un", "*");
			if (hlp.indexOf("*") > 0)
			{
				mdata = mdata.replace("Uno", "un");
				return (mdata + " mil " + cdata);
			}
			else
				return (mdata + " Mil " + cdata);
		}
		
		if (n >= 1)
		{
			return (letras(ce, de, un));
		}
		
		if (n >= 0)
		{
			return " Cero";
		}
		
		return "No disponible";
	}

});
</script>
<style type="text/css">
	.mytable{
		width: 700px;
		font-family:arial;
		margin-top: 10px;
		margin-bottom: 10px;
		text-align: left;
		font-size: 16px;
	}
	
	#bottom{
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
		.mytable{
			-webkit-print-color-adjust: exact;
		}
		#print_button {
			display: none;
		}
	}
	@page 
	{
		margin: 0.5cm 3.0cm 1.0cm 0.5cm;
	}
</style>
<div>
	<table class="mytable" cellspacing="1" cellpadding="0" border="0">
		<tr>
			<td style="width:100px;">
				&nbsp;
			</td>
			<td style="width: 100px;">
				<div id="fecha_impreso">18-feb-2016</div>
			</td>
			<td>
				<div id="impreso_por">JF</div>
			</td>
			<td colspan="3">
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="6" style="height:15px;">
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td colspan="4">
				<div id="cliente">Cliente</div>
			</td>
			<td rowspan="2" style="width:150px;">
				<div id="valor_numero" style="text-align:right; font-weight: bold; font-size: 20px;">$ 0,00</div>
			</td>
		</tr>
		<tr>
			<td style="text-align:right; padding-right:10px;">
				NIT.
			</td>
			<td colspan="4">
				<div id="cliente_id">00000000-0</div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td colspan="4" rowspan="2" style="width: 450px; overflow:hidden;">
				<div id="valor_letras" style="height:45px;">CERO PESOS 00/100 M.L.</div>
			</td>
			<td>
				<div id="efectivo" style="text-align:right;">$ 0,00</div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<div id="cheques" style="text-align:right;">$ 0,00</div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td colspan="4" rowspan="2" style="width: 450px; overflow:hidden;">
				<div id="notas" style="height:45px;">Notas...</div>
			</td>
			<td>
				<div id="bancos" style="text-align:right;">$ 0,00</div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="5">
				&nbsp;
			</td>
			<td>
				<div id="rete_iva" style="text-align:right;">$ 0,00</div>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td colspan="4" rowspan="2" style="width: 450px; overflow:hidden;">
				<div id="observaciones" style="height:45px;">Observaciones</div>
			</td>
			<td>
				<div id="rete_ica" style="text-align:right;">$ 0,00</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="caja_recibo" style="text-align:center;">Recibo Caja</div>
			</td>
			<td>
				<div id="rete_fuente" style="text-align:right;">$ 0,00</div>
			</td>
		</tr>
		<tr>
			<td>
				<div id="caja_interno" style="text-align:center;">Interno Caja</div>
			</td>
			<td>
				<div id="categoria">Categoria</div>
			</td>
			<td>
				<div id="grupo">Grupo</div>
			</td>
			<td>
				<div id="subgrupo">SubGrupo</div>
			</td>
			<td>
				<div id="hora_impreso">00:00 a.m.</div>
			</td>
			<td>
				<div id="descuento" style="text-align:right;">$ 0,00</div>
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>