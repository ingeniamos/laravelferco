@extends('layouts.admin')

@section('content')
	<table class="table">
		<thead>
			<th>Nombre</th>
			<th>Apellido</th>
			<th>Cédula</th>
			<th>Teléfono</th>
			<th>Email</th>
			<th>Puesto</th>
			<th>Líder</th>
			<th></th>
		</thead>
		<tbody id="datos"></tbody>
	</table>

@endsection

@section('scripts')
	{!!Html::script('js/script6.js')!!}
@endsection