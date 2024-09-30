<?php

namespace Database\Seeders;

use App\Models\concepto_flujo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConceptoFlujoSeeder extends Seeder
{
    /**
     * Run the database seeds.
//            "ingresos_o_egresos" => date('Y-m-d H:i').'',
     */
    public function run(): void
    {
        concepto_flujo::create(["cuenta_contable" =>131701010 , "concepto_flujo" => "Matrículas académicas", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>131701050 , "concepto_flujo" => "Matrículas académicas", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>131701090 , "concepto_flujo" => "Matrículas académicas", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>131701091 , "concepto_flujo" => "Matrículas académicas", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>131701020 , "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>131701030 , "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>131701060 , "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>131701040 , "concepto_flujo" => "Inscripción a programas académicos", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>131719010 , "concepto_flujo" => "Ingreso para la ejecución de convenios", "ingresos_o_egresos" => 'ingresos',]);
        concepto_flujo::create(["cuenta_contable" =>138439010 , "concepto_flujo" => "Ingreso arrendamientos", "ingresos_o_egresos" => 'ingresos',]);

        //egresos
        concepto_flujo::create(["cuenta_contable" => 240706140, "concepto_flujo" => "Recaudo para devolución de matrícula", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 240726010, "concepto_flujo" => "Reintegro rendimientos financieros Distrito", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 240726191, "concepto_flujo" => "Reintegro rendimientos financieros convenios", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 439501020, "concepto_flujo" => "Reintegros", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 589090010, "concepto_flujo" => "Reintegro rendimientos financieros convenios", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 249058010, "concepto_flujo" => "Gasto arrendamientos", "ingresos_o_egresos" => 'egresos',]);
    }
}
/*
concepto_flujo::create(["cuenta_contable" => '', "concepto_flujo" => "", "ingresos_o_egresos" => 'egresos',]);
 */
