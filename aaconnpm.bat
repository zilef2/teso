@echo on
set destination=C:\laragon\www\teso\teso.zip
del %destination%
cd C:\laragon\www\teso
del /F /S hot
npm run build && "C:\Program Files\7-Zip\7z.exe" a -r -x!"C:\laragon\www\teso\vendor" -x!"C:\laragon\www\teso\storage" -x!"C:\laragon\www\teso\node_modules" -x!"C:\laragon\www\teso\public\hot.*"  %destination% "C:\laragon\www\teso\app" "C:\laragon\www\teso\resources" "C:\laragon\www\teso\routes" "C:\laragon\www\teso\database" "C:\laragon\www\teso\public" "C:\laragon\www\teso\lang"
pause
