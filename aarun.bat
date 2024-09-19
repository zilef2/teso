@echo on
set "folder=C:\laragon\www\teso"

cd %folder%
start /b php artisan serve
start /b npm run dev