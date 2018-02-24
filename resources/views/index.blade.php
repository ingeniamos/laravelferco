@extends('layouts.principal')

@section('side')
  <div class="card">
    <div class="card-header bg-darkgray text-white">
    <b>Iniciar sesión</b>
    </div>
    <div class="card-body">
  		{!! Form::open(['route'=>'login.store']) !!}
			<div class="form-group">
				{!! Form::label('correo','Correo') !!}
				{!! Form::email('email',null,['class'=>'form-control']) !!}
			</div>
			<div class="form-group">
				{!! Form::label('contrasena','Contraseña') !!}
				{!! Form::password('password',['class'=>'form-control']) !!}
			</div>
			{!! Form::submit('Ingresar',['class'=>'btn btn-info']) !!}
		{!! Form::close() !!}
    </div>
  </div>
@endsection

@section('content')

<div class="row">
	<div class="col-md-12 animated fadeInUp">
	  <div class="card">
	    <div class="card-header alert alert-primary">
	      <b><i class="fa fa-home" aria-hidden="true"></i> BIENVENIDO</b>
	    </div>
	    @include('alerts.errors')
		@include('alerts.request')
	    <div class="card-body">
	    </div>
		</div>
	</div>
</div>

@stop