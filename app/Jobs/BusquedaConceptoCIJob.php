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
        try {
            $user = Myhelp::AuthU();
            Log::info("U -> " . Auth::user()->name);
//            $contraCICE = new \App\Http\Controllers\ContrapartidasCICEController();
            Log::info("asdasdasdasdasU -> " . Auth::user()->name);

//            $contraCICE->Uscar_AJ_CI($this->Transacciones);
//            try {
//                Mail::to($user->email)->send(new Jobfinished());
//            } catch (\Exception $e) {
//                Log::error("Error sending email: " . $e->getMessage());
//            }

//                (new MailMessage)
//                    ->greeting('Hello!')
//                    ->line('One of your invoices has been paid!')
//                    ->lineIf($this->amount > 0, "Amount paid: {$this->amount}")
//                    ->action('View Invoice', $url)
//                    ->line('Thank you for using our application!');
        } catch (\Throwable $th) {
//            DB::rollback();
//            return back()->with('error', 'Ajustes con errores: ' . ZilefErrors::RastroError($th));
        }
    }

}
