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

class BusquedaConceptoCI_AJJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $Transacciones;
    private string $mensajeEmail;
    private User $user;

    /**
     * Create a new job instance.
     */
    public function __construct($Transacciones,$mensaje,$user)
    {
        $this->Transacciones = $Transacciones;
        $this->mensajeEmail = $mensaje;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("U -> " . $this->user->name. ' handle_job de BusquedaConceptoCI_AJJob');
            $busquedaInd = new BusquedaIndependienteController();

            $busquedaInd->Encontrar_AJ_CI($this->Transacciones);
            try {
                $destinatario = $this->user->email;
                $mensaje = $this->mensajeEmail;

                Mail::raw($mensaje, function ($message) use ($destinatario, $mensaje) {
                    $message->to($destinatario)->subject($mensaje);
                });
            } catch (\Exception $e) {
                $mesnajeErr= "Error, no se envio correo: " . $e->getMessage();
                Log::error($mesnajeErr);
            }
        } catch (\Throwable $th) {
            Log::error(ZilefErrors::RastroError($th));
        }
    }
}
