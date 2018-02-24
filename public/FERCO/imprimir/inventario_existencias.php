<?php
	session_start();
	//---
	$OrderBy = isset($_GET['OrderBy']) ? $_GET['OrderBy']:"";
	$Categoria = isset($_GET['Categoria']) ? $_GET['Categoria']:"";
	$Grupo = isset($_GET['Grupo']) ? $_GET['Grupo']:"";
	$SubGrupo = isset($_GET['SubGrupo']) ? $_GET['SubGrupo']:"";
	$Supervisor = false;
	$Admin = false;
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
	
	//---
	var Supervisor = false;
	var Admin = false;
	
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Inventario" && $data[$i]["SubModulo"] == "Existencias" && $data[$i]["Supervisor"] == "true")
				{
		?>
					Supervisor = true;
		<?php
					$Supervisor = true;
				}
			}
		}
		else
		{
		?>
			Admin = true;
		<?php	
			$Admin = true;
		}
	} ?>
	
	$("#fecha_impreso").val(FormatedDate());
	$("#impreso_por").val("<?php echo $_SESSION["UserCode"]; ?>");
	
	$.ajax({
		dataType: 'json',
		url: "../modulos/datos_productos.php",
		data: {
			"Imprimir_Inventario_Existencias":true,
			"OrderBy":"<?php echo $OrderBy; ?>",
			"Categoria":"<?php echo $Categoria; ?>",
			"Grupo":"<?php echo $Grupo; ?>",
			"SubGrupo":"<?php echo $SubGrupo; ?>",
		},
		async: true,
		success: function (data, status, xhr)
		{
			if (data[0]["Lista1"] == undefined)
				return;
			
			var len = data.length;
			Vieja_Categoria = "_empty_";
			var table = "";
			var Total_Ult_Costo = 0;
			var Total_Costo_Prom = 0;
			var Total_Lista1 = 0;
			var Total_Lista2 = 0;
			var Total_Lista3 = 0;
			var Total_Lista4 = 0;
			var Ult_Costo = [];
			var Costo_Prom = [];
			var CategoriaLista1 = [];
			var CategoriaLista2 = [];
			var CategoriaLista3 = [];
			var CategoriaLista4 = [];
			
			for (var i = 0; i < len; i++)
			{
				Total_Ult_Costo += parseFloat(data[i]["Ult_Costo"]);
				Total_Costo_Prom += parseFloat(data[i]["Costo_Prom"]);
				Total_Lista1 += parseFloat(data[i]["Lista1"]);
				Total_Lista2 += parseFloat(data[i]["Lista2"]);
				Total_Lista3 += parseFloat(data[i]["Lista3"]);
				Total_Lista4 += parseFloat(data[i]["Lista4"]);
			}
			
			var Value = "<?php echo $OrderBy; ?>";
			switch(Value)
			{
				case "Categoria":
					table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
					table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Categoria";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Lista 1";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Lista 2";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Lista 3";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Lista 4";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Ult. Costo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
					table += "			Costo Prom.";
					table += "		</td>";
					table += "	</tr>";
					
					for (var i = 0; i < len; i++)
					{
						var TmpLista1 = parseFloat(data[i]["Lista1"]);
						var TmpLista2 = parseFloat(data[i]["Lista2"]);
						var TmpLista3 = parseFloat(data[i]["Lista3"]);
						var TmpLista4 = parseFloat(data[i]["Lista4"]);
						var TmpUltC = parseFloat(data[i]["Ult_Costo"]);
						var TmpCProm = parseFloat(data[i]["Costo_Prom"]);
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Categoria"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista1.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista2.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista3.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista4.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpUltC.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpCProm.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "	</tr>";
					}
				break;
				case "Grupo":
					table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
					table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Categoria";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Grupo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
					table += "			Lista 1";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
					table += "			Lista 2";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
					table += "			Lista 3";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
					table += "			Lista 4";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
					table += "			Ult. Costo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:13%;\">";
					table += "			Costo Prom.";
					table += "		</td>";
					table += "	</tr>";
					
					for (var i = 0; i < len; i++)
					{
						var TmpLista1 = parseFloat(data[i]["Lista1"]);
						var TmpLista2 = parseFloat(data[i]["Lista2"]);
						var TmpLista3 = parseFloat(data[i]["Lista3"]);
						var TmpLista4 = parseFloat(data[i]["Lista4"]);
						var TmpUltC = parseFloat(data[i]["Ult_Costo"]);
						var TmpCProm = parseFloat(data[i]["Costo_Prom"]);
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Categoria"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Grupo"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista1.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista2.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista3.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista4.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpUltC.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpCProm.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "	</tr>";
					}
				break;
				case "SubGrupo":
					table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
					table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
					table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
					table += "			Categoria";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			Grupo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:10%;\">";
					table += "			SubGrupo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Lista 1";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Lista 2";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Lista 3";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Lista 4";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Ult. Costo";
					table += "		</td>";
					table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
					table += "			Costo Prom.";
					table += "		</td>";
					table += "	</tr>";
					
					for (var i = 0; i < len; i++)
					{
						var TmpLista1 = parseFloat(data[i]["Lista1"]);
						var TmpLista2 = parseFloat(data[i]["Lista2"]);
						var TmpLista3 = parseFloat(data[i]["Lista3"]);
						var TmpLista4 = parseFloat(data[i]["Lista4"]);
						var TmpUltC = parseFloat(data[i]["Ult_Costo"]);
						var TmpCProm = parseFloat(data[i]["Costo_Prom"]);
						
						table += "	<tr>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Categoria"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["Grupo"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
						table += "			"+data[i]["SubGrupo"];
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista1.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista2.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista3.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpLista4.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpUltC.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
						table += "			$ "+TmpCProm.toLocaleString('es-ES', { minimumFractionDigits: '2' });
						table += "		</td>";
						table += "	</tr>";
					}
				break;
				default:
					for (var i = 0; i < len; i++)
					{
						if (Vieja_Categoria != data[i]["Categoria"])
						{
							Ult_Costo[""+data[i]["Categoria"]+""] = parseFloat(data[i]["Ult_Costo"]);
							Costo_Prom[""+data[i]["Categoria"]+""] = parseFloat(data[i]["Costo_Prom"]);
							CategoriaLista1[""+data[i]["Categoria"]+""] = parseFloat(data[i]["Lista1"]);
							CategoriaLista2[""+data[i]["Categoria"]+""] = parseFloat(data[i]["Lista2"]);
							CategoriaLista3[""+data[i]["Categoria"]+""] = parseFloat(data[i]["Lista3"]);
							CategoriaLista4[""+data[i]["Categoria"]+""] = parseFloat(data[i]["Lista4"]);
							Vieja_Categoria = data[i]["Categoria"];
						}
						else
						{
							Ult_Costo[""+data[i]["Categoria"]+""] += parseFloat(data[i]["Ult_Costo"]);
							Costo_Prom[""+data[i]["Categoria"]+""] += parseFloat(data[i]["Costo_Prom"]);
							CategoriaLista1[""+data[i]["Categoria"]+""] += parseFloat(data[i]["Lista1"]);
							CategoriaLista2[""+data[i]["Categoria"]+""] += parseFloat(data[i]["Lista2"]);
							CategoriaLista3[""+data[i]["Categoria"]+""] += parseFloat(data[i]["Lista3"]);
							CategoriaLista4[""+data[i]["Categoria"]+""] += parseFloat(data[i]["Lista4"]);
						}
					}
					
					Vieja_Categoria = "_empty_";
					for (var i = 0; i < len; i++)
					{
						if (Vieja_Categoria != data[i]["Categoria"])
						{
							Vieja_Categoria = data[i]["Categoria"];
							
							if (i != 0)
								table += "</table><br />";
							
							var TmpL1 = CategoriaLista1[""+data[i]["Categoria"]+""];
							var TmpL2 = CategoriaLista2[""+data[i]["Categoria"]+""];
							var TmpL3 = CategoriaLista3[""+data[i]["Categoria"]+""];
							var TmpL4 = CategoriaLista4[""+data[i]["Categoria"]+""];
							var UltC = Ult_Costo[""+data[i]["Categoria"]+""];
							var CProm = Costo_Prom[""+data[i]["Categoria"]+""];
							
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\" style=\"margin-top:10px;\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							if (Admin == true || Supervisor == true)
							{
								table += "		<td style=\"width:150px;\">";
								table += "			Categoria";
								table += "		</td>";
								table += "		<td>";
								table += "			Lista 1";
								table += "		</td>";
								table += "		<td>";
								table += "			Lista 2";
								table += "		</td>";
								table += "		<td>";
								table += "			Lista 3";
								table += "		</td>";
								table += "		<td>";
								table += "			Lista 4";
								table += "		</td>";
								table += "		<td>";
								table += "			Ult. Costo";
								table += "		</td>";
								table += "		<td>";
								table += "			Costo Prom.";
								table += "		</td>";
							}
							else
							{
								table += "		<td style=\"text-align: left; width: 50px;\">";
								table += "			Categoria";
								table += "		</td>";
								table += "		<td style=\"text-align: left;\">";
								table += "			<input type=\"text\" id=\"categoria\" style=\"width: 200px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Categoria"]+"\"/>";
								table += "		</td>";
							}
							table += "	</tr>";
							
							if (Admin == true || Supervisor == true)
							{
								table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
								table += "		<td>";
								table += "			<input type=\"text\" id=\"categoria\" style=\"width: 150px; height: 20px;\" class=\"myinput\" readonly value=\""+data[i]["Categoria"]+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" id=\"categoria_total_l1\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+TmpL1.toLocaleString('es-ES', { minimumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" id=\"categoria_total_l2\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+TmpL2.toLocaleString('es-ES', { minimumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" id=\"categoria_total_l3\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+TmpL3.toLocaleString('es-ES', { minimumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" id=\"categoria_total_l4\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+TmpL4.toLocaleString('es-ES', { minimumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" id=\"categoria_total_ult_costo\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+UltC.toLocaleString('es-ES', { minimumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "		<td>";
								table += "			<input type=\"text\" id=\"categoria_total_costo_prom\" style=\"width: 120px; height: 20px; text-align:right;\" class=\"myinput\" readonly value=\"$ "+CProm.toLocaleString('es-ES', { minimumFractionDigits: '2' })+"\"/>";
								table += "		</td>";
								table += "	</tr>";
							}
							
							table += "</table>";
							table += "<table class=\"table2\" cellspacing=\"0\" cellpadding=\"2\">";
							table += "	<tr style=\"background: #DEDEDE; font-size: 12px;\">";
							if (Admin == true || Supervisor == true)
							{
								table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
								table += "			Codigo";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:15%;\">";
								table += "			Producto";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
								table += "			Existencias";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
								table += "			Lista 1";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
								table += "			Lista 2";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
								table += "			Lista 3";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
								table += "			Lista 4";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
								table += "			Ult. Costo";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
								table += "			Costo Prom.";
								table += "		</td>";
							}
							else
							{
								table += "		<td style=\"border-bottom:grey 1px solid; width:11%;\">";
								table += "			Codigo";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:27%;\">";
								table += "			Producto";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:8%;\">";
								table += "			Existencias";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:6%;\">";
								table += "			Unidad";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
								table += "			Lista 1";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
								table += "			Lista 2";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
								table += "			Lista 3";
								table += "		</td>";
								table += "		<td style=\"border-bottom:grey 1px solid; width:12%;\">";
								table += "			Lista 4";
								table += "		</td>";
							}
							table += "	</tr>";
						}
					
						var TmpLista1 = parseFloat(data[i]["Lista1"]);
						var TmpLista2 = parseFloat(data[i]["Lista2"]);
						var TmpLista3 = parseFloat(data[i]["Lista3"]);
						var TmpLista4 = parseFloat(data[i]["Lista4"]);
						var TmpUltC = parseFloat(data[i]["Ult_Costo"]);
						var TmpCProm = parseFloat(data[i]["Costo_Prom"]);
						
						table += "	<tr>";
						if (Admin == true || Supervisor == true)
						{
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["Codigo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["Existencia"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpLista1.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpLista2.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpLista3.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpLista4.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpUltC.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpCProm.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
						}
						else
						{
							var TmpLista1 = parseFloat(data[i]["Lista1_Unitaria"]);
							var TmpLista2 = parseFloat(data[i]["Lista2_Unitaria"]);
							var TmpLista3 = parseFloat(data[i]["Lista3_Unitaria"]);
							var TmpLista4 = parseFloat(data[i]["Lista4_Unitaria"]);
						
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["Codigo"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;\">";
							table += "			"+data[i]["Nombre"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["Existencia"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;\">";
							table += "			"+data[i]["Unidad"];
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpLista1.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpLista2.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpLista3.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
							table += "		<td style=\"border-bottom:#A9A9A9 1px solid; text-align:right;\">";
							table += "			$ "+TmpLista4.toLocaleString('es-ES', { minimumFractionDigits: '2' });
							table += "		</td>";
						}
						table += "	</tr>";
					}
				break;
			}
			
			table += "</table>";
			$("#mid").html(table);
			if (Admin == true || Supervisor == true)
			{
				$("#lista1").val("$ "+Total_Lista1.toLocaleString('es-ES', { minimumFractionDigits: '2' }));
				$("#lista2").val("$ "+Total_Lista2.toLocaleString('es-ES', { minimumFractionDigits: '2' }));
				$("#lista3").val("$ "+Total_Lista3.toLocaleString('es-ES', { minimumFractionDigits: '2' }));
				$("#lista4").val("$ "+Total_Lista4.toLocaleString('es-ES', { minimumFractionDigits: '2' }));
				$("#total_ult_costo").val("$ "+Total_Ult_Costo.toLocaleString('es-ES', { minimumFractionDigits: '2' }));
				$("#total_costo_prom").val("$ "+Total_Costo_Prom.toLocaleString('es-ES', { minimumFractionDigits: '2' }));
			}
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
	
	$("#export_button").bind('click', function ()
	{
		var data = "";
		data += "../modulos/export_xls.php?Inventario_Existencias=true&OrderBy=<?php echo $OrderBy; ?>&Categoria=<?php echo $Categoria; ?>";
		data += "&Grupo=<?php echo $Grupo; ?>&SubGrupo=<?php echo $SubGrupo; ?>";
		window.location = data;
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
	}
	#bottom ul{
		width:220px;
		height:25px;
		padding:0px;
		list-style:none;
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
		<li style="padding:7px 0px; margin-left: 235px;">
			Existencias Inventario
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
	<?php
		if ($Admin == true || $Supervisor == true)
		{
	?>
	<div align="center" style="width: 900px; border-bottom: #A9A9A9 1px solid; font-size:16px; font-weight:700; font-family: calibri; margin-top:10px; padding:5px 0px;">
		Valor Total del Inventario
	</div>
	<table class="table1" cellspacing="1" cellpadding="1" style="text-align: center;">
		<tr>
			<td>
				Lista 1
			</td>
			<td>
				Lista 2
			</td>
			<td>
				Lista 3
			</td>
			<td>
				Lista 4
			</td>
			<td>
				Ult. Costo
			</td>
			<td>
				Costo Prom.
			</td>
		</tr>
		<tr>
			<td>
				<input type="text" id="lista1" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="lista2" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="lista3" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="lista4" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_ult_costo" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
			<td>
				<input type="text" id="total_costo_prom" style="width: 130px; height: 20px; text-align:right;" class="myinput" readonly/>
			</td>
		</tr>
	</table>
	<div align="center" style="width: 900px; border-top: #A9A9A9 1px solid; margin-bottom:10px;">
		&nbsp;
	</div>
	<?php
		}
	?>
	<div id="mid">
		<!-- TOTAL
		<table class="table2" cellspacing="0" cellpadding="2" style="margin-top:10px;">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="width:150px;">
					Categoria
				</td>
				<td>
					Lista 1
				</td>
				<td>
					Lista 2
				</td>
				<td>
					Lista 3
				</td>
				<td>
					Lista 4
				</td>
				<td>
					Ult. Costo
				</td>
				<td>
					Costo Prom.
				</td>
			</tr>
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td>
					<input type="text" id="categoria" style="width: 150px; height: 20px;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="categoria_total_l1" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="categoria_total_l2" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="categoria_total_l3" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="categoria_total_l4" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="categoria_total_ult_costo" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
				<td>
					<input type="text" id="categoria_total_costo_prom" style="width: 100px; height: 20px; text-align:right;" class="myinput" readonly/>
				</td>
			</tr>
		</table>
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:11%;">
					Codigo
				</td>
				<td style="border-bottom:grey 1px solid; width:15%;">
					Producto
				</td>
				<td style="border-bottom:grey 1px solid; width:8%;">
					Existencias
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Lista 1
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Lista 2
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Lista 3
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Lista 4
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Ult. Costo
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Costo Prom.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					1100101101-1
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:center;">
					999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
			</tr>
		</table>
		-->
		<!-- CATEGORIA
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:16%;">
					Categoria
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Lista 1
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Lista 2
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Lista 3
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Lista 4
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Ult. Costo
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Costo Prom.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
			</tr>
		</table>
		-->
		<!-- GRUPO
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:14%;">
					Categoria
				</td>
				<td style="border-bottom:grey 1px solid; width:14%;">
					Grupo
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Lista 1
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Lista 2
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Lista 3
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Lista 4
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Ult. Costo
				</td>
				<td style="border-bottom:grey 1px solid; width:12%;">
					Costo Prom.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
			</tr>
		</table>
		-->
		<!-- SUBGRUPO
		<table class="table2" cellspacing="0" cellpadding="2">
			<tr style="background: #DEDEDE; font-size: 12px;">
				<td style="border-bottom:grey 1px solid; width:11%;">
					Categoria
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Grupo
				</td>
				<td style="border-bottom:grey 1px solid; width12%;">
					SubGrupo
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Lista 1
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Lista 2
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Lista 3
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Lista 4
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Ult. Costo
				</td>
				<td style="border-bottom:grey 1px solid; width:11%;">
					Costo Prom.
				</td>
			</tr>
			<tr>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:left; overflow:hidden; white-space: nowrap;">
					ASDASDASDSADSAD ASDASDASDASDASDASDASDSADSADASDASDSADSADASDAS
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; border-right:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
				<td style="border-bottom:#A9A9A9 1px solid; text-align:right;">
					$ 9.999.999.999.99
				</td>
			</tr>
		</table>
		-->
	</div>
	<div id="bottom" align="center">
		<ul>
			<li>
				<input type="button" id="print_button" value="Imprimir"/>
			</li>
			<li>
				<input type="button" id="export_button" value="Exportar"/>
			</li>
		</ul>
	</div>
</div>
<br /><br />