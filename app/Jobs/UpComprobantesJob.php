<?php

namespace App\Jobs;

use App\helpers\ZilefErrors;
use App\Imports\AsientoImport;
use App\Imports\ComprobanteImport;
use App\Mail\Jobfinished;
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
    public int $tries = 4;
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
//            'job' => $this->job->getRawBody(),
        ]);
        Mail::raw('error', function ($message) {
            $message->to('ajelof2@gmail.com')->subject('Ha fallado el proceso UpComprobantesJob.');
        });
    }

    public function handle(): void
    {
        $startTime = microtime(true);
        $jobId = uniqid('job_');

        try {
            // Configuraciones iniciales
            ini_set('memory_limit', '2G');
            set_time_limit(3600); // 1 hora

            // Registrar inicio con información detallada
            Log::info("1) Inicio de UpComprobantesJob", [
                'job_id' => $jobId,
                'memory_start' => $this->formatBytes(memory_get_usage(true)),
                'max_memory' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'php_version' => phpversion()
            ]);

            // Registrar errores fatales
            register_shutdown_function(function () use ($jobId, $startTime) {
                $error = error_get_last();
                if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
                    $duration = microtime(true) - $startTime;
                    $errorInfo = [
                        'error_type' => $error['type'],
                        'error_message' => $error['message'],
                        'error_file' => $error['file'],
                        'error_line' => $error['line'],
                        'duration' => $duration,
                        'memory_peak' => $this->formatBytes(memory_get_peak_usage(true))
                    ];

                    Log::error("Error fatal en UpComprobantesJob: " . print_r($errorInfo, true));

                    // Intentar enviar correo de error fatal
                    try {
                        Mail::raw(
                            "Error fatal en UpComprobantesJob:\n" . print_r($errorInfo, true),
                            function ($message) {
                                $message->to('ajelof2@gmail.com')
                                    ->subject('Error fatal en proceso upasientos');
                            }
                        );
                    } catch (\Exception $e) {
                        Log::error("No se pudo enviar correo de error fatal: " . $e->getMessage());
                    }
                }
            });

            // Verificar archivo antes de importar
            $filePath = storage_path('app/' . $this->path);
            if (!file_exists($filePath)) {
                throw new \Exception("El archivo no existe: " . $this->path);
            }

            $fileSize = filesize($filePath);
            Log::info("2) Verificación pre-importación", [
                'file_exists' => true,
                'file_size' => $this->formatBytes($fileSize),
                'memory_before' => $this->formatBytes(memory_get_usage(true))
            ]);

            // Realizar la importación con verificación de memoria
            $memoryStart = memory_get_usage(true);
            $elImport = new ComprobanteImport();
            Log::info("3) Iniciando importación de Excel");
            Excel::import($elImport, $filePath);

            $memoryEnd = memory_get_usage(true);
            $memoryPeak = memory_get_peak_usage(true);

            Log::info("4) Importación completada", [
                'memory_used' => $this->formatBytes($memoryEnd - $memoryStart),
                'memory_peak' => $this->formatBytes($memoryPeak),
                'duration' => microtime(true) - $startTime,
                'rows_processed' => $elImport->ContarFilasAbsolutas ?? 'N/A'
            ]);

            // Enviar correo de éxito
            $this->enviarCorreoExito();

        } catch (\Throwable $th) {
            // Capturar información detallada del error
            $errorDetails = [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'memory_current' => $this->formatBytes(memory_get_usage(true)),
                'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
                'duration' => microtime(true) - $startTime,
                'available_memory' => $this->formatBytes($this->getAvailableMemory())
            ];

            Log::error("Error en UpComprobantesJob: " . print_r($errorDetails, true));
            Log::error("Stack trace completo:\n" . $th->getTraceAsString());

            // Intentar enviar correo de error
            try {
                $mensajeMortal = isset($elImport) ? $elImport->MensajeMortal : 'No disponible';
                $error = ZilefErrors::RastroError($th);
                $this->enviarCorreoError($error, $mensajeMortal, $errorDetails);
            } catch (\Throwable $e) {
                Log::error("Error al enviar correo de error: " . $e->getMessage());
            }

            throw $th;
        }
    }

    private function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }

    private function getAvailableMemory(): int
    {
        $limit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $used = memory_get_usage(true);
        return max(0, $limit - $used);
    }

    private function parseMemoryLimit($memoryLimit): int
    {
        if ($memoryLimit === '-1') {
            return PHP_INT_MAX;
        }

        preg_match('/^(\d+)(.)$/', $memoryLimit, $matches);
        if (!$matches) {
            return (int)$memoryLimit;
        }

        $value = (int)$matches[1];
        switch (strtoupper($matches[2])) {
            case 'G':
                $value *= 1024;
            case 'M':
                $value *= 1024;
            case 'K':
                $value *= 1024;
        }

        return $value;
    }

    private function enviarCorreoError(string $error, string $mensajeMortal, array $errorDetails): void
    {
        try {
            $mensaje = "Ha fallado el proceso upasientos:\n\n" .
                "Error: $error\n\n" .
                "Mensaje Mortal: $mensajeMortal\n\n" .
                "Detalles técnicos:\n" .
                print_r($errorDetails, true);

            Mail::raw($mensaje, function ($message) {
                $message->to('ajelof2@gmail.com')
                    ->subject('Error en proceso upasientos');
            });
        } catch (\Throwable $th) {
            Log::error("Error al enviar correo de error: " . $th->getMessage());
        }
    }

    private function enviarCorreoExito(): void
    {
        try {
            Mail::to($this->userMail)->send(new Jobfinished('CE'));
        } catch (\Throwable $th) {
            Log::error("Error al enviar correo de éxito: " . $th->getMessage());
            throw $th;
        }
    }

//    private function enviarCorreoError(string $error, string $mensajeMortal): void
//    {
//        try {
//            $mensaje = "Ha fallado el proceso upasientos: " . $error . "\nAdemás " . $mensajeMortal;
//
//            Mail::raw('error', function ($message) use ($mensaje) {
//                $message->to('ajelof2@gmail.com')
//                    ->subject($mensaje);
//            });
//        } catch (\Throwable $th) {
//            Log::error("Error al enviar correo de error: " . $th->getMessage());
//        }
//    }
}
