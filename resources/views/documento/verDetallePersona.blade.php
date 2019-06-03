<div class="tab-pane fade" id="actorsocial" role="tabpanel" aria-labelledby="actorsocial-tab">
            <br>
            <table class="table table-bordered">
                  <h5>Actores Sociales / Personas</h5>
                  @if (count($actoresSociales)==0)
                  <center>Sin Actores Sociales vinculados
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoPersonaController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Cargo</th>
                        <td>
                              <a class="btn btn-primary" href="{{URL::action('DocumentoPersonaController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
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