<?php

namespace App\Jobs;

use App\helpers\ZilefErrors;
use App\Imports\AsientoImport;
use App\Models\Parametro;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UpAsientosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public mixed $path;
    private mixed $userMail;
    private mixed $mensajeEmail;

    public function __construct($userMail, $mensajeEmail, $path)
    {
        $this->userMail = $userMail;
        $this->mensajeEmail = $mensajeEmail;
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
//        try {
//        $elImport = new AsientoImport();
//        Excel::import($elImport, storage_path('app/' . $this->filePath));
            Log::info('UpAsientosJob 2222222222222222222222222222222222.');
        Parametro::create([
            "Fecha_creacion_parametro" => date('Y-m-d'),
            "nombre" => "Testing",
            "valor" => date('Y-m-d H:i') . '',
        ]);
//            $destinatario = $this->userMail;
//            $mensaje = $this->mensajeEmail;
//            Mail::raw($mensaje, function ($message) use ($destinatario, $mensaje) {
//                $message->to($destinatario)->subject($mensaje);
//            });
//        } catch (\Throwable $th) {
//            Log::error(ZilefErrors::RastroError($th));
//        }
    }
}
