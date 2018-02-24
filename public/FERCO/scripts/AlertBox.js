/*
	- Alert Box Func
	- Wait Click Func
	- Clear Func
	- Enable/Disable Func
	- GetFormattedDate
*/

var localizationobj = {};
localizationobj.pagergotopagestring = "Ir a la Pagina:";
localizationobj.pagershowrowsstring = "Mostrar Filas de:";
localizationobj.pagerrangestring = " de ";
localizationobj.emptydatastring = "No hay datos para mostrar";
localizationobj.loadtext = "Cargando...";
localizationobj.browseButton = "Buscar";
localizationobj.uploadButton = "Subir";
localizationobj.cancelButton = "Cancelar";
localizationobj.uploadFileTooltip = "Subir Archivo";
localizationobj.cancelFileTooltip = "Cancelar";

var days = {
	names: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
	namesAbbr: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
	namesShort: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"]
};
localizationobj.days = days;
var months = {
	names: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	namesAbbr: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
};
localizationobj.months = months;

var ClickOK = false;
var ClickCANCEL = false;
var ClickClose = false;
var ClickTimer1 = 0;
var ClickTimer2 = 0;
var FocusID = "";
var ClearJSON = [{}];
var EnableDisableJSON = [{}];

/*function Check_SameID (ID, Type, From)
{
	var FindSource =
	{
		datatype: "json",
		datafields: [
			{ name: 'Same', type: 'bool'},
		],
		type: 'GET',
		data: {
			"Check_SameID":true,
			"FindID":ID,
			"FindType":Type,
			"FindFrom":From
		},
		url: "modulos/datos.php",
		async: false,
	};
	var FindDataAdapter = new $.jqx.dataAdapter(FindSource,{
		autoBind: true,
		loadComplete: function ()
		{	
			var records = FindDataAdapter.records;
			return records[0]["Same"];
		}
	});
};*/

function DeFocus()
{
	var tmp = document.createElement("input");
	document.body.appendChild(tmp);
	tmp.focus();
	document.body.removeChild(tmp);
}

