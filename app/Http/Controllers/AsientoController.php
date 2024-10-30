<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\asiento;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AsientoController extends Controller
{
    public $thisAtributos,$FromController = 'asiento';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create asiento', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read asiento', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update asiento', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete asiento', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new asiento())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$asientos = Asiento::with('no_nada');
        $asientos = Asiento::Where('id','>',0);
        return $asientos;

    }
    public function Filtros(&$asientos,$request){
        if ($request->has('search')) {
            $asientos = $asientos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $asientos = $asientos->orderBy($request->field, $request->order);
        }else
            $asientos = $asientos->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
//        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
//        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
//        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' asientos '));
        $asientos = $this->Mapear();
        $this->Filtros($asientos,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $asientos->paginate($perPage),
            'total'                 => $asientos->count(),

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
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:asientos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $asiento = asiento::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:asientos EXITOSO', 'asiento id:' . $asiento->id . ' | ' . $asiento->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $asiento->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:asientos');
        DB::beginTransaction();
        $asiento = asiento::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $asiento->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:asientos EXITOSO', 'asiento id:' . $asiento->id . ' | ' . $asiento->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $asiento->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($asientoid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:asientos');
        $asiento = asiento::find($asientoid);
        $elnombre = $asiento->nombre;
        $asiento->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:asientos', 'asiento id:' . $asiento->id . ' | ' . $asiento->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $asiento = asiento::whereIn('id', $request->id);
        $asiento->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.asiento')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
