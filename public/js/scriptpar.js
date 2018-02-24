$(document).ready(function(){
	Cargar();
	CargarAgenda();
});

function Cargar(){
	var tablaGrupos = $("#datosgrupos");
	var tablaSubgrupos = $("#datossubgrupos");
	var selSubgrupos = $("#selSubgrupos");
	var routeGrupos = "http://localhost/fercol/public/grupos";
	var routeSubgrupos = "http://localhost/fercol/public/subgrupos";

	$("#datosgrupos").empty();
	$("#selSubgrupos").empty();
	$.get(routeGrupos, function(res) {
		$(res).each(function(key,value){
			tablaGrupos.append("<tr><td>"+value.id+"</td><td>"+value.nombre+"</td><td><a href='#' id="+value.id+" OnClick='Eliminar(this,\"grupo\");'><i class='fa fa-window-close fa-lg' aria-hidden='true'></i></a></td></tr>");
			selSubgrupos.append("<option value="+value.id+">"+value.nombre+"</option>");
		});
	});


	$("#datossubgrupos").empty();
	$.get(routeSubgrupos, function(res) {
		$(res).each(function(key,value){
			tablaSubgrupos.append("<tr><td>"+value.id+"</td><td>"+value.nombre+"</td><td>"+value.grupo_id+"</td><td><a href='#' id="+value.id+" OnClick='Eliminar(this,\"subgrupo\");'><i class='fa fa-window-close fa-lg' aria-hidden='true'></i></a></td></tr>");
		});
	});
}

function showMensaje(tipo){
	$("#mensaje").alert('close');
	var mensajes = $("#mensajes");
	mensajes.append("<div id='mensaje' style='display:none;' class='alert alert-"+tipo+" alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><span id='msj'></span></div>");
}

$('#regGrupo').click(function(){
  var dato = $('#grupo').val();
  var usuario  = $('#usuario').val();
  var route = 'http://localhost/fercol/public/grupo';
  var token = $('#token').val();

	$.ajax({
	  url: route,
	  headers: {'X-CSRF-TOKEN': token},
	  type: 'POST',
	  dataType: 'json',
	  data: {
	  	nombre:dato,
	  	create_id:usuario,
	  	update_id:usuario
	  },

	  success:function(msj){
	  	showMensaje('success');
	  	Cargar();
	  	$("#msj").html(msj.message);
	  	$('#mensaje').fadeIn();
	  },
	  error:function(msj) {
	 	showMensaje('danger');
	 	console.log(msj);
	 	var errormessages = "";
        $.each(msj.responseJSON, function(i, field){
            errormessages+="<li>"+field+"</li>";
        });

        $('#msj').html(
            "<ul>"+errormessages+"</ul>"
        );

	  	$("#mensaje").fadeIn();
	  }

	});
});

$('#regSubgrupo').click(function(){
  var dato = $('#subgrupo').val();
  var grupo = $('#selSubgrupos').val();
  var usuario  = $('#usuario').val();
  var route = 'http://localhost/fercol/public/subgrupo';
  var token = $('#token').val();

	$.ajax({
	  url: route,
	  headers: {'X-CSRF-TOKEN': token},
	  type: 'POST',
	  dataType: 'json',
	  data: {
	  	nombre:dato,
	  	grupo_id:grupo,
	  	create_id:usuario,
	  	update_id:usuario
	  },

	  success:function(msj){
	  	showMensaje('success');
	  	Cargar();
	  	$("#msj").html(msj.message);
	  	$('#mensaje').fadeIn();
	  },
	  error:function(msj) {
	 	showMensaje('danger');
	 	console.log(msj);
	 	var errormessages = "";
        $.each(msj.responseJSON, function(i, field){
            errormessages+="<li>"+field+"</li>";
        });

        $('#msj').html(
            "<ul>"+errormessages+"</ul>"
        );

	  	$("#mensaje").fadeIn();
	  }

	});
});

function Eliminar(btn,model){
	var route = "http://localhost/fercol/public/"+model+"/"+btn.id+"";
	var token = $("#token").val();

	$.ajax({
		url: route,
		headers: {'X-CSRF-TOKEN': token},
		type: 'DELETE',
		dataType: 'json',
		success: function(msj){
			showMensaje('warning');
		  	Cargar();
		  	$("#msj").html(msj.message);
		  	$('#mensaje').fadeIn();
		}
	});
}

// function Mostrar(btn){
// 	var route = "http://localhost/blogs/public/genero/"+btn.value+"/edit";
// 	$.get(route, function(res){
// 		$("#genre").val(res.genre);
// 		$("#id").val(res.id);
// 	});
// }

// $("#actualizar").click(function(){
// 	var value = $("#id").val();
// 	var dato = $("#genre").val();
// 	var route = "http://localhost/blogs/public/genero/"+value+"";
// 	var token = $("#token").val();

// 	$.ajax({
// 		url: route,
// 		headers: {'X-CSRF-TOKEN': token},
// 		type: 'PUT',
// 		dataType: 'json',
// 		data: {genre: dato},
// 		success: function(){
// 			Cargar();
// 			$("#myModal").modal('toggle');
// 			$("#msj-success").fadeIn();
// 		}
// 	});
	
// });