@extends('layouts.principal')

@include('layouts.side')

@section('content')

@include('agenda.modal')
@include('user.modal')

<div class="row">
  <div class="col-md-12 animated fadeInUp">
    <div class="card">
	      <div class="card-header alert alert-primary">
	        <b><i class="fa fa-user-circle" aria-hidden="true"></i> USUARIOS</b>
	      </div>

      	<div id="mensajes"></div>

      	<div class="card-body">

      		{!! Form::open() !!}
	          <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
	          <div class="form-row align-items-center">
	            @include('user.forms.form1')
	            <div class="col-2">
	              <div>&nbsp;</div>
	              {!! link_to('#', $title='Añadir', $attributes=['id'=>'regUsuario', 'class'=>'btn btn-info'],$secure=null) !!}
	            </div>
	          </div>
	        {!! Form::close() !!}

	        <table class="table" style="margin-top: 20px">
	          <thead class="thead-default">
	            <th>id</th>
	            <th>Nombre</th>
	            <th>Apellido</th>
	            <th>Correo</th>
	            <th width="15%"></th>
	          </thead>
	          <tbody id="datosusuarios"></tbody>
	        </table>

			{{-- <table class="table">
				<thead>
					<th>Cod.</th>
					<th>Nombre</th>
					<th>Correo</th>
					<th>Operación</th>
				</thead>
				<tbody>
					@foreach ($users as $user)
					<tr>
						<td>{{$user->id}}</td>
						<td>{{$user->name}}</td>
						<td>{{$user->email}}</td>
						<td>
						{!! link_to_route('user.edit', $title = 'Editar', $parameters = $user->id, $attributes = ['class'=>'btn btn-primary']) !!}
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			{!! $users->render() !!} --}}

		</div>
	</div>
  </div>
</div>

@endsection

@section('scripts')
  {!!Html::script('js/script_user.js')!!}
@endsection