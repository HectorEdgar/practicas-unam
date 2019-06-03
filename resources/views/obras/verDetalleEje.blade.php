<div class="tab-pane fade show" id="ejes" role="tabpanel" aria-labelledby="ejes-tab">
 <br>
            <h4>Ejes de la Obra</h4>
            <table class="table table-bordered">
                  @if (count($ejes)==0)
                  <center>Sin ejes vinculados
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('ObraEjeController@ligarObra',$obra->id_obra)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Población</th>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('ObraEjeController@ligarObra',$obra->id_obra)}}">Administrar</a>
                        </td>
                  </tr>
                  <tr>
                        @foreach ($ejes as $item)
                        <td>
                              {{$item->Id_eje}}
                        </td>
                        <td>
                              {{ $item->nombre}}
                        </td>
                        <td>
                              {{ $item->descripcion}}
                        </td>
                        <td>
                              {{ $item->area}}
                        </td>
                        <td>
                              {{ $item->poblacion}}
                        </td>
                        <td></td>
                  </tr>
                  @endforeach @endif

            </table>
</div>