<?php
	session_start();
	require_once('../modulos/config.php');
	require('fpdf.php');
	$Interno = "";
	if (isset($_GET['Interno'])){
		$Interno = $_GET['Interno'];
	}
	
	class PDF extends FPDF
	{
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';

	function WriteHTML($html)
	{
		// Intérprete de HTML
		$html = str_replace("\n",' ',$html);
		$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e)
		{
			if($i%2==0)
			{
				// Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,$e);
			}
			else
			{
				// Etiqueta
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else
				{
					// Extraer atributos
					$a2 = explode(' ',$e);
					$tag = strtoupper(array_shift($a2));
					$attr = array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])] = $a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}

	function OpenTag($tag, $attr)
	{
		// Etiqueta de apertura
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF = $attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}

	function CloseTag($tag)
	{
		// Etiqueta de cierre
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF = '';
	}

	function SetStyle($tag, $enable)
	{
		// Modificar estilo y escoger la fuente correspondiente
		$this->$tag += ($enable ? 1 : -1);
		$style = '';
		foreach(array('B', 'I', 'U') as $s)
		{
			if($this->$s>0)
				$style .= $s;
		}
		$this->SetFont('',$style);
	}

	function PutLink($URL, $txt)
	{
		// Escribir un hiper-enlace
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
	}

	$html = '
<link rel="stylesheet" href="sty.css" type="text/css" />
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
});
</script>
<style type="text/css">
	#header
	{
		width: 686px;
		height: 40px;
	}
	
	#table1
	{
		width: 685px;
	}
	
	#table2
	{
		width: 685px;
	}
	
	#table3
	{
		width: 655px;
	}
	
	#bottom{
		width: 655px;
	}
