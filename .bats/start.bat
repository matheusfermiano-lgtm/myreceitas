@echo off
cd /d "%~dp0"
echo ========================================================
echo  Iniciando o ambiente Docker para o MyReceitas...
echo ========================================================

docker info >nul 2>&1
if %errorlevel% equ 0 goto docker_ok

echo Docker nao esta em execucao. Abrindo o Docker Desktop...
start "" "C:\Program Files\Docker\Docker\Docker Desktop.exe"
echo Aguardando o Docker ficar pronto, isso pode levar ate 3 minutos...
set /a espera=0

:esperar_docker
docker info >nul 2>&1
if %errorlevel% equ 0 goto docker_ok
set /a espera+=1
if %espera% geq 60 goto docker_falhou
timeout /t 3 /nobreak >nul
goto esperar_docker

:docker_falhou
echo.
echo [ERRO] O Docker nao iniciou a tempo.
echo Abra o Docker Desktop manualmente e tente novamente.
pause
exit /b 1

:docker_ok
echo Docker pronto!
echo.

docker-compose up -d --build
if %errorlevel% neq 0 goto compose_falhou
goto esperar_site_inicio

:compose_falhou
echo.
echo [ERRO] Falha ao iniciar os conteineres.
pause
exit /b 1

:esperar_site_inicio
echo.
echo Aguardando o site responder em http://localhost:8000 ...
set /a tentativas=0

:esperar_site
curl -s -o nul http://localhost:8000
if %errorlevel% equ 0 goto abrir
set /a tentativas+=1
if %tentativas% geq 60 goto abrir
timeout /t 2 /nobreak >nul
goto esperar_site

:abrir
start chrome "http://localhost:8000"
timeout /t 4 /nobreak >nul
powershell -NoProfile -Command "$s = New-Object -ComObject WScript.Shell; if ($s.AppActivate('Chrome')) { Start-Sleep -Milliseconds 500; $s.SendKeys('{F5}') }"

echo.
echo ========================================================
echo  Ambiente MyReceitas iniciado com sucesso!
echo.
echo  Acesse o site em: http://localhost:8000
echo ========================================================
echo.
echo  Para parar o ambiente, execute o script stop.bat
echo.
pause