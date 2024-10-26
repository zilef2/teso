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

        //CargosModelos.php
        $nombresGenericos = [
            ['name' => 'Sandra Cristina Bedoya Cardenas', 'cc' => '43923483', 'email' => 'sandra.bedoya@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'tesorera'],
            ['name' => 'Sandra Patricia Giraldo Bustos', 'cc' => '26424937', 'email' => 'tesoreria@colmayor.edu.co', 'sexo' => 'Femenino', 'rol' => 'tesorera'],
            ['name' => 'Jorge William Arredondo Arango', 'cc' => '71719090', 'email' => 'viceadministrativa@colmayor.edu.co', 'sexo' => 'Masculino', 'rol' => 'administrativo'],
        ];
        /* ROLES
            'tesorera',//1
        */

        $myhelp = new Myhelp();
        foreach ($nombresGenericos as $key => $value) {
            $primeraParte = $myhelp->cortarFrase($value['name'],1);
            $segundaParte = $value['cc'];
            $yearRandom = (random_int(20, 40));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $unUsuario = User::create([
                'name' => $value['name'],
                'email' => $value['email'],
                'password' => bcrypt($primeraParte . '+'. $segundaParte),
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
