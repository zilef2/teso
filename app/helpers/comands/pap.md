# Aprendiendo jobs
php artisan queue:restart
php artisan queue:work --queue=default --tries=3
php artisan queue:work --queue=default --tries=1 --timeout=3600

# INICIO DEL PROYECTO
// 18septiembre2024 FLUEF
php artisan copy:u PorcentajeInteresCuenta
php artisan copy:u Comprobante


## just model
php artisan make:model cuenta --all
php artisan make:model transaccion --all
php artisan make:model Comprobante --all


### laravel excel
php artisan make:import CuentaImport --model=cuenta
php artisan make:import TransaccionesImport --model=transaccion
php artisan make:import ComprobanteImport --model=Comprobante
php artisan make:import BancoImport --model=Banco // desuso
php artisan make:import PreAfectacionImport
php artisan make:import afectacionImport --model=afectacion

# Comandos
php artisan make:command LanguageCopyU

# Primera notificacion (fallido)
php artisan make:controller JobController
php artisan make:notification JobCompleted (unused)

# Buscando Cruces
php artisan make:job BusquedaConceptoCI
php artisan make:job BC_Anulaciones
php artisan make:job BC_CIGeneral
php artisan make:controller BusquedaIndependienteController
php artisan make:job CruceCE

# Subiendo exceles con jobs
php artisan make:job UpAsientosJob
php artisan make:job testingAndDoubs
php artisan make:job UpComprobantesJob
# lisenerMailLogin
php artisan make:Mail NewSession