function GetRandomWord(Num)
{
	// HEX TABLE
	var Characters = ["A", "B", "C", "D", "E", "F", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
	var Str = "";
	for (var i = 0; i < Num; i++)
	{
		Str = Str + Characters[Math.floor(Math.random() * Characters.length)];
	}
	return Str;
}

function getRandomColor()
{
	var letters = "0123456789ABCDEF".split("");
	var color = "#";
	for (var i = 0; i < 6; i++ )
		color += letters[Math.floor(Math.random() * 16)];
	return color;
}

function GetImage(SRC)
{
	// Return Folder+File&Ext
	var img_src = document.getElementById(SRC).src;
	var tmp = img_src.split("/");
	var len = tmp.length;
	var img = tmp[len-2]+"/"+tmp[len-1];
	return img;
};

function SystemMap(Data, Type)
{
	var ID;
	if (Type)
	{
		ID = document.getElementById("Logo_SystemMes");
		var Content = ID.innerHTML;
		var N = Content.indexOf("➢");
		if (N > 0)
		{
			var NContent = Content.slice(0,N);
			NContent = NContent + "&#10146; " + Data;
			$("#Logo_SystemMes").html(NContent);
		}
		else
			ID.innerHTML += " &#10146; " + Data;
	}
	else {
		$("#Logo_SystemMes").html(Data);
	}
}

function SetFormattedDate (MyDate)
{
	var Full = false;
	MyDate = MyDate + ""; //to string
	
	if (MyDate.indexOf(":") < 0)
	{
		var Tmp = MyDate.split(/[-]/);
		var NewDate = new Date(Tmp[0], Tmp[1]-1, Tmp[2]);
	}
	else
	{
		var Tmp = MyDate.split(/[- :]/);
		var NewDate = new Date(Tmp[0], Tmp[1]-1, Tmp[2], Tmp[3], Tmp[4], Tmp[5]);
		Full = true;
	}
	
	var day = NewDate.getDate();
	var month = NewDate.getMonth();
	var year = NewDate.getFullYear();
	var seconds = NewDate.getSeconds();
	var minutes = NewDate.getMinutes();
	var hours = NewDate.getHours();
	
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
	
	if (Full)
		var FormattedDate = year + ", " + month + ", " + day + ", " + hours + ":" + minutes + ":" + seconds;
	else
		var FormattedDate = year + ", " + month + ", " + day;
	
	FormattedDate = FormattedDate + ""; // to string
	return FormattedDate;
};

function GetFormattedDate (MyDate, Format, Full)
{
	var day = MyDate.getDate();
	var month = MyDate.getMonth();
	var year = MyDate.getFullYear();
	var seconds = MyDate.getSeconds();
	var minutes = MyDate.getMinutes();
	var hours = MyDate.getHours();
	
	day = day + "";// to string
	if (day.length == 1)
	{
		day = "0" + day;
	};
	
	month2 = months["namesAbbr"][month];
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
	
	if (Format)
	{
		if (Full)
			var FormattedDate = day + "-" + month2 + "-" + year + " " + hours + ":" + minutes + ":" + seconds;
		else
			var FormattedDate = day + "-" + month2 + "-" + year;
	}
	else
	{
		if (Full)
			var FormattedDate = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
		else
			var FormattedDate = year + "-" + month + "-" + day;
	}
	
	return FormattedDate;
	
	/*var day = MyDate.getDate();
	var month = MyDate.getMonth();
	var year = MyDate.getFullYear();
	
	day = day + "";// to string
	if (day.length == 1)
	{
		day = "0" + day;
	};
	
	month = month + 1;// is this right?
	month = month + "";// to string
	if (month.length == 1)
	{
		month = "0" + month;
	};
	
	var FormattedDate = year + "-" + month + "-" + day;
	
	return FormattedDate;*/
};

function SetFormattedTime (MyTime)
{
	MyTime = MyTime + ""; //to string
	
	var Tmp = MyTime.split(/[:]/);

	var year = 2000;
	var month = 01;
	var day = 01;
	
	var FormattedTime = year + ", " + month + ", " + day + ", " + Tmp[0] + ":" + Tmp[1] + ":" + Tmp[2];
	return FormattedTime;
};

function GetFormattedTime (MyTime)
{
	var minutes = MyTime.getMinutes();
	var hours = MyTime.getHours();
	
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
	
	var FormattedTime = hours + ":" + minutes;
	
	return FormattedTime;
};

function ClearAll (val)
{
	var left = window.pageXOffset;
	var top = window.pageYOffset;
	
	var len = ClearJSON.length;
	var a = 0;
	while (a < len)
	{
		switch(ClearJSON[a]["type"])
		{
			case "jqxComboBox":
				if (ClearJSON[a]["id"] != val)
					$("#"+ClearJSON[a]["id"]+"").jqxComboBox('clearSelection');
			break;
			case "jqxDropDownList":
				if (ClearJSON[a]["id"] != val)
					$("#"+ClearJSON[a]["id"]+"").jqxDropDownList('clearSelection');
			break;
			case "jqxCheckBox":
				if (ClearJSON[a]["id"] != val)
					$("#"+ClearJSON[a]["id"]+"").jqxCheckBox('uncheck');
			break;
			case "jqxDateTimeInput":
				if (ClearJSON[a]["id"] != val)
					$("#"+ClearJSON[a]["id"]+"").jqxDateTimeInput('setDate', new Date());
			break;
			case "jqxGrid":
				if (ClearJSON[a]["id"] != val)
					$("#"+ClearJSON[a]["id"]+"").jqxGrid('clear');
			break;
			case "jqxMaskedInput":
				if (ClearJSON[a]["id"] != val)
					$("#"+ClearJSON[a]["id"]+"").jqxMaskedInput('clear');
			break;
			case "jqxNumberInput":
				if (ClearJSON[a]["id"] != val)
					$("#"+ClearJSON[a]["id"]+"").val('');
			break;
			default:
				if (ClearJSON[a]["id"] != val)
					$("#"+ClearJSON[a]["id"]+"").val('');
			break;
		}
		a++;
	}
	//window.scrollTo($('#Container1').offset().top, scrollLeft: $('#Container1').offset().left);
	window.scrollTo(top, left);
	document.getElementById("Container1").scrollTop = 0;
	document.getElementById("Container1").scrollLeft = 0;
};

function EnableDisableAll (val)
{
	var len = EnableDisableJSON.length;
	var a = 0;
	while (a < len)
	{
		switch(EnableDisableJSON[a]["type"])
		{
			case "jqxComboBox":
				$("#"+EnableDisableJSON[a]["id"]+"").jqxComboBox({ disabled: val });
			break;
			case "jqxDropDownList":
				$("#"+EnableDisableJSON[a]["id"]+"").jqxDropDownList({ disabled: val });
			break;
			case "jqxCheckBox":
				$("#"+EnableDisableJSON[a]["id"]+"").jqxCheckBox({ disabled: val });
			break;
			case "jqxDateTimeInput":
				$("#"+EnableDisableJSON[a]["id"]+"").jqxDateTimeInput({ disabled: val });
			break;
			case "jqxButton":
				$("#"+EnableDisableJSON[a]["id"]+"").jqxButton({ disabled: val });
			break;
			case "jqxNumberInput":
				$("#"+EnableDisableJSON[a]["id"]+"").jqxNumberInput({ disabled: val });
			break;
			case "jqxMaskedInput":
				$("#"+EnableDisableJSON[a]["id"]+"").jqxMaskedInput({ disabled: val });
			break;
			case "jqxGrid":
				$("#"+EnableDisableJSON[a]["id"]+"").jqxGrid({ disabled: val });
			break;
			default:
				$("#"+EnableDisableJSON[a]["id"]+"").jqxInput({ disabled: val });
			break;
		}
		a++;
	}
	// Set FirstFocus
	/*switch(EnableDisableJSON[0]["type"])
	{
		case "jqxComboBox":
			$("#"+EnableDisableJSON[0]["id"]+"").jqxComboBox('focus');
		break;
		case "jqxDropDownList":
			$("#"+EnableDisableJSON[0]["id"]+"").jqxDropDownList('focus');
		break;
		case "jqxCheckBox":
			$("#"+EnableDisableJSON[0]["id"]+"").jqxCheckBox('focus');
		break;
		case "jqxDateTimeInput":
			$("#"+EnableDisableJSON[0]["id"]+"").jqxDateTimeInput('focus');
		break;
		case "jqxButton":
			$("#"+EnableDisableJSON[0]["id"]+"").jqxButton('focus');
		break;
		case "jqxGrid":
			$("#"+EnableDisableJSON[0]["id"]+"").jqxGrid('focus');
		break;
		case "jqxMaskedInput":
			$("#"+EnableDisableJSON[0]["id"]+"").jqxMaskedInput('focus');
		break;
		case "jqxNumberInput":
			$("#"+EnableDisableJSON[0]["id"]+"").jqxNumberInput('focus');
		break;
		default:
			$("#"+EnableDisableJSON[0]["id"]+"").jqxInput('focus');
		break;
	}
	window.scrollTo(0, 0);*/
};

function WaitClick_Combobox (Event)
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer1);
		ClickOK = false;
		ClickCANCEL = false;
		$("#"+Event+"").jqxComboBox('focus');
		$("#"+Event+"").jqxComboBox('open');
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true) 
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
			$("#"+Event+"").jqxComboBox('focus');
			$("#"+Event+"").jqxComboBox('open');
		}
	}, 10);
};

