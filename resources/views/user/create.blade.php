@extends('layouts.admin')

@section('content')

	@include('alerts.request')
	{!!Form::open(['route'=>'user.store'])!!}
		@include('user.forms.user')
		{!!Form::submit('Registrar', ['class'=>'btn btn-primary'])!!}
	{!!Form::close()!!}

<!-- <form action="" method="POST">
	<div class="form-group">
		<label for="nombre">Nombre</label>
		<input type="text" class="form-control"></input>
	</div>
	<div class="form-group">
		<label for="correo">Correo</label>
		<input type="text" class="form-control"></input>
	</div>
	<div class="form-group">
		<label for="contraseña">Contraseña</label>
		<input type="password" class="form-control"></input>
	</div>
	<button class="btn btn-primary">Enviar</button>
</form> -->

@stop