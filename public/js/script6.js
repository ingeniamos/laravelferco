$(document).ready(function(){
	Cargar();
});

function Cargar(){
	var tablaDatos = $("#datos");
	var route = "http://localhost/blogs/public/votantes";

	$("#datos").empty();
	$.get(route, function(res) {
		$(res).each(function(key,value){
			tablaDatos.append("<tr><td>"+value.nombre+"</td><td>"+value.apellido+"</td><td>"+value.cedula+"</td><td>"+value.telefono+"</td><td>"+value.email+"</td><td>"+value.puesto_id+"</td><td>"+value.lider_id+"</td></tr>");
		});
	});
}
