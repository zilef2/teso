<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\banco;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BancoController extends Controller
{
    public $thisAtributos,$FromController = 'banco';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create banco', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read banco', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update banco', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete banco', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new banco())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$bancos = Banco::with('no_nada');
        $bancos = Banco::Where('id','>',0);
        return $bancos;

    }
    public function Filtros(&$bancos,$request){
        if ($request->has('search')) {
            $bancos = $bancos->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $bancos = $bancos->orderBy($request->field, $request->order);
        }else
            $bancos = $bancos->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' bancos '));
        $bancos = $this->Mapear();
        $this->Filtros($bancos,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $bancos->paginate($perPage),
            'total'                 => $bancos->count(),

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
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:bancos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $banco = banco::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:bancos EXITOSO', 'banco id:' . $banco->id . ' | ' . $banco->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $banco->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:bancos');
        DB::beginTransaction();
        $banco = banco::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $banco->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:bancos EXITOSO', 'banco id:' . $banco->id . ' | ' . $banco->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $banco->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($bancoid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:bancos');
        $banco = banco::find($bancoid);
        $elnombre = $banco->nombre;
        $banco->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:bancos', 'banco id:' . $banco->id . ' | ' . $banco->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $banco = banco::whereIn('id', $request->id);
        $banco->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.banco')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
