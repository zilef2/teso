<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\ceconafectacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CeconafectacionController extends Controller
{
    public $thisAtributos,$FromController = 'ceconafectacion';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create ceconafectacion', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read ceconafectacion', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update ceconafectacion', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete ceconafectacion', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new ceconafectacion())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$ceconafectacions = Ceconafectacion::with('no_nada');
        $ceconafectacions = Ceconafectacion::Where('id','>',0);
        return $ceconafectacions;

    }
    public function Filtros(&$ceconafectacions,$request){
        if ($request->has('search')) {
            $ceconafectacions = $ceconafectacions->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $ceconafectacions = $ceconafectacions->orderBy($request->field, $request->order);
        }else
            $ceconafectacions = $ceconafectacions->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' ceconafectacions '));
        $ceconafectacions = $this->Mapear();
        $this->Filtros($ceconafectacions,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $ceconafectacions->paginate($perPage),
            'total'                 => $ceconafectacions->count(),

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
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:ceconafectacions');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $ceconafectacion = ceconafectacion::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:ceconafectacions EXITOSO', 'ceconafectacion id:' . $ceconafectacion->id . ' | ' . $ceconafectacion->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $ceconafectacion->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:ceconafectacions');
        DB::beginTransaction();
        $ceconafectacion = ceconafectacion::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $ceconafectacion->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:ceconafectacions EXITOSO', 'ceconafectacion id:' . $ceconafectacion->id . ' | ' . $ceconafectacion->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $ceconafectacion->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($ceconafectacionid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:ceconafectacions');
        $ceconafectacion = ceconafectacion::find($ceconafectacionid);
        $elnombre = $ceconafectacion->nombre;
        $ceconafectacion->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:ceconafectacions', 'ceconafectacion id:' . $ceconafectacion->id . ' | ' . $ceconafectacion->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $ceconafectacion = ceconafectacion::whereIn('id', $request->id);
        $ceconafectacion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.ceconafectacion')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
