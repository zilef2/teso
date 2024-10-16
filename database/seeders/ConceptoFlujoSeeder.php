<?php

namespace Database\Seeders;

use App\Models\concepto_flujo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConceptoFlujoSeeder extends Seeder
{
    /**
     * Run the database seeds.
        "ingresos_o_egresos" => date('Y-m-d H:i').'',
    php artisan db:seed --class=ConceptoFlujoSeeder
php artisan migrate --path=/database/migrations/nombre_de_archivo_migracion.php
     */
    public function run(): void
    {
        concepto_flujo::where('id','>',0)->delete();
        concepto_flujo::create(["cuenta_contable" => 131701010, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701020, "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701030, "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701040, "concepto_flujo" => "Inscripción programas académicos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701050, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701060, "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701080, "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701090, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701091, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131719010, "concepto_flujo" => "Ingreso para ejecución de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131790010, "concepto_flujo" => "Otras ventas de servicios (Lacma, facultad de Salud)", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 133712010, "concepto_flujo" => "Transferencias inversión", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138413010, "concepto_flujo" => "Devolución IVA", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138426010, "concepto_flujo" => "Reconocimiento de incapacidades", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138439010, "concepto_flujo" => "Ingreso arrendamientos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138490020, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138490040, "concepto_flujo" => "Ingresos para la ejecución de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 240706010, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 240706060, "concepto_flujo" => "Recaudo para terceros", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 240706140, "concepto_flujo" => "Recaudo para terceros", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 240726010, "concepto_flujo" => "Rendimientos financieros inversión", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 240726191, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 249040030, "concepto_flujo" => "Reconocimiento de incapacidades", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201536, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201547, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201559, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201561, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201631, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201632, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201653, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201669, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201679, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201681, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201692, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201693, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201694, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201695, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201708, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201712, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201714, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201717, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201721, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201726, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201729, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201731, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201732, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201738, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201744, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201745, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201746, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201749, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201752, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201756, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201757, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 290201758, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 291026010, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 430550060, "concepto_flujo" => "Utilidades en la administración de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 439501060, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 442803010, "concepto_flujo" => "Transferencias funcionamiento", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 480201010, "concepto_flujo" => "Rendimientos financieros propios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 480201020, "concepto_flujo" => "Rendimientos Financieros Cuentas Convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 480825010, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 510101010, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720803010, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720803020, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720803030, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720803040, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720805030, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720805031, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720805032, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720805033, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 720806030, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 819090010, "concepto_flujo" => "Devolución IVA", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 890590010, "concepto_flujo" => "Devolución IVA", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 512035010, "concepto_flujo" => "Ingreso para ejecución de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 512035020, "concepto_flujo" => "Ingreso para ejecución de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 799008020, "concepto_flujo" => "Ingreso para ejecución de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 799008030, "concepto_flujo" => "Ingreso para ejecución de convenios", "ingresos_o_egresos" => 'ingresos']);


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
