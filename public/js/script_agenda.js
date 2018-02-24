// $(document).ready(function(){
// 	CargarAgenda();
// });

function CargarAgenda(){
	var ageActividades = $("#ageActividades");
	var routeAgenda = "http://localhost/fercol/public/agenda";
	$("#ageActividades").empty();

	$.get(routeAgenda, function(res) {
		$(res).each(function(key,value){
			var alerta = "danger";
			var d = new Date();
			var strDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
			var fecha1 = moment(strDate);
			var fecha2 = moment(value.fecha_limite);
			var diff = fecha2.diff(fecha1, 'days');
			if (diff < 0) {
				alerta = "dark";
			} else if (diff > 5 && diff <= 10) {
				alerta = "warning";
			} else if (diff > 10 ) {
				alerta = "primary";
			}
			ageActividades.append("<div class='alert alert-"+alerta+" alert-dismissible fade show' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button><b><i class='fa fa-clock-o' aria-hidden='true'></i> "+value.fecha_limite+"</b><br/>"+value.actividad+"<br/>Quedan "+diff+" días</div>");
		});
	});
}