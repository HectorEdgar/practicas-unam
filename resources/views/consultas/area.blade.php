@extends('layouts.admin')
@section('titulo')
    Área de Consultas
@endsection
@section('contenido')
@if(Session::has('message'))
<div class="alert alert-warning">
  <strong>Sin resultados!</strong> {{Session::get('message')}}
</div>
@endif
<h1>Área de Consultas</h1>

<div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <!--Etiquetas de breadcrum-->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Área de Consultas</li>
                </ol>
            </nav>
            <div class="clearfix"><br></div><div class="clearfix"></div>
            
        </div>
</div>

<ul class="nav nav-pills nav-justified container-fluid" id="mistabs" role="tablist">
      <li class="nav-item mr-1">
            <button class="btn btn-primary container-fluid" id="tipo-tab" data-toggle="tab" href="#tipo" role="tab" aria-controls="tipo"
                  aria-selected="false">Tipo de Doc.</button>
      </li>
      <li class="nav-item mr-1">
            <button class="btn btn-danger container-fluid" id="id-tab" data-toggle="tab" href="#id" role="tab" aria-controls="id"
                  aria-selected="false">Id del Doc.</button>
      </li>
     
        <li class="nav-item mr-1">
            <button class="btn btn-success container-fluid" id="status-tab" data-toggle="tab" href="#status" role="tab" aria-controls="status"
                  aria-selected="false">Estatus de Revisión</button>
      </li>

      
      
</ul>
<br><br>
<div class="tab-content" id="myTabContent">
<div class="tab-pane fade show active" id="tipo" role="tabpanel" aria-labelledby="tipo-tab">
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <h3><strong>Consultar por Tipo de Documento</strong> </h3>Selecciona:
        {!! Form::open(array('url'=>'consultas','method'=>'GET','autocomplete'=>'off','role'=>'search')) !!}
        <div class="row">
                <div class="col-sm-8">
                            <label for="tipo"><strong>Tipo de Proyecto</strong></label>
                            <select class="custom-select" name="tipo" id="tipo">
                                    @foreach ($categorias as $item)
                                    <option value="{{ $item->id_cata_doc }}">{{$item->tipo_doc }}</option>
                                    @endforeach
                            </select>
                </div>
                <div class="col-sm-4">
                        <label for="tipo"><strong><br></strong></label>
                        <div class="form-group"><button type="submit" class="form-control">Buscar</button></div>
                </div>
        </div>
        {{Form::close()}}

    </div>    

</div>

<div class="tab-pane fade" id="id" role="tabpanel" aria-labelledby="id-tab">

    <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <h3><strong>Consulta por el ID del Documento</strong> </h3><br>Ingrese:
    {!! Form::open(array('url'=>'consultas','method'=>'GET','autocomplete'=>'off','role'=>'search')) !!}
    <div class="row">
                <div class="col-sm-8">
                <label ><strong><br></strong></label>
                <strong>Id del Documento</strong><br>
                <input type="number" class="form-control" placeholder="Id del Documento" name="busqueda" required>
                </div>

                <div class="col-sm-4">
                <label ><strong><br></strong></label>
                <div class="form-group"><button type="submit" class="form-control">Buscar</button></div>
                </div>
    </div>
    {{Form::close()}}
</div>
</div>

<div class="tab-pane fade" id="status" role="tabpanel" aria-labelledby="status-tab">
        
    <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h3><strong>Consulta por el Estatus de Revisión</strong> </h3><br>Selecciona:
            {!! Form::open(array('url'=>'consultas','method'=>'GET','autocomplete'=>'off','role'=>'search')) !!}
            <div class="row">
                        <div class="col-sm-8">
                        <label ><strong><br></strong></label>
                        <strong>Estatus</strong><br>
                        <select class="custom-select" name="estatus" id="estatus" for="consulta">
                                <option value="1">Revisados</option>
                                <option value="0">No Revisados</option>
                        </select>
                       <div class="text-center"><label for="consulta"><input type="checkbox" id="cbox1" value="0"  for="consulta"> Con proyecto</label></div>
                       <select class="custom-select" name="proyecto" id="proyecto" for="proyecto" >
                          
                           <option value="0"  selected="0"> - - Seleccione - - </option>
                            @foreach ($proyectos as $proye)
						    <option value="{{ $proye->id_proyecto }}">{{$proye->proyecto}}</option>
                        
                        

						@endforeach
                    </select>
                        </div>
                     
                        <div class="col-sm-4">
                        <label ><strong><br></strong></label>
                        
                        <div class="form-group"><br><button type="submit" class="form-control">Buscar</button></div>
                        </div>
            </div>
            {{Form::close()}}
        </div>
</div>


</div>

<script  type="text/javascript">

$("#proyecto").hide();

$("#cbox1").on( 'change', function() {
    if( $(this).is(':checked') ) {
        $("#proyecto").show();
        $(this).val(1);
        
    } else {
        $("#proyecto").hide();
        
        $(this).val(0);
    }
});

	</script>
@endsection