function WaitClick_DropDownList (Event)
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer1);
		ClickOK = false;
		ClickCANCEL = false;
		$("#"+Event+"").jqxDropDownList('focus');
		$("#"+Event+"").jqxDropDownList('open');
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
			$("#"+Event+"").jqxDropDownList('focus');
			$("#"+Event+"").jqxDropDownList('open');
		}
	}, 10);
};

function WaitClick_Input (Event)
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer1);
		ClickOK = false;
		ClickCANCEL = false;
		$("#"+Event+"").jqxInput('focus');
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
			$("#"+Event+"").jqxInput('focus');
		}
	}, 10);
};

function WaitClick_MaskedInput (Event)
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer1);
		ClickOK = false;
		ClickCANCEL = false;
		$("#"+Event+"").jqxMaskedInput('focus');
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
			$("#"+Event+"").jqxMaskedInput('focus');
		}
	}, 10);
};

function WaitClick_NumberInput (Event)
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer1);
		ClickOK = false;
		ClickCANCEL = false;
		$("#"+Event+"").jqxNumberInput('focus');
		var input = $('#'+Event+' input')[0];
		if ('selectionStart' in input) {
			input.setSelectionRange(0, 0);
		} else {
			var range = input.createTextRange();
			range.collapse(true);
			range.moveEnd('character', 0);
			range.moveStart('character', 0);
			range.select();
		}
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
			$("#"+Event+"").jqxNumberInput('focus');
			var input = $("#"+Event+" input")[0];
			if ('selectionStart' in input) {
				input.setSelectionRange(0, 0);
			} else {
				var range = input.createTextRange();
				range.collapse(true);
				range.moveEnd('character', 0);
				range.moveStart('character', 0);
				range.select();
			}
		}
	}, 10);
};

