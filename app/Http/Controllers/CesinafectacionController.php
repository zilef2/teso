<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\cesinafectacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CesinafectacionController extends Controller
{
    public $thisAtributos,$FromController = 'cesinafectacion';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create cesinafectacion', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read cesinafectacion', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update cesinafectacion', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete cesinafectacion', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new cesinafectacion())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$cesinafectacions = Cesinafectacion::with('no_nada');
        $cesinafectacions = Cesinafectacion::Where('id','>',0);
        return $cesinafectacions;

    }
    public function Filtros(&$cesinafectacions,$request){
        if ($request->has('search')) {
            $cesinafectacions = $cesinafectacions->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $cesinafectacions = $cesinafectacions->orderBy($request->field, $request->order);
        }else
            $cesinafectacions = $cesinafectacions->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' cesinafectacions '));
        $cesinafectacions = $this->Mapear();
        $this->Filtros($cesinafectacions,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $cesinafectacions->paginate($perPage),
            'total'                 => $cesinafectacions->count(),

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
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:cesinafectacions');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $cesinafectacion = cesinafectacion::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:cesinafectacions EXITOSO', 'cesinafectacion id:' . $cesinafectacion->id . ' | ' . $cesinafectacion->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $cesinafectacion->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:cesinafectacions');
        DB::beginTransaction();
        $cesinafectacion = cesinafectacion::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $cesinafectacion->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:cesinafectacions EXITOSO', 'cesinafectacion id:' . $cesinafectacion->id . ' | ' . $cesinafectacion->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $cesinafectacion->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($cesinafectacionid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:cesinafectacions');
        $cesinafectacion = cesinafectacion::find($cesinafectacionid);
        $elnombre = $cesinafectacion->nombre;
        $cesinafectacion->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:cesinafectacions', 'cesinafectacion id:' . $cesinafectacion->id . ' | ' . $cesinafectacion->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $cesinafectacion = cesinafectacion::whereIn('id', $request->id);
        $cesinafectacion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.cesinafectacion')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
