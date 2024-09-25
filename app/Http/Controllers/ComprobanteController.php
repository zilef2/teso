<?php

namespace App\Http\Controllers;

use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\Comprobante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ComprobanteController extends Controller
{
    public $thisAtributos,$FromController = 'Comprobante';


    //<editor-fold desc="Construc | mapea | filtro and losSelect">
    public function __construct() {
//        $this->middleware('permission:create Comprobante', ['only' => ['create', 'store']]);
//        $this->middleware('permission:read Comprobante', ['only' => ['index', 'show']]);
//        $this->middleware('permission:update Comprobante', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:delete Comprobante', ['only' => ['destroy', 'destroyBulk']]);
        $this->thisAtributos = (new Comprobante())->getFillable(); //not using
    }


    public function Mapear(): Builder {
        //$Comprobantes = Comprobante::with('no_nada');
        $Comprobantes = Comprobante::Where('id','>',0);
        return $Comprobantes;

    }
    public function Filtros(&$Comprobantes,$request){
        if ($request->has('search')) {
            $Comprobantes = $Comprobantes->where(function ($query) use ($request) {
                $query->where('nombre', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('codigo', 'LIKE', "%" . $request->search . "%")
                    //                    ->orWhere('identificacion', 'LIKE', "%" . $request->search . "%")
                ;
            });
        }

        if ($request->has(['field', 'order'])) {
            $Comprobantes = $Comprobantes->orderBy($request->field, $request->order);
        }else
            $Comprobantes = $Comprobantes->orderBy('updated_at', 'DESC');
    }
    public function losSelect()
    {
        $no_nadasSelect = No_nada::all('id','nombre as name')->toArray();
        array_unshift($no_nadasSelect,["name"=>"Seleccione un no_nada",'id'=>0]);
        return $no_nadasSelect;
    }
    //</editor-fold>

    public function index(Request $request) {
        $numberPermissions = MyModels::getPermissionToNumber(Myhelp::EscribirEnLog($this, ' Comprobantes '));
        $Comprobantes = $this->Mapear();
        $this->Filtros($Comprobantes,$request);
//        $losSelect = $this->losSelect();


        $perPage = $request->has('perPage') ? $request->perPage : 10;
        return Inertia::render($this->FromController.'/Index', [
            'fromController'        => $Comprobantes->paginate($perPage),
            'total'                 => $Comprobantes->count(),

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
        $permissions = Myhelp::EscribirEnLog($this, ' Begin STORE:Comprobantes');
        DB::beginTransaction();
//        $no_nada = $request->no_nada['id'];
//        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Comprobante = Comprobante::create($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'STORE:Comprobantes EXITOSO', 'Comprobante id:' . $Comprobante->id . ' | ' . $Comprobante->nombre, false);
        return back()->with('success', __('app.label.created_successfully', ['name' => $Comprobante->nombre]));
    }
    //fin store functions

    public function show($id){}public function edit($id){}

    public function update(Request $request, $id){
        $permissions = Myhelp::EscribirEnLog($this, ' Begin UPDATE:Comprobantes');
        DB::beginTransaction();
        $Comprobante = Comprobante::findOrFail($id);
        $request->merge(['no_nada_id' => $request->no_nada['id']]);
        $Comprobante->update($request->all());

        DB::commit();
        Myhelp::EscribirEnLog($this, 'UPDATE:Comprobantes EXITOSO', 'Comprobante id:' . $Comprobante->id . ' | ' . $Comprobante->nombre , false);
        return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $Comprobante->nombre]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */

    public function destroy($Comprobanteid){
        $permissions = Myhelp::EscribirEnLog($this, 'DELETE:Comprobantes');
        $Comprobante = Comprobante::find($Comprobanteid);
        $elnombre = $Comprobante->nombre;
        $Comprobante->delete();
        Myhelp::EscribirEnLog($this, 'DELETE:Comprobantes', 'Comprobante id:' . $Comprobante->id . ' | ' . $Comprobante->nombre . ' borrado', false);
        return back()->with('success', __('app.label.deleted_successfully', ['name' => $elnombre]));
    }

    public function destroyBulk(Request $request){
        $Comprobante = Comprobante::whereIn('id', $request->id);
        $Comprobante->delete();
        return back()->with('success', __('app.label.deleted_successfully', ['name' => count($request->id) . ' ' . __('app.label.Comprobante')]));
    }
    //FIN : STORE - UPDATE - DELETE

}
