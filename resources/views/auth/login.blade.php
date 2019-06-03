@extends('layouts.admin')
@section('contenido')
<div class="row container-fluid">
    <div class="col-lg-3 col-md-2"></div>
    <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header text-white bg-primary text-center">
                Inicio de sesión
            </div>
            <div class="card-body ">
                <form method="POST" action="{{ route('login') }}" class="form-signin">
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>                    
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span> 
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password" class="col-md-4 control-label">Contraseña</label>
                            <input id="password" type="password" class="form-control" name="password" required> 
                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span> 
                            @endif
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Recordarme en este equipo
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            Iniciar Sesión
                        </button>
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-2"> <div class="clearfix"></div></div>
</div>
@endsection