</style>
<div>
	<div id="header" class="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 0px;">
			&nbsp;
		</li>
		<li style="padding:2px 0px; margin-left: 155px;">
			<table  cellspacing="0" cellpadding="0" style="font-size:14px; text-align:center;">
				<tr>
					<td>
						Orden de Compra
					</td>
					<td>
						Remisi&oacute;n
					</td>
					<td>
						Factura
					</td>
					<td>
						Fecha
					</td>
				</tr>
				<tr>
					<td style="padding-right:3px;">
						<input type="text" id="orden_compra" class="myinput" style="text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="remision" class="myinput" style="text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="factura" class="myinput" style="text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="fecha" class="myinput" style="text-align:center;" readonly/>
					</td>
				</tr>
			</table>
		</li>
	</div>
	<table id="table1" class="table1" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				Cliente
			</td>
			<td>
				<input type="text" id="cliente" style="width: 458px;" class="myinput" readonly/>
			</td>
			<td>
				NIT
			</td>
			<td>
				<input type="text" id="cliente_ID" style="width: 110px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Beneficiario
			</td>
			<td>
				<input type="text" id="beneficiario" style="width: 458px;" class="myinput" readonly/>
			</td>
			<td>
				Id Benef.
			</td>
			<td>
				<input type="text" id="beneficiario_ID" style="width: 110px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Direcci&oacute;n
			</td>
			<td>
				<input type="text" id="direccion" style="width: 458px;" class="myinput" readonly/>
			</td>
			<td>
				Tel&eacute;fono
			</td>
			<td>
				<input type="text" id="telefono" style="width: 110px;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<!--<table class="table2" cellspacing="0" cellpadding="3">
			<tr style="background: #DEDEDE; font-size: 14px;">
				<td style="border-bottom:grey 1px solid; width:12%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:48%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Cantidad
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Unitario
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					SubTotal
				</td>
			</tr>
			<tr>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					12312312-2
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					Producto prueba con un nombre muy, muy, muy largo 1
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					99999.99
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					$99999999.99
				</td>
				<td style="border-bottom:grey 1px solid;">
					$999999999999.99
				</td>
			</tr>
		</table>-->
	</div>
	<table id="table3" class="table3" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				Observaciones
			</td>
			<td colspan="5" rowspan="2">
				<textarea readonly rows="2" cols="30" id="observaciones" maxlength="200" style="resize:none; width:357px; font-size:10px;"></textarea>
			</td>
			<td>
				SubTotal
			</td>
			<td>
				<input type="text" id="subtotal" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<div id="tipo_servicio1">Servicios</div>
			</td>
			<td>
				<input type="text" id="tipo_servicio2" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Dir. Entrega
			</td>
			<td colspan="5">
				<input type="text" id="direccion_entrega" style="width:357px;" class="myinput" readonly/>
			</td>
			<td>
				IVA
			</td>
			<td>
				<input type="text" id="iva" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Sector
			</td>
			<td colspan="3">
				<input type="text" id="sector" style="width:250px;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				<input type="text" id="ruta" style="width:107px;" class="myinput" readonly/>
			</td>
			<td>
				<div id="tipo_descuento" style="width:148px;">Descuento</div>
			</td>
			<td>
				<input type="text" id="descuento" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Conductor
			</td>
			<td colspan="3">
				<input type="text" id="conductor" style="width:250px;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				<input type="text" id="placa" style="width:107px;" class="myinput" readonly/>
			</td>
			<td>
				TOTAL
			</td>
			<td>
				<input type="text" id="total" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<input type="text" id="estado" style="width:140px;" class="myinput" readonly/>
			</td>
			<td>
				Impr.
			</td>
			<td>
				<input type="text" id="impreso_por" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Mod.
			</td>
			<td>
				<input type="text" id="modificado_por" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Despachado
			</td>
			<td>
				<input type="text" id="fecha_despacho" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Forma Pago
			</td>
			<td>
				<input type="text" id="forma_pago" style="width:140px;" class="myinput" readonly/>
			</td>
			<td>
				Vend.
			</td>
			<td>
				<input type="text" id="vendedor" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Desp.
			</td>
			<td>
				<input type="text" id="despachado_por" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Impreso
			</td>
			<td>
				<input type="text" id="fecha_impreso" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		<tr>
			<td>
				O. Producci&oacute;n
			</td>
			<td>
				<input type="text" id="orden_produccion" style="width:140px; background-color:lightblue;" class="myinput" readonly/>
			</td>
			<td>
				Peso
			</td>
			<td>
				<input type="text" id="peso" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				R. C.
			</td>
			<td>
				<input type="text" id="caja_recibo" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Modificado
			</td>
			<td>
				<input type="text" id="fecha_modificado" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		</tr>
	</table>
	<table id="table3" class="table3" cellspacing="0" cellpadding="1">
		<tr>
			<td style="padding:0px; text-align:center;">
				Edades de cartera al día (no incluyen presente pedido)
			</td>
		</tr>
	</table>
	<table id="table3" class="table3" cellspacing="0" cellpadding="1">
		<tr>
			<td style="background:#AAA; padding:2px 0px; text-align:center;">
				Deuda Total
			</td>
			<td style="background:#CFE6C0; padding:2px 0px; text-align:center;">
				Corriente
			</td>
			<td style="background:#E6DFC0; padding:2px 0px; text-align:center;">
				30 a 45
			</td>
			<td style="background:#E6CFC0; padding:2px 0px; text-align:center;">
				45 a 60
			</td>
			<td style="background:#E2A0A0; padding:2px 0px; text-align:center;">
				60 a 90
			</td>
			<td style="background:#D45555; padding:2px 0px; text-align:center;">
				mas de 90
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="deuda_total" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_corriente" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_30" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_60" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_90" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_mas90" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<p>
		"Autorizo a <?php echo $COMPANY_NAME; ?> para que recolecte, almacene, use y administre mis datos personales,
		los cuales han sido suministrados con la finalidad de desarrollar<br />sus operaciones comerciales,
		de conformidad con lo establecido en la Politica de Tratamiento de Datos Personales,
		Ley 1581/2012 y el decreto 1377/2013<br />disponible en nuestra p&aacute;gina web <?php echo $COMPANY_WEB; ?>"
		<br /><br />
		Firma: __________________________________ c.c ____________________
		</p>
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>
		';

	$pdf = new PDF();
	// Primera página
	$pdf->AddPage();
	$pdf->SetFont('Arial','',20);
	$pdf->Write(5,'Para saber qué hay de nuevo en este tutorial, pulse ');
	$pdf->SetFont('','U');
	$link = $pdf->AddLink();
	$pdf->Write(5,'aquí',$link);
	$pdf->SetFont('');
	// Segunda página
	$pdf->AddPage();
	$pdf->SetLink($link);
	//$pdf->Image('logo.png',10,12,30,0,'','http://www.fpdf.org');
	//$pdf->SetLeftMargin(45);
	$pdf->SetFontSize(14);
	$pdf->WriteHTML($html);
	$pdf->Output();
?>
<link rel="stylesheet" href="sty.css" type="text/css" />
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
	
	if ("<?php echo $Interno; ?>" != "")
	{
		var ClienteID = "";
		$.ajax({
			dataType: 'json',
			url: "../modulos/datos.php",
			data: {"Imprimir_Factura":"<?php echo $Interno; ?>"},
			async: true,
			success: function (data, status, xhr)
			{
				var TotalPeso = 0;
				var table = "";
				table += "<table id=\"table2\" class=\"table2\" cellspacing=\"0\" cellpadding=\"0\">";
				table += "	<tr style=\"background: #DEDEDE; font-size: 10px;\">";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:12%;\">";
				table += "			Codigo";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:43%;\">";
				table += "			Producto";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:10%;\">";
				table += "			Cantidad";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:15%;\">";
				table += "			Unitario";
				table += "		</td>";
				table += "		<td style=\"border-bottom:#A9A9A9 1px solid; width:20%;\">";
				table += "			SubTotal";
				table += "		</td>";
				table += "	</tr>";
				
				var len = data.length;
				for (var i = 0; i < len; i++)
				{
					TotalPeso = TotalPeso + parseFloat(data[i]["Peso"]);
					if (i == 0)
					{
						$("#orden_compra").val(data[i]["Ord_Compra"]);
						$("#remision").val(data[i]["Remision"]);
						$("#factura").val(data[i]["Factura"]);
						$("#fecha").val(data[i]["Fecha"]);
						//
						ClienteID = data[i]["ClienteID"];
						$("#cliente").val(data[i]["Cliente"]);
						$("#cliente_ID").val(data[i]["ClienteID"]);
						$("#beneficiario").val(data[i]["Beneficiario"]);
						$("#beneficiario_ID").val(data[i]["BeneficiarioID"]);
						$("#direccion").val(data[i]["Direccion"]);
						$("#telefono").val(data[i]["Telefono"]);
						//
						$("#observaciones").val(data[i]["Observaciones"]);
						$("#subtotal").val("$ "+data[i]["SubTotal2"]);
						if (data[i]["TipoServicio1"] == "")
							$("#tipo_servicio1").html("Servicios");
						else
							$("#tipo_servicio1").html(data[i]["TipoServicio1"]);
						
						$("#tipo_servicio2").val("$ "+data[i]["TipoServicio2"]);
						$("#direccion_entrega").val(data[i]["Direccion_Entrega"]);
						$("#iva").val("$ "+data[i]["IVA"]);
						if (data[i]["TipoDescuento1"] == "")
							$("#tipo_descuento").html("Descuento");
						else
							$("#tipo_descuento").html(data[i]["TipoDescuento1"]);
						
						$("#descuento").val("$ "+data[i]["TipoDescuento2"]);
						$("#sector").val(data[i]["Sector"]);
						$("#ruta").val(data[i]["Ruta"]);
						$("#total").val("$ "+data[i]["Total"]);
						$("#conductor").val(data[i]["Conductor"]);
						$("#placa").val(data[i]["Placa"]);
						$("#fecha_despacho").val(data[i]["Fecha_Despacho"]);
						$("#estado").val(data[i]["Estado"]);
						$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
						$("#modificado_por").val(data[i]["Modificado_Por"]);
						$("#fecha_impreso").val(FormatedDate());
						$("#forma_pago").val(data[i]["Forma_Pago"]);
						$("#vendedor").val(data[i]["Vendedor"]);
						$("#despachado_por").val(data[i]["Despachado_Por"]);
						$("#fecha_modificado").val(data[i]["Fecha_Modificado"]);
						$("#orden_produccion").val(data[i]["Ord_Produccion"]);
						$("#caja_recibo").val(data[i]["Caja_Recibo"]);
					}
					
					table += "<tr>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "		"+data[i]["Codigo"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left;\">";
					table += "		"+data[i]["Nombre"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
					table += "		"+data[i]["Cantidad"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+data[i]["Unitario"];
					table += "	</td>";
					table += "	<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
					table += "		$ "+data[i]["SubTotal"];
					table += "	</td>";
					table += "</tr>";
				}
				table += "</table>";
				
				$("#peso").val(TotalPeso.toFixed(2)+" Kg.");
				
				$("#mid").html(table);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert(textStatus+ " - " +errorThrown);
			}
		});
		
		var ClientWait = setInterval(function()
		{
			if (ClienteID != "")
			{
				$.ajax({
					dataType: 'json',
					datafields: [
						{ name: 'DeudaTotal', type: 'decimal'},
						{ name: 'Corriente', type: 'decimal'},
						{ name: 'De30a45', type: 'decimal'},
						{ name: 'De45a60', type: 'decimal'},
						{ name: 'De60a90', type: 'decimal'},
						{ name: 'Mas90', type: 'decimal'},
					],
					data: {"Cargar_Cartera":ClienteID},
					url: "../modulos/datos.php",
					async: true,
					success: function (data, status, xhr)
					{
						var DeudaTotal = parseFloat(data[0]["DeudaTotal"]);
						var DeudaCorriente = parseFloat(data[0]["Corriente"]);
						var Deuda30 = parseFloat(data[0]["De30a45"]);
						var Deuda60 = parseFloat(data[0]["De45a60"]);
						var Deuda90 = parseFloat(data[0]["De60a90"]);
						var DeudaMas90 = parseFloat(data[0]["Mas90"]);
						
						$("#deuda_total").val("$ "+DeudaTotal.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
						$("#deuda_corriente").val("$ "+DeudaCorriente.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
						$("#deuda_30").val("$ "+Deuda30.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
						$("#deuda_60").val("$ "+Deuda60.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
						$("#deuda_90").val("$ "+Deuda90.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
						$("#deuda_mas90").val("$ "+DeudaMas90.toLocaleString('es-ES', { minimumFractionDigits: '2', maximumFractionDigits: '2' }));
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						alert(textStatus+ " - " +errorThrown);
					}
				});
				clearInterval(ClientWait);
			}
		},50);
	}
	
	$("#print_button").bind('click', function ()
	{
		$("#fecha_impreso").val(FormatedDate());
		window.print()
	});
});
</script>
<style type="text/css">
	#header
	{
		width: 686px;
		height: 40px;
	}
	
	#table1
	{
		width: 685px;
	}
	
	#table2
	{
		width: 685px;
	}
	
	#table3
	{
		width: 655px;
	}
	
	#bottom{
		width: 655px;
	}
</style>
<div>
	<div id="header" class="header">
		<li>
			<img src="../images/logo.png" alt="Logo" width="110" height="30" style="padding:5px 0px 5px 5px;">
		</li>
		<li style="padding:7px 0px; margin-left: 0px;">
			&nbsp;
		</li>
		<li style="padding:2px 0px; margin-left: 155px;">
			<table  cellspacing="0" cellpadding="0" style="font-size:14px; text-align:center;">
				<tr>
					<td>
						Orden de Compra
					</td>
					<td>
						Remisi&oacute;n
					</td>
					<td>
						Factura
					</td>
					<td>
						Fecha
					</td>
				</tr>
				<tr>
					<td style="padding-right:3px;">
						<input type="text" id="orden_compra" class="myinput" style="text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="remision" class="myinput" style="text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="factura" class="myinput" style="text-align:center;" readonly/>
					</td>
					<td>
						<input type="text" id="fecha" class="myinput" style="text-align:center;" readonly/>
					</td>
				</tr>
			</table>
		</li>
	</div>
	<table id="table1" class="table1" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				Cliente
			</td>
			<td>
				<input type="text" id="cliente" style="width: 458px;" class="myinput" readonly/>
			</td>
			<td>
				NIT
			</td>
			<td>
				<input type="text" id="cliente_ID" style="width: 110px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Beneficiario
			</td>
			<td>
				<input type="text" id="beneficiario" style="width: 458px;" class="myinput" readonly/>
			</td>
			<td>
				Id Benef.
			</td>
			<td>
				<input type="text" id="beneficiario_ID" style="width: 110px;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Direcci&oacute;n
			</td>
			<td>
				<input type="text" id="direccion" style="width: 458px;" class="myinput" readonly/>
			</td>
			<td>
				Tel&eacute;fono
			</td>
			<td>
				<input type="text" id="telefono" style="width: 110px;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div id="mid">
		<!--<table class="table2" cellspacing="0" cellpadding="3">
			<tr style="background: #DEDEDE; font-size: 14px;">
				<td style="border-bottom:grey 1px solid; width:12%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:48%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Cantidad
				</td>
				<td style="border-bottom:grey 1px solid; width:10%;">
					Unitario
				</td>
				<td style="border-bottom:grey 1px solid; width:20%;">
					SubTotal
				</td>
			</tr>
			<tr>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					12312312-2
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					Producto prueba con un nombre muy, muy, muy largo 1
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					99999.99
				</td>
				<td style="border-bottom:grey 1px solid; border-right:grey 1px solid;">
					$99999999.99
				</td>
				<td style="border-bottom:grey 1px solid;">
					$999999999999.99
				</td>
			</tr>
		</table>-->
	</div>
	<table id="table3" class="table3" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				Observaciones
			</td>
			<td colspan="5" rowspan="2">
				<textarea readonly rows="2" cols="30" id="observaciones" maxlength="200" style="resize:none; width:357px; font-size:10px;"></textarea>
			</td>
			<td>
				SubTotal
			</td>
			<td>
				<input type="text" id="subtotal" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<div id="tipo_servicio1">Servicios</div>
			</td>
			<td>
				<input type="text" id="tipo_servicio2" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Dir. Entrega
			</td>
			<td colspan="5">
				<input type="text" id="direccion_entrega" style="width:357px;" class="myinput" readonly/>
			</td>
			<td>
				IVA
			</td>
			<td>
				<input type="text" id="iva" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Sector
			</td>
			<td colspan="3">
				<input type="text" id="sector" style="width:250px;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				<input type="text" id="ruta" style="width:107px;" class="myinput" readonly/>
			</td>
			<td>
				<div id="tipo_descuento" style="width:148px;">Descuento</div>
			</td>
			<td>
				<input type="text" id="descuento" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Conductor
			</td>
			<td colspan="3">
				<input type="text" id="conductor" style="width:250px;" class="myinput" readonly/>
			</td>
			<td colspan="2">
				<input type="text" id="placa" style="width:107px;" class="myinput" readonly/>
			</td>
			<td>
				TOTAL
			</td>
			<td>
				<input type="text" id="total" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Estado
			</td>
			<td>
				<input type="text" id="estado" style="width:140px;" class="myinput" readonly/>
			</td>
			<td>
				Impr.
			</td>
			<td>
				<input type="text" id="impreso_por" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Mod.
			</td>
			<td>
				<input type="text" id="modificado_por" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Despachado
			</td>
			<td>
				<input type="text" id="fecha_despacho" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		<tr>
			<td>
				Forma Pago
			</td>
			<td>
				<input type="text" id="forma_pago" style="width:140px;" class="myinput" readonly/>
			</td>
			<td>
				Vend.
			</td>
			<td>
				<input type="text" id="vendedor" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Desp.
			</td>
			<td>
				<input type="text" id="despachado_por" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Impreso
			</td>
			<td>
				<input type="text" id="fecha_impreso" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		<tr>
			<td>
				O. Producci&oacute;n
			</td>
			<td>
				<input type="text" id="orden_produccion" style="width:140px; background-color:lightblue;" class="myinput" readonly/>
			</td>
			<td>
				Peso
			</td>
			<td>
				<input type="text" id="peso" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				R. C.
			</td>
			<td>
				<input type="text" id="caja_recibo" style="width:84px;" class="myinput" readonly/>
			</td>
			<td>
				Modificado
			</td>
			<td>
				<input type="text" id="fecha_modificado" style="width:120px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
		</tr>
	</table>
	<table id="table3" class="table3" cellspacing="0" cellpadding="1">
		<tr>
			<td style="padding:0px; text-align:center;">
				Edades de cartera al día (no incluyen presente pedido)
			</td>
		</tr>
	</table>
	<table id="table3" class="table3" cellspacing="0" cellpadding="1">
		<tr>
			<td style="background:#AAA; padding:2px 0px; text-align:center;">
				Deuda Total
			</td>
			<td style="background:#CFE6C0; padding:2px 0px; text-align:center;">
				Corriente
			</td>
			<td style="background:#E6DFC0; padding:2px 0px; text-align:center;">
				30 a 45
			</td>
			<td style="background:#E6CFC0; padding:2px 0px; text-align:center;">
				45 a 60
			</td>
			<td style="background:#E2A0A0; padding:2px 0px; text-align:center;">
				60 a 90
			</td>
			<td style="background:#D45555; padding:2px 0px; text-align:center;">
				mas de 90
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="deuda_total" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_corriente" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_30" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_60" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_90" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
			<td>
				<input type="text" id="deuda_mas90" class="myinput" style="width: 112px; text-align:right;" readonly/>
			</td>
		</tr>
	</table>
	<div id="bottom" align="center">
		<p>
		"Autorizo a <?php echo $COMPANY_NAME; ?> para que recolecte, almacene, use y administre mis datos personales,
		los cuales han sido suministrados con la finalidad de desarrollar<br />sus operaciones comerciales,
		de conformidad con lo establecido en la Politica de Tratamiento de Datos Personales,
		Ley 1581/2012 y el decreto 1377/2013<br />disponible en nuestra p&aacute;gina web <?php echo $COMPANY_WEB; ?>"
		<br /><br />
		Firma: __________________________________ c.c ____________________
		</p>
		<input type="button" id="print_button" value="Imprimir"/>
	</div>
</div>