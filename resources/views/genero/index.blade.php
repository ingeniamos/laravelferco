@extends('layouts.admin')

@section('content')

	@include('genero.modal')

	<div id="msj-success" class="alert alert-success alert-dismissible" style="display: none" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			<span class="sr-only">Close</span>
		</button>
		Género actualizado correctamente
	</div>

	<div id="msj-done" class="alert alert-warning alert-dismissible" style="display: none" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			<span class="sr-only">Close</span>
		</button>
		Género eliminado correctamente
	</div>

	<table class="table">
		<thead>
			<th>Nombre</th>
			<th>Operaciones</th>
		</thead>
		<tbody id="datos"></tbody>
	</table>

@endsection

@section('scripts')
	{!!Html::script('js/script2.js')!!}
@endsection