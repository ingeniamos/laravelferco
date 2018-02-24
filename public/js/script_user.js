$(document).ready(function(){
	Cargar();
	CargarAgenda();
});

function Cargar(){
	var tablaUsuarios = $("#datosusuarios");
	var routeUsers = "http://localhost/fercol/public/usuarios";
	$("#datosusuarios").empty();

	$.get(routeUsers, function(res) {
		$(res).each(function(key,value){
			tablaUsuarios.append("<tr><td>"+value.id+"</td><td>"+value.nombre+"</td><td>"+value.apellidos+"</td><td>"+value.email+"</td><td><a href='#' id="+value.id+" OnClick='Agendar(this);' data-toggle= 'modal' data-target='#agendaModal'﻿><i class='fa fa-calendar-plus-o fa-lg' aria-hidden='true'></i></a> <a href='#' id="+value.id+" OnClick='Mostrar(this);' data-toggle= 'modal' data-target='#myModal'﻿><i class='fa fa-pencil-square fa-lg' aria-hidden='true'></i></a> <a href='#' id="+value.id+" OnClick='Eliminar(this,\"user\");'><i class='fa fa-window-close fa-lg' aria-hidden='true'></i></a></td></tr>");
			responsable.innerHTML +='<option value='+value.id+'>'+value.nombre+' '+value.apellidos+'</option>';
		});
	});
}

function Mostrar(btn){
	//$('#indicador').parent().addClass('col-5')
	$("#mensajeModal").alert('close');
	var route = "http://localhost/fercol/public/user/"+btn.id+"/edit";
	$.get(route, function(res){
		$("#idUser").val(res.id);
		$("#nombreModal").val(res.nombre);
		$("#apellidosModal").val(res.apellidos);
		$("#cedulaModal").val(res.cedula);
		$("#passwordModal").val(res.password);
		$("#emailModal").val(res.email);
	});
}

function Agendar(btn){
	$("#mensajeModal").alert('close');
	$("#responsable").val(btn.id);
}

function showMensaje(tipo){
	$("#mensaje").alert('close');
	var mensajes = $("#mensajes");
	mensajes.append("<div id='mensaje' style='display:none;' class='alert alert-"+tipo+" alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><span id='msj'></span></div>");
}

function mensajeModal(tipo, modal){
	$("#mensajeModal").alert('close');
	var mensajes = $("#mensajesModal"+modal+"");
	mensajes.append("<div id='mensajeModal' style='display:none;' class='alert alert-"+tipo+" alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><span id='msjModal'></span></div>");
}

$('#regUsuario').click(function(){
  var nombre = $('#nombre').val();
  var apellidos = $('#apellidos').val();
  var cedula = $('#cedula').val();
  var password = $('#password').val();
  var email  = $('#email').val();
  // var usuario  = $('#usuario').val();
  var route = 'http://localhost/fercol/public/user';
  var token = $('#token').val();

	$.ajax({
	  url: route,
	  headers: {'X-CSRF-TOKEN': token},
	  type: 'POST',
	  dataType: 'json',
	  data: {
	  	nombre:nombre,
	  	apellidos:apellidos,
	  	cedula:cedula,
	  	email:email,
	  	password:password,
	  	level:2,
	  },

	  success:function(msj){
	  	showMensaje('success');
	  	Cargar();
	  	$("#msj").html(msj.message);
	  	$('#mensaje').fadeIn();
	  },
	  error:function(msj) {
	 	showMensaje('danger');
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

$("#actUser").click(function(){
	var value = $("#idUser").val();
	var nombre = $('#nombreModal').val();
  	var apellidos = $('#apellidosModal').val();
  	var cedula = $('#cedulaModal').val();
  	var password = $('#passwordModal').val();
  	var email  = $('#emailModal').val();
	var route = "http://localhost/fercol/public/user/"+value+"";
	var token = $("#token").val();

	$.ajax({
		url: route,
		headers: {'X-CSRF-TOKEN': token},
		type: 'PUT',
		dataType: 'json',
		data: {
			nombre:nombre,
			apellidos:apellidos,
		  	cedula:cedula,
		  	email:email,
		  	password:password,
		},
		success: function(msj){
			$("#myModal").modal('toggle');
			showMensaje('success');
		  	Cargar();
		  	$("#msj").html(msj.message);
		  	$('#mensaje').fadeIn();
		},
		error:function(msj) {
		 	mensajeModal('danger','User');
		 	var errormessages = "";
	        $.each(msj.responseJSON, function(i, field){
	            errormessages+="<li>"+field+"</li>";
	        });

	        $('#msjModal').html(
	            "<ul>"+errormessages+"</ul>"
	        );

		  	$("#mensajeModal").fadeIn();
	  	}

	});
	
});

//AGENDAR

$('#regAgenda').click(function(){
  var actividad = $('#actividad').val();
  var responsable = $('#responsable').val();
  var fecha  = $('#fecha').val();
  var usuario  = $('#usuario').val();
  var route = 'http://localhost/fercol/public/agenda';
  var token = $('#token').val();

	$.ajax({
		  url: route,
		  headers: {'X-CSRF-TOKEN': token},
		  type: 'POST',
		  dataType: 'json',
		  data: {
		  	actividad:actividad,
		  	estado:'Pendiente',
		  	responsable:responsable,
		  	fecha_limite:fecha,
		  	create_id:usuario,
		  	update_id:usuario,
		  },

	  	success: function(msj){
			$("#agendaModal").modal('toggle');
			showMensaje('success');
		  	Cargar();
		  	$("#msj").html(msj.message);
		  	$('#mensaje').fadeIn();
		},
		error:function(msj) {
		 	mensajeModal('danger','Agenda');
		 	var errormessages = "";
	        $.each(msj.responseJSON, function(i, field){
	            errormessages+="<li>"+field+"</li>";
	        });

	        $('#msjModal').html(
	            "<ul>"+errormessages+"</ul>"
	        );

		  	$("#mensajeModal").fadeIn();
	  	}

	});
});