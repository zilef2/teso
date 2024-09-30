<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
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
        $Result = $clase->map(function ($clas) {
            $clas->cuenta = $clas->cuenta();
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

    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' index transaccions '));
        $laclase = $this->Mapear($this->Filtros($request));
//        $losSelect = $this->losSelect();

        $perPage = $request->has('perPage') ? $request->perPage : 20;
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
            'thisAtributos'         => array_values(array_diff($this->thisAtributos, [
                'nombre_cuenta',
                'nit',
            ])),
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

    public function Buscar_CP(Request $request){

        $codigo = "CI";
        $valor_debito_credito =  (strcmp($codigo, "CI") === 0)? "valor_debito" : "valor_credito";
        $laFecha = new \DateTime();

        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
        $mes = 8; // Obtiene el mes (en formato numérico)
        $anio = $laFecha->format('Y'); // Obtiene el año

        $Transacciones = transaccion::Where('codigo',$codigo)
            ->WhereYear('fecha_elaboracion',$anio)
            ->whereMonth('fecha_elaboracion',$mes)->get();
        //validar que tanto el Comprobante como la transsacion exista
//        dd($Transacciones[0]);

        foreach ($Transacciones as $index => $transa) {
            $comprobantes = Comprobante::Where('numero_documento',$transa->documento)
                ->Where('codigo',$codigo);
//                ->get();
//            $princi = clone $comprobantes;
            $principal = $comprobantes->where($valor_debito_credito,$transa->{$valor_debito_credito})->first();
            $otros = $comprobantes->where($valor_debito_credito,$transa->{$valor_debito_credito})->get()
                ->reject(function ($item) use ($principal) {
                return $item->id === $principal->id;
            });
if($comprobantes->get()->count() > 1)
    dd(
        $principal,$comprobantes->get(),$comprobantes->count()
    );
            $otros1 = $otros->first();
            if($otros1 && count($otros)){
                $cuentaCP = $otros1->codigo_cuenta;
                $transa->update([
                    'n_contrapartidas' => count($otros),
                    'contrapartida_CI' => $cuentaCP,
                    'concepto_flujo_homologación' => $this->hallarConcepto($cuentaCP),
                ]);
                dd($transa);
            }

        }

        return back()->with('success', __('app.label.deleted_successfully', ['name' => __('app.label.transaccion')]));
    }
    //FIN : STORE - UPDATE - DELETE
    private function hallarConcepto($cuentaCP)
    {
        $cf = concepto_flujo::Where('cuenta_contable',$cuentaCP)->first();
        if($cf){
            return $cf->concepto_flujo;
        }
        return '';
    }

}
