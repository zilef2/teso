@echo on
set "folder=C:\laragon\www\cmainspecciones"

cd %folder%
start /b php artisan serve
start /b npm run dev