//misterdebug - crud-generator-laravel
(example-project: tomag)


# INICIO DEL PROYECTO
// 18septiembre2024 FLUEF
php artisan copy:u PorcentajeInteresCuenta
php artisan copy:u Comprobante

//muchos a muchos
php artisan make:migration pivot_aspecto_inspeccion_table


## just model
php artisan make:model Generico --all
php artisan make:model cuenta --all
php artisan make:model transaccion --all
php artisan make:model Comprobante --all

## control

[##]: # (php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer")


### Utilidades
para borrar:  php artisan rm:crud post --force


###laravel excel
php artisan make:import CuentaImport --model=cuenta
php artisan make:import TransaccionesImport --model=transaccion
php artisan make:import ComprobanteImport --model=Comprobante
php artisan make:import BancoImport --model=Banco

php artisan make:export TodaBDExport

### node
vue-datapicker
### laravel
composer require maatwebsite/excel