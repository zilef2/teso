<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\PorcentajeInteresCuenta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PorcentajeInteresCuentaController extends Controller
{
    public $thisAtributos,$FromController = 'PorcentajeInteresCuenta';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create PorcentajeInteresCuenta', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read PorcentajeInteresCuenta', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update PorcentajeInteresCuenta', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete PorcentajeInteresCuenta', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new PorcentajeInteresCuenta())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$PorcentajeInteresCuentas = PorcentajeInteresCuenta::with('no_nada');
        $PorcentajeInteresCuentas = PorcentajeInteresCuenta::Where('id','>',0);
        return $PorcentajeInteresCuentas;

    }
    public function Filtros(&$PorcentajeInteresCuentas,$request){
        if ($request->has('search')) {
            $PorcentajeInteresCuentas = $PorcentajeInteresCuentas->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $PorcentajeInteresCuentas = $PorcentajeInteresCuentas->orderBy($request->field, $request->order);
        }else
            $PorcentajeInteresCuentas = $PorcentajeInteresCuentas->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' PorcentajeInteresCuentas '));
        $PorcentajeInteresCuentas = $this->Mapear();
        $this->Filtros($PorcentajeInteresCuentas,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $PorcentajeInteresCuentas->paginate($perPage),
            'total'                 => $PorcentajeInteresCuentas->count(),

            'breadcrumbs'           => [['label' => __('app.label.'.$this->FromController), 'href' => route($this->FromController.'.index')]],
            'title'                 => __('app.label.'.$this->FromController),
            'filters'               => $request->all(['search', 'field', 'order']),
            'perPage'               => (int) $perPage,
            'numberPermissions'     => $numberPermissions,
//            'losSelect'             => $losSelect,
        ]);
    }

    public function create(){}

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:PorcentajeInteresCuentas');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $PorcentajeInteresCuenta = PorcentajeInteresCuenta::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:PorcentajeInteresCuentas EXITOSO', 'PorcentajeInteresCuenta id:' . $PorcentajeInteresCuenta->id . ' | ' . $PorcentajeInteresCuenta->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $PorcentajeInteresCuenta->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:PorcentajeInteresCuentas');
        DB::beginTransaction();
        $PorcentajeInteresCuenta = PorcentajeInteresCuenta::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $PorcentajeInteresCuenta->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:PorcentajeInteresCuentas EXITOSO', 'PorcentajeInteresCuenta id:' . $PorcentajeInteresCuenta->id . ' | ' . $PorcentajeInteresCuenta->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $PorcentajeInteresCuenta->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($PorcentajeInteresCuentaid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:PorcentajeInteresCuentas');
        $PorcentajeInteresCuenta = PorcentajeInteresCuenta::find($PorcentajeInteresCuentaid);
        $elnombre = $PorcentajeInteresCuenta->nombre;
        $PorcentajeInteresCuenta->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:PorcentajeInteresCuentas', 'PorcentajeInteresCuenta id:' . $PorcentajeInteresCuenta->id . ' | ' . $PorcentajeInteresCuenta->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $PorcentajeInteresCuenta = PorcentajeInteresCuenta::whereIn('id', $request->id);
        $PorcentajeInteresCuenta->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.PorcentajeInteresCuenta')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
