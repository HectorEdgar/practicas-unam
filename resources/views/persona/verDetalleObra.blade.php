<div class="tab-pane fade show" id="obras" role="tabpanel" aria-labelledby="obras-tab">
 <br>
 <h4>Obras relacionados con la Persona/Actor social</h4>
            <table class="table table-bordered">
                  @if (count($obras)==0)
                  <center>Sin obras vinculadas
                        <br>
                        <a href="{{url('/obras')}}" class="btn btn-warning">Ir a Obras</a>
                  </center>
                  @else
                  <tr>
                        <th>Id</th>
                        <td>Nombre de la Obra</td>
                        <td>Ir a la Obra</td>
                        <td>
                        <a  href="{{url('/obras')}}" class="btn btn-warning">Ir a Obras</a>
                        </td>
                  </tr>
                  <tr>
                        @foreach ($obras as $item)
                        <td>
                              {{$item->id_obra}}
                        </td>
                        <td>
                              {{ $item->nombre}}
                        </td>
                        <td><a href="{{URL::action('ObraController@show',$item->id_obra)}}">
					<img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
                            </a>
                        </td>
                        <td></td>
                  </tr>
                  @endforeach @endif

            </table>
</div>