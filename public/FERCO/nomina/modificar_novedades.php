<?php
header('Content-Type: text/html; charset=utf-8');
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
	var Body = document.getElementById("Nomina_ModNovedades_Content");
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
				SystemMap("Modificar Novedades", true);
				ReDefine();
				ClearDocument();
				// Buscar Novedades
				NovedadSource.data = {"Nomina_Novedades":true},
				NovedadDataAdapter = new $.jqx.dataAdapter(NovedadSource);
				$("#nomina_modificar_novedades_interno").jqxComboBox({source: NovedadDataAdapter});
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
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Mod_Novedades" && $data[$i]["Guardar"] == "false")
				{
		?>
					$("#nomina_modificar_novedades_guardar").jqxButton({ disabled: true });
					$("#nomina_modificar_novedades_nuevo").jqxButton({ disabled: true });
		<?php
				}
				
				if ($data[$i]["Modulo"] == "Nomina" && $data[$i]["SubModulo"] == "Mod_Novedades" && $data[$i]["Imprimir"] == "false")
				{
		?>
					$("#nomina_modificar_novedades_imprimir").jqxButton({ disabled: true });
		<?php
				}
			}
		}
	} ?>
	
	function ReDefine()
	{
		ClearJSON = [
			{id:"nomina_modificar_novedades_interno", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente1", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente2", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente3", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente_ID1", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente_ID2", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente_ID3", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_justificacion", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_comentario", type:""},
			{id:"nomina_modificar_novedades_novedad", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_fecha_ini", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_fecha_fin", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_hora_ini", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_hora_fin", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_subtotal", type:"jqxNumberInput"},
			{id:"nomina_modificar_novedades_total", type:"jqxNumberInput"},
			{id:"nomina_modificar_novedades_descontado", type:"jqxCheckBox"},
			{id:"nomina_modificar_novedades_remunerado100", type:"jqxCheckBox"},
			{id:"nomina_modificar_novedades_remunerado66", type:"jqxCheckBox"},
			{id:"nomina_modificar_novedades_cesantia", type:"jqxCheckBox"},
			//---
			{id:"nomina_modificar_novedades_reposicion", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_fecha", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_hora_ini2", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_hora_fin2", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_total2", type:"jqxNumberInput"},
			//---
			{id:"nomina_modificar_novedades_items_grid", type:"jqxGrid"},
			{id:"nomina_modificar_novedades_observacion", type:""},
		];
		
		EnableDisableJSON = [
			{id:"nomina_modificar_novedades_cliente1", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente2", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente3", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente_ID1", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente_ID2", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_cliente_ID3", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_justificacion", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_comentario", type:""},
			{id:"nomina_modificar_novedades_novedad", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_fecha_ini", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_fecha_fin", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_hora_ini", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_hora_fin", type:"jqxDateTimeInput"},
			//---
			{id:"nomina_modificar_novedades_reposicion", type:"jqxComboBox"},
			{id:"nomina_modificar_novedades_fecha", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_hora_ini2", type:"jqxDateTimeInput"},
			{id:"nomina_modificar_novedades_hora_fin2", type:"jqxDateTimeInput"},
			//---
			{id:"nomina_modificar_novedades_items_grid", type:"jqxGrid"},
			{id:"nomina_modificar_novedades_guardar", type:"jqxButton"},
		];
	}
	ReDefine();
	function ClearDocument()
	{
		// Variables
		Locked = false;
		Interno = "";
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
			data:{"Nomina_Novedades_Modificar":$("#nomina_modificar_novedades_interno").val()},
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
						$("#nomina_modificar_novedades_items_grid").jqxGrid("addrow", null, datarow, "first");
					}
				}
				
				$("#nomina_modificar_novedades_cliente1").val(records[0]["EmpleadoID"]);
				$("#nomina_modificar_novedades_cliente2").val(records[0]["ReemplazoID"]);
				$("#nomina_modificar_novedades_cliente3").val(records[0]["AutorizadorID"]);
				$("#nomina_modificar_novedades_novedad").val(records[0]["Novedad"]);
				
				clearTimeout(Timer1);
				Timer1 = setTimeout(function()
				{
					$("#nomina_modificar_novedades_justificacion").val(records[0]["Justificacion"]);
				},500);
				
				$("#nomina_modificar_novedades_comentario").val(records[0]["Comentario"]);
				$("#nomina_modificar_novedades_fecha_ini").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Ini"])));
				$("#nomina_modificar_novedades_fecha_fin").jqxDateTimeInput("setDate", new Date(SetFormattedDate(records[0]["Fecha_Fin"])));
				$("#nomina_modificar_novedades_hora_ini").jqxDateTimeInput("setDate", new Date(SetFormattedTime(records[0]["FechaHora_Ini"])));
				$("#nomina_modificar_novedades_hora_fin").jqxDateTimeInput("setDate", new Date(SetFormattedTime(records[0]["FechaHora_Fin"])));
				$("#nomina_modificar_novedades_reposicion").val(records[0]["Reposicion"]);
				$("#nomina_modificar_novedades_observacion").val(records[0]["Observacion"]);
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
		
		GridArray.sort(function(a, b)
		{
			var A = new Date(a.Fecha+" "+a.HoraIni);
			var B = new Date(b.Fecha+" "+b.HoraIni);
			if(A < B)
				return 1;
			else if(A > B)
				return -1;
			return 0;
		});
		
		$("#nomina_modificar_novedades_items_grid").jqxGrid("addrow", null, GridArray, "first");
		Locked = false;
	}
	
	function Calc(first)
	{
		if (first)
		{
			var Empleado = $("#nomina_modificar_novedades_cliente1").jqxComboBox('getSelectedItem');
			var FechaIni = GetFormattedDate($("#nomina_modificar_novedades_fecha_ini").jqxDateTimeInput('getDate'));
			var FechaFin = GetFormattedDate($("#nomina_modificar_novedades_fecha_fin").jqxDateTimeInput('getDate'));
			var Hora_Ini = $("#nomina_modificar_novedades_hora_ini").jqxDateTimeInput('getText');
			Hora_Ini = From12To24(Hora_Ini);
			var Hora_Fin = $("#nomina_modificar_novedades_hora_fin").jqxDateTimeInput('getText');
			Hora_Fin = From12To24(Hora_Fin);
			
			if (FechaIni == FechaFin)
			{
				//alert(GetHours(Hora_Ini, Hora_Fin, true))
				if (!Empleado)
				{
					$("#nomina_modificar_novedades_total").val(GetHours(Hora_Ini, Hora_Fin, true));
				}
				else
				{
					var val = GetHours(Hora_Ini, Hora_Fin, true);
					if (parseInt(ClienteHoras[Empleado.value]) < 240)
						val = val / 2;
					$("#nomina_modificar_novedades_total").val(val);
				}
			}
			else if (FechaIni > FechaFin)
			{
				$("#nomina_modificar_novedades_total").val("0");
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
					$("#nomina_modificar_novedades_total").val(Total);
				}
				else
				{
					if (parseInt(ClienteHoras[Empleado.value]) < 240)
						Total = Total / 2;
					$("#nomina_modificar_novedades_total").val(Total);
				}
			}
		}
		else
		{
			var Fecha = GetFormattedDate($("#nomina_modificar_novedades_fecha").jqxDateTimeInput('getDate'));
			var Hora_Ini = $("#nomina_modificar_novedades_hora_ini2").jqxDateTimeInput('getText');
			Hora_Ini = From12To24(Hora_Ini);
			var Hora_Fin = $("#nomina_modificar_novedades_hora_fin2").jqxDateTimeInput('getText');
			Hora_Fin = From12To24(Hora_Fin);
			
			var IniTmp = Hora_Ini.split(/[:]/);
			var FinTmp = Hora_Fin.split(/[:]/);
			
			var TotalHoras = (FinTmp[0] - IniTmp[0]);
			var TotalMinutos = ((FinTmp[1] - IniTmp[1]).toFixed(2)/60);
			var Total = TotalHoras + TotalMinutos;
		
			/*var Tmp = "" + (FinTmp[0] - IniTmp[0])+"."+(FinTmp[1] - IniTmp[1]);
			var Total = parseFloat(Tmp);
			
			$("#nomina_modificar_novedades_total2").val(GetHours(Hora_Ini, Hora_Fin));*/
			
			if (Total < 0)
			{
				Total += 24;
				$("#nomina_modificar_novedades_total2").val(Total);
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
				$("#nomina_modificar_novedades_total2").val(Total);
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
	
	var NovedadSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Interno', type: 'string'},
		],
		//data: {"Nomina_Novedades":true},
		url: "modulos/datos.php",
	};
	//var NovedadDataAdapter = new $.jqx.dataAdapter(NovedadSource);
	
	$("#nomina_modificar_novedades_interno").jqxComboBox(
	{
		theme: mytheme,
		width: 120,
		height: 20,
		//source: NovedadDataAdapter,
		searchMode: 'containsignorecase',
		autoComplete: true,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Interno',
		valueMember: 'Interno'
	});
	$("#nomina_modificar_novedades_interno").bind('change', function (event)
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
			var item_value = $("#nomina_modificar_novedades_interno").jqxComboBox('getSelectedItem');
			if (item_value)
			{
				$("#nomina_modificar_novedades_interno").jqxComboBox('clearSelection');
				ClearDocument();
			}
			else
			{
				var value = $("#nomina_modificar_novedades_interno").val();
				var item = $("#nomina_modificar_novedades_interno").jqxComboBox('getItems');
				
				for (var i = 0; i < item.length; i++)
				{
					if (item[i].label == value)
					{
						$("#nomina_modificar_novedades_interno").jqxComboBox('selectItem', item[i].value);
						return;
					}
				}
				$("#nomina_modificar_novedades_interno").jqxComboBox('clearSelection');
				ClearDocument();
			}
		}
	});
	$("#nomina_modificar_novedades_interno").bind('bindingComplete', function (event) {
		if (Interno != "")
			$("#nomina_modificar_novedades_interno").jqxComboBox('selectItem', Interno);
	});
	// Buscar Novedades
	NovedadSource.data = {"Nomina_Novedades":true},
	NovedadDataAdapter = new $.jqx.dataAdapter(NovedadSource);
	$("#nomina_modificar_novedades_interno").jqxComboBox({source: NovedadDataAdapter});
	
	$("#nomina_modificar_novedades_cliente1").jqxComboBox(
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
	$("#nomina_modificar_novedades_cliente1").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_novedades_cliente_ID1").val() != event.args.item.value)
				$("#nomina_modificar_novedades_cliente_ID1").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_novedades_cliente_ID1").jqxComboBox(
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
	$("#nomina_modificar_novedades_cliente_ID1").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_novedades_cliente1").val() != event.args.item.value)
				$("#nomina_modificar_novedades_cliente1").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_novedades_cliente2").jqxComboBox(
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
	$("#nomina_modificar_novedades_cliente2").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_novedades_cliente_ID2").val() != event.args.item.value)
				$("#nomina_modificar_novedades_cliente_ID2").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_novedades_cliente_ID2").jqxComboBox(
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
	$("#nomina_modificar_novedades_cliente_ID2").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_novedades_cliente2").val() != event.args.item.value)
				$("#nomina_modificar_novedades_cliente2").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_novedades_cliente3").jqxComboBox(
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
	$("#nomina_modificar_novedades_cliente3").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_novedades_cliente_ID3").val() != event.args.item.value)
				$("#nomina_modificar_novedades_cliente_ID3").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_novedades_cliente_ID3").jqxComboBox(
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
	$("#nomina_modificar_novedades_cliente_ID3").bind('change', function (event) {
		if (event.args)
		{
			if ($("#nomina_modificar_novedades_cliente3").val() != event.args.item.value)
				$("#nomina_modificar_novedades_cliente3").val(event.args.item.value);
		}
	});
	
	$("#nomina_modificar_novedades_novedad").jqxComboBox({
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
	$("#nomina_modificar_novedades_novedad").bind('change', function (event) {
		if (event.args)
		{
			var Desc = Descontable[event.args.item.value] ? "check":"uncheck";
			var R100 = Remunerado100[event.args.item.value] ? "check":"uncheck";
			var R66 = Remunerado66[event.args.item.value] ? "check":"uncheck";
			var Ces = Cesantia[event.args.item.value] ? "check":"uncheck";
			
			$("#nomina_modificar_novedades_descontado").jqxCheckBox(Desc);
			$("#nomina_modificar_novedades_remunerado100").jqxCheckBox(R100);
			$("#nomina_modificar_novedades_remunerado66").jqxCheckBox(R66);
			$("#nomina_modificar_novedades_cesantia").jqxCheckBox(Ces);
			
			var item = event.args.item.value;
			
			if (item.indexOf("extra") > 0)
				JustificacionSource.data = {"Nomina_Justificacion":true};
			else
				JustificacionSource.data = {"Nomina_Justificacion2":true};
			
			var JustificacionDataAdapter = new $.jqx.dataAdapter(JustificacionSource);
			$("#nomina_modificar_novedades_justificacion").jqxComboBox({source: JustificacionDataAdapter});
		}
		else
		{
			$("#nomina_modificar_novedades_novedad").jqxComboBox('clearSelection');
			$("#nomina_modificar_novedades_descontado").jqxCheckBox("uncheck");
			$("#nomina_modificar_novedades_remunerado100").jqxCheckBox("uncheck");
			$("#nomina_modificar_novedades_remunerado66").jqxCheckBox("uncheck");
			$("#nomina_modificar_novedades_cesantia").jqxCheckBox("uncheck");
		}
	});
	
	$("#nomina_modificar_novedades_descontado").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
		disabled: true
	});
	
	$("#nomina_modificar_novedades_remunerado100").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
		disabled: true
	});
	
	$("#nomina_modificar_novedades_remunerado66").jqxCheckBox({
		theme: mytheme,
		boxSize: 15,
		disabled: true
	});
	
	$("#nomina_modificar_novedades_cesantia").jqxCheckBox({
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
		url: "modulos/parametros.php",
	};
	
	$("#nomina_modificar_novedades_justificacion").jqxComboBox({
		theme: mytheme,
		height: 20,
		width: 170,
		promptText: 'Seleccionar',
		selectedIndex: -1,
		displayMember: 'Tipo',
		valueMember: 'Tipo',
		searchMode: 'containsignorecase',
		autoComplete: true,
	});
	$("#nomina_modificar_novedades_justificacion").bind('change', function (event) {
		if (!event.args)
		{
			$("#nomina_modificar_novedades_justificacion").jqxComboBox('clearSelection');
		}
	});
	
	$("#nomina_modificar_novedades_comentario").jqxInput(
	{
		theme: mytheme,
		width: 170,
		height: 20,
		placeHolder: "¿Cual?"
	});
	
	$("#nomina_modificar_novedades_fecha_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_modificar_novedades_fecha_ini").on('change', function (event) 
	{
		if(event.args)
			Calc(true);
	});
	
	$("#nomina_modificar_novedades_hora_ini").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_modificar_novedades_hora_ini").on('change', function (event) 
	{
		if(event.args)
			Calc(true);
	});
	
	$("#nomina_modificar_novedades_fecha_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_modificar_novedades_fecha_fin").on('change', function (event) 
	{
		if(event.args)
			Calc(true);
	});
	
	$("#nomina_modificar_novedades_hora_fin").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_modificar_novedades_hora_fin").on('change', function (event) 
	{
		if(event.args)
			Calc(true);
	});
	
	$("#nomina_modificar_novedades_total").jqxNumberInput({
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
	
	$("#nomina_modificar_novedades_reposicion").jqxComboBox({
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
	$("#nomina_modificar_novedades_reposicion").bind('change', function (event) {
		if (event.args)
		{
			var val = Reponer[event.args.item.value] ? false:true;
			
			$("#nomina_modificar_novedades_fecha").jqxDateTimeInput({ disabled: val});
			$("#nomina_modificar_novedades_hora_ini2").jqxDateTimeInput({ disabled: val});
			$("#nomina_modificar_novedades_hora_fin2").jqxDateTimeInput({ disabled: val});
			$("#nomina_modificar_novedades_addrow").jqxButton({ disabled: val});
		}
		else
		{
			$("#nomina_modificar_novedades_reposicion").jqxComboBox('clearSelection');
			$("#nomina_modificar_novedades_fecha").jqxDateTimeInput({ disabled: false});
			$("#nomina_modificar_novedades_hora_ini2").jqxDateTimeInput({ disabled: false});
			$("#nomina_modificar_novedades_hora_fin2").jqxDateTimeInput({ disabled: false});
			$("#nomina_modificar_novedades_addrow").jqxButton({ disabled: false});
		}
	});
	
	$("#nomina_modificar_novedades_fecha").jqxDateTimeInput({
		theme: mytheme,
		width: 100,
		height: 20,
		formatString: 'dd-MMM-yyyy',
		culture: 'es-ES',
	});
	$("#nomina_modificar_novedades_fecha").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	$("#nomina_modificar_novedades_hora_ini2").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_modificar_novedades_hora_ini2").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	$("#nomina_modificar_novedades_hora_fin2").jqxDateTimeInput({
		theme: mytheme,
		width: 80,
		height: 20,
		formatString: 'hh:mm tt',
		showCalendarButton: false,
		showTimeButton: true,
	});
	$("#nomina_modificar_novedades_hora_fin2").on('change', function (event) 
	{
		if(event.args)
			Calc();
	});
	
	
	$("#nomina_modificar_novedades_total2").jqxNumberInput({
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
	
	$("#nomina_modificar_novedades_addrow").jqxButton({
		width: 70,
		template: "success"
	});
	$("#nomina_modificar_novedades_addrow").bind('click', function ()
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
	
	$("#nomina_modificar_novedades_items_grid").jqxGrid(
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
					var selectedrowindex = $("#nomina_modificar_novedades_items_grid").jqxGrid('getselectedrowindex');
					var rowscount = $("#nomina_modificar_novedades_items_grid").jqxGrid('getdatainformation').rowscount;
					if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
						var id = $("#nomina_modificar_novedades_items_grid").jqxGrid('getrowid', selectedrowindex);
						$("#nomina_modificar_novedades_items_grid").jqxGrid('deleterow', id);
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
	
	$("#nomina_modificar_novedades_items_grid").jqxGrid('localizestrings', localizationobj);
	$("#nomina_modificar_novedades_items_grid").jqxGrid('hidecolumn', 'Hora_Ini');
	$("#nomina_modificar_novedades_items_grid").jqxGrid('hidecolumn', 'Hora_Fin');
	
	//----------------------------------------------------------------------------------
	//									GUARDAR
	//----------------------------------------------------------------------------------
	function ModificarNovedad()
	{
		if (Locked == true)
			return;
		
		Locked = true;
		
		var Interno = $("#nomina_modificar_novedades_interno").jqxComboBox('getSelectedItem');
		var Empleado = $("#nomina_modificar_novedades_cliente1").jqxComboBox('getSelectedItem');
		var Reemplazo = $("#nomina_modificar_novedades_cliente2").jqxComboBox('getSelectedItem');
		var Autorizador = $("#nomina_modificar_novedades_cliente3").jqxComboBox('getSelectedItem');
		var Justificacion = $("#nomina_modificar_novedades_justificacion").jqxComboBox('getSelectedItem');
		var Novedad = $("#nomina_modificar_novedades_novedad").jqxComboBox('getSelectedItem');
		var Reposicion = $("#nomina_modificar_novedades_reposicion").jqxComboBox('getSelectedItem');
		var myarray = [];
		
		if (!Interno)
		{
			Alerts_Box("Debe Seleccionar un Interno!", 3);
			WaitClick_Combobox("nomina_modificar_novedades_interno");
			Locked = false;
			return;
		}
		
		if (!Empleado)
		{
			Alerts_Box("Debe Seleccionar un Empleado!", 3);
			WaitClick_Combobox("nomina_modificar_novedades_cliente1");
			Locked = false;
			return;
		}
		
		if (!Autorizador)
		{
			Alerts_Box("Debe Seleccionar a la persona que Autoriza!", 3);
			WaitClick_Combobox("nomina_modificar_novedades_cliente3");
			Locked = false;
			return;
		}
		
		if (!Justificacion)
		{
			Alerts_Box("Debe Seleccionar una Justificacion!", 3);
			WaitClick_Combobox("nomina_modificar_novedades_justificacion");
			Locked = false;
			return;
		}
		
		if (!Novedad)
		{
			Alerts_Box("Debe Seleccionar un tipo de Novedad!", 3);
			WaitClick_Combobox("nomina_modificar_novedades_novedad");
			Locked = false;
			return;
		}
		
		if (!Reposicion)
		{
			Alerts_Box("Debe Seleccionar un tipo de Reposicion!", 3);
			WaitClick_Combobox("nomina_modificar_novedades_reposicion");
			Locked = false;
			return;
		}
		else
		{
			if (Reponer[Reposicion.value] == true)
			{
				var datinfo = $("#nomina_modificar_novedades_items_grid").jqxGrid('getdatainformation');
				var count = datinfo.rowscount;
				
				if (count <= 0)
				{
					Alerts_Box("Debe Ingresar almenos un Turno de Reposicion!", 3);
					WaitClick();
					Locked = false;
					return;
				}
				
				var Total = 0;
				for (i = 0; i < count; i++)
				{
					var currentRow = $("#nomina_modificar_novedades_items_grid").jqxGrid('getrowdata', i);
					Total = Total + currentRow.Total;
				};
				
				for (i = 0; i < count; i++)
				{
					var array = {};
					var currentRow = $("#nomina_modificar_novedades_items_grid").jqxGrid('getrowdata', i);
					
					array["Fecha"] = GetFormattedDate(currentRow.Fecha);
					array["Hora_Ini"] = currentRow.Hora_Ini;
					array["Hora_Fin"] = currentRow.Hora_Fin;
					array["Total"] = currentRow.Total;
					
					if (i==0)
					{
						array["Interno"] = Interno.value;
						array["Empleado"] = Empleado.value;
						array["Reemplazo"] = Reemplazo ? Reemplazo.value:"";
						array["Autorizador"] = Autorizador.value;
						array["Justificacion"] = Justificacion.value;
						array["Comentario"] = $("#nomina_modificar_novedades_comentario").val();
						array["Novedad"] = Novedad.value;
						array["Descontable"] = $("#nomina_modificar_novedades_descontado").val();
						array["Remunerado100"] = $("#nomina_modificar_novedades_remunerado100").val();
						array["Remunerado66"] = $("#nomina_modificar_novedades_remunerado66").val();
						array["Cesantia"] = $("#nomina_modificar_novedades_cesantia").val();
						array["Fecha_Ini"] = GetFormattedDate($("#nomina_modificar_novedades_fecha_ini").jqxDateTimeInput('getDate'));
						array["Fecha_Fin"] = GetFormattedDate($("#nomina_modificar_novedades_fecha_fin").jqxDateTimeInput('getDate'));
						array["FechaHora_Ini"] = From12To24($("#nomina_modificar_novedades_hora_ini").jqxDateTimeInput('getText'));
						array["FechaHora_Fin"] = From12To24($("#nomina_modificar_novedades_hora_fin").jqxDateTimeInput('getText'));
						array["Total_Novedad"] = $("#nomina_modificar_novedades_total").val();
						array["Total_Reposicion"] = Total;
						array["Reposicion"] = Reposicion.value;
						array["Observacion"] = $("#nomina_modificar_novedades_observacion").val();
					}
					myarray[i] = array;
				};
			}
			else
			{
				var array = {};
				
				array["Interno"] = Interno.value;
				array["Empleado"] = Empleado.value;
				array["Reemplazo"] = Reemplazo ? Reemplazo.value:"";
				array["Autorizador"] = Autorizador.value;
				array["Justificacion"] = Justificacion.value;
				array["Comentario"] = $("#nomina_modificar_novedades_comentario").val();
				array["Novedad"] = Novedad.value;
				array["Descontable"] = $("#nomina_modificar_novedades_descontado").val();
				array["Remunerado100"] = $("#nomina_modificar_novedades_remunerado100").val();
				array["Remunerado66"] = $("#nomina_modificar_novedades_remunerado66").val();
				array["Cesantia"] = $("#nomina_modificar_novedades_cesantia").val();
				array["Fecha_Ini"] = GetFormattedDate($("#nomina_modificar_novedades_fecha_ini").jqxDateTimeInput('getDate'));
				array["Fecha_Fin"] = GetFormattedDate($("#nomina_modificar_novedades_fecha_fin").jqxDateTimeInput('getDate'));
				array["FechaHora_Ini"] = From12To24($("#nomina_modificar_novedades_hora_ini").jqxDateTimeInput('getText'));
				array["FechaHora_Fin"] = From12To24($("#nomina_modificar_novedades_hora_fin").jqxDateTimeInput('getText'));
				array["Total_Novedad"] = $("#nomina_modificar_novedades_total").val();
				array["Total_Reposicion"] = 0;
				array["Reposicion"] = Reposicion.value;
				array["Observacion"] = $("#nomina_modificar_novedades_observacion").val();
				
				myarray[0] = array;
			}
		}
		
		/*alert(JSON.stringify(myarray))
		Locked = false;
		return;*/
		
		$("#Loading_Mess").html("Procesando Solicitud...");
		$("#Loading").show();
		$.ajax({
			dataType: "json",
			url: "modulos/guardar.php",
			data: {"Nomina_Novedades_Modificar":myarray},
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
						Alerts_Box("Novedad Modificada con Exito!", 2);
						// Buscar Novedades
						NovedadSource.data = {"Nomina_Novedades":true},
						NovedadDataAdapter = new $.jqx.dataAdapter(NovedadSource);
						$("#nomina_modificar_novedades_interno").jqxComboBox({source: NovedadDataAdapter});
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
	
	$("#nomina_modificar_novedades_guardar").jqxButton({
		width: 120,
		template: "info"
	});
	$("#nomina_modificar_novedades_guardar").bind('click', function ()
	{
		ModificarNovedad();
	});
	$("#nomina_modificar_novedades_imprimir").jqxButton({
		width: 120,
		template: "warning"
	});
	$("#nomina_modificar_novedades_imprimir").bind('click', function ()
	{
		var data = "imprimir/nom_novedades.php?Interno="+$("#nomina_modificar_novedades_interno").val()+"";
		window.open(data, "", "width=735, height=600, menubar=no, titlebar=no");
	});
	$("#nomina_modificar_novedades_nuevo").jqxButton({
		width: 120,
		template: "success"
	});
	$("#nomina_modificar_novedades_nuevo").bind('click', function ()
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
				<div id="nomina_modificar_novedades_interno"></div>
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
				<div id="nomina_modificar_novedades_cliente1"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_cliente_ID1"></div>
			</td>
		</tr>
		<tr>
			<td>
				Reemplazo
			</td>
			<td>
				<div id="nomina_modificar_novedades_cliente2"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_cliente_ID2"></div>
			</td>
		</tr>
		<tr>
			<td>
				Autoriz&oacute;
			</td>
			<td>
				<div id="nomina_modificar_novedades_cliente3"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_cliente_ID3"></div>
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
				<div id="nomina_modificar_novedades_novedad"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_justificacion"></div>
			</td>
			<td>
				Desde el Dia
			</td>
			<td>
				<div id="nomina_modificar_novedades_fecha_ini"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_hora_ini"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_total"></div>
			</td>
		</tr>
		<tr>
			<td>
				<li class="parte1_li_txt">
					Dsc.
				</li>
				<li>
					<div id="nomina_modificar_novedades_descontado"></div>
				</li>
				<li class="parte1_li_txt">
					100%
				</li>
				<li>
					<div id="nomina_modificar_novedades_remunerado100"></div>
				</li>
				<li class="parte1_li_txt">
					66%
				</li>
				<li>
					<div id="nomina_modificar_novedades_remunerado66"></div>
				</li>
				<li class="parte1_li_txt">
					Cst.
				</li>
				<li>
					<div id="nomina_modificar_novedades_cesantia"></div>
				</li>
			</td>
			<td>
				<input type="text" id="nomina_modificar_novedades_comentario"/>
			</td>
			<td>
				Hasta el Dia
			</td>
			<td>
				<div id="nomina_modificar_novedades_fecha_fin"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_hora_fin"></div>
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
				<div id="nomina_modificar_novedades_reposicion"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_fecha"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_hora_ini2"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_hora_fin2"></div>
			</td>
			<td>
				<div id="nomina_modificar_novedades_total2"></div>
			</td>
			<td>
				<input type="button" id="nomina_modificar_novedades_addrow" value="A&ntilde;adir"/>
			</td>
		</tr>
	</table>
	
	<table cellpadding="2" cellspacing="4" style="margin:10px 0px 10px 0px;">
		<tr>
			<td rowspan="5">
				<div id="nomina_modificar_novedades_items_grid"></div>
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
				<textarea rows="5" cols="31" id="nomina_modificar_novedades_observacion" maxlength="200" style="resize:none; height:85px;"></textarea>
			</td>
			<td>
				<input type="button" id="nomina_modificar_novedades_guardar" value="Guardar"/>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="nomina_modificar_novedades_imprimir" value="Impimir"/>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" id="nomina_modificar_novedades_nuevo" value="Nuevo"/>
			</td>
		</tr>
	</table>
</div>

