<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\afectacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AfectacionController extends Controller
{
    public $thisAtributos,$FromController = 'afectacion';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create afectacion', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read afectacion', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update afectacion', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete afectacion', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new afectacion())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$afectacions = Afectacion::with('no_nada');
        $afectacions = Afectacion::Where('id','>',0);
        return $afectacions;

    }
    public function Filtros(&$afectacions,$request){
        if ($request->has('search')) {
            $afectacions = $afectacions->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $afectacions = $afectacions->orderBy($request->field, $request->order);
        }else
            $afectacions = $afectacions->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' afectacions '));
        $afectacions = $this->Mapear();
        $this->Filtros($afectacions,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $afectacions->paginate($perPage),
            'total'                 => $afectacions->count(),

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
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:afectacions');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $afectacion = afectacion::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:afectacions EXITOSO', 'afectacion id:' . $afectacion->id . ' | ' . $afectacion->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $afectacion->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:afectacions');
        DB::beginTransaction();
        $afectacion = afectacion::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $afectacion->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:afectacions EXITOSO', 'afectacion id:' . $afectacion->id . ' | ' . $afectacion->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $afectacion->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($afectacionid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:afectacions');
        $afectacion = afectacion::find($afectacionid);
        $elnombre = $afectacion->nombre;
        $afectacion->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:afectacions', 'afectacion id:' . $afectacion->id . ' | ' . $afectacion->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $afectacion = afectacion::whereIn('id', $request->id);
        $afectacion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.afectacion')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
