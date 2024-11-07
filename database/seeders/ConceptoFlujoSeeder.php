<?php

namespace Database\Seeders;

use App\Models\concepto_flujo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConceptoFlujoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * "ingresos_o_egresos" => date('Y-m-d H:i').'',
     * php artisan db:seed --class=ConceptoFlujoSeeder
     * php artisan migrate --path=/database/migrations/nombre_de_archivo_migracion.php
     */
    public function run(): void
    {
        concepto_flujo::where('id', '>', 0)->delete();
        concepto_flujo::create(["cuenta_contable" => 131701010, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701020, "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701030, "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701040, "concepto_flujo" => "Inscripcion programas académicos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701050, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701060, "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701080, "concepto_flujo" => "Servicios educativos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701090, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131701091, "concepto_flujo" => "Matrículas Académicas", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131719010, "concepto_flujo" => "Ingreso para ejecucion de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 131790010, "concepto_flujo" => "Otras ventas de servicios (Lacma, facultad de Salud)", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 133712010, "concepto_flujo" => "Transferencias inversión", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138413010, "concepto_flujo" => "Devolucion IVA", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138426010, "concepto_flujo" => "Reconocimiento de incapacidades", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138439010, "concepto_flujo" => "Ingreso arrendamientos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138490020, "concepto_flujo" => "Otros ingresos", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 138490040, "concepto_flujo" => "Ingresos para la ejecucion de convenios", "ingresos_o_egresos" => 'ingresos']);
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
        concepto_flujo::create(["cuenta_contable" => 430550060, "concepto_flujo" => "Utilidades en la administracion de convenios", "ingresos_o_egresos" => 'ingresos']);
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
        concepto_flujo::create(["cuenta_contable" => 819090010, "concepto_flujo" => "Devolucion IVA", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 890590010, "concepto_flujo" => "Devolucion IVA", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 512035010, "concepto_flujo" => "Ingreso para ejecucion de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 512035020, "concepto_flujo" => "Ingreso para ejecucion de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 799008020, "concepto_flujo" => "Ingreso para ejecucion de convenios", "ingresos_o_egresos" => 'ingresos']);
        concepto_flujo::create(["cuenta_contable" => 799008030, "concepto_flujo" => "Ingreso para ejecucion de convenios", "ingresos_o_egresos" => 'ingresos']);


        //egresos
        concepto_flujo::create(["cuenta_contable" => 240706140, "concepto_flujo" => "Recaudo para devolucion de matrícula", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 240726010, "concepto_flujo" => "Reintegro rendimientos financieros Distrito", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 240726191, "concepto_flujo" => "Reintegro rendimientos financieros convenios", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 439501020, "concepto_flujo" => "Reintegros", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 589090010, "concepto_flujo" => "Reintegro rendimientos financieros convenios", "ingresos_o_egresos" => 'egresos',]);
        concepto_flujo::create(["cuenta_contable" => 249058010, "concepto_flujo" => "Gasto arrendamientos", "ingresos_o_egresos" => 'egresos',]);


        concepto_flujo::create(["cuenta_contable" => 240101010, "concepto_flujo" => "Otros egresos", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 240706010, "concepto_flujo" => "Devolucion matrículas académicas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 240706140, "concepto_flujo" => "Devolucion matrículas académicas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 240726010, "concepto_flujo" => "Reintegro rendimientos financieros Distrito", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 240726191, "concepto_flujo" => "Reintegro rendimientos financieros convenios", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242404010, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242404020, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242405020, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242405040, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242405060, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242407020, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242407040, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242407050, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242407070, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242407080, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242407090, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242408010, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242408020, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242408030, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242411010, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242413010, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242490020, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242490030, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242490040, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 242490050, "concepto_flujo" => "Deducciones empleados / contratistas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243603010, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243603020, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243604020, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243605010, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243605020, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243605030, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243605040, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243605050, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243606010, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243608010, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243608020, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243615010, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243625010, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243690030, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 243690040, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 244024010, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 244085020, "concepto_flujo" => "Retenciones en la fuente", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 249054020, "concepto_flujo" => "Honorarios", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 290201145, "concepto_flujo" => "Seguridad social", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 439501010, "concepto_flujo" => "Devolucion matrículas académicas", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 439501020, "concepto_flujo" => "Reintegros", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 439501130, "concepto_flujo" => "Reintegros", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510101010, "concepto_flujo" => "Nómina institucional - Administrativa", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510302010, "concepto_flujo" => "Seguridad social", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510303010, "concepto_flujo" => "Seguridad social", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510305010, "concepto_flujo" => "Seguridad social", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510306010, "concepto_flujo" => "Seguridad social", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510307010, "concepto_flujo" => "Seguridad social", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510401050, "concepto_flujo" => "Seguridad social", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510801010, "concepto_flujo" => "Contratistas directos", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510801020, "concepto_flujo" => "Contratistas directos", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 510890010, "concepto_flujo" => "Subvenciones y sostenimiento", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 511119010, "concepto_flujo" => "Gastos de viaje y manutencion", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 511179010, "concepto_flujo" => "Honorarios", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 512024010, "concepto_flujo" => "Servicios financieros(GMF y comisiones)", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 589501010, "concepto_flujo" => "Reintegros", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 720802050, "concepto_flujo" => "Gastos de viaje y manutencion", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 720802120, "concepto_flujo" => "Movilidad académica", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 720802150, "concepto_flujo" => "Subvenciones y sostenimiento", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 720803010, "concepto_flujo" => "Nómina institucional - Docentes planta", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 720803020, "concepto_flujo" => "Nómina institucional - Administrativa", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 720803030, "concepto_flujo" => "Nómina institucional - Cátedra general", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 720803040, "concepto_flujo" => "Nómina institucional - Ocasional", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 720804010, "concepto_flujo" => "Nómina institucional - Administrativa", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 799002010, "concepto_flujo" => "Gastos de desplazamiento convenios", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 799008010, "concepto_flujo" => "Servicios financieros(GMF y comisiones) convenios", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 799010010, "concepto_flujo" => "Contratistas convenios", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 799010020, "concepto_flujo" => "Contratistas convenios", "ingresos_o_egresos" => 'egresos']);
        concepto_flujo::create(["cuenta_contable" => 799010040, "concepto_flujo" => "Subvenciones y sostenimiento convenios", "ingresos_o_egresos" => 'egresos']);

    }
}
/*
concepto_flujo::create(["cuenta_contable" => '', "concepto_flujo" => "", "ingresos_o_egresos" => 'egresos',]);
 */
