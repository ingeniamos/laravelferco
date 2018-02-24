@extends('layouts.principal')

@include('layouts.side')

@section('content')

@include('indicador.modal')

<div class="row">
  <div class="col-md-12 animated fadeInUp">
    <div class="card">
      <div class="card-header alert alert-danger">
        <b><i class="fa fa-line-chart" aria-hidden="true"></i> INDICADORES</b>
      </div>

      <div id="mensajes"></div>

      <div class="card-body">

        {!! Form::open() !!}
          <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
          <div class="form-row align-items-center">
            @include('indicador.form.form1')
            <div class="col-2">
              <div>&nbsp;</div>
              {!! link_to('#', $title='Añadir', $attritubes=['id'=>'regIndicador', 'class'=>'btn btn-info'],$secure=null) !!}
            </div>
          </div>
        {!! Form::close() !!}

        <table class="table" style="margin-top: 20px">
          <thead class="thead-default">
            <th>id</th>
            <th>Indicador</th>
            <th>Valor</th>
            <th>Fecha Inicio</th>
            <th>Fecha Final</th>
            <th width="10%"></th>
          </thead>
          <tbody id="datosindicadores"></tbody>
        </table>

      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
  {!!Html::script('js/jquery.mask.min.js')!!}
  {!!Html::script('js/script_ind.js')!!}
@endsection