<?php

namespace App\Jobs;

use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Models\Comprobante;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BusquedaConceptoCI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $Transacciones;

    /**
     * Create a new job instance.
     */
    public function __construct($Transacciones)
    {
        $this->Transacciones = $Transacciones;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('El job BusquedaConceptoCI ha comenzado.');
        try {

            // Lógica del job
            $this->procesarTransacciones();

            // Log después de la operación
            $codigo = "AJ";

            DB::beginTransaction();
            foreach ($this->Transacciones as $index => $transa) {
                $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                    ->Where('codigo', $codigo);

                if ($this->NoHayComprobantes($comprobantes, $transa)){
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida_CI' => 'No se encontró comprobantes para el documento',
                        'concepto_flujo_homologación' => 'No se encontró comprobantes para el documento',
                    ]);
                    continue;
                }

                $lasContrapartidas = clone $comprobantes;
                $lasContrapartidasForeach = clone $comprobantes;

                //core
                $principales = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->get();

                $lasContrapartidas = $lasContrapartidas->WhereNotIn('id', $principales->pluck('id'))
                    ->WhereIn('documento_ref', $principales->pluck('documento_ref'))
                    ->get();
                // $lasContrapartidas  =  should be one

                //AJUSTES
                if ($this->LaContraPartidaNoSumaCero($lasContrapartidas, $transa, $principales)){
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida_CI' => 'No se encontró una suma coherente, no suman 0',
                        'concepto_flujo_homologación' => 'No se encontró una suma coherente, no suman 0',
                    ]);
                    continue;
                }

                //va y busca los demas
                foreach ($principales as $principal) {
                    //validacion adicional: el doc_ref debe ser igual para el original y la CP
                    $principalRef = $principal->documento_ref;
                    $Col_ContrapartidaRef = $lasContrapartidasForeach->Where('documento_ref', $principalRef)
                        ->Where('id', '!=', $principal->id)
                        ->get();

                    if ($Col_ContrapartidaRef->count() <= 0 || !isset($ContrapartidaRef[0])) {
                        $transa->update([
                            'n_contrapartidas' => 0,
                            'contrapartida_CI' => 'No se encontro CP en ' . $codigo,
                            'concepto_flujo_homologación' => 'No se encontro CP en ' . $codigo,
                        ]);
                    } else {
                        $int_ContrapartidaRef = $ContrapartidaRef[0]->codigo_cuenta;
                        $concepto = $this->hallarConcepto($int_ContrapartidaRef, $codigo);
                        $transa->update([
                            'n_contrapartidas' => count($lasContrapartidas),
                            'contrapartida_CI' => $int_ContrapartidaRef,
                            'concepto_flujo_homologación' => $concepto,
                        ]);
                    }
                }
            }
            DB::commit();
            Log::info('El job BusquedaConceptoCI ha terminado exitosamente.');

//            return redirect()->route('transaccion.index')->with('success',
//                'Operación exitosa. ' . $Transacciones->count() . ' transacciones ' . $codigo . ' de agosto fueron revisadas'
//            );
        } catch (\Throwable $th) {
            DB::rollback();
//            return back()->with('error', 'Ajustes con errores: ' . ZilefErrors::RastroError($th));
            Log::info('El job BusquedaConceptoCI ha terminado desastrosamente.');
        }
    }
}
