@echo off
REM =====================================================
REM run.bat -- one-step setup + launch for the Online Bookstore (Windows)
REM
REM Prerequisites (one-time, must already be installed):
REM   - MySQL server running (as a Windows service)
REM   - PHP installed and on PATH (php -v works in cmd)
REM   - mysql.exe on PATH (or edit MYSQL_EXE below)
REM
REM Usage:
REM   run.bat                       (uses root, no password, port 8000)
REM   run.bat root MyPassword123!   (custom user/password)
REM   run.bat root MyPassword123! 8080   (custom port too)
REM =====================================================

setlocal

set DB_USER=%1
if "%DB_USER%"=="" set DB_USER=root

set DB_PASS=%2
if "%DB_PASS%"=="" set DB_PASS=Naman5989

set PORT=%3
if "%PORT%"=="" set PORT=8000

set SCRIPT_DIR=%~dp0

where php >nul 2>nul
if %errorlevel% equ 0 (
    set PHP_EXE=php
) else if exist "C:\xampp\php\php.exe" (
    set PHP_EXE="C:\xampp\php\php.exe"
) else (
    set PHP_EXE=php
)

echo Starting the site at http://localhost:%PORT%/index.php
echo Opening in default browser...
start http://localhost:%PORT%/index.php
echo (Press Ctrl+C to stop the server)

cd /d "%SCRIPT_DIR%frontend"
%PHP_EXE% -S localhost:%PORT%
