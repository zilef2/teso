<?php

use App\Exports\FormExport;
use App\Http\Controllers\AreaInspeccionController;
use App\Http\Controllers\AspectoController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\EjemploController;
use App\Http\Controllers\IdeasController;
use App\Http\Controllers\SeguimientoAltController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\InspeccionsController;
use App\Http\Controllers\RegistrosmedioController;

use App\Http\Controllers\ZipController;
use App\Http\Controllers\SubiExcel;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

//Route::get('/', [FormularioController::class, 'welcome'])->name('welcome');
Route::get('/', function () { return redirect('/login'); });
//Route::get('/dashboard', [dashboardController::class, 'Dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [InspeccionsController::class, 'Index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/setLang/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return back();
})->name('setlang');

//,'handleErrorZilef'
//, 'verified'
Route::middleware(['auth'])->group(callback: function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/ejemplo', [EjemploController::class, 'ejemplo'])->name('ejemplo');

    Route::resource('/Inspeccion', InspeccionsController::class);
    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::resource('/AreaInspeccion', AreaInspeccionController::class);
    Route::resource('/Aspecto', AspectoController::class);
    Route::post('/inspeccion/destroy-bulk', [InspeccionsController::class, 'destroyBulk'])->name('inspeccion.destroy-bulk');
    Route::post('/inspeccion/firmando', [InspeccionsController::class, 'inspeccionfirmar'])->name('inspeccion.firmar');



    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
     Route::get('/subirexceles', [SubiExcel::class, 'subirexceles'])->name('subirexceles');
     Route::post('/uploadExcel', [SubiExcel::class, 'uploadExcel'])->name('uploadExcel');


    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');

    Route::resource('/parametro', ParametrosController::class);

    Route::get('/DB_info', [UserController::class,'todaBD']);
//    Route::get('/downloadAnexos', [UserController::class,'downloadAnexos'])->name('downloadAnexos');
    Route::get('/downClaro',function(){
        return Excel::download(new FormExport, 'BaseDatosInspecciones.xlsx');
    })->name('downClaro');

    Route::get('/CategoriasSimilares', [IdeasController::class, 'index'])->name('CategoriasSimilares');


    Route::resource('/inspeccion', InspeccionsController::class);

    Route::get('/CreateWindow2/{id}', [InspeccionsController::class, 'createPaso2'])->name('inspeccion.CreateWindow2');

    Route::match(['get', 'post'],'/createPasoContadorP1/{id}/{contador}', [InspeccionsController::class, 'createPasoContadorP1'])->name('createPasoContadorP1');
    Route::post('/FinishInspeccion/{id}/{contador}', [InspeccionsController::class, 'FinishInspeccion'])->name('FinishInspeccion');
    Route::get('/Informe/{id}', [InspeccionsController::class, 'Informe'])->name('Informe');
    Route::get('/Seguimiento/{id}', [SeguimientoAltController::class, 'Seguimiento'])->name('Seguimiento');
    Route::get('/Resultado/{id}', [\App\Http\Controllers\ResultadoController::class, 'Resultado'])->name('Resultado');
    Route::post('/GuardarResultado/{id}', [\App\Http\Controllers\ResultadoController::class, 'GuardarResultado'])->name('inspeccion.GuardarResultado');
    Route::post('/GuardarSeguimiento/{id}', [SeguimientoAltController::class, 'GuardarSeguimiento'])->name('inspeccion.GuardarSeguimiento');

    Route::get('/DescompresionDespliegue/{esAmbientePruebas}', [ZipController::class, 'DescompresionDespliegue']);

    Route::resource('/registrosmedio', RegistrosmedioController::class);
    Route::post('/registrosmedio/destroy-bulk', [RegistrosmedioController::class, 'destroyBulk'])->name('registrosmedio.destroy-bulk');


});
//aquipue
//aquipue


// <editor-fold desc="Artisan">
require __DIR__ . '/auth.php';
Route::get('/exception', function () {
    throw new Exception('Probando excepciones y enrutamiento. La prueba ha concluido exitosamente.');
});

Route::get('/foo', function () {
    if (file_exists(public_path('storage'))) {
        return 'Ya existe';
    }
    App('files')->link(
        storage_path('App/public'),
        public_path('storage')
    );
    return 'Listo';
});

Route::get('/clear-c', function () {
    Artisan::call('optimize');
    Artisan::call('optimize:clear');
    return "Optimizacion finalizada";
    // throw new Exception('Optimizacion finalizada!');
});

Route::get('/tmantenimiento', function () {
    echo Artisan::call('down --secret="token-it"');
    return "Aplicación abajo: token-it";
});
Route::get('/Arriba', function () {
    echo Artisan::call('up');
    return "Aplicación funcionando";
});

//</editor-fold>
