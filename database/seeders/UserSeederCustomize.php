<?php

namespace Database\Seeders;

use App\helpers\Myhelp;
use App\Models\Empresa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeederCustomize extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=UserSeeder2.php
     *
     */
    public function run(){
        $genPa = env('sap_gen');
        
//        $tiposResponsable = DB::table('tipo_users')->pluck('nombre')->toArray();
        
        $nombresGenericos = [
//            ['name' => 'Diana Marcela Cardona Gomez', 'cc' => '1152447749', 'email' => 'profesional.ambiental@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'responsable_inspeccion' , 'tipo_user' => $tiposResponsable[1]],
            ['name' => 'Edwin David Moreno Quintero', 'cc' => '112', 'email' => 'ambiental@colmayor.edu.co', 'sexo' => 'Masculino', 'rol' => 'responsable_inspeccion'],
            ['name' => 'Jennyfer Figueroa Cano', 'cc' => '113', 'email' => 'jennyfer.figueroa@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'responsable_inspeccion'],
            ['name' => 'Daniel Cuartas Arboleda', 'cc' => '8161368', 'email' => 'daniel.cuartas@colmayor.edu.co', 'sexo' => 'Masculino', 'rol' => 'responsable_inspeccion'],
            ['name' => 'Mildrey Andrea Florez Ortiz', 'cc' => '115', 'email' => 'sstcolmayor1@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'responsable_inspeccion'],
            ['name' => 'Nora Inés Cano Muñoz', 'cc' => '116', 'email' => 'sstcolmayor3@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'responsable_inspeccion'],
            ['name' => 'Sandra Yurany Vásquez Tangarife', 'cc' => '117', 'email' => 'sandra.yurany@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'responsable_inspeccion'],
            ['name' => 'Sandra Liliana García Ferrraro', 'cc' => '118', 'email' => 'sandra.garcia@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'responsable_inspeccion'],
            ['name' => 'Maria Alejandra Ortiz Restrepo', 'cc' => '119', 'email' => 'maria.ortizr@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'responsable_inspeccion'],
            ['name' => 'Carlos Eduardo Carvajal Tangarife', 'cc' => '120', 'email' => 'saludocupacional@colmayor.edu.co', 'sexo' => 'Masculino', 'rol' => 'responsable_inspeccion'],
        ];
/* ROLES
            'responsable_de_recibir_la_inspeccion',//1
            'copasst', //2
            'lider_del_proceso', //3
            'verificador', //4
        'responsable_inspeccion',// 5
*/
        
        $myhelp = new Myhelp();
        foreach ($nombresGenericos as $key => $value) {
            $primeraParte = $myhelp->cortarFrase($value['name'],1);
            $yearRandom = (random_int(20, 40));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $unUsuario = User::create([
                'name' => $value['name'],
                'email' => $value['email'],
                'password' => bcrypt($primeraParte . '+*'),
                'email_verified_at' => date('Y-m-d H:i'),
                'identificacion' => $value['cc'],
                'celular' => '123456',
                'fecha_nacimiento' => $anios,
                'sexo' => $value['sexo'],
                'tipo_user' => $value['tipo_user'] ?? '',
            ]);
            $unUsuario->assignRole($value['rol']);
        }
    }
}
/*
php artisan migrate --path=/database/migrations/nombre_de_la_migracion.php
php artisan db:seed --class=NombreDeLaSeeder
*/
