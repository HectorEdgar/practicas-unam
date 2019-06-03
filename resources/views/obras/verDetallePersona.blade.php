<div class="tab-pane fade" id="personas" role="tabpanel" aria-labelledby="personas-tab">
        <br>
            <table class="table table-bordered">
                  <h5>Actores Sociales / Personas</h5>
                  @if (count($actoresSociales)==0)
                  <center>Sin Actores Sociales vinculados
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('ObraPersonaController@ligarObra',$obra->id_obra)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Cargo</th>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('ObraPersonaController@ligarObra',$obra->id_obra)}}">Administrar</a>
                        </td>

                  </tr>
                  <tr>

                        @foreach ($actoresSociales as $actor)
                        <td>
                              {{$actor->Id_persona}}
                        </td>
                        <td>
                              {{ $actor->nombre }}
                        </td>
                        <td>
                              {{ $actor->apellidos}}
                        </td>
                        <td>
                              {{ $actor->cargo}}
                        </td>
                        <td>
                        </td>
                  </tr>
                  @endforeach @endif

            </table>  
</div>