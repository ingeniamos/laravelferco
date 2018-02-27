<!DOCTYPE html>
<html lang="es">
  <head>
    <title>CMI - Cuadro de Mando Integral</title>
    <meta name="description" content="Software CMI para el control del Cuadro de Mando Integral">
    <meta name="author" content="Gabriel Gamboa Z.">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    {{-- {!!Html::style('vendor/bootstrap/css/bootstrap.min.css')!!} --}}
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/miEstilo.css">

  </head>
  <body>
    <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <a class="navbar-brand" href="{!!URL::to('/')!!}">
          <img src="imgs/logo.png" width="200" class="d-inline-block align-top" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-md-end" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item active">
              <a class="btn btn-primary" href="user"><i class="fa fa-user-circle-o" aria-hidden="true"></i> Usuarios<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
              <a class="btn btn-danger" href="indicador"><i class="fa fa-line-chart" aria-hidden="true"></i> Indicadores</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-warning" href="mapa"><i class="fa fa-sitemap" aria-hidden="true"></i> Mapa</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-success" href="semaforo"><i class="fa fa-dot-circle-o" aria-hidden="true"></i> Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-info" href="software"><i class="fa fa-th" aria-hidden="true"></i> Software</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-dark" href="agendador"><i class="fa fa-calendar-o" aria-hidden="true"></i> Agenda</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-secondary" href="parametros"><i class="fa fa-cog" aria-hidden="true"></i></a>
            </li>
          </ul>
        </div>
      </nav>

      <div class="row">
      	<div class="col-md-3">
	      	@section('side')
	    	  @show
	      </div> 

        <div class="col-md-9">
          @yield('content')
        </div>

      </div> 
    </div> <!-- fin del container -->

      <nav class="navbar fixed-bottom navbar-dark bg-dark" style="border-radius: 0">
        <a class="navbar-brand" href="{!!URL::to('/')!!}">
          <img src="imgs/logo_pq.png" height="25" class="d-inline-block align-top" alt="">
        </a>
        <div class="float-right text-white">
          <a class="text-white" href="mailto:bugs@cmisoft.com">
            <i class="fa fa-envelope" aria-hidden="true"></i> bugs@cmisoft.com
          </a>
        </div>
      </nav>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    {!!Html::script('js/script_agenda.js')!!}
    {!!Html::script('js/moment.js')!!}
    <script src="http://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

    {{-- {!!Html::script('vendor/jquery/jquery.min.js')!!}
    {!!Html::script('vendor/bootstrap/js/bootstrap.min.js')!!} --}}
    @section('scripts')
    @show
  </body>
</html>