@echo off
echo ========================================================
echo  Parando e removendo o ambiente Docker do MyReceitas...
echo ========================================================

docker-compose down

if %errorlevel% neq 0 (
    echo.
    echo [AVISO] Nao foi possivel parar os conteineres. Eles podem ja estar parados.
)

echo.
echo ========================================================
echo  Ambiente MyReceitas finalizado.
echo ========================================================
echo.
pause