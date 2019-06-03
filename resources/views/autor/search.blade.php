{!! Form::open(array('url'=>'autor','method'=>'GET', 'autocomplete'=>'off','role'=>'search'))!!}
<div class="form-group">
    <div class="input-group">
        <input type="text" name="searchText" class="form-control" placeholder="Buscar..." value="{{$searchText}}">
        <span class="input-group-btn">
            <button type="submit" class="btn btn-outline-info"><img length="30px" width="30px" src="{{asset('imgs/buscar.svg')}}"></img></button>
        </span>
    </div>

</div>



{{Form::close()}}