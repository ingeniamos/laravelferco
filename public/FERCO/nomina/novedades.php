<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
$Interno = isset($_POST['Interno']) ? $_POST['Interno']:"";
if (isset($_POST["Crear_Emergente"]) && $_POST["Crear_Emergente"] == true)
{
		$_SESSION["NumOfID"]++;
	}
$NumOfID = $_SESSION["NumOfID"];
?>
<script type="text/javascript">
$(document).ready(function ()
{// Implementar los permisos, mejorar la ventana de alerta en el guardado y añador el limpiado e imprimir en sus botones
	//----- GOLBALS
	var mytheme = "energyblue";
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	var ClienteHoras = new Array();
	var ClienteData = new Array();
	var NovedadesData = new Array();
	var Descontable = new Array();
	var Remunerado100 = new Array();
	var Remunerado66 = new Array();
	var Cesantia = new Array();
	var ReposicionData = new Array();
	var Reponer = new Array();
	var HorasData = new Array();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Nomina_Content");
	var Body = document.getElementById("Nomina_Novedades_Content");
	var Times = 0;
	var Refresh = 0;
	var Hide = 0;
	
	function CheckRefresh ()
	{
		clearInterval(Refresh);
		Refresh = setInterval(function()
		{
			if (Times <= 0 && (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none")) {
				Times++;
			}
			else if (Times > 0 && jQuery.css( Body, "display" ) === "block" && jQuery.css( Main, "display" ) === "block")
			{
				//Code... do something?
				SystemMap("Novedades", true);
				ReDefine();
				ClearDocument();
				clearInterval(Refresh);
				CheckHide();
			}
		},500);
	};
	
	function CheckHide ()
	{
		clearInterval(Hide);
		Hide = setInterval(function()
		{
			if (jQuery.css( Body, "display" ) === "none" || jQuery.css( Main, "display" ) === "none") {
				CheckRefresh();
				clearInterval(Hide);
			}
		},200);
	};
	// END - Code for Refresh Data
	
	//---
	<?php
	if (isset($_SESSION["UserID"]) && $_SESSION["UserID"] != "")
	{
		$data = $_SESSION["UserAccess"];
		$num = count($data);
		
		if ($data[0]["Lvl"] != "Administrador") {
			for ($i = 0; $i < $num; $i++)
			{
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Novedades" && $data[$i]["Guardar"] == "false")
				{
		?>
					$("#nomina_novedades_guardar<?php echo $NumOfID ?>").jqxButton({ disabled: true });
					$("#nomina_novedades_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Novedades" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#nomina_novedades_imprimir<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		<?php
				}
			}
		}
	} ?>
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"nomina_novedades_interno<?php echo $NumOfID ?>", type:""},
			{id:"nomina_novedades_cliente1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente_ID1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente_ID2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente_ID3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_justificacion<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_comentario<?php echo $NumOfID ?>", type:""},
			{id:"nomina_novedades_novedad<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_fecha_ini<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_fecha_fin<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_hora_ini<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_hora_fin<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_subtotal<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"nomina_novedades_total<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			{id:"nomina_novedades_descontado<?php echo $NumOfID ?>", type:"jqxCheckBox"},
			{id:"nomina_novedades_remunerado100<?php echo $NumOfID ?>", type:"jqxCheckBox"},
			{id:"nomina_novedades_remunerado66<?php echo $NumOfID ?>", type:"jqxCheckBox"},
			{id:"nomina_novedades_cesantia<?php echo $NumOfID ?>", type:"jqxCheckBox"},
			//---
			{id:"nomina_novedades_reposicion<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_fecha<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_hora_ini2<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_hora_fin2<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_total2<?php echo $NumOfID ?>", type:"jqxNumberInput"},
			//---
			{id:"nomina_novedades_items_grid<?php echo $NumOfID ?>", type:"jqxGrid"},
			{id:"nomina_novedades_observacion<?php echo $NumOfID ?>", type:""},
		];
		
		EnableDisableJSON = [
			{id:"nomina_novedades_cliente1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente_ID1<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente_ID2<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_cliente_ID3<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_justificacion<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_comentario<?php echo $NumOfID ?>", type:""},
			{id:"nomina_novedades_novedad<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_fecha_ini<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_fecha_fin<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_hora_ini<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_hora_fin<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			//---
			{id:"nomina_novedades_reposicion<?php echo $NumOfID ?>", type:"jqxComboBox"},
			{id:"nomina_novedades_fecha<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_hora_ini2<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			{id:"nomina_novedades_hora_fin2<?php echo $NumOfID ?>", type:"jqxDateTimeInput"},
			//---
			{id:"nomina_novedades_items_grid<?php echo $NumOfID ?>", type:"jqxGrid"},
			{id:"nomina_novedades_guardar<?php echo $NumOfID ?>", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Locked = false;
		//---
		ClearAll();
		EnableDisableAll(false);
		ClickOK = false;
		ClickCANCEL = false;
	}
	
	function LoadValues()
	{
		var GetValuesSource =
		{
			datatype: "json",
			datafields: [
				{ name: "EmpleadoID", type: 'string'},
				{ name: "ReemplazoID", type: 'string'},
				{ name: "AutorizadorID", type: 'string'},
				{ name: "Justificacion", type: 'string'},
				{ name: "Comentario", type: 'string'},
				{ name: "Novedad", type: 'string'},
				{ name: "Fecha_Ini", type: 'string'},
				{ name: "Fecha_Fin", type: 'string'},
				{ name: "FechaHora_Ini", type: 'string'},
				{ name: "FechaHora_Fin", type: 'string'},
				//---
				{ name: "Reposicion", type: 'string'},
				{ name: "Fecha", type: 'string'},
				{ name: "Hora_Ini", type: 'string'},
				{ name: "Hora_Fin", type: 'string'},
				{ name: "Total", type: 'decimal'},
				{ name: "Observacion", type: 'string'},
			],
			data:{"Nomina_Novedades_Modificar":"<?php echo $Interno ?>"},
			url: "modulos/datos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				var len = records.length;
				
				for (var i = 1; i < len; i++)
				{
					if (records[i]["Fecha"] != undefined)
					{
						var datarow = {
							"Fecha":new Date(SetFormattedDate(records[i]["Fecha"])),
							"HoraIni":From24To12(records[i]["Hora_Ini"]),
							"HoraFin":From24To12(records[i]["Hora_Fin"]),
							"Hora_Ini":records[i]["Hora_Ini"],
							"Hora_Fin":records[i]["Hora_Fin"],
							"Total":records[i]["Total"],
						};
						$("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid("addrow", null, datarow, "first");
					}
				}
				
				$("#nomina_novedades_cliente1<?php echo $NumOfID ?>").val(records[0]["EmpleadoID"]);
				$("#nomina_novedades_cliente2<?php echo $NumOfID ?>").val(records[0]["ReemplazoID"]);
				$("#nomina_novedades_cliente3<?php echo $NumOfID ?>").val(records[0]["AutorizadorID"]);
				$("#nomina_novedades_novedad<?php echo $NumOfID ?>").val(records[0]["Novedad"]);
				
				clearTimeout(Timer1);
				Timer1 = setTimeout(function()
				{
					$("#nomina_novedades_justificacion<?php echo $NumOfID ?>").val(records[0]["Justificacion"]);
				},500);
				
				$("#nomina_novedades_comentario<?php echo $NumOfID ?>").val(records[0]["Comentario"]);
				$("#nomina_novedades_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Ini"])));
				$("#nomina_novedades_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Fin"])));
				$("#nomina_novedades_hora_ini<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedTime(records[0]["FechaHora_Ini"])));
				$("#nomina_novedades_hora_fin<?php echo $NumOfID ?>").jqxDateTimeInput("setDate", new Date(SetFormattedTime(records[0]["FechaHora_Fin"])));
				$("#nomina_novedades_reposicion<?php echo $NumOfID ?>").val(records[0]["Reposicion"]);
				$("#nomina_novedades_observacion<?php echo $NumOfID ?>").val(records[0]["Observacion"]);
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	}
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'},
			{ name: 'Horas', type: 'int'},
		],
		data: {"Nomina_Salarios":true},
		url: "modulos/datos.php",
		async: false
	};
	var ClienteDataAdapter = new $.jqx.dataAdapter(ClienteSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				ClienteHoras[records[i]["ClienteID"]] = records[i]["Horas"];
				ClienteData.push(records[i]);
			}
		}
	});
	
	var NovedadesSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Novedad', type: 'string'},
			{ name: 'Descontable', type: 'bool'},
			{ name: 'Remunerado100', type: 'bool'},
			{ name: 'Remunerado66', type: 'bool'},
			{ name: 'Cesantia', type: 'bool'},
		],
		data: {"Nomina_Novedades":true},
		url: "modulos/parametros.php",
		async: false
	};
	var NovedadesDataAdapter = new $.jqx.dataAdapter(NovedadesSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				Descontable[records[i]["Novedad"]] = records[i]["Descontable"];
				Remunerado100[records[i]["Novedad"]] = records[i]["Remunerado100"];
				Remunerado66[records[i]["Novedad"]] = records[i]["Remunerado66"];
				Cesantia[records[i]["Novedad"]] = records[i]["Cesantia"];
				NovedadesData.push(records[i]);
			}
		}
	});
	
	var ReposicionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Reposicion', type: 'string'},
			{ name: 'Reponer', type: 'bool'},
		],
		data: {"Nomina_Reposicion":true},
		url: "modulos/parametros.php",
		async: false
	};
	var ReposicionDataAdapter = new $.jqx.dataAdapter(ReposicionSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				Reponer[records[i]["Reposicion"]] = records[i]["Reponer"];
				ReposicionData.push(records[i]);
			}
		}
	});
	
	var HorasSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo', type: 'string'},
			{ name: 'Hora_Ini', type: 'string'},
			{ name: 'Hora_Fin', type: 'string'},
		],
		data: {"Nomina_Horario":true},
		url: "modulos/parametros.php",
		async: false
	};
	var HorasDataAdapter = new $.jqx.dataAdapter(HorasSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				if (records[i]["Tipo"] == "Laboral_Dia")
				{
					HorasData["Dia_Ini"] = records[i]["Hora_Ini"];
					HorasData["Dia_Fin"] = records[i]["Hora_Fin"];
				}
				else if (records[i]["Tipo"] == "Laboral_Tarde")
				{
					HorasData["Tarde_Ini"] = records[i]["Hora_Ini"];
					HorasData["Tarde_Fin"] = records[i]["Hora_Fin"];
				}
			}
		}
	});
	
	function From12To24 (Hour)
	{
		var Hour24 = 0;
		var Tmp = Hour.split(/[: ]/);
		if (Tmp[2] == "PM")
		{
			if (Tmp[0] != "12")
			{
				Hour24 = (parseInt(Tmp[0])+12);
				if (Hour24 == 24)
					Hour24 = 0+""+0;
			}
			else
				Hour24 = parseInt(Tmp[0]);
		}
		else
		{
			if (Tmp[0] == "12")
			{
				Hour24 = 0+""+0;
			}
			else
				Hour24 = parseInt(Tmp[0]);
		}
		
		var TotalTime = "" + Hour24 + ":" + Tmp[1];
		
		return TotalTime;
	}
	
	function From24To12 (Hour)
	{
		var Tmp = Hour.split(/[:]/);
		
		if (Tmp[0] > 12)
		{
			var TmpH = Tmp[0]-12;
			if (TmpH < 10)
			{
				TmpH = "0" + TmpH;
			};
			var TotalTime = "" + TmpH + ":" + Tmp[1] + " PM";
		}
		else if (Tmp[0] == 0)
		{
			var TotalTime = "12:" + Tmp[1] + " AM";
		}
		else
		{
			if (Tmp[0].length == 1)
			{
				Tmp[0] = "0" + Tmp[0];
			};
			var TotalTime = "" + Tmp[0] + ":" + Tmp[1] + " AM";
		}
		
		return TotalTime;
	}
	
	function GetHours(Hora_Ini, Hora_Fin, first)
	{
		var IniTmp = Hora_Ini.split(/[:]/);
		var FinTmp = Hora_Fin.split(/[:]/);
		
		var HoraIni = parseInt(IniTmp[0]);
		var MinutoIni = parseInt(IniTmp[1]);
		var HoraFin = parseInt(FinTmp[0]);
		var MinutoFin = parseInt(FinTmp[1]);
		
		var HoraIniTotal = parseFloat(""+HoraIni+"."+MinutoIni).toFixed(2);
		var HoraFinTotal = parseFloat(""+HoraFin+"."+MinutoFin).toFixed(2);
		
		var HoraTotal = 0;
		var MinutoTotal = 0;
		
		if (first)
		{
			// Dia
			var DiaIniTmp = HorasData["Dia_Ini"].split(/[:]/);
			var DiaMinutoIni = parseInt(DiaIniTmp[1]);
			
			var HoraDiaIni = parseFloat(HorasData["Dia_Ini"]).toFixed(2);
			var HoraDiaFin = parseFloat(HorasData["Dia_Fin"]).toFixed(2);
			// Tarde
			var HoraTardeIni = parseFloat(HorasData["Tarde_Ini"]).toFixed(2);
			var HoraTardeFin = parseFloat(HorasData["Tarde_Fin"]).toFixed(2);
			
			if (HoraIniTotal < parseFloat(HoraDiaIni))// 00:00
			{
				MinutoTotal = 0;
				for (;;)
				{
					if (MinutoIni == 60)
					{
						MinutoIni = 0;
						HoraIni++;
						
						if (MinutoIni < 10)
							MinutoIni = "0"+MinutoIni;
					}
					
					var TmpIniTime = ""+HoraIni+"."+MinutoIni;
					var TmpTotalTime = (MinutoTotal / 60).toFixed(2);
					
					var tmp_HoraDiaIni = ""+HoraDiaIni;// can't compare in numbers -> compare in string
					if (TmpIniTime.localeCompare(tmp_HoraDiaIni) == 0)
					{
						//alert("bucle1 - salida1")
						HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
						break;
					}
					
					var tmp_HoraFinTotal = ""+HoraFinTotal;// can't compare in numbers -> compare in string
					if (TmpIniTime.localeCompare(tmp_HoraFinTotal) == 0)
					{
						//alert("bucle1 - salida2")
						HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
						break;
					}
					
					MinutoIni = parseInt(MinutoIni);
					
					MinutoIni++;
					MinutoTotal++;
				}
			}
			
			if (HoraIniTotal >= parseFloat(HoraDiaIni) && HoraIniTotal <= parseFloat(HoraDiaFin))
			{
				MinutoTotal = 0;
				for (;;)
				{
					if (MinutoIni == 60)
					{
						MinutoIni = 0;
						HoraIni++;
						
						if (MinutoIni < 10)
							MinutoIni = "0"+MinutoIni;
					}
					
					var TmpIniTime = ""+HoraIni+"."+MinutoIni;
					var TmpTotalTime = (MinutoTotal / 60).toFixed(2);
					
					var tmp_HoraDiaFin = ""+HoraDiaFin;// can't compare in numbers -> compare in string
					if (TmpIniTime.localeCompare(tmp_HoraDiaFin) == 0)
					{
						//alert("bucle2 - salida1")
						HoraTotal = TmpTotalTime
						HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
						break;
					}
					
					var tmp_HoraFinTotal = ""+HoraFinTotal;// can't compare in numbers -> compare in string
					if (TmpIniTime.localeCompare(tmp_HoraFinTotal) == 0)
					{
						//alert("bucle2 - salida2")
						HoraTotal = TmpTotalTime
						HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
						break;
					}
					
					MinutoIni = parseInt(MinutoIni);
					
					MinutoIni++;
					MinutoTotal++;
				}
			}
			
			if (HoraIniTotal < parseFloat(HoraTardeIni))// 12:00
			{
				MinutoTotal = 0;
				for (;;)
				{
					if (MinutoIni == 60)
					{
						MinutoIni = 0;
						HoraIni++;
						
						if (MinutoIni < 10)
							MinutoIni = "0"+MinutoIni;
					}
					
					var TmpIniTime = ""+HoraIni+"."+MinutoIni;
					var TmpTotalTime = (MinutoTotal / 60).toFixed(2);
					
					var tmp_HoraTardeIni = ""+HoraTardeIni;// can't compare in numbers -> compare in string
					if (TmpIniTime.localeCompare(tmp_HoraTardeIni) == 0)
					{
						//alert("bucle3 - salida1")
						HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
						break;
					}
					
					var tmp_HoraFinTotal = ""+HoraFinTotal;// can't compare in numbers -> compare in string
					if (TmpIniTime.localeCompare(tmp_HoraFinTotal) == 0)
					{
						//alert("bucle3 - salida2")
						HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
						break;
					}
					
					MinutoIni = parseInt(MinutoIni);
					
					MinutoIni++;
					MinutoTotal++;
				}
			}
			
			if (HoraIniTotal >= parseFloat(HoraTardeIni) && HoraIniTotal <= parseFloat(HoraTardeFin))
			{
				MinutoTotal = 0;
				for (;;)
				{
					if (MinutoIni == 60)
					{
						MinutoIni = 0;
						HoraIni++;
						
						if (MinutoIni < 10)
							MinutoIni = "0"+MinutoIni;
					}
					
					var TmpIniTime = ""+HoraIni+"."+MinutoIni;
					var TmpTotalTime = (MinutoTotal / 60).toFixed(2);
					
					var tmp_HoraTardeFin = ""+HoraTardeFin;// can't compare in numbers -> compare in string
					if (TmpIniTime.localeCompare(tmp_HoraTardeFin) == 0)
					{
						//alert("bucle4 - salida1")
						HoraTotal = (+HoraTotal + +TmpTotalTime).toFixed(2);
						break;
					}
					
					var tmp_HoraFinTotal = ""+HoraFinTotal;// can't compare in numbers -> compare in string
					if (TmpIniTime.localeCompare(tmp_HoraFinTotal) == 0)
					{
						//alert("bucle4 - salida2")
						HoraTotal = (+HoraTotal + +TmpTotalTime).toFixed(2);
						break;
					}
					
					MinutoIni = parseInt(MinutoIni);
					
					MinutoIni++;
					MinutoTotal++;
				}
			}
			return HoraTotal;
		}
		else
		{
			for (;;)
			{
				if (MinutoIni == 60)
				{
					MinutoIni = 0;
					HoraIni++;
					
					if (MinutoIni < 10)
						MinutoIni = "0"+MinutoIni;
				}
				
				if (HoraIni >= 24)
				{
					HoraIni = 0;
				}
				
				var TmpIniTime = ""+HoraIni+"."+MinutoIni;
				var TmpTotalTime = (MinutoTotal / 60).toFixed(2);
				
				var tmp_HoraFinTotal = ""+HoraFinTotal;// can't compare in numbers -> compare in string
				if (TmpIniTime.localeCompare(tmp_HoraFinTotal) == 0)
				{
					HoraTotal = TmpTotalTime;
					break;
				}
				
				MinutoIni = parseInt(MinutoIni);
				
				MinutoIni++;
				MinutoTotal++;
			}
			
			return HoraTotal;
		}
	}
	
	function GenerateData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var cData = Calc();
		var GridArray = [];
		
		if (cData["Hora_Ini1"] != "")
		{
			var datarow = {
				"Fecha":new Date(SetFormattedDate(cData["Fecha1"])),
				"HoraIni":From24To12(cData["Hora_Ini1"]),
				"HoraFin":From24To12(cData["Hora_Fin1"]),
				"Hora_Ini":cData["Hora_Ini1"],
				"Hora_Fin":cData["Hora_Fin1"],
				"Total":GetHours(cData["Hora_Ini1"], cData["Hora_Fin1"]),
			};
			//alert(JSON.stringify(datarow))
			GridArray.push(datarow);
		}
		
		if (cData["Hora_Ini2"] != "")
		{
			var datarow = {
				"Fecha":new Date(SetFormattedDate(cData["Fecha2"])),
				"HoraIni":From24To12(cData["Hora_Ini2"]),
				"HoraFin":From24To12(cData["Hora_Fin2"]),
				"Hora_Ini":cData["Hora_Ini2"],
				"Hora_Fin":cData["Hora_Fin2"],
				"Total":GetHours(cData["Hora_Ini2"], cData["Hora_Fin2"]),
			};
			//alert(JSON.stringify(datarow))
			GridArray.push(datarow);
		}
		
		$("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid("addrow", null, GridArray, "first");
		Locked = false;
	}
	
	function Calc(first)
	{
		if (first)
		{
			var Empleado = $("#nomina_novedades_cliente1<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
			var FechaIni = GetFormattedDate($("#nomina_novedades_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
			var FechaFin = GetFormattedDate($("#nomina_novedades_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
			var Hora_Ini = $("#nomina_novedades_hora_ini<?php echo $NumOfID ?>").jqxDateTimeInput('getText');
			Hora_Ini = From12To24(Hora_Ini);
			var Hora_Fin = $("#nomina_novedades_hora_fin<?php echo $NumOfID ?>").jqxDateTimeInput('getText');
			Hora_Fin = From12To24(Hora_Fin);
			
			if (FechaIni == FechaFin)
			{
				//alert(GetHours(Hora_Ini, Hora_Fin, true))
				if (!Empleado)
				{
					$("#nomina_novedades_total<?php echo $NumOfID ?>").val(GetHours(Hora_Ini, Hora_Fin, true));
				}
				else
				{
					var val = GetHours(Hora_Ini, Hora_Fin, true);
					if (parseInt(ClienteHoras[Empleado.value]) < 240)
						val = val / 2;
					$("#nomina_novedades_total<?php echo $NumOfID ?>").val(val);
				}
				
			}
			else if (FechaIni > FechaFin)
			{
				$("#nomina_novedades_total<?php echo $NumOfID ?>").val("0");
			}
			else
			{
				var Total = parseFloat(GetHours(Hora_Ini, "23:00", true))
				
				for (var i = 2;;i++)
				{
					var FechaTmp = new Date(FechaIni);
					FechaTmp.setDate(FechaTmp.getDate() + i);
					var NewFecha = GetFormattedDate(FechaTmp);
					
					if (NewFecha == FechaFin) {
						Total = +Total + +parseFloat(GetHours("01:00", Hora_Fin, true));
						break;
					}
					else
					{
						Total = +Total + +parseFloat(GetHours("01:00", "23:00", true));
					}
				}
				
				if (!Empleado)
				{
					$("#nomina_novedades_total<?php echo $NumOfID ?>").val(Total);
				}
				else
				{
					if (parseInt(ClienteHoras[Empleado.value]) < 240)
						Total = Total / 2;
					$("#nomina_novedades_total<?php echo $NumOfID ?>").val(Total);
				}
			}
		}
		else
		{
			var Fecha = GetFormattedDate($("#nomina_novedades_fecha<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
			var Hora_Ini = $("#nomina_novedades_hora_ini2<?php echo $NumOfID ?>").jqxDateTimeInput('getText');
			Hora_Ini = From12To24(Hora_Ini);
			var Hora_Fin = $("#nomina_novedades_hora_fin2<?php echo $NumOfID ?>").jqxDateTimeInput('getText');
			Hora_Fin = From12To24(Hora_Fin);
			
			var IniTmp = Hora_Ini.split(/[:]/);
			var FinTmp = Hora_Fin.split(/[:]/);
			
			var TotalHoras = (FinTmp[0] - IniTmp[0]);
			var TotalMinutos = ((FinTmp[1] - IniTmp[1]).toFixed(2)/60);
			var Total = TotalHoras + TotalMinutos;
			
			if (Total < 0)
			{
				Total += 24;
				$("#nomina_novedades_total2<?php echo $NumOfID ?>").val(Total);
				var FechaTmp = new Date(Fecha);
				FechaTmp.setDate(FechaTmp.getDate() + 2);
				var NewFecha = GetFormattedDate(FechaTmp);
				
				var Data = {
					"Fecha1":Fecha,
					"Fecha2":NewFecha,
					"Hora_Ini1":Hora_Ini,
					"Hora_Ini2":"00:00",
					"Hora_Fin1":"00:00",
					"Hora_Fin2":Hora_Fin,
				};
				return Data;
			}
			else
			{
				$("#nomina_novedades_total2<?php echo $NumOfID ?>").val(Total);
				var Data = {
					"Fecha1":Fecha,
					"Fecha2":"",
					"Hora_Ini1":Hora_Ini,
					"Hora_Ini2":"",
					"Hora_Fin1":Hora_Fin,
					"Hora_Fin2":"",
				};
				return Data;
			}
		}
	}
	
	$("#nomina_novedades_interno<?php echo $NumOfID ?>").jqxInput(
	{
		theme: mytheme,
		width: 100,
		height: 20,
		disabled: true
	});
	
	$("#nomina_novedades_cliente1<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 290,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Nombre',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#nomina_novedades_cliente1<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_novedades_cliente_ID1<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_novedades_cliente_ID1<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_novedades_cliente_ID1<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#nomina_novedades_cliente_ID1<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_novedades_cliente1<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_novedades_cliente1<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_novedades_cliente2<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 290,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Nombre',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#nomina_novedades_cliente2").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_novedades_cliente_ID2<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_novedades_cliente_ID2<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_novedades_cliente_ID2<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#nomina_novedades_cliente_ID2<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_novedades_cliente2<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_novedades_cliente2<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_novedades_cliente3<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 290,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar Nombre',
		selectedIndex: -1,
		displayMember: 'Nombre',
		valueMember: 'ClienteID'
	});
	$("#nomina_novedades_cliente3<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_novedades_cliente_ID3<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_novedades_cliente_ID3<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_novedades_cliente_ID3<?php echo $NumOfID ?>").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		source: ClienteData,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar ID',
		selectedIndex: -1,
		displayMember: 'ClienteID',
		valueMember: 'ClienteID'
	});
	$("#nomina_novedades_cliente_ID3<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_novedades_cliente3<?php echo $NumOfID ?>").val() != event.args.item.value)
				$("#nomina_novedades_cliente3<?php echo $NumOfID ?>").val(event.args.item.value);
		}
	});
	
	$("#nomina_novedades_novedad<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 200,
		source: NovedadesData,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Novedad',
		valueMember: 'Novedad',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#nomina_novedades_novedad<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			var Desc = Descontable[event.args.item.value] ? "check":"uncheck";
			var R100 = Remunerado100[event.args.item.value] ? "check":"uncheck";
			var R66 = Remunerado66[event.args.item.value] ? "check":"uncheck";
			var Ces = Cesantia[event.args.item.value] ? "check":"uncheck";
			
			$("#nomina_novedades_descontado<?php echo $NumOfID ?>").jqxCheckBox(Desc);
			$("#nomina_novedades_remunerado100<?php echo $NumOfID ?>").jqxCheckBox(R100);
			$("#nomina_novedades_remunerado66<?php echo $NumOfID ?>").jqxCheckBox(R66);
			$("#nomina_novedades_cesantia<?php echo $NumOfID ?>").jqxCheckBox(Ces);
			
			var item = event.args.item.value;
			
			if (item.indexOf("extra") > 0)
				JustificacionSource.data = {"Nomina_Justificacion":true};
			else
				JustificacionSource.data = {"Nomina_Justificacion2":true};
			
			var JustificacionDataAdapter = new $.jqx.dataAdapter(JustificacionSource);
			$("#nomina_novedades_justificacion<?php echo $NumOfID ?>").jqxComboBox({source: JustificacionDataAdapter});
		}
		else
		{
			$("#nomina_novedades_novedad<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#nomina_novedades_descontado<?php echo $NumOfID ?>").jqxCheckBox("uncheck");
			$("#nomina_novedades_remunerado100<?php echo $NumOfID ?>").jqxCheckBox("uncheck");
			$("#nomina_novedades_remunerado66<?php echo $NumOfID ?>").jqxCheckBox("uncheck");
			$("#nomina_novedades_cesantia<?php echo $NumOfID ?>").jqxCheckBox("uncheck");
		}
	});
	
	$("#nomina_novedades_descontado<?php echo $NumOfID ?>").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
		disabled: true
	});
	
	$("#nomina_novedades_remunerado100<?php echo $NumOfID ?>").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
		disabled: true
	});
	
	$("#nomina_novedades_remunerado66<?php echo $NumOfID ?>").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
		disabled: true
	});
	
	$("#nomina_novedades_cesantia<?php echo $NumOfID ?>").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
		disabled: true
	});
	
	var JustificacionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo', type: 'string'},
		],
		//data: {"Nomina_Justificacion":true},
		url: "modulos/parametros.php",
	};
	//var JustificacionDataAdapter = new $.jqx.dataAdapter(JustificacionSource);
	
	$("#nomina_novedades_justificacion<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 170,
		//source: JustificacionDataAdapter,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#nomina_novedades_justificacion<?php echo $NumOfID ?>").bind('change', function (event) {
		if (!event.args)
		{
			$("#nomina_novedades_justificacion<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
		}
	});
	
	$("#nomina_novedades_comentario<?php echo $NumOfID ?>").jqxInput(
	{
		theme: mytheme,
		width: 170,
		height: 20,
		placeHolder: "¿Cual?"
	});
	
	$("#nomina_novedades_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_novedades_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	$("#nomina_novedades_fecha_ini<?php echo $NumOfID ?>").on('change', function (event) 
	{
		if(event.args)
			Calc(true);
	});
	
	$("#nomina_novedades_hora_ini<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_novedades_hora_ini<?php echo $NumOfID ?>").on('change', function (event) 
	{
		if(event.args)
			Calc(true);
	});
	
	$("#nomina_novedades_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_novedades_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	$("#nomina_novedades_fecha_fin<?php echo $NumOfID ?>").on('change', function (event) 
	{
		if(event.args)
			Calc(true);
	});
	
	$("#nomina_novedades_hora_fin<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_novedades_hora_fin<?php echo $NumOfID ?>").on('change', function (event) 
	{
		if(event.args)
			Calc(true);
	});
	
	$("#nomina_novedades_total<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 80,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: ' h',
		symbolPosition: 'right',
		digits: 5,
		min: 0,
		max: 99999,
		disabled: true
	});
	
	//---------------------
	//---------------------
	
	$("#nomina_novedades_reposicion<?php echo $NumOfID ?>").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 150,
		source: ReposicionData,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Reposicion',
		valueMember: 'Reposicion',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#nomina_novedades_reposicion<?php echo $NumOfID ?>").bind('change', function (event) {
		if (event.args)
		{
			var val = Reponer[event.args.item.value] ? false:true;
			
			$("#nomina_novedades_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({ disabled: val});
			$("#nomina_novedades_hora_ini2<?php echo $NumOfID ?>").jqxDateTimeInput({ disabled: val});
			$("#nomina_novedades_hora_fin2<?php echo $NumOfID ?>").jqxDateTimeInput({ disabled: val});
			$("#nomina_novedades_addrow<?php echo $NumOfID ?>").jqxButton({ disabled: val});
		}
		else
		{
			$("#nomina_novedades_reposicion<?php echo $NumOfID ?>").jqxComboBox('clearSelection');
			$("#nomina_novedades_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({ disabled: false});
			$("#nomina_novedades_hora_ini2<?php echo $NumOfID ?>").jqxDateTimeInput({ disabled: false});
			$("#nomina_novedades_hora_fin2<?php echo $NumOfID ?>").jqxDateTimeInput({ disabled: false});
			$("#nomina_novedades_addrow<?php echo $NumOfID ?>").jqxButton({ disabled: false});
		}
	});
	
	$("#nomina_novedades_fecha<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_novedades_fecha<?php echo $NumOfID ?>").jqxDateTimeInput('setDate', new Date(currenttime));
	$("#nomina_novedades_fecha<?php echo $NumOfID ?>").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	$("#nomina_novedades_hora_ini2<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_novedades_hora_ini2<?php echo $NumOfID ?>").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	$("#nomina_novedades_hora_fin2<?php echo $NumOfID ?>").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_novedades_hora_fin2<?php echo $NumOfID ?>").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	
	$("#nomina_novedades_total2<?php echo $NumOfID ?>").jqxNumberInput({
		theme: mytheme,
		height: 20,
		width: 60,
		inputMode: 'simple',
		textAlign: 'right',
		symbol: ' h',
		symbolPosition: 'right',
		digits: 2,
		min: 0,
		max: 99,
		disabled: true
	});
	
	//----------------------
	//----------------------
	
	$("#nomina_novedades_addrow<?php echo $NumOfID ?>").jqxButton({
		width: 70,
		template: "success"
	});
	$("#nomina_novedades_addrow<?php echo $NumOfID ?>").bind('click', function ()
	{
		GenerateData();
	});
	
	var Source =
	{
		datatype: "json",
		datafields: [
			{ name: 'Fecha', type: 'date'},
			{ name: 'HoraIni', type: 'string'},
			{ name: 'HoraFin', type: 'string'},
			{ name: 'Hora_Ini', type: 'date'},
			{ name: 'Hora_Fin', type: 'date'},
			{ name: 'Total', type: 'decimal'},
		],
	};
	var DataAdapter = new $.jqx.dataAdapter(Source);
	
	$("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid(
	{
		theme: mytheme,
		height: 250,
		width: 400,
		source: DataAdapter,
		editable: false,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '7%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('getselectedrowindex');
					var rowscount = $("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('getrowid', selectedrowindex);
						$("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('deleterow', id);
					}
				}
			},
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: "34%",
				height: 20,
				editable: false,
				filtertype: 'date',
				cellsformat: 'dd-MMM-yyyy',
				createeditor: function (row, cellvalue, editor) {
					editor.jqxDateTimeInput({ culture: 'es-ES' });
				}
			},
			{
				text: 'Hora Inicio',
				datafield: 'HoraIni',
				width: "22%",
				height: 20,
				editable: false,
				filterable: false,
			},
			{
				text: 'Hora Final',
				datafield: 'HoraFin',
				width: "22%",
				height: 20,
				editable: false,
				filterable: false,
			},
			{
				text: '',
				datafield: 'Hora_Ini',
				columntype: 'datetimeinput',
				editable: false,
				filterable: false,
				cellsformat: 'HH:mm',
			},
			{
				text: '',
				datafield: 'Hora_Fin',
				columntype: 'datetimeinput',
				editable: false,
				filterable: false,
				cellsformat: 'HH:mm',
			},
			{
				text: 'Total(h)',
				datafield: 'Total',
				width: "15%",
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 2 });
				}
			},
		]
	});
	
	$("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('localizestrings', localizationobj);
	$("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('hidecolumn', 'Hora_Ini');
	$("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('hidecolumn', 'Hora_Fin');
	
	//----------------------------------------------------------------------------------
	//									GUARDAR
	//----------------------------------------------------------------------------------
	function CrearNovedad()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Empleado = $("#nomina_novedades_cliente1<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Reemplazo = $("#nomina_novedades_cliente2<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Autorizador = $("#nomina_novedades_cliente3<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Justificacion = $("#nomina_novedades_justificacion<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Novedad = $("#nomina_novedades_novedad<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var Reposicion = $("#nomina_novedades_reposicion<?php echo $NumOfID ?>").jqxComboBox('getSelectedItem');
		var myarray = new Array();
		
		if (!Empleado)
		{
			Alerts_Box("Debe Seleccionar un Empleado!", 3);
			WaitClick_Combobox("nomina_novedades_cliente1<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Autorizador)
		{
			Alerts_Box("Debe Seleccionar a la persona que Autoriza!", 3);
			WaitClick_Combobox("nomina_novedades_cliente3<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Justificacion)
		{
			Alerts_Box("Debe Seleccionar una Justificacion!", 3);
			WaitClick_Combobox("nomina_novedades_justificacion<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Novedad)
		{
			Alerts_Box("Debe Seleccionar un tipo de Novedad!", 3);
			WaitClick_Combobox("nomina_novedades_novedad<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		
		if (!Reposicion)
		{
			Alerts_Box("Debe Seleccionar un tipo de Reposicion!", 3);
			WaitClick_Combobox("nomina_novedades_reposicion<?php echo $NumOfID ?>");
			Locked = false;
			return;
		}
		else
		{
			if (Reponer[Reposicion.value] == true)
			{
				var datinfo = $("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('getdatainformation');
				var count = datinfo.rowscount;
				
				if (count < 1)
				{
					Alerts_Box("Debe Ingresar almenos un Turno de Reposicion!", 3);
					WaitClick();
					Locked = false;
					return;
				}
				
				var Total = 0;
				for (i = 0; i < count; i++)
				{
					var currentRow = $("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
					Total = Total + currentRow.Total;
				};
				
				for (i = 0; i < count; i++)
				{
					var array = {};
					var currentRow = $("#nomina_novedades_items_grid<?php echo $NumOfID ?>").jqxGrid('getrowdata', i);
					
					array["Fecha"] = GetFormattedDate(currentRow.Fecha);
					array["Hora_Ini"] = currentRow.Hora_Ini;
					array["Hora_Fin"] = currentRow.Hora_Fin;
					array["Total"] = currentRow.Total;
					
					if (i==0)
					{
						array["Empleado"] = Empleado.value;
						array["Reemplazo"] = Reemplazo ? Reemplazo.value:"";
						array["Autorizador"] = Autorizador.value;
						array["Justificacion"] = Justificacion.value;
						array["Comentario"] = $("#nomina_novedades_comentario<?php echo $NumOfID ?>").val();
						array["Novedad"] = Novedad.value;
						array["Descontable"] = $("#nomina_novedades_descontado<?php echo $NumOfID ?>").val();
						array["Remunerado100"] = $("#nomina_novedades_remunerado100<?php echo $NumOfID ?>").val();
						array["Remunerado66"] = $("#nomina_novedades_remunerado66<?php echo $NumOfID ?>").val();
						array["Cesantia"] = $("#nomina_novedades_cesantia<?php echo $NumOfID ?>").val();
						array["Fecha_Ini"] = GetFormattedDate($("#nomina_novedades_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
						array["Fecha_Fin"] = GetFormattedDate($("#nomina_novedades_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
						array["FechaHora_Ini"] = From12To24($("#nomina_novedades_hora_ini<?php echo $NumOfID ?>").jqxDateTimeInput('getText'));
						array["FechaHora_Fin"] = From12To24($("#nomina_novedades_hora_fin<?php echo $NumOfID ?>").jqxDateTimeInput('getText'));
						array["Total_Novedad"] = $("#nomina_novedades_total<?php echo $NumOfID ?>").val();
						array["Total_Reposicion"] = Total;
						array["Reposicion"] = Reposicion.value;
						array["Observacion"] = $("#nomina_novedades_observacion<?php echo $NumOfID ?>").val();
					}
					myarray[i] = array;
				};
			}
			else
			{
				var array = {};
				
				array["Empleado"] = ""+Empleado.value+"";
				array["Reemplazo"] = Reemplazo ? Reemplazo.value:"";
				array["Autorizador"] = Autorizador.value;
				array["Justificacion"] = Justificacion.value;
				array["Comentario"] = $("#nomina_novedades_comentario<?php echo $NumOfID ?>").val();
				array["Novedad"] = Novedad.value;
				array["Descontable"] = $("#nomina_novedades_descontado<?php echo $NumOfID ?>").val();
				array["Remunerado100"] = $("#nomina_novedades_remunerado100<?php echo $NumOfID ?>").val();
				array["Remunerado66"] = $("#nomina_novedades_remunerado66<?php echo $NumOfID ?>").val();
				array["Cesantia"] = $("#nomina_novedades_cesantia<?php echo $NumOfID ?>").val();
				array["Fecha_Ini"] = GetFormattedDate($("#nomina_novedades_fecha_ini<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
				array["Fecha_Fin"] = GetFormattedDate($("#nomina_novedades_fecha_fin<?php echo $NumOfID ?>").jqxDateTimeInput('getDate'));
				array["FechaHora_Ini"] = From12To24($("#nomina_novedades_hora_ini<?php echo $NumOfID ?>").jqxDateTimeInput('getText'));
				array["FechaHora_Fin"] = From12To24($("#nomina_novedades_hora_fin<?php echo $NumOfID ?>").jqxDateTimeInput('getText'));
				array["Total_Novedad"] = $("#nomina_novedades_total<?php echo $NumOfID ?>").val();
				array["Total_Reposicion"] = 0;
				array["Reposicion"] = Reposicion.value;
				array["Observacion"] = $("#nomina_novedades_observacion<?php echo $NumOfID ?>").val();
				
				myarray[0] = array;
			}
		}
		
		/*alert(JSON.stringify(myarray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: 'json',
			url: "modulos/guardar.php",
			data: {"Nomina_Novedades_Crear":myarray},
			async: true,
			success: function (data)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				ClickOK = false;
				ClickCANCEL = false;
				Alerts_Box("Novedad Creada con Exito!<br/>Codigo Interno Generado = \""+data[0]["Interno"]+"\"", 2, true);
				$("#nomina_novedades_interno<?php echo $NumOfID ?>").val(data[0]["Interno"]);
				EnableDisableAll(true);
				Timer1 = setInterval(function()
				{
					if (ClickOK == true)
					{
						ClearDocument();
						clearInterval(Timer1);
						clearTimeout(Timer2);
					}
					else if (ClickCANCEL == true)
					{
						clearInterval(Timer1);
						clearTimeout(Timer2);
						Locked = false;
					}
				},10);
				Timer2 = setTimeout(function()
				{
					$("#Ask_Alert").jqxWindow('close');
					clearInterval(Timer1);
					ClickOK = false;
					ClickCANCEL = true;
					Locked = false;
				},5000);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrio un Error al intentar guardar los datos!\nIntente luego de unos segundos...", 3);
			}
		});
	}
	
	$("#nomina_novedades_guardar<?php echo $NumOfID ?>").jqxButton({
		width: 120,
		template: "info"
	});
	$("#nomina_novedades_guardar<?php echo $NumOfID ?>").bind('click', function ()
	{
		CrearNovedad();
	});
	$("#nomina_novedades_imprimir<?php echo $NumOfID ?>").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#nomina_novedades_imprimir<?php echo $NumOfID ?>").bind('click', function ()
	{
		var data = "imprimir/nom_novedades.php?Interno="+$("#nomina_novedades_interno<?php echo $NumOfID ?>").val()+"";
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	$("#nomina_novedades_nuevo<?php echo $NumOfID ?>").jqxButton({
		width: 120,
		template: "success"
	});
	$("#nomina_novedades_nuevo<?php echo $NumOfID ?>").bind('click', function ()
	{
		ClearDocument();
	});
	
	if ("<?php echo $Interno ?>" != "")
	{
		EnableDisableAll(true);
		$("#nomina_novedades_interno<?php echo $NumOfID ?>").val("<?php echo $Interno ?>");
		$("#nomina_novedades_nuevo<?php echo $NumOfID ?>").jqxButton({ disabled: true });
		LoadValues();
	}
	else
		CheckRefresh();
});
</script>
<!-- PART 1 -->
<div id="Parte1">
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td>
				Interno
			</td>
			<td>
				<input type="text" id="nomina_novedades_interno<?php echo $NumOfID ?>"/>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Empleado
			</td>
			<td>
				<div id="nomina_novedades_cliente1<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_cliente_ID1<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Reemplazo
			</td>
			<td>
				<div id="nomina_novedades_cliente2<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_cliente_ID2<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				Autoriz&oacute;
			</td>
			<td>
				<div id="nomina_novedades_cliente3<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_cliente_ID3<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 20px 0px;">
		<tr>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Novedad
			</td>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Justificacion
			</td>
			<td>
				&nbsp;
			</td>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Fecha
			</td>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Hora
			</td>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Total
			</td>
		</tr>
		<tr>
			<td>
				<div id="nomina_novedades_novedad<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_justificacion<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				Desde el Dia
			</td>
			<td>
				<div id="nomina_novedades_fecha_ini<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_hora_ini<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_total<?php echo $NumOfID ?>"></div>
			</td>
		</tr>
		<tr>
			<td>
				<li class="parte1_li_txt">
					Dsc.
				</li>
				<li>
					<div id="nomina_novedades_descontado<?php echo $NumOfID ?>"></div>
				</li>
				<li class="parte1_li_txt">
					100%
				</li>
				<li>
					<div id="nomina_novedades_remunerado100<?php echo $NumOfID ?>"></div>
				</li>
				<li class="parte1_li_txt">
					66%
				</li>
				<li>
					<div id="nomina_novedades_remunerado66<?php echo $NumOfID ?>"></div>
				</li>
				<li class="parte1_li_txt">
					Cst.
				</li>
				<li>
					<div id="nomina_novedades_cesantia<?php echo $NumOfID ?>"></div>
				</li>
			</td>
			<td>
				<input type="text" id="nomina_novedades_comentario<?php echo $NumOfID ?>"/>
			</td>
			<td>
				Hasta el Dia
			</td>
			<td>
				<div id="nomina_novedades_fecha_fin<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_hora_fin<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td style="text-align:center; background: #51A351; color: #FFF;">
				Reposicion
			</td>
			<td style="text-align:center; background: #51A351; color: #FFF;">
				Fecha
			</td>
			<td style="text-align:center; background: #51A351; color: #FFF;">
				Desde
			</td>
			<td style="text-align:center; background: #51A351; color: #FFF;">
				Hasta
			</td>
			<td style="text-align:center; background: #51A351; color: #FFF;">
				Total
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<div id="nomina_novedades_reposicion<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_fecha<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_hora_ini2<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_hora_fin2<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<div id="nomina_novedades_total2<?php echo $NumOfID ?>"></div>
			</td>
			<td>
				<input type="button" id="nomina_novedades_addrow<?php echo $NumOfID ?>" value="A&ntilde;adir"/>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td rowspan="5">
				<div id="nomina_novedades_items_grid<?php echo $NumOfID ?>"></div>
			</td>
			<td style="height:140px">
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				Observacion
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td rowspan="3" style="padding: 0px; margin: 0px;">
				<textarea rows="5" cols="31" id="nomina_novedades_observacion<?php echo $NumOfID ?>" maxlength="200" style="resize:none; height:85px;"></textarea>
			</td>
			<td>
				<input type="button" id="nomina_novedades_guardar<?php echo $NumOfID ?>" value="Guardar"/>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="nomina_novedades_imprimir<?php echo $NumOfID ?>" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="nomina_novedades_nuevo<?php echo $NumOfID ?>" value="Nuevo"/>
			</td>
		</tr>
	</table>
</div>

