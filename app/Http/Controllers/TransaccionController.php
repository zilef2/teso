<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\transaccion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransaccionController extends Controller
{
    public string $FromController = 'transaccion';
    public array $arrayBusque;
    public array $arrayFillableSearch;
    public array $thisAtributos;


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create transaccion', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read transaccion', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update transaccion', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete transaccion', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new transaccion())->getFillable();
        $this->thisAtributos = array_diff($this->thisAtributos, ['deleted_at']);
        $this->arrayBusque = [
            'search',
            'searchNumtransaccion',
            'searchBanco',
            'searchtipo',
        ];

        $this->arrayFillableSearch = [
            'codigo_transaccion_contable',
            'numero_transaccion_bancaria',
            'banco',
            'tipo_de_recurso',
        ];
    }


   private function Mapear($clase)
    {
//        $valorTotal = clone $clase;
//        $valorTotal =  $valorTotal->sum('valor_total_solicitatdo_por_necesidad');

        $Result = $clase->map(function ($clas) {
            $clas->cuenta = $clas->cuenta();
//            $clas->Categori = $clas->categoria();
//            $clas->proceso_que_solicita_presupuest = $clas->proceso_que_solicita_presupuesto();

//            $clas->procesos_involucrado = $clas->BDToString('procesos_involucrados');
//            $clas->plan_de_mejoramiento_al_que_apunta_la_necesida = $clas->BDToString('plan_de_mejoramiento_al_que_apunta_la_necesidad');
//            $clas->linea_del_plan_desarrollo_al_que_apunta_la_necesida = $clas->BDToString('linea_del_plan_desarrollo_al_que_apunta_la_necesidad');
            return $clas;
        });

        return $Result;
    }

    public function BusquedasText($transaccions,$arrayBusquedas,$request){
        foreach ($arrayBusquedas as $index => $busqueda) {
            $campo = $this->arrayFillableSearch[$index];
            if ($request->has($busqueda)) {
                $transaccions = $transaccions->where(function ($query) use ($request,$busqueda,$campo) {
                    $query->where($campo, 'LIKE', "%" . $request->{$busqueda} . "%")
                    ;
                });
            }
        }
        return $transaccions;
    }
    public function Filtros($request){
        $transaccions = transaccion::Query();
//        $transaccions = transaccion::All();

        if ($request->has(['field', 'order'])) {
            $transaccions = $transaccions->orderBy($request->field, $request->order);
        }else{
            $transaccions = $transaccions->orderBy('updated_at', 'DESC');
        }
        $transaccions = $transaccions->get();
        $transaccions = $this->BusquedasText($transaccions,$this->arrayBusque,$request);
        return $transaccions;
    }

//    public function losSelect()
//    {
//        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
//        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
//        return $no_nadasSelect;
//    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' transaccions '));
        $laclase = $this->Mapear($this->Filtros($request));
//        $losSelect = $this->losSelect();

        $perPage = $request->has('perPage') ? $request->perPage : 100;
        $total = $laclase->count();
        $page = request('page', 1);
        $fromController = new LengthAwarePaginator(
            $laclase->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $filters = ['search', 'field', 'order'];
        $filters = array_merge($this->arrayBusque,$filters);

        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $fromController,
            'total'                 => $total,

            'breadcrumbs'           => [['label' => __('app.label.'.$this->FromController), 'href' => route($this->FromController.'.index')]],
            'title'                 => __('app.label.'.$this->FromController),
            'filters'               => $request->all($filters),
            'perPage'               => (int) $perPage,

            'numberPermissions'     => $numberPermissions,
            'thisAtributos'         => $this->thisAtributos,
//            'losSelect'             => $losSelect,
        ]);
    }

    public function create(){}

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:transaccions');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $transaccion = transaccion::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:transaccions EXITOSO', 'transaccion id:' . $transaccion->id . ' | ' . $transaccion->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $transaccion->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:transaccions');
        DB::beginTransaction();
        $transaccion = transaccion::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $transaccion->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:transaccions EXITOSO', 'transaccion id:' . $transaccion->id . ' | ' . $transaccion->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $transaccion->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($transaccionid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:transaccions');
        $transaccion = transaccion::find($transaccionid);
        $elnombre = $transaccion->nombre;
        $transaccion->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:transaccions', 'transaccion id:' . $transaccion->id . ' | ' . $transaccion->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $transaccion = transaccion::whereIn('id', $request->id);
        $transaccion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.transaccion')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
