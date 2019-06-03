@extends('layouts.admin') 
@section('titulo')
    Crear Usuario
@endsection
@section('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/')}}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{URL::action('UsuarioController@index')}}">Usuario</a></li>
                <li class="breadcrumb-item active" aria-current="page">Agregar</li>
            </ol>
        </nav>
        <h3>Nuevo Usuario</h3>
        @if (count($errors)>0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif 
        {!!Form::open(array('url'=>'/usuario','method'=>'POST','autocomplete'=>'off')) !!} {{Form::token()}}
        {{ csrf_field() }}
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name">Nombre de Usuario</label>
            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus placeholder="Nombre.."> 
            @if ($errors->has('name'))
            <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
            </span> 
            @endif
        </div>
        
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email">Correo Electrónico</label>
            
            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="Correo electrónico.."> 
            @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span> 
            @endif  
        </div>
        
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label for="password" >Contraseña</label>
            <input id="password" type="password" class="form-control" name="password" required placeholder="Contraseña.."> 
            @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span> 
            @endif
        </div>
        
        <div class="form-group">
            <label for="password-confirm" >Confirma la Contraseña</label> 
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="Confirmar la contaseña...">
        </div>
        <div class="form-group">
            <label for="permisos">Permisos de:</label>
            <select class="form-control" name="permisos">
              <option value="admin">Administrador</option>
              <option selected value="catalogador">Catalogador</option>
              <option value="revisor">Revisor</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="reset" class="btn btn-secondary">Cancelar</button>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection