<!DOCTYPE html>
<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--HOLA -->
    <title>SISTEMA DE CATALOGACIÓN PUIC - UNAM</title>
    <!-- Bootstrap-->
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <!-- Custom styles for this template-->
    <link href="{{asset('css/sb-admin.min.css')}}" rel="stylesheet">
    <!-- Custom fonts for this template-->
    <link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/estiloLibre.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/bootstrap-select.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
    <!--API DE GOOGLE MAPS-->
    <!-- EL TOKEN HA SIDO GENERADO POR YU BAN MENA CON SU CORREO-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDCkZRcQ_Uu4KSbgM6j-8Pe-SYt2zWzZQU"></script>

    <style>
            .tooltip {
                position: relative;
                display: inline-block;
            }

            .tooltip .tooltiptext {
                visibility: hidden;
                width: 140px;
                background-color: #555;
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px;
                position: absolute;
                z-index: 1;
                bottom: 150%;
                left: 50%;
                margin-left: -75px;
                opacity: 0;
                transition: opacity 0.3s;
            }

            .tooltip .tooltiptext::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: #555 transparent transparent transparent;
            }

            .tooltip:hover .tooltiptext {
                visibility: visible;
                opacity: 1;
            }
            </style>

</head>

<!-- ESTILO PARA CAMBIAR EL TAMAÑO DE LOS MENUSITOS DEL MENU DE ARRIBA -->
   <style>
        .dropdown-menu {
            width: 220px !important;
        }
    </style>


