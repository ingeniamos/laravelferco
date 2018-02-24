@extends('layouts.admin')

@section('content')
	@include('alerts.request')
	{!!Form::model($user, ['route'=>['user.update',$user->id],'method'=>'PUT'])!!}
		@include('user.forms.user')
		{!!Form::submit('Update', ['class'=>'btn btn-primary'])!!}
	{!!Form::close()!!}

	{!!Form::open(['route'=>['user.destroy',$user->id],'method'=>'DELETE'])!!}
		{!!Form::submit('Borrar', ['class'=>'btn btn-danger'])!!}
	{!!Form::close()!!}

@stop