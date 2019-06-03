<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//EDGAR
Route::resource('autor','AutorController')->middleware('auth');
Route::resource('tema', 'TemaController')->middleware('auth');
Route::resource('eje', 'EjeController')->middleware('auth');
Route::resource('usuario', 'UsuarioController')->middleware('auth');
Route::resource('log', 'LogController')->middleware('auth');
Route::resource('archivo', 'ArchivoController')->middleware('auth');
Route::resource('descarga', 'DescargaController')->middleware('auth');
//Route::get('descarga/download/{ruta}', 'DescargaController@download');
Route::get('descargas/download/{ruta}', 'DescargaController@download');

//si no se ingresa una url valida redirecciona a home
//genera unos bugs :´v
//Route::get('/{slug?}', 'HomeController@index');

//Routh::auth();
Route::get('/', function () {
    return view('index');
});

Route::get('/home', function () {
    return view('index');
});
Route::get('logout', 'auth\LoginController@logout');

//YU
Route::resource('editor','EditorController')->middleware('auth');
Route::resource('paises', 'PaisesController')->middleware('auth');
Route::resource('institucion', 'InstitucionController')->middleware('auth');
Route::resource('proyecto', 'ProyectoController')->middleware('auth');
Route::resource('persona','PersonaController')->middleware('auth');
Route::resource('lugar','LugarController')->middleware('auth');
Route::resource('etnia','EtniaController')->middleware('auth');
Route::resource('subtema','SubtemaController')->middleware('auth');
Route::resource('obras','ObraController')->middleware('auth');


Route::get('consultas/resultados','ConsultasController@resultados')->middleware('auth');
Route::resource('consultas','ConsultasController')->middleware('auth');


Route::get('/verPdf/{id}','DocumentoController@verPdf')->name('verPdf')->middleware('auth');






Route::resource('cntrl_autor', 'DocumentoAutorController')->middleware('auth');
Route::get('cntrl_autor/ligar/{id}', 'DocumentoAutorController@ligarDocumento')->middleware('auth');
Route::get('cntrl_autor/nuevoAutor/{id}', 'DocumentoAutorController@nuevoAutorDocumento')->middleware('auth');

Route::resource('cntrl_editor', 'DocumentoEditorController')->middleware('auth');
Route::get('cntrl_editor/ligar/{id}', 'DocumentoEditorController@ligarDocumento')->middleware('auth');
Route::get('cntrl_editor/nuevoEditor/{id}', 'DocumentoEditorController@nuevoEditorDocumento')->middleware('auth');


Route::resource('cntrl_proyecto', 'DocumentoProyectoController')->middleware('auth');
Route::get('cntrl_proyecto/ligar/{id}', 'DocumentoProyectoController@ligarDocumento')->middleware('auth');
Route::delete('cntrl_proyecto/borrar/{id}/{id2}', 'DocumentoProyectoController@destroy2')->name('cntrl_proyecto.destroy2')->middleware('auth');

Route::resource('cntrl_persona', 'DocumentoPersonaController')->middleware('auth');
Route::get('cntrl_persona/ligar/{id}', 'DocumentoPersonaController@ligarDocumento')->middleware('auth');
Route::delete('cntrl_persona/borrar/{id}/{id2}', 'DocumentoPersonaController@destroy2')->name('cntrl_persona.destroy2')->middleware('auth');

Route::resource('cntrl_institucion', 'DocumentoInstitucionController')->middleware('auth');
Route::get('cntrl_institucion/ligar/{id}', 'DocumentoInstitucionController@ligarDocumento')->middleware('auth');
Route::delete('cntrl_institucion/borrar/{id}/{id2}', 'DocumentoInstitucionController@destroy2')->name('cntrl_institucion.destroy2')->middleware('auth');

