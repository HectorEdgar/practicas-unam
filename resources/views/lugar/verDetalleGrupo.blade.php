<div class="tab-pane fade show" id="grupos" role="tabpanel" aria-labelledby="grupos-tab">
 <br>
            <h4>Grupos relacionados con este lugar</h4>
            <table class="table table-bordered">
                  @if (count($grupos)==0)
                  <center>Sin grupos vinculados
                        <br>
                        <a class="btn btn-primary" href="{{URL::action('LugarEtniaController@ligarLugar',$lugar->id_lugar)}}">Administrar</a>
                  </center>
                  @else
                  <tr>
                        <th>Id</th>
                        <td>Grupo / Etnia</td>
                        <td>
                        <a class="btn btn-primary" href="{{URL::action('LugarEtniaController@ligarLugar',$lugar->id_lugar)}}">Administrar</a>
                        </td>
                  </tr>
                  <tr>
                        @foreach ($grupos as $item)
                        <td>
                              {{$item->id_etnia}}
                        </td>
                        <td>
                              {{ $item->nombre}}  /  {{ $item->nombre2}}
                        </td>
                        <td></td>
                  </tr>
                  @endforeach @endif

            </table>
</div>