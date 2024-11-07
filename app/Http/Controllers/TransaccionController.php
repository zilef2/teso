<?php

namespace App\Http\Controllers;

use App\helpers\CPhelp;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Parametro;
use App\Models\transaccion;
use Composer\DependencyResolver\Transaction;
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
            'searchCodigo',
            'searchConcepto',
            'searchDocRef',
            'concepto_flujo_omologaci',
        ];

        $this->arrayFillableSearch = [
            'codigo_cuenta_contable',
            'contrapartida',
            'documento',
            'codigo',
            'concepto_flujo_homologacion',
            'documento_ref',
            'concepto_flujo_homologacion',
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
//        $cacheKey = $this->generateCacheKey($request);
        $transaccions = transaccion::Query();

        if ($request->has(['field', 'order'])) {
            $transaccions = $transaccions->orderBy($request->field, $request->order);
        } else {
            $transaccions = $transaccions->orderBy('updated_at', 'DESC');
        }

        if ($request->has('OnlyCP')) {
            if ($request->OnlyCP == 'onlycp')
                $transaccions = $transaccions->whereNotNull('contrapartida');
            if ($request->OnlyCP == 'onlyemptycp')
                $transaccions = $transaccions->whereNull('contrapartida');
            if ($request->OnlyCP == 'noSeEncontro')
                $transaccions = $transaccions->where('contrapartida', 'LIKE', "%No se encontro%");
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
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' index transaccions '));
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

        $filters = ['search', 'field', 'order', 'OnlyCP', 'OnlyEmptyCP'];
        $filters = array_merge($this->arrayBusque, $filters);

        $Indicadores = [
            'Transacciones' => transaccion::count(),
            'NoSeEncontro' => transaccion::Where('contrapartida', 'LIKE', "%No se encontro%")->count(),
            'AJCount' => transaccion::Where('codigo', "AJ")->count(),
            'ANCount' => transaccion::Where('codigo', "AN")->count(),
        ];

        //solo para el filtro de CE
        $resultadosCFH = Transaccion::whereNotNull('concepto_flujo_homologacion')->distinct()
            ->pluck('concepto_flujo_homologacion')
            ->take(8);
        $resultadosCFHCount = Transaccion::whereNotNull('concepto_flujo_homologacion')
            ->Where('codigo','CE')
            ->distinct()
            ->pluck('concepto_flujo_homologacion')
            ->count();
        //return final
        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $fromController,
            'total' => $total,

            'breadcrumbs' => [['label' => __('app.label.' . $this->FromController), 'href' => route($this->FromController . '.index')]],
            'title' => __('app.label.' . $this->FromController),
            'filters' => $request->all($filters),
            'perPage' => (int)$perPage,

            'numberPermissions' => $numberPermissions,
            'Indicadores' => $Indicadores,
            'resultadosCFH' => $resultadosCFH,
            'resultadosCFHCount' => $resultadosCFHCount,
            'thisAtributos' => array_values(array_diff($this->thisAtributos, [
                'nombre_cuenta',
                'nit',
            ])),
//            'losSelect'             => $losSelect,
        ]);
    }


    //<editor-fold desc="no index">
    public function create(){}
    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request)
    {
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:transaccions');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $transaccion = transaccion::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:transaccions EXITOSO', 'transaccion id:' . $transaccion->id . ' | ' . $transaccion->nombre, false);
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
        ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:transaccions');
        DB::beginTransaction();
        $transaccion = transaccion::findOrFail($id);
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $transaccion->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:transaccions EXITOSO', 'transaccion id:' . $transaccion->id . ' | ' . $transaccion->nombre, false);
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
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:transaccions');
        $transaccion = transaccion::find($transaccionid);
        $elnombre = $transaccion->nombre;
        $transaccion->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:transaccions', 'transaccion id:' . $transaccion->id . ' | ' . $transaccion->nombre . ' borrado', false);
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

    public function Buscar_CP_CI(Request $request)//todo: this function shouldnt be here
    {
        try {
            $codigo = "CI";
            $frase_reservada = "No se encontro";
            if(!CPhelp::Val_Exista_CI_auxiliar($codigo)){
                return back()->with('error', 'Faltan archivos');
            }
            DB::beginTransaction();
            //debito para CI? 30oct2024
            $INT_TransaccionesOperadas = CPhelp::BuscarContrapartidaGeneral($codigo,$frase_reservada);
            DB::commit();

//            return redirect()->route('transaccion.index')->with('success',
            return back()->with('success',
                'Operacion exitosa. ' . $INT_TransaccionesOperadas . ' transacciones ' . $codigo . ' de agosto fueron revisadas'
            );
        } catch (\Throwable $th) {
            DB::rollback();
            $mensaj = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            return back()->with('error', 'Operacion con errores ' . $mensaj);
        }
    }

    //FIN : STORE - UPDATE - DELETE



}
