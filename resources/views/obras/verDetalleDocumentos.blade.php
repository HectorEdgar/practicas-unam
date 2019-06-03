<div class="tab-pane fade show active" id="docs" role="tabpanel" aria-labelledby="docs-tab">
        <br>
                   <h4>Documentos en línea de la Obra</h4>
                   <table class="table table-bordered">
                         @if (count($documentos)==0)
                         <center>Sin documentos vinculados
                               <br>

                         </center>
                         @php
                         $flag = false;
                         @endphp

                         @else

                         @php
                         $flag = true;
                         @endphp

                         <tr>
                               <th>Id</th>
                               <th>Título</th>
                               <th>Estatus</th>
                               <th>Fecha publicación</th>
                               <th>Investigador</th>
                               <th>Revisado</th>
                           
                           
                         </tr>
                         <tr>
                                    @php
                                    $sinSubir = false;
                                    @endphp

                               @foreach ($documentos as $item)

                               @if($item->linea ==1)




                               <td>
                                     {{$item->Id_doc}}
                               </td>
                               <td>
                                     {{ $item->titulo}}
                               </td>
                               <td>
                                     {{ $item->status}}
                               </td>
                               <td>
                                     @if($item->fecha_publi == 1)

                                     {{ $item->fecha}}


                                     @else

                                    {{ $item->mes}} - {{ $item->mes2}} - {{ $item->anio}}



                                     @endif

                              </td>
                              <td>
                                          {{ $item->investigador}}
                              </td>
                               @if(Auth::user()->hasAnyRole(['admin','revisor','catalogador']))
                                @if ($item ->revisado==0)
                                <td>
                                    <a class="btn btn-danger" href="{{action('DocumentoObraController@validarRevision',['id'=>$item->Id_doc, 'id2'=>$item->fk_obra] )}}">
                                            No Revisado </a>
                                </td>
                                @else

                                <td>
                                    <a class="btn btn-success"  href="{{action('DocumentoObraController@validarRevision',['id'=>$item->Id_doc, 'id2'=>$item->fk_obra] )}}">
                                            Revisado </a>
                                </td>
                                @endif

                                @endif

                               <td>
                               <a href="{{asset('doc/')}}/{{$item->Id_doc}}.pdf" class="btn btn-outline-info col-sm-12 col-xs-12" >
                                               Visualizar</a>
                               </td>
                            
                         </tr>

                         @else

                         @php
                         $sinSubir = true;
                        @endphp



                         @endif
                         @endforeach @endif



                   </table>
                   <br>

                  @if($flag and $sinSubir )

                   <hr>
                   <h4>Documentos sin subir  de la Obra</h4>
                   <hr>
                   <br>
                   <table class="table table-bordered">

                              <tr>
                                          <th>Id</th>
                                          <th>Título</th>
                                          <th>Estatus</th>
                                          <th>Fecha de publicación</th>
                                          <th>Investigador</th>
                                          <th>Revisado</th>
                                    </tr>

                        @foreach ($documentos as $item)


                         @if ($item->linea ==0)


                         <tr>

                               <td>
                                     {{$item->Id_doc}}
                               </td>
                               <td>
                                     {{ $item->titulo}}
                               </td>
                               <td>
                                     {{ $item->status}}
                               </td>
                               <td>
                                          @if($item->fecha_publi == 1)

                                          {{ $item->fecha}}


                                          @else

                                         {{ $item->mes}} - {{ $item->mes2}} - {{ $item->anio}}



                                          @endif
                              </td>
                              <td>
                                          {{ $item->investigador}}
                               </td>

                               @if(Auth::user()->hasAnyRole(['admin','revisor','catalogador']))
                                @if ($item ->revisado==0)
                                <td>
                                          <a class="btn btn-danger"  href="{{action('DocumentoObraController@validarRevision',['id' => $item->Id_doc, 'id2' =>$item->fk_obra] )}}">
                                                  No Revisado </a>
                                      </td>
                                      @else

                                      <td>
                                          <a class="btn btn-success"  href="{{action('DocumentoObraController@validarRevision',['id' => $item->Id_doc, 'id2' =>$item->fk_obra] )}}">
                                                  Revisado </a>
                                      </td>
                                @endif

                                @endif

                             
                         </tr>
                         @endif
                         @endforeach

                   </table>
                   @endif

       </div>