<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Ingeniamos - admin</title>

    <!-- Bootstrap Core CSS -->
    {!!Html::style('vendor/bootstrap/css/bootstrap.min.css')!!}
    {!!Html::style('vendor/metisMenu/metisMenu.min.css')!!}
    {!!Html::style('dist/css/sb-admin-2.css')!!}
    {!!Html::style('vendor/morrisjs/morris.css')!!}
    {!!Html::style('vendor/font-awesome/css/font-awesome.min.css')!!}
    {!!Html::style('vendor/jqwidgets/jqwidgets/styles/jqx.base.css')!!}
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">Admin Ingeniamos</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        {!! Auth::user()->name !!}
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        {{-- <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li> --}}
                        <li><a href="{!!URL::to('/logout')!!}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Buscador...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        @if (Auth::user()->id ==20)
                        <li>
                            <a href="#"><i class="fa fa-users fa-fw"></i> Coordinadores<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="{!!URL::to('/user')!!}"><i class="fa fa-list fa-fw"></i> Listados</a>
                                </li>
                                <li>
                                    <a href="{!!URL::to('/user/create')!!}"><i class="fa fa-plus fa-fw"></i> Agregar</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-user fa-fw"></i> Líderes<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="{!!URL::to('/lider')!!}"><i class="fa fa-list fa-fw"></i> Listados</a>
                                </li>
                                <li>
                                    <a href="{!!URL::to('/lider/create')!!}"><i class="fa fa-plus fa-fw"></i> Agregar</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        @endif
                        <li>
                            <a href="#"><i class="fa fa-address-card-o fa-fw"></i> Votantes<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="{!!URL::to('/votante')!!}"><i class="fa fa-list fa-fw"></i> Listado</a>
                                </li>
                                <li>
                                    <a href="{!!URL::to('/votante/create')!!}"><i class="fa fa-plus fa-fw"></i> Agregar</a>
                                </li>
                                
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-edit fa-fw"></i> Informes</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            @yield('content')
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    {!!Html::script('vendor/jquery/jquery.min.js')!!}
    {!!Html::script('vendor/bootstrap/js/bootstrap.min.js')!!}
    {!!Html::script('vendor/metisMenu/metisMenu.min.js')!!}
    {!!Html::script('vendor/raphael/raphael.min.js')!!}
    {!!Html::script('vendor/morrisjs/morris.min.js')!!}
    {!!Html::script('data/morris-data.js')!!}
    {!!Html::script('dist/js/sb-admin-2.js')!!}

    @section('scripts')
    @show    
    

</body>

</html>