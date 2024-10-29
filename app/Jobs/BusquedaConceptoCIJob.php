<?php

namespace App\Jobs;

use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use App\Http\Controllers\ContrapartidasCICEController;
use App\Mail\Jobfinished;
use App\Models\Comprobante;
use App\Models\concepto_flujo;
use App\Models\Parametro;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Param;

class BusquedaConceptoCIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $Transacciones;
    private string $mensaje;

    /**
     * Create a new job instance.
     */
    public function __construct($Transacciones,$mensaje)
    {
        $this->Transacciones = $Transacciones;
        $this->mensaje = $mensaje;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = Myhelp::AuthU();
            Log::info("U -> " . $user->name);
            $contraCICE = new \App\Http\Controllers\ContrapartidasCICEController();
            Log::info("asdasdasdasdasU -> " . Auth::user()->name);

            $contraCICE->Uscar_AJ_CI($this->Transacciones);
            try {
                $destinatario = $user->email;
                $mensaje = $this->mensaje;
                Mail::raw($mensaje, function ($message) use ($mensaje, $destinatario) {
                    $message->to($destinatario)->subject($mensaje);
                });
            } catch (\Exception $e) {
                Log::error("Error enviado correo: " . $e->getMessage());
            }
        } catch (\Throwable $th) {
            Log::error(ZilefErrors::RastroError($th));
        }
    }

}
