<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\ordenpago;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrdenpagoController extends Controller
{
    public $thisAtributos,$FromController = 'ordenpago';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create ordenpago', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read ordenpago', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update ordenpago', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete ordenpago', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new ordenpago())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$ordenpagos = Ordenpago::with('no_nada');
        $ordenpagos = Ordenpago::Where('id','>',0);
        return $ordenpagos;

    }
    public function Filtros(&$ordenpagos,$request){
        if ($request->has('search')) {
            $ordenpagos = $ordenpagos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $ordenpagos = $ordenpagos->orderBy($request->field, $request->order);
        }else
            $ordenpagos = $ordenpagos->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' ordenpagos '));
        $ordenpagos = $this->Mapear();
        $this->Filtros($ordenpagos,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $ordenpagos->paginate($perPage),
            'total'                 => $ordenpagos->count(),

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
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:ordenpagos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $ordenpago = ordenpago::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:ordenpagos EXITOSO', 'ordenpago id:' . $ordenpago->id . ' | ' . $ordenpago->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $ordenpago->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:ordenpagos');
        DB::beginTransaction();
        $ordenpago = ordenpago::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $ordenpago->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:ordenpagos EXITOSO', 'ordenpago id:' . $ordenpago->id . ' | ' . $ordenpago->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $ordenpago->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($ordenpagoid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:ordenpagos');
        $ordenpago = ordenpago::find($ordenpagoid);
        $elnombre = $ordenpago->nombre;
        $ordenpago->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:ordenpagos', 'ordenpago id:' . $ordenpago->id . ' | ' . $ordenpago->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $ordenpago = ordenpago::whereIn('id', $request->id);
        $ordenpago->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.ordenpago')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
