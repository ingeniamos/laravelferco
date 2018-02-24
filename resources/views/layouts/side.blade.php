@section('side')
  <div class="card">
    <div class="card-header bg-darkgray text-white">
    <b>Sesión</b>
    </div>
    <div class="card-body">
      <form id="form1" name="form1" method="post" action="index2.php?x=3">
        <div class="form-group">
          <label for="usuario">Bienvenid@</label>
          <input type="hidden" name="usuario" value="{!! Auth::user()->id !!}" id="usuario">
          <h2>{!! Auth::user()->nombre !!}</h2>
        </div>
        <a class="btn btn-info" href="{!!URL::to('/logout')!!}">Cerrar sesión</a>          
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header bg-warning"><b>Tareas pendientes</b></div>
    <div class="card-body" id="ageActividades">

      

      {{-- <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <b><i class="fa fa-clock-o" aria-hidden="true"></i> Sep-15-17</b><br/>Enviar consolidado semestre
      </div>

      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <b><i class="fa fa-clock-o" aria-hidden="true"></i> Sep-30-17</b><br/>Formular plan de compras
      </div> --}}

      <a class="btn btn-info" href="#">Ver todas</a>

    </div>
  </div>

  <div class="card">
    <div class="card-header bg-primary text-white"><b>Agenda</b></div>
    <div class="card-body">

      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <b><i class="fa fa-clock-o" aria-hidden="true"></i> Sep-08-17</b><br/>Cliente: Secretario Gobernación
      </div>

      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <b><i class="fa fa-clock-o" aria-hidden="true"></i> Oct-01-17</b><br/>Proveedor: Mariscos del Valle
      </div>

      <a class="btn btn-info" href="agenda.html">Ver todas</a>

    </div>
  </div>
  <div style="height: 80px"></div>
@endsection
{{-- 
@section('scripts')
  {!!Html::script('js/script_agenda.js?v=002')!!}
@endsection --}}