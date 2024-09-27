<?php

use App\Exports\FormExport;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\EjemploController;
use App\Http\Controllers\ParametrosController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\PorcentajeInteresCuentaController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\ConceptoflujoController;

use App\Http\Controllers\ZipController;
use App\Http\Controllers\SubiExcelController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

//Route::get('/', [FormularioController::class, 'welcome'])->name('welcome');
Route::get('/', function () { return redirect('/login'); });
//Route::get('/dashboard', [dashboardController::class, 'Dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [dashboardController::class, 'Dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

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

    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
     Route::get('/subirexceles', [SubiExcelController::class, 'subirexceles'])->name('subirexceles');
     Route::post('/upExCuentas', [SubiExcelController::class, 'upExCuentas'])->name('upExCuentas');
     Route::post('/upExTransacciones', [SubiExcelController::class, 'upExTransacciones'])->name('upExTransacciones');
     Route::post('/uploadFileComprobantes', [SubiExcelController::class, 'uploadFileComprobantes'])->name('uploadFileComprobantes');
     Route::post('/uploadFileBancos', [SubiExcelController::class, 'uploadFileBancos'])->name('uploadFileBancos');


    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');

    Route::resource('/parametro', ParametrosController::class);

    Route::get('/DB_info', [UserController::class,'todaBD']);
//    Route::get('/downloadAnexos', [UserController::class,'downloadAnexos'])->name('downloadAnexos');
//    Route::get('/downClaro',function(){
//        return Excel::download(new FormExport, 'BaseDatosInspecciones.xlsx');
//    })->name('downClaro');


//    Route::get('/Resultado/{id}', [\App\Http\Controllers\ResultadoController::class, 'Resultado'])->name('Resultado');

    Route::get('/DescompresionDespliegue/{esAmbientePruebas}', [ZipController::class, 'DescompresionDespliegue']);


    //<editor-fold desc="resources">
    Route::resource('/user', UserController::class);
    Route::resource('/cuenta', CuentaController::class);
    Route::resource('/transaccion', TransaccionController::class);
    Route::resource('/porcentajeInteresCuenta', PorcentajeInteresCuentaController::class);
    Route::resource('/Comprobante', ComprobanteController::class);
    Route::resource('/Conceptoflujo', ConceptoflujoController::class);
    //</editor-fold>

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
