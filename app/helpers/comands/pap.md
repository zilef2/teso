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

# Comandos
php artisan make:command LanguageCopyU
php artisan make:job BusquedaConceptoCI