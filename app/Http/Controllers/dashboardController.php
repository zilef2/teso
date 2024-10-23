<?php

namespace App\Http\Controllers;

use App\helpers\MyGlobalHelp;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\Comprobante;
use App\Models\Inspeccion;
use App\Models\Role;
use App\Models\transaccion;
use App\Models\User;
use Inertia\Inertia;

class dashboardController extends Controller
{
    public function Dashboard()
    {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' | inspeccions create | '));


        $numeroEntidades['transaccion'] = transaccion::count();
        $numeroEntidades['Comprobanteci'] = Comprobante::Where('codigo','ci')->count();
        $numeroEntidades['Comprobantece'] = Comprobante::Where('codigo','ce')->count();
        $numeroEntidades['Comprobanteaj'] = Comprobante::Where('codigo','aj')->count();
        $numeroEntidades['Comprobantean'] = Comprobante::Where('codigo','an')->count();
        $numeroEntidades['Comprobanteca'] = Comprobante::Where('codigo','ca')->count();
        return Inertia::render('Dashboard', [
            'users' => (int)User::count(),
            'roles' => (int) Role::count(),

            'rolesNameds' => Role::where('name', '<>', 'superadmin')->pluck('name'),

            'numberPermissions'   => $numberPermissions,
            'numeroEntidades'   => $numeroEntidades,
        ]);
    }
}
