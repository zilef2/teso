<?php

namespace App\Jobs;

use App\helpers\CPhelp;
use App\helpers\ZilefLogs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Opcodes\LogViewer\Log;

class CruceCEJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $codigo;
    public $frase_reservada;

    /**
     * Create a new job instance.
     */
    public function __construct($codigo, $frase_reservada)
    {
        $this->codigo = $codigo;
        $this->frase_reservada = $frase_reservada;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $INT_TransaccionesOperadas = CPhelp::BuscarContrapartidaGeneral($this->codigo, $this->frase_reservada);
            $mensaje = 'Éxito. Transacciones cruzadas: ' . $INT_TransaccionesOperadas;
            Mail::raw($mensaje, function ($message) {
                $message->to('ajelof2@gmail.com')->subject('Ha fallado el proceso upasientos. Ojala no sea que revento el servidor');
            });
            Log::info($mensaje);
        } catch (\Throwable $th) {
            $mensajeError = ' Fallo dentro de la importacion: ' . $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            ZilefLogs::EscribirEnLog($this, 'IMPORT:cuentas', $mensajeError, false);
            Mail::raw('Error: '. $mensajeError, function ($message) use($mensajeError) {
                $message->to('ajelof2@gmail.com')
                    ->subject('Ha fallado el proceso Cruce CE')
                    ->body($mensajeError);
            });
        }

    }
}
