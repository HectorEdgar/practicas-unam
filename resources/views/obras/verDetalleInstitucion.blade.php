<div class="tab-pane fade" id="institucion" role="tabpanel" aria-labelledby="institucion-tab">
         <br>
            <h4>Instituciones que se tocan en el documento</h4>
            <table class="table table-bordered">
                  @if (count($instituciones)==0)
                  <center>Sin instituciones vinculadas
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('ObraInstitucionController@ligarObra',$obra->id_obra)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Siglas</th>
                        <th>País</th>
                        <th>Localidad</th>
                        <th>Relación</th>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('ObraInstitucionController@ligarObra',$obra->id_obra)}}">Administrar</a>
                        </td>
                  </tr>
                  <tr>
                        @foreach ($instituciones as $inst)
                        <td>
                              {{$inst->id}}
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
                         <td>@if($inst->extra==1) 
                              <p style="color:darkgreen">
                                   <strong>Directa</strong></p>
                              @elseif($inst->extra==2)
                              <p style="color:darkblue"><strong>Indirecta</strong></p>
                              
                              @endif
                        </td>
                        <td></td>
                  </tr>
                  @endforeach @endif

            </table>
</div>