Route::resource('cntrl_subtema', 'DocumentoSubtemaController')->middleware('auth');
Route::get('cntrl_subtema/ligar/{id}', 'DocumentoSubtemaController@ligarDocumento')->middleware('auth');
Route::delete('cntrl_subtema/borrar/{id}/{id2}', 'DocumentoSubtemaController@destroy2')->name('cntrl_subtema.destroy2')->middleware('auth');

Route::resource('cntrl_tema', 'DocumentoTemaController')->middleware('auth');
Route::get('cntrl_tema/ligar/{id}', 'DocumentoTemaController@ligarDocumento')->middleware('auth');
Route::delete('cntrl_tema/borrar/{id}/{id2}', 'DocumentoTemaController@destroy2')->name('cntrl_tema.destroy2')->middleware('auth');

Route::resource('cntrl_lugar', 'DocumentoLugarController')->middleware('auth');
Route::get('cntrl_lugar/ligar/{id}', 'DocumentoLugarController@ligarDocumento')->middleware('auth');
Route::delete('cntrl_lugar/borrar/{id}/{id2}', 'DocumentoLugarController@destroy2')->name('cntrl_lugar.destroy2')->middleware('auth');

Route::resource('cntrl_obra', 'DocumentoObraController')->middleware('auth');
Route::get('cntrl_obra/ligar/{id}', 'DocumentoObraController@ligarDocumento')->middleware('auth');
Route::delete('cntrl_obra/borrar/{id}/{id2}', 'DocumentoObraController@destroy2')->name('cntrl_obra.destroy2')->middleware('auth');


Route::resource('obra_eje', 'ObraEjeController')->middleware('auth');
Route::get('obra_eje/ligar/{id}', 'ObraEjeController@ligarObra')->middleware('auth');
Route::delete('obra_eje/borrar/{id}/{id2}', 'ObraEjeController@destroy2')->name('obra_eje.destroy2')->middleware('auth');

Route::resource('obra_lugar', 'ObraLugarController')->middleware('auth');
Route::get('obra_lugar/ligar/{id}', 'ObraLugarController@ligarObra')->middleware('auth');
Route::delete('obra_lugar/borrar/{id}/{id2}', 'ObraLugarController@destroy2')->name('obra_lugar.destroy2')->middleware('auth');

Route::resource('obra_obra', 'ObraObraController')->middleware('auth');
Route::get('obra_obra/ligar/{id}', 'ObraObraController@ligarObra')->middleware('auth');
Route::delete('obra_obra/borrar/{id}/{id2}', 'ObraObraController@destroy2')->name('obra_obra.destroy2')->middleware('auth');


Route::resource('obra_institucion', 'ObraInstitucionController')->middleware('auth');
Route::get('obra_institucion/ligar/{id}', 'ObraInstitucionController@ligarObra')->middleware('auth');
Route::delete('obra_institucion/borrar/{id}/{id2}', 'ObraInstitucionController@destroy2')->name('obra_institucion.destroy2')->middleware('auth');
Route::delete('obra_institucion/editar/{id}/{id2}', 'ObraInstitucionController@destroy3')->name('obra_institucion.destroy3')->middleware('auth');



Route::get('cntrl_institucion/nuevoInstitucion/{id}', 'DocumentoInstitucionController@nuevoInstitucionDocumento')->middleware('auth');
Route::get('cntrl_persona/nuevoPersona/{id}', 'DocumentoPersonaController@nuevoPersonaDocumento')->middleware('auth');
Route::get('cntrl_proyecto/nuevoProyecto/{id}', 'DocumentoProyectoController@nuevoProyectoDocumento')->middleware('auth');
Route::get('cntrl_subtema/nuevoSubtema/{id}', 'DocumentoSubtemaController@nuevoSubtemaDocumento')->middleware('auth');
Route::get('cntrl_tema/nuevoTema/{id}', 'DocumentoTemaController@nuevoTemaDocumento')->middleware('auth');
Route::get('cntrl_lugar/nuevoLugar/{id}', 'DocumentoLugarController@nuevoLugarDocumento')->middleware('auth');
Route::get('cntrl_obra/nuevoObra/{id}', 'DocumentoObraController@nuevoObraDocumento')->middleware('auth');

