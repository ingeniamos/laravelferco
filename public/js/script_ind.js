$(document).ready(function(){
	Cargar();
	CargarAgenda();
	$('.money').mask('000,000,000,000,000', {reverse: true});
});

function Cargar(){
	var tablaIndicadores = $("#datosindicadores");
	var selSubgrupos = $("#selSubgrupos");
	var routeIndicadores = "http://localhost/fercol/public/indicadores";
	var routeSubgrupos = "http://localhost/fercol/public/subgrupos";
	var routeUsers = "http://localhost/fercol/public/usuarios";
	var routeUnidades = "http://localhost/fercol/public/unidads";

	$("#datosindicadores").empty();
	//$("#selSubgrupos").empty();
	$(".select").empty();

	$.get(routeIndicadores, function(res) {
		$(res).each(function(key,value){
			tablaIndicadores.append("<tr><td>"+value.id+"</td><td>"+value.nombre+"</td><td class='money'>"+value.valor+"</td><td>"+value.fecha_inicio+"</td><td>"+value.fecha_fin+"</td><td><a href='#' id="+value.id+" OnClick='Mostrar(this);' data-toggle= 'modal' data-target='#myModal'﻿><i class='fa fa-pencil-square fa-lg' aria-hidden='true'></i></a> <a href='#' id="+value.id+" OnClick='Eliminar(this,\"indicador\");'><i class='fa fa-window-close fa-lg' aria-hidden='true'></i></a></td></tr>");
		});
	});

	$.get(routeSubgrupos, function(res) {
		$(res).each(function(key,value){
			selSubgrupos.append("<option value="+value.id+">"+value.nombre+"</option>");
			selSubgrupo.innerHTML +='<option value='+value.id+'>'+value.nombre+'</option>';
		});
	});

	$.get(routeUsers, function(res) {
		$(res).each(function(key,value){
			responsable.innerHTML +='<option value='+value.id+'>'+value.nombre+' '+value.apellidos+'</option>';
		});
	});

	$.get(routeUnidades, function(res) {
		$(res).each(function(key,value){
			selUnidad.innerHTML +='<option value='+value.id+'>'+value.nombre+'</option>';
		});
	});
}

function showMensaje(tipo){
	$("#mensaje").alert('close');
	var mensajes = $("#mensajes");
	mensajes.append("<div id='mensaje' style='display:none;' class='alert alert-"+tipo+" alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><span id='msj'></span></div>");
}

function mensajeModal(tipo){
	$("#mensajeModal").alert('close');
	var mensajes = $("#mensajesModal");
	mensajes.append("<div id='mensajeModal' style='display:none;' class='alert alert-"+tipo+" alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><span id='msjModal'></span></div>");
}

$('#regIndicador').click(function(){
  var nombre = $('#indicador').val();
  var valor = $('#valor').val();
  var escala = $('#selEscala').val();
  var subgrupo = $('#selSubgrupos').val();
  var usuario  = $('#usuario').val();
  var route = 'http://localhost/fercol/public/indicador';
  var token = $('#token').val();

	$.ajax({
	  url: route,
	  headers: {'X-CSRF-TOKEN': token},
	  type: 'POST',
	  dataType: 'json',
	  data: {
	  	nombre:nombre,
	  	valor:valor,
	  	escala:escala,
	  	query:'Predefinida',
	  	fecha_inicio:'2018-01-01',
	  	fecha_fin:'2018-12-01',
	  	unidad:1,
	  	responsable:usuario,
	  	subgrupo_id:subgrupo,
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

function Mostrar(btn){
	//$('#indicador').parent().addClass('col-5')
	var route = "http://localhost/fercol/public/indicador/"+btn.id+"/edit";

	$.get(route, function(res){
		$("#idIndicador").val(res.id);
		$("#indicador1").val(res.nombre);
		$("#valor1").val(res.valor);
		$("#selEscala1").val(res.escala);
		$("#selSubgrupo").val(res.subgrupo_id);
		$("#responsable").val(res.responsable);
		$("#selUnidad").val(res.unidad);
		$("#query").val(res.query);
		$("#fecha_inicio").val(res.fecha_inicio);
		$("#fecha_fin").val(res.fecha_fin);
	});
	
}

$("#actIndicador").click(function(){
	var value = $("#idIndicador").val();
	var usuario  = $('#usuario').val();
	var nombre = $("#indicador1").val();
	var valor = $("#valor1").val();
	var escala = $("#selEscala1").val();
	var subgrupo_id = $("#selSubgrupo").val();
	var responsable = $("#responsable").val();
	var unidad = $("#selUnidad").val();
	var query = $("#query").val();
	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();
	var route = "http://localhost/fercol/public/indicador/"+value+"";
	var token = $("#token").val();

	$.ajax({
		url: route,
		headers: {'X-CSRF-TOKEN': token},
		type: 'PUT',
		dataType: 'json',
		data: {
			nombre:nombre,
			valor:valor,
			escala:escala,
			subgrupo_id:subgrupo_id,
			responsable:responsable,
			unidad:unidad,
			query:query,
			fecha_inicio:fecha_inicio,
			fecha_fin:fecha_fin,
			create_id:usuario,
	  		update_id:usuario
		},
		success: function(msj){
			$("#myModal").modal('toggle');
			showMensaje('success');
		  	Cargar();
		  	$("#msj").html(msj.message);
		  	$('#mensaje').fadeIn();
		},
		error:function(msj) {
	 	mensajeModal('danger');
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