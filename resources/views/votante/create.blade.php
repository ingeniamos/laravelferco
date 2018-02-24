@extends('layouts.admin')

@section('content')	
	{!!Form::open(['route'=>'votante.store'])!!}
		<div id="msj-success" style="display:none;" class="alert alert-success alert-dismissible" role="alert">
			<strong>Votante agregado correctamente</strong>
		</div>

		<div id="msj-error" style="display:none;" class="alert alert-danger alert-dismissible" role="alert">
			<strong id="msj"></strong>
		</div>

		<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">

		@include('votante.forms.votante')

		<input type="hidden" name="user" value="{{ Auth::user()->id }}" id="user">
		{!! link_to('#', $title='Registrar', $attritubes=['id'=>'registro', 'class'=>'btn btn-primary'],$secure=null) !!}
	{!!Form::close()!!}
@endsection

@section('scripts')
	{!!Html::script('js/script5.js')!!}
@endsection