<?php

namespace App\helpers;

class CargosModelos {

    //JUST THIS PROJECT
    public static function CargosYModelos() {
        $crudSemiCompleto = ['update', 'read', 'create','download','sugerencia','aprobar','egreso','ingreso','firmar'];
        $crudCompleto = array_merge(['delete'], $crudSemiCompleto);
        
        //otros cargos NO_ADMIN
        $nombresDeCargos = [
            'tesorera',//1
            'administrativo', //2
        ];//recuerda userseeder, RoleSeeder
        $isSome = [];
        foreach ($nombresDeCargos as $key => $value) {
            $isSome[] = 'is' . $value;
        }
        //arrrays for easyway
        $elcore = 'transaccion';
        $Models = [
            'role',
            'permission',
            'user',
            'parametro',

            $elcore,
//            'area',
        ];
        
        $fullCrud = [
            'responsable_inspeccion' => ['user', $elcore
//                , 'area'
            ]
        ];
        return [
            'nombresDeCargos' => $nombresDeCargos,
            'Models' => $Models,
            'isSome' => $isSome,
            'core' => $elcore,
            'fullCrud' => $fullCrud,
            'crudCompleto' => $crudCompleto,
            'crudSemiCompleto' => $crudSemiCompleto,
        ];
    }
}
?>
