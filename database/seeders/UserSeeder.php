<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use function Psy\debug;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UserSeeder
     * @return void
     */
    public function run(){
        $genPa = env('sap_gen');
        $nombreAdmin = 'Admin';
        $App = env('APP_NAME');
        $genero = 'Masculino';
        if($genPa === null){
            echo "Sin enviroment\n";
            echo "Sin enviroment\n";
            echo "Sin enviroment\n";
            return null;
        }
        $superadmin = User::updateOrCreate([
            'name'              => 'Superadmin',
            'email'             => 'ajelof2+11@gmail.com',
        ],
            [
            'password'          => bcrypt($genPa),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $genero,
            'identificacion' => '11232456',
            'celular' => '11232456',
        ]);$superadmin->assignRole('superadmin');


        $admin = User::updateOrCreate([
            'name'              => "$nombreAdmin $App",
            'email'             => "alejofg2+11@gmail.com",
        ],[
            'password'          => bcrypt($genPa.'uiu'), //cont_insuiu
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $genero,
            'identificacion' => '11232411',
            'celular' => '11232454',

        ]);$admin->assignRole('admin');

        //baja categoria
        $nombresGenericos = [
            ['name' => 'transacciones', 'cc'=> '1234444', 'rol' => 'tesorera'],
//            ['name' => 'bgenerico', 'cc'=> '1234459', 'rol' => 'copasst'],
//            ['name' => 'cgenerico', 'cc'=> '1234445', 'rol' => 'lider_del_proceso'],
//            ['name' => 'dgenerico', 'cc'=> '1234446', 'rol' => 'verificador'],
        ];

        $genero = 'Femenino';
        $tiposResponsable = DB::table('tipo_users')->pluck('nombre')->toArray();
        $tamanoTiposResponsable = count($tiposResponsable);
        $contadorTiposResponsable = 0;
        foreach ($nombresGenericos as $value) {
            $yearRandom = (random_int(22, 55));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');

            $unUsuario = User::updateOrCreate([
                'name'              => $value['name'],
                'email'             => $value['name'] . '@gmail.com', //transacciones@gmail.com //bgenericocont_ins123
            ],[
                'password'          => bcrypt($value['name'].'*123'), //123_teso
                'email_verified_at' => date('Y-m-d H:i'),
                'identificacion' => $value['cc'],
                'celular' => '123456',
                'fecha_nacimiento' => $anios,
                'sexo' => $genero,

                'cargo' => $value['rol'],
                'firma' => '',
                'tipo_user' => $tiposResponsable[$contadorTiposResponsable],
            ]);
            $contadorTiposResponsable++;
            if($contadorTiposResponsable > $tamanoTiposResponsable) $contadorTiposResponsable = 0;
            $unUsuario->assignRole($value['rol']);
        }
    }
}
/*
php artisan migrate --path=/database/migrations/nombre_de_la_migracion.php
php artisan db:seed --class=NombreDeLaSeeder
*/
