@extends('layouts.principal')

@include('layouts.side')

@section('content')

  <div class="row">
    <div class="col-md-12 animated fadeInUp">
      <div class="card">
        <div class="card-header alert alert-secondary">
          <b><i class="fa fa-cog" aria-hidden="true"></i> PARÁMETROS</b>
        </div>

        <div id="mensajes"></div>      

        <div class="card-body row">
          <div class="col-md-6">
            <h4 class="display-5">Grupos de indicadores</h4>                  
            {!! Form::open() !!}
              <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
              <div class="form-row align-items-center">
                <div class="col-9">
                  <label class="sr-only" for="grupo">Nombre grupo</label>
                  <input type="text" class="form-control mb-2 mb-sm-0" id="grupo" placeholder="Nombre grupo">
                </div>
                <div class="col-2">
                  {!! link_to('#', $title='Añadir', $attritubes=['id'=>'regGrupo', 'class'=>'btn btn-info'],$secure=null) !!}
                </div>
              </div>
            {!! Form::close() !!}

            <table class="table" style="margin-top: 20px">
      				<thead class="thead-default">
      					<th>id</th>
      					<th>Grupo</th>
      					<th width="10%"></th>
      				</thead>
      				<tbody id="datosgrupos"></tbody>
      			</table>
          </div>

          <div class="col-md-6">
            <h4 class="display-5">Subgrupos</h4>
            {!! Form::open() !!}
              <div class="form-row align-items-center">
                <div class="col-5">
                  <label class="sr-only" for="subgrupo">Subgrupo</label>
                  <input type="text" class="form-control mb-2 mb-sm-0" id="subgrupo" placeholder="Nombre subgrupo">
                </div>
                <div class="col-4">
                  <select class="form-control" id="selSubgrupos">
                  </select>
                </div>
                <div class="col-2">
                  {!! link_to('#', $title='Añadir', $attritubes=['id'=>'regSubgrupo', 'class'=>'btn btn-info'],$secure=null) !!}
                </div>
              </div>
            {!! Form::close() !!}

            <table class="table" style="margin-top: 20px">
      				<thead class="thead-default">
      					<th>id</th>
      					<th>Subgrupo</th>
      					<th>Grupo</th>
      					<th width="10%"></th>
      				</thead>
      				<tbody id="datossubgrupos"></tbody>
      			</table>
          </div>

          <div class="col-md-6">
            <h4 class="display-5">Tipos de documentos</h4>                  
            <form>
              <div class="form-row align-items-center">
                <div class="col-9">
                  <label class="sr-only" for="tipoId">Tipo de documento</label>
                  <input type="text" class="form-control mb-2 mb-sm-0" id="tipoId" placeholder="Tipo de documento">
                </div>
                <div class="col-2">
                  <button type="submit" class="btn btn-info">Añadir</button>
                </div>
              </div>
            </form>

            <table class="table" style="margin-top: 20px">
              <thead class="thead-default">
                <tr>
                  <th>id</th>
                  <th>Tipo de documento</th>                        
                  <th width="10%"></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Cédula</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>2</td>
                  <td>Tarjeta de identidad</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>3</td>
                  <td>Cédula de extranjería</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
              </tbody>
            </table>
          </div>

          <div class="col-md-6">
            <h4 class="display-5">Paises</h4>                  
            <form>
              <div class="form-row align-items-center">
                <div class="col-9">
                  <label class="sr-only" for="paises">País</label>
                  <input type="text" class="form-control mb-2 mb-sm-0" id="paises" placeholder="País">
                </div>
                <div class="col-2">
                  <button type="submit" class="btn btn-info">Añadir</button>
                </div>
              </div>
            </form>
            <table class="table" style="margin-top: 20px">
              <thead class="thead-default">
                <tr>
                  <th>id</th>
                  <th>Nombre del país</th>
                  <th width="15%">Long,Lat</th>                      
                  <th width="10%"></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Colombia</td>
                  <td>1.09,1.01</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>2</td>
                  <td>Venezuela</td>
                  <td>1.09,1.01</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>3</td>
                  <td>Francia</td>
                  <td>1.09,1.01</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
              </tbody>
            </table>
          </div>

          <div class="col-md-6">
            <h4 class="display-5">Ciudades</h4>                  
            <form>
              <div class="form-row align-items-center">
                <div class="col-9">
                  <label class="sr-only" for="ciudad">Ciudad</label>
                  <input type="text" class="form-control mb-2 mb-sm-0" id="ciudad" placeholder="Ciudad">
                </div>
                <div class="col-2">
                  <button type="submit" class="btn btn-info">Añadir</button>
                </div>
              </div>
            </form>
            <table class="table" style="margin-top: 20px">
              <thead class="thead-default">
                <tr>
                  <th>id</th>
                  <th>Nombre ciudad</th>
                  <th width="10%">País</th>                      
                  <th width="10%"></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Cúcuta</td>
                  <td>1</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>2</td>
                  <td>Bucaramanga</td>
                  <td>1</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>3</td>
                  <td>Bogotá</td>
                  <td>1</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
              </tbody>
            </table>
          </div>

          <div class="col-md-6">
            <h4 class="display-5">Barrios</h4>                  
            <form>
              <div class="form-row align-items-center">
                <div class="col-9">
                  <label class="sr-only" for="barrios">Barrios</label>
                  <input type="text" class="form-control mb-2 mb-sm-0" id="barrios" placeholder="Barrios">
                </div>
                <div class="col-2">
                  <button type="submit" class="btn btn-info">Añadir</button>
                </div>
              </div>
            </form>
            <table class="table" style="margin-top: 20px">
              <thead class="thead-default">
                <tr>
                  <th>id</th>
                  <th>Nombre barrio</th>
                  <th width="10%">Ciudad</th>                      
                  <th width="10%"></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Quinta Oriental</td>
                  <td>1</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>2</td>
                  <td>Ceiba</td>
                  <td>1</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>3</td>
                  <td>Caobos</td>
                  <td>1</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
              </tbody>
            </table>
          </div>

          <div class="col-md-6">
            <h4 class="display-5">Módulos</h4>                  
            <form>
              <div class="form-row align-items-center">
                <div class="col-9">
                  <label class="sr-only" for="modulos">Módulos</label>
                  <input type="text" class="form-control mb-2 mb-sm-0" id="modulos" placeholder="Módulos">
                </div>
                <div class="col-2">
                  <button type="submit" class="btn btn-info">Añadir</button>
                </div>
              </div>
            </form>
            <table class="table" style="margin-top: 20px">
              <thead class="thead-default">
                <tr>
                  <th>id</th>
                  <th>Nombre módulo</th>
                  <th width="10%"></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Usuarios</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>2</td>
                  <td>Indicadores</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>                      
                <tr>
                  <td>3</td>
                  <td>Mapa estratégico</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>
                <tr>
                  <td>4</td>
                  <td>Semáforo</td>
                  <td><i class="fa fa-window-close fa-lg" aria-hidden="true"></i> <i class="fa fa-unlock-alt fa-lg" aria-hidden="true"></i></td>
                </tr>   
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>
  <div style="height: 80px"></div>

@endsection

@section('scripts')
	{!!Html::script('js/scriptpar.js')!!}
@endsection