<div class="tab-pane fade" id="ubicacion" role="tabpanel" aria-labelledby="ubicacion-tab">
     <br>
            <h4>Lugares que se tocan en el documento</h4>
            <table class="table table-bordered">
                  @if (count($lugares)==0)
                  <center>Sin Lugares vinculados
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('ObraLugarController@ligarObra',$obra->id_obra)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>ID</th>
                        <th>Ubicación</th>
                        <th>País</th>
                        <th>Región Geográfica</th>
                        <td>Latitud</td>
                        <td>Longitud</td>
                        <td>Complejo</td>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('ObraLugarController@ligarObra',$obra->id_obra)}}">Administrar</a>
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
                                {{$lugar->latitud}}
                        </td>
                        <td>
                                {{$lugar->longitud}}
                        </td>
                        <td>

                               @if($lugar->complejo==null)
                              <p><strong>- - - - - </strong></p>
                              @else
                             <strong>({{$lugar->complejo}})</strong><br>
                             @endif
                        </td>
                        <td>
                             
                        </td>
                  </tr>
                  @endforeach @endif

            </table>
</div>