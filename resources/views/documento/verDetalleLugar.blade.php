<div class="tab-pane fade" id="lugares" role="tabpanel" aria-labelledby="lugares-tab">
            <br>
            <h4>Lugares que se tocan en el documento</h4>
            <table class="table table-bordered">
                  @if (count($lugares)==0)
                  <center>Sin Lugares vinculados
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoLugarController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>ID</th>
                        <th>Ubicación</th>
                        <th>País</th>
                        <th>Región Geográfica</th>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoLugarController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                        </td>
                  </tr>
                  <tr>
                        @foreach ($lugares as $lugar)
                        <td>
                              {{$lugar->id}}
                        </td>
                        <td>
                              {{$lugar->ubicacion}}
                        </td>
                        <td>
                              {{$lugar->pais}}
                        </td>
                        <td>
                              {{$lugar->region}}
                        </td>
                        <td>
                        </td>
                  </tr>
                  @endforeach @endif

            </table>
      </div>