function WaitClick_WindowOpen (Event)
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer2);
		ClickOK = false;
		ClickCANCEL = false;
		$("#"+Event+"").jqxWindow('open');
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
			$("#"+Event+"").jqxWindow('open');
		}
	}, 10);
};

function WaitClick_WindowClose (Event)
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer1);
		ClickOK = false;
		ClickCANCEL = false;
		$("#"+Event+"").jqxWindow('close');
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true || ClickClose == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
			$("#"+Event+"").jqxWindow('close');
		}
	}, 10);
};

function WaitClick_TextArea (Event)
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer1);
		ClickOK = false;
		ClickCANCEL = false;
		$("#"+Event+"").focus();
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
			$("#"+Event+"").focus();
		}
	}, 10);
};

function WaitClick ()
{
	ClickTimer1 = setTimeout(function()
	{
		$("#Normal_Alert").jqxWindow('close');
		clearInterval(ClickTimer1);
		ClickOK = false;
		ClickCANCEL = false;
	},10000);
	ClickTimer2 = setInterval(function()
	{
		if (ClickOK == true)
		{
			ClickOK = false;
			ClickCANCEL = false;
			clearTimeout(ClickTimer1);
			clearInterval(ClickTimer2);
		}
	}, 10);
};

function displayEvent(event)
{
	if (event.type === 'close')
	{
		if (event.args.dialogResult.OK)
			ClickOK = true;
		else if (event.args.dialogResult.Cancel)
			ClickCANCEL = true;
		else
			ClickClose = true;
	}
};

$("#Normal_Alert").jqxWindow({
	theme: "energyblue",
	autoOpen: false,
	showCloseButton: false,
	maxHeight: 250,
	maxWidth: 350,
	height: "auto",
	width: 300,
	zIndex: 100001,
	resizable: false,
	isModal: true,
	modalOpacity: 0.3,
	okButton: $('#ok2'),
	initContent: function () {
		$("#ok2").jqxButton({ width: '100px', height: '30px', template: 'default'});
		$("#ok2").focus();
	}
});