Route::resource('obra_proyecto', 'ObraProyectoController')->middleware('auth');
Route::get('obra_proyecto/ligar/{id}', 'ObraProyectoController@ligarObra')->middleware('auth');
Route::delete('obra_proyecto/borrar/{id}/{id2}', 'ObraProyectoController@destroy2')->name('obra_proyecto.destroy2')->middleware('auth');

Route::resource('obra_persona', 'ObraPersonaController')->middleware('auth');
Route::get('obra_persona/ligar/{id}', 'ObraPersonaController@ligarObra')->middleware('auth');
Route::delete('obra_persona/borrar/{id}/{id2}', 'ObraPersonaController@destroy2')->name('obra_persona.destroy2')->middleware('auth');

Route::resource('obra_tema', 'ObraTemaController')->middleware('auth');
Route::get('obra_tema/ligar/{id}', 'ObraTemaController@ligarObra')->middleware('auth');
Route::delete('obra_tema/borrar/{id}/{id2}', 'ObraTemaController@destroy2')->name('obra_tema.destroy2')->middleware('auth');

Route::get('obra_eje/nuevoObra/{id}', 'ObraEjeController@nuevoObraEje')->middleware('auth');
Route::get('obra_obra/nuevoObra/{id}', 'ObraObraController@nuevoObraObra')->middleware('auth');
Route::get('obra_institucion/nuevoObra/{id}', 'ObraInstitucionController@nuevoObraInstitucion')->middleware('auth');
Route::get('obra_tema/nuevoObra/{id}', 'ObraTemaController@nuevoObraTema')->middleware('auth');
Route::get('obra_persona/nuevoObra/{id}', 'ObraPersonaController@nuevoObraPersona')->middleware('auth');
Route::get('obra_proyecto/nuevoObra/{id}', 'ObraProyectoController@nuevoObraProyecto')->middleware('auth');
Route::get('obra_lugar/nuevoObra/{id}', 'ObraLugarController@nuevoObraLugar')->middleware('auth');
Route::get('obra_lugar/vincular/{id}/{id2}', 'ObraLugarController@vincular')->middleware('auth');
Route::get('obra_lugar/editar/{id}/{id2}', 'ObraLugarController@editar')->middleware('auth');
Route::post('obra_lugar/editarVinculo/{id}', 'ObraLugarController@editarVinculo')->middleware('auth');

Route::resource('lugar_etnia', 'LugarEtniaController')->middleware('auth');
Route::get('lugar_etnia/ligar/{id}', 'LugarEtniaController@ligarLugar')->middleware('auth');
Route::delete('lugar_etnia/borrar/{id}/{id2}', 'LugarEtniaController@destroy2')->name('lugar_etnia.destroy2')->middleware('auth');
Route::get('lugar_etnia/nuevoEtnia/{id}', 'LugarEtniaController@nuevoLugarEtnia')->middleware('auth');
Route::get('lugar_etnia/vincular/{id}/{id2}', 'LugarEtniaController@vincular')->middleware('auth');
Route::get('lugar_etnia/editar/{id}/{id2}', 'LugarEtniaController@editar')->middleware('auth');
Route::post('lugar_etnia/editarVinculo/{id}', 'LugarEtniaController@editarVinculo')->middleware('auth');


//Jair
Route::resource('ponencia', 'PonenciaController')->middleware('auth');
Route::resource('categoriaDocumento', 'CategoriaDocumentoController')->middleware('auth');;
Route::resource('documento', 'DocumentoController')->middleware('auth');;
Route::get('show/{id}', 'DocumentoController@cambiarEstadoRevision')->middleware('auth');
Route::get('validar/{id}', 'ObraController@validarCoordenadas')->middleware('auth');
Route::get('validarRevision/{id}/{id2}', 'DocumentoObraController@validarRevision')->middleware('auth');


Auth::routes();

