@echo on
@REM fseguimiento teso
set "folder=C:\laragon\www\teso"
cd %folder%
start /b git pull

set "folder=C:\laragon\www\fseguimiento"
cd %folder%
start /b git pull
