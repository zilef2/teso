<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LanguageCopyU extends Command
{
    protected $signature = 'lang:u {Combinaciones}';
    protected $description = 'Genera las combinaciones posibles y las inserta en es_appphp';

    public function handle(): void
    {
        $Combinaciones = strtolower($this->argument('Combinaciones'));
        
        $directory = 'lang/es/app.php';
        $files = glob($directory);

        $explotada = explode(' ', $Combinaciones);
        $explot = clone $explotada;
        foreach ($explotada as $index => $palabra) {
            $explot[$index] = ucfirst($palabra);
            $arrayResultados[$index] = implode(' ',$explot);
        }

        $insertable = "'$Combinaciones' => '$Combinaciones',\n\t\t//aquipues";
        $pattern = '/\/\/aquipues/';

        foreach ($files as $file){
            $content = file_get_contents($file);
            if (strpos($content, $pattern) === false) {
                $content2 = preg_replace($pattern, $insertable, $content);
//                $content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if($content == $content2)
                    $this->info("Language Actualizado: $file\n");
                else
                    $this->info("Language sin cambios: $file\n");
            } else {
                $this->error("No existe aquipues en: $file\n");
            }
        }
    }
}
