<div class="tab-pane fade show active" id="docs" role="tabpanel" aria-labelledby="docs-tab">
        <br>
                   <h4>Documentos relacionados con el Autor</h4>
                  
                   <table class="table table-bordered">
                         @if (count($documentos)==0)
                         <center>Sin documentos vinculados
                               <br>
                               <a href="{{url('/documento')}}"class="btn btn-warning">Ir a Documentos</a>
                         </center>
                         @else
                         <tr>
                               <th>Id</th>
                               <td>Nombre del Documento</td>
                               <td>Ir al Documento</td>
                               <td>
                               <a href="{{url('/documento')}}" class="btn btn-warning">Ir a Documentos</a>
                               </td>
                         </tr>
                         <tr>
                               @foreach ($documentos as $item)
                               <td>
                                     {{$item->Id_doc}}
                               </td>
                               <td>
                                     {{ $item->titulo}}
                               </td>
                               <td><a href="{{URL::action('DocumentoController@show',$item->Id_doc)}}">
                           <img length="30px" width="30px" src="{{asset('imgs/ver.svg')}}" title="Ver Detalle"></img>
                                   </a>
                               </td>
                               <td></td>
                         </tr>
                         @endforeach @endif
       
                   </table>
       </div>