$("#Ask_Alert").jqxWindow({
	theme: "energyblue",
	autoOpen: false,
	showCloseButton: false,
	maxHeight: 250,
	maxWidth: 350,
	height: "auto",
	width: 300,
	zIndex: 100001,
	resizable: false,
	isModal: true,
	modalOpacity: 0.3,
	okButton: $("#ok"),
	cancelButton: $("#cancel"),
	initContent: function () {
		$("#ok").jqxButton({ width: '100px', height: '30px', template: 'default' });
		$("#cancel").jqxButton({ width: '100px', height: '30px', template: 'default' });
		$("#ok").focus();
	}
});

function EventListeners()
{
	$("#Normal_Alert").on('close', function (event)
	{
		displayEvent(event);
	});
	$("#Ask_Alert").on('close', function (event)
	{
		displayEvent(event);
	});
};

function Alerts_Box (mess, type, ok)
{
	ClickOK = false;
	ClickCANCEL = false;
	// Info
	if (type == 1)
	{
		var send = "<li style=\"margin-right:5px; margin-top:4px;\">"+
		"<img width=\"20\" height=\"20\" src=\"images/info.png\" alt=\"Information!\" />"+
		"</li><li style=\"margin-top:5px;\">Informacion!</li>";
		if (ok)
		{
			$("#ask_alert_title").html(send);
			$("#ask_alert_title").css('background', '#49AFCD');
		}
		else
		{
			$("#normal_alert_title").html(send);
			$("#normal_alert_title").css('background', '#49AFCD');
		}
	}
	// Success
	if (type == 2)
	{
		var send = "<li style=\"margin-right:5px; margin-top:4px;\">"+
		"<img width=\"20\" height=\"20\" src=\"images/success.png\" alt=\"Success!\" />"+
		"</li><li style=\"margin-top:5px;\">Exito!</li>";
		if (ok)
		{
			$("#ask_alert_title").html(send);
			$("#ask_alert_title").css('background', '#6ABB6A');
		}
		else
		{
			$("#normal_alert_title").html(send);
			$("#normal_alert_title").css('background', '#6ABB6A');
		}
	}
	// Error
	if (type == 3)
	{
		var send = "<li style=\"margin-right:5px; margin-top:4px;\">"+
		"<img width=\"20\" height=\"20\" src=\"images/error.png\" alt=\"Error!\" />"+
		"</li><li style=\"margin-top:5px;\">Error!</li>";
		if (ok)
		{
			$("#ask_alert_title").html(send);
			$("#ask_alert_title").css('background', '#DA4F49');
		}
		else
		{
			$("#normal_alert_title").html(send);
			$("#normal_alert_title").css('background', '#DA4F49');
		}
	}
	// Warning
	if (type == 4)
	{
		var send = "<li style=\"margin-right:5px; margin-top:4px;\">"+
		"<img width=\"20\" height=\"20\" src=\"images/warning.png\" alt=\"Warning!\" />"+
		"</li><li style=\"margin-top:5px;\">Advertencia!</li>";
		if (ok)
		{
			$("#ask_alert_title").html(send);
			$("#ask_alert_title").css('background', '#FAA732');
		}
		else
		{
			$("#normal_alert_title").html(send);
			$("#normal_alert_title").css('background', '#FAA732');
		}
	}
	
	if (ok)
	{
		$("#ask_alert_text").html(mess);
		$("#Ask_Alert").jqxWindow('open');
		$('#ok').focus();
		$("#Ask_Alert").css('z-index', '100001');
		$("#Ask_Alert").css('height', 'auto');
		$('#ask_alert_container').css('height', 'auto');
	}
	else
	{
		$("#normal_alert_text").html(mess);
		$("#Normal_Alert").jqxWindow('open');
		$('#ok2').focus();
		$("#Normal_Alert").css('z-index', '100001');
		$("#Normal_Alert").css('height', 'auto');
		$('#normal_alert_container').css('height', 'auto');
	}
};

EventListeners();