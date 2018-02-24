@extends('layouts.principal')

@include('layouts.side')

@section('content')

<div class="row">
  <div class="col-md-12 animated fadeInUp">
    <div class="card">
      <div class="card-header alert alert-success">
        <b><i class="fa fa-dot-circle-o" aria-hidden="true"></i> DASHBOARD</b>
      </div>

      <div id="mensajes"></div>
      @include('alerts.errors')
	  @include('alerts.request')
      <div class="card-body">

      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
	{!!Html::script('js/script_semaforo.js')!!}
@endsection