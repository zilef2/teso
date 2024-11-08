<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\sinafectacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SinafectacionController extends Controller
{
    public $thisAtributos,$FromController = 'sinafectacion';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create sinafectacion', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read sinafectacion', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update sinafectacion', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete sinafectacion', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new sinafectacion())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$sinafectacions = Sinafectacion::with('no_nada');
        $sinafectacions = Sinafectacion::Where('id','>',0);
        return $sinafectacions;

    }
    public function Filtros(&$sinafectacions,$request){
        if ($request->has('search')) {
            $sinafectacions = $sinafectacions->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $sinafectacions = $sinafectacions->orderBy($request->field, $request->order);
        }else
            $sinafectacions = $sinafectacions->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' sinafectacions '));
        $sinafectacions = $this->Mapear();
        $this->Filtros($sinafectacions,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $sinafectacions->paginate($perPage),
            'total'                 => $sinafectacions->count(),

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
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:sinafectacions');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $sinafectacion = sinafectacion::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:sinafectacions EXITOSO', 'sinafectacion id:' . $sinafectacion->id . ' | ' . $sinafectacion->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $sinafectacion->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:sinafectacions');
        DB::beginTransaction();
        $sinafectacion = sinafectacion::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $sinafectacion->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:sinafectacions EXITOSO', 'sinafectacion id:' . $sinafectacion->id . ' | ' . $sinafectacion->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $sinafectacion->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($sinafectacionid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:sinafectacions');
        $sinafectacion = sinafectacion::find($sinafectacionid);
        $elnombre = $sinafectacion->nombre;
        $sinafectacion->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:sinafectacions', 'sinafectacion id:' . $sinafectacion->id . ' | ' . $sinafectacion->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $sinafectacion = sinafectacion::whereIn('id', $request->id);
        $sinafectacion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.sinafectacion')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
