<div class="tab-pane fade" id="subtemas" role="tabpanel" aria-labelledby="subtemas-tab">
            <br>
            <h4>Subtemas que se tocan en el documento</h4>
            <table class="table table-bordered">
                  @if (count($subtemas)==0)
                  <center>Sin Subtemas vinculados
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoSubtemaController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                  </center>
                  @else

                  <tr>
                        <th>ID</th>
                        <th>Subtema</th>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoSubtemaController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                        </td>

                  </tr>
                  <tr>
                        @foreach ($subtemas as $sub)
                        <td>
                        {{$sub->id_sub}}
                         </td>

                        <td>
                              {{$sub->subtema}}
                        </td>
                        <td>
                        </td>

                  </tr>
                  @endforeach @endif

            </table>
      </div>