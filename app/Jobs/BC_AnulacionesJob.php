<?php

namespace App\Jobs;

use App\helpers\ZilefErrors;
use App\Http\Controllers\BusquedaIndependienteController;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BC_AnulacionesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $Transacciones;
    private string $mensajeEmail;
    private string $userMail;

    /**
     * Create a new job instance.
     */
    public function __construct($Transacciones, $mensaje, $user)
    {
        $this->Transacciones = $Transacciones;
        $this->mensajeEmail = $mensaje;
        $this->userMail = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("U -> " . $this->userMail . ' handle_job de BusquedaConceptoCI_AJJob');

            $busquedaInd = new BusquedaIndependienteController();
            $busquedaInd->Encontrar_AN_CI($this->Transacciones);

            $destinatario = $this->userMail;
            $mensaje = $this->mensajeEmail;
            Mail::raw($mensaje, function ($message) use ($destinatario, $mensaje) {
                $message->to($destinatario)->subject($mensaje);
            });

        } catch (\Exception $e) {
            $mesnajeErr = "Error, no se envio correo: " . $e->getMessage();
            Log::error($mesnajeErr);
        } catch (\Throwable $th) {
            Log::error(ZilefErrors::RastroError($th));
        }
    }
}
