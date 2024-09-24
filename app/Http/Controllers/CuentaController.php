<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\cuenta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CuentaController extends Controller
{
    public string $FromController = 'cuenta';
    public array $arrayBusque;
    public array $arrayFillableSearch;
    public array $thisAtributos;


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create cuenta', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read cuenta', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update cuenta', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete cuenta', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new cuenta())->getFillable(); 
        $this->arrayBusque = [
            'search',
            'searchNumCuenta',
            'searchBanco',
            'searchtipo',
        ];
        
        $this->arrayFillableSearch = [
            'codigo_cuenta_contable',
            'numero_cuenta_bancaria',
            'banco',
            'tipo_de_recurso',
        ];
    }


    public function Mapear(): Builder {
        //$cuentas = Cuenta::with('no_nada');
        $cuentas = Cuenta::Where('id','>',0);
        return $cuentas;

    }
    
    private function BusquedasText($cuentas,$arrayBusquedas,$request){
        foreach ($arrayBusquedas as $index => $busqueda) {
            $campo = $this->arrayFillableSearch[$index];
            if ($request->has($busqueda)) {
                $cuentas = $cuentas->where(function ($query) use ($request,$busqueda,$campo) {
                    $query->where($campo, 'LIKE', "%" . $request->{$busqueda} . "%")
                    ;
                });
            }
        }
        return $cuentas;
    }
    public function Filtros(&$cuentas,$request){
        
        $cuentas = $this->BusquedasText($cuentas,$this->arrayBusque,$request);

        if ($request->has(['field', 'order'])) {
            $cuentas = $cuentas->orderBy($request->field, $request->order);
        }else{
            $cuentas = $cuentas->orderBy('updated_at', 'DESC');
        }
    }
    
//    public function losSelect()
//    {
//        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
//        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
//        return $no_nadasSelect;
//    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' cuentas '));
        $cuentas = $this->Mapear();
        $this->Filtros($cuentas,$request);
//        $losSelect = $this->losSelect();
        $filters = ['search', 'field', 'order'];
        $filters = array_merge($this->arrayBusque,$filters);
        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $total = $cuentas->count();
        $page = request('page', 1);
        $fromController = new LengthAwarePaginator(
            $cuentas->get()->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );
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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:cuentas');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $cuenta = cuenta::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:cuentas EXITOSO', 'cuenta id:' . $cuenta->id . ' | ' . $cuenta->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $cuenta->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:cuentas');
        DB::beginTransaction();
        $cuenta = cuenta::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $cuenta->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:cuentas EXITOSO', 'cuenta id:' . $cuenta->id . ' | ' . $cuenta->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $cuenta->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($cuentaid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:cuentas');
        $cuenta = cuenta::find($cuentaid);
        $elnombre = $cuenta->nombre;
        $cuenta->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:cuentas', 'cuenta id:' . $cuenta->id . ' | ' . $cuenta->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $cuenta = cuenta::whereIn('id', $request->id);
        $cuenta->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.cuenta')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
