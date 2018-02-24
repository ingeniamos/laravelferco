@extends('layouts.admin')

@section('content')
	<table class="table">
		<thead>
			<th>Cod.</th>
			<th>Nombre</th>
			<th>Apellido</th>
			<th>Cédula</th>
			<th>Teléfono</th>
			<th>Email</th>
			<th>Puesto</th>
			<th>Coordinador</th>
			<th></th>
		</thead>
		<tbody id="datos"></tbody>
		@foreach ($liders as $user)
			<tr>
				<td>{{$user->id}}</td>
				<td>{{$user->nombre}}</td>
				<td>{{$user->apellido}}</td>
				<td>{{$user->cedula}}</td>
				<td>{{$user->telefono}}</td>
				<td>{{$user->email}}</td>
				<td>{{$user->puesto_id}}</td>
				<td>{{$user->coordinador_id}}</td>
			</tr>
		@endforeach
	</table>
	{!! $liders->render() !!}

	<div id='jqxWidget'>
        <div id="grid">
        </div>
    </div>
@endsection

@section('scripts')
	{!!Html::script('js/script4.js?v=01')!!}
	{!!Html::script('vendor/jqwidgets/scripts/jquery-1.12.4.min.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxcore.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxdata.js')!!} 
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxbuttons.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxscrollbar.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxmenu.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxgrid.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxgrid.selection.js')!!} 
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxgrid.sort.js')!!} 
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxlistbox.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxdropdownlist.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxgrid.pager.js')!!}
    {!!Html::script('vendor/jqwidgets/jqwidgets/jqxgrid.columnsresize.js')!!} 
    {!!Html::script('vendor/jqwidgets/scripts/demos.js')!!}
@endsection