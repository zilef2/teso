<?php

namespace App\Jobs;

use App\helpers\ZilefErrors;
use App\Imports\AsientoImport;
use App\Imports\ComprobanteImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class UpComprobantesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public mixed $path;
    private mixed $userMail;
    private mixed $mensajeEmail;
    public int $tries = 8;
    public int $timeout = 0;

    public function __construct($userMail, $mensajeEmail, $path)
    {
        $this->userMail = $userMail;
        $this->mensajeEmail = $mensajeEmail;
        $this->path = $path;
    }

    public function failed(?Throwable $exception): void
    {
        Log::error("UpComprobantesJob falló: " . $exception->getMessage(), [
            'trace' => $exception->getTraceAsString(),
            'job' => $this->job->getRawBody(),
        ]);
        Mail::raw('error', function ($message){
            $message->to('ajelof2@gmail.com')->subject('Ha fallado el proceso UpComprobantesJob. Ojala no sea que revento el servidor');
        });
    }
    public function handle(): void
    {
        try {
            Log::info(" 1 Inicio de UpComprobantesJob");
            $elImport = new ComprobanteImport();
            Excel::import($elImport, storage_path('app/' . $this->path));
            Log::info("el import se ha completado UpComprobantesJob");

            //ahora mandamos un correo
            $destinatario = $this->userMail;
            $mensaje = $this->mensajeEmail;
            Mail::raw($mensaje, function ($message) use ($destinatario, $mensaje) {
                $message->to($destinatario)->subject($mensaje);
            });
            log::info('4 Operacion subir asientos exitosa');
        } catch (\Throwable $th) {
            $error = ZilefErrors::RastroError($th);
            Log::error($error);
            Mail::raw('error', function ($message) use($error){
                $message->to('ajelof2@gmail.com')->subject('Ha fallado el proceso upasientos:  '.$error);
            });
            $this->fail('Something went wrong.');
        }
    }
}
