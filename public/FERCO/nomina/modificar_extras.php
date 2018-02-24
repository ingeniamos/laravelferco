<?php
session_start();
?>
<script type="text/javascript">
$(document).ready(function ()
{// Implementar los permisos, mejorar la ventana de alerta en el guardado y añador el limpiado e imprimir en sus botones
	//----- GOLBALS
	var mytheme = "energyblue";
	var Interno = "";
	var Timer1 = 0;
	var Timer2 = 0;
	var Locked = false;
	var ClienteData = new Array();
	var FestivosData = new Array();
	var HorasData = new Array();
	
	// START - Code for Refresh Data
	var Main = document.getElementById("Nomina_Content");
	var Body = document.getElementById("Nomina_ModExtras_Content");
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
				SystemMap("Modificar Extras", true);
				ReDefine();
				ClearDocument();
				// Buscar Extras
				ExtrasSource.data = {"Nomina_Extras":true},
				ExtrasDataAdapter = new $.jqx.dataAdapter(ExtrasSource);
				$("#nomina_modificar_extras_interno").jqxComboBox({source: ExtrasDataAdapter});
				//---
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
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Mod_Extras" && $data[$i]["Guardar"] == "false")
				{
		?>
					$("#nomina_modificar_extras_guardar").jqxButton({ disabled: true });
					$("#nomina_modificar_extras_nuevo").jqxButton({ disabled: true });
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Mod_Extras" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#nomina_modificar_extras_imprimir").jqxButton({ disabled: true });
		<?php
				}
			}
		}
	} ?>
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"nomina_modificar_extras_interno", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_cliente1", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_cliente2", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_cliente_ID1", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_cliente_ID2", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_justificacion", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_comentario", type:""},
			//--
			{id:"nomina_modificar_extras_fecha", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_extras_hora_ini", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_extras_hora_fin", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_extras_total", type:"jqxNumberInput"},
			{id:"nomina_modificar_extras_festivo", type:"jqxCheckBox"},
			{id:"nomina_modificar_extras_nocturno", type:"jqxCheckBox"},
			//---
			{id:"nomina_modificar_extras_items_grid", type:"jqxGrid"},
			{id:"nomina_modificar_extras_observacion", type:""},
		];
		
		EnableDisableJSON = [
			{id:"nomina_modificar_extras_cliente1", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_cliente2", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_cliente_ID1", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_cliente_ID2", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_justificacion", type:"jqxComboBox"},
			{id:"nomina_modificar_extras_comentario", type:""},
			//--
			{id:"nomina_modificar_extras_fecha", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_extras_hora_ini", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_extras_hora_fin", type:"jqxDateTimeInput"},
			//---
			{id:"nomina_modificar_extras_items_grid", type:"jqxGrid"},
			{id:"nomina_modificar_extras_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Interno = "";
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
				{ name: "AutorizadorID", type: 'string'},
				{ name: "Justificacion", type: 'string'},
				{ name: "Comentario", type: 'string'},
				{ name: "Observacion", type: 'string'},
				{ name: "GridData", type: 'string'},
				//--
				//{ name: "Turno", type: 'date'},
				//{ name: "Hora_Ini", type: 'string'},
				//{ name: "Hora_Fin", type: 'string'},
				//{ name: "Total", type: 'decimal'},
				//{ name: "Nocturno", type: 'bool'},
				//{ name: "Festivo", type: 'bool'},
			],
			data:{"Nomina_Extras_Modificar":$("#nomina_modificar_extras_interno").val()},
			url: "modulos/datos.php",
		};
		var GetValuesAdapter = new $.jqx.dataAdapter(GetValuesSource,{
			autoBind: true,
			loadComplete: function ()
			{
				var records = GetValuesAdapter.records;
				var len = records[0]["GridData"].length;
				$("#nomina_modificar_extras_items_grid").jqxGrid("clear");
				for (var i = 0; i < len; i++)
				{
					var datarow = {
						"Fecha":new Date(SetFormattedDate(records[0]["GridData"][i]["Turno"])),
						"HoraIni":From24To12(records[0]["GridData"][i]["Hora_Ini"]),
						"HoraFin":From24To12(records[0]["GridData"][i]["Hora_Fin"]),
						"Hora_Ini":records[0]["GridData"][i]["Hora_Ini"],
						"Hora_Fin":records[0]["GridData"][i]["Hora_Fin"],
						"Total":records[0]["GridData"][i]["Total"],
						"Festivo":records[0]["GridData"][i]["Festivo"],
						"Nocturno":records[0]["GridData"][i]["Nocturno"],
					};
					$("#nomina_modificar_extras_items_grid").jqxGrid("addrow", null, datarow, "first");
				}
				
				$("#nomina_modificar_extras_cliente1").val(records[0]["EmpleadoID"]);
				$("#nomina_modificar_extras_cliente2").val(records[0]["AutorizadorID"]);
				$("#nomina_modificar_extras_justificacion").val(records[0]["Justificacion"]);
				$("#nomina_modificar_extras_comentario").val(records[0]["Comentario"]);
				$("#nomina_modificar_extras_observacion").val(records[0]["Observacion"]);
			},
			loadError: function(jqXHR, status, error) {
				alert("Request failed: \n" + error);
			},
		});
	}
	
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
				if (records[i]["Tipo"] == "Diurno")
				{
					HorasData["Diurnas_Ini"] = records[i]["Hora_Ini"];
					HorasData["Diurnas_Fin"] = records[i]["Hora_Fin"];
				}
				else if (records[i]["Tipo"] == "Nocturno")
				{
					HorasData["Nocturnas_Ini"] = records[i]["Hora_Ini"];
					HorasData["Nocturnas_Fin"] = records[i]["Hora_Fin"];
				}
			}
		}
	});
	
	var FestivoSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Fecha', type: 'string'}
		],
		data: {"Nomina_Festivos":true},
		url: "modulos/parametros.php",
		async: false
	};
	var FestivoDataAdapter = new $.jqx.dataAdapter(FestivoSource,
	{
		autoBind: true,
		beforeLoadComplete: function (records)
		{
			for (var i = 0; i < records.length; i++)
			{
				FestivosData.push(records[i]["Fecha"]);
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
	
	function FechaFestiva(Fecha)
	{
		var len = FestivosData.length;
		for (var i = 0; i < len; i++)
		{
			if (Fecha == FestivosData[i])
			{
				return true;
			}
		}
		return false;
	}
	
	function DiaFestivo(Fecha, FechaFestiva)
	{
		if (Fecha == "")
			return false;
		
		var DateCheck = new Date(Fecha)
		var Day = DateCheck.getDay();
		var DayNum = DateCheck.getDate();
		var Month = DateCheck.getMonth();
		var Year = DateCheck.getFullYear();
		
		Day = Day + 1;
		DayNum = DayNum + 1;
		DayNum = DayNum + "";
		if (DayNum.length == 1)
		{
			DayNum = "0" + DayNum;
		};
		
		Month = Month - 1;
		Month = Month + "";
		if (Month.length == 1)
		{
			Month = "0" + Month;
		};
		
		if (Day == 7) // Sunday
		{
			return true;
		}
		
		if (FechaFestiva(Fecha))
			return true;
		else
			return false;
	}
	
	function HoraNocturna(Hora_Ini, Hora_Fin)
	{
		var HorasNocturnas_Ini = HorasData["Nocturnas_Ini"];
		var HorasNocturnas_Fin = HorasData["Nocturnas_Fin"];
		var Nocturno = 0;
		
		if (Date.parse("12/31/1999 " + Hora_Ini) > Date.parse("12/31/1999 " + HorasNocturnas_Ini))
		{
			Nocturno++;
		}
		else if (Date.parse("12/31/1999 " + Hora_Ini) == Date.parse("12/31/1999 " + HorasNocturnas_Ini) && 
				Date.parse("12/31/1999 " + Hora_Fin) > Date.parse("12/31/1999 " + HorasNocturnas_Ini))
		{
			Nocturno++;
		}
		else if (Date.parse("12/31/1999 " + Hora_Ini) < Date.parse("12/31/1999 " + HorasNocturnas_Fin))
		{
			Nocturno++;
		}
		
		if (Date.parse("12/31/1999 " + Hora_Fin) > Date.parse("12/31/1999 " + HorasNocturnas_Ini))
		{
			Nocturno++;
		}
		else if (Date.parse("12/31/1999 " + Hora_Fin) < Date.parse("12/31/1999 " + HorasNocturnas_Fin))
		{
			Nocturno++;
		}
		
		if (Nocturno > 0)
			return true;
		else
			return false;
	}
	
	function GetHours(Hora_Ini, Hora_Fin)
	{
		//--- Parametro Diurno
		var HoraDiurnaIniTmp = HorasData["Diurnas_Ini"].split(/[:]/);
		var HoraDiurnaFinTmp = HorasData["Diurnas_Fin"].split(/[:]/);
		var HoraDiurnaIni = parseInt(HoraDiurnaIniTmp[0]);
		var MinutoDiurnoIni = parseInt(HoraDiurnaIniTmp[1]);
		var HoraDiurnaFin = parseInt(HoraDiurnaFinTmp[0]);
		var MinutoDiurnoFin = parseInt(HoraDiurnaFinTmp[1]);
		//--- Parametro Nocturno
		var HoraNocturnaIniTmp = HorasData["Nocturnas_Ini"].split(/[:]/);
		var HoraNocturnaFinTmp = HorasData["Nocturnas_Fin"].split(/[:]/);
		var HoraNocturnaIni = parseInt(HoraNocturnaIniTmp[0]);
		var MinutoNocturnoIni = parseInt(HoraNocturnaIniTmp[1]);
		var HoraNocturnaFin = parseInt(HoraNocturnaFinTmp[0]);
		var MinutoNocturnoFin = parseInt(HoraNocturnaFinTmp[1]);
		//--- Parametro TOTALES
		var HorasDiurnas_Ini = parseFloat(""+HoraDiurnaIni+"."+MinutoDiurnoIni).toFixed(2);//6.00;
		var HorasDiurnas_Fin = parseFloat(""+HoraDiurnaFin+"."+MinutoDiurnoFin).toFixed(2);//22.00;
		var HorasNocturnas_Ini = parseFloat(""+HoraNocturnaIni+"."+MinutoNocturnoIni).toFixed(2);//22.00;
		var HorasNocturnas_Fin = parseFloat(""+HoraNocturnaFin+"."+MinutoNocturnoFin).toFixed(2);//6.00;
		//--- Valores a Devolver
		var HoraMadrugada = 0;
		var HoraDiurna = 0;
		var HoraNocturna = 0;
		var HoraInicial_Madrugada = 0;
		var HoraInicial_Diurna = 0;
		var HoraInicial_Nocturna = 0;
		var HoraFinal_Madrugada = 0;
		var HoraFinal_Diurna = 0;
		var HoraFinal_Nocturna = 0;
		//--- Valores Temporales
		var MinutoTotal = 0;
		//--- Valores de la Llamada
		var IniTmp = Hora_Ini.split(/[:]/);
		var FinTmp = Hora_Fin.split(/[:]/);
		var HoraIni = parseInt(IniTmp[0]);
		var MinutoIni = parseInt(IniTmp[1]);
		var HoraFin = parseInt(FinTmp[0]);
		var MinutoFin = parseInt(FinTmp[1]);

		var HoraIniTotal = parseFloat(""+HoraIni+"."+MinutoIni).toFixed(2);
		var HoraFinTotal = parseFloat(""+HoraFin+"."+MinutoFin).toFixed(2);
		
		//alert("Hora Inicial ->" + HoraIniTotal+ " - Hora Final ->" + HoraFinTotal)
		
		if (HoraIniTotal >= 0 && HoraIniTotal < parseFloat(HorasNocturnas_Fin))
		{
			//alert("Bucle 1")
			HoraInicial_Madrugada = HoraIniTotal;
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
				
				//if (TmpIniTime == HorasNocturnas_Fin)
				var tmp_HorasNocturnas_Fin = ""+HorasNocturnas_Fin;// can't compare in numbers -> compare in string
				if (TmpIniTime.localeCompare(tmp_HorasNocturnas_Fin) == 0)
				{
					//alert("salida1-1! "+ TmpIniTime + " - " + TmpTotalTime)
					HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
					HoraMadrugada = TmpTotalTime;
					HoraFinal_Madrugada = parseFloat(TmpIniTime).toFixed(2);
					break;
				}
				
				//if (TmpIniTime == HoraFinTotal)
				var tmp_HoraFinTotal = ""+HoraFinTotal;// can't compare in numbers -> compare in string
				if (TmpIniTime.localeCompare(tmp_HoraFinTotal) == 0)
				{
					//alert("salida1-2! "+ TmpIniTime + " - " + TmpTotalTime)
					HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
					HoraMadrugada = TmpTotalTime;
					HoraFinal_Madrugada = parseFloat(TmpIniTime).toFixed(2);
					break;
				}
				
				MinutoIni = parseInt(MinutoIni);
				
				MinutoIni++;
				MinutoTotal++;
			}
		}
		
		if (HoraIniTotal >= parseFloat(HorasDiurnas_Ini) && HoraIniTotal <= parseFloat(HorasDiurnas_Fin))
		{
			//alert("Bucle 2")
			HoraInicial_Diurna = HoraIniTotal;
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
				
				var tmp_HorasDiurnas_Fin = ""+HorasDiurnas_Fin;// can't compare in numbers -> compare in string
				if (TmpIniTime.localeCompare(tmp_HorasDiurnas_Fin) == 0)
				{
					//alert("salida2-1! "+ TmpIniTime+ " - " + TmpTotalTime)
					HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
					HoraDiurna = TmpTotalTime;
					HoraFinal_Diurna = parseFloat(TmpIniTime).toFixed(2);
					break;
				}
				
				var tmp_HoraFinTotal = ""+HoraFinTotal;// can't compare in numbers -> compare in string
				if (TmpIniTime.localeCompare(tmp_HoraFinTotal) == 0)
				{
					//alert("salida2-2! "+ TmpIniTime+ " - " + TmpTotalTime)
					HoraIniTotal = parseFloat(TmpIniTime).toFixed(2);
					HoraDiurna = TmpTotalTime;
					HoraFinal_Diurna = parseFloat(TmpIniTime).toFixed(2);
					break;
				}
				
				MinutoIni = parseInt(MinutoIni);
				
				MinutoIni++;
				MinutoTotal++;
			}
		}
		
		if (HoraIniTotal >= parseFloat(HorasNocturnas_Ini) && HoraIniTotal < 24)
		{
			//alert("Bucle 4")
			HoraInicial_Nocturna = HoraIniTotal;
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
				
				if (HoraIni >= 24)
				{
					HoraIni = 0;
				}
				
				var TmpIniTime = ""+HoraIni+"."+MinutoIni;
				var TmpTotalTime = (MinutoTotal / 60).toFixed(2);
				

				if (TmpIniTime == "0.00")
				{
					//alert("salida4-1! "+ TmpIniTime+ " - " + TmpTotalTime)
					HoraNocturna = TmpTotalTime;
					HoraFinal_Nocturna = parseFloat(TmpIniTime).toFixed(2);
					break;
				}
				
				var tmp_HoraFinTotal = ""+HoraFinTotal;// can't compare in numbers -> compare in string
				if (TmpIniTime.localeCompare(tmp_HoraFinTotal) == 0)
				{
					//alert("salida4-2! "+ TmpIniTime+ " - " + TmpTotalTime)
					HoraNocturna = TmpTotalTime;
					HoraFinal_Nocturna = parseFloat(TmpIniTime).toFixed(2);
					break;
				}
				
				MinutoIni = parseInt(MinutoIni);
				
				MinutoIni++;
				MinutoTotal++;
			}
		}
		
		HoraInicial_Madrugada = HoraInicial_Madrugada+"";
		HoraInicial_Diurna = HoraInicial_Diurna+"";
		HoraInicial_Nocturna = HoraInicial_Nocturna+"";
		
		HoraFinal_Madrugada = HoraFinal_Madrugada+"";
		HoraFinal_Diurna = HoraFinal_Diurna+"";
		HoraFinal_Nocturna = HoraFinal_Nocturna+"";
		
		
		/*alert("HoraInicial_Madrugada -> " + HoraInicial_Madrugada 
		+ " - HoraInicial_Diurna -> " + HoraInicial_Diurna 
		+ " - HoraInicial_Nocturna -> " + HoraInicial_Nocturna 
		+ " - HoraFinal_Madrugada -> " + HoraFinal_Madrugada 
		+ " - HoraFinal_Diurna -> " + HoraFinal_Diurna 
		+ " - HoraFinal_Nocturna ->" + HoraFinal_Nocturna 
		+ " - HoraMadrugada -> " + HoraMadrugada 
		+ " - HoraDiurna -> " + HoraDiurna 
		+ " - HoraNocturna ->" + HoraNocturna);*/
		
		var Data = {
			"HoraInicial_Madrugada":HoraInicial_Madrugada.replace(".", ":"),
			"HoraInicial_Diurna":HoraInicial_Diurna.replace(".", ":"),
			"HoraInicial_Nocturna":HoraInicial_Nocturna.replace(".", ":"),
			"HoraFinal_Madrugada":HoraFinal_Madrugada.replace(".", ":"),
			"HoraFinal_Diurna":HoraFinal_Diurna.replace(".", ":"),
			"HoraFinal_Nocturna":HoraFinal_Nocturna.replace(".", ":"),
			"HoraMadrugada":HoraMadrugada,
			"HoraDiurna":HoraDiurna,
			"HoraNocturna":HoraNocturna,
		};
		return Data;
	}
	
	function Calc()
	{
		var Check1 = false;
		var Check2 = false;
		var Fecha = GetFormattedDate($("#nomina_modificar_extras_fecha").jqxDateTimeInput('getDate'));
		var Hora_Ini = $("#nomina_modificar_extras_hora_ini").jqxDateTimeInput('getText');
		Hora_Ini = From12To24(Hora_Ini);
		var Hora_Fin = $("#nomina_modificar_extras_hora_fin").jqxDateTimeInput('getText');
		Hora_Fin = From12To24(Hora_Fin);
		
		var IniTmp = Hora_Ini.split(/[:]/);
		var FinTmp = Hora_Fin.split(/[:]/);
		
		var TotalHoras = (FinTmp[0] - IniTmp[0]);
		var TotalMinutos = ((FinTmp[1] - IniTmp[1]).toFixed(2)/60);
		var Total = TotalHoras + TotalMinutos;
		
		if (Total < 0)
		{
			Total += 24;
			$("#nomina_modificar_extras_total").val(Total);
			var FechaTmp = new Date(Fecha);
			FechaTmp.setDate(FechaTmp.getDate() + 2);
			var NewFecha = GetFormattedDate(FechaTmp);
			Check2 = DiaFestivo(NewFecha, FechaFestiva);
			Check1 = DiaFestivo(Fecha, FechaFestiva);
			var Festivo = (Check1 + Check2 >= 1) ? "check":"uncheck";
			$("#nomina_modificar_extras_festivo").jqxCheckBox(Festivo);
			
			var Nocturno = HoraNocturna(Hora_Ini+":00", Hora_Fin+":00") ? "check":"uncheck";
			$("#nomina_modificar_extras_nocturno").jqxCheckBox(Nocturno);
			
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
			$("#nomina_modificar_extras_total").val(Total);
			Check1 = DiaFestivo(Fecha, FechaFestiva);
			var Festivo = Check1 ? "check":"uncheck";
			$("#nomina_modificar_extras_festivo").jqxCheckBox(Festivo);
			
			var Nocturno = HoraNocturna(Hora_Ini+":00", Hora_Fin+":00") ? "check":"uncheck";
			$("#nomina_modificar_extras_nocturno").jqxCheckBox(Nocturno);
			
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
	
	function GenerateData()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var cData = Calc();
		var GridArray = new Array();
		//alert(""+ cData["Hora_Ini1"] +" - "+ cData["Hora_Fin1"] +" - "+ cData["Hora_Ini2"] + " - " + cData["Hora_Fin2"]);
		
		if (cData["Hora_Ini1"] != "")
		{
			var hData = GetHours(cData["Hora_Ini1"], cData["Hora_Fin1"]);
			
			var Madrugada = hData["HoraMadrugada"];
			var Diurno = hData["HoraDiurna"];
			var Nocturno = hData["HoraNocturna"];
			
			if (Madrugada != 0)
			{
				var datarow = {
					"Fecha":new Date(SetFormattedDate(cData["Fecha1"])),
					"HoraIni":From24To12(hData["HoraInicial_Madrugada"]),
					"HoraFin":From24To12(hData["HoraFinal_Madrugada"]),
					"Hora_Ini":hData["HoraInicial_Madrugada"],
					"Hora_Fin":hData["HoraFinal_Madrugada"],
					"Total":Madrugada,
					"Festivo":DiaFestivo(cData["Fecha1"], FechaFestiva),
					"Nocturno":true,
				};
				//alert("Ini_Mad - "+JSON.stringify(datarow))
				GridArray.push(datarow);
			}
			
			if (Diurno != 0)
			{
				var datarow = {
					"Fecha":new Date(SetFormattedDate(cData["Fecha1"])),
					"HoraIni":From24To12(hData["HoraInicial_Diurna"]),
					"HoraFin":From24To12(hData["HoraFinal_Diurna"]),
					"Hora_Ini":hData["HoraInicial_Diurna"],
					"Hora_Fin":hData["HoraFinal_Diurna"],
					"Total":Diurno,
					"Festivo":DiaFestivo(cData["Fecha1"], FechaFestiva),
					"Nocturno":false,
				};
				//alert("Ini_Diu - "+JSON.stringify(datarow))
				GridArray.push(datarow);
			}
			
			if (Nocturno != 0)
			{
				var datarow = {
					"Fecha":new Date(SetFormattedDate(cData["Fecha1"])),
					"HoraIni":From24To12(hData["HoraInicial_Nocturna"]),
					"HoraFin":From24To12(hData["HoraFinal_Nocturna"]),
					"Hora_Ini":hData["HoraInicial_Nocturna"],
					"Hora_Fin":hData["HoraFinal_Nocturna"],
					"Total":Nocturno,
					"Festivo":DiaFestivo(cData["Fecha1"], FechaFestiva),
					"Nocturno":true,
				};
				//alert("Ini_Noc - "+JSON.stringify(datarow))
				GridArray.push(datarow);
			}
		}
		
		if (cData["Hora_Ini2"] != "")
		{
			var hData = GetHours(cData["Hora_Ini2"], cData["Hora_Fin2"]);
			
			var Madrugada = hData["HoraMadrugada"];
			var Diurno = hData["HoraDiurna"];
			var Nocturno = hData["HoraNocturna"];
			
			if (Madrugada != 0)
			{
				var datarow = {
					"Fecha":new Date(SetFormattedDate(cData["Fecha2"])),
					//"HoraIni":From24To12("00:00"),
					"HoraIni":From24To12(hData["HoraInicial_Madrugada"]),
					"HoraFin":From24To12(hData["HoraFinal_Madrugada"]),
					//"Hora_Ini":"00:00",
					"Hora_Ini":hData["HoraInicial_Madrugada"],
					"Hora_Fin":hData["HoraFinal_Madrugada"],
					"Total":Madrugada,
					"Festivo":DiaFestivo(cData["Fecha2"], FechaFestiva),
					"Nocturno":true,
				};
				//alert("Fin_Mad - "+JSON.stringify(datarow))
				GridArray.push(datarow);
			}
			
			if (Diurno != 0)
			{
				var datarow = {
					"Fecha":new Date(SetFormattedDate(cData["Fecha2"])),
					"HoraIni":From24To12(hData["HoraInicial_Diurna"]),
					"HoraFin":From24To12(hData["HoraFinal_Diurna"]),
					"Hora_Ini":hData["HoraInicial_Diurna"],
					"Hora_Fin":hData["HoraFinal_Diurna"],
					"Total":Diurno,
					"Festivo":DiaFestivo(cData["Fecha2"], FechaFestiva),
					"Nocturno":false,
				};
				//alert("Fin_Diu - "+JSON.stringify(datarow))
				GridArray.push(datarow);
			}
			
			if (Nocturno != 0)
			{
				var datarow = {
					"Fecha":new Date(SetFormattedDate(cData["Fecha2"])),
					"HoraIni":From24To12(hData["HoraInicial_Nocturna"]),
					"HoraFin":From24To12(hData["HoraFinal_Nocturna"]),
					"Hora_Ini":hData["HoraInicial_Nocturna"],
					"Hora_Fin":hData["HoraFinal_Nocturna"],
					"Total":Nocturno,
					"Festivo":DiaFestivo(cData["Fecha2"], FechaFestiva),
					"Nocturno":true,
				};
				//alert("Fin_Noc - "+JSON.stringify(datarow))
				GridArray.push(datarow);
			}
		}
		
		//alert(JSON.stringify(datarow))
		GridArray.sort(function(a, b)
		{
			var A = new Date(a.Fecha+" "+a.HoraIni);
			var B = new Date(b.Fecha+" "+b.HoraIni);
			if(A < B) return 1;
			if(A > B) return -1;
			return 0;
		});
		$("#nomina_modificar_extras_items_grid").jqxGrid("addrow", null, GridArray, "first");
		
		Locked = false;
		
		$("#nomina_modificar_extras_fecha").jqxDateTimeInput('setDate', new Date());
		$("#nomina_modificar_extras_hora_ini").jqxDateTimeInput('setDate', "1999-12-31 00:00:00");
		$("#nomina_modificar_extras_hora_fin").jqxDateTimeInput('setDate', "1999-12-31 00:00:00");
		$("#nomina_modificar_extras_total").val("");
		$("#nomina_modificar_extras_festivo").jqxCheckBox('uncheck');
		$("#nomina_modificar_extras_nocturno").jqxCheckBox('uncheck');
	}
	
	var ClienteSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Nombre', type: 'string'},
			{ name: 'ClienteID', type: 'string'}
		],
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
				ClienteData.push(records[i]);
			}
		}
	});
	
	var ExtrasSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Interno', type: 'string'},
		],
		//data: {"Nomina_Extras":true},
		url: "modulos/datos.php",
	};
	//var ExtrasDataAdapter = new $.jqx.dataAdapter(ExtrasSource);
	
	$("#nomina_modificar_extras_interno").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		//source: ExtrasDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Interno',
		valueMember: 'Interno'
	});
	$("#nomina_modificar_extras_interno").bind('change', function (event)
	{
		if (event.args)
		{
			if (Interno == event.args.item.value)
				return;
			
			Interno = event.args.item.value;
			
			clearTimeout(Timer1);
			Timer1 = setTimeout(function()
			{
				if (ClickOK == true)
				{
					EnableDisableAll(false);
					ClickOK = false;
				}
				LoadValues();
			},500);
		}
		else
		{
			var item_value = $("#nomina_modificar_extras_interno").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#nomina_modificar_extras_interno").jqxComboBox('clearSelection');
				ClearDocument();
			}
			else
			{
				var value = $("#nomina_modificar_extras_interno").val();
				var item = $("#nomina_modificar_extras_interno").jqxComboBox('getItems');
				
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#nomina_modificar_extras_interno").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#nomina_modificar_extras_interno").jqxComboBox('clearSelection');
				ClearDocument();
			}
		}
	});
	$("#nomina_modificar_extras_interno").bind('bindingComplete', function (event) {
		if (Interno != "")
			$("#nomina_modificar_extras_interno").jqxComboBox('selectItem', Interno);
	});
	// Buscar Extras
	ExtrasSource.data = {"Nomina_Extras":true},
	ExtrasDataAdapter = new $.jqx.dataAdapter(ExtrasSource);
	$("#nomina_modificar_extras_interno").jqxComboBox({source: ExtrasDataAdapter});
	
	$("#nomina_modificar_extras_cliente1").jqxComboBox(
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
	$("#nomina_modificar_extras_cliente1").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_extras_cliente_ID1").val() != event.args.item.value)
				$("#nomina_modificar_extras_cliente_ID1").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_extras_cliente_ID1").jqxComboBox(
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
	$("#nomina_modificar_extras_cliente_ID1").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_extras_cliente1").val() != event.args.item.value)
				$("#nomina_modificar_extras_cliente1").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_extras_cliente2").jqxComboBox(
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
	$("#nomina_modificar_extras_cliente2").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_extras_cliente_ID2").val() != event.args.item.value)
				$("#nomina_modificar_extras_cliente_ID2").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_extras_cliente_ID2").jqxComboBox(
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
	$("#nomina_modificar_extras_cliente_ID2").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_extras_cliente2").val() != event.args.item.value)
				$("#nomina_modificar_extras_cliente2").val(event.args.item.value);
		}
	});
	
	var JustificacionSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Tipo', type: 'string'},
		],
		data: {"Nomina_Justificacion":true},
		url: "modulos/parametros.php",
		async: true
	};
	var JustificacionDataAdapter = new $.jqx.dataAdapter(JustificacionSource);
	
	$("#nomina_modificar_extras_justificacion").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 170,
		source: JustificacionDataAdapter,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#nomina_modificar_extras_justificacion").bind('change', function (event) {
		if (!event.args)
		{
			$("#nomina_modificar_extras_justificacion").jqxComboBox('clearSelection');
		}
	});
	
	$("#nomina_modificar_extras_comentario").jqxInput(
	{
		theme: mytheme,
		width: 170,
		height: 20,
	});
	
	$("#nomina_modificar_extras_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_modificar_extras_fecha").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	$("#nomina_modificar_extras_hora_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_modificar_extras_hora_ini").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	$("#nomina_modificar_extras_hora_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_modificar_extras_hora_fin").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	$("#nomina_modificar_extras_total").jqxNumberInput({
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
	
	$("#nomina_modificar_extras_nocturno").jqxCheckBox({
		theme: mytheme,
		boxSize: 19,
		disabled: true
	});
	
	$("#nomina_modificar_extras_festivo").jqxCheckBox({
		theme: mytheme,
		boxSize: 19,
		disabled: true
	});
	
	//----------------------
	//----------------------
	
	$("#nomina_modificar_extras_addrow").jqxButton({
		width: 65,
		template: "success"
	});
	$("#nomina_modificar_extras_addrow").bind('click', function ()
	{
		GenerateData();
	});
	
	var GridSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Fecha', type: 'date'},
			{ name: 'HoraIni', type: 'string'},
			{ name: 'HoraFin', type: 'string'},
			{ name: 'Hora_Ini', type: 'date'},
			{ name: 'Hora_Fin', type: 'date'},
			{ name: 'Total', type: 'decimal'},
			{ name: 'Festivo', type: 'bool'},
			{ name: 'Nocturno', type: 'bool'},
		],
	};
	var GridDataAdapter = new $.jqx.dataAdapter(GridSource);
	
	$("#nomina_modificar_extras_items_grid").jqxGrid(
	{
		theme: mytheme,
		height: 250,
		width: 500,
		source: GridDataAdapter,
		editable: false,
		editmode: 'click',
		columns:
		[
			{
				text: '',
				datafield: 'Del',
				columntype: 'button',
				editable: false,
				width: '6%',
				height: 20,
				cellsrenderer: function (row, datafield, value) {
					return "X";
				},
				buttonclick: function () {
					var selectedrowindex = $("#nomina_modificar_extras_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#nomina_modificar_extras_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#nomina_modificar_extras_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#nomina_modificar_extras_items_grid").jqxGrid('deleterow', id);
					}
				}
			},
			{
				text: 'Fecha',
				datafield: 'Fecha',
				columntype: 'datetimeinput',
				width: "30%",
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
				width: "16%",
				height: 20,
				editable: false,
				filterable: false,
			},
			{
				text: 'Hora Final',
				datafield: 'HoraFin',
				width: "16%",
				height: 20,
				editable: false,
				filterable: false,
			},
			{
				text: '',
				datafield: 'Hora_Ini',
				columntype: 'datetimeinput',
				width: "16%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: 'HH:mm',
			},
			{
				text: '',
				datafield: 'Hora_Fin',
				columntype: 'datetimeinput',
				width: "16%",
				height: 20,
				editable: false,
				filterable: false,
				cellsformat: 'HH:mm',
			},
			{
				text: 'Total(h)',
				datafield: 'Total',
				width: "12%",
				height: 20,
				cellsalign: 'right',
				columntype: 'numberinput',
				editable: false,
				filterable: false,
				createeditor: function (row, cellvalue, editor) {
					editor.jqxNumberInput({ decimalDigits: 2, digits: 2 });
				}
			},
			{
				text: 'Fest.',
				datafield: 'Festivo',
				columntype: 'checkbox',
				width: "10%",
				editable: false,
				filterable: false,
			},
			{
				text: 'Noct.',
				datafield: 'Nocturno',
				columntype: 'checkbox',
				width: "10%",
				editable: false,
				filterable: false,
			},
		]
	});
	
	$("#nomina_modificar_extras_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#nomina_modificar_extras_items_grid").jqxGrid('hidecolumn', 'Hora_Ini');
	$("#nomina_modificar_extras_items_grid").jqxGrid('hidecolumn', 'Hora_Fin');
	
	//----------------------------------------------------------------------------------
	//									GUARDAR
	//----------------------------------------------------------------------------------
	function ModificarExtras()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Interno = $("#nomina_modificar_extras_interno").jqxComboBox('getSelectedItem');
		var Empleado = $("#nomina_modificar_extras_cliente1").jqxComboBox('getSelectedItem');
		var Autorizador = $("#nomina_modificar_extras_cliente2").jqxComboBox('getSelectedItem');
		var Justificacion = $("#nomina_modificar_extras_justificacion").jqxComboBox('getSelectedItem');
		var Comentario = $("#nomina_modificar_extras_comentario").jqxComboBox('getSelectedItem');
		
		if (!Interno)
		{
			Alerts_Box("Debe Seleccionar un Interno!", 3);
			WaitClick_Combobox("nomina_modificar_extras_interno");
			Locked = false;
			return;
		}
		
		if (!Empleado)
		{
			Alerts_Box("Debe Ingresar un Empleado!", 3);
			WaitClick_Combobox("nomina_modificar_extras_cliente1");
			Locked = false;
			return;
		}
		
		if (!Autorizador)
		{
			Alerts_Box("Debe Ingresar la persona que Autoriza!", 3);
			WaitClick_Combobox("nomina_modificar_extras_cliente2");
			Locked = false;
			return;
		}
		
		if (!Justificacion)
		{
			Alerts_Box("Debe Ingresar una Justificacion!", 3);
			WaitClick_Combobox("nomina_modificar_extras_justificacion");
			Locked = false;
			return;
		}
		
		var datinfo = $("#nomina_modificar_extras_items_grid").jqxGrid('getdatainformation');
		var count = datinfo.rowscount;
		var myarray = new Array();
		
		if (count <= 0)
		{
			Alerts_Box("Debe Ingresar almenos un Turno!", 3);
			WaitClick();
			Locked = false;
			return;
		}
		
		for (i = 0; i < count; i++)
		{
			var array = {};
			var currentRow = $("#nomina_modificar_extras_items_grid").jqxGrid('getrowdata', i);
			
			array["Fecha"] = GetFormattedDate(currentRow.Fecha);
			array["Hora_Ini"] = currentRow.Hora_Ini;
			array["Hora_Fin"] = currentRow.Hora_Fin;
			array["Total"] = currentRow.Total;
			array["Festivo"] = currentRow.Festivo;
			array["Nocturno"] = currentRow.Nocturno;
			
			if (i==0)
			{
				array["Interno"] = Interno.value;
				array["Empleado"] = Empleado.value;
				array["Autorizador"] = Autorizador.value;
				array["Justificacion"] = Justificacion.value;
				array["Comentario"] = $("#nomina_modificar_extras_comentario").val();
				array["Observacion"] = $("#nomina_modificar_extras_observacion").val();
			}
			myarray[i] = array;
		};
		
		/*alert(JSON.stringify(myarray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: {"Nomina_Extras_Modificar":myarray},
			async: true,
			success: function (data)
			{
				ReDefine();
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				
				switch(data[0]["MESSAGE"])
				{
					case "OK":
						EnableDisableAll(true);
						//ClearDocument();
						Alerts_Box("Horas Extras Modificadas con Exito!", 2);
						// Buscar Extras
						ExtrasSource.data = {"Nomina_Extras":true},
						ExtrasDataAdapter = new $.jqx.dataAdapter(ExtrasSource);
						$("#nomina_modificar_extras_interno").jqxComboBox({source: ExtrasDataAdapter});
					break;
					
					case "CHANGED":
						Alerts_Box("No es posible guardar cambios debido a que otro usuario, modificó este movimiento.", 4);
					break;
					
					case "ERROR":
						Alerts_Box("Ocurrió un error mientras se guardaban los cambios.<br/>Intente luego de unos segundos...", 3);
					break;
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#Loading").hide();
				$("#Loading_Mess").html("Cargando...");
				Locked = false;
				Alerts_Box("Ocurrió un error mientras se guardaban los cambios...<br/>Favor Contactar al Soporte Tecnico.<br />"
				+"Error: "+errorThrown, 3);
			}
		});
	}
	
	$("#nomina_modificar_extras_guardar").jqxButton({
		width: 120,
		template: "info"
	});
	$("#nomina_modificar_extras_guardar").bind('click', function ()
	{
		ModificarExtras();
	});
	$("#nomina_modificar_extras_imprimir").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#nomina_modificar_extras_imprimir").bind('click', function ()
	{
		var data = "imprimir/nom_extras.php?Interno="+$("#nomina_modificar_extras_interno").val()+"";
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	$("#nomina_modificar_extras_nuevo").jqxButton({
		width: 120,
		template: "success"
	});
	$("#nomina_modificar_extras_nuevo").bind('click', function ()
	{
		ClearDocument();
	});
	
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
				<div id="nomina_modificar_extras_interno"></div>
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
				<div id="nomina_modificar_extras_cliente1"></div>
			</td>
			<td>
				<div id="nomina_modificar_extras_cliente_ID1"></div>
			</td>
		</tr>
		<tr>
			<td>
				Autoriz&oacute;
			</td>
			<td>
				<div id="nomina_modificar_extras_cliente2"></div>
			</td>
			<td>
				<div id="nomina_modificar_extras_cliente_ID2"></div>
			</td>
		</tr>
		<tr>
			<td>
				Justificacion
			</td>
			<td colspan="2">
				<li style="margin-right: 5px;">
					<div id="nomina_modificar_extras_justificacion"></div>
				</li>
				<li>
					<input type="text" id="nomina_modificar_extras_comentario"/>
				</li>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Fecha
			</td>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Hora Inicio
			</td>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Hora Fin
			</td>
			<td style="text-align:center; background: #467ED0; color: #FFF;">
				Total
			</td>
			<td style="text-align:center; background: #E00; color: #FFF;">
				Fest.
			</td>
			<td style="text-align:center; background: #000; color: #FFF;">
				Noct.
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td>
				<div id="nomina_modificar_extras_fecha"></div>
			</td>
			<td>
				<div id="nomina_modificar_extras_hora_ini"></div>
			</td>
			<td>
				<div id="nomina_modificar_extras_hora_fin"></div>
			</td>
			<td>
				<div id="nomina_modificar_extras_total"></div>
			</td>
			<td>
				<div id="nomina_modificar_extras_festivo"></div>
			</td>
			<td>
				<div id="nomina_modificar_extras_nocturno"></div>
			</td>
			<td>
				<input type="button" id="nomina_modificar_extras_addrow" value="A&ntilde;adir"/>
			</td>
		</tr>
	</table>
	
	<div id="nomina_modificar_extras_items_grid"></div>
	
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td>
				Observacion
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<tr>
			<td rowspan="3">
				<textarea rows="5" cols="31" id="nomina_modificar_extras_observacion" maxlength="200" style="resize:none;"></textarea>
			</td>
			<td>
				<input type="button" id="nomina_modificar_extras_guardar" value="Guardar"/>
			</td>
			<td>
				<input type="button" id="nomina_modificar_extras_imprimir" value="Imprimir"/>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<input type="button" id="nomina_modificar_extras_nuevo" value="Nuevo"/>
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
	</table>
</div>

