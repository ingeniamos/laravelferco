$('#registro').click(function(){
  var nombre = $('#nombre').val();
  var apellido = $('#apellido').val();
  var cedula = $('#cedula').val();
  var telefono = $('#telefono').val();
  var puesto_id = $('#puesto_id').val();
  var lider_id = $('#lider_id').val();
  var email = $('#email').val();
  var user_id = $('#user').val();
  var route = 'http://localhost/blogs/public/votante';
  var token = $('#token').val();

	$.ajax({
	  url: route,
	  headers: {'X-CSRF-TOKEN': token},
	  type: 'POST',
	  dataType: 'json',
	  data: {
	  	nombre:nombre,
		apellido:apellido,
		cedula:cedula,
		telefono:telefono,
		puesto_id:puesto_id,
		lider_id:lider_id,
		email:email,
		user_id:user_id
	  },

	  success:function(){
	  	$('#msj-success').fadeIn();
	  },
	  error:function(msj) {var errormessages = "";
        $.each(msj.responseJSON, function(i, field){
            errormessages+="<li>"+field+"</li>";
        });

        $('#msj').html(
            "<ul>"+errormessages+"</ul>"
        );

	  	$("#msj-error").fadeIn();
	  }

	});
});