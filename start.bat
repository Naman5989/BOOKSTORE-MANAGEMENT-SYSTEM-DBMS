@echo off
title Online Bookstore
echo ===================================================
echo        ONLINE BOOKSTORE MANAGEMENT SYSTEM
echo ===================================================
echo.

:: 1. Detect PHP executable
where php >nul 2>nul
if %errorlevel% equ 0 (
    set PHP_EXE=php
) else if exist "C:\xampp\php\php.exe" (
    set PHP_EXE="C:\xampp\php\php.exe"
) else (
    echo [ERROR] PHP was not found. Please install PHP or ensure XAMPP is installed at C:\xampp\php.
    echo.
    pause
    exit /b 1
)

:: 2. Check and start MySQL80 service if stopped
sc query MySQL80 | find "RUNNING" >nul 2>nul
if %errorlevel% neq 0 (
    echo [INFO] Starting MySQL service...
    net start MySQL80 >nul 2>nul
)

:: 3. Change directory to frontend
if exist "%~dp0frontend\index.php" (
    cd /d "%~dp0frontend"
) else if exist "%~dp0Online_Bookstore\frontend\index.php" (
    cd /d "%~dp0Online_Bookstore\frontend"
) else (
    echo [ERROR] frontend folder could not be found!
    pause
    exit /b 1
)

:: 4. Open website in default browser
echo [INFO] Opening website in your default browser...
start http://localhost:8000/index.php

:: 5. Launch the PHP web server
echo.
echo ===================================================
echo  Server is RUNNING at: http://localhost:8000
echo  Keep this window open while using the website.
echo  Press Ctrl + C to stop the server anytime.
echo ===================================================
echo.

%PHP_EXE% -S localhost:8000
pause
