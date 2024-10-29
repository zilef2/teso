<?php

namespace App\Jobs;

use App\helpers\Myhelp;
use App\helpers\ZilefErrors;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BC_CIGeneral implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
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

            $contraCICE->Encontrar_AJ_CI($this->Transacciones);
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
