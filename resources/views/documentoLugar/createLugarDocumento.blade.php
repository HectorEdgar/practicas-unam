<!--Esta es la vista que crea un nuevo lugar
y lo liga automaticamente con el documento -->

@extends('layouts.admin') 
@section('titulo')
    Crear Lugar
@endsection
@section('contenido')
@if(Session::has('messageError'))
<div class="alert alert-warning">
  <strong>¡Aviso!</strong> {{Session::get('messageError')}}
</div>
@endif
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
       <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}">Documentos</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/documento')}}/{{$documento->Id_doc}}">Resumen Doc. ({{ $documento->Id_doc}})</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/cntrl_lugar')}}/ligar/{{$documento->Id_doc}}">Administrar Documento y Lugar</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crear Lugar</li>
                </ol>
            </nav>
        <h3>Nuevo Lugar</h3>
        <h5>Este nuevo lugar se ligará automáticamente con el documento ({{$documento->Id_doc}})</h5>

        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 

        <!-- LOS DATOS SE MANDARÁN AL METODO UPDATE DEL CONTROLADOR DOCUMENTOlugar-->
      {!!Form::model($documento,['method'=>'PATCH','route'=>['cntrl_lugar.update',$documento->Id_doc]]) !!}
        {{Form::token()}}
       <div class="form-group">
            <label for="ubicacion">Ubicación</label>
            <input type="text" name="ubicacion" class="form-control" placeholder="Ubicación...">
        </div>
          <div class="form-group">
            <label for="pais">Elige un País:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
                    <label class="input-group-text" for="pais">País</label>
            </div>

                <select class="custom-select" name="pais" id="pais">
                    <option value="0" selected>-- Seleccione --</option>
                    @foreach($paises as $item)
                <option value="{{$item->id_pais}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>

          <div class="form-group">
            <label for="sector">Eliga una Región Geográfica:</label><br>
            <div class="input-group mb-3">
            <div class="input-group-prepend">
                    <label class="input-group-text" for="region">Región</label>
            </div>

                <select class="custom-select" name="region_geografica" id="region_geografica">
                    <option value="0" selected>-- Seleccione --</option>
                    @foreach($regiones as $item)
                    <option value="{{$item->id_region}}">{{$item->nombrereg}}</option>
                    @endforeach
                </select>
            </div>
        </div>
       <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a class="btn btn-danger" href="{{url('/cntrl_lugar')}}/ligar/{{$documento->Id_doc}}">Cancelar</a>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection