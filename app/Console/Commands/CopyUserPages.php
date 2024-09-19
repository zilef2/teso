<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;


/*
 * php artisan make:command MakeModelExperimental
 */
class CopyUserPages extends Command
{
    protected $signature = 'copy:u {folderName} {depende?}';
    protected $description = 'Copia la carpeta designada a una ubicación específica';

    public function handle(){
        $plantillaActual = 'generic';
        $mensajeA = 'La genericacion del componente: ';
        $mensajeExito = ' fue realizada con exito ';
        $mensajeFallo = ' fallo';
        
        $foldernan = $this->argument('folderName');

        $this->warn("Empezando make:model");
        Artisan::call('make:model', [
            'name' => $foldernan,
            '--all' => true,
        ]);
        Artisan::call('copy:f');
        $this->warn("Fin model");
        
        
        $RealizoVueConExito = $this->MakeVuePages($plantillaActual);
        $mensaje = $RealizoVueConExito ? $mensajeA.' Vuejs'.$mensajeExito
            : $mensajeA.' Vuejs'.$mensajeFallo;
        $this->info($mensaje);
        

        $RealizoControllerConExito = $this->MakeControllerPages($plantillaActual);
        $mensaje = $RealizoControllerConExito ? $mensajeA.'el controlador'.$mensajeExito
            : $mensajeA.' controlador '.$mensajeFallo;
        $this->info($mensaje);

        
        if($RealizoControllerConExito || $RealizoVueConExito)
            $this->replaceWordInFiles($plantillaActual,
            [
                'vue' => $RealizoVueConExito,
                'controller' => $RealizoControllerConExito
            ]);
        
        
        
        
        
        
        $this->DoWebphp($foldernan);
        $this->info("Fin de la operacion. Se limpiará cache\n\n");
        $this->info('optimize: ');
        $this->info(Artisan::call('optimize'));
        $this->info('optimize:clear: ');
        $this->info(Artisan::call('optimize:clear'));
    }


    private function MakeControllerPages($plantillaActual){
        $folderName = $this->argument('folderName');
        $folderMayus = ucfirst($folderName);
        $sourcePath = base_path('app/Http/Controllers/'.$plantillaActual.'sController.php');
        $destinationPath = base_path("app/Http/Controllers/".$folderMayus."sController.php");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '{$destinationPath}' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        $this->info("- ".$sourcePath);
        $this->info("- ".$destinationPath);

        return true;
    }

    private function MakeVuePages($plantillaActual){
        $folderName = $this->argument('folderName');

        $sourcePath = base_path('resources/js/Pages/'.$plantillaActual);
        $destinationPath = base_path("resources/js/Pages/{$folderName}");

        if (File::exists($destinationPath)) {
            $this->warn("La carpeta de destino '{$folderName}' ya existe.");
            return false;
        }
        File::copyDirectory($sourcePath, $destinationPath);
        return true;
    }

    private function replaceWordInFiles($oldWord,$permiteRemplazo){
        $newWord = $this->argument('folderName');
        $folderMayus = ucfirst($newWord);
        $files = File::allFiles(base_path("resources/js/Pages/{$newWord}"));
        $controller = base_path("app/Http/Controllers/{$folderMayus}".'Controller.php');
        $depende = $this->argument('depende') ?? '';

        $depende = $depende == '' || $depende == null ? 'no_nada' : $depende;

        if($permiteRemplazo['vue']){
            foreach ($files as $key => $file) {

                $content = file_get_contents($file);
                $content = str_replace(array($oldWord, ucfirst($oldWord),'geeneric'),//ojo aqui, es estatico
                                            [$newWord, $folderMayus,$folderMayus],
                                            $content
                );
                file_put_contents($file, $content);
            }
        }

        //reemplazo de controlador
        if($permiteRemplazo['controller']){
                $sourcePath = base_path('app/Http/Controllers/'.ucfirst($oldWord).'Controller.php');
                $content = file_get_contents($sourcePath);
                $content = str_replace(array($oldWord, 'dependex','deependex','geeneric'),//ojo aqui, es estatico
                                       array($newWord,  $depende ,ucfirst($depende),ucfirst($newWord)),
                                       $content
                );

                file_put_contents($controller, $content);
        }
    }

    private function DoFillable()
    {
        $directory = 'app/Models';
        $files = glob($directory . '/*.php');

        $fillable = "\n    protected \$fillable = [\n        'id',\n    ];\n";

        foreach ($files as $file) {
            
            $content = file_get_contents($file);

            if (strpos($content, 'protected $fillable') === false) {
                $content = preg_replace('/(use HasFactory;)/', "$1$fillable", $content);
                file_put_contents($file, $content);
                $this->info("Actualizado: $file\n");
            } else {
                $this->info("Ya existe en: $file\n");
            }
        }

        return true;
    }
    private function DoWebphp($resource)
    {
        $directory = 'routes';
        $files = glob($directory . '/*.php');

        $insertable = '\nRoute::resource("/' . $resource . '", ' . ucfirst($resource) . 'Controller::class);';
        $pattern = '/aquipues/';
        foreach ($files as $file) {
            $content = file_get_contents($file);

            if (strpos($content, $pattern) === false) {
                $content2 = preg_replace($pattern, "$0$insertable", $content);
                file_put_contents($file, $content2);
                if($content == $content2)
                    $this->info("Actualizado: $file\n");
                else
                    $this->info("Sin cambios: $file\n");
            } else {
                $this->info("No existe aquipues en: $file\n");
            }
        }

        return true;
    }
    
}