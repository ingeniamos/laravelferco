$('#registroLider').click(function(){
  var nombre = $('#nombre').val();
  var apellido = $('#apellido').val();
  var cedula = $('#cedula').val();
  var telefono = $('#telefono').val();
  var puesto_id = $('#puesto_id').val();
  var coordinador_id = $('#coordinador_id').val();
  var email = $('#email').val();
  var route = 'http://localhost/blogs/public/lider';
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
		coordinador_id:coordinador_id,
		email:email
	  },

	  success:function(){
	  	$('#msj-success').fadeIn();
	  },
	  error:function(msj) {
	  	var errormessages = "";
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