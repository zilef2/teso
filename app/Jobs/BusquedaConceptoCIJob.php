<?php

namespace App\Jobs;

use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Http\Controllers\ContrapartidasCICEController;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Parametro;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Param;

class BusquedaConceptoCIJob implements ShouldQueue
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
        $inicioicrotime = microtime(true);
        Log::info('El job BusquedaConceptoCI ha comenzado.');


        $inicioicrotime = microtime(true);
        Log::info('El job BusquedaConceptoCI ha comenzado.');

        try {
            // Log después de la operación
            $codigo = "AJ";

            $CPController = new ContrapartidasCICEController();
            DB::beginTransaction();
            foreach ($this->Transacciones as $index => $transa) {
                $comprobantes = Comprobante::Where('numero_documento', $transa->documento)
                    ->Where('codigo', 'aj');

                if ($CPController->NoHayComprobantes($comprobantes, $transa)) {
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
                $principal = $comprobantes->where('codigo_cuenta', $transa->codigo_cuenta_contable)->first();
                //todo: si hay mas, es error, del excel o que se permitio subir 2 veces

                $lasContrapartidas = $lasContrapartidas->WhereNot('id', $principal->id)
                    ->Where('documento_ref', $principal->documento_ref)
                    ->get();

                //AJUSTES
                if ($CPController->LaContraPartidaNoSumaCeroGet($lasContrapartidas, $transa, $principal)) {
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida_CI' => 'No se encontró una suma coherente, no suman 0',
                        'concepto_flujo_homologación' => 'No se encontró una suma coherente, no suman 0',
                    ]);
                    continue;
                }

                //va y busca los demas
//                foreach ($Todas as $principal) {
                //validacion adicional: el doc_ref debe ser igual para el original y la CP
                $Col_ContrapartidaRef = $lasContrapartidasForeach->Where('documento_ref', $principal->documento_ref)
                    ->WhereNotIn('id', $principal->id)
                    ->Where('valor_credito', $transa->valor_debito)
                    ->get()
                    ->sortByDesc('valor_credito')
                    ->first();


                if ($Col_ContrapartidaRef) {
                    $transa->update([
                        'n_contrapartidas' => 0,
                        'contrapartida_CI' => 'No se encontro CP para doc_ref',
                        'concepto_flujo_homologación' => 'No se encontro CP para doc_ref',
                    ]);
                } else {
                    $int_ContrapartidaRef = intval($Col_ContrapartidaRef->codigo_cuenta);
                    $concepto = $CPController->hallarConcepto($int_ContrapartidaRef, $codigo);
                    $IntCp = count($lasContrapartidasForeach);
                    $IntCp = $IntCp == 0 ? $IntCp : $IntCp - 1;
                    $transa->update([
                        'n_contrapartidas' => $IntCp,
                        'contrapartida_CI' => $int_ContrapartidaRef,
                        'concepto_flujo_homologación' => $concepto,
                    ]);
                }
//                }
            }
            DB::commit();
            Log::info('El job BusquedaConceptoCI ha terminado exitosamente.');
            $finicrotime = microtime(true);
            Parametro::create([
                'Fecha_creacion_parametro' => date('Y-m-d H:i:s'),
                'nombre' => 'Cruzar Ajustes CI',
                'valor' => number_format($finicrotime - $inicioicrotime, 3),
                'categoria' => 'jobs_' . $inicioicrotime,
                'numero' => number_format($finicrotime, 3),
            ]);
            //todo: configurar email cuando termine


        } catch (\Throwable $th) {
            DB::rollback();
            ZilefErrors::RastroError($th);
        }
    }

}
