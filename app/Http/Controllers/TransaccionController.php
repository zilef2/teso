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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransaccionController extends Controller
{
    public string $FromController = 'transaccion';
    public array $arrayBusque;
    public array $arrayFillableSearch;
    public array $thisAtributos;


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct()
    {
        $this->thisAtributos = (new transaccion())->getFillable();
        $this->thisAtributos = array_diff($this->thisAtributos, ['deleted_at']);
        $this->arrayBusque = [
            'search',
            'searchContrapartida',
            'searchDocumento',
            'searchConcepto',
            'searchDocRef',
            'searchCodigo',
        ];

        $this->arrayFillableSearch = [
            'codigo_cuenta_contable',
            'contrapartida_CI',
            'documento',
            'concepto_flujo_homologación',
            'documento_ref',
            'codigo',
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

    public function BusquedasText($transaccions, $request)
    {
        foreach ($this->arrayBusque as $index => $busqueda) {
            $campo = $this->arrayFillableSearch[$index];
            if ($request->has($busqueda)) {
                $transaccions = $transaccions->where(function ($query) use ($request, $busqueda, $campo) {
                    $query->where($campo, 'LIKE', "%" . $request->{$busqueda} . "%");
                });
            }
        }
        return $transaccions->get();
    }

    public function Filtros($request)
    {
        $cacheKey = $this->generateCacheKey($request);
        $transaccions = transaccion::Query();

        if ($request->has(['field', 'order'])) {
            $transaccions = $transaccions->orderBy($request->field, $request->order);
        } else {
            $transaccions = $transaccions->orderBy('updated_at', 'DESC');
        }

        if ($request->has('OnlyCP')) {
            if($request->OnlyCP == 'onlycp')
                $transaccions = $transaccions->whereNotNull('contrapartida_CI');
            if($request->OnlyCP == 'onlyemptycp')
                $transaccions = $transaccions->whereNull('contrapartida_CI');
            if($request->OnlyCP == 'noSeEncontro')
                $transaccions = $transaccions->where('contrapartida_CI','LIKE', "%No se encontro%");
//            if($request->OnlyCP == 'allcp')
        }
        // Cachear la búsqueda usando Cache::remember()
//        return Cache::remember($cacheKey, 60, function () use ($request,$transaccions) {
        return $this->BusquedasText($transaccions, $request);
//        });
    }

    private function generateCacheKey($request)
    {
        $parts = [];

        if ($request->has('field')) {
            $parts[] = 'field_' . $request->field;
        }

        if ($request->has('order')) {
            $parts[] = 'order_' . $request->order;
        }

        foreach ($this->arrayBusque as $busqueda) {
            if ($request->has($busqueda)) {
                $parts[] = $busqueda . '_' . $request->{$busqueda};
            }
        }

        // Unir todas las partes con un delimitador único (puede ser un guión bajo, por ejemplo)
        return 'transaccions_search_' . implode('_', $parts);
    }

    //</editor-fold>

    public function index(Request $request)
    {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' index transaccions '));
        $laclase = $this->Mapear($this->Filtros($request));
//        $losSelect = $this->losSelect();

        $perPage = $request->has('perPage') ? $request->perPage : 10;
        $total = $laclase->count();
        $page = request('page', 1);
        $fromController = new LengthAwarePaginator(
            $laclase->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        $filters = ['search', 'field', 'order', 'OnlyCP', 'OnlyEmptyCP'];
        $filters = array_merge($this->arrayBusque, $filters);
        $Indicadores = [
            'Transacciones' => transaccion::count(),
            'NoSeEncontro' => transaccion::Where('contrapartida_CI','LIKE', "%No se encontro%")->count(),
            'AJCount' => transaccion::Where('codigo', "AJ")->count(),
            'ANCount' => transaccion::Where('codigo', "AN")->count(),
        ];
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $fromController,
            'total' => $total,

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all($filters),
            'perPage' => (int)$perPage,

            'numberPermissions' => $numberPermissions,
            'Indicadores' => $Indicadores,
            'thisAtributos' => array_values(array_diff($this->thisAtributos, [
                'nombre_cuenta',
                'nit',
            ])),
//            'losSelect'             => $losSelect,
        ]);
    }


    //<editor-fold desc="no index">
    public function create()
    {
    }

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request)
    {
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

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
        Myhelp::EscribirEnLog($this, ' Begin UPDATE:transaccions');
        DB::beginTransaction();
        $transaccion = transaccion::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $transaccion->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:transaccions EXITOSO', 'transaccion id:' . $transaccion->id . ' | ' . $transaccion->nombre, false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $transaccion->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($transaccionid)
    {
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:transaccions');
        $transaccion = transaccion::find($transaccionid);
        $elnombre = $transaccion->nombre;
        $transaccion->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:transaccions', 'transaccion id:' . $transaccion->id . ' | ' . $transaccion->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request)
    {
        $transaccion = transaccion::whereIn('id', $request->id);
        $transaccion->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.transaccion')]));
    }

    //</editor-fold>


    public function Buscar_CP_CE(Request $request)
    {
    }

    public function Buscar_CP_CI(Request $request)
    {
        try {
            $codigo = "CI";
            if (Comprobante::Where('codigo', $codigo)->count() === 0)
                return back()->with('error', 'No se encontro ningun comprobante con código: ' . $codigo);

            DB::beginTransaction();
            [$Transacciones, $valor_debito_credito] = $this->TransaccionesCI($codigo);


            foreach ($Transacciones as $index => $transa) {
                $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                    ->Where('codigo', $codigo);

                $HayComprobantes = clone $comprobantes;
                $HayComprobantes = $HayComprobantes->count();
                if ($HayComprobantes === 0) {
                    $transa->update([
                        'contrapartida_CI' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
                        'concepto_flujo_homologación' => 'No se encontro ningun comprobante para el documento ' . $transa->documento,
                    ]);
                    continue;
                }

                $lasContrapartidas = clone $comprobantes;

//                $principales = $comprobantes->where($valor_debito_credito, $transa->{$valor_debito_credito})->get();//todo: no es asi, debe ser mayor a cero
                $principales = $comprobantes->where($valor_debito_credito, '>', 0)->get();//todo: no es asi, debe ser mayor a cero
                $lasContrapartidas = $lasContrapartidas->where("valor_credito", '>', 0)->get();


                //validacion de credito - debito
                $sumprincipales = $principales->sum('valor_debito'); //todo: hacer mas dinamica
                $sumlasContrapartidas = $lasContrapartidas->sum('valor_credito'); //todo: hacer mas dinamica

                if ($sumprincipales !== $sumlasContrapartidas) {
                    dd('Error fatal, La suma de la contrapartida no concuerda',
                        $principales,
                        $lasContrapartidas,
                        $sumprincipales, $sumlasContrapartidas);
                    $transa->update([
                        'contrapartida_CI' => "debitos y creditos distintos ($sumprincipales | $sumlasContrapartidas)",
                        'concepto_flujo_homologación' => "debitos y creditos distintos ($sumprincipales | $sumlasContrapartidas)",
                    ]);
                    continue;
                }


                //va y busca los demas
                foreach ($principales as $principal) {
                    foreach ($lasContrapartidas as $item) {
                        $soloTieneUno = floor(intval($principal->valor_debito)) - floor(intval($item->valor_credito)) == 0;
                        $cuentaCP = $item->codigo_cuenta;

                        //buscamos el concepto
                        $concepto = $this->hallarConcepto($cuentaCP);
                        $transa->update([
                            'n_contrapartidas' => count($lasContrapartidas),
                            'contrapartida_CI' => $cuentaCP,
                            'concepto_flujo_homologación' => $concepto,
                        ]);
                    }
                }
            }
            DB::commit();

            return redirect()->route('transaccion.index')->with('success',
                'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' de agosto fueron revisadas'
            );
        } catch (\Throwable $th) {
            DB::rollback();
            $mensaj = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            return back()->with('error', 'Operacion con errores ' . $mensaj);
        }
    }

    //FIN : STORE - UPDATE - DELETE
    private function hallarConcepto($cuentaCP){
        $cf = concepto_flujo::Where('cuenta_contable', $cuentaCP)->first();
        if ($cf) {
            return $cf->concepto_flujo;
        }
        return 'Buscar en AJ o AN';
    }

    private function TransaccionesCI($codigo)
    {
        $valor_debito_credito = (strcmp($codigo, "CI") === 0) ? "valor_debito" : "valor_credito";
        $laFecha = new \DateTime();

        $mes = $laFecha->format('m'); // Obtiene el mes (en formato numérico)
        $mes = 8; // Obtiene el mes (en formato numérico)
        $anio = $laFecha->format('Y'); // Obtiene el año

        $Transacciones = transaccion::Where('codigo', $codigo)
            ->WhereNull('concepto_flujo_homologación')
//            ->WhereYear('fecha_elaboracion', $anio)
//            ->whereMonth('fecha_elaboracion', $mes)
            ->get();
        //validar que tanto el Comprobante como la transsacion exista
//        dd($Transacciones[0]);
        return [$Transacciones, $valor_debito_credito];
    }

}
