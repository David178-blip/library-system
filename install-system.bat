@echo off
setlocal enabledelayedexpansion

echo ==========================================
echo    Library System - Installer Setup
echo ==========================================
echo.

:: Check for PHP
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH.
    pause
    exit /b
)
echo [OK] PHP found.

:: Check for Node.js
node -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Node.js is not installed or not in PATH.
    pause
    exit /b
)
echo [OK] Node.js found.

:: Check for Composer
call composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Composer is not installed or not in PATH.
    pause
    exit /b
)
echo [OK] Composer found.

:: Check for npm
call npm -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] npm is not installed or not in PATH.
    pause
    exit /b
)
echo [OK] npm found.

echo.
echo ------------------------------------------
echo Installing PHP dependencies...
call composer install
if %errorlevel% neq 0 (
    echo [ERROR] Composer install failed.
    pause
    exit /b
)

echo.
echo Installing Node.js dependencies...
call npm install
if %errorlevel% neq 0 (
    echo [ERROR] npm install failed.
    pause
    exit /b
)

echo.
echo Setting up environment file...
if not exist .env (
    copy .env.example .env
    echo [OK] Created .env from .env.example.
    echo [ACTION] Please edit .env and configure your database settings before continuing.
    pause
) else (
    echo [OK] .env already exists.
)

echo.
echo Generating application key...
php artisan key:generate

echo.
echo Running database migrations and seeding...
echo (Ensure your database is running and configured in .env)
php artisan migrate --seed
if %errorlevel% neq 0 (
    echo [ERROR] Migration failed. Check your database settings in .env.
    pause
    exit /b
)

echo.
echo Building frontend assets...
call npm run build
if %errorlevel% neq 0 (
    echo [ERROR] npm build failed.
    pause
    exit /b
)

echo.
echo ==========================================
echo    INSTALLATION COMPLETE!
echo ==========================================
echo You can now run the system using:
echo library_system.bat
echo.
pause
