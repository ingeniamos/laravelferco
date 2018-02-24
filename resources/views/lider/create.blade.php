@extends('layouts.admin')

@section('content')	
	{!!Form::open(['route'=>'lider.store'])!!}
		<div id="msj-success" style="display:none;" class="alert alert-success alert-dismissible" role="alert">
			<strong>Líder agregado correctamente</strong>
		</div>

		<div id="msj-error" style="display:none;" class="alert alert-danger alert-dismissible" role="alert">
			<strong id="msj"></strong>
		</div>

		<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">

		@include('lider.forms.lider')
		{!! link_to('#', $title='Registrar', $attritubes=['id'=>'registroLider', 'class'=>'btn btn-primary'],$secure=null) !!}
	{!!Form::close()!!}
@endsection

@section('scripts')
	{!!Html::script('js/script3.js')!!}
@endsection