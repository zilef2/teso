<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\helpers\ZilefLogs;
use App\Models\concepto_flujo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ConceptoflujoController extends Controller
{
    public string $FromController = 'concepto_flujo';
    public array $arrayBusque;
    public array $arrayFillableSearch;
    public array $thisAtributos;


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
        $this->thisAtributos = (new concepto_flujo())->getFillable();
        $this->thisAtributos = array_diff($this->thisAtributos, ['deleted_at']);
        $this->arrayBusque = [
            'search',
            'search2',
        ];

        $this->arrayFillableSearch = [
            'cuenta_contable',
            'concepto_flujo',
        ];
    }


    private function Mapear($clase)
    {
        $Result = $clase->map(function ($clas) {
//            $clas->cuenta = $clas->cuenta();
            return $clas;
        });

        return $Result;
    }

    public function BusquedasText($concepto_flujos,$request){
        foreach ($this->arrayBusque as $index => $busqueda) {
            $campo = $this->arrayFillableSearch[$index];
            if ($request->has($busqueda)) {
                $concepto_flujos = $concepto_flujos->where(function ($query) use ($request,$busqueda,$campo) {
                    $query->where($campo, 'LIKE', "%" . $request->{$busqueda} . "%")
                    ;
                });
            }
        }
        return $concepto_flujos->get();
    }
    public function Filtros($request){
        $concepto_flujos = concepto_flujo::Query();
//        $concepto_flujos = concepto_flujo::All();

        if ($request->has(['field', 'order'])) {
            $concepto_flujos = $concepto_flujos->orderBy($request->field, $request->order);
        }else{
            $concepto_flujos = $concepto_flujos->orderBy('updated_at', 'DESC');
        }
        return $this->BusquedasText($concepto_flujos, $request);
    }

    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(ZilefLogs::EscribirEnLog($this, ' concepto_flujos '));
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

            'breadcrumbs'           => [['label' => __('app.label.'.$this->FromController),
                'href' => route($this->FromController.'.index')]],
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
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin STORE:concepto_flujos');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $concepto_flujo = concepto_flujo::create($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'STORE:concepto_flujos EXITOSO', 'concepto_flujo id:' . $concepto_flujo->id . ' | ' . $concepto_flujo->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $concepto_flujo->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = ZilefLogs::EscribirEnLog($this, ' Begin UPDATE:concepto_flujos');
        DB::beginTransaction();
        $concepto_flujo = concepto_flujo::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $concepto_flujo->update($request->all());

        DB::commit();
        ZilefLogs::EscribirEnLog($this, 'UPDATE:concepto_flujos EXITOSO', 'concepto_flujo id:' . $concepto_flujo->id . ' | ' . $concepto_flujo->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $concepto_flujo->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($concepto_flujoid){
        $permissions = ZilefLogs::EscribirEnLog($this, 'DELETE:concepto_flujos');
        $concepto_flujo = concepto_flujo::find($concepto_flujoid);
        $elnombre = $concepto_flujo->nombre;
        $concepto_flujo->delete();
        ZilefLogs::EscribirEnLog($this, 'DELETE:concepto_flujos', 'concepto_flujo id:' . $concepto_flujo->id . ' | ' . $concepto_flujo->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $concepto_flujo = concepto_flujo::whereIn('id', $request->id);
        $concepto_flujo->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.concepto_flujo')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
