<div class="tab-pane fade" id="instituciones" role="tabpanel" aria-labelledby="instituciones-tab">
            <br>
            <h4>Instituciones que se tocan en el documento</h4>
            <table class="table table-bordered">
                  @if (count($instituciones)==0)
                  <center>Sin instituciones vinculadas
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoInstitucionController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Siglas</th>
                        <th>País</th>
                        <th>Localidad</th>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('DocumentoInstitucionController@ligarDocumento',$documento->Id_doc)}}">Administrar</a>
                        </td>
                  </tr>
                  <tr>
                        @foreach ($instituciones as $inst)
                        <td>
                              {{$inst->Id_institucion}}
                        </td>
                        <td>
                              {{ $inst->nombre }}
                        </td>
                        <td>
                              {{ $inst->siglas}}
                        </td>
                        <td>
                              {{ $inst->pais}}
                        </td>
                        <td>
                              {{ $inst->localidad}}
                        </td>
                        <td></td>
                  </tr>
                  @endforeach @endif

            </table>
      </div>