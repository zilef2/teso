<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\Comprobante;
use App\Models\transaccion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ComprobanteController extends Controller
{
    public $thisAtributos,$FromController = 'Comprobante';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    /**
     * @var array|string[]
     */
    private array $arrayFillableSearch;

    public function __construct() {
//        $this->middleware('permission:create Comprobante', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read Comprobante', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update Comprobante', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete Comprobante', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new Comprobante())->getFillable();
        $this->arrayFillableSearch = [
            'codigo',
            'numero_documento',
            'valor_debito',
            'valor_credito',
        ];
    }


    private function Mapear($clase)
    {
        $Result = $clase->map(function ($clas) {
            return $clas;
        });

        return $Result;
    }

    public function BusquedasText($laclase,$request){
        foreach ($this->arrayFillableSearch as $index => $busqueda) {
            if ($request->has($busqueda)) {
                $laclase = $laclase->where(function ($query) use ($request,$busqueda) {
                    $query->where($busqueda, 'LIKE', "%" . $request->{$busqueda} . "%");
                });
            }
        }
        return $laclase->get();
    }
    public function Filtros($request){
        $Comprobantes = Comprobante::Query();

        if ($request->has(['field', 'order'])) {
            $Comprobantes = $Comprobantes->orderBy($request->field, $request->order);
        }else{
            $Comprobantes = $Comprobantes->orderBy('updated_at', 'DESC');
        }

//        $Comprobantes = $Comprobantes->get();
        return $this->BusquedasText($Comprobantes,$request);
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' Comprobantes '));
        $laclase = $this->Mapear($this->Filtros($request));


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

        return Inertia::render($this->FromController . '/Index', [
            'fromController' => $fromController,
            'total' => $total,

            'breadcrumbs'           => [['label' => __('app.label.'.$this->FromController), 'href' => route($this->FromController.'.index')]],
            'title'                 => __('app.label.'.$this->FromController),
            'filters'               => $request->all(['field', 'order',
                'codigo',
                'numero_documento',
                'valor_debito',
                'valor_credito',
                ]),
            'perPage'               => (int) $perPage,
            'numberPermissions'     => $numberPermissions,
//            'losSelect'             => $losSelect,
        ]);
    }

    public function create(){}

    //! STORE - UPDATE - DELETE
    //! STORE functions

    public function store(Request $request){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:Comprobantes');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Comprobante = Comprobante::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:Comprobantes EXITOSO', 'Comprobante id:' . $Comprobante->id . ' | ' . $Comprobante->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $Comprobante->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:Comprobantes');
        DB::beginTransaction();
        $Comprobante = Comprobante::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Comprobante->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:Comprobantes EXITOSO', 'Comprobante id:' . $Comprobante->id . ' | ' . $Comprobante->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Comprobante->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($Comprobanteid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:Comprobantes');
        $Comprobante = Comprobante::find($Comprobanteid);
        $elnombre = $Comprobante->nombre;
        $Comprobante->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:Comprobantes', 'Comprobante id:' . $Comprobante->id . ' | ' . $Comprobante->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $Comprobante = Comprobante::whereIn('id', $request->id);
        $Comprobante->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.Comprobante')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
