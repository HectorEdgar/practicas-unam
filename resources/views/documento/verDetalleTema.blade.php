<div class="tab-pane fade" id="temas" role="tabpanel" aria-labelledby="temas-tab">
            <br>
            <h4>Temas que se tocan en el documento</h4>
            <table class="table table-bordered">
                  @if (count($temas)==0)
                  <center>Sin Temas vinculados
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoTemaController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                  </center>
                  @else


                  <tr>
                        <th>Tema</th>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoTemaController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                        </td>

                  </tr>
                  <tr>
                        @foreach ($temas as $tema)
                        <td>
                              {{$tema->descripcion}}
                        </td>
                        <td>
                        </td>
                  </tr>
                  @endforeach @endif


            </table>
      </div>