<body class="fixed-nav sticky-footer bg-dark" id="page-top">
    <!-- Navigation-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="{{url('/')}}">
            <img length="55px" width="40px" src="{{asset('imgs/logo_UNAM.png')}}"></img>PUIC-UNAM Catalogación
        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive"
            aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Documento">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#Documento" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-file"></i>
                        <span class="nav-link-text">Documento</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="Documento">
                        <li>
                            <a href="{{URL::action('DocumentoController@index')}}">Ver Documentos</a>
                        </li>
                        <li>
                            <a href="{{URL::action('DocumentoController@create')}}">Crear Documento</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Obras">
                    <a class="nav-link " href="{{URL::action('ObraController@index')}}"data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-file"></i>
                        <span class="nav-link-text">Obras</span>
                    </a>
                   
                </li>



                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Autor">
                    <a class="nav-link"  href="{{URL::action('AutorController@index')}}" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-user"></i>
                        <span class="nav-link-text">Autor</span>
                    </a>
              
                </li>

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Editor">
                    <a class="nav-link " href="{{URL::action('EditorController@index')}}" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-male"></i>
                        <span class="nav-link-text">Editor</span>
                    </a>
                    
                </li>

                


            @if(Auth::check())
                @if(!Auth::user()->hasRole('catalogador'))
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Proyecto">
                    <a class="nav-link "  href="{{URL::action('ProyectoController@index')}}" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-product-hunt"></i>
                        <span class="nav-link-text">Proyecto</span>
                    </a>
                   
                </li>
            @endif
                @endif

                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Institucion">
                    <a class="nav-link "  href="{{URL::action('InstitucionController@index')}}" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-italic"></i>
                        <span class="nav-link-text">Institución</span>
                    </a>
                  
                </li>
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="persona">
                    <a class="nav-link " href="{{URL::action('PersonaController@index')}}" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-thumbs-up"></i>
                        <span class="nav-link-text">Persona / Actor Social</span>
                    </a>
                   
                </li>

                @if(Auth::check())
                @if(!Auth::user()->hasRole('catalogador'))
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tema">
                    <a class="nav-link "  href="{{URL::action('TemaController@index')}}" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-book"></i>
                        <span class="nav-link-text">Tema</span>
                    </a>
                  
                </li>
                @endif
                @endif
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="lugar">
                    <a class="nav-link " href="{{URL::action('LugarController@index')}}" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-map"></i>
                        <span class="nav-link-text">Lugar</span>
                    </a>
                    
                </li>
                @if(Auth::check())
                    @if(!Auth::user()->hasRole('catalogador','admin'))
                        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="grupo">
                            <a class="nav-link " href="{{URL::action('EtniaController@index')}}" data-parent="#exampleAccordion">
                                <i class="fa fa-fw fa-users"></i>
                                <span class="nav-link-text">Grupo</span>
                            </a>
                        
                        </li>
                        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="paises">
                            <a class="nav-link " href="{{URL::action('PaisesController@index')}}" data-parent="#exampleAccordion">
                                <i class="fa fa-fw fa-map"></i>
                                <span class="nav-link-text">Paises</span>
                            </a>
                        
                        </li>
                    @endif
                @endif


                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="subtema">
                    <a class="nav-link "  href="{{URL::action('SubtemaController@index')}}" data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-bookmark"></i>
                        <span class="nav-link-text">Subtema</span>
                    </a>
                   
                </li>
            </ul>

            <ul class="navbar-nav sidenav-toggler">
                <li class="nav-item">
                    <a class="nav-link text-center" id="sidenavToggler">
                        <i class="fa fa-fw fa-angle-left"></i>
                    </a>
                </li>
            </ul>
            <!--menu de arriba-->
            <ul class="navbar-nav ml-auto">
                @if(Auth::check())
                    @if(Auth::user()->hasRole('catalogador'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle mr-lg-2" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-fw fa-users"></i>
                                <span class="d-lg-none">Catalogador
                                    <span class="badge badge-pill badge-primary"></span>
                                </span>
                                <span class="indicator text-primary d-none d-lg-block">
                                    <i class="fa fa-fw fa-circle"></i>
                                </span>
                            </a>


                            <div class="dropdown-menu" aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">Menú Catalogador:</h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{URL::action('DocumentoController@index', ['role' => 3])}}">
                                    <span class="text-success">
                                        <strong>
                                            <i class="fa fa-file fa-fw"></i>Mis documentos<br>Registrados</strong>
                                    </span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{url('/descarga')}}">
                                    <span class="text-danger">
                                        <strong>
                                            <i class="fa fa-list fa-fw"></i>Lista de Descarga</strong>
                                    </span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item small" href="#"></a>
                            </div>
                        </li>
                    @endif
                    @if(Auth::user()->hasRole('admin'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="alertsDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-fw fa-users"></i>
                                <span class="d-lg-none">Administrador
                                    <span class="badge badge-pill badge-warning"></span>
                                </span>
                                <span class="indicator text-warning d-none d-lg-block">
                                    <i class="fa fa-fw fa-circle"></i>
                                </span>
                            </a>


                            <div class="dropdown-menu" aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Menú de Administrador:</h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{URL::action('UsuarioController@index')}}">
                                    <span class="text-success">
                                        <strong>
                                            <i class="fa fa-users fa-fw"></i>Ver Usuarios</strong>
                                    </span>
                                </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{URL::action('UsuarioController@create')}}">
                                    <span class="text-success">
                                        <strong>
                                            <i class="fa fa-address-book fa-fw"></i>Registrar Usuario</strong>
                                    </span>
                                    </a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{URL::action('LogController@index')}}">
                                    <span class="text-danger">
                                        <strong>
                                            <i class="fa fa-exchange fa-fw"></i>Log de Cambios</strong>
                                    </span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{URL::action('DescargaController@index')}}">
                                    <span class="text-danger">
                                        <strong>
                                            <i class="fa fa-list fa-fw"></i>Lista de Descarga</strong>
                                    </span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item small" href="#"></a>
                            </div>
                        </li>
                    @endif
                    @if(Auth::user()->hasRole('revisor'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="alertsDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-fw fa-users"></i>
                                <span class="d-lg-none">Revisor
                                    <span class="badge badge-pill badge-warning"></span>
                                </span>
                                <span class="indicator text-warning d-none d-lg-block">
                                    <i class="fa fa-fw fa-circle"></i>
                                </span>
                            </a>


                            <div class="dropdown-menu" aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Menú de Revisor:</h6>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{URL::action('DescargaController@index')}}">
                                    <span class="text-danger">
                                        <strong>
                                            <i class="fa fa-list fa-fw"></i>Lista de Descarga</strong>
                                    </span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item small" href="#"></a>
                            </div>
                        </li>
                    @endif

                @endif
                <!--  <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0 mr-lg-2">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Search for...">
                            <span class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </li>
            -->
                @guest
                <li class="nav-item">
                    <a class="btn btn-primary" href="{{ route('login') }}">Iniciar Sesión</a>
                </li>

                @else
                <button class="btn btn-success">
                    {{ Auth::user()->name }}
                </button>
                <li class="nav-item">
                    <a class="nav-link" href="{{url('/logout')}}">
                        <i class="fa fa-fw fa-sign-out"></i>Salir
                    </a>
                </li>
                @endguest


            </ul>
        </div>
    </nav>
    <div class="content-wrapper">
        <div class="container-fluid">
            <!--Contenido-->
            <div class="container">
                <br>
                <br> @yield('contenido')
            </div>
            <!--Fin Contenido-->
        </div>
    </div>
    <footer class="sticky-footer">
        <div class="container">
            <div class="text-center">
                <small>Copyright © PUIC - UNAM 2018</small>
            </div>
        </div>
    </footer>
    <!-- jQuery-->
    <script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
    <!-- Bootstrap-->
    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('js/sb-admin.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-select.js')}}"></script>
    <!--STACK PERMITE UTILIZAR SCRIPTS PROPIOS-->


</body>



</html>