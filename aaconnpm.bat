@echo on
set destination=C:\laragon\www\cmainspecciones\cmainspecciones.zip
del %destination%
cd C:\laragon\www\cmainspecciones
del /F /S hot
npm run build && "C:\Program Files\7-Zip\7z.exe" a -r -x!"C:\laragon\www\cmainspecciones\vendor" -x!"C:\laragon\www\cmainspecciones\storage" -x!"C:\laragon\www\cmainspecciones\node_modules" -x!"C:\laragon\www\cmainspecciones\public\hot.*"  %destination% "C:\laragon\www\cmainspecciones\app" "C:\laragon\www\cmainspecciones\resources" "C:\laragon\www\cmainspecciones\routes" "C:\laragon\www\cmainspecciones\database" "C:\laragon\www\cmainspecciones\public" "C:\laragon\www\cmainspecciones\lang"
pause
